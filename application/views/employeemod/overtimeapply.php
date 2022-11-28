<?php

/**
 * @author Justin
 * @copyright 2016
 *
 * modified @Angelica
 */
$desc = "";
$sched = (isset($sched)) ? $sched : '';
$datetoday = "";
$timetoday = "";
if($this->input->post("id")){
    #$query = $this->payroll->displayOtrequest($this->input->post("id"));
    #$desc  = $query->row(0)->type;
}
$isHead = true;
$empid = $this->session->userdata('username');
$isHead = $this->utils->findIfHead($empid);
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
</style>
<input type="hidden" id="empids" value="<?=$empid?>">
<form id="frmot">
<input name="model" value="applyOT" hidden=""/>
<input name="otid" value="<?=isset($otid)?$otid:''?>" hidden="" />
<input name="base_id" value="<?=isset($base_id)?$base_id:''?>" hidden="" />
<div class="modal-dialog" style="width: 40%;">
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
                <center><b><h3 tag="title" class="modal-title"><?=isset($base_id)? 'Edit ':'Add '?>Overtime Application</h3></b></center>
            </div>
        <div class="modal-body">
           <div class="form_row" hidden>
                <label class="field_name align_right">Department</label>
                <div class="field">
                    <select class="chosen" id="departments" name="departments">
                        <?
                            foreach ($officelist as $code => $desc) {?>
                                <option value="<?=$code?>" <?= $office==$code?"selected":"" ?> ><?=$desc?></option>
                            <?}
                        ?>
                    </select>
                </div>
            </div>
            <div class="form_row" <?= ($this->session->userdata("usertype") == "ADMIN") ? "" : "hidden" ?> >
                <div class="field" style="padding-bottom: 10px;">
                    &nbsp;&nbsp;<input type="checkbox" name="allemp" value="allemp">&nbsp;&nbsp; <b>All Employees</b>
                </div>
            </div> 
            <div class="form_row" <?= ($this->session->userdata("usertype") == "ADMIN") ? "" : "hidden" ?> >
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <select class="chosen col-md-6" id="emplist" name="emplist" multiple="">
                        <?
                            # for ica-hyperion 21129 
                            # justin (with e)
                            
                            # end for ica-hyperion 21129

                            $isHead = false; # ica-hyperion 21552
                            foreach ($emplist as $code => $desc) {
                                if($empid == $code && $isHead === false){?>
                                    <option value="<?=$code?>" selected><?=$desc?></option>
                                <?}else{
                                ?>
                                    <option value="<?=$code?>"><?=$desc?></option>
                                <?}
                            }
                        ?>
                    </select>&nbsp;&nbsp;
                    <span id="loadingemp" hidden=""></span>
                </div>
            </div>
            <div class="form_row" style="padding-bottom: 10px;">
                <label class="field_name align_right">Date</label>
                <div class="field" style="width: 45%;">
                    <div class='input-group date' id='datesetfrom' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                        <input type='text' class="form-control" size="12" name="datesetfrom" id="dfrom" type="text" value="<?=isset($dfrom) ? $dfrom : $datetoday?>"/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div><br>
                    <!-- &nbsp;&nbsp;To&nbsp;&nbsp; -->
                    <div class="input-group date" id="datesetto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd" style='display: none;'>
                        <input class="align_center" size="16" name="datesetto" id="dto" type="text" value="<?=isset($dfrom) ? $dfrom : $datetoday?>">
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                </div>
            </div>
            <div class="form_row" style="padding-bottom: 10px;">
                <label class="field_name align_right">Office Hour</label>
                <div class="field"  style="width: 76%;">
                    <input type="text" class="form-control align_center" id="roh" name="roh" value="<?=$sched?>"/>
                </div>
                <span id="loadingroh" hidden=""></span>
            </div><br>
            <div class="form_row">
                <label class="field_name align_right">Overtime Start</label>
                <div class="field">
                    <div class="col-md-5" style="padding-left: 0px; ">
                        <div class='input-group time' style="width: 110%;">
                            <input type='text' class="form-control" name="tfrom" id="tfrom" value="<?=isset($tstart)?$tstart:''?>"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-1 align_right" >
                        <label>End</label>
                    </div>
                    <div class="col-md-6" style="margin-left: -2%;">
                        <div class='input-group time'>
                            <input type='text' class="form-control" name="tto" id="tto" value="<?=isset($tend)?$tend:''?>"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div><br>
            <div class="form_row" style="padding-bottom: 10px;">
                <label class="field_name align_right">Total Hr./Min.</label>
                <div class="field"  style="width: 45%;">
                    <input type="text" class="form-control align_center" id="tot" name="tot" value="<?=isset($total)?$total:0?>" />
                </div>
            </div><br>
            <div class="form_row">
                <label class="field_name align_right">Special Task to be done</label>
                <div class="field">
                    <textarea rows="4" class="form-control" name="reason" id="reason" placeholder="Reason"><?=isset($reason)?$reason:''?></textarea>
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
</form>
<script>
var toks = hex_sha512(" ");
    var totalHours = 0;

