<?php
/**
 * @author Angelica
 * @copyright 2018
 */

if(isset($empinfo)){
   $empdetails = $empinfo[0]['employeeid'];    
}else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0]['employeeid'];
}
?>


<input type="hidden" name="employeeid" value="<?=$empdetails?>">
<div id="wrap_sched_history">
	<div style="margin-top: 40px;">
    	<img src='<?=base_url()?>images/loading.gif' /> Loading employee schedule history ..
	</div>
</div>



<script>
	$(document).ready(function(){
	    var employeeid = $("input[name=employeeid]").val();
	    var id = $("#145_menuid").attr("ld");
	    if(id == "employee/schedule_info_history_main"){
	    $.ajax({
	        url: "<?=site_url("schedule_/getEmployeeScheduleHistory")?>",
	        type : "POST",
	        data : {employeeid:employeeid},
	        success:function(msg){
	            $('#wrap_sched_history').html(msg);
	        }
	    });
		}
		else{
			$('#wrap_sched_history').hide();
		}

	});
</script>