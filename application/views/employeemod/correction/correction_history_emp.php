<?php

/**
 * @author Justin
 * @copyright 2016
 */

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\offbusinesshistory.php
 */

$stat   = isset($stat) ? $stat : "";
// newly added variable for #ica-hyperion 21090 by justin (with e)
$otherType = array(
                    "ABSENT"          => "ABSENT",
                    "DIRECT"          => "OFFICIAL BUSINESS",
                    "CORRECTION"      => "CORRECTION OF TIME IN/OUT "
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
<table class="table table-hover table-bordered" id="offbus">
    <thead style="background-color: #0072c6;">
        <tr>
            <th rowspan="2">&nbsp;</th>
            <th rowspan="2">Date Applied</th>
            <th colspan="2">Inclusive Dates</th>
            <th rowspan="2">Reason</th>
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
        if(sizeof($ob_list) > 0){
    ?>
    <tbody>
        <?
            foreach($ob_list as $key => $row){
               $att_confirmed = $this->attendance->isAttendanceConfirmed($row->employeeid, $row->datefrom)->num_rows();
                $bold = $row->isread ? "" : "style='font-weight: bold;'";
        ?>
            <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : "")?>>
                <td>
                    <div class="btn-group">
                        <!-- <a class="btn btn-primary" id="editrequest" href="#" data-toggle="modal" data-target="#myModal" idkey="<?=$row->id?>"><i class="icon glyphicon glyphicon-edit"></i></a> -->
                        <?
                          $showEditRemoveBtn = true;
                          if($row->dstatus == "APPROVED" || $row->cstatus == "APPROVED" || $row->hrstatus == "APPROVED" || $row->cpstatus == "APPROVED" || $row->fdstatus == "APPROVED" || $row->bostatus == "APPROVED" || $row->pstatus == "APPROVED" || $row->upstatus == "APPROVED" || $row->status == "DISAPPROVED" || $row->status == "APPROVED") $showEditRemoveBtn = false;

                         if($showEditRemoveBtn && $row->status != "CANCELLED"){
                      ?>
                          <a class="btn btn-info" style="margin-right: 2px;" data-toggle="modal" data-target="#myModal" id="<?php echo $row->leaveid?>" onclick="editRequest(this.id)"><i class="icon glyphicon glyphicon-edit"></i></a>
                          <a class="btn btn-danger" href="#" id="<?php echo $row->leaveid?>" onclick="delRequest(this.id)"><i class="icon glyphicon glyphicon-trash"></i></a>
                      <?php } elseif($row->status != 'CANCELLED' && $row->status != 'DISAPPROVED' && $att_confirmed == 0){?>
                        <a class="btn btn-danger cancelrequest" id="cancelrequest" href="#" idkey="<?php echo $row->leaveid?>" ><i class=" glyphicon glyphicon-ban-circle"></i><b style="font-size: 90%;">&nbsp;&nbsp;Cancel</b></a>
                      <?php } ?>
                    </div>
                </td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->date_applied))?></td>
                <td <?=$bold?>><?=date('F d, Y',strtotime($row->datefrom))?></td>
                <td <?=$bold?> ><?=date('F d, Y',strtotime($row->dateto))?></td>
                <td <?=$bold?>><?=$row->reason?></td>
                <td><a href="#" tag='view_d' data-toggle="modal" data-target="#myModal" code="<?=$row->employeeid?>" idnum="<?=$row->leaveid?>" ><i class="icon-large icon-eye-open"></i></a></td>
                <td><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalleave" idkey="<?=$row->leaveid?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td>
                <td <?=$bold?>><?=$row->status?></td>
                <td width="1%"><input type="checkbox" value="1" name="mar" idkey="<?=$row->leaveid?>" <?=($row->isread ? " checked disabled" : "")?> /></td>
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
                        code            : code,
                        idkey           : idnum,
                        job             : "view",
                        view            : "ob_details_emp"
                    }
    $.ajax({
       url      :   "<?=site_url("ob_application_/getLeaveDetails")?>",
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
                        idkey    : idkey
                    };
    $.ajax({
       url      :   "<?=site_url("ob_application_/getApprovalSeqStatus")?>",
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
           data     :   {model : "markasread", id : idkey , val : cval, tbl : "ob_app_emplist"},
           success  : function(msg){
            loadbushistory('',0,'load');
            getUpdatedNotification("CORRECTION");
            getUpdatedManageNotification();
           }
        }); 
});

function editRequest(idKey){
  // displayed edit modal
  $("#myModal").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");

  var form_data = {
                        idkey           : idKey,
                        // baseid          : base_id,
                        target          : 'CORRECTION',
                        job             : "view",
                        view            : "correction_apply"
                    };
  $.ajax({
        url      :   "<?=site_url("ob_application_/getLeaveDetails")?>",
        type     : "POST",
        data     : form_data,
        success: function(msg){
            $("#myModal").html(msg);
        }
    }); 
}
function delRequest(idKey){
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
        url      :   "<?=site_url("ob_application_/deleteCorrectionApp")?>",
        type     :   "POST",
        data     :   {id: idKey},
        success  :   function(msg){
           Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: msg,
              showConfirmButton: true,
              timer: 1000
          })
            loadbushistory('',0,'load');
        }
       }); 
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Correction for Time In/Out Application is safe.',
         'error'
       )
     }
   })
}

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
              url      :   "<?php echo site_url("ob_application_/cancelOBApp")?>",
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

$("#offbus").dataTable({
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
$("#offbus_length").append("<span style='margin-left: 45px;'>Status : <select id='leavestatus' class='form-control' style='margin-bottom: 2px; width:220px;'><?=$this->extras->showCategoryopt($stat)?></select></div>");
$("#leavestatus").change(function(){ loadbushistory($(this).val()); });

function getUpdatedNotification(module){
  $.ajax({
    url: "<?=site_url('utils_/getUpdatedNotification')?>",
    type: "POST",
    data: {module:module},
    success:function(response){
      $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifcount").text(response);
      if(response == 0){
        $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifdiv").hide();
      }
    }
  });
}

function getUpdatedManageNotification(module){
      $.ajax({
        url: "<?=site_url('utils_/getUpdatedManageNotification')?>",
        type: "POST",
        data: {module:module},
        success:function(response){
          $("a[menuid='78']").find(".notifcount").text(response);
          if(response == 0){
            $("a[menuid='78']").find(".notifdiv").hide();
          }
        }
      });
    }

</script>