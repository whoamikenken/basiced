<?php
/**
 * @author Justin
 * Copyright 2016
 */
$datedisplay = "";
$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$edata = $edata;
$deptid = $this->employee->getindividualdept($empid);
$datedisplay = $this->time->createRangeToDisplay($from_date,$to_date);
$teachingtype = $this->employee->getempteachingtype($empid);
?>
<style>
#indvtbl tr th,#indvtblnt tr th{
    background-color: #510051;
    color: #ADAD0E;
}
</style>
<?
$qdate = $this->attcompute->displayDateRange($from_date, $to_date);
if($teachingtype){  // Teaching
?>
<div class="well-content" style='border: transparent !important; '>

<h2>Attendance</h2>
<p><?=$datedisplay?></p>
<p><?=$this->employee->getfullname($empid)?></p>
<table class="table table-bordered datatable" id="indvtbl">
    <thead>
        <tr>
            <th rowspan="2" class="align_center">Date</th>
            <th class="align_center" colspan="2">Offidsadascial Time</th>
            <th class="align_center" colspan="2">Actual Log Time</th>
            <th rowspan="2" class="align_center">Overload</th>
            <th class="align_center" colspan="2">No. of late/UT (hr:min)</th>
            <th class="align_center" >Absent</th>
            <th class="align_center" colspan="3">Leaves</th>
            <th class="align_center" rowspan="2" >Remarks/Others</th>
            <th class="align_center" colspan="2">Total Deduction</th>
            <th class="align_center" rowspan="2" >Holiday</th>
        </tr>
        <tr>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
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
    <tbody id="employeelist">
<?
    $x = $tlec = $tlab = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = $tempOverload = $overload = $tOverload = $lastDayOfWeek = $firstDayOfWeek = $service_credit = ""; 
	$tempabsent = "";
	$firstDayOfWeek = $this->attcompute->getFirstDayOfWeek($empid);
	$lastDayOfWeek = $this->attcompute->getLastDayOfWeek($empid);
	
	if(date("l",strtotime($qdate[0]->dte) != $firstDayOfWeek))
	{
		$tempOverload = $this->attcompute->getPastDayOverload($empid,$qdate[0]->dte,$firstDayOfWeek,$edata);
	}
	
    foreach ($qdate as $rdate) {
    $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
    $sched = $this->attcompute->displaySched($empid,$rdate->dte);
	$schedquery = $this->db->last_query();
    $countrow = $sched->num_rows();
    // $withLog = $this->attcompute->withLog($empid,$rdate->dte);
	// $countrowWithLog = $sched->num_rows();
    
    if($x%2 == 0)   $color = " style='background-color: white;'";
    else            $color = " style='background-color: #f2f2f2;'";
    $x++;
    
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
        list($el,$vl,$sl,$ol,$oltype)     = $this->attcompute->displayLeave($empid,$rdate->dte);
		
        // Absent
        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte);
		if($oltype == "ABSENT") $absent = $absent;
        else if($el || $vl || $sl || $ol || $holiday) $absent = "";
		
        // Late / Undertime
        list($lateutlec,$lateutlab,$tschedlec,$tschedlab) = $this->attcompute->displayLateUT($stime,$etime,$login,$logout,$type,$absent);
        if($el || $vl || $sl || $ol || $oltype || $holiday){
            $lateutlec = $lateutlab = $tschedlec = $tschedlab = "";
        }
		
		if($holiday)
		{
			if($this->attcompute->isHolidayWithpay($rdate->dte) == "YES")
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
		
		// Service Credit
        $service_credit    = $this->attcompute->displayServiceCredit($empid,$rdate->dte);
		
		if($service_credit) $absent = 0;
		
		// Overload
		if(!$absent && !$lateutlec)
		{
			// $overload           = $this->attcompute->displayOverloadTime($stime,$etime,$login,$logout);
			// $tempOverload           += $this->attcompute->displayOverloadTime($login,$logout);
			$tempOverload           += $this->attcompute->displayOverloadTime($stime,$etime,$lateutlab);
			// var_dump(date('H:i',strtotime($overload)));
			
			// die;
		}
		else
		{
			// $overload = 0;
			$tempOverload += 0;
		}
		
		if($tempOverload > $this->attcompute->exp_time("30:00"))
		{
			$overload = $tempOverload - $this->attcompute->exp_time("30:00");
		}
        
        ?>

        <tr class="edata" <?=$color?>>
            <?if($dispLogDate){?>
                <td class="align_center" rowspan="<?=$countrow?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?> </td>
            <?}?>
            <td class="align_center" key="ss"><?=($stime ? date('h:i A',strtotime($stime)) : "--")?></td>
            <td class="align_center" key="es"><?=($stime ? date('h:i A',strtotime($etime)) : "--")?></td>
            <td class="align_center" key="ti" style='<?=($lateutlec && date('H:i',strtotime($stime)) < date('H:i',strtotime($login)))?"color:red":""?>'><?=(($login && !$absent) ? date("h:i A",strtotime($login)) : "--")?></td>
            <td class="align_center" key="to" style='<?=($lateutlec && date('H:i',strtotime($etime)) < date('H:i',strtotime($logout)))?"color:red":""?>'><?=(($logout && !$absent) ? date("h:i A",strtotime($logout)) : "--")?></td>
			<td class="align_center" key="overload"><?=($lastDayOfWeek == date("l",strtotime($rdate->dte)))?($overload?$this->attcompute->sec_to_hm($overload):""):""?></td>
			<td class="align_center" key="utlec"><?=$lateutlec?></td>
            <td class="align_center" key="utlab"><?=$lateutlab?></td>
            <td class="align_center" key="ab" style='<?=($absent)?"background-color: #ffe6e6;":""?>'><?=$absent?></td>
            <?if($dispLogDate){?>
                <td class="align_center" rowspan="<?=$countrow?>" key="el"><?=$el?></td>
                <td class="align_center" rowspan="<?=$countrow?>" key="vl"><?=$vl?></td>
                <td class="align_center" rowspan="<?=$countrow?>" key="sl"><?=$sl?></td>
                <td class="align_center" rowspan="<?=$countrow?>" key="ol"><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : ($q ? ($q == "1" ? "" : $q) : ""))?></td>
            <?}?>
            <td class="align_center" key="dlec"><?=$tschedlec?></td>
            <td class="align_center" key="dlab"><?=$tschedlab?></td>
            <?if($dispLogDate){?>
                <td class="align_center" rowspan="<?=$countrow?>"><?=$holiday?></td>
            <?}?>
        </tr>
        <?
            $tempsched = $dispLogDate;
            
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
            
            // Leave
            if($dispLogDate){
                $tel      += $el;
                $tvl      += $vl;
                $tsl      += $sl;
				$tol      += ($ol ? 1 : "") + ($q ? ($q == 1 ? "" : 1) : "") ;
            }
            
            // Deductions
            if($tschedlec){
                $tdlec += $this->attcompute->exp_time($tschedlec);
            }
            if($tschedlab){
                $tdlab += $this->attcompute->exp_time($tschedlab);
            }
            
        }
        // total holiday
        $tholiday += $holiday;
		
		if($lastDayOfWeek == date("l",strtotime($rdate->dte)))
		{
			$tOverload += $overload;
			$overload = $tempOverload = 0;
		}
    }
	else
	{
		$withLog = $this->attcompute->withLog($empid,$rdate->dte);
		$countrowWithLog = $sched->num_rows();
		if($countrowWithLog > 0)
		{
			$tempsched = "";
			foreach($withLog->result() as $rsched){
				if($tempsched == $dispLogDate)  $dispLogDate = "";
				$stime = $rsched->starttime;
				$etime = $rsched->endtime; 
				$type  = $rsched->leclab;
			?>
				<tr class="edata" <?=$color?>>
				<?if($dispLogDate){?>
					<td class="align_center" rowspan="<?=$countrow?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?> </td>
				<?}?>
				<td class="align_center" key="ss"><?=($stime ? date('h:i A',strtotime($stime)) : "--")?></td>
				<td class="align_center" key="es"><?=($stime ? date('h:i A',strtotime($etime)) : "--")?></td>
				<td class="align_center" key="ti" style='<?=($lateutlec && date('H:i',strtotime($stime)) < date('H:i',strtotime($login)))?"color:red":""?>'><?=(($login && !$absent) ? date("h:i A",strtotime($login)) : "--")?></td>
				<td class="align_center" key="to" style='<?=($lateutlec && date('H:i',strtotime($etime)) < date('H:i',strtotime($logout)))?"color:red":""?>'><?=(($logout && !$absent) ? date("h:i A",strtotime($logout)) : "--")?></td>
				<td class="align_center" key="overload"><?=($lastDayOfWeek == date("l",strtotime($rdate->dte)))?($overload?$this->attcompute->sec_to_hm($overload):""):""?></td>
				<td class="align_center" key="utlec"><?=$lateutlec?></td>
				<td class="align_center" key="utlab"><?=$lateutlab?></td>
				<td class="align_center" key="ab" style='<?=($absent)?"background-color: #ffe6e6;":""?>'><?=$absent?></td>
				<?if($dispLogDate){?>
					<td class="align_center" rowspan="<?=$countrow?>" key="el"><?=$el?></td>
					<td class="align_center" rowspan="<?=$countrow?>" key="vl"><?=$vl?></td>
					<td class="align_center" rowspan="<?=$countrow?>" key="sl"><?=$sl?></td>
					<td class="align_center" rowspan="<?=$countrow?>" key="ol"><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : ($q ? ($q == "1" ? "" : $q) : ""))?></td>
				<?}?>
				<td class="align_center" key="dlec"><?=$tschedlec?></td>
				<td class="align_center" key="dlab"><?=$tschedlab?></td>
				<?if($dispLogDate){?>
					<td class="align_center" rowspan="<?=$countrow?>"><?=$holiday?></td>
				<?}?>
			</tr>
			<?
			}
			
		}
	}
  }
  $tdlec = ($tdlec ? $this->attcompute->sec_to_hm($tdlec) : "");
  $tdlab = ($tdlab ? $this->attcompute->sec_to_hm($tdlab) : "");
  ?>
  <tr class="edata">
        <th class="align_right" colspan="5"><b>TOTAL</b></th>
            <th class="align_center"><?=$tOverload = ($tOverload ? $this->attcompute->sec_to_hm($tOverload) : "")?></th>
            <th class="align_center"><?=$tlec?></th>
            <th class="align_center"><?=$tlab?></th>
            <th class="align_center"><?=$tabsent?></th>
            <th class="align_center"><?=$tel?></th>
            <th class="align_center"><?=$tvl?></th>
            <th class="align_center"><?=$tsl?></th>
            <th class="align_center"></th>
            <th class="align_center"><?=$tdlec?></th>
            <th class="align_center"><?=$tdlab?></th>
            <th class="align_center"><?=$tholiday?></th>
      </tr>
    </tbody>
