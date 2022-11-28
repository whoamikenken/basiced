<?php

/**
 * @author Justin
 * @copyright 2016
 *
 * @modified Angelica
 *
 */

$desc = "";
$datetoday = date("d-m-Y");
$maxCredit = 10;
$employeeid = "";

$teachingtype = $this->employee->loadfieldemployee('teachingtype',$this->session->userdata('username'));

?>
<style>
.modal{
    width: 70%;
    left: 0;
    right: 0;
    margin: auto;
}
</style>
<form id="frmleave">
<!-- <input name="model" value="applyLeave" hidden=""/> -->
<input name="model" value="applyLeaveWithSequence" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="7%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>Leave Application</strong></td>
                    <td>
                        <span id="wrapUpload" hidden="" class="pull-right" style="color: #6A1B9A;"><b>Upload Medical Certificate<b>
                            <input type="file" name="filess" id="file" class="input-large pull-right" >
                            <span id="fileErrorMsg" class="pull-right" hidden="" style="color: red; margin-left: 1px;"></span>
                        </span>
                    </td>
                </tr>

            </table>
        </div>
        <div class="modal-body">
            <div class="content">
                <div class="form_row">
                    <label class="field_name align_right">Leave Type</label>
                    <div class="field no-search">
                        <input type="checkbox" name="ltype" value="VL"/> VACATION &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="ltype" value="SL"/> SICK &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="ltype" value="EL"/> EMERGENCY &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="ltype" value="other"/> OTHER &nbsp;&nbsp;&nbsp;
                        <select name="othleave" id="othleave" style="width: 110px;"><?=$this->employeemod->othLeave();?></select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">With Pay?</label>
                    <div class="field no-search">
                        <select class="form-control" name="withpay" id="withpay"><?=$this->employeemod->withPay();?></select>
                    </div>
                </div>
				
				<!-- ///< For half day leave -->

                <div class="form_row">
                    <div class="field"  style="padding-bottom: 10px;">
                     <input type="checkbox" class="double-sized-cb" name="ishalfday" value="1">&nbsp;&nbsp; <b>Check this if your leave to be applied is halfday</b>
                    </div>
                </div>
				
                <br />
                <div class="form_row">
                    <label class="field_name align_right">Leave From</label>
                    <div class="field">
                        <div class="input-group date" id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetfrom" type="text" value="" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <div class="input-group date" id="datesetto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetto" type="text" value="" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Days</label>
                    <div class="field no-search">
                        <input type="text" name="ndays" id="ndays" value="0" readonly />
                        <span id="loadingdays" hidden=""></span>
                    </div>
                </div>
				
				<div class="form_row" id="wrap_sched_affected" style="display: none;">
                    <label class="field_name align_right">Check Schedules Affected</label>
                    <div class="field" id="sched_affected">
                        No Schedule     
                    </div>
                </div>
				
				
                <div class="form_row">
                    <label class="field_name align_right">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" style="width: 100%;resize: none;" name="reason" id="reason" placeholder="Reason"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span id="errormsg" hidden="" style="color: red; margin-right: 20px;"></span>
            <span id="loading" hidden=""></span>
            <span id="saving">
                <button type="button" id="save" class="btn btn-danger">Save</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </span>
        </div>
    </div>
</div>
</form>
<script>

$(document).ready(function(){
    var dateToday = new Date();
    $("#datesetfrom,#datesetto").datepicker({
        autoclose: true,
        todayBtn : true
    });
});


