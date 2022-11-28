<?php
/**
* @author Max Consul
* @copyright 2019
* 
* API controller
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'/libraries/REST_Controller.php');

class Aims_ extends REST_Controller{

    function __construct(){
        parent::__construct();
        $this->load->model("aims");
    }

    public function addEmployeeToAims_post(){
        ini_set('memory_limit',-1);
        set_time_limit(0);
        $success_count = $failed_count = 0;
        $empdata = file_get_contents("php://input");
        $empdata = json_decode($empdata);
        $empinfo = array(
            "employeeid" => isset($empdata->employeeid) ? $empdata->employeeid : $empdata->aims_employeeid,
            "lastname" => isset($empdata->lname) ? $empdata->lname : $empdata->aims_lname,
            "firstname" => isset($empdata->fname) ? $empdata->fname : $empdata->aims_fname,
            "middlename" => isset($empdata->mname) ? $empdata->mname : $empdata->aims_mname,
            "campus" => isset($empdata->campusid) ? $empdata->campusid : $empdata->aims_campusid,
            "teachingtype" => isset($empdata->teachingtype) ? $empdata->teachingtype : $empdata->tnt
        );
        $return = array();
        $response = $this->aims->saveEmployeeToAims($empinfo);
        if($response == 1){
            $return["msg"] = "Successfully added to aims.";
        }else if($response == 2){
            $return["msg"] = "Failed to saved employee to aims. Already exists.";
        }else{
            $return["msg"] = "Failed to saved employee to aims. Please contact admin.";
        }

        $this->response($return, 200);
        /*$curl_uri = "http://192.168.2.97/poveda/";

        $form_data = array(
            "grant_type" => "authorization_code",
            "client_id" => "9",
            "client_secret" => "LD58qpCHI4kNaOHDXeibGgPukgQ8x5GDYz0Oaun1",
            "code" => $header_data["code"]
        );
        ini_set('display_errors',1);
        error_reporting(-1);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $curl_uri); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_POST, 1 ); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $form_data); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept"=>"application/json"));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($response, true);
        $this->db->insert("aims_token", $response);*/
        /*------>*/

       /* $curl2 = curl_init();
        curl_setopt($curl2, CURLOPT_URL, $curl_uri); 
        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_POST, 1 ); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $empinfo); 
        curl_setopt($curl2, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Accept: application/json', "Authorization: Bearer ".$response["access_token"]));
        $response2 = curl_exec($curl2);
        $err = curl_error($curl2);
        curl_close($curl2);
        $response2 = json_decode($response2, true);
        $this->response($response2, 200);*/
    }

}