<?php

/**
 * @author Justin
 * @copyright 2016
 */

$desc = "";
$datetoday = "";
$timetoday = "";
if($this->input->post("id")){
    #$query = $this->payroll->displayOtrequest($this->input->post("id"));
    #$desc  = $query->row(0)->type;
}
?>
<style type="text/css">
    input[name=allemp]
    {
      /* Double-sized Checkboxes */
      -ms-transform: scale(1.5); /* IE */
      -moz-transform: scale(1.5); /* FF */
      -webkit-transform: scale(1.5); /* Safari and Chrome */
      -o-transform: scale(1.5); /* Opera */
      padding: 10px;
    }
    .modal{
        width: 100%;
        left: 0;
        right: 0;
        margin: auto;
    }
</style>

<form id="frmot">
<input name="model" value="applyOT" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <!-- <div class="modal-header">
            <h4 class="modal-title"><strong>Overtime</strong></h4>
        </div> -->
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
            <center><b><h3 tag="title" class="modal-title">Edit Overtime Application</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="form_row">
                <label class="field_name align_right">Will be approve by approver?</label>
                <div class="field no-search">
                    <div class="col-md-8" style="width: 38.5%;padding-left: 0px;">
                        <select class="form-control" name="allowApprover" id="allowApprover">
                            <option value="1">YES</option> <!-- kapag yes, dadaan sa sequence approver -->
                            <option value="0">NO</option> <!-- kapag no, deretso approved na -->
                        </select>
                    </div>
                </div>
            </div>
            <input type="hidden" name="departments">
            <div class="form_row" hidden>
                <label class="field_name align_right">Department</label>
                <div class="field">
                    <select class="chosen col-md-6" id="departments" name="departments">
                        <option value= "">--- Select all Department ---</option>
                        <?
                            foreach ($deptlist as $code => $desc) {?>
                                <option value="<?=$code?>" <?= $deptid==$code?"selected":"" ?> ><?=$desc?></option>
                            <?}
                        ?>
                    </select>
                </div>
            </div>
            <div class="form_row" hidden>
                <div class="field" style="padding-bottom: 10px;">
                    &nbsp;&nbsp;<input type="checkbox" name="allemp" value="allemp">&nbsp;&nbsp; <b>All Employees</b>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <input type="hidden" name="emplist" value="<?=$employeeid?>">
                    <input type="text" class="col-md-6 form-control" name="employeename" value="<?=$fullname?>" style="width: 79.5%;" readonly>
                    <!-- <select class="chosen col-md-6" id="emplist" name="emplist" multiple="">
                        <?
                            foreach ($emplist as $code => $desc) {?>
                                <option value="<?=$code?>"><?=$desc?></option>
                            <?}
                        ?>
                    </select>&nbsp;&nbsp;
                    <span id="loadingemp" hidden=""></span> -->
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Date From</label>
                    <div class="field" style="padding-bottom: 10px;padding-top: 5px;">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <div class="col-md-5" style="padding-right: 0px; padding-left: 0px;">
                                <div class='input-group date' id="datesetfrom" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd" style="width: 90%;">
                                    <input type='text' class="form-control" size="16" id="dfrom" name="datesetfrom" type="text" value="<?=$dfrom?>"/>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <span class="col-md-1" style="padding-left: 0px;padding-right: 0px;width: 5%; margin-left: -1.1%">&nbsp;<b>To</b>&nbsp;</span>
                            <div class="col-md-5" style="padding-left: 0px;">
                                <div class='input-group date' id="datesetto" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd" style="width: 90%;">
                                    <input type='text' class="form-control" size="16" id="dto" name="datesetto" type="text" value="<?=$dto?>"/>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="form_row">
                <label class="field_name align_right">Office Hour</label>
                <div class="field" style="padding-bottom: 10px;padding-top: 5px;">
                    <input type="text" class="align_center form-control" style="width:79.5%;" id="roh" name="roh" value="" readonly=""/>
                </div>
                <span id="loadingroh" hidden=""></span>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Overtime Start</label>
                <div class="field" style="padding-bottom: 10px;padding-top: 5px;">
                    <div class="col-md-12" style="padding-left: 0px;">
                        <div class="col-md-5" style="padding-left: 0px; padding-right: 0px; ">
                            <div class='input-group time'  style="width: 90%;" >
                                <input type='text' class="form-control" name="tfrom" id="tfrom" value="<?=$tstart?>">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                        <span class="col-md-1" style="padding-left: 0px;padding-right: 0px;width: 6%; margin-left: -2.1%">&nbsp;<b>End</b>&nbsp;</span>
                        <div class="col-md-5" style="padding-left: 0px;">
                            <div class='input-group time'  style="width: 90%;" >
                                <input type='text' class="form-control" name="tto" id="tto" value="<?=$tend?>"  >
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div><br>

            <div id="overtime_logs_disp" hidden=""></div>
            
            <div class="form_row">
                <label class="field_name align_right">Applied Total Hr./Min.</label>
                <div class="field" style="padding-bottom: 10px;">
                    <input type="text" class="align_center form-control" id="tot" name="tot" value="0" readonly="" style="width: 36.5%;" />
                </div>
            </div>
            <div class="form_row" id="total_approve_div" style="display:none;">
                <label class="field_name align_right">Approved Total Hr./Min.</label>
                <div class="field">
                    <input type="text" class="align_center form-control" id="tots" name="tots" value="<?= $total_approved ?>" style="width: 36.5%;" />
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Reason</label>
                <div class="field">
                    <textarea rows="4" class="form-control" name="reason" id="reason" style="width: 79.5%;" placeholder="Reason"><?=$reason?></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-success">Submit</button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="modify" value="1">
