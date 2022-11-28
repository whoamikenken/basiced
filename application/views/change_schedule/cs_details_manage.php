<?php
/**
 * @modified Angelica Arangco  2017
 */
 $CI =& get_instance();
 $CI->load->model("utils");
 $user = $this->session->userdata('usertype');
 $isreadonly = "readonly='true'";
 $isdisabled = "disabled";
 // $ishidden   = ($colhead=='hrhead' || ($user=='ADMIN' || $user=='SUPER ADMIN')?'style="visibility:visible;"':'hidden=""');
 $ishidden = "hidden";
 
 $colstat = ($colstatus) ? $colstatus : "";
?>
<style>
#reason
{
  resize: none;
}

.modal-overflow .modal-body{
    margin-bottom: 5px !important;
}

</style>
<form id="frmapproved">
<input name="id" value="<?=$csid?>" hidden="" />
<div class="modal-dialog modal-lg" style="width: 1400px;">
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                    <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                    <p style="font-family: Avenir;  margin-top: -0.5%;">D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title">Change Schedule Details Application</h3></b></center>
        </div>
        <div class="modal-body">
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
                <label class="field_name align_right">Date Active</label>
                <div class="field">
                    <div class="input-group date" id="dfrom" data-date="<?=$date_effective?>" data-date-format="yyyy-mm-dd" style="width: 20%;">
                        <input class="form-control" size="16" name="dfrom" type="text" value="<?=$isTemporary? '' : $date_effective?>" readonly>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ///< For change schedule on specific dates only -->
            <div class="form_row">
                <label class="field_name align_right">Date From</label>
                <div class="field">
                    <div class="col-md-12" style="padding-left: 0px;">
                        <div class="col-md-5" style="padding-left: 0px;width: 20.2%;padding-right: 0px;">
                            <div class="input-group date" id="start" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd">
                                <input class="form-control" size="16" name="start" type="text" value="<?=$isTemporary? $dfrom : ''?>" readonly>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <span class="col-md-1" id="timeto_text" style="width: 3.333333%;">&nbsp;<b>TO</b>&nbsp;</span>
                        <div class="col-md-5" style="width: 20.2%;">
                            <div class="input-group date" id="end" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd">
                                <input class="form-control" size="16" name="end" type="text" value="<?=$isTemporary? $dto : ''?>" readonly>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form_row">
                <label class="field_name align_right">Status</label>
                    <div class="field no-search" id='mh_statusdiv'>
                        <select class="form-control" name="mh_status" id="mh_status" <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? 'disabled' : "")?> style="width: 40%;" >
                            <?
                                $opt_status = $this->extras->showLeaveStatus();
                                foreach($opt_status as $c=>$val){
                                if($val == "APPROVED"){
                                    if($this->extensions->checkIfSecondApprover($idkey, "ob")) $val = "APPROVED";
                                    else $val = "ENDORSE";
                                    if($colhead == 'hrhead') $val = "NOTED";
                                }
                            ?><option<?=($c==$colstat ? " selected" : "")?> value="<?=$c?>" ><?=($val=="PENDING"?"Select status..":$val)?></option><?    
                            }
                            ?>
                        </select>
                    </div>
            </div>
            <br>
            <div class="form_row" style="border: transparent !important;">
                <table class="table table-striped table-bordered table-hover" width="100%">
                    <thead>
                        <tr style="background-color: #0072c6;">
                            <th rowspan="2">Day of Week</th>
                            <th rowspan="2">From</th>
                            <th rowspan="2">To</th>
                            <th colspan="2" <?=$ishidden?>>First Half</th>
                            <th rowspan="2" <?=$ishidden?>>Early Dismissal</th>
                            <th class="align_center" rowspan="2" hidden="">Lec</th>
                            <th class="align_center" rowspan="2" hidden="">Lab</th>
                            <th class="align_center" rowspan="2" hidden="">Course</th>
                            <th class="align_center" rowspan="2" hidden="">Section</th>
                            <th class="align_center" rowspan="2">Subject</th>
                            <th class="align_center" rowspan="2">Aimsdept</th>
                        </tr>
                        <tr style="background-color: #0072c6;" <?=$ishidden?>>
                            <th>Tardy Start</th>
                            <th>Absent Start</th>
                            <th style="display:none">Half Day Absent</th>
                        </tr>
                    </thead>
                    
                    <tbody id="schedule">
                    <?
                    if($csdata){
                        $prev_day = '';
                        foreach($csdata as $row){

                            $start              = $row->starttime!="00:00:00"?date("h:i A",strtotime($row->starttime)):"";
                            $end                = $row->endtime!="00:00:00"?date("h:i A",strtotime($row->endtime)):"";
                            $tardy_start        = $row->tardy_start!="00:00:00"?strtotime($row->tardy_start):"";
                            $absent_start       = $row->absent_start!="00:00:00"?strtotime($row->absent_start):"";
                            $early_dismissal    = $row->early_dismissal!="00:00:00"?strtotime($row->early_dismissal):"";

                            if($colhead=='hrhead'){
                                if($start){
                                    if(!$tardy_start){
                                        if($prev_day == $row->dayofweek) $tardy_start = strtotime("+1 minutes",strtotime($start));
                                        else                             $tardy_start = strtotime("+6 minutes",strtotime($start));
                                    }
                                    if(!$absent_start)  $absent_start = strtotime("+121 minutes",strtotime($start));
                                    
                                }
                                if($end){
                                    if(!$early_dismissal) $early_dismissal = strtotime("-121 minutes",strtotime($end));
                                }
                            }

                            $tardy_start        = $tardy_start?date("h:i A",$tardy_start):"";
                            $absent_start       = $absent_start?date("h:i A",$absent_start):"";
                            $early_dismissal    = $early_dismissal?date("h:i A",$early_dismissal):"";


                            // echo "<span style='color:red'>".$row->id."</span>";
                            ?>
                                <tr tag="grp" detail_id="<?=$row->id?>" dayofweek="<?=$row->day_code?>" dayidx="<?=$row->day_index?>">
                                    <td><?=$row->day_name?></td>
                                    <td>
                                        <div class='input-group time'>
                                            <input class="input-small align-center form-control" type="text" name="fromtime" value="<?=$start?date("h:i A",strtotime($start)):""?>" readonly="" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>
                                
                                    <td>
                                        <div class='input-group time'>
                                            <input class="input-small align-center form-control" type="text" name="totime" value="<?=$end?date("h:i A",strtotime($end)):""?>" readonly="" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>
                                
                                    <td <?=$ishidden?>>
                                        <div class='input-group time'>
                                            <input class="input-small align-center form-control <?=$ishidden?'':' isreq'?>" type="text" name="tardy_f" value="<?=$tardy_start?>" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>
                                
                                    <td <?=$ishidden?>>
                                        <div class='input-group time'>
                                            <input class="input-small align-center form-control <?=$ishidden?'':' isreq'?>" type="text" name="absent_f" value="<?=$absent_start?>"  />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>
                                
                                    <td style="display:none">
                                        <div class='input-group time'>
                                            <input class="input-small align-center form-control" type="text" name="absent_e" value="<?=$row->absent_half_start!="00:00:00"?date("h:i A",strtotime($row->absent_half_start)):""?>"  />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <td <?=$ishidden?>>
                                        <div class='input-group time'>
                                            <input class="input-small align-center form-control <?=$ishidden?'':' isreq'?>" type="text" name="early_d" value="<?=$early_dismissal?>" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <td class="align_center" hidden=""><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LEC" <?=$row->leclab == "LEC" ? " checked" : ""?> /></td>
                                    <td class="align_center" hidden=""><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LAB" <?=$row->leclab == "LAB" ? " checked" : ""?> /></td>
                                    
                                    <td hidden="">
                                        <select name="course" id="course" class="course chosen" style="width: 200px;" >
                                            <?= $this->setup->generateCourseDropdown($course) ?>
                                        </select>
                                    </td>
                                    <td width="8%" class="align_center forteaching" hidden="">
                                        <select name="section" id="section" class="section chosen" style="width: 200px;" >
                                            <?= $this->setup->generateSectionDropdown($course, $section) ?>
                                        </select>
                                    </td>
                                    <td width="8%" class="align_center forteaching">
                                        <select name="subject" id="subject" class="subject chosen" style="width: 200px;" disabled >
                                            <?= $this->setup->generateSubjectDropdown($course, $subject) ?>
                                        </select>
                                    </td>
                                    <td width="8%" class="forteaching">
                                        <select class="chosen" name="aimsdept" disabled >
                                            <?
                                                $aimstype = $CI->utils->getAIMSDepartment();
                                                foreach($aimstype as $key =>$value){
                                                ?><option <?=($key==$row->aimsdept ? " selected" : "")?> value="<?=$key?>"><?=$value?></option><?    
                                                }
                                            ?>
                                        </select>
                                    </td>

                                  </tr>
                            <?

                            $prev_day = $row->dayofweek;
                        }
                    }?>
                    </tbody>
                </table>
        </div>
        <div class="form_row" style="border: transparent !important;">
            <label class="field_name align_left " style="width: 5%">Reason</label>
            <div>
                <textarea class="form-control" rows="4" class="align_left" name="reason" id="reason" style="width: 95%;margin-left: 5%;" placeholder="Reason" required="" readonly=""><?=$row->reason?></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <?if(!in_array($status,array("APPROVED","DISAPPROVED"))){?>
                <button type="button" id="approve" class="btn btn-success">Save</button>
                <?}?>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
