/*
 * $Id: kpoppassd.c,v 1.3 2003/04/29 02:12:05 awithers Exp $
 *
 * Copyright (c) 2000,2001 Alex Withers <awithers@gonzaga.edu>
 * 
 * This software is placed under the GPL, see COPYING.
 *
 */
/*
 * A Eudora and NUPOP change password server for Linux systems using
 * Kerberos 5.  Changes kerberos password.
 *
 * Based on original poppassd by:
 *
 * Pawel Krawczyk <kravietz@ceti.com.pl>
 * John Norstad <j-norstad@nwu.edu>
 * Roy Smith <roy@nyu.edu>
 * Daniel L. Leavitt <dll@mitre.org>
 *
 */
 
/* Steve Dorner's description of the simple protocol:
 *
 * The server's responses should be like an FTP server's responses; 
 * 1xx for in progress, 2xx for success, 3xx for more information
 * needed, 4xx for temporary failure, and 5xx for permanent failure.  
 * Putting it all together, here's a sample conversation:
 *
 *   S: 200 hello\r\n
 *   E: user yourloginname\r\n
 *   S: 300 please send your password now\r\n
 *   E: pass yourcurrentpassword\r\n
 *   S: 200 My, that was tasty\r\n
 *   E: newpass yournewpassword\r\n
 *   S: 200 Happy to oblige\r\n
 *   E: quit\r\n
 *   S: 200 Bye-bye\r\n
 *   S: <closes connection>
 *   E: <closes connection>
 */
 
/** !!! IMPORTANT !!! Look these over and adjust for your system. **/
#define VERSION "0.4"
#define BAD_PASS_DELAY 3	/* delay in seconds after bad 'Old password' */
#define MAX_LEN_USERNAME "16"	/* maximum length of username */
#define MAX_PARSELEN_PASS "126"	/* maximum length for sscanf */
#define MAX_LEN_PASS 12		/* maximum length of password */
#define MIN_LEN_PASS 6
#define BUFSIZE 128
#define SALT_SIZE 2
#define NOPASSWD                /* If this is defined then it will check
				   if the user exists in /etc/passwd! */

#include <sys/types.h>
#include <fcntl.h>
#include <syslog.h>
#include <stdlib.h>
#include <stdio.h>
#include <ctype.h>
#include <strings.h>
#include <errno.h>
/*#include <varargs.h>*/
#include <pwd.h>
#include <string.h>
#include <dirent.h>
#include <signal.h>
#include <sys/time.h>
#include <time.h>
#include <stdarg.h>

#include <krb5.h>

#ifndef _WIN32
#include <unistd.h>
#endif

#if !__GLIBC__ >= 2
#include <ulimit.h>
#endif

/* The following two functions were inspired by the original poppassd 1.2 */
/* written by John Norstad <j-norstad@nwu.edu>. */

void WriteToClient (char *fmt, ...) 
{

   va_list ap;
   va_start (ap, fmt);
   vfprintf (stdout, fmt, ap);
   fputs ("\r\n", stdout );
   fflush (stdout);
   va_end (ap);
}

void ReadFromClient (char *line)
{
   char *sp;
   int i;

   bzero(line, BUFSIZE);
   fgets (line, BUFSIZE-1, stdin);
   if ((sp = strchr(line, '\n')) != NULL) *sp = '\0'; 
   if ((sp = strchr(line, '\r')) != NULL) *sp = '\0'; 
	
   /* Convert initial keyword on line to lower case. */
	
   for (sp = line; isalpha(*sp); sp++) *sp = tolower(*sp);
   line[BUFSIZE-1] = '\0';
}

