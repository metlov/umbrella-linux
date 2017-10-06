<?php
/**
 * folder_sizes -- Version 1.4
 * By Robin Rainton <robin@rainton.com>
 * http://rainton.com/fun/freeware
 *
 * Utilise the "folders_bottom" and/or "left_main_after" hooks to display
 * (or link to display) folder size and messages count.
 * Copyright (c) 2002-2003 Robin Rainton
 * Copyright (c) 2006 The SquirrelMail Project Team
 * @version $Id: setup.php,v 1.8 2006/05/24 06:11:05 tokul Exp $
 * @package sm-plugins
 * @subpackage folder_sizes
 */

/** Init plugin */
function squirrelmail_plugin_init_folder_sizes() {
  global $squirrelmail_plugin_hooks;
  $squirrelmail_plugin_hooks['folders_bottom']['folder_sizes'] =
    'folder_sizes_folders_page';
  $squirrelmail_plugin_hooks['left_main_after']['folder_sizes'] =
    'folder_sizes_link';
  $squirrelmail_plugin_hooks['optpage_loadhook_folder']['folder_sizes'] =
    'folder_sizes_optpage';
  $squirrelmail_plugin_hooks['loading_prefs']['folder_sizes'] =
    'folder_sizes_pref';
}

/**
 * Adds link to left_main.php page
 */
function folder_sizes_link() {
  include_once(SM_PATH . 'plugins/folder_sizes/functions.php');
  folder_sizes_link_do();
}

/**
 * Adds block in folders page
 */
function folder_sizes_folders_page() {
  /* load getTimeStamp() for IMAP functions */
  include_once(SM_PATH . 'functions/date.php');
  /**
   * load imap functions (don't load them in functions.php, 
   * because we don't need them in loading_prefs hook)
   */
  include_once(SM_PATH . 'functions/imap_general.php');
  include_once(SM_PATH . 'functions/imap_messages.php');
  /* load sqm_baseuri() */
  include_once(SM_PATH . 'functions/display_messages.php');
  /** load own functions */
  include_once(SM_PATH . 'plugins/folder_sizes/functions.php');
  folder_sizes_folders_page_do();
}

/**
 * Adds optpage widgets on folders page
 * @since 1.5
 */
function folder_sizes_optpage() {
  include_once(SM_PATH . 'plugins/folder_sizes/functions.php');
  folder_sizes_optpage_do();
}

/** Loads user's prefs */
function folder_sizes_pref() { 
  include_once(SM_PATH . 'plugins/folder_sizes/functions.php');
  folder_sizes_pref_do();
}

/**
 * Returns plugin's version string
 * @return string version string
 * @since 1.5
 */
function folder_sizes_version() {
  return '1.5';
}

/**
 * Local variables:
 * mode: php
 * c-basic-offset: 2
 * End:
 * vim: syntax=php et ts=2
 */
?>