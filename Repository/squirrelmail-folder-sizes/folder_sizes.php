<?php
/**
 * folder_sizes
 * By Robin Rainton <robin@rainton.com>
 * http://rainton.com/fun/freeware
 *
 * This page shows the header and details of folder usage. It is expected that
 * it will be invoked by the link that sits under the folder list in the left
 * hand frame.
 * Copyright (c) 2002-2003 Robin Rainton
 * Copyright (c) 2006 The SquirrelMail Project Team
 * @version $Id: folder_sizes.php,v 1.7 2006/05/01 11:40:46 tokul Exp $
 * @package sm-plugins
 * @subpackage folder_sizes
 */

/** SquirrelMail init */
if (file_exists('../../include/init.php')) {
  /* sm 1.5.2+*/

  /* main init script */
  include_once('../../include/init.php');
} else {
  /* sm 1.4.0+ */

  /** @ignore */
  define('SM_PATH', '../../');
  /* main init script */
  include_once(SM_PATH . 'include/validate.php');
}
/* load getTimeStamp() for IMAP functions */
include_once(SM_PATH . 'functions/date.php');
/* load decodeheader() (sm 1.4.0) */
include_once(SM_PATH . 'functions/mime.php');
/* load IMAP functions */
include_once(SM_PATH . 'functions/imap_general.php');
include_once(SM_PATH . 'functions/imap_messages.php');
include_once(SM_PATH . 'functions/display_messages.php');
/* load own functions */
include_once(SM_PATH . 'plugins/folder_sizes/functions.php');

/* get delimiter */
sqgetGlobalVar('delimiter', $delimiter,     SQ_SESSION);
  
displayPageHeader($color, 'None');

/*
 * The main function to do the work is in the setup.php file for this plugin.
 * That's because the list can also be shown on the bottom of the Folders
 * page.
 */
folder_sizes_list();

if (check_sm_version(1,5,1)) {
  $oTemplate->display('footer.tpl');
} else {
  echo '</body></html>';
}
/**
 * Local variables:
 * mode: php
 * c-basic-offset: 2
 * End:
 * vim: syntax=php et ts=2
 */
?>