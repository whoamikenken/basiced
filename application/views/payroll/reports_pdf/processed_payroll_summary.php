<?php 

/**
 * @author Angelica
 * @copyright 2017
 *
 */

$CI =& get_instance();
$CI->load->library('PdfCreator_mpdf');

$total_basic_pay = $total_net_pay = $total_deminimiss = $total_other = $total_income = $total_deduc = $total_contribution = $total_loan = $total_whtax = 0;
// echo '<pre>';
// print_r($emplist);


foreach ($income_config as $key => $val) {
    if($val['hasData'] == 0) unset($income_config[$key]);
}
foreach ($incomeoth_config as $key => $val) {
    if($val['hasData'] == 0) unset($incomeoth_config[$key]);
}
foreach ($fixeddeduc_config as $key => $val) {
    if($val['hasData'] == 0) unset($fixeddeduc_config[$key]);
}
foreach ($deduction_config as $key => $val) {
    if($val['hasData'] == 0) unset($deduction_config[$key]);
}
foreach ($loan_config as $key => $val) {
    if($val['hasData'] == 0) unset($loan_config[$key]);
}

foreach ($emplist as $key => $row) {
	$total_basic_pay 	+= $row['salary'];
	$total_income 		+= $row['salary'];
	$total_whtax 		+= $row['whtax'];
	$total_deduc 		+= $row['whtax'];


	foreach ($row['income'] as $code => $amt) {
		$income_config[$code]['total'] += $amt;
		$total_income += $amt;

		if(array_key_exists($code, $deminimiss_config)){
			if(!in_array($code, $deminimiss)){
				$total_deminimiss += $amt;
			}
		}
		if(array_key_exists($code, $others_config)){
			if(!in_array($code, $other)){
				$total_other += $amt;
			}
		}
	}
	foreach ($row['fixeddeduc'] as $code => $amt) {
		$fixeddeduc_config[$code]['total'] += $amt;
		$total_contribution += $amt;
	}
	foreach ($row['deduction'] as $code => $amt) {
		$deduc_config[$code]['total'] += $amt;
		$total_deduc += $amt;
	}
	foreach ($row['loan'] as $code => $amt) {
		$loan_config[$code]['total'] += $amt;
		$total_deduc += $amt;
	}

	$total_deduc += $total_contribution;

}
	$total_net_pay += $total_income;
	$total_net_pay -= $total_deduc;

// print_r($income_config);
// print_r($deminimiss);

// echo '</pre>';

// function mPDF($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P') {

// $mpdf = new mPDF('utf-8','LETTER','10','','3','3','6','10','9','9');
$mpdf = new mPDF('utf-8','LETTER','10','','15','15','15','15','9','9');

$styles = '
			<style>
				table{
					width: 100%;
					font-family:calibri;
					border-collapse: collapse;
				}
				.header, #maincontent th{
					color: blue;
				}
				#maincontent table, #maincontent td {
				    border: 1px solid black;
				}
				#maincontent tbody tr:nth-child(even) {
				  background-color: white;
				}
				#maincontent tbody tr:nth-child(odd) {
				  background-color: #BDBDBD;
				}
				.amount{
					text-align: right;
				}
			</style>



';

$header = '
			
			<table class="header">
				<tr>
					<td class="align_left" rowspan="2" style="padding:0 20px;" valign="bottom"><img src="images/school_logo.jpg" style="width: 50px;"/></td>
					<td><b>Pinnacle Technologies Inc.</b></td>
				</tr>
				<tr>
					<td>Payroll Cut-Off '.date("F d, Y",strtotime($sdate))." - ".date("F d, Y",strtotime($edate)).'</td>
				</tr>
			</table><br><br>
';

