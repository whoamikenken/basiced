<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Facial extends CI_Model {

	function getServerTime(){
        return $this->db->query("SELECT CURRENT_TIMESTAMP")->row()->CURRENT_TIMESTAMP;
    }

	function Timelog($time){
		$isExist = false;
		$q_alumniAims = $this->db->query("SELECT * FROM facial_Log WHERE `time` = '$time'")->result();
		foreach ($q_alumniAims as $row) $isExist = true;
		return $isExist;
  	}

    function saveAttendanceLogTest($data,$person){
        $imageData = array();
        if (isset($data->base64image)) {
            $imageData['base64image'] = $data->base64image;
            unset($data->base64image);
        }else{
            unset($data->isImgDeleted);
            unset($data->isUpload);
            unset($data->state);
            unset($data->id);
        }
        
        if ($person == "STRANGERBABY") {
            unset($data->employeeid);
            $this->db->insert("facial_Log_Strangers", $data);
            $imageData['base_id'] = $this->db->insert_id();
            $this->db->insert($this->db->database_files.".facial_strangers_image", $imageData);
        }else{
            $this->db->insert("facial_Log_test", $data);
            if (isset($imageData['base64image'])) {
                $imageData['base_id'] = $this->db->insert_id();
                $this->db->insert($this->db->database_files.".facial_logs_image", $imageData);
            }
            
        }
        
    }

    function saveAttendanceLogFR($data,$person){
        $imageData = array();
        if (isset($data->imgBase64)) {
            $imageData['base64image'] = $data->imgBase64;
            unset($data->imgBase64);
        }
        
        // echo "<pre>";print_r($person);die;
        if ($person == "STRANGERBABY") {
            unset($data->employeeid);
            $this->db->insert("facial_Log_Strangers", $data);

            $imageData['base_id'] = $this->db->insert_id();
            $this->db->insert($this->db->database_files.".facial_strangers_image", $imageData);

        }else{
            $this->db->insert("facial_Log", $data);
            
            $imageData['base_id'] = $this->db->insert_id();
            $this->db->insert($this->db->database_files.".facial_logs_image", $imageData);
            // echo "<pre>";print_r($this->db->last_query());die;

        }
        
    }

    function saveAttendanceLog($data,$person){
        $imageData = array();
        if (isset($data->base64image)) {
            $imageData['base64image'] = $data->base64image;
            unset($data->base64image);
        }else{
            unset($data->isImgDeleted);
            unset($data->isUpload);
            unset($data->state);
            unset($data->id);
        }
        
        if ($person == "STRANGERBABY") {
            unset($data->employeeid);
            $this->db->insert("facial_Log_Strangers", $data);
            $imageData['base_id'] = $this->db->insert_id();
            $this->db->insert($this->db->database_files.".facial_strangers_image", $imageData);
        }else{
            $this->db->insert("facial_Log", $data);
            if (isset($imageData['base64image'])) {
                $imageData['base_id'] = $this->db->insert_id();
                $this->db->insert($this->db->database_files.".facial_logs_image", $imageData);
            }
            
        }
        
    }

    function saveFeatureData($data){
        return $this->db->insert("facial_feature", $data);
    }

    function updateFeatureData($FaceId,$PersonId, $DeviceKey, $status){
        return $this->db->query("UPDATE facial_person SET facial_status = '$status' WHERE deviceKey = '$DeviceKey' AND personId = '$PersonId'"); 
    }

  	function Ttesa($data){
		return $this->db->query("INSERT INTO facial_console (`text`) VALUES ('$data')"); 
	}

	function heartbeat($data){
		$this->db->like('deviceKey', $data->deviceKey);
		$this->db->from('facial_heartbeat');
		if($this->db->count_all_results()) {
		  $this->db->query("UPDATE facial_heartbeat SET `time` = '$data->time',`ip` = '$data->ip',`personCount` = '$data->personCount',`faceCount` = '$data->faceCount',`status` = 'Connected',`version` = '$data->version',`timestamp` = CURRENT_TIMESTAMP WHERE deviceKey = '$data->deviceKey'");
		}else{
		  $this->db->insert("facial_heartbeat", $data);
		}

	}

	function checkTask($serial){
		$q_alumniAims = $this->db->query("SELECT * FROM facial_task WHERE `status` = 'Pending' AND `deviceKey` = '$serial' ORDER BY id LIMIT 1")->result();
		return $q_alumniAims;
	}

	function facialMasterSetup(){
        return $this->db->query("SELECT * FROM facial_heartbeat")->result();
    }

    function facialDeviceDownChecker(){
        return $this->db->query("SELECT * FROM facial_heartbeat")->result_array();
    }

    function loadEmpWithPermission($deviceKey){
        $data = $this->db->query("SELECT permission FROM facial_heartbeat WHERE deviceKey = '$deviceKey'");
        if($data->num_rows > 0) return $data->result_array();
        else return FALSE;
    }

    function savePermissionToDevice($emp,$deviceKey,$name){
		return $this->db->query("UPDATE facial_heartbeat SET permission = '$emp', deviceName = '$name' WHERE deviceKey = '$deviceKey'"); 
	}

	function getUsers(){
        return $this->db->query("SELECT * FROM user_info WHERE `type` = 'ADMIN'")->result_array();
    }

    function getDataFacial($code){
        $data = $this->db->query("SELECT * FROM facial_heartbeat WHERE deviceKey = '$code' ");
        if($data->num_rows > 0) return $data->result_array();
        else return FALSE;
    }

    function getFaceIDImage($faceId){
        return $this->db->query("SELECT Feature FROM facial_feature WHERE FaceID = '$faceId'");
    }

    function saveFacialSetup($filetype = "", $file = "", $name = "", $recog = "", $serial_number = "", $recogScore = "", $recogDistance = "", $campus = "", $recogInterval = "", $mask = "", $mask_dialogue = "", $video_link = ""){
    	$add = "";
    	if ($filetype != "") $add .= ", filetype = '$filetype'";
    	if ($file != "") $add .= ", image = '$file'";
        if ($mask != "") $add .= ", mask = '$mask'";
        if ($mask_dialogue != "") $add .= ", mask_dialogue = '$mask_dialogue'";
        if ($video_link != "") $add .= ", video_link = '$video_link'";
		return $this->db->query("UPDATE facial_heartbeat SET deviceName = '$name',recog = '$recog', recogScore = '$recogScore', recogDistance = '$recogDistance', recogInterval = '$recogInterval', campusid = '$campus' $add WHERE deviceKey = '$serial_number'"); 
	}

	function facialDeviceLog(){
        return $this->db->query("SELECT * FROM facial_Log")->result_array();
    }

    function facialDevicePerson($serial_number, $type = "", $deptid = "", $office = "", $status = "", $empid = ""){
        $WC = "serial_number = '$serial_number' ";
        $datenow = date("Y-m-d");
        if ($type != "") $WC .= " AND a.teachingtype = '$type'";
        if ($deptid != "") $WC .= " AND a.deptid = '$deptid'";
        if ($office != "") $WC .= " AND a.office = '$office";
        if ($empid != "") $WC .= " AND a.employeeid = '$empid'";
        if ($status != "") $WC .= " AND a.isactive = '$status'";
        // if ($deviceKey != "") $WC .= " AND b.deviceKey = '".$deviceKey."'";
        $WC .= " AND (('$datenow' < a.dateresigned2 OR a.dateresigned2 = '0000-00-00' OR a.dateresigned2 = '1970-01-01' OR a.dateresigned2 IS NULL) AND a.isactive ='1')";
        return $this->db->query("SELECT CONCAT(a.`lname`, ' ,', a.`fname` , ' ,', a.`mname`) AS fullname, b.* FROM facial_person b INNER JOIN employee a ON b.employeeid = a.employeeid WHERE $WC")->result();
    }

    function deleteFacialTaskDevice($deviceKey){

        $dbname = $this->db->database_files;

        $this->db->query("DELETE FROM `facial_task` WHERE deviceKey = '$deviceKey'");
        return true;
    }

    function saveTaskToDevice($deviceKey, $interface, $task){
		return $this->db->query("INSERT INTO facial_task (`interface`,`task`,`deviceKey`) VALUES ('$interface','$task','$deviceKey')"); 
	}
    
    function insertTaskResult($table){
        return $this->db->insert("facial_task_result", $table);
    }

    function updateTaskResult($taskID,$status){
        return $this->db->query("UPDATE facial_task SET status = '$status' WHERE id = '$taskID'"); 
    }

    function getPersonData($id, $deviceKey = ""){
        return $this->db->query("SELECT * FROM facial_person WHERE personId = '$id' AND serial_number = '$deviceKey'")->result_array();
    }

    function getEmployee(){
        $datenow = date("Y-m-d");
        $WC = " WHERE (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
        return $this->db->query("SELECT `employeeid`, CONCAT(`lname`,', ',`fname`,' ',`mname`) AS fullname FROM employee $WC ORDER BY `lname` ASC")->result_array();
    }

    function getEmployeeDevice($key){
        return $this->db->query("SELECT `personId`, name, employeeid FROM facial_person WHERE serial_number = '$key' ORDER BY `name` ASC")->result_array();
    }

    function saveUpdatePerson($filetype = "", $file = "", $name, $card, $empid, $serial_number, $personID=""){
        $add = "";
        if ($filetype != "") $add .= ", filetype = '$filetype'";
        if ($file != "") $add .= ", image = '$file'";
        return $this->db->query("UPDATE facial_person SET name = '$name', card = '$card', employeeid = '$empid', serial_number = '$serial_number' $add WHERE serial_number = '$serial_number' AND personid = '$personID'"); 
    }

    function savePerson($faceid = "", $name, $card, $empid, $serial_number, $personId){
        return $this->db->query("INSERT INTO facial_person (`personId`,`name`,`card`,`employeeid`,`serial_number`,`FaceId1`) VALUES ('$personId','$name','$card','$empid','$serial_number','$faceid')"); 
    }

    function saveImageFaceID($data){
        $dbname = $this->db->database_files;
        $this->db->insert("$dbname.facial_image", $data);
    }

    function deletePerson($id, $serial_number){
        $this->db->query("DELETE FROM facial_person WHERE personId = '$id' AND serial_number = '$serial_number'");
        $dbname = $this->db->database_files;
        $this->db->query("DELETE FROM $dbname.facial_image WHERE personId = '$id' AND DeviceKey = '$serial_number'");
        return true;
    }

    function deleteLogs($deviceKey){
        $this->db->query("DELETE FROM facial_Log WHERE deviceKey = '$deviceKey'");
        return true;
    }   

    function deleteDevice($deviceKey){
        $this->db->query("DELETE FROM facial_heartbeat WHERE deviceKey = '$deviceKey'");
        return true;
    }

    function resetPerson($deviceKey){
        $this->db->query("DELETE FROM facial_person WHERE serial_number = '$deviceKey'");
        $this->db->query("DELETE FROM facial_face_feature WHERE deviceKey = '$deviceKey'");
        $dbname = $this->db->database_files;
        $this->db->query("DELETE FROM $dbname.facial_image WHERE DeviceKey = '$deviceKey'");
        return true;
    }

    function getActiveEmployeesForFacial($status='', $deptid='',$office='',$empid='',$empList=''){
        $where_clause = "";
        $datenow = date('Y-m-d');
        if($status != "") $where_clause .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
        if($deptid) $where_clause .= " AND deptid = '$deptid' ";
        if($office) $where_clause .= " AND office = '$office' ";
        if($empid) $where_clause .= " AND employeeid = '$empid' ";
        if($empList != "''") $where_clause .= " AND employeeid IN($empList) ";

        $query = $this->db->query("SELECT employeeid, fname, lname, employeecode FROM employee WHERE 1 $where_clause");
        if($query->num_rows > 0) return $query->result_array();
        else return false;
    }

    function getEmployeeLogs()
    { 
        $datas = $this->input->get();
        $toks = $datas["toks"];

        $imageDatabase = "";
        $imageDatabase = $this->db->database_files;
        // Table's primary key 
        $primaryKey = 'id';
        $WC = "1";

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */  
        $joinQuery = $extraWhere = $groupBy = $having = $data = "";
        $datenow = date("Y-m-d");

        // Table columns  
        $type = $toks ? $this->gibberish->decrypt( $datas["type"], $toks ) : $datas["type"];
        $deptid = $toks ? $this->gibberish->decrypt( $datas["deptid"], $toks ) :  $datas["deptid"];
        $office = $toks ? $this->gibberish->decrypt( $datas["office"], $toks ) :  $datas["office"];
        $employee = $toks ? $this->gibberish->decrypt( $datas["employee"], $toks ) :  $datas["employee"];
        $from = $toks ? $this->gibberish->decrypt( $datas["from"], $toks ) :  $datas["from"];
        $to = $toks ? $this->gibberish->decrypt( $datas["to"], $toks ) :  $datas["to"];
        $status = $toks ? $this->gibberish->decrypt( $datas["status"], $toks ) :  $datas["status"];
        $deviceKey = $toks ? $this->gibberish->decrypt( $datas["serial"], $toks ) :  $datas["serial"];
        $empstat = $toks ? $this->gibberish->decrypt( $datas["empstat"], $toks ) :  $datas["empstat"];
        // echo "<pre>";print_r($empstat);die;
        if ($type != "") $WC .= " AND c.teachingtype = '".$type."'";
        if ($deptid != "") $WC .= " AND c.deptid = '".$deptid."'";
        if ($office != "") $WC .= " AND c.office = '".$office."'";
        if ($empstat != "") $WC .= " AND c.employmentstat = '".$empstat."'";
        if ($employee != "") $WC .= " AND c.employeeid = '".$employee."'";
        if ($status != "") $WC .= " AND c.isactive = '".$status."'";
        if ($deviceKey != "") $WC .= " AND a.deviceKey = '".$deviceKey."'";
        if($from && $to)  $WC .= " AND DATE(FROM_UNIXTIME(FLOOR(a.time/1000))) BETWEEN '$from' AND '$to'";
        $WC .= " AND (('$datenow' < c.dateresigned2 OR c.dateresigned2 = '0000-00-00' OR c.dateresigned2 = '1970-01-01' OR c.dateresigned2 IS NULL) AND c.isactive ='1')";

        // DB table to use
        $table = "(SELECT `c`.`employeeid` AS `employeeid`,`a`.`id` AS `id`,`a`.`deviceKey` AS `deviceKey`,`b`.`name` AS `name` ,`e`.`description` AS `description`,`a`.`time` AS `time`,`d`.`deviceName` AS `deviceName`,`f`.`base64image` AS `base64image` FROM `facial_Log` AS `a` INNER JOIN `facial_person` AS `b` ON (`a`.`personId` = `b`.`personId`) INNER JOIN `employee` AS `c` ON (`b`.`employeeid` = `c`.`employeeid`) LEFT JOIN `facial_heartbeat` AS `d` ON (`a`.`deviceKey` = `d`.`deviceKey`) LEFT JOIN `code_office` AS `e` ON (`c`.`office` = `e`.`code`) LEFT JOIN ".$imageDatabase.".`facial_logs_image` AS `f` ON (`a`.`id` = `f`.`base_id`) WHERE $WC GROUP BY a.id) temp";

        // echo "<pre>";print_r($table);die;
        $joinQuery = "";
        $extraWhere = "";

        $columns = array(   
            array( 'db' => 'employeeid', 'dt' => 0),
            array( 'db' => 'deviceKey', 'dt' => 5),
            array( 'db' => 'name', 'dt' => 1),
            array( 'db' => 'description', 'dt' => 2),
            array( 'db' => 'time', 'dt' => 3, 'formatter' => function( $d, $row ) { return date("Y-m-d g:i:s A", substr($d, 0, 10));  } ),
            array( 'db' => 'deviceName', 'dt' => 4),
            array( 'db' => 'base64image', 'dt' => 6, 'formatter' => function( $d, $row ) {
                return ($d != "")?'<img src="data:image/jpeg;base64,'.$d.'" width="150" height="200">':"Not Found";})
        );

        // SQL server connection information
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );  
        
        $data = SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having );
        
        echo json_encode($data);
    }

    function getDeviceStrangers()
    { 
        $datas = $this->input->get();
        
        // DB table to use
        $table = 'facial_Log_Strangers'; 

        // Table's primary key 
        $primaryKey = 'id';
        $WC = "1";
        $toks = $datas["toks"];
        $imageDatabase = "";
        $imageDatabase = $this->db->database_files;
        $deviceKey = $toks ? $this->gibberish->decrypt( $datas["serialStranger"], $toks ) :  $datas["serialStranger"];
        $startdate = $toks ? $this->gibberish->decrypt( $datas["startdate"], $toks ) :  $datas["startdate"];
        $enddate = $toks ? $this->gibberish->decrypt( $datas["enddate"], $toks ) :  $datas["enddate"];


        if ($deviceKey != "") $WC .= " AND a.deviceKey = '".$deviceKey."'";
        if ($startdate != "" && $enddate != "") $WC .= " AND DATE(a.`date`) BETWEEN '".$startdate."' AND '".$enddate."'";
        // DB table to use
        $table = "(SELECT `a`.`id` AS `id`,`a`.`deviceKey` AS `deviceKey`,`a`.`date` AS `date`,`b`.`base64image` AS `base64image` FROM `facial_Log_Strangers` AS `a` INNER JOIN ".$imageDatabase.".`facial_strangers_image` AS `b` ON (`a`.`id` = `b`.`base_id`) WHERE $WC) temp";

        // Table columns  
        $columns = array(   
            array( 'db' => 'deviceKey', 'dt' => 1),
            array( 'db' => 'date', 'dt' => 0, 'formatter' => function( $d, $row ) { return date("Y-m-d h:i:s A",strtotime($d)); } ),
            array( 'db' => 'base64image', 'dt' => 2, 'formatter' => function( $d, $row ) {
                return ($d != "")?'<img src="data:image/jpeg;base64,'.$d.'" width="150" height="200">':"CARD USED";})
        ); 

        // SQL server connection information
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );  

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */  
        $joinQuery = $extraWhere = $groupBy = $having = $data = "";
        

        $data = SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having );
        echo json_encode($data);
    }

    function getDeviceTask()
    { 
        // DB table to use
        $table = 'facial_task'; 

        // Table's primary key 
        $primaryKey = 'id';

        // Table columns  
        $columns = array(   
            array( 'db' => 'ip', 'dt' => 4, 'field' => 'ip'),
            array( 'db' => 'id', 'dt' => 5, 'field' => 'id'),
            array( 'db' => 'deviceKey', 'dt' => 3, 'field' => 'deviceKey'),
            array( 'db' => 'interface', 'dt' => 1, 'field' => 'interface'),
            array( 'db' => 'status', 'dt' => 2, 'field' => 'status' ),
            array( 'db' => 'timestamp', 'dt' => 0, 'field' => 'timestamp', 'formatter' => function( $d, $row ) { return date("Y-m-d h:i:s A",strtotime($d)); } )
        ); 

        // SQL server connection information
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );  

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */  
        $joinQuery = $extraWhere = $groupBy = $having = $data = "";
        $datas = $this->input->get();
        
        $extraWhere = "deviceKey = '".$datas["serial_number"]."' AND `status` = 'Pending'";

        $data = SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having );
        echo json_encode($data);
    }

    function logsImage($id=''){
        $res = $this->db->query("SELECT base64image FROM facial_Log WHERE id='$id'");
        if (isset($res->row(0)->base64image)) return $res->row(0)->base64image;
        else return "";
    }

    function getEmpIdFacial($id=''){
        $res = $this->db->query("SELECT employeeid FROM facial_person WHERE personId='$id'");
        if (isset($res->row(0)->employeeid)) return $res->row(0)->employeeid;
        else return "";
    }

    function getPersonNamePersonID($id=''){
        $res = $this->db->query("SELECT name FROM facial_person WHERE personId = '$id'");
        if (isset($res->row(0)->name)) return $res->row(0)->name;
        else return "Not Found";
    }

    function getEmpRFID($id=''){
        $res = $this->db->query("SELECT employeecode FROM employee WHERE employeeid='$id'");
        if (isset($res->row(0)->employeecode)) return $res->row(0)->employeecode;
        else return "";
    }

    function checkFacialPerson($id='', $serial){
        $res = $this->db->query("SELECT employeeid FROM facial_person WHERE employeeid='$id' AND serial_number='$serial'");
        if (isset($res->row(0)->employeeid)) return $res->row(0)->employeeid;
        else return "none";
    }

    function logChecker($time='', $personId){
        $res = $this->db->query("SELECT personId FROM facial_Log WHERE personId = '$personId' AND `time` = '$time'");
        if (isset($res->row(0)->personId)) return true;
        else return false;
    }

    function deleteTaskResult($deviceID){
        return $this->db->query("DELETE FROM facial_task WHERE deviceKey = '$deviceID' AND status = 'Completed'"); 
    }

    function getLastLogFacial($employeeid,$date){
        $return['log_type'] = "new";
        $query = $this->db->query("SELECT log_type, localtimein FROM timesheet_trail WHERE date(localtimein) = '$date' AND userid = '$employeeid'")->result();
        foreach($query as $row)
        {
            $return['log_type'] = $row->log_type;
            $return['localtimein'] = $row->localtimein;
        }
        return $return; 
    }

    function getLastLogFacialStudent($employeeid,$date){
        $return['log_type'] = "new";
        $query = $this->db->query("SELECT log_type, localtimein FROM timesheet_trail_student WHERE date(localtimein) = '$date' AND userid = '$employeeid'")->result();
        foreach($query as $row)
        {
            $return['log_type'] = $row->log_type;
            $return['localtimein'] = $row->localtimein;
        }
        return $return; 
    }

    public function saveFacialCheckIn($data){
        $this->db->insert("timesheet_trail", $data);
    }

    public function saveFacialCheckInStudent($data){
        $this->db->insert("timesheet_trail_student", $data);
    }

    public function insertDowntimeFacial($data){
        $this->db->insert("facial_downtime", $data);
    }

    function deleteOtherLogsFacial($userid, $date = ""){
        $this->db->query("DELETE FROM timesheet_trail WHERE userid = '$userid' AND username = 'Facial' AND date(localtimein) = '$date'");
    }

    function deleteOtherLogsFacialStudent($userid, $date = ""){
        $this->db->query("DELETE FROM timesheet_trail_student WHERE userid = '$userid' AND username = 'Facial' AND date(localtimein) = '$date'");
    }

    function deleteNoOutFacial($userid, $time){
        $this->db->query("DELETE FROM timesheet_noout WHERE userid = '$userid' AND localtimein = '$time' AND username = 'Facial'");
    }

    function deleteNoOutFacialStudent($userid, $time){
        $this->db->query("DELETE FROM timesheet_noout_student WHERE userid = '$userid' AND localtimein = '$time' AND username = 'Facial'");
    }

    function adminNumberDowntime(){
        $this->db->select('cp_number');
        return $this->db->get('facial_admin_number')->result_array();
    }

    function adminEmailDowntime(){
        $this->db->select('email');
        return $this->db->get('facial_admin_email')->result_array();
    }

    function updateFacialStatus($deviceKey, $status){
        $this->db->set('status', $status);
        $this->db->where('deviceKey', $deviceKey);
        $this->db->update('facial_heartbeat');
    }

    function updateFacialDataStatus($emp,$deviceKey,$status,$faceID){
        $setter = "";
        if (strpos(pack('H*', $faceID), 'face1') !== false){
            $setter = "facial_status1";
        }elseif(strpos(pack('H*', $faceID), 'face2') !== false){
            $setter = "facial_status2";
        }elseif(strpos(pack('H*', $faceID), 'face3') !== false){
            $setter = "facial_status3";
        }
        $this->db->set($setter, $status);
        $this->db->where('serial_number', $deviceKey);
        $this->db->where('personId', $emp);
        return $this->db->update('facial_person');
    }

    function updateFaceID($personID,$deviceKey,$faceId){
        $setter = "";
        if (strpos(pack('H*', $faceId), 'face1') !== false){
            $setter = "facial_status1 = 'Pending', FaceId1 = '$faceId'";
        }elseif(strpos(pack('H*', $faceId), 'face2') !== false){
            $setter = "facial_status2 = 'Pending', FaceId2 = '$faceId'";
        }elseif(strpos(pack('H*', $faceId), 'face3') !== false){
            $setter = "facial_status3 = 'Pending', FaceId3 = '$faceId'";
        }
        $this->db->query("UPDATE facial_person SET $setter WHERE serial_number = '$deviceKey' AND personID = '$personID'");
    }

    public function facial_console($data){
        $this->db->insert("facial_console", $data);
    }

    function getTaskInterface($id=''){

        $dbname = $this->db->database_files;

        $res = $this->db->query("SELECT interface FROM `facial_task` WHERE id='$id'");
        if (isset($res->row(0)->interface)) return $res->row(0)->interface;
        else return "";
    }

    function getTaskInfo($id=''){

        $dbname = $this->db->database_files;

        $res = $this->db->query("SELECT task FROM `facial_task` WHERE id='$id'");
        if (isset($res->row(0)->task)) return $res->row(0)->task;
        else return "";
    }

    function getFacialIDTaskNo($id=''){

        $dbname = $this->db->database_files;

        $res = $this->db->query("SELECT task FROM `facial_task` WHERE id='$id'")->result();
        $task = explode(",", $res[0]->task);
        $json = json_decode("{".$res[0]->task."}");
        if (count($json)){
            return $json->faceId;
        }else{
            return "";
        } 
    }

    function getPersonID($faceId='', $deviceKey = ''){
        $res = $this->db->query("SELECT personId FROM facial_person WHERE serial_number = '$deviceKey' AND FaceId1 = '$faceId' OR FaceId2 = '$faceId' OR FaceId3 = '$faceId'");
        if (isset($res->row(0)->personId)) return $res->row(0)->personId;
        else return "";
    }

    function getFaceImageFaceID($faceId,$personID,$deviceKey){
        $dbname = $this->db->database_files;
        return $this->db->query("SELECT image,mime FROM $dbname.facial_image WHERE DeviceKey = '$deviceKey' AND personID = '$personID' AND FaceID = '$faceId'");
    }

    function checkExistingFaceIDImage($faceId,$personID,$deviceKey){
        if (strpos(pack('H*', $faceId), 'face1') !== false){
            $setter = "facial_status1";
        }elseif(strpos(pack('H*', $faceId), 'face2') !== false){
            $setter = "facial_status2";
        }elseif(strpos(pack('H*', $faceId), 'face3') !== false){
            $setter = "facial_status3";
        }
        $this->db->select($setter);
        $this->db->where('serial_number', $deviceKey);
        $this->db->where('personId', $personID);
        $this->db->where($setter, "Success");
        $res = $this->db->get('facial_person');

        if (isset($res->row(0)->$setter)) return $res->row(0)->$setter;
        else return false;
    }

    function deleteFacialImageDevice($deviceKey){
        $dbname = $this->db->database_files;
        $this->db->query("DELETE FROM ".$dbname.".`facial_image` WHERE DeviceKey = '$deviceKey'");
        return true;
    }

    function deleteFacialImagePerson($personId,$deviceKey){
        $dbname = $this->db->database_files;
        $this->db->query("DELETE FROM ".$dbname.".`facial_image` WHERE DeviceKey = '$deviceKey' AND personId = '$personId'");
        return true;
    }

    function updateImageFaceID($faceId,$personID,$deviceKey,$file){
        $dbname = $this->db->database_files;
        $this->db->query("UPDATE $dbname.facial_image SET image = '$file' WHERE DeviceKey = '$deviceKey' AND personID = '$personID' AND FaceID = '$faceId'");
    }

    function facialRemoveTimesheet($deviceKey, $from, $to){
        $this->db->query("DELETE FROM `timesheet` WHERE addedby = '$deviceKey' AND DATE(timein) BETWEEN '$from' AND '$to'");
        $this->db->query("DELETE FROM `timesheet_trail` WHERE machine_id = '$deviceKey' AND DATE(localtimein) BETWEEN '$from' AND '$to'");
        return true;
    }

    function getDataFacialLogs($deviceKey, $from, $to){
        return $this->db->query("SELECT * FROM `facial_Log` WHERE deviceKey = '$deviceKey' AND DATE(FROM_UNIXTIME(FLOOR(`time`/1000))) BETWEEN '$from' AND '$to' ORDER BY `time`")->result_array();
    }

    function facialCommand($payload,$url)
    { 
        $faceServer = $this->db->faceServer;
        $urlServer = $faceServer.$url;
        $curl = curl_init();
        // echo "<pre>";print_r($urlServer);die;
        curl_setopt_array($curl, array(
          CURLOPT_URL => $urlServer,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 4000,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $payload,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
          ),
        ));

        $response = curl_exec($curl);
        
        $response = json_decode("[".$response."]");
        curl_close($curl);
        if($url == "api/face/find"){
            $response = $response[0]->data;
        }else{
            $response = $response[0]->success;
        }
        return $response;
    }

    function facialCommandLogs($ip,$from, $to)
    { 
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://'.$ip.':8090/findRecords?pass=12345678&personId=-1&length=-1&index=0&startTime='.$from.'%2001:00:00&endTime='.$to.'%2011:59:00&model=0',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    function reprocessFacialLogs($dateFrom, $dateTo, $empid = ""){
        $period = $this->getDatesFromRange($dateFrom, $dateTo);        
        $wh = "";
        if($empid) $wh = " AND employeeid = '$empid'";
        $process = 0;
        $emplist = $this->db->query("SELECT employeeid FROM employee WHERE isactive = 1 $wh")->result_array();
        foreach ($emplist as $rw => $val) {
            $empId = $val['employeeid'];
            foreach ($period as $key => $value) {
                // Checkif has logs
                $record = $this->db->query("SELECT `id` FROM facial_Log WHERE employeeid = '$empId' AND DATE(FROM_UNIXTIME(FLOOR(`time`/1000))) = '$value'")->result_array();
                if (count($record) > 1) {
                    // Create Time And Time Out
                    // OUT
                    $timeoutRecord = $this->db->query("SELECT `time` FROM facial_Log WHERE employeeid = '$empId' AND DATE(FROM_UNIXTIME(FLOOR(`time`/1000))) = '$value' ORDER BY `time` DESC LIMIT 1")->result_array();
                    $Out = date("Y-m-d H:i:s", substr($timeoutRecord[0]['time'], 0, 10));
                    // IN
                    $timeinRecord = $this->db->query("SELECT `time` FROM facial_Log WHERE employeeid = '$empId' AND DATE(FROM_UNIXTIME(FLOOR(`time`/1000))) = '$value' ORDER BY `time` ASC LIMIT 1")->result_array();
                    $In = date("Y-m-d H:i:s", substr($timeinRecord[0]['time'], 0, 10));

                    // Check if timesheet is existing 
                    $timesheetRecord = $this->db->query("DELETE FROM timesheet WHERE userid = '$empId' AND DATE(timein) = '$value' AND otype = 'Facial'");
                    // Create TimeSheet data
                    $timesheetData = array();
                    $timesheetData['userid'] = $empId;
                    $timesheetData['timein'] = $In;
                    $timesheetData['timeout'] = $Out;
                    $timesheetData['otype'] = "Facial";
                    $timesheetData['addedby'] = "FacialResync";

                    $this->db->insert("timesheet", $timesheetData);
                    $process++;
                }
            }
        }
        return $process;
    }

    function getDatesFromRange($start, $end, $format = 'Y-m-d') {
        $array = array();
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach($period as $date) { 
            $array[] = $date->format($format); 
        }

        return $array;
    }

    function checkerIfHasLogs($empid='', $date){

        $res = $this->db->query("SELECT id FROM facial_Log WHERE employeeid = 'empid' AND DATE(FROM_UNIXTIME(FLOOR(`time`/1000))) = '$date'")->result();
        if (count($res) > 1){
            return "logged";
        }else{
            return "nolog";
        } 
    }

    function getFacialFeature($personID=''){
        $res = $this->db->query("SELECT feature FROM facial_face_feature WHERE personId = '$personID'")->result();
        if (count($res) > 0){
            return $res[0]->feature;
        }else{
            return "noimage";
        } 
    }

    public function getFacialName($deviceKey=''){
        $res = $this->db->query("SELECT deviceName FROM facial_heartbeat WHERE deviceKey='$deviceKey'");
        return $res->row(0)->deviceName;
    }

    public function getFacialModel($deviceKey=''){
        $res = $this->db->query("SELECT version FROM facial_heartbeat WHERE deviceKey='$deviceKey'");
        if($res->row(0)->version != "13.6483" || $res->row(0)->version != "62.4100" ){
            return 'FR';
        }else{
            return 'RA';
        }
    }

    function getAllFRDevices($serial_number){
        return $this->db->query("SELECT * FROM facial_heartbeat WHERE deviceKey != '$serial_number' AND `timestamp` >= CURRENT_TIMESTAMP - INTERVAL 5 MINUTE AND (version = '1.41.7.0' OR version = '1.41.7.4' OR version = '1.51.0.5')")->result();
    }

    function updateFacialDataStatusRA($emp,$deviceKey,$status){
        $this->db->set("facial_status1", $status);
        $this->db->where('serial_number', $deviceKey);
        $this->db->where('personId', $emp);
        return $this->db->update('facial_person');
    }

    public function isStudentExist($id=''){
        $emp = $this->db->query("SELECT CONCAT(`lname`, ', ', `fname`, ', ', `mname`) AS fullname FROM student WHERE studentid = '$id'");
        if($emp->num_rows() > 0) {
            return true;
        }else{
            return false;
        }
    }

    public function isStudent($id=''){
        $emp = $this->db->query("SELECT CONCAT(`lname`, ', ', `fname`, ', ', `mname`) AS fullname FROM employee WHERE employeeid = '$id'");
        if($emp->num_rows() > 0) {
            return "employee";
        }else{
            $emp = $this->db->query("SELECT CONCAT(`lname`, ', ', `fname`, ', ', `mname`) AS fullname FROM student WHERE studentid = '$id'");
            if($emp->num_rows() > 0) {
                return "student";
            }else{
                return false;
            }
        }
    }

    public function getNumberStudentParent($studentid=''){
        $res = $this->db->query("SELECT mobile FROM student WHERE studentid='$studentid'");
        if(isset($res->row(0)->mobile)){
            return $res->row(0)->mobile;
        }else{
            return '';
        }
    }

}