</form>
<script>
var toks = hex_sha512(" ");
computeTotal();
$(document).ready(function(){
   // $(".modal-header").html('<table width="100%"><tr><td rowspan="2" width="12%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td><td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td></tr><tr><td><strong>Request on Overtime</strong></td></tr></table>'); 
   getEmplist();
   displayStartEndtimePerDay(1);
});

$('#departments').on('change',function(){
   getEmplist();
});

$("input[name='datesetfrom'],input[name='datesetto']").on('change, blur',function(){
    var d1 = new Date($("input[name='datesetfrom']").val());
    var d2 = new Date($("input[name='datesetto']").val());
    if(d1 > d2){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Please fill-up a valid date.",
            showConfirmButton: true,
            timer: 2000
        })
        $(this).val('');
    }
})

function getEmplist(){
    $("#loadingemp").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    $.ajax({
       url      :   "<?=site_url("overtime_/getEmplist")?>",
       type     :   "POST",
       data     :   {toks:toks,deptid:GibberishAES.enc($('#departments').val(), toks)},
       success  :   function(ret){
        $("select[name='emplist']").html(ret).trigger("chosen:updated");
        $("#loadingemp").hide();
       }
    });
}

$('#emplist').on('change',function(){
    $("#loadingemp").hide();
});

$("#save").unbind('click').click(function(){
    var form_data   =   $("#frmot").serialize();

    if($("#allowApprover").val() == 0){
        var tothour = $("#tots").val();
        if(!tothour){

            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Invalid approved total hours',
                showConfirmButton: true,
                timer: 2000
            });
            return;
        }
    }
    if($("#reason").val() == ""){
        $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
        return false;
    }else{
        $("#saving").hide();
        // $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("overtime_/saveOTAppHRDirect")?>",
           type     :   "POST",
           data     :   {form_data:GibberishAES.enc(form_data, toks), toks:toks},
           success  :   function(msg){
            Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: msg,
                    showConfirmButton: true,
                    timer: 2000
                });          
                setTimeout(function(){ location.reload(); }, 2500);  
           }
        });
    }
});
// $("#reason").focusin(function(){computeTotal();});
// $("#tfrom,#tto").change(function(){computeTotal();});
$("#dfrom,#dto,#emplist").on('change',function(){
    displayStartEndtimePerDay(0);
});

function displayStartEndtimePerDay(firstload){
    if($('#dfrom').val() != ''){
         $("#loadingroh").show().html("<img src='<?=base_url()?>images/loading.gif' />");
        $.ajax({
           url      :   "<?=site_url("overtime_/displayStartEndtimePerDay")?>",
           type     :   "POST",
           dataType : 'json',
           data     :   {
                            startdate   : $("#dfrom").val(),
                            enddate     : $("#dto").val(),
                            employeeid  : "<?=$employeeid?>"
           },
           success  :   function(msg){
            // console.log(msg);
            $("#roh").val(msg.scheddisp);

            if(!firstload){
                if(msg.min_otstart){
                    min_otstart = convertTime24to12(msg.min_otstart);
                    $('#tfrom').val(min_otstart);
                }
                if(msg.max_timeout){
                    $('#tto').val(convertTime24to12(msg.max_timeout));
                }
                // console.log(msg.max_timeout);
                computeTotal();
            }
            $("#loadingroh").hide();
           }
        }); 
        if(!firstload) computeTotal();
    }
}


$("#dfrom,#dto,#emplist,#allowApprover,#tfrom,#tto").blur(function(){
    if($('#allowApprover').val() == 0){
        $("#total_approve_div").show();
        // getTotalApprovedHours();
        displayStartEndtimePerDay();
        var employeelist = $("#emplist").val();
        if(employeelist != null){
            if(employeelist.length > 1){
                $('#overtime_logs_disp').html('Unable to check logs.').css('text-align','center');
                $("#roh").val("");            
                return;
            }

            if($('#tto').val() != '' && $('#tfrom').val() != '' && $('#dfrom').val() != '' && $('#dto').val() != ''){
                    ///< get OT LOGS
                    $.ajax({
                       url      :   "<?=site_url("overtime_/getOTLogsDirect")?>",
                       type     :   "POST",
                       data     :   {
                                        dfrom   : $("#dfrom").val(),
                                        dto     : $("#dto").val(),
                                        tstart   : $("#tfrom").val(),
                                        tend    : $("#tto").val(),
                                        emplist : employeelist 
                       },
                       success  :   function(msg){
                            $('#overtime_logs_disp').html(msg).show();
                            $('#tot').val($('input[name=total_computed]').val());
                            $('#tots').val($('input[name=total_computed]').val());
                       }
                    }); 
            }

        }else{
            $("#roh").val("");            
        }


    }else{
        $("#total_approve_div").hide();
        computeTotal();
        $('#overtime_logs_disp').hide();
    }
});

