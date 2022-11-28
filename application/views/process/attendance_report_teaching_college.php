<?php

$qdate = $this->attcompute->displayDateRange($from_date, $to_date);
$totalhours = $this->attcompute->gettotalhours($empid,$from_date,$to_date);

?>



<div class="well-content" style='border: transparent !important;'>
	    <!-- PAULO 04-16-2018 -->
	  <div style='flex: 50% !important;'>
	  	<!-- <h2>Attendance</h2> -->
	    <p><?=$datedisplay?></p>
	    <p><?=$this->employee->getfullname($empid)?></p>
	  </div>
	  <? if( $this->employee->getHeadDeptCode($empid) !='' ){?>
	  <div style='flex: 50% !important;'>
	  	<p>TOTAL HOURS</p>
	  	<?foreach( $totalhours as $weekattendance ){
	  		$datestart = date("F d ",strtotime($weekattendance->datestart));
	  		$dateend = date("d Y",strtotime($weekattendance->dateend));
	  		$weekhours = $weekattendance->totalhours;
	  		echo "<p>$datestart - $dateend : <font color='red'>$weekhours</font></p>";
	  	}
	  }?>
	  </div>
	    <!-- END OF HYPERION21595 -->
<table class="table table-bordered datatable" id="indvtbl">
    <thead>
        <tr>
            <th rowspan="2" class="align_center">Date</th>
            <th class="align_center" colspan="2">Official Time</th>
            <th class="align_center" colspan="2">Actual Log Time</th>
            <th rowspan="2" class="align_center">Overload</th>
            <th rowspan="2" class="align_center">Subtitute</th>
            <th class="align_center" colspan="3">No. of late/UT (hr:min)</th>
            <th class="align_center" rowspan="2">Absent</th>
            <th class="align_center" colspan="4">Leaves</th>
            <th class="align_center" rowspan="2" >Remarks/Others</th>
            <th class="align_center" colspan="3">Total Deduction</th>
            <th class="align_center" rowspan="2" >Holiday</th>
            <!-- <th class="align_center" rowspan="2" >Total Per Day</th> -->
        </tr>
        <tr>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
            <th class="align_center">Admin</th>
            <!-- <th class="align_center">Subject</th> -->
            <th class="align_center">Family</th>
            <th class="align_center">Vacation</th>
            <th class="align_center">Sick</th>
            <th class="align_center">Other</th>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
            <th class="align_center">Admin</th>
        </tr>
    </thead>
    <tbody id="employeelist">
