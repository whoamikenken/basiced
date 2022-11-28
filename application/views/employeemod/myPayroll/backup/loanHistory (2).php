<?php
	//Added 6-3-2017
	$CI =& get_instance();
    $CI->load->model('my_payroll');
	$status = $this->input->post("status");
	$loanHistory = $CI->my_payroll->getLoanHistory($this->session->userdata('username'),$status);
	// $cutoffarray = explode(",",$cutoff);
	$cutofflist = $CI->my_payroll->getCutoffList();
	// var_dump($cutofflist[1]->startdate);
	// die;
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
    background-color: #510051;
    color: #ADAD0E;
}

</style>
<h5>Loan History</h5>
<div>
<?
	foreach($loanHistory as $history)
	{
	?>
		<table class="table table-hover table-bordered table-striped">
			<thead>
				<tr>
					<th>Loan Description</th>
					<th>Date of Loan</th>
					<th>Starting Balance</th>
					<th>Cut-off Deduction</th>
					<th>Amount Deducted</th>
					<th>Remaining Balance</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style='text-align:center'><?=$CI->my_payroll->getLoanDescription($history->code_loan)?></td>
					<td style='text-align:center'><?=date("F d, Y",strtotime($history->datefrom))?></td>
					<td style='text-align:center'><?=number_format($history->startingamount,2)?></td>
					<td style='text-align:center'><?=date("F d",strtotime($cutoffarray[0]))?> - <?=date("d Y",strtotime($cutoffarray[1]))?></td>
					<td style='text-align:center'><?=number_format($history->amount,2)?></td>
					<td style='text-align:center'><?=number_format($history->famount,2)?></td>
				</tr>
				<?
				
					$totalAmountDeducted = $remainingBalance = 0;
					
					$totalAmountDeducted += $history->amount;
					
					$remainingBalance = $history->famount;
					
					foreach($cutofflist as $list)
					{
						if($list->startdate != $cutoffarray[0] && $list->enddate != $cutoffarray[1] && $list->startdate > $cutoffarray[0] && $list->enddate > $cutoffarray[1])
						{
							
							$cutoff = $list->startdate.",".$list->enddate;
							$consecutiveHistory = $CI->my_payroll->getConsecutiveLoanHistory($this->session->userdata('username'),$cutoff);
							$newAmount=0;
							if($consecutiveHistory[0]->loan)
							{
							foreach(explode("/",$consecutiveHistory[0]->loan) as $row)
							{
								$r = explode("=",$row);
								if($r[0] == $history->code_loan)
								{
									$newAmount = $r[1];
								}
							}
							$totalAmountDeducted += $newAmount;
							$remainingBalance -= $newAmount;
							?>
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td style='text-align:center'><?=date("F d",strtotime($consecutiveHistory[0]->cutoffstart))?> - <?=date("F d",strtotime($consecutiveHistory[0]->cutoffend))?></td>
									<td style='text-align:center'><?=number_format($newAmount,2)?></td>
									<td style='text-align:center'><?=number_format($remainingBalance,2)?></td>
								</tr>
							<?
							}
							else
							{?>
								<tr><td colspan='3' style='text-align:center'>NO RECORD</td></tr>
							<?}
						}
						// if($remainingBalance <= 0) break; BREAK AFTER THE REMAINING BALANCE TURN TO 0
					}
				?>
			</tbody>
			<tfoot>
				<tr style='color:red'>
					<td colspan="2" class="align_center" style='font-weight:bold;text-align:center'>TOTAL</td>
					<td style='text-align:center'><?=number_format($history->startingamount,2)?></td>
					<td></td>
					<td style='text-align:center'><?=number_format($totalAmountDeducted,2)?></td>
					<td style='text-align:center'><?=number_format($remainingBalance,2)?></td>
				</tr>
			</tfoot>
		</table>
		<br>
	<?}
?>
</div>

<script>

</script>