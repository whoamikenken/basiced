<?php
/**
 * @author Justin
 * @copyright 2015
 */

$dateRange = "";
$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$dept = $deptid;
$edata = $edata;

$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$departments = $this->extras->showdepartment();
$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept);

$isFlexi = false;
$flexi = $this->employee->getindividualemployee($empid);
 foreach($flexi as $row)    $isFlexi = (bool)$row->isFlexi;
?>
<h2>Attendance Summary</h2>
<p><?php print($dateRange); ?></p>
<?php
  if ($dept != "") {
    print("<p>Department: ".$departments[$dept]."</p>");
  }
?>

<div id="displaylogs" class="well_content">
  <table class="table table-striped table-bordered table-hover datatable">
  	<thead>
  		<tr>
  			<th class="sorting_asc">Employee ID</th>
  			<th>Name</th>
  			<th>No. of Lates</th>
  			<th>Total no. of Minutes Late</th>
            <th>No. of Thu Lates</th>
  			<!--<th>Total no. of Thu Minutes Late</th>-->
  			<th>No. of Overtime</th>
  			<th>Total no. of minutes Overtime</th>
  			<th>No. of Early Dismissal</th>
  			<th>Total no. of minutes Early Dismissal</th>
  			<th>Absences</th>
  			<th>Leaves</th>
  			<th>Halfday(s)</th>
  			<th>Failure(s) to Log in/out</th>
  			<!--<th>Multiple Logs</th>-->
  			<th>No. of Holidays</th>
  		</tr>
  	</thead>
  	<tbody>
