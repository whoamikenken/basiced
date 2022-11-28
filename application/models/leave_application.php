<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave_application extends CI_Model {

	function checkLeaveBalance($employeeid='',$ltype='',$datefrom='',$dateto=''){
		$balance = $credit = $availed = 0;
		$isHistory = "current";
		$haveCredits = true;
		$bal_q = $this->db->query("SELECT balance,credit,avail FROM employee_leave_credit WHERE employeeid='$employeeid' AND leavetype='$ltype' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");

		if($bal_q->num_rows() > 0){
			$balance = $bal_q->row(0)->balance;
			$credit = $bal_q->row(0)->credit;
			$availed = $bal_q->row(0)->avail;
			$isHistory = "current";
		}else{
			$bal_q = $this->db->query("SELECT balance,credit,avail FROM employee_leave_credit_history WHERE employeeid='$employeeid' AND leavetype='$ltype' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");
			if($bal_q->num_rows() > 0){
				$balance = $bal_q->row(0)->balance;
				$credit = $bal_q->row(0)->credit;
				$availed = $bal_q->row(0)->avail;
				$isHistory = "old";
			}else{
				$haveCredits = false;
			}
		}
		return array($haveCredits,$balance,$credit,$availed,$isHistory);
	}

	function checkExistingLeaveApp($employeeid='',$status='',$datefrom='',$dateto='',$nodays=0,$sched_affected=''){
		$exist_count = 0;
		$leave_q = $this->db->query("SELECT * FROM leave_app_emplist a INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
						 WHERE a.employeeid='$employeeid' AND a.status='$status' 
						 AND (('$datefrom' BETWEEN datefrom AND dateto) OR ('$dateto' BETWEEN datefrom AND dateto))");

		foreach ($leave_q->result() as $key => $row) {
			$sched_aff_q = explode(',', $row->sched_affected);
			
        	if($nodays == 0.5 && $row->nodays == 0.5){
		    	if(sizeof(array_intersect($sched_aff_q, $sched_affected)) > 0){
		    		$exist_count++;
		    	}
		    }else{
		        $exist_count++;
		    }
		}

		return $exist_count;
	}

    /**
	 * Gets request details based on leave app id.
	 *
	 * @param string $leaveid (Default: "")
	 *
	 * @return stdClass Object
	 */
    function getAppSequencePerLeave($leaveid=''){
    	$res = $this->db->query("SELECT * FROM leave_app_emplist a INNER JOIN leave_app_base b ON a.`base_id`=b.`id` WHERE a.id='$leaveid'");
    	return $res;
    }

    function getAppSequence($type=""){
    	$res = $this->db->query("SELECT * FROM code_request_form WHERE code_request='$type'");
    	return $res;
    }


    /**
	 * Inserts new leave application in base table and gets last inserted id.
	 *
	 * @return int
	 */
    function insertBaseLeaveApp($user, $ltype, $othleave, $datefrom, $dateto, $paid, $nodays, $ishalfday, $sched_affected, $leave_category, $category, $seminar, $organizer, $venue, $location, $fee, $deadline, $title, $transporation, $accomodation, $others, $total, $reason, $dhead, $chead, $hrhead, $cphead, $dphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $dpseq, $fdseq, $boseq, $pseq, $upseq, $final_file, $size, $filetype,$liquidated="",$dayscount=""){
    	$id = "";
    	if($this->session->userdata("usertype") == "EMPLOYEE") $liquidated = "NO";
    	$res = $this->db->query("INSERT INTO leave_app_base (
    			applied_by, `type`, other, datefrom, 	dateto, 	paid, 	nodays, dayscount,	 isHalfDay, sched_affected, leave_category, category, seminar, organizer, venue, location, fee, deadline, title, transportation, accomodation, others, total,	 reason,liquidated,	 dhead,		chead,	 hrhead, cphead, dphead,	fdhead, 	bohead, 	phead, 	uphead, 	dseq, 	cseq, 	hrseq, cpseq, dpseq, 	fdseq, 		boseq, 		pseq, 	upseq, date_applied) VALUES (
    			'$user', '$ltype', '$othleave', '$datefrom', '$dateto', '$paid', '$nodays', '$dayscount', '$ishalfday', '$sched_affected', '$leave_category', '$category', '$seminar', ".$this->db->escape($organizer).", ".$this->db->escape($venue).", ".$this->db->escape($location).", '$fee', '$deadline', '$title', '$transporation', '$accomodation', '$others', '$total', ".$this->db->escape($reason).",'$liquidated', '$dhead', '$chead', '$hrhead', '$cphead', '$dphead', '$fdhead', '$bohead', '$phead', '$uphead', '$dseq', '$cseq', '$hrseq', '$cpseq', '$dpseq', '$fdseq', '$boseq', '$pseq', '$upseq', CURRENT_DATE)
    			");
    	if($res){
    		$dbname = $this->db->database_files;
    		$id = $this->db->insert_id();
    		$this->db->query("INSERT INTO $dbname.leave_app_files (base_id, content, mime, size) VALUES ('$id', '$final_file', '$filetype', '$final_file') ");
    	}
    	return $id;
    }

    /**
	 * Inserts leave app in secondary table for list of employees.
	 *
	 * @return Array
	 */
    function insertLeaveAppEmpList($base_id, $user, $teachingType, $dstatus, $ddate){
    	$empcount = $isread = 0;
    	$arr_data_failed = array();
		$isread = 1;

		$res = $this->db->query("
			INSERT INTO leave_app_emplist (base_id, employeeid, teachingType, dstatus, ddate, isread) VALUES ('$base_id', '$user', '$teachingType','$dstatus', '$ddate', '$isread')
		");

		if($res) $empcount++;
		else array_push($arr_data_failed, $user);

		return array($empcount,$arr_data_failed);
    }

    function modifyLeaveDetails($base_id='',$paid='',$nodays='',$isHalfDay='',$sched_affected='',$reason='', $final_file='', $size='', $filetype='',$title='',$organizer='',$venue='',$seminar='',$location='',$dayscount='', $ltype= ''){
    	$update_clause = "";
    	if($ltype) $update_clause .= "type = '$ltype',";
    	if($final_file && $size && $file_type){
    		// $update_clause = "content='$final_file', size='$size', mime='$file_type',";
    	}
    	// echo "<pre>"; print_r($update_clause); die;
    	
    	$res = $this->db->query("UPDATE leave_app_base SET paid='$paid',
    												nodays='$nodays',
    												dayscount='$dayscount',
    												title='$title',
    												organizer={$this->db->escape($organizer)},
    												venue={$this->db->escape($venue)},
    												seminar='$seminar',
    												location={$this->db->escape($location)},
    												isHalfDay='$isHalfDay',
    												sched_affected='$sched_affected',
    												$update_clause
    												reason={$this->db->escape($reason)}
    											WHERE id='$base_id'
    												");
    	if($res) return 1;
    	else return 0;
    }


	function saveLeaveAppHRDirect($base_id='', $teachingType='', $ltype='', $other='', $ltypetemp='', $employeeid='', $sched_affected='', $category='', $seminar='', $organizer='', $venue='', $location='', $fee='', $deadline='', $title='', $transportation='', $accomodation='', $others='',$total='', $datefrom='', $dateto='', $nodays=0, $availed=0, $balance=0, $paid='NO'){
		$empcount = $isread = 0;
    	$arr_data_failed = array();
		$isread = 0;
		$admin_username = $this->session->userdata("username");
		$res = $this->db->query("
			INSERT INTO leave_app_emplist (base_id, employeeid, teachingType, status, isread, approver_admin) VALUES ('$base_id', '$employeeid', '$teachingType', 'APPROVED','$isread', '$admin_username')
		");

		$leave_id = $this->db->insert_id();

		if($leave_id){
			$insert_q = $this->db->query("
									INSERT INTO leave_request (aid,employeeid,leavetype,other,fromdate,todate,paid,dateapplied,no_days,isHalfDay,sched_affected,category,seminar,organizer,venue,location,fee,deadline,title,remarks,status,dateapproved)
									 (SELECT a.id , a.employeeid, b.type, b.other, b.datefrom, b.dateto, b.paid, b.date_applied, b.nodays, b.isHalfDay, b.sched_affected, b.category, b.seminar, b.organizer, b.venue, b.location, b.fee, b.deadline, b.title, b.reason, 'APPROVED', CURRENT_DATE
										FROM leave_app_emplist a
										INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
										 WHERE a.id='$leave_id');

								");
			$this->insertSeminarInformation($leave_id);
			$id = $this->db->insert_id();
			// $q_date_processed = $this->db->query("UPDATE leave_request SET date_processed = `timestamp` WHERE id='$id'");
		}


    	if($ltype != 'ABSENT' && $insert_q && $paid == "YES"){
			if($ltype == 'other' && $paid == 'YES'){
				$this->db->query("UPDATE employee_leave_credit SET avail='".($availed+$nodays)."', balance='".($balance-$nodays)."' WHERE employeeid='$employeeid' AND leavetype='$other' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");
			}else{

				$this->db->query("UPDATE employee_leave_credit SET avail='".($availed+$nodays)."', balance='".($balance-$nodays)."' WHERE employeeid='$employeeid' AND leavetype='$ltypetemp' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");
			}
			if(is_string($sched_affected)) $sched_affected = explode(',', $sched_affected);

			///< insert to timesheet
			if(sizeof($sched_affected) > 0 && $paid == "YES") $this->insertToTimesheetFromLeave($employeeid,$sched_affected,$datefrom,$ltype);
		}

		if($res) $empcount++;
		else array_push($arr_data_failed, $employeeid);

		return array($empcount,$arr_data_failed);

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

    public function updateLeaveAppFile($filename, $id){
    	return $this->db->query("UPDATE leave_app_base SET filename = '$filename' WHERE id = '$id'");
    }

    /**
	 * Gets list of employee leave applications.
	 *
	 * @return stdClass Object
	 */
    function getEmpLeaveHistory($employeeid="", $status="", $leaveid="", $isread=''){
    	$wC = "";
    	// if($datefrom && $dateto) $wC .= " AND b.`date_applied` BETWEEN '$datefrom' AND '$dateto'";
    	if($status)				 $wC .= " AND a.`status`='$status'";
    	if($leaveid)			 $wC .= " AND a.id='$leaveid'";
    	// if($isread <> '')		 $wC .= " AND a.isread='$isread'";
        $res = $this->db->query("SELECT a.id AS leaveid, a.*,b.* ,REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname
        							FROM leave_app_emplist a
									INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
									INNER JOIN employee c ON a.employeeid=c.employeeid
									WHERE a.employeeid='$employeeid' 
									$wC")->result();
									// OR b.applied_by='$employeeid' 
        return $res;
	}

	/**
	 * Gets Leave details.
	 *
	 * @param string $leaveid (Default: "")
	 * @param string $colstatus (Default: "")
	 *
	 * @return array
	 */
	function getLeaveDetails($leaveid='',$colstatus=''){
		$data = array();
		$res = $this->db->query("SELECT a.id AS leaveid, a.remarks as dis_remarks, a.employeeid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname,e.description AS epos, d.description AS edept, a.*,b.* 
									FROM leave_app_emplist a
									INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
									INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
									LEFT JOIN code_position e ON c.positionid = e.positionid
									LEFT JOIN code_office d ON c.office = d.code 
									WHERE a.id='$leaveid'");

		if($res->num_rows() > 0){
			foreach ($res->result() as $obj) 
			{
				$data['base_id'] 		= $obj->base_id;
				$data['leaveid'] 		= $obj->leaveid;
				$data['employeeid'] 	= $obj->employeeid;
				$data['leavetype'] 		= $obj->type;
				$data['leavecategory'] 	= $obj->leave_category;
				$data['othertype'] 		= $obj->other;
				$data['paid'] 			= $obj->paid;
				$data['date_applied'] 	= $obj->date_applied;
				$data['dfrom'] 			= $obj->datefrom;
				$data['dto'] 			= $obj->dateto;
				$data['nodays'] 		= $obj->nodays;
				$data['dayscount'] 		= $obj->dayscount;
				$data['isHalfDay'] 		= $obj->isHalfDay;
				$data['sched_affected'] = $obj->sched_affected;
				$data['reason'] 		= $obj->reason;
				$data['remarks'] 		= $obj->dis_remarks;
				$data['status'] 		= $obj->status;
				if($colstatus) 	$data['colstat']= $obj->$colstatus; 
				$data['fullname'] 		= $obj->fullname;
				$data['pos'] 			= $obj->epos;
				$data['edept']  		= $obj->edept;
				$data['rem']			= $obj->remarks;
				$data['rem']			= $obj->remarks;
				$data['category']		= $obj->category;
				$data['organizer']		= $obj->organizer;
				$data['seminar']		= $obj->seminar;
				$data['title']			= $obj->title;
				$data['venue']			= $obj->venue;
				$data['location']		= $obj->location;
				$data['fee']			= str_replace(",", "", $obj->fee);
				$data['deadline']		= $obj->deadline;
				$data['title']		= $obj->title;
				$data['transportation']	= str_replace(",", "", $obj->transportation);
				$data['accomodation']	= str_replace(",", "", $obj->accomodation);
				$data['others']			= str_replace(",", "", $obj->others);
				$data['total']			= $obj->total;
				$data['filename']		= $obj->filename;
				$data['mime']			= $obj->mime;
				$data['notdeduct']		= $obj->notdeduct;
  			}
		}
		return $data;
	}


	
	/**
	 * Gets list of leave applications for given approver to manage.
	 *
	 * @return stdClass Object
	 */
	function getLeaveAppListToManage($user="", $colhead="", $colstatus='', $status="", $prevcolstatus='',$datefrom="", $dateto="",$teachingType='',$code_request='',$seq_count='',$deptid='',$office=''){

		if($code_request){
			$code_request = str_replace('NON','',$code_request);
			$code_request = str_replace('HEAD','',$code_request);
		}

		$colseq =  $colhead ? (substr($colhead,0,-4) . 'seq') : '';
		$sample =  $colhead ? (substr($colhead,0,-4) . 'status') : '';


		$wC = "";
    	if($datefrom && $dateto) $wC .= " AND (b.datefrom BETWEEN '$datefrom' AND '$dateto') AND (b.dateto BETWEEN '$datefrom' AND '$dateto')";
    	if($status)			 	 $wC .= " AND $colstatus='$status'";
		if($colseq)			 	 $wC .= " AND $colseq!='0'";
		if($seq_count)			 $wC .= " AND $colseq='$seq_count'";
    	if($prevcolstatus) 	 	 $wC .= " AND $prevcolstatus='APPROVED'";
    	if($teachingType) 	 	 $wC .= " AND c.teachingtype='$teachingType'";
    	if($deptid)		 		 $wC .= " AND c.deptid='$deptid'";
    	if($office)		 		 $wC .= " AND c.office='$office'";
    	if($code_request) 		 $wC .= " AND (b.type='$code_request' OR b.other='$code_request')";

		if($status == "PENDING"){
			$res = $this->db->query("SELECT a.id AS aid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname, c.deptid, c.office, a.*,b.* 
							FROM leave_app_emplist a
							INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
							INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
							WHERE $colhead='$user' AND status !='CANCELLED' $wC");
		}else if($status == "APPROVED"){
			$res = $this->db->query("SELECT a.id AS aid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname, c.deptid, c.office, a.*,b.* 
							FROM leave_app_emplist a
							INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
							INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
							WHERE $colhead='$user' AND status !='CANCELLED' AND $sample = 'APPROVED' AND (b.datefrom BETWEEN '$datefrom' AND '$dateto') AND (b.dateto BETWEEN '$datefrom' AND '$dateto' ) $wC");
		}else{
			$res = $this->db->query("SELECT a.id AS aid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname, c.deptid, c.office, a.*,b.* 
							FROM leave_app_emplist a
							INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
							INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
							WHERE $colhead='$user' AND status !='CANCELLED' AND (b.datefrom BETWEEN '$datefrom' AND '$dateto') AND (b.dateto BETWEEN '$datefrom' AND '$dateto') $wC");
		}
		// echo "<pre>"; print_r($this->db->last_query()); die;
		// $res = $this->db->query("SELECT a.id AS aid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname, c.deptid, a.*,b.* 
		// 					FROM leave_app_emplist a
		// 					INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
		// 					INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
		// 					WHERE $colhead='$user' $wC");

		return $res;
	}

	/**
	 * Saves leave status change made by approver.
	 *
	 * @return stdClass Object
	 */
	function saveLeaveStatusChange($user='', $leave_id='', $status='',$colstatus='',$coldate='',$colhead='',$isLastApprover='',$base_id='',$remarks='',$prev_colhead='', $endorse = '', $notdeduct = ''){
		$res = $prev_wC ='';
		$return = '';
		$paid = "";
		if($prev_colhead) $prev_wC = " AND $prev_colhead='$user'";
		$test_q = $this->db->query("SELECT a.id FROM leave_app_emplist a INNER JOIN leave_app_base b ON b.id=a.base_id WHERE a.id='$leave_id' AND $colhead='$user' $prev_wC");
		if($test_q->num_rows() > 0){

				
				if($status == 'APPROVED' && $isLastApprover){
					$leave_q = $this->db->query("SELECT a.employeeid,b.type,b.other,b.datefrom,b.dateto,b.nodays, b.isHalfDay, b.sched_affected, b.paid FROM leave_app_emplist a INNER JOIN leave_app_base b ON a.`base_id`=b.`id` WHERE a.id='$leave_id'");

					$ishalfday = 0;
					$sched_affected = array();
					$sched_affected_string = '';
					$datefrom = $dateto = $employeeid = $ltype = $other = $nodays = '';


					if($leave_q->num_rows() > 0){
						$l_q = $leave_q->row(0);
					    $employeeid 			= $l_q->employeeid;
					    $ishalfday 				= $l_q->isHalfDay;
					    $sched_affected_string 	= $l_q->sched_affected;
					    $datefrom 				= $l_q->datefrom;
					    $dateto 				= $l_q->dateto;
					    $ltype 					= $l_q->type;
					    $other 					= $l_q->other;
					    $nodays 				= $l_q->nodays;
						$paid					= $l_q->paid;
					}
					if($ishalfday && $sched_affected_string && $datefrom) $sched_affected = explode(',', $sched_affected_string);


					///< check for existing applications
					$exist_app = $this->checkExistingLeaveApp($employeeid,'APPROVED',$datefrom,$dateto,$nodays,$sched_affected);
					// if($exist_app) {return array('err_code'=>0,'msg'=>'Employee already have approved applications for this date.');}

					$ltypetemp = $ltype;

					if($ltype != 'ABSENT'){
						///< check for balances
						if($ltype == 'other'){
							list($haveCredits,$balance,$credit,$availed,$isHistory) = $this->leave_application->checkLeaveBalance($employeeid,$other,$datefrom,$dateto);
						}else{
							list($haveCredits,$balance,$credit,$availed,$isHistory) = $this->leave_application->checkLeaveBalance($employeeid,$ltype,$datefrom,$dateto);
							// echo "<pre>";print_r($this->db->last_query());die;
							if(!$haveCredits && $ltype == 'EL'){
								$ltypetemp = 'VL';
								list($haveCredits,$balance,$credit,$availed,$isHistory) = $this->leave_application->checkLeaveBalance($employeeid,$ltypetemp,$datefrom,$dateto);
								
							}elseif(!$haveCredits && $ltype == 'VL'){
								$ltypetemp = 'EL';
								list($haveCredits,$balance,$credit,$availed,$isHistory) = $this->leave_application->checkLeaveBalance($employeeid,$ltypetemp,$datefrom,$dateto);
							}
						}
						// echo "<pre>"; print_r($haveCredits);
						if(!$notdeduct && ($ltype != "PL-SEM" || $ltype != "PL-M" || $ltype != "PL-G")){
							if(!$haveCredits) {return array('err_code'=>0,'msg'=>"Employee have no leave credits for the given date.");}
							if($paid == "YES"){
								if($balance > 0){
									if($balance >= $nodays){

									}else {return array('err_code'=>0,'msg'=>"Insufficient leave balance.");}
								}else {return array('err_code'=>0,'msg'=>"Employee have no remaining balance.");}
							}
						}
					}
					$insert_q = $this->db->query("
									INSERT INTO leave_request (aid,employeeid,leavetype,other,fromdate,todate,paid,dateapplied,no_days,isHalfDay,sched_affected,remarks,status,dateapproved)
									 (SELECT a.id , a.employeeid, b.type, b.other, b.datefrom, b.dateto, b.paid, b.date_applied, b.nodays, b.isHalfDay, b.sched_affected, b.reason, '$status', CURRENT_DATE
										FROM leave_app_emplist a
										INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
										 WHERE a.id='$leave_id');

								");

					if($insert_q){
						$this->insertSeminarInformation($leave_id);
						if($ltype != 'ABSENT'){
							if(!$notdeduct){
								if($ltype == 'other' && $paid == 'YES'){
									if ($isHistory == "current") {
										$this->db->query("UPDATE employee_leave_credit SET avail='".($availed+$nodays)."', balance='".($balance-$nodays)."' WHERE employeeid='$employeeid' AND leavetype='$other' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");
									}elseif($isHistory == "old"){
										$this->db->query("UPDATE employee_leave_credit_history SET avail='".($availed+$nodays)."', balance='".($balance-$nodays)."' WHERE employeeid='$employeeid' AND leavetype='$other' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");
									}
								}else{
									if ($isHistory == "current") {
										$this->db->query("UPDATE employee_leave_credit SET avail='".($availed+$nodays)."', balance='".($balance-$nodays)."' WHERE employeeid='$employeeid' AND leavetype='$ltypetemp' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");
									}elseif($isHistory == "old"){
										$this->db->query("UPDATE employee_leave_credit_history SET avail='".($availed+$nodays)."', balance='".($balance-$nodays)."' WHERE employeeid='$employeeid' AND leavetype='$ltypetemp' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");
									}
									
								}
							}
							
							///< insert to timesheet
							if(sizeof($sched_affected) > 0 && $paid == "YES") $this->insertToTimesheetFromLeave($employeeid,$sched_affected,$datefrom,$ltype);
						}

						$this->db->query("UPDATE leave_app_emplist SET isread='0', notdeduct = '$notdeduct' WHERE id='$leave_id'");
					}else {return array('err_code'=>0,'msg'=>"Failed to save.");}

				}


				if($status == 'DISAPPROVED' || $isLastApprover){
					$res = $this->db->query("UPDATE leave_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE, status='$status',remarks='{$remarks}', notdeduct = '$notdeduct' WHERE id='$leave_id'");
					$this->db->query("UPDATE leave_app_emplist SET isread='0', notdeduct = '$notdeduct' WHERE id='$leave_id'");

					if($res){
						if($isLastApprover) $status = "NOTED";
						if($endorse) $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$endorse);
						else $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$status);
					}

				}else{
					$res = $this->db->query("UPDATE leave_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE,remarks='$remarks', notdeduct = '$notdeduct' WHERE id='$leave_id'");

						if($isLastApprover) $status = "NOTED";
						if($endorse) $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$endorse);
						else $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$status);
				}


		} ///< end if test_q

		return $return;
	}




	# new function for ica-hyperion 21194
	# by justin (with e)
	# > kapag si admin nag apply ng leave request, tapos direct approved.. isa-save nya leave_request
	function leaveDirectApproved($leave_id){
		$status = 'APPROVED';
		# echo '<pre>'. "SELECT a.employeeid,b.type,b.other,b.datefrom,b.dateto,b.nodays, b.isHalfDay, b.sched_affected FROM leave_app_emplist a INNER JOIN leave_app_base b ON a.`base_id`=b.`id` WHERE a.id='{$leave_id}'"; die;
		$leave_q = $this->db->query("SELECT a.employeeid,b.type,b.other,b.datefrom,b.dateto,b.nodays, b.isHalfDay, b.sched_affected, b.paid FROM leave_app_emplist a INNER JOIN leave_app_base b ON a.`base_id`=b.`id` WHERE a.id='$leave_id'");

		$ishalfday = 0;
		$sched_affected = array();
		$sched_affected_string = '';
		$datefrom = $dateto = $employeeid = $ltype = $other = $nodays = '';


		if($leave_q->num_rows() > 0){
			$l_q = $leave_q->row(0);
		    $employeeid 			= $l_q->employeeid;
		    $ishalfday 				= $l_q->isHalfDay;
		    $sched_affected_string 	= $l_q->sched_affected;
		    $datefrom 				= $l_q->datefrom;
		    $dateto 				= $l_q->dateto;
		    $ltype 					= $l_q->type;
		    $other 					= $l_q->other;
		    $nodays 				= $l_q->nodays;
			$paid					= $l_q->paid;
		}
		if($ishalfday && $sched_affected_string && $datefrom) $sched_affected = explode(',', $sched_affected_string);


		///< check for existing applications
		$exist_app = $this->checkExistingLeaveApp($employeeid,'APPROVED',$datefrom,$dateto);
		if($exist_app) {return array('err_code'=>0,'msg'=>'Employee already have approved applications for this date.');}

		if($ltype != 'ABSENT'){
			///< check for balances
			if($ltype == 'other'){
				list($haveCredits,$balance,$credit,$availed) = $this->leave_application->checkLeaveBalance($employeeid,$other,$datefrom,$dateto);
			}else{
				list($haveCredits,$balance,$credit,$availed) = $this->leave_application->checkLeaveBalance($employeeid,$ltype,$datefrom,$dateto);
			}

			if(!$haveCredits) {return array('err_code'=>0,'msg'=>"Employee have no leave credits for the given date.");}

			if($balance > 0){
				if($balance >= $nodays){

				}else {return array('err_code'=>0,'msg'=>"Insufficient leave balance.");}
			}else {return array('err_code'=>0,'msg'=>"Employee have no remaining balance.");}
		}
		$insert_q = $this->db->query("
						INSERT INTO leave_request (aid,employeeid,leavetype,other,fromdate,todate,paid,dateapplied,no_days,isHalfDay,sched_affected,remarks,status,dateapproved)
						 (SELECT a.id , a.employeeid, b.type, b.other, b.datefrom, b.dateto, b.paid, b.date_applied, b.nodays, b.isHalfDay, b.sched_affected, b.reason, '$status', CURRENT_DATE
							FROM leave_app_emplist a
							INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
							 WHERE a.id='$leave_id');

					");

		if($insert_q){
			if($ltype != 'ABSENT'){
				if($ltype == 'other' && $paid == 'YES'){
					$this->db->query("UPDATE employee_leave_credit SET avail='".($availed+$nodays)."', balance='".($balance-$nodays)."' WHERE employeeid='$employeeid' AND leavetype='$other' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");
				}else{
					$this->db->query("UPDATE employee_leave_credit SET avail='".($availed+$nodays)."', balance='".($balance-$nodays)."' WHERE employeeid='$employeeid' AND leavetype='$ltype' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");
				}
				
				///< insert to timesheet
				if(sizeof($sched_affected) > 0 && $paid == "YES") $this->insertToTimesheetFromLeave($employeeid,$sched_affected,$datefrom,$ltype);
			}


			$this->db->query("UPDATE leave_app_emplist SET isread='0' WHERE id='$leave_id'");
		}else {return array('err_code'=>0,'msg'=>"Failed to save.");}

		return array('err_code'=>0,'msg'=>"Success");

	}

	# > direct approved...
	function leaveDirectApprovedByModified($id, $base_id){
		$dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";

		# tatangalin lahat ng seq..
		$this->db->query("UPDATE leave_app_base SET dseq='{$dseq}', cseq='{$cseq}', hrseq='{$hrseq}', cpseq='{$cpseq}', fdseq='{$fdseq}', boseq='{$boseq}', pseq='{$pseq}', upseq='{$upseq}' WHERE id='{$base_id}'");

		# update to approved status.
		$this->db->query("UPDATE leave_app_emplist SET status='APPROVED' WHERE id='{$id}'");
	}

	# > displayed list of result ng mga inapply ni admin...
	function getEmpLeaveListByAdmin($category,$ltype,$dfrom,$dto,$deptid,$othtype){
		$empList = array();
		$this->load->model('utils');
		$codedayofweek = array("0"=>"SU", "1"=>"M", "2"=>"T", "3"=>"W", "4"=>"TH", "5"=>"F", "6"=>"S");

		# status column
		$status_col = array(
							"status",
							"dstatus",
							"cstatus",
							"hrstatus",
							"cpstatus",
							"fdstatus",
							"bostatus",
							"pstatus",
							"upstatus"
						   );

		# get muna yung username ni admin
		// $user = $this->session->userdata("username"); ///< @Angelica lahat kita ni admin

		# query ng employee list
		$sql = "SELECT b.id AS id, a.id AS aid, b.`base_id` AS baseID, b.`employeeid` AS empID, CONCAT(c.`lname`, ', ', c.`fname`, ' ', c.`mname` ) AS fullname,
				a.`type` AS ltype, a.`other` AS otype, d.`description` AS lDesc, a.`nodays`, a.`datefrom` AS dfrom, a.`dateto` AS dto, b.`status`, a.`date_applied`, a.liquidated, a.ishalfday, a.paid, a.applied_by
				FROM leave_app_base a
				INNER JOIN leave_app_emplist b ON b.`base_id` = a.`id`
				INNER JOIN employee c ON c.`employeeid` = b.`employeeid`
				INNER JOIN code_request_form d ON d.`code_request` = a.`type`
				WHERE a.id != '' ";

		# kapag may sinelect na category
		if($category) $sql .= " AND b.`status`='". $category ."'";
		if($ltype) $sql .= " AND a.`type`='". $ltype ."'";  

		# kapag may sinelect na dfrom at dto
		// if($dfrom && $dto) $sql .= " AND (a.datefrom BETWEEN '$dfrom' AND '$dto') AND (a.dateto BETWEEN '$dfrom' AND '$dto')";

		#Change viewing to date applied
		if($dfrom && $dto) $sql .= " AND (a.date_applied BETWEEN '$dfrom' AND '$dto')";

		$sql .= "GROUP BY a.id ORDER BY id;";
		
		# run the query here
		$query_empList = $this->db->query($sql)->result();

		$leave_description = $this->extensions->getLeaveRequestCode(); 
		foreach ($query_empList as $q_row) {
			$isCanEdit = true;
			$id = $q_row->id;
			$aid = $q_row->aid;
			if($q_row->nodays == "0.00" && $q_row->ishalfday == "1" && $q_row->paid == "YES"){
				$daysCount = $no_days = 0;
				$applied_by = $q_row->applied_by;
				$leavetype = $q_row->ltype;
				if( $q_row->dfrom <> '' && $q_row->dfrom <> 'undefined' && $q_row->dto <> '' && $q_row->dto <> 'undefined'){
					$start 	= date_format(date_create($q_row->dfrom), 'Y-m-d');
					$end 	= date_format(date_create($q_row->dto), 'Y-m-d');

					$this->load->model('leave');
					$dates_arr = $this->utils->getDatesFromRange($start, $end);
					if($leavetype == 'ML')  $empsched_arr = $this->leave->getEmployee_Schedule($q_row->empID,$start);
					else $empsched_arr = $this->leave->getEmployeeSchedDays($q_row->empID,$start);

					foreach ($dates_arr as $date) {
						$dayofwk = date('w', strtotime($date));
						if(in_array($codedayofweek[$dayofwk], $empsched_arr)) $daysCount++;
					}
				}
				if($daysCount){
					$no_days = number_format(($daysCount/2),2);
					$this->db->query("UPDATE leave_app_base SET nodays = '$no_days', dayscount = '$daysCount' WHERE id = '$aid'");
					$q_row->nodays = $no_days;
				}
				// echo "<pre>"; print_r($daysCount); die;
			}
			# pang check ko pede i-edit yung data or delete..
			$query_status = $this->db->query("SELECT * FROM leave_app_emplist WHERE id='{$id}'")->result();
			foreach ($query_status as $qs) {
				for ($i=0; $i < count($status_col) ; $i++) { 
					$col = $status_col[$i];

					if($qs->$col == "APPROVED" || $qs->$col == "DISAPPROVED"){
						$isCanEdit = false;
						break;	
					}
				}
			}
			
			# push here the list
			array_push($empList,array(
				"id" => $q_row->id,
				"baseID" => $q_row->baseID,
				"empID"  => $q_row->empID,
				"fullname" => $q_row->fullname,
				"ltype" => $q_row->ltype,
				"lDesc" => $leave_description[($q_row->ltype == 'other') ? $q_row->otype : $q_row->ltype],
				"nodays" => $q_row->nodays,
				"dfrom" => $q_row->dfrom,
				"dto" => $q_row->dto,
				"status" => $q_row->status,
				"date_applied" => $q_row->date_applied,
				"liquidated" => $q_row->liquidated,
				"isEdit" => $isCanEdit
			));
		}

		return $empList;
	}

	# > delete leave request na ina-apply ni admin
	function deleteLeaveRequestByAdmin($id, $isDelToRequest = false){
		if($isDelToRequest){
			# delete na sa leave_request
			$this->db->query("DELETE FROM leave_request WHERE aid='{$id}'");
		}else{
			# get muna yung base id
			$base_id = $this->db->query("SELECT base_id FROM leave_app_emplist WHERE id='{$id}'")->row(0)->base_id;

			# pag na get na delete na sya dito sa leave_app_base
			$this->db->query("DELETE FROM leave_app_base WHERE id='{$base_id}'");
		}	

		# return success query
		return "success";
	}
	# end of new function for ica-hyperion 21194

	function insertToTimesheetFromLeave($employeeid='',$sched='',$dfrom='',$ltype=''){
        ///< sample laman ng sched_affected
          /*array(2) {
            [0]=>
            string(17) "08:00:00|12:00:00"
            [1]=>
            string(17) "13:00:00|17:00:00"
          }*/

          $ltypedesc = 'LEAVE';

          if($ltype=='VL') $ltypedesc = 'VACATION';
          if($ltype=='SL') $ltypedesc = 'SICK';
          if($ltype=='EL') $ltypedesc = 'EMERGENCY';

        if(count($sched) > 0){
            foreach ($sched as $row) {
                $time = explode('|', $row);
                if(isset($time[0]) && isset($time[1]) && $dfrom){
                    $timein = $dfrom . ' ' . $time[0];
                    $timeout = $dfrom . ' ' . $time[1];
                    $this->db->query("INSERT INTO timesheet (userid,timein,timeout,otype) VALUES ('$employeeid','$timein','$timeout','$ltypedesc')");
                }
            }
            
        }
    }

	/**
	 * Sorts approval heads based on sequence. Stores sorted details in array.
	 *
	 * @param stdClass Object $setup approval sequence details of specific OT
	 *
	 * @return array
	 */
	function sortApprovalSeq($setup){
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

	/**
	 * Deletes an Leave app when it's still not approved/disapproved by approving head.
	 *
	 * @param int $id overtime app id
	 *
	 * @return string
	 */
	function deleteLeaveApp($id){
        $return = "";
        $query = $this->db->query("SELECT id, dstatus FROM leave_app_emplist WHERE base_id='$id' 
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
            $query = $this->db->query("DELETE FROM leave_app_emplist WHERE id='$id'");
            if($query)  $return = "Successfully Deleted!.";
            else 		$return = "Failed to delete.";
            
        }
        return $return;
    }


	function getEmpSchedMinMaxTimePerday($employeeid=''){
		$res = $this->db->query(" SELECT MIN(starttime) as start,MAX(endtime) as end,idx,dayofweek 
		                             FROM employee_schedule_history 
		                             WHERE employeeid='$employeeid'
		                             GROUP BY idx");
		return $res;
	}

	function getPayrollCutoff($dfrom, $dto){
		return $this->db->query("SELECT b.*, a.* 
								 FROM cutoff a
								 LEFT JOIN payroll_cutoff_config b ON b.baseid = a.ID
								 WHERE '$dfrom' BETWEEN a.CutoffFrom AND a.CutoffTo AND '$dto' BETWEEN a.CutoffFrom AND a.CutoffTo;")->result();
	}


	function deleteLeaveAppByAdmin($aid,$data){
		$msg = 'Failed to delete application.';

		$this->load->model('utils');

		$employeeid = $data['employeeid'];
		$nodays = $data['nodays'];
		$dfrom = $data['dfrom'];
		$dto = $data['dto'];
		$base_id = $data['base_id'];
		$leave_type = ($data['leavetype'] == 'other') ? $data['othertype'] : $data['leavetype'];
		$status = $data['status'];

		$leave_description = $this->extensions->getLeaveRequestCode();
		$tnt = $this->employee->getempdatacol('teachingtype',$employeeid);
		$tbl = 'attendance_confirmed_nt';
		if($tnt == 'teaching') $tbl = 'attendance_confirmed';

		$payroll_cutoff_from = $payroll_cutoff_to = '';
		$q_payroll_cutoff = $this->getPayrollCutoff($dfrom, $dto);
		foreach ($q_payroll_cutoff as $row) {
			$payroll_cutoff_from = $row->startdate;
			$payroll_cutoff_to   = $row->enddate;
		}
		$leave_stat = "";
		if($payroll_cutoff_from && $payroll_cutoff_to){
			$q_finalize = $this->db->query("SELECT * FROM payroll_computed_table WHERE employeeid='$employeeid' AND cutoffstart='payroll_cutoff_from' AND cutoffend='payroll_cutoff_to' AND `status` != 'PENDING';")->result();

			if(count($q_finalize) > 0) return $msg.' Cutoff is already finalized.';
		}

		$chk_count_q = $this->utils->getSingleTblData('leave_app_emplist',array('*'),array('base_id'=>$base_id));
		if($chk_count_q->num_rows() > 0){
			$leave_stat = $chk_count_q->row()->status;
			// $del_app_q = $this->db->delete('leave_app_emplist',array('id'=>$aid,'employeeid'=>$employeeid));
			$del_app_q = $this->db->query("UPDATE leave_app_emplist SET status = 'CANCELLED' WHERE id = '$aid' ");
		}else{
			$leave_stat = $chk_count_q->row()->status;
			// $del_app_q = $this->db->delete('leave_app_base',array('id'=>$base_id));
			$del_app_q = $this->db->query("UPDATE leave_app_base SET nodays = '0' WHERE id = '$base_id' ");
			$del_app_q = $this->db->query("UPDATE leave_app_emplist SET status = 'CANCELLED' WHERE id = '$aid' ");
		}

		$credit_update = true;
		if($del_app_q){

			$this->db->query("DELETE FROM timesheet WHERE userid = '$employeeid' AND DATE('$dfrom') AND otype = '{$leave_description[$leave_type]}' ");
			$del_request_q = $this->db->delete('leave_request',array('aid'=>$aid,'employeeid'=>$employeeid));
			$valid_leave_list = array("VL", "SL", "EL");
			/*if(in_array($leave_type, $valid_leave_list) && $status != "PENDING"){
				$leave_type = ($leave_type != "SL") ? "VL" : "SL";*/
			if($leave_stat == "APPROVED"){
				$credit_update = $this->db->query("UPDATE employee_leave_credit SET balance = balance + $nodays, avail = avail - $nodays WHERE employeeid = '$employeeid' AND (('$dfrom' BETWEEN dfrom AND dto) OR ('$dto' BETWEEN dfrom AND dto)) AND leavetype='$leave_type'");
			}
			/*}*/

			$username = $this->session->userdata('username');
			$this->db->query("INSERT INTO leave_delete_history (base_id, deleted_by) VALUES ('$base_id', '$username') ");
			
		}

		if(!$del_request_q) $msg = 'Failed to delete application. ';
		if(!$credit_update) $msg = 'Failed to update leave balance.';

		if($credit_update) $msg = 'Successfully cancelled leave application.';

		return $msg;
	}

	function deleteLeaveRequestForAdminAutoApprove($base_id){
		$this->db->query("DELETE FROM leave_app_base WHERE id = '$base_id' ");
		$this->db->query("DELETE FROM leave_app_emplist WHERE base_id = '$base_id' ");
	}

	function validateLeaveRequest($employeeid, $ltype, $date=""){
		$data = array();
		$data_credit = '';
		$leave_no_days = 0;
		$where_clause = "";
		$ec_dfrom = $ec_dto = "";

		$where_clause_credit = "";
		if($ltype == "EL" || $ltype == "VL") $where_clause_credit = " AND leavetype = 'VL' ";
		else if($ltype == "SL") $where_clause_credit = " AND(leavetype = 'SL')";
		else $where_clause_credit = " AND leavetype = '$ltype' ";

		if($date) $where_clause_credit .= " AND ('$date' BETWEEN dfrom AND dto)";
		$query_credit = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid = '$employeeid' $where_clause_credit ");
		if($query_credit->num_rows() > 0){
			$data_credit = $query_credit->row()->balance;
			$ec_dfrom = $query_credit->row()->dfrom;
			$ec_dto = $query_credit->row()->dto;
		}

		if($ltype == "EL" || $ltype == "VL") $where_clause = " AND(type = 'VL' OR type = 'EL')";
		else $where_clause = " AND type = '$ltype'";

		if($ec_dfrom && $ec_dto) $where_clause .= " AND ((datefrom BETWEEN '$ec_dfrom' AND '$ec_dto') AND (dateto BETWEEN '$ec_dfrom' AND '$ec_dto'))";
		$query = $this->db->query("SELECT * FROM leave_app_emplist a INNER JOIN leave_app_base b ON b.`id` = a.`base_id` WHERE employeeid = '$employeeid' $where_clause AND a.status = 'PENDING' ");
		if($query->num_rows() > 0){
			// echo "<pre>"; print_r($this->db->last_query());
			$data = $query->result_array();
			foreach($data as $row){
				$leave_no_days += $row['nodays'];
			}
		}

		$total = $data_credit - $leave_no_days;
		return $total;

	}

	function countAvailableLeave($employeeid, $ltype, $date){
		$this->load->model('leave');
		$leaves = $this->leave->getEmpLeaveCredit($employeeid, $ltype);
    	$leaves = $this->leave->recalculateEmpLeaveCredit($employeeid, $leaves->result());

		$data = array();
		$data_credit = '';
		$leave_no_days = 0;
		$where_clause = "";
		if($date) $where_clause = " AND ('$date' BETWEEN datefrom AND dateto)";
		if($ltype != "PL-SEM" || $ltype != "PL-M" || $ltype != "PL-G"){
			$query = $this->db->query("SELECT * FROM leave_app_emplist a INNER JOIN leave_app_base b ON b.`id` = a.`base_id` WHERE employeeid = '$employeeid' AND type = '$ltype' AND paid = 'YES' AND status != 'DISAPPROVED' AND status != 'CANCELLED' AND status != 'APPROVED' $where_clause");
			if($query->num_rows() > 0){
				$data = $query->result_array();
				foreach($data as $row){
					$leave_no_days += $row['nodays'];
				}
			}
		}

		$wc = "";
		if($date) $wc = " AND ('$date' BETWEEN dfrom AND dto)";

		$query_credit = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid = '$employeeid' $wc AND leavetype = '$ltype'");
		if($query_credit->num_rows() > 0){
			
			$data_credit = $query_credit->row()->balance;
		}
		$total = $data_credit - $leave_no_days;
		return $total;
	}

	function getAvailableOtherLeaveBalances($employeeid, $other_leave_type, $date=""){
		$available_balance = $pending_other_leave_request = $other_leave_credit = 0;

		$q_pending_request = $this->db->query("SELECT COUNT(b.nodays) AS count_other_request
											   FROM leave_app_emplist a 
											   INNER JOIN leave_app_base b ON b.id = a.base_id 
											   WHERE employeeid='$employeeid' AND a.other='$other_leave_type' AND a.status!='DISAPPROVED';")->result();
		foreach ($q_pending_request as $row) $pending_other_leave_request = $row->count_other_request;

		$wc = "";
		if($date) $wc = " AND ('$date' BETWEEN dfrom AND dto)";
		$q_other_leave_credit = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$employeeid' $wc AND leavetype='$other_leave_type';")->result();
		foreach ($q_other_leave_credit as $row) $other_leave_credit = $row->balance;

		$available_balance = $other_leave_credit - $pending_other_leave_request;
		return $available_balance;
	}

	function getLeaveHistory($is_DA=false,$leavetype='',$oth_type='',$datefrom='',$dateto='',$employeeid='',$leave_id='',$orderby='',$getBalance=false){
	 	$wC = $bal_q = '';
	 	$cond = $ret = array();
	 	if($is_DA){
	 		if($leavetype)	array_push($cond,"other = '$leavetype'");
	 		if($oth_type)	array_push($cond,"othertype = '$oth_type'");
	 	}
	 	else{
	 		array_push($cond, "a.other != 'DA'");
	 		if($leavetype)	array_push($cond,"(a.leavetype IN ('$leavetype') OR (a.other IN ('$leavetype')))");
	 		if($getBalance) $bal_q = ",(SELECT z.balance FROM employee_leave_credit z WHERE z.employeeid=a.`employeeid` AND (z.leavetype=a.`leavetype` OR z.leavetype=a.`other`) AND (a.`fromdate` BETWEEN z.dfrom AND z.dto) LIMIT 1 ) AS balance";
	 	} 	

	 	if($datefrom && $dateto) 	array_push($cond,"(fromdate BETWEEN '$datefrom' AND '$dateto' OR todate BETWEEN '$datefrom' AND '$dateto')");	

	 	if(sizeof($cond) > 0) {
	 		$wC = implode(' AND ', $cond);
	 		$wC = 'WHERE ' . $wC;
	 	}

	 	$res = $this->db->query("SELECT REPLACE(CONCAT(b.LName,', ',b.FName,' ',b.MName), 'Ã‘', 'Ñ') AS fullname, b.deptid, c.description as deptdesc, b.positionid, d.description as posdesc, a.* $bal_q, b.teachingtype 
	 							FROM leave_request a
								INNER JOIN employee b ON b.employeeid=a.employeeid
								LEFT JOIN code_office c ON c.`code`=b.`deptid`
								LEFT JOIN code_position d ON d.`positionid`=b.`positionid` $wC $orderby;");

	 	if($res->num_rows > 0) return $res->result();
	 	else return '';

	}

	function getOtherLeave(){
		return $this->db->query("SELECT * FROM code_request_form WHERE is_leave=1")->result();
	}

	function getAvailableEmployeeOtherLeave($employeeid){
		return $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$employeeid' AND NOT leavetype IN ('VL', 'SC', 'SL');")->result();
	}

	function getOtherLeaveOptions($employeeid){
        $other_leave = array();
        
        $other_leave_arr = array();
        $q_other_leave = $this->getOtherLeave();
        foreach ($q_other_leave as $row) $other_leave_arr[$row->code_request] = $row->description;

        $q_available_other_leave = $this->getAvailableEmployeeOtherLeave($employeeid);
        foreach ($q_available_other_leave as $row){
            if(array_key_exists($row->leavetype, $other_leave_arr) && $row->balance > 0){
                $other_leave[$row->leavetype] = $other_leave_arr[$row->leavetype];
            }
        }

        return $other_leave;
	}

	function updateLeaveAppBaseData($withpay,$datesetfrom,$datesetto,$ndays,$l_type,$base_id){
		$q_update = $this->db->query("UPDATE leave_app_base SET type = '$l_type', datefrom = '$datesetfrom', dateto = '$datesetto', nodays = '$ndays' WHERE id = '$base_id' ");
		if($q_update) return true;
		else return false;
	}

	function getAvailableEmployeeOtherLeaveAdmin(){
		return $this->db->query("SELECT * FROM employee_leave_credit WHERE NOT leavetype IN ('VL', 'SC', 'SL');")->result();
	}

	function getRemAllowance($empid=''){
		$limit = 10000;
		$wC = "";
		$lastid = $this->db->query("SELECT MAX(id) as ids FROM employee_leave_credit WHERE employeeid = '$empid'")->row()->ids;
		$range = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid = '$empid' AND id ='$lastid'");
		/*if($range->num_rows() > 0){
			$dfrom = $range->row(0)->dfrom;
			$dto = $range->row(0)->dto;
		}
		if(isset($dfrom) && isset($dto)) $wC .= " AND (a.datefrom BETWEEN '$dfrom' AND '$dto') AND (a.dateto BETWEEN '$dfrom' AND '$dto')";*/
		$remaining = $this->db->query("SELECT SUM(a.total) as remaining FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id where a.type = 'PL-SEM' AND b.employeeid = '$empid' AND (b.status <> 'DISAPPROVED' AND b.status <> 'CANCELLED') $wC")->row()->remaining;
		// echo $this->db->last_query(); die;
		if($remaining == NULL) $remaining = 0;
		if($limit > $remaining) $remaining = $limit - $remaining;
		else $remaining = 0;

		if($remaining > 0) return $remaining;
		else return 0;
	}

	function validateVacationLeaveCredits($employeeid){
		$data = array();
		$leave_no_days = 0;
		$query = $this->db->query("SELECT * FROM leave_app_emplist a INNER JOIN leave_app_base b ON b.`id` = a.`base_id` WHERE employeeid = '$employeeid' AND type = 'VL'  AND a.status != 'DISAPPROVED' AND a.status != 'CANCELLED' AND a.status != 'APPROVED' ");
		if($query->num_rows() > 0){
			$data = $query->result_array();
			foreach($data as $row){
				$leave_no_days += $row['nodays'];
			}
		}

		return $leave_no_days;
	}

	function insertSeminarInformation($leave_id){
		$leave_info = $seminar_info = array();
		$leave_type = '';
		$q_leave = $this->db->query("SELECT b.employeeid,b.base_id, a.* FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id WHERE b.id = '$leave_id' ");
		if($q_leave->num_rows() > 0){
			$leave_info = $q_leave->result_array();
			$leave_type =  $leave_info[0]["type"];
			$seminar_info = array(
				"employeeid" => $leave_info[0]["employeeid"],
				"title" => $leave_info[0]["title"],
				"venue" => $leave_info[0]["location"],
				"seminar_title" => $leave_info[0]["title"],
				"location" => $leave_info[0]["location"],
				"dateF" => $leave_info[0]["datefrom"],
				"datet" => $leave_info[0]["dateto"],
				"organizer" => $leave_info[0]["organizer"],
				"leave_id" => $leave_info[0]["base_id"],
				"regfee" => $leave_info[0]["fee"],
				"transfee" => $leave_info[0]["transportation"],
				"accfee" => $leave_info[0]["accomodation"],
				"otherfee" => $leave_info[0]["other"],
				"total" => $leave_info[0]["total"],
				"is201" => "NO",
				"status" => "APPROVED",
				"modified_by" => $this->session->userdata('username')
			);
			if($leave_type == "PL-SEM") $this->db->insert("employee_pts_pdp1", $seminar_info);
			// echo $this->db->last_query(); die;
		}
	}
	
	function getLeaveHRApprover($id){
		$q_leave = $this->db->query("SELECT * FROM leave_app_base WHERE id = '$id'");
		if($q_leave->num_rows() > 0) return $q_leave->row()->hrhead;
		else return false; 
	}

	function getSeminarAvailedAmount($ltype, $employee){
		return $this->db->query("SELECT * FROM employee_leave_credit WHERE leavetype = '$ltype' AND employeeid = '$employee'");
	}

	function getAllApprovedSeminar($datefrom, $dateto, $employeeid){
		return $this->db->query("SELECT * FROM leave_request a INNER JOIN leave_app_base b ON a.`aid` = b.`id` WHERE datefrom BETWEEN '$datefrom' AND '$dateto' AND dateto BETWEEN '$datefrom' AND '$dateto' AND employeeid = '$employeeid' AND STATUS = 'APPROVED' ");
	}

	public function hasFiledLeave($employeeid, $datefrom, $dateto, $ishalfday="", $timefrom="", $timeto=""){
    	$where_clause = "";
    	// if($ishalfday == "true") $dateto = $datefrom;
    	$sched_affected = $timefrom."|".$timeto;
    	if($datefrom == $dateto){
	    	if($ishalfday == "true"){
	    		$dateto = $datefrom; 
	    		$where_clause = " AND( isHalfDay = '1' AND sched_affected = '$sched_affected') ";
	    	}
	    	return $this->db->query("SELECT * FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id WHERE employeeid = '$employeeid' AND ('$datefrom' BETWEEN datefrom AND dateto OR '$dateto' BETWEEN datefrom AND dateto) AND (status != 'CANCELLED' AND status != 'DISAPPROVED')  $where_clause ")->num_rows();
	    }else{
	    	if($ishalfday == "true"){
	    		// $dateto = $datefrom; 
	    		$where_clause = " AND isHalfDay = '1' AND sched_affected = '$sched_affected' OR(  employeeid = '$employeeid' AND datefrom = '$datefrom' AND dateto = '$dateto') ";
	    	}
	    	return $this->db->query("SELECT * FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id WHERE employeeid = '$employeeid'
	    	AND ('$datefrom' BETWEEN datefrom AND dateto OR '$dateto' BETWEEN datefrom AND dateto OR datefrom BETWEEN '$datefrom' AND '$dateto' AND dateto BETWEEN '$datefrom' AND '$dateto')
	    	AND (status != 'CANCELLED' AND status != 'DISAPPROVED') $where_clause ")->num_rows();
	    }
	    	 // echo $this->db->last_query(); die;
    }

    public function hasFiledLeaveDetails($employeeid, $datefrom, $dateto, $ishalfday="", $timefrom="", $timeto=""){
    	$where_clause = "";
    	// if($ishalfday == "true") $dateto = $datefrom;
    	$sched_affected = $timefrom."|".$timeto;
    	if($datefrom == $dateto){
	    	if($ishalfday == "true"){
	    		$dateto = $datefrom; 
	    		$where_clause = " AND( isHalfDay = '1' AND sched_affected = '$sched_affected') ";
	    	}
	    	return $this->db->query("SELECT * FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id WHERE employeeid = '$employeeid' AND ('$datefrom' BETWEEN datefrom AND dateto OR '$dateto' BETWEEN datefrom AND dateto) AND (status != 'CANCELLED' AND status != 'DISAPPROVED')  $where_clause ")->result_array();
	    }else{
	    	if($ishalfday == "true"){
	    		// $dateto = $datefrom; 
	    		$where_clause = " AND isHalfDay = '1' AND sched_affected = '$sched_affected' OR(  employeeid = '$employeeid' AND datefrom = '$datefrom' AND dateto = '$dateto') ";
	    	}
	    	return $this->db->query("SELECT * FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id WHERE employeeid = '$employeeid'
	    	AND ('$datefrom' BETWEEN datefrom AND dateto OR '$dateto' BETWEEN datefrom AND dateto OR datefrom BETWEEN '$datefrom' AND '$dateto' AND dateto BETWEEN '$datefrom' AND '$dateto')
	    	AND (status != 'CANCELLED' AND status != 'DISAPPROVED') $where_clause ")->result_array();
	    }
	    	 // echo $this->db->last_query(); die;
    }

    public function allowedProLeave($employeeid){
    	return $this->db->query("SELECT * FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id WHERE employeeid = '$employeeid' AND type = 'PL-SEM' AND liquidated = 'NO' ")->num_rows();
    }

    public function getLeaveAttachments($id){
    	$dbname = $this->db->database_files;
    	return $this->db->query("SELECT * FROM $dbname.leave_app_files WHERE base_id = '$id'");
    }

    public function updateLiquidated($idkey, $status){
    	$timestamp = $this->extensions->getServerTime();
    	$username = $this->session->userdata("username");
    	$this->db->query("UPDATE leave_app_base SET liquidated = '$status', updatedby = '$username', liquidation_date = '$timestamp' WHERE id = '$idkey'");
    }

    public function saveLeaveSched($base_id, $starttime, $endtime, $dateactive){
    	$is_exists = $this->db->query("SELECT * FROM leave_schedref WHERE base_id = '$base_id' ");
    	if($is_exists->num_rows() == 0) $this->db->query("INSERT INTO leave_schedref (base_id, starttime, endtime, dateactive) VALUES ('$base_id', '$starttime', '$endtime', '$dateactive') ");
    	else $this->db->query("UPDATE leave_schedref SET starttime = '$starttime', endtime = '$endtime', dateactive = '$dateactive' WHERE base_id = '$base_id'");
    }

    public function getSeminarFiles($leave_id=""){
    	$filename = $content = $mime = "";
        $dbname = $this->db->database_files; 
    	$q_leave = $this->db->query("SELECT * FROM $dbname.leave_app_files WHERE base_id = '$leave_id' AND content !=  ''");
    	if($q_leave->num_rows() > 0){
    		if($q_leave->row()->content != "") $filename = "Attachment";
			$content = $q_leave->row()->content;
			$mime = $q_leave->row()->mime;
		}
		return array($filename, $content, $mime);
    }

    public function isAlreadyApproved($id){
    	return $this->db->query("SELECT id, dstatus FROM leave_app_emplist WHERE base_id='$id' 
        								AND (dstatus='APPROVED' 
        									OR cstatus='APPROVED' 
        									OR hrstatus='APPROVED' 
        									OR cpstatus='APPROVED' 
        									OR fdstatus='APPROVED' 
        									OR bostatus='APPROVED' 
        									OR pstatus='APPROVED' 
        									OR upstatus='APPROVED' 
        									OR `status`='DISAPPROVED')")->num_rows();
    }

    public function cancelLeaveApp($id){
        $query = $this->db->query("UPDATE leave_app_emplist  SET status = 'CANCELLED' WHERE id='$id'");
        return true;
    }

    public function returnLeaveCredit($employeeid, $nodays, $ltype, $datefrom, $dateto){
    	return $this->db->query("UPDATE employee_leave_credit SET balance = balance + '$nodays', avail = avail - '$nodays' WHERE employeeid='$employeeid' AND leavetype='$ltype' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto)) ");
    }

} //endoffile