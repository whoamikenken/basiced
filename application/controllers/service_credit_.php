<?php
/**
 * class referenced from overtime or change_schedule
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Service_Credit_ extends CI_Controller {
	
	function __construct(){
        parent::__construct();
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
        $this->load->model('service_credit');
    }
	
	function checkDateAvailability(){
		$date = $this->input->post("date");
		$employeeid = $this->input->post("empid");
		$return = $this->service_credit->checkDateAvailability($date,$employeeid);
		echo json_encode($return);
	}

	function getListOfAvailableSCDate(){
		$this->load->library('extras_lib');
		$this->load->model('attcompute');
		$fdate 	 	= $this->input->post('fdate');
		$tdate 	 	= $this->input->post('tdate');
		$empid 	 	= $this->input->post('empid');
		$isHalfDay 	= $this->input->post('dayMode');
		$day_list	= array();
		$arr_days	= array(
			"Monday" 	=> "M",
			"Tuesday" 	=> "T",
			"Wednesday" => "W",
			"Thursday" 	=> "TH",
			"Friday" 	=> "F",
			"Saturday" 	=> "S",
			"Sunday" 	=> "SD"
		);

		$days_arr = $this->extras_lib->getDateIncluded($fdate, $tdate);

        foreach ($days_arr as $date) {
        	$day_list[$date] = 0;
        	$dayofweek 		 = $arr_days[date("l", strtotime($date))]; 

        	$q_avail_date = $this->service_credit->checkSCDateAvailability($date, $empid);
        	
        	// walang sched
        	if(count($q_avail_date) == 0) $day_list[$date] = (($isHalfDay) ? 0.5 : 1);

        	// kapag may sched at halfday
        	if(count($q_avail_date) > 0 && $isHalfDay){
        		$isHalfDayResult = $this->service_credit->getEmpSchedIsHalfDay($empid, $date, $dayofweek);
        		$day_list[$date] = (($isHalfDayResult) ? 0.5 : 0);
        	}

        	if(count($q_avail_date > 0 && !$isHalfDay)){
        		$checkIfHoliday = $this->service_credit->checkSCDateAvailabilityWithHoliday($date, $empid);
        		if($checkIfHoliday == 0) $day_list[$date] = (($isHalfDay) ? 0.5 : 1);
        	}

        	// kpag yung days ay holiday...
        	$office = $this->service_credit->getEmpDept($empid);
        	$isHoliday = $this->attcompute->isHolidayNew($empid, $date, $office);
        	if($isHoliday) $day_list[$date] = (($isHalfDay) ? 0.5 : 1);

        }

        $day_list = $this->service_credit->getListOfAvailableSCDate($empid, $fdate, $tdate, $day_list);

        echo json_encode($day_list);
	}

	function editSC()
	{
		$data['code'] = $this->input->post('code');
		$this->load->view('employeemod/schistoryedit',$data);
	}

	function editSCU(){
		$data['code'] = $this->input->post('code');
		$this->load->view('employeemod/scapplyuse', $data);
	}

	function editSCUseManagement()
	{
		$data['code'] = $this->input->post('code');
		$data['details'] = $this->input->post('details');
		$this->load->view('process/sc_used_management_modify',$data);
	}
	function SCactions()
	{
		 $msg = "Successfully Updated!";
		 $code = $this->input->post("code");
		 $job = $this->input->post('job');
		 $hrhead = $this->service_credit->getDeptHead('head','HR');
		 if($job== "delete")
		 {
			if ($this->service_credit->delSC($code) > 0) 
			{
				$msg = "Successfully Deleted!";
			}
			else
			{
				if($this->service_credit->getSCStatus($code)) $msg = "Failed to delete! This items was already APPROVED!";
				else $msg = "Failed to delete! This items was already DISAPPROVED!";
			}
		 }
		 else{
		 	$allowApprover = $this->input->post('allowApprover');
		 	$id = $this->input->post('id');
		 	$date = $this->input->post('date');
		 	$date1 = $this->input->post('date1');
		 	$sc = $this->input->post('sc');
		 	$day = $this->input->post('day');
		 	$reason = $this->input->post('reason');
		 	if ($allowApprover == 0) {
				$this->service_credit->SCAppManageApproved($id,$hrhead);
			}	
			else{
			 	if ($this->service_credit->updateSC($id,$date,$date1,$sc,$day,$reason) > 0) {
			 		$msg = "Successfully Updated!";
			 	}
			 	else
			 	{
			 		$msg = "Failed to update!";	
			 	}
			}
		 }
		 echo $msg;
	}
	function SCUseActions()
	{
		$code = $this->input->post("code");
		$job = $this->input->post('job');
		echo $this->service_credit->serviceCreditUse($job,$code);
	}

	function saveSCApplication(){
		$this->load->model('extras');
		$this->load->model('utils');
		$this->load->library('extras_lib');
		$que = "";
		$message = 0;
		$msg = 0;
		$user 			= $this->session->userdata("username");
		$data 			= $this->input->post();
		$date			= isset($data['date']) 	? $data['date']  : "";
		$date1			= isset($data['date1']) 	? $data['date1']  : "";
		$dayMode		= isset($data['dayMode']) 	? $data['dayMode']  : "";
		$sc				= isset($data['sc']) 	? $data['sc']  : "";
		$sc_val 		= ($sc > 0.5) ? 1 : 0;  
		$reason			= isset($data['reason']) 	? $data['reason']  : "";
		$arr_date		= array();

		$dstatus = "PENDING";
        $ddate = "";
        $dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";

        $teachingType = $office = '';
		$det_res = $this->utils->getSingleTblData('employee',array('teachingType','office'),array('employeeid'=>$user));
		if($det_res->num_rows() > 0){
			$teachingType = $det_res->row(0)->teachingType;
			$office 	  = $det_res->row(0)->office;
		}

		$dheads = $this->service_credit->getuserDeptHead('head',	$user);	
		$dhead = $this->service_credit->getDeptHead('head',	$office);	
        $chead = $this->service_credit->getDeptHead('divisionhead',$office);	
        $hrhead = $this->service_credit->getDeptHead('head','HR');

        $code_request = 'SC';

        $isHead = $this->utils->checkIfHead($user);

        ///< head will look up on head code setup
        $forhead = '';
        if($user==$dheads || $user==$chead || $isHead) $forhead = 'HEAD';
        $code_request .= $forhead;

        if($teachingType == 'nonteaching') $code_request = $code_request.'NON';
        if(!$forhead && $office == 'LIB') $code_request = str_replace('NON', '', $code_request); ///< #ICA-HYPERION21711

		
		$seq = $this->service_credit->getAppSequence($code_request);

		if($seq->num_rows > 0)
		{
        	 $dseq   = $seq->row(0)->dhseq;
        	 $cseq   = $seq->row(0)->chseq;
        	 $hrseq  = $seq->row(0)->hhseq;
        	 $cpseq  = $seq->row(0)->cpseq;
        	 $fdseq  = $seq->row(0)->fdseq;
        	 $boseq  = $seq->row(0)->boseq;
        	 $pseq   = $seq->row(0)->pseq;
        	 $upseq  = $seq->row(0)->upseq;
	         $fdhead = $seq->row(0)->financedir;
    	     $bohead = $seq->row(0)->budgetoff;
        	 $phead  = $seq->row(0)->president;
    	   	 $uphead = $seq->row(0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

        	$cphead = '';
        	$campusid = $this->employee->getempdatacol('campusid',$user);
        	$c_res = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='$campusid'");
        	if($c_res->num_rows() > 0){
        		$cphead = $c_res->row(0)->campus_principal;
        	}


/*        	if($dhead == $user){
        		$dstatus = "APPROVED";
        		$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");

        	}*/

        	if (($date != "" || $date !=null) AND ($date1 != "" || $date1 !=null) ) {
        		$daterange = $this->utils->getDatesFromRange($date,$date1,'Y-m-d');
        		foreach ($daterange as $key) {
        			$q_date = $this->service_credit->checkSCDateAvailability($key, $user);
        			if(count($q_date) > 0) $arr_date[] = $key;
        		} 
        		
        		if ($date != $date1) {
        			$ids = $getidlast =  0 ;
        			$getId = $this->db->query("SELECT id FROM sc_app ORDER BY id ASC");
        			if ($getId->num_rows() > 0) {
        				foreach ($getId->result() as $keys => $value) {
        					$getidlast = $value->id;
        				}
        				$ids = $getidlast + 1;
		        		foreach ($daterange as $key) {
		        			$day_list	= array();
							$arr_days	= array(
								"Monday" 	=> "M",
								"Tuesday" 	=> "T",
								"Wednesday" => "W",
								"Thursday" 	=> "TH",
								"Friday" 	=> "F",
								"Saturday" 	=> "S",
								"Sunday" 	=> "SD"
							);

							$days_arr = $this->extras_lib->getDateIncluded($key, $key);

					        foreach ($days_arr as $date) {
					        	$day_list[$date] = 0;
					        	$dayofweek 		 = $arr_days[date("l", strtotime($date))]; 

					        	$q_avail_date = $this->service_credit->checkSCDateAvailability($date, $user);
					        	
					        	// walang sched
					        	if(count($q_avail_date) == 0) $day_list[$date] = (($dayMode) ? 0.5 : 1);

					        	// kapag may sched at halfday
					        	if(count($q_avail_date) > 0 && $dayMode){
					        		$isHalfDayResult = $this->service_credit->getEmpSchedIsHalfDay($user, $date, $dayofweek);
					        		$day_list[$date] = (($isHalfDayResult) ? 0.5 : 0);
					        	}

					        	// kpag yung days ay holiday...
					        	$office = $this->service_credit->getEmpDept($user);
					        	$isHoliday = $this->attcompute->isHolidayNew($user, $date, $office);
					        	if($isHoliday) $day_list[$date] = (($dayMode) ? 0.5 : 1);

					        }

					        $day_list = $this->service_credit->getListOfAvailableSCDate($user, $key, $key, $day_list);
					        if($day_list[$key]){
			        			$query = $this->db->query("SELECT * FROM sc_app WHERE date between '$key' AND '$key'  AND applied_by ='$user'");
			        			if ($query->num_rows() > 0) {
				        			$message++;
			        			}
			        			else
			        			{
			        				$sc = (in_array($key, $arr_date)) ? 0 : $sc_val;
			        				$base_id = $this->service_credit->insertBaseSCApp($ids,$user, $key, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead,
				        		    $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'EMPLOYEE');	

				        			if($base_id)	$que =  $this->service_credit->insertSCApp($ids,$base_id,$teachingType, $dstatus, $ddate, $user);
					        		else	$return = "Failed to save application.";
			        			}
			        		}
		        		}
        			}
        			else
        			{
        				 foreach ($daterange as $key) {
		        			$query = $this->db->query("SELECT * FROM sc_app WHERE date='$key' AND applied_by ='$user'");
		        			if ($query->num_rows() > 0) {
			        			$message++;
		        			}
		        			else
		        			{
		        				$sc = (in_array($key, $arr_date)) ? 0 : $sc_val;
		        				$base_id = $this->service_credit->insertBaseSCApp('-1',$user, $key, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead,
			        		    $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'EMPLOYEE');		

			        			if($base_id)	$que =  $this->service_credit->insertSCApp('-1',$base_id,$teachingType, $dstatus, $ddate, $user);
				        		else	$return = "Failed to save application.";
		        			}
		        		 }
        			}

        		}
        		else
        		{

        			// half day
        			$query = $this->db->query("SELECT * FROM sc_app WHERE date='$date' AND applied_by ='$user'");
		        			if ($query->num_rows() > 0) {
			        			$message++;
		        			}
		        			else
		        			{
		        			$base_id = $this->service_credit->insertBaseSCApp('',$user, $date, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead,
				        		    $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'EMPLOYEE');		

				        			if($base_id)	$que =  $this->service_credit->insertSCApp('',$base_id,$teachingType, $dstatus, $ddate, $user);
					        		else	$return = "Failed to save application.";
					        }
        		}
        	}	
        	else
        	{
        		$query = $this->db->query("SELECT * FROM sc_app WHERE date='$date' AND applied_by ='$user'");
        			if ($query->num_rows() > 0) {
	        			$message++;
        			}
        			else
        			{
			        	$base_id = $this->service_credit->insertBaseSCApp('',$user, $date, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'EMPLOYEE');	
			        	
			        	if($base_id)	$que =  $this->service_credit->insertSCApp('',$base_id,$teachingType, $dstatus, $ddate, $user);
			        	else		$return = "Failed to save application.";
	        		}
        	}
        }
		else
		{
        	$return = "No current setup for $teachingType service credit.";
        }
		
        if (count($message) > 0 ) {
        	$return = $message." Duplicate Entry! Check your history";
        }
		if($que){
			$return = " Service Credit successfully applied." ;
		}else{ 			$return = "Duplicate Entry! Check your history.";}
        echo $return;
	}

	function saveSCApp(){
			$this->load->model('extras');
			$this->load->model('utils');
			$que = "";
			$message = 0;
			$msg = 0;
			$user 			= $this->session->userdata("username");
			$data 			= $this->input->post();
			$date			= isset($data['date']) 	? $data['date']  : "";
			$date1			= isset($data['date1']) 	? $data['date1']  : "";
			$dayMode		= isset($data['dayMode']) 	? $data['dayMode']  : "";
			$sc				= isset($data['sc']) 	? $data['sc']  : "";
			$reason			= isset($data['reason']) 	? $data['reason']  : "";
			
			$dstatus = "PENDING";
	        $ddate = "";
	        $dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";
	        $teachingType = $office = '';

	        $det_res = $this->utils->getSingleTblData('employee',array('teachingType','office'),array('employeeid'=>$user));
    		if($det_res->num_rows() > 0){
    			$teachingType = $det_res->row(0)->teachingType;
    			$office 	  = $det_res->row(0)->office;
    		}

    		$dheads = $this->service_credit->getuserDeptHead('head',	$user);	
    		$dhead = $this->service_credit->getDeptHead('head',	$office);	
            $chead = $this->service_credit->getDeptHead('divisionhead',$office);	
            $hrhead = $this->service_credit->getDeptHead('head','HR');

            $code_request = 'SC';

            $isHead = $this->utils->checkIfHead($user);

            ///< head will look up on head code setup
            $forhead = '';
            if($user==$dheads || $user==$chead || $isHead) $forhead = 'HEAD';
            $code_request .= $forhead;

            if($teachingType == 'nonteaching') $code_request = $code_request.'NON';
            if(!$forhead && $office == 'LIB') $code_request = str_replace('NON', '', $code_request); ///< #ICA-HYPERION21711
			
			$seq = $this->service_credit->getAppSequence($code_request);
			
			if($seq->num_rows > 0)
			{
	        	 $dseq   = $seq->row(0)->dhseq;
	        	 $cseq   = $seq->row(0)->chseq;
	        	 $hrseq  = $seq->row(0)->hhseq;
	        	 $cpseq  = $seq->row(0)->cpseq;
	        	 $fdseq  = $seq->row(0)->fdseq;
	        	 $boseq  = $seq->row(0)->boseq;
	        	 $pseq   = $seq->row(0)->pseq;
	        	 $upseq  = $seq->row(0)->upseq;
    	         $fdhead = $seq->row(0)->financedir;
        	     $bohead = $seq->row(0)->budgetoff;
	        	 $phead  = $seq->row(0)->president;
        	   	 $uphead = $seq->row(0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

	        	$cphead = '';
	        	$campusid = $this->employee->getempdatacol('campusid',$user);
	        	$c_res = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='$campusid'");
	        	if($c_res->num_rows() > 0){
	        		$cphead = $c_res->row(0)->campus_principal;
	        	}


/*	        	if($dhead == $user){
	        		$dstatus = "APPROVED";
	        		$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");

	        	}*/

	        	if (($date != "" || $date !=null) AND ($date1 != "" || $date1 !=null) ) {
	        		$daterange = $this->utils->getDatesFromRange($date,$date1,'Y-m-d');
	        		if ($date != $date1) {
	        			$ids = $getidlast =  0 ;
	        			$getId = $this->db->query("SELECT id FROM sc_app ORDER BY id ASC");
	        			if ($getId->num_rows() > 0) {
	        				foreach ($getId->result() as $keys => $value) {
	        					$getidlast = $value->id;
	        				}
	        				$ids = $getidlast + 1;
			        		foreach ($daterange as $key) {
			        			$query = $this->db->query("SELECT * FROM sc_app WHERE date between '$key' AND '$key'  AND applied_by ='$user'");
			        			if ($query->num_rows() > 0) {
				        			$message++;
			        			}
			        			else
			        			{
			        				$base_id = $this->service_credit->insertBaseSCApp($ids,$user, $key, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead,
				        		    $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'EMPLOYEE');	

				        			if($base_id)	$que =  $this->service_credit->insertSCApp($ids,$base_id,$teachingType, $dstatus, $ddate, $user);
					        		else	$return = "Failed to save application.";
			        			}
			        		}
	        			}
	        			else
	        			{
	        				 foreach ($daterange as $key) {
			        			$query = $this->db->query("SELECT * FROM sc_app WHERE date='$key' AND applied_by ='$user'");
			        			if ($query->num_rows() > 0) {
				        			$message++;
			        			}
			        			else
			        			{

			        				$base_id = $this->service_credit->insertBaseSCApp('-1',$user, $key, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead,
				        		    $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'EMPLOYEE');		

				        			if($base_id)	$que =  $this->service_credit->insertSCApp('-1',$base_id,$teachingType, $dstatus, $ddate, $user);
					        		else	$return = "Failed to save application.";
			        			}
			        		 }
	        			}

	        		}
	        		else
	        		{
	        			$query = $this->db->query("SELECT * FROM sc_app WHERE date='$date' AND applied_by ='$user'");
			        			if ($query->num_rows() > 0) {
				        			$message++;
			        			}
			        			else
			        			{
			        			$base_id = $this->service_credit->insertBaseSCApp('',$user, $date, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead,
					        		    $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'EMPLOYEE');		

					        			if($base_id)	$que =  $this->service_credit->insertSCApp('',$base_id,$teachingType, $dstatus, $ddate, $user);
						        		else	$return = "Failed to save application.";
						        }
	        		}
	        	}	
	        	else
	        	{
	        		$query = $this->db->query("SELECT * FROM sc_app WHERE date='$date' AND applied_by ='$user'");
	        			if ($query->num_rows() > 0) {
		        			$message++;
	        			}
	        			else
	        			{
				        	$base_id = $this->service_credit->insertBaseSCApp('',$user, $date, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'EMPLOYEE');	
				        	
				        	if($base_id)	$que =  $this->service_credit->insertSCApp('',$base_id,$teachingType, $dstatus, $ddate, $user);
				        	else		$return = "Failed to save application.";
		        		}
	        	}
	        }
			else
			{
	        	$return = "No current setup for $teachingType service credit.";
	        }
			
	        if (count($message) > 0 ) {
	        	$return = $message." Duplicate Entry! Check your history";
	        }
			if($que){
				$return = " Service Credit successfully applied." ;
			}else{ 			$return = "Duplicate Entry! Check your history.";}
	        echo $return;
	    }
	function saveSCAppManagement(){
			$this->load->model('extras');
			$this->load->model('utils');
			$que = "";
			$message = 0;
			$msg = 0;
			$applied_by 	= $this->session->userdata("username");
			$data 			= $this->input->post();
			$date			= isset($data['date']) 	? $data['date']  : "";
			$date1			= isset($data['date1']) ? $data['date1']  : "";
			$dayMode		= isset($data['dayMode']) ? $data['dayMode']  : "";
			$sc				= isset($data['sc']) ? $data['sc']  : "";
			$reason			= isset($data['reason']) ? $data['reason']  : "";
			$user 			= isset($data['employee'])? $data['employee']:"";
			$allowApprover  = isset($data['allowApprover'])?$data['allowApprover'] : "";
			$office = $this->service_credit->getEmpDept($user);
			$dheads = $this->service_credit->getuserDeptHead('head',	$user);	
			$isPrincipal = $this->extensions->checkIfCampusPrincipal($user);
			$dhead = $this->service_credit->getDeptHead('head',	$office);	
	        $chead = $this->service_credit->getDeptHead('divisionhead',$office);	
	        $hrhead = $this->service_credit->getDeptHead('head','HR');
			
			$dstatus = "PENDING";
	        $ddate = "";
	        $dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";
	        $teachingType = $this->employee->getempdatacol('teachingType',$user);
	        $code_request = 'SC';

	        if($teachingType == 'nonteaching' ) {
	        	$code_request = 'SCNON';
	        }
	        else
	        {
	        	 $code_request = 'SC';
	        }

	        if ($dheads == $user || $isPrincipal) {
	        	$code_request = 'SCHEAD';
	        }
			
			$seq = $this->service_credit->getAppSequence($code_request);
			if($seq->num_rows > 0)
			{
	        	$dseq   = $seq->row(0)->dhseq;
	        	$cseq   = $seq->row(0)->chseq;
	        	$hrseq  = $seq->row(0)->hhseq;
	        	$cpseq  = $seq->row(0)->cpseq;
	        	$fdseq  = $seq->row(0)->fdseq;
	        	$boseq  = $seq->row(0)->boseq;
	        	$pseq   = $seq->row(0)->pseq;
	        	$upseq  = $seq->row(0)->upseq;
	        	$fdhead = $seq->row(0)->financedir;
	        	$bohead = $seq->row(0)->budgetoff;
	        	$phead  = $seq->row(0)->president;
	        	$uphead = $seq->row(0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

	        	$cphead = '';
	        	$campusid = $this->employee->getempdatacol('campusid',$user);
	        	$c_res = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='$campusid'");
	        	if($c_res->num_rows() > 0){
	        		$cphead = $c_res->row(0)->campus_principal;
	        	}


/*	        	if($dhead == $user){
	        		$dstatus = "APPROVED";
	        		$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");

	        	}
*/
	        	if (($date != "" || $date !=null) AND ($date1 != "" || $date1 !=null) ) {
	        		$daterange = $this->utils->getDatesFromRange($date,$date1,'Y-m-d');
	        		if ($date != $date1) {
	        			$ids = $getidlast =  0 ;
	        			$getId = $this->db->query("SELECT id FROM sc_app ORDER BY id ASC");
	        			if ($getId->num_rows() > 0) {
	        				foreach ($getId->result() as $keys => $value) {
	        					$getidlast = $value->id;
	        				}
	        				$ids = $getidlast + 1;
			        		foreach ($daterange as $key) {
			        			$query = $this->db->query("SELECT * FROM sc_app WHERE date between '$key' AND '$key'  AND applied_by ='$user'");

			        			if ($query->num_rows() > 0) {
				        			$message++;
			        			}
			        			else
			        			{

			        				$base_id = $this->service_credit->insertBaseSCApp($ids,$user, $key, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead,
				        		    $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'ADMIN');		

				        			if($base_id){
				        				$que =  $this->service_credit->insertSCApp($ids,$base_id,$teachingType, $dstatus, $ddate, $user);
				        				if ($allowApprover == 0) 
				        				{
				        					$this->service_credit->SCAppManageApproved($base_id,$hrhead);
				        				}	
				        				
				        			}
					        		else {
					        			$return = "Failed to save application.";
					        		}
			        			}
			        		}
	        			}
	        			else
	        			{
	        				 foreach ($daterange as $key) {
	        					$query = $this->db->query("SELECT * FROM sc_app WHERE date='$key' AND applied_by ='$user'");

			        			if ($query->num_rows() > 0) {
				        			$message++;
			        			}
			        			else
			        			{

			        				$base_id = $this->service_credit->insertBaseSCApp('-1',$user, $key, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead,
				        		    $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'ADMIN');	

				        			if($base_id)
			        				{
			        					$que =  $this->service_credit->insertSCApp('-1',$base_id,$teachingType, $dstatus, $ddate, $user);
				        				if ($allowApprover == 0) 
				        				{
				        					$this->service_credit->SCAppManageApproved($base_id,$hrhead);
				        				}	
				        			}
					        		else
					        		{	
					        			$return = "Failed to save application.";
			        				}
			        			}
			        		 }
	        			}

	        		}
	        		else
	        		{
	        			$query = $this->db->query("SELECT * FROM sc_app WHERE date='$date' AND applied_by ='$user'");

			        			if ($query->num_rows() > 0) {
				        			$message++;
			        			}
			        			else
			        			{
			        			$base_id = $this->service_credit->insertBaseSCApp('',$user, $date, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead,
					        		    $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'ADMIN');	

					        			if($base_id)
					        			{
					        				$que =  $this->service_credit->insertSCApp('',$base_id,$teachingType, $dstatus, $ddate, $user);
						        			if ($allowApprover == 0) 
						        			{
						        				$this->service_credit->SCAppManageApproved($base_id,$hrhead);
						        				
						        			}	
						        		}
						        		else
						        		{	
						        			$return = "Failed to save application.";
						        		}
						        }
	        		}
	        	}	
	        	else
	        	{
	        		$query = $this->db->query("SELECT * FROM sc_app WHERE date='$date' AND applied_by ='$user'");
	        		
	        			if ($query->num_rows() > 0) {
		        			$message++;
	        			}
	        			else
	        			{
				        	$base_id = $this->service_credit->insertBaseSCApp('',$user, $date, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,'ADMIN');
				        	
				        	if($base_id)
				        	{
				        		$que =  $this->service_credit->insertSCApp('',$base_id,$teachingType, $dstatus, $ddate, $user);
				        		if ($allowApprover == 0) 
				        		{
				        			$this->service_credit->SCAppManageApproved($base_id,$hrhead);
				        		}	
				        	}
				        	else
				        	{
				        		$return = "Failed to save application.";
		        			}
		        		}
	        	}
	        	
	        	// echo $user.'<br>';
	        	// echo $applied_by;die;
	        }
			else
			{
	        	$return = "No current setup for $teachingType service credit.";
	        }

			
	        if (count($message) > 0 ) {
	        	$return = $message." Duplicate Entry! Check your history";
	        }
			if($que){
				$return = " Service Credit successfully applied." ;
			}else{ 			$return = "Duplicate Entry! Check your history.";}
	        echo $return;
	    }
	// function saveSCApp(){
	// 	$this->load->model('extras');
	// 	$this->load->model('utils');
	// 	$que = $message ="";
	// 	$msg = 0;
	// 	$user 	= $this->session->userdata("username");
	// 	$data 			= $this->input->post();
	// 	$date			= isset($data['date']) 	? $data['date']  : "";
	// 	$date1			= isset($data['date1']) 	? $data['date1']  : "";
	// 	$dayMode		= isset($data['dayMode']) 	? $data['dayMode']  : "";
	// 	$sc				= isset($data['sc']) 	? $data['sc']  : "";
	// 	$reason			= isset($data['reason']) 	? $data['reason']  : "";
		
	// 	$office = $this->service_credit->getEmpDept($user);
		
	// 	$dheads = $this->service_credit->getuserDeptHead('head',	$user);	
	// 	$dhead = $this->service_credit->getDeptHead('head',	$office);	
 //        $chead = $this->service_credit->getDeptHead('divisionhead',$office);	
 //        $hrhead = $this->service_credit->getDeptHead('head','HR');
		
	// 	$dstatus = "PENDING";
 //        $ddate = "";
 //        $dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";

 //        $teachingType = $this->employee->getempdatacol('teachingType',$user);
 //        $code_request = 'SC';

 //        if($teachingType == 'nonteaching' ) {
 //        	$code_request = 'SCNON';
 //        }
 //        else
 //        {
 //        	 $code_request = 'SC';
 //        }

 //        if ($dheads == $user) {
 //        	$code_request = 'SCHEAD';
 //        }
		
	// 	$seq = $this->service_credit->getAppSequence($code_request);
		
	// 	if($seq->num_rows > 0)
	// 	{
 //        	$dseq  = $seq->row(0)->dhseq;
 //        	$cseq  = $seq->row(0)->chseq;
 //        	$hrseq = $seq->row(0)->hhseq;
 //        	$cpseq = $seq->row(0)->cpseq;
 //        	$fdseq = $seq->row(0)->fdseq;
 //        	$boseq = $seq->row(0)->boseq;
 //        	$pseq  = $seq->row(0)->pseq;
 //        	$upseq = $seq->row(0)->upseq;
 //        	$fdhead = $seq->row(0)->financedir;
 //        	$bohead = $seq->row(0)->budgetoff;
 //        	$phead = $seq->row(0)->president;
 //        	$uphead = $seq->row(0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

 //        	$cphead = '';
 //        	$campusid = $this->employee->getempdatacol('campusid',$user);
 //        	$c_res = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='$campusid'");
 //        	if($c_res->num_rows() > 0){
 //        		$cphead = $c_res->row(0)->campus_principal;
 //        	}


 //        	if($dhead == $user){
 //        		$dstatus = "APPROVED";
 //        		$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");

 //        	}

 //        	if (($date != "" || $date !=null) AND ($date1 != "" || $date1 !=null) ) {
 //        		$dateapplied = "";
 //        		if ($date == $date1) 
 //        		{
 //        			        		$query = $this->db->query("SELECT * FROM sc_app WHERE date='$date' AND applied_by ='$user'");
 //        			        			if ($query->num_rows() > 0) {
 //        				        			$message++;
 //        			        			}
 //        			        			else
 //        			        			{
 //        						        	$base_id = $this->service_credit->insertBaseSCApp($user, $date, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq);
        						        	
 //        						        	if($base_id)	$que =  $this->service_credit->insertSCApp($base_id,$teachingType, $dstatus, $ddate, $user);
 //        						        	else		$return = "Failed to save application.";
 //        				        		}
 //        		}
 //        		else
 //        		{
 //        				$service_credit_count = 0;
 //        				$daterange = $this->utils->getDatesFromRange($date,$date1,'Y-m-d');
 //        				$dateapplied = $date."/".$date1;
 //        		// echo '<pre>';var_dump($daterange);
 //        		foreach ($daterange as $key) 
 //        		{
 //        			$service_credit_count ++ ;
 //        		}
 //        			// $query = $this->db->query("SELECT * FROM sc_app WHERE date='$key' AND applied_by ='$user'");
 //        			// if ($query->num_rows() > 0) {
	//         		// 	$message++;
 //        			// }
 //        			// else
 //        			// {
 //        				$base_id = $this->service_credit->insertBaseSCApp($user, $dateapplied, $dayMode, $service_credit_count, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead,
	//         		    $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq);		

	//         			if($base_id)	
	//         				$que =  $this->service_credit->insertSCApp($base_id,$teachingType, $dstatus, $ddate, $user);
	// 	        		else	$return = "Failed to save application.";
 //        			// }
        		

        		    
 //        		// }
	// 	        }
 //        	}	
 //        	else
 //        	{
 //        		$query = $this->db->query("SELECT * FROM sc_app WHERE date='$date' AND applied_by ='$user'");
 //        			if ($query->num_rows() > 0) {
	//         			$message++;
 //        			}
 //        			else
 //        			{
	// 		        	$base_id = $this->service_credit->insertBaseSCApp($user, $date, $dayMode, $sc, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq);
			        	
	// 		        	if($base_id)	$que =  $this->service_credit->insertSCApp($base_id,$teachingType, $dstatus, $ddate, $user);
	// 		        	else		$return = "Failed to save application.";
	//         		}
 //        	}
 //        }
	// 	else
	// 	{
 //        	$return = "No current setup for $teachingType service credit.";
 //        }
		
	// 	if($que){
	// 		$return = " Service Credit successfully applied." ;
	// 	}else{ 			
	// 		$return = "Duplicate Entry! Check your history.";}
 //        echo $return;
 //    }
	

	function serviceCreditActions()
	{
		$data = $this->input->post();
		$return = $this->service_credit->serviceCreditActions($data);
		echo json_encode($return);

	}
	function getApprovalSeqStatus(){
		$this->load->model('utils');
		$post 		= $this->input->post();
		$id 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		$view 		= isset($post['view']) 	? $post['view'] 	: "";
		// $position_names = array('dhead'=>'Department Head','chead'=>'Cluster Head','hrhead'=>'HR Director','fdhead'=>'Financial Director','bohead'=>'Budget Officer','phead'=>'President','uphead'=>'University Physician');
		$position_names = $this->utils->getRequestApprover();

		$arr_aprvl_seq 	= array();
		$setup = $this->service_credit->getAppSequencePerSC($id);
		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->sortApprovalSeqPerSC($setup->row(0));
			
		}
		foreach ($arr_aprvl_seq as $key => $obj) {
			$arr_aprvl_seq[$key]['position_name'] 	= $position_names[$obj['position']];
			$arr_aprvl_seq[$key]['fullname'] 		= $this->utils->getFullName($obj['head_id']);
		}
		$data['arr_aprvl_seq'] = $arr_aprvl_seq;
		
		$this->load->view("employeemod/$view",$data);
	}

	function getApprovalSeqStatusUse(){
		$this->load->model('utils');
		$post 		= $this->input->post();
		$id 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		$view 		= isset($post['view']) 	? $post['view'] 	: "";
		// $position_names = array('dhead'=>'Department Head','chead'=>'Cluster Head','hrhead'=>'HR Director','fdhead'=>'Financial Director','bohead'=>'Budget Officer','phead'=>'President','uphead'=>'University Physician');
		$position_names = $this->utils->getRequestApprover();

		$arr_aprvl_seq 	= array();
		$setup = $this->service_credit->getAppSequencePerSCU($id);
		if($setup->num_rows() > 0){

			$arr_aprvl_seq = $this->sortApprovalSeqPerSC($setup->row(0));
		}
		foreach ($arr_aprvl_seq as $key => $obj) {
			$arr_aprvl_seq[$key]['position_name'] 	= $position_names[$obj['position']];
			$arr_aprvl_seq[$key]['fullname'] 		= $this->utils->getFullName($obj['head_id']);
		}
		$data['arr_aprvl_seq'] = $arr_aprvl_seq;
		 // var_dump( $data['arr_aprvl_seq']);

		$this->load->view("employeemod/$view",$data);
	}

	function sortApprovalSeqPerSC($setup){
		$arr_aprvl_seq = array();
		$arr_aprvl_seq[ $setup->dseq ] = array('position'=>'dhead' , 'head_id'=>$setup->dhead ,  'status'=>$setup->dstatus , 'date'=>$setup->ddate);
		$arr_aprvl_seq[ $setup->cseq ] = array('position'=>'chead' , 'head_id'=>$setup->chead,   'status'=>$setup->cstatus , 'date'=>$setup->cdate);
		$arr_aprvl_seq[ $setup->hrseq ] = array('position'=>'hrhead', 'head_id'=>$setup->hrhead, 'status'=>$setup->hrstatus , 'date'=>$setup->hrdate);
		$arr_aprvl_seq[ $setup->cpseq ] = array('position'=>'cphead', 'head_id'=>$setup->cphead, 'status'=>$setup->cpstatus , 'date'=>$setup->cpdate);
		$arr_aprvl_seq[ $setup->fdseq ] = array('position'=>'fdhead', 'head_id'=>$setup->fdhead, 'status'=>$setup->fdstatus , 'date'=>$setup->fddate);
		$arr_aprvl_seq[ $setup->boseq ] = array('position'=>'bohead', 'head_id'=>$setup->bohead, 'status'=>$setup->bostatus , 'date'=>$setup->bodate);
		$arr_aprvl_seq[ $setup->pseq  ] = array('position'=>'phead' , 'head_id'=>$setup->phead,  'status'=>$setup->pstatus , 'date'=>$setup->pdate);
		$arr_aprvl_seq[ $setup->upseq ] = array('position'=>'uphead', 'head_id'=>$setup->uphead, 'status'=>$setup->upstatus , 'date'=>$setup->update);
		//unset 0
		unset($arr_aprvl_seq['0']);
		//ksort
		ksort($arr_aprvl_seq);
		return $arr_aprvl_seq;
	}
	function getViewSCUsage()
	{
		$data 	    = $this->input->post();
		$folder     = isset($data['folder']) ? $data['folder']:"";
		$view 		= isset($data['view']) 	? $data['view'] 	: "";
		$this->load->view("employeemod/$view",$data);

			
	}
	
	
	function getSCAppListToManage(){
		$this->load->model('utils');
		$colhead = $status = $isLastApprover = "";		///< ex. $colhead = "dhead" / "chead" / "hrhead";
		$prevcolstatus = ""	;   ///< column name for head status to check if already approved by previous approver in sequence
		$status 	= $this->input->post('status');
		$datefrom 	= $this->input->post('datefrom');
		$dateto 	= $this->input->post('dateto');
		$isHrHead 	= false;

		$user 			= $this->session->userdata('username');

		$hrhead = $this->utils->getDeptHead('head','HR');
		if($user == $hrhead) $isHrHead = true;
		
		///< for regular employee
		$sc_list = array();
		///< --------------------------(condition added for Campus Principal which is ommitted for non teaching)-------------------------------------------
		$sc_list_teaching = $this->getSCAppListToManageProcess('SC',$status,$datefrom,$dateto,$user,'teaching');
		$sc_list_non = $this->getSCAppListToManageProcess('SCNON',$status,$datefrom,$dateto,$user,'nonteaching');

		# > added by mcu-hyperion 21295 by justine (with e)
		$sc_list_head = $this->getSCAppListToManageProcess('SCHEAD',$status,$datefrom,$dateto,$user,'teaching',false);
		$sc_list_head_non = $this->getSCAppListToManageProcess('SCHEAD',$status,$datefrom,$dateto,$user,'nonteaching',false);

		if(sizeof($sc_list_teaching) > 0) 	$sc_list =  array_merge($sc_list, $sc_list_teaching);
		if(sizeof($sc_list_non) > 0) 		$sc_list =  array_merge($sc_list, $sc_list_non);

		# > added by mcu-hyperion 21295 by justine (with e)
		if(sizeof($sc_list_head) > 0)		$sc_list =  array_merge($sc_list, $sc_list_head);
		if(sizeof($sc_list_head_non) > 0)		$sc_list =  array_merge($sc_list, $sc_list_head_non);


		$data['sc_list'] =$sc_list;
		$data['isHrHead'] 	= $isHrHead; 

		/*echo "<pre>"; print_r($data);
		echo "</pre>";*/ 

		$this->load->view("employeemod/mailscapp_details",$data);
	}

	function getSCAppListToManageProcess($code_request="SC",$status='',$datefrom='',$dateto='',$user='',$teachingType='',$isSCUse=false){
		$colhead = $isLastApprover = "";
		$prevcolstatus = ""	;
		$sc_list = array();
		$arr_aprvl_seq 	= array();
		$setup 			= $this->service_credit->getAppSequence($code_request);
		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->service_credit->sortApprovalSeq($setup->row(0));
		}

		$aprvl_count = sizeof($arr_aprvl_seq);
		$prevkey 	 = '';
		$arr_apprv = array();
		foreach ($arr_aprvl_seq as $key => $obj) {
			if($obj['head_id'] == $user){
				$colhead = $obj['position'];
				if($aprvl_count == $key) 	 $isLastApprover = true;
				if($key > 1) 				 $prevkey 		 = $key - 1;
				// break;

				if($prevkey){
					$prevcolstatus = substr($arr_aprvl_seq[$prevkey]['position'],0,-4) . 'status';
				}
				$colstatus =  $colhead ? (substr($colhead,0,-4) . 'status') : '';

				array_push($arr_apprv, array('seq_count'=>$key,'colhead'=>$colhead,'colstatus'=>$colstatus,'prevcolstatus'=>$prevcolstatus,'isLastApprover'=>$isLastApprover,'code_request'=>$code_request));

				$isLastApprover = '';
			}
		}
		//echo "<pre>" . $code_request . "<br>";print_r($arr_apprv); echo "</pre>";

		foreach ($arr_apprv as $key => $arr) {
			$prev_seq = ($arr['seq_count'] > 1) ? ($arr['seq_count'] - 1) : 0;
			if($isSCUse) $temp_res = $this->service_credit->getSCUAppListToManage($user, $arr['colhead'], $arr['colstatus'], $status, $arr['prevcolstatus'], $datefrom, $dateto,$teachingType,$arr['seq_count'],$prev_seq);
			
			else 		 $temp_res = $this->service_credit->getSCAppListToManage($user, $arr['colhead'], $arr['colstatus'], $status, $arr['prevcolstatus'], $datefrom, $dateto,$teachingType,$arr['seq_count'], $prev_seq);
			
			if($temp_res->num_rows() > 0){
				foreach ($temp_res->result() as $key => $row) {
					$sc_list[$row->aid] = array('data_list'=>$row,'colhead'=>$arr['colhead'],'colstatus'=>$arr['colstatus'],'prevcolstatus'=>$arr['prevcolstatus'],'isLastApprover'=>$arr['isLastApprover'],'code_request'=>$arr['code_request']);
				}
			}

			// $temp_res = $temp_res->result();
			// array_push($sc_list, array('data_list'=>$temp_res,'colhead'=>$arr['colhead'],'colstatus'=>$arr['colstatus'],'prevcolstatus'=>$arr['prevcolstatus'],'isLastApprover'=>$arr['isLastApprover'],'code_request'=>$code_request));
		}
		
		return $sc_list;
	}

	
	function getSCDetails(){
		$post 		= $this->input->post();
		$id 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		$view 		= isset($post['view']) 		? $post['view'] 	: "";
		$colhead 	= isset($post['colhead']) 	? $post['colhead'] 	: "";
		$code_request 	= isset($post['code_request']) 	? $post['code_request'] 	: "";
		$colstatus 	=  $colhead ? (substr($colhead,0,-4) . 'status') : '';
		
		$data 		= $this->service_credit->getSCDetails($id,$colstatus);
		$data["idkey"] = $id;
		$data['job'] 	 		= $this->input->post("job");
		$data['colhead'] 		= $colhead;
		$data['isLastApprover'] = $this->input->post("isLastApprover");
		$data['code_request'] = $this->input->post("code_request");
		
		$this->load->view("employeemod/$view",$data);
	}
	
	function getStartEndtimePerDay($startdate='', $enddate='', $employeeid=''){
		$scheddisp = '';
		if($employeeid){
			$dates_arr = $this->getDatesFromRange($startdate,$enddate);
			$daysofwk_arr = array();
			foreach ($dates_arr as $date) {
			    $dayofwk = date('w', strtotime($date));
			    array_push($daysofwk_arr, $dayofwk);
			}

			$res = $this->service_credit->getEmpSchedMinMaxTimePerday($employeeid);

			if($res->num_rows() > 0){
				foreach ($res->result() as $key => $row) {
				    if(in_array($row->idx, $daysofwk_arr)){
				        $scheddisp .= $row->dayofweek . ' ' . date('h:i A',strtotime($row->start)) . '-' . date('h:i A',strtotime($row->end)) . ' | ';
				    }
				}
			}
			if($scheddisp) $scheddisp = substr($scheddisp, 0, -3);
		}
		return $scheddisp;
	}
	
	function getDatesFromRange($start, $end, $format = 'Y-m-d') {
	    $array = array();
	    $interval = new DateInterval('P1D');

	    $realEnd = new DateTime($end);
	    $realEnd->add($interval);

	    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

	    foreach($period as $date) { 
			$array[] = $date->format($format); 
	    }

	    return $array;
	}
	
	function saveSCStatusChange(){
		///< scid,status,colhead,isLastApprover -- if last na ,set status
		$scid 			= $this->input->post('scid');
		$status 		= $this->input->post('status');
		$colhead 		= $this->input->post('colhead');
		$isLastApprover = $this->input->post('isLastApprover');
		$colstatus 		= substr($colhead,0,-4) . 'status';
		$coldate 		= substr($colhead,0,-4) . 'date';
		$user = $this->session->userdata('username');
		$approval_id    = $this->input->post('approval_id');
		$code_request   = $this->input->post('code_request');

		// $date           = $this->input->post('date');
		// $date1           = $this->input->post('date1');
		// echo $approval_id;
		if ($approval_id == 0) {
			$res = $this->service_credit->saveSCStatusChange($user,$scid,$status,$colstatus,$coldate,$colhead,$isLastApprover,'',$approval_id);

		}
		else
		{
			$res = $this->service_credit->saveSCStatusChange($user,$scid,$status,$colstatus,$coldate,$colhead,$isLastApprover,'',$approval_id);	
		}

		if(!$isLastApprover && $status == 'APPROVED'){ ///< get next in sequence with same head id
			$arr_aprvl_seq 	= array();
			$setup 			= $this->service_credit->getAppSequence($code_request);
			if($setup->num_rows() > 0){
				$arr_aprvl_seq = $this->service_credit->sortApprovalSeq($setup->row(0));
			}
			$aprvl_count = sizeof($arr_aprvl_seq);
			$prevkey 	 = '';
			$arr_apprv = array();
			foreach ($arr_aprvl_seq as $key => $obj) {
				$isLastApprover_tmp = false;
				$colhead_tmp = $obj['position'];

				if($obj['head_id'] == $user && $colhead_tmp != $colhead){
					if($aprvl_count == $key) 	 $isLastApprover_tmp = true;
					if($key > 1) 				 $prevkey 		 = $key - 1;

					$colstatus_tmp =  $colhead_tmp ? (substr($colhead_tmp,0,-4) . 'status') : '';
					$coldate_tmp =  $colhead_tmp ? (substr($colhead_tmp,0,-4) . 'date') : '';

					if($isLastApprover_tmp){
						if($arr_aprvl_seq[$prevkey]['head_id'] == $user){
							$prev_colhead = $arr_aprvl_seq[$prevkey]['position'];
							$res_tmp = $this->service_credit->saveSCStatusChange($user,$scid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$prev_colhead);
						}
					}else{
						$res_tmp = $this->service_credit->saveSCStatusChange($user,$scid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp);
					}

				}
			}

		}

		if($res) echo json_encode(array('err_code'=>0,'msg'=>"Success! Status now is : $status"));
		else 	 echo json_encode(array('err_code'=>2,'msg'=>'Failed to set status.'));
		// if($res) echo "Success! Status now is : $status";
		// else 	 echo $approval_id.'Failed to set status.';
	}
	
	# > added by justin (with e) for ica-hyperion 21185
	function findAvailableSCDates(){
		$data = array();

		$fields_data = array("employeeid" => $this->session->userdata("username"));
		$q_availableSCDates = $this->service_credit->searchAvailableSCDates($fields_data);
		foreach ($q_availableSCDates as $res) {
			$data[$res->id][$res->date]["available_sc"] = $res->total_sc;
			$data[$res->id][$res->date]["cdate"] 		= date("F d, Y", strtotime($res->date));
		}

		echo json_encode($data);
	}

	function getSCDatesWithAvailable(){
		$return ='';
		$return = $this->service_credit->getSCDatesWithAvailable();
		echo $return;
		
	}
	
	function getSCDatesWithAvailableHR(){
		$empid = $this->input->post('empid');
		$employeeid = isset($empid)? $empid :"";
		$return ='';
		$return = $this->service_credit->getSCDatesWithAvailableHR($employeeid);
		echo $return;
		
	}
	function saveSCAppUse(){
		$this->load->model('extras');
		$this->load->model('utils');
		
		$user 	= $this->session->userdata("username");
		$data 			= $this->input->post();
		$date			= isset($data['date']) 	? $data['date']  : "";
		$dayMode		= isset($data['dayMode']) 	? $data['dayMode']  : "";
		
		
		$scdate			= isset($data['scdate']) 	? $data['scdate']."/"  : "";
		$scdate			.= isset($data['scdate2']) 	? $data['scdate2']  : "";
		
		$sc				= isset($data['sc']) 	? $data['sc']."/"  : "";
		$sc				.= isset($data['sc2']) 	? $data['sc2']  : "";
		
		$nsc			= 0;
		$sched_affected = "";
		$ishalfday = isset($data['ishalfday']) ? 1 : 0;
        if($ishalfday) $sched_affected = implode(',', $data['sched_affected']);
		foreach(explode('/',$sc) as $k => $v)
		{
			$nsc += $v;
		}
		
		$remark			= isset($data['remark']) 	? $data['remark']  : "";
		
		$office = $this->service_credit->getEmpDept($user);
		
		$dhead = $this->service_credit->getDeptHead('head',	$office);	
        $chead = $this->service_credit->getDeptHead('divisionhead',$office);	
        $hrhead = $this->service_credit->getDeptHead('head',		'HR');
		
		$dstatus = "PENDING";
        $ddate = "";
        $dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";
		
		$teachingType = $this->employee->getempdatacol('teachingType',$user);
        $code_request = '';
		if($teachingType == 'nonteaching') {$code_request = 'SCNON';}
		else {$code_request = 'SC';}
		$seq = $this->service_credit->getAppSequence($code_request);
		
		if($seq->num_rows > 0)
		{
        	$dseq  = $seq->row(0)->dhseq;
        	$cseq  = $seq->row(0)->chseq;
        	$hrseq = $seq->row(0)->hhseq;
        	$cpseq = $seq->row(0)->cpseq;
        	$fdseq = $seq->row(0)->fdseq;
        	$boseq = $seq->row(0)->boseq;
        	$pseq  = $seq->row(0)->pseq;
        	$upseq = $seq->row(0)->upseq;
        	$fdhead = $seq->row(0)->financedir;
        	$bohead = $seq->row(0)->budgetoff;
        	$phead = $seq->row(0)->president;
        	$uphead = $seq->row(0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

        	$cphead = '';
        	$campusid = $this->employee->getempdatacol('campusid',$user);
        	$c_res = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='$campusid'");
        	if($c_res->num_rows() > 0){
        		$cphead = $c_res->row(0)->campus_principal;
        	}

/*        	if($dhead == $user){
        		$dstatus = "APPROVED";
        		$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");
        	}*/

        	$base_id = $this->service_credit->insertBaseSCAppUse($user, $date, $dayMode, $nsc, $scdate, $sc, $remark, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,$sched_affected,'EMPLOYEE');
        	
        	$que = "";
        	if($base_id)	$que =  $this->service_credit->insertSCAppUse($base_id,$teachingType,'PENDING', $ddate, $user);
        	else		$return = "Failed to save application.";

        }
		else
		{
        	$return = "No current setup for $teachingType service credit.";
        }
		
		if($que){
			// $this->service_credit->useServiceCredit($user,$scdate,$sc);
			$return = "Service Credit successfully applied.";
		}else{ 			$return = "Failed to save application.";}
         echo $return;
    }

    	# for ica-hyperion 21185
    	# by justin (with e)
		function saveSCAppUseWithMultipleSCDate(){
			$remarks 	= $this->input->post('remark');
			$code 	= $this->input->post('code');
			$sc_request = $this->input->post('sc_request'); 
			$result 	= "";

			if($code){ 
				$this->updateSCUse($code, $sc_request, $remarks);
				die;
			}

			$arr_success = array();
			$arr_error   = array();
			foreach ($sc_request as $scnum => $info) {
				$data = array();
				$data = $info;
				$data['employee'] = $this->input->post('employee');
				$data['daymode'] = "";
				$data['remark'] = $remarks;
				$return = $this->saveSCAppUseNew($data);
				
				if($return == "Service Credit successfully applied.") array_push($arr_success, $data['date']);
				else{
					$arr_error[$data['date']] = $return;
				}
			}

			$result .= "Success : ". count($arr_success) ."\nError List:";
			foreach ($arr_error as $key => $error) {
				$result .= "\n* Date : ". date("F d, Y", strtotime($key)) ." - $error";
			}

			if(count($arr_error) == 0) $result .= " no error";

			echo $result;
		}

		function saveSCAppUseNew($data){
			$this->load->model('extras');
			$this->load->model('utils');
			
			$user 	= $this->session->userdata("username");
			$date			= isset($data['date']) 	? $data['date']  : "";
			$dayMode		= isset($data['dayMode']) 	? $data['dayMode']  : "";
			
			
			$scdate			= isset($data['scdate']) 	? $data['scdate']."/"  : "";
			$scdate			.= isset($data['scdate2']) 	? $data['scdate2']  : "";
			
			$sc				= isset($data['sc']) 	? $data['sc']."/"  : "";
			$sc				.= isset($data['sc2']) 	? $data['sc2']  : "";
			
			$nsc			= 0;
			$sched_affected = "";
			$base_id = "";
			$ishalfday = isset($data['ishalfday']) ? 1 : 0;
	        if($ishalfday) $sched_affected = isset($data['sched_affected']) ? $data['sched_affected'] : "";
			foreach(explode('/',$sc) as $k => $v)
			{
				$nsc += $v;
			}
			
			$remark			= isset($data['remark']) 	? $data['remark']  : "";
			
			$office = $this->service_credit->getEmpDept($user);
			
			$dhead = $this->service_credit->getDeptHead('head',	$office);	
	        $chead = $this->service_credit->getDeptHead('divisionhead',$office);	
	        $hrhead = $this->service_credit->getDeptHead('head',		'HR');
			
			$dstatus = "PENDING";
	        $ddate = "";

	        $cpstatus = "PENDING";
	        $cpdate = "";

	        $dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";
			
			$teachingType = $this->employee->getempdatacol('teachingType',$user);
	        $code_request = '';
			if($teachingType == 'nonteaching' && $dhead != $user) {$code_request = 'SCNON';}
			else if($teachingType == 'nonteaching' && $dhead == $user) {$code_request = 'SCHEADNON';}
			else {$code_request = 'SC';}
			$seq = $this->service_credit->getAppSequence($code_request);
			
			if($seq->num_rows > 0)
			{
	        	$dseq  = $seq->row(0)->dhseq;
	        	$cseq  = $seq->row(0)->chseq;
	        	$hrseq = $seq->row(0)->hhseq;
	        	$cpseq = $seq->row(0)->cpseq;
	        	$fdseq = $seq->row(0)->fdseq;
	        	$boseq = $seq->row(0)->boseq;
	        	$pseq  = $seq->row(0)->pseq;
	        	$upseq = $seq->row(0)->upseq;
	        	$fdhead = $seq->row(0)->financedir;
	        	$bohead = $seq->row(0)->budgetoff;
	        	$phead = $seq->row(0)->president;
	        	$uphead = $seq->row(0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

	        	$cphead = '';
	        	$campusid = $this->employee->getempdatacol('campusid',$user);
	        	$c_res = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='$campusid'");
	        	if($c_res->num_rows() > 0){
	        		$cphead = $c_res->row(0)->campus_principal;
	        	}

/*	        	if($dhead == $user){
	        		$dstatus = "APPROVED";
	        		$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");
	        	}
*/
	        	if($cphead == $user){
	        		$cpstatus = "APPROVED";
	        		$cpdate 	 = date_format( new DateTime('today') ,"Y-m-d");
	        	}

	        	/*validate applied sc balance*/
	        	$available_sc = $this->service_credit->validateAvaliableSC($scdate);
	        	$sc_days = substr($sc, 0, -1);
	        	$available_sc -= $sc_days;
	        	
	        	if($available_sc >= 0){
		        	$base_id = $this->service_credit->insertBaseSCAppUse($user, $date, $dayMode, $nsc, $scdate, $sc, $remark, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,$sched_affected,'EMPLOYEE');
	        	}else{
	        		$return = "Failed to save application. insufficient balance. ";
	        	}
	        	
	        	$que = "";
	        	if($base_id)	$que =  $this->service_credit->insertSCAppUse($base_id,$teachingType,'PENDING', $ddate, $user, $cpstatus, $cpdate);
	        	else		$return = "Failed to save application.";

	        }
			else
			{
	        	$return = "No current setup for $teachingType service credit.";
	        }
			
			if($que){
				$return = "Service Credit successfully applied.";
			}else{ 			$return = "Failed to save application.";}
	        return $return;
		}
    	# end for ica-hyperion 21185


		function saveSCAppUseHR(){
			$this->load->model('extras');
			$this->load->model('utils');
			
			$user 	= $this->session->userdata("username");
			$data 			= $this->input->post();

			$code			= isset($data['code']) 	? $data['code']  : "";

			$date			= isset($data['date']) 	? $data['date']  : "";
			$dayMode		= isset($data['dayMode']) 	? $data['dayMode']  : "";
			
			$empid 			= isset($data['employee']) ? $data['employee'] : "";
			$scdate			= isset($data['scdate']) 	? $data['scdate']."/"  : "";
			$scdate			.= isset($data['scdate2']) 	? $data['scdate2']  : "";
			
			$sc				= isset($data['sc']) 	? $data['sc']."/"  : "";
			$sc				.= isset($data['sc2']) 	? $data['sc2']  : "";
			
			$allowApprover  = isset($data['allowApprover'])? $data['allowApprover'] :"";
			$nsc			= 0;
			$sched_affected = "";
			$ishalfday = isset($data['ishalfday']) ? 1 : 0;
	        if($ishalfday) $sched_affected = implode(',', $data['sched_affected']);
			foreach(explode('/',$sc) as $k => $v)
			{
				$nsc += $v;
			}
			
			$remark			= isset($data['remark']) 	? $data['remark']  : "";

			if($code && $allowApprover == 1){ 
				$return = $this->updateSCUseAdmin($code, $date, $remark, $scdate, $sc, $sched_affected, $ishalfday);
				echo $return;
				die;
			}
			
			$office = $this->service_credit->getEmpDept($empid);
			
			$dhead = $this->service_credit->getDeptHead('head',	$office);	
	        $chead = $this->service_credit->getDeptHead('divisionhead',$office);	
	        $hrhead = $this->service_credit->getDeptHead('head',		'HR');
			
			$dstatus = "PENDING";
	        $ddate = "";
	        $dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";
			
			$teachingType = $this->employee->getempdatacol('teachingType',$empid);
	        $code_request = '';
			if($teachingType == 'nonteaching') {$code_request = 'SCNON';}
			else {$code_request = 'SC';}

			$seq = $this->service_credit->getAppSequence($code_request);
			
			if($seq->num_rows > 0)
			{
	        	$dseq  = $seq->row(0)->dhseq;
	        	$cseq  = $seq->row(0)->chseq;
	        	$hrseq = $seq->row(0)->hhseq;
	        	$cpseq = $seq->row(0)->cpseq;
	        	$fdseq = $seq->row(0)->fdseq;
	        	$boseq = $seq->row(0)->boseq;
	        	$pseq  = $seq->row(0)->pseq;
	        	$upseq = $seq->row(0)->upseq;
	        	$fdhead = $seq->row(0)->financedir;
	        	$bohead = $seq->row(0)->budgetoff;
	        	$phead = $seq->row(0)->president;
	        	$uphead = $seq->row(0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

	        	$cphead = '';
	        	$campusid = $this->employee->getempdatacol('campusid',$empid);
	        	$c_res = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='$campusid'");
	        	if($c_res->num_rows() > 0){
	        		$cphead = $c_res->row(0)->campus_principal;
	        	}

/*	        	if($dhead == $user){
	        		$dstatus = "APPROVED";
	        		$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");
	        	}
*/
	        	if ($allowApprover == 1) {
	        	
		        	$base_id = $this->service_credit->insertBaseSCAppUse($empid, $date, $dayMode, $nsc, $scdate, $sc, $remark, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,$sched_affected,'ADMIN');
		        	
		        	$que = "";
		        	if($base_id)
		        	{	$que =  $this->service_credit->insertSCAppUse($base_id,$teachingType,'PENDING', $ddate, $empid);$return = "Service Credit successfully applied."; }
		        	else {$return = "Failed to save application.";}

	        	}
	        	else
	        	{
	        		$dseq = $cseq  = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";
	      			$dhead = $chead = $cphead = $fdhead = $bohead = $phead = "";

	        		$base_id = $this->service_credit->SCAppBaseUseHR($user, $date, $dayMode, $nsc, $scdate, $sc, $remark, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,$sched_affected,'ADMIN');

	        		if ($base_id) {
	        			$que = $this->service_credit->SCAppBaseUseHRApproved($base_id,$teachingType, $empid,$scdate,$sc,$date); $return = "Service Credit Used successfully applied.";
	        			$this->service_credit->serviceCreditUse("delete", $code);
	        		}
	        		else
	        		{
	        			$return = "Failed to apply service credit used!";
	        		}

	        	}

	        }
			else
			{
	        	$return = "No current setup for $teachingType service credit.";
	        }

			
			// if($que){
			// 	// $this->service_credit->useServiceCredit($user,$scdate,$sc);
			// 	$return = "Service Credit successfully applied.";
			// }else{ 			$return = "Failed to save application.";}
	         echo $return;
	    }


	function getSCUAppListToManage(){
		$this->load->model('utils');
		$colhead = $status = $isLastApprover = "";		///< ex. $colhead = "dhead" / "chead" / "hrhead";
		$prevcolstatus = ""	;   ///< column name for head status to check if already approved by previous approver in sequence
		$status 	= $this->input->post('status');
		$datefrom 	= $this->input->post('datefrom');
		$dateto 	= $this->input->post('dateto');
		$isHrHead 	= false;

		$user 			= $this->session->userdata('username');

		$hrhead = $this->utils->getDeptHead('head','HR');
		if($user == $hrhead) $isHrHead = true;

		///< for regular employee
		$scu_list = array();
		///< --------------------------(condition added for Campus Principal which is ommitted for non teaching)-------------------------------------------
		$scu_list_teaching = $this->getSCAppListToManageProcess('SC',$status,$datefrom,$dateto,$user,'teaching',true);
		$scu_list_non = $this->getSCAppListToManageProcess('SCNON',$status,$datefrom,$dateto,$user,'nonteaching',true);

		# > added by mcu-hyperion 21295 by justine (with e)
		$scu_list_head = $this->getSCAppListToManageProcess('SCHEAD',$status,$datefrom,$dateto,$user,'teaching',true);
		$scu_list_head_non = $this->getSCAppListToManageProcess('SCHEADNON',$status,$datefrom,$dateto,$user,'nonteaching',true);

		if(sizeof($scu_list_teaching) > 0) 	$scu_list =  array_merge($scu_list, $scu_list_teaching);
		if(sizeof($scu_list_non) > 0) 		$scu_list =  array_merge($scu_list, $scu_list_non);

		# > added by mcu-hyperion 21295 by justine (with e)
		if(sizeof($scu_list_head) > 0)		$scu_list =  array_merge($scu_list, $scu_list_head);
		if(sizeof($scu_list_head_non) > 0)		$scu_list =  array_merge($scu_list, $scu_list_head_non);


		$data['scu_list'] =$scu_list;
		$data['isHrHead'] 	= $isHrHead; 

		$this->load->view("employeemod/mailscuapp_details",$data);
	}

	
	function getSCUDetails(){
		$post 		= $this->input->post();
		$id 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		$view 		= isset($post['view']) 		? $post['view'] 	: "";
		$colhead 	= isset($post['colhead']) 	? $post['colhead'] 	: "";
		$colstatus 	=  $colhead ? (substr($colhead,0,-4) . 'status') : '';
		$data 		= $this->service_credit->getSCUDetails($id,$colstatus);
		$data["idkey"] = $id;
		$data['job'] 	 		= $this->input->post("job");
		$data['colhead'] 		= $colhead;
		$data['isLastApprover'] = $this->input->post("isLastApprover");
		$data['code_request'] = $this->input->post("code_request");
		// echo'<pre>'; var_dump($data);
		$this->load->view("employeemod/$view",$data);
	}
	
	function saveSCUStatusChange(){
		///< scid,status,colhead,isLastApprover -- if last na ,set status
		$scid 			= $this->input->post('scid');
		$status 		= $this->input->post('status');
		$colhead 		= $this->input->post('colhead');
		$isLastApprover = $this->input->post('isLastApprover');
		$colstatus 		= substr($colhead,0,-4) . 'status';
		$coldate 		= substr($colhead,0,-4) . 'date';
		$empid 			= ($this->input->post('empid') ? $this->input->post('empid') : $this->input->post('employeeid'));
		$scdate         = ($this->input->post('scdate') ? $this->input->post('scdate') : $this->input->post('service_credit_date_use'));
		$scused			= ($this->input->post('scused') ? $this->input->post('scused') : $this->input->post('service_credit_use'));
		$dated 			= $this->input->post('dated');
		$code_request   = $this->input->post('code_request');
		$user = $this->session->userdata('username');

		$res = $this->service_credit->saveSCUStatusChange($user,$scid,$status,$colstatus,$coldate,$colhead,$isLastApprover,$empid,$scdate,$scused,$dated);

		if(!$isLastApprover && $status == 'APPROVED'){ ///< get next in sequence with same head id
			$arr_aprvl_seq 	= array();
			$setup 			= $this->service_credit->getAppSequence($code_request);
			if($setup->num_rows() > 0){
				$arr_aprvl_seq = $this->service_credit->sortApprovalSeq($setup->row(0));
			}
			$aprvl_count = sizeof($arr_aprvl_seq);
			$prevkey 	 = '';
			$arr_apprv = array();
			foreach ($arr_aprvl_seq as $key => $obj) {
				$isLastApprover_tmp = false;
				$colhead_tmp = $obj['position'];

				if($obj['head_id'] == $user && $colhead_tmp != $colhead){
					if($aprvl_count == $key) 	 $isLastApprover_tmp = true;
					if($key > 1) 				 $prevkey 		 = $key - 1;

					$colstatus_tmp =  $colhead_tmp ? (substr($colhead_tmp,0,-4) . 'status') : '';
					$coldate_tmp =  $colhead_tmp ? (substr($colhead_tmp,0,-4) . 'date') : '';

					if($isLastApprover_tmp){
						if($arr_aprvl_seq[$prevkey]['head_id'] == $user){
							$prev_colhead = $arr_aprvl_seq[$prevkey]['position'];
							$res_tmp = $this->service_credit->saveSCUStatusChange($user,$scid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$empid,$scdate,$scused,$dated,$prev_colhead);
						}
					}else{
						$res_tmp = $this->service_credit->saveSCUStatusChange($user,$scid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$empid,$scdate,$scused,$dated);
					}

				}
			}

		}


		if($res) echo json_encode(array('err_code'=>0,'msg'=>"Success! Status now is : $status"));
		else 	 echo json_encode(array('err_code'=>2,'msg'=>'Failed to set status.'));


		// if($res) echo "Success! Status now is : $status";
		// else 	 echo 'This employee has no service credit available on this specific date!.';
	}

	function displayRecountSC(){
		$employeeid = $this->input->post("employeeid");
		$data = array(
			"sc-credit" => 0,
			"sc-avail" => 0,
			"sc-balance" => 0
		);

		list($data["sc-credit"], $data["sc-avail"], $data["sc-balance"]) = $this->service_credit->getRecountSC($employeeid);

		foreach ($data as $key => $value) $data[$key] = number_format($value, 2);
		echo json_encode($data);
	}

	function displayAvailableSC(){
		$data = array();
		$data = $this->input->post();

		$data["sc_list"] = array();
		$q_service_credit = $this->service_credit->getServiceCredit($data["employeeid"])->result();
		foreach ($q_service_credit as $row) {
			$data["sc_list"][$row->id] = array(
				"caption" => date("F d, Y", strtotime($row->date)),
				"credit"  => ($row->total_sc) ? $row->total_sc : 0,
				"avail"   => ($row->used_sc) ? $row->used_sc : 0,
				"balance" => ($row->available_sc) ? $row->available_sc : 0
			);
		}
		
		$this->load->view("employee/available_sc_table", $data);
	}
	
	function newSCDate(){
		$data = array();
		$data = $this->input->post();

		$this->load->view("employee/sc_modal", $data);
	}

	function saveSCDate(){
		$data = array();
		$data = $this->input->post();

		$this->load->library('extras_lib');
		$this->load->model('attcompute');
		$fdate 	 	= $data["dfrom"];
		$tdate 	 	= ($data["dto"]) ? $data["dto"] : $data["dfrom"];
		$empid 	 	= $data["empid"];
		$isHalfDay 	= (isset($data["halfday"])) ? true : false;
		$day_list	= array();
		$arr_days	= array(
			"Monday" 	=> "M",
			"Tuesday" 	=> "T",
			"Wednesday" => "W",
			"Thursday" 	=> "TH",
			"Friday" 	=> "F",
			"Saturday" 	=> "S",
			"Sunday" 	=> "SD"
		);

		$days_arr = $this->extras_lib->getDateIncluded($fdate, $tdate);

        foreach ($days_arr as $date) {
        	$day_list[$date] = 0;
        	$dayofweek 		 = $arr_days[date("l", strtotime($date))]; 

        	$q_avail_date = $this->service_credit->checkSCDateAvailabilityWithHoliday($date, $empid);
        	
        	// walang sched
        	if(count($q_avail_date) == 0) $day_list[$date] = (($isHalfDay) ? 0.5 : 1);

        	// kapag may sched at halfday
        	if(count($q_avail_date) > 0 && $isHalfDay){
        		$isHalfDayResult = $this->service_credit->getEmpSchedIsHalfDay($empid, $date, $dayofweek);
        		$day_list[$date] = (($isHalfDayResult) ? 0.5 : 0);
        	}

        	// kpag yung days ay holiday...
        	$office = $this->service_credit->getEmpDept($empid);
        	$isHoliday = $this->attcompute->isHolidayNew($empid, $date, $office);
        	if($isHoliday) $day_list[$date] = (($isHalfDay) ? 0.5 : 1);

        }

        $response = "";
        $day_list = $this->service_credit->getListOfAvailableSCDate($empid, $fdate, $tdate, $day_list);
        foreach ($day_list as $date => $value) {
        	if($value > 0){
        		$data_insert = array(
        			"employeeid" 	=> $empid,
        			"date" 		 	=> date("Y-m-d", strtotime($date)),
        			"total_sc"	 	=> $value,
        			"used_sc" 	 	=> 0,
        			"available_sc"	=> $value
        		);

        		$q_save = $this->service_credit->saveSCDate($data_insert);
        		if(!$q_save) $response = "Failed to save Service Credit Date";
        	}
        }

        if(!$response) $response = "Successfully saved.";
        echo $response;
	}

	function deleteSCDate(){
		$id = $this->input->post("id");

		$response = "";
		$q_delete = $this->service_credit->deleteSCDate($id);
		if(!$q_delete) $response = "Failed to delete Service Credit Date";

		if(!$response) $response = "Successfully delete.";
        echo $response;
	}

	function updateSCUse($code, $sc_request, $remarks){
		$date = $ishalfday = $scdate = $sc_days = "";
		foreach($sc_request as $key => $value){
			$date = $value['date'];
			$ishalfday = $value['ishalfday'];
			$scdate = $value['scdate']."/";
			$sc_days = $value['sc']."/";
		}
		
		$response = $this->service_credit->updateSCUse($date, $ishalfday, $scdate, $sc_days, $code, $remarks);
		echo $response;
	}

	function updateSCUseAdmin($code, $date, $remark, $scdate, $sc, $sched_affected, $ishalfday){
		$response = $this->service_credit->updateSCUse($date, $ishalfday, $scdate, $sc, $code, $remark);
		if($response) return $response;
	}

	function validateSCRequest(){
		$employeeid = $this->input->post('employeeid');
		$result = 0;
		$result = $this->service_credit->getAvailableSCBalances($employeeid);
		echo $result;
	}

	function validateSCURequest(){
		$employeeid = $this->input->post('employeeid');
		$result = 0;
		$result = $this->service_credit->getAvailableSCUBalances($employeeid);
		echo $result;
	}
}