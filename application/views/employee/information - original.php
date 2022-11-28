<?php
/**
 * @author Justin
 * @copyright 2016
 */
 
    $employeeid = $employeecode = $fname = $lname = $mname = $cityaddr = $provaddr = $regaddr = $addr = $gender = $civil_status = $spouse = $bdate = $mobile = $citytelno = $email = $employmentstat = $emptype = $empshift = $dateemployed = $maxregular = $maxparttime = $bplace = $deptid = $assignment = $remarks = $position = $management = $dateresigned = $datepos = $resigned_reason = $tinno = $sssno = $philhealth = $pagibig = $peraa = $medicare = $emp_accno = $citizenship = $religion = $nationality = $prc = $passport = $visa = $icard = $crnno = $permanent_address = $cp_name = $cp_relation = $cp_address = $cp_mobile = $cp_telno = $teaching = $teachingtype = $accai = $leavetype = $occupation = $mother = $motheroccu = $father = $fatheroccu = $hosp = $hosptxt = $operation = $operationtxt = $operationdate = $medhistory = $medhistorytxt = $medconditions = $age = "";     
    $legitimate_relations = array();
    $employment_history = array();
 
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
    $cityaddr = $empdetails['cityaddr'];
    $provaddr = $empdetails['provaddr'];
    $regaddr = $empdetails['regaddr'];
    $addr = $empdetails['addr'];
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
    $dateemployed = isset($empdetails['dateemployed']) ? $empdetails['dateemployed'] : "";
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
    $dateresigned = $empdetails['dateresigned'] ? $empdetails['dateresigned'] : NULL;
    $resigned_reason = $empdetails['resigned_reason'];
    $tinno = $empdetails['tinno'];
    $sssno = $empdetails['sssno'];
    $philhealth = $empdetails['philhealth'];
    $pagibig = $empdetails['pagibig'];
    $peraa = $empdetails['peraa'];
    $medicare = $empdetails['medicare'];
    $emp_accno = $empdetails['emp_accno'];
    $citizenship = $empdetails['citizenship'];
    $religion = $empdetails['religion'];
    $nationality = $empdetails['nationality'];
    $prc = $empdetails['prc'];
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
    $teaching = $empdetails['teaching'];
    $teachingtype = $empdetails['teachingtype'];
    $accai = $empdetails['isactive'];

    $employment_history = $this->employee->getEmploymentStatusHistory($employeeid);
    print_r($employment_history);
 }
 $ishidden = $isdisabled = $isreadonly = "";
 $cansave = true;
 if($this->session->userdata("usertype") == "EMPLOYEE"){
   $ishidden   = " hidden";
   $isdisabled = " style='pointer-events: none;'";
   $isreadonly = " style='pointer-events: none;'";
   $cansave   = $this->db->query("SELECT * FROM employee_restriction WHERE employeeid='$employeeid'")->num_rows();
 }
