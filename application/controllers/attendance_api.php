<?php
/**
 * @author Max Consul
 * @copyright 2018
 */
set_time_limit(0);
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');
 
class Attendance_api extends REST_Controller
{
    function __construct(){
        parent::__construct();
        // $this->config->set_item('rest_valid_logins', array('admin2'=>'12345'));
        // $this->config->load('rest');
        // var_dump($this->config->item('rest_valid_logins'));
        $this->load->model("facial");
    }

    public function getStudent_post()
    {
        $this->load->model("facial");
        $return = array('code' => 0, 'msg' => 'error');
        $data = $this->input->post();
        if ($data['secret'] == "MuTemXecGz") {
            $studentid = $data['StudNo'];
            $record = $this->db->query("SELECT * FROM student WHERE studentid = '$studentid'")->result();
            echo json_encode($record);
        } else {
            echo json_encode($return);
        }
    }

    public function saveStudent_post()
    {
        $this->load->model("facial");
        $return = array('code' => 0,'msg' => 'error');
        $data = $this->input->post();
        $dataToProcess = array();
        $dataToProcess['studentid'] = $data['StudNo'];
        $dataToProcess['fname'] = $data['FName'];
        $dataToProcess['lname'] = $data['LName'];
        $dataToProcess['studentcode'] = $data['RFIDno'];
        $dataToProcess['coursecode'] = $data['CourseCode'];
        $dataToProcess['mobile'] = $data['Pmobile'];
        if($data['secret'] == "MuTemXecGz"){
            $checkIfExisting = $this->facial->isStudentExist($data['StudNo']);
            unset($data['secret']);
            if ($checkIfExisting) {
                $this->db->where('studentid', $data['StudNo']);
                $this->db->update('student', $dataToProcess);
                $return = array('code' => 1, 'msg' => 'updated');
                echo json_encode($return);
            }else{
                $this->db->insert("student", $dataToProcess);
                $return = array('code' => 1, 'msg' => 'inserted');
                echo json_encode($return);
            }
        }else{
            echo json_encode($return);
        }
    }

    function view_employee_attendance_post(){
        $this->load->model('student');
        $studentid     = $this->post('studentid');
        $cutoff_from    = $this->post('cutoff_from');
        $cutoff_to      = $this->post('cutoff_to');
        
        $data['logtime'] = $this->student->getInOut($studentid,$cutoff_from,$cutoff_to);

        $this->response($data,200);


    }

    function getCutoffDate_post(){
        $data = '';
        $query = $this->db->query("SELECT CutoffFrom, CutoffTo FROM cutoff ORDER BY timestamp DESC")->result_array();
        if($query){
            foreach($query as $key => $value){
                $datefrom = date_create($value['CutoffFrom']);
                $datefrom = date_format($datefrom, "m/d/Y");
                $dateto = date_create($value['CutoffTo']);
                $dateto = date_format($dateto, "m/d/Y");
                $data .= "<option value=".$dateto." - ".$dateto.">".$datefrom." - ".$dateto."</option>";
            }
        }
        $this->response($data,200);
    }

