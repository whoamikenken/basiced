<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author Justin
 * @copyright 2016
 */
 
class Email extends CI_Controller {
 
 function __construct(){
  parent::__construct();
 }

 function send_mail(){
 $this->load->helper("file");
 $name = $this->input->post("name");
 
 $ep   = explode('/',$name);
 $eid = explode('.',$ep[count($ep)-1]); 
 $emailto = $this->employee->getEmail($eid[0]);
  // Email configuration
  
  $config = Array(
     'protocol' => 'smtp',
     'smtp_host' => 'ssl://smtp.gmail.com',
     'smtp_port' => 465,                        // GMAIL's SMTP server port
     'smtp_user' => 'justin@schools.ph',  
     'smtp_pass' => 'justinpinnacle',  
     'mailtype' => 'html',
     'charset' => 'iso-8859-1',
     'wordwrap' => TRUE
  ); 
  $this->load->library('email',$config);
  $this->email->set_newline("\r\n");  
  $this->email->from('justin@schools.ph', 'Justin');
  $this->email->to($emailto);
  $this->email->subject('Subject Testing');
  $this->email->message('this is testing');
  #$this->email->attach($_SERVER['DOCUMENT_ROOT'].'/stjudedtr/pdf/sample.pdf');
  $this->email->attach($name);
    
  if($this->email->send()){
    unlink($name);
    echo "Email sent to $emailto."; 
  }else{
    echo $this->email->print_debugger(); 
  }
  
 }
 
 
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  /* send email to multiple email accts.
  $list = array('one@example.com', 'two@example.com', 'three@example.com');
  $this->email->to($list);
  // cc
  $this->email->cc("justin@schools.ph");
  */
 /*
  * send attachments
  *  $this->email->attach('/path/to/file1.png'); // attach file
  *  $this->email->attach('/path/to/file2.pdf');
  */
  /*
   $data['message'] = "Sorry Unable to send email..."; 
   if($this->email->send()){     
   $data['message'] = "Mail sent...";   
  } 
  */
}