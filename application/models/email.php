<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* @author Max Consul
* @copyright 2019
*/

class Email extends CI_Model {

    function __construct(){
        parent::__construct();
    }

    /*for changing of password*/
    function sendEmail($emp_email, $temp_pass){
        // Set SMTP Configuration
        $setting = $this->db->query("SELECT * FROM email ORDER BY date_created DESC LIMIT 1")->result_array();
        $toks = $setting[0]['toks'];
        $setting[0]['password'] = $this->gibberish->decrypt($setting[0]['password'], $toks); 
        $emailConfig = array(
            'protocol' => 'smtp', 
            'smtp_host' => 'ssl://smtp.googlemail.com', 
            'smtp_port' => 465, 
            'smtp_user' => $setting[0]['from_email'], 
            'smtp_pass' => $setting[0]['password'], 
            'mailtype' => 'html', 
            'wordrap' => TRUE,
            'charset' => 'iso-8859-1'
        );
        // Set your email information
        $from = array(
            'email' => $setting[0]['from_email'],
            'name' => $setting[0]['from_name']
        );

        $data['temp_pass'] = $temp_pass;
        $emp_email = strtolower($emp_email);
        $to = array($emp_email);
        $subject = 'Your gmail subject here';
        // $message = 'Type your gmail message here'; // use this line to send text email.
        // load view file called "welcome_message" in to a $message variable as a html string.
        $message =  $this->load->view('recovery_email',$data,true); 
        // Load CodeIgniter Email library
        $this->load->library('email', $emailConfig);
        // Sometimes you have to set the new line character for better result
        $this->email->set_newline("\r\n");
        // Set email preferences
        $this->email->from($from['email'], $from['name']);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        // Ready to send email and check whether the email was successfully sent
        if (!$this->email->send()) {
            // Raise error message
            // show_error($this->email->print_debugger());
        } else {
            // Show success notification or other things here
            // echo 'Success to send email';
        }

    }

    /*for leave/ob/correction/schedule*/
    function sendEmailForOnlineApplication($emp_email, $other_data){
        $setting = $this->db->query("SELECT * FROM email ORDER BY date_created DESC LIMIT 1")->result_array();
        $toks = $setting[0]['toks'];
        $setting[0]['password'] = $this->gibberish->decrypt($setting[0]['password'], $toks); 
        // Set SMTP Configuration
        $emailConfig = array(
            'protocol' => 'smtp', 
            'smtp_host' => 'ssl://smtp.googlemail.com', 
            'smtp_port' => 465, 
            'smtp_user' => $setting[0]['from_email'], 
            'smtp_pass' => $setting[0]['password'], 
            'mailtype' => 'html', 
            'wordrap' => TRUE,
            'charset' => 'iso-8859-1'
        );
        // Set your email information
        $from = array(
            'email' => $setting[0]['from_email'],
            'name' => $setting[0]['from_name']
        );

        $emp_email = strtolower($emp_email);
        $to = array($emp_email);
        $subject = 'Your gmail subject here';
        // $message = 'Type your gmail message here'; // use this line to send text email.
        $message =  $this->load->view('online_application',$other_data,true); 
        // Load CodeIgniter Email library
        $this->load->library('email', $emailConfig);
        // Sometimes you have to set the new line character for better result
        $this->email->set_newline("\r\n");
        // Set email preferences
        $this->email->from($from['email'], $from['name']);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        // Ready to send email and check whether the email was successfully sent
/*        if (!$this->email->send()) {
            // Raise error message
            show_error($this->email->print_debugger());
        } else {
            // Show success notification or other things here
            echo 'Success to send email';
        }*/

    }

    /*for changing of password*/
    function sendForgotPass($message, $emp_email){
        // Set SMTP Configuration
        $setting = $this->db->query("SELECT * FROM email ORDER BY date_created DESC LIMIT 1")->result_array();
        $toks = $setting[0]['toks'];
        $setting[0]['password'] = $this->gibberish->decrypt($setting[0]['password'], $toks); 
        $emailConfig = array(
            'protocol' => 'smtp', 
            'smtp_host' => 'ssl://smtp.googlemail.com', 
            'smtp_port' => 465, 
            'smtp_user' => $setting[0]['from_email'], 
            'smtp_pass' => $setting[0]['password'], 
            'mailtype' => 'html', 
            'wordrap' => TRUE,
            'charset' => 'iso-8859-1'
        );

        // Set your email information
        $from = array(
            'email' => $setting[0]['from_email'],
            'name' => $setting[0]['from_name']
        );

        $emp_email = strtolower($emp_email);
        $to = array($emp_email);
        $subject = 'Reset Password';
        // Load CodeIgniter Email library
        $this->load->library('email', $emailConfig);
        // Sometimes you have to set the new line character for better result
        $this->email->set_newline("\r\n");
        // Set email preferences
        $this->email->from($from['email'], $from['name']);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        // Ready to send email and check whether the email was successfully sent
        if (!$this->email->send()) {
            // Raise error message
            show_error($this->email->print_debugger());
        } else {
            // Show success notification or other things here
            // echo 'Success to send email';
        }
    }

