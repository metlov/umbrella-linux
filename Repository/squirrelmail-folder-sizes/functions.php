<?php
/**
 * Folder Sizes plugin functions
 * Copyright (c) 2006 The SquirrelMail Project Team
 * Function dependencies:
 * sqimap_status_messages()
 * sqimap_get_small_header_list() - with workarounds for 1.5.x and 1.4.x
 *
 * @version $Id: functions.php,v 1.10 2006/05/24 06:08:07 tokul Exp $
 * @package sm-plugins
 * @subpackage folder_sizes
 */

/*
 * These next two (folder_sizes_link and folder_sizes_folders_page) are called
 * to either show a link to our standalone page (folder_sizes.php) or to
 * output the folder size details (from the bottom of the Folders page via
 * it's hook.
 */
function folder_sizes_link_do() {
  global $folder_sizes_left_link, $folder_sizes_link_button,
    $squirrelmail_language, $default_charset;

  set_my_charset();
  
  if (!isset($folder_sizes_left_link) || !$folder_sizes_left_link) return;

  // switch domain
  bindtextdomain('folder_sizes',SM_PATH . 'locale');
  textdomain('folder_sizes');

  if (function_exists('bind_textdomain_codeset')) {
    if ($squirrelmail_language == 'ja_JP') {
      bind_textdomain_codeset ('folder_sizes', 'EUC-JP');
    } else {
      bind_textdomain_codeset ('folder_sizes', $default_charset );
    }
  }

  echo '<P><CENTER>';
  if ($folder_sizes_link_button) {
    echo '<form target="right" action="../plugins/folder_sizes/folder_sizes.php" method="get">';
    echo '<input class="small" type="submit" value="' . _("Folder Sizes") . '"></form>';
  } else {
    echo '<A HREF="../plugins/folder_sizes/folder_sizes.php" TARGET="right">' .
         _("Folder Sizes") . '</A>';
  }
  echo '</CENTER></p>';

  // revert domain
  bindtextdomain('squirrelmail',SM_PATH . 'locale');
  textdomain('squirrelmail');
}

/** internal function to add block to folders page */
function folder_sizes_folders_page_do() {
  global $folder_sizes_on_folder_page;
  if (isset($folder_sizes_on_folder_page) && $folder_sizes_on_folder_page) {
    echo "<P>";
    folder_sizes_list();
  }
}

/**
 */
function folder_sizes_pref_do() { 
  global $username, $data_dir;
  global $folder_sizes_left_link, $folder_sizes_on_folder_page,
         $folder_sizes_subtotals, $folder_sizes_link_button;

  $folder_sizes_left_link = getPref($data_dir, $username, 'folder_sizes_left_link',true);
  $folder_sizes_link_button = getPref($data_dir, $username, 'folder_sizes_link_button',false);
  $folder_sizes_on_folder_page = getPref($data_dir, $username, 'folder_sizes_on_folder_page',false);
  $folder_sizes_subtotals = getPref($data_dir, $username, 'folder_sizes_subtotals',false);
}

/**/
function folder_sizes_optpage_do() {
  global $optpage_data, $squirrelmail_language, $default_charset;

  set_my_charset();

  // switch domain
  bindtextdomain('folder_sizes',SM_PATH . 'locale');
  textdomain('folder_sizes');

  if (function_exists('bind_textdomain_codeset')) {
    if ($squirrelmail_language == 'ja_JP') {
      bind_textdomain_codeset ('folder_sizes', 'EUC-JP');
    } else {
      bind_textdomain_codeset ('folder_sizes', $default_charset );
    }
  }

  /* Load Folder Sizes options */
  $optgrp = _("Folder Sizes Options");
  $optvals = array();

  $optvals[] = array(
    'name'    => 'folder_sizes_left_link',
    'caption' => _("Show link under folder list"),
    'type'    => SMOPT_TYPE_BOOLEAN,
    'refresh' => SMOPT_REFRESH_FOLDERLIST
  );
  $optvals[] = array(
    'name'    => 'folder_sizes_link_button',
    'caption' => _("Show button instead of link"),
    'type'    => SMOPT_TYPE_BOOLEAN,
    'refresh' => SMOPT_REFRESH_FOLDERLIST
  );
  $optvals[] = array(
    'name'    => 'folder_sizes_on_folder_page',
    'caption' => _("Show block on folders page"),
    'type'    => SMOPT_TYPE_BOOLEAN
  );
  $optvals[] = array(
    'name'    => 'folder_sizes_subtotals',
    'caption' => _("Show subtotals"),
    'type'    => SMOPT_TYPE_BOOLEAN
  );

  /* Add our option data to the global array. */
  $optpage_data['grps']['folder_sizes'] = $optgrp;
  $optpage_data['vals']['folder_sizes'] = $optvals;

  // revert domain
  bindtextdomain('squirrelmail',SM_PATH . 'locale');
  textdomain('squirrelmail');
}

