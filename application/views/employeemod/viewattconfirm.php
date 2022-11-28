<?php
/**
 * @author Justin
 * @copyright 2016
 */

if(!isset($deptlist)) $deptlist = array();
$deptlist_str = implode(',', $deptlist);

if(!isset($nofinalize)) $nofinalize = 0;

if(!$this->input->post("cutoff"))die;
$toks = $this->input->post("toks");
$cutoff = $toks ? $this->gibberish->decrypt($this->input->post("cutoff"), $toks) : $this->input->post("cutoff");
$dex = explode(",",$cutoff);
$dateRange = "";
$from_date = $dex[0];
$to_date = $dex[1];
$edata = "timesheet";
$empid = isset($fv)?$fv:'';
$tnt = $toks ? $this->gibberish->decrypt($this->input->post("tnt"), $toks) : $this->input->post("tnt");
$estatus = "";
$dept = "";
$dateRange = $this->time->createRangeToDisplay($from_date, $to_date);
$departments = $this->extras->showdepartment();

list($dtr_start,$dtr_end,$payroll_start,$payroll_end) = $this->payrolloptions->getDtrPayrollCutoffPair($from_date,$to_date);

$result = $this->attendance->emp_confirmed($from_date, $to_date, $tnt,$empid, $deptlist_str,$payroll_start);
$showfinalize = $this->employeemod->showFinalize($from_date,$to_date,$tnt,$payroll_start,$payroll_end);
if($nofinalize) $showfinalize = false;

?>
<h3>Attendance Summary <?=$tnt?strtoupper($tnt):''?></h3>
<p><?=$dateRange?></p>
<p><?= ($dept != "") ? $departments[$dept] : " " ?></p>
<style>
#attstbl tr th,#asctblnt tr th{
    background-color: #0072c6;
    color: black;
}

#attstbl tbody tr.is-consecutive{
    background-color: rgb(255, 102, 102); 
    color: rgb(255, 255, 255);
}
#attstbl tbody tr.is-consecutive:hover{
    background-color: rgb(255, 102, 102); 
    color: black;
}  
p{
    color:black;
}

