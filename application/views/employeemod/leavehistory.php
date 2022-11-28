<?php

/**
 * @author Justin
 * @copyright 2016
 */

$ishead = $this->employee->getDeptCode($this->session->userdata("username"));
$ishead = $ishead ? "Cluster Head" : "Head/Dean";
$stat   = isset($stat) ? $stat : "";
$show   = false;
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
.datatable tr th{
    padding: 1px 12px 1px 12px;
}
#leaveh tr td,#leaveh tr th{
    text-align: center;
}
#leaveh tr th{
    background-color: #2e5266 ;
    color: #ffffff;
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
<h5>History</h5>
<table class="table table-hover table-bordered datatable" id="leaveh">
    <thead>
        <tr>
            <th rowspan="2" class="mh" hidden="">&nbsp;</th>
            <th rowspan="2">Date Applied</th>
            <th colspan="2">Inclusive Dates</th>
            <th rowspan="2">Type</th>
            <th rowspan="2">Details</th>
            <th rowspan="2">Approving Authority</th>
            <th rowspan="2">Status</th>
            <th rowspan="2">Mark as read</th>
        </tr>
        <tr>
            <th>From</th>
            <th>To</th>
        </tr>
    </thead>
    <?
        $query = $this->employeemod->displayleavehistory($stat);
        if($query->num_rows() > 0){
    ?>
    <tbody>
        <?
            foreach($query->result() as $row){
                $nomodif = false;                
                $bold = $row->isread ? "" : "style='font-weight: bold;'";
                if(in_array($row->deptheadstatus,array("APPROVED","DISAPPROVED")))
                    $nomodif = true;
                else   
                    $show   = true;
        ?>
            <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : ($row->status == "CANCELED" ? " style='background: #ffcccc'" : ""))?>>
                <td class="mh" hidden="">
                    <?if(!$nomodif){?>                
                        <div class="btn-group">
                            <a class="btn btn-primary" tag="editrequest" href="#" data-toggle="modal" data-target="#myModal" idnum="<?=$row->id?>" ><i class="icon glyphicon glyphicon-edit"></i></a>
                            <a class="btn btn-primary" tag="delrequest" href="#" idnum="<?=$row->id?>" ><i class="icon glyphicon glyphicon-remove-sign"></i></a>
                        </div>
                    <?}?>                    
                </td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->timestamp))?></td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->datefrom))?></td>
                <td <?=$bold?> ><?=date('F d, Y',strtotime($row->dateto))?></td>
                <td <?=$bold?>><?=$this->employeemod->othLeaveDesc(($row->type == "other" ? $row->other : $row->type))?></td>
                <td><a href="#modal-view" tag='view_d' data-toggle="modal" code="<?=$row->employeeid?>" idnum="<?=$row->id?>" ><i class="icon-large icon-eye-open"></i></a></td>
                <td><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalleave" idkey="<?=$row->id?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                <td <?=$bold?>><?=$row->status?></td>
                <td width="1%"><input type="checkbox" value="1" name="mar" idkey="<?=$row->id?>"  <?=($row->isread ? " checked disabled" : "")?>  /></td>
            </tr>   
        <?
            }
        ?>
    </tbody>
    <?
        }
    ?>
    
</table>
<div class="modal fade" id="myModalleave" data-backdrop="static"></div>
<?if($show){?><script>$(".mh").show();</script><?}?>
<script>
$("#applyleave").click(function(){  
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "leaveapply"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});
$("a[tag='view_d']").click(function(){
    var code = "";  
    var idnum = "";  
    if($(this).attr("code")) code = $(this).attr("code");
    if($(this).attr("idnum")) idnum = $(this).attr("idnum");
    var form_data = {
                        code: code,
                        idnum: idnum,
                        job : "lview",
                        folder   : "employeemod", 
                        view     : "mailleaveapp_manage"
                    }
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#modal-view").find("div[tag='display']").html(msg);
       }
    }); 
});
$("a[tag='view_app']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "approval_list"
                    };
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalleave").html(msg);
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
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "leave_app"},
           success  : function(msg){
            //loadleavehistory("<?=$stat?>");
            location.reload();
           }
        }); 
});
$("a[tag='editrequest']").click(function(){
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "leavemodify", id: $(this).attr("idnum")},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });
});
$("a[tag='delrequest']").click(function(){
   var pmpt = confirm("Do you really want to delete this request?");
   if(pmpt){
       $.ajax({
        url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
        type     :   "POST",
        data     :   {model : "delLeave", id: $(this).attr("idnum")},
        success  :   function(msg){
            alert(msg);
            location.reload();
        }
       }); 
   }
});
$("#leaveh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$("#leaveh_length").append("<span style='margin-left: 45px;'>Status : <select id='leavestatus' style='margin-bottom: 2px;'><?=$this->extras->showCategoryopt($stat)?><option value='CANCELED'>CANCELED</option></select></div>");
$("#leavestatus").change(function(){ loadleavehistory($(this).val()); });
</script>