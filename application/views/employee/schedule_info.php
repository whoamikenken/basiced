<?php

$CI =& get_instance();
$CI->load->model('utils');

if(isset($empinfo)){
   $empdetails = $empinfo[0]['employeeid'];   
}else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0]['employeeid'];
   $emptype = $empinfo[0]['emptype'];
   $teachingtype = $empinfo[0]['teachingtype'];
}

$dateactive = $CI->utils->getEmployeeDateActive($empdetails);
// $dateactive = $this->extensions->getLatestDateActive($empdetails);

 $flexi = $this->employee->getindividualemployee($empdetails);
 foreach($flexi as $row)    $empflexi = $row->isFlexi == 1 ? " checked" : "";
 
$sched = "";

$readonlys = "";
$dis = "";
$hide= "";
$usertype = $this->session->userdata("usertype");
if ($usertype == "EMPLOYEE") {
  $readonlys = 'disabled';
  $dis = 'disabled="disabled"';
  $hide = 'style="display:none"';
}
else
{
  $readonly = '';
}

$aimsdept_arr = $CI->utils->getAIMSDepartment();
$subject_arr = $CI->utils->getSubject();

unset($aimsdept_arr['']);

$teachingtype = $empinfo[0]['teachingtype'];
$hideAimsDept = $teachingtype == 'teaching' ? '' : ' hidden';

?>



