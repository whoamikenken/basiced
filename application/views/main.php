<?php

/**
 * @author Justin
 * @copyright 2016
 */

if(!$this->session->userdata("logged_in")){
  redirect('main');
}

$CI =& get_instance();
$CI->load->model('disciplinary_action'); 
$today = date("Y-m-d");
$months = date("m");
$utype = $this->session->userdata('usertype');
$employeeid = $this->session->userdata('username');

if($utype != "EMPLOYEE"){   // ADMIN
?>
<style>

button.list-group-item.titlemenu{
  background-color: #0072c6 !!important;
  color: white !important;
}
button.list-group-item:hover{
    color: #1f1f1f;
    background: #fff;

}
button.list-group-item{
  background: #1f1f1f;
  color: white;
  border-radius: 9px;
  margin-bottom: 2px;
}
.list-group-item.titlemenu{
    border-top-left-radius: 27px !important;
    border-top-right-radius: 27px !important;
    border-radius: 0px;
}

.menu.animated.pulse.delay-1s.col-md-8.menuactive > div > div > button {
    font-size: 19px;
    text-align: center;
    margin: auto;
    margin: 3px;
}

.notifdiv{
  position: absolute !important;
}

#content{
  width: calc(100% - 267px) !important;
}


</style>
<div id="content"> 
  <br>
    <div class="row animated fadeInUp">
        <div id="mdoffset" class="col-md-1"></div>
        <?
        $total = "";
        $user = $this->session->userdata("userid");
        $utype = $this->session->userdata('usertype');

        // foreach($this->menus->loadmenus() as $mmenus){
        if($utype != "EMPLOYEE"){
        foreach($this->menus->loadmenus("",$this->session->userdata("userid"),$utype) as $mmenus){
      
            list($menuid,$root,$link,$title,$status,$arranged,$icon,$comments) = $mmenus;
        ?>
          <div parenttitle="<?=$menuid?>" class="col-md-2 menu animated pulse delay-1s">
          <table class="menutab">
            <div class="list-group">
                   <button class="list-group-item titlemenu" titleid='<?=$menuid?>' style="background-color: #0072c6;color: black !important;"><center><h4><b><i class="<?=$icon?>"></i>&nbsp;&nbsp;&nbsp;&nbsp;<?=$title?></b></h4></center></button>
        <?

            foreach($this->menus->loadmenus($menuid,$this->session->userdata("userid"),$utype) as $submenus){
                list($menuid,$root,$link,$title,$status,$arranged,$icon,$comments,$description) = $submenus;

                if($menuid == "44"){
                    $acc = $this->extras->showAccessmsg($user);
                    if($acc == true) $total = $this->user->showMsg($user);
                }
                else $total = "";
                    
          // LACKING OF IN/OUT NOTI
          // $lackInOutCount = 0;
          // $lackInOutCount = $this->attendance->lackingInOutNotif();
          
          // $excessiveCount ="";
                if($menuid == 150 || $menuid == 151 || $menuid == 152) $link = "includes/ams_bundy_clock";
            ?>
              
              <?
              if($menuid == "100")
              {
                  $count = 0;
                  $employeestatus = $this->employeemod->employeestatusupdatenotif()->result();
                  foreach($employeestatus as $row)
                  {
                      $count += $this->employeemod->employeestatusupdatenotifcontent($row->code, $row->duration)->num_rows();
                      if($count > 0) break;
                  }

                  if($count != 0){
                      ?>  
                        <div class="cul6">
                          <button class="list-group-item modules animated infinite pulse delay-3s" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                          <div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="color:red;font-weight: bold;"></i></div></button>
                        </div>
                      <?
                  }
                  else{
                    ?>  
                        <div class="cul6">
                          <button class="list-group-item modules" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                        </div>
                      <?
                    }
              }
              else if($menuid == "95")
              {
                  // EMPLOYEE DEFICIENCY NOTI
                  $empDefcount = 0;
                  $empDefcount = $this->employeemod->employeedeficiencynotif('','','','',true)->num_rows();

                  if($empDefcount != 0){
                      ?>
                        <div class="cul6">
                          <button class="list-group-item modules animated infinite pulse delay-3s" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                          <div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="color:red;font-weight: bold;"></i></div></button>
                        </div>
                      <?
                  }
                  else{
                    ?>  
                        <div class="cul6">
                          <button class="list-group-item modules" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                        </div>
                      <?
                    }
              }
              else if($menuid == "183")
              {
                  $CI->load->model('seminar');
                  $where_clause = " AND isread = '0' ";
                  $attendees_notif = $CI->seminar->seminarAttendeesList($where_clause);
                  $attendees_notif = count($attendees_notif);
                  if($attendees_notif > 0){
                      ?>
                        <div class="cul6">
                          <button class="list-group-item modules animated infinite pulse delay-3s" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title?></b><div class='notifdiv bell'><i class='glyphicon glyphicon-bell large' style="color:red"></i></div></button>
                        </div>
                      <?
                  }
                  else{
                    ?>  
                        <div class="cul6">
                          <button class="list-group-item modules" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                        </div>
                      <?
                    }
              }
              else if($menuid == "184")
              {
                  $CI->load->model('expiration');
                  $prcExpiration_notif = $CI->expiration->prcExpiryData()->num_rows();
                  if($prcExpiration_notif > 0){
                      ?>
                        <div class="cul6">
                          <button class="list-group-item modules animated infinite pulse delay-3s" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title?></b><div class='notifdiv bell'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b><?= $prcExpiration_notif ?></b></span></div></button>
                        </div>
                      <?
                  }
                  else{
                    ?>  
                        <div class="cul6">
                          <button class="list-group-item modules" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                        </div>
                      <?
                    }
              }else if($menuid == "203")
              {
                  $CI->load->model('retirement');
                    $retirementCount = $CI->retirement->employeeRetiree('','','','1')->num_rows();
                  if($retirementCount > 0){
                      ?>
                        <div class="cul6">
                          <button class="list-group-item modules animated infinite pulse delay-3s" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title?></b><div class='notifdiv bell'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b><?= $retirementCount ?></b></span></div></button>
                        </div>
                      <?
                  }
                  else{
                    ?>  
                        <div class="cul6">
                          <button class="list-group-item modules" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                        </div>
                      <?
                    }
              }
              else if($menuid == "101")
              {
                  // DISCIPLINARY ACTION
                  $excessiveTardinessCount = $excessiveAbsenteismCount = false;
                  $excessiveTardinessCount = $CI->disciplinary_action->empWithExcessiveTardiness(true,'',false,date('Y'));
                if(!$excessiveTardinessCount) $excessiveAbsenteismCount = $CI->disciplinary_action->empWithExcessiveAbsenteism(true,'',false,date('Y'));
                  
                  if($excessiveTardinessCount == true || $excessiveAbsenteismCount == true)
                  {
                      ?>
                        <div class="cul6">
                          <button class="list-group-item modules animated infinite pulse delay-3s" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                          <div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="color:red;font-weight: bold;"></i></div></button>
                        </div>
                      <?
                  }
                  else{
                    ?>  
                        <div class="cul6">
                          <button class="list-group-item modules" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                        </div>
                      <?
                    }
              }else if($menuid == "181")
                {
                    // DATA REQUEST APPROVAL
                $CI->load->model('approval');
                $dataRequestPending = 0;
                $dataRequestPending = $CI->approval->ifHasPendingRequest();
                if($dataRequestPending > 0){
                      ?>
                        <div class="cul6">
                          <button class="list-group-item modules animated infinite pulse delay-3s" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                          <div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="font-weight: bold;"></i><span class='notifcount'><b><?= $dataRequestPending ?></b></span></div></button>
                        </div>
                      <?
                  }
                  else{
                    ?>  
                        <div class="cul6">
                          <button class="list-group-item modules" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                        </div>
                      <?
                    }
                }else if($menuid == "157")
                {
                 $endorsedApplicant = 0;
                  $endorsedApplicant = $this->employeemod->endorsedApplicant()->num_rows();
                  if($endorsedApplicant > 0){
                      ?>
                        <div class="cul6">
                          <button class="list-group-item modules animated infinite pulse delay-3s" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                          <div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="font-weight: bold;"></i><span class='notifcount'><b><?= $endorsedApplicant ?></b></span></div></button>
                        </div>
                      <?
                  }
                  else{
                    ?>  
                        <div class="cul6">
                          <button class="list-group-item modules" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                        </div>
                      <?
                    }
                }
              else if($menuid == "47")
              {
                  // DISCIPLINARY ACTION
                $CI->load->model('documents');
                $pendingDocReq = $CI->documents->ifHasPendingRequest();
                if($pendingDocReq > 0){
                      ?>
                        <div class="cul6">
                          <button class="list-group-item modules animated infinite pulse delay-3s" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                          <div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="font-weight: bold;"></i><span class='notifcount'><b><?= $pendingDocReq ?></b></span></div></button>
                        </div>
                      <?
                  }
                  else{
                    ?>  
                        <div class="cul6">
                          <button class="list-group-item modules" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b>
                        </div>
                      <?
                    }
              }
              else{
                ?>
                  <div class="cul6">
                    <button class="list-group-item modules" data-toggle="popover" data-placement="top" data-container="body" data-trigger="hover" title="Module Description" data-content="<?=$description?>" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'><b><?=$title.$total?></b></button>
                  </div>
                <?
              }
              ?>

        <?       
            }
        ?>   
          </table>   
        </div>
        <?    
        }
        }
        ?>
        </div>
    </div>
