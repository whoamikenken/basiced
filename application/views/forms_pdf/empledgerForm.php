
<?php
/**
 * @author Glen Mark
 * @copyright 2017
 *
 */
$CI =& get_instance();
include "application/config/connection.php";
include "application/views/forms_pdf/function/payrollfunc.php";

$deductionConfig = $incomeConfig= $otherDeductionConfig = array();

$getIncomequery = $CI->db->query("SELECT id,description FROM payroll_income_config")->result();
foreach ($getIncomequery as $key => $value) {
	$incomeConfig[$value->id] = $value->description;
}


$getDeductionquery = $CI->db->query("SELECT code_deduction,description FROM deductions")->result();
foreach ($getDeductionquery as $key => $value) {
	$deductionConfig[$value->code_deduction] = $value->description;
}

$Deductionquery = $CI->db->query("SELECT id,description FROM payroll_deduction_config")->result();
foreach ($Deductionquery as $key => $value) {
	$otherDeductionConfig[$value->id] = $value->description;
}

$totearnings = $earnings = $totalSSS=$totalphil=$totalpagibig= "";
$mpdf = new mPDF('utf-8','LEGAL-L','3','','3','3','3','3','9','9');
$mpdf->setHeader('Employee Ledger Printing');
$mpdf->setFooter('{PAGENO}');
$data = $this->reports->loadEmployeeLedgerData($employeeid,$estatus,$deptid,$year);

foreach ($data as $key => $row) {


	$content .= "
			<br><br><br><br><br><br><br><br>
			<div class='container'> 

			<table class='header'  width='75%'>
			
			<tr>
			<td >Payroll Year: ".$year."</td>
			</tr>
			<tr>
			    <td  style='text-align: left';>Employee Name : ".strtoupper($row->fullname)."</td>
			</tr>
			<tr>
			    <td  style='text-align: left';>SSS No. : ".$row->emp_sss."</td><td>Employment Status : ".$row->employmentstatus." </td>
			</tr>
			<tr>
			    <td  style='text-align: left';>TIN No. : ".$row->emp_tin."</td><td>Employment Type/Position : ".strtoupper($row->empPosition)."</td>
			</tr>
			<tr>
			    <td  style='text-align: left';>College/Department : ".strtoupper($row->department)."</td>
			</tr>
			</table>	
			

			";
	$content .='<br>
				<table class="data" border=1 width="100%">
				<thead>
				<tr>
					<td rowspan="2">PAYROLL SCHEDULE</td>
					<td rowspan="2">BASIC SALARY (period)</td>
					<td colspan='.sizeof($incomeConfig).'>ADDITIONAL</td>
					<td rowspan="2">GROSS EARNINGS</td>
					<td colspan="'.(sizeof($deductionConfig) + sizeof($otherDeductionConfig)).'">DEDUCTIONS</td>
					<td rowspan="2">TOTAL DEDUCTIONS (period)</td>
					<td rowspan="2">NET SALARY (period)</td>
				</tr>
				<tr>
				';

				foreach ($incomeConfig as $code => $description)
				{
	$content .='<td>'.$description.'</td>';
				}
				foreach ($deductionConfig as $code => $description) {
	$content .='<td>'.$description.'</td>';
				}
				foreach ($otherDeductionConfig as $code => $description) 
				{
	$content .='<td>'.$description.'</td>';
				}
	$content .='</tr></thead>';
	//query for all employees
	$totalbasicSalary = $totalGross=  0;$deduct = $otherD = 0;$incomeSum = $overAllTotalDeductions = $overAllNetPay = 0;
	$query = $CI->db->query("SELECT * FROM payroll_computed_table WHERE employeeid='$row->employeeid' ORDER BY DATE(cutoffstart)")->result();
	foreach ($query as $key => $row) {
			$employeeIncome = $employeeDeduction = $employeeOtherDeduction = array(); 
			$totalDeduction = $totalIncome = $totalOthDeduction=  0;
			$totalbasicSalary += $row->salary;
			//income
			$income = $row->income;
			$explodeIncome = explode("/", $income);
			for ($i=0; $i <count($explodeIncome); $i++) 
			{ 
				$finalIncome = explode("=",$explodeIncome[$i]);
				$employeeIncome[$finalIncome[0]] = number_format($finalIncome[1],2);
				$totalIncome += $finalIncome[1];
			}
			//deduction
			$deduction = $row->fixeddeduc;
			$explodeFixeddeduc = explode("/", $deduction);
			for ($b=0; $b <count($explodeFixeddeduc) ; $b++) { 
				$finalDeduction = explode("=",$explodeFixeddeduc[$b]);
				$employeeDeduction[$finalDeduction[0]] = number_format($finalDeduction[1],2);
				$totalDeduction += $finalDeduction[1];
			}
			//other deduction
			$othDeduction = $row->otherdeduc;
			$explodeOthDeduction = explode('/', $othDeduction);
			for ($a=0; $a <count($explodeOthDeduction); $a++) { 
				$finalOthDeduction = explode("=",$explodeOthDeduction[$a]);
				$employeeOtherDeduction[$finalOthDeduction[0]] = number_format($finalOthDeduction[1],2);
				$totalOthDeduction += $finalOthDeduction[1];
			}
			$totalGross += ($row->salary + $totalIncome);
			$overAllTotalDeductions += $totalDeduction + $totalOthDeduction;
			$overAllNetPay += ($row->salary + $totalIncome) - ($totalDeduction+$totalOthDeduction);

	$content .= '<tr>
				<td>'.date("m-d-Y",strtotime($row->cutoffstart))." - ".date("m-d-Y",strtotime($row->cutoffend)).'</td>
				<td>'.number_format($row->salary,2).'</td></tr>
				';
			foreach ($incomeConfig as $code => $description) {
				if (isset($employeeIncome[$code])) {
	$content .=	'<td>'.$employeeIncome[$code].'</td>';
				}
				else
				{
	$content .= '<td>0.00</td>';
				}
			}
	//GROSS SALARY
	$content .= '<td>'.number_format($row->salary + $totalIncome,2).'</td>';

			foreach ($deductionConfig as $code => $description){
				if (isset($employeeDeduction[$code])){
	$content .= '<td>'.$employeeDeduction[$code].'</td>';
				}
				else
				{
	$content .= '<td>0.00</td>';	
				}
			}

			foreach ($otherDeductionConfig as $code => $description){
				if (isset($employeeOtherDeduction[$code])){
	$content .= '<td>'.$employeeOtherDeduction[$code].'</td>';
				}
				else
				{
	$content .= '<td>0.00</td>';	
				}
			}
	//TOTAL DEDUCTIONS AND NET SALARY
	$content .='<td>'.number_format($totalDeduction+$totalOthDeduction,2).'</td>
	<td>'.number_format(($row->salary + $totalIncome) - ($totalDeduction+$totalOthDeduction),2).'</td>';


	}//end of getting income deduction and other deduction
	// $content .='<tr><td></td><td>'.number_format($totalbasicSalary,2).'</td></tr>';
			
	// 		foreach ($incomeConfig as $code => $description) {
	// 			if (isset($employeeIncome[$code])) {
	// 				$incomeSum += $employeeIncome[$code];
	// 			}
	// $content .=	'<td>'.$incomeSum.'</td>';				
	// 		}


	// $content .= '<td>'.number_format($totalGross,2).'</td>';

	// 		foreach ($deductionConfig as $code => $description) 
	// 		{
	// 			if (isset($deductionConfig[$code])) {
	// 				$deduct += $employeeDeduction[$code];
	// 			}
	// $content .=	'<td>'.$deduct.'</td>';				
	// 		}
	// 		foreach ($otherDeductionConfig as $code => $description) 
	// 		{
	// 			if (isset($employeeOtherDeduction[$code])) {
	// 				$otherD += $employeeOtherDeduction[$code];
	// 			}
	// $content .=	'<td>'.$otherD.'</td>';				
	// 		}		


	// $content .= '<td>'.number_format($overAllTotalDeductions,2).'</td>
				// <td>'.number_format($overAllNetPay,2).'</td>';
	$content .= "</tr></table>";
	$content .= "<pagebreak>";
}//end of looping for employee



