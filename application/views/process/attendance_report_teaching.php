<?
$qdate = $this->attcompute->displayDateRange($from_date, $to_date);
$fixedday = $this->attcompute->isFixedDay($empid);
// $hide_from_fixedday = $fixedday ? ' hidden':'';
$hide_from_fixedday = ' hidden';
$usertype = $this->session->userdata('usertype');
?>

<div class="panel-body" style="border: transparent !important;">
<h2>Attendance</h2>
<p><?=$datedisplay?></p>
<p><?=$this->employee->getfullname($empid)?></p>
<table class="table table-bordered datatable" id="indvtbl">
    <thead>
        <tr style="background-color: #0072c6";>
            <th rowspan="2" class="align_center">Date</th>
            <th class="align_center" colspan="2" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >Official Time</th>
            <th class="align_center" colspan="2">Actual Log Time</th>
            <th class="align_center" colspan="3">Overtime (hr:min)</th>
            <!-- <th rowspan="2" class="align_center">Overload</th> -->
            <!-- <th rowspan="2" class="align_center">Subtitute</th> -->
            <th class="align_center">Late</th>
            <th class="align_center">Undertime</th>
            <!-- <th class="align_center" >Absent</th> -->
            <th class="align_center" rowspan="2">Absent</th>
            <th class="align_center" colspan="3">Leaves</th>
            <!-- <th class="align_center" rowspan="2">Service Credit</th> -->
            <th class="align_center" rowspan="2" >Remarks/Others</th>
            <th class="align_center" colspan="3" <?=$hide_from_fixedday?>>Work Hours</th>
            <th class="align_center" rowspan="2" >Holiday</th>
            <!-- <th class="align_center" rowspan="2" >Total Per Day</th> -->
        </tr>
        <tr style="background-color: #0072c6";>
            <th class="align_center" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >IN</th><th class="align_center" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >OUT</th>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
            <th class="align_center">Regular</th>
            <th class="align_center">Rest Day</th>
            <th class="align_center">Holiday</th>
<!--             <th class="align_center">Lec</th>
            <th class="align_center">Lab</th> -->
            <th class="align_center">Hr:min</th>
<!--             <th class="align_center">Lec</th>
            <th class="align_center">Lab</th> -->
            <th class="align_center">Hr:min</th>
            <!-- <th class="align_center">Subject</th> -->
            <th class="align_center">Vacation</th>
            <th class="align_center">Sick</th>
            <th class="align_center">Other</th>
            <th class="align_center" <?=$hide_from_fixedday?>>Lec</th>
            <th class="align_center" <?=$hide_from_fixedday?>>Lab</th>
            <th class="align_center" <?=$hide_from_fixedday?>>Admin</th>
        </tr>
    </thead>
    <tbody id="employeelist">
