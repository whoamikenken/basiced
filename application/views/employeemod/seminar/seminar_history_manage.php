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
</style>
<div class="panel-heading" style="background-color: #0072c6;"><h4><b><?= ucfirst(strtolower($status)) ?> Application List</b></h4></div>
   <div class="panel-body">
   </div>
<div id="leavehistory" style="padding: 10px;">

    <?if($isHrHead){?>
        <hr>
        <br>
        <div class="form_row">
          <label class="field_name align_right">BATCH APPROVAL</label>
          <div class="field no-search">
              <select class="form-control" id="batch_category">
                  <option value="">Select approval status..</option>
                  <option value="APPROVED">APPROVED</option>
                  <option value="DISAPPROVED">DISAPPROVED</option>
              </select>
              &nbsp;
              <input type="button" class="btn btn-primary" id="save_batchapprove" value="SAVE">
              &nbsp;
              <span id="batch_errormsg" class="error-msg"></span>
          </div>
          
        </div>
    <?}?>


    <table class="table table-hover table-bordered datatable" id="leaveh">                                                     
        <thead>
            <tr style="background-color: #0072c6;">
                <?if($isHrHead){?>
                  <th class="no-sort" rowspan="2">Select All <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id="selectall" class="double-sized-cb"></th>
                <?}?>
                <th rowspan="2">Approval</th>
                <th rowspan="2" class="sorting_asc">Employee ID</th>
                <th rowspan="2">Full Name</th>
                <th rowspan="2">Department</th>
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
                                                <a class="btn btn-primary" href="#mymodalleave" tag='edit_d' data-toggle="modal" code="<?=$row->employeeid?>" idnum="<?=$row->aid?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" ><i class="glyphicon glyphicon-edit"></i></a>
                                			<?}else{?>
                                			     <a class="btn btn-primary" href="#mymodalleave" tag='view_d' data-toggle="modal" code="<?=$row->employeeid?>" idnum="<?=$row->aid?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" ><i class="icon-zoom-in"></i></a>
                                			<?}?>
                                			<?if($row->$list['colstatus'] == "APPROVED" && $row->deptid == "HR"){?>
                                                <a class="btn btn-primary" tag='cancel_d' code="<?=$row->employeeid?>" idnum="<?=$row->aid?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" ><i class="glyphicon glyphicon-remove-sign-sign"></i></a>
                                			
                                			<?}?>
                                            <!--<a class="btn" tag='delete_d' code="<?=$row->id?>" ><i class="glyphicon glyphicon-remove-sign-sign"></i></a>-->
                                          </div>
                                        </td>
                                        <td width="10%"><?=$row->employeeid?></td>
                                        <td><?=$row->fullname?></td>
                                        <td><?=$this->extensions->getDepartmentDescription($row->deptid)?></td>
                                        <td width="10%"><?=date('F d, Y',strtotime($row->date_applied))?></td>
                                        <td><?=date('F d, Y',strtotime($row->datesetfrom))?></td>
                                        <td><?=date('F d, Y',strtotime($row->datesetto))?></td>
                                        <td width="20%"><?=$row->remarks?></td>
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
                        view            : "seminar_details_manage"
                    }
    $.ajax({
       url      :   "<?=site_url("seminar_/getSeminarDetails")?>",
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
            alert(message);
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
            alert(message);
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
  if($('#batch_category').val() == ''){
    $('#batch_errormsg').html('Please select approval status.').css('color','red');
    return false;
  }

  if(checked_length > 0){
      $('#batch_errormsg').html('');
      $('input[name=multiple_approve]:checked').each(function(){
          $('#batch_errormsg').html("<img src='<?=base_url()?>images/loading.gif' /> Saving.. Please wait.").css('color','green');
          employeeid = $(this).attr('employeeid');
          base_id = $(this).attr('base_id');
          status = $('#batch_category').val();
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
              alert(msg.msg);
                $(".inner_navigation .main li .active a").click();
              }
          });    
  }else{
      $('#batch_errormsg').html('Please select application.').css('color','red');
  }

});

///< end for batch approving


$("#leaveh").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$('.chosen').chosen();
</script>