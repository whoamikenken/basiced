<?php

 $isreadonly = "readonly='true'";
 $isdisabled = "disabled";
 // echo '<pre>';
// print_r($ot_logs);
 // echo $test;
?>

<style>
    .modal{
        width: 100%;
        left: 0;
        right: 0;
        margin: auto;
    }
    .modal-body, .modal-footer{
        padding-right: 5%;
    }

    
</style>


<div class="modal-dialog">
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
                <center><b><h3 tag="title" class="modal-title">Overtime Details Application</h3></b></center>
            </div>
        <div class="modal-body">
            <form id="form_ot">
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Name</label>
                <div class="field">
                    <span style="line-height: 5px;"><b><?=$fullname?></b></span>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Department</label>
                <div class="field">
                    <span style="line-height: 5px;"><b><?=$edept?></b></span>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Position</label>
                <div class="field">
                    <span style="line-height: 5px;"><b><?=$pos?></b></span>
                </div>
            </div>
            
            <div class="form_row">
                <label class="field_name align_right">Date </label>
                <div class="field" style="padding-bottom: 10px;">
                    <div class='input-group date' style="width: 60%;" id="datesetfrom" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd">
                        <input class="form-control" size="16" name="datesetfrom" type="text" value="<?=$dfrom?>" readonly>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Office Hour</label>
                <div class="field" style="width: 75%;">
                    <input type="text" class="form-control" id="roh" name="roh" value="<?=$sched?>" readonly />
                </div>
            </div>
                <br>
            <?if($colhead == 'hrheadss'){
                ///< hrhead timesheet logs validation

                $total_hr = "";

                if(isset($ot_logs)){
                    foreach ($ot_logs as $date => $log_detail) {
                        // $total += $log_detail['ottotal'];
                        if($this->attcompute->exp_time($log_detail['otsubtotal']) < 3600){
                            $total = $total_approved = "00:00";
                        }
                        $total_hr += $this->attcompute->exp_time($log_detail['otsubtotal']);

                        ?>

                                <div class="form_row" style="display: none;">
                                    <label class="field_name align_right">Date</label>
                                    <div class="field" style="padding-bottom: 10px;">
                                        <div class="input-group date" id="" data-date="<?=$date?>" data-date-format="yyyy-mm-dd">
                                            <input class="form-control" size="16" name="" type="text" value="<?=$date?>" readonly">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form_row">
                                    <label class="field_name align_right" style="padding-bottom: 10px;">Overtime Start</label>
                                    <div class="field">
                                        <div class="col-md-12" style="padding-left: 0px;">
                                            <div class="col-md-5" style="padding-left: 0px; padding-right: 0px;">
                                                <div class="input-group time" style="width: 117%;">
                                                    <input class="input-small form-control" type="text" name="tfrom" id="tfrom" value="<?=$log_detail['otstart']?>" readonly="" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <label class="col-md-1" id="timeto_text">End</label>
                                            <div class="col-md-5">
                                                <div class="input-group time" style="width: 127%;">
                                                    <input class="input-small form-control" type="text" name="tto" id="tto" value="<?=$log_detail['otend']?>" readonly="" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form_row" hidden="hidden">
                                    <label class="field_name align_right" style="padding-bottom: 10px; ">Total Hr./Min.</label>
                                    <div class="field" style="width: 48%;">
                                        <input type="text" class="form-control" id="" name="" value="<?=$log_detail['otsubtotal']?>" readonly=""  />
                                    </div>
                              </div>
                    <?} //end for
                }

                $total_hr = $this->attcompute->sec_to_hm($total_hr);

            }else{
                ?>
                    <div class="form_row">
                        <label class="field_name align_right">Overtime Start</label>
                        <div class="field" style="padding-bottom: 10px;">
                            <div class="col-md-12" style="padding-left: 0px;">
                                <div class="col-md-5" style="padding-left: 0px; padding-right: 0px;">
                                    <div class="input-group time">
                                        <input class="input-small form-control" type="text" name="tfrom" id="tfrom" value="<?=$tstart?>" readonly="" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                                <span class="col-md-1" style="display: block; width: 14%; font-weight: bold;" id="timeto_text">&nbsp;End&nbsp;</span>
                                <div class="col-md-5" style="padding-left: 0px; padding-right: 0px;">
                                    <div class="input-group time">
                                        <input class="input-small form-control" type="text" name="tto" id="tto" value="<?=$tend?>" readonly="" />
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?}?>
            <div class="form_row">
                <label class="field_name align_right">Applied Total Hr./Min.</label>
                <div class="field" style="width: 48%;">
                    <input type="text" class="form-control" id="tot" name="tot" value="<?=$total?>" readonly />
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Approved Total Hr./Min.</label>
                <div class="field" style="width: 48%;">
                        <input class="input-small form-control" type="text" name="tot_approved" id="tot_approved" value="<?=($total_approved)?$total_approved:''?>"  readonly />
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Reason</label>
                <div class="field" style="padding-bottom: 10px;">
                    <textarea class="form-control" rows="4" style="width: 100%;resize: none;" name="reason" id="reason" placeholder="Reason" readonly=""><?=urldecode($reason)?></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Date Applied</label>
                <div class="field" style="width: 48%;">
                    <input class="required form-control" id="mh_dateapplied" name="mh_dateapplied" <?=$isreadonly?> type="text" value="<?=$date_applied?>"/>
                </div>
            </div>
            <br>
            <div class="form_row">
                <label class="field_name align_right">Status</label>
                    <div class="field no-search" style="width: 48%;" >
                        <select class="form-control" name="mh_status" id="mh_status" <?= (in_array($colstat,array("APPROVED","ENDORSED","DISAPPROVED")) ? $isdisabled : "")?> >
                            <?
                                $opt_status = $this->extras->showLeaveStatus();
                                foreach($opt_status as $c=>$val){
                                if($val == "APPROVED"){
                                    if($this->extensions->checkIfSecondApprover($idkey, "overtime")) $val = "APPROVED";
                                    else $val = "ENDORSED";
                                    if($colhead == 'hrhead') $val = "NOTED";
                                }
                            ?><option<?=($c==$colstat ? " selected" : "")?> value="<?=$c?>" ><?=($val=="PENDING"?"Select status..":$val)?></option><?    
                            }
                            ?>
                        </select>
                    </div>
            </div>
            <?if($colstat == 'DISAPPROVED'){?>
                <div class="form_row">
                    <label class="field_name align_right">Remarks</label>
                    <div class="field no-search" style="padding-bottom: 10px;">
                        <input class="col-md-8" type="text" name="txtRemarks" id="txtRemarks"  size="100" value="<?=$rem?>"  />
                    </div>
                </div>
            <?}?>
    </form>
