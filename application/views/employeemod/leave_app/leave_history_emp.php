<?php

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\leavehistory.php
 */

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
    color: #000000;
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
<div class="panel animated fadeIn">
    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Leave Application History</b></h4></div>
    <div id="leavecontent" class="panel-body" style="padding-bottom: 32px;">  
        <table class="table table-striped table-bordered table-hover" id="leaveh" style="width: 100%;">
            <thead>
                <tr style="background-color: #0072c6">
                    <th rowspan="2" class="mh" >&nbsp;</th>
                    <th rowspan="2">Date Applied</th>
                    <th colspan="2">Inclusive Dates</th>
                    <th rowspan="2">Type</th>
                    <th rowspan="2">Details</th>
                    <th rowspan="2">Approving Authority</th>
                    <th rowspan="2">Status</th>
                    <th class="no-sort" rowspan="2">Mark as read <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id="selectall" class="double-sized-cb"></th>
                </tr>
                <tr style="background-color: #0072c6">
                    <th>From</th>
                    <th>To</th>
                </tr>
            </thead>
            <?php
            if(sizeof($leave_list) > 0){
            ?>
            <tbody>
                <?php
                    foreach($leave_list as $key => $row){
                      $att_confirmed = $this->attendance->isAttendanceConfirmed($row->employeeid, $row->datefrom)->num_rows();
                        $nomodif = false;                
                        $bold = $row->isread ? "" : "style='font-weight: bold;'";
                        if($row->dstatus == "APPROVED" || $row->cstatus == "APPROVED" || $row->hrstatus == "APPROVED" || $row->cpstatus == "APPROVED" || $row->fdstatus == "APPROVED" || $row->bostatus == "APPROVED" || $row->pstatus == "APPROVED" || $row->upstatus == "APPROVED" || $row->status == "DISAPPROVED" || $row->status == "APPROVED") $nomodif = true;
                        else $show = true;
                        ?>
                        <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : ($row->status == "CANCELED" ? " style='background: #ffcccc'" : ""))?>>
                            <td class="mh">
                                <?php if(!$nomodif && $row->status != 'CANCELLED'){
                                  ?>                
                                <div >
                                    <a class="btn btn-info" tag="editrequest" href="#" data-toggle="modal" data-target="#mymodalleave" style="margin-right: 2px;" idkey="<?php echo $row->leaveid?>"  base_id="<?php echo $row->base_id?>" ><i class="icon glyphicon glyphicon-edit"></i></a>
                                    <a class="btn btn-danger" tag="delrequest" href="#" idnum="<?php echo $row->leaveid?>"  base_id="<?php echo $row->base_id?>" ><i class="icon glyphicon glyphicon-trash"></i></a>
                                </div>
                                <?php } elseif($row->status != 'CANCELLED' && $row->status != 'DISAPPROVED' && $row->status != 'APPROVED' && $att_confirmed == 0){?> 
                                  <a class="btn btn-danger cancelrequest" id="cancelrequest" href="#" idkey="<?php echo $row->leaveid?>" ><i class=" glyphicon glyphicon-ban-circle"></i><b style="font-size: 90%;">&nbsp;&nbsp;Cancel</b></a>
                                <?php } ?>                     
                            </td>
                            <td <?=$bold?>><?=date('F d, Y',strtotime($row->date_applied))?></td>
                            <td <?=$bold?>><?=date('F d, Y',strtotime($row->datefrom))?></td>
                            <td <?=$bold?> ><?=date('F d, Y',strtotime($row->dateto))?></td>
                            <td <?=$bold?>><?=$this->employeemod->othLeaveDesc(($row->type == "other" ? $row->other : $row->type))?></td>
                            <td><a href="#" tag='view_d' data-toggle="modal" data-target="#mymodalleave" code="<?=$row->employeeid?>" idkey="<?=$row->leaveid?>" ><i class="icon-large icon-eye-open"></i></a></td>
                            <td><a href="#" tag='view_app' data-toggle="modal" data-target="#mymodalleave" idkey="<?=$row->leaveid?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                            <td <?=$bold?>><?=$row->status?></td>
                            <td width="1%"><input type="checkbox" value="1" name="mar" idkey="<?=$row->leaveid?>"  <?=($row->isread ? " checked disabled" : "")?>  /></td>
                        </tr>   
                        <?
                    }
                ?>
            </tbody>
            <?php } ?>

        </table>
    </div>
