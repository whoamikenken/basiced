<?php 
	
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Seminar extends CI_Model {

		public function checkExistingSeminarApp($employeeid='',$datesetfrom='',$datesetto=''){
			return $this->db->query("SELECT * FROM seminar_app a INNER JOIN seminar_app_emplist b ON b.`base_id`=a.`id`
							 WHERE b.employeeid='$employeeid' 
							 AND (('$datesetfrom' BETWEEN datesetfrom AND datesetto) OR ('$datesetto' BETWEEN datesetfrom AND datesetto))")->num_rows();
			
		}

		/**
		* Inserts new seminar application in base table and gets last inserted id.
		*
		* @return int
		*/
	    public function insertBaseSeminarApp($data){
	    	$id = "";
	    	unset($data["base_id"]);
	    	$res = $this->db->insert("seminar_app", $data);
	    	if($res)  	$id = $this->db->insert_id();
	    	return $id;
	    }

		public function saveLeaveAppHRDirect($base_id, $teachingtype, $data){
			$empcount = $isread = 0;
	    	$arr_data_failed = array();
			$isread = 0;

			$res = $this->db->query("
				INSERT INTO seminar_app_emplist (base_id, employeeid, teachingtype, status, isread) VALUES ('$base_id', '{$data["employeeid"]}', '$teachingtype', 'APPROVED','$isread')
			");

			$leave_id = $this->db->insert_id();

			if($leave_id){
				$insert_q = $this->db->query("
										INSERT INTO seminar_request (id,employeeid,datesetfrom,datesetto,timefrom,timeto,category,seminar,title,organizer,venue,location,fee,deadline,remarks,status,date_applied)
										 (SELECT id,a.employeeid,b.datesetfrom,b.datesetto,b.timefrom,b.timeto,b.category,b.seminar,b.title,b.organizer,b.venue,b.location,b.fee,b.deadline,b.remarks,a.status,b.date_applied
											FROM seminar_app_emplist a
											INNER JOIN seminar_app b ON a.`base_id`=b.`id`
											 WHERE a.id='$leave_id');

									");
				$id = $this->db->insert_id();
				if($id) $empcount++;
			}

			if($res) $empcount++;
			else array_push($arr_data_failed, $employeeid);

			return array($empcount,$arr_data_failed);

	    }

	    public function getAppSequence($type=""){
	    	$res = $this->db->query("SELECT * FROM code_request_form WHERE code_request='$type'");
	    	return $res;
	    }

	    public function insertSeminarAppEmpList($base_id, $user, $teachingType, $dstatus, $ddate){
	    	$empcount = $isread = 0;
	    	$arr_data_failed = array();
			$isread = 1;

			$res = $this->db->query("
				INSERT INTO seminar_app_emplist (base_id, employeeid, teachingType, dstatus, ddate, isread) VALUES ('$base_id', '$user', '$teachingType','$dstatus', '$ddate', '$isread')
			");

			if($res) $empcount++;
			else array_push($arr_data_failed, $user);

			return array($empcount,$arr_data_failed);
	    }

	    public function getEmpSeminarHistory($employeeid="", $status="", $seminarid="", $isread=''){
	    	$wC = "";
	    	if($status)				 $wC .= " AND a.`status`='$status'";
	    	if($seminarid)			 $wC .= " AND a.id='$seminarid'";
	        $res = $this->db->query("SELECT a.id AS seminarid, a.*,b.* ,REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname
	        							FROM seminar_app_emplist a
										INNER JOIN seminar_app b ON a.`base_id`=b.`id`
										INNER JOIN employee c ON a.employeeid=c.employeeid
										WHERE a.employeeid='$employeeid' 
										$wC")->result();
	        return $res;
		}
	
	    public function getAppSequencePerSeminar($seminarid=''){
	    	$res = $this->db->query("SELECT * FROM seminar_app_emplist a INNER JOIN seminar_app b ON a.`base_id`=b.`id` WHERE a.id='$seminarid'");
	    	return $res;
	    }

		public function getSeminarDetails($seminarid='',$colstatus=''){
			$data = array();
			$res = $this->db->query("SELECT a.id AS seminarid, a.employeeid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname,e.description AS epos, c.deptid, d.description AS edept, a.*,b.* 
										FROM seminar_app_emplist a
										INNER JOIN seminar_app b ON a.`base_id`=b.`id`
										INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
										LEFT JOIN code_position e ON c.positionid = e.positionid
										LEFT JOIN code_office d ON c.deptid = d.code 
										WHERE a.id='$seminarid'");

			if($res->num_rows() > 0){
				foreach ($res->result() as $obj) {
					$data['base_id'] 				= $obj->base_id;
					$data['seminarid'] 				= $obj->seminarid;
					$data['applied_by'] 			= $obj->applied_by;
					$data['deptid'] 				= $obj->deptid;
					$data['epos'] 					= $obj->epos;
					$data['fullname'] 				= $obj->fullname;
					$data['datesetfrom'] 			= $obj->datesetfrom;
					$data['datesetto'] 				= $obj->datesetto;
					$data['timefrom'] 				= $obj->timefrom;
					$data['timeto'] 				= $obj->timeto;
					$data['category'] 				= $obj->category;
					$data['category'] 				= $obj->category;
					$data['seminar'] 				= $obj->seminar;
					$data['title'] 					= $obj->title;
					$data['organizer'] 				= $obj->organizer;
					$data['venue'] 					= $obj->venue;
					$data['location'] 				= $obj->location;
					$data['fee'] 					= $obj->fee;
					$data['deadline'] 				= $obj->deadline;
					$data['remarks']  				= $obj->remarks;
					$data['date_applied']			= $obj->date_applied;
					if($colstatus) 	$data['colstat']= $obj->$colstatus; 
	  			}
			}
			return $data;
		}

		public function modifySeminarDetails($update_data, $base_id){
			unset($update_data["base_id"]);
	    	$this->db->set($update_data);
	    	$this->db->where("id", $base_id);
	    	$res = $this->db->update("seminar_app");
	    	if($res) return 1;
	    	else return 0;
	    }

		public function deleteSeminarApp($id){
	        $return = "";
	        $query = $this->db->query("SELECT id, dstatus FROM seminar_app_emplist WHERE id='$id' 
	        								AND (dstatus='APPROVED' 
	        									OR cstatus='APPROVED' 
	        									OR hrstatus='APPROVED' 
	        									OR cpstatus='APPROVED' 
	        									OR fdstatus='APPROVED' 
	        									OR bostatus='APPROVED' 
	        									OR pstatus='APPROVED' 
	        									OR upstatus='APPROVED' 
	        									OR `status`='DISAPPROVED')");
	        if($query->num_rows() > 0){
	            $return = "Failed to delete!. The request is already ".$query->row()->dstatus;
	        }else{
	            $query = $this->db->query("DELETE FROM seminar_app_emplist WHERE id='$id'");
	            if($query)  $return = "Successfully Deleted!.";
	            else 		$return = "Failed to delete.";
	            
	        }
	        return $return;
	    }

	    public function getSeminarAppListToManage($user="", $colhead="", $colstatus='', $status="", $prevcolstatus='',$datefrom="", $dateto="",$teachingType='',$code_request='',$seq_count=''){

			if($code_request){
				$code_request = str_replace('NON','',$code_request);
				$code_request = str_replace('HEAD','',$code_request);
			}

			$colseq =  $colhead ? (substr($colhead,0,-4) . 'seq') : '';
			$sample =  $colhead ? (substr($colhead,0,-4) . 'status') : '';


			$wC = "";
	    	if($datefrom && $dateto) $wC .= " AND (b.`datesetfrom` BETWEEN '$datefrom' AND '$dateto' OR b.`datesetto` BETWEEN '$datefrom' AND '$dateto')";
	    	if($status)			 	 $wC .= " AND $colstatus='$status'";
			if($colseq)			 	 $wC .= " AND $colseq!='0'";
			if($seq_count)			 $wC .= " AND $colseq='$seq_count'";
	    	if($prevcolstatus) 	 	 $wC .= " AND $prevcolstatus='APPROVED'";
	    	if($teachingType) 	 	 $wC .= " AND c.teachingType='$teachingType'";

			if($status == "PENDING"){
				$res = $this->db->query("SELECT a.id AS aid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname, c.deptid, a.*,b.* 
								FROM seminar_app_emplist a
								INNER JOIN seminar_app b ON a.`base_id`=b.`id`
								INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
								WHERE $colhead='$user' $wC");
			}else{
				$res = $this->db->query("SELECT a.id AS aid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname, c.deptid, a.*,b.* 
								FROM seminar_app_emplist a
								INNER JOIN seminar_app b ON a.`base_id`=b.`id`
								INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
								WHERE $colhead='$user' AND $sample = 'APPROVED' AND (b.`datesetfrom` BETWEEN '$datefrom' AND '$dateto' OR b.`datesetto` BETWEEN '$datefrom' AND '$dateto') ");
			}
			return $res;
		}

		public function sortApprovalSeq($setup){
			$this->load->model('employee');
			$this->load->model('utils');
			$user = $this->session->userdata('username');
			$deptid = $this->employee->getempdatacol('deptid');

			$chead = $dhead = $cphead = $dphead = '';

			$isClusterHead = $isDeptHead = $isCpHead = $isDpHead = false;
			$isCluster_q = $this->db->query("SELECT code FROM code_office WHERE divisionhead='$user'");
			if($isCluster_q->num_rows() > 0) $isClusterHead = true;
			$isHead_q = $this->db->query("SELECT code FROM code_office WHERE head='$user'");
			if($isHead_q->num_rows() > 0) $isDeptHead = true;
			$isCp_q = $this->db->query("SELECT code FROM code_campus WHERE campus_principal='$user'");
			if($isCp_q->num_rows() > 0) $isCpHead = true;
			$isDp_q = $this->db->query("SELECT code FROM code_department WHERE head='$user'");
			if($isDp_q->num_rows() > 0) $isDpHead = true;

			if($isClusterHead) 	$chead = $user;
			if($isDeptHead) 	$dhead = $user;
			if($isCpHead) 		$cphead = $user;
			if($isDpHead) 		$dphead = $user;

			/*$dhead = $this->overtime->getDeptHead('head',		$deptid);	
			$chead = $this->overtime->getDeptHead('divisionhead',$deptid);*////< user must be divisionhead of his own department to be counted as cluster head
			$hrhead = $this->utils->getDeptHead('head',		'HR');


			$arr_aprvl_seq = array();
			$arr_aprvl_seq[ $setup->dhseq ] = array('position'=>'dhead' , 'head_id'=>$dhead);
			$arr_aprvl_seq[ $setup->chseq ] = array('position'=>'chead' , 'head_id'=>$chead);
			$arr_aprvl_seq[ $setup->hhseq ] = array('position'=>'hrhead', 'head_id'=>$hrhead);
			$arr_aprvl_seq[ $setup->cpseq ] = array('position'=>'cphead', 'head_id'=>$cphead);
			$arr_aprvl_seq[ $setup->dpseq ] = array('position'=>'dphead', 'head_id'=>$dphead);
			$arr_aprvl_seq[ $setup->fdseq ] = array('position'=>'fdhead', 'head_id'=>$setup->financedir);
			$arr_aprvl_seq[ $setup->boseq ] = array('position'=>'bohead', 'head_id'=>$setup->budgetoff);
			$arr_aprvl_seq[ $setup->pseq  ] = array('position'=>'phead' , 'head_id'=>$setup->president);
			$arr_aprvl_seq[ $setup->upseq ] = array('position'=>'uphead', 'head_id'=>($setup->univphy . 
												($setup->univphyt <> ""?(",".$setup->univphyt):"")));
			//unset 0 , those not included in sequence
			unset($arr_aprvl_seq['0']);

			//ksort
			ksort($arr_aprvl_seq);
			return $arr_aprvl_seq;
		}

		function updateSeminarAppBaseData($datesetfrom,$datesetto,$timefrom,$timeto,$base_id){
			$q_update = $this->db->query("UPDATE seminar_app SET datesetfrom = '$datesetfrom', datesetto = '$datesetto', timefrom = '$timefrom', timeto = '$timeto' WHERE id = '$base_id' ");
			if($q_update) return true;
			else return false;
		}

		function saveSeminarStatusChange($user='', $seminarid='', $status='',$colstatus='',$coldate='',$colhead='',$isLastApprover='',$base_id='',$prev_colhead='', $endorse = ''){
			$res = $prev_wC ='';
			$return = '';

			if($prev_colhead) $prev_wC = " AND $prev_colhead='$user'";
			$test_q = $this->db->query("SELECT a.id FROM seminar_app_emplist a INNER JOIN seminar_app b ON b.id=a.base_id WHERE a.id='$seminarid' AND $colhead='$user' $prev_wC");
			if($test_q->num_rows() > 0){

				if($status == 'APPROVED' && $isLastApprover){
					$leave_q = $this->db->query("SELECT a.employeeid,b.datesetfrom,b.datesetto FROM seminar_app_emplist a INNER JOIN seminar_app b ON a.`base_id`=b.`id` WHERE a.id='$seminarid'");

					$ishalfday = 0;
					$sched_affected = array();
					$sched_affected_string = '';
					$datesetfrom = $datesetto = $employeeid = '';


					if($leave_q->num_rows() > 0){
						$l_q = $leave_q->row(0);
					    $employeeid 			= $l_q->employeeid;
					    $datesetfrom 			= $l_q->datesetfrom;
					    $datesetto 				= $l_q->datesetto;
					}
					if($ishalfday && $sched_affected_string && $datesetfrom) $sched_affected = explode(',', $sched_affected_string);


					///< check for existing applications
/*					$exist_app = $this->checkExistingSeminarApp($employeeid,'APPROVED',$datesetfrom,$datesetto);
					if($exist_app) {return array('err_code'=>0,'msg'=>'Employee already have approved applications for this date.');}*/

					$ltypetemp = $ltype;

					$insert_q = $this->db->query("
									INSERT INTO seminar_request (aid,employeeid,datesetfrom,datesetto,timefrom,timeto,category,seminar,title,organizer,venue,location,fee,deadline,remarks,status,date_applied,dateapproved)
									 (SELECT a.id,a.employeeid,b.datesetfrom,b.datesetto,b.timefrom,b.timeto,b.category,b.seminar,b.title,b.organizer,b.venue,b.location,b.fee,b.deadline,b.remarks,'$status',b.date_applied, CURRENT_DATE
										FROM seminar_app_emplist a
										INNER JOIN seminar_app b ON a.`base_id`=b.`id`
										 WHERE a.id='$seminarid');

								");

					if($insert_q) $this->db->query("UPDATE seminar_app_emplist SET isread='0' WHERE id='$seminarid'");
					else return array('err_code'=>0,'msg'=>"Failed to save.");

				}


				if($status == 'DISAPPROVED' || $isLastApprover){
					$res = $this->db->query("UPDATE seminar_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE, status='$status' WHERE id='$seminarid'");
					$this->db->query("UPDATE seminar_app_emplist SET isread='0' WHERE id='$seminarid'");

					if($res){
						if($endorse) $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$endorse);
						else $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$status);
					}

				}else{
					$res = $this->db->query("UPDATE seminar_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE WHERE id='$seminarid'");
						if($endorse) $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$endorse);
						else $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$status);
				}


			} 

			return $return;
		}

		public function checkInhouseSeminar($id){
			return $this->db->query("SELECT * FROM inhouse_seminar WHERE id = '$id' ")->num_rows();
		}

		public function addInhouseSeminar($data){
			unset($data["id"]);
			return $this->db->insert("inhouse_seminar", $data);
		}

		public function updateInhouseSeminar($data){
			$this->db->where("id", $data["id"]);
			$this->db->set($data);
			return $this->db->update("inhouse_seminar");
		}

		public function checkIfExisting($inhouse_seminar=''){
			return $this->db->query("SELECT * FROM inhouse_seminar WHERE username = '$inhouse_seminar'")->result_array();
		}

		public function isSeminarToday($datetoday, $user="", $year="", $deptid='', $office=''){
			$where_clause = $wc = "";
			if($user) $where_clause = " WHERE employeeid = '$user'";
			// if($year) $wc = " AND attendees LIKE '%$year%'";
			$curr_date = date("Y-m-d", strtotime($this->extensions->getServerTime()));
			if($deptid && $office) $wc .= " AND (FIND_IN_SET (attendeesDept, '$deptid') OR FIND_IN_SET (attendeesOffice, '$office'))";
			else if($deptid && !$office) $wc .= " AND FIND_IN_SET (attendeesDept, '$deptid') ";
			else if (!$deptid && $office) $wc .= " AND FIND_IN_SET (attendeesOffice, '$office')";
			if($user) $wc .= " AND FIND_IN_SET (employees, '$user')";
			return $this->db->query("SELECT * FROM `inhouse_seminar` WHERE ((date_from BETWEEN '$curr_date' AND '$datetoday') AND (date_to BETWEEN '$curr_date' AND '$datetoday')) AND id NOT IN (SELECT base_id FROM seminar_attendees $where_clause) $wc ");
		}

		function seminarDetails($seminarid=''){
			$where_clause='';
			if($seminarid) $where_clause = "WHERE a.id = '$seminarid'";
			return $this->db->query("SELECT a.*, b.level, b.Description, c.id as online_id FROM inhouse_seminar a INNER JOIN reports_item b ON a.workshop = b.ID LEFT JOIN user_gate_history c ON a.username = c.username $where_clause")->result_array();
		}

		public function seminarAnnouncement($datetoday, $user="", $deptid='', $office=''){
			$where_clause =  $wc = "";
			$curr_date = date("Y-m-d", strtotime($this->extensions->getServerTime()));
			// if($user) $where_clause = "WHERE employeeid = '$user'";
			if($deptid && $office) $wc .= " AND (FIND_IN_SET (attendeesDept, '$deptid') OR FIND_IN_SET (attendeesOffice, '$office'))";
			else if($deptid && !$office) $wc .= " AND FIND_IN_SET (attendeesDept, '$deptid') ";
			else if (!$deptid && $office) $wc .= " AND FIND_IN_SET (attendeesOffice, '$office')";
			if($user) $wc .= " AND FIND_IN_SET (employees, '$user')";
			return $this->db->query("SELECT * FROM `inhouse_seminar` WHERE ('$datetoday' BETWEEN date_from AND date_to OR '$curr_date' BETWEEN date_from AND date_to) AND id NOT IN (SELECT base_id FROM seminar_attendees $where_clause) $wc  ");
		}

		public function saveSeminarPoll($data){
			return $this->db->insert("seminar_attendees", $data);
		}

		public function seminarPollAttendees($id){
			return $this->db->query("SELECT * FROM seminar_attendees WHERE base_id ='$id' ")->result_array();
		}

		public function seminarAttendeesList($where_clause){
			return $this->db->query("SELECT a.employeeid, REPLACE(CONCAT(a.lname,', ',a.fname,' ',a.mname), 'Ã‘', 'Ñ') AS fullname, b.description, c.reason, c.id, c.isread, c.base_id as seminarid, e.level, c.isgoing, d.location FROM employee a INNER JOIN code_office b ON b.code = a.office INNER JOIN seminar_attendees c ON c.employeeid = a.employeeid LEFT JOIN inhouse_seminar d ON c.base_id = d.id LEFT JOIN reports_item e ON d.workshop = e.ID WHERE base_id != '' $where_clause")->result_array();
		}

		public function getSchoolYear($orderby=''){
			return $this->db->query("SELECT * FROM school_year $orderby")->result_array();
		}

		public function getAttendedEmployee($category = '', $seminarid=''){
			$wC = '';
			if($category) $wC .= " AND d.level = '$category'";
			if($seminarid && $seminarid != 'undefined') $wC .= " AND c.id = '$seminarid'";
			return $this->db->query("SELECT a.userid, REPLACE(CONCAT(b.lname,', ',b.fname,' ',b.mname), 'Ã‘', 'Ñ') AS fullname, a.localtimein, a.log_type, c.id FROM inhouse_seminar_timesheet a INNER JOIN employee b ON a.userid = b.employeeid INNER JOIN inhouse_seminar c ON c.username = a.username INNER JOIN reports_item d ON c.workshop = d.ID WHERE 1 $wC ORDER BY c.id, a.userid")->result_array();
		}

		public function getSYMonth($year){
			$query = $this->db->query("SELECT * FROM school_year WHERE sy = '$year'")->row();
			return array($query->month_from, $query->month_to);
		}

		public function attendeesMarkread($id){
			return $this->db->query("UPDATE seminar_attendees SET isread = '1' WHERE id = '$id' ");
		}

		public function attendeesAdminNotifCount(){
			return $this->db->query("SELECT * FROM seminar_attendees WHERE isread = '0' ")->num_rows();
		}

		public function getSeminarAttendees($query){
			return $this->db->query($query)->result_array();
		}

		public function getSeminarWithinSY($startYear='', $endYear='', $attendees='', $month_from='', $month_to='', $month=''){
			// if($month) $month_from = $month_to = $month;
			$startDate = $startYear."-".$month_from."-01";
			$endDate = $endYear."-".$month_to."-31";
			return $this->db->query("SELECT * FROM inhouse_seminar WHERE date_from BETWEEN '$startDate' AND '$endDate' ORDER BY date_from")->result_array();
		}

		public function getAttendedEmployeeList($sortby, $status, $employees = ''){
			$orderby = '';
			$where_clause = 'WHERE 1 ';
			$datenow = date('Y-m-d');
			if($status != "all" && $status != ''){
		        if($status=="1"){
		          $where_clause .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
		        }
		        if($status=="0"){
		          $where_clause .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
		        }
		        if(is_null($status)) $where_clause .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
		     }
		    if($employees && !is_array($employees)) $where_clause .= " AND employeeid IN ($employees)"; 
			$this->db->query("UPDATE employee set office = NULL WHERE office = ''");
			if($sortby == "Office"){
				$orderby = " ORDER BY case when office is null then 1 else 0 end, office, lname ";
			}
	        else{
	        	$orderby = " ORDER BY lname";
	        }
	        return $this->db->query("SELECT dateemployed, employeeid, office, REPLACE(CONCAT(lname,', ',fname,' ',mname), 'Ã‘', 'Ñ') AS fullname FROM employee $where_clause AND employeeid <> '' $orderby")->result_array();
		}

		// public function getEmployeeSeminarAttendance($username, $employeeid){
		// 	$query = $this->db->query("SELECT * FROM seminar_timesheet WHERE userid = '$employeeid' AND username = '$username'")->row();
		// 	return array($query->timein, $query->timeout);
		// }

		public function getEmployeeSeminarAttendance($username, $employeeid){
			$queryIN = $this->db->query("SELECT localtimein FROM inhouse_seminar_timesheet WHERE userid = '$employeeid' AND username = '$username' AND log_type = 'IN'")->row();
			$queryOUT = $this->db->query("SELECT localtimein FROM inhouse_seminar_timesheet WHERE userid = '$employeeid' AND username = '$username' AND log_type = 'OUT'")->row();
			return array($queryIN->localtimein, $queryOUT->localtimein);
		}

		public function getLeavetypeAndColspan($empid, $dfrom, $dto){
			$leaveQuery = $this->db->query("SELECT * FROM leave_request a INNER JOIN code_request_form b ON a.leavetype = b.code_request WHERE a.status = 'APPROVED' AND (a.fromdate BETWEEN 'dfrom' AND '$dto') AND (a.todate BETWEEN '$dfrom' AND '$dto') AND employeeid = '$empid'")->row();
			$datefrom = $leaveQuery->fromdate;
			$dateto = $leaveQuery->todate;
			$colspanQuery = $this->db->query("SELECT * FROM inhouse_seminar WHERE (date_from BETWEEN '$datefrom' AND '$dateto') AND (date_to BETWEEN '$datefrom' AND '$dateto')")->num_rows();
			return array($leaveQuery->description, $colspanQuery);


		}

	}