<?php

/**
 * @author Robert Ram Bolista
 * @copyright ram_bolista@yahoo.com
 * @date 6-25-2014
 * @time 19:47
 */
 
 
$sched = "";
$holiday_code = "";   
$holiday_rate = "";
$description = "";
$withPay = "";
$worked_hours = $worked_rate = $worked_excess = $restday_hours = $restday_rate = $restday_excess = 0;

$qsched = $this->db->query("select holiday_code,holiday_rate,description,withPay,worked_hours, worked_rate, worked_excess,restday_hours, restday_rate, restday_excess from code_holiday_type where holiday_type='{$holiday_type}'")->result();

if(count($qsched)>0){
 $holiday_code = $qsched[0]->holiday_code;   
 $holiday_rate = $qsched[0]->holiday_rate;
 $description = $qsched[0]->description;
 $withPay = $qsched[0]->withPay;
 $worked_hours = $qsched[0]->worked_hours;
 $worked_rate = $qsched[0]->worked_rate;
 $worked_excess = $qsched[0]->worked_excess;
 $restday_hours = $qsched[0]->restday_hours;
 $restday_rate = $qsched[0]->restday_rate;
 $restday_excess = $qsched[0]->restday_excess;

}
?>

<style type="text/css">
   @media (min-width: 452px){
  .modal-lg {
      width: 600px;
  }
  .modal-open .modal {
    overflow-x: unset;
    overflow-y: unset;
  }
  .modal.container{
    width: unset;
    margin-left: unset;
  }

  .form_row{
    padding-bottom: 10px;
  }

  .field{
    margin-right: -5px;
    padding-left: 40px; 
  }

  .field_name{
    margin-right: 20px;
  }
</style>

<div class="widgets_area">
<form id="form_schedule">
<input type="hidden" name="holiday_rate" id="holiday_rate" value="<?=$holiday_rate?Globals::_e($holiday_rate):'100.00'?>"/>
<div class="row">
    <div class="col-md-12">
        <div class="form_row">
            <label  class="field_name align_right">Code</label>
                <div class="field">
                    <input type="text" name="holiday_code" id="holiday_code" class="form-control isrequired" value="<?=$holiday_code?Globals::_e($holiday_code):''?>"/>
                    <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>

                </div>
            </div>
      
        <div class="form_row">
                <label class="field_name align_right">Description</label>
                <div class="field">
                    <input type="text" name="holiday_description" id="holiday_description" class="form-control isrequired" value="<?=$description?Globals::_e($description):''?>"/>
                    <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>

                </div>
        </div>
        
        <!-- <div class="form_row">
                <label class="field_name align_right">Unworked Rate</label>
                <div class="field">
                    <input type="number" name="holiday_rate" id="holiday_rate"  maxlength="5" class="form-control isrequired" value="<?=$holiday_rate?$holiday_rate:'0'?>"/>
                    <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>

                </div>
        </div> -->
        <!-- <br><br>
        <div class="form-group">
            <div class="col-md-12">
              <div class="col-md-4">
                <label class="field_name align_right">Worked First </label>
                <input type="number" name="worked_hours" id="worked_hours" class="form-control" maxlength="5" value="<?=$worked_hours?$worked_hours:'0'?>"/>
              </div>
              <div class="col-md-4">
                <label class="field_name align_right">Rate </label>
                <input type="number" name="worked_rate" id="worked_rate" class="form-control"  maxlength="5" value="<?=$worked_rate?$worked_rate:'0'?>"/> 
              </div>
              <div class="col-md-4">
                <label class="field_name align_right">Excess </label>
                <input type="number" name="worked_excess" id="worked_excess" class="form-control" maxlength="5" value="<?=$worked_excess?$worked_excess:'0'?>"/>
              </div>
            </div>
        </div>
        <br><br><br>
        <div class="form-group">
            <div class="col-md-12">
              <div class="col-md-4">
                <label class="field_name align_right">Rest Day First</label>
                <input type="number" name="restday_hours" id="restday_hours" class="form-control" maxlength="5" value="<?=$restday_hours?$restday_hours:'0'?>"/>
              </div>
              <div class="col-md-4">
                <label class="field_name align_right">Rate </label>
                <input type="number" name="restday_rate" id="restday_rate" class="form-control" maxlength="5" value="<?=$restday_rate?$restday_rate:'0'?>"/>
              </div>
              <div class="col-md-4">
                <label class="field_name align_right">Excess </label>
                <input type="number" name="restday_excess" id="restday_excess" class="form-control" maxlength="5" value="<?=$restday_excess?$restday_excess:'0'?>"/>
              </div>
            </div>
        </div> -->
      </div>
    </div>
  </div>
</div>    
</form>
</div>  
<script>
var toks = hex_sha512(" ");
if($("#withPay").val() == "NO")
{
  disableRate();
}

// $("#holiday_code").keypress(function (e) {
//     var keyCode = e.keyCode || e.which;
//     var regex = /^[A-Za-z0-9]+$/;
//     var isValid = regex.test(String.fromCharCode(keyCode));
//     return isValid;
// });

$(".chosen").chosen();

$("#save-dtr-setup").unbind("click").click(function(){
   var wh = $("#worked_hours").val();
   var wr = $("#worked_rate").val();
   var we = $("#worked_excess").val();
   var rh = $("#restday_hours").val();
   var rr = $("#restday_rate").val();
   var re = $("#restday_excess").val();
   var cancontinue = false;
   
   // cancontinue = checkifCodeExist($("#holiday_code").val(), 'code_holiday_type');
   if ($("#holiday_code").val()=="") {
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Code is required!',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  }

   else if ($("#holiday_description").val() =="") {
    // alert("Description is required!");
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Description is required!',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  }
   else if ($("#holiday_rate").val() < 0 ) {
    // alert("Unworked Rate has an invalid input number!");
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Unworked Rate has an invalid input number!',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  }
   else if (wh < 0 ) {alert("Worked Hours has an invalid input number!");}
   else if (wr < 0) {alert("Worked Rate has an invalid  input number!");}
   else if (we < 0) {alert("Worked Excess has an invalid input number!");}
   else if (rh < 0) {alert("Rest Hours has an invalid input number!");}
   else if (rr < 0) {alert("Rest Rate has an  invalid input number!");}
   else if (re < 0) {alert("Rest Excess has an invalid input number!");}
   else
   {

      var form_data = {
        holiday_code : GibberishAES.enc($("input[name='holiday_code']").val(), toks),
        holiday_description : GibberishAES.enc($("input[name='holiday_description']").val(), toks),
        holiday_rate : GibberishAES.enc($("input[name='holiday_rate']").val(), toks),
        holiday_type : GibberishAES.enc("<?=$holiday_type?>", toks),
        toks:toks
      };

           $.ajax({
            url: "<?=site_url("maintenance_/checkifCodeExist")?>",
            data : {code:GibberishAES.enc($("#holiday_code").val(), toks), table: GibberishAES.enc('code_holiday_type', toks), holiday_type: GibberishAES.enc('<?=$holiday_type?>', toks), toks:toks},
            type : "POST",
            success:function(msg){
              if(msg === '1'){
                  saveholidaytype(form_data);
              }else{
                  Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Code already exist!',
                    showConfirmButton: true,
                    timer: 1000
                  })
              }
            }
         });   
      }
});

$("#withPay").change(function(){
   if($(this).val() == "NO")
   {
     disableRate();
   }
   else
   {
     $("#holiday_rate").prop("readonly",false);
   }
});

function disableRate(){
  $("#holiday_rate").val(0);
  $("#holiday_rate").prop("readonly",true);
}

function saveholidaytype(form_data){
  $.ajax({
      url: "<?=site_url("maintenance_/saveholidaytype")?>",
      data : form_data,
      type : "POST",
      success:function(msg){
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: msg,
            showConfirmButton: true,
            timer: 1000
        })
        location.reload();
        table.fnDraw();
        $("#modalclose").click();
        cancontinue = true;
         // alert(msg);
      }
   });
}
</script>