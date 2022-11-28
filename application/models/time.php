<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Time extends CI_Model {
   
    /**
    * @author Robert Ram Bolista
    * @copyright ram_bolista@yahoo.com
    * @date 6-26-2014
    * @time 16:56
    */
   
   // Transform hours like "1:45" into the total number of minutes, "105". 
   function hoursToMinutes($hours) 
   { 
       $minutes = 0; 
       if (strpos($hours, ':') !== false) 
       { 
           // Split hours and minutes. 
           list($hours, $minutes) = explode(':', $hours); 
       } 
       return $hours * 60 + $minutes; 
   }
   
   // Transform minutes like "105" into hours like "1:45". 
   function minutesToHours($minutes) 
   { 
       $hours = (int)($minutes / 60); 
       $minutes -= $hours * 60; 
       return sprintf("%d:%02.0f", $hours, $minutes); 
   }   
   
   /**
   * getTardy()
   *
   * return total number of minutes late
   *
   * @param (time) ($timein) employee login
   * @param (time) ($schedulein) employee in-schedule
   * @param (time) ($tardystart) employee starting time of late 
   * @return (time-minute) ($return)
   */
   function getTardy($timein='',$schedulein='',$tardystart=''){
      $return=0;
      $timein = $this->time->hoursToMinutes(date("H:i",strtotime($timein)));
      $schedulein = $this->time->hoursToMinutes(date("H:i",strtotime($schedulein)));
      $tardystart = $this->time->hoursToMinutes(date("H:i",strtotime($tardystart)));
      
      $totalminute_tardy = $schedulein - $tardystart;
      $late = $timein - $schedulein;
      if($late>=$totalminute_tardy) $return = $late;
      if($return<0)$return=0;
      return $return;
   }
   
   // get HalfdayAbsent : Added By Justin : 03/30/2015
   function getHalfAbsent($timein='',$schedulehalfabsent='',$scheduleabsentwhole=''){
    $return = 0;
    // Convert Hour to minutes
    $timein = $this->time->hoursToMinutes(date("H:i",strtotime($timein)));
    $schedulehalfabsent = $this->time->hoursToMinutes(date("H:i",strtotime($schedulehalfabsent)));
    $scheduleabsentwhole = $this->time->hoursToMinutes(date("H:i",strtotime($scheduleabsentwhole)));
    if($timein >= $schedulehalfabsent && $timein < $scheduleabsentwhole){
        $return = 1;
    }
    return $return; 
   }
   
   /**
   * getUnderTime()
   *
   * return total number of minutes undertimed
   *
   * @param (time) ($timeout) employee logout
   * @param (time) ($scheduleout) employee out-schedule
   * @return (time-minute) ($return)
   */
   function getUnderTime($timeout='',$scheduleout=''){
      $return=0;
      if($this->time->hoursToMinutes(date("H:i",strtotime($timeout)))>0){
      $timeout = $this->time->hoursToMinutes(date("H:i",strtotime($timeout)));
      $scheduleout = $this->time->hoursToMinutes(date("H:i",strtotime($scheduleout)));
      $return = $scheduleout - $timeout;   
      }
      
      if($return<0)$return=0;
      return $return;
   }
   
   /**
   * getOverTime()
   *
   * return total number of minutes undertimed
   *
   * @param (time) ($timeout) employee logout
   * @param (time) ($scheduleout) employee out-schedule
   * @param (time) ($timein) employee login
   * @param (time) ($schedulein) employee in-schedule
   * 
   * @return (time-minute) ($return)
   */
   function getOverTime($timeout='',$scheduleout='',$timein='',$schedulein='',$holiday=FALSE){
      $return=0;
      
      if($scheduleout=="" || $holiday){
         $timein = $this->time->hoursToMinutes(date("H:i",strtotime($timein)));
         $timeout = $this->time->hoursToMinutes(date("H:i",strtotime($timeout)));
         $return = $timeout - $timein;
         
      }else{
         if($this->time->hoursToMinutes(date("H:i",strtotime($timeout)))>0){
         $timeout = $this->time->hoursToMinutes(date("H:i",strtotime($timeout)));
         $scheduleout = $this->time->hoursToMinutes(date("H:i",strtotime($scheduleout)));
         $return = $timeout - $scheduleout;   
         }   
      }
      
      
      if($return<0)$return=0;
      return $return;
   }
   
   /**
   * getLeave()
   *
   * return total number of minutes undertimed
   *
   * @param (date) ($date) date of leave
   * @param (varchar) ($employeeid) employee unique identity
   * 
   * @return (boolean) ($return)
   */
   function getLeave($date='',$employeeid='',&$arraytime){
      $return=0;
      $timein="";
      $timeout="";
      $isleave=false;
      $sql = $this->db->query("SELECT * FROM employee_schedule_adjustment WHERE employeeid='$employeeid' AND cdate='$date'")->result();
      $return = count($sql);
      foreach($sql as $row){
          $timein=$row->starttime;
          $timeout=$row->endtime;   
          $isleave=$row->remarks > 0 ? true : false;
      }
      $arraytime = array($timein,$timeout,$isleave);
      return $return;
   }
   
   /**
   * getHoliday()
   *
   * return total number of minutes undertimed
   *
   * @param (date) ($date) date of leave
   * 
   * @return (boolean) ($return)
   */
   function getHoliday($date=''){
      $return=0;
      $timein="";
      $timeout="";
      $sql = $this->db->query("SELECT * FROM code_holiday_calendar WHERE '$date' BETWEEN date_from AND date_to")->result();
      $return = count($sql);
      #$arraytime = array($timein,$timeout);
      return $return;
   }


   // gets the number of minutes tardy for the second half
   function getSecondHalfTardy($timein='', $tardystart = ''){
      $timein = $this->time->hoursToMinutes(date("H:i",strtotime($timein)));
      $tardystart = $this->time->hoursToMinutes(date("H:i",strtotime($tardystart)));
      return ($timein - $tardystart);
   }

   // determines if the actual log is after the time to be late/absent
  function loggedLate($log = '', $lateRef=''){
    $actuallog = strtotime($log);
    $reference = strtotime($lateRef);
    return ($actuallog >= $reference);
   }

   // determines if sched is half day only
   function isSchedHalfDayOnly($schedin='', $schedout){
    $intvalschedin = strtotime($schedin);
    $intvalschedout = strtotime($schedout);
    $diff = $intvalschedout - $intvalschedin;
    return ($diff == 14400);
   }

   // create a display for the range of date covered of the process
   function createRangeToDisplay($from_date = '', $to_date = ''){
    $daterange = "";
    if ( ($from_date != "") && ($to_date != ""))  {
      if((date("Y-m", strtotime($from_date))) == (date("Y-m", strtotime($to_date)))){
        $daterange = date("F d-", strtotime($from_date)) . date("d Y", strtotime($to_date));

      }else if ( (date("Y-m-d", strtotime($from_date))) != (date("Y-m-d", strtotime($to_date))) ) {
        $daterange = date("F d, Y - ", strtotime($from_date)) . date("F d, Y", strtotime($to_date));
      }else{
        $daterange = date("F d, Y", strtotime($from_date));
      }
    }else if ((!$from_date) && ($to_date != "")) {
      $daterange = date("F d, Y", strtotime($to_date));
    }else if (($from_date != "") && (!$to_date)) {
      $daterange = date("F d, Y", strtotime($from_date));
    }
    return $daterange;
   }// end createRangeToDisplay function


   function giveNumDaysCovered($from_date = '', $to_date = ''){
    $diff = date_diff(date_create($from_date),date_create($to_date));
    return intval($diff->format("%a") + 1);
   }

   function toHoursAndMinutes($arr = ''){
      $timedisp = explode(":", $this->time->minutesToHours(array_sum($arr)));

      $hr = (intval($timedisp[0]) > 0) ? intval($timedisp[0]) . ((intval($timedisp[0]) > 1) ? "hrs ":"hr "):"";

      $min = (intval($timedisp[1]) > 0) ? intval($timedisp[1]) . ((intval($timedisp[1]) > 1) ? "mins":"min"):"";

      return ($hr . $min);
   }
  
  function EditedOT($date=""){
    $ot = "";            
    $query = $this->db->query("SELECT * FROM payroll_emp_otaccepted WHERE otdate='$date' ORDER BY id DESC");
    if($query->num_rows() > 0){
        $ot =  $query->row(0)->overtime > 0 ? $query->row(0)->overtime : "";
    }
    return $ot;
  }
  
  function date_range($first, $last, $step = '+1 day', $output_format = 'Y-m-d' ) {

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while( $current <= $last ) {

      $dates[] = date($output_format, $current);
      $current = strtotime($step, $current);
    }

    return $dates;
  }

  function roundOffTime($time){
    $new_time = 0;
    $time= explode(":", $time);
    $hours = $time[0];
    $minutes = $time[1];
    
    if($minutes >= 50){
      $new_time = $hours + 1;
      return $new_time .= ":00";
    }else{
      $new_time = $hours;
      return $new_time .= ":00";
    }

  }

    public function getTotalActiveEmployeeCount(){
        $datenow = date('Y-m-d', strtotime($this->extensions->getServerTime()));
        $q_present = $this->db->query("SELECT count(employeeid) as total FROM employee where '$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL AND isactive = '1'");
        return $q_present->row()->total;
    }

    public function getPresentEmployee($datenow, $empstat=''){
        $where_clause = '';
        if($empstat) $where_clause = " AND b.employmentstat NOT IN ('$empstat') ";
        $q_present = $this->db->query("SELECT * FROM (SELECT userid, timein AS localtimein FROM timesheet WHERE DATE(timein) = '$datenow' AND otype != 'ob'
UNION
SELECT userid, logtime AS localtimein FROM timesheet_trail WHERE DATE(localtimein) = '$datenow' AND log_type = 'IN'
UNION
SELECT userid, localtimein FROM webcheckin_history WHERE DATE(localtimein) = '$datenow' AND log_type = 'IN') a
INNER JOIN employee b ON a.userid = b.employeeid WHERE 1 $where_clause");
        if($q_present->num_rows() > 0) return $q_present->result_array();
        else return false;
    }

    public function getPartTimeEmployees($datenow){
      $parttimer = $this->db->query("SELECT * FROM employee where employmentstat = 'PT' AND ( ( '$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL ) AND isactive = '1' )");
      if($parttimer->num_rows() > 0) return $parttimer->result_array();
        else return false;
    }
     
    public function getPresentEmployeeList(){
      $datenow = date("Y-m-d", strtotime($this->db->query("SELECT CURRENT_TIMESTAMP ")->row()->CURRENT_TIMESTAMP));
      // $datenow = "2021-10-06";
      $q_attendance = $this->db->query("SELECT * FROM (SELECT userid, timein AS localtimein, timeout FROM timesheet WHERE DATE(timein) = '$datenow' AND otype != 'ob'
UNION
SELECT userid, localtimein, localtimein AS timeout FROM timesheet_trail WHERE DATE(localtimein) = '$datenow' AND log_type = 'IN'
UNION
SELECT userid, localtimein, localtimein AS timeout FROM webcheckin_history WHERE DATE(localtimein) = '$datenow' AND log_type = 'IN') a
INNER JOIN employee b ON a.userid = b.employeeid");
      if($q_attendance->num_rows() > 0) return $q_attendance->result_array();
      else return false;
    }

    public function getTimeoutEmployeeList($user_id){
      $datenow = date("Y-m-d", strtotime($this->db->query("SELECT CURRENT_TIMESTAMP ")->row()->CURRENT_TIMESTAMP));
      $q_attendance = $this->db->query("SELECT * FROM login_attempts WHERE DATE(datecreated) = '$datenow' AND status = 'success' AND action = 'OUT' AND user_id = '$user_id' ");
      if($q_attendance->num_rows() > 0) return $q_attendance->row()->datecreated;
      else return "";
    }

    public function getLeaveTodayEmployees($datenow){
      $q_leave = $this->db->query("SELECT DISTINCT * FROM leave_request a INNER JOIN employee b ON a.`employeeid` = b.`employeeid` WHERE '$datenow' BETWEEN fromdate AND todate ");
      if($q_leave->num_rows() > 0) return $q_leave->result_array();
      else return false;
    }

    // public function getObTodayEmployees($datenow){
    //   $q_leave = $this->db->query("SELECT DISTINCT * FROM ob_request a INNER JOIN employee b ON a.`employeeid` = b.`employeeid` WHERE '$datenow' BETWEEN fromdate AND todate ");
    //   if($q_leave->num_rows() > 0) return $q_leave->result_array();
    //   else return false;
    // }

    public function getAbsentEmployeeList($datenow){
      $q_attendance = $this->db->query("SELECT * FROM employee WHERE ( ( '$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL ) AND isactive = '1' ) AND ( employeeid NOT IN (SELECT userid FROM timesheet_trail WHERE log_type = 'IN' AND DATE (localtimein) = '$datenow') AND employeeid NOT IN (SELECT userid FROM webcheckin_history WHERE log_type = 'IN' AND DATE (localtimein) = '$datenow') AND employeeid NOT IN (SELECT userid FROM timesheet WHERE DATE (timein) = '$datenow') AND employeeid NOT IN (SELECT employeeid FROM leave_request WHERE '$datenow' BETWEEN fromdate AND todate) )");
      // if ($q_attendance->num_rows() == 0) {
      //    $q_attendance = $this->db->query("SELECT * FROM webcheckin_history a INNER JOIN employee b ON a.userid = b.employeeid WHERE DATE(localtimein)='$datenow' AND log_type = 'IN' GROUP BY userid ORDER BY a.`localtimein` DESC LIMIT 9");
      // }
      if($q_attendance->num_rows() > 0) return $q_attendance->result_array();
      else return false;
    }

    public function dateDiff($date){
        $date_today = date('Y-m-d', strtotime($this->extensions->getServerTime()));
        $now = date("Y-m-d",strtotime($date_today));
        $diff = strtotime($date) - strtotime($now);
        return abs(round($diff / 86400)); 
    }

    // function getLoginKen($empid, $date, $type){
    //     $login = "";
    //     // echo "<pre>";print_r($date);die;
    //     $tbl = "timesheet";

    //     $q_findLogTime = $this->db->query("SELECT timein,timeout FROM timesheet WHERE userid='$empid' AND DATE_FORMAT(timein, '%Y-%m-%d') = '$date' ORDER BY timein DESC;")->result_array();
    //     if (count($q_findLogTime) == 1) {
    //       if ($type == "IN") {
    //         $login = $q_findLogTime[0]['timein'];
    //       }else{
    //         $login = $q_findLogTime[0]['timeout'];
    //       }
    //     }elseif(count($q_findLogTime) > 1){
    //       if ($type == "IN") {
    //         $login = $q_findLogTime[0]['timein'];
    //       }else{
    //         $q_findLogTime = $this->db->query("SELECT timein,timeout FROM timesheet WHERE userid='$empid' AND DATE_FORMAT(timein, '%Y-%m-%d') = '$date' ORDER BY timeout ASC;")->result_array();
    //         $login = $q_findLogTime[0]['timeout'];
    //       }
    //     }elseif(count($q_findLogTime) == 0){
    //       if ($type == "IN") {
    //         $q_findLogTime = $this->db->query("SELECT localtimein FROM timesheet_trail WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='IN' ORDER BY localtimein DESC;")->result_array();
    //         if (count($q_findLogTime) == 0) {
    //             $q_findLogTime = $this->db->query("SELECT localtimein FROM webcheckin_history WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='IN' ORDER BY localtimein DESC;")->result_array();
    //             if (count($q_findLogTime) > 0) {
    //                 $login = $q_findLogTime[0]['localtimein'];
    //             }
    //         }else{
    //           $login = $q_findLogTime[0]['localtimein'];
    //         }
    //       }else{
    //         $q_findLogTime = $this->db->query("SELECT localtimein FROM timesheet_trail WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='OUT' ORDER BY localtimein ASC;")->result_array();
    //         if (count($q_findLogTime) == 0) {
    //             $q_findLogTime = $this->db->query("SELECT localtimein FROM webcheckin_history WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='OUT' ORDER BY localtimein ASC;")->result_array();
    //             if (count($q_findLogTime) > 0) {
    //                 $login = $q_findLogTime[0]['localtimein'];
    //             }
    //         }else{
    //           $login = $q_findLogTime[0]['localtimein'];
    //         }
    //       }
    //     }
    //     if ($login != "") {
    //       return date('g:i A', strtotime($login));
    //     }else{
    //       return "--:--";
    //     }
        
    // } 

    function getLoginKen($empid, $date, $type){
        $login = "";
        // echo "<pre>";print_r($date);die;
        $tbl = "timesheet";

        $q_findLogTime = $this->db->query("SELECT timein,timeout FROM timesheet WHERE userid='$empid' AND DATE_FORMAT(timein, '%Y-%m-%d') = '$date' ORDER BY timein ASC;")->result_array();
        if (count($q_findLogTime) == 1) {
          if ($type == "IN") {
            $login = $q_findLogTime[0]['timein'];
          }else{
            $login = $q_findLogTime[0]['timeout'];
          }
        }elseif(count($q_findLogTime) > 1){
          if ($type == "IN") {
            $login = $q_findLogTime[0]['timein'];
          }else{
            $q_findLogTime = $this->db->query("SELECT timein,timeout FROM timesheet WHERE userid='$empid' AND DATE_FORMAT(timein, '%Y-%m-%d') = '$date' ORDER BY timeout DESC;")->result_array();
            $login = $q_findLogTime[0]['timeout'];
          }
        }elseif(count($q_findLogTime) == 0){
          if ($type == "IN") {
            $q_findLogTime = $this->db->query("SELECT localtimein FROM timesheet_trail WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='IN' ORDER BY localtimein ASC;")->result_array();
            if (count($q_findLogTime) == 0) {
                $q_findLogTime = $this->db->query("SELECT localtimein FROM webcheckin_history WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='IN' ORDER BY localtimein ASC;")->result_array();
                if (count($q_findLogTime) > 0) {
                    $login = $q_findLogTime[0]['localtimein'];
                }
            }else{
              $login = $q_findLogTime[0]['localtimein'];
            }
          }else{
            $q_findLogTime = $this->db->query("SELECT localtimein FROM timesheet_trail WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='OUT' ORDER BY localtimein DESC;")->result_array();
            if (count($q_findLogTime) == 0) {
                $q_findLogTime = $this->db->query("SELECT localtimein FROM webcheckin_history WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='OUT' ORDER BY localtimein DESC;")->result_array();
                if (count($q_findLogTime) > 0) {
                    $login = $q_findLogTime[0]['localtimein'];
                }
            }else{
              $login = $q_findLogTime[0]['localtimein'];
            }
          }
        }
        if ($login != "") {
          return date('g:i A', strtotime($login));
        }else{
          return "--:--";
        }
        
    }

}
/* End of file time.php */
/* Location: ./application/models/time.php */