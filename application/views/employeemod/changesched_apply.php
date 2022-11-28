<?php

/**
 * @author Justin
 * @copyright 2017
 */

$deptcode = $this->employee->getHeadDeptCode($this->session->userdata('username'));
$datetoday = date("d-m-Y");
?>
<style>
.modal{
    width: 75%;
    left: 0;
    right: 0;
    margin: auto;
}
.leclab
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
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
            <div class="form_row">
                <label class="field_name align_right">Date</label>
                <div class="field">
                    <div class="input-group date" id="dfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="dfrom" type="text" value="<?=$datetoday?>" readonly>
                        <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <select class="chosen col-md-6" id="employeeid" name="employeeid" multiple="">
                        <?
                        $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",true,'',$deptcode);
                        foreach($opt_type as $val){
                        ?>
                        <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form_row" style="border: transparent !important;">
                <table class="table table-striped table-bordered table-hover" width="100%">
                    <thead>
                        <tr>
                            <th rowspan="2"></th>
                            <th rowspan="2"></th>
                            <th rowspan="2">Day of Week</th>
                            <th rowspan="2">From</th>
                            <th rowspan="2">To</th>
                            <th colspan="2" hidden="">First Half</th>
                            <th colspan=""  hidden="">Second Half</th>
                            <th rowspan="2" hidden="">Early Dismissal</th>
                            <th class="align_center" rowspan="2">Lec</th>
                            <th class="align_center" rowspan="2">Lab</th>
                        </tr>
                        <tr hidden="">
                            <th>Tardy Start</th>
                            <th>Absent Start</th>
                            <th>Half Day Absent</th>
                        </tr>
                    </thead>
                    
                    <tbody id="schedule">
                    <?
                    $query = $this->employeemod->loadSchedday();
                    foreach ($query as $row) {
                    ?>
                      <tr tag="grp" dayofweek="<?=$row->day_code?>" dayidx="<?=$row->idx?>">
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
                    
                        <td><?=$row->day_name?></td>
                        <td>
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align-center" type="text" name="fromtime" value="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                    
                        <td>
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align-center" type="text" name="totime" value="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                    
                        <td hidden="">
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align-center" type="text" name="tardy_f" value="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                    
                        <td hidden="">
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align-center" type="text" name="absent_f" value="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                    
                        <td hidden="">
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align-center" type="text" name="absent_e" value="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                        
                        <td hidden="">
                          <div class="input-group bootstrap-timepicker">
                            <input class="input-small align-center" type="text" name="early_d" value=""/>
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                          </div>
                        </td>
                        
                        <td class="align_center"><input type="checkbox" class="leclab" name="leclab" value="LEC" /></td>
                        <td class="align_center"><input type="checkbox" class="leclab" name="leclab" value="LAB" /></td>
                        
                      </tr>
                    <?}?>
                    </tbody>
                </table>
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
$(".chosen").chosen();
$("#dfrom").datepicker({
    autoclose: true,
    todayBtn : true
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
  $(obj).find("td:first").find("div:first").html("").append($(copy_button)).append($(paste_button));
  $(obj).find("td:eq(1)").html($(delete_button));
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
$("#save").unbind().click(function(){
   var pars2 = "~u~"; 
   var schedule = "";
   $("#schedule").find("tr[tag='grp']").each(function(){
     if($(this).find("input[name='fromtime']:first").val() && $(this).find("input[name='totime']:first").val()){
       schedule += schedule ? "|" : ""; 
       schedule += $(this).attr("dayofweek");
       schedule += pars2;
       schedule += $(this).attr("dayidx");
       schedule += pars2;
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
     }
   });
   if($("#employeeid").val() != null){
       var form_data = "dfrom="+$("input[name='dfrom']").val()+"&eids="+$("#employeeid").val();
           form_data += "&timesched=" + schedule;
           form_data += "&model=requestsched";
       $.ajax({
          url: "<?=site_url("employeemod_/loadmodelfunc")?>",
          data : form_data,
          type : "POST",
          success:function(msg){
            alert(msg);
            location.reload();
          }
       });
   }else    alert("Please select employee first..");
});
$(".leclab").on('change', function() {
    $(this).closest('tr').find(".leclab").not(this).prop('checked', false);
});
$("a[tag='copy_sched']").click(function(){  var obj = $(this).parent().parent().parent();   copytime(obj);
    $("a[tag='copy_sched']").each(function(){   $(this).css({"color":"","background-color":""});    });
    $(this).css({"color":"#D10303","background-color":"#BABABA"});
});
$("a[tag='paste_sched']").click(function(){ var obj = $(this).parent().parent().parent();   pastetime(obj); });
function copytime(obj){
    if(schedarr.length > 0)  schedarr = [];
    schedarr.push({
        'fromtime'  :obj.find("input[name='fromtime']").val(),
        'totime'    :obj.find("input[name='totime']").val(),
        'schedtype' :obj.find("input[name='leclab']:checked").val(),
    });  
}
function pastetime(obj){
    obj.find("input[name='fromtime']").val(schedarr[0]['fromtime']);
    obj.find("input[name='totime']").val(schedarr[0]['totime']);
    obj.find("input[name='leclab']").each(function(){
        if($(this).val() == schedarr[0]['schedtype'])   $(this).prop("checked",true);   
        else                                            $(this).removeAttr("checked");
    });
}
</script>