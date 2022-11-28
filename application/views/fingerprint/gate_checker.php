<?php  
/**
 * @author Ken
 * @copyright bente-bente
 */          
?> 
<form id="form-check-login" enctype="multipart/form-data" >
    <div class="form-group">
        <label for="exampleInputEmail1">Username</label>
        <input type="text" class="form-control" name="check_username" id="check_username" value="">
    </div>
    <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
        <input type="password" class="form-control" name="check_password" id="check_password" value="">
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function(){ 
        $("input[name='dvid']").remove(); 

        $('<input>').attr({ 
            type: 'hidden',
            id: 'dvid',
            name: 'dvid', 
            value: getDeviceId()  
        }).appendTo('#form-check-login');     

        getLocalIP().then((privateip) => {  
            $("input[name='privateip']").remove(); 
            $('<input>').attr({ 
                type: 'hidden',
                id: 'privateip',
                name: 'privateip', 
                value: privateip 
            }).appendTo('#form-check-login');     
        }); 

        $('#check_username').keyup(function(){  
            $(this).parent().find('#id-error').remove();
        });

        $(".btnsubmit").click(function(e){ 
            var isallowed = false; 
            switch(true)
            {
                case $('#check_username').val() == "" && $('#check_password').val() == "":  
                    Swal.fire({
                      icon: 'error',
                      title: 'Error!',
                      text: 'Oh snap! Change a few things up and try submitting again.',
                      showConfirmButton: true,
                      timer: 1500
                    })
                    $('#check_username').focus();
                break;
                case $('#check_username').val() == "":  
                    Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: 'The username or password you entered is incorrect.',
                      showConfirmButton: true,
                      timer: 1500
                    })
                    $('#check_username').focus();
                break;
                case $('#check_password').val() == "":  
                    Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: 'The username or password you entered is incorrect.',
                      showConfirmButton: true,
                      timer: 1500
                    })
                    $('#check_password').focus();
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
                                    // window.location.href = "<?php echo site_url('gate'); ?>";
                                    location.reload();
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
                    $('#container-error').html('<span class="text-error" ><b>Oh snap!</b> Change a few things up and try submitting again.</span>');
                break;
            } 

            e.preventDefault();
            return false; 
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

</script>