///< @author Angelica for halfday leave
$('input[name=ishalfday],input[name=datesetfrom],input[name=datesetto]').on('change', function(){

    if($('input[name=ishalfday]').is(":checked")){

        var start = $("input[name='datesetfrom']").val();
        $("input[name=datesetto]").val(start);
        $("#datesetto").hide();
        $('#wrap_sched_affected').show();
        if(start != ''){

                $.ajax({
                   url      :   "<?=site_url("leave_/getEmployeeScheduleStartEnd")?>",
                   type     :   "POST",
                   data     :   {start:start},
                   success  :   function(ret){
                    var arr_sched = JSON.parse(ret);
                    // console.log(arr_sched);

                    var hrs = 0;
                    var fromtime    = '',
                        totime      = '',
                        isAm        = '',
                        isBoth      = '';
                    $("#ndays").val(hrs);
                    ///< append sched affected
                    if($(arr_sched).size() > 0){
                        $('#sched_affected').html("");

                        for (var key in arr_sched) {

                            var key_arr = key.split('|');
                            fromtime = key_arr[0] ? key_arr[0] : '';
                            totime   = key_arr[1] ? key_arr[1] : '';
                            hrs      = key_arr[2] ? key_arr[2] : 0;
                            isAm     = key_arr[3] ? key_arr[3] : 0;
                            isBoth      = key_arr[4] ? key_arr[4] : 0;

                            $('#sched_affected').append('<span class="col-md-3"><input type="checkbox" name="sched_affected[]" class="sched_affected" fromtime="'+fromtime+'" totime="'+totime+'" hrs="'+hrs+'" isAm="'+isAm+'" isBoth="'+isBoth+'" value="'+fromtime+"|"+totime+'"> '+arr_sched[key]+'</span>');
                        }
                    }else $('#sched_affected').html("No Schedule");

                   }
                });
        }
    }else{
        $("#datesetto").show();
        $('#wrap_sched_affected').hide();
        var start = $("input[name='datesetfrom']").val();
            end   = $("input[name='datesetto']").val();
        countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
    }
});

$(document).off('change').on('change','.sched_affected',function(){
    $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    var hrs = 0,
        days = 0,
        hasAm = 0,
        hasPm = 0,
        forWholeDay = 0;

    $('.sched_affected').each(function(){
        if($(this).is(':checked')){
            hrs += (+$(this).attr('hrs'));

            if($(this).attr('isAm') != 0) hasAm = 1;
            if($(this).attr('isAm') == 0 && $(this).attr('isBoth') == 0) hasPm = 1;
            if($(this).attr('isBoth') != 0 || (hasAm != 0 && hasPm != 0)) forWholeDay = 1;

        }
    });

    ///< CONDITIONS
    ///< for teaching
    ///< halfday - hindi abot ng 12
    ///< pero pag abot ng 12 (5hrs - whole) (<5 half)

    ///< for nonteaching
    ///< if 1 sched is checked (half day), if 2 (whole day)

    if("<?=$teachingtype?>"=="teaching"){
        if(forWholeDay != 0) days = hrs >= 5 ? 1 : 0.5;
        else            days = hrs > 0 ? 0.5 : 0;
    }else{
        if(forWholeDay != 0) days = 1;
        else            days = hrs > 0 ? 0.5 : 0;
    }

    $("#ndays").val(days);
    $("#loadingdays").hide();
});
///< end of script for halfday leave

$("#othleave").css("pointer-events","none");
$("input[name='ltype']").on('change', function() {
    $("input[name='ltype']").not(this).prop('checked', false);
    if($(this).val() == "other")
        $("#othleave").css("pointer-events","");
    else{
        $("#othleave").css("pointer-events","none").val("");
    }
});
$("#othleave").change(function(){
   if($(this).val() == "DA"){
        loaddailyleave();
    }    
});
$("input[name='ltype']").click(function(){
    $(this).prop("checked",true);

    var dateToday = new Date();
    ///< for VL disable 3 days after today
    if($(this).val() == "VL"){
        dateToday.setDate(dateToday.getDate() + 2);
        $("#datesetfrom,#datesetto").datepicker('remove');
        $("#datesetfrom,#datesetto").datepicker({
            autoclose: true,
            useCurrent: false,
            startDate: dateToday,
            todayBtn : true,
        });
    }else{
        $("#datesetfrom,#datesetto").datepicker('remove');
        $("#datesetfrom,#datesetto").datepicker({
            autoclose: true,
            todayBtn : true
        });
    }

    if($(this).val() == "SL")  $('#wrapUpload').removeAttr('hidden');
    else                        $('#wrapUpload').attr('hidden',true);
    

});
$("input[name='datesetfrom']").change(function(){
   // var  start = new Date($(this).val()),
   //      end   = new Date($("input[name='datesetto']").val()),
        // diff  = new Date(end - start),
        // days  = diff/1000/60/60/24;
        // if(days >= 0)   days += 1;
        // $("#ndays").val(days);
        var start = $(this).val(),
            end   = $("input[name='datesetto']").val();
        countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
});
$("input[name='datesetto']").change(function(){
  /* var  start = new Date($(this).val()),
        end   = new Date($("input[name='datesetfrom']").val()),
        diff  = new Date(start - end),
        days  = diff/1000/60/60/24;
        if(days >= 0)   days += 1;
        var spltf = ($("input[name='datesetfrom']").val()).split("-");
        var spltt = ($(this).val()).split("-");
        var start   = new Date(spltf[0], spltf[1], spltf[2]);
        var end     = new Date(spltt[0], spltt[1], spltt[2]);
        var less = 0;
        for (var i = start; i <= end; ){
            if (i.getDay() == 0)    less++;
            i.setTime(i.getTime() + 1000*60*60*24);
        }
        $("#ndays").val((days-less));*/
        var end = $(this).val(),
            start   = $("input[name='datesetfrom']").val();
        countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
});

