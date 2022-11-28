<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class webcheckin_ extends CI_Controller {

    /**
     * Loads setup model everytime this class is accessed.a
     */
    function __construct(){
        parent::__construct();
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
        $this->load->model('webcheckin');
        $this->load->model("utils");
    }
// 
    public function loadWebHistoryTable(){
        $toks = $this->input->post("toks");
        $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));
        $employees = $this->webcheckin->getHistoryData($data['status'],$data['type'],$data['from'],$data['to'],$data['employeeFilter'],$data['deptid'],$data['office']);
        $data['employee'] = $employees;
        // echo "<pre>";print_r($this->db->last_query());die;
        $this->load->view("web_check/web_history_table", $data);
    }

    public function loadWebHistoryDetail(){
        $toks = $this->input->post("toks");
        $record = $this->webcheckin->getHistoryDetails($this->gibberish->decrypt( $this->input->post("id"), $toks ));
        // $image = $this->webcheckin->getHistoryDetailsImage($this->gibberish->decrypt( $this->input->post("id"), $toks ));
        $data['record'] = $record;
        $data['recordID'] = $this->gibberish->decrypt( $this->input->post("id"), $toks );
        // $data['imageData'] = $image;
        $this->load->view("web_check/web_history_detail", $data);
    }

    public function getImageRecordWeb(){
        $toks = $this->input->post("toks");
        $image = $this->webcheckin->getHistoryDetailsImage($this->gibberish->decrypt( $this->input->post("id"), $toks ));
        // echo "<pre>";print_r($this->gibberish->decrypt( $this->input->post("id"), $toks ));die;
        if (count($image) > 0) {
           echo '<img src="data:image/jpeg;base64, '.$image[0]['image'].'" id="image" width="'.$image[0]['width'].'" height="'.$image[0]['height'].'">';
        }else{
            echo "<h2>No Image Found On Server";
        }
        
    }

    public function loadWebSetupTable(){
        $toks = $this->input->post("toks");
        $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));

        $this->webcheckin->checkStatusOfEmployee();
        $employees = $this->webcheckin->getWebSetupData($data['status'],$data['type'],$data['from'],$data['to'],$data['employeeFilter'],$data['deptid'],$data['office'],'',$data['empstatFilter']);
        // echo "<pre>";print_r($this->db->last_query());die;
        $data['employee'] = $employees;
        $this->load->view("web_check/web_setup_table", $data);
    }

    public function loadSurveyCatSetupTable(){
        $data['record'] = $this->webcheckin->getSurveyCatData("");
        $this->load->view("web_check/survey_cat_table", $data);
    }

    public function loadSurveyItemsSetupTable(){
        $data['record'] = $this->webcheckin->getSurveyItemsData("");
        $this->load->view("web_check/survey_items_table", $data);
    }

    public function loadSurveyResponseSetupTable(){
        $data['record'] = $this->webcheckin->getSurveyItemsData("");
        $this->load->view("web_check/survey_items_table", $data);
    }

    public function getSurveySummary(){
        $employees = $this->webcheckin->getAllReponseAnswer();
        foreach ($employees as $key => $value) {
            foreach (explode('/', substr($value['answer'], 1)) as $row => $val) {
                $questions =  explode('*', $val);
                $data['survey'][$questions[2]][$value['employee']]['question'] = $questions[2];
                $data['survey'][$questions[2]][$value['employee']]['type'] = $questions[0];
                $data['survey'][$questions[2]][$value['employee']]['answer'] = $questions[1];
            }
        }
        
        
        $counts = array();
        foreach ($data['survey'] as $key => $value) {
            foreach ($value as $d => $subarr) {
            if (isset($counts[$subarr['question']])) {
                if (isset($counts[$subarr['question']]['surveyAns'][$subarr['answer']])) {
                    $counts[$subarr['question']]['surveyAns'][$subarr['answer']]++;
                }else{
                    $counts[$subarr['question']]['surveyAns'][$subarr['answer']] = 1;
                }
                $counts[$subarr['question']]['count']++;
            }
            else {
                if ($subarr['type'] == "YN" && !isset($counts[$subarr['question']]['surveyAns'][$subarr['answer']])) {
                    $counts[$subarr['question']]['surveyAns']["Yes"] = 0;
                    $counts[$subarr['question']]['surveyAns']["No"] = 0;
                }
                $counts[$subarr['question']]['count'] = 1;
                $counts[$subarr['question']]['surveyAns'][$subarr['answer']] = 1;
                $counts[$subarr['question']]['type'] = $subarr['type'];
            }
              $counts[$subarr['question']] = isset($counts[$subarr['question']]) ? $counts[$subarr['question']]++ : 1;
            }
        }

        $data['counts'] = $counts;
        $data['respondee'] = count($data);               
        // $result = array_count_values($data);
       
       // echo "<pre>";print_r($data);die;
        $this->load->view("web_check/survey_response_print_summary", $data);
    }
 
    public function loadResponseHistoryTable(){
        $data = $this->input->post();
        $employees = $this->webcheckin->getResponseData($data['category'], $data["survey"], $data['type'], $data['employeeFilter'], $data['deptid']);
        $data['employee'] = $employees;
        $this->load->view("web_check/survey_response_table", $data);
    }

    public function loadEmployeeListDropdownm(){
        $toks = $this->input->post("toks");
        $type = $toks ? $this->gibberish->decrypt( $this->input->post("type"), $toks ) : $this->input->post('type');
        $status = $toks ? $this->gibberish->decrypt( $this->input->post("status"), $toks ) : $this->input->post('status');
        $deptid = $toks ? $this->gibberish->decrypt( $this->input->post("deptid"), $toks ) :  $this->input->post('deptid');
        $office = $toks ? $this->gibberish->decrypt( $this->input->post("office"), $toks ) :  $this->input->post('office');
        $id = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) :  $this->input->post('id');
        $action = $toks ? $this->gibberish->decrypt( $this->input->post("action"), $toks ) :  $this->input->post('action');
        $emps = $toks ? $this->gibberish->decrypt( $this->input->post("emps"), $toks ) :  $this->input->post('emps');
        $selected = $toks ? $this->gibberish->decrypt( $this->input->post("selected"), $toks ) :  $this->input->post('selected');
        $records = $this->webcheckin->getEmployeeListDropdownWebcheckin($type,$deptid,$office,$status);
        // echo "<pre>";print_r($status);die;
        $option = "";
        if ($selected == "add") {
            $option = "<option value=''>All Employee</option>";
        }elseif($selected == "edit" || $selected == "select"){
            $option = "";
        }elseif($selected == "items"){
            if($type == "") $option = "<option value='all'>All Employee</option>";
            else $option = "";
        }elseif($selected == "itemsAll"){
            $option = "<option value='all'>All Employee</option>";
        }else{
            $option = "<option value=''>All Employee</option>";
        }

        if ($action == 'batch') {
            $id = explode('/', $id);
        }
         // echo "<pre>";print_r();die;
        foreach($records as $value){
            $select = "";
            if ($id == $value['employeeid']) $select = "selected";
            if ($selected == "select") $select = "selected";
            if (in_array($value['employeeid'], $id) && $action == 'batch') {
                $select = "selected";
            }
            $option .= "<option value='". Globals::_e($value['employeeid']) ."' ".$select.">".Globals::_e($value['employeeid'])." - ". Globals::_e($value['fullname']) ."</option>";
        }
        echo $option;
    }

    public function loadOfficeByDept(){
        $toks = $this->input->post("toks");
        $deptid = $toks ? $this->gibberish->decrypt( $this->input->post("deptid"), $toks ) : $this->input->post('deptid');
        $records = $this->extras->getOffice("",$deptid);
        echo $records;
    }

    public function manageWebSetup(){
        $toks = $this->input->post("toks");
        $code = $toks? $this->gibberish->decrypt( $this->input->post("code"), $toks ) : $this->input->post('code');
        $type = $toks? $this->gibberish->decrypt( $this->input->post("type"), $toks ) : $this->input->post('type');
        $employee = $toks? $this->gibberish->decrypt( $this->input->post("employee"), $toks ) : $this->input->post('employee');
        $data = array();
        $record = array();
        $data['id'] = $code;
        $data["employee"] = $data["status"] = $data["deptid"] = $data["office"] = $data["date_from"] = $data["date_to"] = "";
        if ($type == 'edit') {
            $record = $this->webcheckin->getWebSetupData("","","","","","","",$code);
            foreach ($record as $key => $value) {
                $data["employee"] = Globals::_e($value["employee"]);
                $data["status"] = Globals::_e($value["status"]);
                $data["deptid"] = Globals::_e($value["deptid"]);
                $data["office"] = Globals::_e($value["office"]);
                $data["date_from"] = Globals::_e($value["date_from"]);
                $data["date_to"] = Globals::_e($value["date_to"]);
            }
        }
        
        if($code == "none"){
            $data['tag'] = "add";
        }elseif($type == "batch"){
            $data['tag'] = "batch";   
            if (isset($employee)) {
                $data["employee"] = $employee;
            }
        }else{
            $data['tag'] = "edit";   
        }
        // echo "<pre>";print_r($data);die;
        $this->load->view('web_check/web_setup_manage', $data);
    }

    public function deleteSurveyCat(){
        $record = $this->webcheckin->deleteSurveyCatSetup($this->input->post("code"));
        if ($record) echo "success";
        else "error";
    }

    public function deleteWebCheckInSetup(){
        $toks = $this->input->post("toks");
        $id = $toks? $this->gibberish->decrypt( $this->input->post("id"), $toks ) : $this->input->post('id');
        $record = $this->webcheckin->deleteWebSetup($id);
        if ($record) echo "success";
        else "error";
    }

    public function manageSurveyCatSetup(){
        $code = $this->input->post('code');
        $data = array();
        $record = array();
        $data['id'] = $code;
        $data["rank"] = $data["name"] = "";
        $record = $this->webcheckin->getSurveyCatData($code);
        foreach ($record as $key => $value) {
            $data["rank"] = $value["rank"];
            $data["name"] = $value["name"];
        }

        if($code == "none"){
            $data['tag'] = "add";
            $this->load->view('web_check/web_survey_cat_manage', $data);
        }else{
            $data['tag'] = "edit";   
            $this->load->view('web_check/web_survey_cat_manage', $data);
        }
    }

    public function saveSurveyCat(){
        $data = $this->input->post();
        $dataInsert = array();

        $checker = $this->webcheckin->checkExistingRank($data["rank"]);
        if ($data["action"] == "edit") { 
            if ($checker != $data["id"] && $checker != "none") {
                echo "dup";
            }else{
                $update_data = $this->webcheckin->updateSurveyCatSetup($data["id"], $data["name"], $data["rank"]);
                if($update_data) echo "updated.";
            }
        }else{
            if ($checker != "none") {
                echo "dup";
            }else{
                $dataInsert["rank"] = $data["rank"];
                $dataInsert["name"] = $data["name"];
                $save_data = $this->webcheckin->saveSurveyCatSetup($dataInsert);
                if($save_data) echo "added.";
            }
        }   
    }

    public function manageSurveyitemsSetup(){
        $code = $this->input->post('code');
        $data = array();
        $record = array();
        $data['id'] = $code;
        $data["category"] = $data["description"] = $data["status"] = $data["audience"] = $data["questions"] = "";
        $record = $this->webcheckin->getSurveyItemsData($code);
        $data['categorySetup'] = $this->webcheckin->getSurveyCatData("");
        $allData['employeeid'] = "all";
        $allData['fullname'] = "All Employee";
        $data['employeeList'][] = $allData;
        $data['employeeList'][] = $this->extensions->getEmployeeListToDropdown();
        foreach ($record as $key => $value) {
            $data["category"] = $value["category"];
            $data["description"] = $value["description"];
            $data["status"] = $value["status"];
            $data["audience"] = $value["audience"]  ;
            $data["questions"] = $value["questions"];
        }

        if($code == "none"){
            $data['tag'] = "add";
            $this->load->view('web_check/web_survey_items_manage', $data);
        }else{
            $data['tag'] = "edit";   
            $this->load->view('web_check/web_survey_items_manage', $data);
        }
    }

    public function viewSurveyitemsSetup(){
        $data["record"] = $this->webcheckin->getSurveyItemsData($this->input->post('code'));
        // echo "<pre>";print_r($data);die;
        $this->load->view('web_check/web_survey_items_view', $data);
    }

    public function viewSurveyResponseSetup(){
        $data["record"] = $this->webcheckin->getSurveyResponseData($this->input->post('code'));
        // echo "<pre>";print_r($data);die;
        $this->load->view('web_check/web_survey_response_view', $data);
    }

    public function viewSurveyitemsAudience(){
        $data["title"] = $this->input->post('title');
        $data["audience"] = $this->input->post('audience');
        $this->load->view('web_check/web_survey_items_audience', $data);
    }

    public function saveSurveyItemsSetup(){
        $data = $this->input->post();
        $dataInsert = array();
        $dataInsert["category"] = $data["category"];
        $dataInsert["description"] = $data["description"];
        $dataInsert["status"] = $data["status"];
        $dataInsert["questions"] = $data["questions"];
        $dataInsert["audience"] = $data["audience"];

        if ($data["action"] == "edit") { 
            $update_data = $this->webcheckin->updateSurveyItemsSetup($dataInsert, $data["id"]);
            if($update_data) echo "updated.";
        }else{
            $save_data = $this->webcheckin->saveSurveyItemsSetup($dataInsert);
            if($save_data) echo "added.";
        }   
    }

    public function saveSetup(){
        $toks = $this->input->post("toks");
        $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));
        if ($data["action"] == "edit") { 
            $checkerTrasaction = $this->webcheckin->checkDateTransaction($data["employeeList"],$data["date_to"]);
            $checkerSetup = $this->webcheckin->checkDateSetup($data["employeeList"],$data["date_to"],$data["id"]);
            if ($checkerTrasaction) {
                echo "transaction";
            }elseif($checkerSetup){
                echo "date";
            }else{
                $update_data = $this->webcheckin->updateWebSetup($data["id"], $data["date_to"]);
                if($update_data) echo "updated.";
            }
        }elseif($data["action"] == "batch"){
            $emps = explode("/", $data['id']);
            $id_list = $this->webcheckin->getEmployeelistWithWSID($data['employeeList'], $data['ldfrom'], $data['ldto']);
            foreach ($id_list as $key => $value) {
                $dataInsert = array();
                $update_data = $this->webcheckin->updateWebSetup($value['id'], $data["date_to"]);
            }
            // foreach ($emps as $key) {
            //     $dataInsert = array();
            //     $update_data = $this->webcheckin->updateWebSetup($key, $data["date_to"]);
            // }
            echo "updated.";
        }elseif($data["action"] == "add"){
            $list = "";
            foreach (explode(",", $data["employeeList"]) as $key => $value) {
                $checker = $this->webcheckin->checkExistingSetup($value, $data["date_from"], $data["date_to"]);
                if ($checker) {
                    $list .= $this->extensions->getEmployeeBasicName($value)." ";
                }
            }
            if ($list != "") {
                echo $list;
            }else{
                foreach (explode(",", $data["employeeList"]) as $key => $value) {
                    $dataInsert["status"] = $data["status"];
                    $dataInsert["date_from"] = $data["date_from"];
                    $dataInsert["date_to"] = $data["date_to"];
                    $dataInsert["employee"] = $value;

                    $save_data = $this->webcheckin->saveWebSetup($dataInsert);
                }
        
                echo "added.";
            }
        }
        
    }

    public function getServerTime(){
        return $this->db->query("SELECT CURRENT_TIMESTAMP")->row()->CURRENT_TIMESTAMP;
    }

    public function saveCheckIn(){
        $data = $this->input->get();
        $last_log_type = $this->webcheckin->getLastLog($data['userid']);
        $imagedata = array();
        $imagedata['width'] = $data["width"];
        $imagedata['height'] = $data["height"];
        $imagedata['image'] = base64_encode(file_get_contents($_FILES['webcam']['tmp_name']));
        $last_id ='';

        // echo "<pre>";print_r($data);die;
        //SAVE SURVEY
        if ($data["answer"] != "") {
            foreach (explode("~!~", substr($data["answer"], 3)) as $category => $categoryValue) {
                $surveyInfo = explode("#$#", $categoryValue);
                $surveyData = array();
                $surveyData['employee'] = $data['userid'];
                $surveyData['category'] = $surveyInfo[0];
                $surveyData['description'] = $surveyInfo[1];
                $surveyData['answer'] = $surveyInfo[2];
                $this->webcheckin->saveSurveyData($surveyData);
            }
        }

        //UNLINK ANSWER AND IMAGE DATA
        unset($data["answer"]);
        unset($data["width"]);
        unset($data["height"]);
        
        // echo "<pre>";print_r(date_create($data['localtimein']));die;
        //CONTINUE SAVING CHECKIN
        if ($last_log_type['log_type'] != "new") {
            $start = date_create($last_log_type['localtimein']);
            $end = date_create($data['localtimein']);
            $diff = date_diff($end,$start);
            $minutes = ($diff->h * 60) + $diff->i;
            if ($minutes < 2) {
                echo "wait";
                die;
            }
        }
        
        $data['localtimein'] = date('Y-m-d', strtotime($this->getServerTime()))." ".date(" H:i:s");
        
        if ($last_log_type['log_type'] == "new"){
            $nout = array();
            $data['log_type'] = "IN";
            $nout['userid'] = $data['userid'];
            $nout['localtimein'] = $data['localtimein'];
            $nout['log_type'] = $data['log_type'];
            $nout['username'] = "Webcheckin";

            $this->webcheckin->saveNoOut($nout);
            $last_id = $this->webcheckin->saveCheckIn($data);
            $imagedata['base_id'] = $last_id;
            $save_data = $this->webcheckin->saveCheckInImage($imagedata);
        }elseif ($last_log_type['log_type'] == "IN") {
            $data['log_type'] = "OUT";
            $timesheetData = array();
            $timesheetData['userid'] = $data['userid'];
            $timesheetData['timein'] = $last_log_type['localtimein'];
            $timesheetData['timeout'] = $data['localtimein'];
            $timesheetData['otype'] = "webcheckin";

            $this->webcheckin->deleteNoOut($data['userid'], $last_log_type['localtimein']);
            $last_id = $this->webcheckin->saveCheckIn($data);
            $imagedata['base_id'] = $last_id;
            $save_data = $this->webcheckin->saveCheckInToTimesheet($timesheetData);
            $this->webcheckin->saveCheckInImage($imagedata);

        }elseif ($last_log_type['log_type'] == "OUT") {
            $nout = array();
            $data['log_type'] = "IN";
            $nout['userid'] = $data['userid'];
            $nout['localtimein'] = $data['localtimein'];
            $nout['log_type'] = $data['log_type'];
            $nout['username'] = "Webcheckin";
            $this->webcheckin->saveNoOut($nout);
            $this->webcheckin->deleteOtherLogs($data['userid']);
            $last_id = $this->webcheckin->saveCheckIn($data);
            $imagedata['base_id'] = $last_id;
            $this->webcheckin->saveCheckInImage($imagedata);
        }
        
        if($last_id) echo "success";
    }

}