<?
    $x = $totr = $totrest = $tothol = $tlec = $tlab = $tadmin = $tabsent = $tabsentperday = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tdadmin = $tholiday = $tempOverload = $overload = $tOverload = $lastDayOfWeek = $firstDayOfWeek  = $cs_app = ""; 
	$tempabsent = $lateutlec= $lateutlab = $lateutadmin = $utlec= $utlab = $utadmin = $tutlec = $tutlab = $tutadmin = $twork_lec = $twork_lab = $twork_admin = $work_lec = $work_lab = $work_admin = "";
	$t_service_credit = $service_credit = "";
	$perday_absent = $total_perday_absent = 0;
	$hasLogin = '';
	$seq_new = 0;
	$hasLog = $isSuspension = false;
	$stime_new = $etime_new = $seq_new = $tardy_start_new = $absent_start_new = $earlydismissal_new = 0;
	$firstDayOfWeek = $this->attcompute->getFirstDayOfWeek($empid);
	$lastDayOfWeek = $this->attcompute->getLastDayOfWeek($empid);
	if(date("l",strtotime($qdate[0]->dte) != $firstDayOfWeek))
	{
		$tempOverload = $this->attcompute->getPastDayOverload($empid,$qdate[0]->dte,$firstDayOfWeek,$edata);
	}
	
	$isCreditedHoliday = false;
	$hasHalfdayHoliday = false;
	$firstDate = true;
	$days_pending = 0;
	$ob_data = array();
	$date_now = date('Y-m-d');
    foreach ($qdate as $rdate) {
    	
    		$isSuspension = false;
    		$is_holiday_valid = false;
			$holidayInfo = array();
			// Holiday
			$holiday = $this->attcompute->isHolidayNew($empid,$rdate->dte,$deptid,"","","teaching" ); 
			$holidayInfo = $this->attcompute->holidayInfo($rdate->dte, "", "teaching", "", $holiday);
			if($holiday)
			{
				if($holidayInfo){
					if($holidayInfo["code"]=="SUS") $isSuspension = true;
					//if($holidayInfo["withPay"]=='NO' || !$holidayInfo["withPay"]) $holiday = '';
					// if($holidayInfo["holiday_rate"] <= 0) $holiday = ''; 
				}
				$is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
			}

			if(!$is_holiday_valid){
				$holidayInfo = array();
				$holiday = "";
			}
			
		    $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
		    $sched = $this->attcompute->displaySched($empid,$rdate->dte);
			$schedquery = $this->db->last_query();
		    $countrow = $sched->num_rows();

		    $isValidSchedule = true;

		    if($countrow > 0){
		    	if($sched->row(0)->starttime == "00:00:00" && $sched->row(0)->endtime == "00:00:00") $isValidSchedule = false;
		    }

		    
		    if($x%2 == 0)   $color = ' style="background-color: white;"';
		    else            $color = ' style="background-color: #f2f2f2;"';
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
				$service_credit = null;
				$service_credit_used = 0;
				$counter = 0;

				$isFirstSched = true;
				$q_sched = $sched;
				$perday_absent = $this->attendance->getTotalAbsentPerday($sched->result(), $empid, $rdate->dte);
				$total_perday_absent += $perday_absent;
		        foreach($sched->result() as $rsched){
		        	if(!$is_holiday_valid && $isFirstSched){
						$holidayInfo = $this->attcompute->holidayInfo($rdate->dte, "second", "teaching");
					}
		        	$work_lec = $work_lab = $work_admin = "";

		        	//NOT FLEXIBLE -----------------------------------------------------------------------------------------------------------------------------------
		        	if($rsched->flexible != "YES")
		        	{
				        if($tempsched == $dispLogDate) $dispLogDate = "";
				        $stime = $rsched->starttime;
				        $etime = $rsched->endtime; 
				        $type  = $rsched->leclab;
						$seq += 1;
						$tardy_start = $rsched->tardy_start;
						$absent_start = $rsched->absent_start;
						$earlydismissal = $rsched->early_dismissal;


				        // logtime
				        list($login,$logout,$q,$haslog_forremarks) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq,$absent_start,$earlydismissal);
				        

				        // Overtime
						list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,true);
				        
				        // Leave
				        list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count, $lnopay)     = $this->attcompute->displayLeave($empid,$rdate->dte,"",$stime,$etime,$seq);

				        //late-under-undertime remarks
				        $ob_data = $this->attcompute->displayLateUTAbs($empid, $rdate->dte);

				        //Service Credit 
						$service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);

				        // Change Schedule
						$cs_app = $this->attcompute->displayChangeSchedApp($empid,$rdate->dte);

						// Leave Pending
						$pending = $this->attcompute->displayPendingApp($empid,$rdate->dte,"",$stime,$etime);
						if (strpos($pending, '~') !== false){
							$pending_arr = explode("~", $pending);
							$pending = $pending_arr[0];
							$days_pending = $pending_arr[1];
						}
						$pending_ob = $this->attcompute->displayPendingOBApp($empid,$rdate->dte, ($seq==1) ? "late" : "undertime");
						
				        // Absent
				        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlydismissal, $isFirstSched);

						if($oltype == "ABSENT") 				$absent = $absent;
				        else if($holiday && $isCreditedHoliday) $absent = "";
				        if ($vl >= 1 || $el >= 1 || $sl >= 1 || $ob >= 1 || $service_credit >= 1){
		                    $absent = "";
		                    $haswholedayleave = true;
		                }
		                if ($vl > 0 || $el > 0 || $sl > 0 || $ob > 0 || $service_credit > 0){
		                	$absent = "";
		                    $hasleavecount++;
		                }
		                if($abs_count >= 1) $haswholedayleave = true;

				        // Late / Undertime
				        list($lateutlec,$lateutlab,$lateutadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUT($stime,$etime,$tardy_start,$login,$logout,$type,$absent);
				        #list($lateutlec,$lateutlab,$lateutadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUT($stime,$etime,$tardy_start,$tmp_login,$tmp_logout,$type,$absent);
				        list($utlec,$utlab,$utadmin) = $this->attcompute->computeUndertime($stime,$etime,$tardy_start,$login,$logout,$type,$absent);


				        if($el || $vl || $sl/* || $ob*/ || $service_credit || ($holiday && $isCreditedHoliday)){
				             $lateutlec = $lateutlab = $lateutadmin = $tschedlec = $tschedlab = $tschedadmin = "";
				              $utlec = $utlab = $utadmin = "";
				        }
						
						
						//Total Hours of Work
						$schedstart   = strtotime($stime);
						$schedend   = strtotime($etime);

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

						if($absent){
							if(!$login && !$logout && !$haslog_forremarks) $log_remarks = 'NO TIME IN AND OUT';
							elseif(!$login) $log_remarks = 'NO TIME IN';
							elseif(!$logout) $log_remarks = 'NO TIME OUT';
						}


						$hasOL = $ol ? ($ol != 'CORRECTION' && $ol != 'undertime' && $ol != 'late'  && $ol == 0 ? true : false) : false; 

						if($hasOL && !$ob) $login = $logout = "";

        	            if(!$fixedday){
        	            	if($absent=='' || $hasOL){
        	            		$tsched   = round(abs(strtotime($stime) - strtotime($etime)) / 60,2);
        	            		$tsched   = date('H:i', mktime(0,$tsched));
        	            		if($type == 'LEC' && ($tschedlec=='' || $hasOL))       $work_lec =  $tsched;
        	            		elseif($type == 'LAB' && ($tschedlab=='' || $hasOL))   $work_lab = $tsched;
        	            		else {
        	            			if($tschedadmin=='' || $hasOL) $work_admin = $tsched;
        	            		}
        	            	}
        	            }

        	            #round of tschedadmin
				        if($tschedadmin > 0){
				            $tschedadmin = $this->time->roundOffTime($tschedadmin);
				        }
				        if($isFirstSched){
				        	if(!$login && $absent) $login = $this->attcompute->getLogin($empid, $edata, $rdate->dte);
				        	if(!$logout && $absent) $logout = $this->attcompute->getLogout($empid, $edata, $rdate->dte);
				        	if($login && $logout){
				        		$end_time = $q_sched->row(count($q_sched->result()) - 1)->endtime;
				        		$getData = $this->attcompute->displayLateUT($stime, $end_time, $stime, $login, $logout, "ADMIN", "");

				        		if(!$lateutadmin){
				        			$log_remarks = "";
				        			$lateutadmin = $getData[5];
				        		}
				        		if($tschedadmin){
				        			$log_remarks = "";
				        			$lateutadmin = $tschedadmin;
				        			$tschedadmin = "";
				        		}
				        		$hasLogin = TRUE;
				        	}else{
				        		$sched_new = $sched->result();

				        		if(isset($sched_new[1]->starttime)) $stime_new = $sched_new[1]->starttime;
						        if(isset($sched_new[1]->endtime)) $etime_new = $sched_new[1]->endtime; 
						        if(isset($sched_new[1]->leclab)) $type_new  = $sched_new[1]->leclab;
								$seq_new += 1;
								if(isset($sched_new[1]->tardy_start)) $tardy_start_new = $sched_new[1]->tardy_start;
								if(isset($sched_new[1]->absent_start)) $absent_start_new = $sched_new[1]->absent_start;
								if(isset($sched_new[1]->early_dismissal)) $earlydismissal_new = $sched_new[1]->early_dismissal;

			        			list($login_new,$logout_new,$q_new,$haslog_forremarks_new) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime_new,$etime_new,"NEW",$seq_new,$tardy_start_new,$absent_start_new,$earlydismissal_new);
			        			// if($login_new || $logout_new){
			        			// 	$lateutadmin = $tschedadmin;
				        		// 	$lateutlec = $tschedlec;
				        		// 	$lateutlab = $tschedlab;
				        		// 	$tschedadmin = "";
				        		// 	$tschedlec = "";
				        		// 	$tschedlab = "";
			        			// }
			        		}
				        }else{
				        	if(!$logout) $logout = $this->attcompute->getLogout($empid, $edata, $rdate->dte);
				        	// if($lateutadmin) $lateutadmin = "";
				        	if($el == FALSE && $vl == FALSE && $sl == FALSE  && $ob == FALSE){
				        		// if($login){
				        		// 	$utadmin = $tschedadmin;
				        		// 	$utlec = $tschedlec;
				        		// 	$utlab = $tschedlab;
				        		// 	$tschedadmin = "";
				        		// 	$tschedlec = "";
				        		// 	$tschedlab = "";
					        	// }
					        	if($login && $logout){
					        		$getData = $this->attcompute->displayLateUT($stime, $etime, $stime, $login, $logout, "ADMIN", "");
					        		
					        		if(!$utadmin){
					        			$log_remarks = "";
					        			// $utadmin = $getData[5];
					        		}
					        		if(!$lateutadmin){
					        			$log_remarks = "";
					        			// $lateutadmin = $getData[2];
					        		}
					        		if($tschedadmin){
					        			$log_remarks = "";
					        			$utadmin = $tschedadmin;
					        			$tschedadmin = "";
					        		}
					        	}
					        }
				        }

				        if(!$holiday) $holiday = $this->attcompute->isHolidayNew($empid, $rdate->dte,$deptid,"","on","teaching" );  
					    if($holiday)
						{
							$sched_count = "";
							if($isFirstSched) $sched_count = "first";
							else $sched_count = "second";
							$newholidayInfo = $this->attcompute->holidayInfo($rdate->dte, $sched_count, "teaching");
							if(isset($newholidayInfo["halfday"])) $holidayInfo = $newholidayInfo;
							if($holidayInfo){
								if($holidayInfo["code"]=="SUS") $isSuspension = true;
								if($holidayInfo["holiday_rate"] <= 0) $holiday = ''; 
							}
						}
			            $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
				        if(isset($holidayInfo['description'])){
							$log_remarks = '';
							if(isset($holidayInfo['halfday'])){
								if($holidayInfo['sched_count'] == "second" && !$isFirstSched){
									$lateutlec = $lateutlec;
									$utlec = $utlec;
									$absent = '';
									$tschedlec = $tschedlab = $tschedadmin = "";
									$hasHalfdayHoliday = true;
								}else if($holidayInfo['sched_count'] != "second" && $isFirstSched){
									$lateutlec = $lateutlec;
									$utlec = $utlec;
									$absent = '';
									$tschedlec = $tschedlab = $tschedadmin = "";
									$hasHalfdayHoliday = true;
								}else{
									$lateutlec = $utlec = $absent =  '';
								}
							}else{
								$lateutlec = $utlec = $absent = '';
							}
						}else{
							$log_remarks = '';
							if($absent){
								if(!$login && !$logout && !$haslog_forremarks) $log_remarks = 'NO TIME IN AND OUT';
								elseif(!$login) $log_remarks = 'NO TIME IN';
								elseif(!$logout) $log_remarks = 'NO TIME OUT';
							}
						}

				        /*negate other deduction*/
				        if ($this->attcompute->exp_time($lateutlec) || $this->attcompute->exp_time($lateutlab) || $this->attcompute->exp_time($lateutadmin) == 14400) $utadmin = $utlec = $utlab ='';
				        if ($this->attcompute->exp_time($utadmin) || $this->attcompute->exp_time($utlec) || $this->attcompute->exp_time($utlab) == 14400) $lateutlec = $lateutlab = $lateutadmin = '';
				        /*end*/

				        if($this->attcompute->exp_time($tschedadmin) <= 14400 && $this->attcompute->exp_time($tschedadmin)) $tschedadmin = "4:00";
				        else if($this->attcompute->exp_time($tschedadmin) >= 14400) $tschedadmin = "4:00";

				        if ($this->attcompute->exp_time($utadmin) >= 14400) $utadmin = "4:00";
				        elseif ($this->attcompute->exp_time($utlec) >= 14400) $utlec = "4:00";
				        elseif ($this->attcompute->exp_time($utlab) >= 14400) $utlab = "4:00";

				        if ($this->attcompute->exp_time($lateutlec) >= 14400) $lateutlec = "4:00";
				        elseif ($this->attcompute->exp_time($lateutlab) >= 14400) $lateutlab = "4:00";
				        elseif ($this->attcompute->exp_time($lateutadmin) >= 14400) $lateutadmin = "4:00";

			            if($lateutadmin){
			            	if(in_array("late", $ob_data)) $log_remarks = "EXCUSED LATE";
			            	else{ 
			            			$log_remarks = "UNEXCUSED LATE";
			            			$ob_type = false;
			            			$ob_data = array();
			            		}
			            }

						if($utadmin){
							if(in_array("undertime", $ob_data)) $log_remarks = "EXCUSED UNDERTIME";
				            else{
				            		$log_remarks = "UNEXCUSED UNDERTIME";
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
				        <!--HERE-->
				        <tr class="edata" <?=$color?>>
				            <?if($dispLogDate){
				            	?>

				                <td class="align_center" rowspan="<?=$countrow?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
				            <?}?>
				            <td class="align_center" key="ss" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime ? date("h:i A",strtotime($stime)) : "--")?></td>
				            <td class="align_center" key="es" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime ? date("h:i A",strtotime($etime)) : "--")?></td>
				            <td class="align_center" key="ti" 
				            	style="<?=($absent || $tschedadmin || $ol == "ABSENT") ? "background-color: #ffe6e6;" : ((($lateutlec || $lateutlab || $lateutadmin) && date("H:i",strtotime($stime)) < date("H:i",strtotime($login)))?"color:red":"")?>">
				            	<?=($login  ? date("h:i A",strtotime($login)) : "--")?></td>
				            <td class="align_center" key="to" 
				            	style="<?=($absent || $tschedadmin || $ol == "ABSENT") ? "background-color: #ffe6e6;" : ((($utlec || $utlab || $utadmin) && date("H:i",strtotime($logout)) < date("H:i",strtotime($etime)))?"color:red":"")?>">
				            	<?=($logout ? date("h:i A",strtotime($logout)) : "--")?></td>

				            <?if($dispLogDate){?>
								<td class="align_center" key="otr"   rowspan="<?=$countrow?>"><?=($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):""?></td>
								<td class="align_center" key="otrest" rowspan="<?=$countrow?>"><?=($otrest)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otrest)):""?></td>
								<td class="align_center" key="othol" rowspan="<?=$countrow?>"><?=($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):""?></td>
							<?}?>

							<!-- <td class="align_center" key="overload"><?=($lastDayOfWeek == date("l",strtotime($rdate->dte)))?($overload?$this->attcompute->sec_to_hm($overload):""):""?></td> -->
							<!-- <td class="align_center" key="subtitute"></td> -->

