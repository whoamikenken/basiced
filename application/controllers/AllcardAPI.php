<?php
/**
* @author Max Consul
* @copyright 2019
* 
* API controller
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');

class AllcardAPI extends REST_Controller{

	function __construct(){
	    parent::__construct();
	}

	function employeeDetails_get(){
		$header_data = getallheaders();
		$access_token = substr($header_data["Authorization"], 7);
		if($this->extensions->verifyAccessToken($access_token)){
			$employeelist = $this->extensions->getEmployeeList();
			if($employeelist > 0) $this->response($employeelist, 200);
			else $this->response("Employee not exists.", 200);
		}else{
			$this->response("Invalid Credentials", 200);
		}
	}

	function studentDetails_get(){
		$header_data = getallheaders();
		$access_token = substr($header_data["Authorization"], 7);
		if($this->extensions->verifyAccessToken($access_token)){
			$studentlist = $this->extensions->getStudentList();
			if($studentlist > 0) $this->response($studentlist, 200);
			else $this->response("Student not exists.", 200);
		}else{
			$this->response("Invalid Credentials", 200);
		}
	}

	function generateAccessToken_post(){
		$posted_data = file_get_contents("php://input");
		$posted_data = json_decode($posted_data);
		if($posted_data->client_secret == "HIKT1G/DfmqHadXjnGVTIQ" && $posted_data->username == "pinnacle1!" && $posted_data->password == "povedahyperion1!"){
			$access_token = str_split('abcdefghijklmnopqrstuvwxyz'
	                 .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
				.'0123456789!@#$%^&*(){}-_+[]`');  /*any characters*/

			shuffle($access_token);  /*probably optional since array_is randomized; this may be redundant*/
			$granted_token = '';
			foreach (array_rand($access_token, 80) as $k) $granted_token .= $access_token[$k];

			$this->db->insert("token_allowed", array("access_token" => $granted_token, "status" => "PENDING"));  /*save granted token*/

			$this->response($granted_token, 200);
		}else{
			$this->response("Invalid Credentials", 200);
		}
	}

	function updateEmployeeRfid_post(){
		$success_count = 0;
		$failed_count = 0;
		$header_data = getallheaders();
		$access_token = substr($header_data["Authorization"], 7);
		if($this->extensions->verifyAccessToken($access_token)){
			$posted_data = file_get_contents("php://input");
			$posted_data = json_decode($posted_data);
			foreach($posted_data as $post_val){
				$employeeid = $post_val->employeeid;
				$rfid = $post_val->rfid;
				$res = $this->extensions->updateEmployeeCardnumber($employeeid, $rfid);

				if($res){
					$success_count += 1;
				}
				else{
					$failed_count += 1;
				}

			}

			if($failed_count && $success_count) $this->response("Successfully updated ".$success_count." employee card number. And failed to update ".$failed_count." Please check all required fields.", 200);
			else if(!$failed_count && $success_count) $this->response("Successfully updated ".$success_count." employee card number.", 200);
			else if($failed_count && !$success_count) $this->response("Failed to update ".$failed_count." employee card number. Please check all required fields.", 200);
			else $this->response("Error while processing your request. Please try again.", 200);
		}else{
			$this->response("Invalid Credentials", 200);
		}
	}

}