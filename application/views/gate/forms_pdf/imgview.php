<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
include "application/config/connection.php";

$iquery = mysql_query("SELECT * FROM elfinder_file WHERE title='$eid'");
if ($row = mysql_fetch_assoc($iquery)) {
        $ctype = $row['mime'];                    // image/jpeg
        $content = $row['content'];               // (Binary/Image)
} 
        header("Content-type: $ctype");
        print $content;
        
#echo "test";
?>