<!-- 							<td class="align_center" key="utlec"><?=$lateutlec?$lateutlec:""?></td>
				            <td class="align_center" key="utlab"><?=$lateutlab?$lateutlab:""?></td> -->
				            <td class="align_center" key="utlab"><?=$lateutadmin?$lateutadmin:""?></td>
<!-- 				            <td class="align_center" key="utlec"><?=$utlec?$utlec:""?></td>
				            <td class="align_center" key="utlab"><?=$utlab?$utlab:""?></td> -->
				            <td class="align_center" key="utlab"><?=$utadmin?$utadmin:""?></td>
				            <!-- <td class="align_center" key="ab" style='<?=($absent)?"background-color: #ffe6e6;":""?>'><?=$absent?$absent:""?></td> -->
				            <!-- <td class="align_center" key="ab" style='<?=($absent)?"background-color: #ffe6e6;":""?>'><?=$absent?1:""?></td> -->
				            <td class="align_center" key="dadmin" style="<?=($tschedadmin)?"background-color: #ffe6e6;":""?>"><?=(!$fixedday && !$hasOL) ? $tschedadmin : $tschedadmin?></td>
				            <?
								$rwcount = 1;
								if(!$dispLogDate) $rwcount = 1;
								if($haswholedayleave || $pending || $holiday) $rwcount = $countrow;

								if((!$haswholedayleave && !$pending && !$holiday)){
							?>
									<?php if($vl) { ?>
					                	<td class="align_center" rowspan="<?=$rwcount?>" key="vl"><?=$hasleavecount <= 1 ? $vl : ""?></td>
					                <?php } else if($el){?>
					                	<td class="align_center" rowspan="<?=$rwcount?>" key="el"><?=$hasleavecount <= 1 ? $el : ""?></td>
					                <?php }else{ ?>
					                	<td class="align_center" rowspan="<?=$rwcount?>" key="vl"><?=$hasleavecount <= 1 ? "" : ""?></td>
					               	<?php } ?>
					                <td class="align_center" rowspan="<?=$rwcount?>" key="sl"><?=$hasleavecount <= 1 ? $sl : ""?></td>
					                <td class="align_center" rowspan="<?=$rwcount?>" key="el"><?=$hasleavecount <= 1 ? "" : ""?></td>
					                <!-- <td class="align_center" rowspan="<?=$rwcount?>" key="sc"><?=$hasleavecount <= 1 ? $service_credit : ""?></td> -->
					                <!-- <td class="align_center" rowspan="<?=$rwcount?>" key="ol">
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
				            	<?}if($dispLogDate && ($haswholedayleave || $pending || $holiday)){?>
				            		<?php if($vl) { ?>
					                	<td class="align_center" rowspan="<?=($pending && $days_pending >= 1) ? $countrow : $rwcount?>>" key="vl"><?=$hasleavecount <= 1 ? $vl : ""?></td>
					                <?php } else if($el){?>
					                	<td class="align_center" rowspan="<?=($pending && $days_pending >= 1) ? $countrow : $rwcount?>>" key="el"><?=$hasleavecount <= 1 ? $el : ""?></td>
					                <?php }else{ ?>
					                	<td class="align_center" rowspan="<?=($pending && $days_pending >= 1) ? $countrow : $rwcount?>>" key="vl"><?=$hasleavecount <= 1 ? "" : ""?></td>
					               	<?php } ?>
					                <td class="align_center" rowspan="<?=($pending && $days_pending >= 1) ? $countrow : $rwcount?>>" key="sl"><?=$hasleavecount <= 1 ? $sl : ""?></td>
					                <td class="align_center" rowspan="<?=($pending && $days_pending >= 1) ? $countrow : $rwcount?>>" key="el"><?=$hasleavecount <= 1 ? "" : ""?></td>
				            	<?php } ?>
				            	<?php  
									$rwcount = 1;
									if(!$dispLogDate ||  (!$haswholedayleave && !$pending && !$holiday)) $rwcount = 1;
								?>
								<?php if(!$haswholedayleave && !$pending && !$holiday){ ?>
									<td class="align_center" rowspan="<?=$rwcount?>" key="remarks">
											<span style="text-align:center;<?=($pending || $pending_ob)?'background-color:#ffffff;':""?> <?= ($lnopay) ? 'display: none;' : ''; ?> <?=(!$ob_data)?'color:red;':""?> "><?=($log_remarks?$log_remarks.$pending_ob."<br>":"")?></span>
						                	<span style="text-align:center;<?=($pending)?'background-color:#e01414;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=$dispLogDate ? ($cs_app?$cs_app."<br>":"") : ""?></span>
						                	<span style="text-align:center;<?=($pending)?'background-color:#e01414;color:white;':""?> <?=(!$ob_data && !$ob)?'color:white;':""?> "><?=(($pending && !$ob) || $pending == "OVERTIME APPLICATION")?"PENDING ".$pending."<br>":""?></span>
						                	<span style="text-align:center;"><?= ($lnopay) ? $this->attendance->getLeaveNoPay($rdate->dte, $empid)."<br>" : '' ?></span>
											<span style="text-align:center;<?=($pending && !$ob)?'background-color:#e01414;':""?> <?=($ob)?'color:black;':""?> "><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" 
													: ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)."<br>") 
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
											<span style="text-align:center;"><?=($otreg || $otrest || $othol)?'OVERTIME APPLICATION<br>':""?></span>
									</td>
								<?php } ?>
								<?php if($dispLogDate && ($haswholedayleave || $pending || $holiday)){ ?>
									<td class="align_center" rowspan="<?=$countrow?>" key="remarks">
											<span style="text-align:center;<?=($pending || $pending_ob)?'background-color:#ffffff;':""?> <?= ($lnopay) ? 'display: none;' : ''; ?> <?=(!$ob_data)?'color:red;':""?> "><?=($log_remarks?$log_remarks.$pending_ob."<br>":"")?></span>
						                	<span style="text-align:center;<?=($pending)?'background-color:#e01414;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=$dispLogDate ? ($cs_app?$cs_app."<br>":"") : ""?></span>
						                	<span style="text-align:center;<?=($pending)?'background-color:#e01414;color:white;':""?> <?=(!$ob_data && !$ob)?'color:white;':""?> "><?=(($pending && !$ob) || $pending == "OVERTIME APPLICATION")?"PENDING ".$pending."<br>":""?></span>
						                	<span style="text-align:center;"><?= ($lnopay) ? $this->attendance->getLeaveNoPay($rdate->dte, $empid)."<br>" : '' ?></span>
											<span style="text-align:center;<?=($pending && !$ob)?'background-color:#e01414;':""?> <?=($ob)?'color:black;':""?> "><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" 
													: ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)."<br>") 
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
											<span style="text-align:center;"><?=($otreg || $otrest || $othol)?'OVERTIME APPLICATION<br>':""?></span>
									</td>
								<?php } ?>
				            <!-- <td class="align_center" key="dlec"><?=(!$fixedday && !$hasOL) ? '' : $tschedlec?></td> -->
				            <!-- <td class="align_center" key="dlab"><?=(!$fixedday && !$hasOL) ? '' : $tschedlab?></td> -->
				            <td class="align_center" key="" <?=$hide_from_fixedday?> ><?=$work_lec?></td>
				            <td class="align_center" key="" <?=$hide_from_fixedday?> ><?=$work_lab?></td>
				            <td class="align_center" key="" <?=$hide_from_fixedday?> ><?=$work_admin?></td>
				            <?php if(isset($holidayInfo["halfday"])){ ?>
							<td class="align_center" rowspan="<?=$rwcount?>" style="vertical-align : middle;text-align:center;"><?=(isset($holidayInfo['type']) && $hasHalfdayHoliday ? $holidayInfo['type'] : '')?></td>
				            <?php }else if($dispLogDate && !isset($holidayInfo["halfday"])){ ?>
				            	<td class="align_center" rowspan="<?=$countrow?>" style="vertical-align : middle;text-align:center;"><?=(isset($holidayInfo['type']) ? $holidayInfo['type'] : '')?></td>
				            <?php } ?>
				            <?if($dispLogDate){?>
				                <!-- <td class="align_center" rowspan="<?=$countrow?>"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->
				            <?}?>
				        </tr>
				        <?
					        }
					        else
					        {
					        ///< FLEXIBLE ---------------------------------------------------------------------------------------------------------------------------------
					        	
					        	$totalQ = 0;
					        	if($tempsched == $dispLogDate){  $dispLogDate = "";}
					        	$stime  = $rsched->starttime;
					        	$etime  = $rsched->endtime; 
					        	$type  = $rsched->leclab;
					        	$tstart = $rsched->tardy_start; 
					        	$earlyd = $rsched->early_dismissal;
					        	
					        	// logtime
					        	$log = $this->attcompute->getLogsPerDay($empid,$rdate->dte,$edata);
					        	
			        	        // Overtime
			        			list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,true);
					        	
					        	
					        	// Leave
					        	list($el,$vl,$sl,$ol,$oltype,$ob)             = $this->attcompute->displayLeave($empid,$rdate->dte);

			        	        //Service Credit 
			        			$service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);

			        	        // Change Schedule
			        			$cs_app = $this->attcompute->displayChangeSchedApp($empid,$rdate->dte);

			        			// Leave Pending
			        			$pending = $this->attcompute->displayPendingApp($empid,$rdate->dte);
					        			
					        	       					        	
					        	// Absent
					        	$absent = $this->attcompute->displayAbsentFlexi($log,$rsched->hours,$rsched->mode,$empid,$rdate->dte,$type);
					        	print_r($absent);

					        	if($oltype == "ABSENT") $absent = $absent;
    							else if($holiday && $isCreditedHoliday) $absent = "";

    			                if ($vl > 0 || $el > 0 || $sl > 0 || $ob > 0 || $service_credit > 0){
        		                    $absent = "";
        		                }

        		                $count_leave = $vl > 0 ? $vl : ( $el > 0 ? $el : ( $sl > 0 ? $sl : ( $ob > 0 ? $ob : ( $service_credit > 0 ? $service_credit : 0 ) ) ) ) ;

					        	// Late / Undertime
					        	$lateutlec = $lateutlab = $lateutadmin = '';
					        	list($utlec,$utlab,$utadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUTFlexi($log,$rsched->hours,$rsched->mode,$type,$absent,$rsched->breaktime, $count_leave);

					        	if($el >= 1 || $vl >= 1 || $sl >= 1 || $ob >= 1 || $service_credit >= 1 || ($holiday && $isCreditedHoliday)){
					        	     $utlec = $utlab = $utadmin = $tschedlec = $tschedlab = $tschedadmin = "";
					        	}
					        	

					        	if(date("Y-m-d",strtotime($lateutlec)) < $rdate->dte)
					        	{
					        		$lateutlec = $lateutlab = "";
					        	}


    							$hasOL = $ol ? ($ol != 'CORRECTION' && $ol != 'undertime' && $ol != 'late' ? true : false) : false; 

    	        	            if(!$fixedday){
    	        	            	if($absent=='' || $hasOL){
    	        	            		$tsched   = round(abs(strtotime($stime) - strtotime($etime)) / 60,2);
    	        	            		$tsched   = date('H:i', mktime(0,$tsched));
    	        	            		if($type == 'LEC' && ($tschedlec=='' || $hasOL))       $work_lec =  $tsched;
    	        	            		elseif($type == 'LAB' && ($tschedlab=='' || $hasOL))   $work_lab = $tsched;
    	        	            		else {
    	        	            			if($tschedadmin=='' || $hasOL) $work_admin = $tsched;
    	        	            		}
    	        	            	}
    	        	            }

					        	// Service Credit
					        	/*if(is_null($service_credit))
					        	{
					        		$service_credit    = $this->attcompute->displayServiceCredit($empid,$rdate->dte);
					        		$service_credit = $service_credit?$service_credit:null;
					        	}

					        	if($absent)
					        	{
					        		if($service_credit <> 0 || $service_credit){
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
					        	}*/



					        	$log_remarks = '';

					        	$login = $logout = $q = "";
					        	if(count($log) > 0)
					        	{
					        		for($i = 0;$i < count($log);$i++)
					        		{
					        			$login = $log[$i][0];
					        			$logout = $log[$i][1];

					        			if($login=='0000-00-00 00:00:00') $login = "";
				        				if($logout=='0000-00-00 00:00:00') $logout = "";

							        	if($absent){
							        		if(!$login && !$logout) $log_remarks = 'NO TIME IN AND OUT';
							        		elseif(!$login) $log_remarks = 'NO TIME IN';
							        		elseif(!$logout) $log_remarks = 'NO TIME OUT';
							        	}

							        	if($lateutadmin){
							            	if(in_array("late", $ob_data)) $log_remarks = "EXCUSED LATE";
							            	else{ 
							            			$log_remarks = "UNEXCUSED LATE";
							            			$ob_type = false;
							            		}
							            }

										if($utadmin){
											if(in_array("undertime", $ob_data)) $log_remarks = "EXCUSED UNDERTIME";
								            else{
								            		$log_remarks = "UNEXCUSED UNDERTIME";
								            		$ob_type = false;
								            }
								        }

										if($absent){
											if(in_array("absent", $ob_data)) $log_remarks = "EXCUSED ABSENT";
								            else{
								            		$log_remarks = "UNEXCUSED ABSENT";
								            		$ob_type = false;
								            }
								        }

					        			$q = $log[$i][2];
					        			if($q) $totalQ++;
					        			?>
					        			<tr class="edata" <?=$color?>>
    							            <?if($dispLogDate){
    							            	?>

    							                <td class="align_center" rowspan="<?=$countrow?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
    							            <?}?>
    							            <td class="align_center" key="ss" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime ? date("h:i A",strtotime($stime)) : "--")?></td>
    							            <td class="align_center" key="es" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime ? date("h:i A",strtotime($etime)) : "--")?></td>
    							            <td class="align_center" key="ti" 
    							            	style="<?=($absent || $tschedadmin || $ol == "ABSENT") ? "background-color: #ffe6e6;" : ((($lateutlec || $lateutlab || $lateutadmin) && date("H:i",strtotime($stime)) < date("H:i",strtotime($login)))?"color:red":"")?>">
    							            	<?=(($login ) ? date("h:i A",strtotime($login)) : "--")?></td>
    							            <td class="align_center" key="to" 
    							            	style="<?=($absent || $tschedadmin || $ol == "ABSENT") ? "background-color: #ffe6e6;" : ((($utlec || $utlab || $utadmin) && date("H:i",strtotime($logout)) < date("H:i",strtotime($etime)))?"color:red":"")?>">
    							            	<?=(($logout ) ? date("h:i A",strtotime($logout)) : "--")?></td>

    							            <?if($dispLogDate){?>
    											<td class="align_center" key="otr"   rowspan="<?=$countrow?>"><?=($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):""?></td>
    											<td class="align_center" key="otrest" rowspan="<?=$countrow?>"><?=($otrest)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otrest)):""?></td>
    											<td class="align_center" key="othol" rowspan="<?=$countrow?>"><?=($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):""?></td>
    										<?}?>

    										<!-- <td class="align_center" key="overload"><?=($lastDayOfWeek == date("l",strtotime($rdate->dte)))?($overload?$this->attcompute->sec_to_hm($overload):""):""?></td> -->
    										<!-- <td class="align_center" key="subtitute"></td> -->

