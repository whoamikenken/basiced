<?php
	//Added (6-9-2017)

	$code = '';
	$CI =& get_instance();
	$CI->load->model('disciplinary_action');
	$toks = $this->input->post("toks");
	$type = $toks ? $this->gibberish->decrypt( $this->input->post("type"), $toks ) : $this->input->post("type");
	$department =  $toks ? $this->gibberish->decrypt( $this->input->post("department"), $toks ) :$this->input->post("department");
	$month =  $toks ? $this->gibberish->decrypt( $this->input->post("month"), $toks ) : $this->input->post("month");
	$usertype =  $toks ? $this->gibberish->decrypt( $this->input->post("usertype"), $toks ) : $this->session->userdata("usertype");

	if(!$month) $month = $CI->disciplinary_action->latestDetailedAttendance();

	if($type == "Tardiness")
	{
		$excessiveQuery = $CI->disciplinary_action->empWithExcessiveTardiness(false,$month,false,$year, $department);
		$code = 'ET';
		$freqinfo = $CI->disciplinary_action->getOffensesInfo("ET");
	}
	else if($type == "Absenteism")
	{
		$excessiveQuery = $CI->disciplinary_action->empWithExcessiveAbsenteism(false,$month,false,$year, $department);
		$code = 'EA';
		$freqinfo = $CI->disciplinary_action->getOffensesInfo("EA"); 
	}
	$freq = "";
	if($freqinfo) $freq = $freqinfo[0]->frequency;
	$sanctions = array();
	$sanctionToPunish = $this->extensions->getDisciplinarySanctions($code);
	$sanctionToPunish = explode("/", $sanctionToPunish);
	foreach($sanctionToPunish as $key => $value){
		$data = explode("=", $value);
		if(isset($data[0]) && isset($data[1])) $sanctions[$data[0]] = $data[1];
	}
	
	$sanctions_type = '';

?>

<style>
.dataTables_paginate {
    margin-top: 6px;
}
#excessiveTable tr td,#excessiveTable tr th{
    text-align: center;
}
#excessiveTable tr th{
    background-color: #3b5998 ;
    color: #ffffff;
}
input[name=mar]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
}

#monthly_sort_chosen{
	width: 165px !important;
}

#positionTable_wrapper{
	margin-top: 20px;
}
</style>