$html = "
		<style>
		p{
		 margin-left:50px;
		}
		.tblremarks
		{
		 margin-left:50px;
		 width:100%;
		}
		.header
		{
		 border-collapse:collapse;
		 position:absolute;
		 font-size:12px;
		 font-family:calibri;
		}
		.datadeduction
		{
			
			text-align:right;
		}
		.datagrosspay
		{
			
			text-align:right;
		}
		.tbl
		{
		 margin-left:50px;
	     border-collapse:collapse;
		 width:95%;

		}
		#grosspay
		{
			width:30%;
			margin-top:10px;
			 margin-left:50px;
		}
		#otherdeduction
		{
			 margin-left:50px;
			width:30%;
		margin-top: 10px;

		}
		.container{
			
			width:100%;
		}
		.data
		{
		 border-collapse:collapse;
		 font-weight:normal;
		 font-size:12px;
		 font-family:times new roman;
		 text-align:center;
		 width:100%;	
		}
		.datas
		{
		 border-collapse:collapse;
		 font-weight:normal;
		 font-size:12px;
		 font-family:times new roman;
		 text-align:right;
	
		}
		.head{
		text-align:center;
	 	font-size:12px;
	  	border:1px solid;	
		}
		</style>
		<body>".$content."</body>
		";
// echo $html;

$mpdf->WriteHTML($html);
$mpdf->Output();
die;



?>
<style>
		p{
		 margin-left:50px;
		}
		.tblremarks
		{
		 margin-left:50px;
		 width:100%;
		}
		.header
		{
		 border-collapse:collapse;
		 position:absolute;
		 font-size:12px;
		 font-family:calibri;
		}
		.datadeduction
		{
			
			text-align:right;
		}
		.datagrosspay
		{
			
			text-align:right;
		}
		.tbl
		{
		 margin-left:50px;
	     border-collapse:collapse;
		 width:95%;

		}
		#grosspay
		{
			width:30%;
			margin-top:10px;
			 margin-left:50px;
		}
		#otherdeduction
		{
			 margin-left:50px;
			width:30%;
		margin-top: 10px;

		}
		.container{
			margin-top:10%;
			width:100%;
		}
		.data
		{
		 border-collapse:collapse;
		 font-weight:normal;
		 font-size:12px;
		 font-family:times new roman;
		 text-align:center;
		 width:100%;	
		}
		.datas
		{
		 border-collapse:collapse;
		 font-weight:normal;
		 font-size:12px;
		 font-family:times new roman;
		 text-align:right;
	
		}
		.head{
		text-align:center;
	 	font-size:12px;
	  	border:1px solid;	
		}
