<?php
/**
 * @author Justin
 * @copyright 2015
 */

$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$dept = $deptid;
$edata = $edata;

$dateRange      = $this->time->createRangeToDisplay($from_date, $to_date);
$departments    = $this->extras->showdepartment();
$result         = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept);

$isFlexi = false;
$flexi = $this->employee->getindividualemployee($empid);
foreach($flexi as $row)    $isFlexi = (bool)$row->isFlexi;
?>

<h2>Payroll Deduction</h2>
<p><?= $dateRange; ?></p>
<p><?= ($dept != "") ? $departments[$dept] : ""; ?></p>
<div class="well-content">
    <div class="align_left" style="margin-bottom: 3px; font-color: red;" id="success" hidden=""></div>
    <?if($dcut){?>
        <div class="align_right" style="margin-bottom: 3px;" id="failed"><a href="#" class="btn btn-primary" id="docutoff">Do Cut-Off</a></div>
    <?}?>
	<table class="table table-bordered table-hover table-striped datatable" id="tdeduct">
		<thead>
			<tr>
				<th class="align_center">Employee ID</th>
				<th class="align_center">Employee Name</th>
				<th class="align_center">Late Frequency</th>
				<th class="align_center">Thursday Late Frequency</th>
                <th class="align_center">Minutes Late</th>
                <th class="align_center">Early Dismissal</th>
                <th class="align_center">Half Day</th>
                <th class="align_center">Whole Day Absences</th>
                <th class="align_center">Total Absences</th>
                <th class="align_center">Failure to Log In/Out</th>
                <th class="align_center">10 day attendance Bonus Balance</th>
                <th class="align_center">Overtime</th>
			</tr>
		</thead>
		<tbody>
<?
	if (count($result) > 0) {
		$empDept = "";
		foreach ($result as $key => $datum) {
			$theId  = $datum["qEmpId"];
            $deptid = $this->employee->getindividualdept($theId);
            $arrLeaves = array();
    		$arrOvertime = array();
    		$arrHolidays = array();
            $lbal   = $this->attendance->checkLeaveBalance($theId);
			$tabsences  = $dispTotalLateToday = $dispTotalLateTH = $dispHalfDayToday = $dispFailureToLog = $dispTotalAbsentToday = $dispTotalUndertime = $disptotalut = $disptotallate = $disptotalTH = 0;
            
			$empsumm = $this->attendance->giveIndividualSummary($from_date, $to_date, $theId, $edata);
			foreach ($empsumm as $key => $entry) {
			    $intervalfail = 0;
               // sched for in and out interval of 0 and 1 minute will be mark as absent and failure to log..
                if(!empty($entry["queLogin"]) && $entry["queLogout"]){
                    $to_time = strtotime($entry["queLogout"]);
                    $from_time = strtotime($entry["queLogin"]);
                    if(round(abs($to_time - $from_time) / 60,2) <= 1){
                        $dispFailureToLog += 1;
                        $intervalfail     += 1;
                        #if(empty($dispTotalAbsentToday) && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $intervalfail == 0) $dispTotalAbsentToday += 1;
                        if ($entry["queTotalAbsentToday"] == 0 && $entry["queIsHoliday"] == 0 && $entry["queFailureToLog"] == 0 && $dispFailureToLog == 0) $dispTotalAbsentToday+=1;                        
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
                #if(($this->time->hoursToMinutes($entry["queUndertime"]) <= 75) && $entry["queIsHoliday"] == 0 && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)){
                if(($this->time->hoursToMinutes($entry["queUndertime"]) <= $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) ) && $entry["queIsHoliday"] == 0 && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)){
                    $dispTotalUndertime += ($this->time->hoursToMinutes($entry["queUndertime"]) > 0) ? $this->time->hoursToMinutes($entry["queUndertime"]) : 0;
                    #if($ut > 0 && $ut <= 75) $disptotalut++;
                    if($ut > 0 && $ut <= $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) ) $disptotalut++;
                }
                #}
                /*
                if($entry["queHalfDayToday"] > 0 && $entry["queIsHoliday"] == 0){
                $dispTotalLateToday += 0;
                $dispTotalLateTH += 0;
                }else{
                */
                if(($entry["queSchedStart"] != "" && $entry["queSchedStart"] != "00:00:00") && ($entry["queSchedEnd"] != "" && $entry["queSchedEnd"] != "00:00:00")){
                #if($ut < 75 && $entry["queIsHoliday"] == 0){
                #if($ut < $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) && $entry["queIsHoliday"] == 0){
                    #if(!empty($entry["queLogout"]) && ($entry["queLogout"] > $entry["queTimeToBeAbsent2"])){
                    if($entry["queLogin"] > $entry["queTimeToBeAbsent2"])   $exempt  = 1;   else  $exempt = 0;
                    
                        if(!$exempt){
                            $dispTotalLateToday += ($this->time->hoursToMinutes($entry["queTotalLateToday"]) > 0) ? $this->time->hoursToMinutes($entry["queTotalLateToday"]) : "";
                            if(($this->time->hoursToMinutes($entry["queTotalLateToday"]) > 0)) $disptotallate++;
                        }
                        $dispTotalLateTH += ($this->time->hoursToMinutes($entry["queHalfTHLate"]) > 0)
                          ? $this->time->hoursToMinutes($entry["queHalfTHLate"]) : "";
                        if(($this->time->hoursToMinutes($entry["queHalfTHLate"]) > 0)  && !$isFlexi) $disptotalTH++;
                        
                        $exempt = 0;
                    #}
                #}
                }
                
            
            if($entry["queIsHoliday"] == 0 && !empty($entry["queLogout"]) && ($entry["queLogout"] < $entry["queTimeToBeAbsent2"]) && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)){
            $dispTotalAbsentToday += 1;
            }
            
            //get halfday
            if(($entry["queFailureToLog"] == 0 && $intervalfail ==0) && $entry["queIsHoliday"] == 0 && !empty($entry["queLogout"])) 
                $dispHalfDayToday += ($entry["queHalfDayToday"] > 0) ? $entry["queHalfDayToday"] : "";
                
            if(!empty($entry["queLogout"]) && ($entry["queLogout"] < $entry["queTimeToBeAbsent2"]))
                $dispHalfDayToday += 1;
                                        
            #if($ut > 75 && $entry["queIsHoliday"] == 0 && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)) $dispHalfDayToday += 1;
            if($ut > $this->employee->getindividualed($deptid,date("Y-m-d",strtotime($entry["queLogDate"]))) && $entry["queIsHoliday"] == 0 && ($entry["queFailureToLog"] == 0 && $intervalfail ==0)) $dispHalfDayToday += 1;
            
            # Thursday absent will count as halfday..
            if($entry["queHalfDayToday"] == 0)  $dispHalfDayToday += (date('D',strtotime($entry["queLogDate"]))=='Thu' && $entry["queTotalAbsentToday"] > 0) ? $entry["queTotalAbsentToday"] : "";                                
			}//end foreach
            $disptotallate      -=  $disptotalTH;
            if($disptotallate < 0)  $disptotallate = 0;
                        
			if ($empDept != $datum["qDepartment"]) {
?>
				<tr><td colspan="12"><strong><?= $datum["qDepartment"]; ?></strong></td></tr>
<?				
			}// end if
