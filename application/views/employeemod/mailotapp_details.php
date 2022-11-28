<style>
.dataTables_paginate {
    margin-top: 6px;
}
#oth tr td,#oth tr th{
    text-align: center;
}
</style>
<div class="panel-heading" style="background-color: #0072c6;"><h4><b><?= ucfirst(strtolower($status)) ?> Application List</b></h4></div>
   <div class="panel-body">
<div id="othistory" class="well-content" style="padding: 1%;">

        <?if($isHrHead){?>
            <hr>
            <div class="row">
              <div class="col-md-12" style="margin-left: 25%;">
                <div class="col-md-12" style="padding-right: 0px;">&nbsp;&nbsp;
                    <label style="display: inline;font-size: 16px;">BATCH APPROVAL: &nbsp;&nbsp;</label>
                    &nbsp;&nbsp;<input type="radio" name="batch_category" value="APPROVED">&nbsp;&nbsp;<span style="font-size: 15px;">NOTED</span>&nbsp;&nbsp;
                    &nbsp;&nbsp;<input type="radio" name="batch_category" value="DISAPPROVED">&nbsp;&nbsp;<span style="font-size: 15px;">DISAPPROVED</span>&nbsp;&nbsp;
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

    <table class="table table-hover table-bordered datatable" id="oth">                                                     
        <thead>
            <tr style="background-color: #0072c6;">
                <?if($isHrHead){?>
                  <th class="no-sort" rowspan="2">Select All <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id="selectall" class="double-sized-cb"></th>
                <?}?>
                <th rowspan="2">Action</th>
                <th rowspan="2" class="sorting_asc">Employee ID</th>
                <th rowspan="2">Full Name</th>
                <th rowspan="2">Department</th>
                <th rowspan="2">Date Applied</th>
                <th colspan="2">Inclusive Dates</th>
                <th colspan="2">Time</th>
                <th rowspan="2">Total Hour/s</th>
                <th rowspan="2">Special Task To Be Done</th>
                <th rowspan="2">Status</th>
            </tr>
            <tr style="background-color: #0072c6;">
                <th>From</th>
                <th>To</th>
                <th>Start</th>
                <th>End</th>
            </tr>
        </thead>

        <tbody id="manageot">                                                               
            <?
                foreach ($ot_list as $key => $list) {
                    if(sizeof($list['data_list']) > 0 /*|| $employeeot_h->num_rows() > 0*/){
                        $row = $list['data_list'];
                        // foreach($list['data_list'] as $row){
                        ?>
                          <tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">

                            <?if($isHrHead){?>
                                <td>
                                  <input type='checkbox' name='multiple_approve' class="double-sized-cb" idkey="<?=$row->aid?>" base_id="<?=$row->base_id?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" employeeid="<?=$row->employeeid?>" ottotal="<?=$row->total?>" >
                                </td>
                            <?}?>

                            <td class="align_center col-md-1">
                              <div class="btn-group">
                                <a class="btn btn-info" href="#" tag='edit_d' data-toggle="modal" data-target="#myModalot" code="<?=$row->employeeid?>" idnum="<?=$row->aid?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" ottotal="<?=$row->total?>" ><i class="<?php echo  ($row->{$list['colstatus']} != 'PENDING') ? 'glyphicon glyphicon-eye-open' : 'glyphicon glyphicon-edit' ?>"></i></i></a>
                              </div>
                            </td>
                            <td><?=$row->employeeid?></td>
                            <td><?=$row->fullname?></td>
                            <td width="10%"><?=$this->extensions->getOfficeDescription($row->office)?></td>
                            <td><?=date('F d, Y',strtotime($row->date_applied))?></td>
                            <td><?=date('F d, Y',strtotime($row->dfrom))?></td>
                            <td><?=date('F d, Y',strtotime($row->dto))?></td>
                            <td><?=$row->tstart != '00:00:00' ? date('h:i A',strtotime($row->tstart)) : ''?></td>
                            <td><?=$row->tend != '00:00:00' ? date('h:i A',strtotime($row->tend)) : ''?></td>
                            <td><?=$row->total?></td>
                            <td><?=urldecode($row->reason)?></td>
                            <td><?=$row->$list['colstatus']?></td>
                          </tr>
                        <?
                        // } //end foreach
                    
                    
                    } //endif
                } //end foreach ot_list
                ?>
        </tbody>

    </table>
    <div class="modal fade" id="myModalot" data-backdrop="static"></div>
</div>
<script>
$("a[tag='edit_d']").click(function(){
    var code = "";  
    var idnum = "";  
    if($(this).attr("code")) code = $(this).attr("code");
    if($(this).attr("idnum")) idnum = $(this).attr("idnum");
    
    var colhead         = $(this).attr('colhead'),
        colstatus       = $(this).attr('colstatus'),
        isLastApprover  = $(this).attr('isLastApprover'),
        code_request    = $(this).attr('code_request');

    var form_data = {
                        code            : code,
                        idkey           : idnum,
                        colhead         : colhead,
                        isLastApprover  : isLastApprover,
                        code_request    : code_request,
                        job             : "edit",
                        folder          : "employeemod", 
                        view            : "mailotapp_manage"
                    }
    $.ajax({
       url      :   "<?=site_url("overtime_/getOTDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalot").html(msg);
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
                 form_data += "&otid="+ $(this).attr('idkey');
                 form_data += "&base_id="+ $(this).attr('base_id');
                 form_data += "&ottotal="+ $(this).attr('ottotal');
                 form_data += "&employeeid="+ employeeid;
                 form_data += "&isBatchApprove=1";
          

          $.ajax({
              url:"<?=site_url("overtime_/saveOTStatusChange")?>",
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


$("#ob_deptid, #ob_office").change(function(){
    var category = $("#category").val(), 
        dfrom    = $("input[name='ldfrom']").val(), 
        dto      = $("input[name='ldto']").val();
        deptid   = $("#ob_deptid").val();
        office   = $("#ob_office").val();
        
    loadOTAppList("", "", "PENDING", deptid, office);
});

///< end for batch approving
$("#oth").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$('.chosen').chosen();
</script>