    public function FacialAPIFR_post(){
        $this->load->model("facial");
        $this->load->model("webcheckin");
        $this->load->model("fingerprint");
        $posted_data = file_get_contents("php://input");
        // $this->facial->facial_console(array('text' => $posted_data));
        foreach (explode("&",$posted_data) as $key => $value) {
            $newData = explode("=", $value);
            $data->$newData[0] = urldecode($newData[1]);
        }
        // echo "<pre>";print_r($data);die;
        $posted_data = $data;
        $time = $posted_data->time;
        $serial = $posted_data->deviceKey;
        $response = "";
        $data = array();
        $employeeId = $this->facial->getEmpIdFacial($posted_data->personId);
        $webSetup = '';
        $posted_data->employeeid = $employeeId;

        $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$employeeId' AND `status` = 'active'");
        if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;
        if($webSetup){
            $posted_data->isvalid = 0;
        }
        $chekerIfLogExisting = $this->facial->logChecker($posted_data->time, $posted_data->personId, $serial);
        
        if ($chekerIfLogExisting) {      
            echo '{"result": 1,"success": True}';
            // echo 'result=1&success=True';
            die;
        }else {

            $this->facial->saveAttendanceLogFR($posted_data, $posted_data->personId);
            echo '{"result": 1,"success": True}';
            // echo 'result=1&success=True';
        }
        // echo "<pre>";print_r($this->db->last_log_typequery());die;
        if ($posted_data->personId != "STRANGERBABY") {
            $logDate = date("Y-m-d", substr($posted_data->time, 0, 10));
            $last_log_type = $this->facial->getLastLogFacial($employeeId, $logDate);

            $data['localtimein'] = date("Y-m-d H:i:s", substr($posted_data->time, 0, 10));
            $data['userid'] = $employeeId;
            $data['username'] = "Facial";
            $data['machine_id'] = $posted_data->deviceKey;


            // For Gate
            $dataAttempts["user_id"] = $employeeId;
            $dataAttempts["ip"] = $posted_data->ip;
            $dataAttempts["terminalid"] = $posted_data->deviceKey;
            $dataAttempts["fullname"] = $this->fingerprint->getFullNameEmployee($data['userid']);
            $dataAttempts["stamp"] = date("H:i:s", substr($posted_data->time, 0, 10));
            $dataAttempts['username'] = $this->facial->getFacialName($posted_data->deviceKey);
            $dataAttempts['time'] = $posted_data->time;

            if ($last_log_type['log_type'] == "new"){
                $nout = array();
                $data['log_type'] = "IN";
                $nout['userid'] = $employeeId;
                $nout['localtimein'] = $data['localtimein'];
                $nout['log_type'] = $data['log_type'];
                $nout['username'] = $data['username'];

                // LOGIN ATTEMPTS
                $dataAttemptsTerminal["user_id"] = $employeeId;
                $dataAttemptsTerminal["terminalid"] = $posted_data->deviceKey;
                $dataAttemptsTerminal["fullname"] = $this->fingerprint->getFullNameEmployee($data['userid']);
                $dataAttemptsTerminal["stamp_in"] = date("H:i:s", substr($posted_data->time, 0, 10));
                $dataAttemptsTerminal['terminal_in'] = $this->facial->getFacialName($posted_data->deviceKey);
                $dataAttemptsTerminal['time_in'] = $posted_data->time;

                // Gate
                $dataAttempts['action'] = "IN";
                $dataAttempts['status'] = "success";

                $this->webcheckin->saveNoOut($nout);
                $this->facial->saveFacialCheckIn($data);
                $this->db->insert("login_attempts_terminal", $dataAttemptsTerminal);
                $this->db->insert("login_attempts", $dataAttempts);
            }elseif ($last_log_type['log_type'] == "IN") {
                $data['log_type'] = "OUT";
                $timesheetData = array();
                $timesheetData['userid'] = $data['userid'];
                //CONDITION FOR FACIAL
                $d1 = new DateTime($last_log_type['localtimein']);
                $d2 = new DateTime($data['localtimein']);
                if ($d1 > $d2) {
                    $timesheetData['timein'] = $data['localtimein'];
                    $timesheetData['timeout'] = $last_log_type['localtimein'];
                }else{
                    $timesheetData['timein'] = $last_log_type['localtimein'];
                    $timesheetData['timeout'] = $data['localtimein'];
                }
                
                $timesheetData['otype'] = $data['username'];
                $timesheetData['addedby'] = $data['machine_id'];

                $getIDTerminalLog = $this->fingerprint->getTerminalLogID($data['userid'], date("H:i:s",strtotime($last_log_type['localtimein'])));

                // LOGIN ATTEMPTS
                $dataAttemptsTerminal["stamp_out"] = date("H:i:s", substr($posted_data->time, 0, 10));
                $dataAttemptsTerminal['terminal_out'] = $this->facial->getFacialName($posted_data->deviceKey);
                $dataAttemptsTerminal['time_out'] = $posted_data->time;

                // Gate
                $dataAttempts['action'] = "OUT";
                $dataAttempts['status'] = "success";

                $this->db->where("id = '$getIDTerminalLog'");
                $this->db->update('login_attempts_terminal', $dataAttemptsTerminal);
                $this->db->insert("login_attempts", $dataAttempts);

                $this->facial->deleteNoOutFacial($data['userid'], $last_log_type['localtimein']);
                $this->facial->saveFacialCheckIn($data);
                $this->webcheckin->saveCheckInToTimesheet($timesheetData);
                $this->facial->deleteOtherLogsFacial($data['userid']);
            }elseif ($last_log_type['log_type'] == "OUT") {

                // LOGIN ATTEMPTS
                $dataAttemptsTerminal["user_id"] = $employeeId;
                $dataAttemptsTerminal["terminalid"] = $posted_data->deviceKey;
                $dataAttemptsTerminal["fullname"] = $this->fingerprint->getFullNameEmployee($data['userid']);
                $dataAttemptsTerminal["stamp_in"] = date("H:i:s", substr($posted_data->time, 0, 10));
                $dataAttemptsTerminal['terminal_in'] = $this->facial->getFacialName($posted_data->deviceKey);
                $dataAttemptsTerminal['time_in'] = $posted_data->time;

                // GATE
                $dataAttempts['action'] = "IN";
                $dataAttempts['status'] = "success";

                $this->db->insert("login_attempts_terminal", $dataAttemptsTerminal);
                $this->db->insert("login_attempts", $dataAttempts);

                $nout = array();
                $data['log_type'] = "IN";
                $nout['userid'] = $data['userid'];
                $nout['localtimein'] = $data['localtimein'];
                $nout['log_type'] = $data['log_type'];
                $nout['username'] = $data['username'];
                $this->webcheckin->saveNoOut($nout);
                $this->facial->saveFacialCheckIn($data);
            }
        }
    }

