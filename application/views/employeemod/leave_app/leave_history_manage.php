<?php

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\mailleaveapp_details.php
 */

// echo '<pre>';print_r($leave_list);die;
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
#leaveh tr td,#leaveh tr th{
    text-align: center;
}
#leaveh_wrapper{
  border:0px;
}
</style>
<div class="panel-heading"><h4><b><?= ucfirst(strtolower($status)) ?> Application List</b></h4></div>
   <div class="panel-body" style="padding: 0px;">
    <div id="leavehistory" style="padding: 1%;">

        <?if($isHrHead){?>
            <div class="row">
              <div class="col-md-12" style="margin-left: 25%;">
                <div class="col-md-12" style="padding-right: 0px;width: 50%;">&nbsp;&nbsp;
                    <label style="display: inline;font-size: 16px;">BATCH APPROVAL: &nbsp;&nbsp;</label>
                    <!-- <select class="form-control" id="batch_category" style="display: inline;width: 65%;">
                        <option value="">Select approval status..</option>
                        <option value="APPROVED">NOTED</option>
                        <option value="DISAPPROVED">DISAPPROVED</option>
                    </select> -->
                    <!-- &nbsp;&nbsp;<input type="radio" name="batch_category" value="">PENDING &nbsp;&nbsp; -->
                    &nbsp;&nbsp;<input type="radio" name="batch_category" value="APPROVED" style="display: inline;">&nbsp;&nbsp;<span style="font-size: 15px;">NOTED</span>&nbsp;&nbsp;
                    &nbsp;&nbsp;<input type="radio" name="batch_category" value="DISAPPROVED" style="display: inline;">&nbsp;&nbsp;<span style="font-size: 15px;">DISAPPROVED</span>&nbsp;&nbsp;
                    &nbsp;&nbsp;<input type="button" class="btn btn-primary" id="save_batchapprove" value="SAVE">&nbsp;&nbsp;
                    &nbsp;&nbsp;<span id="batch_errormsg" class="error-msg"></span>
                </div>
              </div>
              
            </div>
            <br>
        <?}?>

        <div class="row">
          <div class="col-md-12">
            <div class="col-md-6">
              <div class="col-md-12" style="padding-right: 0px;margin-left: 15%;">
                <div class="col-md-2" style="padding-right: 0px;margin-top: 1%;position: unset!important">
                    <label style="display: inline;font-size: 15px;">Department: &nbsp;&nbsp;</label>
                </div>
                <div class="col-md-6">
                  <select class="chosen" id="ob_deptid" style="display: inline;">
                      <option value="">Select all department</option>
                      <?php foreach($this->extras->showdepartment() as $key => $desc): ?>
                        <option value="<?=$key?>" <?=$key==$deptid ? "selected" : ""?>><?=$desc?></option>
                      <?php endforeach ?>
                    </select>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-12" style="padding-left: 0px;">
                <div class="col-md-2" style="padding-right: 0px;margin-top: 1%;position: unset!important">
                    <label style="display: inline;font-size: 15px;">Office: &nbsp;&nbsp;</label>
                </div>
                <div class="col-md-6">
                  <select class="chosen" id="ob_office" style="display: inline;">
                      <option value="">Select all office</option>
                      <?php foreach($this->extras->showoffice() as $key => $desc): ?>
                        <option value="<?=$key?>" <?=$key==$office ? "selected" : ""?>><?=$desc?></option>
                      <?php endforeach ?>
                    </select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <table class="table table-hover table-bordered datatable" id="leaveh">                                                     
            <thead>
                <tr style="background-color: #0072c6;">
                    <?if($isHrHead){?>
                      <th class="no-sort" rowspan="2">Select All <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id="selectall" class="double-sized-cb"></th>
                    <?}?>
                    <th rowspan="2">Action</th>
                    <th rowspan="2" class="sorting_asc">Employee ID</th>
                    <th rowspan="2">Full Name</th>
                    <th rowspan="2">Office</th>
                    <th rowspan="2">Leave Type</th>
                    <th rowspan="2">Date Applied</th>
                    <th colspan="2">Inclusive Dates</th>
                    <th rowspan="2">Reason</th>
                    <th rowspan="2">Status</th>

                </tr>
                <tr style="background-color: #0072c6;">
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody id="manageleave">                                                               
                    <?
                    foreach ($leave_list as $key => $list) {
             
                        if(sizeof($list['data_list']) > 0){
                            $row = $list['data_list'];
                         

                                    // foreach($list['data_list'] as $k => $row){
                        ?>
                                          <tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">

                                            <?if($isHrHead){?>
                                                <td width="10%">
                                                  <input type='checkbox' name='multiple_approve' class="double-sized-cb" idkey="<?=$row->aid?>" base_id="<?=$row->base_id?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" employeeid="<?=$row->employeeid?>" >
                                                </td>
                                            <?}?>


                                            <td class="align_center col-md-1">
                                              <div class="btn-group">
                                    			<?if($row->$list['colstatus'] != "CANCELED"){?>
                                                    <a class="btn btn-info" href="#mymodalleave" tag='edit_d' data-toggle="modal" code="<?=$row->employeeid?>" idnum="<?=$row->aid?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" ><i class="<?php echo  ($row->{$list['colstatus']} != 'PENDING') ? 'glyphicon glyphicon-eye-open' : 'glyphicon glyphicon-edit' ?>"></i></i></a>
                                    			<?}elseif($row->$list['colstatus'] == "APPROVED" && $row->deptid == "HR"){?>
                                                    <a class="btn btn-info" tag='cancel_d' code="<?=$row->employeeid?>" idnum="<?=$row->aid?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" ><i class="glyphicon glyphicon-remove-sign-sign"></i></a>
                                          
                                          <?}else{?>
                                    			     <a class="btn btn-info" href="#mymodalleave" tag='view_d' data-toggle="modal" code="<?=$row->employeeid?>" idnum="<?=$row->aid?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" ><i class="icon-zoom-in"></i></a>
                                    			<?}?> 
                                    			
                                                <!--<a class="btn" tag='delete_d' code="<?=$row->id?>" ><i class="glyphicon glyphicon-remove-sign-sign"></i></a>-->
                                              </div>
                                            </td>
                                            <td width="10%"><?=$row->employeeid?></td>
                                            <td><?=$row->fullname?></td>
                                            <td width="10%"><?=$this->extensions->getOfficeDescription($row->office)?></td>
                                            <td width="10%"><?=$this->employeemod->othLeaveDesc(($row->type == "other" ? $row->other : $row->type))?></td>
                                            <td><?=date('F d, Y',strtotime($row->date_applied))?></td>
                                            <td><?=date('F d, Y',strtotime($row->datefrom))?></td>
                                            <td><?=date('F d, Y',strtotime($row->dateto))?></td>
                                            <td width="20%"><?=$row->reason?></td>
                                            <td><?=$row->$list['colstatus']?></td>
                                          </tr>
                                    <?
                                    // } //end foreach
                        } //endif

                    } //end foreach leave_list
                    ?>
        </tbody>
        </table>
    </div>
  </div>