$iquery     = $this->db->query("SELECT * FROM elfinder_file where title='$employeeid'");
?>
<style>@media (max-width: 768px) { .elfinderimg{   display: none;  }   } .error{color: red;}</style>
<div class="widgets_area">
<div class="row">
<form id="info">
    <div class="col-md-12">
            <a href="#" name='backlist'>Back to employee list</a>
            <div class="form_row">
                <table style="width: 500px;margin-left: 25%;padding: 0;">
                    <tr>
                        <td class="align_right" rowspan="2" width="30%"><img src="<?=base_url()?>images/school_logo.jpg" style="width: 60px;" /></td>
                        <td class="align_center" valign='bottom' style="padding: 0;"><h4 style="font-size: 23px;font-family: Courier New;"><b>MANILA CENTRAL UNIVERSITY</b></h4></td>
                    </tr>
                    <tr>
                        <td class="align_center" valign='baseline' style="padding: 0;"><h5 style="font-size: 11px;"><strong>A prime institution in the field of medical science</strong></h5></td>
                    </tr>
                </table>
            </div>
            <?if($cansave){?>
            <div class='align_right'><label class="text-info">
                <b>(Click SAVE for each tab you accomplish)</b></label> 
                <a href="#" class="btn btn-primary" id="saveinfo">Save</a>
                <a class="btn btn-primary" id="print_out">Print</a>
            </div>
            <?}?>
            <br />
            <div class="well-header" style="background: #823982;">
                <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">GENERAL INFORMATION</h5>
            </div>
            <div class="well-content no-search">
                
                <div class="pull-right" style="width: 35%;" >
                    <?if($iquery->num_rows() > 0){?>
                        <img class="elfinderimg" src="<?=site_url('forms/loadForm')?>?form=imgview&eid=<?=$employeeid?>" style="float: right;position: absolute;width: 150px;"/>
                    <?}else{?>
                        <img class="elfinderimg" src="<?=base_url()?>images/no_image.gif" style="float: right;position: absolute;width: 150px;"/>
                    <?}?>
                </div>
                
                <div class="form_row" >
                    <label class="field_name align_right">Employee ID</label>
                    <div class="field">
                        <input class="col-md-4 required" name="employeeid" type="text" value="<?=$employeeid?>"<?=($employeeid?" readonly":"")?>/>
                        <input class="hidden" name="employeecode" type="text" value="<?=$employeecode?>" />
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">First Name</label>
                    <div class="field">
                        <input class="col-md-4 required" name="fname" type="text" value="<?=$fname?>"/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Last Name</label>
                    <div class="field">
                        <input class="col-md-4 required" name="lname" type="text" value="<?=$lname?>"/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Middle Name</label>
                    <div class="field">
                        <input class="col-md-4 required" name="mname" type="text" value="<?=$mname?>"/>
                    </div>
                </div>                
            </div>
            <div class="well-header" style="background: #823982;">
                <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Identification Numbers</h5>
            </div>
            <div class="well-content no-search">
                <div class="form_row">
                    <div class="col-md-4">
                        <div class="form_row">
                            <label class="field_name align_right">TIN #</label>
                            <div class="field">
                                <input type="text" class="col-md-10 required" id="tinno" name="tinno" value="<?=$tinno?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form_row">
                            <label class="field_name align_right">SSS #</label>
                            <div class="field">
                                <input type="text" class="col-md-10 required" id="sssno" name="sssno" value="<?=$sssno?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form_row">
                            <label class="field_name align_right">PhilHealth</label>
                            <div class="field">
                                <input type="text" class="col-md-10 required" name="philhealth" value="<?=$philhealth?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="col-md-4" >
                        <div class="form_row">
                            <label class="field_name align_right">PAG-IBIG</label>
                            <div class="field">
                                <input type="text" class="col-md-10 required" name="pagibig" value="<?=$pagibig?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form_row">
                            <label class="field_name align_right">HMO</label>
                            <div class="field">
                                <input type="text" class="col-md-10 required" name="medicare" value="<?=$medicare?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form_row">
                            <label class="field_name align_right">PRC#</label>
                            <div class="field">
                                <input class="col-md-10 required" name="prc" id="prc" type="text" value="<?=$prc?>"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="col-md-4">
                        <div class="form_row">
                            <label class="field_name align_right">Account No.</label>
                            <div class="field">
                                <input type="text" class="col-md-10 required" name="empaccno" value="<?=$emp_accno?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="well-header" style="background: #823982;" <?=$ishidden?>>
                <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Employee Information</h5>
            </div>
            <div class="well-content no-search" <?=$ishidden?>>
                <div class="form_row" <?=$ishidden?>>
                    <div class="col-md-4" >                
                        <label class="field_name align_right">Cluster Head</label>
                        <div class="field">
                            <div class="col-md-12 no-search">
                                <input type="checkbox" class="tload" name="tload" id="tload" value="academic" <?= $teaching == "academic" ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" />&nbsp;&nbsp;Academic
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" class="tload" name="tload" id="tload" value="admin" <?= $teaching == "admin" ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" />&nbsp;&nbsp;Admin
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" >                
                        <label class="field_name align_right">Type</label>
                        <div class="field">
                            <div class="col-md-12 no-search">
                                <input type="checkbox" class="tloadtype" name="tloadtype" id="tloadtype" value="teaching" <?= $teachingtype == "teaching" ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" <?=$isdisabled?> />&nbsp;&nbsp;Teaching
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" class="tloadtype" name="tloadtype" id="tloadtype" value="nonteaching" <?= $teachingtype == "nonteaching" ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" <?=$isdisabled?> />&nbsp;&nbsp;Non-Teaching
                            </div>
                        </div>
                    </div>         
                    <div class="col-md-4" >                
                        <label class="field_name align_right">Account</label>
                        <div class="field">
                            <div class="col-md-12 no-search">
                                <input type="checkbox" class="accai" name="accai" id="accai" value="1" <?=  $accai ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" />&nbsp;&nbsp;Active
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="checkbox" class="accai" name="accai" id="accai" value="0" <?= !$accai ? " checked" : "" ?> style="-webkit-transform: scale(1.5);" />&nbsp;&nbsp;In-Active
                            </div>
                        </div>
                    </div>             
                </div>
                <div class="form_row" <?=$ishidden?>>
                    <!-- <div class="col-md-4">
                        <label class="field_name align_right">Leave Type</label>
                        <div class="field">
                            <div class="col-md-10 no-search">                        
                                <select class="<?=($isreadonly ? "" : "chosen ")?> col-md-11" name="leave_type" id="leave_type" <?=$isreadonly?>><?=$this->extras->leavetype($leavetype);?></select>
                            </div>                            
                        </div>
                    </div> -->
                    <div class="col-md-4">
                        <label class="field_name align_right">Shift Type</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="<?=($isreadonly ? "" : "chosen ")?> col-md-11 required" name="emptype" <?=$isreadonly?>>
                                <?
                                  $opt_type = $this->extras->showemployeetype();
                                  foreach($opt_type as $c=>$val){
                                  ?><option<?=($c==$emptype ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                  }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field_name align_right">Shift Schedule</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="<?=($isreadonly ? "" : "chosen ")?> col-md-11 required" name="empshift" <?php //print(isset($empshift) ? "disabled=\"true\"" : ""); ?> <?=$isdisabled?>>
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
                                    <option><?php print($empshift); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field_name align_right">Date Employed</label>
                        <div class="field">
                            <div class="col-md-9 input-group date dateemployed" data-date="<?=($dateemployed != "1970-01-01" ? date("Y-m-d",strtotime($dateemployed)) : "")?>" data-date-format="yyyy-mm-dd">
                                <input class="align_center col-md-9 " size="16" type="text" name="dateemployed" value="<?=($dateemployed != "1970-01-01" ? date("Y-m-d",strtotime($dateemployed)) : "")?>" readonly <?=$isreadonly?>>
                                <span class="add-on" <?=$isreadonly?>><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                </div>   
                <div class="form_row">
                    <div class="col-md-4">
                        <div class="form_row" <?=$ishidden?>>
                            <label class="field_name align_right">Date Resigned</label>
                            <div class="field">
                                <div class="col-md-9 input-group date dateresigned" data-date="<?=($dateresigned != "1970-01-01" ? date("Y-m-d",strtotime($dateresigned)) : "")?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center col-md-9" size="16" type="text" name="dateresigned" value="<?=($dateresigned != "1970-01-01" ? date("Y-m-d",strtotime($dateresigned)) : "")?>" readonly <?=$isreadonly?>>
                                    <span class="add-on" <?=$isreadonly?>><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field_name align_right">Reason</label>
                        <div class="field">
                            <input type="text" class="col-md-8" name="reason" value="<?=$resigned_reason?>" <?=$isreadonly?>/>
                        </div>
                    </div>
                </div> 
                <div class="form_row">
                    <div class="col-md-4">
                        <label class="field_name align_right">Position</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="<?=($isreadonly ? "" : "chosen ")?> col-md-11 required" name="position" id="position" <?=$isreadonly?>>
                                <?
                                  $opt_type = $this->extras->showPostion();
                                  foreach($opt_type as $c=>$val){
                                  ?><option<?=($c==$position ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                  }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="form_row" id="divreq" hidden="">
                            <div class="field">
                                <div class="col-md-4 no-search">
                                <span id="isrequired" hidden=""></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field_name align_right">Date</label>
                        <div class="field">
                            <div class="col-md-9 input-group date datepos" data-date="<?=$datepos?>" data-date-format="yyyy-mm-dd">
                                <input class="col-md-9 align_center" type="text" name="datepos" value="<?=$datepos?>" readonly <?=$isreadonly?>>
                                <span class="add-on" <?=$isreadonly?>><i class="glyphicon glyphicon-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-4" <?=$ishidden?>>
                        <label class="field_name align_right">Assignment</label>
                        <div class="field">
                            <div class="col-md-8">
                                <input type="text" class="col-md-12" name="assignment" value="<?=$assignment?>" <?=$isreadonly?>/>
                            </div>
                        </div>
                    </div> -->
                </div>
                
                <div class="form_row">
                    <!-- <div class="col-md-4" <?=$ishidden?>>
                        <label class="field_name align_right">Remarks</label>
                        <div class="field">
                            <input type="text" class="col-md-8" name="remarks" value="<?=$remarks?>" <?=$isreadonly?>/>
                        </div>
                    </div> -->
                    <div class="col-md-4" <?=$ishidden?>>
                        <label class="field_name align_right">Management Level</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="<?=($isreadonly ? "" : "chosen ")?> col-md-11 required" name="management" <?=$isreadonly?>>
                                <?
                                  $opt_type = $this->extras->showManagement();
                                  foreach($opt_type as $c=>$val){
                                  ?><option<?=($c==$management ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                  }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field_name align_right">Department</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="<?=($isreadonly ? "" : "chosen ")?> col-md-11 required" name="deptid" <?=$isreadonly?>>
                                <?
                                  $opt_department = $this->extras->showdepartment();
                                  foreach($opt_department as $c=>$val){
                                  ?><option<?=($c==$deptid ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                  }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field_name align_right">Employee Status</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="<?=($isreadonly ? "" : "chosen ")?>col-md-11 " name="employmentstat" <?=$isreadonly?>>
                                <?
                                  $opt_status = $this->extras->showemployeestatus();
                                  foreach($opt_status as $c=>$val){
                                  ?><option<?=($c==$employmentstat ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                  }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!--
                <div class="form_row">
                    <label class="field_name align_right">Maximum Full-time Load</label>
                    <div class="field">
                        <input type="text" class="col-md-2" name="maxregular" value="<?=$maxregular?>"/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Maximum Part-time Load</label>
                    <div class="field">
                        <input type="text" class="col-md-2" name="maxparttime" value="<?=$maxparttime?>"/>
                    </div>
                </div>
                -->
            </div>


            <!-- estat -->
            <div class="well-header" style="background: #823982;" <?=$ishidden?>>
                            <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Employment Status</h5>
                        </div>
            <div class="well-content no-search" <?=$ishidden?>>
                <div class="form_row">
                    <span class="col-md-1">&nbsp;</span>
                    <span class="col-md-2">
                        <span class="col-md-12 text-center"><b>Management Level</b></span>
                        <span id="currentMgmt" class="col-md-12 text-center" style="margin-left: 0px;"><?=$this->extras->getemployeemlevel($management)?></span>
                    </span>
                    <span class="col-md-2">
                        <span class="col-md-12 text-center"><b>Department</b></span>
                        <span id="currentDept" class="col-md-12 text-center" style="margin-left: 0px;"><?=$this->extras->getemployeedepartment($deptid)?></span>
                    </span>
                    <span class="col-md-2">
                        <span class="col-md-12 text-center"><b>Employee Status</b></span>
                        <span id="currentEStatus" class="col-md-12 text-center" style="margin-left: 0px;"><?=$this->extras->getemployeestatus($employmentstat)?></span>
                    </span>
                    <span class="col-md-2">
                        <span class="col-md-12 text-center"><b>Position</b></span>
                        <span id="currentPos" class="col-md-12 text-center" style="margin-left: 0px;"><?=$this->extras->showPosDesc($position)?></span>
                    </span>
                    <span class="col-md-2">
                        <span class="col-md-12 text-center"><b>Start Date</b></span>
                        <span id="currentDatepos" class="col-md-12 text-center" style="margin-left: 0px;"><?=$datepos?></span>
                    </span>
                    <span class="col-md-1">
                        <span class="col-md-12 text-center"></span>
                        <span class="pull-center">
                            <a class='btn btn-danger edit_estat_history' mgmt="<?=$management?>" dept="<?=$deptid?>" estat="<?=$employmentstat?>" pos="<?=$position?>" datepos="<?=$datepos?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
                        </span>
                    </span>

                </div>
                <div class="form_row">
                    <hr><b>History</b>
                </div>
                <div class="form_row">
                    <span class="col-md-1">&nbsp;</span>
                    <span class="col-md-2 text-center"><b>Management Level</b></span>
                    <span class="col-md-2 text-center"><b>Department</b></span>
                    <span class="col-md-2 text-center"><b>Employee Status</b></span>
                    <span class="col-md-2 text-center"><b>Position</b></span>
                    <span class="col-md-2 text-center"><b>Start Date</b></span>
                    <span class="col-md-1">
                    </span>
                </div>
                <div id="estatHistory">
                     <?  
                        foreach ($employment_history as $key => $obj): ?>
                            
                            <div class="form_row">
                                <span class="col-md-1">&nbsp;</span>
                                <span class="col-md-2 text-center"><?=$obj->mgmtdesc?></span>
                                <span class="col-md-2 text-center"><?=$obj->deptdesc?></span>
                                <span class="col-md-2 text-center"><?=$obj->statdesc?></span>
                                <span class="col-md-2 text-center"><?=$obj->posdesc?></span>
                                <span class="col-md-2 text-center"><?=$obj->dateposition?></span>
                                <span class="col-md-1">
                                    <span class="pull-center">
                                         <a class='btn btn-danger delete_estat_history' estatid="<?=$obj->id?>"><i class='glyphicon glyphicon-trash'></i></a>        
                                    </span>
                                </span>
                            </div>

                        <?endforeach;
                    ?>
                </div>
            </div>




            <div class="well-header" style="background: #823982;">
                <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Personal Information</h5>
            </div>
            <div class="well-content no-search">
                <div class="form_row">
                    <div class="col-md-4" >                
                        <label class="field_name align_right">Date of Birth</label>
                        <div class="field">
                            <div class="col-md-10 input-group date birthdate" data-date="<?=date("Y-m-d",strtotime($bdate))?>" data-date-format="yyyy-mm-dd">
                                <input class="col-md-10 align_center" size="16" name="birthdate" type="text" value="<?=date("Y-m-d",strtotime($bdate))?>" readonly>
                                <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
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
                            <input class="col-md-10 required" type="text" name="age" id="age" value="<?=$age ? $age : $this->extras->computeAge($bdate)?>" readonly=""/>
                        </div>
                    </div>             
                </div>
                <div class="form_row">
                    <div class="col-md-4">
                        <label class="field_name align_right">Civil Status</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="chosen col-md-10 required" name="civil_status" id="civil_status">
                                <?
                                  $opt_civil_stat = $this->extras->listCivilStatus();
                                  foreach ($opt_civil_stat as $key => $stat) {
                                    print("<option value=\"".$key."\" ".(($civil_status == $key) ? "selected" : "" ).">".$stat."</option>");
                                  }
                                ?>
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field_name align_right">Spouse</label>
                        <div class="field">
                            <input class="col-md-10" name="spouse" id="spouse" type="text" value="<?=$spouse?>"/>
                            <span id="isrequireds" hidden=""></span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field_name align_right">Occupation</label>
                        <div class="field">
                            <input class="col-md-10" name="occupation" id="occupation" type="text" value="<?=$occupation?>"/>
                            <span id="isrequireds" hidden=""></span>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="col-md-4">
                        <label class="field_name align_right">Citizenship</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="chosen col-md-10" name="citizenship">
                                <?
                                  $opt_type = $this->extras->showCitizenship();
                                  foreach($opt_type as $c=>$val){
                                  ?><option<?=($c==$citizenship ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                  }
                                ?>
                                </select>
                                <span id="isrequiredc" hidden=""></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form_row">
                            <label class="field_name align_right">Gender</label>
                            <div class="field">
                                <div class="col-md-10 no-search">
                                    <select class="chosen col-md-10" name="gender">
                                    <?
                                      $opt_gender = $this->extras->showgender();
                                      foreach($opt_gender as $c=>$val){
                                      ?><option<?=($c==$gender ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
                                      }
                                    ?>
                                    </select>
                                    <span id="isrequiredgen" hidden=""></span>
                                </div>
                                
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
                <div class="form_row">
                    <div class="col-md-4">
                        <label class="field_name align_right">Religion</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="chosen col-md-10" name="religion">
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
                    <div class="col-md-4">
                        <label class="field_name align_right">Nationality</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="chosen col-md-10" name="nationality" id="selNationality">
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
                </div>
                <!-- <div class="form_row">
                    <div class="col-md-2">
                        <label class="align_right">City Address</label>
                    </div>
                    <div class="col-md-9">
                        <input class="col-md-12" name="cityaddr" id="cityaddr" type="text" value="<?=$cityaddr?>"/>
                    </div>
                </div> -->
                <div class="form_row">
                    <label class="field_name align_center" style='font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;'> Address: &nbsp;&nbsp;&nbsp;</label>
                </div>
                <div class="form_row">
                    <div class="col-md-4">
                        <label class="field_name align_right">Region</label>
                        <div class="field">
                            <div class="col-md-10 no-search" id="regionDiv">
                                <select class="chosen col-md-10" name="region">
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
                    <div class="col-md-4">
                        <label class="field_name align_right">Province</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="chosen col-md-10" name="province" id="selProvince">
                                    <option value="">Choose a province ...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="field_name align_right">Municipality</label>
                        <div class="field">
                            <div class="col-md-10 no-search">
                                <select class="chosen col-md-10" name="municipality" id="selMunicipality">
                                    <option value="">Choose a municipality ...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form_row">  
                    <div class="col-md-2">
                        <label class="align_right">Rm # Bldg./House#, Street, Brgy.</label>
                    </div>
                    <div class="col-md-9">
                        <input class="col-md-12" name="addr" id="addr" type="text" value="<?=$addr?>"/>
                    </div>
                </div>
                
                <div class="form_row">
                    <label class="field_name align_center" style='font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;'> Parents: &nbsp;&nbsp;&nbsp;</label>
                </div>
                <div class="form_row">
                    <div class="col-md-6">
                        <label class="field_name align_right">Mother</label>
                        <div class="field">
                            <input class="col-md-10" name="mother" id="mother" type="text" value="<?=$mother?>"/>
                            <span id="isrequireds" hidden=""></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="field_name align_right">Occupation</label>
                        <div class="field">
                            <input class="col-md-10" name="motheroccu" id="motheroccu" type="text" value="<?=$motheroccu?>"/>
                            <span id="isrequireds" hidden=""></span>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="col-md-6">
                        <label class="field_name align_right">Father</label>
                        <div class="field">
                            <input class="col-md-10" name="father" id="father" type="text" value="<?=$father?>"/>
                            <span id="isrequireds" hidden=""></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="field_name align_right">Occupation</label>
                        <div class="field">
                            <input class="col-md-10" name="fatheroccu" id="fatheroccu" type="text" value="<?=$fatheroccu?>"/>
                            <span id="isrequireds" hidden=""></span>
                        </div>
                    </div>
                </div>
            <div class="well-header" style="background: #823982;">
                <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Number of Children</h5>
            </div>
            <div class="well-content no-search">
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
                            <a class='btn btn-danger echildren' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-danger delete_entry'><i class='glyphicon glyphicon-trash'></i></a>
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
                <a class="btn btn-primary" href="#modal-view" tag="add_children" data-toggle="modal">Add Children</a>
            </div>
                
                
                <!-- Immigration Details-->
                <div class="form_row" <?=$ishidden?>>
                    <label class="field_name align_right">Immigration Details:</label>
                </div>
                <div class="form_row" <?=$ishidden?>>
                    <label class="field_name align_right">Passport Info (ID#, Date Issued)</label>
                    <div class="field">
                        <input class="col-md-4 required" name="passport" id="txtPassport" type="text" value="<?=$passport?>"/>
                    </div>
                </div>
                <div class="form_row" <?=$ishidden?>>
                    <label class="field_name align_right">Visa Info (ID#, Date Issued)</label>
                    <div class="field">
                        <input class="col-md-4 required" name="visa" id="txtVisa" type="text" value="<?=$visa?>"/>
                    </div>
                </div>
                <div class="form_row" <?=$ishidden?>>
                    <label class="field_name align_right">ICARD #</label>
                    <div class="field">
                        <input class="col-md-4 required" name="icard" id="txtICARD" type="text" value="<?php print(($icard != "") ? $icard : ""); ?>" />
                    </div>
                </div>
                <div class="form_row" <?=$ishidden?>>
                    <label class="field_name align_right">CRN #</label>
                    <div class="field">
                        <input class="col-md-4 required" name="crn" id="txtCNR" type="text" value="<?=$crnno?>"/>
                    </div>
                </div>
            </div>
            <div class="well-header" style="background: #823982;">
                <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">CONFIDENTIAL</h5>
            </div>
            <div class="well-content no-search">
                <div class="form_row">
                    <label class="field_name align_center" style='font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;'> Health: &nbsp;&nbsp;&nbsp;</label>
                </div>
                <div class="form_row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-11">
                        <label class="align_center"> &nbsp;&nbsp;&nbsp;a.) Have you ever been hospitalized in the past 2 years?</label>&nbsp;
                        <input class="yesno" type="checkbox" name="healthcbox" value="1" <?=$hosp == 1 ? " checked" : ""?> /> Yes 
                        <input class="yesno" type="checkbox" name="healthcbox" value="2" <?=$hosp == 2 ? " checked" : ""?>/> No <br />
                        <label class="align_center"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If yes, for what sickness?</label>&nbsp;
                        <input type="text" class="col-md-9 <?=($hosp == 1 ? "required" : "disabled")?>" id="txthealth" name="txthealth" value="<?=$hosptxt?>"/>
                    </div>
                </div>
                <div class="form_row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-11">
                        <label class="align_center"> &nbsp;&nbsp;&nbsp;b.) Have you undergone any operation?</label>&nbsp;
                        <input class="yesno" type="checkbox" name="operationcbox" value="1" <?=$operation == 1 ? " checked" : ""?> /> Yes 
                        <input class="yesno" type="checkbox" name="operationcbox" value="2" <?=$operation == 2 ? " checked" : ""?> /> No <br />
                        <label class="align_center"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If yes, for what sickness?</label>&nbsp;
                        <input type="text" class="col-md-5 <?=($operation == 1 ? "required" : "disabled")?>" id="txtoperation" name="txtoperation" value="<?=$operationtxt?>"/>
                        <label class="align_center"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;When?</label>&nbsp;
                        <input type="text" class="col-md-3 <?=($operation == 1 ? "required" : "disabled")?>" id="txtoperationdate" name="txtoperationdate" value="<?=$operationdate?>"/>
                    </div>
                </div>
                <div class="form_row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-11">
                        <label class="align_center"> &nbsp;&nbsp;&nbsp;c.) Do you have any present or past medical history which will involve special consideration as to job assignment?</label>&nbsp;
                        <input class="yesno" type="checkbox" name="medhiscbox" value="1" <?=$medhistory == 1 ? " checked" : ""?> /> Yes 
                        <input class="yesno" type="checkbox" name="medhiscbox" value="2" <?=$medhistory == 2 ? " checked" : ""?> /> No <br />
                        <label class="align_center"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;If so, indicate the condition?</label>&nbsp;
                        <input type="text" class="col-md-9 <?=($medhistory == 1 ? "required" : "disabled")?>" id="txtmedhis" name="txtmedhis" value="<?=$medhistorytxt?>"/>
                    </div>
                </div>
                <div class="form_row">
                    <div class="col-md-1">&nbsp;</div>
                    <div class="col-md-11">
                        <label class="align_center"> &nbsp;&nbsp;&nbsp;d.) Check any of these conditions you have or have had:</label>
                        <br />
                        <div class="col-md-1">&nbsp;</div>
                        <div class="col-md-10">
                            <input type="checkbox" name="adbox" value="1" <?= (in_array(1,explode(",",$medconditions)) ? " checked" : "")?> /> Allergic Disorders (Asthma,fever,hives) <br />
                            <input type="checkbox" name="ccbox" value="2" <?= (in_array(2,explode(",",$medconditions)) ? " checked" : "")?>/> Cardiovascular conditions (Elevated blood pressure,anemia,heart,abnormalities) <br />
                            <input type="checkbox" name="gpbox" value="3" <?= (in_array(3,explode(",",$medconditions)) ? " checked" : "")?>/> Gastrointestinal problems (ulcers,liver desease, browel problems) <br />
                            <input type="checkbox" name="mbox" value="4"  <?= (in_array(4,explode(",",$medconditions)) ? " checked" : "")?>/> Musculoskeletal (fractured bone,disc or joint problems) <br />
                        </div>
                    </div>
                </div>
            </div>
                        
            <div class="well-header" style="background: #823982;">
                <h5 style="color: #FFC700;font-weight: bold;font-size: 14px;">Tax Dependents</h5>
            </div>
            <div class="well-content no-search">
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
                                <a class='btn btn-danger editrelation' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-danger deleterelation'><i class='glyphicon glyphicon-trash'></i></a>
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
                <a class="btn btn-primary" href="#modal-view" tag="add_legit" data-toggle="modal">Add Relation</a>
            </div>
            <!--
            <div class="well-content no-search" style="border: 0px !important;">            
            <div class="form_row">
                <div class="field">
                    <a href="#" class="btn btn-primary" id="saveinfo">Save</a>
                    <a class="btn btn-primary" id="print_out">Print</a>
                </div>
            </div>
            </div>
            -->
        </div>
</form>        
</div>
</div>
<script>

$(document).ready(function() {
    ImmiFields(true);

    var ProvID = "<?=$provaddr?>";
    var munid = "<?=$cityaddr?>";
    var regCode = "<?=$regaddr?>";

    $.ajax({
        url: "<?=site_url('employee_/loadExtrasFunction')?>",
        type: "POST",
        data: {
          provid : ProvID,
          regCode: regCode ,
          fnctn: "provincelist"
        },
        success: function(msg){
           $("select[name='province']").html(msg).trigger("liszt:updated");
           $("select[name='municipality']").html('<option value="">Choose a municipality ...</option>').trigger("liszt:updated");
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
           $("select[name='municipality']").html(msg).trigger("liszt:updated");
        }
    });

});

$("select[name='region']").change(function() {
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
           $("select[name='province']").html(msg).trigger("liszt:updated");
           $("select[name='municipality']").html('<option value="">Choose a municipality ...</option>').trigger("liszt:updated");
        }
    });
});
$("select[name='province']").change(function() {
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
           $("select[name='municipality']").html(msg).trigger("liszt:updated");
        }
    });
});

