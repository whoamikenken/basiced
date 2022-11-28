<?php
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Attendance_ extends CI_Controller {

    public function __construct(){
        parent::__construct();
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
    }

	///< public functions revised for old reports ( process/displayindividuallogs, process/attendance_report_teaching, process/attendance_report_nonteaching )
	public function loadAttendanceReport(){
		$info = array();
        $toks = $this->input->post('toks');
		$dfrom 	= $this->input->get('datesetfrom') ? $this->input->get('datesetfrom') : $this->input->post('datesetfrom');
		$dto 	= $this->input->get('datesetto') ? $this->input->get('datesetto') : $this->input->post('datesetto');
		$empid_post	= $this->input->get('fv') ? $this->input->get('fv') : $this->input->post('fv');
        $edata  = $this->input->get('edata') ? $this->input->get('edata') : $this->input->post('edata');
        $estatus  = $this->input->get('estatus') ? $this->input->get('estatus') : $this->input->post('estatus');
		$isactive 	= $this->input->get('isactive') ? $this->input->get('isactive') : $this->input->post('isactive');
        $tnt    = $this->input->get('tnt') ? $this->input->get('tnt') : $this->input->post('tnt');
        $campus    = $this->input->get('campus') ? $this->input->get('campus') : $this->input->post('campus');
        $reportType    = $this->input->get('reportType') ? $this->input->get('reportType') : $this->input->post('reportType');
        $date_filter_from    = $this->input->get('date_filter_from') ? $this->input->get('date_filter_from') : ($this->input->post('date_filter_from') ? $this->input->post('date_filter_from') : date("Y-m-01"));
        $date_filter_to    = $this->input->get('date_filter_to') ? $this->input->get('date_filter_to') : ($this->input->post('date_filter_to') ? $this->input->post('date_filter_to') : date("Y-m-t"));
		$deptid_post 	= $this->input->get('officeid');
        $deptid    = $this->input->get('deptid');
        $office    = $this->input->get('office');

        $usertype = $this->session->userdata('usertype');
        $userid = $this->session->userdata('username');
        if ($usertype == "EMPLOYEE") {
            $deptid = $office = '';
            $depthead = $this->extensions->checkIfDeptHead($userid);
            $officehead = $this->extensions->checkIfOfficeHead($userid);
            if($depthead){ 
                $deptcodes = $this->extensions->getAllDepartmentUnder($userid);
                $data["dept_keys"] = "'" . implode( "','", $this->db->escape($deptcodes) ) . "'";
                $deptid = $data["dept_keys"];
            }else{
                $deptid = '';
            }

            if($officehead){ 
                $officecodes = $this->extensions->getAllOfficeUnder($userid);
                $officecodes = "'" . implode( "','", $this->db->escape($officecodes) ) . "'";
                $office = $officecodes;
            }else{
                $office = '';
            }
        }


		$deptid_post = $this->input->get('office') ? $this->input->get('office') : $this->input->post('office');
        $campus = $this->input->get('campus') ? $this->input->get('campus') : $this->input->post('campus');
        $reportType = $this->input->get('reportType') ? $this->input->get('reportType') : $this->input->post('reportType');
        
        $dfrom  = $toks ?  $this->gibberish->decrypt( $dfrom, $toks ) : $dfrom;
        $dto  = $toks ?  $this->gibberish->decrypt( $dto, $toks ) : $dto;
        // $dfrom = "2021-03-25";
        // $dto = "2021-03-26";
        $empid_post  = $toks ?  $this->gibberish->decrypt( $empid_post, $toks ) : $empid_post;
        $edata  = $toks ?  $this->gibberish->decrypt( $edata, $toks ) : $edata;
        $isactive  = $toks ?  $this->gibberish->decrypt( $isactive, $toks ) : $isactive;
        $tnt  = $toks ?  $this->gibberish->decrypt( $tnt, $toks ) : $tnt;
        $estatus  = $toks ?  $this->gibberish->decrypt( $estatus, $toks ) : $estatus;
        $deptid_post  = $toks ?  $this->gibberish->decrypt( $deptid_post, $toks ) : $deptid_post;
        $deptid  = $toks ?  $this->gibberish->decrypt( $deptid, $toks ) : $deptid;
        $office  = $toks ?  $this->gibberish->decrypt( $office, $toks ) : $office;
        $campus  = $toks ?  $this->gibberish->decrypt( $campus, $toks ) : $campus;
        $reportType  = $toks ?  $this->gibberish->decrypt( $reportType, $toks ) : $reportType;
        $date_filter_from  = $toks ?  $this->gibberish->decrypt( $date_filter_from, $toks ) : $date_filter_from;
        $date_filter_to  = $toks ?  $this->gibberish->decrypt( $date_filter_to, $toks ) : $date_filter_to;

        if($date_filter_from == "undefined") $date_filter_from = $dfrom;
        if($date_filter_to == "undefined") $date_filter_to = $dto;
        $this->load->model('utils');
        $arr_empids = $this->utils->getEmployeeIDList($deptid_post,'',$tnt,$empid_post,' ORDER BY campus, department, fullname', $campus, $deptid, $office, $isactive, $estatus);
       
           
        // echo "<pre>";print_r($this->db->last_query());die;
        // echo "<pre>";print_r($dto);die;
        $date_range = $this->utils->getDatesFromRange($dfrom, $dto);
        
// echo "<pre>"; print_r($date_range); die;
        $info['emplist_detail'] = array();
        foreach ($arr_empids as $empid => $e_info) {
            #$info['emplist_detail'][$empid]['fullname'] = $this->utils->getFullName($empid);
            $info['emplist_detail'][$empid] = $e_info;
            if(!$tnt) $tnt = $this->employee->getempdatacol('teachingtype',$empid);
			$deptid = $this->employee->getempdatacol('deptid',$empid);

            $fixedday = $this->attcompute->isFixedDay($empid);

			$hasLog = false;
			$firstDate = true;
			foreach ($date_range as $date) {
				$holiday 		= $this->attcompute->isHolidayNew($empid,$date,$deptid ); 

				$holidayInfo 	= $this->attcompute->holidayInfo($date, '',$tnt);
                if(isset($holidayInfo["withPay"])) if($holidayInfo["withPay"]=='NO') $holiday = '';

				$isSuspension 	= $this->isSuspension($holiday,$holidayInfo);

				if($firstDate && $holiday){
                    if($tnt=='teaching'){
					   $hasLog = $this->attendance->checkPreviousSchedAttendanceTeaching($date,$empid);
                    }else{
                        $hasLog = $this->attendance->checkPreviousSchedAttendanceNonTeaching($date,$empid);
                    }
		    		$firstDate = false;
		    	}

				list($info['attendance_list'][$deptid][$empid][$date]['detail'],$hasLog) = $this->getDailyAttendanceDetails($empid,$date,$edata,$hasLog,$isSuspension,$holiday,$tnt,$fixedday, $firstDate);
                $info['attendance_list'][$deptid][$empid][$date]['holidayinfo'] = $holidayInfo;
				$info['attendance_list'][$deptid][$empid][$date]['isHoliday'] = $holiday;
			} ///< end loop dates
		} ///< end loop empids
        // die;
		$info['datedisplay'] = $this->time->createRangeToDisplay($dfrom,$dto);
        $info['empcount'] = sizeof($arr_empids);

        switch ($reportType) {
            case 'ABSENT':
            case 'FAILURETOLOG':
                $info['reportType'] = $reportType;
                $info['date_filter_from'] = $date_filter_from;
                $info['date_filter_to'] = $date_filter_to;
                $info['dfrom'] = $dfrom;
                $info['dto'] = $dto;
                $this->load->view('process/lackingInOut',$info);
                break;
            
            default:
                /*if($tnt=='teaching'){
                  $this->load->view('process/reports_pdf/attendance_detailed',$info);
                }else{*/
                    // echo "<pre>";print_r($info);die;
                  $this->load->view('process/reports_pdf/attendance_detailed_NT',$info);
                // }

                break;
        }
	}

	public function getDailyAttendanceDetails($empid='', $date='',$edata='',$hasLog='',$isSuspension='',$holiday='',$tnt='teaching',$fixedday=TRUE, $firstDate){
		$perday_info = array();
		$sched = $this->attcompute->displaySched($empid,$date);
		$sched_count = $sched->num_rows();

		$isValidSchedule = $this->isValidSchedule($sched);

		if($isValidSchedule){
			$haswholedayleave = false;
			$sched_seq = 1;
            $hasleavecount = 0;
			$hasLogprev = $hasLog;
            $hasLog = false;

		    $isCreditedHoliday = $this->isCreditedHoliday($hasLogprev,$isSuspension);

			foreach($sched->result() as $rsched){
				list($persched_info,$hasLog) = $this->getPerSchedAtendanceDetails($empid,$date,$edata,$rsched,$sched_seq,$isCreditedHoliday,$holiday,$tnt,$fixedday,$hasLog, $firstDate);
				array_push($perday_info, $persched_info);
                $sched_seq++;
			}
		}else{

			$persched_info = $this->getNoSchedAtendanceDetails($empid,$date,$edata);
			array_push($perday_info, $persched_info);
		}

		return array($perday_info,$hasLog);
	}

	public function getPerSchedAtendanceDetails($empid='',$date='',$edata='',$rsched='',$sched_seq=0,$isCreditedHoliday=false,$holiday='',$tnt='teaching',$fixedday=TRUE,$hasLog=false, $firstDate){
		$persched_info = array();
		// $sched_seq++;
		$hasleavecount = 0;
		$haswholedayleave = false;

		$sched_start 	= $rsched->starttime;
		$sched_end 		= $rsched->endtime; 
		$sched_type  	= $rsched->leclab;
		$tardy_start 	= $rsched->tardy_start;
		$absent_start 	= $rsched->absent_start;
		$early_d 		= $rsched->early_dismissal;

		list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$date,true);

		$service_credit = $this->attcompute->displayServiceCredit($empid,$sched_start,$sched_end,$date);

		$cs_app = $this->attcompute->displayChangeSchedApp($empid,$date);

		$pending = $this->attcompute->displayPendingApp($empid,$date);

		if($rsched->flexible != "YES"){
            $log_remarks = '';
			
			list($login,$logout,$log_for) = $this->attcompute->displayLogTime($empid,$date,$sched_start,$sched_end,$edata,$sched_seq,$absent_start,$early_d);
            $log_remarks = (($log_for == "Fingerprint" || $log_for == "Facial" || $log_for == "webcheckin") && ($log_for != 1)) ? strtoupper($log_for) :"";
            // echo "<pre>"; print_r($log_for); die;

			list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count)     = $this->attcompute->displayLeave($empid,$date,"",$sched_start,$sched_end);

	        $absent = $this->attcompute->displayAbsent($sched_start,$sched_end,$login,$logout,$empid,$date,$early_d);

	        if($holiday && $isCreditedHoliday) $absent = "";
	        if ($vl >= 1 || $el >= 1 || $sl >= 1 || $ob >= 1 || $ol >= 1 || $service_credit >= 1){
                $absent = "";
                $haswholedayleave = true;
            }
            if ($vl > 0 || $el > 0 || $sl > 0 || $ol > 0 || $ob > 0 || $service_credit > 0){
            	$absent = "";
                $hasleavecount++;
            }
            if($abs_count >= 1) $haswholedayleave = true;
            $ob_data = $this->attcompute->displayLateUTAbs($empid, $date);
            if($tnt=='teaching'){
                list($lateutlec,$lateutlab,$lateutadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUT($sched_start,$sched_end,$tardy_start,$login,$logout,$sched_type,$absent);
                list($utlec,$utlab,$utadmin) = $this->attcompute->computeUndertime($sched_start,$sched_end,$tardy_start,$login,$logout,$sched_type,$absent);
            }else{
                $lateutlab = $lateutadmin = $utlab = $utadmin = $tschedlec = $tschedlab = $tschedadmin = '';
                $lateutlec = $this->attcompute->displayLateUTNT($sched_start,$sched_end,$login,$logout,$absent,'',$tardy_start);
                $utlec  = $this->attcompute->computeUndertimeNT($sched_start,$sched_end,$login,$logout,$absent,'',$tardy_start);
            }


            if($el || $vl || $sl || $ob || $service_credit || ($holiday && $isCreditedHoliday)){
                 $lateutlec = $lateutlab = $lateutadmin = $tschedlec = $tschedlab = $tschedadmin = "";
                 $utlec = $utlab = $utadmin = "";
            }

            if($holiday && $isCreditedHoliday){
                $absent = "";
                $holidayInfo = $this->attcompute->holidayInfo($date);
                if($holidayInfo){
                    if($holidayInfo["holiday_rate"] <= 0) $holiday = ''; 
                }
            }

            $is_holiday_valid = $this->attendance->getTotalHoliday($date, $date, $empid);
            if(isset($holidayInfo['description'])){
                
                if(isset($holidayInfo['halfday'])){
                    if($holidayInfo['sched_count'] == "second" && !$firstDate){
                        $lateutlec = $lateutlec;
                        $utlec = $utlec;
                        $absent = '';
                    }else if($holidayInfo['sched_count'] != "second" && $firstDate){
                        $lateutlec = $lateutlec;
                        $utlec = $utlec;
                        $absent = '';
                    }else{
                        $lateutlec = $utlec = $absent =  '';
                    }
                }else{
                    $lateutlec = $utlec = $absent = '';
                }
            }else{
                if($absent){
                    if(!$login && !$logout) $log_remarks = 'NO TIME IN AND OUT';
                    elseif(!$login) $log_remarks = 'NO TIME IN';
                    elseif(!$logout) $log_remarks = 'NO TIME OUT';
                }
            }

            $absent = $this->attcompute->exp_time($absent);
            if($absent >= 14400) $absent = 14400;
            $absent   = ($absent ? $this->attcompute->sec_to_hm($absent) : "");
            
            if($lateutlec && !$utlec){
                if(in_array("late", $ob_data)) $log_remarks = "LATE";
                else{
                    $log_remarks = "LATE";
                    $ob_type = false;
                    $ob_data = array();
                }
            }else if($utlec){
                if(in_array("undertime", $ob_data)) $log_remarks = "UNDERTIME";
                else{
                    $log_remarks = "UNDERTIME";
                    $ob_type = false;
                    $ob_data = array();
                }
            }else if($absent && $log_remarks == ""){
                if(in_array("absent", $ob_data)) $log_remarks = "EXCUSED ABSENT";
                else{
                    if(strtotime($date) < strtotime(date('Y-m-d'))){
                        $log_remarks = "UNEXCUSED ABSENT";
                        $ob_type = false;
                        $ob_data = array();
                    }
                }
            }
                

            $pending_ob = $this->attcompute->displayPendingOBApp($empid,$date);
            $cs_app = $this->attcompute->displayChangeSchedApp($empid,$date);
            $pending = $this->attcompute->displayPendingApp($empid,$date);

            if($pending_ob) $log_remarks .= "<br>".$pending_ob;
            if($cs_app) $log_remarks .= "<br>".$cs_app;
            if($pending) $log_remarks .= "<br>PENDING".$pending;

             if($ol){
                if($oltype){
                    if($oltype == "ABSENT") $log_remarks .= "<br>ABSENT W/ FILE";
                    else $log_remarks .= "<br>".$oltype;
                }else{
                    $log_remarks .= "<br>".$this->employeemod->othLeaveDesc($ol);
                }
            }

            if(isset($holidayInfo['description'])){
                if(isset($holidayInfo['halfday'])){
                    if($holidayInfo['sched_count'] == "second" && !$firstDate){
                        $log_remarks .= "<br>". $holidayInfo['description'];
                    }else if($holidayInfo['sched_count'] != "second" && $firstDate){
                        $log_remarks .= "<br>". $holidayInfo['description'];
                    }
                }else{
                    $log_remarks .= "<br>". $holidayInfo['description'];
                }
            }

            if($ol && $ol != "CORRECTION" ) $login = $logout = "";

            if(($lateutlec || $lateutlab || $lateutadmin) && date("H:i",strtotime($sched_start)) < date("H:i",strtotime($login)) ) $hasLate = 1;
            else 	$hasLate = 0;

            if(($utlec || $utlab || $utadmin) && date("H:i",strtotime($logout)) < date("H:i",strtotime($sched_end)) ) $hasUT = 1;
            else 	$hasUT = 0;

            if(!$fixedday && !$ol) $absent = $tschedlec = $tschedlab = $tschedadmin = '';

            if($tschedlec || $tschedlab || $tschedadmin || $absent) $isAbsent = 1;
            else $isAbsent = 0;

            $persched_info = array(
            		'sched_start' 	=> $sched_start,
            		'sched_end' 	=> $sched_end,
            		'sched_type' 	=> $sched_type,
            		'absent_start' 	=> $absent_start,
            		'early_d' 		=> $early_d,
            		'flexi' 		=> $rsched->flexible,
            		'login' 		=> $login,
            		'logout' 		=> $logout,
            		'lateut_lec' 	=> $lateutlec,
            		'lateut_lab' 	=> $lateutlab,
            		'lateut_admin' 	=> $lateutadmin,
                    'ut_lec'        => $utlec,
                    'ut_lab'        => $utlab,
                    'ut_admin'      => $utadmin,
                    'absent'        => $absent,
            		'deduc_lec' 	=> $tschedlec,
            		'deduc_lab' 	=> $tschedlab,
            		'deduc_admin' 	=> $tschedadmin,
            		'otreg' 		=> $otreg,
            		'otrest' 		=> $otrest,
            		'othol' 		=> $othol,
            		'vl' 			=> $vl,
                    'el'            => $el,
            		'sl' 			=> $sl,
                    'other'         => $ol,
            		'ob' 		=> $ob,
            		'service_credit'=> $service_credit,
            		'cs_app' 		=> $cs_app,
            		'pending' 		=> $pending,
            		'ol' 			=> $ol,
            		'oltype' 			=> $oltype,
            		'hasleavecount' => $hasleavecount ,
            		'haswholedayleave' => $haswholedayleave,
            		'hasLate'		=> $hasLate,
            		'hasUT'			=> $hasUT,
            		'isAbsent'		=> $isAbsent,
                    'log_remarks'      => $log_remarks

            	);




		}else{

			$getLog = $this->attcompute->getLogsPerDay($empid,$date,$edata,true);
            $log = array();
            if(count($getLog) > 1) $log[] = $getLog[0];
            else                   $log = $getLog; 

			list($el,$vl,$sl,$ol,$oltype,$ob)             = $this->attcompute->displayLeave($empid,$date);

            $count_leave = $vl > 0 ? $vl : ( $el > 0 ? $el : ( $sl > 0 ? $sl : ( $ob > 0 ? $ob : ( $service_credit > 0 ? $service_credit : 0 ) ) ) ) ;

            $absent = $this->attcompute->displayAbsentFlexi($log,$rsched->hours,$rsched->mode,$empid,$date,$sched_type,$rsched->breaktime, $count_leave);


            if($holiday && $isCreditedHoliday) $absent = "";

            if ($vl > 0 || $el > 0 || $sl > 0 || $ob > 0 || $ol >= 1 || $service_credit > 0){
                $absent = "";
            }

            if($tnt=='teaching'){
                $lateutlec = $lateutlab = $lateutadmin = '';
                list($utlec,$utlab,$utadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUTFlexi($log,$rsched->hours,$rsched->mode,$sched_type,$absent,$rsched->breaktime, $count_leave);
            }else{
                $lateutlec = $lateutlab = $lateutadmin = $utlab = $utadmin = $tschedlec = $tschedlab = $tschedadmin = '';
                $utlec = $this->attcompute->displayLateUTNTFlexi($log,$rsched->hours,$rsched->mode,$absent,$rsched->breaktime, $count_leave);
            }

            if($el >= 1 || $vl >= 1 || $sl >= 1 || $ob >= 1 || $ol >= 1 || $service_credit >= 1 || ($holiday && $isCreditedHoliday)){
                 $utlec = $utlab = $utadmin = $tschedlec = $tschedlab = $tschedadmin = "";
            }

            $login = $logout = "";
            $arr_logs = array();
            if(count($log) > 0){
                for($i = 0;$i < count($log);$i++){
                    $login = $log[$i][0];
                    $logout = $log[$i][1];

                    if($login=='0000-00-00 00:00:00') $login = "";
                    if($logout=='0000-00-00 00:00:00') $logout = "";

                    array_push($arr_logs, array('login'=>$login,'logout'=>$logout));
                }
            }


            list($logins,$logouts,$log_for) = $this->attcompute->displayLogTime($empid,$date,$sched_start,$sched_end,$edata,$sched_seq,$absent_start,$early_d);
            $log_remarks = (($log_for == "Fingerprint" || $log_for == "Facial" || $log_for == "webcheckin") && ($log_for != 1)) ? strtoupper($log_for) :"";

            

            if($holiday && $isCreditedHoliday){
                $absent = "";
                $holidayInfo = $this->attcompute->holidayInfo($date);
                if($holidayInfo){
                    if($holidayInfo["holiday_rate"] <= 0) $holiday = ''; 
                }
            }

            $is_holiday_valid = $this->attendance->getTotalHoliday($date, $date, $empid);
            if(isset($holidayInfo['description'])){
                
                if(isset($holidayInfo['halfday'])){
                    if($holidayInfo['sched_count'] == "second" && !$firstDate){
                        $lateutlec = $lateutlec;
                        $utlec = $utlec;
                        $absent = '';
                    }else if($holidayInfo['sched_count'] != "second" && $firstDate){
                        $lateutlec = $lateutlec;
                        $utlec = $utlec;
                        $absent = '';
                    }else{
                        $lateutlec = $utlec = $absent =  '';
                    }
                }else{
                    $lateutlec = $utlec = $absent = '';
                }
            }else{
                // $log_remarks = '';
                if($absent){
                    if(!$login && !$logout) $log_remarks = 'NO TIME IN AND OUT';
                    elseif(!$login) $log_remarks = 'NO TIME IN';
                    elseif(!$logout) $log_remarks = 'NO TIME OUT';
                }
            }

            $absent = $this->attcompute->exp_time($absent);
            if($absent >= 14400) $absent = 14400;
            $absent   = ($absent ? $this->attcompute->sec_to_hm($absent) : "");
            if($tnt=='teaching'){
                 if(($lateutlec || $lateutlab || $lateutadmin) && !$absent){
                    if(in_array("undertime", $ob_data)) $log_remarks = "EXCUSED UNDERTIME";
                    else{ 
                            $log_remarks = "UNEXCUSED UNDERTIME";
                            $ob_type = false;
                            $ob_data = array();
                        }
                }

                if($lateutlec || $lateutlab || $lateutadmin && $firstDate){
                    if(in_array("late", $ob_data)) $log_remarks = "EXCUSED LATE";
                    else{
                            $log_remarks = "UNEXCUSED LATE";
                            $ob_type = false;
                            $ob_data = array();
                    }
                }

                if($absent){
                    if(in_array("absent", $ob_data)) $log_remarks = "EXCUSED ABSENT";
                    else{
                        if(strtotime($date) < strtotime(date('Y-m-d'))){
                            $log_remarks = "UNEXCUSED ABSENT";
                            $ob_type = false;
                            $ob_data = array();
                        }
                    }
                }
            }else{
                if($lateutlec && !$utlec){
                    if(in_array("late", $ob_data)) $log_remarks = "LATE";
                    else{
                        $log_remarks = "LATE";
                        $ob_type = false;
                        $ob_data = array();
                    }
                }else if($utlec){
                    if(in_array("undertime", $ob_data)) $log_remarks = "UNDERTIME";
                    else{
                        $log_remarks = "UNDERTIME";
                        $ob_type = false;
                        $ob_data = array();
                    }
                }else if($absent){
                    if(in_array("absent", $ob_data)) $log_remarks = "ABSENT";
                    else{
                        if(strtotime($date) < strtotime(date('Y-m-d'))){
                            $log_remarks = "ABSENT";
                            $ob_type = false;
                            $ob_data = array();
                        }
                    }
                }
            }

            $pending_ob = $this->attcompute->displayPendingOBApp($empid,$date);
            $cs_app = $this->attcompute->displayChangeSchedApp($empid,$date);
            $pending = $this->attcompute->displayPendingApp($empid,$date);

            if($pending_ob) $log_remarks .= "<br>".$pending_ob;
            if($cs_app) $log_remarks .= "<br>".$cs_app;
            if($pending) $log_remarks .= "<br>PENDING".$pending;

             if($ol){
                if($oltype){
                    if($oltype == "ABSENT") $log_remarks .= "<br>ABSENT W/ FILE";
                    else $log_remarks .= "<br>".$oltype;
                }else{
                    $log_remarks .= "<br>".$this->employeemod->othLeaveDesc($ol);
                }
            }

            if(isset($holidayInfo['description'])){
                if(isset($holidayInfo['halfday'])){
                    if($holidayInfo['sched_count'] == "second" && !$firstDate){
                        $log_remarks .= "<br>". $holidayInfo['description'];
                    }else if($holidayInfo['sched_count'] != "second" && $firstDate){
                        $log_remarks .= "<br>". $holidayInfo['description'];
                    }
                }else{
                    $log_remarks .= "<br>". $holidayInfo['description'];
                }
            }

            if(($lateutlec || $lateutlab || $lateutadmin) && date("H:i",strtotime($sched_start)) < date("H:i",strtotime($login)) ) $hasLate = 1;
            else    $hasLate = 0;

            if(($utlec || $utlab || $utadmin) && date("H:i",strtotime($logout)) < date("H:i",strtotime($sched_end)) ) $hasUT = 1;
            else    $hasUT = 0;

            if(!$fixedday && !$ol) $absent = $tschedlec = $tschedlab = $tschedadmin = '';

            if($tschedlec || $tschedlab || $tschedadmin || $absent) $isAbsent = 1;
            else $isAbsent = 0;


        	$persched_info = array(
        			'sched_start' 	=> $sched_start,
        			'sched_end' 	=> $sched_end,
        			'sched_type' 	=> $sched_type,
        			'absent_start' 	=> $absent_start,
        			'early_d' 		=> $early_d,
        			'flexi' 		=> $rsched->flexible,
        			'logs' 			=> $arr_logs,
        			'lateut_lec' 	=> $lateutlec,
        			'lateut_lab' 	=> $lateutlab,
        			'lateut_admin' 	=> $lateutadmin,
                    'ut_lec'        => $utlec,
                    'ut_lab'        => $utlab,
                    'ut_admin'      => $utadmin,
                    'absent'        => $absent,
        			'deduc_lec' 	=> $tschedlec,
        			'deduc_lab' 	=> $tschedlab,
        			'deduc_admin' 	=> $tschedadmin,
        			'otreg' 		=> $otreg,
        			'otrest' 		=> $otrest,
        			'othol' 		=> $othol,
                    'vl'            => $vl,
                    'el'            => $el,
                    'sl'            => $sl,
                    'other'         => $ol,
                    'ob'        => $ob,
                    'service_credit'=> $service_credit,
                    'cs_app'        => $cs_app,
                    'pending'       => $pending,
                    'ol'            => $ol,
                    'oltype'            => $oltype,
                    'hasleavecount' => $hasleavecount ,
                    'haswholedayleave' => $haswholedayleave,
                    'hasLate'       => $hasLate,
                    'hasUT'         => $hasUT,
                    'isAbsent'      => $isAbsent,
                    'log_remarks'      => $log_remarks

        		);
		}

        if(!$hasLog){
            $hasOL = $ol ? ($ol != 'CORRECTION' ? true : false) : false; 
            if((!$tschedadmin && !$absent) || $hasOL) $hasLog = true;
        }

		return array($persched_info,$hasLog);
	}


	public function getNoSchedAtendanceDetails($empid='',$date='',$edata=''){
		$persched_info = array();

		$log = $this->attcompute->getLogsPerDay($empid,$date,$edata);

		list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$date,false);

		$pending = $this->attcompute->displayPendingApp($empid,$date);

		$cs_app = $this->attcompute->displayChangeSchedApp($empid,$date);

		list($el,$vl,$sl,$ol,$oltype,$ob)     = $this->attcompute->displayLeave($empid,$date);

		$service_credit = $this->attcompute->displayServiceCredit($empid,'','',$date);

		$login = $logout = "";
    	$arr_logs = array();
    	if(count($log) > 0){
    		for($i = 0;$i < count($log);$i++){
    			$login = $log[$i][0];
    			$logout = $log[$i][1];

    			if($login=='0000-00-00 00:00:00') $login = "";
				if($logout=='0000-00-00 00:00:00') $logout = "";

				array_push($arr_logs, array('login'=>$login,'logout'=>$logout));
			}
    	}

    	$persched_info = array( 
    				'nosched' 		=> 1,
        			'logs' 			=> $arr_logs,
        			'otreg' 		=> $otreg,
        			'otrest' 		=> $otrest,
        			'othol' 		=> $othol,
        			'vl' 			=> $vl,
        			'sl' 			=> $sl,
        			'other' 		=> $el,
        			'service_credit'=> $service_credit,
        			'cs_app' 		=> $cs_app,
        			'pending' 		=> $pending,
        			'ol' 			=> $ol,
        			'hasLate'		=> 0,
            		'isAbsent'		=> 0

        		);

    	return $persched_info;

	}
	
	public function isValidSchedule($sched=''){
		$isValidSchedule = false;
		if($sched->num_rows() > 0){
			if($sched->row(0)->starttime == "00:00:00" && $sched->row(0)->endtime == "00:00:00") $isValidSchedule = false;
			else $isValidSchedule = true;
		}
		return $isValidSchedule;
	}

	public function isSuspension($holiday='',$holidayInfo=''){
		$isSuspension = false;
		if($holiday){
			if(isset($holidayInfo["holiday_type"]) && $holidayInfo["holiday_type"]=="SUS") $isSuspension = true;
		}
		return $isSuspension;
	}

	public function isCreditedHoliday($hasLogprev='',$isSuspension=''){
		$isCreditedHoliday = false;
		if($hasLogprev || $isSuspension) 	$isCreditedHoliday = true;
		return $isCreditedHoliday;
	}

    # ica-hyperion 21550
    public function loadPrintAttendanceReport(){
      $this->load->model('utils');
      $this->load->library('PdfCreator_tcpdf');
      $this->load->library('PdfCreator_mpdf');
      $folder   = $this->input->get("folder"); 
      $view     = $this->input->get("view");
      $this->load->view("$folder/$view");
    }

    public function validateUnconfirmAttendance(){
        $success_count = $failed_count = 0;
        $response = array();
        $emplist = $this->input->post();
        foreach($emplist as $value){
            $toks = $value["toks"];
            $value["dfrom"] = $this->gibberish->decrypt($value["dfrom"], $toks);
            $value["tnt"] = $this->gibberish->decrypt($value["tnt"], $toks);
            $value["dto"] = $this->gibberish->decrypt($value["dto"], $toks);
            $value["empid"] = $this->gibberish->decrypt($value["empid"], $toks);
            if($value['tnt'] == "teaching") $res = $this->attendance->unconfirmedTeachingEmployeeAttendance($value['dfrom'], $value['dto'],$value['empid']);
            else $res = $this->attendance->unconfirmedNonTeachingEmployeeAttendance($value['dfrom'], $value['dto'],$value['empid']);
            if($res) $success_count+=1;
            else $failed_count+=1;
        }

        $response['msg'] = "Successfully unconfirmed ".$success_count. " employee. ";
        echo json_encode($response);
    }

    public function saveConfirmation(){
        $success_count = $failed_count = $recomputed_emp = $totalcount = 0;
        $res = $tnt = $deptid = $dateresigned = $hold_status = $employeelist = $teachingtype = $cutoff = '';
        $data = array();
        $emplist = $this->input->post();
        $data['total_count'] = count($emplist);
        if($data['total_count'] > 0 && is_array($emplist)){
            $data['employeelist'] = implode(',',array_keys($emplist));
            $arrayData = array_pop(array_reverse($emplist));
            $toks = $arrayData["toks"];
            $data['cutoff'] = $this->gibberish->decrypt($arrayData["dfrom"], $toks).'~|~'.$this->gibberish->decrypt($arrayData["dto"], $toks);
            $data['teachingtype'] = $this->gibberish->decrypt($arrayData["tnt"], $toks);
            echo $this->attendance->saveConfirmationProgress($data);
        }else{
            echo "no_emp";
        }
    }

    public function processConfirmation(){
        $toks = $this->input->post('toks');
        $dfrom = $this->gibberish->decrypt($this->input->post('dfrom'), $toks);
        $dto = $this->gibberish->decrypt($this->input->post('dto'), $toks);
        $tnt = $this->gibberish->decrypt($this->input->post('tnt'), $toks);
        echo $this->attendance->processingConfirmation($tnt, $dto, $dfrom);
    }

    public function validateConfirmAttendance(){
        $this->load->model('utils');
        $success_count = $failed_count = $recomputed_emp = 0;
        $res = $tnt = $deptid = $dateresigned = $hold_status = '';
        $usertype   = $this->session->userdata("usertype");
        $emplist = $this->input->post();
        // echo "<pre>"; print_r(count($emplist)); die;
        foreach ($emplist as $value) {
            $toks = $value["toks"];
            $value["dfrom"] = $this->gibberish->decrypt($value["dfrom"], $toks);
            $value["tnt"] = $this->gibberish->decrypt($value["tnt"], $toks);
            $value["dto"] = $this->gibberish->decrypt($value["dto"], $toks);
            $value["empid"] = $this->gibberish->decrypt($value["empid"], $toks);
            list($dtr_start,$dtr_end,$payroll_start,$payroll_end,$payroll_quarter) = $this->payrolloptions->getDtrPayrollCutoffPair($value['dfrom'],$value['dto']);

            $canConfirm = false;
            $emp_data = $this->utils->getEmployeeInfo('teachingtype,deptid,dateresigned',array('employeeid'=>$value['empid']));
            if($emp_data){
              $tnt          = $value["tnt"];
              $deptid       = $emp_data[0]->deptid;
              $dateresigned = $emp_data[0]->dateresigned;
              $canConfirm   = $this->attendance->empCanConfirmAttendance($payroll_start,$dateresigned);
            }

            if($canConfirm){
                if($value['tnt'] == 'teaching'){
                    $isBED = false;
                    $bed_depts = $this->extensions->getBEDDepartments();
                    if(in_array($deptid, $bed_depts)) $isBED = true;
                    $res = $this->attendance->saveEmployeeAttendanceSummaryTeaching($value['dfrom'],$value['dto'],$payroll_start,$payroll_end,$payroll_quarter,$value['empid'], $isBED, $hold_status, $usertype);
                }elseif($value['tnt'] == 'nonteaching'){
                    $res = $this->attendance->saveEmployeeAttendanceSummaryNonTeaching($value['dfrom'],$value['dto'],$payroll_start,$payroll_end,$payroll_quarter,$value['empid'], $hold_status, $usertype);
                }
            }

            if($res) $success_count++;
            else $failed_count++;

            $recomputed_emp += 1;
            $emplist_total = sizeof($emplist);

            $this->db->query("UPDATE recomputing_percentage SET emp_count = '$recomputed_emp', emp_total = '$emplist_total', success = '$success_count', failed = '$failed_count' WHERE teachingtype = '$tnt' "); 

        }

        if($tnt == "teaching") $this->db->query("UPDATE recomputing_percentage SET emp_count = '0', emp_total = '0', success = '0', failed = '0' WHERE teachingtype = 'teaching' ");
        else $this->db->query("UPDATE recomputing_percentage SET emp_count = '0', emp_total = '0', success = '0', failed = '0' WHERE teachingtype = 'nonteaching' ");

        $response['msg'] = "Successfully confirmed ".$success_count. " employee. ";
        echo json_encode($response);
    }

     public function validateAttendanceConfirmedViewing(){
        $emplist = array();
        $this->load->model("utils");
        $userid = $this->session->userdata('username');
        $data = $this->input->post();
        $deptcodes = "";
        if(isset($data["toks"])){
            $toks = $data["toks"];
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
        list($dfrom, $dto) = explode(",", $data['cutoff']);

    
        $tnt = isset($data['tnt']) ? $data['tnt'] : '';
        $employeeid = $data['employeeid']; isset($data['tnt']) ? $data['tnt'] : '';
        $deptid =  isset($data['deptid']) ? $data['deptid'] : '';
        $office =  isset($data['office']) ? $data['office'] : '';
        $campus = isset($data['campus']) ? $data['campus'] : '';
        $empstat = isset($data['campus']) ? $data['empstat'] : '';
        $data['departments'] = $this->extras->showdepartment();
        $data['dept_list'] = $this->utils->getDepartments();
        $data['dateRange'] = $this->time->createRangeToDisplay($dfrom, $dto);
        $data['showfinalize'] = $this->employeemod->showFinalize($dfrom,$dto,$tnt);

        $depthead = $this->extensions->checkIfDeptHead($userid);
        if($depthead){ 
            $deptcodes = $this->extensions->getAllDepartmentUnder($userid);
            $deptid = "'" . implode( "','", $this->db->escape($deptcodes) ) . "'";
        }

        if($tnt == "teaching"){
            $data['result'] = $this->viewAttendanceConfirmed($dfrom, $dto, $tnt,$employeeid, array(), $campus, $deptid, $office, $empstat);
            $this->load->view("employeemod/viewattconfirm", $data);
        }else{

            $data['result'] = $this->viewAttendanceConfirmedNT($dfrom, $dto, $tnt,$employeeid, array(), $campus, $deptid, $office, $empstat);
            $this->load->view("employeemod/viewattconfirm_nt", $data);
        }
    }

    public function viewAttendanceConfirmed($dfrom, $dto, $tnt,$employeeid,$emplist=array(), $campus="", $deptid = "", $office = "", $empstat=""){
        $totDeduction = $totdeduc = 0;
        $att_list = $this->attendance->emp_confirmed($dfrom, $dto, $tnt,$employeeid, $campus, $deptid, $office, "department", '', $empstat);
        foreach($att_list as $row){
            /*attendance computation*/
            $totLate = $this->attcompute->exp_time($row['latelec']) + $this->attcompute->exp_time($row['latelab']) + $this->attcompute->exp_time($row['lateadmin']);
            $totLate = $this->attcompute->sec_to_hm($totLate);
            $totDeduction = $this->attcompute->exp_time($row['day_absent']);
            /*end*/

            $emplist[$row['qEmpId']] = array(
                'fullname' => $row['qFullname'],
                'department' => $row['qDepartment'],
                'totdeduc' => $totLate,
                'vleave' => $row['vleave'],
                'sleave' => $row['sleave'],
                'oleave' => $row['oleave'],
                'totDeduction' => $row['absent'],
            );

        }
        
        return $emplist;
    }

    public function viewAttendanceConfirmedNT($dfrom, $dto, $tnt,$employeeid,$emplist=array(), $campus="", $deptid = "", $office = "", $empstat = ""){
        $totDeduction = $totdeduc = 0;
        $att_list = $this->attendance->emp_confirmed_nt($dfrom, $dto, $tnt,$employeeid, $campus, $deptid, $office, "department", '', $empstat);
        // echo "<pre>"; print_r($this->db->last_query()); die;
        foreach($att_list as $row){
            /*attendance computation*/
            $tlec = $row["lateut"];
            $tutlec = $row["ut"];
            $tabsent = $row["absent"];
            $lateutdeduc = $this->attcompute->sec_to_hm($this->attcompute->exp_time($tlec) + $this->attcompute->exp_time($tutlec));

            // $lateutdeduc = $row["lateut"];
            // if($tabsent) $tabsent = number_format(($this->attcompute->exp_time($tabsent) / (8 *3600)),2);

            $fixedday = $row['fixedday'];
            $workdays = $row['workdays'];
            /*end*/

            $emplist[$row['qEmpId']] = array(
                'fullname' => $row['qFullname'],
                'department' => $row['qDepartment'],
                'otreg' => $row['otreg'],
                'otrest' => $row['otrest'],
                'othol' => $row['othol'],
                'lateutdeduc' => $lateutdeduc,
                'vleave' => $row['vleave'],
                'sleave' => $row['sleave'],
                'oleave' => $row['oleave'],
                'totDeduction' => $tabsent,
                'fixedday' => $fixedday ? $workdays : $workdays,
                'isholiday' => $row['isholiday']
            );

        }
        return $emplist;
        
    }

    public function loadCutoffAttendance_Summary(){
        $this->load->model("hr_reports");
        $toks         = $this->input->post('toks');
        $cutoff         = ($toks) ? $this->gibberish->decrypt($this->input->post('cutoff'), $toks) :  $this->input->post('cutoff');
        $teachingtype   = ($toks) ? $this->gibberish->decrypt($this->input->post('tnt'), $toks) :  $this->input->post('tnt');
        $employeeid   = ($toks) ? $this->gibberish->decrypt($this->input->post('employeeid'), $toks) :  $this->input->post('employeeid');
        $campus   = ($toks) ? $this->gibberish->decrypt($this->input->post('campus'), $toks) :  $this->input->post('campus');
        $deptid   = ($toks) ? $this->gibberish->decrypt($this->input->post('deptid'), $toks) :  $this->input->post('deptid');
        $empstat   = ($toks) ? $this->gibberish->decrypt($this->input->post('empstat'), $toks) :  $this->input->post('empstat');


        $dates = explode(',',$cutoff);
        if(isset($dates[0]) && isset($dates[1])){
            $sdate = $dates[0];
            $edate = $dates[1];
            list($dtr_start,$dtr_end,$payroll_start,$payroll_end) = $this->payrolloptions->getDtrPayrollCutoffPair($sdate,$edate);
        }else{
            echo 'Invalid Cutoff';
            return;
        }
        ///< get data
        $data['cutoff'] = $cutoff;
        $data['attendance_list'] = $this->hr_reports->getAttConfirmed_summary($teachingtype,$sdate,$edate,$payroll_start,$employeeid,$campus,$deptid, $empstat);
        $this->load->model('utils');
        $data['showfinalize'] = $this->attendance->checkIfHasPendingAttendance($sdate,$edate,$teachingtype,$payroll_start,$payroll_end);
        $data['dept_list'] = $this->utils->getOffice();
        $data['dtr_aimsdept'] = $this->utils->getAIMSDepartment();
        $data['sdate'] = $sdate;
        $data['edate'] = $edate;
        $data['teachingtype'] = $teachingtype;
        $data['dateRange'] = $this->time->createRangeToDisplay($sdate, $edate);
        // echo "<pre>"; print_r($data); die;
        $this->load->view('employeemod/attendance_confirmed',$data);

    }

    public function getAttendanceToday(){
        $this->load->model("leave");
        $this->load->model("ob_application");
        // echo "<pre>";print_r($this->db->last_query());die;
        $present_data = $active_data = $leave_data = $ob_data = $leave_ob_data = $hol_data = $flexiEmployee = $parttimeemp = array();
        $datenow = $this->extensions->getServerTime();
        $datenow = date("Y-m-d", strtotime($datenow));
        // $datenow = "2021-06-01";
        $deptid = $this->extensions->getEmployeeDeparment($this->session->userdata("username"));
        $where_clause = " AND deptid = '$deptid'";
        $active_employee = $this->extensions->getActiveEmployees();
        // echo "<pre>"; print_r(count($active_employee)); die;
        if($active_employee){
            foreach($active_employee as $row){
                $sched = $this->attcompute->displaySched($row['employeeid'],$datenow);
                if($sched->num_rows() > 0 && $sched->row()->flexible == "YES") $flexiEmployee[$row["employeeid"]] = $row["employeeid"];
                $active_data[$row["employeeid"]] = $row["employeeid"];
            }
        }

        $leave_employee = $this->leave->getLeaveTodayEmployees($datenow);
        if($leave_employee){
            foreach($leave_employee as $row){
                $leave_data[$row["employeeid"]] = $row["employeeid"];
                if(in_array($row["employeeid"], $flexiEmployee)) unset($flexiEmployee[$row["employeeid"]]);
            }
        }

        $ob_employee = $this->ob_application->getObTodayEmployees($datenow);
        if($ob_employee){
            foreach($ob_employee as $row){
                $ob_data[$row["employeeid"]] = $row["employeeid"];
                if(in_array($row["employeeid"], $flexiEmployee)) unset($flexiEmployee[$row["employeeid"]]);
            }
        }

        $hol_data = $this->attendance->getEmployeeOnHoliday($datenow);

        // $hol_data = $this->attendance->getEmployeeFlexiSched($datenow);
        if($hol_data){
            foreach($hol_data as $row){
                if(in_array($row["employeeid"], $flexiEmployee)) unset($flexiEmployee[$row["employeeid"]]);
            }
        }

        $parttime = $this->time->getPartTimeEmployees($datenow);
        // echo "<pre>"; print_r($parttime); die;
        if($parttime){
            foreach($parttime as $row){
                if(in_array($row["employeeid"], $flexiEmployee)) unset($flexiEmployee[$row["employeeid"]]);
            }
        }

        $leave_ob_data = array_merge($ob_data, $leave_data);
        
        unset($active_data[""]);
        unset($flexiEmployee[""]);
        $present_employee = $this->time->getPresentEmployee($datenow);
        if($present_employee){
            foreach($present_employee as $row){
                $present_data[$row["userid"]] = $row["userid"];
            }
        }

        $active_data = array_diff_key($active_data, $present_data);
        $active_data = array_diff_key($active_data, $leave_ob_data);
        $active_data = array_diff_key($active_data, $flexiEmployee);
        // echo "<pre>"; print_r($flexiEmployee); die;
        $data["present"] = count($present_data);
        $data["leave_ob"] = count($leave_ob_data);
        $data["absent"] = count($active_data) - count($hol_data);
        $data["holiday"] = count($hol_data);
        $data["flexible"] = count($flexiEmployee);
        $data["pt"] = count($parttime);

        echo json_encode($data);
    }

    public function getPresentAttendanceToday(){
        $datenow = $this->extensions->getServerTime();
        $datenow = date("Y-m-d", strtotime($datenow));
        $deptid = $this->extensions->getEmployeeDeparment($this->session->userdata("username"));
        $where_clause = $last_id = "";
        if($this->session->userdata("usertype") != "ADMIN") $where_clause = " AND deptid = '$deptid'";
        $data["total"] = $this->time->getTotalActiveEmployeeCount();
        $data["late"] = $data["ontime"] = 0;

        $present_employee = $this->time->getPresentEmployee($datenow, $where_clause);
        // echo "<pre>";print_r($present_employee);die;
        if($present_employee){
            foreach($present_employee as $row){
                if($last_id != $row["userid"]){
                    $islate = $this->extensions->getTimeInAccuracy($row["userid"], date("H:i:s", strtotime($row["localtimein"])));
                    if($islate) $data["late"] += 1;
                    else $data["ontime"] += 1;
                    $islate = false;
                }

                $last_id = $row["userid"];
            }
        }
        echo json_encode($data);
    }

    public function getUsageLogin(){
        $deptid = $this->extensions->getUsageLoginData();
        echo json_encode($deptid);
    }

    public function isCutoffExists(){
        $data = $this->input->post();
        $dkey = $data["dkey"];
        $cutofffrom = $data["dfrom"];
        $cutoffto = $data["dto"];
        $payrolldfrom = $data["payrolldfrom"];
        $payrolldto = $data["payrolldto"];
        echo $this->attendance->isCutoffExists($cutofffrom, $cutoffto, $payrolldfrom, $payrolldto, $dkey);
    }

}