<div class="modal fade" id="mymodalleave" data-backdrop="static"></div>
<script>
$("a[tag='edit_d']").click(function(){
    var code = "";  
    var idnum = "";  
    if($(this).attr("code")) code = $(this).attr("code");
    if($(this).attr("idnum")) idnum = $(this).attr("idnum");
    
    var colhead = $(this).attr('colhead'),
        colstatus = $(this).attr('colstatus'),
        isLastApprover = $(this).attr('isLastApprover'),
        code_request    = $(this).attr('code_request');

    var form_data = {
                        code            : code,
                        idkey           : idnum,
                        colhead         : colhead,
                        isLastApprover  : isLastApprover,
                        code_request    : code_request,
                        job             : "edit",
                        folder          : "employeemod", 
                        view            : "leave_details_manage"
                    }
    $.ajax({
       url      :   "<?=site_url("leave_application_/getLeaveDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#mymodalleave").html(msg);
       }
    }); 
});
$("a[tag='delete_d']").click(function(){
    var id = $(this).attr("code");
    var confirmdel = confirm("Are you sure you want to delete this?");
    if(confirmdel == true){
        var form_data = {
                            code: id,
                            job : "delete",
                            folder   : "employeemod", 
                            view     : "mailleaveapp_manage"
                        }
        $.ajax({
        url      :   "<?=site_url("employeemod_/fileconfig")?>",
        type     :   "POST",
        data     :   form_data,
        success: function(msg){
            var message = $(msg).find("message").text();
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: msg.msg,
                showConfirmButton: true,
                timer: 1000
            });
            view_leave_status();
        }
    }); 
    }
});

