<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Configuration_ extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

    function __construct(){
        parent::__construct();
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
    }
    
	public function index()	{}
    
    public function viewForm(){
        $toks = $this->input->post("toks");
        $data['info_type'] = $toks ?  $this->gibberish->decrypt( $this->input->post("info_type"), $toks ) : $this->input->post("info_type");
        $data['id'] = $toks ? $this->gibberish->decrypt( $this->input->post("action"), $toks ) : $this->input->post("action");
        $this->load->view('config/view_form', $data);
    }
	
	public function viewFormPosition(){
        $toks = $this->input->post("toks");
        $data['info_type'] = $toks ? $this->gibberish->decrypt( $this->input->post("info_type"), $toks ) : $this->input->post("info_type");
        $data['id'] = $toks ? $this->gibberish->decrypt( $this->input->post("action"), $toks ) :  $this->input->post("action");
        // $data["course_list"] = $this->setup->courseList();
        $data["course_list"] = $this->setup->departmentSetup();
        $data["subject_list"] = $this->setup->subjectList();
        // echo "<pre>";print_r($data);die;
        $this->load->view('config/view_form_position', $data);
    }
    
    public function saveForm(){
        $toks = $this->input->post("toks");
        if($toks){
            $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));
        }else{
            $formdataquery = $this->input->post("formdata");
            $data = array();
            foreach($formdataquery as $each){
                    $data[$each['name']] = $each['value'];

            }
        }
        $id = isset($data["id"]) ? $data["id"] : "";
        $code = isset($data["code"]) ? $data["code"] : "";
        $description = isset($data["description"]) ? $data["description"] : "";
        $schedid = isset($data["schedid"]) ? $data["schedid"] : "";
        $date_active = isset($data["date_active"]) ? $data["date_active"] : "";

        $inProgress = $this->setup->userCodeProgress($code);
        if($inProgress){
            $return = array("err_code"=>4,"msg"=>"Inprogress");
            echo json_encode($return); die;
        }
        $empModel = new Employee();

        $infotype = $toks ? $this->gibberish->decrypt( $this->input->post("info_type"), $toks ) : $this->input->post("info_type");
        $action = $toks ? $this->gibberish->decrypt( $this->input->post("action"), $toks ) : $this->input->post("action");
        $return = $empModel->savePersonnelInfoTypes( 
            $infotype,
            $action,
            $data
        );
        

        ///< insert history of cluster saving
        if($infotype == 'type'){
            if($return['err_code']==0 || $return['err_code']==3){
                $user = $this->session->userdata('username');
                $this->db->query("INSERT INTO code_type_history (code, description, schedid, date_active, edited_by) 
                                    VALUES ('$code','$description','$schedid','$date_active','$user')");

            }
        }

        if(($schedid !== null || $schedid !== '') && $id == 0 && $infotype == 'type'){
            $this->db->query("INSERT INTO code_type (code, description, schedid, date_active) 
                                    VALUES ('$code','$description','$schedid','$date_active')");
        }
        // echo "<pre>"; print_r($this->db->last_query());
        
        echo json_encode($return);
        
    }
	
	public function saveFormPosition(){
        $empModel = new Employee();
        $file = $filename = $fileDoc = $filenameDoc = "";
        $toks = $this->input->post("toks");
        $action = $this->gibberish->decrypt($this->input->post("action"), $toks);
        $title = $this->gibberish->decrypt($this->input->post("title"), $toks);
        $hiring = $this->gibberish->decrypt($this->input->post("hiring"), $toks);
        $experience = $this->gibberish->decrypt($this->input->post("experience"), $toks);
        $till = $this->gibberish->decrypt($this->input->post("till"), $toks);
        $id = $this->gibberish->decrypt($this->input->post("id"), $toks);
        $isteaching = $this->gibberish->decrypt($this->input->post("isteaching"), $toks);
        $type = $this->gibberish->decrypt($this->input->post("type"), $toks);
        $course = $this->gibberish->decrypt($this->input->post("course"), $toks);
        $subject = $this->gibberish->decrypt($this->input->post("subject"), $toks);
        $desc = $this->gibberish->decrypt($this->input->post("desc"), $toks);
        if(isset($_FILES['file']['name'])){
            $filename = basename($_FILES['file']['name']);
            $file = file_get_contents($_FILES['file']['tmp_name'], $filename);
            $file = base64_encode($file);
        }

        if(isset($_FILES['doc']['name'])){
            $filenameDoc = basename($_FILES['doc']['name']);
            $fileDoc = file_get_contents($_FILES['doc']['tmp_name'], $filenameDoc);
            $fileDoc = base64_encode($fileDoc);
        }
        
		$return = $empModel->savePosition($action,$title,$hiring,$experience,$till,$id,$isteaching,$type,$course,$subject,$filename,$file,$filenameDoc,$fileDoc,$desc);

		if($return['err_code'] == 0)
		{
			// $id = $id;
			// if(!$id) $id = $return['last_id'];
			// $r = $empModel->savePositionDesc($desc,$id);
		}
        echo json_encode($return);
    }
    // public function deleteDivision(){
    //     $action = "delete";
    //     $code = $this->input->post('managementid');  
    //     $save_data = $this->employee->saveDivision($code, $action);
    //     echo $save_data;

    // }
    function deleteDivision(){
        $data=$this->employee->saveDivision($this->input->post("managementid"));
        echo $data;
        
    }
    public function deleteRow(){
        $toks = $this->input->post("toks");
        $id = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) :  $this->input->post("id");
        $info_type = $toks ? $this->gibberish->decrypt( $this->input->post("infotype"), $toks ) : $this->input->post("infotype");
        $empModel = new Employee();
        echo $empModel->deleteFromTable($info_type, $id);
    }
    function addrequesttype(){
       $data['job'] = $this->input->post("job");
       $data['rid'] = $this->input->post("rid");       
       $this->load->view('config/requesttype_detail', $data);     
    }

    //  public function saverequesttype(){
    //   $job = $this->input->post("job");
    //   $msg = "";
    //   if($job=="delete"){
    //    $rid = $this->input->post("rid"); 
    //   $query =  $this->db->query("delete from code_request_type where rid='{$rid}'"); 
    //   }else{  
      
    //   $rid = $this->input->post("rid"); 
    //   $code = $this->input->post("u_code");
    //   $description = $this->input->post("u_description"); 
    //   $this->db->query("CALL prc_code_requestype_set('{$rid}','{$code}','{description}')");
     
    //  if ($rid) {
    //       $msg = "Successfully Updated!";
    //  }
    //  else
    //  {
    //       $msg = "Successfully Saved!";
    //  }

    //   } 
    //  echo $msg;
    // }
    function saverequesttype(){
        $rid = $this->input->post("rid");
        $code = $this->input->post("u_code");
        $description = $this->input->post("u_description");
        $this->db->query("CALL prc_requestype_set('$rid','$code','$description')");          
    }

    
    public function dbrequesttypelist(){   
        # concat(lastname,\', \',firstname,\' \',middlename) as fullname
        $this->load->library('datatables');
        $this->datatables
             ->select('id,request_code,description')
             ->edit_column('id', 
                           '<a class="btn btn-info" href="#modal-view" tag="edit_d" data-toggle="modal" rid="$1"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;
                              <a class="btn btn-danger" href="#" tag="delete_d" rid="$1"><i class="glyphicon glyphicon-trash" ></i></a>', 
                           'id')                                                        
             ->from('code_request_type');
             
          # ->edit_column('id', '<input type="checkbox" value="$1">', 'id')   
          #->edit_column('userid', '<a href="profiles/edit/$1">$2</a>', 'userno, userid');
        $results = $this->datatables->generate('json');
        echo $results;
    }
    function loadprofileconfig(){
        $toks = $this->input->post("toks");
        if($toks){
            $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));
        }else{
            $data = $this->input->post();
        }
        // print_r($data);
        echo $this->user->eprofileconfig($data);
    }
    function loadprofileconfigdisplay(){
        $this->load->view("config/eprofileconfig");
    }
    function opengate(){
        echo $this->extras->opengate();
    }
    function saveltype(){
        $data = $this->input->post();
        echo $this->extras->saveltype($data);
    }
    function updateltype(){
        $data = $this->input->post();
        echo $this->extras->updateltype($data);
    }

    function loadClusterHistory(){
        echo $this->load->view('config/clustertype_history');
    }

    function tagBatch(){
        echo $this->load->view('config/tagBatch');
    }

    function savingBatchSched(){
        $data = $this->input->post();
        extract($data);
        if($employeeid[0] == "all"){
              $where_clause = 'WHERE 1 ';
              $datenow = date('Y-m-d');
              if($teachingType && $teachingType != "undefined") $where_clause .= "AND teachingtype = '$teachingType' ";
              if($department) $where_clause .= "AND deptid = '$department' ";
              if($status != "all" && $status != ''){
                if($status=="1"){
                  $where_clause .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
                }
                if($status=="0"){
                  $where_clause .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
                }
                if(is_null($status)) $where_clause .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
              }
              if($office && $office != 'All' && $office != 'all') $where_clause .= " AND office = '$office' ";
              $records = $this->employee->load201sort($where_clause);
              $employeeid = array();
              foreach ($records as $key => $value) {
                  $employeeid[] = $value['employeeid'];
              }
        }
        $bs = $this->db->query("SELECT * FROM code_type where code='$emptype'")->row();
        $schedid = $bs->schedid;

        $date             = new DateTime($bs->date_active);
        $date_orig = $date->format('Y-m-d');
        $date->modify('-1 day');
        $data['date_active'] = $date->format('Y-m-d');

        foreach ($employeeid as $key => $empid) {
            $this->db->query("DELETE FROM employee_schedule_history WHERE employeeid='{$empid}' AND DATE_FORMAT(dateactive,'%Y-%m-%d %H:%i:%s')='{$data['date_active']}".' 00:00:00'."';");

            $this->db->query("UPDATE employee SET date_active='$date_orig' WHERE employeeid='{$empid}'");

            $this->db->query("DELETE FROM employee_schedule WHERE employeeid='{$empid}'");
            $this->db->query("INSERT INTO employee_schedule(employeeid, starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,editedby,dateedit, weekly_sched) (SELECT '{$empid}',starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,'".$this->session->userdata("userid")."','{$data['date_active']}', weekly_flexible FROM code_schedule_detail WHERE schedid='{$schedid}')");

            $this->db->query("INSERT INTO employee_schedule_history(employeeid, starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,changeby,dateactive, weekly_sched) (SELECT '{$empid}',starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,'".$this->session->userdata("userid")."','{$data['date_active']}', weekly_flexible FROM code_schedule_detail WHERE schedid='{$schedid}')");

            $this->db->query("UPDATE employee SET empshift='{$schedid}', emptype = '$emptype' WHERE employeeid='{$empid}'");
        }

        echo "done";
    }
}

/* End of file configuration_.php */
/* Location: ./application/controllers/configuration_.php */