<?php

$usertype = $this->session->userdata("usertype");

if($this->session->userdata('username') && $usertype != "ADMIN" && $usertype != "EMPLOYEE"){
    $this->load->view('includes/header'); 

    $displayname = "Applicant!";

    if ($this->session->userdata("fullname")) {
        $displayname = ucwords(strtolower($this->session->userdata("fullname")));
    }else $displayname = $lname.", ".$fname." ".$mname;
?>
<style>
@import "https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700";
body {
    font-family: 'Poppins', sans-serif;
    background: #fafafa;
}

p {
    font-family: 'Poppins', sans-serif;
    font-size: 1.1em;
    font-weight: 300;
    line-height: 1.7em;
    color: black;
}

.valid{
    border: 1px solid #ccc !important;
}

.valid.error{
    display: none !important;
}

a,
a:hover,
a:focus {
    color: inherit;
    text-decoration: none;
    transition: all 0.3s;
}

.navbar {
    padding: 15px 10px;
    background: #fff;
    border: none;
    border-radius: 0;
    margin-bottom: 40px;
    box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
}

.navbar-btn {
    box-shadow: none;
    outline: none !important;
    border: none;
}

.line {
    width: 100%;
    height: 1px;
    border-bottom: 1px dashed #ddd;
    margin: 40px 0;
}

ul li a:hover {
    color: #1f1f1f!important;
    background: #fff!important;
}

ul ul a {
    padding-left: 0px !important;
}

/* ---------------------------------------------------
    SIDEBAR STYLE
----------------------------------------------------- */

#sidebar {
    width: 250px;
    position: fixed;
    margin-top: 26px;
    left: 0;
    height: 100vh;
    z-index: 0;
    background: #1f1f1f;
    color: #fff;
    transition: all 0.3s;
}

a#current {
    background: #000000;
    color: #fff;
}

#sidebar.active {
    margin-left: -292px;
}

#sidebar .sidebar-header {
    padding: 20px;
    background: #1f1f1f;
}

#sidebar ul.components {
    padding: 20px 0;
    border-bottom: 1px solid #fff4a8;
}

#sidebar ul p {
    color: #fff;
    padding: 10px;
}

#sidebar ul li a {
    padding: 10px;
    font-size: 1.1em;
    display: block;
}

#sidebar ul li a:hover {
    color: #1f1f1f;
    background: #fff;
}


a[data-toggle="collapse"] {
    position: relative;
}

.dropdown-toggle::after {
    display: block;
    position: absolute;
    top: 50%;
    right: 20px;
    transform: translateY(-50%);
}

ul li a.dropdownHigh:hover {
    color: #313131!important;
    background: #0072c6!important;
}
ul ul a.dropdownHigh {
    font-size: 0.9em !important;
    padding-left: 17px !important;
    padding-top: 7px !important;
    padding-bottom: 8px !important;
    background: #313131;
    color: #fff!important;
}
ul ul a {
    font-size: 0.9em !important;
    padding-left: 17px !important;
    background: #1f1f1f;
}

a.dropdownHigh:hover span.dropLogo:not(:hover) {
    color: #000!important;
}
span.dropLogo{
    color: #000!important;
    display: inline-block;
    background-color: white;
    border-radius: 60px;
    box-shadow: 0px 0px 2px #0072c6!important;
    padding: 0.5em 0.6em;
}

.cpass{
    /*border-bottom: 1px solid #c7bdbd;*/
    height: 1.5px;
    background-color: #313131;
    background-image: linear-gradient(to right, #313131, #0072c6,#0072c6, #313131);
}


ul ul a {
    font-size: 0.9em !important;
    padding-left: 17px !important;
    background: #1f1f1f;
}

ul.CTAs {
    padding: 20px;
}

ul.CTAs a {
    text-align: center;
    font-size: 0.9em !important;
    display: block;
    border-radius: 5px;
    margin-bottom: 5px;
}

a.download {
    background: #fff;
    color: #7386D5;
}

a.article,
a.article:hover {
    background: #6d7fcc !important;
    color: #fff !important;
}

/* ---------------------------------------------------
    CONTENT STYLE
----------------------------------------------------- */

#content {
    margin: 0;
    left: 252px;
    min-height: 100vh;
    transition: all 0.3s;
    position: absolute;
    top: 46px;
    right: 0;
}

#content.active {
    width: 100% !important;
    padding: 0px;
    min-height: 100vh;
    transition: all 0.3s;
    position: absolute;
    left: 0px;
}

#content.active > table > tbody > tr > td{
    padding-left: 7%;
    padding-top: 80px;
}
/* ---------------------------------------------------
    MEDIAQUERIES
----------------------------------------------------- */