/*
 * Next few do the work.
 * First smaller one counts the messages and size in a mailbox folder.
 * Second outputs sub-totals for each level.
 * The third actually calculates all sizes and builds HTML table for these.
 */
function get_folder_size($imapConnection, $mailbox) {
  static $annotate_more;
  
  /*
   * Cyrus-imap provides mailbox size info through ANNOTATEMORE. Make use of it
   * because it's much faster.
   * @author Marc Groot Koerkamp
   */
  if (!isset($annotate_more)) {
    $annotate_more = sqimap_capability($imapConnection,'ANNOTATEMORE');
  }
  if ($annotate_more) {
    //* ANNOTATION "inbox" "/vendor/cmu/cyrus-imapd/size" ("value.shared" "78357886")
    $query = 'GETANNOTATION "'.$mailbox.'" "/vendor/cmu/cyrus-imapd/size" "value"';
    // don't handle errors inside sqimap_run_command (third argument)
    $aResponse = sqimap_run_command($imapConnection,$query,false,$response,$message);
    if (is_array($aResponse) && isset($aResponse[0]) &&
        preg_match('/\(\"value\.shared\"\s\"(.*)\"\)/',$aResponse[0],$aReg)) {
      $size = $aReg[1];
      $aStatus = sqimap_status_messages($imapConnection,$mailbox);
      return array($aStatus['MESSAGES'],$aStatus['UNSEEN'], $size);
    } else {
      $annotate_more = false;
    }
  }
  /*
   * default operation.
   * get server side sort setting
   */
  global $allow_server_sort;

  /*
   * Select the appropriate mailbox (folder) and retrieve message headers
   */
  $size = 0;
  $num_messages = sqimap_get_num_messages($imapConnection, $mailbox);
  if ($num_messages > 0) {
    $mbxresponse = sqimap_mailbox_select($imapConnection, $mailbox);
    if (check_sm_version(1,5,0)) {
      $msgs_list = sqimap_get_small_header_list($imapConnection, NULL, array(), array('RFC822.SIZE'));
      foreach ($msgs_list as $hdr) {
        $size += $hdr['SIZE'];
      }
      unset($msgs_list);
    } else {
      /**
       * older plugin code to fetch message information in batches
       *
       * $msgs_list = sqimap_get_small_header_list($imapConnection, NULL, '999999');
       * does not work on large folders.
       */

      /*
       * Only grab small number of headers at a time.
       */
      if ($allow_server_sort) {
        $id = sqimap_get_sort_order($imapConnection, 6, $mbxresponse);
      } else {
        $id = sqimap_get_php_sort_order($imapConnection, $mbxresponse);
      }
      while (count($id) > 0) {
        $msgs_list = sqimap_get_small_header_list($imapConnection, array_slice($id, 0, 50));
        /*
         * We have the headers list now - just add up the size and return it and count
         */
        foreach ($msgs_list as $hdr) {
          $size += $hdr['SIZE'];
        }
        /*
         * Trash message list we no longer need.
         */
        unset($msgs_list);
        /*
         * Next lot
         */
        $id = array_slice($id, 50);
      }
    }
  }
  return array($num_messages,
               sqimap_unseen_messages($imapConnection, $mailbox),
               $size);
}

/*
 * Sub to add the totals and subtotals for the passed depth.
 * If Depth is zero, this is the grand total and should be in bold.
 */

function folder_sizes_add_totals($from_depth, $to_depth)
{
  global $color;
  global $subfolders, $subcount, $subunread, $subsize, $tab_cols;

  for ($lp = $from_depth; $lp > $to_depth; $lp--) {
    $indent = $lp > 0 ? "<TD BGCOLOR=\"$color[4]\" COLSPAN=$lp>&nbsp;</TD>"
                     : "";
    echo "<TR BGCOLOR=\"$color[0]\">$indent<TD" .
         ($tab_cols - $lp > 3 ? " colspan=" . ($tab_cols - $lp - 3)
                              : "") . ">" .
         ($lp > 0 ? "" : "<B>") .
         sprintf(ngettext("%d Folder","%d Folders",$subfolders[$lp]),$subfolders[$lp]) .
          ($lp > 0 ? "" : "</B>") . "</TD>" .
         "<TD>" . ($lp > 0 ? "" : "<B>") .
         $subcount[$lp] .
         ($lp > 0 ? "" : "</B>") . "</TD>" .
         "<TD>" . ($lp > 0 ? "" : "<B>") .
         $subunread[$lp] .
         ($lp > 0 ? "" : "</B>") . "</TD>" .
         "<TD>" . ($lp > 0 ? "" : "<B>") .
         show_readable_size($subsize[$lp]) .
         ($lp > 0 ? "" : "</B>") . "</TD></TR>\n";

    $next_depth = $lp - 1;

    if (!isset($subfolders[$next_depth])) $subfolders[$next_depth] = 0;
    if (!isset($subcount[$next_depth])) $subcount[$next_depth] = 0;
    if (!isset($subunread[$next_depth])) $subunread[$next_depth] = 0;
    if (!isset($subsize[$next_depth])) $subsize[$next_depth] = 0;

    $subfolders[$next_depth] += $subfolders[$lp];
    $subcount[$next_depth] += $subcount[$lp];
    $subunread[$next_depth] += $subunread[$lp];
    $subsize[$next_depth] += $subsize[$lp];
  }
}

