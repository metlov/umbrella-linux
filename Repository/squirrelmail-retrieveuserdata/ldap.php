<?php
  /*
   *  ldap.php
   *
   *  LDAP Server Access 0.5
   *  SquirrelMail Retrieve User Data Plugin
   *  By Ralf Kraudelt <kraude@wiwi.uni-rostock.de>
   *
   *  The function retrieve_data() retrives the user's data from a
   *  LDAP server. The result of this function is an
   *  array("error", "common_name", "mail_address"). The error
   *  value has no specifc use - an unsuccessful retrieval leads to an error.
   *  Common_name contains the value, which is used as $full_name in the
   *  user's preferences file, mail_address is an array of $email_addresses.
   *  Using this function interface and return value, you can write your
   *  own functions to access databases, text files, ...
   *
   *  ldap.php uses some configuration values from config.php - please 
   *  have a look at this file and edit the values
   *
   *  An example of my own LDAP server configuration is given in config.php.
   *  
   */

  function retrieve_data ($uid, $passwd) {
    global $SQRUD_LDAP_SERVER, $SQRUD_LDAP_USERNAME, $SQRUD_LDAP_MAIL,
           $SQRUD_LDAP_FROM_MAIN_CONFIG, $SQRUD_LDAP_UID,
	   $SQRUD_LDAP_ANONYMOUS_BIND, $SQRUD_LDAP_BIND_RDN,
           $SQRUD_LDAP_MAIL_ALIASES, $SQRUD_LDAP_MAIL_ALIAS_PREFIX,
	   $ldap_server;
	   
    require_once('../config/config.php');

    $ldap_error = 0;
    $common_name = '';
    $mail_address = '';
    
    // Use LDAP server used as addressbook?
    if ($SQRUD_LDAP_FROM_MAIN_CONFIG == 1) {
      $SQRUD_LDAP_SERVER = $ldap_server;
    }
     
    // fill in optional fields
    if (empty($SQRUD_LDAP_SERVER[0]['port'])) {
      $SQRUD_LDAP_SERVER[0]['port'] = 389;
    }
     
    if (empty($SQRUD_LDAP_SERVER[0]['charset'])) {
      $SQRUD_LDAP_SERVER[0]['charset'] = 'utf-8';
    } else {
      $SQRUD_LDAP_SERVER[0]['charset'] = strtolower($SQRUD_LDAP_SERVER[0]['charset']);
    }

    // Connect server
    $ldap = ldap_connect($SQRUD_LDAP_SERVER[0]['host'], $SQRUD_LDAP_SERVER[0]['port']);
    if (!$ldap) {
      $ldap_error = 1;
    }

    // Bind to server
    if ($ldap_error == 0) {
      if ($SQRUD_LDAP_ANONYMOUS_BIND == 1) {
        $bind_result = ldap_bind( $ldap );
      } else {
        $bind_user = str_replace("SQRUD_UID", $uid, $SQRUD_LDAP_BIND_RDN);
        $bind_result = ldap_bind( $ldap, $bind_user, $passwd );
      }
      if (!$bind_result) {
        $ldap_error = 1;
      }
    }

    // Search for username
    if ($ldap_error == 0) {
      $search_result = ldap_search($ldap, $SQRUD_LDAP_SERVER[0]['base'],
                                   "$SQRUD_LDAP_UID=$uid",
                                  array($SQRUD_LDAP_USERNAME, $SQRUD_LDAP_MAIL,
					$SQRUD_LDAP_MAIL_ALIASES));
      if (!$search_result) {
        $ldap_error = 1;
      }
    }

    // Get entries from server
    if ($ldap_error == 0) {
      $info = ldap_get_entries($ldap, $search_result);
      
      if ($info['count'] >= 1) {
        // set return value of the name
        $common_name = $info[0][strtolower($SQRUD_LDAP_USERNAME)][0];
        $common_name = rud_charset_decode($common_name, $SQRUD_LDAP_SERVER[0]['charset']);
	
        // set return values of mail addresses
	for($i = 0;isset($info[0][strtolower($SQRUD_LDAP_MAIL)][$i]);$i++) {
	  $mail_address[$i] = $info[0][strtolower($SQRUD_LDAP_MAIL)][$i];
          $mail_address[$i] = rud_charset_decode($mail_address[$i], $SQRUD_LDAP_SERVER[0]['charset']);
	}
	
	// Strip out the prefix if it exists.
	for($i = 0;isset($info[0][strtolower($SQRUD_LDAP_MAIL_ALIASES)][$i]);$i++) {
	  preg_match("/^($SQRUD_LDAP_MAIL_ALIAS_PREFIX\s*)(.*)/i",
		     $info[0][strtolower($SQRUD_LDAP_MAIL_ALIASES)][$i],
		     $matches);
	  if(!empty($matches[2]) && isset($matches)) {
	    $mail_address[] = 
	      rud_charset_decode($matches[2], $SQRUD_LDAP_SERVER[0]['charset']);
	  }
	}
      } else {
        $ldap_error = 1;
      }
    }

    // Disconnect from server
    if ($ldap) {
      ldap_close($ldap);
    }

    return array('error'=>$ldap_error, 'common_name'=>$common_name, 'mail_address'=>$mail_address);

  }
   
  /*
   * Decode from charset used by this LDAP server to iso8859-1
   * Taken from functions/abook_ldap_server.php
   */
   function rud_charset_decode($str, $charset) {
     if ($charset == 'utf-8') {
       if (function_exists('utf8_decode'))
         return utf8_decode($str);
       else
         return $str;
       } else {
         return $str;
     }
   }
?>
