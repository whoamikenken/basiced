<?php

/**
 * @author Justin
 * @copyright 2015   
 */
$employeeid = $this->input->post("employeeid");
$loan = $this->input->post("loan");
$loanbase = $this->input->post("loanbase");
$startingamount = $this->input->post("startingamount");
$currentamount = $this->input->post("currentamount");
$remaining = $this->input->post("remaining");
$amount   = $this->input->post("amount");
$famount  = $this->input->post("famount");
$nocutoff = $this->input->post("nocutoff");
$datefrom = $this->input->post("datefrom")=="0000-00-00" ? "" : $this->input->post("datefrom");
$dateto = $this->input->post("dateto")=="0000-00-00" ? "" : $this->input->post("dateto");
$id   = $this->input->post("id");
$basedon = $this->input->post('basedon');
$schedule = $this->input->post("schedule");
$cperiod = $this->input->post("period");
$hide = $this->input->post("ishidden");

$ishidden = (($hide == true || $schedule <> '') ? "" : " hidden");
$user           = $this->session->userdata('username');

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
                 "startingamount" => $startingamount,
                 "famount" => $famount,
                 "nocutoff" => $nocutoff,
                 "datefrom" => $datefrom,
                 "dateto" => $dateto,
                 "schedule" => $schedule,
                 "period" => $cperiod
               );  
      
      array_push($gf,$tarrs);
      $this->session->set_userdata("deductions",$gf);
  }         
}else if($this->input->post("job")=="delete"){
  $this->db->query("INSERT INTO employee_loan_history(employeeid,code_loan,cutoffstart,cutoffend,startBalance,amount,remainingBalance,schedule,cutoff_period,mode,user) VALUES('$employeeid','$loan','$datefrom','$dateto','$startingamount','$amount','$remaining','$schedule','$cperiod','DELETED','$user')");

  $this->db->query("delete from employee_loan where id='$id'");
}

?>
<form id="form_addloan">

