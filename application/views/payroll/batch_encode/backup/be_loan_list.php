
<script src="<?=base_url()?>jsbstrap/bootstrap.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();
</script>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/library/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/library/fixedColumns.dataTables.min.css">

<div class="well-blue">
    <div class="well-header">
        <h5>Employee List</h5>
    </div>
    <div class="well-content">
        <div class="form_row" style="float:right;">
          <form method="" enctype="multipart/form-data">

              <label><b>(NOTE : CSV File Extension Only)<b></label>
            <input type="file" name="userfile" id="userfile">
            <button type="button" id="submit" name="submit" class="btn blue">UPLOAD</button>
            </div>
            <br>
        <span class='pull-left'>
            <label>Loan : </label>
                    <select class="chosen" id="loan" name="loan">
                    <option value='All'> -Select Loan - </option>
                        <?
                        foreach ($loantype as $code) {?>
                            <option value='<?=strtolower($code->id)?>'><?=$code->description?></option>
                        <?}
                        ?>
                    </select>
        </span>
  
       <span class='pull-left'>
            <label>Schedule : </label>
                     <select class="chosen align_left" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule($schedule);?></select>
        </span>

        <span class="pull-right">
            <span id="errorMsg"></span>&nbsp;&nbsp;&nbsp;
            <a href="#" class="btn blue" id="saveloan" style="display: none"><b>SAVE</b></a>
        </span>
       <p id="loadingbar" align="right" style="padding: 7px;"></p>
        <br>

        <table class="table table-striped table-bordered table-hover datatable table-template" id="dble" width="100%">
            <thead>
                <tr>
                    <th width="10%" style="text-align: center;">Employee</th>
                    <th width="25%" style="text-align: center;">Fullname</th>
                    <th style="text-align: center; display: none;">Based on</th>
                    <th style="text-align: center;">Deduction Date</th>
                    <th style="text-align: center;">Starting Balance</th>
                    <th style="text-align: center;">Current Balance</th>
                    <th style="text-align: center;">No. of cutoff</th>
                    <th style="text-align: center;">Amount</th>
                    <th width="10%" style="text-align: center;">Schedule</th>
                    <th width="10%" style="text-align: center;">Quarter</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;"></th>
                </tr>
            </thead>
            <tbody id="employeelist">

            </tbody>
        </table>
    </div>
</div>

<div id="be_modal" class="modal hide fade" data-backdrop="static" data-replace="true" data-keyboard="false" tabindex="-1">
    <div class="modal-header">
        <button id="modalclose" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
    </div>
    <div class="modal-body">
        <div class="row-fluid">
            <div class="row-fluid span12" tag='display'></div>
        </div></div>
    <div class="modal-footer">
        <a href="#" data-dismiss="modal" aria-hidden="true" class="btn grey">Close</a>
    </div>
</div>

<!-- do script -->
<script src="<?=base_url()?>jsbstrap/jquery-1.12.4.js"></script>
<script src="<?=base_url()?>jsbstrap/library/bootstrap-datepicker.js"></script>
<script src="<?=base_url()?>jsbstrap/library/chosen.jquery.min.js"></script>
<script src="<?=base_url()?>jsbstrap/library/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>jsbstrap/library/dataTables.fixedColumns.min.js"></script>

<script type="text/javascript">
    $("#loan,#schedule").change(function()
    {
        var loan  = $("#loan").val();
        var sched = $("#schedule").val();
        var category = $("#category").val();

        $('#employeelist').html("<img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.");
                $.ajax({
                    url : "<?=site_url('payroll_/loadPayrollBatchEncode')?>",
                    type : "POST",
                    data : {
                        category: category,
                        loan : loan,
                        schedule : sched
                    },
                    success : function(msg){
                        $("#saveloan").show();
                        $('#employeelist').html(msg);
                    }
                });


    });
$(document).on('keydown',".startingbalance,.currentbalance,.nocutoff,.amount",function(e)
{
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
         // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
         // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
             // let it happen, don't do anything
             return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});
 




