<?php

/**
 * @author Justin
 * @copyright 2016
 */

$desc = "";
$datetoday = "";
$timetoday = "";
$deptcode = $this->employee->getHeadDeptCode($this->session->userdata('username'));

$isReadonly = "";
$status = isset($status) ? $status : '';

$r = $this->employee->getBudgetOff($this->session->userdata("username"));
if($r != "passed" || $status != "PENDING"){ //IDENTIFY IF THE USER IS NOT THE BUDGET OFFICER
    $isReadonly = "readonly";
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
<!-- <input name="model" value="applySeminar" hidden=""/> -->
<input name="model" value="applySeminarWithSequence" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="70px"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td colspan="2"><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>APPLICATION - ATTENDANCE TO PROFESSIONAL DEVELOPMENT PROGRAMS</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td>
                        <span style="color: #6A1B9A;"><b>Upload Official Invitation<b></span>
                        <input type="file" name="filess" id="file" class="input-large pull-right" >
                        <span id="fileErrorMsg" class="pull-right" hidden="" style="color: red; margin-left: 10px;"></span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="modal-body">
            
            <div class="form_row">
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <select class="chosen col-md-6" id="employeeids" name="employeeids" multiple="">
                        <?
                            foreach ($emplist as $code => $desc) {?>
                                <option value="<?=$code?>"><?=$desc?></option>
                            <?}
                        ?>
                    </select>
                </div>
            </div>
            
            <div class="form_row">
                <label class="field_name align_right"><strong>Purpose of Attendance</strong></label>
                <div class="field">
                    <textarea class="isreq" rows="3" style="width: 100%;resize: none;" name="poa" id="poa" placeholder="Purpose of Attendance"></textarea>
                </div>
            </div>
            
            <div class="form_row">
                <label class="align_right"><h4><strong>PROGRAM DETAILS</strong></h4></label>
            </div>
                        
            <div class="form_row">
                <label class="field_name align_right"><strong>Course Title</strong></label>
                <div class="field no-search">
                    <textarea class="isreq" rows="2" style="width: 100%;resize: none;" name="course" id="course" placeholder="Course Title"></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Date From</strong></label>
                <div class="field">
                    <div class="input-group date" id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="datesetfrom" id="dfrom" type="text" value="<?=$datetoday?>" readonly>
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                    <strong>To</strong>
                    <div class="input-group date" id="datesetto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="datesetto" id="dto" type="text" value="<?=$datetoday?>" readonly>
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Time Start</strong></label>
                <div class="field">
                    <div class="input-group bootstrap-timepicker">
                        <input class="input-small align_center" type="text" name="tfrom" id="tfrom" value="<?=$timetoday?>" style="width: 125px;" />
                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                    </div>
                    <strong>To</strong>
                    <div class="input-group bootstrap-timepicker">
                        <input class="input-small align_center" type="text" name="tto" id="tto" value="<?=$timetoday?>" style="width: 125px;" />
                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Days</strong></label>
                <div class="field no-search" id="dayscon">
                    <input class="isreq" type="text" name="ndays" id="ndays" value="1" readonly="" />
                </div>
            </div>
            
            <div class="form_row">            
                <label class="field_name align_right"><strong>Request for University Assistance </strong></label>
                <div class="field">
                    <table style="border-spacing: 10px;border-collapse: separate;">
                        <tr>
                            <th></th>
                            <th>Requested</th>
                            <th>Approved</th>
                        </tr>
                        <tr style="display:none">
                            <td>Paid Work Day</td>
                            <td><input type="text" name="pwd" id="pwd" value="" onkeypress="return numbersonly(this)" /></td>
                            <td><input type="text" name="pwdApproved" id="pwdApproved" value="" <?=$isReadonly?>/></td>
                        </tr>
                        <tr>
                            <td>Course Feeggg</td>
                            <td><input class="isreq fees" type="text" name="cfee" id="cfee" value="" onkeypress="return numbersonly(event,this)" onblur="total()" /></td>
                            <td><input type="text" name="cfeeApproved" id="cfeeApproved" value="" <?=$isReadonly?>/></td>
                        </tr>
                        <tr>
                            <td>Meal</td>
                            <td><input class="isreq fees" type="text" name="meal" id="meal" value="" onkeypress="return numbersonly(event,this)"  onblur="total()" /></td>
                            <td><input type="text" name="mealApproved" id="mealApproved" value="" <?=$isReadonly?>/></td>
                        </tr>
                        <tr>
                            <td>Transportation</td>
                            <td><input class="isreq fees" type="text" name="transpo" id="transpo" value="" onkeypress="return numbersonly(event,this)"  onblur="total()" /></td>
                            <td><input type="text" name="transpoApproved" id="transpoApproved" value="" <?=$isReadonly?>/></td>
                        </tr>
                        <tr>
                            <td>Hotel</td>
                            <td><input class="isreq fees" type="text" name="hotel" id="hotel" value="" onkeypress="return numbersonly(event,this)"  onblur="total()" /></td>
                            <td><input type="text" name="hotelApproved" id="hotelApproved" value="" <?=$isReadonly?>/></td>
                        </tr>
                        <tr>
                            <td>Other Miscellaneous Fee</td>
                            <td><input class="isreq fees" type="text" name="othermiscellaneous" id="othermiscellaneous" value="" onkeypress="return numbersonly(event,this)"  onblur="total()" /></td>
                            <td><input type="text" name="othermiscellaneousApproved" id="othermiscellaneousApproved" value="" <?=$isReadonly?>/></td>
                        </tr>
                        <tr>
                            <td>Total Cost</td>
                            <td>&#8369; <label name="tcLabel" id="tcLabel"></td>
                            <input type="hidden" name="tc" id="tc" value="" readonly/>
                            <td>&#8369; <label name="tcApprovedLabel" id="tcApprovedLabel" <?=$isReadonly?>/></label></td>
                            <input type="hidden" name="tcApproved" id="tcApproved" value="" <?=$isReadonly?>/>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="form_row">
                <label class="field_name align_right"><strong>Venue</strong></label>
                <div class="field">
                    <textarea class="isreq" rows="3" style="width: 100%;resize: none;" name="venue" id="venue" placeholder="Venue"></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Speaker</strong></label>
                <div class="field">
                    <textarea class="isreq" rows="2" style="width: 100%;resize: none;" name="speaker" id="speaker" placeholder="Speaker"></textarea>
                </div>
            </div>
            <div class="form_row" hidden="">
                <label class="field_name align_right"><strong>Miscellaneous Expenses</strong></label>
                <div class="field">
                    <textarea class="" rows="2" style="width: 100%;resize: none;" name="miscellaneous" id="miscellaneous" placeholder="Miscellaneous Expense"></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Statement of Commitment</strong></label>
                <div class="field">
                    <textarea class="isreq" rows="3" style="width: 100%;resize: none;" name="soc" id="soc" placeholder="Statement of Commitment"></textarea>
                </div>
            </div>
            
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="save" class="btn btn-danger">Apply</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>

$("#save").click(function(){
    $('#fileErrorMsg').html('');
    var iscontinue  = true;
    var data   =   new FormData();

    if($("#ndays").val() < 0){
        alert("Invalid No. of days!.");
        return false;
    }
    $("#frmseminar .isreq").each(function(){
        if($(this).val() == ""){
            $(this).css("border-color","red").attr("placeholder", "This field is required!.");  
            iscontinue = false;
        }
    });
    if($("#ndays").val() < 1){
        alert("Invalid No. of Days..");
        return false;
    }
    // var form_data = $("#frmseminar").serialize();
    // form_data   += "&eid="+$("#employeeids").val();
    var eidObj = new Object();
    eidObj.name = "eid"
    eidObj.value = $("#employeeids").val() + "";

    var form_arr = $("#frmseminar").serializeArray();
    form_arr.push(eidObj);

    var form_data = JSON.stringify(form_arr);

    data.append('form_data',form_data);

    if ( $('#file').get(0).files.length !== 0 ) {
        var file = $('#file')[0].files[0];
        data.append('filess',file);

        ///< validation for file type and size
        $.ajax({
           url      :   "<?=site_url("leave_/validateInvitationFile")?>",
           type     :   "POST",
           data     :   data,
           contentType: false,
           processData: false,
           success  :   function(msg){
            console.log(msg);
                if(msg == 1){
                    iscontinue = false;
                    $('#fileErrorMsg').show().html('Invalid file type. Choose from jpeg/png/pdf.');
                }else if(msg == 2){
                    iscontinue = false;
                    $('#fileErrorMsg').show().html('File too large.');
                }else{
                    $('#fileErrorMsg').html('');
                }
           }
        });
    }

    if(!iscontinue)  return false;
    else{
        $("#saving").hide();
        $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("process_/saveSeminarHRDirect")?>",
           type     :   "POST",
           data     :   data,
           contentType: false,
           processData: false,
           success  :   function(msg){
            alert(msg);
            // console.log(msg);
            //loadbushistory();
            $("#close").click();
            $(".inner_navigation .main li .active a").click();
           }
        });

    }
});