$content = '
			<table id="maincontent" class="table table-striped" cellspacing="0">
					<thead>
						<tr>
							<th colspan="2">PAYROLL SUMMARY</th>
						</tr>
						<tr>
							<th>CATEGORY</th>
							<th>AMOUNT</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>BASIC PAY</td>
							<td class="amount">'.formatAmount($total_basic_pay).'</td>
						</tr>

						<tr>
							<td>Deminimiss</td>
							<td class="amount">'.formatAmount($total_deminimiss).'</td>
						</tr>';
						
							foreach ($deminimiss as $code) {
$content .= '					<tr>
									<td>&nbsp;&nbsp;'.$income_config[$code]["description"].'</td>
									<td class="amount">&nbsp;&nbsp;'.formatAmount($income_config[$code]["total"]).'</td>
								</tr>';
							}
						

$content .= '			<tr>
							<td>OTHERS</td>
							<td class="amount">'.formatAmount($total_other).'</td>
						</tr>';
						
							foreach ($other as $code) {
$content .= '					<tr>
									<td>&nbsp;&nbsp;'.$income_config[$code]["description"].'</td>
									<td class="amount">&nbsp;&nbsp;'.formatAmount($income_config[$code]["total"]).'</td>
								</tr>';
							}
						

$content .= '			<tr>
							<td style="color:red;">TOTAL INCOME</td>
							<td class="amount"><b>'.formatAmount($total_income).'</b></td>
						</tr>

						<tr><td colspan="2" style="border:none;background-color: white;">&nbsp;</td></tr>

						<tr>
							<td>NET PAY</td>
							<td class="amount">'.formatAmount($total_net_pay).'</td>
						</tr>

						<tr>
							<td>W/ TAX</td>
							<td class="amount">'.formatAmount($total_whtax).'</td>
						</tr>';

						
							foreach ($fixeddeduc_config as $code => $row) {
$content .= '				<tr>
									<td>'.$row["description"].'</td>
									<td class="amount">'.formatAmount($row["total"]).'</td>
								</tr>';
							}
						

						
							foreach ($deduction_config as $code => $row) {
$content .= '					<tr>
									<td>'.$row["description"].'</td>
									<td class="amount">'.formatAmount($row["total"]).'</td>
								</tr>';
							}
						

						
							foreach ($loan_config as $code => $row) {
$content .= '					<tr>
									<td>'.$row["description"].'</td>
									<td class="amount">'.formatAmount($row["total"]).'</td>
								</tr>';
							}
						

$content .= '			<tr>
							<td style="color:red;">TOTAL DEDUCTION</td>
							<td class="amount"><b>'.formatAmount($total_deduc).'</b></td>
						</tr>

						<tr><td colspan="2" style="border:none;background-color: white;">&nbsp;</td></tr>';

						
							foreach ($fixeddeduc_config as $code => $row) {
$content .= '					<tr>
									<td>'.$row["description"].'</td>
									<td class="amount">'.formatAmount($row["total"]).'</td>
								</tr>';
							}
						

$content .= '			<tr>
							<td style="color:red;">TOTAL CONTRIBUTION</td>
							<td class="amount"><b>'.formatAmount($total_contribution).'</b></td>
						</tr>

					</tbody>
					<tfoot>
						<tr>
							<td style="text-align: center;padding-top: 10px;border:none;"><b>NUMBER OF EMPLOYEES:</b></td>
							<td style="text-align: center;padding-top: 10px;border:none;"><b>'.sizeof($emplist).'</b></td>
						</tr>
						<tr>
							<td style="border:none;">Prepared by:</td>
							<td style="border:none;">Reviewed by:</td>
						</tr>
					</tfoot>
				</table>


';



$main = "
			$styles
			<div class='container'>
			$header
			$content

			</div>

";



function formatAmount($amount=''){
    if($amount){
        $amount = number_format( $amount, 2 );
    }else{
        $amount = '0.00';
    }
    return $amount;
}


$mpdf->WriteHTML($main);

$mpdf->Output();