    public function FacialAPI_post(){
        $this->load->model("facial");
        $this->load->model("webcheckin");
        $posted_data = file_get_contents("php://input");
        $this->facial->facial_console(array('text' => "FacialLogs".$posted_data));
        $posted_data = json_decode($posted_data);
        $time = $posted_data->time;
        $serial = $posted_data->deviceKey;
        $response = "";
        $data = array();
        $Timelog = $this->facial->Timelog($time);
        $employeeId = $this->facial->getEmpIdFacial($posted_data->personId);
        $webSetup = '';
        $posted_data->employeeid = $employeeId;



        // Disable For Now
        // $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$employeeId' AND `status` = 'active'");
        // if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;
        // if($webSetup){
        //     $posted_data->isvalid = 0;
        // }

        $chekerIfLogExisting = $this->facial->logChecker($posted_data->time, $posted_data->personId);
        if ($chekerIfLogExisting) {
            echo '{"result": 1,"success": True}';
            die;
        }

        if ($Timelog) {      
            echo '{"result": 1,"success": True}';
        }else {
            $this->facial->saveAttendanceLog($posted_data, $posted_data->personId);
            echo '{"result": 1,"success": True}';
        }

        $isStudentChecker = $this->facial->isStudent($employeeId);

        if($isStudentChecker == "employee"){
            if ($posted_data->personId != "STRANGERBABY") {
                $logDate = date("Y-m-d", substr($posted_data->time, 0, 10));
                $last_log_type = $this->facial->getLastLogFacial($employeeId, $logDate);

                $data['localtimein'] = date("Y-m-d H:i:s", substr($posted_data->time, 0, 10));
                $data['userid'] = $employeeId;
                $data['username'] = "Facial";
                $data['machine_id'] = $posted_data->deviceKey;

                if ($last_log_type['log_type'] == "new"){
                    $nout = array();
                    $data['log_type'] = "IN";
                    $nout['userid'] = $employeeId;
                    $nout['localtimein'] = $data['localtimein'];
                    $nout['log_type'] = $data['log_type'];
                    $nout['username'] = $data['username'];

                    $this->webcheckin->saveNoOut($nout);
                    $this->facial->saveFacialCheckIn($data);
                }elseif ($last_log_type['log_type'] == "IN") {
                    $data['log_type'] = "OUT";
                    $timesheetData = array();
                    $timesheetData['userid'] = $data['userid'];

                    //CONDITION FOR FACIAL
                    $d1 = new DateTime($last_log_type['localtimein']);
                    $d2 = new DateTime($data['localtimein']);
                    if ($d1 > $d2) {
                        $timesheetData['timein'] = $data['localtimein'];
                        $timesheetData['timeout'] = $last_log_type['localtimein'];
                    }else{
                        $timesheetData['timein'] = $last_log_type['localtimein'];
                        $timesheetData['timeout'] = $data['localtimein'];
                    }
                    
                    $timesheetData['otype'] = $data['username'];
                    $timesheetData['addedby'] = $data['machine_id'];

                    $this->facial->deleteNoOutFacial($data['userid'], $last_log_type['localtimein']);
                    $this->facial->saveFacialCheckIn($data);
                    $this->webcheckin->saveCheckInToTimesheet($timesheetData);
                    $this->facial->deleteOtherLogsFacial($data['userid'], $logDate);
                }elseif ($last_log_type['log_type'] == "OUT") {
                    $nout = array();
                    $data['log_type'] = "IN";
                    $nout['userid'] = $data['userid'];
                    $nout['localtimein'] = $data['localtimein'];
                    $nout['log_type'] = $data['log_type'];
                    $nout['username'] = $data['username'];
                    $this->webcheckin->saveNoOut($nout);
                    $this->facial->saveFacialCheckIn($data);
                }
            }
        }elseif($isStudentChecker == "student"){
            if ($posted_data->personId != "STRANGERBABY") {
                $logDate = date("Y-m-d", substr($posted_data->time, 0, 10));
                $last_log_type = $this->facial->getLastLogFacialStudent($employeeId, $logDate);
                // echo "<pre>";print_r($last_log_type);die;
                $getStudentParentNumber = $this->facial->getNumberStudentParent($employeeId);

                $data['localtimein'] = date("Y-m-d H:i:s", substr($posted_data->time, 0, 10));
                $data['userid'] = $employeeId;
                $data['username'] = "Facial";
                $data['machine_id'] = $posted_data->deviceKey;

                if ($last_log_type['log_type'] == "new"){
                    $nout = array();
                    $data['log_type'] = "IN";
                    $nout['userid'] = $employeeId;
                    $nout['localtimein'] = $data['localtimein'];
                    $nout['log_type'] = $data['log_type'];
                    $nout['username'] = $data['username'];

                    $this->webcheckin->saveNoOutStudent($nout);
                    $this->facial->saveFacialCheckInStudent($data);

                    if ($getStudentParentNumber != 0) {
                        $msg = $employeeId." has entered through ". $data['machine_id']." at ".$data['localtimein'] ;
                        $this->smsSenderMain($getStudentParentNumber, $msg);
                    }
                }elseif ($last_log_type['log_type'] == "IN") {
                    $data['log_type'] = "OUT";
                    $timesheetData = array();
                    $timesheetData['userid'] = $data['userid'];

                    //CONDITION FOR FACIAL
                    $d1 = new DateTime($last_log_type['localtimein']);
                    $d2 = new DateTime($data['localtimein']);
                    if ($d1 > $d2) {
                        $timesheetData['timein'] = $data['localtimein'];
                        $timesheetData['timeout'] = $last_log_type['localtimein'];
                    }else{
                        $timesheetData['timein'] = $last_log_type['localtimein'];
                        $timesheetData['timeout'] = $data['localtimein'];
                    }
                    
                    $timesheetData['username'] = $data['username'];
                    $timesheetData['mac_add_in'] = $data['machine_id'];
                    
                    if ($getStudentParentNumber != 0) {
                        $msg = $employeeId . " has exited through " . $data['machine_id'] . " at " . $data['localtimein'];
                        $this->smsSenderMain($getStudentParentNumber, $msg);
                    }

                    $this->facial->deleteNoOutFacialStudent($data['userid'], $last_log_type['localtimein']);
                    $this->facial->saveFacialCheckInStudent($data);
                    $this->webcheckin->saveCheckInToTimesheetStudent($timesheetData);
                    $this->facial->deleteOtherLogsFacialStudent($data['userid'], $logDate);
                    
                }elseif ($last_log_type['log_type'] == "OUT") {
                    $nout = array();
                    $data['log_type'] = "IN";
                    $nout['userid'] = $data['userid'];
                    $nout['localtimein'] = $data['localtimein'];
                    $nout['log_type'] = $data['log_type'];
                    $nout['username'] = $data['username'];
                    $this->webcheckin->saveNoOutStudent($nout);
                    $this->facial->saveFacialCheckInStudent($data);
                    if ($getStudentParentNumber != 0) {
                        $msg = $employeeId . " has entered through " . $data['machine_id'] . " at " . $data['localtimein'];
                        $this->smsSenderMain($getStudentParentNumber, $msg);
                    }
                }
            }
        }
    }

