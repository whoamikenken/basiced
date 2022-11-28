<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
/**
 * @Kennedy Hipolitp
 * @2019
 * @Updated UI
 */
 
 $cdisable = false;
 $salto="";
 $salfrom="";
 $salrange="";
 $er="";
 $ec="";
 $ee="";
 $tc="";
 $prov_er="";
 $prov_ee="";
 $total_er="";
 $total_ee="";
 $total="";
 if($id){
     $sql = $this->db->query("SELECT * FROM sss_deduction WHERE id='{$id}'");
     if($sql->num_rows()>0){
        $salfrom = $sql->row(0)->compensationfrom;
        $salto   = $sql->row(0)->compensationto;
        $salrange = $sql->row(0)->salary_range;
        $er = $sql->row(0)->emp_er;
        $ec = $sql->row(0)->emp_con;
        $ee = $sql->row(0)->emp_ee;

        $prov_er = $sql->row(0)->provident_er;
        $prov_ee = $sql->row(0)->provident_ee;
        $total_er = $sql->row(0)->total_er;
        $total_ee = $sql->row(0)->total_ee;
        // $total = $sql->row(0)->total;
        $tc = $sql->row(0)->total_contribution;
     }
     $cdisable = true;
 }
?>
<form name="form_sss" id="form_sss">
<div class="col-md-12">
    <div class="form-group">
      <label for="employeeid" class="col-sm-3 align_right">Salary Range</label>
      <div class="col-sm-9">
         <input placeholder="ID" class="col-md-4 form-control" id="mh_id" name="mh_id" type="hidden" value="<?=$id?>"/>
         <div class="col-md-6"> 
         <input placeholder="from" class="form-control" id="mh_comfrom" name="mh_comfrom" type="text" value="<?=$salfrom?>"/>
         </div>
         <div class="col-md-6">   
         <input placeholder="to" class="form-control" id="mh_comto" name="mh_comto" type="text" value="<?=$salto?>"/>
         </div>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Salary Bracket</label>
      <div class="col-sm-6">
         <input class="form-control" id="mh_salrange" name="mh_salrange" type="text" value="<?=$salrange?>"/>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">ER</label>
      <div class="col-sm-6">
        <input class="form-control" id="mh_er" name="mh_er" type="text" value="<?=$er?>" onblur="calculate();" onKeyUp="calculate();this.blur();this.focus();" onChange="calculate();"/>
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">EC</label>
      <div class="col-sm-6">
        <input class="form-control" id="mh_ec" name="mh_ec" type="text" value="<?=$ec?>" onblur="calculate();" onKeyUp="calculate();this.blur();this.focus();" onChange="calculate();"/> 
      </div>
    </div>

    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">EE</label>
      <div class="col-sm-6">
        <input class="form-control" id="mh_ee" name="mh_ee" type="text" value="<?=$ee?>" onblur="calculate();" onKeyUp="calculate();this.blur();this.focus();" onChange="calculate();"/> 
      </div>
    </div>

    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Provident ER</label>
      <div class="col-sm-6">
        <input class="form-control" id="provident_er" name="provident_er" type="text" value="<?=$prov_er?>" onblur="calculate();" onKeyUp="calculate();this.blur();this.focus();" onChange="calculate();"/> 
      </div>
    </div>

    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Provident EE</label>
      <div class="col-sm-6">
        <input class="form-control" id="provident_ee" name="provident_ee" type="text" value="<?=$prov_ee?>" onblur="calculate();" onKeyUp="calculate();this.blur();this.focus();" onChange="calculate();"/> 
      </div>
    </div>

    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Total ER with Provident</label>
      <div class="col-sm-6">
        <input class="form-control" id="total_er" name="total_er" type="text" value="<?=$total_er?>" readonly> 
      </div>
    </div>

    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Total EE with Provident</label>
      <div class="col-sm-6">
        <input class="form-control" id="total_ee" name="total_ee" type="text" value="<?=$total_ee?>" readonly> 
      </div>
    </div>

    <!-- <br><br> -->
    <div class="form-group" style="display: none;">
      <label  for="employeeid" class="col-sm-3 align_right">Total</label>
      <div class="col-sm-6">
        <input class="form-control" id="total" name="total" type="text" value="<?=$total?>" readonly> 
      </div>
    </div>

    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Total Contribution</label>
      <div class="col-sm-6">
        <input class="form-control" readonly="true" id="mh_tc" name="mh_tc" type="text" value="<?=$tc?>"/> 
      </div>
    </div>

    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Year</label>
      <div class="col-sm-6">
        <select class="form-control" name="year" id="year"><?=$this->payrolloptions->periodyear("","",$year);?></select>
      </div>
    </div>

  </div>
  <script>
    $('.chosen-select').chosen()
  </script>
</form>
<script>
var toks = hex_sha512(" ");
function calculate() {
// document.form_sss.mh_tc.value = (document.form_sss.mh_er.value -0) + (document.form_sss.mh_ec.value -0) + (document.form_sss.mh_ee.value -0);
  var er = ($("#mh_er").val()) ? parseFloat($("#mh_er").val()) : 0;
  var ee = ($("#mh_ee").val()) ? parseFloat($("#mh_ee").val()) : 0;
  var ec = ($("#mh_ec").val()) ? parseFloat($("#mh_ec").val()) : 0;
  var prov_er = ($("#provident_er").val()) ? parseFloat($("#provident_er").val()) : 0;
  var prov_ee = ($("#provident_ee").val()) ? parseFloat($("#provident_ee").val()) : 0;
  var total_er = er+prov_er;
  var total_ee = ee+prov_ee;
  var total = total_er + total_ee + ec;
  $("#mh_tc").val(total);
  $("#total_er").val(total_er);
  $("#total_ee").val(total_ee);
}
$("#button_save_modal").unbind("click").click(function(){
//$("#mh_buttonsave").click(function(){
 var $validator = $("#form_sss").validate({
        rules: {
            mh_comfrom: {
              required: true,
              minlength: 2
            }
        }
    });
 if($("#form_sss").valid()){  
 $.ajax({
    url:"<?=site_url("maintenance_/save_sss")?>",
    type:"POST",
    data:{ 
       toks:toks,
       sssid: GibberishAES.enc($("input[name='mh_id']").val(), toks),
       salfrom: GibberishAES.enc($("input[name='mh_comfrom']").val(), toks),
       salto: GibberishAES.enc($("input[name='mh_comto']").val(), toks),
       salrange: GibberishAES.enc($("input[name='mh_salrange']").val(), toks),
       er: GibberishAES.enc($("input[name='mh_er']").val(), toks),
       ec: GibberishAES.enc($("input[name='mh_ec']").val(), toks),
       ee: GibberishAES.enc($("input[name='mh_ee']").val(), toks),
       tc: GibberishAES.enc($("input[name='mh_tc']").val(), toks),

       prov_er: GibberishAES.enc($("input[name='provident_er']").val(), toks),
       prov_ee: GibberishAES.enc($("input[name='provident_ee']").val(), toks),
       total_er: GibberishAES.enc($("input[name='total_er']").val(), toks),
       total_ee: GibberishAES.enc($("input[name='total_ee']").val(), toks),
       total: GibberishAES.enc($("input[name='total']").val(), toks),
       year: GibberishAES.enc($("select[name='year']").val(), toks)
    },
    success: function(msg){
       $("#modalclose").click();
       $(".inner_navigation .main li .active a").click(); 
       location.reload();
    }
 });
 }else {
       $validator.focusInvalid();
       return false;
   }  
});
</script>
