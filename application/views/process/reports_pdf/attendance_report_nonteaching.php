<?php 

$qdate = $this->attcompute->displayDateRange($from_date, $to_date);


$content .= '
		<div class="well-content" style="border: transparent !important;">
		<table class="table table-bordered datatable" id="indvtbl">
		    <thead>
		        <tr>
					<th rowspan="2" class="align_center" width="120px">Date</th>
					<!-- <th class="align_center" colspan="2" style="width:140px;">Official Time</th> -->
					<th class="align_center" colspan="2" style="width:140px;">Actual Log Time</th>
					<th class="align_center" colspan="3">Overtime (hr:min)</th>
					<th class="align_center">Late/Undertime</th>
					<th class="align_center" rowspan="2" width="80px">Absent</th>                        
					<th class="align_center" colspan="3">Leaves</th>
					<th class="align_center" rowspan="2" width="60px">Service Credit</th>
					<!-- <th class="align_center" rowspan="2" >Remarks/Others</th> -->
					<th class="align_center" rowspan="2" >Holiday</th>
		        </tr>
		        <tr>
		            <!-- <th class="align_center">IN</th><th class="align_center">OUT</th> -->
	                <th class="align_center">IN</th><th class="align_center">OUT</th>
	                <th class="align_center" width="50px;">Regular</th>
	                <th class="align_center" width="50px;">Rest Day</th>
	                <th class="align_center" width="50px;">Holiday</th>
	                <th class="align_center" width="80px">Hr:min</th>            
	                <th class="align_center" width="50px">Vacation</th>
	                <th class="align_center" width="50px">Sick</th>
	                <th class="align_center" width="50px">Other</th>
		        </tr>
		    </thead>
		    <tbody id="employeelist">
';

    $x = $totr = $totrest = $tothol = $tlec = $absent = $tabsent = $tabsentperday = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = $pending = $tempOverload = $overload = $tOverload = $lastDayOfWeek = $cs_app = ""; 
    $tlec = 0 ;
	$tempabsent = "";
	$t_service_credit = $service_credit = "";

	$hasLog = $isSuspension = false;

    $isCreditedHoliday = false;
	$firstDate = true;
	foreach ($qdate as $rdate) {
				
			// Holiday
			$isSuspension = false;
		    $holiday = $this->attcompute->isHolidayNew($empid,$rdate->dte,$deptid ); 

			$holidayInfo = $this->attcompute->holidayInfo($rdate->dte);
		    if($holiday)
			{
				if($holidayInfo['holiday_type']==5) $isSuspension = true;
			}
			
		    $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
		    $sched = $this->attcompute->displaySched($empid,$rdate->dte);

		    $countrow = $sched->num_rows();

		    $isValidSchedule = true;

		    if($countrow > 0){
		    	if($sched->row(0)->starttime == '00:00:00' && $sched->row(0)->endtime == '00:00:00') $isValidSchedule = false;
		    }
		    
		    if($x%2 == 0)   $color = " style='background-color: white;'";
		    else            $color = " style='background-color: #fafafa;'";
		    $x++;
			
		    ///< for validation of absent for 1st day in range. this will check for previous day attendance
	    	if($firstDate && $holiday){
	    		// $tabsentperday = $this->attendance->checkPreviousSchedAttendanceNonTeaching($rdate->dte,$empid);
	    		$hasLog = $this->attendance->checkPreviousSchedAttendanceNonTeaching($rdate->dte,$empid);
	    		$firstDate = false;
	    	}

		    if($countrow > 0 && $isValidSchedule){
		    	$haswholedayleave = false;
		    	$hasleavecount = 0;

		    	///< for validation of holiday (will only be credited if not absent during last schedule)
		    	// $prevabsentperday = $tabsentperday;
		    	// $tabsentperday = 0;
				$hasLogprev = $hasLog;
		    	$hasLog = false;

		    	// if($prevabsentperday > 0) $isCreditedHoliday = false;
		    	// else 					  $isCreditedHoliday = true;

		    	if($hasLogprev || $isSuspension) 	$isCreditedHoliday = true;
		    	else 								$isCreditedHoliday = false;


		        $tempsched = "";
		        $seq = 0;
				$service_credit = null;
				$service_credit_used = 0;

				
		        foreach($sched->result() as $rsched){

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
						
						$seq += 1;
						
						// logtime
						list($login,$logout,$q)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq,$absent_start,$earlyd);
						
						 // Overtime
						list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,true);
						
						// Leave
						list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count)  = $this->attcompute->displayLeave($empid,$rdate->dte,'',$stime,$etime);

						//Service Credit 
						$service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);

						// Change Schedule
						$cs_app = $this->attcompute->displayChangeSchedApp($empid,$rdate->dte);
						
						
						// Leave Pending
						$pending = $this->attcompute->displayPendingApp($empid,$rdate->dte);

						 // Absent
						$absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd);
						if($oltype == "ABSENT") $absent = $absent;
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
						$lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent,$teachingtype,$tstart);
						if($el || $vl || $sl || $ob || $service_credit || ($holiday && $isCreditedHoliday)) $lateutlec = "";


											
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
						
						///< hide login and logout if leave for that sched
						if($ol && $ol != 'CORRECTION' ) $login = $logout = '';