function countDaysWithinSchedule(start, end){
    $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    $.ajax({
       url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
       type     :   "POST",
       data     :   {start:start, end:end},
       success  :   function(days){
        $("#ndays").val(days);
        $("#loadingdays").hide();
       }
    });
}



$("#save").click(function(){
    $("#errormsg").html('');
    var ndays = $("#ndays").val();
    
    if(ndays <= 0){
        $("#errormsg").show().html("Invalid No. of days!.");
        return false;
    }else{
        ///< added for requesting days condition -- atleast 3day leave must apply before 3 days --atleast 10day leave must apply before 2weeks 
            ///< --limit max num of leaves to available credits
            ///< only for VL
        if($("input[name='ltype']:checked").val() == "VL"){
            if(!checkVLDays(ndays)) return false;
        }else if($("input[name='ltype']:checked").val() == "SL"){
            ///< different saving for sick leave because of medical cert uploading, new format of data will be sent 
            ///< this will not continue to normal saving
            saveSickLeave();
            return false;
        }
    }

    var form_data   =   $("#frmleave").serialize();
   
     // return;

    if($("input[name='ltype']").is(":checked") == false){
        $("#errormsg").show().html("Leave Type is required!.");
        return false;
    }else if($("#reason").val() == ""){
        $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
        return false;
    }else{
        $("#saving").hide();
        $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("leave_application_/saveLeaveApp")?>",
           type     :   "POST",
           data     :   form_data,
           dataType : 'json',
           success  :   function(msg){
                alert(msg.msg);
                if(msg.err_code){
                    $("#close").click();
                    $(".inner_navigation .main li .active a").click();
                }
                else{
                    $("#saving").show();
                    $("#loading").hide().html("");
                }
           }
        });
    }
});

function saveSickLeave(){
    var iscontinue  = true,
        data        = new FormData(),
        form_arr    = $("#frmleave").serializeArray(),
        form_data   = JSON.stringify(form_arr);

    data.append('form_data',form_data);

    if ( $('#file').get(0).files.length !== 0 ) {
        var file = $('#file')[0].files[0];
        data.append('filess',file);

        ///< validation for file type and size
        $.ajax({
           url      :   "<?=site_url("leave_/validateUploadFile")?>",
           type     :   "POST",
           data     :   data,
           contentType: false,
           processData: false,
           success  :   function(msg){
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
        // $("#saving").hide();
        // $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("leave_application_/saveSickLeaveApp")?>",
           type     :   "POST",
           data     :   data,
           dataType : 'json',
           contentType: false,
           processData: false,  
           success  :   function(msg){
    			alert(msg.msg);
                if(msg.err_code){
                    $("#close").click();
                    $(".inner_navigation .main li .active a").click();
                }else{
                    $("#saving").show();
                    $("#loading").hide().html("");
                }
                
           }
        });

    }
}

function checkVLDays(ndays){
    var isValidVL = true;
    var  now = new Date("<?=$datetoday?>"),
         datefrom   = new Date($("input[name='datesetfrom']").val()),
         diff  = new Date(datefrom - now),
         days  = diff/1000/60/60/24;
    
    if(ndays <= 3){        
        if(days < 3){
            $("#errormsg").show().html("Leave of atleast 3 days should be filed 3 days before the day of leave.");
           isValidVL = false;
        }
    }else if(ndays > 3 && ndays <= 10){
        if(days < 14){
            $("#errormsg").show().html("Leave of atleast 4 days should be filed 2 weeks before the day of leave.");
           isValidVL = false;
        }
    }else if(ndays > 10){
        $("#errormsg").show().html("Atleast 10-day leave is allowed.");
        isValidVL = false;
    }
    return isValidVL;
}

$(".chosen").chosen();
</script>