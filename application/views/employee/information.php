<?php
/**
 * @author Justin
 * @copyright 2016
 */
$datetoday = date("Y-m-d");
$CI =& get_instance();
$CI->load->model('utils');
$CI->load->model('aims');
$hide=$iquery="";
$usertype = $this->session->userdata("usertype");
if ($usertype == "EMPLOYEE") 
    $hide="style='display:none'";
else $hide="";
    $employeeid = $employeecode = $fname = $lname = $mname = $nname = $cityaddr = $provaddr = $regaddr = $addr = $permaMunicipality = $permaRegion = $permaProvince = $permaBarangay = $permaAddress = $permaZipcode = $office = $blood_type = $height = $weight = $gender = $civil_status = $spouse = $bdate = $mobile = $citytelno = $email = $employmentstat = $emptype = $empshift = $dateemployed = $campusid = $maxregular = $maxparttime = $bplace = $deptid = $assignment = $remarks = $position = $management = $dateresigned = $dateresigned2 = $datepos = $resigned_reason = $tinno = $sssno = $philhealth = $pagibig = $peraa = $medicare = $emp_bank = $emp_accno = $citizenship = $religion = $nationality = $prc = $passport = $visa = $icard = $crnno = $permanent_address = $cp_name = $cp_relation = $cp_address = $cp_mobile = $cp_telno  = $teaching = $teachingtype = $accai = $leavetype = $occupation = $mother = $motheroccu = $father = $fatheroccu = $hosp = $hosptxt = $operation = $operationtxt = $operationdate = $medhistory = $medhistorytxt = $medconditions = $age = $date_active = $zip_code= $barangay = $dates=$ages= $aims = $aimsdept= $aimscb = $prc_expiration = $passport_expiration = $emp_hmo = $landline = $personal_email = $rank = $spouse_contact = $status_update_date = $dr_update_date = "";     
    $legitimate_relations = array();
    $employment_history = array();
   
    $applicable_field = $applicable_children = $applicable_emergencyContact = $applicable_skill = "";
 
 if(isset($empinfo))   $empdetails = $empinfo[0];  
 else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0];
 }
 if($empdetails['employeeid']){
    $var = $this->employee->loadallemployee(array("employeeid"=>$empdetails['employeeid']));
    $empdetails = $var[0];
    $this->session->unset_userdata("personalinfo");
    $this->session->set_userdata("personalinfo",$var);
    $employeeid = $empdetails['employeeid'];
    $employeecode = $empdetails['employeecode'];
    $fname = $empdetails['fname'];
    $lname = $empdetails['lname'];
    $mname = $empdetails['mname'];
    $nname = $empdetails['nname'];
    $suffix = $empdetails['suffix'];
    $cityaddr = $empdetails['cityaddr'];
    $provaddr = $empdetails['provaddr'];
    $regaddr = $empdetails['regaddr'];
    $barangay = $empdetails['barangay'];
    $zip_code = $empdetails['zip_code'];
    $addr = $empdetails['addr'];
    $permaProvince = $empdetails['permaProvince'];
    $permaRegion = $empdetails['permaRegion'];
    $permaMunicipality = $empdetails['permaMunicipality'];
    $permaZipcode = $empdetails['permaZipcode'];
    $permaBarangay = $empdetails['permaBarangay'];
    $permaAddress = $empdetails['permaAddress'];
    $occupation = $empdetails['occupation'];
    $age    = $empdetails['age'];
    $gender = $empdetails['gender'];
    $civil_status = $empdetails['civil_status'];
    //NEW DATA ADDED FOR INTEGRATION
    $aimsdept = $empdetails['aimsdept'];
    $aimscb = $empdetails['aimcheckbox'];
    $spouse = $empdetails['spouse_name'];
    $spouse_contact = $empdetails['spouse_contact'];
    $bdate = (isset($empdetails['bdate']) && $empdetails['bdate'] != "-0001-11-30" && $empdetails['bdate'] != "1970-01-01" && $empdetails['bdate'] != "0000-00-00") ? $empdetails['bdate'] : "" ;
    $mobile = $empdetails['mobile'];
    $citytelno = $empdetails['citytelno'];
    $email = $empdetails['email'];
    $personal_email = $empdetails['personal_email'];
    $employmentstat = $empdetails['employmentstat'];
    $emptype = $empdetails['emptype'];
    $empshift = $empdetails['empshift'];
    $date_active = $empdetails['date_active'];
    $dateemployed = isset($empdetails['dateemployed']) && $empdetails['dateemployed'] != "0000-00-00" && $empdetails['dateemployed'] != "1970-01-01" ? date("Y-m-d",strtotime($empdetails['dateemployed'])) : "" ;
    $campusid = $empdetails['campusid'];
    $office = $empdetails['office'];
    $maxregular = $empdetails['maxregular'];
    $maxparttime = $empdetails['maxparttime'];
    $bplace = $empdetails['bplace'];
    $deptid = $empdetails['deptid'];
    $leavetype = $empdetails['leavetype'];
    $position = $empdetails['position'];
    $datepos = (!empty($empdetails['dateposition']) && $empdetails['dateposition'] != "0000-00-00" && $empdetails['dateposition'] != "1970-01-01") ? date("Y-m-d",strtotime($empdetails['dateposition'])) : "";
    $assignment = $empdetails['assignment'];
    $remarks = $empdetails['remarks'];
    $management = $empdetails['management'];
    // $dateresigned = $empdetails['dateresigned'] ? $empdetails['dateresigned'] : '';
    $dateresigned = (!empty($empdetails['dateresigned']) && $empdetails['dateresigned'] != "0000-00-00" && $empdetails['dateresigned'] != "1970-01-01") ? date("Y-m-d",strtotime($empdetails['dateresigned'])) : "";
    // var_dump($dateresigned);
    $dateresigned2 = (isset($empdetails['dateresigned2']) && $empdetails['dateresigned2'] != "-0001-11-30" && $empdetails['dateresigned2'] != "1970-01-01" && $empdetails['dateresigned2'] != "0000-00-00")  ? $empdetails['dateresigned2'] : '';
    $resigned_reason = $empdetails['resigned_reason'];
    $tinno = $empdetails['tinno'];
    $sssno = $empdetails['sssno'];
    $rank = $empdetails['rank'];
    $philhealth = $empdetails['philhealth'];
    $pagibig = $empdetails['pagibig'];
    $peraa = $empdetails['peraa'];
    $medicare = $empdetails['medicare'];
    $emp_bank = $empdetails['emp_bank'];
    $emp_accno = $empdetails['emp_accno'];
    $citizenship = $empdetails['citizenship'];     
    $religion = $empdetails['religion'];
    $nationality = $empdetails['nationality'];
    $prc = $empdetails['prc'];
    $emp_hmo = $empdetails['emp_hmo'];
    $passport = $empdetails['passport'];
    $visa = $empdetails['visa'];
    $icard = $empdetails['icard'];
    $crnno = $empdetails['crn'];
    $permanent_address = $empdetails['permanentaddress'];
    $legitimate_relations = $empdetails['legitimate_relations'];
    $mother = $empdetails['mother'];
    $motheroccu = $empdetails['motheroccu'];
    $father = $empdetails['father'];
    $fatheroccu = $empdetails['fatheroccu'];
    $hosp = $empdetails['hospitalized'];
    $hosptxt = $empdetails['hospitalizedtxt'];
    $operation = $empdetails['operation'];
    $operationtxt = $empdetails['operationtxt'];
    $operationdate = $empdetails['operationdate'];
    $medhistory = $empdetails['medhistory'];
    $medhistorytxt = $empdetails['medhistorytxt'];
    $medconditions = $empdetails['medconditions'];
    $cp_name = $empdetails['cp_name'];
    $cp_relation = $empdetails['cp_relation'];
    $cp_address = $empdetails['cp_address'];
    $cp_mobile = $empdetails['cp_mobile'];
    $cp_telno = $empdetails['cp_telno'];
    // $cp_type = $empdetails['cp_type'];
    $teaching = $empdetails['teaching'];
    $teachingtype = $empdetails['teachingtype'];
    $accai = $empdetails['isactive']; 
    // $status_update_date = $empdetails['status_update_date']; 
    // $dr_update_date = $empdetails['date_resigned_update_date']; 
    $accai = ($dateresigned2 == '' && $accai) ? 1 : ($datetoday < $dateresigned2 && $accai) ? 1 : 0;
    $blood_type = $empdetails['blood_type'];
    $height = $empdetails['height'];
    $weight = $empdetails['weight'];
    $prc_expiration = $empdetails['prc_expiration'];
    $passport_expiration = $empdetails['passport_expiration'];
    $landline = $empdetails['landline'];
    $employment_history = $this->employee->getEmploymentStatusHistory($employeeid);

    $aimscb = $CI->aims->checkIfAlreadyInAims($employeeid);

    // echo "<pre>"; print_r($employment_history); die;
    $applicable_field   = $this->db->query("SELECT * FROM employee_applicable_fields WHERE employeeid='{$empdetails['employeeid']}'");
    if($applicable_field->num_rows > 0) $applicable_children = $applicable_field->row(0)->children;
    if($applicable_field->num_rows > 0) $applicable_emergencyContact = $applicable_field->row(0)->emergencyContact;
    if($applicable_field->num_rows > 0) $applicable_skill   = $applicable_field->row(0)->skill;
 }
 $ishidden = $isdisabled = $isreadonly = "";
 $cansave = true;
 
 if($usertype == "EMPLOYEE"){
   $ishidden   = " hidden";
   $isdisabled = " style='pointer-events: none;'";
   $isreadonly = " style='pointer-events: none;'";
   // $cansave   = $this->db->query("SELECT * FROM employee_restriction WHERE employeeid='$employeeid'")->num_rows();
 }
