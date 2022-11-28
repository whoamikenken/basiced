<?php

/**
 * @author Justin
 * @copyright 2016
 */

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\offbusinessapply.php
 */

$datetoday = "";
$timetoday = "";

$isdisabled = isset($leaveid) ? 'readonly': '';

$othertype  = isset($othertype) ? $othertype    : '';

$base_id    = isset($base_id)   ? $base_id      : '';
$paid       = isset($paid)      ? $paid         : '';
$nodays     = isset($nodays)    ? $nodays       : '';
$isHalfDay  = isset($isHalfDay) ? $isHalfDay    : '';
$dfrom      = isset($dfrom)     ? $dfrom        : '';
$dto        = isset($dto)       ? $dto          : '';
$reason     = isset($reason)    ? $reason       : '';

?>
<style>
.modal{
    width: 700px;
    left: 0;
    right: 0;
    margin: auto;
}
.th-style{
    background-color: #2e5266;
    color: #ffffff;
    text-align: center;
}
</style>
<form id="frmleave">

<input type="hidden" name="base_id" value="<?=$base_id?>">

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="10%"><img src="<?=base_url()?>/images/school_logo.png" /></td>
                    <td><h4 class="modal-title"><strong>&nbsp;&nbsp;<?=$this->extras->school_name()?></strong></h4></td>
                </tr>
                <tr>
                    <td><strong>&nbsp;&nbsp;&nbsp;OB/Excuse Slip</strong></td>
                </tr>
            </table>
        </div>
        <div class="modal-body">
            <div class="content">
             
                <div class="form_row">
                    <div class="field no-search">
                    	
                        <input type="checkbox" id='absent' name="dltype" value="ABSENT"     <?=($othertype == "ABSENT" ? " checked" : "")?>    /> ABSENT &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="dltype" value="DIRECT"     <?=($othertype == "DIRECT" ? " checked" : "")?>    /> OB &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="dltype" value="CORRECTION" <?=($othertype == "CORRECTION" ? " checked" : "")?>/> CORRECTION OF TIME IN/OUT &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </div>
                </div>
                <br>
                <div class="form_row">
                    <label class="field_name align_right">With Pay?</label>
                    <div class="field">
                    <select class="form-control" name="withpay" id="withpay" >
						<option>Select</option>
						<?=$this->employeemod->withPay($paid);?>
					</select>
                    </div>
                </div>

                <!-- ///< For half day leave -->

                <div class="form_row" id="wrap_half_day">
                    <div class="field"  style="padding-bottom: 10px;">
                     <input type="checkbox" class="double-sized-cb" name="ishalfday" value="1" <?=$isHalfDay?'checked':''?> >&nbsp;&nbsp; <b>Check this if your leave to be applied is halfday</b>
                    </div>
                </div>
                <br/>

                <div class="form_row">
                    <label class="field_name align_right" id="lblFrom">Leave From</label>
                    <div class="field">
                        <div class="input-group date" id="datesetfrom" data-date="<?=$dfrom?$dfrom:$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetfrom" type="text" value="<?=$dfrom?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <div class="input-group" id="hideTo">
                            <label class="align_center">To</label>
                        </div>
                        <div class="input-group date" id="datesetto" data-date="<?=$dto?$dto:$datetoday?>" data-date-format="yyyy-mm-dd" <?=$isHalfDay?'style="display:none;"':''?> >
                            <input class="align_center" size="16" name="datesetto" type="text" value="<?=$dto?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                </div>

                <div class="form_row" id="wrap_sched_affected" style="display: none;">
                    <label class="field_name align_right">Check Schedules Affected</label>
                    <div class="field" id="sched_affected">
                        No Schedule     
                    </div>
                </div>
                        
                <!-- newly addded by justin (with e) for #ica-21090 -->
                <div class="form_row" id="hideTable">
                    <label class="field_name align_right">My Time Record</label>
                    <div class="field">
                        <table class="table table-hover table-bordered datatable" id="tableId">
                            <thead>
                                <th class="th-style" style="text-align: center;">TIME IN</th>
                                <th class="th-style" style="text-align: center;">TIME OUT</th>
                                <th class="th-style" style="text-align: center;">EDIT</th>
                            </thead>
                            <tbody id="tbody-data">
                                <!-- displayed data here -->
                                <tr>
                                <td colspan="3" style="text-align: center;">No Data Available..</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                
                <!-- end newly addded by justin (with e) for #ica-21090 -->
                <div class="form_row" id="hideTITO" style="display: hidden;">
                    <?
                        $timein = $timeout = "";
                        # get time if official business
                        # new added by justin (with e)
                        if(isset($leaveid)){
                            $getTime = $this->db->query("SELECT * FROM ob_app WHERE id='$leaveid'");
                            if($othertype == 'DIRECT'){
                                $timein = date('h:i A', strtotime($getTime->row()->timefrom));
                                $timeout = date('h:i A', strtotime($getTime->row()->timeto));
                            }
                        }
                    ?>
                    <label class="field_name align_right">Time In </label>
                    <div class="field">
                        <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="tfrom" id="tfrom" value="<?=$timein?>" style="width: 125px;" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                        To
                        <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="tto" id="tto" value="<?=$timeout?>" style="width: 125px;" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                        <div class="input-group" id="hideBtn">
                            <a class="btn btn-primary" code='add' id='add' onclick="function"><i class="icon-save"></i></a>
                        </div>
                    </div>
                </div>
                <div class="form_row" id="hideDays">
                    <label class="field_name align_right">Days</label>
                    <div class="field no-search">
                        <input class="col-md-3" type="text" name="ndays" id="ndays" value="1" readonly="" />
                    </div>
                </div>
        

                <div class="form_row">
                    <label class="field_name align_right">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" style="width: 90%;resize: none;" name="reason" id="reason" placeholder="Reason" ></textarea>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer" >
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-danger">&nbsp;&nbsp;Save&nbsp;&nbsp;</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>

    ///< @author Angelica for halfday 
    $('input[name=ishalfday],input[name=datesetfrom],input[name=datesetto]').on('change', function(){

        if($('input[name=ishalfday]').is(":checked")){

            var start = $("input[name='datesetfrom']").val();
            $("input[name=datesetto]").val(start);
            $("#datesetto, #hideTo").hide();
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
            $("#datesetto, #hideTo").show();
            $('#wrap_sched_affected').hide();
            // var start = $("input[name='datesetfrom']").val();
            //     end   = $("input[name='datesetto']").val();
            // countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
        }
    });
    $(document).on('change',"input[name='sched_affected[]']", function() {
        $("input[name='sched_affected[]']").not(this).prop('checked', false);
    });


