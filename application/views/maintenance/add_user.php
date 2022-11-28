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
 $email = "";
 $campusid = array();
 $campuses = $this->db->query("SELECT * FROM code_campus")->result_array();
 if($uid){
     $sql = $this->db->query("select a.username,b.lname,b.fname,b.mname,a.firstname, a.middlename, a.lastname, a.type, b.email, b.campusid, a.campus, a.email AS emailAdmin from user_info a left join employee b on a.username=b.employeeid WHERE a.id='{$uid}'")->result();
     if(count($sql)>0){
      foreach($sql as $mrow){
        if($mrow->lname){
          $lastname = $mrow->lname;
          $fistname = $mrow->fname;
          $middlename = $mrow->mname;
          $campusid[0] = $mrow->campusid;
          $email = $mrow->email;
        }else{
          $lastname = $mrow->lastname;
          $fistname = $mrow->firstname;
          $middlename = $mrow->middlename;
          $campusid = explode(',', $mrow->campus);
          $email = $mrow->emailAdmin;
        }  
        
        
        $username = $mrow->username;
        $utype = $mrow->type;
      }
     }
 }

?>

<style>
  .error{
    color:red;
  }
  .field_name{
    width: 50%!important;
  }
</style>
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<form id="form_user" method="POST" action="#">
<div class="form_row">
    <label class="field_name align_left" style="margin-left: 17px;">User Name</label>
    <div class="col-md-12">
        <input class="form-control" id="u_username" name="u_username" type="text" value="<?=$username?>" placeholder="User Name" <?=($uid) ? "readonly" : " "?>/>
        <span style="color: red;display: none;" id="warning">&nbsp;&nbsp;Username already exist!</span>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_left" style="margin-left: 17px;">First Name</label>
    <div class="col-md-12">
        <input class="form-control" id="u_firstname" name="u_firstname" type="text" value="<?=$fistname?>" placeholder="First Name"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_left" style="margin-left: 17px;">Middle Name</label>
    <div class="col-md-12">
        <input class="form-control" id="u_middlename" name="u_middlename" type="text" value="<?=$middlename?>" placeholder="Middle Name"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_left" style="margin-left: 17px;">Last Name</label>
    <div class="col-md-12">
        <input class="form-control" id="u_lastname" name="u_lastname" type="text" value="<?=$lastname?>" placeholder="Last Name"/>
    </div>
</div>
<div class="form_row" id="empEml">
    <label class="field_name align_left" style="margin-left: 17px;">Work Email Address</label>
    <div class="col-md-12">
        <input class="form-control" name="email" type="email" value="<?=$email?>" placeholder="Work Email"/>
        <label id="emailExistWork" class="error" style="display: none;">Email is existing</label>
    </div>
</div>
<div class="form_row" id="admEml">
    <label class="field_name align_left" style="margin-left: 17px;">Admin Email Address</label>
    <div class="col-md-12">
        <input class="form-control" name="emailAdmin" type="email" value="<?=$email?>" placeholder="Admin Email"/>
        <label id="emailExistAdmin" class="error" style="display: none;">Email is existing</label>
    </div>
</div>
<?if($uid){?>
<div class="form_row">
    <i>Just leave it blank if there's no changes in his/her password</i>
</div>
<?}?>
<div class="form_row">
    <label class="field_name align_left" style="margin-left: 17px;">Password</label>
    <div class="col-md-12">
        <input class="form-control" id="u_password" name="u_password" type="password" placeholder="Password" onblur="checkLength(this)"/>
    </div>
