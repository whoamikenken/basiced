<?php
/**
 * @author Justin
 * @copyright 2016
 */

$dateRange = "";

$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$dept = $deptid;

#$from_date = '2016-02-01';
#$to_date = '2016-02-01';
/*
$from_date = date('Y-m-d');
$to_date = date('Y-m-d');
$empid = "";
$dept = "";
*/
$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept);

$isFlexi = false;
$flexi = $this->employee->getindividualemployee($empid);
 foreach($flexi as $row)    $isFlexi = (bool)$row->isFlexi;
  	
if (count($result) > 0) {
	$deptDisplay = "";
	foreach ($result as $key => $data) {
		$empid = $data["qEmpId"];
		$empFullname = $data["qFullname"];
		$arrLeaves = array();
		$arrOvertime = array();
		$arrHolidays = array();
        $tabsences  = $dispTotalLateToday = $dispTotalLateTH = $dispHalfDayToday = $dispFailureToLog = $dispTotalAbsentToday = $dispTotalUndertime = $disptotalut = $disptotallate = $disptotalTH = 0;

		$indSummary = $this->attendance->giveIndividualSummary($from_date, $to_date, $empid);
		foreach ($indSummary as $key => $entry) {
           
           $intervalfail = 0;
           // sched for in and out interval of 0 and 1 minute will be mark as absent and failure to log..
            if(!empty($entry["queLogin"]) && $entry["queLogout"]){
                $to_time = strtotime($entry["queLogout"]);
                $from_time = strtotime($entry["queLogin"]);
                if(round(abs($to_time - $from_time) / 60,2) <= 1){
                    $dispFailureToLog += 1;
                    $intervalfail     += 1;
                    if(empty($dispTotalAbsentToday) && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $intervalfail == 0) $dispTotalAbsentToday += 1;
                }
            }
            
           if($entry["queFailureToLog"] == 0 && $intervalfail ==0 && $entry["queIsHoliday"] == 0){
            $dispTotalAbsentToday += ($entry["queTotalAbsentToday"] > 0 && $entry["queIsHoliday"] == 0 && date('D',strtotime($entry["queLogDate"]))<>'Thu') ? $entry["queTotalAbsentToday"] : "";
           }
           
			// get all leaves
			if ($entry["queIsOnLeave"] > 0 && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $intervalfail ==0) {
				array_push($arrLeaves, $entry["queIsOnLeave"]);
			}
			//get all holidays
			if ($entry["queIsHoliday"] > 0) {
				array_push($arrHolidays, $entry["queIsHoliday"]);
			}
			// get all overtime
			if (($this->time->hoursToMinutes($entry["queOvertime"])) > 0 && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $intervalfail ==0) {
				array_push($arrOvertime, ($this->time->hoursToMinutes($entry["queOvertime"])));
			}
            
            $dispFailureToLog += ($entry["queFailureToLog"] > 0) ? $entry["queFailureToLog"] : "";

            $ut = ($this->time->hoursToMinutes($entry["queUndertime"]) > 0 && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $intervalfail ==0) ? $this->time->hoursToMinutes($entry["queUndertime"]) : 0;
            // get all undertime
            #if($isFlexi == TRUE)
                #$dispTotalUndertime += 0;
            #else{
            if(($this->time->hoursToMinutes($entry["queUndertime"]) <= 75) && $entry["queIsHoliday"] == 0 && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)){
                $dispTotalUndertime += ($this->time->hoursToMinutes($entry["queUndertime"]) > 0) ? $this->time->hoursToMinutes($entry["queUndertime"]) : 0;
                if($ut > 0 && $ut <= 75) $disptotalut++;
            }
            #}
            
            if($entry["queHalfDayToday"] > 0 && $entry["queIsHoliday"] == 0){
            $dispTotalLateToday += 0;
            $dispTotalLateTH += 0;
            }else{
                if($ut < 75 && $entry["queIsHoliday"] == 0){
                    if(!empty($entry["queLogout"]) && ($entry["queLogout"] > $entry["queTimeToBeAbsent2"])){
                        $dispTotalLateToday += ($this->time->hoursToMinutes($entry["queTotalLateToday"]) > 0)
                          ? $this->time->hoursToMinutes($entry["queTotalLateToday"]) : "";
                        if(($this->time->hoursToMinutes($entry["queTotalLateToday"]) > 0)) $disptotallate++;
                        
                        $dispTotalLateTH += ($this->time->hoursToMinutes($entry["queHalfTHLate"]) > 0)
                          ? $this->time->hoursToMinutes($entry["queHalfTHLate"]) : "";
                        if(($this->time->hoursToMinutes($entry["queHalfTHLate"]) > 0) && !$isFlexi) $disptotalTH++;
                    }
                }
            }   
            
        
        if($entry["queIsHoliday"] == 0 && !empty($entry["queLogout"]) && ($entry["queLogout"] < $entry["queTimeToBeAbsent2"]) && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)){
        $dispTotalAbsentToday += 1;
        }
        
        //get halfday
        if(($entry["queFailureToLog"] == 0 && $intervalfail ==0) && $entry["queIsHoliday"] == 0 && !empty($entry["queLogout"])) 
            $dispHalfDayToday += ($entry["queHalfDayToday"] > 0) ? $entry["queHalfDayToday"] : "";
            
        if(!empty($entry["queLogout"]) && ($entry["queLogout"] < $entry["queTimeToBeAbsent2"]))
            $dispHalfDayToday += 1;
                                    
        if($ut > 75 && $entry["queIsHoliday"] == 0 && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)) $dispHalfDayToday += 1;
        
        # Thursday absent will count as halfday..
        if($entry["queHalfDayToday"] == 0)  $dispHalfDayToday += (date('D',strtotime($entry["queLogDate"]))=='Thu' && $entry["queTotalAbsentToday"] > 0) ? $entry["queTotalAbsentToday"] : "";
            
		}// end foreach
        
        $disptotallate      -=  $disptotalTH;
        if($disptotallate < 0)  $disptotallate = 0;

        $this->db->query("INSERT INTO employee_att_summary 
                    (employeeid,dfrom,dto,tlate,tminlate,tthlate,tovertime,tminovertime,tearlydismissal,tearlymindismissal,tabsent,tleave,thalfday,tfailuretolog,tnoholiday,user) 
                                                VALUES 
                    ('$empid','$from_date','$to_date','$disptotallate','$dispTotalLateToday','$disptotalTH','".count($arrOvertime)."','".array_sum($arrOvertime)."','$disptotalut','$dispTotalUndertime','$dispTotalAbsentToday','".array_sum($arrLeaves)."','$dispHalfDayToday','$dispFailureToLog','".count($arrHolidays)."','".$this->session->userdata("username")."')");

	} // end foreach
}
?> 		