void get_name_from_passwd_file(program_name, kcontext, me, username)
    char * program_name;
    krb5_context kcontext;
    krb5_principal * me;
    char * username;
{
  struct passwd *pw;
  krb5_error_code code;

#ifdef PASSWD
  /* This checks if the user exists in /etc/passwd */
  if (pw = getpwnam(username)) {
    if ((code = krb5_parse_name(kcontext, pw->pw_name, me))) {
#else
    if ((code = krb5_parse_name(kcontext, username, me))) {
#endif
      com_err (program_name, code, "when parsing name %s", pw->pw_name);
      exit(1);
    }
#ifdef PASSWD
  } else {
    fprintf(stderr, "Unable to identify user from password file\n");
    exit(1);
  }
#endif
}

int main (int argc, char *argv[])
{
  char line[BUFSIZE];
  char user[BUFSIZE];
  char salt[SALT_SIZE];
  char oldpass[BUFSIZE];
  char newpass[BUFSIZE];
  int length, num_alpha, num_nonalpha, i;
  char *encrypt_pw;
  krb5_error_code ret;
  krb5_context context;
  krb5_principal princ;
  char *pname;
  krb5_ccache ccache;
  krb5_get_init_creds_opt opts;
  krb5_creds creds;

  char pw[1024];
  int pwlen;
  int result_code;
  krb5_data result_code_string, result_string;

  pname = argv[1];

  *user = *oldpass = *newpass = 0;
  num_alpha = num_nonalpha = 0; 

  if ( ret = krb5_init_context(&context) )
    {
      com_err(argv[0], ret, "initializing kerberos library");
    }


  /* Write error messages to syslog. */
  openlog("poppassd", LOG_PID, LOG_LOCAL4);

  WriteToClient ("200 kpoppassd v%s hello, who are you?", VERSION);

  ReadFromClient (line);
  sscanf (line, "user %" MAX_LEN_USERNAME "s", user) ;
  if ( strlen (user) == 0 )
    {
      WriteToClient ("500 Username required.");
      exit(1);
    }

  get_name_from_passwd_file(argv[0], context, &princ, user);

  krb5_get_init_creds_opt_init(&opts);
  krb5_get_init_creds_opt_set_tkt_life(&opts, 5*60);
  krb5_get_init_creds_opt_set_renew_life(&opts, 0);
  krb5_get_init_creds_opt_set_forwardable(&opts, 0);
  krb5_get_init_creds_opt_set_proxiable(&opts, 0);

  WriteToClient ("200 Your password please.");
  ReadFromClient (line);
  sscanf (line, "pass %" MAX_PARSELEN_PASS "s", oldpass) ;
  length = strlen(oldpass);
  if( length > MAX_LEN_PASS ) 
    {
      WriteToClient("500 Old password is incorrect.");
      exit(1);
    }

  if( ret = krb5_get_init_creds_password(context, &creds, princ, oldpass,
					 krb5_prompter_posix, NULL, 
					 0, "kadmin/changepw", &opts) )
    {
      if (ret == KRB5KRB_AP_ERR_BAD_INTEGRITY)
	com_err(argv[0], 0,
		"Password incorrect while getting initial ticket");
      else
	com_err(argv[0], ret, "getting initial ticket");
     
      WriteToClient ("500 Old password is incorrect.");
      syslog(LOG_ERR, "old password is incorrect for user %s", user);
          
      /* Pause to make brute force attacks harder. */
      sleep(BAD_PASS_DELAY);
      exit(1);
    }
     
  WriteToClient ("200 Your new password please.");
  ReadFromClient (line);
  sscanf (line, "newpass %" MAX_PARSELEN_PASS "s", newpass);
  length = strlen(newpass);
  if ( length < MIN_LEN_PASS )
    {
      WriteToClient ("500 New password is too short.");
      exit(1);
    }
  if ( length > MAX_LEN_PASS ) 
    {
      WriteToClient("500 New password is too long.");
      exit(1);
    }

  /* Check for trusted system compliance. */
  for ( i = 0; i < length; i++ )
    {
      if ( isalpha(newpass[i]) )
	num_alpha++;
      else
	num_nonalpha++;
    }
  if ( !((num_alpha >= 2) && (num_nonalpha >= 1)) )
    {
      WriteToClient("500 New password must contain at least two alpha characters and one nonalpha character.");
      exit(1);
    }

  if (ret = krb5_change_password(context, &creds, newpass,
				 &result_code, &result_code_string,
				 &result_string)) 
    {
      com_err(argv[0], ret, "changing password");
      WriteToClient("500 Server error, password not changed.");
      exit(1);
    }
  else 
    {

      if ( !result_code )
	{
	  syslog(LOG_ERR, "changed POP3 password for %s", user);
	  WriteToClient("200 Password changed, thank-you.");
	  ReadFromClient (line);

	  if (strncmp(line, "quit", 4) != 0)
	    {
	      WriteToClient("500 Quit required.");
	      exit (1);
	    }
	}
      /* Unfortunately the Kerberos API documentation is not all that great. */
      /* If the kerberos server rejects a password change then krb5_change_password */
      /* will return a 4 (?) */
      else if ( result_code == 4 )
	{
	  WriteToClient("500 Server error, password change rejected.");
	  exit(1);
	}
      else
	{
	  WriteToClient("500 Server error, password not changed.");
	  exit(1);
	}
    }

  if (result_code) {
    /* FIX this to WriteToClient() */
    /*printf("%.*s%s%.*s\n",*/
    /*result_code_string.length, result_code_string.data,*/
    /*result_string.length?": ":"",*/
    /*result_string.length, result_string.data);*/
    exit(2);
  }

  free(result_string.data);
  free(result_code_string.data);

  WriteToClient("200 Bye.");
  closelog();
  exit(0);
}

