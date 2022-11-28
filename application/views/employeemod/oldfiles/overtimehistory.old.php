<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
$ishead = $this->employee->getDeptCode($this->session->userdata("username"));
$ishead = $ishead ? "Cluster Head" : "Head/Dean";
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
#overtimeh tr td,#overtimeh tr th{
    text-align: center;
}
#overtimeh tr th{
    background-color: #510051;
    color: #ADAD0E;
}
input[name=mar]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
}
</style>
<table class="table table-hover table-bordered datatable" id="overtimeh">
    <thead>
        <tr>
            <th rowspan="2">&nbsp;</th>
            <th rowspan="2">Date Applied</th>
            <th colspan="2">Inclusive Dates</th>
            <th rowspan="2">Details</th>
            <th rowspan="2">Status</th>
            <th rowspan="2">Approving Authority</th>
            <th rowspan="2">Mark as read</th>
        </tr>
        <tr>
            <th>From</th>
            <th>To</th>
        </tr>
    </thead>
    <?
        $query = $this->employeemod->displayotrequest($dfrom,$dto);
        if($query->num_rows() > 0){
    ?>
    <tbody>
        <?
            foreach($query->result() as $row){
            $bold = $row->isread ? "" : "style='font-weight: bold;'";
        ?>
            <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : "")?>>
                <td>
                    <div class="btn-group">
                        <a class="btn btn-primary" id="editrequest" href="#" data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>"><i class="icon glyphicon glyphicon-edit"></i></a>
                        <a class="btn btn-primary" id="delrequest" href="#" idkey="<?=$row->id?>" ><i class="icon glyphicon glyphicon-remove-sign"></i></a>
                    </div>
                </td>
                <td><?=date('F d, Y',strtotime($row->timestamp))?></td>
                <td><?=date('F d, Y',strtotime($row->dfrom))?></td>
                <td><?=date('F d, Y',strtotime($row->dto))?></td>
                <td><a href="#" tag='view_d' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>" ><i class="icon-large icon-eye-open"></i></a></td>
                <td><?=$row->status?></td>
                <td <?=$bold?>><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                <td width="1%"><input type="checkbox" value="1" name="mar" idkey="<?=$row->id?>" <?=($row->isread ? " checked disabled" : "")?> /></td>
            </tr> 
        <?
            }
        ?>
    </tbody>
    <?
        }
    ?>
    
</table>
<div class="modal fade" id="myModalatt" data-backdrop="static"></div>
<script>
$("a[tag='view_d']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        job      : "view",
                        mod      : "emph",
                        folder   : "employeemod", 
                        view     : "mailotapp_view",
                        manage   : "1"
                    };
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});
$("a[tag='view_app']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "approval_list_overtime"
                    };
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});
$("input[name='mar']").click(function(){
   var cval  = $(this).val();
   var idkey = $(this).attr("idkey");
   $(this).attr("disabled",true);
   $(this).closest("tr").removeAttr("style");
   $.ajax({
           url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
           type     :   "POST",
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "overtime_app"},
           success  : function(msg){
            //loadbushistory();
            location.reload();
           }
        }); 
});
$("#editrequest").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "mailotapp_modify",
                        manage   : "1"
                    };
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});
$("#delrequest").click(function(){
   var pmpt = confirm("Do you really want to delete this request?");
   if(pmpt){
       $.ajax({
        url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
        type     :   "POST",
        data     :   {model : "delOT", id: $(this).attr("idkey")},
        success  :   function(msg){
            alert(msg);
            location.reload();
        }
       }); 
   }
});
$("#overtimeh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$("#overtimeh_length").append("<span style='margin-left: 45px;'>Status : <select id='ovtstatus' style='margin-bottom: 2px;'><?=$this->extras->showCategoryopt($stat)?></select></div>");
$("#ovtstatus").change(function(){ loadbushistory($(this).val()); });
</script>