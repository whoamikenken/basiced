<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maintenance_ extends CI_Controller {

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

  public function __construct(){
      parent::__construct();
      if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
      $this->load->model('setup');
  }

	public function index()
	{
        # nothing
	}
   public function cutoff_summary(){
      $data['title'] = 'CUT-OFF SUMMARY';
      $this->load->view('maintenance/cutoff_summary', $data);
  }
    public function cutoff_details(){
      $data['sid'] = $this->input->post("sid");
      # $data['isproc'] = $this->input->post("isproc");
      $data['isproc'] = 0;
      $this->load->view('maintenance/cutoff_details', $data);
    }
      function save_term(){
        $employeeid  = $this->input->post('employeeid',TRUE);
        $terminal_name  = $this->input->post('terminal_name',TRUE);
        $campus = $this->input->post('campus',TRUE);
        $building  = $this->input->post('building',TRUE);
        $floor  = $this->input->post('floor',TRUE);
        $password  = $this->input->post('password',TRUE);
        $rt_password  = $this->input->post('rt_password',TRUE);
        $data=$this->extras->save_terminal($employeeid,$campus,$building,$floor,$terminal_name,$password,$rt_password);
        echo json_encode($data);
    }
    public function cutoff_entry(){
      $data['title'] = '';
      $this->load->view('maintenance/cutoff_entry', $data);
    }
    public function office(){
      $data['title'] = '';
      $this->load->view('process/office', $data); 
    }
    public function manage_office(){
       $toks = $this->input->post("toks");
       $data['code'] = $toks ? $this->gibberish->decrypt( $this->input->post("code"), $toks ) : $this->input->post("code");
       $data['job'] = $toks ? $this->gibberish->decrypt( $this->input->post("job"), $toks ) : $this->input->post("job");
       $data['records'] = $this->setup->manageOffice($data['code']);
       $this->load->view('maintenance/manage_office', $data); 
    }
    public function save_office(){ 
        $res = "";
        $toks = $this->input->post("toks");
        $job = $this->gibberish->decrypt($this->input->post("job"), $toks);  
        $code = $this->gibberish->decrypt($this->input->post("code"), $toks);
        $description = $this->gibberish->decrypt($this->input->post("description"), $toks);
        $department_id = $this->gibberish->decrypt($this->input->post("department_id"), $toks);
        $division = $this->gibberish->decrypt($this->input->post("division"), $toks);
        $head = $this->gibberish->decrypt($this->input->post("head"), $toks);
        $divhead = $this->gibberish->decrypt($this->input->post("divhead"), $toks);
        $isBED = $this->gibberish->decrypt($this->input->post("isBED"), $toks);
        $last_dept = $this->gibberish->decrypt($this->input->post("last_dept"), $toks);
        $res = $this->setup->saveOffice($code,$description,$division,$head,$divhead,$job,$isBED, $department_id, $last_dept);
       echo $res;
    }
    public function machine(){
      $data['title'] = '';
      $this->load->view('process/machine', $data); 
    }
    public function manage_machine(){
       $data['code'] = $this->input->post("code");
       $this->load->view('maintenance/manage_machine', $data); 
    }
    public function save_machine(){ 
       $job = $this->input->post("job"); 
       $code = $this->input->post("code");
       if($job=="delete"){
         $this->db->query("delete from machine_setup WHERE mac_add='{$code}';");
       }else{
         $description = $this->input->post("description");
         $type = $this->input->post("type");
         $status = $this->input->post("status");
         $this->db->query("CALL prc_machine_set('{$code}','{$description}','{$type}','{$status}');");
       }
    }


    public function saveaccess(){ 
       $toks = $this->input->post("toks");
       $uid = $toks ? $this->gibberish->decrypt( $this->input->post("uid"), $toks ) : $this->input->post("uid"); 
       $accesslist = $toks ? explode(",",$this->gibberish->decrypt( $this->input->post("accesslist"), $toks )) :  explode(",",$this->input->post("accesslist"));
       // $gate = explode(",",$this->input->post("gate"));
       $gate = $toks ? $this->gibberish->decrypt( $this->input->post("gate"), $toks ) : $this->input->post("gate");
       $ams = $toks ? $this->gibberish->decrypt( $this->input->post("ams"), $toks ) : $this->input->post("ams");
       $payroll = $toks ? explode(",",$this->gibberish->decrypt( $this->input->post("payroll"), $toks )) :  explode(",",$this->input->post("payroll"));
       
       /** Clear Access First */
       $this->db->query("delete from user_access WHERE userid='{$uid}';");
       foreach($accesslist as $aa){
        list($r,$w,$mid) = explode(":",$aa);
        $recheck = $this->db->query("SELECT * FROM user_access WHERE userid = '$uid' AND menu_id = '$mid'")->num_rows();
        if($recheck == 0) $this->db->query("CALL prc_user_access_set('{$uid}','{$mid}','".($r==1?"YES":"NO")."','".($w==1?"YES":"NO")."');");
       }  

       $this->db->query("UPDATE user_info SET gateaccess='0' WHERE id='$uid'");

       if($gate){
         list($r, $w, $name) = explode(":",$gate);
         $this->db->query("UPDATE user_info SET gateaccess='$r' WHERE id='$uid'");
       }

       if($ams){
         list($r, $w, $name) = explode(":",$ams);
         $this->db->query("UPDATE user_info SET ams_access='$r' WHERE id='$uid'");
       }

       $allow_arr = array("gate_tap_allow", "ams_tap_allow");
       foreach ($allow_arr as $column) {
          $value = ($column == "gate_tap_allow") ? $this->input->post("gate_allow") : $this->input->post("ams_allow");
          $this->db->query("UPDATE user_info SET $column='$value' WHERE id='$uid'");
       }

       $this->db->query("DELETE FROM user_access_payroll WHERE userid='$uid'");

       if(sizeof($payroll)>0){
          foreach ($payroll as $key => $posid) {
              $this->db->query("INSERT INTO user_access_payroll (userid,position_type) VALUES ('$uid','$posid')");
          }
       }
    }

    public function cutoff_save(){
      # $sy = $this->input->post("sy");
      # $sem = $this->input->post("sem");  
        
      $income_base = $this->input->post("income_base");
      $start_date = $this->input->post("startdate");
      $end_date = $this->input->post("enddate");
      $period = $this->input->post("period");
      
      # $start_date = $this->input->post("sdyear")."-".$this->input->post("sdmonth")."-".$this->input->post("sdday");
      # $end_date = $this->input->post("edyear")."-".$this->input->post("edmonth")."-".$this->input->post("edday");
      # ,'$sem'
      $this->db->query("CALL prc_generate_cutoff_details('{$start_date}','{$end_date}','{$income_base}','".$this->session->userdata("userid")."','{$period}',@res,@message,@idc)");
      $q = $this->db->query("select @res as RESULT_NUM,@message as MESSAGE,@idc as IDC");
      
      echo "<user>
               <status>".$q->row(0)->RESULT_NUM."</status>
               <message>".$q->row(0)->MESSAGE."</message>
               <id>".$q->row(0)->IDC."</id>
            </user>"; 
    }
    public function process_cutoff(){
      $cutoffid = $this->input->post("cutoffid");  
      $this->db->query("CALL prc_process_payroll_percutoff('{$cutoffid}',@res,@message)");
      $q = $this->db->query("select @res as RESULT_NUM,@message as MESSAGE");
      echo "<user>
               <status>".$q->row(0)->RESULT_NUM."</status>
               <message>".$q->row(0)->MESSAGE."</message>
            </user>";  
    }
    public function set_holiday_dates(){
       /** udpate the holiday code of an existing */
       $cid= $this->input->post("cid"); 
       $cdate= $this->input->post("cdate");
       $holiday= $this->input->post("holiday");
       
       $data = array("holidays"=>$holiday);
       $this->db->where('id',$cid);
       $this->db->where('cdate',$cdate);
       $this->db->update('cutoff_details',$data);
    }
    public function resetentry(){
       $this->db->query("TRUNCATE payroll_summary;");
       $this->db->query("TRUNCATE employee_dtr;");
       $this->db->query("TRUNCATE timesheet_load;");
       $this->db->query("TRUNCATE employee_schedule_percutoff;");
       $this->db->query("TRUNCATE cutoff_details;");
       $this->db->query("TRUNCATE cutoff_summary;");
       $this->db->query("DROP TEMPORARY TABLE IF EXISTS tmptable;");
       $this->db->query("DROP TEMPORARY TABLE IF EXISTS dtr_tmptable;"); 
    }
    /**
     * This site will get the list option of a DROPBOX 
     */
    public function gather_options(){
       $hasitsown = false; 
       $result = array();
       $type_o = $this->input->post("type_o");
       switch($type_o){
          case "holiday":
             $result = $this->extras->showholiday();      
          break;
          case "dtr_request":
             $result = $this->extras->enum_select( "timesheet","type");      
          break;
          case "adjustment_code":
             $issched = $this->input->post("issched")==1;
             $result = $this->extras->showadjustment_code($issched);      
          break;
          case "request":
             $result = $this->extras->showrequestform();      
          break;
          case "cutoffdates":
             $cutofid = $this->input->post("cutofid");
             $result = $this->extras->showcutofdatebyid($cutofid);      
          break;
          case "hours":
             $hasitsown = true;
             $result = $this->extras->showhours();      
          break;
          case "minutes":
             $hasitsown = true;
             $result = $this->extras->showminutes("",true);      
          break;
          case "timestatus":
             $hasitsown = true;
             $result = $this->extras->showstat();      
          break;
          case "leclab":
             $result = $this->extras->showLecLab();      
          break;
       } 
      echo "<user>"; 
      if(!$hasitsown){
          foreach($result as $key=>$code){
             echo "<option value='{$key}'>{$code}</option>"; 
          } 
      }else{
        echo $result;
      }
      echo "</user>";
    }
    public function manage_holidays(){
      $toks = $this->input->post("toks");
       $data['holiday_id'] =  $this->gibberish->decrypt($this->input->post("holiday_id"), $toks);
       $data['job'] = $this->gibberish->decrypt($this->input->post("job"), $toks);
       $this->load->view('maintenance/holiday_details', $data);
    }
    public function manage_tax(){
       $toks = $this->input->post("toks");
       $data['code'] = $this->gibberish->decrypt($this->input->post("code"), $toks);
       $this->load->view('maintenance/tax_details', $data); 
    }
    public function manage_leave(){
      $toks = $this->input->post("toks");
      $data['code'] = $toks ? $this->gibberish->decrypt($this->input->post('code'), $toks) : $this->input->post("code");
      $this->load->view('maintenance/leave_details', $data); 
    }
    public function manage_other_leave(){
       $toks = $this->input->post("toks");
       $data['code'] = $toks ? $this->gibberish->decrypt($this->input->post('code'), $toks) : $this->input->post("code");
       $this->load->view('maintenance/other_request_setup', $data);
    }
    public function manage_leave_date(){
       $data['code'] = $this->input->post("code");
       $this->load->view('maintenance/leave_details_date', $data); 
    }
    
    public function save_leave(){ 
        $res = 0;
        $data = $this->input->post();
        $toks = $this->input->post("toks");
        $job = $this->gibberish->decrypt($this->input->post("job"), $toks); 
        $code = $this->gibberish->decrypt($this->input->post("code"), $toks);
        if(!$code){
            $msg = "Code is required.";
            $res = 2;
            $return = array("msg" => $msg, "code" => $res);
            echo json_encode($return);
            return;
        }
        if(!$this->input->post("description")){
            $msg = "Description is required.";
            $res = 2;
            $return = array("msg" => $msg, "code" => $res);
            echo json_encode($return);
            return;
        }
        if($job=="delete"){
            $res = $this->db->query("delete from code_request_form WHERE code_request='{$code}';");
            if($res) $msg = "Successfully deleted.";
            else $msg = "Failed to process data.";
        }else{
            $description = $this->gibberish->decrypt($this->input->post("description"), $toks);
            $details = $this->gibberish->decrypt($this->input->post("details"), $toks);
            $credits = $this->gibberish->decrypt($this->input->post("credits"), $toks);
            /*
            $ltype = $this->input->post("ltype");
            $dfrom = $this->input->post("dfrom");
            $dto = $this->input->post("dto");
            */
            $dhseq = $this->gibberish->decrypt($this->input->post("dhseq"), $toks);
            $hhseq = $this->gibberish->decrypt($this->input->post("hhseq"), $toks);
            $chseq = $this->gibberish->decrypt($this->input->post("chseq"), $toks);
            $cpseq = $this->gibberish->decrypt($this->input->post("cpseq"), $toks);
            $dpseq = $this->gibberish->decrypt($this->input->post("dpseq"), $toks);
            $upseq = $this->gibberish->decrypt($this->input->post("upseq"), $toks);
            $boseq = $this->gibberish->decrypt($this->input->post("boseq"), $toks);
            $fdseq = $this->gibberish->decrypt($this->input->post("fdseq"), $toks);
            $pseq  = $this->gibberish->decrypt($this->input->post("pseq"), $toks);
            $bo = $this->gibberish->decrypt($this->input->post("bo"), $toks);
            $up = $this->gibberish->decrypt($this->input->post("up"), $toks);
            $upt = $this->gibberish->decrypt($this->input->post("upt"), $toks);
            $fd = $this->gibberish->decrypt($this->input->post("fd"), $toks);
            $pres = $this->gibberish->decrypt($this->input->post("pres"), $toks);
            $user = $this->session->userdata("username");
            $mngt  = $this->gibberish->decrypt($this->input->post("mngt"), $toks);

            // $del  = $this->extras->leaveSetup(0, $data);//delete existing
            // $save = $this->extras->leaveSetup(1, $data);//create new

            // echo "<pre>";print_r($this->input->post());die;
            if($this->db->query("SELECT * FROM code_request_form WHERE code_request = ".$this->db->escape($code)." ")->num_rows() == 0){
                $this->db->query("INSERT INTO online_application_code (description) VALUES (".$this->db->escape($description).") ");
                $request_id = $this->db->insert_id();
                $this->db->query("CALL prc_code_request_form_set(".$this->db->escape($code).",".$this->db->escape($description." teaching").",'{$mngt}','{$credits}','{$bo}','{$up}','{$upt}','{$fd}','{$pres}','{$dhseq}','{$hhseq}','{$chseq}','{$cpseq}','{$dpseq}','{$upseq}','{$boseq}','{$fdseq}','{$pseq}','$user', '$request_id');");
                $this->db->query("UPDATE code_request_form SET ismain = '1' WHERE code_request = ".$this->db->escape($code)." ");

                $app_arr = array(
                  $this->db->escape($code."NON") => $this->db->escape($description." Non - Teaching"),
                  $this->db->escape($code."HEAD") => $this->db->escape($description." Head - Teaching"),
                  $this->db->escape($code."HEADNON") => $this->db->escape($description." Head Non - Teaching") );
                $sort = 1;
                foreach($app_arr as $app_code => $app_description){
                  $sort += 1;
                  $this->db->query("CALL prc_code_request_form_set($app_code,$app_description,'{$mngt}','{$credits}','0','0','0','0','0','0','0','0','0','0','0','0','0','0','$user','$request_id');");
                  $this->db->query("UPDATE code_request_form SET sort = '$sort' WHERE code_request = $app_code ");
                }
              }else{
                $this->db->query("CALL prc_code_request_form_set(".$this->db->escape($code).",".$this->db->escape($description).",'{$mngt}','{$credits}','{$bo}','{$up}','{$upt}','{$fd}','{$pres}','{$dhseq}','{$hhseq}','{$chseq}','{$cpseq}','{$dpseq}','{$upseq}','{$boseq}','{$fdseq}','{$pseq}','$user', '');");
              }
           
             $genderApplicable = $this->gibberish->decrypt($this->input->post("genderApplicable"), $toks);
             $this->db->query("UPDATE code_request_form SET genderApplicable = '{$genderApplicable}', details = '$details' WHERE code_request = ".$this->db->escape($code) ." ");
             $this->db->query("UPDATE code_request_form SET genderApplicable = '{$genderApplicable}', details = '$details' WHERE code_request = '".$code."NON"."' ");
             $this->db->query("UPDATE code_request_form SET genderApplicable = '{$genderApplicable}', details = '$details' WHERE code_request = '".$code."HEAD"."' ");
             $this->db->query("UPDATE code_request_form SET genderApplicable = '{$genderApplicable}', details = '$details' WHERE code_request = '".$code."HEADNON"."' ");

            $eligibilityPeriod = $this->input->post("eligibilityPeriod");

            if($eligibilityPeriod){
                $this->db->query("DELETE FROM code_request_eligibility_period WHERE code_request = '{$code}'");
                foreach(explode("|",$eligibilityPeriod) as $k => $v){
                    list($empStat,$lv,$credit_non) = explode("~u~",$v);
                    $empStat = $this->gibberish->decrypt($empStat, $toks);
                    // $count = $this->gibberish->decrypt($count, $toks);
                    // $mode = $this->gibberish->decrypt($mode, $toks);
                    $count = "";
                    $mode = "";
                    $lv = $this->gibberish->decrypt($lv, $toks);
                    $credit_non = $this->gibberish->decrypt($credit_non, $toks);
                    if($empStat != "undefined" && $mode != "undefined"){
                        $res = $this->db->query("INSERT INTO code_request_eligibility_period (code_request,emp_status,count,mode,credit,credit_non) VALUES('{$code}','{$empStat}','{$count}','{$mode}','{$lv}','{$credit_non}')");
                    }
                }
            }

            if($res) $msg = "Main Leave Acces has been ".(($this->gibberish->decrypt($this->input->post("id"), $toks)) ? 'updated' : 'saved') ." successfully.";
            else $msg = "Failed to process data.";
        }

        $return = array("msg" => $msg, "code" => $res);
        echo json_encode($return);
    }
    public function save_other_request(){ 
        $toks = $this->input->post("toks"); 
       $job = $toks ? $this->gibberish->decrypt($this->input->post('job'), $toks) : $this->input->post("job"); 
       $code = $toks ? $this->gibberish->decrypt($this->input->post('code'), $toks) : $this->input->post("code");
        if($job=="delete"){
            $res = $this->db->query("delete from code_request_form WHERE code_request='{$code}';");
            if($res) $msg = "Successfully deleted.";
            else $msg = "Failed to process data.";
        }else{
             $description = $toks ? $this->gibberish->decrypt($this->input->post('description'), $toks) : $this->input->post("description");
              $credits = $toks ? $this->gibberish->decrypt($this->input->post('credits'), $toks) : $this->input->post("credits");
            /*
            $ltype = $this->input->post("ltype");
            $dfrom = $this->input->post("dfrom");
            $dto = $this->input->post("dto");
            */
            if(!$code){
                $msg = "Code is required.";
                $res = 2;
                $return = array("msg" => $msg, "code" => $res);
                echo json_encode($return);
                return;
            }
            if(!$this->input->post("description")){
                $msg = "Description is required.";
                $res = 2;
                $return = array("msg" => $msg, "code" => $res);
                echo json_encode($return);
                return;
            }
            // $dhseq = $this->input->post("dhseq");
            // $hhseq = $this->input->post("hhseq");
            // $chseq = $this->input->post("chseq");
            // $cpseq = $this->input->post("cpseq");
            // $dpseq = $this->input->post("dpseq");
            // $upseq = $this->input->post("upseq");
            // $boseq = $this->input->post("boseq");
            // $fdseq = $this->input->post("fdseq");
            // $pseq  = $this->input->post("pseq");
            // $bo = $this->input->post("bo");
            // $up = $this->input->post("up");
            // $upt = $this->input->post("upt");
            // $fd = $this->input->post("fd");
            // $pres = $this->input->post("pres");
            // $user = $this->session->userdata("username");

            $dhseq = $toks ? $this->gibberish->decrypt($this->input->post('dhseq'), $toks) : $this->input->post("dhseq");
             $hhseq =$toks ? $this->gibberish->decrypt($this->input->post('hhseq'), $toks) : $this->input->post("hhseq");
             $chseq = $toks ? $this->gibberish->decrypt($this->input->post('chseq'), $toks) :$this->input->post("chseq");
             $cpseq =$toks ? $this->gibberish->decrypt($this->input->post('cpseq'), $toks) : $this->input->post("cpseq");
             $dpseq =$toks ? $this->gibberish->decrypt($this->input->post('dpseq'), $toks) : $this->input->post("dpseq");
             $upseq =$toks ? $this->gibberish->decrypt($this->input->post('upseq'), $toks) : $this->input->post("upseq");
             $boseq = $toks ? $this->gibberish->decrypt($this->input->post('boseq'), $toks) :$this->input->post("boseq");
             $fdseq =$toks ? $this->gibberish->decrypt($this->input->post('fdseq'), $toks) : $this->input->post("fdseq");
             $pseq  = $toks ? $this->gibberish->decrypt($this->input->post('pseq'), $toks) :$this->input->post("pseq");
             $bo =$toks ? $this->gibberish->decrypt($this->input->post('bo'), $toks) : $this->input->post("bo");
             $up =$toks ? $this->gibberish->decrypt($this->input->post('up'), $toks) : $this->input->post("up");
             $upt =$toks ? $this->gibberish->decrypt($this->input->post('upt'), $toks) : $this->input->post("upt");
             $fd = $toks ? $this->gibberish->decrypt($this->input->post('fd'), $toks) :$this->input->post("fd");
             $pres =$toks ? $this->gibberish->decrypt($this->input->post('pres'), $toks) : $this->input->post("pres");
             $user = $this->session->userdata("username");
             $res = 0;
             if($this->db->query("SELECT * FROM code_request_form WHERE code_request = ".$this->db->escape($code)." ")->num_rows() == 0){
              $this->db->query("INSERT INTO online_application_code (description) VALUES (".$this->db->escape($description).") ");
              $request_id = $this->db->insert_id();
              $this->db->query("CALL prc_code_request_form_set(".$this->db->escape($code).",".$this->db->escape($description).",'0','{$credits}','{$bo}','{$up}','{$upt}','{$fd}','{$pres}','{$dhseq}','{$hhseq}','{$chseq}','{$cpseq}','{$dpseq}','{$upseq}','{$boseq}','{$fdseq}','{$pseq}','$user', '$request_id');");
              $res = $this->db->query("UPDATE code_request_form SET ismain = '1' WHERE code_request = ".$this->db->escape($code)." ");

              $app_arr = array(
                $this->db->escape($code."NON") => $this->db->escape($description." Non Teaching"),
                $this->db->escape($code."HEAD") => $this->db->escape($description." for Head - Teaching"),
                $this->db->escape($code."HEADNON") => $this->db->escape($description." for Head - Non Teaching") );
              foreach($app_arr as $app_code => $app_description){
                $this->db->query("CALL prc_code_request_form_set($app_code,$app_description,'0','{$credits}','0','0','0','0','0','0','0','0','0','0','0','0','0','0','$user','$request_id');");
              }
            }else{
              $res = $this->db->query("CALL prc_code_request_form_set(".$this->db->escape($code).",".$this->db->escape($description).",'0','{$credits}','{$bo}','{$up}','{$upt}','{$fd}','{$pres}','{$dhseq}','{$hhseq}','{$chseq}','{$cpseq}','{$dpseq}','{$upseq}','{$boseq}','{$fdseq}','{$pseq}','$user', '');");
              
            }

            if($res) $msg = "Other Leave Acces has been ".(($this->input->post("isedit")) ? 'updated' : 'saved') ." successfully.";
            else $msg = "Failed to process data.";
        }

        $return = array("msg" => $msg, "code" => 1);
        echo json_encode($return);
    }
    public function leavedatevalidity(){
        echo $this->extras->leavedatevalidity($this->input->post());
    }
    public function save_tax(){ 
       $toks = $this->input->post("toks");
       $job = $this->gibberish->decrypt($this->input->post("job"), $toks);
       $taxid = $this->gibberish->decrypt($this->input->post("taxid"), $toks); 
       $taxtype = $this->gibberish->decrypt($this->input->post("taxtype"), $toks);
       $taxstatus = $this->gibberish->decrypt($this->input->post("taxstatus"), $toks);
       $basic = $this->gibberish->decrypt($this->input->post("basic"), $toks);
       $basicamt = $this->gibberish->decrypt($this->input->post("basicamt"), $toks);
       $percent = $this->gibberish->decrypt($this->input->post("percent"), $toks);
       $taxrange = $this->gibberish->decrypt($this->input->post("taxrange"), $toks);
       $exemption = $this->gibberish->decrypt($this->input->post("exemption"), $toks);
       
       if($job=="delete"){
         $this->db->query("delete from code_tax WHERE tax_id='{$taxid}';");
         echo "Successfully deleted tax setup.";
       }else{
         $this->db->query("CALL prc_tax_set('{$taxid}','{$taxtype}','{$taxstatus}','{$basic}','{$basicamt}','{$percent}','{$taxrange}','{$exemption}');");
         if(!$taxid) echo "Successfully added new tax setup.";
         else echo "Successfully updated tax setup.";
       }
    }
    public function manage_income(){
       $data['code'] = $this->input->post("code");
       $this->load->view('maintenance/income_details', $data); 
    }
    public function save_income(){ 
       $job = $this->input->post("job"); 
       $code = $this->input->post("code");
       if($job=="delete"){
         $this->db->query("delete from incomes WHERE code_income='{$code}';");
       }else{
         $description = $this->input->post("description");
         $taxable = $this->input->post("taxable");
         $this->db->query("CALL prc_incomes_set('{$code}','{$description}',0,'{$taxable}');");
       }
    }
    public function manage_deduction(){
       $data['code'] = $this->input->post("code");
       
       $this->load->view('maintenance/deduction_details', $data); 
    }
    public function save_deduction(){ 
       $job = $this->input->post("job"); 
       $code = $this->input->post("code");
       if($job=="delete"){
         $this->db->query("delete from deductions WHERE code_deduction='{$code}';");
       }else{
         $description = $this->input->post("description");
         $type = $this->input->post("type");
         $this->db->query("CALL prc_deductions_set('{$code}','{$description}',0,'{$type}');");
       }
    }
    public function dbuserlist(){   
        # concat(lastname,\', \',firstname,\' \',middlename) as fullname
        $this->load->library('datatables');
        $this->datatables
             ->select('id,username,lastname,firstname,middlename')
             ->edit_column('id', 
                           '<div class="btn-group">
                              <a class="btn btn-info" href="#modal-view" tag="access_d" data-toggle="modal" userid="$1"><i class="glyphicon glyphicon-list-alt"></i></a>
                              <a class="btn btn-info" href="#modal-view" tag="access_m" data-toggle="modal" userid="$1"><i class="glyphicon glyphicon-envelope"></i></a>
                              <a class="btn btn-info" href="#modal-view" tag="edit_d" data-toggle="modal" userid="$1"><i class="glyphicon glyphicon-edit"></i></a>
                              <a class="btn btn-danger" href="#" tag="delete_d" userid="$1"><i class="glyphicon glyphicon-trash"></i></a>
                              <a class="btn btn-primary" href="#modal-view" tag="edit_ppassword" data-toggle="modal" userid="$1"><i class="glyphicon glyphicon-edit"></i></a>
                            </div>', 
                           'id')
             ->edit_column('lastname', '$1, $2 $3', 'lastname,firstname,middlename')
             ->where('type <>',"SUPER ADMIN")
             ->from('user_info');
             
          # ->edit_column('id', '<input type="checkbox" value="$1">', 'id')   
          #->edit_column('userid', '<a href="profiles/edit/$1">$2</a>', 'userno, userid');
        $results = $this->datatables->generate('json');
        echo $results;
    }
    public function dbschedulelist(){   
        # concat(lastname,\', \',firstname,\' \',middlename) as fullname
        $this->load->library('datatables');
        $this->datatables
             ->select('schedid,schedcode,description,tardy_start')
             ->edit_column('schedid', 
                           '', 
                           'schedid')
             ->from('code_schedule');
        $results = $this->datatables->generate('json');
        
        print_r($results);
        // var_dump($results); die;
    }
    public function editpayslipPassword(){
      $toks = $this->input->post("toks");
       $data['job'] = $toks ? $this->gibberish->decrypt( $this->input->post("job"), $toks ) : $this->input->post("job");
       $data['uid'] = $toks ? $this->gibberish->decrypt( $this->input->post("uid"), $toks ) : $this->input->post("uid");       
       $this->load->view('maintenance/editpayslipPassword', $data);     
    }
    public function addnewuser(){
      $toks = $this->input->post("toks");
       $data['job'] = $toks ? $this->gibberish->decrypt( $this->input->post("job"), $toks ) : $this->input->post("job");
       $data['uid'] = $toks ? $this->gibberish->decrypt( $this->input->post("uid"), $toks ) : $this->input->post("uid");       
       $this->load->view('maintenance/add_user', $data);     
    }
    public function useraccess(){
      $toks = $this->input->post("toks");
       $data['uid'] = $toks ? $this->gibberish->decrypt( $this->input->post("uid"), $toks ) : $this->input->post("uid");         
       $this->load->view('maintenance/useraccess', $data);     
    }
    public function hrmngmnt(){
      $toks = $this->input->post("toks");
       $data['uid'] = $toks ? $this->gibberish->decrypt( $this->input->post("uid"), $toks ) : $this->input->post("uid");   
       $this->load->view('maintenance/hrmsgreceiver', $data);
    }
    public function hrmngmntf(){
      $toks = $this->input->post("toks");
       $data['uid']  = $toks ? $this->gibberish->decrypt( $this->input->post("uid"), $toks ) : $this->input->post("uid");   
       $data['val']  = $toks ? $this->gibberish->decrypt( $this->input->post("val"), $toks ) : $this->input->post("val");   
       echo $this->employee->sendMessageList($data);     
    }

    public function loadScheduleTable(){
        $data['records'] = $this->setup->SCSetup();
        $this->load->view("maintenance/scheduleTable", $data);
    }

    public function loadRequestTable(){
        $data['records'] = $this->setup->RTSetup();
        $this->load->view("maintenance/requestTable", $data);
    }

    public function proconfig(){
       $toks = $this->input->post("toks");
       $data['eid']     = $toks? $this->gibberish->decrypt( $this->input->post("eid"), $toks ) : $this->input->post('eid');
       $data['dfrom']   = $toks? $this->gibberish->decrypt( $this->input->post("dfrom"), $toks ) :  $this->input->post('dfrom');
       $data['dto']     = $toks? $this->gibberish->decrypt( $this->input->post("dto"), $toks ) :  $this->input->post('dto');
       $return = $this->employee->proconfig($data);
       echo json_encode($return);
    }
    public function addnewschedule(){
      $toks = $this->input->post("toks");
       $data['job'] = $this->gibberish->decrypt( $this->input->post("job"), $toks );
       $data['schedid'] =  $this->gibberish->decrypt( $this->input->post("schedid"), $toks );       
       $data['isedit'] =  $this->gibberish->decrypt( $this->input->post("isedit"), $toks );
       $this->load->view('maintenance/add_schedule', $data);     
    }      
      
    public function saveuser(){
       $toks = $this->input->post("toks");
       $formdata = $this->input->post("formdata");
       if($formdata){
          $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $formdata, $toks ));
          extract($data);
          // echo "<pre>"; print_r($data);
          $username = $u_username;
          $firstname = str_replace("%C3%B1","ñ",$u_firstname);
          $middlename = str_replace("%C3%B1","ñ",$u_middlename);
          $lastname = str_replace("%C3%B1","ñ",$u_lastname);
          $password = $u_password;
          if(isset($ppassword)) $payslippassword = md5($ppassword);
          $type = $u_type;
          $email = ($type == "ADMIN") ? $emailAdmin : '';
          if($email) $email =  str_replace("%40","@",$email);
          $campus = ($type == "ADMIN") ? $campusAdmin : '';

       }else{
         $job = $toks ? $this->gibberish->decrypt( $this->input->post("job"), $toks ) : $this->input->post("job");
         $uid = $toks ? $this->gibberish->decrypt( $this->input->post("uid"), $toks ) : $this->input->post("uid");
         $username = $toks ? $this->gibberish->decrypt( $this->input->post("u_username"), $toks ) : $this->input->post("u_username");
         $firstname = $toks ? $this->gibberish->decrypt( $this->input->post("u_firstname"), $toks ) : $this->input->post("u_firstname");
         $middlename = $toks ? $this->gibberish->decrypt( $this->input->post("u_middlename"), $toks ) : $this->input->post("u_middlename");
         $lastname = $toks ? $this->gibberish->decrypt( $this->input->post("u_lastname"), $toks ) : $this->input->post("u_lastname");
         $password = $toks ? $this->gibberish->decrypt( $this->input->post("u_password"), $toks ) : $this->input->post("u_password");
         $payslippassword = $toks ? $this->gibberish->decrypt( $this->input->post("ppassword"), $toks ) : $this->input->post("ppassword");  
         $payslippassword = md5($payslippassword);
         $type = $toks ? $this->gibberish->decrypt( $this->input->post("u_type"), $toks ) : $this->input->post("u_type");
         $email =  $toks ? $this->gibberish->decrypt( $this->input->post("emailAdmin"), $toks ) : $this->input->post("emailAdmin");
         $campus = ($type == "ADMIN")? $toks ? $this->gibberish->decrypt( $this->input->post("campusAdmin"), $toks ) : $this->input->post("campusAdmin"):"";
       }
       if($password) $password =  str_replace("%40","@",$password);
       $cansave = true;
       $message = "";
       $stat = 0;

       if($job=="new"){
          if($cansave){
           $sql = $this->employee->isUsernameExist($username);
           if ($sql) {
             $message = "Username already taken.";
             $cansave = false;
           }
           $stat = !$cansave?1:$stat;
          }  
          
          if($cansave){
           $sql = $this->db->query("select * from user_info where firstname='{$firstname}' and lastname='{$lastname}' and username='{$username}'");
           if($cansave) $message = "User already exists.";
           $cansave = $sql->num_rows()==0;
           $stat = !$cansave?2:$stat;
          }
       }
       else if ($job == "editpayslipPassword") {
          $cansave = false;
          $query = $this->db->query("UPDATE user_info SET ppass = '{$payslippassword}' WHERE id='{$uid}'");
          if ($query === true) {
              $message = "Payslip Password Successfully Saved."; 
          }
          else
          {
            $message = $query;
          }
       }
       else if($job=="delete"){
          $this->db->query("delete from user_info where id='{$uid}'");
          $cansave = false;  
       }

       if($cansave && $type == "ADMIN" && $email){
           $sql = $this->db->query("SELECT * FROM employee a INNER JOIN user_info b WHERE b.id != '{$uid}' AND b.email = '{$email}' OR a.email = '{$email}' OR a.personal_email = '{$email}' limit 1");
           if($cansave) $message = "Email Already Exists.";
           $cansave = $sql->num_rows()==0;
           $stat = !$cansave?3:$stat;
        }
       
       if($cansave){
          if($uid){
           if($type == "EMPLOYEE") $this->db->query("update user_info set
                                                 ".($password?"password=MD5('{$password}'),":"")."
                                                  email='{$email}',
                                                  campus='{$campus}',
                                                  type='{$type}' 
                            WHERE id='{$uid}'");
           else $this->db->query("update user_info set
                                                 ".($password?"password=MD5('{$password}'),":"")."
                                                  lastname='{$lastname}',
                                                  firstname='{$firstname}',
                                                  middlename='{$middlename}',
                                                  email='{$email}',
                                                  campus='{$campus}',
                                                  type='{$type}' 
                            WHERE id='{$uid}'");
            $message = "User data has been updated.";  
          }
          else{
           $this->db->query("insert into user_info (username,password,lastname,firstname,middlename,type,status,createdby,email,campus) values('{$username}',MD5('{$password}'),'{$lastname}','{$firstname}','{$middlename}','{$type}','ACTIVE','".$this->session->userdata('userid')."','{$email}','{$campus}')");
           $message = "User has been Added."; 
          }
           
       }
       echo "<user>
                <status>{$stat}</status>
                <message>{$message}</message>
             </user>"; 
    }
    public function saveschedule(){
      $toks = $this->input->post("toks");
      $job = $this->gibberish->decrypt( $this->input->post("job"), $toks );
      $formdata = $this->input->post("formdata");

      if($formdata){
        $formdata = base64_decode(urldecode($formdata));
        $data = Globals::convertFormDataToArray($formdata);
        $toks = $data['toks'];

        if($toks){
          foreach ($data as $key => $value) {
            if($key != "toks") $data[$key] = $this->gibberish->decrypt( $value, $toks );
          }
        }
      }
      $datenow = date("Y-m-d");
      if($job=="delete"){
       $schedid = $this->gibberish->decrypt( $this->input->post("schedid"), $toks );
       $query = $this->db->query("SELECT code FROM code_type WHERE schedid='$schedid';");
       if($query->num_rows() > 0){
           $qcode = $query->row(0)->code;
           $querycheck = $this->db->query("SELECT * FROM employee WHERE (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1') AND (emptype='$qcode' OR empshift='$schedid')");
           // echo "<pre>";print_r($this->db->last_query());die;
           if($querycheck->num_rows() > 0){
                echo    "Failed to delete.. This schedule is already in use..";
            }else{   
                $this->db->query("DELETE FROM code_schedule WHERE schedid='$schedid'");
                echo    "Schedule has been deleted successfully.";
            }
       }else{    
           $this->db->query("DELETE FROM code_schedule WHERE schedid='$schedid'");
           echo    "Schedule has been deleted successfully.";
       }
       
      }else{  
      
      $schedid = isset($data['schedid'])? $data['schedid'] : $this->gibberish->decrypt( $this->input->post("schedid"), $toks );

      $code = isset($data['f_code'])? $data['f_code'] : $this->gibberish->decrypt( $this->input->post("f_code"), $toks );

      // $this->form_validation->set_rules('f_code', 'code', 'required');
      $description = isset($data['f_description'])? $data['f_description'] : $this->gibberish->decrypt( $this->input->post("f_description"), $toks );

      $isedit   = isset($data['isedit'])? $data['isedit'] : $this->gibberish->decrypt( $this->input->post("isedit"), $toks );
      $total_hours_per_week   = isset($data['f_hours'])? $data['f_hours'] : $this->gibberish->decrypt( $this->input->post("f_hours"), $toks )?$this->gibberish->decrypt( $this->input->post("f_hours"), $toks ):"";
      $flexible   = isset($data['f_flexible'])? $data['f_flexible'] : $this->gibberish->decrypt( $this->input->post("f_flexible"), $toks );
      $no_dtr   = isset($data['no_dtr'])? $data['no_dtr'] :  $this->gibberish->decrypt( $this->input->post("no_dtr"), $toks )?$this->gibberish->decrypt( $this->input->post("no_dtr"), $toks ):"NO";
      $hours   = isset($data['f_hrs'])? $data['f_hrs'] : $this->gibberish->decrypt( $this->input->post("f_hrs"), $toks );
      $breaktime   = isset($data['breaktime'])? $data['breaktime'] : $this->gibberish->decrypt( $this->input->post("breaktime"), $toks );
      $mode   = isset($data['f_mode'])? $data['f_mode'] : $this->gibberish->decrypt( $this->input->post("f_mode"), $toks );
      if(!$isedit){
          // if new data
          $schedidexists = $this->db->query("SELECT * FROM code_schedule WHERE schedcode LIKE '$code'");

          if($schedidexists->num_rows() > 0 && !$schedid)    echo "Code already exists!.";
          else{
            // comment by : justin (with e)
            // error : unable to save new schedule
            /*$this->db->query("CALL prc_code_schedule_set('{$schedid}',
                                                   '{$code}',
                                                   '{$description}',
                                                   @a)");
      			$q = $this->db->query("SELECT @a as SCHEDID")->result();                                                   
                  $schedid = $q[0]->SCHEDID; 
      			$this->db->query("UPDATE code_schedule SET total_hours_per_week = '{$total_hours_per_week}' , flexible = '{$flexible}' , hours = '{$hours}' , mode = '{$mode}'  WHERE schedid = '{$schedid}'");*/
            // end of comment

            // new added by justin (with e)
            $this->db->query("INSERT INTO code_schedule (schedcode, description, flexible, hours, breaktime, mode, no_dtr) VALUES ('{$code}','{$description}','{$flexible}','{$hours}','{$breaktime}','{$mode}','{$no_dtr}')");
            $schedid = $this->db->query("SELECT * FROM code_schedule WHERE schedcode LIKE '$code'")->row()->schedid;
            // end of new added

            /** Insert new data */
            if($this->gibberish->decrypt( $this->input->post("timesched"), $toks ) || isset($data['timesched'])){
              $timescheds = isset($data['timesched'])? $data['timesched'] :$this->gibberish->decrypt( $this->input->post("timesched"), $toks );
            $sched_list = explode("|",$timescheds);
                foreach($sched_list as $slist){
                  $nosched = 0;
                  $halfsched = $earlyd = 0;
                  $weekly_flexible = "weekly";
                  list($dw,$tsched,$tstart,$astart,$tend,$aend,$nosched,$halfsched,$earlyd, $weekly_flexible) = explode("~u~",$slist);
                    // $tsched = $this->gibberish->decrypt($tsched, $toks);

                    $extime = explode("-",$tsched);
                    $start_time = isset($extime[0]) ? date("H:i:s",strtotime($extime[0])) : '';
                    $end_time = isset($extime[1]) ? date("H:i:s",strtotime($extime[1])) : '';
                    $tstart = date("H:i:s",strtotime($tstart));
                    $astart = date("H:i:s",strtotime($astart));
                    $tend = date("H:i:s",strtotime($tend));
                    $aend = date("H:i:s",strtotime($aend));
                    $halfsched = date("H:i:s",strtotime($halfsched));
                    $earlyd = date("H:i:s",strtotime($earlyd));
                    /*$tstart = date("H:i:s",strtotime($this->gibberish->decrypt($tstart, $toks)));
                    $astart = date("H:i:s",strtotime($this->gibberish->decrypt($astart, $toks)));
                    $tend = date("H:i:s",strtotime($this->gibberish->decrypt($tend, $toks)));
                    $aend = date("H:i:s",strtotime($this->gibberish->decrypt($aend, $toks)));
                    $earlyd = date("H:i:s",strtotime($this->gibberish->decrypt($earlyd, $toks)));
                    $dw = $this->gibberish->decrypt($dw, $toks);
                    $nosched = $this->gibberish->decrypt($nosched, $toks);
                    $halfsched = $this->gibberish->decrypt($halfsched, $toks);*/
                    
                    #echo $start_time." - ".$end_time." - ".$tstart." - ".$astart." - ".$astart." - ".$tend." - ".$aend." - ".$nosched."<br />";die;
                    
                    $dow = "";
                    switch($dw){
                      case "M" : $dow = 1; break;
                      case "T" : $dow = 2; break;
                      case "W" : $dow = 3; break;
                      case "TH" : $dow = 4; break;
                      case "F" : $dow = 5; break;
                      case "S" : $dow = 6; break;
                      case "SUN" : $dow = 0; break;
                    }
                   
                    $this->db->query("CALL prc_code_schedule_detail_set('{$schedid}',
                                                                        '{$start_time}',
                                                                        '{$end_time}',
                                                                        '{$dw}',
                                                                        '{$dow}',
                                                                        '{$tstart}',
                                                                        '{$astart}',
                                                                        '{$tend}',
                                                                        '{$aend}',
                                                                        '{$nosched}',
                                                                        '{$halfsched}',
                                                                        '{$earlyd}',
                                                                        '{$flexible}',
                                                                        '{$hours}',
                                                                        '{$breaktime}',
                                                                        '{$mode}',
                                                                        '".$this->session->userdata("userid")."')");
                    $last_inserted = $this->db->query("SELECT id FROM code_schedule_detail WHERE schedid='$schedid' ORDER BY id DESC LIMIT 1")->row()->id;
                    $this->db->query("UPDATE code_schedule_detail SET weekly_flexible = '$weekly_flexible' WHERE id = '$last_inserted' AND schedid = '$schedid'"); 
                }
            }
			
            if(isset($uid) && $uid) echo "Schedule has been saved successfully.";
            else echo "Schedule has been saved successfully.";
          }
      }else{     

          // if edit         
          // comment by justin (with e)
          // error : unable to update data
          /*$this->db->query("CALL prc_code_schedule_set('{$schedid}',
                                                   '{$code}',
                                                   '{$description}',
                                                   @a)");
    			$q = $this->db->query("SELECT @a as SCHEDID")->result();                                                   
    			$schedid = $q[0]->SCHEDID; */  
          // end of comment
    			$this->db->query("UPDATE code_schedule SET schedcode = '{$code}', description = '{$description}' , total_hours_per_week = '{$total_hours_per_week}' , flexible = '{$flexible}' , hours = '{$hours}' , breaktime = '{$breaktime}' , mode = '{$mode}' , no_dtr = '{$no_dtr}'  WHERE schedid = '{$schedid}'");
          $this->db->query("delete from code_schedule_detail where schedid='$schedid'"); 

          /** Insert new data */
    		  if($this->input->post("timesched") || isset($data['timesched'])){
            $timescheds = isset($data['timesched'])? $data['timesched'] : $this->input->post("timesched");
    			$sched_list = explode("|",$timescheds);
              foreach($sched_list as $slist){
                $nosched = 0;
                $halfsched = $earlyd = 0;
                $weekly_flexible = "weekly";
                list($dw,$tsched,$tstart,$astart,$tend,$aend,$nosched,$halfsched,$earlyd, $weekly_flexible) = explode("~u~",$slist);
                  // $tsched = $this->gibberish->decrypt($tsched, $toks);
                  $extime = explode("-",$tsched);
                  $start_time = isset($extime[0]) ? date("H:i:s",strtotime($extime[0])) : '';
                    $end_time = isset($extime[1]) ? date("H:i:s",strtotime($extime[1])) : '';
                    $tstart = date("H:i:s",strtotime($tstart));
                    $astart = date("H:i:s",strtotime($astart));
                    $tend = date("H:i:s",strtotime($tend));
                    $aend = date("H:i:s",strtotime($aend));
                    $halfsched = date("H:i:s",strtotime($halfsched));
                    $earlyd = date("H:i:s",strtotime($earlyd));
                  /*$dw = $this->gibberish->decrypt($dw, $toks);
                    $nosched = $this->gibberish->decrypt($nosched, $toks);
                    $halfsched = $this->gibberish->decrypt($halfsched, $toks);
                  $tstart = date("H:i:s",strtotime($this->gibberish->decrypt($tstart, $toks)));
                  $astart = date("H:i:s",strtotime($this->gibberish->decrypt($astart, $toks)));
                  $tend = date("H:i:s",strtotime($this->gibberish->decrypt($tend, $toks)));
                  $aend = date("H:i:s",strtotime($this->gibberish->decrypt($aend, $toks)));
                  $earlyd = date("H:i:s",strtotime($this->gibberish->decrypt($earlyd, $toks)));*/
                  
                  #echo $start_time." - ".$end_time." - ".$tstart." - ".$astart." - ".$astart." - ".$tend." - ".$aend." - ".$nosched."<br />";die;
                  
                  $dow = "";
                  switch($dw){
                    case "M" : $dow = 1; break;
                    case "T" : $dow = 2; break;
                    case "W" : $dow = 3; break;
                    case "TH" : $dow = 4; break;
                    case "F" : $dow = 5; break;
                    case "S" : $dow = 6; break;
                    case "SUN" : $dow = 0; break;
                  }
                 
                  $this->db->query("CALL prc_code_schedule_detail_set('{$schedid}',
                                                                      '{$start_time}',
                                                                      '{$end_time}',
                                                                      '{$dw}',
                                                                      '{$dow}',
                                                                      '{$tstart}',
                                                                      '{$astart}',
                                                                      '{$tend}',
                                                                      '{$aend}',
                                                                      '{$nosched}',
                                                                      '{$halfsched}',
                                                                      '{$earlyd}',
                                                                      '{$flexible}',
                                                                      '{$hours}',
                                                                      '{$breaktime}',
                                                                      '{$mode}',
                                                                      '".$this->session->userdata("userid")."')");
                  $last_inserted = $this->db->query("SELECT id FROM code_schedule_detail WHERE schedid='$schedid' ORDER BY id DESC LIMIT 1")->row()->id;
                  $this->db->query("UPDATE code_schedule_detail SET weekly_flexible = '$weekly_flexible' WHERE id = '$last_inserted' AND schedid = '$schedid'"); 
                  // echo "<pre>"; print_r($this->db->last_query()); die;
              }
    		  }

    		  
            echo "Schedule has been updated successfully.";
    		
          }                    
      }
    }
    public function manage_sss(){
       $toks = $this->input->post("toks");
       $data['id'] = $this->input->post("id");
       $data['job'] = $this->input->post("job");
       if($toks){
        foreach($data as $key => $val){
          $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
       }
       $this->load->view('maintenance/sss_details',$data); 
    }
    public function save_sss(){
        $toks= $this->input->post("toks");
        $job= $this->gibberish->decrypt($this->input->post("job"), $toks);
        $sssid= $this->gibberish->decrypt($this->input->post("sssid"), $toks);
        $salfrom= $this->gibberish->decrypt($this->input->post("salfrom"), $toks); 
        $salto= $this->gibberish->decrypt($this->input->post("salto"), $toks);
        $salrange= $this->gibberish->decrypt($this->input->post("salrange"), $toks);
        $er= $this->gibberish->decrypt($this->input->post("er"), $toks);
        $ec= $this->gibberish->decrypt($this->input->post("ec"), $toks);
        $ee= $this->gibberish->decrypt($this->input->post("ee"), $toks);
        $tc= $this->gibberish->decrypt($this->input->post("tc"), $toks);

        $prov_er= $this->gibberish->decrypt($this->input->post("prov_er"), $toks);
        $prov_ee= $this->gibberish->decrypt($this->input->post("prov_ee"), $toks);
        $total_er= $this->gibberish->decrypt($this->input->post("total_er"), $toks);
        $total_ee= $this->gibberish->decrypt($this->input->post("total_ee"), $toks);
        $total= $this->gibberish->decrypt($this->input->post("total"), $toks);
        $year= $this->gibberish->decrypt($this->input->post("year"), $toks);
        if($sssid<>""){
            if($job=="delete") {
                $this->db->query("DELETE FROM sss_deduction WHERE id='$sssid'");
            }
            else{
            $this->db->query("UPDATE 
                                sss_deduction
                              SET
                                compensationfrom='$salfrom',
                                compensationto='$salto',
                                salary_range='$salrange',
                                emp_er='$er',
                                emp_con='$ec',
                                emp_ee='$ee',

                                provident_er='$prov_er',
                                provident_ee='$prov_ee',
                                total_er='$total_er',
                                total_ee='$total_ee',
                                -- total='$total',
                                year='$year',

                                total_contribution='$tc'
                              WHERE
                                id='$sssid'");
            }
        }else{
            $this->db->query("INSERT INTO 
                                sss_deduction 
                                (compensationfrom,
                                compensationto,
                                salary_range,
                                emp_er,
                                emp_con,
                                emp_ee,
                                provident_er,
                                provident_ee,
                                total_er,
                                total_ee,
                                -- total,
                                year,
                                total_contribution) 
                             values(
                                '$salfrom',
                                '$salto',
                                '$salrange',
                                '$er',
                                '$ec',
                                '$ee',
                                '$prov_er',
                                '$prov_ee',
                                '$total_er',
                                '$total_ee',
                                -- '$total',
                                '$year',
                                '$tc')");    
        }
    }
    public function manage_philhealth(){
       $toks = $this->input->post("toks");
       $data['id'] = $this->input->post("id");
       $data['job'] = $this->input->post("job");
       if($toks){
        foreach($data as $key => $val){
          $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
       }
       $this->load->view('maintenance/philhealth_details',$data); 
    }
    public function save_philhealth(){
        $toks= $this->input->post("toks");
        $job= $this->gibberish->decrypt($this->input->post("job"), $toks);
        $philhealthid= $this->gibberish->decrypt($this->input->post("philhealthid"), $toks);
        $salfrom= $this->gibberish->decrypt($this->input->post("salfrom"), $toks); 
        $salto= $this->gibberish->decrypt($this->input->post("salto"), $toks);
        $salrange= $this->gibberish->decrypt($this->input->post("salrange"), $toks);
        $salbase= $this->gibberish->decrypt($this->input->post("salbase"), $toks);
        $er= $this->gibberish->decrypt($this->input->post("er"), $toks);
        $ee= $this->gibberish->decrypt($this->input->post("ee"), $toks);
        $tc= $this->gibberish->decrypt($this->input->post("tc"), $toks);
        if($philhealthid<>""){
            if($job=="delete") {
                $this->db->query("DELETE FROM philhealth_deduction WHERE id='$philhealthid'");
            }
            else{
            $this->db->query("UPDATE 
                                philhealth_deduction
                              SET
                                compensationfrom='$salfrom',
                                compensationto='$salto',
                                salary_range='$salrange',
                                salary_base='$salbase',
                                emp_er='$er',
                                emp_ee='$ee',
                                total_contribution='$tc'
                              WHERE
                                id='$philhealthid'");
            }
        }else{
            $this->db->query("INSERT INTO 
                                philhealth_deduction 
                                (compensationfrom,
                                compensationto,
                                salary_range,
                                salary_base,
                                emp_er,
                                emp_ee,
                                total_contribution) 
                             values(
                                '$salfrom',
                                '$salto',
                                '$salrange',
                                '$salbase',
                                '$er',
                                '$ee',
                                '$tc')");
        }
    }
     public function manage_hdmf(){
       $toks = $this->input->post("toks");
       $data['id'] = $this->input->post("id");
       $data['job'] = $this->input->post("job");
       if($toks){
        foreach($data as $key => $val){
          $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
       }
       $this->load->view('maintenance/hdmf_details',$data); 
    }
    public function save_hdmf(){
        $toks= $this->input->post("toks");
        $job= $this->gibberish->decrypt($this->input->post("job"), $toks);
        $hdmfid= $this->gibberish->decrypt($this->input->post("hdmfid"), $toks);
        $salfrom= $this->gibberish->decrypt($this->input->post("salfrom"), $toks); 
        $salto= $this->gibberish->decrypt($this->input->post("salto"), $toks);
        $salrange= $this->gibberish->decrypt($this->input->post("salrange"), $toks);
        $salbase= $this->gibberish->decrypt($this->input->post("salbase"), $toks);
        $er= $this->gibberish->decrypt($this->input->post("er"), $toks);
        $ee= $this->gibberish->decrypt($this->input->post("ee"), $toks);
        $tc= $this->gibberish->decrypt($this->input->post("tc"), $toks);
        if($hdmfid<>""){
            if($job=="delete") {
                $this->db->query("DELETE FROM hdmf_deduction WHERE id='$hdmfid'");
            }
            else{
            $this->db->query("UPDATE 
                                hdmf_deduction
                              SET
                                compensationfrom='$salfrom',
                                compensationto='$salto',
                                salary_range='$salrange',
                                salary_base='$salbase',
                                emp_er='$er',
                                emp_ee='$ee',
                                total_contribution='$tc'
                              WHERE
                                id='$hdmfid'");
            }
        }else{
            $this->db->query("INSERT INTO 
                                hdmf_deduction 
                                (compensationfrom,
                                compensationto,
                                salary_range,
                                salary_base,
                                emp_er,
                                emp_ee,
                                total_contribution) 
                             values(
                                '$salfrom',
                                '$salto',
                                '$salrange',
                                '$salbase',
                                '$er',
                                '$ee',
                                '$tc')");
        }
    }
    
    public function holidaytypelist(){   
        $this->load->library('datatables');
        $this->datatables
             ->select('holiday_type,holiday_code,description')
             ->edit_column('holiday_type', 
                           '<a class="btn btn-info" href="#dtr-modal" tag="edit_d" data-toggle="modal" holiday_type="$1"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                              <a class="btn btn-danger" href="#" tag="delete_d" holiday_type="$1"><i class="glyphicon glyphicon-trash"></i></a>', 
                           'holiday_type')
             ->from('code_holiday_type');
        $results = $this->datatables->generate('json');
        echo $results;
    }
    
    public function addnewholidaytype(){
       $toks = $this->input->post("toks");
       $data['job'] = $this->gibberish->decrypt( $this->input->post("job"), $toks );
       $data['holiday_type'] = $this->gibberish->decrypt( $this->input->post("holiday_type"), $toks );   
       $this->load->view('maintenance/addnewholidaytype', $data);     
    }

    public function checkifCodeExist(){
      $toks = $this->input->post("toks");
      $code = $toks ?  $this->gibberish->decrypt( $this->input->post("code"), $toks ) : $this->input->post("code");
      $table = $toks ? $this->gibberish->decrypt( $this->input->post("table"), $toks ) : $this->input->post("table");
      $holiday_type = $toks ? $this->gibberish->decrypt( $this->input->post("holiday_type"), $toks ) : $this->input->post("holiday_type");
      echo $this->extras->checkifCodeExist($code, $table, $holiday_type);
    }
    
    public function saveholidaytype(){
      $toks = $this->input->post("toks");
      $job = $toks ? $this->gibberish->decrypt( $this->input->post("job"), $toks ) : $this->input->post("job");
      $msg = "";
      if($job=="delete"){
       $holiday_type = $this->gibberish->decrypt( $this->input->post("holiday_type"), $toks ); 
      $query =  $this->db->query("delete from code_holiday_type where holiday_type='{$holiday_type}'"); 
      $msg="Holiday Type has been deleted successfully.";
      }else{  
      
      $holiday_type = $this->gibberish->decrypt( $this->input->post("holiday_type"), $toks ); 
      $holiday_code = $this->gibberish->decrypt( $this->input->post("holiday_code"), $toks );
      $holiday_rate = $this->gibberish->decrypt( $this->input->post("holiday_rate"), $toks ); 
      $description  = $this->gibberish->decrypt( $this->input->post("holiday_description"), $toks ); 
      $withPay = "YES"; 
	   $worked_hours = $this->input->post("worked_hours"); 
		 $worked_rate = $this->input->post("worked_rate"); 
		 $worked_excess = $this->input->post("worked_excess"); 
		 $restday_hours = $this->input->post("restday_hours"); 
		 $restday_rate = $this->input->post("restday_rate"); 
		 $restday_excess = $this->input->post("restday_excess"); 
   
      $this->db->query("CALL prc_code_holiday_type_set('{$holiday_type}','{$holiday_code}','{$holiday_rate}','{$description}','{$withPay}','{$worked_hours}','{$worked_rate}','{$worked_excess}','{$restday_hours}','{$restday_rate}','{$restday_excess}')");
     
     if ($holiday_type) {
          $msg = "Holiday Type has been updated successfully.";
     }
     else
     {
          $msg = "Holiday Type has been saved successfully.";
     }

      } 
     echo $msg;
    }

    public function deleteholiday(){
      $toks = $this->input->post("toks");
      $id = $this->gibberish->decrypt( $this->input->post("holiday_id"), $toks ); 
      $this->db->query("delete from code_holidays where holiday_id='{$id}'");
      echo "Holiday Name has been deleted successfully!";
    }

    public function holidaylist(){   
        $this->load->library('datatables');
        $this->datatables
             ->select('a.holiday_id,a.code,a.hdescription,b.description,a.is_active',false)
             ->join('code_holiday_type b','b.holiday_type=a.holiday_type','inner')
             ->edit_column('a.holiday_id',
                           '<a class="btn btn-info" href="#dtr-modal" tag="edit_d" data-toggle="modal" holiday_id="$1"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                            <a class="btn btn-danger" href="#" tag="delete_d" holiday_id="$1"><i class="glyphicon glyphicon-trash"></i></a>', 
                           'a.holiday_id')
             ->from('code_holidays a');
        $results = $this->datatables->generate('json');
        echo $results;
    }
    
    public function save_holidays(){ 
       $toks = $this->input->post("toks");
       $job = $this->input->post("job");
       $holiday_id = $this->gibberish->decrypt( $this->input->post("holiday_id"), $toks ); 
       $response = array();
       $response["what"] = $this->input->post("job");
       $response["id"] = $this->input->post("holiday_id");
       //$response["code"] = $this->input->post("code_status");
       //echo "<pre>". var_dump($response);die;
       // print(json_encode($response));

       if($job=="del"){
        $this->db->query("delete from code_holidays WHERE holiday_id='{$holiday_id}'");
       }else{ 
        $code = $this->gibberish->decrypt( $this->input->post("mh_code"), $toks ); 
        $description =  $this->gibberish->decrypt( $this->input->post("mh_description"), $toks ); 
        $type =  $this->gibberish->decrypt( $this->input->post("mh_type"), $toks ); 
        $active = $this->gibberish->decrypt( $this->input->post("active"), $toks ); 
        $freq = $this->gibberish->decrypt( $this->input->post("hol_freq"), $toks ); 
        $campus =  $this->gibberish->decrypt( $this->input->post("campus"), $toks ); 
        $Ptype = $this->gibberish->decrypt( $this->input->post("payment"), $toks ); 
        $Ttype =$this->gibberish->decrypt( $this->input->post("teaching"), $toks ); 
        $this->session->set_userdata("cs_list", $this->gibberish->decrypt( $this->input->post("code_status"), $toks ));
        // $response = array();
        // $response["code"] = $code;
        // $response["desc"] = $description;
        // $response["type"] = $type;
        // $response["active"] = $active;
        // $response["frequency"] = $freq;


        // get the check boxes selected
        $this->db->query("CALL prc_code_holidays_set('{$holiday_id}', ".$this->db->escape($code).", ".$this->db->escape($description)." ,'{$type}','{$active}','{$freq}','{$campus}','{$Ptype}','{$Ttype}');");
        
        // end of saving

        // $response["departments"] = $deptsaffected;
        // print(json_encode($response));
        // $permanent      = $this->input->post("permanent");
        // $prob           = $this->input->post("prob");
        // $contractual    = $this->input->post("contractual");


        // $this->extras->setHolidayAffectedDepartments($dayid, $deptsaffected,$permanent,$prob,$contractual);
       }
    }

    public function saveAffectedDepartment(){
      $toks = $this->input->post("toks");
      $code_status = $this->session->userdata("cs_list");
      $deptsaffected  = $this->input->post("deptsaffected");
      $res1 = $this->db->query("SELECT holiday_id from code_holidays order by managed_on desc limit 1");
        $dayid = $res1->row()->holiday_id;
        $this->db->query("DELETE FROM holiday_inclusions WHERE holi_cal_id={$dayid}");
        // saving for holiday inclusions
        // author : justin (with e)
        foreach ($deptsaffected as $dept) {
            $i = 0;
            $status_included = "";
            foreach ((explode(",", $code_status)) as $cs_val) {
                $val = $dept."~".$cs_val;
                // echo "<pre>";print_r(count($this->input->post($cs_val)));
                if (count($this->input->post($cs_val)) > 0) {
                  if($this->input->post($cs_val)){
                    foreach ($this->input->post($cs_val) as $k_val){

                        if($k_val == $val){
                            if($i == 0){
                                $status_included = $k_val;
                                $i +=1;
                            }else{
                                $status_included = $status_included.", ". $k_val;
                                $i +=1;
                            }
                        }
                    }
                  }
                }
            }

            // save here
            $this->extras->saveHolidayInclusion($dayid,$dept,$status_included);

        }
    }
    
    
    public function addnewholidaycalendar(){
      // var_dump($data); die;
       $toks = $this->input->post("toks");
       $data['job'] = $this->gibberish->decrypt( $this->input->post("job"), $toks );
       $data['holiday_c'] = $this->gibberish->decrypt( $this->input->post("holiday_c"), $toks );
       $data['hcalendar_id'] = $this->gibberish->decrypt( $this->input->post("hcalendar_id"), $toks );
       $data['start'] = $this->gibberish->decrypt( $this->input->post("start"), $toks );
       $data['end'] = $this->gibberish->decrypt( $this->input->post("end"), $toks );
       $this->load->view('maintenance/addnewholidaycalendar', $data);     
    }
    
    public function save_holiday_calendar(){
       $toks = $this->input->post("toks");
       $job = $this->gibberish->decrypt( $this->input->post("job"), $toks );
       $holiday_c = $this->gibberish->decrypt( $this->input->post("holiday_c"), $toks );
       $hcalendar_id = $this->gibberish->decrypt( $this->input->post("hcalendar_id"), $toks );
       $halfday = $this->gibberish->decrypt( $this->input->post("halfday"), $toks );
       $fromtime = $this->gibberish->decrypt( $this->input->post("fromtime"), $toks );
       $totime = $this->gibberish->decrypt( $this->input->post("totime"), $toks );
       $sched_count = $this->gibberish->decrypt( $this->input->post("sched_count"), $toks );
       $msg = '';

       if($job=="delete"){
        $this->db->query("delete from code_holiday_calendar WHERE id='{$hcalendar_id}'");
        $msg = "Holiday has been deleted successfully!";
       }else{ 
        $id = $this->gibberish->decrypt( $this->input->post("mh_cal"), $toks );
        $from = date("Y-m-d",strtotime($this->gibberish->decrypt( $this->input->post("dfrom"), $toks )));
        $to = date("Y-m-d",strtotime($this->gibberish->decrypt( $this->input->post("dto"), $toks )));
        if($job == "new"){
          $this->db->query("INSERT INTO code_holiday_calendar (date_from,date_to,holiday_id,halfday,fromtime,totime,sched_count) VALUES ('$from','$to','$id','$halfday','$fromtime','$totime','$sched_count')");
          $msg = "Holiday has been saved successfully";
        }else{
          $this->db->query("UPDATE code_holiday_calendar set date_from = '$from', date_to = '$to', holiday_id = '$id', halfday = '$halfday', fromtime='$fromtime', totime='$totime', sched_count='$sched_count' WHERE id ='$hcalendar_id'");
          $msg = "Holiday has been updated successfully";
        }
          
       }
       echo $msg;     
    }
    
    function holidaycalendarlist(){
      $sql = $this->db->query("SELECT id,DATE_FORMAT(date_from,'%M %d, %Y') AS datefrom, DATE_FORMAT(date_to,'%M %d, %Y') AS dateto,(SELECT b.hdescription FROM code_holidays b WHERE b.holiday_id=a.holiday_id) AS Event,a.holiday_id FROM code_holiday_calendar a")->result();
      $events = array();
      foreach($sql as $row){
         $eventArray['id'] = $row->id != ""?$row->id:"";
         $eventArray['holiday_id'] = $row->holiday_id != ""?$row->holiday_id:"";
         $eventArray['title'] =  $row->Event != ""?$row->Event:"";
         $eventArray['start'] = $row->datefrom != ""?$row->datefrom:"";
         $eventArray['end'] = $row->dateto != "" ?$row->dateto:"";
         $events[] = $eventArray;
      }
      
      echo json_encode($events);
    }


    public function forceLogout(){
      $toks =  $this->input->post("toks");
      $online_id = $toks ? $this->gibberish->decrypt( $this->input->post("online_id"), $toks ) : $this->input->post('online_id');
      $username = '';
      $username_q = $this->db->query("SELECT username from user_gate_history Where id='$online_id'");
      if($username_q->num_rows() > 0) $username = $username_q->row(0)->username;

      $res = '';
      if($username){
          $this->db->query($this->db->update_string("user_gate_history", 
                            array("logout"=>date('Y-m-d H:i:s'),"logout_by"=>$this->session->userdata('username')), 
                            array("id"=>$online_id, "logout"=>"0000-00-00 00:00:00")));

          $this->db->query($this->db->update_string("user_gate_history", 
                            array("logout"=>date('Y-m-d H:i:s'),"logout_by"=>$this->session->userdata('username')), 
                            array("username"=>$username, "logout"=>"0000-00-00 00:00:00")));

          $res = $this->db->query("DELETE FROM ci_sessions WHERE username = '$username' ");
      }

      if($res) echo 1;
      else echo 0;

    }
	
	  public function addnewstudschedule(){
       $data['job'] = $this->input->post("job");
       $data['schedid'] = $this->input->post("schedid");       
       $data['isedit'] = $this->input->post("isedit");
       $this->load->view('maintenance/add_student_schedule', $data);     
    } 

    /*
    * add update delete for dependent
    * author : justin (with e)
    */
    public function newDependent(){
        $this->load->view('maintenance/dependent_details');
    }

    public function updateDependent(){
        $this->load->view('maintenance/dependent_modify');
    }
    public function displayedTaxExemption(){
      $res = '';
      $toks = $this->input->post('toks');
      $dep_code = $this->gibberish->decrypt($this->input->post('code'), $toks);
      $sql  = $this->db->query("SELECT * FROM code_tax_status WHERE status_code='{$dep_code}'"); 
      $res = $sql->row()->status_exemption;
      if($res == "") $res = 0;
      echo $res;
    }
    /*
    * end of add update delete for dependent
    */
    /*
    * Title : For saving dependent setup
    * Author : justin (with e)
    * Date : 08-25-2017
    */
    public function saveDependentSetup(){
      $result = "";
      $continue = true;
      $toks = $this->input->post("toks");
      $data['job'] = $this->gibberish->decrypt($this->input->post("job"), $toks);
      $data['dep_code'] = $this->gibberish->decrypt($this->input->post("dep_code"), $toks);
      $data['stat_name'] = $this->gibberish->decrypt($this->input->post("stat_name"), $toks);
      $data['tax_exc'] = $this->gibberish->decrypt($this->input->post("tax_exc"), $toks);
      
      // validation for new dependent 
      if($data['job'] == "saveNewDependent"){
          $exist_code = $this->db->query("SELECT * FROM code_tax_status WHERE status_code='".$data['dep_code']."'")->result();
          if(count($exist_code) > 0){
            $result = "Error saving!. Your Dependent Code was already exist.";
            $continue = false;
          }
      }
      // end of validation

      // save here
      if($continue === true && $data['job'] != "deleteDependent"){
        $data['dep_code'] = strtoupper($this->gibberish->decrypt($this->input->post("dep_code"), $toks));
        $data['stat_name'] = strtoupper($this->gibberish->decrypt($this->input->post("stat_name"), $toks));

        $result = $this->extras->saveDependent($data);
      }else{
        // delete here
        $result = $this->extras->deleteDependent($data['dep_code']);
      }
      // end of saving

      echo $result; 
    }

    public function deleteLeaveAppDate(){
        $data = $this->input->post();
        $toks = $this->input->post("toks");
        $return = array('status' => 0, 'msg'=> "Error!", "data" => "Please report to admin", "icon" => "error");
        if($toks){
            foreach ($data as $key => $value) {
                if($key != "toks") $data[$key] = urldecode($this->gibberish->decrypt( $value, $toks ));
            }
        }
        
        $del = $this->extras->deleteData("code_leave_setup", "id", $data['code']);
        $this->db->query("DELETE FROM employee_leave_credit WHERE dfrom='{$data['from']}' AND dto='{$data['to']}' AND base_id = '{$data['code']}'");
        if ($del) $return = array('status' => 1, 'msg'=> "Deleted!", "data" => "Successfully validity date.", "icon" => "success");
        
        echo json_encode($return);
    }

    public function onlineApplicationApproverSeq(){
      $this->load->model("leave");
      $app_seq = array();
      $toks = $this->input->post("toks");
      $code = $this->input->post("code");
      $code = $this->gibberish->decrypt($code, $toks);
      $ol_app = $this->leave->applicationApproverSequence($code);
      if($ol_app->num_rows() > 0){
        $app_seq = array(
          $ol_app->row()->hhseq => "HR Director",
          $ol_app->row()->dhseq => "Manager/Dean",
          $ol_app->row()->chseq => "Immediate Superior/Department Head",
          $ol_app->row()->cpseq => "",
          $ol_app->row()->dpseq => "",
          $ol_app->row()->upseq => "",
          $ol_app->row()->boseq => "",
          $ol_app->row()->fdseq => "",
          $ol_app->row()->pseq => "Payroll Head"
        );
      }
      unset($app_seq[0]);

      $data["app_seq"] = $app_seq;
      $data["code"] = $code;
      $this->load->view("maintenance/ol_approver_sequence", $data);
    }

    public function deleteOnlineApplicationCode(){
      $response = array();
      $this->load->model("leave");
      $toks = $this->input->post("toks");
      $id = $this->input->post("code");
      $id = $this->gibberish->decrypt($id, $toks);
      $app_details = $this->leave->onlineApplicationDetails($id);
      if($app_details->num_rows() > 0){
        $code_request = $app_details->row()->code_request;
        $is_used = $this->leave->isApplicationCodeUsed($code_request);
        if($is_used > 0){
          $response["msg"] = "Unable to delete. Code already in used.";
          $response["err"] = 1;
        }else{
          $res = $this->leave->deleteOnlineApplicationCode($id);
          $response["msg"] = "Successfully deleted.";
          $response["err"] = 0;
        }
      }

      echo json_encode($response);
    }

    /*
    * End of saving dependent setup
    */
}
/* End of file maintenance_.php */
/* Location: ./application/controllers/maintenance_.php */