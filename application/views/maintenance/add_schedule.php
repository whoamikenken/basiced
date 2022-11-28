    <?php

    $sched = "";
    $sched_code = "";   

    $description = "";
    $tardy_start = "";
    $total_hours_per_week = "";
    $flexible = "NO";
    $hours = "";
    $mode = "";
    $breaktime = 0;
    $no_dtr = "NO";
    $dow_code_counter = 0;
    $daycounter=0;

    $qsched = $this->db->query("select schedcode,description,tardy_start,total_hours_per_week,flexible,hours,mode,breaktime,no_dtr from code_schedule where schedid='{$schedid}'")->result();
    if(count($qsched)>0){
     $sched_code = $qsched[0]->schedcode;   
     $description = $qsched[0]->description;
     $tardy_start = $qsched[0]->tardy_start;
     $total_hours_per_week = $qsched[0]->total_hours_per_week;
     $flexible = $qsched[0]->flexible;   
     $hours = $qsched[0]->hours;
     $breaktime = $qsched[0]->breaktime;
     $mode = $qsched[0]->mode;
     $no_dtr = $qsched[0]->no_dtr;
    }

    ?>
    <style>
      .modal-open .modal {
        overflow-x: unset;
        overflow-y: unset;
      }
      .modal.container{
        width: unset;
        margin-left: unset;
      }
      @media (min-width: 992px){
      .modal-lg {
          width: 1410px;
      }

      .form_row{
        padding-bottom: 0px;
      }

      .weekly_flexible.selectize-control.multi .selectize-input {
  height: auto !important;
}
    }
    </style>
    <div class="widgets_area">
    <form id="form_schedule" autocomplete="off">
    <div class="row">
        <div class="col-md-12">
    			<input type="hidden" name="f_hours" value="<?=$total_hours_per_week?>"/>
                <div class="form_row">
                    <label class="field_name align_right">Code</label>
                    <div class="col-md-4">
                        <input type="text" name="f_code" class="form-control isrequired" id="f_code" value="<?=$sched_code?>"/>
                    </div>
                    <label class="field_name align_left" style="width: 80px;">Description</label>
                    <div class="form_group  col-md-4 align_left">
                        <input type="text" name="f_description" class="form-control isrequired" id="f_description" value="<?=$description?>"/>
                    </div>
                    </div>
                    <div class="form_row">
                        <div class="field" style="margin-left: 210px;">
                              <div class="col-md-2" style="width: 50px;margin-top: 0.7%;" >
                                <input style="transform: scale(2);" class="nodtr" type="checkbox" name="f_flexible" value='YES' <?=$flexible == "YES"?"checked":""?>/>
                              </div>
                              <label class="field_name " style="margin-top: 0.5%; width: auto;">Check if schedule is flexible <?= $mode; ?></label>
                              <div class="col-md-1">
                                <input type="text" name="f_hrs" class="form-control" placeholder="hrs" value="<?=$hours?>"  <?=$hours?"":"disabled"?> onkeypress="return numbersonly()"/>
                              </div>
                              <div class="col-md-2" style="margin-left: -2%;">
                                <select name="f_mode" class="chosen  nodtr" <?=$mode?"":"disabled"?> style="width: 50px;">
                              <?  
                                $array = array("day"=>"Per Day","week"=>"Per Week","cutoff"=>"Per Cutoff");
                                foreach($array as $k=>$v)
                                {
                                ?>
                                  <option  <?= $k == $mode ? 'selected' :""?>  value="<?=$k?>"><?=$v?></option>
                                <?                    
                                }
                              ?>
                            </select>
                              </div>
                              <div class="col-md-1" style="margin-left: -2%; padding-top: 8px;">
                                 <b>Has</b>
                              </div>
                              <div class="col-md-1" style="margin-left: -5%;">
                                <input type="text" class="form-control" name="breaktime" value="<?=$breaktime?>" <?=$breaktime?"":"disabled"?> onkeypress="return numbersonly()">
                              </div>
                              <div class="col-md-1"  style="margin-left: -20px; width: auto; padding-top: 8px;">
                                 <b>HR/s breaktime</b>
                              </div>
                              <div class="col-md-2" style="width: 50px;margin-top: 0.7%;display:none;" >
                                <input style="transform: scale(2);" type="checkbox" name="no_dtr" value='YES' <?=$no_dtr == "YES"?"checked":""?>/>
                              </div>
                              <label class="field_name " style="margin-top: 0.5%;display:none;">NO DTR</label>
                        </div>
                </div>
                <!-- <div class="form_row">
                    <label class="field_name align_right">Description</label>
                    <div class="form_group  col-md-9">
                        <input type="text" name="f_description" class="form-control" id="f_description" value="<?=$description?>"/>
                    </div>
                </div> -->
                <div class="well-content" style="border: transparent !important;">
                        <table class="table table-striped table-bordered table-hover col-md-12">
                        <thead style="background-color: #0072c6;">
                            <tr>
                                <th class="col-md-2" rowspan="2"></th>
                                <th class="col-md-1" rowspan="2">Day of Week</th>
                                <th class="" rowspan="2">Weekly Schedule</th>
                                <th class="col-md-1.5" rowspan="2">From</th>
                                <th class="col-md-1.5" rowspan="2">To</th>
                                <th class="col-md-3" colspan="2">First Half</th>
                                <!--
                                <th class="col-md-2">Second Half</th>
                                <th class="col-md-1" rowspan="2" style="text-align: center;">HalfDay Schedule</th>
                                -->
                                <th class="col-md-1.5" rowspan="2"> Under Time End</th>
                            </tr>
                            <tr>
                                <th>Tardy Start</th>
                                <th>Absent Start</th>
                                <!--<th>Half Day Absent</th>-->
                            </tr>
                        </thead>
                        <tbody id="schedule">
    <?
    $weeklySchedules = array("weekly"=>"Weekly","1"=>"1st Week","2"=>"2nd Week","3"=>"3rd Week","4"=>"4th Week","5"=>"5th Week");
    $lasdayofweek = "";
    
    $dow = array("M"=>"Monday","T"=>"Tuesday","W"=>"Wednesday","TH"=>"Thursday","F"=>"Friday","S"=>"Saturday","SUN"=>"Sunday");
    foreach($dow as $dow_code => $dow_desc){
        $sql_schedperday = $this->db->query("SELECT DISTINCT starttime,endtime,dayofweek,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,weekly_flexible
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
            $weekly_flexible      = $mrow_schedperday->weekly_flexible;
            # $mrow_schedperday = $sql_schedperday->row($p); 
            $dow_code_counter += 1;
            $weekly_sched = "";
            foreach (explode(',', $weekly_flexible) as $key => $value) {
                if($weekly_sched != "") $weekly_sched .= "<br>".$weeklySchedules[$value];
                $weekly_sched .= $weeklySchedules[$value];
            }

    ?>                        
                            <tr tag='grp' dayofweek='<?=$dow_code?>'  id='<?=$dow_code_counter?>'>
                              <td class="col-lg-2">
                                <div class="btn-group" style="float: right;">
                                  <?php
                                  if($lasdayofweek!=$dow_code){
                                  ?>
                                  <a class="btn btn-info  nodtr" style=" color: white; margin-right: 10px;" href="#" tag="copy_sched"  title="Copy"><i class="glyphicon glyphicon-duplicate"></i></a>
                                  <a class="btn btn-info  nodtr" style="margin-right: 10px;" href="#" tag="paste_sched" title="Paste"><i class="glyphicon glyphicon-paste"></i></a>
                                  <a class="btn btn-info edit_erase_time  nodtr" style="margin-right: 10px;" href="#" id="erase_time" tag="erase_sched"  title="Clear"><i class="icon-eraser"></i></a>
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
                              <td><div><?=($lasdayofweek!=$dow_code ? $dow_desc : "")?></div></td>
                              <td>
                                <div style="text-align: center">
                                  <a class="btn btn-primary weekly_sched" style=" color: white; margin-right: 10px;" href="#weekly-view" data-toggle="modal" tag="weekly_sched" weekly_flexible = "<?=$weekly_flexible?>"  title="<?=$weekly_sched?>"><i class="glyphicon glyphicon-calendar"></i></a>
                                </div>
                              </td>
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control  ftime <?= ($dow_code == 'SUN' || $dow_code == 'S') ? '' : 'isrequired' ?>" name="fromtime" id="fromtime" type="text" value="<?=$ftime?>"/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                </div>
                              </td>
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control  totime <?= ($dow_code == 'SUN' || $dow_code == 'S') ? '' : 'isrequired' ?>" name="totime" type="text" value="<?=$etime?>"/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                </div>
                          
                              </td>
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control  tardy_f <?= ($dow_code == 'SUN' || $dow_code == 'S') ? '' : 'isrequired' ?>" name="tardy_f" type="text" value="<?=$tardy_s?>"/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                
                                </div>
                              </td>
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control  absent_f <?= ($dow_code == 'SUN' || $dow_code == 'S') ? '' : 'isrequired' ?>" name="absent_f" type="text" value="<?=$absent_s?>"/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                  
                                </div>
                              </td>
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control  early_d <?= ($dow_code == 'SUN' || $dow_code == 'S') ? '' : 'isrequired' ?>" name="early_d" type="text" value="<?=$early_d?>"/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                  
                                </div>
                              </td>
                              <td hidden="">
                                <div class='input-group time'>
                                  <input type='text' class="form-control  nodtr" name="absent_e" type="text" value="<?=$absent_half_s?>"/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
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
                              <td class="col-lg-2" >
                                <div class="btn-group" style="float: right;">
                                  <a class="btn btn-info  nodtr" style=" color: white; margin-right: 10px; " href="#" tag="copy_sched"  title="Copy"><i class="glyphicon glyphicon-duplicate"></i></a>
                                  <a class="btn btn-info  nodtr" style=" margin-right: 10px;" href="#" tag="paste_sched" title="Paste"><i class="glyphicon glyphicon-paste"></i></a>
                                  <a class="btn btn-info erase_time  nodtr" style=" margin-right: 10px;" href="#" id="erase_time" tag="erase_sched" title="Clear"><i class="icon-eraser"></i></a>
                                    <a class="btn btn-primary  nodtr" href="#" tag='add_sched'><i class="glyphicon glyphicon-plus"></i></a>  
                                      
                                </div>
                              </td>
                              <td><div><?=$dow_desc?></div></td>
                              <td>
                                <div style="text-align: center">
                                  <a class="btn btn-primary weekly_sched" style=" color: white; margin-right: 10px;" href="#weekly-view" data-toggle="modal" tag="weekly_sched" weekly_flexible="weekly"  title="Weekly"><i class="glyphicon glyphicon-calendar"></i></a>
                                </div>
                              </td>
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control ftime  nodtr <?= ($dow_code == 'SUN' || $dow_code == 'S') ? '' : 'isrequired' ?>" id="ftime" name="fromtime" type="text" value=""/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                </div>
                                
                              </td>
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control totime  nodtr <?= ($dow_code == 'SUN' || $dow_code == 'S') ? '' : 'isrequired' ?>" id="totime" name="totime" type="text" value=""/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                  
                                </div>
                              </td>
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control tardy_f  nodtr <?= ($dow_code == 'SUN' || $dow_code == 'S') ? '' : 'isrequired' ?>" id="tardy_f" name="tardy_f" type="text" value=""/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                  
                                </div>
                              </td>
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control absent_f  nodtr <?= ($dow_code == 'SUN' || $dow_code == 'S') ? '' : 'isrequired' ?>" id="absent_f" name="absent_f" type="text" value=""/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                  
                                </div>
                              </td>
                              <!-- <td>
                                <div class="input-group bootstrap-timepicker">
                                    <input name="tardy_e" class="col-md-8 input-small align_center" type="text" value=""/>
                                    <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                </div>
                              </td> -->
                              
                              <td>
                                <div class='input-group time'>
                                  <input type='text' class="form-control early_d  nodtr <?= ($dow_code == 'SUN' || $dow_code == 'S') ? '' : 'isrequired' ?>" id="early_d" name="early_d" type="text" value=""/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                  
                                </div>
                              </td>
                              
                              <td hidden="">
                                <div class='input-group time'>
                                  <input type='text' class="form-control nodtr" id="absent_e" name="absent_e" type="text" value=""/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                  </span>
                                </div>
                              </td>
                              
                              <td class="align_center" hidden="">
                                <div>
                                    <input type="checkbox" name="nosched " id="nosched" style="-webkit-transform: scale(1.5);" value="1" />
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
      var toks = hex_sha512(" "); 
    $("#f_code").keypress(function (e) {
        var keyCode = e.keyCode || e.which;
        var regex = /^[A-Za-z0-9]+$/;
        var isValid = regex.test(String.fromCharCode(keyCode));
        return isValid;
    });

    $(".erase_time").click(function(){
        var tr_id = $(this).closest("tr").attr("dayofweek");
        $("tr[dayofweek='"+ tr_id +"']").find(".ftime").val('');
        $("tr[dayofweek='"+ tr_id +"']").find(".totime").val('');
        $("tr[dayofweek='"+ tr_id +"']").find(".tardy_f").val('');
        $("tr[dayofweek='"+ tr_id +"']").find(".absent_f").val('');
        $("tr[dayofweek='"+ tr_id +"']").find(".early_d").val('');
    });

    $(".edit_erase_time").click(function(){
        var tr_id = $(this).closest("tr").attr("id");
        $("tr[id='"+ tr_id +"']").find(".ftime").val('');
        $("tr[id='"+ tr_id +"']").find(".totime").val('');
        $("tr[id='"+ tr_id +"']").find(".tardy_f").val('');
        $("tr[id='"+ tr_id +"']").find(".absent_f").val('');
        $("tr[id='"+ tr_id +"']").find(".early_d").val('');
    });

    var schedarr = [];

    $(document).ready(function(){
    	$("input[name='f_flexible']").trigger( "change" );
      // if ("<?= $no_dtr ?>" == "YES") {
        // $(".nodtr").attr('disabled','disabled');
      // }

      $('[name="no_dtr"]').change(function()
      {
        if ($(this).is(':checked')) {
           $("#schedule").find("input, select").attr('disabled','disabled');
           $("input[name='f_flexible']").attr('disabled','disabled');
           $("input[name='f_hrs']").attr('disabled','disabled');
           $("input[name='f_mode']").attr('disabled','disabled');
           $("input[name='breaktime']").attr('disabled','disabled');
        }else{
          $("#schedule").find("input, select").removeAttr('disabled');
          $("input[name='f_flexible']").removeAttr('disabled','disabled');
           $("input[name='f_hrs']").removeAttr('disabled','disabled');
           $("input[name='f_mode']").removeAttr('disabled','disabled');
           $("input[name='breaktime']").removeAttr('disabled','disabled');
        };
      });
    });

    $("input[name='f_flexible']").change(function(){
      if($(this).is(':checked'))
      {
        $("input[name='f_hrs'],input[name='breaktime']").removeAttr("disabled");
        $("select[name='f_mode']").removeAttr("disabled").trigger('chosen:updated');
      }
      else{
        $("input[name='f_hrs']").attr("disabled",true).val("");
    		$("input[name='breaktime']").attr("disabled",true).val('0');
    		$("select[name='f_mode']").attr("disabled",true).val("").trigger('chosen:updated');
    	}
    });

    $(".time").datetimepicker({
        format: "LT"
      });
  
    $("a[tag='add_sched']").click(function(){
      var obj = $(this).parent().parent().parent().clone(true);
      
      var delete_button = $("<a class='btn btn-danger' href='#' tag='delete_sched'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
        $(this).parent().parent().parent().remove();  
      });
      
      var timefrom_picker = $("<input name='fromtime' class='form-control nodtr' type='text'/>");
      var timeto_picker   = $("<input name='totime' class='form-control nodtr' type='text'/>");
      var tardy_start_picker   = $("<input name='tardy_f' class='form-control nodtr' type='text'/>");
      var absent_start_picker   = $("<input name='absent_f' class='form-control nodtr' type='text'/>");
      var tardy_half_picker   = $("<input name='tardy_e' class='form-control nodtr' type='text'/>");
      var absent_half_picker   = $("<input name='absent_e' class='form-control nodtr' type='text'/>");
      var early_d_picker       = $("<input name='early_d' class='form-control nodtr' type='text'/>");
      var toptions        = $(obj).find("td:last").find("div:first").find("select:first").find("option").clone(true);
      var type_drop       = $("<select class='chosen' name='schedtype'><select/>").append(toptions);
      //alert($(obj).find("td:last").find("div:first").html());
      
      $(obj).find("td:first").find("div:first").html("");
      $(obj).find("td:eq(0)").find("div:first").html($(delete_button));
      // $(obj).find("td:eq(1)").html("");
      $(obj).find("td:eq(1)").find("div:first").html(""); 
      $(obj).find("td:eq(2)").find("div:first").html('<a class="btn btn-primary weekly_sched" style=" color: white; margin-right: 10px;" href="#weekly-view" data-toggle="modal" tag="weekly_sched" weekly_flexible="weekly"  title="Weekly"><i class="glyphicon glyphicon-calendar"></i></a>').click(function(){
        var weekly_flexible = "weekly";
        var week_obj = $(this);
        $.ajax({
          url: "<?=site_url('schedule_/adjust_weekly_schedule')?>",
          type: "POST",
          data: {weekly_flexible: GibberishAES.enc(weekly_flexible, toks), toks:toks},
          success:function(response){
            $("div[tag='weekly-display']").html(response);

            $("#week_save_modal").click(function(){
              if(!$("select[name='wSched']").val()){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Weekly Schedule is required!',
                    showConfirmButton: true,
                    timer: 1000
                })
              }else{
                $(week_obj).attr("weekly_flexible", $("select[name='wSched']").val());
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
      $(obj).find("td:eq(4)").find("div:first").append($(timeto_picker));
      $(obj).find("td:eq(4)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
      
      $(obj).find("td:eq(5)").find("div:first").html("");
      $(obj).find("td:eq(5)").find("div:first").append($(tardy_start_picker));
      $(obj).find("td:eq(5)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
      
      $(obj).find("td:eq(6)").find("div:first").html("");
      $(obj).find("td:eq(6)").find("div:first").append($(absent_start_picker));
      $(obj).find("td:eq(6)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
    
      $(obj).find("td:eq(7)").find("div:first").html("");
      $(obj).find("td:eq(7)").find("div:first").append($(early_d_picker));
      $(obj).find("td:eq(7)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
      
   
      
        $(obj).find("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='tardy_e'],input[name='absent_e'],input[name='early_d']").datetimepicker({
            format: "LT"
      }); 
      $(obj).insertAfter($(this).parent().parent().parent());   
      $(timefrom_picker).focus();
    }); 
    $("a[tag='delete_sched']").click(function(){
      var obj = $(this).parent().parent().parent().remove();  
    });

    $(".save-dtr-setup").unbind().bind("click").click(function(){

      if($("#f_code").val() == ''){
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Code is required!',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  }else if($("#f_description").val() == ''){
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Description is required!',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  }
 var hasconflict = 0;
   var last_trcode = "";
   $("#schedule").find("tr[tag='grp']").each(function(){
    var ftime = $(this).find("input[name='fromtime']:first").val();
    var totime = $(this).find("input[name='totime']:first").val();
    var tardy_f = $(this).find("input[name='tardy_f']:first").val();
    var absent_f = $(this).find("input[name='absent_f']:first").val();
    var early_d = $(this).find("input[name='early_d']:first").val();
        if(last_trcode != $(this).attr("dayofweek")){
           if((ftime == totime && ftime && totime) || (ftime == tardy_f && ftime && tardy_f) || (ftime == absent_f && ftime && absent_f) || (ftime == early_d && ftime && early_d) || (totime == early_d && totime && early_d) || (totime == tardy_f && totime && tardy_f) || (tardy_f == absent_f && tardy_f && absent_f) || (absent_f == early_d && absent_f && early_d)){
             
             // return;
              // hasconflict++;
           }
         }
         /*if((ftime > totime && ftime && totime) || (ftime > tardy_f && ftime && tardy_f) || (ftime > absent_f && ftime && absent_f) || (ftime > early_d && ftime && early_d) || (totime > early_d && totime && early_d)){
          
           // return;
           hasconflict++;
         }*/
         var last_trcode = $(this).attr("dayofweek");
       });

       if(hasconflict>0){
         Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Invalid Schedule',
            showConfirmButton: true,
            timer: 1000
          });
         return;
       } 

      var isexist = isScheduleCodeExists();
       if(isexist >= 1 && !"<?=$schedid?>"){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Code already exists!",
            showConfirmButton: true,
            timer: 1000
        });

        return;
       }

      var format = /[ `!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;

      if(format.test($("input[name='f_code']").val())){
          Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Code has invalid characters.",
            showConfirmButton: true,
            timer: 1000
          });
          return;
      }

      // return;
       var pars2 = "~u~"; 
       var schedule = "";
       var timediff = 0;
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
        schedule += pars2;
        schedule += $(this).find("a[tag='weekly_sched']:first").attr("weekly_flexible");
    	   
         }
       }); 
       
      var isflexi = "NO";
      if($("input[name='f_flexible']").is(":checked")) isflexi = "YES";
    	var form_data = "f_code="+GibberishAES.enc($("input[name='f_code']").val(), toks);
        form_data+= "&f_hours="+GibberishAES.enc($("input[name='f_hours']").val(), toks); 
        form_data+= "&f_description="+GibberishAES.enc($("input[name='f_description'").val(), toks); 
        form_data+= "&f_flexible="+GibberishAES.enc(isflexi, toks);
        form_data+= "&f_hrs="+GibberishAES.enc($("input[name='f_hrs']").val(), toks);
        form_data+= "&f_mode="+GibberishAES.enc($("select[name='f_mode']").val(), toks);
        form_data+= "&breaktime="+GibberishAES.enc($("input[name='breaktime']").val(), toks);
        form_data+= "&no_dtr="+GibberishAES.enc($("input[name='no_dtr']").val(), toks);
    		form_data+="&timesched=" + GibberishAES.enc(schedule, toks); 
    		form_data+="&schedid="+ GibberishAES.enc("<?=$schedid?>", toks);
    		form_data+="&isedit="+ GibberishAES.enc("<?=$isedit?>", toks);
        form_data+="&toks="+toks;
        var encodedData = encodeURIComponent(window.btoa(form_data));
      // var iscontinues = validateForm($("#form_schedule"));
    	var iscontinues = true;
    	// if($("input[name=f_code] , input[name=f_description]").val() == "")
    	// {	
    	// 	iscontinue = false;
    	// }
    	// if($("input[name=f_flexible]").is(':checked'))
    	// {
    	// 	if($("input[name=f_hrs]").val() <= 0)
    	// 	{
    	// 		iscontinue = false;
    	// 	}
    	// }
    	 
    	// if($('[name="no_dtr"]').is(':checked'))
     //  {
     //    if ($("#f_code").val()=="") {alert("Code is required!"); return;}
     //    if ($("#f_description").val() =="") {alert("Description is required!"); return;}
     //  }else{
     //    if ($("#f_code").val()=="") {alert("Code is required!"); return;}
     //    if ($("#f_description").val() =="") {alert("Description is required!"); return;}
     //    if ($("[name='fromtime']").val() =="") {alert("Please Fill up Time Schedule!"); return;}
     //    if ($("[name='totime']").val() =="") {alert("Please Fill up Time Schedule!"); return;}
     //    if ($("[name='tardy_f']").val() =="") {alert("Please Fill up Time Schedule!"); return;}
     //    if ($("[name='absent_f']").val() =="") {alert("Please Fill up Time Schedule!"); return;}
     //    if ($("[name='early_d']").val() =="") {alert("Please Fill up Time Schedule!"); return;}
     //  }
        if(iscontinues){
          var res='';
      		$.ajax({
      			url: "<?=site_url("maintenance_/saveschedule")?>",
      			data : {formdata:encodedData},
      			type : "POST",
      			success:function(msg){
      				$("#modalclose").click();
              res = msg.substring(0, 12);
              if(res == "Successfully"){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: msg,
                    showConfirmButton: true,
                    timer: 1000
                })
              }else{
                Swal.fire({
                    icon: 'success',
                    title: 'success!',
                    text: msg,
                    showConfirmButton: true,
                    timer: 1000
                })
              }
              location.reload();
      			}
      		}); 
        }
    });


    $("a[tag='copy_sched']").click(function(){  var obj = $(this).parent().parent().parent(); console.log(obj);  copytime(obj);
        $("a[tag='copy_sched']").each(function(){   $(this).css({"color":"","background-color":""});    });
        $(this).css({"color":"#D10303","background-color":"#blue"});
    });
    $("a[tag='paste_sched']").click(function(){ var obj = $(this).parent().parent().parent(); console.log(obj);  pastetime(obj); });
    // $("#erase_time").click(function(){ var obj = $(this).parent().parent().parent();   erasetime(obj); });

    function isScheduleCodeExists(){
      var isexists = "";
      var code = $("input[name='f_code']").val();
      $.ajax({
        url: "<?=site_url('schedule_/isScheduleCodeExists')?>",
        type: "POST",
        data: {code: GibberishAES.enc(code, toks), toks:toks},
        async: false,
        success:function(response){
          isexists = response;
        }
      });

      return isexists;
    }

    ///< modified @Angelica for schedule copy and paste per day
    function copytime(obj){
        if(schedarr.length > 0)  schedarr = [];
        var schedarr_temp = [];
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
        if(schedarr_orig.length == 1){
          obj.find("input[name='fromtime']").val(schedarr_orig[0]['fromtime']);
          obj.find("input[name='totime']").val(schedarr_orig[0]['totime']);
          obj.find("input[name='leclab']").each(function(){
              if($(this).val() == schedarr_orig[0]['schedtype'])   $(this).prop("checked",true);   
              else                                            $(this).removeAttr("checked");
          });
          obj.find("input[name='tardy_f']").val(schedarr_orig[0]['tardy_f']);
          obj.find("input[name='absent_f']").val(schedarr_orig[0]['absent_f']);
          obj.find("input[name='early_d']").val(schedarr_orig[0]['early_d']);
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
              $(obj).next(':first').find("input[name='tardy_f']").val(schedarr_orig[i]['tardy_f']);
              $(obj).next(':first').find("input[name='absent_f']").val(schedarr_orig[i]['absent_f']);
              $(obj).next(':first').find("input[name='early_d']").val(schedarr_orig[i]['early_d']);
          }
        }


        // obj.find("input[name='fromtime']").val(schedarr[0]['fromtime']);
        // obj.find("input[name='totime']").val(schedarr[0]['totime']);
        // obj.find("input[name='leclab']").each(function(){
        //     if($(this).val() == schedarr[0]['schedtype'])   $(this).prop("checked",true);   
        //     else                                            $(this).removeAttr("checked");
        // });
    }




    function pad (str) {
    	if (str < 10) {
    		return "0"+str;
    	}
    	else{
    		return str;
    	}
    }

    function numbersonly(myfield, e, dec)
    {
        var key;
        var keychar;
        
        if (window.event)
           key = window.event.keyCode;
        else if (e)
           key = e.which;
        else
           return true;
        keychar = String.fromCharCode(key);
        if ((key==null) || (key==0) || (key==8) || 
            (key==9) || (key==13) || (key==27) )
           return true;
        else if ((("0123456789").indexOf(keychar) > -1))
           return true;
        else if (dec && (keychar == "."))
           {
           myfield.form.elements[dec].focus();
           return false;
           }
        else
           return false;
    }

    $(".weekly_sched").click(function(){
        var weekly_flexible = $(this).attr("weekly_flexible");
        var week_obj = $(this);
        $.ajax({
          url: "<?=site_url('schedule_/adjust_weekly_schedule')?>",
          type: "POST",
          data: {weekly_flexible: GibberishAES.enc(weekly_flexible, toks), toks:toks},
          success:function(response){
            $("div[tag='weekly-display']").html(response);

            $("#week_save_modal").click(function(){
              if(!$("select[name='wSched']").val()){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Weekly Schedule is required!',
                    showConfirmButton: true,
                    timer: 1000
                })
              }else{
                $(week_obj).attr("weekly_flexible", $("select[name='wSched']").val());
                $("#week_close_modal").click();
              }
              
            });
          }
        });
    })

    $(".chosen").chosen();
    </script>