    public function callback_post(){
        $this->load->model("facial");
        $posted_data = file_get_contents("php://input");
        // $this->facial->Ttesa($posted_data);
        $posted_data = json_decode($posted_data);
        $Timelog = $this->facial->heartbeat($posted_data);
        $facialData = $this->facial->getDataFacial($posted_data->deviceKey);
        if ($facialData[0]["status"] == "Disconnected") {
            $insertData = array();
            $insertData["status"] = "Reconnected";
            $insertData["deviceKey"] = $facialData[0]["deviceKey"];
            $insertData["name"] = $facialData[0]["deviceName"];
            $this->facial->insertDowntimeFacial($insertData);
            $getAllAdminNumber = $this->facial->adminNumberDowntime();
            $getAllAdminEmail = $this->facial->adminEmailDowntime();
            foreach ($getAllAdminEmail as $key => $value) {
                $emailList .= $value['email'].", ";
            }
            $emailList = substr($emailList, 0, -1);
            foreach ($getAllAdminNumber as $keys => $value) {
                $numberList .= $value['cp_number'].";";
            }
            $this->facial->updateFacialStatus($facialData[0]["deviceKey"], "Reconnected");
            $message = $this->getDisconnectedEmailTemplate($facialData[0]["deviceKey"],$facialData[0]["deviceName"],$facialData[0]["ip"],$facialData[0]["timestamp"], "Disconnected");
            $this->email->sendEmailFacialDowntime($message, $emailList, "Facial Downtime");
        }
        
        echo '{"result": true}';
    }

