<?php

$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$payroll_cutoff = $this->extensions->getPayrollCutoffConfig($from_date, $to_date);
list($display,$dfrom,$dto) = $this->employeemod->attendance_confirmation(); 
$late = $undertime = '';
?>
	<span><b><?= $from_date ?>&nbsp;-&nbsp;<?= $to_date ?></b></span>
		<table class="table table-striped table-bordered table-hover datatable">
			<thead>
				<tr>
					<th style="background-color: #0072c6">Date</th>
					<th style="background-color: #0072c6" <?=($logs == "IN/OUT" || $logs == "IN" ? '' : 'hidden')?>>IN</th>
					<th style="background-color: #0072c6" <?=($logs == "IN/OUT" || $logs == "OUT" ? '' : 'hidden')?>>OUT</th>
				</tr>	
			</thead>
			<tbody>
				<?php foreach($trail_records as $empid => $empdata): ?>
				<tr>
					<td colspan="3"><b><?= $this->extensions->getEmployeeName($empid) ?></b></td>
				</tr>
				<?php foreach($empdata as $date => $records): ?>
				<tr>
					<td><?= date("F d, Y", strtotime($date)) ?></td>
					<td <?=($logs == "IN/OUT" || $logs == "IN" ? '' : 'hidden')?>><?= $records["in"] ?></td>
					<td <?=($logs == "IN/OUT" || $logs == "OUT" ? '' : 'hidden')?>><?= $records["out"] ?></td>
				</tr>
				<?php endforeach ?>
				<?php endforeach ?>
			</tbody>
		</table>
		<br>
<a class="btn btn-primary" id="generate_pdf">Generates</a>
<script>
	  $("#generate_pdf").click(function(){
	        $('#loading').removeAttr('hidden');
	        var empid = $('select[name=employeeid]').val();
	        var cutoff = $("#cutoff").val();
	        var terminal = $("#terminal").val();
	        var logs = $("#logs").val();
	        var gate = $("#terminal option:selected").attr('gate');
	        var cutoff_arr = cutoff.split(',');
	        var datesetfrom = '',
	        datesetto = '';
	        var category_selected = $("#category_selected").val();
	        if(cutoff_arr != ''){
	            $('#cutoffMsg').html('');
	            datesetfrom = cutoff_arr[0];
	            datesetto = cutoff_arr[1];
	        }else{
	            $('#cutoffMsg').html('Please select cutoff.');
	            return;
	        }
	        if($("#specific").prop("checked")){
	        	datesetfrom = $("input[name='datefrom']").val();
	          	datesetto = $("input[name='dateto']").val();
	      	}
	      	var data = "datesetfrom=" + GibberishAES.enc( datesetfrom, toks) + "&datesetto=" + GibberishAES.enc(datesetto , toks) + "&category_selected=" + GibberishAES.enc( category_selected, toks) + "&fv=" + GibberishAES.enc(empid , toks)+ "&terminal=" + GibberishAES.enc(terminal , toks)+ "&logs=" + GibberishAES.enc(logs , toks)+ "&gate=" + GibberishAES.enc(gate , toks);
	        var encodedData = encodeURIComponent(window.btoa(data));
		    window.open("<?=site_url("reports_/showDetailedAttendanceReport")?>?form_data="+encodedData+"&toks="+toks);
	});
</script>

