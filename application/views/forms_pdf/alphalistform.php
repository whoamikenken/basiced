<?php
require_once(APPPATH."constants.php");
$year  = ($_GET['year'] ? $_GET['year'] : $_POST['year']);

$pdf = new mpdf('utf-8','LEGAL-L','','UTF-8',5,5,5,8,9,2);
 
$year  = ($_GET['year'] ? $_GET['year'] : $_POST['year']);
	
$result = $this->reports->alphalistEmp($year);

//$pdf->WriteHTML($info);

$PEACH 	= "rgb(252, 228, 214)";
$GREEN 	= "rgb(169, 208, 142)";
$ORANGE = "rgb(244, 176, 132)";
$BLUE 	= "rgb(91, 155, 213)";

$info = "
<style>
.footer{
	width: 100%;
	text-align: right;
}
</style>
";
$info .= "
					<div>
						<div>
							<label>Payroll Sheet</label><br>
							<label>for the year <u>".$year."</u></label>
						</div>
						<div style='padding-top:1%;font-size:10px'>
							<label>Client : <b>".$SCHOOL_NAME."</b></label><br>
							<label>Address : <b>".$SCHOOL_ADDRESS."</b></label>
							<label>SSS# : <b>".$SCHOOL_SSS."</b></label>
							<label>PEN : <b>".$SCHOOL_PEN."</b></label>
							<label>TIN : <b>".$SCHOOL_TIN."</b></label>
							<label>PAGIBIG TRN : <b>".$SCHOOL_PAGIBIG_TRN."</b></label>
						</div>
						<div style='padding-top:1%'>
							<table border='1' style='width:100%;font-size:.7em;border-collapse: collapse;text-align:center'>
								<thead>
									<tr style='background-color:".$PEACH."'>
										<th style='vertical-align: bottom;'>No.</th>
										<th style='vertical-align: bottom;'>Name</th>
										<th style='vertical-align: bottom;'>SSS #</th>
										<th style='vertical-align: bottom;'>TIN</th>
										<th style='vertical-align: bottom;'>Philhealth No.</th>
										<th style='vertical-align: bottom;'>Pag-Ibig</th>
										<th style='vertical-align: bottom;'>B-DAY</th>
										<th style='vertical-align: bottom;'>Period Covered</th>
										<th style='vertical-align: bottom;'>Net Income</th>
										<th style='vertical-align: bottom;'>COLA</th>
										<th style='vertical-align: bottom;'>Gross Income</th>
										<th style='vertical-align: bottom;'>Total SSS EE</th>
										<th style='vertical-align: bottom;'>Total MCR EE</th>
										<th style='vertical-align: bottom;'>TOTAL P-Ibig EE</th>
										<th style='vertical-align: bottom;'>Total EE</th>
										<th style='vertical-align: bottom;'>Net Pay w/o EE</th>
										<th style='vertical-align: bottom;'>13th Mo. Pay</th>
										<th style='vertical-align: bottom;'>Total Salary</th>
										<th style='vertical-align: bottom;'>Status</th>
										<th style='vertical-align: bottom;'>Personal Exemption</th>
										<th style='vertical-align: bottom;'>Tax due</th>
										<th style='vertical-align: bottom;'>Tax W/Held</th>
										<th style='vertical-align: bottom;'>Still Due</th>
										<th style='vertical-align: bottom;'>Monthly Tax</th>
										<th style='vertical-align: bottom;'>Still Due</th>
										<th style='vertical-align: bottom;'>Monthly Taxes</th>
									</tr>
								</thead>
";
$info .= "
								<tbody>";
