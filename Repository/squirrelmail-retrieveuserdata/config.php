<?php

  /*
   * Make everything global or nothing works, better solution?
   */
  global $SQRUD_RETRIEVE_ON_EVERY_LOGIN, $SQRUD_RETRIEVE_DATA_FROM;
  global $SQRUD_LDAP_SERVER, $SQRUD_LDAP_USERNAME, $SQRUD_LDAP_MAIL,
         $SQRUD_LDAP_BASE_DN, $SQRUD_LDAP_FROM_MAIN_CONFIG,
	 $SQRUD_LDAP_UID, $SQRUD_LDAP_ANONYMOUS_BIND,$SQRUD_LDAP_BIND_RDN,
         $SQRUD_LDAP_MAIL_ALIASES, $SQRUD_LDAP_MAIL_ALIAS_PREFIX;
  global $SQRUD_MYSQL_SERVER, $SQRUD_MYSQL_USER, $SQRUD_MYSQL_PASS,
         $SQRUD_MYSQL_DB, $SQRUD_MYSQL_TABLE, $SQRUD_MYSQL_NAME_FIELD,
         $SQRUD_MYSQL_MAIL_FIELD, $SQRUD_MYSQL_USERNAME_FIELD,
         $SQRUD_MYSQL_USE_PERSISTENT;
  global $SQRUD_VPOP_VUSERINFO;
  global $SQRUD_TEXTFILE_NAME, $SQRUD_TEXTFILE_SEP;
  global $SQRUD_DBA_NAME, $SQRUD_DBA_HANDLER, $SQRUD_DBA_SEP;
  global $SQRUD_PGSQL_SERVER, $SQRUD_PGSQL_PORT, $SQRUD_PGSQL_USER,
         $SQRUD_PGSQL_PASS, $SQRUD_PGSQL_DB, $SQRUD_PGSQL_TABLE,
         $SQRUD_PGSQL_NAME_FIELD, $SQRUD_PGSQL_MAIL_FIELD,
         $SQRUD_PGSQL_USERNAME_FIELD, $SQRUD_PGSQL_USE_PERSISTENT;
  /*
   *  =====================================================================
   *  General configuration options
   *  =====================================================================
   *
   *  $SQRUD_RETRIEVE_DATA_FROM - file containing retrieve_data() function,
   *                              see ldap.php for an example
   *  $SQRUD_RETRIEVE_ON_EVERY_LOGIN - to re$SQRUD_LDAP_BIND_RDNtrieve the user's data on every
   *                                   login, set it to 1
   */
  $SQRUD_RETRIEVE_DATA_FROM = "ldap.php";
//  $SQRUD_RETRIEVE_DATA_FROM = "mysql.php";
//  $SQRUD_RETRIEVE_DATA_FROM = "passwd.php";
//  $SQRUD_RETRIEVE_DATA_FROM = "textfile.php";
//  $SQRUD_RETRIEVE_DATA_FROM = "dba.php";
//  $SQRUD_RETRIEVE_DATA_FROM = "vpopmail.php";
//  $SQRUD_RETRIEVE_DATA_FROM = "pgsql.php";

  $SQRUD_RETRIEVE_ON_EVERY_LOGIN = 1;

  /*
   *  =====================================================================
   *  LDAP configuration, if you use ldap.php in $SQRUD_RETRIEVE_DATA_FROM
   *  =====================================================================
   *
   * $SQRUD_LDAP_FROM_MAIN_CONFIG - use configuration of first (=zeroth)
   *       LDAP addressbook server from ../config/config.php
   * $SQRUD_LDAP_USERNAME - name of the LDAP attribute, where the user's name
   *       is stored 
   * $SQRUD_LDAP_MAIL - same for the email address
   * $SQRUD_LDAP_MAIL_ALIASES - extra email addresses may be placed under a 
   *                            different name. Like 'proxyaddresses' for 
   *                            Microsoft AD.
   * $SQRUD_LDAP_MAIL_ALIAS_PREFIX - Extra email addresses are sometimes 
   *                                 labeled like SMTP: email@example.com
   *                                 set this to that prefix. (SMTP is used
   *                                 for Microsoft AD.)
   * $SQRUD_LDAP_UID - name of LDAP attribute containing SM logon username
   *                   typically "UID" for Unix and "sAMAccountName"
   *                   for Microsoft Active Directory
   * $SQRUD_LDAP_SERVER[0] - your LDAP server configuration, 
   *       used, if $SQRUD_LDAP_FROM_MAIN_CONFIG is not 1
   * $SQRUD_LDAP_ANONYMOUS_BIND - anonymous bind instead of SM username/password
   * $SQRUD_LDAP_BIND_RDN - template for RDN used for binding to LDAP, if you
   *       don't allow anonymous binds to access the data; the value SQRUD_UID
   *       will be replaced with the current user's ID during runtime
   */
   $SQRUD_LDAP_FROM_MAIN_CONFIG = 0;
   $SQRUD_LDAP_UID = "UID";
   $SQRUD_LDAP_USERNAME = "cn";
   $SQRUD_LDAP_MAIL = "mailLocalAddress"; // or "mail"
   $SQRUD_LDAP_MAIL_ALIASES = "proxyaddresses";
   $SQRUD_LDAP_MAIL_ALIAS_PREFIX = "smtp:";
   $SQRUD_LDAP_ANONYMOUS_BIND = 0;
   $SQRUD_LDAP_BIND_RDN = "uid=SQRUD_UID,ou=people,dc=wiwi,dc=uni-rostock,dc=de";
