<?php

/**
 * @author Justin
 * @copyright 2014
 */
                                                                                                                                                                                                                                                                    
include "application/config/database.php";

$server = $db['default']['hostname'];$username = $db['default']['username'];$password = $db['default']['password'];$db = $db['default']['database'];

	$con = mysql_connect($server,$username,$password);
    $db = mysql_select_db($db);
    if(!$db)
    echo "Failed to Connect to Database..";
?>
