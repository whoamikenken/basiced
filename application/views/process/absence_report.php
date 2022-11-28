<?php
/**
 * @author Melvin Cobar Empleo
 * @copyright 2014
 */

// variables...
$dateRange = "";
$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$dept = $deptid;

$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$departments = $this->extras->showdepartment();
$result = $this->attendance->giveAttendanceSummary($from_date, $to_date, $empid, $dept);
?>
<h2>Absence Report</h2>
<p><?php print($dateRange); ?></p>
<p><?php print(($dept != "") ? $departments[$dept] : "" ); ?></p>
<div class="well-content">
    <table class="table table-bordered table-hover table-striped datatable">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Whole day</th>
                <!--<th>Half day</th>-->
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
<?php
    if (count($result) > 0) {
        $empdept = "";
        foreach ($result as $key => $datum) {
            $theId = $datum["qEmpId"];
            $theName = $datum["qFullname"];
            $arrAbsenceHalf = array();
            $arrAbsenceWhole = array();

            $empsumm = $this->attendance->giveIndividualSummary($from_date, $to_date, $theId);
            foreach ($empsumm as $key => $summ) {
                if ($summ["queTotalAbsentToday"] == .5) {
                    array_push($arrAbsenceHalf, $summ["queTotalAbsentToday"]);
                }

                if ($summ["queTotalAbsentToday"] > .5) {
                    array_push($arrAbsenceWhole, $summ["queTotalAbsentToday"]);
                }
            }
            $sumAbsenceHalf = array_sum($arrAbsenceHalf);
            $sumAbsenceWhole = array_sum($arrAbsenceWhole);

            if ($empdept != $datum["qDepartment"]) {
?>                
            <tr><td colspan="5"><strong><?php print($datum["qDepartment"]); ?></strong></td> </tr>
<?php
            }// end if
?>
            <tr>
                <td><?php print($theId); ?></td>
                <td><?php print($theName); ?></td>
                <td><?php print(($sumAbsenceWhole > 0) ? $sumAbsenceWhole : ""); ?></td>
                <!--<td><?php print((count($arrAbsenceHalf) > 0) ? count($arrAbsenceHalf) : ""); ?></td>-->
                <td><?php print((($sumAbsenceHalf + $sumAbsenceWhole) > 0) 
                    ? ($sumAbsenceHalf + $sumAbsenceWhole) : ""); ?></td>
            </tr>
<?php
            $empdept = $datum["qDepartment"]; // set current department
        }// end foreach
    }else{
?>
        <tr class="error"><td colspan="5">NO RECORD FOUND</td></tr>
<?php
    }// end else
?>            
        </tbody>
    </table>
</div>