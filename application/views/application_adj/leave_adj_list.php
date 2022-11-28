<?php
/**
 * @author Angelica Arangco
 * @copyright 2018
 */
$row_count = 0 ;
?>

<style>
	#adj_table{
		width: 100%;
	}
	th, td {
	    padding: 10px;
	}
	.table-template tr th {
    background-color: #0072c6 !important;
    color: #000000 !important;
    text-align: center;
}
</style>

<div>
	<table class="table-hover table-bordered table-striped datatable table-template" id="adj_table">
		<thead>
			<tr>
				<th rowspan="2" width="10px"></th>
				<th rowspan="2">EMPLOYEE ID</th>
				<th rowspan="2">EMPLOYEE NAME</th>
				<th rowspan="2">LEAVE DATE/S</th>
				<th rowspan="2" width="20px">NO. OF DAYS</th>
				<th rowspan="2">AMOUNT</th>
				<th rowspan="2">PAYROLL CUTOFF</th>
				<th rowspan="2">STATUS</th>
				<th rowspan="2">DATE APPROVED</th>
				<th rowspan="2" width="60px">INCLUDE?<br><input type="checkbox" id="include_all" class="double-sized-cb"></th>
			</tr>
			<tr>
				<? foreach ($arr_income_config as $code_income => $det) { ?>
					<th><?=$det['description']?></th>
				<? } ?>
			</tr>
		</thead>
		<tbody>
			<? foreach ($adj_list as $empid => $row) { 
					foreach ($row as $status => $row2) {
						
						foreach ($row2 as $adj_id => $detail) {
							$row_count++;
							$date_str = '';

							$date_arr = explode('|',$detail['date']);
							foreach ($date_arr as $day) {
								if($date_str) $date_str .= '/';
								$date_str .= date('M d, Y',strtotime($day));
							}
			?>
							<tr>
								<td><b><?=$row_count.'.'?></b>&nbsp;</td>
								<td><?=$empid?></td>
								<td><?=$detail['fullname']?></td>
								<td><?=$date_str?></td>
								<td><?=$detail['total_days']?></td>
								<td class="align_right"><?=formatAmount($detail['amount'])?></td>
								<td><?=$detail['startdate']?date('M d, Y', strtotime($detail['startdate'])) . ' - ' . date('M d, Y', strtotime($detail['enddate'])):''?></td>
								<td><?=$status?></td>
								<td><?=date("M d, Y H:i A", strtotime($detail['timestamp']))?></td>
								<td class="align_center">
									<?if($status != 'PROCESSED'){?>
										<input type="checkbox" name="include" class="double-sized-cb" 
											employeeid="<?=$empid?>" 
											request_id="<?=$detail['request_id']?>" 
											date="<?=$detail['date']?>" 
											total_days="<?=$detail['total_days']?>" 
											income_adj_str="<?=$detail['income_adj_str']?>" 
											amount="<?=$detail['amount']?>">
									<?}?>
								</td>
							</tr>
			<? 

						}
					}
				} 


			?>	

		</tbody>
	</table>
</div>



<script>
	$(".chosen").chosen();

	///< select employees to include
	$('#include_all').on('click',function(){
	  if($(this).is(':checked')) 	$('input[name=include]').prop('checked',true); 
	  else  						$('input[name=include]').prop('checked',false);
	});

	$('input[name=include]').on('click',function(){
	  if(!$(this).is(':checked'))     $('#include_all').prop('checked',false);
	});


	$('#save_process_adj').unbind('click').on('click',function(){

		if($('#payroll_cutoff').val() == ''){
			$('#p_cutoff_msg').html('Please select payroll cutoff.');
			return;
		}else $('#p_cutoff_msg').html('');

		$('#saving').hide();
		$('#loading').html("<img src='<?=base_url()?>images/loading.gif'/>Saving..Please wait.").show();


		var form_data = {
		  dtr_cutoff_id       :   "<?=$dtr_cutoff_id?>",
		  payroll_cutoff_id   :   $('#payroll_cutoff').val()
		};

		var emplist = [];

		$('input[name=include]:checked').each(function(){
			var det = {};
		    det['employeeid'] = $(this).attr('employeeid');
		    det['request_id'] = $(this).attr('request_id');
		    det['date'] = $(this).attr('date');
		    det['total_days'] = $(this).attr('total_days');
		    det['income_adj_str'] = $(this).attr('income_adj_str');
		    det['amount'] = $(this).attr('amount');
		    emplist.push(det);
		});

		form_data['emplist'] = emplist;

		$.ajax({
		 url     :   "<?=site_url("application_adj_/saveLeaveAdj")?>",
		 type    :   "POST",
		 dataType : 'json',
		 data    :   form_data,
		 success :   function(msg){

		                 var data_failed = msg.data_failed;
		                 var failed = '';
		                 for (var key in data_failed) {
		                     failed += data_failed[key] + ", ";
		                 }
		                 if(failed) failed = failed.substring(0, failed.length-2);
		                 else failed = 'NONE';

		                 if(msg.err_code == 0){
		                   
		                   if(failed == 'NONE') alert(msg.msg+'\n'+'Success count: '+msg.success_count+'\n'+'Data insert failed: '+failed);
		                   else{
		                     alert(msg.msg+'\n'+'Success count: '+msg.success_count+'\n' + 'Data insert failed: '+failed);
		                   }                  
		                 }else{
		                   alert(msg.msg+'\n'+'Success count: '+msg.success_count+'\n'+'Data insert failed: '+failed);
		                 }

		                 $('#saving').show();
		                 $('#loading').html("").hide();

		                 $('#dtr_cutoff').change();
		             }
		});
	});
</script>
<?
function formatAmount($amount=''){
    if($amount){
        $amount = number_format( $amount, 2 );
    }else{
        $amount = '0.00';
    }
    return $amount;
}

?>