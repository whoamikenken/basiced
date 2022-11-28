<?php 
/**
 * @author Justin
 * @copyright 2015
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Attcompute extends CI_Model {
    
    
    /*
     * Date
     */
    function displayDateRange($dfrom = "",$dto = ""){
        /*
        $query = $this->db->query("SELECT DATE('$dfrom') + INTERVAL A + B + C DAY dte FROM
                                    (SELECT 0 A UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 ) d,
                                    (SELECT 0 B UNION SELECT 10 UNION SELECT 20 UNION SELECT 30 UNION SELECT 40 UNION SELECT 60 UNION SELECT 70 UNION SELECT 80 UNION SELECT 90) m , 
                                    (SELECT 0 C UNION SELECT 100 UNION SELECT 200 UNION SELECT 300 UNION SELECT 400 UNION SELECT 600 UNION SELECT 700 UNION SELECT 800 UNION SELECT 900) Y
                                    WHERE DATE('$dfrom') + INTERVAL A + B + C DAY  <=  DATE('$dto') ORDER BY A + B + C;")->result();
        */
        $query = $this->db->query("SELECT * FROM 
                                    (SELECT ADDDATE('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) dte FROM
                                    (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
                                    (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                                    (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                                    (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
                                    (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
                                    WHERE dte BETWEEN '$dfrom' AND '$dto'")->result();
        return $query;
    }
    
    /*
     * Schedule
     */
    function displaySched($eid="",$date = ""){
        $return = "";
        $query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) /*AND starttime <> '00:00:00'*/ ORDER BY starttime DESC LIMIT 1;");
        if($query->num_rows() > 0){
            #$da = date("Y-m-d",strtotime($query->row(0)->dateactive));
            $da = $query->row(0)->dateactive;
            #$query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND /*DATE(dateactive)='$da'*/ dateactive='$da' GROUP BY starttime,endtime ORDER BY editstamp;");
            $query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE_FORMAT(dateactive,'%Y-%m-%d %H') = DATE_FORMAT('$da','%Y-%m-%d %H') GROUP BY starttime,endtime ORDER BY starttime;"); 
        }
        return $query; 
    }

    
    /*
     * Time-In
     */
    function displayLogTime($eid="",$date="",$tstart="",$tend="",$tbl="",$seq=1){
		
        $return = array("","","");
        if($tbl == "NEW")   $tbl = "timesheet";
        else                $tbl = "timesheet_bak";
		$query = $this->db->query("SELECT timein,timeout,otype FROM $tbl WHERE userid='$eid' AND DATE(timein)='$date' AND TIME(timein)<='$tend' ORDER BY timein ASC LIMIT $seq");
		$seq = $seq - 1;
		if($query->num_rows() > 0){
            // $timein  = $query->row($seq)->timein;
            // $timeout = $query->row($seq)->timeout;
            // $otype   = $query->row($seq)->otype;
			foreach($query->result() as $row)
			{
				array_push($return,array($row->timein,$row->timeout,$row->timein));
			}
        }else{
        	$query = $this->db->query("SELECT logtime FROM timesheet_trail WHERE userid='$eid' AND DATE(logtime)='$date' AND log_type = 'IN' ORDER BY logtime DESC LIMIT $seq");
        	if($query->num_rows() > 0){
	            $timein  = $query->row($seq)->logtime;
	            $return = array($timein,"","");
            }else
                $return = array("","",true);
        }	
        
        return $return;
    }
   
    /*
     * Late & Undertime
     */ 
    // Teaching
    function displayLateUT($stime="",$etime="",$login="",$logout="",$type="",$absent=""){
        $lec = $lab = $tschedlec = $tschedlab = "";
        
        if($login && $logout && !$absent){
            if($login)  $login = date("H:i:s",strtotime($login));
            if($logout) $logout = date("H:i:s",strtotime($logout));
            
            // Late
            $schedstart  = strtotime($stime);
            $logtime     = strtotime($login);
            $late        = round(abs($logtime - $schedstart) / 60,2);
            if($type == "LEC"){
                if( ($login > $stime) && ($late > 15) )   $lec = date('H:i', mktime(0,$late)); 
            }else{
                if( ($login > $stime) && ($late > 15) )   $lab = date('H:i', mktime(0,$late));
            }
            
            // Undertime
            $schedend    = strtotime($etime);
            $logtime     = strtotime($logout);
            $ut          = round(abs($logtime - $schedend) / 60,2);
            if($type == "LEC"){ 
                if($lec){
                    if( $logout < $etime ){
                        $secs  = strtotime($lec)-strtotime("00:00:00");
                        if($secs>0) $lec = date("H:i",strtotime(date('H:i', mktime(0,$ut)))+$secs);
                    }
                }else{
                    if( $logout < $etime )   $lec = date('H:i', mktime(0,$ut));
                }
            }else{
                if($lab){
                    if( $logout < $etime ){
                        $secs  = strtotime($lab)-strtotime("00:00:00");
                        if($secs>0) $lab = date("H:i",strtotime(date('H:i', mktime(0,$ut)))+$secs);
                    }
                }else{
                    if( $logout < $etime )   $lab = date('H:i', mktime(0,$ut));
                }
            }
        }
        
        if($absent){
            // total sched
            $stime    = strtotime($stime);
            $etime    = strtotime($etime);
            $tsched   = round(abs($stime - $etime) / 60,2);
            if($type == "LEC"){
                $tschedlec  = date('H:i', mktime(0,$tsched));
            }else{
                $tschedlab  = date('H:i', mktime(0,$tsched));
            }
        }
            
        return array($lec,$lab,$tschedlec,$tschedlab);
    }
    // Non Teaching
    function displayLateUTNT($stime="",$etime="",$login="",$logout="",$absent="",$ttype="",$tardy=""){
        $lateut = "";
        if($login && $logout && !$absent){
            
            if($login)  $login = date("H:i",strtotime($login));
            if($logout) $logout = date("H:i",strtotime($logout));
            
            // Late
            $schedstart  = strtotime($stime);
            $logtime     = strtotime($login);
            if($login > $stime){
                $lateut        = round(abs($logtime - $schedstart) / 60,2);
                if($ttype){
                    if( $lateut > 15 )   $lateut = date('H:i', mktime(0,$lateut));
                    else                 $lateut = "";
                }else{
                    if( $lateut > round(abs(strtotime($tardy) - $schedstart) / 60,2) )   $lateut = date('H:i', mktime(0,$lateut));
                    else                 $lateut = "";
                }
            }
            
            // Undertime
            $schedend    = strtotime($etime);
            $logtime     = strtotime($logout);
            $ut          = round(abs($logtime - $schedend) / 60,2);
            if(abs($logout) > 0){
                if($lateut){
                    if( $logout < $etime ){
                        $secs  = strtotime($lateut)-strtotime("00:00:00");
                       if($secs>0) $lateut = date("H:i",strtotime(date('H:i', mktime(0,$ut)))+$secs);
                    }
                }else{
                    if( $logout < $etime )   $lateut = date('H:i', mktime(0,$ut));
                }
            }
        }
		if($lateut == "00:00") $lateut = "";
        return $lateut;
    }
    
    /*
     * Absent
     */
    function displayAbsent($stime="",$etime="",$login="",$logout="",$empid="",$dset="",$earlyd=""){
        $absent = "";
        $isteaching = $this->employee->getempteachingtype($empid);
        if($login)  $login = date("H:i:s",strtotime($login));
        if($logout) $logout = date("H:i:s",strtotime($logout));
        $schedend   = strtotime($etime);
        $logtime    = strtotime($login);
        $interval   = round(abs($logtime - $schedend) / 60,2);
        if( $stime && ($interval <= 30 || !$login) && $stime <> '00:00:00'  ) $absent++;
        
        if($empid){
            $query = $this->db->query("SELECT * FROM attendance_absent_checker WHERE employeeid='$empid' AND scheddate = '$dset' AND schedstart = '$stime' AND schedend = '$etime'");
            if($query->num_rows() > 0)  $absent++;
        }
        if($logout <= $stime && !$absent) $absent++;  // log-out <= start of schedule will be marked as absent.
        if(!$absent)if($logout < $earlyd) $absent++;  // log-out <= early dismissal will be marked as absent. 
        if(!$isteaching)    $absent = ($absent/2) ? ($absent/2) : "";
        return $absent;
    }
    
    /*
     * Leave
     */
    function displayLeave($eid="",$date="",$absent=""){
        $sl = $el = $vl = $ol = $oltype = "";
        $query = $this->db->query("SELECT * FROM leave_request WHERE '$date' BETWEEN fromdate AND todate AND employeeid='$eid'");
        if($query->num_rows() > 0){  
            if($query->row(0)->leavetype == "VL" && $query->row(0)->paid == "YES"){       $vl++; $ol = $query->row(0)->leavetype; $oltype = "VACATION";}
            else if($query->row(0)->leavetype == "EL" && $query->row(0)->paid == "YES"){  $el++; $ol = $query->row(0)->leavetype; $oltype = "EMERGENCY";}
            else if($query->row(0)->leavetype == "SL" && $query->row(0)->paid == "YES"){  $sl++; $ol = $query->row(0)->leavetype; $oltype = "SICK";}
            else if($query->row(0)->leavetype == "other" && $query->row(0)->paid == "YES"){  $ol = $query->row(0)->other; $oltype = "OFFICIAL BUSINESS";}
            else                                         {$ol = $query->row(0)->other; $oltype = $query->row(0)->othertype;}
        }
        return array($el,$vl,$sl,$ol,$oltype);
    }
	
	/*
     * Leave
     */
    function displayPendingApp($eid="",$date="",$absent=""){
        $return="";
        $query1 = $this->db->query("SELECT * FROM leave_app WHERE '$date' BETWEEN datefrom AND dateto AND employeeid='$eid' AND status = 'PENDING'");
        if($query1->num_rows() > 0){  
            $return="LEAVE APPLICATION";
        }
		$query2 = $this->db->query("SELECT * FROM seminar_app WHERE '$date' BETWEEN dfrom AND dto AND employeeid='$eid' AND status = 'PENDING'");
        if($query2->num_rows() > 0){  
            $return="SEMINAR APPLICATION";
        }
		$query3 = $this->db->query("SELECT a.*,b.* FROM ot_app_emplist a LEFT JOIN ot_app b ON a.base_id = b.id WHERE '$date' BETWEEN b.dfrom AND b.dto AND a.employeeid='$eid' AND a.status = 'PENDING'");
        if($query3->num_rows() > 0){  
            $return="OVERTIME APPLICATION";
        }
		// $query4 = $this->db->query("SELECT a.*,b.* FROM change_sched_app_emplist a LEFT JOIN change_sched_app_detail b ON a.base_id = b.id WHERE a.employeeid='$eid' AND a.status = 'PENDING'"); //DATENAME(w,$date) = dayofweek
        // if($query4->num_rows() > 0){  
            // $return="OVERTIME APPLICATION";
        // }
		
		
        return $return;
    }
    
    /*
     * Overtime
     */
    function displayOt($eid="",$date=""){
        $otreg = $otsat = $otsun = $othol = "";
        $wdname = date("l",strtotime($date));
        $query = $this->db->query("SELECT a.*,b.* FROM ot_app a LEFT JOIN ot_app_emplist b ON a.id = b.base_id WHERE b.employeeid='$eid' AND '$date' BETWEEN a.dfrom AND a.dto AND status = 'APPROVED'");
        
        if      ($wdname == "Saturday") $otsat = ($query->num_rows() > 0 ? $query->row(0)->total : "");
        else if ($wdname == "Sunday")   $otsun = ($query->num_rows() > 0 ? $query->row(0)->total : "");
        else                            $otreg = ($query->num_rows() > 0 ? $query->row(0)->total : "");
        
        if($this->isHoliday($date)){
            $otreg = $otsat = $otsun = "";
			// $query->result();
			foreach($query->result() as $row)
			{
				$othol = $row->total;
				break;
			
			}
        }
        
        return array($otreg,$otsat,$otsun,$othol);
    }
    
    /*
     * Holiday
     */
    function isHoliday($date=""){
        $sql = $this->db->query("SELECT date_from,date_to FROM code_holiday_calendar WHERE '$date' BETWEEN date_from AND date_to");
        if($sql->num_rows() > 0)  return true;
        else                      return false;
    }
	
	//Added 5-31-17 Holiday With Pay
    function isHolidayWithpay($date=""){
		$return="";
        $sql = $this->db->query("SELECT a.withPay
		FROM code_holiday_type a
		LEFT JOIN code_holidays b ON a.`holiday_type` = b.holiday_type
		LEFT JOIN code_holiday_calendar c ON b.`holiday_id` = c.holiday_id
		WHERE '$date' BETWEEN c.date_from AND c.date_to");
		foreach($sql->result() as $row)
		{
			$return = $row->withPay;
		}
		return $return;
    }
    
    
    /*
     * Attendance Confirmed & Vice Versa
     */
    function att_confirmed($empid="",$date=""){
        $sql = $this->db->query("SELECT * FROM attendance_confirmed WHERE logdate = '$date' AND employeeid='$empid'");
        return $sql;
    }
    
    function att_nt_confirmed($empid="",$date=""){
        $sql = $this->db->query("SELECT * FROM attendance_confirmed_nt WHERE logdate = '$date' AND employeeid='$empid'");
        return $sql;
    }
    
    /*
     *  Total Time 
     */ 
    function exp_time($time) { //explode time and convert into seconds
        $time = explode(':', $time);
		$h = $m = 0;
		if(isset($time[0])) { $h = $time[0];} else{ $h = 0;}
		if(isset($time[1])) { $m = $time[1]; }else {$m = 0;}
        $time = $h * 3600 + $m * 60;
        return $time;
    }
    function sec_to_hm($time) { //convert seconds to hh:mm
        $hour = floor($time / 3600);
        $minute = strval(floor(($time % 3600) / 60));
        if ($minute == 0) {
            $minute = "00";
        } else {
            $minute = $minute;
        }
        $time = $hour . ":" . str_pad($minute,2,'0',STR_PAD_LEFT);
        return $time;
    }

	//Added 6-7-2017 DISPLAY OVERLOAD
	// function displayOverloadTime($stime,$etime,$login,$logout) {
        // $st = $this->exp_time(date("H:i",strtotime($stime)));
        // $et = $this->exp_time(date("H:i",strtotime($etime)));
        // $li = $this->exp_time(date("H:i",strtotime($login)));
        // $lo = $this->exp_time(date("H:i",strtotime($logout)));
		
		// $return =  ($lo - $li) - ($et - $st);
		// $return = $this->sec_to_hm($return);
		// return $return;
    // }
	
	function displayOverloadTime($stime,$etime,$lateutlab) {
        $st = $this->exp_time(date('H:i',strtotime($stime)));
        $et = $this->exp_time(date('H:i',strtotime($etime)));
		$lab = 0;
		
		if($lateutlab)
		{
			$lab = $this->exp_time(date('H:i',strtotime($lateutlab)));
		}
		
		$return =   ($et - $st) - $lab;
		// $return = $this->sec_to_hm($return);
		return $return;
    }
	
		
	//Added 6-7-2017
    function getLastDayOfWeek($eid=""){
        $return = "";
        $query = $this->db->query("SELECT DISTINCT(dayofweek) FROM employee_schedule_history WHERE employeeid = '$eid' ORDER BY idx DESC LIMIT 1")->result();
       
	   switch($query[0]->dayofweek)
	   {
		   case "M": $return = "Monday"; break;
		   case "T": $return = "Thusday"; break;
		   case "W": $return = "Wednesday"; break;
		   case "TH": $return = "Thursday"; break;
		   case "F": $return = "Friday"; break;
		   case "S": $return = "Saturday"; break;
		   case "SUN": $return = "Sunday"; break;
	   }
		
		
        return $return; 
    }
	
	function getFirstDayOfWeek($eid=""){
        $return = "";
        $query = $this->db->query("SELECT DISTINCT(dayofweek) FROM employee_schedule_history WHERE employeeid = '$eid' ORDER BY idx ASC LIMIT 1")->result();
       
	   switch($query[0]->dayofweek)
	   {
		   case "M": $return = "Monday"; break;
		   case "T": $return = "Thusday"; break;
		   case "W": $return = "Wednesday"; break;
		   case "TH": $return = "Thursday"; break;
		   case "F": $return = "Friday"; break;
		   case "S": $return = "Saturday"; break;
		   case "SUN": $return = "Sunday"; break;
	   }
		
		
        return $return; 
    }
	
	
	function getPastDayOverload($eid,$date,$firstDay,$edata){
		
        $return = "";
        $d = date("Y-m-d",strtotime("last ".$firstDay,strtotime($date)));
		
		while ($d != $date){
			$sched = $this->displaySched($eid,$d);
			foreach($sched->result() as $rsched){
				$stime = $rsched->starttime;
                $etime = $rsched->endtime; 
                $type  = $rsched->leclab;
				
				// Holiday
				$holiday = $this->attcompute->isHoliday($d); 
				
				// logtime
				list($login,$logout,$q) = $this->attcompute->displayLogTime($eid,$d,$stime,$etime,$edata);
				
				// Leave
				list($el,$vl,$sl,$ol,$oltype)     = $this->attcompute->displayLeave($eid,$d);
				
				// Absent
				$absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$eid,$d);
				if($oltype == "ABSENT") $absent = $absent;
				else if($el || $vl || $sl || $ol || $holiday) $absent = "";
				
				// Late / Undertime
				list($lateutlec,$lateutlab,$tschedlec,$tschedlab) = $this->attcompute->displayLateUT($stime,$etime,$login,$logout,$type,$absent);
				if($el || $vl || $sl || $ol || $oltype || $holiday){
					$lateutlec = $lateutlab = $tschedlec = $tschedlab = "";
				}
				
				if($holiday)
				{
					if($this->attcompute->isHolidayWithpay($d) == "YES")
					{
						if($tempabsent)
						{
							$absent = 1;
						}
					}
					else
					{
						if(!$login && !$logout)
						{
							$absent = 1;
						}
					}
				}
				else
				{
					$tempabsent = $absent;
				}
				
				if(!$absent && !$lateutlec)
				{
					$return           += $this->attcompute->displayOverloadTime($stime,$etime,$lateutlab);
				}
				else
				{
					$return += 0;
				}
				
			}
			$d = date('Y-m-d',strtotime($d . "+1 days"));
		}
		
        return $return; 
    }
	
	//ADDED 07-06-17 SERVICE CREDIT
	function displayServiceCredit($eid,$date)
	{
		$service_credit = 0;
		$query = $this->db->query("SELECT a.*,b.* FROM sc_app_use a LEFT JOIN sc_app_use_emplist b ON a.id = b.base_id WHERE b.employeeid='$eid' AND a.date = '$date' AND b.status = 'APPROVED'");
		
		if($query->num_rows() > 0){
			foreach($query->result() as $row)
			{
				$service_credit = $row->needed_service_credit;
			}
		}
		
		return $service_credit;
	}
	
	//ADDED 07-15-17 WITH LOG
	function withLog($eid="",$date = ""){
        $return = "";
        $query = $this->db->query("SELECT * FROM timesheet WHERE employeeid = '$eid' AND DATE(timein)  = DATE('$date') AND DATE(timein) = DATE(''_ ORDER BY timein DESC LIMIT 1;");
        
        return $query; 
    }
        
}
?>