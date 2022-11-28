<?php

/**
 * @author Justin
 * @copyright 2016
 */

$desc = "";
$datetoday = date("d-m-Y");
$timetoday = date("h:i A");
$deptcode = $this->employee->getHeadDeptCode($this->session->userdata('username'));

$purpose = $course = $dfrom = $dto = $tstart = $tend = $nodays = $paiddays = $cfee = $meal = $transpo = $hotel = $tcost = $venue = $speaker = $misc = $statemnt = "";
$eid = array();
if($this->input->post("idkey")){
    $qmod = $this->employeemod->seminar_modify_query($this->input->post("idkey"));
    $purpose = $qmod->row()->purpose;
    $course  = $qmod->row()->course;
    $dfrom   = $qmod->row()->dfrom;
    $dto     = $qmod->row()->dto;
    $tstart  = $qmod->row()->tstart;
    $tend    = $qmod->row()->tend;
    $nodays  = $qmod->row()->nodays;
    $paiddays= $qmod->row()->paiddays;
    $cfee    = $qmod->row()->coursefee;
    $meal    = $qmod->row()->meal;
    $transpo = $qmod->row()->transportation;
    $hotel   = $qmod->row()->hotel;
    $tcost   = $qmod->row()->totalcost;
    $venue   = $qmod->row()->venue;
    $speaker = $qmod->row()->speaker;
    $misc    = $qmod->row()->miscellaneous;
    $statemnt= $qmod->row()->statement;
    foreach($qmod->result() as $rdata){
        $eid[] = $rdata->employeeid;    
    }
}
?>
<style>
.modal{
    width: 75%;
    left: 0;
    right: 0;
    margin: auto;
}
</style>
<form id="frmseminar">
<input name="model" value="modifySeminar" hidden=""/>
<input name="id" value="<?=$this->input->post("idkey")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="70px"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                </tr>
                <tr>
                    <td><strong>APPLICATION - ATTENDANCE TO PROFESSIONAL DEVELOPMENT PROGRAMS</strong></td>
                </tr>
            </table>
        </div>
        <div class="modal-body">
            
            <div class="form_row">
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <select class="chosen col-md-6" id="employeeid" name="employeeid" multiple="">
                        <?
                            # ica-hyperion 21128
                            $empId = $this->session->userdata('username');
                            $res = $this->db->query("SELECT CONCAT(lname, ', ', fname, ' ', mname) AS fullname FROM employee WHERE employeeid='$empId'");
                        ?>
                        <option value="<?=$empId?>" selected><?=$empId." - ".$res->row()->fullname?></option>
                    </select>
                    </select>
                </div>
            </div>
            
            <div class="form_row">
                <label class="field_name align_right"><strong>Purpose of Attendance</strong></label>
                <div class="field">
                    <textarea class="isreq" rows="3" style="width: 100%;resize: none;" name="poa" id="poa" placeholder="Purpose of Attendance"><?=$purpose?></textarea>
                </div>
            </div>
            
            <div class="form_row">
                <label class="align_right"><h4><strong>PROGRAM DETAILS</strong></h4></label>
            </div>
                        
            <div class="form_row">
                <label class="field_name align_right"><strong>Course Title</strong></label>
                <div class="field no-search">
                    <textarea class="isreq" rows="2" style="width: 100%;resize: none;" name="course" id="course" placeholder="Course Title"><?=$course?></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Date From</strong></label>
                <div class="field">
                    <div class="input-group date" id="datesetfrom" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="datesetfrom" type="text" value="<?=$dfrom?>" readonly>
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                    <strong>To</strong>
                    <div class="input-group date" id="datesetto" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="datesetto" type="text" value="<?=$dto?>" readonly>
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Time Start</strong></label>
                <div class="field">
                    <div class="input-group bootstrap-timepicker">
                        <input class="input-small align_center" type="text" name="tfrom" id="tfrom" value="<?=$tstart?>" style="width: 125px;" />
                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                    </div>
                    <strong>To</strong>
                    <div class="input-group bootstrap-timepicker">
                        <input class="input-small align_center" type="text" name="tto" id="tto" value="<?=$tend?>" style="width: 125px;" />
                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Days</strong></label>
                <div class="field no-search">
                    <input class="isreq" type="text" name="ndays" id="ndays" value="<?=$nodays?>" readonly="" />
                </div>
            </div>
            
            <div class="form_row" hidden="true">            
                    <label class="field_name align_right"><strong>Request for University Assistance </strong></label>
                    <label class="field_name align_right">Paid Work Days&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <div class="field no-search">
                        <input class="" type="text" name="pwd" id="pwd" value="<?=$paiddays?>" />
                    </div>
            </div>
            <div class="form_row">
                <div class="field">
                    <label class="field_name align_right">Course Fee</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="cfee" id="cfee" value="<?=$cfee?>" />
                    </div>
                </div>
            </div>
            <div class="form_row">
                <div class="field">
                    <label class="field_name align_right">Meal</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="meal" id="meal" value="<?=$meal?>" />
                    </div>
                </div>
            </div>
            <div class="form_row">
                <div class="field">
                    <label class="field_name align_right">Transportation</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="transpo" id="transpo" value="<?=$transpo?>" />
                    </div>
                </div>
            </div>
            <div class="form_row">
                <div class="field">
                    <label class="field_name align_right">Hotel</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="hotel" id="hotel" value="<?=$hotel?>" />
                    </div>
                </div>
            </div>
            <div class="form_row">
                <div class="field">
                    <label class="field_name align_right">Total Cost</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="tc" id="tc" value="<?=$tcost?>" />
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Venue</strong></label>
                <div class="field">
                    <textarea class="isreq" rows="3" style="width: 100%;resize: none;" name="venue" id="venue" placeholder="Venue"><?=$venue?></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Speaker</strong></label>
                <div class="field">
                    <textarea class="isreq" rows="2" style="width: 100%;resize: none;" name="speaker" id="speaker" placeholder="Speaker"><?=$speaker?></textarea>
                </div>
            </div>
            <div class="form_row" hidden="true">
                <label class="field_name align_right"><strong>Miscellaneous Expenses</strong></label>
                <div class="field">
                    <textarea class="" rows="2" style="width: 100%;resize: none;" name="miscellaneous" id="miscellaneous" placeholder="Miscellaneous Expense"><?=$misc?></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Statement of Commitment</strong></label>
                <div class="field">
                    <textarea class="isreq" rows="3" style="width: 100%;resize: none;" name="soc" id="soc" placeholder="Statement of Commitment"><?=$statemnt?></textarea>
                </div>
            </div>
            
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="save" class="btn btn-danger">Save</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
$("#employeeid").attr('disabled','disabled');
$("#save").click(function(){
    if($("#ndays").val() < 0){
        alert("Invalid No. of days!.");
        return false;
    }
    var iscontinue  = true;
    var form_data   =   $("#frmseminar").serialize();
        form_data   += "&eid="+$("#employeeid").val();
    $("#frmseminar .isreq").each(function(){
        if($(this).val() == ""){
            $(this).css("border-color","red").attr("placeholder", "This field is required!.");  
            iscontinue = false;
        }
    });
    if(!iscontinue)  return false;
    else{
        $("#saving").hide();
        $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
           type     :   "POST",
           data     :   form_data,
           success  :   function(msg){
            alert(msg);
            loadbushistory("","","");
            $("#close").click();
           }
        });
    }
});

$("input[name='datesetfrom']").change(function(){
   var  start = new Date($(this).val()),
        end   = new Date($("input[name='datesetto']").val()),
        diff  = new Date(end - start),
        days  = diff/1000/60/60/24;
        if(days >= 0)   days += 1;
        $("#ndays").val(days);
});
$("input[name='datesetto']").change(function(){
   var  start = new Date($(this).val()),
        end   = new Date($("input[name='datesetfrom']").val()),
        diff  = new Date(start - end),
        days  = diff/1000/60/60/24;
        if(days >= 0)   days += 1;
        $("#ndays").val(days);
});
$("#datesetfrom,#datesetto").datepicker({
    autoclose: true,
    todayBtn : true
});
$("input[name='tfrom'],input[name='tto']").timepicker({
    minuteStep: 1,
    showSeconds: false,
    showMeridian: true,
    defaultTime: false
  });
$(".chosen").chosen();
</script>