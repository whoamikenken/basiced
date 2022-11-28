<?php

/**
 * @author Justin
 * @copyright 2016
 */
$ppassExist = $this->employeemod->existPayslipPassword($this->session->userdata("username"));
$datetoday = date("d-m-Y");
?>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="panel animated fadeIn">
                   <div class="panel-heading"><h4><b>Change Account Password</b></h4></div>
                   <div class="panel-body">
                        <div class="col-md-6">
                            <form id="frmcpass" autocomplete="false" class="form-horizontal">
                                <div class="form-group">
                                        <label class="col-md-3">Current Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="curpass" name="curpass" />
                                    </div>
                                </div>
                                <div class="form-group">
                                        <label class="col-md-3">New Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="newpass" name="newpass"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                        <label class="col-md-3">Confirm Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="newpassc" name="newpassr" />
                                    </div>
                                </div>
                                <div class="form-group">
                                        <label class="col-md-3"></label>
                                    <div class="col-md-9">
                                        <a class="btn btn-primary" id="savecpass" href="#" class="btn btn-default">Save</a>
                                        <input type="reset" class="btn btn-primary" class="btn btn-default" />
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <div id="popover-password" style="display: none;margin-right: 3%; margin-left: 3%;">
                                    <p>Password Strength: <span id="result"> </span></p>
                                    <div class="progress" >
                                        <div class="progress-bar progress-bar-success" id="password-strength" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                        </div>
                                    </div>
                                    <ul class="list-unstyled" id="passwordValidatot" style="text-align: left;">
                                        <li class=""><span class="low-upper-case"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span>&nbsp; 1 lowercase &amp; 1 uppercase</li>
                                        <li class=""><span class="one-number"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span> &nbsp;1 number (0-9)</li>
                                        <li class=""><span class="one-special-char"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span> &nbsp;1 Special Character (!@#$%^&*).</li>
                                        <li class=""><span class="eight-character"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span>&nbsp; Atleast 8 Character</li>
                                    </ul>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
                
                <?if($this->session->userdata("usertype") == "EMPLOYEE"){?>
                <div class="panel animated fadeIn" <?= ($ppassExist)? "":"hidden='true'"?>>
                   <div class="panel-heading"><h4><b>Change Payslip Password</b></h4></div>
                   <div class="panel-body">
                        <div class="col-md-6">
                        <form id="frmcpass" autocomplete="false" class="form-horizontal">
                                <div class="form-group">
                                        <label class="col-md-3">Current Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="curppass" name="curppass" />
                                    </div>
                                </div>
                                <div class="form-group">
                                        <label class="col-md-3">New Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="newppass" name="newpass" />
                                    </div>
                                </div>
                                <div class="form-group">
                                        <label class="col-md-3">Confirm Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" id="newppassr" name="newpassr" />
                                    </div>
                                </div>
                            <div class="form-group">
                                    <label class="col-md-3"></label>
                                <div class="col-md-9">
                                    <a class="btn btn-primary" id="saveppass" href="#" class="btn btn-default">Save</a>
                                    <!-- <input type="reset" class="btn btn-primary" class="btn btn-default" /> -->
                                </div>
                            </div>
                        </form>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-12">
                                <div id="popover-password-pp" style="display: none;margin-right: 3%; margin-left: 3%;">
                                    <p>Password Strength: <span id="result-pp"> </span></p>
                                    <div class="progress" >
                                        <div class="progress-bar progress-bar-success" id="password-strength-pp" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                        </div>
                                    </div>
                                    <ul class="list-unstyled" id="passwordValidatot-pp" style="text-align: left;">
                                        <li class=""><span class="low-upper-case"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span>&nbsp; 1 lowercase &amp; 1 uppercase</li>
                                        <li class=""><span class="one-number"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span> &nbsp;1 number (0-9)</li>
                                        <li class=""><span class="one-special-char"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span> &nbsp;1 Special Character (!@#$%^&*).</li>
                                        <li class=""><span class="eight-character"><i class="glyphicon glyphicon-remove" aria-hidden="true"></i></span>&nbsp; Atleast 8 Character</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
                <?}?>
                
            </div>
        </div>        
    </div>        
</div>
<script>


$('#newpass').keyup(function() {
    $("#popover-password-pp").hide();
    $("#popover-password").show();
    var password = $('#newpass').val();
    if (checkStrength(password) == 'Strong') {
        $('#savecpass').attr('disabled', false);
        $("#passwordValidatot").hide();
    }else{
        $('#savecpass').attr('disabled', true);
        $("#passwordValidatot").show();
    }
});

$('#newppass').keyup(function() {
    $("#popover-password").hide();
    $("#popover-password-pp").show();
    var password = $('#newppass').val();
    if (checkStrengthPP(password) == 'Strong') {
        $('#saveppass').attr('disabled', false);
        $("#passwordValidatot-pp").hide();
    }else{
        $('#saveppass').attr('disabled', true);
        $("#passwordValidatot-pp").show();
    }
});

function checkStrengthPP(password) {
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
        $('#result-pp').removeClass()
        $('#password-strength-pp').addClass('progress-bar-danger');
        $('#result-pp').addClass('text-danger').text('Weak');
        $('#password-strength-pp').css('width', '10%');
    } else if (strength == 2) {
        $('#result-pp').addClass('good');
        $('#password-strength-pp').removeClass('progress-bar-danger');
        $('#password-strength-pp').addClass('progress-bar-warning');
        $('#result-pp').addClass('text-warning').text('Fair')
        $('#password-strength-pp').css('width', '60%');
        return 'Fair'
    }else if (strength == 3) {
        $('#result-pp').addClass('good');
        $('#password-strength-pp').removeClass('progress-bar-danger');
        $('#password-strength-pp').addClass('progress-bar-warning');
        $('#result-pp').addClass('text-warning').text('Good')
        $('#password-strength-pp').css('width', '60%');
        return 'Good'
    } else if (strength == 4) {
        $('#result-pp').removeClass()
        $('#result-pp').addClass('strong');
        $('#password-strength-pp').removeClass('progress-bar-warning');
        $('#password-strength-pp').addClass('progress-bar-success');
        $('#result-pp').addClass('text-success').text('Strong');
        $('#password-strength-pp').css('width', '100%');

        return 'Strong'
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

function checkLength(el) {
  if (el.value.length < 5) {
    Swal.fire({
        icon: 'info',
        title: 'Warning!',
        text: 'Password must contain at least 5 characters',
        showConfirmButton: true,
        timer: 5000
    })
  }
}


$("#savecpass").click(function(){
    
    if ($("#newpass").val().length < 8) {
        Swal.fire({
            icon: 'info',
            title: 'Warning!',
            text: 'Password must contain at least 8 characters',
            showConfirmButton: true,
            timer: 5000
        })
        return;
    }

    if($("#newpass").val() != $("#newpassc").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Password mismatch!',
            showConfirmButton: true,
            timer: 1000
        })
        return false;
    }

    if($("#newpass").val() == '' || $("#newpassc").val() == ''){
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Password and confirm password is required',
          showConfirmButton: true,
          timer: 1000
      })
      return false;
    }

    var form_data   =   {
                            model   :   "changepass",
                            oldpass :   CryptoJS.MD5($("#curpass").val()).toString(),
                            newpass :   CryptoJS.MD5($("#newpass").val()).toString(),
                            retpass :   CryptoJS.MD5($("#newpassc").val()).toString()
                        };
    $.ajax({
        url      : "<?=site_url("employeemod_/loadmodelfunc")?>",
        type     : "POST",
        data     : form_data,
        success: function(msg){
			if(msg == "Successfully Saved!")
			{
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Password has been updated successfully.',
                    showConfirmButton: true,
                    timer: 1000
                })
				location.reload();
			}else if(msg == "Incorrect Current Password"){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Incorrect Current Password',
                    showConfirmButton: true,
                    timer: 1000
                })
            }
        }
    });  
});

