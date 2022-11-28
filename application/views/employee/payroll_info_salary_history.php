<?php
/**
 * @author Angelica Arangco
 * @copyright 2018
 */
?>
<style type="text/css">
    th{
        text-align: center;
    }

    .scrollbar{
   overflow: auto;
   margin-bottom: 10px;
}

  .scrollbar::-webkit-scrollbar {
    width: 10px;
    height: 10px;
  }

  /* Track */
  .scrollbar::-webkit-scrollbar-track {
    box-shadow: inset 0 0 0 grey; 
    border-radius: 10px;
  }
   
  /* Handle */
  .scrollbar::-webkit-scrollbar-thumb {
    background: #0072c6;
    border-radius: 10px;
  }

  /* Handle on hover */
  .scrollbar::-webkit-scrollbar-thumb:hover {
    background: #fadd14; 
  }
</style>

    <div class="panel">
        <div class="panel-heading"><h4><b>Salary History</b></h4></div>
        <div class="panel-body">
            <div class="scrollbar">
        <table class="table table-bordered table-hover table-striped datatable" id="ph">
            <thead style="background-color: #0072c6">
                <tr>
                    <th rowspan="2">Monthly Rate</th>
                    <th rowspan="2">Rank</th>
                    <th rowspan="2">Monthly</th>
                    <th rowspan="2">Semi-Monthly</th>
                    <th rowspan="2">Daily</th>
                    <th rowspan="2">Hourly</th>
                    <th rowspan="2">Per Minute</th>
                    <!-- <th colspan="3">LEC/LAB Rate (Hourly)</th> -->
                    <th rowspan="2">Date Effective</th>
                    <th rowspan="2">Last Updated</th>
                    <th rowspan="2"></th>
                </tr>
               <!--  <tr>
                	<th>Lec</th>
                	<th>Lab</th>
                	<th>Aims Department</th>
                </tr> -->
            </thead>
            <tbody>

            	<? foreach ($salary_list as $base_id => $det) { 
            			$rowcount = sizeof($det['perdept_arr']) > 0 ? sizeof($det['perdept_arr']) : 1;
            	?>
            			<tr>
            				<td rowspan="<?=$rowcount?>" class="align_center" width="10px">
            					<input type="checkbox" name="" class="double-sized-cb" <?=$det['fixedday'] ? 'checked':''?> >
            				</td>
                            <td rowspan="<?=$rowcount?>" class="align_right"><?=$this->extensions->getRankTypeDescription($det['type'])?></td>
            				<td rowspan="<?=$rowcount?>" class="align_right"><?=(is_int($det['monthly']) || is_double($det['monthly']) || is_numeric($det['monthly'])  ? number_format($det['monthly'], 2) : $det['monthly'])?></td>
            				<td rowspan="<?=$rowcount?>" class="align_right"><?= (is_int($det['semimonthly']) || is_double($det['semimonthly']) || is_numeric($det['semimonthly']) ? number_format($det['semimonthly'], 2) : $det['semimonthly'])?></td>
            				<td rowspan="<?=$rowcount?>" class="align_right"><?= (is_int($det['daily']) || is_double($det['daily']) || is_numeric($det['daily']) ? number_format($det['daily'], 2) : $det['daily'])?></td>
            				<td rowspan="<?=$rowcount?>" class="align_right"><?= (is_int($det['hourly']) || is_double($det['hourly']) || is_numeric($det['hourly']) ? number_format($det['hourly'], 2) : $det['hourly'])?></td>
            				<td rowspan="<?=$rowcount?>" class="align_right"><?= (is_int($det['minutely']) || is_double($det['minutely']) || is_numeric($det['minutely']) ? number_format($det['minutely'], 2) : $det['minutely'])?></td>
            				
            				<!-- <? if(sizeof($det['perdept_arr'])){ 
            						foreach ($det['perdept_arr'] as $aimsdept => $leclab) { ?>
            								<td class="align_right"><?=isset($leclab['lechour'])?$leclab['lechour']:''?></td>
            								<td class="align_right"><?=isset($leclab['labhour'])?$leclab['labhour']:''?></td>
            								<td class="align_center"><?=isset($aimsdept_arr[$aimsdept])?$aimsdept_arr[$aimsdept]:''?></td>
    						<? 
    										break;
    								}
            				?>

            				<? }else{ ?>
            						<td></td>
            						<td></td>
            						<td></td>
            				<? } ?>  -->

            				<td rowspan="<?=$rowcount?>" class="align_center"><?=date('M d,Y',strtotime($det['date_effective']))?></td>
            				<td rowspan="<?=$rowcount?>" class="align_center"><?=date('M d,Y h:i A',strtotime($det['timestamp']))?></td>
            				<td rowspan="<?=$rowcount?>" class="span1 align_center">
                                <a class="btn btn-danger"
                                tag="delete_salary" base_id="<?=$base_id?>" 
                                employeeid="<?=$employeeid?>" 
                                date_effective="<?=$det['date_effective']?>"
                                date_effective_disp="<?=date('F d,Y',strtotime($det['date_effective']))?>"><i class="glyphicon glyphicon-trash"></i></a>
            				</td>

            			</tr>

            				<? if($rowcount > 1){ 
            						$temp_pd_count = 0;
            						foreach ($det['perdept_arr'] as $aimsdept => $leclab) {
            							$temp_pd_count++;
            							if($temp_pd_count > 1){
            				?>
            								<tr>
                								<td class="align_right"><?=isset($leclab['lechour'])?$leclab['lechour']:''?></td>
                								<td class="align_right"><?=isset($leclab['labhour'])?$leclab['labhour']:''?></td>
                								<td class="align_center"><?=isset($aimsdept_arr[$aimsdept])?$aimsdept_arr[$aimsdept]:''?></td>
                							</tr>
            				<?		
            							}
            						}
            				?>

            				<? } ?>
            	<? } //end loop salary list ?>

            </tbody>
        </table>
    </div>
        <br />
    </div>
