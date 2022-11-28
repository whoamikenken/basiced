<?php
	//Added 6-8-2017
	$ppassExist = $this->employeemod->existPayslipPassword($this->session->userdata("username"));
	// echo "<pre>";print_r($ppassExist);die;
?>
<style type="text/css">
	.panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content">
    <div class="widgets_area" >
        <div class="row">  
            <div class="col-md-12" >
                <div class="panel animated fadeIn" >
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>My Payslip</b></h4></div>
                   <div class="panel-body" id="payslipcontent" style="padding-bottom: 32px; ">
						<?if($ppassExist){?>
						<div class="content" >
							<div class="form-group">
					            <div class="col-md-12">
					                <label  for="employeeid" class="col-sm-1" style="width: auto!important;">Enter Password</label>
					                <div class="col-sm-4" >
					                    <input class="form-control" id="password" type="password"/>
					                </div>
							            <div id="message" ></div>
					            </div>
					             <div class="col-sm-3" style="margin-left: 10%; margin-top: 1%;" >
        								<a href="#" class="btn btn-primary" id="verify">Verify</a>
					             </div>
					        </div>
						</div>
						<?}else{?>
							<div class="col-md-6">
	                        <form id="frmcpass" autocomplete="false" class="form-horizontal">
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
	                                    <p>Password Strength: <span id="result"> </span></p>
	                                    <div class="progress" >
	                                        <div class="progress-bar progress-bar-success" id="password-strength" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
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
						<?}?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var toks = hex_sha512(" ");
	$("#verify").click(function(){
		$("#message").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
		var password = $("#password").val();
		$.ajax({
			url      :   "<?=site_url("employeemod_/verifyPayslipPassword")?>",
			type     :   "POST",
			data     :   {password:  GibberishAES.enc(password , toks), toks:toks},
			success  :   function(msg){
				if(msg == "SUCCESS")
				{
					$.ajax({
						url      :   "<?=site_url("employeemod_/fileconfig")?>",
						type     :   "POST",
						data     :   {folder: "employeemod/myPayroll", view: "myPayslip"},
						success  :   function(msg){
							$("#payslipcontent").html(msg);
						}
					});
				}
				else
				{
					$("#message").html("<label style='color:red'>Incorrect Password</label>");
				}
			}
		});
	});

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
            model   :   "addppass",
            newpass :   CryptoJS.MD5($("#newppass").val()).toString()
        };
	    $.ajax({
	        url      : "<?=site_url("employeemod_/loadmodelfunc")?>",
	        type     : "POST",
	        data     : form_data,
	        success: function(msg){
				if(msg == "Successfully Saved!.")
				{
	                Swal.fire({
	                    icon: 'success',
	                    title: 'Success!',
	                    text: 'Password has been saved successfully.',
	                    showConfirmButton: true,
	                    timer: 1000
	              })
					location.reload();
				}
	        }
	    });
	});

	$('#show_password').hover(function functionName() {
	    $('#newppass').attr('type', 'text');
	    $('.icon').removeClass('icon-eye-open').addClass('icon-eye-close');
	}, function () {
	    $('#newppass').attr('type', 'password');
	    $('.icon').removeClass('icon-eye-close').addClass('icon-eye-open');
	   }
	);

	$('#newppass').keyup(function() {
	    $("#popover-password").hide();
	    $("#popover-password-pp").show();
	    var password = $('#newppass').val();
	    if (checkStrength(password) == 'Strong') {
	        $('#saveppass').attr('disabled', false);
	        $("#passwordValidatot-pp").hide();
	    }else{
	        $('#saveppass').attr('disabled', true);
	        $("#passwordValidatot-pp").show();
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