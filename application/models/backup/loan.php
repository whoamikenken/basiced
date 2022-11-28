<?php 
/**
 * @author Glen Mark Liporada
 * @copyright 2018
 *
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loan extends CI_Model {

	function saveEmployeeLoan($employeeid,$data=array()){
	    $user = $this->session->userdata("username");

	    if ($data['loan'] <> "All") {
	    	$query = $this->db->query("SELECT * FROM employee_loan WHERE employeeid='{$employeeid}' AND code_loan='{data['loan']}'");
	    	  
	    	$base_id = $base_id_history = '';

	    	if($query->num_rows() == 0) $res = $this->insertEmpLoan($data,$employeeid); 
	    	else                        $res = $this->updateEmpLoan($data,$employeeid);

	    	if ($res) {
	    		$this->insertEmpLoanHistoy($data,$employeeid);
	    	}
	    	return $res;
	    }
	    
	}

	function insertEmpLoan($data,$employeeid)
	{
		$res = $this->db->query("INSERT INTO employee_loan(employeeid,code_loan,datefrom,amount,startingamount,currentamount,nocutoff,loan_base,schedule,cutoff_period) VALUES('{$employeeid}','{$data['loan']}','{$data['deductiondate']}','{$data['amount']}','{$data['startingbalance']}','{$data['currentbalance']}','{$data['nocutoff']}','{$data['baseon']}','{$data['schedule']}','{$data['period']}')");

		if($res) return true;
        else return false;
	}

	function updateEmpLoan($data,$employeeid)
	{
		$res = $this->db->query("UPDATE employee_loan SET (datefrom='{$data['datefrom']}',amount='{$data['amount']}',startingamount='{$data['startingbalance']}',currentamount='{$data['currentbalance']}',nocutoff='{$data['nocutoff']}',loan_base='{$data['baseon']}',schedule='{$data['schedule']}',cutoff_period='{$data['period']}') WHERE employeeid='{$employeeid}' AND  code_loan='{$data['loan']}'");

		if($res) return true;
        else return false;
	}


	function insertEmpLoanHistoy($data,$employeeid)
	{
		$res = $this->db->query("INSERT INTO employee_loan_history(employeeid,code_loan,cutoffstart,cutoffend,amount,startBalance,currentBalance,mode,schedule,cutoff_period) VALUES('{$employeeid}','{$data['loan']}','{$data['deductiondate']}','0000-00-00','{$data['amount']}','{$data['startingbalance']}','{$data['currentbalance']}','UPDATE','{$data['schedule']}','{$data['period']}')");

		if($res) return true;
        else return false;
	}

 	function deleteLoanBatch($employeeid,$info){
        $query = "";
        $query = "DELETE FROM employee_loan WHERE employeeid='{$employeeid}' AND code_loan='{$info['loan']}';";
        $q_saveLoan = $this->db->query($query);
        return $q_saveLoan;
    }
	
}