<input class="align_right col-md-4" id="id" name="id" type="hidden" value="<?=$id?>"/>
<div class="col-md-12">
                                       
       <div class="form_row">
           <label class="field_name">Loan</label>
           <div class="field no-search">
               <select id="dloan_drop" name="dloan_drop" class="chosen required" name="dtax_status">
                <?
                    if ($id) {
                      echo $this->payrolloptions->loan($loan);
                    }
                    else
                    {
                      echo $this->payrolloptions->loan();
                    }
                ?></select>
           </div>
       </div>
       <div class="form_row">
           <label class="field_name ">Based on</label>
           <div class="field no-search">
               <select class="form-control" name="basedon" id="basedon">
                <?
                  if ($id) 
                  {?>
                      <option value="">-- Select --</option> 
                      <?if ($basedon == 1) {?>
                        <option value="1" selected>Monthly</option>
                        <option value="0">Term</option>  
                      <?}
                      else if ($basedon == 0 ) {?>
                      <option value="1">Monthly</option>
                        <option value="0" selected>Term</option>  
                      <?}
                      ?>
                      
                      
                  <?}
                  else
                  {?>
                    <option value="">-- Select --</option> 
                    <option value="1">Monthly</option> 
                    <option value="0">Term</option> 
                  <?}
                ?>
                   
               </select>
           </div>
       </div>
       
       <div class="form_row">
           <label class="field_name">Deduction Date</label>
           <div class="field">
               <div class="input-group date" id="ddatefrom" data-date="" data-date-format="yyyy-mm-dd">
                   <input size="16" class="align_center required" type="text" name="ddatefrom" value="<?=$datefrom?$datefrom:""?>" readonly>
                   <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
               </div>
           </div>
       </div>
       <div class="form_row">
           <label class="field_name">Starting Balance</label>
           <div class="field">
                <input class="align_right col-md-4 required" id="startingamountloan" name="startingamountloan" type="text" value="<?=$currentamount?$currentamount:""?>"/>
           </div>
       </div>
       <div class="form_row">
           <label class="field_name">Current Balance</label>
           <div class="field">
                <input class="align_right col-md-4 required" id="currentamount" name="currentamount" type="text" disabled="" value="<?=$startingamount?$startingamount:""?>" style='background-color:#E8E8E8'   />
           </div>
       </div>
       <div class="form_row">
           <label class="field_name">No. of Cut-off</label>
           <div class="field">
                <input class="align_right col-md-4 required" id="nocutoff" name="nocutoff" type="text" disabled="" value="<?=$nocutoff?$nocutoff:""?>" style='background-color:#E8E8E8' />
           </div>
       </div>
       <div class="form_row">
           <label class="field_name">Amount</label>
           <div class="field">
                <input class="align_right col-md-4 required" id="amountloan" name="amountloan" type="text" disabled="" value="<?=$amount?$amount:""?>"  style='background-color:#E8E8E8' />
           </div>
       </div>
       <!-- <div class="form_row">
           <label class="field_name">Starting Balance</label>
           <div class="field">
                <input class="align_right col-md-4 required" id="startingamount" name="dstartingamount" type="text" value=""/>
           </div>
       </div> -->
     <!--   <div class="form_row">
           <label class="field_name">Amount</label>
           <div class="field">
                <input class="align_right col-md-4 required" id="amountloan" name="damountloan" type="text" value="" readonly/>
           </div>
       </div>-->
       <?
       if ($basedon == 1) {?>
         <div class="form_row" id='famounts' >
           <label class="field_name">Last Amount</label>
           <div class="field">
                <input class="align_right col-md-4 required" id="famount" name="dfamount" type="text" value="<?=$famount?>" readonly/>
           </div>
       </div>
       <?}
       ?>
       <div class="form_row" id='famounts' style="display: none">
           <label class="field_name">Last Amount</label>
           <div class="field">
                <input class="align_right col-md-4 required" id="famount" name="dfamount" type="text" value="" readonly/>
           </div>
       </div>
       <!-- <div class="form_row">
           <label class="field_name">No. of Cut-off</label>
           <div class="field">
                <input class="align_right col-md-4 required" id="nocutoff" name="dnocutoff" type="text" value=""/>
           </div>
       </div> -->
       <div class="form_row">
           <label class="field_name">Schedule</label>
           <div class="field no-search">
               <select class="chosen align_left" name="dschedule" id="dschedule"><?=$this->payrolloptions->payschedule($schedule);?></select>
           </div>
       </div>
       <div class="content" style="margin-top: 3px;" id="qload" hidden=""></div>
       <?
       if ($basedon == 1 || $basedon == 0 ) {?>
         <div class="form_row" id="qshow" >
           <label class="field_name">Quarter</label>
           <div class="field no-search">
               <select id="dperiod_drop" name="dperiod_drop" class="form-control"><?=$this->payrolloptions->quarter($cperiod,FALSE,$schedule);?></select>
           </div>
       </div>
       </div>
       <?}
       ?>
       <div class="form_row" id="qshow" hidden="">
           <label class="field_name">Quarter</label>
           <div class="field no-search">
               <select id="dperiod_drop" name="dperiod_drop" class="form-control"><?=$this->payrolloptions->quarter($cperiod,FALSE,$schedule);?></select>
           </div>
       </div>
   </div>