@media (max-width: 8px) {
    #sidebar {
        margin-left: -250px;
    }
    #sidebar.active {
        margin-left: 0;
    }
    #content {
        width: 100%;
    }
    #content.active {
        width: calc(100% - 450px);
    }
    #sidebarCollapse span {
        display: none;
    }
}

.mCSB_inside>.mCSB_container {
     margin-right: 0px !important;
}

.swal2-cancel{
   margin-right: 20px;
 }

*{
    font-family: Avenir ;
}



</style>

<div class="panel">
    <div class="panel-heading">
        <header class="dark_grey"> <!-- Header start -->
            <a  class="logo_imageschool modules"><span class="hidden-480">&nbsp;&nbsp; PTI  <!--<span style="font-weight: bold;">Main Menu</span>--></span></a>
            <ul class="header_actions pull-left">
                <li data-toggle="tooltip" data-placement="bottom" title="Hide/Show main navigation" ><a href="#" id="sidebarCollapse"><i class="glyphicon glyphicon-chevron-left"></i></a></li>
            </ul>
            <ul class="header_actions pull-left dark_grey hidden-768 hidden-480" style="padding-inline-start: 0px;">
                <li><a href="#"> <span style="font-weight: bold;">Welcome, <?= $displayname ?></span></a></li>
            </ul>
            <ul class="header_actions">
                <div class="dropdown">
                    <a class="btn dropdown-toggle-user drop_logo" type="button" id="dropdownMenu1" style="width: 240px !important" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        HYPERION APPLICANT PORTAL
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" id="hovered" style="margin-left: 30px;width: 240px; background-color: #313131 !important ">
                        <li><a class="dropdownHigh" href="<?=site_url('applicant')?>" name='signout' site="<?=site_url('applicant')?>"><span class="glyphicon glyphicon-log-out dropLogo"></span>&emsp;<span><b>Sign Out</b></span></a></li>
                    </ul>
                </div>
            </ul>
            <script src="<?=base_url()?>jsbstrap/library/jquery.easytabs.js"></script>
            <script src="<?=base_url()?>jsbstrap/library/jquery.inputmask.bundle.js"></script>
            <script src="<?=base_url()?>js/sweetalert.js"></script>
        </header>
    </div>
</div>
<!-- <header class="dark_grey"> 
    <a href="#" class="logo_image"><span class="hidden-480">PTI</span></a>
    <ul class="header_actions pull-left hidden-480 hidden-768">
        <li rel="tooltip" data-placement="bottom" title="Hide/Show main navigation" ><a href="#" class="hide_navigation"><i class="glyphicon glyphicon-chevron-left"></i></a></li>
    </ul>
    <ul class="header_actions pull-left">
        <li><a href="#"> <span style="color : #089CD2;font-weight: bold;">Welcome, <?= $displayname ?></span></a></li>
    </ul>
    <ul class="header_actions">
        <li><a href="<?=site_url('applicant')?>" name='signout' site="<?=site_url('applicant')?>"><i class="glyphicon glyphicon-log-out"></i> <span class="hidden-768 hidden-480">Logout</span></a></li>
        <li class="responsive_menu"><a class="iconic" href="#"><i class="icon-reorder"></i></a></li>
    </ul>
    <ul class="header_actions">
        <li><a href="#"> <span style="color : #089CD2;font-weight: bold;"><?=strtoupper('APPLICANT')?> PORTAL</span></a></li>
    </ul>
    
<script src="<?=base_url()?>jsbstrap/library/jquery.easytabs.js"></script>
<script src="<?=base_url()?>jsbstrap/library/jquery.inputmask.bundle.js"></script>
<script src="<?=base_url()?>js/sweetalert.js"></script>


</header> -->
<div class="modal fade" id="continueSession" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body" style="margin-bottom: 0px !important; padding: 15px !important;">
               <center><p style="display: inline-flex;">Do you want to continue your transaction?&nbsp;<button class="btn btn-primary" id="continue_session">Yes</button>&nbsp;<a href="<?=site_url('applicant')?>" site="<?=site_url('applicant')?>"><span class="btn btn-danger" >No</span></a></p></center>
            </div>
        </div>
    </div>
</div>


<style>
    .inner_content{
        padding-top: 45px;
    }
</style>

<?
}

?>

<?php
$userType = $this->session->userdata('usertype');

$CI =& get_instance();
$CI->load->model('applicantt');


