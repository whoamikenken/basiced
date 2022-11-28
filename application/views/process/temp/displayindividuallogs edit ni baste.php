<?php
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
	if($teachingtype){
?>
		<div class="well-content" style='border: transparent !important;'>
			<h2>Attendance</h2>
			<p><?=$datedisplay?></p>
			<p><?=$this->employee->getfullname($empid)?></p>
			<table class="table table-bordered datatable" id="indvtbl">
				<thead>
					<tr>
						<th rowspan="2" class="align_center">Date</th>
						<th class="align_center" colspan="2">Official Time</th>
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
		$x = 0;
		$tlec = $tlab = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = $tempOverload = $overload = $tOverload = $lastDayOfWeek = $firstDayOfWeek = $service_credit = ""; 
		foreach ($qdate as $rdate){
			if($x%2 == 0)   $color = " style='background-color: white;'";
			else            $color = " style='background-color: #f2f2f2;'";
			$x++;
			
			$dispLogDate = date("d-M (l)",strtotime($rdate->dte));
			
			// Holiday
			$holiday = $this->attcompute->isHoliday($rdate->dte); 
			
			$sched = $this->attcompute->displaySched($empid,$rdate->dte);
			$schedquery = $this->db->last_query();
			if($sched->num_rows() > 0){
				$countrow = $sched->num_rows();
				$tempsched = "";
				$seq = 0;
				foreach($sched->result() as $rsched){
					if($tempsched == $dispLogDate)  $dispLogDate = "";
					$stime = $rsched->starttime;
					$etime = $rsched->endtime; 
					$type  = $rsched->leclab;
					$seq += 1;
					
					// logtime
					list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq);
				
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
						$tempOverload           += $this->attcompute->displayOverloadTime($stime,$etime,$lateutlab);
					}
					else
					{
						$tempOverload += 0;
					}
					
					if($tempOverload > $this->attcompute->exp_time("30:00"))
					{
						$overload = $tempOverload - $this->attcompute->exp_time("30:00");
					}
					
					?>
					<tr class="edata" <?=$color?>>
						<?if($dispLogDate){?>
							<td class="align_center" key="ld" kd="<?=$rdate->dte?>" rowspan="<?=$countrow?>"><?=$dispLogDate?></td>
						<?}?>
						<td class="align_center" key="ss"><?=($stime ? date('h:i A',strtotime($stime)) : "--")?></td>
						<td class="align_center" key="es"><?=($stime ? date('h:i A',strtotime($etime)) : "--")?></td>
						<td class="align_center" key="ti" style='<?=($lateutlec && date('H:i',strtotime($stime)) < date('H:i',strtotime($login)))?"color:red":""?>'><?=(($login && !$absent && $login != "0000-00-00 00:00:00") ? date("h:i A",strtotime($login)) : "--")?></td>
						<td class="align_center" key="to" style='<?=($lateutlec && date('H:i',strtotime($etime)) < date('H:i',strtotime($logout)))?"color:red":""?>'><?=(($logout && !$absent && $logout != "0000-00-00 00:00:00") ? date("h:i A",strtotime($logout)) : "--")?></td>
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
			}
			else{
				$withLog = $this->attcompute->withLog($empid,$rdate->dte);
				if($withLog->num_rows() > 0){
					$countrowWithLog = $sched->num_rows();
					$tempsched = "";
					foreach($withLog->result() as $log){
						if($tempsched == $dispLogDate)  $dispLogDate = "";
						$stime = "";
						$etime = "";
						$type  = "";
						
						?>
						<tr class="edata" <?=$color?>>
							<?if($dispLogDate){?>
								<td class="align_center" key="ld" kd="<?=$rdate->dte?>" rowspan="<?=$countrowWithLog?>"><?=$dispLogDate?> </td>
							<?}?>
							<td class="align_center" key="ss">--</td>
							<td class="align_center" key="es">--</td>
							<td class="align_center" key="ti" style=''></td>
							<td class="align_center" key="to" style=''></td>
							<td class="align_center" key="overload"></td>
							<td class="align_center" key="utlec"></td>
							<td class="align_center" key="utlab"></td>
							<td class="align_center" key="ab" style=''></td>
							<?if($dispLogDate){?>
								<td class="align_center" key="el" rowspan="<?=$countrowWithLog?>"></td>
								<td class="align_center" key="vl" rowspan="<?=$countrowWithLog?>"></td>
								<td class="align_center" key="sl" rowspan="<?=$countrowWithLog?>"></td>
								<td class="align_center" key="ol" rowspan="<?=$countrowWithLog?>"></td>
							<?}?>
							<td class="align_center" key="dlec"></td>
							<td class="align_center" key="dlab"></td>
							<?if($dispLogDate){?>
								<td class="align_center" rowspan="<?=$countrowWithLog?>"><?=$holiday?></td>
							<?}?>
						</tr>
						<?
						$tempsched = $dispLogDate;
					}
				}
				else
				{
					?>
					<tr class="edata" style='background-color: gray;color:white'>
						<td class="align_center" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?> </td>
						<td class="align_center" key="ss">--</td>
						<td class="align_center" key="es">--</td>
						<td class="align_center" key="ti" style=''>--</td>
						<td class="align_center" key="to" style=''>--</td>
						<td class="align_center" key="overload">--</td>
						<td class="align_center" key="utlec">--</td>
						<td class="align_center" key="utlab">--</td>
						<td class="align_center" key="ab" style=''>--</td>
						<td class="align_center" key="el">--</td>
						<td class="align_center" key="vl">--</td>
						<td class="align_center" key="sl">--</td>
						<td class="align_center" key="ol">--</td>
						<td class="align_center" key="dlec">--</td>
						<td class="align_center" key="dlab">--</td>
						<td class="align_center"><?=$holiday?></td>
					</tr>
					<?
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
<?
	}
	
?>