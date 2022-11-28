<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Announcements extends CI_Model {

	/**
	* Query to save new announcement.
	*
	* @return query result
	*/
	function saveAnnouncement($arr_deptids="", $datefrom="", $dateto="", $tfrom="", $tto="", $event="", $venue="", $posted_until="", $user=""){
		$res="";
		if($arr_deptids){
				$res   = $this->db->query("
		   					INSERT INTO announcement (datefrom,dateto,timefrom,timeto,`event`,venue,posted_until,`user`,date_created)
								VALUES ('$datefrom','$dateto','$tfrom','$tto','$event','$venue','$posted_until','$user',CURRENT_DATE)
		   					");
				if($res){
					$id = $this->db->insert_id();
					if($id){
						foreach ($arr_deptids as $deptid) {
							$res = $this->db->query("
								INSERT INTO announcement_dept (base_id, deptid) VALUES ('$id', '$deptid')
							");
						}
					}
				}
			
		}
		return $res;
	}
	function editAnnouncement($id)
	{
		$query = $this->db->query("DELETE FROM announcement WHERE id='$id'");
		return $query;
	}
	function delAnnouncement($id)
	{
		$query = $this->db->query("DELETE FROM announcement WHERE id='{$id}'");
		$query = $this->db->query("DELETE FROM announcement_dept WHERE id='{$id}'");
		return $query; 		
	}

	/**
	* Get List of announcements per username of creator.
	* 
	* @param string $user (Default: "")
	*
	* @return query result
	*/
	function getHistory($user=""){
        $wC = "";
        if($user)   $wC = " WHERE `user`='$user'";
        $query = $this->db->query("
        				SELECT a.id,GROUP_CONCAT(b.deptid) AS deptids,datefrom,dateto,timefrom,timeto,`event`,venue,posted_until,`user`,date_created
        				FROM announcement a
        				LEFT JOIN announcement_dept b ON a.id=b.base_id
        				$wC
        				GROUP BY datefrom,dateto,timefrom,timeto,`event`,venue,posted_until,`user`,date_created
        			")->result();
        return $query;
    }

    function getDeptHistory($code=''){
        $returns = array();
        $this->db->select("deptid, id");
        $this->db->where("base_id",$code);
        $this->db->order_by("deptid","asc");
        $q = $this->db->get("announcement_dept"); 
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          $returns[$row->id] = $row->deptid;
        }
        return $returns;
    }

    /**
	* Get list of active announcements for the department of logged in user/employee.
	*
	* @param string $deptid (Default: "")
	* @param string $userid (Default: "")
	* 
	* @return query result
	*/
	function getAnnouncements($deptid="", $userid="",$months="", $year=""){
		if(!$userid) $userid = $this->session->userdata('username');
		$months = ($months == "") ? date("m") : $months;
		$year = ($year == "") ? date("Y") : $year;
		$dept_res = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$userid'");
		$deptid = $dept_res->num_rows() > 0 ? $dept_res->row(0)->deptid : "";

		$wC = "";
        if($deptid) $wC .= "WHERE b.deptid='$deptid'";
        if ($months) { $wC .= "AND (SUBSTR(a.datefrom,1,7) = '$year-$months' OR SUBSTR(a.dateto,1,7) = '$year-$months')";  }
        $query = $this->db->query("
        				SELECT a.id AS base_id,b.id,b.deptid,datefrom,dateto,timefrom,timeto,`event`,venue,posted_until,`user`,date_created
        				FROM announcement a
        				LEFT JOIN announcement_dept b ON a.id=b.base_id
        				 $wC
        				ORDER BY datefrom 
        		")->result();
        return $query;
    }  

    /**
	* Get List of all department id's.
	* 
	* @return array
	*/
    function getAllDepartmentIDs(){
    	$arr_deptids = array();
    	$res = $this->db->query("SELECT code FROM code_department");
    	if($res->num_rows() > 0){
    		foreach ($res->result() as $obj) {
    			array_push($arr_deptids, $obj->code);
    		}
    	}
    	return $arr_deptids;
    }

    public function getTodayAnnouncement(){
        $query = $this->db->query("
                        SELECT a.id,a.event,a.`venue`,a.timeto,a.`timefrom`, b.deptid, c.description, COUNT(*) AS `total`
                        FROM announcement a
                        LEFT JOIN announcement_dept b ON a.id=b.base_id
                        INNER JOIN code_department c ON b.`deptid` = c.`code`
                        WHERE DATE(NOW()) BETWEEN a.datefrom AND a.dateto
                        GROUP BY a.id
                        ORDER BY datefrom 
                ");
        if($query->num_rows() > 0) return $query->result_array();
        else return false;
    }

    public function getAnnoucementDetail($id = ""){
        $query = $this->db->query("
                        SELECT a.id,c.description
                        FROM announcement a
                        LEFT JOIN announcement_dept b ON a.id=b.base_id
                        INNER JOIN code_department c ON b.`deptid` = c.`code`
                        WHERE a.id = '$id'
                        ORDER BY datefrom 
                
                ");
        if($query->num_rows() > 0) return $query->result_array();
        else return false;
    }

    public function getTodayAnnouncementHoliday(){
        $query = $this->db->query("SELECT a.`id`,a.totime,a.fromtime,date_to,hdescription,c.description,b.campus, b.teaching_type, a.holiday_id FROM code_holiday_calendar a
                                    INNER JOIN code_holidays b ON a.holiday_id = b.holiday_id
                                    INNER JOIN code_holiday_type c ON b.holiday_type = c.holiday_type
                                    WHERE DATE(NOW()) BETWEEN a.date_from AND a.`date_to`ORDER BY date_from
                ");
        if($query->num_rows() > 0) return $query->result_array();
        else return false;
    }

    public function getAnnouncementMonth(){
    	$months = date("m");
		$year = date("Y");
    	$query = $this->db->query("
        				SELECT a.*, b.deptid, c.description
        				FROM announcement a
        				LEFT JOIN announcement_dept b ON a.id=b.base_id
        				INNER JOIN code_department c ON b.`deptid` = c.`code`
        				WHERE (SUBSTR(a.datefrom,1,7) = '$year-$months' OR SUBSTR(a.dateto,1,7) = '$year-$months')
        				ORDER BY datefrom 
        		");
        if($query->num_rows() > 0) return $query->result_array();
    	else return false;
    }

    public function getAnnouncementsInfo($id){
    	return $this->db->query("SELECT * FROM announcement a INNER JOIN announcement_dept b ON a.id = b.base_id WHERE a.id='$id'");
    }

} //endoffile