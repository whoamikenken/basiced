<?
/**
* @author justin (with e)
* @copyright 2018
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>

<div class="my-container no-padding-margin">
	<div class="hr-title">
        &nbsp;
    </div>

    <center>
    	<div class="login-div align_left">
            <div class="login-div-header align_center">
                <table>
                    <tr>
                        <td class="align_left" rowspan="6">
                            <img src="<?=base_url()?>/images/school_logo.png" class="login-logo">
                        </td>
                        <td class="align_center login-header">
                            <strong><?=$this->extras->school_name()?></strong><br>
                            <span class="login-header-description">Dasmariñas Cavite<span>
                        </td>
                    </tr>
                </table>
            </div>
           	<h5 class="login-lbl">Log In</h5>
           	<form id="frm-login">
           	<div class="login-field">
                <label for="username">Username</label>
                <input type="text" name="fusername" id="fusername" placeholder="Username">
                <i class="icon-user"></i>
            </div>
            <div class="login-field">
                <label for="password">Password</label>
                <input type="password" name="fpassword" id="fpassword" placeholder="Password">
                <i class="icon-lock"></i>
            </div>
            <div class="login-button clearfix">
                <button type="submit" class="pull-right btn btn-lg blue" id='logsubmit' site='<?=site_url('main/validate')?>'>
                	SIGN IN 
                	<i class="icon-arrow-right"></i>
                </button>
            </div>
            </form>
       </span>
    </center>
</div>
<script type="text/javascript">

	$("#frm-login").submit(function(event){
		var error = "";
		if(!$("#fusername").val()) error = "Username is required.";
		if(!$("#fpassword").val() && !error) error = "Password is required.";

		var formdata = {
			username    : $("#fusername").val(),
			password    : getHashPassword($("#fpassword").val()),
            gateaccess  : 1
		};

        if(!error){
            $("#fpassword").val("");

            $.ajax({
                url : "<?=site_url("gate_/validateGateAccount")?>",
                type : "POST",
                data : formdata,
                success : function(response){

                    if(!response.trim()) location.reload();
                    else                 alert(response);
                }
            });
        }else alert(error);
		event.preventDefault();
	});

    $(document).ready(function(){
        $("#fusername").focus();
    });
</script>