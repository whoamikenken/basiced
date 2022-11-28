<?php
	//Added (6-13-2017)
	$toks = $this->input->post("toks");
	$employeeid = $toks ? $this->gibberish->decrypt( $this->input->post("employeeid"), $toks ) : $this->input->post("employeeid");
	$type = $toks ? $this->gibberish->decrypt( $this->input->post("type"), $toks ) : $this->input->post("type");
	$month =  $toks ? $this->gibberish->decrypt( $this->input->post("month"), $toks ) : $this->input->post("month");
	
	if($type == "Tardiness")
	{
		$query_res = $this->disciplinary_action->viewExcessiveTardinessDetails($employeeid, $month);
	}
	else if($type == "Absenteism")
	{
		$query_res = $this->disciplinary_action->viewExcessiveAbsenteismDetails($employeeid, $month);
	}
	// var_dump($query_res);
	// die;
?>

<style>
.modal {
	width:70%;
	left: 0;
    right: 0;
    margin: auto;
}
.modal-body{
	margin: 10px;
}
.dataTables_paginate {
    margin-top: 6px;
}

#offbus tr td,#offbus tr th{
    text-align: center;
}
#offbus tr th{
    background-color: #0072c6;
    color: #000;
}
</style>

<table class="table table-hover table-bordered datatable dataTable" id="offbus">
	<?if($type == "Tardiness"){?>
    <thead>
        <tr>
            <th>#</th>
            <th>Employee Name</th>
            <th>Date of tardy</th>
            <th>No.of Minute Late</th>
        </tr>
    </thead>
    <tbody>
    	<?
			$i = 0;
        	foreach ($query_res as $row) {
					$i++;
				?>
        		<tr>
					<td><?=$i?></td>
					<td><?=$row['fullname']?></td>
					<td><?=$row['dateTardy']?></td>
					<td><?=$row['lateut']?></td>
				</tr>
            <?}
		?>
    </tbody>
	<?}else if($type == "Absenteism"){?>
	<thead>
        <tr>
            <th>#</th>
            <th>Employee Name</th>
            <th>Date Absent</th>
        </tr>
    </thead>
    <tbody>
    	<?
			$i = 0;
        	foreach ($query_res as $row) {
					$i++;
				?>
        		<tr>
					<td><?=$i?></td>
					<td><?=$row['fullname']?></td>
					<td><?=$row['dateAbsent']?></td>
				</tr>
            <?}
		?>
    </tbody>
	<?}?>
</table>

<script type="text/javascript">


$("#offbus").dataTable({
});
</script>
