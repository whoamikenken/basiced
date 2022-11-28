<?php 
/**
 * @author Justin (with e)
 * @copyright 2018
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Loan_ extends CI_Controller {

	function __construct(){
	    parent::__construct();
	    if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
	}
	
	# for mcu-hyperion 21657
	function showLoanPaymentHistory(){
		$this->load->model("loan");
		$data = array();
		$toks = $this->input->post("toks");
		$data   = $this->input->post();
		if($toks){
		  unset($data["toks"]);
		  foreach($data as $key => $val){
		    $data[$key] = $this->gibberish->decrypt($val, $toks);
		  }
		}

		$q_loan_history = $this->loan->getLoanPaymentHistory($data['employeeid']);
		$data["loan_history_list"] = array();
		if($q_loan_history) $q_loan_history = Globals::result_XHEP($q_loan_history);
		foreach ($q_loan_history as $row) {
			$data["loan_history_list"][] = array(
				"lp_id"=> $row->id,
				"type" => $row->loan_desc,
				"payroll_cutoff" => ($row->cutoffstart && $row->cutoffend) ? date("F", strtotime($row->cutoffend)) ." ". date("d", strtotime($row->cutoffstart)) ."-". date("d", strtotime($row->cutoffend)) .", ". date("Y", strtotime($row->cutoffend)) : "",
				"loan_amount" => number_format($row->startingamount, 2),
				"remaining_balance" => number_format($row->balance, 2),
				"amount_payment" => number_format($row->debit, 2),
				"no_cutoff"	=> $row->payment_seq,
				"remaining_cutoff" => $row->no_payment,
				"status" => $row->status,
				"remarks" => $row->remarks,
				"code_loan" => $row->code_loan,
				"date" => $row->timestamp
			);
		}
		if($q_loan_history) $q_loan_history = Globals::result_XHEP($q_loan_history);
		$q_loan_history = $this->loan->getLoanPaymentHistoryByHistoryTable($data['employeeid']);

		foreach ($q_loan_history as $row) {
			$data["loan_history_list"][] = array(
				"lp_id"=> $row->id,
				"type" => $row->loan_desc,
				"payroll_cutoff" => ($row->cutoffstart && $row->cutoffend) ? date("F", strtotime($row->cutoffend)) ." ". date("d", strtotime($row->cutoffstart)) ."-". date("d", strtotime($row->cutoffend)) .", ". date("Y", strtotime($row->cutoffend)) : "",
				"loan_amount" => number_format($row->startBalance, 2),
				"remaining_balance" => number_format($row->balance, 2),
				"amount_payment" => number_format($row->debit, 2),
				"no_cutoff"	=> $row->payment_seq,
				"remaining_cutoff" => $row->no_payment,
				"status" => $row->status,
				"remarks" => $row->remarks,
				"code_loan" => $row->code_loan,
				"date" => $row->timestamp
			);
		}

		$data["is_title_display"] = (isset($data["is_title_display"])) ? $data["is_title_display"] : true;
		$this->load->view("employee/loans_history", $data);
	}

	function showEmpLoanList(){
		$this->load->model('loan');
		$this->load->model('payrolloptions');
		$data = array();
		$toks = $this->input->post("toks");
		$data   = $this->input->post();
		if($toks){
		  unset($data["toks"]);
		  foreach($data as $key => $val){
		    $data[$key] = $this->gibberish->decrypt($val, $toks);
		  }
		}

		$data["loan_list"] = array();
		$q_emp_loan = $this->loan->getEmployeeLoan($data['employeeid']);
		// echo "<pre>"; print_r($q_emp_loan); die;
		if($q_emp_loan) $q_emp_loan = Globals::result_XHEP($q_emp_loan);
		foreach ($q_emp_loan as $row) {
			$cutoff = $this->loan->getLastLoanPaymentEditHistoryCutoff($row->id);

			$data["loan_list"][] = array(
				"id" => $row->id,
				"type" => $row->loan_desc,
				"start_date" => ($row->datefrom != "0000-00-00" ? date('F d, Y',strtotime($row->datefrom)) : ""),
				"start_balance" => number_format($row->startingamount, 2),
				"remain_balance" => number_format($row->currentamount, 2),
				"amount" => number_format($row->amount, 2),
				"total_amount" => number_format((int) $row->total_amount, 2),
				"remain_cutoff" => $row->nocutoff - $cutoff,
				"no_cutoff" => $row->nocutoff,
				"schedule" => $this->payrolloptions->payscheduledesc($row->schedule),
				"period" => $this->payrolloptions->quarterdesc($row->cutoff_period,FALSE,$row->schedule),
				"code_loan" => $row->code_loan,
				"is_able_edit" => true
			);
		}

		$data["is_edit_display"] = isset($data["edit_display"]) ? $data["edit_display"] : true;
		$this->load->view("employee/loans_list", $data);
	}
	
	function showAddEditLoanModal(){
		$this->load->model('loan');
		$this->load->model('utils');
		$this->load->model('payrolloptions');
		$data = array();
		$toks = $this->input->post("toks");
		$data   = $this->input->post();
		if($toks){
		  unset($data["toks"]);
		  foreach($data as $key => $val){
		    $data[$key] = $this->gibberish->decrypt($val, $toks);
		  }
		}
		$data["is_modify"] = false;
		$code_loan = $deduction_date = $start_balance = $no_cutoff = $amount = $schedule = $quarter = $loanbase = "";

		$loanbase = "0";
		$q_emp_loan = $this->loan->getEmployeeLoan($data['employeeid'], $data["id"]);
		foreach ($q_emp_loan as $row) {
			$loanbase = $row->loan_base;
			$data["is_modify"] = true;
			$code_loan = $row->code_loan;
			$deduction_date = $row->datefrom;
			$start_balance = $row->currentamount; 
			$cutoff = $this->loan->getLastLoanPaymentEditHistoryCutoff($data["id"]);
			$no_cutoff = $row->nocutoff - $cutoff;		
			$amount = $row->amount;
			$schedule = $row->schedule;
			$quarter = $row->cutoff_period;
		}
		$data["based_on"] = $this->utils->basedon($loanbase, false);
		$data["code_loan"] = $this->payrolloptions->loan($code_loan);
		$data["deduction_date"] = $deduction_date;
		$data["start_balance"] = $start_balance;
		$data["no_cutoff"] = $no_cutoff;
		$data["amount"] = ($amount) ? $amount : 0;
		$data["schedule"] = $this->payrolloptions->payschedule("semimonthly");
		$data["quarter"] = ($schedule) ? $this->payrolloptions->quarter($quarter,FALSE,"semimonthly") : $this->payrolloptions->quarter("",FALSE,"semimonthly");

		$this->load->view("employee/loans_modal",$data);
	}

	function saveEmployeeLoan(){
		$this->load->model('loan');
		$data = $this->input->post();
		$toks = $this->input->post("toks");
		if($toks) $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));

		if(!isset($data["id"])){
			$data["id"] = $this->loan->getEmployeeLoanBaseId($data["employeeid"], $data["code_loan"]);
			$data["id"] = ($data["id"]) ? $data["id"] : "new";
		}

		$loan_data = array(
			"employeeid" 		=> $data["employeeid"],
			"loan" 				=> $data["code_loan"],
			"deductiondate"		=> $data["deduction_date"],
			"amount" 			=> $data["amount"],
			"startingbalance" 	=> $data["start_balance"],
			"currentbalance" 	=> isset($data["currentbalance"]) ? $data["currentbalance"] : $data["start_balance"],
			"nocutoff" 			=> $data["no_cutoff"],
			"schedule" 			=> "semimonthly",
			"cutoff_period"		=> $data["quarter"],
			"baseon"			=> $data["based_on"]
		);

		$result = "";
		if($data['id'] == 'new'){
			$is_loan_exist = $this->loan->getEmployeeLoanBaseId($data["employeeid"], $data["code_loan"]); 
			if(!$is_loan_exist){
				$q_insert_loan = $this->loan->insertEmpLoan($loan_data, $data["employeeid"]);

				if(!$q_insert_loan) $result = "Failed to saved..";
			}else $result = "Loan is already exist..";
		}else{
			$is_able_edit = $this->loan->isLoanAbleToEdit($data["employeeid"], $data["code_loan"]);
			if($is_able_edit){
				$q_update_loan = $this->loan->updateEmpLoan($loan_data, $data["employeeid"]);

				if(!$q_update_loan) $result = "Failed to saved..";
			}else{
				$q_update_loan = $this->loan->updateEmpLoanForLoanHasPayment($loan_data, $data["employeeid"]);
				
				if(!$q_update_loan) $result = "Failed to saved..";
			}
		}
		
		if(!$result) {
			$this->loan->insertEmpLoanHistoy($loan_data, $data["employeeid"]);
			$result = "Loan is successfully saved..";
		}
		echo $result;
	}

	function viewLoanPayment(){
		$this->load->model('loan');
		$data = array();
		$toks = $this->input->post("toks");
		$data   = $this->input->post();
		if($toks){
		  unset($data["toks"]);
		  foreach($data as $key => $val){
		    $data[$key] = $this->gibberish->decrypt($val, $toks);
		  }
		}

		$data["loan_payment_list"] = array();
		$q_loan_payment = $this->loan->getLoanPayment($data['id'], 'PAYMENT');
		foreach ($q_loan_payment as $row) {
			$data["loan_payment_list"][] = array(
				"payroll_cutoff" => ($row->cutoffstart && $row->cutoffend) ? date("F", strtotime($row->cutoffend)) ." ". date("d", strtotime($row->cutoffstart))  ."-". date("d", strtotime($row->cutoffend))  .", ". date("Y", strtotime($row->cutoffend)) : "",
				"credit" => number_format($row->credit, 2),
				"debit" => number_format($row->debit, 2),
				"balance" => number_format($row->balance, 2),
				"cutoff_seq" => $row->payment_seq,
				"remain_cutoff" => $row->no_payment,
				"timestamp" => ($row->timestamp) ? date("F d, Y", strtotime($row->timestamp)) : ""
			);
		}

		$this->load->view("employee/loans_payment", $data);
	}
	
	function showDeleteLoanModal(){
		$data = array();
		$data = $this->input->post();
		$toks = $data["toks"];
		unset($data["toks"]);
		foreach($data as $key => $val){
			$data[$key] = $this->gibberish->decrypt($val, $toks);
		}
		$this->load->model('loan');

		$data["delete_msg"] = "Are you sure, you want to delete this loan?";
		
		$q_loan_payment = $this->loan->getLoanPayment($data['id'], 'PAYMENT');
		$data["loan_payment_list"] = array();
		foreach ($q_loan_payment as $row) {
			$data["loan_payment_list"][] = array(
				"payroll_cutoff" => ($row->cutoffstart && $row->cutoffend) ? date("F", strtotime($row->cutoffend)) ." ". date("d", strtotime($row->cutoffstart))  ."-". date("d", strtotime($row->cutoffend))  .", ". date("Y", strtotime($row->cutoffend)) : "",
				"credit" => number_format($row->credit, 2),
				"debit" => number_format($row->debit, 2),
				"balance" => number_format($row->balance, 2),
				"cutoff_seq" => $row->payment_seq,
				"remain_cutoff" => $row->no_payment,
				"timestamp" => ($row->timestamp) ? date("F d, Y", strtotime($row->timestamp)) : ""
			);
		}


		$this->load->view("employee/loans_delete_modal", $data);
	}

	function deleteEmployeeLoan(){
		$this->load->model('loan');
		$data = array();
		$toks = $this->input->post("toks");
		$data   = $this->input->post();
		if($toks){
		  unset($data["toks"]);
		  foreach($data as $key => $val){
		    $data[$key] = $this->gibberish->decrypt($val, $toks);
		  }
		}

		$result = $this->loan->deleteEmpLoan($data['id'], $data['remarks']);

		echo $result;
	}

	function showLoanPaymentRemarks(){
		$this->load->model('loan');
		$toks = $this->input->post("toks");
		$id = $toks ? $this->gibberish->decrypt($this->input->post("lp_id"), $toks) :  $this->input->post("lp_id");

		$remarks = $this->loan->getLoanPaymentRemarks($id);

		echo $remarks;
	}
	# end for mcu-hyperion 21657

	function validateSkippingLoan(){
		$this->load->model('loan');
		$toks = $this->input->post("toks");
		$data   = $this->input->post();
		if($toks){
		  unset($data["toks"]);
		  foreach($data as $key => $val){
		    $data[$key] = $this->gibberish->decrypt($val, $toks);
		  }
		}
		$employeeid = $data['employeeid'];
		$code_loan = $data['code_loan'];
		$status = $data['status'];
		$is_exisiting = $this->loan->checkIfLoanIsExisting($employeeid, $code_loan);
		$res = $this->loan->skipEmployeeLoanPayment($employeeid, $code_loan, $status, $is_exisiting);
		if($res) echo "Successfully change loan status";
		else echo "Failed to change loan ";
	}

}
# end file