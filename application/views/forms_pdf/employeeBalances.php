<?php

/**
 * @author Justin
 * @copyright 2016
 */
require_once(APPPATH."constants.php");
// $division  = ($_GET['division'] ? $_GET['division'] : $_POST['division']);
// $department  = ($_GET['department'] ? $_GET['department'] : $_POST['department']);
// $cutoff  = ($_GET['cutoff'] ? $_GET['cutoff'] : $_POST['cutoff']);
// $deduction  = ($_GET['deduction'] ? $_GET['deduction'] : $_POST['deduction']);
// $type = "";
$CI =& get_instance();
$sss = $pag = $phil = "";
$code=$startdate =$enddate= "";
$stats = $status;
// if($deduction == "SSS"){$code = "1";}
// elseif($deduction == "PHILHEALTH"){$code = "4";}
// elseif($deduction == "PAGIBIG"){$code = "5";}

if ($deduction) {
	$query = $this->db->query("SELECT id,description FROM payroll_deduction_config  WHERE id ='$deduction' ORDER by ID LIMIT 1");
	foreach ($query->result() as $key) {
	   $code = $key->id;
	}
}
elseif ($loandeduction) {
	$query = $this->db->query("SELECT id,description FROM payroll_loan_config ORDER by ID");
	foreach ($query->result() as $key) {
	   $code = $key->id;
	}
}


 // Translates date into words
	$explodecutoff = explode(',',$cutoff );
	$cutoffstart = $explodecutoff[0];
	$cutoffend = $explodecutoff[1];
	$dateInWords = $cutoffend?date("F d, Y", strtotime($cutoffend)):"ALL DATES";

   
 // FILTER FOR CUTOFF
 	$getCutoffid = $CI->db->query("SELECT id FROM cutoff WHERE CutoffFrom='$cutoffstart' AND CutoffTo='$cutoffend'");
 	if ($getCutoffid->num_rows() > 0) {
 		$id = $getCutoffid->row(0)->id;
 		$getPayrollCutoff = $CI->db->query("SELECT startdate, enddate FROM payroll_cutoff_config WHERE baseid='$id'");

 		if ($getPayrollCutoff->num_rows() > 0){
 			$startdate = $getPayrollCutoff->row(0)->startdate;
 			$enddate = $getPayrollCutoff->row(0)->enddate;
 		}
 	}
//getLoanDescription
$loanConfig = array();
$getIncomequery = $CI->db->query("SELECT id,description FROM payroll_loan_config ")->result();
foreach ($getIncomequery as $key => $value) {
	$loanConfig[$value->id] = $value->description;
}



$pdf = new mpdf('P','LETTER','','UTF-8',5,5,8,5);
$info = "  <style>
                @page{            
                    /*margin-top: 4.35cm;*/
                    margin-top: 3cm;
                    odd-header-name: html_Header;
                    odd-footer-name: html_Footer;
                }  
                .content{
                    height: 100%;
                    margin-top: 15px;
                }
                table{
                    border-collapse: collapse;
                    font-size: 12px;
                    border-spacing: 5px;
                }
                .content-header{
                    text-align: center;
                    font-size: 12px;
                }
                .content-body{
                    border: 1px solid black;
                    padding-top: 8px;
                    padding-bottom: 8px;
                    padding-left: 8px;
                }
				.title td
				{
					text-align:center;
					font-weight:bold;
				}

            </style>";

$info .= "
<htmlpageheader name='Header'>
    <div>
        <table width='50%' style='padding: 0;' >
            <tr>
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo.jpg' style='width: 70px;' /></td>
                <td valign='bottom' width='300px' style='text-align: left;'><span style='font-size: 15px;color:blue'><b>".strtoupper($SCHOOL_NAME)."</b></span></td>
                
            </tr>
            <tr>
                <td valign='top' style='padding-right: 50px;text-left: center;'><span style='font-size: 10px;color:blue'>Employee Balances As of " . $stats . "</span></td>
            </tr>
        </table>
    </div>
	
</htmlpageheader>";
$no = 1;
$old_empID = $date='';
$codeloan = "";
$end_table = false ;
$i = 0;