$("#saveloan").unbind('click').click(function()
{
   var form_data = {};
   var hasChanges = false;
   $('.data-list').each(function(){
        
        if ($(this).attr('status-tag') == 'NOTSAVED') {
            hasChanges = true;
             employeeid = $(this).attr('employeeid');
             form_data[employeeid] = {};
             form_data[employeeid]['loan'] = $("#loan").val(); 
             form_data[employeeid]['baseon'] = $(this).find("select[name=baseon]").val(); 
             form_data[employeeid]['deductiondate'] = $(this).find("input[name=ddatefrom]").val();
             form_data[employeeid]['startingbalance'] = $(this).find("input[name=startingbalance]").val();
             form_data[employeeid]['currentbalance'] = $(this).find("input[name=currentbalance]").val(); 
             form_data[employeeid]['nocutoff'] = $(this).find("input[name=nocutoff]").val(); 
             form_data[employeeid]['amount'] = $(this).find("input[name=amount]").val(); 
             form_data[employeeid]['schedule'] = $(this).find("select[name=schedule]").val(); 
             form_data[employeeid]['cutoff_period'] = $(this).find("select[name=period]").val(); 
        }

   });

    if(!hasChanges){
            $('#errorMsg').html("NO CHANGES WERE MADE.").css('color','red');
            return false;
        }

    $.ajax({
        url:"<?=site_url("payroll_/saveLoanBatch")?>",
        type:"POST",
        data:{form_data:form_data},
        dataType:"JSON",
        success:function(msg)
        {
                var data_failed = msg.data_failed;
                var failed = '';
                for (var key in data_failed) {
                    failed += data_failed[key] + ", ";
                }
                if(failed) failed = failed.substring(0, failed.length-2);
                else failed = 'NONE';

                if(msg.err_code == 0){
                  
                  if(failed == 'NONE') $('#be_modal').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'green','font-size':'15px','font-weight':'bold'});
                  else{
                    $('#be_modal').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>').css({'color':'green','font-size':'15px','font-weight':'bold'});
                    $('#be_modal').find('.modal-body').append('<span style="color:red;">Data insert failed: '+failed+'</span>');
                  }                  
                }else{
                  $('#be_modal').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'red','font-size':'15px','font-weight':'bold'});
                }

                $j('#be_modal').modal('show');
                $j('.modal-backdrop').css('z-index','90');

                $('.data-list').each(function(){
                    if($(this).attr('status-tag') == 'NOTSAVED'){
                        if(!data_failed.includes($(this).attr('employeeid'))){
                            $(this).attr('status-tag','');
                            $(this).find('.status-tag').html('SAVED').css('color','green');
                        }
                    }else{
                        $(this).find('.status-tag').html('');
                    }   
                });

                $('#errorMsg').html('');
        }
    })


});
$(document).on('input',".nocutoff,.currentbalance",function(){
    var tr = $(this).closest('tr');
     var currentbalance  = $(tr).find('.currentbalance').val();
     var nocutoff        = $(tr).find('.nocutoff').val();
     if (currentbalance !="" && nocutoff !="") {
         var amt = currentbalance / nocutoff;
         var amount = Math.floor(amt);
         if (amount == 'Infinity' || amount == 'NaN' ) {
            $amount = 0;
         }
         $(tr).find('.amount').val(amount);
     }
});


  $(".chosen").chosen();

  // UPLOAD CSV
  // ----- MAX -----
  $('#submit').click(function(){
    var codeloan = $('select[name="loan"]').val();
    var form_data  = new FormData();
    var file_data = $("#userfile").prop("files")[0]
    form_data.append('file',file_data);
    form_data.append('loans', codeloan);
    form_data.append('user', user);

    var url = '<?= site_url('be_loan_upload/uploadData') ?>';
        $('#loadingbar').html("<img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.");
        if( document.getElementById("userfile").files.length == 0 ){
          alert("Please upload file first");
        }else{ 
          $.ajax({
              url : url,  // Controller URL
              type : 'POST',
              data: form_data,
              dataType: "json",
              contentType : false,
              processData : false,
              success : function(response) {
                $('#loadingbar').html('');
                if(response != ""){
                  var result = "<br><p style='color:green'><b>There are </b>" +response.noEmp+ " data that is not inserted.</p><p style='color:red'>Employee ID: " +response.empId+ "<p><b>Reason:</b> Employee ID is not existing.";    
                  $('#be_modal').find('div[tag="display"]').html(result);
                  $j('#be_modal').modal('show');
                  $j('.modal-backdrop').css('z-index','90');  
                }else{
                  var result = "All data in inserted and updated";
                  $('#be_modal').find('div[tag="display"]').html(result);
                  $j('#be_modal').modal('show');
                  $j('.modal-backdrop').css('z-index','90');  
                }
                    
            }
          });
      }
  });

</script>