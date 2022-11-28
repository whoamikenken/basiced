<?php
    /**
    * @author Justin
    * @copyright 2016
    */
    date_default_timezone_set('Asia/Manila');
    $CI =& get_instance();
    $CI->load->model('applicantt');
    $CI->load->model('utils');

    $usertype = $this->session->userdata("usertype");
    $employeeid = $employeecode = $nname = $cityaddr = $provaddr = $regaddr = $addr = $cityaddr2 = $provaddr2 = $regaddr2 = $addr2 = $blood_type = $height = $weight = $gender = $civil_status = $spouse = $bdate = $mobile = $citytelno = $employmentstat = $emptype = $empshift = $dateemployed = $campusid = $maxregular = $maxparttime = $bplace = $deptid = $assignment = $remarks = $position = $management = $dateresigned = $dateresigned2 = $datepos = $resigned_reason = $tinno = $sssno = $philhealth = $pagibig = $peraa = $medicare = $emp_bank = $emp_accno = $citizenship = $religion = $nationality = $prc = $passport = $visa = $icard = $crnno = $permanent_address = $cp_name = $cp_relation = $cp_address = $cp_mobile = $cp_telno = $teaching = $teachingtype = $accai = $leavetype = $occupation = $mother = $motheroccu = $father = $fatheroccu = $hosp = $hosptxt = $operation = $operationtxt = $operationdate = $medhistory = $medhistorytxt = $medconditions = $age = $date_active = $distinguishingMarks = $zip_code = $barangay = $zip_code2 = $barangay2 = $dates=$ages= $aims = $aimsdept= $aimscb = $prc_expiration = $passport_expiration = $emp_hmo = $landline = $spouse_mobile = $cur_email = "";     
    $legitimate_relations = array();
    $employment_history = array();
    $applicable_field = $applicable_children = $applicable_emergencyContact = $applicable_skill = $applicable_family = $address_fieldterm =  "";


    $ishidden = $isdisabled = $isreadonly = "";
    $cansave = true;
    $ishidden   = " hidden";
    $iquery     = $this->db->query("SELECT * FROM elfinder_file where title='$employeeid'");

    if(!$applicantId) $applicantId = $CI->applicantt->getApplicantId($lname, $fname, $mname, $email, $positionid);

    if($applicantId){
        $res = $this->applicantt->getApplicantPersonalInfo($applicantId);

        if($res->num_rows() > 0){
            $data = $res->result_array();
            $empdetails = $data[0];
            $employeecode = $empdetails['employeecode'];
            $lname = $empdetails['lname'];
            $fname = $empdetails['fname'];
            $mname = $empdetails['mname'];
            $nname = $empdetails['nname'];

            /*address*/

            $cityaddr = $empdetails['cityaddr'];
            $provaddr = $empdetails['provaddr'];
            $regaddr = $empdetails['regionaladdr'];
            $addr = $empdetails['addr'];
            $zip_code = $empdetails['zip_code'];
            $barangay = $empdetails['barangay'];

            $cityaddr2 = $empdetails['cityaddr2'];
            $provaddr2 = $empdetails['provaddr2'];
            $regaddr2 = $empdetails['regionaladdr2'];
            $addr2 = $empdetails['addr2'];
            $zip_code2 = $empdetails['zip_code2'];
            $barangay2 = $empdetails['barangay2'];

            $occupation = $empdetails['occupation'];
            $age    = $empdetails['age'];
            $gender = $empdetails['gender'];
            $civil_status = $empdetails['civil_status'];
            // civil_status
            $spouse = $empdetails['spouse_name'];
            $spouse_mobile = $empdetails['spouse_mobile'];
            $bdate = isset($empdetails['bdate']) ? $empdetails['bdate'] : "";
            $mobile = $empdetails['mobile'];
            $citytelno = $empdetails['citytelno'];
            $email = $empdetails['email'];
            $cur_email = $empdetails['cur_email'];
            $employmentstat = $empdetails['employmentstat'];
            $emptype = $empdetails['emptype'];
            $empshift = $empdetails['empshift'];
            $date_active = $empdetails['date_active'];

            $dateemployed = isset($empdetails['dateemployed']) ? $empdetails['dateemployed'] : "";
            $maxregular = $empdetails['maxregular'];
            $maxparttime = $empdetails['maxparttime'];
            $bplace = $empdetails['bplace'];
            $deptid = $empdetails['deptid'];
            $leavetype = $empdetails['leavetype'];

            $positionid = $empdetails['positionid'];
            $datepos = (!empty($empdetails['dateposition']) && $empdetails['dateposition'] != "0000-00-00" && $empdetails['dateposition'] != "1970-01-01") ? date("Y-m-d",strtotime($empdetails['dateposition'])) : "";
            $assignment = $empdetails['assignment'];
            $remarks = $empdetails['remarks'];
            $management = $empdetails['managementid'];
            $dateresigned = $empdetails['dateresigned'] ? $empdetails['dateresigned'] : '';
            $resigned_reason = $empdetails['resigned_reason'];
            $tinno = $empdetails['emp_tin'];
            $hmo = $empdetails['emp_hmo'];
            $sssno = $empdetails['emp_sss'];
            $philhealth = $empdetails['emp_philhealth'];
            $pagibig = $empdetails['emp_pagibig'];
            $peraa = $empdetails['emp_peraa'];
            $medicare = $empdetails['emp_medicare'];
            $emp_accno = $empdetails['emp_accno'];
            $citizenship = $empdetails['citizenid'];
            $religion = $empdetails['religionid'];
            $nationality = $empdetails['nationalityid'];
            $prc = $empdetails['prc'];
            $prc_expiration = $empdetails['prc_expiration'];
            $passport = $empdetails['passport'];
            $passport_expiration = $empdetails['passport_expiration'];
            $visa = $empdetails['visa'];
            $icard = $empdetails['icardnum'];
            $crnno = $empdetails['crnno'];
            $permanent_address = $empdetails['permanentaddr'];
            // $legitimate_relations = $empdetails['legitimate_relations'];
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
            $landline = $empdetails['landline'];
            $cp_relation = $empdetails['cp_relation'];
            $cp_address = $empdetails['cp_address'];
            $cp_mobile = $empdetails['cp_mobile'];
            $cp_telno = $empdetails['cp_telno'];
            $teaching = $empdetails['teaching'];
            $teachingtype = $empdetails['teachingtype'];
            $accai = $empdetails['isactive'];
            $applicable_emergencyContact = $empdetails['emergencyContactcbox'];

            // $employment_history = $this->employee->getEmploymentStatusHistory($employeeid);
            $applicable_field   = $this->db->query("SELECT * FROM applicant_applicable_fields WHERE employeeid='$applicantId'");
            if($applicable_field->num_rows > 0){
                $applicable_family = $applicable_field->row(0)->children;
                $applicable_emergencyContact = $applicable_field->row(0)->emergency;
                $address_fieldterm = $applicable_field->row(0)->address;
            }
        }
    }

// $employee_photo = $this->db->query("SELECT * FROM employee_photo where employeeid = '$applicantId'");
// $hasPhoto = 0;
// if($employee_photo->num_rows() > 0){
//     $hasPhoto++;
//     $photo = json_decode(json_encode($employee_photo), true);
//     $photo = "data:image/jpg;base64,".$photo[0]['file'];
// }

$employee_photo =  $this->db->query("SELECT * FROM elfinder_file WHERE name LIKE '%$applicantId%'");
// echo "<pre>"; print_r($this->db->last_query()); die;
$hasPhoto = 0;
if($employee_photo->num_rows() > 0){
    $hasPhoto++;
    foreach ($employee_photo->result() as $key => $value) {
      $photo = "data:".$value->mime.";base64,".base64_encode($value->content);
    }
}


?>
<style>@media (max-width: 768px) { .elfinderimg{   display: none;  }   } .error{color: red;}
#saveShiftSched{
color: red;text-decoration:underline;cursor: pointer;
font-weight: bold;font-style: italic;
}
#saveShiftSched:hover{
color: green;
}

.upperCase{
    text-transform:uppercase;
}


.modal-body{
background-color: #f5f5f5;
}

.cbox{
-ms-transform: scale(1.5); /* IE */
-moz-transform: scale(1.5); /* FF */
-webkit-transform: scale(1.5); /* Safari and Chrome */
-o-transform: scale(1.5); /* Opera */
}
    .has-error{
    
border-style: solid;
    border-color: #ff0000;
}
.valid{
    border: 2px green solid;
}
.invalid{
    border: 2px red solid;
}

#info .chosen-container{
    text-transform: uppercase;
}

#info input{
    text-transform: uppercase;
}


</style>