<!--     										<td class="align_center" key="utlec"></td>
    							            <td class="align_center" key="utlab"></td>
    							            <td class="align_center" key="utlab"></td>
    										<td class="align_center" key="utlec"><?=$utlec?$utlec:""?></td>
    							            <td class="align_center" key="utlab"><?=$utlab?$utlab:""?></td> -->
    							            <td class="align_center" key="utlab"><?=$utadmin?$utadmin:""?></td>
    							            <td class="align_center" key="dadmin"><?=(!$fixedday && !$hasOL) ? '' : $tschedadmin?></td>
    							            <!-- <td class="align_center" key="ab" style='<?=($absent)?"background-color: #ffe6e6;":""?>'><?=$absent?$absent:""?></td> -->
    							            <!-- <td class="align_center" key="ab" style='<?=($absent)?"background-color: #ffe6e6;":""?>'><?=$absent?1:""?></td> -->
    							            <?if($dispLogDate){?>
    							                <td class="align_center" rowspan="<?=$countrow?>" key="vl"><?=$vl?></td>
    							                <td class="align_center" rowspan="<?=$countrow?>" key="sl"><?=$sl?></td>
    							                <td class="align_center" rowspan="<?=$countrow?>" key="el"><?=$el?></td>
    							                <!-- <td class="align_center" rowspan="<?=$countrow?>" key="sc"><?=$service_credit?></td> -->

    							                <td class="align_center" rowspan="<?=$countrow?>" key="ol" style="<?=($pending)?'background-color:#b3d9ff;':""?>">
    							                	<?=($log_remarks?$log_remarks."<br>":"")?>
    							                	<?=($cs_app?$cs_app."<br>":"")?>
    												<?=($pending)?"PENDING ".$pending."<br>":($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" : ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)."<br>") : '')?> 
    												<?=$service_credit?'SERVICE CREDIT<br>':''?>
    												<?=(isset($holidayInfo["description"]) ? $holidayInfo["description"] : "")?>
    							                	
    						                	</td>
    							            <?}?>
    							            <!-- <td class="align_center" key="dlec"><?=(!$fixedday && !$hasOL) ? '' : $tschedlec?></td> -->
    							            <!-- <td class="align_center" key="dlab"><?=(!$fixedday && !$hasOL) ? '' : $tschedlab?></td> -->
    							            <td class="align_center" key="" <?=$hide_from_fixedday?> ><?=$work_lec?></td>
	            				            <td class="align_center" key="" <?=$hide_from_fixedday?> ><?=$work_lab?></td>
	            				            <td class="align_center" key="" <?=$hide_from_fixedday?> ><?=$work_admin?></td>
    							            <?php if(isset($holidayInfo["halfday"])){ ?>
											<td class="align_center" rowspan="<?=$rwcount?>" style="vertical-align : middle;text-align:center;"><?=(isset($holidayInfo['type']) && $hasHalfdayHoliday ? $holidayInfo['type'] : '')?></td>
								            <?php }else{ ?>
								            	<td class="align_center" rowspan="<?=$countrow?>" style="vertical-align : middle;text-align:center;"><?=(isset($holidayInfo['type']) ? $holidayInfo['type'] : '')?></td>
								            <?php } ?>
								            <?if($dispLogDate){?>
								                <!-- <td class="align_center" rowspan="<?=$countrow?>"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->
								            <?}?>
    							        </tr>
					        			<?
					        			$stime = $etime = "";
					        		}
					        	}
					        	else
					        	{
					        		if($absent) $log_remarks = 'NO TIME IN AND OUT';
					        		?>
					        		<tr class="edata" <?=$color?>>
					        			<?if($stime && $etime){
					        				if($dispLogDate){?>
					        					<td class="align_center" rowspan="<?=$countrow?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
					        				<?}
					        			?>
					        				<td class="align_center" rowspan="<?=$countrow?>" key="ss" etype="nt" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime != "00:00:00" ? date("h:i A",strtotime($stime)) : "--")?></td>
					        				<td class="align_center" rowspan="<?=$countrow?>" key="es" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime != "00:00:00" ? date("h:i A",strtotime($etime)) : "--")?></td>
					        			<?}?>
					        			<td class="align_center" key="ti"><?=$login?date("h:i A",strtotime($login)):"--"?></td>
					        			<td class="align_center" key="to"><?=$logout?date("h:i A",strtotime($logout)):"--"?></td>
					        					
					        			<?if($stime && $etime){?>
					        				<?if($dispLogDate){?>
					        				<td class="align_center" key="otr"   rowspan="<?=$countrow?>"><?=($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):""?></td>
					        				<td class="align_center" key="otrest" rowspan="<?=$countrow?>"><?=($otrest)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otrest)):""?></td>
					        				<td class="align_center" key="othol" rowspan="<?=$countrow?>"><?=($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):""?></td>
					        				<?}
					        			?>
