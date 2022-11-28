<?php 
/**
 * @author Angelica Arangco
 * @copyright 2018
 *
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payrollcomputation extends CI_Model {

	function computeEmployeeIncome($empid='',$schedule='',$quarter='',$sdate='',$edate='',$arr_income_config='',$payroll_cutoff_id=''){
		$arr_info = array();
		$str_income = '';
		$totalincome = 0;

		$res = $this->payrolloptions->incometitle($empid,'amount',$schedule,$quarter,'',$sdate,$edate);

		foreach ($res->result() as $key => $row) {
			$amount = $row->title;

			$arr_info[$row->code_income] = $amount;
			$totalincome += $amount;
			$arr_income_config[$row->code_income]['hasData'] = 1;
			if($str_income) $str_income .= '/';
			$str_income .= $row->code_income . '=' . $amount;
		}

	/*	$this->load->model('income');
		$leave_adj_code = '31';
		$leave_adj_amt = $this->income->getLeaveAdjAmount($payroll_cutoff_id,$empid);

		if($leave_adj_amt){
			$arr_info[$leave_adj_code] = $leave_adj_amt;
			$totalincome += $leave_adj_amt;
			$arr_income_config[$leave_adj_code]['hasData'] = 1;
			if($str_income) $str_income .= '/';
			$str_income .= $leave_adj_code . '=' . $leave_adj_amt;
		}

		$ob_adj_code = '32';
		$ob_adj_amt = $this->income->getLeaveAdjAmount($payroll_cutoff_id,$empid,'OB');
		$ob_adj_amt += $this->income->getLeaveAdjAmount($payroll_cutoff_id,$empid,'CORRECTION');

		if($ob_adj_amt){
			$arr_info[$ob_adj_code] = $ob_adj_amt;
			$totalincome += $ob_adj_amt;
			$arr_income_config[$ob_adj_code]['hasData'] = 1;
			if($str_income) $str_income .= '/';
			$str_income .= $ob_adj_code . '=' . $ob_adj_amt;
		}*/

		return array($arr_income_config,$arr_info,$totalincome,$str_income);
	}

	function computeEmployeeIncomeAdj($empid='',$schedule='',$quarter='',$sdate='',$edate='',$arr_income_adj_config='',$totalincome=0,$payroll_cutoff_id=0){
		$arr_info = array();
		$str_income_adj = '';

		$res = $this->payrolloptions->getEmpIncomeAdj($empid,'amount',$schedule,$quarter,'',$sdate,$edate);

		foreach ($res->result() as $key => $row) {
			$amount = $row->title;
			if($row->deduct==1) $amount = $amount * -1;

			$arr_info[$row->code_income] = $amount;
			$totalincome += $amount;
			$arr_income_adj_config[$row->code_income]['hasData'] = 1;
			if($str_income_adj) $str_income_adj .= '/';
			$str_income_adj .= $row->code_income . '=' . $amount;
		}

		$res = $this->payrolloptions->getEmpIncomeAdjSalary($empid,'amount',$schedule,$quarter,'SALARY',$sdate,$edate);

		foreach ($res->result() as $key => $row) {
			$amount = $row->title;
			if($row->deduct==1) $amount = $amount * -1;

			$arr_info[$row->code_income] = $amount;
			$totalincome += $amount;
			$arr_income_adj_config[$row->code_income]['hasData'] = 1;
			if($str_income_adj) $str_income_adj .= '/';
			$str_income_adj .= $row->code_income . '=' . $amount;
		}
		
		$this->load->model('income');
		$leave_adj_code = '31';
		$leave_adj_amt = $this->income->getLeaveAdjAmount($payroll_cutoff_id,$empid);

		if($leave_adj_amt){
			$arr_info[$leave_adj_code] = $leave_adj_amt;
			$totalincome += $leave_adj_amt;
			$arr_income_adj_config[$leave_adj_code]['hasData'] = 1;
			if($str_income_adj) $str_income_adj .= '/';
			$str_income_adj .= $leave_adj_code . '=' . $leave_adj_amt;
		}

		$ob_adj_code = '32';
		$ob_adj_amt = $this->income->getLeaveAdjAmount($payroll_cutoff_id,$empid,'OB');
		$ob_adj_amt += $this->income->getLeaveAdjAmount($payroll_cutoff_id,$empid,'CORRECTION');

		if($ob_adj_amt){
			$arr_info[$ob_adj_code] = $ob_adj_amt;
			$totalincome += $ob_adj_amt;
			$arr_income_adj_config[$ob_adj_code]['hasData'] = 1;
			if($str_income_adj) $str_income_adj .= '/';
			$str_income_adj .= $ob_adj_code . '=' . $ob_adj_amt;
		}

		return array($arr_income_adj_config,$arr_info,$totalincome,$str_income_adj);
	}


	function computeEmployeeOtherIncome($employeeid='',$sdate='',$edate='',$tnt='teaching',$schedule='',$quarter=''){
		$this->load->model('income');
		$this->load->model('payrollprocess');
		$total_holiday_and_leave = $this->extensions->getTotalLeaveAndHoliday($employeeid, $sdate, $edate);
		$income_config_q = $this->payroll->displayIncome();
		$arr_income_config = $this->payrollprocess->constructArrayListFromStdClass($income_config_q,'id','deductedby');
		$workingdays = '';
		$computeOtherIncome = 1;
		foreach ($arr_income_config as $codeIncome => $det) {
				$deductedby = $det['description'];

				$oth_q = $this->income->getEmployeeOtherIncomeConfig($employeeid,$sdate,$edate,$codeIncome);

				if($oth_q->num_rows() > 0){
					$row = $oth_q->row(0);

				    ///< compute for deduction and total pay
				    $total_deduc = $total_pay = $deduc_hours = 0;
				    $oth_monthly = $row->monthly;
				    $oth_daily = $row->daily;
				    $oth_hourly = $row->hourly;


					if($deductedby != '' || $deductedby != NULL){

						$deduc_min = 0;

						if($tnt == 'teaching'){
							list($tardy_amount,$absent_amount,$x,$x,$x,$tardy_min,$absent_min) = $this->getTardyAbsentSummaryTeaching($employeeid,$tnt,'','',$sdate,$edate,$oth_hourly,$oth_hourly,$oth_hourly, $oth_daily, $computeOtherIncome);
							$workingdays = 261;
						}else{
							list($tardy_amount,$absent_amount,$x,$tardy_min,$absent_min) = $this->getTardyAbsentSummaryNT($employeeid,$tnt,'','',$sdate,$edate,$oth_hourly);
							$workingdays = 313;
						}

						///< deduct base on setup
						if($deductedby == 'BOTH'){
							$deduc_min = $tardy_min + $absent_min;
							$total_deduc = $tardy_amount + $absent_amount;

						}elseif($deductedby == 'TARDY'){
							$deduc_min = $tardy_min;
							$total_deduc = $tardy_amount;

						}elseif($deductedby == 'ABSENT'){
							$deduc_min = $absent_min;
							// $total_deduc = $absent_amount;
							if($deduc_min > 0) $total_deduc = $oth_monthly * 2 * 12 / $workingdays;
						}

						$deduc_hours = $this->time->minutesToHours($deduc_min);
					}

				    if($deductedby == 'ABSENT'){
				    	$no_days = $deduc_hours / 8;
				    	$total_pay = $oth_monthly - ($no_days * $total_deduc);
				    }
				    else $total_pay = $oth_monthly - $total_deduc;

				    if($total_pay < 0) $total_pay = 0;

				    if($codeIncome == 29) $total_pay -= $total_holiday_and_leave * $oth_daily;

				    ///< insert to employee_income
			    	$this->income->saveEmployeeOtherIncome($employeeid,$sdate,$edate,$codeIncome,$total_pay,$schedule,$quarter);

			    	//< get corresponding dtr cutoff for given payroll cutoff
			    	list($dtr_start,$dtr_end) = $this->payrolloptions->getDtrPayrollCutoffPair('','',$sdate,$edate);

			    	///< save other income computation results for viewing
			    	$this->income->saveEmployeeOtherIncomeComputed($codeIncome,$employeeid,$dtr_start,$dtr_end,$sdate,$edate,$total_pay,$total_deduc,$deduc_hours);
				    
				} ///< end if

		} //<end loop income config

	}

	function computeLongevity($employeeid='',$sdate='',$edate='',$tnt='teaching',$schedule='',$quarter=''){
		$codeIncome = '14';
		$year = date("Y",strtotime($sdate));
		$regyear = $this->employee->EmpRegularDate($employeeid);
		$noCreditYears = $year - date("Y",strtotime($regyear));

		if($noCreditYears >= 5){

			$this->load->model('income');

			$a = $noCreditYears - 4;
			$prev_basicpay= $this->employee->GetBasicPreviousPay($employeeid);
			$present_basicpay= $this->employee->GetBasicCurrentPay($employeeid);

			$pcpay= round(((($prev_basicpay + $present_basicpay)/ 2)/12),2); 
			$totallongevity = round(((($pcpay * 3)*$a)/26),2);   ///< TOTAL LONGEVITY

			///< save to longevity computed for viewing
			$dtr_cutoff_id = $this->payrolloptions->getDtrPayrollCutoffID('','',$sdate,$edate);
			$this->income->saveLongevityComputed($dtr_cutoff_id,$employeeid,$noCreditYears,$prev_basicpay,$present_basicpay,$totallongevity);

			///< save to employee_income if is included
			$isIncluded = $this->income->isIncludedLongevity($employeeid);

			if($isIncluded){
					///< compute for deduction and total pay
					$total_deduc = $total_pay =  0;

					$deductedby = "";
					$income_config_q = $this->payroll->displayIncome($codeIncome);
					if($income_config_q->num_rows() > 0) $deductedby = $income_config_q->row(0)->deductedby;

					$workingdays = 313;
					if($tnt == 'teaching') $workingdays = 261;
					$hourly = (($totallongevity * 12) / $workingdays) / 8;

					if($tnt == 'teaching'){
						list($tardy_amount,$absent_amount,$x,$x,$x,$tardy_min,$absent_min) = $this->getTardyAbsentSummaryTeaching($employeeid,$tnt,'','',$sdate,$edate,$hourly,$hourly,$hourly);
					}else{
						list($tardy_amount,$absent_amount,$x,$tardy_min,$absent_min) = $this->getTardyAbsentSummaryNT($employeeid,$tnt,'','',$sdate,$edate,$hourly);
					}

					///< deduct base on setup
					if($deductedby == 'BOTH'){
						$total_deduc = $tardy_amount + $absent_amount;

					}elseif($deductedby == 'TARDY'){
						$total_deduc = $tardy_amount;

					}elseif($deductedby == 'ABSENT'){
						$total_deduc = $absent_amount;
					}

					$total_pay = ($totallongevity/2) - $total_deduc; ///< NET LONGEVITY PER CUTOFF
					if($total_pay < 0) $total_pay = 0;

			    	$this->income->saveEmployeeOtherIncome($employeeid,$sdate,$edate,$codeIncome,$total_pay,$schedule,$quarter);

			} //end if isIncluded


		}

	}


	function computeEmployeeFixedDeduc($empid='',$schedule='',$quarter='',$sdate='',$edate='',$arr_fixeddeduc_config='',$arr_info_emp='',$prevSalary=0,$prevGrosspay=0, $getTotalNotIncludedInGrosspay = 0,$basicPay=0, $prevBasicPay=0){
		$arr_info = $ee_er = array();
		$str_fixeddeduc = '';
		$totalfix = 0;
		$total_gross = $arr_info_emp['grosspay'] + $prevGrosspay;
		$employee_salary = $this->db->query("SELECT * FROM payroll_employee_salary_history WHERE employeeid = '$empid' ");
		if($employee_salary->num_rows() > 0){
			$employee_salary = $this->db->query("SELECT * FROM payroll_employee_salary_history WHERE employeeid = '$empid' ORDER BY date_effective DESC LIMIT 1")->row()->monthly;
		}else{
			$employee_salary = $this->db->query("SELECT * FROM payroll_employee_salary WHERE employeeid = '$empid' ORDER BY date_effective DESC LIMIT 1")->row()->monthly;
		}

		$res = $this->payrolloptions->getEmpFixedDeduc($empid,'amount','HIDDEN',$schedule,$quarter,'',$sdate,$edate);

		foreach ($res->result() as $key => $row) {
			$cutoff_period = $row->cutoff_period;
			$er = $ec = $provident_er = 0;
			$amount_fx = $row->title;
			$code_deduction = $row->code_deduction;
		/*	if($amount_fx == NULL){
				if($row->code_deduction == 'PHILHEALTH'){
					$amount_fx = $this->computePHILHEALTHContri($arr_info_emp['salary'] * 2);
				}
				else if ($row->code_deduction == 'SSS') {
					$amount_fx = $this->computeSSSContri($arr_info_emp['grosspay']);
				}
				else if ($row->code_deduction == 'PERAA') {
					$amount_fx = ($arr_info_emp['salary'] * 2) * 0.0325;
				}
				else if ($row->code_deduction == 'PAGIBIG') {
					$amount_fx = $this->computePagibigContri($arr_info_emp['salary'] * 2);
				}
			}*/
			if($row->code_deduction == 'PHILHEALTH'){
				list($amount_fx,$er) = $this->computePHILHEALTHContri($amount_fx,$arr_info_emp["salary"],$prevBasicPay,$cutoff_period,$quarter,$sdate,$empid);
				if(!$this->payrolloptions->checkIdnumber($empid, $code_deduction)) $amount_fx = $er = 0;

			}
			else if ($row->code_deduction == 'SSS') {
				list($amount_fx,$ec,$er,$provident_er) = $this->computeSSSContri($amount_fx,$arr_info_emp['grosspay'],$prevGrosspay,$empid,$sdate,$edate,$quarter,$cutoff_period,$getTotalNotIncludedInGrosspay);
				if(!$this->payrolloptions->checkIdnumber($empid, $code_deduction)) $amount_fx = $er = $ec = 0;
			}
			else if ($row->code_deduction == 'PAGIBIG') {
				list($amount_fx,$er) = $this->computePagibigContri($amount_fx,$empid,$arr_info_emp['salary'] * 2,$cutoff_period,$sdate,$quarter);
				if(!$this->payrolloptions->checkIdnumber($empid, $code_deduction)) $amount_fx = $er = 0;
			}
			else if ($row->code_deduction == 'PERAA') {
				if($row->amount == "") $amount_fx = ($arr_info_emp['salary'] * 2) * 0.0325;
				$er = $amount_fx;
			}

			$ee_er[$row->code_deduction]['EE'] = $amount_fx;
			$ee_er[$row->code_deduction]['ER'] = $er;
			$ee_er[$row->code_deduction]['EC'] = $ec;
			$ee_er[$row->code_deduction]['provident_er'] = $provident_er;


			$arr_info[$row->code_deduction] = $amount_fx;
			$totalfix += $amount_fx;

			$arr_fixeddeduc_config[$row->code_deduction]['hasData'] = 1;
			if($str_fixeddeduc) $str_fixeddeduc .= '/';
			$str_fixeddeduc .= $row->code_deduction . '=' . $amount_fx;
		}

		return array($arr_fixeddeduc_config,$arr_info,$totalfix,$str_fixeddeduc,$ee_er);
	}

	

	function computeEmployeeDeduction($empid='',$schedule='',$quarter='',$sdate='',$edate='',$arr_deduc_config='',$arr_deduc_config_arithmetic=''){
		$arr_info = array();
		$str_deduc = '';
		$total_deducSub = $total_deducAdd = 0;

		$res = $this->payrolloptions->deducttitle($empid,'amount','SHOW',$schedule,$quarter,'',$sdate,$edate);

		foreach ($res->result() as $key => $row) {
			$arr_info[$row->code_deduction] = $row->title;
			if ($arr_deduc_config_arithmetic[$row->code_deduction]['description'] == "sub") {
				 $total_deducSub += $row->title;
			}else{
				 $total_deducAdd += $row->title;	
			}
			// $total_deduc += $row->title;
			$arr_deduc_config[$row->code_deduction]['hasData'] = 1;
			if($str_deduc) $str_deduc .= '/';
			$str_deduc .= $row->code_deduction . '=' . $row->title;
		}

		return array($arr_deduc_config,$arr_info,$total_deducSub,$total_deducAdd,$str_deduc);
	}

	function computeEmployeeLoan($empid='',$schedule='',$quarter='',$sdate='',$edate='',$arr_loan_config=''){
		$arr_info = array();
		$str_loan = '';
		$totalloan = 0;

		$res = $this->payrolloptions->loantitle($empid,'amount',$schedule,$quarter,'',$sdate,$edate);

		foreach ($res->result() as $key => $row) {
			$arr_info[$row->code_loan] = $row->title;
			$totalloan += $row->title;
			$arr_loan_config[$row->code_loan]['hasData'] = 1;
			if($str_loan) $str_loan .= '/';
			$str_loan .= $row->code_loan . '=' . $row->title;
		}

		return array($arr_loan_config,$arr_info,$totalloan,$str_loan);
	}


	/*function computePHILHEALTHContri($monthlySalary=0){
		$contri = 0;
		if($monthlySalary <= 10000) $contri = 275;
		elseif($monthlySalary > 10000 && $monthlySalary <= 40000) $contri = $monthlySalary * 0.0275;
		elseif($monthlySalary > 40000) $contri = 1100;

		return $contri / 2; ///< for employee and employer
	}

	function computeSSSContri($gross=0){
		$return = '0.00';
		$query = $this->db->query("SELECT emp_ee FROM sss_deduction WHERE '$gross' BETWEEN compensationfrom AND compensationto");
		if ($query->num_rows() > 0) {
			$return = $query->row()->emp_ee;
		} 
		return $return;
	}

	function computePagibigContri($gross=0){
		$return = '0.00';
		$query = $this->db->query("SELECT emp_ee FROM hdmf_deduction WHERE '$gross' BETWEEN compensationfrom AND compensationto");
		if ($query->num_rows() > 0) {
			$return = $query->row()->emp_ee;
		} 
		return $return;
	}*/

	function philhealthContribution($monthlySalary=""){
		$isrange = $this->db->query("SELECT * FROM `philhealth_empshare` WHERE $monthlySalary BETWEEN min_salary AND max_salary AND min_salary != '' AND max_salary != ''");
		if($isrange->num_rows() > 0){
			if($isrange->row()->percentage){
				$isrange->row()->percentage = "0.0".$isrange->row()->percentage;
				$ee = $monthlySalary * $isrange->row()->percentage;
				return $ee;
			}else{
				$ee = $isrange->row()->def_amount;
				return $ee;
			}
		}
		$isminimum = $this->db->query("SELECT * FROM `philhealth_empshare` WHERE min_salary > $monthlySalary AND min_salary != '' AND def_amount != ''");
		if($isminimum->num_rows() > 0){
			$ee = $isminimum->row()->def_amount;
			return $ee;
		}
		$ismaximum = $this->db->query("SELECT * FROM `philhealth_empshare` WHERE max_salary > $monthlySalary AND max_salary != '' AND def_amount != ''");
		if($ismaximum->num_rows() > 0){
			$ee = $ismaximum->row()->def_amount;
			return $ee;
		}
	}

	function computePHILHEALTHContri($encoded_ee=NULL,$basicPay=0,$prevBasicPay=0,$cutoff_period="",$quarter="",$sdate="",$employeeid=""){
		$cutoffdate = "";
		if($quarter==1) $cutoffdate = date('Y-m',strtotime("-1 months", strtotime($sdate)));
		else $cutoffdate = date('Y-m',strtotime($sdate));
		list($prev_ee,$prev_ec,$prev_er) = $this->getPrevPhilhealthContri($cutoffdate,$quarter,$employeeid);

		$ee = $er = $true_ee = 0;
		if($encoded_ee == NULL){
			if($cutoff_period == 3){
				if($quarter == 1) $ee = $this->philhealthContribution($basicPay);
				else $ee = $this->philhealthContribution($basicPay+$prevBasicPay);

				if($prev_ee == 0) $ee += $ee;
			}else{
				$ee = $this->philhealthContribution($basicPay);
			}
			// $ee = $this->philhealthContribution($monthlySalary);
			
			if($cutoff_period == "3") $ee = $ee / 2; ///< for 1st & 2nd cutoff

			$ee = $ee / 2; ///< for employee and employer
			
			// $true_ee = bcdiv($ee, 1, 2);
			$true_ee = round($ee,2);
			$excess = $ee - $true_ee;
			
			$er = $ee + $excess;
			$er = round($er,2);
			
		}else{
			$true_ee = $er = $encoded_ee;
		}
		return array($true_ee,$er); 
	}

	function getPrevPhilhealthContri($cutoff_month='',$quarter=1,$employeeid=''){
		$prev_ee = $prev_ec = $prev_er = 0;
		if($cutoff_month){
			$res = $this->db->query("SELECT b.code_deduction,b.EE,b.EC,b.ER FROM payroll_computed_table a
										INNER JOIN payroll_computed_ee_er b ON b.base_id = a.id
										WHERE a.employeeid='$employeeid' AND DATE_FORMAT(a.cutoffstart,'%Y-%m')='$cutoff_month' AND a.quarter=1 AND b.code_deduction = 'PHILHEALTH' AND status = 'PROCESSED'");

			if($res->num_rows() > 0){
				foreach($res->result() as $row){
					$prev_ee += $row->EE;
					$prev_ec += $row->EC;
					$prev_er += $row->ER;
				}
			}else{
				

			}
		}
		

		return array($prev_ee,$prev_ec,$prev_er);
	}

	///< Ticket #ICA-HYPERION21515
	function computeSSSContri($encoded_ee=NULL,$gross=0,$prevGrosspay=0,$empid='',$sdate='',$edate='',$quarter='',$cutoff_period='',$getTotalNotIncludedInGrosspay=0){
		$ee = $ec = $er = $provident_er = 0;
		$total_gross = 0;
		$year = date("Y", strtotime($sdate));
		if($cutoff_period == 3){
			if($quarter == 1){
				$gross -= $getTotalNotIncludedInGrosspay;
				list($ee,$ec,$er,$provident_er) = $this->getSSSContriFromSetup($encoded_ee,$gross,$year);
			}elseif($quarter == 2){
				$gross -= $getTotalNotIncludedInGrosspay;
				list($prev_ee,$prev_ec,$prev_er) = $this->getPrevSSSContri(date('Y-m',strtotime($sdate)),$quarter,$empid, $sdate, $edate);
				list($ee,$ec,$er,$provident_er) = $this->getSSSContriFromSetup($encoded_ee,$gross + $prevGrosspay,$year);

				$ee = $ee - $prev_ee;
				$ec = $ec - $prev_ec;
				$er = $er - $prev_er;
			} 

		}else{
			$gross -= $getTotalNotIncludedInGrosspay;
			if($cutoff_period == 1) 	$total_gross = $gross;
			elseif($cutoff_period == 2) $total_gross = $gross + $prevGrosspay;

			list($ee,$ec,$er,$provident_er) = $this->getSSSContriFromSetup($encoded_ee,$total_gross,$year);
		}

		return array($ee,$ec,$er,$provident_er);
	}

	///< Ticket #ICA-HYPERION21515
	function getSSSContriFromSetup($encoded_ee=NULL,$gross=0,$year=""){
		$ee = $ec = $er = $provident_er = 0;
		
		if($encoded_ee == NULL){
			$query = $this->db->query("SELECT emp_ee,emp_con,emp_er,total_ee,provident_er FROM sss_deduction WHERE '$gross' BETWEEN compensationfrom AND compensationto AND year = '$year'");
			if ($query->num_rows() > 0) {
				$ee = $query->row()->total_ee;
				$ec = $query->row()->emp_con;
				$er = $query->row()->emp_er;
				$provident_er = $query->row()->provident_er;
			} 
		}else{
			$ee = $encoded_ee;
			$query = $this->db->query("SELECT emp_ee,emp_con,emp_er,provident_er FROM sss_deduction WHERE emp_ee <= $encoded_ee ORDER BY emp_ee DESC LIMIT 1");
			if ($query->num_rows() > 0) {
				$ec = $query->row()->emp_con;
				$er = $query->row()->emp_er;
				$provident_er = $query->row()->provident_er;
			} 
		}
		return array($ee,$ec,$er,$provident_er);
	}

	///< Ticket #ICA-HYPERION21515
	function getPrevSSSContri($cutoff_month='',$quarter=1,$employeeid='',$sdate='',$edate=''){
		$prev_ee = $prev_ec = $prev_er = 0;
		if($cutoff_month){
			if($quarter > 1){
				$res = $this->db->query("SELECT b.code_deduction,b.EE,b.EC,b.ER FROM payroll_computed_table a
											INNER JOIN payroll_computed_ee_er b ON b.base_id = a.id
											WHERE a.employeeid='$employeeid' AND DATE_FORMAT(a.cutoffstart,'%Y-%m')='$cutoff_month' AND a.quarter=1 AND b.code_deduction = 'SSS' AND a.cutoffstart != '$sdate' AND a.cutoffend != '$edate'
											LIMIT 1");

				if($res->num_rows() > 0){
					$prev_ee = $res->row(0)->EE;
					$prev_ec = $res->row(0)->EC;
					$prev_er = $res->row(0)->ER;
				}
			}
		}else{

		}

		return array($prev_ee,$prev_ec,$prev_er);
	}

	function computePagibigContri($encoded_ee=NULL,$employeeid='',$gross=0,$cutoff_period,$sdate="",$quarter=""){
		$cutoffdate = "";
		if($quarter==1) $cutoffdate = date('Y-m',strtotime("-1 months", strtotime($sdate)));
		else $cutoffdate = date('Y-m',strtotime($sdate));
		list($prev_ee,$prev_ec,$prev_er) = $this->getPrevPagibigContri($cutoffdate,$quarter,$employeeid);
		// echo "<pre>"; print_r(array($prev_ee,$prev_ec,$prev_er)); die;
		$ee = $er = 0;

		if($encoded_ee == NULL){
			$query = $this->db->query("SELECT emp_ee,emp_er FROM hdmf_deduction WHERE '$gross' BETWEEN compensationfrom AND compensationto");
			if ($query->num_rows() > 0) {
				$ee = $query->row()->emp_ee;
				$er = $query->row()->emp_er;
			} 
		}else{
			$ee = $encoded_ee;
			$query = $this->db->query("SELECT emp_ee,emp_er FROM hdmf_deduction WHERE emp_ee <= $encoded_ee ORDER BY emp_ee DESC LIMIT 1");
			if ($query->num_rows() > 0) {
				$er = $query->row()->emp_er;
			} 
		}

		if($cutoff_period == "3") $ee = $ee / 2; ///< for employee and employer

		if($prev_ee == 0) $ee += $ee;
		return array($ee,$er);
	}

	function getPrevPagibigContri($cutoff_month='',$quarter=1,$employeeid=''){
		$prev_ee = $prev_ec = $prev_er = 0;
		if($cutoff_month){
			// if($quarter == 1){
			$res = $this->db->query("SELECT b.code_deduction,b.EE,b.EC,b.ER FROM payroll_computed_table a
										INNER JOIN payroll_computed_ee_er b ON b.base_id = a.id
										WHERE a.employeeid='$employeeid' AND DATE_FORMAT(a.cutoffstart,'%Y-%m')='$cutoff_month' AND a.quarter=1 AND b.code_deduction = 'PAGIBIG' AND status = 'PROCESSED'");
			// echo $this->db->last_query(); die;

			if($res->num_rows() > 0){
				foreach($res->result() as $row){
					$prev_ee += $row->EE;
					$prev_ec += $row->EC;
					$prev_er += $row->ER;
				}
			}else{
				/*$prev_ee = 100;
				$prev_ec = 100;
				$prev_er = 100;*/

			}
		}
		/*}else{

		}*/

		return array($prev_ee,$prev_ec,$prev_er);
	}

	function computeTeachingCutoffSalary($workhours_lec='',$workhours_lab='',$workhours_admin='',$hourly=0,$lechour=0,$labhour=0,$fixedday=0,$regpay=0,$perdept_amt_arr=array(),$hold_status=''){
		$salary = 0;
		$perdept_amount = 0;

		if(sizeof($perdept_amt_arr) > 0){
			foreach ($perdept_amt_arr as $aimsdept => $leclab_arr) {
				foreach ($leclab_arr as $type => $amt) {
					if ($type != 'ADMIN') $perdept_amount += $amt['work_amount'];
				}
			}
		}

		if($hold_status == 'ALL') 			$regpay = $perdept_amount = 0;
		elseif($hold_status == 'LECLAB') 	$perdept_amount = 0;

		if($fixedday){
			$salary += $regpay + $perdept_amount;
		}else{
			$salary = $perdept_amount;
		}
		
		return $salary;
	}

	function computeNTCutoffSalary($workdays=0,$fixedday=0,$regpay=0,$daily=0){
		$salary = 0;
		if($fixedday){
			$salary = $regpay;
		}else{
			$salary = $workdays * $daily;
		}

		return $salary;
	}

	function computeOvertime($empid='',$tnt='teaching',$schedule='',$quarter='',$sdate='',$edate='',$hourly=0){
		$overtimepay = 0;
		$otreg = $otrest = $othol = 0;
		if($hourly){
			$minutely = $hourly / 60;

			if($tnt == 'teaching'){

			}else{
				$detail_q = $this->db->query("SELECT otreg,otrest,othol FROM attendance_confirmed_nt WHERE employeeid='$empid' AND payroll_cutoffstart='$sdate' AND payroll_cutoffend='$edate'");
		    	if($detail_q->num_rows() > 0){
		    		$otreg 		= $detail_q->row(0)->otreg;
		    		$otrest 	= $detail_q->row(0)->otrest;
		    		$othol 		= $detail_q->row(0)->othol;
		    		
			        $otreg = $this->attcompute->exp_time($otreg);
			        $otrest = $this->attcompute->exp_time($otrest);
			        $othol = $this->attcompute->exp_time($othol);
		    	}
			}

			$otreg      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($otreg));
			$otrest      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($otrest));
			$othol      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($othol));

			$otregpay = $otreg * ($minutely * 1.25);
			$otrestpay = $otrest * ($minutely * 0.25);
			$otholpay = $othol * ($minutely * 2.00);

			$overtimepay = $otregpay + $otrestpay + $otholpay;
		}

		return $overtimepay;
	}

	function computeOvertime2($empid='',$tnt='teaching',$hourly=0,$base_id='',$employmentstat=''){
		$this->load->model('utils');
		$overtimepay = 0;
		$ot_det = array();

		$minutely_orig = $hourly / 60;

		$setup = $this->getOvertimeSetup($employmentstat);
		// echo '<pre>';
		// print_r($setup);
		// echo '</pre>';

		$tbl = 'attendance_confirmed_ot_hours';
		if($tnt=='nonteaching') $tbl = 'attendance_confirmed_nt_ot_hours';

		if($base_id){
			$ot_q = $this->utils->getSingleTblData($tbl,array('*'),array('base_id'=>$base_id));
			
			foreach ($ot_q->result() as $key => $row) {
				$att_baseid = $row->id;

				$ot_hours = $row->ot_hours;
				$ot_type = $row->ot_type;
				$holiday_type = $row->holiday_type;
				$is_excess = $row->is_excess;

				$ot_min = $this->time->hoursToMinutes($ot_hours);

				$percent = 100; ///< default

				if(isset($setup[$employmentstat][$ot_type][$holiday_type][$is_excess])){ ///< get percent if has existing setup
					$percent = $setup[$employmentstat][$ot_type][$holiday_type][$is_excess];
				}

				$percent = $percent / 100;

				$minutely = $minutely_orig * $percent;
				$initial_pay = $minutely * $ot_min;

				$ot_det[$att_baseid] = $initial_pay; ///< insert later for overtime amount details

				$overtimepay += $initial_pay;

			}
		}

		return array($overtimepay,$ot_det);
	}

	function getOvertimeSetup($employmentstat=''){
		$filter = $setup = array();

		if($employmentstat) $filter['code_status'] = $employmentstat;
		$ot_q = $this->utils->getSingleTblData('code_overtime',array('*'),$filter);

		foreach ($ot_q->result() as $key => $row) {
			$setup[$row->code_status][$row->ot_types] = array(
																'NONE' 		=> array('0'=>$row->percent,'1'=>$row->excess_percent),
																'REGULAR' 	=> array('0'=>$row->regular_percent,'1'=>$row->regular_percent_excess),
																'SPECIAL' 	=> array('0'=>$row->other_percent,'1'=>$row->other_percent_excess)
															);
		}
		return $setup;
	}


	/**
	 * Compute withholding tax. Taxable income = Salary + taxable income - included deductions - fixed deduc.
	 * Refer to ticket# ICA-Hyperion21063
	 *
	 * @return Float
	 */
	function computeWithholdingTax($schedule='',$dependents='',$regpay='',$arr_income,$arr_income_adj,$arr_deduc,$arr_fixeddeduc,$overtime=0){
		$whtax = $total_income = $total_deduc = $total_fixeddeduc =  $total_taxable = 0;
		$this->load->model('payrollprocess');

		///< get total taxable income first

		$income_config_q = $this->payroll->displayIncome();
		$arr_income_config = $this->payrollprocess->constructArrayListFromStdClass($income_config_q,'id','taxable');
		$deduction_config_q = $this->payroll->displayDeduction();
		$arr_deduc_config = $this->payrollprocess->constructArrayListFromStdClass($deduction_config_q,'id','arithmetic');

		if(sizeof($arr_income) > 0){
			foreach ($arr_income as $key => $value) {
				if($arr_income_config[$key]['description'] == 'withtax') $total_income += $value;
			}
		}
		if(sizeof($arr_income_adj) > 0){
			foreach ($arr_income_adj as $key => $value) {
				if(isset($arr_income_config[$key]['description'])){
					if($arr_income_config[$key]['description'] == 'withtax') $total_income += $value;

				}else{
					if($key == 'SALARY') $total_income += $value;
				}
			}
		}
		if(sizeof($arr_deduc) > 0){
			foreach ($arr_deduc as $key => $value) {
				$isDeductionWithtax = $this->payrollprocess->checkDeductionIfWithtax($key);
				if($isDeductionWithtax == "withtax"){
					if($arr_deduc_config[$key]['description'] == 'sub') $total_deduc -= $value;
					else $total_deduc += $value;
				}
			}
		}
		if(sizeof($arr_fixeddeduc) > 0){
			foreach ($arr_fixeddeduc as $key => $value) {
				if($key != 'PERAA') $total_fixeddeduc += $value; ///< fixed deductions are subtracted automatically
			}
		}

		
		$total_taxable = $regpay + $total_income + $overtime /*+ $total_deduc*/ - $total_fixeddeduc;

		$tax_config_q = $this->db->query("SELECT * FROM code_tax WHERE tax_type='$schedule' AND status_='$dependents' AND tax_range <= '$total_taxable' ORDER BY tax_range DESC LIMIT 1");

		if($tax_config_q->num_rows() > 0){
			$tax_config = $tax_config_q->row(0);

			if(is_numeric($regpay) && is_numeric($tax_config->tax_range) && is_numeric($regpay) && is_numeric($tax_config->percent) && is_numeric($tax_config->basic_tax)){

				$whtax = (( $total_taxable - $tax_config->tax_range ) * ($tax_config->percent/100) ) + $tax_config->basic_tax;
			}
		}
		return $whtax;
	}


	function getTardyAbsentSummaryTeaching($empid = "",$ttype="",$schedule = "",$quarter = "",$sdate = "",$edate = "",$hourly=0,$lechour=0,$labhour=0,$perdept_salary=array(),$force_useHourly=false){
		$this->load->model("utils");
		$separated_department = $this->extensions->getBEDDepartments();

		$empDepartment = $this->utils->getEmployeeDepartment($empid); 
		$tardy_amount = $absent_amount = $tardy_lec = $tardy_lab = $tardy_admin = $absent_lec = $absent_lab = $absent_admin = 0;
		$workhours_lec = $workhours_lab = $workhours_admin = $hold_status = '';
		$isFinal = 0;

		$total_tardy_min = $total_absent_min = 0;

		$min_lec = $lechour / 60;
		$min_lab = $labhour / 60;
		$min_admin = $hourly / 60;

		$perdept_amt_arr = array();
			    
		$base_id = '';
    	$detail_q = $this->db->query("SELECT id ,latelec, latelab, lateadmin, deduclec, deduclab, deducadmin, workhours_lec, workhours_lab, workhours_admin , hold_status_change, isFinal
    									FROM attendance_confirmed 
    									WHERE employeeid='$empid' AND payroll_cutoffstart='$sdate' AND payroll_cutoffend='$edate' 
    											AND `status`='PROCESSED' AND forcutoff=1
    									ORDER BY cutoffstart DESC");
    	
    	if($detail_q->num_rows() > 0){
    		$tlec = $tlab = $tadmin = $tdlec = $tdlab = $tdadmin = 0;

    		$hold_status = $detail_q->row(0)->hold_status_change;
    		$isFinal = $detail_q->row(0)->isFinal;
    			
    		if($hold_status != 'ALL'){

	    		///< workhours will refer to latest  cutoff
	    		$workhours_lec 	= $detail_q->row(0)->workhours_lec;
	    		$workhours_lab 	= $detail_q->row(0)->workhours_lab;
	    		$workhours_admin 	= $detail_q->row(0)->workhours_admin;

	    		foreach ($detail_q->result() as $key => $row) {
	    			///< for cases of more than 1 dtr cutoff per 1 payroll cutoff
	    			///< sum up tardy and absent
	    			$base_id 	= $row->id;
	    			$perdept_q = $this->db->query("SELECT work_hours, late_hours, deduc_hours, `type`, aimsdept FROM workhours_perdept WHERE base_id='$base_id'");

	    			foreach ($perdept_q->result() as $key_dept => $row_dept) {
	    				$aimsdept = $row_dept->aimsdept;
	    				$type = $row_dept->type;
	    				$type_rate = ($type == 'LEC') ? 'lechour' : 'labhour';

	    				if( ($type != 'LEC' && $type != 'LAB') || $hold_status != 'LECLAB' ){
			    				if($type == 'ADMIN'){
			    					$rate_min = $min_admin;
			    				}else{
			    					$rate_min = isset($perdept_salary[$aimsdept][$type_rate]) ? ($perdept_salary[$aimsdept][$type_rate] / 60): 0;
			    				}

			    				if($force_useHourly) $rate_min = $min_admin;

			    				$late_min = $this->time->hoursToMinutes($row_dept->late_hours);
			    				$deduc_min = $this->time->hoursToMinutes($row_dept->deduc_hours);

			    				$work_amt = ($type == 'ADMIN') ? 0 : ($this->time->hoursToMinutes($row_dept->work_hours) * $rate_min); //< no perdept work amount if type=ADMIN
			    				$late_amt = $late_min * $rate_min;
			    				$deduc_amt = $deduc_min * $rate_min;

			    				if(!isset($perdept_amt_arr[$aimsdept][$type]['work_amount'])) $perdept_amt_arr[$aimsdept][$type]['work_amount'] = 0;
			    				if(!isset($perdept_amt_arr[$aimsdept][$type]['late_amount'])) $perdept_amt_arr[$aimsdept][$type]['late_amount'] = 0;
			    				if(!isset($perdept_amt_arr[$aimsdept][$type]['deduc_amount'])) $perdept_amt_arr[$aimsdept][$type]['deduc_amount'] = 0;
			    				$perdept_amt_arr[$aimsdept][$type]['work_amount'] += $work_amt;
			    				$perdept_amt_arr[$aimsdept][$type]['late_amount'] += $late_amt;
			    				$perdept_amt_arr[$aimsdept][$type]['deduc_amount'] += $deduc_amt;

			    				$tardy_amount += $late_amt;
			    				/*if($type != "ADMIN")*/ $absent_amount += $deduc_amt;

			    				$total_tardy_min += $late_min;
			    				$total_absent_min += $deduc_min;
	    				}
	    			}

	    			if(in_array($empDepartment, $separated_department)){
    					$daily_rate = $this->getEmployeeDailySalary($empid);
						$days_absent = $this->getEmployeeDayAbsent($empid, $sdate, $edate);
						$perday_deduction = $daily_rate * $days_absent; #per day deduction
						$absent_amount += $perday_deduction;

    				}

	    		}
    		} // end if hold_status

    	}

		return array($tardy_amount,$absent_amount,$workhours_lec,$workhours_lab,$workhours_admin,$perdept_amt_arr,$hold_status,$total_tardy_min,$total_absent_min,$isFinal,$base_id);
	}

	function getTardyAbsentSummaryNT($empid = "",$ttype="",$schedule = "",$quarter = "",$sdate = "",$edate = "",$hourly=0,$useDTRCutoff=false){
		$tardy_amount = $absent_amount = $tardy = $ut = $absent = $isFinal =0;
		$workdays = 0;
		$base_id = '';

		$minutely = $hourly / 60;

		$wC = '';
		if($useDTRCutoff){
			$wC .= " AND cutoffstart='$sdate' AND cutoffend='$edate'";
		}else{
			$wC .= " AND payroll_cutoffstart='$sdate' AND payroll_cutoffend='$edate'";
		}
	  
    	$detail_q = $this->db->query("SELECT id, lateut, ut, absent, workdays, isFinal FROM attendance_confirmed_nt WHERE employeeid='$empid' $wC");
    	if($detail_q->num_rows() > 0){
    		$base_id 	= $detail_q->row(0)->id;

    		$tlec 		= $detail_q->row(0)->lateut;
    		$utlec 		= $detail_q->row(0)->ut;
    		$tabsent 	= $detail_q->row(0)->absent;

    		$workdays 	= $detail_q->row(0)->workdays;
    		$isFinal 	= $detail_q->row(0)->isFinal;

	        $tardy = $this->attcompute->exp_time($tlec);
	        $ut = $this->attcompute->exp_time($utlec);
	        $absent = $this->attcompute->exp_time($tabsent);
    	}


	    $tardy      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($tardy)) + $this->time->hoursToMinutes($this->attcompute->sec_to_hm($ut));
	    $absent      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($absent));

	    $tardy_amount     = number_format($tardy * $minutely,2,'.', '');
	    $absent_amount     = number_format($absent * $minutely,2,'.', '');


		return array($tardy_amount,$absent_amount,$workdays,$tardy,$absent,$base_id, $isFinal);
	}

	# added by justin bibeee (with e) for ica-hyperion 21555
	function getYearToDateSummaries_whTax($employeeid, $sel_year, $date_to){
		$amount = 0;

		$q_yearly_withholdingtax = $this->db->query("SELECT withholdingtax
													 FROM payroll_computed_table 
													 WHERE employeeid = '$employeeid' AND cutoffstart LIKE '$sel_year%' AND cutoffend <= '$date_to' AND `status`='PROCESSED';")->result();

		foreach ($q_yearly_withholdingtax as $res) $amount += round($res->withholdingtax, 2);

		return $amount;
	}

	function getExistingWithholdingTax($employeeid, $date){
			$query_whtax = $this->db->query("SELECT * FROM payroll_employee_salary_history WHERE date_effective <= '$date' AND employeeid = '$employeeid' ORDER BY date_effective DESC LIMIT 1 ");
			if($query_whtax->num_rows > 0) return $query_whtax->row()->whtax;
			else return false;
	}

	function getPerdeptSalary($employeeid=''){
		$perdept_salary = array();
		$res = $this->db->query("SELECT * FROM payroll_emp_salary_perdept WHERE employeeid='$employeeid'");
		foreach ($res->result() as $key => $row) {
			$perdept_salary[$row->aimsdept] = array('lechour'=>$row->lechour,'labhour'=>$row->labhour);
		}
		return $perdept_salary;
	}

	function getPerdeptSalaryHistory($employeeid='',$payroll_cutoff_from=''){
		$perdept_salary = array();
		$base_id = '';
		$base_res = $this->db->query("SELECT id FROM payroll_employee_salary_history WHERE employeeid='$employeeid' AND date_effective <= '$payroll_cutoff_from' ORDER BY date_effective DESC LIMIT 1");
		if($base_res->num_rows() > 0) $base_id = $base_res->row(0)->id;

		if($base_id){
			$res = $this->db->query("SELECT * FROM payroll_emp_salary_perdept_history WHERE base_id='$base_id'");
			foreach ($res->result() as $key => $row) {
				$perdept_salary[$row->aimsdept] = array('lechour'=>$row->lechour,'labhour'=>$row->labhour);
			}
		}
		return $perdept_salary;
	}

	function getEmployeeDailySalary($empid){
		$employeeDailyRate = $this->db->query("SELECT * FROM payroll_employee_salary WHERE employeeid = '$empid' ORDER BY date_effective DESC LIMIT 1");
		if($employeeDailyRate->num_rows() > 0) return $employeeDailyRate->row()->daily;
		return false;
	}

	function getEmployeeDayAbsent($empid, $sdate, $edate){
		$employeeDailyRate = $this->db->query("SELECT * FROM attendance_confirmed WHERE employeeid = '$empid' AND payroll_cutoffstart = '$sdate' AND payroll_cutoffend = '$edate' ")->row()->absent;
		return $employeeDailyRate;
	}

}//endoffile
