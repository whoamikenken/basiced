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

	function processPayrollSummary($emplist=array(),$sdate='',$edate='',$schedule='',$quarter='',$recompute=false){

		$workdays = 0;
		$workhours_lec = $workhours_lab = $workhours_admin = "";

		//< initialize needed info ---------------------------------------------------
		$arr_info    = $arr_income_config = $arr_incomeoth_config = $arr_deduc_config = $arr_fixeddeduc_config = $arr_loan_config = array();

		///< ------------------------------ income config ------------------------------------------------------------
		$income_config_q = $this->payroll->displayIncome();
		$arr_income_config = $this->constructArrayListFromStdClass($income_config_q,'id','description');

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


		foreach ($emplist as $row) {
			
			$empid = $row->employeeid;
			$tnt = $row->teachingtype;
			$regpay =  $row->regpay;
			$daily =  $row->daily;
			$hourly =  $row->hourly;
			$lechour =  $row->lechour;
			$labhour =  $row->labhour;
			$fixedday =  $row->fixedday;
			$dependents = $row->dependents;

			$arr_info[$empid]['income'] = $arr_info[$empid]['deduction'] = $arr_info[$empid]['fixeddeduc'] = $arr_info[$empid]['loan'] = array();

			$arr_info[$empid]['fullname'] 	= $row->fullname;


			if($recompute === true) $this->db->query("DELETE FROM payroll_computed_table WHERE cutoffstart='$sdate' AND cutoffend='$edate' AND schedule='$schedule' AND quarter='$quarter' AND employeeid='$empid' AND status='PENDING'");
			
			///< check for pending computation, if true - display directly, else compute payroll first
			$res = $this->getPayrollSummary('',$sdate,$edate,$schedule,$quarter,$empid);

			if($res->num_rows() > 0){
				$res 							= $res->row(0);

				$arr_info[$empid]['tardy'] 		= $res->tardy;
				$arr_info[$empid]['absents'] 	= $res->absents;
				$arr_info[$empid]['whtax'] 		= $res->withholdingtax;
				$arr_info[$empid]['salary'] 	= $res->salary;

				//<!--NET BASIC PAY-->
				$arr_info[$empid]['netbasicpay'] = ($res->salary  - ($res->absents+ $res->tardy));
				$arr_info[$empid]['grosspay']    = $res->gross;
				$arr_info[$empid]['netpay']    	 = $res->net;

				//< income
				$income_arr 				= $this->constructArrayListFromComputedTable($res->income);
				$arr_info[$empid]['income'] = $income_arr;
				foreach ($income_arr as $k => $v) {$arr_income_config[$k]['hasData'] = 1;}

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


			}else{
				$str_income = $str_fixeddeduc = $str_deduc = $str_loan = "";
				$total_deducSub= $totalincome = $totalfix=$total_deducAdd=$totalloan=0;


				if($tnt == 'teaching'){
					list($tardy_amount,$absent_amount,$workhours_lec,$workhours_lab,$workhours_admin) = $this->getTardyAbsentSummaryTeaching($empid,$tnt,$schedule,$quarter,$sdate,$edate,$hourly,$lechour,$labhour);
					$arr_info[$empid]['salary'] 	= $this->computeTeachingCutoffSalary($workhours_lec,$workhours_lab,$workhours_admin,$hourly,$lechour,$labhour,$fixedday,$regpay);

				}else{
					list($tardy_amount,$absent_amount,$workdays) = $this->getTardyAbsentSummaryNT($empid,$tnt,$schedule,$quarter,$sdate,$edate,$hourly);
					$arr_info[$empid]['salary'] 	= $this->computeNTCutoffSalary($workdays,$fixedday,$regpay,$daily);
				}

				$arr_info[$empid]['tardy'] 		= $tardy_amount;
				$arr_info[$empid]['absents'] 	= $absent_amount;
				

				$res = $this->payrolloptions->incometitle($empid,'amount',$schedule,$quarter,'',$sdate,$edate);
				foreach ($res->result() as $key => $row) {
					$arr_info[$empid]['income'][$row->code_income] = $row->title;
					$totalincome += $row->title;
					$arr_income_config[$row->code_income]['hasData'] = 1;
					if($str_income) $str_income .= '/';
					$str_income .= $row->code_income . '=' . $row->title;
				}

				//<!--GROSS PAY-->
				$arr_info[$empid]['grosspay'] = ($arr_info[$empid]['salary'] + $totalincome  - ($arr_info[$empid]['absents']+ $arr_info[$empid]['tardy']) );

				$res = $this->payrolloptions->getEmpFixedDeduc($empid,'amount','HIDDEN',$schedule,$quarter,'',$sdate,$edate);

				foreach ($res->result() as $key => $row) {
					$amount_fx = $row->title;

					if($row->code_deduction == 'PHILHEALTH'){
						$amount_fx = $this->computePHILHEALTHContri($arr_info[$empid]['salary'] * 2);
					}

					$arr_info[$empid]['fixeddeduc'][$row->code_deduction] = $amount_fx;
					$totalfix += $amount_fx;

					$arr_fixeddeduc_config[$row->code_deduction]['hasData'] = 1;
					if($str_fixeddeduc) $str_fixeddeduc .= '/';
					$str_fixeddeduc .= $row->code_deduction . '=' . $amount_fx;
				}

				$res = $this->payrolloptions->deducttitle($empid,'amount','SHOW',$schedule,$quarter,'',$sdate,$edate);

				foreach ($res->result() as $key => $row) {
					$arr_info[$empid]['deduction'][$row->code_deduction] = $row->title;
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


				$res = $this->payrolloptions->loantitle($empid,'amount',$schedule,$quarter,'',$sdate,$edate);
					
				foreach ($res->result() as $key => $row) {
					$arr_info[$empid]['loan'][$row->code_loan] = $row->title;
					$totalloan += $row->title;
					$arr_loan_config[$row->code_loan]['hasData'] = 1;
					if($str_loan) $str_loan .= '/';
					$str_loan .= $row->code_loan . '=' . $row->title;
				}
				
				//<!--NET BASIC PAY-->
				$arr_info[$empid]['netbasicpay'] = ($arr_info[$empid]['salary']  - ($arr_info[$empid]['absents']+ $arr_info[$empid]['tardy']));

				///< TAX COMPUTATION
				$arr_info[$empid]['whtax']  = $this->computeWithholdingTax($schedule,$dependents,$arr_info[$empid]['salary'],$arr_info[$empid]['income'],$arr_info[$empid]['deduction'],$arr_info[$empid]['fixeddeduc']);

				//<!--NET PAY-->
				$arr_info[$empid]['netpay'] = ($arr_info[$empid]['grosspay'] - $totalloan - $totalfix - $total_deducSub - $arr_info[$empid]['whtax'] + $total_deducAdd);

				///< save to computed table
				$data_tosave = array();
				$data_tosave['dfrom'] 			= $sdate;
				$data_tosave['dto'] 			= $edate;
				$data_tosave['eid'] 			= $empid;
				$data_tosave['schedule'] 		= $schedule;
				$data_tosave['quarter'] 		= $quarter;
				$data_tosave['regularpay'] 		= $arr_info[$empid]['salary'];
				$data_tosave['ottime'] 			= '';
				$data_tosave['income'] 			= $str_income;
				$data_tosave['deductfixed'] 	= $str_fixeddeduc;
				$data_tosave['deductothers'] 	= $str_deduc;
				$data_tosave['loans'] 			= $str_loan;
				$data_tosave['withholding'] 	= $arr_info[$empid]['whtax'];
				$data_tosave['tardy'] 			= $arr_info[$empid]['tardy'];
				$data_tosave['absents'] 		= $arr_info[$empid]['absents'];
				$data_tosave['netbasicpay'] 	= $arr_info[$empid]['netbasicpay'];
				$data_tosave['grosspay'] 		= $arr_info[$empid]['grosspay'];
				$data_tosave['netpay'] 			= $arr_info[$empid]['netpay'];


				$this->savePayrollCutoffSummaryDraft($data_tosave);

			}

		} //end loop emplist

		$data['emplist'] = $arr_info;
		$data['income_config'] = $arr_income_config;
		$data['incomeoth_config'] = $arr_incomeoth_config;
		$data['fixeddeduc_config'] = $arr_fixeddeduc_config;
		$data['deduction_config'] = $arr_deduc_config;
		$data['loan_config'] = $arr_loan_config;

		return $data;

	}



	function getPayrollSummary($status='',$cutoffstart='',$cutoffend='',$schedule='',$quarter='',$employeeid='',$checkCount=false){
		$wC = '';
		if($employeeid)	$wC .= " AND employeeid='$employeeid'";
		if($status)		$wC .= " AND status='$status'";

		if($checkCount){
			$cutoff_exist_q = $this->db->query("SELECT count(id) AS existcount from payroll_computed_table WHERE cutoffstart='$cutoffstart' AND cutoffend='$cutoffend' AND schedule='$schedule' AND quarter='$quarter' $wC");
			if($cutoff_exist_q->num_rows() > 0) return $cutoff_exist_q->row(0)->existcount;
			else 								return 0;
		}else{
			$payroll_q = $this->db->query("SELECT * FROM payroll_computed_table WHERE cutoffstart='$cutoffstart' AND cutoffend='$cutoffend' AND schedule='$schedule' AND quarter='$quarter' $wC");
			return $payroll_q;
		}
	}


	function computeTeachingCutoffSalary($workhours_lec='',$workhours_lab='',$workhours_admin='',$hourly=0,$lechour=0,$labhour=0,$fixedday=0,$regpay=0){
		$salary = 0;

		$min_lec = $lechour / 60;
		$min_lab = $labhour / 60;
		$min_admin = $hourly / 60;

		$workminlec = $this->time->hoursToMinutes($workhours_lec);
		$workminlab = $this->time->hoursToMinutes($workhours_lab);
		$workminadmin = $this->time->hoursToMinutes($workhours_admin);

		$salary = ( $workminlec * $min_lec ) + ( $workminlab * $min_lab ); 

		if($fixedday){
			$salary += $regpay;
		}else{
			$salary += ( $workminadmin * $min_admin );
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

	/**
	 * Compute withholding tax. Taxable income = Salary + taxable income - included deductions - fixed deduc.
	 * Refer to ticket# ICA-Hyperion21063
	 *
	 * @return Float
	 */
	function computeWithholdingTax($schedule='',$dependents='',$regpay='',$arr_income,$arr_deduc,$arr_fixeddeduc){
		$whtax = $total_income = $total_deduc = $total_fixeddeduc =  $total_taxable = 0;

		///< get total taxable income first

		$income_config_q = $this->payroll->displayIncome();
		$arr_income_config = $this->constructArrayListFromStdClass($income_config_q,'id','taxable');
		$deduction_config_q = $this->payroll->displayDeduction();
		$arr_deduc_config = $this->constructArrayListFromStdClass($deduction_config_q,'id','arithmetic');

		if(sizeof($arr_income) > 0){
			foreach ($arr_income as $key => $value) {
				if($arr_income_config[$key]['description'] == 'withtax') $total_income += $value;
			}
		}
		if(sizeof($arr_deduc) > 0){
			foreach ($arr_deduc as $key => $value) {
				if($arr_deduc_config[$key]['description'] == 'sub') $total_deduc += $value;
			}
		}
		if(sizeof($arr_fixeddeduc) > 0){
			foreach ($arr_fixeddeduc as $key => $value) {
				$total_fixeddeduc += $value; ///< fixed deductions are subtracted automatically
			}
		}

		$total_taxable = $regpay + $total_income - $total_fixeddeduc;

		$tax_config_q = $this->db->query("SELECT * FROM code_tax WHERE tax_type='$schedule' AND status_='$dependents' AND tax_range <= '$total_taxable' ORDER BY tax_range DESC LIMIT 1");

		if($tax_config_q->num_rows() > 0){
			$tax_config = $tax_config_q->row(0);

			if(is_numeric($regpay) && is_numeric($tax_config->tax_range) && is_numeric($regpay) && is_numeric($tax_config->percent) && is_numeric($tax_config->basic_tax)){

				$whtax = (( $total_taxable - $tax_config->tax_range ) * ($tax_config->percent/100) ) + $tax_config->basic_tax;
			}
		}
		return $whtax;
	}

	function computePHILHEALTHContri($monthlySalary=0){
		$contri = 0;
		if($monthlySalary <= 10000) $contri = 275;
		elseif($monthlySalary > 10000 && $monthlySalary <= 40000) $contri = $monthlySalary * 0.0275;
		elseif($monthlySalary > 40000) $contri = 1100;

		return $contri / 2; ///< for employee and employer
	}

	function getTardyAbsentSummaryTeaching($empid = "",$ttype="",$schedule = "",$quarter = "",$sdate = "",$edate = "",$hourly=0,$lechour=0,$labhour=0){
		$tardy_amount = $absent_amount = $tardy_lec = $tardy_lab = $tardy_admin = $absent_lec = $absent_lab = $absent_admin = 0;
		$workhours_lec = $workhours_lab = $workhours_admin = '';

		$min_lec = $lechour / 60;
		$min_lab = $labhour / 60;
		$min_admin = $hourly / 60;
		
			    
    	$detail_q = $this->db->query("SELECT latelec, latelab, lateadmin, deduclec, deduclab, deducadmin, workhours_lec, workhours_lab, workhours_admin FROM attendance_confirmed WHERE employeeid='$empid' AND payroll_cutoffstart='$sdate' AND payroll_cutoffend='$edate'");

    	if($detail_q->num_rows() > 0){
    		$tlec 		= $detail_q->row(0)->latelec;
    		$tlab 		= $detail_q->row(0)->latelab;
    		$tadmin	 	= $detail_q->row(0)->lateadmin;
    		$tdlec 		= $detail_q->row(0)->deduclec;
    		$tdlab 		= $detail_q->row(0)->deduclab;
    		$tdadmin 	= $detail_q->row(0)->deducadmin;

    		$workhours_lec 	= $detail_q->row(0)->workhours_lec;
    		$workhours_lab 	= $detail_q->row(0)->workhours_lab;
    		$workhours_admin 	= $detail_q->row(0)->workhours_admin;

	        $tardy_lec = $this->attcompute->exp_time($tlec);
	        $tardy_lab = $this->attcompute->exp_time($tlab);
	        $tardy_admin = $this->attcompute->exp_time($tadmin);

	        $absent_lec = $this->attcompute->exp_time($tdlec);
	        $absent_lab = $this->attcompute->exp_time($tdlab);
	        $absent_admin = $this->attcompute->exp_time($tdadmin);
    	}
			   


	    $tardy_lec      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($tardy_lec));
	    $tardy_lab      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($tardy_lab));
	    $tardy_admin      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($tardy_admin));

	    $absent_lec      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($absent_lec));
	    $absent_lab      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($absent_lab));
	    $absent_admin      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($absent_admin));

	    $tardy_amount_lec = $tardy_lec * $min_lec;
	    $tardy_amount_lab = $tardy_lab * $min_lab;
	    $tardy_amount_admin = $tardy_admin * $min_admin;

	    $absent_amount_lec = $absent_lec * $min_lec;
	    $absent_amount_lab = $absent_lab * $min_lab;
	    $absent_amount_admin = $absent_admin * $min_admin;


	    $tardy_amount     = number_format($tardy_lec + $tardy_lab + $tardy_admin ,2,'.', '');

	    $absent_amount     = number_format($absent_lec + $absent_lab + $absent_admin ,2,'.', '');


		return array($tardy_amount,$absent_amount,$workhours_lec,$workhours_lab,$workhours_admin);
	}

	function getTardyAbsentSummaryNT($empid = "",$ttype="",$schedule = "",$quarter = "",$sdate = "",$edate = "",$hourly=0){
		$tardy_amount = $absent_amount = $tardy = $absent = 0;
		$workdays = 0;

		$minutely = $hourly / 60;
	  
    	$detail_q = $this->db->query("SELECT lateut, absent, workdays FROM attendance_confirmed_nt WHERE employeeid='$empid' AND payroll_cutoffstart='$sdate' AND payroll_cutoffend='$edate'");

    	if($detail_q->num_rows() > 0){
    		$tlec 		= $detail_q->row(0)->lateut;
    		$tabsent 	= $detail_q->row(0)->absent;

    		$workdays 	= $detail_q->row(0)->workdays;

	        $tardy = $this->attcompute->exp_time($tlec);
	        $absent = $this->attcompute->exp_time($tabsent);
    	}


	    $tardy      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($tardy));
	    $absent      	= $this->time->hoursToMinutes($this->attcompute->sec_to_hm($absent));

	    $tardy_amount     = number_format($tardy * $minutely,2,'.', '');
	    $absent_amount     = number_format($absent * $minutely,2,'.', '');


		return array($tardy_amount,$absent_amount,$workdays);
	}

	function savePayrollCutoffSummaryDraft($data){
		$sdate          = $data['dfrom'];
		$edate          = $data['dto'];
		$eid            = $data['eid'];  
		$schedule       = $data['schedule'];
		$quarter        = $data['quarter'];
		$regularpay     = $data['regularpay'];
		$income         = $data['income'];
		$ottime         = $data['ottime'];
		$withholding    = $data['withholding'];
		$deductfixed    = $data['deductfixed'];
		$loans          = $data['loans'];
		$deductothers   = $data['deductothers'];
		$tardy          = $data['tardy'];
		$absents        = $data['absents'];
		$netbasicpay 	= $data['netbasicpay'];
		$grosspay       = $data['grosspay'];
		$netpay       = $data['netpay'];
		$grosssalary 	= ($data['regularpay'] + $data['income']) - ($data['deductothers']+$data['deductfixed'] + $data['loans']);
		$netsalary 		= ($data['regularpay'] + $data['income']) - (($data['absents'] + $data['tardy']));
		$user           = $this->session->userdata('username');

		$query_res = $this->db->query("INSERT INTO payroll_computed_table 
                                                (employeeid,cutoffstart,cutoffend,schedule,quarter,salary,income,overtime,withholdingtax,fixeddeduc,otherdeduc,loan,tardy,absents,addedby,gross,net,netbasicpay) 
                                        VALUES  ('$eid','$sdate','$edate','$schedule','$quarter','$regularpay','$income','$ottime','$withholding','$deductfixed','$deductothers','$loans','$tardy','$absents','$user','$grosspay','$netpay','$netbasicpay')");

		return $query_res;
	}

	function finalizePayrollCutoffSummary($empid = "",$cutoffstart="", $cutoffend="", $schedule = "",$quarter = ""){
		$user           = $this->session->userdata('username');
		$update_res = $this->db->query("UPDATE payroll_computed_table SET status='PROCESSED' WHERE employeeid='$empid' AND schedule='$schedule' AND cutoffstart='$cutoffstart' AND cutoffend='$cutoffend' AND quarter='$quarter'");
		$success = false;

		if($update_res){
			$sel_res = $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid='$empid' AND schedule='$schedule' AND cutoffstart='$cutoffstart' AND cutoffend='$cutoffend' AND quarter='$quarter'");

			if($sel_res->num_rows() > 0){

				$loans 			= $sel_res->row()->loan;
				$income 		= $sel_res->row()->income;
				$deductfixed 	= $sel_res->row()->fixeddeduc;
				$deductothers 	= $sel_res->row()->otherdeduc;
				$grosssalary 	= ($sel_res->row()->salary + $sel_res->row()->income) - ($sel_res->row()->otherdeduc+$sel_res->row()->loan + $sel_res->row()->fixeddeduc);
				$netsalary 		= ($sel_res->row()->salary + $sel_res->row()->income) - (($sel_res->row()->absents + $sel_res->row()->tardy));

				$query = $this->db->query("INSERT INTO payroll_computed_table_history 
				                                    (employeeid,cutoffstart,cutoffend,schedule,quarter,salary,income,overtime,withholdingtax,fixeddeduc,otherdeduc,loan,tardy,absents,addedby) 
				                            (SELECT employeeid,cutoffstart,cutoffend,schedule,quarter,salary,income,overtime,withholdingtax,fixeddeduc,otherdeduc,loan,tardy,absents,'$user'
				                            FROM payroll_computed_table WHERE employeeid='$empid' AND schedule='$schedule' AND cutoffstart='$cutoffstart' AND cutoffend='$cutoffend' AND quarter='$quarter')
				                            ");


				$uptloan      =   explode("/",$loans);
				$uptincome    =   explode("/",$income);
				$uptcontri    =   explode("/",$deductfixed);
				$uptothded    =   explode("/",$deductothers);

				$this->finalizeLoan($empid,$schedule,$quarter,$cutoffstart,$cutoffend,$loans,$uptloan,$user);
				$this->finalizeIncome($empid,$schedule,$quarter,$cutoffstart,$cutoffend,$income,$uptincome,$user);
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
            }
        }
	}



	function getProcessedPayrollSummary($emplist=array(), $sdate='',$edate='',$schedule='',$quarter=''){
		//< initialize needed info ---------------------------------------------------
		$arr_info    = $arr_income_config = $arr_incomeoth_config = $arr_deduc_config = $arr_fixeddeduc_config = $arr_loan_config = array();

		///< ------------------------------ income config ------------------------------------------------------------
		$income_config_q = $this->payroll->displayIncome();
		$arr_income_config = $this->constructArrayListFromStdClass($income_config_q,'id','description');

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
			$res = $this->getPayrollSummary('PROCESSED',$sdate,$edate,$schedule,$quarter,$empid);

			if($res->num_rows() > 0){

				$regpay =  $row->regpay;
				$dependents = $row->dependents;

				$arr_info[$empid]['income'] = $arr_info[$empid]['deduction'] = $arr_info[$empid]['fixeddeduc'] = $arr_info[$empid]['loan'] = array();

				$arr_info[$empid]['fullname'] 	= $row->fullname;
				$arr_info[$empid]['salary'] 	= $regpay;


				$res 							= $res->row(0);

				$arr_info[$empid]['tardy'] 		= $res->tardy;
				$arr_info[$empid]['absents'] 	= $res->absents;
				$arr_info[$empid]['whtax'] 		= $res->withholdingtax;

				//< income
				$income_arr 				= $this->constructArrayListFromComputedTable($res->income);
				$arr_info[$empid]['income'] = $income_arr;
				foreach ($income_arr as $k => $v) {$arr_income_config[$k]['hasData'] = 1;}

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
		$data['incomeoth_config'] = $arr_incomeoth_config;
		$data['fixeddeduc_config'] = $arr_fixeddeduc_config;
		$data['deduction_config'] = $arr_deduc_config;
		$data['loan_config'] = $arr_loan_config;
		$data['sdate'] = $sdate;
		$data['edate'] = $edate;

		return $data;
	}


	function getAtmPayrolllist($emp_bank=''){
		$res = $this->db->query("SELECT a.employeeid, REPLACE(a.LName, 'Ñ', '?') AS lname,REPLACE(a.FName, 'Ñ', '?') AS fname,REPLACE(a.MName, 'Ñ', '?') AS mname ,a.`emp_bank`, b.`monthly`, a.emp_accno
							FROM employee a
							INNER JOIN payroll_employee_salary b ON b.`employeeid`=a.`employeeid`
							WHERE a.`emp_bank`='$emp_bank' ORDER BY a.LName;");
		$data = array();
		if($res->num_rows() > 0){
			foreach ($res->result() as $key => $row) {
				$fullname = $row->lname . ', ' . $row->fname . ' ' . substr($row->mname, 0,1) . '.';
				$data['list'][$row->emp_bank][$row->employeeid] = array('fullname'=>$fullname,'account_num'=>$row->emp_accno,'net_salary'=>$row->monthly);
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


} //endoffile


?>