if(isset($lname) && isset($fname) && isset($fname) && isset($email)) {
    $applicantId = $CI->applicantt->getApplicantId($lname, $fname, $mname, $email);
}else{
    $res = $CI->applicantt->getApplicantPersonalInfo($applicantId);
    if($res->num_rows() > 0){
        $lname = $res->row(0)->lname;
        $fname = $res->row(0)->fname;
        $mname = $res->row(0)->mname;
        $email = $res->row(0)->email;
        $positionid = $CI->applicantt->getJobClass($res->row(0)->positionApplied);
        $redtag = $res->row(0)->redtag;
        $redTagRemarks = $res->row(0)->redTagRemarks;
    }
}

$isprocessed = $CI->applicantt->isAlreadyOnProcess($applicantId);
if($this->session->userdata("usertype") == "ADMIN") $isprocessed = false;
$data = array('applicantId'=>$applicantId,'fname'=>$fname,'lname'=>$lname,'mname'=>$mname,'cur_email'=>$email,'positionid'=>$positionid, 'redtag' => (isset($redtag) ? $redtag : ''), 'remarks' => (isset($redTagRemarks) ? $redTagRemarks : ''));
?>
<style>
.tab-content>.active {
    padding-top: 3%;
}

.this {
    padding: 0px 20px 0px 20px;
    
    white-space: nowrap;
    box-shadow: 5px -2px 0px 0px rgba(0,0,0,0.1);
    border: 10px solid #ddd;
    border-bottom-color: transparent;
}

.this:hover {
    cursor: pointer;
}
.thisbehind {
    background-color: #c5c5c5;
    border: 3px solid #D8BE96;
    font-weight: bolder;        
}

.thisbehind:hover {
    background: #0072c6!important;
}

li>.active {
    background-color: #FCF6CD;
    z-index: 1;
    border: 2px solid #FCF6CD;
    padding: 10px 10px 10px 10px;
}
.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
    color: black;
    cursor: default;
    z-index: 90;
    padding: 13px 13px 13px 13px ;
}
.nav>li>a {
    position: relative;
    display: block;
    padding: 13px 13px 13px 13px ;
}
.nav-tabs>li>a {
    margin-right: 2px;
    line-height: 1.42857143;
    border: 1px solid transparent;
    padding: 13px 13px 13px 13px ;
}
</style>
<script src="<?=base_url()?>jsbstrap/library/jquery.inputmask.bundle.js"></script>
<input type="hidden" id="isprocessed" value="<?= $isprocessed ?>">
<input type="hidden" id="tableControlChecker">
<input type="hidden" id="tableControlCheckerTab">
<div class="inner_content">
    <div class="well blue" style="margin: 20px; <?= ($userType == "ADMIN" || $userType == "EMPLOYEE" ? "" : "margin-top: -2.5%;"); ?> ">
        <div class="well-content no_padding" style="border: 0 !important;width: 87%;margin: auto;">
            <div class="navbar-header" style="width: 100%;border-bottom: 0px solid black;">
                <ul class="nav nav-tabs" id="pinfotab">
                  <li class="active"><a href="#tab1" id="personalTab" class="this thisbehind" data-toggle="tab" style="color: #000000;">STEP 1 : PERSONAL DATA</a></li>
                  <li><a href="#tab2" class="this thisbehind" id="educTab" data-toggle="tab" style="color: #000000;">STEP 2 : EDUCATIONAL & PROFESSIONAL BACKGROUND</a></li>
                  <?if($userType == "ADMIN"):?>
                     <!-- <li><a href="#tab3" class="this thisbehind" data-toggle="tab" style="color: #000000;">STEP 3 : FORMATION AND DEVELOPMENT</a></li> -->
                  <?endif;?>
                  <?if(!$userType):?>
                    <li><a href="#tab4" class="this thisbehind" data-toggle="tab" style="color: #000000;">STEP 3 : UPLOAD INITIAL REQUIREMENT</a></li>
                  <?endif;?>
                  <?if($userType!='' && $userType!='EMPLOYEE'):?>
                    <li><a href="#tab5" class="this thisbehind" data-toggle="tab">APPLICANT STATUS</a></li>
                  <?endif;?>
                  <?if($userType!='' && $userType=='EMPLOYEE' && $job != 'viewing'):?>
                    <li><a href="#tab6" class="this thisbehind" data-toggle="tab">APPLICANT STATUS</a></li>
                  <?endif;?>
                  <?if($userType!='' && $userType=='EMPLOYEE' && $job == 'viewing'):?>
                    <li><a href="#tab7" class="this thisbehind" data-toggle="tab">APPLICANT STATUS</a></li>
                  <?endif;?>

                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane" id="tab1" ld='applicant/info_personal' <?=($userType!='' && ($userType=='EMPLOYEE' || $userType=='ADMIN') ? "style='pointer-events:none'" : '')?>>
                    <?$this->load->view('applicant/info_personal',$data);?>
                </div>
                <div class="tab-pane" id="tab2" ld='applicant/info_education'  <?=($userType!='' && ($userType=='EMPLOYEE' || $userType=='ADMIN') ? "style='pointer-events:none'" : '')?>>
                    <?$this->load->view('applicant/info_education',$data);?>
                </div>
                <?if($userType == "ADMIN"):?>
                    <div class="tab-pane" id="tab3" ld='applicant/info_seminar'>
                        <?$this->load->view('applicant/info_seminar',$data);?>
                    </div>
                <?endif;?>
                <?if(!$userType):?>
                    <div class="tab-pane" id="tab4" ld='applicant/info_requirements'>
                        <?$this->load->view('applicant/info_requirements',$data);?>
                    </div>
                <?endif;?>

                <?if($userType!='' && $userType!='EMPLOYEE'):?>
                  <div class="tab-pane" id="tab5" ld='applicant/info_applicant_status'>
                      <?$this->load->view('applicant/info_applicant_status',$data);?>
                  </div>
                <?endif;?>
                <?if($userType!='' && $userType=='EMPLOYEE' && $job != 'viewing'):?>
                  <div class="tab-pane" id="tab6" ld='applicant/info_applicant_status_approver'>
                      <?$this->load->view('applicant/info_applicant_status_approver',$data);?>
                  </div>
                <?endif;?>
                <?if($userType!='' && $userType=='EMPLOYEE' && $job == 'viewing'):?>
                  <div class="tab-pane" id="tab7" ld='applicant/info_applicant_status_approver_viewing'>
                      <?$this->load->view('applicant/info_applicant_status_approver_viewing',$data);?>
                  </div>
                <?endif;?>

            </div>
        </div>
    </div>

