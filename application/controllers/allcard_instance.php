<?php
ini_set('max_execution_time', 0);
ini_set("memory_limit", "2G");


if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Allcard_instance extends CI_Controller{

	public function index(){
		$empinfo = array();
		$query = $this->db->query("SELECT * FROM employee WHERE employeeid = '1983-06-001' ");
		foreach($query->result_array() as $employee){
			$empinfo = array(
                "PersonType" => "E",
                "IDNumber"  => $employee['employeeid'],
                "FirstName" => ($employee['fname']) ? $employee['fname'] : "PTI Default Name",
                "MiddleName" => ($employee['mname']) ? $employee['mname'] : "PTI Default Name",
                "LastName" => ($employee['lname']) ? $employee['lname'] : "PTI Default Name",
                "CampusName" => "Pinnacle Technologies Inc.",
                "DepartmentName" => ($employee['deptid']) ? $this->extensions->getDeparmentDescriptionReport($employee['deptid']) : " ",
                "PositionName" => ($employee['positionid']) ? $this->extensions->getPositionDescription($employee['positionid']) : " "
            );

			$this->employee->saveALLCARDLogs($empinfo, false);
			$api_url = Globals::apiUrl()."/api/person/editemployee";
			$logs_data = $empinfo;
			$empinfo = json_encode($empinfo);
			var_dump($empinfo);die;
			$token = $this->extensions->getPostmanToken();
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

			echo "<pre>"; print_r($response);
			echo "<pre>"; print_r($err);

			/*save to hyperion logs*/
			if($response){
				$this->employee->saveALLCARDLogs($logs_data);
			}
		}

	}

}