<div class="widgets_area">
	<a href="#" class="btn btn-success" name='backlist'>Back to employee list</a>
	<a href="#" class="btn btn-info" name='reload' style='float:right'><i class="icon-refresh"></i> Reload</a>
	<div class="row" style="margin-top: 10px;">
		<div class="col-md-12">
			<div class="panel">
                   <div class="panel-heading"><h4><b>List of Employees With Excessive <?= ($type == "Tardiness" ? "Tardiness" : "Absenteeism") ?></b></h4></div>
                   <div class="panel-body">
					<a class="btn batchaddbtnsanction" type="<?= $code ?>" href="#modal-view" data-toggle="modal" style='margin-bottom:1%;float:right;background-color:red;font-weight:bold;color:white; <?= ($usertype == "EMPLOYEE") ? 'display: none' : '' ?>'>Tag Disciplinary Action</a>
					<select class="chosen" id="monthly_sort">
						<?php  foreach($this->extensions->monthSelection() as $key => $value): 			
							$notif = ($type=="Tardiness") ?  $CI->disciplinary_action->empWithExcessiveTardiness(true, $key, true, $year, $department) : $CI->disciplinary_action->empWithExcessiveAbsenteism(true, $key, true, $year, $department) ;
							?>
							<option value="<?= $key ?>" <?= ($month == $key) ? "selected" : "" ?> ><?= ($notif == 0) ? $value : $value." (".$notif.")" ; ?></option>
						<?php endforeach ?>
					</select>
					<table class="table table-striped table-bordered table-hover" id="positionTable" style="margin-top: 20px;">
						<thead>
							<tr style="background-color: #0072c6;">
								<th  <?= ($usertype == "EMPLOYEE") ? 'hidden' : '' ?>></th>
								<th>Employee ID</th>
								<th>Name</th>
								<th>Department</th>
								<th><?= ($type == "Tardiness" ? "Tardiness" : "Absenteeism") ?> Frequency</th>
								<th>Last Disciplinary Sanction</th>
								<th>Sanction</th>
								<th>View Detailed</th>
							</tr>
						</thead>
						<tbody>
							<?
								$i = 0;
								foreach($excessiveQuery as $row)
								{
									foreach ($row as $key => $value) $row[$key] = Globals::_e($value);
									if($row['freq'] >= $freq){
										$sanctionCode =  $CI->disciplinary_action->getEmpSanction($row['employeeid'], $code, $month, $year);
										if(isset($sanctionCode[0]['sanction_code'])) $sanctionCodes = $sanctionCode[0]['sanction_code'];
										else $sanctionCodes = '';
										foreach ($sanctions as $key => $value) {
											if($value <= $row['freq']) $sanctions_type = $key;
										}
										?>
											<tr>
												<?php if($sanctionCodes == ''){ ?>
													<td class="align_center" <?= ($usertype == "EMPLOYEE") ? 'hidden' : '' ?>><input type="checkbox" name='check' class="check" empid="<?=$row["employeeid"]?>" sanctions="<?= $sanctions_type ?>" style="transform:scale(2);"></td>
												<?php }else{ ?>
													<td <?= ($usertype == "EMPLOYEE") ? 'hidden' : '' ?>></td>
												<?php } ?> 
												<td class="align_center"><?=$row['employeeid']?></td>
												<td class="align_center"><?=$row['fullname']?></td>
												<td class="align_center"><?=$row['department']?></td>
												<td class="align_center"><?=($row['freq'] > 1) ? $row['freq']." days" : $row['freq']." day"?></td>
												 <?php if ($type == "Tardiness"){?><td class="align_center"><?=$this->attcompute->sec_to_hm($row['count'])." Hours"?></td> <?php } ?>
												 <?php if ($type == "Absenteism"){?><td class="align_center"><?=$row['count']." Hours"?></td> <?php } ?>
												<td class="align_center"><?= $sanctionCodes; ?></td>
												<td class="align_center"><a href="#modal-view" data-toggle="modal" empid="<?=$row["employeeid"]?>" type="<?=$type?>" class="btn viewDetails"><span class="icon-search" aria-hidden="true"></span></a></td>
											</tr>
										<?
									
									}
									$sanctions_type = '';
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div id="excessive_history"></div>       
</div>

<script type="text/javascript">
var toks = hex_sha512(" ");
$("#positionTable").DataTable();
$("#positionTable").on('click', '.viewDetails', function(){
	$("#modal-view").find("div[tag='display']").html('');
	var employeeid = $(this).attr("empid");
	var type = $(this).attr("type");
	var month = $("#monthly_sort").val();
	// console.log(employeeid + " " + type);
	
	$("#modal-view").find("h3[tag='title']").text("Excessive <?= ($type == "Tardiness" ? "Tardiness" : "Absenteeism") ?>");
	$("#button_save_modal").hide();
	// $("#excessive_history").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
	$.ajax({
		url:"<?=site_url('disciplinary_action_/viewExcessiveDetails')?>",
		type:"POST",
		data:{employeeid: GibberishAES.enc( employeeid, toks),type: GibberishAES.enc(type , toks),month: GibberishAES.enc(month , toks), toks:toks},
		success:function(result){
			$("#modal-view").find("div[tag='display']").html(result);
		}
	});
})
;

$(".check").change(function(){
	var employeeid = $(this).attr("empid");
	var type = $(this).attr("type");
	var old_sanctions ='';
	$('input[type=checkbox]:checked').each(function() {
        var sanctions_type = $(this).attr("sanctions");
	    if(old_sanctions){  
	        if(sanctions_type != old_sanctions){
	        	$(this).prop( "checked", false );
	        	alert("Unable to tag disciplinary action with different sanctions type. Try again..");
	        }
	    }
        old_sanctions = sanctions_type;
    });
});

$(".batchaddbtnsanction").unbind("click").bind("click",function(){

	var old_sanctions = '';
	var iscontinue = true;
	var sancType = $(this).attr("type");
	if($("input.check:checkbox:checked").length > 0)
	{
		var emplist = "";
		var offense = "<?=$type?>";
		var month = "<?=$month?>";
		var year = "<?=$year?>";
		$("input.check:checkbox:checked").each(function(){
			emplist +=  $(this).attr("empid") + "/";
		});

		var emplist = emplist.split('/');
		
		if(iscontinue){
			$("#modal-view").find("h3[tag='title']").text("Batch Employee Offense");
			$("#button_save_modal").show();
			$("#button_save_modal").text("Save");
			$.ajax({
				url: "<?=site_url('disciplinary_action_/viewBatchAdd')?>",
				type: "POST",
				data: {
					offense :  GibberishAES.enc(offense, toks),
					emplist :  GibberishAES.enc( emplist, toks),
					sancType: GibberishAES.enc(sancType , toks),
					year: GibberishAES.enc( year, toks),
					month: GibberishAES.enc(month , toks),
					toks:toks
				},
				success: function(msg){
				$("#modal-view").find("div[tag='display']").html(msg);
				}
			});  
		}
	}		
	else
	{
		msg = "No record selected"
		Swal.fire({
          	icon: 'warning',
          	title: 'Warning!',
          	text: msg,
          	showConfirmButton: true,
          	timer: 2000
      	})
		return false;
	}
});

$("#excessiveTable").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});