    public function callbackFR_post(){
        $this->load->model("facial");
        $posted_data = file_get_contents("php://input");
        $this->facial->facial_console(array('text' => $this->input->ip_address()." : ".$posted_data));
        foreach (explode("&",$posted_data) as $key => $value) {
            $newData = explode("=", $value);
            $data->$newData[0] = $newData[1];
        }
        $posted_data = $data;
        $Timelog = $this->facial->heartbeat($posted_data);
        $facialData = $this->facial->getDataFacial($posted_data->deviceKey);
        if ($facialData[0]["status"] == "Disconnected") {
            $insertData = array();
            $insertData["status"] = "Reconnected";
            $insertData["deviceKey"] = $facialData[0]["deviceKey"];
            $insertData["name"] = $facialData[0]["deviceName"];
            $this->facial->insertDowntimeFacial($insertData);
            $getAllAdminNumber = $this->facial->adminNumberDowntime();
            $getAllAdminEmail = $this->facial->adminEmailDowntime();
            foreach ($getAllAdminEmail as $key => $value) {
                $emailList .= $value['email'].", ";
            }
            $emailList = substr($emailList, 0, -1);
            foreach ($getAllAdminNumber as $keys => $value) {
                $numberList .= $value['cp_number'].";";
            }
            $this->facial->updateFacialStatus($facialData[0]["deviceKey"], "Reconnected");
            $message = $this->getDisconnectedEmailTemplate($facialData[0]["deviceKey"],$facialData[0]["deviceName"],$facialData[0]["ip"],$facialData[0]["timestamp"], "Disconnected");
            $this->email->sendEmailFacialDowntime($message, $emailList, "Facial Downtime");
        }
        
        echo '{"result": true}';
    }

