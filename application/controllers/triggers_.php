<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Triggers_ extends CI_Controller{

	function __construct(){
		parent::__construct();
		$this->load->model("triggers");
	}

/*	public function saveAttendanceDetailed(){
		$current_date = $this->extensions->getServerTime();
		$current_date = date("Y-m-d", strtotime($current_date));
		$emplist = $this->extensions->getActiveEmployees();
		foreach($emplist as $emplists){
			$teachingtype = $this->extensions->getEmployeeTeachingType($emplists["employeeid"]);
			if($teachingtype){
				if($teachingtype == "teaching"){
					$isBED = false;
                    $deptid = $this->extensions->getEmployeeDeptid($emplists["employeeid"]);
                    $bed_depts = $this->extensions->getBEDDepartments();
                    if(in_array($deptid, $bed_depts)) $isBED = true;
					$att_data = $this->attendance->computeEmployeeAttendanceSummaryTeaching($current_date,$current_date,$emplists["employeeid"],"",$isBED);
					if(isset($att_data[19]) && $att_data[19]) $this->attendance->saveAttendanceSummaryPerDay($att_data[19], $emplists["employeeid"], $teachingtype);
				}else{
					$att_data = $this->attendance->computeEmployeeAttendanceSummaryNonTeaching($current_date,$current_date,$emplists["employeeid"]);
					if(isset($att_data[17]) && $att_data[17]) $this->attendance->saveAttendanceSummaryPerDay($att_data[17], $emplists["employeeid"], $teachingtype);
				}
			}
		}
	}*/

    public function getEmployeeListToConfirm(){
        $current_cutoff = $this->getCurrentCutoff();   
    }

    public function getCurrentCutoff(){
        $emp_email = "";
        $confirmed_employees = $active_employees = $other_data = array();
        if(Globals::is_connect_internet()){
            $date_now = $this->extensions->getServerTime();
            $date_now = date("Y-m-d", strtotime($date_now));
            list($cutoffstart, $cutoffend) = $this->extensions->getCurrentCutoff($date_now);
            $teaching_confirmed_list = $this->attendance->emp_confirmed($cutoffstart, $cutoffend, "teaching");
            $nonteaching_confirmed_list = $this->attendance->emp_confirmed_nt($cutoffstart, $cutoffend, "nonteaching");
            $confirmed_list = array_merge($teaching_confirmed_list, $nonteaching_confirmed_list);
            foreach($confirmed_list as $att_info) $confirmed_employees[$att_info["qEmpId"]] = $att_info["qEmpId"];
            $active_list = $this->setup->getActiveEmployees();
            foreach($active_list as $emp_info) $active_employees[$emp_info["employeeid"]] = $emp_info["employeeid"];
            $not_confirmed_list = array_diff_assoc($active_employees, $confirmed_employees);
            foreach($not_confirmed_list as $employeeid){
                $other_data["employeeid"] = $employeeid;
                $this->load->model("email");
                $emp_email = $this->extensions->getEmployeeEmail($employeeid);
                $other_data["cutoffstart"] = $cutoffstart;
                $other_data["cutoffend"] = $cutoffend;
                $this->email->sendEmailForAttendanceConfirmation($emp_email, $other_data);
            }
            echo json_encode(array("status"=>"1", "msg"=>"Successfully notify employee for their attendance."));
        }else{
            echo json_encode(array("status"=>"0", "msg"=>"Failed to notify employee. Device not connected in internet."));
        }
    }

}
