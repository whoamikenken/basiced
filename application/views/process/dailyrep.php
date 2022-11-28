<?php
/**
 * @author Justin
 * @copyright 2017
 */

$dateRange = "";
$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$dept = $deptid;
$edata = $edata;
$estatus = isset($estatus) ? $estatus : "";

$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$departments = $this->extras->showdepartment();
$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept, $tnt, $estatus);

if($from_date != $to_date){  echo "<h2 style='color: red;'>Date Range must be 1 day only.</h2>";exit();  } 
?>
<h2>Daily Report</h2>
<p><?=$dateRange?></p>
<p>Department: <?=$departments[$dept]?></p>
<p>Type: <span style="color: <?=($sortdata == "ABSENT" ? "red" : "green")?>;font-weight: bold;"><?=$sortdata ? $sortdata : "ALL"?></span></p>
<style>
#attstbl tr th,#asctblnt tr th{
    background-color: #510051;
    color: #ADAD0E;
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
                $stime = "";
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

if(!abs($stime)) continue;
if($sortdata == "LATE"   && ($tlec || $tlab) == 0) continue;
else if($sortdata == "ONTIME" && ($tlec || $tlab) >= 0 && $tabsent > 0) continue;
else if($sortdata == "ABSENT"   && $tabsent == 0) continue;

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
                <th class="align_center" colspan="4">Overtime (hr:min)</th>
                <th class="align_center">Late/Undertime</th>
                <th class="align_center" rowspan="2">Absent</th>                        
                <th class="align_center" colspan="3">Leaves</th>
                <th class="align_center" rowspan="2" >Remarks/Others</th>
                <th class="align_center" rowspan="2" >Holiday</th>
            </tr>
            <tr>
                <th class="align_center">Regular</th>
                <th class="align_center">Saturday</th>
                <th class="align_center">Sunday</th>
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
            
                $x = $totr = $totsat = $totsun = $tothol =$tlec = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = ""; 
                foreach ($qdate as $rdate) {
                $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
                $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                $countrow = $sched->num_rows();
                $stime = "";
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
                        list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                                
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
                    if($otsat){
                        $totsat += $this->attcompute->exp_time($otsat);
                    }
                    // total sunday
                    if($otsun){
                        $totsun += $this->attcompute->exp_time($otsun);
                    }
                    // total holiday
                    if($othol){
                        $tothol += $this->attcompute->exp_time($othol);
                    }
                }else{
                    /* Overtime */
                    list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                    if($otreg)  $totr += $this->attcompute->exp_time($otreg);
                    if($otsat)  $totsat += $this->attcompute->exp_time($otsat);
                    if($otsun)  $totsun += $this->attcompute->exp_time($otsun);
                    if($othol)  $tothol += $this->attcompute->exp_time($othol);
                }
            } // end foreach
      $tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "");         
      $totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "");
      $totsat = ($totsat ? $this->attcompute->sec_to_hm($totsat) : ""); 
      $totsun = ($totsun ? $this->attcompute->sec_to_hm($totsun) : ""); 
      $tothol = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "");
      
      if(!abs($stime)) continue;      
      if($sortdata == "LATE"   && ($tlec || $tlab) == 0) continue;
      else if($sortdata == "ONTIME" && ($tlec || $tlab) >= 0 && $tabsent > 0) continue;
      else if($sortdata == "ABSENT"   && $tabsent == 0) continue;
  		if ($deptDisplay != $data["qDepartment"]) {
    ?>
    	<tr><td colspan="13"><?="<p>Department: ".$data["qDepartment"]."</p>"?></td></tr>
        <?}?>
    	<tr>
    		<td><?=$empid?></td>
    		<td><?=$empFullname?></td>
            <td class="align_center"><?=$totr?></th>
            <td class="align_center"><?=$totsat?></th>
            <td class="align_center"><?=$totsun?></th>
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