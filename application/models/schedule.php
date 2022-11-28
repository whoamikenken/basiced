<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schedule extends CI_Model {


	function getOfficialSchedList($schedid=''){
		$schedlist = array();
		$wC = '';
		if($schedid) $wC .= " WHERE schedid='$schedid'";
		$res = $this->db->query("SELECT * FROM code_schedule $wC");

		foreach ($res->result() as $key => $row) {
			$schedlist[$row->schedid] = $row->description;
		}
		return $schedlist;
	}
	function getOfficialSchedDetail($schedid=''){
	}
	/**
	 Employee Edit / Delete Schedules
	**/
	function SCHEDactions($id,$job,$base_id)
	{
		$msg = "";
		if ($job == "delete") 
		{
			$sched_app = $this->db->query("DELETE FROM change_sched_app WHERE id='$id' ");
			if ($sched_app) 
			{
				$sched_app_detail = $this->db->query("DELETE FROM change_sched_app_detail WHERE base_id='$base_id'");
				if ($sched_app_detail) 
				{
					$sched_app_emplist = $this->db->query("DELETE FROM change_sched_app_emplist WHERE base_id='$base_id'");
					if ($sched_app_emplist) 
					{
						$msg = "Successfully Deleted";
					}
					else
					{
						$msg = "Failed to saved!";
					}
				}
			}			
		}
		return $msg;
	}
    /**
	 * Inserts new change sched application in base table and gets last inserted id.
	 *
	 * @return int
	 */
    function insertBaseSchedApp($user, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq, $date_effective, $specific, $start, $end,$reason){
    	$id = "";
    	$res = $this->db->query("INSERT INTO change_sched_app (
    			applied_by,  dhead,		chead,	 hrhead, cphead, 	fdhead, 	bohead, 	phead, 	uphead, 	dseq, 	cseq, 	hrseq, cpseq, 	fdseq, 	boseq, 		pseq, 	upseq, date_effective, date_applied, isTemporary, dfrom, dto,reason) VALUES (
    			'$user', 	'$dhead', '$chead', '$hrhead', '$cphead', '$fdhead', '$bohead', '$phead', '$uphead', '$dseq', '$cseq', '$hrseq', '$cpseq', '$fdseq', '$boseq', '$pseq', '$upseq', '$date_effective', CURRENT_DATE, '$specific', '$start', '$end','$reason')
    			");
    	if($res)  	$id = $this->db->insert_id();
    	return $id;
    }
    function earlydismissal($id,$rangefrom,$rangeto,$tardy,$absent,$early,$job,$year,$sequences)
	{
        $return = array("err_code"=>0,"msg"=>'');
		if ($job =="update") {
			$tardy = $tardy * 60;
			$tardy = $this->attcompute->sec_to_hm($tardy);
			$absent = $absent * 60;
			$absent = $this->attcompute->sec_to_hm($absent);
			$early = $early * 60;
			$early = $this->attcompute->sec_to_hm($early);

			$stime=$etime=$total=$day=$b=$ttardy=$tabsent=$tearly=$r=$tstart=$tend=$tardyset=$comptardy=$absentset=$compabsent=$earlyset=$compearly="";
						//EMPLOYE SCHEDULE
						$query = $this->db->query("SELECT dayofweek,employeeid,starttime,endtime FROM employee_schedule WHERE DATE_FORMAT(dateedit,'%Y')= '{$year}'  AND (leclab='LEC' OR leclab= 'LAB')");
						if ($query->num_rows()>0) {
						  // print_r($query->result());
						 foreach ($query->result()  as $key => $row) {
						            
						           $stime = $row->starttime;
						           $etime = $row->endtime;
						           $day   = $row->dayofweek;
						           $tstart = date('H:i:s',strtotime($row->starttime));
						           $tend  = date('H:i:s',strtotime($row->endtime));
						           $id = $row->employeeid;
						           $total = (abs(strtotime($row->starttime) - strtotime($row->endtime))/ 3600)*60;
						          
						           $tardyset = date('H:i:s',strtotime($tardy));
						           $comptardy = strtotime($tardyset) - strtotime("00:00:00");
						           $ttardy = date("H:i:s A",strtotime($tstart)+$comptardy);
						          
						           $absentset = date('H:i:s',strtotime($absent));
						           $compabsent = strtotime($absentset) - strtotime("00:00:00");
						           $tabsent = date("H:i:s A",strtotime($tstart) + $compabsent);
						          
						           $earlyset = date('H:i:s',strtotime($early));
						           $compearly = strtotime($earlyset) - strtotime("00:00:00");
						           $tearly = date("H:i:s A",strtotime($tend) - $compearly);
						           // echo 'START TIME '.$row->starttime.'<br> END TIME '.$row->endtime. '<br> TARDY '. $ttardy. '<br> ABSENT '.$tabsent .' EARLY '. $tabsent;  
						           if($total >= $rangefrom && $total <= $rangeto)
						            {
						              $query = $this->db->query("UPDATE employee_schedule SET tardy_start='{$ttardy}',absent_start='{$tabsent}',early_dismissal='{$tearly}' WHERE dayofweek='{$day}' AND employeeid='$id' AND starttime='{$tstart}' AND endtime='{$tend}'  ");
						            } 
						             
						         }
						}
						//EMPLOYE SCHEDULE HISTORY
						$query = $this->db->query("SELECT dayofweek,employeeid,starttime,endtime FROM employee_schedule_history WHERE DATE_FORMAT(dateactive,'%Y')= '{$year}'  AND (leclab='LEC' OR leclab= 'LAB')");
						if ($query->num_rows()>0) {
						  // print_r($query->result());
						 foreach ($query->result()  as $key => $row) {
						            
						           $stime = $row->starttime;
						           $etime = $row->endtime;
						           $day   = $row->dayofweek;
						           $tstart = date('H:i:s',strtotime($row->starttime));
						           $tend  = date('H:i:s',strtotime($row->endtime));
						           $id = $row->employeeid;
						           $total = (abs(strtotime($row->starttime) - strtotime($row->endtime))/ 3600)*60;
						         
						           $tardyset = date('H:i:s',strtotime($tardy));
						           $comptardy = strtotime($tardyset) - strtotime("00:00:00");
						           $ttardy = date("H:i:s A",strtotime($tstart)+$comptardy);

						           $absentset = date('H:i:s',strtotime($absent));
						           $compabsent = strtotime($absentset) - strtotime("00:00:00");
						           $tabsent = date("H:i:s A",strtotime($tstart) + $compabsent);

						           $earlyset = date('H:i:s',strtotime($early));
						           $compearly = strtotime($earlyset) - strtotime("00:00:00");
						           $tearly = date("H:i:s A",strtotime($tend) - $compearly);

						           // echo 'START TIME '.$row->starttime.'<br> END TIME '.$row->endtime. '<br> TARDY '. $ttardy. '<br> ABSENT '.$tabsent .' EARLY '. $tabsent;  

						           if($total >= $rangefrom && $total <= $rangeto)
						            {
						              $query = $this->db->query("UPDATE employee_schedule_history SET tardy_start='{$ttardy}',absent_start='{$tabsent}',early_dismissal='{$tearly}' WHERE dayofweek='{$day}' AND employeeid='$id' AND starttime='{$tstart}' AND endtime='{$tend}'  ");
						            } 
						         }
						}
						$query = $this->db->query("Update earlydismissal SET rangefrom='{$rangefrom}',rangeto='{$rangeto}',tardy='{$tardy}',absent='{$absent}',early='{$early}',year='{$year}',sequence='{$sequences}' WHERE id='{$id}'");
						if (count($query) >0)
							$return = array("err_code"=>0,"msg"=>'Successfully updated!');
						else
							$return = array("err_code"=>2,"msg"=>'Unable to update data!');
		}
		else {
			$query = $this->db->query("DELETE FROM earlydismissal WHERE id='{$id}'");
			if (count($query) >0) $return = array("err_code"=>0,"msg"=>'Successfully deleted!');
			else
			$return = array("err_code"=>2,"msg"=>'Unable to delete data!');
		}
		// echo $query;die;
		return $return;
	}
    /**
	 * Inserts sched app details as referenced to base table.
	 *
	 * @return query result
	 */
    function insertSchedAppDetail($base_id, $timesched, $tnt,$reason){
    	$res = '';
		$sched_list = explode("|",$timesched);
		foreach($sched_list as $slist){
			$nosched = 0;
			$halfsched = 0;
			list($dow,$idx,$tsched,$tardy,$absent,$halfabsent,$earlyd,$leclab,$toremove,$course,$section,$subject,$aimsdept) = explode("~u~",$slist);
			  $extsched = explode("-",$tsched);
			  $start_time = date("H:i:s",strtotime($extsched[0]));
			  $end_time = date("H:i:s",strtotime($extsched[1]));
			  $tardy = $tardy ? date("H:i:s",strtotime($tardy)) : "";
			  $absent = $absent ? date("H:i:s",strtotime($absent)) : "";
			  $halfabsent = $halfabsent ? date("H:i:s",strtotime($halfabsent)) : "";
			  $earlyd = $earlyd ? date("H:i:s",strtotime($earlyd)) : "";
			  if($toremove=="checked") $start_time = $end_time = "00:00:00";

			  if($tnt == 'nonteaching') $leclab = '';
    			
    		  $res = $this->db->query("INSERT INTO change_sched_app_detail (base_id, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,course,section,subject,aimsdept,reason) 
							VALUES('$base_id','$start_time','$end_time','$dow','$idx','$tardy','$absent','$halfabsent','$earlyd','$leclab','$course','$section','$subject','$aimsdept','$reason')");
		}
		return $res;
    }
    /**
	 * Inserts app in secondary table for list of employees.
	 *
	 * @return int
	 */
    function insertSchedAppEmpList($base_id, $arr_emplist, $teachingType, $dstatus, $ddate, $user, $reason){
    	$empcount = $isread = 0;
    	$arr_data_failed = array();

    	foreach ($arr_emplist as $employeeid) {
    		if($employeeid == $user) $isread = 1;

    		if(isset($employeeid["employeeid"])) $employeeid = $employeeid["employeeid"];

			$res = $this->db->query("
				INSERT INTO change_sched_app_emplist (base_id, employeeid, teachingType, dstatus, ddate, isread, reason) VALUES ('$base_id', '$employeeid', '$teachingType', '$dstatus', '$ddate', '$isread', '$reason')
			");
			if($res) $empcount++;
			else array_push($arr_data_failed, $employeeid);
			$isread = 0;

		}
		return array($empcount,$arr_data_failed);
    }
    /*
    * new function for ica-hyperion 21194
    * by justin (with e)
    */	
    # save change sched app
    function insertSchedAppEmpListByAdmin($base_id, $empID, $teachingType, $dstatus, $ddate, $user, $isDirectApproved, $reason){
    	$isread = 0;
    	
    	# para sa direct approved
    	$status_col = $status_val = '';
    	if($isDirectApproved == 0){
    		$status_col = ', status';
    		$status_val = ", 'APPROVED'";
    	}
    	$res = $this->db->query("INSERT INTO change_sched_app_emplist (base_id, employeeid, teachingType, dstatus, ddate, isread, reason, isDirectApproved {$status_col}) VALUES ('$base_id', '$empID', '$teachingType', '$dstatus', '$ddate', '$isread', '$reason', '$isDirectApproved' $status_val)");

    	$csid = '';

    	if($res) $csid = $this->db->insert_id();  //< @Angelica Ticket #ICA-HYPERION21362
    	return $csid;
    }

    # get list change sched app
    function getChangeSchedListByAdmin($user,$category='',$dfrom='',$dto='',$isLoad=0){
    	$WC = '';
    	# query
    	$sql = "SELECT csa.id AS base_id,csae.id AS csid, csae.`employeeid` AS empId, CONCAT(e.`lname`, ', ', e.`fname`, ' ', e.`mname`) AS fullname, csad.`timestamp`,csa.`date_effective`, 
    			csa.`isTemporary`, csa.`dfrom`, csa.`dto`, csae.`reason`, csae.`status`
					FROM change_sched_app csa
					LEFT JOIN change_sched_app_detail csad ON csad.`base_id` = csa.`id`
					LEFT JOIN change_sched_app_emplist csae ON csae.`base_id` = csa.`id`
					LEFT JOIN employee e ON e.`employeeid` = csae.`employeeid`
						WHERE csae.employeeid != ''";

		if($isLoad > 0){
			if($category) $sql .= " AND csae.`status`='{$category}'"; # kapag sinelect si category..
			if($dfrom && $dto) $sql .=  " AND csa.date_applied BETWEEN '{$dfrom}' AND '{$dto}' "; #kapag may sinelect na date..
		}else{
			$sql .= " AND csae.`status`='PENDING'"; // kapag 0 lahat ng data na idi-displayed nya sa cs_history_admin ay pending. para sa default displayed ito..
		}

		$sql .= " ORDER BY base_id;";
		return $query = $this->db->query($sql)->result();
    }
    /*
    * end of new function for ica-hyperion 21194
    */
    function insertSchedAppEmpListHead($base_id, $arr_emplist, $dhead, $chead, $dseq, $cseq, $user){
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
	    				INSERT INTO change_sched_app_emplist (base_id, employeeid, dstatus, ddate, isread) VALUES ('$base_id', '$employeeid', '$dstatus', '$ddate' , '$isread')
	    			");
	    			if($res) $empcount++;
	    		}
    			
	    		if(in_array($chead, $arr_emplist)){
	    			if($cseq ==  1){
	    				$cstatus = "APPROVED";
		        		$cdate 	 = date_format( new DateTime('today') ,"Y-m-d");
	    			}
	    			$res = $this->db->query("
	    				INSERT INTO change_sched_app_emplist (base_id, employeeid, cstatus, cdate, isread) VALUES ('$base_id', '$employeeid', '$cstatus', '$cdate' , '$isread')
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
	    				INSERT INTO change_sched_app_emplist (base_id, employeeid, dstatus, ddate, cstatus, cdate, isread) VALUES ('$base_id', '$employeeid', '$dstatus', '$ddate' , '$cstatus', '$cdate' , '$isread')
	    			");
	    			if($res) $empcount++;
	    		}
			}
			$isread = 0;
		}
		return $empcount;
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
    function getAppSequencePerSched($id=''){
    	$res = $this->db->query("SELECT * FROM change_sched_app_emplist a INNER JOIN change_sched_app b ON a.`base_id`=b.`id` WHERE a.id='$id'");
    	return $res;
    }

    /**
	 * Get list of days in a week.
	 *
	 * @return array
	 */
   	function getSchedDays(){
        $res = $this->db->query("SELECT day_index, day_code, day_name FROM code_daysofweek ORDER BY day_index");    
        $schedDays = array();  
        if($res->num_rows() > 0 ){
        	foreach ($res->result() as $key => $row) {
        		$schedDays[$row->day_index] = array('day_code'=>$row->day_code,'day_name'=>$row->day_name);
        	}
        }     
        $sun = $schedDays[0];
        unset($schedDays[0]);
        $schedDays[0] = $sun;         
        return $schedDays;
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

		// $dhead = $this->utils->getDeptHead('head',		$deptid);	
		// $chead = $this->utils->getDeptHead('divisionhead',$deptid);	
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

	/**
	 * Gets list of OT applications for given approver to manage.
	 *
	 * @return stdClass Object
	 */
	function getCSAppListToManage($user="", $colhead="", $colstatus='', $status="", $prevcolstatus='',$datefrom="", $dateto="",$teachingType='',$seq_count=''){
		$colseq =  $colhead ? (substr($colhead,0,-4) . 'seq') : '';

		$wC = "";
    	if($datefrom && $dateto) $wC .= " AND b.`date_applied` BETWEEN '$datefrom' AND '$dateto'";
    	if($status)			 	 $wC .= " AND $colstatus='$status'";
		// if($colseq)			 	 $wC .= " AND $colseq!='0'";
		if($seq_count)			 $wC .= " AND ($colseq='$seq_count' OR $colseq='0')";
    	if($prevcolstatus) 	 	 $wC .= " AND $prevcolstatus='APPROVED'";
    	if($teachingType) 	 	 $wC .= " AND c.teachingType='$teachingType'";

		$res = $this->db->query("SELECT a.id AS csid, REPLACE(CONCAT(c.LName, ', ', c.FName, ' ', c.MName),'Ã‘','Ñ') AS fullname,
  			b.*,b.`reason` AS getReason, a.* FROM change_sched_app_emplist a  INNER JOIN change_sched_app b ON b.id = a.`base_id` INNER JOIN employee c ON a.`employeeid` = c.`employeeid` 
								WHERE ($colhead='$user') AND a.status != 'DISAPPROVED' $wC");
		return $res;
	}

	/**
	 * Gets App details per application id.
	 *
	 * @param string $csid (Default: "")
	 * @param string $colstatus (Default: "")
	 *
	 * @return array
	 */
	function getSchedEmpDetails($csid='', $colstatus=''){
		$data =array();
		$res = $this->db->query("SELECT a.id AS csid, REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname,e.description AS epos, d.description AS edept, a.*,b.*,f.*
									FROM change_sched_app_emplist a
									INNER JOIN change_sched_app b ON b.id=a.`base_id`
									INNER JOIN employee c ON a.`employeeid`=c.`employeeid`
									LEFT JOIN code_position e ON c.positionid = e.positionid
									LEFT JOIN code_office d ON c.deptid = d.code 
									INNER JOIN change_sched_app_detail f ON b.id=f.`base_id`
									WHERE a.id='$csid'");
		if($res->num_rows() > 0){
			foreach ($res->result() as $obj) {
				$data['csid'] 			= $obj->csid;
				$data['base_id'] 		= $obj->base_id;
				$data['employeeid'] 	= $obj->employeeid;
				$data['date_applied'] 	= $obj->date_applied;
				$data['status'] 		= $obj->status;
				if($colstatus) 	$data['colstat']= $obj->$colstatus; 
				$data['fullname'] 		= $obj->fullname;
				$data['pos'] 			= $obj->epos;
				$data['edept']  		= $obj->edept;
				$data['date_effective']	= $obj->date_effective;
				$data['isTemporary']	= $obj->isTemporary;
				$data['dfrom']  		= $obj->dfrom;
				$data['dto']  			= $obj->dto;
				$data['course']  		= $obj->course;
				$data['section']  		= $obj->section;
				$data['subject']  		= $obj->subject;
				$data['aimsdept']  		= $obj->aimsdept;
			}
		}
		return $data;
	}

	/**
	 * Get sched app detail from secondary table with given base id.
	 *
	 * @return query result
	 */
	function getSchedDetails($base_id='',$colstatus=''){
		$data='';
		$res = $this->db->query("SELECT *
								FROM change_sched_app_detail a 
								LEFT JOIN code_daysofweek b ON a.dayofweek = b.day_code WHERE base_id='$base_id'
								");
		if($res->num_rows() > 0){
			$data = $res->result();
		}
		return $data;
	}

	function saveChangeSchedule($scid='',$timesched='',$reason='')
	{
		$sched_list = explode("|",$timesched);
			foreach($sched_list as $slist){
				$nosched = 0;
				$halfsched = 0;
				list($detail_id,$dow,$idx,$tsched,$tardy,$absent,$halfabsent,$earlyd,$leclab,$isremove) = explode("~u~",$slist);
				  $extsched = explode("-",$tsched);
				  $start_time = date("H:i:s",strtotime($extsched[0]));
				  $end_time = date("H:i:s",strtotime($extsched[1]));
				  $tardy = $tardy ? date("H:i:s",strtotime($tardy)) : "";
				  $absent = $absent ? date("H:i:s",strtotime($absent)) : "";
				  $halfabsent = $halfabsent ? date("H:i:s",strtotime($halfabsent)) : "";
				  $earlyd = $earlyd ? date("H:i:s",strtotime($earlyd)) : "";
				  if($isremove=="checked") $start_time = $end_time = "00:00:00";

		  // if($tnt == 'nonteaching') $leclab = '';
    		$res = $this->db->query("UPDATE change_sched_app_detail SET 
	    		  						starttime = '$start_time',
	    		  						endtime = '$end_time',
	    		  						dayofweek = '$dow',
	    		  						idx = '$idx',
	    		  						tardy_start = '$tardy',
	    		  						absent_start = '$absent',
	    		  						absent_half_start = '$halfabsent',
	    		  						early_dismissal = '$earlyd',
	    		  						leclab = '$leclab',
	    		  						reason = '$reason'
	    		  					WHERE id='$detail_id'
								");
				  
			}
			return $res;

	}

	/**
	 * Saves new status of application made by approver. Inserts changes to official schedule if approved.
	 *
	 * @return query result
	 */
	function saveSchedStatusChange($user='',$csid='',$employeeid='', $status='',$colstatus='',$coldate='',$colhead='',$isLastApprover='', $timesched='', $base_id='', $date_active='',$reason='',$prev_colhead='', $course=''){
		$res = $prev_wC ='';

		if($colhead) 			$prev_wC = " AND $colhead='$user'";
		if($prev_colhead) 		$prev_wC = " AND $prev_colhead='$user'";
		$test_q = $this->db->query("SELECT a.id FROM change_sched_app_emplist a INNER JOIN change_sched_app b ON b.id=a.base_id WHERE a.base_id='$base_id' $prev_wC");

		if($test_q->num_rows() > 0){
		
			if($colstatus == 'hrstatus'){
				$sched_list = explode("|",$timesched);
				foreach($sched_list as $slist){
					$nosched = 0;
					$halfsched = 0;
					list($detail_id,$dow,$idx,$tsched,$tardy,$absent,$halfabsent,$earlyd,$leclab,$isremove) = explode("~u~",$slist);
					  $extsched = explode("-",$tsched);
					  $start_time = date("H:i:s",strtotime($extsched[0]));
					  $end_time = date("H:i:s",strtotime($extsched[1]));
					  $tardy = $tardy ? date("H:i:s",strtotime($tardy)) : "";
					  $absent = $absent ? date("H:i:s",strtotime($absent)) : "";
					  $halfabsent = $halfabsent ? date("H:i:s",strtotime($halfabsent)) : "";
					  $earlyd = $earlyd ? date("H:i:s",strtotime($earlyd)) : "";
					  if($isremove=="checked") $start_time = $end_time = "00:00:00";

					  // if($tnt == 'nonteaching') $leclab = '';
		    			
		    		    $this->db->query("UPDATE change_sched_app_detail SET 
		    		  						starttime = '$start_time',
		    		  						endtime = '$end_time',
		    		  						dayofweek = '$dow',
		    		  						idx = '$idx',
		    		  						tardy_start = '$tardy',
		    		  						absent_start = '$absent',
		    		  						absent_half_start = '$halfabsent',
		    		  						early_dismissal = '$earlyd',
		    		  						leclab = '$leclab',
		    		  						reason = '$reason',
		    		  						course = '$course'
		    		  					WHERE id='$detail_id'
									");
					  
				}
			}
			$this->db->query("UPDATE change_sched_app_detail set course = '$course' where base_id='$base_id'");
			if($date_active) $this->db->query("UPDATE change_sched_app SET date_effective='$date_active' WHERE id='$base_id'");

			if($status == 'DISAPPROVED' || $isLastApprover){
				// additional by justin (with e)  for ica-hyperion 21983
				if($this->session->userdata("usertype") == "ADMIN"){
					$res = $this->db->query("UPDATE change_sched_app_emplist SET `status`='$status', isApprovedByAdmin=1 WHERE base_id='$base_id'");

					$isLastApprover = true;
				}else $res = $this->db->query("UPDATE change_sched_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE, status='$status' WHERE base_id='$base_id'");
			}else{
				// additional by justin (with e)  for ica-hyperion 21983
				if($this->session->userdata("usertype") == "ADMIN"){
					$res = $this->db->query("UPDATE change_sched_app_emplist SET `status`='$status', isApprovedByAdmin=1 WHERE base_id='$base_id'");

					$isLastApprover = true;
				}else $res = $this->db->query("UPDATE change_sched_app_emplist SET $colstatus='$status', $coldate=CURRENT_DATE WHERE base_id='$base_id'");
			}


			if($status == 'APPROVED' && $isLastApprover){
				///<check if temporary schedule
				$isTemporary = $dfrom = $dto = "";
				$tmp = $this->db->query("SELECT isTemporary, dfrom, dto FROM change_sched_app WHERE id='$base_id'");

				if($tmp->num_rows() > 0){
					$isTemporary 	= $tmp->row(0)->isTemporary;
					$dfrom 			= $tmp->row(0)->dfrom;
					$dto 			= $tmp->row(0)->dto;
				}

				if($isTemporary){
					$dow = '';
					$dow_q = $this->db->query("SELECT GROUP_CONCAT(CONCAT_WS(',',DAYOFWEEK)) as dow FROM change_sched_app_detail WHERE base_id='$base_id'");
					if($dow_q->num_rows() > 0) $dow = $dow_q->row(0)->dow;
				
					///< insert initial sched with diff date active
					$this->db->query("INSERT INTO employee_schedule_history(employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,dateactive, subject, course, section, aimsdept) 
			                             (SELECT DISTINCT  employeeid, starttime, endtime, DAYOFWEEK, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab, CONCAT('$dto',' 23:59:00'), subject, course, section, aimsdept
											FROM employee_schedule_history a WHERE employeeid='$employeeid' AND FIND_IN_SET(DAYOFWEEK,'$dow')
											AND DATE_FORMAT(dateactive,'%Y-%m-%d') = 
											(SELECT z.dateactive FROM employee_schedule_history z 
											WHERE z.`employeeid` = a.`employeeid` AND FIND_IN_SET(z.DAYOFWEEK,'$dow') GROUP BY dateactive ORDER BY dateactive DESC LIMIT 1))");



					///< insert temp sched
					 $res = $this->db->query("INSERT INTO employee_schedule_history(employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,dateactive, subject, course, section, aimsdept) 
			                             (SELECT employeeid, starttime, endtime, DAYOFWEEK, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,CONCAT((DATE(c.dfrom)-INTERVAL 1 DAY),' 23:59:00'), subject, course, section, aimsdept
											FROM change_sched_app_emplist a 
											INNER JOIN change_sched_app c ON c.`id`=a.`base_id`
											INNER JOIN change_sched_app_detail b ON a.`base_id`=b.`base_id` 
											WHERE a.employeeid='{$employeeid}' AND a.base_id='$base_id')");

					/*$this->db->query("INSERT INTO employee_official_schedule_history (employeeid,datefrom,dateto,start_time,end_time,tardy,absent,halfday_absent,early_dismissal,user,timestamp) VALUES ('$employeeid','$dfrom','$dto','$fromtime','$totime','$tardy','$fabsent','$habsent','$earlyd','$uname','".date('Y-m-d h:i:s')."')");*/


				}else{


					$this->db->query("DELETE FROM employee_schedule WHERE employeeid = '$employeeid'");

		            $this->db->query("INSERT INTO employee_schedule(employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,dateedit, subject, course, section, aimsdept) 
			                             (SELECT employeeid, starttime, endtime, DAYOFWEEK, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,CONCAT((DATE(c.date_effective)-INTERVAL 1 DAY),' 00:00:00'), subject, course, section, aimsdept
											FROM change_sched_app_emplist a 
											INNER JOIN change_sched_app c ON c.`id`=a.`base_id`
											INNER JOIN change_sched_app_detail b ON a.`base_id`=b.`base_id`  
											WHERE a.employeeid='{$employeeid}' AND a.base_id='$base_id')");

		            $res = $this->db->query("INSERT INTO employee_schedule_history(employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab, dateactive, subject, course, section, aimsdept) 
			                             (SELECT employeeid, starttime, endtime, DAYOFWEEK, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,CONCAT((DATE(c.date_effective)-INTERVAL 1 DAY),' 00:00:00'), subject, course, section, aimsdept
											FROM change_sched_app_emplist a 
											INNER JOIN change_sched_app c ON c.`id`=a.`base_id`
											INNER JOIN change_sched_app_detail b ON a.`base_id`=b.`base_id` 
											WHERE a.employeeid='{$employeeid}' AND a.base_id='$base_id')");
					
				}
				$this->db->query("UPDATE change_sched_app_emplist SET isread=0 WHERE base_id='$base_id'");
			}

		} ///< end if test_q

		return $res;
	}
	/**
	 * Gets list of employee applications.
	 *
	 * @return stdClass Object
	 */
    function getEmpSchedHistory($employeeid="", $datefrom="", $dateto="", $status="", $id="", $isread=''){
    	$wC = "";
    	if($datefrom && $dateto) $wC .= " AND b.`date_applied` BETWEEN '$datefrom' AND '$dateto'";
    	if($status)				 $wC .= " AND a.`status`='$status'";
    	if($id)				 	$wC .= " AND a.id='$id'";
    	// if($isread <> '')		 $wC .= " AND a.isread='$isread'";
        $res = $this->db->query("SELECT a.id AS csid, a.*,b.* ,REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname
        							FROM change_sched_app_emplist a
									INNER JOIN change_sched_app b ON a.`base_id`=b.`id`
									INNER JOIN employee c ON a.employeeid=c.employeeid
									WHERE a.employeeid='$employeeid' 
									$wC");
									// OR b.applied_by='$employeeid' 
        return $res;
	}

	function getCSManagementHistory($category='', $deptid='', $dfrom='', $dto=''){
		$wC = '';
		if($category)		$wC .= " AND a.`status`='$category'";
		if($deptid) 		$wC .= " AND c.deptid='$deptid'";
		if($dfrom && $dto)  $wC .= " AND b.`date_applied` BETWEEN '$dfrom' AND '$dto'";

        $res = $this->db->query("SELECT a.id AS csid, a.*,b.* ,REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname
        							FROM change_sched_app_emplist a
									INNER JOIN change_sched_app b ON a.`base_id`=b.`id`
									INNER JOIN employee c ON a.employeeid=c.employeeid
									WHERE IFNULL(a.employeeid,'')!='' 
									$wC");
        return $res;
	}

	/**
	 * Saves new change schedule application directly by HR.
	 *
	 * @return string
	 */
	function saveSchedAppHRDirect($user, $arr_emplist, $hrhead, $date_effective, $timesched, $tnt){
		$base_id = "";
		$empcount = 0;
		$start_time = $end_time = $tardy = $absent = $halfabsent = $earlyd = $leclab = $dow = $idx = '';

		$sched_list = explode("|",$timesched);
		foreach($sched_list as $slist){
			$nosched = 0;
			$halfsched = 0;
			list($dow,$idx,$tsched,$tardy,$absent,$halfabsent,$earlyd,$leclab,$isremove) = explode("~u~",$slist);
			  $extsched = explode("-",$tsched);
			  $start_time = date("H:i:s",strtotime($extsched[0]));
			  $end_time = date("H:i:s",strtotime($extsched[1]));
			  $tardy = $tardy ? date("H:i:s",strtotime($tardy)) : "";
			  $absent = $absent ? date("H:i:s",strtotime($absent)) : "";
			  $halfabsent = $halfabsent ? date("H:i:s",strtotime($halfabsent)) : "";
			  $earlyd = $earlyd ? date("H:i:s",strtotime($earlyd)) : "";
			  if($isremove=="checked") $start_time = $end_time = "00:00:00";

			  if($tnt == 'nonteaching') $leclab = '';
			  
		}

		$res = $this->db->query("INSERT INTO change_sched_app (
				applied_by,	 hrhead, hrseq,  date_effective, date_applied) VALUES (
				'$user',  '$hrhead','1', '$date_effective', CURRENT_DATE)
				");
		if($res)  	$base_id = $this->db->insert_id();

		if($base_id) $res = $this->insertSchedAppDetail($base_id, $timesched, $tnt);
		if($res){
			$datebefore = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date_effective) ) ));
	    	foreach ($arr_emplist as $employeeid) {
				$res = $this->db->query("
					INSERT INTO change_sched_app_emplist (base_id, employeeid, status, hrstatus, hrdate) VALUES ('$base_id', '$employeeid', 'APPROVED', 'APPROVED', CURRENT_DATE)
				");
				if($res) {
					$this->db->query("DELETE FROM employee_schedule WHERE employeeid = '$employeeid'");

		            $this->db->query("INSERT INTO employee_schedule(employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,dateedit) VALUES ('$employeeid', '$start_time', '$end_time', '$dow', '$idx', '$tardy', '$absent', '$halfabsent', '$earlyd', '$leclab', '$datebefore')");

		            $res = $this->db->query("INSERT INTO employee_schedule_history(employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,dateactive) VALUES ('$employeeid', '$start_time', '$end_time', '$dow', '$idx', '$tardy', '$absent', '$halfabsent', '$earlyd', '$leclab', '$datebefore')");
				}
				if($res) $empcount++;
			}
		}

		return $empcount;

	}

	function showSelectAimsDept(){
        $return = "<option value=''>Choose Aims department..</option>"; 
        $query = $this->db->query("SELECT * FROM tblCourseCategory")->result();
       foreach($query as $val){
          $return .= "<option value='".$val->GROUP_ID."'>".$val->DESCRIPTION."</option>";    
       }
       	return $return;
    }  

    function showSubject(){
    	// no data, connected to aims..
        $return = "<option value=''>Select an Option</option>"; 
       	return $return;
    } 

	function getEmployeeScheduleHistory($employeeid=''){
		$res = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid='$employeeid' ORDER BY dateactive DESC, idx ASC, starttime ASC");
		return $res;
	}


	function updateEmployeeScheduleHistory($user='',$sched_id='',$employeeid='',$timesched="",$dateactive_time="00:00:00"){
		$res = "";
		if($timesched){
			list($dayofweek,$starttime,$endtime,$tardy_start,$absent_start,$early_dismissal,$leclab,$flexible,$hours,$breaktime,$dateactive,$aimsval,$subjectval,$weekly_sched) = explode('~u~', $timesched);

			$dateactive = new DateTime($dateactive);
			$dateactive->modify('-1 day');
			$dateactive = $dateactive->format('Y-m-d') . " " . $dateactive_time;

			$starttime = date("H:i:s",strtotime($starttime));
			$endtime = date("H:i:s",strtotime($endtime));
			$tardy_start = date("H:i:s",strtotime($tardy_start));
			$absent_start = date("H:i:s",strtotime($absent_start));
			$early_dismissal = date("H:i:s",strtotime($early_dismissal));
			$aims = $aimsval;
			$subject = $subjectval;

			$res = $this->db->query("UPDATE employee_schedule_history 
										SET starttime='$starttime',
											endtime='$endtime',
											tardy_start='$tardy_start',
											absent_start='$absent_start',
											early_dismissal='$early_dismissal',
											leclab='$leclab',
											flexible='$flexible',
											`hours`='$hours',
											breaktime='$breaktime',
											mode='day',
											dateactive='$dateactive',
											changeby='$user',
											aimsdept = '$aims',
											subject = '$subject',
											weekly_sched = '$weekly_sched'
										WHERE editstamp='$sched_id' AND dayofweek='$dayofweek' AND employeeid='$employeeid'
										");
			// echo "<pre>"; print_r($this->db->last_query()); die;
		}
		return $res;
	}

	function deleteEmployeeScheduleHistory($sched_id='',$employeeid=''){
		$res = $this->db->query("DELETE FROM employee_schedule_history WHERE editstamp='$sched_id' AND employeeid='$employeeid'");
		return $res;
	}

	function isApprovedByAdmin($id){
		$is_admin_approved = 0;
		$status = "";

		$q_admin_approved = $this->db->query("SELECT isApprovedByAdmin, `status` FROM change_sched_app_emplist WHERE id='$id';")->result();
		foreach ($q_admin_approved as $row){
			$is_admin_approved = $row->isApprovedByAdmin;
			$status = $row->status;
		}

		return array($is_admin_approved, $status);
	}

	function findFinalizedEmp($emp_list='',$date_apply=''){
		$res = "";
		foreach ($emp_list as $employeeid) {
			$resFnd = 0;
			$empIDFinal = $employeeid;
			if(strlen($empIDFinal)) $empIDFinal = "0".$employeeid;

			$findFinalEmp = $this->db->query("SELECT * FROM attendance_confirmed_nt WHERE employeeid='".$empIDFinal."' ")->result();
			foreach ($findFinalEmp as $ffe) {
				$date_apply = date('Ymd', strtotime($date_apply));
				$cutOffStart = date('Ymd', strtotime($ffe->cutoffstart));
				$cutOffEnd = date('Ymd', strtotime($ffe->cutoffend));
				if($date_apply >= $cutOffStart && $date_apply <= $cutOffEnd) $resFnd = 1; 
			}
			
			if($resFnd == 1){
				$empDetails = $this->db->query("SELECT * FROM user_info WHERE username='".$employeeid."'")->result();
				foreach ($empDetails as $ed) {
					$res = $res ."* ".$employeeid." - ".$ed->lastname.", ".$ed->firstname." ".$ed->middlename."\n";
				}
			}

		}
		
		return $res;
	}

	function isScheduleCodeExists($code){
		return $this->db->query("SELECT * FROM code_schedule WHERE schedcode = '$code' ")->num_rows();
	}
	
} //endoffile