<?php
$datetoday=date("Y-m-d");
$nocutoff= ("nocutoff");
/**
 * @author Justin
 * @copyright 2015   
 */
$toks = $this->input->post("toks");
$employeeid = $toks ? $this->gibberish->decrypt($this->input->post('employeeid'), $toks) : $this->input->post("employeeid");
$income = $toks ? $this->gibberish->decrypt($this->input->post('income'), $toks) : $this->input->post("income");
$incomebase = $toks ? $this->gibberish->decrypt($this->input->post('incomebase'), $toks) : $this->input->post("incomebase");

$amount = $toks ? $this->gibberish->decrypt($this->input->post('amount'), $toks) : $this->input->post("amount");
$deduct = $toks ? $this->gibberish->decrypt($this->input->post('deduct'), $toks) : $this->input->post("deduct");
$nocutoff = $toks ? $this->gibberish->decrypt($this->input->post('nocutoff'), $toks) : $this->input->post("nocutoff");
$datefrom = $toks ? $this->gibberish->decrypt($this->input->post('datefrom'), $toks) : $this->input->post("datefrom");
$dateto = $toks ? $this->gibberish->decrypt($this->input->post('dateto'), $toks) : $this->input->post("dateto");
$datefrom = $datefrom=="0000-00-00" ? "" : $datefrom;
$dateto = $dateto=="0000-00-00" ? "" : $dateto;

$schedule = $toks ? $this->gibberish->decrypt($this->input->post('schedule'), $toks) : $this->input->post("schedule");
$cperiod = $toks ? $this->gibberish->decrypt($this->input->post('period'), $toks) : $this->input->post("period");
$hide = $toks ? $this->gibberish->decrypt($this->input->post('ishidden'), $toks) : $this->input->post("ishidden");
$ishidden = ($hide == true ? "" : " hidden");
$job =  $toks ? $this->gibberish->decrypt($this->input->post('job'), $toks) : $this->input->post("job");
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
                 "datefrom" => $datefrom,
                 "dateto" => $dateto,
                 "nocutoff" => $nocutoff,
                 "schedule" => $schedule,
                 "period" => $cperiod
               );  
      
      array_push($gf,$tarrs);
      $this->session->set_userdata("deductions",$gf);
  }         
}else if($job=="delete"){
  $this->db->query("delete from employee_income where employeeid='{$employeeid}' and code_income='{$income}'");
}

?>

<style type="text/css">
  
  .form-group{
    padding-bottom: 0px;
  }

  #form_addincome{
    margin-top: 30px;
  }

</style>

<form id="form_addincome" class="form-horizontal">
<div class="col-sm-12">
<div class="form-group">
    <label class="col-sm-3 control-label">Income</label>
    <div class="col-sm-7">
        <select id="income_drop" name="income_drop" class="chosen" name="tax_status">
        <?=$this->payrolloptions->income($income);?>
        </select>
    </div>
</div>
<?
switch($income){
  case "SALARY":
?>

<div class="form-group">
    <label class="col-sm-3 control-label">Type</label>
    <div class="col-sm-7">
        <select id="incombase_drop" name="incombase_drop" class="chosen" name="tax_status">
        <?
            $opt_incomebase= $this->extras->showincomebase();
            foreach($opt_income as $c=>$val){
            ?><option<?=($c==$incomebase ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
            }
        ?>
        </select>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Amount</label>
    <div class="col-sm-7">
        <input class="form-control required" id="amountincome" name="amountincome" type="text" value="<?=$amount?>"/>
    </div>
</div>
<?    
  break;
  default:
?>
<div class="form-group">
    <label class="col-sm-3 control-label">Effectivity Date</label>
    <div class="col-sm-7">
        <div class='input-group date' d="datefrom" data-date="<?= ($datefrom)? $datefrom:$datetoday ?>" data-date-format="yyyy-mm-dd">
            <input type='text' class="form-control" name="datefrom" value="<?= ($datefrom)? $datefrom:$datetoday ?>" size="16"/>
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">Amount</label>
    <div class="col-sm-7">
        <input class="form-control required" id="amountincome" name="amountincome" type="text" value="<?=$amount?>"/>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label">No. of Cut-off</label>
    <div class="col-sm-7">
        <input class="form-control required" id="nocutoff" name="nocutoff" value="<?=$nocutoff?>"/>
    </div>
</div>
<?    
  break;
}
?>
<div class="form-group">
    <label class="col-sm-3 control-label">Schedule</label>
    <div class="col-sm-7">
        <select class="chosen align_left" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule($schedule);?></select>
    </div>
</div>
<!-- <div class="form-group">
      <label class="col-sm-3 control-label text-right">Quarter</label>
      <div class="col-sm-9">
      <select id="quarter" name="quarter" class="form-control">
        <?=$quarter?>
      </select>
      </div>
  </div>
  <div class="form-group">
        <label class="col-sm-3 control-label text-right">Deduct</label>
        <div class="col-sm-8">
             <input type="radio" name="deduct_income" value="1" <?=$deduct==1?'checked':''?> > YES &nbsp;
             <input type="radio" name="deduct_income" value="0" <?=$deduct==0?'checked':''?> > NO
        </div>
    </div> -->
<div class="content" style="margin-top: 3px;" id="qload" <?=$ishidden?>></div>
<div class="form-group" id="qshow" <?=$ishidden?>>
    <label class="col-sm-3 control-label">Quarter</label>
    <div class="col-sm-7">
         <select id="period_drop" name="period_drop" class="form-control">
        <?=$this->payrolloptions->quarter($cperiod,FALSE,$schedule);?>
        </select>
    </div>
</div>
</div>
</form>
<script>
  var toks = hex_sha512(" ");
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
    
    var $validator = $("#form_addincome").validate({
        rules: {
            amountincome: {
              required: true
            }
        }
    });
    
    if($("#form_addincome").valid()){
           var form_data = $("#form_addincome").serialize();
               form_data += "&job=employee/income_info";
           $.ajax({
              url: "<?=site_url("employee_/validateinfo")?>",
              data : {formdata:GibberishAES.enc(form_data, toks), toks:toks},
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
                loadincome();
              
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
          schedule  :    GibberishAES.enc( $(this).val(), toks), 
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