$content .= '
			        <tr class="edata" '.$color.'>
			            ';

			            if($dispLogDate){
$content .= '
			                <td class="align_center" rowspan="'.$countrow.'" key="ld" kd="'.$rdate->dte.'" >'.$dispLogDate.'</td>';
			            }
$content .= '
			            <!-- <td class="align_center" key="ss" etype="nt">'.($stime ? date("h:i A",strtotime($stime)) : "--").'</td> -->
			            <!-- <td class="align_center" key="es">'.($stime ? date("h:i A",strtotime($etime)) : "--").'</td> -->
			            <td class="align_center" key="ti" 
			            	style="'.($absent ? "background-color: #ffe6e6;" : (($lateutlec && date('H:i',strtotime($etime)) > date('H:i',strtotime($logout)))?"color:red":"")).'">
			            	'.(($login && ( (date("H:i:s",strtotime($logout)) < $earlyd && date("H:i:s",strtotime($logout)) >= $stime) ? $absent : !$absent)) ? date("h:i A",strtotime($login)) : "--").'</td>
			            <td class="align_center" key="to" 
			            	style="'.($absent ? "background-color: #ffe6e6;" : ((($lateutlec || $lateutlab || $lateutadmin) && date("H:i",strtotime($logout)) < date("H:i",strtotime($etime)))?"color:red":"")).'">
			            	'.(($logout && ( (date("H:i:s",strtotime($logout)) < $earlyd && date("H:i:s",strtotime($logout)) >= $stime) ? $absent : !$absent)) ? date("h:i A",strtotime($logout)) : "--").'</td>';

			            if($dispLogDate){
$content .= '
							<td class="align_center" key="otr"   rowspan="'.$countrow.'">'.(($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):"").'</td>
							<td class="align_center" key="otrest" rowspan="'.$countrow.'">'.(($otrest)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otrest)):"").'</td>
							<td class="align_center" key="othol" rowspan="'.$countrow.'">'.(($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):"").'</td>';
						}

$content .= '
						<td class="align_center" key="utlec">'.($lateutlec?$lateutlec:"").'</td>
			            <td class="align_center" key="ab" style="'.($absent?"background-color: #ffe6e6;":"").'">'.($absent?$absent:"").'</td>';

							$rwcount = 1;
							if(!$dispLogDate) $rwcount = 1;
							if($haswholedayleave || $pending || $holiday) $rwcount = $countrow;

							if($dispLogDate || (!$haswholedayleave && !$pending && !$holiday)){
						
$content .= '
				                <td class="align_center" rowspan="'.$rwcount.'" key="vl">'.($hasleavecount <= 1 ? $vl : "").'</td>
				                <td class="align_center" rowspan="'.$rwcount.'" key="sl">'.($hasleavecount <= 1 ? $sl : "").'</td>
				                <td class="align_center" rowspan="'.$rwcount.'" key="el">'.($hasleavecount <= 1 ? $el : "").'</td>
				                <td class="align_center" rowspan="'.$rwcount.'" key="sc">'.($hasleavecount <= 1 ? $service_credit : "").'</td>
				                <!-- <td class="align_center" rowspan="'.$rwcount.'" key="ol"  style="'.(($pending)?'background-color:#b3d9ff;':"").'">

				                	'.($dispLogDate ? ($cs_app?$cs_app."<br>":"") : "").'
				                	'.(($pending)?"PENDING ".$pending."<br>":"").'
									'.($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" 
											: $oltype."<br>") : $this->employeemod->othLeaveDesc($ol)."<br>") 
											: '').' 
									'.($service_credit?'SERVICE CREDIT<br>':'').'

									'.($dispLogDate ?  (isset($holidayInfo["description"]) ? $holidayInfo["description"] : "") : "").'
				                	
			                	</td> -->';
			            	}

			            if($dispLogDate){
$content .= '
			                <td class="align_center" rowspan="'.$countrow.'">'.(isset($holidayInfo["type"]) ? $holidayInfo["type"] : "").'</td>';
			            }

