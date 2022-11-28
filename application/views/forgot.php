<?php

    $CI =& get_instance();
    $CI->load->model('utils'); 
    $key = htmlspecialchars($_GET["key"]);

?>
<style type="text/css">
  
</style>
<!DOCTYPE html>
<html>
<head>
    <title>HYPERION</title>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/login.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/bootstrap.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
    <style type="text/css">
        body{
           font-family: avenir!important;
        }
        .school-name {
            font-family: avenir;
            text-transform: uppercase;
            font-size: 16px;
            font-weight: 700;
        }
        span.login-header-description {
            font-family: avenir;
            font-size: 18px;
            float: center;
            margin-top: -5px;
            font-weight: 700;
        }

        /*==========================requirements style============================*/
        .invalid {
          color: #848482;
          transition: all .1s ease-in;
        }
        .invalid:before {
          content: "";
          padding-right: 15px;
        }
        .invalidPass {
          background: rgba(192, 57, 43, 0.85);
        }
        .valid {
          color: #3CBC3C;
          transition: all .2s ease-in;
          animation-name: grow;
          animation-duration: .2s;
          animation-iteration-count: 1;
          animation-timing-function: ease-in;
        }
        .valid strong{
            text-shadow: 0px 5px 5px rgba(0,0,0,0.1),
            5px 10px 5px rgba(0,0,0,0.05),
            -5px 10px 5px rgba(0,0,0,0.05);
        }

        .valid::before {
          content: "\2713 ";
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
          left: -5px;
          position: relative;
          font-weight: 800;
          animation-name: grow;
          animation-duration: 1s;
          animation-iteration-count: 1;
          animation-timing-function: ease-in;
          -webkit-transition: -webkit-transform 1200ms cubic-bezier(0.19, 1, 0.22, 1), opacity 100ms ease-out;
          -moz-transition: -moz-transform 1200ms cubic-bezier(0.19, 1, 0.22, 1), opacity 100ms ease-out;
          -ms-transition: -ms-transform 1200ms cubic-bezier(0.19, 1, 0.22, 1), opacity 100ms ease-out;
          -o-transition: -o-transform 1200ms cubic-bezier(0.19, 1, 0.22, 1), opacity 100ms ease-out;
          transition: transform 1200ms cubic-bezier(0.19, 1, 0.22, 1), opacity 100ms ease-out;
          -webkit-transform: scale(1.3);
          -moz-transform: scale(1.3);
          -ms-transform: scale(1.3);
          -o-transform: scale(1.3);
          transform: scale(1.3);
          -webkit-backface-visibility: hidden;
        }

        .pswd_info ul {
          margin: 0 auto;
          width: 250px;
        }

        .pswd_info li {
          padding: 5px;
          text-align: left;
          -webkit-transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
          -moz-transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
          -ms-transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
          -o-transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
          transition: all 800ms cubic-bezier(0.19, 1, 0.22, 1);
          -webkit-backface-visibility: visible;
          transition: all .2s ease-in;
        }


        /*==========================requirements logic============================*/

        .pswd_info {
          display: none;
          padding: 10px 5px;
          margin-top: 5px;
          text-align: left;
          font-family: avenir;
          position: relative;
          text-align: center;
          margin: 1em auto;
          width: 400px;
          /*transition: all .2s ease-in;*/
        }

        .incorrectMsg {
          display: none;
        }

        .shake {
          animation: shake 0.8s cubic-bezier(.36, .07, .19, .97) both;
          transform: translate3d(0, 0, 0);
          backface-visibility: hidden;
          perspective: 1000px;
        }

        @keyframes shake {
          10%,
          90% {
            transform: translate3d(-1px, 0, 0);
          }
          20%,
          80% {
            transform: translate3d(2px, 0, 0);
          }
          30%,
          50%,
          70% {
            transform: translate3d(-4px, 0, 0);
          }
          40%,
          60% {
            transform: translate3d(4px, 0, 0);
          }
        }

        @keyframes grow {
          50% {
            transform: scale(1.1);
          }
        }
    </style>
