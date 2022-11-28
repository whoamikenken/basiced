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
    function displaySchedOLD($eid="",$date = ""){
        $return = "";
        $query = $this->db->query("SELECT * FROM employee_schedule WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateedit) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) /*AND starttime <> '00:00:00'*/ ORDER BY dateedit DESC,starttime DESC LIMIT 1;");
        if($query->num_rows() > 0){
            #$da = date("Y-m-d",strtotime($query->row(0)->dateactive));
            $da = $query->row(0)->dateedit;
            #$query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND /*DATE(dateactive)='$da'*/ dateactive='$da' GROUP BY starttime,endtime ORDER BY editstamp;");
            $query = $this->db->query("SELECT * FROM employee_schedule WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateedit) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE_FORMAT(dateedit,'%Y-%m-%d %H') = DATE_FORMAT('$da','%Y-%m-%d %H') GROUP BY starttime,endtime ORDER BY starttime;"); 
        }
        else
        {
            $query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) /*AND starttime <> '00:00:00'*/ ORDER BY dateactive DESC,starttime DESC LIMIT 1;");
            if($query->num_rows() > 0){
                #$da = date("Y-m-d",strtotime($query->row(0)->dateactive));
                $da = $query->row(0)->dateactive;
                #$query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND /*DATE(dateactive)='$da'*/ dateactive='$da' GROUP BY starttime,endtime ORDER BY editstamp;");
                $query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE_FORMAT(dateactive,'%Y-%m-%d %H') = DATE_FORMAT('$da','%Y-%m-%d %H') GROUP BY starttime,endtime ORDER BY starttime;"); 
            }
        }
        return $query; 
    }

    /*
     * Schedule
     */
    function displaySched($eid="",$date = ""){
        $return = $wc = $where_clause = "";
        $datepos = $this->extensions->getDatePosition($eid);
        $latestda = date('Y-m-d', strtotime($this->extensions->getLatestDateActive($eid)));
        $weekOfMonth = $this->weekOfMonth($date);
        // echo "<pre>"; print_r($weekOfMonth); die;
        if($date >= $latestda) $wc .= " AND DATE(dateactive) = DATE('$latestda')";

        // if($weekOfMonth == 0){
        //   $weekOfMonth = "1";
        // }
        // $weekOfMonth++;
        $query = $this->db->query("SELECT dateactive, weekly_sched FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE('$date') >= DATE('$datepos') $wc ORDER BY dateactive DESC,starttime DESC LIMIT 1;");
        if($query->num_rows() > 0){
            $da = $query->row(0)->dateactive;
            $query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE_FORMAT(dateactive,'%Y-%m-%d %H') = DATE_FORMAT('$da','%Y-%m-%d %H') AND ('$weekOfMonth' IN (SELECT weekly_sched FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE_FORMAT(dateactive,'%Y-%m-%d %H') = DATE_FORMAT('$da','%Y-%m-%d %H')) OR (weekly_sched = 'weekly' OR weekly_sched = '' OR weekly_sched IS NULL)) GROUP BY starttime,endtime ORDER BY starttime;"); 
            // echo "<pre>"; print_r($this->db->last_query()); die;
        }
        return $query; 
    }


    function weekOfMonth($date) {
        $firstDayOfMonth = date("Y-m-01", strtotime($date));
        $dtCurrent          = new \DateTime($date);
        $dtFirstOfMonth     = new \DateTime($firstDayOfMonth);
        $numWeeks           = 1 + ( intval($dtCurrent->format("W")) - 
                                    intval($dtFirstOfMonth->format("W")));

        if($numWeeks == "-50") $numWeeks = 1;
        else if($numWeeks == "-49") $numWeeks = 2;
        else if($numWeeks == "-48") $numWeeks = 3;
        else if($numWeeks == "-47") $numWeeks = 4;
        else if($numWeeks == "-46") $numWeeks = 5;
        return $numWeeks;
    // var_dump($numWeeks);

        // return intval(date("W", strtotime($date))) - intval(date("W", strtotime($firstOfMonth)));
    }

    // function displaySched($eid="",$date = ""){
    //     $return = "";
    //     $query = $this->db->query("SELECT dateactive FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) ORDER BY dateactive DESC,starttime DESC LIMIT 1;");
    //     if($query->num_rows() > 0){
    //         $da = $query->row(0)->dateactive;
    //         $query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND DATE_FORMAT(dateactive,'%Y-%m-%d %H') = DATE_FORMAT('$da','%Y-%m-%d %H') GROUP BY starttime,endtime ORDER BY starttime;"); 
    //     }
    //     return $query; 
    // }

    function scheduleChecker($eid="",$date = ""){
        $return = "";
        $query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) ORDER BY dateactive DESC,starttime DESC LIMIT 1");
        
        if($query->num_rows() > 0){
            $return = "true";
        }else{
          $return = "false";
        }
        return $return; 
    }

    function isFlexible($eid="",$date = ""){
        $return = "";
        $query = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$eid' AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) AND flexible = 'YES' AND hours = 0 ORDER BY dateactive DESC,starttime DESC LIMIT 1");
        
        if($query->num_rows() > 0){
            $return = true;
        }else{
          $return = false;
        }
        return $return; 
    }

    /*
     * Code Schedule 
     */
    function displayCodeSched($eid="",$date = ""){
        $return = "None";
        $query = $this->db->query("SELECT 
                                      E.day_code,
                                      E.day_name,
                                      F.employeeid,
                                      F.description,
                                      F.starttime,
                                      F.endtime,
                                      F.idx,
                                      F.tardy_start,
                                      F.absent_start,
                                      F.tardy_half_start,
                                      F.absent_half_start,
                                      F.no_schedule,
                                      F.half_schedule,
                                      F.early_dismissal,
                                      F.flexible,
                                      F.hours,
                                      F.breaktime
                                    FROM
                                      code_daysofweek AS E
                                    LEFT JOIN
                                      (SELECT 
                                        C.employeeid,
                                        D.*
                                      FROM 
                                        employee AS C
                                      INNER JOIN 
                                        (SELECT 
                                          B.dayofweek,
                                          A.description,
                                          B.schedid,
                                          B.starttime,
                                          B.endtime,
                                          B.idx,
                                          B.tardy_start,
                                          B.absent_start,
                                          B.tardy_half_start,
                                          B.absent_half_start,
                                          B.no_schedule,
                                          B.half_schedule,
                                          B.early_dismissal,
                                          B.flexible,
                                          B.hours,
                                          B.breaktime
                                        FROM 
                                          code_schedule AS A
                                        INNER JOIN code_schedule_detail AS B
                                        ON A.schedid = B.schedid) AS D
                                      ON  C.empshift = D.schedid) AS F
                                    ON E.day_code = F.dayofweek AND F.employeeid = '$eid' WHERE E.day_index = DATE_FORMAT('$date','%w') AND F.starttime >= TIME_FORMAT('$date', '%H:%i:%s')
                                    ORDER BY F.starttime ASC;")->result();
        return $query; 
    }


    function checkWebSetupStatus($id){
        $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$id' AND STATUS = 'active'");
        if($weblogin->num_rows() > 0) return $weblogin->row()->status;
        else return false;
    }
    
    /*
     * Time-In
     */
    function displayLogTime($eid="",$date="",$tstart="",$tend="",$tbl="",$seq=1,$absent_start='',$earlyd='',$earlyd=''){
        $haslog = true;
        $timein = $timeout = $otype = "";

        if($tbl == "NEW")   $tbl = "timesheet";
        else                $tbl = "timesheet_bak";

        /*$wCAbsentEarlyD = '';
        if($absent_start) $wCAbsentEarlyD .= " AND ( TIME(timeout) > '$absent_start' )";
        if($earlyd)       $wCAbsentEarlyD .= " AND ( TIME(timein) < '$earlyd'  )";*/

        $query = $this->db->query("
                SELECT timein,timeout,otype FROM $tbl 
                WHERE userid='$eid' 
                AND ( DATE(timein)='$date' OR DATE(timeout)='$date' ) 
                AND ( TIME(timein)<='$tend' )
                AND ( TIME(timeout) > '$tstart' ) 
                AND timein != timeout
        
                ORDER BY timein ASC LIMIT 1");
        
             // echo "<pre>";print_r($this->db->last_query());
        if($query->num_rows() > 0){

            $seq = $seq - 1;
            $timein  = $query->row($seq)->timein;
            $timeout = $query->row($seq)->timeout;
            $otype   = $query->row($seq)->otype;
            // echo "<pre>"; print_r($timein);die;
        }else{
            $wCAbsentEarlyD = '';
            if($absent_start) $wCAbsentEarlyD .= " AND ( TIME(timeout) > '$absent_start' OR DATE_FORMAT(timeout,'%H:%i:%s') = '00:00:00' )";
            if($earlyd)       $wCAbsentEarlyD .= " AND ( TIME(timein) < '$earlyd' OR DATE_FORMAT(timein,'%H:%i:%s') = '00:00:00' )";

            $query = $this->db->query("
                    SELECT timein,timeout,otype FROM $tbl 
                    WHERE userid='$eid' 
                    AND ( DATE(timein)='$date' OR DATE(timeout)='$date' ) 
                    AND ( TIME(timein)<='$tend' OR  DATE_FORMAT(timein,'%H:%i:%s') = '00:00:00' )
                    AND ( TIME(timeout) > '$tstart' OR DATE_FORMAT(timeout,'%H:%i:%s') = '00:00:00' ) 
                    AND timein != timeout
                    $wCAbsentEarlyD 
                    ORDER BY timein ASC LIMIT 1");
            
           
            if($query->num_rows() > 0){

                $seq = $seq - 1;

                $timein  = $query->row($seq)->timein;
                $timeout = $query->row($seq)->timeout;
                $otype   = $query->row($seq)->otype;
                
            }else{

               $time_logs = array();
               $query = $this->db->query("SELECT FROM_UNIXTIME(FLOOR(a.time/1000)) localtimein FROM facial_Log a
                INNER JOIN facial_person b ON a.personId = b.personId
                WHERE DATE(FROM_UNIXTIME(FLOOR(a.time/1000))) = '$date' AND b.employeeid = '$eid' GROUP BY a.id ORDER BY date ASC LIMIT 2");
              
               /*get facial logs records*/
               if($query->num_rows() > 1){
                 $tap_count = 0;
                 foreach($query->result() as $trow){
                   $time_logs[$tap_count] = $trow->localtimein;
                   $tap_count++;
                 }  
                 if($time_logs[1] < $time_logs[0]){
                  $timein = $time_logs[1];
                  $timeout = $time_logs[0];
                 }else{
                  $timein = $time_logs[0];
                  $timeout = $time_logs[1];
                 }
                 $otype = "Facial";
               }
               else{
                $facial = false;
                 $query = $this->db->query("SELECT localtimein FROM timesheet_trail WHERE userid='$eid' AND DATE(localtimein)='$date' AND log_type = 'IN' ORDER BY localtimein DESC LIMIT $seq");

                 if ($query->num_rows() == 0) {
                     $query = $this->db->query("SELECT localtimein FROM webcheckin_history WHERE userid='$eid' AND DATE(localtimein)='$date' AND log_type = 'IN' ORDER BY localtimein DESC LIMIT $seq");

                     if ($query->num_rows() == 0) {
                         $query = $this->db->query("SELECT FROM_UNIXTIME(FLOOR(a.time/1000)) AS localtimein FROM facial_Log a
                          INNER JOIN facial_person b ON a.personId = b.personId
                          WHERE DATE(FROM_UNIXTIME(FLOOR(a.time/1000))) = '$date' AND b.employeeid = '$eid'; ORDER BY date DESC LIMIT $seq");
                         if($query->num_rows() > 0) $facial = true;
                     }
                 }

                  
                  if($query->num_rows() > 0){
                      $seq = $seq - 1;
                      $timein  = ($facial ?  date("Y-m-d H:i:s", strtotime($query->row($seq)->localtimein)) : $query->row($seq)->localtimein);
                      $timeout = $otype = "";
                      // echo "<pre>";print_r($query->row($seq)->localtimein);
                      // $return = array($timein,"","",$haslog);
                  }else{
                      $haslog = false;
                      $checklog_q = $this->db->query("SELECT timeid FROM $tbl WHERE userid='$eid' AND DATE(timein)='$date'");
                     
                      if($checklog_q->num_rows() > 0) $haslog = true;

                      $timein = $timeout = "";
                      $otype = true;
                       
                  }
                }
            }   

        }
        
        if($timein=='0000-00-00 00:00:00') $timein = "";
        if($timeout=='0000-00-00 00:00:00') $timeout = "";
        $query1 = $this->db->query("SELECT * FROM ob_request WHERE '$date' BETWEEN fromdate AND todate AND employeeid='$eid' AND (ob_type = 'ob' OR ob_type = '') ");
        
        
        if(!$timein && !$timeout && $query1->num_rows() > 0){
          $leave_id = $query1->row()->aid;
          $status = 'APPROVED';
          $leave_q = $this->db->query("SELECT a.base_id FROM ob_app_emplist a INNER JOIN ob_app b ON a.`base_id`=b.`id` WHERE a.id='$leave_id'");
          
          $ishalfday = 0;
          $sched_affected = array();
          $sched_affected_string = '';
            if($leave_q->num_rows() > 0){
              $base_id = $leave_q->row(0)->base_id;
              $timerecord = $this->employeemod->findApplyTimeRecord($base_id);
              if(count($timerecord) > 0){
                foreach ($timerecord as $key => $value) $sched_affected = explode('-', $value->request_time);
                if($sched_affected){
                  $timefrom = $sched_affected[0];
                  $timeto = $sched_affected[1];
                }
                $df = $dt = "";
                $qdate = $this->attcompute->displayDateRange($date, $date);
                foreach($qdate as $rdate){
                  $df = date('Y-m-d', strtotime($rdate->dte)). " ". date('H:i:s',strtotime($timefrom));
                  $dt = date('Y-m-d', strtotime($rdate->dte)). " ". date('H:i:s',strtotime($timeto));
                  $isexist = $this->db->query("SELECT * FROM timesheet where DATE(timein) = '{$rdate->dte}' AND userid = '$eid'");
                  if($isexist->num_rows() == 0 && $timefrom != "00:00:00" && $timeto != "00:00:00"){
                    $this->db->query("INSERT INTO timesheet (`userid`,`timein`,`timeout`,`otype`) VALUES ('".$eid."','".date('Y-m-d H:i:s', strtotime($df))."','".date('Y-m-d H:i:s', strtotime($dt))."','ob')");
                  }
                }
                if($this->db->insert_id()){
                  $query = $this->db->query("
                    SELECT timein,timeout,otype FROM timesheet 
                    WHERE userid='$eid' 
                    AND ( DATE(timein)='$date' OR DATE(timeout)='$date' ) 
                    AND ( TIME(timein)<='$tend' )
                    AND ( TIME(timeout) > '$tstart' ) 
                    AND timein != timeout
                    ORDER BY timein ASC LIMIT 1");
                  if($query->num_rows() > 0){
                        $seq = $seq - 1;
                        $timein  = $query->row($seq)->timein;
                        $timeout = $query->row($seq)->timeout;
                        $otype   = $query->row($seq)->otype;
                  }
              }
                
              }
            }
     
        }

        if($timein=='0000-00-00 00:00:00') $timein = "";
        if($timeout=='0000-00-00 00:00:00') $timeout = "";
        
        return array($timein,$timeout,$otype,$haslog);
    }


    function isFixedDay($empid=''){
        $fixedday = TRUE;
        $fixedday_q = $this->db->query("SELECT fixedday FROM payroll_employee_salary WHERE employeeid='$empid'");
        if($fixedday_q->num_rows() > 0) $fixedday = $fixedday_q->row(0)->fixedday;
        return $fixedday;
    }
   
    /*
     * Late & Undertime
     */ 
    ///< LATE condition (if timein > tardy start, then late = timein - tardy_start)
    // Teaching
    function displayLateUT($stime="",$etime="",$tardy_start='',$login="",$logout="",$type="",$absent=""){
        $lec = $lab = $tschedlec = $tschedlab = $admin = $tschedadmin = "" ;
        if($login)  $login = date("H:i",strtotime($login));
        if($logout) $logout = date("H:i",strtotime($logout));
            
        $schedstart   = strtotime($stime);
        $schedend   = strtotime($etime);
        $schedtardy   = strtotime($tardy_start) - 60;
        
        if($login && $logout && !$absent){
            if($login)  $login = date("H:i:s",strtotime($login));
            if($logout) $logout = date("H:i:s",strtotime($logout));
            
            // Late
            $logtime    = strtotime($login);
            $logouttime    = strtotime($logout);
            
            $late = '';
            if($logtime > $schedtardy) $late        = round(($logtime - $schedstart) / 60,2);

            if($late > 0){
                if($type == 'LEC')       $lec =  $late;
                elseif($type == 'LAB')   $lab = $late;
                else                    $admin = $late;
            }
            
            // Undertime
            $ut='';
            if($logouttime < $schedend) $ut = round(($schedend - $logouttime) / 60,2);
            if($ut > 0){
                if($type == 'LEC')       $lec +=  $ut;
                elseif($type == 'LAB')   $lab += $ut;
                else                    $admin += $ut;
            }
            
        }

        if($type == 'LEC' && $lec)       $lec =  date('H:i', mktime(0,$lec));
        elseif($type == 'LAB' && $lab)   $lab =  date('H:i', mktime(0,$lab));
        elseif($admin)                   $admin =  date('H:i', mktime(0,$admin));
        
        if($absent){
            // total sched
            $tsched   = round(abs($schedstart - $schedend) / 60,2);
            $tsched   = date('H:i', mktime(0,$tsched));
            if($type == 'LEC')       $tschedlec =  $tsched;
            elseif($type == 'LAB')   $tschedlab = $tsched;
            else                    $tschedadmin = $tsched;
        }
         
        return array($lec,$lab,$admin,$tschedlec,$tschedlab,$tschedadmin);
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
            // $schedtardy   = strtotime($tardy) - 60; Comment ko muna to. nagkakalate ako dito
            $schedtardy   = strtotime($tardy) - 60;
             //< get actual tardy start
            // echo $logtime;
            // echo $schedtardy;
            if($logtime > $schedtardy){
                $lateut        = round(($logtime - $schedstart) / 60,2);
                $lateut = date('H:i', mktime(0,$lateut));
            }
            
        }
        if($lateut == "00:00") $lateut = "";
        return $lateut;
    }

    function computeUndertime($stime="",$etime="",$tardy_start='',$login="",$logout="",$type="",$absent=""){
        $lec = $lab = $admin = "" ;
        $schedend   = strtotime($etime);
        
        if($login && $logout && !$absent){
            if($logout) $logout = date("H:i:s",strtotime($logout));
            
            $logouttime    = strtotime($logout);
            
            // Undertime
            $ut='';
            if($logouttime < $schedend) $ut = round(($schedend - $logouttime) / 60,2);
            if($ut > 0){
                if($type == 'LEC')       $lec +=  $ut;
                elseif($type == 'LAB')   $lab += $ut;
                else                    $admin += $ut;
            }
            
        }

        if($lec > 0 && $lec < 1) $lec = 1;
        if($lab > 0 && $lab < 1) $lab = 1;
        if($admin > 0 && $admin < 1) $admin = 1;

        if($type == 'LEC' && $lec)       $lec =  date('H:i', mktime(0,$lec));
        elseif($type == 'LAB' && $lab)   $lab =  date('H:i', mktime(0,$lab));
        elseif($admin)                   $admin =  date('H:i', mktime(0,$admin));
        
        return array($lec,$lab,$admin);
    }

    function computeUndertimeNT($stime="",$etime="",$login="",$logout="",$absent="",$ttype="",$early=""){
        $lateut = "";
        if($login && $logout && !$absent){
            $earlyd = strtotime($early);
            if($login)  $login = date("H:i",strtotime($login));
            if($logout) $logout = date("H:i",strtotime($logout));
            if($earlyd) $earlyd = date("H:i",$earlyd);
            $schedend    = strtotime($etime);
            $logtime     = strtotime($logout);
            
            if(abs($logout) > 0){
                if( $logout < $earlyd ){
                  $ut          = round(abs($schedend - $logtime) / 60,2);
                  $lateut = date('H:i', mktime(0,$ut));
                }
            }
        }
        if($lateut == "00:00") $lateut = "";
        return $lateut;
    }
    
    /*
     * Absent
     */
    function displayAbsent($stime="",$etime="",$login="",$logout="",$empid="",$dset="",$earlyd="", $firstsched="1",$absent_start=""){
        $absent = "";
        $isteaching = $this->employee->getempteachingtype($empid);
        if($login)  $login = date("H:i:s",strtotime($login));
        if($logout) $logout = date("H:i:s",strtotime($logout));
        $earlyd = strtotime($earlyd) + 60;
        $earlyd = date("H:i:s", $earlyd);
        $absent_start = strtotime($absent_start) + 60;
        $absent_start = date("H:i:s", $absent_start);
        $schedstart   = strtotime($stime);
        $schedend   = strtotime($etime);
        $logtime    = strtotime($login);
        $logouttime    = strtotime($logout);
        
        $schedHour = round((abs($logouttime - $logtime) /60)/60,2);
        $interval   = round(abs($schedend - $etime) / 60,2);

        $hours_rendered =  round(abs($schedstart - $logouttime) / 60,2);
        
        $totalHoursOfWork = round(abs($schedend - $schedstart) / 60,2);
        
        if($schedHour <= 2)
        {
            if( $stime && ($interval <= 30 || !$login) && $stime <> '00:00:00'  ) $absent = date('H:i', mktime(0,$totalHoursOfWork));
        }
        else if($schedHour > 2)
        {
            if( $stime && ($interval <= 60 || !$login) && $stime <> '00:00:00'  ) $absent = date('H:i', mktime(0,$totalHoursOfWork));
        }
            

        
        if($empid){
            $query = $this->db->query("SELECT * FROM attendance_absent_checker WHERE employeeid='$empid' AND scheddate = '$dset' AND schedstart = '$stime' AND schedend = '$etime'");
            if($query->num_rows() > 0)  $absent++;
        }

        if($logout <= $stime && !$absent) $absent = date('H:i', mktime(0,$totalHoursOfWork));  // log-out <= start of schedule will be marked as absent.

        if(!$absent){
          if($firstsched){
            if($login > $absent_start) $absent = date('H:i', mktime(0,$totalHoursOfWork));
            // log-out <= early dismissal will be marked as absent. 
          }
          else{
            $totalHoursOfWork = $totalHoursOfWork - $hours_rendered;
            if($logout < $absent_start) $absent = date('H:i', mktime(0,$totalHoursOfWork));
          }
        }  
        // echo "<pre>"; print_r($absent);
        // if(!$isteaching)    $absent = ($absent/2) ? ($absent/2) : "";
        // echo "<pre>"; print_r(date('H:i', mktime(0,$totalHoursOfWork)));

        return $absent;
    }


    
    /*
     * Leave
     */
    //backup
    /*function displayLeave($eid="",$date="",$absent=""){
        $sl = $el = $vl = $ol = $oltype = "";
        $query = $this->db->query("SELECT * FROM leave_request WHERE '$date' BETWEEN fromdate AND todate AND employeeid='$eid'");
        if($query->num_rows() > 0){  
            if($query->row(0)->leavetype == "VL" && $query->row(0)->paid == "YES"){       $vl++; $ol = $query->row(0)->leavetype; $oltype = "VACATION";}
            else if($query->row(0)->leavetype == "EL" && $query->row(0)->paid == "YES"){  $el++; $ol = $query->row(0)->leavetype; $oltype = "EMERGENCY";}
            else if($query->row(0)->leavetype == "SL" && $query->row(0)->paid == "YES"){  $sl++; $ol = $query->row(0)->leavetype; $oltype = "SICK";}
            else if($query->row(0)->leavetype == "other" && $query->row(0)->paid == "YES"){  $ol = $query->row(0)->other; $oltype = "Official Business";}
            else                                         {$ol = $query->row(0)->other; $oltype = $query->row(0)->othertype;}
        }
        return array($el,$vl,$sl,$ol,$oltype);
    }*/
    function displayLeave($eid="",$date="",$absent="",$stime='',$etime='',$sched_count=''){
       $sl = $el = $vl = $ol = $ob = $abs_count = $oltype = $tfrom = $tto = $daterange = $split=$l_nopay="";
        $time_aff = $stime.'|'.$etime;
        $query = $this->db->query("SELECT * FROM leave_request WHERE '$date' BETWEEN fromdate AND todate AND employeeid='$eid' ");
        if($query->num_rows() > 0){  
          foreach($query->result() as $res){
              // $res = $query->row(0);
              $arr_sched_aff = array();
              $no_days = $res->no_days;
              $isHalfDay = $res->isHalfDay;
              $base_id = $res->aid;

              $new_time = $this->displayLeaveSched($base_id, $date, $sched_count);
              if($new_time != "|") $time_aff = $new_time;
              if($isHalfDay && $res->sched_affected){
                  $arr_sched_aff = explode(',', $res->sched_affected);
              }


              if($res->leavetype == "VL" && $res->paid == "YES")
              {     
                  if($isHalfDay && sizeof($arr_sched_aff) > 0){
                      if(in_array($time_aff, $arr_sched_aff)){
                          $vl = $no_days; 
                          $ol = $res->leavetype; 
                          $oltype = $this->employeemod->othLeaveDesc($ol);
                      }
                  }else{
                      $vl = $no_days >= 1 ? 1.00 : $no_days;  
                      $ol = $res->leavetype; 
                      $oltype = $this->employeemod->othLeaveDesc($ol);
                  }  
              }
              else if(strpos($res->leavetype, 'PL-') !== false && $res->paid == "YES")
              {     
                  if($isHalfDay && sizeof($arr_sched_aff) > 0){
                      if(in_array($time_aff, $arr_sched_aff)){
                          $ol = $res->leavetype; 
                          $oltype = $this->employeemod->othLeaveDesc($ol);
                          $ol = 0.5; 
                      }
                  }else{
                      $ol = $res->leavetype; 
                      $oltype = $this->employeemod->othLeaveDesc($ol);
                      $ol = $no_days >= 0 ? 0.5 : $no_days;  
                  }  
              }
              else if($res->leavetype == "EL" && $res->paid == "YES"){  
                  if($isHalfDay){
                      if(in_array($time_aff, $arr_sched_aff)){
                          $vl = $no_days; 
                          $ol = $res->leavetype; 
                          $oltype = $this->employeemod->othLeaveDesc($ol);
                      }
                  }else{
                      $vl = 1.00; 
                      $ol = $res->leavetype; 
                      $oltype = $this->employeemod->othLeaveDesc($ol);
                  }  
              }
              else if($res->leavetype == "other" && $res->paid == "YES"){  
                  if($isHalfDay && sizeof($arr_sched_aff) > 0){
                      if(in_array($time_aff, $arr_sched_aff)){
                          
                          $ol = $res->other; 
                          $oltype = $this->employeemod->othLeaveDesc($ol);
                          $ol = $no_days; 
                      }
                  }else{
                        
                      $ol = $res->other; 
                      $oltype = $this->employeemod->othLeaveDesc($ol);
                      $ol = $no_days >= 1 ? 0.5 : $no_days;
                  }  
              }
              else if($res->leavetype == "SL" && $res->paid == "YES"){  
                  if($isHalfDay && sizeof($arr_sched_aff) > 0){
                      if(in_array($time_aff, $arr_sched_aff)){
                          $sl = 0.5; 
                          $ol = $res->leavetype; 
                          $oltype = $this->employeemod->othLeaveDesc($ol);
                      }
                  }else{
                      $sl = $no_days >= 1 ? 0.5 : $no_days;  
                      $ol = $res->leavetype; 
                      $oltype = $this->employeemod->othLeaveDesc($ol);
                  }  
              }
              else if($res->leavetype == "ABSENT"){  
                  if($isHalfDay && sizeof($arr_sched_aff) > 0){
                      if(in_array($time_aff, $arr_sched_aff)){
                          $sl = $no_days; 
                          $ol = $res->leavetype; 
                          $oltype = $this->employeemod->othLeaveDesc($ol);
                      }
                  }else{
                      $sl = $no_days >= 1 ? 0.5 : $no_days;  
                      $ol = $res->leavetype; 
                      $oltype = $this->employeemod->othLeaveDesc($ol);
                  }  
              }
              else if($res->leavetype == "other"/* && $res->paid == "YES"*/){ 
                  // $othertype = $res->othertype;
                  // if($othertype=='NO PUNCH IN/OUT')   $oltype = 'CORRECTED TIME IN/OUT';
                  // elseif($othertype=='ABSENT')        $oltype = 'ABSENT W/ FILE';
                  // else                                $oltype = "Official Business";
                  $ol = $res->other; 
              }else if($res->leavetype && $res->paid == "NO"){ 
                  if($res->sched_affected) $arr_sched_aff = explode(',', $res->sched_affected);
                  if(sizeof($arr_sched_aff) > 0){
                      if(in_array($time_aff, $arr_sched_aff)){
                          $l_nopay = true;
                          $ol = $res->leavetype; 
                          $oltype = $this->employeemod->othLeaveDesc($ol);
                      }
                  }else{
                    $l_nopay = true;
                    $ol = $res->leavetype; 
                    $oltype = $this->employeemod->othLeaveDesc($ol);
                  }
              }
              else{
                  if($isHalfDay && sizeof($arr_sched_aff) > 0){
                      if(in_array($time_aff, $arr_sched_aff)){
                          
                          $ol = $res->leavetype; 
                          $oltype = $this->employeemod->othLeaveDesc($ol);
                          $ol = 0.5; 
                      }else{
                        if(!$stime && !$etime){
                          $ol = $res->leavetype; 
                          $oltype = $this->employeemod->othLeaveDesc($ol);
                          $ol = 0.5; 
                        }
                      }
                  }else{
                        
                      $ol = $res->leavetype; 
                      $oltype = $this->employeemod->othLeaveDesc($ol);
                      $ol = $no_days >= 1 ? 0.5 : $no_days;
                  }
              }
            }
        }

        if($ol) return array($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$l_nopay);
        
        $query1 = $this->db->query("SELECT * FROM ob_request WHERE '$date' BETWEEN fromdate AND todate AND employeeid='$eid' AND (ob_type = 'ob' OR ob_type = '') ");
        if($query1->num_rows() > 0){  
          // echo "<pre>"; print_r($this->db->last_query());
            // $res = $query1->row(0);

            foreach($query1->result() as $res){
                $arr_sched_aff = array();
                $no_days = $res->no_days;
                $isHalfDay = $res->isHalfDay;
                $arr_sched_aff = explode(',', $res->sched_affected);
                $base_id = $res->aid;

                $new_time = $this->displayOBSched($base_id, $date, $sched_count);
                if($new_time != "|") $time_aff = $new_time;
                if($isHalfDay  && sizeof($arr_sched_aff) > 0){
                    if(in_array($time_aff, $arr_sched_aff)){
                        $othertype = $res->othertype;
                        if($othertype=='DIRECT' && $res->paid == "YES"){
                            if($isHalfDay) $ob = 0.50;
                            else $ob = $no_days;
                        }
                        if($othertype=='CORRECTION')        $oltype = 'CORRECTED TIME IN/OUT';
                        elseif($othertype=='ABSENT')        $oltype = 'ABSENT W/ FILE';
                        else                                $oltype = "Official Business";
                        $ol = $othertype;
                    }else{
                      if(!$stime && !$etime){
                        $othertype = $res->othertype;
                        $obtype = "";
                        if($othertype=='CORRECTION')        $obtype = 'CORRECTED TIME IN/OUT';
                        elseif($othertype=='ABSENT')        $obtype = 'ABSENT W/ FILE';
                        else                                $obtype = "Official Business";
                        if($ol) $oltype .= ", ".$obtype;
                        else $oltype .= $obtype;
                      }
                    }
                }else{
                    $othertype = $res->othertype;
                    if($othertype=='DIRECT' && $res->paid == "YES"){
                        if($isHalfDay) $ob = 0.50;
                        else $ob = 1.00; 
                    }
                    if($othertype=='CORRECTION')        $oltype = 'CORRECTED TIME IN/OUT';
                    elseif($othertype=='ABSENT')        $oltype = 'ABSENT W/ FILE';
                    else                                $oltype = "Official Business";
                    $ol = $othertype;
                }
            }
        }else{
          // $teachingtype = $this->extensions->getEmployeeTeachingType($eid);
          

          $queryCheck = $this->db->query("SELECT a.id FROM ob_app_emplist a INNER JOIN ob_app b ON a.`base_id`=b.`id` WHERE a.employeeid='$eid' AND a.status='APPROVED' AND ('$date' BETWEEN b.datefrom AND b.dateto)");
          // echo "<pre>"; print_r($this->db->last_query());
          if($queryCheck->num_rows() > 0){
            
            $res1 = $queryCheck->row(0);
            $base_id = $res1->id;
            $CI =& get_instance();
            $CI->load->model('ob_application');

            $result = $CI->ob_application->directApprovedByAdmin($base_id);
               
            
            if($result['err_code'] == 1){
               $query1 = $this->db->query("SELECT * FROM ob_request WHERE '$date' BETWEEN fromdate AND todate AND employeeid='$eid' AND (ob_type = 'ob' OR ob_type = '') ");
              if($query1->num_rows() > 0){  
                  // $res = $query1->row(0);
                  foreach($query1->result() as $res){
                      $arr_sched_aff = array();
                      $no_days = $res->no_days;
                      $isHalfDay = $res->isHalfDay;
                      $arr_sched_aff = explode(',', $res->sched_affected);
                      $base_id = $res->aid;

                      $new_time = $this->displayOBSched($base_id, $date, $sched_count);
                      if($new_time != "|") $time_aff = $new_time;
                      if($isHalfDay  && sizeof($arr_sched_aff) > 0){
                          if(in_array($time_aff, $arr_sched_aff)){
                              $othertype = $res->othertype;
                              if($othertype=='DIRECT' && $res->paid == "YES"){
                                  if($isHalfDay) $ob = 0.50;
                                  else $ob = $no_days;
                              }
                              if($othertype=='CORRECTION')        $oltype = 'CORRECTED TIME IN/OUT';
                              elseif($othertype=='ABSENT')        $oltype = 'ABSENT W/ FILE';
                              else                                $oltype = "Official Business";
                              $ol = $othertype;
                          }else{
                            if(!$stime && !$etime){
                              $othertype = $res->othertype;
                              $obtype = "";
                              if($othertype=='CORRECTION')        $obtype = 'CORRECTED TIME IN/OUT';
                              elseif($othertype=='ABSENT')        $obtype = 'ABSENT W/ FILE';
                              else                                $obtype = "Official Business";
                              if($ol) $oltype .= ", ".$obtype;
                              else $oltype .= $obtype;
                            }
                          }
                      }else{
                          $othertype = $res->othertype;
                          if($othertype=='DIRECT' && $res->paid == "YES"){
                              if($isHalfDay) $ob = 0.50;
                              else $ob = 1.00; 
                          }
                          if($othertype=='CORRECTION')        $oltype = 'CORRECTED TIME IN/OUT';
                          elseif($othertype=='ABSENT')        $oltype = 'ABSENT W/ FILE';
                          else                                $oltype = "Official Business";
                          $ol = $othertype;
                      }
                  }
              }
            }
           
          }
        }

        $query2 = $this->db->query("SELECT * FROM ob_request WHERE '$date' BETWEEN fromdate AND todate AND employeeid='$eid' AND ob_type != 'ob'  ");

        /*if($query2->num_rows() > 0){  
            $res = $query2->row(0);
            $ob = "";
            $othertype = $res->ob_type;
            if($othertype=='late')        $oltype = 'Excuse Slip (late)';
            elseif($othertype=='absent')  $oltype = 'Excuse Slip (absent)';
            // $ol = $othertype;
            
        }*/
        // echo "<pre>"; print_r(array($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$l_nopay)); 
        return array($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$l_nopay);
    }

    public function displayOtherOB($eid="",$date="",$stime="",$etime="",$login="",$logout=""){
         $query2 = $this->db->query("SELECT * FROM ob_request WHERE '$date' BETWEEN fromdate AND todate AND employeeid='$eid' AND ob_type != 'ob'  ");

         $ol = $oltype = $ob = "";
         if($query2->num_rows() > 0){  
            $res = $query2->row(0);
            $ob = "";
            $othertype = $res->ob_type;
            $timefrom = $res->timefrom;
            $timeto = $res->timeto;

            $login = date("H:i:s", strtotime($login));
            $logout = date("H:i:s", strtotime($logout));

            if($othertype=='late'){
              $oltype = 'Excuse Slip (late)';
              if ($login > $timefrom && $login < $timeto) $ol = $othertype;
            }

            if($othertype=='undertime'){
              $oltype = 'Excuse Slip (undertime)';
              if ($logout > $timefrom && $logout < $timeto) $ol = $othertype;
            }
            
        }
        return array($ol,$oltype,$ob);
    }

    function displayLeaveSched($base_id='', $date='',$sched_count=''){
      $sched = array();
      $leave_d = $this->db->query("SELECT a.base_id, employeeid FROM leave_app_emplist a INNER JOIN leave_app_base b ON a.base_id = b.id WHERE a.id = '$base_id'");
      if($leave_d->num_rows() > 0){
        $leave_id = $leave_d->row()->base_id;
        $employeeid = $leave_d->row()->employeeid;
        $leave_sched = $this->db->query("SELECT * FROM leave_schedref WHERE base_id = '$leave_id' ");
        if($leave_sched->num_rows() > 0){
          $dateactive = $leave_sched->row()->dateactive;
          $schedule = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$employeeid' AND dateactive = '$dateactive' AND idx  = DATE_FORMAT('$date','%w') ");
        // echo $this->db->last_query(); 
          if($schedule->num_rows() > 0){
            $seq_count = 1;
            foreach($schedule->result() as $res){
              $sched[$seq_count] = $res->starttime."|".$res->endtime;
              $seq_count++;
            }
          }
        }
      }

      return isset($sched[$sched_count]) ? $sched[$sched_count] : "|";
    }

    function displayOBSched($base_id='', $date='',$sched_count=''){
      $sched = array();
      $leave_d = $this->db->query("SELECT a.base_id FROM ob_app_emplist a INNER JOIN ob_app b ON a.base_id = b.id WHERE a.id = '$base_id'");
      if($leave_d->num_rows() > 0){
        $leave_id = $leave_d->row()->base_id;
        $leave_sched = $this->db->query("SELECT * FROM ob_schedref WHERE base_id = '$leave_id' ");
        if($leave_sched->num_rows() > 0){
          $dateactive = $leave_sched->row()->dateactive;
          $schedule = $this->db->query("SELECT * FROM employee_schedule_history WHERE dateactive = '$dateactive' AND idx  = DATE_FORMAT('$date','%w') ");
          if($schedule->num_rows() > 0){
            $seq_count = 1;
            foreach($schedule->result() as $res){
              $sched[$seq_count] = $res->starttime."|".$res->endtime;
              $seq_count++;
            }
          }
        }
      }

      return isset($sched[$sched_count]) ? $sched[$sched_count] : "|";
    }

    function displayChangeSchedApp($employeeid='',$date=''){
        $return = '';
        $query4 = $this->db->query("SELECT a.id FROM change_sched_app_emplist a INNER JOIN change_sched_app b ON a.base_id = b.id WHERE '$date' BETWEEN b.dfrom AND b.dto AND a.employeeid='$employeeid' AND a.status = 'APPROVED'"); 
        if($query4->num_rows() > 0){  
            $return = 'EMPLOYEE CHANGE SCHEDULE';
        }
        return $return;
    }

    //ADDED 07-06-17 SERVICE CREDIT
    function displayServiceCredit($eid="",$stime='',$etime='',$date="")
    {

        $service_credit = '';
        $time_aff = $stime.'|'.$etime;
        
        $query = $this->db->query("SELECT a.*,b.* FROM sc_app_use a LEFT JOIN sc_app_use_emplist b ON a.id = b.base_id WHERE b.employeeid='$eid' AND a.date = '$date' AND b.status = 'APPROVED'");
        
        if($query->num_rows() > 0){
            foreach($query->result() as $row)
            {
                $arr_sched_aff = array();
                $service_credit = $row->needed_service_credit;

                if($service_credit == 0.5 && $row->sched_affected){
                    $arr_sched_aff = explode(',', $row->sched_affected);
                }

                if($service_credit == 0.5 && sizeof($arr_sched_aff) > 0){
                    if(!in_array($time_aff, $arr_sched_aff)){
                        $service_credit = '';
                    }
                }

            }
        }
        
        return $service_credit;
    }
    

    //Service Credit 
    function displayServiceCreditRemarks($eid,$stime,$etime,$date)
    {
        $return = '';
        $query = $this->db->query("SELECT DISTINCT a.otype FROM timesheet a  INNER JOIN sc_app_use_emplist b ON(b.`employeeid` = a.`userid`) WHERE b.`status` = 'APPROVED' AND DATE(timein) = '$date' AND DATE(timeout) = '$date'  AND  b.employeeid='$eid' ORDER BY timein ASC");
        if ($query->num_rows() > 0) {
            $return = $query->row(0)->otype;
        }
        return $return;
    }

    
    /*
     * Leave
     */
    function displayPendingApp($eid="",$date="",$absent="", $stime="",$etime=""){
        $return="";
        $query1 = $this->db->query("SELECT a.id,b.type,nodays,isHalfDay,sched_affected FROM leave_app_emplist a INNER JOIN leave_app_base b ON a.base_id = b.id WHERE '$date' BETWEEN b.datefrom AND b.dateto AND a.employeeid='$eid' AND a.status = 'PENDING' AND isHalfDay = '0'");
        if($query1->num_rows() > 0){  
            $time_aff = $stime.'|'.$etime;
            $arr_sched_aff = array();
            $no_days = $query1->row(0)->nodays;
            $isHalfDay = $query1->row(0)->isHalfDay;
            $arr_sched_aff = explode(',', $query1->row(0)->sched_affected);

            $desc_q = $this->db->query("SELECT description FROM code_request_form WHERE code_request='{$query1->row(0)->type}'");
            if($desc_q->num_rows() > 0) $return.=($return?", ".$desc_q->row(0)->description." Application":$desc_q->row(0)->description." Application");
            else $return.=($return?", LEAVE Application":"LEAVE Application");
        }

        $time_aff = $stime.'|'.$etime;
        $query1 = $this->db->query("SELECT a.id,b.type,nodays,isHalfDay,sched_affected FROM leave_app_emplist a INNER JOIN leave_app_base b ON a.base_id = b.id WHERE '$date' BETWEEN b.datefrom AND b.dateto AND a.employeeid='$eid' AND a.status = 'PENDING' AND isHalfDay = '1' ");
        if($query1->num_rows() > 0){  
          foreach($query1->result() as $row){
              $arr_sched_aff = array();
              $no_days = $row->nodays;
              $isHalfDay = $row->isHalfDay;
              $arr_sched_aff = explode(',', $row->sched_affected);

              if(sizeof($arr_sched_aff) > 0){
                  if(in_array($time_aff, $arr_sched_aff)){
                     $desc_q = $this->db->query("SELECT description FROM code_request_form WHERE code_request='{$row->type}'");
                    if($desc_q->num_rows() > 0) $return.=($return?", ".$desc_q->row(0)->description." Application":$desc_q->row(0)->description." Application");
                    else $return.=($return?", LEAVE Application":"LEAVE Application");
                  }else{
                    if(!$stime && !$etime){
                      $desc_q = $this->db->query("SELECT description FROM code_request_form WHERE code_request='{$row->type}'");
                      if($desc_q->num_rows() > 0) $return.=($return?", ".$desc_q->row(0)->description." Application, ":$desc_q->row(0)->description." Application, ");
                      else $return.=($return?", Leave Application, ":"Leave Application, ");
                    }
                  }
              }
            }
        }

        $query1 = $this->db->query("SELECT a.id,b.type,b.ob_type, nodays, isHalfDay, sched_affected FROM ob_app_emplist a INNER JOIN ob_app b ON a.base_id = b.id WHERE '$date' BETWEEN b.datefrom AND b.dateto AND a.employeeid='$eid' AND a.status = 'PENDING'");
       
        if($query1->num_rows() > 0){  
          foreach($query1->result() as $row){
              $time_aff = $stime.'|'.$etime;
              $obtype = $row->type;
              $obtype2 = $row->ob_type;
              $obtypedesc = ($obtype == 'CORRECTION' ? "CORRECTION FOR TIME IN/OUT Application":($obtype == 'ABSENT' ? "ABSENT Application":"Official Business Application"));

              /*if($obtype2=='late')        $obtypedesc = 'EXCUSE SLIP (late) APPLICATION';
              elseif($obtype2=='undertime')  $obtypedesc = 'EXCUSE SLIP (undertime) APPLICATION';
              elseif($obtype2=='absent')  $obtypedesc = 'EXCUSE SLIP (absent) APPLICATION';*/
              // if($obtype2 != "ob") $obtypedesc = "";

              $arr_sched_aff = array();
              $no_days = $row->nodays;
              $isHalfDay = $row->isHalfDay;
              $arr_sched_aff = explode(',', $row->sched_affected);

              if($isHalfDay  && sizeof($arr_sched_aff) > 0){
                  if(in_array($time_aff, $arr_sched_aff)){
                     $return.=($return?", ".$obtypedesc:$obtypedesc."<br>");
                  }
              }else{
                  $return.=($return?", ".$obtypedesc:$obtypedesc."<br>");
              }
            }
        }
        $query2 = $this->db->query("SELECT * FROM seminar_app a INNER JOIN seminar_app_emplist b ON a.id = b.base_id WHERE '$date' BETWEEN datesetfrom AND datesetto AND employeeid='$eid' AND b.status = 'PENDING'");
        if($query2->num_rows() > 0){  
            $return.=($return?", SEMINAR APPLICATION":"SEMINAR APPLICATION");
        }
        $query3 = $this->db->query("SELECT a.id FROM ot_app_emplist a INNER JOIN ot_app b ON a.base_id = b.id WHERE '$date' BETWEEN b.dfrom AND b.dto AND a.employeeid='$eid' AND a.status = 'PENDING'");
        if($query3->num_rows() > 0){  
            $return.=($return?", OVERTIME APPLICATION":"OVERTIME APPLICATION");
        }
        $query4 = $this->db->query("SELECT a.id FROM change_sched_app_emplist a INNER JOIN change_sched_app b ON a.base_id = b.id WHERE '$date' BETWEEN b.dfrom AND b.dto AND a.employeeid='$eid' AND a.status = 'PENDING'"); 
        if($query4->num_rows() > 0){  
            $return.=($return?", CHANGE SCHEDULE APPLICATION":"CHANGE SCHEDULE APPLICATION");
        }

        $query4 = $this->db->query("SELECT a.id FROM sc_app_emplist a INNER JOIN sc_app b ON a.base_id = b.id WHERE `date`='$date' AND a.employeeid='$eid' AND a.status = 'PENDING'"); 
        if($query4->num_rows() > 0){  
            $return.=($return?", SERVICE CREDIT APPLICATION":"SERVICE CREDIT APPLICATION");
        }
        
        return $return;
    }

    function displayPendingOBApp($eid="", $date="", $ob_type=""){
        $return = $obtypedesc = "";
        $query1 = $this->db->query("SELECT a.id,b.type,b.ob_type FROM ob_app_emplist a INNER JOIN ob_app b ON a.base_id = b.id WHERE '$date' BETWEEN b.datefrom AND b.dateto AND a.employeeid='$eid' AND a.status = 'PENDING' AND ob_type = '$ob_type'");
        if($query1->num_rows() > 0){  
            $obtype2 = $query1->row(0)->ob_type;

            if($obtype2=='late')        $obtypedesc = ' <br>PENDING EXCUSE SLIP (late) APPLICATION';
            elseif($obtype2=='undertime')  $obtypedesc = ' <br>PENDING EXCUSE SLIP (undertime) APPLICATION';
            elseif($obtype2=='absent')  $obtypedesc = ' <br>PENDING EXCUSE SLIP (absent) APPLICATION';

            $return.=($return?", ".$obtypedesc:$obtypedesc);
        }

        return $return;
    }
    
    /*
     * Overtime
     */
    function displayOt($eid="",$date="",$hasSched=true){
        $otreg = $otrest = $othol = "";
        // $query = $this->db->query("SELECT a.*,b.* FROM ot_app a LEFT JOIN ot_app_emplist b ON a.id = b.base_id WHERE b.employeeid='$eid' AND '$date' BETWEEN a.dfrom AND a.dto AND b.status = 'APPROVED'");

        $query = $this->db->query("
                                    SELECT tstart,tend,total
                                    FROM overtime_request
                                    WHERE employeeid='$eid' AND ('$date' BETWEEN dfrom AND dto) AND `status` = 'APPROVED' 
                                ");
       if($query->num_rows > 0){
            foreach($query->result() as $value){
                if(!$value->total){
                    $time1 = new DateTime($value->tstart);
                    $time2 = new DateTime($value->tend);
                    $timediff = $time1->diff($time2);
                    $value->total = $timediff->h.":".$timediff->i;
                } 

                if      ($hasSched)  $otreg += $this->attcompute->exp_time($value->total);
                else                 $otrest += $this->attcompute->exp_time($value->total);
                
                if($this->isHoliday($date)){

                    $otreg = $otrest = "";
                    $othol += $this->attcompute->exp_time($value->total);
                }
            }
        }
        
        $otreg = ($otreg) ? $this->attcompute->sec_to_hm($otreg) : "";
        $otrest = ($otrest) ? $this->attcompute->sec_to_hm($otrest) : "";
        $othol = ($othol) ? $this->attcompute->sec_to_hm($othol) : "";
        return array($otreg,$otrest,$othol);
    }

    function displayOtCollege($eid="",$date="",$holiday='',$holiday_type=''){
        $otreg = $otsat = $otsun = $othol = 0;
        $wdname = date("l",strtotime($date));
        // $query = $this->db->query("SELECT a.*,b.* FROM ot_app a LEFT JOIN ot_app_emplist b ON a.id = b.base_id WHERE b.employeeid='$eid' AND '$date' BETWEEN a.dfrom AND a.dto AND status = 'APPROVED'");
        $query = $this->db->query("SELECT * FROM overtime_request WHERE employeeid='$eid' AND '$date' BETWEEN dfrom AND dto AND STATUS = 'APPROVED'");

        if($query->num_rows() > 0){
            foreach ($query->result() as $key => $row) {

                if($holiday && in_array($holiday_type, array('1','2','4'))){
                    $othol += $this->exp_time($row->total);
                }else{
                    if      ($wdname == "Saturday") $otsat += $this->exp_time($row->total);
                    else if ($wdname == "Sunday")   $otsun += $this->exp_time($row->total);
                    else                            $otreg += $this->exp_time($row->total);
                }

            }
        }

        $otreg = $otreg != 0 ? $this->sec_to_hm($otreg) : ''; 
        $otsat = $otsat != 0 ? $this->sec_to_hm($otsat) : ''; 
        $otsun = $otsun != 0 ? $this->sec_to_hm($otsun) : ''; 
        $othol = $othol != 0 ? $this->sec_to_hm($othol) : ''; 
        
        return array($otreg,$otsat,$otsun,$othol);
    }

    ///< @Angelica -- based on new ot setup
    function getOvertime($employeeid='',$date='',$hasSched=true,$code_holtype=''){
        // need time ng ot
        // check if weekend

        //TODO : NIGHT_DIFF

        $ot_list = array();
        $excess_limit = 8*60*60;

        $dayofweek = date('N',strtotime($date));
        $isWeekend = in_array($dayofweek, array('6','7')) ? true : false;

        $ot_type = '';
        if($hasSched) $ot_type = 'WITH_SCHED';
        if($hasSched && $isWeekend) $ot_type = 'WITH_SCHED_WEEKEND';
        if(!$hasSched) $ot_type = 'NO_SCHED';

        $holiday_type = 'NONE';
        if($code_holtype){
            if($code_holtype == 1)  $holiday_type = 'REGULAR';
            elseif($code_holtype != 1) $holiday_type = 'SPECIAL';
        }


        $ot_q = $this->db->query("
                                    SELECT tstart,tend,total
                                    FROM overtime_request
                                    WHERE employeeid='$employeeid' AND ('$date' BETWEEN dfrom AND dto) AND `status` = 'APPROVED' 
                                ");

        foreach ($ot_q->result() as $key => $row) {
            $isExcess = false;
            $excess = 0;
            $ottime = $this->exp_time($row->total);

            if($ottime > $excess_limit){
                $excess = $ottime - $excess_limit;
                $ottime = $excess_limit;
            }

            if($excess > 0) $isExcess = true;

            $ot_list[$ot_type][$holiday_type][0] = $ottime;
            if($isExcess) $ot_list[$ot_type][$holiday_type][1] = $excess;
        }
        // echo '<pre>'.$date;
        // print_r($ot_list);
        // echo '</pre>';

        return $ot_list;
    }

    function constructOTlist($ot_list,$ot_list_tmp){
        foreach ($ot_list_tmp as $ot_type => $det) {
            foreach ($det as $ot_hol_type => $ex_det) {
                foreach ($ex_det as $isExcess => $ot_hours) {
                    if(!isset($ot_list[$ot_type][$ot_hol_type][$isExcess])) $ot_list[$ot_type][$ot_hol_type][$isExcess] = 0;
                    $ot_list[$ot_type][$ot_hol_type][$isExcess] += $ot_hours;
                }
            }
        }
        return $ot_list;
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
    
    function holidayInfo($date="", $sched_count="", $teachingtype ="", $deptid ="", $holiday_id=""){
        $return=array();
        $wc = "";
        if($sched_count) $wc .= " AND sched_count = '$sched_count' ";
        if($teachingtype) $wc .= " AND (teaching_type = '$teachingtype' OR teaching_type = 'all')";
        if($holiday_id) $wc .= " AND c.holiday_id = '$holiday_id'";
        $sql = $this->db->query("SELECT c.holiday_id, c.halfday, c.fromtime, c.totime , c.sched_count, a.withPay, a.holiday_type, a.description, b.hdescription, b.code, a.holiday_rate
        FROM code_holiday_type a
        LEFT JOIN code_holidays b ON a.`holiday_type` = b.holiday_type
        LEFT JOIN code_holiday_calendar c ON b.`holiday_id` = c.holiday_id
        WHERE '$date' BETWEEN c.date_from AND c.date_to $wc");
        foreach($sql->result() as $row)
        {
          // echo "<pre>"; print_r($row); die;
          if($deptid){
            $que = $this->db->query("SELECT status_included from holiday_inclusions where holi_cal_id = '{$row->holiday_id}' AND dept_included = '{$deptid}' AND status_included IS NOT NULL");
            if($que->num_rows() > 0){
                if($row->halfday == "on"){
                    $return['fromtime'] = $row->fromtime;
                    $return['totime'] = $row->totime;
                    $return['halfday'] = $row->halfday;
                    $return['sched_count'] = $row->sched_count;
                }
                $return["holiday_type"] = $row->holiday_type;
                $return["withPay"] = $row->withPay;
                $return["type"] = Globals::_e($row->description);
                $return["description"] = Globals::_e($row->hdescription);
                $return["code"] = Globals::_e($row->code);
                $return["holiday_rate"] = $row->holiday_rate;
              }
            }else{
              if($row->halfday == "on"){
                  $return['fromtime'] = $row->fromtime;
                  $return['totime'] = $row->totime;
                  $return['halfday'] = $row->halfday;
                  $return['sched_count'] = $row->sched_count;
              }
              $return["holiday_type"] = $row->holiday_type;
              $return["withPay"] = $row->withPay;
              $return["type"] = Globals::_e($row->description);
              $return["description"] = Globals::_e($row->hdescription);
              $return["code"] = Globals::_e($row->code);
              $return["holiday_rate"] = $row->holiday_rate;
            }
        }
        return $return;
    }


    function isHolidayNew($empid,$date,$deptid,$campus="",$halfday="",$teachingtype=""){
        $wc = "";
        if($teachingtype) $wc = " AND (teaching_type = '$teachingtype' OR teaching_type = 'all')";
        $sql = $this->db->query("SELECT * FROM code_holiday_calendar a INNER JOIN code_holidays b ON a.holiday_id = b.holiday_id WHERE '$date' BETWEEN date_from AND date_to AND (halfday = '$halfday' OR halfday IS NULL) $wc");
        
        if($sql->num_rows() > 0){
          foreach($sql->result() as $row){
              $paymentType = "";
              $holiday_id = $row->holiday_id;
              $query = $this->db->query("SELECT * from employee where employeeid = '{$empid}'");
              $employmentstat = $query->row(0)->employmentstat;
              $campusid = $query->row(0)->campusid;
              $teachingtype = $query->row(0)->teachingtype;
              $holiday = $this->db->query("SELECT * FROM code_holidays WHERE holiday_id = '$holiday_id'")->result();
              $Ptype = $this->db->query("SELECT fixedday FROM payroll_employee_salary WHERE employeeid = '{$empid}'");
              if ($Ptype->num_rows() > 0) {
                  $paymentType = $Ptype->row(0)->fixedday;
              }
              
              
              if(isset($holiday[0]->campus) && $campusid){
                  if ($holiday[0]->campus == "All" OR $holiday[0]->campus == $campusid) {
                      if ($holiday[0]->teaching_type == "all" OR $holiday[0]->teaching_type == $teachingtype) {
                          if ($holiday[0]->payment_type == "all" OR $holiday[0]->payment_type == $paymentType) {
                              $que = $this->db->query("SELECT status_included from holiday_inclusions where holi_cal_id = '{$holiday_id}' AND dept_included = '{$deptid}' AND status_included IS NOT NULL");
                              if($que->num_rows() > 0)
                              {
                                  $return = false;
                                  foreach(explode(", ",$que->row(0)->status_included) as $k => $v)
                                  {
                                      $include = explode("~",$v);
                                      if(isset($include[1])){
                                        if($include[1] == $employmentstat)
                                        {
                                            $return = $holiday_id;
                                            break;
                                        }
                                      }
                                  }
                                  return $return;
                              }
                          }
                      }
                  }
              }
              else { return false; }
            }
        }
        else{   return false;}
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
       if($query)
       {
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
       }
        
        
        return $return; 
    }
    
    function getFirstDayOfWeek($eid=""){
        $return = "";
        $query = $this->db->query("SELECT DISTINCT(dayofweek) FROM employee_schedule_history WHERE employeeid = '$eid' ORDER BY idx ASC LIMIT 1")->result();
       
       if($query)
       {
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
                    $tempabsent = isset($tempabsent)?$tempabsent:"";
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
    

    //ADDED 07-15-17 WITH LOG
    function withLog($eid="",$date = ""){
        $return = "";
        $query = $this->db->query("SELECT * FROM timesheet WHERE userid = '$eid' AND DATE(timein)  = DATE('$date') AND DATE(timein) = DATE('$date') ORDER BY timein");
        
        return $query; 
    }
    
    //ADDED 07-21-17 DISPLAY LOG TIME OF FLEXI SCHED
    function displayLogTimeFlexi($eid="",$date="",$tbl=""){
        $return = array();
        if($tbl == "NEW")   $tbl = "timesheet";
        else                $tbl = "timesheet_bak";
        // $query = $this->db->query("SELECT timein,timeout,otype FROM $tbl WHERE userid='$eid' AND DATE(timein)='$date' ORDER BY timein ASC");
        $query = $this->db->query("SELECT MIN(timein) AS timein,MAX(timeout) AS timeout,otype FROM $tbl WHERE userid='$eid' AND DATE(timein)='$date' ORDER BY timein ASC");

        if($query->num_rows() > 0){
            foreach($query->result() as $row)
            {
                $timein = $row->timein;
                $timeout = $row->timeout;
                if($timein!=null || $timeout!=null){
                    if($timein=='0000-00-00 00:00:00') $timein = "";
                    if($timeout=='0000-00-00 00:00:00') $timeout = "";
                    array_push($return,array($timein,$timeout,$row->otype));
                }
            }
        }else{
             $query = $this->db->query("SELECT localtimein FROM timesheet_trail WHERE userid='$eid' AND DATE(localtimein)='$date' AND log_type = 'IN' ORDER BY localtimein DESC");
             if ($query->num_rows() == 0) {
                 $query = $this->db->query("SELECT localtimein  FROM webcheckin_history WHERE userid='$eid' AND DATE(localtimein)='$date' AND log_type = 'IN' ORDER BY localtimein DESC");
             }

            if($query->num_rows() > 0){
                foreach($query->result() as $row)
                {
                    $logtime = $row->localtimein;
                    if($logtime=='0000-00-00 00:00:00') $logtime = "";
                    array_push($return,array($logtime,"",""));
                }
            }   
            
        }
        
        return $return;
    }

    function getLogsPerDay($eid="",$date="",$tbl="", $is_add_time_trail = true){
        
        $return = array();
        if($tbl == "NEW")   $tbl = "timesheet";
        else                $tbl = "timesheet_bak";
        $query = $this->db->query("SELECT DISTINCT timein,timeout,otype FROM $tbl WHERE userid='$eid' AND DATE(timein)='$date' ORDER BY timein ASC");
        // $query = $this->db->query("SELECT MIN(timein) AS timein,MAX(timeout) AS timeout,otype FROM $tbl WHERE userid='$eid' AND DATE(timein)='$date' ORDER BY timein ASC");

        if($query->num_rows() > 0){
            foreach($query->result() as $row)
            {
                if($row->timein!=null || $row->timeout!=null){
                    array_push($return,array($row->timein,$row->timeout,$row->otype));
                }
            }
        }
        
        $query = $this->db->query("SELECT DISTINCT localtimein FROM timesheet_trail WHERE userid='$eid' AND DATE(localtimein)='$date' AND log_type = 'IN' ORDER BY localtimein DESC");
        if($query->num_rows() > 0){
            foreach($query->result() as $row)
            {
                if($is_add_time_trail) array_push($return,array($row->localtimein,"",""));
            }
        }   
        
        return $return;
    }
    
    //ADDED 07-21-17 DISPLAY ABSENT OF FLEXI SCHED
    function displayAbsentFlexi($log="",$hours="",$mode="",$empid="",$dset="",$type='',$breaktime=0,$count_leave=0){
        $absent = "";
        $time = sprintf('%02d:%02d', (int) $hours, fmod($hours, 1) * 60);
        $h = date("H:i",strtotime($time));

        $hSTR = $this->exp_time($time);
        $breaktime = $breaktime * 60 * 60;

        if($mode == "day")
        {
            $totalHour= 0;

            if($count_leave == 0.50){
                $totalHour = ($hSTR-$breaktime)/2;
            }else{
                $totalHour = ($hSTR-$breaktime);
            }
            
            if(count($log) <= 0) $absent = $totalHour;
            else{

                if(isset($log[0][0])){
                    if($log[0][0] == null || $log[0][0] == '0000-00-00 00:00:00') $absent = $totalHour;
                }
                if(isset($log[0][1])){
                    if($log[0][1] == null || $log[0][1] == '0000-00-00 00:00:00') $absent = $totalHour;
                }

            }

            if( $absent > 0 ){
                $absent = $this->sec_to_hm($absent);
            }
        
            if($empid){
                $query = $this->db->query("SELECT * FROM attendance_absent_checker WHERE employeeid='$empid' AND scheddate = '$dset'");
                if($query->num_rows() > 0)  $absent = $h;
            }

        }

        if($absent < 0) $absent = 0; 
        return $absent;
    }
    
    //ADDED 07-21-17 DISPLAY LATE OF FLEXI SCHED
    // Teaching
    function displayLateUTFlexi($log="",$hours="",$mode="",$type="",$absent="",$breaktime=0,$count_leave=0){
        $lec = $lab  = $admin = $tschedlec = $tschedlab = $tschedadmin = "";
        $time = sprintf('%02d:%02d', (int) $hours, fmod($hours, 1) * 60);
        $h = date("H:i:s",strtotime($time));
        // $hSTR  = strtotime($h);
        $hSTR = $this->exp_time($time);
        $breaktime = $breaktime * 60 * 60;
        if($mode == "day")
        {
            if(count($log) > 0 && !$absent)
            {

                $login = $logout = $totalHour= 0;

                if($count_leave == 0.50){
                    $totalHour = ($hSTR-$breaktime)/2;
                }

                for($i = 0;$i < count($log);$i++)
                {
                    // if($log[$i][0]) $login = strtotime($log[$i][0]);
                    // if($log[$i][1]) $logout = strtotime($log[$i][1]);
              
                    if($log[$i][0]) $login = new DateTime($log[$i][0]);
                    if($log[$i][1]) $logout = new DateTime($log[$i][1]);

                    $duration = $login->diff($logout); //$duration is a DateInterval object
              
                    $duration = $this->exp_time($duration->format("%H:%I"));

                    $totalHour += $duration;
                }

         
                $diff = $hSTR - $totalHour;     
                

                if($diff >  (($hSTR-$breaktime)/2) && $diff <= ((($hSTR-$breaktime)/2)+$breaktime)){
                    $diff = ($hSTR-$breaktime)/2;
                }elseif($diff > ((($hSTR-$breaktime)/2)+$breaktime) || $totalHour > ((($hSTR-$breaktime)/2)+$breaktime) ){
                    $diff = $diff - $breaktime;
                }

                if( $diff > 0 ){

                    if($type == "LEC"){ 
                        $lec = $this->sec_to_hm($diff);
                    }elseif($type=="LAB"){
                        $lab = $this->sec_to_hm($diff);
                    }else{
                        $admin = $this->sec_to_hm($diff);
                    }
                }
            }elseif(count($log) == 0 && !$absent){
                $totalHour = 0;
                if($count_leave == 0.50){
                    $totalHour = (($hSTR-$breaktime)/2);
                }elseif($count_leave >= 1){
                    $totalHour = $hSTR;
                }

                $diff = $hSTR - $totalHour;     
    
                if($diff >  (($hSTR-$breaktime)/2) && $diff <= ((($hSTR-$breaktime)/2)+$breaktime)){
                    $diff = ($hSTR-$breaktime)/2;
                }elseif($diff > ((($hSTR-$breaktime)/2)+$breaktime) || $totalHour > ((($hSTR-$breaktime)/2)+$breaktime) ){
                    $diff = $diff - $breaktime;
                }

                if( $diff > 0 ){
                    if($type == "LEC"){ 
                        $lec = $this->sec_to_hm($diff);
                    }elseif($type=="LAB"){
                        $lab = $this->sec_to_hm($diff);
                    }else{
                        $admin = $this->sec_to_hm($diff);
                    }
                }
            }
            
            if($absent)
            {
                if($type == "LEC"){
                    $tschedlec  = $this->sec_to_hm($hSTR-$breaktime);
                }elseif($type=="LAB"){
                    $tschedlab  = $this->sec_to_hm($hSTR-$breaktime);
                }else{
                    $tschedadmin  = $this->sec_to_hm($hSTR-$breaktime);
                }
            }
        }
        return array($lec,$lab,$admin,$tschedlec,$tschedlab,$tschedadmin);
    }  
    // Non-Teaching
    function displayLateUTNTFlexi($log="",$hours="",$mode="",$absent="",$breaktime=0,$count_leave=0){
        $lec = $lab = $tschedlec = $tschedlab = "";
        $lateut = "";
        $time = sprintf('%02d:%02d', (int) $hours, fmod($hours, 1) * 60);
        $h = date("H:i:s",strtotime($time));
        $hSTR  = $this->exp_time($h);
        $breaktime = $breaktime * 60 * 60;

        if($mode == "day")
        {
            if(count($log) > 0 && !$absent)
            {
                $login = $logout = $totalHour= 0;

                if($count_leave == 0.50){
                    $totalHour = ($hSTR-$breaktime)/2;
                }
                
                for($i = 0;$i < count($log);$i++)
                {
                    if(isset($log[$i][0]) && isset($log[$i][1])){
                        if($log[$i][0] != '0000-00-00 00:00:00' && $log[$i][1] != '0000-00-00 00:00:00' && $log[$i][0] != '' && $log[$i][1] != ''){
                            if($log[$i][0]) $login = $this->exp_time(date("H:i:s",strtotime($log[$i][0])));
                            if($log[$i][1]) $logout = $this->exp_time(date("H:i:s",strtotime($log[$i][1])));
                        }
                    }

                    $totalHour += $logout - $login;
                }
                
                $diff = $hSTR - $totalHour;
                
                // $lateut = date('H:i', $diff);
                if($diff > 0){
                    $lateut = $this->sec_to_hm($diff);
                }
                

            }elseif(count($log) == 0 && !$absent){
                $totalHour = 0;
                if($count_leave == 0.50){
                    $totalHour = (($hSTR-$breaktime)/2);
                }elseif($count_leave >= 1){
                    $totalHour = $hSTR;
                }

                $diff = $hSTR - $totalHour;     
    
                if($diff >  (($hSTR-$breaktime)/2) && $diff <= ((($hSTR-$breaktime)/2)+$breaktime)){
                    $diff = ($hSTR-$breaktime)/2;
                }elseif($diff > ((($hSTR-$breaktime)/2)+$breaktime) || $totalHour > ((($hSTR-$breaktime)/2)+$breaktime) ){
                    $diff = $diff - $breaktime;
                }

                if( $diff > 0 ){
                    $lateut = $this->sec_to_hm($diff);
                }
            }
        }
        if($lateut == "00:00") $lateut = "";
        return $lateut;
    }
    
    //ADDED 07-21-17 DISPLAY LATE OF FLEXI SCHED
    function displayOverloadTimeFlexi($log="",$hours="",$mode="",$lateutlab="") {
        $return = "";
        if($mode == "day")
        {
            if(count($log) > 0)
            {
                $st = $et = $lab = 0;
                for($i = 0;$i < count($log);$i++)
                {
                    if($log[$i][0]) $st += $this->exp_time(date('H:i',strtotime($log[$i][0])));
                    if($log[$i][1]) $et += $this->exp_time(date('H:i',strtotime($log[$i][1])));
                }
                
                if($lateutlab)
                {
                    $lab = $this->exp_time(date('H:i',strtotime($lateutlab)));
                }
                
                $return =   ($et - $st) - $lab;
            }
        }
        return $return;
    }

    function getLogout($empid, $edata, $date){
        $logout = "";

        $tbl = "timesheet_bak";
        if($edata == "NEW") $tbl = "timesheet";

        $q_findLogTime = $this->db->query("SELECT * FROM $tbl WHERE userid='$empid' AND (DATE_FORMAT(timein, '%Y-%m-%d') BETWEEN '$date' AND '$date' OR DATE_FORMAT(timeout, '%Y-%m-%d') BETWEEN '$date' AND '$date') AND timein != timeout /* AND otype IS NULL*/ ORDER BY timein DESC;")->result();

        foreach ($q_findLogTime as $res){
          if($res->timeout != "0000-00-00 00:00:00" && $res->timeout) $logout = $res->timeout;
        }

        if(!$logout){
            $q_findLogTime = $this->db->query("SELECT * FROM timesheet_trail WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='OUT' ORDER BY localtimein DESC;")->result();

            if (count($q_findLogTime) == 0) {
                $q_findLogTime = $this->db->query("SELECT localtimein FROM webcheckin_history WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='OUT' ORDER BY localtimein DESC;")->result();
                  // echo "<pre>"; print_r($this->db->last_query());
            }
            
            foreach ($q_findLogTime as $res){
                if($res->localtimein != "0000-00-00 00:00:00" && $res->localtimein) $logout = $res->localtimein;
            }
        }

        return $logout;
    }

    function getLogin($empid, $edata, $date){
        $login = "";

        $tbl = "timesheet_bak";
        if($edata == "NEW") $tbl = "timesheet";

        $q_findLogTime = $this->db->query("SELECT * FROM $tbl WHERE userid='$empid' AND (DATE_FORMAT(timein, '%Y-%m-%d') BETWEEN '$date' AND '$date' OR DATE_FORMAT(timeout, '%Y-%m-%d') BETWEEN '$date' AND '$date') AND timein != timeout /* AND otype IS NULL*/ ORDER BY timein DESC;")->result();
        foreach ($q_findLogTime as $res){
            if($res->timein != "0000-00-00 00:00:00" && $res->timein) $login = $res->timein;
        } 

        if(!$login){
            $q_findLogTime = $this->db->query("SELECT * FROM timesheet_trail WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='IN' ORDER BY localtimein DESC;")->result();

            if (count($q_findLogTime) == 0) {
                $q_findLogTime = $this->db->query("SELECT localtimein FROM webcheckin_history WHERE userid='$empid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$date' AND log_type='IN' ORDER BY localtimein DESC;")->result();
            }
            
            foreach ($q_findLogTime as $res){
                if($res->localtimein != "0000-00-00 00:00:00" && $res->localtimein) $login = $res->localtimein;
            }
        }
                  // echo "<pre>"; print_r($this->db->last_query());


        return $login;
    }

    function getOvertimeAmountPayed($empid, $data){
        $this->load->model('income');
        $this->load->model('overtime');
        $key = $ot_type = $ot_hours = '';
        $ot_amount = $ot_type = '';

        foreach($data as $key => $value){
            foreach($value as $ot_data){

                $emp_status = $this->extras->getEmploymentStatus($empid);
                $ot_workhours = $this->attcompute->sec_to_hm($ot_data[0]);
                $ot_workhours = $this->time->hoursToMinutes($ot_workhours);
                $getOvertimeSetup = $this->overtime->getOvertimeSetup($emp_status, $key);
                $percentage = number_format($getOvertimeSetup['percent'], 2) / 100;
                /*get employee hourly rate*/
                $emp_minutely = $this->income->getEmployeeSalaryRate($empid, "minutely");
                $ot_amount = $emp_minutely * $ot_workhours;

                $ot_amount = $ot_amount * $percentage;


                if($key == "WITH_SCHED" || $key == "WITH_SCHED_WEEKEND") $ot_type = "Regular Day";
                else if($key == "NO_SCHED") $ot_type = "Rest Day";
                /*end*/                
            }
        }

        return array($ot_amount, $ot_type);
    }

    function getOvertimeAmountDetailed($empid, $ot_details, $emp_ot=''){
        #echo "<pre>"; print_r($ot_details);
        $this->load->model('utils');
        $this->load->model('payrollcomputation');
        $this->load->model('time');
        $this->load->model('income');
        $ot_amount = 0;
        $ot_type = "";

        $rate_per_hour = ($this->income->getEmployeeSalaryRate1($empid, "daily") / 8);
        $rate_per_minute = $rate_per_hour / 60;
        $employeement_status = $this->extras->getEmploymentStatus($empid);
        $setup = $this->payrollcomputation->getOvertimeSetup($employeement_status);

        $percent = 100;
        foreach ($ot_details as $ot_type => $holiday_type_list) {
            foreach ($holiday_type_list as $holiday_type => $ot_info) {
                $ot_min = ($emp_ot) ? $emp_ot : $ot_info[0];
                $ot_min = $this->sec_to_hm($ot_min);
                $ot_min = $this->time->hoursToMinutes($ot_min);
                $sel_setup = (isset($ot_info[1])) ? 1 : 0;

                if(isset($setup[$employeement_status][$ot_type][$holiday_type][$sel_setup])) $percent = $setup[$employeement_status][$ot_type][$holiday_type][$sel_setup];
                $percent = $percent / 100;
                
                $minutely = $rate_per_minute * $percent;
                $ot_amount = $minutely * $ot_min;

                switch ($ot_type) {
                    case 'WITH_SCHED': case 'WITH_SCHED_WEEKEND':
                        $ot_type = "Regular Day";
                        break;
                    
                    case 'NO_SCHED':
                        $ot_type = "Rest Day";
                        break;
                }
            }
        }

        return array($ot_amount, $ot_type);
    }

    function insertOTListToArray($ot_save_list, $ot_list){
        if(count($ot_list)){
            foreach ($ot_list as $ot_type => $ot_data) {
                foreach ($ot_data as $holiday_type => $holiday_data) {
                    foreach ($holiday_data as $is_excess => $ot_time) {
                        $ot_save_list[] = array(
                            'ot_hours'=> $this->sec_to_hm($ot_time),
                            'ot_type' => $ot_type,
                            'holiday_type' => $holiday_type,
                            'is_excess' => $is_excess
                        );
                    }
                }
            }
        }
        
        return $ot_save_list;
    }

    function gettotalhours($empid='',$dfrom= "",$dto=""){
        $return = array();
        $query = $this->db->query("SELECT DATE_FORMAT(DATE_ADD(timein, INTERVAL(2-DAYOFWEEK(timein)) DAY),'%Y-%m-%d')AS datestart,
        DATE_FORMAT(DATE_ADD(timeout, INTERVAL(6-DAYOFWEEK(timeout)) DAY),'%Y-%m-%d')dateend,
        WEEK(timein)AS numweek,
        #SUM(TIMESTAMPDIFF(HOUR, timein, timeout)) AS totalhours
        SEC_TO_TIME(SUM(TIME_TO_SEC(timeout) - TIME_TO_SEC(timein))) AS totalhours
        FROM timesheet
        WHERE userid='$empid' 
        AND DATE_FORMAT(timein,'%Y-%m-%d') >= '$dfrom'
        AND DATE_FORMAT(timein,'%Y-%m-%d') <= '$dto'
        GROUP BY numweek")->result();
        return $query;
    }

    function displayLateUTAbs($empid, $date){
        $ob_data = array();
        $q_ob = $this->db->query("SELECT * FROM ob_request WHERE employeeid = '$empid' AND ob_type != 'ob'  AND fromdate AND todate BETWEEN '$date' AND '$date' ");
        
        if($q_ob->num_rows > 0){

            foreach($q_ob->result_array() as $value){
                $ob_data[$value['ob_type']] = $value['ob_type'];
            }
        }
        return $ob_data;
    }

    function holidayHalfdayComputation($login, $logout, $fromtime, $totime , $firstsched){
        if(!$firstsched){
            if(($this->exp_time($fromtime) <= $this->exp_time($logout) ) || ($this->exp_time($logout) <= $this->exp_time($totime)) ){
                if($logout) return $this->exp_time($fromtime) - $this->exp_time(date("H:i", strtotime($logout)));
                else return false;
            }
        }else{
            if(($this->exp_time($fromtime) <= $this->exp_time($login) ) || ($this->exp_time($login) <= $this->exp_time($totime)) ){
                if($login) return $this->exp_time(date("H:i", strtotime($login))) - $this->exp_time($totime);
                else return false;
            }
        }
    }

    function employeeScheduleDateActive($eid, $date, $starttime="", $endtime=""){
      $wc = "";
      if($starttime && $endtime) $wc = " AND starttime = '$starttime' AND endtime = '$endtime' ";
      $query = $this->db->query("SELECT dateactive FROM employee_schedule_history WHERE employeeid = '$eid' $wc AND idx  = DATE_FORMAT('$date','%w') AND DATE(dateactive) <= DATE(DATE_SUB('$date',INTERVAL 1 DAY)) ORDER BY dateactive DESC,starttime DESC LIMIT 1;");
      if($query->num_rows() > 0) return $query->row()->dateactive;
      else return false;
    }

}