<div class="widgets_area animated fadeIn delay-1s" style="margin-top: 3%;">
<form id="form_schedules">
<div class="row">
    <div class="col-md-12">
        <div class="well blue" style="margin: 2px;">
            <div class="well-content" style="border: transparent !important;" $readonlys >
                <table class="table table-striped table-bordered table-hover" id="sched_table">
                <thead style="background-color: #0072c6;">
                    <!-- <tr>
                        <th colspan="7"><input type="checkbox" name="flexisched" id="flexisched" class="double-sized-cb" value="1" <?=$empflexi?> /> &nbsp;Flexible Schedule</th>
                        <th></th>
                    </tr> -->
                    <tr>
                        <th width="13%" rowspan="4"<?=$hide?> ></th>
                        <th rowspan="2" class="align_center" width="7%" align="center">Day of Week</th>
                        <th rowspan="2" class="align_center" align="center">Weekly Schedule</th>
                        <th rowspan="2" class="align_center" align="center">From</th>
                        <th rowspan="2" class="align_center" align="center">To</th>
                        <th colspan="2" class="align_center">First Half</th>
                        <th colspan="" hidden>Second Half</th>
                        <!--
                        <th rowspan="2" style="text-align: center;">No Schedule</th>
                        <th rowspan="2" style="text-align: center;">HalfDay Schedule</th>
                        -->
                        <th rowspan="2" class="align_center">Early Dismissal End</th>
                        <!--<th rowspan="" colspan="2" class="align_center">Type</th>
                        <th rowspan="2" class="align_center" <?=$hideAimsDept?> >Course</th>
                        <th rowspan="2" class="align_center" <?=$hideAimsDept?> >Section</th>
                        <th rowspan="2" class="align_center" <?=$hideAimsDept?> >Per Subject</th>
                        <th rowspan="2" class="align_center" <?=$hideAimsDept?> >AIMS Department</th>-->
                    </tr>
                    <tr>
                        <th class="align_center">Tardy Start</th>
                        <th class="align_center">Absent Start</th>
                        <th hidden>Half Day Absent</th>
                        <!--<th>LEC</th>
                        <th>LAB</th>-->
                        <!-- <th>Tardy Start</th> -->
                    </tr>
                </thead>
                <tbody id="schedule">
                      <?php

                      $strsql2 = "SELECT 
                                    B.day_code,
                                    B.day_name, 
                                    A.starttime,
                                    A.endtime,
                                    A.idx,
                                    A.tardy_start,
                                    A.absent_start,
                                    A.tardy_half_start,
                                    A.absent_half_start,
                                    A.no_schedule,
                                    A.half_schedule,
                                    A.early_dismissal,
                                    A.leclab,
                                    A.flexible,
                                    A.hours,
                                    A.breaktime,
                                    A.dateedit,
                                    A.course,
                                    A.section,
                                    A.subject,
                                    A.aimsdept,
                                    A.weekly_sched
                                   from employee_schedule as A 
                                  INNER join code_daysofweek AS B
                                  on A.dayofweek = B.day_code and A.employeeid = '{$empdetails}'";

                      $queResult1 = $this->db->query($strsql2)->result_array();

                      if (count($queResult1) < 1) {
                        $strsql1 = "SELECT 
                                      E.day_code,
                                      E.day_name,
                                      F.employeeid,
                                      F.description,
                                      F.starttime,
                                      F.endtime,
                                      F.idx,
                                      F.tardy_start,
                                      F.absent_start,
                                      F.tardy_half_start,
                                      F.absent_half_start,
                                      F.no_schedule,
                                      F.half_schedule,
                                      F.early_dismissal,
                                      F.flexible,
                                      F.hours,
                                      F.breaktime,
                                      F.weekly_sched
                                    FROM
                                      code_daysofweek AS E
                                    LEFT JOIN
                                      (SELECT 
                                        C.employeeid,
                                        D.*
                                      FROM 
                                        employee AS C
                                      INNER JOIN 
                                        (SELECT 
                                          B.dayofweek,
                                          A.description,
                                          B.schedid,
                                          B.starttime,
                                          B.endtime,
                                          B.idx,
                                          B.tardy_start,
                                          B.absent_start,
                                          B.tardy_half_start,
                                          B.absent_half_start,
                                          B.no_schedule,
                                          B.half_schedule,
                                          B.early_dismissal,
                                          B.flexible,
                                          B.hours,
                                          B.breaktime,
                                          B.weekly_flexible
                                        FROM 
                                          code_schedule AS A
                                        INNER JOIN code_schedule_detail AS B
                                        ON A.schedid = B.schedid) AS D
                                      ON  C.empshift = D.schedid) AS F
                                    ON E.day_code = F.dayofweek AND F.employeeid = '{$empdetails}'
                                    ORDER BY E.day_id ASC";
                        $queResult1 = $this->db->query($strsql1)->result_array();
                      }

                      $date_active = $prev_date_active = $flexible = $flexi_hours = $flexi_breaktime = '';
                      $lasdayofweek = "";
                      $counter = 0;
                      $dowarray = array("M"=>"MONDAY","T"=>"TUESDAY","W"=>"WEDNESDAY","TH"=>"THURSDAY","F"=>"FRIDAY","S"=>"SATURDAY","SUN"=>"SUNDAY");
                      $weeklySchedules = array("weekly"=>"Weekly","1"=>"1st Week","2"=>"2nd Week","3"=>"3rd Week","4"=>"4th Week","5"=>"5th Week");
                      $dowInitials = array("M","T","W","TH","F","S","SUN");
                      $queResult2 = array();
                      foreach ($queResult1 as $key => $value) {
                        if(in_array($value['day_code'], $dowInitials)){
                           unset($dowarray[$value['day_code']]);
                        }
                      }
                      foreach ($dowarray as $daycode => $dayname) {
                        array_push($queResult1, array("day_code"=>$daycode, "day_name"=>$dayname,"starttime"=>'',"endtime"=>'',"idx"=>1,"tardy_start"=>'',"absent_start"=>'',"tardy_half_start"=>'',"tardy_half_start"=>'',"absent_half_start"=>'',"no_schedule"=>0,"half_schedule"=>8,"early_dismissal"=>'',"leclab"=>'',"flexible"=>'NO',"hours"=>0,"breaktime"=>0,"dateedit"=>'2020-08-24 00:00:00',"course"=>'',"section"=>'',"aimsdept"=>'',"weekly_sched"=>'weekly'));
                      }
                      foreach ($dowInitials as $k => $v) {
                        foreach ($queResult1 as $key => $value) {
                          if($v == $value['day_code']) array_push($queResult2, $value);
                        }
                      }

                      foreach ($queResult1 as $key => $schedPerDay) {
                        $counter++;
                        $sched_start = ($schedPerDay["starttime"] != "") ? date("h:i A",strtotime($schedPerDay["starttime"])) : "";

                        $sched_end = ($schedPerDay["endtime"] != "") ? date("h:i A",strtotime($schedPerDay["endtime"])) : "";

                        $tardy1 = ($schedPerDay["tardy_start"] != "") ? date("h:i A",strtotime($schedPerDay["tardy_start"])) : "";

                        $absent1 = ($schedPerDay["absent_start"] != "") ? date("h:i A",strtotime($schedPerDay["absent_start"])) : "";

                        $tardy2 = ($schedPerDay["tardy_half_start"] != "") ? date("h:i A",strtotime($schedPerDay["tardy_half_start"])) : "";

                        $absent2 = ($schedPerDay["absent_half_start"] != "") ? date("h:i A",strtotime($schedPerDay["absent_half_start"])) : "";
                        
                        $no_sched = ($schedPerDay["no_schedule"] == 1) ? " checked" : "";
                        
                        $half_sched = ($schedPerDay["half_schedule"] == 1) ? " checked" : "";
                        
                        $earlyd = ($schedPerDay["early_dismissal"] != "") ? date("h:i A",strtotime($schedPerDay["early_dismissal"])) : "";

                        $weekly_flexible = $schedPerDay["weekly_sched"];
                        $weekly_sched = "";
                        if($weekly_flexible != ''){
                          foreach (explode(',', $weekly_flexible) as $key => $value) {
                              if($weekly_sched != "") $weekly_sched .= "<br>".$weeklySchedules[$value];
                              $weekly_sched .= $weeklySchedules[$value];
                          }
                        }else{
                          $weekly_sched .= $weeklySchedules['weekly'];
                          $weekly_flexible = 'weekly';
                        }

                        
                        $leclab = isset($schedPerDay["leclab"]) ? $schedPerDay["leclab"] :""; 
                        $dow_code = $schedPerDay["day_code"];
                        if(!$flexible)        $flexible         = isset($schedPerDay["flexible"]) ? $schedPerDay["flexible"]  :""; 
                        if(!$flexi_hours)     $flexi_hours      = isset($schedPerDay["hours"]) ? $schedPerDay["hours"]        :""; 
                        if(!$flexi_breaktime) $flexi_breaktime  = isset($schedPerDay["breaktime"]) ? $schedPerDay["breaktime"]:""; 


                        // if(!$date_active) $date_active = isset($schedPerDay['dateedit']) ? date("Y-m-d",strtotime($schedPerDay["dateedit"])) : "";

                        if(!$date_active){
                          $date_active = isset($schedPerDay['dateedit']) ? date("Y-m-d",strtotime($schedPerDay["dateedit"])) : "";
                          if($date_active){
                            
                            $datetime = new DateTime($date_active);
                            $datetime->add(new DateInterval('P1D'));
                            $date_active = $datetime->format('Y-m-d');
                            $prev_date_active = $date_active;
                          }
                        }
                      ?>

                        <tr tag="grp" dayofweek="<?php print($schedPerDay["day_code"]); ?>" dowc="<?= $schedPerDay["day_code"].$counter ?>" >
                          <td class="col-md-1" align="center"  <?=$hide?> >
                            <?php
                              if($lasdayofweek!=$dow_code){ ?>
                                <div class="btn-group">
                             <? }
                             else{  ?>
                                <div class="btn-group" style="float: right; margin-right: 4px;">
                             <?
                             }
                            ?>

                              <!-- <a class="btn btn-info" href="#" tag="copy_sched" style="color: white;"  title="Copy" <?=$hide?> <?= $dis?>><i class="icon-copy" ></i></a>
                              <a class="btn btn-info" href="#" tag="paste_sched" title="Paste" <?=$hide?> <?= $dis?>><i class="glyphicon glyphicon-paste"></i></a>
                              <a class="btn btn-info erase_time" href="#" tag="erase_sched"  title="Erase"><i class="icon-eraser"></i></a>
                           
                      
                          <?php
                                    if (($schedPerDay["starttime"] != "") && ($schedPerDay["endtime"] != "")) {
                              ?>
                                      <a class="btn btn-danger" href="#" tag="delete_sched" <?=$hide?> <?=$dis?>><i class="glyphicon glyphicon-trash" ></i></a>
                              <?php        
                                    }else{
                              ?>  
                                      <a class="btn btn-primary"  href="#" tag="add_sched" <?=$hide?> <?=$dis?>><i class="glyphicon glyphicon-plus" ></i></a>
                              <?php
                                    }
                              ?> -->
                              <?php
                                  if($lasdayofweek!=$dow_code){
                                  ?>
                                  <a class="btn btn-info  nodtr" style=" color: white; margin-right: 5px;" href="#" tag="copy_sched"  title="Copy"><i class="glyphicon glyphicon-duplicate"></i></a>
                                  <a class="btn btn-info  nodtr" href="#" tag="paste_sched" style="margin-right: 5px;" title="Paste"><i class="glyphicon glyphicon-paste"></i></a>
                                  <a class="btn btn-info erase_time  nodtr" href="#" style="margin-right: 5px;" id="erase_time" tag="erase_sched"  title="Clear"><i class="icon-eraser"></i></a>
                                  <?php
                                  }
                                  ?>
                                  <?if($lasdayofweek!=$dow_code){?>
                                  <a class="btn btn-primary  nodtr" href="#" tag='add_sched'><i class="glyphicon glyphglyphicon glyphicon-plus"></i></a>
                                  <?}else{?>
                                      <a class="btn btn-danger align_right  nodtr"  href="#" tag='delete_sched' ><i class="glyphicon glyphicon-trash"></i></a>
                                  <?}?>
                        </div>
                          
                          </td>

                          <td align="center">
                            <?=($lasdayofweek!=$dow_code ? $schedPerDay["day_name"] : "")?>                              
                          </td>
                          <td>
                            <div style="text-align: center">
                              <a class="btn btn-primary weekly_sched" style=" color: white; margin-right: 10px;" href="#weekly-view" data-toggle="modal" tag="weekly_sched" weekly_flexible = "<?=$weekly_flexible?>"  title="<?=$weekly_sched?>"><i class="glyphicon glyphicon-calendar"></i></a>
                            </div>
                          </td>

                          <td align="center">
                            <input type="hidden" name="daycode" value="<?php print($schedPerDay["day_code"]); ?>">
                            <div class='input-group time'>
                                <input type='text' class="form-control ftime" id="ftime" name="fromtime" value="<?php print($sched_start); ?>" <?=$readonlys?>>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                          </td>

                          <td align="center">
                            <div class='input-group time'>
                                <input type='text' class="form-control totime" id="totime" name="totime" value="<?php print($sched_end); ?>" <?=$readonlys?>/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                          </td>

                          <td align="center">
                          <input type="hidden" name="daycode" value="<?php print($schedPerDay["day_code"]); ?>">
                            <div class='input-group time'>
                                <input type='text' class="form-control tardy_f" id="tardy_f" name="tardy_f" value="<?php print($tardy1); ?>" <?=$readonlys?>/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                          </td>

                          <td align="center">
                          <input type="hidden" name="daycode" value="<?php print($schedPerDay["day_code"]); ?>">
                            <div class='input-group time'>
                                <input type='text' class="form-control absent_f" id="absent_f" name="absent_f" value="<?php print($absent1); ?>" <?=$readonlys?>/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                          </td>

                          <!-- <td>
                            <div class="input-group bootstrap-timepicker">
                              <input class="col-md-8 input-small align-center" type="text" name="tardy_e" value="<?php print($tardy2); ?>">
                              <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
                          </td> -->

                          <td hidden>
                            <input type="hidden" name="daycode" value="<?php print($schedPerDay["day_code"]); ?>">
                            <div class='input-group time'>
                                <input type='text' class="form-control absent_e" id="absent_e" name="absent_e" value="<?php print($absent2); ?>" <?=$readonlys?>/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                          </td>
                          
                          <td class="align_center" hidden="">
                           <div>
                              <input type="checkbox" name="nosched" id="nosched" <?=$readonlys?> style="-webkit-transform: scale(1.5);" value="1" <?=$no_sched?> />
                           </div>
                          </td>
                          
                          <td class="align_center" hidden="">
                           <div>
                              <input type="checkbox" name="halfsched" id="halfsched" <?=$readonlys?> style="-webkit-transform: scale(1.5);" value="1" <?=$half_sched?> />
                           </div>
                          </td>
                          
                          <td>
                            <input type="hidden" name="daycode" value="<?php print($schedPerDay["day_code"]); ?>">
                            <div class='input-group time'>
                                <input type='text' class="form-control early_d" id="early_d" name="early_d" value="<?php print($earlyd); ?>" <?=$readonlys?>/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-time"></span>
                                </span>
                            </div>
                          </td>
                          
                          <!--<td class="align_center"><div><input type="checkbox" class="lec double-sized-cb" id="<?= 'lec'.$counter ?>" counter="<?= $counter ?>" name="leclab" value="LEC" <?=(isset($schedPerDay['leclab']) && $schedPerDay['leclab'] == "LEC") ? " checked" : ""?> /></div></td>
                          <td class="align_center"><div><input type="checkbox" class="lab double-sized-cb" counter="<?= $counter ?>" name="leclab" id="<?= 'lab'.$counter ?>" value="LAB" <?=(isset($schedPerDay['leclab']) && $schedPerDay['leclab'] == "LAB") ? " checked" : ""?> /></div></td>
                          <td <?=$hideAimsDept?>>
                            <select name="course" id="course" class="course chosen" style="width: 200px;" >
                               <?= $this->setup->generateCourseDropdown($schedPerDay["course"]) ?>
                            </select>
                          </td>
                          <td <?=$hideAimsDept?>>
                            <select name="section" id="section" class="section chosen" style="width: 200px;" >
                               <?= $this->setup->generateSectionDropdown($schedPerDay["course"], $schedPerDay["section"]) ?>
                            </select>
                          </td>
                          <td <?=$hideAimsDept?>>
                            <select name="subject" id="subject" class="aimsdept chosen" style="width: 200px;" >
                               <?= $this->setup->generateSubjectDropdown($schedPerDay["course"], $schedPerDay["subject"]) ?>
                            </select>
                          </td>
                          <td <?=$hideAimsDept?>>
                            <select name="aimsdept" id="aimsdept" class="aimsdept chosen" style="width: 200px;" >
                                <option value="" <?=(isset($schedPerDay['aimsdept']) && $schedPerDay['aimsdept']=='')?' selected':''?> >Choose Aims department..</option>
                                <? foreach ($aimsdept_arr as $key => $desc) { ?>
                                      <option value="<?=$key?>" <?=(isset($schedPerDay['aimsdept']) && $schedPerDay['aimsdept']==$key)?' selected':''?> ><?=$desc?></option>
                                <? } ?>
                            </select>-->
                          </td>
                        </tr>
                      <?php
                      $lasdayofweek = $dow_code; 
                      }//end foreach
                      ?>
                </tbody>
                </table>
                </div>
            </div>
            <br>
                 <div class="form_row" <?=$hide?>>
                <div class="col-md-6">
                    <label class="field_name align_left">Effectivity Date:</label>
                    <div class="field">
                      <div class='input-group date' style="width: 400px;" id="date_active1" data-date="" data-date-format="yyyy-mm-dd">
                          <input type='text' class="form-control" size="16" name="date_active1" value="<?=$dateactive?>" <?=$readonlys?>/>
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                      </div>
                    
                    </div>
                </div>
                <div class="col-md-6 align_left" >
                  <div class="col-md-6" style=" margin-left: -210px; margin-top: 5px;">
                    <input type="checkbox" name="is_flexi_sched" class="double-sized-cb float-right" style="transform: scale(2);"  <?=$flexible=='YES'?'checked':''?>> &nbsp;<b> Flexible Schedule</b>&nbsp; &nbsp; 
                  </div>
                  <div class="col-md-3" style=" margin-left: -210px;">
                    <input style="width: 50px;" type="text" name="flexi_hours" class="form-control" value="<?=$flexible=='YES'?$flexi_hours:0?>" <?=$flexible=='YES'?'':'disabled'?> oninput="return setNumberOnly(this)">
                  </div>
                      <label class="field_name align_left" style=" margin-left: -130px;">Hours </label>
                  <div class="col-md-3" style=" margin-left: -90px;">
                    <input style="width: 50px;" type="text" name="flexi_breaktime" class="form-control" value="<?=$flexible=='YES'?$flexi_breaktime:0?>" <?=$flexible=='YES'?'':'disabled'?> oninput="return setNumberOnly(this)">
                  </div>
                      <label class="field_name align_left" style=" margin-left: -100px;">Hr/s Breaktime</label>
    <?if(in_array($this->session->userdata("usertype"),array('ADMIN','SUPER ADMIN')))
    {?>
        
        <div>
            <a href="#" class="btn btn-primary" id="saveschedule">S A V E</a>
            <span id="loadingSave" hidden="">
              &nbsp;&nbsp;<img src='<?=base_url()?>images/loading.gif' /> Saving.. Please wait.
            </span>
            
        </div>
    <?
    }?>
                </div>
            </div>

            