<!-- 		        						<td class="align_center" key="utlec"></td>
		        			            <td class="align_center" key="utlab"></td>
		        			            <td class="align_center" key="utlab"></td>
					        			<td class="align_center" key="utlec"><?=$utlec?$utlec:""?></td>
							            <td class="align_center" key="utlab"><?=$utlab?$utlab:""?></td> -->
							            <td class="align_center" key="utlab"><?=$utadmin?$utadmin:""?></td>
							            <td class="align_center" key="dadmin"><?=(!$fixedday && !$hasOL) ? '' : $tschedadmin?></td>
					        			<?if($dispLogDate){?>
					        				<td class="align_center" rowspan="<?=$countrow?>" key="vl"><?=$vl?></td>
					        				<td class="align_center" rowspan="<?=$countrow?>" key="sl"><?=$sl?></td>
					        				<td class="align_center" rowspan="<?=$countrow?>" key="el"><?=$el?></td>
					        				<!-- <td class="align_center" rowspan="<?=$countrow?>" key="sc"><?=$service_credit?></td> -->
					        				<td class="align_center" rowspan="<?=$countrow?>" key="ol" style="<?=($pending)?"background-color:#b3d9ff;":""?>">
					        					<?=($log_remarks?$log_remarks."<br>":"")?>
        					                	<?=($cs_app?$cs_app."<br>":"")?>
        										<?=($pending)?"PENDING ".$pending."<br>":($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" : ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)."<br>") : '')?> 
        										<?=$service_credit?'SERVICE CREDIT<br>':''?>
        										<?=(isset($holidayInfo["description"]) ? $holidayInfo["description"] : "")?>

					        				</td>
					        				<?}
					        			}?>
					        			<!-- <td class="align_center" key="dlec"><?=(!$fixedday && !$hasOL) ? '' : $tschedlec?></td> -->
							            <!-- <td class="align_center" key="dlab"><?=(!$fixedday && !$hasOL) ? '' : $tschedlab?></td> -->
							            <td class="align_center" key="" <?=$hide_from_fixedday?> ><?=$work_lec?></td>
							            <td class="align_center" key="" <?=$hide_from_fixedday?> ><?=$work_lab?></td>
							            <td class="align_center" key="" <?=$hide_from_fixedday?> ><?=$work_admin?></td>
							            <?php if(isset($holidayInfo["halfday"])){ ?>
											<td class="align_center" rowspan="<?=$rwcount?>" style="vertical-align : middle;text-align:center;"><?=(isset($holidayInfo['type']) && $hasHalfdayHoliday ? $holidayInfo['type'] : '')?></td>
							            <?php }else{ ?>
							            	<td class="align_center" rowspan="<?=$countrow?>" style="vertical-align : middle;text-align:center;"><?=(isset($holidayInfo['type']) ? $holidayInfo['type'] : '')?></td>
							            <?php } ?>
							            <?if($dispLogDate){?>
							                <!-- <td class="align_center" rowspan="<?=$countrow?>"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->
							            <?}?>
					        		</tr>
					        		<?
					        	}
					        }///< end if FLEXIBLE/NOT

				            $tempsched = $dispLogDate;
				            
				            /*
				             * Total
				             */ 

				           
				            
				            // Absent
							if($absent){
								if(!$fixedday && !$hasOL) 	{}
				                else $tabsent += $this->attcompute->exp_time($absent) > 0 ? 1 : 0;
				            }
							
				            
				            // Late
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


				            // UT
				            if($tutlec){
				                $secs  = strtotime($utlec)-strtotime("00:00:00");
				                if($secs>0) $tutlec = date("H:i",strtotime($tutlec)+$secs);
				            }else
				                $tutlec    = $utlec;
				                
				            if($tutlab){
				                $secs  = strtotime($utlab)-strtotime("00:00:00");
				                if($secs>0) $tutlab = date("H:i",strtotime($tutlab)+$secs);
				            }else
				                $tutlab    = $utlab;

				            if($tutadmin){
				                $secs  = strtotime($utadmin)-strtotime("00:00:00");
				                if($secs>0) $tutadmin = date("H:i",strtotime($tutadmin)+$secs);
				            }else
				                $tutadmin    = $utadmin;


				            
				            // Leave
				            // if($dispLogDate){
				            	$tel      += ($el) ? 0.5 : 0;
								$tvl      += ($vl) ? 0.5 : 0;
								$tsl      += ($sl) ? 0.5 : 0;
								$tol      += ($ol ? 1 : "") + ($q ? ($q == 1 ? "" : 1) : "") ;
				            // }
				            
				            // Deductions
				            if(isset($tschedlec)){
			                	if(!$fixedday && !$hasOL) 	{}
			                    else $tdlec += $this->attcompute->exp_time($tschedlec);
				            }
				            if(isset($tschedlab)){
			                	if(!$fixedday && !$hasOL) 	{}
			                    else $tdlab += $this->attcompute->exp_time($tschedlab);
				            }
				            if(isset($tschedadmin)){
			                	if(!$fixedday && !$hasOL) 	{}
			                    else $tdadmin += $this->attcompute->exp_time($tschedadmin);
				            }

				            if((!$tschedadmin && !$absent) || $hasOL) $hasLog = true;
				            $hasLog = $hasLog ? $hasLog : ($hasOL ? true : false); 

				            if($fixedday){
			            		$tsched   = round(abs(strtotime($stime) - strtotime($etime)) / 60,2);
			            		$tsched   = date('H:i', mktime(0,$tsched));
			            		$tsched   = $this->attcompute->exp_time($tsched);
			            		if($type == 'LEC')       $twork_lec +=  $tsched;
			            		elseif($type == 'LAB')   $twork_lab += $tsched;
			            		else                     $twork_admin += $tsched;
				            }else{
			            		if($type == 'LEC' && $work_lec)       $twork_lec +=  $this->attcompute->exp_time($work_lec);
			            		elseif($type == 'LAB' && $work_lab)   $twork_lab += $this->attcompute->exp_time($work_lab);
			            		else {
			            			if($work_admin) $twork_admin += $this->attcompute->exp_time($work_admin);
			            		}
				            }

			            	if($service_credit && ($dispLogDate || !$haswholedayleave)) $t_service_credit+=$service_credit;
			        $hasHalfdayHoliday = false;
		            $isFirstSched = false;
		            if(isset($holidayInfo["halfday"])) $isCreditedHoliday = false;
		        } ///< end foreach sched

		        if($otreg){
		        			$totr += $this->attcompute->exp_time($otreg);
		        		}

		        if($otrest){
		        			$totrest += $this->attcompute->exp_time($otrest);
		        		}

		        if($othol){
		        			$tothol += $this->attcompute->exp_time($othol);
		        		}

		    }
		    else{
				///< no sched or not valid sched
		    	$countrow = 1;
				$totalQ = 0;
				$stime = "";
				$etime = ""; 
						
				$log = $this->attcompute->displayLogTimeFlexi($empid,$rdate->dte,$edata);

				// Overtime
				list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,false);

				// Leave Pending
				$pending = $this->attcompute->displayPendingApp($empid,$rdate->dte);

				if($otreg){
							$totr += $this->attcompute->exp_time($otreg);
						}

				if($otrest){
							$totrest += $this->attcompute->exp_time($otrest);
						}

				if($othol){
							$tothol += $this->attcompute->exp_time($othol);
						}

				// Leave
				list($el,$vl,$sl,$ol,$oltype,$ob)     = $this->attcompute->displayLeave($empid,$rdate->dte);

		        // Leave
		        // list($el,$vl,$sl,$ol,$oltype,$ob)     = $this->attcompute->displayLeave($empid,$rdate->dte,"",$stime,$etime);

		        //Service Credit 
				$service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);
				
				if(count($log)> 0)
				{
					///< no sched with log

					$login = $logout = $q = "";
					$stime = $etime = "--";
					for($i = 0;$i < count($log);$i++)
					{
						$login = $log[$i][0];
						$logout = $log[$i][1];
						$q = $log[$i][2];
						if($q) $totalQ++;
		?>
					<tr class="edata" style="background-color: gray;color:white">
		<?if($stime && $etime){
			if($dispLogDate){
				?>
						<td class="align_center" rowspan="<?=$countrow?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
			<?}?>
						<td class="align_center" rowspan="<?=$countrow?>" key="ss" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=$stime?></td>
						<td class="align_center" rowspan="<?=$countrow?>" key="es" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=$etime?></td>
		<?}?>
						<td class="align_center" key="ti" style=""><?=$login?date("h:i A",strtotime($login)):"--"?></td>
						<td class="align_center" key="to" style=""><?=(!$logout)?"--":($logout == "00:00:00")?"--":date("h:i A",strtotime($logout))?></td> 
		<?if($stime && $etime){?>

						<td class="align_center" key="otr"   ><?=$otreg?$otreg:"--"?></td>
						<td class="align_center" key="otrest" ><?=$otrest?$otrest:"--"?></td>
						<td class="align_center" key="othol" ><?=$othol?$othol:"--"?></td>

						<!-- <td class="align_center" rowspan="<?=$countrow?>" key="overload">--</td> -->
						<!-- <td class="align_center" rowspan="<?=$countrow?>" key="subtitute">--</td> -->

<!-- 						<td class="align_center" rowspan="<?=$countrow?>" key="utlec"><?=$lateutlec?$lateutlec:""?></td>
						<td class="align_center" rowspan="<?=$countrow?>" key="utlab"><?=$lateutlab?$lateutlab:""?></td> -->
						<td class="align_center" rowspan="<?=$countrow?>" key="utlab"><?=$lateutadmin?$lateutadmin:""?></td>
<!-- 						<td class="align_center" rowspan="<?=$countrow?>"></td>
						<td class="align_center" rowspan="<?=$countrow?>"></td> -->
						<td class="align_center" rowspan="<?=$countrow?>"></td>
						<td class="align_center" rowspan="<?=$countrow?>" key="dadmin">--</td>
						<!-- <td class="align_center" rowspan="<?=$countrow?>" key="ab" style="">--</td> -->
						<?if($dispLogDate){?>
						<td class="align_center" rowspan="<?=$countrow?>" key="vl"><?=($vl?$vl:"--")?></td>
						<td class="align_center" rowspan="<?=$countrow?>" key="sl"><?=($sl?$sl:"--")?></td>
						<td class="align_center" rowspan="<?=$countrow?>" key="el"><?=($el?$el:"--")?></td>
						<!-- <td class="align_center" rowspan="<?=$countrow?>" key="sc"><?=($service_credit?$service_credit:"--")?></td> -->
						<td class="align_center" rowspan="<?=$countrow?>" key="ol"  style="<?=($pending)?'background-color:#b3d9ff;':""?>">

							<!-- <?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : ($q ? ($q == "1" ? "" : $q) : ""))?> <?=$holiday?$holidayInfo["description"]:""?> -->
							
		                	<?=($cs_app?$cs_app.'<br>':"")?>
							<?=($pending)?"PENDING ".$pending.'<br>':($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" : ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)."<br>") 
								: '')?> 
							<?=$service_credit?'SERVICE CREDIT<br>':''?>
							<?=(isset($holidayInfo["description"]) ? $holidayInfo["description"] : "")?>

							<!-- <?if(strpos($pending, "SERVICE CREDIT") === false && !$service_credit){?>
								<a class="btn btn-primary" id="applysc" href="#" data-toggle="modal" data-target="#myModal1" style="width: 80px; line-height: 12px;" dateInitial="<?=$rdate->dte?>" >Apply as Service Credit</a>
								<span class="notifdiv bell" style="position: relative;top:5px;"><i class="glyphicon glyphicon-bell large" style="color: #FF1744;font-size: 20px;"></i></span>
							<?}?> -->

						</td>
						<?}?>
						<!-- <td class="align_center" rowspan="<?=$countrow?>" key="dlec">--</td> -->
						<!-- <td class="align_center" rowspan="<?=$countrow?>" key="dlab">--</td> -->
						<td class="align_center" key="" <?=$hide_from_fixedday?> >--</td>
						<td class="align_center" key="" <?=$hide_from_fixedday?> >--</td>
						<td class="align_center" key="" <?=$hide_from_fixedday?> >--</td>

					<?if($dispLogDate){?>	
					
						<td class="align_center" rowspan="<?=$countrow?>"><?=(isset($holidayInfo["type"]) ? $holidayInfo["type"] : "")?></td>
		<?}
		}?>
					    <?if($dispLogDate){?>
			                <!-- <td class="align_center" rowspan="<?=$countrow?>"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->
			            <?}?>
					</tr>
		<?
								$stime = $etime = "";
					}
				}
				else
				{
					///< no sched no log
					
		?>
					<tr class="edata" style='background-color: gray;color:white'>
						<td class="align_center" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?> </td>
						<td class="align_center" key="ss" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >--</td>
						<td class="align_center" key="es" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >--</td>
						<td class="align_center" key="ti" style="">--</td>
						<td class="align_center" key="to" style="">--</td>
						<td class="align_center" key="otr"   ><?=$otreg?$otreg:"--"?></td>
						<td class="align_center" key="otrest" ><?=$otrest?$otrest:"--"?></td>
						<td class="align_center" key="othol" ><?=$othol?$othol:"--"?></td>
						<!-- <td class="align_center" key="overload">--</td> -->
						<!-- <td class="align_center" key="subtitute">--</td> -->
<!-- 						<td class="align_center" key="utlec">--</td>
						<td class="align_center" key="utlab">--</td> -->
						<td class="align_center" key="utadmin">--</td>
<!-- 						<td class="align_center" key="utlec">--</td>
						<td class="align_center" key="utlab">--</td> -->
						<td class="align_center" key="dadmin">--</td>
						<td class="align_center" key="utadmin">--</td>
						<!-- <td class="align_center" key="ab" style="">--</td> -->
						<td class="align_center" key="vl"><?=($vl?$vl:"--")?></td>
						<td class="align_center" key="sl"><?=($sl?$sl:"--")?></td>
						<td class="align_center" key="el"><?=($el?$el:"--")?></td>
						<!-- <td class="align_center" key="sc"><?=($service_credit?$service_credit:"--")?></td> -->
						<td class="align_center" key="ol"  style="<?=($pending)?'background-color:#e01414;':''?>" >
								<span style="<?=($pending)?'background-color:#e01414;color:white;':''?>"><?= ($pending) ? "PENDING ".$pending : "" ?></span>
								<span style="text-align:center;"><?=($otreg || $otrest || $othol)?'OVERTIME APPLICATION<br>':""?></span>
								<?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)) : "")?> 
								<?=$service_credit?'SERVICE CREDIT<br>':''?>
								<?=(isset($holidayInfo["description"]) ? $holidayInfo["description"] : "")?></td>
						<!-- <td class="align_center" key="dlec">--</td> -->
						<!-- <td class="align_center" key="dlab">--</td> -->
						<td class="align_center" key="" <?=$hide_from_fixedday?> >--</td>
						<td class="align_center" key="" <?=$hide_from_fixedday?> >--</td>
						<td class="align_center" key="" <?=$hide_from_fixedday?> >--</td>
						<td class="align_center"><?=(isset($holidayInfo["type"]) ? $holidayInfo["type"] : "")?></td>
			            <?if($dispLogDate){?>
			                <!-- <td class="align_center" rowspan="<?=$countrow?>"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->
			            <?}?>
					</tr>
		<?
				}
						
				/*
				 * Total
				 */ 
						
				// Leave
				// if($dispLogDate){
					$tel      += ($el) ? 0.5 : 0;
					$tvl      += ($vl) ? 0.5 : 0;
					$tsl      += ($sl) ? 0.5 : 0;
					$tol      += ($ol ? 1 : "") + ($totalQ ? ($totalQ == 1 ? "" : 1) : "") ;
				// }

				if($service_credit && ($dispLogDate || !$haswholedayleave)) $t_service_credit+=$service_credit;
			} ///< end else no sched

			// total holiday
			if($holiday) $tholiday++;
			else if(isset($holidayInfo["halfday"])) $tholiday += 0.5;

			if($lastDayOfWeek == date("l",strtotime($rdate->dte)))
			{
				$tOverload += $overload;
				$overload = $tempOverload = 0;
			}
  	  	
  	  		$holiday = '';
  			$firstDate = true;
  }

  $twork_lec = $twork_lec ? $this->attcompute->sec_to_hm($twork_lec) : "";
  $twork_lab = $twork_lab ? $this->attcompute->sec_to_hm($twork_lab) : "";
  $twork_admin = $twork_admin ? $this->attcompute->sec_to_hm($twork_admin) : "";


  $tdlec = ($tdlec ? $this->attcompute->sec_to_hm($tdlec) : "");
  $tdlab = ($tdlab ? $this->attcompute->sec_to_hm($tdlab) : "");
  $tdadmin = ($tdadmin ? $this->attcompute->sec_to_hm($tdadmin) : "");
  // $tabsent = ($tabsent ? $this->attcompute->sec_to_hm($tabsent) : "");
  ?>
  <tr class="edata">
        <th class="align_right" <?= ($usertype == "EMPLOYEE") ? 'colspan="3"' : 'colspan="5"' ?>><b>TOTAL</b></th>
            <!-- <th class="align_center"><?=$tOverload = ($tOverload ? $this->attcompute->sec_to_hm($tOverload) : "")?></th> -->
            <th class="align_center"><?=$totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "")?></th>
            <th class="align_center"><?=$totrest   = ($totrest ? $this->attcompute->sec_to_hm($totrest) : "")?></th>
            <th class="align_center"><?=$tothol   = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "")?></th>
