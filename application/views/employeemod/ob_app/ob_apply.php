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

$othertype  = isset($othertype) ? $othertype                : '';

$base_id    = isset($base_id)   ? $base_id                  : '';
$paid       = isset($paid)      ? $paid                     : '';
$ob_type    = isset($ob_type)   ? $ob_type                  : '';
$nodays     = isset($nodays)    ? $nodays                   : '';
$isHalfDay  = isset($isHalfDay) ? $isHalfDay                : '';
$dfrom      = isset($dfrom)     ? $dfrom                    : '';
$dto        = isset($dto)       ? $dto                      : '';
$reason     = isset($reason)    ? $reason                   : '';
$destination= isset($destination)    ? $destination         : '';


# newly added for ica-hyperion 21194
# by justin (with e)
$CI =& get_instance();
$CI->load->model('utils');
$empID = $this->session->userdata('username');
$isAdmin = $this->extras->findIfAdmin($empID);
$sel_emp = '';
$control = '';
$webSetup = '';

$weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$empID' AND STATUS = 'active'");
if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;

# kunin yung selected employee
if($isAdmin && $base_id){
    #echo "<pre>". "SELECT employeeid FROM ob_app_emplist WHERE base_id='{$base_id}'";
    $sel_emp = $this->db->query("SELECT employeeid FROM ob_app_emplist WHERE base_id='{$base_id}'")->row()->employeeid;
}
# end of newly added for ica-hyperion 21194
// WEBSETUP
if(!$webSetup) {
    if ($base_id) $control .= "";
    else  $control .= "checked";
}else {
    if ($base_id) $control .= "";
    else  $control .= "checked";
} 

?>

<style type="text/css">
    .form_row{
        padding-bottom: 10px;
    }
</style>
<form id="frmleave">

