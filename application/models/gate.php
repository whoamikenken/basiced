<?php
/**
* @author justin (with e)
* @copyright 2018
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gate extends CI_Model{
	public $attendance_setup = array(
		"employee" => array(
			"timesheet" => "timesheet",
			"trail"		=> "timesheet_trail",
			"userdata"	=> "employee",
			"join_on"	=> "employeeid"
		),
		"student" => array(
			"timesheet" => "timesheet_student",
			"trail"		=> "timesheet_trail_student",
			"userdata"	=> "student",
			"join_on"	=> "studentid"			
		)
	);

	function __construct(){
		parent::__construct();
	}

	function validateUser($data){
		return $this->db->get_where('user_info', $data)->result();
	}

	function insertGateHistory($data){
		$include_col = array('username', 'ip', 'mac', 'login', 'logout', 'logout_by');

		$save_data = array();
		foreach ($data as $key => $value) 
			if(in_array($key, $include_col)) $save_data[$key] = $value;

		return $this->db->insert('user_gate_history', $save_data);
	}

	function getGateLastLog($ip_address, $mac_address){
		return $q_gate_history = $this->db->query("SELECT a.*, b.gate_tap_allow
												   FROM user_gate_history a
												   INNER JOIN user_info b ON b.username = a.username
												   WHERE a.ip='$ip_address' AND mac='$mac_address' AND a.logout='0000-00-00 00:00:00' AND a.logout_by='' AND b.gateaccess='1'
												   ORDER BY a.id LIMIT 1;")->result();
	}

	function getTerminalLogHistory($username, $date='', $type='employee'){
		$date = ((!$date) ? date("Y-m-d") : $date) ."%";
		extract($this->attendance_setup[$type]);

		return $this->db->query("SELECT a.*, CONCAT(b.lname, ', ', b.fname, ' ', IFNULL(b.mname, '')) AS fullname
								 FROM $trail a
								 LEFT JOIN $userdata b ON b.{$join_on} = a.userid
								 WHERE username='$username' AND logtime LIKE '$date'
								 ORDER BY logtime DESC")->result();

	}

	function getUserLOgData($userid){
		$data = array();

		foreach ($this->attendance_setup as $type => $info) {
			$table 	= $info['userdata'];
			$id_column = $info['join_on'];
			$rf_column = $type ."code";
			$q_data = $this->db->query("SELECT * FROM $table WHERE $id_column = '$userid' OR $rf_column = '$userid';")->result();

			foreach ($q_data as $row) {
				$data = array(
					"userid" => $row->{$id_column},
					"name"	 => strtoupper($row->lname .", ". $row->fname ." ". $row->mname),
					"type"	 => ($type == "student") ? "ST" : (($row->teachingtype == "teaching") ? "ET" : "ENT")
				);
			}
			
			if(count($data)) break;
		}
		
		return $data;
	}

	function findUserLastLog($userid, $type, $date=''){
		$table = $this->attendance_setup[$type]["trail"];
		$date = ((!$date) ? date("Y-m-d") : $date) ."%";

		return $this->db->query("SELECT * FROM $table WHERE userid='$userid' AND logtime LIKE '$date' ORDER BY logtime DESC LIMIT 1;")->result();
	}

	function saveUserLog($trail_data, $timesheet_data, $logtype, $type){
		#$q_save = true;

		/*if($logtype == "OUT"){
			$timesheet_table = $this->attendance_setup[$type]["timesheet"];
			$q_save = $this->db->insert($timesheet_table, $timesheet_data);
		}

		if($q_save){
			$trail_table = $this->attendance_setup[$type]["trail"];
			$q_save = $this->db->insert($trail_table, $trail_data);
		}*/

		$q_save = $this->db->query("CALL prc_save_logs('{$trail_data["userid"]}', '{$trail_data["logtime"]}', '{$trail_data["username"]}', '$type');");
		return $q_save;
	}

	function getUserLogImage($userid){
		$src = base_url() ."images/personal.png";

		$q_image = $this->db->query("SELECT * FROM elfinder_file WHERE title='$userid';")->result();
		foreach ($q_image as $row) $src = "data:". $row->mime .";base64,". base64_encode($row->content);

		return $src;
	}

	function getIsAlreadyTap($date1, $date2='', $validate_seconds=60){
		$is_already_tap = false;
		
		$date_from = strtotime($date1);
		$date_to   = strtotime($date2);
		
		$diff_logs = $date_to - $date_from;
		
		if($diff_logs <= $validate_seconds) $is_already_tap = true;
		return $is_already_tap;
	}

	function getTerminalMacAddress($ip_address){
		$mac_address = "";
		$ip_address = "(". $ip_address .")";

		$mac_list_string = shell_exec('arp -an');
		foreach (explode("?", $mac_list_string) as $mac_info) {
			if($mac_info){
				$mac_info = trim($mac_info);
				list($ip, $mac) = explode(" at ", $mac_info);

				if($ip == $ip_address) $mac_address = substr($mac, 0, 17);
			}
		}
		return $mac_address;
	}

	public function checkLastLog($employeeid, $date_now){
		return $this -> db -> select("action, TIMESTAMPDIFF(MINUTE, datecreated, NOW()) AS difference") -> from ($this -> db -> dtr_base.".`tblLoginAttempts`") -> where("user_id",$employeeid) -> where("status","success") -> where("date(datecreated)",$date_now) -> order_by("id","desc") -> limit(1) -> get();
	}

	public function saveWorkhomeInformation($username, $last_record, $date_now, $timestamp, $login_stat){
		$response = array("error"=>1, "msg"=>"There is problem in your employee details. Please contact admin.");
		$emprecords = array();
		$new_record = ( $last_record == "IN" ? "OUT" : "IN" );
		$fullname = $this->extensions->getEmployeeName($username);
		$fields = array( 
			"user_id" => $username, 
			"stamp" => date("H:i:s",strtotime($timestamp)),
			"action" => $new_record,
			"time" => time(), 
			"fullname" => $fullname,
			"status" => $login_stat, 
			"ip" => $this->input->ip_address(), 
			"terminalid" => "online gate", 
			"hardwareid" => "personal machine",
			"accuracy" => "100", 
			"username" => "workhome"    
		);
		$this -> db -> insert($this -> db -> dtr_base.".`tblLoginAttempts`", $fields);

		$trail_fields = array( 
			"userid" => $username, 
			"logtime" => $timestamp,
			"mac_add" => $this->input->ip_address(), 
			"log_type" => $new_record,
			"machine_id" => "personal",
			"username" => "workhome"    
		);

		$res = $this -> db -> insert("timesheet_trail", $trail_fields); 
		if($res) $response = array("error"=>0, "msg"=>"<b>Well done!</b> You have successfully logged ". $new_record. ". Please attached your accomplishment later.", "time"=>date("H:i A", strtotime($timestamp)), "remarks"=>$this->attendanceRemarks($username, $timestamp));
		else $response = array("error"=>1, "msg"=>"Failed to log. Please try again later.", "time"=>date("H:i A", strtotime($timestamp)), "remarks"=>$this->attendanceRemarks($username, $timestamp));

		switch ($login_stat) 
		{ 
			case 'success': 

				switch ($new_record) 
				{ 
					case 'OUT':   
						if (!in_array("timeout", $emprecords)) $emprecords["timeout"] = $date_now." ".date("H:i:s",strtotime($timestamp)); 

						$query = $this -> db -> select("*") -> from ($this -> db -> dtr_base.".`tblLoginAttempts`") -> where("user_id",$username) -> where("status","success") -> where("action","IN") -> where("date(datecreated)",$date_now) -> order_by("id","desc") -> limit(1) -> get();
						if ($query -> num_rows() == 1) 
						{ 
							foreach ($query -> result() as $rw) 
							{   
								if (!in_array("timein", $emprecords)) $emprecords["timein"] = date("Y-m-d",strtotime($rw -> datecreated)) . " " . $rw -> stamp; 
							}
						} else{
							$response = array("error"=>1, "msg"=>"There is problem in your employee details. Please contact admin.");
						}

						$fields = array(
							"userid" => $username, 
							"timein" => $emprecords["timein"], 
							"timeout" => $emprecords["timeout"],
							"username" => "workhome" 
						);    
						$res = $this -> db -> insert($this -> db -> dtr_base.".`tblTimesheet`",$fields );
						if($res) $response = array("error"=>0, "msg"=>"<b>Well done!</b> You have successfully logged ". $new_record. ". Please contact your head to confirm your accomplishment.");
						else $response = array("error"=>1, "msg"=>"Failed to log. Please try again later.");
					break; 
				}
			break; 
		}
		
		return $response;
	} 
	
	public function currentLogStatusWeb($username, $date_now){
		return $this->db->query("SELECT * FROM webcheckin_trail WHERE userid = '$username' AND DATE(date_created) = '$date_now' ORDER BY date_created DESC LIMIT 1 ")->result();
	}

	public function currentFirstLogStatusWeb($username, $date_now){
		return $this->db->query("SELECT * FROM webcheckin_history WHERE userid = '$username' AND DATE(date_created) = '$date_now' LIMIT 1")->result();
	}

	public function currentLogStatus($username, $date_now){
		return $this->db->query("SELECT * FROM employee_accomplishments WHERE employeeid = '$username' AND DATE(timestamp) = '$date_now' ORDER BY timestamp DESC LIMIT 1 ");
	}

	public function saveEmployeeAccomplishment($data, $id){
		$this->db->where("id", $id);
		$this->db->set($data);
		return $this->db->update("employee_accomplishments");
	}

	public function attendanceRemarks($employeeid, $timestamp){
		return $this->extensions->getTimeInAccuracy($employeeid, date("h:i:s", strtotime($timestamp)));
	}

	public function accomplishmentLists($where_clause){
		return $this->db->query("SELECT * FROM employee_accomplishments WHERE employeeid != '' $where_clause");
	}

	public function isFieldWork($employeeid, $date){
		return $this->db->query("SELECT * FROM employee_accomplishments WHERE DATE(timestamp) = '$date' AND employeeid = '$employeeid' ");
	}

	public function enterFieldWork($data){
		return $this->db->insert("employee_accomplishments", $data);
	}

	public function accomplishmentTimestamp($id){
		return $this->db->query("SELECT * FROM employee_accomplishments WHERE id = '$id' AND log_type = 'IN' ");
	}

}
