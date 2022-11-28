<?php

if(!$this->input->get("cdate"))die;
$dex = explode(",",$this->input->get("cdate"));
$dateRange = "";
$from_date = $dex[0];
$to_date = $dex[1];
$edata = "timesheet";
$empid = "";
$tnt = $this->input->get("tnt");
$estatus = "";
$dept = "";
$deptids = $this->input->get('deptids');
$campus = $this->input->get('campus');
$employeeid = $this->input->get("employeeid");
$user = $this->session->userdata('username');
// echo $user;die;
$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$departments = $this->extras->showdepartment();
$result = $this->attendance->emp_confirmedperdept($from_date, $to_date, $tnt,$employeeid,$deptids,$campus);
$showfinalize = $this->employeemod->showFinalize($from_date,$to_date,$tnt);
$getname = $this->utils->getFullname($user);
$mpdf = new mPDF('utf-8','A4-L','10','','3','3','33','10','9','9');
$mpdf->SetHTMLHeader("<table class='header'>
		<tr>
		<td><img src='images/school_logo.jpg' style='width: 80px;'/></td>
		<td style='color:blue;'>
			<b style='align_center'><h1>Pinnacle Technologies Inc.</h1></b>
			<b>Attendance Confirmed </b>
			<p style='color:black;'>&nbsp;$dateRange</p>
		</td>
		</tr>
		</table>",'',false);


$content = '';
if($tnt == "teaching"){
	$content .= '	<div id="attstbl" class="well_content">
				  		<table class="" width="100%" id="asctblnt" border=1>
						  	<thead>
						  		<tr>
						  			<th class="sorting_asc" rowspan="2">Employee ID</th>
						  			<th rowspan="2" width="30%">Name</th>
						  			<!-- <th rowspan="2">Overload</th>-->
						            <th class="align_center" colspan="3">Overtime (hr:min)</th>
						            <th class="align_center" rowspan="2">Late (hr:min)</th>
						            <th class="align_center" rowspan="2">Undertime (hr:min)</th>
						            <!--<th class="align_center" rowspan="2">Absent</th>-->
						            <th class="align_center" colspan="3">Leaves</th>
						            <!-- <th class="align_center" rowspan="2" >Remarks/Others</th> -->
			          				<th  rowspan="2">Total Deduction (hr:min)</th>
						            <th class="align_center" rowspan="2">Total Deduction (day/s)</th>
						            <th rowspan="2" width="8%">Signature</th>
						            <!--<th class="align_center" colspan="3">Work Hours</th>-->
						        </tr>
						        <tr>
						            <th class="align_center">Regular</th>
						            <th class="align_center">Rest Day</th>
						            <th class="align_center">Holiday</th>
						            <!--<th class="align_center">Lec</th>
						            <th class="align_center">Lab</th>
						            <th class="align_center">Admin</th>
						            <th class="align_center">Lec</th>
						            <th class="align_center">Lab</th>
						            <th class="align_center">Admin</th>-->
						            <!--<th class="align_center">Subject</th>-->
						            <!--<th class="align_center">Emergency</th>-->
						            <th class="align_center">Vacation</th>
						            <th class="align_center">Sick</th>
						            <th class="align_center">Other(s)</th>
						            <!--<th class="align_center">Lec</th>
						            <th class="align_center">Lab</th>
						            <th class="align_center">Admin</th>
						            <th class="align_center">Lec</th>
						            <th class="align_center">Lab</th>
						            <th class="align_center">Admin</th>-->
						        </tr>
						  	</thead>
						  	<tbody>';

	if (count($result) > 0) {
		$deptDisplay = "";
		foreach ($result as $key => $data) {
			// echo '<pre>';print($data);
			$empid = $data["qEmpId"];
			$empFullname = $data["qFullname"];
	        $deptid = $this->employee->getindividualdept($empid);
	        $overload = $data["overload"];
	        $totr = $data["otreg"];
	        $totrest = $data["otrest"];
	        $tothol = $data["othol"]; 
	        $tlec = $data["latelec"];
	        $tlab = $data["latelab"];
	        $tadmin = $data["lateadmin"];
	        $tutlec = $data["utlec"];
	        $tutlab = $data["utlab"];
	        $tutadmin = $data["utadmin"];
	        $tabsent = $data["absent"]; 
	        $tel = $data["eleave"];
	        $tvl = $data["vleave"];
	        $tsl = $data["sleave"];
	        $tol = $data["oleave"];         
	        $tdlec = $data["deduclec"];
	        $tdlab = $data["deduclab"];
	        $tdadmin = $data["deducadmin"];
	        $workhours_lec = $data["workhours_lec"];
	        $workhours_lab = $data["workhours_lab"];
	        $workhours_admin = $data["workhours_admin"];
	        $fixedday = $data["fixedday"];


	        /** HYPERION21587 **/
	        /** PAULO04-26-2018 **/
	        //Service Credit 
	        $service_credit = array();
	        $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
	        foreach ($qdate as $rdate) {
				$sched = $this->attcompute->displaySched($empid,$rdate->dte);
				$countrow = $sched->num_rows();
				$isValidSchedule = true;
				if($countrow > 0){
			    	if($sched->row(0)->starttime == "00:00:00" && $sched->row(0)->endtime == "00:00:00") $isValidSchedule = false;
			    }
			    if($countrow > 0 && $isValidSchedule){
			    	foreach($sched->result() as $rsched){
				        $stime = $rsched->starttime;
				        $etime = $rsched->endtime; 
						$service_credit[] = $this->attcompute->displayServiceCredit($data["qEmpId"],$stime,$etime,$rdate->dte);
			    	}
			    }
	        }
	        /** END OF HYPERION21587 **/

	        $totUndertime = $this->attcompute->exp_time($data['utadmin']) + $this->attcompute->exp_time($data['utlec']) + $this->attcompute->exp_time($data['utlab']);
	        $totLate = $this->attcompute->exp_time($data['latelec']) + $this->attcompute->exp_time($data['latelab']) + $this->attcompute->exp_time($data['lateadmin']);
	        $totDeduction = $this->attcompute->exp_time($data['deducadmin']) + $this->attcompute->exp_time($data['deduclec']) + $this->attcompute->exp_time($data['deduclab']);
	        $totDeduction = $totDeduction / (8 * 3600);
	    	if ($deptDisplay != $data["qDepartment"]) {
				$content .= '	<tr class="pdept">
									<td colspan="8"><b><p>Department: '.$data["qDepartment"].'</p></b></td>
								</tr>';
			
			} # > end if condition

			$content .= '		<tr class="pdata">
							        <td class="pdataid">'. $empid .'</td>
							        <td>'. $empFullname .'</td>
							        <!--  <td>'. $overload .'</td>-->
							        <td class="align_center">'. $totr .'</th>
							        <td class="align_center">'. $totrest .'</th>
							        <td class="align_center">'. $tothol .'</th>
							        <td class="align_center">'. $this->attcompute->sec_to_hm($totLate) .'</td>
							        <td class="align_center">'.  $this->attcompute->sec_to_hm($totUndertime).'</td>
							        <!--<td class="align_center">'. $tadmin .'</td>
							        <td class="align_center">'. $tutlec .'</td>
							        <td class="align_center">'. $tutlab .'</td>
							        <td class="align_center">'. $tutadmin .'</td>
							        <td class="align_center">'. $tabsent .'</td>-->
							        <!--<td class="align_center">'. $tel .'</td>-->
							        <td class="align_center">'. $tvl .'</td>
							        <td class="align_center">'. $tsl .'</td>
							        <td class="align_center">'. $tol .'</td>
							        <!-- <td class="align_center">'. $tol .'</td> 
							        <td class="align_center">'. $tdlec .'</td>
							        <td class="align_center">'. $tdlab .'</td>-->
			  		        		<td align="center">'.array_sum($service_credit).'</td>
			  		        		<td class="align_center">'. $totDeduction.'</td>
							        <!--<td class="align_center">'. $this->attcompute->sec_to_hm($totDeduction).'</td>-->
							        <!--<td class="align_center">'. (($fixedday) ? '' :$workhours_lec) .'</td>
							        <td class="align_center">'. (($fixedday) ? '' :$workhours_lab) .'</td>
							        <td class="align_center">'. (($fixedday) ? '' :$workhours_admin) .'</td>-->
							        <td></td>
								</tr>';

			$deptDisplay = $data["qDepartment"];
		} # > end of foreach 
	} # > end if condition for count result
	if (count($result) > 0) {
		
	$content .= '<tfoot>
				<tr class="bordertop">
					<td>&nbsp;</td>
				</tr>
				<tr class="bordernone">
					<td> &nbsp; </td>
				</tr>
				<tr class="bordernone">
					<td> &nbsp; </td>
				</tr>
				<tr class="bordernone">
					<td> &nbsp; </td>
				</tr>
				<tr class="bordernone">
					<td colspan="2">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Acknowledge By:'.str_repeat('_',30 ).'  </td>
				</tr>
				<tr class="bordernone">
					<td colspan="2" align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    '.$getname.'  </td>
				</tr>
				</tfoot>';
	}

	$content .='</table>
				</div>';
					
}else{
	$content .= '	<div id="attstbl" class="well_content">
				      <table class="" width="100%" id="asctblnt" border=1>
				        <thead>
				            <tr>
				                <th class="sorting_asc" rowspan="2" >Employee ID</th>
				  			    <th rowspan="2" style="width:17.5%">Name</th>
								<!--<th rowspan="2">Overload</th>-->
				                <th class="align_center" colspan="3"  style="width:15%">Overtime (hr:min)</th>
				                <th class="align_center" style="width:7.5%">Late</th>
				                <th class="align_center" style="width:7.5%">Undertime</th>
				                <th class="align_center" rowspan="2" style="width:7.5%">Absent</th>                        
				                <th class="align_center" colspan="3" style="width:15%">Leaves</th>
				                <!-- <th class="align_center" rowspan="2" >Remarks/Others</th> -->
				                <th class="align_center" rowspan="2"  style="width:10%">No. of Days</th>
				                <th class="align_center" rowspan="2"  style="width:10%">Holiday</th>
				                <th rowspan="2" style="width:8%">Signature</th>
				            </tr>
				            <tr>
				                <th class="align_center">Regular</th>
				                <th class="align_center">Rest Day</th>
				                <th class="align_center">Holiday</th>
				                <th class="align_center">Hr:min</th>            
				                <th class="align_center">Hr:min</th>            
				                <th class="align_center">Emergency</th>
				                <th class="align_center">Vacation</th>
				                <th class="align_center">Sick</th>
				            </tr>
				        </thead>
				      	<tbody>';
	$result = $this->attendance->emp_confirmed_ntperdept($from_date, $to_date, $tnt,$employeeid,$deptids);
	if (count($result) > 0) {
    	$deptDisplay = "";
    	foreach ($result as $key => $data) {
    		$empid = $data["qEmpId"];
    		$empFullname = $data["qFullname"];
            $overload = $data["overload"];
            $totr = $data["otreg"];
            $totrest = $data["otrest"];
            $tothol = $data["othol"]; 
            $tlec = $data["lateut"];
            $tutlec = $data["ut"];
            $tabsent = $data["absent"];
            $tel = $data["eleave"];
            $tvl = $data["vleave"];
            $tsl = $data["sleave"];
            $tol = $data["oleave"];         
            $ishol = $data['isholiday'];
            $workdays = $data['workdays'];
            $fixedday = $data['fixedday'];
            if ($deptDisplay != $data["qDepartment"]) {
            	$content .= '	<tr class="pdept">
            						<td colspan="14">
            							<b><p>Department: '. $data["qDepartment"] .'</p></b>
            						</td>
            					</tr>';
            } # > end if condition

            $content .= '		<tr class="pdata">
						    		<td class="pdataid">'.$empid .'</td>
						    		<td>'.$empFullname .'</td>
						    		<!--<td>'.$overload .'</td>-->
						            <td class="align_center">'.$totr .'</th>
						            <td class="align_center">'.$totrest .'</th>
						            <td class="align_center">'.$tothol .'</th>
						            <td class="align_center">'.$tlec .'</th>
						            <td class="align_center">'.$tutlec .'</th>
						            <td class="align_center">'.$tabsent .'</th>
						            <td class="align_center">'.$tel .'</th>
						            <td class="align_center">'.$tvl .'</th>
						            <td class="align_center">'.$tsl .'</th>
						            <!-- <td class="align_center">'.$tol .'</th> -->
						            <td class="align_center">'. (($fixedday)?'':$workdays) .'</th>
						            <td class="align_center">'.$ishol .'</th>
						            <td></td>
						    	</tr>';
        
      		$deptDisplay = $data["qDepartment"];
        } # > end of foreach 
    } # > end if condition for count result
	$content .= '		</tbody>';
	$content .= '<tfoot>
				<tr class="bordertop">
					<td>&nbsp;</td>
				</tr>
				<tr class="bordernone">
					<td> &nbsp; </td>
				</tr>
				<tr class="bordernone">
					<td> &nbsp; </td>
				</tr> 
				<tr class="bordernone">
					<td> &nbsp; </td>
				</tr>
				<tr class="bordernone">
					<td colspan="3">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acknowledge By:'.str_repeat('_',30 ).'  </td>
				</tr>
				<tr class="bordernone">
					<td colspan="3" align="center">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; '.$getname.'  </td>
				</tr>
				</tfoot>';

	$content .='</table>
				</div>';
					
}

# =================================================== Displayed Here =================================
$html = "
		<style>
		#attstbl tr th,#asctblnt tr th{
		    background-color: #3C8DBC;
		    color: white;
		   

		}
		#asctblnt
		{
			 border-collapse: collapse;
			 border-width:thin;
		}
		.header
		{
		 
		 position:absolute;
		 font-size:12px;
		 font-family:calibri;
		}
		thead{
		 font-size:8px;
		}
		#attstbl tr th,#asctblnt tr th{
	    	background-color: #3b5998;
	    	color: #FFF;
	    	font-size : 10px;
		}
		.align_center{
			text-align: center;
		}
		td {
			font-size : 10px;
		}
		.bordernone{
			border-style:hidden
			border:hidden;
		}
		.bordertop{
			border-style:hidden
			border:hidden;
			border-top-style:solid;
		}
		</style>
		<body>".$content."</body>
		";