<input type="hidden" name="base_id" value="<?=$base_id?>">

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                    <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                    <p>D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title"><?=($base_id) ? " Edit " : " Add "?> OB / Excuse (Tardiness/Undertime)</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
                
                <div id="wrapUpload" style="padding-bottom: 10px;">
                    <?php if(!$base_id){ ?>
                        <span id="fileErrorMsg" style="margin-left: 15px;"><b>Upload Supporting Documents</b></span>
                        <input type="file" name="filess" id="filess" style="margin-left:30px;display: inline;" >
                        <span id="fileErrorMsg" style="color: red; margin-left: 1px;"></span>
                    <?php }else if($hasfiles){ ?>
                        <label id="processing" style="display: none;margin-left: 60%;"><img src='<?=base_url()?>images/loading.gif' />  Your request is processing, Please Wait..</label>
                        <label style="margin-left: 20%;color: blue;text-decoration: underline;" id="filename" file="" mime="">Click to view uploaded image.</label><br>
                    <?php } ?>
                </div><br>

                <!-- for ica-hyperion 21194 -->
                <!-- by justin (with e) -->
                <?if($isAdmin){
                    # kapag admin ang nag applay ng leave request.. lilitaw ito..
                ?>
                
                <div class="alert alert-success" id="msg_header" style="display: none;">
                    <strong>Success!</strong> Indicates a successful or positive action.
                </div>

                <!-- Approve by approver section -->
                <div class="form_row" style="margin-bottom: 0px;">
                    <label class="field_name align_right">Will be approve by approver?</label>
                    <div class="field no-search">
                        <div class="col-md-12">
                            <select class="form-control" name="allowApprover" id="allowApprover">
                                <option value="1">YES</option> <!-- kapag yes, dadaan sa sequence approver -->
                                <option value="0">NO</option> <!-- kapag no, deretso approved na -->
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <div class="col-md-12">
                        <div class="col-md-2" style="width: 18.666667%;">
                            <label class="field_name align_right" style="float: right;
    margin-right: 40%;">Employee</label>
                        </div>
                        <div class="col-md-8">
                            <select class="chosen" id="employee" name="employee" <?=($sel_emp) ? "disabled" : ""?>>
                                <option value=''>Select an employee</option>
                                <?
                                    $emplist = $CI->utils->getEmpListToCbo();

                                    $i = 0;
                                    # displayed employee list
                                    foreach ($emplist as $key => $value) {
                                        if($i > 0){
                                ?>
                                        <option value="<?=$key?>" <?=($sel_emp == $key)? "selected" : "" ?>><?=$key ." - ". $value?></option>
                                <?      } # end of if condition
                                        $i += 1;
                                    } # end of foreach 
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?}else{?>
                    <select id="employee" name="employee" style="display: none;"><option value="<?=$empID?>"></option></select>
                <?}?>
                <!-- end for ica-hyperion 21194 -->
                <div class="form_row" style="display: none;" >
                    <div class="field no-search">
                        <input type="checkbox" name="ltype" value="DIRECT"     checked   /> OB
                    </div>
                </div>
               <!--  <div class="form_row">
                    <label class="field_name align_right">With Pay?</label>
                    <div class="field">
                    <select class="form-control" name="withpay" id="withpay" >
						<option>Select</option>
						<?=$this->employeemod->withPay($paid);?>
					</select>
                    </div>
                </div> -->

                <!-- ///< For half day leave -->

                <div class="form_row" style="margin-left: 2.5%;">
                    <div class="field"  style="padding-bottom: 10px;">
                        <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "ob") ? "checked" : "" ?> name="ob_type" value="ob" style="margin-right: 10px;"  <?= $control ?> > <b style="margin-right: 10px;">Official Business</b>
                        <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "late") ? "checked" : "" ?> name="ob_type" value="late" style="margin-right: 10px;"> <b style="margin-right: 10px;">Excuse for Tardy</b>
                        <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "undertime") ? "checked" : "" ?> name="ob_type" value="undertime" style="margin-right: 10px;"> <b style="margin-right: 10px;">Excuse for Undertime</b>
                        <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "absent") ? "checked" : "" ?> name="ob_type" value="absent" style="margin-right: 10px;display: none;"> <b style="margin-right: 10px;display: none;">ABSENCES</b>
                    </div>
                </div>

                <div class="form_row" id="wrap_half_day">
                    <div class="field"  style="padding-bottom: 10px;padding-top: 5px;">
                        <div class="col-md-12">
                            &nbsp;<input type="checkbox" class="double-sized-cb" name="ishalfday" value="1" <?=$isHalfDay?'checked':''?> >&nbsp;&nbsp; <b>Check if OB is halfday</b>
                        </div>
                    </div>
                </div>

                <div class="form_row">
                    <label class="field_name align_right" id="datefrom_text">Date From</label>
                    <div class="field">
                        <div class="col-md-12"style="padding-left: 0px;">
                            <div class="col-md-5">
                                <div class='input-group date' id="datesetfrom" data-date="<?=$dfrom?$dfrom:$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 115%;">
                                    <input type='text' class="form-control" size="16" name="datesetfrom" id="dfrom" type="text" value="<?=$dfrom?>"/>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <span class="col-md-1" style="display: block; margin-left: 35px;" id="dateto_text">&nbsp;<b>To</b>&nbsp;</span>
                            <div class="col-md-5" id="dateto_div">
                                <div class='input-group date' id="datesetto" data-date="<?=$dto?$dto:$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 115%;">
                                    <input type='text' class="form-control" size="16" name="datesetto" id="dto" type="text" value="<?=$dto?>"/>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- modified by justin (with e) for ica-hyperion 21194 -->
                <!-- para sa edit ng request ni Admin -->
                <div class="form_row" id="wrap_sched_affected" <?=($isHalfDay == 1) ? "" : "style=\"display: none;\""?>>
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
                
                <!-- end newly addded by justin (with e) for #ica-21090 -->
                <div class="form_row" id="hideTITO" style="display: hidden;" <?=($isHalfDay == 1) ? "hidden" : ""?>>
                    <!-- modified by justin (with e) for ica-hyperion 21194 -->
                    <!-- para sa edit ng request ni Admin -->
                    <?
                        $timein = $timeout = "";
                        
                        # modified by justin (with e) for ica-hyperion 21194
                        if($base_id && $isHalfDay == 0){ 
                            $getTime = $this->db->query("SELECT * FROM ob_app WHERE id='$base_id'");
                            if($othertype == 'DIRECT'){
                                $timein = date('h:i A', strtotime($getTime->row()->timefrom));
                                $timeout = date('h:i A', strtotime($getTime->row()->timeto));
                            }
                        }
                        # end of modified for ica-hyperion 21194
                    ?>
                    <label class="field_name align_right" id="time_text">Time In </label>
                    <div class="field">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <div class="col-md-5" id="timefrom_div">
                                <div class='input-group time' style="width: 115%;">
                                    <input type='text' class="form-control" name="tfrom" id="tfrom" value="<?=$timein?>" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                            <span class="col-md-1" id="timeto_text" style="display: block; margin-left: 35px;">&nbsp;<b>Out</b>&nbsp;</span>
                            <div class="col-md-5" id="timeto_div">
                                <div class='input-group time' style="width: 115%;">
                                    <input type='text' class="form-control"  name="tto" id="tto" value="<?=$timeout?>"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group" id="hideBtn">
                            <a class="btn btn-primary" code='add' id='add' onclick="function"><i class="icon-save"></i></a>
                        </div>
                    </div>
                </div>
                <div class="form_row" id="destination_div">
                    <label class="field_name align_right">Destination</label>
                    <div class="field no-search">
                        <div class="col-md-12">
                            <input type="text" name="destination" id="destination" class="form-control" value="<?=isset($destination)?$destination:""?>" />
                            <span id="loadingdays" hidden=""></span>
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
                    <label class="field_name align_right">Purpose</label>
                    <div class="field">
                        <div class="col-md-12">
                        <textarea rows="4" class="form-control"  id="reason"  placeholder="Reason"><?=$reason?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" >
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-success">&nbsp;&nbsp;Submit&nbsp;&nbsp;</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
    var toks = hex_sha512(" ");
    checkOBApplicationType("<?= $ob_type ?>");
    // for ica-hyperion 21194
    // by justin (with e)
    // dito ko nilagay sa function na ito yung gawa ni ate angelica.. para sa checking ng sched affected..
    function checkSchedAffected(){
        
        displayStartEndTime();

        if($('input[name=ishalfday]').is(":checked")){

            var start = $("input[name='datesetfrom']").val();
            var end = $("input[name=datesetto]").val();
            // $("#dateto_text").hide();
            // $("#datesetto, #hideTo,#hideTITO").hide();
            $('#wrap_sched_affected').show();
            $('#tfrom,#tto').val('');
            if(start != '' && end != ''){
                var issame_sched = checkIfSameSched();
                if(issame_sched){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'You are not allowed to apply with different schedule.',
                        showConfirmButton: true,
                        timer: 2000
                    });
                }
                    $.ajax({
                       url      :   "<?=site_url("leave_/getEmployeeScheduleStartEnd")?>",
                       type     :   "POST",
                       data     :   {
                                        toks:toks,
                                        start:GibberishAES.enc(start, toks),
                                        end:GibberishAES.enc(end, toks)
                                        // added by justin (with e) for ica-hyperion 21194
                                        <?if($isAdmin){?>
                                        , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                                        <?}?>
                                        // end for ica-hyperion 21194
                                    },
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
                                isBoth   = key_arr[4] ? key_arr[4] : 0;
                                
                                // new added for ica-hyperion 21194
                                // by justin (with e)
                                // > para sa pag selected ng value after ma load yung data, kapag edit request..
                                val              = fromtime +"|"+ totime;
                                var isChecked    = "";
                                <?if($isHalfDay == 1){?>
                                    var selSched = "<?=$sched_affected?>";
                                    if(val == selSched) isChecked = 'checked="checked"';
                                <?}?>    
                                // end of new added for ica-hyperion 21194
                                var time12format = arr_sched[key].split(' - ');
                                var start = time12format[0];
                                var end = time12format[1];
                                // modified by justin (with e) for ica-hyperion 21194
                                // > displayed dito yung process kanina..
                                $('#sched_affected').append('<span class="col-md-4"><input type="checkbox" name="sched_affected" class="sched_affected" start="'+start+'" end="'+end+'" fromtime="'+fromtime+'" totime="'+totime+'" hrs="'+hrs+'" isAm="'+isAm+'" isBoth="'+isBoth+'" value="'+ val +'" '+ isChecked +'> '+arr_sched[key]+'</span>');
                            }


                        }else $('#sched_affected').html("No Schedule");

                       }
                    });
            }
        }
        else{
            $("#datesetto, #hideTo,#hideTITO").show();
            $('#wrap_sched_affected').hide();
            if($("input[name='ob_type']:checked").val() == "ob") $("#dateto_text").show();
            // var start = $("input[name='datesetfrom']").val();
            //     end   = $("input[name='datesetto']").val();
            // countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
        }
    }
    // > load yung data kapag nakatag ito na half day..
    <?if($isHalfDay == 1){?>
        checkSchedAffected();
    <?}?>    
    // end for ica-hyperion 21194

    ///< @author Angelica for halfday 
    // > modified by justin (with e) for ica-hyperion 21194
    // > hiniwalay ko lang yung function nito.. para magamit ko sa pag load ng data, same sa edit ng data..
    $('input[name=ishalfday],input[name=datesetfrom],input[name=datesetto]').on('change blur', checkSchedAffected);


    $(document).on('change',"input[name='sched_affected']", function() {
        $("input[name='sched_affected']").not(this).prop('checked', false);
        $('#tfrom').val($(this).attr('start'));
        $('#tto').val($(this).attr('end'));
    });

    $('input[name=datesetfrom],input[name=datesetto],select[name=employee]').change(function(){
        displayStartEndTime();
    });

    function displayStartEndTime(){
        if($('input[name=datesetfrom]').val() != '' && !$('input[name=ishalfday]').is(":checked")){
             $("#loadingroh").show().html("<img src='<?=base_url()?>images/loading.gif' />");
            $.ajax({
               url      :   "<?=site_url("ob_application_/displayStartEndtimePerDay")?>",
               type     :   "POST",
               dataType : 'json',
               data     :   {
                                toks:toks,
                                startdate   : GibberishAES.enc($("input[name=datesetfrom]").val(), toks),
                                enddate     : GibberishAES.enc($("input[name=datesetfrom]").val(), toks),
                                employeeid  : GibberishAES.enc($("select[name=employee]").val(), toks),
                                ob_type     : GibberishAES.enc($("input[name='ob_type']:checked").val(), toks)
               },
               success  :   function(msg){
                // console.log(msg);
                $("#roh").val(msg.scheddisp);
                if(msg.sched_start){
                    $('#tfrom').val(msg.sched_start);
                }else $('#tfrom').val('');
                if(msg.sched_end){
                    $('#tto').val(msg.sched_end);
                }else $('#tto').val('');
                // console.log(msg.max_timeout);
                $("#loadingroh").hide();
               }
            }); 
        }
    }


