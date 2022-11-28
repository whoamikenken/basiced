<?php
/**
 * @author Justin
 * @copyright 2015
 */

if($job == "add"){
    $lname = "";
    $fname = "";
    $mname = "";
    $leavetype = "";
    $dateapplied = "";
    $no_days = "";
    $fromdate = "";
    $todate = "";
    $status = "";
    $remarks = "";
    $isreadonly = "";
}

$server_date = $this->extensions->getServerTime();
$server_date = explode(" ", $server_date);

$cdatefrom = $server_date[0];

?>
<form id="form_leave">
<input hidden="" id="leavebal" value="" />
<div class="form_row">
    <label class="field_name align_right">Employee ID</label>
    <div class="field">
        <select class="search-choice chosen" style="width: 350px;" id="mh_empid" name="mh_empid"  multiple="">
            <?
                foreach ($emplist as $code => $desc) {?>
                    <option value="<?=$code?>"><?=$desc?></option>
                <?}
            ?>
        </select>
    </div>
</div>
<!-- <div class="form_row">
    <label class="field_name align_right">Leave Type</label>
    <div class="field no-search">
        <input type="checkbox" name="mh_leavetype" value="VL" checked=""/> VACATION 
        <input type="checkbox" name="mh_leavetype" value="SL"/> SICK 
        <input type="checkbox" name="mh_leavetype" value="EL"/> EMERGENCY
        <input type="checkbox" name="mh_leavetype" value="other"/> OTHER
        <select name="othleave" id="othleave" style="width: 150px;"><?=$this->employeemod->othLeave();?></select>
    </div>
</div> -->
<div class="form_row">
    <label class="field_name align_right">With Pay?</label>
    <div class="field no-search">
        <select class="form-control" name="withpay" id="withpay"><?=$this->employeemod->withPay();?></select>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">From Date</label>
        <div class="field">
            <div class="input-group date" id="mh_fromdate" data-date="<?=$cdatefrom?>" data-date-format="yyyy-mm-dd">
                <input class="align_center" size="16" id="mh_fromdate" name="mh_fromdate" type="text" value="<?=$cdatefrom?>" data-date-format="yyyy-mm-dd" readonly="" />
                <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
            </div>
        </div>
</div>
<div class="form_row">
    <label class="field_name align_right">To Date</label>
        <div class="field">
            <div class="input-group date" id="mh_todate" data-date="<?=$cdatefrom?>" data-date-format="yyyy-mm-dd">
                <input class="align_center" size="16" id="mh_todate" name="mh_todate" type="text" value="<?=$cdatefrom?>" data-date-format="yyyy-mm-dd" readonly="" />
                <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
            </div>
        </div>
</div>
<div class="form_row">
    <label class="field_name align_right">No. of Days</label>
    <div class="field">
        <input class="col-md-4 required" id="mh_noofdays" name="mh_noofdays" <?=$isreadonly?> type="text" value="1"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Reason</label>
    <div class="field no-search">
        <textarea rows="4" style="width: 100%;resize: none;" name="mh_reason" id="mh_reason" placeholder="Reason"></textarea>
    </div>
</div>
</form>
<script>

$(document).ready(function(){
    $("#othleave").hide();
    $("input[name='mh_leavetype']").on('change', function() {
        $("input[name='mh_leavetype']").not(this).prop('checked', false);
        if($(this).val() == "other")
            $("#othleave").show();
        else{
            $("#othleave").hide().val("");
        }
    });
    $("input[name='mh_leavetype']").click(function(){
        $(this).prop("checked",true);
    });
});

$("#mh_fromdate,#mh_todate").datepicker({
    autoclose: true
});
    
$("#button_save_modal").unbind("click").click(function(){  
    $("#modal-view").find("#button_save_modal").attr('disabled',true);
    $("#modal-view").find(".modal-footer").html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
    $.ajax({
        url:"<?=site_url("leave_/saveLeaveHRDirect")?>",
        type:"POST",
        data:{
            eid: $("#mh_empid").val(),
            ltype: 'other',
            othleave: 'DA',
            ndays: $("input[name='mh_noofdays']").val(),
            datesetfrom: $("input[name='mh_fromdate']").val(),
            datesetto: $("input[name='mh_todate']").val(),
            reason: $("#mh_reason").val(),
            withpay: $("#withpay").val()
        },
        success: function(msg){
            alert(msg);
            $("#modalclose").click();
            $(".inner_navigation .main li .active a").click();
            // console.log(msg);
        }
        });
});

$("input[name='mh_fromdate']").change(function(){
   var  start = new Date($(this).val()),
        end   = new Date($("input[name='mh_todate']").val()),
        diff  = new Date(end - start),
        days  = diff/1000/60/60/24;
        if(days >= 0)   days += 1;
        $("#mh_noofdays").val(days);
});
$("input[name='mh_todate']").change(function(){
   var  start = new Date($(this).val()),
        end   = new Date($("input[name='mh_fromdate']").val()),
        diff  = new Date(start - end),
        days  = diff/1000/60/60/24;
        if(days >= 0)   days += 1;
        $("#mh_noofdays").val(days);
});

$('.chosen').chosen();
</script>