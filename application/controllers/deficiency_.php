<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deficiency_ extends CI_Controller {

    /**
     * Loads deficiency model everytime this class is accessed.
     */
    function __construct(){
        parent::__construct();
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
        $this->load->model('deficiency');
    }

    public function viewForm(){
        $toks = $this->input->post("toks");
        $data['info_type'] = $toks ? $this->gibberish->decrypt( $this->input->post("info_type"), $toks ) : $this->input->post("info_type");
        $data['id'] = $toks ? $this->gibberish->decrypt( $this->input->post("action"), $toks ) : $this->input->post("action");
        $data['departmentid'] = $toks ? $this->gibberish->decrypt( $this->input->post("departmentid"), $toks ) :  $this->input->post("departmentid");
        $data['departmentDeflist'] = $this->deficiency->deptDeficiency();
        // echo "<pre>";print_r($data);die;
        $this->load->view('deficiency/deficiency_setup_modal', $data);
    }

    public function deptDeficiencyModal(){
        $data['deptid'] = $this->input->post("deptid");
        $data['id'] = $this->input->post("action");
        $data['departmentlist'] = $this->deficiency->loadDefDepartments($data['deptid']);
        $this->load->view('deficiency/department_deficiency_modal', $data);
    }

    public function saveDeficiencyDept(){
        $depitid = $this->input->post("deptid");
        $id = $this->input->post("id");
        $this->deficiency->saveDeficiencyDept($depitid, $id);
        // echo "<pre>"; print_r($this->db->last_query());
    }

    public function saveForm(){
        $formdata = $this->input->post("formdata");
        $formdata = base64_decode(urldecode($formdata));
        $data = Globals::convertFormDataToArray($formdata);
        $toks = $data['toks'];
        if($toks){
            foreach ($data as $key => $value) {
                if($key != "toks") $data[$key] = $this->gibberish->decrypt( $value, $toks );
            }
        }
        $return = array("err_code"=>0, 'msg'=>"Success.");
        $id = isset($data["id"]) ? $data["id"] : $this->input->post("id");
        $type = strtoupper( isset($data["type"]) ? $data["type"] : $this->input->post("type"));
        $desc = isset($data["desc"])  ? $data["desc"]  : $this->input->post("desc");
        $departmentid = isset($data["departmentid"]) ? $data["departmentid"]  : $this->input->post("departmentid");
        $description = isset($data["description"])  ? $data["description"]  : $this->input->post("description");
        $action = isset($data["action"]) ? $data["action"]  : $this->input->post("action");
        $isUnique = $this->deficiency->isUniqueDeficiency($id, $type, $desc, $description);
        //check if exists
        $defExists = $this->deficiency->getDeficiencyTypes("",$type);
        if($action == 'add'){
            if($defExists && !$id){
                $return = array("err_code"=>2,"msg"=>"Type already exists."); 
            }else{
                $query_res = $this->deficiency->insertDeficiency($type, $desc, $departmentid);
                if(!$query_res) $return = array("err_code"=>2,"msg"=>"Type already exists."); 
            }
        }
        elseif($action == 'edit'){
            if($defExists && !$id){
                $return = array("err_code"=>2,"msg"=>"Type already exists.");
            }else{  
                $query_res = $this->deficiency->updateDeficiency($id, $type, $desc, $departmentid);
                if(!$query_res) $return = array("err_code"=>2,"msg"=>"Failed to update."); 
            }
        }
        echo json_encode($return);
    }

    public function deleteRow(){
        $toks = $this->input->post("toks");
        $id = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) :  $this->input->post("id");
        $info_type = $toks ? $this->gibberish->decrypt( $this->input->post("infotype"), $toks ) :  $this->input->post("infotype");
        $query_res = $this->deficiency->removeDeficiency($id);
        if($query_res) echo "SUCCESS!";
        else           echo "FAILED!";
    }

    public function loadDeficiencyHistory(){
        $toks = $this->input->post("toks");
        $employeeid = $toks ? $this->gibberish->decrypt( $this->input->post("employeeid"), $toks ) : $this->input->post("employeeid");
        $forviewing = $toks ? $this->gibberish->decrypt( $this->input->post("forviewing"), $toks ) : $this->input->post("forviewing");
        $return=array();
        $return['def'] = $toks ? $this->gibberish->decrypt( $this->input->post("def"), $toks ) : $this->input->post("def");
        $return['head'] = $toks ? $this->gibberish->decrypt( $this->input->post("head"), $toks ) : $this->input->post("head");
        $return['d_list'] = $this->deficiency->getDeficiencyHistory($employeeid);
        // echo "<pre>"; print_r($return['d_list']->num_rows()); die;
        $return['forviewing'] = $forviewing;
        // echo "<pre>";print_r($return);die;
        if($forviewing) $this->load->view('deficiency/deficiency_history_modal',$return);
        else $this->load->view('deficiency/deficiency_history',$return);
        
    }

    public function loadDeficiency(){
        $toks = $this->input->post("toks");
        $data['deptid'] = $toks ? $this->gibberish->decrypt( $this->input->post("deptid"), $toks ) :  $this->input->post("deptid");

        $data['records'] = $this->deficiency->deficiencySetup($data['deptid']);
        // echo "<pre>";print_r($this->db->last_query());die;
        $this->load->view('deficiency/deficiency_data', $data);
    }

    public function loadDeptDeficiency(){
        $data['deparments'] = $this->deficiency->deptDeficiency();
        $this->load->view('deficiency/dept_deficiency_data', $data);
    }

    public function deleteDeptDeficiency(){
        $id = $this->input->post("id");
        $did = $this->input->post("did");
        $deleteQ = $this->deficiency->deleteDeptDeficiency($id, $did);
        if($deleteQ) echo "Deleted";
        else echo "InUse";
    }

    public function saveEmployeeDeficiency(){
        $return = array("err_code"=>0, 'msg'=>"Employee Clearance has been saved successfully");
        $def_id = $this->input->post("def_id");
        if($def_id) $return["msg"] = "Employee Clearance has been updated successfully";
        $toks = $this->input->post("toks");
        $def_id = $this->gibberish->decrypt($def_id, $toks);
        $employeeid = $this->gibberish->decrypt($this->input->post("employeeid"), $toks);
        $deptid = $this->gibberish->decrypt($this->input->post("departments"), $toks);
        $lookfor = $this->gibberish->decrypt($this->input->post("lookfor"), $toks);
        $type = $this->gibberish->decrypt($this->input->post("deficiencies"), $toks);
        $remarks = $this->gibberish->decrypt($this->input->post("remarks"), $toks);
        $datesub = $this->gibberish->decrypt($this->input->post("datesub"), $toks);
        $isCompleted = $this->gibberish->decrypt($this->input->post("isCompleted"), $toks);
        $datecompleted = $this->gibberish->decrypt($this->input->post("datecompleted"), $toks);
        $schoolYear = $this->gibberish->decrypt($this->input->post("sySelect"), $toks);
        try {
            $employeelist = explode(',', $employeeid);
            foreach ($employeelist as $key => $empid) {
                $query_res = $this->deficiency->saveEmpDeficiency($def_id, $empid,$deptid, $lookfor, $type, $remarks,$datesub, $isCompleted,$datecompleted, $schoolYear);
            }           
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        if(!isset($query_res)) $return = array("err_code"=>2,"msg"=>"Failed saving employee deficiency."); 
        
        echo json_encode($return);
        
    }

    function deleteEmpDef(){
        $id = $this->input->post("id");
        $query_res = $this->deficiency->deleteEmpDef($id);
        if($query_res) echo "Employee Clearance has been deleted successfully";
        else           echo "Employee Clearance has failed to delete.";
    }

    function getEmpDefDetails(){
        $toks = $this->input->post("toks");
        $def_id = $toks ? $this->gibberish->decrypt( $this->input->post("def_id"), $toks ) : $this->input->post("def_id");
        $res = $this->deficiency->getEmpDefDetails($def_id);
        $data = array();
        if($res->num_rows() > 0){
            foreach ($res->result() as $key => $row) {
                $data['id']                 = $row->id;
                $data['concerned_dept']     = $row->concerned_dept;
                $data['def_id']             = $row->def_id;
                $data['employeeid']         = $row->employeeid;
                $data['remarks']            = $row->remarks;
                $data['submission_date']    = $row->submission_date;
                $data['is_completed']       = $row->is_completed;
                $data['date_completed']     = $row->date_completed;
                $data['user']               = $row->user;
                $data['date_created']       = $row->date_created;
                $data['lookfor']            = $row->lookfor;
                $data['sy']                 = $row->sy;
                   
            }
        }
        echo json_encode($data);
    }

    function getDeficiencytype(){
        $toks = $this->input->post("toks");
        $dept = $toks ? $this->gibberish->decrypt( $this->input->post("dept"), $toks ) : $this->input->post("dept");
        $type = $this->deficiency->getDeficiencyTypes('','',$dept);
        $return = '<option value="">Select Clearance</option>';
        foreach ($type as $key) {
            $return .= '<option value="'.$key->id.'">'.$key->description.'</option>';
        }
        echo $return;                           
    }

    function getLookForList(){
        $return = '<option value="">Select Person to Look for</option>';
        $headCol = array("divisionhead","head");
        $toks = $this->input->post("toks");
        $dept = $toks ? $this->gibberish->decrypt( $this->input->post("dept"), $toks ) : $this->input->post("dept");
        $list = $this->extensions->getApproverList($dept);
        foreach ($list as $key => $value) {
            foreach ($headCol as $col) {
                if($this->extensions->getEmployeeName($value[$col])){
                    $return .= '<option value="'.$value[$col].'">'.$this->extensions->getEmployeeName($value[$col]).'</option>';
                }
            }
        }
        echo $return;
    }

    function loadUnderEmployee(){
        $data = array();
        $toks = $this->input->post("toks");
        $data['office'] = $emplist = $toks ? $this->gibberish->decrypt( $this->input->post("office"), $toks ) : $this->input->post("emplist");
        $data['deptid'] = $emplist = $toks ? $this->gibberish->decrypt( $this->input->post("deptid"), $toks ) : $this->input->post("deptid");
        $data['category'] = $category = $toks ? $this->gibberish->decrypt( $this->input->post("category"), $toks ) : $this->input->post("category");
        $data['employee'] = $employee = $toks ? $this->gibberish->decrypt( $this->input->post("employee"), $toks ) : $this->input->post("employee");
        $data['username'] = $username = $this->session->userdata('username');
        $data['result'] = $this->extras->getUnderDeptEmployee($category, $data['office'], $data['deptid'], $username, $employee);
        $this->load->view('employeemod/manage_clearance_data_def', $data);
    }

    function completionOfClearance(){
        $toks = $this->input->post("toks");
        $data['tbl_id'] = $emplist = $toks ? $this->gibberish->decrypt( $this->input->post("tbl_id"), $toks ) : $this->input->post("tbl_id");
        $this->load->view('deficiency/completionOfClearance', $data);
    }

    function saveCompletionOfClearance(){
        $toks = $this->input->post("toks");
        $tbl_id = $emplist = $toks ? $this->gibberish->decrypt( $this->input->post("tbl_id"), $toks ) : $this->input->post("tbl_id");
        $comdate = $emplist = $toks ? $this->gibberish->decrypt( $this->input->post("comdate"), $toks ) : $this->input->post("comdate");
        $remarks = $emplist = $toks ? $this->gibberish->decrypt( $this->input->post("remarks"), $toks ) : $this->input->post("remarks");
        $this->deficiency->saveCompletionOfClearance($tbl_id, $comdate, $remarks);
        // echo "<pre>"; print_r($this->db->last_query());
    }

    function deleteClearance(){
        $toks = $this->input->post("toks");
        $tbl_id = $emplist = $toks ? $this->gibberish->decrypt( $this->input->post("tbl_id"), $toks ) : $this->input->post("tbl_id");
        $this->deficiency->deleteEmpDef($tbl_id);
    }

} //endoffile