</div>
<div id="popover-password" style="display: none;margin-right: 3%; margin-left: 3%;">
    <p>Password Strength: <span id="result"> </span></p>
    <div class="progress" >
        <div id="password-strength" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
        </div>
    </div>
    <ul class="list-unstyled" id="checkerPassword" style="text-align: left;">
        <li class=""><span class="low-upper-case"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span>&nbsp; 1 lowercase &amp; 1 uppercase</li>
        <li class=""><span class="one-number"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span> &nbsp;1 number (0-9)</li>
        <li class=""><span class="one-special-char"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span> &nbsp;1 Special Character (!@#$%^&*).</li>
        <li class=""><span class="eight-character"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span>&nbsp; Atleast 8 Character</li>
    </ul>
</div>
<div class="form_row">
    <label class="field_name align_left" style="margin-left: 17px;">Confirm password</label>
    <div class="col-md-12">
        <input class="form-control" id="u_cpassword" name="u_cpassword" type="password" placeholder="Confirm Password" />
    </div>
</div>
<div class="input-group" id="invalid" style="margin-right: 3%; margin-left: 3%;display: none;">
    <label id="emailExistWork" style="color: red">Password Mis-match</label>
</div>
<div class="form_row">
    <label class="field_name align_left" style="margin-left: 17px;">Type</label>
    <div class="col-md-12">
        <select class="form-control" id="u_type" name="u_type">
          <!-- <?=$this->extras->showusertype($utype)?> -->
          <?php if(!$uid){ ?>
              <option value="ADMIN" <?= ($utype == "ADMIN")? "selected":"" ?>>ADMIN</option>
          <?php }else{  ?>
              <option value="ADMIN" <?= ($utype == "ADMIN")? "selected":"" ?>>ADMIN</option>
              <option value="EMPLOYEE" <?= ($utype == "EMPLOYEE")? "selected":"" ?>>EMPLOYEE</option>
          <?php } ?>
        </select>
    </div>
</div> 
<div class="form_row" id="empCamp">
    <label class="field_name align_left" style="margin-left: 17px;">Campus</label>
    <div class="col-md-12">
        <select class="chosen" name="campusid">
                <?= $this->extras->getCampuses($campusid[0]) ?>
        </select>
    </div>
</div>
<div class="form_row" id="admCamp">
    <label class="field_name align_left" style="margin-left: 17px;">Campus</label>
    <div class="col-md-12">
        <select class="chosen" id="campusAdmin" multiple="multiple">
            <?php foreach($campuses as $value): ?>
                <option value="<?= $value['code'] ?>" <?= (in_array($value['code'] , $campusid, TRUE)? "selected":"") ?>> <?= $value['description'] ?></option>
            <?php endforeach ?>
        </select>
    </div>
</div> 
</form>
<script>
if("<?=$utype?>" == "EMPLOYEE"){
  $("#u_firstname").attr("readonly", true);
  $("#u_middlename").attr("readonly", true);
  $("#u_lastname").attr("readonly", true);
}
var iscontinue = false;
var toks = hex_sha512(" ");
function checkLength(el) {
  if (el.value.length <= 5) {
    Swal.fire({
        icon: 'info',
        title: 'Warning!',
        text: 'Password must contain at least 5 characters',
        showConfirmButton: true,
        timer: 5000
    })
  }
}

$(function(){
    typeCheck("<?= ($utype =="")? "ADMIN":$utype ?>");
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
        }    
    });   
   
    $('#u_type').chosen();
    $('.chosen').chosen();
    
    $("#button_save_modal").unbind("click").click(function(){
      if ("<?= $uid ?>" == "") {
        // console.log(iscontinue);
        if(iscontinue){
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'Username already exists.',
              showConfirmButton: true,
              timer: 5000
          })
          $("#u_username").focus();
          $("#u_username").select();
          return false;
         }

        if($("#u_password").val() == '' || $("#u_cpassword").val() == ''){
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'Password and confirm password is required',
              showConfirmButton: true,
              timer: 1000
          })
          return false;
        }

        if($("#u_password").val().length < 8){
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'Password must contain at least 8 characters',
              showConfirmButton: true,
              timer: 5000
          })
          return false;
         }

         var pass = PasswordValidator($("#u_password").val());
          if (!pass) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Password must contain at least 1 capital letter,\n\n1 small letter, 1 number and 1 special character',
                showConfirmButton: true,
                timer: 5000
            })
          }
      }else{
        if($("#u_password").val() != ""){
          if($("#u_password").val().length < 8){
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'Password must contain at least 8 characters',
              showConfirmButton: true,
              timer: 5000
          })
          return false;
         }
        } 
      }


      if($("#u_password").val() != $("#u_cpassword").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Password mismatch!',
            showConfirmButton: true,
            timer: 1000
        })
        return false;
       }


        $("#form_user").submit();
    });

    $("input[name='u_username']").on("change", function(){
        $.ajax({
            url: $("#site_url").val() + "/employee_/isUsernameExist",
            type: "POST",
            data: { employeeid :  GibberishAES.enc($(this).val() , toks), toks:toks },
            success:function(response){
                if(response==1){
                    iscontinue = true;
                    $("input[name='u_username']").css("border-color", "red");
                    $("#warning").show();
                }
                else{
                    iscontinue = false;
                    $("#warning").hide();
                    $("input[name='u_username']").css("border-color", "black");
                }
            }
        });
    });

    $("input[name='email'], select[name='campusid']").change(function() {
      var empId = "<?= $utype ?>";
      if (empId == "ADMIN") {
        return false;
      }
      
        var formdata = {
            column:  GibberishAES.enc($(this).attr("name") , toks),
            value:  GibberishAES.enc($(this).val() , toks),
            employeeid: GibberishAES.enc($("#u_username").val()  , toks),
            toks:toks
        };
        $.ajax({
            url: $("#site_url").val() + "/employee_/updateEmployeeInformation",
            data: formdata,
            type: "POST",
            success: function(response) {
              if (response == "EmailExist") {
                $("#emailExistWork").show();
              }
            }
        });
    });

    $("#u_cpassword").change(function() {
      if ($(this).val() == $("#u_password").val()) {
        $("#invalid").hide();
      }else{
        $("#invalid").show();
      }
    });

    $("#u_password").change(function() {
        $("#invalid").hide();
    });

    $("#u_type").change(function() {
      var type = $(this).val();
      typeCheck(type);
    });

    function typeCheck(type){
      if (type == "ADMIN") {
        $("#u_firstname").attr("readonly", false);
        $("#u_middlename").attr("readonly", false);
        $("#u_lastname").attr("readonly", false);
        $("#admCamp").show();
        $("#admEml").show();
        $("#empCamp").hide();
        $("#empEml").hide();
      }else if(type == "EMPLOYEE"){
        $("#u_firstname").attr("readonly", true);
        $("#u_middlename").attr("readonly", true);
        $("#u_lastname").attr("readonly", true);
        $("#empCamp").show();
        $("#empEml").show();
        $("#admCamp").hide();
        $("#admEml").hide();
      }
    }

    $("#form_user").submit(function(){
      var formdata   =  '';
      $('#form_user input, #form_user select, #form_user textarea').each(function(){
          if(formdata) formdata += '&'+$(this).attr('name')+'='+$(this).val();
          else formdata = $(this).attr('name')+'='+$(this).val();
      })
      formdata += "&uid=<?=$uid?>&job=<?=$job?>&campusAdmin="+$("#campusAdmin").val();
       if($("#form_user").valid()){ 
       $.ajax({
           url:"<?=site_url("maintenance_/saveuser")?>",
           data: {formdata:GibberishAES.enc(formdata, toks), toks:toks},
           type: "POST",
           success: function(msg){
              var message = $(msg).find("message:eq(0)").text();
              var stat = $(msg).find("status:eq(0)").text();      
              switch(stat){
                 case "1":
                  $("#u_username").focus();
                  $("#u_username").select();
                 break;
                 case "2":
                  $("#u_firstname").select();
                  $("#u_firstname").focus();
                 break;
                 case "3":
                  $("#emailExistAdmin").show();
                  $("#emailAdmin").focus();
                 break;
                 default:
                 Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: message,
                      showConfirmButton: true,
                      timer: 1000
                  })
                 setTimeout(function() {
                  user_setup();
                  $("#adduser").click();
                 }, 1500);                
                 break;
              }
           }
       });
       } 
       return false;
    });
});

