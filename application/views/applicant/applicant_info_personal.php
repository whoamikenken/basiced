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
    $employeeid = $employeecode = $nname = $cityaddr = $provaddr = $regaddr = $addr = $cityaddr2 = $provaddr2 = $regaddr2 = $addr2 = $blood_type = $height = $weight = $gender = $email= $civil_status = $spouse = $bdate = $mobile = $citytelno = $employmentstat = $emptype = $empshift = $dateemployed = $campusid = $maxregular = $maxparttime = $bplace = $deptid = $assignment = $remarks = $position = $management = $dateresigned = $dateresigned2 = $datepos = $resigned_reason = $tinno = $sssno = $philhealth = $pagibig = $peraa = $medicare = $emp_bank = $emp_accno = $citizenship = $religion = $nationality = $prc = $passport = $visa = $icard = $crnno = $permanent_address = $cp_name = $cp_relation = $cp_address = $cp_mobile = $cp_telno = $teaching = $teachingtype = $accai = $leavetype = $occupation = $mother = $motheroccu = $father = $fatheroccu = $hosp = $hosptxt = $operation = $operationtxt = $operationdate = $medhistory = $medhistorytxt = $medconditions = $age = $date_active = $distinguishingMarks = $zip_code = $barangay = $zip_code2 = $barangay2 = $dates=$ages= $aims = $aimsdept= $aimscb = $prc_expiration = $passport_expiration = $emp_hmo = $landline = "";     
    $legitimate_relations = array();
    $employment_history = array();
    $applicable_field = $applicable_children = $applicable_emergencyContact = $applicable_skill = "";


    $ishidden = $isdisabled = $isreadonly = "";
    $cansave = true;
    $ishidden   = " hidden";
    $iquery     = $this->db->query("SELECT * FROM elfinder_file where title='$employeeid'");

    if(!$applicantId) $applicantId = $CI->applicantt->getApplicantId($lname, $fname, $mname);

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

            // $cityaddr2 = $empdetails['cityaddr2'];
            // $provaddr2 = $empdetails['provaddr2'];
            // $regaddr2 = $empdetails['regionaladdr2'];
            // $addr2 = $empdetails['addr2'];
            // $zip_code2 = $empdetails['zip_code2'];
            // $barangay2 = $empdetails['barangay2'];

            $occupation = $empdetails['occupation'];
            $age    = $empdetails['age'];
            $gender = $empdetails['gender'];
            $civil_status = $empdetails['civil_status'];
            // civil_status
            $spouse = $empdetails['spouse_name'];
            $bdate = isset($empdetails['bdate']) ? $empdetails['bdate'] : "";
            $mobile = $empdetails['mobile'];
            $citytelno = $empdetails['citytelno'];
            $email = $empdetails['email'];
            $employmentstat = $empdetails['employmentstat'];
            $emptype = $empdetails['emptype'];
            $empshift = $empdetails['empshift'];
            $date_active = $empdetails['date_active'];

            $dateemployed = isset($empdetails['dateemployed']) ? $empdetails['dateemployed'] : "";
            $maxregular = $empdetails['maxregular'];
            $maxparttime = $empdetails['maxparttime'];
            $bplace = $empdetails['bplace'];
            $blood_type = $empdetails['blood_type'];
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
            $sssno = $empdetails['emp_sss'];
            $philhealth = $empdetails['emp_philhealth'];
            $pagibig = $empdetails['emp_pagibig'];
            $peraa = $empdetails['emp_peraa'];
            $emp_bank = $empdetails['emp_bank'];
            $medicare = $empdetails['emp_medicare'];
            $emp_accno = $empdetails['emp_accno'];
            $citizenship = $empdetails['citizenid'];
            $religion = $empdetails['religionid'];
            $nationality = $empdetails['nationalityid'];
            $prc = $empdetails['prc'];
            $passport = $empdetails['passport'];
            $visa = $empdetails['visa'];
            $icard = $empdetails['icardnum'];
            $weight = $empdetails['weight'];
            $height = $empdetails['height'];
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
            // $landline = $empdetails['landline'];
            $cp_relation = $empdetails['cp_relation'];
            $cp_address = $empdetails['cp_address'];
            $cp_mobile = $empdetails['cp_mobile'];
            $cp_telno = $empdetails['cp_telno'];
            $teaching = $empdetails['teaching'];
            $teachingtype = $empdetails['teachingtype'];
            $accai = $empdetails['isactive'];
            // $applicable_emergencyContact = $empdetails['emergencyContactcbox'];

            // $employment_history = $this->employee->getEmploymentStatusHistory($employeeid);
            $applicable_field   = $this->db->query("SELECT * FROM applicant_applicable_fields WHERE employeeid='$applicantId'");
            if($applicable_field->num_rows > 0) $applicable_children = $applicable_field->row(0)->children;
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



