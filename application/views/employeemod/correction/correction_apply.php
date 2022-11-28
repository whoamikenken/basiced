<?php

/**
 * @author Justin
 * @copyright 2016
 */

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\offbusinessapply.php
 */
// echo 'CORRECTION';
$datetoday = "";
$timetoday = "";

$isdisabled = isset($leaveid) ? 'readonly': '';

$othertype  = isset($othertype) ? $othertype    : 'CORRECTION';

$base_id    = isset($base_id)   ? $base_id      : '';
$idkey    = isset($idkey)   ? $idkey      : '';
$paid       = isset($paid)      ? $paid         : '';
$nodays     = isset($nodays)    ? $nodays       : '';
$isHalfDay  = isset($isHalfDay) ? $isHalfDay    : '';
$dfrom      = isset($dfrom)     ? $dfrom        : '';
$dto        = isset($dto)       ? $dto          : '';
$reason     = isset($reason)    ? $reason       : '';

# newly added for ica-hyperion 21194 & 21196
# by justin (with e)
$CI =& get_instance();
$CI->load->model('utils');
$empID = $this->session->userdata('username');
$isAdmin = $this->extras->findIfAdmin($empID);
$sel_emp = '';
$webSetup = '';

$weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$empID' AND STATUS = 'active'");
if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;

# kunin yung selected employee
if($isAdmin && $base_id){
    #echo "<pre>". "SELECT employeeid FROM ob_app_emplist WHERE base_id='{$base_id}'";
    $sel_emp = $this->db->query("SELECT employeeid FROM ob_app_emplist WHERE id='{$idkey}'")->row()->employeeid;
}
# end of newly added for ica-hyperion 21194 & 21196

?>
<style>
.th-style{
    text-align: center;
}
.form_row{
        padding-bottom: 10px;
    }
</style>
<form id="frmleave">

<input type="hidden" name="base_id" value="<?=$base_id?>">
<input type="hidden" name="sel_emp" value="<?=$sel_emp?>">
<input type="checkbox" name="ltype" value="CORRECTION" checked style="display: none;" />  

