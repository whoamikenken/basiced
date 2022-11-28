<?php

/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */

?>

<div id="content">
    <div class="widgets_area">
        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading"><h4><b>Email Setup</b></h4></div>
            <div class="panel-body">
                <form id="emailSetup">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputName2">From Email</label>
                                        <input class="form-control" type="text" name="from_email" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputName2">Password</label>
                                        <input class="form-control" type="password" name="Password" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputName2">From Name</label>
                                        <input class="form-control" type="text" name="from_name" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="exampleInputName2">Verify Password</label>
                                        <input class="form-control" type="password" name="verify_password" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-success" id="saveEmail" type="button">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <br><br>
                <div class="col-md-12" id="table">

                </div>                                                                      
            </div>
        </div>
    </div>   
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
    emailSetup();
    setTimeout(function(){ $('.panel').removeClass("animated fadeIn delay-1s");}, 3000);
});


function emailSetup(){
    
    $.ajax({
        url:  $("#site_url").val() + "/setup_/loadEmailSetup",
        type: "POST",
        success:function(response){
            $("#table").html(response);
        }
    });
}

$("#saveEmail").click(function () {
        var formdata = {
            from_email: GibberishAES.enc($("input[name='from_email']").val(), toks),
            Password: GibberishAES.enc($("input[name='Password']").val(), toks),
            from_name: GibberishAES.enc($("input[name='from_name']").val(), toks),
            verify_password: GibberishAES.enc($("input[name='verify_password']").val(), toks),
            toks: toks
        };
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/saveEmail')?>",
            data: formdata,
            success: function (response) {
                if (response == "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Email settings have been saved.',
                        showConfirmButton: true,
                        timer: 1500
                    })
                    setTimeout(function() {
                        emailSetup();
                    }, 1500);
                }else if(response == "pass"){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Password Mis-Match.',
                        showConfirmButton: true,
                        timer: 1500
                    })
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error!',
                        text: 'Error! Please report.',
                        showConfirmButton: true,
                        timer: 1500
                  })
                }
            }
        });
    });

</script>