</div>

<script>

$('[data-toggle="popover"]').popover();

$('.modules').click(function(){
  $('#content').addClass("animated fadeOut");
});

$(".list-group").delegate(".activeTitle", "click", function(){
  var titleid = $(this).attr("titleid");
  $('.row').removeClass("fadeInUp delay-1s").addClass("fadeOutDown");
   setTimeout(
    function() 
    { 
      $(".menu").show();
      $("button[titleid='" + titleid + "']").removeClass("activeTitle").addClass("titlemenu");
      $('.menuactive').removeClass("fadeOutDown");
      $("div[parenttitle='" + titleid + "']").removeClass("col-md-8");
      $("#mdoffset").removeClass('col-md-2').addClass('col-md-1');
      $("div[parenttitle='" + titleid + "']").removeClass("menuactive");
      $(".cul6").removeClass('col-md-6');
      $(".menu").addClass("col-md-2");
      $('.row').removeClass("fadeOutDown").addClass("fadeInUp");
    }, 1000);
});


$(".menu").delegate(".titlemenu", "click", function(){
  var titleid = $(this).attr("titleid");;
  $('.row').removeClass("fadeInUp").addClass("fadeOutDown");
  $(this).addClass("activeTitle");
  $(this).removeClass("titlemenu");
  setTimeout(
    function() 
    { 
      $(".menu").hide();
      $("div[parenttitle='" + titleid + "']").show();
      $("div[parenttitle='" + titleid + "']").removeClass("col-md-2");
      $("#mdoffset").removeClass('col-md-1').addClass('col-md-2');
      $(".cul6").addClass('col-md-6');
      $("div[parenttitle='" + titleid + "']").addClass("col-md-8");
      $("div[parenttitle='" + titleid + "']").addClass("menuactive");
      $('.row').removeClass("fadeOutDown").addClass("fadeInUp delay-1s");
    }, 500);
 
});


$(".list-group-item").click(function(){
   var site = $(this).attr("site");
   var root = $(this).attr("root");
   var menuid = $(this).attr("menuid");
   var titlebar = $(this).text();
  
   $("#mainform").attr("action","<?=site_url("main/site")?>");
   $("input[name='sitename']").val(site);
   $("input[name='rootid']").val(root);
   $("input[name='menuid']").val(menuid);
   $("input[name='titlebar']").val(titlebar);
   
   if(site) $("#mainform").submit();
});
</script>

<?
}else{    // EMPLOYEE
list($remarks,$time) = $this->extras->getTimeInEmployeeRemarks(); 
$color = "green";
if ($remarks == "absent") {
  $color = "red";
  $remarks = "";
  $remarkStatus = "No Time In and Out";
}elseif ($remarks == "noSched") {
  $color = "red";
  $remarks = "";
  $remarkStatus = "No Schedule";
}elseif ($remarks == "notlog") {
  $color = "green";
  $remarks = "";
  $remarkStatus = "No Time In";
}elseif ($remarks == "LateIn") {
  $color = "red";
  $remarks = $time;
  $remarkStatus = "Late";
}elseif ($remarks == "On Time") {
  $color = "green";
  $remarks = $time;
  $remarkStatus = "On Time";
}

$qholidays = $this->extras->showHol(date("m"),date("Y"));
// echo "<pre>";print_r($this->db->last_query());die;
$CI =& get_instance();
$CI->load->model('announcements');
$a_list = $CI->announcements->getAnnouncements("","",date("m"),date("Y"));
// echo "<pre>";print_r($a_list);
// echo "<pre>";print_r($this->db->last_query());die;

///< for deficiency notif
$CI->load->model('deficiency');
$deficiency_list = $CI->deficiency->getDeficiencyHistory($this->session->userdata('username'), "0");
$d_list_f = "";
$defcount = $deficiency_list->num_rows();
if($defcount > 0) $d_list_f = $CI->deficiency->formatDefListForNotif($deficiency_list);

// FOR DISCIPLINARY ACTION NOTICE
$CI->load->model('disciplinary_action');
$disciplinary_action = $CI->disciplinary_action->getOffenseHistory($this->session->userdata('username'),"NO")->num_rows();


?>
<link href="<?=base_url()?>css/events.css" rel="stylesheet"/>
<link href="<?=base_url()?>css/eventlist.css" rel="stylesheet"/>
<style>
    .def-label{
        color: #f3c92b;
        font-weight: bold;
    }
    .table-striped>tbody>tr:nth-child(odd)>td, 
    .table-striped>tbody>tr:nth-child(odd)>th {
       background-color: #FAF5E2;
     }

     .tooltip {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 15px;
        font-style: normal;
        font-weight: normal;
        line-height: 1.42857143;
        text-align: left;
        text-align: start;
        text-decoration: none;
        text-shadow: none;
        text-transform: none;
        letter-spacing: normal;
        word-break: normal;
        word-spacing: normal;
        word-wrap: normal;
        white-space: normal;
        /* ... */
    }

    .clock {
        margin: 0 auto;
        padding: 5px;
        color: #fff;
    }

    ul.timer li {
        display: inline;
        font-size: 1.1em;
        text-align: center;
        color: black;
    }

    ul.timer {
        text-align: center;
        padding-left: 0px;
    }

    #point {
        position: relative;
        -moz-animation: mymove 1s ease infinite;
        -webkit-animation: mymove 1s ease infinite;
        padding-left: 10px;
        padding-right: 10px;
    }
    #Date { 
        font-size: 23px;
        text-align: center;
        color: black; 
    }

    .btn-success:active, .btn-success.active, .open > .dropdown-toggle.btn-success {
        border: solid 2px #337ab7;
    }

    .btn-danger:active, .btn-danger.active, .open > .dropdown-toggle.btn-danger {
        border: solid 2px #337ab7;
    }

    .nav-pills > li.active > a, .nav-pills > li.active > a:hover {
    color: #fff!important;
    background-color: #337ab7 !important;
    }

    input[type="radio"] {
      display: none;
    }

    input[type="radio"] + label {
      color: #292321;
        font-family: 'Raleway';
        font-weight: 300;
        font-size: 16px;
    }

    input[type="radio"] + label span {
      display: inline-block;
      width: 30px;
      height: 30px;
      margin: 2px 4px 0 0;
      vertical-align: middle;
      cursor: pointer;
      -moz-border-radius: 50%;
      border-radius: 50%;
    }

    input[type="radio"] + label span {
      background-color: #333;
    }

    input[type="radio"]:checked + label span {
      border: 2px solid #3DD2AF;
      background: transparent;
    }

    input[type="radio"] + label span,
    input[type="radio"]:checked + label span {
      -webkit-transition: background-color 0.20s ease-in-out;
      -o-transition: background-color 0.20s ease-in-out;
      -moz-transition: background-color 0.20s ease-in-out;
      transition: background-color 0.20s ease-in-out;
    }
