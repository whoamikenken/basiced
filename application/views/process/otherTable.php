

<div class="well blue">
	<div class="well-header">
	</div>
	<div class="well-content"> 
		<a href="#" class="btn btn-primary" id="butt_printresult" style="float:right">Print</a>
		
		<h4>Payroll Cut-off: <b><?=date('F d, Y',strtotime($p_cutoff_from)). ' - ' . date('F d, Y',strtotime($p_cutoff_to))?></b></h4>
		<!-- <label>Cut-Off</label> -->
		<!-- <select class="chosen col-md-4 cutoff" id="cutoffs"><?=$this->employeemod->displayCutOffconfig()?></select> -->
		<!-- <a href="#" class="btn btn-primary" id="butt_save">Save</a> -->
		<p class='appendata'></p>
		<br>
		<table class="table table-striped table-bordered table-hover" id='data'>
			<thead style="background-color: #0072c6;">
				<tr >
					<th></th>
					<th style="text-align:center">Employee ID</th>
					<th style="text-align:center">Employee Name</th>
					<th style="text-align:center">Monthly <?=$this->payrolloptions->incomedesc($codeIncome)?></th>
					<th style="text-align:center">Total Number of Hours to be Deduct</th>
					<th style="text-align:center">Total <?=$this->payrolloptions->incomedesc($codeIncome)?></th>
				</tr>
			</thead>
			<tbody>
				<?
				$count = 0;
					// $otherList = $this->employee->showOtherTable($cutoff,$campus,$othIncome);
					#echo "<pre>"; var_dump($empList); die;
					if(sizeof($empList) > 0)
					{
						foreach($empList as $key => $row)
						{
							$count++;
							?>
							<tr class="marker">
								<td><?=$count?></td>
								<td class='id' style="text-align:center"><?=$key?></td>
								<td style="text-align:center"><?=$this->employee->getfullname($key)?></td>
								<td class='income' style="text-align:center"><?=$row['monthly_pay']?></td>
								<td class='hours' style="text-align:center"><?=$row['deduc_hours']?></td>
								<td class='t_pay' style="text-align:center"><?=$row['total_pay']?></td>
								<!-- <td style="text-align:center"></td> -->
							</tr>
						<?
						}
					}
					else
					{
						?>
							<tr>
								<td colspan="5" style="text-align:center">No data exist !</td>
							</tr>
						<?
					}
				?>
			</tbody>
		</table>
	</div>
</div>
<script>
	$(".chosen").chosen();
$("#butt_save").unbind("click").click(function()
	{
		$(".append").hide();
		if ($("#cutoffs").val() == "") {
			alert("Cut-Off date is required!");
		}
		else
		{
			$(".appendata").show();
				var ids = [];
				var income = [];
				if ($("#data").find('.marker')) {

					$(".id").each(function()
					{
						ids.push($(this).text());
					})
					$(".t_pay").each(function()
					{
						income.push($(this).text());
					})

				}
				if (ids != "" || ids == null ) {
				var form_data = {
					id:ids,
					income:income,
					incomedata:$("#othincome_drop").val(),
					cutoff:$("#cutoffs").val()
				}
				// console.log(form_data);return;
				$(".appendata").append("<span style='color:red' class='append'>Data processing.... Please Wait</span>");
				$.ajax({
							type: "POST",
							url	: "<?=site_url("process_/saveOtherIncomeData")?>",
							data: form_data,
							success: function(msg){
								alert(msg);
								$(".appendata").hide();
								// console.log(msg);
							}
					   });	
				}
					
	     }

	});
$(document).ready(function()
{
$(".appendata").hide();
});

// for ica-hyperion 21294
// by justin (with e)
$("#butt_printresult").unbind("click").click(function(){
	if ($("#cutoffs").val() == "") {
		alert("Cut-Off date is required!");
	}
	else
	{
		var ids = [];
		var income = [];
		var hours = [];
		var t_pay = [];
		if ($("#data").find('.marker')) {

			$(".id").each(function()
			{
				ids.push($(this).text());
			})
			$(".income").each(function()
			{
				income.push($(this).text());
			})
			$(".hours").each(function()
			{
				hours.push($(this).text());
			})
			$(".t_pay").each(function()
			{
				t_pay.push($(this).text());
			})

		}
		if (ids != "" || ids == null ) {
			var form_data = {
				id:ids,
				income:income,
				incomedata:$("#othincome_drop").val(),
				cutoff:$("#cutoffs").val()
			}
			
			var params = "?id="+ ids +"&income="+ income +"&incomedata="+ $("#othincome_drop").val() +"&cutoff="+ $("#cutoffs").val() +"&hours="+ hours +"&t_pay="+ t_pay;
			window.open("<?=site_url("process_/printOtherIncome")?>"+ params,"other_income");	
		}
	}
});
// end for ica-hyperion 21294
</script>