/*
 * Main functon for calculating and outputting folder list.
 */
function folder_sizes_list() {
  $right_main = sqm_baseuri() . 'src/right_main.php';

  sqgetGlobalVar('username',  $username,      SQ_SESSION);
  sqgetGlobalVar('onetimepad',$onetimepad,    SQ_SESSION);
  sqgetGlobalVar('key',       $key,           SQ_COOKIE);

  /*
   * First - we go about the business of getting a list of folders.
   */
  global $imapServerAddress, $imapPort, $delimiter;
  $imapConnection = sqimap_login ($username, $key, $imapServerAddress, $imapPort, 0);
  $boxes = sqimap_mailbox_list($imapConnection);

  /*
   * Work through the folder list and count messages in each.
   * We will add the message count for each folder to the original boxes
   * array. This is so we can determine how deep the directories are before we
   * start.
   * This is necessary because we need to know how many columns are going to
   * be presented to begin with.
   */

  $max_depth = 0;
  for ($boxnum = 0; $boxnum < count($boxes); $boxnum++) {
    
    /*
     * Record new depth if it's greatest so far
     */

    $parts = explode($delimiter, $boxes[$boxnum]['unformatted-disp']);
    $boxes[$boxnum]['display'] = array_pop($parts);
    $boxes[$boxnum]['depth'] = count($parts);

    if ($boxes[$boxnum]['depth']  > $max_depth)
      $max_depth = $boxes[$boxnum]['depth'];

    /*
     * Get sizes for this folder if it's selectable
     */
    if (!in_array('noselect', $boxes[$boxnum]['flags'])) {
      list($count, $unread, $size) =
        get_folder_size($imapConnection, $boxes[$boxnum]['unformatted']);
      $boxes[$boxnum]['count'] = $count;
      $boxes[$boxnum]['unread'] = $unread;
      $boxes[$boxnum]['size'] = $size;
    }
  }

  global $squirrelmail_language, $default_charset;
  set_my_charset();

  // switch domain
  bindtextdomain('folder_sizes',SM_PATH . 'locale');
  textdomain('folder_sizes');

  if (function_exists('bind_textdomain_codeset')) {
    if ($squirrelmail_language == 'ja_JP') {
      bind_textdomain_codeset ('folder_sizes', 'EUC-JP');
    } else {
      bind_textdomain_codeset ('folder_sizes', $default_charset );
    }
  }
  
  global $tab_cols;
  $tab_cols = $max_depth + 4;
  
  /*
   * Now we know how many columns the table will have, send it out.
   * Start the table in the same way as others on Folders page, so everything
   * looks nice and neat.
   * This also looks nice on it's own page luckily ;-)
   */

  $indent_width = 20;

  global $color;
  echo "<TABLE BGCOLOR=\"$color[0]\" WIDTH=\"70%\" ALIGN=\"CENTER\" cellpadding=1 cellspacing=0 border=0>\n";
  echo "<TR><TD><TABLE BGCOLOR=\"$color[0]\" WIDTH=\"100%\" ALIGN=CENTER cellpadding=3 cellspacing=1 border=0>\n".
       "<COLGROUP>" . ($tab_cols > 4 ? "<col span=" . ($tab_cols - 4) . " width=$indent_width>"
                                     : "") .
       "<col width=\"*\"><col span=3 width=\"5%\" align=\"right\"></colgroup>\n" .
       "<TR><TD ALIGN=CENTER COLSPAN=$tab_cols><B>" . _("Folder Sizes") .
       "</B></TD></TR>\n";
  /*
   * Table heading is color 5
   */

  echo "<TR BGCOLOR=\"$color[5]\" ALIGN=\"center\"><TD COLSPAN=" . ($tab_cols - 3) .
       "><B>" . _("Folder") . "</B></TD>" .
       "<TD><B>" . _("Count") . "</B></TD><TD><B>" . _("Unread") .
       "</B></TD><TD><B>" . _("Size") . "</B></TD></TR>\n";

  global $use_special_folder_color, $folder_sizes_subtotals;

  /*
   * Have to make the following global so the folder_sizes_add_totals function
   * can see them.
   */

  global $subfolders, $subcount, $subunread, $subsize;

  $last_depth = 0;
  for ($boxnum = 0; $boxnum < count($boxes); $boxnum++) {
    /*
     * If we are keeping subtotals show last one(s)
     */
    if ($folder_sizes_subtotals && $last_depth > $boxes[$boxnum]['depth']) {
      folder_sizes_add_totals($last_depth,  $boxes[$boxnum]['depth']);
      $last_depth = $boxes[$boxnum]['depth'];
    }

    /*
     * Indent sub-folders
     */

    $indent = $boxes[$boxnum]['depth'] > 0
            ? "<TD COLSPAN=" . $boxes[$boxnum]['depth'] . ">&nbsp;</TD>"
            : "";

    /*
     * How many columns will this folder name cross?
     * For non-selectable is number of cols - depth
     * For other is number of cols - depth - 3
     */

    $use_cols = in_array('noselect', $boxes[$boxnum]['flags'])
      ? $tab_cols - $boxes[$boxnum]['depth']
      : $tab_cols - $boxes[$boxnum]['depth'] - 3;

    /*
     * Make a link to each folder - it's a bit friendlier.
     * Of course - use a special colour for those special folders.
     */
    if ($use_special_folder_color &&
        isSpecialMailbox($boxes[$boxnum]['unformatted'])) {
      $special_color = ";color:$color[11]";
    } else {
      $special_color = '';
    }

    echo "<TR BGCOLOR=\"$color[4]\">" . $indent .
       "<TD" . ($use_cols > 1 ? " COLSPAN=$use_cols" : "") . ">" .
       (in_array('noselect', $boxes[$boxnum]['flags'])
        ? ""
        : "<A HREF=\"$right_main?sort=0&amp;startMessage=1&amp;mailbox=" .
          urlencode($boxes[$boxnum]['unformatted']) . "\" TARGET=\"right\" " .
          "STYLE=\"text-decoration:none".$special_color."\">") .
       htmlspecialchars(imap_utf7_decode_local($boxes[$boxnum]['display'])) .
       (in_array('noselect', $boxes[$boxnum]['flags']) ? "" : "</A>") .
       "</TD>";

    /*
     * If we are moving down a level, reset the counters for when
     * the subtotal is (perhaps) displayed.
     */

    if ($last_depth < $boxes[$boxnum]['depth']) {
      $last_depth = $boxes[$boxnum]['depth'];
      $subfolders[$last_depth] = 0;
      $subcount[$last_depth] = 0;
      $subunread[$last_depth] = 0;
      $subsize[$last_depth] = 0;
    }
    
    /*
     * Show counts (and add to subtotals) for selectable
     */
    if (!in_array('noselect', $boxes[$boxnum]['flags'])) {
      echo "<TD>" . $boxes[$boxnum]['count'] . "</TD>" .
           "<TD>" . $boxes[$boxnum]['unread'] . "</TD>" .
           "<TD>" . show_readable_size($boxes[$boxnum]['size']) . "</TD>";

      if (!isset($subfolders[$last_depth])) $subfolders[$last_depth] = 0;
      if (!isset($subcount[$last_depth])) $subcount[$last_depth] = 0;
      if (!isset($subunread[$last_depth])) $subunread[$last_depth] = 0;
      if (!isset($subsize[$last_depth])) $subsize[$last_depth] = 0;
      $subfolders[$last_depth]++;
      $subcount[$last_depth] += $boxes[$boxnum]['count'];
      $subunread[$last_depth] += $boxes[$boxnum]['unread'];
      $subsize[$last_depth] += $boxes[$boxnum]['size'];
    }
    echo "</TR>\n";
  }

  /*
   * Grand totals
   */

  folder_sizes_add_totals($last_depth,  -1);

  /*
   * We're done - close table, connection and return
   */

  echo "</TABlE></TABLE>\n";

  // revert domain
  bindtextdomain('squirrelmail',SM_PATH . 'locale');
  textdomain('squirrelmail');

  sqimap_logout($imapConnection);
}

if (! function_exists('ngettext')) {
  /**
   * Function is used as replacement in broken installs
   * @ignore
   */
  function ngettext($str,$str2,$number) {
    if ($number>1) {
      return $str2;
    } else {
      return $str;
    }
  }
}
/**
 * Local variables:
 * mode: php
 * c-basic-offset: 2
 * End:
 * vim: syntax=php et ts=2
 */
?>