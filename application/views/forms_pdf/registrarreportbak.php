<?php
ini_set('memory_limit',-1);
set_time_limit(0);
/**
Modified by : Glen Mark 2017
**/
$CI =& get_instance();
$CI->load->model('payrollprocess');
$CI->load->model('payroll');
include "application/config/connection.php";
include "application/views/forms_pdf/function/payrollfunc.php";



function formatAmount($amount=''){
    if($amount){
        if($amount < 0) {
            $amount = $amount * -1;
            $amount = number_format( $amount, 2 );
            $amount = '(' . $amount . ')';
        }else{
            $amount = number_format( $amount, 2 );
        }
    }else{
        $amount = '0.00';
    }
    return $amount;
}


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

///< ------------------------------ fixed deduction config ----------------------------------------------------
$fixeddeduc_config_q = $CI->db->query("SELECT code_deduction,description FROM deductions");
$arr_fixeddeduc_config = $CI->payrollprocess->constructArrayListFromStdClass($fixeddeduc_config_q,'code_deduction','description');

///< ------------------------------ income config ------------------------------------------------------------
$income_config_q = $CI->payroll->displayIncome();
$arr_income_config =$CI->payrollprocess->constructArrayListFromStdClass($income_config_q,'id','description');

///< ------------------------------ other deduction config ----------------------------------------------------------
$deduction_config_q = $CI->payroll->displayDeduction();
$arr_deduc_config = $CI->payrollprocess->constructArrayListFromStdClass($deduction_config_q,'id','description');

///< ------------------------------ loan config ---------------------------------------------------------------
$loan_config_q = $CI->payroll->displayLoan();
$arr_loan_config = $CI->payrollprocess->constructArrayListFromStdClass($loan_config_q,'id','description');


$arr_income_adj_config = $arr_income_config;
$arr_income_adj_config['SALARY'] = array('description'=>'SALARY','hasData'=>0);

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
$campus       = $_GET['campus'];
$status 	=$_GET['status'];
$totearnings = $earnings = $totalSSS=$totalphil=$totalpagibig= "";
$emplist    = isset($_GET['emplist'])?$_GET['emplist']:"";

