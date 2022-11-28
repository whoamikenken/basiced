<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

 class Batch_encode_ extends CI_Controller {
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		$this->load->model('income');
		$this->load->model('deduction');
		$this->load->model('utils');
		$this->load->model('loan');
	}

	public function getReglamentoryBatchEncodeData(){
		$response['success_count'] = $response['failed_count'] = 0;
		$data = $this->input->post();
		$data['employmentstat'] = implode(',', $data['employmentstat']);
		// $emplist = $this->utils->getEmplist($data['deptid'],'','','','',$data['employmentstat'],'NAMEONLY','','','',true);
		// $emplist = $this->utils->getEmplist($data['deptid'],'','',$data['teachingtype'],$data['employmentstat'],'NAMEONLY','','','','',true,$data['office']);
		$emplist = $this->utils->getEmplistModified($data["deptid"],'','',$data["teachingtype"],$data["employmentstat"],'NAMEONLY','','','','',$data["status"], $data["campus"],$data["office"]);
		if($data["employeeid"][0]){
			$emplist = array();
			foreach($data["employeeid"] as $employeeid){
				$emplist[$employeeid] = $employeeid;
			}
		}
		foreach($emplist as $empid => $row){
			$data['process_amount'] = ($data['process_amount'] == 0 || $data['process_amount'] == '')? NULL : $data['process_amount'];
			$data_tosave = array(
				"amount" => $data['process_amount'],
				"cutoff_period" => $data['process_schedule'],
				"code_deduction" => $data['code_categ'],
				"employeeid" => $empid,
				"schedule" => "semimonthly"
			);
			$isExisting = $this->deduction->checkIfHasExistingDeduction($empid, $data['code_categ']);
			if(!$isExisting) $res = $this->deduction->insertData($data_tosave);
			else $res = $this->deduction->updateData($data_tosave, $data['code_categ']);

			if($res) $response['success_count']++;
			else $response['failed_count']++;
		}
		echo json_encode($response);
	}

	public function getIncomeBatchEncodeData(){
		$response['success_count'] = $response['failed_count'] = 0;
		$data = $this->input->post();
		$data['employmentstat'] = implode(',', $data['employmentstat']);
		// $emplist = $this->utils->getEmplist($data['deptid'],'','',$data['teachingtype'],$data['employmentstat'],'NAMEONLY','','','','',true,$data['office']);

		$emplist = $this->utils->getEmplistModified($data["deptid"],'','',$data["teachingtype"],$data["employmentstat"],'NAMEONLY','','','','',$data["status"], $data["campus"],$data["office"]);

		if(isset($data["employeeid"][0]) && $data["employeeid"][0] != ''){
			$emplist = array();
			foreach($data["employeeid"] as $employeeid){
				$emplist[$employeeid] = $employeeid;
			}
		}
		foreach($emplist as $empid => $row){
			$data_tosave = array(
				"datefrom" => $data['dateset'],
				"amount" => $data['process_amount'],
				"nocutoff" => $data['process_nocutoff'],
				"cutoff_period" => $data['process_schedule'],
				"code_income" => $data['code_categ'],
				"employeeid" => $empid,
				"schedule" => "semimonthly"
			);
			$isExisting = $this->income->checkIfHasExistingIncome($empid, $data['code_categ']);
			if(!$isExisting) $res = $this->income->insertData($data_tosave);
			else $res = $this->income->updateData($data_tosave, $data['code_categ']);

			if($res) $response['success_count']++;
			else $response['failed_count']++;
		}

		echo json_encode($response);
	}

	public function getDeductionBatchEncodeData(){
		$response['success_count'] = $response['failed_count'] = 0;
		$data = $this->input->post();
		$data['employmentstat'] = isset($data['employmentstat']) ?  implode(',', $data['employmentstat']) : '';
		$emplist = $this->utils->getEmplistModified($data["deptid"],'','',$data["teachingtype"],$data["employmentstat"],'NAMEONLY','','','','',$data["status"], $data["campus"],$data["office"]);
		if($data["employeeid"][0]){
			$emplist = array();
			foreach($data["employeeid"] as $employeeid){
				$emplist[$employeeid] = $employeeid;
			}
		}
		foreach($emplist as $empid => $row){
			$data_tosave = array(
				"datefrom" => $data['dateset'],
				"amount" => $data['process_amount'],
				"nocutoff" => $data['process_nocutoff'],
				"cutoff_period" => $data['process_schedule'],
				"code_deduction" => $data['code_categ'],
				"employeeid" => $empid,
				"schedule" => "semimonthly"
			);
			$isExisting = $this->deduction->checkIfHasExistingDeduction($empid, $data['code_categ']);
			if(!$isExisting) $res = $this->deduction->insertData($data_tosave);
			else $res = $this->deduction->updateData($data_tosave, $data['code_categ']);

			if($res) $response['success_count']++;
			else $response['failed_count']++;
		}

		echo json_encode($response);
	}


	public function getLoanBatchEncodeData(){
		$response['success_count'] = $response['failed_count'] = 0;
		$data = $this->input->post();
		$data['employmentstat'] = implode(',', $data['employmentstat']);
		// $emplist = $this->utils->getEmplist($data['deptid'],'','','','',$data['employmentstat'],'NAMEONLY','','','',true);
		$emplist = $this->utils->getEmplistModified($data["deptid"],'','',$data["teachingtype"],$data["employmentstat"],'NAMEONLY','','','','',$data["status"], $data["campus"],$data["office"]);		
		if($data["employeeid"][0]){
			$emplist = array();
			foreach($data["employeeid"] as $employeeid){
				$emplist[$employeeid] = $employeeid;
			}
		}
		foreach($emplist as $empid => $row){
			$data_tosave = array(
				"datefrom" => $data['dateset'],
				"loan_base" => $data['process_baseon'],
				"startingamount" => $data['process_starting_balance'],
				"currentamount" => $data['process_current_balance'],
				"amount" => $data['process_amount'],
				"nocutoff" => $data['process_nocutoff'],
				"cutoff_period" => $data['process_schedule'],
				"code_loan" => $data['code_categ'],
				"employeeid" => $empid,
				"schedule" => "semimonthly"
			);
			$isExisting = $this->loan->checkIfHasExistingLoan($empid, $data['code_categ']);
			if(!$isExisting) $res = $this->loan->insertData($data_tosave);
			else $res = $this->loan->updateData($data_tosave, $data['code_categ']);

			if($res) $response['success_count']++;
			else $response['failed_count']++;
		}

		echo json_encode($response);
	}

	public function getSalaryBatchEncodeData(){
		$response['success_count'] = $response['failed_count'] = 0;
		$data = $this->input->post();
		$data['employmentstat'] = implode(',', $data['employmentstat']);
		$emplist = $this->utils->getEmplistModified($data["deptid"],'','',$data["teachingtype"],$data["employmentstat"],'NAMEONLY','','','','',$data["status"], $data["campus"],$data["office"]);
		if($data["employeeid"][0]){
			$emplist = array();
			foreach($data["employeeid"] as $employeeid){
				$emplist[$employeeid] = $employeeid;
			}
		}
		foreach($emplist as $empid => $row){
			$data_tosave = array(
				"employeeid" => $empid,
				// "type" => $data['process_type'],
				"monthly" => $data['process_monthly'],
				"semimonthly" => $data['process_semimonthly'],
				"daily" => $data['process_daily'],
				"hourly" => $data['process_hourly'],
				"minutely" => $data['process_minutely'],
				"schedule" => $data['process_salary_schedule'],
				"dependents" => $data["process_tax_status"],
				"fixedday" => 1,
				"date_effective" => date("Y-m-d")
			);
			$res = $this->income->insertBatchSalary($data_tosave);
			// echo "<pre>"; print_r($this->db->last_query());
			if($res) $response['success_count']++;
			else $response['failed_count']++;
		}

		echo json_encode($response);
	}

	public function loadDepartmentList(){
		$this->load->model('utils');
		$option = "<option value=''> - All Department -</option>";
		$dept_list = $this->utils->getDepartments();
		foreach($dept_list as $key => $description){
			if($key != "" && $description !=""){
				$option .= "<option value = '".$key."'>".$description."</option>";
			}
		}
		echo $option;
	}

	public function loadEmploymentStatusList(){
		// $option = "<option value=''> - All Employment Status -</option>";
		$option = '';
		$status_list = $this->extras->showemployeestatus("All Status");
		foreach($status_list as $key => $description){
			$option .= "<option value = '".$key."'>".$description."</option>";
		}
		echo $option;
	}

	public function loadBatchEncodeCategory(){
		$option = "";
		$categ_list = Globals::getBatchEncodeCategory();
		foreach($categ_list as $key => $description){
			if($description != "Previous Employer Data"){
				$option .= "<option value = '".$key."'>".$description."</option>";
			}
		}
		echo $option;
	}

	public function loadBatchEncodeCategory2(){
		$option = "";
		$categ_list = Globals::getBatchEncodeCategory2();
		foreach($categ_list as $key => $description){
			if($description != "Previous Employer Data"){
				$option .= "<option value = '".$key."'>".$description."</option>";
			}
		}
		echo $option;
	}

	public function loadBatchEncodeTypeSelection(){
		$config_rec = array();
		$this->load->model('payroll_reports');
		$option = "<option value=''> -- Select Type --</option>";
		$toks = $this->input->post('toks');
		$code = ($toks) ? $this->gibberish->decrypt($this->input->post('code'), $toks) : $this->input->post('code');
		if($code == "income") $config_rec = $this->payroll_reports->getPayrollIncomeConfig(); 
		else if($code == "deduction") $config_rec = $this->payroll_reports->getPayrollDeductionConfig(); 
		else if($code == "loan" || $code == "loan_adj") $config_rec = $this->payroll_reports->getPayrollLoanConfig(); 
		foreach($config_rec as $row){
			$option .= "<option value = '".$row['id']."'>".$row['description']."</option>";
		}

		if($code == "regdeduc"){
			$option .= "<option value = 'PHILHEALTH'>PHILHEALTH</option>";
			$option .= "<option value = 'PAGIBIG'>PAGIBIG</option>";
			$option .= "<option value = 'SSS'>SSS</option>";
		}

		echo $option;
	}

	public function loadPayrollBatchEncode(){
		$toks = $this->input->post("toks");
		$category = $this->gibberish->decrypt($this->input->post('category'), $toks);
		$deptid = $this->gibberish->decrypt($this->input->post('deptid'), $toks);
		$employmentstat = $this->gibberish->decrypt($this->input->post('employmentstat'), $toks);
		$code_type = $this->gibberish->decrypt($this->input->post('code_type'), $toks);
		$schedule = $this->gibberish->decrypt($this->input->post('schedule'), $toks);
		$campus = $this->gibberish->decrypt($this->input->post('campus'), $toks);
		$status = $this->gibberish->decrypt($this->input->post('status'), $toks);
		$tnt = $this->gibberish->decrypt($this->input->post('tnt'), $toks);
		$office = $this->gibberish->decrypt($this->input->post('office'), $toks);

		switch ($category) {
			case 'salary':
				$this->loadSalaryBatchEncode($deptid, $employmentstat, $campus, $status, $office, $tnt);
				break;
			
			case 'income':
				$this->loadIncomeBatchEncode($deptid, $employmentstat, $code_type, $schedule, $campus, $status, $office, $tnt);
				break;
			
			case 'deduction':
				$this->loadDeductionBatchEncode($deptid, $employmentstat, $code_type, $schedule, $campus, $status, $office, $tnt);
				break;

			case 'loan':
				$loan  =    $this->gibberish->decrypt($this->input->post('loan'), $toks) ? $this->gibberish->decrypt($this->input->post('loan'), $toks): "";
				$schedule  = $this->gibberish->decrypt($this->input->post('schedule'), $toks) ? $this->gibberish->decrypt($this->input->post('schedule'), $toks) : "";
				$this->loadLoanBatchEncode($deptid, $employmentstat,$code_type,$schedule, $campus, $status, $office, $tnt);
				break;

			case 'loan_adj':
				$loan  =    $this->gibberish->decrypt($this->input->post('loan'), $toks) ? $this->gibberish->decrypt($this->input->post('loan'), $toks): "";
				$schedule  = $this->gibberish->decrypt($this->input->post('schedule'), $toks) ? $this->gibberish->decrypt($this->input->post('schedule'), $toks) : "";
				$this->loadLoanAdjBatchEncode($deptid, $employmentstat,$code_type,$schedule, $campus, $status, $office, $tnt);
				break;
			
			case 'regdeduc':
				$this->loadReglamentoryBatchEncode($deptid, $employmentstat, $campus, $status, $office, $tnt);
				break;

			case 'regpayment':
				$cutoff = $this->gibberish->decrypt($this->input->post('cutoff'), $toks);
				$reglamentory = $this->gibberish->decrypt($this->input->post('reglamentory'), $toks);
				$this->loadPaymentReglamentoryBatchEncode($deptid, $employmentstat,$cutoff,$reglamentory);
				break;

			case 'prevdata':
				$this->PreviousEmployerDataBatchEncode();
				break;

			default:
				# code...
				break;
		}

	}

	public function loadIncomeBatchEncode($deptid='', $employmentstat='', $code_income = 0, $searchShed, $campus='', $status='', $office='', $tnt=''){
		$data = array();
		$this->load->model('utils');
		$this->load->model('payrolloptions');
		
		$emplist = $this->utils->getEmplistModified($deptid,'','',$tnt,$employmentstat,'NAMEONLY',true,'','','',$status, $campus,$office);
		$data["emplist"] = array();
		
		foreach ($emplist as $empid => $fullname) {
			$q_income = $this->payroll->getEmployeeIncome($empid, $code_income,"", $searchShed);
			$datefrom = $amount = $nocutoff = $cutoff_period = '';
			$schedule = '';

			foreach ($q_income as $res) {
				$datefrom = $res->datefrom;
				$amount = $res->amount;
				$nocutoff = $res->nocutoff;
				$cutoff_period = $res->cutoff_period;
				$schedule = $res->schedule;
			}

			$isIncludeToList = true;
			if($searchShed && $searchShed != $cutoff_period) $isIncludeToList = false;
			if($isIncludeToList){	
				$data["emplist"][$empid]["fullname"] 		= $fullname;
				$data["emplist"][$empid]["datefrom"] 		= $datefrom;
				$data["emplist"][$empid]["amount"] 			= $amount;
				$data["emplist"][$empid]["nocutoff"] 		= $nocutoff;
				$data["emplist"][$empid]["schedule"] 		= $schedule;
				$data["emplist"][$empid]["cutoff_period"] 	= $cutoff_period;

			}
		}
			
		$data["codeincome"] = $code_income;
		$this->load->view('payroll/batch_encode/be_income',$data);

	}

	public function loadSalaryBatchEncode($deptid='',$employmentstat='', $campus='', $status='', $office='', $tnt=''){
		$data = array();

		$this->load->model('utils');
		$emplist = $this->utils->getEmplistModified($deptid,'','',$tnt,$employmentstat,'NAMEONLY','','','','',$status, $campus,$office);
		$data['type_config'] = $this->setup->getPayrollTypeArray();
		foreach ($emplist as $employeeid => $fullname) {
			$rank = $this->extensions->getEmployeeRank($employeeid);
			$rank_desc = $this->extensions->getRankTypeDescription($rank);
			$data['list'][$employeeid]['fullname'] = $fullname;
			$data['list'][$employeeid]['teachingtype'] = $this->employee->getempdatacol('teachingtype',$employeeid);
			$data['list'][$employeeid]['iscollege'] = $this->extensions->checkIfCollegeTeaching($employeeid);
			$data['list'][$employeeid]['rank'] = "";
			$data['list'][$employeeid]['type'] = $rank;
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
				$data['list'][$employeeid]['rank'] = $row->rank;
				$data['list'][$employeeid]['type'] = $rank;
 			}
			
			$salary_perdept = $this->payroll->getPerDeptLecLabPay($employeeid);

			$data['list'][$employeeid]['perdept_arr'] =  isset($salary_perdept[$employeeid]) ? $salary_perdept[$employeeid] : array();

		}

		$aimsdept_arr = $this->utils->getAIMSDepartment();
		unset($aimsdept_arr['']);
		$data['aimsdept_arr'] = $aimsdept_arr;

		$this->load->view('payroll/batch_encode/be_salary_list',$data);
	}

	public function loadDeductionBatchEncode($deptid='', $employmentstat='', $code_deduc = 0, $searchShed, $campus='', $status='', $office='', $tnt=''){
		$data = array();
		$this->load->model('utils');
		$this->load->model('deduction');
		
		$emplist = $this->utils->getEmplistModified($deptid,'','',$tnt,$employmentstat,'NAMEONLY',true,'','','',$status, $campus,$office);
		
		$data["emplist"] = array();
		$data["code_deduc"]	= $code_deduc;
		foreach ($emplist as $empid => $fullname) {
			$q_deduc = $this->deduction->getEmployeeDeduction($empid, $code_deduc,"", $searchShed);
			
			$datefrom = $amount = $nocutoff = $cutoff_period = '';
			$schedule = '';
			foreach ($q_deduc as $res) {
				$datefrom = $res->datefrom;
				$amount = $res->amount;
				$nocutoff = $res->nocutoff;
				$cutoff_period = $res->cutoff_period;
				$schedule = $res->schedule;
			}

			$isIncludeToList = true;
			if($searchShed && $searchShed != $cutoff_period) $isIncludeToList = false;
			if($isIncludeToList){	
				$data["emplist"][$empid]["fullname"] 		= $fullname;
				$data["emplist"][$empid]["datefrom"] 		= $datefrom;
				$data["emplist"][$empid]["amount"] 			= $amount;
				$data["emplist"][$empid]["nocutoff"] 		= $nocutoff;
				$data["emplist"][$empid]["schedule"] 		= $schedule;
				$data["emplist"][$empid]["cutoff_period"] 	= $cutoff_period;
				
			}
		}
		// echo "<pre>"; print_r($data); die;
			
		$this->load->view('payroll/batch_encode/be_deduction',$data);
	}

	public function loadLoanBatchEncode($deptid='',$employmentstat='',$loan='',$schedule='', $campus='', $status='', $office='', $tnt=''){
		$data = array();
		$this->load->model('utils');
		$this->load->model('payroll');
		$this->load->model('loan');
		$emplist = $this->utils->getEmplistModified($deptid,'','',$tnt,$employmentstat,'NAMEONLY',true,'','','',$status, $campus,$office);
		$loantype = $this->payroll->displayLoan()->result();
		
		$data['loan'] = $loan;
		$data['loantype'] = $loantype;
		foreach ($emplist as $employeeid => $fullname) {
			if(!$schedule) $data['list'][$employeeid]['fullname'] = $fullname;
			$loanbase = $this->payroll->getEmployeeLoan($employeeid,$loan,"",$schedule);
			foreach($loanbase->result() as $key => $row)
			{
				$iscontinue = false;
				if(!$schedule || $schedule == $row->cutoff_period) $iscontinue = true;
				if($iscontinue){
					$data['list'][$employeeid]['fullname'] = $fullname;
					$data['list'][$employeeid]['loanbase'] = $row->loan_base;
					$data['list'][$employeeid]['deductiondate'] = $row->datefrom;
					$data['list'][$employeeid]['startingbalance'] = ($row->startingamount) ? $row->startingamount : "";
					$data['list'][$employeeid]['currentbalance'] = ($row->currentamount) ? $row->currentamount : "";
					$data['list'][$employeeid]['nocutoff'] = $row->nocutoff;
					$data['list'][$employeeid]['amount'] = ($row->amount) ? $row->amount : "";
					$data['list'][$employeeid]['schedule'] = $row->schedule;
					$data['list'][$employeeid]['period'] = $row->cutoff_period;
					$data['list'][$employeeid]['can_edit'] = $this->loan->isLoanAbleToEdit($employeeid, $loan);
					$data['list'][$employeeid]['id'] = $row->id;
					$data['list'][$employeeid]['nocutoff'] = $row->nocutoff - $this->loan->getLastLoanPaymentEditHistoryCutoff($row->id);
					$data['list'][$employeeid]['cutoff_period'] = $row->cutoff_period;
				}
			}
		}
		$this->load->view('payroll/batch_encode/be_loan_listdata',$data);	

	}

	public function loadLoanAdjBatchEncode($deptid='',$employmentstat='',$loan='',$schedule='', $campus='', $status='', $office='', $tnt=''){
		$data = array();
		$this->load->model('utils');
		$this->load->model('payroll');
		$this->load->model('loan');
		$emplist = $this->utils->getEmplistModified($deptid,'','',$tnt,$employmentstat,'NAMEONLY','','','','',$status, $campus,$office);
		$loantype = $this->payroll->displayLoan()->result();
		
		$data['loan'] = $loan;
		$data['loantype'] = $loantype;
		foreach ($emplist as $employeeid => $fullname) {
			$loanbase = $this->payroll->getEmployeeLoan($employeeid,$loan,$schedule);
			if($loanbase->num_rows() > 0){
				if(!$schedule) $data['list'][$employeeid]['fullname'] = $fullname;
				foreach($loanbase->result() as $key => $row){
					$adjustment = $this->getLoanAdjustment($row->id, $row->employeeid, $row->datefrom, $row->code_loan, $row->nocutoff, $row->cutoff_period);


					$data['list'][$employeeid]['fullname'] = $fullname;
					$data['list'][$employeeid]['loanbase'] = $row->loan_base;
					$data['list'][$employeeid]['deductiondate'] = $row->datefrom;
					$data['list'][$employeeid]['startingbalance'] = $row->startingamount;
					$data['list'][$employeeid]['currentbalance'] = $row->currentamount;
					$data['list'][$employeeid]['nocutoff'] = $row->nocutoff;
					$data['list'][$employeeid]['amount'] = $row->amount;
					$data['list'][$employeeid]['schedule'] = $row->schedule;
					$data['list'][$employeeid]['period'] = $row->cutoff_period;
					$data['list'][$employeeid]['can_edit'] = $this->loan->isLoanAbleToEdit($employeeid, $loan);
					$data['list'][$employeeid]['id'] = $row->id;
					$data['list'][$employeeid]['nocutoff'] = $row->nocutoff - $this->loan->getLastLoanPaymentEditHistoryCutoff($row->id);
					$data['list'][$employeeid]['cutoff_period'] = $row->cutoff_period;
				}
			}
		}
		$this->load->view('payroll/batch_encode/be_loan_listdata',$data);	

	}

	public function loadReglamentoryBatchEncode($deptid='', $employmentstat='', $campus='', $status='', $office='', $tnt=''){
		$data = array();
		$this->load->model('utils');
		$this->load->model('payrolloptions');

		$emplist = $this->utils->getEmplistModified($deptid,'','',$tnt,$employmentstat,'NAMEONLY','','','','',$status, $campus,$office);
		$data["emplist"] = array();
		$arr_code_deduction = array();
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

	public function loadPaymentReglamentoryBatchEncode($deptid='',$employmentstat='',$cutoff='',$reglamentory=''){
		$data['list'] = array();
		$datenow = date("Y-m-d", strtotime($this->extensions->getServerTime()));
		$dates = explode(' ',$cutoff);
		if(isset($dates[0]) && isset($dates[1])){

			$sdate = $dates[0];
			$edate = $dates[1];

			$this->load->model('payrollprocess');
			$this->load->model('utils');

			$cutoff_details = $this->payrolloptions->getPayrollCutoffDetails('','',$sdate,$edate,true);
			$schedule = isset($cutoff_details[$sdate.'|'.$edate]['schedule']) ? $cutoff_details[$sdate.'|'.$edate]['schedule'] : '';
			$quarter = isset($cutoff_details[$sdate.'|'.$edate]['quarter']) ? $cutoff_details[$sdate.'|'.$edate]['quarter'] : '';


			$payroll_q = $this->payrollprocess->getPayrollSummary('PROCESSED',$sdate,$edate,$schedule,$quarter,'',false,'',false);
			foreach ($payroll_q->result() as $key => $row) {
				$employeeid = $row->employeeid;
				
				$ee_er_q = $this->payrollprocess->getReglamentoryPaymentComputed('',$row->id);

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
		$data['reglamentory'] = array("pagibig"=>"PAGIBIG", "sss"=>"SSS", "philhealth"=>"PHILHEALTH");
		if($reglamentory && $reglamentory != "undefined") $data["reglamentory"] = array($data["reglamentory"][$reglamentory]); 
		asort($data['list']);
		$data["showlist"] = ($cutoff == "undefined") ? false : true;
		$data["cutoff"] = $this->payrolloptions->payrollProcessedCutoff($cutoff);
		$this->load->view('payroll/batch_encode/be_payment_reglamentory_list',$data);
	}

	public function loadReglamentoryBatchEncodeFilter($deptid='', $employmentstat='',$reglamentory='',$quarter='',$status='',$office='',$teachingtype='',$campus=''){
		$data = array();
		$this->load->model('utils');
		$this->load->model('payrolloptions');

		$emplist = $this->utils->getEmplistModified($deptid,'','',$teachingtype,$employmentstat,'NAMEONLY','','','','',$status, $campus,$office);
		$data["emplist"] = array();
		// echo "<pre>"; print_r($emplist); die;
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
					if($quarter){
						if($res->cutoff_period == $quarter){
							$data["emplist"][$empid][$fields_amount] = !empty($res->amount) || $res->amount == 0 ? $res->amount : "";
							$data["emplist"][$empid][$fields_quarter] = !empty($res->cutoff_period) ? $res->cutoff_period : "";
						}
					}else{
						$data["emplist"][$empid][$fields_amount] = !empty($res->amount) || $res->amount == 0 ? $res->amount : "";
						$data["emplist"][$empid][$fields_quarter] = !empty($res->cutoff_period) ? $res->cutoff_period : "";
					}
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


	public function loadPayrollBatchEncodeFilter()
	{
		$deptid = $this->input->post('deptid');
		$office = $this->input->post('office');
		$campus = $this->input->post('campus');
		$status = $this->input->post('status');
		$teachingtype = $this->input->post('teachingtype');
		$quarter = $this->input->post('quarter');
		$employmentstat = $this->input->post('employmentstat');
		$reglamentory = $this->input->post('reglamentory');
		$this->loadReglamentoryBatchEncodeFilter($deptid, $employmentstat,$reglamentory,$quarter,$status,$office,$teachingtype,$campus);
	}

	public function PreviousEmployerDataBatchEncode(){
		$this->load->view('payroll/batch_encode/Previous_Employer_Data');
	}

	public function PreviousEmployerDataBatchEncodeMinimumWage(){
		$data = array();
		$data = $this->input->post(); 
		$employeeid = $data['employeeid'];
		$employee_previous_records = array();
		$data['gross_income_config'] = $this->payroll->getGrossIncomeList();
	
		$employee_previous_records = $this->payroll->getPreviousEmployerDataOfMinimumWage($employeeid);
		foreach($employee_previous_records as $key => $value){
			if($value['fixedday']=='0'){
				unset($employee_previous_records[$key]);		
			}
		} 

		foreach($employee_previous_records as $key => $value){
			$data['employee_records'][$value['employeeid']][$value['fullname']][$value['type']] = $value['amount'];
		} 
		
		unset($data['gross_income_config']['allconfig'][12]);
		$this->load->view('payroll/batch_encode/previous_employer_data_minimum_wage', $data);
	}

	public function PreviousEmployerDataBatchEncodeNonMinimumWage(){
		$data = array();
		$data = $this->input->post(); 
		$employeeid = $data['employeeid']; 
		$employee_previous_records = array();
		$data['gross_income_config'] = $this->payroll->getGrossIncomeList();
	
		$employee_previous_records = $this->payroll->getPreviousEmployerDataOfMinimumWage($employeeid);

		foreach($employee_previous_records as $key => $value){
			if($value['fixedday']=='1'){
				unset($employee_previous_records[$key]);		
			}
		}

		foreach($employee_previous_records as $key => $value){
			$data['employee_records'][$value['employeeid']][$value['fullname']][$value['type']] = $value['amount'];
		} 
		
		$this->load->view('payroll/batch_encode/previous_employer_data_non_minimum_wage', $data);
	}

	public function encodeOverloadRate(){
		$this->load->model("income");
		$this->load->model("payrollcomputation");
		$where_clause = "";
		$success = $failed = $total = 0;
		$hourly_rate = 0;
		$inc_data = array();
		$emplist = $this->input->post("employeeid");
		$hours = $this->input->post("hours");
		$tnt = $this->input->post("tnt");
		$deptid = $this->input->post("deptid");
		$employmentstat = $this->input->post("employmentstat");
		if(!$emplist){
			if($tnt) $where_clause .= " AND teachingtype = '$tnt' ";
			if($deptid) $where_clause .= " AND deptid = '$deptid' ";
			if($employmentstat) $where_clause .= " AND employmentstat = '$employmentstat' ";
			$emp_arr = $this->setup->getActiveEmployees($where_clause);
			foreach($emp_arr as $emp_arrs){
				$emplist[] = $emp_arrs["employeeid"];
			}
		}
		foreach($emplist as $employeeid){
			if($employeeid){
				$daily = $this->payrollcomputation->getEmployeeDailySalary($employeeid);
				$hourly_rate = $daily / 8;
				$inc_data["hourly"] = $hourly_rate * $hours;
				$inc_data["daily"] = ($hourly_rate * $hours) * 8;
				$inc_data["dateEffective"] = date("Y-m-d");
				$inc_data["dateEnd"] = date("Y-m-d");
				$inc_data["other_income"] = 12;
				$inc_data["employeeid"] = $employeeid;
				$inc_data["overload_hours"] = $hours;
				$isExisting = $this->income->checkIfHasOverload($employeeid);
				$res = $this->income->saveOverloadRate($inc_data, $isExisting);
				if($res) $success++;
				else $failed++;

				$total++;
				$inc_data = array();
			}
		}

		$data = array("success"=>$success,"failed"=>$failed,"total"=>$total);
		echo json_encode($data);
	}

	public function getLoanAdjustment($base_id, $employeeid, $datefrom, $code_loan, $nocutoff, $cutoff_period){
		$payroll_list = $this->loan->getLoanPayment($base_id);
		if($payroll_list){
			foreach($payroll_list as $row){
				// echo "<pre>"; print_r($row); die;
			}
		}
	}

}