</div>
<div class="modal-footer">
    <div id="loading" hidden=""></div>
    <div id="saving">
        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
        <?if($job == "edit"){?>
            <?php if(!in_array($colstat,array("APPROVED","DISAPPROVED"))){ ?>
                <button type="button" id="saveot" class="btn btn-success">Save</button>
            <?php } ?>
        <?}?>
    </div>
</div>
<script>
$("#saveot").click(function(){  

    var newstat = $("#mh_status").val();
    var oldstat = '<?=$colstat?>';
    if(newstat == oldstat){
        // alert('No changes were made.');
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'No changes were made.',
            showConfirmButton: true,
            timer: 1500
        })
        $("#close").click();
        return false;
    }
    var d = 
        {
            colhead         : "<?=$colhead?>",
            isLastApprover  : "<?=$isLastApprover?>",
            code_request    : "<?=$code_request?>",
            otid            : "<?=$otid?>",
            base_id         : "<?=$base_id?>",
            status          : newstat,
            ottotal         : $('input[name=tot_approved]').val(),
            remarks         : $("#txtRemarks").val()
        };

    $.ajax({
        url:"<?=site_url("overtime_/saveOTStatusChange")?>",
        type:"POST",
        dataType : 'JSON',
        data:d,
        success: function(msg){
            $("#close").click();
            // alert(msg.msg);

            if(msg.err_code == 0){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: msg.msg,
                    showConfirmButton: true,
                    timer: 1500
                })
                loadOTAppList('','','PENDING');
            }else{
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: msg.msg,
                    showConfirmButton: true,
                    timer: 1500
                })
            }
            getUpdatedManageNotification("OVERTIME");
        }
    });
});

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
          $("a[menuid='75']").find(".notifcount").text(response);
          if(response == 0){
            $("a[menuid='75']").find(".notifdiv").hide();
          }
        }
      });
    }

$('.time').datetimepicker({
    format: 'LT'
});

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$('.chosen').chosen();
</script>