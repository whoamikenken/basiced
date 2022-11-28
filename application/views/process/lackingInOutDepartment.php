<?
	//Added 6-3-2017
	$type = $this->input->post("type");
	$eid = $this->input->post("eid");
	$head = $this->input->post("head");
	$lackingInOut = $this->attendance->lackingInOutNotifPerEmployee($type,'',$head);
	$user = $this->session->userdata("username");
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
.datatable tr th{
    padding: 1px 12px 1px 12px;
}
#tbl tr th,#indvtblnt tr th{
    background-color: #3b5998;
    color: white;
}
</style>
<div id="lackingInOut" class="well-content" style='border: transparent !important;'>
	<h2>Lacking of In/Out</h2>
	<div class="form_row">
		<label class=>Type :</label>
		<select id="type">
			<option value="ABSENT" <?=($type=="ABSENT")?"selected":""?>>Absent</option>
			<option value="FAILURE TO LOG" <?=($type=="FAILURE TO LOG")?"selected":""?>>Failure to Log</option>
		</select>
	</div>
	<table class="table table-striped table-bordered table-hover datatable dataTable" id='tbl'>
		<?if($type == "ABSENT"){?>
		<thead>
			<tr>
				<th>Employee ID</th>
				<th>Employee Name</th>
				<th>Date</th>
				<th>Time In</th>
				<th>Time Out</th>
				<!--<th>Mark as Read</th>-->
			</tr>
		</thead>
		<tbody>
		<?
			foreach($lackingInOut as $row)
			{
				?>
				<tr>
					<td class='align_center'><?=$row['employeeid']?></td>
					<td class='align_center'><?=$row['fullname']?></td>
					<td class='align_center'><?=$row['date']?></td>
					<td class='align_center'>--:--</td>
					<td class='align_center'>--:--</td>
					<!--<td class='align_center'><input type='checkbox' class='markRead' style='transform:scale(2)'/></td>-->
				</tr>
				<?
			}
		}else{
		?>
		<thead>
			<tr>
				<th>Employee ID</th>
				<th>Employee Name</th>
				<th>Date</th>
				<th>Time In</th>
				<th>Time Out</th>
				<!--<th>Mark as Read</th>-->
			</tr>
		</thead>
		<tbody>
		<?
			foreach($lackingInOut as $row)
			{
				?>
				<tr>
					<td class='align_center'><?=$row['employeeid']?></td>
					<td class='align_center'><?=$row['fullname']?></td>
					<td class='align_center'><?=$row['date']?></td>
					<td class='align_center'><?=($row['timein'] && $row['timein'] != "0000-00-00 00:00:00" )?date("g:i A",strtotime($row['timein'])):"-- : --"?></td>
					<td class='align_center'><?=($row['timeout'] && $row['timeout'] != "0000-00-00 00:00:00" )?date("g:i A",strtotime($row['timeout'])):"-- : --"?></td>
				</tr>
				<?
			}
		}
		?>
		</tbody>
	</table>
</div>
<script>
	$("#type").change(function(){
		var type = $(this).val();
		var eid = "";
		var head = "<?=$head?>";
		loadTbl(type,eid,head);
	});
	
	function loadTbl(type,eid,head){
		$("#displaylogs").html("Loading, please wait...");
		 $.ajax({
			url: "<?=site_url("process_/showLackInOutForDepartment")?>",
			type: "POST",
			data: {type : type,eid:eid,head:head},
			success: function(msg) {
				$("#displaylogs").html(msg);
				$("#cutofflist").html(msg);
			}
		});   
		return false;  
	}
	
	$(document).ready(function() {
		var ulist = $("#tbl").dataTable({
			"sPaginationType": "full_numbers",
			"oLanguage": {
							 "sEmptyTable":     "No Data Available.."
						 },
			"aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
		});
	});

</script>