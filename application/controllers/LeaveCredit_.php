<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LeaveCredit_ extends CI_Controller{

	public function index(){
	
	
	}


	function recountPastPerYear(){

		$yearRecount = array("2019","2020", "2021","2022");
		// 06042
		echo "Processing.... Please wait....";
		$count = 0;
		foreach ($yearRecount as $rowYear => $yearsCurrent) {
			$getEmployedData = $this->extras->getEmployeeDataRecountLeavePastYear();
			foreach ($getEmployedData as $key => $value) { 
				$todayDate = $yearsCurrent.substr($value->dateemployed, 4);
				$today = new DateTime($todayDate);

				$dateEmployed = new DateTime($value->dateemployed);

	        	//Get Years of service
				$diff = $dateEmployed->diff($today);
				$YearService = $diff->y;


	        	//Get Months
				$Months = $dateEmployed->diff($today); 
				$MonthsService = (($Months->y) * 12) + ($Months->m);

				$isNewFulltime = false;

				if (substr($value->dateemployed, 8) == substr($todayDate, 8)) {
					if ($MonthsService == 8) {
						$isNewFulltime = true;
					}
				}

				if ($todayDate > date('Y-m-d')) {
					continue;
				}
				// echo "<pre>";print_r($date->format('Y-m-d'));die;
	        	//Added New condition for 18 months
				if ($MonthsService >= 9 || $isNewFulltime == true) {

	          	//For other leave code but now fix for VL 
					$leave_credit = $this->extras->getLeaveCredit($value->employeeid);
	          	//Get Data From Setup
					$leaveSetup = $this->extras->leaveCreditSetupData("VL", $YearService, $value->teachingtype, $value->employmentstat);
					$leaveSetupData = explode("/", $leaveSetup);

	          	//Moved previos credit to history and delete
					foreach ($leave_credit as $row => $data) {
						if ($leaveSetup != "NoSetup/") {
							$this->extras->deleteLeaveData($data->id);
							$leaveData = array();
							$leaveData["employeeid"] = $data->employeeid;
							$leaveData["leavetype"] = $data->leavetype;
							$leaveData["balance"] = $data->balance;
							$leaveData["credit"] = $data->credit;
							$leaveData["avail"] = $data->avail;
							$leaveData["dfrom"] = $data->dfrom;
							$leaveData["dto"] = $data->dto;
							$leaveData["user"] = "Recompute".$yearsCurrent;
							$leaveData["employmentStat"] = $value->employmentstat;
							$this->extras->saveLeaveToHistory($leaveData);
						}
					}

	          	//Add new credit base on setup
					if ($leaveSetup != "NoSetup/") {
						$count++;
						$date = new DateTime(substr($todayDate, 0, 4).$value->startDate);

						// $isleap = "false";
						$date->modify('+1 year');

						// $begin = new DateTime(substr($todayDate, 0, 4).$value->startDate);
						// $end = new DateTime($date->format('Y-m-d'));

						// $interval = DateInterval::createFromDateString('1 day');
						// $period = new DatePeriod($begin, $interval, $end);
						// foreach ( $period as $dt ) {
						//     if(($dt->format('m') === '02') && ($dt->format('d') === '29')) {
						//         $isleap = "true";
						//     }
						// }
						// echo "<pre>";print_r(substr($todayDate, 0, 4).$value->startDate."|".$date->format('Y-m-d')." - ".$isleap);
						// if ($isleap == "true") {
						// 	$date->modify('-1 day');
						// }else{
						// 	$date->modify('-1 day');
						// }

						$date->modify('-1 day');
						
						// echo "<pre>";print_r(substr($todayDate, 0, 4).$value->startDate);die;
						$checkAvailedLeave = $this->extras->recountAvailedLeave($value->employeeid, "VL", substr($todayDate, 0, 4).$value->startDate, $date->format('Y-m-d'));
						// echo "<pre>";print_r($this->db->last_query());
						$newBalance = $leaveSetupData[0] - $checkAvailedLeave;
	          	// echo"<pre>";print_r($leaveSetupData[0]);die;
						$newleaveData["avail"] = ($checkAvailedLeave != 0)? $checkAvailedLeave:"0.00";
						$newleaveData["balance"] = $newBalance;
						$newleaveData["credit"] = $leaveSetupData[0];
						$newleaveData["leavetype"] = "VL";
						$newleaveData["balanceType"] = $leaveSetupData[1];
						$newleaveData["creditType"] = $leaveSetupData[1];
						$newleaveData["availType"] = $leaveSetupData[1];
						$newleaveData["employeeid"] = $value->employeeid;
						$newleaveData["user"] = "Recompute";
						$newleaveData["dfrom"] = substr($todayDate, 0, 4).$value->startDate;
						$newleaveData["employmentStat"] = $value->employmentstat;
						$newleaveData["dto"] = $date->format('Y-m-d');
						$this->extras->insertLeaveData($newleaveData);
					}
				}
			}
		}

		echo "Finished process. ".$count." is done";
	}
}
