<?php

/**
 * @author Angelica Arangco 2017
 *
 */
$user = $this->session->userdata("username");
$isreadonly = $job == 'edit' ? '' : 'readonly';
$isdisabled = $job == 'edit' ? '' : 'disabled';
$ishidden   = $job == 'edit' ? '' : "style='display:none;'";

$isdisabled = $colhead != 'cphead' ? '' : 'disabled';
$isreadonly = $colhead != 'cphead' ? '' : 'readonly';

$disabled_checkb = '';
$disabled_type_withpay = "";
if($user != $hrhead) $disabled_type_withpay = "disabled";
$approver_position = $this->extensions->getEmployeePositionId($this->session->userdata('username'));
// if($approver_position != 183) $disabled_checkb = "disabled";

?>

<form id="form_leave">
<input hidden="" id="leavebal" value="" />

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
            <center><b><h3 tag="title" class="modal-title">Leave Application Details</h3></b></center>
        </div>
        <label id="processing" style="display: none;margin-left: 60%;"><img src='<?=base_url()?>images/loading.gif' />  Your request is processing, Please Wait..</label>
              <label style="margin-left: 60%;color: blue;text-decoration: underline;" id="filename" name='filename' file="" mime="">Click to view uploaded image.</label><br>
        <div class="modal-body">
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Name</label>
                    <div class="field">
                        <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$fullname?></b></span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Office</label>
                    <div class="field">
                        <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$edept?></b></span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Position</label>
                    <div class="field">
                        <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$pos?></b></span>
                    </div>
                </div>

                <div class="form_row">
                    <label class="field_name align_right">Leave Type </label>
                    <div class="field no-search" style="padding-bottom: 10px;">
                        <select name="ltype" class="form-control" style="width: 60%;" <?=$disabled_type_withpay?> <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?> >

                        </select>
                    </div>
                </div>

                <div class="form_row" <?= ($code_request == "PL-SEM")? "":"hidden" ?> style="display:none;">
                    <label class="field_name align_right">Category </label>
                    <div class="field no-search" style="padding-bottom: 10px;">
                        <select name="catleave" class="form-control" style="width: 60%;" <?=$disabled_type_withpay?> >
                            <option value="">-Select Category-</option>
                            <?
                                $catlist = $this->utils->getLeaveCategories();
                                foreach ($catlist as $code => $desc) {?>
                                    <option value="<?=$desc->level?>" <?= ($desc->level == $leavecategory)? "selected":"" ?>><?=$desc->level?></option>
                                <?}
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form_row">
                    <label class="field_name align_right">With Pay?</label>
                    <div class="field no-search">
                        <select class="form-control" name="withpay" id="withpay" style="width: 28%;" <?=$isdisabled?> <?=$disabled_type_withpay?> <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?> ><?=$this->employeemod->withPay($paid);?></select>
                    </div>
                </div>
                <div class="form_row">
                    <div class="field" style="padding-bottom: 10px;">
                    &nbsp;<input type="checkbox" class="double-sized-cb" name="ishalfday" value="1" <?=$disabled_type_withpay?> <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?> <?=$isHalfDay?'checked':''?> >&nbsp;&nbsp; <b>Check this if your leave to be applied is halfday</b>
                    </div>
                </div>
                <div class="form_row" style="padding-bottom: 10px;">
                    <div class="align_left" style="margin-left: 20%;"><b>Leave From <span id="datetotext" style="margin-left: 21%;">To</span></b></div>
                    <div class="field" style="width: 60%;">
                        <div class="col-md-12" id="date_div" style="padding-left: 0px;">
                            <div class="col-md-5" style="padding-left: 0px;">
                                <div class='input-group date' id="datesetfrompicker" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd" disabled="">
                                    <input type='text' class="form-control" size="16" name="datesetfrom" type="text" value="<?=$dfrom?>" <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?> <?=$disabled_type_withpay?> autcomplete="off"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-5" style="margin-left: 8px;">
                                <div class="input-group date" id="datesettopicker" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd" disabled="">
                                    <input type='text' class="form-control" size="16" name="datesetto" type="text" value="<?=$dto?>" <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?> <?=$disabled_type_withpay?> autcomplete="off" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="form_row">
                    <div class="field" style="width: 60%;">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <div class="col-md-5" style="padding-left:0px;">
                                <label class="field_name align_right">Days</label>
                                <input type="text" name="ndays" id="ndays" value="<?=$nodays?>" class="form-control" <?=$isreadonly?> onkeypress="return numbersonly()" style="width: 75%;margin-left:25%;" />
                            </div>
                            <div class="col-md-7" style="<?= (substr($leavetype, 0, 3) == "PL-") ? '' : 'display: none' ?>">
                            &nbsp;&nbsp;&nbsp;<input type="checkbox" class="double-sized-cb" name="notdeduct" value="<?=$notdeduct?>" style="margin-top: 3%;">&nbsp;&nbsp; <b>Check this if this leave will not deduct on leave balance.</b>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="form_row" style="padding-bottom: 10px; display: none">
                    <label class="field_name align_right">No. of days</label>
                    <div class="field no-search">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <input type="text" class="form-control" name="nodays" id="nodays" placeholder="No. days" style="width: 61%;" value="" readonly />
                            <span id="loadingdays" hidden=""></span>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">No. of leave credit/s to be deducted</label>
                    <div class="field no-search">
                        <input type="text" name="ndays" id="ndays" value="<?=$nodays?>" class="form-control"  <?=$disabled_type_withpay?> <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?> onkeypress="return numbersonly()" style="width: 28.5%;" />
                    </div>
                </div>

                <div class="form_row">
                    <div class="field no-search">
                       <div class="col-md-7" style="<?= (substr($leavetype, 0, 3) == "PL-") ? '' : 'display: none' ?>">
                            &nbsp;&nbsp;&nbsp;<input type="checkbox" class="double-sized-cb" name="notdeduct" value="<?=$notdeduct?>" style="margin-top: 3%;">&nbsp;&nbsp; <b>Check this if this leave will not deduct on leave balance.</b>
                        </div>
                    </div>
                </div>
                <div class="form_row" id="wrap_sched_affected" <?=($isHalfDay) ? "": 'style="display: none;"'?>>
                    <label class="field_name align_right">Check Schedules Affected</label>
                    <div class="field" id="sched_affected">
                        No Schedule     
                    </div>
                </div>
                <div id="seminar_app" style="<?= (substr($leavetype, 0, 3) == "PL-") ? '' : 'display: none' ?>;">
                    <div class="row">
                        <!-- <div class="col-md-12" style="margin-left: 0px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar Category</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="category" style="display: inline;margin-left: 13px;width: 92%;" disabled="">
                                        <option value=""> - Select Seminar Category - </option>
                                        <?php
                                            $seminarList = Globals::seminarList();
                                            foreach($seminarList as $c=>$val){
                                                ?><option value="<?=$c?>" <?= (isset($category) && $category==$c) ? "selected" : "" ?> ><?=$val?></option><?    
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-md-12" style="margin-left: 0px;display:none;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar - Workshop/Training</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="seminar" style="display: inline;margin-left: 13px;width: 92%;" disabled="">

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Type of Seminar Title</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <textarea class="form-control" id="seminar_title" value="<?= isset($title) ? $title : '' ?>" style="width: 92%;height: 80px;margin-left: 13px;" disabled=""><?= isset($title) ? $title : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Organizer</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" class="form-control" name="organizer" value="<?= isset($organizer) ? $organizer : '' ?>" style="margin-left: 13px;width: 92%;" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-left: 0px;padding-bottom: 10px;display:none;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Venue</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="venue" style="display: inline;margin-left: 13px;width: 35%;" disabled="">
                                        <option value="sample">Sample</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Location</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="text" name="location" value="<?= isset($location) ? $location : '' ?>" class="form-control" style="margin-left: 13px;width: 34.5%;" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Registration Fee</label>
                                    <div class="col-md-8">
                                        <input type="number" name="fee" value="<?= isset($fee) ? $fee : '' ?>" class="form-control" value="" style="margin-left: 8px;" disabled=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" style="padding-left: 0px;">
                                <div class="form-group">
                                    <label class="col-md-4 align_right">Deadline of Registration</label>
                                    <div class="col-md-8">
                                        <div class="input-group date" data-date="<?= isset($datesetto) ? $datesetto : '' ?>" data-date-format="yyyy-mm-dd" style="width: 86%;" disabled="">
                                            <input type="text" name="deadline" class="form-control" value="<?= isset($deadline) ? $deadline : '' ?>" disabled=""/>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Transportation</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="number" name="transportation" value="<?= isset($transportation) ? $transportation : '' ?>" class="form-control" style="margin-left: 13px;width: 34.5%;" disabled="">
                                </div>
                            </div>
                        </div>
                       <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Accomodation</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="number" name="accomodation" value="<?= isset($accomodation) ? $accomodation : '' ?>" class="form-control" style="margin-left: 13px;width: 34.5%;" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Others</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <input type="number" name="others" value="<?= isset($others) ? $others : '' ?>" class="form-control" style="margin-left: 13px;width: 34.5%;" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Total:</label> <span id="budget_total" style="margin-left: 3%;font-weight: bold;" ><?= isset($total) ? $total."PHP" : '0' ?></span>
                                <input type="hidden" name="total" value="<?= isset($total) ? $total : '0' ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right"><?= (substr($leavetype, 0, 3) == "PL-") ? 'Other Remarks' : 'Reason' ?></label>
                    <div class="field no-search" style="padding-bottom: 10px;">
                        <textarea rows="4" style="width: 90%;resize: none;" class="form-control" name="reason" id="reason" placeholder="Reason" <?=$isreadonly?> disabled="" ><?=$reason?></textarea>
                    </div>
                </div>
                <div class="form_row" style="<?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'display:none;' : "")?>">
                    <label class="field_name align_right">Status</label>
                    <div class="field no-search">
                        <select class="form-control" name="mh_status" id="mh_status" <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?> style="width: 40%;" >
                            <?
                                $opt_status = $this->extras->showLeaveStatus();
                                foreach($opt_status as $c=>$val){
                                if($val == "APPROVED"){
                                    if($this->extensions->checkIfSecondApprover($idkey, "leave")) $val = "APPROVED";
                                    else $val = "ENDORSED";
                                    if($colhead == 'hrhead') $val = "NOTED";
                                }
                            ?><option<?=($c==$colstat ? " selected" : "")?> value="<?=$c?>" ><?=($val=="PENDING"?"Select status..":$val)?></option><?    
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form_row" style="<?= (!in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'display:none;' : "")?>">
                    <label class="field_name align_right">Status</label>
                    <div class="field no-search">
                        <input type="text" class="form-control" style="width: 40%;" disabled value="<?= ($colstat != 'DISAPPROVED') ? $this->extensions->statusLabel($base_id, "leave", $colhead) : 'DISAPPROVED'?>">
                    </div>
                </div>
                <br>
                <!-- <?=$colstat=='DISAPPROVED'?'':'style="display: none;"'?> -->
                <div class="form_row" id='remarks' style="<?= ($colstat != 'DISAPPROVED') ? 'display:none;' : '' ?>">
                    <label class="field_name align_right">Remarks</label>
                    <div class="field no-search">
                        <input class="col-md-8 form-control" style="width: 90%;" type="text" name="txtRemarks" id="txtRemarks" value="<?=($rem) ? $rem : $remarks?>" <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?>/>
                    </div>
                </div>

        </div>

        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <?if(!in_array($colstat,array("APPROVED","DISAPPROVED"))){?>
                    <button type="button" id="save" class="btn btn-success">Save</button>
                <?}?>
            </div>
        </div>

    </div>
</div>
<script>
  var data = "";
  var mime = "";
  var loadStat = true;
  var fileinter;

    var toks = hex_sha512(" ");
    <?if($isHalfDay == 1){?>
        checkSchedAffected();
    <?}?>
    getAvailableOtherLeave();
    countDays($("#datesetfrompicker").find("input").val(), $("#datesettopicker").find("input").val());
    getSeminar($("select[name='category']").val());
    var l_type = '';

    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });
    $('.chosen').chosen();
    
    $(document).ready(function(){
        l_type = $("select[name='ltype']").val();
        if($("input[name='notdeduct']").val() == "1") $("input[name='notdeduct']").prop( "checked", true );
        disabledWithPay(l_type);
        setTimeout(function() {
            loadLazy();
        }, 500);
    });

    $("#save").click(function(){  
        var newstat = $("#mh_status").val();
        var endorse = $("#mh_status option:selected").text();
        var oldstat = '<?=$colstat?>';
        var withpay = $("select[name='withpay']").val();
        var datesetfrom = $("input[name='datesetfrom']").val();
        var datesetto = $("input[name='datesetto']").val();
        var ndays = $("input[name='ndays']").val();
        var notdeduct = ($("input[name='notdeduct']").is(":checked")) ? 1 : 0;
        if(newstat == oldstat){
            // alert('No changes were made.');
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'No changes were made.',
                showConfirmButton: true,
                timer: 1000
            })
            $("#close").click();
            return false;
        }

        /*get new code_request*/
        var ltype_len = "<?= strlen($leavetype) ?>";
        var code_request = "<?=$code_request?>";
        if(code_request.substring(ltype_len)){
            code_request = code_request.substring(ltype_len);
            code_request = $("select[name='ltype']").val() + code_request;
        }
        /*end*/

        var form_data = 
            {
                "<?=$base_id?>" :{
                    colhead         : "<?=$colhead?>",
                    isLastApprover  : "<?=$isLastApprover?>",
                    code_request    : code_request,
                    leaveid         : "<?=$leaveid?>",
                    base_id         : "<?=$base_id?>",
                    status          : newstat,
                    status_desc     : endorse,
                    remarks         : $("#txtRemarks").val(),
                    withpay         : withpay,
                    datesetfrom     : datesetfrom,
                    datesetto       : datesetto,
                    ndays           : ndays,
                    notdeduct       : notdeduct,
                    l_type          : $("select[name='ltype']").val(),
                    update          : true
                }
            };

        $.ajax({
            url:"<?=site_url("leave_application_/saveLeaveStatusChange")?>",
            type:"POST",
            dataType : 'json',
            data:form_data,
            success: function(msg){
                $("#close").click();
                // alert(msg.msg);
                if(msg.err_code == 1){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer:3000
                    })
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer:3000
                    })
                }
                view_leave_status('','','PENDING');
                getUpdatedManageNotification("LEAVE");
            }
        });
    });
    
    $('#mh_status').on('change',function(){
        if($(this).val() == 'DISAPPROVED'){
            $('#remarks').show();
        }else{
            $('#remarks').hide();
        }
    });

    $("#othleave").css("pointer-events","none");
    $("input[name='ltype']").on('change', function() {
        $("input[name='ltype']").not(this).prop('checked', false);
        if($(this).val() == "other"){
            l_type = $(this).val();
            disabledWithPay($(this).val());
            $("#othleave").css("pointer-events","");
        }
        else{
            l_type = $(this).val();
            if($(this).val() == "ABSENT") $("#withpay").val("NO").trigger("liszt:updated");
            if($(this).val() == "EL") $("#withpay").val("YES").trigger("liszt:updated");
            disabledWithPay($(this).val());
            $("#othleave").css("pointer-events","none").val("");
        }
    });

    $("#filename").click(function(){
        if (data != "") {
          var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
          window.open(objectURL);
        }else if(loadStat == true){
          Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Please wait loading file.',
                showConfirmButton: true,
                timer: 1000
          });
          fileinter = setInterval(fileChecker, 1500);
        }else{
          Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'No File Uploaded.',
                showConfirmButton: true,
                timer: 3500
          })
        }
    });

    function loadLazy(){
      $.ajax({
          url:"<?=site_url('leave_application_/getLeaveAttachments')?>",
          type: "POST",
          data:{base_id:"<?=$base_id?>"},
          dataType: "json",
          async:true,
          success:function(response){
            // console.log("<?=$base_id?>");
            if(response.file && response.mime) $("label[name='filename']").show();
            else  $("label[name='filename']").hide();
            data = response.file;
            mime = response.mime;
            loadStat = false;
          }
        });
    }

    function fileChecker() {
      if (loadStat == false) {
        clearInterval(fileinter);
        var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
        window.open(objectURL);
      }
    }

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

    function disabledWithPay(category){
        if(category == "other" && "<?= $approver_position?>" == 183) $("#withpay").prop('disabled', false).trigger("liszt:updated");
        else if(category != "other" && "<?= $approver_position?>" == 183) $("#withpay").prop('disabled', true).trigger("liszt:updated");
    }

    function getSeminar(code){
        $.ajax({
            url : "<?= site_url('extensions_/showreportseduclevel') ?>",
            type: "POST",
            data: {code: code, idkey: "<?= isset($seminar) ? $seminar : '' ?>"},
            success:function(response){
                $("select[name='seminar']").html(response).trigger("chosen:updated");
            }
        })
    }

    function getAvailableOtherLeave(){
        var employeeid = "<?= $employeeid ?>";
        var date;
        if ("<?php echo $job ?>" == 'edit') {
            date = $("#datesetfrompicker").find("input").val();
        }else{
            date = "<?php echo date("Y-m-d") ?>";
        }
        $.ajax({
            url: "<?= site_url('leave_/getEmployeeLeaveList') ?>",
            type: "POST",
            data: {employeeid: employeeid, leavetype: "<?= $leavetype ?>", date:date},
            success:function(response){
                $("select[name='ltype']").html(response).trigger("liszt:updated");
            }
        })
    }

    function checkSchedAffected(){
        $("#errormsg").hide();
        if($('input[name=ishalfday]').is(":checked")){

            var start = $("#datesetfrompicker").find("input").val();
            /*$("#datesettopicker").find("input").val(start);
            $("#datesettopicker").hide();
            $("#datetotext").hide();*/
            
            $("input[name='ndays']").val('0');
            if(start != ''){

                    $.ajax({
                       url      :   "<?=site_url("leave_/getEmployeeScheduleStartEnd")?>",
                       type     :   "POST",
                       data     :   {
                                        start : start,
                                        empID : "<?= $employeeid ?>"

                                    },
                       success  :   function(ret){
                        var arr_sched = JSON.parse(ret);

                        var hrs = 0;
                        var fromtime    = '',
                            totime      = '',
                            isAm        = '',
                            isBoth      = '';
                        $("input[name='ndays']").val(hrs);
                        ///< append sched affected
                        if($(arr_sched).size() > 0){
                            $('#sched_affected').html("");

                            for (var key in arr_sched) {

                                if(key=='FLEXI'){
                                    $("input[name='ndays']").val(0.5);
                                }else{

                                    var key_arr = key.split('|');
                                    fromtime = key_arr[0] ? key_arr[0] : '';
                                    totime   = key_arr[1] ? key_arr[1] : '';
                                    hrs      = key_arr[2] ? key_arr[2] : 0;
                                    isAm     = key_arr[3] ? key_arr[3] : 0;
                                    isBoth      = key_arr[4] ? key_arr[4] : 0;
                                    // $sched_affected

                                    // for ica-hyperion 21194
                                    // modified by justin (with e)
                                    var val = fromtime +"|"+ totime;
                                    var selSched = '', isChecked = '';
                                    if("<?=$sched_affected?>") selSched = "<?=$sched_affected?>";
                                    
                                    if(val == selSched){
                                        isChecked = "checked";
                                        // var days = 0.5;
                                        var start = $("input[name='datesetfrom']").val();
                                        var end = $("input[name='datesetto']").val();
                                        // var nodays = countDaysWithinScheduleHalfday(start, end);
                                        // days *= nodays;
                                        countDaysWithinScheduleHalfday(start, end);
                                        // $("input[name='ndays']").val(days);
                                    } 
                                    // end for ica-hyperion 21194

                                    $('#sched_affected').append('<span class="col-md-4"><input type="checkbox" name="sched_affected[]" class="sched_affected" fromtime="'+fromtime+'" totime="'+totime+'" hrs="'+hrs+'" isAm="'+isAm+'" isBoth="'+isBoth+'" value="'+ val +'" '+ isChecked +' > '+arr_sched[key]+'</span>');

                                    $('#wrap_sched_affected').show();
                                }

                            }
                        }else{
                            $('#sched_affected').html("No Schedule");
                            $('#wrap_sched_affected').show();
                        }
                       }
                    });
            }
        }else{
            $("#datesettopicker").show();
            $("#datetotext").show();
            $('#wrap_sched_affected').hide();
            var start = $("#datesetfrompicker").find("input").val();
                end   = $("#datesettopicker").find("input").val();
            countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
            countDays(start, end);
        }
    }

    function countDaysWithinSchedule(start, end){
        $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
        $.ajax({
           url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
           type     :   "POST",
           data     :   {
                            start : start, 
                            end : end,
                            empID : "<?= $employeeid ?>"
                        },
           success  :   function(days){
            $("input[name='ndays']").val(days);
            $("#loadingdays").hide();
           }
        });
    }

    function countDays(start, end){
        $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
        $.ajax({
           url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
           type     :   "POST",
           data     :   {
                            alldays : true,
                            start : start, 
                            end : end,
                            empID : "<?= $employeeid ?>"
                        },
           success  :   function(days){
            $("#nodays").val(days);
            $("#loadingdays").hide();
           }
        });
    }

    // function countDaysWithinScheduleHalfday(start, end){
    //     var scheddays = 0;
    //     $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
    //     $.ajax({
    //        url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
    //        type     :   "POST",
    //        data     :   {
    //                         toks:toks,
    //                         start : GibberishAES.enc(start, toks), 
    //                         end : GibberishAES.enc(end, toks),
    //                         withpay : GibberishAES.enc($("select[name='withpay']").val(), toks),
    //                         empID : GibberishAES.enc("<?=$employeeid?>", toks)
    //                     },
    //                     async: false,
    //        success  :   function(days){
    //             scheddays = days;
    //        }
    //     });

    //     return scheddays;
    // }

    function countDaysWithinScheduleHalfday(start, end){
        var scheddays = 0;
        // var days = 0.5;         
        var days = ("<?=$nodays?>" != '' ? "<?=$nodays?>" : 0.5);                                      
        if ("<?=$paid?>" == "NO") {days = 0;}         
        // $("#loadingdays").show().html("<img src='<?=base_url()?>images/loading.gif' />");
        $.ajax({
           url      :   "<?=site_url("leave_/countDaysWithinSchedule")?>",
           type     :   "POST",
           data     :   {
                            toks:toks,
                            start : GibberishAES.enc(start, toks), 
                            end : GibberishAES.enc(end, toks),
                            // fordetails : GibberishAES.enc("1", toks),
                            withpay : GibberishAES.enc($("#lwithpay").val(), toks),
                            empID : GibberishAES.enc("<?=$employeeid?>", toks)
                        },
                        // async: false,
           success  :   function(nodays){
                scheddays = nodays;
                // days = scheddays;
                $("input[name='ndays']").val(days);
           }
        });

        // return scheddays;
    }

    function getUpdatedManageNotification(module){
      $.ajax({
        url: "<?=site_url('utils_/getUpdatedManageNotification')?>",
        type: "POST",
        data: {module:module},
        success:function(response){
          $("a[menuid='78']").find(".notifcount").text(response);
          if(response == 0){
            $("a[menuid='78']").find(".notifdiv").hide();
          }
        }
      });

      $.ajax({
        url: "<?=site_url('utils_/getApproverUpdatedNotification')?>",
        type: "POST",
        data: {module:module},
        success:function(response){
          $("a[menuid='73']").find(".notifcount").text(response);
          if(response == 0){
            $("a[menuid='73']").find(".notifdiv").hide();
          }
        }
      });
    }

</script>
