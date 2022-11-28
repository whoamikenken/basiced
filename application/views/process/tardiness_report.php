<?php

$dateRange = "";
$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$dept = $deptid;

$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$departments = $this->extras->showdepartment();
$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept);

?>
<h2>Tardiness Report</h2>
<p><?php print($dateRange); ?></p>
<p><?php print(($dept != "") ? $departments[$dept] : "" ); ?></p>
<div class="well-content">
	<table class="table table-bordered table-hover table-striped datatable">
		<thead>
			<tr>
				<th>Employee ID</th>
				<th>Employee Name</th>
				<th>No. of Late(s)</th>
				<th>Total Time</th>
			</tr>
		</thead>
		<tbody>
<?php
	if (count($result) > 0) {
		$empDept = "";
		foreach ($result as $key => $datum) {
			$theId = $datum["qEmpId"];
			$arrLates = array();
			$empsumm = $this->attendance->giveIndividualSummary($from_date, $to_date, $theId);
			foreach ($empsumm as $key => $summ) {
				if (($this->time->hoursToMinutes($summ["queTotalLateToday"])) > 0) {
					array_push($arrLates, ($this->time->hoursToMinutes($summ["queTotalLateToday"])));
				}
			}//end foreach
			$sumlate = explode(":", $this->time->minutesToHours(array_sum($arrLates)));
			$hr = (intval($sumlate[0]) > 0) ? intval($sumlate[0]) . ((intval($sumlate[0]) > 1) ? "hrs ":"hr "):"";
			$min = (intval($sumlate[1]) > 0) ? intval($sumlate[1]) . ((intval($sumlate[1]) > 1) ? "mins":"min"):"";

			if ($empDept != $datum["qDepartment"]) {
?>
				<tr><td colspan="4"><strong><?php print($datum["qDepartment"]); ?></strong></td></tr>
<?php				
			}// end if
?>
			<tr>
				<td class="align-center"><?php print($datum["qEmpId"]); ?></td>
				<td class="align-center"><?php print($datum["qFullname"]); ?></td>
				<td class="align-center"><?php print(count($arrLates)); ?></td>
				<td class="align-center"><?php print($hr.$min); ?></td>
			</tr>
<?php
			$empDept = $datum["qDepartment"];
		}// end foreach
	}else{?>
		<tr class="error"><td colspan="4">NO RECORDS FOUND</td></tr>
<?php
	} // END ELSE
?>			
		</tbody>
	</table>
</div>