<?php
if (count($result) > 0) {
	$deptDisplay = "";
	foreach ($result as $key => $data) {
		$empid = $data["qEmpId"];
		$empFullname = $data["qFullname"];
        $deptid = $this->employee->getindividualdept($empid);
		$arrLeaves = array();
		$arrOvertime = array();
		$arrHolidays = array();
        $tabsences  = $dispTotalLateToday = $dispTotalLateTH = $dispHalfDayToday = $dispFailureToLog = $dispTotalAbsentToday = $dispTotalUndertime = $disptotalut = $disptotallate = $disptotalTH = 0;
                
		$indSummary = $this->attendance->giveIndividualSummary($from_date, $to_date, $empid, $edata);
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
			if ($entry["queIsOnLeave"] > 0 && $entry["queIsHoliday"] == 0 /*&& $entry["queFailureToLog"] == 0*/ && $intervalfail ==0) {
				array_push($arrLeaves, $entry["queIsOnLeave"]);
			}
			//get all holidays
			if ($entry["queIsHoliday"] > 0) {
				array_push($arrHolidays, $entry["queIsHoliday"]);
			}
			// get all overtime
			if (($this->time->hoursToMinutes($entry["queOvertime"])) > 0 && $entry["queIsHoliday"] == 0 /*&& $entry["queFailureToLog"] == 0*/ && $intervalfail ==0) {
				array_push($arrOvertime, ($this->time->hoursToMinutes($entry["queOvertime"])));
			}
            
            $dispFailureToLog += ($entry["queFailureToLog"] > 0) ? $entry["queFailureToLog"] : "";

            $ut = ($this->time->hoursToMinutes($entry["queUndertime"]) > 0 && $entry["queIsHoliday"] == 0 /*&& $entry["queFailureToLog"] == 0*/ && $intervalfail ==0) ? $this->time->hoursToMinutes($entry["queUndertime"]) : 0;
            // get all undertime
            #if(($this->time->hoursToMinutes($entry["queUndertime"]) <= 75) && $entry["queIsHoliday"] == 0 && (/*$entry["queFailureToLog"] == 0 && */$intervalfail ==0)){
            if(($this->time->hoursToMinutes($entry["queUndertime"]) <= $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) ) && $entry["queIsHoliday"] == 0 && (/*$entry["queFailureToLog"] == 0 && */$intervalfail ==0)){
                $dispTotalUndertime += ($this->time->hoursToMinutes($entry["queUndertime"]) > 0) ? $this->time->hoursToMinutes($entry["queUndertime"]) : 0;
                #if($ut > 0 && $ut <= 75) $disptotalut++;
                if($ut > 0 && $ut <= $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) ) $disptotalut++;
            }
            
            if(($entry["queSchedStart"] != "" && $entry["queSchedStart"] != "00:00:00") && ($entry["queSchedEnd"] != "" && $entry["queSchedEnd"] != "00:00:00")){
              #  if($entry["queHalfDayToday"] > 0 && $entry["queIsHoliday"] == 0){
              #  $dispTotalLateToday += 0;
              #  $dispTotalLateTH += 0;
              #  }else{
                    #if($ut < 75 && $entry["queIsHoliday"] == 0){
                        #if($ut < $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) && $entry["queIsHoliday"] == 0){
                        if($entry["queIsHoliday"] == 0){
                        
                            if($entry["queLogin"] > $entry["queTimeToBeAbsent2"])   $exempt  = 1;   else  $exempt = 0;                         
                            
                            if(!$exempt){    
                                $dispTotalLateToday += ($this->time->hoursToMinutes($entry["queTotalLateToday"]) > 0) ? $this->time->hoursToMinutes($entry["queTotalLateToday"]) : "";
                                if(($this->time->hoursToMinutes($entry["queTotalLateToday"]) > 0)) $disptotallate++;
                            }
                            
                            $dispTotalLateTH += ($this->time->hoursToMinutes($entry["queHalfTHLate"]) > 0)
                              ? $this->time->hoursToMinutes($entry["queHalfTHLate"]) : "";
                            if(($this->time->hoursToMinutes($entry["queHalfTHLate"]) > 0) && !$isFlexi) $disptotalTH++;
                            
                            $exempt = 0;
                    }
              #  }  
            }
            
        
        if($entry["queIsHoliday"] == 0 && !empty($entry["queLogout"]) && ($entry["queLogout"] < $entry["queTimeToBeAbsent2"]) && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)){
        $dispTotalAbsentToday += 1;
        }
        
        //get halfday
        if((/*$entry["queFailureToLog"] == 0 && */$intervalfail ==0) && $entry["queIsHoliday"] == 0/* && !empty($entry["queLogout"])*/) 
            $dispHalfDayToday += ($entry["queHalfDayToday"] > 0) ? $entry["queHalfDayToday"] : "";
            
        if(!empty($entry["queLogout"]) && ($entry["queLogout"] < $entry["queTimeToBeAbsent2"]))
            $dispHalfDayToday += 1;
                                    
        #if($ut > 75 && $entry["queIsHoliday"] == 0 && (/*$entry["queFailureToLog"] == 0 && */$intervalfail ==0)) $dispHalfDayToday += 1;
        if($ut > $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) && $entry["queIsHoliday"] == 0 && (/*$entry["queFailureToLog"] == 0 && */$intervalfail ==0)) $dispHalfDayToday += 1;
        
        # Thursday absent will count as halfday..
        if($entry["queHalfDayToday"] == 0)  $dispHalfDayToday += (date('D',strtotime($entry["queLogDate"]))=='Thu' && $entry["queTotalAbsentToday"] > 0) ? $entry["queTotalAbsentToday"] : "";
            
		}// end foreach
        
        // Total Late - Total Late Thursday
        $disptotallate      -=  $disptotalTH;
        if($disptotallate < 0)  $disptotallate = 0;
        #$dispTotalLateToday -=  $dispTotalLateTH;

		if ($deptDisplay != $data["qDepartment"]) {
?>
	<tr><td colspan="13"><?php print("<p>Department: ".$data["qDepartment"]."</p>"); ?></td></tr>
<?php
		}// end if
?>
	<tr>
		<td><?php print($empid); ?></td>
		<td><?php print($empFullname); ?></td>
        <td><?php print($disptotallate)?></td>
        <td><?php print($dispTotalLateToday)?></td>
        <td><?php print($disptotalTH)?></td>
        <!--<td><?php print($dispTotalLateTH)?></td>-->
		<td><?php print(count($arrOvertime)); ?></td>
		<td><?php print(array_sum($arrOvertime)); ?></td>
        <td><?php print($disptotalut)?></td>
        <td><?php print($dispTotalUndertime)?></td>
        <td><?php print($dispTotalAbsentToday)?></td>        
		<td><?php print(array_sum($arrLeaves)); ?></td>
        <td><?php print($dispHalfDayToday)?></td>
        <td><?php print($dispFailureToLog)?></td>        
		<td><?php print(count($arrHolidays)); ?></td>
	</tr>
<?php
		$deptDisplay = $data["qDepartment"];
	} // end foreach
}else{
?>
<tr class="error"><td colspan="14">NO RECORDS FOUND</td></tr>
<?
}// end else
?>  		
  	</tbody>
  </table>
</div>