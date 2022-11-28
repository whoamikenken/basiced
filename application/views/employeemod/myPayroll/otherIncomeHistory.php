<?php
	//Added 6-5-2017
	$CI =& get_instance();
	$CI->load->model('my_payroll');
	$cutoff = $this->input->post("cutoff");
	$otherIncomeHistory = $CI->my_payroll->getConsecutiveOtherHistory($this->session->userdata('username'),$cutoff);
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
<h4><b>&nbsp;&nbsp;Other Income History</b></h4>
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
			if($otherIncomeHistory->num_rows() > 0)
			{
				foreach($otherIncomeHistory->result() as $history){
					if($history->income)
					{
					foreach(explode("/",$history->income) as $row)
					{
						$r = explode("=",$row);
					?>
					<tr>
						<td style='text-align:center'><?=$CI->my_payroll->getOtherIncomeDescription($r[0])?></td>
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