// $("#newpassr").keypress(function(){
//     $("#test").text("");
// });
// $("#newpassr").blur(function(){
//    var npass = $("#newpass").val();
//    if(npass != "" && $(this).val() != ""){
//        if(npass != $(this).val())   $("#test").css({"color":"red","font-size":"11px"}).text("Password did not match!");
//        else                         $("#test").css({"color":"green","font-size":"11px"}).text("Password Match!");
//    }
// });
</script>

<?if($this->session->userdata("usertype") == "EMPLOYEE"){?>
<script>
$("#saveppass").click(function(){

    if ($("#newppass").val().length < 8) {
        Swal.fire({
            icon: 'info',
            title: 'Warning!',
            text: 'Password must contain at least 8 characters',
            showConfirmButton: true,
            timer: 5000
        })

        return;
    }

    if($("#newppass").val() != $("#newppassr").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Password mismatch!',
            showConfirmButton: true,
            timer: 1000
        })
        return false;
    }

    if($("#newppass").val() == '' || $("#newppassr").val() == ''){
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Password and confirm password is required',
          showConfirmButton: true,
          timer: 1000
      })
      return false;
    }

	var form_data   =   {
        model   :   "changeppass",
        oldpass :   CryptoJS.MD5($("#curppass").val()).toString(),
        newpass :   CryptoJS.MD5($("#newppass").val()).toString(),
        retpass :   CryptoJS.MD5($("#newppassr").val()).toString()
    };
    
    $.ajax({
        url      : "<?=site_url("employeemod_/loadmodelfunc")?>",
        type     : "POST",
        data     : form_data,
        success: function(msg){
			if(msg == "Successfully Saved!")
			{
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Password has been updated successfully.',
                    showConfirmButton: true,
                    timer: 1000
                })
				location.reload();
			}else if(msg == "Incorrect Current Password"){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Incorrect Current Password',
                    showConfirmButton: true,
                    timer: 1000
                })
            }
        }
    });
});

$("#newppassr").keypress(function(){
    $("#test2").text("");
});
$("#newppassr").blur(function(){
   var npass = $("#newppass").val();
   if(npass != "" && $(this).val() != ""){
       if(npass != $(this).val())   $("#test2").css({"color":"red","font-size":"11px"}).text("Password did not match!");
       else                         $("#test2").css({"color":"red","font-size":"11px"}).text("Password Match!");
   }
});

$('#show_password').hover(function functionName() {
    $('#newppass').attr('type', 'text');
    $('.icon').removeClass('icon-eye-open').addClass('icon-eye-close');
}, function () {
    $('#newppass').attr('type', 'password');
    $('.icon').removeClass('icon-eye-close').addClass('icon-eye-open');
   }
);

</script>
<?}?>