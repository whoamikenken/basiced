<?php

/**
 * @author Justin
 * @copyright 2016
 */

?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
#seminarh tr td,#seminarh tr th{
    text-align: center;
}
#seminarh tr th{
    background-color: #8b9dc3 ;
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
<?
$colStatus = array(
                    "status",
                    "deptheadstatus",
                    "hrdirstatus",
                    "clusterstatus",
                    "cpstatus",
                    //"univphystatus",
                    "budgetoffstatus",
                    "financedirstatus",
                    "presidentstatus"
                  )
?>
<table class="table table-hover table-bordered datatable" id="seminarh">
    <thead>
        <tr>
            
            <th rowspan="2">&nbsp;</th>
            
            <th rowspan="2">Date Applied</th>
            <th rowspan="2">Details</th>
            <th rowspan="2">Approving Authority</th>
            <th rowspan="2">Status</th>            
            <th colspan="3">Post Activity Report</th>            
            <th rowspan="2">Mark as read</th>
        </tr>
        <tr>
            <th>Create Report</th>
            <th>Date Submitted</th>
            <th>Status</th>
        </tr>        
    </thead>
    <?
        $query = $this->employeemod->displayseminarhistory($category,$dfrom,$dto,($this->employeemod->seminarnotif()->num_rows() ? "PENDING" : ""));
        if($query->num_rows() > 0){
    ?>
    <tbody>
        <?
            foreach($query->result() as $row){
            $bold = $row->isread ? "" : "style='font-weight: bold;'";
        ?>
            <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : "")?>>
                
                <td>
                    <?
                      $unable_btn = true;
                      foreach ($colStatus as $key){
                        if($row->$key == "APPROVED" || $row->$key == "DISAPPROVED"){
                          $unable_btn = false;
                          break;
                        }
                      }

                      if($unable_btn){
                    ?>
                      <div class="btn-group">
                          <!-- <a class="btn btn-primary" id="editrequest" href="#" data-toggle="modal" data-target="#myModal" bid="<?=$row->base_id?>"><i class="icon glyphicon glyphicon-edit"></i></a> -->
                          <a class="btn btn-primary" id="<?=$row->base_id?>" href="#" data-toggle="modal" data-target="#myModal" onclick="editSeminar(this.id)"><i class="icon glyphicon glyphicon-edit"></i></a>
                          <!-- <a class="btn btn-primary" id="delrequest" href="#" bid="<?=$row->base_id?>" ><i class="icon glyphicon glyphicon-remove-sign"></i></a> -->
                          <a class="btn btn-primary" id="<?=$row->base_id?>" href="#" onclick="deleteSeminar(this.id)"><i class="icon glyphicon glyphicon-remove-sign" ></i></a>
                      </div>
                    <? }?>
                </td>
                
                <td><?=date('F d, Y',strtotime($row->timestamp))?></td>
                <!-- <td><a href="#" tag='view_d' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>" ><i class="icon-large icon-eye-open"></i></a></td> -->
                <td><a href="#" data-toggle="modal" data-target="#myModalatt" id="<?=$row->id?>" onclick="view_detail(this.id)"><i class="icon-large icon-eye-open"></i></a></td>
                <td <?=$bold?>><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                <td><?=$row->status?></td>                
                <td><a href="#" tag='att_report' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>" ><i class="icon-large icon-briefcase"></i></a></td>
                <td class="par"><?=($row->dateattached ? date("F d, Y",strtotime($row->dateattached)) : "")?></td>
                <td><?=$row->attstat?></td>
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
// new function for ica-hyperion 21128
// by : justin (with e)
function editSeminar(idkey){
  $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "seminarmodify", idkey: idkey},
        success: function(msg){
            $("#myModal").html(msg);
        }
  });
}
function deleteSeminar(idkey){
  var pmpt = confirm("Do you really want to delete this request?");
    if(pmpt){
        $.ajax({
            url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
            type     :   "POST",
            data     :   {model: "delSeminar", id: idkey},
            success  :   function(msg){
                alert(msg);
                location.reload();
            }
        });
    }
}
function view_detail(idkey){
  var form_data = {
                        idkey    : idkey,
                        job      : "view",
                        mod      : "emph",
                        folder   : "employeemod", 
                        view     : "mailseminarapp_view"
                    };
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
}
// end of new function for ica-hyperion 21128
$("a[tag='view_d']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        job      : "view",
                        mod      : "emph",
                        folder   : "employeemod", 
                        view     : "mailseminarapp_view"
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
$("a[tag='att_report']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey: idkey,
                        job : "add",
                        folder   : "employeemod", 
                        view     : "mailseminarapp_attach"
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
                        view     : "approval_list_seminar"
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
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "seminar_app"},
           success  : function(msg){
            //loadbushistory();
            location.reload();
           }
        });
});

$("#editrequest").click(function(){
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "seminarmodify", idkey: $(this).attr("bid")},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});
$("#delrequest").click(function(){
    var pmpt = confirm("Do you really want to delete this request?");
    if(pmpt){
        $.ajax({
            url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
            type     :   "POST",
            data     :   {model: "delSeminar", id: $(this).attr("bid")},
            success  :   function(msg){
                alert(msg);
                location.reload();
            }
        });
    }
});

// $(function(){
//    $(".par").each(function(){
//     if($(this).text() == "")    $("#newrequest").prop("disabled",true);
//    }); 
// });

$("#seminarh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$("#seminarh_length").append("<span style='margin-left: 45px;'>Status : <select id='seminarstatus' style='margin-bottom: 2px;'><?=$this->extras->showCategoryopt(($this->employeemod->seminarnotif()->num_rows() ? "APPROVED" : ""))?></select></div>");
$("#seminarstatus").change(function(){ loadbushistory($(this).val()); });
$(".no-sort").removeClass("sorting");
</script>