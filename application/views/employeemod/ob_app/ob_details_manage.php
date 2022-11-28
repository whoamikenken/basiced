<?php
/**
 * @author Justin
 * @copyright 2016
 */

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\mailleaveapp_manage.php
 */

$CI =& get_instance();
$CI->load->model('utils');

$isreadonly = "readonly='true'";
$isdisabled = "disabled";
$ishidden = 'hidden=true';
$user = $this->session->userdata("username");

$canedit = false;

if($CI->utils->getDeptHead('head','HR') == $user || $CI->utils->getDeptHead('divisionhead','HR') == $user){
    // $isreadonly = $isdisabled = $ishidden ="";
    // $canedit = true;
}

?>

<form id="form_leave">

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
            <center><b><h3 tag="title" class="modal-title"><?= ($othertype != "CORRECTION") ? "Official Business Details Application" : "Correction for Time in/out Details Application" ?></h3></b></center>
        </div>
        <div class="modal-body">
              <label id="processing" style="display: none;margin-left: 60%;"><img src='<?=base_url()?>images/loading.gif' />  Your request is processing, Please Wait..</label>
              <label style="margin-left: 60%;color: blue;text-decoration: underline;" id="filename" file="" mime="">Click to view uploaded image.</label><br>
              <?if($othertype != "CORRECTION"){?>
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
               </div><br>
               <div class="form_row" style="margin-left: 2.5%;">
                    <div class="field">
                        <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "ob") ? "checked" : "" ?> name="ob_type" value="ob" style="margin-right: 10px;" <?=$isdisabled?>> <b style="margin-right: 10px;" <?=$isdisabled?> >OB</b>
                        <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "late") ? "checked" : "" ?> name="ob_type" value="late" style="margin-right: 10px;" <?=$isdisabled?> > <b style="margin-right: 10px;" <?=$isdisabled?> >LATE</b>
                        <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "undertime") ? "checked" : "" ?> name="ob_type" value="undertime" style="margin-right: 10px;" <?=$isdisabled?> > <b style="margin-right: 10px;" <?=$isdisabled?> >UNDERTIME</b>
                        <input type="checkbox" class="double-sized-cb" <?= ($ob_type == "absent") ? "checked" : "" ?> name="ob_type" value="absent" style="margin-right: 10px;display: none;" <?=$isdisabled?> > <b style="margin-right: 10px;display: none;">ABSENCES</b>
                    </div>
                </div><br>
                <div class="form_row" id="wrap_half_day">
                    <div class="field" style="padding-bottom: 10px;">
                        <div class="col-md-12">
                            &nbsp;<input type="checkbox" class="double-sized-cb" name="ishalfday" value="1" <?=$isHalfDay?'checked':''?> <?=$isdisabled?> >&nbsp;&nbsp; <b>Check this if your leave to be applied is halfday</b>
                        </div>
                    </div>
                </div><br>
                 <?}?>


             <!--    <div class="form_row">
                    <label class="field_name align_right">Leave Type</label>
                    
                        <div class="field">
                            <input type="checkbox" name="ltype" value="ABSENT" <?=($othertype == "ABSENT" ? " checked" : "")?> disabled="" /> ABSENT &nbsp;&nbsp;&nbsp;
                            <input type="checkbox" name="ltype" value="DIRECT" <?=($othertype == "DIRECT" ? " checked" : "")?> disabled="" /> OB &nbsp;&nbsp;&nbsp;
                            <input type="checkbox" name="ltype" value="CORRECTION" <?=($othertype == "CORRECTION" ? " checked" : "")?> disabled="" /> CORRECTION OF TIME IN/OUT &nbsp;&nbsp;&nbsp;
                        </div>
                </div> -->

               <!--  <div class="form_row">
                    <label class="field_name align_right">With Pay?</label>
                    <div class="field no-search">
                        <select class="form-control" name="withpay" id="withpay" disabled=""><?=$this->employeemod->withPay($paid);?></select>
                    </div>
                </div> -->
                <div class="form_row">
                    <label class="field_name align_right">Leave From</label>
                    <div class="field">
                        <div class="col-md-5" style="padding-left: 0px">
                            <div class='input-group date'  data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd" disabled="">
                                <input type='text' class="form-control" name="datesetfrom" value="<?=$dfrom?>" readonly/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <?if($othertype != "CORRECTION"){?>
                        <div class="col-md-2" style="width: 7.666667%;"><b>To</b></div>
                        <div class="col-md-5">
                            <div class='input-group date'  data-date="<?=$dto?>" data-date-format="yyyy-mm-dd" disabled="">
                                <input type='text' class="form-control" name="datesetto" value="<?=$dto?>" readonly/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div> 
                        <?}?>
                    </div>
                </div><br>
                <!-- newly added by justin (with e) for #ica-hyperion 21090 -->
                <?if($othertype == "DIRECT"){
                    $timefrom = strtoupper(date('h:i a',strtotime($timefrom)));
                    $timeto = strtoupper(date('h:i a',strtotime($timeto)));
                ?>
                <div class="form_row" id="hideTITO" >
                    <label class="field_name align_right">Time In</label>
                        <div class="field">
                            <div class="col-md-5" style="padding-left: 0px;">
                                <div class='input-group time'>
                                    <input type='text' class="form-control" name="tfrom" id="tfrom" value="<?=$timefrom?>"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2" style="width: 7.666667%;">&nbsp;<b>Out</b>&nbsp;</div>
                            <div class="col-md-5">
                                <div class='input-group time'>
                                    <input type='text' class="form-control" name="tto" id="tto" value="<?=$timeto?>"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                </div><br>
                <?}?>

                <!-- displayed time record -->
                <?if($othertype == "CORRECTION"){?>

                    <div class="form_row" id="hideTable" >
                        <label class="field_name align_right">My Time Record</label>

                            <script> 
                            $("#hideME").hide(); 
                            $("input[name='tfrom']").val("");
                            $("input[name='tto']").val("");
                            //$("#toTxt").hide();
                            </script>
                            <div class="field">
                                <table class="table table-hover table-bordered" id="tblTimeRecord" style="width: 700px">
                                    <thead>
                                        <tr>
                                            <?if($canedit){?>
                                              <th class="input-small align_center" style="background-color: #0072c6;color: black;">Actual Time</th>
                                              <th class="input-small align_center" style="background-color: #0072c6;color: black;">Request Time</th>
                                              <th class="input-small align_center" style="background-color: #0072c6;color: black;">Final Time</th>
                                              <th class="input-small align_center" style="background-color: #0072c6;color: black;">Status</th>
                                              <th class="input-small align_center" style="background-color: #0072c6;color: black;">EDIT </th>
                                            <?}else{?>
                                              <th class="input-small align_center" style="background-color: #0072c6;color: black;">Actual Time</th>
                                              <th class="input-small align_center" style="background-color: #0072c6;color: black;">Request Time</th>
                                              <th class="input-small align_center" style="background-color: #0072c6;color: black;">Final Time</th>
                                              <th class="input-small align_center" style="background-color: #0072c6;color: black;">Status</th>
                                            <?}?>
                                        </tr>
                                    </thead>
                                    <tbody id="displayedTimeInOut">
                                            <?
                                                $timerecord = $this->db->query("SELECT * FROM leave_app_ti_to WHERE aid='$base_id'")->result();
                                                if(count($timerecord) > 0){
                                                    foreach($timerecord as $tr){
                                                        $style ='';
                                                        if($tr->status == 'NEW') $style = 'style="background-color:#A6D89F"'; 
                                                        if($tr->status == 'UPDATED') $style = 'style="background-color:#B08CB0"';
                                                        if($tr->status == 'REMOVED') $style = 'style="background-color:#FF9C9C"';
                                                        ?>
                                                        <tr <?=$style?> id="TR-<?=$tr->tid?>">
                                                            <?
                                                            if($canedit){?>
                                                              <td class="input-small align_center" id="AT-<?=$tr->tid?>"><?=$tr->actual_time?></td>
                                                              <td class="input-small align_center" id="RT-<?=$tr->tid?>"><?=($tr->request_time)? $tr->request_time : "(--:-- --) - (--:-- --)"?></td>
                                                              <td class="input-small align_center" id="FT-<?=$tr->tid?>"><?=($tr->final_time)? $tr->final_time : ""?></td>
                                                              <td class="input-small align_center" id="ST-<?=$tr->tid?>"><?=$tr->status?></td>
                                                              <td class="input-small align_center">
                                                                <div class="input-group">
                                                                <?# displayed button
                                                                  if($tr->status){?>
                                                                     <a class="btn btn-primary" id='edit'  code="<?=$tr->tid?>"><i class="icon glyphicon glyphicon-edit"></i></a>
                                                                     <?if($tr->status == 'UPDATED'){?>
                                                                        <a class="btn btn-primary" id='remove'  code="<?=$tr->tid?>"><i class="icon glyphicon glyphicon-trash"></i></a>
                                                                     <?}?>
                                                                     <a class="btn btn-danger"  id='disapproved'  code="<?=$tr->tid?>"><i id='icon-<?=$tr->tid?>' class="icon glyphicon glyphicon-thumbs-down"></i></a>
                                                                <?}?>
                                                                </div>
                                                              </td>
                                                            <?}else{?>
                                                              <td class="input-small align_center"><?=$tr->actual_time?></td>
                                                              <td class="input-small align_center"><?=($tr->request_time)? $tr->request_time : "(--:-- --) - (--:-- --)"?></td>
                                                              <td class="input-small align_center"><?=($tr->final_time)? $tr->final_time : "(--:-- --) - (--:-- --)"?></td>
                                                              <td class="input-small align_center"><?=$tr->status?></td>
                                                            <?}?>
                                                        </tr>
                                            <?      } // end of for each
                                                }else{?>
                                                <tr><td class="input-small align_center" colspan="<?=($canedit)?'5':'4'?>">No data available...</td></tr>
                                            <?  } // end of if else condition
                                            ?>
                                    </tbody>
                                </table>
                                <br />
                            </div>
                    </div>
                    <br>
                <?}?>
                <!-- end of newly added by justin (with e) for #ica-hyperion 21090 -->
                 <!-- <div class="form_row">
                    <label class="field_name align_right">Destination</label>
                    <div class="field no-search">
                        <textarea rows="4" style="width: 90%;resize: none;" name="destination" id="destination"  readonly=""><?=$destination?></textarea>
                    </div>
                </div><br> -->
                <div class="form_row">
                    <label class="field_name align_right">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" class="form-control" style="resize: none;width: 89%;" name="reason" id="reason" placeholder="Reason" readonly=""><?=$reason?></textarea>
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right">Date Applied</label>
                    <div class="field">
                        <input class="form-control required" id="mh_dateapplied" name="mh_dateapplied" <?=$isreadonly?> type="text" value="<?=$date_applied?>" style="width: 40%;" />
                    </div>
                </div>
                <br>
               <div class="form_row" style="<?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'display:none;' : "")?>">
                    <label class="field_name align_right">Status</label>
                    <div class="field no-search">
                        <select class="form-control" name="mh_status" id="mh_status" <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?> style="width: 40%;" >
                            <?
                                $opt_status = $this->extras->showLeaveStatus();
                                foreach($opt_status as $c=>$val){
                                if($val == "APPROVED"){
                                    if($this->extensions->checkIfSecondApprover($idkey, "ob")) $val = "APPROVED";
                                    else $val = "ENDORSED";
                                    if($colhead == 'hrhead') $val = "NOTED";
                                }
                            ?><option <?=($c==$colstat ? " selected" : "")?> value="<?=$c?>" ><?=($val=="PENDING"?"Select status..":$val)?></option><?    
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form_row" style="<?= (!in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'display:none;' : "")?>">
                    <label class="field_name align_right">Status</label>
                    <div class="field no-search">
                        <input type="text" class="form-control" style="width: 40%;" disabled value="<?=$this->extensions->statusLabel($base_id, "ob", $colhead)?>">
                    </div>
                </div>
                <br>
                <div class="form_row" id='remarks' style="display: none;">
                    <label class="field_name align_right">Remarks</label>
                    <div class="field no-search">
                        <input class="form-control" type="text" name="txtRemarks" id="txtRemarks"  size="100" value="<?=$rem?>"/>
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
</div>

</form>
<script>
  var data = "";
  var mime = "";
  var loadStat = true;
  var fileinter;
$(document).ready(function() {
  setTimeout(function() {
    loadLazy();
  }, 500);
});
    $("#save").click(function(){  
        var newstat = $("#mh_status").val();
        var oldstat = '<?=$colstat?>';
        var endorse = $("#mh_status option:selected").text();
        if(newstat == oldstat){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'No changes were made.',
                showConfirmButton: true,
                timer: 3500
            })
            $("#close").click();
            return false;
        }
        var timeRecord = "";

        if("<?=$othertype?>" == "CORRECTION"){
            $("#tblTimeRecord").find("tbody tr").each(function(){
                if($(this).find("td").length > 1){
                    timeRecord += (timeRecord?"|":"");
                    timeRecord += $(this).attr('id');
                    timeRecord += "~u~";
                    var sTime = $(this).find('td:eq(1)').text().split(" - ");
                    timeRecord += sTime[0];
                    timeRecord += "~u~";
                    timeRecord += sTime[1];
                }
            });    
        }
        
        
        var form_data = 
            {
                colhead         : "<?=$colhead?>",
                endorse         : endorse,
                isLastApprover  : "<?=$isLastApprover?>",
                code_request    : "<?=$code_request?>",
                leaveid         : "<?=$leaveid?>",
                base_id         : "<?=$base_id?>",
                timerecord      : timeRecord,
                status          : newstat,
                timefrom        : $("#tfrom").val(),
                timeto          : $("#tto").val(),
                remarks         : $("#txtRemarks").val()
            };

        $.ajax({
            url:"<?=site_url("ob_application_/saveLeaveStatusChange")?>",
            type:"POST",
            dataType : 'json',
            data:form_data,
            success: function(msg){
                $("#close").click();
                if(msg.err_code == 1){
                  Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 3500
                    })
                    setTimeout(
                        function() {
                            view_offbus_status('','','PENDING');
                            getApproverUpdatedNotification("<?= ($othertype == 'CORRECTION') ? 'CORRECTION' : 'OB' ?>");
                            getUpdatedManageNotification();
                            $("#mymodalleave").hide();
                        }, 2000
                    );
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg.msg,
                        showConfirmButton: true,
                        timer: 3500
                    })
                }
                $(".inner_navigation .main li .active a").click();
                view_offbus_status('','','PENDING');
                getApproverUpdatedNotification("<?= ($othertype == 'CORRECTION') ? 'CORRECTION' : 'OB' ?>");
                getUpdatedManageNotification();
                $("#mymodalleave").hide();
            }
        });
    });

    $("#mh_status").change(function(){
      if($(this).val() == "DISAPPROVED") $("#remarks").show();
      else $("#remarks").hide();
    });

    $("#othleave").css("pointer-events","none");
    $("input[name='ltype']").on('change', function() {
        $("input[name='ltype']").not(this).prop('checked', false);
        if($(this).val() == "other")
            $("#othleave").css("pointer-events","");
        else{
            $("#othleave").css("pointer-events","none").val("");
        }
    });

    $('.time').datetimepicker({
        format: 'LT'
    });

    $('.date').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $(".chosen").chosen();

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
          url:"<?=site_url('ob_application_/getOBAttachments')?>",
          type: "POST",
          data:{base_id:"<?=$base_id?>"},
          dataType: "json",
          async:true,
          success:function(response){
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

    function getApproverUpdatedNotification(module){
      $.ajax({
        url: "<?=site_url('utils_/getApproverUpdatedNotification')?>",
        type: "POST",
        data: {module:module},
        success:function(response){
          $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifcount").text(response);
          if(response == 0){
            $("#sidebar ul li.active>a, a[aria-expanded='true']").find(".notifdiv").hide();
          }
        }
      });
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
    }

</script>