<?php
$CI =& get_instance();
$CI->load->model('applicantt');
$usertype = $this->session->userdata("usertype");
$last_status = $code_status;
$next_status = $last_status+1;
$code_status = $CI->applicantt->getNextApplicantStatus($code_status);
$apprv_list         = $CI->applicantt->getApprovalStatusSetup(true);
$doc_list           = $CI->applicantt->getDocumentSetup(true);
$doc_list_submitted = $CI->applicantt->getApplicantDocumentSubmitted(true,$applicantId);
$applicant_name = $CI->applicantt->getApplicantName($applicantId);
$status = $CI->applicantt->getApplicantStatus($applicantId);
$applicant_approval_setup = $CI->applicantt->getApplicantSetup($positionid, $code_status, true);
// echo "<pre>"; print_r($this->db->last_query()); die;
$app_stat = $assigned_head = "";
$userid = $this->session->userdata("username");
$lastSequnce = $CI->applicantt->getLastSequence($code_status);
$isendorsed = $CI->applicantt->checkApplicationEndorsement($applicantId);
$pointer_off = $endorsementRemarks = $endorsementStat = "";
if($isendorsed->num_rows() > 0){
    $pointer_off = 'style="pointer-events:none"';
    $endorsementRemarks = $isendorsed->row()->remarks;
    $endorsementStat = $isendorsed->row()->status;
}
?>
<style>
hr{
  border-top: 1px solid #3a4651;
  border-color: a0d1ca;
}