$("#selNationality").change(function() {
    ImmiFields(($("#selNationality").val() > 1) ? false : true);
});

if($("#bplace").val() == "")    $("#print_out").hide();
else                            $("#print_out").show();
$("#print_out").click(function() {
<?php
    print("var id = \"" . $empdetails['employeeid'] . "\";");
?>
    if (id != "") {
        var vals = "?form=empdetails&id=" + id;
        window.open("<?=site_url("forms/loadForm")?>"+vals,"pdftest");
    };
});

$(".editrelation").click(function(){
   addlegit($(this)); 
});
$(".deleterelation").click(function(){
    var mtable = $("#legitlist").find("tbody");
    $(this).parent().parent().remove();
    if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");  
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
  success: "valid"
});

$.validator.addMethod("loginRegex", function(value, element) {
    return this.optional(element) || /^[a-z0-9\s]+$/i.test(value);
}, "Username must contain only letters, numbers.");

$("#saveinfo").click(function(){
    
   var $validator = $("#info").validate({
        rules: {
            employeeid: {
              required: true,
              minlength: 2
            },
            employeecode: {
              loginRegex: true
            },
            lname: {
              required: true,
              minlength: 2
            },
            fname: {
              required: true,
              minlength: 2
            },
            emptype:{
              required: true
            },
            position:{
              required: true
            },
            management: {
              required: true
            },
            deptid: {
              required: true
            },
            /*
            employmentstat: {
              required: true
            },
            */
            empshift: {
              required: true
            },
            dateemployed: {
              required: true
            },
            tinno: {
              required: true
            },
            sssno: {
              required: true
            },
            philhealth: {
              required: true
            },
            pagibig: {
              required: true
            },
            medicare: {
              required: true
            },
            bplace: {
              required: true
            },
            gender: {
              required: true
            },
            civil_status: {
              required: true
            },
            cityaddr: {
              required: true
            },
            permanentaddress: {
              required: true
            },
            mobile: {
              required: true
            },
            citytelno: {
              required: true
            },
            email: {
              required: true
            },
            cpname: {
              required: true
            },
            cprelationship: {
              required: true
            },
            cpaddress: {
              required: true
            },
            cpcontactno: {
              required: true
            },
            cptelno: {
              required: true
            },
            prc:{
              required: true  
            },
            
        }
    });
 
   var legitimate_relations = "";
   $("#legitlist").find("tbody tr").each(function(){
     if($(this).find("td").length>1){
         legitimate_relations += (legitimate_relations?"|":"");
         legitimate_relations += $(this).find("td:eq(0)").text();
         legitimate_relations += "~u~";
         //legitimate_relations += $(this).find("td:eq(1)").text();
         legitimate_relations += $(this).find("td:eq(1)").attr("reldata");
         legitimate_relations += "~u~";
         legitimate_relations += $(this).find("td:eq(2)").text();
         legitimate_relations += "~u~";
         legitimate_relations += $(this).find("td:eq(3)").text();
         legitimate_relations += "~u~";            
         legitimate_relations += $(this).find("td:eq(4)").text();
         legitimate_relations += "~u~";            
         legitimate_relations += $(this).find("td:eq(5)").text()=="YES"?1:0;
     }
   });
   
   /** Educational Background */
   var e_children = "";
   $("#childrenlist").find("tbody tr").each(function(){
       if($(this).find("td").length>1){
        e_children += (e_children?"|":"");
        e_children += $(this).find("td:eq(0)").text();
        e_children += "~u~";
        e_children += $(this).find("td:eq(1)").text();
        e_children += "~u~";
        e_children += $(this).find("td:eq(2)").text();
       }
   });
   
    var isReq = false;
    if($("select[name='civil_status']").val() == "002"){
        if($("#spouse").val() == ""){
            isReq = true;
        }
    }        
   
   // select box required
   var ddreq = ["citizenship","gender"];
   for(var x = 0;x < ddreq.length; x++){
    
    if($("select[name='"+ddreq[x]+"']").val() == ""){
        if(x == 0)  $("#isrequiredgen").html("<br />This field is required..").css("color","red").show();
        else        $("#isrequiredc").html("<br />This field is required..").css("color","red").show();
    }
   }
    
   if($("#info").valid()){
    if(isReq == false){
        if($("select[name='citizenship']").val() != ""){
        if($("select[name='gender']").val() != ""){
            if($("#position").val() != ""){
               var form_data = $("#info").serialize();                              
                   form_data += "&job=employee/personal_info";  
                   form_data += "&legitimate_relations="+legitimate_relations;
                   form_data += "&employee_children="+e_children;  console.log(form_data);
               $.ajax({
                  url: "<?=site_url("employee_/validateinfo")?>",
                  data : form_data,
                  type : "POST",
                  success:function(msg){
                    alert($(msg).find("message:eq(0)").text());
                    cancontinue = true;
                    $("#isrequired").hide();
                  }
               }); 
            }else{
                alert("Position is required..");
                $("#divreq").show();
                $("#isrequired").html("This field is required..").css("color","red").show();
            }
        }else {alert("Gender is required.."); }
        }else {alert("Citizenship is required.."); }
    }else{
        alert("Spouse is Required..");
        $("#isrequireds").html("This field is required..").css("color","red").show();
        $("#spouse").focus();
    }
   }else {
       $validator.focusInvalid();
       return false;
   }
   
});
$(".birthdate, .dateemployed, .dateresigned, .datepos").datepicker({
    autoclose: true,
    todayBtn: true
});
$("a[tag='add_legit']").click(function(){
    addlegit("");
});

