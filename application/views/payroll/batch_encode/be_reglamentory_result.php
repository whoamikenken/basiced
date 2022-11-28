<?php 
/**
* @author justin (with e)
* @copyright 2018
*
* > for mcu-hyperion 21478
*/
?>
<style type="text/css">
	.success-emp{
		color : green;
	}
	.error-emp{
		color : red;
	}
</style>

<div class="form_row">
	<label class="success-emp"><strong>Success : <?=count($success_emp)?></strong></label>
</div>
<div class="form_row">
	<label class="error-emp"><strong>Error : <?=count($error_emp)?></strong></label>
</div>
<div class="form_row">
	<strong class="error-emp">List of Error :</strong>
	<?
		if(count($error_emp) == 0){
	?>
		<p class="error-emp"> * No error..</p>
	<?	}else{
			foreach ($error_emp as $key => $message) {
	?>
			<p class="error-emp"> <?=$message?> </p>
			
			<script type="text/javascript">
				$("#tbl_content").find("#row-<?=$empId?>").attr('style','background-color: #AC191994;');				
			</script>
	<?		} # end of foreach
		}
	?>
</div>
<script type="text/javascript">
	$('#wrapListEncode').html("<img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.");
    
    $.ajax({
        url : "<?=site_url('payroll_/loadPayrollBatchEncode')?>",
        type : "POST",
        data : {
            category : $("#category").val(),
            deptid : $("select[name=deptid]").val(),
            employmentstat : $("select[name=employmentstat]").val()
        },
        success : function(msg){
            $('#wrapListEncode').html(msg);
        }
    });
	
</script>