<?php
    $eid = $this->session->userdata("personalinfo");
    $eid = $eid[0]['employeeid'];
    $datetoday = date('Y-m-d');

    $date_effective = $workingday = $fixedday = $workhours = $workhoursexemp = $monthly = $semimonthly = $biweekly = $weekly = $daily = $hourly = $minutely = $sched = $sssid = $sssamount = $pagibigid = $pagibigamount = $philhealthid = $philhealthamount = $peraaid = $peraaamount = $sssamount_er = $philhealthamount_er = $pagibigamount_er = $peraaquarter = $taxstatus = $whtax = $absents = $balance = $rank = $type = "";

    // for default all cutoff
    $philhealthquarter = $sssquarter = $pagibigquarter = 3;

    $row = $this->payroll->displaySalary($eid);
    foreach($row as $data){
        $date_effective    = !empty($data->date_effective)     ? ( $data->date_effective != '0000-00-00 00:00:00' ? date('Y-m-d',strtotime($data->date_effective)) : "" )  : "";
        $workingday        = !empty($data->workdays)           ? $data->workdays           : "";
        $fixedday          = !empty($data->fixedday)           ? $data->fixedday           : "";  
        $workhours         = !empty($data->workhours)          ? $data->workhours          : "";  
        $workhoursexemp    = !empty($data->workhoursexemp)     ? $data->workhoursexemp     : "";
        $monthly           = !empty($data->monthly)            ? $data->monthly            : "";   
        $semimonthly       = !empty($data->semimonthly)        ? $data->semimonthly        : "";   
        $biweekly          = !empty($data->biweekly)           ? $data->biweekly           : "";   
        $weekly            = !empty($data->weekly)             ? $data->weekly             : "";
        $daily             = !empty($data->daily)              ? $data->daily              : "";
        $hourly            = !empty($data->hourly)             ? $data->hourly             : "";
        $minutely          = !empty($data->minutely)           ? $data->minutely           : "";         
        $sched             = !empty($data->schedule)           ? $data->schedule           : "";
        $taxstatus         = !empty($data->dependents)         ? $data->dependents         : "";
        $whtax             = !empty($data->whtax)              ? $data->whtax              : "";
        $absents           = !empty($data->absent)             ? $data->absent             : "";
        $balance           = !empty($data->absentbalance)      ? $data->absentbalance      : "";
        $sssid             = !empty($data->sssid)              ? $data->sssid              : "";
        $sssamount         = !empty($data->sssamount)      || $data->sssamount == 0        ? $data->sssamount          : "";
        $sssquarter        = !empty($data->sssquarter)         ? $data->sssquarter         : "";
        $pagibigid         = !empty($data->pagibigid)          ? $data->pagibigid          : "";
        $pagibigamount     = !empty($data->pagibigamount)   || $data->pagibigamount == 0   ? $data->pagibigamount      : "";
        $pagibigquarter    = !empty($data->pagibigquarter)     ? $data->pagibigquarter     : "";
        $philhealthid      = !empty($data->philhealthid)       ? $data->philhealthid       : "";
        $philhealthamount  = !empty($data->philhealthamount) || $data->philhealthamount == 0  ? $data->philhealthamount   : "";
        $philhealthquarter = !empty($data->philhealthquarter)  ? $data->philhealthquarter  : "";

        $sssamount_er       = !empty($data->sssamount_er) || $data->sssamount_er == 0  ? $data->sssamount_er   : "";
        $philhealthamount_er       = !empty($data->philhealthamount_er) || $data->philhealthamount_er == 0  ? $data->philhealthamount_er  : "";
        $pagibigamount_er       = !empty($data->pagibigamount_er) || $data->pagibigamount_er == 0  ? $data->pagibigamount_er   : "";


        $Lec               = !empty($data->lechour)  ? $data->lechour  : "";
        $Lab               = !empty($data->labhour)  ? $data->labhour  : "";
        $Honorarium        = !empty($data->honorarium)  ? $data->honorarium  : "";
        // $rank              = !empty($data->rank)  ? $data->rank  : "";
        $type              = !empty($data->type)  ? $data->type  : "";
    }

    /*if(!$row){
       $emp_deduction = $this->payroll->displayEmployeeDeduction($eid);
       $sssid = isset($emp_deduction["sssid"]) ? $emp_deduction["sssid"] : "";
       $sssamount = isset($emp_deduction["sssamount"]) ? $emp_deduction["sssamount"] : "";
       $sssquarter = isset($emp_deduction["sssquarter"]) ? $emp_deduction["sssquarter"] : "";

       $pagibigid = isset($emp_deduction["pagibigid"]) ? $emp_deduction["pagibigid"] : "";
       $pagibigamount = isset($emp_deduction["pagibigamount"]) ? $emp_deduction["pagibigamount"] : "";
       $pagibigquarter = isset($emp_deduction["pagibigquarter"]) ? $emp_deduction["pagibigquarter"] : "";

       $philhealthid = isset($emp_deduction["philhealthid"]) ? $emp_deduction["philhealthid"] : "";
       $philhealthamount = isset($emp_deduction["philhealthamount"]) ? $emp_deduction["philhealthamount"] : "";
       $philhealthquarter = isset($emp_deduction["philhealthquarter"]) ? $emp_deduction["philhealthquarter"] : "";
    }*/

    $rank = $this->employee->getEmployeeRank($eid);
    $ratebased = $this->payroll->getEmployeeRateBased($eid);
    $latest_effective = $this->payroll->getLatestSalaryEffective($eid);
    if($latest_effective) $date_effective = $latest_effective;

    $CI =& get_instance();
    $CI->load->model('utils');
    $aimsdept_arr = $CI->utils->getAIMSDepartment();
    unset($aimsdept_arr['']);
    $aimsdept = '';

    $leclablist = $this->payroll->getPerDeptLecLabPay($eid);
    $isteaching = $this->employee->getempteachingtype($eid);
    $type_config = $this->setup->getPayrollTypeArray();
    $isCollegeTeaching = $this->extensions->checkIfCollegeTeaching($eid);
    $teachingtype = $this->extensions->empTeachingType($eid);
?>
<style>

.form_row .field {
    padding-bottom: 15px;
    margin-left: 0px;
}


/*
.aimsdept{
    width: 208px;
    margin-left: -50px;
    padding: 0px;
    -webkit-tap-highlight-color: blue;
}*/


