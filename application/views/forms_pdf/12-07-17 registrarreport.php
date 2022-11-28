<?php
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
$month = date("F",$dfrom);
$from = date("j",strtotime($dfrom));
$to = date("j",strtotime($dto));
$year = date("Y",strtotime($dfrom));
$loantitle=$loanamount=$lamount= $a="";
$eid        = $_GET['eid']; 
$dept       = $_GET['dept'];
$dfrom      = $_GET['dfrom'];
$dto        = $_GET['dto'];
$schedule   = $_GET['schedule'];
$quarter    = $_GET['quarter']; 
$sort       = $_GET['sort'];
$totearnings = $earnings = $totalSSS=$totalphil=$totalpagibig= "";





$mpdf = new mPDF('utf-8','A3-L','10','','3','3','33','10','9','9');
$mpdf->SetHTMLHeader("<table class='header'>
		<tr>
		<td><img src='images/school_logo.jpg' style='width: 80px;'/></td>
		<td style='color:blue;'><b style='align_center'><h1>Pinnacle Technologies Inc.</h1></b>
		<b>PAYROLL SHEET FOR SALARY SCHEDULE : ".$month." ".$from." - ".$to.", ".$year." </b>
		
		</tr>
		</table>	",'',false);
	// echo '<pre>';print_r($empids);unset($empids['']);
	// echo '<pre>';print_r($row);
// <div class='container'> 
// 		<br>
// 		<span><b>Breakdown of Income</b></span></td>
// 		<table class='data' border=1>
// 		<tr>
// 		<td>No.</td>
// 		<td>ID</td>
// 		<td>NAME OF EMPLOYEE</td>
// 		<td>SALARY</td>
// 		".$incometitles."
// 		<td>GROSS EARNINGS</td>
// 		</tr>
// 		".$incomeresult."
// 		</table>
// 		<pagebreak>
// 		</div>

// 		<div class='container'> 
// 		<br>
// 		<span><b>Breakdown of Deduction</span></b></td>
// 		<table class='data' border=1>
// 		<tr>
// 			<td rowspan='2'>No.</td>
// 			<td rowspan='2'>ID</td>
// 			<td rowspan='2'>NAME OF EMPLOYEE</td>
// 			<td colspan='3'>CONTRIBUTION</td>
// 			<td rowspan='2'>WITHOLDINGTAX</td>
// 			<td rowspan='2'>SSS LOAN</td>
// 			<td rowspan='2'>PAGIBIG LOAN</td>
// 			<td rowspan='2'>PERRA LOAN</td>
// 			".$otherdeductiontitle."
// 			<td rowspan='2'>TOTAL DEDUCTION</td>
// 			<td rowspan='2'>NET PAY</td>

// 		</tr>
// 		<tr><td>SSS</td>
// 		<td>PHILHEALTH</td>
// 		<td>PAGIBIG</td>
// 		</tr>

// 			".$deductionresult."
// 		</table>
// 		</div>
$empids = array();
	$gross = $this->payroll->SlipRecord($eid,$schedule,$quarter,$dfrom,$dto,$dept,$sort);
	foreach ($gross as $row) {
		$employeeIncome = $employeeDeduction = $employeeOtherDeduction = array(); 
		$totalDeduction = $totalIncome = $totalOthDeduction=  0;
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
						$employeeDeduction[$finalDeduction[0]] = $finalDeduction[1];
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
			$empids[] = array("fullname" => $row->fullname,"empid" => $row->employeeid,"salary"=>$row->salary,"income"=>$employeeIncome,"deduction"=>$employeeDeduction,"totalIncome"=>$totalIncome,"totalDeduction"=>$totalDeduction,"otherdedeuction"=>$employeeOtherDeduction,"tax" => $row->withholdingtax,"overallDeduction" => ($totalDeduction + $totalOthDeduction + $row->withholdingtax),"netpay" => ($row->salary - ($totalDeduction + $totalOthDeduction + $row->withholdingtax)) );
			
			
		}
$interval = 30;

$mpdf->AddPage();
$content .= "
			<span><b>Breakdown of Income</b></span></td>
			<table class='data' border=1>
				<thead>
				<tr>
					<td>No.</td>
					<td>ID</td>
					<td>NAME OF EMPLOYEE</td>
					<td>SALARY</td>
			";

			foreach ($incomeConfig as $code => $description) {
$content .="<td>".$description."</td>";
			}
$content .="<td>GROSS EARNINGS</td></tr></thead>";

$empid = array();
for($i=0;$i<=sizeof($empids);$i += $interval) 
{ 
	for ($k=$i;$k<$interval;$k++) 
	{ 	
		
		$empid[$empids[$k]['empid']] =  array("empid"=>$empids[$k]['empid'],"fullname" =>$empids[$k]['fullname'] , "deduction" =>$empids[$k]['deduction'],"fixeddeduc"=>$empids[$k]['otherdedeuction'],"tax" => $empids[$k]['tax'],'OAD'=>$empids[$k]['overallDeduction'],'netpay' => $empids[$k]['netpay']);
$content .= "<tr>
		<td>".round($k+1)."</td>
		<td>".$empids[$k]['empid']."</td>
		<td>".$empids[$k]['fullname']."</td>
		<td>".$empids[$k]['salary']."</td>";
		foreach ($incomeConfig as $key => $value) {
			if (isset($empids[$k]["income"][$key])) 
			{
$content .="<td>".$empids[$k]["income"][$key]."</td>";	
			}
			else
			{
$content .="<td>0.00</td>";
			}
		}
$content .="<td>".number_format($empids[$k]["totalIncome"],2)."</td></tr>
	
	";
	}
$content.="</table>";

}

$content .="<pagebreak><br><span><b>Breakdown of Deduction</b></span></td>
<table class='data' border=1>
	<thead>
	<tr>
		<td rowspan='2'>No.</td>
		<td rowspan='2'>ID</td>
		<td rowspan='2'>NAME OF EMPLOYEE</td>
		<td colspan='".sizeof($deductionConfig)."'>CONTRIBUTION</td>
		<td rowspan='2'>WITHOLDINGTAX</td>";
		$no = 1;
foreach ($otherDeductionConfig as $code => $description) {
		
$content .="<td rowspan='2'>".$description."</td>";
			}
$content .="<td rowspan='2'>TOTAL DEDUCTION</td>
		<td rowspan='2'>NET PAY</td>
		</tr>
		<tr>";
		foreach ($deductionConfig as $code => $description) {
$content .="<td>".$description."</td>";
		}
$content .="</tr></thead>";
foreach($empid as $k =>$key)
{
$content .="<tr>
			<td>".$no++."</td>
			<td>".$key['empid']."</td>
			<td>".$key["fullname"]."</td>";

foreach ($deductionConfig as $code => $description) {
				if (isset($key['deduction'][$code])) {
$content .="<td>".$key['deduction'][$code]."</td>";
			}else{
$content .= "<td>0.00</td>";			
				}
			}
if (isset($key['tax']))
{
$content .="<td>".number_format($key['tax'],2)."</td>";	
}
else
{
$content .="<td>0.00</td>";
}

	foreach ($otherDeductionConfig as $code => $description) {
		 if (isset($key['fixeddeduc'][$code])) {
$content.="<td>".number_format($key['fixeddeduc'][$code],2)."</td>";
	}
	else
	{
$content .="<td>0.00</td>";
	}
	}
$content .="<td>".number_format($key['OAD'],2)."</td>
			<td>".number_format($key['netpay'],2)."</td>
		</tr>";
}
$content .="</table>";


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
<?

$empids = array();
	$gross = $this->payroll->SlipRecord($eid,$schedule,$quarter,$dfrom,$dto,$dept,$sort);
	foreach ($gross as $row) {
		$employeeIncome = $employeeDeduction = $employeeOtherDeduction = array(); 
		$totalDeduction = $totalIncome = $totalOthDeduction=  0;
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
						$employeeDeduction[$finalDeduction[0]] = $finalDeduction[1];
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
			$empids[] = array("fullname" => $row->fullname,"empid" => $row->employeeid,"salary"=>$row->salary,"income"=>$employeeIncome,"deduction"=>$employeeDeduction,"totalIncome"=>$totalIncome,"totalDeduction"=>$totalDeduction,"otherdedeuction"=>$employeeOtherDeduction,"tax" => $row->withholdingtax,"overallDeduction" => ($totalDeduction + $totalOthDeduction + $row->withholdingtax),"netpay" => ($row->salary - ($totalDeduction + $totalOthDeduction + $row->withholdingtax)) );
			
			
		}
	// echo '<pre>';print_r($empids);unset($empids['']);
	// echo '<pre>';print_r($row);
$interval = 50;
?>

<span><b>Breakdown of Income</b></span></td>
<table class='data' border=1>
	<tr>
		<td>No.</td>
		<td>ID</td>
		<td>NAME OF EMPLOYEE</td>
		<td>SALARY</td>
	<?
	foreach ($incomeConfig as $code => $description) {
	?>
		<td><?= $description?></td>
	<?
	}
	?>
		<td>GROSS EARNINGS</td>
	</tr>
	
<?
	$empid = array();
for($i=0;$i<=sizeof($empids);$i += $interval) 
{ 
	for ($k=$i;$k<$interval;$k++) 
	{ 	
		
		$empid[$empids[$k]['empid']] =  array("empid"=>$empids[$k]['empid'],"fullname" =>$empids[$k]['fullname'] , "deduction" =>$empids[$k]['deduction'],"fixeddeduc"=>$empids[$k]['otherdedeuction'],"tax" => $empids[$k]['tax'],'OAD'=>$empids[$k]['overallDeduction'],'netpay' => $empids[$k]['netpay']);
		 	 	
			
		?>
		<tr>
		<td><?=round($k)?></td>
		<td><?=$empids[$k]['empid']?></td>
		<td><?=$empids[$k]['fullname']?></td>
		<td><?=$empids[$k]['salary']?></td>
		<?
		foreach ($incomeConfig as $key => $value) {
			if (isset($empids[$k]["income"][$key])) {?>
			<td><?=$empids[$k]["income"][$key]?></td>	
			<?}
			else
			{?>
			<td>0.00</td>
			<?}
		}
		?>
		<td><?=$empids[$k]["totalIncome"]?></td>
		</tr>

		<?
	}

	?>

</table>
<span><b>Breakdown of Deduction</b></span></td>
<table class='data' border=1>
	<tr>
		<td rowspan='2'>No.</td>
		<td rowspan='2'>ID</td>
		<td rowspan='2'>NAME OF EMPLOYEE</td>
		<td colspan='<?=sizeof($deductionConfig)?>'>CONTRIBUTION</td>
		<td rowspan='2'>WITHOLDINGTAX</td>
		<!-- <td rowspan='2'>SSS LOAN</td>
		<td rowspan='2'>PAGIBIG LOAN</td>
		<td rowspan='2'>PERRA LOAN</td> -->
	<?
	foreach ($otherDeductionConfig as $code => $description) {
	?>
		<td rowspan='2'><?= $description?></td>
	<?}
	?>
		<td rowspan='2'>TOTAL DEDUCTION</td>
		<td rowspan='2'>NET PAY</td>

	</tr>

	<tr>
		<?
		foreach ($deductionConfig as $code => $description) {?>
			<td><?=$description?></td>
		<?}
		?>
	</tr>
	
	<?foreach($empid as $k =>$key)
		
	{?>
		<tr>
			<td><?=$no++?></td>
			<td><?=$key['empid']?></td>
			<td><?=$key["fullname"]?></td>
			<?
			foreach ($deductionConfig as $code => $description) {
				if (isset($key['deduction'][$code])) {?>
				<td><?=$key['deduction'][$code]?></td>
			<?
			}
			else
				{?>
				<td>0.00</td>
				<?}
			}
			?>
			<?php if (isset($key['tax'])): ?>
				<td><?=number_format($key['tax'],2)?></td>
			<?php else: ?>
				<td>0.00</td>
			<?php endif ?>

			
			
	<?
		foreach ($otherDeductionConfig as $code => $description) {
			 if (isset($key['fixeddeduc'][$code])) {
	?>
				<td><?=number_format($key['fixeddeduc'][$code],2)?></td>
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
			<td><?=number_format($key['OAD'],2)?></td>
			<td><?=number_format($key['netpay'],2)?></td>
		</tr>
	<?
	}
	?>
</table>
<?
	
	// for ($a=$k; $a<= $a+$interval ; $a++) { 
	// 
	// }
?>
<?}

unset($empid['']);
// echo '<pre>';var_dump($empid);
?>


	<!-- // echo '<pre>'; print_r($key); -->

		<!-- <?=$data?> -->
	

<!-- <pagebreak> -->
		<!-- <span><b>Breakdown of Deduction</b></span></td>
		<table class='data' border=1>
			<tr>
			<td rowspan='2'>No.</td>
			<td rowspan='2'>ID</td>
			<td rowspan='2'>NAME OF EMPLOYEE</td>
			<td colspan='3'>CONTRIBUTION</td>
			<td rowspan='2'>WITHOLDINGTAX</td>
			<td rowspan='2'>SSS LOAN</td>
			<td rowspan='2'>PAGIBIG LOAN</td>
			<td rowspan='2'>PERRA LOAN</td>
			<?
			foreach ($otherDeductionConfig as $code => $description) {
			?>
			<td rowspan='2'><?= $description?></td>
			<?}
			?>
			<td rowspan='2'>TOTAL DEDUCTION</td>
			<td rowspan='2'>NET PAY</td>

			</tr>

			<tr><td>SSS</td>
			<td>PHILHEALTH</td>
			<td>PAGIBIG</td>
			</tr>

		</table> -->
			<!-- echo $k."--"; -->
<!-- 
<div class='container'> 
		
		<span><b>Breakdown of Income</b></span></td>
		<table class='data' border=1>
			<tr>
			<td>No.</td>
			<td>ID</td>
			<td>NAME OF EMPLOYEE</td>
			<td>SALARY</td>
			<?
			foreach ($incomeConfig as $code => $description) {
			?>
			<td><?= $description?></td>
			<?}
			?>
			</tr>

		</table>
		<span><b>Breakdown of Deduction</b></span></td>
		<table class='data' border=1>
			<tr>
			<td rowspan='2'>No.</td>
			<td rowspan='2'>ID</td>
			<td rowspan='2'>NAME OF EMPLOYEE</td>
			<td colspan='3'>CONTRIBUTION</td>
			<td rowspan='2'>WITHOLDINGTAX</td>
			<td rowspan='2'>SSS LOAN</td>
			<td rowspan='2'>PAGIBIG LOAN</td>
			<td rowspan='2'>PERRA LOAN</td>
			<?
			foreach ($otherDeductionConfig as $code => $description) {
			?>
			<td rowspan='2'><?= $description?></td>
			<?}
			?>
			<td rowspan='2'>TOTAL DEDUCTION</td>
			<td rowspan='2'>NET PAY</td>

			</tr>

			<tr><td>SSS</td>
			<td>PHILHEALTH</td>
			<td>PAGIBIG</td>
			</tr>

		</table>

</div>
 -->