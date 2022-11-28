<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student extends CI_Model {

	function getStudSYList(){
		$sylist = array();
		$sql = $this->db->query("SELECT DISTINCT SY FROM Poveda.tblStatusHistory WHERE SY<>'' ORDER BY SY DESC)
		                                UNION ALL
		                               (SELECT DISTINCT SY FROM Poveda.tblConfig WHERE SY<>'' AND SY NOT IN (SELECT DISTINCT SY FROM Poveda.tblStatusHistory WHERE SY<>'' ORDER BY SY DESC) ORDER BY SY DESC)
		                                UNION ALL
		                               (SELECT DISTINCT SY FROM Poveda.tblSchedule WHERE SY<>''
		                                                                    AND SY NOT IN (SELECT DISTINCT SY FROM Poveda.tblStatusHistory WHERE SY<>'')
		                                                                    AND SY NOT IN (SELECT DISTINCT SY FROM Poveda.tblConfig WHERE SY<>'' AND SY NOT IN (SELECT DISTINCT SY FROM Poveda.tblStatusHistory WHERE SY<>'')))
		                                ORDER BY SY DESC;");
		if($sql->num_rows() > 0){
			foreach ($sql->result() as $key => $row) {
			    $sylist[$row->SY] = $row->SY;
			}
		}

		return $sylist;

	}

	function getStudDepartmentList(){
		$deptlist = array();
		$sql = $this->db->query("SELECT code, description FROM Poveda._depttypes ORDER BY description");
		if($sql->num_rows() > 0){
			foreach ($sql->result() as $key => $row) {
			    $deptlist[$row->code] = $row->description;
			}
		}

		return $deptlist;
	}

	function getStudYearLevelList(){
		$yearlevellist = array();
		$sql = $this->db->query("SELECT DISTINCT YearLevel FROM Poveda.tblStudClassList ");
		if($sql->num_rows() > 0){
			foreach ($sql->result() as $key => $row) {
			    $yearlevellist[$row->YearLevel] = $row->YearLevel;
			}
		}

		return $yearlevellist;
	}

	function getStudSectionList(){
		$sectionlist = array();
		$sql = $this->db->query("SELECT DISTINCT SectCode FROM Poveda.tblStudClassList");
		if($sql->num_rows() > 0){
			foreach ($sql->result() as $key => $row) {
			    $sectionlist[$row->SectCode] = $row->SectCode;
			}
		}

		return $sectionlist;
	}

	function getStudList($sy='',$sem='',$yl='',$section='',$coursecode='',$dept=''){

		$wC = '';
		$cond = $ret = array();
		if($sy) 			array_push($cond,"sy = '$sy'");
		if($sem) 			array_push($cond,"sem = '$sem'");
		if($yl) 			array_push($cond,"yearlevel = '$yl'");
		if($section) 		array_push($cond,"section = '$section'");
		if($coursecode) 	array_push($cond,"coursecode = '$coursecode'");
		if($dept) 			array_push($cond,"depttype = '$dept'");
		if(sizeof($cond) > 0) {
			$wC = implode(' AND ', $cond);
			$wC = 'WHERE ' . $wC;
		}

		$res = $this->db->query("SELECT * FROM student $wC limit 1");	

		return $res;
	}

	function insertBaseStudSched($dateactive='',$sy='',$yearlevel='',$section='',$dept=''){
		$id = "";
		$user = $this->session->userdata('username');

		$res = $this->db->query("INSERT INTO student_schedule_base (department, SY, Sem, YearLevel, CourseCode, SectCode, ParentSection, SubjCode, FCode, addedby) 
													VALUES ('$dept','$sy','','$yearlevel','','','$section','','','$user')");
		if($res)  	$id = $this->db->insert_id();
    	return $id;
	}

	function insertStudSchedDetail($base_id='',$timesched='',$dateactive=''){
    	$res = '';
		$sched_list = explode("|",$timesched);
		foreach($sched_list as $slist){
			$nosched = 0;
			$halfsched = 0;
			list($dow,$idx,$tsched,$tardy,$absent,$halfabsent,$earlyd,$leclab) = explode("~u~",$slist);
			  $extsched = explode("-",$tsched);
			  $start_time = date("H:i:s",strtotime($extsched[0]));
			  $end_time = date("H:i:s",strtotime($extsched[1]));
			  $tardy = $tardy ? date("H:i:s",strtotime($tardy)) : "";
			  $absent = $absent ? date("H:i:s",strtotime($absent)) : "";
			  $halfabsent = $halfabsent ? date("H:i:s",strtotime($halfabsent)) : "";
    			
    		  $res = $this->db->query("INSERT INTO student_schedule_detail (base_id, starttime, endtime, dayofweek, tardy_start, absent_start, absent_half_start, dateactive) 
							VALUES('$base_id','$start_time','$end_time','$dow','$tardy','$absent','$halfabsent','$dateactive')");
			  
		}
		return $res;
	}

	function isDuplicateSched($schedid='',$sy='',$yearlevel='',$section='',$dept='',$dept_arr=array()){
		$isDuplicateSched = false;
		$wC = '';
		if($schedid) $wC .= " AND schedid != '$schedid'";
		$sched_q = $this->db->query("SELECT schedid FROM student_schedule_base
							WHERE (SY='$sy' OR SY='ALL') AND (YearLevel='$yearlevel' OR YearLevel='ALL') 
								AND (ParentSection='$section' OR ParentSection='ALL') 
								$wC;");
		if($sched_q->num_rows() > 0){
			foreach ($sched_q->result() as $key => $row) {
				$dept_tmp_arr = explode(',', $row->department);
				if(sizeof(array_intersect($dept_arr, $dept_tmp_arr)) > 0){
					$isDuplicateSched = true;
				}
			}
		}
		return $isDuplicateSched;
	}


	function getLogTime($studentid="",$date="",$tstart="",$tend="",$absent_start='',$earlyd='',$tbl=""){
		
        $return = array("","","");
        if($tbl == "NEW")   $tbl = "timesheet";
        else                $tbl = "timesheet_bak";

        $wCAbsentEarlyD = '';
        if($absent_start) $wCAbsentEarlyD .= " AND TIME(timeout) > '$absent_start'";
        if($earlyd)       $wCAbsentEarlyD .= " AND TIME(timein) < '$earlyd'";
        
		$query = $this->db->query("SELECT timein,timeout,otype FROM $tbl WHERE userid='$studentid' AND DATE(timein)='$date' AND TIME(timein)<='$tend' AND TIME(timeout) > '$tstart' $wCAbsentEarlyD ORDER BY timein ASC LIMIT 1");
		if($query->num_rows() > 0){
          
			$timein  = $query->row($seq)->timein;
            $timeout = $query->row($seq)->timeout;
            $otype   = $query->row($seq)->otype;
									
	
            $return = array($timein,$timeout,$otype);
        }else{
        	$query = $this->db->query("SELECT logtime FROM timesheet_trail WHERE userid='$studentid' AND DATE(logtime)='$date' AND log_type = 'IN' ORDER BY logtime DESC LIMIT 1");
        	if($query->num_rows() > 0){
	            $timein  = $query->row($seq)->logtime;
	            $return = array($timein,"","");
            }else
                $return = array("","",true);
        }	
        
        return $return;
    }

    function getInOut($studentid,$cutoff_from,$cutoff_to){
    	$query = $this->db->query("SELECT DATE_FORMAT(timein, '%m/%d/%Y') AS timein_date, DATE_FORMAT(timeout, '%m/%d/%Y') AS timeout_date,DATE_FORMAT(timein, '%H:%i:%s') AS timein, DATE_FORMAT(timeout, '%H:%i:%s') AS timeout,username FROM timesheet_student WHERE userid = '$studentid' AND DATE_FORMAT(timein, '%m/%d/%Y') BETWEEN '$cutoff_from' AND '$cutoff_to' ")->result_array();
    	return $query;
    }

    function getAimsStudentList($where_clause){
    	return $this->db->query("SELECT a.StudNo AS studentid, a.StudCardNo AS studentcode, a.LName AS lname, a.FName AS fname, a.MName AS mname, a.NName AS cname, b.SY AS sy, b.Sem AS sem, a.YearLevel AS yearlevel, a.SectCode AS section, a.CourseCode AS coursecode, a.Department AS Department, a.Gender AS gender, e.code AS depttype
								FROM
									ICADasma.tblPersonalData a 
									INNER JOIN ICADasma.tblStatusHistory b ON a.`StudNo` = b.`StudNo` 
									INNER JOIN ICADasma.tblStudClassList c ON b.`StudNo` = c.`StudNo`
									INNER JOIN ICADasma.tblCourses d ON d.CourseCode = c.`CourseCode`
									INNER JOIN ICADasma._depttypes e ON e.code = d.`HSOrCollege` WHERE a.StudNo != '' $where_clause ")->result_array();
    }

    function insertAimsStudentToHyperion($data){
    	return $this->db->insert("student", $data);
    }
}