<div class="widgets_area">
    <form id="info">
        <div class="row" style="width:100%; margin-left: unset; margin-right: unset;">
        <!-- <a href="#" name='backlist'>Back to employee list</a> -->
            <input type="hidden" name="job" value="employee/personal_info">
            <input name="usertype" type="hidden" value="<?=$usertype?>"/>
            <div class="form_row"><br>
                <table>
                    <tr>
                        <td class="align_right" rowspan="2" width="40%"><img src="<?=base_url()?>images/school_logo.png" style="width: 100px;" /></td>
                        <td class="align_left" valign='bottom' style="padding: 0;"><h4 style="font-size: 23px;font-family: Book Antiqua;margin-left: 10px;"><b><?= $this->extras->school_name()?></b></h4></td>
                    </tr>
                    <tr>
                        <td class="align_left" valign='baseline' style="padding: 0;"><h5 style="font-size: 19px;margin-left: 1%; margin-top: -1%; font-family: Book Antiqua"><strong><?= $this->extras->school_desc()?></strong></h5></td>
                    </tr>
                </table>
            </div>
            <?if($cansave){?>
<!--                 <div class='align_right'><label class="text-info" style="color:red; font-size: 16px;   ">
                    <b>(Click SAVE for each tab you accomplish)</b></label> 
                    <a href="#" class="btn btn-primary" id="saveinfo"> Save</a> -->
                    <!-- <a class="btn btn-primary" id="print_out">Print</a> -->
              <!--   </div> -->
            <?}?><br><br>
            <!-- porgress bar -->
            <!-- <br /> -->
            <!-- <div class="panel">
                <div class="panel-heading"><h4><b>GENERAL INFORMATION</b></h4></div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <input type="hidden" name="applicantId" value="<?=$applicantId?>">
                            <div class="form_row" style="display: none;">
                                <?php if(!isset($applicantId)){ ?>
                                    <span id="refresh"><input type="checkbox" name="isnew" class="cbox" checked></span>
                                    <h1>wewew</h1>
                                <?php } ?>
                                <label class="field_name align_right">Applicant ID</label>
                                <div class="field">
                                    <input class="col-md-4 required" name="applicantId" type="text" value="<?= $applicantId?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <?$q = $this->db->query("SELECT * from code_position");?>
                                <div class="col-md-12">
                                    <label  for="label100" class="col-sm-3">Applying For:</label>
                                    <div class="col-sm-9">
                                        <select class="form-control upperCase" name="positionid">
                                            <?
                                            foreach ($q->result() as $key => $row) {?>
                                                <option value="<?=$row->positionid?>" <?=$positionid==$row->positionid?"selected":""?>><?=$row->description?></option>
                                            <?}
                                            ?>
                                        </select>
                                    </div> 
                                </div>
                            </div><br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-sm-3">First Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control upperCase required" name="fname" type="text" value="<?=$fname?>"<?=($fname?" readonly":"")?>/>
                                    </div> 
                                </div>
                            </div><br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-sm-3">Last Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control upperCase required" name="lname" type="text" value="<?=$lname?>"<?=($lname?" readonly":"")?>/>
                                    </div> 
                                </div>
                            </div><br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-sm-3">Middle Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control upperCase required" name="mname" type="text" value="<?=$mname?>"<?=($mname?" readonly":"")?>/>
                                    </div> 
                                </div>
                            </div><br><br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label  for="employeeid" class="col-sm-3">Nick Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control upperCase required" name="nname" id="nname" type="text" value="<?=$nname?>"<?=($nname?" readonly":"")?>/>
                                    </div> 
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="pull-right" style="width: 35%;" >
                                    <?if($iquery->num_rows() > 0){?>
                                        <img class="elfinderimg" src="<?=site_url('forms/loadForm')?>?form=imgview&eid=<?=$employeeid?>" style="float: right;position: absolute;width: 150px;"/>
                                    <?}else{?>
                                        <img class="elfinderimg" src="<?=base_url()?>images/no_image.gif" style="float: right;position: absolute;width: 150px;"/>
                                    <?}?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
             <?php if($usertype == "ADMIN" || $usertype == "EMPLOYEE"):?>
                <div >
                    <div>
                        <a href="#" class="btn btn-success" name='backlist' style="margin-bottom: 2%;">Back to applicant list</a>
                    </div>
                </div>
            <?php endif;?>
            <input type="hidden" name="applicantId" value="<?=$applicantId?>">
            <div class="panel" id="genInfo">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>GENERAL INFORMATION</b></h4></div>
                <div class="panel-body">
                    <form id="Personal_form_data">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?$q = $this->db->query("SELECT * from code_position");?>
                            <div class="col-md-12">
                                <label  for="label100" class="col-sm-3">Applying For:</label>
                                <div class="col-sm-9">
                                    <input type="text" name="positionid" class="form-control upperCase" value="<?=$this->extensions->getApplicantPosition($applicantId)?>" readonly>
                                    <!-- <select class="form-control upperCase" name="positionid">
                                        <?
                                        foreach ($q->result() as $key => $row) {?>
                                            <option value="<?=$row->positionid?>" <?=$positionid==$row->positionid?"selected":""?>><?=$row->description?></option>
                                        <?}
                                        ?>
                                    </select> -->
                                </div> 
                            </div>
                        </div><br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Last Name:</label>
                                <div class="col-sm-9">
                                    <input class="form-control upperCase required" name="lname" type="text" value="<?=$lname?>"<?=($lname?" readonly":"")?>/>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">First Name:</label>
                                <div class="col-sm-9">
                                    <input class="form-control upperCase required" name="fname" type="text" value="<?=$fname?>" <?=($fname?" readonly":"")?>/>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Middle Name:</label>
                                <div class="col-sm-9">
                                    <input class="form-control upperCase required" name="mname" type="text" value="<?=$mname?>" <?=($mname?" readonly":"")?> />
                                </div>
                            </div>
                        </div> 
                        <br><br>                                                                                                                                                                                                                           
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-sm-3">Nick Name</label>
                                <div class="col-sm-9">
                                    <input class="form-control upperCase required" name="nname" id="nname" type="text" value="<?=$nname?>"<?=($nname?" readonly":"")?>/>
                                </div> 
                            </div>
                        </div>  
                        <br><br>                                                  
                        <!-- FOR AIMS INTEGRATION -->
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="col-xs-12" id="appPhoto">
                            <?php if(isset($hasPhoto) && $hasPhoto == 0): ?>
                                <img class="elfinderimg " src="<?=base_url()?>images/no_image.gif" style="float: right;width: 180px; height: 180px;"/>
                            <?php else: ?>
                                <img class="elfinderimg " src="<?php echo  $photo; ?>"  style="float: right;width: 180px; height: 180px; border: 2px solid #a1a1a1"/>
                            <?php endif ?> 
                            
                        </div>
                        <div class="col-xs-12" id="employeeImage">
                            <?php if(!file_exists("images/employee/".$employeeid.".jpg")): ?>
                                <a href="#modal-view" data-toggle='modal' type="button" class="btn btn-primary uploadPhoto" filename="<?= $applicantId ?>" modalTitle="Upload Photo" style="float: right; width: 180px; margin-top: 10px;">Upload Photo</a>
                            <?php else: ?>
                                <a href="#modal-view" data-toggle='modal' type="button" class="btn btn-primary uploadPhoto" filename="<?= $applicantId ?>" modalTitle="Upload New Photo"  style="float: right; width: 180px; margin-top: 10px;">Upload New Photo</a>
                            <?php endif ?> 
                        </div>
                    </div>
                </form>
                </div>
            </div>

            <!-- <div class="panel">
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>IDENTIFICATION NUMBER</b></h4></div>
                <div class="panel-body">
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-xs-12 col-md-3 passport">Passport#:</label>
                                <div class="col-xs-12 col-md-9">
                                   <input type="text" class="form-control upperCase required passport" name="passport" id="passport" value="<?=$passport?>"
                                   <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($passport == "" || $passport == "-" )) ? "" : $isreadonly?'readonly style="pointer-events: none;"':"" ?>/>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-xs-12 col-md-3">Date of Expiration:</label>
                                <div class="col-xs-12 col-md-9">
                                  <div class='input-group date' id='passport_expiration'>
                                      <input class="form-control upperCase col-md-12 passport_expiration" type="text" name="passport_expiration" id="passport_expiration" value="<?=($passport_expiration ? date("Y-m-d",strtotime($passport_expiration)) : "")?>"  ></input>
                                      <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                  </div>
                                  <span id="passport_alert" style="display:none;color:red;">This field is required!</span>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-xs-12 col-md-3">TIN #:</label>
                                <div class="col-xs-12 col-md-9">
                                    <input type="text" class="form-control upperCase required" id="emp_tin" name="emp_tin" value="<?=$tinno?>"
                                    <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($mname == "" || $tinno == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-xs-12 col-md-3 prc">PRC#:</label>
                                <div class="col-xs-12 col-md-9">
                                   <input class="form-control upperCase required prc" name="prc" id="prc" type="text" value="<?=$prc?>"
                                   <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($prc == "" || $prc == "-" )) ? "" : $isreadonly?' readonly style="pointer-events: none;"':"" ?>/>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-xs-12 col-md-3">Date of Expiration:</label>
                                <div class="col-xs-12 col-md-9">
                                  <div class='input-group date' id='prc_expiration'>
                                      <input class="form-control upperCase col-md-12 dateff" type="type" name="prc_expiration" id="prc_expiration" value="<?=($prc_expiration ? date("Y-m-d",strtotime($prc_expiration)) : "")?>" ></input>
                                      <span class="input-group-addon" name="prc_expiration" id="prc_expiration">
                                        <span class="glyphicon glyphicon-calendar" name="prc_expiration" id="prc_expiration"></span>
                                      </span>
                                  </div>
                                  <span id="prc_alert" style="display:none;color:red;">This field is required!</span>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-xs-12 col-md-3">PAG-IBIG:</label>
                                <div class="col-xs-12 col-md-9">
                                    <input type="text" class="form-control upperCase required" name="emp_pagibig" id="emp_pagibig" value="<?=$pagibig?>"
                                    <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($pagibig == "" || $pagibig == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-xs-12 col-md-3">SSS #:</label>
                                <div class="col-xs-12 col-md-9">
                                   <input type="text" class="form-control upperCase required" id="emp_sss" name="emp_sss" value="<?=$sssno?>"
                                   <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($sssno == "" || $sssno == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-xs-12 col-md-3">PhilHealth:</label>
                                <div class="col-xs-12 col-md-9">
                                   <input type="text" class="form-control upperCase required" name="emp_philhealth" id="emp_philhealth" value="<?=$philhealth?>"
                                   <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($philhealth == "" || $philhealth == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-md-3">HMO #:</label>
                                <div class="col-md-9">
                                   <input type="text" class="form-control upperCase required" id="emp_hmo" name="emp_hmo" value="<?=$hmo?>"
                                   <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($emp_hmo == "" || $emp_hmo == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
                <div class="panel" >
                <div class="panel-heading" style="background-color: #0072c6;"><h4><b>PERSONAL RECORD</b></h4></div>
                <form id="PIFORM">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Date of Birth:</label>
                                        <div class="col-xs-12 col-md-9">
                                           <div class='input-group date'>
                                              <input type='text' class="form-control ss-field-required dateFormat" name="bdate" id="bdate" value="<?= $bdate != '' && $bdate != '1970-01-01' ? date("F m, y",strtotime($bdate)) : "" ?>" 
                                              <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($bdate == "" || $bdate == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                              <span class="input-group-addon" id="bdate_saveAlert">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                              </span>
                                          </div>
                                        </div> 
                                    </div>
                                </div>
                                <br><br>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Gender:</label>
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
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3 ">Civil Status:</label>
                                        <div class="col-xs-12 col-md-9 " <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($civil_status == "" || $civil_status == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
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
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Mobile Number:</label>
                                        <div class="col-xs-12 col-md-9">
                                            <input type="hidden" class="ss-field-required" id="mobile_2" value="<?=$mobile?>">
                                            <input type="text" name='mobile' class="form-control upperCase required ss-field-required" id="mobile" value="<?=$mobile?>"/>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Age:</label>
                                        <div class="col-xs-12 col-md-9">
                                             <!-- <?php if ($age == "" && $bdate=="") {
                                                  $ages = "";
                                              }
                                              else
                                                  $ages =$this->extras->computeAge($bdate); 
                                              ?> -->
                                              <input class="form-control upperCase ss-field-required" type="text" name="age" id="age" value="<?= $ages ?>"  readonly=""/>
                                        </div> 
                                    </div>
                                </div>
                                <br><br>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3 ">Nationality:</label>
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
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Citizenship:</label>
                                        <div class="col-xs-12 col-md-9 " <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($citizenship == "" || $citizenship == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
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
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Landline:</label>
                                        <div class="col-xs-12 col-md-9">
                                           <input type="text" name='landline' class="form-control upperCase ss-field-required" id="landline" value="<?=$landline?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($landline == "" || $landline == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3 ">Place of Birth:</label>
                                        <div class="col-xs-12 col-md-9">
                                              <input class="form-control upperCase ss-field-required" name="bplace" id="bplace" type="text" value="<?=$bplace?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($bplace == "" || $bplace == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                        </div> 
                                    </div>
                                </div>
                                <br><br>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Religion:</label>
                                        <div class="col-xs-12 col-md-9 " <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($religion == "" || $religion == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
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
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Personal Email:</label>
                                        <div class="col-xs-12 col-md-9 ">
                                          <input type="text" name='email' class="form-control ss-field-required" id="email" value="<?=$email?>"/>
                                        </div> 
                                    </div>
                                </div>
                                <br><br>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Work Email:</label>
                                        <div class="col-xs-12 col-md-9">
                                          <input type="text" name='cur_email' class="form-control ss-field-required" id="cur_email" value="<?=$cur_email?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($cur_email == "" || $cur_email == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="row" hidden>
                            <label class="col-sm-9" value="">Spouse Details:</label>
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Spouse:</label>
                                        <div class="col-xs-12 col-md-9">
                                           <input class="form-control upperCase validate" name="spouse_name" id="spouse" type="text" value="<?=$spouse?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($spouse == "" || $spouse == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Occupation:</label>
                                        <div class="col-xs-12 col-md-9">
                                           <input class="form-control upperCase required" name="occupation" id="occupation" type="text" value="<?=$occupation?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($occupation == "" || $occupation == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-4">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label for="employeeid" class="col-xs-12 col-md-3">Contact Number</label>
                                        <div class="col-xs-12 col-md-9">
                                         <input type="text" name='spouse_mobile' class="form-control upperCase required" id="spouse_mobile" value="<?=$spouse_mobile?>"/>
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
                                        <div class="col-xs-12 col-md-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($regaddr == "" || $regaddr == "-" ) ? "" : $isreadonly ? "style='pointer-events:none;'" : "" ?>>
                                            <select class="chosen-select ss-item-required" name="regionaladdr" id="region" >
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
                                    <div class="col-xs-12 col-md-9" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($provaddr == "" || $provaddr == "-" ) ? "" : $isreadonly ? "style='pointer-events:none;'" : "" ?>>
                                      <select class="chosen-select ss-item-required" name="provaddr" id="selProvince" >
                                          <option value="">Choose a province ...</option>
                                      </select>
                                    </div> 
                                </div>
                                <br><br>
                                <div class="form-group">
                                    <div class="col-md-12" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($cityaddr == "" || $cityaddr == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                        <label  for="employeeid" class="col-xs-12 col-md-3">City/Municipality:</label>
                                        <div class="col-xs-12 col-md-9" >
                                            <select class="chosen-select ss-item-required" name="cityaddr" id="selMunicipality">
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
                                           <input class="form-control upperCase ss-field-required" name="addr" id="addr" type="text" value="<?=$addr?>"
                                           <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($addr == "" || $addr == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                        </div> 
                                    </div>
                                </div>
                                <br><br>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Barangay:</label>
                                        
                                        <div class="col-xs-12 col-md-9" id="barangays" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($barangay == "" || $barangay == "-" )) ? "" : $isreadonly?" readonly":"" ?>>
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
                                          <input class="form-control upperCase ss-field-required" name="zip_code" id="zip_code" type="text" maxlength="4" value="<?=$zip_code?>" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($zip_code == "" || $zip_code == "-" )) ? "" : $isreadonly?" readonly":"" ?>/>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="row">
                            <div class="form-group">
                                <input type="text" name="usertype" id="usertype" value="<?=$usertype?>" hidden>
                                <div class="col-md-12 ss-item-required">
                                    <style type="text/css">
                                    input[type="checkbox"]:required:invalid + label { color: red; }
                                    input[type="checkbox"]:required:valid + label { color: green; }
                                    </style>
                                    <form >
                                    <font size="1.5"><p><input id="field_terms" type="checkbox" name="terms" <?= ($address_fieldterm == "1" ? "checked" : "") ?>>
                                    <label><b><u>Please check if permanent address is the same as current address</u></b></label></p></font>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-9" value="">Permanent Address:</label>
                            <div class="col-xs-12 col-sm-12 col-md-6">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Region:</label>
                                        <div class="col-xs-12 col-md-9 " id="permaRegions"  <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($regaddr2 == "" || $regaddr2 == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                            <select class="chosen-select ss-item-required" name="regionaladdr2" id="regionaladdr2" >
                                            <?
                                                $add = $this->extras->regionlist();
                                                foreach($add as $c=>$val){
                                                    ?><option<?=($c==$regaddr2 ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
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
                                        <div class="col-xs-12 col-md-9 " id="permaProvinces" <?= ($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($provaddr2 == "" || $provaddr2 == "-" ) ? "" : $isreadonly? "style='pointer-events:none;'" : "" ?>>
                                          <select class="chosen-select ss-item-required" name="provaddr2" id="provaddr2">
                                              <option value="">Choose a province ...</option>
                                          </select>
                                        </div> 
                                    </div>
                                </div>
                                <br><br>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid"  class="col-xs-12 col-md-3">Municipality:</label>
                                        <div class="col-xs-12 col-md-9 " id="permaMunicipalitys">
                                            <select class="chosen-select ss-item-required" name="cityaddr2" id="cityaddr2">
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
                                           <input class="form-control upperCase ss-field-required" name="addr2" id="addr2" type="text" value="<?=$addr2?>"/>
                                        </div> 
                                    </div>
                                </div>
                                <br><br>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3 ">Barangay:</label>
                                        <div class="col-xs-12 col-md-9 " id="barangays2" <?= (($usertype != "ADMIN" || $usertype != "SUPER ADMIN") && ($barangay2 == "" || $barangay2 == "-" )) ? "" : $isreadonly?" readonly":"" ?>>
                                           <select class="chosen-select ss-item-required" name="barangay2" id="barangay2">
                                                <option value="">Choose a barangay ...</option>
                                            </select>
                                        </div> 
                                    </div>
                                </div>
                                <br><br>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label  for="employeeid" class="col-xs-12 col-md-3">Zip Code :</label>
                                        <div class="col-xs-12 col-md-9 ">
                                          <input class="form-control upperCase ss-field-required" name="zip_code2" id="zip_code2" type="text" maxlength="4" value="<?=$zip_code2?>"/>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </div>
                </form>          
            </div>

            
            <!--   <input type="hidden" name="empaccno" value="">
            <div class="panel">
            <div class="panel-heading"><h4><b>BANK DETAILS</b></h4></div>
            <div class="panel-body">
            <div align="center" class="col-sm-12">
            <div class="col-sm-12">
            <div class="col-sm-6">
            <label><b>Bank Name</b></label>
            </div>
            <div class="col-sm-6">
            <label><b>Account Name</b></label>
            </div>
            </div> 
            </div>
            <div class="col-sm-12" id="employee_bank">
            <?php foreach($this->extensions->getBankList() as $row): ?>
            <div align='center' class="col-sm-12">
            <div class="col-sm-6">
            <span><?= $row['bank_name'] ?></span>
            </div>
            <div class="col-sm-6">
            <div  class="form-group">
            <input type="text" class="form-control upperCase employee_account" bank="<?= $row['code'] ?>" style="width: 60%;">
            </div>
            </div>
            </div>
            <?php endforeach ?>
            </div>
            </div>
            </div>
            -->
            
            <input type="hidden" name="children_count">
            <div class="panel" hidden>
                <div class="panel-heading"><h4><b>CHILDREN</b></h4></div>
                <div class="panel-body" id="childrenTables">
                    <div id="childrenSpan" style="display: none;">
                        <span style="color:red">This field is required.</span>
                    </div>
                    <div>
                        <input type="checkbox" name="childrencbox" id="childcBoxs" class="applicable-field" <?= ($applicable_children == "0" ? "checked" : "") ?> >
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
                                <th class="col-md-4">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody class="tbody_children" id="tbody_children">
                            <!-- <?
                                $boCount = 1;
                                $applicant_child = $this->db->query("select * from applicant_children where employeeid='$applicantId' order by birthorder")->result();
                                if(count($applicant_child)>0){
                                    foreach($applicant_child as $eb){
                                        ?>
                                        <tr>
                                            <td class="testinglang"><?=strtoupper($eb->name)?></td>
                                            <td><?=strtoupper($eb->gender)?></td>
                                            <td><?=strtoupper($eb->birthorder)?></td>
                                            <td><?=strtoupper($eb->birthdate)?></td>
                                            <td><?=strtoupper($eb->age)?></td>
                                            <td>
                                                <button type="button" class='btn btn-info echildren' href='#infoModal' tbl_id="<?=$eb->id?>" data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></button>&nbsp;
                                                <button type="button" tbl_id="<?=$eb->id?>" class='btn btn-warning delete_entry'><i class='glyphicon glyphicon-trash'></i></button>
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
                            ?> -->                      
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-info employee_emergencyContact" href="#infoModal" id="add_children" tag="add_children" data-toggle="modal">Add Children</button>
                </div>
            </div>
            <input type="hidden" name="family_count">
            <div class="panel" >
                <div class="panel-heading"><h4><b>FAMILY MEMBER</b></h4></div>
                <div class="panel-body" id="childrenTable">
                    <div id="familySpan" style="display: none;">
                        <span style="color:red">This field is required.</span>
                    </div>
                    <div>
                        <input type="checkbox" name="childrencbox" id="childcBox" class="applicable-field" <?= ($applicable_family == "1" ? "checked" : "") ?> >
                        <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                    </div>
                    <table class="table table-hover table-responsive" id="familylist">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Relation</th>
                                <th>Date of Birth</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                                $employee_child = $this->db->query("select * from applicant_family where employeeid='$applicantId'")->result();
                                if(count($employee_child)>0){
                                    foreach($employee_child as $eb){
                                ?>
                                <tr id="<?= $eb->id ?>" table="employee_family" style="border-top: 1px solid #ddd !important;">
                                    <td><?=strtoupper($eb->name)?></td>
                                    <td reldata="<?=$eb->relation?>"><?=$this->extras->getrelation($eb->relation)?></td>
                                    <td><?=$eb->bdate?></td>
                                    <td>
                                            <a class='btn btn-info edit_family' tbl_id = "<?=$eb->id?>" href='#infoModal' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                                            <a class='btn btn-warning delete_family' tbl_id = "<?=$eb->id?>"><i class='glyphicon glyphicon-trash'></i></a>
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
                    <a class="btn btn-info add_family" id="add_family" href="#infoModal" tag="add_family" data-toggle="modal">Add Family Member</a>
                </div>
            </div>
            <input type="hidden" name="emergency_count">
            <div class="panel" >
                <div class="panel-heading"><h4><b>EMERGENCY CONTACT INFORMATION</b></h4></div>
                <div class="panel-body" id="emergencyTable">
                    <div id="emergencySpan" style="display: none;">
                        <span style="color:red">This field is required.</span>
                    </div>
                    <div>
                        <input type="checkbox" name="emergencyContactcbox" id="eciBox" class="applicable-field" <?= ($applicable_emergencyContact == "1" ? "checked" : "") ?> >
                        <span style="font-style: italic;">&nbsp;Check this box if Not Applicable</span>
                    </div>
                    <table class="table table-hover" id="emergencycontactlist">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Relation</th>
                                <th>Mobile #</th>
                                <th>Telephone #</th>
                                <th>Business #</th>
                                <th>Emergency Type</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody class="tbody_emergency">
                            <?php
                            $applicant_emergencyContact = $this->db->query("SELECT * from applicant_emergencyContact where employeeid='$applicantId'")->result();
                            if(count($applicant_emergencyContact)>0){
                                foreach($applicant_emergencyContact as $eb){
                                    ?>
                                    <tr >
                                        <td><?=strtoupper($eb->name)?></td>
                                        <td reldata="<?=$eb->relation?>"><?=strtoupper($this->extras->getrelation($eb->relation))?></td>
                                        <td><?=strtoupper($eb->mobile)?></td>
                                        <td><?=strtoupper($eb->homeNo)?></td>
                                        <td><?=strtoupper($eb->officeNo)?></td>
                                        <td class="reltype"  reltype="<?=$eb->type?>"><?=strtoupper($eb->type)?></td>
                                        <td>
                                        <a class='btn btn-info eEmergencyContact' href='#infoModal' tbl_id="<?=$eb->id?>" data-toggle="modal" ><i class='glyphicon glyphicon-edit'></i></a>&nbsp;
                                        <a class='btn btn-warning delete_emergency' tbl_id="<?=$eb->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                                        </td>
                                    </tr>    
                                    <?                            
                                }
                            }else{
                                ?>
                                <tr>
                                    <td colspan="6">No existing data</td>
                                </tr>
                            <? } ?>                      
                        </tbody>
                    </table>
                    <a class="btn btn-info add_emergencyContact" id="add_emergencyContact" href="#infoModal" tag="add_emergencyContact" data-toggle="modal" <?= (count($applicant_emergencyContact) == 2) ? "disabled" : "" ?> >Add Emergency Contact</a>
                </div>
            </div>

            <div class="panel" style="display: none;">
                <div class="panel-heading"><h4 class="h4white"><b>CONFIDENTIAL</b></h4></div>
                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label class="field_name align_right"><h3><strong>Health</strong></h3></label>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label  for="employeeid" class="col-sm-5">a.) Have you ever been hospitalized in the past 2 years?</label>
                            <div class="col-sm-7">
                                <input class="yesno" type="checkbox" name="healthcbox" value="1" <?=$hosp == 1 ? " checked" : ""?> /> Yes 
                                <input class="yesno" type="checkbox" name="healthcbox" value="2" <?=$hosp == 2 ? " checked" : ""?>/> No
                            </div> 
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="col-sm-9">
                                <input type="text" class="form-control upperCase <?=($hosp == 1 ? "required" : "disabled")?>" id="txthealth" name="txthealth" value="<?=$hosptxt?>"/>
                            </div>
                            <label  for="employeeid" class="col-sm-3">If yes, for what sickness?</label> 
                        </div>
                    </div>
                    <br><br><br>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label  for="employeeid" class="col-sm-5">b.) Have you undergone any operation?</label>
                            <div class="col-sm-7">
                                <input class="yesno" type="checkbox" name="operationcbox" value="1" <?=$operation == 1 ? " checked" : ""?> /> Yes 
                                <input class="yesno" type="checkbox" name="operationcbox" value="2" <?=$operation == 2 ? " checked" : ""?> /> No
                            </div> 
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="col-sm-9">
                                <div class="col-md-6">
                                    <input type="text" class="form-control upperCase <?=($operation == 1 ? "required" : "disabled")?>" id="txtoperation" name="txtoperation" value="<?=$operationtxt?>"/>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" class="form-control upperCase <?=($operation == 1 ? "required" : "disabled")?>" id="txtoperationdate" name="txtoperationdate" value="<?=$operationdate?>"/>
                                </div>
                            </div>
                            <label  for="employeeid" class="col-sm-3">If yes, for what sickness?&nbsp;&nbsp;&nbsp;&nbsp;When?</label> 
                        </div>
                    </div>
                    <br><br><br>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label  for="employeeid" class="col-sm-5">c.) Do you have any present or past medical history which will involve special consideration as to job assignment?</label>
                            <div class="col-sm-7">
                                <input class="yesno" type="checkbox" name="medhiscbox" value="1" <?=$medhistory == 1 ? " checked" : ""?> /> Yes 
                                <input class="yesno" type="checkbox" name="medhiscbox" value="2" <?=$medhistory == 2 ? " checked" : ""?> /> No
                            </div> 
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="col-sm-9">
                                <input type="text" class="form-control upperCase <?=($medhistory == 1 ? "required" : "disabled")?>" id="txtmedhis" name="txtmedhis" value="<?=$medhistorytxt?>"/>
                            </div>
                            <label  for="employeeid" class="col-sm-3">If so, indicate the condition?</label> 
                        </div>
                    </div>
                    <br><br><br><br>
                    <div class="form-group">
                        <div class="col-md-12">
                            <label  for="employeeid" class="col-sm-5">d.) Check any of these conditions you have or have had:</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="col-sm-12">
                                <input type="checkbox" name="adbox" value="1" <?= (in_array(1,explode(",",$medconditions)) ? " checked" : "")?> /> Allergic Disorders (Asthma,fever,hives) <br />
                                <input type="checkbox" name="ccbox" value="2" <?= (in_array(2,explode(",",$medconditions)) ? " checked" : "")?>/> Cardiovascular conditions (Elevated blood pressure,anemia,heart,abnormalities) <br />
                                <input type="checkbox" name="gpbox" value="3" <?= (in_array(3,explode(",",$medconditions)) ? " checked" : "")?>/> Gastrointestinal problems (ulcers,liver desease, browel problems) <br />
                                <input type="checkbox" name="mbox" value="4"  <?= (in_array(4,explode(",",$medconditions)) ? " checked" : "")?>/> Musculoskeletal (fractured bone,disc or joint problems) <br />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel" style="display:none;">
                <div class="panel-heading"><h4><b>Tax Dependents</b></h4></div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <table class="table table-hover" id="legitlist">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Relationship</th>
                                    <th>Address</th>
                                    <th>Contact #</th>
                                    <th>Birth Date</th>
                                    <th>Legitimate</th>
                                    <th class="col-md-2">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?
                                    $legitimate_relations = $this->db->query("select legitimate_name,legitimate_relation,legitimate_address,legitimate_contactno,legitimate_bdate,legit from applicant_legitimate_relations where employeeid='$applicantId'")->result_array();
                                    if(count($legitimate_relations)>0){
                                    foreach($legitimate_relations as $lg_rel => $row){
                                    // list($lg_name,$lg_relation,$lg_address,$lg_contact,$lg_bdate,$lg_legit) = explode("~u~",$lg_rel);
                                    ?>
                                    <tr>
                                        <td><?=strtoupper($row['legitimate_name'])?></td>
                                        <td reldata='<?=$row['legitimate_relation']?>'><?=strtoupper($this->extras->getrelation($row['legitimate_relation']))?></td>
                                        <td><?=strtoupper($row['legitimate_address'])?></td>
                                        <td><?=strtoupper($row['legitimate_contactno'])?></td>
                                        <td><?=strtoupper($row['legitimate_bdate'])?></td>
                                        <td><?=($row['legit']==1?"YES":"NO")?></td>
                                        <td class="align_center">
                                        <!-- <a class='btn btn-danger editrelation' href='#infoModal' type="button" data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a href="#" class='btn btn-danger deleterelation'><i class='glyphicon glyphicon-trash'></i></a> -->
                                        <button type="button" class='btn btn-info editrelation' href='#infoModal' data-toggle="modal" ><i class='glyphicon glyphicon-edit'></i></button>&nbsp;
                                        <button class='btn btn-warning deleterelation' tbl_id="<?=$eb->id?>"><i class='glyphicon glyphicon-trash'></i></button>
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
                        </table>
                        <a class="btn btn-primary" href="#infoModal" tag="add_legit" data-toggle="modal">Add Relation</a>
                    </div>
                </div>
            </div>
        </div>
    </form>        
</div>
<div class="modal fade" id="infoModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">

        <div class="modal-content">
            <div class="modal-header">
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                        <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
                    </div>
                </div>
                <center><b><h3 tag="title" class="modal-title">Modal Header</h3></b></center>
            </div>
            <div class="modal-body" style="background-color: white">
                <div class="row">
                    <div id="display">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a type="button" data-dismiss="modal" class="btn btn-danger modalclose" id="modalclose">Close</a>
                <a type="button" class="btn btn-success button_save_modal" id='button_save_modal'>Save changes</a>
            </div>
        </div>
    </div>
</div>
<script>
var toks = hex_sha512(" ");
var oneHundredPer = 1096.8;
var progress = 0;
var emergency_count = 0;
var ProvID = "<?=$provaddr?>";
var munid = "<?=$cityaddr?>";
var regCode = "<?=$regaddr?>";
var brgyid = "<?=$barangay?>";
var ProvID2 = "<?=$provaddr2?>";
var munid2 = "<?=$cityaddr2?>";
var regCode2 = "<?=$regaddr2?>";
var brgyid2 = "<?=$barangay2?>";


emergencyContactList();
childrenlist();
validateInputs();
loadCityAndProvinces();
loadPermanentCityAndProvinces();
disableAddEmergency();
// checkCivilStatus();
$(document).ready(function(){
    loadChildrendatatable("<?=$applicantId?>");
    loadCheckboxStatus();
    load1stTabCBStatus();
    dob = new Date($("#bdate").val());
    var today = new Date();
    var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
    if(isNaN(age)) {
        $('#age').val("");
    }

    // if($("#isprocessed").val() >= 5){
    //     $("#info input, #info button, #info select").attr("disabled", true);
    //     $("#info select").trigger("chosen:updated");
    // }
    // else{ 
    //     $("#info input, #info button").attr("disabled", false);
    //     $("#info select").attr("disabled", false);
    //     $("#info select").trigger("chosen:updated");
    // }

    $("#infoModal").find("#button_save_modal").addClass("button_save_modal");
    $("#infoModal").find("#modalclose").addClass("modalclose");

    $('[name=terms]').click(function(){
        $("select[name='regionaladdr']").blur();
        $("select[name='provaddr2']").blur();
        $("select[name='cityaddr2']").blur();
        $("select[name='addr2']").blur();
        $("select[name='barangay2']").blur();
        $("select[name='zip_code2']").blur();

        if($(this).prop("checked") == true){
            // alert("Checkbox is checked.");
            updateCheckBox(1, "address");
            var region= $("#region").val();
            var addr= $("#addr").val();
            var province=$("#selProvince").val();
            var barangay=$("#barangay").val();
            var municipality=$("#selMunicipality").val();
            var zip_code=$("#zip_code").val();
            loadPermanentCityAndProvinces(1,region, province, barangay, municipality);
            $("#regionaladdr2").val(region).trigger("chosen:updated");
            $("#addr2").val(addr);
            $("#zip_code2").val(zip_code);
            var applicantId = $("input[name='applicantId']").val();
            var cAddr = [region, addr, province, barangay, municipality, zip_code];
            var cName = ['regionaladdr2', 'addr2', 'provaddr2', 'barangay2', 'cityaddr2', 'zip_code2'];
            $.each(cAddr, function(a, caddr){
                $.each(cName, function(n, cname){
                    if(a == n){
                        var formdata = {
                        column:  GibberishAES.enc( cname, toks),
                        value:  GibberishAES.enc(caddr , toks),
                        applicantId:  GibberishAES.enc(applicantId , toks),
                        toks:toks
                    }
                    $.ajax({
                        url: "<?= site_url('applicant/updateApplicantInformation')?>",
                        data: formdata,
                        type: "POST",
                        success:function(response){
                            loadSuccessModals(cname);
                        }
                    });
                    }
                });
            });
            $("#permaRegions, #addr2, #permaProvinces, #barangays2, #permaMunicipalitys, #zip_code2").css("pointer-events", "none");
        }
        else if($(this).prop("checked") == false){
            updateCheckBox(0, "address");
            // alert("Checkbox is unchecked.");
            // var region1= $("#region1").val();
            $("#regionaladdr2").val("").trigger("chosen:updated");
            $("#addr2").val("");
            $("#provaddr2").val("").trigger("chosen:updated");
            $("#barangay2").val("").trigger("chosen:updated");
            $("#cityaddr2").val("").trigger("chosen:updated");
            $("#zip_code2").val("");
            UnloadPermaCityAndProvinces();
            var applicantId = $("input[name='applicantId']").val();
            var cAddr = [region, addr, province, barangay, municipality, zip_code];
            var cName = ['regionaladdr2', 'addr2', 'provaddr2', 'barangay2', 'cityaddr2', 'zip_code2'];
            $.each(cAddr, function(a, caddr){
                $.each(cName, function(n, cname){
                    if(a == n){
                        var formdata = {
                        column:  GibberishAES.enc( cname, toks),
                        value:  GibberishAES.enc("" , toks),
                        applicantId:  GibberishAES.enc(applicantId , toks),
                        toks:toks
                    }
                    $.ajax({
                        url: "<?= site_url('applicant/updateApplicantInformation')?>",
                        data: formdata,
                        type: "POST",
                        success:function(response){
                            loadSuccessModals(cname);
                        }
                    });
                    }
                });
            });
        }
    });
});

// $("#childcBox").change(function(){
//     if ($("#childcBox").prop("checked") == true) $("#add_family").prop("disabled", true);
//     else $("#add_family").prop("disabled", false);
// });

$("#childcBox").change(function(){
    if($("#childcBox").prop('checked')){ 
      $("#childrenTable a").attr('disabled', true);
      $("#childrenTable a").css('pointer-events', 'none');
      updateCheckBox(1, "children");
    }
    else{
      $("#childrenTable a").attr('disabled', false);
      $("#childrenTable a").css('pointer-events', '');
      updateCheckBox(0, "children");
    }
});

$("#eciBox").change(function(){
    if($("#eciBox").prop('checked')){ 
      $("#emergencyTable a").attr('disabled', true);
      $("#emergencyTable a").css('pointer-events', 'none');
      updateCheckBox(1, "emergency");
    }
    else{
      $("#emergencyTable a").attr('disabled', false);
      $("#emergencyTable a").css('pointer-events', '');
      updateCheckBox(0, "emergency");
    }
});

function load1stTabCBStatus(){
    if($("#childcBox").prop('checked')){ 
      $("#childrenTable a").attr('disabled', true);
      $("#childrenTable a").css('pointer-events', 'none');
    }
    else{
      $("#childrenTable a").attr('disabled', false);
      $("#childrenTable a").css('pointer-events', '');
    }

    if($("#eciBox").prop('checked')){ 
      $("#emergencyTable a").attr('disabled', true);
      $("#emergencyTable a").css('pointer-events', 'none');
    }
    else{
      $("#emergencyTable a").attr('disabled', false);
      $("#emergencyTable a").css('pointer-events', '');
    }

}



// $("#eciBox").change(function(){
//     if ($("#eciBox").prop("checked") == true) $("#add_emergencyContact").prop("disabled", true);
//     else $("#add_emergencyContact").prop("disabled", false);
// });

   $('input[type=text]').each(function(){
    if ($(this).val()){
      $(this).addClass("notEmpty");
    }
});
$("#get").addClass("notEmpty");
if (!$('#get').data('value') == 0){
  $('#get').addClass("notEmpty");
  var text = $('#'+$('#get').data('value')).text();
  $('#get').text(text);
}
$('select[name=empshift]').on('change',function(){
    if($('input[name=date_active]').val() != date_active || $('select[name=empshift]').val() != empshift){
        $('#saveShiftSched').show();
    }else{
        $('#saveShiftSched').hide();
    }     
});
$('#date_active').datetimepicker({format: 'MMMM D, YYYY'}).on('dp.change', function (event) {
       if($('input[name=date_active]').val() != date_active || $('select[name=empshift]').val() != empshift){
        $('#saveShiftSched').show();
    }else{
        $('#saveShiftSched').hide();
    }
});
$(document).ready(function() {
    $('#saveShiftSched').hide();
  if ($("#aims").is(":checked")) {
        
        $(".aimsdepartment").show();

    } else {
        
        $(".aimsdepartment").hide();
    }
    ImmiFields(true);

    var ProvID = "<?=$provaddr?>";
    var munid = "<?=$cityaddr?>";
    var regCode = "<?=$regaddr?>";

    var ProvID2 = "<?=$provaddr2?>";
    var munid2 = "<?=$cityaddr2?>";
    var regCode2 = "<?=$regaddr2?>";

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            provid :  GibberishAES.enc( ProvID , toks),
            regCode:  GibberishAES.enc( regCode, toks),
            fnctn: GibberishAES.enc( "provincelist" , toks),
            toks:toks
        },
        success: function(msg){
            $("select[name='province']").html(msg).trigger("chosen:updated");
            // $("select[name='municipality']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            munid :  GibberishAES.enc( munid , toks),
            ProvID: GibberishAES.enc( ProvID , toks) ,
            fnctn:  GibberishAES.enc( "municipalitylist" , toks),
            toks:toks
        },
        success: function(msg){
            $("select[name='cityaddr']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            brgyid :  GibberishAES.enc( brgyid , toks),
            munid: GibberishAES.enc( munid , toks) ,
            fnctn:  GibberishAES.enc( "barangaylist" , toks),
            toks:toks
        },
        success: function(msg){
            $("select[name='barangay']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            provid :  GibberishAES.enc( ProvID2 , toks),
            regCode:  GibberishAES.enc( regCode2 , toks) ,
            fnctn: GibberishAES.enc( "provincelist"  , toks),
            toks:toks
        },
        success: function(msg){
            $("select[name='province2']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            munid :  GibberishAES.enc( munid2 , toks) ,
            ProvID: GibberishAES.enc( ProvID2 , toks) ,
            fnctn: GibberishAES.enc( "municipalitylist", toks),
            toks:toks
        },
        success: function(msg){
            $("select[name='cityaddr2']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            brgyid :  GibberishAES.enc( brgyid2 , toks) ,
            munid: GibberishAES.enc( munid2 , toks)  ,
            fnctn:  GibberishAES.enc( "barangaylist" , toks) ,
            toks:toks
        },
        success: function(msg){
            $("select[name='barangay2']").html(msg).trigger("chosen:updated");
        }
    });

});
$("select[name='regionaladdr']").change(function(){
    clearOtherData('regionaladdr');
    getProvince(true);
});

function getProvince(ischange=false){
    var regCode = $("select[name='regionaladdr']").val();
    if(ischange){
        ProvID = "";
    }
    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          provid :  GibberishAES.enc( ProvID , toks),
          regCode:  GibberishAES.enc( regCode , toks),
          fnctn:  GibberishAES.enc( "provincelist" , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='provaddr']").html(msg).trigger("chosen:updated");
           $("select[name='cityaddr']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
           $("select[name='barangay']").html('<option value="">Choose a barangay ...</option>').trigger("chosen:updated");
           $("#zip_code").val("");
        }
    });
}

$("select[name='provaddr']").change(function(){
    clearOtherData('provaddr');
    getMunicipality(true);
});

function getMunicipality(ischange=false){
    var ProvID = $("select[name='provaddr']").val();
    if(ischange){
        munid = "";
    }
    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          munid :  GibberishAES.enc( munid , toks),
          ProvID: GibberishAES.enc( ProvID , toks),
          fnctn:  GibberishAES.enc( "municipalitylist" , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='cityaddr']").html(msg).trigger("chosen:updated");
           $("select[name='barangay']").html('<option value="">Choose a barangay ...</option>').trigger("chosen:updated");
        }
    });
}

$("select[name='cityaddr']").change(function(){
    clearOtherData('cityaddr');
    getBarangay(true);
    getZipCode($("select[name='cityaddr'] option:selected").html());
});

function getBarangay(ischange=false){
    var munid = $("select[name='cityaddr']").val();
    if(ischange){
        brgyid = "";
    }
    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          brgyid :  GibberishAES.enc( brgyid , toks),
          munid: GibberishAES.enc( munid , toks),
          fnctn:  GibberishAES.enc( "barangaylist" , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='barangay']").html(msg).trigger("chosen:updated");
        }
    });
}

$("select[name='barangay']").change(function(){
    getZipCode($("select[name='barangay'] option:selected").html(), $("select[name='cityaddr'] option:selected").html());
});

$("select[name='regionaladdr2']").change(function() {
    clearOtherData('regionaladdr2');
    getPermaProvince();
});

function getPermaProvince(){
    var permaReg = $("select[name='regionaladdr2']").val();

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          provid :  GibberishAES.enc( ProvID2 , toks),
          regCode:  GibberishAES.enc( permaReg , toks),
          fnctn:  GibberishAES.enc( "provincelist" , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='provaddr2']").html(msg).trigger("chosen:updated");
           $("select[name='cityaddr2']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
           $("select[name='barangay2']").html('<option value="">Choose a barangay ...</option>').trigger("chosen:updated");
           $("#zip_code2").val("");
        }
    });
}

$("select[name='provaddr2']").change(function(){
    clearOtherData('provaddr2');
    getPermaMunicipality();
});

function getPermaMunicipality(){
    var permaProv = $("select[name='provaddr2']").val();
    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          munid :  GibberishAES.enc( munid2 , toks),
          ProvID:  GibberishAES.enc( permaProv , toks),
          fnctn: GibberishAES.enc( "municipalitylist"  , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='cityaddr2']").html(msg).trigger("chosen:updated");
           $("select[name='barangay2']").html('<option value="">Choose a barangay ...</option>').trigger("chosen:updated");
        }
    });
}

function getPermaBarangay(ischange=false){
    var munid = $("select[name='cityaddr2']").val();
    if(ischange){
        brgyid = "";
    }
    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          brgyid :  GibberishAES.enc( brgyid2 , toks),
          munid:  GibberishAES.enc( munid , toks),
          fnctn:  GibberishAES.enc( "barangaylist" , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='barangay2']").html(msg).trigger("chosen:updated");
        }
    });
}

