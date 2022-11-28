<style>
.dataTables_paginate {
    margin-top: 6px;
}
#oth tr td,#oth tr th{
    text-align: center;
}

</style>

<div class="panel-heading"><h4><b>USED SERVICE CREDIT LIST</b></h4></div>
<div id="othistory" class="well-content" style="padding-bottom: 31px;">
    <?if($isHrHead){?>
        <div class="form_row">
          <label class="field_name align_right">BATCH APPROVAL</label>
          <div class="field no-search">
              <select class="form-control" id="batch_category_scu">
                  <option value="">Select approval status..</option>
                  <option value="APPROVED">APPROVED</option>
                  <option value="DISAPPROVED">DISAPPROVED</option>
              </select>
              &nbsp;
              <input type="button" class="btn btn-primary" id="save_batchapprove_scu" value="SAVE">
              &nbsp;
              <span id="batch_errormsg_scu" class="error-msg"></span>
          </div>
          
        </div>
        <br>
    <?}?>
    
    <table class="table table-hover table-bordered datatable" id="scu">                                                     
        <thead>
            <tr>
                <?if($isHrHead){?>
                  <th class="no-sort">Select All <br><input type='checkbox' id="selectall_scu" class="double-sized-cb"></th>
                <?}?>
                <th>Approval</th>
                <th class="sorting_asc">Employee ID</th>
                <th>Full Name</th>
                <th>Date Applied</th>
                <th>Inclusive Dates</th>
                <th>Service Credit</th>
                <th>Remarks</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody id="manageot">                                                               
            <?
                foreach ($scu_list as $key => $list) {
                    if(sizeof($list['data_list']) > 0 ){
                          $row = $list['data_list'];
                        // foreach($list['data_list'] as $row){
                            // echo'<pre>';var_dump($list);
                        ?>
                			<tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">

                                <?if($isHrHead){?>
                                    <td>
                                      <input type='checkbox' name='multiple_approve_scu' class="double-sized-cb" scid="<?=$row->aid?>" base_id="<?=$row->base_id?>" 
                                        service_credit_date_use="<?=$row->service_credit_date_use?>" service_credit_use="<?=$row->service_credit_use?>" dated="<?=$row->date?>" 
                                        colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" employeeid="<?=$row->employeeid?>" >
                                    </td>
                                <?}?>

                                <td class="align_center col-md-1">
                					<div class="btn-group">
                						<a class="btn" href="#" tag='scuedit_d' data-toggle="modal" data-target="#myModalscu" code="<?=$row->employeeid?>" idnum="<?=$row->base_id?>"  colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" ><i class="glyphicon glyphicon-edit"></i></a>
                					</div>
                                </td>
                                <td><?=$row->employeeid?></td>
                                <td><?=$row->fullname?></td>
                                <td><?=date('F d, Y',strtotime($row->date_applied))?></td>
                                <td><?=date('F d, Y',strtotime($row->date))?></td>
                                <td><?=$row->needed_service_credit?></td>
                                <td><?=$row->remark?></td>
                                <td><?=$row->$list['colstatus']?></td>
                            </tr>
                        <?
                		// } //end foreach
                        
                    
                    } //endif
                } //end foreach scu_list
        		
        		
            ?>
        </tbody>
    </table>
    <div class="modal fade" id="myModalscu" data-backdrop="static"></div>
</div>
<script>
$("a[tag='scuedit_d']").click(function(){
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
                        view            : "mailscuapp_manage"
                    }
    $.ajax({
       url      :   "<?=site_url("service_credit_/getSCUDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalscu").html(msg);
       }
    }); 
});

///< for batch approving

$('#selectall_scu').on('click',function(){
  if($(this).is(':checked'))      $('input[name=multiple_approve_scu]').prop('checked',true);
  else                            $('input[name=multiple_approve_scu]').prop('checked',false);
});

$('input[name=multiple_approve_scu]').on('click',function(){
  if(!$(this).is(':checked'))     $('#selectall_scu').prop('checked',false);
});

$('#save_batchapprove_scu').on('click',function(){

  var failed_emp = success_emp = '';
  var checked_length = $('input[name=multiple_approve_scu]:checked').length;
  var loopcounter = 0;

  if($('#batch_category_scu').val() == ''){
    $('#batch_errormsg_scu').html('Please select approval status.').css('color','red');
    return false;
  }

  if(checked_length > 0){
      $('#batch_errormsg_scu').html('');
      $('input[name=multiple_approve_scu]:checked').each(function(){
       

          $('#batch_errormsg_scu').html("<img src='<?=base_url()?>images/loading.gif' /> Saving.. Please wait.").css('color','green');

          var employeeid = $(this).attr('employeeid');

           var   form_data = "status=" + $('#batch_category_scu').val();
                 form_data += "&colhead="+ $(this).attr('colhead');
                 form_data += "&isLastApprover="+ $(this).attr('isLastApprover');
                 form_data += "&code_request="+ $(this).attr('code_request');
                 form_data += "&approval_id="+ $(this).attr('approvalid');
                 form_data += "&scid="+ $(this).attr('scid');
                 form_data += "&base_id="+ $(this).attr('base_id');
                 form_data += "&dated="+ $(this).attr('dated');
                 form_data += "&service_credit_date_use="+ $(this).attr('service_credit_date_use');
                 form_data += "&service_credit_use="+ $(this).attr('service_credit_use');
                 form_data += "&employeeid="+ employeeid;
                 form_data += "&isBatchApprove=1";
          

          $.ajax({
              url:"<?=site_url("service_credit_/saveSCUStatusChange")?>",
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
      $('#batch_errormsg_scu').html('Please select application.').css('color','red');
  }

});

///< end for batch approving


$("#scu").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$('.chosen').chosen();

</script>