</style>
<?php 
//Web Check In Setup Checker
$WebChecker = $this->employee->webCheckInChecker($this->session->userdata("username"));
$recent = $this->employee->webCheckRecent($this->session->userdata("username"));
// echo "<pre>";print_r($WebChecker);die;
$WebCheckerInfo = "Check In";
if (count($recent) != 0) {
  if ($recent[0]->log_type == "IN") {
    $WebCheckerInfo = "Check Out";
  }elseif($recent[0]->log_type == "OUT"){
    $WebCheckerInfo = "Check In";
  }
}

$survey = $this->employee->webCheckSurvey($this->session->userdata("username"));

$employee_photo = $this->db->query("SELECT * FROM employee_photo where employeeid = '$employeeid'");
$hasPhoto = $hasElfinderPhoto = 0;
if($employee_photo->num_rows() > 0){
    $hasPhoto++;
    $photo = json_decode(json_encode($employee_photo->result()), true);
}else{
  $employee_elfinder_file = $this->db->query("SELECT * FROM elfinder_file a WHERE a.name LIKE '%$employeeid%'")->result();
    foreach ($employee_elfinder_file as $key => $value) {
      $hasElfinderPhoto++;
      $photo = "data:".$value->mime.";base64,".base64_encode($value->content);
    }
}
?>
<div id="content">
    <div class="form_row" style="margin-top: 2%;margin-left: 1%;">
        <div id="userPhotoDiv" style="width: 20%;float: left;text-align: center;margin-top: 6%;" >
          <?php  $userPhoto = $this->extras->userPhoto(); ?>
            <?if($hasPhoto > 0){
              // $photo = json_decode(json_encode($userPhoto->result()), true);
              // $photo = json_decode(json_encode($employee_photo->result()), true);

              ?>
                <img src="data:image/jpg;base64,<?= $photo[0]['file']; ?>" class="img-circle center-block img-ball"/>
            <?}else if($hasElfinderPhoto > 0){?>
                <img src="<?=$photo?>" class="img-circle center-block img-ball"/>
                <!-- <span class="shadow" style="margin-left:-11%!important;margin-top: -26%!important;"></span> -->
            <?}else{?>
                <img src="<?=base_url()?>images/personal.png" class="img-circle center-block img-ball"/>
                <!-- <span class="shadow" style="margin-left:-11%!important;margin-top: -26%!important;"></span> -->
            <?}?>         
        </div>
        <div id="userContentDiv" style="width: 79%;float: left;" >
            <div class="pinfo-margin-side pinfo-margin-top">
                <h4>Welcome, <strong class="text-info"><?=ucwords(strtolower($this->employee->getfullnameuser($this->session->userdata("userid"))))?></strong></h4>
            </div>
            <div class="row">
                <div class="col-sm-12" style="padding-left: 2px;">
                  <form id="webCheckinForm">
                    <input type="hidden" name="localtimein" id="localtimein" value="">
                    <input type="hidden" name="city" id="city" value="">
                    <input type="hidden" name="state" id="state" value="">
                    <input type="hidden" name="country" id="country" value="">
                    <input type="hidden" name="lat" id="lat" value="">
                    <input type="hidden" name="long" id="long" value="">
                    <input type="hidden" name="height" id="height" value="">
                    <input type="hidden" name="width" id="width" value="">
                    <input type="hidden" name="ip" id="ip" value="<?= $this->session->userdata('ip_address'); ?>">
                    <input type="hidden" name="userid" value="<?= $this->session->userdata("username") ?>">
                    <input type="hidden" id="surveyAnswer" name="answer" value="">
                  </form>
                    <div id="survey" style="display: none">
                      <div class="mm-survey">
                        <div class="mm-survey-progress">
                          <div class="mm-survey-progress-bar mm-progress"></div>
                        </div>
                        <div class="mm-survey-bottom">
                          <div class="mm-survey-container">
                            <form id="survey-form">
                            <?php foreach ($survey as $row => $val): ?>
                              <div class="mm-survey-page <?= ($row == 0)? 'active':'' ?>" data-page="<?= $row + 1 ?>">
                              <div class="mm-survery-content">
                                <div class="mm-survey-question">
                                  <p><?= $val->description ?></p>
                                </div>
                                <div class="mm-survey-item">
                                  <?php foreach (explode("/", substr($val->questions, 1)) as $key => $value): 
                                    $info = explode("*", $value);
                                    ?>
                                    <?php if ($info[1] == "TEXT"){ ?>
                                      <div class="form-group">
                                        <label><?= $info[0] ?></label>
                                        <input type="text" class="form-control survey active" category="<?= $val->category ?>" question="<?= $info[0] ?>" description="<?= $val->description ?>" item="<?= $info[1] ?>">
                                      </div>
                                    <?php }elseif ($info[1] == "TIME") { ?>
                                      <div class="form-group">
                                        <label><?= $info[0] ?></label>
                                        <div class='input-group time'>
                                            <input type='text' class="form-control survey active" category="<?= $val->category ?>" question="<?= $info[0] ?>" description="<?= $val->description ?>" item="<?= $info[1] ?>" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                      </div>
                                    <?php }elseif ($info[1] == "DATE") { ?>
                                      <div class="form-group">
                                        <label><?= $info[0] ?></label>
                                        <div class='input-group date'>
                                            <input type='text' class="form-control survey active" category="<?= $val->category ?>" question="<?= $info[0] ?>" description="<?= $val->description ?>" item="<?= $info[1] ?>"/>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                      </div>
                                    <?php }elseif ($info[1] == "NUMBER") { ?>
                                      <div class="form-group">
                                        <label><?= $info[0] ?></label>
                                        <input type="number" class="form-control survey active" category="<?= $val->category ?>" question="<?= $info[0] ?>" description="<?= $val->description ?>" item="<?= $info[1] ?>">
                                      </div>
                                    <?php }elseif ($info[1] == "YN") { ?>
                                      <div class="form-group">
                                        <label><?= $info[0] ?></label><br>
                                        <div data-toggle="buttons">
                                          <div class="col-sm-4 col-md-1 col-sm-offset-2 col-md-offset-0">
                                            <label class="btn btn-success survey" value="Yes" item="<?= $info[1] ?>" category="<?= $val->category ?>" question="<?= $info[0] ?>" description="<?= $val->description ?>">
                                            <input type="radio" autocomplete="off"> Yes
                                            </label>
                                          </div>
                                          <div class="col-sm-4 col-md-1">
                                            <label class="btn btn-danger survey" value="No" item="<?= $info[1] ?>" category="<?= $val->category ?>" question="<?= $info[0] ?>" description="<?= $val->description ?>">
                                            <input type="radio" autocomplete="off"> No
                                            </label>
                                          </div>
                                        </div>
                                      </div>
                                    <?php } ?>
                                  <?php endforeach ?>
                                </div>
                              </div>
                            </div>
                            <?php endforeach ?>
                            </form>
                          </div>
                          <div class="mm-survey-controller">
                            <div class="mm-next-btn">
                              <button disabled="true">Next</button>
                            </div>
                            <div class="mm-finish-btn">
                              <button id="submitSurveyForm" >Submit</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div id="webCapturing" style="text-align: center;display: none">
                      <div class="col-md-12">
                        <div id="my_camera"></div>
                        <br><br>
                        <div>
                          <button class="btn btn-primary" id="snap" style="padding: 8px;font-size: 27px;"><i class="glyphicon glyphicon-camera"></i> Snap</button>&nbsp;&nbsp;&nbsp;
                          <button class="btn btn-warning" id="retake" style="padding: 8px;font-size: 27px;"><i class="glyphicon glyphicon-repeat"></i> Retake</button>&nbsp;&nbsp;&nbsp;
                          <button class="btn btn-success" id="saveCheckIn" style="padding: 8px;font-size: 27px;"><i class="glyphicon glyphicon-ok"></i> Submit</button>
                        </div><br><br>  
                      </div>
                    </div>
                    <div id="dashboard">
                        <div class="col-md-<?= ($WebChecker)? '4':'12'?>" style="padding-left: 0px;">
                            <div class="panel panel-primary" style="margin-bottom: 20px !important;border: 1px solid transparent !important;height: 267px">
                                <div class="panel-heading" style="background-color: #337ab7!important;color: white;">
                                    <h3 class="panel-title">Today's Attendance</h3>
                                </div>
                                <div class="panel-body">
                                    <button class="pinfo-margin-side btn btn-success" id="checkin" data-toggle="tooltip" title="Click to inititate work from home." style="margin-bottom:1%;font-size:130%;display:none;" >Check In</button>
                                    <div class="container pinfo-margin-side" id="accomplishment" style="display: none;">
                                        <div class="form-group" style="margin-bottom:4%;">
                                          <label class="control-label col-sm-2" for="files">Attachment:</label>
                                          <div class="col-sm-10">
                                            <input type="file" class="form-control" id="files" placeholder="Enter email" name="files">
                                          </div>
                                        </div>
                                        <div class="form-group">
                                          <label class="control-label col-sm-2" for="acc_remarks">Remarks:</label>
                                          <div class="col-sm-10">          
                                            <textarea name="acc_remarks" id="acc_remarks" cols="100" rows="5" style="width: 100%;"></textarea>
                                          </div>
                                        </div>
                                        
                                        <button class="pinfo-margin-side btn btn-success" id="submit_accomplishment" data-toggle="tooltip" title="Click to inititate work from home." style="margin-bottom:1%;font-size:130%;float: right;margin-right: 1% !important;">Submit</button>
                                    </div>
                                    <p class="pinfo-margin-side" id="verifiying" style="display:none;"><strong><img src='<?=base_url()?>images/loading.gif'> Verifiying employee schedule, please Wait..</img> </strong></p>
                                    <p class="pinfo-margin-side"><strong>Time Log&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;<span style="color: <?=$color?>;" id="current_timein"><?=$remarks?></span></strong></p>
                                    <p class="pinfo-margin-side"><strong>Remarks&nbsp;:&nbsp;&nbsp;<span style="color: <?=$color?>;" id="att_remarks"><?= $remarkStatus ?></span></strong></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" <?= ($WebChecker)? '':'style="display:none"'?> style="padding-left: 0px">
                            <div class="panel panel-primary" style="margin-bottom: 20px !important;border: 1px solid transparent !important;height: 267px">
                                <div class="panel-heading" style="background-color: #337ab7!important;color: white;">
                                    <h3 class="panel-title">Web Check In</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="clock">
                                        <div id="Date"><?php echo date("D d F Y", strtotime($this->db->query("SELECT CURRENT_TIMESTAMP")->row()->CURRENT_TIMESTAMP)); ?></div>
                                        <ul class="timer">
                                            <li id="hours">--</li>
                                            <li id="point">:</li>
                                            <li id="min">--</li>
                                            <li id="point">:</li>
                                            <li id="sec">--</li>
                                            <li>&nbsp;</li>
                                            <li id="periods">--</li>
                                        </ul>
                                    </div>
                                    <br><br>
                                    <div class="col-md text-center">
                                        <button class="btn <?= ($WebCheckerInfo == 'Check In')? 'btn-success':'btn-danger' ?>" id="webCheckin" data-toggle="tooltip" title="Click to inititate work from home web check in." style="margin-bottom:1%;font-size:130%;"><?= $WebCheckerInfo ?></button>
                                    </div><br><br>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" <?= ($WebChecker)? '':'style="display:none"'?> style="padding-left: 0px">
                            <div class="panel panel-primary" style="margin-bottom: 20px !important;border: 1px solid transparent !important;height: 267px">
                                <div class="panel-heading" style="background-color: #337ab7!important;color: white;">
                                    <h3 class="panel-title">Recent Check In</h3>
                                </div>
                                <div class="panel-body">
                                  <?php if (count($recent) == 0){ ?>
                                    <br><br><h2 style="text-align: center">No Check In Today</h2>
                                  <?php }else{ ?>
                                    <ul class="list-group" style="text-align: center;">
                                      <?php foreach ($recent as $key => $value): ?>
                                        <?php if ($value->log_type == "IN"){ ?>
                                          <li class="list-group-item list-group-item-success">&nbsp;&nbsp;&nbsp;&nbsp;IN  : <?= date("Y-m-d g:i:s A", strtotime($value->localtimein));  ?></li>
                                        <?php }elseif ($value->log_type == "OUT") { ?>
                                          <li class="list-group-item list-group-item-danger">OUT : <?= date("Y-m-d g:i:s A", strtotime($value->localtimein)) ?></li>
                                        <?php } ?>
                                      <?php endforeach ?>
                                    </ul>
                                  <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
            <div class="col-md-12" style="padding-left: 2px;padding-right: 28px;">
                <div class="panel panel-primary" style="margin-bottom: 20px !important;border: 1px solid transparent !important;">
                    <div class="panel-heading" style="background-color: #337ab7!important;color: white;">
                        <h3 class="panel-title">ANNOUNCEMENTS & HOLIDAYS</h3>
                    </div>
                    <div class="panel-body">
                        <ul  class="nav nav-pills">
                            <li><a href="#pastevent" data-toggle="tab" id="pasteventClicker">PAST EVENT(s)</a></li>
                            <li class="active"><a href="#upcoming" data-toggle="tab">CURRENT AND UPCOMING EVENT(s)</a></li>    
                        </ul>
                        <br>
                        <div class="container-fluid" id="seminar_announce"></div>
                        <div class="tab-content clearfix">
                            <!-- FOR PAST EVENTS -->
                            <div class="tab-pane" id="pastevent">
                              <div class="col-md-6">
                                <select class="form-control" name="month" id="month">
                                    <?=$this->extras->showMonth($months)?>
                                 </select>
                              </div>
                              <div class="col-md-6">
                                  <?php
                                      $Startyear=date('Y');
                                      $endYear=$Startyear-1;
                                      $yearArray = range($Startyear,$endYear);
                                      ?>
                                     
                                      <select class="form-control" name="year" id="year">
                                          <option value="">- Select Year- </option>
                                          <?php
                                          foreach ($yearArray as $year) {
                                              // this allows you to select a particular year
                                          $selected = ($year == $Startyear) ? 'selected' : '';
                                          echo '<option value="'.$year.'" '.$selected.' >'.$year.'</option>';
                                      }
                                   ?>
                                    </select>
                              </div>
                                <br><br>
                                <div id='currenthistory' style="display:block;">  <?foreach($a_list as $row){
                               $qdate = $this->extras->holDate($row->datefrom, $row->dateto);

                               foreach($qdate as $rowd){
                                             if ($today >= $row->datefrom ) {
                                               ?>
                                               <div class="eventsize">
                                                   <a href="#" class="postcard-link">
                                                   <div class="postcard-left">
                                                       <div class="showcdes">
                                                           <p style="color: white"><?=date("l",strtotime($rowd->dte))?></p>
                                                           <p style="font-weight: bold !important;font-size: 21px;color: white"><?=date("d",strtotime($rowd->dte))?></p>
                                                           <p style="color: white"><span><?=date("F",strtotime($rowd->dte))?></span> <span><?=date("Y",strtotime($rowd->dte))?></span></p>
                                                       </div>
                                                       <div class="postcard-text">
                                                            <h3></h3>
                                                           <span style="font-size: 9px;">
                                                               <!-- <p style="color: red;">         CONCERNED DEPARTMENT:                                               </p>
                                                               <p style="color: black;">       <b>&nbsp;<?=$this->extras->getemployeedepartment($row->deptid)?></b></p> -->
                                                               <p style="color: red;"> <b> VENUE:</b><span style="color: black;"><?=$row->venue?><span> </p>
                                                               
                                                               <p style="color: red; "><b>TIME:</b>                                                            
                                                               <span style="color: black;"><?
                                                                                                   $timedisp = date("h:i a",strtotime($row->timefrom)) . " - " . date("h:i a",strtotime($row->timeto));
                                                                                                   echo ucwords(strtoupper($timedisp));
                                                                                                   ?></span>  </p>
                                                               <p style="color: red; "><b>EVENT:</b>  <p>
                                                               <p style="color: black">
                                                                 <?=$row->event?>
                                                               </p>                                                          
                                                           </span>
                                                       </div>
                                                   </div>
                                                   </a><hr class="clearfix">
                                               </div>
                                           <?
                                               }
                                           }
                                           }   
                                           ?>
                                       
                                           <?foreach($qholidays as $row){
                                               $username = $this->session->userdata("username");
                                               $qdate = $this->extras->holDate($row->date_from,$row->date_to);
                                               $deptid = $this->extensions->getEmployeeDeparment($username);
                                               $campus = $this->extensions->getEmployeeDeparment($username);
                                               $teachingtype = $this->extensions->getEmployeeTeachingType($username);
                                               $wholeHoliday = $this->attcompute->isHolidayNew($username,$row->date_from,$deptid,$campus,"",$teachingtype="");
                                               $halfHoliday = $this->attcompute->isHolidayNew($username,$row->date_from,$deptid,$campus,"on",$teachingtype="");
                                               // echo "<pre>";print_r($today);die;
                                               foreach($qdate as $rowd){
                                                 if ($today > $rowd->dte && ($wholeHoliday || $halfHoliday)) {
                                                 

                                               ?>
                                               <div class="eventsize">
                                                   <a href="#" class="postcard-link">
                                                   <div class="postcard-left">
                                                       <div class="showcdes">
                                                           <p style="color: white"><?=date("l",strtotime($rowd->dte))?></p>
                                                           <p style="font-weight: bold !important;font-size: 21px;color: white"><?=date("d",strtotime($rowd->dte))?></p>
                                                           <p style="color: white"><span><?=date("F",strtotime($rowd->dte))?></span> <span><?=date("Y",strtotime($rowd->dte))?></span></p>
                                                       </div>
                                                       <div class="postcard-text" >
                                                           <h3><?#=ucwords(strtolower($row['event']))?></h3>
                                                           <p class="event-text" style="font-size: 8.5px;">
                                                               <p style="color: red;"><b>HOLIDAY NAME:</b></p>
                                                               <p style="color: black;"><?= ucwords(strtoupper($row->hdescription))?></p><br />
                                                               <p style="color: red;"><b>HOLIDAY TYPE:</b></p>
                                                               <p style="color: black;"><?= ucwords(strtoupper($row->description))?></p>
                                                           </p>
                                                       </div>
                                                   </div>
                                                   </a><hr class="clearfix">
                                               </div>
                                           <?
                                           }
                                           }
                                           }   
                                           ?>
                                           </div>
                                <div id="pasteventhistory"></div> 
                            </div>
                                    <!-- FOR UPCOMING EVENT -->
                        <div class="tab-pane active" id="upcoming">
                              <?foreach($a_list as $row){
                                  $qdate = $this->extras->holDate($row->datefrom,$row->dateto);
                                  foreach($qdate as $rowd){
           
                                                if ($today <= $rowd->dte ) {
                                                  ?>
                                                  <div class="eventsize">
                                                       <a href="#" class="postcard-link">
                                                       <div class="postcard-left">
                                                           <div class="showcdes1" style="color: white; background-color: #337ab7;">
                                                               <p style="color: white;"><?=date("l",strtotime($rowd->dte))?></p>
                                                               <p style="color: white;font-weight: bold !important;font-size: 21px;"><?=date("d",strtotime($rowd->dte))?></p>
                                                               <p style="color: white;"><span><?=date("F",strtotime($rowd->dte))?></span> <span><?=date("Y",strtotime($rowd->dte))?></span></p>
                                                           </div>
                                                           <div class="postcard-text">
                                                                <p class="event-text" style="font-size: 8.5px;">
                                                                     <p style="color: red;"><b>VENUE:</b></p>
                                                                     <p style="color: black;">&nbsp;<?=$row->venue?></p>
                                                                     <p style="color: red;"><b>TIME:</b></p>
                                                                     <p style="color: black;">&nbsp;&nbsp;<?
                                                                                                           $timedisp = date("h:i a",strtotime($row->timefrom)) . " - " . date("h:i a",strtotime($row->timeto));
                                                                                                           echo ucwords(strtoupper($timedisp));
                                                                                                           ?></p>
                                                                     <p style="color: red;"><b>EVENT:</b></p>
                                                                     <p style="color: black;">&nbsp;&nbsp;<?=$row->event?></p>
                                                                 </p>
                                                           </div>
                                                       </div>
                                                       </a><hr class="clearfix">
                                                   </div>
                                              <?
                                                  }
                                              }
                                              }   
                                              ?>
                                          
                                              <?foreach($qholidays as $row){
                                                   $username = $this->session->userdata("username");
                                                   $qdate = $this->extras->holDate($row->date_from,$row->date_to);
                                                   $deptid = $this->extensions->getEmployeeDeparment($username);
                                                   $campus = $this->extensions->getEmployeeDeparment($username);
                                                   $teachingtype = $this->extensions->getEmployeeTeachingType($username);
                                                   $wholeHoliday = $this->attcompute->isHolidayNew($username,$row->date_from,$deptid,$campus,"",$teachingtype="");
                                                   $halfHoliday = $this->attcompute->isHolidayNew($username,$row->date_from,$deptid,$campus,"on",$teachingtype="");
                                                  // echo "<pre>";print_r($today);die;
                                                  foreach($qdate as $rowd){
                                                    if ($today <= $rowd->dte && ($wholeHoliday || $halfHoliday)) 
                                                    {
                                                  ?>
                                                  <div class="eventsize">
                                                       <a href="#" class="postcard-link">
                                                       <div class="postcard-left">
                                                           <div class="showcdes" style="color: white; background-color: #337ab7;">
                                                               <p style="color: white;"><?=date("l",strtotime($rowd->dte))?></p>
                                                               <p style="color: white;font-weight: bold !important;font-size: 21px;"><?=date("d",strtotime($rowd->dte))?></p>
                                                               <p style="color: white;"><span><?=date("F",strtotime($rowd->dte))?></span> 
                                                               <span><?=date("Y",strtotime($rowd->dte))?></span></p>
                                                           </div>
                                                           <div class="postcard-text" >
                                                              <h3><?#=ucwords(strtolower($row['event']))?></h3>
                                                              <p style="font-size: 8.5px;">
                                                                   <p style="color: red;"><b>Holiday Name:</b></p>
                                                                   <p style="color: black;">&nbsp;<?=$row->hdescription?></p><br />
                                                                   <p style="color: red;"><b>Holiday Type:</b></p>
                                                                   <p style="color: black;">&nbsp;<?=$row->description?></p>
                                                               </p>
                                                           </div>
                                                       </div>
                                                       </a><hr class="clearfix">
                                                   </div>
                                              <?
                                              }
                                              }
                                              }   
                                              ?>
                                    </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>  
<div class="modal fade" id="deficiency-modal" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg">

    <div class="modal-content" >
      <div class="modal-header" >
        <div class="media">
          <div class="media-left">
            <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
          </div>
          <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
            <h4 class="media-heading"  style="font-size: 18px !important"><b>Pinnacle Technologies Inc.</b></h4>
            <p style="font-family:Avenir; margin-top: -1%; font-size: 16px !important; font-weight: 300 !important">D`Great</p>
          </div>
        </div>
        <center><b><h3 tag="title" class="modal-title" style="font-family: 'Poppins';">Modal Header</h3></b></center>
      </div>
      <div class="modal-body">
        <div class="row">
              <div tag='display'>
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
        <button type="button" class="btn btn-success" id='button_save_modal'>Save changes</button>
        <div id='leaveloading' style="display: none;"><img class='pull-right' src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..</div>
      </div>
    </div>

  </div>
</div>    
<div id="map"></div>
<style type="text/css">
  #snackbar {
  visibility: hidden;
  min-width: 250px;
  margin-left: -125px;
  background-color: #333;
  color: #fff;
  text-align: center;
  border-radius: 2px;
  padding: 16px;
  position: fixed;
  z-index: 1;
  left: 42%;
  bottom: 30%;
  font-size: 17px;
}

  #snackbar.show {
    visibility: visible;
    -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
    animation: fadein 0.5s, fadeout 0.5s 2.5s;
  }

  @-webkit-keyframes fadein {
    from {bottom: 0; opacity: 0;} 
    to {bottom: 30%; opacity: 1;}
  }

  @keyframes fadein {
    from {bottom: 0; opacity: 0;}
    to {bottom: 30%; opacity: 1;}
  }

  @-webkit-keyframes fadeout {
    from {bottom: 30%; opacity: 1;} 
    to {bottom: 0; opacity: 0;}
  }

  @keyframes fadeout {
    from {bottom: 30%; opacity: 1;}
    to {bottom: 0; opacity: 0;}
  }

</style>
<div id="snackbar">Your information has been saved.</div>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDjPDJx-ya24arAX0jx5fdgPsm6EJex8SE" async defer></script>
<script type="text/javascript">
  Webcam.set({
      width: 720,
      height: 720,
      image_format: 'jpeg',
      jpeg_quality: 90,
      unfreeze_snap: true
    });

  Webcam.set("constraints", {
    optional: [{ minWidth: 600 }]
  });
</script>
<script type="text/javascript">
    var toks = hex_sha512(" ");
    currentLogStatus();
    seminarAnnouncement();
    var server_unixtime = "<?= date('U') ?>";
    var answer = "";
    var secs = "";
    var hh = "";
    var mm = "";
    var period = "";

    $(document).ready(function(){

      $('.time').datetimepicker({
            format: 'LT'
      });

      $(".date").datetimepicker({
        format: "YYYY-MM-DD"
      });

    var withDA = "<?=$disciplinary_action?>";
    if(withDA > 0 ){
      $('.button_save_modal').html("Proceed");
            $('.modal-footer').find("a[data-dismiss='modal']").hide();
            $("#modal-view").find("h3[tag='title']").html("");
            $("#modal-view").attr("data-keyboard", "false");
            $("#modal-view").find("#modalclose").css("display", "none");
            $("#modal-view").find(".modal-dialog").css("width", "35%").css('margin-bottom:', '-27px;');
            // $("#modal-view").find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
            $("#modal-view").find("div[tag='display']").html("<h4><strong>You currently have a Disciplinary Action Notice: </strong><tag style='font-size: 1.1em; font-weight: 300;'> Kindly confirm this notice on Disciplinary Action Module</tag></h4>").css('color','black').css('text-align','left').css('margin-left','29px').css('margin-left','29px');
            $('#modal-view').modal('show');
        }
    else{
      var defcount = "<?=$defcount?>";
      if(defcount > 0 ){
        $('#button_save_modal').hide();
        $("#deficiency-modal").find("h3[tag='title']").html("<b>Reminder from Clearance Module</b>").css('color','red');
        $("#deficiency-modal").find("div[tag='display']").html("<?=$d_list_f?>");
        $('#deficiency-modal').modal('show');
      }
    }
    
    $('.button_save_modal').click(function(){
      var site = $('a[menuid="119"]').attr("site");
      var root = $('a[menuid="119"]').attr("root");
      var menuid = $('a[menuid="119"]').attr("menuid");
      var titlebar = $('a[menuid="119"]').text();
      
      $.ajax({
        url: "<?=site_url("utils_/storeCurrentMenu")?>",
        type: 'POST',
        data : {menuid: GibberishAES.enc(menuid, toks), toks:toks},
        success: function(msg){
          $("#mainform").attr("action","<?=site_url("main/site")?>");
          $("input[name='sitename']").val(site);
          $("input[name='rootid']").val(root);
          $("input[name='menuid']").val(menuid);
          $("input[name='titlebar']").val(titlebar);

          if(site) $("#mainform").submit();
        }
      }); 
    });
    
  });
    function displayAnnouncementHoliday(month,year)
    {
        var form_data = {
            month: GibberishAES.enc( month, toks),
            year: GibberishAES.enc(year , toks),
            toks:toks
        }
        $("#pasteventhistory").html("<tr><td colspan='4'><img src='<?=base_url()?>images/loading.gif'> Loading Please Wait..</img></td></tr>");
        $.ajax({
            url:"<?=site_url("announcements_/loadpastEvents")?>",
            type:"POST",
            data:form_data,
            success:function(msg)
            {
                $("#pasteventhistory").html(msg);
                // alert(msg);
            }
        });
    }
    
    $("#year,#month").change(function()
    {
       if ($("#month").val() =="" && $("#year").val() == "") {
         $("#currenthistory").show();
         $("#pasteventhistory").hide();
       }
       else
       {
        displayAnnouncementHoliday($("#month").val(),$("#year").val());
        $("#currenthistory").hide();
         $("#pasteventhistory").show();
       }
    });

    function seminarAnnouncement(){
      $.ajax({
        url: "<?= site_url('seminar_/seminarAnnouncement') ?>",
        success:function(response){
          $("#seminar_announce").html(response);
        }
      });
    }

  $("#submit_accomplishment").click(function(){
    $(this).hide();
    $("#verifiying").show();

    var acc_remarks = $.trim($("#acc_remarks").val());
    if(acc_remarks){
      var form_data  = new FormData();
      file_data = $("#files").prop("files")[0]
      form_data.append("files",file_data);
      form_data.append("acc_remarks",acc_remarks);
      form_data.append("id",$(this).val());
      currentLogStatus();
    }else{
      alert("Please attached your accomplishments.");
      currentLogStatus();
      return;
    }
    $.ajax({
        url: "<?=site_url('gate_/validateEmployeeAccomplishment')?>",
        type: "POST",
        data: form_data,
        contentType: false,
        processData: false,
        dataType: "json",
        success:function(response){
          $("#snackbar").html(response.msg);
          $("#current_timein").text(response.time);
          if(response.remarks) $("#att_remarks").text("Late");
          else $("#att_remarks").text("On time");
          var x = document.getElementById("snackbar");
          x.className = "show";
          setTimeout(function() { x.className = x.className.replace("show", ""); }, 3000);
          currentLogStatus();
        }
    });
  });
  
  $("#checkin").click(function(){
    $.ajax({
      url: "<?=site_url('gate_/enterFieldWork')?>",
      dataType: "json",
      success:function(response){
        $("#snackbar").html(response.msg);
        $("#current_timein").text(response.time);
        if(response.remarks) $("#att_remarks").text("Late");
        else $("#att_remarks").text("On time");
        var x = document.getElementById("snackbar");
        x.className = "show";
        setTimeout(function() { x.className = x.className.replace("show", ""); }, 3000);
        currentLogStatus();
      }
    });
  });

  function getTwentyFourHourTime(amPmString) { 
      var d = new Date("1/1/2013 " + amPmString); 
      return d.getHours() + ':' + d.getMinutes(); 
  }

  const convertTime12to24 = (time12h) => {
    const [time, modifier] = time12h.split(' ');

    let [hours, minutes] = time.split(':');

    if (hours === '12') {
      hours = '00';
    }

    if (modifier === 'PM') {
      hours = parseInt(hours, 10) + 12;
    }

    return `${hours}:${minutes}`;
}

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
  } else { 
    x.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function showPosition(position) {
    $('#lat').val(position.coords.latitude);
    $('#long').val(position.coords.longitude);
    //Create query for the API.
      var latitude = "latitude=" + position.coords.latitude;
      var longitude = "&longitude=" + position.coords.longitude;
      var query = latitude + longitude + "&localityLanguage=en";

      const Http = new XMLHttpRequest();

      var bigdatacloud_api =
        "https://api.bigdatacloud.net/data/reverse-geocode-client?";

      bigdatacloud_api += query;

      Http.open("GET", bigdatacloud_api);
      Http.send();

      Http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          var myObj = JSON.parse(this.responseText);
            $('#country').val(myObj.countryName);
            $('#state').val(myObj.principalSubdivision);
            $('#city').val(myObj.locality);
            $('#lat').val(position.coords.latitude);
            $('#lang').val(position.coords.longitude);
          
        }
      };
}

function geocodeLatLng(geocoder, map, infowindow, lat, lang) {

var latlng = {lat: parseFloat(lat), lng: parseFloat(lang)};
geocoder.geocode({'location': latlng}, function(results, status) {
  if (status === 'OK') {
    if (results[0]) {
      map.setZoom(11);
      var marker = new google.maps.Marker({
        position: latlng,
        map: map
      });
      infowindow.setContent(results[0].formatted_address);
      infowindow.open(map, marker);
    } else {
      window.alert('No results found');
    }
  } else {
    window.alert('Geocoder failed due to: ' + status);
  }
});
}

  $("#webCheckin").click(function(){
    var seconds = $("#sec").text();
    var minutes = $("#min").text();
    var hours = $("#hours").text();

    $("#localtimein").val(hh+":"+mm+":"+secs+period);
    var lastLog = "<?= (count($recent))? date("H:i", strtotime($recent[0]->localtimein)):"" ?>";
    var startTime = new Date('2012/10/09 '+ lastLog); 
    var endTime = new Date('2012/10/09 '+ convertTime12to24(hours+":"+minutes+" "+period));
    var difference = endTime.getTime() - startTime.getTime(); // This will give difference in milliseconds
    var resultInMinutes = Math.round(difference / 60000);
    if (resultInMinutes < 2 && lastLog != "") {
        Swal.fire({
              icon: 'warning',
              title: 'warning!',
              text: 'Please wait two minutes.',
              showConfirmButton: true,
              timer: 2500
        })
        return;
    }
    getLocation();

    $("#sidebarCollapse").click();
    $("#dashboard").hide();

    var surveyChecker = "<?= count($survey); ?>";
    if (surveyChecker > 0) {
      getSurvey();
    }else{
      getImage();
    }

  });

  $("#retake").click(function(){
    Webcam.reset();
    Webcam.attach( '#my_camera' );
    $("#my_camera").css("width","100%");
    $("video").css("width","100%");
    $("video").css("height","unset");
    $("#my_camera").css("height","unset");
  });

  $("#snap").click(function(){
    var vidHeight = $("video").height();
    var vidWidth = $("video").width();
    Webcam.snap( function(data_uri) {
      document.getElementById('my_camera').innerHTML = '<img src="'+data_uri+'" width="'+ vidWidth +'"  height="'+ vidHeight +'"/>';
      $("#height").val(Number(vidHeight) / 3);
      $("#width").val(Number(vidWidth) / 3);
    });
  });

  $("#saveCheckIn").click(function(){
    $(this).prop('disabled', true);
    Swal.fire({
          icon: 'info',
          title: 'Sent!',
          text: 'Please wait',
          timer: 1000
    })
    
    Webcam.snap( function(data_uri) {
      Webcam.upload( data_uri,  '<?=site_url('webcheckin_/saveCheckIn')?>'+ '?' + $("#webCheckinForm").serialize(), function(code, text) {
        if (text.trim() == 'success') {
            Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: 'You have successfully check in.',
                  showConfirmButton: true,
                  timer: 2500
            })
          setTimeout(function() {
            location.reload();
          }, 2500);
        }else if(text.trim() == 'wait'){
          Swal.fire({
                icon: 'info',
                title: 'Wait!',
                text: 'Please wait for two minutes.',
                timer: 1000
          })
          setTimeout(function() {
            location.reload();
          }, 2500);
        }
      } );
    
    } );
  });

  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
  });

  function currentLogStatus(){
    var checker = '<?= $WebChecker ?>';
    var recent = '<?= count($recent) ?>';
    $.ajax({
      url: "<?=site_url('gate_/currentLogStatus')?>",
      dataType: "json",
      data: {checker: GibberishAES.enc( checker, toks),recent: GibberishAES.enc(recent , toks),toks:toks},
      success:function(response){
        if (response.type == "web") {
            if (response.remarks == "On Time") { var color = "green"
            }else{ var color = "red" }
            if(response.remarks == "Absent"){
                $("#current_timein").text("").css("color", color);
                $("#att_remarks").text("No Time In and Out").css("color", color);
            }else if(response.remarks == "Late"){
                $("#current_timein").text("").css("color", "red");
                $("#att_remarks").text("Late").css("color", "red");
            }else if(response.remarks == "LateIn"){
                $("#current_timein").text(response.timein).css("color", "red");
                $("#att_remarks").text("Late").css("color", "red");
            }else if(response.remarks == "Not Logged"){
                $("#current_timein").text("").css("color", "green");
                $("#att_remarks").text("No Time In").css("color", "green");
            }else if(response.remarks == "NoSched"){
                $("#current_timein").text("").css("color", "red");
                $("#current_timein").text(response.timein).css("color", "red");

                $("#att_remarks").text("No Schedule").css("color", "red");
            }else if(response.remarks == "NoSchedToday"){
                $("#current_timein").text(response.timein).css("color", "red");
                $("#att_remarks").text("No Schedule Today").css("color", "red");
            }else if(response.remarks == "Logged"){
                $("#current_timein").text(response.timein).css("color", "green");
                $("#att_remarks").text("Schedule Logged").css("color", "green");
            }else if(response.remarks == "holiday"){
                $("#att_remarks").text(response.timein).css("color", "green");
            }else if(response.remarks == "Flexi"){
                $("#att_remarks").text("Flexible Schedule").css("color", "green");
            }else{
                $("#current_timein").text(response.timein).css("color", color);
                $("#att_remarks").text(response.remarks).css("color", color);
            }   
        }else{
            $("#acc_remarks").val("");
            $("#files").val("");
            if(response.id && response.log == "IN"){ 
              $("#submit_accomplishment").show().val(response.id);
              $("#accomplishment").show();
            }else if(response.remarks == "Flexi"){
                $("#att_remarks").text("Flexible Schedule").css("color", "green");
            }else{
              $("#accomplishment").hide();
              $("#verifiying").hide();
            }
        }
      }
    });
  }

    function getServerTime(unix_timestamp, categ) {
        var date = new Date(unix_timestamp * 1000);
        // Hours part from the timestamp
        var hours = date.getHours();
        // Minutes part from the timestamp
        var minutes = "" + date.getMinutes();
        // Seconds part from the timestamp
        var seconds = "" + date.getSeconds();

        // Will display time in 10:30:23 format
        hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
        if (categ == "seconds") return seconds.substr(-2);
        else if (categ == "minutes") return minutes.substr(-2);
        else return hours;
    }

    setInterval(function() {
        // Create a newDate() object and extract the seconds of the current time on the visitor's
        var seconds = getServerTime(server_unixtime, "seconds");
        // Add a leading zero to seconds value
        $("#sec").html((seconds < 10 ? "0" : "") + seconds);
        secs = (seconds < 10 ? "0" : "") + seconds;
        server_unixtime++;
    }, 1000);

    setInterval(function() {
        // Create a newDate() object and extract the minutes of the current time on the visitor's
        var minutes = getServerTime(server_unixtime, "minutes");
        // Add a leading zero to the minutes value
        $("#min").html((minutes < 10 ? "0" : "") + minutes);
        mm = (minutes < 10 ? "0" : "") + minutes;
    }, 1000);

    setInterval(function() {
        // Create a newDate() object and extract the hours of the current time on the visitor's
        var hours = getServerTime(server_unixtime, "hours");
        
        var dd = "AM";
        var h = hours;
        if (h >= 12) {
            h = hours - 12;
            dd = "PM";
        }
        if (h == 0) {
            h = 12;
        }
        // Add a leading zero to the hours value 
        $("#hours").html((h < 10 ? "0" : "") + h);
        hh = (h < 10 ? "0" : "") + h;
        $("#periods").html(dd);
        period = dd;

    }, 1000);

  var x;
  var count;
  var current;
  var percent;
  var z = [];

  init();
  getCurrentSlide();
  goToNext();
  getCount();
  buildStatus();
  deliverStatus();

  function init() {
    $('.mm-survey-container .mm-survey-page').each(function() {

      var item;
      var page;

      item = $(this);
      page = item.data('page');

      item.addClass('mm-page-'+page);
    });

  }

  function getCount() {

    count = $('.mm-survey-page').length;
    return count;

  }

  function goToNext() {

    $('.mm-next-btn').on('click', function() {
      var validator = false;
      $(".mm-survey-page.active .survey.active").each(function( index ) {
        var val = "";
        if ($(this).attr("type") == "YN" && $(this).hasClass("active")) val = $(this).attr("value");
        else val = $(this).val();
        if (val == "") {
          validator = true;
        }
      });
      if (validator) {
        Swal.fire({
              icon: 'warning',
              title: 'warning!',
              text: 'Please answer the questions.',
              showConfirmButton: true,
              timer: 2500
        })
        return false;
      }

      goToSlide(x);
      getCount();
      current = x + 1;
      var g = current/count;
      buildProgress(g);
      var y = (count + 1);
      getButtons();
      $('.mm-survey-page').removeClass('active');
      $('.mm-page-'+current).addClass('active');
      getCurrentSlide();
      checkStatus();
      if( $('.mm-page-'+count).hasClass('active') ){
        if( $('.mm-page-'+count).hasClass('pass') ) {
          $('.mm-finish-btn').addClass('active');
        }
        else {
          $('.mm-page-'+count+' .mm-survery-content .mm-survey-item').on('click', function() {
            $('.mm-finish-btn').addClass('active');
          });
        }
      }
      else {
        $('.mm-finish-btn').removeClass('active');
        if( $('.mm-page-'+current).hasClass('pass') ) {
          $('.mm-survey-container').addClass('good');
          $('.mm-survey').addClass('okay');
        }
        else {
          $('.mm-survey-container').removeClass('good');
          $('.mm-survey').removeClass('okay');
        }
      }
      buttonConfig();
    });

  }

  function buildProgress(g) {

    if(g > 1){
      g = g - 1;
    }
    else if (g === 0) {
      g = 1;
    }
    g = g * 100;
    $('.mm-survey-progress-bar').css({ 'width' : g+'%' });

  }

  function goToSlide(x) {

    return x;

  }

  function getCurrentSlide() {
    $('.mm-survey-page').each(function() {

      var item;

      item = $(this);

      if( $(item).hasClass('active') ) {
        x = item.data('page');
      }
      else {
        
      }

      return x;

    });

  }

  function getButtons() {
    if(current === 0) {
      current = y;
    }
    if(current === count) {
      $('.mm-next-btn').hide();
      $('.mm-finish-btn').show();
    }
    else {
      $('.mm-next-btn').show();
    }

  }

  $('.mm-survey-q li input').each(function() {
    var item;
    item = $(this);
    $(item).on('click', function() {
      if( $('input:checked').length > 0 ) {
          $('label').parent().removeClass('active');
          item.closest( 'li' ).addClass('active');
      }
      else {
        //
      }
    });

  });

  percent = (x/count) * 100;
  $('.mm-survey-progress-bar').css({ 'width' : percent+'%' });

  function checkStatus() {
    $('.mm-survery-content .mm-survey-item').on('click', function() {
      var item;
      item = $(this);
      item.closest('.mm-survey-page').addClass('pass');
    });
  }

  function buildStatus() {
    $('.mm-survery-content .mm-survey-item').on('click', function() {
      var item;
      item = $(this);
      item.addClass('bingo');
      item.closest('.mm-survey-page').addClass('pass');
      $('.mm-survey-container').addClass('good');
    });
  }

  function deliverStatus() {
    $('.mm-survey-item').on('click', function() {
      if( $('.mm-survey-container').hasClass('good') ){
        $('.mm-survey').addClass('okay');
      }
      else {
        $('.mm-survey').removeClass('okay');  
      }
      buttonConfig();
    });
  }

  function lastPage() {
    if( $('.mm-next-btn').hasClass('cool') ) {
      alert('cool');
    }
  }

  function buttonConfig() {
    if( $('.mm-survey').hasClass('okay') ) {
      if (count == 1) {
        $('.mm-next-btn').hide();
        $('.mm-next-btn button').prop('disabled', false);
        $('.mm-finish-btn').show();
      }else{
        $('.mm-next-btn button').prop('disabled', false);
      }
    }
    else {
        $('.mm-next-btn button').prop('disabled', true);
        $('.mm-next-btn').hide();
    }
  }

  $("#submitSurveyForm").click(function(){
    var form = "";
    var category = "";
    var oldcategory = "";
    $(".mm-survey-page .survey.active").each(function( index ) {
      var value = "";
      category = $(this).attr("category");
      description = $(this).attr("description");
      if (category != oldcategory) {
        answer += "~!~" + category + "#$#"+ description + "#$#";
      }
      if ($(this).attr("item") == "YN" && $(this).hasClass("active")) value = $(this).attr("value");
      else value = $(this).val();
      type = $(this).attr("item");
      question = $(this).attr("question");
      answer += "/"+type+"*"+value+"*"+question;

      oldcategory = category;
    });

    $("#surveyAnswer").val(answer);
    $("#survey").hide();
    getImage();
  });

  function getImage(){
    $("#webCapturing").show();
    $("#webCapturing").addClass("animated fadeIn delay-1s");
    Webcam.attach( '#my_camera' );
    $("#my_camera").css("width","100%");
    $("video").css("width","100%");
    $("video").css("height","unset");
    $("#my_camera").css("height","unset");
    $("#userPhotoDiv").hide();
    $("#userContentDiv").css("width","100%");
  }

  function getSurvey(){
    $("#survey").show();
    $("#survey").addClass("animated fadeIn delay-1s");
  }

  $("#pasteventClicker").click(function(){
       if ($("#month").val() =="" && $("#year").val() == "") {
         $("#currenthistory").show();
         $("#pasteventhistory").hide();
       }
       else
       {
        displayAnnouncementHoliday($("#month").val(),$("#year").val());
        $("#currenthistory").hide();
         $("#pasteventhistory").show();
       }
    });

</script>
<?}?>