$("#approve").click(function(){
    var newstat = $("#mh_status").val();
    var iscontinue = true;
    
    $( "#statusAlert" ).remove();
                
    if(newstat == "APPROVED")
    {
        $(".isreq").each(function(){
            if($(this).val() == ""){
                $(this).css("border-color","red").attr("placeholder", "This field is required!.");  
                iscontinue = false;
            }   
            else{
                $(this).css("border-color","").attr("placeholder", "");  
            }
        });
    }
    else
    {
        $(".isreq").each(function(){
            if($(this).val() != ""){
                if(jQuery.inArray($("#mh_status").val(), ["APPROVED","DISAPPROVED"]) == -1)
                {
                    $( "<span id='statusAlert' style='color:red;margin-left:23%;'><b>This field is required!.</b></span>" ).insertAfter( "#mh_statusdiv" );
                    iscontinue = false;
                }
            }
        });
    }
    
    if(iscontinue)
    {
        var pars2 = "~u~"; 
        var schedule = "";
        $("#schedule").find("tr[tag='grp']").each(function(){
          if($(this).find("input[name='fromtime']:first").val() && $(this).find("input[name='totime']:first").val()){
            schedule += schedule ? "|" : ""; 
            schedule += $(this).attr("detail_id");
            schedule += pars2;
            schedule += $(this).attr("dayofweek");
            schedule += pars2;
            schedule += $(this).attr("dayidx");
            schedule += pars2;
            schedule += $(this).find("input[name='fromtime']:first").val()+ " - " + $(this).find("input[name='totime']:first").val();
            schedule += pars2;
            schedule += $(this).find("input[name='tardy_f']:first").val();
            schedule += pars2;
            schedule += $(this).find("input[name='absent_f']:first").val();
            schedule += pars2;
            schedule += $(this).find("input[name='absent_e']:first").val();
            schedule += pars2;
            schedule += $(this).find("input[name='early_d']:first").val();
            schedule += pars2;
            schedule += ($(this).find("input[name='leclab']:checked").val() === undefined ? "" : $(this).find("input[name='leclab']:checked").val());
          }
        });
         var   form_data = "timesched=" + schedule;
               form_data += "&status=" + newstat;
               form_data += "&colhead=<?=$colhead?>";
               form_data += "&isLastApprover=<?=$isLastApprover?>";
               form_data += "&code_request=<?=$code_request?>";
               form_data += "&csid=<?=$csid?>";
               form_data += "&base_id=<?=$base_id?>";
               form_data += "&employeeid=<?=$employeeid?>";
               form_data += "&date_active="+ $('input[name=dfrom]').val();
               form_data += "&reason="+$("textarea[name=reason]").val();
               form_data += "&course="+$("#course").val();

        $.ajax({
            url:"<?=site_url("schedule_/saveSchedStatusChange")?>",
            type:"POST",
            dataType : 'JSON',
            data:form_data,
            success: function(msg){
                $("#close").click();
                alert(msg.msg);
                if(msg.err_code == 0){
                    location.reload();
                }
            }
        });
    }
});

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$(".chosen").chosen();

$('.time').datetimepicker({
    format: 'LT'
});

</script>