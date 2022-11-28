<?php 

// echo '<pre>';print_r($attendance_list);die;

ini_set('memory_limit', -1);
set_time_limit(0);

$CI =& get_instance();
$CI->load->model('utils');
$CI->load->library('PdfCreator_mpdf');
$userlog = $this->session->userdata("username");
$mpdf = new mPDF('L','LETTER','','UTF-8',5,5,8,5);
$mpdf->simpleTables=true;
$mpdf->packTableData=true;

$content = '';
$style = '';

$header = '
<table class="header">
	<tr>
		<td class="align_center" rowspan="3" style="padding-left: 0px" valign="bottom"><img src="'.base_url().'images/school_logo.bmp" style="width: 100px;"/></td><br><br>
		<td style="font-size:25px;width: 100%;"><b>Pinnacle Technologies Inc.</b></td>
	</tr>
	<tr>
		<td style="font-size:20px;">D`Great</td>
	</tr>
	<tr>
		<td style="font-size:18px;">'.$datedisplay.' Attendance  </td>
	</tr>
</table>
';

$mpdf->SetHTMLHeader($header);

$style .= '
<style>
	.container{
		display: block;
		 position:absolute;
		 top: 120px;
	}
	table, .well-content{
		width: 98%;
		font-family:calibri;
	}
	.header, #maincontent th{
		color: black;
	}
	.table-bordered{
		border: 1px solid #fff;
	    border-collapse: collapse;
	    font-size: 10px;
	}

	.table-bordered td, .table-bordered th {
	    border: 1px solid #626262;
	}

	.table-bordered tr th{
	    background-color: #393737;
	    color: #d2cf85;
	}

	.table-bordered td{
		text-align: center;
	}


	.align_center{
		text-align: center;
	}
	.align_right{
		text-align: right;
	}
	.border_bottom{
		border-bottom: 1px solid grey;
	}
	.late{
		color:red;
	}
	.absent{
		background-color: #ffe6e6;
	}
	.nosched td{
		background-color: gray !important;
		color: white !important;
	}

	.hide{
		display : none;
	}
</style>
';


$mpdf->WriteHTML($style);
$current_count = 0;

		foreach ($attendance_list as $deptid => $emplist) { 
			foreach ($emplist as $empid => $datelist) {
				$current_count++;

				$content .= '

				<div class="well-content container" style="border: transparent !important;">
<br>
				<div class="well-content container" style="margin-top:10px;">
					<legend>Employee Name: '. $emplist_detail[$empid]["fullname"] .'</legend>
				</div>
				<div class="well-content container" style="margin-top:10px;">
					<legend>Employee ID: '. $empid .'</legend>
				</div>
				<div class="well-content container" style="margin-top:10px;">
					<legend>Campus: '. $emplist_detail[$empid]["campus"] .'</legend>
				</div>
				<div class="well-content container" style="margin-top:10px;">
					<legend>Department: '. $emplist_detail[$empid]["department"] .'</legend>
				</div>
				<br>
				<table class="table table-bordered datatable" id="indvtbl">
				    <thead>
				        <tr>
				            <th rowspan="2" class="align_center" width="120px">Date</th>
				            <th class="align_center" colspan="2" style="width:120px;">Actual Log Time</th>
				            <th class="align_center" colspan="3">OT (hr:min)</th>
				            <th class="align_center" rowspan="2">Late (hr:min)</th>
				            <th class="align_center" rowspan="2">UT (hr:min)</th>
				            <th class="align_center" colspan="3">Leaves</th>
				            <th class="align_center" rowspan="2" width="50px;">Service Credit</th>
				            <th class="align_center" rowspan="2" width="50px;">OB</th>
				            <th class="align_center" rowspan="2" width="70px;">Remarks</th>
				            <th class="align_center" rowspan="2">Total Deduction</th>
				            <th class="align_center" rowspan="2">Holiday</th>
				        </tr>
				        <tr>
				            <th class="align_center" width="40px;">IN</th>
				            <th class="align_center" width="40px;">OUT</th>
				            <th class="align_center" width="20px;">Reg</th>
				            <th class="align_center" width="20px;">Rest</th>
				            <th class="align_center" width="20px;">Hol</th>
				            <!-- <th class="align_center hide" width="40px;">Lec</th> -->
				            <!-- <th class="align_center hide" width="40px;">Lab</th> -->
				            <!-- <th class="align_center" width="40px;">Admin</th> -->
				            <!-- <th class="align_center hide" width="40px;">Lec</th> -->
				            <!-- <th class="align_center hide" width="40px;">Lab</th> -->
				            <!-- <th class="align_center" width="40px;">Admin</th> -->
				            <th class="align_center" width="20px;">VL</th>
				            <th class="align_center" width="20px;">SL</th>
				            <th class="align_center" width="20px;">Other</th>
				            <!-- <th class="align_center hide" width="40px;">Lec</th> -->
				            <!-- <th class="align_center hide" width="40px;">Lab</th> -->
				            <!-- <th class="align_center" width="40px;">Admin</th> -->
				        </tr>
				    </thead>
				    <tbody id="employeelist">
				';

				$x = $tdadmin = 0;
				$t_otreg = $t_otrest = $t_othol = $t_latelec = $t_latelab = $t_lateadmin = $t_utlec = $t_utlab = $t_utadmin = $t_vl = $t_sl = $t_othleave = $t_sc = $t_ob = $t_deduclec = $t_deduclab = $t_deducadmin = $t_hol = '';
				$date_now = date('Y-m-d');
				// echo "<pre>"; print_r($datelist); die;
				foreach ($datelist as $date => $att_detail) {


					$isSingleLog = true;
					$log_count = sizeof($att_detail['detail']);
					$lateut_lec_perday = $lateut_lab_perday = $lateut_admin_perday = $vl_perday = $sl_perday = $ob_perday = $other_perday = $sc_perday = 0;
					$ut_lec_perday = $ut_lab_perday = $ut_admin_perday = 0;
					$otreg_perday = $otrest_perday = $othol_perday = 0;
					$deduclec_perday = $deduclab_perday = $deducadmin_perday = 0;

					if($x%2 == 0)   $color = ' style="background-color: white;"';
					else            $color = ' style="background-color: #f2f2f2;"';
					$x++;

					if($log_count > 0 && !isset($att_detail['detail'][0]['nosched'])){
						$login_prev = $logout_prev = '';

						foreach ($att_detail['detail'] as $key => $persched_log) {


							if(($login_prev != $persched_log['login'] || $logout_prev != $persched_log['logout']) && ($persched_log['login'] != '' && $persched_log['logout'] != '') ){
								$isSingleLog = false;
							}else{
								$isSingleLog = true;
							}

							$login_prev = $persched_log['login'];
							$logout_prev = $persched_log['logout'];

							if($CI->attcompute->exp_time($persched_log['deduc_lec']) || $CI->attcompute->exp_time($persched_log['deduc_lab']) || $CI->attcompute->exp_time($persched_log['deduc_admin']) >= 14400){
								$persched_log['deduc_lec'] = "4:00";
								$persched_log['deduc_lab'] = "4:00";
								$persched_log['deduc_admin'] = "4:00";
							}
							if($CI->attcompute->exp_time($persched_log['ut_lec']) || $CI->attcompute->exp_time($persched_log['ut_lab']) || $CI->attcompute->exp_time($persched_log['ut_admin']) >= 14400){
								$persched_log['ut_lec'] = "4:00";
								$persched_log['ut_lab'] = "4:00";
								$persched_log['ut_admin'] = "4:00";
							}

							$lateut_lec_perday += $CI->attcompute->exp_time($persched_log['lateut_lec']);
							$lateut_lab_perday += $CI->attcompute->exp_time($persched_log['lateut_lab']);
							$lateut_admin_perday += $CI->attcompute->exp_time($persched_log['lateut_admin']);

							$ut_lec_perday += $CI->attcompute->exp_time($persched_log['ut_lec']);
							$ut_lab_perday += $CI->attcompute->exp_time($persched_log['ut_lab']);
							$ut_admin_perday += $CI->attcompute->exp_time($persched_log['ut_admin']);

							$vl_perday += $persched_log['vl'] + $persched_log['el'];
							$sl_perday += $persched_log['sl'];
							$other_perday += $persched_log['other'];
							$sc_perday += $persched_log['service_credit'];
							$ob_perday += $persched_log['ob'];

							$deduclec_perday += $CI->attcompute->exp_time($persched_log['deduc_lec']);
							$deduclab_perday += $CI->attcompute->exp_time($persched_log['deduc_lab']);
							$deducadmin_perday += $CI->attcompute->exp_time($persched_log['deduc_admin']);

						}
					}

					$vl_perday = $vl_perday > 1 ? 1 : $vl_perday;
					$sl_perday = $sl_perday > 1 ? 1 : $sl_perday;
					$other_perday = $other_perday > 1 ? 1 : $other_perday;
					$sc_perday = $sc_perday > 1 ? 1 : $sc_perday;
					$ob_perday = $ob_perday > 1 ? 1 : $ob_perday;
					$t_hol += (isset($att_detail['holidayinfo']['description']) ? 1 : 0);

					$ob_data = $this->attcompute->displayLateUTAbs($empid, $date);

					$pending = $this->attcompute->displayPendingApp($empid,$date);
					$pending_ob = $this->attcompute->displayPendingOBApp($empid,$date, ($hasLate) ? "late" : "undertime");

					$log_counter = $sched = 0;
					
					 $tempholiday = '';
					foreach ($att_detail['detail'] as $key => $persched_log) {
						$log_counter++;
						$hasLate = $persched_log['hasLate'];
						$hasUT = $persched_log['hasUT'];
						$schedule = $this->attcompute->displaySched($empid,$date);
						$log_remarks = '';
						$absent = $this->attcompute->displayAbsent($schedule->row($sched)->starttime,$schedule->row($sched)->endtime,$persched_log['login'],$persched_log['logout'],$empid,$date,$schedule->row($sched)->early_dismissal);

						list($lateutlec,$lateutlab,$lateutadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUT($schedule->row($sched)->starttime,$schedule->row($sched)->endtime,$schedule->row($sched)->tardy_start,$persched_log['login'],$persched_log['logout'],$persched_log['sched_type'],$absent);
					
						if($tschedadmin > 0){
				            $tschedadmin = $this->time->roundOffTime($tschedadmin);
				        }

				        if($this->attcompute->exp_time($tschedadmin) <= 14400 && $this->attcompute->exp_time($tschedadmin)) $tschedadmin = "4:00";
				        else if($this->attcompute->exp_time($tschedadmin) >= 14400) $tschedadmin = "4:00";

				        if ($this->attcompute->exp_time($lateutlec) >= 14400) $lateutlec = "4:00";
				        elseif ($this->attcompute->exp_time($lateutlab) >= 14400) $lateutlab = "4:00";
				        elseif ($this->attcompute->exp_time($lateutadmin) >= 14400) $lateutadmin = "4:00";

						if(isset($tempholiday) && $tempholiday != '') $att_detail['holidayinfo'] = $tempholiday;

						if(isset($att_detail['holidayinfo']['description'])){
							if($att_detail['holidayinfo']['halfday'] == 'on'){
								if($att_detail['holidayinfo']['sched_count'] == 'first'){
									if($sched == 0){
										$tschedlec = $tschedlab = $tschedadmin = $absent = "";
										$log_remarks .= $att_detail['holidayinfo']['description'].'<br>';
									}
									else{
										$tempholiday = $att_detail['holidayinfo'];
										unset($att_detail['holidayinfo']);
									}
								}else if($att_detail['holidayinfo']['sched_count'] == 'second'){
									if($sched == 1){
										$tschedlec = $tschedlab = $tschedadmin = $absent = "";
										$log_remarks .= $att_detail['holidayinfo']['description'].'<br>';
									}else{
										$tempholiday = $att_detail['holidayinfo'];
										unset($att_detail['holidayinfo']);
									}
								}
							}else{
								$tschedlec = $tschedlab = $tschedadmin = $absent = "";
								$log_remarks .= $att_detail['holidayinfo']['description'].'<br>';
							}
						}


						if($absent){
							if(!$persched_log['login'] && !$persched_log['logout']) $log_remarks = 'NO TIME IN AND OUT';
							elseif(!$persched_log['login']) $log_remarks = 'NO TIME IN';
							elseif(!$persched_log['logout']) $log_remarks = 'NO TIME OUT';
						}

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

						if($absent && !isset($att_detail['holidayinfo'])){
							if(in_array("absent", $ob_data)) $log_remarks = "EXCUSED ABSENT";
				            else{
				            	if(strtotime($date) < strtotime($date_now)){
				            		$log_remarks = "UNEXCUSED ABSENT";
				            		$ob_type = false;
			            			$ob_data = array();
				            	}
				            }
				        }
				        $tdadmin += $this->attcompute->exp_time($tschedadmin);
						

						 if($log_counter|| !$isSingleLog){ 
								if(!isset($persched_log["nosched"]) && $persched_log['flexi'] != 'YES'){

$content .= '
										<tr '.$color.'>';
				    						if($log_counter){
				    							if($isSingleLog && $log_count > 1){
				    								foreach ($att_detail['detail'] as $key => $value) {
				    									if($value['hasLate'] == 1) $hasLate = 1;
				    									if($value['hasUT'] == 1) $hasUT = 1;
				    								}
				    							}
if($log_counter == 1) $content .= '	    							<td rowspan="2">'.date("d-M (l)",strtotime($date)).'</td>';
				    						}
$content .= '
				    						<td class="'.( ($persched_log["isAbsent"]?"absent ":'').($hasLate?"late":'') ).'" >'.
				    							(( isset($persched_log['login']) && $persched_log['login'] != '' )?date("h:i A",strtotime($persched_log['login'])):'--').'</td>
				    						<td class="'.( ($persched_log["isAbsent"]?"absent ":'').($hasUT?"late":'') ).'" >'.
				    							(( isset($persched_log['logout']) && $persched_log['logout'] != '' )?date("h:i A",strtotime($persched_log['logout'])):'--').'</td>';

				    						if($log_counter){
				    							$otreg_perday += $CI->attcompute->exp_time($persched_log['otreg']);
				    							$otrest_perday += $CI->attcompute->exp_time($persched_log['otrest']);
				    							$othol_perday += $CI->attcompute->exp_time($persched_log['othol']);

$content .= '	    							<td rowspan="'.($isSingleLog?1:$log_count).'">'.$persched_log['otreg'].'</td>
				    							<td rowspan="'.($isSingleLog?1:$log_count).'">'.$persched_log['otrest'].'</td>
				    							<td rowspan="'.($isSingleLog?1:$log_count).'">'.$persched_log['othol'].'</td>';
				    						} 

				    						if(!$isSingleLog){
$content .= '		    							<!-- <td>'.$persched_log['lateut_lec'].'</td>
					    							<td>'.$persched_log['lateut_lab'].'</td> -->
					    							<td>'.$persched_log['lateut_admin'].'</td>
					    							<!-- <td>'.$persched_log['ut_lec'].'</td>
					    							<td>'.$persched_log['ut_lab'].'</td> -->
					    							<td>'.$persched_log['ut_admin'].'</td>';
				    						}elseif($isSingleLog && $log_counter){
$content .= '	    								<!-- <td>'.($lateut_lec_perday?date('H:i', mktime(0,0,$lateut_lec_perday)):'').'</td>
				    								<td>'.($lateut_lab_perday?date('H:i', mktime(0,0,$lateut_lab_perday)):'').'</td> -->
				    								<td>'.($lateut_admin_perday?date('H:i', mktime(0,0,$lateut_admin_perday)):'').'</td>
				    								<!-- <td>'.($ut_lec_perday?date('H:i', mktime(0,0,$ut_lec_perday)):'').'</td>
				    								<td>'.($ut_lab_perday?date('H:i', mktime(0,0,$ut_lab_perday)):'').'</td> -->
				    								<td>'.($ut_admin_perday?date('H:i', mktime(0,0,$ut_admin_perday)):'').'</td>';
				    						}

				    						if(!$isSingleLog && !$persched_log['haswholedayleave']){
$content .= '		    							<td>'.$persched_log['vl'].'</td>
					    							<td>'.$persched_log['sl'].'</td>
					    							<td>'.$persched_log['other'].'</td>
					    							<td>'.$persched_log['service_credit'].'</td>
					    							<td>'.$persched_log['ob'].'</td>';
				    						}elseif( ($isSingleLog || $persched_log['haswholedayleave']) && $log_counter){
$content .= '	    								<td>'.($vl_perday?number_format($vl_perday,2):'').'</td>
				    								<td>'.($sl_perday?number_format($sl_perday,2):'').'</td>
				    								<td>'.($other_perday?number_format($other_perday,2):'').'</td>
				    								<td>'.($sc_perday?number_format($sc_perday,2):'').'</td>
				    								<td>'.($ob_perday?number_format($ob_perday,2):'').'</td>';
				    						} 

				    						if($log_counter){
$content .= '	    							<td rowspan="'.($isSingleLog?1:$log_count).'">
				    								'.(($persched_log['ol']) ? (($oltype) ? (($oltype == 'ABSENT') ? 'ABSENT W/FILE<br>' : $oltype.'<br>') : $CI->employeemod->othLeaveDesc($persched_log['ol'])."<br>") : $persched_log['cs_app']).$log_remarks.$pending_ob.($pending ? "PENDING ".$pending : '').'
				    							</td>';
				    						}

				    						if(!$isSingleLog){
$content .= '		    							<!-- <td>'.$persched_log['deduc_lec'].'</td>
					    							<td>'.$persched_log['deduc_lab'].'</td> -->
					    							<td>'.$persched_log['deduc_admin'].'</td>';
				    						}elseif($isSingleLog && $log_counter){
$content .= '	    								<!-- <td>'.($deduclec_perday?date('H:i', mktime(0,0,$deduclec_perday)):'').'</td>
				    								<td>'.($deduclab_perday?date('H:i', mktime(0,0,$deduclab_perday)):'').'</td> -->
				    								<td>'.$tschedadmin.'</td>';
				    						}

				    						if($log_counter){
$content .= '		    							<td rowspan="'.($isSingleLog?1:$log_count).'">'.(isset($att_detail['holidayinfo']['type'])?$att_detail['holidayinfo']['type']:'').'</td>';
				    						}


$content .= '    						</tr>';

						}else{ ///< no sched and flexi------------------------------------------------------------------------------------------------------------------------ 

    									$log_count = sizeof($persched_log['logs']);


$content .= '							<tr '.$color.(isset($persched_log["nosched"])?" class='nosched'":"").'>';
				    						if($log_counter == 1){
$content .= '				    				<td rowspan="'.$log_count.'">'.date("d-M (l)",strtotime($date)).'</td>';
				    						} 

				    							if(sizeof($persched_log['logs']) > 0){
					    							foreach ($persched_log['logs'] as $log_key => $logs) {
					    								
$content .= '				    						<td class="'.( $persched_log["hasLate"]?"late":'' ).'" >
							    								'.(( isset($logs['login']) && $logs['login'] != '' )?date("h:i A",strtotime($logs['login'])):'--').'</td>';
$content .= '				    						<td class="'.( $persched_log["hasUT"]?"late":'' ).'" >
							    								'.(( isset($logs['logout']) && $logs['logout'] != '' )?date("h:i A",strtotime($logs['logout'])):'--').'</td>';
					    							
					    								break;
					    							}
				    							}else{
$content .= '	    									<td rowspan="'.$log_count.'"  class="'.( $persched_log["isAbsent"]?"absent ":'' ).'" >--</td>
				    									<td rowspan="'.$log_count.'"  class="'.( $persched_log["isAbsent"]?"absent ":'' ).'" >--</td>';
				    							}


				    						if($log_counter == 1){
				    							$otreg_perday += $CI->attcompute->exp_time($persched_log['otreg']);
				    							$otrest_perday += $CI->attcompute->exp_time($persched_log['otrest']);
				    							$othol_perday += $CI->attcompute->exp_time($persched_log['othol']);

$content .= '	    							<td rowspan="'.$log_count.'">'.$persched_log['otreg'].'</td>
				    							<td rowspan="'.$log_count.'">'.$persched_log['otrest'].'</td>
				    							<td rowspan="'.$log_count.'">'.$persched_log['othol'].'</td>';
				    						}

				    					
$content .= '	    							<!-- <td rowspan="'.$log_count.'">'.(isset($persched_log['lateut_lec'])?$persched_log['lateut_lec']:'--').'</td>
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['lateut_lab'])?$persched_log['lateut_lab']:'--').'</td> -->
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['lateut_admin'])?$persched_log['lateut_admin']:'--').'</td>
				    							<!-- <td rowspan="'.$log_count.'">'.(isset($persched_log['ut_lec'])?$persched_log['ut_lec']:'--').'</td>
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['ut_lab'])?$persched_log['ut_lab']:'--').'</td>  -->
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['ut_admin'])?$persched_log['ut_admin']:'--').'</td>
				    						
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['vl'])?$persched_log['vl']:'--').'</td>
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['sl'])?$persched_log['sl']:'--').'</td>
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['other'])?$persched_log['other']:'--').'</td>
				    							<td rowspan="'.$log_count.'">'.$persched_log['service_credit'].'</td>
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['ob'])?$persched_log['ob']:'--').'</td>';
				    				
				    						if($log_counter == 1){
$content .= '	    							<td rowspan="'.$log_count.'">
				    								'.( $persched_log['ol'] ? ($persched_log['oltype'] ? ($persched_log['oltype'] == 'ABSENT' ? 'ABSENT W/ FILE<br>'
				    									 : $persched_log['oltype'].'<br>')
				    									 : $CI->employeemod->othLeaveDesc($persched_log['ol'])."<br>" )
				    									 : $absent_perday ? "NO TIME IN AND OUT<br>" : '') . 
				    									 ( $persched_log['cs_app'] ) .
				    									 ($lateut_lec_perday ? "UNEXCUSED LATE" : "")

				    								.'
				    							</td>';
				    						}

				    					
$content .= '	    							<!-- <td rowspan="'.$log_count.'">'.(isset($persched_log['deduc_lec'])?$persched_log['deduc_lec']:'--').'</td>
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['deduc_lab'])?$persched_log['deduc_lab']:'--').'</td> -->
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['deduc_admin'])?$persched_log['deduc_admin']:'--').'</td>';
				    					
				    						if($log_counter == 1){
$content .= '	    							<td rowspan="'.$log_count.'">'. ((isset($att_detail['holidayinfo']['description'])) ? $att_detail['holidayinfo']['description'] ." - ": "") .''.(isset($att_detail['holidayinfo']['type'])?$att_detail['holidayinfo']['type']:'').'</td>';
				    						}

$content .= '	    					</tr>';

				    					if($log_count > 1){ 
				    						for ($i=1; $i < $log_count; $i++) { 
				    							$logs = $persched_log['logs'][$i];
				    							
				    							
$content .= '	    							<tr '.(isset($persched_log["nosched"])?"class='nosched'":"").'>
					    							<td class="'.( $persched_log["hasLate"]?"late":'' ).'" >'.(( isset($logs['login']) && $logs['login'] != '' )?date("h:i A",strtotime($logs['login'])):'--').'</td>
					    							<td class="'.( $persched_log["hasUT"]?"late":'' ).'" >'.(( isset($logs['logout']) && $logs['logout'] != '' )?date("h:i A",strtotime($logs['logout'])):'--').'</td>
					    						</tr>';
				    					
				    						}
				    					} //< end if log_count

   
							} //< end else no sched

					}
					$sched++;
				} ///< end loop logs

				$t_otreg += $otreg_perday;
				$t_otrest += $otrest_perday;
				$t_othol += $othol_perday;

				$t_latelec += $lateut_lec_perday;
				$t_latelab += $lateut_lab_perday;
				$t_lateadmin += $lateut_admin_perday;

				$t_utlec += $ut_lec_perday;
				$t_utlab += $ut_lab_perday;
				$t_utadmin += $ut_admin_perday;

				$t_vl += $vl_perday;
				$t_sl += $sl_perday;
				$t_othleave += $other_perday;

				$t_sc += $sc_perday;

				$t_ob += $ob_perday;

				$t_deduclec += $deduclec_perday;
				$t_deduclab += $deduclab_perday;
				$t_deducadmin += $deducadmin_perday;


			} ///< end loop dates


$content .= '
					
				</tbody>
				</table>
				<br>


				<table class="table table-bordered datatable" >
						<tr>
							<th class="align_center" rowspan="2" width="240px"><b>TOTAL</b></th>
						    <th class="align_center" colspan="3">OT (hr:min)</th>
						    <th class="align_center" rowspan="2">Late (hr:min)</th>
						    <th class="align_center" rowspan="2">UT (hr:min)</th>
						    <th class="align_center" colspan="3">Leaves</th>
						    <th class="align_center" rowspan="2" width="50px">Service Credit</th>
						    <th class="align_center" rowspan="2" width="50px">OB</th>
						    <th class="align_center" rowspan="2" width="70px;" >Remarks</th>
						    <th class="align_center" rowspan="2">Total Deduction</th>
						    <th class="align_center" rowspan="2">Holiday</th>
						</tr>
						<tr>
						    <th class="align_center" width="20px">Reg</th>
						    <th class="align_center" width="20px">Rest</th>
						    <th class="align_center" width="20px">Hol</th>
						    <!-- <th class="align_center" width="40px">Lec</th>
						    <th class="align_center" width="40px">Lab</th>
						    <th class="align_center" width="40px">Admin</th> -->
						    <!-- <th class="align_center" width="40px">Lec</th>
						    <th class="align_center" width="40px">Lab</th>
						    <th class="align_center" width="40px">Admin</th> -->
						    <th class="align_center" width="20px">VL</th>
						    <th class="align_center" width="20px">SL</th>
						    <th class="align_center" width="20px">Other</th>
						    <!-- <th class="align_center" width="40px">Lec</th>
						    <th class="align_center" width="40px">Lab</th>
						    <th class="align_center" width="40px">Admin</th> -->
						</tr>

						<tr class="edata" >
						    <td class="align_right border_bottom"></td>
					        <td class="align_center border_bottom">'.($t_otreg ? $this->attcompute->sec_to_hm($t_otreg) : "").'</td>
					        <td class="align_center border_bottom">'.($t_otrest ? $this->attcompute->sec_to_hm($t_otrest) : "").'</td>
					        <td class="align_center border_bottom">'.($t_othol ? $this->attcompute->sec_to_hm($t_othol) : "").'</td>
					        <!-- <td class="align_center border_bottom">'.($t_latelec ? $this->attcompute->sec_to_hm($t_latelec) : "").'</td> -->
					        <!-- <td class="align_center border_bottom">'.($t_latelab ? $this->attcompute->sec_to_hm($t_latelab) : "").'</td> -->
					        <td class="align_center border_bottom">'.($t_lateadmin ? $this->attcompute->sec_to_hm($t_lateadmin) : "").'</td>
					        <!-- <td class="align_center border_bottom">'.($t_utlec ? $this->attcompute->sec_to_hm($t_utlec) : "").'</td> -->
					        <!-- <td class="align_center border_bottom">'.($t_utlab ? $this->attcompute->sec_to_hm($t_utlab) : "").'</td> -->
					        <td class="align_center border_bottom">'.($t_utadmin ? $this->attcompute->sec_to_hm($t_utadmin) : "").'</td>
					        <td class="align_center border_bottom">'.($t_vl ? $t_vl : "").'</td>
					        <td class="align_center border_bottom">'.($t_sl ? $t_sl : "").'</td>
					        <td class="align_center border_bottom">'.($t_othleave ? $t_othleave : "").'</td>
					        <td class="align_center border_bottom">'.($t_sc ? $t_sc : "").'</td>
					        <td class="align_center border_bottom">'.($t_ob ? $t_ob : "").'</td>
					        <td class="align_center border_bottom"></td>
					        <!-- <td class="align_center border_bottom">'.($t_deduclec ? $this->attcompute->sec_to_hm($t_deduclec) : "").'</td> -->
					        <!-- <td class="align_center border_bottom">'.($t_deduclab ? $this->attcompute->sec_to_hm($t_deduclab) : "").'</td> -->
					        <td class="align_center border_bottom">'.($tdadmin ? $this->attcompute->sec_to_hm($tdadmin) : "").'</td>
					        <td class="align_center border_bottom">'.($t_hol ? $t_hol : "").'</td>
					        
					 	</tr>
				</table>

				<br>

					<table style="width:55%;">
					<tr>
						<td style="width:100%;">
							<table>
								<tr class="align_right">
									<td>Acknowledged by : </td>
									<td> _________________________________________ </td>
								</tr>

							</table>
						</td>
						';

		if($this->extras->findIfAdmin($userlog) == true){

			$content .= '<td style="width:100%;">
							<table>
								<tr class="align_right">
									<td>Certified Correct : </td>
									<td> _________________________________________ </td>
								</tr>
							</table>
						</td>';
		}else{
			$content .=	 '<td style="width:33%;padding-top:-32px;">
							<table>
								<tr>
									<td>Verified by : </td>
									<td> _________________________________________ </td>
								</tr>
							</table>
						</td>
						';
			}
		$content .= '<td style="width:33%;"></td></tr>
					</table>
				</div>';

		if($empcount != $current_count) $content .= '<pagebreak>';

			$mpdf->WriteHTML($content);
			$content = '';

		} ///< end loop emp
	}

    			

$mpdf->Output();

die;

echo '<pre>attendance detailed';

?>


<style>
	table, .well-content{
		width: 100%;
		font-family:calibri;
	}
	.header, #maincontent th{
		color: black;
	}
	.table-bordered{
		border: 1px solid #fff;
	    border-collapse: collapse;
	}

	.table-bordered td, .table-bordered th {
	    border: 1px solid grey;
	}

	.table-bordered tr th{
	    background-color: #3b5998;
	    color: #FFF;
	}

	.table-bordered td{
		text-align: center;
	}

	.table-bordered tbody tr:nth-child(even) {background-color: #dedede;}

	.table-bordered tbody:nth-child(even) td[rowspan] {
	    background-color: #dedede;
	}

	.align_center{
		text-align: center;
	}
	.align_right{
		text-align: right;
	}
	.border_bottom{
		border-bottom: 1px solid grey;
	}
	.late{
		color:red;
	}
	.absent{
		background-color: #ffe6e6;
	}
	.nosched{
		background-color: gray !important;
		color: white !important;
	}
</style>

<table class="header">
	<tr>
		<td class="align_center" rowspan="2" style="padding:0 20px;" valign="bottom"><img src="images/school_logo.png" style="width: 50px;"/></td>
		<td style="font-size:15px;width: 100%;"><b>Pinnacle Technologies Inc.</b></td>
	</tr>
	<tr>
		<td><?=$datedisplay?> Attendance</td>
	</tr>
</table><br><br>

<div class="well-content" style="border: transparent !important;">
<table class="table table-bordered datatable" id="indvtbl">
    <thead>
        <tr>
            <th rowspan="2" class="align_center" width="120px">Date</th>
            <th class="align_center" colspan="2" style="width:140px;">Actual Log Time</th>
            <th class="align_center" colspan="3">OT (hr:min)</th>
            <th class="align_center" colspan="3">Late (hr:min)</th>
            <th class="align_center" colspan="3">UT (hr:min)</th>
            <th class="align_center" colspan="3">Leaves</th>
            <th class="align_center" rowspan="2" width="60px;">Service Credit</th>
            <th class="align_center" rowspan="2" >Remarks</th>
            <th class="align_center" colspan="3">Total Deduction</th>
            <th class="align_center" rowspan="2" >Holiday</th>
        </tr>
        <tr>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
            <th class="align_center" width="50px;">Reg</th>
            <th class="align_center" width="50px;">Rest</th>
            <th class="align_center" width="50px;">Hol</th>
            <th class="align_center" width="50px;">Lec</th>
            <th class="align_center" width="50px;">Lab</th>
            <th class="align_center" width="50px;">Admin</th>
            <th class="align_center" width="50px;">Lec</th>
            <th class="align_center" width="50px;">Lab</th>
            <th class="align_center" width="50px;">Admin</th>
            <th class="align_center" width="50px;">VL</th>
            <th class="align_center" width="50px;">SL</th>
            <th class="align_center" width="50px;">Other</th>
            <th class="align_center" width="40px;">Lec</th>
            <th class="align_center" width="40px;">Lab</th>
            <th class="align_center" width="40px;">Admin</th>
        </tr>
    </thead>
    <tbody id="employeelist">

    	<? foreach ($attendance_list as $deptid => $emplist) { 
    			foreach ($emplist as $empid => $datelist) {
    				foreach ($datelist as $date => $att_detail) {


    					$isSingleLog = true;
    					$log_count = sizeof($att_detail['detail']);
    					$lateut_lec_perday = $lateut_lab_perday = $lateut_admin_perday = $vl_perday = $sl_perday = $other_perday = $sc_perday = 0;
    					$ut_lec_perday = $ut_lab_perday = $ut_admin_perday = 0;
    					$deduclec_perday = $deduclab_perday = $deducadmin_perday = 0;

    					if($log_count > 1 && !isset($att_detail['detail'][0]['nosched'])){
    						$login_prev = $logout_prev = '';

    						foreach ($att_detail['detail'] as $key => $persched_log) {


    							if(($login_prev != $persched_log['login'] || $logout_prev != $persched_log['logout']) && ($persched_log['login'] != '' && $persched_log['logout'] != '') ){
    								$isSingleLog = false;
    							}else{
    								$isSingleLog = true;
    							}

    							$login_prev = $persched_log['login'];
    							$logout_prev = $persched_log['logout'];

    							$lateut_lec_perday += $CI->attcompute->exp_time($persched_log['lateut_lec']);
    							$lateut_lab_perday += $CI->attcompute->exp_time($persched_log['lateut_lab']);
    							$lateut_admin_perday += $CI->attcompute->exp_time($persched_log['lateut_admin']);

    							$ut_lec_perday += $CI->attcompute->exp_time($persched_log['ut_lec']);
    							$ut_lab_perday += $CI->attcompute->exp_time($persched_log['ut_lab']);
    							$ut_admin_perday += $CI->attcompute->exp_time($persched_log['ut_admin']);


    							$vl_perday += $persched_log['vl'];
    							$sl_perday += $persched_log['sl'];
    							$other_perday += $persched_log['other'];
    							$sc_perday += $persched_log['service_credit'];

    							$deduclec_perday += $CI->attcompute->exp_time($persched_log['deduc_lec']);
    							$deduclab_perday += $CI->attcompute->exp_time($persched_log['deduc_lab']);
    							$deducadmin_perday += $CI->attcompute->exp_time($persched_log['deduc_admin']);
    						}
    					}

    					$vl_perday = $vl_perday > 1 ? 1 : $vl_perday;
    					$sl_perday = $sl_perday > 1 ? 1 : $sl_perday;
    					$other_perday = $other_perday > 1 ? 1 : $other_perday;
    					$sc_perday = $sc_perday > 1 ? 1 : $sc_perday;

    					$log_counter = 0;
    					

    					foreach ($att_detail['detail'] as $key => $persched_log) {
    						$log_counter++;

    						$hasLate = $persched_log['hasLate'];
							$hasUT = $persched_log['hasUT'];
    							
    						
    						if($log_counter == 1 || !$isSingleLog){ 
    								if(!isset($persched_log["nosched"]) && $persched_log['flexi'] == 'NO'){

    						?>

				    					<tr>
				    						<? if($log_counter == 1){ 
						    						if($isSingleLog && sizeof($att_detail['detail']) > 1){
														foreach ($att_detail['detail'] as $key => $value) {
															if($value['hasLate'] == 1) $hasLate = 1;
															if($value['hasUT'] == 1) $hasUT = 1;
															// var_dump($hasUT);
														}
													}

				    							?>
				    							<td rowspan="<?=$isSingleLog?1:$log_count?>"><?=date("d-M (l)",strtotime($date))?></td>
				    						<? } ?>

				    						<td class="<?=( ($persched_log["isAbsent"]?"absent ":'').($hasLate?"late":'') )?>" ><?=( isset($persched_log['login']) && $persched_log['login'] != '' )?date("h:i A",strtotime($persched_log['login'])):'--'?></td>
				    						<td class="<?=( ($persched_log["isAbsent"]?"absent ":'').($hasUT?"late":'') )?>" ><?=( isset($persched_log['logout']) && $persched_log['logout'] != '' )?date("h:i A",strtotime($persched_log['logout'])):'--'?></td>

				    						<? if($log_counter == 1){ ?>
				    							<td rowspan="<?=$isSingleLog?1:$log_count?>"><?=$persched_log['otreg']?></td>
				    							<td rowspan="<?=$isSingleLog?1:$log_count?>"><?=$persched_log['otrest']?></td>
				    							<td rowspan="<?=$isSingleLog?1:$log_count?>"><?=$persched_log['othol'].'ddd'?></td>
				    						<? } 

				    						if(!$isSingleLog){ ?>
					    							<td><?=$persched_log['lateut_lec']?></td>
					    							<td><?=$persched_log['lateut_lab']?></td>
					    							<td><?=$persched_log['lateut_admin']?></td>
					    							<td><?=$persched_log['ut_lec']?></td>
					    							<td><?=$persched_log['ut_lab']?></td>
					    							<td><?=$persched_log['ut_admin']?></td>
				    						<? }elseif($isSingleLog && $log_counter == 1){ ?>
				    								<td><?=$lateut_lec_perday?date('H:i', mktime(0,0,$lateut_lec_perday)):''?></td>
				    								<td><?=$lateut_lab_perday?date('H:i', mktime(0,0,$lateut_lab_perday)):''?></td>
				    								<td><?=$lateut_admin_perday?date('H:i', mktime(0,0,$lateut_admin_perday)).'dddd':''?></td>
				    								<td><?=$ut_lec_perday?date('H:i', mktime(0,0,$ut_lec_perday)):''?></td>
				    								<td><?=$ut_lab_perday?date('H:i', mktime(0,0,$ut_lab_perday)):''?></td>
				    								<td><?=$ut_admin_perday?date('H:i', mktime(0,0,$ut_admin_perday)):''?></td>
				    						<? }

				    						if(!$isSingleLog || !$persched_log['haswholedayleave']){ ?>
					    							<td><?=$persched_log['vl']?></td>
					    							<td><?=$persched_log['sl']?></td>
					    							<td><?=$persched_log['other']?></td>
					    							<td><?=$persched_log['service_credit']?></td>
					    							<td><?=$persched_log['ob']?></td>
				    						<? }elseif( ($isSingleLog || $persched_log['haswholedayleave']) && $log_counter == 1 ){ ?>
				    								<td><?=$vl_perday?number_format($vl_perday,2):''?></td>
				    								<td><?=$sl_perday?number_format($sl_perday,2):''?></td>
				    								<td><?=$other_perday?number_format($other_perday,2):''?></td>
				    								<td><?=$sc_perday?number_format($sc_perday,2):''?></td>
				    						<? } 

				    						if($log_counter == 1){ ?>
				    							<td rowspan="<?=$isSingleLog?1:$log_count?>">
				    								<?=( $persched_log['ol'] ? ($persched_log['oltype'] ? ($persched_log['oltype'] == 'ABSENT' ? 'ABSENT W/ FILE<br>'
				    									 : $persched_log['oltype'].'<br>')
				    									 : $CI->employeemod->othLeaveDesc($persched_log['ol'])."<br>" )
				    									 : '') . 
				    									 ( $persched_log['cs_app'] )

				    								?>
				    							</td>
				    						<? }

				    						if(!$isSingleLog){ ?>
					    							<td><?=$persched_log['deduc_lec']?></td>
					    							<td><?=$persched_log['deduc_lab']?></td>
					    							<td><?=$persched_log['deduc_admin']?></td>
				    						<? }elseif($isSingleLog && $log_counter == 1){ ?>
				    								<td><?=$deduclec_perday?date('H:i', mktime(0,0,$deduclec_perday)):''?></td>
				    								<td><?=$deduclab_perday?date('H:i', mktime(0,0,$deduclab_perday)):''?></td>
				    								<td><?=$deducadmin_perday?date('H:i', mktime(0,0,$deducadmin_perday)):''?></td>
				    						<? }

				    						if($log_counter == 1){ ?>
				    							<td rowspan="<?=$isSingleLog?1:$log_count?>"><?=(isset($att_detail['holidayinfo']['type'])?$att_detail['holidayinfo']['type']:'')?></td>
				    						<? } ?>


				    					</tr>
    	<?				
    								}else{ ///< no sched and flexi------------------------------------------------------------------------------------------------------------------------ 

    									$log_count = sizeof($persched_log['logs']);

    									?>

    									<tr <?=(isset($persched_log["nosched"])?"class='nosched'":"")?>>
				    						<? if($log_counter == 1){ ?>
				    							<td rowspan="<?=$log_count?>"><?=date("d-M (l)",strtotime($date))?></td>
				    						<? } 

				    							if(sizeof($persched_log['logs']) > 0){
					    							foreach ($persched_log['logs'] as $log_key => $logs) {?>
					    								
							    						<td class="<?=( $persched_log["hasLate"]?"late":'' )?>" >
							    								<?=( isset($logs['login']) && $logs['login'] != '' )?date("h:i A",strtotime($logs['login'])):'--'?></td>
							    						<td class="<?=( $persched_log["hasUT"]?"late":'' )?>" >
							    								<?=( isset($logs['logout']) && $logs['logout'] != '' )?date("h:i A",strtotime($logs['logout'])):'--'?></td>
					    						<?	
					    								break;
					    							}
				    							}else{?>
				    									<td rowspan="<?=$log_count?>"  class="<?=( $persched_log["isAbsent"]?"absent ":'' )?>" >--</td>
				    									<td rowspan="<?=$log_count?>"  class="<?=( $persched_log["isAbsent"]?"absent ":'' )?>" >--</td>
				    							<? } ?>


				    						<? if($log_counter == 1){ ?>
				    							<td rowspan="<?=$log_count?>"><?=$persched_log['otreg']?></td>
				    							<td rowspan="<?=$log_count?>"><?=$persched_log['otrest']?></td>
				    							<td rowspan="<?=$log_count?>"><?=$persched_log['othol']?></td>
				    						<? } ?>

				    					
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['lateut_lec'])?$persched_log['lateut_lec']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['lateut_lab'])?$persched_log['lateut_lab']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['lateut_admin'])?$persched_log['lateut_admin']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['ut_lec'])?$persched_log['ut_lec']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['ut_lab'])?$persched_log['ut_lab']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['ut_admin'])?$persched_log['ut_admin']:'--')?></td>
				    						
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['vl'])?$persched_log['vl']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['sl'])?$persched_log['sl']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['other'])?$persched_log['other']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=$persched_log['service_credit']?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['ob'])?$persched_log['ob']:'--')?></td>
				    				
				    						<? if($log_counter == 1){ ?>
				    							<td rowspan="<?=$log_count?>">
				    								<?=( $persched_log['ol'] ? ($persched_log['oltype'] ? ($persched_log['oltype'] == 'ABSENT' ? 'ABSENT W/ FILE<br>'
				    									 : $persched_log['oltype'].'<br>')
				    									 : $CI->employeemod->othLeaveDesc($persched_log['ol'])."<br>" )
				    									 : '') . 
				    									 ( $persched_log['cs_app'] )

				    								?>
				    							</td>
				    						<? } ?>

				    					
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['deduc_lec'])?$persched_log['deduc_lec']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['deduc_lab'])?$persched_log['deduc_lab']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['deduc_admin'])?$persched_log['deduc_admin']:'--')?></td>
				    					
				    						<? if($log_counter == 1){ ?>
				    							<td rowspan="<?=$log_count?>"><?=(isset($att_detail['holidayinfo']['type'])?$att_detail['holidayinfo']['type']:'')?></td>
				    						<? } ?>

				    					</tr>

				    					<?if($log_count > 1){ 
				    						for ($i=1; $i < $log_count; $i++) { 
				    							$logs = $persched_log['logs'][$i];
				    							
				    					?>		
				    							<tr <?=(isset($persched_log["nosched"])?"class='nosched'":"")?>>
					    							<td class="<?=( $persched_log["hasLate"]?"late":'' )?>" ><?=( isset($logs['login']) && $logs['login'] != '' )?date("h:i A",strtotime($logs['login'])):'--'?></td>
					    							<td class="<?=( $persched_log["hasUT"]?"late":'' )?>" ><?=( isset($logs['logout']) && $logs['logout'] != '' )?date("h:i A",strtotime($logs['logout'])):'--'?></td>
					    						</tr>
				    					<?
				    						}
				    					} //< end if log_count

   
    								} //< end else no sched
    						}
    					} ///< end loop logs
    				} ///< end loop dates
    			} ///< end loop emp
    		}
    	?>
    			

    	

    </tbody>
</table>
</div>


<?


print_r($attendance_list);
?>