<?php

/**
 * @author Justin
 * @copyright 2015
 */


$bal = "";
$query = $this->db->query("SELECT SUM((DATEDIFF(todate,fromdate)+1)) AS total FROM leave_request b WHERE `status`='APPROVED' AND employeeid='$id';");
foreach($query->result() as $row){
   $bal =  $row->total;
}

?>
<user>
    <bal><?=$bal?></bal>
</user>