<?php
$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$payroll_cutoff = $this->extensions->getPayrollCutoffConfig($from_date, $to_date);
list($display,$dfrom,$dto) = $this->employeemod->attendance_confirmation(); 
// $late = $undertime = $last_deptid = '';
?>
<!-- <p><?=$dateRange?></p>
<div class="form_row">
    <div id="msgbox">
        <p style="font-size: 14px;"><?=$display?></p>
    </div>
</div> -->
<!-- <span><b><?= $from_date ?>&nbsp;-&nbsp;<?= $to_date ?></b></span> -->
	<table class="table table-striped table-bordered table-hover datatable">
		<?php if($category != "att_adj") {?>
				<thead>
					<tr>
						<th>Employee ID</th>
						<th>Fullname</th>
						<th>Schedule Date</th>
						<th>Hours</th>
						<?php if($category == "overtime"){ ?>
							<th>Amount</th>
							<th>Type</th>
						<?php } ?>
					</tr>	
				</thead>
				<tbody>
				<?php if($records) {  ?>
						<?php foreach($records as $row): ?>
							<?php if($last_deptid != $row["description"]): ?>
								<tr>
									<td colspan="<?=($category == "overtime") ? '6' : '4'?>"><b><?=$row["description"]?></b></td>
								</tr>
							<?php endif ?>
							<?php if(isset($row['late']) && isset($row['undertime'])){
								$late = ($row['late']) ? $row['late'] : "00:00"; 
								$undertime = ($row['undertime']) ? $row['undertime'] : "00:00"; 
								$row['hours'] = $this->attcompute->sec_to_hm($this->attcompute->exp_time($late) + $this->attcompute->exp_time($undertime));
							}else{
								$row['hours'] = $this->attcompute->sec_to_hm($this->attcompute->exp_time($row['hours']));
								}?>
								<tr <?= ($row['hours'] == "0:00") ? "hidden" : "" ?>>
									<td><?=$row['employeeid']?></td>
									<td><?=Globals::_e($row['fullname'])?></td>
									<td><?=$row['sched_date']?></td>
									<td><?=$row['hours']?></td>
									<?php if($category == "overtime"){ ?>
										<td><?=number_format($row['ot_amount'], 2)?></td>
										<td><?=strtoupper($row['ot_type'])?></td>
									<?php } ?>
								</tr>
							<?php $last_deptid = $row["description"]; ?>
						<?php endforeach ?>
				<?php } ?>
				</tbody>
		<?php }else{ ?>
				<thead>
					<tr>
						<th class="align_center">Employee ID</th>
						<th class="align_center">Fullname</th>
						<th class="align_center">Deficiency Date</th>
						<th class="align_center">Total Hours</th>
						<th class="align_center">Amount</th>
						<th class="align_center">Income Adjustment</th>
						<th class="align_center">Payroll Cutoff</th>
						<th class="align_center">Status</th>
						<th class="align_center">Adjusted By:</th>
					</tr>	
				</thead>
				<tbody>
					<?php foreach($att_adj as $key => $value): ?>
							<tr>
								<td colspan="10"><p style="font-weight: bold;"><?= strtoupper($key) ?></p></td>
							</tr>
						<?php foreach($value as $row): ?>
							<tr>
								<td class="align_center"><?= $row['employeeid'] ?></td>
								<td><?= Globals::_e($row['fullname']) ?></td>
								<td class="align_center"><?= $row['date'] ?></td>
								<td class="align_center"><?= isset($row['total_hours']) ? $row['total_hours']." hours/s" : $row['total_days']." day/s" ?></td>
								<td class="align_center"><?= number_format($row['amount'], 2) ?></td>
								<td class="align_center">Adjustment</td>
								<td class="align_center"><?= $payroll_cutoff ?></td>
								<td class="align_center"><?= $row['status'] ?></td>
								<td class="align_center"><?= $row['addedby'] ?></td>
							</tr>
						<?php endforeach ?>
					<?php endforeach ?>
				</tbody>
		<?php } ?>
	</table>
<br>
<a class="btn btn-primary" id="generate_pdf">Generate</a>
<script>
	  $("#generate_pdf").click(function(){
	        $('#loading').removeAttr('hidden');
	        var empid = $('select[name=employeeid]').val();
	        var cutoff = $("#cutoff").val();
	        var office = $("#office").val();
	        var cutoff_arr = cutoff.split(',');
	        var datesetfrom = '',
	        datesetto = '';
	        var category_selected = $("#category_selected").val();
	        if(cutoff_arr != ''){
	            $('#cutoffMsg').html('');
	            datesetfrom = "<?= $from_date ?>";
	            datesetto = "<?= $to_date ?>";
	        }else{
	            $('#cutoffMsg').html('Please select cutoff.');
	            return;
	        }
		    var data = "datesetfrom=" + GibberishAES.enc( datesetfrom, toks) + "&datesetto=" + GibberishAES.enc(datesetto , toks) + "&category_selected=" + GibberishAES.enc( category_selected, toks) + "&fv=" + GibberishAES.enc(empid , toks)  + "&office=" + GibberishAES.enc(office , toks);
	        var encodedData = encodeURIComponent(window.btoa(data));
		    window.open("<?=site_url("reports_/showDetailedAttendanceReport")?>?form_data="+encodedData+"&toks="+toks);
	});
</script>