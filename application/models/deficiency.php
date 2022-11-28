<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deficiency extends CI_Model {

	function getDeficiencyTypes($id="",$type="", $deptid=''){
		$wC = "";
		if($id)	  $wC .= " AND id='$id'";
		if($type) $wC .= " AND type='$type'";
		if($deptid) $wC .= " AND deptid = '$deptid'";
		$result = $this->db->query("SELECT id,type, description FROM code_deficiency WHERE status <> 0 $wC")->result();
		return $result;
	}

	function getDeficiencyInfo($id="",$type=""){
		$result = $this->db->query("SELECT id,type, description, deptid FROM code_deficiency WHERE id='$id'")->result();
		return $result;
	}

	function isUniqueDeficiency($id="", $type="", $desc="", $description=""){
		$query = $this->db->query("SELECT * from code_deficiency where type = '$type'")->num_rows();
		return $query;
	}

	function insertDeficiency($type="", $desc="", $deptid=""){
		$res = $this->db->query("INSERT INTO code_deficiency (type,description,deptid) VALUES ('$type','$desc', '$deptid')");
		return $res;
	}
	function updateDeficiency($id="", $type="", $desc="", $deptid){
		$res = $this->db->query("UPDATE code_deficiency SET `type`='$type', description='$desc', deptid='$deptid' WHERE `id`='$id'");
		// $res = $this->db->update_string("code_deficiency",array('type'=>$type, 'description'=>$desc), "`id`='$id'");
		return $res;
	}

	function deficiencySetup($deptid=''){
		$wc="";
		if($deptid) $wc .= " AND a.deptid = '$deptid'";
        return $this->db->query("SELECT a.*, a.description as defdesc, b.description as deptdesc FROM code_deficiency a LEFT JOIN code_office b ON a.deptid = b.code WHERE 1 $wc")->result_array();
    }

    function deptDeficiency(){
    	return $this->db->query("SELECT * FROM department_deficiency a INNER JOIN code_office b ON a.deptid = b.code")->result_array();
    }

    function deleteDeptDeficiency($id='', $did=''){
    	$this->db->query("DELETE FROM department_deficiency WHERE deptid NOT IN (SELECT deptid FROM code_deficiency WHERE deptid = '$did' AND status = 1) AND id = '$id'");
    	$inUse = $this->db->query("SELECT * FROM code_deficiency WHERE deptid = '$did' AND status = 1")->num_rows();
    	if($inUse > 0) return false;
    	else return true;
    }

    function loadDefDepartments($id=''){
    	return $this->db->query("SELECT * FROM code_office WHERE code NOT IN (SELECT deptid FROM department_deficiency WHERE deptid != '$id')")->result_array();
    }

    function saveDeficiencyDept($depitid, $id=''){
    	if($id){
    		$query = $this->db->query("UPDATE department_deficiency set deptid = '$depitid' where id ='$id'");
    	}else{
    		$query = $this->db->query("INSERT INTO department_deficiency(deptid)VALUES('$depitid')");
    	}
    	return $query;
    }

	function removeDeficiency($id=""){
		$res = $this->db->query("UPDATE code_deficiency SET `status`='0' WHERE `id`='$id'");
		return $res;
	}

	function getEmpDefDetails($id=''){
		$res = $this->db->query("SELECT * FROM employee_deficiency WHERE id='$id'");
		return $res;
	}

	function getDeficiencyHistory($employeeid="",$isCompleted=""){
		$userid = $this->session->userdata("username");
		$usertype = $this->session->userdata("usertype");
        $wC = "";
        if($employeeid)   $wC .= " AND `employeeid`='$employeeid'";
        if($isCompleted != "") $wC .= " AND isread = '$isCompleted'";

		// if($usertype != "ADMIN" && $userid != $employeeid) $wC.= " AND user = '$userid'";
        $query = $this->db->query("
        				SELECT a.id AS empdef_id ,b.`description` AS defdesc,c.`description` AS deptdesc, a.*,b.*,c.* FROM employee_deficiency a INNER JOIN code_deficiency b on a.def_id=b.id LEFT JOIN code_office c ON c.`code`=a.`concerned_dept` WHERE 1 $wC");
        
        return $query;
    }

    function saveEmpDeficiency($id="", $employeeid="",$deptid="", $lookfor="", $def_id="", $remarks="",$datesub="", $isCompleted="",$datecompleted="", $sy=""){
    	$res = "";
    	$user = $this->session->userdata('username');
    	if($id){
    		//update employee def
			$res = $this->db->query("UPDATE employee_deficiency 
					SET `concerned_dept`='$deptid', 
						`lookfor`='$lookfor', 
						`def_id`='$def_id', 
						`employeeid`='$employeeid', 
						`remarks`='$remarks', 
						`submission_date`='$datesub', 
						`is_completed`='$isCompleted', 
						`date_completed`='$datecompleted', 
						`user`='$user',
						`sy`='$sy'
					WHERE `id`='$id'");
    	}else{
    		//add new employee def
			$res = $this->db->query("INSERT INTO employee_deficiency 
						(concerned_dept,lookfor,def_id,employeeid,remarks,submission_date,is_completed,date_completed,user, sy) 
						VALUES ('$deptid','$lookfor','$def_id','$employeeid','$remarks','$datesub','$isCompleted','$datecompleted','$user', '$sy')
						");
    	}
		return $res;
	}

	/**
	 * Formats list of deficiencies for employee notification
	 *
	 * @param stdClass Object $d_list
	 *
	 * @return string
	 */
	function formatDefListForNotif($d_list){
		$this->load->model('utils');
		$d_list_f = "";
		$d_list_f .= "<div class='container' style='width:100%;padding:5%;'><table class='table table-striped' style='padding:5%;'>";
		if($d_list->num_rows() > 0){
			foreach ($d_list->result() as $key => $row) {
				$lookfor = $this->utils->getFullName($row->lookfor);
				$d_list_f .= "<tr> <td><span class='def-label' style='color:black;'>Office: </span><b>{$row->description}</b><br><span class='def-label' style='color:black;'>Remarks: </span><b>{$row->remarks}</b></td> <td><span class='def-label' style='color:black;'>Look For: </span><b>$lookfor</b></td></tr>";
			}
		}
		$d_list_f .= "</table></div>";
		return $d_list_f;
	}

	function deleteEmpDef($id=""){
		$res = $this->db->query("DELETE FROM employee_deficiency WHERE id='$id'");
		return $res;
	}

	function saveCompletionOfClearance($tblid, $datecompleted, $remarks){
    	$user = $this->session->userdata('username');
    	$table_id = explode('~~', $tblid);
    	foreach ($table_id as $id) {
    		$this->db->query("UPDATE employee_deficiency 
				SET `is_completed`='1', 
					`date_completed`='$datecompleted', 
					`remarks`=".$this->db->escape($remarks)."
				WHERE `id`='$id'");
    	}
    }

     function countIncompleteClearance($lookfor){
    	return $this->db->query("SELECT * FROM employee_deficiency a INNER JOIN code_deficiency b ON a.def_id=b.id WHERE a.lookfor = '$lookfor' AND a.is_completed = '0'");
    }



} //endoffile