?>
			<tr class="idata">
				<td class="align_center"><?= $datum["qEmpId"]; ?></td>
				<td class="align_center"><?= $datum["qFullname"]; ?></td>
				<td class="align_center"><?= $disptotallate; ?></td>
                <td class="align_center"><?= $disptotalTH; ?></td>
				<td class="align_center"><?= $dispTotalLateToday;?></td>
                <td class="align_center"><?= $dispTotalUndertime; ?></td>
                <td class="align_center"><?= $dispHalfDayToday; ?></td>
                <td class="align_center"><?= $dispTotalAbsentToday; ?></td>
                <td class="align_center"><?= (($dispHalfDayToday / 2) + $dispTotalAbsentToday); ?></td>
                <td class="align_center"><?= $dispFailureToLog; ?></td>
                <td class="align_center"><?= $lbal?></td>
                <td class="align_center"><?= $this->extras->OtTime($theId,$from_date,$to_date)?></td>
			</tr>
<?
			$empDept = $datum["qDepartment"];
		}// end foreach
	}else{?>
		<tr class="error"><td colspan="10">NO RECORDS FOUND</td></tr>
<?
	} // END ELSE
?>			
		</tbody>
	</table>
</div>
<script>
$("#docutoff").click(function(){
   $(".idata").each(function(){
     var form_data = {
       dfrom        :   "<?=$from_date?>",
       dto          :   "<?=$to_date?>", 
       eid          :   $(this).find("td:eq(0)").text(),
       latefreq     :   $(this).find("td:eq(2)").text(),
       thlatefreq   :   $(this).find("td:eq(3)").text(),
       minslate     :   $(this).find("td:eq(4)").text(),
       earlyd       :   $(this).find("td:eq(5)").text(),
       halfd        :   $(this).find("td:eq(6)").text(),
       tabsences    :   $(this).find("td:eq(7)").text(),
       failtolog    :   $(this).find("td:eq(9)").text(),
       attbonus     :   $(this).find("td:eq(10)").text(),
       ottime       :   $(this).find("td:eq(11)").text(),
       model        :   "deduccutoffsaving"
     };
     $("#failed").hide();
     $("#success").hide();
        $.ajax({
            url     :   "<?=site_url("payroll_/loadmodelfunc")?>",
            type    :   "POST",
            data    :   form_data,
            success :   function(msg){
                $("#success").show().html("<b>"+msg+"</b>");
                $("#failed").show();
            }
        });
   });
});
</script>