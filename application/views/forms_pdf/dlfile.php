<?php

/**
 * @author Justin
 * @copyright 2015
 */
include "application/config/connection.php";
  
$fn = $_GET['filename'];

$gotten = mysql_query("SELECT * FROM elfinder_file WHERE title='$fn';");
if ($row = mysql_fetch_assoc($gotten)) {
        $ContentType = $row['mime'];
		$data = $row['content'];
        $FileName = $row['name'];		
        $FileSize = $row['FileSize'];		
} 
        header("Content-type: $ContentType");
        header("Content-Disposition: attachment; filename=\"$FileName\"");
        print $data;

?>