<input type="hidden" id="user_type" value="<?= $userType ?>">
<?
$this->load->view("includes/modalview");
?>

<script>
 var toks = hex_sha512(" ");
var cancontinue = true;
$(document).ready(function(){
    $('#pinfotab li:first').addClass('active');
    $('.tab-content div:first').addClass('active');
});

function refreshtab(tabn){
    var form_data = { 
      view : GibberishAES.enc($(tabn).attr("ld") , toks),
      toks:toks
    }
    
    $.ajax({
            url: "<?=site_url("main/siteportion")?>",
            data: form_data,
            type:"POST",
            success: function(msg){
                $(tabn).html(msg);
            }
        });
}

function updateCheckBox(value, column){
    $.ajax({
        url: "<?=site_url('applicant/updateCheckBoxApplicant')?>",
        type: "POST",
        data: {
            value :  GibberishAES.enc( value , toks),
            column:  GibberishAES.enc( column, toks),
            applicantId : GibberishAES.enc($("input[name='applicantId']").val(), toks),
            toks:toks
        },
        success: function(msg){

        }
    });
}

$("#pinfotab li").click(function(){
  // var obj = $(this).find("a").attr("href");  
  //   refreshtab(obj);
  // return cancontinue;
});

    // $("#continue_session").click(function(){
    //     addRemainingSession();
    //      $("#continueSession").modal("toggle");
    // });

    // function addRemainingSession(){
    //     $.ajax({
    //         url: "<?=site_url("main/addRemainingSessions")?>",
    //         type: "POST",
    //         success: function(msg){ 
               
    //         }
    //     });
    // }

    // if("<?=$applicantId?>"){
    //     var checksession = setInterval(function(){ 
    //         $.ajax({
    //             url: "<?=site_url("main/checkRemainingSession")?>",
    //             type: "POST",
    //             success: function(msg){ 
    //                 if(msg >= 5){
    //                     $("#continueSession").modal("toggle");
    //                     clearInterval(checksession);
    //                 }
    //             }
    //         });
    //     }, 10000);
    // }

$("#dropdownMenu1").mouseenter(function(){
        $("#hovered").css("display", "block");
    })
    $("#hovered,.header_actions").mouseleave(function(){
        $("#hovered").css("display", "none");
    }).mouseleave();

$("a[name='backlist']").click(function(){
    location.reload();
});

var applicantSession = 0;

$(document).on('click','li,a,button,select,input',function(){
    applicantSession = 0;
});

var checksession = setInterval(function(){ 
    applicantSession = applicantSession+1;
    if(applicantSession == 10){
        $("#continueSession").modal("toggle");
    }
}, 60000);

$("#continue_session").click(function(){
        $("#continueSession").modal("toggle");
        applicantSession = 0;    
});
</script>