$("select[name='cityaddr2']").change(function(){
    clearOtherData('cityaddr2');
    getPermaBarangay(true);
    getPermaZipCode($("select[name='cityaddr2'] option:selected").html());
});

$("select[name='barangay2']").change(function(){
    getPermaZipCode($("select[name='barangay2'] option:selected").html(), $("select[name='cityaddr2'] option:selected").html());
});

// $("select[name='regionaladdr2']").change(function() {
//     var regCode = $(this).val();

//     $.ajax({
//         url: "<?=site_url('employee_/loadExtrasFunction')?>",
//         type: "POST",
//         data: {
//             provid : "",
//             regCode: regCode ,
//             fnctn: "provincelist"
//         },
//         success: function(msg){
//             $("select[name='provaddr2']").html(msg).trigger("chosen:updated");
//             $("select[name='cityaddr2']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
//         }
//     });
// });

// $("select[name='provaddr']").change(function() {
//     var ProvID = $(this).val();
//     $.ajax({
//         url: "<?=site_url('employee_/loadExtrasFunction')?>",
//         type: "POST",
//         data: {
//             munid : "",
//             ProvID: ProvID ,
//             fnctn: "municipalitylist"
//         },
//         success: function(msg){
//             $("select[name='cityaddr']").html(msg).trigger("chosen:updated");
//         }
//     });
// });

    function clearOtherData(data){
        var applicantId = $("input[name='applicantId']").val();
        $.ajax({
            url: "<?=site_url('employee_/loadExtrasFunction')?>",
            type: "POST",
            data: {
              applicantId :  GibberishAES.enc( applicantId , toks),
              changeaddr :  GibberishAES.enc( data , toks),
              fnctn:  GibberishAES.enc( "changeaddrApplicant" , toks),
              toks:toks
            },
            success: function(msg){
            }
        });
    }

    function formcheck(form)
    {
        $(form).find('input.ss-field-required:text').each(function(idx)
        {
            // console.log(this);
            if($(this).val().length == 0)
            {
                alert("Please Complete your signing form");
                $(this).addClass("invalid");
                throw new Error("Something went badly wrong!");
            }else
            {
                $(this).removeClass("invalid").addClass("valid");
            }
        });
        $(form).find('select.chosen-select').each(function(idx)
        {
            if($(this).val().length == 0)
            {
                alert("Please Complete your signing form");
                $(this).parent().find('div.chosen-container').addClass("invalid");
                throw new Error("Something went badly wrong!");
            }else{
                $(this).parent().find('div.chosen-container').removeClass("invalid").addClass("valid");
            }
        });
        return true;
    }

    // function formcheckTableChildren(tbody)
    // {
    //     $(tbody).find('input.ss-field-required:text').each(function(idx)
    //     {
    //         // console.log(this);
    //         if($(this).val().length == 0)
    //         {
    //             alert("Please Complete your signing form");
    //             $(this).addClass("invalid");
    //             throw new Error("Something went badly wrong!");
    //         }else
    //         {
    //             $(this).removeClass("invalid").addClass("valid");
    //         }
    //     });
    //     return true;
    // }

