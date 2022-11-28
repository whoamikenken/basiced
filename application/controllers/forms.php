<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forms extends CI_Controller{
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		$this->load->library('PdfCreator_tcpdf');
		$this->load->library('PdfCreator_mpdf');
	}

	function index(){
		// do nothing
	}

	function loadForm(){
		$data = array();
		$formdata = $this->input->get("formdata") ? $this->input->get("formdata") : $this->input->post("formdata");
		if($formdata){
			$this->load->model("webcheckin");
			// $formdata = $this->input->post("formdata");

			
			$formdata = base64_decode(urldecode($formdata));
			$data = Globals::convertFormDataToArray($formdata, $toks );
			$toks = $data['toks'];

			if($toks){
				foreach ($data as $key => $value) {
					if($key != "toks") $data[$key] = $this->gibberish->decrypt( $value, $toks );
				}
			}
			$form = $toks ? $data['form']  : $data['form'];
			// $data = $this->input->get();
			// echo "<pre>";print_r($form);die;
			$data['payroll_config'] = $this->extensions->getAllIncomeKeysAndDescription();
			$this->load->view('forms_pdf/' . $form, $data);
		}else{
			$data = $this->input->post();

			$form = $data["form"];
			$data['payroll_config'] = $this->extensions->getAllIncomeKeysAndDescription();
			$this->load->view('forms_pdf/' . $form, $data);
		}
		
	}

	function loadExcelReportPost(){ // I added this function, just in case some report still using the original one ( loadExcelReport() ) - Riel
		$formdata =$this->input->post("formdata");
		$formdata = base64_decode(urldecode($formdata));
		$data = Globals::convertFormDataToArray($formdata, $toks );
		$toks = $data['toks'];

		if($toks){
			foreach ($data as $key => $value) {
				if($key != "toks") $data[$key] = $this->gibberish->decrypt( $value, $toks );
			}
		}

		$form = $toks ? $data['form']  : $this->input->post("form");

		$this->load->view('reports_excel/' . $form, $data);
	}

	function loadExcelReport(){
		$form = $this->input->get("form");
		$data = $this->input->get();
		$this->load->view('reports_excel/' . $form, $data);
	}

	function showPayslipReport(){
		$data = array();
		$data = $this->input->post();
		list($data["dfrom"], $data["dto"]) = explode(" ", $data["payrollcutoff"]);
		$data["dept"] = $data["deptid"];
		$data["campus"] = $data["campusid"];
		$data["eid"] = $data["employeeid"];
		// echo "<pre>"; print_r($data); die;
		// $data["sort"] = 0;
		$data['payroll_config'] = $this->extensions->getAllIncomeKeysAndDescription();
		$this->load->view('forms_pdf/payslip', $data);
	}

	function showExcelReport(){
		if($this->input->get()) exit('No direct script access allowed');
		$data = array();
		$data = $this->input->post();

		$this->load->view('reports_excel/'. $data["form"], $data);
	}

	function showPDFReport(){
		if($this->input->get()) exit('No direct script access allowed');
		$data = array();
		$data = $this->input->post();

		$this->load->view('forms_pdf/'. $data["form"], $data);
	}

	function getAllIncomeList(){
		$income = array();

		$q_income = $this->reports->getPayrollIncomeConfig("payroll_income_config","selectAll");
		foreach($q_income as $row){
			$income[$row["id"]]["description"] 	= $row["description"];
			$income[$row["id"]]["taxable"] 		= $row["taxable"];
		}

		return $income;
	}

	function explodeSelectedColConfig($col_config_tmp){
		$col_config = array();
		$col_config_tmp = explode("/", $col_config_tmp);

		foreach ($col_config_tmp as $config) {
			if($config){
				list($key, $value) = explode("=", $config);

				if($value) $col_config[$key] = $value;
			}
		}

		return $col_config;
	}

	function includeToSelectedConfig($config, $config_tmp){
		foreach ($config_tmp as $key => $value) {
			if(array_key_exists($key, $config)) $config[$key] += $value;
			else 								$config[$key] = $value;
		}

		asort($config);
		return $config;
	}

	function showIncomeAndOtherIncomeTransactionReportPerIncome(){
		if($this->input->get()) exit('No direct script access allowed');
		$data = array();
		$data = $this->input->post();
		list($data["cutoff_start"], $data["cutoff_end"]) = explode(",", $data["cutoff"]);
		$data["is_select_all"] = ($data["income"][0] == "allincome") ? true : false;
		$data["income_list"] = $this->getAllIncomeList();
		$data["department"] = array();
		$data["campus"] = $this->reports->getAllCampus();
		$q_emp_list = $this->reports->getPayrollComputedData($data["cutoff_start"], $data["cutoff_end"], $data["status"], $data["tnt"],$data["sort_by"]);
		// echo "<pre>"; print_r($this->db->last_query()); die;
		$data_list = $income_list = array();
		foreach ($data["income_list"] as $key => $info) 
			if(in_array($key, $data["income"])) $income_list[$key] = $info;

		if($data["is_select_all"]) $income_list = $data["income_list"];

		foreach ($q_emp_list as $row) {
			$income = $this->explodeSelectedColConfig($row->income);
			$income = $this->includeToSelectedConfig($income, $this->explodeSelectedColConfig($row->income_adj));
			if($row->overtime > 0) $income[40] = $row->overtime;

			if(count($income)){
				foreach ($income as $code => $amount) {
					if(array_key_exists($code, $income_list)){
						$sort_key = ($data["sort_by"] == "department") ? $row->office : "name";

						$data["department"][$row->office] = $row->dept_desc;
						$data_list[$code][$sort_key][$row->employeeid]["name"]   	= $row->fullname;
						$data_list[$code][$sort_key][$row->employeeid]["campusid"]  = $row->campusid;
						$data_list[$code][$sort_key][$row->employeeid]["amount"] 	= $amount;
						$data_list[$code][$sort_key][$row->employeeid]["status"] 	= $data['status'];
						$data_list[$code][$sort_key][$row->employeeid]["cutoff"] 	= $data['cutoff'];
					}
				}
			}
		}
		$data["data_list"] = $data_list;
		asort($data["campus"]);
		if($data['format_'] == "xls"){$this->load->view("reports_excel/income_report_per_income", $data);}
		else{$this->load->view("forms_pdf/income_report_per_income", $data);}
	}

	function showIncomeAndOtherIncomeTransactionReportPerEmployee(){
		if($this->input->get()) exit('No direct script access allowed');
		$data = array();
		$data = $this->input->post();
		list($data["cutoff_start"], $data["cutoff_end"]) = explode(",", $data["cutoff"]);
		$data["is_select_all"] = ($data["income"][0] == "allincome") ? true : false;
		$data["income_list"] = $this->getAllIncomeList();
		$data["department"] = array();
		$data["campus"] = $this->reports->getAllCampus();
		$q_emp_list = $this->reports->getPayrollComputedData($data["cutoff_start"], $data["cutoff_end"], $data["status"], $data["tnt"], $data["sort_by"]);

		$data_list = $income_list = array();
		foreach ($data["income_list"] as $key => $info) 
			if(in_array($key, $data["income"])) $income_list[$key] = $info;

		if($data["is_select_all"]) $income_list = $data["income_list"];

		foreach ($q_emp_list as $row) {
			$income = $this->explodeSelectedColConfig($row->income);
			$income = $this->includeToSelectedConfig($income, $this->explodeSelectedColConfig($row->income_adj));
			if($row->overtime > 0) $income[40] = $row->overtime;

			if(count($income)){
				foreach ($income as $code => $amount) {
					if(array_key_exists($code, $income_list)){
						$sort_key = ($data["sort_by"] == "department") ? $row->office : "name";

						$data["department"][$row->office] = $row->dept_desc;
						$data_list[$sort_key][$row->employeeid]["name"]   		 = $row->fullname;
						$data_list[$sort_key][$row->employeeid]["campusid"]  	 = $row->campusid;
						$data_list[$sort_key][$row->employeeid]["status"] 	= $data['status'];
						$data_list[$sort_key][$row->employeeid]["cutoff"] 	= $data['cutoff'];
						$data_list[$sort_key][$row->employeeid]["income"][$code] = $amount;
						
					}
				}
			}
		}
		$data["data_list"] = $data_list;
		asort($data["campus"]);
		
		// $this->load->view("reports_excel/income_report_per_employee", $data);
		if($data['format_'] == "xls"){$this->load->view("reports_excel/income_report_per_employee", $data);}
		else{$this->load->view("forms_pdf/income_report_per_employee", $data);}
	}

	function showOtherIncomeSetupReport(){
		if($this->input->get()) exit('No direct script access allowed');
		$data = array();
		$data["emplist"] = array();
		$deminimiss_arr = $this->reports->getDeminimissIncome();
		$isdetailed = $this->input->post('isdetailed');

		$q_list = $this->reports->getOtherIncomeEmployeeList();
		#echo "<pre>". $this->db->last_query();
		foreach ($q_list as $row) {
			if($isdetailed == "yes"){
				list($monthly,$daily,$hourly,$date_effective) = $this->extensions->getEmployeeLatestSalary($row->empID);

				$data["emplist"][$row->empID]["fullname"] = $row->fullname;
				
				if(array_key_exists($row->other_income, $deminimiss_arr)){
					if(!isset($data["emplist"][$row->empID]["income_list"]["deminimiss"])){
						$data["emplist"][$row->empID]["income_list"]["deminimiss"] = array(
							"income_desc" => "Deminimiss",
							"monthly"	  => 0,
							"daily"		  => 0,
							"hourly"	  => 0
						);
					}
					$data["emplist"][$row->empID]["income_list"]["deminimiss"]["monthly"] += $row->monthly;
					$data["emplist"][$row->empID]["income_list"]["deminimiss"]["daily"] += $row->daily;
					$data["emplist"][$row->empID]["income_list"]["deminimiss"]["hourly"] += $row->hourly;
					$data["emplist"][$row->empID]["income_list"]["deminimiss"]["date_effective"] = $row->dateEffective;
				}else{
					if($row->other_income){
						$data["emplist"][$row->empID]["income_list"][$row->other_income] = array(
							"income_desc" => strtolower($row->income_desc),
							"monthly"	  => $row->monthly,
							"daily"		  => $row->daily,
							"hourly"	  => $row->hourly,
							"date_effective" => $row->dateEffective
						);
					}
				}

				$data["emplist"][$row->empID]["income_list"]['salary'] = array(
					"income_desc" => "Salary",
					"monthly"	  => $monthly,
					"daily"		  => $daily,
					"hourly"	  => $hourly,
					"date_effective"	  => $date_effective
				);
			}else{
				list($monthly,$daily,$hourly,$date_effective) = $this->extensions->getEmployeeLatestSalary($row->empID);
				if(in_array($row->other_income, $deminimiss_arr)){
					$data["emplist"][$row->empID]["fullname"] = $row->fullname;
					if(!isset($data["emplist"][$row->empID]["income_list"]["deminimisss"])){
						$data["emplist"][$row->empID]["income_list"]["deminimisss"] = array(
							"income_desc" => "Deminimiss",
							"monthly"	  => 0,
							"daily"		  => 0,
							"hourly"	  => 0
						);
					}
					$data["emplist"][$row->empID]["income_list"]["deminimisss"]["monthly"] += $row->monthly;
					$data["emplist"][$row->empID]["income_list"]["deminimisss"]["daily"] += $row->daily;
					$data["emplist"][$row->empID]["income_list"]["deminimisss"]["hourly"] += $row->hourly;
					$data["emplist"][$row->empID]["income_list"]["deminimisss"]["date_effective"] = $row->dateEffective;

					$data["emplist"][$row->empID]["income_list"]['salary'] = array(
						"income_desc" => "Salary",
						"monthly"	  => $monthly,
						"daily"		  => $daily,
						"hourly"	  => $hourly,
						"date_effective"	  => $date_effective
					);
				}else{
					$data["emplist"][$row->empID]["fullname"] = $row->fullname;
					
					if($row->other_income){
						$data["emplist"][$row->empID]["income_list"][$row->other_income] = array(
							"income_desc" => strtolower($row->income_desc),
							"monthly"	  => $monthly,
							"daily"		  => $daily,
							"hourly"	  => $hourly,
							"date_effective" => $dateEffective
						);
					}
					
					$data["emplist"][$row->empID]["income_list"]['salary'] = array(
						"income_desc" => "Salary",
						"monthly"	  => $monthly,
						"daily"		  => $daily,
						"hourly"	  => $hourly,
						"date_effective"	  => $date_effective
					);
				}
			}
			
		}
		$data['isdetailed'] = $isdetailed;
		#echo "<pre>"; print_r($data); die;
		$this->load->view("forms_pdf/other_income_report", $data);
	}


	function showAttendanceCutOffReport(){
		if($this->input->get()) exit('No direct script access allowed');
		$this->load->model("attendance");

		$data = $setup = array();
		$data = $this->input->post();
		$categ_keys = '';
		$category = $data['sortby'];
		if($category == "department") $categ_keys = $data['dept_keys'];
		// else if($category == "campus") $categ_keys = $data['campus_keys'];
		// echo "<pre>"; print_r($data); die;

		if($category == "campus"){
			if(!$categ_keys) $setup = $this->reports->getAllCampus();
			else $setup = $categ_keys;
		}else{
			if(!$categ_keys) $setup = $this->extras->showoffice();
			// if(!$categ_keys) $setup = $this->reports->getAllDepartment();
			else $setup = $categ_keys;
		}

		list($data["date_from"], $data["date_to"]) = explode(",", $data["cutoff_date"]);

		$function = ($data["teaching_type"] == "teaching") ? "getAttendanceCutOffReportDataForTeaching" : "getAttendanceCutOffReportDataForNonTeaching";
		$emplist = $this->attendance->{$function}($data["cutoff_date"], $data["teaching_type"], $data["empid_list"], $category, $data["campus_keys"], "", $data["dept_keys"], $data["office_keys"], $data["empstat_"]);

		$data["emp_list"] = $emplist;

		$data['campus'] = $this->reports->getAllCampus();
		$data['department'] = $this->extras->showoffice();
		// $data['department'] = $this->reports->getAllDepartment();
		$this->load->view("forms_pdf/attendance_cutoff_report", $data);
	}

	function showAttendanceCutOffReportForEmpSide(){
		if($this->input->get()) exit('No direct script access allowed');

		$userid = $this->session->userdata('username');
		$this->load->model("attendance");
		$data = array();
		$toks = $this->input->post("toks");
		$data   = $this->input->post();

		if($toks){
		  unset($data["toks"]);
		  foreach($data as $key => $val){
		    $data[$key] = $this->gibberish->decrypt($val, $toks);
		  }
		}

		$data["campus"] = $this->extras->showdepartment();
		list($data["date_from"], $data["date_to"]) = explode(",", $data["cutoff_date"]);
		if ($data["dept_keys"] == "") {
			$depthead = $this->extensions->checkIfDeptHead($userid);
	        if($depthead){ 
	            $deptcodes = $this->extensions->getAllDepartmentUnder($userid);
	            $data["dept_keys"] = "'" . implode( "','", $this->db->escape($deptcodes) ) . "'";
	            $data["selected_dept"] = $data["dept_keys"];
	        }
		}

		$empid = "";
		if (isset($data["employeeid"])) $empid = $data["employeeid"];
		else $empid = $data["empid_list"];

		$function = ($data["teaching_type"] == "teaching") ? "getAttendanceCutOffReportDataForTeaching" : "getAttendanceCutOffReportDataForNonTeaching";
		$emplist = $this->attendance->{$function}($data["cutoff_date"], $data["teaching_type"], $empid, "", $data["selected_campus"], true, $data["selected_dept"]);
		
		$data["emp_list"] = array();
		foreach ($emplist as $sort_key => $list) {
			$data["emp_list"][$sort_key] = $list;
		}
		// echo "<pre>";print_r($data);die;
		$this->load->view("forms_pdf/attendance_cutoff_report", $data);
	}

	function showEmployeeBalancesPerEmployee(){
		if($this->input->get()) exit('No direct script access allowed');

		$this->load->model("attendance");
		$data = array();
		$loan_balance = $loan_amount = 0;
		$last_empid = '';
		$data = $this->input->post();
		$data["department"] = array();
		$data["campus"] = $this->reports->getAllCampus();
		if($data['deduction'] || $data['loandeduction']) $data['display_subtotal'] = true;
		$is_have_other_deduc = $is_have_loan = false;
		if($data["tag"] == "DEDUCTION") $is_have_other_deduc = true;
		else  							$is_have_loan = true;

		if($data['tag'] == "DEDUCTION"){
			list($data["cutoff_start"], $data["cutoff_end"]) = explode(",", $data["cutoff"]);
			$q_emp_list = $this->reports->getEmployeeListFromPayrollComputedTable($data["employeeid"], "", $data["cutoff_start"], $data["cutoff_end"], $data["status"], false, $is_have_other_deduc, $is_have_loan, $data['tag']);
		}else{
			$q_emp_list = $this->reports->getEmployeeListFromPayrollComputedTable($data["employeeid"], "", $data["cutoff"], $data["cutoff"], $data["status"], false, $is_have_other_deduc, $is_have_loan, $data['tag']);
		}

		$data_list = array();
		if($data['tag'] == "DEDUCTION"){
			foreach ($q_emp_list as $row) {
				$payroll_col_selected = ($data["tag"] == "DEDUCTION") ? "otherdeduc" : "loan";
				$other_deduc_loan_list = $this->explodeSelectedColConfig($row->{$payroll_col_selected});
				
				if(count($other_deduc_loan_list) > 0){
					$selected_deduc_loan_key = ($data["tag"] == "DEDUCTION") ? $data["deduction"] : $data["loandeduction"];
					
					foreach ($other_deduc_loan_list as $code => $amount) {
						$is_add_to_list = true;

						if($selected_deduc_loan_key && $code != $selected_deduc_loan_key) $is_add_to_list = false;
						
						if($is_add_to_list){
							$sort_key = ($data["sort_by"] == "department") ? $row->deptid : "name";
							
							$data["department"][$row->deptid] = $row->dept_desc;
							$data_list[$sort_key][$row->employeeid]["name"] = $row->fullname;
							$data_list[$sort_key][$row->employeeid]["campusid"] = $row->campusid;
							$data_list[$sort_key][$row->employeeid]["loan_deduc_list"][$code]["balance"] = $this->reports->getLoanBalance($row->employeeid, $code, $data["cutoff_end"]);
							$data_list[$sort_key][$row->employeeid]["loan_deduc_list"][$code]["amount"] = $amount;

						}
					}
				}
			}
		}else{
			foreach ($q_emp_list as $row) {
				$payroll_col_selected = ($data["tag"] == "DEDUCTION") ? "otherdeduc" : "loan";
				$other_deduc_loan_list = $this->explodeSelectedColConfig($row->{$payroll_col_selected});
				
				if(count($other_deduc_loan_list) > 0){
					$selected_deduc_loan_key = ($data["tag"] == "DEDUCTION") ? $data["deduction"] : $data["loandeduction"];
					
					foreach ($other_deduc_loan_list as $code => $amount) {
						$is_add_to_list = true;

						if($selected_deduc_loan_key && $code != $selected_deduc_loan_key) $is_add_to_list = false;
						
						if($is_add_to_list){
							$sort_key = ($data["sort_by"] == "department") ? $row->deptid : "name";
							
							$data["department"][$row->deptid] = $row->dept_desc;
							$data_list[$sort_key][$row->employeeid]["name"] = $row->fullname;
							$data_list[$sort_key][$row->employeeid]["campusid"] = $row->campusid;
							$data_list[$sort_key][$row->employeeid]["loan_deduc_list"][$code]["balance"] = $this->reports->getLoanBalance($row->employeeid, $code, $data["cutoff"], true);
							$data_list[$sort_key][$row->employeeid]["loan_deduc_list"][$code]["amount"] += $amount;
					
						}
					}
				}
				$last_empid = $row->employeeid;
			}
		}

		if (strpos($data['cutoff'], "~~") !== false) {
		    $data['cutoff_start'] = str_replace("~~"," ",$data['cutoff']);
		    $converted_date = date("Y-m", strtotime($data['cutoff_start']));
		    list($data['cutoff_start'], $data['cutoff_end']) = $this->extensions->getCutoffdate($converted_date);

		}

		$data["config"] = $this->reports->getPayrollConfig(($data["tag"] == "DEDUCTION") ? "deduction" : "loan");
		$data["data_list"] = $data_list;
		// $this->load->view("reports_excel/employee_balances_per_employee", $data);
		if($data['format_'] == "xls"){$this->load->view("reports_excel/employee_balances_per_employee", $data);}
		else{$this->load->view("forms_pdf/employee_balances_per_employee", $data);}
	}

	function showEmployeeBalancesPerDeduction(){
		if($this->input->get()) exit('No direct script access allowed');

		$this->load->model("attendance");
		$data = array();
		$data = $this->input->post();
		$data["department"] = array();
		$data["campus"] = $this->reports->getAllCampus();
		$is_have_other_deduc = $is_have_loan = false;
		if($data["tag"] == "DEDUCTION") $is_have_other_deduc = true;
		else  							$is_have_loan = true;

		if($data['tag'] == "DEDUCTION"){
			list($data["cutoff_start"], $data["cutoff_end"]) = explode(",", $data["cutoff"]);
			$q_emp_list = $this->reports->getEmployeeListFromPayrollComputedTable($data["employeeid"], "", $data["cutoff_start"], $data["cutoff_end"], $data["status"], false, $is_have_other_deduc, $is_have_loan, $data['tag']);
		}else{
			$q_emp_list = $this->reports->getEmployeeListFromPayrollComputedTable($data["employeeid"], "", $data["cutoff"], $data["cutoff"], $data["status"], false, $is_have_other_deduc, $is_have_loan, $data['tag']);
		}

		$data_list = array();
		$old_employeeid = '';
			if($row->employeeid == $old_employeeid){

			foreach ($q_emp_list as $row) {
				$payroll_col_selected = ($data["tag"] == "DEDUCTION") ? "otherdeduc" : "loan";
				$other_deduc_loan_list = $this->explodeSelectedColConfig($row->{$payroll_col_selected});
				
				if(count($other_deduc_loan_list) > 0){
					$selected_deduc_loan_key = ($data["tag"] == "DEDUCTION") ? $data["deduction"] : $data["loandeduction"];
					
					foreach ($other_deduc_loan_list as $code => $amount) {
						$is_add_to_list = true;

						if($selected_deduc_loan_key && $code != $selected_deduc_loan_key) $is_add_to_list = false;
						
						if($is_add_to_list){
							$sort_key = ($data["sort_by"] == "department") ? $row->deptid : "name";
							
							$data["department"][$row->deptid] = $row->dept_desc;
							$data_list[$code][$sort_key][$row->employeeid]["name"] = $row->fullname;
							$data_list[$code][$sort_key][$row->employeeid]["campusid"] = $row->campusid;
							$data_list[$code][$sort_key][$row->employeeid]["balance"] += $this->reports->getLoanBalance($row->employeeid, $code, $data["cutoff_end"]);
							$data_list[$code][$sort_key][$row->employeeid]["amount"] += $amount;

						}
					}
				}
			}
			}else{
			foreach ($q_emp_list as $row) {
				$payroll_col_selected = ($data["tag"] == "DEDUCTION") ? "otherdeduc" : "loan";
				$other_deduc_loan_list = $this->explodeSelectedColConfig($row->{$payroll_col_selected});
				
				if(count($other_deduc_loan_list) > 0){
					$selected_deduc_loan_key = ($data["tag"] == "DEDUCTION") ? $data["deduction"] : $data["loandeduction"];
					
					foreach ($other_deduc_loan_list as $code => $amount) {
						$is_add_to_list = true;

						if($selected_deduc_loan_key && $code != $selected_deduc_loan_key) $is_add_to_list = false;
						
						if($is_add_to_list){
							$sort_key = ($data["sort_by"] == "department") ? $row->deptid : "name";
							
							$data["department"][$row->deptid] = $row->dept_desc;
							$data_list[$code][$sort_key][$row->employeeid]["name"] = $row->fullname;
							$data_list[$code][$sort_key][$row->employeeid]["campusid"] = $row->campusid;
							$data_list[$code][$sort_key][$row->employeeid]["balance"] += $this->reports->getLoanBalance($row->employeeid, $code, $data["cutoff"]);
							$data_list[$code][$sort_key][$row->employeeid]["amount"] += $amount;

						}
					}
				}
			}
			}
			$old_employeeid = $row->employeeid;
		

		if (strpos($data['cutoff'], "~~") !== false) {
		    $data['cutoff_start'] = str_replace("~~"," ",$data['cutoff']);
		    $converted_date = date("Y-m", strtotime($data['cutoff_start']));
		    list($data['cutoff_start'], $data['cutoff_end']) = $this->extensions->getCutoffdate($converted_date);

		}

		$data["config"] = $this->reports->getPayrollConfig(($data["tag"] == "DEDUCTION") ? "deduction" : "loan");
		$data["data_list"] = $data_list;
		// $this->load->view("reports_excel/employee_balances_per_deduction", $data);
		if($data['format_'] == "xls"){$this->load->view("reports_excel/employee_balances_per_deduction", $data);}
		else{$this->load->view("forms_pdf/employee_balances_per_deduction", $data);}
	}

	function showAlphalistNew(){
		$this->load->model("income");
		$year = $this->input->post('year');
		$schedule = $this->input->post('schedule');
		$fixed_deduc = array("SSS","PHILHEALTH","PAGIBIG","PERAA");
		$gross_and_tax = array("GROSS PAY","W/TAX");
	  	$getAllCutoffPerYear = $this->extensions->getAllCutoffPerYear($year);
	  	$taxable_income = $this->extensions->getTaxableIncome();
	  	$nontaxable_income = $this->extensions->getNonTaxableIncome();
	  	$old_empid = $old_date = '';	
	  	$data['emp_list_total'] = array();
	  	if($getAllCutoffPerYear){
	        foreach ($getAllCutoffPerYear as $key => $date) {
	        	$old_date = $date['startdate']."-".$date['enddate'];
	        	$emp_data = $this->extensions->getPayrollComputedData($date['startdate'], $date['enddate']);

	        	foreach($emp_data as $emp_key => $emp_info){
	        		$data['emp_list'][$date['startdate']."-".$date['enddate']][$emp_info['employeeid']]['employeeid'] = $emp_info['employeeid'];
	        		$data['emp_list'][$date['startdate']."-".$date['enddate']][$emp_info['employeeid']]['fullname'] = $emp_info['fullname'];
	        		$data['emp_list'][$date['startdate']."-".$date['enddate']][$emp_info['employeeid']]['salary'] = $emp_info['salary'];
	        		foreach($taxable_income as $inc_id => $inc_arr){
	        			$data['emp_list'][$date['startdate']."-".$date['enddate']][$emp_info['employeeid']]['income'][$inc_arr['id']] = $this->getIncomeValue($emp_info['income'], $inc_arr['id']);
	        		}
	        		foreach($fixed_deduc as $deduc_id => $deduc_arr){
	        			$data['emp_list'][$date['startdate']."-".$date['enddate']][$emp_info['employeeid']]['fixeddeduc'][$deduc_arr] = $this->getFixeddeducValue($emp_info['fixeddeduc'], $deduc_arr);
	        		}

	        		$data['emp_list'][$date['startdate']."-".$date['enddate']][$emp_info['employeeid']]['gross'] = $emp_info['gross'];
	        		$data['emp_list'][$date['startdate']."-".$date['enddate']][$emp_info['employeeid']]['withholdingtax'] = $emp_info['withholdingtax'];

	        		/*for total*/
	        		if($old_date == $date['startdate']."-".$date['enddate'] || $old_empid == $emp_info['employeeid']){
	        			$data['emp_list_total'][$emp_info['employeeid']]['employeeid'] = $emp_info['employeeid'];
		        		$data['emp_list_total'][$emp_info['employeeid']]['fullname'] = $emp_info['fullname'];
		        		$data['emp_list_total'][$emp_info['employeeid']]['salary'] += $emp_info['salary'];
		        		foreach($taxable_income as $inc_id => $inc_arr){
		        			if($inc_arr['id'] != 56) $data['emp_list_total'][$emp_info['employeeid']]['income_wtax'] += $this->getIncomeValue($emp_info['income'], $inc_arr['id']);
		        			else $data['emp_list_total'][$emp_info['employeeid']]['_13thmonth_wt'] = $this->getIncomeValue($emp_info['income'], $inc_arr['id']);
		        		}

		        		foreach($nontaxable_income as $inc_id => $inc_arr){
		        			if($inc_arr['id'] != 57) $data['emp_list_total'][$emp_info['employeeid']]['income_ntax'] += $this->getIncomeValue($emp_info['income'], $inc_arr['id']);
		        			else $data['emp_list_total'][$emp_info['employeeid']]['_13thmonth_nt'] = $this->getIncomeValue($emp_info['income'], $inc_arr['id']);
		        		}
		        		foreach($fixed_deduc as $deduc_id => $deduc_arr){
		        			$data['emp_list_total'][$emp_info['employeeid']]['fixeddeduc'][$deduc_arr] += $this->getFixeddeducValue($emp_info['fixeddeduc'], $deduc_arr);
		        		}

		        		$data['emp_list_total'][$emp_info['employeeid']]['gross'] += $emp_info['gross'];
		        		$data['emp_list_total'][$emp_info['employeeid']]['withholdingtax'] += $emp_info['withholdingtax'];
	        		}
	        		/*end*/
	        		$old_empid = $emp_info['employeeid'];
	        	}
	        }
	    }
        
        $data['schedule'] = $schedule;
        $data['fixed_deduc'] = $fixed_deduc;
        $data['getAllCutoffPerYear'] = $getAllCutoffPerYear;
        $data['taxable_income'] = $taxable_income;
        $data['gross_and_tax'] = $gross_and_tax;
        $data['year'] = $year;
        // echo "<pre>"; print_r($data); die;
		$this->load->view('reports_excel/alphalistform_new', $data);
	}

	function getIncomeValue($emp_income, $id){
        $income_val = array();
    	$emp_income = explode("/", $emp_income);
    	foreach($emp_income as $key => $value){
	        $exploded_income = explode("=", $value);
	        $income_val[$exploded_income[0]] = $exploded_income[1];
	    }	    

      	return isset($income_val[$id]) ? $income_val[$id] : 0;
    }

    function getFixeddeducValue($emp_fixeddeduc, $id){
        $fixeddeduc_val = array();
    	$emp_fixeddeduc = explode("/", $emp_fixeddeduc);
    	foreach($emp_fixeddeduc as $key => $value){
	        $exploded_fixeddeduc = explode("=", $value);
	        $fixeddeduc_val[$exploded_fixeddeduc[0]] = $exploded_fixeddeduc[1];
	    }	    

      	return isset($fixeddeduc_val[$id]) ? $fixeddeduc_val[$id] : 0;
    }

    function showIncomeAdjustmentReport(){
    	$data = array();
    	$data = $this->input->post();
		$data["campus"] = $this->reports->getAllCampus();
		$data["department"] = array();

    	$selected_income = (in_array("selectAll", $data["income"])) ? "" : implode(",", $data["income"]);
    	$data["setup"] = $this->reports->getPayrollIncomeAdjustment($selected_income);

    	list($data["cutoff_start"], $data["cutoff_end"]) = explode(",", $data["cutoff"]);
    	$q_payroll_computed = $this->reports->getPayrollComputedData($data["cutoff_start"], $data["cutoff_end"], $data["status"], $data["tnt"]);

    	$data["list"] = array();
    	foreach ($q_payroll_computed as $row) {
    		$sort = ($data["sort_by"] == "department") ? (($row->deptid == "ACAD") ? $row->deptid ."-". $row->campusid : $row->deptid) : "name";
    		$income = $this->explodeSelectedColConfig($row->income);
    		
    		$data["department"][$row->deptid] = $row->dept_desc;
    		foreach ($income as $code => $amount) {
    			if(array_key_exists($code, $data["setup"])){
					
					if($data["form"] == "incomeadj"){
						$data["list"][$code][$sort][] = array(
							"employeeid" => $row->employeeid,
							"name" 		 => $row->fullname,
							"amount"	 => $amount
						);
					}else{
						$data["department"][$sort] = ($row->deptid == "ACAD") ? $row->dept_desc ." - ". $data["campus"][$row->campusid] : $row->dept_desc;
						$data["list"][$sort][$row->employeeid]["name"] = $row->fullname;
						$data["list"][$sort][$row->employeeid]["income"][] = $data["setup"][$code]["description"];
						$data["list"][$sort][$row->employeeid]["amount"][] = $amount;
					}
    			}
    		}
    	}

    	$data["title"] = "Income Adjustment(Per ". (($data["form"] == "incomeadj") ? "Income" : "Employee") .")";
    	$this->load->view("reports_excel/income_adjustment_report", $data);
    }

	function generateConfirmedAttendance(){
		$this->load->model('hr_reports');
		$cutoff 		= $this->input->get('cutoff');
		$teachingtype 		= $this->input->get('teachingtype');
		$employeeid 		= $this->input->get('employeeid');
		$dates = explode(',',$cutoff);
		if(isset($dates[0]) && isset($dates[1])){
			$sdate = $dates[0];
			$edate = $dates[1];
			list($dtr_start,$dtr_end,$payroll_start,$payroll_end) = $this->payrolloptions->getDtrPayrollCutoffPair($sdate,$edate);
		}else{
			echo 'Invalid Cutoff';
			return;
		}

		///< get data
		$data['cutoff'] = $cutoff;
		$data['attendance_list'] = $this->hr_reports->getAttConfirmed_summary($teachingtype,$sdate,$edate,$payroll_start,$employeeid);
	
		$this->load->model('utils');
		$data['showfinalize'] = $this->employeemod->showFinalize($sdate,$edate,$teachingtype,$payroll_start,$payroll_end);
		$data['office_list'] = $this->utils->getOffice();
		$data['sdate'] = $sdate;
		$data['edate'] = $edate;
		$data['teachingtype'] = $teachingtype;
		$data['dateRange'] = $this->time->createRangeToDisplay($sdate, $edate);


		if($teachingtype == "teaching") $this->load->view('reports_excel/attendance_confirmed',$data);
		else $this->load->view('reports_excel/attendance_confirmed_nt',$data);
	}

	public function payrollRegisterReport(){
		$this->load->model("payrollprocess");
		$deptid     = $this->input->post('deptid');
		$employeeid = $this->input->post('eid');
		$schedule   = $this->input->post('schedule');
		$cutoff     = $this->input->post('cutoff');
		$cutoff = str_replace("+", " ", $cutoff);
		$cutoffdate = explode(" ", $cutoff);
		$sdate = $cutoffdate[0];
		$edate = $cutoffdate[1];
		if(!$sdate && !$edate){
			$sdate = $this->input->post("dfrom");
			$edate = $this->input->post("dto");
		}
		$quarter    = $this->input->post('quarter');
		$emplist    = $this->input->post('emplist');
		$status    = $this->input->post('status');
		$bank    = $this->input->post('bank');
		$income_config = $this->extensions->getAllIncomeKeysAndDescription();
		$deduction_config = $this->extensions->getAllDeductionKeysAndDescription();
		$fixeddeduc_config = $this->extensions->getAllFixedDeductionKeysAndDescription();
		$loan_config = $this->extensions->getAllLoanKeysAndDescription();
		$res = $this->payrollprocess->getPayrollSummary($status,$sdate,$edate,$schedule,$quarter,$employeeid,false,'',$bank);
		foreach ($res->result() as $key => $row) {
            $officesort[$key]  = trim($row->office_code);
            $fullnamesort[$key] = trim($row->lname);
        }
        array_multisort($officesort, SORT_ASC, $fullnamesort, SORT_ASC, $res->result());

		$inc_disp = $inc_adj_disp = $fixeddeduc_disp = $loan_disp = $deduc_disp = array();
		foreach($res->result() as $row){
			// echo "<pre>"; print_r($row); die;
			$income = $this->payrollprocess->constructArrayListFromComputedTable($row->income);
			$deduction = $this->payrollprocess->constructArrayListFromComputedTable($row->otherdeduc);
			$fixeddeduc = $this->payrollprocess->constructArrayListFromComputedTable($row->fixeddeduc);
			$loan = $this->payrollprocess->constructArrayListFromComputedTable($row->loan);
			$income_adj = $this->payrollprocess->constructArrayListFromComputedTable($row->income_adj);
			$employeeid = $row->employeeid;
			$emplist[$employeeid] = array(
				"office" => $this->extensions->getEmployeeOfficeDesc($employeeid),
				"employeeid" => $employeeid,
				"fullname" => $this->extensions->getEmployeeName($employeeid),
				"regpay" => number_format($row->salary, 2),
				"tardy" => number_format($row->tardy, 2),
				"absent" => number_format($row->absent, 2),
				"netbasicpay" => number_format($row->netbasicpay, 2)
			);
		


			foreach($income as $key => $val){
				$inc_name  = isset($income_config[$key]) ? $key : "";
				if($inc_name) $emplist[$employeeid]["income"][$inc_name] = $val;

				$inc_disp[$key] = isset($income_config[$key]) ? trim($income_config[$key]) : "";
			}

			$emplist[$employeeid]["overtime"] = number_format($row->overtime, 2);
			$emplist[$employeeid]["grosspay"] = number_format($row->grosspay, 2);

			$emplist[$employeeid]["witholdingtax"] = number_format($row->witholdingtax, 2);

			foreach($fixeddeduc as $key => $val){
				$emplist[$employeeid]["fixeddeduc"][$key] = number_format($val, 2);
				$fixeddeduc_disp[$key] = isset($fixeddeduc_config[$key]) ? trim($fixeddeduc_config[$key]) : "";
			}

			foreach($deduction as $key => $val){
				$deduc_name  = isset($deduction_config[$key]) ? $key : "";
				if($deduc_name) $emplist[$employeeid]["deduction"][$deduc_name] = $val;
				$deduc_disp[$key] = isset($deduction_config[$key]) ? trim($deduction_config[$key]) : "";
			}

			foreach($loan as $key => $val){
				$loan_name  = isset($loan_config[$key]) ? $key : "";
				if($loan_name) $emplist[$employeeid]["loan"][$loan_name] = $val;
				$loan_disp[$key] = isset($loan_config[$key]) ? trim($loan_config[$key]) : "";
			}

			foreach($income_adj as $key => $val){
				$inc_name  = isset($income_config[$key]) ? $key : "";
				if($inc_name) $emplist[$employeeid]["income_adj"][$inc_name] = $val;
				$inc_adj_disp[$key] = isset($income_config[$key]) ? trim($income_config[$key]) : "";
			}

			$emplist[$employeeid]["netpay"] = number_format($row->net, 2);
		}


		// foreach ($emplist as $key => $row) {
  //           $officesort[$key]  = $row['office'];
  //           $fullnamesort[$key] = $row['fullname'];
  //       }
  //       array_multisort($officesort, SORT_ASC, $fullnamesort, SORT_ASC, $emplist);



		$data["emplist"] = $emplist;
		$data["date_from"] = $sdate;
		$data["date_to"] = $edate;
		$data["inc_disp"] = $inc_disp;
		$data["inc_adj_disp"] = $inc_adj_disp;
		$data["deduc_disp"] = $deduc_disp;
		$data["fixeddeduc_disp"] = $fixeddeduc_disp;
		$data["loan_disp"] = $loan_disp;
		// echo "<pre>"; print_r($data); die;
		$this->load->view("forms_pdf/payrollregister", $data);
	}

	public function confirmed_history(){
		$formdata = $this->input->get("formdata") ? $this->input->get("formdata") : $this->input->post("formdata");
		$formdata = base64_decode(urldecode($formdata));
		$data = Globals::convertFormDataToArray($formdata, $toks );
		$toks = $data['toks'];

		if($toks){
			foreach ($data as $key => $value) {
				if($key != "toks") $data[$key] = $this->gibberish->decrypt( $value, $toks );
			}
		}

		$confirmed_list = array();
		list($cutoffstart, $cutoffend) = explode(",", $data["cutoff"]);

		$teaching_att = $this->attendance->confirmed_history($cutoffstart, $cutoffend, $data["dateFrom"], $data["dateTo"], $data["deptid"]);
		if($teaching_att->num_rows() > 0) $confirmed_list = array_merge($teaching_att->result_array());
		// echo "<pre>"; print_r($this->db->last_query()); die;
		
		$nonteaching_att = $this->attendance->confirmed_history_nt($cutoffstart, $cutoffend, $data["dateFrom"], $data["dateTo"], $data["deptid"]);
		if($nonteaching_att->num_rows() > 0) $confirmed_list = array_merge($nonteaching_att->result_array(), $confirmed_list);

		$data["payroll_cutoff"] = $this->extensions->getPayrollCutoffConfig($cutoffstart, $cutoffend);
		$data["confirmed_list"] = $confirmed_list;
		if($data["schedformat"] == "PDF") $this->load->view("forms_pdf/confirmed_history", $data);
		else $this->load->view("reports_excel/confirmed_history", $data);
	}

}
/* End of file forms.php*/
/* Location: ./application/controllders/*/