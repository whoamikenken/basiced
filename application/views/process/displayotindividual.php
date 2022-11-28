<?php
/**
 * @author Justin
 * @copyright 2015
 */

$empid = $fv;

// create a display for range of date
$datedisplay = $this->time->createRangeToDisplay($dset,$dsetto);

// retrieve data from database
$results = $this->attendance->giveIndividualSummary($dset, $dsetto, $empid);

foreach ($results as $key => $row) {
    if ($row["queFullName"] != "") {
      $empFullname = $row["queFullName"];
      break;
    }
  }

?>
<div class="well-content" style='border: transparent !important;'>
<h2>OT Approval</h2>
<p style="font-weight: bold;"><?=$datedisplay?></p>
<p style="font-weight: bold;"><?php print((isset($empFullname) ) ? $empFullname : ""); ?></p>
<table class="table table-striped table-bordered table-hover datatable">
    <thead>
        <tr>
            <th rowspan="2" class="sorting_asc">Date</th>
            <th>Over Time</th>
            <th rowspan="2">Status</th>
        </tr>
        <tr>
            <th>Mins.</th>
        </tr>
    </thead>
    <tbody>
<?
if (count($results) > 0) {
  // if there are records to retrieved, display them.
  foreach ($results as $key => $row) {
    $empid = $row["queEmpId"];
    $dispLogDate = date("d-M (l)",strtotime($row["queLogDate"]));
    $dispTotalOvertime = $this->time->EditedOT($row["queLogDate"]) ? $this->time->EditedOT($row["queLogDate"]) : ( ($this->time->hoursToMinutes($row["queOvertime"]) > 0) ? $this->time->hoursToMinutes($row["queOvertime"]) : "" );            
    $isaccepted = $this->payroll->otchecking($empid,$row["queLogDate"]);
        ?>
          <tr>
            <td class="align-center"><?=$dispLogDate?></td>
            <td class="align-center"><?=$dispTotalOvertime?></td>
            <td>
            <?if($isaccepted){?>
                Accepted
            <?}else{?>
                <a class="btn btn-primary" id="acceptot" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default" onclick="javascript:otaccept('<?=$dispTotalOvertime?>','<?=$empid?>','<?=$row["queLogDate"]?>');">Accept</a>
            <?}?>
            </td>
          </tr>
        <?
  }
}
?>
    </tbody>
</table>
</div>
<script>
function otaccept(ot,eid,otdate){
 $.ajax({
        url      : "<?=site_url('payroll_/payrollconfig')?>",
        type     : "POST",
        data     : {view   :   "otaccept", ot : ot, eid : eid, otdate, otdate},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });
}
$(".datatable").dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[10, 20, 40, -1], [10, 20, 40, "All"]]
});
</script>