</style>
<div class="widgets_area">
    <div class="row">
    <form id="info">
       <div class="col-md-12">
        <!-- <a href="#" name='backlist'>Back to employee list</a> -->
            <input type="hidden" name="job" value="employee/personal_info">
            <input name="usertype" type="hidden" value="<?=$usertype?>"/>
            <div class="form-group"><br>
                <center>
                <table>
                    <tr>
                        <td class="align_right" rowcol-md-="2" width="40%"><img src="<?=base_url()?>images/school_logo.png" style="width: 100px;" /></td>
                        <td class="align_left" valign='bottom' style="padding: 0;"><h4 style="font-size: 23px;font-family: Book Antiqua;margin-left: 10px;"><b><?= $this->extras->school_name()?></b></h4></td>

                    </tr>
                    <tr>

                        <td class="align_left" valign='baseline' style="padding: 0;"><h5 style="font-size: 19px;margin-left: 1%; margin-top: -1%; font-family: Book Antiqua"><strong><?= $this->extras->school_tag()?></strong></h5></td>
                    </tr>
                </table>
                </center>
            </div>
            <?if($cansave){?>

<!--                 <div class='align_right'><label class="text-info" style="color:red; font-size: 16px;   ">
                    <b>(Click SAVE for each tab you accomplish)</b></label> 
                    <a href="#" class="btn btn-primary" id="saveinfo"> Save</a> -->
                    <!-- <a class="btn btn-primary" id="print_out">Print</a> -->
              <!--   </div> -->
            <?}?><br>
            <!-- porgress bar -->
            <!-- <br /> -->
                <div class="panel">
                    <div class="panel-heading" style="background-color: #3b5998;"><h4><b>GENERAL INFORMATION</b></h4></div>
                    <div class="panel-body"  style="background: #ebeced;">
                        <div class="pull-right" style="width: 35%;" >
                            <?if($iquery->num_rows() > 0){?>
                                <img class="elfinderimg" src="<?=site_url('forms/loadForm')?>?form=imgview&eid=<?=$employeeid?>" style="float: right;position: absolute;width: 150px;"/>
                            <?}else{?>
                                <img class="elfinderimg" src="<?=base_url()?>images/no_image.gif" style="float: right;position: absolute;width: 150px;"/>
                            <?}?>
                        </div>
                        <input type="hidden" name="applicantId" value="<?=$applicantId?>">
                        <div class="form-group">
                            <div class="col-md-12">
                                <?php if(!isset($applicantId)){ ?>
                                    <col-md- id="refresh"><input type="checkbox" name="isnew" class="cbox" checked></col-md->
                                    <h1>wewew</h1>
                                <?php } ?>
                                <label class="col-md-3">Applicant ID</label>
                                <div class="field">
                                    <input class="form-control col-md-4 required" name="applicantId" type="text" value="<?= $applicantId?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <?$q = $this->db->query("SELECT * from code_position");?>
                            <label  for="label100" class="col-md-3">Applying For:</label>
                            <div class="field">
                                <select class="chosen" name="positionid">
                                    <?
                                    foreach ($q->result() as $key => $row) {?>
                                        <option value="<?=$row->positionid?>" <?=$positionid==$row->positionid?"selected":""?>><?=$row->description?></option>
                                    <?}
                                    ?>
                                </select>
                            </div> 
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-md-3">First Name</label>
                                <div class="field">
                                    <input class="form-control required col-md-4" name="fname" type="text" value="<?=$fname?>"<?=($fname?" readonly":"")?>/>
                                </div> 
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-md-3">Last Name</label>
                                <div class="field">
                                    <input class="form-control required col-md-4" name="lname" type="text" value="<?=$lname?>"<?=($lname?" readonly":"")?>/>
                                </div> 
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-md-3">Middle Name</label>
                                <div class="field">
                                    <input class="form-control required col-md-4" name="mname" type="text" value="<?=$mname?>"<?=($mname?" readonly":"")?>/>
                                </div> 
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label  for="employeeid" class="col-md-3">Nick Name</label>
                                <div class="field">
                                    <input class="form-control required col-md-4" name="nname" id="nname" type="text" value="<?=$nname?>"<?=($nname?" readonly":"")?>/>
                                </div> 
                            </div>
                        </div>
                        <br><br>
                        <div class="panel-heading" style="background: #3b5998;">
                            <h5 style="color: #ffffff;font-weight: bold;font-size: 12px;">IDENTIFICATION NUMBERS</h5>
                        </div>
                        <div class="panel-body no-search" style="background: #ebeced;">
                            <br>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="field_name align_right">TIN #</label>
                                        <div class="field">
                                            <input type="text" class="col-md-10 required" id="emp_tin" name="emp_tin" value="<?=$tinno?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="field_name align_right">SSS #</label>
                                        <div class="field">
                                            <input type="text" class="col-md-10 required" id="emp_sss" name="emp_sss" value="<?=$sssno?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="field_name align_right">PhilHealth</label>
                                        <div class="field">
                                            <input type="text" class="col-md-10 required" name="emp_philhealth" id="emp_philhealth" value="<?=$philhealth?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4" >
                                    <div class="form-group">
                                        <label class="field_name align_right">PAG-IBIG</label>
                                        <div class="field">
                                            <input type="text" class="col-md-10 required" name="emp_pagibig" id="emp_pagibig" value="<?=$pagibig?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="field_name align_right">PERAA</label>
                                        <div class="field">
                                            <input type="text" class="col-md-10" name="emp_peraa" id="emp_peraa" value="<?=$peraa?>"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="field_name align_right">PRC#</label>
                                        <div class="field">
                                            <input class="col-md-10 required" name="prc" id="prc" type="text" value="<?=$prc?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-4" style="min-width: 275px;">
                                    <label class="field_name align_right">Bank</label>
                                    <div class="field">
                                        <div class="col-md-10 no-search">
                                            <select class="<?=($isreadonly ? "" : "chosen ")?>" name="emp_bank" <?=$isreadonly?>>
                                            <?
                                              $opt_type = $CI->utils->getBankList("Select bank..");
                                              foreach($opt_type as $c=>$val){
                                              ?><option<?=($c==$emp_bank ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                              }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="field_name align_right">Account No.</label>
                                        <div class="field">
                                            <input type="text" class="col-md-10 required" name="emp_accno" id="acc_no" value="<?=$emp_accno?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
                    <br>
                <div class="panel">
                    <div class="panel-heading" style="background-color: #3b5998;"><h4><b>PERSONAL INFORMATION</b></h4></div>
                    <div class="panel-body no-search" style="background: #ebeced;">
                        <div class="form-group">
                            <div class="col-md-4" >                
                                <label class="field_name align_right">Date of Birth</label>
                                <div class="field">
                                    <div class="col-md-10 input-append date birthdate" data-date="<?=date("F d-Y",strtotime($bdate))?>" >
                                        <input class="col-md-10 align_center dateFormat" size="16" name="bdate" type="text" id="bdate" value="<?=date("F d-Y",strtotime($bdate))?date("F d-Y",strtotime($bdate)):""?>" readonly>
                                        <col-md- class="add-on"><i class="icon-calendar"></i></col-md->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4" >                
                                <label class="field_name align_right">Place of Birth</label>
                                <div class="field">
                                    <input class="col-md-10 required" name="bplace" id="bplace" type="text" value="<?=$bplace?>"/>
                                </div>
                            </div>         
                            <div class="col-md-4" >                
                                <label class="field_name align_right">Age</label>
                                <div class="field">
                                <?php if ($age == "" && $bdate=="") {
                                    $ages = "";
                                }
                                else
                                    $ages =$this->extras->computeAge($bdate); 
                                ?>
                               
                                    <input class="col-md-10 required" type="text" name="age" id="age" value="<?=$ages?>" readonly=""/><!-- $this->extras->computeAge($bdate) -->
                                </div>
                            </div>             
                        </div>
                        <div class="form-group">
                            <div class="col-md-4" style="min-width: 275px;">
                                <label class="field_name align_right">Civil Status</label>
                                <div class="field no-search">
                                        <select class="chosen required" name="civil_status" id="civil_status">
                                        <?
                                          $opt_civil_stat = $this->extras->listCivilStatus();
                                          foreach ($opt_civil_stat as $key => $stat) {?>
                                            <option value="<?=$key?>" <?= ($civil_status == $key) ? "selected" : ""?> ><?=$stat?></option>
                                          <?}
                                        ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="field_name align_right">Spouse</label>
                                <div class="field">
                                    <input class="col-md-10" name="spouse_name" id="spouse_name" type="text" value="<?=$spouse?>"/>
                                    <col-md- id="isrequireds" hidden=""></col-md->
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="field_name align_right">Occupation</label>
                                <div class="field">
                                    <input class="col-md-10" name="occupation" id="occupation" type="text" value="<?=$occupation?>"/>
                                    <col-md- id="isrequireds" hidden=""></col-md->
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4" style="min-width: 275px;">
                                <label class="field_name align_right">Citizenship</label>
                                <div class="field no-search">
                                        <select class="chosen" name="citizenid" id="citizenid">
                                        <?
                                          $opt_type = $this->extras->showCitizenship();
                                          foreach($opt_type as $c=>$val){
                                          ?><option<?=($c==$citizenship ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                          }
                                        ?>
                                        </select>
                                        <col-md- id="isrequiredc" hidden=""></col-md->
                                </div>
                            </div>
                            <br>
                            <div class="col-md-4" style="min-width: 275px;">
                                <div class="form-group">
                                    <label class="field_name align_right">Gender</label>
                                    <div class="field no-search">
                                            <select class="chosen" name="gender">
                                            <?
                                              $opt_gender = $this->extras->showgender();
                                              foreach($opt_gender as $c=>$val){
                                              ?><option<?=($c==$gender ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                              }
                                            ?>
                                            </select>
                                            <col-md- id="isrequiredgen" hidden=""></col-md->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="field_name align_right">Contact Number</label>
                                <div class="field">
                                    <input type="text" name='mobile' class="col-md-10 required" id="mobile" value="<?=$mobile?>"/>
                                </div>
                            </div>    
                        </div>
                        <div class="form-group">
                            <div class="col-md-4" style="min-width: 275px;">
                                <label class="field_name align_right">Religion</label>
                                <div class="field no-search">
                                        <select class="chosen" name="religionid" id="religionid">
                                        <?
                                          $opt_type = $this->extras->showReligion();
                                          foreach($opt_type as $c=>$val){
                                          ?><option<?=($c==$religion ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                          }
                                        ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-4" style="min-width: 275px;">
                                <label class="field_name align_right">Nationality</label>
                                <div class="field no-search">
                                        <select class="chosen" name="nationalityid" id="selNationality">
                                        <?
                                          $opt_type = $this->extras->showNationality();
                                          foreach($opt_type as $c=>$val){
                                          ?><option<?=($c==$nationality ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                          }
                                        ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="field_name align_right">Email Address</label>
                                <div class="field">
                                    <input type="text" name='email' class="col-md-10 required" id="email" value="<?=$email?>"/>
                                </div>
                            </div>  
                        </div>
                        
                        <!-- <div class="form-group">
                            <div class="col-md-2">
                                <label class="align_right">City Address</label>
                            </div>
                            <div class="col-md-9">
                                <input class="col-md-12" name="cityaddr" id="cityaddr" type="text" value="<?=$cityaddr?>"/>
                            </div>
                        </div> -->
                        <div class="form-group">
                            <label class="field_name align_center" style='font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;'> Address: &nbsp;&nbsp;&nbsp;</label>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4" style="min-width: 275px;">
                                <label class="field_name align_right">Region</label>
                                <div class="field no-search">
                                        <select class="chosen" name="regionaladdr" id="region">
                                        <?
                                          $add = $this->extras->regionlist();
                                          foreach($add as $c=>$val){
                                          ?><option<?=($c==$regaddr ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                          }
                                        ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-4" style="min-width: 275px;">
                                <label class="field_name align_right">Province</label>
                                <div class="field no-search">
                                        <select class="chosen" name="provaddr" id="selProvince">
                                            <option value="">Choose a province ...</option>
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-4" style="min-width: 275px;">
                                <label class="field_name align_right">Municipality</label>
                                <div class="field no-search">
                                        <select class="chosen" name="cityaddr" id="selMunicipality">
                                            <option value="">Choose a municipality ...</option>
                                        </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">  
                            <div class="col-md-2">
                                <label class="align_right">Rm # Bldg./House#, Street</label>
                            </div>
                            <div class="col-md-9">
                                <input class="col-md-12" name="addr" id="addr" type="text" value="<?=$addr?>"/>
                            </div>
                        </div>
                        <div class="form-group">  
                            <div class="col-md-4">
                                <label class="field_name align_right">Barangay</label>
                                <div class="field">
                                     <input class="col-md-6" name="barangay" id="barangay" type="text" value="<?=$barangay?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="field_name align_right">Zip Code</label>
                                <div class="field">
                                    <input class="col-md-4" name="zip_code" id="zip_code" type="text" maxlength="4" value="<?=$zip_code?>"/>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="form-group">  
                            <div class="col-md-2">
                                <label class="align_right">Distinguishing Marks</label>
                            </div>
                            <div class="col-md-9">
                                <input class="col-md-12" name="distinguishingMarks" id="distinguishingMarks" type="text" value="<?=$distinguishingMarks?>"/>
                            </div>
                        </div> -->

                        <div class="form-group">
                            <div class="col-md-4" >                
                                <label class="field_name align_right" style="font-size:.8em">Height</label>
                                <div class="field">
                                    <input name="height" style="text-transform: uppercase;" id="height" type="text" value="<?=$height?>" placeholder="- ft -" style="width: 80px;" <?=$isreadonly?>/>
                                </div>
                            </div>
                            <div class="col-md-4" >                
                                <label class="field_name align_right" style="font-size:.8em">Weight</label>
                                <div class="field">
                                    <input name="weight" style="text-transform: uppercase;" id="weight" type="text" value="<?=$weight?>" placeholder="- kg -" style="width: 80px;" <?=$isreadonly?>/>
                                </div>
                            </div>    
                            <div class="col-md-4"  style="min-width: 275px;">
                                <label class="field_name align_right" style="font-size:.8em">Blood Type</label>
                                <div class="field no-search">
                                        <select class="chosen" name="blood_type" id="blood_type" <?=$isdisabled?>>
                                        <?
                                          $opt_type = $CI->utils->getBloodTypes();
                                          foreach($opt_type as $c=>$val){
                                          ?><option<?=($c==$blood_type ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                          }
                                        ?>
                                        </select>
                                </div>
                            </div>
                        </div>
                         <div class="form-group">
                            <label class="field_name align_center" style='font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;'> Parents: &nbsp;&nbsp;&nbsp;</label>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="field_name align_right">Mother</label>
                                <div class="field">
                                    <input class="col-md-10" name="mother" id="mother" type="text" value="<?=$mother?>"/>
                                    <col-md- id="isrequireds" hidden=""></col-md->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="field_name align_right">Occupation</label>
                                <div class="field">
                                    <input class="col-md-10" name="motheroccu" id="motheroccu" type="text" value="<?=$motheroccu?>"/>
                                    <col-md- id="isrequireds" hidden=""></col-md->
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6">
                                <label class="field_name align_right">Father</label>
                                <div class="field">
                                    <input class="col-md-10" name="father" id="father" type="text" value="<?=$father?>"/>
                                    <col-md- id="isrequireds" hidden=""></col-md->
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="field_name align_right">Occupation</label>
                                <div class="field">
                                    <input class="col-md-10" name="fatheroccu" id="fatheroccu" type="text" value="<?=$fatheroccu?>"/>
                                    <col-md- id="isrequireds" hidden=""></col-md->
                                </div>
                            </div>
                        </div>
                </div>
                <div class="panel">
                <div class="panel-heading" style="background-color: #3b5998;"><h4><b>Number of Children</b></h4></div>
                    <div class="panel-body no-search" style="background: #ebeced;">
                    <div>
                        <input type="checkbox" name="childrencbox" id="childcBox" class="applicable-field" <?= ($applicable_children == "0" ? "checked" : "") ?> >
                        <col-md- style="font-style: italic;">&nbsp;Check this box if Not Applicable</col-md->
                    </div>
                    <table class="table table-hover" id="childrenlist">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Date of Birth</th>
                                <th>Age</th>
                                <th class="col-md-2">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?
                        
                            $employee_child = $this->db->query("select * from employee_children where employeeid='$employeeid'")->result();
                                if(count($employee_child)>0){
                                    foreach($employee_child as $eb){
                        ?>
                                        <tr>
                                            <td><?=$eb->name?></td>
                                            <td><?=$eb->birthdate?></td>
                                            <td><?=$eb->age?></td>
                                            <td>
                                                <a class='btn orange echildren' href='#modal-view' data-toggle='modal'><i class='icon-edit'></i></a><a class='btn orange delete_entry'><i class='icon-trash'></i></a>
                                            </td>
                                        </tr>    
                        <?                            
                                    }
                               }else{
                        ?>
                                    <tr>
                                        <td colcol-md-="6">No existing data</td>
                                    </tr>
                        <?                    
                                }
                        ?>                      
                       </tbody>
                    </table>
                    <a class="btn blue" href="#modal-view" tag="add_children" data-toggle="modal">Add Children</a>
                </div>
            </div>
                
                        
            <!--Emergency Contact-->
            <div class="panel">
                <div class="panel-heading" style="background-color: #3b5998;"><h4><b>Emergency Contact Information</b></h4></div>
                <div class="panel-body no-search" style="background: #ebeced;">
                    <div>
                        <input type="checkbox" name="emergencyContactcbox" id="eciBox" class="applicable-field" <?= ($applicable_emergencyContact == "0" ? "checked" : "") ?> >
                        <col-md- style="font-style: italic;">&nbsp;Check this box if Not Applicable</col-md->
                    </div>
                    <table class="table table-hover" id="emergencycontactlist">
                       <thead>
                          <tr>
                             <th>Name</th>
                             <th>Relation</th>
                             <th>Mobile #</th>
                             <th>Home #</th>
                             <th>Office #</th>
                             <th class="col-md-2">&nbsp;</th>
                          </tr>
                       </thead>
                       <tbody>
    <?
            $employee_emergencyContact = $this->db->query("select * from employee_emergencyContact where employeeid='$employeeid'")->result();
                   if(count($employee_emergencyContact)>0){
                        foreach($employee_emergencyContact as $eb){
    ?>
                        <tr>
                            <td><?=$eb->name?></td>
                            <td><?=$eb->relation?></td>
                            <td><?=$eb->mobile?></td>
                            <td><?=$eb->homeNo?></td>
                            <td><?=$eb->officeNo?></td>
                            <td>
                                <a class='btn orange eEmergencyContact' href='#modal-view' data-toggle='modal'><i class='icon-edit'></i></a><a class='btn orange delete_entry'><i class='icon-trash'></i></a>
                            </td>
                        </tr>    
    <?                            
                        }
                   }else{
    ?>
                        <tr>
                            <td colcol-md-="6">No existing data</td>
                        </tr>
    <?                    
                   }
    ?>                      
                       </tbody>
                    </table>
                    <a class="btn blue" href="#modal-view" tag="add_emergencyContact" data-toggle="modal">Add Emergency Contact</a>
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
                        <h4 class="media-heading" style="font-family: Avenir;"><b>ATENEO DE ILOILO</b></h4>
                        <p style="font-family: Avenir;  margin-top: -1.5%;">Santa Maria Catholic School</p>
                    </div>
                </div>
                <center><b><h3 tag="title" class="modal-title">Modal Header</h3></b></center>
            </div>
            <div class="modal-body">
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
</div>
<script>
var oneHundredPer = 1096.8;
var progress = 0;
var emergency_count = 0;
var ProvID = "<?=$provaddr?>";
var munid = "<?=$cityaddr?>";
var regCode = "<?=$regaddr?>";

var ProvID2 = "<?=$provaddr2?>";
var munid2 = "<?=$cityaddr2?>";
var regCode2 = "<?=$regaddr2?>";
emergencyContactList();
childrenlist();
validateInputs();
loadCityAndProvinces();
loadPermanentCityAndProvinces();
disableAddEmergency();

$(document).ready(function(){

    

    if($("#isprocessed").val() >= 1){
        $("input, button, select").attr("disabled", true);
        $("select").trigger("chosen:updated");
    }
    else{ 
        $("input, button").attr("disabled", false);
        $("select").attr("disabled", false);
        $("select").trigger("chosen:updated");
    }

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
            var region= $("#region").val();
            var addr= $("#addr").val();
            var province=$("#selProvince").val();
            var barangay=$("#barangay").val();
            var municipality=$("#selMunicipality").val();
            var zip_code=$("#zip_code").val();
            $("#region1").val(region).trigger("chosen:updated");
            $("#addr2").val(addr).trigger("chosen:updated");
            $("#selProvince2").val(province).trigger("chosen:updated");
            $("#barangay2").val(barangay).trigger("chosen:updated");
            $("#selMunicipality1").val(municipality).trigger("chosen:updated");
            $("#zip_code2").val(zip_code).trigger("chosen:updated");
            var applicantId = $("input[name='applicantId']").val();
            var cAddr = [region, addr, province, barangay, municipality, zip_code];
            var cName = ['regionaladdr2', 'addr2', 'provaddr2', 'barangay2', 'cityaddr2', 'zip_code2'];
            $.each(cAddr, function(a, caddr){
                $.each(cName, function(n, cname){
                    if(a == n){
                        var formdata = {
                        column: cname,
                        value: caddr,
                        applicantId: applicantId
                    }
                    $.ajax({
                        url: "<?= site_url('applicant/updateApplicantInformation')?>",
                        data: formdata,
                        type: "POST",
                        succes:function(response){
                        }
                    });
                    }
                });
            });

        }
        else if($(this).prop("checked") == false){
            // alert("Checkbox is unchecked.");
            // var region1= $("#region1").val();
            $("#region1").val("").trigger("chosen:updated");
            $("#addr2").val("").trigger("chosen:updated");
            $("#selProvince2").val("").trigger("chosen:updated");
            $("#barangay2").val("").trigger("chosen:updated");
            $("#selMunicipality1").val("").trigger("chosen:updated");
            $("#zip_code2").val("").trigger("chosen:updated");
            var applicantId = $("input[name='applicantId']").val();
            var cAddr = [region, addr, province, barangay, municipality, zip_code];
            var cName = ['regionaladdr2', 'addr2', 'provaddr2', 'barangay2', 'cityaddr2', 'zip_code2'];
            $.each(cAddr, function(a, caddr){
                $.each(cName, function(n, cname){
                    if(a == n){
                        var formdata = {
                        column: cname,
                        value: "",
                        applicantId: applicantId
                    }
                    $.ajax({
                        url: "<?= site_url('applicant/updateApplicantInformation')?>",
                        data: formdata,
                        type: "POST",
                        succes:function(response){
                        }
                    });
                    }
                });
            });
        }
    });
});


$("#childcBox").change(function(){
    if ($("#childcBox").prop("checked") == true) $("#add_children").prop("disabled", true);
    else $("#add_children").prop("disabled", false);
});

$("#eciBox").change(function(){
    if ($("#eciBox").prop("checked") == true) $("#add_emergencyContact").prop("disabled", true);
    else $("#add_emergencyContact").prop("disabled", false);
});

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
            provid : ProvID,
            regCode: regCode ,
            fnctn: "provincelist"
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
            munid : munid,
            ProvID: ProvID ,
            fnctn: "municipalitylist"
        },
        success: function(msg){
            $("select[name='cityaddr']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            provid : ProvID2,
            regCode: regCode2 ,
            fnctn: "provincelist"
        },
        success: function(msg){
            $("select[name='province2']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            munid : munid2,
            ProvID: ProvID2 ,
            fnctn: "municipalitylist"
        },
        success: function(msg){
            $("select[name='cityaddr2']").html(msg).trigger("chosen:updated");
        }
    });

});

$("select[name='regionaladdr']").change(function() {
    var regCode = $(this).val();

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            provid : "",
            regCode: regCode ,
            fnctn: "provincelist"
        },
        success: function(msg){
            $("select[name='provaddr']").html(msg).trigger("chosen:updated");
            $("select[name='cityaddr']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
        }
    });
});

