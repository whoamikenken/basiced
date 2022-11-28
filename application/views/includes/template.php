<?
/**
 * @author Justin
 * @copyright 2016
 */

$CI =& get_instance();

$data['title'] = $title;
$data['autoload'] = $autoload;
$this->load->view('includes/header',$data);
$total = "";

if($this->session->sess_read() && $this->session->userdata("logged_in")){
$userid = $this->session->userdata("userid");
// $this->menus->login_trail($userid);

// $user = $this->session->userdata("userid");
$username = $this->session->userdata("username");
$utype = $this->session->userdata('usertype');
if(isset($startdate)){
  echo '<p id="startdate" style="display:none;">'.$startdate.'</p>';
  echo '<p id="enddate" style="display:none;">'.$enddate.'</p>';
}
?>
<style>
@import "https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700";
body {
    font-family: avenir;
    background: #fafafa;
}

.tooltip-inner {
    font-family: avenir!important;
}

p {
    font-family: avenir;
    font-size: 1.1em;
    font-weight: 300;
    line-height: 1.7em;
    color: black;
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

#sidebar ul li.active>a,
a[aria-expanded="true"] {
    color: #090909 !important;
    background: #0072c6 !important;
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

</style>
<div class="panel">
    <div class="panel-heading">
        <header class="dark_grey"> <!-- Header start -->
            <a href="<?=base_url()?>" class="logo_imageschool modules"><span class="hidden-480">&nbsp;&nbsp; PTI <!--<span style="font-weight: bold;">Main Menu</span>--></span></a>
            <ul class="header_actions pull-left">
                <li data-toggle="tooltip" data-placement="bottom" title="Hide/Show main navigation" ><a href="#" id="sidebarCollapse"><i class="glyphicon glyphicon-chevron-left"></i></a></li>
            </ul>
            <?php if($utype == "ADMIN"): 
                ?>
                <ul class="header_actions pull-left dark_grey" style="padding-inline-start: 0px;">
                    <li><a href="#" id="load_admin_dashboard"> <span style="font-weight: bold;">DASHBOARD</span></a></li>
                </ul>
            <?php endif ?>
            <ul class="header_actions pull-left dark_grey hidden-768 hidden-480" style="padding-inline-start: 0px;">
                <li><a href="#"> <span style="font-weight: bold;">Welcome, <?=ucwords(strtolower($this->session->userdata("fullname")))?></span></a></li>
            </ul>
            <!-- <ul class="header_actions">
                <li><a href="#" name='signout' site="<?=site_url('main/signout')?>"><i class="glyphicon glyphicon-log-out"></i> <span class="hidden-768 hidden-480">Logout</span></a></li>
            </ul>
            <ul class="header_actions">
                <li style="<?=($utype == "ADMIN") ? '' : 'display:none;'?>"><a href="#" id="manage_account"><i class="icon-wrench"></i>&nbsp;<span>Change Password</span></a></li>
                <li><a href="<?=base_url()?>" class="logo_image hidden-768 hidden-480"><span>&nbsp&nbsp&nbsp&nbsp&nbspHYPERION</span></a></li>
            </ul>
            <?
            if($utype == "EMPLOYEE"){
            ?>
            <ul class="header_actions" id='changePasswordDiv'>
                <li><a href="#" id="cpassmod" site="employeemod/changepass" root='' menuid='72'><i class="icon-wrench"></i> <span class="hidden-768 hidden-480">Change Password</span></a></li>
                <li class="responsive_menu"><a class="iconic" href="#"><i class="icon-reorder"></i></a></li>
            </ul>
            <?}?> -->
            <ul class="header_actions">
                <div class="dropdown">
                    <a class="btn dropdown-toggle-user drop_logo" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        HYPERION <?=($utype == "ADMIN") ? 'ADMIN PORTAL' : 'EMPLOYEE PORTAL'?>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" id="hovered" style="margin-left: 25px;width: 216px; background-color: #313131 !important ">
                        <li class="drop-li" style="<?=($utype == "ADMIN") ? '' : 'display:none;'?>"><a class="dropdownHigh" href="#" id="manage_account"><span class="glyphicon glyphicon-wrench dropLogo"></span>&emsp;<span><b>Change Password</b></span></a></li>
                        <li style="<?=($utype != "ADMIN") ? '' : 'display:none;'?>"><a href="#" class="dropdownHigh" id="cpassmod" site="employeemod/changepass" root='' menuid='72' ><span class="glyphicon glyphicon-wrench dropLogo"></span>&emsp;<span><b>Change Password</b></span></a></li>
                        <div class="cpass"></div>
                        <li><a class="dropdownHigh" href="#" name='signout' site="<?=site_url('main/signout')?>" ><span class="glyphicon glyphicon-log-out dropLogo"></span>&emsp;<span><b>Sign Out</b></span></a></li>
                    </ul>
                </div>
            </ul>
        </header>
    </div>
</div>

<nav id="sidebar"> <!-- Main navigation start -->
    <ul class="list-unstyled components">
<?
if($utype != "EMPLOYEE"){
    // echo "<pre>"; print_r($this->menus->loadmenus("",$userid,$utype)); die;
    foreach($this->menus->loadmenus("",$userid,$utype) as $mmenus){
          list($menuid,$root,$link,$title,$status,$arranged,$icon,$comments) = $mmenus;
          $color = ($menuid == 60 ? '#7CB342' : ($menuid == 2 ? '#42A5F5' : ($menuid == 1 ? '#FFF176' : ($menuid == 50 ? '#FFB74D' : ($menuid == 27 ? '#EA80FC' : '') ) ) ) );

          ?>
            <li>
                <a <?=($rootid==$menuid ? " id='current'" : "")?> href="#<?=$arranged?>" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle"><i class="<?=$icon?>" style="color: <?=$color?>;"></i> <?=$title?></a>
               <ul class="collapse list-unstyled" id="<?=$arranged?>">
          <?
                foreach($this->menus->loadmenus($menuid,$userid,$utype) as $submenus){
                   list($menuid,$root,$link,$title,$status,$arranged,$icon,$comments) = $submenus;
                    if($menuid == "44"){
                       $acc = $this->extras->showAccessmsg($userid);
                       if($acc == true) $total = $this->user->showMsg($userid);
                    }
                    else $total = "";

                  if($menuid == 150 || $menuid == 151 || $menuid == 152) $link = "includes/ams_bundy_clock";
          ?>
              <?if($menuid == "100")
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
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules animated infinite pulse delay-3s" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?><div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="color:red;font-weight: bold;"></i></div>
                  <?
                        }
                        else{
                            ?>
                            <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?>
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
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules animated infinite pulse delay-3s" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i>&nbsp;<?=$title.$total?><div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="color:red;font-weight: bold;"></i></div>
                  <?
                        }
                        else{
                            ?>
                            <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?>
                            <?
                        }
            }
            

            else if($menuid == "183")
            {
                  // EMPLOYEE DEFICIENCY NOTI
                  $CI->load->model('seminar');
                  $where_clause = " AND isread = '0' ";
                  $attendees_notif = $CI->seminar->seminarAttendeesList($where_clause);
                  $attendees_notif = count($attendees_notif);

                  if($attendees_notif != 0){

                    ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules animated infinite pulse delay-3s" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?><div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="color:red;font-weight: bold;"></i></div>
                  <?
                        }
                        else{
                            ?>
                            <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?>
                            <?
                        }
            }
                else if($menuid == "101")
                {
                    // DISCIPLINARY ACTION
                $CI->load->model('disciplinary_action');
                $excessiveTardinessCount = $excessiveAbsenteismCount = false;
                $excessiveTardinessCount = $CI->disciplinary_action->empWithExcessiveTardiness(true,'',false,date('Y'));
                if(!$excessiveTardinessCount) $excessiveAbsenteismCount = $CI->disciplinary_action->empWithExcessiveAbsenteism(true,'',false,date('Y'));

                if($excessiveTardinessCount == true || $excessiveAbsenteismCount == true)
                {
                    ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules animated infinite pulse delay-3s" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?><div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="color:red;font-weight: bold;"></i></div>
                    <?
                        }
                        else{
                            ?>
                            <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?>
                            <?
                        }
                }else if($menuid == "181")
                {
                    // DATA REQUEST APPROVAL
                $CI->load->model('approval');
                // $dataRequestPending = $CI->approval->ifHasPendingRequest();
                $dataRequestPending = 0;
                if($dataRequestPending > 0)
                {
                    ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules animated infinite pulse delay-3s" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?><div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="color:white;font-weight: bold;"></i><span class="notifcount" style="color:white;font-weight: bold;"><?=$dataRequestPending ?></span></div>
                    <?
                    }
                    else{
                        ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i><?=$title.$total?>
                        <?
                    }
                    
                }else if($menuid == "157")
                {
                $endorsedApplicant = 0;
                  $endorsedApplicant = $this->employeemod->endorsedApplicant()->num_rows();
                  if($endorsedApplicant > 0){
                    ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules animated infinite pulse delay-3s" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?><div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="color:white;font-weight: bold;"></i><span class="notifcount" style="color:white;font-weight: bold;"><?=$endorsedApplicant ?></span></div>
                    <?
                    }
                    else{
                        ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i><?=$title.$total?>
                        <?
                    }
                }
                else if($menuid == "47"){
                    $CI->load->model('documents');
                    $pendingDocReq = $CI->documents->ifHasPendingRequest();
                    if($pendingDocReq > 0)
                    {
                    ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules animated infinite pulse delay-3s" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?><div class="notifdiv bell"><i class="glyphicon glyphicon-bell large" style="color:white;font-weight: bold;"></i><span class="notifcount" style="color:white;font-weight: bold;"><?=$pendingDocReq ?></span></div>
                    <?
                    }
                    else{
                        ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i><?=$title.$total?>
                        <?
                    }
                }
                else if($menuid == "184"){
                    $CI->load->model('expiration');
                    $prcExpiration_notif = $CI->expiration->prcExpiryData()->num_rows();
                    if($prcExpiration_notif > 0){ ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules animated infinite pulse delay-3s" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title?><div class='notifdiv bell'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b><?= $prcExpiration_notif ?></b></span></div>
                        <?
                        }
                    else{
                        ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?>
                        <?
                    }
                }else if($menuid == "203"){
                    $CI->load->model('retirement');
                    $retirementCount = $CI->retirement->employeeRetiree('','','','1')->num_rows();
                    if($retirementCount > 0){ ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules animated infinite pulse delay-3s" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title?><div class='notifdiv bell'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b><?= $retirementCount ?></b></span></div>
                        <?
                        }
                    else{
                        ?>
                        <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?>
                        <?
                    }
                }
                else{
                    ?>
                    <li<?=($menuid_selected==$menuid ? " class='active'" : "")?>><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>'> &nbsp;&nbsp;<i class="glyphicon glyphicon-unchecked"></i> &nbsp;<?=$title.$total?>
                    <?
                }
              ?>
              </a></li>
    <?
          }
    ?>
        </ul>
      </li>

    <?
    }
    ?>
    </ul>
    <?
}else{
    foreach($this->menus->loadempmenus("",$userid,$utype) as $mmenus){
        $ishidden = "";
        $checks = '';
        list($menuid,$root,$link,$title,$status,$arranged,$icon,$comments) = $mmenus;
        $color = ($menuid == 63 ? '#42A5F5' : ($menuid == 64 ? '#7DB443' : ($menuid == 85 ? '#7db443' : ($menuid == 50 ? '#FFB74D' : ($menuid == 119 ? '#ff0000' : ($menuid == 65 ? '#ea80fc' : ($menuid == 78 ? '#6e00ed' :($menuid == 103 ? '#00e9ff' : ($menuid == 172 ? '#5bc0de' : ($menuid == 178 ? '#ff9b1f' : ($menuid == 179 ? '#93f9f4' : ($menuid == 180 ? '#3c763d' : ($menuid == 185 ? '#00ffa1' : ($menuid == 203 ? '#b8cc00' : ($menuid == 204 ? '#f57e7e' : '')))) ) ) ) ) ) ) ) ) ) ) );

        // if($menuid == 178){
        //     if(in_array($username,$this->employee->getDeptHead($username)) || in_array($username, array($this->employee->getDeptHead($username,true))) || $this->employee->campus_principal($username)) $title = "Manage Clearance";
        //     else $title = "My Clearance";
        // }

        if($link){
            if(in_array($menuid,array(85))){
                #if($this->session->userdata("username") == $this->employee->getDeptHead($this->session->userdata("username")) || $this->session->userdata("username") == $this->employee->getDeptHead($this->session->userdata("username")))
                if(in_array($username,$this->employee->getDeptHead($username)) || in_array($username, array($this->employee->getDeptHead($username,true))) || $this->employee->campus_principal($username))
                    $ishidden = "";
                else
                    $ishidden = " display: none;";
            }else                $ishidden = "";

            if($menuid == 172 || $menuid == 78 || $menuid == 203 || $menuid == 204){
                // $checks = $this->employee->checkIfOfficeHead($username);
                if(in_array($username,$this->employee->getDeptHead($username)) || in_array($username, array($this->employee->getDeptHead($username,true))) || $this->employee->campus_principal($username)) $ishidden = "";
                else $ishidden = " display:none;";
            }

            if($menuid == 180) $ishidden = " display:none;";

            if($menuid == 185){
                if(in_array($username,$this->employee->getDeptHeadOnly($username)) || in_array($username, array($this->employee->getDeptHeadOnly($username,true))) || $this->employee->campus_principal($username)) $ishidden = "";
                else $ishidden = " display:none;";
            }


        ?>
          <li class="<?=($menuid_selected==$menuid ? " active" : "")?>">
            <a href="#" data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'"  site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>' style="background: #1f1f1f;color: #f9f9f9;border: 0px solid #dedede;<?=$ishidden?>">
            <i class="<?=$icon?>"  style="color: <?=$color?>;"></i>&nbsp;<?=$title?>
                <?if($menuid == 119){
                    // FOR DISCIPLINARY ACTION NOTICE
                    $CI->load->model('disciplinary_action');
                    $disciplinary_action = $CI->disciplinary_action->getOffenseHistory($username,"NO")->num_rows();
                    $danotif         = ($disciplinary_action > 0 ? " <div class='notifdiv bell'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$disciplinary_action."</b></span></div>" : "");
                    if($disciplinary_action > 0)
                    {
                        echo $danotif;
                    }
                }else if($menuid == 172){
                    $applicantlistnotif = $this->employeemod->applicantListNotif($username);
                    $appnotif =($applicantlistnotif > 0 ? " <div class='notifdiv bell'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$applicantlistnotif."</b></span></div>" : "");

                    if($applicantlistnotif > 0)
                    {
                        echo $appnotif;
                    }
                }else if($menuid == 180){
                    $CI->load->model('seminar');
                    $office_under = $this->extensions->getAllOfficeUnder($username);
                    $office_under = "'".implode("','", $office_under). "'";
                    $where_clause = " AND office IN ($office_under) AND isgoing = '0'";
                    $attendees_notif = $CI->seminar->seminarAttendeesList($where_clause);
                    $attendees_notif = count($attendees_notif);
                    $seminar_appnotif = ($attendees_notif > 0 ? " <div class='notifdiv bell'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$attendees_notif."</b></span></div>" : "");
                    if($attendees_notif > 0)
                    {
                        echo $seminar_appnotif;
                    }
                }

                else if($menuid == 178){
                    $empDefcount = 0;
                    $CI->load->model('deficiency');
                    // $deptid = $this->extras->getHeadOffice($username);
                    // if(in_array($username,$this->employee->getDeptHead($username)) || in_array($username, array($this->employee->getDeptHead($username,true))) || $this->employee->campus_principal($username)){
                    //     if ($ishidden != "") {
                    //          $empDefcount = $this->employeemod->employeedeficiencynotif('','',$deptid,'',true)->num_rows();
                            
                    //     }else{
                    //         $empDefcount = $this->employeemod->employeedeficiencynotif('','',$deptid,'Incomplete',true)->num_rows();
                    //     }
                    //     $empDefcount = $this->employeemod->employeedeficiencynotif('','',$deptid,'',true)->num_rows();
                    // }else{
                    //     $empDefcount = $this->employeemod->employeedeficiencynotif('','',$deptid,'',true, true)->num_rows();
                    //     // echo "<pre>";print_r($this->db->last_query());die;

                    // }

                    $deficiencies = $CI->deficiency->getDeficiencyHistory($username,"0")->num_rows();
                    $defnotif         = ($deficiencies > 0 ? " <div class='notifdiv bell'><i class='glyphicon glyphicon-bell large' ></i><span class='notifcount'><b>".$deficiencies."</b></span></div>" : "");
                    if($deficiencies > 0)
                    {
                        echo $defnotif;
                    }

                        
                    // $deficiencies = $CI->deficiency->getDeficiencyHistory($username,"0")->num_rows();
                    // $defnotif         = ($deficiencies > 0 ? " <div class='notifdiv bell'><i class='glyphicon glyphicon-bell large' ></i><span class='notifcount'><b>".$deficiencies."</b></span></div>" : "");
                    // $defnotif2  = ($empDefcount > 0 ? " <div class='notifdiv bell'><i class='glyphicon glyphicon-bell large' ></i><span class='notifcount'><b>".$empDefcount."</b></span></div>" : "");
                    // if($empDefcount > 0)
                    // {
                    //     echo $defnotif2;
                    // }else{
                    //     echo $defnotif;
                    // }
                }

                // else if($menuid == 203){
                //     $empDefcount = 0;
                //     $CI->load->model('deficiency');
                //     $deptid = $this->extras->getHeadOffice($username);
                //     $empDefcount = $this->employeemod->employeedeficiencynotif('','',$deptid,'',true, true)->num_rows();
                //      $deficiencies = $CI->deficiency->getDeficiencyHistory($username,"0")->num_rows();
                //     $defnotif         = ($deficiencies > 0 ? " <div class='notifdiv bell'><i class='glyphicon glyphicon-bell large' ></i><span class='notifcount'><b>".$deficiencies."</b></span></div>" : "");
                //     $defnotif2  = ($empDefcount > 0 ? " <div class='notifdiv bell'><i class='glyphicon glyphicon-bell large' ></i><span class='notifcount'><b>".$empDefcount."</b></span></div>" : "");
                //     if($empDefcount > 0)
                //     {
                //         echo $defnotif2;
                //     }else{
                //         echo $defnotif;
                //     }
                // }

                if($menuid == 179){
                    $CI->load->model('documents');
                    $documents = $CI->documents->countDocumentApplication($username)->num_rows();
                    // echo "<pre>";print_r($this->db->last_query());die;
                    $docnotif         = ($documents > 0 ? " <div class='notifdiv bell'><i class='glyphicon glyphicon-bell large' ></i><span class='notifcount'><b>".$documents."</b></span></div>" : "");
                    if($documents > 0)
                    {
                        echo $docnotif;
                    }
                }else if($menuid == 203){
                    $CI->load->model('deficiency');
                    $incomplete = $CI->deficiency->countIncompleteClearance($username)->num_rows();
                    $incompletenotif         = ($incomplete > 0 ? " <div class='notifdiv bell'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$incomplete."</b></span></div>": "");
                    if($incomplete > 0)
                    {
                        echo $incompletenotif;
                    }
                }
            ?>
            </a>
          </li>
        <?
        }else{
            $CI =& get_instance();
            $CI->load->model('utils');

            $lnotifcount    = $CI->utils->getNotif('leave_app_emplist');
            $lnotif         = ($lnotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$lnotifcount."</b></span></div>" : "");
            $mlnotifcount    = $CI->utils->getNotifManageLEAVE();
            $mlnotif         = ($mlnotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$mlnotifcount."</b></span></div>" : "");

            $snotifcount    = $this->employeemod->seminarnotif()->num_rows();
            $snotif         = ($snotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$snotifcount."</b></span></div>" : "");

            $msnotifcount    = $CI->utils->getNotifManageSeminar();
            $msnotif         = ($msnotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$msnotifcount."</b></span></div>" : "");

            $offbusnotifcount = $CI->utils->getNotifOB('DIRECT');
            $offbusnotif      = ($offbusnotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$offbusnotifcount."</b></span></div>" : "");
            $moffbusnotifcount = $CI->utils->getNotifManageOB('DIRECT');
            $moffbusnotif      = ($moffbusnotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$moffbusnotifcount."</b></span></div>" : "");

            $cornotifcount    = $CI->utils->getNotifOB('CORRECTION');
            $cornotif      = ($cornotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$cornotifcount."</b></span></div>" : "");
            $mcornotifcount = $CI->utils->getNotifManageOB('CORRECTION');
            $mcornotif      = ($mcornotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$mcornotifcount."</b></span></div>" : "");

            $onotifcount    = $CI->utils->getNotif('ot_app_emplist');
            $onotif         = ($onotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$onotifcount."</b></span></div>" : "");
            $monotifcount       = $CI->utils->getNotifManage('ot_app','ot_app_emplist','OT');
            $monotifcount       += $CI->utils->getNotifManage('ot_app','ot_app_emplist','OTNON');
            // $monotifcount       += $CI->utils->getNotifManage('ot_app','ot_app_emplist','OTHEAD');
            // $monotifcount       += $CI->utils->getNotifManage('ot_app','ot_app_emplist','OTHEADNON');
            // die;
            $monotif        = ($monotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$monotifcount."</b></span></div>" : "");

            $cschednotifcount   = $CI->utils->getNotif('change_sched_app_emplist');
            $cschednotif        = ($cschednotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$cschednotifcount."</b></span></div>" : "");
            $mcschednotifcount  = $CI->utils->getNotifManage('change_sched_app','change_sched_app_emplist','ECS','schedule');
            $mcschednotifcount  += $CI->utils->getNotifManage('change_sched_app','change_sched_app_emplist','ECSNON','schedule');
            $mcschednotifcount  += $CI->utils->getNotifManage('change_sched_app','change_sched_app_emplist','ECSHEAD','schedule');
            $mcschednotifcount  += $CI->utils->getNotifManage('change_sched_app','change_sched_app_emplist','ECSNONHEAD','schedule');

            $mcschednotif       = ($mcschednotifcount > 0 ? " <div style='font-size:10px' class='notifdiv'><i class='glyphicon glyphicon-bell'></i><span class='notifcount'><b>".$mcschednotifcount."</b></span></div>" : "");

                  $scnotifcount   = $CI->utils->getNotif('sc_app_emplist') + $CI->utils->getNotif('sc_app_use_emplist') ;
            $scnotif        = ($scnotifcount > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$scnotifcount."</b></span></div>" : "");
            $mscnotifcount  = $CI->utils->getSCManageNotif("SC");
            $mscnotifcount += $CI->utils->getSCManageNotif("SCUse");
            /*$mscnotifcount  = $CI->utils->getNotifManage('sc_app','sc_app_emplist','SC','service_credit') + $CI->utils->getNotifManage('sc_app_use','sc_app_use_emplist','SC','service_credit');
            $mscnotifcount   += $CI->utils->getNotifManage('sc_app','sc_app_emplist','SCNON','service_credit');
            $mscnotifcount   += $CI->utils->getNotifManage('sc_app','sc_app_emplist','SCHEAD','service_credit');*/ //+ $CI->utils->getNotifManage('sc_app_use','sc_app_use_emplist','SCNON','service_credit')
            $mscnotif       = ($mscnotifcount > 0 ? "<div style='font-size:10px' class='notifdiv'></span><i class='glyphicon glyphicon-bell'></i> <span class='notifcount'>".$mscnotifcount."</span></i></div>" : "");

           // if(in_array($menuid,array(65))){
            //   $mainnotif = $myrequestnofif;
            // }else $mainnotif = '';

            $myrequestnofif = $managerequestnotif = '';

            if($menuid == 65){
                $myrequestcount     = $lnotifcount + $snotifcount + $offbusnotifcount + $onotifcount + $cschednotifcount /* + $scnotifcount */  + $cornotifcount;
                $myrequestnofif     = ($myrequestcount > 0 ? " <span class='notifdiv bell'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$myrequestcount."</b></span></span>" : "");
            }
            if($menuid == 78){
                $managerequestcount     = $mlnotifcount + $msnotifcount + $moffbusnotifcount + $monotifcount + $mcschednotifcount /* + $mscnotifcount */ + $mcornotifcount;
                $managerequestnotif     = ($managerequestcount > 0) ? " <span class='notifdiv bell'><i class='glyphicon glyphicon-bell large'></i><span class='notifcount'><b>".$managerequestcount."</b></span></span>" : "";
            }

            if(in_array($menuid,array(78))){
                // $username = '1990-06-001';
                if(in_array($username,$this->employee->getDeptHead($username)) || in_array($username, array($this->employee->getDeptHead($username,true))) || $this->employee->campus_principal($username)) $ishidden = "";
                else $ishidden = " display:none;";
                    
            }else $ishidden = "";


            ?>
            <li>
                <a <?=($rootid==$menuid ? " id='current'" : "")?> href="#<?=$arranged?>"  menuid="<?= $menuid ?>" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle" style='<?=$ishidden?>'  ><i class="<?=$icon?>" style="color: <?=$color?>;"></i> <?=$title.$myrequestnofif.$managerequestnotif?></a>
                <ul class="collapse list-unstyled" id="<?=$arranged?>">
                  <?php
                foreach($this->menus->loadempmenus($menuid,$userid,$utype) as $submenus){
                    list($menuid,$root,$link,$title,$status,$arranged,$icon,$comments) = $submenus;
                    // if($menuid == 178){
                    //     if(in_array($username,$this->employee->getDeptHead($username)) || in_array($username, array($this->employee->getDeptHead($username,true))) || $this->employee->campus_principal($username)) $title = "Manage Clearance";
                    //     else $title = "My Clearance";
                    // }
                    $notif = ($menuid == 119 ? $danotif : ($menuid == 66 ? $lnotif : ($menuid == 67 ? $offbusnotif : ($menuid == 68 ? $snotif : ($menuid == 70 ? $onotif : ($menuid == 121 ? $scnotif : ($menuid == 73 ? $mlnotif : ($menuid == 74 ? $msnotif : ($menuid == 75 ? $monotif : ($menuid == 80 ? $moffbusnotif : ($menuid == 97 ? $mcschednotif : ($menuid == 98 ? $cschednotif : ($menuid == 122 ? $mscnotif : ($menuid == 132 ? $cornotif : ( $menuid == 137 ? $mcornotif : ( $menuid == 179 ? $docnotif : ( $menuid == 203 ? $incompletenotif : "" ) ))))))))))))))));
                ?>
                    <li <?=($menuid_selected==$menuid ? "class='active'" : "")?> ><a data-toggle="tooltip" data-container="body" class="modules" data-animation="true" data-placement="right" title="'<?=$comments?>'" href="#" site='<?=$link?>' root='<?=$root?>' menuid='<?=$menuid?>' <?= $menuid == 121 ? 'style="display: none"' : ''  ?>> &nbsp;&nbsp;<i class="icon-circle-blank"></i> &nbsp;<?=$title.$notif?>

                    <?if($menuid == 119){
                    // FOR DISCIPLINARY ACTION NOTICE
                    $CI->load->model('disciplinary_action');
                    $disciplinary_action = $CI->disciplinary_action->getOffenseHistory($username,"NO")->num_rows();
                    $danotif         = ($disciplinary_action > 0 ? " <div class='notifdiv'><i class='glyphicon glyphicon-bell large' style='color: #FFB74D;'></i><span class='notifcount'><b>".$disciplinary_action."</b></span></div>" : "");
                    if($disciplinary_action > 0)
                    {
                        echo $danotif;
                    }
                }?></a></li><?
            }
        ?>
                </ul>
            </li>
        <?
        }
    }
    ?>
    </ul>
    <?
}
?>
</nav>
 <!-- Absent Message Modal -->
  <div class="modal fade" id="ABSENTMESSAGEmodal" role="dialog">
    <div class="modal-dialog">

     <div class="modal-content" style="padding:2%;">
        <div class="modal-header">
          <h4 class="modal-title" style="font-family: avenir;">Hello! <b><?= $this->session->userdata("fullname")?></b></h4>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn default viewMyMessage" style="float:left;border: 2px solid red;
    border-radius: 10px;"><i class="icon-next"></i> <span class="hidden-768 hidden-480">See my Attendance</span></a></button>

          <button type="button" style="border-radius:10px;" data-dismiss="modal" class="btn blue closeABSENTMESSAGEmodal">Cancel</button>
        </div>
      </div>

    </div>
  </div>

  <div class="modal fade" id="DRAModal" role="dialog">
    <div class="modal-dialog modal-lg">

     <div class="modal-content" style="padding:2%;">
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
        <center><b><h3 tag="title" class="modal-title" style="font-family: Avenir;">Admin Remarks for Pending Data</h3></b></center>
    </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" style="border-radius:10px;" data-dismiss="modal" class="btn btn-danger closeDRAModal">Close</button>
        </div>
      </div>

    </div>
  </div>

<div class="modal fade" data-backdrop="static" data-keyboard="false" id="seminar_attendees" role="static"></div>

<div class="modal fade" id="continueSession" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body" style="margin-bottom: 0px !important; padding: 15px !important;">
               <center><p>Do you want to continue your transaction? <button class="btn btn-primary" id="continue_session">Yes</button>&nbsp;<span class="btn btn-danger" id="logout">No</span></p></center>
            </div>
        </div>
    </div>
</div>
  
<input type="hidden" id="checkUserid" value="<?= $userid ?>">
<input type="hidden" id="user_type" value="<?= $utype ?>">
<?
}
?>
<?=form_open_multipart(base_url(),"id='mainform'");?>
<?=form_hidden("sitename",$content,"")?>
<?=form_hidden("rootid",$rootid,"")?>
<?=form_hidden("menuid",$menuid_selected,"")?>
<?=form_hidden("titlebar","","")?>
<?=form_close();?>

<?php
$this->load->view($content,$data);
?>
<script>
$('.modules').click(function(){
    $('#content').addClass("animated fadeOut");
});

$("#logout").click(function(){
     location.href = "<?=base_url()?>";
});


$(document).ready(function(){
    addRemainingSession(1);
    $('#current').click();
    $("#sidebar").mCustomScrollbar({
        theme: "inset-3"
    });

    $('[data-toggle="tooltip"]').tooltip()

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar, #content').toggleClass('active');
        $('.collapse.in').toggleClass('in');
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
        $("i", this).toggleClass("glyphicon glyphicon-chevron-left glyphicon glyphicon-chevron-right");
    });
    // console.log(sessionStorage.userLoggedin);
    // console.log(localStorage.userLoggedin);
    /*if(sessionStorage.userLoggedin != 'PovedaPinnacle'){
        if( localStorage.userLoggedin != 'YES'){
            if($("#checkUserid").val()){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: "Your session has been expired. You will be redirected to login page.",
                        showConfirmButton: true,
                        timer: 1000
                    })
            }
            $("a[name='signout']").click();
        }else{
            sessionStorage.setItem('userLoggedin','PovedaPinnacle');
        }
    }else{
        localStorage.setItem('userLoggedin', 'YES');
    }*/
    $("a[name='signout']").click(function(){
        sessionStorage.clear();
        localStorage.clear();
    })
    // console.log(localStorage.userLoggedin);
    window.onunload = () => {
        localStorage.clear();
    }
});

$("#manage_account").click(function(){
    $("#mainform").attr("action","<?=site_url("main/site")?>");
    $("input[name='sitename']").val("employeemod/changepass");
    $("input[name='rootid']").val();
    $("input[name='menuid']").val(72);
    $("input[name='titlebar']").val("Manage Account");

    $("#mainform").submit();
});

$("a[name='signout']").click(function(){
    $.ajax({
        url: $(this).attr("site"),
        type: "POST",
        success: function(msg){  
        // alert("Bye!!");
         //$("body").html(msg);
         window.location.href = msg;
        }
    });
});

$("#sidebar ul a").click(function(){
    //CODE ADDED FOR DISCIPLINARY ACTION
    if("<?=$menuid_selected?>" == 119)
    {
        if($("a[action='tag']").length > 0)
        {
            Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: "Kindly confirm first the unconfirmed Disciplinary Action",
              showConfirmButton: true,
              timer: 1000
            })
            return false;
        }
    }
   var site = $(this).attr("site");
   var root = $(this).attr("root");
   var menuid = $(this).attr("menuid");
   var titlebar = $(this).text();

   $.ajax({
    url: "<?=site_url("utils_/storeCurrentMenu")?>",
    type: 'POST',
    data : {menuid:menuid},
    success: function(msg){
      // console.log(msg);
      $("#mainform").attr("action","<?=site_url("main/site")?>");
      $("input[name='sitename']").val(site);
      $("input[name='rootid']").val(root);
      $("input[name='menuid']").val(menuid);
      $("input[name='titlebar']").val(titlebar);

      if(site) $("#mainform").submit();
    }
   });

});

$("#load_admin_dashboard").click(function(){
    $("#mainform").attr("action","<?=site_url("main/site")?>");
    $("input[name='sitename']").val("includes/dashboard");
    $("input[name='rootid']").val();
    $("input[name='menuid']").val(167);
    $("input[name='titlebar']").val("Dashboard");

    $("#mainform").submit();
});

$("#cpassmod").click(function(){
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
$this->load->view('includes/footer',$data);
?>

<?
  if($this->session->sess_read() && $this->session->userdata("logged_in")){

    ?>
      <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.sessionTimeout.js"></script>
      <script>
        $.sessionTimeout({
          warnAfter: 1200000,
          redirAfter: 1320000,
          logoutUrl: "<?=site_url('main/signout')?>",
          redirUrl : "<?=site_url('main/signout')?>",
          keepAliveUrl : location.href
        });
      </script> -->


    <?

  }


?>

<script>
    if($("#user_type").val() != "ADMIN") seminarAttendees();
    
    $(document).on('click','li,a,button',function(){
        // checkphpsession();

    });
    function checkphpsession(){
        //console.log('asddasdasd');
        $.ajax({
            url: "<?=site_url("main/sessionchecker")?>",
            type: "POST",
            success: function(msg){
                if($(msg).find("result").text()==1){
                    location.href = "<?=base_url()?>";
                }
            }
        });
    }

  <?php if($this->session->userdata('message_box') > 0) {?>
    if("<?=$utype?>" == "EMPLOYEE"){
        /*modal for absent notification message*/
        $( document ).ready(function() {
                var userid = "<?= $this->session->userdata("username")?>";
                $.ajax({
                        type: "POST",
                        url:"<?= site_url('main/checkAbsentNotificationMessage')?>",
                        data: {userid:userid},
                        success:function(response){
                            if(response){
                                $("#ABSENTMESSAGEmodal").modal({backdrop: 'static', keyboard: false});
                                $("#ABSENTMESSAGEmodal").find(".modal-body").html(response);

                            }
                        }
                });

                $.ajax({
                        type: "POST",
                        url:"<?= site_url('approval_/checkAdminRemarks')?>",
                        data: {userid:userid},
                        success:function(response){
                            if(response){
                                $("#DRAModal").modal({backdrop: 'static', keyboard: false});
                                $("#DRAModal").find(".modal-body").html(response);
                                
                            }
                        }
                });
        });
    }
    <?php }?>
    $(".viewMyMessage").click(function(){

        var site = "employeemod/attendance";
        var root = "";
        var menuid = "64";
        var titlebar = "My Attendance";
        var message_box = "message_box";

        $.ajax({
            url: "<?=site_url("utils_/storeCurrentMenu")?>",
            type: 'POST',
            data : {menuid:menuid,message_box:message_box},
            success: function(msg){
            // console.log(msg);
            $("#mainform").attr("action","<?=site_url("main/getTotalUnreadMessages")?>");
            $("input[name='sitename']").val(site);
            $("input[name='rootid']").val(root);
            $("input[name='menuid']").val(menuid);
            $("input[name='titlebar']").val(titlebar);

            if(site) $("#mainform").submit();
            }
        });

    });

    function seminarAttendees(){
        $.ajax({
            url: "<?= site_url('seminar_/seminarAttendees')?>",
            dataType: "json",
            success:function(response){
                if(response.show) $("#seminar_attendees").html(response.content).modal("toggle");
            }
        });
    }

    $("#dropdownMenu1").mouseenter(function(){
        $("#hovered").css("display", "block");
    })
    $("#hovered,.header_actions").mouseleave(function(){
        $("#hovered").css("display", "none");
    }).mouseleave();

    $("#continue_session").click(function(){
        addRemainingSession();
         
    });

    function addRemainingSession(onload=""){
        $.ajax({
            url: "<?=site_url("main/addRemainingSessions")?>",
            type: "POST",
            success: function(msg){ 
                if(onload =="") $("#continueSession").modal("toggle");
            }
        });
    }

    if("<?=$this->session->userdata("logged_in")?>"){
        var checksession = setInterval(function(){ 
            $.ajax({
                url: "<?=site_url("main/checkRemainingSession")?>",
                type: "POST",
                success: function(msg){ 
                    if(msg == 10){
                        $("#continueSession").modal("toggle");
                        clearInterval(checksession);
                        // var dt = new Date();
                        // var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
                        // console.log(time);
                    }
                }
            });
        }, 10000);
    }

</script>
