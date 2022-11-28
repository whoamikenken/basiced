<?php

/**
 * @author Angelica Arangco 2017
 *
 */

$isreadonly = $job == 'edit' ? '' : 'readonly';
$isdisabled = $job == 'edit' ? '' : 'disabled';
$ishidden   = $job == 'edit' ? '' : "style='display:none;'";

$isdisabled = $colhead != 'cphead' ? '' : 'disabled';
$isreadonly = $colhead != 'cphead' ? '' : 'readonly';


$disabled_checkb = '';
$disabled_type_withpay = "";
if($colhead != "hrhead") $disabled_type_withpay = "disabled";
$approver_position = $this->extensions->getEmployeePositionId($this->session->userdata('username'));
// if($approver_position != 183) $disabled_checkb = "disabled";
?>

<form id="form_seminar">

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
                <center><b><h3 tag="title" class="modal-title">Seminar Application Details</h3></b></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form_row">
                        <label class="field_name align_right" style="line-height: 5px;">Name</label>
                        <div class="field">
                            <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$fullname?></b></span>
                        </div>
                    </div>
                    <div class="form_row">
                        <label class="field_name align_right" style="line-height: 5px;">Department</label>
                        <div class="field">
                            <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$this->extensions->getDepartmentDescription($deptid)?></b></span>
                        </div>
                    </div>
                    <div class="form_row">
                        <label class="field_name align_right" style="line-height: 5px;">Position</label>
                        <div class="field">
                            <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$epos?></b></span>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding-bottom: 10px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 align_right">Date From</label>
                                <div class="col-md-8">
                                    <div class="input-group date" data-date="<?= isset($datesetfrom) ? $datesetfrom : "" ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control" name="datesetfrom" value="<?= isset($datesetfrom) ? $datesetfrom : "" ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 align_right">Date To</label>
                                <div class="col-md-8">
                                    <div class="input-group date" data-date="<?= isset($datesetto) ? $datesetto : '' ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" class="form-control" name="datesetto" value="<?= isset($datesetto) ? $datesetto : '' ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding-bottom: 10px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 align_right">Time From</label>
                                <div class="col-md-8">
                                    <div class='input-group time'>
                                        <input type='text' class="form-control" name="fromtime" value="<?= isset($timefrom) ? $timefrom : '' ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 align_right">Time From</label>
                                <div class="col-md-8">
                                    <div class='input-group time'>
                                        <input type='text' class="form-control" name="totime" value="<?= isset($timeto) ? $timeto : '' ?>" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-left: 0px;">
                        <div class="form-group">
                            <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar Category</label>
                            <div class="col-md-10" style="margin-left: 0px;">
                                <select class="form-control" name="category" style="display: inline;margin-left: 5px;width: 97%;" disabled="">
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
                    </div>
                    <div class="col-md-12" style="margin-left: 0px;">
                        <div class="form-group">
                            <label class="col-md-2 align_right" style="margin-left: 0px;">Seminar - Workshop/Training</label>
                            <div class="col-md-10" style="margin-left: 0px;">
                                <select class="form-control" name="seminar" style="display: inline;margin-left: 5px;width: 97%;" disabled="">
                                    <?php
                                        $educlevel = $this->extras->showreportseduclevel(' - Select POVEDAN SPIRITUAL and SPIRITUAL - ','PTS_PDP');
                                        foreach($educlevel as $c=>$val){
                                            ?><option value="<?=$c?>" <?= (isset($seminar) && $seminar==$c) ? "selected" : "" ?> ><?=$val?></option><?    
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding-bottom: 10px;">
                        <div class="form-group">
                            <label class="col-md-2 align_right" style="margin-left: 0px;">Type of Seminar Title</label>
                            <div class="col-md-10" style="margin-left: 0px;">
                                <textarea class="form-control" id="seminar_title" value="<?= isset($title) ? $title : '' ?>" style="width: 97%;height: 80px;margin-left: 5px;" disabled=""><?= isset($title) ? $title : '' ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding-bottom: 10px;">
                        <div class="form-group">
                            <label class="col-md-2 align_right" style="margin-left: 0px;">Organizer</label>
                            <div class="col-md-10" style="margin-left: 0px;">
                                <input type="text" class="form-control" name="organizer" value="<?= isset($organizer) ? $organizer : '' ?>" disabled="" style="margin-left: 5px;width: 97%;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-left: 0px;padding-bottom: 10px;">
                        <div class="form-group">
                            <label class="col-md-2 align_right" style="margin-left: 0px;">Venue</label>
                            <div class="col-md-10" style="margin-left: 0px;">
                                <select class="form-control" name="venue" style="display: inline;margin-left: 5px;width: 35%;" disabled="">
                                    <option value="sample">Sample</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding-bottom: 10px;">
                        <div class="form-group">
                            <label class="col-md-2 align_right" style="margin-left: 0px;">Location</label>
                            <div class="col-md-10" style="margin-left: 0px;">
                                <input type="text" name="location" value="<?= isset($location) ? $location : '' ?>" class="form-control" disabled="" style="margin-left: 5px;width: 97%;">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" style="padding-bottom: 10px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 align_right">Registration Fee</label>
                                <div class="col-md-8" style="padding-left: 15px;">
                                    <input type="text" name="fee" value="<?= isset($fee) ? $fee : '' ?>" class="form-control" value="" disabled="" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-md-4 align_right">Deadline of Registration</label>
                                <div class="col-md-8">
                                    <div class="input-group date" data-date="<?= isset($datesetto) ? $datesetto : '' ?>" data-date-format="yyyy-mm-dd">
                                        <input type="text" name="deadline" class="form-control" value="<?= isset($deadline) ? $deadline : '' ?>" disabled="" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;padding-left: 0px;">Other Remarks</label>
                                <div class="col-md-10" style="margin-left: 0px;padding-left: 4px;">
                                    <textarea id="remarks" value="<?= isset($remarks) ? $remarks : '' ?>" class="form-control" style="width: 99%;height: 80px;margin-left: 5px;" disabled="" ><?= isset($remarks) ? $remarks : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-left: 0px;padding-bottom: 10px;padding-left: 0px;">
                            <div class="form-group">
                                <label class="col-md-2 align_right" style="margin-left: 0px;">Status</label>
                                <div class="col-md-10" style="margin-left: 0px;">
                                    <select class="form-control" name="mh_status" id="mh_status" <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?> style="display: inline;margin-left: 4px;width: 35%;" >
                                        <?
                                            $opt_status = $this->extras->showLeaveStatus();
                                            foreach($opt_status as $c=>$val){
                                            if($val == "APPROVED"){
                                                if($this->extensions->checkIfSecondApprover($idkey, "seminar")) $val = "APPROVED";
                                                else $val = "ENDORSE";
                                                if($colhead == 'hrhead') $val = "NOTED";
                                            }
                                        ?><option<?=($c==$colstat ? " selected" : "")?> value="<?=$c?>" ><?=($val=="PENDING"?"Select status..":$val)?></option><?    
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div id="loading" hidden=""></div>
                <div id="saving">
                    <?if(!in_array($colstat,array("APPROVED","DISAPPROVED"))){?>
                        <button type="button" id="save" class="btn btn-success">Save</button>
                    <?}?>
                    <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $("#save").click(function(){  
        var newstat = $("#mh_status").val();
        var endorse = $("#mh_status option:selected").text();
        var oldstat = '<?=$colstat?>';
        var datesetfrom = $("input[name='datesetfrom']").val();
        var datesetto = $("input[name='datesetto']").val();
        var timefrom = $("input[name='fromtime']").val();
        var timeto = $("input[name='totime']").val();
        if(newstat == oldstat){
            alert('No changes were made.');
            $("#close").click();
            return false;
        }

        /*get new code_request*/
        var code_request = "<?=$code_request?>";
        /*end*/
        
        var form_data = 
            {
                "<?=$base_id?>" :{
                    colhead         : "<?=$colhead?>",
                    isLastApprover  : "<?=$isLastApprover?>",
                    code_request    : code_request,
                    seminarid       : "<?=$seminarid?>",
                    base_id         : "<?=$base_id?>",
                    status          : newstat,
                    status_desc     : endorse,
                    remarks         : $("#txtRemarks").val(),
                    datesetfrom     : datesetfrom,
                    datesetto       : datesetto,
                    timefrom        : timefrom,
                    timeto          : timeto,
                    update          : true
                }
            };

        $.ajax({
            url:"<?=site_url("seminar_/saveSeminarStatusChange")?>",
            type:"POST",
            dataType : 'json',
            data:form_data,
            success: function(msg){
                $("#close").click();
                alert(msg.msg);
                location.reload();
            }
        });
    });

    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });

    $(".time").datetimepicker({
        format: "LT"
    });

    $('.chosen').chosen();

</script>
