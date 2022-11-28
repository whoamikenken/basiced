<?php

/**
 * @author Angelica Arangco 2017
 *
 */

if($isHalfDay){
    list($timefrom, $timeto) = explode("|", $sched_affected);
}

?>
<style>
.modal{
    /*width: 1000px;*/
    left: 0;
    right: 0;
    margin: auto;
}
.th-style{
    text-align: center;
}

.form_row{
    margin-bottom: 20px;
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
                <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                <p>D`Great</p>
            </div>
        </div>
        <center><b><h3 tag="title" class="modal-title">View Details</h3></b></center>
    </div>
    <!-- end header -->

    <!-- body -->
    <label id="processing" style="display: none;margin-left: 60%;"><img src='<?=base_url()?>images/loading.gif' />  Your request is processing, Please Wait..</label>
    <label style="margin-left: 60%;color: blue;text-decoration: underline;<?=($hasfiles) ? '' : ' display: none;'?>" id="filename_details" file="" mime="">Click to view uploaded image.</label>
    <div class="modal-body">
        <div class="content">
            <form id="form_leave">
                <input hidden="" id="leavebal" value="" />
				<div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Name: </label>
                    <div class="field">
                        <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$fullname?></b></span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Office: </label>
                    <div class="field">
                        <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$edept?></b></span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Position: </label>
                    <div class="field">
                        <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$pos?></b></span>
                    </div>
                </div>
			
				<div class="form_row" <?= ($ob_type == "") ? "hidden" : "" ?>>
				    <!-- <label class="field_name align_right">Leave Type</label> -->
				        <div class="field" style="pointer-events: none;">
                            <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "ob") ? "checked" : "" ?> name="ob_type" value="ob" style="margin-right: 10px;"> <b style="margin-right: 10px;">OB</b>
                            <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "late") ? "checked" : "" ?> name="ob_type" value="late" style="margin-right: 10px;"> <b style="margin-right: 10px;">LATE</b>
                            <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "undertime") ? "checked" : "" ?> name="ob_type" value="undertime" style="margin-right: 10px;"> <b style="margin-right: 10px;">UNDERTIME</b>
                            <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "absent") ? "checked" : "" ?> name="ob_type" value="absent" style="margin-right: 10px;display: none;"> <b style="margin-right: 10px;display: none;">ABSENCES</b>
				        </div>
				</div>
			
				<div class="form_row" <?=$othertype == "CORRECTION" ? 'hidden' : '' ?>>
				    <label class="field_name align_right">With Pay?</label>
				    <div class="field no-search">
				        <select class="form-control" name="withpay" id="withpay" disabled="" style="width: 37%;"><?=$this->employeemod->withPay($paid);?></select>
				    </div>
				</div>
                <div class="form_row">
                    <div class="align_left" style="margin-left: 20%;"><b>Leave From <span style="margin-left: 25%;<?= ($ob_type == "late" || $ob_type == "undertime") ? 'display: none;' : ''; ?>">To</span></b></div>
                    <div class="field">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <div class="col-md-5" style="padding-left: 0px;">
                                <div class='input-group date' id="datesetfrom" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd" disabled="">
                                    <input type='text' class="form-control" name="datesetfrom" value="<?=$dfrom?>" readonly />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-5" style="<?= ($ob_type == "late" || $ob_type == "undertime") ? 'display: none;' : ''; ?>">
                                <div class="input-group date" id="datesetto" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd" disabled="">
                                    <input type='text' class="form-control" name="datesetto" type="text" value="<?=$dto?>" readonly/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><br>
				<!-- newly added by justin (with e) for #ica-hyperion 21090 -->
				<?if($othertype == "DIRECT"){
				    $timefrom = strtoupper(date('h:i a',strtotime($timefrom)));
				    $timeto = strtoupper(date('h:i a',strtotime($timeto)));
				?>
                <div class="form_row">
                    <?php if($ob_type=="ob"): ?>
                        <div class="align_left" style="margin-left: 20%;"><b>Time In <span style="margin-left: 32%;">Out</span></b></div>
                        <div class="field">
                            <div class="col-md-12" style="padding-left: 0px;">
                                <div class="col-md-5" style="padding-left: 0px;">
                                    <div class='input-group time'>
                                        <input type='text' class="form-control" name="tfrom" id="tfrom" value="<?=$timefrom?>" readonly/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class='input-group time'>
                                        <input type='text' class="form-control" name="tto" id="tto" value="<?=$timeto?>" readonly/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                    <?php if($ob_type!="ob"): ?>
                        <div class="align_left" style="margin-left: 20%;"><b>Time <?= ($ob_type=="late") ? 'In' : 'Out' ?></b></div>
                        <div class="field">
                            <div class="col-md-12" style="padding-left: 0px;">
                                <div class="col-md-5" style="padding-left: 0px;">
                                    <div class='input-group time'>
                                        <input type='text' class="form-control" name="tfrom" id="tfrom" value="<?= ($ob_type=="late") ? $timefrom : $timeto ?>" readonly/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
				<?}?>

				<!-- displayed time record -->
				<?if($othertype == "CORRECTION"){?>
				    <div id="displayedTimeRecord" class="form_row">
                        <label class="field_name align_right">My Time Record</label>
                        <div class="field">
                            <table class="table table-hover table-bordered" id="tblTimeRecord" style="width: 455px">
                                <thead style="background-color: #0072c6;">
                                    <tr>
                                        <th class="input-small align_center">Actual Time</th>
                                        <th class="input-small align_center">Request Time</th>
                                        <th class="input-small align_center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="displayedTimeInOut">
                                            <?if($base_id){ 
                                                $timerecord = $this->employeemod->findApplyTimeRecord($base_id);
                                                // echo "<pre>"; print_r($this->db->last_query());
                                                if(count($timerecord) > 0){
                                                    foreach($timerecord as $tr){
                                                        $style = '';
                                                        if($tr->status == 'NEW') $style = 'style="background-color:#A6D89F"'; 
                                                        if($tr->status == 'UPDATED') $style = 'style="background-color:#B08CB0"';
                                                        if($tr->status == 'REMOVED') $style = 'style="background-color:#FF9C9C"';
                                                        ?>
                                                        <tr <?=$style?> class='remove'  id='TR-<?=$tr->tid?>'>
                                                            <td class="input-small align_center" id='AT-<?=$tr->tid?>'><?=$tr->actual_time?></td>
                                                            <td class="input-small align_center"  id='RT-<?=$tr->tid?>'><?=($tr->request_time)? $tr->request_time : "(--:-- --) - (--:-- --)"?></td>
                                                            <td class="input-small align_center"  id='ST-<?=$tr->tid?>'><?=$tr->status?></td>
                                                        </tr>
                                            <?      } // end of for each
                                                }else{?>
                                                <tr><td class="input-small align_center" colspan="4">No data available...</td></tr>
                                            <?  } // end of if else condition
                                            

                                             }else{
                                            ?>
                                                <tr><td class="input-small align_center" colspan="4">No data available...</td></tr>
                                            <?}?>
                                </tbody>
                            </table>
                            <br />
                        </div>
                    </div><br>
				<?}?>
				
				<div class="form_row">
				    <label class="field_name align_right">Purpose</label>
				    <div class="field no-search">
				        <textarea rows="4" style="width: 100%;resize: none;"  class="form-control" name="reason" id="reason" placeholder="Reason" readonly=""><?=$reason?></textarea>
				    </div>
				</div><br>
				<div class="form_row">
				    <label class="field_name align_right">Date Applied</label>
				    <div class="field">
				        <input class="form-control required" id="mh_dateapplied" name="mh_dateapplied"  type="text" value="<?=$date_applied?>" disabled/>
				    </div>
				</div>

				<div class="form_row">
                    <label class="field_name align_right">Status</label>
                        <div class="field">
                            <input type="text" name="status" class="form-control" value="<?=$status?>" disabled=''>
                        </div>
                </div>

                <div class="form_row" id='remarks' <?= ($status == "DISAPPROVED") ? " " : "style='display:none;' " ?> >
                    <label class="field_name align_right">Remarks</label>
                    <div class="field no-search">
                        <input class="form-control" type="text" name="txtRemarks" id="txtRemarks"  size="100" value="<?=$rem?>" disabled/>
                    </div>
                </div>

			</form>
        </div>
    </div>
    <!-- end body -->

    <!-- footer -->
    <div class="modal-footer">
        <div id="saving">
            <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
    <!-- end footer -->
</div>

<script>
    $("#filename_details").click(function(){

        $.ajax({
          url:"<?=site_url('ob_application_/getOBAttachments')?>",
          type: "POST",
          data:{base_id:"<?=$base_id?>"},
          dataType: "json",
          cache:false,
          async:false,
          success:function(response){
            $("#filename_details").attr("file", response.file);
            $("#filename_details").attr("mime", response.mime);
          }
        }).done(function(){
             if($("#filename_details").attr("file")){
                  var data = $("#filename_details").attr("file");
                  var mime = $("#filename_details").attr("mime");
                  var objectURL = URL.createObjectURL(b64toBlob(data, mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
                  window.open(objectURL);
              }else{
                  var file_url = $("#filename_details").attr("content");
                  window.open(file_url);
              }
              $("#filename_details").show();
              $("#processing").hide(); 
        });
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
</script>