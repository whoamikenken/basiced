<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inout extends CI_Controller {

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
	public function index()
	{
        /**
        if(!$this->islogged()){            
            $this->loadloginform();
            //echo $this->session->userdata("currentpage");
        }else{
            */
            /**
              $data['title'] = 'Main';
              $data['content'] = 'main';
              $data['rootid'] = '';
              $data['menuid_selected'] = '';
              $data['autoload'] = '';
              $data['macadd'] = $this->input->get("mid");
              $this->user->loaduserdata($data);
              $this->load->view('inout/template', $data);
              */
             redirect("gate"); 
              # echo ;
        /** } */
	}
    public function islogged(){
        return $this->session->userdata("logged_in");
    }
    function loadloginform(){
        $data['title'] = 'PAYROLL';
        $data['content'] = 'login';
        $data['rootid'] = '';
        $data['menuid_selected'] = '';
        $data['autoload'] = 'autofocusinput()';
		$this->load->view('includes/template', $data);
    }
    function dom_(){
        $data['title'] = 'BANDI';
        $data['content'] = '';
        $data['rootid'] = '';
        $data['menuid_selected'] = '';
        $data['macadd'] = $this->input->post("macadd");
        $data['autoload'] = 'autofocusinput()';
        $this->load->model("timesheet");
		$this->load->view('inout/dom', $data);
    }
    function imageview(){
        $data = "";
        $id = $this->input->get("code");
        # $tablename = $this->input->get("it")=="s"?"signature_upload":($this->input->get("it")=="b"?"background_upload":"picture_upload");
        $tablename = "picture_upload";
        # echo $id;
        $q = $this->db->query("select * from $tablename where (title='$id' OR name='$id')")->result_array();
        if (count($q)>0) {
            $row = $q[0];
            $ContentType = $row['type'];
    		$data = $row['description'];
            header("Content-type: $ContentType");
            print $data;
        }else{
            $q = $this->db->query("select * from $tablename where title=''")->result_array();
            if (count($q)>0) {
                $row = $q[0];
                $ContentType = $row['type'];
        		$data = $row['description'];
                header("Content-type: $ContentType");
                print $data;
            }
        } 
    }
    /**
    function imageview(){
        $data = "";
        $id = $this->input->get("code");
        # echo $id;
        $q = $this->db->query("select * from pictures where code='$id'")->result_array();
        if (count($q)>0) {
            $row = $q[0];
            $ContentType = $row['contenttype'];
    		$data = $row['filedata'];
            header("Content-type: $ContentType");
            print $data;
        }else{
            $q = $this->db->query("select * from pictures where code=''")->result_array();
            if (count($q)>0) {
                $row = $q[0];
                $ContentType = $row['contenttype'];
        		$data = $row['filedata'];
                header("Content-type: $ContentType");
                print $data;
            }
        } 
    }
    */
    function sessionholder(){
        $military_time = false;
        $this->session->set_userdata("timekeeping",date("Ymdhis"));
        //echo date("h:i:s A"); /** MILITARY TIME IS OFF */
        echo date("H:i:s"); /** MILITARY TIME IS ON */
        $date_display = date("F d, Y")."<br>".date("l");
        $time_display = $military_time ? date("H:i:s") : date("h:i:s A");
        $macadd = $this->input->post("macadd"); 
        $this->db->query("update machine_setup set mac_status='ACTIVE' WHERE mac_add='{$macadd}'");
        
        echo "<user>
                <datedisplay>{$date_display}</datedisplay>
                <timedisplay>{$time_display}</timedisplay>
              </user>";
    }
    function logme(){
        //$this->se;
        
        $seconds_delay = -100;
        $result = 3;
        $fullname = "";
        $logtype = $this->input->post("ltype");
        $macadd = $this->input->post("macadd");
        $this->load->model("timesheet");
        list($mstat,$message,$description) = $this->timesheet->machinedisplaystatus($macadd);
        $mstat=1;
        /** Check first if the machine is registered */
          $result = "";
          $fullname = "";
          $mess_set = "";
          $time_left = "";
          if($mstat!=""){
              $res = $this->timesheet->logmenow($this->input->post("uid"),$logtype,$seconds_delay,$macadd);
              $result = $res->RESULT_NUM;
              $fullname = trim($res->FULLNAME_SET)?$res->FULLNAME_SET:"Not set";
              $mess_set = $res->MESSAGE_SET;
              $time_left = $res->TIME_LEFT;
          }
          /**
        if($mstat!=""){
              $res = $this->timesheet->logmenow($this->input->post("uid"),$logtype,$seconds_delay);
              $result = $res->RESULT_NUM;
              $fullname = $res->FULLNAME_SET;
              $mess_set = $res->MESSAGE_SET;
              $time_left = $res->TIME_LEFT;
              
              if($result){
                  $message = $mess_set=="IN" ? "You have been successfully timed IN." : ($mess_set=="OUT" ? "You have been successfully timed OUT." : $mess_set);
                  switch($result){
                     case 1:
                        $message = "Please wait for {$time_left} to LOG OUT.";           
                     break;
                     case 2:
                        $message = "Please wait for {$time_left} before LOGGING IN.";           
                     break;
                     case 3:
                        $message = "Sorry, this person is not registered.";           
                     break;
                     case 4:
                        $message = "Sorry, this machine is for LOGGING IN only.";           
                     break;
                     case 5:
                        $message = "Sorry, this machine is for LOGGING OUT only.";           
                     break;
                     case 6:
                        $message = "Please LOG OUT first.";           
                     break;
                     case 7:
                        $message = "Please LOG IN first.";           
                     break;
                  }
                }
              
          }
          echo "<user>
                  <status>{$result}</status>
                  <message>{$message}</message>
                  <fullname>{$fullname}</fullname>
                </user>";
                */
          echo "<user>
                  <status>{$result}</status>
                  <message>{$message}</message>
                  <fullname>{$fullname}</fullname>
                </user>";      
    }
    function triggerdisplay(){
        $valid_cardnumber = 10;
        $result = "";
        $message = "";
        $fullname = "";
        $macadd = $this->input->post("macadd");
        $res = $this->db->query("select * from timesheet_monitoring where device='$macadd' order by timeinserted LIMIT 1");
        if($res->num_rows()>0){
          $mrow = $res->row(0);  
          $result = $mrow->result;
          $mess_set = $mrow->message;
          $time_left = $mrow->timesec;
          $fullname = $mrow->fullname;
          $userid = $mrow->userid;
          # if($valid_cardnumber!=strlen($userid)) $result = 8;
          $deviceid = $mrow->device;
          $message = $mess_set=="IN" ? "You have been successfully timed IN." : ($mess_set=="OUT" ? "You have been successfully timed OUT." : $mess_set);
          switch($result){
             case 1:
                $message = "Please wait for {$time_left} to LOG OUT.";           
             break;
             case 2:
                $message = "Please wait for {$time_left} before LOGGING IN.";           
             break;
             case 3:
                $message = "Sorry, this person is not registered.";           
             break;
             case 4:
                $message = "Sorry, this machine is for LOGGING IN only.";           
             break;
             case 5:
                $message = "Sorry, this machine is for LOGGING OUT only.";           
             break;
             case 6:
                $message = "Please LOG OUT first.";           
             break;
             case 7:
                $message = "Please LOG IN first.";           
             break;
             case 8:
                $message = "Please try again.";           
             break;
          }
          $this->db->query("delete from timesheet_monitoring where userid='{$userid}' and device='{$deviceid}'");
        }
        echo "<user>
                  <status>{$result}</status>
                  <userid>{$userid}</userid>
                  <message>{$message}</message>
                  <fullname>{$fullname}</fullname>
                </user>";
    }
    function loglist_display(){
        $data['title'] = 'LIST';
        $data['limits'] = $this->input->post("limits");
        $this->load->model("timesheet");
		$this->load->view('inout/loglist', $data);
    }
}

/* End of file inout.php */
/* Location: ./application/controllers/inout.php */