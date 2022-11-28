<?php
	//Added 6-3-2017
	$CI =& get_instance();
    $CI->load->model('my_payroll');
	$cutoff = $this->input->post("cutoff");
	$loanList = $CI->my_payroll->getLoan($this->session->userdata('username'),$cutoff);
	
	$cutoffarray = explode(",",$cutoff);
	$cutofflist = $CI->my_payroll->getCutoffList();
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
<h5>Loan History</h5>
<div>
<?
	foreach($loanList as $key => $value)
	{
		$loanHistory = $CI->my_payroll->getLoanHistory($this->session->userdata('username'),$value);
		// echo'<pre>';var_dump($loanHistory);
		// die;
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
				<!--<tr>
					<td style='text-align:center'><?=$CI->my_payroll->getLoanDescription($value)?></td>
					<td style='text-align:center'><?=date("F d, Y",strtotime($history->datefrom))?></td>
					<td style='text-align:center'><?=number_format($history->startingamount,2)?></td>
					<td style='text-align:center;<?=in_array($history->startdate,$cutoffarray)?"font-weight:bold":""?>'><?=date("F d",strtotime($history->startdate))?> - <?=date("d Y",strtotime($history->enddate))?></td>
					<td style='text-align:center'><?=number_format($history->amount,2)?></td>
					<td style='text-align:center'><?=number_format($history->famount,2)?></td>
				</tr>-->
				<?
				
					$totalAmountDeducted = $remainingBalance = 0;
					
					// $totalAmountDeducted += $history->amount;
					
					$remainingBalance = $history->startBalance;
					$codeLoan = "";
					foreach($cutofflist as $list)
					{

						// if($list->startdate != $history->startdate && $list->enddate != $history->enddate && $list->startdate > $history->startdate && $list->enddate > $history->enddate)
						// {
							
							$cutoff = $list->startdate.",".$list->enddate;
							$consecutiveHistory = $CI->my_payroll->getConsecutiveLoanHistory($this->session->userdata('username'),$cutoff,$value);

							$newAmount=0;
							if($consecutiveHistory)
							{
								$totalAmountDeducted += $consecutiveHistory[0]->amount;
								$remainingBalance = $consecutiveHistory[0]->remainingBalance;
								?>
									<tr>
								<?
									if($list->startdate != $history->cutoffstart && $list->enddate != $history->cutoffend && $list->startdate > $history->cutoffstart && $list->enddate > $history->cutoffend)
									{	
								?>
									<?php if ($codeLoan != $consecutiveHistory[0]->code_loan): ?>
										<td style='text-align:center'><?= $CI->my_payroll->getLoanDescription($consecutiveHistory[0]->code_loan);?></td>
										<? $codeLoan = $consecutiveHistory[0]->code_loan?>
										<td style='text-align:center'></td>
										<td style='text-align:center'><?=number_format($history->startBalance,2)?></td>
									<?php else: ?>
										<td style='text-align:center'></td>
										<td style='text-align:center'></td>
										<td style='text-align:center'></td>
									<?php endif ?>
										

								<?	}else{?>
										<td style='text-align:center'><?=$CI->my_payroll->getLoanDescription($value)?></td>
										<td style='text-align:center'><?=date("F d, Y",strtotime($history->cutoffstart))?></td>
										<td style='text-align:center'><?=number_format($history->startBalance,2)?></td>
								<?	}?>
										<td style='text-align:center;<?=in_array($consecutiveHistory[0]->cutoffstart,$cutoffarray)?"font-weight:bold":""?>'><?=date("F d",strtotime($consecutiveHistory[0]->cutoffstart))?> - <?=date("F d",strtotime($consecutiveHistory[0]->cutoffend))?></td>
										<td style='text-align:center'><?=number_format($consecutiveHistory[0]->amount,2)?></td>
										<td style='text-align:center'><?=number_format($consecutiveHistory[0]->remainingBalance,2)?></td>
									</tr>
								<?
								
							}
							else
							{?>
								<!--<tr><td colspan='6' style='text-align:center'>NO RECORD</td></tr>-->
							<?}
						// }
						if($consecutiveHistory)
						{
							if(in_array($consecutiveHistory[0]->cutoffstart,$cutoffarray)) break; //
						}
						// if($remainingBalance <= 0) break; //BREAK AFTER THE REMAINING BALANCE TURN TO 0
					}
				?>
			</tbody>
			<tfoot>
				<tr style='color:red'>
					<td colspan="2" class="align_center" style='font-weight:bold;text-align:center'>TOTAL</td>
					<td style='text-align:center'><?=number_format($history->startBalance,2)?></td>
					<td style='text-align:center'><?=$history->cutoffstart?></td>
					<td style='text-align:center'><?=number_format($history->amount,2)?></td>
					<td style='text-align:center'><?=number_format($history->currentBalance,2)?></td>
				</tr>
			</tfoot>
		</table>
		<br>
	<?}
	}
?>
</div>

<script>

</script>