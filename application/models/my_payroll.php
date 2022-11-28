<?php 
//Added 6-3-2017
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class My_Payroll extends CI_Model {
	
	//MY PAYSLIP
	
	function getEmpPayslipList($empid=""){
		$result=array();
		$return = $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid = '{$empid}' AND (`status`='PROCESSED' OR `status`='SAVED') ORDER BY cutoffstart DESC")->result();
		return $return;
	}
	
	//MY LOAN
	
    function getCutoffListWithLoan($empid=""){
		$result=array();
		$query = $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid = '{$empid}' AND (loan != '' OR loan IS NOT NULL) ORDER BY cutoffstart");
		if($query->num_rows() != 0)
		{
			foreach($query->result() as $row)
			{
				$q = $this->db->query("SELECT startdate,enddate FROM payroll_cutoff_config WHERE startdate = '".$row->cutoffstart."' AND  enddate = '".$row->cutoffend."'")->result();
				if(!in_array($q,$result)) array_push($result,$q);
			}
		}
		return $result;
	}

	function verifyIfPending($empid="",$cutoff="",$category=""){
		$result = "";
		$cutoff = explode(",",$cutoff);
		$query = $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid = '{$empid}' AND status = 'PENDING' AND cutoffstart = '".$cutoff[0]."' AND cutoffend = '".$cutoff[1]."' AND ({$category} != '' OR {$category} IS NOT NULL)");
		if($query->num_rows() != 0)
		{
			$result = "PENDING";
		}
		return $result;

	}
	
	function getLoan($empid="",$cutoff=""){
		$array = array();
		$cutoff = explode(",",$cutoff);
		$query = $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid = '{$empid}' AND cutoffstart = '".$cutoff[0]."' AND cutoffend = '".$cutoff[1]."'")->result();
		if ($query[0]->loan !="") {
			foreach(explode("/",$query[0]->loan) as $row)
			{
				$r = explode("=",$row);
				if($r[1] != 0)
				{
					array_push($array,$r[0]);
				}
			}
		}
		
		
		return $array;
	}

	function getLoanHistory($empid="",$code=""){
		$return = $this->db->query("SELECT * FROM employee_loan_history WHERE employeeid = '{$empid}' AND code_loan = '{$code}' GROUP BY code_loan ORDER BY cutoffstart ASC")->result();
		return $return;
	}
	
	function getConsecutiveLoanHistory($empid="",$cutoff="",$code=""){
		$cutoff = explode(",",$cutoff);
		$query = $this->db->query("SELECT * FROM employee_loan_history WHERE employeeid = '{$empid}' AND cutoffstart = '".$cutoff[0]."' AND cutoffend = '".$cutoff[1]."'  AND code_loan = '{$code}' ORDER BY cutoffstart")->result();
		return $query;
	}
	
	function getLoanDescription($code=""){
		$result=array();
		$query = $this->db->query("SELECT description FROM payroll_loan_config WHERE id = {$code}")->result();
		$result = $query[0]->description;
		return $result;
	}
	
	//MY OTHER INCOME
	// function getCutoffListWithOtherIncome($empid=""){
		// $result=array();
		// $query = $this->db->query("SELECT * FROM employee_income WHERE employeeid = '{$empid}'");
		// if($query->num_rows() != 0)
		// {
			// foreach($query->result() as $row)
			// {
				// $q = $this->db->query("SELECT startdate,enddate FROM payroll_cutoff_config WHERE '".$row->datefrom."' BETWEEN startdate AND enddate")->result();
				// if(!in_array($q,$result)) array_push($result,$q);
			// }
		// }
		// return $result;
	// }
	
	// function getOtherIncomeHistory($empid="",$cutoff=""){
		// $result=array();
		// $cutoff = explode(",",$cutoff);
		// $return = $this->db->query("SELECT * FROM employee_income WHERE employeeid = '{$empid}' AND datefrom BETWEEN '".$cutoff[0]."' AND '".$cutoff[1]."' ORDER BY code_income ASC")->result();
		// return $return;
	// }
	
	function getOtherIncomeDescription($code=""){
		$result=array();
		$query = $this->db->query("SELECT description FROM payroll_income_config WHERE id = {$code}")->result();
		$result = $query[0]->description;
		return $result;
	}
		
	//MY OTHER DEDUCTION
	// function getCutoffListWithOtherDeduction($empid=""){
		// $result=array();
		// $query = $this->db->query("SELECT * FROM payroll_process_otherdeductions WHERE employeeid = '{$empid}'");
		// if($query->num_rows() != 0)
		// {
			// foreach($query->result() as $row)
			// {
				// $q = $this->db->query("SELECT startdate,enddate FROM payroll_cutoff_config WHERE '".$row->datefrom."' BETWEEN startdate AND enddate")->result();
				// if(!in_array($q,$result)) array_push($result,$q);
			// }
		// }
		// return $result;
	// }
	
	// function getOtherDeductionHistory($empid="",$cutoff=""){
		// $result=array();
		// $cutoff = explode(",",$cutoff);
		// $return = $this->db->query("SELECT * FROM payroll_process_otherdeductions WHERE employeeid = '{$empid}' AND datefrom BETWEEN '".$cutoff[0]."' AND '".$cutoff[1]."' ORDER BY code_income ASC")->result();
		// return $return;
	// }
	
	function getOtherDeductionDescription($code=""){
		$result=array();
		$query = $this->db->query("SELECT description FROM payroll_deduction_config WHERE id = {$code}")->result();
		$result = $query[0]->description;
		return $result;
	}
	
	//OTHER
		
	function getCutoffList(){
		$result=array();
		$q = $this->db->query("SELECT startdate,enddate FROM payroll_cutoff_config ORDER BY startdate")->result();
		$result = $q;
		return $result;
	}
	
	function getEmpLoan($empid=""){
		$result=array();
		$q = $this->db->query("SELECT * FROM employee_loan WHERE employeeid = '{$empid}' AND visibility='SHOW' ORDER BY code_loan");
		$result=$q;
		return $result;
	}
	
	function getEmpIncome($empid=""){
		$result=array();
		$q = $this->db->query("SELECT * FROM employee_income WHERE employeeid = '{$empid}' AND visibility='SHOW' ORDER BY code_income");
		$result=$q;
		return $result;
	}
	
	function getEmpOtherDeduction($empid=""){
		$result=array();
		$q = $this->db->query("SELECT * FROM employee_deduction WHERE employeeid = '{$empid}' AND visibility='SHOW' ORDER BY code_deduction");
		$result=$q;
		return $result;
	}
	
	function getConsecutiveOtherHistory($empid="",$cutoff=""){
		$result=array();
		$cutoff = explode(",",$cutoff);
		$query = $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid = '{$empid}' AND cutoffstart = '".$cutoff[0]."' AND cutoffend = '".$cutoff[1]."' ORDER BY cutoffstart");
		$result = $query;
		return $result;
	}

}
?>