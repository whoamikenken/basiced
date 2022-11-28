<?php

/**
 * @author Justin
 * @copyright 2015   
 */
$toks = $this->input->post("toks");
$employeeid = $toks ? $this->gibberish->decrypt($this->input->post('employeeid'), $toks) : $this->input->post("employeeid");
$deduction = $toks ? $this->gibberish->decrypt($this->input->post('deduction'), $toks) : $this->input->post("deduction");
$deductionbase = $toks ? $this->gibberish->decrypt($this->input->post('deductionbase'), $toks) : $this->input->post("deductionbase");
$memberid = $toks ? $this->gibberish->decrypt($this->input->post('memberid'), $toks) : $this->input->post("memberid");

$amount = $toks ? $this->gibberish->decrypt($this->input->post('amount'), $toks) : $this->input->post("amount");
$nocutoff = $toks ? $this->gibberish->decrypt($this->input->post('nocutoff'), $toks) : $this->input->post("nocutoff");
$datefrom = $toks ? $this->gibberish->decrypt($this->input->post('datefrom'), $toks) : $this->input->post("datefrom");
$dateto = $toks ? $this->gibberish->decrypt($this->input->post('dateto'), $toks)  : $this->input->post("dateto");
$datefrom = $datefrom=="0000-00-00" ? "" : $datefrom;
$dateto = $dateto=="0000-00-00" ? "" : $dateto;

$schedule = $toks ? $this->gibberish->decrypt($this->input->post('schedule'), $toks) : $this->input->post("schedule");
$cperiod = $toks ? $this->gibberish->decrypt($this->input->post('period'), $toks) : $this->input->post("period");
$hide = $toks ? $this->gibberish->decrypt($this->input->post('ishidden'), $toks) : $this->input->post("ishidden");
$ishidden = ($hide == true ? "" : " hidden");
$job = $toks ? $this->gibberish->decrypt($this->input->post('job'), $toks) : $this->input->post("job");
if($job=="new"){
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
                 "nocutoff" => $nocutoff,
                 "datefrom" => $datefrom,
                 "dateto" => $dateto,
                 "schedule" => $schedule,
                 "period" => $cperiod
               );  
      
      array_push($gf,$tarrs);
      $this->session->set_userdata("deductions",$gf);
  }         
}else if($job=="delete"){
  $this->db->query("delete from employee_deduction where employeeid='{$employeeid}' and code_deduction='{$deduction}'");
}

?>

<style type="text/css">
  .col-md-7{
    margin-left: 20px;
  }

  .form_row{
    padding-bottom: 15px;
  }

  .col-md-12{
    margin-top: 20px;
  }
</style>

<form id="form_adddeduction">
  <div class="col-md-12">
    <div class="form_row">
        <label class="field_name col-md-3 align_right">Deduction</label>
        <div class="field">
            <div class="col-md-7 no-search">
                <select id="deduction_drop" name="deduction_drop" class="chosen required">
                <?=$this->payrolloptions->deduction($deduction);?>
                </select>
            </div>
        </div>
    </div>
    <?
    switch($deduction){
      default:
    ?>
    <div class="form_row" style="display: none;">
        <label class="field_name">Member ID</label>
        <div class="field">
             <input class="form-control col-md-11 required" id="memberid" name="memberid" type="text" value="<?=$memberid?>"/>
        </div>
    </div>
    <div class="form_row" hidden="">
        <label class="field_name">Starting Date</label>
        <div class="field">
                 <div class="input-group date" id="datefrom" data-date="" data-date-format="yyyy-mm-dd">
                    <input size="16" class="align_center" type="text" name="datefrom" value="<?=$datefrom?>" readonly>
                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                 </div>
         
        </div>
    </div>
    <div class="form_row">
        <label class="field_name col-md-3 align_right">Amount</label>
        <div class="field">
          <div class="col-md-7">
             <input class="form-control required" id="amountdeduct" name="amountdeduct" type="text" value="<?=$amount?>"/>
          </div>
        </div>
    </div>
    <div class="form_row">
        <label class="field_name col-md-3 align_right">No.&nbsp;of&nbsp;Cut&nbsp;-&nbsp;off</label>
        <div class="field">
          <div class="col-md-7">
             <input class="form-control required" id="nocutoff" name="nocutoff" type="text" value="<?=$nocutoff?>"/>
          </div>
        </div>
    </div>
    <?    
      break;
    }
    ?>
    <div class="form_row">
        <label class="field_name col-md-3 align_right">Schedule</label>
        <div class="field">
            <div class="col-md-7 no-search">
                <select class="chosen align_left required" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule($schedule);?></select>
            </div>
        </div>
    </div>
    <div class="content" style="margin-top: 3px;" id="qload" <?=$ishidden?>></div>
    <div class="form_row" id="qshow" <?=$ishidden?>>
        <label class="field_name col-md-3 align_right">Quarter</label>
        <div class="field">
            <div class="col-md-7 no-search">
                <select id="period_drop" name="period_drop" class="form-control">
                <?=$this->payrolloptions->quarter($cperiod,FALSE,$schedule);?>
                </select>
            </div>
        </div>
    </div>
  </div>
</form>
<script>
  var toks = hex_sha512(" ");
$('.chosen').chosen();
$('#datefrom,#dateto').datetimepicker();
$("#cboxrangefrom").click(function(){
    $("#months_f,#days_f,#years_f").attr("disabled",!$(this).is(":checked"));
});
$("#cboxrangeto").click(function(){
    $("#months_t,#days_t,#years_t").attr("disabled",!$(this).is(":checked"));
});
//$("#addbutton").click(function(){
  $("#button_save_modal").html('Save');
$("#button_save_modal").unbind("click").click(function(){    
    var $validator = $("#form_adddeduction").validate({
        rules: {
            deduction_drop: {
              required: true
            },
            amountdeduct: {
                required: true
            },
            nocutoff: {
                required: true
            },
            schedule: {
              required: true
            }
        }
    });
    
    if($("#form_adddeduction").valid()){
           var form_data = $("#form_adddeduction").serialize();
               form_data += "&job=employee/deduction_info";      
               //console.log(form_data);                                                 
           $.ajax({
              url: "<?=site_url("employee_/validateinfo")?>",
              data : {formdata:GibberishAES.enc(form_data, toks), toks:toks},
              type : "POST",
              success:function(msg){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Successfully saved data!',
                    showConfirmButton: true,
                    timer: 1000
                })
                $("#modalclose").click();
                loaddeductions();
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
          schedule  :   GibberishAES.enc( $(this).val() , toks), 
          model     :    GibberishAES.enc( "quarter", toks),
          toks:toks
        },
        success: function(msg){
           $("#qload").hide();
           $("select[name='period_drop']").html(msg).trigger("chosen:updated");
           $("#qshow").show();
        }
    });
});
$('.chosen').chosen();
</script>