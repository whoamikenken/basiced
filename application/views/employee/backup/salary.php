<?php
/**
 * @author Justin
 * @copyright 2016
 */
 
 $emp = "";
 if($this->input->post("eid")){
     foreach($this->input->post("eid") as $key=>$val){
        if($emp)    $emp .= ",";
        $emp .= $val;
     }    
 }
$workingday = $fixedday = $workhours = $workhoursexemp = $monthly = $semimonthly = $biweekly = $weekly = $daily = $hourly = $minutely = $sched = $sssid = $sssamount = $sssquarter = $sssquarter = $pagibigid = $pagibigamount = $pagibigquarter = $philhealthid = $philhealthamount = $philhealthquarter = $taxstatus = $whtax = $absents = $balance= "";
 $row = $this->payroll->displaySalary($emp);
 foreach($row as $data){
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
     $sssamount         = !empty($data->sssamount)          ? $data->sssamount          : "";
     $sssquarter        = !empty($data->sssquarter)         ? $data->sssquarter         : "";
     $pagibigid         = !empty($data->pagibigid)          ? $data->pagibigid          : "";
     $pagibigamount     = !empty($data->pagibigamount)      ? $data->pagibigamount      : "";
     $pagibigquarter    = !empty($data->pagibigquarter)     ? $data->pagibigquarter     : "";
     $philhealthid      = !empty($data->philhealthid)       ? $data->philhealthid       : "";
     $philhealthamount  = !empty($data->philhealthamount)   ? $data->philhealthamount   : "";
     $philhealthquarter = !empty($data->philhealthquarter)  ? $data->philhealthquarter  : "";
 }
