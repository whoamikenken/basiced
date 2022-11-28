<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee_ extends CI_Controller {

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *    http://example.com/index.php/welcome
   *  - or -  
   *    http://example.com/index.php/welcome/index
   *  - or -
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
    
    public function index()
    {
        # nothing
    }

    public function getAttendanceToday(){
        $this->load->model("leave");
        $this->load->model("ob_application");
        $present_data = $active_data = $leave_data = $ob_data = $leave_ob_data = array();
        $datenow = $this->extensions->getServerTime();
        $datenow = date("Y-m-d", strtotime($datenow));
        $active_employee = $this->extensions->getActiveEmployees();
        if($active_employee){
            foreach($active_employee as $row){
                $active_data[$row["employeeid"]] = $row["employeeid"];
            }
        }
        $leave_employee = $this->leave->getLeaveTodayEmployees($datenow);
        if($leave_employee){
            foreach($leave_employee as $row){
                $leave_data[$row["employeeid"]] = $row["employeeid"];
            }
        }

        $ob_employee = $this->ob_application->getObTodayEmployees($datenow);
        if($ob_employee){
            foreach($ob_employee as $row){
                $ob_data[$row["employeeid"]] = $row["employeeid"];
            }
        }

        $leave_ob_data = array_merge($ob_data, $leave_data);

        unset($active_data[""]);
        $present_employee = $this->time->getPresentEmployee($datenow);
        if($present_employee){
            foreach($present_employee as $row){
                $present_data[$row["user_id"]] = $row["user_id"];
            }
        }

        $active_data = array_diff_key($active_data, $present_data);
        $active_data = array_diff_key($active_data, $leave_ob_data);

        $data["present"] = count($present_data);
        $data["leave_ob"] = count($leave_ob_data);
        $data["absent"] = count($active_data);
        echo json_encode($data);
    }

    function loadExtrasFunction(){
      $toks = $this->input->post("toks");
      $data   = $this->input->post();
      if($toks){
        unset($data["toks"]);
        foreach($data as $key => $val){
          $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
      }
      $fnctn  = ($toks) ? $this->gibberish->decrypt($this->input->post("fnctn"), $toks) : $this->input->post("fnctn");
      echo $this->extras->$fnctn($data);
    }
    function viewModal(){
      $data = $this->input->post();
      if(!isset($data["toks"])){
        $folder = $data['folder'];
        $page = $data['page'];
        $this->load->view($folder.'/'.$page,$data);
      }else{
        $toks = $data["toks"];
        unset($data["toks"]);
        foreach($data as $key => $val){
          $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
        $folder = $data['folder'];
        $page = $data['page'];
        $this->load->view($folder.'/'.$page,$data);
      }
    }
    function goToEmpList(){
      $this->load->view('employee/employee');  
    }
    function echildren(){
      $toks = $this->input->post("toks");
      $data['title'] = '';
      $data['applicant'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicant"), $toks ) : $this->input->post("applicant");
      $this->load->view('employee/employee_children', $data);  
    }

    function childrenDataTable(){
      $employeeid = $this->input->post("employeeid");
      $table = $this->input->post("table");
      $data['childrenlist'] = $this->extras->loadChildrendata($employeeid,$table);
      $this->load->view('employee/children_data', $data);
    }
    function efamily(){
      $toks = $this->input->post("toks");
      $data['title'] = '';
      $data['applicant'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicant"), $toks ) : $this->input->post("applicant");
      $this->load->view('employee/employee_family', $data);  
    }
    function legitimate(){
      $data['title'] = '';
      $this->load->view('employee/legitimate_relations', $data);
    }
    function education(){
      $data['title'] = '';
      $toks = $this->input->post("toks");
      $data['applicant'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicant"), $toks ) : $this->input->post("applicant");
      $this->load->view('employee/educational_background', $data);
    }
    function workhistory(){
      $data['title'] = '';
      $toks = $this->input->post("toks");
      $data['applicant'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicant"), $toks ) : $this->input->post("applicant");
      $this->load->view('employee/workhistory', $data);
    }
    function workhistoryunrelated(){
      $data['title'] = '';
      $toks = $this->input->post("toks");
      $data['applicant'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicant"), $toks ) : $this->input->post("applicant");
      $this->load->view('employee/workhistoryunrelated', $data);
    }
    function workhistoryrelated(){
      $data['title'] = '';
      $this->load->view('employee/workhistoryrelated', $data);
    }
    function eligibilities(){
      $data['title'] = '';
      $toks = $this->input->post("toks");
      $data['applicant'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicant"), $toks ) : $this->input->post("applicant");
      $this->load->view('employee/eligibilities', $data);
    }
    function sctt(){
      $data['title'] = '';
      $toks = $this->input->post("toks");
      $data['applicant'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicant"), $toks ) : $this->input->post("applicant");
      $this->load->view('employee/sctt', $data);
    }
    function ot(){
      $data['title'] = '';
      $data['applicant'] = $this->input->post("applicant");
      $this->load->view('employee/other_credentials', $data);
    }

    function oc(){
      $data['title'] = '';
      $toks = $this->input->post("toks");
      $data['applicant'] = $toks ? $this->gibberish->decrypt( $this->input->post("formdata"), $toks ) : $this->input->post("applicant");
      $this->load->view('employee/other_credential', $data);
    }

    function affiliation(){
      $data['title'] = '';
      $this->load->view('employee/affiliation', $data);
    }
    function awards(){
      $data['title'] = '';
      $this->load->view('employee/awards', $data);
    }
    function skills(){
      $data['title'] = '';
      $this->load->view('employee/skills', $data);
    }
    function language(){
      $data['title'] = '';
      $this->load->view('employee/language', $data);
    }
    function seminar(){
      $data['title'] = '';
      $this->load->view('employee/seminar', $data);
    }
    function pts(){
      $data['title'] = '';
      $this->load->view('employee/pts', $data);
    }
     function pts_pdp1(){
      $data['title'] = '';
      $this->load->view('employee/pts_pdp1', $data);
    }
    function pts_pdp2(){
      $data['title'] = '';
      $this->load->view('employee/pts_pdp2', $data);
    }
    function pts_pdp3(){
      $data['title'] = '';
      $this->load->view('employee/pts_pdp3', $data);
    }
    function pgd(){
      $data['title'] = '';
      $this->load->view('employee/pgd', $data);
    }
    function researches(){
      $data['title'] = '';
      $this->load->view('employee/researches', $data);
    }
    function ar(){
      $data['title'] = '';
      $this->load->view('employee/ar', $data);
    }
    function scho(){
      $data['title'] = '';
      $this->load->view('employee/scholarship', $data);
    }
    function scs(){
      $data['title'] = '';
      $this->load->view('employee/scs', $data);
    }
    function org(){
      $data['title'] = '';
      $this->load->view('employee/org', $data);
    }
    function tw(){
      $data['title'] = '';
      $this->load->view('employee/tw', $data);  
    }
    function resource(){
      $data['title'] = '';
      $this->load->view('employee/resource', $data);  
    }
    function community(){
      $data['title'] = '';
      $this->load->view('employee/community', $data);  
    }
    function administrative(){
      $data['title'] = '';
      $this->load->view('employee/administrative', $data);  
    }
  //ADDED 07-28-2017
  function eEmergencyContact(){
    $toks = $this->input->post("toks");
      $data['title'] = $tbl_id = $data['type'] = '';
      $data['applicant'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicant"), $toks ) : $this->input->post("applicant");
      $data['applicantId'] = $toks ? $this->gibberish->decrypt( $this->input->post("applicantId"), $toks ) : $this->input->post("applicantId");
      $data['gender'] = $toks ? $this->gibberish->decrypt( $this->input->post("gender"), $toks ) : $this->input->post("gender");
      $data['civil_status'] = $toks ? $this->gibberish->decrypt( $this->input->post("civil_status"), $toks ) : $this->input->post("civil_status");
      $tbl_id = $toks ? $this->gibberish->decrypt( $this->input->post("tbl_id"), $toks ) : $this->input->post("tbl_id");
      if($tbl_id != '') $data['type'] = $this->extras->getEmergencyType($tbl_id);
      $this->load->view('employee/employee_emergencyContact', $data);  
    }
  function eSkill(){
      $data['title'] = '';
      $this->load->view('employee/employee_skill', $data);  
    }
  
  function loadLoanHistory()
  {
    $data = $this->input->post();
    $this->load->view('employee/loanhistory', $data);  
  }

  function isComplete(){
    $toks = $this->input->post("toks");
    $id = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) : $this->input->post("id");
    $tbl = $toks ? $this->gibberish->decrypt( $this->input->post("tbl"), $toks ) : $this->input->post("tbl");
    echo $this->extras->isComplete($id, $tbl);
  }
  
  function editEStat(){

      $prepend = $currentMgmt = $currentDept = $currentEStat = $currentPos = $currentDatepos = "";
      $toSave = false;
      $return = array("err_code"=>0, 'msg'=>"Success.");
      $data = $this->input->post();
      $present = $this->employee->getPresentEStatus($data['employeeid']);

      if($present->num_rows() > 0 ){
        // if($present->row(0)->managementid <> $data['managementid'])     $toSave = true; 
        if($present->row(0)->deptid <> $data['deptid'])                 $toSave = true;
        if($present->row(0)->office <> $data['office'])                 $toSave = true; 
        if($present->row(0)->employmentstat <> $data['employmentstat']) $toSave = true; 
        if($present->row(0)->positionid <> $data['positionid'])         $toSave = true; 
        if($present->row(0)->dateposition <> $data['datepos'])          $toSave = true; 

        $resdatepos = $present->row(0)->dateposition ;
        if($resdatepos == "0000-00-00")  $resdatepos = '';
        if($resdatepos <> $data['datepos'])     $toSave = true; 

        $dateres = $present->row(0)->dateresigned2 ;
        if($dateres == "0000-00-00")  $dateres = '';
        if($dateres <> $data['dateresigned'])     $toSave = true;
      }
      if($toSave){

        $res = $this->employee->saveEmploymentStatusChanges($data['employeeid'],$data['deptid'],$data['office'],$data['employmentstat'],$data['positionid'],$data['datepos'],$data['dateresigned'],$data['reason']);

        if($res){
          $employment_history = $this->employee->getEmploymentStatusHistory($data['employeeid'], "true", "");
          
          if($employment_history){
            
            foreach ($employment_history as $key => $obj):
                if($obj->dateresigned == "0000-00-00"){
                    $obj->dateresigned = "";
                }
                else{
                    $obj->dateresigned;
                }
              $prepend = 
                '<div class="row" style="padding-bottom: 2px;">
                    <span class="col-xs-12 col-md-2 text-center">'.$obj->deptdesc.'</span>
                    <span class="col-xs-12 col-md-2 text-center">'.$obj->officedesc.'</span>
                    <span class="col-xs-12 col-md-1 text-center">'.$obj->statdesc.'</span>
                    <span class="col-xs-12 col-md-2 text-center">'.$obj->posdesc.'</span>
                    <span class="col-xs-12 col-md-1 text-center">'.$obj->dateposition.'</span>
                    <span class="col-xs-12 col-md-2 text-center">'.$data['dateresigned'].'</span>
                    <span class="col-xs-12 col-md-2">
                    <span class="pull-center">
                    <a class="btn btn-info view_seperation_reason" dstatid="'.$obj->id.'" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-eye-open"></i></a>&nbsp;
                         <a class="btn btn-warning delete_estat_history" estatid="'.$obj->id.'"><i class="glyphicon glyphicon-trash"></i></a>        
                    </span>
                    </span>
                </div>';         
              
            endforeach;
          }

          // $currentMgmtDesc = $this->extras->getemployeemlevel($data['managementid']);
          $currentDeptDesc = $this->extras->getemployeedepartment($data['deptid']);
          $currentOfficeDesc = $this->extras->getemployeeoffice($data['office']);
          $currentEStatDesc = $this->extras->getemployeestatus($data['employmentstat']);
          $currentPosDesc = $this->extras->showPosDesc($data['positionid']);

          $return = array("err_code"=>0, 'msg'=>"Successfully saved!", 'prepend'=>$prepend, 
            // 'currentMgmt'     =>$data['managementid'], 
            'currentDept'     =>$data['deptid'], 
            'currentEStat'    =>$data['employmentstat'], 
            'currentPos'      =>$data['positionid'], 
            'currentDatepos'  =>$data['datepos'],
            'currentDateres'  =>$data['dateresigned'],
            // 'currentMgmtDesc' =>$currentMgmtDesc, 
            'currentDeptDesc' =>$currentDeptDesc,
            'currentOfficeDesc' =>$currentOfficeDesc,  
            'currentEStatDesc'=>$currentEStatDesc,
            'currentReason'=>$data['reason'],  
            'currentPosDesc'  =>$currentPosDesc);
        }
        else      $return = array("err_code"=>2, 'msg'=>"Unable to save changes.");

      }else{
        $return = array("err_code"=>1, 'msg'=>"No changes were made.");
      }
      echo json_encode($return);

    }


    function deleteEStatHistory(){
      $return = array("err_code"=>0, 'msg'=>"Success.");
      $toks = $this->input->post("toks");
      $data   = $this->input->post();
      if($toks){
        unset($data["toks"]);
        foreach($data as $key => $val){
          $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
      }
      $empModel = new Employee();
      $res = $empModel->deleteEmploymentStatusHistory($data['estatid']);
      if($res) $return = array("err_code"=>0, 'msg'=>"Success.");
      else $return = array("err_code"=>2, 'msg'=>"Failed to delete.");

      echo json_encode($return);
    }

    function deleteData(){
      $return = array("err_code"=>0, 'msg'=>"Success.");
      $toks = $this->input->post('toks');
      $data = $this->input->post();
        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       }
      $table = $data["table"];
      $tbl_id = $data["tbl_id"];
      $res = $this->employee->deleteData($data["tbl_id"], $data["table"]);
      if($res) $return = array("err_code"=>0, 'msg'=>"Success.");
      else $return = array("err_code"=>2, 'msg'=>"Failed to delete.");

      echo json_encode($return);
    }

    
    
    function validateinfo(){
        $status = 1;
        $message = "Done saving.";
        $this->load->model('profile');
        $toks = $this->input->post("toks");
        $formdata = $this->input->post("formdata");
        $data = $formdata ? Globals::convertFormDataToArray($this->gibberish->decrypt( $formdata, $toks )) : $formdata;
        $employeeid = isset($data['employeeid']) ? $data['employeeid'] : $this->input->post('employeeid');
        $newJob =   isset($data['newJob']) ? $data['newJob'] : $this->input->post('newJob');
        if($employeeid){
          $empid = $this->employee->getempdatacol('employeeid', $employeeid);
          if(!$empid){
            $message = "New employee was successfully added.";
          }
        }
        # end for ica-hyperion 21442

        if($newJob == 'new'){
          if($empid){
              echo "<user>
                    <status>2</status>
                    <message>Employee ID already exists.</message>
                  </user>";
              die;
          }
        }

        $job = isset($data['job']) ? $data['job'] : $this->input->post('job');
        switch($job){
            
            case "employee/deduction_info": /** DEDUCTIONS */
                $employee_info = $this->session->userdata("personalinfo");
                $deduction =  isset($data['deduction_drop']) ? $data['deduction_drop'] : $this->input->post('deduction_drop');
                $memberid =  isset($data['memberid']) ? $data['memberid'] : $this->input->post('memberid');
                $schedule =  isset($data['schedule']) ? $data['schedule'] : $this->input->post('schedule');
                $period = isset($data['period_drop']) ? $data['period_drop'] : $this->input->post('period_drop');
                
                $amount =  isset($data['job']) ? $data['amountdeduct'] : $this->input->post('amountdeduct');
                $nocutoff =  isset($data['nocutoff']) ? $data['nocutoff'] : $this->input->post('nocutoff');
                $datefrom =  isset($data['datefrom']) ? $data['datefrom'] : $this->input->post('datefrom');
                $dateto =  isset($data['dateto']) ? $data['dateto'] : $this->input->post('dateto');          
              
                $dsetfrom = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
                $dsetto = $dateto ? date("Y-m-d",strtotime($dateto)) : "0000-00-00";
                $this->profile->saveEmployeeDeduction($employee_info[0]['employeeid'], $deduction, $memberid, '', $dsetfrom, $dsetto, $amount, $nocutoff, $schedule, $period);
                                                                  
            break;
            case "employee/income_info": /** INCOME */
                $employee_info = $this->session->userdata("personalinfo");
                $income = isset($data['income_drop']) ? $data['income_drop'] : $this->input->post('income_drop');   
                $incomebase = isset($data['incomebase_drop']) ? $data['incomebase_drop'] : $this->input->post('incomebase_drop');   
                $remarks =  isset($data['remarks']) ? $data['remarks'] : $this->input->post('remarks');   
                $schedule =  isset($data['schedule']) ? $data['schedule'] : $this->input->post('schedule');   
                $period = isset($data['period_drop']) ? $data['period_drop'] : $this->input->post('period_drop');   
                  
                $amount =  isset($data['amountincome']) ? $data['amountincome'] : $this->input->post('amountincome');   
                $nocutoff =  isset($data['nocutoff']) ? $data['nocutoff'] : $this->input->post('nocutoff');   
                $datefrom =  isset($data['datefrom']) ? $data['datefrom'] : $this->input->post('datefrom');   
                $dateto =  isset($data['dateto']) ? $data['dateto'] : $this->input->post('dateto');          
              
                $dsetfrom = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
                $dsetto = $dateto ? date("Y-m-d",strtotime($dateto)) : "0000-00-00";
        
                $this->profile->saveEmployeeIncome($employee_info[0]['employeeid'], $income, $income['remarks'], $dsetfrom, $dsetto, $amount, $nocutoff, $incomebase, $schedule, $period);
 
            break;

            case "employee/income_adj_info": /** INCOME */
              $employee_info = $this->session->userdata("personalinfo");
                $income = $this->input->post("income_drop");
                $incomebase = $this->input->post("incomebase_drop");
                $remarks = $this->input->post("remarks");
                $schedule = $this->input->post("schedule");
                $period = $this->input->post("period_drop");
                $deduct = $this->input->post("deduct_income");
                $taxable = $this->input->post("taxable_income");
                  
                $amount = $this->input->post("amountincome");
                $nocutoff = $this->input->post("nocutoff");
                $datefrom = $this->input->post("datefrom");
                $dateto = $this->input->post("dateto");              
              
                $dsetfrom = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
                $dsetto = $dateto ? date("Y-m-d",strtotime($dateto)) : "0000-00-00";

                $this->profile->saveEmployeeIncomeAdj($employee_info[0]['employeeid'], $income, $income['remarks'], $dsetfrom, $dsetto, $amount, $nocutoff, $incomebase, $schedule, $period, $deduct, $taxable);
 
            break;

            case "employee/loan_info": /** LOAN */
                $employee_info = $this->session->userdata("personalinfo");
                $loan = $this->input->post("dloan_drop");
                $basedon = $this->input->post("basedon");
                $remarks = $this->input->post("remarks");
                $schedule = $this->input->post("dschedule");
                $period = $this->input->post("dperiod_drop");
                $id = $this->input->post('id');
                $startingamount = $this->input->post("startingamountloan");
                $currentamount = $this->input->post("currentamount");
                $amount = $this->input->post("amountloan");
                $famount = $this->input->post("dfamount");
                $nocutoff = $this->input->post("nocutoff");
                $datefrom = $this->input->post("ddatefrom");
                $dateto = $this->input->post("dateto");             
                $dsetfrom = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
                $dsetto = $dateto ? date("Y-m-d",strtotime($dateto)) : "0000-00-00";
                $user           = $this->session->userdata('username');
                    
                if($id){
                    list($status, $message) = $this->profile->saveEmployeeLoanHistory($employee_info[0]['employeeid'], $loan, $dsetfrom, $dsetto, $currentamount, $amount, $currentamount, $schedule, $period, "UPDATE", $user, $startingamount, $remarks, $datefrom, $famount, $nocutoff, $basedon);
                }
                else{   
                    $remainingBalance = 0; $status = 1;
                    $query = $this->profile->checkEmployeeLoan($loan, $employee_info[0]['employeeid']);

                    if ($query->num_rows() > 0) {
                        $remainingBalance = ($query->row(0)->amount * ($query->row(0)->nocutoff -1)) + $query->row(0)->famount;
                    }

                    if ($remainingBalance == 0 || $remainingBalance == "" || $remainingBalance == NULL) {
                        list($status, $message) = $this->profile->saveEmployeeLoanHistoryPRC($id,$employee_info[0]['employeeid'],$loan,$loan['remarks'],$dsetfrom,$dsetto,$amount,$startingamount, $famount,$nocutoff,$basedon,$schedule,$period, $currentamount);
                    }
                    else{
                        $status = 1;
                        $message = "Failed to Saved! Due to have remaining balance!";
                    }
                }
              
            break;
            case "employee/income_info_oth": /** INCOME */
                $employee_info = $this->session->userdata("personalinfo");
                $income = $this->input->post("income_drop");
                $amount = $this->input->post("amountincome");      
                $pos    = $this->input->post("pos");      
                $this->profile->saveEmployeeOtherIncome($employee_info[0]['employeeid'], $income, $amount, $pos);
 
            break;
            case "employee/schedule_info": /** SCHEDULE */
              $employee_info = $this->session->userdata("personalinfo");
              $flexible = $this->input->post("fsched");
              $flexible = $this->input->post("flexible");
              $flexi_hours = $flexible == 'YES' ? $this->input->post("flexi_hours") : 0;
              $flexi_breaktime = $flexible == 'YES' ? $this->input->post("flexi_breaktime") : 0;
              
              $date_active = new DateTime($this->input->post("date_active"));

              $date_active->modify('-1 day');
              $date_active = $date_active->format('Y-m-d');
              // echo "<pre>"; print_r(explode("|",$this->input->post("timesched"))); die;
              ///< schedule will not be changed if date_active is already processed
              $processed = $this->db->query("SELECT * FROM payroll_employee_attendance_nt WHERE '$date_active' <= cutoffend");
              if($processed->num_rows() == 0){

                $this->db->query("DELETE FROM employee_schedule_history WHERE employeeid='{$employee_info[0]['employeeid']}' AND DATE_FORMAT(dateactive,'%Y-%m-%d %H:%i:%s')='$date_active".' 00:00:00'."'");
        
                  if($this->input->post("timesched")){
                        $sched_list = explode("|",$this->input->post("timesched"));
                        /** Clear user data first */ 
                        $this->db->query("DELETE FROM employee_schedule WHERE  employeeid='{$employee_info[0]['employeeid']}'");
                        /** end of clearing data */
                        $loop = 0;
                        /** Insert new data */
                        $firstloop = true;
                        $cdow = '';
                        $dowArr = array(1,2,3,4,5,6,0);
                        // echo "<pre>"; print_r($dowArr); die;
                        foreach($sched_list as $slist){
                              $nosched = 0;
                              $halfsched = 0;
                              $loop++;
                              list($dw,$tsched,$tstart,$astart,$tsecond,$asecond,$nosched,$halfsched,$early_d,$teaching,$courseval,$sectionval,$subjectval,$aimsval,$weekly_sched) = explode("~u~",$slist);
                                $types = $teaching;
                                $extime = explode("-",$tsched);
                                $start_time = date("H:i:s",strtotime($extime[0]));
                                $end_time = date("H:i:s",strtotime($extime[1]));
                                $tstart = date("H:i:s",strtotime($tstart));
                                $astart = date("H:i:s",strtotime($astart));
                                $tsecond = date("H:i:s",strtotime($tsecond));
                                $asecond = date("H:i:s",strtotime($asecond));
                                $early_d = date("H:i:s",strtotime($early_d));
                                $course = $courseval;
                                $section = $sectionval;
                                $subject = $subjectval;
                                $aims = $aimsval;
                                $dow = 0;
                                switch($dw){
                                case "M" : $dow = 1; break;
                                case "T" : $dow = 2; break;
                                case "W" : $dow = 3; break;
                                case "TH" : $dow = 4; break;
                                case "F" : $dow = 5; break;
                                case "S" : $dow = 6; break;
                                case "SUN" : $dow = 0; break;
                                }
                                // if(in_array($dow, $dowArr)) $dowArr[] = $dow;

                                if(!$firstloop){
                                  if($cdow != $dow){
                                    if (($key = array_search($cdow, $dowArr)) !== false) {
                                        unset($dowArr[$key]);
                                    }
                                  }
                                }
                                if(in_array($dow, $dowArr)){
                                    $this->profile->saveEmployeeSchedule($employee_info[0]['employeeid'],$start_time,$end_time,$dw,$dow,$tstart,$astart,$tsecond,$asecond,$nosched,$halfsched,$early_d,$this->session->userdata("userid"),$date_active,$types,$flexible,$flexi_hours,$flexi_breaktime,'day',$course,$section,$subject,$aims, $weekly_sched);
                                } 
                       
                                $firstloop = false;
                                $cdow = $dow;
                        }
                        $this->profile->saveEmployeeScheduleHistory($employee_info[0]['employeeid']);
                        // echo "<pre>"; print_r($this->db->last_query()); die;
                        $message = "Successfully save schedule!";
                }
            }else $message = "Failed to save schedule. Effectivity date is already processed.";
                 
                break;
                case "employee/salary_info": /** SALARY */
                $employee_info = $this->session->userdata("personalinfo");

                $mon_basic = $this->input->post("mon1");
                $mon_lec = $this->input->post("mon2");
                $mon_lab = $this->input->post("mon3");

                $daily_basic = $this->input->post("dail1");
                $daliy_lec = $this->input->post("dail2");
                $daliy_lab = $this->input->post("dail3");

                $hr_basic = $this->input->post("hr1");
                $hr_lec = $this->input->post("hr2");
                $hr_lab = $this->input->post("hr3");

                $min_basic = $this->input->post("min1");
                $min_lec = $this->input->post("min2");
                $min_lab = $this->input->post("min3");
                $salary_type = $this->input->post("salary_type");
                $tax_status = $this->input->post("tax_status");

                $this->session->userdata("personalinfo",array("salary_base"=>$salary_type,"tax_status"=>$tax_status));
                $this->profile->saveEmployeeSalary($salary_type, $tax_status, $employeeid, $mon_basic, $mon_lec, $mon_lab, $daily_basic, $daliy_lec, $daliy_lab, $hr_basic, $hr_lec, $hr_lab, $min_basic, $min_lec, $min_lab);
            break;
        }
        
        /** Checker here  */
        if($employee_info[0]['employeeid']==""){
           $status = 2;
           $message = "Employee ID is required."; 
        }else if($employee_info[0]['lname']==""){
           $status = 2;
           $message = "Last name is required."; 
        }else if($employee_info[0]['fname']==""){
           $status = 2;
           $message = "First name is required."; 
        }
       
        echo "<user>
                <status>{$status}</status>
                <message>{$message}</message>
              </user>";
    } 
    function checkhasssession(){
        $status = 0;
        $message = "Please complete all information on this tab, then click save button below before you continue to other tabs";
        if($this->session->userdata("personalinfo")){
           $employee_info = $this->session->userdata("personalinfo");
           if($employee_info[0]['employeeid']!="") $status = 1; 
        }
        echo "<user>
                <status>{$status}</status>
                <message>{$message}</message>
              </user>";
    }
    function deletededuction(){
        $employeeid= $this->input->post('employeeid');
        $code= $this->input->post('code');
        # echo "delete from employee_deduction WHERE employeeid='{$employeeid}' and code_deduction='{$code}'";
        $this->db->query("delete from employee_deduction WHERE employeeid='{$employeeid}' and code_deduction='{$code}'");
        /** recreate session */
        $temparrs = array();
        foreach($this->session->userdata("deductions") as $deduct){if($deduct['code_deduction']!=$code) array_push($temparrs, $deduct);}
        $this->session->set_userdata("deductions",$temparrs);
    }
    function deletedincome(){
        $employeeid= $this->input->post('employeeid');
        $code= $this->input->post('code');
        # echo "delete from employee_deduction WHERE employeeid='{$employeeid}' and code_deduction='{$code}'";
        $this->db->query("delete from employee_income WHERE employeeid='{$employeeid}' and code_income='{$code}'");
        /** recreate session */
        $temparrs = array();
        foreach($this->session->userdata("income") as $deduct){if($deduct['code_income']!=$code) array_push($temparrs, $deduct);}
        $this->session->set_userdata("income",$temparrs);
    }

    function batchScheduleDateActive(){
        $toks = $this->input->post("toks");
        $emptype = $toks ? $this->gibberish->decrypt( $this->input->post("emptype"), $toks ) :$this->input->post('emptype');
        $empshift= $toks ? $this->gibberish->decrypt( $this->input->post("empshift"), $toks ) :$this->input->post('empshift');
        echo $this->attendance->batchScheduleDateActive($emptype);
    }

    function saveempshift(){
      $schedid = '';
        $toks = $this->input->post("toks");
        $emptype = $toks ? $this->gibberish->decrypt( $this->input->post("emptype"), $toks ) :$this->input->post('emptype');
        $empshift= $toks ? $this->gibberish->decrypt( $this->input->post("empshift"), $toks ) :$this->input->post('empshift');
        $employeeid= $toks ? $this->gibberish->decrypt( $this->input->post("employeeid"), $toks ) :$this->input->post('employeeid');
        $q = $this->db->query("select DISTINCT a.schedid,a.schedcode,a.description, b.code from code_schedule a inner join code_type b ON b.schedid=a.schedid where a.schedcode<>'0'"
                              .($emptype?" AND b.code='{$emptype}'":""))->result();
        if(count($q)>0){
            foreach($q as $row){
                if($row->code==$emptype){
                  $schedid = $row->schedid;
                  $this->db->query("UPDATE employee set empshift = '$schedid' WHERE employeeid = '$employeeid'");
                }
            }
        }
        echo $schedid;
    }

    function call_shiftschedule(){
      $toks = $this->input->post("toks");
        $emptype = $toks ? $this->gibberish->decrypt( $this->input->post("emptype"), $toks ) :$this->input->post('emptype');
        $empshift= $toks ? $this->gibberish->decrypt( $this->input->post("empshift"), $toks ) :$this->input->post('empshift');

        $return = "<option value=''>Choose a Shift ...</option>";
        $q = $this->db->query("select DISTINCT a.schedid,a.schedcode,a.description,b.code from code_schedule a inner join code_type b ON b.schedid=a.schedid where a.schedcode<>'0'"
                              .($emptype?" AND b.code='{$emptype}'":""))->result();
        if(count($q)>0){
            foreach($q as $row){
                $return .= "<option".($row->code==$emptype?" selected":"")." value='{$row->schedid}'>{$row->description}</option>";
            }
        }     
        $return .= "<option".('nocluster'==$emptype?" selected":"")." value='noschedule'>-NO SCHEDULE-</option>";
        echo $return;      
    }

    function saveShiftSchedule(){
      $toks = $this->input->post("toks");
        $empshift     = $toks ? $this->gibberish->decrypt( $this->input->post("empshift"), $toks ) : $this->input->post('empshift');
        $date_active  = $toks ? $this->gibberish->decrypt( $this->input->post("date_active"), $toks ) : $this->input->post('date_active');
        $prev_date_active = $toks ? $this->gibberish->decrypt( $this->input->post("prev_date_active"), $toks ) : $this->input->post("prev_date_active");
        $employeeid   = $toks ? $this->gibberish->decrypt( $this->input->post("employeeid"), $toks ) : $this->input->post('employeeid');
        $tnt          = $toks ? $this->gibberish->decrypt( $this->input->post("tnt"), $toks ) : $this->input->post('tnt');

        $user = $this->session->userdata('userid');

        $return = array("err_code"=>0, 'msg'=>"Success.");

        ///< check date_active processed -1 day

        // $prev_date_active = new DateTime($prev_date_active);

        // $prev_date_active->modify('-1 day');
        // $prev_date_active = $prev_date_active->format('Y-m-d');

        $date = new DateTime($date_active);
        $date_orig = $date->format('Y-m-d');
        $date->modify('-1 day');
        $date_active = $date->format('Y-m-d');

        ///< schedule will not be changed if date_active is already processed
        $tbl = '';
        if($tnt == 'teaching') $tbl = 'payroll_employee_attendance';
        else                   $tbl = 'payroll_employee_attendance_nt';

        $processed = $this->db->query("SELECT * FROM $tbl WHERE '$date_active' <= cutoffend");
        if($processed->num_rows() == 0){

              // if($prev_date_active == $date_active){
                ///< if change schedule within 1 day, prev sched with the given date will be removed
                // $prev_date_active .= ' 00:00:00';
            $this->db->query("DELETE FROM employee_schedule_history WHERE employeeid='$employeeid' AND DATE_FORMAT(dateactive,'%Y-%m-%d %H:%i:%s')='$date_active".' 00:00:00'."';");
              // }

            $this->db->query("UPDATE employee SET empshift = '$empshift', date_active = '$date_orig' WHERE  employeeid='$employeeid'");
        
            if($empshift == "noschedule"){

                $datefornosched = date('Y-m-d H:i:s', strtotime('-1 minutes', strtotime($date_active)));

                // $this->db->query("DELETE FROM employee_schedule WHERE employeeid='$employeeid' AND TYPE != 'teaching';");
                $this->db->query("DELETE FROM employee_schedule WHERE employeeid='$employeeid'");
                $this->db->query("
                                  INSERT INTO employee_schedule_history (employeeid, DAYOFWEEK, idx, no_schedule,changeby,dateactive) (
                                  SELECT '$employeeid',day_code, day_index,'1','$user','$datefornosched' FROM code_daysofweek);"
                                  );
            }else{
                // $this->db->query("CALL prc_employee_schedule_pershift('$employeeid','$empshift','','$date_active','$user')");
            $this->db->query("INSERT INTO employee_schedule(employeeid, starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,editedby,dateedit, weekly_sched) (SELECT '$employeeid',starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,'".$this->session->userdata("userid")."','$date_active', weekly_flexible FROM code_schedule_detail WHERE schedid='$empshift')");

              $this->db->query("INSERT INTO employee_schedule_history(employeeid, starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,changeby,dateactive, weekly_sched) (SELECT '$employeeid',starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,'".$this->session->userdata("userid")."','$date_active', weekly_flexible FROM code_schedule_detail WHERE schedid='$empshift')");
            }
        }else $return = array("err_code"=>3, 'msg'=>"Failed. Effectivity date is already processed.");
        echo json_encode($return);

    }


  
  //Added 5-26-17
  function getManagementLevel(){
    $toks = $this->input->post("toks");
    $deptid = $toks ? $this->gibberish->decrypt($this->input->post('deptid'), $toks) : $this->input->post('deptid');
    $return = "";
    
        $q = $this->db->query("SELECT managementid FROM code_office where code = '{$id}'")->result();
        foreach($q as $row){
            $return = $row->managementid;
         }   
     
        echo $return;      
    }
  
  //ADDED 8-16-2017
  function empList(){
        $active = $this->input->post('active');
    $this->load->view('employee/employeelist', $active);  
    }


  // for education & professional background
  function getEPB(){
    $empID = $this->session->userdata("username");
    $data = "";
    $tbl = array(
                'employee_education',
                'employee_eligibilities',
                'employee_subj_competent_to_teach',
                'employee_work_history_unrelated',
                'employee_work_history_related',
                'employee_work_history'
                );
    for($i = 0; $i < count($tbl); $i++){
      $query = $this->db->query("SELECT * FROM {$tbl[$i]} WHERE employeeid='{$empID}'")->result();
      if($i > 0) 
        $data .= "|". count($query);
      else
        $data .= count($query);
    }
    echo $data;
  }
  // end of new function added for #ica-hyperion 21090

  function getTSW(){
    $empID = $this->session->userdata("username");
    $data = "";
    $tbl = array(
                'employee_pts',
                'employee_pts_pdp1',
                'employee_pgd',
                'employee_researches',
                'employee_awardsrecog',
                'employee_scholarship',
                'employee_scs',
                'employee_workshops',
                'employee_resource',
                'employee_proorg',
                'employee_community',
                'employee_administrative'
                );
    for($i = 0; $i < count($tbl); $i++){
    $query = $this->db->query("SELECT * FROM {$tbl[$i]} WHERE employeeid='{$empID}'")->result();
    if($i > 0) 
        $data .= "|". count($query);
    else
        $data .= count($query);
    }
    echo $data;
  }

  function saveNewEmployee(){
    $api_url = "";
    if($_SERVER["HTTP_HOST"] == "192.168.2.97") $api_url = "192.168.2.97/povedadtr/index.php/aims_/addEmployeeToAims";
    else if(($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" || $_SERVER["HTTP_HOST"] == "poveda-hris.pinnacle.com.ph") && strpos($_SERVER["REQUEST_URI"], 'hristrng') !== false) $api_url = "https://".$_SERVER["HTTP_HOST"]."/hristrng/index.php/aims_/addEmployeeToAims";
    else if(($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" || $_SERVER["HTTP_HOST"] == "poveda-hris.pinnacle.com.ph") && strpos($_SERVER["REQUEST_URI"], 'hris') !== false) $api_url = "https://".$_SERVER["HTTP_HOST"]."/hris/index.php/aims_/addEmployeeToAims";
    $response = array();
    $toks = $this->input->post("toks");
    $insert_data = $this->input->post();
    unset($insert_data["toks"]);
    foreach($insert_data as $key => $val){
      $insert_data[$key] = $this->gibberish->decrypt($val, $toks);
    }
    // echo "<pre>"; print_r($insert_data); die; 
    if ($this->employee->isEmployeeIDExist($insert_data['employeeid']) != 0) {
      $response['msg'] = "Duplicate Employee ID";
      $response['status'] = 3;
      json_encode($response);
      die;
    }

    $insert_data['campusid'] = $insert_data['campus'];
    $insert_data['added_by'] = $this->session->userdata('username');
    unset($insert_data['campus']);
    if(!in_array("", $insert_data)){
      $res = $this->employee->addNewEmployee($insert_data);
      unset($insert_data['added_by']);
      /*insert to aims*/
      if(isset($insert_data["aimcheckbox"]) && $insert_data["aimcheckbox"]){
        unset($insert_data["aimcheckbox"]);
        // $api_url = "192.168.2.97/povedadtr/index.php/aims_/addEmployeeToAims";

        $empinfo = json_encode($insert_data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $empinfo); 
        curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
      }
      
      // var_dump($res); die;
      if($res){ 
        $account_data = array(        #insert new account
        "username" => $insert_data['employeeid'],
        "lastname" => $insert_data['lname'],
        "firstname" => $insert_data['fname'],
        "middlename" => $insert_data['mname'],
        "campus" => $insert_data['campusid'],
        "password" => md5(strtoupper($insert_data['lname'])),
        "status" => "ACTIVE",
        "type" => "EMPLOYEE",
        "ipadd" => $this->input->ip_address(),
        "createdby" => $this->session->userdata('username')
        );
        $res = $this->employee->addNewEmployeeAccount($account_data);
        if(($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" || $_SERVER["HTTP_HOST"] == "poveda-hris.pinnacle.com.ph") && strpos($_SERVER["REQUEST_URI"], 'hris') !== false) $this->postDataToAllCard($insert_data);
        $response['msg'] = "Successfully added employee";
        $response['status'] = 1;
    }
    else{
        $response['msg'] = "Failed to add employee";
        $response['status'] = 1;
    }
    }else
    {
      $response['msg'] = "Please complete all required fields. ";
      $response['status'] = 0;
    }
    echo json_encode($response);
  }
  function postDataToAllCard($employee){
        $api_url = Globals::apiUrl()."/api/person/addemployee";
        $empinfo = array(
          "PersonType" => "E",
          "IDNumber"  => $employee['employeeid'],
          "FirstName" => $employee['fname'],
          "MiddleName" => $employee['mname'],
          "LastName" => htmlentities( (string) $employee['lname'], ENT_QUOTES, 'utf-8', FALSE),
          "CampusName" => "Pinnacle Technologies Inc.",
          "DepartmentName" => isset($employee['deptid']) ? $employee['deptid'] : " ",
          "PositionName" => isset($employee['positionid']) ? $employee['positionid'] : " "
        );

        // $empinfo = json_encode($empinfo);
        $token = $this->extensions->getPostmanToken();
        $access_token = "Bearer ".$token;

        $headers = array(
            'Content-type: application/json',
            'Authorization: '.$access_token,
        );

       /* $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_POST, 1 ); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $empinfo); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);*/
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_PORT => "6271",
          CURLOPT_URL => $api_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>  "\r\n{
                \r\n\"PersonType\": \" ".$empinfo['PersonType']."\",
                \r\n\"IDNumber\": \" ".$empinfo['IDNumber']." \",
                \r\n\"FirstName\": \" ".$empinfo['FirstName']." \",
                \r\n\"MiddleName\": \" ".$empinfo['MiddleName']." \",
                \r\n\"LastName\": \" ".$empinfo['LastName']." \",
                \r\n\"CampusName\": \" ".$empinfo['CampusName']." \",
                \r\n\"DepartmentName\": \" ".$empinfo['DepartmentName']." \",
                \r\n\"PositionName\": \" ".$empinfo['PositionName']." !\"\r\n
              }\r\n",
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

        /*save to hyperion logs*/
        if($response){
          $this->employee->saveALLCARDLogs($empinfo);
        }
  }

  function uploadPhoto(){
    $data['fileName'] = $this->input->post("filename");
    $data['applicant'] = $this->input->post("applicant");
    $this->load->view("employee/uploadPhoto", $data);
  }

  // function uploadingPhoto(){
  //   $file = $filename = $employeeid = $mime = "";
  //   $employeeid = $this->input->post("employeeid");
  //   if(isset($_FILES['file']['name'])){
  //       $filename = basename($_FILES['file']['name']);
  //       $file = file_get_contents($_FILES['file']['tmp_name'], $filename);
  //       $file = base64_encode($file);
  //       $mime = $_FILES['file']['type'];
  //   }
  //   if($mime == "image/png" || $mime == "image/jpg" || $mime == "image/jpeg") $return = $this->employee->savePhoto($employeeid,$filename,$file);
  //   else $return = array("err_code" => "2", "msg" => "Invalid file type.");
  //   echo json_encode($return);
  // }

  function uploadingPhoto(){
    $file = $filename = $employeeid = $file_type = $temp_name = $tmp_name = $getblob =  "";
    $employeeid = $this->input->post("employeeid");

    if(isset($_FILES['file']['name'])){
        $filename = basename($_FILES['file']['name']); 
        $file = file_get_contents($_FILES['file']['tmp_name'], $filename); 
        $getblob = $this->db->escape(file_get_contents($_FILES['file']['tmp_name']));
        $getblob = rtrim($getblob,"'");
        $getblob = ltrim($getblob,"'");
        $file_type = $_FILES["file"]["type"];
        $tmp_name = $_FILES['file']['tmp_name'];
        $filename = $employeeid.".".pathinfo($filename, PATHINFO_EXTENSION);
    }
    $return = $this->employee->savePhoto_elfinder($filename,$file_type,$getblob);
    echo json_encode($return);
  }

  function getEmployeeList(){
    $this->load->model("utils");
    $campus_list  = $this->utils->getCampusList();
    $department    = $this->utils->getDepartments();
    $schedcluster    = $this->utils->getschedcluster();
    $emp_list = array();
    $active = $this->input->post("active");
    $employees = $this->employee->getEmployeeList($active);
    foreach($employees as $row){
      $employee_photo = $this->employee->getEmployeePhoto($row['employeeid']);
        if($employee_photo)
        {
          $photo = json_decode(json_encode($employee_photo), true);
          $user_img = "data:image/jpg;base64,".$photo[0]['file'];
        }
        else{
          $user_img = $this->employeeAvatar($row['age'], $row['gender']);
        }
        list($remarks, $remarks_icon, $status) = $this->getEmployeeRemarks($row['employeeid']);
        $emp_list[$row['employeeid']] = array(
            "employeeid" => $row['employeeid'],
            "remarks" => $remarks,
            "fullname" => $row['fullname'],
            "campusid" => isset($campus_list[$row['campusid']]) ? $campus_list[$row['campusid']] : "",
            "deptid" => isset($department[$row['deptid']]) ? $department[$row['deptid']] : "",
            "emptype" => isset($schedcluster[$row['emptype']]) ? $schedcluster[$row['emptype']] : "",
            "teachingtype" => $row['teachingtype'],
            "remarks_icon" => $remarks_icon,
            "status" => $status,
            "user_img" => $user_img
        );
    }
    $data['employee'] = $emp_list;
    $this->load->view("employee/employeelist", $data);
  }

  function getEmployeeRemarks($employeeid){
      $remarks_icon = base_url()."img/absent.png";
      $remarks = "Absent - No current activity yet";
      $status = "Not available";
      $on_leave = $this->employeeLeaveToday($employeeid);
      $is_present = $this->employeeAttendanceToday($employeeid);

      if($is_present){ 
        $remarks_style = " style='color:green;'";
        $remarks_icon = base_url()."img/present.png";
        $remarks = "Present: Tap time - ".date('h:i A', strtotime($is_present));
        $status = "Available";
      }
      if($on_leave){ 
        $remarks_style = " style='color:blue;'";
        $remarks_icon = base_url()."img/leave.png";
        $remarks = "Not in office - On leave today";
        $status = "Not available";
      }

      return array($remarks, $remarks_icon, $status);
  }

  function employeeLeaveToday($employeeid){
    date_default_timezone_set('Asia/Manila');
    $on_leave = false;
    list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$l_nopay) = $this->attcompute->displayLeave($employeeid,date("Y-m-d"));
    if($el || $vl || $sl || $ob || $ol) $on_leave = true;
    return $on_leave;
  }

  function employeeAttendanceToday($employeeid){
    $emp_timein = $this->timesheet->employeeTimeinToday($employeeid, date("Y-m-d"));
    return $emp_timein;
  }

  function employeeAvatar($age, $gender){
    $emp_avatar = "";
    if($gender == "M") $emp_avatar = base_url()."img/malecostume-512.png";
    if($gender == "F") $emp_avatar = base_url()."img/female1-512.png";
    if($gender == "M" && $age >= 60) $emp_avatar = base_url()."img/matureman2-512.png";
    if($gender == "F" && $age >= 60) $emp_avatar = base_url()."img/maturewoman-3-512.png";
    if(!$emp_avatar) $emp_avatar = base_url()."img/unknown-512.png";

    return $emp_avatar;
  }

  // function updateEmployeeInformation(){
  //   $column = $this->input->post("column");
  //   $employeeid = $this->input->post("employeeid");
  //   $value = $this->input->post("value");
  //   $res = $this->employee->updateEmployeeInformation($employeeid, $column, $value);
  //   echo $res;
  // }
    function updateEmployeeInformation(){
    $toks = $this->input->post("toks");
    $column = $toks ? $this->gibberish->decrypt( $this->input->post("column"), $toks ) : $this->input->post("column");
    $employeeid = $toks ? $this->gibberish->decrypt( $this->input->post("employeeid"), $toks ) : $this->input->post("employeeid");
    $value = $toks ? $this->gibberish->decrypt( $this->input->post("value"), $toks ) : $this->input->post("value");
    $bank = $toks ? $this->gibberish->decrypt( $this->input->post("bank"), $toks ) : $this->input->post("bank");
    $emp = $this->extras->getBankDesc($employeeid);
    $emp_bank = json_decode(json_encode($emp[0]), true);
    $convert_to_array = explode('/', $emp_bank["emp_bank"]);
    if($column == "rank") $this->employee->updateEmployeeRank($employeeid, $value);
    $datetoday = date('Y-m-d');
    for($i=0; $i < count($convert_to_array ); $i++){
        $key_value = explode('=', $convert_to_array [$i]);
        $banks[$key_value [0]] = isset($key_value [1]) ? $key_value [1] : "";
    }
    $check = '';
    $newVal = '';
    $newVal .= '';
      foreach($this->extensions->getBankList() as $row){
        if($bank == $row['code']){
          foreach($banks as $key => $val){
            if($key != $bank){
                $newVal .=$key."=".$val."/";
            }
            else{  
               $bankVal = $bank."=".$value;
               $check = $this->employee->checkIFBankExist($bankVal, $employeeid, $bank);
               $newVal .=$bank."=".$value."/";
            }
        }
        $newVal .=$bank."=".$value."/";
        }
      }
    if($bank != '') $value = $newVal; 
    if ($column == "email" || $column == "personal_email") {
      $checker = $this->employee->emailChecker($value);
      if ($checker) {
        echo "EmailExist";
        die;
      }
    }
    if($column == "emp_sss" || $column == "emp_tin" || $column == "emp_philhealth" || $column == "emp_pagibig" || $column == "passport" || $column == "emp_hmo"|| $column == "prc"){
        $check = $this->checkifIDExist($value, $column, $employeeid);
    }

    if($check > 0){
      $res = 'exist!';
    }else{
      $res = $this->employee->updateEmployeeInformation($employeeid, $column, $value);
      if($column == "isactive"){
        $this->employee->updateEmployeeInformation($employeeid, 'status_update_date', $datetoday);
      }
    }
    // echo "<pre>"; print_r($this->db->last_query())
    echo $res;
  }

  function updateEmployeeInfoApi(){
      $employee = $this->input->post();
      $api_url = Globals::apiUrl()."/api/person/editemployee";
      $empinfo = array(
        "PersonType" => "E",
        "IDNumber"  => $employee['employeeid'],
        "FirstName" => isset($employee['fname']) ? $employee['fname'] : $this->extensions->getEmployeeFname($employee['employeeid']),
        "MiddleName" => isset($employee['mname']) ? $employee['mname'] : $this->extensions->getEmployeeMname($employee['employeeid']),
        "LastName" => isset($employee['lname']) ? $employee['lname'] : $this->extensions->getEmployeeLname($employee['employeeid']),
        "CampusName" => "Pinnacle Technologies Inc.",
        "DepartmentName" => ($employee['deptid']) ? $employee['deptid'] : "Administrative Support Sevices",
        "PositionName" => ($employee['positionid']) ? $employee['positionid'] : "Head, Administrative Support Services"
      );

      $logs_data = $empinfo;
      $empinfo = json_encode($empinfo);
      $token = $this->extensions->getPostmanToken();
      // var_dump($token); die;
      $access_token = "Bearer ".$token;
      $headers = array(
          'Content-type: application/json',
          'Authorization: '.$access_token,
      );

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $api_url); 
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
      curl_setopt($curl, CURLOPT_POSTFIELDS, $empinfo); 
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);

      /*save to hyperion logs*/
      if($response){
        $this->employee->saveALLCARDLogs($logs_data);
      }
  }

  function isEmployeeIDExist(){
    $empid = $this->input->post('employeeid');
    $res = $this->employee->isEmployeeIDExist($empid);
    echo $res;
  }

  function isUsernameExist(){
    $toks = $this->input->post("toks");
    $empid = $toks ? $this->gibberish->decrypt($this->input->post('employeeid'), $toks) : $this->input->post('employeeid');
    $res = $this->employee->isUsernameExist($empid);
    echo $res;
  }

  function getPresentEmployeeList(){
    $this->load->model("utils");
    $department    = $this->utils->getDepartments();
    $emp_list_attendance = array();
    $today_attendance = $this->time->getPresentEmployeeList();
    if($today_attendance){
      foreach($today_attendance as $row){
          if(file_exists("images/employee/".$row['employeeid'].".jpg")) $user_img = base_url()."images/employee/".$row['employeeid'].".jpg"; /*for employee image*/
          else $user_img = $this->employeeAvatar($row['age'], $row['gender']);

          $emp_list_attendance[$row['employeeid']] = array(
              "employeeid" => $row['employeeid'],
              "timein" => date("g:i A", strtotime($row['datecreated'])),
              "timeout" => $this->time->getTimeoutEmployeeList($row["employeeid"]),
              "fullname" => $row['lname'].", ".$row['fname'].", ".$row['mname'],
              "deptid" => isset($department[$row['deptid']]) ? $department[$row['deptid']] : "Not assigned yet.",
              "user_img" => $user_img
          );
      }
    }

    $data['att_list'] = $emp_list_attendance;
    $this->load->view("includes/presentlist", $data);
  }

  function getEmployeeImageDashboard(){
    $this->load->model("utils");
    $employee_photo = $this->utils->getEmployeePhoto($this->input->post("employeeid")); 
      $hasPhoto = $hasElfinderPhoto = 0;
      $user_img = "";
      if($employee_photo->num_rows() > 0){
          $hasPhoto++;
          $user_img = json_decode(json_encode($employee_photo->result()), true);
      }
      else{
        $employee_elfinder_file = $this->utils->getEmployeePhotoElfinder($this->input->post("employeeid"));
          foreach ($employee_elfinder_file as $key => $value) {
            $hasElfinderPhoto++;
            $user_img = "data:".$value->mime.";base64,".base64_encode($value->content);
          }
          if (!$employee_elfinder_file) {
            $user_img = $this->employeeAvatar($row['age'], $row['gender']);
          }
      }
      echo '<img src="'.$user_img.'" class="img-circle" style="width:50px;height: 70px;">';
  }

  function getAttendanceListModal(){
    $toks = $this->input->post("toks");
    $label = $this->gibberish->decrypt( $this->input->post("label"), $toks );
    $data["label"] = $label;
    $datenow = $this->extensions->getServerTime();
    $datenow = date("Y-m-d", strtotime($datenow));
    // $datenow = "2021-10-06";
    $this->load->model("utils");
    $department    = $this->utils->getDepartments();
    $emp_list_attendance = array();
    if($label == "Present"){
      $today_attendance = $this->time->getPresentEmployeeList();
      // echo "<pre>"; print_r($this->db->last_query()); die;
      if($today_attendance){
        foreach($today_attendance as $row){
            $deptToShow = "Not assigned yet.";
            $deptOffice = $this->utils->getDeptOnOffice($row['office']);
            if(isset($department[$row['deptid']])){
              $deptToShow = $department[$row['deptid']];
            }elseif ($deptOffice) {
              if (isset($department[$deptOffice])) {
                $deptToShow = $department[$deptOffice];
              }
            }

            if(in_array($row['employeeid'], $emp_list_attendance)){
              $emp_list_attendance[$row['employeeid']]['timeout'] = $this->time->getLoginKen($row['employeeid'], $datenow, "OUT");
            }else{
              $emp_list_attendance[$row['employeeid']] = array(
                  "employeeid" => $row['employeeid'],
                  "timein" => $this->time->getLoginKen($row['employeeid'], $datenow, "IN"),
                  "timeout" => $this->time->getLoginKen($row['employeeid'], $datenow, "OUT"),
                  "fullname" => $row['lname'].", ".$row['fname'].", ".$row['mname'],
                  "deptid" => $deptToShow,
                  "gender" => $row['gender'],
                  "age" => $row['age']
              );
            }
              

          }
          // echo "<pre>";print_r($emp_list_attendance);die;
          // die;
        }
      }else if($label == "Late"){
          $datenow = $this->extensions->getServerTime();
          $datenow = date("Y-m-d", strtotime($datenow));
          // $datenow = "2021-06-01";
          $deptid = $this->extensions->getEmployeeDeparment($this->session->userdata("username"));
          $where_clause = "";
          if($this->session->userdata("usertype") != "ADMIN") $where_clause = " AND deptid = '$deptid'";
          $data["late"] = $data["ontime"] = 0;
          $present_employee = $this->time->getPresentEmployee($datenow, $where_clause);
          if($present_employee){
              foreach($present_employee as $row){
                  $islate = $this->extensions->getTimeInAccuracy($row["user_id"], date("H:i:s", strtotime($row["localtimein"])));
                  if($islate){
                     $deptToShow = "Not assigned yet.";
                      $deptOffice = $this->utils->getDeptOnOffice($row['office']);
                      if(isset($department[$row['deptid']])){
                        $deptToShow = $department[$row['deptid']];
                      }elseif ($deptOffice) {
                        if (isset($department[$deptOffice])) {
                          $deptToShow = $department[$deptOffice];
                        }
                      }
                      $emp_list_attendance[$row['employeeid']] = array(
                          "employeeid" => $row['employeeid'],
                          "timein" => date('g:i A', strtotime($row['localtimein'])),
                          "timeout" => $this->time->getTimeoutEmployeeList($row["employeeid"]),
                          "fullname" => $row['lname'].", ".$row['fname'].", ".$row['mname'],
                          "deptid" => $deptToShow,
                          "gender" => $row['gender'],
                          "age" => $row['age']
                      );
                  }
                  $islate = false;
              }
          }
      }else if($label == "On Time"){
          $datenow = $this->extensions->getServerTime();
          $datenow = date("Y-m-d", strtotime($datenow));
          // $datenow = "2021-06-01";
          $deptid = $this->extensions->getEmployeeDeparment($this->session->userdata("username"));
          $where_clause = "";
          if($this->session->userdata("usertype") != "ADMIN") $where_clause = " AND deptid = '$deptid'";
          $data["late"] = $data["ontime"] = 0;
          $present_employee = $this->time->getPresentEmployee($datenow, $where_clause);
          if($present_employee){
              foreach($present_employee as $row){
                  $islate = $this->extensions->getTimeInAccuracy($row["user_id"], date("H:i:s", strtotime($row["localtimein"])));
                  if(!$islate){
                    $deptToShow = "Not assigned yet.";
                    $deptOffice = $this->utils->getDeptOnOffice($row['office']);
                    if(isset($department[$row['deptid']])){
                      $deptToShow = $department[$row['deptid']];
                    }elseif ($deptOffice) {
                      if (isset($department[$deptOffice])) {
                        $deptToShow = $department[$deptOffice];
                      }
                    }
                      $emp_list_attendance[$row['employeeid']] = array(
                          "employeeid" => $row['employeeid'],
                          "timein" => date('g:i A', strtotime($row['localtimein'])),
                          "timeout" => $this->time->getTimeoutEmployeeList($row["employeeid"]),
                          "fullname" => $row['lname'].", ".$row['fname'].", ".$row['mname'],
                          "deptid" => $deptToShow,
                          "gender" => $row['gender'],
                          "age" => $row['age']
                      );
                  }
                  $islate = false;
              }
          }
      }else if($label == "Absent"){
        $today_attendance = $this->time->getAbsentEmployeeList($datenow);
        // echo"<pre>";print_r($this->db->last_query());die;
        if($today_attendance){
          foreach($today_attendance as $row){
            $sched = $this->attcompute->displaySched($row['employeeid'],$datenow);
            if($sched->num_rows() > 0){
              if($sched->row()->flexible != "YES"){
                $deptToShow = "Not assigned yet.";
                $deptOffice = $this->utils->getDeptOnOffice($row['office']);
                if(isset($department[$row['deptid']])){
                  $deptToShow = $department[$row['deptid']];
                }elseif ($deptOffice) {
                  if (isset($department[$deptOffice])) {
                    $deptToShow = $department[$deptOffice];
                  }
                }
                  $emp_list_attendance[$row['employeeid']] = array(
                      "employeeid" => $row['employeeid'],
                      "fullname" => $row['lname'].", ".$row['fname'].", ".$row['mname'],
                      "deptid" => $deptToShow,
                      "gender" => $row['gender'],
                      "age" => $row['age']
                  );
              }
                
            }else{
              $deptToShow = "Not assigned yet.";
              $deptOffice = $this->utils->getDeptOnOffice($row['office']);
              if(isset($department[$row['deptid']])){
                $deptToShow = $department[$row['deptid']];
              }elseif ($deptOffice) {
                if (isset($department[$deptOffice])) {
                  $deptToShow = $department[$deptOffice];
                }
              }
                $emp_list_attendance[$row['employeeid']] = array(
                    "employeeid" => $row['employeeid'],
                    "fullname" => $row['lname'].", ".$row['fname'].", ".$row['mname'],
                    "deptid" => $deptToShow,
                    "gender" => $row['gender'],
                    "age" => $row['age']
                );
            }
          }
      }
    }else if($label == "Flexible"){
        $today_attendance = $this->time->getAbsentEmployeeList($datenow);
        // echo"<pre>";print_r($this->db->last_query());die;
        if($today_attendance){
          foreach($today_attendance as $row){
            $sched = $this->attcompute->displaySched($row['employeeid'],$datenow);
            if($sched->num_rows() > 0){
              if($sched->row()->flexible == "YES"){
                $deptToShow = "Not assigned yet.";
                $deptOffice = $this->utils->getDeptOnOffice($row['office']);
                if(isset($department[$row['deptid']])){
                  $deptToShow = $department[$row['deptid']];
                }elseif ($deptOffice) {
                  if (isset($department[$deptOffice])) {
                    $deptToShow = $department[$deptOffice];
                  }
                }
                  $emp_list_attendance[$row['employeeid']] = array(
                      "employeeid" => $row['employeeid'],
                      "fullname" => $row['lname'].", ".$row['fname'].", ".$row['mname'],
                      "deptid" => $deptToShow,
                      "gender" => $row['gender'],
                      "age" => $row['age']
                  );
              }
                
            }
          }
      }
    }else if($label == "Part-time"){
        $parttime = $this->time->getPartTimeEmployees($datenow);
        if($parttime){
          foreach($parttime as $row){
            $deptToShow = "Not assigned yet.";
            $deptOffice = $this->utils->getDeptOnOffice($row['office']);
            if(isset($department[$row['deptid']])){
              $deptToShow = $department[$row['deptid']];
            }elseif ($deptOffice) {
              if (isset($department[$deptOffice])) {
                $deptToShow = $department[$deptOffice];
              }
            }
              $emp_list_attendance[$row['employeeid']] = array(
                  "employeeid" => $row['employeeid'],
                  "fullname" => $row['lname'].", ".$row['fname'].", ".$row['mname'],
                  "deptid" => $deptToShow,
                  "gender" => $row['gender'],
                  "age" => $row['age']
              );
                
          }
          
      }
    }else if($label == "Leave/OB"){
      
        $today_attendance = $this->time->getLeaveTodayEmployees($datenow);
        // $today_attendance_ob = $this->time->getObTodayEmployees($datenow);

        if($today_attendance){
          foreach($today_attendance as $row){
              $emp_list_attendance[$row['employeeid']] = array(
                  "employeeid" => $row['employeeid'],
                  "fullname" => $row['lname'].", ".$row['fname'].", ".$row['mname'],
                  "deptid" => isset($department[$row['deptid']]) ? $department[$row['deptid']] : "Not assigned yet.",
                  "gender" => $row['gender'],
                  "age" => $row['age']
              );
          }
      }

      // if($today_attendance_ob){
      //     foreach($today_attendance_ob as $row){
      //         $emp_list_attendance[$row['employeeid']] = array(
      //             "employeeid" => $row['employeeid'],
      //             "fullname" => $row['lname'].", ".$row['fname'].", ".$row['mname'],
      //             "deptid" => isset($department[$row['deptid']]) ? $department[$row['deptid']] : "Not assigned yet.",
      //             "gender" => $row['gender'],
      //             "age" => $row['age']
      //         );
      //     }
      // }
    }else if($label == "On Holiday"){
      $emp_list_attendance = $this->attendance->getEmployeeOnHoliday($datenow);
    }

    $data['att_list'] = $emp_list_attendance;
   
    $this->load->view("includes/presentlistmodal", $data);
  }

  function getBirthdayCelebrantsToday(){
    $this->load->model("utils");
    $department    = $this->utils->getDepartments();
    $bdaylist = array();
    $today_attendance = $this->extensions->getBirthdayCelebrantsToday();
    if($today_attendance){
      foreach($today_attendance as $row){
          if(file_exists("images/employee/".$row['employeeid'].".jpg")) $user_img = base_url()."images/employee/".$row['employeeid'].".jpg"; /*for employee image*/
          else $user_img = $this->employeeAvatar($row['age'], $row['gender']);

          $bdaylist[$row['employeeid']] = array(
              "employeeid" => Globals::_e($row['employeeid']),
              "fullname" => Globals::_e($row['lname'].", ".$row['fname'].", ".$row['mname']),
              "deptid" => isset($department[$row['deptid']]) ? Globals::_e($department[$row['deptid']]) : "Not assigned yet.",
              "user_img" => $user_img
          );
      }
    }

    $data['bdaylist'] = $bdaylist;
    $this->load->view("includes/birthdaylist", $data);
  }

  function updateTableStatus(){
    $toks = $this->input->post("toks");
    $table = $toks ? $this->gibberish->decrypt( $this->input->post("table"), $toks ) : $this->input->post("table");
    $id = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) : $this->input->post("id");
    $approver = $toks ? $this->gibberish->decrypt( $this->input->post("approverid"), $toks ) : $this->input->post("approverid");
    $res = $this->employee->updateTableStatus($table, $id, $approver);
    
    echo $res;
  }

  public function loadEmployeeList(){   
        # concat(lastname,\', \',firstname,\' \',middlename) as fullname
        $toks = $this->input->post("toks");
        $active = $this->input->post("empStatus");
        if($active == "active"){
          $where = '(dateresigned2 = "0000-00-00" OR dateresigned2 = "1970-01-01" OR dateresigned2 IS NULL) AND (isactive = "1")';
        }else if($active == "inactive"){
          $where = '(dateresigned2 != "1970-01-01" AND dateresigned2 != "0000-00-00" AND dateresigned2 IS NOT NULL) OR (isactive = "0")';
        }

        $datenow = date("Y-m-d");
        $data = $this->input->post();
  
        $active = $status = $this->gibberish->decrypt($data["status"], $toks);
        $campus = $this->gibberish->decrypt($data["campus"], $toks);
        $teachingType = $this->gibberish->decrypt($data["teachingType"], $toks);
        $department = $this->gibberish->decrypt($data["department"], $toks);
        $office = $this->gibberish->decrypt($data["office"], $toks);
        $empstat = $this->gibberish->decrypt($data["empstat"], $toks);
        $employeeid = "";
        if(strpos($employeeid, "'") !== false) $employeeid = $this->db->escape($this->gibberish->decrypt($data["employeeid"], $toks));
        else $employeeid = $this->gibberish->decrypt($data["employeeid"], $toks);
        $where = "employeeid <> ''";
        
        if($campus){
          $where .= " AND a.campusid = '$campus'";
        }
        if($teachingType){
          $where .= " AND a.teachingtype = '$teachingType'";
        }
        // if($company_campus){
        //   $where .= " AND a.company_campus = '$company_campus'";
        // }
        if($department){
          $where .= " AND a.deptid = '$department'";
        }
        
        if($employeeid){
          $where .= " AND a.employeeid = '$employeeid'";
        }

        if($status != "all"){
          if($active=="1"){
            $where .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($active=="0"){
            $where .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($status)) $where .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }

        if($office && $office != 'All' && $office != 'all'){
          $where .= " AND a.office = '$office'";
        }

        if($empstat){
          $where .= " AND a.employmentstat = '$empstat'";
        }
        // echo "<pre>"; print_r($where); die;
        // $where .= " AND a.employmentstat != 'PT'";

        $this->load->library('datatables');
        $this->datatables
             ->select('a.employeeid, a.fname, a.mname, a.lname, a.teachingtype, a.deptid, b.description,c.description office, a.isactive')
             ->edit_column('a.employeeid, a.fname, a.mname, a.lname, a.teachingtype, a.deptid, b.description,c.description office, a.isactive', 
                           '<div employeeid="$1" class="pointer">
                            <div class="col-sm-2 col-md-1 imageDiv" imageDiv="$1" id="img_$1"><img src="'.base_url().'/images/loading2.gif" class="img-circle" style="width:100px;height: 100px;"></div>
                            <div class="col-sm-6 col-sm-5">
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="media-heading">$4, $3 $2</h4>
                                    <ul>
                                        <li>Employee ID : $1</li>
                                        <li id="campus_$1" class="campus" empid="$1">Campus :  </li>
                                        <li>Type : $5</li>
                                        <li>Department : $7</li>
                                        <li>Office : $8</li>
                                        <li class="status" empid="$1" id="status_$1">Status :</li>
                                    </ul>
                                </div>
                            </div>
                            </div>
                            <div class="col-sm-4 col-md-6" id="rem_$1"></div>
                            </div>', 
                           'a.employeeid, a.fname, a.mname, a.lname, a.teachingtype, a.deptid, b.description,c.description office, a.isactive')
             // ->edit_column('a.employeeid, a.fname, a.mname, a.lname, a.teachingtype, a.deptid, b.description')
             ->from('employee a')
             ->join('code_department b', 'a.deptid = b.code', 'left')
             ->join('code_office c', 'a.office = c.code', 'left')
             ->where($where);
             // ->join('elfinder_file c', 'a.employeeid = c.title', 'inner')
             
             
          # ->edit_column('id', '<input type="checkbox" value="$1">', 'id')   
          #->edit_column('userid', '<a href="profiles/edit/$1">$2</a>', 'userno, userid');

        $results = $this->datatables->generate('json');
        echo $results;
    }

    // function loadImage(){
    //   $id = $this->input->post("id");
    //   $gender = $this->input->post("gender");
    //   $age = $this->input->post("age");
    //   $image = $this->employee->getEmployeePhoto($id);
    //   $elFinderImage = $this->employee->getElfinderImage($id);
    //   if($image){
    //     $user_img = "data:image/jpg;base64,".$image[0]->file;
    //     echo '<img src="'.$user_img.'" class="img-circle" style="width:100px;height: 100px;">';
    //   }else if($elFinderImage){
    //     echo '<img src="'.$elFinderImage.'" class="img-circle" style="width:100px;height: 100px;">';
    //   }
    //   else{
    //     $user_img = $this->employeeAvatar($age, $gender);
    //     echo '<img src="'.$user_img.'" class="img-circle" style="width:100px;height: 100px;">';
    //   }
    // }

    function loadImage(){
      $toks = $this->input->post("toks");
      $id = $toks ? $this->gibberish->decrypt($this->input->post('id'), $toks) : $this->input->post("id");
      $gender = $toks ? $this->gibberish->decrypt($this->input->post('gender'), $toks) : $this->input->post("gender");
      $age = $toks ? $this->gibberish->decrypt($this->input->post('age'), $toks) : $this->input->post("age");
      $image = $this->employee->getEmployeePhoto($id);
      $elFinderImage = $this->employee->getElfinderImage($id);
      if($elFinderImage){
        echo '<img src="'.$elFinderImage.'" class="img-circle" style="width:100px;height: 100px;">';
      }else{
        $user_img = $this->employeeAvatar($age, $gender);
        echo '<img src="'.$user_img.'" class="img-circle" style="width:100px;height: 100px;">';
      }
    }

    function loadStatus(){
      $datenow = date('Y-m-d');
      $output = 'Status: ';
      $id = $this->input->post("id");
      $status = $this->employee->getemployeestatus($id);
      if(($status[0]['dateresigned2'] !== null && $status[0]['dateresigned2'] != '0000-00-00' && $status[0]['dateresigned2'] != '1970-01-01' && $datenow >= $status[0]['dateresigned2']) || $status[0]['isactive'] == 0){
        $output = "Status : <span style='color: red'>Inactive</span>";
      }
      else if(($status[0]['dateresigned2'] == '0000-00-00' || $status[0]['dateresigned2'] == '1970-01-01' || $status[0]['dateresigned2'] === null || $datenow < $status[0]['dateresigned2']) && $status[0]['isactive'] == 1){
        $output = "Status : <span style='color: green'>Active</span>";
      }
      echo $output;
    }

    function loadCampus(){
      $output = '';
      $id = $this->input->post("id");
      $campus = $this->employee->getemployeecampus($id);
      $output = "Campus : ".$campus;
      echo $output;
    }

    function loadRemarks(){
      $id = $this->input->post("id");
      list($remarks, $remarks_icon, $status) = $this->getEmployeeRemarks($id);
      $output = '';
      $output .= '<div class="row"><div class="col-md-6 col-md-offset-6"><div class="pull-right">';
      $output .= '<span><b>'.$remarks.'</b></span>&emsp;';
      $output .= '<img src="'.$remarks_icon.'" class="media-object" style="width: 40px">';
      $output .= '<p style="color:black"><i>Status: <b>'.$status.'</b></i></p>&emsp;';
      $output .= '</div></div></div>';
      echo $output;
    }

    function loadStatusHistory(){
      $toks = $this->input->post('toks');
      $id = $toks ?  $this->gibberish->decrypt($this->input->post('id'), $toks) : $this->input->post('id');
      $table = $toks ?  $this->gibberish->decrypt($this->input->post('table'), $toks) : $this->input->post('table');
      $return = '';
      $history = $this->employee->loadStatusHistory($id, $table);
      foreach ($history as $value) {
        $return .= 'Updated by:'.$value['approver'].' - '.$value['date_approve'].'</br>';
      }
      echo $return;

    }

    function checkifIDExist($value, $column, $employeeid){
      $check = $this->employee->checkifIDExist($value, $column, $employeeid);
      return $check;
    }

    function getZipCode(){
      $res = false;
      $checkMunSolo = '';
      $toks = $this->input->post("toks");
      $place = $toks ?  mb_strtolower($this->gibberish->decrypt( $this->input->post("place"), $toks )) : mb_strtolower($this->input->post("place"));
      $mun = $toks ?  mb_strtolower($this->gibberish->decrypt( $this->input->post("mun"), $toks )) : mb_strtolower($this->input->post("mun"));
      $provCode = $toks ?  mb_strtolower($this->gibberish->decrypt( $this->input->post("provCode"), $toks )) : mb_strtolower($this->input->post("provCode"));
      $munCode = $toks ?  mb_strtolower($this->gibberish->decrypt( $this->input->post("munCode"), $toks )) : mb_strtolower($this->input->post("munCode"));
      // echo "<pre>"; print_r($place);
      // echo "<pre>"; print_r($mun);
      // echo "<pre>"; print_r($provCode);
      // echo "<pre>"; print_r($munCode);
      $cityof = "city of ";
      if(strpos($place, $cityof) !== false){
          $place = str_replace($cityof, '', $place);
      }
      $place =  strtok($place, '(');
      $place = rtrim($place, ' ');
      if($provCode == "1339") $checkMunSolo = Zipcode::soloZipinManila($mun);
      if(!$checkMunSolo && $provCode == "1339"){
        $res = Zipcode::postalCodeList($place, $munCode);
        if(!$res){
          $place = str_replace('ñ', 'n', $place);
          $res = Zipcode::postalCodeList($place, $munCode);
          if(!$res){
            $place = str_replace('ã‘a', 'ñ', $place);
            $res = Zipcode::postalCodeList($place, $munCode);
          }
        }
      }else if(!$checkMunSolo){
        $res = Zipcode::postalCodeList($place, $provCode);
        if(!$res){
          $place = str_replace('ñ', 'n', $place);
          $res = Zipcode::postalCodeList($place, $provCode);
          if(!$res){
            $place = str_replace('ã‘a', 'ñ', $place);
            $res = Zipcode::postalCodeList($place, $provCode);
          }
        }
      }else{
        $res = $checkMunSolo;
      }

      if($res == ''){
        $res = Zipcode::postalCodeList($mun, $provCode);
        if(!$res){
          $place = str_replace('ñ', 'n', $mun);
          $res = Zipcode::postalCodeList($place, $munCode);
          if(!$res){
            $place = str_replace('ã‘a', 'ñ', $place);
            $res = Zipcode::postalCodeList($place, $munCode);
          }
        }
      }
      echo $res;
    }

    public function addEmployeeToAims(){
      $empinfo = $this->input->post();
      $api_url = "";
      if($_SERVER["HTTP_HOST"] == "192.168.2.97") $api_url = "192.168.2.97/povedadtr/index.php/aims_/addEmployeeToAims";
      else if(($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" || $_SERVER["HTTP_HOST"] == "poveda-hris.pinnacle.com.ph") && strpos($_SERVER["REQUEST_URI"], 'hristrng') !== false) $api_url = "https://".$_SERVER["HTTP_HOST"]."/hristrng/index.php/aims_/addEmployeeToAims";
      else if(($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" || $_SERVER["HTTP_HOST"] == "poveda-hris.pinnacle.com.ph") && strpos($_SERVER["REQUEST_URI"], 'hris') !== false) $api_url = "https://".$_SERVER["HTTP_HOST"]."/hris/index.php/aims_/addEmployeeToAims";

      $empinfo = json_encode($empinfo);
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $api_url); 
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($curl, CURLOPT_POSTFIELDS, $empinfo); 
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);

      echo $response;
  }

  function load201sort(){
    // $where_clause = "WHERE employeeid<>'' ";
      $where_clause = 'WHERE 1 ';
      $option = "<option value=''> All Employee </option>";
      $toks = $this->input->post("toks");
      $campus = $this->gibberish->decrypt($this->input->post("campus"), $toks);
      $teachingType = $this->gibberish->decrypt($this->input->post("teachingType"), $toks);
      $department = $this->gibberish->decrypt($this->input->post("department"), $toks);
      $office = $this->gibberish->decrypt($this->input->post("office"), $toks);
      $status = $active = $this->gibberish->decrypt($this->input->post("status"), $toks);
      $employmentstat = $active = $this->gibberish->decrypt($this->input->post("employmentstat"), $toks);

      $employeeid = $this->gibberish->decrypt($this->input->post("employeeid"), $toks);
      $datenow = date('Y-m-d');
      // echo "<pre>"; print_r(array($campus, $teachingtype, $department, $office, $status, $employeeid)); die;
      if($campus && $campus != "undefined") $where_clause .= "AND campusid = '$campus' ";
      if($teachingType && $teachingType != "undefined") $where_clause .= "AND teachingtype = '$teachingType' ";
      if($department) $where_clause .= "AND deptid = '$department' ";
      if($employmentstat && $employmentstat != "undefined" && $employmentstat != "null") $where_clause .= " AND FIND_IN_SET(employmentstat,'$employmentstat')";
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
      foreach($records as $row){
        if($row["employeeid"] == $employeeid) $option .= "<option value='".$row['employeeid']."' selected>".$row['employeeid'].' - '.$row['lname'].", ".$row['fname']." ".$row['mname']."</option>";
        else $option .= "<option value='".$row['employeeid']."' >".$row['employeeid'].' - '.$row['lname'].", ".$row['fname']." ".$row['mname']."</option>";
      }
      echo $option;
    }

    function load201sort2(){
    // $where_clause = "WHERE employeeid<>'' ";
      $where_clause = 'WHERE 1 ';
      $option = "<option value='all'> All Employee </option>";
      $toks = $this->input->post("toks");
      $campus = $this->gibberish->decrypt($this->input->post("campus"), $toks);
      $teachingType = $this->gibberish->decrypt($this->input->post("teachingType"), $toks);
      $department = $this->gibberish->decrypt($this->input->post("department"), $toks);
      $office = $this->gibberish->decrypt($this->input->post("office"), $toks);
      $status = $active = $this->gibberish->decrypt($this->input->post("status"), $toks);
      $employmentstat = $active = $this->gibberish->decrypt($this->input->post("employmentstat"), $toks);

      $employeeid = $this->gibberish->decrypt($this->input->post("employeeid"), $toks);
      $datenow = date('Y-m-d');
      // echo "<pre>"; print_r(array($campus, $teachingtype, $department, $office, $status, $employeeid)); die;
      if($campus && $campus != "undefined") $where_clause .= "AND campusid = '$campus' ";
      if($teachingType && $teachingType != "undefined") $where_clause .= "AND teachingtype = '$teachingType' ";
      if($department) $where_clause .= "AND deptid = '$department' ";
      if($employmentstat && $employmentstat != "undefined" && $employmentstat != "null") $where_clause .= " AND FIND_IN_SET(employmentstat,'$employmentstat')";
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
      foreach($records as $row){
        if($row["employeeid"] == $employeeid) $option .= "<option value='".$row['employeeid']."' selected>".$row['employeeid'].' - '.$row['lname'].", ".$row['fname']." ".$row['mname']."</option>";
        else $option .= "<option value='".$row['employeeid']."' >".$row['employeeid'].' - '.$row['lname'].", ".$row['fname']." ".$row['mname']."</option>";
      }
      echo $option;
    }

    function historyTable(){
        $toks = $this->input->post("toks");
        $data['employeeid'] = $this->gibberish->decrypt($this->input->post("employeeid"), $toks);
        $data['employment_history'] = $this->employee->getEmploymentStatusHistory($data['employeeid']);
        list($data['deptid'],$data['office'],$data['employmentstatus'],$data['position'],$data['datepos'],$data['management'],$data['dateresigned'],$data['resigned_reason']) = $this->employee->getCurrentEmpStatusData($data['employeeid']);
        $this->load->view('employee/employment_history_table', $data);
    }

  //   function loadUploadedPhoto(){
  //   $toks = $this->input->post("toks");
  //   $employeeid= $toks ? $this->gibberish->decrypt($this->input->post('employeeid'), $toks) : $this->input->post("employeeid");
  //   // $employee_photo = $this->employee->getEmployeePhoto($employeeid);
  //   // if($employee_photo){
  //   //   $photo = json_decode(json_encode($employee_photo), true);
  //   //   $user_img = "data:image/jpg;base64,".$photo[0]['file'];
  //   // }
  //   $employee_photo = $this->employee->getEmployeePhoto($employeeid);
  //   if($employee_photo)
  //   {
  //     $photo = json_decode(json_encode($employee_photo), true);
  //     $user_img = "data:image/jpg;base64,".$photo[0]['file'];
  //   }
  //   echo $user_img;
  // }

  function loadUploadedPhoto(){
    $toks = $this->input->post("toks");
    $employeeid= $toks ? $this->gibberish->decrypt($this->input->post('employeeid'), $toks) : $this->input->post("employeeid");
    $elFinderImage = $this->employee->getElfinderImage($employeeid);
    echo $elFinderImage;
  }

   function updatePRC(){
        
        $toks = $this->input->post("toks");
        $employeeid = $this->gibberish->decrypt($this->input->post("employeeid"), $toks);
        $prc_expiration = $this->gibberish->decrypt($this->input->post("prc_expiration"), $toks);
        $prc = $this->gibberish->decrypt($this->input->post("prc"), $toks);
        $this->employee->updatePRC($employeeid, $prc_expiration, $prc);
    }

    

}

/* End of file employee_.php */
/* Location: ./application/controllers/employee.php */