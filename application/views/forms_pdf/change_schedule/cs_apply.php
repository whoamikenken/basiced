<?php

$datetoday = date("d-m-Y");

$datetoday = date("d-m-Y");
$isHead = $this->utils->checkIfHead($this->session->userdata('username'));
$isHead = false; //< ica-hyperion21204
$tnt = $this->employee->getempdatacol('teachingType',$this->session->userdata('username'));

# newly added for ica-hyperion 21194
# by justin (with e)
$empID = $this->session->userdata('username');
$isAdmin = $this->utils->findIfAdmin($empID);
# end for ica-hyperion 21194

$is_disabled = ($this->utils->checkIfHead($this->session->userdata('username')) || $isAdmin) ? "" : "disabled";
?>
<style>
.modal{
    width: 75%;
    left: 0;
    right: 0;
    margin: auto;
}
#reason
{
  resize: none;
}
</style>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="70px"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                </tr>
                <tr>
                    <td><strong>Change Schedule Request Form</strong></td>
                </tr>
            </table>
        </div>
        <div class="modal-body">
            <div class="form_row" style="display: none;">
                <label class="field_name align_right">Type</label>
                <div class="field">
                    <input type="radio" name="tnt" value="teaching" <?=($tnt == "teaching") ? "checked" : ""?> <?=$is_disabled?>> Teaching &nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="tnt" value="nonteaching" <?=($tnt == "nonteaching") ? "checked" : ""?> <?=$is_disabled?>> Non-Teaching
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Date Active</label>
                <div class="field">
                    <div class="input-group date" id="dfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="dfrom" type="text" value="" readonly>
                        <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                    </div>
                </div>
            </div>

            <!-- ///< For change schedule on specific dates only -->

             <div class="form_row">
                <div class="field"  style="padding-bottom: 10px;">
                 <input type="checkbox" class="double-sized-cb" name="specific" id="ctisdo" value="1">&nbsp;&nbsp; <b>Check this if specific dates only</b>
                    &nbsp;&nbsp;From
                    <div class="input-group date" id="start" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="start" type="text" value="" readonly>
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                    &nbsp;&nbsp;To&nbsp;&nbsp;
                    <div class="input-group date" id="end" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="end" type="text" value="" readonly>
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                </div>
            </div>

            <div class="form_row">
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <select class="chosen col-md-6" id="employeeid" name="employeeid" multiple="" <?=$is_disabled?>>
                        <? 
                           foreach ($emplist as $code => $desc): 
                              $is_selected = ($code == $this->session->userdata('username')) ? "selected" : "";
                        ?>
                              <option value="<?=$code?>" <?=$is_selected?>><?=$desc?></option>  
                        <? endforeach; ?>
                    </select>&nbsp;&nbsp;
                    <span id="loadingemp" hidden=""></span>
                </div>
            </div>
            <div class="form_row" style="border: transparent !important;">
                <table class="table table-striped table-bordered table-hover" width="100%" border="1">
                    <thead>
                        <tr>
                            <th rowspan="2"></th>
                            <th rowspan="2"></th>
                            <th rowspan="2">Day of Week</th>
                            <th rowspan="2">From</th>
                            <th rowspan="2">To</th>
                            <th rowspan="2">Remove Schedule</th>
                            <th colspan="2" hidden="">First Half</th>
                            <th colspan=""  hidden="">Second Half</th>
                            <th rowspan="2" hidden="">Early Dismissal</th>
                            <!-- <th  class="align_center" rowspan="2">Remove Schedule</th> -->
                            <th class="align_center forteaching" rowspan="2">Lec</th>
                            <th class="align_center forteaching" rowspan="2">Lab</th>
                            <th class="align_center forteaching">AIMS Dept</th>
                        </tr>
                        <tr hidden="">
                            <th>Tardy Start</th>
                            <th>Absent Start</th>
                            <th>Half Day Absent</th>
                        </tr>
                    </thead>
                    
                    <tbody id="schedule">
                    <?
                    // echo '<pre>';print_r($scheddays);
                    
                    foreach ($scheddays as $index => $row) {
                    ?>
                      <tr tag="grp" dayofweek="<?=$row['day_code']?>" dayidx="<?=$index?>">
                        <td class="col-md-1">
                          <div class="btn-group">
                            <a class="btn" href="#" tag="copy_sched"  title="Copy"><i class="glyphicon glyphicon-copy"></i></a>
                            <a class="btn" href="#" tag="paste_sched" title="Paste"><i class="glyphicon glyphicon-paste"></i></a>
                          </div>
                        </td>
                        <td class="col-md-1">
                          <div class="btn-group">
                            <a class="btn" href="#" tag="add_sched"><i class="glyphicon glyphicon-plus"></i></a>
                          </div>
                        </td>
                    
                        <td><?=$row['day_name']?></td>
                        <td>
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="fromtime" value="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                    
                        <td>
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="totime" value="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                    
                        <td hidden="">
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="tardy_f" value="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                    
                        <td hidden="">
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="absent_f" value="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                    
                        <td hidden="">
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="absent_e" value="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                        
                        <td hidden="">
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="early_d" value=""/>
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                        <td class="align_center"><input type='checkbox' class="double-sized-cb" name='toremove' value='checked'></input></td>
                        <td class="align_center forteaching"><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LEC" /></td>
                        <td class="align_center forteaching"><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LAB" /></td>

                        <td class="forteaching">
                          <select name="aimsdept" class="aimsdept" style="width: 200px;" >
                              <option value="" selected=""  >Choose Aims department..</option>
                              <? foreach ($aimsdept_arr as $key => $desc) { ?>
                                    <option value="<?=$key?>"><?=$desc?></option>
                              <? } ?>
                          </select>

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
                    <textarea rows="4" class="align_left" name="reason" id="reason" style="width: 95%;" placeholder="Reason" required=""></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="save" class="btn btn-danger">Apply</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
var schedarr = [];
var selEmp = [];
$(".chosen").chosen();
$("#dfrom").datepicker({
    autoclose: true,
    todayBtn : true
});
$(document).ready(function() {
   
  // $('.forteaching').hide();
});
$("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='absent_e'],input[name='early_d']").timepicker({
    minuteStep: 1,
    showSeconds: false,
    showMeridian: true,
    defaultTime: false
  });
$("a[tag='add_sched']").click(function(){
  var obj = $(this).parent().parent().parent().clone(true);
  var copy_button  = $('<a class="btn" href="#" tag="copy_sched"  title="Copy"><i class="glyphicon glyphicon-copy"></i></a>').click(function(){var obj = $(this).parent().parent().parent();$("a[tag='copy_sched']").each(function(){   $(this).css({"color":"","background-color":""});    });$(this).css({"color":"#D10303","background-color":"#BABABA"});copytime(obj);});
  var paste_button = $('<a class="btn" href="#" tag="paste_sched" title="Paste"><i class="glyphicon glyphicon-paste"></i></a>').click(function(){var obj = $(this).parent().parent().parent();pastetime(obj);});
  var delete_button = $("<a class='btn' href='#' tag='delete_sched'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){$(this).parent().parent().remove();});
  var timefrom_picker = $("<input name='fromtime' class='input-small align_center' type='text'/>");
  var timeto_picker   = $("<input name='totime' class='input-small align_center' type='text'/>");
  var tardy_picker = $("<input name='tardy_f' class='input-small align_center' type='text'/>");
  var absent_picker   = $("<input name='absent_f' class='input-small align_center' type='text'/>");
  var half_picker = $("<input name='absent_e' class='input-small align_center' type='text'/>");
  var earlyd_picker   = $("<input name='early_d' class='input-small align_center' type='text'/>");
  var toptions        = $(obj).find("td:last").find("div:first").find("select:first").find("option").clone(true);
  var type_drop       = $("<select class='chosen' name='schedtype'><select/>").append(toptions);
  $(obj).find("td:first").find("div:first").html("");
  $(obj).find("td:eq(1)").html($(delete_button));
  $(obj).find("td:eq(2)").html('');
  $(obj).find("td:eq(3)").find("div:first").html("");
  $(obj).find("td:eq(3)").find("div:first").append($(timefrom_picker));
  $(obj).find("td:eq(3)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");  
  $(obj).find("td:eq(4)").find("div:first").html("");
  $(obj).find("td:eq(4)").find("div:first").append($(timeto_picker));
  $(obj).find("td:eq(4)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  $(obj).find("td:eq(5)").find("div:first").html("");
  $(obj).find("td:eq(5)").find("div:first").append($(tardy_picker));
  $(obj).find("td:eq(5)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  $(obj).find("td:eq(6)").find("div:first").html("");
  $(obj).find("td:eq(6)").find("div:first").append($(absent_picker));
  $(obj).find("td:eq(6)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  $(obj).find("td:eq(7)").find("div:first").html("");
  $(obj).find("td:eq(7)").find("div:first").append($(half_picker));
  $(obj).find("td:eq(7)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  $(obj).find("td:eq(8)").find("div:first").html("");
  $(obj).find("td:eq(8)").find("div:first").append($(earlyd_picker));
  $(obj).find("td:eq(8)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  $(obj).find("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='absent_e'],input[name='early_d']").timepicker({
        minuteStep: 1,
        showSeconds: false,
        showMeridian: true,
        defaultTime: false
  }); 
  $(obj).insertAfter($(this).parent().parent().parent());   
  $(timefrom_picker).focus();
}); 
$("a[tag='delete_sched']").click(function(){    var obj = $(this).parent().parent().parent().remove();      });

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
  $.ajax({
       url      :   "<?=site_url("schedule_/validateFinalizedEmp")?>",
       type     :   "POST",
       data     :   { eids  : ''+selEmp+'', 
                      dfrom : dtActive},
       success  :   function(msg){
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

  if (hours === '12') {
    hours = '00';
  }

  if (modifier === 'PM') {
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
// end new function for validation in time

// start of saving
$("#save").unbind().click(function(){

    if($("#reason").val() == ""){
        $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
        return false;
    }
    
   var pars2 = "~u~"; 
   var schedule = "";
   var error_msg = tfrom = tto = "";
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
       schedule += $(this).find("select[name='aimsdept']:first").val();                                              
     }

   });

   if($("#employeeid").val() != null){

        // check if no error
        if(error_msg != ""){
          alert("Please enter a valid time. \n\nList of days have error in time : \n"+error_msg);
          return;
        }

       var form_data = "dfrom="+$("input[name='dfrom']").val()+"&eids="+$("#employeeid").val();
           form_data += "&timesched=" + schedule;
           form_data += "&model=requestsched";
           form_data += "&tnt="+$('input[name=tnt]:checked').val();
           form_data += "&specific="+$('input[name=specific]').is(":checked");
           form_data += "&start="+$('input[name=start]').val();
           form_data += "&end="+$('input[name=end]').val();
           form_data += "&reason="+$('#reason').val();

       $.ajax({
          url: "<?=site_url("schedule_/saveSchedApp")?>",
          data : form_data,
          type : "POST",
          success:function(msg){

            alert(msg);
            if(msg.search('successfully applied'))location.reload();
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

    $.ajax({
       url      :   "<?=site_url("utils_/getEmplist")?>",
       // url      :   "<?=site_url("utils_/getEmpListSched")?>",
       type     :   "POST",
       data     :   {tnt:tnt, deptid:'<?=$deptid?>'},
       success  :   function(ret){
        // console.log(ret);
        $("select[name='employeeid']").html(ret).trigger("liszt:updated");
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

$('input[name=specific],input[name=start],input[name=end]').on('change',function(){
  
  if($('input[name=specific]').is(":checked")){
      $("input[name='dfrom']").val('');
      $("#dfrom").datepicker('remove');
      $("#start,#end").datepicker({
          autoclose: true,
          todayBtn : true
      });

      var start = $("input[name='start']").val();
      var end = $("input[name='end']").val();

      if(start != '' && end != ''){
          $.ajax({
             url      :   "<?=site_url("schedule_/getDayofweekFromDates")?>",
             type     :   "POST",
             data     :   {start:start, end:end},
             success  :   function(ret){
              var arr_dow = JSON.parse(ret);

              $('tr[tag=grp]').each(function(){
                  var dayidx = $(this).attr('dayidx');

                  ///< only show dayidx in daterange
                  if($.inArray(dayidx,arr_dow) < 0){
                      $("tr[dayidx='"+dayidx+"'] ").prop('hidden',true);
                  }else{
                      $("tr[dayidx='"+dayidx+"'] ").prop('hidden',false);
                  }
              });
             }
          });
      }
  }
  else
  {
        $("input[name='start']").val('');
        $("input[name='end']").val('');
        $("#start,#end").datepicker('remove');
        $("#dfrom").datepicker({
            autoclose: true,
            todayBtn : true
        });
        $('tr[tag=grp]').prop('hidden',false);
  }
});


///< modified @Angelica for schedule copy and paste per day

function copytime(obj){
    if(schedarr.length > 0)  schedarr = [];

    var schedarr_temp = [];
    $('tr[dayofweek='+obj.attr('dayofweek')+']').each(function(){
      var from = $(this).find("input[name='fromtime']").val();
      var to   = $(this).find("input[name='totime']").val();
      var lec  = $(this).find("input[name='leclab']:checked").val();

      if(from != '' || to != '' || lec != undefined){
          schedarr_temp = {
            'fromtime'  :from,
            'totime'    :to,
            'schedtype' :lec,
          };
          schedarr.push(schedarr_temp);
      }
    });
    // console.log(schedarr);

    // schedarr.push({
    //     'fromtime'  :obj.find("input[name='fromtime']").val(),
    //     'totime'    :obj.find("input[name='totime']").val(),
    //     'schedtype' :obj.find("input[name='leclab']:checked").val(),
    // });  
}
function pastetime(obj){

    var schedarr_orig       = [],
        schedarr_orig_temp  = [];
    
    $('tr[dayofweek='+obj.attr('dayofweek')+']').each(function(){
        var from = $(this).find("input[name='fromtime']").val();
        var to   = $(this).find("input[name='totime']").val();
        var lec  = $(this).find("input[name='leclab']:checked").val();

        if(from != '' || to != '' || lec != undefined){
            schedarr_orig_temp = {
              'fromtime'  :from,
              'totime'    :to,
              'schedtype' :lec,
            };
            schedarr_orig.push(schedarr_orig_temp);
        }

        $(this).find("a[tag=delete_sched]").click();
    });
    // console.log(schedarr_orig);
    if(schedarr_orig.length == 0){
      if(schedarr.length > 0){
        obj.find("input[name='fromtime']").val(schedarr[0]['fromtime']);
        obj.find("input[name='totime']").val(schedarr[0]['totime']);
        obj.find("input[name='leclab']").each(function(){
            if($(this).val() == schedarr[0]['schedtype'])   $(this).prop("checked",true);   
            else                                            $(this).removeAttr("checked");
        });

        if(schedarr.length > 1){
            for (var i = schedarr.length - 1; i >= 1; i--) {
                $(obj).find("a[tag=add_sched]").click();
                $(obj).next(':first').find("input[name='fromtime']").val(schedarr[i]['fromtime']);
                $(obj).next(':first').find("input[name='totime']").val(schedarr[i]['totime']);
                $(obj).next(':first').find("input[name='leclab']").each(function(){
                    if($(this).val() == schedarr[i]['schedtype'])   $(this).prop("checked",true);   
                    else                                            $(this).removeAttr("checked");
                });
            }
        }
      }
    }else if(schedarr_orig.length > 0){
      if(schedarr.length > 0){
        for (var i = schedarr.length - 1; i >= 0; i--) {
            $(obj).find("a[tag=add_sched]").click();
            $(obj).next(':first').find("input[name='fromtime']").val(schedarr[i]['fromtime']);
            $(obj).next(':first').find("input[name='totime']").val(schedarr[i]['totime']);
            $(obj).next(':first').find("input[name='leclab']").each(function(){
                if($(this).val() == schedarr[i]['schedtype'])   $(this).prop("checked",true);   
                else                                            $(this).removeAttr("checked");
            });
        }
      }
    }

    if(schedarr_orig.length == 1){
      obj.find("input[name='fromtime']").val(schedarr_orig[0]['fromtime']);
      obj.find("input[name='totime']").val(schedarr_orig[0]['totime']);
      obj.find("input[name='leclab']").each(function(){
          if($(this).val() == schedarr_orig[0]['schedtype'])   $(this).prop("checked",true);   
          else                                            $(this).removeAttr("checked");
      });
    }else if(schedarr_orig.length == 0){

    }

    if(schedarr_orig.length > 1){
      for (var i = schedarr_orig.length - 1; i > 0; i--) {
          $(obj).find("a[tag=add_sched]").click();
          $(obj).next(':first').find("input[name='fromtime']").val(schedarr_orig[i]['fromtime']);
          $(obj).next(':first').find("input[name='totime']").val(schedarr_orig[i]['totime']);
          $(obj).next(':first').find("input[name='leclab']").each(function(){
              if($(this).val() == schedarr_orig[i]['schedtype'])   $(this).prop("checked",true);   
              else                                            $(this).removeAttr("checked");
          });
      }
    }


    // obj.find("input[name='fromtime']").val(schedarr[0]['fromtime']);
    // obj.find("input[name='totime']").val(schedarr[0]['totime']);
    // obj.find("input[name='leclab']").each(function(){
    //     if($(this).val() == schedarr[0]['schedtype'])   $(this).prop("checked",true);   
    //     else                                            $(this).removeAttr("checked");
    // });
}
</script>
<script type="text/javascript" src="<?=base_url()?>/js/timeValidation.js"></script>
<!-- <# @maxconsul, nagcopy ako ng codes sa mcu papunta dito sa ica> -->
<!-- <script src="<?=base_url()?>js/schedule_management.js"></script> -->