</style>

<table class='data' border=1 width="100%">
<thead>
<tr>
	<td rowspan='2'>PAYROLL SCHEDULE</td>
	<td rowspan='2'>BASIC SALARY (period)</td>
	<td colspan='<?=sizeof($incomeConfig)?>'>ADDITIONAL</td>
	<td rowspan='2'>GROSS EARNINGS</td>
	<td colspan='<?=sizeof($deductionConfig) + sizeof($otherDeductionConfig)?>'>DEDUCTIONS</td>
	<td rowspan='2'>TOTAL DEDUCTIONS (period)</td>
	<td rowspan='2'>NET SALARY (period)</td>

</tr>
<tr>

	<?
	//label for Income
	foreach ($incomeConfig as $code => $description) 
	{
	?>
		<td><?=$description?></td>
	<?
	}
	?>
	<?
	
	//label for Deduction
	foreach ($deductionConfig as $code => $description) 
	
	{
	?>
		<td><?=$description?></td>	
	<?
	}
	//label for Other Deduction

	foreach ($otherDeductionConfig as $code => $description) 
	{
	?>
		<td><?=$description?></td>
	<?
	}
	?>
	
</tr>
</thead>
<? 

$query = $CI->db->query("SELECT * FROM payroll_computed_table WHERE employeeid='0003' ORDER BY DATE(cutoffstart)")->result();
// echo'<pre>';print_r($query);
foreach ($query as $key => $row) {
		$employeeIncome = $employeeDeduction = $employeeOtherDeduction = array(); 
		$totalDeduction = $totalIncome = $totalbasicSalary= $totalOthDeduction=  0;
		
		//income
		$income = $row->income;
		$explodeIncome = explode("/", $income);
		for ($i=0; $i <count($explodeIncome); $i++) 
		{ 
			$finalIncome = explode("=",$explodeIncome[$i]);
			$employeeIncome[$finalIncome[0]] = number_format($finalIncome[1],2);
			$totalIncome += $finalIncome[1];
		}
		//deduction
		$deduction = $row->fixeddeduc;
		$explodeFixeddeduc = explode("/", $deduction);
		for ($b=0; $b <count($explodeFixeddeduc) ; $b++) { 
			$finalDeduction = explode("=",$explodeFixeddeduc[$b]);
			$employeeDeduction[$finalDeduction[0]] = number_format($finalDeduction[1],2);
			$totalDeduction += $finalDeduction[1];
		}
		//other deduction
		$othDeduction = $row->otherdeduc;
		$explodeOthDeduction = explode('/', $othDeduction);
		for ($a=0; $a <count($explodeOthDeduction); $a++) { 
			$finalOthDeduction = explode("=",$explodeOthDeduction[$a]);
			$employeeOtherDeduction[$finalOthDeduction[0]] = number_format($finalOthDeduction[1],2);
			$totalOthDeduction += $finalOthDeduction[1];
		}


?>

			<tr>
						<td><?=date("m-d-Y",strtotime($row->cutoffstart))." - ".date("m-d-Y",strtotime($row->cutoffend))?></td>
						<td><?=number_format($row->salary,2)?></td>
						<?
						//for IncomeConfig
						foreach ($incomeConfig as $code => $description) {
							if (isset($employeeIncome[$code])) {
							
						?>
							<td><?=$employeeIncome[$code]?></td>	
						<?
						}
							else
							{
							?>
								<td>0.00</td>	
							<?
							}
						}

						?>
						<td><?= number_format($row->salary + $totalIncome,2)?></td>
						<?
						//for deduction
						foreach ($deductionConfig as $code => $description) 
						{
							if (isset($employeeDeduction[$code])) 
							{

						?>
							<td><?=$employeeDeduction[$code]?></td>						
						<?
							}
							else
							{
							?>
								<td>0.00</td>
							<?
							}
						}
						
						?>
						<?
						//for other deduction
						foreach ($otherDeductionConfig as $code => $description) 
						{
							if (isset($employeeOtherDeduction[$code])) {
								
						?>
							<td><?=$employeeOtherDeduction[$code]?></td>	
						<?
						 	}
						 	else
						 	{
						 	?>
						 		<td>0.00</td>
						 	<?
						 	}
					     }
						?>
						<td><?=number_format($totalDeduction+$totalOthDeduction,2)?></td>
						<td><?=number_format(($row->salary + $totalIncome) - ($totalDeduction+$totalOthDeduction),2)?></td>
						
			<tr>

					 
<?
}

?>
<tbody>
	

</tbody>
</table>