?>
<style>
#myModal{
    width: 90%;
    left: 0;
    right: 0;
    margin: 0 auto;
}
</style>
<div style="float: right;padding: 1px;"><a href="#" class="btn btn-danger" close>X</a></div>
<div class="container">
    <form id="myform">  
        <div class="col-md-12">
            <div class="col-md-8" >
            <!--<input type="hidden" name="model" value="esalary" />-->
            <input type="hidden" name="model"   value="batchencode" />
            <input type="hidden" name="dept"    value="<?=$this->input->post("dept")?>" />
            <input type="hidden" name="tnt"     value="<?=$this->input->post("tnt")?>" />
            <input type="hidden" name="estat"   value="<?=$this->input->post("estat")?>" />
            <input type="hidden" name="eid"     value="<?=$emp?>" />
            <input type="hidden" name="cat"     value="<?=$this->input->post("cat")?>" />
            
            <?if($this->input->post("cat") == 1){?>
                <div class="form_row">
                </div>
                <div class="form_row">
                    <label class="field_name align_right"></label>
                    <div class="field">
                        <input class="col-md-2 align_center" style='background-color: #FAFAFA; font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" value="Salary"/>
                    </div>
                </div>
                <div class="form_row">
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Regular Working Days Per Week</label>
                    <div class="field no-search">
                        <select class="form-control" id="workingdays" name="workingdays" <?=($fixedday) ? " disabled" : ""?>>
                            <?=$this->payrolloptions->viewWorkingdays();?>
                        </select>
                        <input class="col-md-2" type="checkbox" style="-webkit-transform: scale(1);" name="isFixed"  id="isFixed" value="1" <?=($fixedday) ? " checked" : ""?>/>(Note : Check if fixed to 30 days)
                    </div>
                </div>
                <div class="form_row" hidden="">
                    <label class="field_name align_right">Work Hours</label>
                    <div class="field">
                        <select class="form-control" id="workhours" name="workhours">
                            <?=$this->payrolloptions->viewWorkHours(false);?>
                        </select>
                    </div>
                </div>
                <div class="form_row" hidden="">
                    <label class="field_name align_right">Work Hours Exemption</label>
                    <div class="field">
                        <select class="form-control" id="workhoursexemp" name="workhoursexemp">
                            <?=$this->payrolloptions->viewWorkHours(true);?>
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Monthly</label>
                    <div class="field">
                        <input class="col-md-6 align_center" id="monthly" name="monthly" type="text" value="<?=$monthly?>" onkeypress="return numbersonly(this)"/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Semi-Monthly</label>
                    <div class="field">
                        <input class="col-md-6 align_center" id="semimonthly" name="semimonthly" value="<?=$semimonthly?>" type="text" value="" onkeypress="return numbersonly(this)"/>
                    </div>
                </div>
                <div class="form_row" hidden="">
                    <label class="field_name align_right">Bi-Weekly</label>
                    <div class="field">
                        <input class="col-md-6 align_center" id="biweekly" name="biweekly" value="" type="text" value="<?=$biweekly?>" onkeypress="return numbersonly(this)"/>
                    </div>
                </div>
                <div class="form_row" hidden="">
                    <label class="field_name align_right">Weekly</label>
                    <div class="field">
                        <input class="col-md-6 align_center" id="weekly" name="weekly" value="" type="text" value="<?=$weekly?>" onkeypress="return numbersonly(this)"/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Daily</label>
                    <div class="field">
                        <input class="col-md-6 align_center" id="daily" name="daily" type="text" value="<?=$daily?>" onkeypress="return numbersonly(this)"/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Hourly</label>
                    <div class="field">
                        <input class="col-md-6 align_center" id="hourly" name="hourly" type="text" value="<?=$hourly?>" onkeypress="return numbersonly(this)"/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Minutely</label>
                    <div class="field">
                        <input class="col-md-6 align_center" id="minutely" name="minutely" type="text" value="<?=$minutely?>" onkeypress="return numbersonly(this)"/>
                    </div>
                </div>
            <?}?>
            </div>
            <?if($this->input->post("cat") == 2){?>
            <div class="col-md-6">
                <div class="form_row">
                </div>
                <div class="form_row">
                    <label class="field_name align_right"></label>
                    <div class="field">
                        <input class="col-md-3 align_center" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" value="Dependents"/>
                    </div>
                </div>
                <div class="form_row">
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Tax Status</label>
                    <div class="field no-search">
                        <select class="chosen col-md-6 align_left" name="tax_status" id="tax_status"><?=$this->payrolloptions->taxdependents($taxstatus);?></select>
                    </div>
                </div>
            </div><br /><br />
            <?}?>
            <?if($this->input->post("cat") == 3){?>
            <!-- Schedule -->
            <div class="col-md-6">
                <h3>Payment Schedule</h3>
                <div class="form_row">
                    <label class="field_name align_right">Schedule</label>
                    <div class="field no-search">
                        <select class="chosen col-md-4 align_left" name="sched" id="sched"><?=$this->payrolloptions->payschedule($sched);?></select>
                    </div>
                </div>
            </div><br />
            <div id="quload" hidden=""></div>
            <?}?>
            <?if($this->input->post("cat") == 4){?>
            <div class="col-md-6">
                <h3>Tax</h3>
                <div class="form_row">
                    <label class="field_name align_right">WithHolding Tax</label>
                    <div class="field no-search">
                        <input class="col-md-3 align_center" id="whtax" name="whtax" type="text" value="<?=$whtax?>" onkeypress="return numbersonly(this)"/>
                    </div>
                </div>
            </div>
            <?}?>
            <?if($this->input->post("cat") == 5){?>
            <div class="col-md-6">
                <h3>Absent &amp; Balance</h3>
                <div class="form_row">
                    <label class="field_name align_right">Absent</label>
                    <div class="field no-search">
                        <input class="col-md-3 align_center" id="absents" name="absents" type="text" value="<?=$absents?>" onkeypress="return numbersonly(this)"/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Balance</label>
                    <div class="field no-search">
                        <input class="col-md-3 align_center" id="balance" name="balance" type="text" value="<?=$balance?>" onkeypress="return numbersonly(this)"/>
                    </div>
                </div>
            </div>
            <?}?>
            <?if($this->input->post("cat") == 6){?>
            <div class="col-md-12">
                <h3>Deduction</h3>
                <div class="form_row">
                    <label class="field_name align_right"></label>
                    <div class="field">
                        <input class="col-md-3 align_center" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" value="Member ID"/>
                        <input class="col-md-3 align_center" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" value="Contribution"/>
                        <input class="col-md-3 align_center" style='background-color: #FAFAFA;font-weight: bolder !important;border: transparent !important;font-family: "Open Sans", sans-serif;' type="text" value="Quarter"/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">SSS</label>
                    <div class="field no-search">
                        <input type="hidden" name="ssdesc" value="sss"/>
                        <input class="col-md-3 align_center" id="sssid" name="sssid" value="<?=$sssid?>" type="text" onkeypress="return numbersonly(this,'false','false','id')" />
                        <input class="col-md-3 align_center" id="sss" name="sss" value="<?=$sssamount?>"type="text"onkeypress="return numbersonly(this)" />
                        <select class="chosen col-md-4 align_left" name="sssq" id="sssq"><?=$this->payrolloptions->quarter($sssquarter,"","weekly");?></select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">PhilHealth</label>
                    <div class="field">
                        <input type="hidden" name="phdesc" value="philhealth"/>
                        <input class="col-md-3 align_center" id="philhealthid" name="philhealthid" value="<?=$philhealthid?>" type="text" onkeypress="return numbersonly(this,'false','false','id')" />
                        <input class="col-md-3 align_center" id="philhealth" name="philhealth" value="<?=$philhealthamount?>" type="text" value="" onkeypress="return numbersonly(this)" />
                        <select class="chosen col-md-4 align_left" name="philhealthq" id="philhealthq"><?=$this->payrolloptions->quarter($philhealthquarter,"","weekly");?></select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Pag-ibig</label>
                    <div class="field">
                        <input type="hidden" name="pagibigdesc" value="pagibig"/>
                        <input class="col-md-3 align_center" id="pagibigid" name="pagibigid" value="<?=$pagibigid?>" type="text" onkeypress="return numbersonly(this,'false','false','id')" />
                        <input class="col-md-3 align_center" id="pagibig" name="pagibig" value="<?=$pagibigamount?>" type="text" onkeypress="return numbersonly(this)"/>
                        <select class="chosen col-md-4 align_left" name="pagibigq" id="pagibigq"><?=$this->payrolloptions->quarter($pagibigquarter,"","weekly");?></select>
                    </div>
                </div>
            </div>
            <?}?> 
            <?if($this->input->post("cat") == 7){?>
            <div class="col-md-12">  
                <h3>Income</h3>
                <div class="form_row">
                    <label class="field_name">Income</label>
                    <div class="field no-search">
                        <select id="income_drop" name="income_drop" class="form-control" name="tax_status"><?=$this->payrolloptions->income();?></select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Deduction Range</label>
                    <div class="field">
                        <div class="input-group date" id="datefrom" data-date="" data-date-format="yyyy-mm-dd">
                            <input size="16" class="align_center required" type="text" name="datefrom" value="" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Amount</label>
                    <div class="field">
                        <input class="align_right col-md-4 required" id="amountincome" name="amountincome" type="text" value=""/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">No. of Cut-off</label>
                    <div class="field">
                        <input class="align_right col-md-4 required" id="nocutoff" name="nocutoff" type="text" value=""/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Schedule</label>
                    <div class="field no-search">
                        <select class="chosen align_left" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule();?></select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Quarter</label>
                    <div class="field no-search">
                        <select id="period_drop" name="period_drop" class="form-control"><?=$this->payrolloptions->quarter("","","weekly");?></select>
                    </div>
                </div>
            </div>
            <?}?>
            <?if($this->input->post("cat") == 8){?>
            <div class="col-md-12">
                <h3>Loan</h3>
                <div class="form_row">
                    <label class="field_name">Loan</label>
                    <div class="field no-search">
                        <select id="dloan_drop" name="dloan_drop" class="form-control" name="dtax_status"><?=$this->payrolloptions->loan();?></select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Deduction Date</label>
                    <div class="field">
                        <div class="input-group date" id="ddatefrom" data-date="" data-date-format="yyyy-mm-dd">
                            <input size="16" class="align_center required" type="text" name="ddatefrom" value="" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Starting Balance</label>
                    <div class="field">
                         <input class="align_right col-md-4 required" id="dstartingamount" name="dstartingamount" type="text" value=""/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Amount</label>
                    <div class="field">
                         <input class="align_right col-md-4 required" id="damountloan" name="damountloan" type="text" value=""/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Last Amount</label>
                    <div class="field">
                         <input class="align_right col-md-4 required" id="dfamount" name="dfamount" type="text" value=""/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">No. of Cut-off</label>
                    <div class="field">
                         <input class="align_right col-md-4 required" id="dnocutoff" name="dnocutoff" type="text" value=""/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Schedule</label>
                    <div class="field no-search">
                        <select class="chosen align_left" name="dschedule" id="dschedule"><?=$this->payrolloptions->payschedule($schedule);?></select>
                    </div>
                </div>
                <div class="content" style="margin-top: 3px;" id="qload" hidden=""></div>
                <div class="form_row" id="qshow" hidden="">
                    <label class="field_name">Quarter</label>
                    <div class="field no-search">
                        <select id="dperiod_drop" name="dperiod_drop" class="form-control"><?=$this->payrolloptions->quarter($cperiod,FALSE,$schedule);?></select>
                    </div>
                </div>
            </div>
            <?}?> 
            <?if($this->input->post("cat") == 9){?>
            <div class="col-md-12">
                <h3>Other Income</h3>
                <div class="form_row">
                    <label class="field_name">Income</label>
                    <div class="field no-search">
                        <select id="othincome_drop" name="othincome_drop" class="form-control" name="tax_status"><?=$this->payrolloptions->incomeoth();?></select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Amount</label>
                    <div class="field">
                         <input class="align_right col-md-4 required" id="othamountincome" name="othamountincome" type="text" value=""/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name">Position</label>
                    <div class="field no-search">
                         <select class="form-control" name="othpos" id="othpos">
                            <option value="lower" >Lower</option>
                            <option value="upper" >Upper</option>
                         </select>
                    </div>
                </div>
            </div>            
            <?}?>
    </form>
            <div class="col-md-8" style="margin-top: 5px;margin-bottom: 5px;" >
                <div class="form_row">
                </div>
                <div class="form_row">
                    <div class="field">
                        <div id="dhide" hidden=""></div>
                        <div id="dshow">
                            <a href="#" class="btn btn-primary" id="savesalary">Save</a>
                            <a href="#" class="btn btn-danger" close>Close</a>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                </div>
            </div>
        </div>