$content .= '
			        </tr>
';


					}
			        else
			        {
			        ///< FLEXIBLE ---------------------------------------------------------------------------------------------------------------------------------
			        	
			        	// echo '<pre> FLEXIBLE';
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

						//Service Credit 
			        	$service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);
						

						// Absent
						$absent = $this->attcompute->displayAbsentFlexi($log,$rsched->hours,$rsched->mode,$empid,$rdate->dte);
						
						if($oltype == "ABSENT") $absent = $absent;
						else if($holiday && $isCreditedHoliday) $absent = "";

		                if ($vl > 0 || $el > 0 || $sl > 0 || $ob > 0 || $service_credit > 0){
		                    $absent = "";
		                }

		                $count_leave = $vl > 0 ? $vl : ( $el > 0 ? $el : ( $sl > 0 ? $sl : ( $ob > 0 ? $ob : ( $service_credit > 0 ? $service_credit : 0 ) ) ) ) ;

						
						// Late / Undertime
						$lateutlec = $this->attcompute->displayLateUTNTFlexi($log,$rsched->hours,$rsched->mode,$absent,$rsched->breaktime, $count_leave);

						if($el >= 1 || $vl >= 1 || $sl >= 1 || $ob >= 1 || $service_credit >= 1 || ($holiday && $isCreditedHoliday)) $lateutlec = "";
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
						
						$login = $logout = $q = "";
						if(count($log) > 0)
						{
							for($i = 0;$i < count($log);$i++)
							{
								$login = $log[$i][0];
								$logout = $log[$i][1];
								$q = $log[$i][2];
								if($q) $totalQ++;				        	

$content .= '				        			
				        			<tr class="edata" '.$color.'>';
							            if($dispLogDate){
							            	
$content .= '
							                <td class="align_center" rowspan="'.$countrow.'" key="ld" kd="'.$rdate->dte.'" >'.$dispLogDate.'</td>';
							            }

$content .= '
							            <!-- <td class="align_center" key="ss">'.($stime ? date("h:i A",strtotime($stime)) : "--").'</td> -->
							            <!-- <td class="align_center" key="es">'.($stime ? date("h:i A",strtotime($etime)) : "--").'</td> -->
							            <td class="align_center" key="ti" 
							            	style="'.(($absent || $ol == "ABSENT") ? "background-color: #ffe6e6;" : ((($lateutlec) && date("H:i",strtotime($stime)) < date("H:i",strtotime($login)))?"color:red":"")).'">
							            	'.(($login ) ? date("h:i A",strtotime($login)) : "--").'</td>
							            <td class="align_center" key="to" 
							            	style="'.(($absent || $ol == "ABSENT") ? "background-color: #ffe6e6;" : ((($lateutlec) && date("H:i",strtotime($logout)) < date("H:i",strtotime($etime)))?"color:red":"")).'">
							            	'.(($logout ) ? date("h:i A",strtotime($logout)) : "--").'</td>';


							            if($dispLogDate){
$content .= '
											<td class="align_center" key="otr"   rowspan="'.$countrow.'">'.(($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):"").'</td>
											<td class="align_center" key="otrest" rowspan="'.$countrow.'">'.(($otrest)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otrest)):"").'</td>
											<td class="align_center" key="othol" rowspan="'.$countrow.'">'.(($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):"").'</td>';
										}
$content .= '
										<td class="align_center" key="utlec">'.($lateutlec?$lateutlec:"").'</td>
							            <td class="align_center" key="ab" style="'.($absent?"background-color: #ffe6e6;":"").'">'.($absent?$absent:"").'</td>';


							            if($dispLogDate){
$content .= '
							                <td class="align_center" rowspan="'.$countrow.'" key="vl">'.$vl.'</td>
							                <td class="align_center" rowspan="'.$countrow.'" key="sl">'.$sl.'</td>
							                <td class="align_center" rowspan="'.$countrow.'" key="el">'.$el.'</td>
							                <td class="align_center" rowspan="'.$countrow.'" key="sc">'.$service_credit.'</td>

							                <!-- <td class="align_center" rowspan="'.$countrow.'" key="ol" style="'.(($pending)?'background-color:#b3d9ff;':"").'">

							                	'.($cs_app?$cs_app."<br>":"").'
												'.(($pending)?"PENDING ".$pending."<br>":($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" : $oltype."<br>") : $this->employeemod->othLeaveDesc($ol)."<br>") : '')).' 
												'.($service_credit?'SERVICE CREDIT<br>':'').'
												'.(isset($holidayInfo["description"]) ? $holidayInfo["description"] : "").'
							                	
						                	</td> -->';
							            }

			            if($dispLogDate){
$content .= '
			                <td class="align_center" rowspan="'.$countrow.'">'.(isset($holidayInfo["type"]) ? $holidayInfo["type"] : "").'</td>';
			            }

$content .= '				        </tr>';
				        			
				        			$stime = $etime = "";
				        		}
				        	}
				        	else
				        	{
$content .= '				        		
				        		<tr class="edata" '.$color.'>';
				        			if($stime && $etime){
				        				if($dispLogDate){
$content .= '	        					<td class="align_center" rowspan="'.$countrow.'" key="ld" kd="'.$rdate->dte.'" >'.$dispLogDate.'</td>';
				        				}
$content .= '				        			
				        				<!-- <td class="align_center" rowspan="'.$countrow.'" key="ss" etype="nt">'.($stime != "00:00:00" ? date("h:i A",strtotime($stime)) : "--").'</td> -->
				        				<!-- <td class="align_center" rowspan="'.$countrow.'" key="es">'.($stime != "00:00:00" ? date("h:i A",strtotime($etime)) : "--").'</td> -->';
				        			}
$content .= '
				        			<td class="align_center" key="ti">'.($login?date("h:i A",strtotime($login)):"--").'</td>
				        			<td class="align_center" key="to">'.($logout?date("h:i A",strtotime($logout)):"--").'</td>';
				        					
				        			if($stime && $etime){
				        				if($dispLogDate){
$content .= '	        				<td class="align_center" key="otr"   rowspan="'.$countrow.'">'.(($otreg)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otreg)):"").'</td>
				        				<td class="align_center" key="otrest" rowspan="'.$countrow.'">'.(($otrest)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($otrest)):"").'</td>
				        				<td class="align_center" key="othol" rowspan="'.$countrow.'">'.(($othol)?$this->attcompute->sec_to_hm($this->attcompute->exp_time($othol)):"").'</td>';
				        				}
