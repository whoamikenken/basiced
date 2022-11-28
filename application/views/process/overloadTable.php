<?
	$campus = $this->input->post("campus");
	$cutoff = $this->input->post("cutoff");
	$othIncome = $this->input->post("othIncome");
?>

<div class="well blue">
	<div class="well-header">
	</div>
	<div class="well-content"> 
		<a href="#" class="btn btn-primary" id="butt_printresult" style="float:right">Print</a>
		
		<label>Cut-Off</label>
		<select class="chosen col-md-4" id="cutoff"><?=$this->employeemod->displayCutOff()?></select>
		<a href="#" class="btn btn-primary" id="butt_save">Save</a>
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr style="background-color:#343434;color:white">
					<th style="text-align:center">Employee ID</th>
					<th style="text-align:center">Employee Name</th>
					<th style="text-align:center">Monthly <?=$this->payrolloptions->incomedesc($othIncome)?></th>
					<th style="text-align:center">Total Number of Hours Worked</th>
					<th style="text-align:center">Total Number of Hours to be Deduct</th>
					<th style="text-align:center">Total <?=$this->payrolloptions->incomedesc($othIncome)?></th>
				</tr>
			</thead>
			<tbody>
				<?
					$otherList = $this->employee->showOverloadTable($cutoff,$campus,$othIncome);
					if($otherList->num_rows() >0)
					{
						foreach($otherList->result() as $row)
						{
							?>
							<tr>
								<td style="text-align:center"><?=$row->employeeid?></td>
								<td style="text-align:center"><?=$this->employee->getfullname($row->employeeid)?></td>
								<td style="text-align:center"><?=$row->monthly?></td>
								<td style="text-align:center"></td>
								<td style="text-align:center"></td>
								<td style="text-align:center"></td>
							</tr>
						<?
						}
					}
					else
					{
						?>
							<tr>
								<td colspan="6" style="text-align:center">No data exist !</td>
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
</script>