<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
 $cdisable = false;
 $description = "";
 $basic = "";
 if($code){
     $sql = $this->db->query("select code_deduction,description,_type from deductions WHERE code_deduction='{$code}'");
     if($sql->num_rows()>0){
        $description = $sql->row(0)->description;
        $basic = $sql->row(0)->_type=="BASIC" ? " checked" : ""; 
     }
     $cdisable = true;
 }
?>
<form id="form_deduction">
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
    <label class="field_name align_right">Basic Deduction</label>
    <div class="field">
        <input class="col-md-1" id="mh_basic" name="mh_basic" type="checkbox" value="1"<?=($basic ? " checked='true'" : "")?>/>
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
 var $validator = $("#form_deduction").validate({
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
    
   if($("#form_deduction").valid()){   
         $.ajax({
            url:"<?=site_url("maintenance_/save_deduction")?>",
            type:"POST",
            data:{
               code: $("input[name='mh_code']").val(),
               description: $("input[name='mh_description']").val(),
               type: ($("input[name='mh_basic']").is(":checked") ? "BASIC" : "OTHERS")  
            },
            success: function(msg){
                $("#modalclose").click();
                //$("#mainform").submit();
                $(".inner_navigation .main li .active a").click(); 
            }
         });
   }else {
       $validator.focusInvalid();
       return false;
   }
});
</script>