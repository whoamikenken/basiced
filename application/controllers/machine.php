<?php 
/**
 * @author Max Consul
 * @copyright 2019
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Machine extends CI_Controller {

	/**
	* Logic for machine db data
	*
	* @return query result
	*/

    function __construct(){
        parent::__construct();
        
        $this->load->model('machine');
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
    }

	public function loadTerminalList(){

		$data['records'] = $this->machine->getTerminalList();
		$this->load->view('machine/gate_user_list', $data);
	}

	public function loadGateHistoryList(){

		$data['records'] = $this->machine->getGateHistoryList();
		$this->load->view('machine/gate_history', $data);
	}

	public function manageGateAccount(){
		$emplist = array();
		$data['gate_access'] = Globals::getUserAccess();
		$data['campus_list'] = $this->extensions->getCampusLists();
		$emprecords = $this->machine->getActiveEmployee();
		foreach($emprecords as $value){
			$emplist[$value['employeecode']] = $value['employeeid']." - ".$value['fullname'];
		}
		$data['emplist'] = $emplist;
		$data['gateaccess'] = array();
		$data['campus_allowed'] = array();
		$data['employee_allowed'] = array();
		$this->load->view('machine/manage_machine', $data);
	}

	public function validateGateAccount(){
		$gateaccess = $campus = $employee = "";
		$res = "";
		$data = $this->input->post();
		$id = $data['id'];
		$username = $data['username'];
		$gate_type = isset($data['gate_type']) ? $data['gate_type'] : "";
		$password = md5($data['password']);
		$gate_arr = isset($data['gateaccess']) ? $data['gateaccess'] : "";
		$campus_allowed = isset($data['campus_allowed']) ? $data['campus_allowed'] : "";
		$employee_allowed = isset($data['employee_allowed']) ? $data['employee_allowed'] : "";
		if($gate_arr){
			foreach($gate_arr as $val) $gateaccess .= $val.",";
			$gateaccess = substr($gateaccess, 0, -1);
		}
		if($campus_allowed){
			foreach($campus_allowed as $val) $campus .= $val.",";
			$campus = substr($campus, 0, -1);
		}
		if($employee_allowed){
			foreach($employee_allowed as $val) $employee .= $val.",";
			$employee = substr($employee, 0, -1);
		}
		$type = $data['type'];

		$insert_data = array(
			"username" => $username,
			"password" => $password,
			"gateaccess" => $gateaccess,
			"type" => $type,
			"gate_type" => $gate_type,
			"campus_allowed" => $campus,
			"employee_allowed" => $employee
		);

		if(!$username || !$password || !$gate_type) return false;

		if(!$id) $res = $this->machine->insertMachineAccount($insert_data);
		else{
			$where_clause = array("id" => $id);
			$res = $this->machine->updateMachineAccount($insert_data, $where_clause);
		}

		echo $res;
	}

	public function getTerminalData(){
		$emplist = array();
		$id = $this->input->post('id');
		$records = $this->machine->getTerminalList($id);
		foreach($records as $row){
			$data = array(
				"id" => $row['id'],
				"username" => $row['username'],
				"gateaccess" => explode(",", $row['gateaccess']),
				"type" => $row['type'],
				"gate_type" => $row['gate_type'],
				"campus_allowed" => isset($row['campus_allowed']) ? explode(",", $row['campus_allowed']) : array(),
				"employee_allowed" => isset($row['employee_allowed']) ? explode(",", $row['employee_allowed']) : array(),
			);
		}

		$data['gate_access'] = Globals::getUserAccess();
		$data['campus_list'] = $this->extensions->getCampusLists();
		$emprecords = $this->machine->getActiveEmployee();
		foreach($emprecords as $value){
			$emplist[$value['employeecode']] = $value['employeeid']." - ".$value['fullname'];
		}
		$data['emplist'] = $emplist;
		$data['gateaccess'] = array();
		$this->load->view('machine/manage_machine', $data);
	}

	public function deleteTerminalData(){
		$id = $this->input->post('id');
		$where_clause = array("id" => $id);
		$res = $this->machine->deleteTerminal($where_clause);
		echo $res;
	}
}