</div>    
</form>
</div>  

<div class="modal fade" id="weekly-view" role="dialog" data-backdrop="static">
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
        <center><b><h3 tag="title" class="modal-title" style="font-family: Avenir;">Weekly Schedule</h3></b></center>
      </div>
      <div class="modal-body">
        <div class="row">
              <div tag='weekly-display'>
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success week_save_modal"  id='week_save_modal'>Save</button>
        <button type="button" class="btn btn-success" data-dismiss="modal" id='week_close_modal' style="display: none"></button>
      </div>
    </div>

  </div>
</div>  
<script>
var checkboxCounterforAppend = 0;
$(document).ready(function(){
   if ("<?= $teachingtype ?>" == "teaching") {
    $("#sidebar").addClass("active");
    $("#content").addClass("active");
   }else{
    $("#sidebar").removeClass("active");
    $("#content").removeClass("active");
   }

    var id = $(".tab-pane active").attr("ld");
    if(id != "employee/schedule_info_history_main"){
      $('#wrap_sched_history').hide();
    }
});

  $("#sched_table #schedule").delegate(".lec", "click", function() {
    var lec = $(this).attr("counter");
    $('#lab'+lec).prop("checked", false);
  });

  $("#sched_table #schedule").delegate(".lab", "click", function() {
    var lab = $(this).attr("counter");
    $('#lec'+lab).prop("checked", false);
  });


  $(".erase_time").click(function(){
        var tr_id = $(this).closest("tr").attr("dayofweek");
        $("tr[dayofweek='"+ tr_id +"']").find(".ftime").val('');
        $("tr[dayofweek='"+ tr_id +"']").find(".totime").val('');
        $("tr[dayofweek='"+ tr_id +"']").find(".tardy_f").val('');
        $("tr[dayofweek='"+ tr_id +"']").find(".absent_f").val('');
        $("tr[dayofweek='"+ tr_id +"']").find(".early_d").val('');
    });
   setTimeout(
  function() 
  {
    $(".widgets_area").removeClass("animated fadeIn delay-1s");
  }, 2000);

    var schedarr = [];
    $(".chosen").chosen();
    
    $(".time").datetimepicker({
        format: "LT"
    });

    /*$("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='tardy_e'],input[name='absent_e'],input[name='early_d']").timepicker({
      minuteStep: 1,
      showSeconds: false,
      showMeridian: true,
      defaultTime: false
    });*/

    $('input[name=is_flexi_sched]').on('click',function(){
      if(!$(this).is(':checked')){
        $('input[name=flexi_hours],input[name=flexi_breaktime]').val(0).prop('disabled',true);
      }else{
        $('input[name=flexi_hours],input[name=flexi_breaktime]').prop('disabled',false);
      }
    });

    $("a[tag='add_sched']").click(function(){
      checkboxCounterforAppend = checkboxCounterforAppend+1;
      var obj = $(this).parent().parent().parent().clone();
      // console.log(obj);
      var copy_button  = $('<a class="btn" href="#" tag="copy_sched"  title="Copy"><i class="glyphicon glyphicon-copy"></i></a>').click(function(){var obj = $(this).parent().parent().parent();copytime(obj);}); 
      var paste_button = $('<a class="btn" href="#" tag="paste_sched" title="Paste"><i class="glyphicon glyphicon-paste"></i></a>').click(function(){var obj = $(this).parent().parent().parent();pastetime(obj);});
      var delete_button = $("<a class='btn btn-danger' href='#'  tag='delete_sched'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){$(this).parent().parent().remove();});
      var timefrom_picker = $('<input class="form-control ftime" id="ftime" type="text" name="fromtime" />');
      var totime_picker   = $('<input class="form-control" type="text" name="totime" />');
      var tardy_f_picker  = $('<input class="form-control" type="text" name="tardy_f" />');
      var absent_f_picker = $('<input class="form-control" type="text" name="absent_f" />');
      var absent_e_picker = $('<input class="form-control" type="text" name="absent_e" />');
      var early_d_picker  = $('<input class="form-control" type="text" name="early_d" />');
      var aimsDept = $('<select name="aimsdepts" id="aimsdepts" class="form-control chosen-select" style="width: 220px;" ></select>');
      var subjects = $('<select name="subjects" id="subjects" class="form-control chosen-select" style="width: 220px;" >');

      $(obj).find("td:first").find("div:first").html("").append($(copy_button)).append($(paste_button));
      $(obj).find("td:eq(0)").html($(delete_button)).css("padding-left", "141px");
      $(obj).find("td:eq(1)").css("color","#F5F5F5");

      $(obj).find("td:eq(2)").find("div:first").html('<a class="btn btn-primary weekly_sched" style=" color: white; margin-right: 10px;" href="#weekly-view" data-toggle="modal" tag="weekly_sched" weekly_flexible="weekly"  title="Weekly"><i class="glyphicon glyphicon-calendar"></i></a>').click(function(){
        var weekly_flexible = "weekly";
        var week_obj = $(this);
        $.ajax({
          url: "<?=site_url('schedule_/adjust_weekly_schedule_main')?>",
          type: "POST",
          data: {weekly_flexible: GibberishAES.enc(weekly_flexible, toks), toks:toks},
          success:function(response){
            $("div[tag='weekly-display']").html(response);

            $("#week_save_modal").click(function(){
              if(!$("select[name='wSched_main']").val()){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Weekly Schedule is required!',
                    showConfirmButton: true,
                    timer: 1000
                })
              }else{
                $(week_obj).attr("weekly_flexible", $("select[name='wSched_main']").val());
                $("#week_close_modal").click();
              }
              
            });
          }
        });
      }); 

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
      $(obj).find("td:eq(7)").find("div:first").append($(absent_e_picker));
      $(obj).find("td:eq(7)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
      $(obj).find("td:eq(10)").find("div:first").html("");
      $(obj).find("td:eq(10)").find("div:first").append($(early_d_picker));
      $(obj).find("td:eq(10)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");

      $(obj).find("td:eq(11)").find("div:first").html("");
       $(obj).find("td:eq(11)").find("div:first").append("<input type='checkbox' class='double-sized-cb cblec' id='cblec"+checkboxCounterforAppend+"' counter='"+checkboxCounterforAppend+"' name='leclab' value='LEC' />");
       $(obj).find("td:eq(12)").find("div:first").html("");
      $(obj).find("td:eq(12)").find("div:first").append("<input type='checkbox' class='double-sized-cb cblab' id='cblab"+checkboxCounterforAppend+"' counter='"+checkboxCounterforAppend+"' name='leclab' value='LAB' />");

      $(obj).find("td:eq(13)").find("div:first").html("");
      $(obj).find("td:eq(13)").find("div:first").append($(subjects));

      $.ajax({
        url: "<?=site_url("schedule_/loadSubject")?>",
        success : function(ret){
          $("#subjects").html(ret);
          $("#subjects").chosen();
        }
      });

      $(obj).find("td:eq(13)").find("div:first").html("");
      $(obj).find("td:eq(13)").find("div:first").append($(aimsDept));

      $.ajax({
        url: "<?=site_url("schedule_/loadSelectAimsDept")?>",
        success : function(ret){
          $("#aimsdepts").html(ret);
          $("#aimsdepts").chosen();
        }
      });
      // $(obj).find("td:eq(12)").find("div:first").append($(subject));
      $(obj).find("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='absent_e'],input[name='early_d']").datetimepicker({
        format: "LT"
      }); 
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
      var obj = $(this).parent().parent().parent().removeAttr('tag').remove();  
    });

    var prev_date_active = "<?=$prev_date_active?>";

    $("#saveschedule").click(function(){
       $('#saveschedule').attr('disabled',true);
       $('#loadingSave').removeAttr('hidden');
       var date_active1 = $('input[name=date_active1]').val();
       var pars2 = "~u~"; 
       var schedule = "";
       var error_msg = tfrom = tto = "";
       $("#form_schedules #schedule").find("tr[tag='grp']").each(function(){
         if($(this).find("input[name='fromtime']:first").val() && $(this).find("input[name='totime']:first").val() && $(this).attr('tag') == 'grp'){
           schedule += schedule ? "|" : ""; 
           schedule += $(this).find("input[name='daycode']:first").val();
           schedule += pars2;

           // validate here if time from is  greater than to time to. If greater than, log to error_msg
           // author : justin (with e)
           tfrom = convertTimeToNumber($(this).find("input[name='fromtime']:first").val());
           tto = convertTimeToNumber($(this).find("input[name='totime']:first").val());
           if(tfrom > tto) error_msg = error_msg + "* " + convertToDay1($(this).find("input[name='daycode']:first").val()) +"\n";
           // end of validation

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
           schedule += pars2;
           schedule += ($(this).find("input[name='leclab']:checked").val() === undefined ? "" : $(this).find("input[name='leclab']:checked").val());
           schedule += pars2;
           schedule += $(this).find("select[name='course']:first").val();
           schedule += pars2;
           schedule += $(this).find("select[name='section']:first").val();
           schedule += pars2;
           schedule += $(this).find("select[name='subject']:first").val();
           schedule += pars2;
           schedule += $(this).find("select[name='aimsdept']:first").val();
           schedule += pars2;
            schedule += $(this).find("a[tag='weekly_sched']:first").attr("weekly_flexible");
         }
       }); 
       // check if no error
      if(error_msg != ""){
        // alert("Please enter a valid time. \n\nList of days have error in time : \n"+error_msg);
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Please enter a valid time. \n\nList of days have error in time : \n"+error_msg,
            showConfirmButton: true,
            timer: 1000
        })
        $('#saveschedule').removeAttr('disabled');
        $('#loadingSave').attr('hidden',true);
        return;
      }

       var flexisched = $("input[name='flexisched']").is(':checked') ? 'YES' : 'NO';

       var form_data = "timesched=" + schedule;
           form_data += "&job=employee/schedule_info";  
           form_data += "&fsched="+flexisched; 

              if($('input[name=date_active1]').val() == ''){
              // alert("Please set Effectivity Date.");
              Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: "Please set Effectivity Date.",
                  showConfirmButton: true,
                  timer: 1000
              })
              $('#saveschedule').removeAttr('disabled');
              $('#loadingSave').attr('hidden',true);
              return;
           }

         form_data += "&date_active="+$('input[name=date_active1]').val();   
         form_data += "&prev_date_active="+prev_date_active;    
         form_data += "&flexible="+($('input[name=is_flexi_sched]').is(':checked')?'YES':'NO');   
         form_data += "&flexi_hours="+$('input[name=flexi_hours]').val();   
         form_data += "&flexi_breaktime="+$('input[name=flexi_breaktime]').val();   
       $.ajax({
          url: "<?=site_url("employee_/validateinfo")?>",
          data : form_data,
          type : "POST",
          success:function(msg){
            prev_date_active = date_active1;
            // alert($(msg).find("message:eq(0)").text());
            if($(msg).find("message:eq(0)").text() == "Successfully save schedule!"){
              Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: $(msg).find("message:eq(0)").text(),
                  showConfirmButton: true,
                  timer: 1000
              })
            }else{
              Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: $(msg).find("message:eq(0)").text(),
                  showConfirmButton: true,
                  timer: 1000
              })
            }
              
            
            cancontinue = true;
            $('#saveschedule').removeAttr('disabled');
            $('#loadingSave').attr('hidden',true);
          }
       }); 
    });

    $("a[tag='copy_sched']").click(function(){  var obj = $(this).parent().parent();   copytime(obj);
      $("a[tag='copy_sched']").each(function(){   $(this).css({"color":"","background-color":""});    });
      $(this).css({"color":"#D10303"});
    });

    $("a[tag='paste_sched']").click(function(){ var obj = $(this).parent().parent(); pastetime(obj); });

    function copytime(obj){
        if(schedarr.length > 0)  schedarr = [];
        schedarr.push({
            'fromtime'  :obj.find("input[name='fromtime']").val(),
            'totime'    :obj.find("input[name='totime']").val(),
            'tardy_f'   :obj.find("input[name='tardy_f']").val(),
            'absent_f'  :obj.find("input[name='absent_f']").val(),
            'absent_e'  :obj.find("input[name='absent_e']").val(),
            'early_d'   :obj.find("input[name='early_d']").val()
        });  
    }

    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });


    //========================================================================== 

    $("input[name='fromtime'], input[name='totime']").change(function(){
      var time = $(this).val();
      var parent = $(this).parent().parent().parent();
      setTimeDefault($(this).attr('name'), time, parent);
    });

    function setTimeDefault(type, value, parent){
    var hms = value; // your input string
    var a = hms.split(':'); // split it at the colons
    var b = a[1] ? a[1].split(' ') : [0];
    var am = b[1] ? (b[1] == 'AM' ? true : false) : true;
    var pm = b[1] ? (b[1] == 'PM' ? true : false) : false;

    if(pm && a[0]!=12)              a[0] = (+a[0]) + 12 ;
    if(am && a[0]==12)  a[0] = (+a[0]) - 12 ;

    var seconds;
    var newtime;
    var target;
    var plustardy;
    var plusabsent;
    var minusearlyd;

    if(type == 'fromtime'){
      var dow = parent.attr('dayofweek');

      if(a[0]=='0' && b[0]=='00'){
        plustardy = 0;
        plusabsent = 0;
      }else{
        if(parent.is( "tr[dayofweek="+dow+"]:first" ))  plustardy = 16 * 60;
        else                                            plustardy = 30 * 60;

        plusabsent = (60 * 60 * 2);
      }


      seconds = (+a[0]) * 60 * 60 + (+b[0]) * 60 +plustardy; 
      newtime = toHHMMSS(seconds);
      target = parent.children().find("input[name='tardy_f']");
      if(newtime.length == 8) target.val(newtime);

      seconds = (+a[0]) * 60 * 60 + (+b[0]) * 60 + plusabsent; 
      newtime = toHHMMSS(seconds);
      target = parent.children().find("input[name='absent_f']");
      if(newtime.length == 8) target.val(newtime);

    }else if(type == 'totime'){
      // console.log(a[0] + ' // ' + b[0]);

      if(a[0]=='0' && b[0]=='00'){
        minusearlyd = 0;
      }else{
        minusearlyd = (60 * 60 * 2);
      }

      seconds = (+a[0]) * 60 * 60 + (+b[0]) * 60 - minusearlyd; 
      newtime = toHHMMSS(seconds);
      target = parent.children().find("input[name='early_d']");
      if(newtime.length == 8) target.val(newtime);

    }

  }

  $(".course").change(function(){
    var trid = $(this).closest("tr").attr("dowc");
    getAvailableSection($(this).val(), trid);
    getAvailableSubject($(this).val(), trid);
  });

  function toHHMMSS (seconds) {
    var sec_num = parseInt(seconds, 10); // don't forget the second param
    var hours = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);
    var ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    hours = hours < 10 ? '0'+hours : hours;
    minutes = minutes < 10 ? '0'+minutes : minutes;
    seconds = seconds < 10 ? '0'+seconds : seconds;

    return hours+':'+minutes+ ' ' + ampm;
  }

 ///< end of schedule input defaults

 if (typeof schedarr === 'undefined') {
  var schedarr = [];
}



 ///< @Angelica for schedule copy and paste per day

 function copytime(obj){
   if(schedarr.length > 0)  schedarr = [];
   var schedarr_temp = [];
   // alert(obj.attr("dayofweek"));
   $('tr[dayofweek='+obj.attr('dayofweek')+']').each(function(){
     var from          = $(this).find("input[name='fromtime']").val();
     var to            = $(this).find("input[name='totime']").val();
     var lec  = $(this).find("input[name='leclab']:checked").val();
       // var lec           = '';
       var tardy_f       = $(this).find("input[name='tardy_f']").val();
       var absent_f      = $(this).find("input[name='absent_f']").val();
       var early_d       = $(this).find("input[name='early_d']").val();

       if(from != '' || to != '' || lec != undefined || tardy_f != '' || absent_f != '' || early_d != ''){
         schedarr_temp = {
           'fromtime'  :from,
           'totime'    :to,
           'schedtype' :lec,
           'tardy_f'   :tardy_f,
           'absent_f'  :absent_f,
           'early_d'   :early_d,
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
            // var lec           = '';
            var tardy_f       = $(this).find("input[name='tardy_f']").val();
            var absent_f      = $(this).find("input[name='absent_f']").val();
            var early_d       = $(this).find("input[name='early_d']").val();

            if(from != '' || to != '' || lec != undefined || tardy_f != '' || absent_f != '' || early_d != ''){
                schedarr_orig_temp = {
                  'fromtime'  :from,
                  'totime'    :to,
                  'schedtype' :lec,
                  'tardy_f'   :tardy_f,
                  'absent_f'  :absent_f,
                  'early_d'   :early_d,
                };
                schedarr_orig.push(schedarr_orig_temp);
            }
            
        });
         
        if(schedarr_orig.length == 0){
          if(schedarr.length > 0){
            obj.find("input[name='fromtime']").val(schedarr[0]['fromtime']);
            obj.find("input[name='totime']").val(schedarr[0]['totime']);
            obj.find("input[name='leclab']").each(function(){
                if($(this).val() == schedarr[0]['schedtype'])   $(this).prop("checked",true);   
                else                                            $(this).removeAttr("checked");
            });
            obj.find("input[name='tardy_f']").val(schedarr[0]['tardy_f']);
            obj.find("input[name='absent_f']").val(schedarr[0]['absent_f']);
            obj.find("input[name='early_d']").val(schedarr[0]['early_d']);

            if(schedarr.length > 1){
                for (var i = schedarr.length - 1; i >= 1; i--) {
                    $(obj).find("a[tag=add_sched]").click();
                    $(obj).next(':first').find("input[name='fromtime']").val(schedarr[i]['fromtime']);
                    $(obj).next(':first').find("input[name='totime']").val(schedarr[i]['totime']);
                    $(obj).next(':first').find("input[name='leclab']").each(function(){
                        if($(this).val() == schedarr[i]['schedtype'])   $(this).prop("checked",true);   
                        else                                            $(this).removeAttr("checked");
                    });
                    $(obj).next(':first').find("input[name='tardy_f']").val(schedarr[i]['tardy_f']);
                    $(obj).next(':first').find("input[name='absent_f']").val(schedarr[i]['absent_f']);
                    $(obj).next(':first').find("input[name='early_d']").val(schedarr[i]['early_d']);
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
                $(obj).next(':first').find("input[name='tardy_f']").val(schedarr[i]['tardy_f']);
                $(obj).next(':first').find("input[name='absent_f']").val(schedarr[i]['absent_f']);
                $(obj).next(':first').find("input[name='early_d']").val(schedarr[i]['early_d']);
            }
          }
        }

        // obj.find("input[name='fromtime']").val(schedarr[0]['fromtime']);
        // obj.find("input[name='totime']").val(schedarr[0]['totime']);
        // obj.find("input[name='leclab']").each(function(){
        //     if($(this).val() == schedarr[0]['schedtype'])   $(this).prop("checked",true);   
        //     else                                            $(this).removeAttr("checked");
        // });
    }
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
  function convertToDay1(index){
    var day = {SUN:'Sunday',M:'Monday',T:'Tuesday',W:'Wednesday',TH:'Thursday',F:'Friday',S:'Saturday'};
    return day[index];
  }

  function getAvailableSection(course, trid){
    $.ajax({
      url: "<?= site_url('setup_/getAvailableSection') ?>",
      type: "POST",
      data:{course:course},
      success:function(response){
        $("[dowc="+trid+"]").find(".section").html(response).trigger("chosen:updated");
      }
    });
  }

  function getAvailableSubject(course, trid){
    $.ajax({
      url: "<?= site_url('setup_/getAvailableSubject') ?>",
      type: "POST",
      data:{course:course},
      success:function(response){
        $("[dowc="+trid+"]").find(".subject").html(response).trigger("chosen:updated");
      }
    });
  }

  $(".weekly_sched").click(function(){
        var weekly_flexible = $(this).attr("weekly_flexible");
        var week_obj = $(this);
        $.ajax({
          url: "<?=site_url('schedule_/adjust_weekly_schedule_main')?>",
          type: "POST",
          data: {weekly_flexible: GibberishAES.enc(weekly_flexible, toks), toks:toks},
          success:function(response){
            $("div[tag='weekly-display']").html(response);

            $("#week_save_modal").click(function(){
              if(!$("select[name='wSched_main']").val()){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Weekly Schedule is required!',
                    showConfirmButton: true,
                    timer: 1000
                })
              }else{
                $(week_obj).attr("weekly_flexible", $("select[name='wSched_main']").val());
                $("#week_close_modal").click();
              }
              
            });
          }
        });
    })


</script>
<!-- <script type="text/javascript" src="<?=base_url()?>/js/timeValidation.js"></script>
<script src="<?=base_url()?>js/schedule_management.js"></script> -->