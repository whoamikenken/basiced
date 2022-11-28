<style>
.dataTables_paginate {
    margin-top: 6px;
}
#othistory tr td,#othistory tr th{
    text-align: center;

}
</style>
<div class="panel-heading"><h4><b>SERVICE CREDIT APPLY LIST</b></h4></div>
<div id="othistory" class="well-content" style="padding-bottom: 31px;">

    <?if($isHrHead){?>
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
        <br>
    <?}?>

    <table class="table table-hover table-bordered datatable" id="sc">                                                     
        <thead>
            <tr>
                <?if($isHrHead){?>
                  <th class="no-sort">Select All <br><input type='checkbox' id="selectall" class="double-sized-cb"></th>
                <?}?>
                <th>Approval</th>
                <th class="sorting_asc">Employee ID</th>
                <th>Full Name</th>
                <th>Date Applied</th>
                <th>Inclusive Dates</th>
                <th>Service Credit</th>
                <th>Reason</th>
                <th>Status</th>
            </tr>
        </thead>
   
        <tbody id="manageot">                                                               
            <?
            $getDate = array();
              
                foreach ($sc_list as $key => $list) {

                    if(sizeof($list['data_list']) > 0 ){
                        $row = $list['data_list'];
                        // foreach($list['data_list'] as $row){
                        // echo'<pre>';var_dump($row);
                      
                       
                            $getDate[] = $row->approval_id;
                        
                        ?>
                                <? if (in_array($row->approval_id, $getDate)) 
                                {
                                    unset($getDate[$row->approval_id]);
                                ?>

                                    <!-- <h3><?=$row->date?></h3> -->
                                    <tr employeeid='<?=$row->employeeid?>' style="cursor: pointer;">

                                        <?if($isHrHead){?>
                                            <td>
                                              <input type='checkbox' name='multiple_approve' class="double-sized-cb" scid="<?=$row->aid?>" approvalid="<?=$row->approval_id?>" base_id="<?=$row->base_id?>" colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" employeeid="<?=$row->employeeid?>" >
                                            </td>
                                        <?}?>

                                         <td class="align_center col-md-1">
                                            <div class="btn-group">
                                                <a class="btn" href="#" tag='scedit_d' data-toggle="modal" data-target="#myModalsc" code="<?=$row->employeeid?>" idnum="<?=$row->base_id?>" approvalid='<?=$row->approval_id?>' colhead="<?=$list['colhead']?>" colstatus="<?=$list['colstatus']?>" isLastApprover="<?=$list['isLastApprover']?>" code_request="<?=$list['code_request']?>" ><i class="glyphicon glyphicon-edit"></i></a>
                                            </div>
                                         </td>
                                         <td><?=$row->employeeid?></td>
                                         <td><?=$row->fullname?></td>
                                         <td><?=date('F d, Y',strtotime($row->date_applied))?></td>
                                         <!-- <?php if (str_word_count($row->date) == 2): ?>
                                          <td  class='align_center'><?=date("F d Y",strtotime($row->date))?></td>   
                                         <?php else: ?>
                                           <?php $dateexplode = explode("/",$row->date);?>
                                           <td  class='align_center'><?=date("F d Y",strtotime($dateexplode[0]))." - ". date("F d Y",strtotime($dateexplode[1])) ?></td>   
                                         <?php endif ?> -->
                                         <td><?=date('F d, Y',strtotime($row->date))?></td>
                                         <td><?=$row->sc_num?></td>
                                         <td><?=$row->reason?></td>
                                         <td><?=$row->$list['colstatus']?></td>
                                    </tr>
                                <?
                                  }
                                ?>
                              
                           
                		
                        <?
                		// } //end foreach
                
                
                    } //endif
                } //end foreach sc_list
        		
        		
            ?>
          
        </tbody>
    </table>
    <div class="modal fade" id="myModalsc" data-backdrop="static"></div>
</div>
<script>
$("a[tag='scedit_d']").click(function(){
    var code = "";  
    var idnum = ""; 
    var approvalid = ""; 
    if($(this).attr("code")) code = $(this).attr("code");
    if($(this).attr("idnum")) idnum = $(this).attr("idnum");
     if($(this).attr("approvalid")) approvalid = $(this).attr("approvalid");
    var colhead = $(this).attr('colhead'),
        colstatus = $(this).attr('colstatus'),
        isLastApprover = $(this).attr('isLastApprover'),
        code_request    = $(this).attr('code_request');
    var form_data = {
                        code            : code,
                        idkey           : idnum,
                        approval        : approvalid,
                        colhead         : colhead,
                        approvalid      : approvalid,
                        isLastApprover  : isLastApprover,
                        code_request    : code_request,
                        job             : "edit",
                        folder          : "employeemod", 
                        view            : "mailscapp_manage",
                        /*dept     : "<?=$dept?>"*/   
                    }
    $.ajax({
       url      :   "<?=site_url("service_credit_/getSCDetails")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#myModalsc").html(msg);
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
                 form_data += "&approval_id="+ $(this).attr('approvalid');
                 form_data += "&scid="+ $(this).attr('scid');
                 form_data += "&base_id="+ $(this).attr('base_id');
                 form_data += "&employeeid="+ employeeid;
                 form_data += "&isBatchApprove=1";
          

          $.ajax({
              url:"<?=site_url("service_credit_/saveSCStatusChange")?>",
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



$("#sc").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$('.chosen').chosen();
</script>