$("select[name='regionaladdr2']").change(function() {
    var regCode = $(this).val();

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            provid : "",
            regCode: regCode ,
            fnctn: "provincelist"
        },
        success: function(msg){
            $("select[name='provaddr2']").html(msg).trigger("chosen:updated");
            $("select[name='cityaddr2']").html('<option value="">Choose a municipality ...</option>').trigger("chosen:updated");
        }
    });
});

$("select[name='provaddr']").change(function() {
    var ProvID = $(this).val();
    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            munid : "",
            ProvID: ProvID ,
            fnctn: "municipalitylist"
        },
        success: function(msg){
            $("select[name='cityaddr']").html(msg).trigger("chosen:updated");
        }
    });
});

$("select[name='provaddr2']").change(function() {
    var ProvID = $(this).val();
    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
            munid : "",
            ProvID: ProvID ,
            fnctn: "municipalitylist"
        },
        success: function(msg){
            $("select[name='cityaddr2']").html(msg).trigger("chosen:updated");
        }
    });
});

$("#selNationality").change(function() {
    ImmiFields(($("#selNationality").val() > 1) ? false : true);
});

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
            employeeid : "<?=$employeeid?>",
            tnt        : "<?=$teachingtype?>",
            empshift   : newempshift,
            date_active: newdateactive
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
          provid : ProvID,
          regCode: regCode ,
          fnctn: "provincelist"
        },
        success: function(msg){
           $("select[name='provaddr']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          munid : munid,
          ProvID: ProvID ,
          fnctn: "municipalitylist"
        },
        success: function(msg){
           $("select[name='cityaddr']").html(msg).trigger("chosen:updated");
        }
    });
}


