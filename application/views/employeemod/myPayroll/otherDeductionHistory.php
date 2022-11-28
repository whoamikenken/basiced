<?php
	//Added 6-5-2017
	$CI =& get_instance();
	$CI->load->model('my_payroll');
	$cutoff = $this->input->post("cutoff");
	$otherDeductionHistory = $CI->my_payroll->getConsecutiveOtherHistory($this->session->userdata('username'),$cutoff);
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
.datatable tr th{
    padding: 1px 12px 1px 12px;
}
table tr td,#leaveh tr th{
    text-align: center;
}
table tr th{
    background-color: #2E5266;
    color: #FFFFFF;
}

</style>
<h3><b>&nbsp;&nbsp;Other Deduction History</b></h3>
<div style="padding: 20px;">
	<table class="table table-hover table-bordered table-striped">
		<thead>
			<tr>
				<th>Income Description</th>
				<th>Cut off Date</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			<?
			if($otherDeductionHistory->num_rows() > 0)
			{
				foreach($otherDeductionHistory->result() as $history){
					if($history->income)
					{
					foreach(explode("/",$history->otherdeduc) as $row)
					{
						$r = explode("=",$row);
					?>
					<tr>
						<td style='text-align:center'><?=$CI->my_payroll->getOtherDeductionDescription($r[0])?></td>
						<td style='text-align:center'><?=date("F d",strtotime($history->cutoffstart))?> - <?=date("d Y",strtotime($history->cutoffend))?></td>
						<td style='text-align:center'><?=number_format($r[1],2)?></td>
					</tr>
					<?}
					}
					else
					{?>
						<tr><td colspan='3' style='text-align:center'>NO RECORD</td></tr>
					<?}
				}
			}
			else
			{?>
				<tr><td colspan='3' style='text-align:center'>NO RECORD</td></tr>
			<?}?>
		</tbody>
	</table>
</div>

<script>

</script>