<?php
    $CI =& get_instance();
    $CI->load->model('applicantt');
    $app_stat  = $code_status = $last_status = $submittedApplication =  $isArchive = $approver_list = '';
    $userid = $this->session->userdata("username");
    $usertype = $this->session->userdata("usertype");
    if(isset($applicantId)){
        $isArchive = $CI->applicantt->checkArchivedStatus($applicantId);
        $submittedApplication = $CI->applicantt->checkApplication($applicantId);
        if($usertype == "ADMIN"){
            $current_status = $CI->applicantt->getLatestStatus($applicantId);
        }
    }

    if(isset($applicantid)){
        $applicant_current_status = $CI->applicantt->getApplicantSequence($applicantid, $rowid);
        $app_stat = $CI->applicantt->getApplicantSequenceStatus($applicantid, $rowid);
        $assigned_head = $CI->applicantt->getApplicantSequenceApprover($applicantid, $rowid);
        $apprv_list         = $CI->applicantt->getApprovalStatusSetup(true);
        $doc_list           = $CI->applicantt->getDocumentSetup(true);
        $doc_list_submitted = $CI->applicantt->getApplicantDocumentSubmitted(true,$applicantid);
        $applicant_name = $CI->applicantt->getApplicantName($applicantid);
        $status = $CI->applicantt->getApplicantStatus($applicantid);
        if($usertype == "ADMIN"){
            $current_status = $CI->applicantt->getLatestStatus($applicantid);
        }
    }

    if(isset($code_status)){
        $last_status = $code_status;
        $code_status = $CI->applicantt->getNextApplicantStatus($code_status);
        $applicant_approval_setup = $CI->applicantt->getApplicantSetup($positionid, $code_status);
    }else{
        $applicant_approval_setup = $CI->applicantt->getApplicantSetup($positionid);
    }


    $approver_list = isset($applicant_approval_setup[0]['approver_list']) ? $applicant_approval_setup[0]['approver_list'] : '';

