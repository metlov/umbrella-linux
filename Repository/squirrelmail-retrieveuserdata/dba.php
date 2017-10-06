<?php
  /*
   *  dba.php
   *
   *  DBA Access 0.1
   *  SquirrelMail Retrieve User Data Plugin
   *  By Ralf Kraudelt <kraude@wiwi.uni-rostock.de>
   *
   *  The function retrieve_data() retrives the user's data from a DBM-like
   *  database. The database can be created by using create_db.php on the
   *  command line and a file with lines:
   *  uid,fullname,emailaddress 
   *
   *  dba.php uses some configuration values from config.php, please
   *  have a look at it.
   *  
   */

  function retrieve_data ($uid, $passwd) {
    global $SQRUD_DBA_NAME, $SQRUD_DBA_HANDLER, $SQRUD_DBA_SEP;
	   
    require_once('../config/config.php');

    $dba_error = 0;
    $common_name = '';
    $mail_address = '';
    
    // open database
    $dba = dba_open($SQRUD_DBA_NAME, 'r', $SQRUD_DBA_HANDLER);
    error_log($SQRUD_DBA_HANDLER."\n",3,"/tmp/ralf.err");
    if (!$dba) {
      $dba_error = 1;
    }
    
    // fetch key from database
    if (!$dba_error) {
      $value = dba_fetch($uid, $dba);
      if (!$value) {
        $dba_error = 1;
      } else {
        list($common_name, $mail_address) = explode($SQRUD_DBA_SEP, $value, 2);
      }
    }
     
    if (!$dba_error) {
      dba_close($dba);
    }
    
    return array('error'=>$dba_error,
                 'common_name'=>trim($common_name),
		 'mail_address'=>trim($mail_address));

  }
   
?>
