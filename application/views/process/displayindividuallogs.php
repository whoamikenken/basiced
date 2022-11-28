<?php
/**
 * @author Justin
 * Copyright 2016
 */
$datedisplay = "";
$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$edata = $edata;
$office = $this->employee->getindividualoffice($empid);
$datedisplay = $this->time->createRangeToDisplay($from_date,$to_date);
$isteaching = $this->employee->getempteachingtype($empid);
$teachingtype = $this->employee->getempteachingtype($empid);

$data = array('date_now'=>$from_date,'from_date'=>$from_date,'to_date'=>$to_date,'datedisplay'=>$datedisplay,'empid'=>$empid,'edata'=>$edata,'deptid'=>$office,'teachingtype'=>$teachingtype);
?>
<style>
#indvtbl tr th,#indvtblnt tr th{
    background-color: #393737;
    color: #d2cf85;
}
</style>

<div class="modal fade" id="myModal1" data-backdrop="static"></div>

<?php
	/*if($isteaching){  // Teaching
		// $this->load->view('process/attendance_report_teaching',$data);
		$isBED = $this->extensions->checkIfDeptIsBED($office);

		if($isBED){
	    	$this->load->view('process/attendance_report_teaching',$data);
		}else{
			$this->load->view('process/attendance_report_teaching_college',$data);
		}
	}else{*/
		$this->load->view('process/attendance_report_nonteaching',$data);
	// }
?>



<script>
	$("#applysc").click(function(){  
		$.ajax({
			url      : "<?=site_url("employeemod_/fileconfig")?>",
			type     : "POST",
			data     : {folder: "employeemod", view: "scapply",dateInitial:$(this).attr('dateInitial')},
			success: function(msg){
				$("#myModal1").html(msg);
			}
		});
	});
	
	$("#usesc").click(function(){  
		$.ajax({
			url      : "<?=site_url("employeemod_/fileconfig")?>",
			type     : "POST",
			data     : {folder: "employeemod", view: "scapplyuse"},
			success: function(msg){
				$("#myModal1").html(msg);
			}
		});
	});
</script>