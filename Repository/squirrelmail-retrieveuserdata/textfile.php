<?php
  /*
   *  textfile.php
   *
   *  Textfile Access 0.1
   *  SquirrelMail Retrieve User Data Plugin
   *  By Ralf Kraudelt <kraude@wiwi.uni-rostock.de>
   *
   *  The function retrieve_data() retrives the user's data from a plain
   *  text file. It should contain data in the following order:
   *  uid, full name, email address
   *  uid is the SM logon username
   *
   *  textfile.php uses some configuration values from config.php, please
   *  have a look at it.
   *  
   */

  function retrieve_data ($uid, $passwd) {
    global $SQRUD_TEXTFILE_NAME, $SQRUD_TEXTFILE_SEP;
	   
    require_once('../config/config.php');

    $file_error = 0;
    $found = 0;
    $common_name = '';
    $mail_address = '';
    
    // open file
    $file = fopen($SQRUD_TEXTFILE_NAME, 'rb');
    if (!$file) {
      $file_error = 1;
    }
    
    // read from file until we find a line
    if (!$file_error) {
      $line = fgets($file,4096);
      while ($line && !$found) {
        list($file_uid, $common_name, $mail_address) = explode($SQRUD_TEXTFILE_SEP, $line,3);
        if ( strcmp(trim($file_uid), $uid) == 0 ) {
          $found = 1;
        }
        $line = fgets($file,4096);
      };
    }
     

    if (!$file_error) {
      fclose($file);
    }
    
    if (!$found) {
      $file_error = 1;
    }
        
    return array('error'=>$file_error,
                 'common_name'=>trim($common_name),
		 'mail_address'=>trim($mail_address));

  }
   

?>