// $employee_photo = $this->db->query("SELECT * FROM employee_photo where employeeid = '$employeeid'");
// // echo "<pre>"; print_r($this->db->last_query()); die;
// $hasPhoto = $hasElfinderPhoto = 0;
// if($employee_photo->num_rows() > 0){
//     $hasPhoto++;
//     $photo = json_decode(json_encode($employee_photo->result()), true);
// }else{
//   $employee_elfinder_file = $this->db->query("SELECT * FROM elfinder_file a WHERE a.name LIKE '%$employeeid%'")->result();
//     foreach ($employee_elfinder_file as $key => $value) {
//       $hasElfinderPhoto++;
//       $photo = "data:".$value->mime.";base64,".base64_encode($value->content);
//     }
// }

$employee_photo =  $this->db->query("SELECT * FROM elfinder_file a WHERE a.name LIKE '%$employeeid%'");
// echo "<pre>"; print_r($this->db->last_query()); die;
$hasPhoto = 0;
if($employee_photo->num_rows() > 0){
    $hasPhoto++;
    foreach ($employee_photo->result() as $key => $value) {
      $photo = "data:".$value->mime.";base64,".base64_encode($value->content);
    }
}


$iquery  = $this->db->query("SELECT * FROM elfinder_file where title='$employeeid'");
$count = 0;
if ($iquery->num_rows()> 0) {
    $count ++;
   
}

?>
<style>

  @media (min-width: 1200px){
  .container {
        width: 93%;
    }

   .error{
    color:red;
   }
  }
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}

.tooltip {
  position: relative;
  display: inline-block;
  opacity: 1;
  /*border-bottom: 1px dotted black;*/
}

.tooltip .tooltiptext {
  visibility: hidden;

  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  /* Position the tooltip */
  position: absolute;
  z-index: 1;
}
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
  left: 54%;
  bottom: 600px;
  font-size: 17px;
}

#snackbar.show {
  visibility: visible;
  -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
  animation: fadein 0.5s, fadeout 0.5s 2.5s;
}
.tooltip .tooltiptext {
  top: -5px;
  left: 105%;
  padding: 5%;
}
.tooltip .tooltiptext::after {
  content: " ";
  position: absolute;
  top: 50%;
  right: 100%; /* To the left of the tooltip */
  margin-top: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: transparent black transparent transparent;
}
.tooltip:hover .tooltiptext {
  visibility: visible;
}

.swal2-cancel{
    margin-right: 20px;
}

.tricolumn{
  padding-right: 0px;
  padding-left: 0px;
}

.scrollbar{
   overflow: auto;
   margin-bottom: 10px;
}

  .scrollbar::-webkit-scrollbar {
    width: 10px;
    height: 10px;
  }

  /* Track */
  .scrollbar::-webkit-scrollbar-track {
    box-shadow: inset 0 0 0 grey; 
    border-radius: 10px;
  }
   
  /* Handle */
  .scrollbar::-webkit-scrollbar-thumb {
    background: #0072c6;
    border-radius: 10px;
  }

  /* Handle on hover */
  .scrollbar::-webkit-scrollbar-thumb:hover {
    background: #fadd14; 
  }

  .myInput{
    width: 10% !important;
  }

  table.dataTable {
    border-collapse: collapse !important;
  }

