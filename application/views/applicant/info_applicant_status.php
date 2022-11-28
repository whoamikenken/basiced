<?php
 $usertype = $this->session->userdata("usertype");
$CI =& get_instance();
$CI->load->model('applicantt');
$apprv_list         = $CI->applicantt->getApprovalStatusSetup(true);
$doc_list           = $CI->applicantt->getDocumentSetup(true);
$doc_list_submitted = $CI->applicantt->getApplicantDocumentSubmitted(true,$applicantId);
$applicant_name = $CI->applicantt->getApplicantName($applicantId);
$status = $CI->applicantt->getApplicantStatus($applicantId);
$applicant_approval_setup = $CI->applicantt->getApplicantSetup($positionid);
$lastSequnce = $CI->applicantt->getLastSequence('',$positionid);
$isendorsed = $CI->applicantt->checkApplicationEndorsement($applicantId);
$endorsementRemarks = $endorsementStat = $endorsedBy = $endorsedOn = "";
if($isendorsed->num_rows() > 0){
    $endorsementRemarks = $isendorsed->row()->remarks;
    $endorsementStat = $isendorsed->row()->status;
    $endorsedBy = $this->extensions->getEmployeeName($isendorsed->row()->endorsed_by);
    $endorsedOn = date('F d, Y', strtotime($isendorsed->row()->endorsement_date));
}
$current_status = $CI->applicantt->getLatestStatus($applicantId);
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
.chosen-container.chosen-container-single {
    text-align: left;
}

.glyphicon.fast-right-spinner {
    -webkit-animation: glyphicon-spin-r 1s infinite linear;
    animation: glyphicon-spin-r 1s infinite linear;
}

@-webkit-keyframes glyphicon-spin-r {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(359deg);
        transform: rotate(359deg);
    }
}

@keyframes glyphicon-spin-r {
    0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }

    100% {
        -webkit-transform: rotate(359deg);
        transform: rotate(359deg);
    }
}

@-webkit-keyframes glyphicon-spin-l {
    0% {
        -webkit-transform: rotate(359deg);
        transform: rotate(359deg);
    }

    100% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }
}

