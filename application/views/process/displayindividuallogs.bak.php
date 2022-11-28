<?php
/**
 * @Author Justin
 * Copyright 2015 
 */
 
$datedisplay = "";
$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$edata = $edata;
$deptid = $this->employee->getindividualdept($empid);

// create a display for range of date
$datedisplay = $this->time->createRangeToDisplay($from_date,$to_date);

// retrieve data from database
$results = $this->attendance->giveIndividualSummary($from_date, $to_date, $empid, $edata);

  foreach ($results as $key => $row) {
    if ($row["queFullName"] != "") {
        // return employee fullname
      $empFullname = $row["queFullName"];
      break;
    }
  }
$isFlexi = false;
$flexi = $this->employee->getindividualemployee($empid);
 foreach($flexi as $row)    $isFlexi = (bool)$row->isFlexi;

$teachingtype = $this->employee->getempteachingtype($empid);
?>
<div id="displaylogs" class="well-content" style='border: transparent !important;'>
<h2>Attendance</h2>
<p><?php print($datedisplay); ?></p>
<p><?php print((isset($empFullname) ) ? $empFullname : ""); ?></p>
<table class="table table-striped table-bordered table-hover datatable">
    <thead>
        <tr>
            <th rowspan="2" class="align_center">Date</th>
            <th class="align_center" colspan="2">Official Time</th>
            <th class="align_center" colspan="2">Actual Log Time</th>
            <?if($teachingtype){?>
            <th class="align_center" colspan="2">No. of late/UT (hr:min)</th>
            <th class="align_center" >Absent</th>
            <th class="align_center" colspan="3">Leaves</th>
            <th class="align_center" colspan="2">Total Deduction</th>
            <th class="align_center" rowspan="2" >Remarks/Others</th>
            <?}else{?>
            <th class="align_center" colspan="4">Overtime (hr:min)</th>
            <th class="align_center">Late/Undertime</th>
            <th class="align_center" rowspan="2">Absent</th>                        
            <th class="align_center" colspan="3">Leaves</th>
            <th class="align_center" rowspan="2" >Remarks/Others</th>
            <?}?>
        </tr>
        <tr>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
            <?if($teachingtype){?>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
            <th class="align_center">Day</th>
            <th class="align_center">Emergency</th>
            <th class="align_center">Vacation</th>
            <th class="align_center">Sick</th>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
            <?}else{?>
            <th class="align_center">Regular</th>
            <th class="align_center">Saturday</th>
            <th class="align_center">Sunday</th>
            <th class="align_center">Holiday</th>
            <th class="align_center">Hr:min</th>            
            <th class="align_center">Emergency</th>
            <th class="align_center">Vacation</th>
            <th class="align_center">Sick</th>
            <?}?>
        </tr>
    </thead>
    <tbody id="employeelist">
<?php
if (count($results) > 0) {
    // if there are records to retrieved, display them
  foreach ($results as $key => $row) {
    
    $dispLogDate = date("d-M (l)",strtotime($row["queLogDate"]));
    $dispSchedStart = ($row["queSchedStart"] != "" && $row["queSchedStart"] != "00:00:00") ? date("h:i A",strtotime($row["queSchedStart"])) : "--";
    $dispSchedEnd = ($row["queSchedEnd"] != "" && $row["queSchedEnd"] != "00:00:00") ? date("h:i A",strtotime($row["queSchedEnd"])) : "--";
    $dispLogin = ($row["queLogin"] != "") ? date("h:i:s A",strtotime($row["queLogin"])) : "--";
    $dispLogout = ($row["queLogout"] != "") ? date("h:i:s A",strtotime($row["queLogout"])) : "--";
    
    $dispTotalLateToday = ($this->time->hoursToMinutes($row["queTotalLateToday"]) > 0) ? $this->time->hoursToMinutes($row["queTotalLateToday"]) : "";
    
    $dispTotalUndertime = ($this->time->hoursToMinutes($row["queUndertime"]) > 0) ? $this->time->hoursToMinutes($row["queUndertime"]) : "";
    
    $dispTotalOvertime = ($this->time->hoursToMinutes($row["queOvertime"]) > 0) ? $this->time->hoursToMinutes($row["queOvertime"]) : "";
    
    $dispTotalAbsentToday = ($row["queTotalAbsentToday"] > 0 && date('D',strtotime($row["queLogDate"])) <> 'Thu') ? $row["queTotalAbsentToday"] : "";
    
    $dispIsOnLeave = ($row["queIsOnLeave"] > 0) ? $row["queIsOnLeave"] : "";
    $dispHalfDayToday = ($row["queHalfDayToday"] > 0) ? $row["queHalfDayToday"] : "";
    $dispFailureToLog = ($row["queFailureToLog"] > 0) ? $row["queFailureToLog"] : "";
    $dispMultipleLog = ($row["queMultipleLogFreq"]>0) ? $row["queMultipleLogFreq"] : "";
    $dispIsHoliday = ($row["queIsHoliday"] > 0) ? $row["queIsHoliday"] : "";
    
    if($dispIsHoliday == 0 && !empty($row["queLogout"]) && ($row["queLogout"] < $row["queTimeToBeAbsent2"])){
    $dispTotalAbsentToday += 1;
    $dispHalfDayToday      = "";
    $dispTotalLateToday    = "";
    }
    
    if($dispIsHoliday > 0){ 
        $dispTotalLateToday = $dispTotalUndertime = $dispTotalOvertime = $dispTotalAbsentToday = $dispHalfDayToday = "";
    } 
    
    
    ?>
      <tr class="edata">
        <td class="align_center"><?php print($dispLogDate); ?> </td>
        <td class="align_center"><?php print($dispSchedStart); ?></td>
        <td class="align_center"><?php print($dispSchedEnd); ?></td>
        <td class="align_center"><?php print($dispLogin); ?></td>
        <td class="align_center"><?php print($dispLogout); ?></td>
        <?if($teachingtype){?>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
        <?}else{?>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
        <?}?>
      </tr>
    <?php
  }
  ?>
  <tr class="edata">
        <td class="align_right" colspan="5"><b>TOTAL</b></td>
        <?if($teachingtype){?>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
        <?}else{?>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
            <td class="align_center"></td>
        <?}?>
      </tr>
  <?
}else{
    // if no records found,
?>
  <tr class="danger"><td colspan="14">NO RECORD FOUND..</td></tr>
<?php  
}
?>
    </tbody>
</table>

<?php 
#print("<pre>".$this->attendance->giveBaseQuery() . "</pre>"); 
?>
</div>