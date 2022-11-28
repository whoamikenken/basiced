<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
 $code = "";
 $description = "";
 if($rid){
     $sql = $this->db->query("select id,request_code,description from code_request_type WHERE id='{$rid}'")->result();
     if(count($sql)>0){
      foreach($sql as $mrow){  
        $code = $mrow->request_code;
        $description = $mrow->description;
        # echo $code; 
      }
     }
 }
?>
<form id="form_request" method="POST" action="#">
<div class="form_row">
    <label class="field_name align_right">Code</label>
    <div class="field">
      <div class="col-md-10">
        <input class="form-control" id="u_code" name="u_code" type="text" value="<?=$code?>" placeholder="Code"/>
      </div>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Description</label>
    <div class="field">
      <div class="col-md-10">
        <input class="form-control" id="u_description" name="u_description" type="text" value="<?=$description?>" placeholder="Description"/>
      </div>
    </div>
</div>
</form>
<script>
  var ulist
$(function(){
   $("#form_request").validate({
        rules: {
            u_code: {
                required: true,
                minlength: 3
            },
            u_description: {
                required: true,
                minlength: 3
            }    
        },
        messages: {
            u_code: {
                required: 'Code is required'    
            },
            u_description: {
                required: 'Description is required',
                minlength: 'Input atlease 3 character'    
            }                
        }    
    });   
    $("#button_save_modal").unbind("click").click(function(){
        $("#form_request").submit();
    });
    $("#form_request").submit(function(){
       if($("#form_request").valid()){ 
       $.ajax({
           url:"<?=site_url("configuration_/saverequesttype")?>",
           data: $("#form_request").serialize()+"&rid=<?=$rid?>",
           type: "POST",
           success: function(msg){
              location.reload();
              $("#addrequesttype").click();                  
           }
       });
       } 
       return false;
    });
});
</script>