$("select[name='emptype']").change(function(){
  var emptype  = $(this).val();
  var empshift = $("select[name='empshift']").val();
  call_shiftschedule(emptype,empshift);  
});

function addlegit(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Legitimation Relations" : "Add Legitimation Relations");  
    $.ajax({
        url: "<?=site_url('employee_/legitimate')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='lr_name']").val(tdcur.find("td:eq(0)").text()); 
                 //$(modal_display).find("input[name='lr_relationship']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("select[name='lr_relationship']").val(tdcur.find("td:eq(1)").attr("reldata")).trigger('liszt:updated');
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
           $("select[name='empshift']").html(msg).trigger("liszt:updated");
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
$('.chosen').chosen();
$(".echildren").click(function(){
    addchildren($(this));
});
$(".delete_entry").click(function(){
   $(this).parent().parent().remove(); 
});
$("a[tag='add_children']").click(function(){
    addchildren("");
});
function addchildren(obj){
    $("#modal-view").find("h3[tag='title']").text(obj ? "Edit Children" : "Add Children");
    $("#button_save_modal").text("Save");  
    $.ajax({
        url: "<?=site_url('employee_/echildren')?>",
        type: "POST",
        success: function(msg){
            var modal_display = $("#modal-view").find("div[tag='display']");
            $.when($(modal_display).html(msg)).done(function(){ 
               if(obj){
                 var tdcur = $(obj).parent().parent();
                 $(tdcur).attr("iscurrent",1);
                 $(modal_display).find("input[name='eb_name']").val(tdcur.find("td:eq(0)").text()); 
                 $(modal_display).find("input[name='eb_dob']").val(tdcur.find("td:eq(1)").text());
                 $(modal_display).find("input[name='eb_age']").val(tdcur.find("td:eq(2)").text());
              }else{
                 $("#childrenlist").find("tr").each(function(){
                   $(this).attr("iscurrent",0); 
                 }) 
              }
            });
        }
    });  
}

$(".yesno").click(function(){var attname = $(this).attr("name");if($("input[name='"+attname+"']").prop("checked"))   $("input[name='"+attname+"']").not(this).prop("checked",false);});
$("input[name='birthdate']").change(function(){
   dob = new Date($(this).val());
   var today = new Date();
   var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
   $('#age').val(age);
});
if($("#civil_status").val() == "1")   $("#spouse,#occupation").val("").attr("disabled",true).css("background-color","#EEEEEE");
$("#civil_status").change(function(){
   if($(this).val() != "1")
    $("#spouse,#occupation").val("").attr("disabled",false).css("background-color","transparent");
   else
    $("#spouse,#occupation").val("").attr("disabled",true).css("background-color","#EEEEEE");
    $("#spouse").focus();
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

    $("#modal-view").find("h3[tag='title']").text("Edit Employment Status");
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
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});

</script>