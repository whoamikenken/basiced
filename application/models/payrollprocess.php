
<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 *
 * This model is an extension to models\payroll.php
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payrollprocess extends CI_Model {
	
	///< construct an associative array list from computed table string, arr['key'] = $value;
	function constructArrayListFromComputedTable($str=''){
	    $arr = array();
	    if($str){
	        $str_arr = explode('/', $str);
	        if(count($str_arr)){
	            foreach ($str_arr as $i_temp) {
	                $str_arr_temp = explode('=', $i_temp);
	                if(isset($str_arr_temp[0]) && isset($str_arr_temp[1])){
	                    $arr[$str_arr_temp[0]] = $str_arr_temp[1];
	                }
	            }
	        }
	    }
	    return $arr;
	}


	///< construct an associative array list from stdclass object, $arr['key'] = $value;
	function constructArrayListFromStdClass($res='',$key='',$value=''){
	    $arr = array();
	    if($res->num_rows() > 0){
	        foreach ($res->result() as $k => $row) {
	            $arr[$row->$key] = array('description'=>$row->$value,'hasData'=>0);
	        }
	    }
	    return $arr;
	}

	function processPayrollSummary($emplist=array(),$emplist2=array(),$sdate='',$edate='',$schedule='',$quarter='',$recompute=false,$payroll_cutoff_id=''){

		$recomputed_emp_payroll = 0;
		$workdays = 0;
		$workhours_lec = $workhours_lab = $workhours_admin = "";

		//< initialize needed info ---------------------------------------------------
		$info    = $arr_income_config = $arr_income_adj_config = $arr_incomeoth_config = $arr_deduc_config = $arr_fixeddeduc_config = $arr_loan_config = array();

		///< ------------------------------ income config ------------------------------------------------------------
		$income_config_q = $this->payroll->displayIncome();
		$arr_income_config = $this->constructArrayListFromStdClass($income_config_q,'id','description');

		$arr_income_adj_config = $arr_income_config;
		$arr_income_adj_config['SALARY'] = array('description'=>'SALARY','hasData'=>0);

		///< ------------------------------ incomeoth config ---------------------------------------------------------------
		$incomeoth_config_q = $this->payroll->displayIncomeOth();
		$arr_incomeoth_config = $this->constructArrayListFromStdClass($incomeoth_config_q,'id','description');

		///< ------------------------------ fixed deduction config ----------------------------------------------------
		$fixeddeduc_config_q = $this->db->query("SELECT code_deduction,description FROM deductions");
		$arr_fixeddeduc_config = $this->constructArrayListFromStdClass($fixeddeduc_config_q,'code_deduction','description');


		///< ------------------------------ deduction config ----------------------------------------------------------
		$deduction_config_q = $this->payroll->displayDeduction();
		$arr_deduc_config = $this->constructArrayListFromStdClass($deduction_config_q,'id','description');
		$arr_deduc_config_arithmetic = $this->constructArrayListFromStdClass($deduction_config_q,'id','arithmetic');


		///< ------------------------------ loan config ---------------------------------------------------------------
		$loan_config_q = $this->payroll->displayLoan();
		$arr_loan_config = $this->constructArrayListFromStdClass($loan_config_q,'id','description');

		if($recompute === true){
			foreach($emplist2 as $row){
				$eid = $row->employeeid;
				$this->db->query("DELETE FROM payroll_computed_table WHERE cutoffstart='$sdate' AND cutoffend='$edate' AND schedule='$schedule' AND quarter='$quarter' AND employeeid='$eid' AND status='PENDING'");
			}
		}

		foreach ($emplist as $row) {
			$perdept_amt_arr = array();
			$eid = $row->employeeid;
				
			$check_saved_q = $this->getPayrollSummary('SAVED',$sdate,$edate,$schedule,$quarter,$eid,TRUE,'PROCESSED');

			if(!$check_saved_q){

				$info[$eid]['income'] = $info[$eid]['income_adj'] = $info[$eid]['deduction'] = $info[$eid]['fixeddeduc'] = $info[$eid]['loan'] = array();

				$info[$eid]['fullname'] 	= $row->fullname;

				///< check for pending computation, if true - display directly, else compute payroll first
				$res = $this->getPayrollSummary('PENDING',$sdate,$edate,$schedule,$quarter,$eid);

				if($res->num_rows() > 0){

					list($info,$arr_income_config,$arr_income_adj_config,$arr_fixeddeduc_config,$arr_deduc_config,$arr_loan_config) 
						= $this->constructPayrollComputedInfo($res,$info,$eid,$arr_income_config,$arr_income_adj_config,$arr_fixeddeduc_config,$arr_deduc_config,$arr_loan_config);

				}else{ ///< compute

					list($info,$arr_income_config,$arr_income_adj_config,$arr_fixeddeduc_config,$arr_deduc_config,$arr_loan_config) 
						= $this->computeNewPayrollInfo($row,$schedule,$quarter,$sdate,$edate,$payroll_cutoff_id,$info,$arr_income_config,$arr_income_adj_config,$arr_fixeddeduc_config,$arr_deduc_config,$arr_deduc_config_arithmetic,$arr_loan_config);
					
				}
			} ///< end if SAVED

			$recomputed_emp_payroll += 1;
            $emplist_total_payroll = sizeof($emplist);

            $this->session->set_userdata('emplist_total_payroll', $emplist_total_payroll);
            $this->session->set_userdata('recomputed_emp_payroll', $recomputed_emp_payroll);

		} //end loop emplist

		$this->session->unset_userdata('emplist_total_payroll');
        $this->session->unset_userdata('recomputed_emp_payroll');

		$data['emplist'] = $info;
		$data['income_config'] = $arr_income_config;
		$data['income_adj_config'] = $arr_income_adj_config;
		$data['incomeoth_config'] = $arr_incomeoth_config;
		$data['fixeddeduc_config'] = $arr_fixeddeduc_config;
		$data['deduction_config'] = $arr_deduc_config;
		$data['loan_config'] = $arr_loan_config;

		return $data;

	}

	function constructPayrollComputedInfo($res,$info=array(),$eid='',$arr_income_config=array(),$arr_income_adj_config=array(),$arr_fixeddeduc_config=array(),$arr_deduc_config=array(),$arr_loan_config=array()){
		$res = $res->row(0);

		$info[$eid]['base_id'] 		= $res->id;

		$info[$eid]['tardy'] 		= $res->tardy;
		$info[$eid]['absents'] 		= $res->absents;
		$info[$eid]['whtax'] 		= $res->withholdingtax;
		$info[$eid]['salary'] 		= $res->salary;
		$info[$eid]['overtime'] 	= $res->overtime;

		//<!--NET BASIC PAY-->
		$info[$eid]['netbasicpay'] 	= $res->netbasicpay;;
		$info[$eid]['grosspay']    	= $res->gross;
		$info[$eid]['netpay']    	= $res->net;

		$info[$eid]['isHold']    	= $res->isHold;

		$income_adj_arr 				= $this->constructArrayListFromComputedTable($res->income_adj);
		$info[$eid]['income_adj'] = $income_adj_arr;
		foreach ($income_adj_arr as $k => $v) {$arr_income_adj_config[$k]['hasData'] = 1;}
		
		//< income
		$income_arr 				= $this->constructArrayListFromComputedTable($res->income);
		$info[$eid]['income'] = $income_arr;
		foreach ($income_arr as $k => $v) {$arr_income_config[$k]['hasData'] = 1;}

		///< fixed deduc
        $fixeddeduc_arr = $this->constructArrayListFromComputedTable($res->fixeddeduc);
        $info[$eid]['fixeddeduc'] = $fixeddeduc_arr;
        foreach ($fixeddeduc_arr as $k => $v) {$arr_fixeddeduc_config[$k]['hasData'] = 1;}

        ///< deduc
        $deduc_arr = $this->constructArrayListFromComputedTable($res->otherdeduc);
        $info[$eid]['deduction'] = $deduc_arr;
        foreach ($deduc_arr as $k => $v) {$arr_deduc_config[$k]['hasData'] = 1;}

        ///< loan
        $loan_arr = $this->constructArrayListFromComputedTable($res->loan);
        $info[$eid]['loan'] = $loan_arr;
        foreach ($loan_arr as $k => $v) {$arr_loan_config[$k]['hasData'] = 1;}
        return array($info,$arr_income_config,$arr_income_adj_config,$arr_fixeddeduc_config,$arr_deduc_config,$arr_loan_config);
	}

	function computeNewPayrollInfo($row,$schedule,$quarter,$sdate,$edate,$payroll_cutoff_id,$info,$arr_income_config,$arr_income_adj_config,$arr_fixeddeduc_config,$arr_deduc_config,$arr_deduc_config_arithmetic,$arr_loan_config){
		$this->load->model('payrollcomputation','comp');
		$this->load->model('income');
		$perdept_amt_arr = array();
		$workdays =	$absentdays = 0;
		$workhours_lec = $workhours_lab = $workhours_admin = "";

		$eid 		= $row->employeeid;
		$tnt 		= $row->teachingtype;
		$employmentstat = $row->employmentstat;
		$regpay 	=  $row->regpay;
		$daily 		=  $row->daily;
		// $hourly =  $row->hourly;
		$hourly 	= ($row->daily / 8);
		$lechour 	=  $row->lechour;
		$labhour 	=  $row->labhour;
		$fixedday 	=  $row->fixedday;
		$dependents = $row->dependents;
		$isFinal = '';

		$str_income = $str_income_adj = $str_fixeddeduc = $str_deduc = $str_loan = "";
		$total_deducSub= $totalincome= $totalincome_adj = $totalfix=$total_deducAdd=$totalloan = 0;

		if($tnt == 'teaching'){
			$perdept_salary = $this->comp->getPerdeptSalaryHistory($eid,$sdate);

			list($tardy_amount,$absent_amount,$workhours_lec,$workhours_lab,$workhours_admin,$perdept_amt_arr,$hold_status,$x,$x,$forFinalPay,$conf_base_id) = $this->comp->getTardyAbsentSummaryTeaching($eid,$tnt,$schedule,$quarter,$sdate,$edate,$hourly,$lechour,$labhour,$perdept_salary);
			$info[$eid]['salary'] 	= $this->comp->computeTeachingCutoffSalary($workhours_lec,$workhours_lab,$workhours_admin,$hourly,$lechour,$labhour,$fixedday,$regpay,$perdept_amt_arr,$hold_status);

		}else{
			list($tardy_amount,$absent_amount,$workdays,$x,$x,$conf_base_id, $isFinal) = $this->comp->getTardyAbsentSummaryNT($eid,$tnt,$schedule,$quarter,$sdate,$edate,$hourly);
			$info[$eid]['salary'] 	= $this->comp->computeNTCutoffSalary($workdays,$fixedday,$regpay,$daily);
		}

		///< pag wala attendance - wala salary,tardy,absent pero papasok pa rin sa payroll - maiiwan mga income nya (DOUBLE CHECKING)
		if( !$this->hasAttendanceConfirmed($tnt,array('employeeid'=>$eid,'status'=>'PROCESSED','forcutoff'=>'1','payroll_cutoffstart'=>$sdate,'payroll_cutoffend'=>$edate,'quarter'=>$quarter)) ){
			$info[$eid]['salary'] = $tardy_amount = $absent_amount = 0;
			$perdept_amt_arr = array();
		}

		// $info[$eid]['overtime'] = $this->comp->computeOvertime($eid,$tnt,$schedule,$quarter,$sdate,$edate,$hourly);  ///< TO DO : INCLUDE OVERTIME IN COMPUTATIONS (income, tax, gross pay , etc)
		list($info[$eid]['overtime'],$ot_det) = $this->comp->computeOvertime2($eid,$tnt,$hourly,$conf_base_id,$employmentstat);
		/*check cutoff if no late and undertime*/
		if($this->validateDTRCutoff($sdate, $edate)) $tardy_amount = $absent_amount = 0;

		$info[$eid]['tardy'] 		= $tardy_amount;
		$info[$eid]['absents'] 		= $absent_amount;

		///<  compute and save other income
		// $arr_adj_to_add = $this->comp->computeOtherIncomeAdj($eid,$payroll_cutoff_id);
		$this->comp->computeEmployeeOtherIncome($eid,$sdate,$edate,$tnt,$schedule,$quarter);
		// $this->comp->computeLongevity($eid,$sdate,$edate,$tnt,$schedule,$quarter);
		
		///< income
		list($arr_income_config,$info[$eid]['income'],$totalincome,$str_income) = $this->comp->computeEmployeeIncome($eid,$schedule,$quarter,$sdate,$edate,$arr_income_config,$payroll_cutoff_id);
		$getTotalNotIncludedInGrosspay = $this->getTotalNotIncludedInGrosspay($info[$eid]['income']);
		$getTotalNotIncludedInGrosspayPhil = $this->getTotalNotIncludedInGrosspayPhil($info[$eid]['income']);
		///< income adjustment
		list($arr_income_adj_config,$info[$eid]['income_adj'],$totalincome,$str_income_adj) = $this->comp->computeEmployeeIncomeAdj($eid,$schedule,$quarter,$sdate,$edate,$arr_income_adj_config,$totalincome,$payroll_cutoff_id);

		//<!--GROSS PAY-->
		$info[$eid]['grosspay'] = ($info[$eid]['salary'] + $totalincome + $info[$eid]['overtime'] - ($info[$eid]['absents']+ $info[$eid]['tardy']) );

		list($prevSalary,$prevGrosspay,$prevBasicPay) = $this->getPrevCutoffSalary(date('Y-m',strtotime($sdate)),$quarter,$eid);

		///< fixed deduc
		$getTotalIncludedInGrosspayPhil = $this->getTotalIncludedInGrosspayPhil($info[$eid]['income']);
		$basicPay = (($info[$eid]['salary'] + $getTotalIncludedInGrosspayPhil)  - ($info[$eid]['absents']+ $info[$eid]['tardy']));
		list($arr_fixeddeduc_config,$info[$eid]['fixeddeduc'],$totalfix,$str_fixeddeduc,$ee_er) = $this->comp->computeEmployeeFixedDeduc($eid,$schedule,$quarter,$sdate,$edate,$arr_fixeddeduc_config,$info[$eid],$prevSalary,$prevGrosspay,$getTotalNotIncludedInGrosspay,$basicPay, $prevBasicPay);

		///< loan
		list($arr_loan_config,$info[$eid]['loan'],$totalloan,$str_loan) = $this->comp->computeEmployeeLoan($eid,$schedule,$quarter,$sdate,$edate,$arr_loan_config);

		//<!--NET BASIC PAY-->
		$info[$eid]['netbasicpay'] = ($info[$eid]['salary']  - ($info[$eid]['absents']+ $info[$eid]['tardy']));

		if($isFinal){
			list($_13th_month, $employee_benefits) = $this->income->compute13thMonthPay_2($eid,date('Y',strtotime($sdate)),$sdate,$edate,$info[$eid]['netbasicpay'],$info[$eid]['income'], true, $regpay);
			if($_13th_month > 0) $this->income->saveEmployeeOtherIncome($eid,$sdate,$edate,'56',$_13th_month,$schedule,$quarter);
			if($employee_benefits > 0) $this->income->saveEmployeeOtherIncome($eid,$sdate,$edate,'37',$employee_benefits,$schedule,$quarter);

			///< income (RECOMPUTE TO INCLUDE 13TH MONTH PAY)
			list($arr_income_config,$info[$eid]['income'],$totalincome,$str_income) = $this->comp->computeEmployeeIncome($eid,$schedule,$quarter,$sdate,$edate,$arr_income_config,$payroll_cutoff_id);
		}

		///< deduction
		list($arr_deduc_config,$info[$eid]['deduction'],$total_deducSub,$total_deducAdd,$str_deduc) = $this->comp->computeEmployeeDeduction($eid,$schedule,$quarter,$sdate,$edate,$arr_deduc_config,$arr_deduc_config_arithmetic);

		///< TAX COMPUTATION
		$wh_tax = $this->comp->getExistingWithholdingTax($eid, $edate);
		if($wh_tax) $info[$eid]['whtax'] = $wh_tax;
		else $info[$eid]['whtax']  = $this->comp->computeWithholdingTax($schedule,$dependents,$info[$eid]['netbasicpay'],$info[$eid]['income'],$info[$eid]['income_adj'],$info[$eid]['deduction'],$info[$eid]['fixeddeduc'],$info[$eid]['overtime']);

		//<!--NET PAY-->
		// $info[$eid]['netpay'] = ($info[$eid]['grosspay'] - $totalloan - $totalfix - $total_deducSub - $info[$eid]['whtax'] + $total_deducAdd);
		$info[$eid]['netpay'] = ($info[$eid]['grosspay'] - $totalloan - $totalfix - $total_deducSub - $info[$eid]['whtax'] - $total_deducAdd);

		$info[$eid]['isHold'] = 0;
		///< save to computed table
		$data_tosave = $data_tosave_oth = array();
		$data_tosave['cutoffstart'] 	= $sdate;
		$data_tosave['cutoffend'] 		= $edate;
		$data_tosave['employeeid'] 		= $eid;
		$data_tosave['schedule'] 		= $schedule;
		$data_tosave['quarter'] 		= $quarter;
		$data_tosave['salary'] 			= $info[$eid]['salary'];
		$data_tosave['overtime'] 		= $info[$eid]['overtime'];
		$data_tosave['income'] 			= $str_income;
		$data_tosave['income_adj'] 		= $str_income_adj;
		$data_tosave['fixeddeduc'] 		= $str_fixeddeduc;
		$data_tosave['otherdeduc'] 		= $str_deduc;
		$data_tosave['loan'] 			= $str_loan;
		$data_tosave['withholdingtax'] 	= $info[$eid]['whtax'];
		$data_tosave['tardy'] 			= $info[$eid]['tardy'];
		$data_tosave['absents'] 		= $info[$eid]['absents'];
		$data_tosave['netbasicpay'] 	= $info[$eid]['netbasicpay'];
		$data_tosave['gross'] 			= $info[$eid]['grosspay'];
		$data_tosave['net'] 			= $info[$eid]['netpay'];
		$data_tosave['isHold'] 			= $info[$eid]['isHold'];

		$data_tosave_oth['perdept_amt_arr'] = $perdept_amt_arr;
		$data_tosave_oth['ee_er'] 		= $ee_er;
		$data_tosave_oth['ot_det'] 		= $ot_det;

		$info[$eid]['base_id'] = $this->savePayrollCutoffSummaryDraft($data_tosave,$data_tosave_oth);

		return array($info,$arr_income_config,$arr_income_adj_config,$arr_fixeddeduc_config,$arr_deduc_config,$arr_loan_config);
	}

	function hasAttendanceConfirmed($teachingtype='',$filter=array()){
		$hasData = false;
		$tbl = '';
		if($teachingtype == 'teaching') $tbl = 'attendance_confirmed';
		elseif($teachingtype == 'nonteaching') $tbl = 'attendance_confirmed_nt';
		if($tbl){
			$this->db->select('id');
			$res = $this->db->get_where($tbl,$filter);
			if($res->num_rows() > 0) $hasData = true;
		}
		return $hasData;
	}
	
	function getPayrollSummary($status='',$cutoffstart='',$cutoffend='',$schedule='',$quarter='',$employeeid='',$checkCount=false,$status2='',$bank=''){
		$wC = '';
		if($employeeid)					$wC .= " AND a.employeeid='$employeeid'";
		if($bank)						$wC .= " AND bank='$bank'";
		if($status && $status2) 		$wC .= " AND (status='$status' OR status='$status2')";
		elseif($status && !$status2)	$wC .= " AND status='$status'";

		if($checkCount){
			$cutoff_exist_q = $this->db->query("SELECT count(a.id) AS existcount, c.description as office_code, b.lname from payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid INNER JOIN code_office c ON b.office = c.code WHERE a.cutoffstart='$cutoffstart' AND a.cutoffend='$cutoffend' AND a.schedule='$schedule' AND a.quarter='$quarter' $wC ORDER BY c.description, b.lname");
			if($cutoff_exist_q->num_rows() > 0) return $cutoff_exist_q->row(0)->existcount;
			else 								return 0;
		}else{
			$payroll_q = $this->db->query("SELECT a.*, c.description as office_code, b.lname FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid INNER JOIN code_office c ON b.office = c.code WHERE a.cutoffstart='$cutoffstart' AND a.cutoffend='$cutoffend' AND a.schedule='$schedule' AND a.quarter='$quarter' $wC ORDER BY c.description, b.lname");
			return $payroll_q;
		}
	}

	function getPrevCutoffSalary($cutoff_month='',$quarter=1,$employeeid=''){
		$prevSalary = $prevGrosspay = $prevBasicPay = 0;
		if($cutoff_month){
			if($quarter > 1){
				$res = $this->db->query("SELECT salary,gross,tardy,absents,income FROM payroll_computed_table WHERE employeeid='$employeeid' AND DATE_FORMAT(cutoffstart,'%Y-%m')='$cutoff_month' AND quarter=1 LIMIT 1");
				if($res->num_rows() > 0){
					$prevSalary = $res->row(0)->salary;
					$prevGrosspay = $res->row(0)->gross;
					$prevTardy = $res->row(0)->tardy;
					$prevAbsents = $res->row(0)->absents;
					$prevIncome = $res->row(0)->income;
					$prevIncomeIncludedGross = $this->getTotalIncludedInGrosspayPhil($prevIncome);
					$prevBasicPay = ($prevSalary + $prevIncomeIncludedGross) - ($prevTardy + $prevAbsents);
				}
			}
		}else{

		}

		return array($prevSalary,$prevGrosspay,$prevBasicPay);
	}


	///< PENDING STATUS
	function savePayrollCutoffSummaryDraft($data=array(),$data_oth=array()){
		$this->load->model('utils');
		$data['addedby']   = $this->session->userdata('username');

		$base_id = $this->utils->insertSingleTblData('payroll_computed_table',$data);
		if($base_id){

			if(sizeof($data_oth['ee_er']) > 0){
				foreach ($data_oth['ee_er'] as $code => $amt) {
					$amt['EE'] = round($amt['EE'],2);
					$amt['EC'] = round($amt['EC'],2);
					$amt['ER'] = round($amt['ER'],2);
					$this->utils->insertSingleTblData('payroll_computed_ee_er',array('base_id'=>$base_id,'code_deduction'=>$code,'EE'=>$amt['EE'],'EC'=>$amt['EC'],'ER'=>$amt['ER'],'provident_er'=>$amt['provident_er']));
				}
			}

			if(sizeof($data_oth['perdept_amt_arr']) > 0){ ///< perdept amount details saving
				foreach ($data_oth['perdept_amt_arr'] as $aimsdept => $leclab_arr) {
					foreach ($leclab_arr as $type => $amt) {
						$this->utils->insertSingleTblData('payroll_computed_perdept_detail',array('base_id'=>$base_id,'type'=>$type,'aimsdept'=>$aimsdept,'work_amount'=>$amt['work_amount'],'late_amount'=>$amt['late_amount'],'deduc_amount'=>$amt['deduc_amount']));
					}
				}
			}

			if(sizeof($data_oth['ot_det']) > 0){
				foreach ($data_oth['ot_det'] as $att_baseid => $amt) {
					$amt = round($amt,2);
					$this->utils->insertSingleTblData('payroll_computed_overtime',array('base_id'=>$base_id,'att_baseid'=>$att_baseid,'amount'=>$amt));
				}
			}

		} //< end main if

		return $base_id;
	}

	///< SAVED STATUS
	function savePayrollCutoffSummary($empid = "",$cutoffstart="", $cutoffend="", $schedule = "",$quarter = "",$status="SAVED",$bank=''){
		$success = false;
		$update_res = $this->db->query("UPDATE payroll_computed_table SET  bank='$bank',status='$status'
										WHERE employeeid='$empid' AND schedule='$schedule' AND cutoffstart='$cutoffstart' AND cutoffend='$cutoffend' AND quarter='$quarter'");
		if($update_res) $success = true;
		return $success;
	}

	function saveEmpLoanPayment($pct_id, $employeeid, $cutoffstart, $cutoffend, $schedule, $quarter, $loans_list){
		$this->load->model('loan');
		$arr_loan = array();
		if($loans_list){
			foreach (explode("/", $loans_list) as $loans) {
				list($id, $amount) = explode("=", $loans);

				$arr_loan[$id] = $amount;
			}
		}

		if(count($arr_loan) > 0){
			foreach ($arr_loan as $code_loan => $loan_amount) {
				$q_emp_loan = $this->loan->getEmployeeLoanPayment($employeeid, $code_loan, $cutoffstart, $cutoffend, $schedule, $quarter);
				
				foreach ($q_emp_loan as $row) {
					$base_id = $row->id;

					$this->loan->processEmployeePayment($base_id, $loan_amount, $pct_id);
				}
				
			}
		}
	}

	///< PROCESSED STATUS
	function finalizePayrollCutoffSummary($empid = "",$cutoffstart="", $cutoffend="", $schedule = "",$quarter = ""){
		$user = $this->session->userdata('username');
		$update_res = $this->db->query("UPDATE payroll_computed_table SET status='PROCESSED' 
										WHERE employeeid='$empid' AND schedule='$schedule' AND cutoffstart='$cutoffstart' AND cutoffend='$cutoffend' AND quarter='$quarter'");
		// echo "<pre>"; print_r($this->db->last_query()); die;
		$success = false;

		if($update_res){
			$sel_res = $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid='$empid' AND schedule='$schedule' AND cutoffstart='$cutoffstart' AND cutoffend='$cutoffend' AND quarter='$quarter'");

			if($sel_res->num_rows() > 0){

				$pct_id 		= $sel_res->row(0)->id;
				$loans 			= $sel_res->row()->loan;
				$income 		= $sel_res->row()->income;
				$income_adj 		= $sel_res->row()->income_adj;
				$deductfixed 	= $sel_res->row()->fixeddeduc;
				$deductothers 	= $sel_res->row()->otherdeduc;
				$grosssalary 	=($sel_res->row()->salary + $sel_res->row()->income) - ($sel_res->row()->otherdeduc+$sel_res->row()->loan + $sel_res->row()->fixeddeduc);
				$netsalary 		=($sel_res->row()->salary + $sel_res->row()->income) - (($sel_res->row()->absents + $sel_res->row()->tardy));

				$this->saveEmpLoanPayment($pct_id, $empid, $cutoffstart, $cutoffend, $schedule, $quarter, $loans);

				$query = $this->db->query("INSERT INTO payroll_computed_table_history 
				                                    (employeeid,cutoffstart,cutoffend,schedule,quarter,salary,income,overtime,withholdingtax,fixeddeduc,otherdeduc,loan,tardy,absents,addedby) 
				                            (SELECT employeeid,cutoffstart,cutoffend,schedule,quarter,salary,income,overtime,withholdingtax,fixeddeduc,otherdeduc,loan,tardy,absents,'$user'
				                            FROM payroll_computed_table WHERE employeeid='$empid' AND schedule='$schedule' AND cutoffstart='$cutoffstart' AND cutoffend='$cutoffend' AND quarter='$quarter')
				                            ");


				$uptloan      =   explode("/",$loans);
				$uptincome    =   explode("/",$income);
				$uptincome_adj    =   explode("/",$income_adj);
				$uptcontri    =   explode("/",$deductfixed);
				$uptothded    =   explode("/",$deductothers);

				$this->finalizeLoan($empid,$schedule,$quarter,$cutoffstart,$cutoffend,$loans,$uptloan,$user);
				$this->finalizeIncome($empid,$schedule,$quarter,$cutoffstart,$cutoffend,$income,$uptincome,$user);
				$this->finalizeIncomeAdj($empid,$schedule,$quarter,$cutoffstart,$cutoffend,$income_adj,$uptincome_adj,$user);
				$this->finalizeFixedDeduction($empid,$schedule,$quarter,$cutoffstart,$cutoffend,$deductfixed,$uptcontri,$user);
				$this->finalizeOtherDeduction($empid,$schedule,$quarter,$cutoffstart,$cutoffend,$deductothers,$uptothded,$user);

				if($query) $success = true;

			}
		}

		return $success;
	}

	function finalizeLoan($eid='',$schedule = "",$quarter = "",$sdate = "",$edate = "",$loans='',$uptloan=array(),$user=''){
        if(count($uptloan) > 0 && !empty($loans)){
            for($x = 0; $x<count($uptloan); $x++){
                $code = explode("=",$uptloan[$x]);
                $qloan = $this->db->query("SELECT nocutoff,amount,famount FROM employee_loan WHERE employeeid='$eid' AND code_loan='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
                if($qloan->num_rows() > 0){
                    $nocutoff = $qloan->row(0)->nocutoff-1; 
                    $amount = $qloan->row(0)->amount; 
                    $famount = $qloan->row(0)->famount; 
                    if($nocutoff >= 0){
                        $qloan = $this->db->query("UPDATE employee_loan SET nocutoff='$nocutoff' WHERE employeeid='$eid' AND code_loan='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
                        $ploan = $this->db->query("INSERT INTO payroll_process_loan 
                                                            (employeeid,code_loan,cutoffstart,cutoffend,amount,schedule,cutoff_period,user) 
                                                    VALUES  ('$eid','".$code[0]."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user')
                                                    ");
                    
						$hloan = $this->db->query("SELECT * FROM employee_loan_history WHERE employeeid = '".$eid."' AND code_loan = '".$code[0]."' AND schedule='$schedule' ORDER BY cutoffstart DESC LIMIT 1");
						if($hloan->num_rows() > 0){
							if($nocutoff != 0){ 
								$balance = $hloan->row(0)->remainingBalance - $amount;
								$this->db->query("INSERT INTO employee_loan_history (employeeid,code_loan,cutoffstart,cutoffend,startBalance,amount,remainingBalance,schedule,cutoff_period,mode,user)
								VALUES('".$eid."','".$code[0]."','$sdate','$edate',".$hloan->row(0)->remainingBalance.",".$amount.",".$balance.",'".$schedule."','".$quarter."','CUTOFF','".$user."')");
							}
							else {
								$balance = $hloan->row(0)->remainingBalance - $famount;
								$this->db->query("INSERT INTO employee_loan_history (employeeid,code_loan,cutoffstart,cutoffend,startBalance,amount,remainingBalance,schedule,cutoff_period,mode,user)
								VALUES('".$eid."','".$code[0]."','$sdate','$edate',".$hloan->row(0)->remainingBalance.",".$famount.",".$balance.",'".$schedule."','".$quarter."','CUTOFF','".$user."')");
							}
						}
					}						
                }
            }
        }
	}

	function finalizeIncome($eid='',$schedule = "",$quarter = "",$sdate = "",$edate = "",$income='',$uptincome=array(),$user=''){
		if(count($uptincome) > 0 && !empty($income)){
		    for($x = 0; $x<count($uptincome); $x++){
		        $code = explode("=",$uptincome[$x]);
		        $qincome = $this->db->query("SELECT nocutoff FROM employee_income WHERE employeeid='$eid' AND code_income='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
		        if($qincome->num_rows() > 0){
		            $nocutoff = $qincome->row(0)->nocutoff-1; 
		            if($nocutoff >= 0){
		                $qincome = $this->db->query("UPDATE employee_income SET nocutoff='$nocutoff' WHERE employeeid='$eid' AND code_income='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
		                $pincome = $this->db->query("INSERT INTO payroll_process_income 
		                                                    (employeeid,code_income,cutoffstart,cutoffend,amount,schedule,cutoff_period,user) 
		                                            VALUES  ('$eid','".$code[0]."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user')
		                                            ");
		            } 
		        }
				$remainingCutoff = $this->db->query("SELECT * FROM employee_income WHERE employeeid = '$eid' AND code_income = '{$code[0]}' ");
				if($remainingCutoff->num_rows > 0){
					$remainingCutoff = $remainingCutoff->row()->nocutoff;
					if($remainingCutoff == 0) $this->db->query("DELETE FROM employee_income WHERE employeeid = '$eid' AND code_income = '{$code[0]}' ");
				}
		    }
		}

		
	}

	function finalizeIncomeAdj($eid='',$schedule = "",$quarter = "",$sdate = "",$edate = "",$income='',$uptincome=array(),$user=''){
		if(count($uptincome) > 0 && !empty($income)){
		    for($x = 0; $x<count($uptincome); $x++){
		        $code = explode("=",$uptincome[$x]);
		        $qincome = $this->db->query("SELECT nocutoff FROM employee_income_adj WHERE employeeid='$eid' AND code_income='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
		        if($qincome->num_rows() > 0){
		            $nocutoff = $qincome->row(0)->nocutoff-1; 
		            if($nocutoff >= 0){
		                $qincome = $this->db->query("UPDATE employee_income_adj SET nocutoff='$nocutoff' WHERE employeeid='$eid' AND code_income='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
		                $pincome = $this->db->query("INSERT INTO payroll_process_income_adj 
		                                                    (employeeid,code_income,cutoffstart,cutoffend,amount,schedule,cutoff_period,user) 
		                                            VALUES  ('$eid','".$code[0]."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user')
		                                            ");
		            } 
		        }
		    	$remainingCutoff = $this->db->query("SELECT * FROM employee_income_adj WHERE employeeid = '$eid' AND code_income = '{$code[0]}' ")->row()->nocutoff;
				if($remainingCutoff == 0) $this->db->query("DELETE FROM employee_income_adj WHERE employeeid = '$eid' AND code_income = '{$code[0]}' ");
		    }
		}
	}

	function finalizeFixedDeduction($eid='',$schedule = "",$quarter = "",$sdate = "",$edate = "",$deductfixed='',$uptcontri=array(),$user=''){
		if(count($uptcontri) > 0 && !empty($deductfixed)){
		    for($x = 0; $x<count($uptcontri); $x++){
		        $code = explode("=",$uptcontri[$x]);
		        list($tcontri,$er,$ec)   =  $this->payroll->payroll_collection_contribution($code[1]);
		            $pcontri = $this->db->query("INSERT INTO payroll_process_contribution 
		                                                    (employeeid,code_deduct,cutoffstart,cutoffend,amount,schedule,cutoff_period,user) 
		                                            VALUES  ('$eid','".strtoupper($code[0])."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user')
		                                        "); 
		                       $this->db->query("INSERT INTO payroll_process_contribution_collection 
		                                                    (employeeid,code_deduct,cutoffstart,cutoffend,amount,schedule,cutoff_period,user,ec,amounter,amounttotal) 
		                                            VALUES  ('$eid','".strtoupper($code[0])."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user','$ec','$er','$tcontri')
		                                        "); 
		                                        
		    }
		}
	}

	function finalizeOtherDeduction($eid='',$schedule = "",$quarter = "",$sdate = "",$edate = "",$deductothers='',$uptothded=array(),$user=''){
		if(count($uptothded) > 0 && !empty($deductothers)){
            for($x = 0; $x<count($uptothded); $x++){
                $code = explode("=",$uptothded[$x]);
                $qincome = $this->db->query("SELECT nocutoff FROM employee_deduction WHERE employeeid='$eid' AND code_deduction='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");
                if($qincome->num_rows() > 0){
                $nocutoff = $qincome->row(0)->nocutoff-1; 
                    if($nocutoff >= 0){
                        $qincome = $this->db->query("UPDATE employee_deduction SET nocutoff='$nocutoff' WHERE employeeid='$eid' AND code_deduction='".$code[0]."' AND schedule='$schedule' AND FIND_IN_SET(cutoff_period,'$quarter,3')");                                        
                        $pcontri = $this->db->query("INSERT INTO payroll_process_otherdeduct 
                                                                (employeeid,code_deduct,cutoffstart,cutoffend,amount,schedule,cutoff_period,user) 
                                                        VALUES  ('$eid','".strtoupper($code[0])."','$sdate','$edate','".$code[1]."','$schedule','$quarter','$user')
                                                    "); 
                    }             
                }                                                                                           
            	$remainingCutoff = $this->db->query("SELECT * FROM employee_deduction WHERE employeeid = '$eid' AND code_deduction = '{$code[0]}' ")->row()->nocutoff;
				if($remainingCutoff == 0) $this->db->query("DELETE FROM employee_deduction WHERE employeeid = '$eid' AND code_deduction = '{$code[0]}' ");
            }
        }
	}



	function getProcessedPayrollSummary($emplist=array(), $sdate='',$edate='',$schedule='',$quarter='',$status='PROCESSED',$bank=''){
		//< initialize needed info ---------------------------------------------------
		$arr_info    = $arr_income_config = $arr_incomeoth_config = $arr_deduc_config = $arr_fixeddeduc_config = $arr_loan_config = array();

		///< ------------------------------ income config ------------------------------------------------------------
		$income_config_q = $this->payroll->displayIncome();
		$arr_income_config = $this->constructArrayListFromStdClass($income_config_q,'id','description');

		$arr_income_adj_config = $arr_income_config;
		$arr_income_adj_config['SALARY'] = array('description'=>'SALARY','hasData'=>0);

		///< ------------------------------ incomeoth config ---------------------------------------------------------------
		$incomeoth_config_q = $this->payroll->displayIncomeOth();
		$arr_incomeoth_config = $this->constructArrayListFromStdClass($incomeoth_config_q,'id','description');

		///< ------------------------------ fixed deduction config ----------------------------------------------------
		$fixeddeduc_config_q = $this->db->query("SELECT code_deduction,description FROM deductions");
		$arr_fixeddeduc_config = $this->constructArrayListFromStdClass($fixeddeduc_config_q,'code_deduction','description');


		///< ------------------------------ deduction config ----------------------------------------------------------
		$deduction_config_q = $this->payroll->displayDeduction();
		$arr_deduc_config = $this->constructArrayListFromStdClass($deduction_config_q,'id','description');


		///< ------------------------------ loan config ---------------------------------------------------------------
		$loan_config_q = $this->payroll->displayLoan();
		$arr_loan_config = $this->constructArrayListFromStdClass($loan_config_q,'id','description');


		foreach ($emplist as $row) {
			$empid = $row->employeeid;

			///< check for computation
			$res = $this->getPayrollSummary($status,$sdate,$edate,$schedule,$quarter,$empid,false,'',$bank);

			if($res->num_rows() > 0){

				$regpay =  $row->regpay;
				$dependents = $row->dependents;

				$arr_info[$empid]['income'] = $arr_info[$empid]['income_adj'] = $arr_info[$empid]['deduction'] = $arr_info[$empid]['fixeddeduc'] = $arr_info[$empid]['loan'] = array();

				$arr_info[$empid]['fullname'] 	= $row->fullname;
				$res 							= $res->row(0);

				$arr_info[$empid]['base_id'] 	= $res->id;

				$arr_info[$empid]['salary'] 	= $res->salary;
				$arr_info[$empid]['overtime'] 	= $res->overtime;
				$arr_info[$empid]['tardy'] 		= $res->tardy;
				$arr_info[$empid]['absents'] 	= $res->absents;
				$arr_info[$empid]['whtax'] 		= $res->withholdingtax;
				$arr_info[$empid]['editedby'] 	= $res->editedby;
				$arr_info[$empid]['netbasicpay'] = $res->netbasicpay;
				$arr_info[$empid]['grosspay'] 	= $res->gross;
				$arr_info[$empid]['netpay'] 	= $res->net;
				$arr_info[$empid]['isHold'] 	= $res->isHold;

				//< income
				$income_arr 				= $this->constructArrayListFromComputedTable($res->income);
				$arr_info[$empid]['income'] = $income_arr;
				foreach ($income_arr as $k => $v) {$arr_income_config[$k]['hasData'] = 1;}

				$income_adj_arr 				= $this->constructArrayListFromComputedTable($res->income_adj);
				$arr_info[$empid]['income_adj'] = $income_adj_arr;
				foreach ($income_adj_arr as $k => $v) {$arr_income_adj_config[$k]['hasData'] = 1;}

				///< fixed deduc
		        $fixeddeduc_arr = $this->constructArrayListFromComputedTable($res->fixeddeduc);
		        $arr_info[$empid]['fixeddeduc'] = $fixeddeduc_arr;
		        foreach ($fixeddeduc_arr as $k => $v) {$arr_fixeddeduc_config[$k]['hasData'] = 1;}

		        ///< deduc
		        $deduc_arr = $this->constructArrayListFromComputedTable($res->otherdeduc);
		        $arr_info[$empid]['deduction'] = $deduc_arr;
		        foreach ($deduc_arr as $k => $v) {$arr_deduc_config[$k]['hasData'] = 1;}

		        ///< loan
		        $loan_arr = $this->constructArrayListFromComputedTable($res->loan);
		        $arr_info[$empid]['loan'] = $loan_arr;
		        foreach ($loan_arr as $k => $v) {$arr_loan_config[$k]['hasData'] = 1;}


			}

		} //end loop emplist

		$data['emplist'] = $arr_info;
		$data['income_config'] = $arr_income_config;
		$data['income_adj_config'] = $arr_income_adj_config;
		$data['incomeoth_config'] = $arr_incomeoth_config;
		$data['fixeddeduc_config'] = $arr_fixeddeduc_config;
		$data['deduction_config'] = $arr_deduc_config;
		$data['loan_config'] = $arr_loan_config;
		$data['sdate'] = $sdate;
		$data['edate'] = $edate;

		return $data;
	}


	function getAtmPayrolllist($emp_bank='', $cutoffstart, $status = '', $sort_by=''){
		$where_clause = $orderby = '';
        if($sort_by == "department") $orderby = "ORDER BY a.office";
        else $orderby = "ORDER BY a.lname"; 
		if($emp_bank) $where_clause = " AND c.`bank`='$emp_bank' ";

		$res = $this->db->query("SELECT a.employeeid, lname, mname, fname, c.`bank`, c.`net`, a.emp_accno
							FROM employee a
							INNER JOIN payroll_computed_table c ON c.`employeeid`=a.`employeeid`
							WHERE c.`status` = 'PROCESSED' AND cutoffstart='$cutoffstart' $where_clause $orderby");
		$data = array();
		if($res->num_rows() > 0){
			foreach ($res->result() as $key => $row) {
				$employee_bank = $this->getEmployeeBank($row->employeeid);
				$bank = $row->bank;
				$emp_accno = isset($employee_bank[$bank]) ? $employee_bank[$bank] : $row->emp_accno;
				$fullname = $row->lname . ' ' . $row->fname . ' ' . substr($row->mname, 0,1) . '.';
				$data['list'][$row->employeeid] = array('fullname'=>utf8_encode($fullname),'account_num'=>$emp_accno,'net_salary'=>$row->net);
			}
		}

		$b_q = $this->payroll->displayBankList($emp_bank);

		$data['branch'] = '';
		$data['bank_name'] = '';

		if($b_q->num_rows() > 0){
			$data['branch'] = $b_q->row(0)->branch;
			$data['bank_name'] = $b_q->row(0)->bank_name;
		}


		return $data;

	}

	function getEmployeeBank($employeeid=""){
		$bank_arr = array();
		$q_bank = $this->db->query("SELECT emp_bank FROM employee WHERE employeeid = '$employeeid'");
		if($q_bank->num_rows() > 0){
			$bank = $q_bank->row()->emp_bank;
			if($bank){
		        $str_arr = explode('/', $bank);
		        if(count($str_arr)){
		            foreach ($str_arr as $i_temp) {
		                $str_arr_temp = explode('=', $i_temp);
		                if(isset($str_arr_temp[0]) && isset($str_arr_temp[1])){
		                    $bank_arr[$str_arr_temp[0]] = $str_arr_temp[1];
		                }
		            }
		        }
		    }
		}

		return $bank_arr;
	}

	///< Reglamentory Payment
	function getReglamentoryPaymentComputed($id='',$base_id='',$code_deduction=''){
		$wC = '';
		if($id)				$wC .= " AND id='$id'";
		if($base_id)		$wC .= " AND base_id='$base_id'";
		if($code_deduction)	$wC .= " AND code_deduction='$code_deduction'";
		$res = $this->db->query("SELECT * FROM payroll_computed_ee_er WHERE EE <> 0 $wC");
		return $res;
	}

	function updateComputedEE_ORNum($id='',$base_id='',$code_deduction='',$or_number='',$datepaid='',$cutoff=''){
		$wC = "";
		$wC_arr = array();
		if($id) 			array_push($wC_arr, "id='$id'");
		if($base_id) 		array_push($wC_arr, "base_id='$base_id'");
		if($code_deduction)	array_push($wC_arr, "code_deduction='$code_deduction'");
		if(sizeof($wC_arr) > 0){
			$wC = " WHERE " . implode(' AND ', $wC_arr);
		}

		$update = "";
		if(!$datepaid)	$update .= " ,datepaid=NULL";
		else 			$update .= " ,datepaid='$datepaid'";
		
		/*if(!$cutoff)	$update .= " ,cutoff=NULL";
		else			$update .= " ,cutoff='$cutoff'";*/

		$res = $this->db->query("UPDATE payroll_computed_ee_er SET or_number='$or_number' $update $wC");
		return $res;
	}

	function checkDeductionIfWithtax($key){
		$deduc_query = $this->db->query("SELECT taxable FROM payroll_deduction_config WHERE id = '$key'")->row()->taxable;
		return $deduc_query;
	}

	function checkIfPayrollSaved($payroll_start, $payroll_end, $employeeid){
		$query = $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid = '$employeeid' AND cutoffstart = '$payroll_start' AND cutoffend = '$payroll_end' ");
		if($query->num_rows() > 0){
			return $query->row()->status;
		}else{
			return FALSE;
		}
	}

	function getTotalNotIncludedInGrosspay($arr_income){
		$income = $this->extensions->getNotIncludedInGrosspayIncome();
		if(!is_array($arr_income)) $arr_income = $this->constructArrayListFromComputedTable($arr_income);
		$total = 0;
		foreach($arr_income as $inc_key => $value){
			if(array_key_exists($inc_key, $income)) $total += $value;
		}

		return $total;
	}

	function getTotalNotIncludedInGrosspayPhil($arr_income){
		$income = $this->extensions->getNotIncludedInGrosspayIncomePhil();
		if(!is_array($arr_income)) $arr_income = $this->constructArrayListFromComputedTable($arr_income);
		$total = 0;
		foreach($arr_income as $inc_key => $value){
			if(array_key_exists($inc_key, $income)) $total += $value;
		}

		return $total;
	}

	function getTotalIncludedInGrosspayPhil($arr_income){
		$income = $this->extensions->getNotIncludedInGrosspayIncomePhil();
		if(!is_array($arr_income)) $arr_income = $this->constructArrayListFromComputedTable($arr_income);
		$total = 0;
		foreach($arr_income as $inc_key => $value){
			if(!array_key_exists($inc_key, $income)) $total += $value;
		}

		return $total;
	}

	function validateDTRCutoff($sdate, $edate){
		$q_cutoff = $this->db->query("SELECT * FROM payroll_cutoff_config WHERE startdate = '$sdate' AND enddate = '$edate' ");
		return ($q_cutoff->row()->nodtr) ? true : false;
	}
	
	function getAbsentPerdept($empid,$cutoffstart='',$cutoffend=''){
		$query_perdeptAbsent = $this->db->query("SELECT * FROM attendance_confirmed a 
											INNER JOIN workhours_perdept b ON b.`base_id` = a.`id` 
											WHERE a.payroll_cutoffstart='$cutoffstart' 
											AND a.payroll_cutoffend='$cutoffend' 
											AND a.employeeid='$empid' ")->result_array();
		return $query_perdeptAbsent;
	}

	function getAbsentNonteaching($empid,$cutoffstart='',$cutoffend=''){
		$query_perdeptAbsent = $this->db->query("SELECT * FROM attendance_confirmed_nt 
											WHERE payroll_cutoffstart='$cutoffstart' 
											AND payroll_cutoffend='$cutoffend' 
											AND employeeid='$empid' ")->result_array();
		return $query_perdeptAbsent;
	}	

} //endoffile