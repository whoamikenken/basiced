<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ob_application extends CI_Model {

	function checkLeaveBalance($employeeid='',$ltype='',$datefrom='',$dateto=''){
		$balance = $credit = $availed = 0;
		$haveCredits = true;
		$bal_q = $this->db->query("SELECT balance,credit,avail FROM employee_leave_credit WHERE employeeid='$employeeid' AND leavetype='$ltype' AND (('$datefrom' BETWEEN dfrom AND dto) OR ('$dateto' BETWEEN dfrom AND dto))");

		if($bal_q->num_rows() > 0){
			$balance = $bal_q->row(0)->balance;
			$credit = $bal_q->row(0)->credit;
			$availed = $bal_q->row(0)->avail;
		}else $haveCredits = false;
		return array($haveCredits,$balance,$credit,$availed);
	}

	function checkExistingLeaveApp($employeeid='',$status='',$datefrom='',$dateto=''){
		// $leave_q = $this->db->query("SELECT a.id FROM ob_app_emplist a INNER JOIN ob_app b ON a.`base_id`=b.`id` INNER JOIN ob_request c ON a.`base_id` = c.`aid` WHERE a.employeeid='$employeeid' AND a.status='$status' AND ('$datefrom' BETWEEN datefrom AND dateto) OR ('$dateto' BETWEEN datefrom AND dateto)");
		$leave_q = $this->db->query("SELECT a.id FROM ob_app_emplist a INNER JOIN ob_app b ON a.`base_id`=b.`id` INNER JOIN ob_request c ON a.`base_id` = c.`aid` WHERE c.employeeid='$employeeid' AND a.status='$status' AND ('$datefrom' BETWEEN datefrom AND dateto)");
		// echo "<pre>"; print_r($this->db->last_query()); die;
		return $leave_q->num_rows();
	}

    /**
	 * Gets request details based on leave app id.
	 *
	 * @param string $leaveid (Default: "")
	 *
	 * @return stdClass Object
	 */
    function getAppSequencePerLeave($leaveid=''){
    	$res = $this->db->query("SELECT * FROM ob_app_emplist a INNER JOIN ob_app b ON a.`base_id`=b.`id` WHERE a.id='$leaveid'");
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
    function insertBaseLeaveApp($user, $ltype, $datefrom, $dateto, $timefrom, $timeto, $ob_type, $paid, $nodays, $ishalfday, $sched_affected, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,$destination, $final_file, $size, $file_type){
    	$id = "";
    	if($ob_type === 'late' || $ob_type === 'undertime') $dateto = $datefrom;
    	$res = $this->db->query("INSERT INTO ob_app (
    			applied_by, `type`, datefrom, 	dateto, timefrom, 	timeto,		ob_type, 	paid, 	nodays,	 isHalfDay, sched_affected,	 reason,	 dhead,		chead,	 hrhead, cphead,	fdhead, 	bohead, 	phead, 	uphead, 	dseq, 	cseq, 	hrseq, cpseq, 	fdseq, 		boseq, 		pseq, 	upseq, date_applied,destination) VALUES (
    			'$user', '$ltype', '$datefrom', '$dateto', '$timefrom', '$timeto', '$ob_type', '$paid', '$nodays', '$ishalfday', '$sched_affected', ".$this->db->escape($reason).", '$dhead', '$chead', '$hrhead', '$cphead', '$fdhead', '$bohead', '$phead', '$uphead', '$dseq', '$cseq', '$hrseq', '$cpseq', '$fdseq', '$boseq', '$pseq', '$upseq', CURRENT_DATE, ".$this->db->escape($destination).")
    			");
    	if($res){ 
    		$id = $this->db->insert_id();
    		$dbname = $this->db->database_files;
    		$this->db->query("INSERT INTO $dbname.ob_app_files (base_id, content, mime, size) VALUES ('$id', '$final_file', '$file_type', '$final_file') ");
    	}
    	return $id;
    }

    /**
	 * Inserts OB app in secondary table for list of employees.
	 *
	 * @return Array
	 */
    function insertLeaveAppEmpList($base_id, $user, $teachingType, $dstatus, $ddate, $isAdmin = false, $col_stat = ''){
    	$empcount = $isread = 0;
    	$arr_data_failed = array();
		$isread = 1;

		# for ica-hyperion 21194
		# by justin (with e)
		$status = '';
		if($isAdmin && $col_stat){
			$status = ", 'APPROVED'";
		}
		# end for ica-hyperion 21194

		$res = $this->db->query("
			INSERT INTO ob_app_emplist (base_id, employeeid, teachingType, dstatus, ddate, isread $col_stat) VALUES ('$base_id', '$user', '$teachingType','$dstatus', '$ddate', '$isread' $status)
		");

		if($res) $empcount++;
		else array_push($arr_data_failed, $employeeid);

		return array($empcount,$arr_data_failed);
    }

    function modifyLeaveDetails($base_id='',$datefrom='', $dateto='', $tfrom='', $tto='', $paid='',$nodays='',$isHalfDay='',$sched_affected='',$reason='',$destination='', $final_file, $size, $file_type){
    	$update_clause = "";
    	if($final_file && $size && $file_type){
    		$update_clause = "content='$final_file', size='$size', mime='$file_type',";
    	}
    	$res = $this->db->query("UPDATE ob_app SET 
													datefrom='$datefrom',
													dateto='$dateto',
													timefrom='$tfrom',
													timeto='$tto',
													paid='$paid',
    												nodays='$nodays',
    												isHalfDay='$isHalfDay',
    												sched_affected='$sched_affected',
    												reason={$this->db->escape($reason)},
    												$update_clause
    												destination={$this->db->escape($destination)}
    												
    											WHERE id='$base_id'
    												");
    	if($res) return 1;
    	else return 0;
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
    	return $this->db->query("UPDATE ob_app SET filename = '$filename' WHERE id = '$id'");
    }

    /**
	 * Gets list of employee OB applications.
	 *
	 * @return stdClass Object
	 */
    function getEmpOBHistory($employeeid="", $status="", $leaveid="", $isread='',$target=''){
    	$wC = "";
    	// if($datefrom && $dateto) $wC .= " AND b.`date_applied` BETWEEN '$datefrom' AND '$dateto'";
    	if($status)				 $wC .= " AND a.`status`='$status'";
    	if($leaveid)			 $wC .= " AND a.id='$leaveid'";
    	// if($isread <> '')		 $wC .= " AND a.isread='$isread'";
    	if($target)		 		 $wC .= " AND b.type='$target'";
        $res = $this->db->query("SELECT a.id AS leaveid, a.*,b.* ,REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname
        							FROM ob_app_emplist a
									INNER JOIN ob_app b ON a.`base_id`=b.`id`
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
		$res = $this->db->query("SELECT a.id AS leaveid, a.employeeid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname,e.description AS epos, d.description AS edept, a.*,b.* 
									FROM ob_app_emplist a
									INNER JOIN ob_app b ON a.`base_id`=b.`id`
									INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
									LEFT JOIN code_position e ON c.positionid = e.positionid
									LEFT JOIN code_office d ON c.office = d.code 
									WHERE a.id='$leaveid'");

		if($res->num_rows() > 0){
			foreach ($res->result() as $obj) 
			{
				$data['base_id'] 		= $obj->base_id;
				$data['leaveid'] 		= $obj->leaveid;
				$data['employeeid'] 	= Globals::_e($obj->employeeid);
				$data['othertype'] 		= $obj->type;
				$data['paid'] 			= $obj->paid;
				$data['date_applied'] 	= $obj->date_applied;
				$data['dfrom'] 			= $obj->datefrom;
				$data['dto'] 			= $obj->dateto;
				$data['timefrom'] 		= Globals::_e($obj->timefrom);
				$data['timeto'] 		= Globals::_e($obj->timeto);
				$data['nodays'] 		= $obj->nodays;
				$data['isHalfDay'] 		= $obj->isHalfDay;
				$data['sched_affected'] = $obj->sched_affected;
				$data['reason'] 		= Globals::_e($obj->reason);
				$data['destination'] 		= $obj->destination;
				$data['status'] 		= $obj->status;
				if($colstatus) 	$data['colstat']= $obj->$colstatus; 
				$data['fullname'] 		= Globals::_e($obj->fullname);
				$data['pos'] 			= Globals::_e($obj->epos);
				$data['edept']  		= Globals::_e($obj->edept);
				$data['rem']			= Globals::_e($obj->remarks);
				$data['ob_type']		= $obj->ob_type;
				// $data['content']		= $obj->content;
				$data['size']			= "";
				$data['mime']			= "";
				$hasfiles = $this->getOBAttachments($obj->base_id);
				$data["hasfiles"] = $hasfiles->num_rows();
				$data['filename']			= "";
  			}
		}
		return $data;
	}


	function getTimeRecord($base_id=''){
		$timerecord = $this->db->query("SELECT * FROM leave_app_ti_to WHERE aid='$base_id'");
		if($timerecord->num_rows() > 0) return $timerecord->result();
		else return '';
	}

	
	/**
	 * Gets list of leave applications for given approver to manage.
	 *
	 * @return stdClass Object
	 */
	function getLeaveAppListToManage($user="", $colhead="", $colstatus='', $status="", $prevcolstatus='',$datefrom="", $dateto="",$teachingType='',$code_request='',$target='',$seq_count='',$deptid='',$office=''){
		$colseq =  $colhead ? (substr($colhead,0,-4) . 'seq') : '';

		$wC = "";
    	if($datefrom && $dateto) $wC .= " AND (b.datefrom BETWEEN '$datefrom' AND '$dateto') AND (b.dateto BETWEEN '$datefrom' AND '$dateto')";
    	if($status)			 	 $wC .= " AND $colstatus='$status'";
		if($colseq)			 	 $wC .= " AND $colseq!='0'";
		if($seq_count)			 $wC .= " AND $colseq='$seq_count'";
    	if($prevcolstatus) 	 	 $wC .= " AND $prevcolstatus='APPROVED'";
    	if($teachingType) 	 	 $wC .= " AND c.teachingType='$teachingType'";
    	if($deptid)		 		 $wC .= " AND c.deptid='$deptid'";
    	if($office)		 		 $wC .= " AND c.office='$office'";
    	if($target)		 		 $wC .= " AND b.type='$target'";
    	// if($code_request) 		 $wC .= " AND b.type='$code_request'";

		$res = $this->db->query("SELECT a.id AS aid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname, c.deptid, c.office, a.*,b.* 
							FROM ob_app_emplist a
							INNER JOIN ob_app b ON a.`base_id`=b.`id`
							INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
							WHERE $colhead='$user' $wC");
		return $res;
	}
	# for ica-hyperion 21194
	# by justin (with e)
	# > isasave dito kapag direct Approved ni admin..
	function directApprovedByAdmin($leave_id){
		$status = 'APPROVED';
		$admin_username = $this->session->userdata("username");
		$leave_q = $this->db->query("SELECT a.employeeid,b.type,b.datefrom,b.dateto,b.timefrom,b.timeto,b.nodays,b.paid, b.isHalfDay, b.sched_affected FROM ob_app_emplist a INNER JOIN ob_app b ON a.`base_id`=b.`id` WHERE a.id='$leave_id'");
		$this->db->query("UPDATE ob_app_emplist SET status='APPROVED', approver_admin = '$admin_username' WHERE id='{$leave_id}'");
		$ishalfday = 0;
		$sched_affected = array();
		$sched_affected_string = '';
		$datefrom = $dateto = $employeeid = $ltype = $paid = $nodays = $timefrom = $timeto = '';


		if($leave_q->num_rows() > 0){
			$l_q = $leave_q->row(0);
		    $employeeid 			= $l_q->employeeid;
		    $ishalfday 				= $l_q->isHalfDay;
		    $sched_affected_string 	= $l_q->sched_affected;
		    $datefrom 				= $l_q->datefrom;
		    $dateto 				= $l_q->dateto;
		    $timefrom 				= $l_q->timefrom;
		    $timeto 				= $l_q->timeto;
		    $ltype 					= $l_q->type;
		    $nodays 				= $l_q->nodays;
		    $paid 					= $l_q->paid;
		}
		if($ishalfday && $sched_affected_string && $datefrom) $sched_affected = explode('|', $sched_affected_string);
		if($sched_affected){
			$timefrom = $sched_affected[0];
			$timeto = $sched_affected[1];
		}
		# pang samantala lang ito.. saving ng timerecord sa timesheet
		# for ica-gen 193 by : justin (with e) 
		
		/*if($timerecord){
			foreach (explode("|", $timerecord) as $time) {
				list($tid,$timein,$timeout) = explode("~u~", $time);
				$timein = str_replace(": ", " ", $timein);
				$timeout = str_replace(": ", " ", $timeout);
				$timein = date("Y-m-d H:i:s", strtotime($datefrom." ".$timein));
				$timeout = date("Y-m-d H:i:s", strtotime($datefrom." ".$timeout));
				
				$isNew = explode("add-", $tid);
				if(count($isNew) > 0){
					# add new
					
					$this->db->query("INSERT INTO timesheet (userid, timein, timeout) VALUES ('$employeeid', '$timein', '$timeout')");
				}else{
					# update
					$this->db->query("UPDATE timesheet timein='$timein', timeout='$timeout' WHERE timeid='$tid'");
				}

			}
		}*/
		# end for ica-gen 193 

		///< check for existing applications
		$exist_app = $this->checkExistingLeaveApp($employeeid,'APPROVED',$datefrom,$dateto);
		if($exist_app) {return array('err_code'=>0,'msg'=>'Employee already have approved applications for this date.');}

		
		$insert_q = $this->db->query("
						INSERT INTO ob_request (aid,employeeid,othertype,fromdate,todate,timefrom,timeto,ob_type,paid,dateapplied,no_days,isHalfDay,sched_affected,remarks,status,dateapproved)
						 (SELECT a.id , a.employeeid, b.type, b.datefrom, b.dateto, b.timefrom,b.timeto, b.ob_type, b.paid, b.date_applied, b.nodays, b.isHalfDay, b.sched_affected, b.reason, '$status', CURRENT_DATE
							FROM ob_app_emplist a
							INNER JOIN ob_app b ON a.`base_id`=b.`id`
							 WHERE a.id='$leave_id')");
		// echo "<pre>"; print_r($this->db->last_query()); die;

		if($insert_q){

			///< insert to timesheetssss
			if($paid != 'NO'){
				$df = $dt = "";
				$qdate = $this->attcompute->displayDateRange($datefrom, $dateto);
				foreach($qdate as $rdate){
					$df = date('Y-m-d', strtotime($rdate->dte)). " ". date('H:i:s',strtotime($timefrom));
					$dt = date('Y-m-d', strtotime($rdate->dte)). " ". date('H:i:s',strtotime($timeto));
					$isexist = $this->db->query("SELECT * FROM timesheet where DATE(timein) = '{$rdate->dte}' AND userid = '$employeeid'");
					// if($isexist->num_rows() == 0 && $timefrom != "00:00:00" && $timeto != "00:00:00"){
					if($isexist->num_rows() == 0 && $timefrom != "00:00:00" && $timeto != "00:00:00"){
						$this->db->query("INSERT INTO timesheet (`userid`,`timein`,`timeout`,`otype`) VALUES ('".$employeeid."','".date('Y-m-d H:i:s', strtotime($df))."','".date('Y-m-d H:i:s', strtotime($dt))."','ob')");
					}
				}
				
            }
            $this->db->query("UPDATE ob_app_emplist SET isread='0' WHERE id='$leave_id'");
			return array('err_code'=>1,'msg'=>'Success! Status is now '.$status);
			
		}else {return array('err_code'=>0,'msg'=>"Failed to save.");}

				
	}

	# > for modified tapos direct approved..
	function changeSeqForDirectApproved($id, $base_id){
		$dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";
			$admin_username = $this->session->userdata("username");
		# tatangalin lahat ng seq..
		$this->db->query("UPDATE ob_app SET dseq='{$dseq}', cseq='{$cseq}', hrseq='{$hrseq}', cpseq='{$cpseq}', fdseq='{$fdseq}', boseq='{$boseq}', pseq='{$pseq}', upseq='{$upseq}' WHERE id='{$base_id}'");

		# update to approved status.
		$this->db->query("UPDATE ob_app_emplist SET status='APPROVED', SET approver_admin = '$admin_username' WHERE id='{$id}'");

	}
	# end for ica-hyperion 21194

	/**
	 * Saves leave status change made by approver.
	 *
	 * @return stdClass Object
	 */
	function saveLeaveStatusChange($user='', $leave_id='', $status='',$colstatus='',$coldate='',$colhead='',$isLastApprover='',$base_id='',$remarks='', $timerecord='',$prev_colhead='', $endorse=''){
		$res = $prev_wC ='';
		$return = '';

		if($prev_colhead) $prev_wC = " AND $prev_colhead='$user'";
		$test_q = $this->db->query("SELECT a.id FROM ob_app_emplist a INNER JOIN ob_app b ON b.id=a.base_id WHERE a.id='$leave_id' AND $colhead='$user' $prev_wC");
		if($test_q->num_rows() > 0){


				if($status == 'DISAPPROVED' || $isLastApprover){
					$res = $this->db->query("UPDATE ob_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE, status='$status',remarks='{$remarks}' WHERE id='$leave_id'");
					$this->db->query("UPDATE ob_app_emplist SET isread='0' WHERE id='$leave_id'");

					if($res){
						if($endorse) $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$endorse);
						else $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$status);
					}

				}else{
					$res = $this->db->query("UPDATE ob_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE,remarks='$remarks' WHERE id='$leave_id'");
					if($res){
						if($endorse) $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$endorse);
						else $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$status);
					}
				}
				// die;
				if($status == 'APPROVED' && $isLastApprover){
					$leave_q = $this->db->query("SELECT a.employeeid,b.type,b.datefrom,b.dateto,b.timefrom,b.timeto,b.nodays,b.paid, b.isHalfDay, b.sched_affected FROM ob_app_emplist a INNER JOIN ob_app b ON a.`base_id`=b.`id` WHERE a.id='$leave_id'");
					$ishalfday = 0;
					$sched_affected = array();
					$sched_affected_string = '';
					$datefrom = $dateto = $employeeid = $ltype = $paid = $nodays = $timefrom = $timeto = '';


					if($leave_q->num_rows() > 0){
						$l_q = $leave_q->row(0);
					    $employeeid 			= $l_q->employeeid;
					    $ishalfday 				= $l_q->isHalfDay;
					    $sched_affected_string 	= $l_q->sched_affected;
					    $datefrom 				= $l_q->datefrom;
					    $dateto 				= $l_q->dateto;
					    $timefrom 				= $l_q->timefrom;
					    $timeto 				= $l_q->timeto;
					    $ltype 					= $l_q->type;
					    $nodays 				= $l_q->nodays;
					    $paid 					= $l_q->paid;
					}
					if($ishalfday && $sched_affected_string && $datefrom) $sched_affected = explode(',', $sched_affected_string);

					# pang samantala lang ito.. saving ng timerecord sa timesheet
					# for ica-gen 193 by : justin (with e) 
					
					if($timerecord){
						foreach (explode("|", $timerecord) as $time) {
							list($tid,$timein,$timeout) = explode("~u~", $time);
							if($timein != "(--:-- --)" && $timeout != "(--:-- --)"){
								$timein = str_replace(": ", " ", $timein);
								$timeout = str_replace(": ", " ", $timeout);
								$timein = date("Y-m-d H:i:s", strtotime($datefrom." ".$timein));
								$timeout = date("Y-m-d H:i:s", strtotime($datefrom." ".$timeout));
								
								$isNew = explode("add-", $tid);
								if(count($isNew) > 0){
									# add new
									
									if($timein != "0000-00-00 00:00:00") $this->db->query("INSERT INTO timesheet (userid, timein, timeout, otype) VALUES ('$employeeid', '$timein', '$timeout', '$ltype')");
								}else{
									# update
									$iscontinue = $this->db->query("SELECT * FROM timesheet WHERE timeid = '$tid' ")->num_rows();
									if($iscontinue) $this->db->query("UPDATE timesheet timein='$timein', timeout='$timeout', otype='$ltype' WHERE timeid='$tid'");
									else $this->db->query("INSERT INTO timesheet (userid, timein, timeout, otype) VALUES ('$employeeid', '$timein', '$timeout', '$ltype')");
								}
							}else{
								list($tr, $timeid) = explode("-", $tid);
								$this->db->query("DELETE FROM timesheet WHERE timeid='$tid'");
							}
						}
					}
					# end for ica-gen 193 

					///< check for existing applications
					$exist_app = $this->checkExistingLeaveApp($employeeid,'APPROVED',$datefrom,$dateto);
					if($exist_app) {return array('err_code'=>0,'msg'=>'Employee already have approved applications for this date.');}

					
					$insert_q = $this->db->query("
									INSERT INTO ob_request (aid,employeeid,othertype,ob_type,fromdate,todate,timefrom,timeto,paid,dateapplied,no_days,isHalfDay,sched_affected,remarks,status,dateapproved)
									 (SELECT a.id , a.employeeid, b.type, b.ob_type, b.datefrom, b.dateto, b.timefrom,b.timeto, b.paid, b.date_applied, b.nodays, b.isHalfDay, b.sched_affected, b.reason, '$status', CURRENT_DATE
										FROM ob_app_emplist a
										INNER JOIN ob_app b ON a.`base_id`=b.`id`
										 WHERE a.id='$leave_id');

								");

					if($insert_q){

						///< insert to timesheet
						if($paid != 'NO'){
							$df = $dt = "";
							$qdate = $this->attcompute->displayDateRange($datefrom, $dateto);
							foreach($qdate as $rdate){
								$df = date('Y-m-d', strtotime($rdate->dte)). " ". date('H:i:s',strtotime($timefrom));
								$dt = date('Y-m-d', strtotime($rdate->dte)). " ". date('H:i:s',strtotime($timeto));
								if($timefrom != "0000-00-00 00:00:00") $this->db->query("INSERT INTO timesheet (`userid`,`timein`,`timeout`,`otype`) VALUES ('".$employeeid."','".date('Y-m-d H:i:s', strtotime($df))."','".date('Y-m-d H:i:s', strtotime($dt))."','ob')");
							}
			            }

						$this->db->query("UPDATE ob_app_emplist SET isread='0' WHERE id='$leave_id'");
						if($insert_q){
							if($endorse) $return = array('err_code'=>1,'msg'=>'Success! Status is now '.$endorse);
							else $return = array('err_code'=>1,'msg'=>'Success! Status is now Noted.');
						}
					}else {
						return array('err_code'=>0,'msg'=>"Failed to save.");
					}

				}

		} ///< end if test_q
		
		return $return;
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
        $query = $this->db->query("SELECT id, dstatus FROM ob_app_emplist WHERE base_id='$id'
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
            $query = $this->db->query("DELETE FROM ob_app WHERE id='$id'");
            if($query)  $return = "Successfully deleted.";
            else 		$return = "Failed to delete.";
            
        }
        return $return;
    }

    function deleteCorrectionApp($id){
    	$return = "";
        $query = $this->db->query("SELECT id, dstatus FROM ob_app_emplist WHERE base_id='$id'
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
            $query = $this->db->query("DELETE FROM ob_app_emplist WHERE id='$id'");
            if($query)  $return = "Successfully deleted.";
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

	function getEmpActualTimePerday($employeeid, $date){
		$res = $this->db->query(" SELECT * FROM timesheet WHERE userid = '$employeeid' AND DATE(timein) = '$date' ");
		return $res;
	}

	function getObAppHistory($dfrom, $dto){
		$res = $this->db->query("SELECT a.*, CONCAT(b.lname, ',', b.fname, ',', b.mname) AS fullname, deptid, d.description, b.teachingtype
								 FROM ob_request a 
								 INNER JOIN employee b ON b.`employeeid` = a.`employeeid` 
								 INNER JOIN `code_department` c ON c.`code` = b.`deptid`
								 INNER JOIN `code_position` d ON d.`positionid` = b.`positionid`
								 WHERE fromdate BETWEEN '$dfrom' AND '$dto' OR todate BETWEEN '$dfrom' AND '$dto';");
		if($res->num_rows > 0) return $res->result();
		else return FALSE;
	}

	function checkIfHasExistingOBRequest($aid, $employeeid){
		$query = $this->db->query("SELECT * FROM ob_request WHERE aid = '$aid' AND employeeid = '$employeeid' ");
		if($query->num_rows > 0) return true;
		else return false;
	}

    // public function getObTodayEmployees($datenow){
    // 	$q_ob = $this->db->query("SELECT DISTINCT employeeid FROM ob_request WHERE '$datenow' BETWEEN fromdate AND todate ");
    // 	if($q_ob->num_rows() > 0) return $q_ob->result_array();
    // 	else return false;
    // }

    public function getObTodayEmployees($datenow, $empstat=''){
    	if($empstat){
    		$q_ob = $this->db->query("SELECT DISTINCT a.employeeid FROM ob_request a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE ('$datenow' BETWEEN fromdate AND todate) AND b.employmentstat NOT IN ('$empstat') AND a.ob_type = 'ob'");
    	}else{
    		$q_ob = $this->db->query("SELECT DISTINCT employeeid FROM ob_request WHERE '$datenow' BETWEEN fromdate AND todate ");
    	}
    	
    	if($q_ob->num_rows() > 0) return $q_ob->result_array();
    	else return false;
    }

    public function hasFiledOB($employeeid, $datefrom, $dateto, $ishalfday="", $timefrom="", $timeto=""){
    	$where_clause = "";
    	$sched_affected = $timefrom."|".$timeto;
    	if($datefrom == $dateto){
	    	if($ishalfday == "true"){
	    		$dateto = $datefrom; 
	    		$where_clause = " AND( isHalfDay = '1' AND sched_affected = '$sched_affected') ";
	    	}
	    	return $this->db->query("SELECT * FROM ob_app a INNER JOIN ob_app_emplist b ON a.id = b.base_id WHERE employeeid = '$employeeid' AND ('$datefrom' BETWEEN datefrom AND dateto OR '$dateto' BETWEEN datefrom AND dateto) AND status != 'CANCELLED' AND type = 'DIRECT' $where_clause ")->num_rows();
	    }else{
	    	if($ishalfday == "true"){
	    		$dateto = $datefrom; 
	    		$where_clause = " AND isHalfDay = '1' AND sched_affected = '$sched_affected' OR(  employeeid = '$employeeid' AND datefrom = '$datefrom' AND dateto = '$dateto') ";
	    	}
	    	return $this->db->query("SELECT * FROM ob_app a INNER JOIN ob_app_emplist b ON a.id = b.base_id WHERE employeeid = '$employeeid' AND ('$datefrom' BETWEEN datefrom AND dateto OR '$dateto' BETWEEN datefrom AND dateto) AND type = 'DIRECT' $where_clause ")->num_rows();
	    }
    }

    public function hasFiledCorrection($employeeid, $datefrom, $dateto){
    	return $this->db->query("SELECT * FROM ob_app a INNER JOIN ob_app_emplist b ON a.id = b.base_id WHERE employeeid = '$employeeid' AND ('$datefrom' BETWEEN datefrom AND dateto OR '$dateto' BETWEEN datefrom AND dateto) AND type != 'DIRECT'")->num_rows();
    }

    public function updateApplicationTime($timefrom, $timeto, $id){
    	if($timefrom) $timefrom = date("H:i:s", strtotime($timefrom));
    	if($timeto) $timeto = date("H:i:s", strtotime($timeto));
    	$this->db->query("UPDATE ob_app SET timefrom = '$timefrom', timeto = '$timeto' WHERE id = '$id' ");
    }

    public function hasActualLog($employeeid, $date){
    	return $this->db->query("SELECT * FROM timesheet WHERE DATE(timein) = '$date' AND userid = '$employeeid' ")->num_rows();
    }

    public function getOBAttachments($id){
    	$dbname = $this->db->database_files;
    	return $this->db->query("SELECT content, mime FROM $dbname.ob_app_files WHERE base_id = '$id' AND content!=''");
    }

    public function saveOBSched($base_id, $starttime, $endtime, $dateactive){
    	$is_exists = $this->db->query("SELECT * FROM ob_schedref WHERE base_id = '$base_id' ");
    	if($is_exists->num_rows() == 0) $this->db->query("INSERT INTO ob_schedref (base_id, starttime, endtime, dateactive) VALUES ('$base_id', '$starttime', '$endtime', '$dateactive') ");
    	else $this->db->query("UPDATE ob_schedref SET starttime = '$starttime', endtime = '$endtime', dateactive = '$dateactive' WHERE base_id = '$base_id'");
    }

    public function deleteOBInsertedLogs($employeeid, $timein, $timeout){
    	if($timein && $employeeid && $timeout){
    		/*insert to history*/
    		$this->saveTimesheetHistory($employeeid, $timein, $timeout);

    		return $this->db->query("DELETE FROM timesheet WHERE userid = '$employeeid' AND timein = '$timein' AND timeout = '$timeout'");
    	}
    }


    public function saveTimesheetHistory($employeeid, $timein, $timeout){
    	/*insert to history*/
    	$this->db->query("
    		INSERT INTO timesheet_history (timeid, userid, timein, timeout, localtimein, localtimeout, mac_add_in, mac_add_out, addedby, dateadded, type, otype, username, h_type) 
    		SELECT timeid, userid, timein, timeout, localtimein, localtimeout, mac_add_in, mac_add_out, addedby, dateadded, type, otype, username, 'DELETED' FROM timesheet WHERE userid = '$employeeid' AND timein = '$timein' AND timeout = '$timeout' ");
    }

    public function correctionTimeHistory($id){
    	return $this->db->query("SELECT * FROM leave_app_ti_to WHERE aid = '$id' ");
    }


    public function updateTimesheetLogs($timeid, $act_in, $act_out, $req_in, $req_out, $employeeid){
    	/*insert to history*/
    	$this->saveTimesheetHistory($employeeid, $req_in, $req_out);

    	$this->db->query("UPDATE timesheet SET timein = '$act_in', timeout = '$act_out' WHERE timein = '$req_in' AND timeout = '$req_out' AND userid = '$employeeid'");
    }

    public function deleteTimesheetLogs($timeid, $act_in, $act_out, $employeeid){
    	/*insert to history*/
    	$this->saveTimesheetHistory($employeeid, $act_in, $act_out);

    	/*delete timesheet logs*/
    	$this->db->query("DELETE FROM timesheet WHERE userid = '$employeeid' AND timein = '$act_in' AND timeout = '$act_out'");

    }

    public function cancelOBApp($id, $base_id){
        $query = $this->db->query("UPDATE ob_app_emplist  SET status = 'CANCELLED' WHERE id='$id'");
        $query = $this->db->query("UPDATE ob_request  SET status = 'CANCELLED' WHERE aid='$base_id'");
        return true;
    }

} //endoffile