</style>
<br><br>
<div class="widgets_area animated fadeIn delay-1s">
    <br>
    <div class="panel">
        <div class="panel-heading"><h4><b>Payroll Information</b></h4></div>
        <div class="panel-body">
            <form id="payroll">  
                <div class="col-md-12" style="border: 1px solid #0072C6;">
                    <div class="col-md-4" style="margin-right: 10%;">
                    <input type="hidden" name="model" value="esalary" />
                    <input type="hidden" name="eid" value="<?=$eid?>" />
                       
                        <div class="form_row" style="visibility: hidden;">
                            <div class="field-name col-md-4" ></div>
                            <div class="field no-search col-md-8" >
                                <input class=""  type="checkbox" style="-webkit-transform: scale(1);" name="isFixed"  id="isFixed" value="1" <?=($fixedday) ? " checked" : " checked"?> /> &nbsp;&nbsp;Monthly Rate
                            </div>
                            
                        </div>
                        <br><br><br>
                        <div class="form_row" style="margin-bottom: 55px;display: none;">
                            <div class="field-name col-md-4" ></div>
                            <div class="field no-search col-md-8" >
                                <select id="ratebased" name="ratebased" class="form-control">
                                    <option value="teaching" <?= ($ratebased == "") ? "selected" : "" ?> >--</option>
                                    <option value="teaching" <?= ($ratebased == "teaching") ? "selected" : "" ?> >Teaching</option>
                                    <option value="nonteaching" <?= ($ratebased == "nonteaching") ? "selected" : "" ?> >Non Teaching</option>
                                </select>
                            </div>
                        </div>
                        <div class="form_row" hidden="">
                            <label class="field_name align_right">Work Hours</label>
                            <div class="field">
                                <select class="form-control" id="workhours" name="workhours">
                                    <?=$this->payrolloptions->viewWorkHours(false,$workhours);?>
                                </select>
                            </div>
                        </div>
                        <div class="form_row" hidden="">
                            <label class="field_name align_right">Work Hours Exemption</label>
                            <div class="field">
                                <select class="form-control" id="workhoursexemp" name="workhoursexemp">
                                    <?=$this->payrolloptions->viewWorkHours(true,$workhoursexemp);?>
                                </select>
                            </div>
                        </div>
                        <div class="form_row" >
                            <div class="field-name col-md-4" >
                                <label class="field_name align_right" style="margin-top: -40px;">Rank Type:</label>
                            </div>
                            <div class="field no-search col-md-8"  style="margin-top: -40px;pointer-events: none;">
                                <select class="chosen" name="type" id="type" readonly="">
                                    <option>- Select Type -</option>
                                    <?php foreach($type_config as $value): ?>
                                        <option value="<?= $value['id'] ?>" <?= ($value['id'] == $rank) ? "selected" : "" ?> ><?= Globals::_e($value['description']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <!-- <div class="form_row">
                            <div class="field-name col-md-4" >
                                <label class="field_name align_right">Rank:</label>
                            </div>
                            <div class="field no-search col-md-8">
                                <select class="chosen" name="rank" id="rank">
                                    <option>- Select Rank -</option>
                                </select>
                            </div>
                        </div> -->
                        <div class="form_row">
                            <div class="field-name col-md-4" >
                                <label class="field_name align_right">Monthly</label>
                            </div>
                            <div class="field no-search col-md-8">
                                <input class="form-control" id="monthly" name="monthly" type="text" value="<?=$monthly?>" onkeypress="return numbersonly(this)"/>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="field-name col-md-4" >
                                <label class="field_name align_right">Semi&nbsp;-Monthly</label>
                            </div>
                            <div class="field no-search col-md-8">
                                <input class="form-control" id="semimonthly" name="semimonthly" value="<?=$semimonthly?>" type="text" value="" onkeypress="return numbersonly(this)"/>
                            </div>
                        </div>
                        <div class="form_row" hidden="">
                            <label class="field_name align_right">Bi-Weekly</label>
                            <div class="field">
                                <input class="form-control" id="biweekly" name="biweekly" value="<?=$biweekly?>" type="text" value="" onkeypress="return numbersonly(this)"/>
                            </div>
                        </div>
                        <div class="form_row" hidden="">
                            <label class="field_name align_right">Weekly</label>
                            <div class="field">
                                <input class="form-control" id="weekly" name="weekly" value="<?=$weekly?>" type="text" value="" onkeypress="return numbersonly(this)"/>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="field-name col-md-4" >
                                <label class="field_name align_right">Daily</label>
                            </div>
                            <div class="field no-search col-md-8">
                                <input class="form-control" id="daily" name="daily" value="<?=$daily?>" type="text" value="" onkeypress="return numbersonly(this)"/>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="field-name col-md-4" >
                                <label class="field_name align_right">Hourly</label>
                            </div>
                            <div class="field no-search col-md-8">
                                <input class="form-control" id="hourly" name="hourly" value="<?=$hourly?>" type="text" value="" onkeypress="return numbersonly(this)" readonly/>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="field-name col-md-4" >
                                <label class="field_name align_right">Per Minute</label>
                            </div>
                            <div class="field no-search col-md-8">
                                <input class="form-control" id="minutely" name="minutely" value="<?=$minutely?>" type="text" value="" onkeypress="return numbersonly(this)" readonly/>
                            </div>
                        </div>
                       <div class="form_row">
                            <div class="field-name col-md-4" >
                                <label class="field_name align_center">Effectivity Date</label>
                            </div>
                            <div class="field no-search col-md-8">
                                <div class='input-group date' id="date_effective" data-date="" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" ize="16" name="date_effective" value="<?=$date_effective?>"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <!--end LEC / LAB HOUR PER DEPT -->
                        <div class="form_row">
                            <div class="field col-md-4" ></div>
                                <!-- <div id="dhide" hidden=""></div> -->
                            <div id="dshow" class="col-md-8">
                                <a href="#" class="btn btn-primary" id="savesalary">Save</a>
                            </div>
                        </div>
                        <br><br>
                    </div>
                    
                  <div class="col-md-3" style="margin-left: -20px; padding: 0px;display: none;">
                    <br><br><br><br>
                        <div class="form_row" id="wrap_leclabpay">
                                <?php if(isset($leclablist[$eid])){
                                    foreach ($leclablist[$eid] as $key => $row){ ?>  
                                     <div class="field col-md-12 leclab-pay" style="padding-left: 5px;" >  
                                        <div class="leclab-pay col-md-6" style="padding:0px; width: 38.8%;">
                                            <b>Lec Hr:</b> <input name="lechour" class="lechour form-control" type="text" value="<?=$row['lechour']?>" onkeypress="return numbersonly(this)" style="width: 60px;display: inline; padding-left: 5px; padding-right: 5px;"/>
                                            <b>Lab Hr:</b> <input name="labhour" class="labhour form-control" type="text" value="<?=$row['labhour']?>" onkeypress="return numbersonly(this)" style="width: 60px;display: inline; padding-left: 5px; padding-right: 5px;"/>
                                        </div>
                                        <div class="col-md-4" style="padding: 0px;">
                                            <select name="aimsdept" class="aimsdept form-control" style="width: 107%; padding-left: 2px;padding-right: 2px;">
                                                <option value="" <?=$row['aimsdept']==''?' selected':''?> >Choose Aims department..</option>
                                                <? foreach ($aimsdept_arr as $key => $desc) { ?>
                                                      <option value="<?=$key?>" <?=$row['aimsdept']==$key?' selected':''?> ><?=$desc?></option>
                                                <? } ?>
                                            </select>
                                        </div>
                                        <div class="btn-group col-md-2 align_right" style="margin-right: 0px; padding-left: 21px; padding-right: 0px; float: left;" >
                                            <a class="btn btn-primary add_leclabpay" style="margin-right: 0px;"><i class='glyphicon glyphicon-plus-sign'></i></a>
                                            <a class="btn btn-danger del_leclabpay"><i class='glyphicon glyphicon-trash'></i></a>
                                        </div>
                                    </div>

                                    <? }
                                }else{ ?>
                                     <div class="field col-md-12 leclab-pay" style="padding-left: 5px;">
                                        <div class=" col-md-6" style="padding:0px; width: 38.8%;">
                                            <b>Lec&nbsp;Hr:</b> <input name="lechour" class="lechour form-control" type="text" value="0" onkeypress="return numbersonly(this)" style="width: 60px;display: inline; padding-left: 5px; padding-right: 5px;"/>
                                            <b>Lab&nbsp;Hr:</b> <input name="labhour" class="labhour form-control" type="text" value="0" onkeypress="return numbersonly(this)" style="width: 60px;display: inline; padding-left: 5px; padding-right: 5px;"/>
                                        </div>
                                        <div class="col-md-4" style="padding: 0px;">
                                            <select name="aimsdept" class="aimsdept form-control" style="width: 107%; padding-left: 2px;padding-right: 2px;">
                                                <option value="" selected="">Choose Aims department..</option>
                                                <? foreach ($aimsdept_arr as $key => $desc) { ?>
                                                      <option value="<?=$key?>"><?=$desc?></option>
                                                <? } ?>
                                            </select>
                                            
                                        </div>
                                        <div class="btn-group col-md-2 align_right" style="margin-right: 0px; padding-left: 21px; padding-right: 0px; float: left;"  >
                                            <a class="btn btn-primary add_leclabpay" style="margin-right: 0px;"><i class='glyphicon glyphicon-plus-sign'></i></a>
                                            <a class="btn btn-danger del_leclabpay"><i class='glyphicon glyphicon-trash'></i></a>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                        </div>
                        
                    <!-- Dependents -->


                    
                    <div class="col-md-4"  style="border-left: 1px solid #0072C6;">
                        <br><br>
                        <div class="form_row">
                            <div class="col-md-2">
                                <label class="field_name align_right"></label>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="field  col-md-9">
                                <input class="form-control" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" placeholder="Dependents"/>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="col-md-2">
                                <label class="field_name align_right">Tax&nbsp;Status</label>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="field no-search  col-md-9">
                                <select class="chosen col-md-6 align_left" name="tax_status" id="tax_status"><?=$this->payrolloptions->taxdependents($taxstatus);?></select>
                            </div>
                        </div>
<!--                     </div><br /><br /> -->
                    
                    <!-- Schedule -->
                        <div class="form_row">
                            <div class="col-md-2">
                                <label class="field_name align_right"></label>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="field col-md-9">
                                <input class="form-control" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" placeholder="Deduction"/>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="col-md-2">
                                <label class="field_name align_right">Schedule</label>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="field no-search col-md-9">
                                <select class="chosen col-md-4 align_left" name="sched" id="sched"><?=$this->payrolloptions->payschedule($sched);?></select>
                            </div>
                        </div>
                    <div id="quload" hidden=""></div>
                    
                    <!-- Contributions -->
                    <!-- <div class="col-md-6" id="deducload" hidden=""></div> -->
<!--                     <div class="col-md-6" id="deducshow">
                        <br> -->
                        <div class="form_row">
                            <div class="col-md-2">
                                <label class="field_name align_right"></label>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="field col-md-9">
                                <!-- <input class="form-control" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif; display: none;' type="text" placeholder="Member ID"/> -->
                                <div class="col-md-5" style=" float: left; padding-left: 0px; padding-right: 0px;">
                                    <input class="form-control" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" placeholder="Contribution"/>
                                </div>
                                <div class="col-md-5" style="float: right; padding-left: 0px; padding-right: 0px;">
                                    <input class="form-control" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" placeholder="Quarter"/>    
                                </div>        
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="col-md-2">
                                <label class="field_name align_right">&emsp;</label>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="field col-md-9">
                                <div class="col-md-5" style=" float: left; padding: 0px">
                                    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
                                        <div class="col-md-5" style=" float: left; padding-left: 0px; padding-right: 0px;">
                                            <input class="form-control" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" placeholder="EE" readonly />
                                        </div>
                                        <div class="col-md-2"></div>
                                        <div class="col-md-5" style="float: right; padding-left: 0px; padding-right: 0px;">
                                            <input class="form-control" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" placeholder="ER" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5" style="float: right; padding-left: 0px; padding-right: 0px;">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="col-md-2">
                                <label class="field_name align_right">SSS</label>
                            </div>
                            <div class="col-md-1"></div>
                            <div class="field col-md-9">
                                 <input type="hidden" name="ssdescs" id="rsss" value="<?=($sssamount) ? $sssamount : ''?>"/>
                                 <input type="hidden" name="ssdescs" id="rrsss" value=""/>
                                <input type="hidden" name="ssdesc" value="sss"/>
                                <input class="form-control" id="sssid" name="sssid" type="text" value="<?=$sssid?>" style="display: none; " onkeypress="return numbersonly(this,'false','false','id')" />
                                <div class="col-md-5" style=" float: left; padding-left: 0px; padding-right: 0px;">
                                    <!-- <input class="form-control" id="sss" name="sss" type="text" value="<?=$sssamount?>" onkeypress="return numbersonly(this)" /> -->
                                    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
                                        <div class="col-md-5" style=" float: left; padding-left: 0px; padding-right: 0px;">
                                            <input class="form-control" id="sss" name="sss" type="text" value="<?=$sssamount?>" onkeypress="return numbersonly(this)" />
                                        </div>
                                        <div class="col-md-2"></div>
                                        <div class="col-md-5" style="float: right; padding-left: 0px; padding-right: 0px;">
                                            <input class="form-control" id="sss_er" name="sss_er" type="text" value="<?=$sssamount_er?>"  onkeypress="return numbersonly(this)" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5" style="float: right; padding-left: 0px; padding-right: 0px;">
                                    <select class="chosen" name="sssq" id="sssq"><?=$this->payrolloptions->quarter($sssquarter,FALSE,$sched);?></select>
                                    <input class="form-control" id="recent_sssq" name="recent_sssq" type="hidden" value="<?=$sssquarter?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="col-md-2">
                            <label class="field_name align_right" style="width: 100%">PhilHealth</label>
                        </div>
                        <div class="col-md-1"></div>
                            <div class="field col-md-9">
                                <input type="hidden" name="" id="phs" value="<?=($philhealthamount) ? $philhealthamount : ''?>"/>
                                <input type="hidden" name="" id="phsss" value=""/>
                                <input type="hidden" name="phdesc" value="philhealth"/>
                                <input class="form-control" id="philhealthid" name="philhealthid" type="text" value="<?=$philhealthid?>" style="display: none;" onkeypress="return numbersonly(this,'false','false','id')" />
                                <div class="col-md-5" style=" float: left; padding-left: 0px; padding-right: 0px;">
                                    <!-- <input class="form-control" id="philhealthAmount" name="philhealth" type="text" value="<?=$philhealthamount?>" onkeypress="return numbersonly(this)" /> -->
                                    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
                                        <div class="col-md-5" style=" float: left; padding-left: 0px; padding-right: 0px;">
                                            <input class="form-control" id="philhealthAmount" name="philhealth" type="text" value="<?=$philhealthamount?>" onkeypress="return numbersonly(this)" />
                                        </div>
                                        <div class="col-md-2"></div>
                                        <div class="col-md-5" style="float: right; padding-left: 0px; padding-right: 0px;">
                                            <input class="form-control" id="philhealthAmount_er" name="philhealth_er" type="text" value="<?=$philhealthamount_er?>" onkeypress="return numbersonly(this)" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5" style="float: right; padding-left: 0px; padding-right: 0px;">
                                    <select class="chosen col-md-4 align_left" name="philhealthq" id="philhealthq"><?=$this->payrolloptions->quarter($philhealthquarter,FALSE,$sched);?></select>
                                    <input class="form-control" id="recent_philhealthq" name="recent_philhealthq" type="hidden" value="<?=$philhealthquarter?>" />
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="col-md-2">
                            <label class="field_name align_right">Pag&nbsp;-&nbsp;ibig</label>
                        </div>
                        <div class="col-md-1"></div>
                            <div class="field col-md-9">
                                <input type="hidden" name="" id="pgs" value="<?=($pagibigamount) ? $pagibigamount : ''?>"/>
                                <input type="hidden" name="" id="pgsss" value=""/>
                                <input type="hidden" name="pagibigdesc" value="pagibig"/>
                                <input class="form-control" id="pagibigid" name="pagibigid" type="text" value="<?=$pagibigid?>" style="display: none;" onkeypress="return numbersonly(this,'false','false','id')" />
                                <div class="col-md-5" style=" float: left; padding-left: 0px; padding-right: 0px;">
                                    <!-- <input class="form-control" id="pagibigAmount" name="pagibig" type="text" value="<?=$pagibigamount?>" onkeypress="return numbersonly(this)"/> -->
                                    <div class="col-md-12" style="padding-left: 0px; padding-right: 0px;">
                                        <div class="col-md-5" style=" float: left; padding-left: 0px; padding-right: 0px;">
                                            <input class="form-control" id="pagibigAmount" name="pagibig" type="text" value="<?=$pagibigamount?>" onkeypress="return numbersonly(this)"/>
                                        </div>
                                        <div class="col-md-2"></div>
                                        <div class="col-md-5" style="float: right; padding-left: 0px; padding-right: 0px;">
                                            <input class="form-control" id="pagibigAmount_er" name="pagibig_er" type="text" value="<?=$pagibigamount_er?>" onkeypress="return numbersonly(this)" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5" style="float: right; padding-left: 0px; padding-right: 0px;">
                                    <select class="chosen col-md-4 align_left" name="pagibigq" id="pagibigq"><?=$this->payrolloptions->quarter($pagibigquarter,FALSE,$sched);?></select>
                                    <input class="form-control" id="recent_pagibigq" name="recent_pagibigq" type="hidden" value="<?=$pagibigquarter?>" />
                                </div>
                            </div>
    
                        </div>

                        <div class="form_row" style="margin-top: -10px;display: none;">
                            <div class="col-md-2">
                            <label class="field_name align_right">PERAA</label>
                        </div>
                        <div class="col-md-1"></div>
                            <div class="field col-md-9">
                                <!-- <div class="col-md-2"></div> -->
                                <input type="hidden" name="" id="pgs" value="<?=$peraaamount?>"/>
                                <input type="hidden" name="" id="pgsss" value=""/>
                                <input type="hidden" name="peraadesc" value="peraa"/>
                                <input class="form-control" id="peraaid" name="peraaid" type="text" value="<?=$peraaid?>" style="display: none;" onkeypress="return numbersonly(this,'false','false','id')" />
                                <div class="col-md-5" style=" float: left; padding-left: 0px; padding-right: 0px;">
                                    <input class="form-control" id="peraaAmount" name="peraa" type="text" value="<?=$peraaamount?>" onkeypress="return numbersonly(this)"/>
                                </div>
                                <div class="col-md-5" style="float: right; padding-left: 0px; padding-right: 0px;">
                                    <select class="chosen col-md-4 align_left" name="peraaq" id="peraaq"><?=$this->payrolloptions->quarter($peraaquarter,FALSE,$sched);?></select>
                                    <input class="form-control" id="recent_peraaq" name="recent_peraaq" type="hidden" value="<?=$peraaquarter?>" />
                                </div>
                            </div>
                            <br>
                        </div>

                    <!-- Dependents -->
                        <div class="form_row">
                            <div class="col-md-3">
                            <label class="field_name align_right"></label>
                        </div>
                        <div class="col-md-1"></div>
                            <div class="field col-md-9">
                                <input class="form-control" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" placeholder="Adjustments"/>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="col-md-2">
                            <label class="field_name align_right">Withholding Tax</label>
                        </div>
                        <div class="col-md-1"></div>
                            <div class="field no-search  col-md-9">
                                <input class="form-control" id="whtax" name="whtax" type="text" value="<?=$whtax?>" onkeypress="return numbersonly(this)"/>
                            </div>
                        </div>
                        <div class="form_row" style="display: none;">
                            <label class="field_name align_right">Absent</label>
                            <div class="field no-search">
                                <input class="form-control" id="absents" name="absents" type="text" value="<?=$absents?>" onkeypress="return numbersonly(this)"/>
                                Balance
                                <input class="form-control" id="balance" name="balance" type="text" value="<?=$balance?>" onkeypress="return numbersonly(this)"/>
                            </div>
                        </div>
                        <br>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
    <div id="salaryhistory">
    </div>        
</div>
<script>
    loaddecimal();
    var workingdays,workinghours,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,totalhours,salaryperday= 0;
    var quarters;

    $("#sssq").change(function(){
    var rsss = $("#rsss").val();
    var rrsss = $("#rrsss").val();
    var sss = $("#sss").val();
    var sss_er = $("#sss_er").val();
    var quarter = $("#sssq").val();
    var sched = $("#sched").val();
    var a = 0;
    var recent_sssq = $("#recent_sssq").val();

    // if ((rsss != "" || rsss != null) && quarter == 3 )  {
    //     a = 1;
    // }
    // else if (rrsss != "" || rrsss != null) {
    //     a = 2;
    // }
    //     if (sched == "weekly" ) {
    //         if (quarter == 5) {
                
    //             $("#sss").val(sss/2);
    //         }
    //         else
    //         {
                
    //             $("#sss").val(sss*2);
    //         }
    //     }
    //     else if (sched == "semimonthly") 
    //     {
           
    //         if (quarter == 3) {
    //             if(sss) $("#sss").val(sss/2);
    //         }
    //          else
    //         {
    //             if (a == 1 && (rsss != $("#sss").val())) {
    //                 $("#sss").val(sss * 2);
    //             }
    //             else
    //             {
    //                if ((rsss != $("#sss").val())) {
    //                     $("#sss").val(sss * 2);    
    //                }
    //                else
    //                { 

    //                     $("#sss").val(rsss);
    //                }
       
    //             }
    //         }
    //     }

        if (quarter == 3 ) {
            $("#sss").val(sss/2);
            $("#sss_er").val(sss_er/2);
        }
        else
        {
            if(recent_sssq == 3){
                $("#sss").val(sss*2);
                $("#sss_er").val(sss_er*2);
            }
        }
        $("#recent_sssq").val($("#sssq").val());

    });
    


    $("#philhealthq").change(function(){
        var rsss = $("#phs").val();
        var rrsss = $("#phsss").val();
        var sss = $("#philhealthAmount").val();
        var sss_er = $("#philhealthAmount_er").val();
        var quarter = $("#philhealthq").val();
        var sched = $("#sched").val();
         var recent_philhealthq   = $("#recent_philhealthq").val();
        var a = 0;

        // if ((rsss != "" || rsss != null) && quarter == 3 )  {
        //     a = 1;
        // }
        // else if (rrsss != "" || rrsss != null) {
        //     a = 2;
        // }
        // if (sched == "weekly" ) {
        //     if (quarter == 5) {
                
        //         $("#philhealthAmount").val(sss/2);
        //     }
        //     else
        //     {
                
        //         $("#philhealthAmount").val(sss*2);
        //     }
        // }
        // else if (sched == "semimonthly") 
        // {
           
        //     if (quarter == 3) {
        //         if(sss) $("#philhealthAmount").val(sss/2);
        //     }
        //      else
        //     {
        //         if (a == 1 && (rsss != $("#philhealthAmount").val())) {
        //             $("#philhealthAmount").val(sss * 2);
        //         }
        //         else
        //         {
        //            if ((rsss != $("#philhealthAmount").val())) {
        //                 $("#philhealthAmount").val(sss * 2);    
        //            }
        //            else
        //            { 

        //                 $("#philhealthAmount").val(rsss);
        //            }
       
        //         }
        //     }
        // }
        if (quarter == 3) {
            $("#philhealthAmount").val(sss/2);
            $("#philhealthamount_er").val(sss_er/2);
        }
        else
        {
            if(recent_philhealthq == 3){
                $("#philhealthAmount").val(sss*2);
                $("#philhealthamount_er").val(sss_er*2);
            }
        }
        $("#recent_philhealthq").val($("#philhealthq").val());
    });


  $("#pagibigq").change(function(){
    var rsss = $("#pgs").val();
    var rrsss = $("#pgsss").val();
    var sss = $("#pagibigAmount").val();
    var sss_er = $("#pagibigAmount_er").val();
    var quarter = $("#pagibigq").val();
    var sched = $("#sched").val();
    var recent_pagibigq = $("#recent_pagibigq").val();
    var a = 0;

    // if ((rsss != "" || rsss != null) && quarter == 3 )  {
    //     a = 1;
    // }
    // else if (rrsss != "" || rrsss != null) {
    //     a = 2;
    // }
    //     if (sched == "weekly" ) {
    //         if (quarter == 5) {
                
    //             $("#pagibigAmount").val(sss/2);
    //         }
    //         else
    //         {
                
    //             $("#pagibigAmount").val(sss*2);
    //         }
    //     }
    //     else if (sched == "semimonthly") 
    //     {
           
    //         if (quarter == 3) {
    //             if(sss) $("#pagibigAmount").val(sss/2);
    //         }
    //          else
    //         {
    //             if (a == 1 && (rsss != $("#pagibigAmount").val())) {
    //                 $("#pagibigAmount").val(sss * 2);
    //             }
    //             else
    //             {
    //                if ((rsss != $("#pagibigAmount").val())) {
    //                     $("#pagibigAmount").val(sss * 2);    
    //                }
    //                else
    //                { 

    //                     $("#pagibigAmount").val(rsss);
    //                }
       
    //             }
    //         }
    //     }
        if (quarter == 3) {
            $("#pagibigAmount").val(sss/2);
            $("#pagibigAmount_er").val(sss_er/2);
        }
        else
        {
            if(recent_pagibigq == 3){
                $("#pagibigAmount").val(sss*2);
                $("#pagibigAmount_er").val(sss_er*2);
            }
        }
        $("#recent_pagibigq").val($("#pagibigq").val());
    });


    $(document).ready(function(){
        loadhistory();

        /*
         * Save Data
         */

         $("#savesalary").click(function(){
            var form_data = $("#payroll").serialize();
            $.ajax({
                url     :   "<?=site_url("payroll_/ranksaving")?>",
                type    :   "POST",
                data    :   form_data,
                success :   function(msg){

                }
            });
        });

        $("#savesalary").click(function(){
            if(!$("input[name='date_effective']").val()){

                Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: 'Date effective is required!',
                      showConfirmButton: true,
                      timer: 1000
                  })
                return;
            }

            if(!$("select[name='sched']").val()){

                Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: 'Schedule is required!',
                      showConfirmButton: true,
                      timer: 1000
                  })
                return;
            }

            var form_data = $("#payroll").serialize();

            var leclab_arr = [];
            var temparr = {};
            var lechour = labhour = aimsdept = '';
            $('.leclab-pay').each(function(){
                lechour = $(this).find('.lechour').val();
                labhour = $(this).find('.labhour').val();
                aimsdept = $(this).find('.aimsdept').val();
                if(lechour && labhour && aimsdept){
                    temparr = {'lechour':lechour,'labhour':labhour,'aimsdept':aimsdept};
                    leclab_arr.push(temparr);
                }
                temparr = {};
            });

            form_data += "&leclab_arr="+JSON.stringify(leclab_arr);

            $("#dhide").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
            $("#dshow").hide();
            $.ajax({
                url     :   "<?=site_url("payroll_/loadmodelfunc")?>",
                type    :   "POST",
                data    :   form_data,
                success :   function(msg){
                    // console.log(msg);
                    loadhistory();
                    $("#dhide").hide();
                    $("#dshow").show();
                    Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: msg,
                      showConfirmButton: true,
                      timer: 1000
                  })
                }
            });
        });
        
        
        /*
         *  Blur Functions
         */
        $("#monthly").blur(function(){
            var monthly = floorFigure(Number($(this).val()));
            $("#monthly").val(addCommas(monthly));
            // loaddeduction($(this).val()); --ica-hyperion21370 --remove auto compute
        });
       
            
            
        $("#semimonthly").blur(function(){
            $("#semimonthly").val(addCommas($(this).val()));
        });
        $("#biweekly").blur(function(){
            $("#biweekly").val(addCommas($(this).val()));
        });
        $("#weekly").blur(function(){
            $("#weekly").val(addCommas($(this).val()));
        });
        $("#daily").blur(function(){
            $("#daily").val(addCommas($(this).val()));
        });
        $("#hourly").blur(function(){
            $("#hourly").val(addCommas($(this).val()));
        });
        $("#minutely").blur(function(){
            $("#minutely").val(addCommas($(this).val()));
        });
        
        /*
         *  Keyup Functions
         */
        
        // Monthly Salary Computation for the desire no. of workdays per a week.
        $("#ratebased").change(function(){
            $('#semimonthly').keyup();
        });

        $("#monthly").keyup(function(e){
           if (e.keyCode === 9) return false;        
           
           if($("#ratebased").val() == "teaching") workingdays      =   360;
           if($("#ratebased").val() == "nonteaching")workingdays      =  360;
    	
           workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());          
           monthly = $(this).val();     //  monthly value
           loaddaily(workingdays);
           loadhourlyminutely(workingdays,workinghours);
        });
        
        // Semi-Monthly Salary Computation for the desire no. of workdays per a week.
        $("#semimonthly").keyup(function(e){
           if (e.keyCode === 9) return false;
           if($("#ratebased").val() == "teaching") workingdays      =   360;
           if($("#ratebased").val() == "nonteaching")workingdays      =  360;
           workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());          
           semimonthly = $("#semimonthly").val().replace(/,/g, "");     //  semi-monthly value
           // Monthly Salary Computation
           monthly  = Number((semimonthly*24)/12).toFixed(2);                          //   (Monthly Salary * 24 ( Total Semi-Monthly in a year )))   = annual salary   divided by total no. of month in a year.
           $("#monthly").val(addCommas(monthly));
           loaddaily(workingdays,workinghours,"semimonthly");
           loadhourlyminutely(workingdays,workinghours);
        });
        
        // Bi-Weekly Salary Computation for the desire no. of workdays per a week.
        $("#biweekly").keyup(function(e){
           if (e.keyCode === 9) return false; 

           if($("#ratebased").val() == "teaching") workingdays      =   360;
           if($("#ratebased").val() == "nonteaching")workingdays      =  360;

           workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());          
           biweekly = $(this).val();     //  semi-monthly value
           // Monthly Salary Computation
           monthly  = Number((biweekly*26)/12);                          //   (Monthly Salary * 26 ( Total Bi-Weekly in a year )))   = annual salary   divided by total no. of month in a year.
           monthly  = parseFloat(monthly).toFixed(2); 
           $("#monthly").val(addCommas(monthly)); 
           loaddaily(workingdays,workinghours,"biweekly");
           loadhourlyminutely(workingdays,workinghours);
        });
        
        // Weekly Salary Computation for the desire no. of workdays per a week.
        $("#weekly").keyup(function(e){
           if (e.keyCode === 9) return false; 

           if($("#ratebased").val() == "teaching") workingdays      =   360;
           if($("#ratebased").val() == "nonteaching")workingdays      =  360;

           workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());            
           weekly = $(this).val();     //  semi-monthly value
           // Monthly Salary Computation
           monthly  = Number((weekly*52)/12);                          //   (Monthly Salary * 52 ( Total Weekly in a year )))   = annual salary   divided by total no. of month in a year.
           monthly  = parseFloat(monthly).toFixed(2); 
           $("#monthly").val(addCommas(monthly)); 
           loaddaily(workingdays,workinghours,"weekly");
           loadhourlyminutely(workingdays,workinghours);
        });
        
        // Daily Salary Computation for the desire no. of workdays per a week.
        $("#daily").keyup(function(e){
           if (e.keyCode === 9) return false; 

           if($("#ratebased").val() == "teaching") workingdays      =   360;
           if($("#ratebased").val() == "nonteaching")workingdays      =  360;
              
           workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());            
           daily = $(this).val();     //  semi-monthly value
           // Monthly Salary Computation
           monthly  = Number((daily*workingdays)/12).toFixed(2);                          //   (Monthly Salary * ( Total workingdays per week in a year )))   = annual salary   divided by total no. of month in a year.
           $("#monthly").val(addCommas(monthly)); 

           loaddaily(workingdays,workinghours,"daily");
           loadhourlyminutely(workingdays,workinghours);
        });
        
        // Hourly Salary Computation for the desire no. of workdays per a week.
        $("#hourly").keyup(function(e){
           if (e.keyCode === 9) return false; 

           if($("#ratebased").val() == "teaching") workingdays      =   360;
           if($("#ratebased").val() == "nonteaching")workingdays      =  360;

           workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val()); 
           hourly = $(this).val();     //  semi-monthly value
           // Number(((monthly*12)/workingdays)/workinghours);
           // Monthly Salary Computation
           monthly  = Number(((hourly*workingdays)/12)*workinghours);                          //   ((Hourly Salary * ( Total workingdays in a year )))  divided by total no. of month in a year ) multiplied by total no. of workinghours.
           monthly  = parseFloat(monthly).toFixed(2); 
           $("#monthly").val(addCommas(monthly)); 

           loaddaily(workingdays,workinghours,"hourly");
           loadhourlyminutely(workingdays,workinghours,"hourly");
        });
        
        // Minutely Salary Computation for the desire no. of workdays per a week.
        $("#minutely").keyup(function(e){
           if (e.keyCode === 9) return false; 

           if($("#ratebased").val() == "teaching") workingdays      =   360;
           if($("#ratebased").val() == "nonteaching")workingdays      =  360;

           workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());            
           minutely = $(this).val();     //  semi-monthly value
           
           // Number(((monthly*12)/workingdays)/workinghours);
           // Monthly Salary Computation
           monthly  = Number((((minutely*workingdays)/12)*workinghours)*60);                  //   ((Monthly Salary * ( Total workingdays in a year )))   = annual salary   divided by total no. of month in a year ) divided by total no. of workhours
           monthly  = parseFloat(monthly).toFixed(2); 
           $("#monthly").val(addCommas(monthly)); 
           loaddaily(workingdays,workinghours,"minutely");
           loadhourlyminutely(workingdays,workinghours,"minutely");
        });
    });

    $("#sched").change(function(){
        $("#quload").show().html('<div class="form_row"><label class="field_name"></label><div class="field"><div class="col-md-5"><img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</div></div></div>');
        $.ajax({
            url: "<?=site_url('payroll_/loadquarterforsched')?>",
            type: "POST",
            data: {
              schedule  :   $(this).val(), 
              model     :   "quarter"
            },
            success: function(msg){
               $("#quload").hide();
               $("select[name='quarter'],select[name='sssq'],select[name='philhealthq'],select[name='pagibigq'],select[name='peraaq']").html(msg).trigger("chosen:updated");
            }
        });
    });

    /*
     *  Trigger when options are changed.  
     */
    $("#workingdays").change(function(){      
       workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val()); 

        if($("#ratebased").val() == "teaching") workingdays      =   360;
        if($("#ratebased").val() == "nonteaching")workingdays      =  360;

    		//  total desire no. of workdays in a year multiplied to total no of days per week = total no. of workdays in a year.
       if($("#monthly").val() != "")    loaddaily( workingdays, workinghours );                                 
    });                                                                        

    $("#workhours").change(function(){

        if($("#ratebased").val() == "teaching") workingdays      =   360;
        if($("#ratebased").val() == "nonteaching")workingdays      =  360;

        if($("#monthly").val() != "")   loadhourlyminutely( workingdays, ( $(this).val() - $("#workhoursexemp").val() ) );
    });
    $("#workhoursexemp").change(function(){

        if($("#ratebased").val() == "teaching") workingdays      =   360;
        if($("#ratebased").val() == "nonteaching")workingdays      =  360;

        if($("#monthly").val() != "")   loadhourlyminutely( workingdays , ( $("#workhours").val() - $(this).val() ) );
    });

    /*
     *  Trigger when checkbox is checked
     */   
    /*$("#isFixed").click(function() {
        if($(this).is(":checked") == true){
            $('#workingdays').prop('disabled', true).trigger("chosen:updated");
            workingdays      =   360;                
            workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());      
            if($("#monthly").val() != "")    loaddaily( workingdays, workinghours );                         
        }else{
            $('#workingdays').prop('disabled', false).trigger("chosen:updated");
            workingdays      =   Number(52 * $("#workingdays").val());                
            workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());             
            if($("#monthly").val() != "")    loaddaily( workingdays, workinghours );   
        }
    });*/

    $("#type").change(function(){
        var id = $(this).val();
        getRankByType(id);
    });

    $("#rank").change(function(){
        var daily = 0;
        var hourly = 0;
        var minutely = 0;
        var id = $(this).val();
        var teachingtype = "<?=$teachingtype?>";
        $.ajax({
            url: "<?= site_url('setup_/getRankBasicRate') ?>",
            type: "POST",
            data:{id:id},
            success:function(basic_rate){
               $("#monthly").val(addCommas(basic_rate)).keyup();
            }
        });
    });


    /*
     *  Functions to load salary/deductions computation.
     */

    function getRankByType(id){
        if(!id) id = $("#type").val();
        $.ajax({
            url: "<?= site_url('setup_/getRankByType') ?>",
            type: "POST",
            data:{id:id},
            success:function(response){
                $("#rank").html(response).trigger("chosen:updated").change();
            }
        });
    }

    /*
     *  Functions to load salary/deductions computation.
     */

    ///< @Angelica added for multiple leclab rate
    $('.add_leclabpay').on('click',function(){
        var add_ = $(this).parent().parent().clone(true);
        add_.find('.lechour').val(0);
        add_.find('.labhour').val(0);
        add_.find('.aimsdept').val('');
        $('#wrap_leclabpay').append(add_);
    });

    $('.del_leclabpay').on('click',function(){
        var main_parent_ = $(this).parent().parent().parent();
        var sub_parent_ = $(this).parent().parent();

        if($(main_parent_).find('.leclab-pay').length > 1){
            $(sub_parent_).remove();
        }
        else{
            if($(sub_parent_).find('.lechour').val() == 0 && $(sub_parent_).find('.labhour').val() == 0 && $(sub_parent_).find('.aimsdept').val() == ''){

            }else{
                $(sub_parent_).find('.lechour').val(0);
                $(sub_parent_).find('.labhour').val(0);
                $(sub_parent_).find('.aimsdept').val('');
            }
        }

    });

    function loaddecimal(){
        var monthly = floorFigure(Number($("#monthly").val()));
        $("#monthly").val(addCommas(monthly));
        var semimonthly = floorFigure(Number($("#semimonthly").val()));
        $("#semimonthly").val(addCommas(semimonthly));
        var daily = floorFigure(Number($("#daily").val()));
        $("#daily").val(addCommas(daily));
        var hourly = floorFigure(Number($("#hourly").val()));
        $("#hourly").val(addCommas(hourly));
        var minutely = floorFigure(Number($("#minutely").val()));
        $("#minutely").val(addCommas(minutely));
     }

    function loadhourlyminutely(workingdays,workinghours,type){
        var dailyrate = $("#daily").val().replace(/,/g, "");
        $("#daily").val(dailyrate);
       if(type != "hourly"){  
            // Hourly Computation
           //hourly  = Number(((monthly*12)/workingdays)/workinghours);          //   ((Daily Salary / 12)    = annual salary   divided by total no. of daily in a year) divided by total no. of work hours.
           //hourly  = Number($("#daily").val()/workinghours);                     // DYNAMIC daily salary divided by total no. of workhours
           hourly  = floorFigure(Number(($("#daily").val())/8));                     // STATIC daily salary divided by total no. of workhours     
           $("#hourly").val(addCommas(hourly));
       }
       if(type != "minutely"){  
           // Minutely Computation
           //minutely  = Number((((monthly*12)/workingdays)/workinghours)/60);  //   (((Daily Salary / 12)   = annual salary   divided by total no. of daily in a year) divided by total no. of work hours) divided by total no. of minutes in 1 hour.
           //minutely  = Number(($("#daily").val()/workinghours)/60);             // DYNAMIC (daily salary divided by total no. of workhours) divided by total no. of minutes
           minutely  = floorFigure(Number(($("#hourly").val())/60));             // STATIC  (daily salary divided by total no. of workhours) divided by total no. of minutes 
           $("#minutely").val(addCommas(minutely));
       }
    }


    function loaddaily(workingdays,workinghours,type){
           monthly = monthly.replace(/,/g, "");
           if(type != "semimonthly"){ 
               // Semi Monthly Computation  
               semimonthly  = floorFigure(Number((monthly*12)/24));                       //   (Monthly Salary / 12)   = annual salary   divided by total no. of semi monthly in a year.
               $("#semimonthly").val(addCommas(semimonthly));
           }
           if(type != "biweekly"){ 
               // Bi-Weekly Computation
               biweekly  = floorFigure(Number((monthly*12)/26));                              //   (Bi-weekly Salary / 12) = annual salary   divided by total no. of Bi-weekly in a year.
               $("#biweekly").val(addCommas(biweekly));
           }
           if(type != "weekly"){ 
               // Weekly Computation
               weekly  = floorFigure(Number((monthly*12)/52));                                //   (Weekly Salary / 12)    = annual salary   divided by total no. of weekly in a year.
               $("#weekly").val(addCommas(weekly));
           }
           if(type != "daily"){ 
               // Daily Computation
               daily  = floorFigure(Number((monthly*12)/workingdays));
    			//   (Daily Salary / 12)    = annual salary   divided by total no. of daily in a year.
               $("#daily").val(addCommas(daily));
           }
           loadhourlyminutely(workingdays,workinghours,type);
    }

    function loaddeduction(salary){
        $("#deducshow").hide();
        $("#deducload").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");
        $.ajax({
           url      :   "<?=site_url("payroll_/loaddeduction")?>",
           type     :   "POST",
           data     :   {
                            deduc   :   salary
                        },
           success  :   function(msg){
    		 
            $("#deducload").html("");
            $("#deducshow").show();
            var splitstring = msg.split("*");
         
            if ($("#sssq").val() == 1 || $("#sssq").val() == 2 ) {
                 $("#sss").val(splitstring[0]);
                 $("#rrsss").val(splitstring[0]);
                  var b = $("#rsss").val(splitstring[0]);
                // alert(b);
            }
            else
            {
                 $("#rrsss").val(splitstring[0]);
                 $("#sss").val(splitstring[0]/2);

            }   
            
            if ($("#philhealthq").val() == 1 || $("#philhealthq").val() == 2 ) {
                $("#philhealthAmount").val(splitstring[1]); 
                 $("#phsss").val(splitstring[1]);  
                 var b = $("#phs").val(splitstring[0]);
                // alert(b);
            }
            else
            {
                 $("#phsss").val(splitstring[1]);
                 $("#philhealthAmount").val(splitstring[1]/2);  
            }

            if ($("#pagibigq").val() == 1 || $("#pagibigq").val() == 2 ) {
                 $("#pagibigAmount").val(splitstring[2]);
                 $("#pgsss").val(splitstring[2]);
                
                 var b = $("#pgs").val(splitstring[2]);
                // alert(b);
            }
            else
            {
                 
                
                 $("#pgsss").val(splitstring[2]);
                 $("#pagibigAmount").val(splitstring[2]/2);

            }

            //$("#sss").val(splitstring[0]);
            $("#philhealthAmount").val(splitstring[1]);                        
            $("#pagibigAmount").val(splitstring[2]);
           }
        });
    }

    function loadhistory(){
        $.ajax({
          url      :   "<?=site_url("payroll_/getEmployeeSalaryHistory")?>",
          type     :   "POST",
          data     :   {eid: "<?=$eid?>"},
          success  :   function(msg){
           $("#salaryhistory").html(msg);
          }
       });
    }


    /*
     *  Other Functions
     */
     
    function addCommas(nStr)
    {
    	nStr += '';
    	x = nStr.split('.');
    	x1 = x[0];
    	x2 = x.length > 1 ? '.' + x[1] : '';
    	var rgx = /(\d+)(\d{3})/;
    	while (rgx.test(x1)) {
    		x1 = x1.replace(rgx, '$1' + ',' + '$2');
    	}
    	return x1 + x2;
        // return nStr;
    }

    function numbersonly(myfield, e, dec, id)
    {
        var key;
        var keychar;
            
        if (window.event)   key = window.event.keyCode;
        else if (e)         key = e.which;
        else                return true;
        keychar = String.fromCharCode(key);
            
        // control keys
        if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) ) return true;
            
        // numbers
        else if (((id ? "0123456789.- " : "0123456789.").indexOf(keychar) > -1))   return true;
            
        // decimal point jump
        else if (dec && (keychar == "."))
        {
            myfield.form.elements[dec].focus();
            return false;
        }
        else    return false;
    }

    function floorFigure(figure, decimals){
        if (!decimals) decimals = 2;
        var d = Math.pow(10,decimals);
        return (parseInt(figure*d)/d).toFixed(decimals);
    }
        
    /*
     *  Jquery Plug-ins.
     */
    $(".chosen").chosen();
    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });

    setTimeout(
    function() {
        $(".widgets_area").removeClass("animated fadeIn");
    }, 2000);
      
</script>