$content .= '	        			<td class="align_center" key="utlec">'.($lateutlec?$lateutlec:"").'</td>
						            <td class="align_center" key="utlab">'.($lateutlab?$lateutlab:"").'</td>
						            <td class="align_center" key="utlab">'.($lateutadmin?$lateutadmin:"").'</td>';
				        			if($dispLogDate){
$content .= '
				        				<td class="align_center" rowspan="'.$countrow.'" key="vl">'.$vl.'</td>
				        				<td class="align_center" rowspan="'.$countrow.'" key="sl">'.$sl.'</td>
				        				<td class="align_center" rowspan="'.$countrow.'" key="el">'.$el.'</td>
				        				<td class="align_center" rowspan="'.$countrow.'" key="sc">'.$service_credit.'</td>
				        				<!-- <td class="align_center" rowspan="'.$countrow.'" key="ol" style="'.(($pending)?"background-color:#b3d9ff;":"").'">
    					                	'.($cs_app?$cs_app."<br>":"").'
    										'.(($pending)?"PENDING ".$pending."<br>":($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" : $oltype."<br>") : $this->employeemod->othLeaveDesc($ol)."<br>") : '')).' 
    										'.($service_credit?'SERVICE CREDIT<br>':'').'
    										'.(isset($holidayInfo["description"]) ? $holidayInfo["description"] : "").'

				        				</td> -->';
				        				}
				        			}
			            if($dispLogDate){
$content .= '
			                <td class="align_center" rowspan="'.$countrow.'">'.(isset($holidayInfo["type"]) ? $holidayInfo["type"] : "").'</td>';
			            }				        			