</form>
<script>
  var a = "<?=$basedon?>";
  if (a == 1) {
     $("#currentamount").prop('disabled',false);
          $("#currentamount").css('background-color','white');
          $("#nocutoff").css('background-color','#E8E8E8');
          $("#nocutoff").prop('disabled',true);
          $("#nocutoff").css('color','black');
          $("#amountloan").prop('disabled',false);
          $("#amountloan").css('background-color','white');
          $("#amountloan").css('color','black');
  }
  else
  {
    $("#currentamount").prop('disabled',false);
    $("#currentamount").css('background-color','white');
    $("#amountloan").css('background-color','#E8E8E8');
    $("#amountloan").prop('disabled',true);
    $("#amountloan").css('color','black');
    $("#nocutoff").css('color','black');
    $("#nocutoff").prop('disabled',false);
    $("#nocutoff").css('background-color','white');
  }
  $('#currentamount, #nocutoff').on('input',function(){
    var currentamount  = $('#currentamount').val();
    var nocutoff        = $('#nocutoff').val();
    var amountloan  = $('#amountloan').val();
    // if((startingamount != '' && amountloan !='')){
    //   $("#nocutoff").val(startingamount/amountloan).toFixed(2);
    //   return false;
    // }
    // {
      if ($("#basedon").val() == 0) {
        if (currentamount != "" && nocutoff != "")
         {
        var amt = currentamount / nocutoff;
        var amount = Math.floor(amt);
         var famount = (((currentamount - (amount * nocutoff))/nocutoff) + amount).toFixed(2);
          $('#amountloan').val(famount);
           
          }

        // $('#famount').val(famount);
       }
    // };
  });


  $('#amountloan, #currentamount').on('input',function(){
    var currentamount  = $('#currentamount').val();  
    var amountloan  = $('#amountloan').val();
    var nocutoff        = $('#nocutoff').val();
    if ($("#basedon").val() == 1) {
      // alert(currentamount);
      // alert(amountloan);
      // alert(nocutoff);
          if((currentamount != '' &&  amountloan !='')){
           var cutoffresult = Math.round(currentamount/amountloan);
           var famount = (currentamount - (amountloan * (cutoffresult - 1)));
          
           $("#nocutoff").val(cutoffresult);
           if (famount <=0) {
              $("#famount").val(0);
           }
           else
           {
              $("#famount").val(famount);
           }
          return false;
          }
          else
          {
            $("#nocutoff").val('');  
          }
    }
    // else
    // {
    // var amt = (amountloan * nocutoff).toFixed(2);
    // $('#currentamount').val(amt);
    // // var amount = Math.floor(amt);
    // //  var famount = (((amountloan - (amount * nocutoff))/nocutoff) + amount).toFixed(2);
    // }
  });

  $("#basedon").unbind('change').on('change',function()
  {
      var basedon = $(this).val();
      if (basedon == "1") {
          $("#currentamount").val('');
          $("#nocutoff").val('');
          $("#amountloan").val('');
          $("#currentamount").prop('disabled',false);
          $("#currentamount").css('background-color','white');
          $("#nocutoff").css('background-color','#E8E8E8');
          $("#nocutoff").prop('disabled',true);
          $("#nocutoff").css('color','black');
          $("#amountloan").prop('disabled',false);
          $("#amountloan").css('background-color','white');
          $("#amountloan").css('color','black');
          $('#famounts').show();
      }
      else if (basedon == "0") 
      {
          $('#famounts').hide();
          $('#famounts').val('');
          $("#currentamount").val('');
          $("#nocutoff").val('');
          $("#amountloan").val('');
          $("#currentamount").prop('disabled',false);
          $("#currentamount").css('background-color','white');
          $("#amountloan").css('background-color','#E8E8E8');
          $("#amountloan").prop('disabled',true);
          $("#amountloan").css('color','black');
          $("#nocutoff").css('color','black');
          $("#nocutoff").prop('disabled',false);
          $("#nocutoff").css('background-color','white');

      }
      else
      {
          $("#currentamount").val('');
          $("#nocutoff").val('');
          $("#amountloan").val('');
          $("#currentamount").css('background-color','#E8E8E8');
          $("#amountloan").css('background-color','#E8E8E8');
          $("#nocutoff").css('background-color','#E8E8E8');
          $("#currentamount").prop('disabled',true);
          $("#amountloan").prop('disabled',true);
          $("#nocutoff").prop('disabled',true);

      }
  });

  $("#dschedule").change(function(){
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
             $("select[name='dperiod_drop']").html(msg).trigger("liszt:updated");
             $("#qshow").show();
          }
      });
  });
$('.chosen').chosen();
$('#ddatefrom,#dateto').datepicker({
   autoclose: true,
   todayBtn : true
});
$("#cboxrangefrom").click(function(){
    $("#months_f,#days_f,#years_f").attr("disabled",!$(this).is(":checked"));
});
$("#cboxrangeto").click(function(){
    $("#months_t,#days_t,#years_t").attr("disabled",!$(this).is(":checked"));
});
$("#button_save_modal").unbind("click").click(function(){    
    var $validator = $("#form_addloan").validate({
        rules: {
            amountloan: {
              required: true
            }
        }
    });
    
    if($("#form_addloan").valid()){
           var form_data = $("#form_addloan").serialize();
               form_data += "&job=employee/loan_info";
               form_data += "&nocutoff="+$("#nocutoff").val();
               if ( $("#basedon").val() == 0) {
                    form_data+= "&amountloan="+$("#amountloan").val();
                }
                // console.log(form_data);return;
           $.ajax({
              url: "<?=site_url("employee_/validateinfo")?>",
              data : form_data,
              type : "POST",
              success:function(msg){
                $("#modalclose").click();
                refreshtab("#116");
                if ($(msg).find('status').text() == 1) {
                  alert($(msg).find('message').text());
                }
               
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