<div class="modal-dialog modal-lg">
    <div class="modal-content">
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
            <center><b><h3 tag="title" class="modal-title"><?=($base_id) ? " Edit " : " Add "?> Correction for Time in/out Application</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">

                <div id="wrapUpload" style="padding-bottom: 10px;">
                    <?php if(!$base_id){ ?>
                        <span id="fileErrorMsg" style="margin-left: 15px;"><b>Upload Supporting Documents</b></span>
                        <input type="file" name="filess" id="filess" style="margin-left:30px;display: inline;" >
                        <span id="fileErrorMsg" style="color: red; margin-left: 1px;"></span>
                    <?php }else{ ?>
                        <label id="processing" style="display: none;margin-left: 60%;"><img src='<?=base_url()?>images/loading.gif' />  Your request is processing, Please Wait..</label>
                        <label style="margin-left: 20%;color: blue;text-decoration: underline;" id="filename" file="" mime="">Click to view uploaded image.</label><br>
                    <?php } ?>
                </div><br>

                <!-- for ica-hyperion 21194 & 21196 -->
                <!-- by justin (with e) -->
                <?if($isAdmin){
                    # kapag admin ang nag applay ng leave request.. lilitaw ito..
                ?>
                <!-- Approve by approver section -->
                <div class="form_row">
                    <label class="field_name align_right">Will be approve by approver?</label>
                    <div class="field no-search">
                        <select class="form-control" name="allowApprover" id="allowApprover" style="width: 37%;">
                            <option value="1">YES</option> <!-- kapag yes, dadaan sa sequence approver -->
                            <option value="0">NO</option> <!-- kapag no, deretso approved na -->
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Employee</label>
                    <div class="field">
                        <select class="chosen col-md-4" id="employee" name="employee" <?=($sel_emp) ? "disabled" : ""?>>
                                <option value="all" >All Employee</option>
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
                <?}?>
                <!-- end for ica-hyperion 21194 & 21196 -->

                <div class="form_row">
                    <label class="field_name align_right" id="lblFrom">Leave From</label>
                    <div class="field">
                        <div class='input-group date' id="datesetfrom" data-date="<?$dfrom?$dfrom:$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 37%;">
                            <input type='text' class="form-control" size="16" name="datesetfrom" type="text" value="<?=$dfrom?>"/>
                            <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                            </span>
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


                        
                <div id="displayedTimeRecord" class="form_row">
                    <label class="field_name align_right">My Time Record</label>
                    <div class="field">
                        <table class="table table-hover table-bordered" id="tblTimeRecord" style="width: 600px;margin-bottom: 0px;">
                            <thead>
                                <tr style="background-color: #0072c6;">
                                    <th class="input-small align_center">Actual Time</th>
                                    <th class="input-small align_center">Request Time</th>
                                    <th class="input-small align_center">Status</th>
                                    <th class="input-small align_center">Edit</th>
                                </tr>
                            </thead>
                            <tbody id="displayedTimeInOut">
                                        <? $style = ''; if($base_id){ 
                                            $timerecord = $this->employeemod->findApplyTimeRecord($base_id);
                                        
                                            if(count($timerecord) > 0){
                                                foreach($timerecord as $tr){
                                                    if($tr->status == 'NEW') $style = 'style="background-color:#A6D89F"'; 
                                                    if($tr->status == 'UPDATED') $style = 'style="background-color:#B08CB0"';
                                                    if($tr->status == 'REMOVED') $style = 'style="background-color:#FF9C9C"';
                                                    ?>
                                                    <tr <?=$style?> class='remove' time_id='<?=$tr->tid?>'  id='TR-<?=$tr->tid?>'>
                                                        <td class="input-small align_center" id='AT-<?=$tr->tid?>'><?=$tr->actual_time?></td>
                                                        <td class="input-small align_center"  id='RT-<?=$tr->tid?>'><?=($tr->request_time)? $tr->request_time : "(--:-- --) - (--:-- --)"?></td>
                                                        <td class="input-small align_center"  id='ST-<?=$tr->tid?>'><?=$tr->status?></td>
                                                        <td class="input-small align_center">
                                                            <?
                                                                # for displayed button
                                                                if($tr->status){
                                                            ?>
                                                                <a class="btn btn-primary" id='edit'  code="<?=$tr->tid?>"><i class="icon glyphicon glyphicon-edit"></i></a>
                                                                <a class="btn btn-danger" id='remove'  code="<?=$tr->tid?>"><i class="icon glyphicon glyphicon-trash"></i></a>
                                                            <?
                                                                }
                                                            ?>
                                                        </td>
                                                    </tr>
                                        <?      } // end of for each
                                            }else{?>
                                            <tr><td class="input-small align_center remove" colspan="4">No data available...</td></tr>
                                        <?  } // end of if else condition
                                        

                                         }else{
                                        ?>
                                            <tr><td class="input-small align_center remove" colspan="4">No data available...</td></tr>
                                        <?}?>
                            </tbody>
                        </table>
                        <br />
                    </div>
                </div>

               
                
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
                    <div class="form_row">
                        <label class="field_name align_right">Time In</label>
                        <div class="field">
                            <div class="col-md-12"style="padding-left: 0px;">
                                <div class="col-md-5" style="display: block; margin-left: -15px; margin-right: 5px;">
                                    <div class='input-group time' id='datetimepicker3'>
                                        <input type='text' class="form-control" name="tfrom" id="tfrom" value="<?=$timein?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                                <span class="col-md-1" style="display: block;" id="timeto_text">&nbsp;<b>Out</b>&nbsp;</span>
                                <div class="col-md-5" style="margin-right: 5px;padding-left: 0px;">
                                    <div class='input-group time' id='datetimepicker3'>
                                        <input type='text' class="form-control" name="tto" id="tto" value="<?=$timeout?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-1" style="padding-left: 0px;width: 5.333333% !important;">
                                    <div class="input-group" id="hideBtn">
                                        <a class="btn btn-primary" id="add" code="0"><i class="icon-save"></i></a>
                                    </div>  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form_row" id="hideDays">
                    <label class="field_name align_right">Days</label>
                    <div class="field no-search">
                        <input class="col-md-3" type="text" name="ndays" id="ndays" value="1" readonly="" />
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" class="form-control"  id="reason" placeholder="Reason" ><?=$reason?></textarea>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer" >
            <div id="loading" hidden=""></div>
            <div id="saving" name='savingDiv'>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-success">&nbsp;&nbsp;Submit&nbsp;&nbsp;</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
    var toks = hex_sha512(" ");
    checkOBAttachments();
    $("#hideBtn").show();
    $("#hideTo").hide();
    $(".hidemo").hide();
    $("#lblFrom").text("Date of Deficiency");
    $("#hideTable").show();
    $("#datesetto").hide();

    var selDLtype = "<?=$othertype?>";
    $("#hideDays").hide();

    var rowCount = $('#displayedTimeInOut tr').length;
    if(rowCount > 0 && "<?=$base_id?>" != "") $("#hideTITO").hide();

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
        if ($(this).val() == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Date of Deficiency is required!!',
                showConfirmButton: true,
                timer: 1000
            })
            $("input[name='datesetfrom']").focus();
            return false;
        }
       var  start = new Date($(this).val()),
            end   = new Date($("input[name='datesetto']").val()),
            diff  = new Date(end - start),
            days  = diff/1000/60/60/24;
            if(days >= 0)   days += 1;
            $("#ndays").val(days);

            // $("#displayedTimeInOut").html("<tr><td colspan='4'><img src='<?=base_url()?>images/loading.gif' />  Finding records, Please Wait..</td></tr>");
            $.ajax({
                       url      :   "<?=site_url("employeemod_/displayedTITO")?>",
                       type     :   "POST",
                       data     :   {
                                        toks:toks,
                                        ldate : GibberishAES.enc($(this).val(), toks),
                                        ltype : GibberishAES.enc('Correction', toks)
                                        // for ica-hyperion 21194 & 21196
                                        // by justin (with e)
                                        // > para sa admin
                                        <?if($isAdmin){?>
                                        , empID : GibberishAES.enc($("select[name='employee']").val(), toks)
                                        <?}?>
                                        // end for ica-hyperion 21194 & 21196
                                    },
                       success  :   function(msg){
                            $(".remove").remove();
                            $("#displayedTimeInOut").html(msg);
                            var tr_id = $("#displayedTimeInOut").find("tr").attr("id");
                            if(tr_id) $("#hideTITO").hide();
                            else $("#hideTITO").show();
                            reloadButton();
                       }
                    });
            $("#ndays").val("1");
    });


    reloadButton(); // load button event
    function removeAllrow(){
        $( "#tblTimeRecord tbody tr" ).each( function(){
            this.parentNode.removeChild( this ); 
        });
    }
    function clearTime(){
        $("input[name='tfrom']").val('');
        $("input[name='tto']").val('');
    }


    function reloadButton(){
        $('#edit, #remove, #add, #clear').unbind('click').click(function(){
            var code = $(this).attr('code');
            //alert(code);
            $('input[name="tto"]').removeAttr('disabled');
            $('input[name="tfrom"]').removeAttr('disabled');

            // --- Clear function ---
            if($(this).attr('id') == 'clear'){
                $('input[name="tfrom"]').val('');
                $('input[name="tto"]').val('');
                $('#add').attr('code',code);
                return;
            }
            // --- End of Clear function ---

            // --- Edit function ---
            if($(this).attr('id') == 'edit'){
                $("#hideTITO").show();
                if($('#ST-'+ code).text() == 'REMOVED'){
                    // alert('Unable to edit this timerecord. Please Change the status before to modify this timerecord.')
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Unable to edit this timerecord. Please Change the status before to modify this timerecord.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                    return;
                }

                var t_val = "";
                if($('#ST-'+ code).text() == 'UPDATED' || $('#ST-'+ code).text() == 'NEW') 
                    t_val = $('#RT-'+ code).text().split(" - ");
                else
                    t_val = $('#AT-'+ code).text().split(" - ");

                $('input[name="tfrom"]').val(t_val[0]);
                $('input[name="tto"]').val(t_val[1]);
                $('#add').attr('code',code);

                // -- additional condition for mcu-hyperion 21181 --
                var timein, timeout = '';
                timein = $('input[name="tfrom"]').val();
                timeout = $('input[name="tto"]').val();

                if(timein != '--:-- --' && timeout == '--:-- --'){
                    //alert('asda1');
                    // $('input[name="tfrom"]').attr('disabled','disabled');
                }
                if(timein == '--:-- --' && timeout != '--:-- --'){ 
                    //alert('asda2');
                    $('input[name="tto"]').attr('disabled','disabled');
                }
                // -- end of additional condition for mcu-hyperion 21181 --
                return;
            }
            // --- End of Edit function ---
            
            // --- Remove function ---
            if($(this).attr('id') == 'remove'){
                // tag as un-removed
                if($("#ST-"+code).text() == 'REMOVED'){
                    $("#TR-"+code).attr('style','');
                    $("#ST-"+code).text('');
                    return;
                }
                
                // displayed selected time if actual or request time.
                var displayTime = 'AT';
                var sCode = code.split("-");
                if(sCode.length > 1) displayTime = 'RT';

                // for ica-hyperion 21194 & 21196
                // by justin (with e)
                // > check kapag code ay may 'TT'.
                if(sCode[0] == 'TT') displayTime = 'AT';
                // end for ica-hyperion 21194 & 21196
                
                // tag as removed
                var res = confirm("Are you sure, you want to remove this timerecord ("+ $('#'+ displayTime +'-'+ code).text() +")?");
                if(res){
                    if($("#ST-"+code).text() == 'NEW'){
                        $("#TR-"+code).remove();

                        // if no row left..
                        if($("#displayedTimeRecord").find('tbody tr').length == 0) $("#displayedTimeInOut").html($("#displayedTimeInOut").html() + '<tr class="remove"><td class="input-small align_center" colspan="4">No data available..</td></tr>');

                        return;
                    }
                    // not new
                    $("#TR-"+code).attr('style','background-color:#FF9C9C;');
                    $('#RT-'+code).text('(--:-- --) - (--:-- --)');
                    $("#ST-"+code).text('REMOVED');
                }
                return;
            }
            // --- End of Remove function ---

            // --- Save function ---
            if($(this).attr('id') == 'add'){
                var tto = convertTimeToNumber($('input[name="tto"]').val());
                var tfrom = convertTimeToNumber($('input[name="tfrom"]').val());

                // check if time is valid...
                if($('input[name="tto"]').val() == "" || $('input[name="tfrom"]').val() == "" || tfrom > tto || tto == tfrom || Number((tto - tfrom).toFixed(2)) == 0.02 || Number((tto - tfrom).toFixed(2)) == 0.01){
                    // alert("Invalid Time In and Time Out..");
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Invalid Time In and Time Out.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                    return;
                }
                
                if(code == "0"){ // new
                    var content = '';
                    var timeID = '';

                    if($("#displayedTimeRecord").find('tbody tr').find('td').length == 1){
                        timeID = 'T-0';
                        $(".remove").remove();
                    }else{
                        timeID = 'T-'+($("#displayedTimeRecord").find('tbody tr').length + 1);
                    }

                    content += '<tr class="remove" time_id="'+timeID+'" id="TR-'+ timeID +'">';
                    content += '<td id="AT-'+ timeID +'" style="text-align: center">(--:-- --) - (--:-- --)</td>';
                    content += '<td id="RT-'+ timeID +'" style="text-align: center">'+ $('input[name="tfrom"]').val() +' - '+ $('input[name="tto"]').val() +'</td>';
                    content += '<td id="ST-'+ timeID +'" style="text-align: center">NEW</td>';
                    content += '<td style="text-align: center">';
                    content += '<a class="btn btn-info" id="edit"  code="'+ timeID +'"><i class="icon glyphicon glyphicon-edit"></i></a>';
                    content += '<a class="btn btn-danger" id="remove"  code="'+ timeID +'"><i class="icon glyphicon glyphicon-trash"></i></a>';
                    content += '</td>';
                    content += '</tr>';
                    $("#displayedTimeInOut").html($("#displayedTimeInOut").html() + content);
                    $("#TR-"+timeID).attr('style','background-color:#A6D89F;');
                    clearTime();
                    reloadButton();
                    return;
                }else{ // update
                    //alert(code);
                    // check if timerecord is removed
                    if($("#ST-"+code).text() == 'REMOVED'){
                        // alert('Invalid to update. You must unremoved the selected timerecord before to save..');
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: 'Invalid to update. You must unremoved the selected timerecord before to save.',
                            showConfirmButton: true,
                            timer: 1000
                        })
                        return;
                    }

                    //save update
                    $('#RT-'+code).text($('input[name="tfrom"]').val() +" - " + $('input[name="tto"]').val());
                    
                    // check if update or this is new timerecord
                    var sCode = code.split("-");
                    if(sCode.length == 1){
                        $("#TR-"+code).attr('style','background-color:#B08CB0;');
                        $("#ST-"+code).text('UPDATED');
                    }

                    // for ica-hyperion 21194 & 21196
                    // by justin (with e)
                    // > check kapag code ay may 'TT'.
                    if(sCode[0] == "TT"){
                        $("#TR-"+code).attr('style','background-color:#B08CB0;');
                        $("#ST-"+code).text('UPDATED');
                    }
                    // end for ica-hyperion 21194 & 21196

                    clearTime();
                    return;
                }
            }
            // --- End of Save function ---
        });
    }

    $("input[name='datesetto']").change(function(){
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
        var iscontinue = false;
        $( "#tblTimeRecord tbody tr" ).each( function(){
            var status = $(this).find("td:eq(2)").text();
            if(!status) iscontinue = false;
            else iscontinue = true;
        });

        var base_id = "<?=$base_id?>";
        if(hasFiledCorrection() >= 1 && base_id==""){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: "Already applied correction on this date.",
                showConfirmButton: true,
                timer: 2000
            })
            return;
        }

        var counter = 0;
        $("#tblTimeRecord").find("tbody").find("tr").each(function(){
            if($('#ST-'+$(this).attr("time_id")).text() != 'REMOVED') counter++;
        })
        if(counter == 0) iscontinue = false;
        // updated by justin (with e) for #ica-21090 
		if($("#withpay").val() != "Select")
		{
            var tfrom = convertTimeToNumber($("input[name='tfrom']").val());
            var tto = convertTimeToNumber($("input[name='tto']").val());
            var form_data  = new FormData();
            var file_data = "";
            if($("#filess").val()) file_data = $("#filess").prop("files")[0]
            form_data.append("files",file_data);
            form_data.append("toks",toks);
            form_data.append("reason",GibberishAES.enc($("#reason").val(), toks));
            form_data.append("formdata", GibberishAES.enc(decodeURIComponent($("#frmleave").serializeAndEncode()), toks));
            //console.log(form_data);
            if($("input[name='ltype']").is(":checked") == false){
                // alert("Daily Leave Type is required!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Daily Leave Type is required!',
                    showConfirmButton: true,
                    timer: 1000
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
                    timer: 1000
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
                    timer: 1000
                })
                return false;
            }
            else if(($("input[name='tfrom']").val() == "" || $("input[name='tto']").val() =="") && selDLtype == "DIRECT"){
                // alert("Time In/Out is required!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Time In/Out is required!',
                    showConfirmButton: true,
                    timer: 1000
                })
                return false;
            }
            else if((tfrom > tto || tfrom == tto)  && selDLtype == "DIRECT"){
                // alert("Invalid Time In/Out!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Invalid Time In/Out!',
                    showConfirmButton: true,
                    timer: 1000
                })
                return false;
            }
            else if($("input[name='tfrom']").val() != "" && $("input[name='tto']").val() !="" && selDLtype == "CORRECTION"){
                // alert("Please saved the Time In/Out first!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please saved the Time In/Out first!',
                    showConfirmButton: true,
                    timer: 1000
                })
                return false;
            }else if($("select[name='employee']").val() == ""){
                // alert("Please saved the Time In/Out first!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please select employee.',
                    showConfirmButton: true,
                    timer: 1000
                })
                return false;
            }
            else if($("#tblTimeRecord").find("tbody").find("td").length == 1 && selDLtype == "CORRECTION"){
                // alert("Time Record is required!.");
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Time Record is required!',
                    showConfirmButton: true,
                    timer: 1000
                })
                return false;


            }
            else if($("#reason").val() == ""){
                $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
                return false;
            }
            else if(!iscontinue){
                alert("No records saved");
                return false;
            }
            else{
                if($("select[name='employee']").val() == "all")  $("div[name='savingDiv']").html("<img src='<?=base_url()?>images/loading.gif' />  Validating employees, please wait..");
                else $("div[name='savingDiv']").html("<img src='<?=base_url()?>images/loading.gif' />  Validating employee, please wait..");
                    $.ajax({
                       url      :   "<?=site_url("ob_application_/saveOBApp")?>",
                       type     :   "POST",
                       contentType: false,
                       processData: false,
                       dataType :   'json',
                       data     :   form_data,
                       success  :   function(msg){
                        
                            if(selDLtype == "CORRECTION" && msg.err_code == 1){
                                // get the timerecord on table
                                $("div[name='savingDiv']").html("<img src='<?=base_url()?>images/loading.gif' /> Saving time record, please wait..");
                                var timeRecord = "";
                                $("#tblTimeRecord").find("tbody tr").each(function(){
                                    if($(this).find("td").length>1){
                                        timeid = $(this).attr('id').split("TR-");
                                        timeRecord += (timeRecord?"|":"");
                                        timeRecord += timeid[1]; // timeid
                                        timeRecord += "~u~";
                                        timeRecord += $(this).find("td:eq(0)").text(); // Actual Time
                                        timeRecord += "~u~";
                                        timeRecord += $(this).find("td:eq(1)").text(); // Request Time
                                        timeRecord += "~u~";
                                        timeRecord += $(this).find("td:eq(2)").text(); // Status
                                    }
                                });

                                // save here the timerecord
                                $.ajax({
                                    url     : "<?=site_url("employeemod_/saveTimeRecord")?>",
                                    type    : "POST",
                                    data    : {
                                                toks:toks,
                                                aid         : GibberishAES.enc(msg.base_id, toks),
                                                cdate       : GibberishAES.enc($("input[name='datesetfrom']").val(), toks),
                                                time_record  : GibberishAES.enc(timeRecord, toks)
                                              },
                                    success : function(res){
                                        $("div[name='savingDiv']").html('<button type="button" id="close" class="btn btn-danger modalclose" data-dismiss="modal">Close</button><button type="button" id="save" class="btn btn-success">&nbsp;&nbsp;Submit&nbsp;&nbsp;</button>');
                                        // alert(res);
                                        if(res == "No records saved"){
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Warning!',
                                                text: res,
                                                showConfirmButton: true,
                                                timer: 1000
                                            })
                                        }else{
                                           Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: res,
                                                showConfirmButton: true,
                                                timer: 1000
                                            }) 
                                        }
                                            
                                        //loadbushistory('',0,'apply');
                                        $(".modalclose").click();
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

                                });
                            }else{
                                $("div[name='savingDiv']").html('<button type="button" id="close" class="btn btn-danger modalclose" data-dismiss="modal">Close</button><button type="button" id="save" class="btn btn-success">&nbsp;&nbsp;Submit&nbsp;&nbsp;</button>');
                                if(msg.err_code == 1){
                                    // alert(msg.msg);
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: msg.msg,
                                        showConfirmButton: true,
                                        timer: 1000
                                    })
                                    // msg.base_id;
                                    loadbushistory('',0,'apply');
                                    $(".modalclose").click();
                                }else{
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Warning!',
                                        text: msg.msg,
                                        showConfirmButton: true,
                                        timer: 2500
                                    });
                                    // $("#saving").show();
                                }
                            }
                       }
                    });
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
                timer: 1000
            })
		}
        // end of updated by justin (with e) for #ica-21090 
    });

 function hasFiledCorrection(){
        var hasfiled = timefrom = timeto = "";
        $.ajax({
            url: "<?= site_url('ob_application_/hasFiledCorrection') ?>",
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

function checkOBAttachments(){
    $("#filename").hide();
    $("#processing").show();
    if("<?=$base_id?>"){
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
            if(!response.file && !response.mime){
                $("#wrapUpload").hide();
            }
          }
        }).done(function(){
          $("#filename").show();
          $("#processing").hide(); 
    });
    }
}

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

$.fn.serializeAndEncode = function() {
    return $.map(this.serializeArray(), function(val) {
        return [val.name, encodeURIComponent(val.value)].join('=');
    }).join('&');
};

$("#datesetfrom,#datesetto").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".time").datetimepicker({
    format: "LT"
});
$(".chosen").chosen();
//}
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
                toks: toks,
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
                        timer: 1000
                    })
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 1000
                    })
                }
                location.reload();  
            }
        });
    });

   

</script>
<?}?>
<script type="text/javascript" src="<?=base_url()?>/js/timeValidation.js"></script>