if(sizeof($result) > 0){
	$i=0;
	foreach ($result as $key => $row) {
		$i++;
		
		$netIncomeTotal = array();
		$COLATotal = array();
		$grossIncomeTotal = array();
		$sssTotal = array();
		$mcrTotal = array();
		$pagibigTotal = array();
		$eeTotal = array();
		$netPayWithoutEETotal = array();
		$month13PayTotal = array();
		$totalSalaryTotal = array();
		
		
		
		$withholdingtaxTotal = array();
		
		$query = $this->reports->alphalistData($row->employeeid,$year);
		$monthRange = array();
		$monthRange2 = array();
		$withholdingtax = 0;
		$periodCover = $netIncome = $COLA = $grossIncome = $sssEE = $mcrEE = $pagibigEE = $totalEE = $netpayWithoutEE = $month13pay = $totalSalary = 0;
		$periodCover2 = $netIncome2 = $COLA2 = $grossIncome2 = $sssEE2 = $mcrEE2 = $pagibigEE2 = $totalEE2 = $netpayWithoutEE2 = $month13pay2 = $totalSalary2 = 0;
		foreach ($query as $k => $rs) {
			if(date("n",strtotime($rs->cutoffstart)) < 6)
			{
				//NET INCOME
				$netIncome += $rs->salary;
				
				//COLA
				foreach(explode("/",$rs->income) as $inc)
				{
					$in = explode("=",$inc);
					$COLA += $in[1];
				}
				
				//EE
				foreach(explode("/",$rs->fixeddeduc) as $deduc)
				{
					$d = explode("=",$deduc);
					if($d[0] == "PAGIBIG")
					{
						$pagibigEE += $d[1];
					}
					else if($d[0] == "PHILHEALTH")
					{
						$mcrEE += $d[1];
					}
					else if($d[0] == "SSS")
					{
						$sssEE += $d[1];
					}
				}
				
				//13TH MONTH PAY
				$month13pay = 0;
				
				$monthRange[] = date("n",strtotime($rs->cutoffstart));
			}
			else
			{
				//NET INCOME
				$netIncome2 += $rs->salary;
				
				//COLA
				foreach(explode("/",$rs->income) as $inc)
				{
					$in = explode("=",$inc);
					$COLA2 += $in[1];
				}
				
				//EE
				foreach(explode("/",$rs->fixeddeduc) as $deduc)
				{
					$d = explode("=",$deduc);
					if($d[0] == "PAGIBIG")
					{
						$pagibigEE2 += $d[1];
					}
					else if($d[0] == "PHILHEALTH")
					{
						$mcrEE2 += $d[1];
					}
					else if($d[0] == "SSS")
					{
						$sssEE2 += $d[1];
					}
				}
				
				//13TH MONTH PAY
				$month13pay2 = 0;
					
				$monthRange2[] = date("n",strtotime($rs->cutoffstart));
			}
			
			$withholdingtax += $rs->withholdingtax;
		}
		
		$info .= "				
									<tr>
										<td>".$i."</td>
										<td style='white-space: nowrap;text-align:left'>".$row->fullname."</td>
										<td>".$row->emp_sss."</td>
										<td>".$row->emp_tin."</td>
										<td>".$row->philhealth."</td>
										<td>".$row->pagibig."</td>
										<td style='white-space: nowrap;'>".date("F d, Y",strtotime($row->bdate))."</td>";
		
		//FIRST HALF
		if(count($monthRange) > 0)
		{
			if(min($monthRange) == max($monthRange))
			{
				$periodCover = max($monthRange);
			}
			else
			{
				$periodCover = min($monthRange)."-".max($monthRange);
			}
			
			$grossIncome = $netIncome + $COLA;
			
			$totalEE = $sssEE + $mcrEE + $pagibigEE;
			
			$netpayWithoutEE = $grossIncome - $totalEE;
			
			$totalSalary = $netpayWithoutEE + $month13pay;
			$info .= "					<td>".$periodCover."</td>
										<td style='background-color:".$GREEN."'>".formatAmount($netIncome)."</td>
										<td style='background-color:".$GREEN."'>".formatAmount($COLA)."</td>
										<td style='background-color:".$GREEN."'>".formatAmount($grossIncome)."</td>
										<td>".formatAmount($sssEE)."</td>
										<td>".formatAmount($mcrEE)."</td>
										<td>".formatAmount($pagibigEE)."</td>
										<td style='background-color:".$PEACH."'>".formatAmount($totalEE)."</td>
										<td style='background-color:".$ORANGE."'>".formatAmount($netpayWithoutEE)."</td>
										<td>".formatAmount($month13pay)."</td>
										<td style='background-color:".$BLUE."'>".formatAmount($totalSalary)."</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
		";
		}
		
		if(count($monthRange) > 0 && count($monthRange2) > 0)
		{
			$info .= "
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
		";
		}
		
		//SECOND HALF
		if(count($monthRange2) > 0)
		{
			if(min($monthRange2) == max($monthRange2))
			{
				$periodCover2 = max($monthRange2);
			}
			else
			{
				$periodCover2 = min($monthRange2)."-".max($monthRange2);
			}
			
			$grossIncome2 = $netIncome2 + $COLA2;
			
			$totalEE2 = $sssEE2 + $mcrEE2 + $pagibigEE2;
			
			$netpayWithoutEE2 = $grossIncome2 - $totalEE2;
			
			$totalSalary2 = $netpayWithoutEE2 + $month13pay2;
			$info .= "					<td>".$periodCover2."</td>
										<td style='background-color:".$GREEN."'>".$netIncome2."</td>
										<td style='background-color:".$GREEN."'>".$COLA2."</td>
										<td style='background-color:".$GREEN."'>".$grossIncome2."</td>
										<td>".$sssEE2."</td>
										<td>".$mcrEE2."</td>
										<td>".$pagibigEE2."</td>
										<td style='background-color:".$PEACH."'>".$totalEE2."</td>
										<td style='background-color:".$ORANGE."'>".$netpayWithoutEE2."</td>
										<td>".$month13pay2."</td>
										<td style='background-color:".$BLUE."'>".$totalSalary2."</td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
		";
		}		
												
		if(count($monthRange) > 0 && count($monthRange2) > 0)
		{
			$info .= "				
									<tr>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th style='background-color:".$GREEN.";border-top:2px solid black;'>".($netIncome + $netIncome2)."</th>
										<th style='background-color:".$GREEN.";border-top:2px solid black;'>".($COLA + $COLA2)."</th>
										<th style='background-color:".$GREEN.";border-top:2px solid black;'>".($grossIncome + $grossIncome2)."</th>
										<th style='border-top:2px solid black;'>".($sssEE + $sssEE2)."</th>
										<th style='border-top:2px solid black;'>".($mcrEE + $mcrEE2)."</th>
										<th style='border-top:2px solid black;'>".($pagibigEE + $pagibigEE2)."</th>
										<th style='background-color:".$PEACH.";border-top:2px solid black;'>".($totalEE + $totalEE2)."</th>
										<th style='background-color:".$ORANGE.";border-top:2px solid black;'>".($netpayWithoutEE + $netpayWithoutEE2)."</th>
										<th style='border-top:2px solid black;'>".($month13pay + $month13pay2)."</th>
										<th style='background-color:".$BLUE.";border-top:2px solid black;'>".($totalSalary + $totalSalary2)."</th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
									</tr>";
			$netIncomeTotal[] = $netIncome + $netIncome2;
			$COLATotal[] = $COLA + $COLA2;
			$grossIncomeTotal[] = $grossIncome + $grossIncome2;
			$sssTotal[] = $sssEE + $sssEE2;
			$mcrTotal[] = $mcrEE + $mcrEE2;
			$pagibigTotal[] = $pagibigEE + $pagibigEE2;
			$eeTotal[] = $totalEE + $totalEE2;
			$netPayWithoutEETotal[] = $netpayWithoutEE + $netpayWithoutEE2;
			$month13PayTotal[] = $month13pay + $month13pay2;
			$totalSalaryTotal[] = $totalSalary + $totalSalary2;
		}
		else {
			if(count($monthRange) > 0)
			{
				$netIncomeTotal[] = $netIncome;
				$COLATotal[] = $COLA;
				$grossIncomeTotal[] = $grossIncome;
				$sssTotal[] = $sssEE;
				$mcrTotal[] = $mcrEE;
				$pagibigTotal[] = $pagibigEE;
				$eeTotal[] = $totalEE;
				$netPayWithoutEETotal[] = $netpayWithoutEE;
				$month13PayTotal[] = $month13pay;
				$totalSalaryTotal[] = $totalSalary;
			}
			else if(count($monthRange2) > 0)
			{
				$netIncomeTotal[] = $netIncome2;
				$COLATotal[] = $COLA2;
				$grossIncomeTotal[] = $grossIncome2;
				$sssTotal[] = $sssEE2;
				$mcrTotal[] = $mcrEE2;
				$pagibigTotal[] = $pagibigEE2;
				$eeTotal[] = $totalEE2;
				$netPayWithoutEETotal[] = $netpayWithoutEE2;
				$month13PayTotal[] = $month13pay2;
				$totalSalaryTotal[] = $totalSalary2;
			}
		}
		
		$withholdingtaxTotal[] = $withholdingtax;
		
		$info .= "
									<tr style='background-color:white'>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
										<td> </td>
									</tr>";	
	}
}

