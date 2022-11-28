<?php 
/**
 * @author Max Consul
 * @copyright 2018
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Model {

	/**
	* Query to for other setup data
	*
	* @return query resultas
	*/

	public function userSetup(){
		$query = $this->db->query("SELECT id,username,CONCAT(lastname,', ',firstname,' ',middlename) AS fullname, type, locked FROM user_info WHERE TYPE != 'SUPER ADMIN'" );
        if($query->num_rows() > 0) return $query->result_array();
        else return false;
	}

	public function SCSetup(){
		return $this->db->query("SELECT schedid,schedcode,description,tardy_start FROM code_schedule")->result_array();
	}

	public function campusSetup(){
		return $this->db->query("SELECT * FROM code_campus")->result_array();
	}

    public function emailSetup(){
        return $this->db->query("SELECT * FROM email ORDER BY date_created DESC")->result_array();
    }

    public function divisionSetup(){
        return $this->db->query("SELECT * FROM code_managementlevel")->result_array();
    }

    public function facialSetup(){
        return $this->db->query("SELECT * FROM facial_devices")->result_array();
    }

    public function facialHistoryToday($serial){
        return $this->db->query("SELECT * FROM facial_Log a INNER JOIN facial_person b ON a.`deviceKey` = b.`serial_number` WHERE DATE(DATE) = CURDATE() AND a.`deviceKey` = '$serial'")->result_array();
    }

    public function facialHistory($serial){
        return $this->db->query("SELECT * FROM facial_Log a INNER JOIN facial_person b ON a.`deviceKey` = b.`serial_number` WHERE a.`deviceKey` = '$serial'")->result_array();
    }

	public function clusterSetup(){
		return $this->db->query("SELECT * FROM code_request_type")->result_array();
	}
	public function requestSetup(){
		return $this->db->query("SELECT * FROM code_request_type")->result_array();
	}

	public function initRequirementsSetup(){
		return $this->db->query("SELECT * FROM required_documents WHERE type = 'init' ")->result_array();
	}

    public function multipleRequirements($req_id, $applicantid, $type){
        return $this->db->query("SELECT * FROM duplicate_requirements WHERE req_id = '$req_id' AND applicant_id = '$applicantid' AND ini_or_pre = '$type' ")->result_array();
    }

    public function saveMultipleRequirement($req_id, $applicantid, $type){
        return $this->db->query("INSERT INTO duplicate_requirements(req_id, applicant_id, ini_or_pre) VALUES ('$req_id', '$applicantid', '$type')");
    }

	public function preRequirementsSetup(){
		return $this->db->query("SELECT * FROM required_documents WHERE type = 'pre' ")->result_array();
	}

	public function getActiveEmployees($where_clause = ""){
		return $this->db->query("SELECT CONCAT(lname, ', ', fname, ', ', mname ) AS fullname, employeeid FROM employee WHERE (dateresigned='1970-01-01' OR dateresigned='0000-00-00' OR dateresigned IS NULL) AND isactive != 0 $where_clause ")->result_array();
	}

	public function departmentSetup(){
		return $this->db->query("SELECT * FROM code_department")->result_array();
	}

    public function loadHRSetupDetails($code_details){
        $tableName = "code_".$code_details;
        return $this->db->query("SELECT * FROM $tableName")->result_array();
    }

    public function loadEmploymentStatusDetails(){
        return $this->db->query("SELECT * FROM code_status")->result_array();
    }

	public function getSetupData($code){
    	$data = $this->db->query("SELECT * FROM code_campus WHERE code = '$code' ");
    	if($data->num_rows > 0) return $data->result_array();
    	else return FALSE;
    }

    public function getDataFacial($code){
        $data = $this->db->query("SELECT * FROM facial_devices WHERE serial_number = '$code' ");
        if($data->num_rows > 0) return $data->result_array();
        else return FALSE;
    }

    public function getSetupDatarequest($request_code){
    	$data = $this->db->query("SELECT * FROM code_request_type WHERE request_code = '$request_code' ");
    	if($data->num_rows > 0) return $data->result_array();
    	else return FALSE;
    }

    public function saveRequest($data, $action){
    	if($action == "add"){
	    	$validate_code = $this->db->query("SELECT * FROM code_request_type WHERE request_code = '{$data['request_code']}' ");
	    	if($validate_code->num_rows > 0) return FALSE;
	    	else $this->db->insert("code_request_type", $data);
	    	return TRUE;
	    }else if($action == "delete"){
	    	$validate_delete = $this->db->query("SELECT * FROM employee_schedule_adjustment WHERE remarks = '$data' ")->num_rows();
            if($validate_delete > 0){
                return array("err_message" => 1, "Count" => $validate_delete);
            }else{
                $this->db->query("DELETE FROM code_request_type WHERE request_code = '$data' ");
                return array("err_message" => 0, "Count" => 0);
            } 
	    }else{
	    	$validate_code = $this->db->query("SELECT * FROM code_request_type WHERE request_code = '{$data['request_code']}' ");
	    	$update_query = $this->db->query("UPDATE code_request_type SET request_code = '{$data['request_code']}', description = '{$data['description']}' WHERE request_code = '{$data['request_code']}' ");
	    	return $action;
	    }
    }

    public function saveCampus($data, $action){
    	if($action == "add"){
	    	$validate_code = $this->db->query("SELECT * FROM code_campus WHERE code = '{$data['code']}' ");
	    	if($validate_code->num_rows > 0) return FALSE;
	    	else $this->db->insert("code_campus", $data);
	    	return TRUE;
	    }else if($action == "delete"){
	    	$validate_delete = $this->db->query("SELECT * FROM employee WHERE code_campus = '$data' ");
	    	if($validate_delete->num_rows() > 0) return false;
	    	$this->db->query("DELETE FROM code_campus WHERE code = '$data' ");
	    	return true;
	    }else{
	    	$validate_code = $this->db->query("SELECT * FROM code_campus WHERE code = '{$data['code']}' ");
	    	$update_query = $this->db->query("UPDATE code_campus SET description = '{$data['description']}',  campus_principal = '{$data['campus_principal']}' WHERE code = '{$data['code']}' ");
	    	return $action;
	    }
    }

    public function getInitialRequirementsSetupData($code){
    	$data = $this->db->query("SELECT * FROM required_documents WHERE code = '$code' AND type = 'init' ");
    	if($data->num_rows > 0) return $data->result_array();
    	else return FALSE;
    }

    public function getPreRequirementsSetupData($code){
    	$data = $this->db->query("SELECT * FROM required_documents WHERE code = '$code' AND type = 'pre' ");
    	if($data->num_rows > 0) return $data->result_array();
    	else return FALSE;
    }

    public function getRequiredDocumentMaxId(){
        $return = $this->db->query("SELECT MAX(id) as maxid from required_documents")->row()->maxid;
        return $return+1;
    }

    public function saveRequiredRequirements($data, $action){
    	if($action == "add"){
	    	$validate_code = $this->db->query("SELECT * FROM required_documents WHERE code = '{$data['code']}' ");
	    	if($validate_code->num_rows > 0) return FALSE;
	    	else $this->db->insert("required_documents", $data);
	    	return TRUE;
	    }else if($action == "delete"){
	    	// $validate_delete = $this->db->query("SELECT * FROM employee WHERE required_documents = '$data' ");
	    	// if($validate_delete->num_rows() > 0) return false;
	    	$this->db->query("DELETE FROM required_documents WHERE code = '$data' ");
	    	return true;
	    }else{
	    	$validate_code = $this->db->query("SELECT * FROM required_documents WHERE code = '{$data['code']}' ");
	    	$update_query = $this->db->query("UPDATE required_documents SET description = '{$data['description']}' WHERE code = '{$data['code']}' ");
	    	return $action;
	    }
    }

	public function getSetupDataDepartment($code){
    	$data = $this->db->query("SELECT * FROM code_department WHERE code = '$code' ");
    	if($data->num_rows > 0) return $data->result_array();
    	else return FALSE;
    }

    public function getHRSetupData($code, $tbl, $primarykey=''){
        if($primarykey) $data = $this->db->query("SELECT * FROM $tbl WHERE $primarykey = '$code' ");
        else $data = $this->db->query("SELECT * FROM $tbl WHERE code = '$code' ");
        if($data->num_rows > 0) return $data->result_array();
        else return FALSE;
    }

    public function saveDepartment2($data, $action){
    	if($action == "add"){
	    	$validate_code = $this->db->query("SELECT * FROM code_department WHERE code = '{$data['code']}' ");
	    	if($validate_code->num_rows > 0) return "duplicate";
	    	else $this->db->insert("code_department", $data);
	    	return "add";
	    }else if($action == "delete"){
	    	$this->db->query("DELETE FROM code_department WHERE code = '$data' ");
	    	return true;
	    }else{
	    	$validate_code = $this->db->query("SELECT * FROM code_department WHERE code = '{$data['code']}' ");
	    	$update_query = $this->db->query("UPDATE code_department SET description = '{$data['description']}', head = '{$data['head']}' WHERE code = '{$data['code']}' ");
	    	return $action;
	    }
    }

    public function saveHRSetup($code, $description='', $action, $tbl, $primarykey=''){
        if($action == "add"){
            if($primarykey){
                if($primarykey != "nationalityid" && $primarykey != "religionid" && $primarykey != "citizenid" && $primarykey != "schoolid"){
                    $code = ucfirst($description[0]);
                    $validate_code = $this->db->query("SELECT * FROM $tbl WHERE $primarykey = '$code' ");
                    if($validate_code->num_rows > 0) return FALSE;
                    else $this->db->query("INSERT INTO $tbl($primarykey, description)values('$code','$description')");
                    return TRUE;
                }else{
                    $this->db->query("INSERT INTO $tbl($primarykey, description)values(null,'$description')");
                    return TRUE;
                }
                
            }else{
                $validate_code = $this->db->query("SELECT * FROM $tbl WHERE code = '$code' ");
                if($validate_code->num_rows > 0) return FALSE;
                else $this->db->query("INSERT INTO $tbl(code, description)values(null,'$description')");
                return TRUE;
            }
        }else if($action == "delete"){
            if($primarykey){
                $this->db->query("DELETE FROM $tbl WHERE $primarykey = '$code' ");
                return true;
            }else{
                $this->db->query("DELETE FROM $tbl WHERE code = '$code' ");
                return true; 
            }
        }else{
            if($primarykey){
                $validate_code = $this->db->query("SELECT * FROM $tbl WHERE $primarykey = '$code' ");
                $update_query = $this->db->query("UPDATE $tbl SET description = '$description' WHERE $primarykey = '$code' ");
                return $action;
            }else{
                $validate_code = $this->db->query("SELECT * FROM $tbl WHERE code = '$code' ");
                $update_query = $this->db->query("UPDATE $tbl SET description = '$description' WHERE code = '$code' ");
                return $action;
            }   
        }
    }

    public function deductionList($employeeid){
    	$q_deduc = $this->db->query("SELECT a.employeeid,a.code_deduction,a.memberid,a.amount,a.nocutoff,a.datefrom,a.dateto,a.schedule,a.cutoff_period FROM employee_deduction a WHERE a.employeeid='$employeeid' AND visibility='SHOW'");
    	if($q_deduc->num_rows > 0) return $q_deduc->result();
    	else return false;
    }

    public function saveOffice($code,$description,$division,$head,$divhead,$job,$isBED, $department_id, $last_dept){
       if($job=="delete"){
         $res = $this->db->query("delete from code_office WHERE code='{$code}';");
       }else if($job=="add"){
        $validate_code = $this->db->query("SELECT * FROM code_office WHERE code = '$code' ");
        if($validate_code->num_rows > 0) $job = "duplicate";
        else $this->db->query("INSERT INTO code_office (code,description,managementid,head,divisionhead,isBED,department_id) VALUES ('$code','$description','$division','$head','$divhead','$isBED', '$department_id')");
       }else{
        $res = $this->db->query("UPDATE code_office SET code = '{$code}',description = '{$description}',managementid = '{$division}',head = '{$head}',divisionhead = '{$divhead}',isBED = '{$isBED}', department_id='{$department_id}' WHERE code = '{$code}'");
        $this->db->query("UPDATE employee SET deptid = '{$department_id}' WHERE deptid = '{$last_dept}' AND office = '$code'");
       }
       return $job;
    }

    public function getOffice(){
		return $this->db->query("SELECT * FROM code_office")->result_array();
	}

    public function manageOffice($code){
		return $this->db->query("SELECT * FROM code_office WHERE code='{$code}'")->result_array();
	}

    public function getDepartmentDesc($code=''){
        $query = $this->db->query("SELECT * FROM code_department WHERE code='{$code}'");
        if($query->num_rows() > 0) return Globals::_e($query->row()->description);
    }

    public function getOfficesbyDept($dept=''){
        $wc = '';
        if($dept) $wc.= "AND department_id='{$dept}'";
        $option = "<option value='all'>All Office</option>";
        $query = $this->db->query("SELECT * FROM code_office WHERE 1 $wc ORDER BY description ASC")->result_array();
        foreach ($query as $row) {
            if($row["description"]) $option .= "<option value='".$row['code']."'>".$row['description']."</option>";
        }
        return $option;

    }

    public function getOfficesbyDeptMultiple($dept=''){
        $wc = '';
        if($dept && $dept != 'all' && $dept != 'null') $wc.= "AND FIND_IN_SET(department_id, '$dept')";
        $option = "<option value='all'>All Office</option>";
        $query = $this->db->query("SELECT * FROM code_office WHERE 1 $wc ORDER BY description ASC")->result_array();
        foreach ($query as $row) {
            if($row["description"]) $option .= "<option value='".$row['code']."'>".$row['description']."</option>";
        }
        return $option;

    }

    public function getPayrollType(){
    	return $this->db->query("SELECT * FROM rank_code_type");
    }

    public function getPayrollRank(){
    	return $this->db->query("SELECT * FROM rank_code");
    }

    public function getPayrollSet(){
    	return $this->db->query("SELECT * FROM rank_code_set");
    }    

    public function savePayrollRankSetup($description, $table, $code){
    	if($code) $res = $this->db->query("UPDATE $table SET description = '$description' WHERE id = '$code' ");
    	else $res = $this->db->query("INSERT INTO $table (description) VALUES ('$description')");
    	return $res;
    }

    public function getPayrollRankData($id, $table){
    	$data = $this->db->query("SELECT * FROM $table WHERE id = '$id' ");
    	if($data->num_rows > 0){
    		return array($data->row()->id, $data->row()->description);
    	}
    }

    public function deletePayrollRankData($id, $table){
        $data = $this->db->query("DELETE FROM $table WHERE id = '$id'");
        return "Type has been deleted successfully";
    }

    public function manageRankSetup(){
    	return $this->db->query("SELECT a.*, b.`description` AS type_desc, c.`description` AS rank_desc, d.`description` AS set_desc FROM manage_rank a INNER JOIN rank_code_type b ON a.`type` = b.`id` INNER JOIN rank_code c ON a.`rank` = c.`id` INNER JOIN rank_code_set d ON a.`set` = d.`id` ")->result_array();
    }

    public function getSetupDataManageRank($id){
    	$data = $this->db->query("SELECT * FROM manage_rank WHERE id = '$id' ");
    	if($data->num_rows > 0) return $data->result_array();
    	else return FALSE;
    }

    public function saveManageRank($type, $rank, $set, $basic_rate){
    	$res = $this->db->query("INSERT INTO manage_rank (type, rank, `set`, basic_rate) VALUES ('$type','$rank','$set','$basic_rate') ");
    	return $res;
    }

    public function updateManageRank($id, $type, $rank, $set, $basic_rate){
    	$res = $this->db->query("UPDATE manage_rank SET `type` = '$type', rank = '$rank', `set` = '$set', basic_rate = '$basic_rate' WHERE id = '$id' ");
    	return $res;
    }

    public function deleteManageRank($id){
    	$res = $this->db->query("DELETE FROM manage_rank WHERE id = '$id' ");
    	return $res;
    }

    public function getPayrollTypeArray(){
    	return $this->db->query("SELECT * FROM rank_code_type")->result_array();
    }

    public function getPayrollRankArray(){
    	return $this->db->query("SELECT * FROM rank_code")->result_array();
    }

    public function getPayrollSetArray(){
    	return $this->db->query("SELECT * FROM rank_code_set")->result_array();
    } 

    public function getRankByType($id){
    	$q_rank = $this->db->query("SELECT a.*, b.`description` FROM manage_rank a INNER JOIN rank_code b ON a.`rank` = b.`id` WHERE type = '$id' ");
    	if($q_rank->num_rows > 0) return $q_rank->result_array();
    	else return false;
    }

    public function getRankBasicRate($id){
    	$q_basicrate = $this->db->query("SELECT * FROM manage_rank WHERE id = '$id' ");
    	if($q_basicrate->num_rows > 0) return $q_basicrate->row()->basic_rate;
    	else return false;
    }

    public function checkBio($id="", $finger=""){
        $isExist = false;
        $q_bio = $this->db->query("SELECT * FROM bio WHERE userID = '$id' AND finger = '$finger'")->result();
        foreach ($q_bio as $row) $isExist = true;

        return $isExist;
    }

    public function saveUpdateBio($data, $userID, $finger){
        $this->db->where("userID = '$userID' AND finger = '$finger'");
        $q_save_civil_status = $this->db->update('bio', $data);
        $rfid = $data['rfid'];
        $employeeid = $data['userID'];
        $this->db->query("UPDATE employee SET `employeecode` = '$rfid' WHERE employeeid = '$employeeid'");
        return $q_save_civil_status;
    }

    public function saveInsertBio($data){
        $q_save_civil_status = $this->db->insert("bio", $data);
        $rfid = $data['rfid'];
        $employeeid = $data['userID'];
        $this->db->query("UPDATE employee SET `employeecode` = '$rfid' WHERE employeeid = '$employeeid'");
        return $q_save_civil_status;
    }

    public function saveEmail($data){
        return $this->db->insert("email", $data);
    }

   public function checkIfGateIsActive($username){
        $q_gate = $this->db->query("SELECT * FROM user_gate_history WHERE username = '$username' AND (login != '0000-00-00 00:00:00' OR login != NULL OR login != '') AND (logout = '0000-00-00 00:00:00' OR logout = NULL OR logout = '')");
        return $q_gate->num_rows;
    }  
    
    public function getAvailableCourses(){
        $q_course = $this->db->query("SELECT DISTINCT course FROM employee_schedule_history WHERE course IS NOT NULL AND course != 'null'");
        if($q_course->num_rows() > 0) return $q_course->result_array();
        else return false;
    }

    public function getAvailableSection($where_clause){
        $q_section = $this->db->query("SELECT DISTINCT section FROM employee_schedule_history $where_clause AND section IS NOT NULL AND section != 'null' ");
        if($q_section->num_rows() > 0) return $q_section->result_array();
        else return false;
    }

    public function getAvailableSubject($aimsdept){
        $where_clause = "";
        if($aimsdept) $where_clause = " AND aimsdept LIKE '%$aimsdept%' ";
        $q_section = $this->db->query("SELECT DISTINCT subject FROM employee_schedule_history  WHERE subject IS NOT NULL AND subject != 'null' $where_clause ");
        if($q_section->num_rows() > 0) return $q_section->result_array();
        else return false;
    }

    public function generateCourseDropdown($course=""){
        $option = "<option value=''>Select an Option</option>";
        $courselist = $this->getAvailableCourses();
        foreach($courselist as $row){
            if($course == $row['course']) $option .= "<option value='".$row['course']."' selected>".$row['course']."</option>";
            else $option .= "<option value='".$row['course']."'>".$row['course']."</option>";
        }

        return $option;
    }

    public function generateSectionDropdown($course="", $section=""){
        $option = "";
        $where_clause = " WHERE course LIKE '%$course%' ";
        $sectionlist = $this->getAvailableSection($where_clause);
        if($course){
            foreach($sectionlist as $row){
                if($section == $row['section']) $option .= "<option value='".$row['section']."' selected>".$row['section']."</option>";
                else $option .= "<option value='".$row['section']."'>".$row['section']."</option>";
            }
        }

        return $option;
    }

    public function generateSubjectDropdown($aimsdept="", $subject=""){
        $option = "";
        $subjectlist = $this->getAvailableSubject($aimsdept);
        if($subjectlist){
            foreach($subjectlist as $row){
                if($subject == $row['subject']) $option .= "<option value='".$row['subject']."' selected>".$row['subject']."</option>";
                else $option .= "<option value='".$row['subject']."'>".$row['subject']."</option>";
            }
        }

        return $option;
    }

    public function saveFacial($data, $action){
        if($action == "add"){
            $validate_code = $this->db->query("SELECT * FROM facial_devices WHERE serial_number = '{$data['serial_number']}' ");
            if($validate_code->num_rows > 0) return FALSE;
            else $this->db->insert("facial_devices", $data);
            return TRUE;
        }else if($action == "delete"){
            $this->db->query("DELETE FROM facial_devices WHERE serial_number = '$data'");
            return true;
        }else{
            $update_query = $this->db->query("UPDATE facial_devices SET name = '{$data['name']}', ip = '{$data['ip']}', serial_number = '{$data['serial_number']}' WHERE serial_number = '{$data['serial_number']}' ");
            return $action;
        }
    }

    public function savePersonToDevice($data, $action){
        if($action == "add"){
            $validate_code = $this->db->query("SELECT * FROM facial_person WHERE serial_number = '{$data['serial_number']}' ");
            if($validate_code->num_rows > 0) return FALSE;
            else $this->db->insert("facial_person", $data);
            return TRUE;
        }else if($action == "delete"){
            $this->db->query("DELETE FROM facial_person WHERE serial_number = '{$data['serial_number']}' AND personId = '{$data['personId']}'");
            return true;
        }else{
            $update_query = $this->db->query("UPDATE facial_person SET personId = '{$data['personId']}', card = '{$data['card']}',  employeeid = '{$data['employeeid']}', serial_number = '{$data['serial_number']}' WHERE serial_number = '{$data['serial_number']}' ");
            return $action;
        }
    }

    public function getFacialInfo($id){
        return $this->db->query("SELECT * FROM facial_devices WHERE serial_number = '$id'")->result_array();
    }

    public function getDeviceLog($id){
        return $this->db->query("SELECT * FROM facial_Log WHERE serial_number = '$id'")->result_array();
    }

    public function getDevicePerson($id){
        return $this->db->query("SELECT * FROM facial_person WHERE `serial_number` = '$id'")->result_array();
    }

    public function resetDevicePerson($serial){
        $this->db->query("DELETE FROM facial_person WHERE serial_number = '$serial'");
        return "Success";
    }

    public function setupBioExcluded($userid, $name, $action){
        if($action == "add"){
            $this->db->query("INSERT INTO bios_excluded (userid,name) VALUES ('$userid','$name')");
            return TRUE;
        }else{
            $this->db->query("DELETE FROM bios_excluded WHERE userid = '$userid'");
            return true;
        }
    }
 
    public function bypassSetup(){
        return $this->db->query("SELECT * FROM bypass_employee")->result_array();
    }

    public function getBypassSetupData($id){
        $data = $this->db->query("SELECT * FROM bypass_employee WHERE id = '$id' ");
        if($data->num_rows > 0) return $data->result_array();
        else return FALSE;
    }

    public function saveBypass($data, $action, $bypassid){
        if($action == "add"){
            return $this->db->insert("bypass_employee", $data); 
        }else if($action == "delete"){
            $validate_delete = $this->db->query("SELECT * FROM employee WHERE bypass_employee = '$data' ");
            if($validate_delete->num_rows() > 0) return false;
            $this->db->query("DELETE FROM bypass_employee WHERE id = '$data' ");
            return true;
        }else{
            $update_query = $this->db->query("UPDATE bypass_employee SET code = '{$data['code']}',  employee = '{$data['employee']}' WHERE id = '$bypassid' ");
            return $action;
        }
    }

    public function deleteWS($tblid){
        return $this->db->query("DELETE FROM inhouse_seminar WHERE id = '$tblid'");
    }

    public function courseList(){
        $q_course = $this->db->query("SELECT * FROM tblCourses");
        if($q_course->num_rows() > 0) return $q_course->result_array();
        else return false;
    }
    
    public function subjectList(){
        $q_subject = $this->db->query("SELECT * FROM code_subj_competent_to_teach WHERE status <> 0");
        if($q_subject->num_rows() > 0) return $q_subject->result_array();
        else return false;
    }

    public function workshopSelection(){
        $q_workshop = $this->db->query("SELECT * FROM applicant_workshops");
        if($q_workshop->num_rows() > 0) return $q_workshop->result_array();
        else return false;
    }

    public function loadPhilhealthShareSetup(){
        return $this->db->query("SELECT * FROM philhealth_empshare")->result_array();
    }

    public function managePhilhealthShare($code){
        return $this->db->query("SELECT * FROM philhealth_empshare WHERE id='{$code}'")->result_array();
    }

    public function userCodeProgress($emptype){
        $query = $this->db->query("SELECT CONCAT('[',current_count,'/',total_count,']') as progress, current_count, total_count FROM code_type_progress WHERE emptype='$emptype'");
        if($query->num_rows() > 0){
            $current_count =  $query->row(0)->current_count;
            $total_count =  $query->row(0)->total_count;
            if($current_count >= $total_count){
                $this->db->query("DELETE FROM code_type_progress WHERE emptype='{$emptype}'");
                return 0;
            }else{
                return $query->row(0)->progress;
            }
        }
        else{
            return 0;
        }
    }

    public function onlineApplicationList($type=""){
        $wc = " AND is_leave = '1'";
        if($type == "other") $wc = " AND is_leave = '0'";
        return $this->db->query("SELECT a.* FROM code_request_form b INNER JOIN online_application_code a ON a.id = b.base_id WHERE ismain = '1' $wc GROUP BY a.id");
    }

    public function onlineApplicationBaseList($base_id){
        return $this->db->query("SELECT * FROM code_request_form WHERE base_id = '$base_id' ORDER BY sort, description ");
    }

}


