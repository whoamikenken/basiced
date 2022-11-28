<?php
/**
 * @Author Justin
 * Copyright 2016
 */
 
$datedisplay = "";
$from_date = $dfrom;
$to_date = $dto;
$empid = $eid;
$edata = "NEW";
$datedisplay = $this->time->createRangeToDisplay($from_date,$to_date);
?>
<?
$qdate = $this->attcompute->displayDateRange($from_date, $to_date);
?>
<style>
table.dataTable thead>tr>th.sorting_asc,
table.dataTable thead>tr>th.sorting_desc,
table.dataTable thead>tr>th.sorting,
table.dataTable thead>tr>td.sorting_asc,
table.dataTable thead>tr>td.sorting_desc,
table.dataTable thead>tr>td.sorting {
    padding-right: 8px;
}
 
th.sorting_asc::after,
th.sorting_desc::after {
   content:"" !important;
}
</style>
<div class="well-content" style='border: transparent !important;'>
<h2>Attendance</h2>
<p><?=$datedisplay?></p>
<p><?=$this->employee->getfullname($empid)?></p>
<table class="ttable table-striped table-bordered table-hover" id="indvtbl">
    <thead>
        <tr>
            <th rowspan="2" class="align_center">Date</th>
            <th class="align_center" colspan="2">Official Time</th>
            <th class="align_center" colspan="2">Actual Log Time</th>
            <th class="align_center" rowspan="2" width="1%">Tag as absent</th>
        </tr>
        <tr>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
            <th class="align_center">IN</th><th class="align_center">OUT</th>
        </tr>
    </thead>
    <tbody id="employeelist">
    <?
    foreach ($qdate as $rdate) {
        $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
        $sched = $this->attcompute->displaySched($empid,$rdate->dte);
        $countrow = $sched->num_rows();
        if($countrow > 0){
            $tempsched = "";
            foreach($sched->result() as $rsched){
            if($tempsched == $dispLogDate)  $dispLogDate = "";
            $stime = $rsched->starttime;
            $etime = $rsched->endtime; 
            $type  = $rsched->leclab;
            // logtime
            list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);    
            
            $id = $this->employeemod->checktagAbsent($empid,$rdate->dte,$rsched->starttime,$rsched->endtime);
            
    ?>
            <tr class="edata">
                <?if($dispLogDate){?>
                    <td class="align_center" rowspan="<?=$countrow?>"><?=$dispLogDate?> </td>
                <?}?>
                <td class="align_center"><?=($stime ? date('h:i A',strtotime($stime)) : "--")?></td>
                <td class="align_center"><?=($stime ? date('h:i A',strtotime($etime)) : "--")?></td>
                <td class="align_center"><?=(($login) ? date("h:i A",strtotime($login)) : "--")?></td>
                <td class="align_center"><?=(($logout) ? date("h:i A",strtotime($logout)) : "--")?></td>
                <td class="align_center"><input type="checkbox" class="cabsent" schedid="<?=$id?>" scheddate="<?=$rdate->dte?>" schedstart="<?=$rsched->starttime?>" schedend="<?=$rsched->endtime?>" value="1" <?=($id ? "checked" : "")?> /></td>
            </tr>
    <?
            $tempsched = $dispLogDate;
            }
        }
    }
    ?>
    </tbody>
</table>
</div>
<script>

$(document).ready(function(){
    var table = $('#indvtbl').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

$(".cabsent").click(function(){
   if($(this).prop("checked")){
    var form_data = {
                        model       : "addAbsent",
                        sjob        : "addAbsent",            
                        eid         : "<?=$empid?>",
                        scheddate   : $(this).attr("scheddate"),
                        schedstart  : $(this).attr("schedstart"),
                        schedend    : $(this).attr("schedend"),
                        absent      : $(this).val()
                    };
    $.ajax({
        url:"<?=site_url("employeemod_/loadmodelfunc")?>",
        type:"POST",
        data:form_data,
        success: function(msg){
            loaddata("<?=$from_date?>","<?=$to_date?>","<?=$empid?>");
        }
    });
   }else{
    $.ajax({
        url:"<?=site_url("employeemod_/loadmodelfunc")?>",
        type:"POST",
        data:{
            model       : "addAbsent",
            sjob        : "delabsent",
            idkey       : $(this).attr("schedid")
        },
        success: function(msg){
            loaddata("<?=$from_date?>","<?=$to_date?>","<?=$empid?>");
        }
    });
   } 
});
</script>