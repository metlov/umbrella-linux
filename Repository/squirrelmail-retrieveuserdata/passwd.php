<?php
  /*
   *  passwd.php
   *
   *  Passwd File Access 0.1
   *  SquirrelMail Retrieve User Data Plugin
   *  By Ralf Kraudelt <kraude@wiwi.uni-rostock.de>
   *
   *  Retrieve username from GECOS field in passwd file through POSIX function  
   */

  function retrieve_data ($uid, $passwd) {
  
    $error = 0;
    
    $result = posix_getpwnam( $uid );
    
    $common_name = strtok($result['gecos'],',');
    
    if ( empty($common_name) ) {
       $error = 1;
    }
      
    return array('error'=>$error, 'common_name'=>$common_name, 'mail_address'=>'');

  }
   
?>