</div>

<?if($show){?><script>$(".mh").show();</script><?}?>
<script>
var toks = hex_sha512(" ");
$("a[tag='view_d']").click(function(){
    var base_id = "";  
    var idkey = "";  
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        toks            : toks,
                        idkey           : GibberishAES.enc(idkey, toks),
                        baseid          : GibberishAES.enc(base_id, toks),
                        job             : GibberishAES.enc("view", toks),
                        view            : GibberishAES.enc("leave_details_emp", toks)
                    };
    $.ajax({
       url      :   "<?=site_url("leave_application_/getLeaveDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#mymodalleave").html(msg);
       }
    }); 
});
$("a[tag='view_app']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        toks:toks,
                        idkey    : GibberishAES.enc(idkey, toks)
                    };
    $.ajax({
       url      :   "<?=site_url("leave_application_/getApprovalSeqStatus")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#mymodalleave").html(msg);
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
     data     :   {model : "markasread", id : idkey , val : cval, tbl : "leave_app_emplist"},
     success  : function(msg){
      loadleavehistory('',0,'load'); 
     }
  }); 
});
$("a[tag='editrequest']").click(function(){
   var base_id = "";  
    var idkey = "";  
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    var form_data = {
                        toks            : toks,
                        idkey           : GibberishAES.enc(idkey, toks),
                        baseid          : GibberishAES.enc(base_id, toks),
                        job             : GibberishAES.enc("view", toks),
                        view            : GibberishAES.enc("leave_apply", toks)
                    };
    $.ajax({
       url      :   "<?=site_url("leave_application_/getLeaveDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#mymodalleave").html(msg);
       }
    }); 
});
$("a[tag='delrequest']").click(function(){
   // var pmpt = confirm("Do you really want to delete this request?");
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
        url      :   "<?=site_url("leave_application_/deleteLeaveApp")?>",
        type     :   "POST",
        data     :   {id: $(this).attr("idnum")},
        success  :   function(msg){
            Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: msg,
              showConfirmButton: true,
              timer: 1000
          })
            loadleavehistory('',0,'load'); 
        }
       });
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Leave Application is safe.',
         'error'
       )
     }
   })
});

$(".cancelrequest").click(function(){
   const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
      if (result.value) {
            $.ajax({
              url      :   "<?php echo site_url("leave_application_/cancelLeaveApp")?>",
              type     :   "POST",
              data     :   {id: $(this).attr("idkey")},
              dataType : "json",
              success  :   function(response){
                  Swal.fire({
                        icon: response.icon,
                        title: response.title,
                        text: response.msg,
                        showConfirmButton: true,
                        timer: 1500
                  });
                  if(response.icon == "success") setTimeout(function(){ location.reload(); }, 1500);
              }
             }); 
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Application is safe.',
                'error'
            )
        }
    });
});

$('#selectall').on('click',function(){
  $('input[name=mar]').prop('checked',true);
  $('input[name=mar]').click();
});

$("#leaveh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
    "columnDefs": [
      { "type": "date", "targets": 1 },
      { "type": "date", "targets": 2 },
      { "type": "date", "targets": 3 }
    ]
});
$("#leaveh_length").append("<span style='margin-left: 45px;'>Status : <select id='leavestatus' class='form-control' style='margin-bottom: 2px; width:220px;'><?=$this->extras->showCategoryopt($stat)?></select></div>");
$("#leavestatus").change(function(){ loadleavehistory($(this).val()); });
</script>