// $("select[name='cityaddr']").change(function() {
//     var munid = $(this).val();
//     $.ajax({
//         url: "<?=site_url('employee_/loadExtrasFunction')?>",
//         type: "POST",
//         data: {
//             brgyid : brgyid,
//             munid: munid ,
//             fnctn: "barangaylist"
//         },
//         success: function(msg){
//             $("select[name='barangay']").html(msg).trigger("chosen:updated");
//         }
//     });
// });

// $("select[name='cityaddr2']").change(function() {
//     var munid = $(this).val();
//     $.ajax({
//         url: "<?=site_url('employee_/loadExtrasFunction')?>",
//         type: "POST",
//         data: {
//             brgyid : brgyid2,
//             munid: munid ,
//             fnctn: "barangaylist"
//         },
//         success: function(msg){
//             $("select[name='barangay2']").html(msg).trigger("chosen:updated");
//         }
//     });
// });

// $("select[name='provaddr2']").change(function() {
//     var ProvID = $(this).val();
//     $.ajax({
//         url: "<?=site_url('employee_/loadExtrasFunction')?>",
//         type: "POST",
//         data: {
//             munid : "",
//             ProvID: ProvID ,
//             fnctn: "municipalitylist"
//         },
//         success: function(msg){
//             $("select[name='cityaddr2']").html(msg).trigger("chosen:updated");
//         }
//     });
// });

