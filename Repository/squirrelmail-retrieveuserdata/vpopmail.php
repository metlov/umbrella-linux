<?php
   /*
    *  vpopmail.php
    *
    *  Retrieve User Data from VPOPMail Userinfos 0.1
    *  SquirrelMail Retrieve User Data Plugin
    *  By Ralf Kraudelt <kraude@wiwi.uni-rostock.de>
    *  vpopmail support by Dominique Leuenberger <dominique.leuenberger@gibb.ch>
    *
    *  The function retrieve_data() retrives the user's data using
    *  the external vuserinfo - program of vpopmail
    *  function is an array("error", "common_name", "mail_address"). The error
    *  value has no specifc use - an unsuccessful retrieval leads to an error.
    *  Common_name contains the value, which is used as $full_name in the
    *  user's preferences file, mail_address contains $email_address.
    *
    *  vpopmail.php uses some configuration values from config.php:
    *  $SQRUD_VPOP_VUSERINFO -> Complete path to binary vuserinfo
    *  
    */    

class vqpasswd {
 var $pw_name = '';
 var $pw_passwd = '';
 var $pw_uid = 0;
 var $pw_gid = 0;
 var $pw_gecos = '';
 var $pw_dir = '';
 var $pw_shell = '';
 var $pw_clear_password = '';
} 

function retrieve_data ($uid, $passwd) {
  global $SQRUD_VPOP_VUSERINFO; 

  require_once("../plugins/retrieveuserdata/config.php");

  list ($User, $Domain) = split('@', $uid); 
  $vpw = new vqpasswd;

  // Executing vuserinfo -c to get the comment Field (Full Name)
  $NewBuf = sprintf("%s -c %s@%s", $SQRUD_VPOP_VUSERINFO, $User,$Domain);
  exec($NewBuf, $Ausgabe);
  $common_name = $Ausgabe[0];
  $mail_address = '';

  return array("error"=>0, "common_name"=>$common_name, "mail_address"=>$uid);
}

?>
