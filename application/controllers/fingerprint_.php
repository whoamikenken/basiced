<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fingerprint_ extends CI_Controller {

    /**
     * Loads setup model everytime this class is accessed.a
     */
    function __construct(){
        parent::__construct();
        $this->load->model('fingerprint');
        $this->load->model("utils");
        $this->load->model("employee");
    }

    public function getEmployeeForBio(){
        $campus_list  = $this->utils->getCampusList();
        $department    = $this->utils->getDepartments();
        $schedcluster    = $this->utils->getschedcluster();
        $emp_list = array();

        $active = $this->input->post("active");
        $employees = $this->employee->getEmployeeList("active");

        foreach($employees as $row){
            $emp_list[Globals::_e($row['employeeid'])] = array(
                "employeeid" => Globals::_e($row['employeeid']),
                "fullname" => Globals::_e($row['fullname']),
                "campusid" => isset($campus_list[$row['campusid']]) ? Globals::_e($campus_list[$row['campusid']]) : "",
                "deptid" => isset($department[$row['deptid']]) ? Globals::_e($department[$row['deptid']]) : "",
                "emptype" => isset($schedcluster[$row['emptype']]) ? Globals::_e($schedcluster[$row['emptype']]) : "",
                "teachingtype" => Globals::_e($row['teachingtype'])
            );
        }
        $data['employee'] = $emp_list;
        $this->load->view("fingerprint/fingerprint_table", $data);
    }

    public function getEmployeeWithBio(){
        $campus_list  = $this->utils->getCampusList();
        $emp_list = array();
        $active = $this->input->post("active");
        $employees = $this->fingerprint->getEmployeeListWithBio("active");
        foreach($employees as $row){
            $emp_list[Globals::_e($row['employeeid'])] = array(
                "employeeid" => Globals::_e($row['employeeid']),
                "fullname" => Globals::_e($row['fullname']),
                "campusid" => isset($campus_list[$row['campusid']]) ? Globals::_e($campus_list[$row['campusid']]) : "",
                "teachingtype" => Globals::_e($row['teachingtype']),
                "rfid" => Globals::_e($row['rfid'])
            );
        }
        $data['employee'] = $emp_list;
        $this->load->view("fingerprint/biotesting_table", $data);
    }

    public function getEmployeeExcluded(){
        $campus_list  = $this->utils->getCampusList();
        $emp_list = array();
        $bio_list = array();

        $bio = $this->fingerprint->getEmployeeExcludedInOut($this->input->post("employeeid"), $this->input->post("isactive"), $this->input->post("deptid"));
        foreach($bio as $row){
            $bio_list[Globals::_e($row['employeeid'])] = array(
                "employeeid" => Globals::_e($row['employeeid']),
                "fullname" => ($row['fullname']),
                "campusid" => isset($campus_list[$row['campusid']]) ? ($campus_list[$row['campusid']]) : "",
                "teachingtype" => ($row['teachingtype']),
                "status" => Globals::_e($row['isactive'])
            );
        }
        $data['employee'] = $emp_list;
        $data['bio'] = $bio_list;
        // echo "<pre>";print_r($this->input->post());die;
        $this->load->view("fingerprint/excluded_bios_table", $data);
    }

    public function getEmployeeBioPicture(){
        $emp_info = array();
        $toks = $this->input->post("toks");
        $id = $this->gibberish->decrypt($this->input->post("employeeid"), $toks);
        $employees = $this->fingerprint->getEmployeeBioPic($id);
        foreach($employees as $row){
            $emp_info[$row['finger']] = array(
                "userID" => $row['userID'],
                "fullname" => $row['fullname'],
                "template" => $row['template']
            );
        }
        echo json_encode($emp_info);
    }

    public function checkRFIDBIO(){
        $rfid = $this->input->post("rfid");
        echo $this->utils->rfidChecker($rfid);
    }

    public function saveBio(){
        $data = $this->input->post();
        $toks = $data["toks"];
        unset($data["toks"]);
        foreach($data as $key => $val){
            $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
        $data["template"] = str_replace("data:image/png;base64,","", $data["template"]);
        $checker = $this->setup->checkBio($data["userID"], $data["finger"]);
        $message = "";
        $insert_data = array(
            "userID"  => $data["userID"],
            "finger"  => $data["finger"],
            "template"     => $data["template"],
            "rfid" => $data["rfid"],
            "fullname" => $this->utils->getFullName($data["userID"]),
            "fingerID" => bin2hex($data["userID"]."-".$data["finger"]),
            "fingerNum" => $data["fingerNum"],
            "feature" => $data["feature"]
        );
        if ($checker) {
            $this->fingerprint->saveUpdateBio($insert_data, $data["userID"], $data["finger"]);
            $message = "Update Successfully";
        }else{
            $this->fingerprint->saveInsertBio($insert_data);
            $message = "Successfully Registered";
        }
        echo $message;
    }

    public function getPictureCount(){
        $toks = $this->input->post("toks");
        $id = $this->gibberish->decrypt($this->input->post("employeeid"), $toks);
        $employees = $this->fingerprint->getEmployeeBioPic($id);
        echo json_encode($employees);
    }

     public function addToExcluded(){
        $toks = $this->input->post("toks");
        $code = $this->gibberish->decrypt($this->input->post('code'), $toks);
        $name = $this->gibberish->decrypt($this->input->post('name'), $toks);
        $save_data = $this->fingerprint->setupBioExcluded($code, $name, 'add');
        echo $save_data;
    }

    public function removeToExcluded(){
        $toks = $this->input->post("toks");
        $code = $this->gibberish->decrypt($this->input->post("code"), $toks);
        $save_data = $this->fingerprint->setupBioExcluded($code, '', '');
        echo $save_data;
    }

    public function gate(){
        $data = array( 
            'isloggedin' => $this->fingerprint->validate_is_logged_in() 
        ); 
        
        $this->load->view('fingerprint/gate_header');
        $this->load->view('includes/modalview');
        $this->load->view("fingerprint/gate", $data);
    }

    public function seminar_gate(){
        $data = array( 
            'isloggedin' => $this->fingerprint->validate_is_logged_in() 
        ); 
        $this->load->view('inhouse_seminar/gate_header');
        $this->load->view('includes/modalview');
        $this->load->view("inhouse_seminar/gate", $data);
    }

    public function getIpAddressPHP(){
        echo $this->input->ip_address();
    }

    public function get_terminal_username(){
        $privateip = $this->input->post('privateip');
        $terminal_name =  $this->fingerprint->get_terminal_username($privateip);
        echo $terminal_name;
    }

    public function set_terminal_id() 
    {
        return $this->fingerprint->validate_terminal_id(); 
    }

    public function attempts_ajax_list()
    { 
        return $this->fingerprint->_get_attempts_query(); 
    }

    public function verify_gate() 
    { 
        $valid = $this->fingerprint->process_gate();  
        switch ($valid) 
        {  
            default: 
                echo $valid;
            break;
        }    
    }

    public function check_login()
    {  
        $data = array( 
            'fingerprint' => $this->fingerprint, 
        );
        if (is_array($this->input->post())) 
        { 
            foreach ($this->input->post() as $key => $value) 
            { 
                $data[htmlspecialchars($key)]=htmlspecialchars($value); 
            }
        } 
        $this->load->view('fingerprint/gate_checker', $data); 
    }

    public function unset_only() {
        $user_data = $this->session->all_userdata();

        foreach ($user_data as $key => $value) {
            if ($key != 'session_id' && $key != 'ip_address' && $key != 'user_agent' && $key != 'last_activity') {
                $this->session->unset_userdata($key);
            }
        }
    }

    public function syncfingers() 
    { 
        return $this->fingerprint->syncfingers(); 
    }

    function rfidChecker()
    { 
        return $this->fingerprint->validate_rfid_2(); 
    }

    function rfidSeminarChecker() 
    { 
        return $this->fingerprint->validate_rfid_2_seminar(); 
    }

    function resultChecker()  
    {  
        return $this->fingerprint->validate_result_3(); 
    }

    function resultSeminarChecker()  
    {  
        return $this->fingerprint->validate_result_3Seminar(); 
    }

    function getServerTime()  
    {  
        echo strtotime($this->fingerprint->getServerTime()); 
    }

    public function loadEmployeeListDropdownExcluded(){
        $employee_exclude = $this->fingerprint->getEmployeeListExcluded();
        $option = "<option value=''>Select Employee</option>";
        foreach($employee_exclude as $value){
            $option .= "<option value='". Globals::_e($value['employeeid']) ."' >".Globals::_e($value['employeeid'])." - ". Globals::_e($value['fullname']) ."</option>";
        }
        echo $option;
    }

}