</div>
<script>
var workingdays,workinghours,monthly,semimonthly,biweekly,weekly,daily,hourly,minutely,totalhours,salaryperday    = 0;
$(document).ready(function(){
    /*
     * Save Data
     */
    $("#savesalary").click(function(){
        
        var form_data = $("#myform").serialize();
        $("#dhide").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        $("#dshow").hide();
        $.ajax({
            url     :   "<?=site_url("payroll_/loadmodelfunc")?>",
            type    :   "POST",
            data    :   form_data,
            success :   function(msg){
                $("#dhide").hide();
                $("#dshow").show();
                alert(msg);
                $("#myModal").modal('toggle'); 
            }
        });
        
    });
    
    /*
     *  Blur Functions
     */
    $("#monthly").blur(function(){
        $("#monthly").val(addCommas($(this).val()));
        loaddeduction($(this).val());
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
    $("#monthly").keyup(function(e){
       if (e.keyCode === 9) return false;        
       if($("#isFixed").is(":checked") == true)
        workingdays      =   360;
       else
        workingdays      =   Number(52 * $("#workingdays").val());
       workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());          
       monthly = $(this).val();     //  monthly value
       loaddaily(workingdays);
       loadhourlyminutely(workingdays,workinghours);
    });
    
    // Semi-Monthly Salary Computation for the desire no. of workdays per a week.
    $("#semimonthly").keyup(function(e){
       if (e.keyCode === 9) return false;
       if($("#isFixed").is(":checked") == true)
        workingdays      =   360;
       else
        workingdays      =   Number(52 * $("#workingdays").val());
       workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());          
       semimonthly = $(this).val();     //  semi-monthly value
       // Monthly Salary Computation
       monthly  = Number((semimonthly*24)/12);                          //   (Monthly Salary * 24 ( Total Semi-Monthly in a year )))   = annual salary   divided by total no. of month in a year.
       monthly  = parseFloat(monthly).toFixed(2); 
       $("#monthly").val(addCommas(monthly));
       loaddaily(workingdays,workinghours,"semimonthly");
       loadhourlyminutely(workingdays,workinghours);
    });
    
    // Bi-Weekly Salary Computation for the desire no. of workdays per a week.
    $("#biweekly").keyup(function(e){
       if (e.keyCode === 9) return false; 
       if($("#isFixed").is(":checked") == true)
        workingdays      =   360;
       else
        workingdays      =   Number(52 * $("#workingdays").val()); 
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
       if($("#isFixed").is(":checked") == true)
        workingdays      =   360;
       else
        workingdays      =   Number(52 * $("#workingdays").val()); 
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
       if($("#isFixed").is(":checked") == true)
        workingdays      =   360;
       else
        workingdays      =   Number(52 * $("#workingdays").val());                       
       workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());            
       daily = $(this).val();     //  semi-monthly value
       // Monthly Salary Computation
       monthly  = Number((daily*workingdays)/12);                          //   (Monthly Salary * ( Total workingdays per week in a year )))   = annual salary   divided by total no. of month in a year.
       monthly  = parseFloat(monthly).toFixed(2); 
       $("#monthly").val(addCommas(monthly)); 
       loaddaily(workingdays,workinghours,"daily");
       loadhourlyminutely(workingdays,workinghours);
    });
    
    // Hourly Salary Computation for the desire no. of workdays per a week.
    $("#hourly").keyup(function(e){
       if (e.keyCode === 9) return false; 
       if($("#isFixed").is(":checked") == true)
        workingdays      =   360;
       else
        workingdays      =   Number(52 * $("#workingdays").val()); 
       workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());            
       hourly = $(this).val();     //  semi-monthly value
       
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
       if($("#isFixed").is(":checked") == true)
        workingdays      =   360;
       else
        workingdays      =   Number(52 * $("#workingdays").val()); 
       workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());            
       minutely = $(this).val();     //  semi-monthly value
       
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
           $("select[name='quarter'],select[name='sssq'],select[name='philhealthq'],select[name='pagibigq']").html(msg).trigger("liszt:updated");
        }
    });
});

