<style type="text/css">
	.form-row{
		padding-bottom: 15px;
	}
	
	.default{
		width:160px !important;
	}
</style>
<div class="col-md-12">
	<div class="col-md-12">
		<form id="inhouse_seminar_gate">
			<input type="hidden" id="table_ID" name="id" value="">
			<div class="form-row col-md-12" id="usernameDiv">
				<div class="field-label col-md-3">
					<label>Username</label>
				</div>
				<div class="field col-md-9">
					<input type="input" class="form form-control" name="check_username" id="check_password" value="">
				</div>
			</div>
			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Password</label>
				</div>
				<div class="field col-md-9">
					<input type="password" class="form form-control" name="gateusername" id="check_password" value="">
				</div>
			</div>
		</form>
	</div>
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">

<script type="text/javascript">
    $(document).ready(function(){ 
        $("input[name='dvid']").remove(); 

        $('<input>').attr({ 
            type: 'hidden',
            id: 'dvid',
            name: 'dvid', 
            value: getDeviceId()  
        }).appendTo('#inhouse_seminar_gate');     

        getLocalIP().then((privateip) => {  
            privateip = getIpAddressPHP();
            $("input[name='privateip']").remove(); 
            $('<input>').attr({ 
                type: 'hidden',
                id: 'privateip',
                name: 'privateip', 
                value: privateip 
            }).appendTo('#inhouse_seminar_gate');     
        }); 

        $('#check_username').keyup(function(){  
            $(this).parent().find('#id-error').remove();
        });

          
    });

    function openFullscreen() {
      if (elem.requestFullscreen) {
        elem.requestFullscreen();
      } else if (elem.mozRequestFullScreen) { /* Firefox */
        elem.mozRequestFullScreen();
      } else if (elem.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
        elem.webkitRequestFullscreen();
      } else if (elem.msRequestFullscreen) { /* IE/Edge */
        elem.msRequestFullscreen();
      }
    }

    $(".loginSeminar").unbind().click(function(e){ 
            var isallowed = false; 
            switch(true)
            {
                case $('#check_username').val() == "" || $('#check_password').val() == "":  
                    Swal.fire({
                      icon: 'error',
                      title: 'Error!',
                      text: 'Username and Password is required.',
                      showConfirmButton: true,
                      timer: 1500
                    })
                    $('#check_username').focus();
                break;
                default:
                    isallowed = true;
                break;
            }

            switch(true) 
            { 
                case isallowed:   

                    $("input[name='p'], input[name='toks']").remove();  

                    var toks = hex_sha512(" "); 

                    $('<input>').attr({ 
                        type: 'hidden',
                        id: 'p',
                        name: 'p', 
                        value: GibberishAES.enc($("input[name='check_password']").val(), toks) 
                    }).appendTo('#form-check-login');   

                    $('<input>').attr({ 
                        type: 'hidden',
                        id: 'toks',
                        name: 'toks', 
                        value: toks 
                    }).appendTo('#form-check-login');    

                    $.ajax({
                        url: $("#site_url").val() + "/fingerprint_/verify_gate",
                        type: "post",
                        data: {
                            "check_username": $('#check_username').val(),
                            "check_password": $('#check_password').val(),
                            "p": $("input[name='p']").val(),
                            "toks": $("input[name='toks']").val(), 
                            "privateip": $("input[name='privateip']").val(),
                            "dvid": $("input[name='dvid']").val() 
                        },
                        // dataType: "json",
                        success: function(response)
                        {  
                            switch(response)
                            {
                                case '1':
                                    window.location.href = "<?php echo site_url('InhouseSeminarGate'); ?>";
                                break;
                                case '2':
                                    Swal.fire({
                                      icon: 'warning',
                                      title: 'Warning!',
                                      text: 'Account is currently logged in to another device.',
                                      showConfirmButton: true,
                                      timer: 1500
                                    })
                                break;
                                default:
                                    Swal.fire({
                                      icon: 'error',
                                      title: 'Error!',
                                      text: 'Oh snap! Change a few things up and try submitting again.',
                                      showConfirmButton: true,
                                      timer: 1500
                                    })
                                break;
                            }
                        } 
                    });
                break;
                default:   
                    Swal.fire({
                      	icon: 'warning',
                      	title: 'Warning!',
                      	text: '<b>Oh snap!</b> Change a few things up and try submitting again.',
                      	showConfirmButton: true,
                      	timer: 1500
                    })
                break;
            } 

            e.preventDefault();
            return false; 
        });



</script>