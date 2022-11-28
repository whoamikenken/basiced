<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */

$cutoffid = "";
$se = $this->db->query("SELECT id FROM cutoff_summary ORDER BY `datefrom` DESC LIMIT 1;");
if($se->num_rows()>0){
   $cutoffid=$se->row(0)->id;   
}

$dtr = $this->db->query("select * from employee_dtr where cutoffid='{$cutoffid}' AND employeeid='{$employeeid}' order by cdate,starttime");
?>
<script type="text/javascript">
    $("#printfriendly").click(function(){
        var url = "<?=site_url("reports_/reportloader")?>" + "?view=reports_excel/view_dtr" + "&cutoffid=<?=$cutoffid?>" + "&employeeid=<?=$employeeid?>";
        var windowName = "download";//$(this).attr("name");
        var windowSize = "width=400,height=400";
        window.open(url, windowName, windowSize);
    });
</script>
<div class="well-content" style='border: transparent !important;'>
<a href="#" id="printfriendly">Printer Friendly</a>
<table class="table table-striped table-bordered table-hover datatable">
    <thead>
  <tr>
    <th>Date</th>
    <th>Day</th>
    <th width='100px'>Schedule</th>
    <th>Time In</th>
    <th>Time Out</th>
    <th width='50px'>Tardy</th>
    <th>Under Time</th>
    <th>Absent</th>
    <th>Regular</th>
    <th>Legal Hol</th>
    <th>Special Hol</th>
    <th>Rest</th>
    <th>Rest Legal</th>
    <th>Rest Special</th>
    <th>OT Reg</th>
    <th>OT Legal</th>
    <th>OT Special</th>
    <th>OT Rest</th>
    <th>OT Rest Hol</th>
    <th>Night Prem</th>
    <th>Type</th>
  </tr>
  </thead>
    <tbody id="employeelist">
<?
for($i=0;$i<$dtr->num_rows();$i++){
$mrow = $dtr->row($i);    
?>
  <tr>
    <td class='align_center'><?=date("m/d/Y",strtotime($mrow->cdate))?></td>
    <td class='align_center'><?=$mrow->dayofweek_?></td>
    <td class='align_center'><?=$mrow->schedules_?></td>
    <td><?=($mrow->timein ? date("h:iA",strtotime($mrow->timein)) : "")?></td>
    <td class='align_center'><?=($mrow->timein ? (date("mdy",strtotime($mrow->timein))!=date("mdy",strtotime($mrow->timeout)) ? date("m/d/Y h:iA",strtotime($mrow->timeout)) : date("h:iA",strtotime($mrow->timeout))) : "")?></td>
    <td class='align_center'><?=(date("H",strtotime($mrow->tardy))!="00" ? date("g",strtotime($mrow->tardy)) . "h" : "")?> <?=(date("i",strtotime($mrow->tardy))!="00" ? date("i",strtotime($mrow->tardy))."m" : "")?></td>
    <td class='align_center'><?=(date("H",strtotime($mrow->undertime))!="00" ? date("g",strtotime($mrow->undertime)) . "h" : "")?> <?=(date("i",strtotime($mrow->undertime))!="00" ? date("i",strtotime($mrow->undertime))."m" : "")?></td>
    <td class='align_center'><?=(date("H",strtotime($mrow->absent))!="00" ? date("g",strtotime($mrow->absent)) . "h" : "")?> <?=(date("i",strtotime($mrow->absent))!="00" ? date("i",strtotime($mrow->absent))."m" : "")?></td>
    <td class='align_center'><?=(date("H",strtotime($mrow->regular))!="00" ? date("g",strtotime($mrow->regular)) . "h" : "")?> <?=(date("i",strtotime($mrow->regular))!="00" ? date("i",strtotime($mrow->regular))."m" : "")?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td><?=($mrow->type_ ? $mrow->type_ : "ADMIN")?></td>
  </tr>
<?    
}
?>    
</tbody>
</table>
</div>