$("#selNationality").change(function() {
    ImmiFields(($("#selNationality").val() > 1) ? false : true);
});

function getZipCode(place, mun=''){
    var provCode = $("#selProvince").val();
    var munCode = $("#selMunicipality").val();
    var zipRes = 0;
    $.ajax({
        url: "<?=site_url('employee_/getZipCode')?>",
        type: "POST",
        data: {place: GibberishAES.enc(place , toks), mun: GibberishAES.enc(mun , toks), provCode: GibberishAES.enc(provCode , toks), munCode: GibberishAES.enc(munCode , toks), toks:toks},
        success:function(res){
            if(res){
                $("#zip_code").val(res);
                zipRes = res;
            } 
        }
    });
    setTimeout(function(){
        if(zipRes != 0){
            var applicantId = $("input[name='applicantId']").val();
            var formdata = {
                column:  GibberishAES.enc('zip_code' , toks),
                value:  GibberishAES.enc(zipRes , toks),
                applicantId:  GibberishAES.enc(applicantId , toks),
                toks:toks
            }
            $.ajax({
                url: "<?= site_url('applicant/updateApplicantInformation')?>",
                data: formdata,
                type: "POST",
                success:function(response){
                    loadSuccessModals('zip_code');
                    // loadSuccessModals();
                }
            });
        }
    }, 500)
}

