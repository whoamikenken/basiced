<?php 
//Added 6-1-2017

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Disciplinary_Action extends CI_Model {

	function getOffensesTypes($code=""){
		$wC = "";
		if($code)	$wC = "WHERE code='$code'";
		$result = $this->db->query("SELECT * FROM code_disciplinary_action_offense_type $wC")->result();
		return $result;
	}
	
	function getOffensesInfo($code=""){
		$result = $this->db->query("SELECT * FROM code_disciplinary_action_offense_type WHERE code='$code'");
		if($result->num_rows()) return $result->result();
		else return false;
	}
	
	public function insertOffensesInfo($code="", $desc="", $sanctions = "", $message = "", $frequency = "", $month = ""){
		$res = $this->db->query("INSERT INTO code_disciplinary_action_offense_type (code,description,sanctions,message,frequency,nomonths) VALUES ('$code','$desc','$sanctions','$message','frequency','month')");
		return $res;
	}
	
	public function updateOffensesInfo($code="", $desc="", $sanctions = "", $message = "", $frequency = "", $month = ""){
		$res = $this->db->query("UPDATE code_disciplinary_action_offense_type SET `code`='$code', description='$desc', sanctions='$sanctions', message='$message', frequency='$frequency', nomonths='$month' WHERE `code`='$code'");
		return $res;
	}
	
	function getSanctions($code=""){
		$wC = "";
		if($code) $wC = "WHERE code='$code'";
		$result = $this->db->query("SELECT * FROM code_disciplinary_action_sanction $wC")->result();
		return $result;
	}
	function getSanction(){
		$wC = "";
		$return = "<option value=''>Select</option>";
		$result = $this->db->query("SELECT * FROM code_disciplinary_action_sanction ")->result();
		foreach ($result as $key => $row) {
				$return .= "<option value='$row->code'>$row->description</option>";
		}

		return $return;
	}
	function getSanctionsInfo($code=""){
		$result = $this->db->query("SELECT * FROM code_disciplinary_action_sanction WHERE code='$code'")->result();
		return $result;
	}
	
	function getSanctionsDesc($code=""){
		$result="";
		$query = $this->db->query("SELECT description FROM code_disciplinary_action_sanction WHERE code='$code'")->result();
		foreach($query as $row){
			$result = $row->description;
		}
		return $result;
	}
	
	public function insertSanctionsInfo($code="", $desc="", $message = "", $sanctions = ""){
		$res = $this->db->query("INSERT INTO code_disciplinary_action_sanction (code,description,message) VALUES ('$code','$desc','$message')");
		return $res;
	}
	
	public function updateSanctionsInfo($code="", $desc="", $message = "", $sanctions = ""){
		$res = $this->db->query("UPDATE code_disciplinary_action_sanction SET `code`='$code', description='$desc', message='$message' WHERE `code`='$code'");
		return $res;
	}
	
	function removeRecord($code="", $tbl=""){
		$res = $this->db->query("DELETE FROM $tbl WHERE `code`='$code'");
		return $res;
	}
	
	function getEmpOffenseDetails($id=''){
		$res = $this->db->query("SELECT * FROM employee_disciplinary_action WHERE id='$id'");
		return $res;
	}
	
	function getOffenseHistory($employeeid="",$confirm = ""){
        $wC = "";
        if($employeeid)   $wC .= "`employeeid`='$employeeid'";
        if($confirm){ if($wC){$wC .= " AND ";}   $wC .= "`confirm`='$confirm'";}
		
		if($wC) $wC = "WHERE " . $wC; 
	

        $query = $this->db->query("
        				SELECT DISTINCT a.*,b.description AS offense,c.description AS sanction FROM employee_disciplinary_action a
						LEFT JOIN code_disciplinary_action_offense_type b ON a.`offense_code` = b.code
						LEFT JOIN code_disciplinary_action_sanction c ON a.`sanction_code` = c.code 
						$wC 
        			");
        return $query;
    }
	
	function saveEmpOffense($id="",$employeeid="",$dateWarning="", $offense_code="", $dateViolation="", $employeersStatement="",$empStatement="", $sanction_code="", $month="", $year=""){
    	$res = "";
    	$user = $this->session->userdata('username');
    	if($id){
			$res = $this->db->query("UPDATE employee_disciplinary_action 
					SET `dateWarning`='$dateWarning', 
						`offense_code`='$offense_code', 
						`dateViolation`='$dateViolation', 
						`employeers_statement`='$employeersStatement', 
						`employee_statement`='$empStatement', 
						`sanction_code`='$sanction_code', 
						`user`='$user',
						`month` = '$month',
						`year` = '$year'
					WHERE `id`='$id'");
    	}else{
			$res = $this->db->query("INSERT INTO employee_disciplinary_action(employeeid,dateWarning,offense_code,dateViolation,employeers_statement,employee_statement,sanction_code,user, month,year) 
				VALUES ('$employeeid','$dateWarning','$offense_code','$dateViolation','$employeersStatement','$empStatement','$sanction_code','$user', '$month', '$year')");
    	}
		return $res;
	}
	
	function saveEmpOffenseHistory($employeeid="", $offense_code="", $sanction_code=""){
    	// $query = $this->db->query("SELECT FROM");
		$i = 0;
		if($offense_code == "TARDINESS")
		{
			$que = $this->db->query("SELECT * FROM timesheet WHERE userid='".$employeeid."' ORDER BY timein")->result();
			foreach($que as $r)
			{
				$sched = $this->displaySched($employeeid,$r->timein);
				$schedstart = $tardy_start = "";
				foreach($sched as $s){
					$schedstart = $s->starttime;
					$tardy_start = $s->tardy_start;
				}
				
				$login = date("H:i:s",strtotime($r->timein));
				$logtime     = strtotime($login);
				$schedstart  = strtotime($schedstart);
				$tardy_start  = strtotime($tardy_start);
				
				$lateut = "";
				if($logtime > $schedstart)
				{
					$lateut        = round(abs($logtime - $schedstart) / 60,2);
					if( $lateut > round(abs(strtotime($tardy_start) - $schedstart) / 60,2) )   $lateut = date('H:i', mktime(0,$lateut));
                    else                 $lateut = "";
				}
				if($lateut) $i+=1;
			}
		}
		else if($offense_code == "ABSENTEISM")
		{
			$que = $this->db->query("SELECT date FROM timesheet_absents WHERE userid = '".$employeeid."' 
			AND '".$employeeid."' IN (SELECT employeeid FROM employee_schedule WHERE idx  = DATE_FORMAT(date,'%w'))
			AND '".$employeeid."' NOT IN (SELECT employeeid FROM leave_request WHERE DATE(date) BETWEEN fromdate AND todate)
			AND '".$employeeid."' NOT IN (SELECT employeeid FROM seminar_request WHERE DATE(date) BETWEEN dfrom AND dto)
			ORDER BY date DESC");
			foreach($que->result() as $r)
			{
				$holiday = $this->attcompute->isHoliday($r->date); 
				if(!$holiday)
				{
					$i++;							
				}
			}
		}
		
		$query = $this->db->query("DELETE FROM disciplinary_action_history WHERE employeeid='{$employeeid}' AND offense = '{$offense_code}'");
		$this->db->query("INSERT INTO disciplinary_action_history(employeeid,offense,sanction,count) VALUES('{$employeeid}','{$offense_code}','{$sanction_code}','{$i}')");
	}
	
	function deleteEmpOffense($id=''){
		$res = $this->db->query("DELETE FROM employee_disciplinary_action WHERE id='$id'");
		return $res;
	}
	
	function employeeDisciplinaryActionList($division="",$dept="",$offenseType="",$dfrom="",$dto="",$status=""){
		$wC = "";
		if($division) $wC = "AND b.managementid = '$division' ";
		if($dept) $wC .= "AND b.office = '$dept' ";
		if($status) $wC .= "AND a.confirm = '$status' ";
		if($offenseType) $wC .= " AND a.offense_code = '$offenseType'";
		
		
		$query = $this->db->query("SELECT a.*,CONCAT(b.lname,', ',b.fname,' ',b.mname) as fullname,c.description as department,d.description as offense_type,e.description as sanction
		FROM employee_disciplinary_action a
		INNER JOIN employee b on a.employeeid = b.employeeid
		INNER JOIN code_office c on b.office = c.code
		INNER JOIN code_disciplinary_action_offense_type d on a.offense_code = d.code
		INNER JOIN code_disciplinary_action_sanction e on a.sanction_code = e.code
		WHERE a.dateWarning BETWEEN '$dfrom' AND '$dto' $wC ORDER BY a.dateWarning"); //AND a.submission_date != '0000-00-00'
		return $query;
	}
	
	// function empWithExcessiveTardiness($fornotif=false){
	// 	$data = array();
	// 	$total = array();
	// 	$date_from = $date_to = date('Y-m-d');
	// 	$count = 0;
	// 	$old_employeeid = '';
	// 	$q_date = $this->db->query("SELECT * FROM code_leave_setup WHERE leavetype = 'VL' LIMIT 1 ");
	// 	if($q_date->num_rows > 0){
	// 		$date_from = $q_date->row()->dfrom;
	// 		$date_to = $q_date->row()->dto;
	// 	}

	// 	$sanctions = array();
	// 	$sanctionToPunish = $this->extensions->getDisciplinarySanctions("TARDINESS");
	// 	$sanctionToPunish = explode("/", $sanctionToPunish);
	// 	foreach($sanctionToPunish as $key => $value){
	// 		$sanc_data = explode("=", $value);
	// 		$sanctions[$sanc_data[0]] = $sanc_data[1];
	// 	}

	// 	$sanctions_type = '';

	// 	$q_late = $this->db->query("SELECT * FROM employee_attendance_detailed a INNER JOIN employee b ON b.`employeeid` = a.`employeeid` INNER JOIN code_office c ON b.deptid = c.code WHERE sched_date BETWEEN '$date_from' AND '$date_to' AND (late <> '' AND late <> '0:00') ORDER BY a.employeeid ");
	// 	if($fornotif){	
	// 		if($q_late->num_rows > 0){
	// 			foreach($q_late->result_array() as $key => $value){
	// 				if($value['employeeid'] != $old_employeeid){
	// 					$data[$value['employeeid']]['employeeid'] = $value['employeeid'];
	// 					$data[$value['employeeid']]['department'] = $value['description'];
	// 					$data[$value['employeeid']]['fullname'] = $value['lname'].", ".$value['fname'].", ".$value['mname'];
	// 					$data[$value['employeeid']]['freq'] = 1;
	// 					$data[$value['employeeid']]['count'] = $this->attcompute->exp_time($value['late']);
	// 				}else{
	// 					$data[$value['employeeid']]['count'] += $this->attcompute->exp_time($value['late']);
	// 					if($data[$value['employeeid']]['freq']) $data[$value['employeeid']]['freq'] += 1;
	// 					$count = $data[$value['employeeid']]['freq'];
	// 					foreach ($sanctions as $sanc_key => $sanc_count) {
	// 						if($sanc_count <= $count){
	// 							$total[$value['employeeid']] = $value['employeeid'];
	// 							$value['employeeid'] = '';
	// 							$count = 0;
	// 						}
	// 					}
	// 				}
	// 				$old_employeeid = $value['employeeid'];
	// 			}
	// 		}
	// 		return count($total);
	// 	}else{
	// 		if($q_late->num_rows > 0){
	// 			foreach($q_late->result_array() as $key => $value){
	// 				if($value['employeeid'] != $old_employeeid){
	// 					$data[$value['employeeid']]['employeeid'] = $value['employeeid'];
	// 					$data[$value['employeeid']]['department'] = $value['description'];
	// 					$data[$value['employeeid']]['fullname'] = $value['lname'].", ".$value['fname'].", ".$value['mname'];
	// 					$data[$value['employeeid']]['freq'] = 1;
	// 					$data[$value['employeeid']]['count'] = $this->attcompute->exp_time($value['late']);
	// 				}else{
	// 					$data[$value['employeeid']]['count'] += $this->attcompute->exp_time($value['late']);
	// 					if($data[$value['employeeid']]['freq']) $data[$value['employeeid']]['freq'] += 1;
	// 				}
	// 				$old_employeeid = $value['employeeid'];
	// 			}
	// 		}
	// 		return $data;
	// 	}
	// }

	public function getFrequency($code){
		$q_freq = $this->db->query("SELECT * FROM code_disciplinary_action_offense_type WHERE code = '$code'");
		if($q_freq->num_rows() > 0) return $q_freq->row()->frequency;
		else return false;
	}

	public function empWithExcessiveTardiness($fornotif=false, $month = "", $forMonthNotif=false, $year='',$department=''){
		$data = array();
		$total = array();
		$date_from = $date_to = date('Y-m-d');
		$count = 0;
		$monthlyCount = array();
		$old_employeeid = $old_date = '';
		$frequency = $this->getFrequency("ET");
		if($year){
			if($month){
				$date_from = $year."-".$month."-01";
				$date_to = $year."-".$month."-31";
			}else{
				$date_from = $year."-01-01";
				$date_to = $year."-12-31";
			}
		} 
		
		$q_date = $this->db->query("SELECT * FROM code_leave_setup WHERE leavetype = 'VL' LIMIT 1 ");
		// if($q_date->num_rows > 0){
		// 	$date_from = $q_date->row()->dfrom;
		// 	$date_to = $q_date->row()->dto;
		// }
		$sanctions = array();
		$sanctionToPunish = $this->extensions->getDisciplinarySanctions("TARDINESS");
		$sanctionToPunish = explode("/", $sanctionToPunish);
		foreach($sanctionToPunish as $key => $value){
			$sanc_data = explode("=", $value);
			$sanctions[$sanc_data[0]] = $sanc_data[1];
		}

		$where_clause = "";
		if($month) $where_clause .= " AND DATE_FORMAT(sched_date, '%m') = '$month' ";
		if($department) $where_clause .=" AND b.deptid = '$department'";
		$datenow = date("Y-m-d");
		$where_clause .= " AND (('$datenow' < b.dateresigned2 OR b.dateresigned2 = '0000-00-00' OR b.dateresigned2 = '1970-01-01' OR b.dateresigned2 IS NULL) AND b.isactive ='1')";
		$sanctions_type = '';

		$q_late = $this->db->query("SELECT * FROM employee_attendance_detailed a INNER JOIN employee b ON b.`employeeid` = a.`employeeid` INNER JOIN code_department c ON b.deptid = c.code WHERE sched_date BETWEEN '$date_from' AND '$date_to' AND (late <> '' AND late <> '0:00' AND late <> '0') $where_clause ORDER BY a.employeeid ");
		if($fornotif){
			// $q_late = $this->db->query("SELECT * FROM employee_attendance_detailed a INNER JOIN employee b ON b.`employeeid` = a.`employeeid` INNER JOIN code_department c ON b.deptid = c.code WHERE 1 AND sched_date BETWEEN '$date_from' AND '$date_to' AND (late <> '' AND late <> '0:00') $where_clause ORDER BY a.employeeid ");
			if($q_late->num_rows > 0){
				$old_employeeid = '';
				foreach($q_late->result_array() as $key => $value){
					if($value['employeeid'] != $old_employeeid) $sched = $this->attcompute->displaySched($value['employeeid'],$value['sched_date']);
					if($sched->num_rows() == 0 || (isset($sched->row()->flexible) && $sched->row()->flexible != 'YES')){
						if($month){
							if($value['employeeid'] != $old_employeeid){
								$count = 1;
							}else{
								if(date("F",strtotime($value['sched_date'])) == $old_date) $count+=1;
							}
						}else{
							foreach (Globals::monthList() as $ky => $val) {
								if(isset($monthlyCount[$value['employeeid']][$val])){
									if(date("F",strtotime($value['sched_date'])) == $val) $monthlyCount[$value['employeeid']][$val]+=1;
								}else{
									$monthlyCount[$value['employeeid']][$val] = 0;
									if(date("F",strtotime($value['sched_date'])) == $val) $monthlyCount[$value['employeeid']][$val]+=1;
								}
							}
						}
						if($month){
							if($count >= $frequency){
								$old_employeeid = '';
								$total[$value['employeeid']] = $count;
							}
						}

						$old_employeeid = $value['employeeid'];
						$old_date = date("F",strtotime($value['sched_date']));
					}
				}
			}

			foreach ($monthlyCount as $empid => $months) {
				foreach ($months as $month => $monthlyTardiness) {
					if($monthlyTardiness >= $frequency) $total[$empid.$month] = $monthlyTardiness;
				}
			}
			return count($total);
		}else{
			if($q_late->num_rows > 0){
				$old_employeeid = '';
				foreach($q_late->result_array() as $key => $value){
					if($value['employeeid'] != $old_employeeid) $sched = $this->attcompute->displaySched($value['employeeid'],$value['sched_date']);
					if($sched->num_rows() == 0 || (isset($sched->row()->flexible) && $sched->row()->flexible != 'YES')){
						if($value['employeeid'] != $old_employeeid){
							if($count < $frequency) unset($data[$old_employeeid]);
							$data[$value['employeeid']]['employeeid'] = $value['employeeid'];
							$data[$value['employeeid']]['department'] = $value['description'];
							$data[$value['employeeid']]['fullname'] = $value['lname'].", ".$value['fname'].", ".$value['mname'];
							$data[$value['employeeid']]['freq'] = 1;
							$data[$value['employeeid']]['count'] = $this->attcompute->exp_time($value['late']);
							$count = 1;
						}else{
							if(date("F",strtotime($value['sched_date'])) == $old_date){
								$data[$value['employeeid']]['count'] += $this->attcompute->exp_time($value['late']);
								if($data[$value['employeeid']]['freq']) $data[$value['employeeid']]['freq'] += 1;
								$count+=1;
							}
						}

						if($count >= $frequency){
							$old_employeeid = '';
							$total[$value['employeeid']] = $value['employeeid'];
						}

						$old_employeeid = $value['employeeid'];
						$old_date = date("F",strtotime($value['sched_date']));
					}
				}
			}
			return $data;
		}
	}
	
	
	// function empWithExcessiveAbsenteism($fornotif=false){
	// 	$data = array();
	// 	$total = array();
	// 	$date_from = $date_to = date('Y-m-d');
	// 	$count = 0;
	// 	$old_employeeid = '';
	// 	$q_date = $this->db->query("SELECT * FROM code_leave_setup WHERE leavetype = 'VL' LIMIT 1 ");
	// 	if($q_date->num_rows > 0){
	// 		$date_from = $q_date->row()->dfrom;
	// 		$date_to = $q_date->row()->dto;
	// 	}

	// 	$sanctions = array();
	// 	$sanctionToPunish = $this->extensions->getDisciplinarySanctions("ABSENTEISM");
	// 	$sanctionToPunish = explode("/", $sanctionToPunish);
	// 	foreach($sanctionToPunish as $key => $value){
	// 		$sanc_data = explode("=", $value);
	// 		$sanctions[$sanc_data[0]] = $sanc_data[1];
	// 	}

	// 	$q_absents = $this->db->query("SELECT * FROM employee_attendance_detailed a INNER JOIN employee b ON b.`employeeid` = a.`employeeid` INNER JOIN code_office c ON b.deptid = c.code WHERE sched_date BETWEEN '$date_from' AND '$date_to' AND (absents <> '' AND absents <> '0:00') ORDER BY a.employeeid ");
	// 	if($fornotif){	
	// 		if($q_absents->num_rows > 0){
	// 			foreach($q_absents->result_array() as $key => $value){
	// 				if($value['employeeid'] != $old_employeeid){
	// 					$data[$value['employeeid']]['employeeid'] = $value['employeeid'];
	// 					$data[$value['employeeid']]['department'] = $value['description'];
	// 					$data[$value['employeeid']]['fullname'] = $value['lname'].", ".$value['fname'].", ".$value['mname'];
	// 					$data[$value['employeeid']]['freq'] = 1;
	// 					$data[$value['employeeid']]['count'] = $this->attcompute->exp_time($value['absents']);
	// 				}else{
	// 					$data[$value['employeeid']]['count'] += $this->attcompute->exp_time($value['absents']);
	// 					if($data[$value['employeeid']]['freq']) $data[$value['employeeid']]['freq'] += 1;
	// 					$count = $data[$value['employeeid']]['freq'];
	// 					foreach ($sanctions as $sanc_key => $sanc_value) {
	// 						if($sanc_value <= $count){
	// 							$total[$value['employeeid']] = $value['employeeid'];
	// 							$value['employeeid'] = '';
	// 							$count = 0;
	// 						}
	// 					}
	// 				}
	// 				$old_employeeid = $value['employeeid'];
	// 			}
	// 		}
	// 		return count($total) + 1;
	// 	}else{
	// 		if($q_absents->num_rows > 0){
	// 			foreach($q_absents->result_array() as $key => $value){
	// 				if($value['employeeid'] != $old_employeeid){
	// 					$data[$value['employeeid']]['employeeid'] = $value['employeeid'];
	// 					$data[$value['employeeid']]['department'] = $value['description'];
	// 					$data[$value['employeeid']]['fullname'] = $value['lname'].", ".$value['fname'].", ".$value['mname'];
	// 					$data[$value['employeeid']]['freq'] = 1;
	// 					$data[$value['employeeid']]['count'] = $value['absents'];
	// 				}else{
	// 					$data[$value['employeeid']]['count'] += $value['absents'];
	// 					if($data[$value['employeeid']]['freq']) $data[$value['employeeid']]['freq'] += 1;
	// 				}
	// 				$old_employeeid = $value['employeeid'];
	// 			}
	// 		}
	// 		return $data;
	// 	}
	// }

	public function empWithExcessiveAbsenteism($fornotif=false, $month = "", $forMonthNotif="", $year='', $department=''){
		$data = array();
		$total = array();
		$date_from = $date_to = date('Y-m-d');
		$count = 0;
		$monthlyCount = array();
		$frequency = $this->getFrequency('EA');
		$old_employeeid = $old_date = '';
		if($year){
			if($month){
				$date_from = $year."-".$month."-01";
				$date_to = $year."-".$month."-31";
			}else{
				$date_from = $year."-01-01";
				$date_to = $year."-12-31";
			}
		} 
		$q_date = $this->db->query("SELECT * FROM code_leave_setup WHERE leavetype = 'VL' LIMIT 1 ");
		// if($q_date->num_rows > 0){
		// 	$date_from = $q_date->row()->dfrom;
		// 	$date_to = $q_date->row()->dto;
		// }


		$where_clause = "";
		if($month) $where_clause .= " AND DATE_FORMAT(sched_date, '%m') = '$month' ";
		if($department) $where_clause .=" AND b.deptid = '$department'";
		$datenow = date("Y-m-d");
		$where_clause .= " AND (('$datenow' < b.dateresigned2 OR b.dateresigned2 = '0000-00-00' OR b.dateresigned2 = '1970-01-01' OR b.dateresigned2 IS NULL) AND b.isactive ='1')";

		$sanctions = array();
		$sanctionToPunish = $this->extensions->getDisciplinarySanctions("ABSENTEISM");
		$sanctionToPunish = explode("/", $sanctionToPunish);
		foreach($sanctionToPunish as $key => $value){
			$sanc_data = explode("=", $value);
			$sanctions[$sanc_data[0]] = $sanc_data[1];
		}

		$q_absents = $this->db->query("SELECT * FROM employee_attendance_detailed a INNER JOIN employee b ON b.`employeeid` = a.`employeeid` INNER JOIN code_department c ON b.deptid = c.code WHERE sched_date BETWEEN '$date_from' AND '$date_to' AND (absents <> '' AND absents <> '0:00' ) $where_clause ORDER BY a.employeeid ");
		if($fornotif){	
			if($q_absents->num_rows > 0){
				$old_employeeid = '';
				foreach($q_absents->result_array() as $key => $value){
					if($value['employeeid'] != $old_employeeid) $sched = $this->attcompute->displaySched($value['employeeid'],$value['sched_date']);
					if($sched->num_rows() == 0 || (isset($sched->row()->flexible) && $sched->row()->flexible != 'YES')){
						if($month){
							if($value['employeeid'] != $old_employeeid){
								$count = 1;
							}else{
								if(date("F",strtotime($value['sched_date'])) == $old_date) $count+=1;
							}
						}else{
							foreach (Globals::monthList() as $ky => $val) {
								if(isset($monthlyCount[$value['employeeid']][$val])){
									if(date("F",strtotime($value['sched_date'])) == $val) $monthlyCount[$value['employeeid']][$val]+=1;
								}else{
									$monthlyCount[$value['employeeid']][$val] = 0;
									if(date("F",strtotime($value['sched_date'])) == $val) $monthlyCount[$value['employeeid']][$val]+=1;
								}
							}
						}
						if($month){
							if($count >= $frequency){
								$old_employeeid = '';
								$total[$value['employeeid']] = $value['employeeid'];
							}
						}

						$old_employeeid = $value['employeeid'];
						$old_date = date("F",strtotime($value['sched_date']));
					}
				}
			}
			foreach ($monthlyCount as $empid => $months) {
				foreach ($months as $month => $monthlyTardiness) {
					if($monthlyTardiness >= $frequency) $total[$empid.$month] = $monthlyTardiness;
				}
			}
			return count($total);
		}else{
			if($q_absents->num_rows > 0){
				$old_employeeid = '';
				foreach($q_absents->result_array() as $key => $value){
					if($value['employeeid'] != $old_employeeid) $sched = $this->attcompute->displaySched($value['employeeid'],$value['sched_date']);
					if($sched->num_rows() == 0 || (isset($sched->row()->flexible) && $sched->row()->flexible != 'YES')){
						if($value['employeeid'] != $old_employeeid){
							$data[$value['employeeid']]['employeeid'] = $value['employeeid'];
							$data[$value['employeeid']]['department'] = $value['description'];
							$data[$value['employeeid']]['fullname'] = $value['lname'].", ".$value['fname'].", ".$value['mname'];
							$data[$value['employeeid']]['freq'] = 1;
							$data[$value['employeeid']]['count'] = $value['absents'];
						}else{
							if(date("F",strtotime($value['sched_date'])) == $old_date){
								$data[$value['employeeid']]['count'] += $value['absents'];
							    if($data[$value['employeeid']]['freq']) $data[$value['employeeid']]['freq'] += 1;
								$count+=1;
							}
						}

						if($count >= $frequency){
							$total[$value['employeeid']] = $value['employeeid'];
						}

						$old_employeeid = $value['employeeid'];
						$old_date = date("F",strtotime($value['sched_date']));
					}
				}
			}
			// echo "<pre>"; print_r($data); die;
			return $data;
		}
	}

	public function getEmpSanction($emp, $offensecode, $month, $year){
		$res = $this->db->query("SELECT sanction_code FROM employee_disciplinary_action WHERE employeeid = '$emp' AND offense_code='$offensecode' AND month='$month' AND year='$year'")->result_array();
		return $res;
	}
	public function getEmployeeByDept($username=''){
		$officehead = $this->extensions->checkIfOfficeHead($username);
		$datenow = date("Y-m-d");
		$where_clause = '';
		$depthead = $this->extensions->checkIfDeptHead($username);
		if($depthead){ 
			$deptcodes = $this->extensions->getAllDepartmentUnder($username);
			$deptcodes = "'" . implode( "','", $this->db->escape($deptcodes) ) . "'";
			$where_clause .= " AND deptid IN ($deptcodes) " ;
		}
		if($officehead){ 
			$officecodes = $this->extensions->getAllOfficeUnder($username);
			$officecodes = "'" . implode( "','", $this->db->escape($officecodes) ) . "'";
			if($depthead) $where_clause .= " OR office IN ($officecodes) " ;
			else $where_clause .= " AND office IN ($officecodes) " ;
			
		}

		$where_clause .= " AND (('$datenow' < a.dateresigned2 OR a.dateresigned2 = '0000-00-00' OR a.dateresigned2 = '1970-01-01' OR a.dateresigned2 IS NULL) AND a.isactive ='1')";

		// return $this->db->query("SELECT employeeid , lname , fname , mname , emptype , deptid, positionid FROM employee WHERE 1 $where_clause ")->result();
		return $this->db->query("SELECT employeeid , lname , fname , mname, emptype , office, description, positionid, deptid FROM employee a INNER JOIN code_office b ON a.office = b.code  WHERE employeeid != '' $where_clause ")->result();
	}
	
	function displaySched($eid = "",$date = ""){
        $query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND NOT tardy_start < TIME('$date') ORDER BY editstamp DESC LIMIT 1")->result();
        return $query; 
    }
	
	//Added 06-13-2017 View Excessive Tardiness Details
	// function viewExcessiveTardinessDetails($employeeid = ""){
	// 	$data = array();
	// 	$date_from = $date_to = date('Y-m-d');
	// 	$total = 0;
	// 	$old_employeeid = '';
	// 	$q_date = $this->db->query("SELECT * FROM code_leave_setup WHERE leavetype = 'VL' LIMIT 1 ");
	// 	if($q_date->num_rows > 0){
	// 		$date_from = $q_date->row()->dfrom;
	// 		$date_to = $q_date->row()->dto;
	// 	}

	// 	$query = $this->db->query("SELECT * FROM employee_attendance_detailed a INNER JOIN employee b ON b.`employeeid` = a.`employeeid` INNER JOIN code_office c ON b.deptid = c.code WHERE sched_date BETWEEN '$date_from' AND '$date_to' AND (late <> '' AND late <> '0:00') AND a.employeeid = '$employeeid' ");
	// 	if($query->num_rows > 0){
	// 		foreach($query->result_array() as $key => $value){
	// 			$data[$value['sched_date']]['employeeid'] = $value['employeeid'];
	// 			$data[$value['sched_date']]['department'] = $value['description'];
	// 			$data[$value['sched_date']]['fullname'] = $value['lname'].", ".$value['fname'].", ".$value['mname'];
	// 			$data[$value['sched_date']]['dateTardy'] = date("F d, Y",strtotime($value['sched_date']));
	// 			$data[$value['sched_date']]['lateut'] = $this->attcompute->sec_to_hm($this->attcompute->exp_time($value['late']));
	// 		}
	// 	}
	// 	return $data;
 //    }

    public function viewExcessiveTardinessDetails($employeeid = "", $month = ""){
		$data = array();
		$date_from = $date_to = date('Y-m-d');
		$total = 0;
		$old_employeeid = '';

		$where_clause = "";
		if($month) $where_clause = " AND DATE_FORMAT(sched_date, '%m') = '$month' ";

		$query = $this->db->query("SELECT * FROM employee_attendance_detailed a INNER JOIN employee b ON b.`employeeid` = a.`employeeid` INNER JOIN code_department c ON b.deptid = c.code WHERE (late <> '' AND late <> '0:00') $where_clause AND a.employeeid = '$employeeid' ");
		if($query->num_rows > 0){
			foreach($query->result_array() as $key => $value){
				$data[$value['sched_date']]['employeeid'] = $value['employeeid'];
				$data[$value['sched_date']]['department'] = $value['description'];
				$data[$value['sched_date']]['fullname'] = $value['lname'].", ".$value['fname'].", ".$value['mname'];
				$data[$value['sched_date']]['dateTardy'] = date("F d, Y",strtotime($value['sched_date']));
				$data[$value['sched_date']]['lateut'] = $this->attcompute->sec_to_hm($this->attcompute->exp_time($value['late']));
			}
		}
		return $data;
    }
	
	//Added 06-13-2017 View Excessive Absenteism Details
	// function viewExcessiveAbsenteismDetails($employeeid = ""){
	// 	$data = array();
	// 	$date_from = $date_to = date('Y-m-d');
	// 	$total = 0;
	// 	$old_employeeid = '';
	// 	$q_date = $this->db->query("SELECT * FROM code_leave_setup WHERE leavetype = 'VL' LIMIT 1 ");
	// 	if($q_date->num_rows > 0){
	// 		$date_from = $q_date->row()->dfrom;
	// 		$date_to = $q_date->row()->dto;
	// 	}

	// 	$query = $this->db->query("SELECT * FROM employee_attendance_detailed a INNER JOIN employee b ON b.`employeeid` = a.`employeeid` INNER JOIN code_office c ON b.deptid = c.code WHERE sched_date BETWEEN '$date_from' AND '$date_to' AND (absents <> '' AND absents <> '0:00') AND a.employeeid = '$employeeid' ");
	// 	if($query->num_rows > 0){
	// 		foreach($query->result_array() as $key => $value){
	// 			$data[$value['sched_date']]['employeeid'] = $value['employeeid'];
	// 			$data[$value['sched_date']]['department'] = $value['description'];
	// 			$data[$value['sched_date']]['fullname'] = $value['lname'].", ".$value['fname'].", ".$value['mname'];
	// 			$data[$value['sched_date']]['dateAbsent'] = date("F d, Y",strtotime($value['sched_date']));
	// 		}
	// 	}
	// 	return $data;
 //    }

    public function viewExcessiveAbsenteismDetails($employeeid = "", $month = ""){
		$data = array();
		$date_from = $date_to = date('Y-m-d');
		$total = 0;
		$old_employeeid = '';

  		$where_clause = "";
		if($month) $where_clause = " AND DATE_FORMAT(sched_date, '%m') = '$month' ";

		$query = $this->db->query("SELECT * FROM employee_attendance_detailed a INNER JOIN employee b ON b.`employeeid` = a.`employeeid` INNER JOIN code_department c ON b.deptid = c.code WHERE (absents <> '' AND absents <> '0:00') $where_clause AND a.employeeid = '$employeeid' ");
		if($query->num_rows > 0){
			foreach($query->result_array() as $key => $value){
				$data[$value['sched_date']]['employeeid'] = $value['employeeid'];
				$data[$value['sched_date']]['department'] = $value['description'];
				$data[$value['sched_date']]['fullname'] = $value['lname'].", ".$value['fname'].", ".$value['mname'];
				$data[$value['sched_date']]['dateAbsent'] = date("F d, Y",strtotime($value['sched_date']));
			}
		}
		return $data;
    }

    public function latestDetailedAttendance(){
    	$query = $this->db->query("SELECT DATE_FORMAT(sched_date, '%m') AS latest_month FROM employee_attendance_detailed ORDER BY sched_date DESC LIMIT 1");
    	if($query->num_rows() > 0) return $query->row()->latest_month;
    	else return false;
	}
	
	function getCutoffList(){
		$result=array();
		$q = $this->db->query("SELECT startdate,enddate FROM payroll_cutoff_config ORDER BY startdate")->result();
		$result = $q;
		return $result;
	}

} //endoffile
