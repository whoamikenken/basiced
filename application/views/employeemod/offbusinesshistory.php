<?php

/**
 * @author Justin
 * @copyright 2016
 */

$ishead = $this->employee->getDeptCode($this->session->userdata("username"));
$ishead = $ishead ? "Cluster Head" : "Head/Dean";
$stat   = isset($stat) ? $stat : "";
// newly added variable for #ica-hyperion 21090 by justin (with e)
$otherType = array(
                    "ABSENT"          => "ABSENT",
                    "DIRECT"          => "OFFICIAL BUSINESS",
                    "NO PUNCH IN/OUT" => "CORRECTION OF TIME IN/OUT "
                  );
$status = array(
                  'status',
                  'deptheadstatus',
                  'hrdirstatus',
                  'clusterheadstatus',
                  'campusprincipalstatus',
                  'univphystatus',
                  'budgetoffstatus',
                  'financedirstatus',
                  'presidentstatus'
               );
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
.datatable tr th{
    padding: 1px 12px 1px 12px;
}
#offbus tr td,#offbus tr th{
    text-align: center;
}
#offbus tr th{
    background-color: #505050;
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
<table class="table table-hover table-bordered datatable" id="offbus">
    <thead>
        <tr>
            <th rowspan="2">&nbsp;</th>
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
        $query = $this->employeemod->displaybushistory($stat);
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
                        <!-- <a class="btn btn-primary" id="editrequest" href="#" data-toggle="modal" data-target="#myModal" idkey="<?=$row->id?>"><i class="icon glyphicon glyphicon-edit"></i></a> -->
                        <?
                          $showEditRemoveBtn = true;
                          for ($i=0; $i < count($status); $i++) { 
                            if($row->$status[$i] == "APPROVED" || $row->$status[$i] == "DISAPPROVED"){
                              $showEditRemoveBtn = false;
                              break;
                            }  
                          }

                          if($showEditRemoveBtn){
                        ?>
                        <a class="btn btn-primary" data-toggle="modal" data-target="#myModal" id="<?=$row->id?>" onclick="editRequest(this.id)"><i class="icon glyphicon glyphicon-edit"></i></a>
                        <a class="btn btn-primary" href="#" id="<?=$row->id?>" onclick="delRequest(this.id)"><i class="icon glyphicon glyphicon-remove-sign"></i></a>
                        <?}?>
                    </div>
                </td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->timestamp))?></td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->datefrom))?></td>
                <td <?=$bold?> ><?=date('F d, Y',strtotime($row->dateto))?></td>
                <td <?=$bold?>><?=$otherType[$row->othertype]?></td>
                <td><a href="#" tag='view_d' data-toggle="modal" data-target="#myModal" code="<?=$row->employeeid?>" idnum="<?=$row->id?>" ><i class="icon-large icon-eye-open"></i></a></td>
                <td><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalleave" idkey="<?=$row->id?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                <td <?=$bold?>><?=$row->status?></td>
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
<div class="modal fade" id="myModalleave" data-backdrop="static"></div>
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
    $("#myModal").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    var code = "";  
    var idnum = "";  
    if($(this).attr("code")) code = $(this).attr("code");
    if($(this).attr("idnum")) idnum = $(this).attr("idnum");
    var form_data = {
                        code: code,
                        idnum: idnum,
                        job : "lview",
                        folder   : "employeemod", 
                        //view     : "mailleaveapp_manage"
                        view     : "offbusinessdetails"
                    }
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        //$("#modal-view").find("div[tag='display']").html(msg);
        $("#myModal").html(msg);
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
            location.reload();
           }
        }); 
});
// comment by justin (with e), error : unable to edit
// $("#editrequest").click(function(){
//     $.ajax({
//         url      : "<?=site_url("employeemod_/fileconfig")?>",
//         type     : "POST",
//         data     : {folder: "employeemod", view: "offbusinessmodify", id: $(this).attr("idkey")},
//         success: function(msg){
//             $("#myModal").html(msg);
//         }
//     });  
// });
function editRequest(idKey){
  // displayed edit modal
  $("#myModal").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
  $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "offbusinessmodify", id: idKey},
        success: function(msg){
            $("#myModal").html(msg);
        }
    }); 
}
function delRequest(idKey){
  var pmpt = confirm("Do you really want to delete this request?");
   if(pmpt){
       $.ajax({
        url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
        type     :   "POST",
        data     :   {model : "delOffBus", id: idKey},
        success  :   function(msg){
            alert(msg);
            location.reload();
        }
       }); 
   }
}
$("#delrequest").click(function(){
   var pmpt = confirm("Do you really want to delete this request?");
   if(pmpt){
       $.ajax({
        url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
        type     :   "POST",
        data     :   {model : "delOffBus", id: $(this).attr("idkey")},
        success  :   function(msg){
            alert(msg);
            location.reload();
        }
       }); 
   }
});
$("#offbus").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$("#offbus_length").append("<span style='margin-left: 45px;'>Status : <select id='leavestatus' style='margin-bottom: 2px;'><?=$this->extras->showCategoryopt($stat)?></select></div>");
$("#leavestatus").change(function(){ loadbushistory($(this).val()); });
</script>