</style>
<?$user = $this->session->userdata("userid");?>
<input type="text" name="userid" id="userid" value="<?=$user ?>" hidden>
<input type="hidden" id="permaProvince" value="<?= $permaProvince ?>">
<input type="hidden" id="permaMunicipality" value="<?= $permaMunicipality ?>">
<input type="hidden" id="permaRegion" value="<?= $permaRegion ?>">
<input type="hidden" id="permaBrgy" value="<?= $permaBarangay ?>">
<script src="<?=base_url()?>jsbstrap/library/jquery.inputmask.bundle.js"></script>
<div class="widgets_area animated fadeIn delay-1s" id="personal_info">
    <div class="container" style="width: 100% !important;">
        <form id="info">
            <div class="form_row" style="margin-top: 4%;">
                <table>
                    <tr >
                        <td class="align_right" rowspan="2" width="40%"><img src="<?=base_url()?>images/school_logo.png" style="width: 100px;" /></td>
                        <td class="align_left" valign='bottom' style="padding: 0;"><h4 style="font-size: 23px; font-family: Book Antiqua; margin-left: 10px;"><b><?= $this->extras->school_name()?></b></h4></td>

                    </tr>
                    <tr>
                        <td class="" valign='baseline' ><h3 style="font-size: 17px; margin-top: -1%; margin-left: 1.2%;  font-family: Book Antiqua; "><strong><?= $this->extras->school_desc()?></strong></h3></td>
                    </tr>
                </table>
            </div>
            <?if($cansave){?>
              <div class="row">
                  <div class="col-md-6">
                    <a href="#" class="btn btn-success" name='backlist'  <?=$hide?> >Back to employee list</a>
                  </div>
                  <div class="col-md-6 pull-right">
                    <div class="pull-right">
                    <label class="text-info">
                        Print (To preview employee 201.)
                      <a class="btn btn-info" name="print_out">Print</a>
                    </div>
                  </div>
              </div>
            <?}?>
            <br />
            <div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>GENERAL INFORMATION</b></h4></div>
                <div class="panel-body">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Employee ID:</label>
                                <div class="col-sm-9">
                                    <input class="form-control required" id="employeeid" name="employeeid" type="text" value="<?=$employeeid?>" <?=($employeeid?" readonly":"")?>/>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <!-- <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Add user to AIMS:</label>
                                <div class="col-sm-9">
                                    <?php if (($usertype == "ADMIN") || ($usertype == "SUPER ADMIN")): ?> 
                                    <input type="checkbox" class="control aims" name="aims" id="aims" value="aims" <?=$aimscb == "aims" ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" />
                                    <?php endif ?>
                                    <input class="hidden" name="employeecode" type="text" value="<?=$employeecode?>" />
                                </div>
                            </div >
                        </div>
                        <br><br> -->
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Last Name:</label>
                                <div class="col-sm-9">
                                    <input class="form-control required" name="lname" id="lname" type="text" value="<?=$lname?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($lname == "" || $lname == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">First Name:</label>
                                <div class="col-sm-9">
                                    <input class="form-control required" name="fname" id="fname" type="text" value="<?=$fname?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($mname == "" || $fname == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Middle Name:</label>
                                <div class="col-sm-9">
                                    <input class="form-control required" name="mname" id="mname" type="text" value="<?=$mname?>"
                                    <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($mname == "" || $mname == "-" )) ? "" : $isreadonly?" readonly":"" ?> />
                                </div>
                            </div>
                        </div> 
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Suffix:</label>
                                <div class="col-sm-9">
                                    <input class="form-control required" name="suffix" id="suffix" type="text" value="<?=$suffix?>"
                                    <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($suffix == "" || $suffix == "-" )) ? "" : $isreadonly?" readonly":"" ?> />
                                </div>
                            </div>
                        </div> 
                        <br><br>
                        <div class="form-group"  style=" margin-right: 15px;">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Date Hired:</label>
                                <div class='input-group date col-sm-9' <?=($usertype == "ADMIN") ? "" : "style='display:none;'" ?>>
                                    <input type='text' class="form-control" name="dateemployed" id="dateemployed" value="<?= ($dateemployed != '-0001-11-30') ? $dateemployed :'' ?>"  style="margin-left: 19px;" />
                                    <span class="input-group-addon" id="dateemployed_saveAlert">
                                        <span class="glyphicon glyphicon-calendar" style="margin-left: 18px;"></span>
                                    </span>
                                </div>  
                                <div class='input-group date col-sm-9' style="padding-right: 20px;">
                                    <input class="form-control" name="dateemployed" type="text" value="<?= ($dateemployed != '-0001-11-30') ? $dateemployed :'' ?>" readonly <?=($usertype == "EMPLOYEE") ? "" : "style='display:none;'" ?> style="margin-left: 19px; padding-right: 20px;"/>
                                </div> 
                            </div> 
                        </div>
                        <br><br> 
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Rank:</label>
                                <div class="col-sm-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($rank == "" || $rank == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                               <select class="chosen-select" name="rank" id="rank">
                                    <?
                                        $opt_rank_type = $this->extras->listRankType();
                                        foreach ($opt_rank_type as $key => $ranktype) {?>
                                            <option value="<?=$key?>" <?= ($rank == $key) ? "selected" : ""?> ><?=$ranktype?></option>
                                        <?}
                                    ?>
                              </select>
                            </div>
                            </div>
                        </div>  
                        <br><br>
                        <div class="form-group" hidden>
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Rank:</label>
                                <div class="col-sm-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($rank == "" || $rank == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                               <select class="chosen-select" name="rank" id="rank">
                                    <?
                                        $opt_rank_type = $this->extras->listRank();
                                        foreach ($opt_rank_type as $key => $ranktype) {?>
                                            <option value="<?=$key?>" <?= ($rank == $key) ? "selected" : ""?> ><?=$ranktype?></option>
                                        <?}
                                    ?>
                              </select>
                            </div>
                            </div>
                        </div> 
                        <div class="form-group" hidden>
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Rank Set:</label>
                                <div class="col-sm-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($rank == "" || $rank == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                   <select class="chosen-select" name="rank" id="rank">
                                        <?
                                            $opt_rank_type = $this->extras->listRankSet();
                                            foreach ($opt_rank_type as $key => $ranktype) {?>
                                                <option value="<?=$key?>" <?= ($rank == $key) ? "selected" : ""?> ><?=$ranktype?></option>
                                            <?}
                                        ?>
                                  </select>
                                </div>
                            </div>
                        </div>                                                  
                        <!-- FOR AIMS INTEGRATION -->
                         <div class="form-group aimsdepartment" id="aimsdepartment">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">AIMS Department:</label>
                                <div class="col-sm-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($nationality == "" || $nationality == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                    <select class="chosen-select" name="aimsdept" id="aimsdept">
                                           <?
                                              $aimstype = $this->extras->showAimsDepartment();
                                              foreach($aimstype as $key =>$value){
                                              ?><option <?=($key==$aimsdept ? " selected" : "")?> value="<?=$key?>"><?=$value?></option><?    
                                              }
                                          ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-6">
                      <?php if($usertype == "ADMIN"): ?>
                        <div class="col-xs-12">
                          <div class="col-sm-12 no-search">
                            <table>
                              <tr>
                                <th width="27%">Add user to AIMS:</th>
                                <th>
                                  <?php if (($usertype == "ADMIN") || ($usertype == "SUPER ADMIN")) { ?>
                                    <input type="checkbox" class="control aims" name="aimcheckbox" id="aimcheckbox" value="aimcheckbox" <?=$aimscb == "1" ? " checked disabled" : "" ?> style="-webkit-transform: scale(1.5); padding-left: 0px;margin-top: 2px; " />
                                <? }else{ ?>
                                    <input type="checkbox" class="control aims" name="aimcheckbox" id="aimcheckbox" value="aimcheckbox" <?=$aimscb == "1" ? " checked disabled" : "" ?> style="-webkit-transform: scale(1.5); padding-left: 0px;margin-top: 2px; "/>
                                <? } ?>
                                </th>
                              </tr>
                            </table>
                            <!-- <label  for="employeeid" class="col-xs-4" style="margin-left: -80px;">Add user to AIMS:</label> -->

                                 
                                    <!-- <input class=" aims"name="employeecode" type="checkbox" value="<?=$employeecode?>" <?=$aimscb == "1" ? " checked" : "" ?> style="-webkit-transform: scale(1.5); padding-left: 0px; margin-left: -10px; "/> -->
                            </div>
                      
                        </div >
                      <?php endif; ?>
                       
                        <div class="col-xs-12">
                        <?php if($hasPhoto == 0): ?>
                            <img class="elfinderimg" src="<?php echo base_url()?>images/no_image.gif" style="float: right;width: 180px; height: 180px;"/>
                            <?php else: ?>
                                <img class="elfinderimg" src="<?php echo  $photo; ?>" style="float: right;width: 180px; height: 180px; border: 2px solid #a1a1a1"/>
                            <?php endif ?> 
                        </div>
                        <div class="col-xs-12" id="employeeImage">
                            <?php if(!file_exists("images/employee/".$employeeid.".jpg")): ?>
                                <a href="#modal-view" data-toggle='modal' type="button" class="btn btn-primary uploadPhoto" filename="<?= $employeeid ?>" modalTitle="Upload Photo" style="float: right; width: 180px; margin-top: 10px;">Upload Photo</a>
                            <?php else: ?>
                                <a href="#modal-view" data-toggle='modal' type="button" class="btn btn-primary uploadPhoto" filename="<?= $employeeid ?>" modalTitle="Upload New Photo"  style="float: right; width: 180px; margin-top: 10px;">Upload New Photo</a>
                            <?php endif ?> 
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>IDENTIFICATION NUMBER</b></h4></div>
                <div class="panel-body tricolumn">
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div class="form-group tricolumn">
                            <div class="col-md-12 tricolumn">
                                <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Passport&nbsp;#:</label>
                                <div class="col-xs-12 col-md-9">
                                   <input type="text" class="form-control required passport" name="passport" id="passport" value="<?=$passport?>"
                                   <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($passport == "" || $passport == "-" )) ? "" : $isreadonly?'readonly style="pointer-events: none;"':"" ?>/>
                                   <h6><span id="PASSPORTSpan" class="passport"></span></h6>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group tricolumn">
                            <div class="col-md-12 tricolumn">
                                <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Date&nbsp;of Expiration:</label>
                                <div class="col-xs-12 col-md-9">
                                  <div class='input-group date' id='passport_expiration'>
                                      <input class="form-control col-md-12 passport_expiration" type="text" name="passport_expiration" id="passport_expiration" value="<?=($passport_expiration ? date("Y-m-d",strtotime($passport_expiration)) : "")?>"  ></input>
                                      <span class="input-group-addon" id="passport_expiration_saveAlert">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                  </div>
                                  <span id="passport_alert" style="display:none;color:red;">This field is required!</span>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group tricolumn">
                            <div class="col-md-12 tricolumn">
                                <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">TIN&nbsp;#:</label>
                                <div class="col-xs-12 col-md-9">
                                    <input type="text" class="form-control required" id="emp_tin" name="emp_tin" value="<?=$tinno?>"
                                    <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($tinno == "" || $tinno == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    <h6><span id="TINSpan" class="emp_tin"></span></h6>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div class="form-group tricolumn">
                            <div class="col-md-12 tricolumn">
                                <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">PRC&nbsp;#:</label>
                                <div class="col-xs-12 col-md-9">
                                   <input class="form-control required prc" name="prc" id="prc" type="text" value="<?=$prc?>"
                                   <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($prc == "" || $prc == "-" )) ? "" : $isreadonly?" readonly":""?>/>
                                   <h6><span id="PRCSpan" class="prc"></span></h6>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group tricolumn">
                            <div class="col-md-12 tricolumn">
                                <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Date&nbsp;of Expiration:</label>
                                <div class="col-xs-12 col-md-9">
                                  <div class='input-group date' id='prc_expiration'>
                                      <input class="form-control col-md-12 dateff" type="type" name="prc_expiration" id="prc_expiration" value="<?=($prc_expiration ? date("Y-m-d",strtotime($prc_expiration)) : "")?>" ></input>
                                      <span class="input-group-addon" name="prc_expiration" id="prc_expiration_saveAlert">
                                        <span class="glyphicon glyphicon-calendar" name="prc_expiration" id="prc_expiration"></span>
                                      </span>
                                  </div>
                                  <span id="prc_alert" style="display:none;color:red;">This field is required!</span>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group tricolumn">
                            <div class="col-md-12 tricolumn">
                                <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">PAG&nbsp;-&nbsp;IBIG:</label>
                                <div class="col-xs-12 col-md-9">
                                    <input type="text" class="form-control required" name="emp_pagibig" id="emp_pagibig" value="<?=$pagibig?>"
                                    <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($pagibig == "" || $pagibig == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    <h6><span id="PAGIBIGSpan" class="emp_pagibig"></span></h6>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4">
                        <div class="form-group tricolumn">
                            <div class="col-md-12 tricolumn">
                                <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">SSS&nbsp;#:</label>
                                <div class="col-xs-12 col-md-9">
                                   <input type="text" class="form-control required" id="emp_sss" name="emp_sss" value="<?=$sssno?>"
                                   <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($sssno == "" || $sssno == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                   <h6><span id="SSSSpan" class="emp_sss"></span></h6>
                                </div> 

                            </div>
                        </div>
                        <br><br>
                        <div class="form-group tricolumn">
                            <div class="col-md-12 tricolumn">
                                <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">PhilHealth:</label>
                                <div class="col-xs-12 col-md-9">
                                   <input type="text" class="form-control required" name="emp_philhealth" id="emp_philhealth" value="<?=$philhealth?>"
                                   <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($philhealth == "" || $philhealth == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                   <h6><span id="PHILHEALTHSpan" class="emp_philhealth"></span></h6>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group tricolumn">
                            <div class="col-md-12 tricolumn">
                                <label  for="employeeid" class="col-md-3 tricolumn">HMO&nbsp;#:</label>
                                <div class="col-md-9">
                                   <input type="text" class="form-control required" id="emp_hmo" name="emp_hmo" value="<?=$emp_hmo?>"
                                   <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($emp_hmo == "" || $emp_hmo == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                   <h6><span id="HMOSpan" class="emp_hmo"></span></h6>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="empaccno" value="">
            <div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>BANK DETAILS</b></h4></div>
                <div class="panel-body">
                    <div align="center" class="col-sm-12">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <label><b>Bank Name</b></label>
                            </div>
                            <div class="col-sm-6">
                                <label><b>Account Number</b></label>
                            </div>
                        </div> 
                    </div>
                    <div class="col-sm-12" id="employee_bank">
                        <?php 
                        $convert_to_array = explode('/', $emp_bank);
                        for($i=0; $i < count($convert_to_array ); $i++){
                            $key_value = explode('=', $convert_to_array [$i]);
                            $banks[$key_value [0]] = isset($key_value [1]) ? $key_value [1] : "";
                        }
                        foreach($this->extensions->getBankList() as $row): 
                                ?>
                                    <div align='center' class="col-sm-12">
                                        <div class="col-sm-6">
                                            <span><?= $row['bank_name'] ?></span>
                                        </div>
                                        <div class="col-sm-6">
                                            <div  class="form-group">
                                                <?php 
                                                $bankvalue = "";
                                                    foreach($banks as $key => $val){
                                                        if(is_array($val)){
                                                            $return = recursive_return_array_value_by_key($row['code'], $val);
                                                        }
                                                        else if($row['code'] === $key){
                                                            $bankvalue = $val;
                                                        }
                                                    }
                                                 ?>
                                                <input type="text" class="form-control emp_bank" id="emp_bank" name="emp_bank" bank="<?= $row['code'] ?>" employeeid="<?= $employeeid ?>" value="<?= $bankvalue ?>" style="width: 60%; text-align: center;" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($bankvalue == "" || $bankvalue == "-" )) ? "" : $isreadonly?" readonly":"" ?>>
                                                <h6><span class="<?= $row['code'] ?>"></span></h6>

                                            </div>

                                        </div>

                                    </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
            <?php if(!$ishidden) {?>
                <!-- -----BANK DETAILS -->
            
            <div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>EMPLOYEE INFORMATION</b></h4></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group">
                                <label  for="employeeid" class="col-xs-12 col-md-4">Type:</label>
                                <div class="col-xs-12 col-md-8">
                                    <div class="col-md-12 no-search">
                                      <input type="checkbox" class="teachingtype" name="teachingtype" id="teachingtype" value="teaching" <?= $teachingtype == "teaching" ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" <?=$isdisabled?> />&nbsp;&nbsp;Teaching
                                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                      <input type="checkbox" class="teachingtype" name="teachingtype" id="teachingtype" value="nonteaching" <?= $teachingtype == "nonteaching" ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" <?=$isdisabled?> />&nbsp;&nbsp;Non-Teaching
                                    </div>
                                </div> 
                            </div>
                            <br><br>
                            <div class="form-group" style="pointer-events: none;">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-4">Campus:</label>
                                    <div class="col-xs-12 col-md-8">
                                       <select class="chosen-select" name="campusid" id="campusid" <?=$isreadonly?>>
                                            <?= $this->extras->getCampuses($campusid) ?>
                                        </select>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-4">Batch Scheduling:</label>
                                    <div class="col-xs-12 col-md-8">
                                       <select class="chosen-select" name="emptype" id="emptype" <?=$isreadonly?>>
                                           <?
                                              $opt_type = $this->extras->showemployeetype();
                                              foreach($opt_type as $c=>$val){
                                              ?><option <?=($c==$emptype ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                              }
                                          ?>
                                      </select>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-group"  id="account_status" style="<?= (!$accai && $dateresigned2 != '') ? 'pointer-events: none' : '';?>">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-4">Account:</label>
                                    <div class="col-xs-12 col-md-8">
                                      <div class="col-md-12 no-search">
                                        <input type="checkbox" class="isactive isactivecb isactivecb_active" name="isactive" id="isactive" value="1" <?=  $accai ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" />&nbsp;&nbsp;Active
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" class="isactive isactivecb isactivecb_inactive" name="isactive" id="isactive" value="0" <?= !$accai ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" />&nbsp;&nbsp;In-Active
                                      </div>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group" style="pointer-events: none;">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-4">Schedule List:</label>
                                    <div class="col-xs-12 col-md-8">
                                      <select class="chosen-select" name="empshift" id="empshift" <?=$isdisabled?>>
                                          <?php 
                                          $shft = $this->extras->showshiftschedule();
                                              foreach ($shft as $key => $shftItem) {
                                                  print("<option value=\"{$key}\" ");
                                                  if (isset($empshift)) {
                                                  print(($empshift == $key) ? "selected=\"selected\"" : "");
                                                  }
                                                  print(">{$shftItem}</option>");
                                              }
                                          ?>
                                          <option style="font-size:10px"><?php print($empshift); ?></option>
                                      </select>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-4">Effectivity Date:</label>
                                    <div class="col-xs-12 col-md-8">
                                      <div class='input-group date' id='date_active'>
                                          <input type='text' id="" class="form-control" name="date_active" id="date_active" value="<?= ($date_active != '') ? $date_active :'' ?>" />
                                          <span class="input-group-addon" id="date_active_saveAlert">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                      </div>
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label id="saveShiftSched" style="color: green;"><h2>Success!</h2></label>
                                <span id="saveShiftSchedMsg" hidden="" style="font-size: 25px;">Failed, effectivity date is already processed.</span>
                            </div>
                        </div>
                    </div>
                </div><br>
                    <div class="row" hidden>
                        <div class="col-xs-12 col-sm-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label  for="employeeid" class="col-xs-12 col-md-4">Date Resigned:</label  >
                                    <div class='col-xs-12 col-md-8 input-group date' id='dateresigned' >
                                        <input class="form-control dateresigned" type="text"  name="dateresigned"  id='dateresigned' value="<?= ($dateresigned != '') ? $dateresigned :'' ?>" >
                                        <!-- <input class="form-control col-md-6 dateresigned" type="text"  name="dateresigned" value="<?=($dateresigned ? date("Y-m-d",strtotime($dateresigned)) : "")?>" > -->
                                          <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                          <span id="clearResigned" name="clearResigned" style="color:blue;float: right;"><b>&nbsp;&nbsp;&nbsp;CLEAR</b></span>

                                    </div>

                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="col-md-12" >
                                        <label  for="employeeid" class="col-xs-12 col-md-3" style="padding-right: 0px;">Reason for Leaving:</label>
                                        <div class="col-xs-12 col-md-9" style="padding-right: 0px;">
                                           <input type="text" class="form-control" id="resigned_reason" name="resigned_reason" value="<?=$resigned_reason?>" <?=$isreadonly?>/>
                                        </div> 

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             </div>
         <?php } ?>
            <!-- estat -->
            
            <div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>EMPLOYMENT HISTORY</b></h4></div>
                <div class="panel-body" id="emphistory">
                    
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PERSONAL INFORMATION</b></h4></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Date&nbsp;of Birth:</label>
                                    <div class="col-xs-12 col-md-9">
                                       <div class='input-group date'>
                                          <input type='text' class="form-control" name="bdate" id="bdate" value="<?= $bdate != '' && $bdate != '1970-01-01' ? date("Y-m-d",strtotime($bdate)) : "" ?>" 
                                          <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($bdate == "" || $bdate == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                          <span class="input-group-addon " id="bdate_saveAlert">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                      </div>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Gender:</label>
                                    <div class="col-xs-12 col-md-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($gender == "" || $gender == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                       <select class="chosen-select" name="gender" id="gender" >
                                        <?
                                            $opt_gender = $this->extras->showgender();
                                            foreach($opt_gender as $c=>$val){
                                                ?><option<?=($c==$gender ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                            }
                                        ?>
                                        </select>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Civil&nbsp;Status:</label>
                                    <div class="col-xs-12 col-md-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($civil_status == "" || $civil_status == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                       <select class="chosen-select" name="civil_status" id="civil_status">
                                            <?
                                                $opt_civil_stat = $this->extras->listCivilStatus();
                                                foreach ($opt_civil_stat as $key => $stat) {?>
                                                    <option value="<?=$key?>" <?= ($civil_status == $key) ? "selected" : ""?> ><?=$stat?></option>
                                                <?}
                                            ?>
                                      </select>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Mobile Number:</label>
                                    <div class="col-xs-12 col-md-9">
                                        <input type="hidden" id="mobile_2" value="<?=$mobile?>">
                                       <input type="text" name='mobile' class="form-control required" id="mobile" value="<?=$mobile?>" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4">
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Age:</label>
                                    <div class="col-xs-12 col-md-9">
                                       <div class="field">
                                         <!-- <?php if ($age == "" && $bdate=="") {
                                              $ages = "";
                                          }
                                          else
                                              $ages =$this->extras->computeAge($bdate); 
                                          ?> -->
                                          <input class="form-control required" type="text" name="age" id="age" value="<?=$age?>" readonly=""/>
                                      </div>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Nationality:</label>
                                    <div class="col-xs-12 col-md-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($nationality == "" || $nationality == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                      <select class="chosen-select" name="nationalityid" id="selNationality">
                                        <?
                                            $opt_type = $this->extras->showNationality();
                                            foreach($opt_type as $c=>$val){
                                                ?><option<?=($c==$nationality ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                            }
                                        ?>
                                      </select>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Citizenship:</label>
                                    <div class="col-xs-12 col-md-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($citizenship == "" || $citizenship == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                       <select class="chosen-select" name="citizenid" id="citizenship">
                                          <?
                                            $opt_type = $this->extras->showCitizenship();
                                            foreach($opt_type as $c=>$val){
                                                ?><option<?=($c==$citizenship ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                            }
                                        ?>
                                      </select>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Landline:</label>
                                    <div class="col-xs-12 col-md-9">
                                       <input type="text" name='landline' class="form-control required" id="landline" value="<?=$landline?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($landline == "" || $landline == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4">
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Place&nbsp;of Birth:</label>
                                    <div class="col-xs-12 col-md-9">
                                          <input class="form-control required" name="bplace" id="bplace" type="text" value="<?=$bplace?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($bplace == "" || $bplace == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Religion:</label>
                                    <div class="col-xs-12 col-md-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($religion == "" || $religion == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                       <select class="chosen-select" name="religionid" id="religion" >
                                          <?
                                              $opt_type = $this->extras->showReligion();
                                              foreach($opt_type as $c=>$val){
                                                  ?><option<?=($c==$religion ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                              }
                                          ?>
                                      </select>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Personal Email:</label>
                                    <div class="col-xs-12 col-md-9">
                                      <input type="text" name='personal_email' class="form-control required" id="personal_email" value="<?=$personal_email?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($personal_email == "" || $personal_email == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Work&nbsp;Email:</label>
                                    <div class="col-xs-12 col-md-9">
                                      <input type="text" name='email' class="form-control required" id="email" value="<?=$email?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($email == "" || $email == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <div class="row" id="spouseDetails" <?= ($civil_status != "1") ? ""  : "style='display:none'" ?>>
                        <label class="col-sm-9">Spouse Details:</label>
                        <div class="col-xs-12 col-sm-12 col-md-4 ">
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Spouse Name:</label>
                                    <div class="col-xs-12 col-md-9">
                                       <input class="form-control required" name="spouse_name" id="spouse" type="text" value="<?=$spouse?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($spouse == "" || $spouse == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4">
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label  for="employeeid" class="col-xs-12 col-md-3 tricolumn">Occupation:</label>
                                    <div class="col-xs-12 col-md-9">
                                       <input class="form-control required" name="occupation" id="occupation" type="text" value="<?=$occupation?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($occupation == "" || $occupation == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-4">
                            <div class="form-group tricolumn">
                                <div class="col-md-12 tricolumn">
                                    <label for="employeeid" class="col-xs-12 col-md-3 tricolumn" >Contact Number:</label>
                                    <div class="col-xs-12 col-md-9">
                                     <input type="text" name='spouse_contact' class="form-control required" id="spouse_contact" value="<?=$spouse_contact?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($spouse_contact == "" || $spouse_contact == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                        </div>   
                    </div>
                    <br><br>
                    <div class="row">
                        <label class="col-sm-9" value="">Current Address:</label>
                        <div class="col-xs-12 col-sm-12 col-md-6">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-3">Region:</label>
                                    <div class="col-xs-12 col-md-9" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($barangay == "" || $barangay == "-" || $cityaddr == "" || $cityaddr == "-" || $provaddr == "" || $provaddr == "-" || $regaddr == "" || $regaddr == "-" || $addr == "" || $addr == "-" || $zip_code == "" || $zip_code == "-" )) ? "" : $isreadonly?" style='pointer-events:none;'":"" ?>>
                                        <select class="chosen-select" name="regionaladdr" id="region" >
                                        <?
                                            $add = $this->extras->regionlist();
                                            foreach($add as $c=>$val){
                                                ?><option<?=($c==$regaddr ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                            }
                                        ?>
                                    </select>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-xs-12 col-md-3">Province:</label>
                                <div class="col-xs-12 col-md-9"  <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($barangay == "" || $barangay == "-" || $cityaddr == "" || $cityaddr == "-" || $provaddr == "" || $provaddr == "-" || $regaddr == "" || $regaddr == "-" || $addr == "" || $addr == "-" || $zip_code == "" || $zip_code == "-" )) ? "" : $isreadonly ?" style='pointer-events:none;'":"" ?>>
                                  <select class="chosen-select" name="provaddr" id="selProvince" >
                                      <option value="">Choose a province ...</option>
                                  </select>
                                </div> 
                            </div>
                            <br><br>
                            <div class="form-group">
                                <div class="col-md-12" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($barangay == "" || $barangay == "-" || $cityaddr == "" || $cityaddr == "-" || $provaddr == "" || $provaddr == "-" || $regaddr == "" || $regaddr == "-" || $addr == "" || $addr == "-" || $zip_code == "" || $zip_code == "-" )) ? "" : $isreadonly?" style='pointer-events:none;'":"" ?>>
                                    <label  for="employeeid" class="col-xs-12 col-md-3">Municipality:</label>
                                    <div class="col-xs-12 col-md-9" >
                                        <select class="chosen-select" name="cityaddr" id="selMunicipality">
                                            <option value="">Choose a municipality ...</option>
                                        </select>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-3">House #:</label>
                                    <div class="col-xs-12 col-md-9">
                                       <input class="form-control required" name="addr" id="addr" type="text" value="<?=$addr?>"
                                       <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($barangay == "" || $barangay == "-" || $cityaddr == "" || $cityaddr == "-" || $provaddr == "" || $provaddr == "-" || $regaddr == "" || $regaddr == "-" || $addr == "" || $addr == "-" || $zip_code == "" || $zip_code == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-3">Barangay:</label>
                                    <div class="col-xs-12 col-md-9" id="barangays" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($barangay == "" || $barangay == "-" || $cityaddr == "" || $cityaddr == "-" || $provaddr == "" || $provaddr == "-" || $regaddr == "" || $regaddr == "-" || $addr == "" || $addr == "-" || $zip_code == "" || $zip_code == "-" )) ? "" : $isreadonly?" style='pointer-events:none;'":"" ?>>
                                       <select class="chosen-select" name="barangay" id="barangay">
                                            <option value="">Choose a barangay ...</option>
                                        </select>
                                    </div>  
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-3">Zip Code :</label>
                                    <div class="col-xs-12 col-md-9">
                                      <input class="form-control required" name="zip_code" id="zip_code" type="text" maxlength="4" value="<?=$zip_code?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($barangay == "" || $barangay == "-" || $cityaddr == "" || $cityaddr == "-" || $provaddr == "" || $provaddr == "-" || $regaddr == "" || $regaddr == "-" || $addr == "" || $addr == "-" || $zip_code == "" || $zip_code == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <div class="row">
                      <div class="col-xs-12 col-sm-12 col-md-6">
                        <div class="form-group">
                            <input type="text" name="usertype" id="usertype" value="<?=$usertype?>" hidden>
                            <div class="col-md-12" id="ischeck" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($regaddr == $permaRegion && $addr == $permaAddress  ) && 
                                ($provaddr == $permaProvince && $cityaddr == $permaMunicipality  ) && 
                                ($zip_code == $permaZipcode && $barangay == $permaBarangay  ) && ($regaddr != '' && $addr != '' && $provaddr != '' && $cityaddr != '' && $zip_code != '' && $barangay != ''))? "style='pointer-events:none;' value='1'" : "value='0'"; ?> />
                                <style type="text/css">
                                input[type="checkbox"]:required:invalid + label { color: red; }
                                input[type="checkbox"]:required:valid + label { color: green; }
                                </style>
                                <form >
                                <font size="1.5"><p><input id="field_terms" type="checkbox" name="terms" >
                                <label><b><u>Please check if permanent address is the same as current address</u></b></label></p></font>
                                </form>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-9" value="">Permanent Address:</label>
                        <div class="col-xs-12 col-sm-12 col-md-6">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-3">Region:</label>
                                    <div class="col-xs-12 col-md-9" id="permaRegions"  <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($permaMunicipality == "" || $permaMunicipality == "-" || $permaBarangay == "" || $permaBarangay == "-" || $permaProvince == "" || $permaProvince == "-" || $permaRegion == "" || $permaRegion == "-" || $permaAddress == "" || $permaAddress == "-" || $permaZipcode == "" || $permaZipcode == "-" )) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                        <select class="chosen-select" name="permaRegion" id="permaRegionselect" >
                                        <?
                                            $add = $this->extras->regionlist();
                                            foreach($add as $c=>$val){
                                                ?><option<?=($c==$permaRegion ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                            }
                                        ?>
                                    </select>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-3">Province:</label>
                                    <div class="col-xs-12 col-md-9" id="permaProvinces" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($permaMunicipality == "" || $permaMunicipality == "-" || $permaBarangay == "" || $permaBarangay == "-" || $permaProvince == "" || $permaProvince == "-" || $permaRegion == "" || $permaRegion == "-" || $permaAddress == "" || $permaAddress == "-" || $permaZipcode == "" || $permaZipcode == "-" )) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                      <select class="chosen-select" name="permaProvince" id="permaProvince">
                                          <option value="">Choose a province ...</option>
                                      </select>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid"  class="col-xs-12 col-md-3">Municipality:</label>
                                    <div class="col-xs-12 col-md-9" id="permaMunicipalitys" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($permaMunicipality == "" || $permaMunicipality == "-" || $permaBarangay == "" || $permaBarangay == "-" || $permaProvince == "" || $permaProvince == "-" || $permaRegion == "" || $permaRegion == "-" || $permaAddress == "" || $permaAddress == "-" || $permaZipcode == "" || $permaZipcode == "-" )) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                        <select class="chosen-select" name="permaMunicipality" id="permaMunicipality">
                                            <option value="">Choose a municipality ...</option>
                                        </select>
                                    </div> 
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-6">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-3">House #:</label>
                                    <div class="col-xs-12 col-md-9">
                                       <input class="form-control required" name="permaAddress" id="permaAddress" type="text" value="<?=$permaAddress?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($permaMunicipality == "" || $permaMunicipality == "-" || $permaBarangay == "" || $permaBarangay == "-" || $permaProvince == "" || $permaProvince == "-" || $permaRegion == "" || $permaRegion == "-" || $permaAddress == "" || $permaAddress == "-" || $permaZipcode == "" || $permaZipcode == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-3">Barangay:</label>
                                    <div class="col-xs-12 col-md-9" id="permaBarangays" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($permaMunicipality == "" || $permaMunicipality == "-" || $permaBarangay == "" || $permaBarangay == "-" || $permaProvince == "" || $permaProvince == "-" || $permaRegion == "" || $permaRegion == "-" || $permaAddress == "" || $permaAddress == "-" || $permaZipcode == "" || $permaZipcode == "-" )) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                       <select class="chosen-select" name="permaBarangay" id="permaBarangay">
                                            <option value="">Choose a barangay ...</option>
                                        </select>
                                    </div>  
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-xs-12 col-md-3">Zip Code :</label>
                                    <div class="col-xs-12 col-md-9">
                                      <input class="form-control required" name="permaZipcode" id="permaZipcode" type="text" maxlength="4" value="<?=$permaZipcode?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN" || $usertype != "EMPLOYEE") && ($permaMunicipality == "" || $permaMunicipality == "-" || $permaBarangay == "" || $permaBarangay == "-" || $permaProvince == "" || $permaProvince == "-" || $permaRegion == "" || $permaRegion == "-" || $permaAddress == "" || $permaAddress == "-" || $permaZipcode == "" || $permaZipcode == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>   
                    <br><br>
                </div>          
            </div>
            <div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>FAMILY MEMBERS</b></h4></div>
                <div class="panel-body" id="table_family">
                    <div>
                        <input type="checkbox" name="childrencbox" id="childcBox" class="applicable-field" <?= ($applicable_children == "0" ? "checked" : "") ?> >
                        <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                    </div>
                    <div class="scrollbar employee_family_table">
                    <!-- <table class="table table-hover table-responsive" id="familylist">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Relation</th>
                                <th>Date of Birth</th>
                                <th>Data Approval Status</th>
                                <th>Admin Remarks</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                                $employee_child = $this->db->query("select * from employee_family where employeeid='$employeeid'")->result();
                                if(count($employee_child)>0){
                                    foreach($employee_child as $eb){
                                ?>
                                <tr id="<?= $eb->id ?>" table="employee_family" style="border-top: 1px solid #ddd !important;">
                                    <td><?=Globals::_e($eb->name)?></td>
                                    <td reldata="<?=$eb->relation?>"><?=$this->extras->getrelation($eb->relation)?></td>
                                    <td><?=$eb->bdate?></td>
                                    <td class="tooltip" id="<?= $eb->id ?>" table="employee_family" style="border-top: 0px solid #ddd;">
                                        <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $eb->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$eb->status?><span class="tooltiptext tooltiptext_<?=$eb->id?>_employee_family" >Loading..</span></a><?php } ?>
                                        <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a> <a class="btn <?= $eb->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> "> <?=$eb->status?></a><?php } ?>
                                    </td>
                                    <td><?=$eb->dra_remarks?></td>
                                    <td>
                                      <div style="float: right; border-top: 1px solid #ddd !important;">
                                        <?php if ($this->session->userdata("usertype") == "ADMIN"): ?>
                                            <a class='btn btn-primary edit_children' tbl_id = "<?=$eb->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                                            <a class='btn btn-warning delete_entry' tbl_id = "<?=$eb->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                                        <?php endif ?>
                                        </div>
                                    </td>
                                    
                                </tr>    
                                <?                            
                                    }
                            }else{
                            ?>
                                <tr>
                                    <td colspan="6">No existing data</td>
                                </tr>
                                <?                    
                            }
                            ?>                      
                        </tbody>
                    </table> -->
                  </div>
                    <a class="btn btn-info" href="#modal-view" tag="add_family" data-toggle="modal">Add Family Member</a>
                </div>
            </div>

            <!--<div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>CHILDREN</b></h4></div>
                <div class="panel-body">
                    <div>
                        <input type="checkbox" name="childrencbox" id="childcBox" class="applicable-field" <?= ($applicable_children == "0" ? "checked" : "") ?> >
                        <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                    </div>
                    <table class="table table-hover" id="childrenlist">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Birth Order</th>
                                <th>Date of Birth</th>
                                <th>Age</th>
                                <th>Status</th>
                                <th class="col-md-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                                $employee_child = $this->db->query("select * from employee_children where employeeid='$employeeid'")->result();
                                if(count($employee_child)>0){
                                    foreach($employee_child as $eb){
                                ?>
                                <tr id="<?= $eb->id ?>" table="employee_children">
                                    <td><?=$eb->name?></td>
                                    <td reldata='<?=$eb->gender?>'><?=$this->extras->genderdesc($eb->gender)?></td>
                                    <td><?=$eb->birthorder?></td>
                                    <td><?=$eb->birthdate?></td>
                                    <td><?=$eb->age?></td>
                                    <td>
                                        <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $eb->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$eb->status?></a><?php } ?>
                                        <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a> <?=$eb->status?></a><?php } ?>
                                    </td>
                                    <td>
                                    <?php
                                     if ($this->session->userdata("usertype") == "ADMIN"): ?>
                                    <tag style="float: right;">    
                                    <a class='btn btn-primary echildren' tbl_id = "<?=$eb->id?>" href='#modal-view' data-toggle='modal' style="margin-right: 10px;"><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_entry' tbl_id = "<?=$eb->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                                    <?php endif ?>
                                    </td>
                                    </tag>
                                </tr>    
                                <?                            
                                    }
                            }else{
                            ?>
                                <tr>
                                    <td colspan="6">No existing data</td>
                                </tr>
                                <?                    
                            }
                            ?>                      
                        </tbody>
                    </table>
                    <a class="btn btn-info" href="#modal-view" tag="add_children" id="add_children" data-toggle="modal">Add Children</a>
                </div>
            </div>-->

            <!--Emergency Contact-->
            <div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>EMERGENCY CONTACT INFORMATION</b></h4></div>
                <div class="panel-body" id="table_econtact">
                    <div>
                        <input type="checkbox" name="emergencyContactcbox" id="eciBox" class="applicable-field" <?= ($applicable_emergencyContact == "0" ? "checked" : "") ?> >
                        <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                    </div>
                    <div class="">
                      <div class="scrollbar employee_emergencyContact_table">
                      

                      </div>
                    </div>
                    <a class="btn btn-info" href="#modal-view" name="add_emergencyContact" tag="add_emergencyContact" id="add_emergencyContact" data-toggle="modal">Add Emergency Contact</a>
                </div>
            </div>
                   <!-- <table>
                        <tbody>
                            <?
                                if(count($legitimate_relations)>0){
                                    foreach($legitimate_relations as $lg_rel){
                                        list($lg_name,$lg_relation,$lg_address,$lg_contact,$lg_bdate,$lg_legit) = explode("~u~",$lg_rel);
                                        ?>
                                            <tr>
                                                <td><?=$lg_name?></td>
                                                <td reldata='<?=$lg_relation?>'><?=$this->extras->getrelation($lg_relation)?></td>
                                                <td><?=$lg_address?></td>
                                                <td><?=$lg_contact?></td>
                                                <td><?=$lg_bdate?></td>
                                                <td><?=($lg_legit==1?"YES":"NO")?></td>
                                                <td class="align_center">
                                                <a class='btn btn-warning editrelation' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning deleterelation'><i class='glyphicon glyphicon-trash'></i></a>
                                                </td>
                                        </tr>    
                                        <?                            
                                    }
                                }else{
                                ?>
                                    <tr>
                                        <td colspan="6">No existing data</td> 
                                    </tr>
                                    <?                    
                                }
                            ?>                        
                        </tbody>
                    </table>  -->
                    <!-- <a class="btn btn-info" href="#modal-view" tag="add_legit" data-toggle="modal">Add Relation</a> -->

        </form>        
    </div>
