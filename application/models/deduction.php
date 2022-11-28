<?php 
/**
 * @author Angelica Arangco
 * @copyright 2018
 *
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deduction extends CI_Model {

	# for ica-hyperion 21503
	# by justin (with e)
	///<@Angelica - copied for ICA-Hyperion21533
	function getEmployeeDeduction($employeeid, $code_deduction, $schedule){
		$wc = '';
		if($schedule) $wc .= "AND a.cutoff_period = '$schedule'";
	  $q_emp_deduc = $this->db->query("SELECT * FROM employee_deduction a WHERE a.employeeid='$employeeid' AND a.code_deduction='$code_deduction' AND a.visibility='SHOW' $wc;")->result();
	  return $q_emp_deduc;
	}

	function findEmpDeductionHistory($employeeid, $code_deduction){
	  $q_emp_deduc = $this->db->query("SELECT * FROM employee_deduction_history WHERE employeeid='$employeeid' AND code_deduction='$code_deduction' AND visibility='SHOW' ORDER BY datecreated DESC LIMIT 1;");

	  return $q_emp_deduc;
	}

	function saveBEDeduction($isUpdate, $updateClause, $tbl_fields, $valueClauses, $empid, $code_deduction, $sched){
	  $query = "";
	  if($isUpdate) $query = "UPDATE employee_deduction SET $updateClause WHERE employeeid='$empid' AND code_deduction='$code_deduction';";
	  else          $query = "INSERT INTO employee_deduction (employeeid, code_deduction, $tbl_fields , `schedule`) VALUES ('$empid','$code_deduction', $valueClauses , '$sched');";

	  $q_saveDeduc = $this->db->query($query);

	  return $q_saveDeduc;
	}

	function saveBEDeductionHistory($tbl_fields, $valueClauses, $empid, $code_deduction){
	  $userid = $this->session->userdata('username');

	  $query = "INSERT INTO employee_deduction_history (employeeid, code_deduction, $tbl_fields , `schedule`, status, userid) VALUES ('$empid','$code_deduction', $valueClauses , 'semimonthly', 'SAVED', '$userid');";
	  
	  $q_saveDeducHistory = $this->db->query($query);

	  return $q_saveDeducHistory;
	}
	# end for ica-hyperion 21503

	function updateData($empData,$codeDeduction){
		$this->db->where('code_deduction', $codeDeduction);
		$this->db->where('employeeid', $empData['employeeid']);
      	return $this->db->update('employee_deduction', $empData);
    }

    function saveDeductionHistory($empData, $codeDeduction, $userid){
		$this->db->where('code_deduction', $codeDeduction);
		$this->db->set('userid', $userid);
      	return $this->db->insert('employee_deduction_history', $empData);
    }

    function insertData($empData){
  	    return $this->db->insert('employee_deduction', $empData);
    }

    public function checkIfHasExistingDeduction($empid, $code){
    	return $this->db->query("SELECT * FROM employee_deduction WHERE employeeid = '$empid' AND code_deduction = '$code' ")->num_rows();
	}
	
	public function employeeDeductionPayments($employeeid, $code_deduc){
		return $this->db->query("SELECT * FROM employee_deduction_history WHERE employeeid = '$employeeid' AND code_deduction = '$code_deduc'")->num_rows();
	}

	function isDeductionUsed($deduc_id){
    	return $this->db->query("SELECT * FROM employee_deduction WHERE code_deduction = '$deduc_id'")->num_rows();
    }
	
}