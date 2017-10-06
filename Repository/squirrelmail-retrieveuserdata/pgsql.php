<?php
   /*
    *   pgsql.php
    *
    *   PostgreSQL server access 0.1
    *   SquirrelMail Retrieve User Data Plugin
    *   By Ralf Kraudelt <kraude@wiwi.uni-rostock.de>
    *   PostgreSQL support by Mark Bergsma <mark@nedworks.org>
    *
    *   pgsql.php uses some configuration values from config.php
    */

function retrieve_data ($uid, $passwd) {
    global  $SQRUD_PGSQL_SERVER, $SQRUD_PGSQL_PORT, $SQRUD_PGSQL_USER,
            $SQRUD_PGSQL_PASS, $SQRUD_PGSQL_DB, $SQRUD_PGSQL_TABLE,
            $SQRUD_PGSQL_NAME_FIELD, $SQRUD_PGSQL_MAIL_FIELD,
            $SQRUD_PGSQL_USERNAME_FIELD, $SQRUD_PGSQL_USE_PERSISTENT;

    $pgsql_error = 0;
    $common_name = '';
    $mail_address = '';

    /* Build a pgsql connection string */
    $conn_str = " host=".$SQRUD_PGSQL_SERVER.
                " port=".$SQRUD_PGSQL_PORT.
                " user=".$SQRUD_PGSQL_USER.
                " password=".$SQRUD_PGSQL_PASS.
                " dbname=".$SQRUD_PGSQL_DB;

    /* Open a connection to PostgreSQL */
    if (SQRUD_PGSQL_USE_PERSISTENT)
        $pgsql = pg_pconnect($conn_str) or $pgsql_error = 1;
    else
        $pgsql = pg_connect($conn_str) or $pgsql_error = 1;

    /* Query the database */
    if ($pgsql_error == 0) {
        $uid = addslashes($uid);
        $query =   "SELECT $SQRUD_PGSQL_NAME_FIELD, $SQRUD_PGSQL_MAIL_FIELD
                    FROM $SQRUD_PGSQL_TABLE
                    WHERE $SQRUD_PGSQL_USERNAME_FIELD = '$uid'";
        
	$search_result = pg_query($pgsql, $query) or $pgsql_error = 1;
    }

    /* Parse the results */
    if ($pgsql_error == 0) {
        if ($info = pg_fetch_array($search_result)) {
            $common_name = $info[$SQRUD_PGSQL_NAME_FIELD];
            $mail_address = $info[$SQRUD_PGSQL_MAIL_FIELD];
        }
        else
            $pgsql_error = 1;
    }
    
    /* Close the connection if needed */
    if (! $SQRUD_PGSQL_USE_PERSISTENT)
        pg_close($pgsql);

    /* Return the results */
    return array("error"=>$pgsql_error, "common_name"=>$common_name, "mail_address"=>$mail_address);
}

?>