</div>

<div id="add_employee_aims" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                            <h4 class="media-heading" style="font-weight: bold; font-family: Avenir;">Pinnacle Technologies Inc.</h4>
                            <p style="font-family:Avenir; margin-top: -1%;">D`Great</p>
                        </div>
                </div>
                <center><b><h3 tag="title" class="modal-title">Employee 201 File</h3></b></center>
            </div>
            <div class="modal-body">
                <form id="form_employee_aims">
                    <div class="form-group">
                        <label class="field_name align_right">Employee ID</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                            <input class="form-control" name="aims_employeeid" id="aims_username" type="text" value="<?=$employeeid?>">
                        </div>
                        <span style="color: red;display: none;" id="warning">&nbsp;&nbsp;Employee ID already exist!</span>
                    </div>
                    <div class="form-group">
                        <label class="field_name align_right">Last Name:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                            <input class="form-control" name="aims_lname" id="aims_lname" type="text" value="<?=$lname?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="field_name align_right">First Name:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                            <input class="form-control" name="aims_fname" id="aims_fname" type="text" value="<?=$fname?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="field_name align_right">Middle Name:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-list"></i></span>
                            <input class="form-control" name="aims_mname" id="aims_mname" type="text" value="<?=$mname?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="field_name align_right">Campus:</label>
                        <div class="input-group" style="width: 100%;">
                             <select class="form-control" name="aims_campusid" id="aims_campusid">
                                <?= $this->extras->getCampuses($campusid) ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <input type="checkbox" name="tnt" class="cbox" value="teaching" <?=($teachingtype == "teaching" ? "checked" : "")?>> &nbsp;&nbsp;Teaching
                        </div>
                        <div class="col-md-4">
                            <input type="checkbox" name="tnt" class="cbox" value="nonteaching"> &nbsp;&nbsp;Non Teaching
                        </div>
                    </div>
                    <div class="form-group" id="loading" style="display: none;"><img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..</div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" aria-hidden="true" class="btn btn-danger">Close</a>
                <a href="#" id="save_emp_aims" class="btn btn-success">Save</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade success_modal" id="success_modal" role="dialog">
    <div class="modal-dialog modal-sm" style="top: 35%;">
        <div class="modal-content">
            <div class="modal-body" style="margin-bottom: 0px;">
                <p style="color:green;font-weight: bold;">Your information has been saved.</p>
            </div>
        </div>
    </div>
</div>
<!-- <div id="snackbar">Your information has been saved.</div> -->
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<input type="hidden" id="provaddr" value="<?= $provaddr ?>">
<input type="hidden" id="cityaddr" value="<?= $cityaddr ?>">
<input type="hidden" id="brgyid" value="<?= $barangay ?>">
<input type="hidden" id="regaddr" value="<?= $regaddr ?>">
<input type="hidden" id="empshift" value="<?= $empshift ?>">
<input type="hidden" id="date_active" value="<?= $date_active ?>">
<input type="hidden" id="teachingtype" value="<?= $teachingtype ?>">
<input type="hidden" id="usertype" value="<?= $usertype ?>">
<input type="hidden" id="empid" value="<?= $employeeid ?>">
<?php $approver = $this->session->userdata("username"); ?>
<input type="hidden" id="approverid" value="<?= $approver ?>">

<input type="hidden" id="departmentEH" value="<?= $deptid ?>">
<input type="hidden" id="officeEH" value="<?= $office ?>">
<input type="hidden" id="employmentstatEH" value="<?= $employmentstat ?>">
<input type="hidden" id="positionEH" value="<?= $position ?>">
<input type="hidden" id="dateposEH" value="<?= $datepos ?>">
<input type="hidden" id="dateresEH" value="<?= $dateresigned2 ?>">

<script src="<?=base_url()?>js/employee/personal_info.js"></script>
<script>
  var toks = hex_sha512(" ");
    $(document).ready(function(){
    if ($(window).width() < 1599) {
        $("#sidebarCollapse").click();
    }
    loadTable('employee_family_table');
    loadTable('employee_emergencyContact_table');
});

    $("a[name='print_out']").click(function() {
    <?php
        print("var id = \"" . $empdetails['employeeid'] . "\";");
    ?>
        if (id != "") {
            var vals = "form=" + GibberishAES.enc("empdetails", toks);
            vals   +=  "&id="+ GibberishAES.enc(id, toks);
            vals   +=  "&toks="+toks;
            // window.open("<?=site_url("forms/loadForm")?>"+vals,"pdftest");

            encodedData = encodeURIComponent(window.btoa(vals));
        openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
        };
    });

    $('input[name="tnt"]').on('change', function() {
        $('input[name="tnt"]').not(this).prop('checked', false); 
    });
</script>
