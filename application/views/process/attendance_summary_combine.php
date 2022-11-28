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
$edata = $edata;

$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$departments = $this->extras->showdepartment();
$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept, $tnt, $estatus);
#$teachingtype = ($empid ? $this->employee->getempteachingtype($empid) : $tnt);
?>
<h2>Attendance Summary</h2>
<p><?=$dateRange?></p>
<?
  if ($dept != "") {
    print("<p>Department: ".$departments[$dept]."</p>");
  }
?>
<style>
#attstbl tr th,#asctblnt tr th{
    background-color: #2e5266;
    color: #ffffff;
}
</style>
<?if($tnt == "teaching"){ // Teaching?>
<div id="attstbl" class="well_content">
  <table class="table table-striped table-bordered table-hover datatable">
  	<thead>
  		<tr>
  			<th class="sorting_asc" rowspan="2">Employee ID</th>
  			<th rowspan="2">Name</th>
            <th class="align_center" colspan="2">No. of late/UT (hr:min)</th>
            <th class="align_center" >Absent</th>
            <th class="align_center" colspan="3">Leaves</th>
            <th class="align_center" rowspan="2" >Remarks/Others</th>
            <th class="align_center" colspan="2">Total Deduction</th>
            <th class="align_center" rowspan="2" >Holiday</th>
  		</tr>
        <tr>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
            <th class="align_center">Subject</th>
            <th class="align_center">Emergency</th>
            <th class="align_center">Vacation</th>
            <th class="align_center">Sick</th>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
        </tr>
  	</thead>
  	<tbody>
<?
if (count($result) > 0) {
	$deptDisplay = "";
	foreach ($result as $key => $data) {
		$empid = $data["qEmpId"];
		$empFullname = $data["qFullname"];
        $deptid = $this->employee->getindividualdept($empid);
        
        $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
            $x = $tlec = $tlab = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = ""; 
            foreach ($qdate as $rdate) {
                $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
                $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                $countrow = $sched->num_rows();
                
                if($countrow > 0){
                    $tempsched = "";
                    foreach($sched->result() as $rsched){
                        if($tempsched == $dispLogDate)  $dispLogDate = "";
                        $stime = $rsched->starttime;
                        $etime = $rsched->endtime; 
                        $type  = $rsched->leclab;
                        
                        // Holiday
                        $holiday = $this->attcompute->isHoliday($rdate->dte);
                        
                        // logtime
                        list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                        
                        // Leave
                        list($el,$vl,$sl,$ol)     = $this->attcompute->displayLeave($empid,$rdate->dte);
                        
                        // Absent
                        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte);
                        if($el || $vl || $sl || $ol || $holiday) $absent = "";
                        
                        // Late / Undertime
                        list($lateutlec,$lateutlab,$tschedlec,$tschedlab) = $this->attcompute->displayLateUT($stime,$etime,$login,$logout,$type,$absent);
                        if($el || $vl || $sl || $ol || $holiday){
                            $lateutlec = $lateutlab = $tschedlec = $tschedlab = "";
                        }
                        
                        /*
                         * Total
                         */ 
                        
                        // Absent
                        $tabsent  += $absent;
                        
                        // Late / UT
                        if($tlec){
                            $secs  = strtotime($lateutlec)-strtotime("00:00:00");
                            if($secs>0) $tlec = date("H:i",strtotime($tlec)+$secs);
                        }else
                            $tlec    = $lateutlec;
                            
                        if($tlab){
                            $secs  = strtotime($lateutlab)-strtotime("00:00:00");
                            if($secs>0) $tlab = date("H:i",strtotime($tlab)+$secs);
                        }else
                            $tlab    = $lateutlab;
                        
                        // Deductions
                        if($tschedlec){
                            $tdlec += $this->attcompute->exp_time($tschedlec);
                        }
                        if($tschedlab){
                            $tdlab += $this->attcompute->exp_time($tschedlab);
                        }
                        
                    }   // end foreach
                    
                    // total holiday
                    $tholiday += $holiday;
                                        
                    // Leave
                    if($dispLogDate){
                        $tel      += $el;
                        $tvl      += $vl;
                        $tsl      += $sl;
                        $tol      += $ol;
                    } // end if
                    
                } // end if
                
            } // end foreach
        
$tdlec = ($tdlec ? $this->attcompute->sec_to_hm($tdlec) : "");
$tdlab = ($tdlab ? $this->attcompute->sec_to_hm($tdlab) : "");

if ($deptDisplay != $data["qDepartment"]) {
?>
	<tr><td colspan="13"><?="<p>Department: ".$data["qDepartment"]."</p>"?></td></tr>
<?
}
?>
	<tr>
		<td><?=$empid?></td>
		<td><?=$empFullname?></td>
        <td class="align_center"><?=$tlec?></td>
        <td class="align_center"><?=$tlab?></td>
        <td class="align_center"><?=$tabsent?></td>
        <td class="align_center"><?=$tel?></td>
        <td class="align_center"><?=$tvl?></td>
        <td class="align_center"><?=$tsl?></td>
        <td class="align_center"><?=$tol?></td>
        <td class="align_center"><?=$tdlec?></td>
        <td class="align_center"><?=$tdlab?></td>
        <td class="align_center"><?=$tholiday?></td>
	</tr>
    <?
    		$deptDisplay = $data["qDepartment"];
    	} // end foreach
    }
    ?>  		
  	</tbody>
  </table>