function getPermaZipCode(place, mun=''){
    var provCode = $("#provaddr2").val();
    var munCode = $("#cityaddr2").val();
    var zipPermaRes = 0;
    $.ajax({
        url: "<?=site_url('employee_/getZipCode')?>",
        type: "POST",
        data: {place: GibberishAES.enc(place , toks), mun: GibberishAES.enc(mun , toks), provCode: GibberishAES.enc(provCode , toks), munCode: GibberishAES.enc(munCode , toks), toks:toks},
        success:function(res){
            if(res){
                $("#zip_code2").val(res);
                zipPermaRes = res;
            } 
        }
    });
    setTimeout(function(){
        if(zipPermaRes != 0){
            var applicantId = $("input[name='applicantId']").val();
            var formdata = {
                column: GibberishAES.enc('zip_code2'  , toks),
                value:  GibberishAES.enc(zipPermaRes , toks),
                applicantId:  GibberishAES.enc(applicantId , toks),
                toks:toks
            }
            $.ajax({
                url: "<?= site_url('applicant/updateApplicantInformation')?>",
                data: formdata,
                type: "POST",
                success:function(response){
                    // loadSuccessModals();
                    loadSuccessModals('zip_code2');
                }
            });
        }
    }, 500)
}

if($("#bplace").val() == "")    $("#print_out").hide();
else                            $("#print_out").show();


$(".editrelation").click(function(){
    addlegit($(this)); 
});

$("a[name='backlist']").click(function(){
    var obj = $(".inner_navigation .main li[class='active'] a"); 
    var site = $(obj).attr("site");
    var root = $(obj).attr("root");
    var menuid = $(obj).attr("menuid");
    var titlebar = $(obj).text();

    $("#mainform").attr("action","<?=site_url("main/site")?>");
    $("input[name='sitename']").val(site);
    $("input[name='rootid']").val(root);
    $("input[name='menuid']").val(menuid);
    $("input[name='titlebar']").val(titlebar);

    if(site) $("#mainform").submit();

});

$.validator.setDefaults({
    debug: true,
    success: "valid",
    ignore: ":hidden:not(.chzn-done)",
    errorPlacement: function(error, element) {
        if (element.hasClass('chosen')) {
        error.insertAfter(element.next('.chzn-container'));
        }else if(element.hasClass('yesno') || element.hasClass('applicable-field')){
            error.insertBefore(element.parents("div").eq(0));
        }else{
            error.insertAfter(element);
        }
    }
});

$.validator.addMethod("loginRegex", function(value, element) {
    return this.optional(element) || /^[a-z0-9\s]+$/i.test(value);
}, "Username must contain only letters, numbers.");

$(".date").datetimepicker({
    format: "MMMM D, YYYY"
});

$("a[tag='add_legit']").click(function(){
    addlegit("");
});

$("select[name='emptype']").change(function(){
    var emptype  = $(this).val();
    var empshift = $("select[name='empshift']").val();
    call_shiftschedule(emptype,empshift);  
});

///< for separate saving of shift schedule and effectivity date
var empshift    = "<?=$empshift?>";
var date_active = "<?=$date_active?>";

$('input[name=date_active], select[name=empshift]').on('change',function(){
    if($('input[name=date_active]').val() != date_active || $('select[name=empshift]').val() != empshift){
        $('#saveShiftSched').removeAttr('hidden');
    }else{
        $('#saveShiftSched').attr('hidden',true);
    }     
});

$('#saveShiftSched').on('click',function(e){
    $('#saveShiftSched').attr('hidden',true);   

    var newempshift = $('select[name=empshift]').val();
    var newdateactive = $('input[name=date_active]').val();

    $.ajax({
        url: "<?=site_url('employee_/saveShiftSchedule')?>",
        type: "POST",
        dataType : 'JSON',
        data: {
            employeeid :  GibberishAES.enc("<?=$employeeid?>"  , toks),
            tnt        :  GibberishAES.enc("<?=$teachingtype?>" , toks),
            empshift   :  GibberishAES.enc( newempshift, toks),
            date_active:  GibberishAES.enc( newdateactive, toks),
            toks:toks
        },
        success: function(msg){
            $('#saveShiftSchedMsg').removeAttr('hidden');
            $('#saveShiftSchedMsg').show();
            if(msg.err_code==0){
                empshift = newempshift;
                date_active = newdateactive;
                $("#saveShiftSchedMsg").html(msg.msg).css('color','green').delay(5000).fadeOut();
            }else{
                $('#saveShiftSched').removeAttr('hidden');
                $("#saveShiftSchedMsg").html(msg.msg).css('color','red').delay(5000).fadeOut();
            }
        }
    });
});

function loadCityAndProvinces(){
    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          provid : GibberishAES.enc( ProvID, toks),
          regCode: GibberishAES.enc( regCode, toks),
          fnctn: GibberishAES.enc( "provincelist", toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='provaddr']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          munid :  GibberishAES.enc( munid, toks),
          ProvID:  GibberishAES.enc( ProvID, toks),
          fnctn: GibberishAES.enc( "municipalitylist", toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='cityaddr']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
            url: "<?=site_url('employee_/loadExtrasFunction')?>",
            type: "POST",
            data: {
              brgyid :  GibberishAES.enc( brgyid, toks),
              munid:  GibberishAES.enc( munid, toks),
              fnctn:  GibberishAES.enc( "barangaylist", toks),
              toks:toks
            },
            success: function(msg){
                $("select[name='barangay']").html(msg).trigger("chosen:updated");
            }
        });
}

