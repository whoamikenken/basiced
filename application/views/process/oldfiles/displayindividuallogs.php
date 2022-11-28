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
    background-color: #343434;
    color: #ffffff;
}
</style>
<?
$qdate = $this->attcompute->displayDateRange($from_date, $to_date);
?>
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
		
	// Holiday
    $holiday = $this->attcompute->isHoliday($rdate->dte); 
	
    $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
    $sched = $this->attcompute->displaySched($empid,$rdate->dte);
    $countrow = $sched->num_rows();
    
    if($x%2 == 0)   $color = " style='background-color: white;'";
    else            $color = " style='background-color: #fafafa;'";
    $x++;
	
    if($countrow > 0){
        $tempsched = "";
        $seq = 0;
		$service_credit = null;
		$service_credit_used = 0;
		
		
        foreach($sched->result() as $rsched){
			//NOT FLEXIBLE
			if($rsched->flexible != "YES")
			{
				if($tempsched == $dispLogDate){  $dispLogDate = "";}
				$stime  = $rsched->starttime;
				$etime  = $rsched->endtime; 
				$tstart = $rsched->tardy_start; 
				$earlyd = $rsched->early_dismissal;
				
				$seq += 1;
				
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
				if($oltype == "ABSENT") $absent = $absent;
				// else if($el || $vl || $sl || $ol || $oltype || $holiday) $absent = "";
				else if($holiday) $absent = "";
				if (($vl+$sl+$el) >= 1)
				$absent = "";
				
				// Late / Undertime
				$lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent,$teachingtype,$tstart);
				if($el || $vl || $sl || $ol || $oltype || $holiday) $lateutlec = "";
				
				if($holiday){
					$holidayInfo = $this->attcompute->holidayInfo($rdate->dte);
				}
				
				if($holiday)
				{
					if($holidayInfo['withPay'] == "YES")
					{
						if($tempabsent)
						{
							$absent = $absent;
						}
					}
					else
					{
						if(!$login && !$logout)
						{
							$absent = $absent;
						}
					}
				}
				else
				{
					$tempabsent = $absent;
				}
				
				// Service Credit
				if(is_null($service_credit))
				{
					$service_credit    = $this->attcompute->displayServiceCredit($empid,$rdate->dte);
					$service_credit = $service_credit?$service_credit:null;
				}
				
				if($absent)
				{
					if($service_credit <> 0){
						if($service_credit >= $absent)
						{
							$service_credit -= $absent;
							$service_credit_used = $absent;
							$absent = "";
							$lateutlec = $lateutlab = $tschedlec = $tschedlab = "";
						}
						else
						{
							$absent -= $service_credit;
							$service_credit_used = $service_credit;
							$service_credit = "";
						}
					}
				}
				
				?>
				<tr class="edata" <?=$color?>>
					<?if($dispLogDate){?>
						<td class="align_center" rowspan="<?=$countrow?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
					<?}?>
					<td class="align_center" key="ss" etype="nt"><?=($stime != "00:00:00" ? date('h:i A',strtotime($stime)) : "--")?></td>
					<td class="align_center" key="es"><?=($stime != "00:00:00" ? date('h:i A',strtotime($etime)) : "--")?></td>
					
					<td class="align_center" key="ti" style='<?=($lateutlec && date('H:i',strtotime($stime)) < date('H:i',strtotime($login)))?"color:red":""?>'><?=(($login && ( (date("H:i:s",strtotime($logout)) < $earlyd && date("H:i:s",strtotime($logout)) >= $stime) ? $absent : !$absent)) ? date("h:i A",strtotime($login)) : "--")?></td>
					<td class="align_center" key="to" style='<?=($lateutlec && date('H:i',strtotime($etime)) > date('H:i',strtotime($logout)))?"color:red":""?>'><?=(($logout && ( (date("H:i:s",strtotime($logout)) < $earlyd && date("H:i:s",strtotime($logout)) >= $stime) ? $absent : !$absent)) ? date("h:i A",strtotime($logout)) : "--")?></td>
					
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
						<td class="align_center" rowspan="<?=$countrow?>" key="ol" style="<?=($pending)?'background-color:#b3d9ff;':''?>"><?=($pending)?"PENDING ".$pending:($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : ($q ? ($q == "1" ? "" : $q) : ""))?> <?=$service_credit?"SERVICE CREDIT":(!is_null($service_credit)?"SERVICE CREDIT":"")?> <?=$holiday?$holidayInfo["description"]:""?></td><!--//($ol ? ($oltype=="DIRECT" ? $this->employeemod->othLeaveDesc($ol) : $oltype) : "")?>-->
					<?}?>
					<?if($dispLogDate){?>
						<td class="align_center" rowspan="<?=$countrow?>"><?=$holiday?$holidayInfo['type']:""?></td>
					<?}?>
				</tr>
				<?
			}
			//FLEXIBLE
			else
			{
				$totalQ = 0;
				if($tempsched == $dispLogDate){  $dispLogDate = "";}
				$stime  = $rsched->starttime;
				$etime  = $rsched->endtime; 
				$tstart = $rsched->tardy_start; 
				$earlyd = $rsched->early_dismissal;
				
				// logtime
				$log = $this->attcompute->displayLogTimeFlexi($empid,$rdate->dte,$edata);
				
				// Leave
				list($el,$vl,$sl,$ol,$oltype)             = $this->attcompute->displayLeave($empid,$rdate->dte);
				
				// Absent
				$absent = $this->attcompute->displayAbsentFlexi($log,$rsched->hours,$rsched->mode,$empid,$rdate->dte);
				if($oltype == "ABSENT") $absent = $absent;
				else if($el || $vl || $sl || $ol || $holiday) $absent = "";
				
				// Late / Undertime
				$lateutlec = $this->attcompute->displayLateUTNTFlexi($log,$rsched->hours,$rsched->mode,$absent);
				if($el || $vl || $sl || $ol || $oltype || $holiday) $lateutlec = "";
				if(date("Y-m-d",strtotime($lateutlec)) < $rdate->dte)
				{
					$lateutlec = $lateutlab = "";
				}
				
				if($holiday)
				{
					if($this->attcompute->isHolidayWithpay($rdate->dte) == "YES")
					{
						if($tempabsent)
						{
							$absent = $tempabsent;
						}
					}
					else
					{
						if(!$login && !$logout)
						{
							$absent = $tempabsent;
						}
					}
				}
				else
				{
					$tempabsent = $absent;
				}
				
				// Service Credit
				if(is_null($service_credit))
				{
					$service_credit    = $this->attcompute->displayServiceCredit($empid,$rdate->dte);
					$service_credit = $service_credit?$service_credit:null;
				}

				if($absent)
				{
					if($service_credit <> 0){
						if($service_credit >= $absent)
						{
							$service_credit -= $absent;
							$service_credit_used = $absent;
							$absent = "";
							$lateutlec = $lateutlab = $tschedlec = $tschedlab = "";
						}
						else
						{
							$absent -= $service_credit;
							$service_credit_used = $service_credit;
							$service_credit = "";
						}
					}
				}
				$login = $logout = $q = "";
				if(count($log) > 0)
				{
					for($i = 0;$i < count($log);$i++)
					{
						$login = $log[$i][0];
						$logout = $log[$i][1];
						$q = $log[$i][2];
						if($q) $totalQ++;
						?>
						<tr class="edata" <?=$color?>>
							<?if($stime && $etime){
								if($dispLogDate){?>
									<td class="align_center" rowspan="<?=count($log)?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
								<?}
							?>

								<td class="align_center" rowspan="<?=count($log)?>" key="ss" etype="nt"><?=($stime != "00:00:00" ? date('h:i A',strtotime($stime)) : "--")?></td>
								<td class="align_center" rowspan="<?=count($log)?>" key="es"><?=($stime != "00:00:00" ? date('h:i A',strtotime($etime)) : "--")?></td>
							<?}?>
							<td class="align_center" key="ti"><?=$login?date("h:i A",strtotime($login)):"--"?></td>
							<td class="align_center" key="to"><?=$logout?date("h:i A",strtotime($logout)):"--"?></td>
								
							<?if($stime && $etime){?>
								<?if($dispLogDate){?>
								<td class="align_center" key="otr"   rowspan="<?=count($log)?>"><?=($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):""?></td>
								<td class="align_center" key="otsat" rowspan="<?=count($log)?>"><?=($otsat)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otsat)):""?></td>
								<td class="align_center" key="otsun" rowspan="<?=count($log)?>"><?=($otsun)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otsun)):""?></td>
								<td class="align_center" key="othol" rowspan="<?=count($log)?>"><?=($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):""?></td>
								<?}
							?>
							<td class="align_center" key="ut" rowspan="<?=count($log)?>"><?=$lateutlec?></td>
							<td class="align_center" key="ab" rowspan="<?=count($log)?>" style="<?=($absent)?"background-color: #ffe6e6;":""?>" ><?=$absent?></td>
							<?if($dispLogDate){?>
								<td class="align_center" rowspan="<?=count($log)?>" key="el"><?=$el?></td>
								<td class="align_center" rowspan="<?=count($log)?>" key="vl"><?=$vl?></td>
								<td class="align_center" rowspan="<?=count($log)?>" key="sl"><?=$sl?></td>
								<td class="align_center" rowspan="<?=count($log)?>" key="ol" style="<?=($pending)?'background-color:#b3d9ff;':''?>"><?=($pending)?"PENDING ".$pending:($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : ($q ? ($q == "1" ? "" : $q) : ""))?> <?=$service_credit?"SERVICE CREDIT":(!is_null($service_credit)?"SERVICE CREDIT":"")?> <?=$holiday?$holidayInfo['description']:""?></td>
								<td class="align_center" rowspan="<?=count($log)?>"><?=$holiday?$holidayInfo['type']:""?></td>
								<?}
							}?>
						</tr>
						<?
						$stime = $etime = "";
					}
				}
				else
				{
					?>
					<tr class="edata" <?=$color?>>
						<?if($stime && $etime){
							if($dispLogDate){?>
								<td class="align_center" rowspan="<?=count($log)?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
							<?}
						?>
							<td class="align_center" rowspan="<?=count($log)?>" key="ss" etype="nt"><?=($stime != "00:00:00" ? date('h:i A',strtotime($stime)) : "--")?></td>
							<td class="align_center" rowspan="<?=count($log)?>" key="es"><?=($stime != "00:00:00" ? date('h:i A',strtotime($etime)) : "--")?></td>
						<?}?>
						<td class="align_center" key="ti"><?=$login?date("h:i A",strtotime($login)):"--"?></td>
						<td class="align_center" key="to"><?=$logout?date("h:i A",strtotime($logout)):"--"?></td>
								
						<?if($stime && $etime){?>
							<?if($dispLogDate){?>
							<td class="align_center" key="otr"   rowspan="<?=count($log)?>"><?=($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):""?></td>
							<td class="align_center" key="otsat" rowspan="<?=count($log)?>"><?=($otsat)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otsat)):""?></td>
							<td class="align_center" key="otsun" rowspan="<?=count($log)?>"><?=($otsun)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otsun)):""?></td>
							<td class="align_center" key="othol" rowspan="<?=count($log)?>"><?=($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):""?></td>
							<?}
						?>
						<td class="align_center" key="ut" rowspan="<?=count($log)?>"><?=$lateutlec?></td>
						<td class="align_center" key="ab" rowspan="<?=count($log)?>" style="<?=($absent)?"background-color: #ffe6e6;":""?>" ><?=$absent?></td>
						<?if($dispLogDate){?>
							<td class="align_center" rowspan="<?=count($log)?>" key="el"><?=$el?></td>
							<td class="align_center" rowspan="<?=count($log)?>" key="vl"><?=$vl?></td>
							<td class="align_center" rowspan="<?=count($log)?>" key="sl"><?=$sl?></td>
							<td class="align_center" rowspan="<?=count($log)?>" key="ol" style="<?=($pending)?'background-color:#b3d9ff;':''?>"><?=($pending)?"PENDING ".$pending:($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : ($q ? ($q == "1" ? "" : $q) : ""))?> <?=$service_credit?"SERVICE CREDIT":(!is_null($service_credit)?"SERVICE CREDIT":"")?> <?=$holiday?$holidayInfo['description']:""?></td>
							<td class="align_center" rowspan="<?=count($log)?>"><?=$holiday?$holidayInfo['type']:""?></td>
							<?}
						}?>
					</tr>
					<?
				}
			}

            $tempsched = $dispLogDate;
            
            /*
             * Total
             */ 
            
            // Absent
			if($absent){
                $tabsent += $this->attcompute->exp_time($absent);
            }
            
            // Late / UT
            if($lateutlec){
                $tlec += $this->attcompute->exp_time($lateutlec);
            }
			
            // Leave
			if($dispLogDate)
			{
				$tel      += $el;
				$tvl      += $vl;
				$tsl      += $sl;
				$tol      += ($ol ? 1 : "") + ($q ? ($q == 1 ? "" : 1) : "") ;
			}
			$tol	  += $service_credit_used;
			$service_credit_used = 0;
			
        } // end foreach
			   
        
        // total holiday
        $tholiday += $holiday;
        
        /* Overtime */
		if($otreg){
                    $totr += $this->attcompute->exp_time($otreg);
                }

		if($otsat){
                    $totsat += $this->attcompute->exp_time($otsat);
                }

		if($otsun){
                    $totsun += $this->attcompute->exp_time($otsun);
                }

		if($othol){
                    $tothol += $this->attcompute->exp_time($othol);
                }

        
    }else{
		$service_credit = null;
		$service_credit_used = 0;
		
		$totalQ = 0;
		$stime = "";
		$etime = ""; 
		
		$log = $this->attcompute->displayLogTimeFlexi($empid,$rdate->dte,$edata);
		
		// Leave
		list($el,$vl,$sl,$ol,$oltype)     = $this->attcompute->displayLeave($empid,$rdate->dte);
		
		// Overtime
		list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);

		if($otreg){
					$totr += $this->attcompute->exp_time($otreg);
				}

		if($otsat){
					$totsat += $this->attcompute->exp_time($otsat);
				}

		if($otsun){
					$totsun += $this->attcompute->exp_time($otsun);
				}

		if($othol){
					$tothol += $this->attcompute->exp_time($othol);
				}
		
		if(count($log)> 0)
		{
			$login = $logout = $q = "";
			$stime = $etime = "--";
			
			for($i = 0;$i < count($log);$i++)
			{
				$login = $log[$i][0];
				$logout = $log[$i][1];
				$q = $log[$i][2];
				if($q) $totalQ++;
		
				
				?>
				<tr class="edata" style='background-color: gray;color:white'>
					<?if($stime && $etime){?>
					<td class="align_center" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?> </td>
					<?}?>
					<td class="align_center" key="ss" etype="nt">--</td>
					<td class="align_center" key="es">--</td>
					<td class="align_center" key="ti"><?=$login?date("h:i A",strtotime($login)):"--"?></td>
					<td class="align_center" key="to"><?=$logout?date("h:i A",strtotime($logout)):"--"?></td>
					<?if($stime && $etime){?>
					<td class="align_center" key="otr"   ><?=$otreg?$otreg:"--"?></td>
					<td class="align_center" key="otsat" ><?=$otsat?$otsat:"--"?></td>
					<td class="align_center" key="otsun" ><?=$otsun?$otsun:"--"?></td>
					<td class="align_center" key="othol" ><?=$othol?$othol:"--"?></td>
					<td class="align_center" key="ut">--</td>
					<td class="align_center" key="ab">--</td>
					<td class="align_center" key="el">--</td>
					<td class="align_center" key="vl">--</td>
					<td class="align_center" key="sl">--</td>
					<td class="align_center" key="ol"><?=($pending)?"PENDING ".$pending:($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : "")?> <?=$service_credit?"SERVICE CREDIT":(!is_null($service_credit)?"SERVICE CREDIT":"")?></td>
					<td class="align_center"><?=$holiday?$holiday:"--"?></td>
					<?}?>
				</tr>
				<?
			}
       }
	   else
	   {
		   ?>
		  <tr class="edata" style='background-color: gray;color:white'>
					<td class="align_center" key="ld" kd="<?=$rdate->dte?>"><?=$dispLogDate?></td>
					<td class="align_center" key="ss" etype="nt">--</td>
					<td class="align_center" key="es">--</td>
					<td class="align_center" key="ti">--</td>
					<td class="align_center" key="to">--</td>
					<td class="align_center" key="otr"   ><?=$otreg?$otreg:"--"?></td>
					<td class="align_center" key="otsat" ><?=$otsat?$otsat:"--"?></td>
					<td class="align_center" key="otsun" ><?=$otsun?$otsun:"--"?></td>
					<td class="align_center" key="othol" ><?=$othol?$othol:"--"?></td>
					<td class="align_center" key="ut">--</td>
					<td class="align_center" key="ab">--</td>
					<td class="align_center" key="el">--</td>
					<td class="align_center" key="vl">--</td>
					<td class="align_center" key="sl">--</td>
					<td class="align_center" key="ol"><?=($pending)?"PENDING ".$pending:($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : "")?> <?=$service_credit?"SERVICE CREDIT":(!is_null($service_credit)?"SERVICE CREDIT":"")?></td>
					<td class="align_center"><?=$holiday?$holiday:"--"?></td>
				</tr>
			<?
	   }
    }
  }
  $tabsent = ($tabsent ? $this->attcompute->sec_to_hm($tabsent) : "");
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