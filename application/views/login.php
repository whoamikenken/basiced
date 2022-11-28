<?php
/**
* @author justin (with e)
* @copyright 2018
*
* re-design ng login..
*/ 
$CI =& get_instance();
$CI->load->model('utils'); 

$key = $CI->utils->getSingleTblData('authentication',array('key'),array('name'=>'gibber'))->result();
$salt = $CI->utils->getSingleTblData('authentication',array('salt'),array('name'=>'gibber'))->result();

?>
<style type="text/css">
    .school-name{
        font-family: avenir;
        text-transform: uppercase;
        font-size: 16px;
        font-weight: 700;
    }

    p {
      font-family: avenir;
    }

    tr{
     font-family: avenir;
        text-transform: uppercase;
        font-size: 16px;
        font-weight: 700;
    }

    p.hr-title{
        font-family: avenir;
        text-transform: uppercase;
        font-size: 33px;
    }
    span.login-header-description{
        font-family: avenir;
        font-size: 16px;
        float: center;
        margin-top: -5px;
    }
    h5.login-desc{
        font-family: avenir;
        font-size: 16px;
    }

    #fp_modal{
        font-family: avenir;
        cursor: pointer;
        font-weight: 600;

    }
/*
    p.orloginwith {
       width: 100%; 
       text-align: center; 
       border-bottom: 1px solid #000; 
       line-height: 0.1em;
       margin: 10px 0 20px; 
    } 

    p.orloginwith span { 
        background: transparent;
        padding:0 10px; 
    }*/

    p.orloginwith {
      display: flex;
      flex-direction: row;
    }
    p.orloginwith:before, p.orloginwith:after{
      content: "";
      flex: 1 1;
      border-bottom: 1px solid #000;
      margin: auto;
    }
    p.orloginwith:before {
      margin-right: 10px
    }
    p.orloginwith:after {
      margin-left: 10px
    }

    input[type=checkbox]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  transform: scale(1.5);
  padding: 10px;
}

/* Might want to wrap a span around your checkbox text */
.checkboxtext
{
  /* Checkbox text */
  font-size: 100%;
  display: inline;
  font-weight: 600;
}
button > img,
button > span {
  vertical-align: middle;
}
   /* #logsubmit{
        background-color: #e9ce41;
        border-color: #e9ce41;
        font-family: avenir;
        color:black;
        font-weight: bold;
        font-size: 14px;
        height: 40px;
    }*/

button.swal2-cancel.btn.btn-danger {
    margin-right: 18px;
}

input{
    font-family: avenir!important;
}
body{
   font-family: avenir!important;
}
</style>
<!DOCTYPE html>
<html>
<head>
    <title>HYPERION</title>
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/login.css">
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/bootstrap.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
    <script src="<?=base_url()?>jsbstrap/jquery-1.10.2.js"></script>
    <script src="<?=base_url()?>js/gate/sha512.js"></script>
    <script src="<?=base_url()?>js/gibberish-aes-1.0.0.min.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/bootstrap-modal.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/bootstrap-modalmanager.js"></script>
    <script src="<?=base_url()?>js/sweetalert.js"></script>
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
            <h4 style="font-weight: bold; text-align: center;font-family: avenir">HR AND PAYROLL SYSTEM</h4>
            <br>
           <!-- <h5 style="color:black ; font-weight: bold; margin-left: 3%;" class="login-desc">Log In</h5> -->
        <form>
            <div class="input-group" style="margin-right: 3%; margin-left: 3%;" >
                <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                <input id="fusername" type="text" class="form-control" name="fusername" placeholder="Username ... ">
            </div><br>
            <div class="input-group" style="margin-right: 3%; margin-left: 3%;">
                <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                <input id="fpassword" type="password" class="form-control" name="fpassword" placeholder="Password ... ">
                <span class="input-group-addon" id="viewPass" style="cursor: pointer;"><i class="glyphicon glyphicon-eye-close"></i></span>
            </div>
            <div class="input-group" id="invalid" style="margin-right: 3%; margin-left: 3%;display: none;">
                <label id="emailExistWork" style="color: red">The username or password you entered is incorrect.</label>
                <label id="attempts" style="color: red"></label>
            </div>
            <br>
            <div style="margin-right: 3%; margin-left: 3%;">
                <button type="button" class="btn btn-primary" id='logsubmit' site='<?=site_url('main/validate')?>' style="width: 100%; height: 45px; font-size: 22px;font-family: avenir"><b>SIGN IN</b></button>
            </div><br>
            <div class="forgot-password" style="margin-right: 3%; margin-left: 3%; margin-top: 0px;">
                <a id="fp_modal" style="text-align: center;"><h5 style=" font-size: 17px;">Forgot password?</h5></a>
            </div>
            <!-- <div style="margin-right: 3%; margin-left: 3%;">
                <input type="checkbox" name=""><span class="checkboxtext">&emsp;Keep me logged in</span>
            </div><br> -->
            <!-- <div style="margin-right: 3%; margin-left: 3%;">
                <p class="orloginwith"><span style="font-weight: 600;">Or log in with</span></p>
            </div><br>
            <div style="margin-right: 3%; margin-left: 3%;">
                <a href="<?=site_url('main/googlelogin')?>" class="btn btn-info " style="width: 100%; margin: 0px; padding: 0px; font-size: 20px;  float: left;"><span><img src="<?=base_url()?>/img/glogos.png" style="width: 10%; height: auto"></span>&emsp;&nbsp;Sign in with Google</a>
            </div> -->
            <!-- <div style="display:flex;">
                <div style="margin-right: 3%; margin-left: 3%;">
                    <a href="<?=site_url('main/googlelogin')?>"><img src="<?=base_url()?>/img/googlepic.png" class="img-rounded" style="width: 70%;"></a>
                </div>
                <div style="margin-right: 3%; margin-left: 3%;">
                    <button type="submit" class="btn btn-success" id='logsubmit' site='<?=site_url('main/validate')?>' style="width: 100%;">SIGN IN <i class="icon-arrow-right"></i></button>
                </div>
            </div> -->
            <br>
            
            <div>&emsp;</div>
        </form>
    </div>
    <br>

    <div class="modal fade" id="forgot-pw" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="media">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                        <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                        <p>D`Great</p>
                    </div>
                    
                </div>
                <center><b><h3 tag="title" class="modal-title">Forgot Password?</h3></b></center>
                
            </div>
            <div class="modal-body" style="margin-bottom: 0px;">
                <div class="row">
                    <div tag='display'>
                        <div class="form-group">
                            <label for="fp_user" style="float: left;margin-left: 7%;">Please enter your email or username:</label>
                            <input type="text" class="form-control" id="fp_user" name="fp_user" style="width: 85%;margin-left: 7%;margin-right: 7%;">
                        </div>
                        <label class="col-md-12" style="text-align: left;color:red;margin-left: 5%;display: none;" id="warn_msg">&nbsp;</label>
                        <div class="col-md-10" id="loading" style="display:none;"><img src='<?=base_url()?>images/loading.gif'/>&nbsp;Verifying..&nbsp;Please&nbsp;wait.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <p id="verify_acc" class="btn btn-primary">Verify Account</p>
            </div>
        </div>

    </div>
</div>
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script src="<?=base_url();?>js/login-un.js" type="text/javascript" charset="utf-8">
    if($("#content").html().length > 0){
        location.reload();
    }
</script>