<!--             <th class="align_center"><?=$tlec?></th>
            <th class="align_center"><?=$tlab?></th> -->
            <th class="align_center"><?=$tadmin?></th>
<!--             <th class="align_center"><?=$tutlec?></th>
            <th class="align_center"><?=$tutlab?></th> -->
            <th class="align_center"><?=$tutadmin?></th>
            <!-- <th class="align_center"><?=$tabsent?></th> -->
            <th class="align_center"><?=$tdadmin?></th>
            <th class="align_center"><?=$tvl?></th>
            <th class="align_center"><?=$tsl?></th>
            <th class="align_center"><?=$tel?></th>
            <!-- <th class="align_center"><?=$t_service_credit?></th> -->
            <th class="align_center"></th>
            <!-- <th class="align_center"><?=$tdlec?></th> -->
            <!-- <th class="align_center"><?=$tdlab?></th> -->
            <th class="align_center"<?=$hide_from_fixedday?> ><?=$twork_lec?></th>
            <th class="align_center"<?=$hide_from_fixedday?> ><?=$twork_lab?></th>
            <th class="align_center"<?=$hide_from_fixedday?> ><?=$twork_admin?></th>
            <th class="align_center"><?=$tholiday?></th>
            <!-- <th class="align_center"><?=$this->attcompute->sec_to_hm($total_perday_absent)?></th> -->
      </tr>
    </tbody>
</table>
</div>
