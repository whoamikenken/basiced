<?php

/**
 * @author Justin
 * @copyright 2016
 */

$desc = "";
$datetoday = date("d-m-Y");
$timetoday = date("h:i A");
$deptcode = $this->employee->getHeadDeptCode($this->session->userdata('username'));
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
                        $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",true,'',$deptcode);
                        foreach($opt_type as $val){
                        ?>
                        <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                        }
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
                    <label class="field_name align_right">Paid Work Days&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="pwd" id="pwd" value="" onkeypress="return numbersonly(this)" />
                    </div>
            </div>
            <div class="form_row">
                <div class="field">
                    <label class="field_name align_right">Course Fee</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="cfee" id="cfee" value="" onkeypress="return numbersonly(this)" />
                    </div>
                </div>
            </div>
            <div class="form_row">
                <div class="field">
                    <label class="field_name align_right">Meal</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="meal" id="meal" value="" onkeypress="return numbersonly(this)" />
                    </div>
                </div>
            </div>
            <div class="form_row">
                <div class="field">
                    <label class="field_name align_right">Transportation</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="transpo" id="transpo" value="" onkeypress="return numbersonly(this)" />
                    </div>
                </div>
            </div>
            <div class="form_row">
                <div class="field">
                    <label class="field_name align_right">Hotel</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="hotel" id="hotel" value="" onkeypress="return numbersonly(this)" />
                    </div>
                </div>
            </div>
            <div class="form_row">
                <div class="field">
                    <label class="field_name align_right">Total Cost</label>
                    <div class="field no-search">
                        <input class="isreq" type="text" name="tc" id="tc" value="" onkeypress="return numbersonly(this)" />
                    </div>
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
            <div class="form_row">
                <label class="field_name align_right"><strong>Miscellaneous Expenses</strong></label>
                <div class="field">
                    <textarea class="isreq" rows="2" style="width: 100%;resize: none;" name="miscellaneous" id="miscellaneous" placeholder="Miscellaneous Expense"></textarea>
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
           url      :   "<?=site_url("employeemod_/saveSeminarApply")?>",
           type     :   "POST",
           data     :   data,
           contentType: false,
           processData: false,
           success  :   function(msg){
            alert(msg);
            //console.log(msg);
            //loadbushistory();
            location.reload();
            $("#close").click();
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

</script>