function PasswordValidator(value) {
     var regex = new Array();
      regex.push("[A-Z]"); //Uppercase Alphabet.
      regex.push("[a-z]"); //Lowercase Alphabet.
      regex.push("[0-9]"); //Digit.
      regex.push("[!@#$%^&*]"); //Special Character.

      var passed = 0;
      for (var i = 0; i < regex.length; i++) {
          if (new RegExp(regex[i]).test(value)) {
              passed++;
          }
      }

      if (passed > 3) {
          return true;
      }
      else {
          return false;
      }
}

$('#u_password').keyup(function() {
    $("#popover-password").show();
    var password = $('#u_password').val();
    if (checkStrength(password) == 'Strong') {
        $('#resetPass').attr('disabled', false);
        $("#checkerPassword").hide();
    }else{
        $("#checkerPassword").show();
    }
});


function checkStrength(password) {
    var strength = 0;


    //If password contains both lower and uppercase characters, increase strength value.
    if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
        strength += 1;
        $('.low-upper-case').addClass('text-success');
        $('.low-upper-case i').removeClass('glyphicon-remove').addClass('glyphicon-ok');
        $('#popover-password-top').addClass('hide');


    } else {
        $('.low-upper-case').removeClass('text-success');
        $('.low-upper-case i').addClass('glyphicon-remove').removeClass('glyphicon-ok');
        $('#popover-password-top').removeClass('hide');
    }

    //If it has numbers and characters, increase strength value.
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) {
        strength += 1;
        $('.one-number').addClass('text-success');
        $('.one-number i').removeClass('glyphicon-remove').addClass('glyphicon-ok');
        $('#popover-password-top').addClass('hide');

    } else {
        $('.one-number').removeClass('text-success');
        $('.one-number i').addClass('glyphicon-remove').removeClass('glyphicon-ok');
        $('#popover-password-top').removeClass('hide');
    }

    //If it has one special character, increase strength value.
    if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) {
        strength += 1;
        $('.one-special-char').addClass('text-success');
        $('.one-special-char i').removeClass('glyphicon-remove').addClass('glyphicon-ok');
        $('#popover-password-top').addClass('hide');

    } else {
        $('.one-special-char').removeClass('text-success');
        $('.one-special-char i').addClass('glyphicon-remove').removeClass('glyphicon-ok');
        $('#popover-password-top').removeClass('hide');
    }

    if (password.length > 7) {
        strength += 1;
        $('.eight-character').addClass('text-success');
        $('.eight-character i').removeClass('glyphicon-remove').addClass('glyphicon-ok');
        $('#popover-password-top').addClass('hide');

    } else {
        $('.eight-character').removeClass('text-success');
        $('.eight-character i').addClass('glyphicon-remove').removeClass('glyphicon-ok');
        $('#popover-password-top').removeClass('hide');
    }


    // If value is less than 2

    if (strength < 2) {
        $('#result').removeClass()
        $('#password-strength').addClass('progress-bar-danger');

        $('#result').addClass('text-danger').text('Weak');
        $('#password-strength').css('width', '10%');
    } else if (strength == 2) {
        $('#result').addClass('good');
        $('#password-strength').removeClass('progress-bar-danger');
        $('#password-strength').addClass('progress-bar-warning');
        $('#result').addClass('text-warning').text('Fair')
        $('#password-strength').css('width', '60%');
        return 'Fair'
    }else if (strength == 3) {
        $('#result').addClass('good');
        $('#password-strength').removeClass('progress-bar-danger');
        $('#password-strength').addClass('progress-bar-warning');
        $('#result').addClass('text-warning').text('Good')
        $('#password-strength').css('width', '60%');
        return 'Good'
    } else if (strength == 4) {
        $('#result').removeClass()
        $('#result').addClass('strong');
        $('#password-strength').removeClass('progress-bar-warning');
        $('#password-strength').addClass('progress-bar-success');
        $('#result').addClass('text-success').text('Strong');
        $('#password-strength').css('width', '100%');

        return 'Strong'
    }

}
</script>