//   $SQRUD_LDAP_BIND_RDN = "MyWindowsDomain\\SQRUD_UID";  // for Microsoft ADS
   $SQRUD_LDAP_SERVER[0] = array(
     'host' => 'ldap.wiwi.uni-rostock.de',      // hostname, required
     'base' => 'dc=wiwi,dc=uni-rostock,dc=de',  // base distinguished name, required
     'port' => '389',                           // port, optional
     'charset' => 'utf-8'                       // charset, optional
   );
   
  /*
   *  =====================================================================
   *  MySQL Configuration, if you use mysql.php in $SQRUD_RETRIEVE_DATA_FROM
   *  =====================================================================
   *
   *  $SQRUD_MYSQL_SERVER - your MySQL server
   *  $SQRUD_MYSQL_USER - MySQL server username
   *  $SQRUD_MYSQL_PASS - MySQL server password
   *  $SQRUD_MYSQL_DB - MySQL database
   *  $SQRUD_MYSQL_TABLE - The table to lookup the values in
   *  $SQRUD_MYSQL_NAME_FIELD - The field containing the full name
   *  $SQRUD_MYSQL_MAIL_FIELD - The field containing the emailaddress 
   *  $SQRUD_MYSQL_USERNAME_FIELD - The field to match
   *  $SQRUD_MYSQL_USE_PERSISTENT - Whether or not to use persistent database
   *                                connections.
   *  
   */ 
   /*
 *  $SQRUD_MYSQL_SERVER = 'sql.server.net';
 *  $SQRUD_MYSQL_USER = 'username';
 *  $SQRUD_MYSQL_PASS = 'p@ssword';
 *  $SQRUD_MYSQL_DB = 'database';
 *  $SQRUD_MYSQL_TABLE = 'table';
 *  $SQRUD_MYSQL_NAME_FIELD = 'fullname';
 *  $SQRUD_MYSQL_MAIL_FIELD = 'email';
 *  $SQRUD_MYSQL_USERNAME_FIELD = 'id';
 *  $SQRUD_MYSQL_USE_PERSISTENT = 1;
 *
 */

  /*
   *  =====================================================================
   *  Textfile Configuration, if you use textfile.php in
   *                                   $SQRUD_RETRIEVE_DATA_FROM
   *  =====================================================================
   *
   * $SQRUD_TEXTFILE_NAME - filename or URL
   * $SQRUD_TEXTFILE_SEP - seperator between login, username and email address
   */ 
//   $SQRUD_TEXTFILE_NAME = '/tmp/myusers.txt';
//   $SQRUD_TEXTFILE_SEP = ',';

  /*
   *  =====================================================================
   *  DBA Configuration for DBM databases, if you use dba.php in 
   *                                      $SQRUD_RETRIEVE_DATA_FROM
   *  =====================================================================
   *
   * $SQRUD_DBA_NAME - filename or URL
   * $SQRUD_DBA_HANDLER - database type, depends on your PHP installation
   *                      choose between dbm, ndbm, gdbm, db2, db3, cdb
   * $SQRUD_DBA_SEP - seperator between username and email address
   */ 
//   $SQRUD_DBA_NAME = '/tmp/myusers.db';
//   $SQRUD_DBA_HANDLER = 'gdbm';
//   $SQRUD_DBA_SEP = ',';

  /*
   *  =====================================================================
   *  VPOPMail Configuration if you use vpopmail.php in
   *                                      $SQRUD_RETRIEVE_DATA_FROM
   *  =====================================================================
   *
   * $SQRUD_VPOP_VUSERINFO - filename or URL
   */
//   $SQRUD_VPOP_VUSERINFO = "/mail/bin/vuserinfo";

   /*
    *  PostgreSQL Configuration, if you use pgsql.php in $SQRUD_RETRIEVE_DATA_FROM
    *
    *  $SQRUD_PGSQL_SERVER - your PostgreSQL server
    *  $SQRUD_PGSQL_PORT - TCP/IP port the PostgreSQL server is listening on
    *  $SQRUD_PGSQL_USER - PostgreSQL server username
    *  $SQRUD_PGSQL_PASS - PostgreSQL server password
    *  $SQRUD_PGSQL_DB - PostgreSQL database
    *  $SQRUD_PGSQL_TABLE - The table to lookup the values in
    *  $SQRUD_PGSQL_NAME_FIELD - The field containing the full name
    *  $SQRUD_PGSQL_MAIL_FIELD - The field containing the emailadress
    *  $SQRUD_PGSQL_USERNAME_FIELD - The field to match
    *  $SQRUD_PGSQL_USE_PERSISTENT - Whether or not to use persistent database
    *                                connections.
    *
    */
/*
 *  $SQRUD_PGSQL_SERVER = 'sql.server.net';
 *  $SQRUD_PGSQL_PORT = 5432;
 *  $SQRUD_PGSQL_USER = 'username';
 *  $SQRUD_PGSQL_PASS = 'p@ssword';
 *  $SQRUD_PGSQL_DB = 'database';
 *  $SQRUD_PGSQL_TABLE = 'table';
 *  $SQRUD_PGSQL_NAME_FIELD = 'fullname';
 *  $SQRUD_PGSQL_MAIL_FIELD = 'email';
 *  $SQRUD_PGSQL_USERNAME_FIELD = 'id';
 *  $SQRUD_PGSQL_USE_PERSISTENT = 1;
 *
 */

?>
