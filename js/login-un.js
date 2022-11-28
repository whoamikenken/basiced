/*!
 * Gawa ko lang to, ako si Aaron Ruanto.
 */
var toks = hex_sha512(" "); 

$('#logsubmit').click(function(){
    login();
});

$("#viewPass").click(function(){
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


$(document).keypress(function(e) {
    var key = e.which;
    if (key == 13) 
    {
      login();
    }
  });

$("#fusername").on("blur", function(){
    if ($("#invalid").is(":hidden") == false) {
        $("#invalid").hide();
    }
});

$("#makeappointment").click(function(){
    var form_data = "";
    $.ajax({
        url: $("#makeappointment").attr("site"),
        type: "POST",
        data: form_data,
        success: function(msg){
           $("#content").html(msg);
        }
    });
    return false; 
});    

$("#fp_modal").on("click", function(){
    $("#forgot-pw").modal('toggle');
});

$("#verify_acc").on("click", function() {
    var fp_user = $("input[name='fp_user']").val();
    if (!fp_user) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Please fill-up required fields.",
            showConfirmButton: true,
            timer: 2000
        });
        return;
    } else {
        $("#loading").show();
        $.ajax({
            url: $("#site_url").val() + "/main/isAccountExisting",
            type: "POST",
            data: { username: GibberishAES.enc(fp_user, toks), toks:toks },
            dataType: "json",
            success: function(response) {
                if (response.status == 1){
                    Swal.fire({
                        icon: 'success',
                        title: 'Reset password email sent!',
                        text: "An email has been sent to "+fp_user+". Follow the directions in the email to reset your password",
                        showConfirmButton: true
                    }).then(function() {
                           location.reload();
                        });
                    
                }else{
                    // $("#warn_msg").show();
                    // $("#warn_msg").text(response.msg);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: response.msg,
                        showConfirmButton: true,
                        timer: 2500
                    })
                }
                $("#loading").hide();
            },
            error: function (xhr, status, exception) {
                $("#loading").hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Something went wrong, please coordinate to System Administrator.',
                    showConfirmButton: true,
                    timer: 2500
                }).then(function() {
                           location.reload();
                        });
            }
        });
    }
});


function  login(){

  var uname = $('#fusername').val();
        var upass = $('#fpassword').val();
        if(!uname){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please enter your username.",
                showConfirmButton: true,
                timer: 2000
            });
            $('#fusername').focus();
            return false;
        }else if(!upass){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Password is required.",
                showConfirmButton: true,
                timer: 2000
            });
            $('#fpassword').focus();
            return false;
        }
        
        var form_data = {
            toks: toks,
            fusername: GibberishAES.enc(uname, toks),
            fpassword: GibberishAES.enc(upass, toks),
            verify: "1"
        }

        $("#logsubmit").prop("disabled", true);
        $.ajax({
            url: $("#logsubmit").attr("site"),
            type: "POST",
            data: form_data,
            success: function(msg){
                  if($(msg).find("result").text() == 0){
                    var attempts = $(msg).find("locked").text();
                    attempts = 5 - Number(attempts);
                    $("#attempts").text(attempts+" remaining attempts.");
                    $("#invalid").show();
                    $('#fpassword').focus();
                    $('#fpassword').select();
                  }else if($(msg).find("result").text() == "LOCKED"){
                    $("#emailExistWork").text("Your account has been locked.");
                    $("#invalid").show();
                    const swalWithBootstrapButtons = Swal.mixin({
                      customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                      },
                      buttonsStyling: false
                    })

                    swalWithBootstrapButtons.fire({
                      title: 'Locked Account',
                      text: "Do you want to unlock your account?",
                      icon: 'warning',
                      showCancelButton: true,
                      confirmButtonText: 'Yes!',
                      cancelButtonText: 'No!',
                      reverseButtons: true
                    }).then((result) => {
                      if (result.value) {
                        var uid = $(this).attr("userid");
                         $.ajax({
                             url: $("#site_url").val() + "/main/unlockResendAccount",
                             data: {username: $('#fusername').val()},
                             type: "POST",
                             success: function(msg){
                              Swal.fire({
                                    icon: 'success',
                                    title: 'Email Sent!',
                                    text: 'Unlock link has been sent',
                                    showConfirmButton: true,
                                    timer: 2500
                              })
                              setTimeout(function() {
                                window.location.href = window.location; 
                              }, 1500);
                             }
                         });
                      } else if (
                        result.dismiss === Swal.DismissReason.cancel
                      ) {
                        setTimeout(function() {
                            window.location.href = window.location; 
                          }, 1500);
                        swalWithBootstrapButtons.fire(
                          'Cancelled',
                          'Account is still locked',
                          'error'
                        )
                      }
                    })
                  }else if($(msg).find("result").text() == "EMAILED"){
                    $("#emailExistWork").text("Your account has been locked.");
                    $("#invalid").show();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: "Your account has been lock an email have been sent to unlock it.",
                        showConfirmButton: true,
                        timer: 2500
                    });
                    setTimeout(function() {
                      window.location.href = window.location;  
                    }, 3000);
                  }else{
                    sessionStorage.clear();
                    localStorage.clear();
                    sessionStorage.setItem('userLoggedin','PovedaPinnacle'); 
                    localStorage.setItem('userLoggedin', 'YES');
                    // window.location.href = window.location;
                    location.reload();
                  } 
                  $("#logsubmit").prop("disabled", false);

            }
        });
        return false;
}