$info .= "
								</tbody>
								<tfoot>
									<tr style='background-color:".$PEACH."'>
										<th></th>
										<th>TOTAL</th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th>".array_sum($netIncomeTotal)."</th>
										<th>".array_sum($COLATotal)."</th>
										<th>".array_sum($grossIncomeTotal)."</th>
										<th>".array_sum($sssTotal)."</th>
										<th>".array_sum($mcrTotal)."</th>
										<th>".array_sum($pagibigTotal)."</th>
										<th>".array_sum($eeTotal)."</th>
										<th>".array_sum($netPayWithoutEETotal)."</th>
										<th>".array_sum($month13PayTotal)."</th>
										<th>".array_sum($totalSalaryTotal)."</th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</tfoot>
";

$info .= "
							</table>
						</div>
					</div>
";

$footer = array (
    'odd' => array (
        'R' => array (
            'content' => 'Page : {PAGENO} of {nb}',
            'font-size' => 10,
            'font-family' => 'serif',
            'color'=>'#000000'
        )
    ),
    'even' => array ()
); 
$pdf->SetFooter($footer);
$pdf->WriteHTML($info);
$pdf->Output();

function formatAmount($amount=''){
	$return ="";
if($amount)
{
    $return = number_format( $amount, 2 );
}
else
{
    $return = '0.00';
}
return $return;
}
?>