    public function callbackRA08_post(){
        $this->load->model("facial");
        $posted_data = file_get_contents("php://input");
        $this->facial->Ttesa($posted_data);
        foreach (explode("&",$posted_data) as $key => $value) {
            $newData = explode("=", $value);
            $data->$newData[0] = $newData[1];
        }
        $posted_data = $data;
        $Timelog = $this->facial->heartbeat($posted_data);
        $facialData = $this->facial->getDataFacial($posted_data->deviceKey);
        if ($facialData[0]["status"] == "Disconnected") {
            $insertData = array();
            $insertData["status"] = "Reconnected";
            $insertData["deviceKey"] = $facialData[0]["deviceKey"];
            $insertData["name"] = $facialData[0]["deviceName"];
            $this->facial->insertDowntimeFacial($insertData);
            $getAllAdminNumber = $this->facial->adminNumberDowntime();
            $getAllAdminEmail = $this->facial->adminEmailDowntime();
            foreach ($getAllAdminEmail as $key => $value) {
                $emailList .= $value['email'].", ";
            }
            $emailList = substr($emailList, 0, -1);
            foreach ($getAllAdminNumber as $keys => $value) {
                $numberList .= $value['cp_number'].";";
            }
            $this->facial->updateFacialStatus($facialData[0]["deviceKey"], "Reconnected");
            $message = $this->getDisconnectedEmailTemplate($facialData[0]["deviceKey"],$facialData[0]["deviceName"],$facialData[0]["ip"],$facialData[0]["timestamp"], "Disconnected");
            $this->email->sendEmailFacialDowntime($message, $emailList, "Facial Downtime");
        }
        
        echo '{"result": true}';
    }

    public function facialChecker_get(){
        $this->load->model("facial");
        $this->load->model("email");
        $today = date('Y-m-d H:i:s');
        $allFacial = $this->facial->facialDeviceDownChecker();

        //Check All facial Device
        foreach ($allFacial as $rows => $val) {

            $numberList = "";
            $emailList = "";
            $start_date = new DateTime(date($today));

            $since_start = $start_date->diff(new DateTime(date($val['timestamp'])));
            $minutes = ($since_start->days * 24 * 60) + ($since_start->h * 60) + $since_start->i;
            // $this->facial->facial_console(arr);
            // $this->facial->facial_console(array('text' => "CronJob"));
            // echo "<pre>";print_r($minutes);die;
            if ($minutes >= 6 && $val['status'] == "Connected") {
                $getAllAdminNumber = $this->facial->adminNumberDowntime();
                $getAllAdminEmail = $this->facial->adminEmailDowntime();
                foreach ($getAllAdminEmail as $key => $value) {
                    $emailList .= $value['email'].", ";
                }
                $emailList = substr($emailList, 0, -1);
                foreach ($getAllAdminNumber as $keys => $value) {
                    $numberList .= $value['cp_number'].";";
                }

                $insertData = array();
                $insertData["status"] = "Disconnected";
                $insertData["deviceKey"] = $val["deviceKey"];
                $insertData["name"] = $val["deviceName"];
                $insertData["school"] = "Poveda";
                $insertData["campus"] = "Poveda";
                $insertData["timestamp"] = $val['timestamp'];
                
                $this->facial->insertDowntimeFacial($insertData);
                $this->facial->updateFacialStatus($val["deviceKey"], "Disconnected");
                
                $message = $this->getDisconnectedEmailTemplate($val["deviceKey"],$val["deviceName"],$val["ip"],$val["timestamp"], "Disconnected");
                $email = $this->email->sendEmailFacialDowntime($message, $emailList, "Facial Downtime");
                // if ($email == "success") {
                    
                // }
                // $sms = "Facial Device Disconnected\nDevice Key: ".$val["deviceKey"]."\nDevice Name: ".$val["deviceName"]."\nIP Address: ".$val["ip"]."\nLast Log: ".$val["timestamp"];
                // // $sms = nl2br(str_replace(' ', '%20', $sms));
                // $sms = urlencode($sms);

                // $ch = curl_init();

                // curl_setopt($ch, CURLOPT_URL,"http://122.54.191.90:8085/goip_send_sms.html?username=root&password=root&port=2&recipients=".$numberList."&sms=".$sms);

                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // $server_output = curl_exec($ch);

                // curl_close ($ch);

            }
        }
        echo "Success";
        
    }