//PER EMPLOYEE AND SUMMARY
if ($format == "perEmp" && $viewtype == "summary") {
	$data = $this->reports->loademployeeDeduction($startdate,$enddate,$employeeid,$code);
		if (count($data) > 0) {
			// echo count($data);
			foreach ($data as $key) {
			if($old_empID != $key->employeeid){
				$number = 1;
				$end_table = true;
				if($i > 0) $info .="</tbody></table><br><br>";

				$old_empID = $key->employeeid; 
				$info .="<div style='font-weight:bold'>".$no++.".".$key->lname.",".$key->fname." ".$key->mname."</div>";
				$info .="<table width='100%' border=1>
				<head><tr class='title'>
				<td>#</td>
				<td width='30%'>Loan Name</td>
				<td>Total Loan</td>
				<td>Balance</td>
				</tr>
				</thead>
				<tbody>";
			}

			$info .= "<tr class='title'>
				<td>".$number++."</td>
				<td>".$loanConfig[$key->code_loan]."</td>
				<td>".$key->startBalance."</td>
				<td>".$key->remainingBalance."</td>
				</tr>";
			// $end_table = false;

			# closing last table
			$i += 1;
			}
			 $info .="</tbody></table><br><br>";
		
			}
			else
			{
			 $info.="<div style='border:2px solid red;text-align:center'><b> NO RECORD FOUND!</b></div>";
			}

	

}
//PER DEDUCTION AND SUMMARY
else if ($format == "perDed" && $viewtype =="summary") {
	$alldata = array();
	$data = $this->reports->loademployeeDeduction($startdate,$enddate,$employeeid,$code);
	if (count($data) > 1) {
		foreach ($loanConfig as $key => $description) {
		$number = 1;
		$info .="<div style='font-weight:bold'>".$description."</div>";	
		$info .="<table width='100%' border=1>
			<head><tr class='title'>
			<td>#</td>
			<td width='30%'>Employee Name</td>
			<td>Total Loan</td>
			<td>Balance</td>
			</tr>
			</thead>
			<tbody>";
		foreach ($data as $keys ) {
			if ($keys->code_loan  == $key) {
				$info .= "<tr class='title'>
					<td>".$number++."</td>
					<td>".$keys->lname.",".$keys->fname." ".$keys->mname."</td>
					<td>".$keys->startBalance."</td>
					<td>".$keys->remainingBalance."</td>
					</tr>";
				}
			}
				$info .="</table><br><br>";
		}
	}
	else
	{
	 $info.="<div style='border:2px solid red;text-align:center'><b> NO RECORD FOUND!</b></div>";
	}
}
else if ($format=='perEmp' && $viewtype =='detailed') {
	$data = $this->reports->loademployeeDeductionDetailed($stats,$startdate,$enddate,$employeeid,$code);

		if (count($data) > 1) {
			foreach ($data as $key) {
			if($old_empID != $key->employeeid){
				$number = 1;
				$end_table = true;
				if($i > 0) $info .="</tbody></table><br><br>";

				$old_empID = $key->employeeid; 
				$info .="<div style='font-weight:bold'>".$no++.".".$key->lname.",".$key->fname." ".$key->mname."</div>";
				$info .="<table width='100%' border=1 style='border-collapse:collapse'>
				<head><tr class='title'>
				<td>#</td>
				<td width='30%'>Loan Name</td>
				<td>Total Loan</td>
				<td>Payment Date</td>
				<td>Amount</td>
				<td>Balance</td>
				</tr>
				</thead>
				<tbody>";
			}

			$info .= "<tr class='title'>
				";
				$totalloans=0;
				
			if ($loanConfig[$key->code_loan] != $codeloan) 
			 {	//$totalloan = $key->startBalance;
			// 	$totalloans += $key->startBalance + $totalloan;
				$date = date('m/d/Y',strtotime($key->timestamp));
				$codeloan = $loanConfig[$key->code_loan];
				$totalloans = $key->startBalance;
				$info .="<td>".$number++."</td>
				<td>".$loanConfig[$key->code_loan]."</td>";
				$info .="<td>".$totalloans."</td>";
				
			}
			else{
				$info .="<td></td><td></td><td></td>";
				}
				$info .="<td>".date('m/d/Y',strtotime($key->timestamp))."</td>
				<td>".$key->amount."</td>
				<td>".$key->remainingBalance."</td>
				</tr>";
			// $end_table = false;

			# closing last table
			$i += 1;
			}
			 $info .="</tbody></table><br><br>";
			}
			else
			{
			 $info.="<div style='border:2px solid red;text-align:center'><b> NO RECORD FOUND!</b></div>";
			}


}
else
{
	$info.="<div style='border:2px solid red;text-align:center'><b> NO RECORD FOUND!</b></div>";
}

	#$info .="</tbody></table><br><br>";
/*$info .="<div>".$no++.".".$key->lname.",".$key->fname." ".$key->mname."</div>";
$info .="<table width='100%' border=1>
		<head><tr class='title'>
		<td>#</td>
		<td width='30%'>Loan Name</td>
		<td>Total Loan</td>
		<td>Balance</td>
		</tr>
		</thead>
		<tbody>
		<tr class='title'>
		<td>".$number++."</td>
		<td>".$loanConfig[$key->code_loan]."</td>
		<td>".$key->startBalance."</td>
		<td>".$key->remainingBalance."</td>
		</tr>
		</body>
		";

$info .="</table><br><br>";	*/






// echo $this->db->last_query();
// echo $info;
$pdf->WriteHTML($info);
$pdf->Output();
?>