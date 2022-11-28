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
            <h5><b><?=$title?></b></h5>
        </div>
        <div class="modal-body">

        	<!-- <div class="form_row">
        	    <label class="field_name align_right">Select Type: </label>
        	    <div class="field no-search">
        	        <select class="form-control" id="ob_type" name="ob_type">
        	        	<option value="DIRECT">OFFICIAL BUSINESS</option>
        	        	<option value="CORRECTION">CORRECTION FOR TIME IN/OUT</option>
        	        </select>
        	    </div>
        	</div> -->

        	<div class="form_row">
        	    <label class="field_name align_right">DTR Cutoff: </label>
        	    <div class="field no-search">
        	        <select class="form-control" id="dtr_cutoff" name="dtr_cutoff">
        	        	<option value="">- Select Cutoff -</option>
        	        	<? foreach ($dtr_cutoff_arr as $dtr_cutoff_id => $dtr_cutoff_str) { 
        	        			$arr = explode('|', $dtr_cutoff_str);
        	        			$dtr_cutoff_disp = date('M d, Y',strtotime($arr[0])) . ' - ' . date('M d, Y',strtotime($arr[1]));
        	        	?>
        	        			<option value="<?=$dtr_cutoff_id?>"> <?=$dtr_cutoff_disp?> </option>
        	        	<? } ?>
        	        </select>
        	    </div>
        	</div>

        	<div id="wrap_adj_list">
				Please select Cutoff applicable for adjustment.
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
	var ob_type = "<?=$ob_type?>";
	//$('#dtr_cutoff,#ob_type').on('change',function(){
	$('#dtr_cutoff').on('change',function(){
		
		if($('#dtr_cutoff').val() != ''){
    		$('#wrap_adj_list').html("<img src='<?=base_url()?>images/loading.gif' />Loading..please wait.");
			$.ajax({
			   url      :   "<?=site_url("application_adj_/loadOBAdjList")?>",
			   type     :   "POST",
			  // data     :   {dtr_cutoff_id:$('#dtr_cutoff').val(),ob_type:$('#ob_type').val()},
			  	data     :   {dtr_cutoff_id:$('#dtr_cutoff').val(),ob_type:ob_type},
			   success  :   function(ret){
			    	$('#wrap_adj_list').html(ret);
			   }
			});
		}
	});

	$(".chosen").chosen();



</script>