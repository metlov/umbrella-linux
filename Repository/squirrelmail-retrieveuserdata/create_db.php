#!/usr/bin/env php
<?php

  /*
   *  create_db.php
   *
   *  Create DBM-like database for dba.php 0.1
   *  SquirrelMail Retrieve User Data Plugin
   *  By Ralf Kraudelt <kraude@wiwi.uni-rostock.de>
   *
   *  This program creates a DBM-like text database from a plain text for usage
   *  with the dba.php access method of the plugin. The file format is
   *  uid, name, mail
   *  with uid as SM logon name (see users_example.txt with contributors).
   *  The programm uses all configuration options
   *  of dba.php from config.php to create the appropriate database. If you
   *  change the handler or the seperator, please run this program again.
   *
   *  Requirements: PHP interpreter, typically /usr/bin/php under UNIX
   *  Usage: php create_db.php filename
   */
   
  include('config.php');
  
  // Taken from PHP documentation
  // reads a line from stdin
  function read () {
    # 4092 max on win32 fopen

    $fp=fopen("php://stdin", "r");
    $in=fgets($fp,4094);
    fclose($fp);

    # strip newline
    (PHP_OS == "WINNT")?($read=str_replace("\r\n", "",$in)):($read = str_replace("\n", "", $in));

    return $read;
  }
  
  /*
   *  Let's start here
   */

  // stop if called by server
  if (!empty($PHP_SELF)) {
    die("Call me from command line\n");
  }

  print("Read from config.php: database file is $SQRUD_DBA_NAME\n");
  print("                      database type/handler is $SQRUD_DBA_HANDLER\n");
  print("                      name/mail seperator is $SQRUD_DBA_SEP\n\n");
  
  if (!isset($argv[1])) {
    print("Please enter the source filename: ");
    $txtfile = read();
  } else {
    $txtfile = $argv[1];
  }
  
  if (!file_exists($txtfile) || !is_readable($txtfile)) {
    die("Cannot read source file $txtfile\n");
  }
  

  if (file_exists($SQRUD_DBA_NAME)) {
    rename($SQRUD_DBA_NAME, $SQRUD_DBA_NAME.'.old');
  }

  print("Creating $SQRUD_DBA_NAME from $txtfile using handler $SQRUD_DBA_HANDLER\n");
  
  // open database for writing
  $dba = dba_open($SQRUD_DBA_NAME, "n", $SQRUD_DBA_HANDLER);
  if (!$dba) {
    die("Couldn't open $SQRUD_DBA_NAME for write access\n");
  }
  
  // open textfile for reading
  $file = fopen($txtfile, 'rb');
  if (!$file) {
    dba_close($dba);
    die("Couldn't open $txtfile for read access\n");
  }
 
  // loop over textfile
  $lineno = 1;
  $line = fgets($file,4096);
  while ($line) {
    list($uid, $data) = explode($SQRUD_DBA_SEP, $line,2);
    if (!dba_insert($uid, $data, $dba) ){
      print("Couldn't insert $lineno: $line into database\n");
    }
    $lineno+=1;
    $line = fgets($file,4096);
  };
 
  fclose($file);

  dba_sync($dba);
  dba_optimize($dba);
  dba_sync($dba);
  dba_close($dba);
  
?>
