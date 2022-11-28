<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
 $username = "";
 $lastname = "";
 $fistname = "";
 $middlename = "";
 $utype = "";
 if($uid){
     $sql = $this->db->query("select username,lastname,firstname,middlename,type from user_info WHERE id='{$uid}'")->result();
     if(count($sql)>0){
      foreach($sql as $mrow){  
        $lastname = $mrow->lastname;
        $fistname = $mrow->firstname;
        $middlename = $mrow->middlename;
        $username = $mrow->username;
        $utype = $mrow->type;
      }
     }
 }
?>
<form id="form_user" method="POST" action="#">
<div class="form_row">
    <label class="field_name align_right">User Name</label>
    <div class="field">
        <input class="col-md-6" id="u_username" name="u_username" type="text" value="<?=$username?>" placeholder="User Name"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">First Name</label>
    <div class="field">
        <input class="col-md-6" id="u_firstname" name="u_firstname" type="text" value="<?=$fistname?>" placeholder="First Name"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Middle Name</label>
    <div class="field">
        <input class="col-md-6" id="u_middlename" name="u_middlename" type="text" value="<?=$middlename?>" placeholder="Middle Name"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Last Name</label>
    <div class="field">
        <input class="col-md-6" id="u_lastname" name="u_lastname" type="text" value="<?=$lastname?>" placeholder="Last Name"/>
    </div>
</div>
<?if($uid){?>
<div class="form_row">
    <i>Just leave it blank there's no changes in his/her password</i>
</div>
<?}?>
<div class="form_row">
    <label class="field_name align_right">Password</label>
    <div class="field">
        <input class="col-md-6" id="u_password" name="u_password" type="password" placeholder="Password"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Confirm password</label>
    <div class="field">
        <input class="col-md-6" id="u_cpassword" name="u_cpassword" type="password" placeholder="Confirm Password"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Type</label>
    <div class="field">
        <select class="col-md-12" id="u_type" name="u_type"><?=$this->extras->showusertype($utype)?></select>
    </div>
</div> <!--
<div class="form_row">
    <div class="field">
        <button class="btn btn-primary" id="mh_buttonsave">Save</button>
       <a href="#" class="btn grey">Cancel</a>
    </div>
</div>
--!>
</form>
<script>
$(function(){
   $("#form_user").validate({
        rules: {
            u_username: {
                required: true,
                minlength: 3
            },
            u_firstname: {
                required: true,
                minlength: 3
            },
            u_lastname: {
                required: true,
                minlength: 3
            },
            u_type :{
                required: true
            }
<?if(!$uid){?>
            ,
            u_password: {
                required: true,
                minlength: 4
            },
            u_cpassword: {
                required: true,
                minlength: 4,
                equalTo: "#u_password"    
            }
<?}?>            
        },
        messages: {
            u_username: {
                required: 'User name is required',
                minlength: 'Input atlease 3 character'    
            },
            u_firstname: {
                required: 'First name is required',
                minlength: 'Input atlease 3 character'    
            },
            u_lastname: {
                required: 'Last name is required',
                minlength: 'Input atlease 3 character'    
            },
            u_type :{
                required: 'Type is required'
            }
<?if(!$uid){?>
            ,
            u_password: {
                required: 'Password is required',
                minlength: 'Input atlease 4 character'    
            },
            u_cpassword: {
                required: 'Password is required',
                minlength: 'Input atlease 4 character',
                equalTo: "Password did not match"    
            }
<?}?>                        
        }    
    });   
    $('#u_type').chosen();
    $("#button_save_modal").unbind("click").click(function(){
        $("#form_user").submit();
    });
    $("#form_user").submit(function(){
       if($("#form_user").valid()){ 
       $.ajax({
           url:"<?=site_url("maintenance_/saveuser")?>",
           data: $("#form_user").serialize()+"&uid=<?=$uid?>&job=<?=$job?>",
           type: "POST",
           success: function(msg){
              var message = $(msg).find("message:eq(0)").text();
              var stat = $(msg).find("status:eq(0)").text();
              alert(message);
              
              switch(stat){
                 case "1":
                  $("#u_username").focus();
                  $("#u_username").select();
                 break;
                 case "2":
                  $("#u_firstname").select();
                  $("#u_firstname").focus();
                 break;
                 default:
                  ulist.fnDraw();
                  $("#adduser").click();                  
                 break;
              }
           }
       });
       } 
       return false;
    });
});
</script>