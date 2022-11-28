<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave extends CI_Model {

	function getRequestType($code='',$isleave='',$ismain=''){
		$wC = '';
		$cond = $ret = array();
		if($code) 			array_push($cond,"code_request = '$code'");
		if($isleave != '') 	array_push($cond,"is_leave = '$isleave'");
		if($ismain != '') 	array_push($cond,"ismain = '$ismain'");
		if(sizeof($cond) > 0) {
			$wC = implode(' AND ', $cond);
			$wC = 'WHERE ' . $wC;
		}
		$res = $this->db->query("SELECT code_request, description FROM code_request_form $wC");
		if($res->num_rows() > 0){
			foreach ($res->result() as $key => $row) {
				$code_r = $row->code_request;
	            $pos = strpos($code_r, 'NON');
	            $pos1 = strpos($code_r, 'HEAD');
	            if($pos==false && $pos1==false){
					$ret[Globals::_e($row->code_request)] = Globals::_e($row->description);
				}
			}
		}
		return $ret;
	}

	/**
	 * Query to Get distinct days of week of an employee's schedule
	 *
	 * @param string $employeeid (Default: "")
	 *
	 * @return array
	 */

	function getEmployeeSchedDays($employeeid="",$dateactive=""){
		$schedDays = array();
		$wC = '';

		if($dateactive) $wC .= " AND DATE(dateactive) <= DATE(DATE_SUB('$dateactive',INTERVAL 1 DAY))";

		$res = $this->db->query("
									SELECT * FROM (SELECT *
									FROM employee_schedule_history 
									WHERE employeeid='$employeeid'  
									$wC
									ORDER BY dateactive DESC)
									AS tmptable GROUP BY idx 
							");

		if($res->num_rows() > 0) {
			foreach ($res->result() as $key => $row) {
				$day = $row->dayofweek;
				$starttime = $row->starttime;
				$endtime = $row->endtime;

				if($starttime != '00:00:00' && $endtime != '00:00:00' && $row->no_schedule != '1'){
					if(!in_array($day, $schedDays)) array_push($schedDays, $day);
				}
			}
		}
		return $schedDays;
	}

	function getEmployee_Schedule($employeeid="",$dateactive=""){
		$schedDays = array();
		$wC = '';

		if($dateactive) $wC .= " AND DATE(dateedit) <= DATE(DATE_SUB('$dateactive',INTERVAL 1 DAY))";

		$res = $this->db->query("
									SELECT * FROM (SELECT *
									FROM employee_schedule 
									WHERE employeeid='$employeeid'  
									$wC
									ORDER BY dateedit DESC)
									AS tmptable GROUP BY idx 
							");

		if($res->num_rows() > 0) {
			foreach ($res->result() as $key => $row) {
				$day = $row->dayofweek;
				$starttime = $row->starttime;
				$endtime = $row->endtime;

				if($starttime != '00:00:00' && $endtime != '00:00:00' && $row->no_schedule != '1'){
					if(!in_array($day, $schedDays)) array_push($schedDays, $day);
				}
			}
		}
		return $schedDays;
	}
	
	/**
	 * Query to Get employee schedule with given date
	 *
	 * @param string $date (Default: "")
	 * @param string $employeeid (Default: "")
	 *
	 * @return array
	 */

	function getEmployeeSchedule($employeeid='',$date=''){
		$wc = "";
        $datepos = $this->extensions->getDatePosition($employeeid);
        $latestda = date('Y-m-d', strtotime($this->extensions->getLatestDateActive($employeeid)));
        $weekOfMonth = $this->attcompute->weekOfMonth($date);
        // echo "<pre>"; print_r($weekOfMonth); die;
        if($weekOfMonth == 0){
          $weekOfMonth = "1";
        }

        if($date >= $latestda) $wc .= " AND DATE(dateactive) = DATE('$latestda')";

		$query = $this->db->query("SELECT dateactive FROM employee_schedule_history WHERE employeeid = '$employeeid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE('$date') >= DATE('$datepos') $wc ORDER BY dateactive DESC,starttime DESC LIMIT 1;");
		if($query->num_rows() > 0){
            $da = $query->row(0)->dateactive;
            $query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$employeeid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE_FORMAT(dateactive,'%Y-%m-%d %H') = DATE_FORMAT('$da','%Y-%m-%d %H') AND ('$weekOfMonth' IN (SELECT weekly_sched FROM employee_schedule_history WHERE employeeid = '$employeeid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE_FORMAT(dateactive,'%Y-%m-%d %H') = DATE_FORMAT('$da','%Y-%m-%d %H')) OR (weekly_sched = 'weekly' OR weekly_sched = '' OR weekly_sched IS NULL)) GROUP BY starttime,endtime ORDER BY starttime;"); 
        }
        return $query;
	}

	function getEmployeeScheduleLeave($employeeid='',$date=''){
		$query = $this->db->query("SELECT dateedit FROM employee_schedule WHERE employeeid = '$employeeid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateedit) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) ORDER BY dateedit DESC,starttime DESC LIMIT 1;");
		if($query->num_rows() > 0){
            $da = $query->row(0)->dateedit;
            $query = $this->db->query("SELECT * FROM employee_schedule WHERE employeeid = '$employeeid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateedit) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE_FORMAT(dateedit,'%Y-%m-%d %H') = DATE_FORMAT('$da','%Y-%m-%d %H') GROUP BY starttime,endtime ORDER BY starttime;"); 
        }
        return $query;
	}

	/**
	 * Query to get attached invitation in seminar application
	 *
	 * @param int $base_id (Default: 0)
	 *
	 * @return stdClass Object
	 */

	function getSeminarInvitation($base_id = 0){
		$res = "";
		if($base_id <> 0) {
			$res = $this->db->query("SELECT * FROM seminar_app_attach_invitation WHERE base_id='$base_id'");
		}
		return $res;
	}

	/**
	 * Query to get attached file.
	 *
	 * @param int $base_id (Default: 0)
	 *
	 * @return stdClass Object
	 */

	function getUploadedFile($tablename="", $base_id = 0){
		$res = "";
		if($base_id <> 0 && $tablename <>'') {
			$res = $this->db->query("SELECT * FROM $tablename WHERE base_id='$base_id'");
		}
		return $res;
	}

	/**
	 * This function is for HR management which inserts new leave applications directly to leave_request w/o passing through approve heads. 
	 *
	 * @param 
	 *
	 * @return int
	 */
	function saveLeaveHRDirect($arr_emplist='', $hrhead='', $datefrom='', $dateto='', $ltype='', $othleave='', $ndays='', $withpay='', $reason='' ){
		///< for each emp
		///< check credit if balance > 0
		///< save to leave app and leave request
		///< update employee_leave_credit

		$credit_q = array(); $toInsert = false; $count = 0;
		foreach ($arr_emplist as $employeeid) {
			$query = $this->db->query("SELECT * FROM leave_app WHERE employeeid='$employeeid' AND status='APPROVED' AND (('$datefrom' BETWEEN datefrom AND dateto) OR ('$dateto' BETWEEN datefrom AND dateto))");
			if($query->num_rows() == 0){
					if($ltype <> 'other'){
							$credit_q  = $this->getEmployeeLeaveCredit($employeeid, $ltype, 'CURRENT_DATE');
							if($ltype == 'VL'){
									if($credit_q){
										if($credit_q[$employeeid]['balance'] > 0){
											$res = $this->insertToLeaveRequestHRDirect($credit_q, false, $employeeid, $hrhead, $datefrom, $dateto, $ltype, $othleave, $ndays, $withpay, $reason );
											if($res) $count++;
										}else{
											$credit_q  = $this->getEmployeeLeaveCredit($employeeid, 'EL', 'CURRENT_DATE');
											if($credit_q){
												if($credit_q[$employeeid]['balance'] > 0){
													$res = $this->insertToLeaveRequestHRDirect($credit_q, true, $employeeid, $hrhead, $datefrom, $dateto, $ltype, $othleave, $ndays, $withpay, $reason );
													if($res) $count++;
												}
											}
										}
									}else{
										$credit_q  = $this->getEmployeeLeaveCredit($employeeid, 'EL', 'CURRENT_DATE');
										if($credit_q){
											if($credit_q[$employeeid]['balance'] > 0){
												$res = $this->insertToLeaveRequestHRDirect($credit_q, true, $employeeid, $hrhead, $datefrom, $dateto, $ltype, $othleave, $ndays, $withpay, $reason );
												if($res) $count++;
											}
										}
									}
							}else{
									if($credit_q){
										if($credit_q[$employeeid]['balance'] > 0){
											$res = $this->insertToLeaveRequestHRDirect($credit_q, false, $employeeid, $hrhead, $datefrom, $dateto, $ltype, $othleave, $ndays, $withpay, $reason );
											if($res) $count++;
										}
									}
							}
					}else{
							$res = $this->insertToLeaveRequestHRDirect('', false, $employeeid, $hrhead, $datefrom, $dateto, $ltype, $othleave, $ndays, $withpay, $reason );
							if($res) $count++;
					}
			}

		}
		return $count;
	}

	function insertToLeaveRequestHRDirect($credit_q='', $forEL=false, $employeeid='', $hrhead='', $datefrom='', $dateto='', $ltype='', $othleave='', $ndays='', $withpay='', $reason='' ){
		//insert
		$res = $this->db->query("INSERT INTO leave_app (employeeid, type, other, paid, datefrom, dateto, nodays, reason, status, hrdir, hrdirdate, hrdirstatus, hhseq, isread) 
								 				VALUES ('$employeeid', '$ltype', '$othleave', '$withpay', '$datefrom', '$dateto', '$ndays', '$reason', 'APPROVED', '$hrhead', CURRENT_DATE, 'APPROVED', '1', 0)
								");
		if($res) $base_id = $this->db->insert_id();
		if($base_id) $res = $this->db->query("INSERT INTO leave_request (aid, employeeid, leavetype, other, paid, dateapplied, fromdate, todate, no_days, remarks, status, approvedby, dateapproved) 
								 				VALUES ('$base_id', '$employeeid', '$ltype', '$othleave', '$withpay', CURRENT_DATE, '$datefrom', '$dateto', '$ndays', '$reason', 'APPROVED', '$hrhead', CURRENT_DATE)
								 				");
		if($credit_q){	
			$avail = $credit_q[$employeeid]['avail'] + 1;
			$balance = $credit_q[$employeeid]['balance'] - 1;
			if($res){ 
				if($forEL){
					$this->db->query("UPDATE employee_leave_credit SET avail='$avail' WHERE employeeid='$employeeid' AND leavetype='$ltype' AND CURRENT_DATE BETWEEN dfrom AND dto");
					$this->db->query("UPDATE employee_leave_credit SET balance='$balance' WHERE employeeid='$employeeid' AND leavetype='EL' AND CURRENT_DATE BETWEEN dfrom AND dto");
				}else{
					$this->db->query("UPDATE employee_leave_credit SET avail='$avail', balance='$balance' WHERE employeeid='$employeeid' AND leavetype='$ltype' AND CURRENT_DATE BETWEEN dfrom AND dto");
				}
			}
		}
		if($res) return true;
		else return false;

	}

	function getEmployeeLeaveCredit($employeeid='',$leavetype='',$datefrom=''){
		$wC = '';$arr = $ret = array();
		if($employeeid) 	array_push($arr, " employeeid='$employeeid'");
		if($employeeid) 	array_push($arr, " leavetype='$leavetype'");
		if($employeeid) 	array_push($arr, " $datefrom BETWEEN dfrom AND dto");
		if(sizeof($arr)>0) $wC = " WHERE " . implode(' AND ', $arr);

		$credit_q  = $this->db->query("SELECT * FROM employee_leave_credit $wC");
		if($credit_q->num_rows() > 0){
			foreach ($credit_q->result() as $key => $row) {
				$ret[$row->employeeid] = array('leavetype'=>$row->leavetype, 'balance'=>$row->balance, 'credit'=>$row->credit, 'avail'=>$row->avail);
			}
		}else $ret = '';
		return $ret;
	}
	
	/**
	 * Gets employee leave credit details within specific dates
	 *
	 * @return stdClass Object
	 */

	function getEmpLeaveCredit($employeeid='', $leavetype='', $dfrom='', $dto=''){
		$wC = '';
		if($employeeid) 	$wC .= " WHERE employeeid='$employeeid'";
		if($leavetype) 		$wC .= ($wC==''? " WHERE a.leavetype='$leavetype'" : " AND a.leavetype='$leavetype'");
		if($dfrom && $dto) 	$wC .= ($wC==''? " WHERE dfrom='$dfrom' AND dto='$dto'" : " AND dfrom='$dfrom' AND dto='$dto'");
		// else 				$wC .= ($wC==''? " WHERE CURRENT_DATE BETWEEN dfrom AND dto " : " AND CURRENT_DATE BETWEEN dfrom AND dto");

		$res = $this->db->query("SELECT a.*, b.description FROM employee_leave_credit a INNER JOIN code_request_form b ON a.leavetype=b.code_request $wC");
		// echo "<pre>"; print_r($this->db->last_query()); die;
		return $res;
	}

	function recalculateEmpLeaveCredit($employeeid, $leaves){
		foreach ($leaves as $key => $value) {
			$avail = $this->extras->recountAvailedLeave($employeeid, $value->leavetype, $value->dfrom, $value->dto);
			$credit = $this->extras->getLeaveGiven($employeeid,$value->leavetype);
			$balance = $credit - $avail;
			$leavetype = $value->leavetype;
			$dfrom = $value->dfrom;
			$dto = $value->dto;
			$res = $this->db->query("UPDATE employee_leave_credit SET credit='$credit', avail='$avail', balance='$balance' WHERE employeeid = '$employeeid' AND leavetype = '$leavetype' AND dfrom='$dfrom' AND dto='$dto'");
		}
		return $this->getEmpLeaveCredit($employeeid);
	}

	/**
	 * Updates employee leave credit
	 *
	 * @return stdClass Object
	 */
	function saveLeaveCredit($employeeid='', $leavetype='', $dfrom='', $dto='', $credit=0, $avail=0, $balance=0){
		$editedby = $this->session->userdata('username');
		$wC = '';
		if($employeeid) 	$wC .= " WHERE employeeid='$employeeid'";
		if($leavetype) 		$wC .= ($wC==''? " WHERE leavetype='$leavetype'" : " AND leavetype='$leavetype'");
		if($dfrom && $dto) 	$wC .= ($wC==''? " WHERE dfrom='$dfrom' AND dto='$dto'" : " AND dfrom='$dfrom' AND dto='$dto'");
		else 				$wC .= ($wC==''? " WHERE CURRENT_DATE BETWEEN dfrom AND dto " : " AND CURRENT_DATE BETWEEN dfrom AND dto");

		$res = $this->db->query("UPDATE employee_leave_credit SET credit='$credit', avail='$avail', balance='$balance' $wC");

		$insert_trail = $this->db->query("INSERT INTO audit_trail (employeeid,leavetype,credit,avail,balance,editedby) VALUES ('$employeeid','$leavetype','$credit','$avail','$balance','$editedby')");
		return $res;
	}

	/**
	 * Gets list of cutoff dates from employee_leave_credit. This function is for special case employees not included in batch adding of leave credits.
	 *
	 * @return string
	 */
	function getLeaveCreditDates(){
		$this->load->model('utils');
		$arr = array();
		$res = $this->db->query("SELECT dfrom, dto FROM employee_leave_credit GROUP BY dfrom,dto");
		if($res->num_rows() > 0){
			foreach ($res->result() as $key => $row) {
				$arr[$row->dfrom.'|'.$row->dto] = date('M d, Y',strtotime($row->dfrom)) . ' - ' . date('M d, Y',strtotime($row->dto));
			}
		}
		echo $this->utils->constructOptionSelect($arr);
	}

	/**
	 * Saves new leave credit for a specific employee, leave type and cutoff dates. This function is for special case employees not included in batch adding of leave credits.
	 *
	 * @return string
	 */
	function addLeaveCredit($employeeid,$ltype,$credits,$dfrom,$dto){
		$res = $message = "";
		$user = $this->input->post("username");

		$cquery =   $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$employeeid' AND leavetype='$ltype' AND ('$dfrom' BETWEEN dfrom AND dto OR '$dto' BETWEEN dfrom AND dto) ");
		if($cquery->num_rows() == 0){
			$res = $this->db->query("INSERT INTO employee_leave_credit (employeeid,leavetype,balance,credit,dfrom,dto,user) VALUES ('{$employeeid}','$ltype','$credits','$credits','$dfrom','$dto','$user')");
		}else $message = "There is an existing leave credit setup for the cutoff.";

		if($res) $message = "Successfully saved.";
		else $message = "Failed to save leave credit.";

		return $message;

	}

	/**
	 * Save new setup for leave. This will insert new credit to corresponding employees with given date range of leave.
	 * addtl: condition for teaching type removed
	 * 
	 * @param Array $data
	 * 
	 * @return string
	 */
    function addNewLeaveSetup($data){
        $success_count  = 0;
        $msg  			= "";
        $user 			= $this->session->userdata("username");
        // $return 		= array();

        if(!$user) return array('err_code'=>2,'msg'=>'Failed to save. Your session may have expired. Please refresh page and try again.','success_count'=>$success_count,'data_failed'=>array()); 

        ///< check for duplicate setup
    	$duplicate_q = $this->db->query("SELECT * FROM code_leave_setup WHERE leavetype='{$data['mh_leavetype']}' AND ('{$data['datesetfrom']}' BETWEEN dfrom AND dto OR '{$data['datesetto']}' BETWEEN dfrom AND dto) ");

    	$arr_existing_stat = array();
    	if($duplicate_q->num_rows() > 0){
    		foreach ($duplicate_q->result() as $key => $row) {

    			$arr_tempstat = array();
    			$tempstat = $row->employmentStatus;

    			if($tempstat) $arr_tempstat = explode(',', $tempstat);
    			if(count($arr_tempstat)) {
    				foreach ($arr_tempstat as $temp_key => $temp_val) {
	    				array_push($arr_existing_stat, $temp_val);
    				}
    			}
    		}
    	}

    	if(count($arr_existing_stat) > 0){
    		///< compare given stat to existing
    		$arr_stat_intersect = array_intersect($data['empstatus'], $arr_existing_stat);

    		if(count($arr_stat_intersect) > 0) {
    			$arr_stat_intersect_desc = array();
    			foreach ($arr_stat_intersect as $stat_val) {
    				$arr_stat_intersect_desc[$stat_val] = $this->getEmploymentStatusDesc($stat_val);
    			}

    			return array('err_code'=>2,'msg'=>'Failed to save. Conflict setup for employment status.','success_count'=>$success_count,'data_failed'=>$arr_stat_intersect_desc); 
    		}
    	}

    	$arr_elig_setup = $this->getLeaveEligibilitySetup(true,$data['mh_leavetype']); ///< additional validation for eligibility \period per leavetype and employment status

    	if( sizeof($arr_elig_setup) == 0 ){
    		return array('err_code'=>2,'msg'=>'Failed to save new setup. Please set eligibility period first.','success_count'=>$success_count,'data_failed'=>array());
    	}

    	$data["datesetfrom"] = date("Y-m-d", strtotime($data["datesetfrom"]));
    	$data["datesetto"] = date("Y-m-d", strtotime($data["datesetto"]));

    	///< insert new setup
    	$employmentStatus = implode(",",$data['empstatus']);
    	$data['mh_credits'] = isset($data['mh_credits']) ? $data['mh_credits'] : 0;
    	$setup_q = $this->db->query("INSERT INTO code_leave_setup (leavetype,employmentStatus,credit,dfrom,dto,user) 
    									VALUES ('{$data['mh_leavetype']}','{$employmentStatus}','{$data['mh_credits']}','{$data['datesetfrom']}','{$data['datesetto']}','{$user}')");
		$setup_base_id = $this->db->insert_id();



    	if($setup_base_id){

	        	$arr_emp_failed = array();  ///< list of emp ids for failed saving of credit

	        	
	        	///< get list of employees under given employment status
	        	foreach ($data['empstatus'] as $key => $status) {
	        		if(isset($arr_elig_setup[$data['mh_leavetype']][$status])){
	        			$gender_applicable = $this->leaveGenderApplicable($data['mh_leavetype']);
	        			$wc = "";
	        			if($gender_applicable != "all") $wc = " AND gender = '$gender_applicable'";


		        		$emplist_q = $this->db->query("SELECT employeeid, teachingType FROM employee 
		        										WHERE isactive=1 AND employmentstat = '{$status}' $wc");
		        		// echo "<pre>";print_r($this->db->last_query());die;								
		        		if($emplist_q->num_rows() > 0){

		        			foreach ($emplist_q->result() as $key => $row) {
		        				$employeeid =  $row->employeeid;
		        				$teachingType =  $row->teachingType;

		        				// $exist_q = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$employeeid' AND employmentStat='$status' AND leavetype='{$data['mh_leavetype']}'");
		        				$exist_q = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$employeeid' AND leavetype='{$data['mh_leavetype']}'")->result_array();
		        				//Calculate leave
								$countAvailedLeave = $this->extras->recountAvailedLeave($employeeid, $data['mh_leavetype'], $data['datesetfrom'], $data['datesetto']);
	        					$getLeaveGiven = $this->extras->getLeaveGiven($employeeid,$data['mh_leavetype']);
	        					// echo "<pre>";print_r($this->db->last_query());die;
	        					// echo "<pre>";print_r($exist_q[0]['balance']);die;
	        					// if ($data['mh_leavetype'] == "VL") {
	        					// 	if (isset($exist_q[0]['balance'])) {
	        					// 		$getLeaveGiven = $getLeaveGiven + $exist_q[0]['balance'];
	        					// 	}
	        					// }
	        					$remaining = $getLeaveGiven - $countAvailedLeave;

		        				if(count($exist_q) > 0){
		        					$insert_his_q = $this->db->query("INSERT INTO employee_leave_credit_history (employeeid,base_id,leavetype,balance,credit,avail,dfrom,dto,employmentStat,`user`) 
		        										(SELECT employeeid,base_id,leavetype,balance,credit,avail,dfrom,dto,employmentStat,`user` FROM employee_leave_credit
		        											WHERE employeeid='$employeeid' AND leavetype='{$data['mh_leavetype']}'
		        										)
		        									");

		        					$del_q = true;
									// if($insert_his_q) $del_q = $this->db->query("DELETE FROM employee_leave_credit WHERE employeeid='$employeeid' AND employmentStat = '$status' AND leavetype='{$data['mh_leavetype']}' AND ('$old_dfrom' BETWEEN dfrom AND dto OR '$old_dto' BETWEEN dfrom AND dto)");
									$del_q = $this->db->query("DELETE FROM employee_leave_credit WHERE employeeid='$employeeid' AND leavetype='{$data['mh_leavetype']}'");
									// echo "<pre>";print_r($this->db->last_query());
									if($del_q){
										$credit = 0;
										// echo "<pre>";print_r($arr_elig_setup);die;

										if($teachingType=='teaching'){
											$credit = $arr_elig_setup[$data['mh_leavetype']][$status]['credit'];
										} 
										else {
											$credit = $arr_elig_setup[$data['mh_leavetype']][$status]['credit_non'];
										} 
										if($credit){
											$res = $this->db->query("INSERT INTO employee_leave_credit (employeeid,base_id,leavetype,balance,credit,avail,dfrom,dto,employmentStat, user) 
															 	VALUES ('$employeeid','$setup_base_id','{$data['mh_leavetype']}','{$remaining}','{$getLeaveGiven}','{$countAvailedLeave}','{$data['datesetfrom']}','{$data['datesetto']}','$status', '$user')
															");
											if($res) $success_count++;
											else array_push($arr_emp_failed, $employeeid);
										}

									}else array_push($arr_emp_failed, $employeeid);

		        				}else{
									$credit = 0;

									if($teachingType=='teaching'){
										$credit = $arr_elig_setup[$data['mh_leavetype']][$status]['credit'];
									} 
									else {
										$credit = $arr_elig_setup[$data['mh_leavetype']][$status]['credit_non'];
									} 	
									if($credit){
										$res = $this->db->query("INSERT INTO employee_leave_credit (employeeid,base_id,leavetype,balance,credit,avail,dfrom,dto,employmentStat, `user`) 
														 	VALUES ('$employeeid','$setup_base_id','{$data['mh_leavetype']}','{$remaining}','{$getLeaveGiven}','{$countAvailedLeave}','{$data['datesetfrom']}','{$data['datesetto']}','$status', '$user')
														");

										if($res) $success_count++;
										else array_push($arr_emp_failed, $employeeid);
									}else{
										array_push($arr_emp_failed, $employeeid);
									}
		        				}

		        			} ///< end loop for list of emp
		        		}	

	        		
	        		}
	        	}

    	}else return array('err_code'=>2,'msg'=>'Failed to save new setup. Please try again.','success_count'=>$success_count,'data_failed'=>array());


    	return array('err_code'=>1,'msg'=>'Validity Date has been saved successfully.','success_count'=>$success_count,'data_failed'=>$arr_emp_failed);  

    }

    /**
	 * Getting gender applicable for a leave type
	 *
	 * @param Int $code_request
	 * 
	 * @return booelan
	 */
    function leaveGenderApplicable($code_request=""){
    	$q_leave = $this->db->query("SELECT * FROM code_request_form WHERE code_request = '$code_request'");
    	if($q_leave->num_rows() > 0) return $q_leave->row()->genderApplicable;
    	else return false;
    }

	/**
	 * Edit setup for leave. This will update existing credit to corresponding employees with given date range of leave.
	 *
	 * @param Array $data
	 * 
	 * @return string
	 */
    function editLeaveSetup($data){
    	$success_count  = 0;
        $msg  			= "";
        $user 			= $this->session->userdata("username");
        // $return 		= array();
        // echo "<pre>";print_r($data);die;

        if(!$user) return array('err_code'=>2,'msg'=>'Failed to save. Your session may have expired. Please refresh page and try again.','success_count'=>$success_count,'data_failed'=>array()); 


        ///< get old setup data
    	$old_dfrom = $old_dto = $old_empstat = $old_ltype = $old_tnt = $old_credit = '';
    	$old_q = $this->db->query("SELECT * FROM code_leave_setup WHERE id = '{$data['lid']}'");

    	if($old_q->num_rows() > 0){
    		$old_dfrom 				= $old_q->row(0)->dfrom;
    		$old_dto 				= $old_q->row(0)->dto;
    		$old_ltype 				= $old_q->row(0)->leavetype;
    		$old_credit				= $old_q->row(0)->credit;
    		$old_tnt				= $old_q->row(0)->teachingType;
    		$old_empstat 			= $old_q->row(0)->employmentStatus;
    	}else return array('err_code'=>2,'msg'=>'Failed to save changes. Please try again.','success_count'=>$success_count,'data_failed'=>array()); 

    	$arr_oldstat = array();
		if($old_empstat) $arr_oldstat = explode(',', $old_empstat);
		if(count($arr_oldstat) == 0)  return array('err_code'=>2,'msg'=>'Failed. Cannot select assigned employment status.','success_count'=>$success_count,'data_failed'=>array()); 



        ///< check for duplicate setup
    	$duplicate_q = $this->db->query("SELECT * FROM code_leave_setup WHERE leavetype='{$data['mh_leavetype']}' AND ('{$data['datesetfrom']}' BETWEEN dfrom AND dto OR '{$data['datesetto']}' BETWEEN dfrom AND dto) AND id != '{$data['lid']}'");


    	    	$arr_existing_stat = array();
    	    	if($duplicate_q->num_rows() > 0){
    	    		foreach ($duplicate_q->result() as $key => $row) {

    	    			$arr_tempstat = array();
    	    			$tempstat = $row->employmentStatus;

    	    			if($tempstat) $arr_tempstat = explode(',', $tempstat);
    	    			if(count($arr_tempstat)) {
    	    				foreach ($arr_tempstat as $temp_key => $temp_val) {
    		    				array_push($arr_existing_stat, $temp_val);
    	    				}
    	    			}
    	    		}
    	    	}

    	    	if(count($arr_existing_stat) > 0){
    	    		///< compare given stat to existing
    	    		$arr_stat_intersect = array_intersect($arr_oldstat, $arr_existing_stat);

    	    		if(count($arr_stat_intersect) > 0) {
    	    			$arr_stat_intersect_desc = array();
    	    			foreach ($arr_stat_intersect as $stat_val) {
    	    				$arr_stat_intersect_desc[$stat_val] = $this->getEmploymentStatusDesc($stat_val);
    	    			}

    	    			return array('err_code'=>2,'msg'=>'Failed to save. Conflict setup for employment status.','success_count'=>$success_count,'data_failed'=>$arr_stat_intersect_desc); 
    	    		}
    	    	}

    	    	$arr_elig_setup = $this->getLeaveEligibilitySetup(true,$data['mh_leavetype']); ///< additional validation for eligibility period per leavetype and employment status

    	    	if( sizeof($arr_elig_setup) == 0 ){
    	    		return array('err_code'=>2,'msg'=>'Failed to save new setup. Please set eligibility period first.','success_count'=>$success_count,'data_failed'=>array());
    	    	}



    	///< update setup
    	$setup_q = $this->db->query("UPDATE code_leave_setup SET dfrom = '{$data['datesetfrom']}',dto = '{$data['datesetto']}' WHERE id='{$data['lid']}'");
    	

    	if($setup_q){
    		
    		$this->db->query("UPDATE employee_leave_credit SET 
		    							dfrom = '{$data['datesetfrom']}',
		    							dto = '{$data['datesetto']}'
		    							WHERE base_id='{$data['lid']}' AND dfrom='$old_dfrom' AND dto='$old_dto'");

    		foreach ($arr_oldstat as $status) {

        		if(isset($arr_elig_setup[$data['mh_leavetype']][$status])){


	        		$emplist_q = $this->db->query("SELECT employeeid,teachingType FROM employee 
	        										WHERE isactive=1 AND employmentstat = '{$status}'");
	        		// echo "<pre>";print_r($emplist_q->result());die;
	        		if($emplist_q->num_rows() > 0){

	        			foreach ($emplist_q->result() as $key => $row) {
	        				$employeeid =  $row->employeeid;
	        				$teachingType =  $row->teachingType;

	        				$exist_q = $this->db->query("SELECT id FROM employee_leave_credit WHERE employeeid='$employeeid' AND employmentStat='$status' AND leavetype='{$data['mh_leavetype']}' AND ('$old_dfrom' BETWEEN dfrom AND dto OR '$old_dto' BETWEEN dfrom AND dto)");


	        				if($exist_q->num_rows() > 0){

	        					// Ask ken for comments remove old condition inserting history can now only be on adding new data on the leave type

	        					// $insert_his_q = $this->db->query("INSERT INTO employee_leave_credit_history (employeeid,base_id,leavetype,balance,credit,avail,dfrom,dto,employmentStat,`user`) 
	        					// 					(SELECT employeeid,base_id,leavetype,balance,credit,avail,dfrom,dto,employmentStat,`user` FROM employee_leave_credit
	        					// 						WHERE employeeid='$employeeid' AND employmentStat = '$status' AND leavetype='{$data['mh_leavetype']}' AND ('$old_dfrom' BETWEEN dfrom AND dto OR '$old_dto' BETWEEN dfrom AND dto)
	        					// 					)
	        					// 				");

	       //  					$del_q = true;
								// if($insert_his_q) $del_q = $this->db->query("DELETE FROM employee_leave_credit WHERE employeeid='$employeeid' AND employmentStat = '$status' AND leavetype='{$data['mh_leavetype']}' AND ('$old_dfrom' BETWEEN dfrom AND dto OR '$old_dto' BETWEEN dfrom AND dto)");

								// if($del_q){
									$credit = 0;

									if($teachingType=='teaching'){
										$credit = $arr_elig_setup[$data['mh_leavetype']][$status]['credit'];
									} 
									else {
										$credit = $arr_elig_setup[$data['mh_leavetype']][$status]['credit_non'];
									} 	

									// $res = $this->db->query("INSERT INTO employee_leave_credit (employeeid,base_id,leavetype,balance,credit,avail,dfrom,dto,employmentStat,`user`) VALUES ('$employeeid',$data['lid'], $data['mh_leavetype'], $remaining, $getLeaveGiven, countAvailedLeave, $data['datesetfrom'], $data['datesetto'], $status, $user)");					  

									// $res = $this->db->query("INSERT INTO employee_leave_credit (employeeid,base_id,leavetype,balance,credit,avail,dfrom,dto,employmentStat) 
									// 				 	VALUES ('$employeeid','{$data['lid']}','{$data['mh_leavetype']}','{$credit}','{$credit}','0','{$data['datesetfrom']}','{$data['datesetto']}','$status')
									// 				");

									$countAvailedLeave = $this->extras->recountAvailedLeave($employeeid, $data['mh_leavetype'], $data['datesetfrom'], $data['datesetto']);
		        					$getLeaveGiven = $this->extras->getLeaveGiven($employeeid,$data['mh_leavetype']);
		        					// echo "<pre>";print_r($this->db->last_query());die;
		        					$remaining = $getLeaveGiven - $countAvailedLeave;
		        					$user = $this->session->userdata("username");
		        					$dfrom = $data['datesetfrom'];
		        					$dto = $data['datesetto'];
		        					$base_id = $data['lid'];

									// $res = $this->db->query("INSERT INTO employee_leave_credit (employeeid,base_id,leavetype,balance,credit,avail,dfrom,dto,employmentStat, `user`) 
									// 						 	VALUES ('$employeeid','{$data['lid']}','{$data['mh_leavetype']}','{$remaining}','{$getLeaveGiven}','{$countAvailedLeave}','{$data['datesetfrom']}','{$data['datesetto']}','$status', '$user')
									// 						");

									$res = $this->db->query("UPDATE employee_leave_credit SET balance = '$remaining',credit = '$getLeaveGiven', avail = '$countAvailedLeave', dfrom = '$dfrom',dto = '$dto',`user` = '$user' WHERE base_id = '$base_id' AND employeeid = '$employeeid'");

									if($res) $success_count++;
									else array_push($arr_emp_failed, $employeeid);

								// }else array_push($arr_emp_failed, $employeeid);

	        				}

	        			} ///< end loop for list of emp
	        		}	

        		
        		}

    		}

    	}else return array('err_code'=>2,'msg'=>'Failed to save new setup. Please try again.','success_count'=>$success_count,'data_failed'=>array());

    	return array('err_code'=>1,'msg'=>'Validity Date has been updated successfully.','success_count'=>$success_count,'data_failed'=>array());  

    }


    function getEmploymentStatusDesc($code=''){
    	$res = $this->db->query("SELECT description FROM code_status WHERE code='$code'");
    	if($res->num_rows() > 0) return $res->row(0)->description;
    	else return '';
    }

    function getLeaveEligibilitySetup($isArray=false,$code='',$emp_status=''){
    	$wC = '';
    	if($code) 			$wC .= " AND code_request='$code'";
    	if($emp_status)		$wC .= " AND emp_status='$emp_status'";


    	$elig_q = $this->db->query("SELECT * FROm code_request_eligibility_period WHERE  (credit > 0 || credit_non > 0) $wC");

    	if($isArray){
    		$arr_elig_setup = array();
	    	if($elig_q->num_rows() > 0){
	    		foreach ($elig_q->result() as $key => $row) {
					$arr_elig_setup[$row->code_request][$row->emp_status] = array('count'=>$row->count,'mode'=>$row->mode,'credit'=>$row->credit,'credit_non'=>$row->credit_non);    			
	    		}
	    	}
    		return $arr_elig_setup;
    	}

    	return $elig_q;

    	
    }

    function getApplicableLeave($employeeid){
    	// return $this->db->query("SELECT * 
					// 			 FROM code_request_form a
					// 			 WHERE a.ismain=1 AND NOT a.code_request IN (SELECT leavetype FROM employee_leave_credit WHERE employeeid='$employeeid' AND (CURRENT_DATE BETWEEN dfrom AND dto)) AND a.code_request <> 'EL' AND a.code_request <> 'DA'
					// 			 ORDER BY a.description;")->result();  https://jira.pinnacle.edu.ph/browse/POVEDAHYP-2412

    	return $this->db->query("SELECT * 
								 FROM code_request_form a
								 WHERE a.ismain=1 AND NOT a.code_request IN (SELECT leavetype FROM employee_leave_credit WHERE employeeid='$employeeid') AND a.code_request <> 'EL' AND a.code_request <> 'DA'
								 ORDER BY a.description;")->result();
    }

     public function getLeaveDateRange($leave_type, $employmentstatus){
    	$this->load->model('utils');
    	$arr = array();
    	$query = $this->db->query("SELECT * FROM code_leave_setup WHERE leavetype = '$leave_type' AND CURRENT_DATE BETWEEN dfrom AND dto");
    	if($query->num_rows() > 0){
    		foreach ($query->result() as $key => $row) {
    			if(in_array($employmentstatus, explode(',', $row->employmentStatus))){
    				$arr[$row->dfrom.'|'.$row->dto] = date('M d, Y',strtotime($row->dfrom)) . ' - ' . date('M d, Y',strtotime($row->dto));
    			}
			}
		}
		else{
			$res = $this->db->query("SELECT dfrom, dto FROM employee_leave_credit GROUP BY dfrom,dto");
			// echo "<pre>"; print_r($this->db->last_query()); die;
			if($res->num_rows() > 0){
				foreach ($res->result() as $key => $row) {
					$arr[$row->dfrom.'|'.$row->dto] = date('M d, Y',strtotime($row->dfrom)) . ' - ' . date('M d, Y',strtotime($row->dto));
				}
			}
		}
		echo $this->utils->constructOptionSelect($arr);
    }

    public function getEmployeeLeaveList($employeeid, $date = ""){
    	$where_clause = "";
    	if($employeeid) $where_clause .= " AND employeeid = '$employeeid'";
    	if($date) $where_clause .= " AND '$date' BETWEEN dfrom AND dto";
    	$datenow = date("Y-m-d", strtotime($this->db->query("SELECT CURRENT_TIMESTAMP ")->row()->CURRENT_TIMESTAMP));
    	$q_otherleave = $this->db->query("SELECT * FROM code_request_form a INNER JOIN employee_leave_credit b ON a.`code_request` = b.`leavetype` WHERE is_leave = '1' $where_clause GROUP BY code_request ");
    	if($q_otherleave->num_rows() > 0){
    		return $q_otherleave->result_array();
    	}else{
    		$q_otherleave = $this->db->query("SELECT * FROM code_request_form a INNER JOIN employee_leave_credit_history b ON a.`code_request` = b.`leavetype` WHERE is_leave = '1' $where_clause GROUP BY code_request ");
    		if ($q_otherleave->num_rows() > 0) {
    			return $q_otherleave->result_array();
    		}else{
    			return false;
    		} 
    	}
    	
    }

    public function updateLeaveCreditData($employeeid, $lt, $balance, $credit, $avail){
    	return $this->db->query("UPDATE employee_leave_credit set balance = '$balance', credit ='$credit', avail='$avail' WHERE employeeid='$employeeid' AND leavetype = '$lt'");
    }
    
    public function getLeaveTodayEmployees($datenow, $empstat = ''){
    	if($empstat){
    		$q_leave = $this->db->query("SELECT DISTINCT a.employeeid FROM leave_request a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE ('$datenow' BETWEEN a.fromdate AND a.todate) AND b.employmentstat NOT IN ('$empstat')");
    	}else{
    		$q_leave = $this->db->query("SELECT DISTINCT employeeid FROM leave_request WHERE '$datenow' BETWEEN fromdate AND todate ");
    	}
    	
    	if($q_leave->num_rows() > 0) return $q_leave->result_array();
    	else return false;
    }
    // public function getLeaveTodayEmployees($datenow){
    // 	$q_leave = $this->db->query("SELECT DISTINCT employeeid FROM leave_request WHERE '$datenow' BETWEEN fromdate AND todate ");
    // 	if($q_leave->num_rows() > 0) return $q_leave->result_array();
    // 	else return false;
    // }

    public function getEmployeeVacationLeaveCredit($employeeid, $dfrom=""){
    	$wc = "";
    	if($dfrom) $wc = " AND '$dfrom' BETWEEN dfrom AND dto ";
    	$q_credit = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid = '$employeeid' AND leavetype = 'VL' $wc ");
    	if($q_credit->num_rows() > 0) return $q_credit->result_array();
    	return false;
    }

    public function getEmployeeSameScheduleLeave($employeeid, $idx){
    	return $this->db->query("SELECT * FROM employee_schedule_history WHERE idx IN ($idx) AND employeeid = '$employeeid' GROUP BY starttime");
    }

    function getEmpLeaveCreditHistoryByYear($employeeid='', $leavetype='', $dfrom='', $dto=''){
		$wC = 'WHERE b.is_leave = 1';
		if($employeeid) 	$wC .= " AND employeeid='$employeeid'";
		if($leavetype) 		$wC .= " AND leavetype='$leavetype'";
		if($dfrom && $dto) 	$wC .= " AND dfrom='$dfrom' AND dto='$dto'";

		$res = $this->db->query("SELECT a.*, b.description FROM employee_leave_credit_history a INNER JOIN code_request_form b ON a.leavetype=b.code_request $wC GROUP BY YEAR(`dfrom`) ORDER BY YEAR(`dfrom`)")->result();
		// echo"<pre>";print_r($this->db->last_query());die;
		return $res;
	}

	function getEmpLeaveCreditHistory($employeeid='', $leavetype='', $year=''){
		$wC = '';
		if($employeeid) 	$wC .= " WHERE employeeid='$employeeid'";
		if($leavetype) 		$wC .= ($wC==''? " WHERE a.leavetype='$leavetype'" : " AND a.leavetype='$leavetype'");
		if($year) 	$wC .= ($wC==''? " WHERE AND YEAR(dfrom) = '$year'" : " AND YEAR(dfrom) = '$year'");

		$res = $this->db->query("SELECT a.*, b.description FROM employee_leave_credit_history a INNER JOIN code_request_form b ON a.leavetype=b.code_request $wC");
		// echo"<pre>";print_r($this->db->last_query());die;
		return $res;
	}

	function leaveOverlapChecker($leaveType='', $dfrom = '', $dtodate = ""){
        $res = $this->db->query("SELECT dfrom, dto FROM `code_leave_setup` WHERE leavetype = '$leaveType' AND (UNIX_TIMESTAMP(dfrom) - UNIX_TIMESTAMP('$dtodate')) * (UNIX_TIMESTAMP(dto) - UNIX_TIMESTAMP('$dfrom')) <= 0");
        if (isset($res->row(0)->dfrom)) return "Overlapping setup on ".$res->row(0)->dfrom." : ".$res->row(0)->dto;
        else return "none";
    }

    public function applicationApproverSequence($code){
		return $this->db->query("SELECT * FROM code_request_form WHERE code_request = '$code'");
	}

	public function deleteOnlineApplicationCode($id){
		return $this->db->query("DELETE FROM online_application_code WHERE id = '$id'");
	}

	public function onlineApplicationDetails($id){
		return $this->db->query("SELECT * FROM online_application_code a INNER JOIN code_request_form b ON a.id = b.base_id WHERE is_leave = '1' AND a.id = '$id' AND ismain = '1' ");
	}

	public function isApplicationCodeUsed($code){
		$ob = $ot = $cs = 0;
		$leave = $this->db->query("SELECT * FROM leave_app_base WHERE TYPE = '$code' ")->num_rows();
		if($code == "DA") $ob = $this->db->query("SELECT * FROM ob_app")->num_rows();
		if($code == "OT") $ot = $this->db->query("SELECT * FROM ot_app")->num_rows();
		if($code == "CS") $cs = $this->db->query("SELECT * FROM change_schedule")->num_rows();

		return ($leave + $ot + $ob + $cs);
	}

} //endoffile