@keyframes glyphicon-spin-l {
    0% {
        -webkit-transform: rotate(359deg);
        transform: rotate(359deg);
    }

    100% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
    }
}
</style>
<input type="hidden" id="code_status" value="<?= isset($code_status) ? $code_status : ''; ?>">
<div class="widgets_area" >

    <div  class="row">
        <div>
        <?php
            if($redtag == 0){
                ?>
                    <div class="col-md-12" id="remarkNow" style="margin-top: 1%; margin-bottom: .5%; padding: 0px;" hidden>
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
                    <div  class="col-md-12" style="margin-top: 1%; padding: 0px;" id="hideBtn">
                        <div>
                            <button class="btn btn-primary redtag" id="redtags" style="float: right; margin-right: 21px;">Tag as Red Flag</button>
                        </div>
                    </div>
                <?php
            }else{
                ?>  
                    <div class="col-md-12" style=" padding: 0px;">
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
            <div class="col-md-12" style="margin-top: 1%; padding: 0px;">
                <div class="col-md-9">
                    <div class="col-sm-3">
                    </div>
                    <div class="col-sm-9">
                    </div>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-info shareBtn" id="shareBtn" href="#share-view" data-toggle="modal" app_id = "<?=$applicantId?>"  style=" float: right; margin-right: 6px;">Share Application For Viewing</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <?php foreach($applicant_approval_setup as $row): ?>

            <?php if($row["isrequirements"] != 1 && $row["isprerequirements"] != 1){ ?> 
                <div class="widgets_area">
                <div class="panel">
                    <div class="panel-heading"><h4><b><?= $row["description"] ?> &nbsp;&nbsp; - &nbsp;&nbsp; Step: <?= $row["seqno"] ?></b></h4></div>
                    <div class="panel-body">
                        <?php
                            $applicant_current_status = $CI->applicantt->getApplicantSequence($applicantId, $row["id"]);
                        ?>
                       
                        <?php 
                        if(!$row["islaststep"] && $lastSequnce != $row["id"]){ 
                            foreach($applicant_current_status["record"] as $key=>$value){ ?>
                             
                                <div class="col-sm-12 <?= $row['id'] ?>">
                                    <br>
                                    <span class="col-sm-3"><b><?= key($value) ?> :</b></span>
                                    <?php if (key($value) == "DATE") { ?>
                                        <div class='input-group date' style="width: 70%;">
                                            <input type='text' class="form-control" name="<?= $row['id'] ?>" value="<?= $value[key($value)] ?>" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    <? }elseif (key($value) == "TIME") {?>
                                        <div class='input-group time' style="width: 70%;">
                                            <input type='text' class="form-control" name="<?= $row['id'] ?>" value="<?= $value[key($value)] ?>"/>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
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
                                                <div class="col-md-4" style="padding: 0px; float: right;">
                                                   <!--  <p style="display: none;"></p>
                                                    <img id="view" src=""> </img> -->
                                                    <button class="btn btn-primary viewInfo viewInfo_<?= $row['id'] ?> center-block" file="<?= $codeFileData[2] ?>" filetype="<?= $codeFileData[1] ?>" style=" margin-right: 20%; float: none "><b>View <?= $codeFileData[0] ?></b></button>
                                                </div>
                                                
                                            </div>
                                    <? }else{ ?>
                                        <textarea class="col-sm-8 form-control" rows="5" name="<?= $row['id'] ?>" value="<?= $value[key($value)] ?>" style="width: 70%;" ><?= $value[key($value)] ?></textarea>
                                   <?php } ?>
                                </div>
                        <?php } }else{ ?>
                            <div class="col-sm-12" <?=($endorsementRemarks ? 'style="pointer-events:none"' : '')?>>
                                    <br>
                                    <span class="col-sm-3"><b>REMARKS:</b></span>
                                    <textarea type="text" rows="5"  class="col-sm-8 form-control" id="endorsementRemarks"  style="width: 70%; resize: none" ><?=$endorsementRemarks?></textarea>
                                    <div class="col-md-12" style="margin-top: 1%">
                                            <div class="col-md-3 col-md-offset-5">
                                                <label style="float: left"><b>Endorsement Status:</b></label><br>
                                                <select class="chosen endorsementStat" id="endorsementStat">
                                                    <option>SELECT ENDORSEMENT STATUS</option>
                                                    <option value="ACCEPTED" <?= ($endorsementStat == "ACCEPTED") ? "selected" : "" ?>>ACCEPTED</option>
                                                    <option value="WAITLISTED" <?= ($endorsementStat == "WAITLISTED") ? "selected" : "" ?>>WAITLISTED</option>
                                                    <option value="DENIED" <?= ($endorsementStat == "DENIED") ? "selected" : "" ?>>DENIED</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4" style="padding-right: 0px;">
                                                <label><b>Endorsed By: <?=$endorsedBy?></b></label><br>
                                                <label style="float: left"><b>Endorsed On: <?=$endorsedOn?></b></label><br>
                                            </div>
                                    </div>
                                </div>
                        <?php 
                        }
                        $app_stat = $CI->applicantt->getApplicantSequenceStatus($applicantId, $row["id"]);
                        $head = $CI->applicantt->getApplicantSequenceApprover($applicantId, $row["id"]);
                        ?>
                        <div class="col-md-12 <?= $row['id'] ?>"><br><br>
                            <?php if ($row['foremail'] == 1  && (!$row['islaststep'] && $lastSequnce != $row["id"])): ?>
                                <div class="align_left"><button class="btn btn-info send_email" code_stat_id="<?= $row['id'] ?>">Send Email To Employee</button></div>
                            <?php endif ?>
                             <?php if(!$row['islaststep'] && $lastSequnce != $row["id"]){ ?>
                            <div class="align_right">
                                    <div class="col-md-12" style="padding-right: 0px;">
                                        <div class="col-md-2 col-md-offset-4">
                                            <label style="float: left"><b>Status:</b></label><br>
                                            <select class="chosen app_stat" style="display:inline;width: 25%;" <?=($app_stat && $current_status != $row["id"] ? 'disabled' :'')?>>
                                                <option value=""> - Select a status - </option>
                                                <option value="PENDING" <?= ($app_stat == "PENDING") ? "selected" : "" ?> >  PROCESSING  </option>
                                                <option value="ONPROCESS" <?= ($app_stat == "ONPROCESS") ? "selected" : "" ?> >  WAITLISTED  </option>
                                                <option value="APPROVED" <?= ($app_stat == "APPROVED") ? "selected" : "" ?> >  RECOMMENDED FOR NEXT STEP </option>
                                                 <option value="NOT RECOMMENDED" <?= ($app_stat == "NOT RECOMMENDED") ? "selected" : "" ?> >  NOT RECOMMENDED  </option>
                                            </select>&nbsp;&nbsp;&nbsp;
                                        </div>
                                        <div class="col-md-6" style="padding-right: 0px;">
                                            <label style="float: left"><b>Endorsed To:</b></label><br>
                                            <select class="chosen employeeid" <?=($head && $current_status != $row["id"] ? 'disabled' :'')?>>
                                                <?=$this->employee->loadApplicantApproverList($head, $row["approver_list"])?>
                                            </select>&nbsp;&nbsp;&nbsp;
                                        </div>
                                    </div>
                                    <button class="btn btn-success save_status" code_stat_id="<?= $row['id'] ?>" style="display:inline;">Save</button>&nbsp;&nbsp;&nbsp;
                                    <a tbl_id="<?= $row['id'] ?>" applicantid = "<?= $applicantId ?>" class="view_status_history" style="text-decoration: none;display:inline;color:blue;cursor: pointer;"><b>View Status History</b></a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                </div>
            <?php }else{ ?>
                <?php if($row['isrequirements']){ ?>
                    <div class="initial_requirement"></div>
                    <input type="hidden" id="inidesc" value="<?= $row['description'] ?>">
                    <input type="hidden" id="inistep" value="<?= $row['seqno'] ?>">
                    <input type="hidden" id="rowid_inireq" value="<?= $row['id'] ?>">
                <?php }else{ ?>
                    <div class="pre_requirement"></div>
                    <input type="hidden" id="predesc" value="<?= $row['description'] ?>">
                    <input type="hidden" id="prestep" value="<?= $row['seqno'] ?>">
                    <input type="hidden" id="rowid_prereq" value="<?= $row['id'] ?>">
                <?php } ?>
            <? } ?>
        <?php endforeach ?>
        <input type="hidden" id="positionid" value="<?= $positionid ?>">
        <div>
            <div class="col-md-12">
                <table>
                    <th style="width: 40%; text-align: right;"><b>Date Hired:&emsp;</b></th>
                    <th style="width: 30%;">
                        <div class='input-group date'>
                                      <input type='text' class="form-control" name="datehired" id="datehired" />
                                      <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                  </div>
                    </th>
                    <th style="width: 5%;"></th>
                    <th>&emsp;<button class="btn btn-success" id="acceptApplicant" style="color: #000000;border-color: #0072c6;background-color: #0072c6;font-size: 22px;padding: 20px;border-radius: 15px;"><i class="glyphicon glyphicon-send"></i>&nbsp;&nbsp;<B>ACCEPT APPLICANT AS AN EMPLOYEE</B></button></th>
                </table>
            <!-- <div class="col-md-12" style="padding-right: 0px; text-align: right"> 
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="col-md-12">
                            <label  for="employeeid" class=" align_right">Date Hired:</label>
                            <div>
                               
                            </div> 
                            
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    
                </div>
            </div> -->
            </div>
        </div>
        <div class="col-md-12"><h3 id="isemployee" style="color:red;display: none;">THIS APPLICANT IS ALREADY HIRED AS EMPLOYEE</h3></div>
    </div>
</div>

<div class="modal fade" id="historyModal" role="dialog" data-backdrop="static">
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
                <center><b><h3 tag="title" class="modal-title">Modal Header</h3></b></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div id="display">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="share-view" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">

        <div class="modal-content" >
            <div class="modal-header" >
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
                        <h4 class="media-heading"  style="font-size: 18px !important"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family:Avenir; margin-top: -1%; font-size: 16px !important; font-weight: 300 !important">D`Great</p>
                    </div>
                </div>
                <center><b><h3 tag="title" class="modal-title" style="font-family: Avenir;">Modal Header</h3></b></center>
            </div>
            <div class="modal-body" style="background-color: white !important">
                <div class="row">
                    <div tag='display'>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger modalcloseshare" id="modalcloseshare">Close</button>
                <button type="button" class="btn btn-success button_save_modal" id='button_save_modalshare'>Save changes</button>
            </div>
        </div>

    </div>
</div>    
<input type="hidden" id="appID" value="<?= $applicantId ?>">
<input type="hidden" id="lname" value="<?= $lname ?>">
<input type="hidden" id="mname" value="<?= $mname ?>">
<input type="hidden" id="fname" value="<?= $fname ?>">
<input type="hidden" id="email" value="<?= $cur_email ?>">
<input type="hidden" id="redtag_" value="<?= $redtag ?>">
<input type="hidden" id="redTagRemarks" value="<?= $remarks ?>">
<?=form_open_multipart(base_url(),"id='mainform'");?>
<?=form_hidden("sitename","","")?>
<?=form_hidden("rootid","","")?>
<?=form_hidden("menuid","","")?>
<?=form_hidden("titlebar","","")?>
<?=form_close();?>
<script>
    var toks = hex_sha512(" ");
    validateIsEmployee();
    loadInitialRequirementTab();
    loadPreEmploymentRequirementTab();
    $("#acceptApplicant").click(function(){
    var utype = $("#user_type").val();
    var formdata  = $("#PIFORM").serialize();
    if(utype != "ADMIN"){
        formcheck("#PIFORM");
    }
        // var ans = confirm("Are you sure you want to accept this applicant as Employee, You will not able to undo this action.");
        id = '<?= $applicantId?>';
        datehired = $('#datehired').val();
        if(!datehired){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Date hired is required.',
                showConfirmButton: true,
                timer: 1000
            })
            return;
        }

        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "You want to accept this applicant as an employee, You will not able to undo this action.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, proceed!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var counter = 0;
            var current = '';
            $(this).css("pointer-events", "none");
            $(this).html('<i class="glyphicon glyphicon-refresh fast-right-spinner"></i>&nbsp;&nbsp;<B id="acceptigntext">ACCEPTING APPLICANT AS EMPLOYEE.</B>&nbsp;&nbsp;');
            var text = ["ACCEPTING APPLICANT AS EMPLOYEE..", "ACCEPTING APPLICANT AS EMPLOYEE...", "ACCEPTING APPLICANT AS EMPLOYEE."];
            var space = ["&nbsp;", "", "&nbsp;&nbsp;"];
            var checksession = setInterval(function(){ 
                $("#acceptigntext").text(text[counter]).append(space[counter]);
                counter++;
                if (counter >= text.length) {
                    counter = 0;
                }
            }, 5000);
            
                $.ajax({
                    url: "<?= site_url('applicant/saveApplicantToEmployee') ?>",
                    type: "POST",
                    data: {formdata:GibberishAES.enc( formdata, toks),id: GibberishAES.enc(id , toks),datehired: GibberishAES.enc(datehired , toks),toks:toks},
                    success:function(response){
                        if (response == "Duplicate ID") {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning!',
                                text: response,
                                showConfirmButton: true,
                                timer: 2000
                            })
                            $(this).html('<i class="glyphicon glyphicon-send"></i>&nbsp;&nbsp;<B>ACCEPT APPLICANT AS AN EMPLOYEE</B>');
                            $(this).css("pointer-events", "");
                        }else{
                            clearInterval(checksession);
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: "Applicant has been successfully accepted. "+response+" employeeid has been assigned to this applicant. You can now view this applicant on Employee List",
                                showConfirmButton: true,
                                timer: 2000
                            })
                              $("#mainform").attr("action","<?=site_url("main/site")?>");
                              $("input[name='sitename']").val("applicant/applicant_list");
                              $("input[name='rootid']").val("2");
                              $("input[name='menuid']").val("157");
                              $("input[name='titlebar']").val("Applicant List");
                              $("#mainform").submit();
                        }
                    }
                });
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
                swalWithBootstrapButtons.fire(
                    'Cancelled',
                    'Applicaiton is safe.',
                    'error'
                )
            }
        })
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

    $("#shareBtn").click(function(){
        $("#share-view").find("h3[tag='title']").text("Share Application For Viewing");
        $("#button_save_modalshare").text("Save");
        $.ajax({
            url: "<?=site_url('applicant/manageSharing')?>",
            type: "POST",
            data: {app_id:$(this).attr("app_id")},
            success: function(msg){
                $("#share-view").find("div[tag='display']").html(msg);
            }
        }); 
    })


    $(".view_status_history").click(function(){
        $("#historyModal").find(".modal-title").text("Application Status");
        $.ajax({
            url: "<?= site_url('applicant/applicationStatusHistory') ?>",
            type: "POST",
            data: {id:$(this).attr("tbl_id"), applicantid: $(this).attr("applicantid")},
            success:function(response){
                $("#historyModal").find("#display").html(response);
                $("#historyModal").modal("toggle");
            }
        });
    });

    $(".save_status").click(function(){
        var divclass = $(this).attr("code_stat_id");
        var app_categ_list = "";
        var filename = "";
        var filedata = "";
        var filetype = "";
        $("."+divclass+" input, ."+divclass+" textarea").each(function(){
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
                fd.append('applicantid',  GibberishAES.enc($('input[name=applicantId]').val() , toks));
                fd.append('app_categ_list',  GibberishAES.enc(app_categ_list, toks));
                fd.append('app_stat',  GibberishAES.enc(app_stat, toks));
                fd.append('assigned_head',  GibberishAES.enc(employeeid , toks));
                fd.append('code_status',  GibberishAES.enc( divclass, toks));
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
            applicantid : GibberishAES.enc($('input[name=applicantId]').val()  , toks),
            app_categ_list :  GibberishAES.enc(app_categ_list , toks),
            code_status :  GibberishAES.enc(divclass , toks),
            toks:toks
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
                lname:  GibberishAES.enc($("#lname").val() , toks),
                fname:  GibberishAES.enc($("#fname").val() , toks),
                mname:  GibberishAES.enc($("#mname").val() , toks),
                positionid :  GibberishAES.enc($("#positionid").val() , toks),
                code_status :  GibberishAES.enc($("#code_status").val() , toks),
                rowid :  GibberishAES.enc($("#rowid_inireq").val() , toks),
                applicantid :  GibberishAES.enc($("#appID").val() , toks),
                redtag :  GibberishAES.enc($("#redtag_").val() , toks),
                redTagRemarks :  GibberishAES.enc($("#redTagRemarks").val() , toks),
                email :  GibberishAES.enc($("#email").val() , toks),
                desc:  GibberishAES.enc($("#inidesc").val() , toks),
                step:  GibberishAES.enc($("#inistep").val() , toks),
                toks:toks
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
                lname:  GibberishAES.enc($("#lname").val() , toks),
                fname:  GibberishAES.enc($("#fname").val() , toks),
                mname:  GibberishAES.enc($("#mname").val() , toks),
                positionid :  GibberishAES.enc($("#positionid").val() , toks),
                code_status :  GibberishAES.enc($("#code_status").val() , toks),
                rowid :  GibberishAES.enc($("#rowid_prereq").val() , toks),
                applicantid :  GibberishAES.enc($("#appID").val() , toks),
                redtag :  GibberishAES.enc($("#redtag_").val() , toks),
                redTagRemarks :  GibberishAES.enc($("#redTagRemarks").val() , toks),
                email :  GibberishAES.enc($("#email").val() , toks),
                desc:  GibberishAES.enc($("#predesc").val() , toks),
                step:  GibberishAES.enc($("#prestep").val() , toks),
                toks:toks
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
            dataType: "JSON",
            success:function(response){
                if(response.isemployee == 1){
                    $("#acceptApplicant").prop("disabled", true);
                    $("#acceptApplicant").text("THIS APPLICANT IS ALREADY HIRED AS EMPLOYEE.");
                    $("#datehired").prop("disabled", true);
                    $("#datehired").val(response.datehired);

                    // $("#isemployee").show();
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

    $(".app_stat").change(function(){
        var div_id = $(this).closest("div");
        $(div_id).find(".employeeid").css("display", "inline");
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