</div> 

<div class="modal fade" id="delete_salary_confirm" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">

        <div class="modal-content" >
            <div class="modal-header" >
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
                        <h4 class="media-heading" ><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family:Avenir; margin-top: -1%;">D`Great</p>
                    </div>
                </div>
                <center><b><h3 tag="title" class="modal-title" style="font-family: 'Poppins';">Delete Salary History</h3></b></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div tag='display' style="margin-left: 5%; text-align: center;">
                        <h4>Are you sure you want to delete salary? <br><br>Date effective : <b> <span id="date_effective_confirm"></span></b></h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <span id="delMsg" class="error-msg"></span>
                <a href="#" class="btn btn-danger" id='deleteSalaryConfirmBtn'>Yes</a>
                <a href="#" id="closeModal" data-dismiss="modal" aria-hidden="true" class="btn btn-success">No</a>
            </div>
        </div>

    </div>
</div>
<script>

	$('a[tag=delete_salary]').on('click',function(){
		$('#deleteSalaryConfirmBtn').attr('base_id',$(this).attr('base_id'));
		$('#deleteSalaryConfirmBtn').attr('employeeid',$(this).attr('employeeid'));
		$('#deleteSalaryConfirmBtn').attr('date_effective',$(this).attr('date_effective'));
		$('#date_effective_confirm').html($(this).attr('date_effective_disp'));
		$('#delete_salary_confirm').modal('toggle');
	});


	$('#deleteSalaryConfirmBtn').on('click',function(){
		$('#delMsg').html("<img src='<?=base_url()?>/images/loading.gif'/>Deleting...");
		$.ajax({
			url 	: "<?=site_url('payroll_/deleteSalaryHistory')?>",
			type 	: "POST",
			data 	: {
						base_id : $(this).attr('base_id'),
						employeeid : $(this).attr('employeeid'),
						date_effective : $(this).attr('date_effective')
			},
			success : function(ret){
                    Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Successfully deleted data',
                          showConfirmButton: true,
                          timer: 1000
                      })
                    $("#closeModal").click();
					loadhistory();
					$('#delMsg').html("");
				
			}
		});
	});

</script>