<?php

$stat   = isset($stat) ? $stat : "";
$show   = false;
$utype = $this->session->userdata('usertype');
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
#overtimeh tr td,#overtimeh tr th{
    text-align: center;
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
 .swal2-cancel{
   margin-right: 20px;
 }
</style>

<table class="table table-hover table-bordered datatable" id="overtimeh">
    <thead style="background-color: #0072c6">
        <tr>
            <th rowspan="2">&nbsp;</th>
            <th rowspan="2">Employee ID</th>
            <th rowspan="2">Full Name</th>
            <th rowspan="2">Date Applied</th>
            <th colspan="2">Inclusive Dates</th>
            <th rowspan="2">Details</th>
            <th rowspan="2">Approving Authority</th>
            <th rowspan="2">Status</th>
            <th class="no-sort" rowspan="2">Mark as read <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id="selectall" class="double-sized-cb"></th>
        </tr>
        <tr>
            <th>From</th>
            <th>To</th>
        </tr>
    </thead>
    <?
        if($OT_list->num_rows() > 0){
    ?>
    <tbody>
        <?
            foreach($OT_list->result() as $row){
            $bold = $row->isread ? "" : "style='font-weight: bold;'";
        ?>
            <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : "")?>>
                <td>
                    <div class="btn-group" style=" <?= ($this->extensions->checkIfSecondApprover($row->otid, "overtime") && $utype == "EMPLOYEE") ? 'display:none' : '' ?>">
                        <a class="btn btn-info editrequest" id="editrequest" style="margin-right: 5px;" href="#" idkey="<?=$row->otid?>" data-toggle="modal" data-target="#myModalatt" ><i class="icon glyphicon glyphicon-edit" ></i></a>
                            <a class="btn btn-danger delrequest" id="delrequest" href="#" idkey="<?=$row->otid?>" ><i class="icon glyphicon glyphicon-trash"></i></a>
                        <!-- <a class="btn btn-primary" id="editrequest" href="#" data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->id?>"><i class="icon glyphicon glyphicon-edit"></i></a> -->
                        <?
                          #check if this was already approved or disapproved, then disabled the delete request button
                          if($row->status == 'PENDING' && $row->dstatus == 'PENDING' && $row->cstatus == 'PENDING' && $row->cstatus == 'PENDING' && $row->hrstatus == 'PENDING' && $row->cpstatus == 'PENDING' && $row->fdstatus == 'PENDING' && $row->bostatus == 'PENDING' && $row->pstatus == 'PENDING' && $row->upstatus == 'PENDING'){
                        ?>
<!--                             <a class="btn btn-info editrequest" style="margin-right: 15px;" href="#" idkey="<?=$row->otid?>" data-toggle="modal" data-target="#myModalatt"><i class="icon glyphicon glyphicon-edit"></i></a>
                            <a class="btn btn-danger delrequest" href="#" idkey="<?=$row->otid?>" ><i class="icon glyphicon glyphicon-trash"></i></a> -->
                        <?}?>
                    </div>
                </td>
                <td <?=$bold?>><?=$row->employeeid?></td>
                <td <?=$bold?>><?=$row->fullname?></td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->date_applied))?></td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->dfrom))?></td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->dto))?></td>
                <td><a href="#" tag='view_d' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->otid?>" ><i class="icon-large icon-eye-open"></i></a></td>
                <td><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->otid?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                <td <?=$bold?>><?=$row->status?></td>
			         <td width="1%"><input type="checkbox" value="1" name="mar" idkey="<?=$row->otid?>" <?=($row->isread ? " checked disabled" : "")?> /></td>
            </tr> 
        <?
            }
        ?>
    </tbody>
    <?
        }
    ?>
    
</table>
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
       url      :   "<?=site_url("overtime_/getOTDetails")?>",
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
       url      :   "<?=site_url("overtime_/getApprovalSeqStatus")?>",
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
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "ot_app_emplist"},
           success  : function(msg){
            //loadbushistory();
            loadOvertimehistory("","","",0,'load');
           }
        }); 
});
$(".editrequest").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");

    var form_data = {
                        idkey    : idkey
                    };
    $.ajax({
       url      :   "<?=site_url("overtime_/loadApplyOTForm")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalatt").html(msg);
       }
    });
});
$(".delrequest").click(function(){
   const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "Do you really want to delete this request?",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
       $.ajax({
        url      :   "<?=site_url("overtime_/deleteOTApp")?>",
        type     :   "POST",
        data     :   {id: $(this).attr("idkey")},
        success  :   function(msg){
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: msg,
              showConfirmButton: true,
              timer: 1000
          })
            loadOvertimehistory("","","",0,'load');
        }
       }); 
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Ovetime application is safe.',
         'error'
       )
     }
   })
});

$('#selectall').on('click',function(){
  $('input[name=mar]').prop('checked',true);
  $('input[name=mar]').click();
});

$("#overtimeh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    "columnDefs": [
      { "type": "date", "targets": [4] },
      { "type": "date", "targets": [5] }
    ]
});
$("#overtimeh_length").append("<span style='margin-left: 45px;'>Status : <select id='overtimeStatus' class='form-control' style='margin-bottom: 2px; width:220px;'><?=$this->extras->showCategoryopt($stat)?></select></div>");
$("#overtimeStatus").change(function(){ loadOvertimehistory($(this).val()); });
</script>