<div class="row">

<?php
	if ($this->session->userdata("personalinfo")) {

	$datum = $this->session->userdata("personalinfo");
	$id = $datum[0]["employeeid"];
	$query = "SELECT DISTINCT A.modified_on AS recdate, CONCAT(B.firstname, ' ' ,B.lastname) AS who_modified, tab FROM employee_info_history AS A 
				INNER JOIN user_info AS B  ON A.modified_by = B.id AND employeeid = '{$id}' ORDER BY recdate DESC";
	$res = $this->db->query($query)->result_array();

	}// end if
?>
<table class="table table-bordered table-striped table-bordered table-hover">
	<tr>
		<th>Date and time</th>
        <th>Tab</th>
		<th>User</th>
		<!-- <th>Task</th> -->
	</tr>
<?php
	if (count($res)>0) {
		foreach ($res as $key => $item) {
			$dte = explode(" ", $item["recdate"]);

	?>
		<tr>
			<td><?php print(date("M-d-Y (l)",strtotime($dte[0]))." ".date("h:i A",strtotime($dte[1]))); ?></td>
            <td><?=$item["tab"]?></td>
			<td><?php print($item["who_modified"]); ?></td>
		</tr>
	<?php
		} // end foreach
	}else{
?>
	<tr class="error">
		<td colspan="2">No records found</td>
	</tr>
<?php
	}
?>
</table>

</div>