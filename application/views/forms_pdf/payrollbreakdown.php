<?php

include "application/config/connection.php";
include "application/views/forms_pdf/function/payrollfunc.php";
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




function displayOthDeduc($eid,$schedule,$quarter,$dfrom,$dto){
    $return = "";
    $total = 0;
    $query = mysql_query("SELECT otherdeduc FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $otherdeduc = $data['otherdeduc'];
    
    $exotherdeduc = explode("/",$otherdeduc);
    for($x = 0;$x < count($exotherdeduc); $x++){
        $iexotherdeduc = explode("=",$exotherdeduc[$x]);
        if($iexotherdeduc[1] != 0){
        $return .= "<tr>
                        <td class='eddesc'>".deductiondesc($iexotherdeduc[0])."</td>
                        <td class='edamt'> ".number_format($iexotherdeduc[1],2)."</td>
                    </tr>                        
                   ";
        $total += $iexotherdeduc[1];
        }
    }
    return array($return,$total);
}
function displayLoan($eid,$schedule,$quarter,$dfrom,$dto){
    $return = "";
    $sss=$phil=$pera=$other=0;
    $total = 0;
    $query = mysql_query("SELECT loan FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $loan = $data['loan'];
    
    $exloan = explode("/",$loan);
    for($x = 0;$x < count($exloan); $x++){
        $iexloan = explode("=",$exloan[$x]);
        if($iexloan[1] != 0){
       		if ($iexloan[0] == 1) {
       			$sss= $iexloan[1];

       		}
       		else if ($iexloan[0] == 5) {
       			$phil = $iexloan[1];
       		}
       		else if ($iexloan[0] == 12) {
       			$pera = $iexloan[1];
    		}
    		else
    		{
    			$other = $iexloan[1];
    		}

        	
        }
    }

    return array($total,$sss,$phil,$pera,$other);
} 
function DisplayLoanTitle($loan)
{
// for ($i=0; $i <count($loans) ; $i++) { 
// 		 $query = $this->db->query("SELECT * FROM payroll_loan_config WHERE id='$loans[$i]'");
//          $loantitle = $query->row(0)->description;		
// 	}
		$loans = explode(',',$loan);
		$loanamount = "";
		$title = "";
		for ($i=0; $i <count($loans) ; $i++) { 
			$query =mysql_query("SELECT description FROM payroll_loan_config WHERE id='{$loans[$i]}'");
			$data = mysql_fetch_array($query);
			
			if ($data['description'] != "" || $data['description'] != null ) {
				$loantitle .= "<th class ='head'>".$data['description']."</th>";
				 $loanamount .= "";
				 $title = $data['description'];

			}
			else
			{
				$loantitle = "";
				 $loanamount = "";
			}

		}
		
		// for ($i=0; $i <$query->num_rows() ; $i++) { 
		// 	$rows = $query->$row($i);
		// }
	return array($loantitle,$loanamount,$title);
}


function DisplayDeductionTitle($deduction)
{
// for ($i=0; $i <count($loans) ; $i++) { 
// 		 $query = $this->db->query("SELECT * FROM payroll_loan_config WHERE id='$loans[$i]'");
//          $loantitle = $query->row(0)->description;		
// 	}
		$deductions = explode(',',$deduction);
		$deductionamount = "";
		
		for ($i=0; $i <count($deductions) ; $i++) { 
			$query =mysql_query("SELECT description FROM payroll_deduction_config WHERE id='{$deductions[$i]}'");
			$data = mysql_fetch_array($query);
			
			if ($data['description'] != "" || $data['description'] != null ) {
				$deductiontitle .= "<th class ='head'>".$data['description']."</th>";
				 $deductionamount .= "";
				 

			}
			else
			{
				$deductiontitle = "";
				 $deductionamount = "";
			}

		}
		
		
	return array($deductiontitle,$deductionamount);
}



function displayContribution($eid,$schedule,$quarter,$dfrom,$dto){
    $totsss = $totpagibig = $totphil= '';
    $total = 0;
    $query = mysql_query("SELECT fixeddeduc FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $fixeddeduc = $data['fixeddeduc'];
    
    $exfixeddeduc = explode("/",$fixeddeduc);
    for($x = 0;$x < count($exfixeddeduc); $x++){
        $iexfixeddeduc = explode("=",$exfixeddeduc[$x]);
        if($iexfixeddeduc[1] != 0){
	       if ($iexfixeddeduc[0] == "SSS") {
       			$totsss += $iexfixeddeduc[1];
       		}
       		else if ($iexfixeddeduc[0] == "PHILHEALTH") {
       			$totphil += $iexfixeddeduc[1];
       		}
       		else if ($iexfixeddeduc[0] == "PAGIBIG") {
       			$totpagibig += $iexfixeddeduc[1];
       		}
       		else
       		{
       			$totsss = $totsss;
       			$totphil = $totphil;
       			$totpagibig = $totpagibig;
       		}
        $total = $iexfixeddeduc[1];
        }
    }
    return array($totsss,$totphil,$totpagibig,$total);
}
function DisplayIncomeTitle($income)
{
// for ($i=0; $i <count($loans) ; $i++) { 
// 		 $query = $this->db->query("SELECT * FROM payroll_loan_config WHERE id='$loans[$i]'");
//          $loantitle = $query->row(0)->description;		
// 	}
		$incomes = explode(',',$income);
		$incomeamount = "";
		
		for ($i=0; $i <count($incomes) ; $i++) { 
			$query =mysql_query("SELECT description FROM payroll_income_config WHERE id='{$incomes[$i]}'");
			$data = mysql_fetch_array($query);
			
			if ($data['description'] != "" || $data['description'] != null ) {
				$incometitle .= "<th class ='head'>".$data['description']."</th>";
				 $incomeamount .= "";
				 

			}
			else
			{
				$incometitle = "";
				 $incomeamount = "";
			}

		}
		
		
	return array($incometitle,$incomeamount);
}
function displayIncome($eid,$schedule,$quarter,$dfrom,$dto,$incomes){
    $return = "";
    $total = 0;
    $taxable = 0;
    $a = 0;
    $iamount = $incometotal = 0;
    $query = mysql_query("SELECT * FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $income = $data['income'];
    $ottime = $data['overtime'];
    $selectedincome = explode(',',$incomes);
    $exincome = explode("/",$income);
   

    for($x = 0;$x < count($exincome); $x++)
    {
    	$iexincome = explode("=",$exincome[$x]);
        for ($i=0; $i <count($selectedincome); $i++) { 
        	 
        	if ($iexincome[0] == $selectedincome[$i] ) {
        		$query =mysql_query("SELECT description FROM payroll_income_config WHERE id='{$selectedincome[$i]}'");
        		$data = mysql_fetch_array($query);
        		if ($data['description'] != "" || $data['description'] != null ) {
        			$incomelabel .= "<th class ='head'>".$data['description']."</th>";

        		}
        	}
        }
        
       
    }
    
    return array($return,$total,$taxable,$incomelabel,$iamount);
}
$earningArray = $NotearningArray = $deductionArray = $NotdeductionArray = $otherEarning = array();
$getArraydeduction = $getArrayincome = 0;
$getArraydeduction = explode(',', $deduction);
$getArrayincome = explode(',', $income);
$loancount = 0;
$gross = $this->payroll->SlipRecord($eid,$schedule,$quarter,$dfrom,$dto,$dept,$sort);
foreach ($gross as $row ) {
	// var_dump($row);
 list($dincome,$tincome,$taxableloan,$incomelabel,$incomeamount) = displayIncome($row->employeeid,$schedule,$quarter,$dfrom,$dto,$income);
 list($tsss,$tphill,$tpag,$total) = displayContribution($row->employeeid,$schedule,$quarter,$dfrom,$dto);
 list($total,$sss,$phil,$pera,$other) = displayLoan($row->employeeid,$schedule,$quarter,$dfrom,$dto);
 list($loantitles,$lamount) = DisplayLoanTitle($loan);
 list($incometitles,$iamount) = DisplayIncomeTitle($income);
 list($deductiontitles,$damount)=DisplayDeductionTitle($deduction);
 list($return,$totaldeduction) = displayOthDeduc($row->employeeid,$schedule,$quarter,$dfrom,$dto);
 $totalloanSSS += $sss;
 $totalloanPAGIBIG += $phil;
 $totalloanPERRA += $pera;
 $totalotherDEDUCTION += $totaldeduction;
 $totals += $incomeamount;

 $incomexplode = explode('/',$row->income);
  //IN_ARRAY EARNING
 foreach ($incomexplode as $key) {
 	$incomelastexplode = explode('=',$key);
 	if (in_array($incomelastexplode[0], $getArrayincome) ) {
 		$earningArray[$incomelastexplode[0]] += $incomelastexplode[1];
 	}
 	
 }
//NOT IN_ARRAY EARNING
  foreach ($incomexplode as $key) {
 	$incomelastexplode = explode('=',$key);
 	if (!in_array($incomelastexplode[0], $getArrayincome) ) {
 		$NotearningArray[$incomelastexplode[0]] += $incomelastexplode[1];
 	}
 	
 }

 //IN_ARRAY DEDUCTION
 $otherdeduction = explode('/', $row->otherdeduc);
 foreach ($otherdeduction as $key) {
 	$otherdeductionlast = explode('=',$key);
 	if (in_array($otherdeductionlast[0], $getArraydeduction)) {
 		$deductionArray[$otherdeductionlast[0]] += $otherdeductionlast[1];
 	}
 	
 }

//NOT IN_ARRAY DEDUCTION
  foreach ($otherdeduction as $key) {
 	$otherdeductionlast = explode('=',$key);
 	if (!in_array($otherdeductionlast[0], $getArraydeduction)) {
 		$NotdeductionArray[$otherdeductionlast[0]] += $otherdeductionlast[1];
 	}
 }



 
 //GENERATING LOAN TYPE AMOUNT TOTAL
 $loantypes = explode(',', $loan);
 foreach ($loantypes as $key ) {
 	if ($key == 1) {
 		$lamount .= "<td class='data'>".number_format($totalloanSSS,2)."</td>";
 		
 	}
    if ($key== 5) {
 		$lamount .= "<td class='data'>".number_format($totalloanPAGIBIG,2)."</td>";
 		
 	}
 	if ($key == 12) {
 		$lamount .= "<td class='data'>".number_format($totalloanPERRA,2)."</td>";
 		
 	}

 }


 $earnings   = $row->salary;
 $basicpay += $row->salary;
 $totearnings += $earnings;
 $withholdingtax += $row->withholdingtax;
 $totalSSS +=$tsss;
 $totalphil +=$tphill;
 $totalpagibig +=$tpag;
 $absent += $row->absents;
 


}//END OF SLIP RECORD


$amountotherdeduction = $amountincome = "";
//COUNT ITEM FOR LOAN
$loanexplode = explode(',', $loan);
$countloan = 0;
foreach ($loanexplode as $key) {
	if ($key != "") {
		$countloan++;
	}
	else
	{
		$countloan = 0;
	}


}

$deduct = explode(',', $deduction);
foreach ($deduct as $key) {
	$countdeduct++;
}

//FOR CHOOSEN INCOME BY USER TO FILTER REPORT AND COUNT ITEMS..................

	foreach ($getArrayincome as $key) {
		$countincome++;
		// if ($earningArray[$key] != "" || $earningArray[$key] != null ) {
			$amountincome .= "<td class='data'>".number_format($earningArray[$key],2)."</td>";
		// }
	}


//FOR CHOOSEN DEDUCTION BY USER TO FILTER REPORT AND COUNT ITEMS..................

	foreach ($getArraydeduction as $key) {
			
			 // if ($deductionArray[$key] != "" || $deductionArray[$key] != null) {
				if ($countdeduct == 1 && $deductionArray[$key] == 0) {
					$countotherdeduction = 0;
					$amountotherdeduction .= "";
				}
				else
				{
					$countotherdeduction++;
					$amountotherdeduction .= "<td class='data'>".number_format($deductionArray[$key],2)."</td>";	
				}
				$totalotherdedutionchosen += $deductionArray[$key];
				

}

//GETTING THOSE EARNINGS OR INCOMES THAT WAS NOT IN CHOOSEN DISPLAY ---- POSTED IN BREAKDOWN OF GROSS PAY
foreach ($NotearningArray as $key => $value) {
		$query =mysql_query("SELECT description FROM payroll_income_config WHERE id='{$key}'");
        $data = mysql_fetch_array($query);
        if ($data['description'] != ""  || $data['description'] != null ) {
        $grosspay .="<tr>
        			<td >".$data['description']."</td>
        			<td class='datagrosspay'>".number_format($value,2)."</td>
        			<tr>";
        }
        
        $totalgrosspay += $value;


}
//GETTING THOSE DEDUCTION THAT WAS NOT IN CHOOSEN DISPLAY  ----- POSTED IN BREAKDOWN OF OTHER DEDUCTIONS
foreach ($NotdeductionArray as $key => $value) {

		$query =mysql_query("SELECT description FROM payroll_deduction_config WHERE id='{$key}'");
		$data = mysql_fetch_array($query);
		$breakotherdeduction .="<tr>
        			<td >".$data['description']."</td>
        			<td class='datadeduction'>".number_format($value,2)."</td>
        			<tr>";
        $totaldeductions += $value;
}
// echo $countotherdeduction+ "<br>";
// echo $countloan + "<br>";

$deductionspan = (8  + $countotherdeduction) + $countloan ;

$mpdf = new mPDF('utf-8','A3','10','','3','3','3','10','9','9');
$datas .= "
		<div class='container'>
		<table class='header' >
		<tr>
		<td>Pinnacle Technologies Inc.</td>
		</tr>
		<tr>
		<td>BREAKDOWN OF PAYROLL COMPUTATION</td>
		</tr>
		<tr>
		<td>For the period of ".$month." ".$from." - ".$to.", ".$year." </td>
		</tr>
		</table>	
		<br><br>
		<table class='tbl' border=1>
		
		<tr>
			<th colspan='".$deductionspan ."'>D  E  D  U   C   T  I   O  N  S</th>
		</tr>
		<tr >
			<th></th>
			<th class='head'>GROSS PAY</th>
			<th class='head'>W/HOLDING TAX</th>
			<th class='head'>SSS PREMIUM</th>
			<th class='head'>PHILHEALTH PREMIUM</th>
			<th class='head'>PAG-IBIG PREMIUM</th>
			".$loantitles."
			".$incomelabel."
			".$deductiontitles."
			<th class='head'>OTHER DEDUCTION</th>
			<th class='head'>NET PAY</th>
		</tr>

			
		<tbody>
			<tr>
		    <th > </th>
			<td class='data'>".number_format(($totearnings+ $totalgrosspay)-$absent,2)."</td>
			<td class='data'>".number_format($withholdingtax,2)."</td>
			<td class='data'>".number_format($totalSSS,2)."</td>
			<td class='data'>".number_format($totalphil,2)."</td>
			<td class='data'>".number_format($totalpagibig,2)."</td>
			".$lamount."
		   <!-- ".$amountincome."-->
			".$amountotherdeduction."
			<td class='data'>".number_format($totalotherDEDUCTION-$totalotherdedutionchosen,2)."</td>
			<td class='data'>".number_format(($totearnings+$totalgrosspay)-($withholdingtax+$totalSSS+$totalphil+$totalpagibig+$totalloanPERRA+$totalloanPAGIBIG+$totalloanSSS+$totalotherDEDUCTION),2)."</td>
			</tr>
			<br>
			<br>
			<tr>
			<th class='data'>GRAND TOTAL: </th>
			<td class='data'>".number_format(($totearnings+ $totalgrosspay)-$absent,2)."</td>
			<td class='data'>".number_format($withholdingtax,2)."</td>
			<td class='data'>".number_format($totalSSS,2)."</td>
			<td class='data'>".number_format($totalphil,2)."</td>
			<td class='data'>".number_format($totalpagibig,2)."</td>
			".$lamount."
			<!-- ".$amountincome."-->
			".$amountotherdeduction."
			<td class='data'>".number_format($totalotherDEDUCTION-$totalotherdedutionchosen,2)."</td>
			<td class='data'>".number_format(($totearnings+$totalgrosspay)-($withholdingtax+$totalSSS+$totalphil+$totalpagibig+$totalloanPERRA+$totalloanPAGIBIG+$totalloanSSS + $totalotherDEDUCTION),2)."</td>
			</tr>
		</tbody>
		</table>
		<br><br><br>
		<p>BREAKDOWN OF GROSS PAY :</p>
		<table id='grosspay'  style='border-collapse:collapse;'>
		<tr>
		<td ></td><td class='datagrosspay' ><u>Amount</u></td>
		</tr>
		<tr>
		<td >BASIC PAY</td><td class='datagrosspay' >".number_format($basicpay-$absent,2)."</td>
		</tr>
		".$grosspay."
		<br>
		<tr style='border:1px solid;'>
		<td ><b>Grand Total :</b></td><td class='datagrosspay' ><u>".number_format(($totalgrosspay+$basicpay)-$absent,2)."</u></td>
		</tr>
		</table>

		<br><br><br>
		<p>BREAKDOWN OF OTHER DEDUCTIONS : </p>
		<table id='otherdeduction'  style='border-collapse:collapse;'>
		<tr>
		<td ></td><td class='datadeduction'><u>TOTAL</u></td>
		</tr>
		".$breakotherdeduction."
		<br>
		<tr style='border:1px solid;'>
		<td ><b>Sub-Total :</b></td><td class='datadeduction' ><u>".number_format($totaldeductions,2)."</u></td>
		</tr>
		</table>
		<br><br>
		<table class='tblremarks'>
		<tr><td class='remarks'>Prepared By:</td><td class='remarks'>Checked & Verified by:</td></tr>
		<tr><td class='remarks'></td><td class='remarks'></td></tr>
		
		</table>
		</div>
		";
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
		 width:3%;
		 position:absolute;
		 margin-left:370px;
		 text-align:center;
		 font-size:12px;
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
		 width:90%;

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
		 font-weight:normal;
		 font-size:12px;
		 font-family:times new roman;
		 text-align:center;
		 width:5px;
		}
		.head{
		text-align:center;
	 	font-size:12px;
	  	border:1px solid;	
		}
		</style>
		<body>".$datas."</body>
		";

 // echo $html;
 $mpdf->WriteHTML($html);
 $mpdf->Output();
 

?>