/*$( "#upload" ).submit(function( event ) {
  if ( $('#file').get(0).files.length === 0 ) {
    $( "#msg" ).text( "No files selected.." ).show();
    return false;
  }
});*/

$('.fees').on('input',function(){
    var fee = $(this).attr('name'),
        feeval = $(this).val();
    if(feeval==0) $('#'+fee+'Approved').val(0);
    else            $('#'+fee+'Approved').val('');
});


$("input[name='datesetfrom']").change(function(){
   var  start = new Date($(this).val()),
        end   = new Date($("#dto").val()),
        diff  = new Date(end - start),
        days  = diff/1000/60/60/24;
        if(days >= 0)   days += 1;
        $("#ndays").val(days);
        if(days < 1)    $("#dayscon").append("<span style='color: red' id='err'> Invalid no. of days!.</span>");
        else            $("#err").remove();
});
$("input[name='datesetto']").change(function(){
   var  start = new Date($(this).val()),
        end   = new Date($("#dfrom").val()),
        diff  = new Date(start - end),
        days  = diff/1000/60/60/24;
        if(days >= 0)   days += 1;
        $("#ndays").val(days);
        if(days < 1)    $("#dayscon").append("<span style='color: red' id='err'> Invalid no. of days!.</span>");
        else            $("#err").remove();
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

/*
 * Functions
 */
function numbersonly(evt, myfield, e, dec, id)
{ ///< edited for cross-browser compatibility
    var key;
    var keychar;
    var e = evt || window.event;
    if (e)         key = e.which || e.keyCode;
    // else if (window.event)   key = window.event.keyCode;
    // else                return true;
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

function total() 
{
    var courseFee = 0;
    var meal = 0;
    var transportation = 0;
    var hotel = 0;
    var miscellaneous = 0;
    if($("#cfee").val())
    {
        courseFee = $("#cfee").val();
    }
    if($("#meal").val())
    {
        meal = $("#meal").val();
    }
    if($("#transpo").val())
    {
        transportation = $("#transpo").val();
    }
    if($("#hotel").val())
    {
        hotel = $("#hotel").val();
    }
    if($("#othermiscellaneous").val())
    {
        miscellaneous = $("#othermiscellaneous").val();
    }
    
    $("#tc").val(parseFloat(courseFee) + parseFloat(meal) + parseFloat(transportation) + parseFloat(hotel) + parseFloat(miscellaneous));
    $("#tcLabel").html(parseFloat(courseFee) + parseFloat(meal) + parseFloat(transportation) + parseFloat(hotel) + parseFloat(miscellaneous));
}

</script>