function loadPermanentCityAndProvinces(){
    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          provid : ProvID2,
          regCode: regCode ,
          fnctn: "provincelist"
        },
        success: function(msg){
           $("select[name='provaddr2']").html(msg).trigger("chosen:updated");
        }
    });

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          munid : munid2,
          ProvID: ProvID2 ,
          fnctn: "municipalitylist"
        },
        success: function(msg){
           $("select[name='cityaddr2']").html(msg).trigger("chosen:updated");
        }
    });
}

function addlegit(obj){
    $("#infoModal").find("h3[tag='title']").text(obj ? "Edit Legitimation Relations" : "Add Legitimation Relations");  
    $.ajax({
        url: "<?=site_url('applicant/alegitimate')?>",
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
            emptype: emptype,
            empshift: empshift  
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
$("#mobile").inputmask("mask", {"mask": "+(99999) 999-99-99"});
$('.chosen-select').chosen();

$(".echildren").click(function(){
    addchildren($(this));
});

$(".delete_emergency").click(function(){
    var mtable = $("#emergencycontactlist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colcol-md-='6'>No existing data</td></tr>");
    $(this).parent().parent().remove();
    delete_emergency($(this), $(this).attr("tbl_id"));
});

$(".delete_entry").click(function(){
    var mtable = $("#childrenlist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colcol-md-='6'>No existing data</td></tr>");
    $(this).parent().parent().remove();
    delete_entry($(this), $(this).attr("tbl_id"));
});

$(".delete_skill").click(function(){
    var mtable = $("#skilllist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colcol-md-='6'>No existing data</td></tr>");
    $(this).parent().parent().remove();
    delete_entry($(this), $(this).attr("tbl_id"));
});

$(".employee_emergencyContact").click(function(){
    addchildren("");
});

$(".add_emergencyContact").click(function(){
    addemergencycontact("");
});

$(".eEmergencyContact").click(function(){
    addemergencycontact($(this));
});
$("a[tag='add_skill']").click(function(){
    addskill("");
});

function addchildren(obj){
    var tbl_id = "";
    if(obj) tbl_id = obj.attr("tbl_id");
    $("#infoModal").find("h3[tag='title']").text(obj ? "Edit Children" : "Add Children");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('applicant/achildren')?>",
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

function addemergencycontact(obj){
    var tbl_id = "";
    if(obj) tbl_id = obj.attr("tbl_id");
    $("#infoModal").find("h3[tag='title']").text(obj ? "Edit Emergency Contact" : "Add Emergency Contact");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('applicant/aEmergencyContact')?>",
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
                    $(modal_display).find("select[name='eb_relation']").val(tdcur.find("td:eq(1)").attr("rel"));
                    $(modal_display).find("input[name='eb_mobile']").val(tdcur.find("td:eq(2)").text());
                    $(modal_display).find("input[name='eb_homeNo']").val(tdcur.find("td:eq(3)").text());
                    $(modal_display).find("input[name='eb_officeNo']").val(tdcur.find("td:eq(4)").text());
                    $(modal_display).find("select[name='eb_type']").val(tdcur.find("td:eq(5)").attr("reltype"));
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

function addskill(obj){
    $("#infoModal").find("h3[tag='title']").text(obj ? "Edit Skill" : "Add Skill");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('applicant/aSkill')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#infoModal").find("#display");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_name']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("input[name='eb_yearOfUse']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='eb_level']").val(tdcur.find("td:eq(2)").text());
                }else{
                 $("#skilllist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            }); 
        }
    });  
}

function validateInputs(){
    if($("input[name='usertype']").val() == "ADMIN"){
        $("#tab1").find("button, input, select").prop("disabled", true);
    }
}

$(".yesno").click(function(){var attname = $(this).attr("name");if($("input[name='"+attname+"']").prop("checked"))   $("input[name='"+attname+"']").not(this).prop("checked",false);});

$("input[name='bdate']").blur(function(){
    dob = new Date($(this).val());
    var today = new Date();
    var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
    $('#age').val(age);
});

if($("#civil_status").val() == "1")   $("#spouse,#occupation").val("").attr("disabled",true).css("background-color","#EEEEEE");

$("#civil_status").change(function(){
    if($(this).val() != "1"){
        $("#spouse,#occupation").val("").attr("disabled",false).css("background-color","transparent");
    }
    else{
        $("#spouse,#occupation").val("").attr("disabled",true).css("background-color","#EEEEEE");
        $("#spouse").focus();
    }
});

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
    return date.getFullYear()+"-"+('0'+(date.getDate())).slice(-2)+"-"+('0'+(date.getMonth()+1)).slice( -2 );
}

$("input, .chosen-select").on("blur change", function() {
    var column_name = $(this).attr("name");
    var column_value = $(this).val();
    var applicantId = $("input[name='applicantId']").val();

    if ($(this).hasClass("dateFormat")) {
        var d = new Date($(this).val());  
        column_value = dateParserForDatePicker(d);
    }

    if(column_name != null && column_value != null && applicantId != null){
        var formdata = {
            column: column_name,
            value: column_value,
            applicantId: applicantId
        };
        $.ajax({
            url: "<?= site_url('applicant/updateApplicantInformation')?>",
            data: formdata,
            type: "POST",
            succes:function(response){
            }
        });
    }
});

function delete_entry(obj, tbl_id = ""){
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
        url: "<?= site_url('applicant/deleteDatachildren')?>",
        type: "POST",
        data: {table:table, tbl_id:tbl_id, employeeid: userid},
        dataType: "JSON",
        success: function(msg){ 
            // console.log(data); return;
            emergency_count -= 1;
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
        data: {table:table, tbl_id:tbl_id, employeeid: userid},
        dataType: "JSON",
        success: function(msg){ 
            emergency_count -= 1;
            disableAddEmergency();
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

$(".deleterelation").click(function(){
    var mtable = $("#legitlist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colcol-md-='6'>No existing data</td></tr>");
    $(this).parent().parent().parent().remove();
    deleterelation($(this), $(this).attr("tbl_id"));
});

function deleterelation(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_legitimate_relations";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_legitimate_relations"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: "<?= site_url('employee_/deleteData')?>",
        type: "POST",
        data: {table:table, tbl_id:tbl_id, employeeid: userid},
        dataType: "JSON",
        success: function(msg){ 
            
        }
    });  
}

$(".delete_skill").click(function(){
    var mtable = $("#skilllist").find("tbody");
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colcol-md-='6'>No existing data</td></tr>");
    $(this).parent().parent().parent().remove();
    deleteskill($(this), $(this).attr("tbl_id"));
});

function deleteskill(obj, tbl_id = ""){
    var table = "";
    var userid = "";
    if($("input[name='applicantId']").val()){
        table = "applicant_skills";
        userid = $("input[name='applicantId']").val();
    }
    else{
        table = "employee_skills"; 
        userid = $("input[name='employeeid']").val();
    }
    $.ajax({
        url: "<?= site_url('employee_/deleteData')?>",
        type: "POST",
        data: {table:table, tbl_id:tbl_id, employeeid: userid},
        dataType: "JSON",
        success: function(msg){ 
            
        }
    });  
}


</script>




