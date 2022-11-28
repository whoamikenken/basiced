<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Webcheckin extends CI_Model {

    public function getHistoryData($status='', $type='', $from='', $to='', $employee='',$deptid='', $office=''){
        $datenow = date("Y-m-d");
        $where_clause = "";
        if($type)     $where_clause .= "AND b.teachingtype = '$type' ";
        if(isset($status) && $status != "")            $where_clause .= "AND b.isactive = '$status' ";
        if($from && $to)        $where_clause .= "AND DATE(a.localtimein) BETWEEN '$from' AND '$to'";
        if($employee)        $where_clause .= "AND b.employeeid = '$employee'";
        if($deptid)        $where_clause .= "AND b.deptid = '$deptid'";
        if($office)        $where_clause .= "AND b.office = '$office'";
        $where_clause .= " AND (('$datenow' < b.dateresigned2 OR b.dateresigned2 = '0000-00-00' OR b.dateresigned2 = '1970-01-01' OR b.dateresigned2 IS NULL) AND b.isactive ='1')";

        $employee_list = $this->db->query("SELECT CONCAT(b.`lname`, ', ', b.`fname` , ', ', b.`mname`) AS fullname, b.`employeeid`, a.`localtimein`, a.`log_type`, b.`teachingtype`, a.`id`  FROM webcheckin_history a INNER JOIN employee b ON b.`employeeid` = a.`userid` WHERE 1 $where_clause ORDER BY a.`localtimein`")->result_array();
    return $employee_list;  
    }

    public function getResponseData($category='', $survey='', $type='', $employee='', $deptid='', $dept=''){
        $where_clause = "";
        $order_clause = "";
        if(strpos($employee, 'all') !== false) {
            $where_clause .= "";
        }else{
            $where_clause .= "AND b.employeeid IN('$employee')";
        }
        if($type)     $where_clause .= "AND b.teachingtype = '$type' ";
        if($category)            $where_clause .= "AND a.category = '$category' ";
        if($survey)            $where_clause .= "AND a.description = '$survey' ";
        if($deptid)        $where_clause .= "AND b.deptid = '$deptid'";
        if ($dept) $order_clause = "a.`date_created`";
        else $order_clause = "a.`category`, b.`lname`";
        $employee_list = $this->db->query("SELECT CONCAT(b.`lname`, ', ', b.`fname` , ', ', b.`mname`) AS fullname, b.`employeeid`, a.`answer`, a.`id`, a.`date_created`, a.`category`, a.`description`, c.`description` AS department, b.`gender`, b.`age` FROM survey_record a LEFT JOIN employee b ON b.`employeeid` = a.`employee` LEFT JOIN code_department c ON b.`deptid` = c.`code` WHERE 1 $where_clause ORDER BY $order_clause DESC")->result_array();
    return $employee_list;  
    }

    public function getAllReponseAnswer(){
        $employee_list = $this->db->query("SELECT employee,answer FROM survey_record")->result_array();
    return $employee_list;  
    }

    public function getWebSetupData($status='', $type='', $from='', $to='', $employee='', $deptid='', $office='',$setupid = "", $empstat=""){
        $datenow = date("Y-m-d");
        $where_clause = "";
        if($type)     $where_clause .= "AND b.teachingtype = '$type' ";
        if($status)            $where_clause .= "AND a.status = '$status' ";
        if($from && $to)        $where_clause .= "AND DATE(a.date_to) BETWEEN '$from' AND '$to'";
        if($employee)        $where_clause .= "AND b.employeeid = '$employee'";
        if($deptid)        $where_clause .= "AND b.deptid = '$deptid'";
        if($office)        $where_clause .= "AND b.office = '$office'";
        if($setupid)        $where_clause .= "AND a.id = '$setupid'";
        if($empstat)        $where_clause .= "AND b.employmentstat = '$empstat'";
        $where_clause .= " AND (('$datenow' < b.dateresigned2 OR b.dateresigned2 = '0000-00-00' OR b.dateresigned2 = '1970-01-01' OR b.dateresigned2 IS NULL) AND b.isactive ='1')";
        $employee_list = $this->db->query("SELECT a.*, CONCAT(b.`lname`, ', ', b.`fname` , ', ', b.`mname`) AS fullname, c.`description` AS officedesc, d.`log_type`, b.`office`, b.`deptid` FROM weblogin_setup a LEFT JOIN employee b ON a.`employee` = b.`employeeid` LEFT JOIN code_office c ON b.`office` = c.`code` LEFT JOIN webcheckin_history d ON a.`employee` = d.`userid` WHERE 1 $where_clause GROUP BY a.`id` ORDER BY a.`id`")->result_array();
        return $employee_list;
    }

    function getEmployeelistWithWSID($employeelist, $from, $to){
        $wc = "";
        if($employeelist && $employeelist != 'all') $wc .= " AND FIND_IN_SET(a.employee, '$employeelist') ";
        if($from && $to) $wc .= "AND DATE(a.date_to) BETWEEN '$from' AND '$to'";
        $employee_list = $this->db->query("SELECT a.id FROM weblogin_setup a LEFT JOIN employee b ON a.`employee` = b.`employeeid` LEFT JOIN code_office c ON b.`office` = c.`code` LEFT JOIN webcheckin_history d ON a.`employee` = d.`userid` WHERE 1 $wc GROUP BY a.`id` ORDER BY a.`id`")->result_array();
        return $employee_list;
    }

    public function getHistoryDetails($id=''){
        $historyData = $this->db->query("SELECT a.*, CONCAT(b.`lname`, ', ', b.`fname` , ', ', b.`mname`) AS fullname FROM webcheckin_history a LEFT JOIN employee b ON a.`userid` = b.`employeeid` WHERE a.`id` = '$id'")->result_array();
        return $historyData;
    }

    public function getHistoryDetailsImage($id=''){
        $historyData = $this->db->query("SELECT image, height, width FROM ".$this->db->database_files.".webcheckin_image WHERE base_id = '$id'")->result_array();
        return $historyData;
    }

    public function getSurveyCatData($id=''){
        $where_clause = "";
        if($id)     $where_clause .= "AND id = '$id' ";
        $getSurveyCatData = $this->db->query("SELECT * FROM survey_category WHERE 1 $where_clause")->result_array();
        return $getSurveyCatData;
    }

    public function getSurveyItemsData($id=''){
        $where_clause = "";
        if($id)     $where_clause .= "AND id = '$id' ";
        $getSurveyCatData = $this->db->query("SELECT * FROM survey_items WHERE 1 $where_clause")->result_array();
        return $getSurveyCatData;
    }

    public function getSurveyResponseData($id=''){
        $where_clause = "";
        if($id)     $where_clause .= "AND id = '$id' ";
        $getSurveyResponseData = $this->db->query("SELECT survey_record.`answer` as questions FROM survey_record WHERE 1 $where_clause")->result_array();
        return $getSurveyResponseData;
    }

    public function saveWebSetup($data){
        return $this->db->insert("weblogin_setup", $data);
    }

    function updateWebSetup($id,$date){
        $upd = "";
        $ServerTime = date('Y-m-d', strtotime($this->getServerTime()));
        if ($date > $ServerTime) {
            $upd = ",`status` = 'active'";
        }
        return $this->db->query("UPDATE weblogin_setup SET `date_to` = '$date'$upd WHERE `id` = '$id'");
    }

    public function saveNoOut($data){
        return $this->db->insert("timesheet_noout", $data);
    }

    public function saveNoOutStudent($data){
        return $this->db->insert("timesheet_noout_student", $data);
    }

    public function saveCheckIn($data){
        $this->db->insert("webcheckin_history", $data);
        $lastid = $this->db->insert_id();
        $this->db->insert("webcheckin_trail", $data);
        return $lastid;
    }

    public function saveCheckInImage($data){
        return $this->db->insert($this->db->database_files.".webcheckin_image", $data);   
    }

    public function saveCheckInToTimesheet($data){
        return $this->db->insert("timesheet", $data);
    }

    public function saveCheckInToTimesheetStudent($data){
        return $this->db->insert("timesheet_student", $data);
    }

    function getLastLog($employeeid){
        $return['log_type'] = "new";
        $ServerTime = date('Y-m-d', strtotime($this->getServerTime()));
        $query = $this->db->query("SELECT log_type, localtimein FROM webcheckin_trail WHERE date(date_created) = '$ServerTime' AND userid = '$employeeid'")->result();
        foreach($query as $row)
        {
            $return['log_type'] = $row->log_type;
            $return['localtimein'] = $row->localtimein;
        }
        return $return;
        
    }

    public function checkStatusOfEmployee(){
        $ServerTime = date('Y-m-d', strtotime($this->getServerTime()));
        return $this->db->query("UPDATE weblogin_setup SET `status` = 'inactive' WHERE date_to < '$ServerTime'");
    }

    function getServerTime(){
        return $this->db->query("SELECT CURRENT_TIMESTAMP")->row()->CURRENT_TIMESTAMP;
    }

    function deleteNoOut($userid, $time){
        $this->db->query("DELETE FROM timesheet_noout WHERE userid = '$userid' AND localtimein = '$time' AND username = 'Webcheckin'");
    }

    function deleteOtherLogs($userid){
        $this->db->query("DELETE FROM webcheckin_trail WHERE userid = '$userid'");
    }

    public function checkDateTransaction($id,$date){
        $q_desc = $this->db->query("SELECT id FROM webcheckin_history WHERE userid = '$id' AND DATE(date_created) > '$date' LIMIT 1");
        if($q_desc->num_rows() > 0) return $q_desc->row()->id;
        else return false;
    }

    public function checkDateSetup($id,$date,$id){
        $q_desc = $this->db->query("SELECT employee FROM weblogin_setup WHERE employee = '$id' AND '$date' BETWEEN date_from AND date_to AND id != '$id'");
        if($q_desc->num_rows() > 0) return $q_desc->row()->employee;
        else return false;
    }

    public function checkExistingSetup($id,$date_from,$date_to){
        $q_desc = $this->db->query("SELECT employee FROM weblogin_setup WHERE employee = '$id' AND date_to BETWEEN '$date_from' AND '$date_to' LIMIT 1");
        if($q_desc->num_rows() > 0) return $q_desc->row()->employee;
        else return false;
    }

    public function checkExistingRank($rank){
        $q_desc = $this->db->query("SELECT id FROM survey_category WHERE rank = '$rank'");
        if($q_desc->num_rows() > 0) return $q_desc->row()->id;
        else return "none";
    }

    public function saveSurveyCatSetup($data){
        return $this->db->insert("survey_category", $data);
    }

    public function updateSurveyCatSetup($id,$name,$rank){
        return $this->db->query("UPDATE survey_category SET `rank` = '$rank',`name` = '$name' WHERE `id` = '$id'");
    }

    public function deleteSurveyCatSetup($id){
        return $this->db->query("DELETE FROM survey_category WHERE `id` = '$id'");
    }

    /*public function deleteSurveyCatSetup($id){
        return $this->db->query("DELETE FROM survey_category WHERE `id` = '$id'");
    }*/

    public function deleteWebSetup($id){
        return $this->db->query("DELETE FROM weblogin_setup WHERE `id` = '$id'");
    }

    public function saveSurveyItemsSetup($data){
        return $this->db->insert("survey_items", $data);
    }

    public function saveSurveyData($data){
        return $this->db->insert("survey_record", $data);
    }

    public function updateSurveyItemsSetup($data, $id){
        $this->db->where("id = '$id'");
        return $this->db->update('survey_items', $data);
    }

    function getEmployeeListDropdownWebcheckin($type='',$deptid='',$office='',$status=''){
        $where_clause = "";
        $datenow = date('Y-m-d');
        if($type) $where_clause .= " AND teachingtype = '$type' ";
        if($status != "") $where_clause .= " AND isactive = '$status' ";
        if($deptid) $where_clause .= " AND deptid = '$deptid' ";
        if($office) $where_clause .= " AND office = '$office' ";
         $where_clause .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
        return $this->db->query("SELECT employeeid, CONCAT(`lname`, ' ', `fname` , ' ', `mname`) AS fullname FROM employee WHERE 1 $where_clause")->result_array();
    }


}


