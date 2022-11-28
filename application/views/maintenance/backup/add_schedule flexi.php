<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
$sched = "";
$sched_code = "";   
$description = "";
$tardy_start = "";
$flexible = "";
$hours = "";
$mode = "";
$qsched = $this->db->query("select schedcode,description,tardy_start,flexible,hours,mode from code_schedule where schedid='{$schedid}'")->result();
if(count($qsched)>0){
 $sched_code = $qsched[0]->schedcode;   
 $description = $qsched[0]->description;
 $tardy_start = $qsched[0]->tardy_start;
 $flexible = $qsched[0]->flexible;   
 $hours = $qsched[0]->hours;
 $mode = $qsched[0]->mode;
}
?>
<style>
	.b_check {
		transform :scale(2);
		margin:3%;
	}
</style>
<div class="widgets_area">
<form id="form_schedule">
<div class="row">
    <div class="col-md-12">
        <div class="well blue">
            <div class="form_row">
                <label class="field_name align_right">Code</label>
                <div class="field">
					<table>
						<tr>
							<td width="40%"><input type="text" name="f_code" class="col-md-11" value="<?=$sched_code?>"/></td>
							<td width="20%"><input type="checkbox" class="b_check" name="f_flexible" value='YES' <?=$flexible?"checked":""?>/><label>Check if schedule is flexible</label></td>
							<td width="10%"><input type="text" name="f_hours" class="col-md-12" placeholder="hrs" value="<?=$hours?>" disabled/></td>
							<td width="20%">
								<select class="form-control" name="f_mode" disabled>
									<?
										$array = array("day"=>"Per Day","week"=>"Per Week","cutoff"=>"Per Cutoff");
										foreach($array as $k=>$v)
										{
										?>
											<option value="<?=$k?>" ><?=$v?></option>
										<?										
										}
									?>
								</select>
							</td>
						</tr>
					</table>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Description</label>
                <div class="field">
                    <input type="text" name="f_description" class="col-md-10" value="<?=$description?>"/>
                </div>
            </div>
            <div class="well-content" style="border: transparent !important;">
                    <table class="table table-striped table-bordered table-hover col-md-12">
                    <thead>
                        <tr>
                            <th class="col-md-1" rowspan="2"></th>
                            <th class="col-md-2" rowspan="2">Day of Week</th>
                            <th class="col-md-2" rowspan="2">From</th>
                            <th class="col-md-2" rowspan="2">To</th>
                            <th class="col-md-3" colspan="2">First Half</th>
                            <!--
                            <th class="col-md-2">Second Half</th>
                            <th class="col-md-1" rowspan="2" style="text-align: center;">HalfDay Schedule</th>
                            -->
                            <th class="col-md-2" rowspan="2"> Under Time End</th>
                        </tr>
                        <tr>
                            <th>Tardy Start</th>
                            <th>Absent Start</th>
                            <!--<th>Half Day Absent</th>-->
                        </tr>
                    </thead>
                    <tbody id="schedule">
<?