$mpdf = new mPDF('utf-8','A4-L','10','','3','3','33','10','9','9');
$mpdf->SetHTMLHeader("<table class='header'>
		<tr>
		<td><img src='images/school_logo.jpg' style='width: 80px;'/></td>
		<td style='color:blue;'><b style='align_center'><h1>Pinnacle Technologies Inc.</h1></b>
		<b>PAYROLL SHEET FOR SALARY SCHEDULE : ".$month." ".$from." - ".$to.", ".$year." </b>
		
		</tr>
		</table>",'',false);
// all datas
$empids = array();
	$gross = $this->payroll->PayrollRegistrar($eid,$schedule,$quarter,$dfrom,$dto,$dept,$campus,$sort,$status);
	
	foreach ($gross as $row) {
		$employeeIncome = $employeeDeduction = $employeeOtherDeduction = $employeeLoan= $employeeAdjustment = array(); 
		$totalDeduction = $totalIncome = $totalOthDeduction= $totalemployeeLoan = $totalemployeeAdjustment = $netpay = 0;
			if ($emplist) {
				$emp = explode(',', $emplist);
							foreach ($emp as $empid ) {
								if ($row->employeeid == $empid) {
									$income = $row->income;
									$deduction = $row->fixeddeduc;
									$othDeduction = $row->otherdeduc;
									$overtime = $row->overtime;
									$loan = $row->loan;
									$adjustmentIncome = $row->income_adj;

											//< income
											$income_arr 				= $CI->payrollprocess->constructArrayListFromComputedTable($income);
											foreach ($income_arr as $k => $v) {$arr_income_config[$k]['hasData'] = 1;}
											
											///< fixed deduc
									        $fixeddeduc_arr =  $CI->payrollprocess->constructArrayListFromComputedTable($deduction);
									        foreach ($fixeddeduc_arr as $k => $v) {$arr_fixeddeduc_config[$k]['hasData'] = 1;}
									        $loan_arr = $CI->payrollprocess->constructArrayListFromComputedTable($loan);
									        foreach ($loan_arr as $k => $v) {$arr_loan_config[$k]['hasData'] = 1;}

									        ///< deduc
									        $deduc_arr =  $CI->payrollprocess->constructArrayListFromComputedTable($othDeduction);
									        foreach ($deduc_arr as $k => $v) {$arr_deduc_config[$k]['hasData'] = 1;}


									        $income_adj_arr 				= $CI->payrollprocess->constructArrayListFromComputedTable($adjustmentIncome);
									        foreach ($income_adj_arr as $k => $v) {$arr_income_adj_config[$k]['hasData'] = 1;}

									//income
									$explodeIncome = explode("/", $income);
									for ($i=0; $i <count($explodeIncome); $i++) 
									{ 
										$finalIncome = explode("=",$explodeIncome[$i]);
										$employeeIncome[$finalIncome[0]] = round($finalIncome[1],2);
										$totalIncome += $finalIncome[1];
									}
									//deduction
									$explodeFixeddeduc = explode("/", $deduction);
									for ($d=0; $d <count($explodeFixeddeduc) ; $d++) { 
										$finalDeduction = explode("=",$explodeFixeddeduc[$d]);
										$employeeDeduction[$finalDeduction[0]] = round($finalDeduction[1],2);
										$totalDeduction += $finalDeduction[1];
									}
									//other deduction
									$explodeOthDeduction = explode('/', $othDeduction);
									for ($a=0; $a <count($explodeOthDeduction); $a++) { 
										$finalOthDeduction = explode("=",$explodeOthDeduction[$a]);
										$employeeOtherDeduction[$finalOthDeduction[0]] = round($finalOthDeduction[1],2);
										$totalOthDeduction += $finalOthDeduction[1];
									}
									//Loan
									$explodeLoan = explode('/', $loan);
									for ($l=0; $l <count($explodeLoan) ; $l++) 
									{ 
										$finalLoan = explode("=",$explodeLoan[$l]);
										$employeeLoan[$finalLoan[0]] = round($finalLoan[1],2);
										$totalemployeeLoan += $finalLoan[1];

									}
									//income adj
									$incomeadj = explode('/', $adjustmentIncome);
									for ($l=0; $l <count($incomeadj) ; $l++) 
									{ 
										$finalincomeadj = explode("=",$incomeadj[$l]);
										$employeeAdjustment[$finalincomeadj[0]] = round($finalincomeadj[1],2);
										$totalemployeeAdjustment += $finalincomeadj[1];

									}
									$netpay = round($row->net,2);

									
							$empids[] = array("fullname" => $row->fullname,"department"=>$row->deptid,"empid" => $row->employeeid,"tardy"=>round($row->tardy,2),"absent"=>round($row->absents,2),"salary"=>round($row->salary,2),"income"=>$employeeIncome,"deduction"=>$employeeDeduction,"gross"=>$row->gross,"totalDeduction"=>$totalDeduction,"fixeddeduc"=>$employeeOtherDeduction,"tax" => $row->withholdingtax,"OAD" => ($totalDeduction + $totalOthDeduction + $row->withholdingtax),"netpay" => round($netpay,2),'loan'=>$employeeLoan,'totalLoan'=>$totalemployeeLoan,'netbasicpay'=>$row->netbasicpay,'adjustment'=>$employeeAdjustment,'totaladjustment'=>$totalemployeeAdjustment,'overtime'=>$overtime);
								}
							}
			}
			else
			{
						$income = $row->income;
						$deduction = $row->fixeddeduc;
						$othDeduction = $row->otherdeduc;
						$overtime = $row->overtime;
						$loan = $row->loan;
						$adjustmentIncome = $row->income_adj;

								//< income
								$income_arr 				= $CI->payrollprocess->constructArrayListFromComputedTable($income);
								foreach ($income_arr as $k => $v) {$arr_income_config[$k]['hasData'] = 1;}
								
								///< fixed deduc
						        $fixeddeduc_arr =  $CI->payrollprocess->constructArrayListFromComputedTable($deduction);
						        foreach ($fixeddeduc_arr as $k => $v) {$arr_fixeddeduc_config[$k]['hasData'] = 1;}

						        ///< deduc
						        $deduc_arr =  $CI->payrollprocess->constructArrayListFromComputedTable($othDeduction);
						        foreach ($deduc_arr as $k => $v) {$arr_deduc_config[$k]['hasData'] = 1;}

						        $loan_arr = $CI->payrollprocess->constructArrayListFromComputedTable($loan);
						        foreach ($loan_arr as $k => $v) {$arr_loan_config[$k]['hasData'] = 1;}

						        $income_adj_arr 				= $CI->payrollprocess->constructArrayListFromComputedTable($adjustmentIncome);
						        foreach ($income_adj_arr as $k => $v) {$arr_income_adj_config[$k]['hasData'] = 1;}

						//income
						$explodeIncome = explode("/", $income);
						for ($i=0; $i <count($explodeIncome); $i++) 
						{ 
							$finalIncome = explode("=",$explodeIncome[$i]);
							$employeeIncome[$finalIncome[0]] = round($finalIncome[1],2);
							$totalIncome += $finalIncome[1];
						}
						//deduction
						$explodeFixeddeduc = explode("/", $deduction);
						for ($d=0; $d <count($explodeFixeddeduc) ; $d++) { 
							$finalDeduction = explode("=",$explodeFixeddeduc[$d]);
							$employeeDeduction[$finalDeduction[0]] = round($finalDeduction[1],2);
							$totalDeduction += $finalDeduction[1];
						}
						//other deduction
						$explodeOthDeduction = explode('/', $othDeduction);
						for ($a=0; $a <count($explodeOthDeduction); $a++) { 
							$finalOthDeduction = explode("=",$explodeOthDeduction[$a]);
							$employeeOtherDeduction[$finalOthDeduction[0]] = round($finalOthDeduction[1],2);
							$totalOthDeduction += $finalOthDeduction[1];
						}
						//Loan
						$explodeLoan = explode('/', $loan);
						for ($l=0; $l <count($explodeLoan) ; $l++) 
						{ 
							$finalLoan = explode("=",$explodeLoan[$l]);
							$employeeLoan[$finalLoan[0]] = round($finalLoan[1],2);
							$totalemployeeLoan += $finalLoan[1];

						}
						//income adj
						$incomeadj = explode('/', $adjustmentIncome);
						for ($l=0; $l <count($incomeadj) ; $l++) 
						{ 
							$finalincomeadj = explode("=",$incomeadj[$l]);
							$employeeAdjustment[$finalincomeadj[0]] = round($finalincomeadj[1],2);
							$totalemployeeAdjustment += $finalincomeadj[1];

						}
						$netpay = round($row->net,2);

						
				$empids[] = array("fullname" => $row->fullname,"department"=>$row->deptid,"empid" => $row->employeeid,"tardy"=>$row->tardy,"absent"=>$row->absents,"salary"=>$row->salary,"income"=>$employeeIncome,"deduction"=>$employeeDeduction,"gross"=>$row->gross,"totalDeduction"=>$totalDeduction,"fixeddeduc"=>$employeeOtherDeduction,"tax" => round($row->withholdingtax,2),"OAD" => ($totalDeduction + $totalOthDeduction + $row->withholdingtax),"netpay" => $netpay,'loan'=>$employeeLoan,'totalLoan'=>$totalemployeeLoan,'netbasicpay'=>$row->netbasicpay,'adjustment'=>$employeeAdjustment,'totaladjustment'=>$totalemployeeAdjustment,'overtime'=>$overtime);
			}
			


					
		}
		// echo count($empids);die;
		// echo '<pre>';print_r($empids);die;

		

foreach ($arr_income_config as $key => $val) {
    if($val['hasData'] == 0) unset($arr_income_config[$key]);
}
foreach ($arr_fixeddeduc_config as $key => $val) {
    if($val['hasData'] == 0) unset($arr_fixeddeduc_config[$key]);
}
foreach ($arr_loan_config as $key => $val) {
    if($val['hasData'] == 0) unset($arr_loan_config[$key]);
}
foreach ($arr_deduc_config as $key => $val) {
    if($val['hasData'] == 0) unset($arr_deduc_config[$key]);
}

foreach ($arr_income_adj_config as $key => $val) {
    if($val['hasData'] == 0) unset($arr_income_adj_config[$key]);
}
	
$interval = 25;
$column = 4;
//for income variables
$totalsalary = $totalabsent = $grandbasicpay = $totalbasicpay = $totaltardy = $totalgross = $grandtotalsalary = $grandtotalabsent = $grandtotaltardy = $grandtotalgross = $grandtotaltax = $grandtotalEmpDeduction = $grandtotalnet = $totalovertime = $grandtotalovertime=  0;
$incomecode = $deductioncode =  "";
$empid = $subtotal = $grandtotal = array();
//for deduction variables
$totaltax = $totalEmpDeduction = $totalnet= 0;
//start main loop
for($i=0;$i<=sizeof($empids);$i += $interval) 
{ 
	
	$content .= "
				<span><b>Breakdown of Income </b></span>
				<hr></hr>
				<table class='data' border=1 width='100%'  >
					<thead>
					<tr class='headers'>
						<td >No.</td>
						<td>ID</td>
						<td width='10%'>NAME OF EMPLOYEE</td>
						<td>Salary</td>
						<td>Tardy</td>
						<td>Absent</td>
						<td>Net Basic Pay</td>
				";

	foreach ($arr_income_config as $code => $description)
	{
	$content .="<td>".$incomeConfig[$code]."</td>";
	}
	// echo '<pre>';print_r($arr_income_adj_config['SALARY']);
	foreach ($arr_income_adj_config as $code => $description)
	{
	$content .="<td>".$incomeConfig[$code]." ADJ</td>";
	}
	$content .="<td>OVERTIME</td><td>GROSS EARNINGS</td></tr></thead>";
		$breakincome = $i + $interval;
		//start first loop
		for ($k=$i;$k<=$breakincome;$k++) 
		{ 	
			if ($k == sizeof($empids) + 1) {

				break;

			}
			else
			{		
				if ($incomecode != $empids[$k]['department']) {
					if ($incomecode) {
						$content .="<tr><td  height='5' colspan='3' class='dept'>TOTAL</td><td>".number_format($totalsalary,2)."</td>";
						$content .="<td>".number_format($totaltardy,2)."</td><td>".number_format($totalabsent,2)."</td>";
						$content .="<td>".number_format($totalbasicpay,2)."</td>";
						foreach ($arr_income_config as $code => $descriptions) {
							if (isset($subtotal['income'][$code])) 
								{
							$content .="<td>".$subtotal['income'][$code]."</td>";	
								}
							else
								{
							$content .="<td>0.00</td>";
								}
						}
						foreach ($arr_income_adj_config as $code => $descriptions) {
							if (isset($subtotal['adjustment'][$code])) 
								{
							$content .="<td>".formatAmount($subtotal['adjustment'][$code])."</td>";	
								}
							else
								{
							$content .="<td>0.00</td>";
								}
						}
						$content .="<td>".$totalovertime."</td><td>".number_format($totalgross,2)."</td>";
						$content .="</tr>";

					$content .="<tr  stlye='border:0px' border=0px><td border=0 height='7' colspan='".(sizeof($arr_income_config) + sizeof($arr_income_adj_config) +9)."'></td></tr>";
					}
					$content.="<tr><td height='15' class='dept' colspan='".(sizeof($arr_income_config)+ sizeof($arr_income_adj_config)+9) ."'>".$empids[$k]['department']."</td></tr>";
					$incomecode = $empids[$k]['department'];
					$totalsalary = $totalabsent = $totaltardy = $totalgross = $totalbasicpay = 0;
					$subtotal = array();

					
				}

				$grandtotalsalary += round($empids[$k]['salary'],2); 
				$grandtotalabsent += $empids[$k]['absent'];	
				$grandtotaltardy += $empids[$k]['tardy'];
				$grandtotalgross += $empids[$k]['gross'];
				$grandbasicpay += $empids[$k]['netbasicpay'];	
				$grandtotalovertime += $empids[$k]['overtime'];
				$totalsalary += round($empids[$k]['salary']);
				$totalgross  += $empids[$k]['gross'];
				$totaltardy  += $empids[$k]['tardy'];	
				$totalabsent  += $empids[$k]['absent'];	
				$totalbasicpay += $empids[$k]['netbasicpay'];
				$totalovertime += $empids[$k]['overtime'];

				if ($incomecode) {
						$content .= "<tr>";
						$content .="<td>".round($k+1)."</td>
						<td >".$empids[$k]['empid']."</td>
						<td>".$empids[$k]['fullname']."</td>
						<td>".number_format($empids[$k]['salary'],2)."</td>
						<td>".number_format($empids[$k]['tardy'],2)."</td>
						<td>".number_format($empids[$k]['absent'],2)."</td>
						<td>".number_format($empids[$k]['netbasicpay'],2)."</td>";
						foreach ($arr_income_config as $code => $descriptions) 
						{
							if (isset($empids[$k]["income"][$code])) 
							{

								$content .="<td>".$empids[$k]["income"][$code]."</td>";	
							}
							else
							{
								$content .="<td>0.00</td>";
							}
							//getting subtotal
							if (isset($subtotal['income'][$code])) {

								$subtotal['income'][$code] += $empids[$k]["income"][$code];
							}
							else
							{
								$subtotal['income'][$code] = $empids[$k]["income"][$code];	
							}
							//getting grandtotal

							if (!array_key_exists('income', $grandtotal)) $grandtotal['income']=array();
							if (!array_key_exists($code, $grandtotal['income'])) $grandtotal['income'][$code]=0;
							$grandtotal['income'][$code] += number_format($empids[$k]["income"][$code],2,".","");
							/**
							if (isset($grandtotal['income'][$code])) {
								$grandtotal['income'][$code] += number_format($empids[$k]["income"][$code],2,".","");
							}
							else
							{
								$grandtotal['income'][$code] = number_format($empids[$k]["income"][$code],2,".","");	
							}
							*/

						}

						
							foreach ($arr_income_adj_config as $code => $descriptions) {
							if (isset($empids[$k]["adjustment"][$code])) 
							{

								$content .="<td>".formatAmount($empids[$k]["adjustment"][$code])."</td>";	
							}
							else
							{
								$content .="<td>0.00</td>";
							}
							//getting subtotal
							if (isset($subtotal['adjustment'][$code])) {
								$subtotal['adjustment'][$code] += $empids[$k]["adjustment"][$code];
							}
							else
							{
								$subtotal['adjustment'][$code] = $empids[$k]["adjustment"][$code];	
							}
							//getting grandtotal
							if (isset($grandtotal['adjustment'][$code])) {
								$grandtotal['adjustment'][$code] += $empids[$k]["adjustment"][$code];
							}
							else
							{
								$grandtotal['adjustment'][$code] = $empids[$k]["adjustment"][$code];	
							}
						}
					$content .="<td>".$empids[$k]['overtime']."</td><td>".number_format($empids[$k]["gross"],2)."</td></tr>";	
	
				}
			}		
		}
	
$content.="</table>"; //end of first loop




$no = 1;
$content .="<pagebreak><b>Breakdown of Deduction</b></span>
<hr></hr>
<table class='data' border=1  width='100%'  >
	<thead>
	<tr class='headers'>
		<td rowspan='2'>No.</td>
		<td rowspan='2'>ID</td>
		<td rowspan='2' width='10%'>NAME OF EMPLOYEE</td>
		<td rowspan='2'>WITHOLDINGTAX</td>";
if (sizeof($arr_fixeddeduc_config) > 0) 
{

		$content .="<td colspan='".sizeof($arr_fixeddeduc_config)."'>CONTRIBUTION</td>";

}
		
foreach ($arr_deduc_config as $code => $codedescription) {
$content .="<td rowspan='2'>".$codedescription['description']."</td>";
			}
foreach ($arr_loan_config as $code => $codedescription) {
$content .="<td rowspan='2'>".$codedescription['description']."</td>";
			}
$content .="<td rowspan='2'>TOTAL DEDUCTION</td>
		<td rowspan='2'>NET PAY</td>
		</tr>

		<tr class='headers'>";
foreach ($arr_fixeddeduc_config as $code => $codedescription)
		 {
$content .="<td>".$codedescription['description']."</td>";
		}
$content .="</tr></thead>";
//start for second loop
		$breakdeduction = $i + $interval;
		for ($d=$i;$d<=$breakdeduction;$d++) { 
			if ($d == sizeof($empids) + 1) {
				break;
			}
			
			else
			{	
				if ($deductioncode != $empids[$d]['department']) {
					if ($deductioncode) {
						$content .="<tr><td  height='5' colspan='3' class='dept'>TOTAL</td><td>".number_format($totaltax,2)."</td>";
						foreach ($arr_fixeddeduc_config as $code => $descriptions) {
						
							if (isset($subtotal['deduction'][$code])) 
								{
							$content .="<td>".number_format($subtotal['deduction'][$code],2)."</td>";	
								}
							else
								{
							$content .="<td>0.00</td>";
								}
						}

						foreach ($arr_deduc_config as $code => $descriptions) {
							if (isset($subtotal['otherdeduction'][$code])) 
								{
							$content .="<td>".number_format($subtotal['otherdeduction'][$code],2)."</td>";	
								}
							else
								{
							$content .="<td>0.00</td>";
								}
						}

						foreach ($arr_loan_config as $code => $descriptions) {
							if (isset($subtotal['loan'][$code])) 
								{
							$content .="<td>".number_format($subtotal['loan'][$code],2)."</td>";	
								}
							else
								{
							$content .="<td>0.00</td>";
								}
						}
						$content .="<td>".number_format($totalEmpDeduction,2)."</td>";
						$content .="<td>".number_format($totalnet,2)."</td>";
						$content .="</tr>";
						$content .="<tr  style='border:0px' border=0px><td border=0 height='7' colspan='".(sizeof($arr_deduc_config)+sizeof($arr_fixeddeduc_config) + sizeof($arr_loan_config) + 6)."'></td></tr>";
					}
					$content .= "<tr>";
					$content.="<td height='15' class='dept' colspan='".(sizeof($arr_deduc_config)+sizeof($arr_fixeddeduc_config) + sizeof($arr_loan_config)+ 6) ."'>".$empids[$d]['department']."</td></tr>";
					$deductioncode = $empids[$d]['department'];
					$totaltax = $totalEmpDeduction = $totalnet=0;
					$subtotal = array();

					
				}
				$totaltax += $empids[$d]['tax'];
				$totalEmpDeduction +=$empids[$d]['OAD'];
				$totalnet += $empids[$d]['netpay'];
				$grandtotaltax += $empids[$d]['tax'];
				$grandtotalEmpDeduction +=$empids[$d]['OAD'];
				$grandtotalnet += $empids[$d]['netpay'];
				if ($deductioncode) {
					$content .="<tr>
												<td>".round($d+1)."</td>
												<td >".$empids[$d]['empid']."</td>
												<td>".$empids[$d]["fullname"]."</td>";
									if (isset($empids[$d]['tax']))
									{

										$content .="<td>".number_format($empids[$d]['tax'],2)."</td>";	
									}
									else
									{
										$content .="<td>0.00</td>";
									}
									foreach ($arr_fixeddeduc_config as $code => $description) 
									{
										if (isset($empids[$d]['deduction'][$code])) 
												{
													$content .="<td>".$empids[$d]['deduction'][$code]."</td>";
												}
												else
												{
													$content .= "<td>0.00</td>";			
												}
										//getting subtotal
										if (isset($subtotal['deduction'][$code]))
												{
													$subtotal['deduction'][$code] += $empids[$d]['deduction'][$code]; 
												}
												else
												{
													$subtotal['deduction'][$code] = $empids[$d]['deduction'][$code]; 
												}
										//getting grandtotal
										if (isset($grandtotal['deduction'][$code]))
												{
													$grandtotal['deduction'][$code] += $empids[$d]['deduction'][$code]; 
												}
												else
												{
													$grandtotal['deduction'][$code] = $empids[$d]['deduction'][$code]; 
												}
												
									}
									
									foreach ($arr_deduc_config as $code => $description) 
									{
										 if (isset($empids[$d]['fixeddeduc'][$code])) 
										 	{
												$content.="<td>".number_format($empids[$d]['fixeddeduc'][$code],2)."</td>";
											}
										else
											{
												$content .="<td>0.00</td>";
											}
										if (isset($subtotal['otherdeduction'][$code]))
												{
													$subtotal['otherdeduction'][$code] += $empids[$d]['fixeddeduc'][$code]; 
												}
												else
												{
													$subtotal['otherdeduction'][$code] = $empids[$d]['fixeddeduc'][$code]; 
												}
										if (isset($grandtotal['otherdeduction'][$code]))
												{
													$grandtotal['otherdeduction'][$code] += $empids[$d]['fixeddeduc'][$code]; 
												}
												else
												{
													$grandtotal['otherdeduction'][$code] = $empids[$d]['fixeddeduc'][$code]; 
												}	
									}

									foreach ($arr_loan_config as $code => $description) 
									{
										 if (isset($empids[$d]['loan'][$code])) 
										 	{
												$content.="<td>".number_format($empids[$d]['loan'][$code],2)."</td>";
											}
										else
											{
												$content .="<td>0.00</td>";
											}
										if (isset($subtotal['loan'][$code]))
												{
													$subtotal['loan'][$code] += $empids[$d]['loan'][$code]; 
												}
												else
												{
													$subtotal['loan'][$code] = $empids[$d]['loan'][$code]; 
												}
										if (isset($grandtotal['loan'][$code]))
												{
													$grandtotal['loan'][$code] += $empids[$d]['loan'][$code]; 
												}
												else
												{
													$grandtotal['loan'][$code] = $empids[$d]['loan'][$code]; 
												}	
									}
									$content .="<td>".number_format($empids[$d]['OAD'],2)."</td>
												<td>".number_format($empids[$d]['netpay'],2)."</td>
											</tr>";
				}
				
				}
		}//end of second loop
				$content .="</table><pagebreak>";

			

	
}//end of main looping.//

//GRAND TOTAL FOR INCOME
$content .="<div>GRAND TOTAL FOR INCOME</div><table border=1 width='100%'  class='data'>
			<thead>
			<tr  class='headers'>
			<td>Salary</td>
			<td>Tardy</td>
			<td>Absent</td>
			<td>Net Basic Pay</td>";
foreach ($arr_income_config as $code => $codedescription)
{

$content .="<td>".$codedescription['description']."</td>";
}

foreach ($arr_income_adj_config as $code => $codedescription)
{
$content .="<td>".$codedescription['description']." ADJ</td>";
}
$content .="<td>OVERTIME</td><td>GROSS EARNINGS</td></tr></thead>";
$content.="<tbody><tr><td>".number_format($grandtotalsalary,2)."</td>";
$content .="<td>".number_format($grandtotaltardy,2)."</td><td>".number_format($grandtotalabsent,2)."</td>";
$content .="<td>".number_format($grandbasicpay,2)."</td>";
	foreach ($arr_income_config as $code => $descriptions) {
						
		if (isset($grandtotal['income'][$code])) 
		{
			$content .="<td>".number_format($grandtotal['income'][$code],2)."</td>";	
		}
		else
		{
			$content .="<td>0.00</td>";
		}
	}
	foreach ($arr_income_adj_config as $code => $descriptions) {
						
		if (isset($grandtotal['adjustment'][$code])) 
		{
			$content .="<td>".formatAmount($grandtotal['adjustment'][$code])."</td>";	
		}
		else
		{
			$content .="<td>0.00</td>";
		}
	}
$content .="<td>".number_format($grandtotalovertime,2)."</td><td>".number_format($grandtotalgross,2)."</td>";
$content.="</tr></tbody></table><br>";

//GRAND TOTAL FOR DEDUCTION
$content .="<div> GRAND TOTAL FOR DEDUCTION </div><table border=1 width='100%' class='data'>
			<thead><tr class='headers'><td rowspan='2'>WITHOLDINGTAX</td>";

	if (sizeof($arr_fixeddeduc_config) > 0)
	{
		$content .="<td colspan='".sizeof($arr_fixeddeduc_config)."'>CONTRIBUTION</td>";
	}

foreach ($arr_deduc_config as $code => $codedescription) {
$content .="<td rowspan='2'>".$codedescription['description']."</td>";
			}
foreach ($arr_loan_config as $code => $codedescription) {
$content .="<td rowspan='2'>".$codedescription['description']."</td>";
			}
$content .="<td rowspan='2'>TOTAL DEDUCTION</td>
		<td rowspan='2'>NET PAY</td>
		</tr>

		<tr class='headers'>";
foreach ($arr_fixeddeduc_config as $code => $codedescription)
		 {
$content .="<td>".$codedescription['description']."</td>";
		}
$content .="</tr></thead>";
$content .="<tbody><tr><td>".number_format($grandtotaltax,2)."</td>";

	foreach ($arr_fixeddeduc_config as $code => $descriptions) {
						
		if (isset($grandtotal['deduction'][$code])) 
		{
			$content .="<td>".number_format($grandtotal['deduction'][$code],2)."</td>";	
		}
		else
		{
			$content .="<td>0.00</td>";
		}
	}
		foreach ($arr_deduc_config as $codes => $descriptions) {
						
		if (isset($grandtotal['otherdeduction'][$codes])) 
		{
			$content .="<td>".number_format($grandtotal['otherdeduction'][$codes],2)."</td>";	
		}
		else
		{
			$content .="<td>0.00</td>";
		}
	}
	foreach ($arr_loan_config as $codes => $descriptions) {
						
		if (isset($grandtotal['loan'][$codes])) 
		{
			$content .="<td>".number_format($grandtotal['loan'][$codes],2)."</td>";	
		}
		else
		{
			$content .="<td>0.00</td>";
		}
	}
$content .="<td>".number_format($grandtotalEmpDeduction,2)."</td><td>".number_format($grandtotalnet,2)."</td>";
$content .="</tr></tbody></table>";

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
		.dept
		{
			background-color:#ADD8E6;
			font-size:20px;
			font-weight:bold;
			text-align:left;
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
		.data tr:nth-child(even)
		{
			 background-color:#C8C8C8;
			 
		}
		.data .headers td
		{
			 background-color:#3c8dbc;	
			 color:white;
			 font-weight:bold;
		}
		</style>
		<body>".$content."</body>
		";

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
$interval =24;
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

	<tr >
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