<?
$qdate = $this->attcompute->displayDateRange($from_date, $to_date);
$fixedday = $this->attcompute->isFixedDay($empid);
// $hide_from_fixedday = $fixedday ? ' hidden':'';
$hide_from_fixedday = ' hidden';
$usertype = $this->session->userdata('usertype');
?>

    <div class="panel-body" style="border: transparent !important;padding: 0px !important; margin-top: 1%;">
    <h2>Attendance</h2>
    <p><?=$datedisplay?></p>
    <p><?=$this->employee->getfullname($empid)?></p>
    <table class="table table-bordered datatable" id="indvtblnt">
        <thead>
            <tr style="background-color: #0072c6">
                <th rowspan="2" class="align_center">Date</th>
                <th class="align_center" colspan="2" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >Official Time</th>
                <th class="align_center" colspan="2">Actual Log Time</th>
				<!--<th rowspan="2" class="align_center">Overload</th>-->
                <th class="align_center" colspan="3">Overtime (hr:min)</th>
                <th class="align_center">Late</th>
                <th class="align_center">Undertime</th>
                <th class="align_center" rowspan="2">Absent</th>                        
                <th class="align_center" colspan="3">Leaves</th>
                <!-- <th class="align_center" rowspan="2">Service Credit</th> -->
                <th class="align_center" rowspan="2" >Remarks/Others</th>
                <th class="align_center" rowspan="2" <?=$hide_from_fixedday?> >No. of Days</th>
                <th class="align_center" rowspan="2" >Holiday</th>
                <!-- <th class="align_center" rowspan="2" >Total Per Day</th> -->
            </tr>
            <tr style="background-color: #0072c6">
                <th class="align_center" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >IN</th><th class="align_center" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >OUT</th>
                <th class="align_center">IN</th><th class="align_center">OUT</th>
                <th class="align_center">Regular</th>
                <th class="align_center">Rest Day</th>
                <th class="align_center">Holiday</th>
                <th class="align_center">Hr:min</th>            
                <th class="align_center">Hr:min</th>            
                <th class="align_center">Vacation</th>
                <th class="align_center">Sick</th>
                <th class="align_center">Other</th>
            </tr>
        </thead>
        <tbody id="employeelist">
    <?

    $x = $totr = $totrest = $tothol = $tlec = $tutlec= $absent = $tabsent = $tabsentperday = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = $pending = $tempOverload = $overload = $tOverload = $lastDayOfWeek = $cs_app = $date_tmp = ""; 
    $tempabsent = $lateutlec= $lateutlab = $lateutadmin = $utlec= $utlab = $utadmin = $tutlec = $tutlab = $tutadmin = $twork_lec = $twork_lab = $twork_admin = $work_lec = $work_lab = $work_admin = "";
    $tlec = $workdays = $tworkdays = 0 ;
	$tempabsent = "";
	$t_service_credit = $service_credit = "";
	$seq_new = 0;
	$perday_absent = $total_perday_absent = 0;
	$login_new = $logout_new = $q_new = $haslog_forremarks_new = "";
	$not_included_ol = array("ABSENT", "EL", "VL", "SL", "CORRECTION");

	$hasLog = $isSuspension = false;

    $isCreditedHoliday = false;
    $hasHalfdayHoliday = false;
	$firstDate = true;
	$ob_data = array();
	$holidayInfo = array();
	$teachingtype = $this->extensions->getEmployeeTeachingType($empid);

	foreach ($qdate as $rdate) {
				
			// Holiday
    		$is_holiday_valid = false;
			$isSuspension = false;
		    $holiday = $this->attcompute->isHolidayNew($empid,$rdate->dte,$deptid,"","",$teachingtype ); 
			$holidayInfo = $this->attcompute->holidayInfo($rdate->dte, "", $teachingtype, "",$holiday);
			if($holiday)
			{
				if($holidayInfo){
					if($holidayInfo["code"]=="SUS") $isSuspension = true;
					// if($holidayInfo["withPay"]=='NO' || !$holidayInfo["withPay"]) $holiday = '';
					// if($holidayInfo["holiday_rate"] <= 0) $holiday = ''; 
				}
				$is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
			}
            // $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
            if(!$is_holiday_valid){
                $holidayInfo = array();
                $holiday = "";
            }
			
		    $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
		    $sched = $this->attcompute->displaySched($empid,$rdate->dte);

		    $countrow = $sched->num_rows();

		    $isValidSchedule = true;

		    if($countrow > 0){
		    	if($sched->row(0)->starttime == '00:00:00' && $sched->row(0)->endtime == '00:00:00') $isValidSchedule = false;
		    }
		    // echo $isValidSchedule;
		    if($x%2 == 0)   $color = " style='background-color: white;'";
		    else            $color = " style='background-color: #fafafa;'";
		    $x++;
			
		    ///< for validation of absent for 1st day in range. this will check for previous day attendance

	    	if($firstDate && $holiday){
	    		$hasLog = $this->attendance->checkPreviousSchedAttendanceNonTeaching($rdate->dte,$empid);
	    		$firstDate = false;

	    	}
				// echo "<pre>"; print_r($countrow);

		    if($countrow > 0 && $isValidSchedule){
		    	$haswholedayleave = false;
		    	$hasleavecount = 0;

		    	///< for validation of holiday (will only be credited if not absent during last schedule)
				$hasLogprev = $hasLog;
		    	$hasLog = false;
		    	
		    	if($hasLogprev || $isSuspension) 	$isCreditedHoliday = "true";
		    	else 								$isCreditedHoliday = "false";
		        $tempsched = "";
		        $seq = 0;
				$service_credit = null;
				$service_credit_used = 0;

				$isFirstSched = true;
				$q_sched = $sched;
				$perday_absent = $this->attendance->getTotalAbsentPerday($sched->result(), $empid, $rdate->dte);
				$total_perday_absent += $perday_absent;
		        foreach($sched->result() as $rsched){

		        	if(!$is_holiday_valid && $isFirstSched){
						$holidayInfo = $this->attcompute->holidayInfo($rdate->dte, "second");
					}



		        	$workdays = 0;

		        	
		        	$ob_type = true;
					//NOT FLEXIBLE
					if($rsched->flexible != "YES")
					{
						// echo 'NOT FLEXIBLE';
						if($tempsched == $dispLogDate){  $dispLogDate = "";}
						$stime  = $rsched->starttime;
						$etime  = $rsched->endtime; 
						$tstart = $rsched->tardy_start; 
						$absent_start = $rsched->absent_start;
						$earlyd = $rsched->early_dismissal;
						if($earlyd > $etime) $earlyd = $etime;
						
						$seq += 1;
						
						// Leave -- inuna ko lang muna. nag kabug kasi last time sa correction..
						list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$lnopay)  = $this->attcompute->displayLeave($empid,$rdate->dte,'',$stime,$etime,$seq);
						// echo "<pre>"; print_r($this->db->last_query());
						// echo "<pre>"; print_r($absent_start);
						// logtime
						list($login,$logout,$q,$haslog_forremarks)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq,$absent_start,$earlyd);
						 // echo "<pre>"; print_r($q); die;


						 // Overtime
						list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,true);
						
						// // Leave
						// list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$lnopay)  = $this->attcompute->displayLeave($empid,$rdate->dte,'',$stime,$etime,$seq);
						
						//late-under-undertime remarks
				        $ob_data = $this->attcompute->displayLateUTAbs($empid, $rdate->dte);

						//Service Credit 
						$service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);

						// Change Schedule
						$cs_app = $this->attcompute->displayChangeSchedApp($empid,$rdate->dte);
						
						
						// Leave Pending
						$pending = $this->attcompute->displayPendingApp($empid,$rdate->dte, "", $stime, $etime);

						$pending_ob = $this->attcompute->displayPendingOBApp($empid,$rdate->dte, ($seq==1) ? "late" : "undertime");
						if($ob) $pending_ob = "";
						 // Absent
						
						$absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd, $isFirstSched,$absent_start);
						// echo "<pre>"; print_r($absent);
						if($oltype == "ABSENT") $absent = $absent;
						else if($holiday && $isCreditedHoliday) $absent = "";

		                if ($vl >= 1 || $el >= 1 || $sl >= 1 || ($ol && !$ob && $ol !="CORRECTION" && !$lnopay) || $ob >= 1 || $service_credit >= 1){
		                    $absent = "";
		                    $haswholedayleave = true;
		                }
		                if ($vl > 0.5 || $el > 0.5 || $sl > 0.5 || ($ol && !$ob && $ol !="CORRECTION" && !$lnopay) || $ob > 0.5 || $service_credit > 0){
		                	$absent = "";
		                    $hasleavecount+=0.5;
		                }
						if($abs_count >= 1) $haswholedayleave = true;

						// Late / Undertime

						$lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent,$teachingtype,$tstart);
						// if($lateutlec){
						// 	echo "<pre>"; print_r($stime);
						// 	echo "<pre>"; print_r($etime);
						// 	echo "<pre>"; print_r($login);
						// 	echo "<pre>"; print_r($logout);
						// 	echo "<pre>"; print_r($tstart); die;
						// }
						$utlec 	= $this->attcompute->computeUndertimeNT($stime,$etime,$login,$logout,$absent,$teachingtype,$earlyd);
						
						if($el || $vl || $sl  || $ol || (!$ob && $ol !="CORRECTION" && $lnopay && $q != 'Facial' && $q != 'webcheckin' && $q != 'Fingerprint') || $service_credit || ($holiday && $isCreditedHoliday)) $lateutlec = $utlec = "";
						// echo "<pre>"; print_r($utlec);
						

						// if($ob) $el += $ob;
										
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
							$tempabsent = $absent;
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

						$hasOL = $ol ? (($ol != 'CORRECTION' && $ol != 'DIRECT') && $ol != 'undertime' && $ol != 'late' && $ol == 0 ? true : false) : false; 

						if($hasOL && !$ob) $login = $logout = "";

						if(!$fixedday){
							if($absent=='' || $hasOL) $workdays=1;
						}
						
						if(!$haswholedayleave){
							if($isFirstSched){
					        	if(!$login && $absent) $login = $this->attcompute->getLogin($empid, $edata, $rdate->dte);
					        	if(!$logout && $absent) $logout = $this->attcompute->getLogout($empid, $edata, $rdate->dte);
								if($login && $logout && !$absent){
									$lateutlec = $this->attcompute->displayLateUTNT($stime, $etime, $login, $logout, "", "", $tstart);
									$utlec = $this->attcompute->computeUndertimeNT($stime,$etime,$login,$logout,"","",$earlyd);
									if($absent) $lateutlec = $absent;
									if($utlec || $lateutlec) $log_remarks = $absent = "";
									$hasLog = TRUE;
								}else{
									 foreach($sched->result() as $rsched){
									 	if(isset($sched_new[1]->starttime)) $stime  = $rsched->starttime;
										if(isset($sched_new[1]->endtime)) $etime  = $rsched->endtime; 
										if(isset($sched_new[1]->tardy_start)) $tstart = $rsched->tardy_start; 
										if(isset($sched_new[1]->absent_start)) $absent_start = $rsched->absent_start;
										if(isset($sched_new[1]->early_dismissal)) $earlyd = $rsched->early_dismissal;
										$seq_new += 1;
									 	list($login_new,$logout_new,$q_new,$haslog_forremarks_new)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq_new,$absent_start,$earlyd);
									 	// echo "<pre>"; print_r($logout_new);
									 	// if($login_new || $logout_new){
									 	// 	$lateutlec = $absent;
									 	// 	$lateutlab = $absent;
									 	// }
									 }
									 // $absent = "";
								}
								$absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd, $isFirstSched,$absent_start);
								if($el || $vl || $sl  || $ol || (!$ob && $ol !="CORRECTION" && $lnopay && $q != 'Facial' && $q != 'webcheckin' && $q != 'Fingerprint') || $service_credit || ($holiday && $isCreditedHoliday)) $lateutlec = $absent = $utlec = "";
							}else{
								
								// if ($oltype != "") {
									if(!$login && $absent) $login = $this->attcompute->getLogin($empid, $edata, $rdate->dte);
									if(!$logout && $absent) $logout = $this->attcompute->getLogout($empid, $edata, $rdate->dte);
								// }
					        	// echo "<pre>"; print_r($oltype); 

								if($el == FALSE && $vl == FALSE && $sl == FALSE  && $ob == FALSE && $service_credit == FALSE && $ol == FALSE){
									// if($login){
									// 	$utlec = $absent;
									// 	$utlab = $absent;
									// 	$absent = "";
									// }
									// echo "<pre>"; print_r($login);
									if($login && $logout && !$absent){
										$lateutlec = $this->attcompute->displayLateUTNT($stime, $etime, $login, $logout, "", "", $tstart);

										$utlec = $this->attcompute->computeUndertimeNT($stime,$etime,$login,$logout,"","",$earlyd);

										if($absent) $utlec = $absent;
										if($utlec || $lateutlec) $log_remarks = $absent = "";
									}

								}
								$absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd, $isFirstSched,$absent_start);
								if($el || $vl || $sl  || $ol || (!$ob && $ol !="CORRECTION" && $lnopay && $q != 'Facial' && $q != 'webcheckin' && $q != 'Fingerprint') || $service_credit || ($holiday && $isCreditedHoliday)) $lateutlec = $absent = $utlec = "";
							}
						}

						
						
						// if($ol) echo "<pre>"; print_r('2.'.$ol); 
						if(!$holiday) $holiday = $this->attcompute->isHolidayNew($empid, $rdate->dte,$deptid,"","on",$teachingtype );  
					    if($holiday)
						{
							$sched_count = "";
							if($isFirstSched) $sched_count = "first";
							else $sched_count = "second";
							$newholidayInfo = $this->attcompute->holidayInfo($rdate->dte, $sched_count, $teachingtype, $deptid);
							if(isset($newholidayInfo["halfday"])) $holidayInfo = $newholidayInfo;
							if($holidayInfo){
								// if($holidayInfo["code"]=="SUS") $isSuspension = true;
								// if($holidayInfo["holiday_rate"] <= 0) $holiday = ''; 
							}

						}
			            $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
				        if($holiday && isset($holidayInfo['description'])){
							$log_remarks = '';
							if(isset($holidayInfo['halfday'])){
								if($holidayInfo['sched_count'] == "second" && !$isFirstSched){
									$lateutlec = '';
									$utlec = '';
									$absent = '';
									$tschedlec = $tschedlab = $tschedadmin = "";
									$hasHalfdayHoliday = true;
								}else if($holidayInfo['sched_count'] != "second" && $isFirstSched){
									$lateutlec = '';
									$utlec = '';
									$absent = '';
									$tschedlec = $tschedlab = $tschedadmin = "";
									$hasHalfdayHoliday = true;
								}else{
									// $lateutlec = $utlec = $absent =  '';
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
						$absent = $this->attcompute->exp_time($absent);
							// echo "<pre>"; print_r($logout);
						
                        // if($absent >= 10800 && !$logout) $absent = 14400;
                        // echo "<pre>"; print_r($absent);
                        $absent   = ($absent ? $this->attcompute->sec_to_hm($absent) : "");
                        if($lateutlec && !$utlec){
			            	if(in_array("late", $ob_data)){
			            		$log_remarks = "EXCUSED LATE";
			            		$lateutlec = "";
			            	}
			            	else{
			            		$log_remarks = "UNEXCUSED LATE";
			            		$ob_type = false;
			            		$ob_data = array();
			            	}
			            }else if($utlec){
							if(in_array("undertime", $ob_data)){
								$utlec = "";
								$log_remarks = "EXCUSED UNDERTIME";
							}
				            else{
				            	$log_remarks = "UNEXCUSED UNDERTIME";
				            	$ob_type = false;
			            		$ob_data = array();
				            }
				        }else if($absent){
							if(in_array("absent", $ob_data)) $log_remarks = "EXCUSED ABSENT";
				            else{
				            	if(strtotime($rdate->dte) < strtotime($date_tmp)){
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
							<td class="align_center" key="ss" etype="nt" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime != "00:00:00" ? date('h:i A',strtotime($stime)) : "--")?></td>
							<td class="align_center" key="es" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime != "00:00:00" ? date('h:i A',strtotime($etime)) : "--")?></td>
							<!-- <td class="align_center" key="ti">--</td>
		                    <td class="align_center" key="to">--</td> -->
							<td class="align_center" key="ti" 
		                    	style='<?=$absent ? 'background-color: #ffe6e6;' : (($lateutlec && date('H:i',strtotime($stime)) < date('H:i',strtotime($login)))?"color:red":"")?>'>
		                    	<?=($login ? date("h:i A",strtotime($login)) : "--")?>
		                    </td>

							<td class="align_center" key="to" 
								style='<?=$absent ? 'background-color: #ffe6e6;' : (($utlec && date('H:i',strtotime($etime)) > date('H:i',strtotime($logout)))?"color:red":"")?>'>

								<?=($logout  ? date("h:i A",strtotime($logout)) : "--")?></td>
							
							<?if($dispLogDate){?>
								<td class="align_center" key="otr"   rowspan="<?=$countrow?>"><?=($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):""?></td>
								<td class="align_center" key="otrest" rowspan="<?=$countrow?>"><?=($otrest)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otrest)):""?></td>
								<td class="align_center" key="othol" rowspan="<?=$countrow?>"><?=($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):""?></td>
							<?}?>
							<td class="align_center" key="ut"><?=$lateutlec?></td>
							<td class="align_center" key="ut"><?=$utlec?></td>
							<td class="align_center" key="ab" style="<?=($absent)?"background-color: #ffe6e6;":""?>">
								<?=(!$fixedday && !$hasOL) ? $absent : ($absent?$absent:'')?>
								
							</td>
							<?
								$rwcount = 1;
								if(!$dispLogDate) $rwcount = 1;
								if($haswholedayleave || $pending || $holiday) $rwcount = $countrow;
								if((!$haswholedayleave && !$pending && !$holiday)){
							?>
									<td class="align_center" rowspan="<?=$rwcount?>" key="vl"><?=$hasleavecount <= 1 ? (($vl + $el) ? ($vl + $el) : '') : ''?></td>
									<td class="align_center" rowspan="<?=$rwcount?>" key="sl"><?=$hasleavecount <= 1 ? $sl : ''?></td>
									<td class="align_center" rowspan="<?=$rwcount?>" key="el"><?=$hasleavecount <= 1 ? ((!in_array($ol, $not_included_ol) && $ol && $ol!="DIRECT" && !$lnopay) ? (($rwcount) ? 1 : 0.5) : "") : ''?></td>
									<!-- <td class="align_center" rowspan="<?=$rwcount?>" key="sc"><?=$hasleavecount <= 1 ? $service_credit : ""?></td> -->
									<!-- <td class="align_center" rowspan="<?=$rwcount?>" key="ol">
											<span style="<?=($pending)?'background-color:#ffffff;':""?> <?=(!$ob_data)?'color:red;':""?> "><?=($log_remarks?$log_remarks."<br>":"")?></span>
						                	<span style="<?=($pending)?'background-color:#ffffff;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=$dispLogDate ? ($cs_app?$cs_app."<br>":"") : ""?></span>
						                	<span style="<?=($pending)?'background-color:#ffffff;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=($pending)?"PENDING ".$pending."<br>":""?></span>
											<span style="<?=($pending)?'background-color:#ffffff;':""?> <?=($ob)?'color:black;':""?> "><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" 
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
												
									</td> -->

											<!--//($ol ? ($oltype=="DIRECT" ? $this->employeemod->othLeaveDesc($ol) : $oltype) : "")?>-->

								<?}else{?>
				            		<?php if($vl) { ?>
					                	<td class="align_center" key="vl"><?=$hasleavecount <= 1 ? 0.5 : ""?></td>
					                <?php } else if($el){?>
					                	<td class="align_center" key="el"><?=$hasleavecount <= 1 ? 0.5 : ""?></td>
					                <?php }else{ ?>
					                	<td class="align_center" key="vl"><?=$hasleavecount <= 1 ? "" : ""?></td>
					               	<?php } ?>
					                <td class="align_center" key="sl"><?=$hasleavecount <= 1 ? $sl : ""?></td>
					                <td class="align_center" key="el"><?=$hasleavecount <= 1 ? ((!in_array($ol, $not_included_ol) && $ol && $ol!="DIRECT" && !$lnopay) ? (($ol >= 1) ? 1 : 0.5) : "") : ''?></td>
				            	<?php } ?>
								<?php  
									$rwcount = 1;
									if(!$dispLogDate ||  (!$haswholedayleave && !$pending && !$holiday)) $rwcount = 1;
								?>
								<td class="align_center" rowspan="<?=$rwcount?>" key="ol">
										<span style="text-align:center;<?=($pending || $pending_ob)?'background-color:#ffffff;':""?> <?= ($lnopay) ? 'display: none;' : ''; ?> <?=(!$ob_data)?'color:red;':""?> "><?=($log_remarks?$log_remarks.$pending_ob."<br>":"")?></span>
					                	<span style="text-align:center;<?=($pending)?'background-color:#ffffff;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=$dispLogDate ? ($cs_app?$cs_app."<br>":"") : ""?></span>
					                	<span style="text-align:center;<?=($pending)?'background-color:#ffffff;color:red;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=(($pending && !$ob) || $pending == "OVERTIME Application")?"Pending ".$pending."<br>":""?></span>
					                	<!-- <span style="text-align:center;"><?= ($lnopay) ? $this->attendance->getLeaveNoPay($rdate->dte, $empid)."<br>" : '' ?></span> -->
										<span style="text-align:center;<?=($pending && !$ob)?'background-color:#ffffff;':""?> <?=($ob)?'color:black;':""?> "><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" 
												: ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)."<br>") 
												: '')?> </span>
										<span style="text-align:center;background-color:#ffffff;color: black"><?= (($q == "Fingerprint" || $q == "Facial" || $q == "webcheckin") && ($q != 1)) ? strtoupper($q):""?> </span>
											<?php
											if($holiday && isset($holidayInfo['description'])){
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
										<span style="text-align:center;"><?=($otreg || $otrest || $othol)?'OVERTIME Application<br>':""?></span>
								</td>
							<?if($dispLogDate){?>
								<td class="align_center" rowspan="<?=$countrow?>" <?=$hide_from_fixedday?> ><?=$workdays?$workdays:''?></td>
							<?}?>

							<?php if(isset($holidayInfo["halfday"])){ ?>
								<td class="align_center" rowspan="<?=$rwcount?>" style="vertical-align : middle;text-align:center;"><?=($holiday && isset($holidayInfo['type']) && $hasHalfdayHoliday ? $holidayInfo['type'] : '')?></td>
				            <?php }else if($dispLogDate && !isset($holidayInfo["halfday"])){ ?>
				            	<td class="align_center" rowspan="<?=$countrow?>" style="vertical-align : middle;text-align:center;"><?=($holiday && isset($holidayInfo['type']) ? $holidayInfo['type'] : '')?></td>
				            <?php } ?>

				            <?if($dispLogDate){?>
				                <!-- <td class="align_center" rowspan="<?=$countrow?>"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->
				            <?}?>
						</tr>
						<?
					}
					//FLEXIBLE
					else
					{
					   // echo ' FLEXIBLE';
						$totalQ = 0;
						if($tempsched == $dispLogDate){  $dispLogDate = "";}
						$stime  = $rsched->starttime;
						$etime  = $rsched->endtime; 
						$tstart = $rsched->tardy_start; 
						$earlyd = $rsched->early_dismissal;
						
						// logtime
						$getLog = $this->attcompute->getLogsPerDay($empid,$rdate->dte,$edata,true);
						$log = array();
						if(count($getLog) > 1) $log[] = $getLog[0];
						else 				   $log = $getLog; 
						
						 // Overtime
						list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,true);

						// Leave
						// list($el,$vl,$sl,$ol,$oltype)             = $this->attcompute->displayLeave($empid,$rdate->dte);
						list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$lnopay)             = $this->attcompute->displayLeave($empid,$rdate->dte);

						$pending = $this->attcompute->displayPendingApp($empid,$rdate->dte);
						$pending_ob = $this->attcompute->displayPendingOBApp($empid,$rdate->dte, ($seq==1) ? "late" : "undertime");
						if($ob) $pending_ob = "";

						//Service Credit 
			        	$service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);
						
		                $count_leave = $vl > 0 ? $vl : ( $el > 0 ? $el : ( $sl > 0 ? $sl : ( $ob > 0 ? $ob : ( $service_credit > 0 ? $service_credit : 0 ) ) ) ) ;

						// Absent
						$absent = $this->attcompute->displayAbsentFlexi($log,$rsched->hours,$rsched->mode,$empid,$rdate->dte,'',$rsched->breaktime, $count_leave);
						if($oltype == "ABSENT") $absent = $absent;
						else if($holiday && $isCreditedHoliday) $absent = "";

		                if ($vl > 0 || $el > 0 || $sl > 0 || $ob > 0 || $service_credit > 0){
		                    $absent = "";
		                }

						
						// Late / Undertime
						$lateutlec = '';
						$utlec = $this->attcompute->displayLateUTNTFlexi($log,$rsched->hours,$rsched->mode,$absent,$rsched->breaktime, $count_leave);

						if($el >= 1 || $vl >= 1 || $sl >= 1 || $ob >= 1 || $service_credit >= 1 || ($holiday && $isCreditedHoliday)) $utlec = "";
						if(date("Y-m-d",strtotime($utlec)) < $rdate->dte)
						{
							$utlec = $lateutlab = "";
						}

						// if($ob) $el += $ob;

						$log_remarks = '';

						$hasOL = $ol ? ($ol != 'CORRECTION' ? true : false) : false; 

						if(!$fixedday){
							if($absent=='' || $hasOL) $workdays=1;
						}

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

								$q = $log[$i][2];
								if($q) $totalQ++;
								?>
								<tr class="edata" <?=$color?>>
									<?if($dispLogDate){?>
						                <td class="align_center" rowspan="<?=$countrow?>" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?></td>
						            <?}?>
						            <td class="align_center" key="ss" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime ? date("h:i A",strtotime($stime)) : "--")?></td>
						            <td class="align_center" key="es" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime ? date("h:i A",strtotime($etime)) : "--")?></td>

									<td class="align_center" key="ti" 
						            	style="<?=($absent || $ol == "ABSENT") ? "background-color: #ffe6e6;" : (($lateutlec && date("H:i",strtotime($stime)) < date("H:i",strtotime($login)))?"color:red":"")?>">
						            	<?=(($login ) ? date("h:i A",strtotime($login)) : "--")?></td>
						            <td class="align_center" key="to" 
						            	style="<?=($absent || $ol == "ABSENT") ? "background-color: #ffe6e6;" : (($utlec && date("H:i",strtotime($logout)) < date("H:i",strtotime($etime)))?"color:red":"")?>">
						            	<?=(($logout ) ? date("h:i A",strtotime($logout)) : "--")?></td>
										
									<?if($dispLogDate){?>
										<td class="align_center" key="otr"   rowspan="<?=$countrow?>"><?=($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):""?></td>
										<td class="align_center" key="otrest" rowspan="<?=$countrow?>"><?=($otrest)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otrest)):""?></td>
										<td class="align_center" key="othol" rowspan="<?=$countrow?>"><?=($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):""?></td>
									<?}?>

									<td class="align_center" key="ut"><?=$lateutlec?></td>
									<td class="align_center" key="ut"><?=$utlec?></td>
									<td class="align_center" key="ab" style="<?=($absent)?"background-color: #ffe6e6;":""?>"><?=(!$fixedday && !$hasOL) ? '' : ($absent?$absent:'')?></td>
									<!-- <td class="align_center" key="ab" rowspan="<?=$countrow?>" style="<?=($absent)?"background-color: #ffe6e6;":""?>" ><?=$absent?></td> -->

									<?
										$rwcount = 1;
										if(!$dispLogDate) $rwcount = 1;
										if($haswholedayleave || $pending || $holiday) $rwcount = $countrow;
										if((!$haswholedayleave && !$pending && !$holiday)){
									?>
											<td class="align_center" rowspan="<?=$rwcount?>" key="vl"><?=$hasleavecount <= 1 ? (($vl + $el) ? ($vl + $el) : '') : ''?></td>
											<td class="align_center" rowspan="<?=$rwcount?>" key="sl"><?=$hasleavecount <= 1 ? $sl : ''?></td>
											<td class="align_center" rowspan="<?=$rwcount?>" key="el"><?=$hasleavecount <= 1 ? ((!in_array($ol, $not_included_ol) && $ol && $ol!="DIRECT" && !$lnopay) ? (($rwcount) ? 0.5 : 0.5) : "") : ''?></td>
											<!-- <td class="align_center" rowspan="<?=$rwcount?>" key="sc"><?=$hasleavecount <= 1 ? $service_credit : ""?></td> -->
											<!-- <td class="align_center" rowspan="<?=$rwcount?>" key="ol">
													<span style="<?=($pending)?'background-color:#ffffff;':""?> <?=(!$ob_data)?'color:red;':""?> "><?=($log_remarks?$log_remarks."<br>":"")?></span>
								                	<span style="<?=($pending)?'background-color:#ffffff;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=$dispLogDate ? ($cs_app?$cs_app."<br>":"") : ""?></span>
								                	<span style="<?=($pending)?'background-color:#ffffff;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=($pending)?"PENDING ".$pending."<br>":""?></span>
													<span style="<?=($pending)?'background-color:#ffffff;':""?> <?=($ob)?'color:black;':""?> "><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" 
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
														
											</td> -->

													<!--//($ol ? ($oltype=="DIRECT" ? $this->employeemod->othLeaveDesc($ol) : $oltype) : "")?>-->

										<?}else{?>
						            		<?php if($vl) { ?>
							                	<td class="align_center" key="vl"><?=$hasleavecount <= 1 ? 0.5 : ""?></td>
							                <?php } else if($el){?>
							                	<td class="align_center" key="el"><?=$hasleavecount <= 1 ? 0.5 : ""?></td>
							                <?php }else{ ?>
							                	<td class="align_center" key="vl"><?=$hasleavecount <= 1 ? "" : ""?></td>
							               	<?php } ?>
							                <td class="align_center" key="sl"><?=$hasleavecount <= 1 ? $sl : ""?></td>
							                <td class="align_center" key="el"><?=$hasleavecount <= 1 ? ((!in_array($ol, $not_included_ol) && $ol && $ol!="DIRECT" && !$lnopay) ? (($ol >= 1) ? 1 : 0.5) : "") : ''?></td>
						            	<?php } ?>
										<?php  
											$rwcount = 1;
											if(!$dispLogDate ||  (!$haswholedayleave && !$pending && !$holiday)) $rwcount = 1;
										?>
										<td class="align_center" rowspan="<?=$rwcount?>" key="ol">
												<span style="text-align:center;<?=($pending || $pending_ob)?'background-color:#ffffff;':""?> <?= ($lnopay) ? 'display: none;' : ''; ?> <?=(!$ob_data)?'color:red;':""?> "><?=($log_remarks?$log_remarks.$pending_ob."<br>":"")?></span>
							                	<span style="text-align:center;<?=($pending)?'background-color:#ffffff;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=$dispLogDate ? ($cs_app?$cs_app."<br>":"") : ""?></span>
							                	<span style="text-align:center;<?=($pending)?'background-color:#ffffff;color:red;':""?> <?=(!$ob_data && !$ob)?'color:red;':""?> "><?=(($pending && !$ob) || $pending == "OVERTIME Application")?"Pending ".$pending."<br>":""?></span>
							                	<!-- <span style="text-align:center;"><?= ($lnopay) ? $this->attendance->getLeaveNoPay($rdate->dte, $empid)."<br>" : '' ?></span> -->
												<span style="text-align:center;<?=($pending && !$ob)?'background-color:#ffffff;':""?> <?=($ob)?'color:black;':""?> "><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" 
														: ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)."<br>") 
														: '')?> </span>
												<span style="text-align:center;background-color:#ffffff;color: black"><?=($oltype ? ("Fingerprint" ? ($oltype == "Facial" ? "Facial<br>" 
												: $oltype."<br>") : $ol."<br>") 
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
												<span style="text-align:center;"><?=($otreg || $otrest || $othol)?'OVERTIME Application<br>':""?></span>
										</td>
									<?if($dispLogDate){?>
										<td class="align_center" rowspan="<?=$countrow?>" <?=$hide_from_fixedday?> ><?=$workdays?$workdays:''?></td>
									<?}?>

									<?php if(isset($holidayInfo["halfday"])){ ?>
										<td class="align_center" rowspan="<?=$rwcount?>" style="vertical-align : middle;text-align:center;"><?=(isset($holidayInfo['type']) && $hasHalfdayHoliday ? $holidayInfo['type'] : '')?></td>
						            <?php }else if($dispLogDate && !isset($holidayInfo["halfday"])){ ?>
						            	<td class="align_center" rowspan="<?=$countrow?>" style="vertical-align : middle;text-align:center;"><?=(isset($holidayInfo['type']) ? $holidayInfo['type'] : '')?></td>
						            <?php } ?>
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
			        				<td class="align_center" key="ss" etype="nt" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime != "00:00:00" ? date("h:i A",strtotime($stime)) : "--")?></td>
			        				<td class="align_center" key="es" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> ><?=($stime != "00:00:00" ? date("h:i A",strtotime($etime)) : "--")?></td>
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
								<td class="align_center" key="ut"><?=$lateutlec?></td>
								<td class="align_center" key="ut"><?=$utlec?></td>
								<td class="align_center" key="ab" style="<?=($absent)?"background-color: #ffe6e6;":""?>"><?=(!$fixedday && !$hasOL) ?  ($absent?$absent:'') : ''?></td>
								<!-- <td class="align_center" key="ab" rowspan="<?=$countrow?>" style="<?=($absent)?"background-color: #ffe6e6;":""?>" ><?=$absent?></td> -->
								<?if($dispLogDate){?>
			        				<td class="align_center" rowspan="<?=$countrow?>" key="vl"><?=$vl?></td>
			        				<td class="align_center" rowspan="<?=$countrow?>" key="sl"><?=$sl?></td>
			        				<td class="align_center" rowspan="<?=$countrow?>" key="el"><?=$el?></td>
			        				<!-- <td class="align_center" rowspan="<?=$countrow?>" key="sc"><?=$service_credit?></td> -->
			        				<td class="align_center" rowspan="<?=$countrow?>" key="ol">
										<span style="text-align:center;<?=($pending || $pending_ob)?'background-color:#ffffff;':""?> <?= ($lnopay) ? 'display: none;' : ''; ?> <?=(!$ob_data)?'color:#000000;':""?> "><?=($log_remarks?$log_remarks.$pending_ob."<br>":"")?></span>
					                	<span style="text-align:center;<?=($pending)?'background-color:#ffffff;':""?> <?=(!$ob_data && !$ob)?'color:#000000;':""?> "><?=$dispLogDate ? ($cs_app?$cs_app."<br>":"") : ""?></span>
					                	<span style="text-align:center;<?=($pending)?'background-color:#ffffff;color:#000000;':""?> <?=(!$ob_data && !$ob)?'color:#000000;':""?> "><?=(($pending && !$ob) || $pending == "Overtime Application")?"Pending ".$pending."<br>":""?></span>
					                	<!-- <span style="text-align:center;"><?= ($lnopay) ? $this->attendance->getLeaveNoPay($rdate->dte, $empid)."<br>" : '' ?></span> -->
										<span style="text-align:center;<?=($pending && !$ob)?'background-color:#ffffff;':""?> <?=($ob)?'color:black;':""?> "><?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" 
												: ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)."<br>") 
												: '')?> </span>
										<span style="text-align:center;background-color:#ffffff;color: black"><?=($oltype ? ("Fingerprint" ? ($oltype == "Facial" ? "Facial<br>" 
										: $oltype."<br>") : $ol."<br>") 
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
										<span style="text-align:center;"><?=($otreg || $otrest || $othol)?'Overtime Application<br>':""?></span>
									</td>
									<td class="align_center" rowspan="<?=$countrow?>" <?=$hide_from_fixedday?> ><?=$workdays?$workdays:''?></td>
			        				<td class="align_center" rowspan="<?=$countrow?>"><?=(isset($holidayInfo["type"]) ? $holidayInfo["type"] : "")?></td>
			        				<?}
			        			}?>
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
        				if(!$fixedday && !$hasOL) $tabsent += $this->attcompute->exp_time($absent);
                        else $tabsent += $this->attcompute->exp_time($absent);
		            }else{
		            	$hasLog = true;
		            }

		            $hasLog = $hasLog ? $hasLog : ($hasOL ? true : false); 
		            
		            // Late / UT
		            if($lateutlec){
		                $tlec += $this->attcompute->exp_time($lateutlec);
		            }

		            if($utlec){
		                $tutlec += $this->attcompute->exp_time($utlec);
		            }
					
		            // Leave
					if($dispLogDate || $hasleavecount || (!$haswholedayleave && !$pending && !$holiday))
					{
						$tel      += ($el) ? 0.5 : 0;
						$tvl      += ($vl) ? 0.5 : 0;
						$tsl      += ($sl) ? 0.5 : 0;
						$tol      += ($ol > 0) ? 0.5 : 0;
						$date_tmp  = $rdate->dte;
						//$tol 	  += ($ol ? 1 : "") + ($q ? ($q == 1 ? "" : 1) : "") ;
						// echo "<pre>". $rdate->dte ." - ". $ol . " - ". $q . "</pre>";
					}
					#$tol	  += $service_credit_used;
					$service_credit_used = 0;

					if($service_credit && ($dispLogDate || !$haswholedayleave)) $t_service_credit+=$service_credit;

					
					if(!$fixedday){
						if($absent=='' || $hasOL) $tworkdays+=0.5;
					}
					
					$hasHalfdayHoliday = false;
					$isFirstSched = false;	
					if(isset($holidayInfo["halfday"])) $isCreditedHoliday = false;
		        }  ///< end foreach sched
					   
		        
		        // total holiday
		        if($holiday && !isset($holidayInfo["halfday"])) $tholiday++;
		        else if($holiday && isset($holidayInfo["halfday"])) $tholiday += 0.5;
		        
		        /* Overtime */
				if($otreg){
		                    $totr += $this->attcompute->exp_time($otreg);
		                }

				if($otrest){
		                    $totrest += $this->attcompute->exp_time($otrest);
		                }

				if($othol){
		                    $tothol += $this->attcompute->exp_time($othol);
		                }

		        
		    }else{
		    	///< no sched or not valid sched

				$totalQ = 0;
				$stime = "";
				$etime = ""; 
				
				$log = $this->attcompute->displayLogTimeFlexi($empid,$rdate->dte,$edata);
				
						
				// Leave
				list($el,$vl,$sl,$ol,$oltype)     = $this->attcompute->displayLeave($empid,$rdate->dte);
				//Service Credit 
				$service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);

				// Leave Pending
				$pending = $this->attcompute->displayPendingApp($empid,$rdate->dte);

				// Overtime
				list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,false);

				if($otreg){
							$totr += $this->attcompute->exp_time($otreg);
						}

				if($otrest){
							$totrest += $this->attcompute->exp_time($otrest);
						}

				if($othol){
							$tothol += $this->attcompute->exp_time($othol);
						}
				
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
							<?if($stime && $etime){?>
							<td class="align_center" key="ld" kd="<?=$rdate->dte?>" ><?=$dispLogDate?> </td>
							<?}?>
							<td class="align_center" key="ss" etype="nt" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >--</td>
							<td class="align_center" key="es" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >--</td>
							<td class="align_center" key="ti"><?=$login?date("h:i A",strtotime($login)):"--"?></td>
							<td class="align_center" key="to"><?=$logout?date("h:i A",strtotime($logout)):"--"?></td>
							<?if($stime && $etime){?>
							<td class="align_center" key="otr"   ><?=$otreg?$otreg:"--"?></td>
							<td class="align_center" key="otrest" ><?=$otrest?$otrest:"--"?></td>
							<td class="align_center" key="othol" ><?=$othol?$othol:"--"?></td>
							<td class="align_center" key="ut">--</td>
							<td class="align_center" key="ut">--</td>
							<td class="align_center" key="ab">--</td>
							<td class="align_center" key="vl">--</td>
							<td class="align_center" key="sl">--</td>
							<td class="align_center" key="el">--</td>
							<!-- <td class="align_center" key="sc">--</td> -->
							<td class="align_center" key="ol" style="min-width: 150px;" valign="center">
								<?php
								if(!isset($lnopay)) $lnopay = "";
								?>
								<?=($cs_app?$cs_app.'<br>':"")?>
								<?=($pending)?"Pending ".$pending.'<br>':($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" : ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)."<br>") 
									: '')?> 
								<?=$service_credit?'SERVICE CREDIT<br>':''?>
								<?=(isset($holidayInfo["description"]) ? $holidayInfo["description"] : "")?>

								<!-- <?if(strpos($pending, "SERVICE CREDIT") === false && !$service_credit){?>
									<a class="btn btn-primary" id="applysc" href="#" data-toggle="modal" data-target="#myModal1" style="display: none;"> dateInitial="<?=$rdate->dte?>" >Apply as Service Credit
									<span class="notifdiv bell" style="position: relative;top:5px;"><i class="glyphicon glyphicon-bell large" style="color: #FF1744;font-size: 20px;"></i></span></a>
								<?}?> -->

							</td>
							<td class="align_center"<?=$hide_from_fixedday?> >--</td>
							<td class="align_center"><?=(isset($holidayInfo['type']) ? $holidayInfo['type'] : '')?></td>
							<?}?>
				            <?if($dispLogDate){?>
				                <!-- <td class="align_center"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->
				            <?}?>
						</tr>
						<?
					}
		       }
			   else
			   {
			   	///< no sched no log

			   	if (!isset($lnopay)) $lnopay = "";
			   	
				   ?>
				  <tr class="edata" style='background-color: gray;color:white'>
							<td class="align_center" key="ld" kd="<?=$rdate->dte?>"><?=$dispLogDate?></td>
							<td class="align_center" key="ss" etype="nt" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >--</td>
							<td class="align_center" key="es" <?= ($usertype == "EMPLOYEE" ? "hidden" : "")?> >--</td>
							<td class="align_center" key="ti">--</td>
							<td class="align_center" key="to">--</td>
							<td class="align_center" key="otr"   ><?=$otreg?$otreg:"--"?></td>
							<td class="align_center" key="otrest" ><?=$otrest?$otrest:"--"?></td>
							<td class="align_center" key="othol" ><?=$othol?$othol:"--"?></td>
							<td class="align_center" key="ut">--</td>
							<td class="align_center" key="ut">--</td>
							<td class="align_center" key="ab">--</td>
							<td class="align_center" key="vl">--</td>
							<td class="align_center" key="sl">--</td>
							<td class="align_center" key="el">--</td>
							<!-- <td class="align_center" key="sc">--</td> -->
							<td class="align_center" key="ol" >
								<span style="<?=($pending)?'background-color:#ffffff;color:red;':''?>"><?= ($pending) ? "Pending ".$pending : "" ?></span>
								<span style="text-align:center;"><?=($otreg || $otrest || $othol)?'OVERTIME Application<br>':""?></span>
								<?=($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : ($lnopay ? $oltype."<br>NO PAY<br>" : $oltype."<br>")) : $this->employeemod->othLeaveDesc($ol)) : "")?> 
								<?=$service_credit?'SERVICE CREDIT<br>':''?>
								<?=(isset($holidayInfo["description"]) ? $holidayInfo["description"] : "")?></td>

							<td class="align_center"<?=$hide_from_fixedday?> >--</td>
							<td class="align_center"><?=(isset($holidayInfo['type']) ? $holidayInfo['type'] : '')?></td>
				            <?if($dispLogDate){?>
				                <!-- <td class="align_center"><?=$this->attcompute->sec_to_hm($perday_absent)?></td> -->
				            <?}?>
						</tr>
					<?
			   }

			   if($service_credit && ($dispLogDate || !$haswholedayleave)) $t_service_credit+=$service_credit;
			   // total holiday
		        if($holiday && !isset($holidayInfo["halfday"])) $tholiday++;
		        else if($holiday && isset($holidayInfo["halfday"])) $tholiday += 0.5;
		    }///< end else no sched

		    $holiday = '';
  			$firstDate = true;
  }
  $tabsent = ($tabsent ? $this->attcompute->sec_to_hm($tabsent) : "");
  ?>
  <tr class="edata">
        <th class="align_right" <?= ($usertype == "EMPLOYEE") ? 'colspan="3"' : 'colspan="5"' ?> ><b>TOTAL</b></th>
            <!--<th class="align_center"><?=$tOverload = ($tOverload ? $this->attcompute->sec_to_hm($tOverload) : "")?></th>-->
            <th class="align_center"><?=$totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "")?></th>
            <th class="align_center"><?=$totrest   = ($totrest ? $this->attcompute->sec_to_hm($totrest) : "")?></th>
            <th class="align_center"><?=$tothol   = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "")?></th>
            <th class="align_center"><?=$tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "")?></th>
            <th class="align_center"><?=$tutlec   = ($tutlec ? $this->attcompute->sec_to_hm($tutlec) : "")?></th>
            <th class="align_center"><?=$tabsent?></th>
            <th class="align_center"><?=$tvl?></th>
            <th class="align_center"><?=$tsl?></th>
            <!-- <th class="align_center"><?=$tel?></th> -->
            <th class="align_center"><?=$tol?></th>
            <!-- <th class="align_center"><?=$t_service_credit?></th> -->
            <th class="align_center"></th>
            <th class="align_center"<?=$hide_from_fixedday?> ><?=$tworkdays?></th>
            <th class="align_center"><?=$tholiday?></th>
            <!-- <th class="align_center"><?=$this->attcompute->sec_to_hm($total_perday_absent)?></th> -->
      </tr>
    </tbody>
</table>
</div>