die;
?>

			<style>
				table{
					width: 100%;
					font-family:calibri;
					border-collapse: collapse;
				}
				.header, #maincontent th{
					color: blue;
				}
				#maincontent table, #maincontent td {
				    border: 1px solid black;
				}
				#maincontent tbody tr:nth-child(even) {
				  background-color: white;
				}
				#maincontent tbody tr:nth-child(odd) {
				  background-color: #BDBDBD;
				}
				.amount{
					text-align: right;
				}
			</style>

			<div class="container">
				<table class="header">
					<tr>
						<td class="align_left" rowspan="2" style="padding:0 20px;" valign="bottom"></td>
						<td><b>Pinnacle Technologies Inc.</b></td>
					</tr>
					<tr>
						<td>Payroll Cut-Off <?=date("F d, Y",strtotime($sdate))." - ".date("F d, Y",strtotime($edate))?></td>
					</tr>
				</table><br><br>

				<table id="maincontent" class="table table-striped" cellspacing="0">
					<thead>
						<tr>
							<th colspan="2">PAYROLL SUMMARY</th>
						</tr>
						<tr>
							<th>CATEGORY</th>
							<th>AMOUNT</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>BASIC PAY</td>
							<td class="amount"><?=formatAmount($total_basic_pay)?></td>
						</tr>

						<tr>
							<td>Deminimiss</td>
							<td class="amount"><?=formatAmount($total_deminimiss)?></td>
						</tr>
						<?
							foreach ($deminimiss as $code) {?>
								<tr>
									<td>&nbsp;&nbsp;<?=$income_config[$code]["description"]?></td>
									<td class="amount">&nbsp;&nbsp;<?=formatAmount($income_config[$code]["total"])?></td>
								</tr>
							<?}
						?>

						<tr>
							<td>OTHERS</td>
							<td class="amount"><?=formatAmount($total_other)?></td>
						</tr>
						<?
							foreach ($other as $code) {?>
								<tr>
									<td>&nbsp;&nbsp;<?=$income_config[$code]["description"]?></td>
									<td class="amount">&nbsp;&nbsp;<?=formatAmount($income_config[$code]["total"])?></td>
								</tr>
							<?}
						?>

						<tr>
							<td style="color:red;">TOTAL INCOME</td>
							<td class="amount"><b><?=formatAmount($total_income)?></b></td>
						</tr>

						<tr><td colspan="2" style="border:none;background-color: white;">&nbsp;</td></tr>

						<tr>
							<td>NET PAY</td>
							<td class="amount"><?=formatAmount($total_net_pay)?></td>
						</tr>

						<tr>
							<td>W/ TAX</td>
							<td class="amount"><?=formatAmount($total_whtax)?></td>
						</tr>

						<?
							foreach ($fixeddeduc_config as $code => $row) {?>
								<tr>
									<td><?=$row["description"]?></td>
									<td class="amount"><?=formatAmount($row["total"])?></td>
								</tr>
							<?}
						?>

						<?
							foreach ($deduction_config as $code => $row) {?>
								<tr>
									<td><?=$row["description"]?></td>
									<td class="amount"><?=formatAmount($row["total"])?></td>
								</tr>
							<?}
						?>

						<?
							foreach ($loan_config as $code => $row) {?>
								<tr>
									<td><?=$row["description"]?></td>
									<td class="amount"><?=formatAmount($row["total"])?></td>
								</tr>
							<?}
						?>

						<tr>
							<td style="color:red;">TOTAL DEDUCTION</td>
							<td class="amount"><b><?=formatAmount($total_deduc)?></b></td>
						</tr>

						<tr><td colspan="2" style="border:none;background-color: white;">&nbsp;</td></tr>

						<?
							foreach ($fixeddeduc_config as $code => $row) {?>
								<tr>
									<td><?=$row["description"]?></td>
									<td class="amount"><?=formatAmount($row["total"])?></td>
								</tr>
							<?}
						?>

						<tr>
							<td style="color:red;">TOTAL CONTRIBUTION</td>
							<td class="amount"><b><?=formatAmount($total_contribution)?></b></td>
						</tr>

					</tbody>
					<tfoot>
						<tr>
							<td style="text-align: center;padding-top: 10px;border:none;"><b>NUMBER OF EMPLOYEES:</b></td>
							<td style="text-align: center;padding-top: 10px;border:none;"><b><?=sizeof($emplist)?></b></td>
						</tr>
						<tr>
							<td style="border:none;">Prepared by:</td>
							<td style="border:none;">Reviewed by:</td>
						</tr>
					</tfoot>
				</table>

			</div>