$(document).ready(function(){
   // $(".modal-header").html('<table width="100%"><tr><td rowspan="2" width="12%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td><td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td></tr><tr><td><strong>Request on Overtime</strong></td></tr></table>'); 
   <?if(!$isHead){?>
    $('#emplist').attr('disabled','disabled');
    $('input[name="allemp"]').attr('disabled','disabled');
   <?}?>

   displayStartEndtimePerDay(1);
});

$('#departments').on('change',function(){
    $("#loadingemp").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    $.ajax({
       url      :   "<?=site_url("overtime_/getEmplist")?>",
       type     :   "POST",
       data     :   {toks:toks,deptid:GibberishAES.enc($('#departments').val(), toks)},
       success  :   function(ret){
        console.log(ret);
        $("select[name='emplist']").html(ret).trigger("liszt:updated");
        $("#loadingemp").hide();
       }
    });
});

$('#emplist').on('change',function(){
    $("#loadingemp").hide();
});

$("#frmot #save").click(function(){
    var form_data   =   $("#frmot").serialize();

    if($("input[name=allemp]").is(':checked')){

    }else{
       if($("#emplist").val() == null){
        form_data   += "&emplist="+$("#empids").val();
       }else  form_data   += "&emplist="+$("#emplist").val();
    }

    var base_id = $("input[name='base_id']").val();
    if(hasFiledOT() >= 1 && base_id==""){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "You already filed OT application on this date",
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }
    // return;
    if($("#tot").val() <= 0){
        // alert('Invalid number of hours.');
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Invalid number of hours.',
            showConfirmButton: true,
            timer: 1000
        })
        return false;
    }


    if($("#reason").val() == ""){
        $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
        return false;
    }else{
        $("#saving").hide();
        // $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("overtime_/saveOTApp")?>",
           type     :   "POST",
           data     :   {form_data:GibberishAES.enc(form_data, toks),toks:toks},
           success  :   function(msg){
            // console.log(msg);
            // loadbushistory();
            if(msg == "Failed to save application."){
                 Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: msg,
                    showConfirmButton: true,
                    timer: 1000
                });
                 $("#saving").show();
                 return;
            }else{
                 Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: msg,
                    showConfirmButton: true,
                    timer: 1000
                })
            }
            $("#close").click();
            loadOvertimehistory("","","",0,'load');
             // loadOvertimehistory("","","",0,'load');
            // $(".inner_navigation .main li .active a").click();
           }
        });
    }
});
$("#reason").focusin(function(){computeTotal();});
$("#tfrom,#tto").blur(function(){computeTotal();});
$("#dfrom,#dto,#emplist").on('blur',function(){
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
                            toks:toks,
                            startdate   : GibberishAES.enc($("#dfrom").val(), toks),
                            enddate     : GibberishAES.enc($("#dfrom").val(), toks),
                            employeeid  : GibberishAES.enc($("#empids").val(), toks)
           },
           success  :   function(msg){
            // console.log(msg);return;
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

function computeTotal(){
    if($('#tto').val() != '' && $('#tfrom').val() != '' && $('#dfrom').val() != ''){
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
		
		var dates = getDates($("#dfrom").val(), $("#dfrom").val());
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

function hasFiledOT(){
    var hasfiled = timefrom = timeto = "";
    $.ajax({
        url: "<?= site_url('overtime_/hasFiledOT') ?>",
        type: "POST",
        data:{
            toks: toks,
            employeeid : GibberishAES.enc($("select[name=emplist]").val(),toks),
            datesetfrom : GibberishAES.enc($("input[name='datesetfrom']").val(),toks),
            datesetto : GibberishAES.enc($("input[name='datesetfrom']").val(),toks),
            timefrom : GibberishAES.enc($("input[name='tfrom']").val(),toks),
            timeto : GibberishAES.enc($("input[name='tto']").val(),toks)
        },
        async: false,
        success:function(response){
            if(response >= 1) hasfiled = response;
        }
    });
    return hasfiled;
}

 $('.time').datetimepicker({
    format: 'LT'
});

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$('.chosen').chosen();
</script>