<?
    $x = $tlec = $tlab = $tadmin = $tabsent = $tabsentperday = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tdadmin = $tholiday = $tempOverload = $overload = $tOverload = $lastDayOfWeek = $firstDayOfWeek  = $cs_app = ""; 
	$tempabsent = $lateutlec= $lateutlab = $lateutadmin = "";
	$hasLog = $isSuspension = false;
	$perday_absent = $total_perday_absent = 0;
	$firstDayOfWeek = $this->attcompute->getFirstDayOfWeek($empid);
	$lastDayOfWeek = $this->attcompute->getLastDayOfWeek($empid);
	if(date("l",strtotime($qdate[0]->dte) != $firstDayOfWeek))
	{
		$tempOverload = $this->attcompute->getPastDayOverload($empid,$qdate[0]->dte,$firstDayOfWeek,$edata);
	}
	$isFirstSched = "";
	$ob_data = array();
	$isCreditedHoliday = false;
	$firstDate = true;
    foreach ($qdate as $rdate) {
    	
    		$isSuspension = false;
			$holidayInfo = array();
			// Holiday
			$holiday = $this->attcompute->isHolidayNew($empid,$rdate->dte,$deptid); 
			$holidayInfo = $this->attcompute->holidayInfo($rdate->dte);
			if($holiday)
			{
				$holidayInfo = $this->attcompute->holidayInfo($rdate->dte);
				if(isset($holidayInfo['holiday_type'])) if($holidayInfo['holiday_type']==5) $isSuspension = true;
			}
			
		    $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
		    $sched = $this->attcompute->displaySched($empid,$rdate->dte);
			$schedquery = $this->db->last_query();
		    $countrow = $sched->num_rows();

		    $isValidSchedule = true;

		    if($countrow > 0){
		    	if($sched->row(0)->starttime == '00:00:00' && $sched->row(0)->endtime == '00:00:00') $isValidSchedule = false;
		    }

		    
		    if($x%2 == 0)   $color = " style='background-color: white;'";
		    else            $color = " style='background-color: #f2f2f2;'";
		    $x++;

		    ///< for validation of absent for 1st day in range. this will check for previous day attendance

	    	if($firstDate && $holiday){
	    		
	    		$hasLog = $this->attendance->checkPreviousSchedAttendanceTeaching($rdate->dte,$empid);
	    		
	    		$firstDate = false;
	    	}

		    
		    if($countrow > 0 && $isValidSchedule){
		    	$haswholedayleave = false;
		    	$hasleavecount = 0;

	    	    ///< for validation of holiday (will only be credited if not absent during last schedule)
	    	    $hasLogprev = $hasLog;
		    	$hasLog = false;

	    		if($hasLogprev || $isSuspension) 	$isCreditedHoliday = true;
		    	else 								$isCreditedHoliday = false;
		    	
		        $tempsched = "";
				$seq = 0;

				$perday_absent = $this->attendance->getTotalAbsentPerday($sched->result(), $empid, $rdate->dte);
				$total_perday_absent += $perday_absent;
		        foreach($sched->result() as $rsched){

				        if($tempsched == $dispLogDate)  $dispLogDate = "";
				        $stime = $rsched->starttime;
				        $etime = $rsched->endtime; 
				        $type  = $rsched->leclab;
						$seq += 1;
						$tardy_start = $rsched->tardy_start;
						$absent_start = $rsched->absent_start;
						$earlydismissal = $rsched->early_dismissal;

				        // logtime
				        list($login,$logout,$q,$haslog_forremarks) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq,$absent_start,$earlydismissal);
				        // echo "<pre>";print_r($this->db->last_query());
				        if($login=='0000-00-00 00:00:00') $login = '';
				        if($logout=='0000-00-00 00:00:00') $logout = '';

				        // Leave
				        list($el,$vl,$sl,$ol,$oltype,$ob)     = $this->attcompute->displayLeave($empid,$rdate->dte,'',$stime,$etime);

				        //late-under-undertime remarks
				        $ob_data = $this->attcompute->displayLateUTAbs($empid, $rdate->dte);

				        // Change Schedule
						$cs_app = $this->attcompute->displayChangeSchedApp($empid,$rdate->dte);

						// Leave Pending
						$pending = $this->attcompute->displayPendingApp($empid,$rdate->dte);

						$pending_ob = $this->attcompute->displayPendingOBApp($empid,$rdate->dte, ($seq==1) ? "late" : "undertime");
						
				        // Absent
				        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlydismissal, $absent_start, $isFirstSched);

						if($oltype == "ABSENT") 				$absent = $absent;
				        else if($holiday && $isCreditedHoliday) $absent = "";
				        if ($vl >= 1 || $el >= 1 || $sl >= 1 || $ob >= 1){
		                    $absent = "";
		                    $haswholedayleave = true;
		                }
		                if ($vl > 0 || $el > 0 || $sl > 0 || $ob > 0){
		                	$absent = "";
		                    $hasleavecount++;
		                    // $login = $logout = '';
		                }
						
				        // Late / Undertime
				        list($lateutlec,$lateutlab,$lateutadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUT($stime,$etime,$tardy_start,$login,$logout,$type,$absent);
				        if($el || $vl || $sl/* || $ob*/ || ($holiday && $isCreditedHoliday)){
				             $lateutlec = $lateutlab = $lateutadmin = $tschedlec = $tschedlab = $tschedadmin = "";
				        }

						
						if($absent && !$type) $absent = '';

						//Total Hours of Work
						$schedstart   = strtotime($stime);
						$schedend   = strtotime($etime);

						$totalHoursOfWork = round(abs($schedend - $schedstart) / 60,2);
						if($holiday)
						{
							if($this->attcompute->isHolidayWithpay($rdate->dte) == "YES")
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
							$tempabsent = true;
				        }
						
						// Overload
						if(!$absent && !$lateutlec)
						{
							$tempOverload += $this->attcompute->displayOverloadTime($stime,$etime,$lateutlab);
						}
						else
						{
							$tempOverload += 0;
						}
						
						if($tempOverload > $this->attcompute->exp_time("30:00"))
						{
							$overload = $tempOverload - $this->attcompute->exp_time("30:00");
						}

						$log_remarks = '';

						if($absent || ($tschedlec || $tschedlab || $tschedadmin)){
							if(!$login && !$logout && !$haslog_forremarks) $log_remarks = 'NO TIME IN AND OUT';
							elseif(!$login) $log_remarks = 'NO TIME IN';
							elseif(!$logout) $log_remarks = 'NO TIME OUT';
						}

						///< hide login and logout if leave for that sched
						if($hasleavecount && !$ob) $login = $logout = "";
						
						if(($lateutlec || $lateutlab || $lateutadmin) && !$absent){
			            	if(in_array("undertime", $ob_data)) $log_remarks = "EXCUSED UNDERTIME";
			            	else{ 
			            			$log_remarks = "UNEXCUSED UNDERTIME";
			            			$ob_type = false;
			            			$ob_data = array();
			            		}
			            }

						if($lateutlec || $lateutlab || $lateutadmin && $seq == 1){
							if(in_array("late", $ob_data)) $log_remarks = "EXCUSED LATE";
				            else{
				            		$log_remarks = "UNEXCUSED LATE";
				            		$ob_type = false;
			            			$ob_data = array();
				            }
				        }

						if($absent){
							if(in_array("absent", $ob_data)) $log_remarks = "EXCUSED ABSENT";
				            else{
				            	if(strtotime($rdate->dte) < strtotime($date_now)){
				            		$log_remarks = "UNEXCUSED ABSENT";
				            		$ob_type = false;
			            			$ob_data = array();
				            	}
				            }
				        }
				        
				        ?>	

				        <tr class="edata" <?=$color?>>
				            <?if($dispLogDate){?>

				                <td class="align_center" rowspan="<?=$countrow?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
				            <?}?>
				            <td class="align_center" key="ss"><?=($stime ? date('h:i A',strtotime($stime)) : "--")?></td>
				            <td class="align_center" key="es"><?=($stime ? date('h:i A',strtotime($etime)) : "--")?></td>
				            <td class="align_center" key="ti" style='<?=(($lateutlec || $lateutlab || $lateutadmin || $absent) && date('H:i',strtotime($stime)) < date('H:i',strtotime($login)))?"color:red":""?>'>
				            			<?=($login? date("h:i A",strtotime($login)) : "--")?>
		            		</td>
				            <td class="align_center" key="to" style='<?=(($lateutlec || $lateutlab || $lateutadmin || $absent) && date('H:i',strtotime($logout)) < date('H:i',strtotime($etime)))?"color:red":""?>'>
				            			<?=($logout? date("h:i A",strtotime($logout)) : "--")?>
		            		</td>
							<td class="align_center" key="overload"><?=($lastDayOfWeek == date("l",strtotime($rdate->dte)))?($overload?$this->attcompute->sec_to_hm($overload):""):""?></td> 
							<td class="align_center" key="subtitute"></td>
							<td class="align_center" key="utlec"><?=$lateutlec?$lateutlec:""?></td>
				            <td class="align_center" key="utlab"><?=$lateutlab?$lateutlab:""?></td>
				            <td class="align_center" key="utlab"><?=$lateutadmin?$lateutadmin:""?></td>
				            <td class="align_center" key="ab" style='<?=($absent)?"background-color: #ffe6e6;":""?>'><?=$absent?1:""?></td>
				            <?
								$rwcount = 1;
								if(!$dispLogDate) $rwcount = 1;
								if($haswholedayleave || $pending || $holiday) $rwcount = $countrow;

								if($dispLogDate || (!$haswholedayleave && !$pending && !$holiday)){
							?>

					                <td class="align_center" rowspan="<?=$rwcount?>" key="el"><?=$hasleavecount <= 1 ? $el : ''?></td>
					                <td class="align_center" rowspan="<?=$rwcount?>" key="vl"><?=$hasleavecount <= 1 ? $vl : ''?></td>
					                <td class="align_center" rowspan="<?=$rwcount?>" key="sl"><?=$hasleavecount <= 1 ? $sl : ''?></td>
					                <td class="align_center" rowspan="<?=$rwcount?>" key="ol"><?=$hasleavecount <= 1 ? '' : ''?></td>
					                <!-- <td class="align_center" rowspan="<?=$rwcount?>" key="ol" >

					                	<span style="<?=($pending)?'background-color:#b3d9ff;':""?> <?=(!$ob_data)?'color:red;':""?> "><?=($log_remarks?$log_remarks."<br>":"")?></span>
					                	<span style="<?=($pending)?'background-color:#b3d9ff;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=$dispLogDate ? ($cs_app?$cs_app."<br>":"") : ""?></span>
					                	<span style="<?=($pending)?'background-color:#b3d9ff;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=($pending)?"PENDING ".$pending."<br>":""?></span>
										<span style="<?=($pending)?'background-color:#b3d9ff;':""?> <?=($ob)?'color:black;':""?> "><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" 
												: $oltype."<br>") : $this->employeemod->othLeaveDesc($ol)."<br>") 
												: '')?> </span>

										<?php
											if(isset($holidayInfo['description'])){
												if(isset($holidayInfo['halfday'])){
													if($holidayInfo['sched_count'] == "second" && !$isFirstSched){
														echo $holidayInfo['description'];
													}else if($holidayInfo['sched_count'] != "second" && $isFirstSched){
														echo $holidayInfo['description'];
													}else{
														echo $holidayInfo['description'];
													}
												}else{
													echo $holidayInfo['description'];
												}
											}
										?>
					                	
				                	</td> -->
				            	<?}?>
			            	<?php  
								$rwcount = 1;
								if(!$dispLogDate ||  (!$haswholedayleave && !$pending && !$holiday)) $rwcount = 1;
							?>
							<td class="align_center" rowspan="<?=$rwcount?>" key="ol">
									<span style="text-align:center;<?=($pending || $pending_ob)?'background-color:#b3d9ff;':""?> <?=(!$ob_data)?'color:red;':""?> "><?=($log_remarks?$log_remarks.$pending_ob."<br>":"")?></span>
				                	<span style="text-align:center;<?=($pending)?'background-color:#b3d9ff;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=$dispLogDate ? ($cs_app?$cs_app."<br>":"") : ""?></span>
				                	<span style="text-align:center;<?=($pending)?'background-color:#b3d9ff;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=($pending)?"PENDING ".$pending."<br>":""?></span>
									<span style="text-align:center;<?=($pending)?'background-color:#b3d9ff;':""?> <?=($ob)?'color:black;':""?> "><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" 
											: $oltype."<br>") : $this->employeemod->othLeaveDesc($ol)."<br>") 
											: '')?> </span>
										<?php
										if(isset($holidayInfo['description'])){
											if(isset($holidayInfo['halfday'])){
												if($holidayInfo['sched_count'] == "second" && !$isFirstSched){
													echo $holidayInfo['description'];
												}else if($holidayInfo['sched_count'] != "second" && $isFirstSched){
													echo $holidayInfo['description'];
												}else{
													echo "";
												}
											}else{
												echo $holidayInfo['description'];
											}
										}
									?>
										
							</td>
				            <td class="align_center" key="dlec"><?=$tschedlec?></td>
				            <td class="align_center" key="dlab"><?=$tschedlab?></td>
				            <td class="align_center" key="dadmin"><?=$tschedadmin?></td>
				            <?if($dispLogDate){?>
				                <td class="align_center" rowspan="<?=$countrow?>"><?=($holiday && isset($holidayInfo['type']))?$holidayInfo['type']:""?></td>
				            <?}?>
				            <?if($dispLogDate){?>
				                <!-- <td class="align_center" rowspan="<?=$countrow?>"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->
				            <?}?>
				        </tr>
				        <?
				            $tempsched = $dispLogDate;
				            
				            /*
				             * Total
				             */ 
				            
				            // Absent
							if($absent){
				                $tabsent += $this->attcompute->exp_time($absent) > 0 ? 1 : 0;
				            }
							
				            
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

				            if($tadmin){
				                $secs  = strtotime($lateutadmin)-strtotime("00:00:00");
				                if($secs>0) $tadmin = date("H:i",strtotime($tadmin)+$secs);
				            }else
				                $tadmin    = $lateutadmin;
				            
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
				            if($tschedadmin){
				                $tdadmin += $this->attcompute->exp_time($tschedadmin);
				            }

				            if(!$tschedadmin && !$absent) $hasLog = true;
		            
		        }
		    }
			else
			{
				$totalQ = 0;
				$stime = "";
				$etime = ""; 
						
				$log = $this->attcompute->displayLogTimeFlexi($empid,$rdate->dte,$edata);

				// Leave
				list($el,$vl,$sl,$ol,$oltype)     = $this->attcompute->displayLeave($empid,$rdate->dte);

				// Leave Pending
				$pending = $this->attcompute->displayPendingApp($empid,$rdate->dte);
				
				// Overtime
				list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOtCollege($empid,$rdate->dte);

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
				
				$service_credit    = $this->attcompute->displayServiceCredit($empid,$rdate->dte);
				$service_credit = $service_credit?$service_credit:null;
				
				if($holiday)
				{
					$holidayInfo = $this->attcompute->holidayInfo($rdate->dte);
				}
				
				if(count($log)> 0)
				{
					// Leave
					list($el,$vl,$sl,$ol,$oltype)     = $this->attcompute->displayLeave($empid,$rdate->dte);
								
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
		<?if($stime && $etime){
			if($dispLogDate){
				?>
						<td class="align_center" rowspan="<?=count($log)?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
		<?}?>
						<td class="align_center" rowspan="<?=count($log)?>" key="ss"><?=$stime?></td>
						<td class="align_center" rowspan="<?=count($log)?>" key="es"><?=$etime?></td>
		<?}?>
						<td class="align_center" key="ti" style=""><?=$login?date("h:i A",strtotime($login)):"--"?></td>
						<td class="align_center" key="to" style=""><?=(!$logout)?"--":($logout == '00:00:00')?"--":date("h:i A",strtotime($logout))?></td> 
		<?if($stime && $etime){?>
						<td class="align_center" rowspan="<?=count($log)?>" key="overload">--</td>
						<td class="align_center" rowspan="<?=count($log)?>" key="subtitute">--</td>
						<td class="align_center" rowspan="<?=count($log)?>" key="utlec"><?=$lateutlec?$lateutlec:""?></td>
						<td class="align_center" rowspan="<?=count($log)?>" key="utlab"><?=$lateutlab?$lateutlab:""?></td>
						<td class="align_center" rowspan="<?=count($log)?>" key="utlab"><?=$lateutadmin?$lateutadmin:""?></td>
						<td class="align_center" rowspan="<?=count($log)?>" key="ab" style="">--</td>
						<?if($dispLogDate){?>
						<td class="align_center" rowspan="<?=count($log)?>" key="el"><?=($el?$el:"--")?></td>
						<td class="align_center" rowspan="<?=count($log)?>" key="vl"><?=($vl?$vl:"--")?></td>
						<td class="align_center" rowspan="<?=count($log)?>" key="sl"><?=($sl?$sl:"--")?></td>
						<td class="align_center" rowspan="<?=count($log)?>" key="ol"><?=($ol?$ol:"--")?></td>
						<td class="align_center" rowspan="<?=count($log)?>" key="ol">
							<!-- <?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : ($q ? ($q == "1" ? "" : $q) : ""))?> <?=$holiday?$holidayInfo['description']:""?> -->

		                	<?=($cs_app?$cs_app.'<br>':'')?>
							<?=($pending)?"PENDING ".$pending.'<br>':($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" : $oltype.'<br>') : $this->employeemod->othLeaveDesc($ol).'<br>') : ($q ? ($q == "1" ? "" : $q.'<br>') : ""))?> 
							<?=($holiday && isset($holidayInfo['description']))?$holidayInfo['description']:""?>
								
						</td>
						<?}?>
						<td class="align_center" rowspan="<?=count($log)?>" key="dlec">--</td>
						<td class="align_center" rowspan="<?=count($log)?>" key="dlab">--</td>
						<td class="align_center" rowspan="<?=count($log)?>" key="dadmin">--</td>

					<?if($dispLogDate){?>	
					
						<td class="align_center" rowspan="<?=count($log)?>"><?=($holiday && isset($holidayInfo['type']))?$holidayInfo['type']:""?></td>
		<?}
		}?>
					    <?if($dispLogDate){?>
			                <!-- <td class="align_center" rowspan="<?=$countrow?>"><?=$this->attcompute->sec_to_hm($perday_absent)?></td>  -->
			            <?}?>
					</tr>
		<?
								$stime = $etime = "";
					}
				}
				else
				{
					if($holiday)
					{
						$holidayInfo = $this->attcompute->holidayInfo($rdate->dte);
					}
					
					// Leave
					list($el,$vl,$sl,$ol,$oltype)     = $this->attcompute->displayLeave($empid,$rdate->dte);
		?>
					<tr class="edata" style='background-color: gray;color:white'>
						<td class="align_center" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?> </td>
						<td class="align_center" key="ss">--</td>
						<td class="align_center" key="es">--</td>
						<td class="align_center" key="ti" style=''>--</td>
						<td class="align_center" key="to" style=''>--</td>
						<td class="align_center" key="overload">--</td>
						<td class="align_center" key="subtitute">--</td>
						<td class="align_center" key="utlec">--</td>
						<td class="align_center" key="utlab">--</td>
						<td class="align_center" key="utadmin">--</td>
						<td class="align_center" key="ab" style=''>--</td>
						<td class="align_center" key="el"><?=($el?$el:"--")?></td>
						<td class="align_center" key="vl"><?=($vl?$vl:"--")?></td>
						<td class="align_center" key="sl"><?=($sl?$sl:"--")?></td>
						<td class="align_center" key="ol"><?=($ol?$ol:"--")?></td>
						<td class="align_center" key="ol"><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : "")?> <?=($holiday && isset($holidayInfo['description']))?$holidayInfo['description']:""?></td>
						<td class="align_center" key="dlec">--</td>
						<td class="align_center" key="dlab">--</td>
						<td class="align_center" key="dadmin">--</td>
						<td class="align_center"><?=($holiday && isset($holidayInfo['type']))?$holidayInfo['type']:""?></td>
            			<!-- <td class="align_center"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->

						<!-- <?if($dispLogDate){?> -->
			                <!-- <td class="align_center" rowspan="<?=$countrow?>"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->
			            <!-- <?}?> -->
					</tr>
		<?
				}
						
				/*
				 * Total
				 */ 
						
				// Leave
				if($dispLogDate)
				{
					$tel      += $el;
					$tvl      += $vl;
					$tsl      += $sl;
					$tol      += ($ol ? 1 : "") + ($totalQ ? ($totalQ == 1 ? "" : 1) : "") ;
				}
			}
			// total holiday
		    $tholiday += $holiday;
				
			if($lastDayOfWeek == date("l",strtotime($rdate->dte)))
			{
				$tOverload += $overload;
				$overload = $tempOverload = 0;
			}
  			$ob_data = array();
  }
  $tdlec = ($tdlec ? $this->attcompute->sec_to_hm($tdlec) : "");
  $tdlab = ($tdlab ? $this->attcompute->sec_to_hm($tdlab) : "");
  $tdadmin = ($tdadmin ? $this->attcompute->sec_to_hm($tdadmin) : "");
  ?>
  <tr class="edata">
        <th class="align_right" colspan="5"><b>TOTAL</b></th>
            <th class="align_center"><?=$tOverload = ($tOverload ? $this->attcompute->sec_to_hm($tOverload) : "")?></th>
            <th class="align_center"></th>
            <th class="align_center"><?=$tlec?></th>
            <th class="align_center"><?=$tlab?></th>
            <th class="align_center"><?=$tadmin?></th>
            <th class="align_center"><?=$tabsent?></th>
            <th class="align_center"><?=$tel?></th>
            <th class="align_center"><?=$tvl?></th>
            <th class="align_center"><?=$tsl?></th>
            <th class="align_center"></th>
            <th class="align_center"></th>
            <th class="align_center"><?=$tdlec?></th>
            <th class="align_center"><?=$tdlab?></th>
            <th class="align_center"><?=$tdadmin?></th>
            <th class="align_center"><?=$tholiday?></th>
            <!-- <th class="align_center"><?=$this->attcompute->sec_to_hm($total_perday_absent)?></th> -->
      </tr>
    </tbody>
</table>
</div>