<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gate_old extends CI_Controller {

  public function index(){
        session_start();
        $data['title'] = 'BANDI';
        $this->session->sess_expiration = 0;
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $this->user->getLastGateUserLogin($ip_address);

        if($this->session->userdata('logged_in') && $this->session->userdata('gateaccess')){
          $user = $this->session->userdata('username');
          if(  $this->user->checkFirstLogged($user, $this->session->userdata('session_id'))  ){

            ///< this will insert to gate history if user accessed gate url with existing session already

            $sql = $this->db->query("SELECT id FROM user_gate_history
                                    WHERE username='$user'
                                    AND logout='0000-00-00 00:00:00'");

            if($sql->num_rows() == 0){
              $this->db->query($this->db->insert_string("user_gate_history",array("username"=>$user, "ip"=>$this->session->userdata('ip_address'), "login"=>date('Y-m-d H:i:s'))));
            }
            
            /*echo "<pre>"; print_r($this->session->all_userdata());
            echo $this->session->userdata('username');*/
            $this->load->view('gate/template1',$data);
          }else  $this->load->view('gatelogin',$data);
        }else{
          $this->load->view('gatelogin',$data);
        }

    
  }

  public function login(){
        $isvalidate = 1;
        $resulta = "";
        $gateaccess = $this->user->validateGateAccess(); ///< this will insert to gate history if user accessed gate url through proper login

        if($gateaccess == 0){
          $isvalidate = 0;
          $resulta = "Invalid username and password.";
        }elseif($gateaccess == 2){
          $isvalidate = 0;
          $resulta = "This user is already logged in by another device.";
        }elseif($gateaccess == 3){
          $isvalidate = 0;
          $resulta = "No gate access.";
        }elseif($gateaccess == 1){
          
        }

        echo "<user>
                <result>{$isvalidate}</result>
                <message>{$resulta}</message>
              </user>";
  }

  function loglist_display(){
      $data['title'] = 'LIST';
      $data['limits'] = $this->input->post("limits");
      $data['macadd'] = $this->input->post("macadd");
      
      $this->load->model("timesheet");
      $this->load->view('gate/loglist1', $data);
  }

  function userlog(){
      if(!$this->session->userdata('username')) show_404(); ///< prevent access to routes without session
      $this->load->model("timesheet");
      $data = $this->input->post();
      echo $this->timesheet->userlog1($data);
  }

  function dom_(){

      $ip_address = $_SERVER['REMOTE_ADDR'];
      $this->user->getLastGateUserLogin($ip_address);

      if(!$this->session->userdata('username')) show_404(); ///< prevent access to routes without session
      $data['title'] = 'BANDI';
      $this->load->model("timesheet");
      $this->load->view('gate/dom1', $data);
  }
}

/* End of file gate1.php */
/* Location: ./application/controllers/gate1.php */