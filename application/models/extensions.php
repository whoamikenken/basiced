<?php 
/**
 * @author Max Consul
 * @copyright 2018
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Extensions extends CI_Model {

	/**
	* Query for other db data
	*
	* @return query result
	*/

	public function getLastTimesheetId(){
		$query = $this->db->query("SELECT * FROM timesheet ORDER BY timestamp DESC LIMIT 1");
		if($query->num_rows() > 0 ) return $query->row()->timeid;
		else return FALSE;
	}

	public function getLeaveRequestCode(){
		$query = $this->db->query("SELECT * FROM code_request_form")->result_array();
		$description = array();
		$data = array();
		foreach($query as $row){
			$description = explode(" ", $row['description']);
			$data[$row['code_request']] = $description[0];
		}
		return $data;
	}

	public function getCampusId(){
		$query = $this->db->query("SELECT code FROM code_campus");
		$code_campus = array();
		if($query->num_rows > 0){
			foreach($query->result_array() as $value){
				$code_campus[$value['code']] = $value['code'];
			}
			return $code_campus;
		}
		else return false;
	}

	public function getCampusLists(){
		$data = array();
		$query = $this->db->query("SELECT * FROM code_campus");
		if($query->num_rows > 0){
			foreach($query->result_array() as $value){
				$data[$value['code']] = $value['description'];
			}

			return $data;
		}
	}

	public function getBuildingLists(){
		$data = array();
		$query = $this->db->query("SELECT building FROM employee_schedule_history  WHERE building != '' GROUP BY building");
		if($query->num_rows > 0){
			foreach($query->result_array() as $value){
				$data[$value['building']] = $value['building'];
			}
			return $data;
		}
	}

	public function getFloorLists(){
		$data = array();
		$query = $this->db->query("SELECT floor FROM employee_schedule_history WHERE floor != '' GROUP BY floor ");
		if($query->num_rows > 0){
			foreach($query->result_array() as $value){
				$data[$value['floor']] = $value['floor'];
			}

			return $data;
		}
	}

	public function isConsecutiveAbsent($sdate, $edate, $empid){
		$count = 0;
		$old_date = '';
		$date_diff = '';
		$query = $this->db->query("SELECT sched_date FROM `employee_attendance_detailed` WHERE sched_date BETWEEN '$sdate' AND '$edate' AND employeeid = '$empid' AND  absents != '' ")->result_array();
		if(count($query) >= 10) return true;
		else return false;
	}

	public function getEmployeeDeptHead($empid){
		$query = $this->db->query("SELECT head FROM employee a INNER JOIN code_office b ON b.`code` = a.`deptid` WHERE employeeid = '$empid' ");
		if($query->num_rows > 0){
			if($query->row()->head != $empid){
				return $this->getEmployeeName($query->row()->head);
			}else{
				$getDivisionHead = $this->db->query("SELECT divisionhead FROM employee a INNER JOIN code_office b ON b.`code` = a.`deptid` WHERE employeeid = '$empid' ");
				if($getDivisionHead->num_rows > 0){
					return $this->getEmployeeName($getDivisionHead->row()->divisionhead);
				}
			}
		}

	}

	public function getEmployeeName($empid){
		$query = $this->db->query("SELECT CONCAT(lname, ', ', fname , ', ', mname) AS fullname FROM employee WHERE employeeid = '$empid' ");
		if($query->num_rows() > 0) return $query->row()->fullname;
		else return false;
	}

	public function getEmployeeBasicName($empid){
		$query = $this->db->query("SELECT CONCAT(lname, ', ', fname ) AS fullname FROM employee WHERE employeeid = '$empid' ");
		if($query->num_rows > 0) return $query->row()->fullname;
		else return false;
	}

	public function getEmployeePositionId($empid){
		$query = $this->db->query("SELECT positionid FROM employee WHERE employeeid = '$empid' ");
		if($query->num_rows > 0) return $query->row()->positionid;
		else return;
	}

	public function getHRHead(){
		$query = $this->db->query("SELECT CONCAT(lname, ' ,', fname , ' ,', mname) AS fullname FROM employee WHERE positionid = '99' ");
		if($query->num_rows > 0) return $query->row()->fullname;
		else return false;
	}

	public function getPositionDesc($positionid){
		$query = $this->db->query("SELECT * FROM code_position WHERE positionid = '$positionid' ");
		if($query->num_rows > 0) return $query->row()->description;
	}

	public function getEmplistByOfficeHead($office, $teachingtype){
		$query = $this->db->query("SELECT CONCAT(lname, ' ,', fname, ' .', mname) AS fullname, employeeid FROM employee WHERE office = '$office' AND teachingtype = '$teachingtype' ");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function getEmplistByCampusPrincipal($campusid, $teachingtype){
		$query = $this->db->query("SELECT CONCAT(lname, ' ,', fname, ' .', mname) AS fullname, employeeid FROM employee WHERE campusid = '$campusid' AND teachingtype = '$teachingtype' ");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function deleteZeroCutoff($empid, $no_cutoff, $code_income, $be_tag){
		$be_tag = strtolower($be_tag);
		if($no_cutoff == "0"){
			$query = $this->db->query("DELETE FROM employee_$be_tag WHERE employeeid = '$empid' AND code_$be_tag = '$code_income' ");
			if($query) return true;
			else return false;
		}
	}

	public function getZeroCutoff($empid, $no_cutoff, $code_income, $be_tag){
		$be_tag = strtolower($be_tag);
		if($no_cutoff == "0"){
			$query = $this->db->query("SELECT * FROM employee_$be_tag WHERE employeeid = '$empid' AND code_$be_tag = '$code_income' ");
			if($query) return true;
			else return false;
		}
	}

	public function checkIfOfficeHead($empid){
		$query = $this->db->query("SELECT * FROM code_office WHERE head = '$empid' OR divisionhead = '$empid' ");
		if($query->num_rows > 0) return true;
		else return false;
	}

	public function getRemainingCutoff($dfrom, $dto){
		$query = $this->db->query("SELECT * FROM cutoff WHERE CutoffFrom > '$dfrom' AND CutoffTo >  '$dto' ");
		return $query->num_rows();
	}

	public function getRemainingCutoffForPayroll($employeeid, $dfrom, $dto){
		$query = $this->db->query("SELECT * FROM processed_employee WHERE cutoffstart = '$dfrom' AND cutoffend = '$dto' AND employeeid = '$employeeid' LIMIT 1 ");
		return $query->row()->remaining_cutoff;
	}	

	public function getEmpBank($employeeid){
		$query = $this->db->query("SELECT emp_bank FROM employee WHERE employeeid = '$employeeid' ");
		if($query->num_rows > 0) return $query->row()->emp_bank;
		else return false;
	}

	public function getEmpBankAccountNo($employeeid){
		$query = $this->db->query("SELECT emp_accno FROM employee WHERE employeeid = '$employeeid' ");
		if($query->num_rows > 0) return $query->row()->emp_accno;
		else return false;
	}

    public function getBankList(){
     	return $this->db->query("SELECT * FROM code_bank_account")->result_array();
    }

	public function checkIfPayedPhilhealth($eid, $cutoffstart){
		$philhealth = '';
		$date=date_create($cutoffstart);
        date_sub($date,date_interval_create_from_date_string("5 days"));
        $date = date_format($date,"Y-m-d");
        $checkLastCutoff = $this->db->query("SELECT fixeddeduc FROM payroll_computed_table WHERE employeeid = '$eid' AND '$date' BETWEEN cutoffstart AND cutoffend AND status = 'PROCESSED' ");
        if($checkLastCutoff->num_rows > 0){
            $emp_fixeddeduc = explode("/", $checkLastCutoff->row()->fixeddeduc);
            foreach($emp_fixeddeduc as $key => $value){
                $emp_deduc = explode("=", $value);
                if(in_array("PHILHEALTH", $emp_deduc)){
                    $philhealth = true;
                }
            }
        }
        if($philhealth) return '';
        else return "PHILHEALTH";
	}

	function GetYearDiffBasedOnToday($date){
		$today = new DateTime("NOW");
		$dateformat = new DateTime($date);
		$diff = $dateformat->diff($today);
		return $diff->y;
	}
	
	public function getOfficeDescription($code){
		$description = "";
		$query = $this->db->query("SELECT * FROM code_office WHERE code = '$code' ");
		if($query->num_rows() > 0) $description = $query->row()->description;
		else $description = "No Office";
		
		return $description;
	}

	public function getOfficeDesc($code){
		$query = $this->db->query("SELECT * FROM code_office WHERE code = '$code' ");
		if($code){
			if($query->num_rows > 0)return $query->row()->description;
			else return "[DELETED OFFICE]";
		}
		else{
			return "[NO OFFICE]";
		}
	}
	
	public function getOfficeDescriptionReport($code=''){
		if(!$code){
			return "";
		}else{
			$query = $this->db->query("SELECT * FROM code_office WHERE code = '$code' ");
			if($query->num_rows > 0) return $query->row()->description;
			else return '';
		}
	}

	public function getDeparmentDescriptionReport($code=''){
		if(!$code){
			return "No Department";
		}else{
			$query = $this->db->query("SELECT * FROM code_department WHERE code = '$code' ");
			if($query->num_rows() > 0) return $query->row()->description;
			else return "";
		}
	}

	

	public function getPositionDescription($code){
		$query = $this->db->query("SELECT * FROM code_position WHERE positionid = '$code' ");
		if($query->num_rows > 0) return Globals::_e($query->row()->description);
		else return "No Position";
	}

	public function getCutoffdate($date){
		$startdate = $enddate = '';
		$query = $this->db->query("SELECT * FROM payroll_cutoff_config WHERE '$date' BETWEEN DATE_FORMAT(startdate, '%Y-%m') AND DATE_FORMAT(enddate, '%Y-%m') ");
		if($query->num_rows > 0){
			$data = $query->result_array();
			return array($data[0]['startdate'], $data[1]['enddate']);
		}
		else return false;
	}

	public function getEmployeeOtherIncome($employeeid, $deminimiss_list){
		$where_clause = '';
		foreach($deminimiss_list as $key => $value){
			if(!$where_clause) $where_clause .= " AND (other_income = '$key' ";
			if($where_clause) $where_clause .= " OR other_income = '$key' ";
		}
		if($where_clause) $where_clause .= " ) ";
		$query = $this->db->query("SELECT SUM(monthly) as total FROM other_income WHERE employeeid = '$employeeid' $where_clause ");
		return $query->row()->total;

	}

	public function getDisciplinaryActionSetup(){
		$query = $this->db->query("SELECT * FROM code_disciplinary_action_sanction ");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function getDisciplinarySanctions($code){
		$query = $this->db->query("SELECT * FROM code_disciplinary_action_offense_type WHERE code = '$code' ");
		if($query->num_rows >0) return $query->row()->sanctions;
		else return " = 0";
	}

	public function getIncomeSetup(){
		$query = $this->db->query("SELECT * FROM payroll_income_config");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function getIncomeDesc($code){
		$query = $this->db->query("SELECT * FROM payroll_income_config WHERE id = '$code' ");
		if($query->num_rows > 0) return $query->row()->description;
		else return false;
	}

	public function getDeductionSetup(){
		$query = $this->db->query("SELECT * FROM payroll_deduction_config");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function getDeductionDesc($code){
		$query = $this->db->query("SELECT * FROM payroll_deduction_config WHERE id = '$code' ");
		if($query->num_rows > 0) return $query->row()->description;
		else return false;
	}

	public function getLoanSetup(){
		$query = $this->db->query("SELECT * FROM payroll_loan_config");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function getLoanDesc($code){
		$query = $this->db->query("SELECT * FROM payroll_loan_config WHERE id = '$code' ");
		if($query->num_rows > 0) return $query->row()->description;
		else return false;
	}

	public function getFixedDeductionSetup(){
		$array = array(
			"SSS" => "SSS",
			"PHILHEALTH" => "PHILHEALTH",
			"PAGIBIG" => "PAGIBIG FUND",
			"PERAA" => "PERAA",
		);
		return $array;
	}

	public function getFixedDeductionDesc($code){
		$query = $this->db->query("SELECT * FROM deductions WHERE code_deduction = '$code' ");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function getSpecialVoucherData($type){
		$where_clause = " WHERE type = '$type' ";
		if($type == "income") $data['income'] = $this->getIncomeSetup();
		else if($type == "deduction") $data['deduction'] = $this->getDeductionSetup();
		else if($type == "loan") $data['loan'] = $this->getLoanSetup();
		else if($type == "regdeduction") $data['regdeduction'] = $this->getFixedDeductionSetup();
		else if($type != "witholdingtax"){ 
			$data['income'] = $this->getIncomeSetup();
			$data['deduction'] = $this->getDeductionSetup();
			$data['loan'] = $this->getLoanSetup();
			$data['regdeduction'] = $this->getFixedDeductionSetup();
			$where_clause = "";
		}
		$query = $this->db->query("SELECT * FROM special_voucher $where_clause");
		if($query->num_rows > 0){
			$data['records'] = $query->result_array();
			return $data;
		}
		else return false;
	}

	public function insertSpecialVoucher($data){
		$query = $this->db->insert("special_voucher", $data);
		if($query) return true;
		else return false;
	}

	public function editSpecialVoucherData($employeeid = "", $category = "", $account = ""){
		if($category != "witholdingtax"){
			$query = $this->db->query("SELECT * FROM special_voucher a INNER JOIN employee b ON b.employeeid = a.employeeid WHERE a.employeeid = '$employeeid' AND a.type = '$category' AND a.account = '$account' ");
		}else{
			$query = $this->db->query("SELECT * FROM special_voucher a INNER JOIN employee b ON b.employeeid = a.employeeid WHERE a.employeeid = '$employeeid' AND a.type = '$category' ");
		}
		if($query->num_rows > 0) return $query->result_array();
		return false;
	}

	public function updateVoucherData($data){
		$this->db->where('employeeid', $data['employeeid']);
		$this->db->where('type', $data['type']);
		$this->db->where('account', $data['account']);
		$this->db->set($data);
		$query = $this->db->update('special_voucher');
		if($query) return true;
		else return false;
	}

	public function deleteVoucherData($data){
		if($data['type'] != "witholdingtax"){
			$this->db->where('employeeid', $data['employeeid']);
			$this->db->where('type', $data['type']);
			$this->db->where('account', $data['account']);
			$query = $this->db->delete('special_voucher');
		}else{
			$this->db->where('employeeid', $data['employeeid']);
			$this->db->where('type', $data['type']);
			$query = $this->db->delete('special_voucher');
		}
		if($query) return true;
		else return false;
	}

	public function getActiveEmployees($empstat=''){
		$datenow = date("Y-m-d");
		$where_clause = '';
		if($empstat) $where_clause = "AND employmentstat NOT IN ('$empstat')";
		$query = $this->db->query("SELECT * FROM employee WHERE (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1') $where_clause");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function getTaxableIncome(){
		$query = $this->db->query("SELECT * FROM payroll_income_config WHERE taxable = 'withtax' ");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function getNonTaxableIncome(){
		$query = $this->db->query("SELECT * FROM payroll_income_config WHERE taxable = 'notax' ");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function getAllCutoffPerYear($year){
		$query =$this->db->query("SELECT * FROM payroll_cutoff_config WHERE DATE_FORMAT(startdate, '%Y') = '$year' AND  DATE_FORMAT(enddate, '%Y') = '$year' ORDER BY startdate ASC");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	public function getPayrollComputedData($startdate, $enddate){
		$query =$this->db->query("SELECT CONCAT(lname, ', ', fname, ', ', mname) AS fullname ,a.* FROM payroll_computed_table a INNER JOIN employee b ON b.`employeeid` = a.`employeeid` WHERE STATUS ='PROCESSED' AND cutoffstart = '$startdate' AND cutoffend = '$enddate' AND a.employeeid = '1987-06-001' ");
		if($query->num_rows > 0) return $query->result_array();
		else return array();
	}

	public function checkIfSystemIsRecomputing($tnt){
		$query = $this->db->query("SELECT * FROM recomputing_percentage WHERE teachingtype = '$tnt' ");
		if($query->num_rows > 0){
			$emp_count = $query->row()->emp_count;
			$emp_total = $query->row()->emp_total;
			$success = $query->row()->success;
			$failed = $query->row()->failed;
			if(!$emp_count && !$emp_total && !$success && !$failed) return true;
			else return false;
		}
	}

	public function getSpecialVoucherDataForAlphalist(){
		$query_special_voucher = $this->db->query("SELECT * FROM special_voucher ");
		if($query_special_voucher->num_rows > 0) return $query_special_voucher->result_array();
		else return array();
	}

	public function getEmployeeSalary($startdate = '', $enddate = '', $employeeid = ''){
		$query_empsalary = $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid = '$employeeid' AND cutoffstart = '$startdate' AND cutoffend = '$enddate' ");
		if($query_empsalary->num_rows > 0) return $query_empsalary->row()->salary;
		else return false;
	}

	public function getEmployeeLatestSalary($empid){
		$query_salary = $this->db->query("SELECT * FROM payroll_employee_salary_history WHERE employeeid = '$empid' ORDER BY date_effective DESC LIMIT 1 ");
		if($query_salary->num_rows > 0) return array($query_salary->row()->monthly,$query_salary->row()->daily,$query_salary->row()->hourly,$query_salary->row()->date_effective);
		else{
			$query_salary = $this->db->query("SELECT * FROM payroll_employee_salary WHERE employeeid = '$empid' ORDER BY date_effective DESC LIMIT 1 ");
			if($query_salary->num_rows > 0) return array($query_salary->row()->monthly,$query_salary->row()->daily,$query_salary->row()->hourly,$query_salary->row()->date_effective);
		}
	}

	public function getDepartmentDescription($deptid){
		$query_dept = $this->db->query("SELECT * FROM code_office WHERE code = '$deptid' ");
		if($query_dept->num_rows > 0) return $query_dept->row()->description;
		else return false;
	}

	public function getServerTime(){
		$query_time = $this->db->query("SELECT CURRENT_TIMESTAMP ")->row()->CURRENT_TIMESTAMP;
		return $query_time;
	}

	public function getNotIncludedInGrosspayIncome(){
		$data = array();
		$query_income = $this->db->query("SELECT * FROM payroll_income_config WHERE grosspayNotIncluded = '0' ");
		if($query_income->num_rows > 0){
			foreach ($query_income->result_array() as $key => $value) {
				$data[$value['id']] = $value['id'];
			}
		}
		return $data;
	}

	public function getNotIncludedInGrosspayIncomePhil(){
		$data = array();
		$query_income = $this->db->query("SELECT * FROM payroll_income_config WHERE grosspayNotIncludedPhil = '0' ");
		if($query_income->num_rows > 0){
			foreach ($query_income->result_array() as $key => $value) {
				$data[$value['id']] = $value['id'];
			}
		}
		return $data;
	}

	public function getCampusDescription($campusid){
		$query = $this->db->query("SELECT * FROM code_campus WHERE code = '$campusid' ");
		if($query->num_rows > 0) return $query->row()->description;
		else return "No Campus";
	}

	public function getAttendanceAdjustmentRecords($fv, $datesetfrom, $datesetto, $office){
		$data = array();
		$where_clause = '';
		$cutoff_id = $this->getDTRCutoffId($datesetfrom, $datesetto);
		if($fv) $where_clause .= " AND employeeid = '$fv' ";
		if($office) $where_clause .= " AND b.office = '$office' ";
		$query_ob = $this->db->query("SELECT a.*, CONCAT(lname, ', ', fname, ', ', mname) AS fullname FROM ob_adjustment a INNER JOIN employee b ON a.`employeeid` = b.`employeeid` INNER JOIN code_office c ON b.office = c.code WHERE payroll_cutoff_id = '$cutoff_id' $where_clause ");
		if($query_ob->num_rows > 0) $data['ob_adjustment'] = $query_ob->result_array();

		$query_leave = $this->db->query("SELECT a.*, CONCAT(lname, ', ', fname, ', ', mname) AS fullname FROM leave_adjustment a INNER JOIN employee b ON a.`employeeid` = b.`employeeid` WHERE payroll_cutoff_id = '$cutoff_id' $where_clause ");
		if($query_leave->num_rows > 0) $data['leave_adjustment'] = $query_leave->result_array();

		$query_correction = $this->db->query("SELECT a.*, CONCAT(lname, ', ', fname, ', ', mname) AS fullname FROM correction_adjustment a INNER JOIN employee b ON a.`employeeid` = b.`employeeid` WHERE payroll_cutoff_id = '$cutoff_id' $where_clause ");
		if($query_correction->num_rows > 0) $data['correction_adjustment'] = $query_correction->result_array();

		return $data;
	}

	public function getPayrollCutoffConfig($dfrom, $dto){
		$cutoff = explode("-", $dtr_cutoff);
		$query_date = $this->db->query("SELECT * FROM cutoff a INNER JOIN payroll_cutoff_config b ON a.`id` = b.`baseid` WHERE CutoffFrom = '$dfrom' AND CutoffTo = '$dto' ");
		if($query_date->num_rows > 0) return date("F d, Y", strtotime($query_date->row()->startdate))." - ".date("F d, Y", strtotime($query_date->row()->enddate));
		else return date('Y-m-d');
	}

	public function getDeminimissIncomeKeys(){
		$data = array();
		$query_income = $this->db->query("SELECT * FROM payroll_income_config WHERE incomeType = 'deminimiss' ");
		if($query_income->num_rows > 0){
			foreach ($query_income->result_array() as $key => $value) {
				$data[$value['id']] = $value['id'];
			}
		}
		return $data;
	}

	public function getNonDeminimissIncomeKeys(){
		$data = array();
		$query_income = $this->db->query("SELECT * FROM payroll_income_config WHERE incomeType != 'deminimiss' ");
		if($query_income->num_rows > 0){
			foreach ($query_income->result_array() as $key => $value) {
				$data[$value['id']] = $value['id'];
			}
		}
		return $data;
	}

	public function getAllIncomeKeysAndDescription(){
		$data = array();
		$query_income = $this->db->query("SELECT * FROM payroll_income_config");
		if($query_income->num_rows > 0){
			foreach ($query_income->result_array() as $key => $value) {
				$data[$value['id']] = $value['description'];
			}
		}
		return $data;
	}

	public function getDeductioConfignKeys(){
		$data = array();
		$query_deduction = $this->db->query("SELECT * FROM payroll_deduction_config");
		if($query_deduction->num_rows > 0){
			foreach ($query_deduction->result_array() as $key => $value) {
				$data[$value['id']] = $value['id'];
			}
		}
		return $data;
	}

	public function getAllDeductionKeysAndDescription(){
		$data = array();
		$query_income = $this->db->query("SELECT * FROM payroll_deduction_config");
		if($query_income->num_rows > 0){
			foreach ($query_income->result_array() as $key => $value) {
				$data[$value['id']] = $value['description'];
			}
		}
		return $data;
	}

	public function getAllFixedDeductionKeysAndDescription(){
		$data = array();
		$query_income = $this->db->query("SELECT * FROM deductions");
		if($query_income->num_rows > 0){
			foreach ($query_income->result_array() as $key => $value) {
				$data[$value['code_deduction']] = $value['description'];
			}
		}
		return $data;
	}

	public function getAllLoanKeysAndDescription(){
		$data = array();
		$query_income = $this->db->query("SELECT * FROM payroll_loan_config");
		if($query_income->num_rows > 0){
			foreach ($query_income->result_array() as $key => $value) {
				$data[$value['id']] = $value['description'];
			}
		}
		return $data;
	}

	public function getTotalLeaveAndHoliday($employeeid, $sdate, $edate){
		$query_att = $this->db->query("SELECT SUM(eleave + vleave + sleave + oleave) AS total FROM attendance_confirmed WHERE employeeid = '$employeeid' AND payroll_cutoffstart = '$sdate' AND payroll_cutoffend = '$edate' ");

		if($query_att->num_rows > 0) return $query_att->row()->total;
		else return false;
	}

	public function getDTRCutoffId($datefrom,$dateto){
		$q_dtrcutoff = $this->db->query("SELECT * FROM cutoff WHERE CutoffFrom = '$datefrom' AND CutoffTo = '$dateto' ");
		if($q_dtrcutoff->num_rows > 0){
			$dtr_id	= $q_dtrcutoff->row()->ID;
			$q_payrollcutoff = $this->db->query("SELECT * FROM payroll_cutoff_config WHERE baseid = '$dtr_id' ");
			if($q_payrollcutoff->num_rows > 0) return $q_payrollcutoff->row()->id;
		}
		else return false;
	}

	public function getEmployeeTeachingType($employeeid){
		$q_teachintype = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid'");
		// if($q_teachintype->num_rows() > 0) return ($q_teachintype->row()->teachingtype == 'teaching' ? 'Acad' : 'Non-Acad' );
		if($q_teachintype->num_rows() > 0) return $q_teachintype->row()->teachingtype;
		else return false;
	}

	public function getEmployeeList($where=""){
		$q_employeelist = $this->db->query("SELECT CONCAT(lname, ' ,', fname, ' .', mname) AS fullname, employeeid, lname, fname, mname, deptid, office, bdate, gender, campusid, mobile, cp_name, cp_mobile, teachingtype, emp_sss, emp_tin, emp_philhealth, emp_peraa, emp_pagibig, addr, mobile, landline, email, emptype, cp_address, cp_relation, positionid FROM employee WHERE employeeid != '' $where ");
		if($q_employeelist->num_rows() > 0) return $q_employeelist->result_array();
		else return false;
	}

	public function getStudentList(){
		$q_employeelist = $this->db->query("SELECT * FROM student LIMIT 100 ");
		if($q_employeelist->num_rows > 0) return $q_employeelist->result_array();
		else return false;
	}

	public function verifyAccessToken($token){
		return $this->db->query("SELECT * FROM token_allowed WHERE access_token = '$token' ")->num_rows();
	}

	public function getPostmanToken(){
		$api_url = Globals::apiUrl()."/api/authenticate/request";
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_PORT => "6271",
		  CURLOPT_URL => $api_url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "\r\n{\r\n\"username\": \"povedaapi\",\r\n\"password\": \"PovedaApi2019!\"\r\n}\r\n",
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/json",
		    "Postman-Token: 7e7ac43d-92df-488f-8d36-b15d4b2e4193",
		    "cache-control: no-cache"
		  ),
		));
		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		
		return $response; 
	}

	public function updateEmployeeCardnumber($employeeid, $rfid){
		if($this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' AND employeecode != '$rfid' ")->num_rows()){
			$this->db->query("UPDATE employee SET employeecode = '$rfid' WHERE employeeid = '$employeeid' ");
			return true;
		}else{
			return false;
		}
	}

	public function checkIfDeptIsBED($code){
		$q_bed = $this->db->query("SELECT * FROM code_office WHERE code = '$code' ");
		if($q_bed->num_rows() > 0) return $q_bed->row()->isBED;
		else return false;
	}

	public function checkIfCutoffNoDTR($cutoffstart, $cutoffto){
        $cutoffid = $this->db->query("SELECT ID FROM cutoff WHERE CutoffFrom = '$cutoffstart' AND CutoffTo = '$cutoffto' ")->row()->ID;
        return $this->db->query("SELECT nodtr FROM payroll_cutoff_config WHERE baseid = '$cutoffid' ")->row()->nodtr;
	}

	public function checkIfCollegeTeaching($employeeid){
		$collegeDepartment = $this->loadCollegeDepartment();
		$collegeDepartment = "'".implode("','", $collegeDepartment). "'";
		$q_employee = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' AND deptid IN ($collegeDepartment)");
		if($q_employee->num_rows > 0) return true;
		else return false;
	}

	public function loadCollegeDepartment(){
		$data = array();
		$q_dept = $this->db->query("SELECT * FROM code_department WHERE iscollege = '1' ");
		if($q_dept->num_rows > 0){
			foreach($q_dept->result_array() as $row){
				$data[$row['code']] = $row['code'];
			}
		}

		return $data;
	}

    public function empTeachingType($eid)
    {
        $return = "";
        $query = $this->db->query("SELECT teachingtype FROM employee WHERE employeeid='$eid'");
        if ($query->num_rows() > 0) {
            $return = $query->row()->teachingtype;
        }
        return $return;
    }	

    public function checkIfSecondApprover($idkey, $table){
    	$tbl = "";
    	if($table == "leave") $tbl = "leave_app_emplist";
    	elseif($table == "overtime") $tbl = "ot_app_emplist";
    	elseif($table == "ob") $tbl = "ob_app_emplist";
    	elseif($table == "changesched") $tbl = "change_sched_app_emplist";
    	elseif($table == "servicecredit") $tbl = "sc_app_emplist";
    	elseif($table == "useservicecredit") $tbl = "sc_app_use_emplist";
    	elseif($table == "seminar") $tbl = "seminar_app_emplist";
		$issecond = false;
		$q_leave = $this->db->query("SELECT * FROM $tbl WHERE id = '$idkey' ");
		if($q_leave->num_rows() > 0){
			foreach($q_leave->result_array() as $row){
				foreach($row as $value){
					if($value == "APPROVED") $issecond = true;
				}	
			}
		}

		return $issecond;
	}

	public function statusLabel($idkey, $table, $colhead){
		$stat_seq = 0;
		$colseq = substr($colhead, 0, -4)."seq";
    	$tbl = "";
    	if($table == "leave") $tbl = "leave_app_base";
    	elseif($table == "overtime") $tbl = "ot_app";
    	elseif($table == "ob") $tbl = "ob_app";
    	elseif($table == "changesched") $tbl = "change_sched_app";
    	elseif($table == "servicecredit") $tbl = "sc_app";
    	elseif($table == "useservicecredit") $tbl = "sc_app_use";
    	elseif($table == "seminar") $tbl = "seminar_app";
		$q_leave = $this->db->query("SELECT $colseq FROM $tbl WHERE id = '$idkey' ");
		if($q_leave->num_rows() > 0){
			foreach($q_leave->result_array() as $row){
				$stat_seq = $row[$colseq];
			}
		}
		if($stat_seq == 1) return "ENDORSED";
		else if($stat_seq == 2) return "APPROVED";
		else if($stat_seq > 2) return "NOTED";
	}

	public function getBEDDepartments(){
        $data = array();
        $records = $this->db->query("SELECT * FROM code_office WHERE isBED != '1' ")->result_array();
        foreach($records as $row) $data[] = $row["code"];

        return $data;
    }

    public function getEmployeeEmail($employeeid){
		$q_email = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' OR email = '$employeeid' ");
		if($q_email->num_rows > 0){ 
			if($q_email->row()->email) return $q_email->row()->email;
			else return $q_email->row()->personal_email;
		}
		else{
			return false;
		}
	}

	public function generateRandomPassword($length = 10){
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

    public function getEmployeeGender($employeeid){
    	$gender_arr = array(""=>"Not set yet","M"=>"MALE", "F"=>"FEMALE");
    	$gender = $this->db->query("SELECT gender FROM employee WHERE employeeid = '$employeeid' ")->row()->gender;
    	return $gender_arr[$gender];
    }

    public function checkIfDeptHead($userid){
    	return $this->db->query("SELECT * FROM code_department WHERE head = '$userid' OR divisionhead = '$userid' ")->num_rows();
    }

    public function checkIfCampusPrincipal($userid){
    	return $this->db->query("SELECT * FROM code_campus WHERE campus_principal = '$userid' ")->num_rows();
    }

    public function getAllDepartmentUnder($userid){
    	$data = array();
    	$q_dept = $this->db->query("SELECT * FROM code_department WHERE head = '$userid' OR divisionhead = '$userid' ");
    	if($q_dept->num_rows() > 0){
    		foreach($q_dept->result_array() as $row){
    			$data[] = $row["code"];
    		}
    	}
    	return $data;
    }

    public function getAllOfficeUnder($userid){
    	$data = array();
    	$q_office = $this->db->query("SELECT * FROM code_office WHERE head = '$userid' OR divisionhead = '$userid' ");
    	if($q_office->num_rows() > 0){
    		foreach($q_office->result_array() as $row){
    			$data[] = $row["code"];
    		}
    	}
    	return $data;
    }

    public function getAllCampusUnder($userid){
    	$data = array();
    	$q_campus = $this->db->query("SELECT * FROM code_campus WHERE campus_principal = '$userid' ");
    	if($q_campus->num_rows() > 0){
    		foreach($q_campus->result_array() as $row){
    			$data[] = $row["code"];
    		}
    	}
    	return $data;
    }

    public function getEmplistForDepartmentAttendance($where_clause, $teachingtype){
    	return $this->db->query("SELECT CONCAT(lname, ' ,', fname , ' ,', mname) AS fullname, employeeid FROM employee WHERE 1  $where_clause ")->result_array();
    }

    public function getEmployeeFname($employeeid){
    	return $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' ")->row()->fname;
    }

    public function getEmployeeMname($employeeid){
    	return $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' ")->row()->mname;
    }

    public function getEmployeeLname($employeeid){
    	return $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' ")->row()->lname;
    }

    public function getBirthdayCelebrantsToday(){
    	$datenow = date("m-d", strtotime($this->db->query("SELECT CURRENT_TIMESTAMP ")->row()->CURRENT_TIMESTAMP));
      	$q_bday = $this->db->query("SELECT * FROM employee WHERE DATE_FORMAT(bdate, '%m-%d') = '$datenow' AND ('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive = '1' LIMIT 3 ");
      	if($q_bday->num_rows() > 0) return $q_bday->result_array();
      	else return false;
    }

	public function sendEmailToNextApprover($approver_id){
		if(Globals::is_connect_internet() && $_SERVER["HTTP_HOST"] != "192.168.2.97"){
			$email = $this->extensions->getEmployeeEmail($approver_id);
			$fullname = $this->extensions->getEmployeeName($approver_id);
			if($email && $fullname){
				$data["approver_name"] = $fullname;
				$this->load->model("email");
				$this->email->sendEmailForOnlineApplication($email, $data);
			}
		}
	}

    public function getAppSequenceForEmail($type=""){
    	$res = $this->db->query("SELECT dhseq,  chseq,  hhseq,  cpseq,  dpseq,  fdseq,  boseq,  pseq,  upseq FROM code_request_form WHERE code_request='$type'")->result_array();
    	return $res;
    }

    public function getCurrentCutoff($date_now){
    	$q_cutoff = $this->db->query("SELECT * FROM cutoff WHERE '$date_now' BETWEEN ConfirmFrom AND ConfirmTo ");
    	if($q_cutoff->num_rows() > 0){
    		if($q_cutoff->row()->ConfirmFrom && $q_cutoff->row()->ConfirmTo) return array($q_cutoff->row()->CutoffFrom, $q_cutoff->row()->CutoffTo);
    		else return false;
    	}else{
    		return false;
    	}
    }

    public function getCurrentPayrollCutoff($date_now){
    	$q_cutoff = $this->db->query("SELECT * FROM payroll_cutoff_config WHERE '$date_now' BETWEEN startdate AND enddate ");
    	if($q_cutoff->num_rows() > 0){
    		if($q_cutoff->row()->startdate && $q_cutoff->row()->enddate) return array($q_cutoff->row()->startdate, $q_cutoff->row()->enddate);
    		else return false;
    	}else{
    		return false;
    	}
    }

    public function getApplicantPosition($applicantId){
    	$positionid = $positiondesc = "";
    	$q_position = $this->db->query("SELECT * FROM applicant WHERE applicantId = '$applicantId' ");
    	if($q_position->num_rows() > 0) $positionid = $q_position->row()->positionApplied;
    
    	$q_posdesc = $this->db->query("SELECT * FROM code_position WHERE positionid = '$positionid' ");
    	if($q_posdesc->num_rows() > 0) $positiondesc = $q_posdesc->row()->description;

    	return $positiondesc;
    }    

    public function getSubjectDescription($id){
    	$q_sebdesc = $this->db->query("SELECT * FROM code_subj_competent_to_teach WHERE id = '$id' ");
    	if($q_sebdesc->num_rows() > 0) return $q_sebdesc->row()->description;
    	return "--";
    }

    public function getCourseDescription($id){
    	$q_coursedesc = $this->db->query("SELECT * FROM tblCourses WHERE CourseCode = '$id' ");
    	if($q_coursedesc->num_rows() > 0) return $q_coursedesc->row()->Description;
    	else return "--";
    }

    public function getApplicantStatusDesc($id){
    	$q_statusdesc = $this->db->query("SELECT * FROM code_applicant_status WHERE id = '$id' ");
    	if($q_statusdesc->num_rows() > 0) return $q_statusdesc->row()->description;
    	else return false;
    }

	public function getMonthDifference($date1, $date2){
		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
		return $diff;
	}

	public function getEmployeeDeptid($employeeid){
		$q_dept = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' ");
		if($q_dept->num_rows() > 0) return $q_dept->row()->deptid;
		else return false;
	}

	public function employeeidFromEmail($email){
		$q_user = $this->db->query("SELECT * FROM employee WHERE email = '$email' ");
		if($q_user->num_rows() > 0) return $q_user->row()->employeeid;
		else return false;
	}

	public function getEmployeeDeparment($employeeid){
    	$q_dept = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' ");
    	if($q_dept->num_rows() > 0) return $q_dept->row()->deptid;
    	else return false;
    }

    public function getEmployeeCampus($employeeid){
    	$q_campus = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' ");
    	if($q_campus->num_rows() > 0) return $q_campus->row()->campusid;
    	else return false;
    }

    public function getUsageLoginData(){
    	$q_dept = $this->db->query("SELECT COUNT(DISTINCT username) AS LOG, DATE_FORMAT(DATE(`timestamp`), '%M') AS DATE FROM login_attempts_hris WHERE STATUS = 'success' GROUP BY MONTH(DATE(`timestamp`)) LIMIT 12")->result_array();
		return $q_dept;
    }

     public function getEmployeeOfficeDesc($employeeid){
    	$q_office = $this->db->query("SELECT * FROM employee a INNER JOIN code_office b ON a.office = b.code WHERE employeeid = '$employeeid' ");
    	if($q_office->num_rows() > 0) return $q_office->row()->description;
    	else return "No Office";
    }

    public function getTimeInAccuracy($empid, $timein){
        $return = array("","");
        $islate = false;
        $last_id = "";
        $sched = $this->attcompute->displaySched($empid,date("Y-m-d"));
        foreach($sched->result() as $rsched){
        	if($empid != $last_id){
	            $stime = $rsched->tardy_start;
	            if(strtotime($stime) < strtotime($timein)) $islate = true;
	            else $islate = false;
	        }

	        $last_id = $empid;
        }
        return $islate;
    }

    public function getDocumentSetup(){
		$q_document = $this->db->query("SELECT * FROM code_documents");
		return $q_document->result_array();
	}

	public function getDocumentDescription($code){
		$q_doc = $this->db->query("SELECT * FROM code_documents WHERE code = '$code' ");
		if($q_doc->num_rows() > 0) return $q_doc->row()->description;
		else return false;
	}

	public function getDPA($userid){
		return $this->db->query("SELECT * FROM employee where employeeid = '$userid'")->row()->dpa;
	}

	public function acceptDPA($userid){
		return $this->db->query("UPDATE employee set dpa = '1' where employeeid = '$userid'");
	}

	public function loadTeachingEmployee(){
		return $this->db->query("SELECT CONCAT(lname, ' ,', fname, ' .', mname) AS fullname, employeeid FROM employee WHERE teachingtype = 'teaching'")->result_array();
	}

	public function employeeDateEmployed($employeeid){
		$q_emp = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid'");
		if($q_emp->num_rows() > 0) return $q_emp->row()->dateemployed;
		else return false;
	}

	public function reportCodeDescription($id){
		$q_desc = $this->db->query("SELECT * FROM reports_item WHERE ID = '$id'");
		// echo "<pre>"; print_r($this->db->last_query()); die;
		if($q_desc->num_rows() > 0) return $q_desc->row()->level;
		else return false;
	}

	public function monthSelection(){
		return array(
			"01" => "January",
			"02" => "February",
			"03" => "March",
			"04" => "April",
			"05" => "May",
			"06" => "June",
			"07" => "July",
			"08" => "August",
			"09" => "September",
			"10" => "October",
			"11" => "November",
			"12" => "December"
		);
	}

	public function getMonthDescription($code){
		$array = array(
			"January"  => "01",
			"February" => "02",
			"March"	   => "03",
			"April"    => "04",
			"May"      => "05",
			"June"     => "06",
			"July"     => "07",
			"August"   => "08",
			"September"=> "09",
			"October"  => "10",
			"November" => "11",
			"December" => "12"
		);
		$key = array_search($code, $array);
		return $key;
	}

	public function getHolidayHalfdayTime($date, $isFirstSched = ""){
		$where_clause = "";
		if($isFirstSched) $where_clause = " AND sched_count = '$isFirstSched'" ;
		$q_holiday = $this->db->query("SELECT * FROM code_holiday_calendar WHERE '$date' BETWEEN date_from AND date_to $where_clause ");
		if($q_holiday->num_rows > 0) return array($q_holiday->row()->fromtime,$q_holiday->row()->totime);
		else return false;
	}

	public function forgotPassStatusKey($userid, $key, $action){
		if ($action == "insert") {
			return $this->db->query("INSERT INTO forgot_password_history (`userid`,`key`) VALUES ('$userid', '$key') ");
		}else{
			return $this->db->query("UPDATE forgot_password_history SET `status` = 'READ' WHERE `key` = '$key'");
		}
	}

	public function checkUserForgotKey($key){
		$q_email = $this->db->query("SELECT * FROM forgot_password_history WHERE `key` = '$key'");
		if($q_email->row()->status == "SENT"){
			return $q_email->row()->userid;
		} 
		else{
			return false;
		} 
	}

	public function checkUserForgotPass($name){
		$q_email = $this->db->query("SELECT b.id, a.email,b.email AS emailAdmin, a.personal_email, b.username, b.type FROM user_info b LEFT JOIN employee a ON b.username = a.employeeid WHERE b.email = '$name' OR a.email = '$name' OR a.personal_email = '$name' OR b.username = '$name' LIMIT 1");
		$data = array();
		if($q_email->num_rows > 0){
			$data["userid"] = $q_email->row()->id;
			if ($q_email->row()->type == "ADMIN") {
				$data["email"] = $q_email->row()->emailAdmin;
				return $data;
			}else{
				$data["email"] = $q_email->row()->email;
				return $data;
			}	
		} 
		else{
			return false;
		} 
	}

    function getEmployeeListDropdown($type='',$deptid='',$office='',$status=''){
    	$where_clause = "";
    	if($type) $where_clause .= " AND teachingtype = '$type' ";
    	if($status != "") $where_clause .= " AND isactive = '$status' ";
    	if($deptid) $where_clause .= " AND deptid = '$deptid' ";
    	if($office) $where_clause .= " AND office = '$office' ";
    	return $this->db->query("SELECT employeeid, CONCAT(`lname`, ' ', `fname` , ' ', `mname`) AS fullname FROM employee WHERE 1 $where_clause")->result_array();
    }

    function getEmployeeListToDropdown(){
    	return $this->db->query("SELECT employeeid, CONCAT(`lname`, ' ', `fname` , ' ', `mname`) AS fullname FROM employee")->result_array();
    }

    public function getCategoryDescription($id){
		$q_email = $this->db->query("SELECT name FROM survey_category WHERE `id` = '$id'");
		if($q_email->num_rows() > 0){
			return $q_email->row()->name;
		} 
		else{
			return "Category Deleted";
		} 
	}

	function getSeminarTitle($id=''){
		return $this->db->query("SELECT * FROM reports_item WHERE ID = '$id'")->row()->level;
	}

	public function getRankTypeDescription($id){
		$q_rank = $this->db->query("SELECT * FROM rank_code_type WHERE id = '$id'");
		if($q_rank->num_rows() > 0) return $q_rank->row()->description;
		else return false;
	}

	public function getEmployeeRank($employeeid){
		$q_rank = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid'");
		if($q_rank->num_rows() > 0) return $q_rank->row()->rank;
		else return false;
	}

	public function getEmployee201Files($table, $base_id, $employeeid=''){
		$filename = $content = $mime = $dbname = $wc = '';
		$dbname = $this->db->database_files;
        // if($_SERVER["HTTP_HOST"] == "192.168.2.97") $dbname = "PovedahrisFiles";
        // else if($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" && strpos($_SERVER["REQUEST_URI"], 'hristrng') !== false) $dbname = "PovedahrisFiles_Trng";
        // else if($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" && strpos($_SERVER["REQUEST_URI"], 'hris') !== false) $dbname = "PovedahrisFiles"; 
        // if($employeeid) $wc = " AND employeeid = '$employeeid'";
		$query = $this->db->query("SELECT * FROM $dbname.employee201_files WHERE table_name = '$table' AND base_id = '$base_id' AND base_id != '' $wc");
		if($query->num_rows() > 0){
			if($employeeid && $query->num_rows() > 1){
				foreach ($query->result() as $key => $value) {
					if($employeeid == $value->employeeid){
						$filename = $value->filename;
						$content = $value->content;
						$mime = $value->mime;
					}
				}
			}else{
				$filename = $query->row()->filename;
				$content = $query->row()->content;
				$mime = $query->row()->mime;
			}
				
		}
		return array($filename, $content, $mime);
	}

	function getEmployeeTin($empid = ""){
		$query = $this->db->query("SELECT emp_tin FROM employee WHERE employeeid = '$empid'");
		if($query->num_rows() > 0) return $query->row()->emp_tin;
		else return false;
	}

	public function getEemployeeCurrentData($employeeid, $column){
    	$query = $this->db->query("SELECT $column FROM employee WHERE employeeid = '$employeeid' ");
    	if($query->num_rows() > 0) return $query->row()->$column;
    	else return false;
    }

    public function getLatestDateActive($employeeid){
    	$query = $this->db->query("SELECT dateedit FROM employee_schedule WHERE employeeid = '$employeeid' LIMIT 1");
    	if($query->num_rows() > 0) return $query->row()->dateedit;
    	else return false;
    }
	
    public function getEmpstatusdesc($id){
    	return $this->db->query("SELECT description FROM code_status WHERE code = '$id' LIMIT 1")->row()->description;
    }

    public function getDatePosition($employeeid){
    	$dateposition = '';
    	$query = $this->db->query("SELECT dateposition FROM employee_employment_status_history WHERE employeeid = '$employeeid' AND dateposition <> '0000-00-00' ORDER BY dateposition DESC");
    	if($query->num_rows() > 0) $dateposition = $query->row()->dateposition;
    	else $dateposition = $this->extensions->getEemployeeCurrentData($employeeid, "dateposition");
    	$dateposition = (!empty($dateposition) && $dateposition != "0000-00-00" && $dateposition != "1970-01-01") ? date("Y-m-d",strtotime($dateposition)) : "";
    	return $dateposition;
    }

    public function getApproverList($codes){
    	if(is_array($codes)) $codes = implode(',', $codes);
    	$query = $this->db->query("SELECT divisionhead, head, code FROM code_office WHERE FIND_IN_SET (code, '$codes')");
    	if($query->num_rows() > 0){
    		return $query->result_array();
    	}else{
    		return false;
    	}
    }

    public function getTerminals(){
    	$this->load->model('machine');
    	$this->load->model('facial');
    	$terminal = $this->machine->get_terminal();
    	$facial = $this->facial->facialMasterSetup();
    	$options = "<option value='' gate='all'>All Terminal</option>";
    	foreach ($terminal as $row) {
    		$options .= "<option value='".$row->id."' gate='terminal'>".$row->terminal_name."</option>";
    	}

    	foreach ($facial as $row) {
    		$options .= "<option value='".$row->id."' gate='facial'>".$row->deviceName."</option>";
    	}

    	echo $options;
    }

} //endoffile