$lasdayofweek = "";
$dow = array("M"=>"Monday","T"=>"Tuesday","W"=>"Wednesday","TH"=>"Thursday","F"=>"Friday","S"=>"Saturday","SUN"=>"Sunday");
foreach($dow as $dow_code => $dow_desc){
    $sql_schedperday = $this->db->query("SELECT DISTINCT starttime,endtime,dayofweek,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal 
                                         FROM code_schedule_detail   
                                         WHERE schedid='{$schedid}' AND dayofweek='{$dow_code}'")->result();    
    # if($sql_schedperday->num_rows()>0){
    # for($p=0;$p<$sql_schedperday->num_rows();$p++){
    if(count($sql_schedperday)>0){        
      foreach($sql_schedperday as $mrow_schedperday){  
        $ftime = $mrow_schedperday->starttime ? date("h:i A",strtotime($mrow_schedperday->starttime)) : "";
        $etime = $mrow_schedperday->endtime ? date("h:i A",strtotime($mrow_schedperday->endtime)) : "";
        
        $tardy_s = $mrow_schedperday->tardy_start ? date("h:i A",strtotime($mrow_schedperday->tardy_start)) : "";
        $absent_s = $mrow_schedperday->absent_start ? date("h:i A",strtotime($mrow_schedperday->absent_start)) : "";
        $tardy_half_s = $mrow_schedperday->tardy_half_start ? date("h:i A",strtotime($mrow_schedperday->tardy_half_start)) : "";
        $absent_half_s = $mrow_schedperday->absent_half_start ? date("h:i A",strtotime($mrow_schedperday->absent_half_start)) : "";
        $no_sched      = ($mrow_schedperday->no_schedule == 1) ? " checked" : "";
        $half_sched      = ($mrow_schedperday->half_schedule == 1) ? " checked" : "";
        $early_d = $mrow_schedperday->early_dismissal ? date("h:i A",strtotime($mrow_schedperday->early_dismissal)) : "";
        # $mrow_schedperday = $sql_schedperday->row($p); 
?>                        
                        <tr tag='grp' dayofweek='<?=$dow_code?>'>
                          <td>
                            <div class="btn-group">
                            <?if($lasdayofweek!=$dow_code){?>
                                <a class="btn" href="#" tag='add_sched'><i class="glyphicon glyphicon-plus"></i></a>
                            <?}else{?>
                                <a class="btn" href="#" tag='delete_sched'><i class="glyphicon glyphicon-trash"></i></a>
                            <?}?>    
                            </div>
                          </td>
                          <td><?=($lasdayofweek!=$dow_code ? $dow_desc : "")?></td>
                          <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="fromtime" class="col-md-8 input-small align_center" type="text" value="<?=$ftime?>"/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="totime" class="col-md-8 input-small align_center" type="text" value="<?=$etime?>"/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="tardy_f" class="col-md-8 input-small align_center" type="text" value="<?=$tardy_s?>"/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="absent_f" class="col-md-8 input-small align_center" type="text" value="<?=$absent_s?>"/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="early_d" class="col-md-8 input-small align_center" type="text" value="<?=$early_d?>"/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          <td hidden="">
                            <div class="input-group bootstrap-timepicker">
                                <input name="absent_e" class="col-md-8 input-small align_center" type="text" value="<?=$absent_half_s?>"/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          <td class="align_center" hidden="">
                            <div>
                                <input type="checkbox" name="nosched" id="nosched" style="-webkit-transform: scale(1.5);" value="1" <?=$no_sched?> />
                            </div>
                          </td>
                          <td class="align_center" hidden="">
                            <div>
                                <input type="checkbox" name="halfsched" id="halfsched" style="-webkit-transform: scale(1.5);" value="1" <?=$half_sched?> />
                            </div>
                          </td>
                        </tr>
<?
  $lasdayofweek = $dow_code; 
  }
  }else{
?>
                         <tr tag='grp' dayofweek='<?=$dow_code?>'>
                          <td>
                            <div class="btn-group">
                                <a class="btn" href="#" tag='add_sched'><i class="glyphicon glyphicon-plus"></i></a>    
                            </div>
                          </td>
                          <td><?=$dow_desc?></td>
                          <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="fromtime" class="col-md-8 input-small align_center" type="text" value=""/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="totime" class="col-md-8 input-small align_center" type="text" value=""/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="tardy_f" class="col-md-8 input-small align_center" type="text" value=""/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="absent_f" class="col-md-8 input-small align_center" type="text" value=""/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          <!-- <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="tardy_e" class="col-md-8 input-small align_center" type="text" value=""/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td> -->
                          
                          <td>
                            <div class="input-group bootstrap-timepicker">
                                <input name="early_d" class="col-md-8 input-small align_center" type="text" value=""/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          
                          <td hidden="">
                            <div class="input-group bootstrap-timepicker">
                                <input name="absent_e" class="col-md-8 input-small align_center" type="text" value=""/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td>
                          
                          <td class="align_center" hidden="">
                            <div>
                                <input type="checkbox" name="nosched" id="nosched" style="-webkit-transform: scale(1.5);" value="1" />
                            </div>
                          </td>
                          
                          <td class="align_center" hidden="">
                            <div>
                                <input type="checkbox" name="halfsched" id="halfsched" style="-webkit-transform: scale(1.5);" value="1" />
                            </div>
                          </td>
                          
                        </tr>
<?    
  }
}
?>
                    </tbody>
                    </table>
                    </div>
            </div>
            </div>
<!--            
    <div class="field">
        <a href="#" class="btn btn-primary" id="saveschedule">Save</a>
    </div>
    -->
</div>    
</form>
</div>  
<script>

$(document).ready(function(){
	$("input[name='f_flexible']").trigger( "change" );
});

$("input[name='f_flexible']").change(function(){
	if($(this).is(':checked'))
	{
		$("input[name='f_hours']").removeAttr("disabled");
		$("select[name='f_mode']").removeAttr("disabled").trigger('liszt:updated');;
	}
	else{
		$("input[name='f_hours']").attr("disabled",true).val("");
		$("select[name='f_mode']").attr("disabled",true).val("").trigger('liszt:updated');;
	}
});

$("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='tardy_e'],input[name='absent_e'],input[name='early_d']").timepicker({
    minuteStep: 1,
    showSeconds: false,
    showMeridian: true,
    defaultTime: false
  });
$("a[tag='add_sched']").click(function(){
  var obj = $(this).parent().parent().parent().clone(true);
  
  var delete_button = $("<a class='btn' href='#' tag='delete_sched'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
    $(this).parent().parent().parent().remove();  
  });
  
  var timefrom_picker = $("<input name='fromtime' class='col-md-8 input-small align_center' type='text'/>");
  var timeto_picker   = $("<input name='totime' class='col-md-8 input-small align_center' type='text'/>");
  var tardy_start_picker   = $("<input name='tardy_f' class='col-md-8 input-small align_center' type='text'/>");
  var absent_start_picker   = $("<input name='absent_f' class='col-md-8 input-small align_center' type='text'/>");
  var tardy_half_picker   = $("<input name='tardy_e' class='col-md-8 input-small align_center' type='text'/>");
  var absent_half_picker   = $("<input name='absent_e' class='col-md-8 input-small align_center' type='text'/>");
  var early_d_picker       = $("<input name='early_d' class='col-md-8 input-small align_center' type='text'/>");
  var toptions        = $(obj).find("td:last").find("div:first").find("select:first").find("option").clone(true);
  var type_drop       = $("<select class='chosen' name='schedtype'><select/>").append(toptions);
  //alert($(obj).find("td:last").find("div:first").html());
  
  $(obj).find("td:first").find("div:first").html($(delete_button));
  $(obj).find("td:eq(1)").html("");
 
  $(obj).find("td:eq(2)").find("div:first").html("");
  $(obj).find("td:eq(2)").find("div:first").append($(timefrom_picker));
  $(obj).find("td:eq(2)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");  
  
  $(obj).find("td:eq(3)").find("div:first").html("");
  $(obj).find("td:eq(3)").find("div:first").append($(timeto_picker));
  $(obj).find("td:eq(3)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  
  $(obj).find("td:eq(4)").find("div:first").html("");
  $(obj).find("td:eq(4)").find("div:first").append($(tardy_start_picker));
  $(obj).find("td:eq(4)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  
  $(obj).find("td:eq(5)").find("div:first").html("");
  $(obj).find("td:eq(5)").find("div:first").append($(absent_start_picker));
  $(obj).find("td:eq(5)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  
  //$(obj).find("td:eq(6)").find("div:first").html("");
  //$(obj).find("td:eq(6)").find("div:first").append($(tardy_half_picker));
  //$(obj).find("td:eq(6)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  
  $(obj).find("td:eq(6)").find("div:first").html("");
  $(obj).find("td:eq(6)").find("div:first").append($(early_d_picker));
  $(obj).find("td:eq(6)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  
  /*
  $(obj).find("td:eq(7)").find("div:first").html("");
  $(obj).find("td:eq(7)").find("div:first").append($(absent_half_picker));
  $(obj).find("td:eq(7)").find("div:first").append("<span class='add-on'><i class='glyphicon glyphicon-time'></i></span>");
  */
  
  $(obj).find("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='tardy_e'],input[name='absent_e'],input[name='early_d']").timepicker({
        minuteStep: 1,
        showSeconds: false,
        showMeridian: true,
        defaultTime: false
  }); 
  $(obj).insertAfter($(this).parent().parent().parent());   
  $(timefrom_picker).focus();
}); 
$("a[tag='delete_sched']").click(function(){
  var obj = $(this).parent().parent().parent().remove();  
});
$("#button_save_modal").unbind("click").click(function(){
//$("#saveschedule").click(function(){
   //alert($("#form_schedule").serialize());
	var pars2 = "~u~"; 
	var schedule = "";
	$("#schedule").find("tr[tag='grp']").each(function(){
		if($(this).find("input[name='fromtime']:first").val() && $(this).find("input[name='totime']:first").val()){
			schedule += schedule ? "|" : ""; 
			schedule += $(this).attr("dayofweek");
			schedule += pars2;
			schedule += $(this).find("input[name='fromtime']:first").val() + "-" + $(this).find("input[name='totime']:first").val();
			schedule += pars2;
			schedule += $(this).find("input[name='tardy_f']:first").val();
			schedule += pars2;
			schedule += $(this).find("input[name='absent_f']:first").val();
			schedule += pars2;
			schedule += $(this).find("input[name='tardy_e']:first").val();
			schedule += pars2;
			schedule += $(this).find("input[name='absent_e']:first").val();
			schedule += pars2;
			schedule += $(this).find("input[name='nosched']:checked").val();
			schedule += pars2;
			schedule += $(this).find("input[name='halfsched']:checked").val();
			schedule += pars2;
			schedule += $(this).find("input[name='early_d']:first").val();
       
		}
	}); 
	console.log(schedule);
   
	var iscontinue = true;
	if($("input[name='f_flexible").is(':checked'))
	{
		var hours = $("input[name='f_hours']").val();
		var dayofweek = "";
		var from = "";
		var to = "";
		var hourDiff = 0;
		$("#schedule").find("tr[tag='grp']").each(function(){
			$(this).children('td, th').css("background-color","");
			if($(this).find("input[name='fromtime']:first").val() && $(this).find("input[name='totime']:first").val())
			{
				//FOR DAY
				if($("select[name='f_mode']").val() == "day")
				{
					if(dayofweek != $(this).attr("dayofweek"))
					{
						dayofweek = $(this).attr("dayofweek");
					
						from =  new Date("01/01/2017 " +$(this).find("input[name='fromtime']:first").val()).getHours();
						to = new Date("01/01/2017 " +$(this).find("input[name='totime']:first").val()).getHours();
					
						hourDiff = to - from;    
					}
					else
					{
						from =  new Date("01/01/2017 " +$(this).find("input[name='fromtime']:first").val()).getHours();
						to = new Date("01/01/2017 " +$(this).find("input[name='totime']:first").val()).getHours();
					
						hourDiff += to - from;   
					}
					
					if(hourDiff != hours)
					{
						$("#schedule").find("tr[dayofweek='"+dayofweek+"']").children('td, th').css("background-color","#ffe6e6");
						iscontinue = false;
					}
					else
					{
						$("#schedule").find("tr[dayofweek='"+dayofweek+"']").children('td, th').css("background-color","");
					}
				}
			
				//FOR WEEK
				else if($("select[name='f_mode']").val() == "week")
				{
					from =  new Date("01/01/2017 " +$(this).find("input[name='fromtime']:first").val()).getHours();
					to = new Date("01/01/2017 " +$(this).find("input[name='totime']:first").val()).getHours();
					
					hourDiff += to - from; 
					
					if(hourDiff != hours)
					{
						$("#schedule").find("tr[tag='grp']").each(function(){
							if($(this).find("input[name='fromtime']:first").val() && $(this).find("input[name='totime']:first").val())
							{
								$(this).children('td, th').css("background-color","#ffe6e6");
							}
						});
						iscontinue = false;
					}
					else
					{
						$("#schedule").find("tr[tag='grp']").each(function(){
							if($(this).find("input[name='fromtime']:first").val() && $(this).find("input[name='totime']:first").val())
							{
								$(this).children('td, th').css("background-color","");
							}
						});
					}
				}
			}
		});
	}
	
	if(iscontinue)
	{
		var form_data = $("#form_schedule").serialize();
			form_data+="&timesched=" + schedule;
			form_data+="&schedid=<?=$schedid?>"; 
			form_data+="&isedit=<?=$isedit?>";
		$.ajax({
			  url: "<?=site_url("maintenance_/saveschedule")?>",
			  data : form_data,
			type : "POST",
			success:function(msg){
				ulist.fnDraw();
				$("#modalclose").click();
				cancontinue = true;
				alert(msg);        
			}
		}); 
	}
});

$(".chosen").chosen();
</script>