?>
<div class="widgets_area">
    <div class="row">
        <div class="col-md-12">
            <div class="well-content no-search" style="border: 0 !important;">
                <br>
                <div class="panel animated fadeIn">
                     <div class="panel-heading" id="inireq"><h4><b><?= (isset($desc) && isset($step)) ? $desc.'&nbsp;&nbsp; - &nbsp;&nbsp; Step: '.$step : 'Initial Requirements' ; ?></b></h4></div>
                     <div class="panel-body">
                      <div id="msg_header" style="display:none;">
                	    <strong></strong> <span></span>
                	  </div>
                      <div class="col-md-12" align="right">
                        <table class="table table-hover" id="ini_requirements"></table>
                        <?php
                            if(isset($applicantid)){
                                ?>
                                    <div class="align_right">
                                         <div class="col-md-12 <?= $rowid ?>" style="padding-right: 0px;  <?= ($assigned_head && $usertype != "ADMIN" || (isset($forview) && $forview == "viewing")) ? 'pointer-events: none' : ''; ?> ">
                                            <div class="col-md-2 col-md-offset-4">
                                                <label style="float: left"><b>Status:</b></label><br>
                                                <select class="chosen app_stat" style="display:inline;width: 25%;" <?=($app_stat || (isset($forview) && $forview == "viewing")? (isset($current_status) && $current_status == $rowid ? '' : 'disabled') :'')?>>
                                                    <option value=""> - Select a status - </option>
                                                    <option value="PENDING" <?= ($app_stat == "PENDING") ? "selected" : "" ?> >  PROCESSING  </option>
                                                    <option value="ONPROCESS" <?= ($app_stat == "ONPROCESS") ? "selected" : "" ?> >  WAITLISTED  </option>
                                                    <option value="APPROVED" <?= ($app_stat == "APPROVED") ? "selected" : "" ?> >  RECOMMENDED FOR NEXT STEP </option>
                                                    <option value="NOT RECOMMENDED" <?= ($app_stat == "NOT RECOMMENDED") ? "selected" : "" ?> >  NOT RECOMMENDED  </option>
                                                </select>&nbsp;&nbsp;&nbsp;
                                            </div>
                                            <div class="col-md-6" style="padding-right: 0px;">
                                                <label style="float: left"><b>Endorsed To:</b></label><br>
                                                <select class="chosen employeeid" <?=($assigned_head || (isset($forview) && $forview == "viewing")? (isset($current_status) && $current_status == $rowid ? '' : 'disabled') :'')?>>
                                                    <!-- <?=$this->employee->loadallofficeheadempid($assigned_head)?> -->
                                                    <?=$this->employee->loadApplicantApproverList($assigned_head, $approver_list)?>
                                                </select>&nbsp;&nbsp;&nbsp;
                                            </div>
                                        </div>

                                        <button class="btn btn-success save_statuss" code_stat_id="<?= $rowid ?>" style="display:inline;"  <?= ($assigned_head && $usertype != "ADMIN" || (isset($forview) && $forview == "viewing")) ? 'disabled' : ''; ?>>Save</button>&nbsp;&nbsp;&nbsp;
                                        <a tbl_id="<?= $rowid ?>" applicantid = "<?= $applicantid ?>" class="view_status_history1" style="text-decoration: none;display:inline;color:blue;cursor: pointer;  pointer-events: unset !important;"><b>View Status History</b></a>
                                    </div>
                                <?php
                            }
                        ?>

                        <?php if($submittedApplication > 0 && $isArchive == 0){
                            if(isset($applicantId)){
                                $current_status = $CI->applicantt->getLatestStatus($applicantId);
                                $code_status = $CI->applicantt->getNextApplicantStatus($current_status);
                                if($code_status){ 
                                    $seqprereq = $CI->applicantt->getprereqseqno($positionid);
                                    $applicant_current_status = $CI->applicantt->getCodeStatusSequence($code_status);
                                    if($seqprereq > $applicant_current_status){
                                    ?>
                                        <div class="col-md-7" style="float: right; pointer-events: none" <?= (isset($applicantid)) ? "hidden" : "" ?>>
                                            <button  class="btn btn-success" id="upload_documents" style="color: #000000;border-color: #0072c6;background-color: #0072c6;font-size: 20px;padding: 15px;border-radius: 15px;margin-right: 9.5%;"><i class="glyphicon glyphicon-send"></i>&nbsp;&nbsp;SUBMITTED APPLICATION</button>
                                        </div>
                                    <?php    
                                    }    
                                }
                            }
                            ?>
                            <?php
                        }else{
                            ?>
                                <div class="col-md-7" style="float: right;" <?= (isset($applicantid)) ? "hidden" : "" ?>>
                                    <button  class="btn btn-success" id="upload_documents" style="color: #000000;border-color: #0072c6;background-color: #0072c6;font-size: 20px;padding: 15px;border-radius: 15px;margin-right: 9.5%;"><i class="glyphicon glyphicon-send"></i>&nbsp;&nbsp;SUBMIT APPLICATION</button>
                                </div>
                            <?php
                        } ?>
                        <br>         
                      </div>
                      <?php 
                            if(isset($applicantId)){
                                $current_status = $CI->applicantt->getLatestStatus($applicantId);
                                $code_status = $CI->applicantt->getNextApplicantStatus($current_status);
                                if($code_status){ 
                                    $seqprereq = $CI->applicantt->getprereqseqno($positionid);
                                    $applicant_current_status = $CI->applicantt->getCodeStatusSequence($code_status);
                                    if($seqprereq > $applicant_current_status){
                                    ?>
                                        <div class="col-md-12">
                                            <label>Application Status:</label>
                                            <div class="field">
                                                <input type="text" class="status_list form-control" readonly="" value="<?=$this->extensions->getApplicantStatusDesc($code_status)?>" style="width: 100%;">
                                            </div>
                                        </div>
                                    <?php    
                                    }    
                                }
                            }
                        ?>
                    </div>
                 </div>
                 <?php 
                    if(isset($applicantId)){ 
                        $current_status = $CI->applicantt->getLatestStatus($applicantId);
                        $code_status = $CI->applicantt->getNextApplicantStatus($current_status);
                        if($code_status){
                            $seqprereq = $CI->applicantt->getprereqseqno($positionid);
                            $applicant_current_status = $CI->applicantt->getCodeStatusSequence($code_status);
                            if($applicant_current_status >= $seqprereq){
                                ?>
                                    <div class="panel animated fadeIn">

                                 <div class="panel-heading" id="prereq"><h4><b><?= (isset($desc) && isset($step)) ? $desc.'&nbsp;&nbsp; - &nbsp;&nbsp; Step: '.$step : 'Pre Requirements' ; ?></b></h4></div>
                                 <div class="panel-body">
                                  <div id="msg_header" style="display:none;">
                                    <strong></strong> <span></span>
                                  </div>
                                  <div class="col-md-12" align="right">
                                    <table class="table table-hover" id="pre_requirements"></table>
                                    <?php
                                        if(isset($applicantid)){
                                            ?>
                                                <div class="align_right" style=" <?= ($assigned_head && $usertype != "ADMIN") ? 'pointer-events: none' : ''; ?>">
                                                     <div class="col-md-12 <?= $rowid ?>" style="padding-right: 0px;  <?= ($assigned_head && $usertype != "ADMIN") ? 'pointer-events: none' : ''; ?> ">
                                                        <div class="col-md-2 col-md-offset-4">
                                                            <label style="float: left"><b>Status:</b></label><br>
                                                            <select class="chosen app_stat" style="display:inline;width: 25%;" <?=($app_stat? 'disabled' :'')?>>
                                                                <option value=""> - Select a status - </option>
                                                                <option value="PENDING" <?= ($app_stat == "PENDING") ? "selected" : "" ?> >  PROCESSING  </option>
                                                                <option value="ONPROCESS" <?= ($app_stat == "ONPROCESS") ? "selected" : "" ?> >  WAITLISTED  </option>
                                                                <option value="APPROVED" <?= ($app_stat == "APPROVED") ? "selected" : "" ?> >  RECOMMENDED FOR NEXT STEP </option>
                                                                <option value="NOT RECOMMENDED" <?= ($app_stat == "NOT RECOMMENDED") ? "selected" : "" ?> >  NOT RECOMMENDED  </option>
                                                            </select>&nbsp;&nbsp;&nbsp;
                                                        </div>
                                                        <div class="col-md-6" style="padding-right: 0px;">
                                                            <label style="float: left"><b>Endorsed To:</b></label><br>
                                                            <select class="chosen employeeid" <?=($assigned_head? 'disabled' :'')?>>
                                                                <?=$this->employee->loadallofficeheadempid($assigned_head)?>
                                                            </select>&nbsp;&nbsp;&nbsp;
                                                        </div>
                                                    </div>

                                                    <button class="btn btn-success save_statuss" code_stat_id="<?= $rowid ?>" style="display:inline;"  <?= ($assigned_head && $usertype != "ADMIN") ? 'disabled' : ''; ?>>Save</button>&nbsp;&nbsp;&nbsp;
                                                    <a tbl_id="<?= $rowid ?>" applicantid = "<?= $applicantid ?>" class="view_status_history1" style="text-decoration: none;display:inline;color:blue;cursor: pointer;"><b>View Status History</b></a>
                                                </div>
                                            <?php
                                        }
                                    ?>
                                    <div class="col-md-7" style="float: right; pointer-events: none" <?= (isset($applicantid)) ? "hidden" : "" ?>>
                                        <button  class="btn btn-success" id="upload_documents" style="color: #000000;border-color: #0072c6;background-color: #0072c6;font-size: 20px;padding: 15px;border-radius: 15px;margin-right: 9.5%;"><i class="glyphicon glyphicon-send"></i>&nbsp;&nbsp;SUBMITTED APPLICATION</button>
                                    </div>
                                  </div>
                                <br>
                                <div class="col-md-12">
                                            <label>Application Status:</label>
                                            <div class="field">
                                                <input type="text" class="status_list form-control" readonly="" value="<?=$this->extensions->getApplicantStatusDesc($code_status)?>" style="width: 100%;">
                                            </div>
                                        </div>
                                </div>
                             </div>
                                <?php
                            }
                        }
                    } 
                ?>
             </div>
         </div>
     </div>
 </div>