$content .= '		        		</tr>';
				        		
				        	}
		        }///< end if FLEXIBLE/NOT

	            $tempsched = $dispLogDate;
		            /*
		             * Total
		             */ 
		            
		            // Absent
					if($absent){
						// $tabsentperday += $this->attcompute->exp_time($absent);
		                $tabsent += $this->attcompute->exp_time($absent);
		            }else{
		            	$hasLog = true;
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

					if($service_credit && ($dispLogDate || !$haswholedayleave)) $t_service_credit+=$service_credit;

					
					
		        }  ///< end foreach sched
					   
		        
		        // total holiday
		        if($holiday) $tholiday++;
		        
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

$content .= '	
								<tr class="edata" style="background-color: gray;color:white">';
					if($stime && $etime){
						if($dispLogDate){
$content .= '
									<td class="align_center" rowspan="'.$countrow.'" key="ld" kd="'.$rdate->dte.'" >'.$dispLogDate.'</td>';
						}
$content .= '
									<!-- <td class="align_center" rowspan="'.$countrow.'" key="ss">'.$stime.'</td> -->
									<!-- <td class="align_center" rowspan="'.$countrow.'" key="es">'.$etime.'</td> -->';
					}


$content .= '
									<td class="align_center" key="ti" style="">'.($login?date("h:i A",strtotime($login)):"--").'</td>
									<td class="align_center" key="to">'.($logout?date("h:i A",strtotime($logout)):"--").'</td>';					

					if($stime && $etime){
$content .= '
									<td class="align_center" key="otr"   >'.($otreg?$otreg:"--").'</td>
									<td class="align_center" key="otrest" >'.($otrest?$otrest:"--").'</td>
									<td class="align_center" key="othol" >'.($othol?$othol:"--").'</td>

									<td class="align_center" key="ut">--</td>
									<td class="align_center" key="ab">--</td>';

									if($dispLogDate){
$content .= '										
									<td class="align_center" rowspan="'.$countrow.'" key="vl">'.($vl?$vl:"--").'</td>
									<td class="align_center" rowspan="'.$countrow.'" key="sl">'.($sl?$sl:"--").'</td>
									<td class="align_center" rowspan="'.$countrow.'" key="el">'.($el?$el:"--").'</td>
									<td class="align_center" rowspan="'.$countrow.'" key="sc">'.($service_credit?$service_credit:"--").'</td>
									<!-- <td class="align_center" rowspan="'.$countrow.'" key="ol"  style="'.(($pending)?'background-color:#b3d9ff;':"").'">'.

						
					                	($cs_app?$cs_app.'<br>':"").
										(($pending)?"PENDING ".$pending.'<br>':($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE<br>" : $oltype."<br>") : $this->employeemod->othLeaveDesc($ol)."<br>") 
											: '')).
										($service_credit?'SERVICE CREDIT<br>':'').
										(isset($holidayInfo["description"]) ? $holidayInfo["description"] : "");

										if(strpos($pending, "SERVICE CREDIT") === false && !$service_credit){
$content .= '
											<a class="btn btn-primary" id="applysc" href="#" data-toggle="modal" data-target="#myModal1" style="width: 80px; line-height: 12px; display:none;" dateInitial="'.$rdate->dte.'" >Apply as Service Credit</a>
											<span class="notifdiv bell" style="position: relative;top:5px;"><i class="glyphicon glyphicon-bell large" style="color: #FF1744;font-size: 20px;"></i></span>';
										}

$content .= '						</td> -->';
									

									}
					}
			            if($dispLogDate){
$content .= '
			                <td class="align_center" rowspan="'.$countrow.'">'.(isset($holidayInfo["type"]) ? $holidayInfo["type"] : "").'</td>';
			            }