$mpdf->WriteHTML($html);
$mpdf->Output();
die;
?>

<!-- ++++++++++++++++++++++++++++++++++++ END FILE ++++++++++++++++++++++++++++++++++++ -->

<?php  
/**
* @author Justin (with e)
* @copyright 2018
*/

if(!$this->input->get("cdate"))die;
$dex = explode(",",$this->input->get("cdate"));
$dateRange = "";
$from_date = $dex[0];
$to_date = $dex[1];
$edata = "timesheet";
$empid = "";
$tnt = $this->input->get("tnt");
$estatus = "";
$dept = "";
$deptids = $this->input->get('deptids');
$campus = $this->input->get('campus');
$employeeid = $this->input->get("employeeid");
$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$departments = $this->extras->showdepartment();
$result = $this->attendance->emp_confirmedperdept($from_date, $to_date, $tnt,$employeeid,$deptids,$campus);
$showfinalize = $this->employeemod->showFinalize($from_date,$to_date,$tnt);
?>
<h2>Attendance Confirmed</h2>
<p><?=$dateRange?></p>
<?
  if ($dept != "") {
    print("<p>Department: ".$departments[$dept]."</p>");
  }
?>
<style>
	#attstbl tr th,#asctblnt tr th{
    	background-color: #3b5998;
    	color: #FFF;
	}
