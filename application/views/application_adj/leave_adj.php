<?php
/**
 * @author Angelica Arangco
 * @copyright 2018
 */
?>
<style>
	#myModal{
		width: 100%;
		left: 0;
	    right: 0;
	    margin: auto;
	}
	#wrap_adj_list{
		text-align: center;
	}
</style>
<div class="modal-dialog" style="width: 1000px;">
    <div class="modal-content">
		<div class="modal-header">
				<div class="media">
					<div class="media-left">
						<img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
					</div>
					<div class="media-body" style="font-weight: bold;padding-top: 10px;">
						<h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
					</div>
				</div>
				<center><b><h3 tag="title" class="modal-title">Leave Adjustment</h3></b></center>
			</div>
        <div class="modal-body">
        	<div class="form_row">
        	    <label class="field_name align_right">DTR Cutoff: </label>
        	    <div class="field no-search">
        	        <select class="form-control" id="dtr_cutoff" name="dtr_cutoff">
        	        	<option value="">- Select Cutoff -</option>
        	        	<? foreach ($dtr_cutoff_arr as $dtr_cutoff_id => $dtr_cutoff_str) { 
        	        			$arr = explode('|', $dtr_cutoff_str);
        	        			$dtr_cutoff_disp = date('M d, Y',strtotime($arr[0])) . ' - ' . date('M d, Y',strtotime($arr[1]));
        	        			// var_dump(explode('|', $dtr_cutoff_str)); die;
        	        	?>
        	        			<option value="<?=$dtr_cutoff_id?>"> <?=$dtr_cutoff_disp?> </option>
        	        	<? } ?>
        	        </select>
        	    </div>
        	</div>
        	<div id="wrap_adj_list">
				<label>Please select Cutoff applicable for adjustment.</label>
			</div>
			<br>
			<div class="form_row">
			    <label class="field_name align_right">Payroll Cutoff: </label>
			    <div class="field">
			        <select class="form-control" id="payroll_cutoff" name="payroll_cutoff">
        	        	<option value="">- Select Payroll Cutoff -</option>
        	        	<? foreach ($payroll_cutoff_arr as $payroll_cutoff_id => $payroll_cutoff_str) { 
        	        			$arr = explode('|', $payroll_cutoff_str);
        	        			$payroll_cutoff_disp = date('M d, Y',strtotime($arr[0])) . ' - ' . date('M d, Y',strtotime($arr[1]));
        	        	?>
        	        			<option value="<?=$payroll_cutoff_id?>" cutoff="<?=$payroll_cutoff_str?>"> <?=$payroll_cutoff_disp?> </option>
        	        	<? } ?>
        	        </select>
        	        <span id="p_cutoff_msg" class="error-msg"></span>
			    </div>
			</div>
		</div>
		<div class="modal-footer">
		    <div id="loading" hidden=""></div>
		    <div id="saving">
		        <button type="button" id="close" class="btn btn-danger"  data-dismiss="modal">Close</button>
		        <button type="button" id="save_process_adj" class="btn btn-primary">Process</button>
		    </div>
		</div>
	</div>
</div>
<script>
	$('#dtr_cutoff').on('change',function(){
		if($(this).val() != ''){
    		$('#wrap_adj_list').html("<img src='<?=base_url()?>images/loading.gif' />Loading..please wait.");
			$.ajax({
			   url      :   "<?=site_url("application_adj_/getLeaveAdjList")?>",
			   type     :   "POST",
			   data     :   {dtr_cutoff_id:$(this).val()},
			   success  :   function(ret){
			   	// console.log(dtr_cutoff_id);

			    	$('#wrap_adj_list').html(ret);
			   }
			});
		}
	});
	$(".chosen").chosen();
</script>