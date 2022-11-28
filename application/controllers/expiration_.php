<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expiration_ extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->model("expiration");
    }

    public function index(){
        # do nothing
    }

    function expiryPRC(){
        $data["today"] = date('Y-m-d', strtotime($this->extensions->getServerTime()));
        $data['prcExpiryData'] = $this->expiration->prcExpiryData()->result_array();
        $this->load->view("expiration/prc_expiration_data", $data);
    }




}
/* End of file expiration_.php */