</style>
<?if($tnt == "teaching"){ // Teaching?>
<div id="attstbl" class="well_content">
  <table class="table table-striped table-bordered table-hover datatable" width="100%" id="asctblnt">
  	<thead>
  		<tr>
  			<th class="sorting_asc" rowspan="2">Employee ID</th>
  			<th rowspan="2">Name</th>
  			<th rowspan="2">Overload</th>
            <th class="align_center" colspan="3">Overtime (hr:min)</th>
            <th class="align_center" colspan="3">Late (hr:min)</th>
            <th class="align_center" colspan="3">Undertime (hr:min)</th>
            <th class="align_center" >Absent</th>
            <th class="align_center" colspan="3">Leaves</th>
            <!-- <th class="align_center" rowspan="2" >Remarks/Others</th> -->
            <th class="align_center" colspan="3">Total Deduction</th>
            <th class="align_center" colspan="3">Work Hours</th>
        </tr>
        <tr>
            <th class="align_center">Regular</th>
            <th class="align_center">Rest Day</th>
            <th class="align_center">Holiday</th>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
            <th class="align_center">Admin</th>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
            <th class="align_center">Admin</th>
            <th class="align_center">Subject</th>
            <th class="align_center">Emergency</th>
            <th class="align_center">Vacation</th>
            <th class="align_center">Sick</th>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
            <th class="align_center">Admin</th>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
            <th class="align_center">Admin</th>
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
        $overload = $data["overload"];
        $totr = $data["otreg"];
        $totrest = $data["otrest"];
        $tothol = $data["othol"]; 
        $tlec = $data["latelec"];
        $tlab = $data["latelab"];
        $tadmin = $data["lateadmin"];
        $tutlec = $data["utlec"];
        $tutlab = $data["utlab"];
        $tutadmin = $data["utadmin"];
        $tabsent = $data["absent"]; 
        $tel = $data["eleave"];
        $tvl = $data["vleave"];
        $tsl = $data["sleave"];
        $tol = $data["oleave"];         
        $tdlec = $data["deduclec"];
        $tdlab = $data["deduclab"];
        $tdadmin = $data["deducadmin"];
        $workhours_lec = $data["workhours_lec"];
        $workhours_lab = $data["workhours_lab"];
        $workhours_admin = $data["workhours_admin"];
        $fixedday = $data["fixedday"];

if ($deptDisplay != $data["qDepartment"]) {
?>
    <tr class="pdept"><td colspan="13"><b><?="<p>Department: ".$data["qDepartment"]."</p>"?></b></td></tr>
<?
}
?>
    <tr class="pdata">
        <td class="pdataid"><?=$empid?></td>
        <td><?=$empFullname?></td>
        <td><?=$overload?></td>
        <td class="align_center"><?=$totr?></th>
        <td class="align_center"><?=$totrest?></th>
        <td class="align_center"><?=$tothol?></th>
        <td class="align_center"><?=$tlec?></td>
        <td class="align_center"><?=$tlab?></td>
        <td class="align_center"><?=$tadmin?></td>
        <td class="align_center"><?=$tutlec?></td>
        <td class="align_center"><?=$tutlab?></td>
        <td class="align_center"><?=$tutadmin?></td>
        <td class="align_center"><?=$tabsent?></td>
        <td class="align_center"><?=$tel?></td>
        <td class="align_center"><?=$tvl?></td>
        <td class="align_center"><?=$tsl?></td>
        <!-- <td class="align_center"><?=$tol?></td> -->
        <td class="align_center"><?=$tdlec?></td>
        <td class="align_center"><?=$tdlab?></td>
        <td class="align_center"><?=$tdadmin?></td>
        <td class="align_center"><?=$fixedday?'':$workhours_lec?></td>
        <td class="align_center"><?=$fixedday?'':$workhours_lab?></td>
        <td class="align_center"><?=$fixedday?'':$workhours_admin?></td>
        
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
				<!--<th rowspan="2">Overload</th>-->
                <th class="align_center" colspan="3">Overtime (hr:min)</th>
                <th class="align_center">Late</th>
                <th class="align_center">Undertime</th>
                <th class="align_center" rowspan="2">Absent</th>                        
                <th class="align_center" colspan="3">Leaves</th>
                <!-- <th class="align_center" rowspan="2" >Remarks/Others</th> -->
                <th class="align_center" rowspan="2" >No. of Days</th>
                <th class="align_center" rowspan="2" >Holiday</th>
            </tr>
            <tr>
                <th class="align_center">Regular</th>
                <th class="align_center">Rest Day</th>
                <th class="align_center">Holiday</th>
                <th class="align_center">Hr:min</th>            
                <th class="align_center">Hr:min</th>            
                <th class="align_center">Emergency</th>
                <th class="align_center">Vacation</th>
                <th class="align_center">Sick</th>
            </tr>
        </thead>
      	<tbody>
    <?
    $result = $this->attendance->emp_confirmed_ntperdept($from_date, $to_date, $tnt,$employeeid,$deptids);

    if (count($result) > 0) {
    	$deptDisplay = "";
    	foreach ($result as $key => $data) {
    		$empid = $data["qEmpId"];
    		$empFullname = $data["qFullname"];
            $overload = $data["overload"];
            $totr = $data["otreg"];
            $totrest = $data["otrest"];
            $tothol = $data["othol"]; 
            $tlec = $data["lateut"];
            $tutlec = $data["ut"];
            $tabsent = $data["absent"];
            $tel = $data["eleave"];
            $tvl = $data["vleave"];
            $tsl = $data["sleave"];
            $tol = $data["oleave"];         
            $ishol = $data['isholiday'];
            $workdays = $data['workdays'];
            $fixedday = $data['fixedday'];

  		if ($deptDisplay != $data["qDepartment"]) {
    ?>
    	<tr class="pdept"><td colspan="14"><b><?="<p>Department: ".$data["qDepartment"]."</p>"?></b></td></tr>
        <?}?>
    	<tr class="pdata">
    		<td class="pdataid"><?=$empid?></td>
    		<td><?=$empFullname?></td>
    		<!--<td><?=$overload?></td>-->
            <td class="align_center"><?=$totr?></th>
            <td class="align_center"><?=$totrest?></th>
            <td class="align_center"><?=$tothol?></th>
            <td class="align_center"><?=$tlec?></th>
            <td class="align_center"><?=$tutlec?></th>
            <td class="align_center"><?=$tabsent?></th>
            <td class="align_center"><?=$tel?></th>
            <td class="align_center"><?=$tvl?></th>
            <td class="align_center"><?=$tsl?></th>
            <!-- <td class="align_center"><?=$tol?></th> -->
            <td class="align_center"><?=$fixedday?'':$workdays?></th>
            <td class="align_center"><?=$ishol?></th>
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
<!-- <?if($showfinalize){?>
    <div class="pull-right">
    <span id="cmsg" style="color: red;font-weight: bold;"></span>
        <input type="button" id="finalize" class="btn btn-primary" value="Finalize" style="cursor: pointer;" />
    </div>
<?}?> -->
<script>
$("#finalize").click(function(){
    $('.pdata').each(function() {
        var eid = $(this).find(".pdataid").text();
        $.ajax({ 
            url      : "<?=site_url("employeemod_/loadmodelfunc")?>",
            type     : "POST",
            data     : {
                            model: "payrollconfirm",
                            tnt  : "<?=$tnt?>",
                            dfrom: "<?=$from_date?>",
                            dto  : "<?=$to_date?>",
                            eid  : eid
                        },
            success  : function(msg){
                var data = $.parseJSON(msg);
                if(data[0])  $(".pdata,.pdept").remove();    
                $("#finalize").hide();
                $("#cmsg").text(data[1]);
                
            }
        });
    });
});
</script>


