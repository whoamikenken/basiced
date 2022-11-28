<?php

/**
 * @author Justin
 * @copyright 2015
 */

$employeeid = $_POST['employeeid'];
$c = 0;
$tcolor = "#00628B";
?>
<tr>
    <td class="col-md-2" style="text-align: center; background-color: <?=$tcolor?>; color: yellow;"><strong>Date From</strong></td>
    <td class="col-md-2" style="text-align: center; background-color: <?=$tcolor?>; color: yellow;"><strong>Date To</strong></td>
    <td class="col-md-2" style="text-align: center; background-color: <?=$tcolor?>; color: yellow;"><strong>Start Time</strong></td>
    <td class="col-md-2" style="text-align: center; background-color: <?=$tcolor?>; color: yellow;"><strong>End Time</strong></td>
    <td class="col-md-2" style="text-align: center; background-color: <?=$tcolor?>; color: yellow;"><strong>Tardy</strong></td>
    <td class="col-md-2" style="text-align: center; background-color: <?=$tcolor?>; color: yellow;"><strong>Absent</strong></td>
    <td class="col-md-2" style="text-align: center; background-color: <?=$tcolor?>; color: yellow;"><strong>Halfday Absent</strong></td>
    <td class="col-md-2" style="text-align: center; background-color: <?=$tcolor?>; color: yellow;"><strong>Early Dismissal</strong></td>
    <td class="col-md-2" style="text-align: center; background-color: <?=$tcolor?>; color: yellow;"><strong>User</strong></td>
</tr>
<?foreach($this->extras->showOfficialSchedHistory($employeeid) as $row){
    $c++;
    if($c%2 == 0)   $color =  "#E6E6DC"; 
    else    $color = "#81A594";
?>
<style>
td :hover{
    color: red;
    font-weight: bold;
    -webkit-border-radius: 36px 12px;
    -moz-border-radius: 36px / 12px;
    border-radius: 36px / 12px;
    -webkit-transform: rotate(-2deg , 5deg);
}
</style>
<tr>
    <td style="text-align: center; background-color: <?=$color?>;"><label class="center"><?=$row->datefrom?></label></td>
    <td style="text-align: center; background-color: <?=$color?>;"><label><?=$row->dateto?></label></td>
    <td style="text-align: center; background-color: <?=$color?>;"><label class="name_display"><?=date('h:i A',strtotime($row->start_time))?></label></td>
    <td style="text-align: center; background-color: <?=$color?>;"><label class="name_display"><?=date('h:i A',strtotime($row->end_time))?></label></td>
    <td style="text-align: center; background-color: <?=$color?>;"><label class="name_display"><?=date('h:i A',strtotime($row->tardy))?></label></td>
    <td style="text-align: center; background-color: <?=$color?>;"><label class="name_display"><?=date('h:i A',strtotime($row->absent))?></label></td>
    <td style="text-align: center; background-color: <?=$color?>;"><label class="name_display"><?=date('h:i A',strtotime($row->halfday_absent))?></label></td>
    <td style="text-align: center; background-color: <?=$color?>;"><label class="name_display"><?=date('h:i A',strtotime($row->early_dismissal))?></label></td>
    <td style="text-align: center; background-color: <?=$color?>;"><label class="inputdetails_center"><?=$row->user?></label></td>
</tr>
<?}?> 