function UnloadPermaCityAndProvinces(){
    $("select[name='provaddr2']").html('<option value="">Choose a province ...</option>').trigger("chosen:updated");
    $("select[name='cityaddr2']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
    $("select[name='barangay2']").html('<option value="">Choose a barangay ...</option>').trigger("chosen:updated");
    $("#permaRegions, #addr2, #permaProvinces, #barangays2, #permaMunicipalitys, #zip_code2").css("pointer-events", "unset");

}

function loadPermanentCityAndProvinces(terms='', region='', province='', barangay='', municipality=''){
    if(terms){
        ProvID2 = province;
        regCode2 = region;
        munid2 = municipality;
        brgyid2 =barangay;
    }
    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          provid :  GibberishAES.enc( ProvID2, toks),
          regCode:  GibberishAES.enc( regCode2, toks),
          fnctn:  GibberishAES.enc( "provincelist", toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='provaddr2']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          munid :  GibberishAES.enc( munid2, toks),
          ProvID:  GibberishAES.enc( ProvID2, toks) ,
          fnctn: GibberishAES.enc( "municipalitylist" , toks),
          toks:toks
        },
        success: function(msg){
           $("select[name='cityaddr2']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
            url: "<?=site_url('employee_/loadExtrasFunction')?>",
            type: "POST",
            data: {
              brgyid :  GibberishAES.enc( brgyid2, toks),
              munid:  GibberishAES.enc( munid2, toks),
              fnctn:  GibberishAES.enc( "barangaylist", toks),
              toks:toks
            },
            success: function(msg){
                $("select[name='barangay2']").html(msg).trigger("chosen:updated");
            }
        });
}


function addlegit(obj){
    $("#infoModal").find("h3[tag='title']").text(obj ? "Edit Legitimation Relations" : "Add Legitimation Relations");  
    $.ajax({
        url: "<?=site_url('employee_/legitimate')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#infoModal").find("#display");
            $.when($(modal_display).html(msg)).done(function(){ 
                if(obj){
                    var tdcur = $(obj).parent().parent();
                    $(tdcur).attr("iscurrent",1);
                    $(modal_display).find("input[name='lr_name']").val(tdcur.find("td:eq(0)").text()); 
                    //$(modal_display).find("input[name='lr_relationship']").val(tdcur.find("td:eq(1)").text());
                    $(modal_display).find("select[name='lr_relationship']").val(tdcur.find("td:eq(1)").attr("reldata")).trigger('chosen:updated');
                    $(modal_display).find("input[name='lr_address']").val(tdcur.find("td:eq(2)").text());
                    $(modal_display).find("input[name='lr_contactno']").val(tdcur.find("td:eq(3)").text()); 
                    $(modal_display).find("input[name='birthdate_lr']").val(tdcur.find("td:eq(4)").text());
                    $(modal_display).find("input[name='birthdate_lr']").parent().attr("data-date",tdcur.find("td:eq(4)").text());
                    $(modal_display).find("input[name='legit_lr']").prop("checked",tdcur.find("td:eq(5)").text()=="YES");
                }else{
                    $("#legitlist").find("tr").each(function(){
                    $(this).attr("iscurrent",0); 
                    }) 
                }
            });
        }
    });  
}

function call_shiftschedule(emptype,empshift){
    $.ajax({
        url: "<?=site_url('employee_/call_shiftschedule')?>",
        type: "POST",
        data: {
            emptype:  GibberishAES.enc( emptype, toks),
            empshift:  GibberishAES.enc( empshift, toks),
            toks:toks  
        },
        success: function(msg){
            $("select[name='empshift']").html(msg).trigger("chosen:updated");
        }
    });
}

function ImmiFields (abled) {
    $("#txtPassport").attr("disabled",abled);
    $("#txtVisa").attr("disabled",abled);
    $("#txtICARD").attr("disabled",abled);
    $("#txtCNR").attr("disabled",abled);
}

$(".tload").on('change', function() {
    $(".tload").not(this).prop('checked', false);
});

$(".tloadtype").on('change', function() {
    $(".tloadtype").not(this).prop('checked', false);
});

$(".accai").on('change', function() {
    $(".accai").not(this).prop('checked', false);
});

//$("#tinno").inputmask("mask", {"mask": "999-999-999"});
//$("#sssno").inputmask("mask", {"mask": "9999-9999999-9"});
$("#citytelno").inputmask("mask", {"mask": "+(999) 999-99-99"});
$("#mobile").inputmask("mask", {"mask": "9999-9999999"});
$("#landline").inputmask("mask", {"mask": "9999-9999"});
$("#spouse_mobile").inputmask("mask", {"mask": "9999-9999999"});
$('.chosen-select').chosen();

$(".echildren").click(function(){
    addchildren($(this));
});

$(".delete_emergency").click(function(){
    var mtable = $("#emergencycontactlist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
      if (result.value) {
        $(this).parent().parent().remove();
        delete_emergency($(this), $(this).attr("tbl_id"));
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Data is safe.',
                'error'
            )
        }
    })
    
});
$(".delete_entry").click(function(){
    var mtable = $("#childrenlist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    $(this).parent().parent().remove();
    delete_entry($(this), $(this).attr("tbl_id"));
});

$(".delete_family").click(function(){
    var mtable = $("#familylist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
      if (result.value) {
        $(this).parent().parent().remove();
        delete_family($(this), $(this).attr("tbl_id"));
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                'Data is safe.',
                'error'
            )
        }
    })
});

$(".employee_emergencyContact").click(function(){
    addchildren("");
});

$(".add_family").click(function(){
    addfamily("");
});

$(".edit_family").click(function(){
    addfamily($(this));
});

$(".add_emergencyContact").click(function(){
    addemergencycontact("");
});

$(".eEmergencyContact").click(function(){
    addemergencycontact($(this), $(this).attr("tbl_id"));
});

function addchildren(obj){
    var tbl_id = "";
    if(obj) tbl_id = obj.attr("tbl_id");
    $("#infoModal").find("h3[tag='title']").text(obj ? "Edit Children" : "Add Children");
    $("#button_save_modal").text("Save");  
    $.ajax({
        data: {applicant:  GibberishAES.enc("yes", toks), toks:toks},
        url: "<?=site_url('employee_/echildren')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#infoModal").find('#display');
            $.when($(modal_display).html(msg)).done(function(){ 
                if(obj){
                    var tdcur = $(obj).parent().parent();
                    $(tdcur).attr("iscurrent",1);
                    $(modal_display).find("input[name='tbl_id']").val(tbl_id); 
                    $(modal_display).find("input[name='eb_name']").val(tdcur.find("td:eq(0)").text()); 
                    $(modal_display).find("select[name='eb_gender']").val(tdcur.find("td:eq(1)").text());
                    $(modal_display).find("input[name='eb_b_order']").val(tdcur.find("td:eq(2)").text());
                    $(modal_display).find("input[name='eb_dob']").val(tdcur.find("td:eq(3)").text());
                    $(modal_display).find("input[name='eb_age']").val(tdcur.find("td:eq(4)").text());
                }else{
                    $("#childrenlist").find("tr").each(function(){
                        $(this).attr("iscurrent",0); 
                    }) 
                }
            });
        }
    });  
}

function addfamily(obj){
    var tbl_id = "";
    if(obj) tbl_id = obj.attr("tbl_id"); 
    $("#infoModal").find("h3[tag='title']").text(obj ? "Edit Family Member" : "Add Family Member");
    $("#button_save_modal").text("Save");  
    $.ajax({
        data: {applicant:  GibberishAES.enc("yes", toks), toks:toks},
        url: "<?=site_url('employee_/efamily')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#infoModal").find("#display");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_name']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("select[name='eb_relation']").val(tdcur.find("td:eq(1)").attr("reldata")).trigger("chosen:updated");
                 $(modal_display).find("input[name='eb_dob']").val(tdcur.find("td:eq(2)").text());
                 $(modal_display).find("input[name='tbl_id']").val(tbl_id);
              }else{
                 $("#familylist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            }); 
        }
    });  
}

function addemergencycontact(obj, tbl_id=''){
    var gender = $("#gender").val();
    var civil_status = $("#civil_status").val();
    $("#infoModal").find("h3[tag='title']").text(obj ? "Edit Emergency Contact" : "Add Emergency Contact");
    $("#button_save_modal").text("Save");  
    $.ajax({
        data: {applicant:GibberishAES.enc("yes", toks), applicantId: GibberishAES.enc($("input[name='applicantId']").val(), toks), tbl_id: GibberishAES.enc(tbl_id, toks), gender: GibberishAES.enc(gender, toks), civil_status: GibberishAES.enc(civil_status, toks), toks:toks},
        url: "<?=site_url('employee_/eEmergencyContact')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#infoModal").find('#display');
            $.when($(modal_display).html(msg)).done(function(){ 
                if(obj){
                    var tdcur = $(obj).parent().parent();
                    $(tdcur).attr("iscurrent",1);
                    $(modal_display).find("input[name='tbl_id']").val(tbl_id); 
                    $(modal_display).find("input[name='eb_name']").val(tdcur.find("td:eq(0)").text()); 
                    // $(modal_display).find("input[name='eb_relation']").val(tdcur.find("td:eq(1)").text());
                    $(modal_display).find("select[name='eb_relation']").val(tdcur.find("td:eq(1)").attr("reldata")).trigger("chosen:updated");
                    $(modal_display).find("input[name='eb_mobile']").val(tdcur.find("td:eq(2)").text());
                    $(modal_display).find("input[name='eb_homeNo']").val(tdcur.find("td:eq(3)").text());
                    $(modal_display).find("input[name='eb_officeNo']").val(tdcur.find("td:eq(4)").text());
                    $(modal_display).find("select[name='eb_type']").val(tdcur.find("td:eq(5)").attr("reltype")).trigger("chosen:updated");
                    // $(modal_display).find("input[name='eb_type']").val(tdcur.find("td:eq(5)").text());

                }else{
                    $("#eEmergencyContact").find("tr").each(function(){
                        $(this).attr("iscurrent",0); 
                    });
                }
            }); 
        }
    });  
}

function validateInputs(){
    if($("input[name='usertype']").val() == "ADMIN"){
        // $("#tab1").find("button, input, select").prop("disabled", true);
    }
}

$(".yesno").click(function(){var attname = $(this).attr("name");if($("input[name='"+attname+"']").prop("checked"))   $("input[name='"+attname+"']").not(this).prop("checked",false);});

$("input[name='bdate']").blur(function(){
    dob = new Date($(this).val());
    var today = new Date();
    var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
    $('#age').val(age);
});

if($("#civil_status option:selected").text() == "MARRIED")
{
    $("#spouse,#occupation,#spouse_mobile").attr("disabled",false).attr("required", true).css("background-color","transparent");
} 
else
{
    $("#spouse,#occupation,#spouse_mobile").val("").attr("disabled",true).attr("required", false).css("background-color","#EEEEEE").css("border-color","rgb(206 206 206)");
} 