//if("<?=!$isdisabled?>"){
    <?if($othertype == "CORRECTION"){?>
        $("#hideBtn").show();
        $("#hideTo").hide();
        $(".hidemo").hide();
        $("#lblFrom").text("Date of Deficiency");
        $("#hideTable").show();
        $("#datesetto").hide();
    <?}else if($othertype == "ABSENT"){?>
        $('#hideTable').hide();
        $("#lblFrom").text("Date of Absent");
        $("#withpay").attr('disabled',true);
        $('#withpay').val("NO").prop('disabled', true).trigger("liszt:updated");
        $("#hideTITO").hide();
    <?}else{?>
        $("#lblFrom").text("OB Date");
        $('#hideTable').hide();
    <?}?>
    var selDLtype = "<?=$othertype?>";
    $("#hideTable").hide();
    $("#hideDays").hide();
    $("#hideBtn").hide();

    $("input[name='dltype']").on('change', function() {

        $("#hideTo").show();
        $(".hidemo").show();
        $("#hideTITO").show();
        $("#hideTable").hide();
        $("#hideBtn").hide();
        $("#datesetto").show();
        //$("#withpay").attr('disabled',false);

        // new condition added for #ica-21090 by justin (with e)
        if($(this).val() == "ABSENT"){
            $("#lblFrom").text("Date of Absent");
            $("#hideTITO").hide();

            $("#wrap_half_day").show();
            if($('input[name=ishalfday]').is(":checked")){
            	$("#wrap_sched_affected").show();
            }

            $("#withpay option:eq(2)").prop("selected", true);
            $('#withpay').val("NO").prop('disabled', true).trigger("liszt:updated");
        }
        else if($(this).val() == "DIRECT"){
            $("#lblFrom").text("OB Date");
            $('#withpay').val("Select").prop('disabled', false).trigger("liszt:updated");

            $("#wrap_half_day").show();
            if($('input[name=ishalfday]').is(":checked")){
            	$("#wrap_sched_affected").show();
            }
        }
        else{
            $("#datesetto,#wrap_sched_affected, #wrap_half_day").hide();
            $("#hideBtn").show();
            $("#hideTo").hide();
            $(".hidemo").hide();
            $("#lblFrom").text("Date of Deficiency");
            $("#hideTable").show();
            $('#withpay').val("Select").prop('disabled', false).trigger("liszt:updated");;
        }
        $("input[name='dltype']").not(this).prop('checked', false);
        selDLtype = $(this).val();
        // end of new condition for #ica-21090 by justin (with e)
    });


    $("input[name='datesetfrom']").change(function(){
       var  start = new Date($(this).val()),
            end   = new Date($("input[name='datesetto']").val()),
            diff  = new Date(end - start),
            days  = diff/1000/60/60/24;
            if(days >= 0)   days += 1;
            $("#ndays").val(days);
        // new condition added for #ica-21090 by justin (with e)
        if(selDLtype == "CORRECTION"){
            // remove all row here
            removeAllrow();

            // displayed loading
            $("#tbody-data").html("<tr><td colspan=\"3\" style=\"text-align: center;\"><img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..</td></tr>");

            // find timerecord here
            $.ajax({
                url     : "<?=site_url("employeemod_/showTimeRecord")?>",
                type    : "POST",
                data    : { cdate : $(this).val() },
                success : function(msg){
                    removeAllrow();
                    $("#tbody-data").html(msg);
                    reloadBtnEvent();
                    $("#add").attr('code','add');
                }
            });
        }
        // end of new condition for #ica-21090 by justin (with e)
    });
    // newly added for #ica-21090 by justin (with e)
    reloadBtnEvent(); // load button event
    function removeAllrow(){
        $( "#tableId tbody tr" ).each( function(){
            this.parentNode.removeChild( this ); 
        });
    }
    function clearTime(){
        $("input[name='tfrom']").val('');
        $("input[name='tto']").val('');
    }
    function reloadBtnEvent(){
        $('#add, #remove, #edit').unbind('click').click(function(){

            // move to edit
            if($(this).attr('id') == 'edit'){
                $("input[name='tfrom']").val($("#ti-"+ $(this).attr('code')).html());
                $("input[name='tto']").val($("#to-"+ $(this).attr('code')).html());
                $("#add").attr('code',$(this).attr('code'));
                return;
            }

            // add new
            if($(this).attr('id') == 'add'){
                // check time
                var tfrom = convertTimeToNumber($("input[name='tfrom']").val());
                var tto = convertTimeToNumber($("input[name='tto']").val());
                if(tfrom > tto || tfrom == tto || $("input[name='tfrom']").val() == "" || $("input[name='tto']").val() == ""){
                    alert("Invalid Time In/Out!.");
                    return;
                }

                if($(this).attr('code') == 'add'){
                    // add new timerecord
                    var newCode = $("#tableId").find("tbody").find("tr").length;
                    if($("#tableId").find("tbody").find("td").length == 1){
                        newCode = "add-0";
                        removeAllrow();
                    }else{
                        newCode = "add-"+ newCode;
                    }
                    var tbody_data = "";
                
                    tbody_data += "<tr id=\"tr-"+ newCode +"\">";
                    tbody_data += "<td style=\"text-align: center;\" id=\"ti-"+ newCode +"\">"+ $("input[name='tfrom']").val() +"</td>"; // time in 
                    tbody_data += "<td style=\"text-align: center;\" id=\"to-"+ newCode +"\">"+ $("input[name='tto']").val() +"</td>"; // time out
                    tbody_data += "<td style=\"text-align: center;\" code='change'>"; 
                    tbody_data += "<a class=\"btn blue\" code=\""+ newCode +"\" id=\"edit\"><i class=\"glyphicon glyphicon-edit\"></i></a>"; // edit
                    tbody_data += "<a class=\"btn blue\" code=\""+ newCode +"\" id=\"remove\"><i class=\"glyphicon glyphicon-remove-sign\"></i></a>"; // remove
                    tbody_data += "</td>"; 
                    tbody_data += "</tr>";
                    
                    $("#tbody-data").html($("#tbody-data").html() +""+ tbody_data);
                    $("#add").attr('code','add');
                    clearTime();
                    reloadBtnEvent();
                    return;
                }else{
                    // save edited timerecord
                    //alert("hellor");
                    $("#ti-"+ $(this).attr('code')).html($("input[name='tfrom']").val());
                    $("#to-"+ $(this).attr('code')).html($("input[name='tto']").val());
                    $("#add").attr('code','add');
                    clearTime();
                    reloadBtnEvent();
                    return;
                }
            }

            // remove row
            if($(this).attr('id') == 'remove'){
                var res = confirm("Are you sure, you want to remove this time record?");
                if(res){
                    $("#tr-"+ $(this).attr('code')).remove();
                    if($("#tableId").find("tbody").find("td").length == 0) $("#tbody-data").html("<tr><td colspan=\"3\" style=\"text-align: center;\">No Data Available..</td></tr>");
                }
            }
        });

    }
    // end of newly added for #ica-21090 by justin (with e)

    $("input[name='datesetto']").change(function(){
       var  start = new Date($(this).val()),
            end   = new Date($("input[name='datesetfrom']").val()),
            diff  = new Date(start - end),
            days  = diff/1000/60/60/24;
            if(days >= 0)   days += 1;
            $("#ndays").val(days);
    });
    $("#save").unbind('click').click(function(){
        // updated by justin (with e) for #ica-21090 
		if($("#withpay").val() != "Select")
		{
            var tfrom = convertTimeToNumber($("input[name='tfrom']").val());
            var tto = convertTimeToNumber($("input[name='tto']").val());
            var form_data   =   $("#frmleave").serialize();
            console.log(form_data);
            if($("input[name='dltype']").is(":checked") == false){
                alert("Daily Leave Type is required!.");
                return false;
            }
            else if($("input[name='datesetfrom']").val() == "" && $("input[name='datesetto']").val() =="" && selDLtype != "CORRECTION"){
                alert("Date From/To is required!.");
                return false;
            }
            else if($("input[name='datesetfrom']").val() == "" && selDLtype == "CORRECTION"){
                alert("Date of Deficiency is required!.");
                return false;
            }
            else if(($("input[name='tfrom']").val() == "" || $("input[name='tto']").val() =="") && selDLtype == "DIRECT"){
                alert("Time In/Out is required!.");
                return false;
            }
            else if((tfrom > tto || tfrom == tto)  && selDLtype == "DIRECT"){
                alert("Invalid Time In/Out!.");
                return false;
            }
            else if($("input[name='tfrom']").val() != "" && $("input[name='tto']").val() !="" && selDLtype == "CORRECTION"){
                alert("Please saved the Time In/Out first!.");
                return false;
            }
            else if($("#tableId").find("tbody").find("td").length == 1 && selDLtype == "CORRECTION"){
                alert("Time Record is required!.");
                return false;
            }
            else if($("#reason").val() == ""){
                $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
                return false;
            }
            else{
                //alert("save na");
                console.log(form_data);
				$("#saving").hide();
				$("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
				$.ajax({
				   url      :   "<?=site_url("ob_application_/saveOBApp")?>",
				   type     :   "POST",
				   dataType :   'json',
				   data     :   form_data,
				   success  :   function(msg){
					
                        if(selDLtype == "CORRECTION" && msg.err_code == 1){
                            // get the timerecord on table
                            var timeRecord = "";
                            $("#tableId").find("tbody tr").each(function(){
                                if($(this).find("td").length > 1){
                                    //if($(this).find('td:eq(2)').attr('code') == "change"){
                                        timeRecord += (timeRecord?"|":"");
                                        var tID = $(this).attr('id');
                                        tID = tID.split("tr-");
                                        timeRecord += tID[1]; // timesheet id or new add id
                                        timeRecord += "~u~";
                                        timeRecord += $(this).find('td:eq(0)').text(); // time in
                                        timeRecord += "~u~";
                                        timeRecord += $(this).find('td:eq(1)').text(); // time out
                                        timeRecord += "~u~";
                                        timeRecord += $(this).find('td:eq(2)').attr('code');
                                    //}
                                }
                            });

                            // save here the timerecord
                            $.ajax({
                                url     : "<?=site_url("employeemod_/saveTimeRecord")?>",
                                type    : "POST",
                                data    : {
                                            aid         : msg.base_id,
                                            cdate       : $("input[name='datesetfrom']").val(),
                                            timerecord  : timeRecord
                                          },
                                success : function(res){
                                    alert(res);
                                    loadbushistory('',0);
                                    $("#close").click();
                                }

                            });
                        }else{
                            alert(msg.msg);
                            // msg.base_id;
                            loadbushistory('',0);
                            $("#close").click();
                        }
                   }
                });
			}
		}
		else
		{
			alert("PLEASE SELECT WITH PAY!");
		}
        // end of updated by justin (with e) for #ica-21090 
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
//}

$(".chosen").chosen();
</script>

<?if(isset($code)){?>
<script>
    $(document).ready(function(){ 
        if("<?=in_array($status,array("APPROVED","DISAPPROVED"))?>"){
            $("#button_save_modal").hide();
        }else{
            $("#button_save_modal").show();
        }
    });
            
    $("#button_save_modal").unbind("click").click(function(){  
        $(".modal-footer").append("<div id='loading'><img class='pull-right' src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..</div>");
        $(".grey,#button_save_modal").hide();
        $.ajax({
            url:"<?=site_url("employeemod_/loadmodelfunc")?>",
            type:"POST",
            data:{
                eid: "<?=$code?>",
                id: "<?=$idnum?>",
                aid: "<?=$aid?>",
                status: $("#mh_status").val(),
                model: "leave_approve_head",
                ltype: "<?=$leavetype?>",
                dept: "<?=$dept?>"
            },
            success: function(msg){
                $("#loading").remove();
                $(".grey,#button_save_modal").show();
                $("#modalclose").click();
                alert(msg);
                location.reload();  
            }
        });
    });
</script>
<?}?>
<script type="text/javascript" src="<?=base_url()?>/js/timeValidation.js"></script>