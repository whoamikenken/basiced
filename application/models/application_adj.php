<?php 
/**
 * @author Angelica Arangco
 * @copyright 2018
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Application_adj extends CI_Model {

	function getCutoffData(){
		$processed_cutoff_arr = $dtr_cutoff_arr = $payroll_cutoff_arr = array();
		$payroll_cutoff = array();

		$processed_cutoff_q = $this->db->query("SELECT payroll_cutoffstart ,payroll_cutoffend, status FROM attendance_confirmed_nt GROUP BY payroll_cutoffstart,payroll_cutoffend;");

		foreach ($processed_cutoff_q->result() as $key => $row) {
			array_push($processed_cutoff_arr, $row->payroll_cutoffstart.'|'.$row->payroll_cutoffend);
			if($row->status == "PROCESSED") array_push($payroll_cutoff, $row->payroll_cutoffstart.'|'.$row->payroll_cutoffend);
		}

		$payroll_cutoff_q = $this->db->query("SELECT b.startdate,b.enddate, a.CutoffFrom, a.CutoffTo, a.ID AS dtr_cutoff_id, b.id AS payroll_cutoff_id FROM cutoff a INNER JOIN payroll_cutoff_config b ON a.id=b.baseid");
		// echo "<pre>"; print_r($payroll_cutoff); die;
		foreach ($payroll_cutoff_q->result() as $key => $row) {
			$payroll_cutoff_str = $row->startdate.'|'.$row->enddate;

			if(in_array($payroll_cutoff_str, $payroll_cutoff)){
				$payroll_cutoff_arr[$row->payroll_cutoff_id] = $payroll_cutoff_str;
			}

			if(!in_array($payroll_cutoff_str, $processed_cutoff_arr)){
				// $payroll_cutoff_arr[$row->payroll_cutoff_id] = $payroll_cutoff_str;
			}else{
				$dtr_cutoff_arr[$row->dtr_cutoff_id] = $row->CutoffFrom.'|'.$row->CutoffTo;
			}
		}
		return array($dtr_cutoff_arr,$payroll_cutoff_arr);
	}

	function getPayrollProcessedTime($p_start='',$p_end=''){
		$payroll_time = ''; ///< get last processed time
		// $payroll_time_q = $this->db->query("SELECT `dateprocessed` FROM payroll_computed_table WHERE cutoffstart='$p_start' AND cutoffend='$p_end' ORDER BY `dateprocessed` DESC LIMIT 1");
		$payroll_time_q = $this->db->query("SELECT `timestamp` FROM attendance_confirmed_nt WHERE cutoffstart='$p_start' AND cutoffend='$p_end' ORDER BY `timestamp` DESC LIMIT 1");

		// if($payroll_time_q->num_rows() > 0) $payroll_time = $payroll_time_q->row(0)->dateprocessed;
		if($payroll_time_q->num_rows() > 0) $payroll_time = $payroll_time_q->row(0)->timestamp;

		return $payroll_time;
	}

	function getEmpSalary($empid='',$date=''){
		$daily = $hourly = $lechour = $labhour = 0;
		$sal_q = $this->db->query("SELECT daily, hourly, lechour, labhour FROM payroll_employee_salary WHERE employeeid='$empid' AND `timestamp` <= '$date'");

		if($sal_q->num_rows() == 0)
			$sal_q = $this->db->query("SELECT daily, hourly, lechour, labhour FROM payroll_employee_salary_history WHERE employeeid='$empid' AND `timestamp` <= '$date' ORDER BY `timestamp` DESC LIMIT 1");
		if($sal_q->num_rows() > 0){
			$sal = $sal_q->row(0);
			$daily = $sal->daily;
			$hourly = $sal->hourly;
			$lechour = $sal->lechour;
			$labhour = $sal->labhour;
		}

		$list = array('daily'=>$daily,'hourly'=>$hourly,'lechour'=>$lechour,'labhour'=>$labhour);

		return $list;
	}


	################################## LEAVE / OB #########################################################

	function getLeaveAdjProcessed($dtr_cutoff_id='',$type='LEAVE'){
		$adj_list = $processed_request_ids = array();

		$tbl = 'leave_adjustment';
		if($type=='OB') $tbl = 'ob_adjustment';

		$adj_q = $this->db->query("SELECT a.*, REPLACE(CONCAT(b.LName,', ',b.FName,' ',b.MName), 'Ã‘', 'Ñ') as fullname , c.startdate, c.enddate
												FROM $tbl a 
												LEFT JOIN employee b ON b.employeeid=a.employeeid
												LEFT JOIN payroll_cutoff_config c ON a.payroll_cutoff_id=c.id
												WHERE a.dtr_cutoff_id='$dtr_cutoff_id';");

		$this->load->model('payrollprocess');

		foreach ($adj_q->result() as $key => $row) {
			$empid = $row->employeeid;

			$income_adj = $this->payrollprocess->constructArrayListFromComputedTable($row->income_adj);

			$adj_list[$empid]['PROCESSED'][$row->id] = array(
														'request_id' 		=> $row->request_id,
														'payroll_cutoff_id' => $row->payroll_cutoff_id,
														'startdate' 		=> $row->startdate,
														'enddate' 			=> $row->enddate,
														'date' 				=> $row->date,
														'total_days' 		=> $row->total_days,
														'amount' 			=> $row->amount,
														'fullname' 			=> $row->fullname,
														'timestamp' 			=> $row->timestamp,
														'income_adj' 		=> $income_adj
														);

			$ids = explode('|', $row->request_id);
			if(!isset($processed_request_ids[$empid])) $processed_request_ids[$empid] = array();
			$processed_request_ids[$empid] = array_unique(array_merge($processed_request_ids[$empid],$ids));
		}

		return array($adj_list, $processed_request_ids);
	}

	function getLeaveAdjPending($payroll_time='',$dtr_start='',$dtr_end='',$processed_request_ids=array(),$type='LEAVE'){
		$wc = " AND a.leavetype != 'ABSENT'";
		if($type=='OB') $wc = " AND a.othertype='DIRECT'";

		$leave_q = $this->db->query("SELECT a.*,REPLACE(CONCAT(b.LName,', ',b.FName,' ',b.MName), 'Ã‘', 'Ñ') as fullname
							FROM leave_request a
							INNER JOIN employee b ON b.employeeid=a.employeeid 
							WHERE /*a.`timestamp` > '$payroll_time' AND*/ a.paid='YES' $wc
							AND (a.fromdate BETWEEN '$dtr_start' AND '$dtr_end') AND (a.todate BETWEEN '$dtr_start' AND '$dtr_end')
							");
		if($type=="OB"){
			$leave_q = $this->db->query("SELECT a.*,REPLACE(CONCAT(b.LName,', ',b.FName,' ',b.MName), 'Ã‘', 'Ñ') as fullname
							FROM ob_request a
							INNER JOIN employee b ON b.employeeid=a.employeeid 
							WHERE /*a.`timestamp` > '$payroll_time' AND*/ a.paid='YES' $wc
							AND (a.fromdate BETWEEN '$dtr_start' AND '$dtr_end') AND (a.todate BETWEEN '$dtr_start' AND '$dtr_end') 
							");
		}

		$this->load->model('utils');
		$this->load->model('employee');
		$pending_list = array();
		
		foreach ($leave_q->result() as $key => $row) {
			$ltype = $row->leavetype;
			$othtype = $row->other;
			$request_id = $row->id;
			$base_id = $row->aid;
			$empid = $row->employeeid;

            $no_days = $row->no_days;
            $isHalfDay = $row->isHalfDay;
			
			// $isForAdjustment = $this->checkDateProcessed($empid, $row->date_processed, $dtr_start, $dtr_end);
			$isForAdjustment = $this->checkDateProcessed($empid, $row->timestamp, $dtr_start, $dtr_end);
			if(!$isForAdjustment) continue;
			

            $arr_sched_aff = array(); 
            if($no_days == 0.50 || $isHalfDay){
                $arr_sched_aff = explode(',', $row->sched_affected);
            }
            
			if(isset($processed_request_ids[$empid])) if(in_array($request_id, $processed_request_ids[$empid])) continue;

			///< leave request id list ************************************
			if(!isset($pending_list[$empid][$request_id]['request_ids'])) $pending_list[$empid][$request_id]['request_ids'] = array();
			array_push($pending_list[$empid][$request_id]['request_ids'], $request_id);

			///< total num of days
			if(!isset($pending_list[$empid][$request_id]['total_days'])) $pending_list[$empid][$request_id]['total_days'] = 0;
			$pending_list[$empid][$request_id]['total_days'] += $row->no_days;

			$date_range = $this->utils->getDatesFromRange($row->fromdate,$row->todate);

			$teachingtype = $this->employee->getempdatacol("teachingtype", $empid);

			if(!isset($pending_list[$empid][$request_id]['salary'])) $pending_list[$empid][$request_id]['salary'] = $this->getEmpSalary($empid,$date_range[0]);

			if(!isset($pending_list[$empid][$request_id]['amount'])) $pending_list[$empid][$request_id]['amount'] = 0;
			///< list of dates **********************************************
			foreach ($date_range as $day) {
				if(!isset($pending_list[$empid][$request_id]['dates'])) $pending_list[$empid][$request_id]['dates'] = array();
				array_push($pending_list[$empid][$request_id]['dates'], $day);

				$pending_list[$empid][$request_id]['amount'] += $this->computeLeaveAmount($empid,$day,$no_days,$isHalfDay,$arr_sched_aff,$pending_list[$empid][$request_id]['salary'], $base_id);						

			}

			// $pending_list[$empid][$request_id]['dates'] = array_unique($pending_list[$empid][$request_id]['dates']);
			$pending_list[$empid][$request_id]['fullname'] = $row->fullname;
			$pending_list[$empid][$request_id]['timestamp'] = $row->timestamp;
			$pending_list[$empid][$request_id]['paid'] = $row->paid;
		}
		// echo "<pre>"; print_r($pending_list); die;
		return $pending_list;
	}

	function checkDateProcessed($eid, $date_processed, $date_from, $date_to){
		$isForAdjustment = false;
		$this->load->model("extras");

		$table = "attendance_confirmed";
		$table .= ($this->extras->getemployeecol($eid,"teachingtype") == "teaching") ? "" : "_nt";

		$q_date_processed = $this->db->query("SELECT timestamp FROM $table WHERE employeeid='$eid' AND cutoffstart='$date_from' AND cutoffend='$date_to';")->result();
		/*if($eid == "2014111"){
			echo "<pre>"; print_r($q_date_processed);
			echo "<pre>"; print_r($date_processed); die;
		}*/
		foreach ($q_date_processed as $row) {
			 if($date_processed >= $row->timestamp) $isForAdjustment = true;
		}
		
		return $isForAdjustment;
	}

	function computeLeaveAmount($empid='',$day='',$no_days=0,$isHalfDay=false,$arr_sched_aff=array(),$salary=array(), $base_id=""){
		$amount = 0;
		$hourly = $salary['hourly'];
		$lechour = $salary['lechour'];
		$labhour = $salary['labhour'];

		$sched_q = $this->attcompute->displaySched($empid,$day);
		if($sched_q->num_rows() > 0){
			$sched_count = 0;
			foreach ($sched_q->result() as $s_key => $s_row) {
				$sched_count++;
				$work_lec = $work_lab = $work_admin = 0;
				$stime = $s_row->starttime;
				$etime = $s_row->endtime;

				$leclab = $s_row->leclab;

				if($no_days == 0.50 || $isHalfDay){
					$time_aff = $stime.'|'.$etime;

					$new_time = $this->attcompute->displayLeaveSched($base_id, $day, $sched_count);
              		if($new_time != "|") $time_aff = $new_time;

				    if(in_array($time_aff, $arr_sched_aff)){

				    	$tsched   = round(abs(strtotime($stime) - strtotime($etime)) / 60,2);
				    	$tsched   = $this->time->hoursToMinutes(date('H:i', mktime(0,$tsched)));
				    	if($leclab == 'LEC')       $work_lec =  $tsched;
				    	elseif($leclab == 'LAB')   $work_lab = $tsched;
				    	else 					   $work_admin = $tsched;

				        $amount += (($lechour/60)*$work_lec + ($labhour/60)*$work_lab + ($hourly/60)*$work_admin);
				    }
				}else{

					$tsched   = round(abs(strtotime($stime) - strtotime($etime)) / 60,2);
					$tsched   = $this->time->hoursToMinutes(date('H:i', mktime(0,$tsched)));
					if($leclab == 'LEC')       $work_lec =  $tsched;
					elseif($leclab == 'LAB')   $work_lab = $tsched;
					else 					   $work_admin = $tsched;

				    $amount += (($lechour/60)*$work_lec + ($labhour/60)*$work_lab + ($hourly/60)*$work_admin);

				}
			}
		}
		return $amount;
	}

	################################## CORRECTION #########################################################

	function getCorrectionAdjProcessed($dtr_cutoff_id=''){
		$adj_list = $processed_request_ids = array();

		$adj_q = $this->db->query("SELECT a.*, REPLACE(CONCAT(b.LName,', ',b.FName,' ',b.MName), 'Ã‘', 'Ñ') as fullname , c.startdate, c.enddate
												FROM correction_adjustment a 
												LEFT JOIN employee b ON b.employeeid=a.employeeid
												LEFT JOIN payroll_cutoff_config c ON a.payroll_cutoff_id=c.id
												WHERE a.dtr_cutoff_id='$dtr_cutoff_id';");

		$this->load->model('payrollprocess');
		foreach ($adj_q->result() as $key => $row) {
			$empid = $row->employeeid;

			$income_adj = $this->payrollprocess->constructArrayListFromComputedTable($row->income_adj);

			$adj_list[$empid]['PROCESSED'][$row->id] = array(
														'request_id' 		=> $row->request_id,
														'payroll_cutoff_id' => $row->payroll_cutoff_id,
														'startdate' 		=> $row->startdate,
														'enddate' 			=> $row->enddate,
														'date' 				=> $row->date,
														'total_hours' 		=> $row->total_hours,
														'amount' 			=> $row->amount,
														'fullname' 			=> $row->fullname,
														'timestamp' 			=> $row->timestamp,
														'income_adj' 		=> $income_adj
														);

			$ids = explode('|', $row->request_id);
			if(!isset($processed_request_ids[$empid])) $processed_request_ids[$empid] = array();
			$processed_request_ids[$empid] = array_unique(array_merge($processed_request_ids[$empid],$ids));
		}

		return array($adj_list,$processed_request_ids);
	}

	function getCorrectionAdjPending($payroll_time='',$dtr_start='',$dtr_end='',$processed_request_ids=array()){
		$leave_q = $this->db->query("SELECT a.*,REPLACE(CONCAT(b.LName,', ',b.FName,' ',b.MName), 'Ã‘', 'Ñ') as fullname, b.teachingtype, c.base_id
							FROM ob_request a
							INNER JOIN employee b ON b.employeeid=a.employeeid 
							INNER JOIN ob_app_emplist c ON c.id=a.aid 
							WHERE /*a.`timestamp` > '$payroll_time' AND  AND*/ a.othertype='CORRECTION'
							AND (a.fromdate BETWEEN '$dtr_start' AND '$dtr_end') AND (a.todate BETWEEN '$dtr_start' AND '$dtr_end')
							");

		$this->load->model('utils');
		$pending_list = array();

		foreach ($leave_q->result() as $key => $row) {
			$ltype = $row->leavetype;
			$othtype = $row->other;
			$request_id = $row->id;
			$empid = $row->employeeid;
            $aid = $row->base_id;
            $teachingtype = $row->teachingtype;
            
            $isForAdjustment = $this->checkDateProcessed($empid, $row->dateapproved, $dtr_start, $dtr_end);
			if(!$isForAdjustment) continue;
            
			if(isset($processed_request_ids[$empid])) if(in_array($request_id, $processed_request_ids[$empid])) continue;

			///< leave request id list ************************************
			if(!isset($pending_list[$empid][$request_id]['request_ids'])) $pending_list[$empid][$request_id]['request_ids'] = array();
			array_push($pending_list[$empid][$request_id]['request_ids'], $request_id);

			///< total hours
			if(!isset($pending_list[$empid][$request_id]['total_hours'])) $pending_list[$empid][$request_id]['total_hours'] = '00:00';

			$date_range = $this->utils->getDatesFromRange($row->fromdate,$row->todate);

			$teachingtype = $this->employee->getempdatacol("teachingtype", $empid);

			if(!isset($pending_list[$empid][$request_id]['salary'])) $pending_list[$empid][$request_id]['salary'] = $this->getEmpSalary($empid,$date_range[0]);			
			
			if(!isset($pending_list[$empid][$request_id]['amount'])) $pending_list[$empid][$request_id]['amount'] = 0;

			///< list of dates **********************************************
			foreach ($date_range as $day) {
				if(!isset($pending_list[$empid][$request_id]['dates'])) $pending_list[$empid][$request_id]['dates'] = array();
				array_push($pending_list[$empid][$request_id]['dates'], $day);

				$ti_to_q = $this->db->query("SELECT * FROM leave_app_ti_to WHERE aid='$aid' AND cdate='$day' AND (`status`='UPDATED' OR `status`='NEW')");

				if($ti_to_q->num_rows() > 0){
					foreach ($ti_to_q->result() as $key_tito => $row_tito) {
						$final_time_arr = explode(' - ', $row_tito->request_time);
						if(sizeof($final_time_arr) == 2){

							$timein = $final_time_arr[0];
							$timeout = $final_time_arr[1];

							list($total_min, $amount) = $this->computeCorrectionAmount($empid,$day,$timein,$timeout,$pending_list[$empid][$request_id]['salary'],$teachingtype);

							$total_min = $this->time->hoursToMinutes($pending_list[$empid][$request_id]['total_hours']) + $total_min;
							if($total_min > 480) $total_min = 480;
							$pending_list[$empid][$request_id]['total_hours'] = $this->time->minutesToHours($total_min); 
							$pending_list[$empid][$request_id]['amount'] += $amount;

						}
					}
				}

			}

			// $pending_list[$empid][$request_id]['dates'] = array_unique($pending_list[$empid][$request_id]['dates']);
			$pending_list[$empid][$request_id]['fullname'] = $row->fullname;
			$pending_list[$empid][$request_id]['timestamp'] = $row->timestamp;
			$pending_list[$empid][$request_id]['paid'] = $row->paid;
			
		}

		return $pending_list;
	}


	function computeCorrectionAmount($empid='',$day='',$timein='',$timeout='',$salary=array(),$teachingtype=''){
		$amount = 0;
		$total_min = 0;
		$hourly = $salary['hourly'];
		$lechour = $salary['lechour'];
		$labhour = $salary['labhour'];

		$sched_q = $this->attcompute->displaySched($empid,$day);

		if($sched_q->num_rows() > 0){
			foreach ($sched_q->result() as $s_key => $s_row) {
				$work_lec = $work_lab = $work_admin = 0;
				$stime = $s_row->starttime;
				$etime = $s_row->endtime;

				$leclab = $s_row->leclab;
				$earlyd = $s_row->early_dismissal;
				$tstart = $s_row->tardy_start; 
				$absent_start = $s_row->absent_start;
				$flexi = $s_row->flexible;
				$hours = $s_row->hours;
				$mode = $s_row->mode;

				$absent = $this->attcompute->displayAbsent($stime,$etime,$timein,$timeout,$empid,$day,$earlyd);
				$start = $stime;
				$end   = $etime;

				if(!$absent){
					if($earlyd != null && $earlyd != '00:00:00')						 $absent = strtotime($timeout) < strtotime($earlyd) ? true : false;
				}
				if(!$absent){
					if($absent_start != null && $absent_start != '00:00:00') 			 $absent = strtotime($timein) > strtotime($absent_start) ? true : false;
				}

				if(!$absent){
					if($teachingtype=='teaching'){
						$lateut = $this->attcompute->displayLateUT($stime,$etime,$timein,$timeout,'','',$tstart);
					}elseif($teachingtype=='nonteaching'){
						if($flexi=='YES') $lateut = $this->attcompute->displayLateUTNTFlexi(array($timein,$timeout,''),$hours,$mode);
						else 			  $lateut = $this->attcompute->displayLateUTNT($stime,$etime,$timein,$timeout,'','',$tstart);
					}

					
					if($lateut){
						if(strtotime($timein) > strtotime($tstart)) $start = $timein;
						if(strtotime($etime) > strtotime($timeout)) $end = $timeout;
					}

					$tsched   = round(abs(strtotime($start) - strtotime($end)) / 60,2);
			    	$tsched   = $this->time->hoursToMinutes(date('H:i', mktime(0,$tsched)));
			    	if($leclab == 'LEC')       $work_lec =  $tsched;
			    	elseif($leclab == 'LAB')   $work_lab = $tsched;
			    	else 					   $work_admin = $tsched;

			        $amount += (($lechour/60)*$work_lec + ($labhour/60)*$work_lab + ($hourly/60)*$work_admin);
			        $total_min += $tsched;
				}

			}
		}

		return array($total_min,$amount);
	}		


}