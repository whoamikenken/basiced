<?php
$data['title'] = $title;
// $data['autoload'] = $autoload;
$this->load->view('includes/header',$data);

?>


    <div class="login-container">
        <div class="login-header bordered">
            <h4>Sign in</h4>
        </div>
        <form>
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
                <label class="checkbox pull-left">
                    <input type="checkbox" class="uniform" name="checkbox1"> Remember me
                </label>
                <button type="submit" class="pull-right btn btn-lg blue" id='logsubmit' site='<?=site_url('gate1/login')?>'>SIGN IN <i class="icon-arrow-right"></i></button>
            </div>
            <div class="forgot-password">
                <a href="#forgot-pw" role="button" data-toggle="modal">Forgot password?</a>
            </div>
        </form>
    </div>

    <div id="forgot-pw" class="modal hide fade" tabindex="-1" data-width="760">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove-sign"></i></button>
            <h3>Forgot your password?</h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form_row">
                        <label class="field_name">Email address</label>
                        <div class="field">
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="text" class="col-md-12" name="email" placeholder="example@domain.com">
                                </div>
                                <div class="col-md-4">
                                    <a href="#" class="btn btn-block blue">Reset password</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?
$data['upload_file'] = '';
$this->load->view('includes/footer',$data);
?>