$("#civil_status").change(function(){
    if($("#civil_status option:selected").text() == "MARRIED"){
        $("#spouse,#occupation,#spouse_mobile").val("").attr("disabled",false).attr("required", true).css("background-color","transparent");
    }
    else{
        $("#spouse,#occupation,#spouse_mobile").val("").attr("disabled",true).attr("required", false).css("background-color","#EEEEEE").css("border-color","rgb(206 206 206)");
        $("#spouse").focus();
    }
    var cNames = ['spouse_name', 'occupation', 'spouse_mobile'];
    $.each(cNames, function(n, cname){
        var formdata = {
            column:  GibberishAES.enc( cname, toks),
            value: GibberishAES.enc( "", toks),
            applicantId: GibberishAES.enc($("input[name='applicantId']").val()  , toks),
            toks:toks
        }
        $.ajax({
            url: "<?= site_url('applicant/updateApplicantInformation')?>",
            data: formdata,
            type: "POST",
            success:function(response){
                loadSuccessModals(cname);
            }
        });
    });

});

// $("#field_terms").click(function(){
//     alert("adsgafdsfasfs");
//     $('#mobile_2').after('<div class="red">Name is Required</div>');
//   });


$("input[type='checkbox']").click(function(){
    var name = $(this).attr("name");
    if(name == "healthcbox"){
        if($(this).val() == 2)  $("#txthealth").attr("disabled",true).css("background","#EEEEEE").val("");
        else                    $("#txthealth").attr("disabled",false).css("background","transparent").val("");
    }else if(name == "operationcbox"){
        if($(this).val() == 1)  $("#txtoperation,#txtoperationdate").attr("disabled",false).css("background","transparent").val("");
        else                    $("#txtoperation,#txtoperationdate").attr("disabled",true).css("background","#EEEEEE").val("");
    }else if(name == "medhiscbox"){
        if($(this).val() == 1)  $("#txtmedhis").attr("disabled",false).css("background","transparent").val("");
        else                    $("#txtmedhis").attr("disabled",true).css("background","#EEEEEE").val("");
    }
});

$(".disabled").each(function(){ $(this).attr("disabled",true).css("background","#EEEEEE").val("");  });

$(".edit_estat_history").click(function(){
    var employeeid      = "<?=$employeeid?>",
    management      = $(this).attr('mgmt'),
    deptid          = $(this).attr('dept'),
    employmentstat  = $(this).attr('estat'),
    position        = $(this).attr('pos'),
    datepos         = $(this).attr('datepos');

    $("#infoModal").find("h3[tag='title']").text("Edit Employment Status");
    $("#button_save_modal").text("Save");
    var form_data = {
        employeeid: employeeid,
        management: management,
        deptid: deptid,
        employmentstat: employmentstat,
        position: position,
        datepos: datepos,
        folder : "employee",
        page   : "estat_modal"
    };
    $.ajax({
        url: "<?=site_url('employee_/viewModal')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#infoModal").find("#display").html(msg);
        }
    });  
});

$(document).on("click",".delete_estat_history",function(){
    var id = $(this).attr("estatid");
    var delalert = $('#delete-alert').clone();
    // delalert.find('#chosen-row').html(id);
    delalert.find('#del-submit').attr('tagkey',id);
    delalert.removeClass('hide');

    $("#infoModal").find("h3[tag='title']").text("Delete Employment History");
    $("#infoModal").find("#display").html( delalert );
    $("#infoModal").modal('show');
});

$(document).on("click", '#del-submit', function(){
    var id = $(this).attr('tagkey');
    $("#infoModal").find("#display").html("<h3>Deleting...</h3>");
    $.ajax({
        url: "<?=site_url('employee_/deleteEStatHistory')?>",
        type: "POST",
        data: {estatid:id},
        dataType: 'JSON',
        success: function(msg){
            $("#infoModal").find("#display").html(msg.msg);
            $('#modalclose').click();
            if(msg.err_code == 0){
                $('a[estatid="'+id+'"').parent().parent().parent().remove();
            }else{
            }
        }
    });
});

///< to remove error messages
$('.chosen, input').on('change', function(){
    $(this).nextAll('.error:first').hide();
});

$('input').on('input', function(){
    $(this).css('border-color','#8f8f8f').nextAll('.error:first').hide();
});
// $('input').on('focus', function(){
//     $(this).css('border-color','red');
// });

// $('input').on('focusout', function(){
//     $(this).css('border-color','#8f8f8f');
// });

// ADDDED FOR DATE FORMATTING;
function dateParserForDatePicker(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
}

$("input, .chosen-select").on("blur change", function() {

    var column_name = $(this).attr("name");

    var column_value = $(this).val();
    var applicantId = $("input[name='applicantId']").val();

    if ($(this).hasClass("dateFormat")) {
        var d = new Date($(this).val());  
        column_value = dateParserForDatePicker(d);
        // alert(column_value);
    }

    if(column_name != null && column_value != null && applicantId != null){
        var formdata = {
            column:  GibberishAES.enc(column_name , toks),
            value:  GibberishAES.enc(column_value , toks),
            applicantId:  GibberishAES.enc(applicantId , toks),
            toks:toks
        };
        $.ajax({
            url: "<?= site_url('applicant/updateApplicantInformation')?>",
            data: formdata,
            type: "POST",
            success:function(response){
                loadSuccessModals(column_name);

            }
        });
    }
});

function delete_entry(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_children";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_children"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: "<?= site_url('applicant/deleteDatachildren')?>",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc( userid, toks), toks:toks},
        dataType: "JSON",
        success: function(msg){ 
            // console.log(data); return;
            loadChildrendatatable(userid);
            emergency_count -= 1;
        }
    });  
}

function delete_family(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_family";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_family"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: "<?= site_url('applicant/deleteData')?>",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc( userid, toks), toks:toks},
        dataType: "JSON",
        success: function(msg){ 
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfully deleted data.',
                showConfirmButton: true,
                timer: 1000
            })
        }
    });  
}

function delete_emergency(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_emergencyContact";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_emergencyContact"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: "<?= site_url('applicant/deleteData')?>",
        type: "POST",
        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc( userid, toks), toks:toks},
        dataType: "JSON",
        success: function(msg){ 
            emergency_count -= 1;
            disableAddEmergency();
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Successfully deleted data.',
                showConfirmButton: true,
                timer: 1000
            })
        }
    });  
}

function emergencyContactList(){
    emergency_count = $(".tbody_emergency tr").length;
    $("input[name='emergency_count']").val(emergency_count);
    console.log($("input[name='emergency_count']").val());
}
function childrenlist(){
    children_count = $(".tbody_children tr").length;
    $("input[name='children_count']").val(children_count);
    console.log($("input[name='children_count']").val());
}

function disableAddEmergency(){
    if(emergency_count >= 2) $(".add_emergencyContact").css("pointer-events", "none");
    else $(".add_emergencyContact").css("pointer-events", "");
}

$(".uploadPhoto").click(function(){
    var modalTitle = $(this).attr("modalTitle");
    var filename = $(this).attr("filename");
    var applicant = 1;
    $("#modal-view").find(".modal-footer").html('<button type="button" data-dismiss="modal" class="btn btn-danger modalclose" id="modalclose">Close</button> <button type="button" class="btn btn-success button_save_modal" id="button_save_modal">Save</button>');
    $("#modal-view").find("h3[tag='title']").text(modalTitle);
    $("#button_save_modal").text(modalTitle);
    $.ajax({
        url: "<?= site_url('employee_/uploadPhoto')?>",
        type: "POST",
        data: {filename:filename, applicant:applicant},
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });
});

function loadCheckboxStatus(){
        if($("#field_terms").is(':checked')){
            $("#permaRegions, #addr2, #permaProvinces, #barangays2, #permaMunicipalitys, #zip_code2").css("pointer-events", "none");
        }
    }


function checkCivilStatus(){
    if($("#civil_status option:selected").text() == "MARRIED"){
        $("#spouse,#occupation,#spouse_mobile").attr("disabled",false).css("background-color","transparent");
    }
    else{
        $("#spouse,#occupation,#spouse_mobile").val("").attr("disabled",true).css("background-color","#EEEEEE");
        $("#spouse").focus();
    }
}

function loadChildrendatatable(empid){
    var table = "applicant_children";
    $.ajax({
        url: "<?= site_url('employee_/childrenDataTable')?>",
        type: "POST",
        data: {table:table, employeeid:empid},
        success: function(msg){
            $("#tbody_children").html(msg);
        }
    });
}

function loadUploadedPhoto(employeeid){
    var formdata = {
        employeeid:  GibberishAES.enc(employeeid , toks),
        toks:toks
    };
    $.ajax({
        url: "<?= site_url('employee_/loadUploadedPhoto')?>",
        data: formdata,
        type: "POST",
        success: function(response) {
            $(".elfinderimg").removeAttr("src").attr("src", response).css("border", "2px solid #a1a1a1");
        }
    });
}

function loadSuccessModals(column_name, isbank=''){
    // if(!$('.success_modal').is(':visible')){
    //     $(".success_modal").modal("show");
    //     setTimeout(function(){ $(".success_modal").modal("hide"); }, 800);
    // }
    if(column_name != 'datehired'){
        if(column_name == "nationalityid") column_name = "selNationality";
        if(column_name == "citizenid") column_name = "citizenship";
        if(column_name == "religionid") column_name = "religion";
        if(column_name == "spouse_name") column_name = "spouse";
        if(column_name == "regionaladdr") column_name = "region";
        if(column_name == "provaddr") column_name = "selProvince";
        if(column_name == "cityaddr") column_name = "selMunicipality";
        if(column_name == "permaRegion") column_name = "permaRegionselect";
        if(column_name == "bdate" ){
            $("#"+column_name+"_saveAlert").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1.6%; margin-left: 2%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
        }else if(column_name == "prc_expiration" || column_name == "passport_expiration" || column_name == "date_active"){
            $("#"+column_name+"_saveAlert").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1.6%; margin-left: 5%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
        }
        else if(column_name == "bdate"){
            $("#clearResigned").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 3%; margin-left: 28%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
        }else if(column_name == "teachingtype" || column_name == "isactive"){
            $("#"+column_name).parent("div").parent("div").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1%; margin-left: -15%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
        }else if(column_name == "resigned_reason"){
            $("#"+column_name).parent("div").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1%; margin-left: 2%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
        }else if(column_name == "aimcheckbox"){
            $("#"+column_name).parent("div").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 0%; margin-left: -70%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
        }else if(isbank){
            column_name = isbank;
            $("input[bank='"+isbank+"']").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1%; margin-left: 30%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>')
        }else{
            $("#"+column_name).parent("div").after('<span class="glyphicon glyphicon-ok-circle " id="'+column_name+'_saved" style="color: green; font-size: 20px; position: absolute; margin-top: 1.6%; margin-left: -1%;"><p style="font-family: "Poppins", sans-serif; font-size: 12px; margin-top: -45%; margin-left: 50%; font-weight: 600; color: green"></p></span>');
        }
        // var x = document.getElementById("snackbar");
        // x.className = "show";
        // setTimeout(function(){ x.className = x.className.replace("show", ""); }, 1500);
        setTimeout(function(){ $("#"+column_name+"_saved").fadeOut("slow") }, 1000);
        setTimeout(function(){ $("#"+column_name+"_saved").remove(); }, 2000);
    }
}

</script>




