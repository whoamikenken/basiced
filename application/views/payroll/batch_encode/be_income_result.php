<?php 
/**
* @author justin (with e)
* @copyright 2018
* 
* >  for mcu-hyperion 21479
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
	<label class="success-emp"><strong>Success : <?=count($success)?></strong></label>
</div>
<div class="form_row">
	<label class="error-emp"><strong>Error : <?=count($error)?></strong></label>
</div>
<div class="form_row">
	<strong class="error-emp">List of Error :</strong>
	<?
		if(count($error) == 0){
	?>
		<p class="error-emp"> * No error..</p>
	<?	}else{
			foreach ($error as $empId => $message) {
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
	$("#div_save").show();
	$("#div_loading").hide();

	$("#tbl_content").each(function(){
		$(this).removeAttr('style');
	});

	
</script>