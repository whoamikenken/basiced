<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overtime extends CI_Model {

	/**
	 * Generate list of employees (employeeid and fullname) under a given department id. Returns all if no deptid given.
	 *
	 * @param string $deptid (Default: "")
	 *
	 * @return array
	 */
	function getEmplist($deptid = ""){
		$wC = "";
		$emplist=array();
		if($deptid)  $wC .= " AND deptid = '$deptid'";
		$query = $this->db->query("SELECT employeeid,lname,fname,mname FROM employee WHERE (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00') $wC ORDER BY lname")->result();
		foreach($query as $val){
		   $emplist[$val->employeeid] = $val->employeeid." - ".$val->lname.", ".$val->fname." ".$val->mname ;
		}
		return $emplist;
	}
	
	/**
	 * Generates list of employeeid's under a given department. Returns all if no deptid given.
	 *
	 * @param string $deptid (Default: "")
	 *
	 * @return array
	 */
	function getEmpIDs($deptid=""){
		$wC = "";
    	$arr_empids = array();
		if($deptid) $wC = "WHERE deptid='$deptid'";
    	$res = $this->db->query("SELECT employeeid FROM employee $wC");
    	if($res->num_rows() > 0){
    		foreach ($res->result() as $obj) {
    			array_push($arr_deptids, $obj->code);
    		}
    	}
    	return $arr_empids;
    }

    /**
	 * Gets request setup based on code_request.
	 *
	 * @param string $type (Default: "")
	 *
	 * @return stdClass Object
	 */
    function getAppSequence($type=""){
    	$res = $this->db->query("SELECT * FROM code_request_form WHERE code_request='$type'");
    	return $res;
    }

    /**
	 * Gets request details based on ot app id.
	 *
	 * @param string $otid (Default: "")
	 *
	 * @return stdClass Object
	 */
    function getAppSequencePerOT($otid=''){
    	$res = $this->db->query("SELECT * FROM ot_app_emplist a INNER JOIN ot_app b ON a.`base_id`=b.`id` WHERE a.id='$otid'");
    	return $res;
    }

    /**
	 * Gets department data based on given column name and code.
	 *
	 * @param string $col column name ie. head/divisionhead (Default: "")
	 * @param string $code dept code (Default: "")
	 *
	 * @return string
	 */
    function getDeptHead($col="", $code=""){
    	$head = "";
    	$res = $this->db->query("SELECT $col FROM code_office WHERE code='$code'");
    	if($res->num_rows() > 0) $head = $res->row(0)->$col;
    	return $head;
    }

    /**
	 * Inserts new ot application in base table and gets last inserted id.
	 *
	 * @return int
	 */
    function insertBaseOTApp($user, $datefrom, $dateto, $tstart, $tend, $total, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq){
    	$id = "";
    	$res = $this->db->query("INSERT INTO ot_app (
    			applied_by, dfrom, 		dto, 	tstart, 	tend,	 total,	 reason,	 dhead,		chead,	 hrhead, cphead,	fdhead, 	bohead, 	phead, 	uphead, 	dseq, 	cseq, 	hrseq, cpseq, 	fdseq, 		boseq, 		pseq, 	upseq, date_applied) VALUES (
    			'$user', '$datefrom', '$dateto', '$tstart', '$tend', '$total', ".$this->db->escape($reason).", '$dhead', '$chead', '$hrhead', '$cphead', '$fdhead', '$bohead', '$phead', '$uphead', '$dseq', '$cseq', '$hrseq', '$cpseq', '$fdseq', '$boseq', '$pseq', '$upseq', CURRENT_DATE)
    			");
    	if($res)  	$id = $this->db->insert_id();
    	return $id;
    }

    function updateOTApp($user,$base_id_edit,$otid,$emplist,$teachingType, $dstatus, $ddate, $dfrom, $dto, $tstart, $tend, $total, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq){

    	$empcount = $isread = 0;
    	$arr_data_failed = array();
    	$res = '';

    	foreach ($emplist as $employeeid) {
    		if($employeeid == $user) $isread = 1;

	    	if( $this->countApplications($employeeid,$dfrom,$dto,$tstart,$tend,$otid) == 0 ){
		    	$res = $this->db->query("
		    							UPDATE ot_app SET dfrom 	= '$dfrom',
		    											  dto 		= '$dto',
		    											  tstart 	= '$tstart',
		    											  tend 		= '$tend',
		    											  total 	= '$total',
		    											  reason 	= {$this->db->escape($reason)},
		    											  dhead 	= '$dhead', chead = '$chead', hrhead = '$hrhead', cphead = '$cphead', fdhead = '$fdhead', bohead = '$bohead', phead = '$phead', uphead = '$uphead', 
		    											  dseq = '$dseq', cseq = '$cseq', hrseq = '$hrseq', cpseq = '$cpseq', fdseq = '$fdseq', boseq = '$boseq', pseq = '$pseq', upseq = '$upseq'
		    							WHERE id='$base_id_edit' AND applied_by = '$employeeid'

		    		");

		    	$res = $this->db->query("UPDATE ot_app_emplist SET teachingType = '$teachingType',
		    													   dstatus = '$dstatus',
		    													   ddate = '$ddate',
		    													   isread = '$isread'
		    							WHERE id='$otid'
		    		");
		    	if($res) $empcount++;
		    	else array_push($arr_data_failed, $employeeid);
	    	}
	    	$isread = 0;
    	}
    	return array($empcount,$arr_data_failed);
    }

    /**
	 * Inserts ot app in secondary table for list of employees.
	 *
	 * @param int $base_id key from base table
	 * @param array $arr_emplist list of employeeids
	 * @param string $dstatus status for dept head (since dept head is set to be first default approver)
	 * @param date $ddate
	 *
	 * @return Array
	 */
    function insertOTAppEmpList($base_id, $arr_emplist, $teachingType, $dstatus, $ddate, $user, $datefrom, $dateto, $tstart, $tend){
    	$empcount = $isread = 0;
    	$arr_data_failed = array();
    	foreach ($arr_emplist as $employeeid) {
    		if($employeeid == $user) $isread = 1;

    		if( $this->countApplications($employeeid,$datefrom,$dateto,$tstart,$tend) == 0 ){
				$res = $this->db->query("
					INSERT INTO ot_app_emplist (base_id, employeeid, teachingType, dstatus, ddate, isread) VALUES ('$base_id', '$employeeid', '$teachingType','$dstatus', '$ddate', '$isread')
				");

				if($res) $empcount++;
				else array_push($arr_data_failed, $employeeid);
    		}
			$isread = 0;
		}
		return array($empcount,$arr_data_failed);
    }

    /**
	 * This will check for conflict applications in ot.
	 *
	 * @return int
	 */
    function countApplications($employeeid='',$dfrom='',$dto='',$tstart='', $tend='',$otid=''){
    	$count = 0;
    	$wC = '';
    	// if($dfrom) $wC .= " AND ('$dfrom' BETWEEN dfrom AND dto)";
    	// if($dto) $wC   .= " AND ('$dto' BETWEEN dfrom AND dto)";
    	if($otid) $wC .= " AND a.id!='$otid'";

    	$query = $this->db->query("SELECT COUNT(a.id) as appcount
									FROM ot_app_emplist a
									LEFT JOIN ot_app b ON a.`base_id`=b.`id`
									WHERE (a.status='APPROVED' OR a.status='PENDING') AND a.employeeid='$employeeid'
									AND 	
										(
											 (
												('$dfrom' 	BETWEEN dfrom AND dto) OR
												('$dto' 	BETWEEN dfrom AND dto) OR
												(dfrom 		BETWEEN '$dfrom' AND '$dto') OR
												(dto 		BETWEEN '$dfrom' AND '$dto') 
											 
											 )
											 AND
											 (
												('$tstart' 	BETWEEN tstart AND tend) OR
												('$tend' 	BETWEEN tstart AND tend) OR
												(tstart 	BETWEEN '$tstart' AND '$tend') OR
												(tend 		BETWEEN '$tstart' AND '$tend') 
											 )
										)
									$wC

									");

    	if($query->num_rows()>0) $count = $query->row(0)->appcount;
    	return $count;
    	
    }


    function insertOTAppEmpListHead($base_id, $arr_emplist, $dhead, $chead, $dseq, $cseq, $user){
    	$empcount = $isread = 0;
    	foreach ($arr_emplist as $employeeid) {
    		if($employeeid == $user) $isread = 1;
    		$dstatus = "PENDING";
    		$ddate = "";
    		$cstatus = "PENDING";
    		$cdate = "";

    		if($dhead <> $chead){
	    		if(in_array($dhead, $arr_emplist)){
	    			if($dseq == 1){
	    				$dstatus = "APPROVED";
		        		$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");
	    			}
	    			$res = $this->db->query("
	    				INSERT INTO ot_app_emplist (base_id, employeeid, dstatus, ddate, isread) VALUES ('$base_id', '$employeeid', '$dstatus', '$ddate' , '$isread')
	    			");
	    			if($res) $empcount++;
	    		}
    			
	    		if(in_array($chead, $arr_emplist)){
	    			if($cseq ==  1){
	    				$cstatus = "APPROVED";
		        		$cdate 	 = date_format( new DateTime('today') ,"Y-m-d");
	    			}
	    			$res = $this->db->query("
	    				INSERT INTO ot_app_emplist (base_id, employeeid, cstatus, cdate, isread) VALUES ('$base_id', '$employeeid', '$cstatus', '$cdate' , '$isread')
	    			");
	    			if($res) $empcount++;
	    		}
			}else{
	    		if(in_array($dhead, $arr_emplist) || in_array($chead, $arr_emplist)){
	    			if($dseq == 1){
	    				$dstatus = "APPROVED";
		        		$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");
	    			}
	    			if($cseq ==  1){
	    				$cstatus = "APPROVED";
		        		$cdate 	 = date_format( new DateTime('today') ,"Y-m-d");
	    			}
	    			$res = $this->db->query("
	    				INSERT INTO ot_app_emplist (base_id, employeeid, dstatus, ddate, cstatus, cdate, isread) VALUES ('$base_id', '$employeeid', '$dstatus', '$ddate' , '$cstatus', '$cdate' , '$isread')
	    			");
	    			if($res) $empcount++;
	    		}
			}
			$isread = 0;
		}
		return $empcount;
    }

    /**
	 * Gets list of employee ot applications.
	 *
	 * @return stdClass Object
	 */
    function  getEmpOTHistory($employeeid="", $datefrom="", $dateto="", $status="", $otid="", $isread=''){
    	$wC = "";

    	if($datefrom && $dateto) $wC .= " AND b.`dfrom` BETWEEN '$datefrom' AND '$dateto' AND b.`dto` BETWEEN '$datefrom' AND '$dateto'";
    	if($status)				 $wC .= " AND a.`status`='$status'";
    	if($otid)				 $wC .= " AND a.id='$otid'";
    	// if($isread <> '')		 $wC .= " AND a.isread='$isread'";
        $res = $this->db->query("SELECT a.id AS otid, a.*,b.* ,REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname
        							FROM ot_app_emplist a
									INNER JOIN ot_app b ON a.`base_id`=b.`id`
									INNER JOIN employee c ON a.employeeid=c.employeeid
									WHERE a.employeeid='$employeeid' 
									$wC");
									// OR b.applied_by='$employeeid' 
        return $res;
	}

	/**
	 * Gets OT details.
	 *
	 * @param string $otid (Default: "")
	 * @param string $colstatus (Default: "")
	 *
	 * @return array
	 */
	function getOTDetails($otid='',$colstatus=''){
		$data = array();
		$res = $this->db->query("SELECT a.id AS otid, a.employeeid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname,e.description AS epos, d.description AS edept, a.*,b.* 
									FROM ot_app_emplist a
									INNER JOIN ot_app b ON a.`base_id`=b.`id`
									INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
									LEFT JOIN code_position e ON c.positionid = e.positionid
									LEFT JOIN code_office d ON c.deptid = d.code 
									WHERE a.id='$otid'");
		if($res->num_rows() > 0){
			foreach ($res->result() as $obj) 
			{
				$data['base_id'] 		= $obj->base_id;
				$data['otid'] 			= $obj->otid;
				$data['employeeid'] 	= $obj->employeeid;
				$data['date_applied'] 	= $obj->date_applied;
				$data['dfrom'] 			= $obj->dfrom;
				$data['dto'] 			= $obj->dto;
				$data['tstart'] 		=  $obj->tstart != '00:00:00' ? date('h:i A',strtotime($obj->tstart)) : '';
				$data['tend'] 			=  $obj->tstart != '00:00:00' ? date('h:i A',strtotime($obj->tend)) : '';
				$data['total'] 			= $obj->total;
				$data['total_approved'] = $obj->approved_total;
				$data['reason'] 		= $obj->reason;
				$data['status'] 		= $obj->status;
				if($colstatus) 	$data['colstat']= $obj->$colstatus; 
				$data['fullname'] 		= $obj->fullname;
				$data['pos'] 			= $obj->epos;
				$data['edept']  		= $obj->edept;
				$data['rem']			= $obj->remarks;
  			}
		}
		return $data;
	}

	# for ica-hyperion 21552
	# by justin (with e)
	function getCreditedOvertime($empid, $date, $otsubtotal){
		$this->load->model('attcompute');
		$this->load->model('service_credit');

		$tmp = $this->attcompute->exp_time($otsubtotal);

		$tmp_tot = $tmp/* - ($tmp % 1800)*/;

		if($tmp_tot > 0){
			$is_holiday = false;
			$is_no_sched = false;

			$q_avail_date = $this->db->query("SELECT * 
											  FROM employee_schedule_history 
											  WHERE employeeid = '$empid' AND idx  = DATE_FORMAT('$date','%w') AND dateactive <= '$date';
											  ")->result();
        	if(count($q_avail_date) == 0) $is_no_sched = true;

			$deptid = $this->service_credit->getEmpDept($empid);
        	$is_holiday = $this->attcompute->isHolidayNew($empid, $date, $deptid);

			if($is_holiday || $is_no_sched) $tmp_tot -= 3600;
		}
		if($tmp_tot < 3600) $tmp_tot = 0;
		$creditedTime = $this->attcompute->sec_to_hm($tmp_tot);

		return $creditedTime;
	}
	# end for ica-hyperion 21552

	
	/**
	 * Gets list of actual overtime logs for given date range from employee overtime application.
	 *
	 * @return array
	 */
	function getOTDetailsForHR($data=array()){
		$this->load->model('utils');
		$ot_logs = array();
		$log_dates = $this->utils->getDatesFromRange($data['dfrom'], $data['dto']);
		$emp_sched = $emp_timesheet = array();

		$applied_tstart = isset($data['tstart']) 	? strtotime($data['tstart']) 	: '--';
		$applied_tend 	= isset($data['tend']) 		? strtotime($data['tend']) 	: '--';


		///< emp schedule
		$res = $this->getEmpSchedMinMaxTimePerday($data['employeeid'],$data['dto']);

		if($res->num_rows() > 0){
			foreach ($res->result() as $key => $row) {
			    $emp_sched[$row->idx] = array('starttime'=>$row , 'endtime'=>$row);
			}
		}

		
		$logs = $this->db->query("SELECT MAX(timein) AS timein, MAX(timeout) AS timeout, MIN(timein) AS btimein, MIN(timeout) AS btimeout FROM timesheet WHERE userid='{$data['employeeid']}' AND timein >= '{$data['dfrom']}' AND timeout < DATE_ADD('{$data['dto']}', INTERVAL 1 DAY) GROUP BY DATE_FORMAT(timein,'%Y-%m-%d')");
		
		if($logs->num_rows() > 0){
			foreach ($logs->result() as $key => $row) {

				$emp_timesheet[date('Y-m-d', strtotime($row->timein))] = array('timein'=>date('H:i:s',strtotime($row->timein)), 'timeout'=>date('H:i:s',strtotime($row->timeout)), 'btimein'=>date('H:i:s',strtotime($row->btimein)), 'btimeout'=>date('H:i:s',strtotime($row->btimeout)));
			}
		}

		///< construct ot logs per day
		foreach ($log_dates as $day) {
			$sched_start 	= isset($emp_sched[date(strtotime($day))]['starttime']) ? strtotime($emp_sched[date(strtotime($day))]['starttime']) : '';
			$sched_end 		= isset($emp_sched[date(strtotime($day))]['endtime']) ? strtotime($emp_sched[date(strtotime($day))]['endtime']) : '';
			// $sched_start 	= isset($emp_sched[date('w',strtotime($day))]['starttime']) ? strtotime($emp_sched[date('w',strtotime($day))]['starttime']) : '';
			// $sched_end 		= isset($emp_sched[date('w',strtotime($day))]['endtime']) ? strtotime($emp_sched[date('w',strtotime($day))]['endtime']) : '';

			$isBeforeSched = $isAfterSched = $isHoliday = false;

			$isHoliday = $this->attcompute->isHoliday($day);

				// echo '<pre>';
				// echo 'sched_start -> '.date('H:i',$sched_start).'sched_end -> '.date('H:i',$sched_end);
				// echo 'applied_tstart -> '.date('H:i',$applied_tstart).'applied_tend -> '.date('H:i',$applied_tend);
				// echo '</pre>';
			if($sched_start && $sched_end && $applied_tstart != '--' && $applied_tend != '--'){
				if($applied_tstart <= $sched_start && $applied_tend < $sched_end && !$isHoliday){
					$isBeforeSched = true;
				}elseif($applied_tstart > $sched_start && $applied_tend > $sched_end && !$isHoliday){
					$isAfterSched = true;
				}
			}


			///< if holiday, emp schedule will not be considered
			$this->load->model('attcompute');
			if($isHoliday){
				$sched_start = $sched_end = '';
			}



			if($isBeforeSched){
				// echo 'AM';
				// echo 'isBeforeSched > '.$isBeforeSched;
				///<-------------------------------------------------------------------------------------------------------------------------------------------------------------------
					$otstart 		= isset($emp_timesheet[$day]['btimein']) ? strtotime($emp_timesheet[$day]['btimein']) : '--';

					$otend_max = $sched_start ? $sched_start : '--';
					$otend_log = isset($emp_timesheet[$day]['btimeout']) ? strtotime($emp_timesheet[$day]['btimeout']) : '--';

					///< otend_max depends on end of otlog, start of sched, and applied ot end
					if($otend_max == '--') $otend_max = $applied_tend;
					else{
						if($applied_tend != '--') if($applied_tend < $otend_max) $otend_max = $applied_tend;
					}

					$otend = '--';
					if($otstart != '--'){
						// echo ' otstart '.$otstart.'<br>';
						if($otend_max != '--'){
							if($otend_log <= $otend_max) $otend = $otend_log;
							if($otstart > $otend_max) $otstart = '--';	
							
						}else{
							$otend = $otend_log;
						}
					}

					if($otstart != '--' && $otend == '--') $otend = $otend_max;


					///< otstart will depend on start of otlog, start of sched, and applied ot start
					if($otstart != '--'){
						if($applied_tstart != '--') if($otstart < $applied_tstart) $otstart = $applied_tstart;
					}


					///< compute for total time per day
					$ottotal = 0;
					$otsubtotal = "00:00";
					if($otstart != '--' && $otend != '--'){

						$a = new DateTime(date('H:i',$otstart));
						$b = new DateTime(date('H:i',$otend));
						$interval = $a->diff($b);

						$otsubtotal = $interval->format("%H:%I");

						$ottotal = round(  ((strtotime($otend) - strtotime($otstart)) / 3600)  , 2);
						$ottotal = $ottotal > 0 ? $ottotal : 0;

					}

					$otend = $otend != '--' ? date('h:i A', $otend) : '--';
					$otstart = $otstart != '--' ? date('h:i A', $otstart) : '--';

					# for ica-hyperion 21552
					$creditedOT = $this->getCreditedOvertime($data['employeeid'], $day, $otsubtotal);

					///< store logs per day
					$ot_logs[$day] = array('otstart'=>$otstart, 'otend'=>$otend, 'ottotal'=>$ottotal, 'otsubtotal'=>$otsubtotal, 'creditedOT'=>$creditedOT);

				///<-------------------------------------------------------------------------------------------------------------------------------------------------------------------
			}elseif($isAfterSched){
				// echo 'PM';
				///<-------------------------------------------------------------------------------------------------------------------------------------------------------------------
					// $otstart 	= $sched_end ? date('h:i A', $sched_end) : '--';
					$otend 		= isset($emp_timesheet[$day]['timeout']) ? strtotime($emp_timesheet[$day]['timeout']) : '--';

					$otstart_min = $sched_end ? $sched_end : '--';
					$otstart_log = isset($emp_timesheet[$day]['timein']) ? strtotime($emp_timesheet[$day]['timein']) : '--';


					///< otstart_min depends on start of otlog, end of sched, and applied ot start
					if($otstart_min == '--') $otstart_min = $applied_tstart;
					else{
						if($applied_tstart != '--') if($applied_tstart > $otstart_min) $otstart_min = $applied_tstart;
					}

					$otstart = '--';
					if($otend != '--'){
						if($otstart_min != '--'){
							if($otstart_log > $otstart_min) $otstart = $otstart_log;
							if($otend < $otstart_min) $otend = '--';	
							
						}else{
							$otstart = $otstart_log;
						}
					}
					// echo ' otstart '.date('H:i',$otstart).'<br>';

					if($otend != '--' && $otstart == '--') $otstart = $otstart_min;

					///< otend will depend on end of otlog, end of sched, and applied ot start
					if($otend != '--'){
						if($applied_tend != '--') if($otend > $applied_tend) $otend = $applied_tend;
					}


					///< compute for total time per day
					$ottotal = 0;
					$otsubtotal = "00:00";
					if($otstart != '--' && $otend != '--'){

						$a = new DateTime(date('H:i',$otstart));
						$b = new DateTime(date('H:i',$otend));
						$interval = $a->diff($b);

						$otsubtotal = $interval->format("%H:%I");

						$ottotal = round(  ((strtotime($otend) - strtotime($otstart)) / 3600)  , 2);
						$ottotal = $ottotal > 0 ? $ottotal : 0;

					}

					$otend = $otend != '--' ? date('h:i A', $otend) : '--';
					$otstart = $otstart != '--' ? date('h:i A', $otstart) : '--';

					# for ica-hyperion 21552
					$creditedOT = $this->getCreditedOvertime($data['employeeid'], $day, $otsubtotal);

					///< store logs per day
					$ot_logs[$day] = array('otstart'=>$otstart, 'otend'=>$otend, 'ottotal'=>$ottotal, 'otsubtotal'=>$otsubtotal, 'creditedOT'=>$creditedOT);

				///<-------------------------------------------------------------------------------------------------------------------------------------------------------------------
			}else{
				///<-------------------------------------------------------------------------------------------------------------------------------------------------------------------
					// $otstart 	= $sched_end ? date('h:i A', $sched_end) : '--';
				// echo 'whole';
					$otend 		= isset($emp_timesheet[$day]['timeout']) ? strtotime($emp_timesheet[$day]['timeout']) : '--';

					$otstart_min = $sched_end ? $sched_end : '--';
					$otstart_log = isset($emp_timesheet[$day]['btimein']) ? strtotime($emp_timesheet[$day]['btimein']) : '--';


					///< otstart_min depends on start of otlog, end of sched, and applied ot start
					if($otstart_min == '--') $otstart_min = $applied_tstart;
					else{
						if($applied_tstart != '--') if($applied_tstart > $otstart_min) $otstart_min = $applied_tstart;
					}

					$otstart = '--';
					if($otend != '--'){
						if($otstart_min != '--'){
							if($otstart_log > $otstart_min) $otstart = $otstart_log;
							if($otend < $otstart_min) $otend = '--';	
							
						}else{
							$otstart = $otstart_log;
						}
					}
					// echo ' otstart '.date('H:i',$otstart).'<br>';

					if($otend != '--' && $otstart == '--') $otstart = $otstart_min;

					///< otend will depend on end of otlog, end of sched, and applied ot start
					if($otend != '--'){
						if($applied_tend != '--') if($otend > $applied_tend) $otend = $applied_tend;
					}


					///< compute for total time per day
					$ottotal = 0;
					$otsubtotal = "00:00";
					if($otstart != '--' && $otend != '--'){

						$a = $this->attcompute->exp_time(date('H:i',$otstart));
						$b = $this->attcompute->exp_time(date('H:i',$otend));
						// echo $b;
						$otsubtotal = $b-$a;
						$otsubtotal = $this->attcompute->sec_to_hm($otsubtotal);
						// echo "<pre>"; print_r($otsubtotal); die;

					

						$ottotal = round(  ((strtotime($otend) - strtotime($otstart)) / 3600)  , 2);
						$ottotal = $ottotal > 0 ? $ottotal : 0;

					}

					$otend = $otend != '--' ? date('h:i A', $otend) : '--';
					$otstart = $otstart != '--' ? date('h:i A', $otstart) : '--';

					# for ica-hyperion 21552
					$creditedOT = $this->getCreditedOvertime($data['employeeid'], $day, $otsubtotal);

					///< store logs per day
					$ot_logs[$day] = array('otstart'=>$otstart, 'otend'=>$otend, 'ottotal'=>$ottotal, 'otsubtotal'=>$otsubtotal, 'creditedOT'=>$creditedOT);

				///<-------------------------------------------------------------------------------------------------------------------------------------------------------------------
			}	

		} ///< end loop dates

		return $ot_logs;

	}

	/**
	 * Gets list of OT applications for given approver to manage.
	 *
	 * @return stdClass Object
	 */
	function getOTAppListToManage($user="", $colhead="", $colstatus='', $status="", $prevcolstatus='',$datefrom="", $dateto="",$teachingType='',$seq_count='',$deptid='',$office=''){
		$colseq =  $colhead ? (substr($colhead,0,-4) . 'seq') : '';

		$wC = "";
    	if($datefrom && $dateto) $wC .= " AND ((b.`dfrom` BETWEEN '$datefrom' AND '$dateto') AND (b.`dto` BETWEEN '$datefrom' AND '$dateto')) ";
    	if($status)			 	 $wC .= " AND $colstatus='$status'";
		if($colseq)			 	 $wC .= " AND $colseq!='0'";
		if($seq_count)			 $wC .= " AND $colseq='$seq_count'";
    	if($prevcolstatus) 	 	 $wC .= " AND $prevcolstatus='APPROVED'";
    	if($teachingType) 	 	 $wC .= " AND c.teachingType='$teachingType'";
    	if($deptid) 	 		 $wC .= " AND c.deptid='$deptid'";
    	if($office) 	 	 	 $wC .= " AND c.office='$office'";

		$res = $this->db->query("SELECT a.id AS aid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname, c.office, a.*,b.* 
							FROM ot_app_emplist a
							INNER JOIN ot_app b ON a.`base_id`=b.`id`
							INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
							WHERE $colhead='$user' $wC");
		return $res;
	}

	/**
	 * Saves OT status change made by approver.
	 *
	 * @return stdClass Object
	 */
	function saveOTStatusChange($user='',$otid='', $status='',$colstatus='',$coldate='',$colhead='',$isLastApprover='',$base_id='',$ottotal='',$remarks='',$prev_colhead=''){
		$res = $prev_wC ='';

		if($prev_colhead) $prev_wC = " AND $prev_colhead='$user'";
		$test_q = $this->db->query("SELECT a.id FROM ot_app_emplist a INNER JOIN ot_app b ON b.id=a.base_id WHERE a.id='$otid' AND $colhead='$user' $prev_wC");
		if($test_q->num_rows() > 0){

			if($colstatus == 'hrstatus') $this->db->query("UPDATE ot_app SET approved_total='$ottotal' WHERE id='$base_id'");

			if($status == 'DISAPPROVED' || $isLastApprover){
				$res = $this->db->query("UPDATE ot_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE, status='$status',remarks='{$remarks}' WHERE id='$otid'");
				$this->db->query("UPDATE ot_app_emplist SET isread='0' WHERE id='$otid'");
			}else{
				$res = $this->db->query("UPDATE ot_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE,remarks='$remarks' WHERE id='$otid'");
			}
			if($status == 'APPROVED' && $isLastApprover){
				$this->db->query("
						INSERT INTO overtime_request (employeeid,aid,dfrom,dto,tstart,tend,total,reason,status,dateapproved,dateapplied)
						 (SELECT employeeid,a.base_id,dfrom,dto,tstart,tend,approved_total,reason,`status`,a.`timestamp`,date_applied FROM ot_app_emplist a
							 INNER JOIN ot_app b ON a.`base_id`=b.`id`
							 WHERE a.id='$otid');

					");
				$this->db->query("UPDATE ot_app_emplist SET isread='0' WHERE id='$otid'");
			}

		}

		return $res;
	}


	/**
	 * Gets number of OT applications not read by the user.
	 *
	 * @return int
	 */
	/*function getOvertimeNotif(){
		$employeeid = $this->session->userdata('username');
		$res = $this->db->query("SELECT COUNT(id) as count FROM ot_app_emplist WHERE employeeid='$employeeid' AND isread='0'");
		if($res->num_rows() > 0 ) return $res->row(0)->count;
		else 	return 0;
	}*/

	/**
	 * Gets number of PENDING OT applications by approver.
	 *
	 * @return int
	 */
	/*function getOvertimeNotifManage(){
		$user 	 = $this->session->userdata('username');
		$colhead = $prevcolstatus = ""	;

		$arr_aprvl_seq 	= array();
		$setup 			= $this->getAppSequence("OT");
		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->sortApprovalSeq($setup->row(0));
		}
		$prevkey 	 = '';
		foreach ($arr_aprvl_seq as $key => $obj) {
			if($obj['head_id'] == $user){
				$colhead = $obj['position'];
				if($key > 1) 	$prevkey  = $key - 1;
				break;
			}
		}

		if($prevkey){
			$prevcolstatus = substr($arr_aprvl_seq[$prevkey]['position'],0,-4) . 'status';
		}
		$colstatus =  $colhead ? (substr($colhead,0,-4) . 'status') : '';

		$wC = "";
		if($prevcolstatus) 	 	 $wC .= " AND $prevcolstatus='APPROVED'";
		
		$res = $this->db->query("SELECT COUNT(a.id) AS `count`
							FROM ot_app_emplist a
							INNER JOIN ot_app b ON a.`base_id`=b.`id`
							WHERE $colhead='$user' AND $colstatus='PENDING' $wC");
		if($res->num_rows() > 0 ) return $res->row(0)->count;
		else 	return 0;
	}*/

	/**
	 * Sorts approval heads based on sequence. Stores sorted details in array.
	 *
	 * @param stdClass Object $setup approval sequence details of specific OT
	 *
	 * @return array
	 */
	function sortApprovalSeq($setup){
		$this->load->model('employee');
		$user = $this->session->userdata('username');
		$deptid = $this->employee->getempdatacol('deptid');

		$chead = $dhead = $cphead = '';

		$isClusterHead = $isDeptHead = $isCpHead = false;
		$isCluster_q = $this->db->query("SELECT code FROM code_office WHERE divisionhead='$user'");
		if($isCluster_q->num_rows() > 0) $isClusterHead = true;
		$isHead_q = $this->db->query("SELECT code FROM code_office WHERE head='$user'");
		if($isHead_q->num_rows() > 0) $isDeptHead = true;
		$isCp_q = $this->db->query("SELECT code FROM code_campus WHERE campus_principal='$user'");
		if($isCp_q->num_rows() > 0) $isCpHead = true;

		if($isClusterHead) 	$chead = $user;
		if($isDeptHead) 	$dhead = $user;
		if($isCpHead) 		$cphead = $user;

		/*$dhead = $this->overtime->getDeptHead('head',		$deptid);	
		$chead = $this->overtime->getDeptHead('divisionhead',$deptid);*////< user must be divisionhead of his own department to be counted as cluster head
		$hrhead = $this->overtime->getDeptHead('head',		'HR');


		$arr_aprvl_seq = array();
		$arr_aprvl_seq[ $setup->dhseq ] = array('position'=>'dhead' , 'head_id'=>$dhead);
		$arr_aprvl_seq[ $setup->chseq ] = array('position'=>'chead' , 'head_id'=>$chead);
		$arr_aprvl_seq[ $setup->hhseq ] = array('position'=>'hrhead', 'head_id'=>$hrhead);
		$arr_aprvl_seq[ $setup->cpseq ] = array('position'=>'cphead', 'head_id'=>$cphead);
		$arr_aprvl_seq[ $setup->fdseq ] = array('position'=>'fdhead', 'head_id'=>$setup->financedir);
		$arr_aprvl_seq[ $setup->boseq ] = array('position'=>'bohead', 'head_id'=>$setup->budgetoff);
		$arr_aprvl_seq[ $setup->pseq  ] = array('position'=>'phead' , 'head_id'=>$setup->president);
		$arr_aprvl_seq[ $setup->upseq ] = array('position'=>'uphead', 'head_id'=>($setup->univphy . 
											($setup->univphyt <> ""?(",".$setup->univphyt):"")));
		//unset 0 , those not included in sequence
		unset($arr_aprvl_seq['0']);

		///< for hr head
		$hrheadtest = $dheadtest = '';
		foreach ($arr_aprvl_seq as $key => $obj) {
			if($obj['position'] == 'dhead') $dheadtest	 = $obj['head_id'];
			if($obj['position'] == 'hrhead') $hrheadtest = $obj['head_id'];
		}
		
		if($user == $dheadtest && $user == $hrheadtest) $arr_aprvl_seq[1]['head_id'] = '';

		//ksort
		ksort($arr_aprvl_seq);
		return $arr_aprvl_seq;
	}

	/**
	 * Deletes an OT app when it's still not approved/disapproved by approving head.
	 *
	 * @param int $id overtime app id
	 *
	 * @return string
	 */
	function deleteOTApp($id){
        $return = "";
        $query = $this->db->query("SELECT id, dstatus FROM ot_app_emplist WHERE id='$id' AND (dstatus='APPROVED' OR dstatus='DISAPPROVED')");
        if($query->num_rows() > 0){
            $return = "Failed to delete!. The request is already ".$query->row()->dstatus;
        }else{
            $query = $this->db->query("DELETE FROM ot_app_emplist       WHERE id='$id'");
            if($query)  $return = "Successfully Deleted!.";
            else 		$return = "Failed to delete.";
            
        }
        return $return;
    }


	function getEmpSchedMinMaxTimePerday($eid='', $date){
		$query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY))  ORDER BY dateactive DESC,starttime DESC LIMIT 1;");
		if($query->num_rows() > 0){
			$da = $query->row(0)->dateactive;
			$query = $this->db->query("SELECT MIN(starttime) AS START,MAX(endtime) AS END,idx,DAYOFWEEK,dateactive FROM employee_schedule_history WHERE dateactive = '$da' AND employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) ORDER BY dateactive DESC,starttime DESC LIMIT 1;");
	        if($query->num_rows() > 0){
	            $da = $query->row(0)->dateactive;
	            $query = $this->db->query("SELECT MIN(starttime) AS START,MAX(endtime) AS END,idx,DAYOFWEEK FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE_FORMAT(dateactive,'%Y-%m-%d %H') = DATE_FORMAT('$da','%Y-%m-%d %H') GROUP BY starttime,endtime ORDER BY starttime;"); 
	        }
	    }
        
        return $query; 
	}

	function getAttendance($eid='', $dfrom='', $dto=''){
		$dfrom = $dfrom." 00:00:00";
		$dto = $dto." 23:59:59";
		$query = $this->db->query("SELECT * FROM timesheet WHERE userid='$eid' AND ((timein BETWEEN '$dfrom' AND '$dto') AND (timeout BETWEEN '$dfrom' AND '$dto'))");
		return $query;
	}

	/**
	 * Saves new overtime application directly to overtime_request.
	 *
	 * @return int
	 */
    function saveOTAppHRDirect($arr_emplist, $user, $datefrom, $dateto, $tstart, $tend, $total, $reason, $hrhead){
    	$base_id = "";
    	$empcount = 0 ;
    	$admin_username = $this->session->userdata("username");
    	$res = $this->db->query("INSERT INTO ot_app (
    			applied_by, dfrom, 		dto, 	tstart, 	tend,	 total,	 reason, date_applied) 
    			VALUES (
    			'$user', '$datefrom', '$dateto', '$tstart', '$tend', '$total', '$reason', CURRENT_DATE)
    			");
    	if($res)  	$base_id = $this->db->insert_id();

    	if($base_id){
	    	foreach ($arr_emplist as $employeeid) {
				$res = $this->db->query("
					INSERT INTO ot_app_emplist (base_id, employeeid, status, approver_admin) VALUES ('$base_id', '$employeeid', 'APPROVED', '$admin_username')
				");
				if($res)  	$aid = $this->db->insert_id();
				if($aid) 	$res = $this->db->query("
									INSERT INTO overtime_request (
			    			aid, employeeid, dfrom, 		dto, 	tstart, 	tend,	 total,	 reason, status, dateapproved, dateapplied) 
			    			VALUES (
			    			'$base_id', '$employeeid', '$datefrom', '$dateto', '$tstart', '$tend', '$total', '$reason', 'APPROVED',CURRENT_DATE,CURRENT_DATE)
			    			");
				if($res) $empcount++;
			}
		}

		return $empcount;

    }
	

	//APPROVED IF THE HEAD IS HR -- Added 5-18-17
	function chkHRapproved($base_id="",$dhead="",$hrhead="",$dseq=""){
		if($dhead == $hrhead && $dseq > 0)
		{
			$date = date("Y-m-d");
			$this->db->query("UPDATE ot_app_emplist SET dstatus = 'APPROVED' , ddate = '{$date}' WHERE base_id = $base_id");
		}
	}


	# for ica-hyperion 21535
	# by justin (with e)
	function isAllowedToDeleteRequest($base_id, $employeeid, $dfrom, $dto){
		$this->load->model('employee');
		$isAbleToDeleteRequest = true;
		$teachingType = $this->employee->getempdatacol("teachingType", $employeeid);

		$table = "attendance_confirmed_nt";
		if($teachingType == "teaching") $table = "attendance_confirmed";

		$q_findEmployee = $this->db->query("SELECT * 
											FROM $table 
											WHERE employeeid='$employeeid' AND 
												((cutoffstart <='$dfrom' AND cutoffend >='$dfrom') 
												OR 
												(cutoffstart <='$dto' AND cutoffend >='$dto'));
										   ")->result();

		if(count($q_findEmployee) > 0) $isAbleToDeleteRequest = false;

		return $isAbleToDeleteRequest;
	}

	function deleteOvertimeRequest($base_id){
		$q_deleteOnOTApp = $this->db->query("DELETE FROM ot_app WHERE id='$base_id'");
		$q_deleteOnOTAppEmplist = $this->db->query("DELETE FROM ot_app_emplist WHERE base_id='$base_id'");
		$q_deleteOnOvertimeRequest = $this->db->query("DELETE FROM overtime_request WHERE aid='$base_id'");

		$userid = $this->session->userdata("username");
		$q_saveHistory = $this->db->query("INSERT INTO ot_app_history (userid, request_id, status) VALUES ('$userid', '$base_id', 'DELETED')");

		return array($q_deleteOnOTApp, $q_deleteOnOTAppEmplist, $q_deleteOnOvertimeRequest, $q_saveHistory);
	}
	# end for ica-hyperion 21535

	# for ica-hyperion 21668
	function isOvertimeSetupIsExist($code_status, $ot_type){
		$is_continue = true;

		$q_overtime_setup = $this->db->query("SELECT * FROM code_overtime WHERE code_status='$code_status' AND ot_types='$ot_type';")->result();
		if(count($q_overtime_setup) > 0) $is_continue = false;

		return $is_continue;
	}

	function saveNewOvertimeSetup($data){
		$q_save_overtime_setup = $this->db->insert('code_overtime', $data);
		
		return $q_save_overtime_setup;
	}

	function findOvertimeSetupId($code_status, $ot_type){
		$id = "";

		$q_overtime_id = $this->db->query("SELECT id FROM code_overtime WHERE code_status='$code_status' AND ot_types='$ot_type';")->result();
		foreach ($q_overtime_id as $row) $id = $row->id;

		return $id;
	}

	function saveUpdateOvertimeSetup($code_status, $ot_type, $data){
		$id = $this->findOvertimeSetupId($code_status, $ot_type);

								 $this->db->where('id', $id);
		$q_save_overtime_setup = $this->db->update('code_overtime', $data);

		return $q_save_overtime_setup;
	}

	function deleteOvertimeSetup($code_status){
		$q_delete_overtime_setup = $this->db->query("DELETE FROM code_overtime WHERE code_status='$code_status';");

		return $q_delete_overtime_setup;
	}

	function getOvertimeSetupList(){
		$q_overtime_setup = $this->db->query("SELECT DISTINCT b.code, b.description
											  FROM code_overtime a
											  INNER JOIN code_status b ON b.code = a.code_status;
											")->result();

		return $q_overtime_setup;
	}

	function getOvertimeSetupInfo($code_status){
		$q_overtime_setup = $this->db->query("SELECT * FROM code_overtime WHERE code_status = '$code_status';")->result();

		return $q_overtime_setup;
	}

	function getOvertimeSetup($status, $ot_types){
		$data_arr = array();
		$query = $this->db->query("SELECT * FROM code_overtime WHERE code_status = '$status' AND ot_types = '$ot_types' ");
		if($query->num_rows > 0){
			foreach($query->result_array() as $key => $value){
				$data_arr['percent'] = $value['percent'];
				$data_arr['excess_percent'] = $value['excess_percent'];
				$data_arr['regular_percent'] = $value['regular_percent'];
				$data_arr['regular_percent_excess'] = $value['regular_percent_excess'];
				$data_arr['other_percent'] = $value['other_percent'];
				$data_arr['other_percent_excess'] = $value['other_percent_excess'];
			}
		}
		else{ /*if no existing setup*/
				$data_arr['percent'] = 100;
				$data_arr['excess_percent'] = 100;
				$data_arr['regular_percent'] = 100;
				$data_arr['regular_percent_excess'] = 100;
				$data_arr['other_percent'] = 100;
				$data_arr['other_percent_excess'] = 100;
		}

		return $data_arr;
	}
	# end for ica-hyperion 21668

	function getOvertimeAppRequest($id){
		return $this->db->query("SELECT a.id AS otid, a.*,b.* ,REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname
    							FROM ot_app_emplist a
							INNER JOIN ot_app b ON a.base_id = b.id
							INNER JOIN employee c ON a.employeeid = c.employeeid
							WHERE b.id='$id';")->result();
	}

	public function hasFiledOT($employeeid, $datefrom, $dateto, $timefrom="", $timeto=""){
    	$where_clause = "";
		$where_clause = " AND tstart = '$timefrom' AND tto = '$timeto' ";
		return $this->db->query("SELECT * FROM ot_app a INNER JOIN ot_app_emplist b ON a.id = b.base_id WHERE employeeid = '$employeeid' AND (dfrom BETWEEN '$datefrom' AND '$dateto' OR dto BETWEEN '$datefrom' AND '$dateto') ")->num_rows();
    }

    public function modifyOVertimeApplication($datefrom, $dateto, $tstart, $tend, $total, $reason,$base_id_edit){
    	return $this->db->query("UPDATE ot_app SET dfrom = '$datefrom', dto = '$dateto', tstart = '$tstart', tend = '$tend', total = '$total', reason = '$reason' WHERE id = '$base_id_edit'");
    }

    public function saveOTSched($base_id, $dateactive){
    	$is_exists = $this->db->query("SELECT * FROM ot_schedref WHERE base_id = '$base_id' ");
    	if($is_exists->num_rows() == 0) $this->db->query("INSERT INTO ot_schedref (base_id, dateactive) VALUES ('$base_id', '$dateactive') ");
    	else $this->db->query("UPDATE ot_schedref SET dateactive = '$dateactive' WHERE base_id = '$base_id'");
    }

} //endoffile