    public function getDisconnectedEmailTemplate($deviceKey, $deviceName, $ip, $timestamp, $status){
        $data["deviceKey"] = $deviceKey;
        $data["deviceName"] = $deviceName;
        $data["ip"] = $ip;
        $data["timestamp"] = $timestamp;
        $data["status"] = $status;
        return $this->load->view("email/facial_disconnection", $data, TRUE);
    }

    public function sendTask_post(){
        $this->load->model("facial");
        $posted_data = file_get_contents("php://input");
        $posted_data = json_decode($posted_data);
        $serial = $posted_data->deviceKey;
        $task = $this->facial->checkTask($serial);
        if (count($task) > 0) {
            $this->facial->facial_console(array('text' => '{"taskNo":"'.$task[0]->id.'","interfaceName":"'.$task[0]->interface.'","result":true,'.$task[0]->task.'}'));
            echo '{"taskNo":"'.$task[0]->id.'","interfaceName":"'.$task[0]->interface.'","result":true,'.$task[0]->task.'}';
        }else{
            echo '{"result": false}';
        }
        
    }

    public function facialFeature_post(){
        $this->load->model("facial");
        $posted_data = file_get_contents("php://input");
        $this->facial->facial_console(array('text' => $this->input->ip_address()." : ".$posted_data));
        foreach (explode("&",$posted_data) as $key => $value) {
            $newData = explode("=", $value);
            $data->$newData[0] = urldecode($newData[1]);
        }
        // echo "<pre>";print_r($data);die;
        $posted_data = $data;
        $PersonId = $posted_data->personId;
        $FaceId = $posted_data->faceId;
        $DeviceKey = $posted_data->deviceKey;
        $base64 = $posted_data->imgBase64;

        $insertData = array(
            'personID' => $PersonId,
            'DeviceKey' => $DeviceKey,
            'FaceID' => $FaceId,
            'image' => $base64
        );

        $faceInterface = $this->facial->saveImageFaceID($insertData);
        $this->facial->updateFacialDataStatusRA($PersonId, $DeviceKey, "Success");

        echo json_encode($posted_data);
    }

    public function TaskResult_post(){
        $this->load->model("facial");
        $this->load->model("webcheckin");
        $posted_data = file_get_contents("php://input");
        $this->facial->facial_console(array('text' => "taskResult".$posted_data));
        // echo "<pre>";print_r($posted_data);die;
        $posted_data = str_replace('result":"{"','result":{"',stripslashes($posted_data));
        $posted_data = str_replace('","task',',"task',$posted_data);
        $posted_data = json_decode($posted_data);
        
        $dataExtract = $posted_data->result;
        $statusDesc = ($dataExtract->success)?"Success":"Error";
        $deviceKey = $posted_data->deviceKey;
        $status = $dataExtract->success;
        $msg = (isset($dataExtract->msg))? $dataExtract->msg:"none";
        $taskNo = $posted_data->taskNo;
        $insertData = array(
            'deviceKey' => $deviceKey,
            'taskNo' => $taskNo,
            'result' => $statusDesc,
            'msg' => $msg
        );

        $this->facial->insertTaskResult($insertData);
        $taskInterface = $this->facial->getTaskInterface($taskNo);
        $taskInfo = $this->facial->getTaskInfo($taskNo);
        $json = json_decode("{".$taskInfo."}");
        

        if ($taskInterface == "face/create" || $taskInterface == "face/update") {
            
            if (!isset($dataExtract->data)) {
                $dataExtract->data = $this->facial->getFacialIDTaskNo($taskNo);
            }

            $personID = $this->facial->getPersonID($dataExtract->data, $deviceKey);
            if ($statusDesc == "Error") {
                $msg = explode(",",str_replace("",'FaceException',$msg));
                $msg = str_replace("expDesc=","Error: ",$msg[1]);
                $statusDesc = $msg;
            }
            // echo"<pre>";print_r($statusDesc);die;
            $this->facial->updateFacialDataStatus($personID, $deviceKey, $statusDesc, $dataExtract->data);
            // echo "<pre>";print_r($this->db->last_query());die;

        }elseif($taskInterface == "findRecords"){
            $record = $posted_data->result->data->records;
            $dateFrom = date("Y-m-d", strtotime($json->startTime)); 
            $dateTo = date("Y-m-d", strtotime($json->endTime));
            foreach ($record as $key => $value) {
                $chekerIfLogExisting = $this->facial->logChecker($value->time, $value->personId);
                // echo "<pre>";print_r($value);die;
                if (!$chekerIfLogExisting) {
                    $time = $value->time;
                    $serial = $deviceKey;
                    $response = "";
                    $data = array();
                    $Timelog = $this->facial->Timelog($time);
                    $employeeId = $this->facial->getEmpIdFacial($value->personId);
                    $webSetup = '';
                    $value->employeeid = $employeeId;
                    $value->deviceKey = $deviceKey;

                    $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$employeeId' AND `status` = 'active'");
                    if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;
                    if($webSetup){
                        $value->isvalid = 0;
                    }
                    $this->facial->saveAttendanceLog($value, $value->personId);

                    $this->facial->reprocessFacialLogs($dateFrom, $dateTo, $employeeId);
                }
            }    
        }
        if ($status == "true") {
            $this->facial->updateTaskResult($taskNo,"Completed");
            // $this->facial->deleteTaskResult($deviceKey);
            echo '{"result": true}';
        }else{
            echo '{"result": true}';
            // $this->facial->updateTaskResult($taskNo,"Error");
        } 
    }

