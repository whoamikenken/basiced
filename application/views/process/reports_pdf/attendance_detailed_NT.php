<?php 

/**
 * @author Angelica
 * @copyright 2017
 *
 */
ini_set('memory_limit', -1);
set_time_limit(0);

$CI =& get_instance();
$CI->load->model('utils');
$CI->load->library('PdfCreator_mpdf');
$userlog = $this->session->userdata("username");
$mpdf = new mPDF('P','LETTER','','UTF-8',5,5,8,5);
$mpdf->simpleTables=true;
$mpdf->packTableData=true;


$content = '';
$style =  $date_tmp = '';


$header = '
<table class="header">
	<tr>
		<td class="align_center" rowspan="3" style="padding-left: 0px" valign="bottom"><img src="'.$imgurl.'images/school_logo.jpg" style="width: 100px;"/></td><br><br>
		<td style="font-size:18px;width: 100%;"><b>Pinnacle Technologies Inc.</b></td>
	</tr>
	<tr>
		<td style="font-size:15px;">D`Great</td>
	</tr>
	<tr>
		<td style="font-size:12px;">'.$datedisplay.' Attendance  </td>
	</tr>
</table><br><br>
';

$mpdf->SetHTMLHeader($header);

$style .= '
<style>
	.container{
		display: block;
		 position:absolute;
		 top: 90px;
	}
	table, .well-content{
		width: 98%;
		font-family:calibri;
	}
	.header, #maincontent th{
		color: black;
	}

	.nosched tr, .nosched td{
		background-color: gray !important;
		color: white !important;
	}

	.table-bordered{
		border: 1px solid #fff;
		border-collapse: collapse;
	    font-size: 10px;
	}

	.table-bordered td, .table-bordered th {
	    border: 0.5px solid #626262;
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
				<div class="well-content container" style="border: transparent !important;"><br>
				<div class="well-content container" style="margin-top:40px;">
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
				            <th rowspan="2" class="align_center">Date</th>
				            <th class="align_center" colspan="2">Actual Log Time</th>
				            <th class="align_center" colspan="3">OT (hr:min)</th>
				            <th class="align_center">Late</th>
				            <th class="align_center">UT</th>
				            <th class="align_center" rowspan="2">Absent</th>
				            <th class="align_center" colspan="3">Leaves</th>
				            <th class="align_center" rowspan="2">Remarks</th>
				            <th class="align_center" rowspan="2">Holiday</th>
				            <th class="align_center" rowspan="2" width="50px;">Total Per day</th>
				        </tr>
				        <tr>
				            <th class="align_center" width="40px;">IN</th>
				            <th class="align_center" width="40px;">OUT</th>
				            <th class="align_center" width="20px;">Reg</th>
				            <th class="align_center" width="20px;">Rest</th>
				            <th class="align_center" width="20px;">Hol</th>
				            <th class="align_center" width="20px;">(hr:min)</th>
				            <th class="align_center" width="20px;">(hr:min)</th>
				            <th class="align_center" width="20px;">VL</th>
				            <th class="align_center" width="20px;">SL</th>
				            <th class="align_center" width="20px;">Other</th>
				        </tr>
				    </thead>
				    <tbody id="employeelist">
				';

				$x = 0;
				$t_otreg = $t_otrest = $t_othol = $t_latelec = $t_utlec = $t_vl = $t_sl = $t_ob = $t_othleave = $t_sc = $t_absent = $t_hol = '';
				$otreg_perday = $otrest_perday = $othol_perday = $late_perday = $ut_lec_perdays = $absent_perdays = $vl_perdays = $sl_perdays = $other_perdays = $total_perday = 0;
				
				$not_included_ol = array("ABSENT", "EL", "VL", "SL", "CORRECTION");
				foreach ($datelist as $date => $att_detail) {
					$isSingleLog = true;
					$log_count = sizeof($att_detail['detail']);
					$ut_lec_perday = 0;
					$lateut_lec_perday = $vl_perday = $sl_perday = $other_perday = $ob_perday = $sc_perday = 0;
					$maximum_absent = 0;
					$absent_perday = 0;
					$lateutlec = $utlec = '';


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

							$late_perday += $CI->attcompute->exp_time($persched_log['lateut_lec']);

							$ut_lec_perdays += $CI->attcompute->exp_time($persched_log['ut_lec']);

							$vl_perday += $persched_log['vl'];
							$sl_perday += $persched_log['sl'];
							$other_perday += $persched_log['other'];
							$sc_perday += $persched_log['service_credit'];

							$ob_perday += $persched_log['ob'];
							if($CI->attcompute->exp_time($persched_log['absent']) >= 14400) $maximum_absent = "4:00";
							$absent_perday += $CI->attcompute->exp_time($maximum_absent);
							$maximum_absent = 0;

						}
					}

					$vl_perday = $vl_perday > 1 ? 1 : $vl_perday;
					$sl_perday = $sl_perday > 1 ? 1 : $sl_perday;
					$other_perday = $other_perday > 1 ? 1 : $other_perday;
					$sc_perday = $sc_perday > 1 ? 1 : $sc_perday;

					$ob_perday = $ob_perday > 1 ? 1 : $ob_perday;

					$t_hol += ($att_detail['isHoliday'] ? 1 : 0);

					$log_counter = 0;
					$pending = $this->attcompute->displayPendingApp($empid,$date);
					$ob_data = $this->attcompute->displayLateUTAbs($empid, $date);
					foreach ($att_detail['detail'] as $key => $persched_log) {
						$log_counter++;
						// if($persched_log['hasleavecount'] && $log_counter == 2) $log_counter -= 1;
						$hasLate = $persched_log['hasLate'];
						$hasUT = $persched_log['hasUT'];
						$logrem = $pending_ob = '';

						$sched = $this->attcompute->displaySched($empid,$date);
						$lateutlec = $this->attcompute->displayLateUTNT($persched_log['sched_start'],$persched_log['sched_end'],$persched_log['login'],$persched_log['logout'],$persched_log['isabsent'],'nonteaching',$sched->row("0")->tardy_start);
						$utlec 	= $this->attcompute->computeUndertimeNT($persched_log['sched_start'],$persched_log['sched_end'],$persched_log['login'],$persched_log['logout'],$persched_log['isabsent'],'nonteaching',$sched->row("0")->tardy_start);
						list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$lnopay)  = $this->attcompute->displayLeave($empid,$date,'',$persched_log['sched_start'],$persched_log['sched_end']);
						if(isset($att_detail['holidayinfo']['description'])){
							$logrem .= $att_detail['holidayinfo']['description'].'<br>'; 
							$absent_perday = 0;
						}
						if($ob_perday){
							$absent_perday = 0;
						}
						if($lateut_lec_perday && !$ut_lec_perday){
			            	if(in_array("late", $ob_data)) $logrem = "EXCUSED LATE<br>";
			            	else{
			            		$logrem = "UNEXCUSED LATE<br>";
								$pending_ob = $this->attcompute->displayPendingOBApp($empid,$date,"late");
			            		$ob_data = array();
			            	}
			            }else if($ut_lec_perday){
							if(in_array("undertime", $ob_data)) $logrem = "EXCUSED UNDERTIME<br>";
				            else{
				            	$logrem = "UNEXCUSED UNDERTIME<br>";
								$pending_ob = $this->attcompute->displayPendingOBApp($empid,$date,"undertime");
			            		$ob_data = array();
				            }
				        }else if($absent_perday){
							if(in_array("absent", $ob_data)) $logrem = "EXCUSED ABSENT<br>";
				            else{
				            	if(strtotime($date) < strtotime($date_tmp)){
				            		$logrem = "UNEXCUSED ABSENT<br>";
			            			$ob_data = array();
				            	}
				            }
				        }
				        if($absent_perday) $logrem = "NO TIME IN AND OUT<br>";

				        $logrem = $persched_log['log_remarks'];

						$date_tmp = $date;
								if(!isset($persched_log["nosched"]) && $persched_log['flexi'] != 'YES'){
$content .= '
										<tr '.$color.'>';
				    						if($log_counter == 1){
				    							if($isSingleLog && $log_count > 1){
				    								foreach ($att_detail['detail'] as $key => $value) {
				    									if($value['hasLate'] == 1) $hasLate = 1;
				    									if($value['hasUT'] == 1) $hasUT = 1;
				    								}
				    							}
$content .= '	    							<td rowspan="'.$log_count.'">'.date("d-M (l)",strtotime($date)).'</td>';
				    						}
$content .= '
				    						<td class="'.( ($persched_log["isAbsent"]?"absent ":'').($hasLate?"late":'') ).'" >'.
				    							(( isset($persched_log['login']) && $persched_log['login'] != '' )?date("h:i A",strtotime($persched_log['login'])):'--').'</td>
				    						<td class="'.( ($persched_log["isAbsent"]?"absent ":'').($hasUT?"late":'') ).'" >'.
				    							(( isset($persched_log['logout']) && $persched_log['logout'] != '' )?date("h:i A",strtotime($persched_log['logout'])):'--').'</td>';

				    						if($log_counter == 1){
				    							
				    							$otreg_perday += $CI->attcompute->exp_time($persched_log['otreg']);
				    							$otrest_perday += $CI->attcompute->exp_time($persched_log['otrest']);
				    							$othol_perday += $CI->attcompute->exp_time($persched_log['othol']);
				    							

$content .= '	    							<td rowspan="'.$log_count.'">'.$persched_log['otreg'].'</td>
				    							<td rowspan="'.$log_count.'">'.$persched_log['otrest'].'</td>
				    							<td rowspan="'.$log_count.'">'.$persched_log['othol'].'</td>';
				    						} 
				    						
				    						
$content .= '		    					<td>'.$lateutlec.'</td>
				    						<td>'.$utlec.'</td>
				    						<td>'.$persched_log['absent'].'</td>';

				    						if($log_counter == 1){
$content .= '		    						<td rowspan="'.$log_count.'">'.$persched_log['vl'].'</td>
				    							<td rowspan="'.$log_count.'">'.$persched_log['sl'].'</td>
				    							<td rowspan="'.$log_count.'"></td>';
				    						}
				    						
				    						if($log_counter == 1){
				    						
$content .= '	    							<td rowspan="'.$log_count.'">
				    								'.$logrem.(($persched_log['ol']) ? (($persched_log['oltype']) ? (($persched_log['oltype'] == 'ABSENT') ? 'ABSENT W/FILE<br>' :  ($lnopay ? $persched_log['oltype']."<br>NO PAY<br>" : $persched_log['oltype'].'<br>')) : $CI->employeemod->othLeaveDesc($persched_log['ol'])."<br>") : $persched_log['cs_app']).$pending_ob.($pending ? "PENDING ".$pending : '').'
				    							</td>';
				    						}

				    						if($log_counter == 1){
$content .= '	    							<td rowspan="'.$log_count.'">'. ((isset($att_detail['holidayinfo']['description'])) ? $att_detail['holidayinfo']['description'] ." - ": "") .''.(isset($att_detail['holidayinfo']['type'])?$att_detail['holidayinfo']['type']:'').'</td>';
				    						}

				    						if($log_counter == 1){
$content .= '	    							<td rowspan="'.$log_count.'">
				    								'.($absent_perday?date('H:i', mktime(0,0,$absent_perday)):'').'
				    							</td>';
				    						}


$content .= '    						</tr>';

						}else{ ///< no sched and flexi------------------------------------------------------------------------------------------------------------------------ 
										#echo "<pre>"; print_r($persched_log);
										$p_logs = $persched_log['logs'];

										// $log_counter = (count($p_logs) > 1) ? 1 : count($p_logs);
    									//$log_count = sizeof($persched_log['logs']);

    									#echo "<pre>"; print_r($persched_log['logs']); die;
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
$content .= '	    									<td class="'.( $persched_log["isAbsent"]?"absent ":'' ).'" >--</td>
				    									<td class="'.( $persched_log["isAbsent"]?"absent ":'' ).'" >--</td>';
				    							}


				    						if($log_counter == 1){
				    							$otreg_perday += $CI->attcompute->exp_time($persched_log['otreg']);
				    							$otrest_perday += $CI->attcompute->exp_time($persched_log['otrest']);
				    							$othol_perday += $CI->attcompute->exp_time($persched_log['othol']);

$content .= '	    							<td rowspan="'.$log_count.'">'.$persched_log['otreg'].'</td>
				    							<td rowspan="'.$log_count.'">'.$persched_log['otrest'].'</td>
				    							<td rowspan="'.$log_count.'">'.$persched_log['othol'].'</td>';
				    						}
				    						$not_other_leave_include = array('VL', 'SL', 'EL', 'SC', 'DIRECT', 'CORRECTION');
				    						$other = (isset($persched_log['other']) && !in_array($persched_log['other'], $not_other_leave_include) && $persched_log['other']) ? 1 : 0;
				    					
$content .= '	    							<td rowspan="'.$log_count.'">'.(isset($persched_log['lateut_lec'])?$persched_log['lateut_lec']:'--').'</td>
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['ut_lec'])?$persched_log['ut_lec']:'--').'</td>
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['absent'])?$persched_log['absent']:'--').'</td>
				    						
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['vl'])?$persched_log['vl']:'--').'</td>
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['sl'])?$persched_log['sl']:'--').'</td>
				    							
				    							<td rowspan="'.$log_count.'">'. (($other) ? $other : '') .'</td>

				    							<!--td rowspan="'.$log_count.'">'.(isset($persched_log['other'])?$persched_log['other']:'--').'</td 
				    							<td rowspan="'.$log_count.'">'. $persched_log['service_credit'].'</td>
				    							<td rowspan="'.$log_count.'">'.(isset($persched_log['ob'])?$persched_log['ob']:'--').'</td> -->';
				    				
				    						/*if($log_counter == 1){*/
$content .= '	    							<td rowspan="'.$log_count.'">
				    								'.( $persched_log['ol'] ? 
				    										($persched_log['oltype'] ? 
				    											($persched_log['oltype'] == 'ABSENT' ? 'ABSENT W/ FILE<br>': ($lnopay ? $persched_log['oltype']."<br>NO PAY<br>" : $persched_log['oltype'].'<br>'))
				    									 	: $CI->employeemod->othLeaveDesc($persched_log['ol'])."<br>" )
				    									: ' ') . 
				    									( $persched_log['cs_app'] )
				    								.'
				    							</td>';
				    						/*}else{
$content .= '	    							<td rowspan="'.$log_count.'"></td>';
				    						}*/

				    					
				    						if($log_counter == 1){
$content .= '	    							<td rowspan="'.$log_count.'">'. ((isset($att_detail['holidayinfo']['description'])) ? $att_detail['holidayinfo']['description'] ." - ": "") .''.(isset($att_detail['holidayinfo']['type'])?$att_detail['holidayinfo']['type']:'').'</td>';
$content .= '	    							<td rowspan="'.$log_count.'">'.$this->attcompute->sec_to_hm($absent_perday).'</td>';
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

   

					}
				} ///< end loop logs

				$t_otreg += $otreg_perday;
				$t_otrest += $otrest_perday;
				$t_othol += $othol_perday;

				$t_latelec += $lateut_lec_perday;

				$t_utlec += $ut_lec_perday;
				$t_absent += $absent_perday;

				$t_vl += $vl_perday;
				$t_sl += $sl_perday;
				$t_othleave += ($other_perday + $other);
				if((isset($att_detail['holidayinfo']['description']))) $holidaytotal++;
				$t_sc += $sc_perday;

				$t_ob += $ob_perday;

			} ///< end loop dates

$content .= '
					
				</tbody>
				<tfoot>
					<tr>
						<th class="align_center" colspan="3"><b>TOTAL</b></th>
					    <th class="align_center">'.($otreg_perday ? $this->attcompute->sec_to_hm($otreg_perday) : "").'</th>
					    <th class="align_center">'.($otrest_perday ? $this->attcompute->sec_to_hm($otrest_perday) : "").'</th>
					    <th class="align_center">'.($othol_perday ? $this->attcompute->sec_to_hm($othol_perday) : "").'</th>
					    <th class="align_center">'.($late_perday ? $this->attcompute->sec_to_hm($late_perday) : "").'</th>
					    <th class="align_center">'.($ut_lec_perdays ? $this->attcompute->sec_to_hm($ut_lec_perdays) : "").'</th>
					    <th class="align_center">'.($t_absent ? $this->attcompute->sec_to_hm($t_absent) : "").'</th>
					    <th class="align_center">'.$t_vl.'</th>
					    <th class="align_center">'.$t_sl.'</th>
					    <th class="align_center">'.$t_othleave.'</th>
					    <th class="align_center"></th>
					    <th class="align_center">'.$holidaytotal.'</th>
					    <th class="align_center">'.($t_absent ? $this->attcompute->sec_to_hm($t_absent) : "").'</th>
					</tr>
				</tfoot>
				</table>


				<br>
					<table>
						<tr>
							<td align="center">Acknowledged by : _________________________ ';
							if($this->extras->findIfAdmin($userlog) == true){
								$content .= 'Certified Correct : _________________________';
							}else{
								$content .= 'Verified by : _________________________';
							}

				$content .= '</tr>
					</table>
				</div>';

				if($empcount != $current_count) $content .= '<pagebreak>';

				$mpdf->WriteHTML($content);
				$content = '';


		} ///< end loop emp
	}

  #die;  			
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
		color: blue;
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
		<td class="align_center" rowspan="2" style="padding:0 20px;" valign="bottom"><img src="images/school_logo3.bmp" style="width: 50px;"/></td>
		<td style="font-size:15px;width: 100%;"><b>CEBU INSTITUTE OF TECHNOLOGY</b></td>
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
            <th class="align_center" colspan="3">OT </th>
            <th class="align_center">Late</th>
            <th class="align_center">UT</th>
            <th class="align_center" rowspan="2">Absent</th>
            <th class="align_center" colspan="3">Leaves</th>
            <th class="align_center" rowspan="2" width="60px;">Service Credit</th>
            <th class="align_center" rowspan="2" >Remarks</th>
            <th class="align_center" rowspan="2" >Holiday</th>
        </tr>
        <tr>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
            <th class="align_center" width="50px;">Reg</th>
            <th class="align_center" width="50px;">Rest</th>
            <th class="align_center" width="50px;">Hol</th>
            <th class="align_center" width="50px;">(hr:min)</th>
            <th class="align_center" width="50px;">(hr:min)</th>
            <th class="align_center" width="50px;">VL</th>
            <th class="align_center" width="50px;">SL</th>
            <th class="align_center" width="50px;">Other</th>
        </tr>
    </thead>
    <tbody id="employeelist">

    	<? foreach ($attendance_list as $deptid => $emplist) { 
    			foreach ($emplist as $empid => $datelist) {
    				foreach ($datelist as $date => $att_detail) {


    					$isSingleLog = true;
    					$log_count = sizeof($att_detail['detail']);
    					$lateut_lec_perday = $vl_perday = $sl_perday = $other_perday = $sc_perday = 0;
    					$ut_lec_perday = 0;
    					$absent_perday = 0;

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

    							$ut_lec_perday += $CI->attcompute->exp_time($persched_log['ut_lec']);

    							$vl_perday += $persched_log['vl'];
    							$sl_perday += $persched_log['sl'];
    							$other_perday += $persched_log['other'];
    							$sc_perday += $persched_log['service_credit'];

    							$absent_perday += $CI->attcompute->exp_time($persched_log['absent']);
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
    								if(!isset($persched_log["nosched"]) && $persched_log['flexi'] != 'YES'){

    						?>

				    					<tr>
				    						<? if($log_counter == 1){ 
						    						if($isSingleLog && sizeof($att_detail['detail']) > 1){
														foreach ($att_detail['detail'] as $key => $value) {
															if($value['hasLate'] == 1) $hasLate = 1;
															if($value['hasUT'] == 1) $hasUT = 1;
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
					    							<td><?=$persched_log['ut_lec']?></td>
					    							<td><?=$persched_log['absent']?></td>
				    						<? }elseif($isSingleLog && $log_counter == 1){ ?>
				    								<td><?=$lateut_lec_perday?date('H:i', mktime(0,0,$lateut_lec_perday)):''?></td>
				    								<td><?=$ut_lec_perday?date('H:i', mktime(0,0,$ut_lec_perday)):''?></td>
				    								<td><?=$absent_perday?date('H:i', mktime(0,0,$absent_perday)):''?></td>
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
				    								<td><?=$sc_perday?number_format($ob_perday,2):''?></td>
				    						<? } 

				    						if($log_counter == 1){ ?>
				    							<td rowspan="<?=$isSingleLog?1:$log_count?>">
				    								<?=( $persched_log['ol'] ? ($persched_log['oltype'] ? ($persched_log['oltype'] == 'ABSENT' ? 'ABSENT W/ FILE<br>'
				    									 : ($lnopay ? $persched_log['oltype']."<br>NO PAY<br>" : $persched_log['oltype'].'<br>'))
				    									 : $CI->employeemod->othLeaveDesc($persched_log['ol'])."<br>" )
				    									 : '') . 
				    									 ( $persched_log['cs_app'] )

				    								?>
				    							</td>
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
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['ut_lec'])?$persched_log['ut_lec']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['absent'])?$persched_log['absent']:'--')?></td>
				    						
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['vl'])?$persched_log['vl']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['sl'])?$persched_log['sl']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=(isset($persched_log['other'])?$persched_log['other']:'--')?></td>
				    							<td rowspan="<?=$log_count?>"><?=$persched_log['service_credit']?></td>
				    							<td rowspan="<?=$log_count?>"><?=$persched_log['ob']?></td>
				    				
				    						<? if($log_counter == 1){ ?>
				    							<td rowspan="<?=$log_count?>">
				    								<?=( $persched_log['ol'] ? ($persched_log['oltype'] ? ($persched_log['oltype'] == 'ABSENT' ? 'ABSENT W/ FILE<br>'
				    									 : ($lnopay ? $persched_log['oltype']."<br>NO PAY<br>" : $persched_log['oltype'].'<br>'))
				    									 : $CI->employeemod->othLeaveDesc($persched_log['ol'])."<br>" )
				    									 : '') . 
				    									 ( $persched_log['cs_app'] )

				    								?>
				    							</td>
				    						<? }

				    					
				    						if($log_counter == 1){ ?>
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