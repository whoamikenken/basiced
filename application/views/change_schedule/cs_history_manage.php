<?php
/**
 * @modified Angelica Arangco  2017
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
<div class="panel-heading" style="background-color: #0072c6;"><h4><b><?= ucfirst(strtolower($status)) ?> Application List</b></h4></div>
<div class="panel-body">
   <?if($isHrHead){?>
        <hr>
        <div class="row">
          <div class="col-md-12" style="margin-left: 25%;">
            <div class="col-md-6" style="padding-right: 0px;width: 32%;">
                <label style="display: inline;">BATCH APPROVAL: &nbsp;&nbsp;</label>
                <select class="form-control" id="batch_category" style="display: inline;width: 65%;">
                    <option value="">Select approval status..</option>
                    <option value="APPROVED">NOTED</option>
                    <option value="DISAPPROVED">DISAPPROVED</option>
                </select>
            </div>
            <div class="col-md-6">
              <input type="button" class="btn btn-primary" id="save_batchapprove" value="SAVE">
              <span id="batch_errormsg" class="error-msg"></span>
            </div>
          </div>
          
        </div>
        <br>
    <?}?>

<table class="table table-hover table-bordered datatable" id="seminarh">
    <thead>
        <tr style="background-color: #0072c6;">
            <?if($isHrHead){?>
              <th class="no-sort">Select All <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id="selectall" class="double-sized-cb"></th>
            <?}?>
            <th>Approval</th>
            <th>Employee ID</th>
            <th>Full Name</th>
            <th>Type</th>
            <th>Date Applied</th>
            <th>Effectivity Date</th>                        
            <th>Reason</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody id="manageot">                                                               
            <?  
                foreach ($cs_list as $key => $list) {

                    if(sizeof($list['data_list']) > 0 /*|| $employeeot_h->num_rows() > 0*/){
                      $row = $list['data_list'];
                        // foreach($list['data_list'] as $row){
                            // echo '<pre>';print_r($row);
                              $bold = $row->isread ? "" : "style='font-weight: bold;'";
                          ?>
                              <tr <?=(!$row->isread ? " style='background: #B4CDC6'" : "")?>>

                                  <?if($isHrHead){?>
                                      <td>
                                        <input type='checkbox' name='multiple_approve' class="double-sized-cb" idkey="<?=$row->csid?>" base_id="<?=$row->base_id?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" employeeid="<?=$row->employeeid?>" >
                                      </td>
                                  <?}?>

                                  <td>
                                  <?php if($row->$list['colstatus'] == "PENDING"): ?>
                                    <a class="btn btn-info" tag='view_d' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->csid?>" base_id="<?=$row->base_id?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" ><i class="glyphicon glyphicon-edit"></i></a>
                                  <?php endif ?>
                                  </td>
                                  <td><?=$row->employeeid?></td>
                                  <td><?=$this->employee->getfullname($row->employeeid)?></td>
                                  <td><?=$this->extensions->getEmployeeTeachingType($row->employeeid)?></td>
                                  <td><?=date("F d, Y",strtotime($row->date_applied))?></td>
                                  <td><?=date("F d, Y",strtotime($row->date_effective))?></td>
                                  <!-- <td><a href="#" tag='view_app' data-toggle="modal" data-target="#myModalatt" idkey="<?=$row->csid?>" title="View Approval Status" ><i class="icon-large icon-eye-open"></i></a></td> -->
                                  <td><?=$row->getReason?></td>
                                  <td><?=$row->$list['colstatus']?></td>
                                  <!-- <td width="1%"><input class="double-sized-cb" type="checkbox" value="1" name="mar" idkey="<?=$row->csid?>" <?=($row->isread ? " checked disabled" : "")?> /></td> -->
                              </tr>   
                          <?
                        // } //end foreach
                    } //endif
                } //end foreach cs_list
                ?>
    </tbody>

    
</table>
<div class="modal fade" id="myModalatt" data-backdrop="static"></div>
<script>
$("a[tag='view_d']").click(function(){
    if($(this).attr("idkey")) idkey = $(this).attr("idkey");
    if($(this).attr("base_id")) base_id = $(this).attr("base_id");

    var colhead = $(this).attr('colhead'),
        colstatus = $(this).attr('colstatus'),
        isLastApprover = $(this).attr('isLastApprover'),
        code_request    = $(this).attr('code_request');

    var form_data = {
                        idkey           : idkey,
                        baseid          : base_id,
                        colhead         : colhead,
                        isLastApprover  : isLastApprover,
                        code_request    : code_request,
                        job             : "edit",
                        view            : "cs_details_manage"
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
$("a[tag='view_app']").click(function(){
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

  if($('#batch_category').val() == ''){
    $('#batch_errormsg').html('Please select approval status.').css('color','red');
    return false;
  }

  if(checked_length > 0){
      $('#batch_errormsg').html('');
      $('input[name=multiple_approve]:checked').each(function(){
         

          $('#batch_errormsg').html("<img src='<?=base_url()?>images/loading.gif' /> Saving.. Please wait.").css('color','green');

          var employeeid = $(this).attr('employeeid');

           var   form_data = "status=" + $('#batch_category').val();
                 form_data += "&colhead="+ $(this).attr('colhead');
                 form_data += "&isLastApprover="+ $(this).attr('isLastApprover');
                 form_data += "&code_request="+ $(this).attr('code_request');
                 form_data += "&csid="+ $(this).attr('idkey');
                 form_data += "&base_id="+ $(this).attr('base_id');
                 form_data += "&employeeid="+ employeeid;
                 form_data += "&isBatchApprove=1";
          

          $.ajax({
              url:"<?=site_url("schedule_/saveSchedStatusChange")?>",
              type:"POST",
              dataType : 'JSON',
              data:form_data,
              success: function(msg){
                 loopcounter++;
                  if(msg.err_code == 0){
                    success_emp += success_emp ? ',':'';
                    success_emp += employeeid;
                  }else{
                    failed_emp += failed_emp ? ',':'';
                    failed_emp += employeeid;
                  }

                  if(loopcounter == checked_length){
                      failed_emp = failed_emp ? 'Failed to save for employee #: ' + failed_emp :'';

                      if(failed_emp){
                        alert(failed_emp);
                      }else{
                        alert(msg.msg);
                      }
                      $(".inner_navigation .main li .active a").click();

                  }
              }
          });
        
      }); ///< end loop checked

  }else{
      $('#batch_errormsg').html('Please select application.').css('color','red');
  }

});

///< end for batch approving





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
// $("#seminarh_length").append("<span style='margin-left: 45px;'>Status : <select id='changesched' class='form-control' style='margin-bottom: 2px;width:200px;'><?=$this->extras->showCategoryopt(($this->employeemod->seminarnotif()->num_rows() ? "APPROVED" : ""))?></select></div>");
// $("#changesched").change(function(){
    // $("#seminarh_length").append("<a href='#' style='margin-left: 45px;' class='btn blue' id='search'>Save</a>");
   // changesched($(this).val());

//    });
$(".no-sort").removeClass("sorting");

$('.chosen').chosen();
</script>