thead{
  color: #000;
}
h5{
  font-weight: 500;
}
</style>
<input type="hidden" id="code_status" value="<?= $last_status ?>">
<div class="widgets_area" <?=$pointer_off?> >
    <div  class="row">
        <?php
            if(isset($redtag) && $redtag == 0){
                ?>
                    <div class="col-md-12" id="remarkNow" style="margin-top: 1%; margin-bottom: 1%; padding: 0px;" hidden>
                        <div class="col-md-9" >
                            <div class="col-sm-3">
                                <h5 style="font-weight: 700">Red Flag Remarks:</h5>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form form-control rtRemarks" name="rtRemarks" id="rtRemarks" value="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary redtag" id="redtag"  style=" float: right;">Save Red Flag Remarks</button>
                        </div>
                    </div>
                    <div  class="col-md-12" style="margin-top: 1%; margin-bottom: 1%; padding: 0px;" id="hideBtn">
                        <div>
                            <button class="btn btn-primary redtag" id="redtags" style="float: right; margin-right: 21px;">Tag as Red Flag</button>
                        </div>
                    </div>
                <?php
            }else{
                ?>  
                    <div class="col-md-12" style="margin-top: 1%; margin-bottom: 1%; padding: 0px;">
                        <div class="col-md-9">
                            <div class="col-sm-3">
                                <h5>Red Flag Remarks:</h5>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form form-control rtRemarks" name="rtRemarked" id="rtRemarked" value="<?= $remarks ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-danger redtag" id="redtag"  style="pointer-events: none; float: right; margin-right: 6px;">Tagged as Red Flag</button>
                        </div>
                    </div>
                <?php
            }
        ?>
    </div>
    <div class="row">
        <?php foreach($applicant_approval_setup as $row): ?>
            <?php if(!$row["isrequirements"] && !$row["isprerequirements"]){ ?> 
                <div class="panel">
                    <div class="panel-heading"><h4><b><?= $row["description"] ?> &nbsp;&nbsp; - &nbsp;&nbsp; Step: <?= $row["seqno"] ?></b></h4></div>
                    <?php
                        $applicant_current_status = $CI->applicantt->getApplicantSequence($applicantId, $row["id"]);
                        $assigned_head = $CI->applicantt->getApplicantSequenceApprover($applicantId, $row["id"]);
                        $app_stat = $CI->applicantt->getApplicantSequenceStatus($applicantId, $row["id"]);

                    ?>
                    <div class="panel-body"  >
                        <?php 
                        if(!$row["islaststep"] && $lastSequnce != $row["id"]){ 
                        foreach($applicant_current_status["record"] as $key=>$value){ ?>
                                <div class="col-sm-12 <?= $row['id'] ?>" <?= ($code_status != $row['id']) ? "style='pointer-events: none'" : ""; ?>>
                                    <br>
                                    <span class="col-sm-3"><b><?= key($value) ?> :</b></span>
                                    <?php if (key($value) == "DATE") { ?>
                                        <div class='input-group date' style="width: 70%;">
                                            <input type='text' class="form-control date" name="<?= $row['id'] ?>" value="<?= $value[key($value)] ?>" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    <? }elseif (key($value) == "TIME") {?>
                                        <div class='input-group time' style="width: 70%;">
                                            <input type='text' class="form-control time" name="<?= $row['id'] ?>" value="<?= $value[key($value)] ?>" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    <? }elseif (key($value) == "TEXT") {?>
                                       <input type="text" class="col-sm-8 form-control" name="<?= $row['id'] ?>" value="<?= $value[key($value)] ?>"  style="width: 70%;">
                                   <? }elseif (key($value) != "TEXT" && key($value) != "FILE" && key($value) != "TIME" && key($value) != "DATE") {?>
                                       <input type="text" class="col-sm-8 form-control" name="<?= $row['id'] ?>" value="<?= $value[key($value)] ?>" style="width: 70%;" >
                                   <? }elseif (key($value) == "FILE") {?>
                                    <?php
                                    $codeFileData = explode("|", $value[key($value)]);
                                    if(!isset($codeFileData[0])) $codeFileData[0] = "";
                                    if(!isset($codeFileData[1])) $codeFileData[1] = "";
                                    if(!isset($codeFileData[2])) $codeFileData[2] = "";
                                    ?>
                                            <div class="col-sm-9" style="padding: 0px;">
                                                <div class="col-md-8"  style="padding: 0px;">
                                                    <input name="<?= $row['id'] ?>" class="form-control" type="file" data="test" filename="<?= $codeFileData[0] ?>" file="<?= $codeFileData[2] ?>" filetype="<?= $codeFileData[1] ?>"/>
                                                </div>
                                                <div class="col-md-4 <?=$assigned_head?>" style="padding: 0px; float: right; <?=($userid == $assigned_head || $code_status == $row['id'] || $codeFileData[0] != "") ? '' : 'pointer-events: none'?>" >
                                                    <button class="btn btn-primary viewInfo viewInfo_<?= $row['id'] ?> center-block" file="<?= $codeFileData[2] ?>" filetype="<?= $codeFileData[1] ?>" <?=(($userid == $assigned_head || $code_status == $row['id'] || $codeFileData[0] != "")? '' :'disabled')?> style=" margin-right: 20%; <?= ($codeFileData[0] != "") ? 'pointer-events: auto' : '';?>"><b>View <?= $codeFileData[0] ?></b></button>
                                                </div>
                                                
                                            </div>
                                    <? } ?>
                                </div>
                        <?php } }else{ ?>
                                <div class="col-sm-12"  <?= ($code_status != $row['id']) ? "style='pointer-events: none; padding-right: 0px'" : ""; ?>> 
                                    <br>
                                    <span class="col-sm-3"><b>REMARKS:</b></span>
                                    <textarea type="text" rows="5"  class="col-sm-8 form-control" id="endorsementRemarks"  style="width: 70%; resize: none" ><?=$endorsementRemarks?></textarea>
                                    <div class="col-md-12" style="margin-top: 1%">
                                            <div class="col-md-3 col-md-offset-8">
                                                <label style="float: left"><b>Endorsement Status:</b></label><br>
                                                <select class="chosen endorsementStat" id="endorsementStat">
                                                    <option value="">SELECT ENDORSEMENT STATUS</option>
                                                    <option value="ACCEPTED" <?= ($endorsementStat == "ACCEPTED") ? "selected" : "" ?>>ACCEPTED</option>
                                                    <option value="WAITLISTED" <?= ($endorsementStat == "WAITLISTED") ? "selected" : "" ?>>WAITLISTED</option>
                                                    <option value="DENIED" <?= ($endorsementStat == "DENIED") ? "selected" : "" ?>>DENIED</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1" style="padding-right: 0px;">
                                                <label style="float: left"><b>&nbsp;</b></label><br>
                                                <button class="btn btn-success" id="save_status_to_admin" code_status="<?=$row['id']?>" style="display:inline;" <?=(($code_status != $row['id'])? 'disabled' :'')?> >Endorse</button>&nbsp;&nbsp;&nbsp;
                                            </div>
                                    </div>
                                </div>
                        <?php } ?>
                            

                        <div class="col-md-12 <?= $row['id'] ?>"><br><br>
                            <?php if ($row['foremail'] == 1 && (!$row['islaststep'] && $lastSequnce != $row["id"])): ?>
                                <div class="align_left"><button class="btn btn-info send_email" code_stat_id="<?= $row['id'] ?>">Send Email To Employee</button></div>
                            <?php endif ?>
                            <?php if(!$row['islaststep'] && $lastSequnce != $row["id"]){ ?>
                                <div class="align_right">
                                        <div class="col-md-12" <?= ($code_status != $row['id']) ? "style='pointer-events: none; padding-right: 0px'" : "style='padding-right: 0px'"; ?>>
                                            <div class="col-md-2 col-md-offset-4">
                                                <label style="float: left"><b>Status:</b></label><br>
                                                <select class="chosen app_stat" style="display:inline;width: 25%;" <?=($app_stat? 'disabled' :'')?>>
                                                    <option> - Select a status - </option>
                                                    <option value="PENDING" <?= ($app_stat == "PENDING") ? "selected" : "" ?> >  PROCESSING  </option>
                                                    <option value="ONPROCESS" <?= ($app_stat == "ONPROCESS") ? "selected" : "" ?> >  WAITLISTED  </option>
                                                    <option value="APPROVED" <?= ($app_stat == "APPROVED") ? "selected" : "" ?> >  RECOMMENDED FOR NEXT STEP </option>
                                                     <option value="NOT RECOMMENDED" <?= ($app_stat == "NOT RECOMMENDED") ? "selected" : "" ?> >  NOT RECOMMENDED  </option>
                                                </select>&nbsp;&nbsp;&nbsp;
                                            </div>
                                            <div class="col-md-6" style="padding-right: 0px;">
                                                <label style="float: left"><b>Endorsed To:</b></label><br>
                                                <select class="chosen employeeid" <?=($assigned_head? 'disabled' :'')?>>
                                                    <!-- <?=$this->employee->loadallofficeheadempid($assigned_head)?> -->
                                                    <?=$this->employee->loadApplicantApproverList($assigned_head, $row["approver_list"])?>
                                                </select>&nbsp;&nbsp;&nbsp;
                                            </div>
                                        </div>
                                        <button class="btn btn-success save_status" <?=(($code_status != $row['id'])? 'disabled' :'')?> code_stat_id="<?= $row['id'] ?>" style="display:inline;">Save</button>&nbsp;&nbsp;&nbsp;
                                        <a tbl_id="<?= $row['id'] ?>" applicantid = "<?= $applicantId ?>" class="view_status_history" style="text-decoration: none;display:inline;color:blue;cursor: pointer; pointer-events: unset !important;"><b>View Status History</b></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php }else{ ?>
                <input type="hidden" id="rowid" value="<?= $row['id'] ?>">
                <input type="hidden" id="seqno_" value="<?= $row['seqno'] ?>">
                <?php if($row['isrequirements']){ ?>
                    <?php
                        if($code_status != $row['id']){
                            ?>
                                <style type="text/css">
                                    .initial_requirement a.upload_requirements, .initial_requirement a.delete_docs{
                                        pointer-events: none;
                                    }
                                </style>
                            <?php
                        }
                    ?>
                    <div class="initial_requirement"></div>
                    <input type="hidden" id="rowid_ini" value="<?= $row['id'] ?>">
                <?php }else{ ?>
                    <?php
                        if($code_status != $row['id']){
                            ?>
                                <style type="text/css">
                                    .pre_requirement a.upload_requirements, .pre_requirement a.delete_docs{
                                        pointer-events: none;
                                    }
                                </style>
                            <?php
                        }
                    ?>
                    <div class="pre_requirement"></div>
                    <input type="hidden" id="rowid_pre" value="<?= $row['id'] ?>">
                     
                <?php } ?>
                
            <? } ?>
        <?php endforeach ?>
    </div>
</div>
<input type="hidden" id="positionid" value="<?= $positionid ?>">
<div class="modal fade" id="historyModal" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">

        <div class="modal-content">
            <div class="modal-header">
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
                        <h4 class="media-heading"  style="font-size: 18px !important"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family:Avenir; margin-top: -1%; font-size: 16px !important; font-weight: 300 !important">D`Great</p>
                    </div>
                </div>
                <center><b><h3 tag="title" class="modal-title">Modal Header</h3></b></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="display">

                    </div>
                </div>
            </div>
            <div class="modal-footer" id="footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
            </div>
        </div>

    </div>
</div>
<input type="hidden" id="appID" value="<?= $applicantId ?>">
<?=form_open_multipart(base_url(),"id='mainform'");?>
<?=form_hidden("sitename","","")?>
<?=form_hidden("rootid","","")?>
<?=form_hidden("menuid","","")?>
<?=form_hidden("titlebar","","")?>
<?=form_close();?>
<script>
    validateIsEmployee();
    loadInitialRequirementTab();
    loadPreEmploymentRequirementTab();
    $("#acceptApplicant").click(function(){

    var formdata  = $("#PIFORM").serialize();
    formcheck("#PIFORM");
        var ans = confirm("Are you sure you want to accept this applicant as Employee, You will not able to undo this action.");
        id = '<?= $applicantId?>';
        datehired = $('#datehired').val();
        if(ans){
                $.ajax({
                url: "<?= site_url('applicant/saveApplicantToEmployee') ?>",
                type: "POST",
                data: {formdata,id:id,datehired:datehired},
                success:function(response){
                    if (response == "Duplicate ID") {
                        alert(response);
                    }{
                        alert("Applicant has been successfully accepted. "+response+" employeeid has been assigned to this applicant. You can now view this applicant on Employee List");
                          $("#mainform").attr("action","<?=site_url("main/site")?>");
                          $("input[name='sitename']").val("applicant/applicant_list");
                          $("input[name='rootid']").val("2");
                          $("input[name='menuid']").val("157");
                          $("input[name='titlebar']").val("Applicant List");
                          $("#mainform").submit();
                    }

                }
            });
        }
    });

    $(".view_status_history").click(function(){
        $("#historyModal").find(".modal-title").text("Application Status");
        $.ajax({
            url: "<?= site_url('applicant/applicationStatusHistory') ?>",
            type: "POST",
            data: {id:$(this).attr("tbl_id"), applicantid: $(this).attr("applicantid")},
            success:function(response){
                $("#historyModal").find("#display").html(response);
                $("#historyModal").find("#footer").html("<button type='button' data-dismiss='modal' class='btn btn-danger'>Close</button>");
                 $("#historyModal").modal("toggle");

            }
        });
    });

    $(".save_status").click(function(){
        var divclass = $(this).attr("code_stat_id");
        var app_categ_list = "";
        var file = "";
        var filename = "";
        var filedata = "";
        var filetype = "";
        $("."+divclass+" input").each(function(){
            if ($(this).attr("type") == "file") {
                if($(this).val()) file = $(this)[0].files[0];
                if($(this).attr("file")) filedata = $(this).attr("file");
                if($(this).attr("filetype")) filetype = $(this).attr("filetype");
                if($(this).attr("filename")) filename = $(this).attr("filename");
                app_categ_list += "~" + filename + "|" + filetype +"|"+ filedata;
            }else{
                if($(this).val()) app_categ_list += "~" + $(this).val();
            }

        });

        app_categ_list = app_categ_list.substr(1);

        var app_stat = $("."+divclass).find(".app_stat").val();
        var employeeid = $("."+divclass).find(".employeeid").val();
        if(!app_stat){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Applicant status is required.',
                showConfirmButton: true,
                timer: 1000
            })
        }else if(!employeeid){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Endorsement is required.',
                showConfirmButton: true,
                timer: 1000
            })
        }else{
            var fd = new FormData();
                fd.append('file', file);
                fd.append('applicantid', "<?=$applicantId?>");
                fd.append('app_categ_list', app_categ_list);
                fd.append('app_stat', app_stat);
                fd.append('assigned_head', employeeid);
                fd.append('code_status', divclass);
                // fd.append('filedata', filedata);
                // fd.append('filetype', filetype);
                // fd.append('filename', filename);

            $.ajax({
                url: "<?= site_url('applicant/saveApplicantStatus') ?>",
                type: "POST",
                data: fd,
                dataType: "JSON",
                processData: false,  // tell jQuery not to process the data
                contentType: false,  // tell jQuery not to set contentType
                success:function(response){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Application status has been saved successfully.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                    updateLastApplicantStatus();
                    location.reload();
                }
            });
        }
            
    });

    $(".send_email").click(function(){
        var divclass = $(this).attr("code_stat_id");
        var app_categ_list = "";
        $("."+divclass+" input").each(function(){
            if($(this).val()) app_categ_list += "/" + $(this).val();
        });
        app_categ_list = app_categ_list.substr(1);
        var formdata = {
            applicantid : "<?=$applicantId?>",
            app_categ_list : app_categ_list,
            code_status : divclass
        };

        $.ajax({
            url: "<?= site_url('applicant/SendMail') ?>",
            type: "POST",
            data: formdata,
            dataType: "JSON",
            success:function(response){
                alert(response.msg);
            }
        });
    });

    $('.time').datetimepicker({
        format: 'LT'
    });

    $('.date').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('.chosen').chosen();

    function loadInitialRequirementTab(){
        if($(".initial_requirement")[0]){
            var formdata = {
                lname: $("#lname").val(),
                fname: $("#fname").val(),
                mname: $("#mname").val(),
                positionid : $("#positionid").val(),
                code_status : $("#code_status").val(),
                rowid : $("#rowid_ini").val(),
                applicantid : $("#appID").val()
            };

            $.ajax({
                url: "<?= site_url('applicant/loadInitialRequirementTab') ?>",
                type: "POST",
                data: formdata,
                success:function(response){
                    $(".initial_requirement").html(response);
                }
            });
        }
    }

    function loadPreEmploymentRequirementTab(){
        if($(".pre_requirement")[0]){
            var formdata = {
                lname: $("#lname").val(),
                fname: $("#fname").val(),
                mname: $("#mname").val(),
                positionid : $("#positionid").val(),
                code_status : $("#code_status").val(),
                rowid : $("#rowid_pre").val(),
                applicantid : $("#appID").val()
            };
            $.ajax({
                url: "<?= site_url('applicant/loadPreEmploymentRequirementTab') ?>",
                type: "POST",
                data: formdata,
                success:function(response){
                    $(".pre_requirement").html(response);
                }
            });
        }
    }

    function validateIsEmployee(){
        $.ajax({
            url: "<?= site_url('applicant/validateIsEmployee') ?>",
            type: "POST",
            data: {applicantId: "<?= $applicantId?>"},
            success:function(response){
                if(response == 1){
                    $("#acceptApplicant").prop("disabled", true);
                    $("#datehired").prop("disabled", true);
                    $("#isemployee").show();
                }
            }
        });
    }


    $("#redtags").click(function(){
        var appID = $("#appID").val();
        $("#remarkNow").removeAttr("hidden");
        $("#hideBtn").css("display", "none");
        // $.ajax({
        //     url: "<?= site_url('applicant/tagasredflag') ?>",
        //     type: "POST",
        //     data: {applicantId: appID},
        //     success:function(response){
        //         if(response == 1){
        //             Swal.fire({
        //                     icon: 'success',
        //                     title: 'Success!',
        //                     text: 'Application has been tagged as red flag.',
        //                     showConfirmButton: true,
        //                     timer: 1000
        //                 })
                    
                    
        //         }
        //     }
        // })
    });

    $("#redtag").click(function(){
        var remarkValue = $("#rtRemarks").val();
        if(remarkValue == ''){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Red Flag Remarks is required',
                showConfirmButton: true,
                timer: 1000
            })
        }else{
            var appID = $("#appID").val();
            $.ajax({
                url: "<?= site_url('applicant/redFlagRemarks') ?>",
                type: "POST",
                data: {applicantId: appID, remark:remarkValue},
                success:function(response){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Red Flag Remarks has been saved successfully',
                        showConfirmButton: true,
                        timer: 1000
                    })
                    $("#rtRemarks").attr("readonly", " ");
                    $("#redtag").removeClass().addClass("btn btn-danger redtag").css("pointer-events", "none").text("Tagged as Red Flag");
                }
            })
        }
    })

    $(".rtRemarks").on("change, blur", function() {
        var remarkValue = $(this).val();
        var appID = $("#appID").val();
        $.ajax({
            url: "<?= site_url('applicant/redFlagRemarks') ?>",
            type: "POST",
            data: {applicantId: appID, remark:remarkValue},
            success:function(response){

            }
        })
    })

    $(".app_stat").change(function(){
        var div_id = $(this).closest("div");
        $(div_id).find(".employeeid").css("display", "inline");
    });

    $("input[type='file']").change(function() {
        file = $(this)[0].files[0];
        $(this).attr("filename", file.name);
        $(this).attr("filetype", file.type);
        var input = $(this);
        var viewinfoid = $(this).attr("name");
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function () {
            holder = reader.result.split(',')[1];
            input.attr("file", holder);
            $(".viewInfo_"+viewinfoid).attr("file", holder);
            $(".viewInfo_"+viewinfoid).attr("filetype", file.type);
            $(".viewInfo_"+viewinfoid).html("View "+ file.name);
        };
    });

    $(".viewInfo").click(function(){
    var data = $(this).attr("file");
    var type = $(this).attr("filetype");
    if (type == "application/pdf") {
        objectURL = URL.createObjectURL(b64toBlob(data, type)) + '#toolbar=0&navpanes=0&scrollbar=0';
    }else{
        objectURL = URL.createObjectURL(b64toBlob(data, type));
    }

    window.open(objectURL);
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
  });

  function updateLastApplicantStatus(){
    $.ajax({
        url: "<?=site_url('applicant/updateLastApplicantStatus')?>",
        type:"POSt",
        data:{code_status:"<?=$last_status?>"},
        success:function(response){

        }
    })
  }

  $("#save_status_to_admin").click(function(){
    var endorsementStat = $("#endorsementStat").val();
    var applicantid = $("#appID").val();
    var endorsementRemarks = $("#endorsementRemarks").val();
    var code_status = $(this).attr("code_status");
    if(!$("#endorsementStat").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Endorsement Status is required',
            showConfirmButton: true,
            timer: 1000
        })
    }else{
        $.ajax({
            url: "<?= site_url('applicant/endorseToAdmin') ?>",
            type: "POST",
            data: {applicantid: applicantid, remarks:endorsementRemarks, status:endorsementStat, code_status:code_status},
            success:function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Applicant has been endorsed successfully!',
                    showConfirmButton: true,
                    timer: 1000
                })
                location.reload();
            }
        })
    }
    
  })

    // function formcheck()
    // {
    //   var fields = $(".ss-item-required").find("select, textarea, input").serializeArray();
    //   $.each(fields, function(i, field)
    //   {
    //     if (!field.value)
    //       alert(field.name + ' is required');
    //    });
    //   console.log(fields);
    // }

</script>