function computeTotal(){
    if($('#tto').val() != '' && $('#tfrom').val() != '' && $('#dfrom').val() != '' && $('#dto').val() != ''){
        var tfrom = convert_to_24h($("#tfrom").val());
        var tto   = convert_to_24h($("#tto").val()); 
    
        var difference = Math.abs(toSeconds(tfrom) - toSeconds(tto));
        var result = [
            Math.floor(difference / 3600), // an hour has 3600 seconds
            Math.floor((difference % 3600) / 60), // a minute has 60 seconds
            difference % 60
        ];
        result = result.map(function(v) {
            return v < 10 ? '0' + v : v;
        }).join(':');
        ttime = result.split(":");
        
        var dates = getDates($("#dfrom").val(), $("#dto").val());
        var ttime = "";
        var dtfrom = "";
        var dtto = "";
        var diff = 0;
        var hour=0;
        
        for(var i = 0, len = dates.length; i < len; i++)
        {
            
            dtfrom   = moment(formattedDate(dates[i])+" "+$("#tfrom").val()).format("YYYY-MM-DD HH:mm:ss");
            dtto   = moment(formattedDate(dates[i])+" "+$("#tto").val()).format("YYYY-MM-DD HH:mm:ss");
            diff = moment(dtto,'YYYY-MM-DD HH:mm:ss').diff(moment(dtfrom,'YYYY-MM-DD HH:mm:ss'));
            hour += diff;
        }
        // alert(tfrom+"a"+tto+"b"+dtfrom+"c"+dtto+"d"+diff);
        ///< added for 30-min condition ica-hyperion21186
        totalHours = hour/* - (hour%1800000)*/;
        if(totalHours < 3600000) totalHours = 0;
        $("#tot").val(msToHMS(totalHours)); 
        $("#tots").val(msToHMS(totalHours)); 
    }
}

function getDates(startDate, stopDate) {
    var dateArray = [];
    var currentDate = moment(startDate);
    var stopDate = moment(stopDate);
    while (currentDate <= stopDate) {
        dateArray.push( moment(currentDate).format('YYYY-MM-DD') )
        currentDate = moment(currentDate).add(1, 'days');
    }
    return dateArray;
}

function msToHMS( ms ) {
    // 1- Convert to seconds:
    var seconds = ms / 1000;
    // 2- Extract hours:
    var hours = leadzero(parseInt( seconds / 3600 ),2); // 3,600 seconds in 1 hour
    seconds = seconds % 3600; // seconds remaining after extracting hours
    // 3- Extract minutes:
    var minutes = leadzero(parseInt( seconds / 60 ),2); // 60 seconds in 1 minute
    // alert( hours+":"+minutes);
    return hours+":"+minutes;
}

function formattedDate(date) {
    var d = new Date(date || Date.now()),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();
    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [month, day, year].join('/');
}

function convert_to_24h(time_str) {
    var time = time_str;
    var hours = Number(time.match(/^(\d+)/)[1]);
    var minutes = Number(time.match(/:(\d+)/)[1]);
    var AMPM = time.match(/\s(.*)$/)[1];
    if(AMPM == "PM" && hours<12) hours = hours+12;
    if(AMPM == "AM" && hours==12) hours = hours-12;
    var sHours = hours.toString();
    var sMinutes = minutes.toString();
    if(hours<10) sHours = "0" + sHours;
    if(minutes<10) sMinutes = "0" + sMinutes;
    return sHours + ":" + sMinutes + ":00";
}
function convertTime24to12(time24){
    var tmpArr = time24.split(':'), time12;
    if(+tmpArr[0] == 12) {
        time12 = tmpArr[0] + ':' + tmpArr[1] + ' PM';
    } else {
        if(+tmpArr[0] == 00) {
            time12 = '12:' + tmpArr[1] + ' AM';
        } else {
            if(+tmpArr[0] > 12) {
                time12 = (+tmpArr[0]-12) + ':' + tmpArr[1] + ' PM';
            } else {
                time12 = (+tmpArr[0]) + ':' + tmpArr[1] + ' AM';
            }
        }
    }
    return time12;
}
function toSeconds(time_str) {
    var parts = time_str.split(':');
    return parts[0] * 3600 + parts[1] * 60 + parts[2];
}
function leadzero(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}
$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".time").datetimepicker({
    format: "LT"
});
$(".chosen").chosen();
</script>