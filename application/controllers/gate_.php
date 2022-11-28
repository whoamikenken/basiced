<?php
/**
* @author justin (with e)
* @copyright 2018
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gate_ extends CI_Controller{
	
	function __construct(){
		parent::__construct();

		$this->load->model('gate');
		$this->load->library('gibberish');
		
		$this->ip_address  = $this->input->ip_address();
		$this->mac_address = $this->gate->getTerminalMacAddress($this->ip_address);
	}

	public function index(){
		$data = array();

		$q_log = $this->gate->getGateLastLog($this->ip_address, $this->mac_address);
		if(count($q_log) > 0){
			foreach ($q_log as $row){
				$data = array(
					"username"    => $row->username,
					"ip_address"  => $this->ip_address,
					"mac_address" => $this->mac_address,
					"allowed"	  => $row->gate_tap_allow
				);
			}
			$this->redirectGatePage("main", $data);
		}else $this->redirectGatePage("login");
	}

	public function redirectGatePage($view='', $data=array()){
		if(!$view) exit('No direct script access allowed');

		$this->load->view("gate/gate-header");
		$this->load->view("gate/".$view, $data);
		$this->load->view("gate/gate-footer");
	}

	public function validateGateAccount(){
		if(!$this->input->post()) exit('No direct script access allowed');
		$data = array();
		$data = $this->input->post();
		$data["password"] = md5($this->gibberish->decrypt($data["password"], $_COOKIE['salt']));
		$response = "";
		
		$q_account = $this->gate->validateUser($data);
		if(!count($q_account)) $response = "Invalid Username and Password.";
		else{
			$data["login"] = date("Y-m-d H:i:s");
			$data["ip"] = $this->ip_address;
			$data["mac"] = $this->mac_address;
			$q_save = $this->gate->insertGateHistory($data);

			if(!$q_save) $response = "Failed to Login.";
		}

		echo $response;
	}

	public function showLogAttendanceHistory(){
		if(!$this->input->post()) exit('No direct script access allowed');
		$data = $this->input->post();
		$data["list"] = array();
		
		$list = array();
		foreach (array('employee', 'student') as $type) {
			$q_log_history = $this->gate->getTerminalLogHistory($data["username"], '', $type);

			foreach ($q_log_history as $row) {
				$list[$row->logtime][] = array(
					"date" => date("F d, Y", strtotime($row->logtime)),
					"time" => date("h:i A", strtotime($row->logtime)),
					"name" => str_replace("Ã‘", "Ñ", strtoupper($row->fullname)),
					"type" => $row->log_type,
					"user" => $row->username
				);
			}
		}
		
		if(count($list)) krsort($list);
		foreach ($list as $logtime => $emp_list) {
			foreach ($emp_list as $info) {
				$data["list"][] = $info;
			}
		}

		$this->load->view("gate/log-history", $data);
	}

	public function getCurrentDate(){
		if(!$this->input->post()) exit('No direct script access allowed');
		echo date("D d F Y");
	}

	public function logUserAttendance(){
		if(!$this->input->post()) exit('No direct script access allowed');
		$data = $response = array();
		$type_arr = array("ST" => "Student", "ET" => "Teaching Employee", "ENT" => "Non Teaching Employee");
		$data = $this->input->post();
		$allowed_type = explode(",", $data["access"]);

		if(strlen($data['userid']) > 10){
			$rfid_length = strlen($data['userid']);
		    $excess_no = $rfid_length - 10;
		    $data['userid'] = substr($data['userid'], $excess_no);
		}

		$user_data = $this->gate->getUserLOgData($data['userid']);
		
		$response = array(
			"is_error" => false,
			"message"  => "",
			"img_src"  => ""
		);
		if(count($user_data) && isset($user_data["type"]) && in_array($user_data["type"], $allowed_type)){
			$type = ($user_data["type"] == "ST") ? "student" : "employee";
			$log_type = "IN";
			$last_in  = "";
			$last_logtime = "";

			$q_last_log = $this->gate->findUserLastLog($user_data['userid'], $type);
			foreach ($q_last_log as $row){
				$log_type = ($row->log_type == "IN") ? "OUT" : "IN";
				$last_logtime  = $row->logtime;
			}
			$log_time = date("Y-m-d H:i:s", strtotime(date("Y-m-d") ." ". $data['time']));

			$trail_data = array(
				"userid"   => $user_data['userid'],
				"logtime"  => $log_time,
				"log_type"  => $log_type,
				"username" => $data['username']
			);

			$timesheet_data = array(
				"userid"   => $user_data['userid'],
				"timein"   => $last_logtime,
				"timeout"  => $log_time,
				"username" => $data['username']
			);

			$is_already_tap = false;
			if($last_logtime) $is_already_tap = $this->gate->getIsAlreadyTap($last_logtime, $log_time);

			if(!$is_already_tap){
				$this->load->model("timesheet");
				$error_list = array(
					'FAILED TO SAVE LOG. PLEASE CHECK YOUR CONNECTION.',
					'PLEASE LOGOUT FIRST',
					'PLEASE LOGIN FIRST',
					'INVALID USER'
				);
				/*$q_save = $this->gate->saveUserLog($trail_data, $timesheet_data, $log_type, $type);
				if(!$q_save){
					$response["is_error"] = true;
					$response["message"] = "Failed to log. Please try again.";
				}else{
					$response["img_src"] = $this->gate->getUserLogImage($user_data['userid']);
					$response["message"] = utf8_encode(strtoupper($user_data["name"])) ." is Successfully ". ucfirst(strtolower($log_type));
				}*/

				$function 	= ($type == "employee") ? "logEmployee" : "logStudent";
				$machine_id = $this->extras->returnmacaddress();
				$result = json_decode($this->timesheet->{$function}($log_time, $machine_id, 'IN-OUT', $data["username"], $data["userid"]));


				$response["message"]  = $result->message;
				$response["is_error"] = (in_array(strtoupper(trim($result->message)), $error_list)) ? true : false;
				if(!$response["is_error"]) 
					$response["img_src"] = $this->gate->getUserLogImage($user_data['userid']);
			}else{
				$response["is_error"] = true;
				$response["message"] = "You already tapped your card. Please try after 1 minute.";
			}
		}else{
			$response["is_error"] = true;
			if(isset($user_data["type"])) $response["message"] = $type_arr[$user_data["type"]] ." is not allowed to tap.";
			else 						  $response["message"] = "Invalid User.";
		}

		echo json_encode($response);
	}

	function testConnection(){
		if(!$this->input->post()) exit('No direct script access allowed');

		echo "Internet Available";
	}

	public function initiateHomework(){
		$data = $this->input->post();
		$username = $this->session->userdata("username");
		$timestamp = $this->extensions->getServerTime();
		$date_now = date("Y-m-d", strtotime($timestamp));
		$canenter = true;
		$last_record = "";
		$q_log = $this->gate->checkLastLog($username, $date_now); 
		$login_stat = "success";
		if ($q_log -> num_rows() == 1) { 
			foreach ($q_log -> result() as $rw) { 
				$last_record = $rw -> action; 
				switch (true) { 
					case $rw -> difference >= 1:
						$canenter = true; 
					break; 
					default:
					$canenter = false; 
				break;
				}
			} 
		} 
	
		if($canenter){
			$this->validateEmployeeAccomplishment($data, $username, $date_now);
			$res = $this->gate->saveWorkhomeInformation($username, $last_record, $date_now, $timestamp, $login_stat);
		}else{
			$res = array("error"=>1, "msg"=>"You are not allowed to tap. Please wait 1 minute to try again.", "logtype"=>"");
		}

		echo json_encode($res);
		
	}

	public function enterFieldWork(){
		$response = array();
		$username = $this->session->userdata("username");
		$timestamp = $this->extensions->getServerTime();
		$date_now = date("Y-m-d", strtotime($timestamp));
		$q_log = $this->gate->isFieldWork($username, $date_now); 
		if($q_log->num_rows() > 0){
			if($q_log->row()->log_type == "OUT"){
				$field_data = array("employeeid" => $username, "date" => $date_now, "ip"=> $this->input->ip_address(), "log_type" => "IN");
				$res = $this->gate->enterFieldWork($field_data); 
				$response = array("error"=>0, "msg"=>"<b>Well done!</b> You have successfully check in. Please attached your accomplishment later.", "time"=>date("H:i A", strtotime($timestamp)), "remarks"=>$this->gate->attendanceRemarks($username, $timestamp));
			}
		}else{
			$field_data = array("employeeid" => $username, "date" => $date_now, "ip"=> $this->input->ip_address(), "log_type" => "IN");
			$res = $this->gate->enterFieldWork($field_data); 
			$response = array("error"=>0, "msg"=>"<b>Well done!</b> You have successfully check in. Please attached your accomplishment later.", "time"=>date("H:i A", strtotime($timestamp)), "remarks"=>$this->gate->attendanceRemarks($username, $timestamp));
		}

		echo json_encode($response);
	}
	
	public function validateEmployeeAccomplishment(){
		$data = $this->input->post();
		$username = $this->session->userdata("username");
		$timestamp = $this->extensions->getServerTime();
		$date_now = date("Y-m-d", strtotime($timestamp));
		$filename = $file = $final_file = $size = $mime = $filetype = "";
		if($data){
			$allowed_types = array("jpg","jpeg","png","pdf","xlsx","csv","docx");
			if(isset($_FILES['files']['name'])){
				$filename = basename($_FILES['files']['name']);
				$file = file_get_contents($_FILES['files']['tmp_name'], $filename);
				$final_file = base64_encode($file);

				$size = $_FILES["files"]["size"] / 1024;
				$mime = Globals::convertMime($_FILES["files"]["type"]);
				$filetype = $_FILES["files"]["type"];

				$timefrom = "";
				$field_det = $this->gate->accomplishmentTimestamp($data["id"]);
				if($field_det->num_rows() > 0) $timefrom = $field_det->row()->timestamp; 
				$acc_data = array(
					"employeeid" => $username,
					"date" => $date_now,
					"timefrom" => $timefrom,
					"timeto" => $timestamp,
					"remarks" => $data["acc_remarks"],
					"log_type" => "OUT",
					"content" => $final_file,
					"filename" => $filename,
					"size" => $size,
					"mime" => $filetype
				);
				
				$this->gate->saveEmployeeAccomplishment($acc_data, $data["id"]);
				$response = array("error"=>0, "msg"=>"<b>Well done!</b> You have successfully check out. Your attachment upload successfully.", "time"=>date("H:i A", strtotime($timestamp)), "remarks"=>$this->gate->attendanceRemarks($username, $timestamp));
			}
		}

		echo json_encode($response);
	}

	public function currentLogStatus(){
		$username = $this->session->userdata("username");
		$timestamp = $this->extensions->getServerTime();

		$date_now = date("Y-m-d", strtotime($timestamp));
		$time_now = date("H:i:s", strtotime($timestamp));
		$toks = $this->input->get('toks');
		$recent = $toks? $this->gibberish->decrypt($this->input->get("recent"), $toks) : $this->input->get("recent");
		$getChecker = $toks? $this->gibberish->decrypt($this->input->get("checker"), $toks) : $this->input->get("checker");
		$Checker = $this->attcompute->displaySched($username, $date_now)->result();
		
		$isFlexi = $this->attcompute->isFlexible($username, $date_now);
		if ($getChecker) {
			if (empty($Checker)) {
			// echo "<pre>";print_r($getChecker);die;
				$scheduleChecker = $this->attcompute->scheduleChecker($username, $date_now);
				$q_stat = $this->gate->currentFirstLogStatusWeb($username, $date_now);
				if ($isFlexi) {
					echo json_encode(array("id"=>$username, "log"=>"", "remarks"=>"Flexi", "type"=>"web", "timein"=>$time));
				}else{
					if (count($q_stat) != 0) {
						$time = date("h:i A",strtotime($q_stat[0]->localtimein));
					}else{
						$time = '-----';
					}
					if ($scheduleChecker == "true") {
						echo json_encode(array("id"=>$username, "log"=>"", "remarks"=>"NoSchedToday", "type"=>"web", "timein"=>$time));
					}else{
						echo json_encode(array("id"=>$username, "log"=>"", "remarks"=>"NoSched", "type"=>"web", "timein"=>$time));
					}
					
				}
			}else{
				if($recent == 0){
					$deptid = $this->extensions->getEmployeeDeptid($username);
					$teachingtype = $this->extensions->getEmployeeTeachingType($username);
					$holiday = $this->attcompute->isHolidayNew($username,$date_now,$deptid,"","",$teachingtype); 
					$holidayInfo = $timein =  "";
					if ($holiday) {
						$holidayInfo = $this->attcompute->holidayInfo($date_now,"",$teachingtype); 
					}
					// echo "<pre>";print_r();die;
					$absent =  date("H:i:s",strtotime($Checker[0]->absent_start));
					$tardy =  date("H:i:s",strtotime($Checker[0]->tardy_start));
					// if ($time_now >= $absent) {
					// 	$remarks = "Absent";
					// 	$time = $absent;
					// }elseif($time_now >= $tardy){
					// 	$remarks = "Late";
					// 	$time = date("h:i A",strtotime($Checker[0]->starttime));
					// }elseif($time_now <= $absent){
					if ($holiday == "") {
						$remarks = "No Time In and Out";
					}else{
						$remarks = "holiday";
						$timein = $holidayInfo['description'];
					}
						
						// $time = date("h:i A",strtotime($Checker[0]->starttime));
					// }
					echo json_encode(array("id"=>$username, "log"=>"OUT", "remarks"=>$remarks, "type"=>"web", "timein"=>$timein));
				}else{
					$q_stat = $this->gate->currentFirstLogStatusWeb($username, $date_now);
					// echo "<pre>";print_r($q_stat);die;
					$login = date("H:i:s",strtotime($q_stat[0]->localtimein));
					if (count($q_stat) > 0) {
						$tardy =  date("H:i:s",strtotime($Checker[0]->tardy_start));
						$absent =  date("H:i:s",strtotime($Checker[0]->absent_start));
						if($login >= $tardy) {
							$remarks = "LateIn";
							$time = date("h:i A",strtotime($q_stat[0]->localtimein));
						}elseif($login <= $tardy) {
							$remarks = "On Time";
							$time = date("h:i A",strtotime($q_stat[0]->localtimein));
						}
					}
					if(count($q_stat) > 0) echo json_encode(array("id"=>$q_stat[0]->userid, "log"=>$q_stat[0]->log_type, "remarks"=>$remarks, "type"=>"web", "timein"=>$time));
					else echo false;
				}
			}
		}else{
			$q_stat = $this->gate->currentLogStatus($username, $date_now);
			if ($isFlexi) {
				echo json_encode(array("id"=>$username, "log"=>"", "remarks"=>"Flexi", "type"=>"gate", "timein"=>"--:--"));
			}else{
				if($q_stat->num_rows() > 0) echo json_encode(array("id"=>$q_stat[0]->id, "log"=>$q_stat[0]->log_type, "type"=>"gate"));
				else echo false;
			}
		}
	}

	public function accomplishmentLists(){
		$timestamp = $this->extensions->getServerTime();
		$data = $this->input->post();
		$where_clause = "";
		if($data["employee"]) $where_clause .= " AND employeeid = '{$data["employee"]}' ";
		if($data["date"]) $where_clause .= " AND DATE(timestamp) = '{$data["date"]}' ";

		$acc_list = $this->gate->accomplishmentLists($where_clause);
		$data["records"] = ($acc_list->num_rows() > 0) ? $acc_list->result_array() : array();
		$this->load->view("gate/accomplishment_list", $data);
	}

	public function activeEmployeeLists(){
		$option = "<option value=''>Select an option</option>";
		$emplist = $this->setup->getActiveEmployees();
		foreach($emplist as $emplists){
			$option .= "<option value='".$emplists["employeeid"]."'> ".$emplists["fullname"]."</option>";
		}

		echo $option;
	}

}