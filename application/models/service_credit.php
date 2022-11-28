<?php 
/**
 * class referenced from overtime or change_schedule
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Service_Credit extends CI_Model {
	
	function serviceCreditUse($job,$code)
	{
		$msg = "Failed to delete";
		if ($job == "delete") {
			$query = $this->db->query("DELETE FROM sc_app_use WHERE id = '$code'");
			if ($query) {
				$query2 = $this->db->query("DELETE FROM sc_app_use_emplist WHERE base_id = '$code'");
				if ($query2) {
					$msg = "Successfully Deleted";
				}
				else
				{
					$msg = "Failed to delete";
				}
			}
		}
		return $msg;
	}
    function getEmpSC(){
        $user = $this->session->userdata("username");
        $query = $this->db->query("SELECT * FROM employee_service_credit WHERE employeeid = '{$user}'");
		if($query->num_rows() > 0) return $query->result();
		else return false;
    }
   function delSC($code)
	{
			$query = "";
			$arraydata = array();
			$checking = $this->db->query("SELECT * FROM sc_app_emplist WHERE base_id ='{$code}' ");
			foreach ($checking->result() as $key => $value) {
				foreach ($value as $k => $v) {
					$arraydata[] = $v;
				}
				
			}
			
			if (!in_array("APPROVED",$arraydata) || $this->session->userdata('usertype') == "ADMIN") 
			{
				 $query = $this->db->query("DELETE FROM sc_app WHERE id='{$code}'");
				 $query = $this->db->query("DELETE FROM sc_app_emplist WHERE baseid='{$code}'");
				 $query = $this->db->query("DELETE FROM employee_service_credit WHERE id='{$code}'");
			}
			
	
			 return $query;
	}
	function updateSC($id,$date,$date1,$sc,$day,$reason)
	{
		$dates = "";
		if ($date1 != "" && $date !="") {
			$dates = $date;					
			$query = $this->db->query("UPDATE sc_app SET date='{$dates}',service_credit='{$sc}',dayMode='{$day}',reason='{$reason}' WHERE id='{$id}'");
		}
		else
		{
			$query = $this->db->query("UPDATE sc_app SET date='{$date}',service_credit='{$sc}',dayMode='{$day}',reason='{$reason}' WHERE id='{$id}'");
		}
		return $query;
	}

	function checkSCDateAvailability($date, $employeeid){
		$date = date("Y-m-d",strtotime($date));
		$employee_schedule = $this->db->query("SELECT * 
												FROM employee_schedule 
												WHERE employeeid = '{$employeeid}' AND idx  = DATE_FORMAT('{$date}','%w')")->result();
		return $employee_schedule;
	}

	function getEmpSchedIsHalfDay($employeeid, $date, $dayofweek){
		$this->load->library("extras_lib");
		$isHalfDay = true;

		$q_sched_affecting_date = $this->db->query("SELECT * FROM employee_schedule_history 
													WHERE employeeid = '$employeeid' AND `dayofweek`='$dayofweek' AND dateactive = (
													SELECT dateactive 
													FROM employee_schedule_history 
													WHERE employeeid = '$employeeid' AND `dayofweek`='$dayofweek' AND dateactive <='$date' ORDER BY dateactive DESC LIMIT 1
													);")->result();
		
		$hours = 0;
		foreach ($q_sched_affecting_date as $row) {
			$sched_time = $this->extras_lib->getDateDifference($row->starttime, $row->endtime, "%H:%I");
			$hours += $this->extras_lib->convertTimeToNumber($sched_time);
		}

		if($hours > 4) $isHalfDay = false;
		return $isHalfDay;
	}

	function checkDateAvailability($date,$employeeid){
		$return = array();
		$date = date("Y-m-d",strtotime($date));
		$employee_schedule = $this->db->query("SELECT * 
												FROM employee_schedule 
												WHERE employeeid = '{$employeeid}' AND idx  = DATE_FORMAT('{$date}','%w')")->num_rows();
												
		if($employee_schedule > 0)
		{
			$return = array("exist"=>true,"count"=>$employee_schedule);
		}
		else if($employee_schedule == 0)
		{
			$return = array("exist"=>false,"count"=>$employee_schedule);
		}
		
		$availability = $this->db->query("SELECT a.id
											FROM sc_app a
											LEFT JOIN sc_app_emplist b ON a.id = b.base_id
											WHERE a.applied_by = '{$employeeid}' AND a.date='{$date}' AND b.status != 'DISAPPROVED'")->num_rows();
		if($availability > 0)
		{
			$return['availability'] = false;
		}
		else if($availability == 0)
		{
			$return['availability'] = true;
		}
		
		return $return;
	}
	
	function getEmpDept($user){
    	$return = "";
		$res = $this->db->query("SELECT office FROM employee WHERE employeeid='{$user}'");
		if($res->num_rows() > 0) $return = $res->row(0)->office;
    	return $return;
    }
	
	function getDeptHead($col="", $code=""){
    	$head = "";
    	$res = $this->db->query("SELECT $col FROM code_office WHERE code='$code'");
    	if($res->num_rows() > 0) $head = $res->row(0)->$col;
    	return $head;
    }
	function getuserDeptHead($col="", $code=""){
    	$head = "";
    	$res = $this->db->query("SELECT head FROM code_office WHERE code='$code'");
    	if($res->num_rows() > 0) $head = $res->row(0)->$col;
    	return $head;
    }
	function getAppSequence($type=""){
    	$res = $this->db->query("SELECT * FROM code_request_form WHERE code_request='$type'");
    	return $res;
    }
	
	function insertBaseSCApp($ids,$user, $date, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,$appliedby){
    	$id = "";
		$res = $this->db->query("INSERT INTO sc_app (
    			applied_by, date, 	dayMode, 	service_credit,	 reason,	 dhead,		chead,	 hrhead, cphead,	fdhead, 	bohead, 	phead, 	uphead, 	dseq, 	cseq, 	hrseq, cpseq,	fdseq, 		boseq, 		pseq, 	upseq, date_applied,approval_id,user) VALUES (
    			'$user', '$date', '$dayMode', '$sc', '$reason', '$dhead', '$chead', '$hrhead', '$cphead', '$fdhead', '$bohead', '$phead', '$uphead', '$dseq', '$cseq', '$hrseq', '$cpseq', '$fdseq', '$boseq', '$pseq', '$upseq', CURRENT_DATE,'$ids','$appliedby')
    			");
    	if($res)  	$id = $this->db->insert_id();
    	return $id;
    }

    function SCAppManageApproved($baseid,$hrhead)
    {

    	$dhead= $chead = $cphead= $fdhead= $bohead= $phead= $uphead= $dseq= $cseq= $hrseq= $cpseq= $fdseq= $boseq= $pseq= $upseq= "";
    	$date = "";
    	$allStatus="";
    	$status = "APPROVED";
    	$query = $this->db->query("UPDATE sc_app_emplist SET status ='$status',dstatus ='$allStatus',ddate='$date',cstatus='$allStatus',cdate='$date',hrstatus='APPROVED',hrdate=CURRENT_DATE,cpstatus='$allStatus',cpdate='$date',fdstatus='$allStatus',fddate='$date',bostatus='$allStatus',bodate='$date',pstatus='$allStatus',pdate='$date',bostatus='$allStatus',bodate='$date',pstatus='$allStatus',pdate='$date',upstatus='$allStatus' WHERE base_id='$baseid'");
    	
    	if ($query) {
    		$this->db->query("UPDATE sc_app SET dhead='$allStatus',chead='$allStatus',hrhead='$hrhead',cphead='$allStatus',fdhead='$allStatus',bohead='$allStatus',phead='$allStatus',uphead='$allStatus',dseq='$dseq',cseq='$cseq',cpseq='$cpseq',fdseq='$fdseq',boseq='$boseq',pseq='$pseq',upseq='$upseq' WHERE id='$baseid'");

    		$this->db->query("INSERT INTO employee_service_credit (employeeid,date,total_sc,available_sc)(SELECT a.employeeid,b.date,b.service_credit,b.service_credit FROM sc_app_emplist a INNER JOIN sc_app b ON a.`base_id`= b.`id` WHERE a.id='$baseid')");
    	}
    	return $query;
    }
	
	function insertSCApp($ids,$base_id, $teachingType, $dstatus, $ddate, $user){
    	$isread = 0;
		$res = $this->db->query("
			INSERT INTO sc_app_emplist (base_id, employeeid, teachingType, dstatus, ddate, isread,approval_id) VALUES ('$base_id', '$user', '$teachingType', '$dstatus', '$ddate', '$isread','$ids')
		");
		
		if($res)return true;
		else return false;
    }
	function displayschistoryManagement($status,$dfrom,$dto)
	{
		$wC = "";
		if ($status) {	$wC .= "AND b.status ='$status'";}
		if ($dfrom && $dto) {	$wC .= "AND a.date BETWEEN '{$dfrom}' AND '{$dto}'";}

		$query = $this->db->query("SELECT DISTINCT b.employeeid,a.id,a.date,a.service_credit,b.status,b.isread,c.total_sc,c.available_sc,b.status,b.dstatus,b.cstatus,b.hrstatus,b.fdstatus,b.bostatus,b.pstatus,b.upstatus FROM sc_app a LEFT JOIN sc_app_emplist b ON a.id = b.base_id
        												LEFT JOIN employee_service_credit c ON a.date = c.date
        												WHERE a.`user` != '' {$wC}  GROUP BY id ");
		return $query;
	}

	function displayscusehistoryManagement($user,$status,$dfrom,$dto)
	{
		$wC = "";
		if ($status) {	$wC .= "AND b.status ='$status'";}
		if ($dfrom && $dto) {	$wC .= "AND a.date_applied BETWEEN '{$dfrom}' AND '{$dto}'";}

		$query = $this->db->query("SELECT b.employeeid,a.id,a.date,a.service_credit,b.status,b.isread,c.total_sc,c.available_sc,b.status,b.dstatus,b.cstatus,b.hrstatus,b.fdstatus,b.bostatus,b.pstatus,b.upstatus FROM sc_app a LEFT JOIN sc_app_emplist b ON a.id = b.base_id
        												LEFT JOIN employee_service_credit c ON a.date = c.date
        												WHERE a.applied_by = '$user' {$wC} ");
		return $query;
	}
	function displayservicecredithistory($action='',$status=''){
        $user = $this->session->userdata("username");
        if ($action == "load") {
        		       $read = $this->db->query("SELECT DISTINCT a.id,a.date,a.service_credit,b.status,b.isread,c.total_sc,c.available_sc,b.status,b.dstatus,b.cstatus,b.hrstatus, b.fdstatus,b.bostatus,b.pstatus,b.upstatus
        											FROM sc_app a
        											LEFT JOIN sc_app_emplist b ON a.id = b.base_id
        											LEFT JOIN employee_service_credit c ON a.date = c.date
        											WHERE (a.applied_by = '$user' || b.employeeid='$user') GROUP BY a.id ");
        		      	if ($read->num_rows() > 0 ) {
        		      		 $query = $this->db->query("SELECT DISTINCT a.id,a.date,a.service_credit,b.status,b.isread,c.total_sc,c.available_sc,b.status,b.dstatus,b.cstatus,b.hrstatus,b.fdstatus,b.bostatus,b.pstatus,b.upstatus
        											FROM sc_app a
        											LEFT JOIN sc_app_emplist b ON a.id = b.base_id
        											LEFT JOIN employee_service_credit c ON a.date = c.date
        											WHERE (a.applied_by = '$user' || b.employeeid='$user') GROUP BY a.id ");
        		      	}
        		      	else
        		      	{
        		      		$query = $this->db->query("SELECT DISTINCT a.id,a.date,a.service_credit,b.status,b.isread,c.total_sc,c.available_sc,b.status,b.dstatus,b.cstatus,b.hrstatus,b.fdstatus,b.bostatus,b.pstatus,b.upstatus
        											FROM sc_app a
        											LEFT JOIN sc_app_emplist b ON a.id = b.base_id
        											LEFT JOIN employee_service_credit c ON a.date = c.date
        											WHERE (a.applied_by = '$user' || b.employeeid='$user') AND b.status ='PENDING' GROUP BY a.id ");
        		      	}
        }
        else if($action == "status")
        {
        		$query = $this->db->query("SELECT DISTINCT a.id,a.date,a.service_credit,b.status,b.isread,c.total_sc,c.available_sc,b.status,b.dstatus,b.cstatus,b.hrstatus,b.fdstatus,b.bostatus,b.pstatus,b.upstatus
        												FROM sc_app a
        												LEFT JOIN sc_app_emplist b ON a.id = b.base_id
        												LEFT JOIN employee_service_credit c ON a.date = c.date
        												WHERE (a.applied_by = '$user' || b.employeeid='$user') AND b.status ='$status' GROUP BY a.id ");
        }
        else
        {
        	$query = $this->db->query("SELECT DISTINCT a.id,a.date,a.service_credit,b.status,b.isread,c.total_sc,c.available_sc,b.status,b.dstatus,b.cstatus,b.hrstatus,b.fdstatus,b.bostatus,b.pstatus,b.upstatus
        											FROM sc_app a
        											LEFT JOIN sc_app_emplist b ON a.id = b.base_id
        											LEFT JOIN employee_service_credit c ON a.date = c.date
        											WHERE (a.applied_by = '$user' || b.employeeid='$user') AND b.status ='PENDING' GROUP BY a.id ");
        }
      
        return $query;
    }

    function getSCUsedManagementInfo($code='')
    {
    	if ($code) {
    		$query = $this->db->query("SELECT a.*,b.* FROM sc_app_use a INNER JOIN sc_app_use_emplist b ON (b.`base_id` = a.`id`) WHERE a.id='$code'");
    		if ($query->num_rows() > 0) {
    			return $query;
    		}
    	}
    }
	
	function getAppSequencePerSC($id=''){
    	$res = $this->db->query("SELECT * FROM sc_app_emplist a INNER JOIN sc_app b ON a.`base_id`=b.`id` WHERE a.base_id='$id'");
    	return $res;
    }
	
	function sortApprovalSeq($setup){
		$this->load->model('employee');
		$this->load->model('utils');
		$user = $this->session->userdata('username');
		$deptid = $this->employee->getempdatacol('office');

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

		$dhead = $this->utils->getDeptHead('head',		$deptid);	
		$chead = $this->utils->getDeptHead('divisionhead',$deptid);	
		$hrhead = $this->utils->getDeptHead('head',		'HR');

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
		//unset 0
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
	
	function getSCAppListToManage($user="", $colhead="", $colstatus='', $status="", $prevcolstatus='',$datefrom="", $dateto="",$teachingType='',$seq_count='', $prev_seq_count=''){
		$colseq =  $colhead ? (substr($colhead,0,-4) . 'seq') : '';
		$prevcolseq = $prevcolstatus ? (substr($prevcolstatus,0,-6) . 'seq') : '';
		
		$wC = "";
    	if($datefrom && $dateto) $wC .= " AND b.`date_applied` BETWEEN '$datefrom' AND '$dateto'";
    	if($status)			 	 $wC .= " AND $colstatus='$status'";
    	if($prevcolstatus) 	 	 $wC .= " AND $prevcolstatus='APPROVED'";
    	if($prev_seq_count)	 	 $wC .= " AND $prevcolseq='$prev_seq_count'";
    	if($teachingType) 	 	 $wC .= " AND c.teachingType='$teachingType'";
    	if($seq_count)			 $wC .= " AND $colseq='$seq_count'";

		$res = $this->db->query("SELECT a.id AS aid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname, a.*,b.*, SUM(b.service_credit) AS sc_num 
							FROM sc_app_emplist a
							INNER JOIN sc_app b ON a.`base_id`=b.`id`
							INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
							WHERE $colhead='$user' $wC GROUP BY aid, a.approval_id");
		return $res;
	}
	
	function getSCDetails($scid='',$colstatus=''){
		$data = array();
		$res = $this->db->query("SELECT a.id AS scid, a.employeeid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname,e.description AS epos, d.description AS edept, a.*,b.* 
									FROM sc_app_emplist a
									INNER JOIN sc_app b ON a.`base_id`=b.`id`
									INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
									LEFT JOIN code_position e ON c.positionid = e.positionid
									LEFT JOIN code_office d ON c.deptid = d.code 
									WHERE a.base_id='$scid'");
		if($res->num_rows() > 0){
			foreach ($res->result() as $obj) {
				$data['approval_id']	= $obj->approval_id;
				$data['scid'] 			= $obj->scid;
				$data['employeeid'] 	= $obj->employeeid;
				$data['date'] 			= $obj->date;
				$data['date_applied'] 	= $obj->date_applied;
				$data['dayMode'] 		= $obj->dayMode;
				$data['sc_date_list']	= $this->getSCOtherDetailsByApprovalId($obj->approval_id);
				$data['service_credit']	= $obj->service_credit;
				$data['reason'] 		= $obj->reason;
				$data['status'] 		= $obj->status;
				if($colstatus) 	$data['colstat']= $obj->$colstatus; 
				$data['fullname'] 		= $obj->fullname;
				$data['pos'] 			= $obj->epos;
				$data['edept']  		= $obj->edept;
			}
		}
		return $data;
	}

	function getSCOtherDetailsByApprovalId($approval_id){
		$data = array();

		$q_sc_date = $this->db->query("SELECT * FROM sc_app a INNER JOIN sc_app_emplist b ON a.id = b.baseid WHERE a.approval_id='$approval_id' AND b.status='PENDING';")->result();
		foreach ($q_sc_date as $row) {
			$data[$row->id]['date'] 			= $row->date;
			$data[$row->id]['service_credit'] 	= $row->service_credit;
		}
		
		return $data;
	}
	
	function getEmpSchedMinMaxTimePerday($employeeid=''){
		$res = $this->db->query(" SELECT MIN(starttime) as start,MAX(endtime) as end,idx,dayofweek 
		                             FROM employee_schedule_history 
		                             WHERE employeeid='$employeeid'
		                             GROUP BY idx");
		return $res;
	}
	
	function saveSCStatusChange($user='',$scid='', $status='',$colstatus='',$coldate='',$colhead='',$isLastApprover='',$prev_colhead='',$approvalid= ''){
		$res = $prev_wC ='';
		if ($approvalid == 0 ) {
			if($prev_colhead) $prev_wC = " AND $prev_colhead='$user'";
			$test_q = $this->db->query("SELECT a.id FROM sc_app_emplist a INNER JOIN sc_app b ON b.id=a.base_id WHERE a.id='$scid' AND $colhead='$user' $prev_wC");
				if($test_q->num_rows() > 0)
				{
					if($status == 'DISAPPROVED' || $isLastApprover){
						$res = $this->db->query("UPDATE sc_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE, status='$status' WHERE id='$scid'");
						$this->db->query("UPDATE sc_app_emplist SET isread='0' WHERE id='$scid'");
					}else{
						$res = $this->db->query("UPDATE sc_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE WHERE id='$scid'");
					}
					if($status == 'APPROVED' && $isLastApprover){
						$this->db->query("
								INSERT INTO employee_service_credit (employeeid,date,total_sc,available_sc)
								 (SELECT b.applied_by,b.date,b.service_credit,b.service_credit FROM sc_app_emplist a
									 INNER JOIN sc_app b ON a.`base_id`=b.`id`
									 WHERE a.id='$scid')

							");
						$this->db->query("UPDATE sc_app_emplist SET isread='0' WHERE id='$scid'");
					}
				}
		}
		else
		{
			//if the approval_id is greater than 0
			if($prev_colhead) $prev_wC = " AND $prev_colhead='$user'";
				if ($approvalid) {
					$test_q = $this->db->query("SELECT a.id FROM sc_app_emplist a INNER JOIN sc_app b ON b.id=a.base_id WHERE a.approval_id='$approvalid' AND $colhead='$user' $prev_wC");
				}
				else
				{
					$test_q = $this->db->query("SELECT a.id FROM sc_app_emplist a INNER JOIN sc_app b ON b.id=a.base_id WHERE a.id='$scid' AND $colhead='$user' $prev_wC");
				}

				if($test_q->num_rows() > 0)
				{
					if($status == 'DISAPPROVED' || $isLastApprover)
					{
						$res = $this->db->query("UPDATE sc_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE, status='$status' WHERE id='$scid'");
						$this->db->query("UPDATE sc_app_emplist SET isread='0' WHERE approval_id='$approvalid'");
					}
					else
					{
						$res = $this->db->query("UPDATE sc_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE WHERE id='$scid'");
					}
					if($status == 'APPROVED' && $isLastApprover)
					{
						$this->db->query("
								INSERT INTO employee_service_credit (employeeid,date,total_sc,available_sc)
								 (SELECT b.applied_by,b.date,b.service_credit,b.service_credit FROM sc_app_emplist a
									 INNER JOIN sc_app b ON a.`base_id`=b.`id`
									 WHERE a.approval_id='$approvalid')

							");
						$this->db->query("UPDATE sc_app_emplist SET isread='0' WHERE approval_id='$approvalid'");
					}
				}	
		}
		// echo $scid;die;
		return $res;
	}

	# > added by justin (with e) for ica-hyperion 21185
	function searchAvailableSCDates($fields_data, $isAnd = true){
		$whereClause = "";

		foreach ($fields_data as $fields => $value) {
			$whereAndOr = ($isAnd) ? " AND " : " OR ";
			
			$whereClause .= ($whereClause) ? ($whereAndOr) : "";
			$whereClause .= "$fields='$value'";
		}

		$query = $this->db->query("SELECT * FROM employee_service_credit WHERE $whereClause AND available_sc != 0;")->result();
		return $query;
	}
	
	function getSCDatesWithAvailable(){
		$user = $this->session->userdata("username");
		$return = "<option value=''>Select Date</option>";
		$query = $this->db->query("SELECT * FROM employee_service_credit WHERE employeeid='{$user}' AND available_sc != 0");
		if($query->num_rows() != 0)
		{
			foreach($query->result() as $row)
			{
				$return .= "<option value='".$row->date."' available_sc='".$row->available_sc."'>".date("F d, Y",strtotime($row->date))."</option>";
			}
		}
		return $return;
	}

	function getSCDatesWithAvailableHR($empid){
	
		$return = "<option value=''>Select Date</option>";
		$query = $this->db->query("SELECT * FROM employee_service_credit WHERE employeeid='{$empid}' AND available_sc != 0");
		if($query->num_rows() != 0)
		{
			foreach($query->result() as $row)
			{
				$return .= "<option value='".$row->date."' available_sc='".$row->available_sc."'>".date("F d, Y",strtotime($row->date))."</option>";
			}
		}
		return $return;
	}
	
	function SCAppBaseUseHR($user, $date, $dayMode, $nsc, $scdate, $sc, $remark, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,$sched_affected){
    	$id = "";
    	// '$cpseq',
		$res = $this->db->query("INSERT INTO sc_app_use (
    			applied_by, date, 	dayMode, needed_service_credit,	service_credit_date_use,	service_credit_use,	 remark,	 dhead,		chead,	 hrhead, cphead,	fdhead, 	bohead, 	phead, 	uphead, 	dseq, 	cseq, 	hrseq, cpseq,	fdseq, 	boseq, 		pseq, 	upseq, date_applied,sched_affected)
		 		VALUES (
		 		'$user', '$date', '$dayMode', '$nsc', '$scdate', '$sc', '$remark', '$dhead', '$chead', '$hrhead', '$cphead', '$fdhead', '$bohead', '$phead', '$uphead', '$dseq', '$cseq', '$hrseq','$cpseq','$fdseq', '$boseq', '$pseq', '$upseq', CURRENT_DATE,'$sched_affected')");
		// $user, $date, $dayMode, $nsc, $scdate, $sc, $remark, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq
    	if($res)  	$id = $this->db->insert_id();
    	return $id;
    }

    function SCAppBaseUseHRApproved($base_id, $teachingType, $user,$scdate,$sc,$dateused){
    	$isread = 0;
    	

    	$res = '';
		$arrayDate = explode("/",$scdate);
		$arraySC = explode("/",$sc);
		
		$insert = $this->db->query("
			INSERT INTO sc_app_use_emplist (base_id, employeeid, teachingType, isread,hrstatus,hrdate,status) VALUES ('$base_id', '$user', '$teachingType','$isread','APPROVED',CURRENT_DATE,'APPROVED');
		");

		if ($insert) 
		{
			foreach (array_combine($arrayDate, $arraySC) as $key => $value) {
				if ($key != "") {
					$query = $this->db->query("SELECT * FROM employee_service_credit WHERE employeeid='$user' AND date='$key'");
					
									if ($query->num_rows() > 0) {
										$available_sc = $query->row(0)->available_sc;

										if ($available_sc >= $value) {
											 
										
												$timesheet = $this->db->query("SELECT sched_affected FROM sc_app_use WHERE applied_by='$user' AND  service_credit_date_use LIKE'%$key%' AND date='$dateused'");
												
												if($timesheet->num_rows() > 0)
												{

													$sched_affected = $timesheet->row(0)->sched_affected;
													if ($sched_affected != "" ) {
														$sched = explode('|', $sched_affected);
															$timein = date('Y-m-d H:i:s',strtotime("$key $sched[0]"));
															$timeout = date('Y-m-d H:i:s',strtotime("$key $sched[1]"));
															$timesheetinsert = $this->db->query("INSERT INTO timesheet(userid,timein,timeout,otype)VALUES('$user','$timein','$timeout','SERVICE CREDIT')");
															
															if($timesheetinsert) 
															{
																$res = $this->db->query("UPDATE sc_app_use_emplist SET status='APPROVED',isread='0' WHERE id='$base_id'");
																$this->useServiceCredit($user,$scdate,$sc);
																	
															}
													}
													else
													{
														$res = $this->db->query("UPDATE sc_app_use_emplist  SET status='APPROVED', isread='0' WHERE id='$base_id'");
														$this->useServiceCredit($user,$scdate,$sc);

													}
													
															
												}
												else
												{
													$res = $this->db->query("UPDATE sc_app_use_emplist  SET status='APPROVED', isread='1' WHERE id='$base_id'");
													$this->useServiceCredit($user,$scdate,$sc);

												}
												
											
											
										}
									}
				}
				
			}
			if($res)return true;
			else return false;
		}
		
		
    }
	function insertBaseSCAppUse($user, $date, $dayMode, $nsc, $scdate, $sc, $remark, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,$sched_affected,$appliedby){
    	$id = "";
    	// '$cpseq',
		$res = $this->db->query("INSERT INTO sc_app_use (
    			applied_by, date, 	dayMode, needed_service_credit,	service_credit_date_use,	service_credit_use,	 remark,	 dhead,		chead,	 hrhead, cphead,	fdhead, 	bohead, 	phead, 	uphead, 	dseq, 	cseq, 	hrseq, cpseq,	fdseq, 	boseq, 		pseq, 	upseq, date_applied,sched_affected,user)
		 		VALUES (
		 		'$user', '$date', '$dayMode', '$nsc', '$scdate', '$sc', '$remark', '$dhead', '$chead', '$hrhead', '$cphead', '$fdhead', '$bohead', '$phead', '$uphead', '$dseq', '$cseq', '$hrseq','$cpseq','$fdseq', '$boseq', '$pseq', '$upseq', CURRENT_DATE,'$sched_affected','$appliedby')");
		// $user, $date, $dayMode, $nsc, $scdate, $sc, $remark, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq
    	if($res)  	$id = $this->db->insert_id();
    	return $id;
    }
	
	function insertSCAppUse($base_id, $teachingType, $dstatus, $ddate, $user, $cpstatus = '', $cpdate = ''){
    	$isread = 0;
    	$added_field = "";
    	$added_value = "";
    	if($cpstatus && $cpdate){
    		$added_field = ", cpstatus, cpdate ";
    		$added_value = ", '$cpstatus', '$cpdate' ";
    	}

		$res = $this->db->query("
			INSERT INTO sc_app_use_emplist (base_id, employeeid, teachingType, dstatus, ddate, isread $added_field) VALUES ('$base_id', '$user', '$teachingType', '$dstatus', '$ddate', '$isread' $added_value)
		");
		
		if($res)return true;
		else return false;
    }
	
	function useServiceCredit($user,$date,$sc){
		if(strpos($date, ' ') !== false && strpos($sc, ' ') !== false)
		{
			$arrayDate[0] 	= $date;
			$arraySC[0] 	= $sc;
		}
		else
		{
			$arrayDate = explode("/",$date);
			$arraySC = explode("/",$sc);
		}
		foreach(array_combine($arrayDate,$arraySC) as $k => $v)
		{
			if($k && $v)
			{
				$this->db->query("UPDATE employee_service_credit SET used_sc = IFNULL(used_sc, 0) + {$v} , available_sc = available_sc - {$v} WHERE employeeid='{$user}' AND date='{$k}'");
			}
		}
	}
	
	function serviceCreditActions($data)
	{	
		$return = array('err_code'=>'','msg'=>'');
		if ($data['job'] == "delete") {
		    $query = $this->db->query("DELETE FROM sc_app_use WHERE id='{$data['id']}'");
		    if ($query) {
		    	$query1 = $this->db->query("DELETE FROM sc_app_use_emplist WHERE id='{$data['id']}'");
		    	if ($query1) {
		    		$return = array("err_code"=>0,"msg"=>"Successfully Deleted!");
		    	}
		    	else
		    	{
		    		$return = array("err_code"=>0,"msg"=>"Failed to Delete!");
		    	}
		    }
		    else
		    {
		    	$return = array("err_code"=>0,"msg"=>"Successfully Deleted!");
		    }
		return $return;
		}
	}

	function displayuseservicecredithistory($status = ''){
		$where_clause = "";
		if($status) $where_clause .= " AND status = '$status' ";

        $user = $this->session->userdata("username");
        $query = $this->db->query("SELECT a.id,a.date,a.needed_service_credit,a.service_credit_date_use,b.status,b.isread
									FROM sc_app_use a
									LEFT JOIN sc_app_use_emplist b ON a.id = b.base_id
									WHERE b.employeeid = '$user' $where_clause ");
        return $query;
    }

	function displayuseservicecredithistoryManagement($user,$status,$dfrom,$dto){
		$wC = "";
		if ($status) {$wC .=" AND b.status='$status'";}
		if ($dfrom && $dto) {	$wC .= " AND a.date BETWEEN '{$dfrom}' AND '{$dto}'";}
        $query = $this->db->query("SELECT b.employeeid,a.id,a.date,a.needed_service_credit,a.service_credit_date_use,b.status,b.isread
									FROM sc_app_use a
									LEFT JOIN sc_app_use_emplist b ON a.id = b.base_id
									WHERE a.applied_by != ''  $wC GROUP BY id");
        return $query;
    }
	function displayuseservicecredithistorybyfilter($date){
        $user = $this->session->userdata("username");
        $query = $this->db->query("SELECT a.id,a.date,a.needed_service_credit,a.service_credit_date_use,b.status,b.isread,a.date_applied,a.remark
									FROM sc_app_use a
									LEFT JOIN sc_app_use_emplist b ON a.id = b.base_id
									WHERE a.service_credit_date_use LIKE '%$date%' AND a.applied_by = '$user'");
        return $query;
    }
	function getAppSequencePerSCU($id=''){
    	$res = $this->db->query("SELECT * FROM sc_app_use_emplist a INNER JOIN sc_app_use b ON a.`base_id`=b.`id` WHERE b.id='$id'");
    	return $res;
    	// $res = $this->db->query("SELECT * FROM sc_app_use_emplist a INNER JOIN sc_app_use b ON a.`base_id`=b.`id` WHERE a.employeeid='$id'");
    }
	
	function getSCUAppListToManage($user="", $colhead="", $colstatus='', $status="", $prevcolstatus='',$datefrom="", $dateto="",$teachingType='',$seq_count='', $prev_seq_count=''){
		$colseq =  $colhead ? (substr($colhead,0,-4) . 'seq') : '';
		$prevcolseq = $prevcolstatus ? (substr($prevcolstatus,0,-6) . 'seq') : '';
		
		$wC = "";
    	if($datefrom && $dateto) $wC .= " AND b.`date_applied` BETWEEN '$datefrom' AND '$dateto'";
    	if($status)			 	 $wC .= " AND $colstatus='$status'";
    	if($prevcolstatus) 	 	 $wC .= " AND $prevcolstatus='APPROVED'";
    	if($prev_seq_count)	 	 $wC .= " AND $prevcolseq='$prev_seq_count'";
    	if($teachingType) 	 	 $wC .= " AND c.teachingType='$teachingType'";
    	if($seq_count)			 $wC .= " AND $colseq='$seq_count'";	
		$res = $this->db->query("SELECT a.id AS aid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname, a.*,b.* 
							FROM sc_app_use_emplist a
							INNER JOIN sc_app_use b ON a.`base_id`=b.`id`
							INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
							WHERE $colhead='$user' $wC");
		return $res;
	}
	
	function getSCUDetails($scid='',$colstatus=''){
		$data = array();
		$res = $this->db->query("SELECT a.id AS scid, a.employeeid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname,e.description AS epos, d.description AS edept, a.*,b.* 
									FROM sc_app_use_emplist a
									INNER JOIN sc_app_use b ON a.`base_id`=b.`id`
									INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
									LEFT JOIN code_position e ON c.positionid = e.positionid
									LEFT JOIN code_office d ON c.deptid = d.code 
									WHERE a.base_id='$scid'");
		if($res->num_rows() > 0){
			foreach ($res->result() as $obj) {
				$data['scid'] 						= $obj->scid;
				$data['employeeid'] 				= $obj->employeeid;
				$data['date'] 						= $obj->date;
				$data['dayMode'] 					= $obj->dayMode;
				$data['needed_service_credit'] 		= $obj->needed_service_credit;
				$data['service_credit_date_use'] 	= $obj->service_credit_date_use;
				$data['service_credit_use'] 	= $obj->service_credit_use;
				$data['remark'] 		= $obj->remark;				
				$data['date_applied'] 	= $obj->date_applied;
				$data['status'] 		= $obj->status;
				if($colstatus) 	$data['colstat']= $obj->$colstatus; 
				$data['fullname'] 		= $obj->fullname;
				$data['pos'] 			= $obj->epos;
				$data['edept']  		= $obj->edept;
			}
		}
		return $data;
	}
	
	function saveSCUStatusChange($user='',$scid='', $status='',$colstatus='',$coldate='',$colhead='',$isLastApprover='',$empid='',$scdate='',$scuse='',$dated='',$prev_colhead='')
	{
		$res=$available_sc=$prev_wC ='';
		if($prev_colhead) $prev_wC = " AND $prev_colhead='$user'";
		$test_q = $this->db->query("SELECT a.id FROM sc_app_use_emplist a INNER JOIN sc_app_use b ON b.id=a.base_id WHERE a.id='$scid' AND $colhead='$user' $prev_wC");
		if($test_q->num_rows() > 0){

				if($status == 'DISAPPROVED' && $isLastApprover){
				    $this->db->query("UPDATE sc_app_use_emplist SET $colstatus='$status', $coldate=CURRENT_DATE, status='$status' WHERE id='$scid'");
					$res =  $this->db->query("UPDATE sc_app_use_emplist SET isread='0' WHERE id='$scid'");

				}else{
					$arrayDate = explode("/",$scdate);
					$arraySC = explode("/",$scuse);
					foreach (array_combine($arrayDate, $arraySC) as $key => $value) {
						if ($key != "") 
						{
						// echo'<pre>'; var_dump($empid);
							$query = $this->db->query("SELECT * FROM employee_service_credit WHERE employeeid='$empid' AND date='$key'");
							if ($query->num_rows() > 0) {
								$available_sc = $query->row(0)->available_sc;
								 
								if ($available_sc >= $value) {
									$res = $this->db->query("UPDATE sc_app_use_emplist SET $colstatus='$status', $coldate=CURRENT_DATE WHERE id='$scid'");
								}
							}
						}
					}
					
				}
				//LAST APPROVER
				if($status == 'APPROVED' && $isLastApprover){
					$arrayDate = explode("/",$scdate);
					$arraySC = explode("/",$scuse);
					foreach (array_combine($arrayDate, $arraySC) as $key => $value) {
						if ($key != "") {
							$query = $this->db->query("SELECT * FROM employee_service_credit WHERE employeeid='$empid' AND date='$key'");
							
							if ($query->num_rows() > 0) {
								$available_sc = $query->row(0)->available_sc;

								if ($available_sc >= $value) {
									 
								
										$timesheet = $this->db->query("SELECT sched_affected FROM sc_app_use WHERE applied_by='$empid' AND  service_credit_date_use LIKE'%$key%' AND date='$dated'");
										
										if($timesheet->num_rows() > 0) {

											$sched_affected = $timesheet->row(0)->sched_affected;
											if ($sched_affected != "" ) {
												$sched = explode('|', $sched_affected);
													$timein = date('Y-m-d H:i:s',strtotime("$key $sched[0]"));
													$timeout = date('Y-m-d H:i:s',strtotime("$key $sched[1]"));
													$timesheetinsert = $this->db->query("INSERT INTO timesheet(userid,timein,timeout,otype)VALUES('$empid','$timein','$timeout','SERVICE CREDIT')");
													
													if($timesheetinsert) {
														$res = $this->db->query("UPDATE sc_app_use_emplist SET status='APPROVED',isread='0' WHERE id='$scid'");
														$this->useServiceCredit($empid,$scdate,$scuse);
															
													}
											}
											else
											{
												$res = $this->db->query("UPDATE sc_app_use_emplist  SET status='APPROVED', isread='0' WHERE id='$scid'");
												$this->useServiceCredit($empid,$scdate,$scuse);

											}
											
													
										}
										
									
									
								}
							}
						}
						
					}
					
					
					
				}
		}
		
		// if($status == 'DISAPPROVED'){
		// 	$query = $this->db->query("SELECT b.service_credit_date_use,b.service_credit_use FROM sc_app_use_emplist a
		// 	LEFT JOIN sc_app_use b ON a.base_id = b.id WHERE a.id = '$scid'")->result();
		// 	foreach($query as $row)
		// 	{
		// 		$date = explode("/",$row->service_credit_date_use);
		// 		$service_credit = explode("/",$row->service_credit_use);
		// 		foreach(array_combine($date,$service_credit) as $k => $v)
		// 		{
		// 			$this->db->query("UPDATE employee_service_credit SET used_sc = (used_sc - {$v}), available_sc = (available_sc + {$v}) WHERE date = '{$k}'");
		// 		}
		// 	}
		// }		
	
		
		
		return $res;
	}

	// ******************************* SC Nofication *******************************
	function getSCNotification($datefrom, $dateto, $status){
		$this->load->model('utils');

		$isHrHead 	= false;

		$user 			= $this->session->userdata('username');

		$hrhead = $this->utils->getDeptHead('head','HR');
		if($user == $hrhead) $isHrHead = true;

		///< for regular employee
		$sc_list = array();
		///< --------------------------(condition added for Campus Principal which is ommitted for non teaching)-------------------------------------------
		$sc_list_teaching = $this->getSCAppListForSCNotification('SC',$status,$datefrom,$dateto,$user,'teaching');
		$sc_list_non = $this->getSCAppListForSCNotification('SCNON',$status,$datefrom,$dateto,$user,'nonteaching');

		# > added by mcu-hyperion 21295 by justine (with e)
		$sc_list_head = $this->getSCAppListForSCNotification('SCHEAD',$status,$datefrom,$dateto,$user,'teaching',false);
		$sc_list_head_non = $this->getSCAppListForSCNotification('SCHEAD',$status,$datefrom,$dateto,$user,'nonteaching',false);

		if(sizeof($sc_list_teaching) > 0) 	$sc_list =  array_merge($sc_list, $sc_list_teaching);
		if(sizeof($sc_list_non) > 0) 		$sc_list =  array_merge($sc_list, $sc_list_non);

		# > added by mcu-hyperion 21295 by justine (with e)
		if(sizeof($sc_list_head) > 0)		$sc_list =  array_merge($sc_list, $sc_list_head);
		if(sizeof($sc_list_head) > 0)		$sc_list =  array_merge($sc_list, $sc_list_head_non);


		return count($sc_list);
	}

	function getSCUseNotification($datefrom, $dateto, $status){
		$user 			= $this->session->userdata('username');

		$hrhead = $this->utils->getDeptHead('head','HR');
		if($user == $hrhead) $isHrHead = true;

		///< for regular employee
		$scu_list = array();
		///< --------------------------(condition added for Campus Principal which is ommitted for non teaching)-------------------------------------------
		$scu_list_teaching = $this->getSCAppListForSCNotification('SC',$status,$datefrom,$dateto,$user,'teaching',true);
		$scu_list_non = $this->getSCAppListForSCNotification('SCNON',$status,$datefrom,$dateto,$user,'nonteaching',true);

		# > added by mcu-hyperion 21295 by justine (with e)
		$scu_list_head = $this->getSCAppListForSCNotification('SCHEAD',$status,$datefrom,$dateto,$user,'teaching',true);
		$scu_list_head_non = $this->getSCAppListForSCNotification('SCHEAD',$status,$datefrom,$dateto,$user,'nonteaching',true);

		if(sizeof($scu_list_teaching) > 0) 	$scu_list =  array_merge($scu_list, $scu_list_teaching);
		if(sizeof($scu_list_non) > 0) 		$scu_list =  array_merge($scu_list, $scu_list_non);

		# > added by mcu-hyperion 21295 by justine (with e)
		if(sizeof($scu_list_head) > 0)		$scu_list =  array_merge($scu_list, $scu_list_head);
		if(sizeof($scu_list_head) > 0)		$scu_list =  array_merge($scu_list, $scu_list_head_non);

		return count($scu_list);
	}

	function getSCAppListForSCNotification($code_request="SC",$status='',$datefrom='',$dateto='',$user='',$teachingType='',$isSCUse=false){
		$colhead = $isLastApprover = "";
		$prevcolstatus = ""	;
		$sc_list = array();
		$arr_aprvl_seq 	= array();
		$setup 			= $this->getAppSequence($code_request);
		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->sortApprovalSeq($setup->row(0));
		}

		$aprvl_count = sizeof($arr_aprvl_seq);
		$prevkey 	 = '';
		$arr_apprv = array();
		foreach ($arr_aprvl_seq as $key => $obj) {
			if($obj['head_id'] == $user){
				$colhead = $obj['position'];
				if($aprvl_count == $key) 	 $isLastApprover = true;
				if($key > 1) 				 $prevkey 		 = $key - 1;
				// break;

				if($prevkey){
					$prevcolstatus = substr($arr_aprvl_seq[$prevkey]['position'],0,-4) . 'status';
				}
				$colstatus =  $colhead ? (substr($colhead,0,-4) . 'status') : '';

				array_push($arr_apprv, array('seq_count'=>$key,'colhead'=>$colhead,'colstatus'=>$colstatus,'prevcolstatus'=>$prevcolstatus,'isLastApprover'=>$isLastApprover,'code_request'=>$code_request));

				$isLastApprover = '';
			}
		}
		
		foreach ($arr_apprv as $key => $arr) {
			$prev_seq = ($arr['seq_count'] > 1) ? ($arr['seq_count'] - 1) : 0;
			if($isSCUse) $temp_res = $this->getSCUAppListToManage($user, $arr['colhead'], $arr['colstatus'], $status, $arr['prevcolstatus'], $datefrom, $dateto,$teachingType,$arr['seq_count'],$prev_seq);
			
			else 		 $temp_res = $this->getSCAppListToManage($user, $arr['colhead'], $arr['colstatus'], $status, $arr['prevcolstatus'], $datefrom, $dateto,$teachingType,$arr['seq_count'], $prev_seq);
			
			if($temp_res->num_rows() > 0){
				foreach ($temp_res->result() as $key => $row) {
					$sc_list[$row->aid] = array('data_list'=>$row,'colhead'=>$arr['colhead'],'colstatus'=>$arr['colstatus'],'prevcolstatus'=>$arr['prevcolstatus'],'isLastApprover'=>$arr['isLastApprover'],'code_request'=>$arr['code_request']);
				}
			}
		}
		
		return $sc_list;
	}
	
	function getServiceCredit($employeeid, $from_date="", $to_date=""){
		$where_clause = "";
		if($from_date && $to_date){
			$from_date = date("Y-m-d", strtotime($from_date));
			$to_date   = date("Y-m-d", strtotime($to_date));

			$where_clause .= "AND `date` BETWEEN '$from_date' AND '$to_date'";
		}

		return $this->db->query("SELECT * FROM employee_service_credit WHERE employeeid = '$employeeid' $where_clause;");
	}

	function getRecountSC($employeeid){
		$credit = $avail = $balance = 0;

		$q_count = $this->getServiceCredit($employeeid)->result();

		foreach ($q_count as $row) {
			$credit  += ($row->total_sc) ? $row->total_sc : 0;
			$avail 	 += ($row->used_sc) ? $row->used_sc : 0;
			$balance += ($row->available_sc) ? $row->available_sc : 0;
		}

		return array($credit, $avail, $balance);
	}

	function getListOfAvailableSCDate($employeeid, $dfrom, $dto, $day_list){
		$not_available_date = array();
		$date = '';
		$q_service_credit = $this->getServiceCredit($employeeid, $dfrom, $dto)->result();
		foreach ($q_service_credit as $row) $not_available_date[] = $row->date;
		foreach ($day_list as $date => $value){
			if($this->db->query("SELECT * FROM code_holiday_calendar WHERE '$date' BETWEEN date_from AND date_to ")->num_rows == 0){	
				if(in_array($date, $not_available_date)) $day_list[$date] = 0;
			}
		}

		return $day_list;
	}

	function saveSCDate($data){
		return $this->db->insert("employee_service_credit", $data);
	}

	function deleteSCDate($id){
		return $this->db->query("DELETE FROM employee_service_credit WHERE id='$id'");
	}

	function getServiceCreditHistory($dfrom, $dto){
		$res = $this->db->query("SELECT 
								  a.*, CONCAT(b.lname, ',', b.fname, ',', b.mname) AS fullname, deptid, d.description, b.teachingtype
								FROM
								  employee_service_credit a 
								  INNER JOIN employee b ON b.`employeeid` = a.`employeeid` 
								  INNER JOIN `code_office` c ON c.`code` = b.`deptid`
								  INNER JOIN `code_position` d ON d.`positionid` = b.`positionid`
								WHERE `date` BETWEEN '$dfrom' 
								  AND '$dto' ;
								");
		if($res->num_rows > 0) return $res->result();
		else return false;
	}

	function getSCUDetailsEdit($code){
		$query = $this->db->query("SELECT * FROM sc_app_use WHERE id = '$code' ");
		if($query->num_rows > 0) return $query->result_array();
		else return false;
	}

	function updateSCUse($date, $ishalfday, $scdate, $sc_days, $code, $remarks){
		$q_update = 0;
		$query = $this->db->query("SELECT * FROM sc_app_use WHERE id = '$code' ");
		if($query->num_rows > 0) $q_update = $this->db->query("UPDATE sc_app_use SET date='$date', service_credit_use = '$sc_days', needed_service_credit = '$sc_days', service_credit_date_use = '$scdate', remark = '$remarks' WHERE id = '$code' ");
		if($q_update) return "Successfully Update";
		else return "Failed to Update";
	}

	function validateAvaliableSC($date){
		$user = $this->session->userdata("username");
        $query = $this->db->query("SELECT * FROM employee_service_credit WHERE employeeid = '$user' AND date = '$date' ");
        if($query->num_rows > 0) return $query->row()->available_sc;
        else return false;
	}

	function checkSCDateAvailabilityWithHoliday($date, $employeeid){
		$allowedToApply = 0;
		$date = date("Y-m-d",strtotime($date));
		$employee_schedule = $this->db->query("SELECT * 
												FROM employee_schedule 
												WHERE employeeid = '{$employeeid}' AND idx  = DATE_FORMAT('{$date}','%w')")->result();

		if(count($employee_schedule) > 0){
			$is_holiday = $this->db->query("SELECT * FROM code_holiday_calendar WHERE '$date' BETWEEN date_from AND date_to ");
			if($is_holiday->num_rows > 0) return $allowedToApply;
			else return $employee_schedule;
		}else{
			return $employee_schedule;
		}

	}

	function getSCStatus($code){
		$status = '';
		$checking = $this->db->query("SELECT * FROM sc_app_emplist WHERE base_id ='{$code}' ");
		if($checking->num_rows > 0){
			$status = $checking->row()->status;
			if($status == "APPROVED") return true;
			else return false;
		}
	}

	function getAvailableSCBalances($employeeid){
		$q_pending_request = $this->db->query("SELECT SUM(a.`service_credit`) as nodays FROM sc_app a INNER JOIN sc_app_emplist b ON a.`id` = b.`base_id` WHERE employeeid = '$employeeid' AND b.`status` = 'PENDING';")->row()->nodays;
		$q_available_credit = $this->db->query("SELECT SUM(available_sc) as available_sc FROM employee_service_credit WHERE employeeid = '$employeeid' ORDER BY date DESC ")->row()->available_sc;
		$available_balance = $q_available_credit - $q_pending_request;
		return $available_balance;
	}

	function getAvailableSCUBalances($employeeid){
		$q_pending_request = $this->db->query("SELECT SUM(a.`needed_service_credit`) as nodays FROM sc_app_use a INNER JOIN sc_app_use_emplist b ON a.`id` = b.`base_id` WHERE employeeid = '$employeeid' AND b.`status` = 'PENDING';")->row()->nodays;
		$q_available_credit = $this->db->query("SELECT SUM(available_sc) as used_sc FROM employee_service_credit WHERE employeeid = '$employeeid' ORDER BY date DESC ")->row()->used_sc;
		$available_balance = $q_available_credit - $q_pending_request;
		return $available_balance;
	}

}
?>

