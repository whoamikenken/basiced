<?php 
/**
 * @author Justin
 * @copyright 2015
 */
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Payroll_ extends CI_Controller {

	function __construct(){
        parent::__construct();
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session

        $this->load->model("attendance");
        $this->load->model("attcompute");
    }

	public function index()
    {
    	echo "Oooppss.! This page found some problem..";
    }

    function convertFormDataToArray($formdata){
		$data_arr = array();
		$formdata = explode("&", $formdata);
		foreach($formdata as $row){
			list($key, $value) = explode("=", $row);
			$data_arr[$key] = $value;
		}

		return $data_arr;
	}
    
	/*
	* Basic Salary Deduction 
	*/
	function loaddeduction(){
	  $salary = $this->input->post('deduc');
	  echo $this->payroll->sssdeduc($salary);
	}
	/*
	* Load Views Payroll Folder 
	*/
	function payrollconfig(){
		$data = $this->input->post();
		$toks = $this->input->post("toks");
		if($toks){
			foreach($data as $key => $val){
				$data[$key] = $this->gibberish->decrypt($val, $toks);
			}
		}  
		$view = $toks ? $data['view'] : $this->input->post("view");  
		$this->load->view("payroll/$view",$data);  
		#$this->extras->aasda();
	}
	//Payroll View
	function payrollview(){
	$view = $this->input->post("view");  
	$this->load->view("employee/$view");  
	}
	/*
	*  Model Function Config
	*/
	// this will load payroll model.
	function ranksaving(){
	  $eid = $this->input->post("eid");
	  $type = $this->input->post("type");
	  echo $this->payroll->ranksaving($eid, $type);
	}
	function loadmodelfunc(){
	  $data   = $this->input->post();
	  $toks = $this->input->post("toks");
	  if($toks){
	  	foreach($data as $key => $val){
			if($key != "finalpay_arr") $data[$key] = $this->gibberish->decrypt($val, $toks);
		}
	  }
	  $model  = $this->input->post("model");
	  if($toks) $model = $this->gibberish->decrypt($model, $toks);
	  echo $this->payroll->$model($data);
	  // echo "pre"; print_r($this->db->last_query()); die;
	}
	function deleteSalaryHistory(){
		$data = $this->input->post();
		echo $this->payroll->deleteSalaryHistory($data);
	}
	/*
	*  Load All Options..
	*/
	// this will load quarter of sched.
	function loadquarterforsched(){
	  $data   = $this->input->post();
	  $toks = $this->input->post("toks");
	  if($toks){
	  	foreach($data as $key => $val){
			$data[$key] = $this->gibberish->decrypt($val, $toks);
		}
	  }
	  $model  = $this->input->post("model");
	  if($toks) $model = $this->gibberish->decrypt($model, $toks);
	  echo $this->payrolloptions->$model($data,FALSE,$data['schedule']);
	}
	function onloadquarterforsched(){
	  $data   = $this->input->post();
	  $model  = $this->input->post("model");
	  echo $this->payrolloptions->$model($data,FALSE,$data['schedule']);
	}
	// this will load payroll cut-off
	function loadpayrollcutoff(){
	  $data   = $this->input->post();
	  $toks = $this->input->post("toks");
	  if($toks){
	  	foreach($data as $key => $val){
			$data[$key] = $this->gibberish->decrypt($val, $toks);
		}
	  }
	  $model  = $this->input->post("model");
	  $model = $this->gibberish->decrypt($model, $toks);
	  echo $this->payrolloptions->$model($data['schedule'],$data);
	}
	//OTHER Function
	function showOverloadEmpList()
	{
		$this->load->model("income");
		$data["records"] = $this->income->getOverloadList();
		$this->load->view("payroll/overloadEmpList", $data);

	}
	///<  @Angelica added functions
	function getReportConfig(){
		$data = array();
		$data['reportname'] 	= $this->input->post('reportname');
		$data['deptid'] 		= $this->input->post('deptid');
		$data['employeeid'] 	= $this->input->post('employeeid');
		$data['payrollcutoff'] 	= $this->input->post('payrollcutoff');
		$data['schedule'] 		= $this->input->post('schedule');
		$data['quarter'] 		= $this->input->post('quarter');
		$view = $this->input->post("view");  
		$this->load->model('payrollconfig');

		if($data['reportname'] == 'payrollsummary'){
			$data['deminimiss'] = $this->payrollconfig->getIncomeConfig('deminimiss','',array('description'));
			$data['others'] = $this->payrollconfig->getIncomeConfig('other','',array('description'));
		}

		$this->load->view("payroll/$view",$data); 
	}

	function employeeDropdown(){
		$return = "<option value=''>All employee</option>";
		$data = $this->input->post();
		$toks = $data["toks"];
		$office = ($toks) ? $this->gibberish->decrypt($data["office"], $toks) : $data["office"];
		$deptid = ($toks) ? $this->gibberish->decrypt($data["deptid"], $toks) : $data["deptid"];
		$emplist = $this->payroll->employeeDropdown($office, $deptid);
		if($emplist->num_rows() > 0){
			foreach($emplist->result() as $val){
	        	$return .= "<option value='".$val->employeeid."'>".$val->employeeid." - ".$val->lname.", ".$val->fname." ".$val->mname."</option>";
	        }
	    }

        echo $return;
	}

	function loadPayrollReport(){
		$data = array();
		$deptid     = ($this->input->get('deptid')) ? $this->input->get('deptid') : $this->input->post('deptid');
		$employeeid     = ($this->input->get('employeeid')) ? $this->input->get('employeeid') : $this->input->post('employeeid');
		$schedule     = ($this->input->get('schedule')) ? $this->input->get('schedule') : $this->input->post('schedule');
		$cutoff     = ($this->input->get('payrollcutoff')) ? $this->input->get('payrollcutoff') : $this->input->post('payrollcutoff');
		$quarter     = ($this->input->get('quarter')) ? $this->input->get('quarter') : $this->input->post('quarter');
		$sort     = ($this->input->get('sort')) ? $this->input->get('sort') : $this->input->post('sort');
		// echo "<pre>"; print_r($sort); die;

		$reportname     = ($this->input->get('reportname')) ? $this->input->get('reportname') : $this->input->post('reportname');
		$reportformat     = ($this->input->get('reportformat')) ? $this->input->get('reportformat') : $this->input->post('reportformat');
		
		$dates = explode(' ',$cutoff);
		if(isset($dates[0]) && isset($dates[1])){
			$sdate = $dates[0];
			$edate = $dates[1];
		}else{
			echo 'Invalid Cutoff';
			return;
		}
		$this->load->model('payrollprocess');
		$this->load->model('payrollconfig');


		if($reportname == 'payrollsummary'){
			$deminimiss = $this->input->get('deminimiss');
			$other = $this->input->get('other');

			$emplist = $this->payroll->loadAllEmpbyDept($deptid,$employeeid,$schedule);
			if(sizeof($emplist) > 0){
				$data = $this->payrollprocess->getProcessedPayrollSummary($emplist,$sdate,$edate,$schedule,$quarter);
				$data['deminimiss_config'] 	= $this->payrollconfig->getIncomeConfig('deminimiss','',array('description'));
				$data['others_config'] 		= $this->payrollconfig->getIncomeConfig('other','',array('description'));
				$data['deminimiss'] 		= $deminimiss;
				$data['other'] 				= $other;

				if($reportformat == 'xls'){

				}else{
					$this->load->view('payroll/reports_pdf/processed_payroll_summary',$data);
				}

			}else{
				echo 'No employees to display.';
				return;
			}

		}elseif($reportname=='atmpayrolllist'){
			$emp_bank     = ($this->input->get('emp_bank')) ? $this->input->get('emp_bank') : $this->input->post('emp_bank');
			$status     = ($this->input->get('emp_status')) ? $this->input->get('emp_status') : $this->input->post('emp_status');

			if(!$status) $status = $this->input->get('payroll_status');

			$data = $this->payrollprocess->getAtmPayrolllist($emp_bank, $sdate, $status, $sort );
			$data['sdate'] = $sdate;
			$data['edate'] = $edate;
			if($reportformat == 'XLS'){
				$this->load->view('payroll/reports_excel/atm_payroll_list',$data);
			}else{
				$this->load->view('payroll/reports_pdf/atm_payroll_list',$data);
			}
		}

	}

	function loadPayrollSummary(){

		$data = array();
		$toks = $this->input->post("toks");
		$formdata = $this->gibberish->decrypt($this->input->post("formdata"), $toks);
		$formdata = Globals::convertFormDataToArray($formdata);
		$deptid     = $formdata['deptid'];
		$office     = $formdata['office'];
		$employeeid = $formdata['employeeid'];
		$schedule   = $formdata['schedule'];
		$cutoff     = $formdata['payrollcutoff'];
		$quarter    = $formdata['quarter'];
		$campus 	= $formdata['campusid'];

		$success_count = 0;
		$arr_data_failed = array();
		
		$this->session->set_userdata('emplist_total', 0);
        $this->session->set_userdata('recomputed_emp', 0);
        
		$dates = explode('+',$cutoff);
		if(isset($dates[0]) && isset($dates[1])){
			$sdate = $dates[0];
			$edate = $dates[1];
			$payroll_cutoff_id = $this->payrolloptions->getPayrollCutoffId($sdate,$edate);
		}else{
			echo 'Failed. Invalid cutoff.';
			return;
		}
		$this->load->model('payrollprocess');

		$emplist = $this->payroll->loadAllEmpbyDept($deptid,$office,$employeeid,$schedule,$campus, $sdate, $edate);
		// echo "<pre>"; print_r($this->db->last_query()); die;
		$emplist2 = $this->payroll->loadAllEmpbyDeptSample($deptid,$office,$employeeid,$schedule, "", $sdate, $edate);

		if(sizeof($emplist) > 0){

			$data = $this->payrollprocess->processPayrollSummary($emplist,$emplist2,$sdate,$edate,$schedule,$quarter,false,$payroll_cutoff_id);
			$departments = $this->extras->showdepartment();
			$data['dept'] 	= $departments[$deptid];
			$data['deptid'] = $deptid;
			$data['employeeid'] = $employeeid;
			$data['schedule'] = $schedule;
			$data['cutoff'] = $cutoff;
			$data['campus'] = $campus;
			$data['quarter'] = $quarter;
			$data['status'] = 'PENDING';
			$data['issaved'] = '';

		}else{
			echo 'No employees to display.';
			return;
		}

		$this->load->view('payroll/payrolllist',$data);
	}

	function loadProcessedPayrollSummary(){
		$data = array();
		$toks = $this->input->post("toks");
		$formdata = $this->gibberish->decrypt($this->input->post("formdata"), $toks);
		$formdata = Globals::convertFormDataToArray($formdata);
		$deptid     = $formdata['deptid'];
		$office     = $formdata['office'];
		$employeeid = $formdata['employeeid'];
		$schedule   = $formdata['schedule'];
		$payrollcutoff     = $formdata['payrollcutoff'];
		$quarter    = $formdata['quarter'];
		$campus 	= $formdata['campusid'];
		$bank 	= $formdata['bank'];

		$success_count = 0;
		$arr_data_failed = array();

		$this->session->set_userdata('emplist_total', 0);
        $this->session->set_userdata('recomputed_emp', 0);

		$dates = explode('+',$payrollcutoff);
		if(isset($dates[0]) && isset($dates[1])){
			$sdate = $dates[0];
			$edate = $dates[1];
			$payroll_cutoff_id = $this->payrolloptions->getPayrollCutoffId($sdate,$edate);
		}else{
			echo 'Failed. Invalid cutoff.';
			return;
		}
		$this->load->model('payrollprocess');

		$emplist = $this->payroll->loadAllEmpbyDeptForProcessed($deptid,$office,$employeeid,$schedule,$campus,$edate,$sdate);

		if(sizeof($emplist) > 0){

			$data = $this->payrollprocess->getProcessedPayrollSummary($emplist,$sdate,$edate,$schedule,$quarter,'PROCESSED',$bank);
			$departments = $this->extras->showdepartment();
			$data['dept'] 	= $departments[$deptid];
			$data['deptid'] = $deptid;
			$data['office'] = $office;
			$data['employeeid'] = $employeeid;
			$data['schedule'] = $schedule;
			$data['payrollcutoff'] = $payrollcutoff;
			$data['quarter'] = $quarter;
			$data['campusid'] = $campus;
			$data['status'] = 'PROCESSED';

		}else{
			echo 'No employees to display.';
			return;
		}

		$this->load->model('utils');
		$data['hasEditPayrollComputedEditAccess'] = $this->utils->hasEditPayrollComputedEditAccess();

		$this->load->view('payroll/payrolllistview',$data);
	}

	function loadSavedPayrollSummary(){
		$data = array();
		$toks = $this->input->post("toks");
		$formdata = $this->gibberish->decrypt($this->input->post("formdata"), $toks);
		$formdata = Globals::convertFormDataToArray($formdata);
		$deptid     = $formdata['deptid'];
		$office     = $formdata['office'];
		$employeeid = $formdata['employeeid'];
		$schedule   = $formdata['schedule'];
		$cutoff     = $formdata['payrollcutoff'];
		$quarter    = $formdata['quarter'];
		$campus 	= $formdata['campusid'];
		$bank 	= $formdata['bank'];

		$success_count = 0;
		$arr_data_failed = array();

		$this->session->set_userdata('emplist_total', 0);
        $this->session->set_userdata('recomputed_emp', 0);

		$dates = explode('+',$cutoff);
		if(isset($dates[0]) && isset($dates[1])){
			$sdate = $dates[0];
			$edate = $dates[1];
			$payroll_cutoff_id = $this->payrolloptions->getPayrollCutoffId($sdate,$edate);
		}else{
			echo 'Failed. Invalid cutoff.';
			return;
		}
		$this->load->model('payrollprocess');

		$emplist = $this->payroll->loadAllEmpbyDept($deptid,$office,$employeeid,$schedule,$campus);
		

		if(sizeof($emplist) > 0){

			$data = $this->payrollprocess->getProcessedPayrollSummary($emplist,$sdate,$edate,$schedule,$quarter,'SAVED',$bank);
			$departments = $this->extras->showdepartment();
			$data['dept'] 	= $departments[$deptid];
			$data['deptid'] = $deptid;
			$data['office'] = $office;
			$data['employeeid'] = $employeeid;
			$data['schedule'] = $schedule;
			$data['cutoff'] = $cutoff;
			$data['quarter'] = $quarter;
			$data['campus'] = $campus;
			$data['status'] = 'SAVED';
			$data['issaved'] = true;

		}else{
			echo 'No employees to display.';
			return;
		}

		$this->load->view('payroll/payrolllist',$data);
	}

	function recomputePayrollSummary(){
		$data = array();
		$toks = $this->input->post("toks");
		$formdata = $this->gibberish->decrypt($this->input->post("formdata"), $toks);
		$formdata = Globals::convertFormDataToArray($formdata);
		$deptid     = $formdata['deptid'];
		$office     = $formdata['office'];
		$employeeid = $formdata['employeeid'];
		$schedule   = $formdata['schedule'];
		$cutoff     = $formdata['payrollcutoff'];
		$quarter    = $formdata['quarter'];
		$campus 	= $formdata['campusid'];

		$success_count = 0;
		$arr_data_failed = array();

		$this->session->set_userdata('emplist_total', 0);
        $this->session->set_userdata('recomputed_emp', 0);

		$dates = explode('+',$cutoff);
		if(isset($dates[0]) && isset($dates[1])){
			$sdate = $dates[0];
			$edate = $dates[1];
			$payroll_cutoff_id = $this->payrolloptions->getPayrollCutoffId($sdate,$edate);
		}else{
			echo 'Failed. Invalid cutoff.';
			return;
		}
		$this->load->model('payrollprocess');

		$emplist = $this->payroll->loadAllEmpbyDept($deptid,$office,$employeeid,$schedule, $campus, $sdate, $edate, true);
		$emplist2 = $this->payroll->loadAllEmpbyDeptSample($deptid,$office,$employeeid,$schedule, $campus,  $sdate, $edate);

		if(sizeof($emplist) > 0){

			$data = $this->payrollprocess->processPayrollSummary($emplist,$emplist2,$sdate,$edate,$schedule,$quarter,true,$payroll_cutoff_id);
			$departments = $this->extras->showdepartment();
			$data['dept'] 	= $departments[$deptid];
			$data['deptid'] = $deptid;
			$data['employeeid'] = $employeeid;
			$data['schedule'] = $schedule;
			$data['cutoff'] = $cutoff;
			$data['campus'] = $campus;
			$data['quarter'] = $quarter;
			$data['status'] = 'PENDING';
			$data['issaved'] = '';

		}else{
			echo 'No employees to recompute.';
			return;
		}
		$data['recompute_msg'] = 'Recompute Successful.';
		$this->load->view('payroll/payrolllist',$data);
	}

	function savePayrollCutoffSummary(){
		$deptid     = $this->input->post('deptid');
		$employeeid = $this->input->post('employeeid');
		$schedule   = $this->input->post('schedule');
		$cutoff     = $this->input->post('cutoff');
		$quarter    = $this->input->post('quarter');
		$emplist    = $this->input->post('emplist');
		$status    = $this->input->post('status');
		$bank    = $this->input->post('bank');

		$success_count = 0;
		$arr_data_failed = array();

		$dates = explode('+',$cutoff);
		if(isset($dates[0]) && isset($dates[1])){

			$sdate = $dates[0];
			$edate = $dates[1];

			///< PREVIOUS CUTOFF MUST BE FINALIZED FIRST BEFORE SAVING CURRENT PAYROLL CUTOFF
			$prevcount = 0;
			if($status == 'SAVED'){

				foreach ($emplist as $empid) {
					$prev_q = $this->db->query("SELECT COUNT(id) AS prevcount FROM payroll_computed_table WHERE cutoffend < '$sdate' AND `status`='SAVED' AND employeeid='$empid'");
					$prevcount += $prev_q->row(0)->prevcount;
				}
			}

			///< save status change
			if(date('Y-m-d',strtotime($sdate)) > date('Y-m-d')/* && $status == 'PENDING'*/){
				$return = array('err_code'=>2,'msg'=>'Failed. Please unfinalize before the end of cutoff.','success_count'=>$success_count,'data_failed'=>array()); 

			}
			/*comment for now */
			/*elseif($status == 'SAVED' && $prevcount > 0){
				$return = array('err_code'=>2,'msg'=>'Failed. Please finalize previous SAVED cutoff first.','success_count'=>$success_count,'data_failed'=>array()); 

			}*/
			else{

				$this->load->model('payrollprocess');
				
				if(sizeof($emplist) > 0 && $emplist){

					foreach ($emplist as $empid) {
						$res = $this->payrollprocess->savePayrollCutoffSummary($empid,$sdate,$edate,$schedule,$quarter,$status,$bank);
						if($res) 	$success_count++;
						else 		array_push($arr_data_failed, $empid);
					}

					if($success_count) 	$return = array('err_code'=>0,'msg'=>'Successfully saved.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
					else 				$return = array('err_code'=>2,'msg'=>'Failed to save.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 

				}else{
					$return = array('err_code'=>2,'msg'=>'Failed. No employees to save.','success_count'=>$success_count,'data_failed'=>array()); 
				}
			}

		}else{
			$return = array('err_code'=>2,'msg'=>'Failed. Invalid cutoff.','success_count'=>$success_count,'data_failed'=>array()); 
		}

		echo json_encode($return);

	}


	function finalizePayrollCutoffSummary(){
		$deptid     = $this->input->post('deptid');
		$employeeid = $this->input->post('employeeid');
		$schedule   = $this->input->post('schedule');
		$cutoff     = $this->input->post('cutoff');
		$quarter    = $this->input->post('quarter');
		$emplist    = $this->input->post('emplist');
		$status    = $this->input->post('status');

		$success_count = 0;
		$arr_data_failed = array();

		$dates = explode('+',$cutoff);
		if(isset($dates[0]) && isset($dates[1])){

			$sdate = $dates[0];
			$edate = $dates[1];

			$this->load->model('payrollprocess');
			
			if(sizeof($emplist) > 0){

				foreach ($emplist as $empid) {
					$res = $this->payrollprocess->finalizePayrollCutoffSummary($empid,$sdate,$edate,$schedule,$quarter);
					if($res) 	$success_count++;
					else 		array_push($arr_data_failed, $empid);
				}

				if($success_count) 	$return = array('err_code'=>0,'msg'=>'Successfully saved.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
				else 				$return = array('err_code'=>2,'msg'=>'Failed to save.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 

			}else{
				$return = array('err_code'=>2,'msg'=>'Failed. No employees to save.','success_count'=>$success_count,'data_failed'=>array()); 
			}

		}else{
			$return = array('err_code'=>2,'msg'=>'Failed. Invalid cutoff.','success_count'=>$success_count,'data_failed'=>array()); 
		}

		echo json_encode($return);

	}

	function setPayrollHoldStatus(){
		$base_id = $this->input->post('base_id');
		$isHold = $this->input->post('isHold');

		$this->load->model('utils');
		$return = array('err_code'=>0,'msg'=>'Successfully saved.'); 
		$res = $this->utils->updateSingleTblData('payroll_computed_table',array('isHold'=>$isHold,'editedby'=>$this->session->userdata('username')),array('id'=>$base_id));
		if(!$res){
			$return['err_code'] = 2;
			$return['msg'] = 'Failed to update hold status.';
		}
		
		echo json_encode($return);
	}


	function loadLongevityEmpIncluded(){
		$this->load->model('income');
		$data['emplist'] = '';
		$res = $this->income->getLongevityEmpIncluded();
		if($res->num_rows() > 0) $data['emplist'] = $res->result();

		$this->load->view('process/longevity_emp_included',$data);

	}

	function saveLongevityEmpIncluded(){
		$return = array();
		$emplist    = $this->input->post('emplist');

		$remove_q = $this->db->query("UPDATE longevity_income_included SET isIncluded='0'");

		$success_count = 0;
		$arr_data_failed = array();

		if(sizeof($emplist) > 0){
			$this->load->model('income');

			foreach ($emplist as $empid) {
				$res = $this->income->saveLongevityEmpIncluded($empid);
				if($res) 	$success_count++;
				else 		array_push($arr_data_failed, $empid);
			}
			if($success_count) 	$return = array('err_code'=>0,'msg'=>'Successfully saved.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
			else 				$return = array('err_code'=>2,'msg'=>'Failed to save.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 

		}else{
			if($remove_q) $return = array('err_code'=>0,'msg'=>'All employees successfully removed.','success_count'=>$success_count,'data_failed'=>array()); 
			else 		  $return = array('err_code'=>2,'msg'=>'Faile to removed.','success_count'=>$success_count,'data_failed'=>array()); 
		}

		echo json_encode($return);
	}

	function loadEmployeeLongevity(){
		$cutoff    = $this->input->post('cutoff');
		$campus    = $this->input->post('campus');

		$dates = explode(',',$cutoff);
		if(isset($dates[0]) && isset($dates[1])){

			$sdate = $dates[0];
			$edate = $dates[1];

			$dtr_cutoff_id = $this->payrolloptions->getDtrPayrollCutoffID($sdate,$edate,'','');

			$this->load->model('income');
			$res = $this->income->getLongevityEmpComputed('',$dtr_cutoff_id,$campus);
			$data['emplist'] = array();
			if($res->num_rows() > 0){
				foreach ($res->result() as $key => $row) {

					$regdate = $this->employee->EmpregularDate($row->employeeid);

					$data['emplist'][$row->deptid][$row->employeeid] = array('fullname'			=>$row->fullname,
																			  'dateemployed'	=>$row->dateemployed,
																			  'regdate'			=>$regdate,
																			  'credited_years'	=>$row->credited_years,
																			  'prev_basicpay'	=>$row->prev_basicpay,
																			  'present_basicpay'=>$row->present_basicpay,
																			  'amount'			=>$row->amount,
																			  'isIncluded'		=>$row->isIncluded
																			);
				}
			}

			$this->load->model('utils');
			$data['officelist'] = $this->utils->getOffice();
			$data['campus'] = $campus;
			$data['cutoff'] = $cutoff;
			$data['cutoff_year'] = date('Y',strtotime($sdate));


			$this->load->view('process/longevity_emp_computed',$data);

		}else{
			echo '<h4>Invalid Cutoff.</h4>';
		}
	}

	function loadPayrollBatchEncodeFilter()
	{
		$deptid = $this->input->post('deptid');
		$employmentstat = $this->input->post('employmentstat');
		$reglamentory = $this->input->post('reglamentory');
		$this->loadReglamentoryBatchEncodeFilter($deptid, $employmentstat,$reglamentory);
	}
	///< @Angelica -- BATCH ENCODE FUNCTIONS

	function loadPayrollBatchEncode(){
		$category = $this->input->post('category') ? $this->input->post('category'):"";
		$deptid =   $this->input->post('deptid')? $this->input->post('deptid'):"";
		$loan  =    $this->input->post('loan') ? $this->input->post('loan'): "";
		
		$schedule  = $this->input->post('schedule') ? $this->input->post('schedule') : "";
		$employmentstat = $this->input->post('employmentstat');

		switch ($category) {
			case '1':
				$this->loadSalaryBatchEncode($deptid, $employmentstat);
				break;
			case '2':
				$this->loadDeductionBatchEncode($deptid, $employmentstat, TRUE);
				break;
			
			case '3':
				$this->loadIncomeBatchEncode($deptid, $employmentstat, TRUE);
				break;

			case '4':
				$this->loadLoanBatchEncode($deptid, $employmentstat,$loan,$schedule);
				break;
			
			case '6':
				$this->loadReglamentoryBatchEncode($deptid, $employmentstat);
				break;

			case '7':
				$cutoff = $this->input->post('cutoff');
				$reglamentory = $this->input->post('reglamentory');
				$this->loadPaymentReglamentoryBatchEncode($deptid, $employmentstat,$cutoff,$reglamentory);
				break;

			default:
				# code...
				break;
		}

	}

	///< ************************************************** 2. DEDUCTION *************************************************************

	# for ica-hyperion 21503
	# by justin (with e)
	///<@Angelica - copied for ICA-Hyperion21533
	function loadDeductionBatchEncode($deptid='', $employmentstat='', $isDisplayed = false, $code_deduc = 0, $isSearchEmp = false, $searchShed = 0){
		$data = array();
		$this->load->model('utils');
		$this->load->model('deduction');
		
		$emplist = $this->utils->getEmplist($deptid,'','','','',$employmentstat,'NAMEONLY');

		$data["deduc_option"] 	= $this->payrolloptions->deduction($code_deduc);
		$data["deptid"] 		= $deptid;
		$data["employmentstat"] = $employmentstat;
		$data["emplist"] = array();
		if($isSearchEmp){
			foreach ($emplist as $empid => $fullname) {
				$q_deduc = $this->deduction->getEmployeeDeduction($empid, $code_deduc);
				
				$datefrom = $amount = $nocutoff = $cutoff_period = '';
				$schedule = 'semimonthly';
				foreach ($q_deduc as $res) {
					$datefrom = $res->datefrom;
					$amount = $res->amount;
					$nocutoff = $res->nocutoff;
					$cutoff_period = $res->cutoff_period;
					$schedule = $res->schedule;
				}

				$status = "";
				$q_deduc_history = $this->deduction->findEmpDeductionHistory($empid, $code_deduc);
				if($q_deduc_history->num_rows()) $status = $q_deduc_history->row()->status;

				$isIncludeToList = true;

				if($searchShed && $searchShed != $cutoff_period) $isIncludeToList = false;
				if($isIncludeToList){
					$data["emplist"][$empid]["fullname"] 		= $fullname;
					$data["emplist"][$empid]["datefrom"] 		= $datefrom;
					$data["emplist"][$empid]["amount"] 			= $amount;
					$data["emplist"][$empid]["nocutoff"] 		= $nocutoff;
					$data["emplist"][$empid]["schedule"] 		= $schedule;
					$data["emplist"][$empid]["cutoff_period"] 	= $cutoff_period;
					$data["emplist"][$empid]["status"] 			= $status;
				}
			}
			
		}
		
		if($isDisplayed) $this->load->view('payroll/batch_encode/be_deduction',$data);
		else 			 return $data;
	}

	function findEmpListForBEDeduction(){
		$post = $this->input->post();
		$this->load->model('payrolloptions');
		extract($post);
		$data = array();
		$data = $this->loadDeductionBatchEncode($deptid, $employmentstat, FALSE, $code_deduc, TRUE, $schedule);
		$this->load->view('payroll/batch_encode/be_deduction_list',$data);
	}

	function deleteRowPayroll(){
		  $this->load->model('payroll');
		  $return = array("err_code"=>0, 'msg'=>"Success.");
	      $data = $this->input->post();
	      $res = $this->payroll->deleteDataDeduction($data["tr_id"], $data["code_deduc"]);
	      if($res) $return = array("err_code"=>0, 'msg'=>"Success.");
	      else $return = array("err_code"=>2, 'msg'=>"Failed to delete.");

	      echo json_encode($return);
	}

	function saveBEDeduction(){
		$this->load->model('deduction');
		$post = $this->input->post();
		extract($post);
		$data = array();
		$data["success"] = array();
		$schedule = $this->input->post('sched');
		$code_deduc = $this->gibberish->decrypt($code_deduc, $toks);
		$schedule = $this->gibberish->decrypt($schedule, $toks);
		if($emp_list){
			foreach ($emp_list as $empId => $info) {
				$isUpdate = false;
				$q_checkExistEmpDeduc = $this->deduction->getEmployeeDeduction($empId, $code_deduc,$schedule);

				if(count($q_checkExistEmpDeduc) > 0) $isUpdate = true;

				# constract query..
				$updateClause = $tbl_fields = $valueClauses = "";
				foreach ($info as $fields_name => $value) {
					$value = $this->gibberish->decrypt($value, $toks);
					if($isUpdate) $updateClause .= (($updateClause) ? ", " : " ") . "$fields_name = '$value'"; 
					
					$tbl_fields   .= (($tbl_fields) ? ", " : " ") . "$fields_name";
					$valueClauses .= (($valueClauses) ? ", " : " ") . ((is_numeric($value)) ? $value : "'$value'");

					
				}

				$respond = $this->deduction->saveBEDeduction($isUpdate, $updateClause, $tbl_fields, $valueClauses, $empId, $code_deduc, $schedule);	
				
				if($respond){
					$respond = $this->deduction->saveBEDeductionHistory($tbl_fields, $valueClauses, $empId, $code_deduc);
					$data["success"][$empId] = "Successfully saved";
				}else{
					$error_emp[$empId] = "* ". $empId ." - Saving error..";
				}
			}
		}

		if(isset($cleared_emp_list)){
			foreach ($cleared_emp_list as $empId => $info) {
					$respond = $this->payroll->deleteBEDeduction($empId,$code_deduc);
	     			$data["success"][$empId] = "Successfully saved";

			}

		}

		$data["error"] = ($error_emp) ? $error_emp : array();
		$data["code_deduc"] = $code_deduc;
			
		// $this->load->view('payroll/batch_encode/be_income_result',$data);
	# end for ica-hyperion 21503
	}



	///< ************************************************** 4. LOANS  ***************************************************************

	function loadLoanBatchEncode($deptid='',$employmentstat='',$loan='',$schedule='')
	{
		$data = array();
		$this->load->model('utils');
		$this->load->model('loan');
		$this->load->model('payroll');
		$emplist = $this->utils->getEmplist($deptid,'','','','',$employmentstat,'NAMEONLY');
		#echo '<pre>';print_r($emplist);
		$loantype = $this->payroll->displayLoan()->result();
		
		$data['loantype'] = $loantype;

		if (!$loan) {
			$this->load->view('payroll/batch_encode/be_loan_list',$data);
		}
		else
		{
			$totalstartingbal = $totalcurrentbal = $totalamount = 0;
			foreach ($emplist as $employeeid => $fullname) {
				$data['list'][$employeeid]['fullname'] = $fullname;
				
				$loanbase = $this->payroll->getEmployeeLoan($employeeid,$loan,$schedule);

				foreach($loanbase->result() as $key => $row)
				{
					$data['list'][$employeeid]['id'] = $row->id;
					$data['list'][$employeeid]['loanbase'] = $row->loan_base;
					$data['list'][$employeeid]['deductiondate'] = $row->datefrom;
					$data['list'][$employeeid]['startingbalance'] = $row->startingamount;
					$data['list'][$employeeid]['currentbalance'] = $row->currentamount;
					$cutoff = $this->loan->getLastLoanPaymentEditHistoryCutoff($row->id);
					$data['list'][$employeeid]['nocutoff'] = $row->nocutoff - $cutoff;
					$data['list'][$employeeid]['amount'] = $row->amount;
					$data['list'][$employeeid]['schedule'] = $row->schedule;
					$data['list'][$employeeid]['cutoff_period'] = $row->cutoff_period;
					$totalstartingbal += $row->startingamount;
					$totalcurrentbal += $row->currentamount;
					$totalamount += $row->amount;

				}


			}
			    $data['list'][$employeeid]['totalstartingbal'] = $totalstartingbal;
				$data['list'][$employeeid]['totalcurrentbal'] = $totalcurrentbal;
				$data['list'][$employeeid]['totalamount'] = $totalamount;
			$this->load->view('payroll/batch_encode/be_loan_listdata',$data);	
			
			
		}

	}
	
	function saveLoanBatch()
	{
		$form_data = $this->input->post('form_data');
		$this->load->model('loan');
		$period = $this->input->post("period");
		$toks = $this->input->post("toks");
		#var_dump($form_data);
		$success_count = 0;
		$arr_data_failed = array();
		#echo "<pre>"; print_r($form_data); echo "</pre>"; die;
		foreach ($form_data as $employeeid => $fieldata) {
			foreach($fieldata as $key => $val){
				$fieldata[$key] = $this->gibberish->decrypt($val, $toks);
			}
			$is_continue = $this->loan->isLoanAbleToEdit($employeeid, $fieldata['loan']);
			$status = ($fieldata["skip_loan"] == 1) ? "YES" : "NO";
			$is_exisiting = $this->loan->checkIfLoanIsExisting($employeeid, $fieldata['loan']);
			$this->loan->skipEmployeeLoanPayment($employeeid, $fieldata['loan'], $status, $is_exisiting);
			if($is_continue){
				$res = $this->loan->saveEmployeeLoan($employeeid,$fieldata);

				if($res) 	$success_count++;
				else 		array_push($arr_data_failed, $employeeid);
			}else{
				$res = $this->loan->updateEmpLoanForLoanHasPayment($fieldata, $employeeid);

				if($res) 	$success_count++;
				else 		array_push($arr_data_failed, $employeeid);
			}

		}
		if($success_count) 	$return = array('err_code'=>0,'msg'=>'Successfully saved.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
		else 				$return = array('err_code'=>2,'msg'=>'Failed to save.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 

		#echo "<pre>"; print_r($form_data); die;
		echo json_encode($return);
	}


	///< ************************************************** 1. SALARY ***************************************************************
	function loadSalaryBatchEncode($deptid='',$employmentstat=''){
		$data = array();
		$this->load->model('utils');
		$emplist = $this->utils->getEmplist($deptid,'','','','',$employmentstat,'NAMEONLY');

		foreach ($emplist as $employeeid => $fullname) {
			$data['list'][$employeeid]['fullname'] = $fullname;
			$data['list'][$employeeid]['teachingtype'] = $this->employee->getempdatacol('teachingtype',$employeeid);

			$salary_base = $this->payroll->getEmployeeSalary($employeeid);

			foreach ($salary_base->result() as $key => $row) {
				$data['list'][$employeeid]['schedule'] = $row->schedule;
				$data['list'][$employeeid]['tax_status'] = $row->dependents;
				$data['list'][$employeeid]['fixedday'] = $row->fixedday;
				$data['list'][$employeeid]['monthly'] = $row->monthly;
				$data['list'][$employeeid]['semimonthly'] = $row->semimonthly;
				$data['list'][$employeeid]['daily'] = $row->daily;
				$data['list'][$employeeid]['hourly'] = $row->hourly;
				$data['list'][$employeeid]['minutely'] = $row->minutely;
 			}
		}

		$this->load->view('payroll/batch_encode/be_salary_list',$data);
	}


	function saveSalaryBatch(){
		$form_data = $this->input->post('form_data');
		$toks = $this->input->post('toks');
		$form_data_delete = $this->input->post('form_data_delete');
		$success_count = 0;
		$arr_data_failed = array();
		if($form_data){
			foreach ($form_data as $employeeid => $sal) {
				foreach($sal as $key => $val){
					$sal[$key] = $this->gibberish->decrypt($val, $toks);
				}
				$sal["date_effective"] = date("Y-m-d");
				$res = $this->payroll->saveEmployeeSalary($sal);

				if($res) 	$success_count++;
				else 		array_push($arr_data_failed, $empid);

			}
			if($success_count) 	$return = array('err_code'=>0,'msg'=>'Successfully saved.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
			else 				$return = array('err_code'=>2,'msg'=>'Failed to save.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
	   }
		
		if($form_data_delete){
			foreach ($form_data_delete as $info => $employeeid) {
				$res = $this->payroll->deleteEmployeeSalary($employeeid);

				if($res) 	$success_count++;
				else 		array_push($arr_data_failed, $empid);

		}

		if($success_count) 	$return = array('err_code'=>0,'msg'=>'Successfully saved.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
		else 				$return = array('err_code'=>2,'msg'=>'Failed to save.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
		}

		echo json_encode($return);
	}


	///< ************************************************** 3. INCOME ***************************************************************

	# for ica-hyperion 21503
	# by justin (with e)
	function loadIncomeBatchEncode($deptid='', $employmentstat='', $isDisplayed = false, $code_income = 0, $isSearchEmp = false, $searchShed = 0){
		$data = array();
		$this->load->model('utils');
		$this->load->model('payrolloptions');
		
		$emplist = $this->utils->getEmplist($deptid,'','','','',$employmentstat,'NAMEONLY');

		$data["income_option"] 	= $this->payrolloptions->income($code_income);
		$data["deptid"] 		= $deptid;
		$data["employmentstat"] = $employmentstat;
		$data["emplist"] = array();
		if($isSearchEmp){
			foreach ($emplist as $empid => $fullname) {
				$q_income = $this->payroll->getEmployeeIncome($empid, $code_income);
				
				$datefrom = $amount = $nocutoff = $cutoff_period = '';
				$schedule = 'weekly';
				foreach ($q_income as $res) {
					$datefrom = $res->datefrom;
					$amount = $res->amount;
					$nocutoff = $res->nocutoff;
					$cutoff_period = $res->cutoff_period;
					$schedule = $res->schedule;
				}

				if($code_income == '56'){
					$amount = 0; //<compute
				}

				$status = "";
				$q_income_history = $this->payroll->findEmpIncomeHistory($empid, $code_income);
				if($q_income_history->num_rows()) $status = $q_income_history->row()->status;

				$isIncludeToList = true;

				if($searchShed && $searchShed != $cutoff_period) $isIncludeToList = false;
				if($isIncludeToList){
					$data["emplist"][$empid]["fullname"] 		= $fullname;
					$data["emplist"][$empid]["datefrom"] 		= $datefrom;
					$data["emplist"][$empid]["amount"] 			= $amount;
					$data["emplist"][$empid]["nocutoff"] 		= $nocutoff;
					$data["emplist"][$empid]["schedule"] 		= $schedule;
					$data["emplist"][$empid]["cutoff_period"] 	= $cutoff_period;
					$data["emplist"][$empid]["status"] 			= $status;
				}
			}
			
		}
		
		if($isDisplayed) $this->load->view('payroll/batch_encode/be_income',$data);
		else 			 return $data;
	}

	function findEmpListForBEIncome(){
		$post = $this->input->post();
		$this->load->model('payrolloptions');
		extract($post);
		$data = array();
		$data = $this->loadIncomeBatchEncode($deptid, $employmentstat, FALSE, $code_income, TRUE, $schedule);
		$this->load->view('payroll/batch_encode/be_income_list',$data);
	}

	function saveBEIncome(){
		$post = $this->input->post();
		extract($post);
		$data = array();
		$data["success"] = array();

		$schedule = $this->input->post('sched');
		$code_income = $this->gibberish->decrypt($code_income, $toks);
		$schedule = $this->gibberish->decrypt($schedule, $toks);
		// if($schedule == 1) $schedule = "weekly";
		// elseif ($schedule == 2) $schedule = "semimonthly";
		// else $schedule = "monthly"; 
		if($emp_list){
			foreach ($emp_list as $empId => $info) {
				$isUpdate = false;
				$q_checkExistEmpIncome = $this->payroll->getEmployeeIncome($empId, $code_income,$schedule);

				if(count($q_checkExistEmpIncome) > 0) $isUpdate = true;

				# constract query..
				$updateClause = $tbl_fields = $valueClauses = "";
				foreach ($info as $fields_name => $value) {
					$value = $this->gibberish->decrypt($value, $toks);
					if($isUpdate) $updateClause .= (($updateClause) ? ", " : " ") . "$fields_name = '$value'"; 
					
					$tbl_fields   .= (($tbl_fields) ? ", " : " ") . "$fields_name";
					$valueClauses .= (($valueClauses) ? ", " : " ") . ((is_numeric($value)) ? $value : "'$value'");

					
				}

				$respond = $this->payroll->saveBEIncome($isUpdate, $updateClause, $tbl_fields, $valueClauses, $empId, $code_income, $schedule);	
				
				if($respond){
					$respond = $this->payroll->saveBEIncomeHistory($tbl_fields, $valueClauses, $empId, $code_income);
					$data["success"][$empId] = "Successfully saved";
				}else{
					$error_emp[$empId] = "* ". $empId ." - Saving error..";
				}
			}
		}

			if(isset($cleared_emp_list)){
				foreach ($cleared_emp_list as $empId => $info) {
						$respond = $this->payroll->deleteBEIncome($empId,$code_income2);	
						$data["success"][$empId] = "Successfully saved";
				}
			}


		$data["error"] = ($error_emp) ? $error_emp : array();
		$data["code_income"] = $code_income;
		
		// $this->load->view('payroll/batch_encode/be_income_result',$data);
	}

	function BEdeleteIncome(){
		$toks = $this->input->post("toks");
		$empid = $this->gibberish->decrypt($this->input->post("empid"), $toks);
		$code = $this->gibberish->decrypt($this->input->post("code"), $toks);
		echo $this->payroll->deleteBEIncome($empid,$code);
	}

	function BEdeleteDeduction(){
		$toks = $this->input->post("toks");
		$empid = $this->gibberish->decrypt($this->input->post("empid"), $toks);
		$code = $this->gibberish->decrypt($this->input->post("code"), $toks);
		echo $this->payroll->deleteBEDeduction($empid,$code);
	}

	function clearZeros(){
		$toks = $this->input->post("toks");
		$tbl = $this->gibberish->decrypt($this->input->post("tblname"), $toks);
		echo $this->payroll->clearZeros($tbl);
	}
	# end for ica-hyperion 21503

	// --------ica-hyperion-21693--------


	///< ************************************************** 6. REGLAMENTORY DEDUCTION ***********************************************

	///< @Angelica - copy for ICA-Hyperion21501
	# for mcu-hyperion 21478
	# by justin (with e)
	function loadReglamentoryBatchEncode($deptid='', $employmentstat=''){
		$data = array();
		$this->load->model('utils');
		$this->load->model('payrolloptions');

		$emplist = $this->utils->getEmplist($deptid,'','','','',$employmentstat,'NAMEONLY');
		$data["emplist"] = array();

		foreach ($emplist as $empid => $fullname) {
			
			$data["emplist"][$empid]["fullname"] = $fullname;
			$arr_code_deduction = array("SSS", "PHILHEALTH", "PAGIBIG");

			$data["emplist"][$empid]["schedule"] = "semimonthly";
			foreach ($arr_code_deduction as $code_deduction) {
				$code_deduction = strtolower($code_deduction);
				$data["emplist"][$empid][$code_deduction ."amount"] = "";
				$data["emplist"][$empid][$code_deduction ."quarter"] = "";
			}
			$data["emplist"][$empid]["schedule"] = !empty($res->schedule) ? $res->schedule : "semimonthly";

			foreach ($arr_code_deduction as $code_deduction) {
				$q_reglamentory = $this->payroll->findEmpReglamentory($empid, $code_deduction);

				foreach ($q_reglamentory as $res) {
					$fields_amount = strtolower($code_deduction) ."amount";
					$fields_quarter = strtolower($code_deduction) ."quarter";

					$data["emplist"][$empid][$fields_amount] = !empty($res->amount) || $res->amount == 0 ? $res->amount : "";
					$data["emplist"][$empid][$fields_quarter] = !empty($res->cutoff_period) ? $res->cutoff_period : "";
				}	
			}

			foreach ($arr_code_deduction as $code_deduction) {
				$status = "";
				$q_status = $this->payroll->getEmpReglamentoryStatusHistory($empid, $code_deduction);
				foreach ($q_status as $res)	$status = $res->status;

				$data["emplist"][$empid][strtolower($code_deduction)."status"] = $status;
			}
			
		}
		$data["reglamentory"] = $arr_code_deduction;
		$this->load->view('payroll/batch_encode/be_reglamentory',$data);
	}

	function loadReglamentoryBatchEncodeFilter($deptid='', $employmentstat='',$reglamentory=''){
		$data = array();
		$this->load->model('utils');
		$this->load->model('payrolloptions');

		$emplist = $this->utils->getEmplist($deptid,'','','','',$employmentstat,'NAMEONLY');
		$data["emplist"] = array();

		foreach ($emplist as $empid => $fullname) {
			
			$data["emplist"][$empid]["fullname"] = $fullname;
			$arr_code_deduction = array("SSS", "PHILHEALTH", "PAGIBIG");

			$data["emplist"][$empid]["schedule"] = "semimonthly";
			foreach ($arr_code_deduction as $code_deduction) {
				$code_deduction = strtolower($code_deduction);
				$data["emplist"][$empid][$code_deduction ."amount"] = "";
				$data["emplist"][$empid][$code_deduction ."quarter"] = "";
			}
			$data["emplist"][$empid]["schedule"] = !empty($res->schedule) ? $res->schedule : "semimonthly";

			foreach ($arr_code_deduction as $code_deduction) {
				$q_reglamentory = $this->payroll->findEmpReglamentory($empid, $code_deduction);

				foreach ($q_reglamentory as $res) {
					$fields_amount = strtolower($code_deduction) ."amount";
					$fields_quarter = strtolower($code_deduction) ."quarter";

					$data["emplist"][$empid][$fields_amount] = !empty($res->amount) || $res->amount == 0 ? $res->amount : "";
					$data["emplist"][$empid][$fields_quarter] = !empty($res->cutoff_period) ? $res->cutoff_period : "";
				}	
			}

			foreach ($arr_code_deduction as $code_deduction) {
				$status = "";
				$q_status = $this->payroll->getEmpReglamentoryStatusHistory($empid, $code_deduction);
				foreach ($q_status as $res)	$status = $res->status;

				$data["emplist"][$empid][strtolower($code_deduction)."status"] = $status;
			}
			
		}
		$data["reglamentory"] = $arr_code_deduction;
		$data["reglamentoryfilter"] = $reglamentory;
		$this->load->view('payroll/batch_encode/be_reglamentoryfiltering',$data);
	}


	function saveBEReglamentory(){
		$data = array();
		$emplist = $this->input->post('emplist');
		$emplistcleared = $this->input->post('emplistcleared');

		$error_emp = array();
		$success_emp = array();
		if($emplist){
			foreach ($emplist as $empid => $code_info) {
				foreach ($code_info as $code => $info) {
					$fieldsClauses = "";
					$valueClauses = "";
					$updateClause = "";

					$isExist = false;
					$q_isExistReglamentory = $this->payroll->findEmpReglamentory($empid, $code);
					if(count($q_isExistReglamentory) > 0) $isExist = true;

					foreach ($info as $fields => $value) {
						if($fields != "code_deduction"){
							$fields = str_replace($code, "", $fields);
							$fields = str_replace("quarter", "cutoff_period", $fields);

							$value = (is_numeric($value)) ? $value : "'$value'";					
							$fieldsClauses .= (($fieldsClauses) ? ", " : "") ."". $fields;
							$valueClauses .= (($valueClauses) ? ", " : "") ."". $value;
							$updateClause .= (($updateClause) ? ", " : "") ."". $fields ."=". $value;
						}
					} # > end of foreach in info

					$q_saveReglamentory = $this->payroll->saveBEReglamentory($empid, strtoupper($code), $fieldsClauses, $valueClauses, $updateClause, $isExist);

					if($q_saveReglamentory){
						$q_saveReglamentoryHistory = $this->payroll->saveBEReglamentoryHistory($empid, strtoupper($code), $fieldsClauses, $valueClauses);

						$success_emp[] = "success $empid - $code";
					}else $error_emp[] = "* Failed to save this ". strtoupper($code). " for employeeid : ". $empid;
					
				} # > end of foreach in code_info
			} # > end of foreach in emplist
		}
		if($emplistcleared){
				foreach ($emplistcleared as $info => $empId) {
				$respond = $this->payroll->clearBEReglamentory($empId);	
				$success_emp[] = "success $empId";
			}
		}

		$data["success_emp"] = $success_emp;
		$data["error_emp"]   = $error_emp;

		// $this->load->view('payroll/batch_encode/be_reglamentory_result',$data);
	}
	# end for mcu-hyperion 21478

	///<  @Angelica added functions copy by justin (with e) for ica-hyperion 21622
	///< Income Setup 
	function getIncomeSetupMainAccountList(){
		$this->load->model('income');
		$income_list = array();
		$res = $this->income->getIncomeSetupList(array('ismainaccount'=>'1'));

		foreach ($res->result() as $key => $row) {
			$income_list[$row->id] = array('description'=>$row->description);
		}

		$data['income_list'] = $income_list;
		$this->load->view('payroll/incomeconfig_main_acct',$data);
	}

	function checkTaggedToMainAcct(){
		$this->load->model('income');
		$id = $this->input->post('id');
		$tagged_list = $this->income->getIncomeSetupList(array('mainaccount'=>$id));
		echo $tagged_list->num_rows();
	}

	function deleteIncomeMainAcctSetup(){
		$ret = array('err_code'=>0,'msg'=>'Successfully saved.','failed_list_str'=>'');
		$success_count = 0;
		$this->load->model('income');
		$income_list_to_delete = $this->input->post('income_list_to_delete');

		if($income_list_to_delete){

			foreach ($income_list_to_delete as $id => $desc) {
				$tagged_list = $this->income->getIncomeSetupList(array('mainaccount'=>$id));
				if($tagged_list->num_rows() == 0){

					$res = $this->income->deleteIncome(array('id'=>$id));
					if($res){
						$success_count++;
					}else{
						if($ret['failed_list_str']) $ret['failed_list_str'] .= ', ';
						$ret['failed_list_str'] .= $desc;
					}

				}else{
					if($ret['failed_list_str']) $ret['failed_list_str'] .= ', ';
					$ret['failed_list_str'] .= $desc;
				}
			}

			if($success_count == 0){
				$ret['err_code'] = 2;
				$ret['msg'] = 'Failed to save.';
			}

		}else{
			$ret['err_code'] = 3;
			$ret['msg'] = 'No account to delete';
		}

		echo json_encode($ret);
	}

	function showPayrollRegistrarModal(){
		$data = array();

		$this->load->view("payroll/payroll_registrar_modal", $data);
	}

	function loadPaymentReglamentoryOptions(){
		$data = array();
		$data['cutoff_list'] = $this->payrolloptions->displaypayrollcutoffdata();

		$this->load->view('payroll/batch_encode/be_payment_reglamentory_options',$data);
	}
	
	function loadPaymentReglamentoryBatchEncode($deptid='',$employmentstat='',$cutoff='',$reglamentory=''){
		$data['list'] = array();

		$dates = explode(' ',$cutoff);
		if(isset($dates[0]) && isset($dates[1])){

			$sdate = $dates[0];
			$edate = $dates[1];

			$this->load->model('payrollprocess');
			$this->load->model('utils');

			$cutoff_details = $this->payrolloptions->getPayrollCutoffDetails('','',$sdate,$edate,true);
			$schedule = isset($cutoff_details[$sdate.'|'.$edate]['schedule']) ? $cutoff_details[$sdate.'|'.$edate]['schedule'] : '';
			$quarter = isset($cutoff_details[$sdate.'|'.$edate]['quarter']) ? $cutoff_details[$sdate.'|'.$edate]['quarter'] : '';


			$payroll_q = $this->payrollprocess->getPayrollSummary('PROCESSED',$sdate,$edate,$schedule,$quarter,'',false,'',true);
			foreach ($payroll_q->result() as $key => $row) {
				$employeeid = $row->employeeid;
				
				$ee_er_q = $this->payrollprocess->getReglamentoryPaymentComputed('',$row->id,$reglamentory);

				foreach ($ee_er_q->result() as $ee_key => $ee_row) {
					$amount = $ee_row->EE;

					if($amount > 0){
						$data['list'][$employeeid]['fullname'] = $this->utils->getFullName($employeeid,'LFMI');
						$data['list'][$employeeid]['base_id'] = $row->id;
						$data['list'][$employeeid]['amount'] = $amount;
						$data['list'][$employeeid]['or_number'] = $ee_row->or_number;
						$data['list'][$employeeid]['datepaid'] = $ee_row->datepaid;
					}

				}
			}
		}

		$data['code_deduction'] = $reglamentory;
		asort($data['list']);

		$this->load->view('payroll/batch_encode/be_payment_reglamentory_list',$data);
	}

	function getRateBased(){
		$empid = $this->input->post('empid');
		$getRateBased = $this->payroll->getEmployeeRateBased($empid);
		echo $getRateBased;
	}

	function recomputePercentage(){
    	$this->load->view('recomputePercentagePayroll');
  	}

	function getEmployeeSalaryHistory(){
		$eid = $this->input->post('eid');
		$this->load->model('utils');
		$data['aimsdept_arr'] = $this->utils->getAIMSDepartment();
		$data['salary_list'] = $this->payroll->getEmployeeSalaryHistory($eid);
		$data['employeeid'] = $eid;
		$this->load->view('employee/payroll_info_salary_history',$data);
	}

	function savePhilhealthShare(){
		$data = $this->input->post();
		if($data["id"]){
			$id = $data["id"];
			unset($data["id"]);
			echo $this->payroll->updatePhilhealthShare($data, $id);
		}else{
			echo $this->payroll->insertPhilhealthShare($data);
		}
	}

	function deletePhilhealthShare(){
		$id = $this->input->post("code");
		echo $this->payroll->deletePhilhealthShare($id);
	}

	function savePaymentReglamentoryBatch(){
		$form_data = $this->input->post();
		$success_count = 0;
		$arr_data_failed = array();

		$this->load->model('payrollprocess');
		if( isset($form_data['list']) ){
			foreach ($form_data['list'] as $employeeid => $info) {
				$res = $this->payrollprocess->updateComputedEE_ORNum('',$info['base_id'],$form_data['code_deduction'],$form_data['or_number'],$form_data['datepaid']);
				if($res) 	$success_count++;
				else 		array_push($arr_data_failed, $employeeid);
			}
		}
		
		if($success_count) 	$return = array('err_code'=>0,'msg'=>'Successfully saved.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
		else 				$return = array('err_code'=>2,'msg'=>'Failed to save.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 

		echo json_encode($return);
	}

} //end of file