</table>
</div>

<!-- Non Teaching -->
<?}else{?>
    <div class="well-content" style='border: transparent !important;'>
    <h2>Attendance</h2>
    <p><?=$datedisplay?></p>
    <p><?=$this->employee->getfullname($empid)?></p>
    <table class="table table-bordered datatable" id="indvtblnt">
        <thead>
            <tr>
                <th rowspan="2" class="align_center">Date</th>
                <th class="align_center" colspan="2">Official Time</th>
                <th class="align_center" colspan="2">Actual Log Time</th>
				<!--<th rowspan="2" class="align_center">Overload</th>-->
                <th class="align_center" colspan="4">Overtime (hr:min)</th>
                <th class="align_center">Late/Undertime</th>
                <th class="align_center" rowspan="2">Absent</th>                        
                <th class="align_center" colspan="3">Leaves</th>
                <th class="align_center" rowspan="2" >Remarks/Others</th>
                <th class="align_center" rowspan="2" >Holiday</th>
            </tr>
            <tr>
                <th class="align_center">IN</th><th class="align_center">OUT</th>
                <th class="align_center">IN</th><th class="align_center">OUT</th>
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
        <tbody id="employeelist">
    <?
    $x = $totr = $totsat = $totsun = $tothol = $tlec = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = $pending = $tempOverload = $overload = $tOverload = $lastDayOfWeek = $service_credit = ""; 
    $tlec = 0 ;
	$tempabsent = "";
	foreach ($qdate as $rdate) {
    $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
    $sched = $this->attcompute->displaySched($empid,$rdate->dte);
    $countrow = $sched->num_rows();
    
    if($x%2 == 0)   $color = " style='background-color: white;'";
    else            $color = " style='background-color: #fafafa;'";
    $x++;
	
    if($countrow > 0){
        $tempsched = "";
        $seq = "";
        foreach($sched->result() as $rsched){
        if($tempsched == $dispLogDate){  $dispLogDate = "";}
        if($seq == ""){$seq = 1; }else{ $seq = 2;}
		
        $stime  = $rsched->starttime;
        $etime  = $rsched->endtime; 
        $tstart = $rsched->tardy_start; 
        $earlyd = $rsched->early_dismissal;
        
        // Holiday
        $holiday = $this->attcompute->isHoliday($rdate->dte); 
        
        // logtime
        list($login,$logout,$q)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq);
        
        // Overtime
        list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                
        // Leave
        list($el,$vl,$sl,$ol,$oltype)             = $this->attcompute->displayLeave($empid,$rdate->dte);
		
		// Leave Pending
        $pending             = $this->attcompute->displayPendingApp($empid,$rdate->dte);
        
        // Absent
        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd);
		if($oltype == "ABSENT") {$absent = $absent;}
        else if($el || $vl || $sl || $ol || $oltype || $holiday){ $absent = "";}
		
        // Late / Undertime
        $lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent,$teachingtype,$tstart);
		if($el || $vl || $sl || $ol || $oltype || $holiday) $lateutlec = "";
		
		if($holiday)
		{
			if($this->attcompute->isHolidayWithpay($rdate->dte) == "YES")
			{
				if($tempabsent)
				{
					$absent = 0.5;
				}
			}
			else
			{
				if(!$login && !$logout)
				{
					$absent = 0.5;
				}
			}
		}
		else
		{
			$tempabsent = $absent;
        }
		
		// Service Credit
        $service_credit    = $this->attcompute->displayServiceCredit($empid,$rdate->dte);
		
		if($service_credit) $absent = 0;
		
		// Overload
		// if(!$absent && !$lateutlec)
		// {
			// $tempOverload           += $this->attcompute->displayOverloadTime($login,$logout);
		// }
		// else
		// {
			// $tempOverload += 0;
		// }
		// $lastDayOfWeek = $this->attcompute->getLastDayOfWeek($empid);
		
		// if($tempOverload > $this->attcompute->exp_time("30:00"))
		// {
			// $overload = $tempOverload - $this->attcompute->exp_time("30:00");
		// }

        ?>
		
        <tr class="edata" <?=$color?>>
            <?if($dispLogDate){?>
                <td class="align_center" rowspan="<?=$countrow?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
            <?}?>
            <td class="align_center" key="ss" etype="nt"><?=($stime != "00:00:00" ? date('h:i A',strtotime($stime)) : "--")?></td>
            <td class="align_center" key="es"><?=($stime != "00:00:00" ? date('h:i A',strtotime($etime)) : "--")?></td>
			
			<!--<td class="align_center" key="ti"><?=(($login) ? date("h:i A",strtotime($login)) : "--")?></td>
            <td class="align_center" key="to"><?=(($logout) ? date("h:i A",strtotime($logout)) : "--")?></td>-->
			
            <td class="align_center" key="ti" style='<?=($lateutlec && date('H:i',strtotime($stime)) < date('H:i',strtotime($login)))?"color:red":""?>'><?=(($login && ( (date("H:i:s",strtotime($logout)) < $earlyd && date("H:i:s",strtotime($logout)) >= $stime) ? $absent : !$absent)) ? date("h:i A",strtotime($login)) : "--")?></td>
            <td class="align_center" key="to" style='<?=($lateutlec && date('H:i',strtotime($etime)) > date('H:i',strtotime($logout)))?"color:red":""?>'><?=(($logout && ( (date("H:i:s",strtotime($logout)) < $earlyd && date("H:i:s",strtotime($logout)) >= $stime) ? $absent : !$absent)) ? date("h:i A",strtotime($logout)) : "--")?></td>
			
			<!--<td class="align_center" key="overload"><?=($lastDayOfWeek == date("l",strtotime($rdate->dte)))?($overload?$this->attcompute->sec_to_hm($overload):""):""?></td>-->
            <?if($dispLogDate){?>
                <td class="align_center" key="otr"   rowspan="<?=$countrow?>"><?=($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):""?></td>
                <td class="align_center" key="otsat" rowspan="<?=$countrow?>"><?=($otsat)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otsat)):""?></td>
                <td class="align_center" key="otsun" rowspan="<?=$countrow?>"><?=($otsun)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otsun)):""?></td>
                <td class="align_center" key="othol" rowspan="<?=$countrow?>"><?=($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):""?></td>
            <?}?>
            <td class="align_center" key="ut"><?=$lateutlec?></td>
            <td class="align_center" key="ab" style="<?=($absent)?"background-color: #ffe6e6;":""?>" ><?=$absent?></td>
            <?if($dispLogDate){?>
                <td class="align_center" rowspan="<?=$countrow?>" key="el"><?=$el?></td>
                <td class="align_center" rowspan="<?=$countrow?>" key="vl"><?=$vl?></td>
                <td class="align_center" rowspan="<?=$countrow?>" key="sl"><?=$sl?></td>
                <td class="align_center" rowspan="<?=$countrow?>" key="ol" style="<?=($pending)?'background-color:#b3d9ff;':''?>"><?=($pending)?"PENDING ".$pending:($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : ($q ? ($q == "1" ? "" : $q) : ""))?><?=$service_credit?"SERVICE CREDIT":""?></td><!--//($ol ? ($oltype=="DIRECT" ? $this->employeemod->othLeaveDesc($ol) : $oltype) : "")?>-->
            <?}?>
            <?if($dispLogDate){?>
                <td class="align_center" rowspan="<?=$countrow?>"><?=$holiday?></td>
            <?}?>
        </tr>
        <?
            $tempsched = $dispLogDate;
            
            /*
             * Total
             */ 
            
            // Absent
            $tabsent  += $absent;
            
            // Late / UT
            if($lateutlec){
                $tlec += $this->attcompute->exp_time($lateutlec);
            }
			
            // Leave
            if($dispLogDate){
				$tel      += $el;
				$tvl      += $vl;
				$tsl      += $sl;
				$tol      += ($ol ? 1 : "") + ($q ? ($q == 1 ? "" : 1) : "") ;
			}    
        } // end foreach
			   
        
        // total holiday
        $tholiday += $holiday;
        
        /* Overtime */
        // total regular
		if($otreg){
                    $totr += $this->attcompute->exp_time($otreg);
                }
        // if($totr){
            // $secs  = strtotime($otreg)-strtotime("00:00:00");
            // if($secs>0) $totr = date("H:i",strtotime($totr)+$secs);
        // }else
            // $totr    = $otreg;
        // total saturday
		if($otsat){
                    $totsat += $this->attcompute->exp_time($otsat);
                }
        // if($totsat){
            // $secs  = strtotime($otsat)-strtotime("00:00:00");
            // if($secs>0) $totsat = date("H:i",strtotime($totsat)+$secs);
        // }else
            // $totsat    = $otsat;
        // total sunday
		if($otsun){
                    $totsun += $this->attcompute->exp_time($otsun);
                }
        // if($totsun){
            // $secs  = strtotime($otsun)-strtotime("00:00:00");
            // if($secs>0) $totsun = date("H:i",strtotime($totsun)+$secs);
        // }else
            // $totsun    = $otsun;
		
		// total holiday
		if($othol){
                    $tothol += $this->attcompute->exp_time($othol);
                }
        // if($tothol){
            // $secs  = strtotime($othol)-strtotime("00:00:00");
            // if($secs>0) $tothol = date("H:i",strtotime($tothol)+$secs);
        // }else
            // $tothol    = $othol;
		
		//FOR TOTAL OVERLOAD
		// if(!$absent && !$lateutlec)
		// {
			// $tOverload += $this->attcompute->exp_time($overload);
		// }
		
		// if($lastDayOfWeek == date("l",strtotime($rdate->dte)))
		// {
			// $tOverload += $overload;
			// $overload = $tempOverload = 0;
		// }
        
    }else{
        // Overtime
        list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
        /* Overtime */
        // total regular
        if($otreg){
                    $totr += $this->attcompute->exp_time($otreg);
                }
        // if($totr){
            // $secs  = strtotime($otreg)-strtotime("00:00:00");
            // if($secs>0) $totr = date("H:i",strtotime($totr)+$secs);
        // }else
            // $totr    = $otreg;
        // total saturday
		if($otsat){
                    $totsat += $this->attcompute->exp_time($otsat);
                }
        // if($totsat){
            // $secs  = strtotime($otsat)-strtotime("00:00:00");
            // if($secs>0) $totsat = date("H:i",strtotime($totsat)+$secs);
        // }else
            // $totsat    = $otsat;
        // total sunday
		if($otsun){
                    $totsun += $this->attcompute->exp_time($otsun);
                }
        // if($totsun){
            // $secs  = strtotime($otsun)-strtotime("00:00:00");
            // if($secs>0) $totsun = date("H:i",strtotime($totsun)+$secs);
        // }else
            // $totsun    = $otsun;
		
		// total holiday
		if($othol){
                    $tothol += $this->attcompute->exp_time($othol);
                }
        // if($tothol){
            // $secs  = strtotime($othol)-strtotime("00:00:00");
            // if($secs>0) $tothol = date("H:i",strtotime($tothol)+$secs);
        // }else
            // $tothol    = $othol;
        if($otreg || $otsat || $otsun || $othol){
        ?>
        <tr class="edata" <?=$color?>>
            <?if($dispLogDate){?>
                <td class="align_center" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?> </td>
            <?}?>
            <td class="align_center" key="ss" etype="nt"></td>
            <td class="align_center" key="es"></td>
            <td class="align_center" key="ti"></td>
            <td class="align_center" key="to"></td>
			<td class="align_center" key="overload"></td>
            <?if($dispLogDate){?>
                <td class="align_center" key="otr"   ><?=$otreg?></td>
                <td class="align_center" key="otsat" ><?=$otsat?></td>
                <td class="align_center" key="otsun" ><?=$otsun?></td>
                <td class="align_center" key="othol" ><?=$othol?></td>
            <?}?>
            <td class="align_center" key="ut"></td>
            <td class="align_center" key="ab"></td>
            <?if($dispLogDate){?>
                <td class="align_center" key="el"></td>
                <td class="align_center" key="vl"></td>
                <td class="align_center" key="sl"></td>
                <td class="align_center" key="ol"></td>
            <?}?>
            <?if($dispLogDate){?>
                <td class="align_center"></td>
            <?}?>
        </tr>
        <?
       }
    }
  } // end foreach
  ?>
  <tr class="edata">
        <th class="align_right" colspan="5"><b>TOTAL</b></th>
            <!--<th class="align_center"><?=$tOverload = ($tOverload ? $this->attcompute->sec_to_hm($tOverload) : "")?></th>-->
            <th class="align_center"><?=$totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "")?></th>
            <th class="align_center"><?=$totsat   = ($totsat ? $this->attcompute->sec_to_hm($totsat) : "")?></th>
            <th class="align_center"><?=$totsun   = ($totsun ? $this->attcompute->sec_to_hm($totsun) : "")?></th>
            <th class="align_center"><?=$tothol   = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "")?></th>
            <th class="align_center"><?=$tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "")?></th>
            <th class="align_center"><?=$tabsent?></th>
            <th class="align_center"><?=$tel?></th>
            <th class="align_center"><?=$tvl?></th>
            <th class="align_center"><?=$tsl?></th>
            <th class="align_center"><?=$tol?></th>
            <th class="align_center"><?=$tholiday?></th>
      </tr>
    </tbody>
</table>
</div>
<?
	
}?>