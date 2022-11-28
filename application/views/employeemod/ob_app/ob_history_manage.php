<?php

/**
 * @author Justin
 * @copyright 2016
 */

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\mailoffbusiness_details.php
 */

$otherType = array(
                    "ABSENT"          => "ABSENT",
                    "DIRECT"          => "OFFICIAL BUSINESS",
                    "CORRECTION"    => "CORRECTION OF TIME IN/OUT "
                  );
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
#offbush tr td,#offbush tr th{
    text-align: center;
}
</style>
<div class="panel-heading" style="background-color: #0072c6;"><h4><b><?= ucfirst(strtolower($status)) ?> Application List</b></h4></div>
  <div class="panel-body" style="padding: 0px;">
    <div id="leavehistory" style="padding: 1%;">
        <?if($isHrHead){?>
            <hr>
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
            <div class="col-md-2" style="padding-right: 0px;margin-top: 1%;">
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
            <div class="col-md-2" style="padding-right: 0px;margin-top: 1%;position: unset;">
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

    <table class="table table-hover table-bordered datatable" id="offbush">                                                     
        <thead>
            <tr style="background-color: #0072c6;">
                <?if($isHrHead){?>
                  <th class="no-sort" rowspan="2">Select All <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id="selectall" class="double-sized-cb"></th>
                <?}?>
                <th rowspan="2">Action</th>
                <th rowspan="2" class="sorting_asc">Employee ID</th>
                <th rowspan="2">Full Name</th>
                <th rowspan="2">Office</th>
                <!-- <th rowspan="2">Leave Type</th> -->
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
                    // echo '<pre>';print_r($row);
                    // foreach($list['data_list'] as $row){
                ?>
	
                      <tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">

                        <?if($isHrHead){?>
                            <td>
                              <input type='checkbox' name='multiple_approve' class="double-sized-cb" idkey="<?=$row->aid?>" base_id="<?=$row->base_id?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" employeeid="<?=$row->employeeid?>" timefrom="<?=$row->timefrom?>" timeto="<?=$row->timeto?>" >
                            </td>
                        <?}?>

                        <td class="align_center col-md-1">
                          <div class="btn-group">
                            <a class="btn btn-info" href="#mymodalleave" tag='edit_d' data-toggle="modal" code="<?=$row->employeeid?>" idnum="<?=$row->aid?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>"  ><i class="<?php echo  ($row->{$list['colstatus']} != 'PENDING') ? 'glyphicon glyphicon-eye-open' : 'glyphicon glyphicon-edit' ?>"></i></a>
                            <!--<a class="btn" tag='delete_d' code="<?=$row->id?>" ><i class="glyphicon glyphicon-remove-sign-sign"></i></a>-->
                          </div>
                        </td>
                        <td><?=$row->employeeid?></td>
                        <td><?=$row->fullname?></td>
                        <td><?=$this->extensions->getOfficeDescription($row->office)?></td>
                        <!-- <td><?=$otherType[$row->type]?></td> -->
                        <td><?=date('F d, Y',strtotime($row->date_applied))?></td>
                        <td><?=date('F d, Y',strtotime($row->datefrom))?></td>
                        <td><?=date('F d, Y',strtotime($row->dateto))?></td>
                           <td><?=$row->reason?></td>
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
                        view            : "ob_details_manage"
                    }
					
    $.ajax({
        url      :   "<?=site_url("ob_application_/getLeaveDetails")?>",
        type     : "POST",
        data     : form_data,
        success: function(msg){
            $("#mymodalleave").html(msg);
        }
    });
});
$("a[tag='delete_d']").click(function(){
    var id = $(this).attr("code");
    const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   })

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "Are you sure you want to delete this?",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
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
                    text: message,
                    showConfirmButton: true,
                    timer: 1000
                })
                view_offbus_status();
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
   })
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

          var employeeid = $(this).attr('employeeid');

           var   form_data = "status=" + $("input[name='batch_category']:checked").val();
                 form_data += "&colhead="+ $(this).attr('colhead');
                 form_data += "&isLastApprover="+ $(this).attr('isLastApprover');
                 form_data += "&code_request="+ $(this).attr('code_request');
                 form_data += "&leaveid="+ $(this).attr('idkey');
                 form_data += "&base_id="+ $(this).attr('base_id');
                 form_data += "&employeeid="+ employeeid;
                 form_data += "&timefrom="+ $(this).attr('timefrom');
                 form_data += "&timeto="+ $(this).attr('timeto');
                 form_data += "&isBatchApprove=1";
          

          $.ajax({
              url:"<?=site_url("ob_application_/saveLeaveStatusChange")?>",
              type:"POST",
              dataType : 'JSON',
              data:form_data,
              success: function(msg){
                   loopcounter++;
                  if(msg.err_code != 0){
                    success_emp += success_emp ? ',':'';
                    success_emp += employeeid;
                  }else{
                    failed_emp += failed_emp ? ',':'';
                    failed_emp += employeeid;
                  }

                  if(loopcounter == checked_length){
                      failed_emp = failed_emp ? 'Failed to save for employee #: ' + failed_emp :'';

                      if(failed_emp){
                        // alert(failed_emp);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: failed_emp,
                            showConfirmButton: true,
                            timer: 1000
                        })
                      }else{
                        // alert(msg.msg);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: msg.msg,
                            showConfirmButton: true,
                            timer: 1000
                        })
                      }
                      location.reload();

                  }
              }
          });
        
      }); ///< end loop checked
      $("#batch_errormsg").html();
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
        
    view_offbus_status("", "", "PENDING", deptid, office);
});

$("#offbush").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$('.chosen').chosen();
</script>