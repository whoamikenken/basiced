<?
/**
* @author justin (with e)
* @copyright 2018
*/

list($from_date, $to_date) = explode(",", $cdate);
$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);

$function = ($tnt == "teaching") ? "emp_confirmedperdept" : "emp_confirmed_ntperdept";
$result = $this->attendance->{$function}($from_date, $to_date, $tnt, $employeeid, $deptids, $campus);
$old_campus = "";
$old_department = "";
$departments = $this->extras->showdepartment();
?>

<h2>Attendance Confirmed</h2>
<p><?=$dateRange?></p>
<table class="table table-striped table-bordered table-hover datatable" id="asctblnt">
    <?if ($tnt == "teaching"): // <<< teaching?>
        <thead>
            <tr>
                <th class="sorting_asc" rowspan="2">Employee ID</th>
                <th rowspan="2">Name</th>
                <th class="align_center" rowspan="2">Late/Undertime Deduction</th>
                <th class="align_center" colspan="3">Leaves</th>
                <th class="align_center" rowspan="2">Deduction total day/s</th>
            </tr>
            <tr>
                <th class="align_center">Vacation</th>
                <th class="align_center">Sick</th>
                <th class="align_center">Other(s)</th>
            </tr>
        </thead>
    <tbody>
        <?
        foreach ($result as $key => $data):
            $empid = $data["qEmpId"];
            $empFullname = $data["qFullname"];
            $deptid = $this->employee->getindividualdept($empid);
            $campusid = $data["campusid"];
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
            $tsc = $data["scleave"];         
            $tdlec = $data["deduclec"];
            $tdlab = $data["deduclab"];
            $tdadmin = $data["deducadmin"];
            $deducperday = $data["deducperday"];
            $workhours_lec = $data["workhours_lec"];
            $workhours_lab = $data["workhours_lab"];
            $workhours_admin = $data["workhours_admin"];
            $fixedday = $data["fixedday"];
            $totUndertime = $this->attcompute->exp_time($data['utadmin']) + $this->attcompute->exp_time($data['utlec']) + $this->attcompute->exp_time($data['utlab']);
            $totLate = $this->attcompute->exp_time($data['latelec']) + $this->attcompute->exp_time($data['latelab']) + $this->attcompute->exp_time($data['lateadmin']);
            $totDeduction = $this->attcompute->exp_time($data['deducperday']);
            $totDeduction = $this->attcompute->sec_to_hm($totDeduction);

            if($old_department != $deptid):
                $old_department = $deptid;
        ?>
        <tr class="pdata">
            <td colspan="12">
                <strong>
                    <?=$departments[$deptid]?>
                </strong>
            </td>
        </tr>
        <?
            endif;
        ?>
        <?php if($campusid != $old_campus){ ?>
            <tr class="pdata">
                <td colspan="12">
                    <strong>
                        <?=$campusid?>
                    </strong>
                </td>
            </tr>
        <?php }else if(!$campusid){ ?>
         <tr class="pdata">
                <td colspan="12">
                    <strong>
                         No Campus
                    </strong>
                </td>
            </tr>
        <?php } ?>
            <tr class="pdata">
                <td class="align_center"><?=$key?></td>
                <td class="align_center"><?=$data['fullname']?></td>
                <td class="align_center"><?=$data['totdeduc']?></th>
                <td class="align_center"><?=$data['vleave']?></td>
                <td class="align_center"><?=$data['sleave']?></td>
                <td class="align_center"><?=$data['oleave']?></td>
                <td class="align_center"><?=$data['totDeduction']?></td>
            </tr>
        <?php $old_campus = $campusid ?>
        <?
        endforeach;
        ?>
    </tbody>
    <?else: // <<< nonteaching?>
    <thead>
        <tr>
            <th width="10%" class="sorting_asc" rowspan="2">Employee ID</th>
            <th rowspan="2">Name</th>
            <th class="align_center" colspan="3">Overtime (hr:min)</th>
            <th class="align_center">Late</th>
            <th class="align_center">Undertime</th>
            <th width="06.25%" class="align_center" rowspan="2">Absent</th>                        
            <th class="align_center" colspan="4">Leaves</th>
            <th width="06.25%" class="align_center" rowspan="2" >No. of Days</th>
            <th width="06.25%" class="align_center" rowspan="2" >Holiday</th>
        </tr>
        <tr>
            <th width="06.25%" class="align_center">Regular</th>
            <th width="06.25%" class="align_center">Rest Day</th>
            <th width="06.25%" class="align_center">Holiday</th>
            <th width="06.25%" class="align_center">Hr:min</th>            
            <th width="06.25%" class="align_center">Hr:min</th>            
            <th width="06.25%" class="align_center">VL</th>
            <th width="06.25%" class="align_center">SL</th>
            <th width="06.25%" class="align_center">Other</th>
            <th width="06.25%" class="align_center">Service Credit</th>
        </tr>        
    </thead>
    <tbody>
        <?
        foreach ($result as $key => $data):
            $empid = $data["qEmpId"];
            $deptid = $this->employee->getindividualdept($empid);
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
            $tsc = $data["scleave"]; 
            $tsl = $tsl + $tol;   
            $ishol = $data['isholiday'];
            $workdays = $data['workdays'];
            $fixedday = $data['fixedday'];
            if($tabsent) $tabsent = number_format(($this->attcompute->exp_time($tabsent) / (8 *3600)),2);
            
            if($old_department != $deptid):
                $old_department = $deptid;
        ?>
        <tr class="pdata">
            <td colspan="12">
                <strong>
                    <?=$departments[$deptid]?>
                </strong>
            </td>
        </tr>
        <?
            endif;
        ?>
        <tr class="pdata">
            <td class="pdataid"><?=$empid?></td>
            <td><?=$empFullname?></td>
            <td class="align_center"><?=$totr?></td>
            <td class="align_center"><?=$totrest?></td>
            <td class="align_center"><?=$tothol?></td>
            <td class="align_center"><?=$tlec?></td>
            <td class="align_center"><?=$tutlec?></td>
            <td class="align_center"><?=$tabsent?></td>
            <td class="align_center"><?=$tvl?></td>
            <td class="align_center"><?=$tel?></td>
            <td class="align_center"><?=$tol?></td>
            <td class="align_center"><?=$tsc?></td>
            <td class="align_center"><?=$fixedday?'':$workdays?></td>
            <td class="align_center"><?=$ishol?></td>
        </tr>   
        <?
        endforeach;
        ?>
    </tbody>
    <?endif;?>
</table>