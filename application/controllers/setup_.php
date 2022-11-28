<?php 
/**
 * @author Max Consul
 * @copyright 2019
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_ extends CI_Controller {

	/**
	 * Loads setup model everytime this class is accessed.a
	 */
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		$this->load->model('setup');
	}

    public function loadUserSetup(){
        $data['records'] = $this->setup->userSetup();
        $this->load->view("maintenance/usertable", $data);
    }

    public function loadCampusSetup(){
        $data['records'] = $this->setup->campusSetup();
        $this->load->view("config/campus_details", $data);
    }

    public function loadEmailSetup(){
        $data['records'] = $this->setup->emailSetup();
        $this->load->view("config/email_details", $data);
    }

    public function loadEmpFacial(){
        $this->load->model('utils');
        $data['emplist'] = $this->utils->getEmplist("","","","teaching");
        $data['opt_campus'] = $this->extras->showcampus();
        $data['opt_department'] = $this->extras->showdepartment();
        $this->load->view("config/emp_registration", $data);
    }

    public function loadDevicePerson(){
        $this->load->model('utils');
        $serial_number = $this->input->post("gate");
        $data['records'] = $this->setup->getDevicePerson($serial_number);
        $deviceInfo = $this->setup->getFacialInfo($serial_number);
        $data['ip'] = $deviceInfo[0]['ip'];

        $data['serial'] = $serial_number;
        $this->load->view("config/device_log", $data);
    }

    public function loadFacialSetup(){
        $data['records'] = $this->setup->facialSetup();
        $this->load->view("config/devices_table", $data);
    }

    public function loadFacialHistory(){
        $serial_number = $this->input->post("gate");
        $code = $this->input->post("code");
        if ($code == "today") {
            $data['records'] = $this->setup->facialHistoryToday($serial_number);
        }else{
            $data['records'] = $this->setup->facialHistory($serial_number);
        }
        $this->load->view("config/facial_history_table", $data);
    }

    public function loadRequestSetup(){
        $data['records'] = $this->setup->requestSetup();
        $this->load->view("config/request_details", $data);
    }
     public function loadClusterSetup(){
        $data['records'] = $this->setup->clusterSetup();
        $this->load->view("config/cluster_details", $data);
    }

    public function loadofficeSetup(){
        $data['records'] = $this->setup->getOffice();
        $this->load->view("maintenance/officeTable", $data);
    }

    public function getOffice(){
        $toks = $this->input->post("toks");
        $department = $toks ? $this->gibberish->decrypt( $this->input->post("department"), $toks ) : $this->input->post("department");
        echo $this->setup->getOfficesbyDept($department);
    }

    public function getOfficeMultiple(){
        $toks = $this->input->post("toks");
        $department = $toks ? $this->gibberish->decrypt( $this->input->post("department"), $toks ) : $this->input->post("department");
        echo $this->setup->getOfficesbyDeptMultiple($department);
    }

    public function getEmployeeExcluded(){
        $this->load->model("utils");
        $this->load->model("employee");
        $emp_list = array();
        $bio_list = array();

        $employees = $this->employee->getEmployeeListExcluded("active");
        foreach($employees as $row){
            $emp_list[$row['employeeid']] = array(
                "employeeid" => $row['employeeid'],
                "fullname" => $row['fullname']
            );
        }

        $bio = $this->employee->getEmployeeExcludedInOut();
        foreach($bio as $row){
            $bio_list[$row['employeeid']] = array(
                "employeeid" => $row['employeeid'],
                "fullname" => $row['fullname'],
                "teachingtype" => $row['teachingtype'],
                "status" => $row['isactive']
            );
        }
        $data['employee'] = $emp_list;
        $data['bio'] = $bio_list;
        $this->load->view("config/excluded_bios", $data);
    }

    public function loadScheduleTable(){
        $data['records'] = $this->setup->SCSetup();
        $this->load->view("maintenance/scheduleTable", $data);
    }

    public function manageRequest(){
        $toks = $this->input->post("toks");
        $request_code = $this->gibberish->decrypt( $this->input->post("request_code"), $toks );
        $action = $this->input->post('action');
        $data = array();
        $request_info = array();
        /*for delete modal*/
        $action = substr($action, 4);
        $data['action'] = $action;

        /*end*/
        if(!$request_code){
            $data['tag'] = "add";
            $data['title'] = "Add Remark Setup";
            $this->load->view('config/manage_request', $data);
        }else{
            $request_info = $this->setup->getSetupDatarequest($request_code);
            $data['tag'] = "edit";
            $data['title'] = "Edit Remark Setup";
            foreach($request_info as $value){
                $data['request_code'] = Globals::_e($value['request_code']); 
                $data['description'] = Globals::_e($value['description']); 
            }
            $this->load->view('config/manage_request', $data);
        }
    }

    public function saveEmail(){
        $data = $this->input->post();
        $toks = $data["toks"];
        foreach($data as $key => $val){
            if($key != "Password" && $key != "toks") $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
        // echo "<pre>"; print_r($data); die;
        $data["updated_by"] = $this->session->userdata('fullname');
        
        if ($data["verify_password"] != $data["verify_password"]) {
            echo "pass";
        }else{
            unset($data["verify_password"]);
            $this->setup->saveEmail($data);
            echo "success";
        }
    }

    public function saveRequest(){
        $toks = $this->input->post("toks");
        $request_code = $this->gibberish->decrypt( $this->input->post("request_code"), $toks );
        $description = $this->gibberish->decrypt( $this->input->post("description"), $toks );
        $data = array(
            "request_code" => $request_code,
            "description" => $description 
        );
        $action = $this->gibberish->decrypt( $this->input->post("action"), $toks );
        $save_data = $this->setup->saveRequest($data, $action);
        if($save_data) echo $action;
        else if($save_data == "edit") echo $action;
        else echo FALSE;
    }

    public function deleterequest(){
        $action = "delete";
        $toks = $this->input->post("toks");
        $request_code = $this->gibberish->decrypt( $this->input->post("request_code"), $toks );
        $save_data = $this->setup->saveRequest($request_code, $action);
        echo json_encode($save_data);
    }

    public function manageCampus(){
        $code = $this->input->post('code');
        $action = $this->input->post('action');
        $data = array();
        $campus_info = array();
        /*for delete modal*/
        $action = substr($action, 4);
        $data['action'] = $action;

        $data['emplist'] = $this->setup->getActiveEmployees();
        /*end*/
        if(!$code){
            $data['tag'] = "add";
            $data['title'] = "Add Campus setup";
            $data['principal'] = "";
            $this->load->view('config/manage_campus', $data);
        }else{
            $campus_info = $this->setup->getSetupData($code);
            $data['tag'] = "edit";
            $data['title'] = "Edit Campus setup";
            foreach($campus_info as $value){
                $data['code'] = $value['code']; 
                $data['description'] = $value['description']; 
                $data['principal'] = $value['campus_principal']; 
            }
            $this->load->view('config/manage_campus', $data);
        }
    }

    public function manageDevicePerson(){
        $data = array();
        $action = $this->input->post('action');
        $code = $this->input->post('code');
        $device = $this->input->post('device');
        $deviceInfo = $this->setup->getFacialInfo($device);
        $data['ip'] = $deviceInfo[0]['ip'];
        $data['serial_number'] = $device;
        $campus_info = array();
        $data['action'] = $action;
        $data['emplist'] = $this->setup->getActiveEmployees();
        /*end*/
        if(!$code){
            $data['tag'] = "add";
            $data['title'] = "Add Person";
            $this->load->view('config/person_create', $data);
        }else{
            $campus_info = $this->setup->getDataFacial($code);
            $data['tag'] = "edit";
            $data['title'] = "Edit Person";
            foreach($campus_info as $value){
                $data['serial_number'] = $value['serial_number']; 
                $data['name'] = $value['name']; 
                $data['ip'] = $value['ip']; 
            }
            $this->load->view('config/person_create', $data);
        }
    }

    public function manageFacial(){
        $code = $this->input->post('code');
        $action = $this->input->post('action');
        $data = array();
        $campus_info = array();
        $data['action'] = $action;
        /*end*/
        if(!$code){
            $data['tag'] = "add";
            $data['title'] = "Add Facial Device";
            $this->load->view('config/manage_facial', $data);
        }else{
            $campus_info = $this->setup->getDataFacial($code);
            $data['tag'] = "edit";
            $data['title'] = "Edit Facial Device";
            foreach($campus_info as $value){
                $data['serial_number'] = $value['serial_number']; 
                $data['name'] = $value['name']; 
                $data['ip'] = $value['ip']; 
            }
            $this->load->view('config/manage_facial', $data);
        }
    }

    public function addToExcluded(){
        $save_data = $this->setup->setupBioExcluded($this->input->post('code'), $this->input->post('name'), 'add');
        echo $save_data;
    }

    public function removeToExcluded(){
        $save_data = $this->setup->setupBioExcluded($this->input->post('code'), '', '');
        echo $save_data;
    }

    public function deleteFacial(){
        $action = "delete";
        $code = $this->input->post('code');  
        $save_data = $this->setup->saveFacial($code, $action);
        echo $save_data;
    }

    public function deletePerson(){
        $action = "delete";
        $data['personId'] = $this->input->post('code');
        $data['serial_number'] = $this->input->post('serial_number');    
        $save_data = $this->setup->savePersonToDevice($data, $action);
        echo $save_data;
    }

    public function resetDevice(){
        $serial_number = $this->input->post('serial_number');    
        $save_data = $this->setup->resetDevicePerson($serial_number);
        echo $save_data;
    }

    public function saveFacial(){
        $data = array(
            "serial_number" => $this->input->post('serial_number'),
            "name" => $this->input->post('name'),
            "ip" => $this->input->post('ip')
        );
        $action = $this->input->post('action');
        $save_data = $this->setup->saveFacial($data, $action);
        if($save_data) echo $action;
        else if($save_data == "edit") echo $action;
        else echo FALSE;
    }

    public function savePersonToDevice(){
        $data = array(
            "serial_number" => $this->input->post('serial_number'),
            "personId" => $this->input->post('personID'),
            "employeeid" => $this->input->post('employeeid'),
            "card" => $this->input->post('card_number')
        );
        $action = $this->input->post('action');
        $save_data = $this->setup->savePersonToDevice($data, $action);
        if($save_data) echo $action;
        else if($save_data == "edit") echo $action;
        else echo FALSE;
    }

    public function saveCampus(){
        $data = array(
            "code" => $this->input->post('code'),
            "description" => $this->input->post('description'),
            "campus_principal" => $this->input->post('employeeid')
        );
        $action = $this->input->post('action');
        $save_data = $this->setup->saveCampus($data, $action);
        if($save_data) echo $action;
        else if($save_data == "edit") echo $action;
        else echo FALSE;
    }

    public function deletecampus(){
        $action = "delete";
        $code = $this->input->post('code');  
        $save_data = $this->setup->saveCampus($code, $action);
        echo $save_data;
    }

    public function loadDepartmentSetup(){
        $data['records'] = $this->setup->departmentSetup();
        $this->load->view("config/department_details", $data);
    }


    public function manageDepartment(){
        $toks = $this->input->post("toks");
        $code = $toks? $this->gibberish->decrypt( $this->input->post("code"), $toks ) :  $this->input->post('code');
        $action = $toks? $this->gibberish->decrypt( $this->input->post("action"), $toks ) : $this->input->post('action');
        $data = array();
        $department_info = array();
        /*for delete modal*/
        $action = substr($action, 4);
        $data['action'] = $action;
        $data["depthead"] = "";
        /*end*/
        // echo "<pre>";print_r($code);die;
        if(!$code){
            $data['tag'] = "add";
            $data['title'] = "Add Department setup";
            $this->load->view('config/manage_department', $data);
        }else{
            $department_info = $this->setup->getSetupDataDepartment($code);
            $data['tag'] = "edit";
            $data['title'] = "Edit Department setup";
            foreach($department_info as $value){
                $data['code'] = $value['code']; 
                $data['description'] = $value['description']; 
                $data['depthead'] = $value['head']; 
            }
            $this->load->view('config/manage_department', $data);
        }
    }

    public function deleteDepartment(){
        $action = "delete";
        $toks = $this->input->post("toks");
        $code = $this->gibberish->decrypt($this->input->post('code'), $toks);  
        $save_data = $this->setup->saveDepartment2($code, $action);
        echo $save_data;
    }

    public function loadHRSetupDetails(){
        $toks = $this->input->post("toks");
        $code_details = $toks ? $this->gibberish->decrypt($this->input->post("code_table"), $toks) : $this->input->post("code_table");
        $view = "config/".$code_details."_details";
        $data['records'] = $this->setup->loadHRSetupDetails($code_details);
        $this->load->view($view, $data);
    }

    public function loadEmploymentStatusDetails(){
        $data['records'] = $this->setup->loadEmploymentStatusDetails();
        $this->load->view("config/employmentStatus_details", $data);
    }

    public function manageHRSetup(){
        $toks = $this->input->post('toks');
        $code = $toks ? $this->gibberish->decrypt($this->input->post('code'), $toks) : $this->input->post('code');
        $action = $toks ? $this->gibberish->decrypt($this->input->post('action'), $toks) : $this->input->post('action');
        $tableCode = $toks ? $this->gibberish->decrypt($this->input->post("tableCode"), $toks) : $this->input->post('tableCode');
        $primaryKey = '';
        if($tableCode == "code_gender") $primaryKey = "genderid";
        else if($tableCode == "code_nationality") $primaryKey = "nationalityid";
        else if($tableCode == "code_religion") $primaryKey = "religionid";
        else if($tableCode == "code_citizenship") $primaryKey = "citizenid";
        else if($tableCode == "code_relationship") $primaryKey = "relationshipid";
        else if($tableCode == "code_managementlevel") $primaryKey = "managementid";
        else if($tableCode == "code_school") $primaryKey = "schoolid";
        else $primaryKey = '';
        $data = array();
        $HRSetup_info = array();
        /*for delete modal*/
        $action = substr($action, 4);
        $data['action'] = $action;
        /*end*/
        if(!$code || $code == "undefined"){
            $data['tag'] = "add";
            $data['tableCode'] = $tableCode;
            $this->load->view('config/manageHRSetup', $data);
        }else{
            $HRSetup_info = $this->setup->getHRSetupData($code, $tableCode, $primaryKey);
            $data['tag'] = "edit";
            $data['tableCode'] = $tableCode;
            if($primaryKey){
                foreach($HRSetup_info as $value){
                    $data['code'] = $value[$primaryKey]; 
                    $data['description'] = $value['description']; 
                }
            }else{
                foreach($HRSetup_info as $value){
                    $data['code'] = $value['code']; 
                    $data['description'] = $value['description']; 
                }
            }
            
            $this->load->view('config/manageHRSetup', $data);
        }
    }

    public function saveHRSetup(){
        $toks = $this->input->post("toks");
        $description = $toks ? $this->gibberish->decrypt($this->input->post('description'), $toks) : $this->input->post('description');
        $code = $toks ? $this->gibberish->decrypt($this->input->post('code'), $toks) : $this->input->post('code');
        $action = $toks ? $this->gibberish->decrypt($this->input->post('action'), $toks) : $this->input->post('action');
        $table = $toks ? $this->gibberish->decrypt($this->input->post("tableCode"), $toks) : $this->input->post('tableCode');
        $primaryKey = '';
        if($table == "code_gender") $primaryKey = "genderid";
        else if($table == "code_nationality") $primaryKey = "nationalityid";
        else if($table == "code_religion") $primaryKey = "religionid";
        else if($table == "code_citizenship") $primaryKey = "citizenid";
        else if($table == "code_relationship") $primaryKey = "relationshipid";
        else if($table == "code_managementlevel") $primaryKey = "managementid";
        else if($table == "code_school") $primaryKey = "schoolid";
        else $primaryKey = '';
        $save_data = $this->setup->saveHRSetup($code, $description, $action, $table, $primaryKey);
        if($save_data) echo $action;
        else if($save_data == "edit") echo $action;
        else echo FALSE;
    }

    public function deleteHRSetup(){
        $action = "delete";
        $toks = $this->input->post('toks');  
        $code = $toks ? $this->gibberish->decrypt($this->input->post('code'), $toks) : $this->input->post('code'); 
        $table = $toks ? $this->gibberish->decrypt($this->input->post("tableCode"), $toks) : $this->input->post('tableCode');
        $primaryKey = '';
        if($table == "code_gender") $primaryKey = "genderid";
        else if($table == "code_nationality") $primaryKey = "nationalityid";
        else if($table == "code_religion") $primaryKey = "religionid";
        else if($table == "code_citizenship") $primaryKey = "citizenid";
        else if($table == "code_relationship") $primaryKey = "relationshipid";
        else if($table == "code_managementlevel") $primaryKey = "managementid";
        else if($table == "code_school") $primaryKey = "schoolid";
        else $primaryKey = '';
        $save_data = $this->setup->saveHRSetup($code, "", $action, $table,  $primaryKey);
        echo $save_data;
    }

    public function saveDepartment2(){
        $toks = $this->input->post("toks");
        $data = array(
            "code" => $this->gibberish->decrypt($this->input->post('code'), $toks),
            "description" => $this->gibberish->decrypt($this->input->post('description'), $toks),
            "head" => $this->gibberish->decrypt($this->input->post('head'), $toks)
        );
        $action = $this->gibberish->decrypt($this->input->post('action'), $toks);
        $save_data = $this->setup->saveDepartment2($data, $action);
        echo $save_data;
    }

    public function getAttendanceConfirmedList(){
        $data = $this->input->post();
        $cutoff = $data['cdate'];
        $tnt = $data['tnt'];
        $deptid = $data['deptid'];
        $office = $data['office'];
        $campus = $data['campus'];
        $empstatus = $data['empstatus'];
        $employeeid = $data['employeeid'];
        $empstat = $data['empstat'];
        list($from_date, $to_date) = explode(",", $cutoff);
        if($tnt == "teaching") $data['result'] = $this->attendance->emp_confirmed($from_date, $to_date,$tnt,$employeeid, $campus, $deptid,$office,"department", $empstatus, $empstat);
        else $data['result'] = $this->attendance->emp_confirmed_nt($from_date, $to_date, $tnt, $employeeid, $campus, $deptid,$office, "department", $empstatus, $empstat);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['teachingtype'] = $tnt == "teaching" ? "Teaching" : "Non-Teaching";
        $data["last_deptid"] = "";
        $this->load->view("employeemod/viewempconfirm", $data);

    }

    public function getAttendanceNotConfirmedList(){
        $data = $this->input->post();
        $cutoff = $data['cdate'];
        $tnt = $data['tnt'];
        $deptid = $data['deptid'];
        $office = $data['office'];
        $campus = $data['campus'];
        $empstatus = $data['empstatus'];
        $employeeid = $data['employeeid'];
        $empstat = $data['empstat'];
        list($from_date, $to_date) = explode(",", $cutoff);
        if($tnt == "teaching") $data['result'] = $this->attendance->emp_not_yet_confirmed($from_date, $to_date, $tnt, $employeeid, "", "", $deptid, $campus,$office,$empstatus, $empstat);
        else $data['result'] = $this->attendance->emp_not_yet_confirmed_nt($from_date, $to_date, $tnt, $employeeid, "", "", $deptid, $campus,$office,$empstatus, $empstat);
        // echo $this->db->last_query(); die;
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['teachingtype'] = $tnt == "teaching" ? "Teaching" : "Non-Teaching";
        $data["last_deptid"] = "";
        // echo "<pre>"; print_r($data); die;
        $this->load->view("employeemod/viewempnotconfirm", $data);

    }

    public function loadProcessDTR(){
        $this->load->model('utils');
        $data['emplist'] = $this->utils->getEmplist("","","","teaching");
        $data['opt_campus'] = $this->extras->showcampus();
        $data['opt_department'] = $this->extras->showdepartment();
        $this->load->view("payroll/processdtr", $data);
    }

    public function deductionDetails(){
        $employeeid = $this->input->post('employeeid');
        $data['deduc_list'] = $this->setup->deductionList($employeeid);
        $this->load->view("employee/deduction_details", $data);
    }

    public function loadInitialRequirementsSetup(){
        $data['records'] = $this->setup->initRequirementsSetup();
        $this->load->view("applicant/initial_requirements_details", $data);
    }

    public function loadPreRequirementsSetup(){
        $data['records'] = $this->setup->preRequirementsSetup();
        $this->load->view("applicant/pre_requirements_details", $data);
    }

    public function manageInitialRequirements(){
        $toks = $this->input->post('toks');
        $code = $this->gibberish->decrypt($this->input->post('code'), $toks);
        $action = $this->gibberish->decrypt($this->input->post('action'), $toks);
        $type = $this->gibberish->decrypt($this->input->post('type'), $toks);
        $data = array();
        $campus_info = array();
        $action = substr($action, 4);
        $data['action'] = $action;
        $data['emplist'] = $this->setup->getActiveEmployees();
        if(!$code){
            $data['tag'] = "add";
            $data['title'] = "Add Initial Requirements Setup";
            $data['type'] = "";
            $this->load->view('applicant/manage_initial_requirements', $data);
        }else{
            $campus_info = $this->setup->getInitialRequirementsSetupData($code);
            $data['tag'] = "edit";
            $data['title'] = "Edit Initial Requirements Setup";
            foreach($campus_info as $value){
                $data['code'] = $value['code']; 
                $data['description'] = $value['description']; 
                $data['type'] = $value['type']; 
            }
            $this->load->view('applicant/manage_initial_requirements', $data);
        }
    }

    public function managePreRequirements(){
        $toks = $this->input->post('toks');
        $code = $this->gibberish->decrypt($this->input->post('code'), $toks);
        $action = $this->gibberish->decrypt($this->input->post('action'), $toks);
        $type = $this->gibberish->decrypt($this->input->post('type'), $toks);
        $data = array();
        $campus_info = array();
        /*for delete modal*/
        $action = substr($action, 4);
        $data['action'] = $action;

        $data['emplist'] = $this->setup->getActiveEmployees();
        /*end*/
        if(!$code){
            $data['tag'] = "add";
            $data['title'] = "Add Pre Requirements Setup";
            $data['type'] = "";
            $this->load->view('applicant/manage_pre_requirements', $data);
        }else{
            $campus_info = $this->setup->getPreRequirementsSetupData($code);
            $data['tag'] = "edit";
            $data['title'] = "Edit Pre Requirements Setup";
            foreach($campus_info as $value){
                $data['code'] = $value['code']; 
                $data['description'] = $value['description']; 
                $data['type'] = $value['type']; 
            }
            $this->load->view('applicant/manage_pre_requirements', $data);
        }
    }

    public function saveRequiredRequirements(){
        $toks = $this->input->post("toks");
        $id = $this->setup->getRequiredDocumentMaxId();
        $data = array(
            "id" => $id,
            "code" => $this->gibberish->decrypt($this->input->post('code'), $toks),
            "description" => $this->gibberish->decrypt($this->input->post('description'), $toks),
            "type" => $this->gibberish->decrypt($this->input->post('type'), $toks)
        );
        $action = $this->gibberish->decrypt($this->input->post('action'), $toks);
        $save_data = $this->setup->saveRequiredRequirements($data, $action);
        if($save_data) echo $action;
        else if($save_data == "edit") echo $action;
        else echo FALSE;
    }

    public function deleteInitialRequirements(){
        $action = "delete";
        $toks = $this->input->post('toks');  
        $code = $this->gibberish->decrypt($this->input->post('code'), $toks);  
        $save_data = $this->setup->saveRequiredRequirements($code, $action);
        echo $save_data;
    }

    public function deletePreRequirements(){
        $action = "delete";
        $toks = $this->input->post('toks');  
        $code = $this->gibberish->decrypt($this->input->post('code'), $toks);  
        $save_data = $this->setup->saveRequiredRequirements($code, $action);
        echo $save_data;
    }

    public function addMultipleRequirement(){
        $toks = $this->input->post('toks');  
        $req_id = $this->gibberish->decrypt($this->input->post('req_id'), $toks);  
        $applicantid = $this->gibberish->decrypt($this->input->post('applicantid'), $toks);  
        $ini_or_pre = $this->gibberish->decrypt($this->input->post('ini_or_pre'), $toks);  
        $save_data = $this->setup->saveMultipleRequirement($req_id, $applicantid, $ini_or_pre);
        echo $save_data;
    }

    public function applicantInitialRequirements(){
        $this->load->model("applicantt");
        $toks =  $this->input->post("toks");
        $list = $this->setup->initRequirementsSetup();
        $data['app_stat'] = $toks ? $this->gibberish->decrypt( $this->input->post("app_stat"), $toks ) :  $this->input->post("app_stat");
        $data['applicantid'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicantid"), $toks ) : $this->input->post("applicantid");
        foreach($list as $row){
            list($title, $content, $size, $mime) = $this->applicantt->getApplicantUploadedDocs($data['applicantid'], $row["id"]);
            $data["records"][$row["id"]]["title"] = $title;
            $data["records"][$row["id"]]["content"] = $content;
            $data["records"][$row["id"]]["size"] = $size;
            $data["records"][$row["id"]]["mime"] = $mime;
            $data["records"][$row["id"]]["id"] = $row["id"];
            $data["records"][$row["id"]]["code"] = $row["code"];
            $data["records"][$row["id"]]["description"] = $row["description"];
            $data["records"][$row["id"]]["type"] = $row["type"];
            $data["records"][$row["id"]]["original"] = 1;
            $multiple = $this->setup->multipleRequirements($row["id"], $data['applicantid'], "ini");
            foreach ($multiple as $key => $value) {
                $value["id"] = "ini_".$value["id"]."_".$row["id"];
                list($multiTitle, $multiContent, $multiSize, $MultiMime) = $this->applicantt->getApplicantUploadedDocs($data['applicantid'], $value["id"]);
                $data["records"][$value["id"]]["title"] = $multiTitle;
                $data["records"][$value["id"]]["content"] = $multiContent;
                $data["records"][$value["id"]]["size"] = $multiSize;
                $data["records"][$value["id"]]["mime"] = $MultiMime;
                $data["records"][$value["id"]]["id"] = $value["id"];
                $data["records"][$value["id"]]["code"] = $row["code"];
                $data["records"][$value["id"]]["description"] = $row["description"];
                $data["records"][$value["id"]]["type"] = $row["type"];
                $data["records"][$value["id"]]["original"] = 0;
            }
        }
        $this->load->view("applicant/applicant_init_requirements", $data);
    }

    public function applicantPreRequirements(){
        $this->load->model("applicantt");
        $toks =  $this->input->post("toks");
        $list = $this->setup->preRequirementsSetup();
        $data['app_stat'] = $toks ? $this->gibberish->decrypt( $this->input->post("app_stat"), $toks ) :  $this->input->post("app_stat");
        $data['applicantid'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicantid"), $toks ) : $this->input->post("applicantid");
        foreach($list as $row){
            list($title, $content, $size, $mime) = $this->applicantt->getApplicantUploadedDocs($data['applicantid'], $row["id"]);
            $data["record"][$row["id"]]["title"] = $title;
            $data["record"][$row["id"]]["content"] = $content;
            $data["record"][$row["id"]]["size"] = $size;
            $data["record"][$row["id"]]["mime"] = $mime;
            $data["record"][$row["id"]]["id"] = $row["id"];
            $data["record"][$row["id"]]["code"] = $row["code"];
            $data["record"][$row["id"]]["description"] = $row["description"];
            $data["record"][$row["id"]]["type"] = $row["type"];
            $data["record"][$row["id"]]["original"] = 1;
            $multiple = $this->setup->multipleRequirements($row["id"], $data['applicantid'], "pre");
            foreach ($multiple as $key => $value) {
                $value["id"] = "pre_".$value["id"]."_".$row["id"];
                list($multiTitle, $multiContent, $multiSize, $MultiMime) = $this->applicantt->getApplicantUploadedDocs($data['applicantid'], $value["id"]);
                $data["record"][$value["id"]]["title"] = $multiTitle;
                $data["record"][$value["id"]]["content"] = $multiContent;
                $data["record"][$value["id"]]["size"] = $multiSize;
                $data["record"][$value["id"]]["mime"] = $MultiMime;
                $data["record"][$value["id"]]["id"] = $value["id"];
                $data["record"][$value["id"]]["code"] = $row["code"];
                $data["record"][$value["id"]]["description"] = $row["description"];
                $data["record"][$value["id"]]["type"] = $row["type"];
                $data["record"][$value["id"]]["original"] = 0;
            }
        }
        $this->load->view("applicant/applicant_pre_requirements", $data);
    }

 public function rankConfigType(){
        $data = array();
        $q_setup = $this->setup->getPayrollType();
        if($q_setup->num_rows > 0) $data['records'] = $q_setup->result_array();
        $data['categ'] = "type";
        $this->load->view('config/configtype', $data);
    }

    public function rankConfig(){
        $data = array();
        $q_setup = $this->setup->getPayrollRank();
        if($q_setup->num_rows > 0) $data['records'] = $q_setup->result_array();
        $data['categ'] = "rank";
        $this->load->view('config/configrank', $data);
    }

    public function rankConfigSet(){
        $data = array();
        $q_setup = $this->setup->getPayrollSet();
        if($q_setup->num_rows > 0) $data['records'] = $q_setup->result_array();
        $data['categ'] = "set";
        $this->load->view('config/configset', $data);
    }

    public function validatePayrollRankSetup(){

        $table = "";
        $toks = $this->input->post('toks');
        $categ = $this->gibberish->decrypt($this->input->post('categ'), $toks);
        $description = $this->gibberish->decrypt($this->input->post('description'), $toks);
        $code = $this->gibberish->decrypt($this->input->post('code'), $toks);

        if($categ == "type") $table = "rank_code_type";
        else if($categ == "rank") $table = "rank_code";
        else if($categ == "set") $table = "rank_code_set";

        $res = $this->setup->savePayrollRankSetup($description, $table, $code);
        if($res){
            if($table == "rank_code_type"){
                if ($code) {
                    echo "Type has been Updated Successfully ";
                }
                else{
                echo "Type has been Saved Successfully ";
                }
            }
            else if($table == "rank_code"){
                if ($code) {
                    echo "Rank has been Updated Successfully ";
                }
                else{
                echo "Rank has been Saved Successfully ";
                }
            }
            else if($table == "rank_code_set"){
                if ($code) {
                    echo "Set has been Updated Successfully ";
                }
                else{
                echo "Set has been Saved Successfully ";
                }
            }
        } 
        else echo "Failed to Saved!";
    }

    public function editPayrollRankSetup(){
        $id = $this->input->post('id');
        $categ = $this->input->post('categ');

        if($categ == "type") $table = "rank_code_type";
        else if($categ == "rank") $table = "rank_code";
        else if($categ == "set") $table = "rank_code_set";
        
        list($code, $description) = $this->setup->getPayrollRankData($id, $table);
        $data = array(
            "id" => $code,
            "description" => $description
        );

        echo json_encode($data);
    }

    public function deletePayrollRankSetup(){
        $id = $this->input->post('id');
        $categ = $this->input->post('categ');

        if($categ == "TYPE") $table = "rank_code_type";
        else if($categ == "RANK") $table = "rank_code";
        else if($categ == "SET") $table = "rank_code_set";

        $result = $this->setup->deletePayrollRankData($id, $table);

        echo $result;
    }

    public function loadManageRankSetup(){
        $data['records'] = $this->setup->manageRankSetup();
        $this->load->view("config/managerank_details", $data);
    }

    public function manageRank(){
        $toks = $this->input->post('toks');
        $id = $this->gibberish->decrypt($this->input->post('id'), $toks);
        $action = $this->input->post('action');
        $data['type_config'] = $this->setup->getPayrollTypeArray();
        $data['rank_config'] = $this->setup->getPayrollRankArray();
        $data['set_config'] = $this->setup->getPayrollSetArray();
        $data['type'] = $data['rank'] = $data['set'] = $data['basic_rate'] = "";
        $action = substr($action, 4);
        $data['action'] = $action;
        if(!$id){
            $data['tag'] = "add";
            $data['title'] = "Add Rank Table";
            $this->load->view('config/modify_managerank', $data);
        }else{
            $data['tag'] = "edit";
            $data['title'] = "Edit Rank Table";
            $managerank_info = $this->setup->getSetupDataManageRank($id);
            foreach($managerank_info as $value){
                $data['id'] = $value['id']; 
                $data['type'] = $value['type']; 
                $data['rank'] = $value['rank']; 
                $data['set'] = $value['set']; 
                $data['basic_rate'] = $value['basic_rate']; 
            }
            $this->load->view('config/modify_managerank', $data);
        }
    }

    function convertFormDataToArray($formdata){
        $data_arr = array();
        $formdata = explode("&", $formdata);
        foreach($formdata as $row){
            list($key, $value) = explode("=", $row);
            $data_arr[$key] = $value;
        }

        return $data_arr;
    }

    public function saveManageRank(){
        $res = '';
        $toks = $this->input->post("toks");
        $form_data = $this->input->post("form_data");
        $data = $this->gibberish->decrypt($form_data, $toks);
        $data = $this->convertFormDataToArray($data);
        extract($data);
        if($tag=="add") $res =  $this->setup->saveManageRank($type, $rank, $set, $basic_rate);
        else $res = $this->setup->updateManageRank($id, $type, $rank, $set, $basic_rate); 
        echo $res ? $tag : false;
    }

    public function deleteManageRank(){
        $toks = $this->input->post('toks');  
        $id = $this->gibberish->decrypt($this->input->post('id'), $toks);  
        $res = $this->setup->deleteManageRank($id);
        echo $res;
    }

    public function getRankByType(){
        $option = "<option value=''>Select rank</option>";
        $id = $this->input->post('id');
        $toks = $this->input->post('toks');
        if($toks) $id = $this->gibberish->decrypt($id, $toks);
        $data = $this->setup->getPayrollTypeArray();
        foreach($data as $value){
            $option .= "<option value = ' ".$value['id']." ' ".(($value['id'] == $id) ? " selected" : "")." >".$value['description']."</option>";
        }

        echo $option;
    }

    public function getRankBasicRate(){
        $id = $this->input->post('id');
        $toks = $this->input->post('toks');
        if($toks) $id = $this->gibberish->decrypt($id, $toks);
        $basic_rate = $this->setup->getRankBasicRate($id);
        $basic_rate = str_replace(",", "", $basic_rate);
        echo $basic_rate;
    }

    public function syncEmployeeListAllcard(){
        $api_url = Globals::apiUrl()."/api/person/batchaddemployee";
        $empinfo = array();
        $empcount = $success = $failed = $total = 0;

        $datenow = date("Y-m-d");
        $data = $this->input->post();
        $toks = $data["toks"];
        $active = $status = $this->gibberish->decrypt($data["status"], $toks);
        $teachingType = $this->gibberish->decrypt($data["teachingType"], $toks);
        $department = $this->gibberish->decrypt($data["department"], $toks);
        $office = $this->gibberish->decrypt($data["office"], $toks);
        $empstat = $this->gibberish->decrypt($data["empstat"], $toks);
        $employeeid = $this->gibberish->decrypt($data["employeeid"], $toks);
        $where = "";
        if($teachingType) $where .= " AND teachingtype = '$teachingType'";
     
        if($department) $where .= " AND deptid = '$department'";
        
        if($employeeid) $where .= " AND employeeid = '$employeeid'";

        if($status != "all"){
          if($active=="1"){
            $where .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($active=="0"){
            $where .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($status)) $where .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }

        if($office) $where .= " AND office = '$office'";

        if($empstat) $where .= " AND employmentstat = '$empstat'";

        if(($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" || $_SERVER["HTTP_HOST"] == "poveda-hris.pinnacle.com.ph") && strpos($_SERVER["REQUEST_URI"], 'hris') !== false){
            $emp_records = $this->extensions->getEmployeeList($where);
            foreach($emp_records as $employee){
                
                $empinfo[] = array(
                    "PersonType" => "E",
                    "IDNumber"  => $employee['employeeid'],
                    "FirstName" => ($employee['fname']) ? $employee['fname'] : "PTI Default Name",
                    "MiddleName" => ($employee['mname']) ? $employee['mname'] : "PTI Default Name",
                    "LastName" => ($employee['lname']) ? $employee['lname'] : "PTI Default Name",
                    "CampusName" => "Pinnacle Technologies Inc.",
                    "DepartmentName" => ($employee['deptid']) ? $this->extensions->getDeparmentDescriptionReport($employee['deptid']) : " ",
                    "PositionName" => ($employee['positionid']) ? $this->extensions->getPositionDescription($employee['positionid']) : " "
                );
                // echo "<pre>"; print_r($empinfo); die;

                if($empcount == 99){
                    /*insert to allcard logs*/
                    $this->employee->saveALLCARDLogs($empinfo, true);
                    
                    $empinfo = json_encode($empinfo);
                    $token = $this->extensions->getPostmanToken();
                    $access_token = "Bearer ".$token;

                    $headers = array(
                        'Content-type: application/json',
                        'Authorization: '.$access_token,
                    );

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                      CURLOPT_PORT => "6271",
                      CURLOPT_URL => $api_url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 1000,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS =>  $empinfo,
                      CURLOPT_HTTPHEADER => array(
                        "Content-Type: application/json",
                        "Authorization: $access_token",
                        "Postman-Token: 7e7ac43d-92df-488f-8d36-b15d4b2e4193",
                        "cache-control: no-cache"
                      ),
                    ));
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    

                    curl_close($curl);
                    $response = json_decode($response);
                    $success += isset($response->success) ? $response->success : 0;
                    $failed += isset($response->failed) ? $response->failed : 0;
                    $total += isset($response->total) ? $response->total : 0;

                    $empcount = 0;
                    $empinfo = array();
                }
            
                $empcount+=1;
            }

            if($empcount < 99){
                /*insert to allcard logs*/
                $this->employee->saveALLCARDLogs($empinfo, true);
                
                $empinfo = json_encode($empinfo);
                $token = $this->extensions->getPostmanToken();
                $access_token = "Bearer ".$token;

                $headers = array(
                    'Content-type: application/json',
                    'Authorization: '.$access_token,
                );

                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_PORT => "6271",
                  CURLOPT_URL => $api_url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 1000,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS =>  $empinfo,
                  CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: $access_token",
                    "Postman-Token: 7e7ac43d-92df-488f-8d36-b15d4b2e4193",
                    "cache-control: no-cache"
                  ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                

                curl_close($curl);
                $response = json_decode($response);
                $success += isset($response->success) ? $response->success : 0;
                $failed += isset($response->failed) ? $response->failed : 0;
                $total += isset($response->total) ? $response->total : 0;
            }

            echo "Successfully processed ".$success." employee. Failed to processed ".$failed." employee. Total of ".$total." data.";
        }

    }

    public function syncStudentListAllcard(){
        $api_url = Globals::apiUrl()."/api/person/batchaddstudent";
        $studinfo = array();
        $student_records = $this->extensions->getStudentList();
        if($student_records){
            foreach($student_records as $student){
                $studinfo[] = array(
                    "PersonType" => "S",
                    "IDNumber"  => $student['studentid'], 
                    "FirstName" => isset($student['fname']) ?$student['fname'] : "sample name" , 
                    "MiddleName" => isset($student['mname']) ?$student['mname'] : "sample name" ,
                    "LastName" => isset($student['lname']) ?$student['lname'] : "sample name" , 
                    "BirthDate" => isset($student['bdate']) ?$student['bdate'] : "1970-01-01" , 
                    "Gender" => isset($student['gender']) ?$student['gender'] : "M" , 
                    "Address" => isset($student['address']) ?$student['address'] : "sample address" , 
                    "ContactNumber" => isset($student['mobile']) ?$student['mobile'] : "0999-999-9999" ,
                    "TelephoneNumber" => isset($student['landline']) ?$student['landline'] : "0999-999-9999" , 
                    "EmailAddress" => isset($student['email']) ?$student['email'] : "sample@gmail.com" ,
                    "CampusName" => "Pinnacle Technologies Inc.",
                    "EducLevelName" => "Grade School",
                    "YearSectionName" => "Grade 3",
                    "StudSecName" => "Matiyaga",
                    "EmergencyContactPerson" => isset($student['cp_name']) ?$student['cp_name'] : "sample name" ,
                    "EmergencyContactNo" => isset($student['cp_mobile']) ?$student['cp_mobile'] : "0999-999-9999" , 
                    "EmergencyContactAddress" => isset($student['cp_address']) ?$student['cp_address'] : "sample address" 
                );
            }
        }
        $studinfo = json_encode($studinfo);
        $token = $this->extensions->getPostmanToken();
        $access_token = "Bearer ".$token;

        $headers = array(
            'Content-type: application/json',
            'Authorization: '.$access_token,
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_POST, 1 ); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $studinfo); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response);
         echo "Successfully processed ".(isset($response->success) ? $response->success : "0")." employee. Failed to processed ".(isset($response->failed) ? $response->failed : "0")." employee. Total of ".(isset($response->total) ? $response->total : "0")." data.";

    }

    public function getAvailableCourses(){
        $option = "<option value=''> - Select a course - </option>";
        $course_list = $this->setup->getAvailableCourses();
        if($course_list){
            foreach($course_list as $row){
                $option .= "<option value='".$row['course']."'>".$row['course']."</option>";
            }
        }

        echo $option;
    }

    public function getAvailableSection(){
        $course = $this->input->post("course");
        $option = "<option value=''> - Select a course - </option>";
        $where_clause = " WHERE course LIKE '%$course%' ";
        $section_list = $this->setup->getAvailableSection($where_clause);
        if($section_list){
            foreach($section_list as $row){
                $option .= "<option value='".$row['section']."'>".$row['section']."</option>";
            }
        }

        echo $option;
    }

    public function getAvailableSubject(){
        $course = $this->input->post("course");
        $option = "<option value=''> - Select a course - </option>";
        $section_list = $this->setup->getAvailableSubject($course);
        if($section_list){
            foreach($section_list as $row){
                $option .= "<option value='".$row['subject']."'>".$row['subject']."</option>";
            }
        }

        echo $option;
    }

    public function loadBypassSetup(){
        $data['records'] = $this->setup->bypassSetup();
        $this->load->view("gate/bypass_details", $data);
    }

    public function manageBypass(){
        $id = $this->input->post('id');
        $action = $this->input->post('action');
        $data = array();
        $campus_info = array();
        /*for delete modal*/
        $action = substr($action, 4);
        $data['action'] = $action;
        $data["id"] = $id;
        $data['emplist'] = $this->setup->getActiveEmployees();
        $data['campuslist'] = $this->setup->campusSetup();
        /*end*/
        if(!$id){
            $data['tag'] = "add";
            $data['title'] = "Add terminal access";
            $data['principal'] = "";
            $this->load->view('gate/manage_bypass', $data);
        }else{
            $campus_info = $this->setup->getBypassSetupData($id);
            $data['tag'] = "edit";
            $data['title'] = "Edit terminal access";
            foreach($campus_info as $value){
                $data['id'] = $value['id']; 
                $data['code'] = $value['code']; 
                $data['employee'] = $value['employee']; 
            }
            $this->load->view('gate/manage_bypass', $data);
        }
    }

    public function saveBypass(){
        $campuscode = implode(",", $this->input->post("code"));
        $employeeid = implode(",", $this->input->post("employeeid"));
        $bypassid = $this->input->post("bypassid");
        $data = array(
            "code" => $campuscode,
            "employee" => $employeeid
        );
        $action = $this->input->post('action');
        $save_data = $this->setup->saveBypass($data, $action, $bypassid);
        if($save_data) echo $action;
        else if($save_data == "edit") echo $action;
        else echo FALSE;
    }

    public function deleteBypass(){
        $action = "delete";
        $code = $this->input->post('code');  
        $save_data = $this->setup->saveBypass($code, $action);
        echo $save_data;
    }

    public function deleteWorkshop(){
        $toks = $this->input->post("toks");
        $tblid = $toks ? $this->gibberish->decrypt( $this->input->post("tblid"), $toks ) : $this->input->post("tblid");
        $deleteWS = $this->setup->deleteWS($tblid);
        echo $deleteWS;
    }

    public function getDepartmentList(){
        $option = "<option value=''> - Select a department - </option>";
        $dept_list = $this->setup->departmentSetup();
        foreach($dept_list as $row){
            $option .= "<option value='".$row["code"]."'> ".$row['description']." </option>";
        }

        echo $option;
    }

    public function loadPhilhealthShareSetup(){
        $data['records'] = $this->setup->loadPhilhealthShareSetup();
        $this->load->view("payroll/healthshare_table", $data);
    }

    public function managePhilhealthShare(){
       $data["username"] = $this->session->userdata("username");
       $data["id"] = $data["min_salary"] = $data["max_salary"] = $data["percentage"] = $data["def_amount"] = "";
       $data['code'] = $this->input->post("code");
       $record = $this->setup->managePhilhealthShare($data['code']);
       foreach($record as $records){
        $data["id"] = $records["id"];
        $data["min_salary"] = $records["min_salary"];
        $data["max_salary"] = $records["max_salary"];
        $data["percentage"] = $records["percentage"];
        $data["def_amount"] = $records["def_amount"];
       }
       $this->load->view('payroll/manage_healthshare', $data); 
    }
    public function savePhilhealthShare(){ 
        $res = "";
        $job = $this->input->post("job");  
        $code = $this->input->post("code");
        $description = $this->input->post("description");
        $division = $this->input->post("division");
        $head = $this->input->post("head");
        $divhead = $this->input->post("divhead");
        $isBED = $this->input->post("isBED");
        $res = $this->setup->saveOffice($code,$description,$division,$head,$divhead,$job,$isBED);
       
       echo $res;
    }

    public function loadBatchEncodeModal(){
        $toks = $this->input->post("toks");
        $data = $this->input->post();
        foreach($data as $key => $val){
            $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
        if($data["category"] == "income") $data["function"] = "getIncomeBatchEncodeData";
        else if($data["category"] == "deduction") $data["function"] = "getDeductionBatchEncodeData";
        else if($data["category"] == "loan") $data["function"] = "getLoanBatchEncodeData";
        else if($data["category"] == "regdeduc") $data["function"] = "getReglamentoryBatchEncodeData";
        else if($data["category"] == "salary") $data["function"] = "getSalaryBatchEncodeData";
        $data['type_config'] = $this->setup->getPayrollTypeArray();
        $data['emplist'] = $this->setup->getActiveEmployees();
        $this->load->view("payroll/batch_encode/batchprocess", $data);
    }

    public function laodYearEndIncomeModal(){
        $this->load->model("payroll_reports");
        $toks = $this->input->post("toks");
        $data = $this->input->post();
        foreach($data as $key => $val){
            $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
        $data["function"] = "getIncomeBatchEncodeData";
        
        $data['income_config'] = $this->payroll_reports->getPayrollIncomeConfig(); 
        $data['emplist'] = $this->setup->getActiveEmployees();
        // echo "<pre>"; print_r($data); die;
        $this->load->view("payroll/batch_encode/yearend", $data);
    }

    public function loadCodeTypeProgress(){
        $toks = $this->input->post("toks");
        $code = $toks ? $this->gibberish->decrypt( $this->input->post("code"), $toks ) : $this->input->post("code");
        echo $this->setup->userCodeProgress($code);
    }

}