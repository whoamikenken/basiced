<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Retirement_ extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->model("retirement");
    }

    public function index()
    {
        # do nothing
    }

    function loadEmployeeRetiree(){
        $toks = $this->input->post("toks");
        $department = $toks ? $this->gibberish->decrypt( $this->input->post("department"), $toks ) : $this->input->post("department");
        $office = $toks ? $this->gibberish->decrypt( $this->input->post("office"), $toks ) : $this->input->post("office");
        $status = $toks ? $this->gibberish->decrypt( $this->input->post("status"), $toks ) : $this->input->post("status");
        $month = $toks ? $this->gibberish->decrypt( $this->input->post("month"), $toks ) : $this->input->post("month");
        $data['tblData'] = $this->retirement->employeeRetiree($department, $office, $month,$status)->result_array();
        $this->load->view("config/pre_retirement", $data);
    }

    function saveRetirement(){
        $toks = $this->input->post("toks");
        $employeeid = $toks ? $this->gibberish->decrypt( $this->input->post("employeeid"), $toks ) : $this->input->post("employeeid");
        $dateresigned = $toks ? $this->gibberish->decrypt( $this->input->post("dateresigned"), $toks ) : $this->input->post("dateresigned");
        $resigned_reason = $toks ? $this->gibberish->decrypt( $this->input->post("resigned_reason"), $toks ) : $this->input->post("resigned_reason");
        $updateRetirement = $this->retirement->saveRetirement($employeeid, $dateresigned, $resigned_reason);
        if($updateRetirement) echo $this->retirement->employeeRetiree('','','','1')->num_rows();
        
    }




}

/* End of file approval_.php */