//if("<?=!$isdisabled?>"){
    <?if($othertype == "CORRECTION"){?>
        $("#hideBtn").show();
        $("#hideTo").hide();
        $(".hidemo").hide();
        $("#lblFrom").text("Date of Deficiency");
        $("#hideTable").show();
        $("#datesetto").hide();
    <?}else if($othertype == "late" || $othertype == "undertime"){?>
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

    $("input[name='ltype']").on('change', function() {

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
        $("input[name='ltype']").not(this).prop('checked', false);
        selDLtype = $(this).val();
        // end of new condition for #ica-21090 by justin (with e)
    });


    $("input[name='datesetfrom']").blur(function(){
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
            // $("#tbody-data").html("<tr><td colspan=\"3\" style=\"text-align: center;\"><img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..</td></tr>");

            // find timerecord here
            $.ajax({
                url     : "<?=site_url("employeemod_/showTimeRecord")?>",
                type    : "POST",
                data    : {toks:toks, cdate : GibberishAES.enc($(this).val(), toks) },
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
                    // alert("Invalid Time In/Out!.");
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Invalid Time In/Out!',
                        showConfirmButton: true,
                        timer: 2000
                    })
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
                // var res = confirm("Are you sure, you want to remove this time record?");
                // if(res){
                //     $("#tr-"+ $(this).attr('code')).remove();
                //     if($("#tableId").find("tbody").find("td").length == 0) $("#tbody-data").html("<tr><td colspan=\"3\" style=\"text-align: center;\">No Data Available..</td></tr>");
                // }
                const swalWithBootstrapButtons = Swal.mixin({
                 customClass: {
                   confirmButton: 'btn btn-success',
                   cancelButton: 'btn btn-danger'
                 },
                 buttonsStyling: false
               })

               swalWithBootstrapButtons.fire({
                 title: 'Are you sure?',
                 text: "Are you sure, you want to remove this time record?",
                 icon: 'warning',
                 showCancelButton: true,
                 confirmButtonText: 'Yes, proceed!',
                 cancelButtonText: 'No, cancel!',
                 reverseButtons: true
               }).then((result) => {
                 if (result.value) {
                   $("#tr-"+ $(this).attr('code')).remove();
                    if($("#tableId").find("tbody").find("td").length == 0) $("#tbody-data").html("<tr><td colspan=\"3\" style=\"text-align: center;\">No Data Available..</td></tr>");
                 } else if (
                   result.dismiss === Swal.DismissReason.cancel
                 ) {
                   swalWithBootstrapButtons.fire(
                     'Cancelled',
                     'Time record is safe.',
                     'error'
                   )
                 }
               })
            }
        });

    }
    // end of newly added for #ica-21090 by justin (with e)

    $("input[name='datesetto']").blur(function(){
       var  start = new Date($(this).val()),
            end   = new Date($("input[name='datesetfrom']").val()),
            diff  = new Date(start - end),
            days  = diff/1000/60/60/24;
            if(days >= 0)   days += 1;
            $("#ndays").val(days);
    });

    $("#filess").change(function(){
        var sizes = $(this).prop("files")[0].size/1024/1024;
        if(sizes > 2){
            $("#msg_header").removeClass("alert alert-danger");
            $("#msg_header").addClass("alert alert-danger");
            $("#msg_header").find("strong").text("Failed! ");
            $("#msg_header").find("span").text("File size exceeds 2 MB. Please try another file.");
            $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
            return;
        }

        var formdata = document.getElementById("filess");
        var uploadname = formdata.files.item(0).name;
        var file_url = URL.createObjectURL(event.target.files[0]);
    });

    $("#save").unbind('click').click(function(){
        // if((!$("input[name='tto']").val() && !$("input[name='tfrom']").val())){
        //     alert("Invalid date applied. Please fill-up date from and date to fields.");
        //     return;
        // }
        // }
        var base_id = "<?=$base_id?>";
        if(hasFiledOB() >= 1 && base_id==""){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Already applied ob on this date.",
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }

        if(hasFiledLeave() >= 1 && base_id==""){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Already applied leave on this date.",
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }

        // if($("input[name='tfrom']").val() == $("input[name='tto']").val() && !$('input[name="ishalfday"]').is(':checked')){
        if(!$("input[name='tto']").val() && !$('input[name="ishalfday"]').is(':checked')){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please select a valid time.",
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }

        if(!$('input[name="ob_type"]').is(':checked')){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please select a Official business type.",
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }
        
        if(hasActualLog() == 0 && $("input[name='ob_type']:checked").val() != "ob"){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "You need to have an actual logtime before filing a excuse.",
                showConfirmButton: true,
                timer: 5000
            })
            return;
        }

        if(hasUndertime() == 0 && $("input[name='ob_type']:checked").val() == "undertime"){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Not qualified for undertime application.",
                showConfirmButton: true,
                timer: 5000
            })
            return;
        }

        // return;
        if(sameSched()==0 && !$('input[name="ishalfday"]').is(':checked')){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Cannot applied this date due to different schedule.",
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }
        if($('input[name="ishalfday"]').is(':checked') && !$('input[name=sched_affected]').is(":checked")){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please select a schedule affected.",
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }
        // return;

        var tfrom = convertTimeToNumber($("input[name='tfrom']").val());
        var tto = convertTimeToNumber($("input[name='tto']").val());
        if(tfrom > tto && $("input[name='ob_type']:checked").val() == "ob" && !$('input[name="ishalfday"]').is(':checked')){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please fill-up a valid time.",
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }

        if ((!$("input[name=datesetfrom]").val() && !$("input[name=datesetto]")) && !$('input[name="ishalfday"]').is(':checked')) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Invalid date applied. Please fill-up date from and date to fields.',
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }
        if ((!$("input[name=datesetfrom]").val() || !$("input[name=datesetto]").val() ) && $("input[name='ob_type']:checked").val() == "ob" && !$('input[name="ishalfday"]').is(':checked')) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Invalid date applied. Please fill-up date from and date to fields.',
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }

        var imgVal = $('#filess').val(); 
        /*if(imgVal=='') { 
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please upload a file!',
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }*/
        if ((!$("input[name=datesetfrom]").val() && !$("input[name=datesetto]")) && !$('input[name="isHalfDay"]').is(':checked').val()) {
            // alert("Invalid date applied. Please fill-up date from and date to fields.");
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Invalid date applied. Please fill-up date from and date to fields.',
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }
        // if((new Date($("input[name=datesetfrom]").val()) >= new Date($("input[name=datesetto]").val())) && (($("input[name='tfrom']").val()) >= $("input[name='tto']").val()) && !$('input[name="isHalfDay"]').is(':checked') ){
        //     alert("Invalid date applied. Please fill-up a correct date from and date to fieldss.");
        //     return;
        // }
        // updated by justin (with e) for #ica-21090 
		if($("#withpay").val() != "Select")
		{
            var form_data  = new FormData();
            var file_data = "";
            if($("#filess").val()) file_data = $("#filess").prop("files")[0]
            form_data.append("files",file_data);
            form_data.append("toks",toks);
            var tfrom = convertTimeToNumber($("input[name='tfrom']").val());
            var tto = convertTimeToNumber($("input[name='tto']").val());
            form_data.append("reason",GibberishAES.enc($("#reason").val(), toks));
            form_data.append("formdata", GibberishAES.enc(decodeURIComponent($("#frmleave").serializeAndEncode()), toks));

            if($("input[name='ltype']").is(":checked") == false){
                // alert("Daily Leave Type is required!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Daily Leave Type is required!',
                    showConfirmButton: true,
                    timer: 2000
                })
                return false;
            }
            else if($("input[name='datesetfrom']").val() == "" && $("input[name='datesetto']").val() =="" && selDLtype != "CORRECTION"){
                // alert("Date From/To is required!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Date From/To is required!',
                    showConfirmButton: true,
                    timer: 2000
                })
                return false;
            }
            else if($("input[name='datesetfrom']").val() == "" && selDLtype == "CORRECTION"){
                // alert("Date of Deficiency is required!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Date of Deficiency is required!',
                    showConfirmButton: true,
                    timer: 2000
                })
                return false;
            }
            else if(($("input[name='tfrom']").val() == "" || $("input[name='tto']").val() =="") && selDLtype == "DIRECT" && !$('input[name="ishalfday"]').is(':checked')){
                // alert("Time In/Out is required!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Time In/Out is required!',
                    showConfirmButton: true,
                    timer: 2000
                })
                return false;
            }
            /*else if((tfrom > tto || tfrom == tto)  && selDLtype == "DIRECT"){
                // alert("Invalid Time In/Out!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Invalid Time In/Out!',
                    showConfirmButton: true,
                    timer: 2000
                })
                return false;
            }*/
            else if($("input[name='tfrom']").val() != "" && $("input[name='tto']").val() !="" && selDLtype == "CORRECTION"){
                // alert("Please saved the Time In/Out first!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please saved the Time In/Out first!',
                    showConfirmButton: true,
                    timer: 2000
                })
                return false;
            }
            else if($("#tableId").find("tbody").find("td").length == 1 && selDLtype == "CORRECTION"){
                // alert("Time Record is required!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Time Record is required!',
                    showConfirmButton: true,
                    timer: 2000
                })
                return false;
            }
             else if($("#destination").val() == ""){
                $("#destination").css("border-color","red").attr("placeholder", "This field is required!.").focus();
                return false;
            }
            else if($("#reason").val() == ""){
                $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
                return false;
            }

            else{
                //alert("save na");
                // console.log(form_data);
                // return;
            if ($("input[name='halfday']").is(":checked") == false) {
                if ($("input[name=datesetfrom]").val() != "" ) {
                                    // $("#saving").hide();
                                    // $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
                                    $.ajax({
                                       url      :   "<?=site_url("ob_application_/saveOBApp")?>",
                                       type     :   "POST",
                                       contentType: false,
                                       processData: false,
                                       dataType :   'json',
                                       data     :   form_data,
                                       success  :   function(msg){
                                            console.log(msg); 
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
                                                        // alert(res);
                                                        Swal.fire({
                                                            icon: 'success',
                                                            title: 'Success!',
                                                            text: res,
                                                            showConfirmButton: true,
                                                            timer: 2000
                                                        })
                                                        loadbushistory('',0,'apply');
                                                        $("#close").click();
                                                        // location.reload();
                                                    }

                                                });
                                            }else{
                                                        // alert(msg.msg);
                                                        Swal.fire({
                                                            icon: 'success',
                                                            title: 'Success!',
                                                            text: msg.msg,
                                                            showConfirmButton: true,
                                                            timer: 2000
                                                        })
                                                        $("#close").click();
                                                <?
                                                    # for ica-aims 21194
                                                    # by justin (with e)
                                                    # > closing tapos load ng history for admin
                                                    if($isAdmin){
                                                        # dito iload yung data..
                                                ?>
                                                    view_leave_status();
                                                <?  }else{
                                                    # > closing tapos load ng history for employee
                                                ?>
                                                        // msg.base_id;
                                                        loadbushistory('',0,'apply');
                                                         // location.reload();

                                                <?  }?>
                                            }
                                       }
                                    });
                }
                else
                {
                    alert("Date To is required!");
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Date To is required!',
                        showConfirmButton: true,
                        timer: 2000
                    })

                }
            }
            else 
            {  
				$("#saving").hide();
				// $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
				$.ajax({
				   url      :   "<?=site_url("ob_application_/saveOBApp")?>",
				   type     :   "POST",
				   dataType :   'json',
				   data     :   form_data,
				   success  :   function(msg){
					    console.log(msg); 
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
                                            toks: toks,
                                            aid         : GibberishAES.enc(msg.base_id, toks),
                                            cdate       : GibberishAES.enc($("input[name='datesetfrom']").val(), toks),
                                            timerecord  : GibberishAES.enc(timeRecord, toks)
                                          },
                                success : function(res){
                                    // alert(res);
                                     Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: res,
                                        showConfirmButton: true,
                                        timer: 2000
                                    })
                                   loadbushistory('',0,'apply');
                                    $("#close").trigger("click");
                                    location.reload();
                                }

                            });
                        }else{
                                    // alert(msg.msg);
                                if(res.err_code==0){
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: msg.msg,
                                        showConfirmButton: true,
                                        timer: 2000
                                    })
                                    $("#close").trigger("click");
                                }else{
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: msg.msg,
                                        showConfirmButton: true,
                                        timer: 2000
                                    })
                                }
                            <?
                                # for ica-aims 21194
                                # by justin (with e)
                                # > closing tapos load ng history for admin
                                if($isAdmin){
                                    # dito iload yung data..
                            ?>
                                view_leave_status();
                            <?  }else{
                                # > closing tapos load ng history for employee
                            ?>
                                    // msg.base_id;
                                    loadbushistory('',0,'apply');
                                     // location.reload();

                            <?  }?>
                        }
                   }
                });
			 }
            }
		}
		else
		{
			// alert("PLEASE SELECT WITH PAY!");
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "PLEASE SELECT WITH PAY!",
                showConfirmButton: true,
                timer: 2000
            })
		}
        // end of updated by justin (with e) for #ica-21090 
    });

    function hasFiledOB(){
        // console.log('riel');
        var hasfiled = timefrom = timeto = "";
        if($('input[name=ishalfday]').is(":checked")){
            if($('input[name=sched_affected]').is(":checked")){
                var sched_affected = $('input[name=sched_affected]:checked').val().split("|");
                timefrom = sched_affected[0];
                timeto = sched_affected[1];
            }
        }
        $.ajax({
            url: "<?= site_url('ob_application_/hasFiledOB') ?>",
            type: "POST",
            data:{
                toks:toks,
                employeeid : GibberishAES.enc($("select[name=employee]").val(), toks),
                datesetfrom : GibberishAES.enc($("input[name='datesetfrom']").val(), toks),
                datesetto : GibberishAES.enc($("input[name='datesetto']").val(), toks),
                ishalfday : GibberishAES.enc($('input[name=ishalfday]').is(":checked"), toks),
                timefrom : GibberishAES.enc(timefrom, toks),
                timeto : GibberishAES.enc(timeto, toks)
            },
            async: false,
            success:function(response){
                if(response >= 1) hasfiled = response;
            }
        });
        return hasfiled;
    }

    function hasActualLog(){
        var hasfiled = "";
        $.ajax({
            url: "<?= site_url('ob_application_/hasActualLog') ?>",
            type: "POST",
            data:{
                toks:toks,
                employeeid : GibberishAES.enc($("select[name=employee]").val(), toks),
                datesetfrom : GibberishAES.enc($("input[name='datesetfrom']").val(), toks),
                datesetto : GibberishAES.enc($("input[name='datesetto']").val(), toks)
            },
            async: false,
            success:function(response){
                if(response >= 1) hasfiled = response;
            }
        });
        return hasfiled;
    }

    function hasUndertime(){
        var earlydis = "";
        $.ajax({
            url: "<?= site_url('ob_application_/hasUndertime') ?>",
            type: "POST",
            data:{
                toks:toks,
                employeeid : GibberishAES.enc($("select[name=employee]").val(), toks),
                datesetfrom : GibberishAES.enc($("input[name='datesetfrom']").val(), toks),
                datesetto : GibberishAES.enc($("input[name='datesetto']").val(), toks)
            },
            async: false,
            success:function(response){
                earlydis = response;
            }
        });
        return earlydis;
    }

    function sameSched(){
        var issame = true;
        var d1 = $("input[name='datesetfrom']").val();
        var d2 = $("input[name='datesetto']").val();
        if(d1 == d2){
            return true;
        }else{
            $.ajax({
                url: "<?=site_url('ob_application_/employeeAppSched')?>",
                type: "POST",
                async:false,
                data:{
                    toks: toks,
                    employeeid : GibberishAES.enc($("select[name=employee]").val(), toks),
                    datesetfrom : GibberishAES.enc($("input[name='datesetfrom']").val(), toks),
                    datesetto : GibberishAES.enc($("input[name='datesetto']").val(), toks),
                },
                success:function(response){
                    issame = response;
                }
            });
        }
        return issame;
    }

    $('input[name=datesetfrom],input[name=datesetto],select[name=employee]').blur(function(){
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
            $(this).val("");
            return;
        }
        // displayStartEndTime();
    });

    $('input[name=tfrom],input[name=tto]').blur(function(){
        var tfrom = convert_to_24h($("input[name='tfrom']").val());
        var tto = convert_to_24h($("input[name='tto']").val());
        if(tfrom > tto){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Please fill-up a valid time.",
                showConfirmButton: true,
                timer: 2000
            })
            $(this).val("");
            return;
        }
    });

    $.fn.serializeAndEncode = function() {
    return $.map(this.serializeArray(), function(val) {
        return [val.name, encodeURIComponent(val.value)].join('=');
    }).join('&');
};

    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });
    $(".chosen").chosen();
    $('.time').datetimepicker({
        format: 'LT'
    });

    $('input[name="ob_type"]').on('change', function() {
        $('input[name="ob_type"]').not(this).prop('checked', false);  
        var ob_type = $(this).val();
/*        if(ob_type != "ob") $("#hideTITO").hide();
        else $("#hideTITO").show();*/
        checkOBApplicationType(ob_type);
    });

    $("#filename").click(function(){
        $("#filename").hide();
        $("#processing").show();
        $.ajax({
          url:"<?=site_url('ob_application_/getOBAttachments')?>",
          type: "POST",
          data:{base_id:"<?=$base_id?>"},
          dataType: "json",
          cache:false,
          async:false,
          success:function(response){

            $("#filename").attr("file", response.file);
            $("#filename").attr("mime", response.mime);
          }
        }).done(function(){
             if($("#filename").attr("file")){
                  var data = $("#filename").attr("file");
                  var mime = $("#filename").attr("mime");
                  var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
                  window.open(objectURL);
              }else{
                  var file_url = $("#filename").attr("content");
                  window.open(file_url);
              }
              $("#filename").show();
              $("#processing").hide(); 
        });
       /* setTimeout(function(){ 
          if($("#filename").attr("file")){
              var data = $("#filename").attr("file");
              var mime = $("#filename").attr("mime");
              var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
              window.open(objectURL);
          }else{
              var file_url = $("#filename").attr("content");
              window.open(file_url);
          }
          $("#filename").show();
          $("#processing").hide();
        }, 1000);*/
    });

    function b64toBlob(b64Data, contentType) {
        var byteCharacters = atob(b64Data)
        var byteArrays = []
        for (let offset = 0; offset < byteCharacters.length; offset += 512) {
            var slice = byteCharacters.slice(offset, offset + 512),
                byteNumbers = new Array(slice.length)
            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i)
            }
            var byteArray = new Uint8Array(byteNumbers)

            byteArrays.push(byteArray)
        }

        var blob = new Blob(byteArrays, { type: contentType })
        return blob
    }

    function convert_to_24h(time_str) {
        var time = time_str;
        if(time){
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
    }

    function checkOBApplicationType(ob_type){
        if(ob_type == "late"){
            $("#destination_div").hide();
            $("#destination").val(' ');
            $("#withpay").val("NO");
            $("#wrap_half_day").hide();
            $("#time_text").text("Time In").show();
            $("#timefrom_div").show();
            $("#dateto_text, #dateto_div").hide();
            $("#timeto_text, #timeto_div").hide();
            $("#datefrom_text").text("Date");
        }else if(ob_type == "undertime"){
            $("#destination_div").hide();
            $("#destination").val(' ');
            $("#withpay").val("NO");
            $("#wrap_half_day").hide();
            $("#time_text").text("Time").show();
            $("#timeto_div").show();
            $("#dateto_text, #dateto_div").hide();
            $("#timefrom_text, #timefrom_div, #timeto_text").hide();
            $("#datefrom_text").text("Date");
        }else{
            $("#destination_div").show();
            $("#wrap_half_day").show();
            $("#dateto_text").show();
            $("#dateto_div").show();
            $("#timefrom_text, #timefrom_div").show();
            $("#timeto_div, #timeto_text").show();
            $("#datefrom_text").text("Date From");
        }
    }

    function hasFiledLeave(){
        var employeeid = $("select[name='employee']").val();
        if("<?= $this->session->userdata('usertype') ?>" == "EMPLOYEE") employeeid = "<?= $this->session->userdata('username') ?>";
        var hasfiled = timefrom = timeto = "";
        if($('input[name=ishalfday]').is(":checked")){
            if($('input[name=sched_affected]').is(":checked")){
                var sched_affected = $('input[name=sched_affected]:checked').val().split("|");
                timefrom = sched_affected[0];
                timeto = sched_affected[1];
            }
        }
        $.ajax({
            url: "<?= site_url('leave_application_/hasFiledLeave') ?>",
            type: "POST",
            data:{
                toks:toks,
                employeeid : GibberishAES.enc(employeeid, toks),
                datesetfrom : GibberishAES.enc($("input[name='datesetfrom']").val(), toks),
                datesetto : GibberishAES.enc($("input[name='datesetto']").val(), toks),
                ishalfday : GibberishAES.enc($('input[name=ishalfday]').is(":checked"), toks),
                timefrom : GibberishAES.enc(timefrom, toks),
                timeto : GibberishAES.enc(timeto, toks)
            },
            async: false,
            success:function(response){
                if(response >= 1) hasfiled = response;
            }
        });
        return hasfiled;
    }

//}

function checkIfSameSched(){
     var issame = true;
     var start = $("input[name='datesetfrom']").val();
     var end = $("input[name='datesetto']").val();
     $.ajax({
           url      :   "<?=site_url("leave_/checkIfSameSchedLeave")?>",
           type     :   "POST",
           data     :   {
                            toks : toks,
                            start : GibberishAES.enc(start, toks),
                            end : GibberishAES.enc(end, toks)
                            
                            <?if($isAdmin){?>
                            , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                            <?}?>
                        },
            async    :   false,
            success  :   function(response){
                issame = response;
            }

       });

     return issame;
}

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
        var res='';
        // $(".modal-footer").append("<div id='loading'><img class='pull-right' src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..</div>");
        $(".grey,#button_save_modal").hide();
        $.ajax({
            url:"<?=site_url("employeemod_/loadmodelfunc")?>",
            type:"POST",
            data:{
                toks:toks,
                eid: GibberishAES.enc("<?=$code?>", toks),
                id: GibberishAES.enc("<?=$idnum?>", toks),
                aid: GibberishAES.enc("<?=$aid?>", toks),
                status: GibberishAES.enc($("#mh_status").val(), toks),
                model: GibberishAES.enc("leave_approve_head", toks),
                ltype: GibberishAES.enc("<?=$leavetype?>", toks),
                dept: GibberishAES.enc("<?=$dept?>", toks)
            },
            success: function(msg){
                $("#loading").remove();
                $(".grey,#button_save_modal").show();
                $("#modalclose").click();
                res = msg.substring(0, 8);
                if(res == "Success!"){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    })
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    })
                }
                // alert(msg);
                location.reload();  
            }
        });
    });


</script>
<?}?>
<script type="text/javascript" src="<?=base_url()?>/js/timeValidation.js"></script>