<?php

 $cdisable = false;
 $description = "";
  $management = "";
 if($code){
     $sql = $this->db->query("select * from code_department WHERE code='{$code}'");
     if($sql->num_rows()>0){
        $description = $sql->row(0)->description;
        $depthead = $sql->row(0)->head;
        $divhead = $sql->row(0)->divisionhead;
		$management = $sql->row(0)->managementid;
     }
     $cdisable = true;
 }
?>
<form id="form_department">
    <div class="form_row">
        <label class="field_name align_right">Code</label>
        <div class="field">
            <input class="span4 required" id="mh_code" name="mh_code" type="text" value="<?=$code?>"<?=($cdisable?" readonly":"")?>/>
        </div>
    </div>
    <div class="form_row">
        <label class="field_name align_right">Description</label>
        <div class="field">
            <input class="span8 required" id="mh_description" name="mh_description" type="text" value="<?=$description?>"/>
        </div>
    </div>
	<div class="form_row">
      <label class="field_name align_right">Division Level</label>
      <div class="field">
          <select class="chosen" name="mh_division" id="mh_division" style="width: 300px;" >
          <?
            $opt_type = $this->extras->showManagement();
            foreach($opt_type as $c=>$val){
            ?><option <?=($c==$management ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
            }
          ?>
          </select>
      </div>
    </div>
    <div class="form_row">
        <label class="field_name align_right">Dept. Head</label>
        <div class="field">
            <select class="chosen" id="mh_dept" name="mh_dept" style="width: 300px;" ><?=$this->employee->loadallempid($depthead)?></select>
        </div>
    </div>
    <div class="form_row">
        <label class="field_name align_right">Cluster Head</label>
        <div class="field">
            <select class="chosen" id="mh_div" name="mh_div" style="width: 300px;" ><?=$this->employee->loadallempid($divhead)?></select>
        </div>
    </div>
</form>
<script>
$("#button_save_modal").unbind("click").click(function(){
 var $validator = $("#form_department").validate({
        rules: {
            mh_code: {
              required: true,
              minlength: 1
            },
            mh_description: {
              required: true,
              minlength: 2
            },
            mh_division: {
              valueNotEquals: "" 
			}
        }
    });
    
   if($("#form_department").valid()){   
         $.ajax({
            url:"<?=site_url("maintenance_/save_department")?>",
            type:"POST",
            data:{
               code: $("input[name='mh_code']").val(),
               description: $("input[name='mh_description']").val(),
               division: $("#mh_division").val(),
               head: $("#mh_dept").val(),
               divhead: $("#mh_div").val(),
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
$(".chosen").chosen();
</script>