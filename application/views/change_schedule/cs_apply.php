<?php
$empinfo = $this->session->userdata("personalinfo"); 
$empdetails = $empinfo[0];


$employeeid = $empdetails['employeeid'];
$aimsdept = $empdetails['aimsdept'];
$datetoday = date("Y-m-d");

$datetoday = date("Y-m-d");
$isHead = $this->utils->checkIfHead($this->session->userdata('username'));
$isHead = false; //< ica-hyperion21204
$tnt = $this->employee->getempdatacol('teachingType',$this->session->userdata('username'));

# newly added for ica-hyperion 21194
# by justin (with e)
$empID = $this->session->userdata('username');
$isAdmin = $this->utils->findIfAdmin($empID);
# end for ica-hyperion 21194
$usertype = $this->session->userdata("usertype");
if($usertype == "EMPLOYEE"){
   $ishidden   = " hidden";
   $isdisabled = " style='pointer-events: none;'";
   $isreadonly = " style='pointer-events: none;'";
   // $cansave   = $this->db->query("SELECT * FROM employee_restriction WHERE employeeid='$employeeid'")->num_rows();
 }
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
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog modal-lg" style="width: 1500px !important;">
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
            <center><b><h3 tag="title" class="modal-title">Add Change Schedule Application</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="form_row">
                <!-- <input type="hidden" name="othleave" id="othleave"> -->
                <label class="field_name align_right" style="color: darkblue;">Upload Proof Image</label>
                <div class="field">
                    <input type="file" name="el_document" class="file" id="uploadFile" value=""/>
                </div>
            </div>
            <div class="form_row" style="margin-bottom: 0px;<?=($isAdmin) ? '' : 'visibility: hidden;'?>">
                <label class="field_name align_right">Will be approve by approver?</label>
                <div class="field no-search">
                    <div class="col-md-12" style="padding-left: 0px;">
                        <select class="form-control" name="allowSeq" id="allowSeq" style="width: 19%;">
                            <option value="1" <?=($isAdmin) ? "selected" : ""?>>YES</option><!--  kapag yes, dadaan sa sequence approver -->
                            <option value="0">NO</option><!--  < kapag no, deretso approved na -->
                        </select>
                    </div>
                </div>
            </div> 
            <div class="form_row" <?=($usertype == "ADMIN") ? "" : "style='display:none;'" ?>>
                <label class="field_name align_right">Type</label>
                <div class="field" >
                    <input type="radio" name="tnt" value="teaching" checked=""> Teaching &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="tnt" value="nonteaching"> Non-Teaching
                </div>
            </div>
            <div class="form_row" style="display: none;" readonly <?=($usertype == "EMPLOYEE") ? "" : "style='display:none;'" ?>>
                <label class="field_name align_right">Type</label>
                <div class="field" >
                    <input type="radio" name="tnt" value="teaching" checked=""> Teaching &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="tnt" value="nonteaching"> Non-Teaching
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Date Active</label>
                <div class="field" style="padding-bottom: 10px;">
                    <div class="input-group date" id="dfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 19%;">
                        <input class="form-control" size="16" name="dfrom" type="text" value="">
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <!-- ///< For change schedule on specific dates only -->
               <div class="form_row">
                  <div class="col-md-12" style="padding-left: 0px;">
                      <div class="col-md-2"></div>
                      <label class="col-md-5" style="width: 29.666667%;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" class="double-sized-cb" name="specific" id="ctisdo" value="1">&nbsp;&nbsp; <b>Check this if specific dates only</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Date From</label>
                      <div class="col-md-2" style="padding-right: 0px;padding-left: 0px;">
                        <div class="input-group date" id="start" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="form-control" size="16" name="start" type="text" value="" readonly>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                      </div>
                      <span class="col-md-1" id="timeto_text" style="padding-left: 0px;padding-right: 0px;width: 3.333333%;">&nbsp;&nbsp;&nbsp;<b>TO</b></span>
                      <div class="col-md-2" style="padding-left: 0px;">
                        <div class="input-group date" id="end" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="form-control" size="16" name="end" type="text" value="" readonly>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                      </div>
                  </div>
                </div>

            <div class="form_row">
                <label class="field_name align_right">Employee</label>
                <?php if($usertype == "ADMIN"): ?>
                  <div class="field">
                      <select class="chosen" id="employeeid" name="employeeid" multiple <?=$is_disabled?> >
                          <?
                              foreach ($emplist as $code => $desc) {?>
                                  <option value="<?=$code?>"><?=$desc?></option>
                              <?}
                          ?>
                      </select>&nbsp;&nbsp;
                      <!-- <span id="loadingemp" hidden=""></span> -->
                  </div>
                <?php endif ?>
                <?php if($usertype == "EMPLOYEE"): ?>
                  <div class="field">
                      <select class="chosen" id="employeeid" name="employeeid" disabled="disabled" >
                        <option value="<?=$empID?>"><?= $this->extensions->getEmployeeName($empID)?></option>
                      </select>&nbsp;&nbsp;
                      <!-- <span id="loadingemp" hidden=""></span> -->
                  </div>
                <?php endif ?>
            </div>
            <div class="form_row" style="border: transparent !important;">
                <table class="table table-striped table-bordered table-hover" width="100%" border="1" id="sched_table">
                    <thead>
                        <tr>
                            <th rowspan="2"></th>
                            <th rowspan="2"></th>
                            <th rowspan="2">Day of Week</th>
                            <th rowspan="2">From</th>
                            <th rowspan="2">To</th>
                            <th class="adminfields" colspan="2" hidden="">First Half</th>
                            <th class="adminfields" rowspan="2" hidden="">Early Dismissal</th>
                            <th rowspan="2">Remove Schedule</th>
                            <!-- <th class="align_center forteaching" rowspan="2">Lec</th> -->
                            <!-- <th class="align_center forteaching" rowspan="2">Lab</th> -->
                            <?php
                                if(!$usertype == "ADMIN"){
                            ?>
                                <th class="align_center forteaching">Course</th>
                                <th class="align_center forteaching">Section</th>
                            <?php
                                    }
                            ?>
                            <th rowspan="2" class="align_center forteaching">Subject</th>
                            <th rowspan="2" class="align_center forteaching">AIMS Dept</th>
                        </tr>
                        <tr class="adminfields" hidden="">
                            <th>Tardy Start</th>
                            <th>Absent Start</th>
                        </tr>
                    </thead>
                    <tbody id="schedule">
                        <?
                        // echo '<pre>';print_r($scheddays);
                        foreach ($scheddays as $index => $row) {
                        ?>
                        <tr tag="grp" dayofweek="<?=$row['day_code']?>" dayidx="<?=$index?>" counter='<?=$row['day_code']?>-1'>
                            <td width="7%">
                                <div class="btn-group">
                                    <a class="btn btn-info" href="#" tag="copy_sched"  title="Copy"><i class="icon-copy"></i></a>
                                    <a class="btn btn-info" href="#" tag="paste_sched" title="Paste"><i class="icon-paste"></i></a>
                                </div>
                            </td>
                            <td width="3%">
                                <div class="btn-group">
                                    <a class="btn btn-primary" href="#" tag="add_sched"><i class="glyphicon glyphicon-plus"></i></a>
                                </div>
                            </td>
                            <td width="4%"><?=$row['day_name']?></td>
                            <td width="15%">
                                <div class='input-group time'>
                                    <input class="input-small align-center form-control fromtime" type="text" name="fromtime" value="" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </td>
                            <td width="15%">
                                <div class='input-group time'>
                                    <input class="input-small align-center form-control totime" type="text" name="totime" value="" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </td>
                            <td width="8%" class="adminfields" hidden="">
                                <div class='input-group time'>
                                    <input class="input-small align-center form-control" type="text" name="tardy_f" value="" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </td>
                            <td width="8%" class="adminfields" hidden="">
                                <div class='input-group time'>
                                    <input class="input-small align-center form-control" type="text" name="absent_f" value="" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </td>
                            <td width="8%" hidden="">
                                <div class='input-group time'>
                                    <input class="input-small align-center form-control" type="text" name="absent_e" value="" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </td>
                            <td width="8%" class="adminfields" hidden="">
                              <div class='input-group time'>
                                  <input class="input-small align-center form-control" type="text" name="early_d" value="" />
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                              </div>
                            </td>
                            <td width="5%" class="align_center"><input type='checkbox' class="double-sized-cb toremove" name='toremove' value='checked'></input></td>
                            <!-- <td width="5%" class="align_center forteaching"><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LEC" /></td> -->
                            <!-- <td width="5%" class="align_center forteaching"><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LAB" /></td> -->
                            <?php
                                if(!$usertype == "ADMIN"){
                            ?>
                                    <td width="8%" class="align_center forteaching">
                                        <select name="course" id="course" class="course chosen" style="width: 200px;" >
                                            <?= $this->setup->generateCourseDropdown() ?>
                                        </select>
                                    </td>
                                    <td width="8%" class="align_center forteaching">
                                        <select name="section" id="section" class="section chosen" style="width: 200px;" >
                                            <?= $this->setup->generateSectionDropdown() ?>
                                        </select>
                                    </td>
                            <?php
                                }
                            ?>
                            <td width="8%" class="align_center forteaching">
                                <select name="subject" id="subject" class="subjects chosen" style="width: 200px;" >
                                    <?= $this->setup->generateSubjectDropdown() ?>
                                </select>
                            </td>
                            <td width="8%" class="forteaching">
                                <select class="chosen aimsdepts" name="aimsdept" >
                                    <?
                                        $aimstype = $this->utils->getAIMSDepartment();
                                        foreach($aimstype as $key =>$value)
                                        {
                                            ?><option <?=($key==$aimsdept ? " selected" : "")?> value="<?=$key?>"><?=$value?></option><?    
                                        }
                                    ?>
                                </select>
                                <!-- <select name="aimsdept" class="aimsdept chosen" style="width: 200px;" >
                                <option value="" selected=""  >Choose Aims department..</option>
                                <?php if(isset($aimsdept_arr)) foreach ($aimsdept_arr as $key => $desc) { ?>
                                <option value="<?=$key?>"><?=$desc?></option>
                                <? } ?>
                                </select> -->
                            </td>
                        </tr>
                        <?}?>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="form_row" style="border: transparent !important;">
                <label class="field_name align_left " style="width: 5%">Reason</label>
                <div style="margin-left:10px;">
                    <textarea rows="4" class="form-control" name="reason" id="reason" style="width: 95%;margin-left: 5%;" placeholder="Reason" required=""></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="save" class="btn btn-success">Apply</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var schedarr = [];
    var selEmp = [];
    var checkboxCounterforAppend = aimsdeptCounterforAppend = aimsdeptCounterforAppend = 0;
    $(document).ready(function() {

        // end new function for validation in time
        // start of saving
        var base64String = '';
        if (window.File && window.FileReader && window.FileList && window.Blob){
            if(typeof(handleFileSelect) === undefined){
                document.getElementById('uploadFile').addEventListener('change', handleFileSelect, false);
            }
        } else {
            alert('The File APIs are not fully supported in this browser.');
        }

        $(".date").datetimepicker({
            format: "YYYY-MM-DD"
        });

        $('.time').datetimepicker({
            format: 'LT'
        });

        $(".chosen").chosen();
    });

    $("#sched_table").on("click", "a[tag='add_sched']", function(){
        checkboxCounterforAppend = checkboxCounterforAppend+1;
        aimsdeptCounterforAppend = aimsdeptCounterforAppend+1;
        var obj = $(this).parent().parent().parent().clone();
        var dayofweek = $(obj).closest("tr").attr("dayofweek");
        $(obj).closest("tr").attr("counter", dayofweek+"-"+aimsdeptCounterforAppend);
        var copy_button  = $('<a class="btn" href="#" tag="copy_sched"  title="Copy"><i class="icon-copy"></i></a>').click(function(){var obj = $(this).parent().parent().parent();$("a[tag='copy_sched']").each(function(){   $(this).css({"color":"","background-color":""});    });$(this).css({"color":"#D10303","background-color":"#BABABA"});copytime(obj);});
        var paste_button = $('<a class="btn" href="#" tag="paste_sched" title="Paste"><i class="glyphicon glyphicon-paste"></i></a>').click(function(){var obj = $(this).parent().parent().parent();pastetime(obj);});
        var delete_button = $("<a class='btn btn-danger' href='#'  tag='delete_sched'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){$(this).parent().parent().remove();});
        var timefrom_picker = $('<input class="form-control ftime" id="ftime" type="text" name="fromtime" />');
        var totime_picker   = $('<input class="form-control" type="text" name="totime" />');
        var tardy_f_picker  = $('<input class="form-control" type="text" name="tardy_f" />');
        var absent_f_picker = $('<input class="form-control" type="text" name="absent_f" />');
        var absent_e_picker = $('<input class="form-control" type="text" name="absent_e" />');
        var early_d_picker  = $('<input class="form-control" type="text" name="early_d" />');
        var aimsDept = $('<select name="aimsdepts" id="aimsdepts" class="form-control chosen-select aimsdepts" style="width: 220px;" ></select>');
        var subjects = $('<select name="subjects" id="subjects" class="form-control chosen-select subjects" style="width: 220px;" ></select>');
        var sections = $('<select name="sections" id="sections" class="form-control  chosen-select sections" style="width: 220px;" ></select>');
        // var course = $('<select name="courses" id="courses" class="form-control chosen-select courses" style="width: 220px;" ></select>');

        $(obj).find("td:first").find("div:first").html("").append($(copy_button)).append($(paste_button));
        $(obj).find("td:eq(0)").html($(delete_button)).css("padding-left", "47px");
        $(obj).find("td:eq(1)").css("color","#F5F5F5");
        $(obj).find("td:eq(3)").find("div:first").html("");
        $(obj).find("td:eq(3)").find("div:first").append($(timefrom_picker));
        $(obj).find("td:eq(3)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
        $(obj).find("td:eq(4)").find("div:first").html("");
        $(obj).find("td:eq(4)").find("div:first").append($(totime_picker));
        $(obj).find("td:eq(4)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
        $(obj).find("td:eq(5)").find("div:first").html("");
        $(obj).find("td:eq(5)").find("div:first").append($(tardy_f_picker));
        $(obj).find("td:eq(5)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
        $(obj).find("td:eq(6)").find("div:first").html("");
        $(obj).find("td:eq(6)").find("div:first").append($(absent_f_picker));
        $(obj).find("td:eq(6)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
        $(obj).find("td:eq(7)").find("div:first").html("");
        $(obj).find("td:eq(7)").find("div:first").append($(early_d_picker));
        $(obj).find("td:eq(7)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");

        // $(obj).find("td:eq(10)").find("div:first").html("");
        // $(obj).find("td:eq(10)").find("div:first").append("<input type='checkbox' class='double-sized-cb cblec' id='cblec"+checkboxCounterforAppend+"' counter='"+checkboxCounterforAppend+"' name='leclab' value='LEC' />");
        // $(obj).find("td:eq(11)").find("div:first").html("");
        // $(obj).find("td:eq(11)").find("div:first").append("<input type='checkbox' class='double-sized-cb cblab' id='cblab"+checkboxCounterforAppend+"' counter='"+checkboxCounterforAppend+"' name='leclab' value='LAB' />");
        $(obj).find("td:eq(10)").find("div:first").html("");
        // $(obj).find("td:eq(10)").find("div:first").append($(subjects));

        $.ajax({
            url: "<?=site_url("schedule_/loadSubject")?>",
            success : function(ret)
            {
                // $(".subjects").html(ret);
                $(".subjects").chosen();
            }
        });

        $(obj).find("td:eq(11)").find("div:first").html("");
        // $(obj).find("td:eq(11)").find("div:first").append($(aimsDept));
        $.ajax({
            url: "<?=site_url("schedule_/loadSelectAimsDept")?>",
            success : function(ret)
            {
                // $(".aimsdepts").html(ret);
                $(".aimsdepts").chosen();
            }
        });
        $(obj).find("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='absent_e'],input[name='early_d'], .time").datetimepicker({
            format: "LT"}); 

        $(obj).find('.cblec').click(function(){
            var counterCB = $(this).attr("counter");
            $("#cblab"+counterCB).prop("checked", false);
        });

        $(obj).find('.cblab').click(function(){
            var counterCB = $(this).attr("counter");
            $("#cblec"+counterCB).prop("checked", false);
        });

        $(obj).insertAfter($(this).parent().parent().parent());   
    }); 
    
    $("a[tag='delete_sched']").click(function(){    
        var tr_id = $(this).closest("tr").attr("dayofweek");
        $("tr[dayofweek='"+ tr_id +"']").find(".fromtime").val('');
        $("tr[dayofweek='"+ tr_id +"']").find(".totime").val('');
        $("tr[dayofweek='"+ tr_id +"']").find(".toremove").prop("checked", false);
        $("tr[dayofweek='"+ tr_id +"']").find(".leclab").prop("checked", false);
        $("tr[dayofweek='"+ tr_id +"']").find(".subject").val('').trigger("chosen:updated");
        $("tr[dayofweek='"+ tr_id +"']").find(".aimsdept").val('').trigger("chosen:updated");     
    });

    $("#allowSeq").change(function(){
        if($(this).val() == 1) $(".adminfields").hide();
        else $(".adminfields").show();
    });

    $("#employeeid").change(function(){
        validateFinalizedEmployee();
    });

    function validateFinalizedEmployee(){
        var dtActive = '';
        var selEmp = $("#employeeid").val(); // get all employee id
        // get date
        dtActive = $("input[name=dfrom]").val();
        if($("#ctisdo").is(":checked")) dtActive = $("input[name=start]").val();
        // find here if finalized
        $.ajax
        ({
            url      :   "<?=site_url("schedule_/validateFinalizedEmp")?>",
            type     :   "POST",
            data     :   { eids  : ''+selEmp+'', 
            dfrom : dtActive},
            success  :   function(msg)
            {
            /*if(msg != ""){
            alert(msg);
            }*/
            }
        });
    }

    // new function for validation in time
    // author : Justin (with e)
    function convertTimeToNumber(time_val){
        const [time, modifier] = time_val.split(' ');

        let [hours, minutes] = time_val.split(':');

        if (hours === '12') 
        {
            hours = '00';
        }

        if (modifier === 'PM') 
        {
            hours = parseInt(hours, 10) + 12;
        }

        hours = parseInt(hours);
        minutes = parseInt(minutes) / 60;
        return hours + minutes;
    }

    function convertToDay(index){
        var day = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        return day[index];
    }

    $("#save").unbind().click(function(){
        if($("#reason").val() == ""){
            $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
            return false;
        }


        var pars2 = "~u~"; 
        var schedule = "";
        var fileName = "";
        var file = "";
        var mime = "";
        var error_msg = tfrom = tto = "";
        if($("#uploadFile").val() != ""){
            var fileName = $("#uploadFile").val();
            var file = $("#uploadFile")[0].files[0];
            var mime = $("#uploadFile")[0].files[0].type;
        }
        $("#schedule").find("tr[tag='grp']").each(function(){
            if(($(this).find("input[name='fromtime']:first").val() && $(this).find("input[name='totime']:first").val()) || $(this).find("input[name='toremove']").is(":checked")){
                schedule += schedule ? "|" : ""; 
                schedule += $(this).attr("dayofweek");
                schedule += pars2;
                schedule += $(this).attr("dayidx");
                schedule += pars2;
               // validate here if time from is  greater than to time to. If greater than, log to error_msg
               // author : justin (with e)
                tfrom = convertTimeToNumber($(this).find("input[name='fromtime']:first").val());
                tto = convertTimeToNumber($(this).find("input[name='totime']:first").val());
                if(tfrom > tto) error_msg = error_msg + "* " + convertToDay($(this).attr("dayidx")) +"\n";
                // end of validation
                schedule += $(this).find("input[name='fromtime']:first").val() + "-" + $(this).find("input[name='totime']:first").val();
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
                schedule += pars2;
                schedule += ($(this).find("input[name='toremove']:checked").val() === undefined ? "" : $(this).find("input[name='toremove']:checked").val());  
                schedule += pars2;
                schedule += /*$(this).find("select[name='course']:first").val()*/ "";
                schedule += pars2;
                schedule += /*$(this).find("select[name='section']:first").val()*/ "";
                schedule += pars2;
                schedule += $(this).find("select[name='subject']:first").val();
                schedule += pars2;
                schedule += $(this).find("select[name='aimsdept']:first").val();
                schedule += pars2;                                           
            }
        });

        if(!schedule){
            alert("Please fill-up schedule fields.");
            return;
        }

       var filename = $("input[name='el_document']").val().replace(/C:\\fakepath\\/i, '');
       if($("#employeeid").val() != null){
            // check if no error
            if(error_msg != "")
                {
                    alert("Please enter a valid time. \n\nList of days have error in time : \n"+error_msg);
                    return;
                }
            var form_data = "dfrom="+$("input[name='dfrom']").val()+"&eids="+$("#employeeid").val();
               form_data += "&timesched=" + schedule;
               form_data += "&model=requestsched";
               form_data += "&el_document="+$('input[name=el_document]').val();
               form_data += "&tnt="+$('input[name=tnt]:checked').val();
               form_data += "&specific="+$('input[name=specific]').is(":checked");
               form_data += "&start="+$('input[name=start]').val();
               form_data += "&end="+$('input[name=end]').val();
               form_data += "&reason="+$('#reason').val();
               form_data += "&allowseq="+$('#allowseq').val();
               form_data += "&el_document="+$('#el_document').val();
               form_data += "&isAdmin="+"<?=$isAdmin?>";

            $.ajax({
                url: "<?=site_url("schedule_/saveSchedApp")?>",
                data : form_data,
                type : "POST",
                success:function(msg){
                    alert(msg);
                    // $("#myModal").modal("toggle");
                    view_cs_status();
                }
            });
        }
        else    alert("Please select employee first..");
    });

    // end of saving

    $("input[name=tnt]").on('change', function() {
        $("#loadingemp").show().html("<img src='<?=base_url()?>images/loading.gif' />");
        var tnt = $('input[name=tnt]:checked').val();
        if(tnt=='teaching') $('.forteaching').show();
        else                $('.forteaching').hide();
        $.ajax
        ({
            url      :   "<?=site_url("utils_/getEmpListSched")?>",
            type     :   "POST",
            data     :   {tnt:tnt},
            success  :   function(ret)
            {
                $("select[name='employeeid']").html(ret).trigger("chosen:updated");
                $("#loadingemp").hide();
            }
        });
    });

    $(".leclab").on('change', function() {
        $(this).closest('tr').find(".leclab").not(this).prop('checked', false);
    });

    $("a[tag='copy_sched']").click(function(){  var obj = $(this).parent().parent().parent();   copytime(obj);
        $("a[tag='copy_sched']").each(function(){   $(this).css({"color":"","background-color":""});    });
        $(this).css({"color":"#D10303","background-color":"#BABABA"});
    });

    $("a[tag='paste_sched']").click(function(){ var obj = $(this).parent().parent().parent();   pastetime(obj); });
    ///<  @Angelica functions for change schedule with specific dates only
    $('input[name=specific],input[name=start],input[name=end]').on('change blur',function(){
      
        if($('input[name=specific]').is(":checked")){
            $("input[name='dfrom']").val('');
            $("input[name='dfrom']").attr("readonly", true);
            $("input[name=start],input[name=end]").attr("readonly", false);

            var start = $("input[name='start']").val();
            var end = $("input[name='end']").val();

            if(start != '' && end != ''){
                $.ajax({
                    url      :   "<?=site_url("schedule_/getDayofweekFromDates")?>",
                    type     :   "POST",
                    data     :   {start:start, end:end},
                    success  :   function(ret)
                    {
                        var arr_dow = JSON.parse(ret);
                        $('tr[tag=grp]').each(function()
                        {
                            var dayidx = $(this).attr('dayidx');
                        ///< only show dayidx in daterange
                            if($.inArray(dayidx,arr_dow) < 0)
                            {
                                $("tr[dayidx='"+dayidx+"'] ").prop('hidden',true);
                            }
                            else
                            {
                                $("tr[dayidx='"+dayidx+"'] ").prop('hidden',false);
                            }
                        });
                    }
                });
            }
        }
        else{
            $("input[name='start']").val('');
            $("input[name='end']").val('');
            $("input[name='dfrom']").attr("readonly", false);
            $("input[name=start],input[name=end]").attr("readonly", true);
            $('tr[tag=grp]').prop('hidden',false);
        }
    });

    $(".course").change(function(){
        var trid = $(this).closest("tr").attr("dayidx");
        getAvailableSection($(this).val(), trid);
        getAvailableSubject($(this).val(), trid);
    });

    function getAvailableSection(course, trid){
      $.ajax({
        url: "<?= site_url('setup_/getAvailableSection') ?>",
        type: "POST",
        data:{course:course},
        success:function(response){
          $("[dayidx="+trid+"]").find(".section").html(response).trigger("chosen:updated");
        }
      });
    }

    function getAvailableSubject(course, trid){
      $.ajax({
        url: "<?= site_url('setup_/getAvailableSubject') ?>",
        type: "POST",
        data:{course:course},
        success:function(response){
          $("[dayidx="+trid+"]").find(".subject").html(response).trigger("chosen:updated");
        }
      });
    }
    ///< modified @Angelica for schedule copy and paste per day

    function getSubjectForPaste(course, trid, value){
        $.ajax({
            url: "<?= site_url('setup_/getAvailableSubject') ?>",
            type: "POST",
            data:{course:course},
            success:function(response){
                $("[counter="+trid+"]").find(".subjects").html(response).val(value).trigger("chosen:updated");
            }
        });
    }

    function copytime(obj){
        if(schedarr.length > 0)  schedarr = [];

        var schedarr_temp = [];
        $('tr[dayofweek='+obj.attr('dayofweek')+']').each(function(){
          var from = $(this).find("input[name='fromtime']").val();
          var to   = $(this).find("input[name='totime']").val();
          var tardy_f = $(this).find("input[name='tardy_f']").val();
          var absent_f = $(this).find("input[name='absent_f']").val();
          var absent_e = $(this).find("input[name='absent_e']").val();
          var early_d = $(this).find("input[name='early_d']").val();
          var subjects  = $(this).find(".subjects").val();
          var aimsdepts  = $(this).find(".aimsdepts").val();

          if(from != '' || to != ''){
              schedarr_temp = {
                'fromtime'  :from,
                'totime'    :to,
                'tardy_f' :tardy_f,
                'absent_f' :absent_f,
                'absent_e' :absent_e,
                'early_d' :early_d,
                'subjects' :subjects,
                'aimsdepts' :aimsdepts,
              };
              schedarr.push(schedarr_temp);
          }
        });
        
    }

    function pastetime(obj){
        var schedarr_orig       = [],
            schedarr_orig_temp  = [];
        
        $('tr[dayofweek='+obj.attr('dayofweek')+']').each(function(){
            var from = $(this).find("input[name='fromtime']").val();
            var to   = $(this).find("input[name='totime']").val();
            var tardy_f = $(this).find("input[name='tardy_f']").val();
            var absent_f = $(this).find("input[name='absent_f']").val();
            var absent_e = $(this).find("input[name='absent_e']").val();
            var early_d = $(this).find("input[name='early_d']").val();
            var subjects  = $(this).find(".subjects").val();
            var aimsdepts  = $(this).find(".aimsdepts").val();

            if(from != '' || to != ''){
                schedarr_orig_temp = {
                  'fromtime'  :from,
                  'totime'    :to,
                  'tardy_f' :tardy_f,
                  'absent_f' :absent_f,
                  'absent_e' :absent_e,
                  'early_d' :early_d,
                  'subjects' :subjects,
                  'aimsdepts' :aimsdepts,
                };
                schedarr_orig.push(schedarr_orig_temp);
            }

            $(this).find("a[tag=delete_sched]").click();
        });
        if(schedarr_orig.length == 0){
          if(schedarr.length > 0){
            console.log(schedarr);
            obj.find("input[name='fromtime']").val(schedarr[0]['fromtime']);
            obj.find("input[name='totime']").val(schedarr[0]['totime']);
            obj.find("input[name='tardy_f']").val(schedarr[0]['tardy_f']);
            obj.find("input[name='absent_f']").val(schedarr[0]['absent_f']);
            obj.find("input[name='absent_e']").val(schedarr[0]['absent_e']);
            obj.find("input[name='early_d']").val(schedarr[0]['early_d']);
            getSubjectForPaste(schedarr[0]['aimsdepts'], obj.closest("tr").attr("counter"), schedarr[0]['subjects']);
            obj.find(".aimsdepts").val(schedarr[0]['aimsdepts']).trigger("chosen:updated");

            if(schedarr.length > 1){
                for (var i = schedarr.length - 1; i >= 1; i--) {
                    $(obj).find("a[tag=add_sched]").click();
                    $(obj).next(':first').find("input[name='fromtime']").val(schedarr[i]['fromtime']);
                    $(obj).next(':first').find("input[name='totime']").val(schedarr[i]['totime']);
                    $(obj).next(':first').find("input[name='tardy_f']").val(schedarr[i]['tardy_f']);
                    $(obj).next(':first').find("input[name='absent_f']").val(schedarr[i]['absent_f']);
                    $(obj).next(':first').find("input[name='absent_e']").val(schedarr[i]['absent_e']);
                    $(obj).next(':first').find("input[name='early_d']").val(schedarr[i]['early_d']);
                    $(obj).next(':first').find(".subjects").val(schedarr[i]['subjects']).trigger("chosen:updated");
                    $(obj).next(':first').find(".aimsdepts").val(schedarr[i]['aimsdepts']).trigger("chosen:updated");
                }
            }
          }
        }else if(schedarr_orig.length > 0){
          if(schedarr.length > 0){
            for (var i = schedarr.length - 1; i >= 0; i--) {
                $(obj).find("a[tag=add_sched]").click();
                $(obj).next(':first').find("input[name='fromtime']").val(schedarr[i]['fromtime']);
                $(obj).next(':first').find("input[name='totime']").val(schedarr[i]['totime']);
                $(obj).next(':first').find("input[name='tardy_f']").val(schedarr[i]['tardy_f']);
                $(obj).next(':first').find("input[name='absent_f']").val(schedarr[i]['absent_f']);
                $(obj).next(':first').find("input[name='absent_e']").val(schedarr[i]['absent_e']);
                $(obj).next(':first').find("input[name='early_d']").val(schedarr[i]['early_d']);
                $(obj).next(':first').find(".subjects").val(schedarr[i]['subjects']).trigger("chosen:updated");
                $(obj).next(':first').find(".aimsdepts").val(schedarr[i]['aimsdepts']).trigger("chosen:updated");
            }
          }
        }

        if(schedarr_orig.length == 1){
          obj.find("input[name='fromtime']").val(schedarr_orig[0]['fromtime']);
          obj.find("input[name='totime']").val(schedarr_orig[0]['totime']);
          obj.find("input[name='tardy_f']").val(schedarr_orig[0]['tardy_f']);
          obj.find("input[name='absent_f']").val(schedarr_orig[0]['absent_f']);
          obj.find("input[name='absent_e']").val(schedarr_orig[0]['absent_e']);
          obj.find("input[name='early_d']").val(schedarr_orig[0]['early_d']);
          obj.find(".subjects").val(schedarr_orig[0]['subjects']).trigger("chosen:updated");
          obj.find(".aimsdepts").val(schedarr_orig[0]['aimsdepts']).trigger("chosen:updated");
        }else if(schedarr_orig.length == 0){

        }

        if(schedarr_orig.length > 1){
            for (var i = schedarr_orig.length - 1; i > 0; i--) {
                $(obj).find("a[tag=add_sched]").click();
                $(obj).next(':first').find("input[name='fromtime']").val(schedarr_orig[i]['fromtime']);
                $(obj).next(':first').find("input[name='totime']").val(schedarr_orig[i]['totime']);
                $(obj).next(':first').find("input[name='tardy_f']").val(schedarr_orig[i]['tardy_f']);
                $(obj).next(':first').find("input[name='absent_f']").val(schedarr_orig[i]['absent_f']);
                $(obj).next(':first').find("input[name='absent_e']").val(schedarr_orig[i]['absent_e']);
                $(obj).next(':first').find("input[name='early_d']").val(schedarr_orig[i]['early_d']);
                $(obj).next(':first').find(".subjects").val(schedarr_orig[i]['subjects']).trigger("chosen:updated");
                $(obj).next(':first').find(".aimsdepts").val(schedarr_orig[i]['aimsdepts']).trigger("chosen:updated");
            }
        }
    }
</script>

<script type="text/javascript" src="<?=base_url()?>/js/timeValidation.js"></script>