</style>
<?if($tnt == "teaching"){ // Teaching?>
<div id="attstbl" class="well_content">
  <table class="table table-bordered datatable" id="asctblnt">
    <thead>
        <tr>
            <th class="sorting_asc" rowspan="2">Employee ID</th>
            <th rowspan="2">Name</th>
            <!-- <th rowspan="2">Overload</th> -->
            <th class="align_center" colspan="3">No. of late/UT (hr:min)</th>
            <th class="align_center" >Absent</th>
            <th class="align_center" colspan="3">Leaves</th>
            <!-- <th class="align_center" rowspan="2" >Remarks/Others</th> -->
            <th class="align_center" colspan="3">Total Deduction</th>
        </tr>
        <tr>
            <th class="align_center">Lec</th>
            <th class="align_center">Lab</th>
            <th class="align_center">Admin</th>
            <th class="align_center">Subject</th>
            <th class="align_center">Family</th>
            <th class="align_center">Vacation</th>
            <th class="align_center">Sick</th>
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
        $tlec = $data["latelec"];
        $tlab = $data["latelab"];
        $tadmin = $data["lateadmin"];
        $tabsent = $data["absent"]; 
        $tel = $data["eleave"];
        $tvl = $data["vleave"];
        $tsl = $data["sleave"];
        $tol = $data["oleave"];         
        $tdlec = $data["deduclec"];
        $tdlab = $data["deduclab"];
        $tdadmin = $data["deducadmin"];

if ($deptDisplay != $data["qDepartment"]) {
?>
    <tr class="pdept"><td colspan="13"><b><?="<p><b>".$data["qDepartment"]."</></p>"?></b></td></tr>
<?
}
?>
    <tr class="pdata">
        <td class="pdataid"><?=$empid?></td>
        <td><?=$empFullname?></td>
        <!-- <td><?=$overload?></td> -->
        <td class="align_center"><?=$tlec?></td>
        <td class="align_center"><?=$tlab?></td>
        <td class="align_center"><?=$tadmin?></td>
        <td class="align_center"><?=$tabsent?></td>
        <td class="align_center"><?=$tel?></td>
        <td class="align_center"><?=$tvl?></td>
        <td class="align_center"><?=$tsl?></td>
        <!-- <td class="align_center"><?=$tol?></td> -->
        <td class="align_center"><?=$tdlec?></td>
        <td class="align_center"><?=$tdlab?></td>
        <td class="align_center"><?=$tdadmin?></td>
        
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
    <div class="pull-right">
        <span id="cmsg" style="color: red;font-weight: bold;"></span>
        <input type="button" id="generate" class="btn blue" value="Generate" style="cursor: pointer;margin-bottom: 5px;" />
    </div>
    <div id="attstbl" class="well_content">
      <table class="table table-bordered datatable" id="asctblnt">
        <thead>
            <tr>
                <th class="sorting_asc" rowspan="2">Employee ID</th>
                <th rowspan="2">Name</th>
                <!-- <th rowspan="2">Overload</th> -->
                <th class="align_center" colspan="4">Overtime (hr:min)</th>
                <th class="align_center">Late/Undertime</th>
                <th class="align_center" rowspan="2">Absent</th>                        
                <th class="align_center" colspan="3">Leaves</th>
                <!-- <th class="align_center" rowspan="2" >Remarks/Others</th> -->
                <th class="align_center" rowspan="2" >Holiday</th>
                <th rowspan="2">Total Days Absent</th>
                <th class="align_center" rowspan="2" >HOLD STATUS</th>
            </tr>
            <tr>
                <th class="align_center">Regular</th>
                <th class="align_center">Saturday</th>
                <th class="align_center">Sunday</th>
                <th class="align_center">Holiday</th>
                <th class="align_center">Hr:min</th>            
                <th class="align_center">Family</th>
                <th class="align_center">Vacation</th>
                <th class="align_center">Sick</th>
            </tr>
        </thead>
        <tbody>
    <?
    // echo $tnt;
    $result = $this->attendance->emp_confirmed_nt($from_date, $to_date, $tnt,$empid, $deptlist_str,$payroll_start);
    
    if (count($result) > 0) {
        $deptDisplay = "";
        foreach ($result as $key => $data) {
            // echo '<pre>';var_dump($result);die;
            //is-consecutive
            $is_consecutive_absent = $data["is_consecutive_absent"];
            $empid = $data["qEmpId"];
            $empFullname = $data["qFullname"];
            $overload = $data["overload"];
            $totr = $data["otreg"];
            $totsat = $data["otsat"];
            $totsun = $data["otsun"];
            $tothol = $data["othol"]; 
            $tlec = $data["lateut"];
            $tabsent = $data["absent"];
            $tel = $data["eleave"];
            $tvl = $data["vleave"];
            $tsl = $data["sleave"];
            $tol = $data["oleave"];         
            $ishol = $data['isholiday'];

        if ($deptDisplay != $data["qDepartment"]) {
    ?>
        <tr class="pdept"><td colspan="14"><b><?="<p style='color:black;'><b>".$data["qDepartment"]."</b></p>"?></b></td></tr>
        <?}?>
        <tr class="<?=($is_consecutive_absent) ? "is-consecutive" : "pdata"?>">
            <td class="pdataid"><?=$empid?></td>
            <td><?=$empFullname?></td>
            <!-- <td><?=$overload?></td> -->
            <td class="align_center"><?=$totr?></th>
            <td class="align_center"><?=$totsat?></th>
            <td class="align_center"><?=$totsun?></th>
            <td class="align_center"><?=$tothol?></th>
            <td class="align_center"><?=$tlec?></th>
            <td class="align_center"><?=($tabsent/8)?></th>
            <td class="align_center"><?=$tel?></th>
            <td class="align_center"><?=$tvl?></th>
            <td class="align_center"><?=$tsl?></th>
            <!-- <td class="align_center"><?=$tol?></th> -->
            <td class="align_center"><?=$ishol?></th>
            <td class="align_center"><?=$data['day_absent']?></th>
            <td class="align_center"><?=$data['hold_status_change']?></th>
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
<?if($showfinalize && $this->session->userdata("usertype") == "ADMIN"){?>
    <div class="pull-right">
        <span id="cmsg" style="color: red;font-weight: bold;"></span>
        <input type="button" id="generate_att" class="btn btn-primary" value="Generate" style="cursor: pointer;" />
        <input type="button" id="finalize" class="btn btn-primary" value="Finalize" style="cursor: pointer;" />
    </div>
<?}?>
<script>
$("#finalize").click(function(){
    $('.pdata,.is-consecutive').each(function() {
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
                $("#generate_att").hide();
                $("#cmsg").text(data[1]);
                
            }
        });
    });
});

$("#generate_att").click(function(){
    var cutoff = "<?= $from_date.', '.$to_date ?>";
    var teachingtype = "<?= $tnt ?>";
    window.open("<?=site_url("forms/generateConfirmedAttendance")?>?cutoff="+cutoff+"&teachingtype="+teachingtype); 
});

</script>