$content .= '					</tr>';
					
											$stime = $etime = "";
								}
							}
							else
							{
								///< no sched no log

$content .= '					
								<tr class="edata" style="background-color: gray;color:white">
									<td class="align_center" key="ld" kd="'.$rdate->dte.'" >'.$dispLogDate.' </td>
									<!-- <td class="align_center" key="ss">--</td> -->
									<!-- <td class="align_center" key="es">--</td> -->
									<td class="align_center" key="ti" style="">--</td>
									<td class="align_center" key="to" style="">--</td>
									<td class="align_center" key="otr"   >'.($otreg?$otreg:"--").'</td>
									<td class="align_center" key="otrest" >'.($otrest?$otrest:"--").'</td>
									<td class="align_center" key="othol" >'.($othol?$othol:"--").'</td>
									<td class="align_center" key="ut">--</td>
									<td class="align_center" key="ab">--</td>
									<td class="align_center" key="vl">'.($vl?$vl:"--").'</td>
									<td class="align_center" key="sl">'.($sl?$sl:"--").'</td>
									<td class="align_center" key="el">'.($el?$el:"--").'</td>
									<td class="align_center" key="sc">'.($service_credit?$service_credit:"--").'</td>
									<!-- <td class="align_center" key="ol" style="'.(($pending)?"background-color:#b3d9ff;":"").'">
										'.($ol ? ($oltype ? ($oltype == "ABSENT" ? "ABSENT W/ FILE" : $oltype) : $this->employeemod->othLeaveDesc($ol)) : "").' 
										'.($service_credit?'SERVICE CREDIT<br>':'').'
										'.(isset($holidayInfo["description"]) ? $holidayInfo["description"] : "").'</td> -->
									<td class="align_center">'.(isset($holidayInfo["type"]) ? $holidayInfo["type"] : "").'</td>
								</tr>';
					
							}

							   if($service_credit && ($dispLogDate || !$haswholedayleave)) $t_service_credit+=$service_credit;

					    }///< end else no sched

			  }
			  $tabsent = ($tabsent ? $this->attcompute->sec_to_hm($tabsent) : "");

$content .= '
				
				</tbody>
				</table>
				<br><br>


				<table class="table table-bordered datatable">
					<tr>
						<th class="align_center" rowspan="2" width="260px"><b>TOTAL</b></th>
						<th class="align_center" colspan="3">Overtime (hr:min)</th>
						<th class="align_center">Late/Undertime</th>
						<th class="align_center" rowspan="2" width="80px">Absent</th>                        
						<th class="align_center" colspan="3">Leaves</th>
						<th class="align_center" rowspan="2" width="60px">Service Credit</th>
						<!-- <th class="align_center" rowspan="2" >Remarks/Others</th> -->
						<th class="align_center" rowspan="2" >Holiday</th>
			        </tr>
			        <tr>
		                <th class="align_center" width="50px">Regular</th>
		                <th class="align_center" width="50px">Rest Day</th>
		                <th class="align_center" width="50px">Holiday</th>
		                <th class="align_center" width="80px">Hr:min</th>            
		                <th class="align_center" width="50px">Vacation</th>
		                <th class="align_center" width="50px">Sick</th>
		                <th class="align_center" width="50px">Other</th>
			        </tr>

					<tr class="edata">
					    <td class="align_right border_bottom"></td>
				        <td class="align_center border_bottom">'.($totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "")).'</td>
				        <td class="align_center border_bottom">'.($totrest   = ($totrest ? $this->attcompute->sec_to_hm($totrest) : "")).'</td>
				        <td class="align_center border_bottom">'.($tothol   = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "")).'</td>
				        <td class="align_center border_bottom">'.($tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "")).'</td>
				        <td class="align_center border_bottom">'.$tabsent.'</td>
				        <td class="align_center border_bottom">'.$tvl.'</td>
				        <td class="align_center border_bottom">'.$tsl.'</td>
				        <td class="align_center border_bottom">'.$tel.'</td>
				        <td class="align_center border_bottom">'.$t_service_credit.'</td>
				        <td class="align_center border_bottom">'.$tholiday.'</td>
					  </tr>
				</table>

				<br><br>
					<table width="30%">
						<tr>
							<td class="align_right" style="width:30%;">Noted by : </td>
							<td class="align_center"> _________________________________________ </td>
						</tr>
						<tr>
							<td></td>
							<td class="align_center">'.$this->employee->getfullname($empid).'</td>
						</tr>
						<tr>
							<td></td>
							<td class="align_center">'.$this->employee->getempdatacol('deptid',$empid).'</td>
						</tr>
					</table>

				</div>';


?>