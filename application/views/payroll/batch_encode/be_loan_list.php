<?php  
/**
* @author justin (with e)
* @copyright 2018
* 
* >  for mcu-hyperion 21479
*/
?>


<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/library/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/library/fixedColumns.dataTables.min.css">


        <div class="form_row">
          <br>
          <table class="table table-bordered table-hover" id="dble"  width="100%">
            <thead style="background-color: #0072c6">
              <tr valign="center" style="background-color: #424d57; color: white;">
                <th width="10%" style="text-align: center;">Employee</th>
                <th width="25%" style="text-align: center;">Fullname</th>
                <th style="text-align: center; display: none;">Based ons</th>
                <th style="text-align: center;">Deduction Date</th>
                <th style="text-align: center;">Starting Balance</th>
                <th style="text-align: center;">Current Balance</th>
                <th style="text-align: center;">No. of cutoff</th>
                <th style="text-align: center;">Amount</th>
                <th width="10%" style="text-align: center;display: none;">Schedule</th>
                <th width="10%" style="text-align: center;">Quarter</th>
                <th style="text-align: center;display: none;">Status</th>
                <th style="text-align: center;"></th>
              </tr>
            </thead>
            <tbody id='tbl_content' width="100%">
              
            </tbody>
        
            <tr class="data-list" employeeid="" status-tag='' style="background-color: white;">
            <td class="text-nowrap"></td>
            <td class="text-nowrap"></td>
            <td class="text-nowrap" hidden>
                <select class="baseon" name='baseon' class="span11" oldvalue=''></select>
            </td>
            <td class="text-nowrap" style="text-align: center;">
                <div class="input-append date span10"   data-date="" data-date-format="yyyy-mm-dd">
                    <input class="align_center required span11 datete" type="text" name="ddatefrom" value="" oldvalue="" >
                    <span class="add-on"><i class="icon-calendar"></i></span>
                </div>        
            </td>
            <td class="text-nowrap">
                <input type="text" class="span11 align_right startingbalance" name="startingbalance" value="" oldvalue="">
            </td>
            <td class="text-nowrap">
                <input type="text" class="span11 align_right currentbalance" name="currentbalance"  value="" oldvalue="">
            </td>
            <td class="text-nowrap">
                <input type="text" class="span11 align_right nocutoff" name="nocutoff"  value="" oldvalue="" >
            </td>  
            <td class="text-nowrap">
                <input type="text" class="span11 align_right amount" name="amount"  value="" oldvalue="">
            </td>
            <td class="text-nowrap" style="text-align: center;display: none;">
                <select class="span11 align_left schedule" name="schedule" id="schedule" oldvalue="" disabled>
                    
                </select>
            </td>
            <td class="text-nowrap" style="text-align: center;">
                <select class="span11 align_left period" name="period" id="period" oldvalue="" >
                    
                </select>
            </td>
            
            <td class="text-nowrap" class="status-tag align_center" style="display: none;"><p style="display:none;" class="dataStatus">SAVED</p></td>
          

            <!-- mcu-hyperion 21657 -->
            <td class="text-nowrap" class="edit-tag align_center">
                <?if(isset($detail['can_edit'])):?>
                <a class="btn green" tag="delete" employeeid="<?=$employeeid?>" base_id=""><i class="icon-trash"></i></a>
                <?endif;?>
            </td>          
        </tr>

          </table>
          <br>
        </div>
    </div>
</div>

<!-- </div> -->

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
<script src="<?=base_url()?>jsbstrap/library/bootstrap-datepicker.js"></script>
<script src="<?=base_url()?>jsbstrap/library/chosen.jquery.min.js"></script>
<script src="<?=base_url()?>jsbstrap/library/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>jsbstrap/library/dataTables.fixedColumns.min.js"></script>

<script type="text/javascript">

  $(function()
  {
    reloadClass();
  });
  
  $("select[name='schedule']").change(function(){
    if(!$("select[name='code_income']").val()){
      alert("Please select a income category..");
      return;
    }

    findEmployee({
      deptid : "<?=$deptid?>",
      employmentstat : "<?=$employmentstat?>",
      code_income :  $("select[name='code_income']").val(),
      schedule : this.value,
      year : $('select[name=year]').val()
    });
  });

  $("select[name='code_income']").change(function(){
    if(!this.value){
      alert("Please select a income category..");
      return;
    }

    ///< 13th month pay year option
    if($(this).val() == '56')   $('#wrapYear').show();
    else            $('#wrapYear').hide();

    // find table content here..
    findEmployee({
      deptid : "<?=$deptid?>",
      employmentstat : "<?=$employmentstat?>",
      code_income : this.value,
      schedule : $("select[name='schedule']").val(),
      year : $('select[name=year]').val()
    });
  });
  
  function reloadClass(){
    $(".datefrom").datepicker();
    $('.chosen').chosen();
  }

  function findEmployee(formdata){
    $("#tbl_content").html("<tr><td colspan='7' style='text-align center'><img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.</td></tr>");

    $("#div_save").show();
    $("#div_loading").hide();

    $.ajax({
      url : "<?=site_url("payroll_/findEmpListForBEIncome")?>",
      type : "POST",
      data : formdata,
      success(content){
        $("#tbl_content").html(content);
        reloadClass();
      }
    });
  }


  // UPLOAD CSV
  // ----- MAX -----
  $('#submit').click(function(){
  var user = "<?=ucwords(strtolower($this->session->userdata("fullname")))?>";
    var codeincome = $('select[name="code_income"]').val();
    var form_data  = new FormData();
    var file_data = $("#userfile").prop("files")[0]
    form_data.append('file',file_data);
    form_data.append('code_income', codeincome);
    form_data.append('user', user);
    var url = '<?= site_url('be_income_upload/uploadData') ?>';
     $('#loadingbar').html("<img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.");
  if( document.getElementById("userfile").files.length == 0 ){
    $('#loadingbar').html('');
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