</head>
<body class="bg">
<div class="my-container no-padding-margin">
    <br>
    <div class="hr-title">
        <p class="hr-title"></p>
    </div>

    <center>
        <div class="login-div align_left col-lg-12 col-md-4 col-sm-2" >
        <br>
            <div class="login-div-header align_center" style="margin-right: 3%; margin-left: 3%;">
                <table>
                    <tr>
                        <td class="align_left" rowspan="4">
                            
                        </td>
                        <td class="align_center login-header" style="text-align: center;">
                            <strong class="school-name" style="color:black; font-size: 18px"><?=$this->extras->school_name()?></strong><br>
                            <span class="login-header-description" style="color:black">D`Great<span>
                        </td>
                    </tr>
                </table>
            </div>
            <br>
            <h4 style="font-weight: bold; text-align: center">HR AND PAYROLL SYSTEM</h4>
           <h5 style="color:black ; font-weight: bold; margin-left: 3%;" class="login-desc">Password Reset</h5>
        <form>
            <div class="input-group" style="margin-right: 3%; margin-left: 3%;">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input id="fpassword" type="password" class="form-control" name="fpassword" placeholder="New Password ...">
                <span class="input-group-addon" id="viewnewpass" style="cursor: pointer;"><i class="glyphicon glyphicon-eye-close"></i></span>
            </div><br>
            <div class="input-group" style="margin-right: 3%; margin-left: 3%;">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input id="cpassword" type="password" class="form-control" placeholder="Confirm Password ...">
                <span class="input-group-addon" id="viewconfirmpass" style="cursor: pointer;"><i class="glyphicon glyphicon-eye-close"></i></span>
            </div>
            <div class="input-group" id="invalid" style="margin-right: 3%; margin-left: 3%;display: none;">
                <label id="emailExistWork" style="color: red">Password Mis-match</label>
            </div>
            <div id="popover-password" style="display: none;margin-right: 3%; margin-left: 3%;">
                <p>Password Strength: <span id="result"> </span></p>
                <div class="progress" >
                    <div id="password-strength" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                    </div>
                </div>
                <ul class="list-unstyled" style="text-align: left;">
                    <li class=""><span class="low-upper-case"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span>&nbsp; 1 lowercase &amp; 1 uppercase</li>
                    <li class=""><span class="one-number"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span> &nbsp;1 number (0-9)</li>
                    <li class=""><span class="one-special-char"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span> &nbsp;1 Special Character (!@#$%^&*).</li>
                    <li class=""><span class="eight-character"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span>&nbsp; Atleast 8 Character</li>
                </ul>
            </div>
            <br>
            <div style="display: grid;margin-left: 4%;margin-right: 4%;">
                <div>
                    <button type="button" class="btn btn-success" id='resetPass' site='<?=site_url('main/resetPass')?>' style="width: 100%;font-size: 24px;font-weight: 700;" disabled='true'>Reset<i class="icon-arrow-right"></i></button>
                </div>
            </div>
        </form>
    </div>
    <br>
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script src="<?=base_url()?>jsbstrap/jquery-1.10.2.js"></script>
<script src="<?=base_url()?>jsbstrap/bootstrap.js"></script>
<script src="<?=base_url()?>js/sweetalert.js"></script>

<script type="text/javascript">

  $("#viewnewpass").click(function(){
    if($("#fpassword").attr('type') == 'password'){
        $("#fpassword").removeAttr('type').attr('type','text');
        $(this).css("background-color", "#0072c6");
        $(this).find(".glyphicon").removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open');
    }else{
        $("#fpassword").removeAttr('type').attr('type','password');
        $(this).css("background-color", "#eee");
        $(this).find(".glyphicon").removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close');
    }
});

  $("#viewconfirmpass").click(function(){
    if($("#cpassword").attr('type') == 'password'){
        $("#cpassword").removeAttr('type').attr('type','text');
        $(this).css("background-color", "#0072c6");
        $(this).find(".glyphicon").removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open');
    }else{
        $("#cpassword").removeAttr('type').attr('type','password');
        $(this).css("background-color", "#eee");
        $(this).find(".glyphicon").removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close');
    }
});

$('#resetPass').click(function(e) {
    e.preventDefault();
    $("#resetPass").attr("disabled", true);
    var upass = $('#fpassword').val();
    var cpass = $('#cpassword').val();
    if (!upass) {
        alert('Password is required.');
        $('#fpassword').focus();
        return false;
    }

    if ($("#fpassword").val().length < 8) {
        Swal.fire({
            icon: 'info',
            title: 'Warning!',
            text: 'Password must contain at least 8 characters',
            showConfirmButton: true,
            timer: 1000
        })
        return;
    }

    if (cpass != upass) {
        $("#invalid").show();
        $("#resetPass").prop("disabled", true);
    }

    var pass = PasswordValidator($("#fpassword").val());
    if (!pass) {
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Password must contain at least 1 capital letter,\n\n1 small letter, 1 number and 1 special character',
          showConfirmButton: true,
          timer: 5000
      })
    }

    var form_data = {
        fpassword: upass,
        key: "<?= $key ?>"
    }

    $.ajax({
        url: $("#resetPass").attr("site"),
        type: "POST",
        data: form_data,
        dataType: "json",
        success: function(msg) {
            if (msg.status != 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: msg.msg,
                    showConfirmButton: true,
                    timer: 5000
                })
                $('#fpassword').focus();
                $('#fpassword').select();
                $("#resetPass").attr("disabled", false);
            } else {
                sessionStorage.clear();
                localStorage.clear();
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: msg.msg,
                    showConfirmButton: true,
                    timer: 2500
                })
                setTimeout(function() {
                    window.location.href = "<?=base_url()?>";
                }, 3000);
                
            }
        }
    });
    return false;
});

$("#cpassword").change(function() {
  if ($(this).val() == $("#fpassword").val()) {
    $("#invalid").hide();
    $("#resetPass").prop("disabled", false);
  }else{
    $("#invalid").show();
    $("#resetPass").prop("disabled", true);
  }
});

$("#fpassword").change(function() {
    $("#invalid").hide();
});

$('#fpassword').keyup(function() {
    $("#popover-password").show();
    var password = $('#fpassword').val();
    if (checkStrength(password) == 'Strong') {
        $('#resetPass').attr('disabled', false);
        $(".list-unstyled").hide();
    }else{
        $(".list-unstyled").show();
    }
});

function PasswordValidator(value) {
     var regex = new Array();
      regex.push("[A-Z]"); //Uppercase Alphabet.
      regex.push("[a-z]"); //Lowercase Alphabet.
      regex.push("[0-9]"); //Digit.
      regex.push("[!%&@#$^*?_~]"); //Special Character.

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

         
    

