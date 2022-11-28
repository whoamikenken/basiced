<?php

/**
 * @author Justin
 * @copyright 2016
 */

$query = $this->employeemod->ot_modify_query($this->input->post("idkey"));
if($query->num_rows() > 0){
    $dfrom  = $query->row()->dfrom;
    $dto    = $query->row()->dto;
    $tstart = $query->row()->tstart;
    $tend   = $query->row()->tend;
    $total  = $query->row()->total;
    $reason = $query->row()->reason;
}
?>

<form id="frmot">
<input name="model" value="modifyOT" hidden=""/>
<input name="id" value="<?=$this->input->post("idkey")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"><strong>Overtime</strong></h4>
        </div>
        <div class="modal-body">
            <div class="form_row">
                <label class="field_name align_right">Date From</label>
                <div class="field">
                    <table width="100%">
                        <tr>
                            <td width="40%">
                                <div class="input-group date" id="datesetfrom" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="datesetfrom" id="dfrom" type="text" value="<?=$dfrom?>" readonly>
                                    <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                                </div>
                            </td>
                            <td class="align_center" width="10%">
                                To
                            </td>
                            <td class="align_center" width="45%">
                                <div class="input-group date" id="datesetto" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="datesetto" id="dto" type="text" value="<?=$dto?>" readonly>
                                    <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Office Hour</label>
                <div class="field">
                    <input type="text" class="align_center" id="roh" name="roh" value="" readonly="" style="width: 100%;" />
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Overtime Start</label>
                <div class="field">
                    <table width="100%">
                        <tr>
                            <td width="40%">
                                <div class="input-group bootstrap-timepicker">
                                    <input class="input-small align_center" type="text" name="tfrom" id="tfrom" value="<?=$tstart?>" style="width: 125px;" />
                                    <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                </div>
                            </td>
                            <td class="align_center" width="10%">
                                End
                            </td>
                            <td class="align_center" width="45%">
                                <div class="input-group bootstrap-timepicker">
                                    <input class="input-small align_center" type="text" name="tto" id="tto" value="<?=$tend?>" style="width: 125px;" />
                                    <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Total Hr./Min.</label>
                <div class="field">
                    <input type="text" class="align_center" id="tot" name="tot" value="<?=$total?>" readonly="" />
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Reason</label>
                <div class="field">
                    <textarea rows="4" style="width: 100%;resize: none;" name="reason" id="reason" placeholder="Reason"><?=$reason?></textarea>
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
$(document).ready(function(){
   $(".modal-header").html('<table width="100%"><tr><td rowspan="2" width="12%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td><td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td></tr><tr><td><strong>Request on Overtime</strong></td></tr></table>'); 
});
$("#frmot #save").click(function(){
    var form_data   =   $("#frmot").serialize();
    if($("#reason").val() == ""){
        $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
        return false;
    }else{
        $("#saving").hide();
        $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");

        $.ajax({
           url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
           type     :   "POST",
           data     :   form_data,
           success  :   function(msg){
            alert(msg);            
            location.reload();
           }
        });
    }
});
$("#reason").focusin(function(){computeTotal();});
$("#tfrom,#tto").blur(function(){computeTotal();});
$("#dfrom,#dto").change(function(){computeTotal();});
function computeTotal(){
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
    var ttime = result.split(":");
    var dtfrom   = moment(formattedDate($("#dfrom").val())+" "+$("#tfrom").val()).format("YYYY-MM-DD HH:mm:ss");
    var dtto   = moment(formattedDate($("#dto").val())+" "+$("#tto").val()).format("YYYY-MM-DD HH:mm:ss");
    $("#tot").val(leadzero(moment(dtto,'YYYY-MM-DD HH:mm:ss').diff(moment(dtfrom,'YYYY-MM-DD HH:mm:ss'), 'hours'),2)+":00"); 
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
function toSeconds(time_str) {
    var parts = time_str.split(':');
    return parts[0] * 3600 + parts[1] * 60 + parts[2];
}
function leadzero(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}
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