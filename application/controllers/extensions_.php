<?php
/**
* @author Max Consul
* @copyright 2018
* 
* extension controller
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Extensions_ extends CI_Controller{

	public function __construct(){
	    parent::__construct();
	    if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
	}

	public function getEmplistForDepartmentAttendance(){
		$userid = $this->session->userdata('username');
		$toks = $this->input->post("toks");
		$data   = $this->input->post();
		if($toks){
		  unset($data["toks"]);
		  foreach($data as $key => $val){
		    $data[$key] = $this->gibberish->decrypt($val, $toks);
		  }
		}
		$return = $where_clause = "";
		$teachingtype = $data['teachingtype'];
		if($teachingtype) $where_clause .= " AND teachingtype = '$teachingtype' ";
		$selected_campus = $data['selected_campus'];
		$selected_dept = $data['selected_dept'];
		$userid = $this->session->userdata('username');
		$return = '<option value="">All Employees</option>';
		$depthead = $this->extensions->checkIfDeptHead($userid);
		if($depthead){ 
			$deptcodes = $this->extensions->getAllDepartmentUnder($userid);
			$deptcodes = "'" . implode( "','", $this->db->escape($deptcodes) ) . "'";
			$where_clause .= " AND deptid IN ($deptcodes) " ;
		}
		$officehead = $this->extensions->checkIfOfficeHead($userid);
		if($officehead){ 
			$officecodes = $this->extensions->getAllOfficeUnder($userid);
			$officecodes = "'" . implode( "','", $this->db->escape($officecodes) ) . "'";
			$where_clause .= " AND office IN ($officecodes) " ;
		}
		$campusprincipal = $this->extensions->checkIfCampusPrincipal($userid);
		if($campusprincipal){ 
			$campuscodes = $this->extensions->getAllCampusUnder($userid);
			$campuscodes = "'" . implode( "','", $this->db->escape($campuscodes) ) . "'";
			$where_clause .= " AND campusid IN ($campuscodes) " ;
		}
		$employee_list = $this->extensions->getEmplistForDepartmentAttendance($where_clause, $teachingtype);
		foreach($employee_list as $value){
			$return .= '<option value="'. $value['employeeid'] .'">'. $value['employeeid']." - ".$value['fullname'] .'</option>';
		}
		echo $return;

	}

	public function deleteZeroCutoff(){
		$success_count = $error_count = 0;
		$err_empid = "Failed to process Employee ID: ";
		$data = $this->input->post('employee_id');
		$code_based = $this->input->post('code');
		$be_tag = $this->input->post('be_tag');
		foreach($data as $empid => $no_cutoff){
			$response = $this->extensions->deleteZeroCutoff($empid, $no_cutoff, $code_based, $be_tag);
			if($response) $success_count += 1;
			else{
				$error_count += 1;
				$err_empid .= $empid." , ";
			}
		}

		$return = array(
			"success_count" => $success_count,
			"error_count" => $error_count,
			"err_empid" => $err_empid
		);

		echo json_encode($return);
	}

	public function getZeroCutoff(){
		$success_count = $error_count = 0;
		$err_empid = "Failed to process Employee ID: ";
		$data = $this->input->post('employee_id');
		$code_based = $this->input->post('code');
		$be_tag = $this->input->post('be_tag');
		foreach($data as $empid => $no_cutoff){
			$response = $this->extensions->getZeroCutoff($empid, $no_cutoff, $code_based, $be_tag);
			if($response) $success_count += 1;
			else{
				$error_count += 1;
				$err_empid .= $empid." , ";
			}
		}

		$return = array(
			"success_count" => $success_count,
			"error_count" => $error_count,
			"err_empid" => $err_empid
		);

		echo json_encode($return);
	}

	public function getAccountSetup(){
		$query = array();
		$category = $this->input->post('category');
		$return = '<option value="">Select account</option>';
		if($category == "income") $query = $this->extensions->getIncomeSetup();
		else if($category == "deduction") $query = $this->extensions->getDeductionSetup();
		else if($category == "loan") $query = $this->extensions->getLoanSetup();
		else if($category == "regdeduction"){
			$query = $this->extensions->getFixedDeductionSetup();
			foreach($query as $key => $value){
				$return .= '<option value="'. $key .'">'. $value .'</option>';
			}
			echo $return; die;
		}else if($category == "witholdingtax"){
			$return = '<option value="Witholdingtax">No account available.</option>';
		}

		foreach($query as $value){
			$return .= '<option value="'. $value['id'] .'">'. $value['description'] .'</option>';
		}

		echo $return;

	}

	public function getAvailableYearInCutoff(){
		$return = '<option value="">Select available year</option>';
		$query = $this->db->query("SELECT DISTINCT DATE_FORMAT(CutoffFrom, '%Y') AS year FROM cutoff ")->result_array();
		foreach($query as $value){
			$return .= '<option value="'. $value['year'] .'">'. $value['year'] .'</option>';
		}

		echo $return;
	}

	public function loadEncodedHistory(){
		$type = $this->input->post('type');
		$data = $this->extensions->getSpecialVoucherData($type);
		$data['type'] = $type;
		$this->load->view('payroll/special_voucher/sv_history', $data);
	}

	public function validateEncodedData(){
		$success_count = $failed_count = 0;
		$records = array();
		$data = $this->input->post();
		$iscontinue = $this->checkIfNoValue($data);

		if(count($data['employee_list']) > 1){
			if(in_array("all", $data['employee_list'])){
				echo "Already selected all employees."; die;
			}
		}else{
			if(in_array("all", $data['employee_list'])){
				unset($data['employee_list'][0]);
				$employees = $this->extensions->getActiveEmployees();
				foreach($employees as $key => $value){
					$data['employee_list'][$value['employeeid']] = $value['employeeid'];
				}
			}
		}
		if($iscontinue){
			if($data['category'] != "witholdingtax"){
				foreach($data['employee_list'] as $key => $value){	
					$records = array(
						"employeeid" => $value,
						"fullname" => $this->extensions->getEmployeeName($value),
						"account" => $data['account'],
						"type" => $data['category'],
						"amount" => $data['amount'],
						"cutoff" => $data['cutoff'],
						"remarks" => $data['remarks']
					);

					$save_data = $this->extensions->insertSpecialVoucher($records);
					if($save_data) $success_count += 1;
					else $failed_count += 1;
				}
			}else{
				foreach($data['employee_list'] as $key => $value){	
					$records = array(
						"employeeid" => $value,
						"fullname" => $this->extensions->getEmployeeName($value),
						"type" => $data['category'],
						"amount" => $data['amount']
					);

					$save_data = $this->extensions->insertSpecialVoucher($records);
					if($save_data) $success_count += 1;
					else $failed_count += 1;
				}
			}
		}

		echo "Successfully saved ".$success_count." employees.";
	}

	public function checkIfNoValue($array = array()){
		if(count($array) > 0){
			foreach($array as $key => $value){
				if(is_array($value)){
					if(count($value) == 0) return false;
				}else{
					if(!$value) return false;
				}
			}
		}else{
			return false;
		}

		return true;
	}

	public function editEncodedVoucher(){
		$data = array();
		$employeeid = $this->input->post('employeeid');
		$category = $this->input->post('category');
		$account = $this->input->post('account');
		$cutoff = $this->input->post('cutoff');
		$remarks = $this->input->post('remarks');
		$emp_rec = $this->extensions->editSpecialVoucherData($employeeid, $category, $account);
		if($category != "witholdingtax"){
			foreach($emp_rec as $key => $value){
				$data['fullname'] = $value['fullname'];
				$data['employeeid'] = $value['employeeid'];
				$data['account'] = $value['account'];
				$data['amount'] = $value['amount'];
				$data['type'] = $value['type'];
				$data['cutoff'] = $value['cutoff'];
				$data['remarks'] = $remarks;
				$data['date_encode'] = $value['employeeid'];
			}
		}else{
			foreach($emp_rec as $key => $value){
				$data['fullname'] = $value['fullname'];
				$data['employeeid'] = $value['employeeid'];
				$data['amount'] = $value['amount'];
				$data['type'] = $value['type'];
				$data['type'] = $value['type'];
				$data['cutoff'] = $value['cutoff'];
				$data['remarks'] = $remarks;
				$data['date_encode'] = $value['employeeid'];
			}
		}
		$this->load->view('payroll/special_voucher/sv_edit', $data);
	}

	public function validateUpdateVoucher(){
		$data = $this->input->post();
		$iscontinue = $this->checkIfNoValue($data);
		if($iscontinue){
			$update_data['employeeid'] = $data['edit_employeeid'];
			$update_data['remarks'] = $data['edit_remarks'];
			$update_data['cutoff'] = $data['edit_cutoff'];
			$update_data['account'] = $data['edit_account'];
			$update_data['amount'] = $data['edit_amount'];
			$update_data['type'] = $data['edit_type'];
			$update_action = $this->extensions->updateVoucherData($update_data);
			if($update_action) echo "Successfully Updated";
			else echo "Failed to update";
		}else{
			echo "Please fill-up all the fields.";
		}
	}

	public function deleteEncodedVoucher(){
		$data = array();
		$employeeid = $this->input->post('employeeid');
		$category = $this->input->post('category');
		$account = $this->input->post('account');
		$data = $this->extensions->getSpecialVoucherData($category);
		$emp_rec = $this->extensions->editSpecialVoucherData($employeeid, $category, $account);
		if($category != "witholdingtax"){	
			foreach($emp_rec as $key => $value){
				$data['fullname'] = $value['fullname'];
				$data['employeeid'] = $value['employeeid'];
				$data['amount'] = $value['amount'];
				$data['account'] = $value['account'];
				$data['type'] = $value['type'];
			}
		}else{
			foreach($emp_rec as $key => $value){
				$data['fullname'] = $value['fullname'];
				$data['employeeid'] = $value['employeeid'];
				$data['amount'] = $value['amount'];
				$data['type'] = $value['type'];
			}
		}
		$this->load->view('payroll/special_voucher/sv_delete', $data);
	}

	public function validateDeleteVoucher(){
		$data = $this->input->post();
		$iscontinue = $this->checkIfNoValue($data);
		if($iscontinue){
			if($data['delete_type'] != "witholdingtax"){
				$delete_data['employeeid'] = $data['delete_employeeid'];
				$delete_data['account'] = isset($data['delete_account']) ? $data['delete_account'] : "";
				$delete_data['type'] = $data['delete_type'];
				$delete_action = $this->extensions->deleteVoucherData($delete_data);
				if($delete_action) echo "Successfully Deleted Special Voucher";
				else echo "Failed to update";
			}else{
				$delete_data['employeeid'] = $data['delete_employeeid'];
				$delete_data['type'] = $data['delete_type'];
				$delete_action = $this->extensions->deleteVoucherData($delete_data);
				if($delete_action) echo "Successfully Deleted Special Voucher";
				else echo "Failed to update";
			}
		}else{
			echo "Please fill-up all the fields.";
		}
	}

	public function checkIfSystemIsRecomputing(){
		$tnt = $this->input->post('tnt');
		$iscontinue = $this->extensions->checkIfSystemIsRecomputing($tnt);
		echo $iscontinue;
	}

	public function getEmployeeGender(){
		$toks = $this->input->post("toks");
		$employeeid = ($toks) ? $this->gibberish->decrypt($this->input->post("employeeid"), $toks) : $this->input->post("employeeid");
		echo $this->extensions->getEmployeeGender($employeeid);
	}

	public function showreportseduclevel(){
		$option = "<option value=''> - Select a option - </option>";
		$code = $this->input->post("code");
		$idkey = $this->input->post("idkey");
		$educlevel = $this->extras->showreportseduclevelseminar($code);
		foreach($educlevel as $c=>$val){
            if($c == $idkey) $option .= "<option value='".$c."' selected> ".$val." </option>";
            else $option .= "<option value='".$c."'> ".$val." </option>";
        }

        echo $option;
	}

	public function dataRetreival(){
		$dbname = array('Poveda_DTR_apr25'=>"Poveda_DTR_apr25", 'Poveda_DTR_may6'=>"Poveda_DTR_may6",'Poveda_DTR_may10'=>"Poveda_DTR_may10");
		foreach ($dbname as $db => $value) {
			foreach (Globals::dataRequestApprovalList() as $tblname => $desc) {
				$table = $this->db->query("SELECT * FROM $db.$tblname")->result_array();
				foreach ($table as $tablekey => $tabledata) {
					$tblid = $tabledata['id'];
					$checking = $this->db->query("SELECT * FROM $tblname WHERE id = '$tblid'")->num_rows();
					if($checking == 0){
						$this->db->insert($tblname, $tabledata);
					}
				}
			}
		}
	}

	public function loadTableData(){
		$toks = $this->input->post("toks");
		$data['employeeid'] = ($toks) ? $this->gibberish->decrypt($this->input->post("employeeid"), $toks) : $this->input->post("employeeid");
		$view = ($toks) ? $this->gibberish->decrypt($this->input->post("view"), $toks) : $this->input->post("view");
		$this->load->view('employee/'.$view, $data);
	}

	public function loadTableDatas(){
		$toks = $this->input->post("toks");
		$data['employeeid'] = ($toks) ? $this->gibberish->decrypt($this->input->post("employeeid"), $toks) : $this->input->post("employeeid");
		$view = ($toks) ? $this->gibberish->decrypt($this->input->post("view"), $toks) : $this->input->post("view");
		$this->load->view('employee/'.$view, $data);
	}

}