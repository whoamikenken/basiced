<?php

/**
 * @author Justin
 * @copyright 2016
 */

if($this->input->post("id")){
    $query = $this->employeemod->leave_modify_query($this->input->post("id"));
    $eid   = $query->row()->employeeid;
    $ltype = $query->row()->type;
    $paid  = $query->row()->paid;
    $dfrom = $query->row()->datefrom;
    $dto   = $query->row()->dateto;
    $ndays = $query->row()->nodays;
    $reason= $query->row()->reason;
}else   exit();

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
<input name="model" value="modifyLeave" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<input name="eid" value="<?=$eid?>" type="hidden" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="7%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                </tr>
                <tr>
                    <td><strong>Leave Application</strong></td>
                </tr>
            </table>
        </div>
        <div class="modal-body">
            <div class="content">
                <div class="form_row">
                    <label class="field_name align_right">Leave Type</label>
                    <div class="field no-search">
                        <input type="checkbox" name="ltype" value="VL" <?=($ltype == "VL" ? "checked" : "")?>/> VACATION &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="ltype" value="SL" <?=($ltype == "SL" ? "checked" : "")?>/> SICK &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="ltype" value="EL" <?=($ltype == "EL" ? "checked" : "")?>/> EMERGENCY &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="ltype" value="other" <?=($ltype == "other" ? "checked" : "")?>/> OTHER &nbsp;&nbsp;&nbsp;
                        <select name="othleave" id="othleave" style="width: 110px;"><?=$this->employeemod->othLeave();?></select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">With Pay?</label>
                    <div class="field no-search">
                        <select class="form-control" name="withpay" id="withpay"><?=$this->employeemod->withPay($paid);?></select>
                    </div>
                </div>
                <br />
                <div class="form_row">
                    <label class="field_name align_right">Leave From</label>
                    <div class="field">
                        <div class="input-group date" id="datesetfrom" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetfrom" type="text" value="<?=$dfrom?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <div class="input-group date" id="datesetto" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetto" type="text" value="<?=$dto?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Days</label>
                    <div class="field no-search">
                        <input type="text" name="ndays" id="ndays" value="<?=$ndays?>" readonly="" />
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" style="width: 100%;resize: none;" name="reason" id="reason" placeholder="Reason"><?=$reason?></textarea>
                    </div>
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
$("#save").click(function(){
    var form_data   =   $("#frmleave").serialize();
    
    if($("#ndays").val() < 0){
        alert("Invalid No. of days!.");
        return false;
    }
    
    if($("input[name='ltype']").is(":checked") == false){
        alert("Leave Type is required!.");
        return false;
    }else if($("#reason").val() == ""){
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
            loadleavemod();
            $(function(){
              loadleavehistory();  
              $("#close").click();
            });
           }
        });
    }
});
$("#datesetfrom,#datesetto").datepicker({
    autoclose: true,
    todayBtn : true
});
$(".chosen").chosen();
</script>