<div class="modal fade" id="doc_upload_result" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">

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
                <center><b><h3 tag="title" class="modal-title"  style="font-family: 'Poppins';">Application Success</h3></b></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div tag='display'>
                        <img src="<?=base_url()?>images/documents.png" class="img-circle" width="200" height="200" style="margin-left: 35%;">
                        <div class="align_center" style="padding: 10px; width: 80%; word-wrap: break-word;margin-left: 10%;font-family: 'Poppins'">
                            <h4>Your application has been sent successfully to Pinnacle Technologies Inc.</h3>
                            <p><small>This employer will contact you if you are short-listed. Thank you and good luck in your application! </small></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success ok" data-dismiss="modal" disabled="false">Ok</button>
            </div>
        </div>

    </div>
</div>
<input type="hidden" id="appid" value="<?= isset($applicantid) ? $applicantid : 's'; ?>">
<input type="hidden" id="isSubmitted" value="<?=$submittedApplication?>">
<input type="hidden" id="isArchive" value="<?=$isArchive?>">

 <script>
    var toks = hex_sha512(" ");
    loadInitialRequirements();
    loadPreRequirements();
    function loadInitialRequirements(){
        var appid = $("#appid").val();
        var applicantid = '';

        if(appid == 's'){
            applicantid = $("input[name='applicantId']").val();
        }else{
            applicantid = appid;
        }

        $.ajax({
            url: "<?= site_url('setup_/applicantInitialRequirements') ?>",
            type: "POST",
            data: {applicantid:  GibberishAES.enc(applicantid , toks), app_stat: GibberishAES.enc( "<?= $app_stat ?>" , toks), toks:toks},
            success:function(response){
                $("#ini_requirements").html(response);
                if($("#isSubmitted").val() > 0 && $("#isArchive").val() == 0) $("#ini_requirements .delete_docs,#ini_requirements .upload_requirements, #ini_requirements .add_ini_req").attr("disabled", "true").css("pointer-events", "none");
            }
        });
    }

    function loadPreRequirements(){
        var appid = $("#appid").val();
        var applicantid = '';

        if(appid == 's'){
            applicantid = $("input[name='applicantId']").val();
        }else{
            applicantid = appid;
        }

        $.ajax({
            url: "<?= site_url('setup_/applicantPreRequirements') ?>",
            type: "POST",
            data: {applicantid:  GibberishAES.enc(applicantid , toks), app_stat: GibberishAES.enc( "<?= $app_stat ?>" , toks), toks:toks},
            success:function(response){
                $("#pre_requirements").html(response);
                // if($("#isSubmitted").val() > 0 && $("#isArchive").val() == 0) $("#pre_requirements .delete_docs,#pre_requirements .upload_requirements").attr("disabled", "true").css("pointer-events", "none");
            }
        });
    }

    $(".view_status_history1").click(function(){
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

    $(".ok").click(function(){
        location.reload();
    });

    $(".save_statuss").click(function(){
        var divclass = $(this).attr("code_stat_id");
        var app_categ_list = "";
        var filename = "";
        var filedata = "";
        var filetype = "";
        $("."+divclass+" input").each(function(){
            if ($(this).attr("type") == "file") {
                filedata = $(this).attr("file");
                filetype = $(this).attr("filetype");
                filename = $(this).attr("filename");
                app_categ_list += "~" + filename + "|" + filetype +"|"+ filedata;
            }else{
                if($(this).val()) app_categ_list += "~" + $(this).val();
            }
        });
        app_categ_list = app_categ_list.substr(1);

        var app_stat = $("."+divclass).find(".app_stat").val();
        var employeeid = $("."+divclass).find(".employeeid").val();
        var app_id = $('input[name=applicantId]').val();
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
            if(typeof app_id === "undefined") app_id = $("#appid").val();
            var fd = new FormData();
                fd.append('applicantid',  GibberishAES.enc(app_id , toks));
                fd.append('app_categ_list',  GibberishAES.enc( app_categ_list, toks));
                fd.append('app_stat',  GibberishAES.enc( app_stat, toks));
                fd.append('assigned_head',  GibberishAES.enc( employeeid, toks));
                fd.append('code_status',  GibberishAES.enc(divclass , toks));
                fd.append('toks', toks);
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
                }
            });
        }
            
    });

    function updateLastApplicantStatus(){
      $.ajax({
          url: "<?=site_url('applicant/updateLastApplicantStatus')?>",
          type:"POSt",
          data:{code_status: GibberishAES.enc("<?=$last_status?>" , toks), toks:toks},
          success:function(response){

          }
      })
    }
$(".chosen").chosen();
 </script>
