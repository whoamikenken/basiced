<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
 $cdisable = false;
 $description = "";
 $taxable = "";
 if($code){
     $sql = $this->db->query("select code_income,description,taxable from incomes WHERE code_income='{$code}'");
     if($sql->num_rows()>0){
        $description = $sql->row(0)->description;
        $taxable = $sql->row(0)->taxable=="YES" ? " checked" : "";
     }
     $cdisable = true;
 }
?>
<form id="form_income">
<div class="form_row">
    <label class="field_name align_right">Code</label>
    <div class="field">
        <input class="col-md-4 required" id="mh_code" name="mh_code" type="text" value="<?=$code?>"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Description</label>
    <div class="field">
        <input class="col-md-8 required" id="mh_description" name="mh_description" type="text" value="<?=$description?>"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Taxable</label>
    <div class="field">
        <input class="col-md-1" id="mh_taxable" name="mh_taxable" type="checkbox" value="1"<?=($taxable ? " checked='true'" : "")?>/>
    </div>
</div>
<div class="form_row">
    <div class="field">
        <a href="#" class="btn btn-primary" id="mh_buttonsave">Save</a>
        <!--<a href="#" class="btn grey">Cancel</a>--!>
    </div>
</div>
</form>
<script>
$("#mh_buttonsave").click(function(){
 var $validator = $("#form_income").validate({
        rules: {
            mh_code: {
              required: true,
              minlength: 2
            },
            mh_description: {
              required: true,
              minlength: 2
            }
        }
    });
    
   if($("#form_income").valid()){   
         $.ajax({
            url:"<?=site_url("maintenance_/save_income")?>",
            type:"POST",
            data:{
               code: $("input[name='mh_code']").val(),
               description: $("input[name='mh_description']").val(),
               taxable: ($("input[name='mh_taxable']").is(":checked") ? "YES" : "NO")  
            },
            success: function(msg){
                $("#modalclose").click();
                $(".inner_navigation .main li .active a").click(); 
            }
         });
   }else {
       $validator.focusInvalid();
       return false;
   }
});
</script>