$("a[name='backlist']").click(function(){
   // var obj = $(".inner_navigation .main li[class='active'] a"); 
   // var site = $(obj).attr("site");
   // var root = $(obj).attr("root");
   // var menuid = $(obj).attr("menuid");
   // var titlebar = $(obj).text();
  
   // $("#mainform").attr("action","<?=site_url("main/site")?>");
   // $("input[name='sitename']").val(site);
   // $("input[name='rootid']").val(root);
   // $("input[name='menuid']").val(menuid);
   // $("input[name='titlebar']").val(titlebar);
   
   // if(site) $("#mainform").submit();
 // $("a[site='disciplinary_action/disciplinary_action_emplist']").click();
 location.reload();
});

$("a[name='reload']").click(function(){
	var month = $("#monthly_sort").val();
	$("#content").html("<img src='<?=base_url()?>images/loading.gif' />  Refreshing, Please Wait..");
    var form_data = {
        type : GibberishAES.enc("<?=$type?>"  , toks),
        view: GibberishAES.enc("disciplinary_action/emp_list_with_excessive_disciplinary_action.php"  , toks),
        month:  GibberishAES.enc(month , toks),
        year:  GibberishAES.enc("<?= $year ?>" , toks),
        department:  GibberishAES.enc("<?=$department?>"  , toks),
        toks:toks
    }; 
    $.ajax({
        url : "<?=site_url("main/siteportion")?>",
        type: "POST",
        data: form_data,
        success: function(msg){
			$("#content").html(msg);
        }
    });
});


$("#monthly_sort").change(function(){
	var month = $(this).val();
	var form_data = {
        type : GibberishAES.enc("<?=$type?>"  , toks),
        view:  GibberishAES.enc("disciplinary_action/emp_list_with_excessive_disciplinary_action.php" , toks),
        month:  GibberishAES.enc(month , toks),
        year: GibberishAES.enc("<?=$year?>"  , toks),
        department:  GibberishAES.enc("<?=$department?>"  , toks),
        toks:toks

    }; 
    $.ajax({
        url : "<?=site_url("main/siteportion")?>",
        type: "POST",
        data: form_data,
        success: function(msg){
			$("#content").html(msg);
        }
    });
});

$(".chosen").chosen();
</script>
