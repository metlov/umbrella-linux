<?php
   /*
    *  mysql.php
    *
    *  MySQL Server Access 0.2
    *  SquirrelMail Retrieve User Data Plugin
    *  By Ralf Kraudelt <kraude@wiwi.uni-rostock.de>
    *  MySQL support by Thijs Kinkhorst <thijs@jaze.nl>
    *
    *  The function retrieve_data() retrives the user's data from a
    *  MySQL database. The result of this
    *  function is an array("error", "common_name", "mail_address"). The error
    *  value has no specifc use - an unsuccessful retrieval leads to an error.
    *  Common_name contains the value, which is used as $full_name in the
    *  user's preferences file, mail_address contains $email_address.
    *
    *  Mysql.php uses some configuration values from config.php, have a look
    *  at it.
    *  
    */    

function retrieve_data ($uid, $passwd) {
  global $SQRUD_MYSQL_SERVER, $SQRUD_MYSQL_USER, $SQRUD_MYSQL_PASS,
         $SQRUD_MYSQL_DB, $SQRUD_MYSQL_TABLE, $SQRUD_MYSQL_NAME_FIELD,
         $SQRUD_MYSQL_MAIL_FIELD, $SQRUD_MYSQL_USERNAME_FIELD,
         $SQRUD_MYSQL_USE_PERSISTENT;

    $mysql_error = 0;
    $common_name = '';
    $mail_address = '';

    if ($SQRUD_MYSQL_USE_PERSISTENT)
    {
        $mysql = mysql_pconnect($SQRUD_MYSQL_SERVER, $SQRUD_MYSQL_USER, $SQRUD_MYSQL_PASS) or
            $mysql_error = 1;
    }
    else
    {
        $mysql = mysql_connect($SQRUD_MYSQL_SERVER, $SQRUD_MYSQL_USER, $SQRUD_MYSQL_PASS) or
            $mysql_error = 1;
    }

    if ($mysql_error == 0)
    {
        $uid = addslashes($uid);
        $query = "SELECT $SQRUD_MYSQL_NAME_FIELD, $SQRUD_MYSQL_MAIL_FIELD
                  FROM $SQRUD_MYSQL_TABLE
                  WHERE $SQRUD_MYSQL_USERNAME_FIELD = '$uid'";

        $search_result = mysql_db_query($SQRUD_MYSQL_DB, $query, $mysql) or
            $mysql_error = 1;
    }

    if ($mysql_error == 0) {
        if ($info = mysql_fetch_array($search_result))
        {
            $common_name = $info[$SQRUD_MYSQL_NAME_FIELD];
            $mail_address = $info[$SQRUD_MYSQL_MAIL_FIELD];
        }
        else
        {
            $mysql_error = 1;
        }
    }

    if (! $mysql_use_persistent)
    { 
         mysql_close($mysql);
    }

    return array("error"=>$mysql_error, "common_name"=>$common_name, "mail_address"=>$mail_address);

}

?>
