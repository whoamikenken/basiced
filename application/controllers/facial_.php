<?php 

set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set("memory_limit",-1);
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class facial_ extends CI_Controller {

	/**
	 * Loads setup model everytime this class is accessed.a
	 */
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		$this->load->model('facial');
	}

    function loadFacialMaster(){
        $data['records'] = $this->facial->facialMasterSetup();
        $data['today'] = $this->facial->getServerTime();
        $this->load->view("facial/facial_master_table", $data);
    }

    function loadFacialLogTable(){
        $toks = $this->input->post("toks");
        $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));
        $this->webcheckin->checkStatusOfEmployee();
        $employees = $this->webcheckin->getWebSetupData($data['status'],$data['type'],$data['from'],$data['to'],$data['employeeFilter'],$data['deptid'],$data['office']);
        $data['employee'] = $employees;
        $this->load->view("web_check/web_setup_table", $data);
    }

    function loadFacialSetup(){
       
        $data['records'] = $this->facial->facialMasterSetup();
        $data['today'] = $this->facial->getServerTime();
        if(count($data["records"]) > 0) $data["records"] = Globals::result_XHEP($data["records"]);
        $this->load->view("facial/facial_setup_table", $data);

    }

    function loadFacialSetupLocal(){
        $data['records'] = $this->facial->facialMasterSetup();
        $data['today'] = $this->facial->getServerTime();
        if(count($data["records"]) > 0) $data["records"] = Globals::result_XHEP($data["records"]);
        $this->load->view("facial/facial_setup_table_offline", $data);
    }

    function manageFacialLogs(){
        $code = $this->input->post('code');
        $data = array();
        $facialData = $this->facial->getDataFacial($code);
        foreach($facialData as $value){
            $data['deviceKey'] = $value['deviceKey']; 
            $data['deviceName'] = $value['deviceName']; 
        }
        $data["code"] = $code;
        $this->load->view('facial/manage_facial_device_logs', $data);
    }

    function manageFacialLogsLocal(){
        $code = $this->input->post('code');
        $data = array();
        $facialData = $this->facial->getDataFacial($code);
        foreach($facialData as $value){
            $data['deviceKey'] = $value['deviceKey']; 
            $data['deviceName'] = $value['deviceName']; 
            $data['ip'] = $value['ip']; 
        }
        $data["code"] = $code;
        $this->load->view('facial/manage_facial_device_logs_local', $data);
    }

    function loadEmpPermissionFacialMaster(){
    	$this->load->model('setup');
    	$option = "<option value='All'>ALL EMPLOYEE</option>";
    	$deviceKey = $this->input->post('deviceKey');
    	$emp_list = $this->facial->getUsers();
		$emp = $this->facial->loadEmpWithPermission($deviceKey);
		foreach($emp_list as $emp_data){
			$selected = "";
			foreach (explode(",", $emp[0]['permission']) as $emp_id) {
				if ($emp_id == $emp_data["id"]) $selected = "selected";
			}
			$option .= "<option value='".$emp_data["id"]."' ".$selected." >".$emp_data["username"]."</option>";
		}
		echo $option;
	}

	function savePermissionMaster(){
        $emp = $this->input->post('emp');
        $deviceKey = $this->input->post('deviceKey');
        $name = $this->input->post('name');
        $save_data = $this->facial->savePermissionToDevice($emp,$deviceKey,$name);
        if($save_data) echo "success";
        else echo "error";
    }

    function manageFacialLocal(){
        $code = $this->input->post('code');
        $data = array();
        $facialData = $this->facial->getDataFacial($code);
        foreach($facialData as $value){
            $value = Globals::_array_XHEP($value);
            $data['deviceKey'] = $value['deviceKey']; 
            $data['name'] = $value['deviceName']; 
            $data['recog'] = $value['recog']; 
            $data['image'] = $value['image'];
            $data['ip'] = $value['ip']; 
            $data['filetype'] = $value['filetype'];
            $data['recogDistance'] = $value['recogDistance']; 
            $data['recogScore'] = $value['recogScore'];
            $data['recogInterval'] = $value['recogInterval'];
            $data['campusid'] = $value['campusid'];
        }
        $this->load->view('facial/manage_facial_device_local', $data);
    }

    function manageFacial(){
        $code = $this->input->post('code');
        $data = array();
        $facialData = $this->facial->getDataFacial($code);
	    foreach($facialData as $value){
            $value = Globals::_array_XHEP($value);
	        $data['deviceKey'] = $value['deviceKey']; 
	        $data['name'] = $value['deviceName']; 
	        $data['recog'] = $value['recog']; 
	        $data['image'] = $value['image'];
            $data['ip'] = $value['ip']; 
	        $data['filetype'] = $value['filetype'];
            $data['recogDistance'] = $value['recogDistance']; 
            $data['recogScore'] = $value['recogScore'];
            $data['recogInterval'] = $value['recogInterval'];
            $data['campusid'] = $value['campusid'];
	    }
	    $this->load->view('facial/manage_facial_device', $data);
    }

    function saveFacialSetup(){
    	$file = $filename = $filetype = "";
        if(isset($_FILES['file']['name'])){
            $filename = basename($_FILES['file']['name']);
            $file = file_get_contents($_FILES['file']['tmp_name'], $filename);
            $file = base64_encode($file);
            $filetype = $_FILES['file']['type'];
        }
        $name = $this->input->post('name');
        $recog = $this->input->post('recog');
        $recogDistance = $this->input->post('recogDistance');
        $recogScore = $this->input->post('recogScore');
        $recogInterval = $this->input->post('recogInterval');
        $campusid = $this->input->post('campusid');

        $save_data = $this->facial->saveFacialSetup($filetype, $file, $name, $recog, $this->input->post('serial_number'), $recogScore, $recogDistance, $campusid, $recogInterval);
        if($save_data) echo json_encode(array("err_code"=>"1", "msg"=>"updated", "base64"=> $file));
        else echo json_encode(array("err_code"=>"2","msg"=>"error"));
    }

    function saveFacialSetupFR(){
        $file = $filename = $filetype = "";
        if(isset($_FILES['file']['name'])){
            $filename = basename($_FILES['file']['name']);
            $file = file_get_contents($_FILES['file']['tmp_name'], $filename);
            $file = base64_encode($file);
            $filetype = $_FILES['file']['type'];
        }
        // echo "<pre>";print_r($file);die;
        $name = $this->input->post('name');
        $recog = $this->input->post('recog');
        $recogDistance = $this->input->post('recogDistance');
        $recogScore = $this->input->post('recogScore');
        $recogInterval = $this->input->post('recogInterval');
        $serial_number = $this->input->post('serial_number');
        $campusid = $this->input->post('campusid');
        $mask = $this->input->post('mask');
        $mask_dialogue = $this->input->post('mask_dialogue');
        $video_link = $this->input->post('video_link');

        $maskOpen = ($mask == "Enable")? 1:2;

        // Adds Add
        $payloadAddAds = 'deviceKey='.$serial_number.'&secret=12345678&adId=1&adType=2&adSort=1&adUrl='.$video_link;
        $urlAdsAd = 'api/advertising/add';
        // Ads Config
        $payloadAdsConfig = 'deviceKey='.$serial_number.'&secret=12345678&isOpen=1&splitScreen=1&type=2&showTime=5&showClock=0';
        $urlAdsConfig = 'api/advertising/config';
        // Logo 
        $payloadLogo = "deviceKey=".$serial_number."&secret=12345678&imgBase64=".urlencode($file);
        $urlLogo = 'api/device/setLogo';
        // mask 
        $payloadMask = "deviceKey=".$serial_number."&secret=12345678&isMaskOpen=".$maskOpen."&isVoiceOpen=".$maskOpen."&voiceContext=".$mask_dialogue;
        $urlMask = 'api/device/configMask';
        // Configuration 
        $payloadDeviceConfig = "deviceKey=".$serial_number."&secret=12345678&comModContent=".$recog."&comModType=100&companyName=".$name."&delayTimeForCloseDoor=500&displayModContent=".$recog."&displayModType=100&identifyDistance=".$recogDistance."&saveIdentifyTime=".$recogInterval."&identifyScores=".$recogScore."&multiplayerDetection=1&recRank=2&recStrangerTimesThreshold=15&recStrangerType=1&ttsModContent=".$recog."&ttsModStrangerContent=You are not registered&ttsModStrangerType=100&ttsModType=100&wg=%23WG%20%7Bid%7D%20%23&whitelist=1&saveIdentifyMode=1&onLightStartTime=0&onLightEndTime=0";
        $urlDeviceConfig = 'api/device/config';

        $this->facial->facialCommand($payloadLogo, $urlLogo);
        sleep(1);
        $this->facial->facialCommand($payloadAddAds, $urlAdsAd);
        sleep(1);
        $this->facial->facialCommand($payloadAdsConfig, $urlAdsConfig);
        sleep(25);
        $this->facial->facialCommand($payloadMask, $urlMask);
        sleep(25);
        $this->facial->facialCommand($payloadDeviceConfig, $urlDeviceConfig);

        $save_data = $this->facial->saveFacialSetup($filetype, $file, $name, $recog, $this->input->post('serial_number'), $recogScore, $recogDistance, $campusid, $recogInterval, $mask, $mask_dialogue, $video_link);
        if($save_data) echo json_encode(array("err_code"=>"1", "msg"=>"updated", "base64"=> $file));
        else echo json_encode(array("err_code"=>"2","msg"=>"error"));
    }

    function saveTaskToDevice(){
    	$deviceKey = $this->input->post('serial_number');
    	$interface = $this->input->post('interface');
    	$task = $this->input->post('task');
        $save_data = $this->facial->saveTaskToDevice($deviceKey, $interface, $task);
        if($save_data) echo json_encode(array("err_code"=>"1", "msg"=>"success"));
    }

    function clearFacialTask(){
        $deviceKey = $this->input->post('deviceKey');  
        $save_data = $this->facial->deleteFacialTaskDevice($deviceKey);
        echo $save_data;
    }

    function loadFacialDevice(){
        $data['deviceKey'] = $this->input->post('code');
        $data['ip'] = $this->input->post('ip');
        $data['records'] = $this->facial->facialDevicePerson($this->input->post('code'));
        $this->load->view("facial/facial_device_person_table", $data);
    }

    function loadFacialDeviceLogsLocal(){
        $data['deviceKey'] = $this->input->post('code');
        $data['ip'] = $this->input->post('ip');
        $this->load->view("facial/facial_device_logs_local", $data);
    }

    function syncEmployee(){
        $this->load->model('extensions');
        $this->load->model('utils');
        $empList = "";
        foreach (explode(",", $this->input->post("employeeList")) as $key => $value) {
           $empList .= "'".$value."',";
        }

        if ($this->input->post("sync") == "all") {
            $empList = "''";
        }else{
            $empList = substr($empList, 0, -1);
        }

        if ($empList != "'',") {
            $emp = $this->facial->getActiveEmployeesForFacial($this->input->post("status"), "", "", "", $empList);
            $serial_number = $this->input->post("code");
            $success = $error = 0;
            foreach ($emp as $value) {
                $personChecker = $this->facial->checkFacialPerson($value['employeeid'], $serial_number);
                $filetype = $file = $name = $card = $empid = "";
                
                $name = $value["fname"]." ".$value["lname"];
                $card = $value["employeecode"];
                $empid = $value["employeeid"];
                $hasPhoto = 0;
                $file ="";
                $personID = bin2hex($empid);
                $faceId = bin2hex($empid."face1");

                $employee_elfinder_file = $this->utils->getEmployeePhotoElfinder($value['employeeid']);
                if(count($employee_elfinder_file) > 0){
                    foreach ($employee_elfinder_file as $key => $row) {
                        $hasPhoto++;
                        $file = base64_encode($row->content);
                        $filetype = $row->mime;
                    }
                }else{
                    $employee_photo = $this->utils->getEmployeePhoto($value['employeeid']); 
                    if($employee_photo->num_rows() > 0){
                        $hasPhoto++;
                        $imageData = json_decode(json_encode($employee_photo->result()), true);
                        $file = $imageData[0]["file"];
                        $filenameImage = explode(".", $imageData[0]["filename"]);
                        $filetype = substr($imageData[0]["filename"], -7);
                    }
                }
                

                if ($personChecker == "none") {
                    
                    if ($hasPhoto != 0) {

                        $insertPerson = $this->facial->savePerson($faceId, $name, $card, $empid, $serial_number, $personID);
                        if ($insertPerson) {
                            // CREATE PERSON
                            $createPersonTask = '"pass":"12345678","person":{"id":"'.$personID.'","idcardNum":"'.$card.'","name":"'.$name.'"}';
                            $this->facial->saveTaskToDevice($serial_number, "person/create", $createPersonTask);

                            // CREATE FACE TASK
                            $createFaceTask = '"pass":"12345678","personId":"'.$personID.'","faceId":"'.$faceId.'","imgBase64":"'.$file.'","isEasyWay":true';
                            $this->facial->saveTaskToDevice($serial_number, "face/create", $createFaceTask);
                            $insertData = array(
                                'personID' => $personID,
                                'DeviceKey' => $serial_number,
                                'FaceID' => $faceId,
                                'image' => $file,
                                'mime' => $filetype
                            );
                            $this->facial->saveImageFaceID($insertData);
                        }

                        $success++;
                    }else{
                        $error++;
                    }
                }else{

                    if ($hasPhoto != 0) {
                        
                        $existingFaceID = $this->facial->checkExistingFaceIDImage($faceId, $personID, $serial_number);
                        $createFaceupdateTask = '"pass":"12345678","personId":"'.$personID.'","faceId":"'.$faceId.'","imgBase64":"'.$file.'","isEasyWay":true';

                        if ($existingFaceID) {
                            // UPDATE FACE TASK
                            $this->facial->saveTaskToDevice($serial_number, "face/update", $createFaceupdateTask);
                            $this->facial->updateImageFaceID($faceId, $personID, $serial_number, $file);
                        }else{
                            $insertData = array(
                                'personID' => $personID,
                                'DeviceKey' => $serial_number,
                                'FaceID' => $faceId,
                                'image' => $file,
                                'mime' => $filetype
                            );
                            $this->facial->saveTaskToDevice($serial_number, "face/create", $createFaceupdateTask);
                            $this->facial->saveImageFaceID($insertData);
                        }

                        
                    }
                }
            }

            echo $success;
        }else{
            echo 0;
        }
    }

    function syncEmployeeRA(){
        $this->load->model('extensions');
        $this->load->model('utils');
        $this->load->model('fingerprint');
        $empList = "";
        foreach (explode(",", $this->input->post("employeeList")) as $key => $value) {
           $empList .= "'".$value."',";
        }

        if ($this->input->post("sync") == "all") {
            $empList = "''";
        }else{
            $empList = substr($empList, 0, -1);
        }

        // $empList
        if ($empList != "'',") {
            $emp = $this->facial->getActiveEmployeesForFacial($this->input->post("status"), "", "", "", $empList);
            // echo "<pre>";print_r($this->db->last_query());die;
            $serial_number = $this->input->post("code");
            $success = $error = 0;
            foreach ($emp as $value) {
                $personChecker = $this->facial->checkFacialPerson($value['employeeid'], $serial_number);
                $filetype = $file = $name = $card = $empid = "";
                
                $name = $value["fname"]." ".$value["lname"];
                $card = $value["employeecode"];
                $empid = $value["employeeid"];
                $hasPhoto = 0;
                $file ="";
                $personID = bin2hex($empid);
                $faceId = bin2hex($empid."face1");

                $employee_elfinder_file = $this->utils->getEmployeePhotoElfinder($value['employeeid']);
                if(count($employee_elfinder_file) > 0){
                    foreach ($employee_elfinder_file as $key => $row) {
                        $hasPhoto++;
                        $file = base64_encode($row->content);
                        $filetype = $row->mime;
                    }
                }else{
                    $employee_photo = $this->utils->getEmployeePhoto($value['employeeid']); 
                    if($employee_photo->num_rows() > 0){
                        $hasPhoto++;
                        $imageData = json_decode(json_encode($employee_photo->result()), true);
                        $file = $imageData[0]["file"];
                        $filenameImage = explode(".", $imageData[0]["filename"]);
                        $filetype = substr($imageData[0]["filename"], -7);
                    }
                }   
                

                if ($personChecker == "none") {
                    
                    if ($hasPhoto != 0) {

                        $payloadPerson = 'deviceKey='.$serial_number.'&secret=12345678&id='.$personID.'&name='.$name.'&idcardNum='.$card.'&expireTime=&blacklist=&vaccination=&vaccinationTime=&remark=';
                        $urlPerson = 'api/person/add';

                        $responsePerson = $this->facial->facialCommand($payloadPerson, $urlPerson);
                        if($responsePerson){
                            $insertPerson = $this->facial->savePerson($faceId, $name, $card, $empid, $serial_number, $personID);
                        }

                        $payloadFace = 'deviceKey='.$serial_number.'&secret=12345678&personId='.$personID.'&faceId='.$faceId.'&imgBase64='.urlencode($file);
                        $urlFace = 'api/face/add';
                        
                        $responseFace = $this->facial->facialCommand($payloadFace, $urlFace);

                        if($responseFace){
                            $this->facial->updateFacialDataStatus($personID, $serial_number, "Success", $faceId);

                            $insertData = array(
                                'personID' => $personID,
                                'DeviceKey' => $serial_number,
                                'FaceID' => $faceId,
                                'image' => $file,
                                'mime' => $filetype
                            );
                            $this->facial->saveImageFaceID($insertData);

                        }else{
                            $this->facial->updateFacialDataStatus($personID, $serial_number, "Error", $faceId);
                        }

                        $getFingerPrint = $this->fingerprint->getEmployeeBioPic($empid);
                        if (count($getFingerPrint) > 0) {
                            foreach ($getFingerPrint as $ky => $vl) {
                                $payloadFace = 'deviceKey='.$serial_number.'&secret=12345678&data=[{"fingerId":"'.$vl["fingerId"].'","feature":"'.urlencode($vl["feature"]).'","fingerNum":'.$vl["fingerNum"].',"personId":"'.$personID.'"}]';
                                $urlFace = 'api/finger/add';

                                $this->facial->facialCommand($payloadFace, $urlFace);
                            }
                        }
                        $success++;
                    }else{
                        $payloadPerson = 'deviceKey='.$serial_number.'&secret=12345678&id='.$personID.'&name='.$name.'&idcardNum='.$card.'&expireTime=&blacklist=&vaccination=&vaccinationTime=&remark=';
                        $urlPerson = 'api/person/add';

                        $responsePerson = $this->facial->facialCommand($payloadPerson, $urlPerson);
                        if($responsePerson){
                            $insertPerson = $this->facial->savePerson($faceId, $name, $card, $empid, $serial_number, $personID);
                            $success++;
                        }else{
                           $error++; 
                        } 
                    }
                }else{

                    if ($hasPhoto != 0) {
                        
                        // CREATE FACE TASK
                        $payloadFace = 'deviceKey='.$serial_number.'&secret=12345678&personId='.$personID.'&faceId='.$faceId.'&imgBase64='.urlencode($file);
                        $urlFace = 'api/face/add';

                        $responseFace = $this->facial->facialCommand($payloadFace, $urlFace);
                        if($responseFace){
                            $this->facial->updateFacialDataStatus($personID, $serial_number, "Success", $faceId);

                            $insertData = array(
                                'personID' => $personID,
                                'DeviceKey' => $serial_number,
                                'FaceID' => $faceId,
                                'image' => $file,
                                'mime' => $filetype
                            );
                            $this->facial->saveImageFaceID($insertData);
                        }else{
                            $this->facial->updateFacialDataStatus($personID, $serial_number, "Error", $faceId);
                        }
                    }
                }
            }

            echo $success;
        }else{
            echo 0;
        }
    }

    function syncEmployeeCount(){
        $this->load->model('extensions');
        $seconds = count(explode(",", $this->input->post("employeeList"))) * 0.7865168539325843;
        echo $seconds * 1000;
    }

    function syncEmployeeCountDevice(){
        $this->load->model('extensions');
        $emp = $this->facial->getActiveEmployeesForFacial(1,"","","","''");
        $seconds = count($emp) * 0.7865168539325843;
        echo $seconds * 1000;
    }

    function loadFacialLogsTable(){
        $data['records'] = $this->input->post("data");
        $data['ip'] = $this->input->post("ip");
        $data['deviceKey'] = $this->input->post("deviceKey");
        $data['records'] = json_decode($data['records']);
        $data['records'] = $data['records']->data->records;
        $this->load->view("facial/facial_device_logs_local_table", $data);
    }

    function indeviceRegistrationFR(){
        $serial_number = $this->input->post('serial_number'); 
        $personId = $this->input->post('personId'); 

        $payloadPerson = 'deviceKey='.$serial_number.'&secret=12345678&personId='.$personId;
        $urlPerson = 'api/device/takeImg';

        $this->facial->facialCommand($payloadPerson, $urlPerson);
    
    }

    function loadFacialDevicePersonTable(){
        $toks = $this->input->post("toks");
        $data['deviceKey'] = $toks ? $this->gibberish->decrypt( $this->input->post("code"), $toks ) : $this->input->post("code");
        $type = $toks ? $this->gibberish->decrypt( $this->input->post("type"), $toks ) : $this->input->post("type");
        $deptid = $toks ? $this->gibberish->decrypt( $this->input->post("deptid"), $toks ) : $this->input->post("deptid");
        $office = $toks ? $this->gibberish->decrypt( $this->input->post("office"), $toks ) : $this->input->post("office");
        $status = $toks ? $this->gibberish->decrypt( $this->input->post("status"), $toks ) : $this->input->post("status");
        $empid = $toks ? $this->gibberish->decrypt( $this->input->post("empid"), $toks ) : $this->input->post("empid");
        $data['records'] = $this->facial->facialDevicePerson($data['deviceKey'], $type, $deptid, $office, $status, $empid);
        $facialData = $this->facial->getDataFacial($data['deviceKey']);
        foreach($facialData as $value){
            $data['devicemodel'] = ($value['version'] == "1.41.7.4" || $value['version'] == "1.41.7.0" || $value['version'] == "1.51.0.5")? "FR":"RA"; 
        }
        $this->load->view("facial/facial_person_table", $data);
    }

    function managePerson(){
        $data = array();
        $data['code'] = $this->input->post('code');
        $data['ip'] = $this->input->post('ip');
        $data['action'] = $this->input->post('action');
        $data['empid'] = ($this->input->post('empid'))? $this->input->post('empid'):"";
        // echo "<pre>";print_r($data);die;
        $facialData = $this->facial->getPersonData($this->input->post('empid'), $data['code']);
        // echo "<pre>";print_r($this->db->last_query());die;
        $data['employeeId'] = $data['facial_status1'] = $data['facial_status2'] = $data['facial_status3'] = $data['FaceId1'] = $data['FaceId2'] = $data['FaceId3'] ="";
        foreach($facialData as $value){
            $data['personId'] = $value['personId']; 
            $data['card'] = $value['card']; 
            $data['employeeId'] = $value['employeeid']; 
            $data['name'] = $value['name']; 
            $data['facial_status1'] = $value['facial_status1'];
            $data['facial_status2'] = $value['facial_status2'];
            $data['facial_status3'] = $value['facial_status3'];
            $data['FaceId1'] = $value['FaceId1'];
            $data['FaceId2'] = $value['FaceId2'];
            $data['FaceId3'] = $value['FaceId3'];
        }
        // echo "<pre>";print_r($facialData);die;
        $this->load->view('facial/device_person_manage', $data);
    }

    function loadEmployee(){

        $option = "<option value=''>Please Select Employee</option>";
        $emp_list = $this->facial->getEmployee();
        foreach($emp_list as $emp_data){
            if ($this->input->post('emp') == $emp_data["employeeid"]) {
                $option .= "<option value='".$emp_data["employeeid"]."' selected>".$emp_data["fullname"]."</option>";
            }else{
                $option .= "<option value='".$emp_data["employeeid"]."'>".$emp_data["fullname"]."</option>";
            }
            
        }
        echo $option;
    }

    function loadEmployeeAll(){
        $deviceKey = $this->input->post("deviceKey");
        $option = "<option value='all'>All Employee</option>";
        $emp_list = $this->facial->getEmployeeDevice($deviceKey);
        foreach($emp_list as $emp_data){
            if ($this->input->post('emp') == $emp_data["employeeid"]) {
                $option .= "<option value='".$emp_data["employeeid"]."' selected>".$emp_data["name"]."</option>";
            }else{
                $option .= "<option value='".$emp_data["employeeid"]."'>".$emp_data["name"]."</option>";
            }
            
        }
        echo $option;
    }

    function loadDevicePersonEmployee(){
        $deviceKey = $this->input->post("deviceKey");
        $option = "<option value='all'>All Employee</option>";
        $emp_list = $this->facial->getEmployeeDevice($deviceKey);
        foreach($emp_list as $emp_data){
            if ($this->input->post('emp') == $emp_data["personId"]) {
                $option .= "<option value='".$emp_data["personId"]."' selected>".$emp_data["name"]."</option>";
            }else{
                $option .= "<option value='".$emp_data["personId"]."'>".$emp_data["name"]."</option>";
            }
            
        }
        echo $option;
    }

    function savePerson(){
        $name = $this->input->post('name');
        $empid = $this->input->post('empid');
        $card = $this->facial->getEmpRFID($empid);
        $personID = bin2hex($empid);
        $serial_number = $this->input->post('serial_number');
        $checker = $this->facial->checkFacialPerson($empid,$serial_number);
        // echo"<pre>";print_r($checker);die;
        if ($checker != "none" && $this->input->post("action") != "Edit") {
            echo json_encode(array("err_code"=>"2","msg"=>"existing"));
        }
        if ($this->input->post('action') == "Added") {
            if ($this->facial->getFacialModel($serial_number) == "RA") {
                $save_data = $this->facial->savePerson("", $name, $card, $empid, $serial_number, $personID);
                // CREATE PERSON
                $createPersonTask = '"pass":"12345678","person":{"id":"'.$personID.'","idcardNum":"'.$card.'","name":"'.$name.'"}';
                $this->facial->saveTaskToDevice($serial_number, "person/create", $createPersonTask);
            }else{
                $payloadPerson = 'deviceKey='.$serial_number.'&secret=12345678&id='.$personID.'&name='.$name.'&idcardNum='.$card.'&expireTime=&blacklist=&vaccination=&vaccinationTime=&remark=';
                $urlPerson = 'api/person/add';

                $faceId = bin2hex($empid."face1");

                $responsePerson = $this->facial->facialCommand($payloadPerson, $urlPerson);
                if($responsePerson){
                    $save_data = $this->facial->savePerson($faceId, $name, $card, $empid, $serial_number, $personID);
                }
            }
            
        }else{
            $save_data = $this->facial->saveUpdatePerson("", "", $name, $card, $empid, $serial_number, $personID);
            $createPersonTask = '"pass":"12345678","person":{"id":"'.$personID.'","idcardNum":"'.$card.'","name":"'.$name.'"}';
            $this->facial->saveTaskToDevice($serial_number, "person/update", $createPersonTask);
        }
        $file1 = $filename1 = $filetype1 = "";
        $file2 = $filename2 = $filetype2 = "";
        $file3 = $filename3 = $filetype3 = "";

        
        if(isset($_FILES['file1']['name'])){
            $filename1 = basename($_FILES['file1']['name']);
            $file1 = file_get_contents($_FILES['file1']['tmp_name'], $filename1);
            $file1 = base64_encode($file1);
            $filetype1 = $_FILES['file1']['type'];
            $faceId1 = bin2hex($empid."face1");
            $this->facial->updateFaceID($personID, $serial_number, $faceId1);
            
            if ($this->facial->getFacialModel($serial_number) == "RA") {
                $insertData = array(
                    'personID' => $personID,
                    'DeviceKey' => $serial_number,
                    'FaceID' => $faceId1,
                    'image' => $file1,
                    'mime' => $filetype1
                );

                $existingFaceID = $this->facial->checkExistingFaceIDImage($faceId1, $personID, $serial_number);
                $createFaceupdateTask = '"pass":"12345678","personId":"'.$personID.'","faceId":"'.$faceId1.'","imgBase64":"'.$file1.'","isEasyWay":false';
                // CREATE FACE TASK
                if ($existingFaceID) {
                    $this->facial->saveTaskToDevice($serial_number, "face/update", $createFaceupdateTask);
                    $this->facial->updateImageFaceID($faceId1, $personID, $serial_number, $file1);
                }else{
                    $this->facial->saveTaskToDevice($serial_number, "face/create", $createFaceupdateTask);
                    $this->facial->saveImageFaceID($insertData);
                }
            }else{

                $payloadFace = 'deviceKey='.$serial_number.'&secret=12345678&personId='.$personID.'&faceId='.$faceId1.'&imgBase64='.urlencode($file1);
                    $urlFace = 'api/face/add';
                    
                    $responseFace = $this->facial->facialCommand($payloadFace, $urlFace);
                    if ($responseFace) {
                        $this->facial->updateFaceID($personID, $serial_number, $faceId1);
                    }
            }
              
        }

        if(isset($_FILES['file2']['name'])){
            $filename2 = basename($_FILES['file2']['name']);
            $file2 = file_get_contents($_FILES['file2']['tmp_name'], $filename2);
            $file2 = base64_encode($file2);
            $filetype2 = $_FILES['file2']['type'];
            $faceId2 = bin2hex($empid."face2");
            if ($this->facial->getFacialModel($serial_number) == "RA") {
                $this->facial->updateFaceID($personID, $serial_number, $faceId2);
                $insertData = array(
                    'personID' => $personID,
                    'DeviceKey' => $serial_number,
                    'FaceID' => $faceId2,
                    'image' => $file2,
                    'mime' => $filetype2
                );
                $existingFaceID = $this->facial->checkExistingFaceIDImage($faceId2, $personID, $serial_number);
                $createFaceupdateTask = '"pass":"12345678","personId":"'.$personID.'","faceId":"'.$faceId2.'","imgBase64":"'.$file2.'","isEasyWay":false';
                // CREATE FACE TASK
                if ($existingFaceID) {
                    $this->facial->saveTaskToDevice($serial_number, "face/update", $createFaceupdateTask);
                    $this->facial->updateImageFaceID($faceId2, $personID, $serial_number, $file2);
                }else{
                    $this->facial->saveTaskToDevice($serial_number, "face/create", $createFaceupdateTask);
                    $this->facial->saveImageFaceID($insertData);
                }
            }else{
                $payloadFace = 'deviceKey='.$serial_number.'&secret=12345678&personId='.$personID.'&faceId='.$faceId2.'&imgBase64='.urlencode($file2);
                $urlFace = 'api/face/add';
                
                $responseFace = $this->facial->facialCommand($payloadFace, $urlFace);
                if ($responseFace) {
                    $this->facial->updateFaceID($personID, $serial_number, $faceId2);
                }
            }
        }
        if(isset($_FILES['file3']['name'])){
            $filename3 = basename($_FILES['file3']['name']);
            $file3 = file_get_contents($_FILES['file3']['tmp_name'], $filename3);
            $file3 = base64_encode($file3);
            $filetype3 = $_FILES['file3']['type'];
            $faceId3 = bin2hex($empid."face3");
            if ($this->facial->getFacialModel($serial_number) == "RA") {
                $this->facial->updateFaceID($personID, $serial_number, $faceId3);
                $insertData = array(
                    'personID' => $personID,
                    'DeviceKey' => $serial_number,
                    'FaceID' => $faceId3,
                    'image' => $file3,
                    'mime' => $filetype3
                );
                $existingFaceID = $this->facial->checkExistingFaceIDImage($faceId3, $personID, $serial_number);
                $createFaceupdateTask = '"pass":"12345678","personId":"'.$personID.'","faceId":"'.$faceId3.'","imgBase64":"'.$file3.'","isEasyWay":false';
                // CREATE FACE TASK
                if ($existingFaceID) {
                    $this->facial->saveTaskToDevice($serial_number, "face/update", $createFaceupdateTask);
                    $this->facial->updateImageFaceID($faceId3, $personID, $serial_number, $file3);
                }else{
                    $this->facial->saveTaskToDevice($serial_number, "face/create", $createFaceupdateTask);
                    $this->facial->saveImageFaceID($insertData);
                }
            }else{
                $payloadFace = 'deviceKey='.$serial_number.'&secret=12345678&personId='.$personID.'&faceId='.$faceId3.'&imgBase64='.urlencode($file3);
                $urlFace = 'api/face/add';
                
                $responseFace = $this->facial->facialCommand($payloadFace, $urlFace);
                if ($responseFace) {
                    $this->facial->updateFaceID($personID, $serial_number, $faceId3);
                }
            }
        }

        
        if($save_data) echo json_encode(array("err_code"=>"1", "msg"=>"updated"));
        else echo json_encode(array("err_code"=>"2","msg"=>"error"));
    }

    function resetDevicePersonRA(){
        $serial_number = $this->input->post('code'); 
        $payloadPerson = 'deviceKey='.$serial_number.'&secret=12345678&personId=-1';
        $urlPerson = 'api/person/del';

        $facialReset = $this->facial->facialCommand($payloadPerson, $urlPerson);
        if ($facialReset) {
            $this->facial->resetPerson($serial_number);
            echo "success";
        }else{
            echo "error";
        }
    }

    function syncToOtherDevice(){
        $this->load->model("utils");
        $this->load->model("fingerprint");
        $serial_number = $this->input->post('code'); 
        $facialDevices = $this->facial->getAllFRDevices($serial_number);
        $getCurrentEmployeeRegistered = $this->facial->facialDevicePerson($serial_number,"","","","","");
        $success = 0;
        $error = 0;
        $test = 0;
        foreach ($facialDevices as $key => $value) {
            $deviceKey = $value->deviceKey;
            
            foreach ($getCurrentEmployeeRegistered as $row => $val) {

                $personChecker = $this->facial->checkFacialPerson($val->personId, $deviceKey);

                $name = $val->fullname;
                $card = $val->card;
                $empid = $val->employeeid;
                $hasPhoto = 0;
                $file ="";
                $personID = bin2hex($empid);
                $faceId = bin2hex($empid."face1");
                $devicePhoto = 0;
                $photoArray = array();

                // .$val->personId
                $payloadFace = "deviceKey=".$serial_number."&secret=12345678&personId=".$val->personId;
                $urlFaceConfig = 'api/face/find';     
                
                $response = $this->facial->facialCommand($payloadFace, $urlFaceConfig);

                if(count($response) > 0){
                    $photoArray = $response;
                    $devicePhoto++;
                    $hasPhoto++;
                }
                

                if($file == "" && $devicePhoto == 0){
                    $getSaveFaceFeature = $this->facial->getFacialFeature($val->personId);
                    // echo "<pre>";print_r($val->personId);
                    if($getSaveFaceFeature != "noimage"){
                        $hasPhoto++;
                        $file = $getSaveFaceFeature;
                    }
                }

                if($file == "" && $devicePhoto == 0){
                    $employee_elfinder_file = $this->utils->getEmployeePhotoElfinder($val->employeeid);
                    if(count($employee_elfinder_file) > 0){
                        foreach ($employee_elfinder_file as $key => $rw) {
                            $hasPhoto++;
                            $file = base64_encode($rw->content);
                            $filetype = $rw->mime;
                        }
                    }else{
                        $employee_photo = $this->utils->getEmployeePhoto($val->employeeid); 
                        if($employee_photo->num_rows() > 0){
                            $hasPhoto++;
                            $imageData = json_decode(json_encode($employee_photo->result()), true);
                            $file = $imageData[0]["file"];
                            $filenameImage = explode(".", $imageData[0]["filename"]);
                            $filetype = substr($imageData[0]["filename"], -7);
                        }
                    }
                }
                if ($personChecker == "none") {
                    
                    if ($hasPhoto != 0) {

                        $payloadPerson = 'deviceKey='.$deviceKey.'&secret=12345678&id='.$personID.'&name='.$name.'&idcardNum='.$card.'&expireTime=&blacklist=&vaccination=&vaccinationTime=&remark=';
                        $urlPerson = 'api/person/add';

                        $responsePerson = $this->facial->facialCommand($payloadPerson, $urlPerson);
                        if($responsePerson){
                            $insertPerson = $this->facial->savePerson($faceId, $name, $card, $empid, $deviceKey, $personID);
                        }

                        if($devicePhoto == 0){
                            $payloadFace = 'deviceKey='.$deviceKey.'&secret=12345678&personId='.$personID.'&faceId='.$faceId.'&imgBase64='.urlencode($file);
                            $urlFace = 'api/face/add';
                            
                            $responseFace = $this->facial->facialCommand($payloadFace, $urlFace);

                            if($responseFace){
                                $this->facial->updateFacialDataStatus($personID, $deviceKey, "Success", $faceId);
                            }else{
                                $this->facial->updateFacialDataStatus($personID, $deviceKey, "Error", $faceId);
                            }
                        }else{
                            foreach ($photoArray as $key => $value) {
                                $key++;
                                $faceIdReg = $key;
                                $fd = bin2hex($empid."face".$faceIdReg);
                                $payloadFace = 'deviceKey='.$deviceKey.'&secret=12345678&personId='.$personID.'&faceId='.$fd.'&imgBase64='.urlencode($value->imgBase64);
                                $urlFace = 'api/face/add';
                                
                                $responseFace = $this->facial->facialCommand($payloadFace, $urlFace);

                                if($responseFace){
                                    $this->facial->updateFacialDataStatus($personID, $deviceKey, "Success", $fd);
                                }else{
                                    $this->facial->updateFacialDataStatus($personID, $deviceKey, "Error", $fd);
                                }
                            }
                        }

                        $getFingerPrint = $this->fingerprint->getEmployeeBioPic($empid);
                        if (count($getFingerPrint) > 0) {
                            foreach ($getFingerPrint as $ky => $vl) {
                                $payloadFace = 'deviceKey='.$deviceKey.'&secret=12345678&data=[{"fingerId":"'.$vl["fingerId"].'","feature":"'.urlencode($vl["feature"]).'","fingerNum":'.$vl["fingerNum"].',"personId":"'.$personID.'"}]';
                                $urlFace = 'api/finger/add';

                                $this->facial->facialCommand($payloadFace, $urlFace);
                            }
                        }
                        $success++;

                    }else{
                        $payloadPerson = 'deviceKey='.$deviceKey.'&secret=12345678&id='.$personID.'&name='.$name.'&idcardNum='.$card.'&expireTime=&blacklist=&vaccination=&vaccinationTime=&remark=';
                        $urlPerson = 'api/person/add';

                        $responsePerson = $this->facial->facialCommand($payloadPerson, $urlPerson);
                        if($responsePerson){
                            $insertPerson = $this->facial->savePerson($faceId, $name, $card, $empid, $deviceKey, $personID);
                            $success++;
                        }else{
                           $error++; 
                        } 
                    }
                }else{

                    if ($hasPhoto != 0) {
                        
                        // CREATE FACE TASK
                        $payloadFace = 'deviceKey='.$deviceKey.'&secret=12345678&personId='.$personID.'&faceId='.$faceId.'&imgBase64='.urlencode($file);
                        $urlFace = 'api/face/add';

                        $responseFace = $this->facial->facialCommand($payloadFace, $urlFace);
                        if($responseFace){
                            $this->facial->updateFacialDataStatus($personID, $deviceKey, "Success", $faceId);
                        }else{
                            $this->facial->updateFacialDataStatus($personID, $deviceKey, "Error", $faceId);
                        }
                    }
                }
            }
        }
        echo $success;
    }

    function manageFacialFR(){
        $code = $this->input->post('code');
        $data = array();
        $facialData = $this->facial->getDataFacial($code);
        foreach($facialData as $value){
            $value = Globals::_array_XHEP($value);
            $data['deviceKey'] = $value['deviceKey']; 
            $data['name'] = $value['deviceName']; 
            $data['recog'] = $value['recog']; 
            $data['image'] = $value['image'];
            $data['ip'] = $value['ip']; 
            $data['filetype'] = $value['filetype'];
            $data['recogDistance'] = $value['recogDistance']; 
            $data['recogScore'] = $value['recogScore'];
            $data['recogInterval'] = $value['recogInterval'];
            $data['campusid'] = $value['campusid'];
            $data['officeid'] = $value['officeid'];
            $data['mask'] = $value['mask'];
            $data['mask_dialogue'] = $value['mask_dialogue'];
            $data['video_link'] = $value['video_link'];
        }
        $this->load->view('facial/manage_facial_deviceFR', $data);
    }

    function deletePerson(){
        $personId = $this->input->post('personId');
        $serial_number = $this->input->post('serial_number');  
        $save_data = $this->facial->deletePerson($personId, $serial_number);
        echo $save_data;
    }

    function removeDevice(){
        $serial_number = $this->input->post('serial_number');  
        $save_data = $this->facial->deleteDevice($serial_number);
        echo $save_data;
    }

    function resetLogs(){
        $serial_number = $this->input->post('serial_number');  
        $save_data = $this->facial->deleteLogs($serial_number);
        echo $save_data;
    }

    function resetDevice(){
        $serial_number = $this->input->post('serial_number');  
        $this->facial->deleteLogs($serial_number);
        $save_data = $this->facial->resetPerson($serial_number);
        echo $save_data;
    }

    function resetDevicePerson(){
        $serial_number = $this->input->post('code'); 
        $save_data = $this->facial->resetPerson($serial_number);
        echo $save_data;
    }

    function loadFacialDeviceLogs(){
        $data['deviceKey'] = $this->input->post('serial_number');
        $data['ip'] = $this->input->post('ip');
        $this->load->view("facial/facial_logs", $data);
    }

    function getLogs()
    { 
        return $this->facial->getEmployeeLogs(); 
    }

    function getImage(){
        $id = $this->input->post('id');  
        $base64 = $this->facial->logsImage($id);
        echo $base64;
    }

    function loadFacialDeviceStrangers(){
        $data['deviceKey'] = $this->input->post('serial_number');
        $data['ip'] = $this->input->post('ip');
        $this->load->view("facial/facial_logs_strangers", $data);
    }

    function loadFacialDeviceTask(){
        $data['deviceKey'] = $this->input->post('serial_number');
        $this->load->view("facial/facial_task", $data);
    }

    function getLogsStrangers()
    { 
        return $this->facial->getDeviceStrangers(); 
    }

    function getLogsTask()
    { 
        return $this->facial->getDeviceTask(); 
    }

    public function getPersonImage(){
        $content = $mime = "";
        $id = $this->input->post("faceid");
        $deviceKey = $this->input->post("deviceKey");
        $personID = $this->facial->getPersonID($id,$deviceKey);
        $result = $this->facial->getFaceIDImage($id);
        if($result->num_rows() > 0){
            $content = $result->row()->Feature;
            $mime = "image/jpeg";
        }

        $response = array("file" => $content, "mime" => $mime);
        echo json_encode($response);
    }

    function printFacialReport()
    { 
        $this->load->library('PdfCreator_tcpdf');
        $this->load->library('PdfCreator_mpdf');
        $data = $this->input->post();
        $form = $data["form"];
        $this->load->view('facial/' . $form, $data);
    }

    function removeTimesheetData()
    { 
        $data = $this->input->post();
        $this->facial->facialRemoveTimesheet($data['deviceKey'],$data['from'],$data['to']);

        $record = $this->facial->getDataFacialLogs($data['deviceKey'],$data['from'],$data['to']);
        echo json_encode($record);
    }

    public function facialLogsSync(){
        $this->load->model("facial");
        $this->load->model("webcheckin");
        $posted_data = json_decode(json_encode($this->input->post()), FALSE);
        
        $time = $posted_data->time;
        $serial = $posted_data->deviceKey;
        $response = "";
        $data = array();
        $Timelog = $this->facial->Timelog($time);
        $posted_data->datetime = gmdate('Y-m-d H:i:s', substr($time, 0, 10));
        // echo "<pre>";print_r($posted_data);die;
        $employeeId = $this->facial->getEmpIdFacial($posted_data->personId);
        $webSetup = '';
        $posted_data->employeeid = $employeeId;

        $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$employeeId' AND `status` = 'active'");
        if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;
        if($webSetup){
            $posted_data->isvalid = 0;
        }

        
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
                $this->facial->deleteOtherLogsFacial($data['userid']);
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
    }

    public function resyncLogsOffline(){
    $this->load->model("facial");
    $this->load->model("webcheckin");
    $data = $this->input->post();
    $payloadPerson = 'http://'.$data['ip'].':8090/findRecords?pass=12345678&personId=-1&length=-1&index=0&startTime='.$data['from'].' 01:00:00&endTime='.$data['to'].' 11:59:00&model=0';
    $urlPerson = '';


    $record = $this->facial->facialCommandLogs($data['ip'], $data['from'], $data['to']);
    $posted_data = json_decode($record);
    $record = $posted_data->result->data->records;
    
        foreach ($record as $key => $value) {
            $chekerIfLogExisting = $this->facial->logChecker($value->time, $value->personId);

            if ($value->state == 0 && !$chekerIfLogExisting) {
                $imageData = base64_encode(file_get_contents($value->path));
                $time = $value->time;
                $serial = $value->deviceKey;
                $response = "";
                $data = array();
                $Timelog = $this->facial->Timelog($time);
                $employeeId = $this->facial->getEmpIdFacial($value->personId);
                $webSetup = '';
                $value->employeeid = $employeeId;
                $value->base64image = $imageData;

                $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$employeeId' AND `status` = 'active'");
                if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;
                if($webSetup){
                    $value->isvalid = 0;
                }

                if ($Timelog) {      
                    echo '{"result": 1,"success": True}';
                }else {
                    $this->facial->saveAttendanceLog($value, $value->personId);
                    echo '{"result": 1,"success": True}';
                }

                // echo "<pre>";print_r($this->db->last_query());die;
                if ($value->personId != "STRANGERBABY") {
                    $logDate = date("Y-m-d", substr($value->time, 0, 10));
                    $last_log_type = $this->facial->getLastLogFacial($employeeId, $logDate);

                    $data['localtimein'] = date("Y-m-d H:i:s", substr($value->time, 0, 10));
                    $data['userid'] = $employeeId;
                    $data['username'] = "Facial";
                    $data['machine_id'] = $value->deviceKey;

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
                        $this->facial->deleteOtherLogsFacial($data['userid']);
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
            }
        }
    }

    public function syncLogsLocal(){
        $this->load->model("facial");
        $this->load->model("webcheckin");
        $posted_data = file_get_contents("php://input");
        $data = json_decode($posted_data);
        $data = json_decode(json_encode($data), true);
        echo "<pre>";print_r($data);die;
        $chekerIfLogExisting = $this->facial->logChecker($data['time'], $data['personId']);
        
        if (!$chekerIfLogExisting) {
            $imageData = $data['base64image'];
            $time = $data['time'];
            $serial = $data['deviceKey'];
            $response = "";
            $Timelog = $this->facial->Timelog($time);
            $employeeId = $this->facial->getEmpIdFacial($data['personId']);
            $webSetup = '';
            $data['employeeid'] = $employeeId;
            $data['base64image'] = $imageData;

            $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$employeeId' AND `status` = 'active'");
            if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;
            if($webSetup){
                $data['isvalid'] = 0;
            }
            
            if ($Timelog) {      
                echo '{"result": 1,"success": True}';
            }else {
                $this->facial->saveAttendanceLog(json_decode(json_encode($data), FALSE), $data['personId']);
                echo "<pre>";print_r($this->db->last_query());
                echo '{"result": 1,"success": True}';
            }
        }
    }

    public function reprocessFacialLogs()
    {
        $data = $this->input->post();
        $this->facial->reprocessFacialLogs($data['from'], $data['to']);
    }

    public function syncLogsLocalOld(){
    $this->load->model("facial");
    $this->load->model("webcheckin");
    $data = $this->input->post();
    
    echo "<pre>";print_r($data);die;
    $data = json_decode($data);
    $record = $data->result->data->records;
    
    
        foreach ($record as $key => $value) {
            $chekerIfLogExisting = $this->facial->logChecker($value->time, $value->personId);

            if ($value->state == 0 && !$chekerIfLogExisting) {
                $imageData = base64_encode(file_get_contents($value->path));
                $time = $value->time;
                $serial = $value->deviceKey;
                $response = "";
                $data = array();
                $Timelog = $this->facial->Timelog($time);
                $employeeId = $this->facial->getEmpIdFacial($value->personId);
                $webSetup = '';
                $value->employeeid = $employeeId;
                $value->base64image = $imageData;

                $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$employeeId' AND `status` = 'active'");
                if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;
                if($webSetup){
                    $value->isvalid = 0;
                }

                if ($Timelog) {      
                    echo '{"result": 1,"success": True}';
                }else {
                    $this->facial->saveAttendanceLog($value, $value->personId);
                    echo '{"result": 1,"success": True}';
                }

                // echo "<pre>";print_r($this->db->last_query());die;
                if ($value->personId != "STRANGERBABY") {
                    $logDate = date("Y-m-d", substr($value->time, 0, 10));
                    $last_log_type = $this->facial->getLastLogFacial($employeeId, $logDate);

                    $data['localtimein'] = date("Y-m-d H:i:s", substr($value->time, 0, 10));
                    $data['userid'] = $employeeId;
                    $data['username'] = "Facial";
                    $data['machine_id'] = $value->deviceKey;

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
                        $this->facial->deleteOtherLogsFacial($data['userid']);
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
            }
        }
    }

    public function importLogs()
    {
        $data = $this->input->post();

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

            if ($Timelog) {      
                // echo '{"result": 1,"success": True}';
            }else {
                $this->facial->saveAttendanceLog($value, $value->personId);
                // echo '{"result": 1,"success": True}';
            }
        }
    }
    
    public function getEmployeeWithNoLogs()
    {
        $data = $this->input->post();
        $period = $this->facial->getDatesFromRange($data['from'], $data['to']);
        $serial_number = $data['serial_number'];
        $emp = "";
        if($data['emp'] != "all") $emp = $data['emp'];
        $empList = $this->facial->facialDevicePerson($serial_number,"","","","",$emp);
        foreach ($period as $key => $date) {
            //$value->format('Y-m-d')  
            foreach ($empList as $row => $val) {
                $logChecker = $this->facial->checkerIfHasLogs($val->employeeid, $date);
                if ($logChecker == "nolog") {
                    $createFaceRecord = '"pass":"12345678","personId":'.$val->personId.',"length":-1,"index":0,"startTime": "'.$date.' 01:00:00"","endTime":"'.$date.' 23:59:00"","model":0';
                    $this->facial->saveTaskToDevice($serial_number, "findRecords", $createFaceRecord);
                }
            }
        } 
    }

}