<?php 
/**
 * @author Max Consul
 * @copyright 2019
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Machine_ extends CI_Controller {

	/**
	* Logic for machine db data
	*
	* @return query result
	*/

    function __construct(){
        parent::__construct();
        $this->load->model('machine');
        if(!$this->session->userdata('username','terminal_name')) redirect('main'); ///< prevent access to routes without session
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

	public function loadTerminalList(){
		$data['records'] = $this->machine->get_terminal();
		$this->load->view('machine/gate_user_list', $data);
	}

	public function loadGateHistoryList(){
		$data['records'] = $this->machine->getGateHistoryList();
		$this->load->view('machine/gate_history', $data);
	}

	public function manageGateAccount(){

		$emplist = array();
		$data['campus_list'] = $this->extensions->getCampusLists();
		$data['building_list'] = $this->extensions->getBuildingLists();
		$data['floor_list'] = $this->extensions->getFloorLists();
		$emprecords = $this->machine->getActiveEmployee();
		foreach($emprecords as $value){
			$emplist[$value['employeecode']] = $value['employeeid']." - ".$value['fullname'];
		}
		$data['emplist'] = $emplist;
		$this->load->view('machine/manage_machine', $data);
	}

	public function validateGateAccount(){
		$get_data = $campus =$building=$floor= $employee = "";
		$res = "";
		$toks = $this->input->post("toks");
		$formdata = $this->gibberish->decrypt( $this->input->post("formdata"), $toks );
		$data = $this->convertFormDataToArray($formdata);
		// $data = $this->input->post();
		$id = $data['id'];
		$username = $data['username'];
		$terminal_name = $data['terminal_name'];
		$campus = isset($data['campus']) ? $data['campus'] : "";
		$building = isset($data['building']) ? $data['building'] : "";
		$floor = isset($data['floor']) ? $data['floor'] : "";
		$template = isset($data['template']) ? $data['template'] : "";
		$password = md5($data['password']);
		$rt_password = md5($data['rt_password']);
		
		// $type = $data['type'];
		if($data['password'] != ''){
			$insert_data = array(
				"username" => $username,
				"terminal_name" => $terminal_name,
				"campus" => $campus,
				"building" => $building,
				"floor" => $floor,
				"template" => $template,
				"password" => $password,
				"rt_password" => $rt_password
			);
		}else{
			$insert_data = array(
				"username" => $username,
				"terminal_name" => $terminal_name,
				"campus" => $campus,
				"building" => $building,
				"floor" => $floor,
				"template" => $template
			);
		}
			

		if(!$username || !$password ) return false;

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
		$records = $this->machine->get_terminal($id);
				
		foreach($records as $row){
			$data = array(
				"id" => $row->id,
				"username" => $row->username,
				"terminal_name" => $row->terminal_name,
				"campus" => $row->campus,
				"building" => $row->building,
				"floor" => $row->floor,
				"template" => $row->template,
				"password" => $row->password,
				"rt_password" => $row->rt_password,
			); 
		}
		
		$data['campus_list'] = $this->extensions->getCampusLists();
		$data['building_list'] = $this->extensions->getBuildingLists();
		$data['floor_list'] = $this->extensions->getFloorLists();
		$emprecords = $this->machine->getActiveEmployee();
		foreach($emprecords as $value){
			$emplist[$value['employeecode']] = $value['employeeid']." - ".$value['fullname'];
		}
		$data['emplist'] = $emplist;
		// $data['campus_list'] = array();
		// $data['building_list'] = array();
		// $data['floor_list'] = array();
		$this->load->view('machine/manage_machine', $data);
	}

	public function deleteTerminalData(){
		$id = $this->input->post('id');
		$where_clause = array("id" => $id);
		$res = $this->machine->deleteTerminal($where_clause);
		echo $res;
	}

	public function isUsernameExist(){
		$username = $this->input->post('username');
		$res = $this->machine->isUsernameExist($username);
		echo $res;
	}

}