/*
 *  Trigger when options are changed.  
 */
$("#workingdays").change(function(){      
   workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val()); 
   workingdays =   Number(52 * $(this).val());                             //  total desire no. of workdays in a year multiplied to total no of days per week = total no. of workdays in a year.
   if($("#monthly").val() != "")    loaddaily( workingdays, workinghours );                                 
});                                                                        

$("#workhours").change(function(){
    workingdays =   Number(52 * $("#workingdays").val());
    if($("#monthly").val() != "")   loadhourlyminutely( workingdays, ( $(this).val() - $("#workhoursexemp").val() ) );
});
$("#workhoursexemp").change(function(){
    workingdays =   Number(52 * $("#workingdays").val());
    if($("#monthly").val() != "")   loadhourlyminutely( workingdays , ( $("#workhours").val() - $(this).val() ) );
});

/*
 *  Trigger when checkbox is checked
 */   
$("#isFixed").click(function() {
    if($(this).is(":checked") == true){
        $('#workingdays').prop('disabled', true).trigger("liszt:updated");
        workingdays      =   360;                
        workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());      
        if($("#monthly").val() != "")    loaddaily( workingdays, workinghours );                         
    }else{
        $('#workingdays').prop('disabled', false).trigger("liszt:updated");
        workingdays      =   Number(52 * $("#workingdays").val());                
        workinghours    =   ($("#workhours").val() - $("#workhoursexemp").val());             
        if($("#monthly").val() != "")    loaddaily( workingdays, workinghours );   
    }
});


