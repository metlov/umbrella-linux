<?php
   /*
    *  SquirrelMail Retrieve User Data Plugin 0.9
    *  By Ralf Kraudelt <kraude@wiwi.uni-rostock.de>
    *
    *  This plugin retrieves the user's name ($full_name in the user's 
    *  preferences) and email address ($email_address) from an external
    *  source. The access of LDAP and MySQL servers is implemented, but you can
    *  add other sources by writing your own access function (see ldap.php).
    *
    *  There are some options placed in config.php, have a look at it.
    *  Additionally the configuration options for the data source should be
    *  placed in config.php.
    *
    *  This plugin adds 2 values to the user's preferences file:
    *  $got_external_data - is set after a successful retrieval
    *  $full_name_backup - if you don't want your users to change their
    *                      full name, this one is used to get the user's name
    *                      instead of accessing your external source
    *   
    *  This plugin is written to minimize the access of your external data
    *  source. It's only used, if it has never successfully retrieved the user's
    *  data before ($got_external_data), and if you don't force the retrieval
    *  ($SQRUD_RETRIEVE_ON_EVERY_LOGIN).
    *
    *  If you need help with this, or see improvements that can be made, please
    *  email me directly at the address above.  I definately welcome suggestions
    *  and comments.  This plugin, as is the case with all SquirrelMail plugins,
    *  is not directly supported by the developers.  Please come to me off the
    *  mailing list if you have trouble with it.
    *
    */
    
   define(SM_PATH,"../"); 
   require_once(SM_PATH . 'plugins/retrieveuserdata/config.php');
   require_once(SM_PATH . "plugins/retrieveuserdata/$SQRUD_RETRIEVE_DATA_FROM");

   /*
    *  place plugin functions in SM
    */
   function squirrelmail_plugin_init_retrieveuserdata() {
      global $squirrelmail_plugin_hooks;

      $squirrelmail_plugin_hooks['loading_prefs']['retrieveuserdata'] = 'check_userdata';
   }

   /*
    *  after successful login; check, if we ever retrieved the user's data or
    *  we sould always retrieve the user's data; catch them, if required 
    */
   function check_userdata() {
     global $data_dir, $SQRUD_RETRIEVE_ON_EVERY_LOGIN;

     sqgetGlobalVar('username',$username);
     
     if (isset($username) && !empty($username)) {
       $got_external_userdata = getPref($data_dir, $username, 'got_external_userdata');
       // write initial value, what the hell is an empty integer?
       if (($got_external_userdata != 1 && $got_external_userdata != 0) || empty($got_external_userdata)) {
	 $got_external_userdata = 0;
	 setPref($data_dir, $username, 'got_external_userdata', 0);
       }
       // avoid unnecessary access of external data source
       if ($got_external_userdata == 0 || $SQRUD_RETRIEVE_ON_EVERY_LOGIN == 1) {
	 retrieve_external_userdata();
       } 
     } 
   }
   
   /*
    *  catch user data from external source and write them to the 
    *  preferences file
    */
   function retrieve_external_userdata() {
     global $data_dir;
     
     sqgetGlobalVar('username',$username);
     sqgetGlobalVar('key',$password,SQ_COOKIE);
     sqgetGlobalVar('onetimepad',$onetimepad);
     
     $cleartext_password = OneTimePadDecrypt($password, $onetimepad);
     $userdata = retrieve_data($username, $cleartext_password);
     
     if (!$userdata['error']) {
       
       if (!empty($userdata['mail_address'])) {

	 // single or multiple addresses returned by pluginplugin?
	 if (is_array($userdata['mail_address'])){
	   $addresses = $userdata['mail_address'];
	 } else {
	   $addresses[0] = $userdata['mail_address'];
	 }
	 
         // is there already a standard address?
	 $email = getPref($data_dir, $username, 'email_address');
	 
	 // save the address for later lookups
	 if(!empty($email)) {
	   $lookup[strtolower($email)] = 1;
	 }
	 
	 // get number of already used identities
	 $idents = getPref($data_dir, $username, 'identities');
	 if(empty($idents) || !isset($idents)) { $idents = 0; }

	 
	 // get already used addresses for later lookup
         for($i = 1;$i < $idents;$i++) {
           $email = getPref($data_dir, $username, 'email_address'.$i);
	   if(!empty($email)) {
	     $lookup[strtolower($email)] = 1;
	   }
         }

	 // compare addresses from data source with already saved ones and add
	 // new addresses to identities 
	 for($i = 0;isset($addresses[$i]);$i++) {
	   $temp = strtolower($addresses[$i]);
	   if(!isset($lookup[$temp])) {
	     if($idents == 0) {
	       setPref($data_dir,$username,'email_address',
		       $addresses[$i]);
               setPref($data_dir, $username, 'full_name',
	               $userdata['common_name']);
	     } else {
	       setPref($data_dir,$username,'email_address'.$idents,
		       $addresses[$i]);
               setPref($data_dir, $username, 'full_name'.$idents,
	               $userdata['common_name']);
	     }
	     $lookup[$temp] = 1;
             $idents++;
	   } 
	 }
	 
	 if($idents != getPref($data_dir, $username, 'identities')) {
	   setPref($data_dir, $username, 'identities', $idents);
	 }
       }
     
     setPref($data_dir, $username, 'got_external_userdata', 1);
     }
   }

?>
