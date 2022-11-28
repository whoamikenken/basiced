<?php

/**
 * @author Justin
 * @copyright 2015   
 */
$datetoday = date("Y-m-d");
$employeeid = $this->input->post("employeeid");
$income = $this->input->post("income");
$incomebase = $this->input->post("incomebase");

$amount = $this->input->post("amount");
$nocutoff = $this->input->post("nocutoff");
$deduct = $this->input->post("deduct");
$taxable = $this->input->post("taxable");
$datefrom = $this->input->post("datefrom")=="0000-00-00" ? "" : $this->input->post("datefrom");
$dateto = $this->input->post("dateto")=="0000-00-00" ? "" : $this->input->post("dateto");

$schedule = $this->input->post("schedule");
$cperiod = $this->input->post("period");
$hide = $this->input->post("ishidden");
$ishidden = ($hide == true ? "" : " hidden");

if($this->input->post("job")=="new"){
  $gf = $this->session->userdata("deductions");
  $canadd = true;
  foreach($gf as $deductions_included){
    if($canadd && $deductions_included['code_deduction']==$deduction) $canadd = false; 
  }
  if($canadd){
      $tarrs = array(
                 "code_deduction" => $deduction,
                 "memberid" => $memberid,
                 "description" => $deductiontext,
                 "amount" => $amount,
                 "datefrom" => $datefrom,
                 "dateto" => $dateto,
                 "nocutoff" => $nocutoff,
                 "schedule" => $schedule,
                 "period" => $cperiod
               );  
      
      array_push($gf,$tarrs);
      $this->session->set_userdata("deductions",$gf);
  }         
}else if($this->input->post("job")=="delete"){
  $this->db->query("delete from employee_income_adj where employeeid='{$employeeid}' and code_income='{$income}'");
}

?>
<style type="text/css">
  .form_row{
    padding-bottom: 15px;
  }

  .adj_form{
    margin-top: 30px;
  }

  .modal-body{
    width: 100%;
  }

  #centered_label{
    margin-top: 7px;
  }

</style>


<form id="form_addincome_adj" class="adj_form">
    <div class="form_row">
        <label class="col-md-3 control-label text-right" id="centered_label">Income ADJ</label>
            <div class="field">
              <div class="col-md-7">
                <select id="income_drop" name="income_drop" class="form-control" name="tax_status">
                <?=$this->payrolloptions->income($income,TRUE);?>
                </select>
              </div>
            </div>
        </div>

    <div class="form_row">
        <label class="col-md-3 control-label text-right" id="centered_label">Deduction Date</label>
        <div class="field">
          <div class="col-md-7">
            <div class='input-group date' id="datefrom" data-date="<?= ($datefrom)? $datefrom:$datetoday ?>" data-date-format="yyyy-mm-dd">
                <input type='text' class="form-control" name="datefrom" value="<?= ($datefrom)? $datefrom:$datetoday ?>"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
          </div>
        </div>
    </div>
    <div class="form_row">
        <label class="col-md-3 control-label text-right" id="centered_label">Amount</label>
        <div class="field">
          <div class="col-md-7">
            <input class="form-control required" id="amountincome" name="amountincome" type="text" value="<?=$amount?>"/>
          </div>
        </div>
    </div>
    <div class="form_row">
        <label class="col-md-3 control-label text-right" id="centered_label">No. of Cut-off</label>
        <div class="field">
          <div class="col-md-7">
            <input class="form-control required" id="nocutoff" name="nocutoff" type="text" value="<?=$nocutoff?>"/>
          </div>
        </div>
    </div>

    <div class="form_row">
        <label class="col-md-3 control-label text-right" id="centered_label">Schedule</label>
        <div class="field">
            <div class="col-md-7 no-search">
                <select class="chosen align_left" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule($schedule);?></select>
            </div>
        </div>
    </div>
    <div class="content" id="qload" <?=$ishidden?>></div>
    <div class="form_row" id="qshow" <?=$ishidden?>>
        <label class="col-md-3 control-label text-right" id="centered_label">Quarter</label>
        <div class="field">
            <div class="col-md-7 no-search">
                <select id="period_drop" name="period_drop" class="form-control">
                <?=$this->payrolloptions->quarter($cperiod,FALSE,$schedule);?>
                </select>
            </div>
        </div>
    </div>
    <div class="form_row">
        <label class="col-md-3 control-label text-right">Deduct</label>
        <div class="field">
          <div class="col-md-7 no-search">
             <input type="radio" name="deduct_income" value="1" <?=$deduct==1?'checked':''?> > YES &emsp;
             <input type="radio" name="deduct_income" value="0" <?=$deduct==0?'checked':''?> > NO
          </div>
        </div>
    </div>
    <div class="form_row" hidden="">
        <label class="field_name">Taxable</label>
        <div class="field" style="margin-top: 10px;">
             <input type="radio" name="taxable_income" value="1" <?=$taxable==1?'checked':''?> > YES &nbsp;
             <input type="radio" name="taxable_income" value="0" <?=$taxable==0?'checked':''?> > NO
        </div>
    </div>
</form>
<script>
  $("#button_save_modal").text("Save");
$('.chosen').chosen();
$('.date').datetimepicker({
    format: "YYYY-MM-DD"
});
$("#cboxrangefrom").click(function(){
    $("#months_f,#days_f,#years_f").attr("disabled",!$(this).is(":checked"));
});
$("#cboxrangeto").click(function(){
    $("#months_t,#days_t,#years_t").attr("disabled",!$(this).is(":checked"));
});
$("#button_save_modal").unbind("click").click(function(){
    
    var $validator = $("#form_addincome_adj").validate({
        rules: {
            amountincome: {
              required: true
            }
        }
    });
    
    if($("#form_addincome_adj").valid()){
           var form_data = $("#form_addincome_adj").serialize();
               form_data += "&job=employee/income_adj_info";
           $.ajax({
              url: "<?=site_url("employee_/validateinfo")?>",
              data : form_data,
              type : "POST",
              success:function(msg){
                //alert(msg);
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Successfully saved data!',
                    showConfirmButton: true,
                    timer: 1000
                })
                $("#modalclose").click();
                loadincome_adj();
              
              }
           });
           
   }else {
       $validator.focusInvalid();
       return false;
   }
});
$("#schedule").change(function(){
    $("#qshow").hide();
    $("#qload").show().html('<div class="form_row"><label class="field_name"></label><div class="field"><div class="col-md-5"><img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</div></div></div>');
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :   $(this).val(), 
          model     :   "quarter"
        },
        success: function(msg){
           $("#qload").hide();
           $("select[name='period_drop']").html(msg).trigger("liszt:updated");
           $("#qshow").show();
        }
    });
});
</script>