$("a[tag='cancel_d']").click(function(){
    var employeeid = $(this).attr("code");
    var idnum = $(this).attr("idnum");
    var ltype = $(this).attr("ltype");
    var confirmcancel = confirm("Are you sure you want to cancel this?");
    if(confirmcancel == true){
        var form_data = {
                            employeeid: employeeid,
                            idnum: idnum,
                            ltype: ltype,
                            job : "cancel",
                            folder   : "employeemod", 
                            view     : "mailleaveapp_manage"
                        }
        $.ajax({
        url      :   "<?=site_url("employeemod_/fileconfig")?>",
        type     :   "POST",
        data     :   form_data,
        success: function(msg){
            var message = $(msg).find("message").text();
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: msg.msg,
                showConfirmButton: true,
                timer: 1000
            });
            view_leave_status();
        }
    }); 
    }
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


///< for batch approving

$('#selectall').on('click',function(){
  if($(this).is(':checked'))      $('input[name=multiple_approve]').prop('checked',true);
  else                            $('input[name=multiple_approve]').prop('checked',false);
});

$('input[name=multiple_approve]').on('click',function(){
  if(!$(this).is(':checked'))     $('#selectall').prop('checked',false);
});

$('#save_batchapprove').on('click',function(){

  var failed_emp = success_emp = '';
  var checked_length = $('input[name=multiple_approve]:checked').length;
  var loopcounter = 0;
  var result = "";
  var array = {};
  var leave_data = {};
  var employeeid = base_id = status = colhead = isLastApprover = code_request = leaveid = isBatchApprove = remarks = '';
  if($("input[name='batch_category']").is(':checked')){
  }else{
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: "Batch approval is required.",
        showConfirmButton: true,
        timer: 1000
    });
    return false;
  }

  if(checked_length > 0){
      $('#batch_errormsg').html('');
      $('input[name=multiple_approve]:checked').each(function(){
          $('#batch_errormsg').html("<img src='<?=base_url()?>images/loading.gif' /> Saving.. Please wait.").css('color','green');
          employeeid = $(this).attr('employeeid');
          base_id = $(this).attr('base_id');
          status = $("input[name='batch_category']:checked").val();
          colhead = $(this).attr('colhead');
          isLastApprover = $(this).attr('isLastApprover');
          code_request = $(this).attr('code_request');
          leaveid = $(this).attr('idkey');
          isBatchApprove = 1;  
          leave_data = {employeeid,base_id,status,colhead,isLastApprover,code_request,leaveid,isBatchApprove,remarks};

          array[base_id] = leave_data;
        
      }); ///< end loop checked
      // alert("Successfully Saved!");
  $.ajax({
              url:"<?=site_url("leave_application_/saveLeaveStatusChange")?>",
              type:"POST",
              dataType : 'JSON',
              data:array,
              success: function(msg){
              if(msg.err_code == 1){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 1000
                    });
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 1000
                    });
                }
                location.reload();
              }
          });    
  }else{
      Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: "Please select application.",
        showConfirmButton: true,
        timer: 1000
    });
    return false;
  }

});

///< end for batch approving

$("#ob_deptid, #ob_office").change(function(){
    var category = $("#category").val(), 
        dfrom    = $("input[name='ldfrom']").val(), 
        dto      = $("input[name='ldto']").val();
        deptid   = $("#ob_deptid").val();
        office   = $("#ob_office").val();
        
    view_leave_status("", "", "PENDING", deptid, office);
});


$("#leaveh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
}).css("overflow", "initial !important");
$('.chosen').chosen();
</script>