    public function reprocessFacialLogs_post(){
        $this->load->model("facial");
        $data = $this->input->post();
        $dateFrom = $data['from'];
        $dateTo = $data['to'];
        $empid = $data['empid'];
        $process = $this->facial->reprocessFacialLogs($dateFrom, $dateTo, $empid);
        echo 'processed:'.$process;
    }

    public function test_post(){
        $posted_data = file_get_contents("php://input");
        $posted_data = json_decode($posted_data);
        $Timelog = $this->facial->Ttesa($posted_data);
        echo '{"result": true}';
    }

    public function timeCheck_get(){
        $today = date('Y-m-d H:i:s');
        $start_date = new DateTime(date($today));

        $since_start = $start_date->diff(new DateTime(date($this->facial->getServerTime())));
        echo "<pre>";print_r($today);
        echo "<pre>";print_r($this->facial->getServerTime());
    }

    public function recheckWebcheckinEmpStatus_get(){
        $this->load->model("webcheckin");
        $this->webcheckin->checkStatusOfEmployee();
        echo 'done';
    }

    public function testFacial_post(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://192.168.2.152:8090/findRecords?pass=12345678&personId=-1&length=-1&index=0&startTime=2021-03-04 01:00:00&endTime=2022-07-06 11:59:00&model=0',
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
        echo $response;
    }

    function smsSenderMain($number, $msg)
    {

        $sms = urlencode($msg);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://122.54.191.90:8085/goip_send_sms.html?username=root&password=root&port=2&recipients=" . $number . "&sms=" . $sms);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
        return "success";
    }

    public function FacialAPITest_post(){
        $this->load->model("facial");
        $this->load->model("webcheckin");
        $posted_data = file_get_contents("php://input");
        $this->facial->facial_console(array('text' => $posted_data));
        $posted_data = json_decode($posted_data);
        $time = $posted_data->time;
        $serial = $posted_data->deviceKey;
        $response = "";
        $data = array();
        $Timelog = $this->facial->Timelog($time);
        $employeeId = $this->facial->getEmpIdFacial($posted_data->personId);
        $webSetup = '';
        $posted_data->employeeid = $employeeId;

        $this->facial->saveAttendanceLogTest($posted_data, $posted_data->personId);
    }

    public function getConfigfawhgwaioghawgnkawmgnogwhnawion_get(){
        $config = array();
        $config['server'] = $this->db->hostname;
        $config['user'] = $this->db->username;
        $config['pass'] = $this->db->password;
        $config['database'] = $this->db->database;
        echo json_encode($config);
    }
}