/*
 *  Functions to load salary/deductions computation.
 */
 
function loadhourlyminutely(workingdays,workinghours,type){
       if(type != "hourly"){  
           hourly  = Number(($("#daily").val())/8);                     // STATIC daily salary divided by total no. of workhours     
           hourly  = parseFloat(hourly).toFixed(2);
           $("#hourly").val(addCommas(hourly));
       }
       if(type != "minutely"){  
           minutely  = Number(($("#hourly").val())/60);             // STATIC  (daily salary divided by total no. of workhours) divided by total no. of minutes 
           minutely  = parseFloat(minutely).toFixed(2);
           $("#minutely").val(addCommas(minutely));
       }
}

function loaddaily(workingdays,workinghours,type){
        
       if(type != "semimonthly"){ 
           // Semi Monthly Computation  
           semimonthly  = Number((monthly*12)/24);                       //   (Monthly Salary / 12)   = annual salary   divided by total no. of semi monthly in a year.
           semimonthly  = parseFloat(semimonthly).toFixed(2); 
           $("#semimonthly").val(addCommas(semimonthly));
       }
       if(type != "biweekly"){ 
           // Bi-Weekly Computation
           biweekly  = Number((monthly*12)/26);                              //   (Bi-weekly Salary / 12) = annual salary   divided by total no. of Bi-weekly in a year.
           biweekly  = parseFloat(biweekly).toFixed(2);
           $("#biweekly").val(addCommas(biweekly));
       }
       if(type != "weekly"){ 
           // Weekly Computation
           weekly  = Number((monthly*12)/52);                                //   (Weekly Salary / 12)    = annual salary   divided by total no. of weekly in a year.
           weekly  = parseFloat(weekly).toFixed(2);
           $("#weekly").val(addCommas(weekly));
       }
       if(type != "daily"){ 
           // Daily Computation
           daily  = Number((monthly*12)/workingdays);                        //   (Daily Salary / 12)    = annual salary   divided by total no. of daily in a year.
           daily  = parseFloat(daily).toFixed(2);
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
        $("#sss").val(splitstring[0]);
        $("#philhealth").val(splitstring[1]);                        
        $("#pagibig").val(splitstring[2]);
       }
    });
}
$("a[close]").click(function(){   $("#myModal").modal('toggle');  });

$("#schedule").change(function(){
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :   $(this).val(), 
          model     :   "quarter"
        },
        success: function(msg){
           $("select[name='period_drop']").html(msg).trigger("liszt:updated");
        }
    });
});

$("#dschedule").change(function(){
    $("#qshow").hide();
    $("#qload").show().html('<div class="form_row"><label class="field_name"></label><div class="field"><div class="col-md-5"><img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</div></div></div>');
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :   $(this).val(), 
          model     :   "quarter"
        },
        success: function(msg){
           $("#qload").hide();
           $("select[name='dperiod_drop']").html(msg).trigger("liszt:updated");
           $("#qshow").show();
        }
    });
});

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
    return nStr;
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
    
/*
 *  Jquery Plug-ins.
 */
 
$(".chosen").chosen();
$('#datefrom,#dateto,#ddatefrom').datepicker();
</script>