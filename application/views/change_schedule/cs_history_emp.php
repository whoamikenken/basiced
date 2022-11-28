<?php
/**
 * @author Angelica Arangco
 * @copyright 2017
 */
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
#seminarh tr td,#seminarh tr th{
    text-align: center;
}
</style>
<table class="table table-hover table-bordered datatable" id="seminarh">
    <thead  style="background-color: #0072c6; ">
        <tr>
            <th>Actions</th>
            <th>Date Applied</th>
            <th>Employee ID</th>
            <th>Full Name</th>
            <th>Type</th>
            <th>Effectivity Date</th>                        
            <th>Details</th>
            <th>Approving Authority</th>
            <th>Status</th>
            <th>Mark as read</th>
        </tr>
    </thead>
    <?
        // $query = $this->employeemod->displayschedrequesthistory($category,$this->input->post("indi"));
        if($cs_list->num_rows() > 0){
    ?>
    <tbody>
        <?
            foreach($cs_list->result() as $row){
            $bold = $row->isread ? "" : "style='font-weight: bold;'";
        ?>
            <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : "")?>>
                <td >
                  <?php if ($row->status == 'PENDING' && ($row->dstatus == 'PENDING' || $isHead) && $row->cstatus == 'PENDING' && $row->cstatus == 'PENDING' && $row->hrstatus == 'PENDING' && $row->cpstatus == 'PENDING' && $row->fdstatus == 'PENDING' && $row->bostatus == 'PENDING' && $row->pstatus == 'PENDING' && $row->upstatus == 'PENDING' ): ?>
                    <div class="btn-group">
                  <a class="btn btn-info editbtn" href="#modal-view" data-toggle="modal" style="margin-right: 15px;"  data-target="#myModalatts" idkey="<?=$row->csid?>" base_id="<?=$row->base_id?>"><i class="glyphicon glyphicon-edit"></i></a>
                   <a class="btn btn-danger delbtn"  idnumber="<?=$row->id?>"><i class="glyphicon glyphicon-trash"></i></a>
                </div>
                  <?php endif ?>
                  </td>
                <td><?=date("F d, Y",strtotime($row->date_applied))?></td>
                <td><?=$row->employeeid?></td>
                <td><?=$this->employee->getfullname($row->employeeid)?></td>
                <td></td>
                <td><?=date("F d, Y",strtotime($row->date_effective))?></td>
                <td><a href="#" tag='view_d' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->csid?>" base_id="<?=$row->base_id?>"><i class="icon-large icon-eye-open"></i></a></td>
                <td><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->csid?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                <td><?=$row->status?></td>
                <td width="1%"><input class="double-sized-cb" type="checkbox" value="1" name="mar" idkey="<?=$row->csid?>" <?=($row->isread ? " checked disabled" : "")?> /></td>
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
<div class="modal fade" id="myModalatts" data-backdrop="static"></div>
<script>
$(".delbtn").click(function()
  {
    var id = $(this).attr('idnumber');
    var ans = confirm("Are you sure do you want to delete?");
    if (ans) {
        $.ajax({
          url: "<?=site_url("schedule_/SCHEDactions")?>",
          type:"POST",
          data: {id:id,job:"delete"},
          success: function(msg)
          {
            alert(msg);
            location.reload();
          }

        });
    }

  });

$("a[tag='view_d']").click(function(){

    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");
    var form_data = {
                        idkey           : idkey,
                        baseid          : base_id,
                        job             : "view",
                        view            : "cs_details_emp"
                    };
    $.ajax({
       url      :   "<?=site_url("schedule_/getSchedDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});
$(".editbtn").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");
    var form_data = {
                        idkey           : idkey,
                        baseid          : base_id,
                        job             : "edit",
                        view            : "cs_details_modify"
                    };
    // console.log(form_data);return;
    $.ajax({
       url      :   "<?=site_url("schedule_/getSchedDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatts").html(msg);
       }
    });
});
$("a[tag='view_app']").click(function(){
   // alert('yes');
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        idkey    : idkey,
                        folder   : "employeemod", 
                        view     : "changesched_approval_list"
                    };
    $.ajax({
       url      :   "<?=site_url("schedule_/getApprovalSeqStatus")?>",
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
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "change_sched_app_emplist"},
           success  : function(msg){
            location.reload();
           }
        });
});
$(function(){
   $(".par").each(function(){
    if($(this).text() == "")    $("#newrequest").prop("disabled",true);
   }); 
});

$("#seminarh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$("#seminarh_length").append("<span style='margin-left: 45px;'>Status : <select id='seminarstatus' class='form-control' style='margin-bottom: 2px; width:220px;'><?=$this->extras->showCategoryopt($status)?></select></div>");
$("#seminarstatus").change(function(){ changesched('','',$(this).val(),''); });
$(".no-sort").removeClass("sorting");
</script>