</div>

<!-- Non Teaching -->
<?}else{?>

    <div id="attstbl" class="well_content">
      <table class="table table-striped table-bordered table-hover datatable" id="asctblnt">
        <thead>
            <tr>
                <th class="sorting_asc" rowspan="2">Employee ID</th>
  			    <th rowspan="2">Name</th>
                <th class="align_center" colspan="3">Overtime (hr:min)</th>
                <th class="align_center">Late/Undertime</th>
                <th class="align_center" rowspan="2">Absent</th>                        
                <th class="align_center" colspan="3">Leaves</th>
                <th class="align_center" rowspan="2" >Remarks/Others</th>
                <th class="align_center" rowspan="2" >Holiday</th>
            </tr>
            <tr>
                <th class="align_center">Regular</th>
                <th class="align_center">Rest</th>
                <th class="align_center">Holiday</th>
                <th class="align_center">Hr:min</th>            
                <th class="align_center">Emergency</th>
                <th class="align_center">Vacation</th>
                <th class="align_center">Sick</th>
            </tr>
        </thead>
      	<tbody>
    <?
    if (count($result) > 0) {
    	$deptDisplay = "";
    	foreach ($result as $key => $data) {
    		$empid = $data["qEmpId"];
    		$empFullname = $data["qFullname"];
            $deptid = $this->employee->getindividualdept($empid);
            
            $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
            
                $x = $totr = $totrest = $tothol =$tlec = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = ""; 
                foreach ($qdate as $rdate) {
                $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
                $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                $countrow = $sched->num_rows();
                
                if($x%2 == 0)   $color = " style='background-color: white;'";
                else            $color = " style='background-color: #f2f2f2;'";
                $x++;
                
                if($countrow > 0){
                    $tempsched = "";
                    foreach($sched->result() as $rsched){
                        if($tempsched == $dispLogDate)  $dispLogDate = "";
                        $stime  = $rsched->starttime;
                        $etime  = $rsched->endtime; 
                        $type   = $rsched->leclab;
                        $earlyd = $rsched->early_dismissal;
                        
                        // Holiday
                        $holiday = $this->attcompute->isHoliday($rdate->dte); 
                        
                        // logtime
                        list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                        
                        // Overtime
                        list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                                
                        // Leave
                        list($el,$vl,$sl,$ol)     = $this->attcompute->displayLeave($empid,$rdate->dte);
                        
                        // Absent
                        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd);
                        if($el || $vl || $sl || $ol || $holiday) $absent = "";
                        
                        // Late / Undertime
                        $lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent);
                        if($el || $vl || $sl || $ol || $holiday)    $lateutlec = "";
                        
                        /*
                         * Total
                         */ 
                        // Absent
                        $tabsent  += $absent;
                        // Late / UT
                        if($lateutlec)  $tlec += $this->attcompute->exp_time($lateutlec); 
                    }   // end foreach
                    
                    // Leave
                    if($dispLogDate){
                        $tel      += $el;
                        $tvl      += $vl;
                        $tsl      += $sl;
                        $tol      += ($ol ? 1 : "");
                    }
                    
                    // total holiday
                    $tholiday += $holiday;
                    
                    /* Overtime */
                    // total regular
                    if($otreg){
                        $totr += $this->attcompute->exp_time($otreg);
                    }
                    // total saturday
                    if($otrest){
                        $totrest += $this->attcompute->exp_time($otrest);
                    }
                    // total holiday
                    if($othol){
                        $tothol += $this->attcompute->exp_time($othol);
                    }
                }else{
                    /* Overtime */
                    list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                    if($otreg)  $totr += $this->attcompute->exp_time($otreg);
                    if($otrest)  $totrest += $this->attcompute->exp_time($otrest);
                    if($othol)  $tothol += $this->attcompute->exp_time($othol);
                }
            } // end foreach
      $tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "");         
      $totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "");
      $totrest = ($totrest ? $this->attcompute->sec_to_hm($totrest) : ""); 
      $tothol = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "");
  		if ($deptDisplay != $data["qDepartment"]) {
    ?>
    	<tr><td colspan="13"><?="<p>Department: ".$data["qDepartment"]."</p>"?></td></tr>
        <?}?>
    	<tr>
    		<td><?=$empid?></td>
    		<td><?=$empFullname?></td>
            <td class="align_center"><?=$totr?></th>
            <td class="align_center"><?=$totrest?></th>
            <td class="align_center"><?=$tothol?></th>
            <td class="align_center"><?=$tlec?></th>
            <td class="align_center"><?=$tabsent?></th>
            <td class="align_center"><?=$tel?></th>
            <td class="align_center"><?=$tvl?></th>
            <td class="align_center"><?=$tsl?></th>
            <td class="align_center"><?=$tol?></th>
            <td class="align_center"><?=$tholiday?></th>
    	</tr>
        <?
      		$deptDisplay = $data["qDepartment"];
       	} // end foreach
     }
        ?>  		
      	</tbody>
      </table>
    </div>
<?}?>