    /*for changing of password*/
    function sendLockAccountEmail($message, $emp_email){
        // Set SMTP Configuration
        $setting = $this->db->query("SELECT * FROM email ORDER BY date_created DESC LIMIT 1")->result_array();
        $toks = $setting[0]['toks'];
        $setting[0]['password'] = $this->gibberish->decrypt($setting[0]['password'], $toks); 
        $emailConfig = array(
            'protocol' => 'smtp', 
            'smtp_host' => 'ssl://smtp.googlemail.com', 
            'smtp_port' => 465, 
            'smtp_user' => $setting[0]['from_email'], 
            'smtp_pass' => $setting[0]['password'], 
            'mailtype' => 'html', 
            'wordrap' => TRUE,
            'charset' => 'iso-8859-1'
        );

        // Set your email information
        $from = array(
            'email' => $setting[0]['from_email'],
            'name' => $setting[0]['from_name']
        );

        $emp_email = strtolower($emp_email);
        $to = array($emp_email);
        $subject = 'Locked Account';
        // Load CodeIgniter Email library
        $this->load->library('email', $emailConfig);
        // Sometimes you have to set the new line character for better result
        $this->email->set_newline("\r\n");
        // Set email preferences
        $this->email->from($from['email'], $from['name']);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        // Ready to send email and check whether the email was successfully sent
        if (!$this->email->send()) {
            // Raise error message
            show_error($this->email->print_debugger());
        } else {
            // Show success notification or other things here
            return 'success';
        }

    }

    function sendEmailFacialDowntime($message, $emp_email, $subject){

        // Set SMTP Configuration
        $setting = $this->db->query("SELECT * FROM email ORDER BY date_created DESC LIMIT 1")->result_array();
        $toks = $setting[0]['toks'];
        $setting[0]['password'] = $this->gibberish->decrypt($setting[0]['password'], $toks); 
        $emailConfig = array(
            'protocol' => 'smtp', 
            'smtp_host' => 'ssl://smtp.googlemail.com', 
            'smtp_port' => 465, 
            'smtp_user' => $setting[0]['from_email'], 
            'smtp_pass' => $setting[0]['password'], 
            'mailtype' => 'html', 
            'wordrap' => TRUE,
            'charset' => 'iso-8859-1'
        );

        // Set your email information
        $from = array(
            'email' => $setting[0]['from_email'],
            'name' => $setting[0]['from_name']
        );

        $emp_email = strtolower($emp_email);
        $to = $emp_email;
        $subject = $subject;
        // Load CodeIgniter Email library
        $this->load->library('email', $emailConfig);
        // Sometimes you have to set the new line character for better result
        $this->email->set_newline("\r\n");
        // Set email preferences
        $this->email->from($from['email'], $from['name']);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        // Ready to send email and check whether the email was successfully sent
        if (!$this->email->send()) {
            // Raise error message
            show_error($this->email->print_debugger());
        } else {
            // Show success notification or other things here
            return 'success';
        }

    }

    /*for leave/ob/correction/schedule*/
    function sendEmailForAttendanceConfirmation($emp_email, $other_data){
        // Set SMTP Configuration
        $setting = $this->db->query("SELECT * FROM email ORDER BY date_created DESC LIMIT 1")->result_array();
        $toks = $setting[0]['toks'];
        $setting[0]['password'] = $this->gibberish->decrypt($setting[0]['password'], $toks); 
        $emailConfig = array(
            'protocol' => 'smtp', 
            'smtp_host' => 'ssl://smtp.googlemail.com', 
            'smtp_port' => 465, 
            'smtp_user' => $setting[0]['from_email'], 
            'smtp_pass' => $setting[0]['password'], 
            'mailtype' => 'html', 
            'wordrap' => TRUE,
            'charset' => 'iso-8859-1'
        );
        // Set your email information
        $from = array(
            'email' => $setting[0]['from_email'],
            'name' => $setting[0]['from_name']
        );

        $emp_email = strtolower($emp_email);
        $to = array($emp_email);
        $subject = 'Your gmail subject here';
        // $message = 'Type your gmail message here'; // use this line to send text email.
        $message =  $this->load->view('attendance_notification',$other_data,true); 
        // Load CodeIgniter Email library
        $this->load->library('email', $emailConfig);
        // Sometimes you have to set the new line character for better result
        $this->email->set_newline("\r\n");
        // Set email preferences
        $this->email->from($from['email'], $from['name']);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);
        // Ready to send email and check whether the email was successfully sent
        if (!$this->email->send()) {
            // Raise error message
            show_error($this->email->print_debugger());
        } else {
            // Show success notification or other things here
            echo 'Success to send email';
        }

    }

}