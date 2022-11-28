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
 $salbase="";
 $er="";
 $ee="";
 $tc="";
 if($id){
     $sql = $this->db->query("SELECT * FROM hdmf_deduction WHERE id='{$id}'");
     if($sql->num_rows()>0){
        $salfrom = $sql->row(0)->compensationfrom;
        $salto = $sql->row(0)->compensationto;
        $salrange = $sql->row(0)->salary_range;
        $salbase = $sql->row(0)->salary_base;
        $er = $sql->row(0)->emp_er;
        $ee = $sql->row(0)->emp_ee;
        $tc = $sql->row(0)->total_contribution;
     }
     $cdisable = true;
 }
?>
<form name="form_hdmf" id="form_hdmf">
  <div class="col-md-12">
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Salary Range:</label>
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
      <label  for="employeeid" class="col-sm-3 align_right">Salary Base</label>
      <div class="col-sm-6">
         <input class="form-control" id="mh_salbase" name="mh_salbase" type="text" value="<?=$salbase?>" onblur="calculate();" onKeyUp="calculate();this.blur();this.focus();" onChange="calculate();"/>  
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
      <label  for="employeeid" class="col-sm-3 align_right">EE</label>
      <div class="col-sm-6">
        <input class="form-control" id="mh_ee" name="mh_ee" type="text" value="<?=$ee?>" onblur="calculate();" onKeyUp="calculate();this.blur();this.focus();" onChange="calculate();"/> 
      </div>
    </div>
    <br><br>
    <div class="form-group">
      <label  for="employeeid" class="col-sm-3 align_right">Total Contribution</label>
      <div class="col-sm-6">
        <input class="form-control" readonly="true" id="mh_tc" name="mh_tc" type="text" value="<?=$tc?>"/> 
      </div>
    </div>
  </div>
</form>
<script>
var toks = hex_sha512(" ");
function calculate() {
document.form_hdmf.mh_tc.value = (document.form_hdmf.mh_salbase.value -0) + (document.form_hdmf.mh_er.value -0) + (document.form_hdmf.mh_ee.value -0);
}
$("#button_save_modal").unbind("click").click(function(){
//$("#mh_buttonsave").click(function(){
 var $validator = $("#form_hdmf").validate({
        rules: {
            mh_comfrom: {
              required: true,
              minlength: 2
            }
        }
    });
 if($("#form_hdmf").valid()){  
 $.ajax({
    url:"<?=site_url("maintenance_/save_hdmf")?>",
    type:"POST",
    data:{ 
       toks:toks,
       hdmfid: GibberishAES.enc($("input[name='mh_id']").val(), toks),
       salfrom: GibberishAES.enc($("input[name='mh_comfrom']").val(), toks),
       salto: GibberishAES.enc($("input[name='mh_comto']").val(), toks),
       salrange: GibberishAES.enc($("input[name='mh_salrange']").val(), toks),
       salbase: GibberishAES.enc($("input[name='mh_salbase']").val(), toks),
       er: GibberishAES.enc($("input[name='mh_er']").val(), toks),
       ee: GibberishAES.enc($("input[name='mh_ee']").val(), toks),
       tc: GibberishAES.enc($("input[name='mh_tc']").val(), toks),
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