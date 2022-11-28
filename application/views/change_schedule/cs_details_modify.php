<?php
/**
 * @modified Angelica Arangco  2017
 */
 $isreadonly = "readonly='true'";
 $isdisabled = "disabled";
 $ishidden   = ($colhead=='hrhead'?'':'hidden=""');
$reason = "";

?>
<style>
#reason
{
  resize: none;
}
</style>
<form id="frmapproved">
<input name="id" value="<?=$csid?>" hidden="" />
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
          <center><b><h3 tag="title" class="modal-title">Edit Change Schedule Application</h3></b></center>
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
                <div class="field" style="width: 36.5%;">
                  <div class='input-group date' id="dfrom" data-date="<?=$date_effective?>" data-date-format="yyyy-mm-dd">
                        <input type='text' class="form-control" size="16" name="dfrom" value="<?=$isTemporary? '' : $date_effective?>"/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- ///< For change schedule on specific dates only -->
            <div class="form_row">
              <label class="field_name align_right">Check this if specific dates only</label>
                <div class="field" style="padding-bottom: 10px;">
                  <div class="col-md-1" style="padding-left: 0px;padding-right: 0px;">
                    <input type="checkbox" class="double-sized-cb" name="specific" id="ctisdo" value="1" <?=$isTemporary?" checked":"" ?>>
                  </div>
                  <div class="col-md-5">
                    <div class="col-md-2 align_right"><b>From</b></div>
                    <div class="col-md-10">
                      <div class='input-group date' id="start" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd">
                          <input type='text' class="form-control" size="16" name="start" value="<?=$isTemporary?$dfrom:''?>" />
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <div class="col-md-2 align_right"><b>To</b></div>
                    <div class="col-md-10">
                      <div class='input-group date' id="end" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd">
                          <input type='text' class="form-control" size="16" name="end" value="<?=$isTemporary?$dto:''?>" />
                          <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                      </div>
                    </div>
                  </div>
                </div>
            </div>

            <!-- <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Status</label>
                <div class="field">
                    <input type="text" name="" value="<?=$status?>" readonly>
                </div>
            </div> -->
            <div class="form_row" style="border: transparent !important;">
                <table class="table table-striped table-bordered table-hover" width="100%">
                    <thead>
                        <tr>
                            <th rowspan="2">Day of Week</th>
                            <th rowspan="2">From</th>
                            <th rowspan="2">To</th>
                            <th colspan="2" <?=$ishidden?>>First Half</th>
                            <th colspan=""  <?=$ishidden?>>Second Half</th>
                            <th rowspan="2" <?=$ishidden?>>Early Dismissal</th>
                            <th class="align_center" rowspan="2" hidden="">Lec</th>
                            <th class="align_center" rowspan="2" hidden="">Lab</th>
                        </tr>
                        <tr <?=$ishidden?>>
                            <th>Tardy Start</th>
                            <th>Absent Start</th>
                            <th>Half Day Absent</th>
                        </tr>
                    </thead>
                    
                    <tbody id="schedule">
                    <?
                    if($csdata){
                        
                        foreach($csdata as $row){
                            $reason = $row->reason;
                            // echo '<pre>';print_r($csdata);
                            ?>
                                <tr tag="grp" detail_id="<?=$row->id?>" dayofweek="<?=$row->day_code?>" dayidx="<?=$row->day_index?>">
                                    <td><?=$row->day_name?></td>
                                    <td>
                                      <div class='input-group time'>
                                          <input type='text' class="form-control" name="fromtime" value="<?=date("h:i A",strtotime($row->starttime))?>"/>
                                          <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-time"></span>
                                          </span>
                                      </div>
                                    </td>
                                
                                    <td>
                                      <div class='input-group time'>
                                          <input type='text' class="form-control" name="totime" value="<?=date("h:i A",strtotime($row->endtime))?>"/>
                                          <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-time"></span>
                                          </span>
                                      </div>
                                    </td>
                                
                                    <td <?=$ishidden?>>
                                      <div class='input-group time'>
                                          <input type='text' class="form-control" name="tardy_f" value="<?=date("h:i A",strtotime($row->tardy_start))?>" />
                                          <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-time"></span>
                                          </span>
                                      </div>
                                    </td>
                                
                                    <td <?=$ishidden?>>
                                      <div class='input-group time'>
                                          <input type='text' class="form-control" name="absent_f" value="<?=date("h:i A",strtotime($row->absent_start))?>" />
                                          <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-time"></span>
                                          </span>
                                      </div>
                                    </td>
                                
                                    <td <?=$ishidden?>>
                                      <div class='input-group time'>
                                          <input type='text' class="form-control" name="absent_e" value="<?=date("h:i A",strtotime($row->absent_half_start))?>" />
                                          <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-time"></span>
                                          </span>
                                      </div>
                                    </td>
                                    
                                    <td <?=$ishidden?>>
                                      <div class='input-group time'>
                                          <input type='text' class="form-control" name="early_d" value="<?=date("h:i A",strtotime($row->early_dismissal))?>" />
                                          <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-time"></span>
                                          </span>
                                      </div>
                                    </td>
                                    
                                    <td class="align_center" hidden=""><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LEC" <?=$row->leclab == "LEC" ? " checked" : ""?> /></td>
                                    <td class="align_center" hidden=""><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LAB" <?=$row->leclab == "LAB" ? " checked" : ""?> /></td>
                                    
                                  </tr>
                            <?
                        }
                    }?>
                    </tbody>
                </table>
            </div>
            <div class="form_row" style="border: transparent !important;">
            <label class="field_name align_right " style="width: auto">Reason</label>
            <div class="field" style="margin-left: 10%;">
                <div style="margin-left:10px;">
                  <textarea rows="4" class="form-control align_left" name="reason" id="reason" style="width: 100%;" placeholder="Reason" required="" ><?=$reason?$reason:""?></textarea>
                </div>
              </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
$(document).ready(function() {
  $(".chosen").chosen();
    if($('input[name=specific]').is(":checked")){
        $("#start,#end").datetimepicker({
            format: "YYYY-MM-DD"
        });  
    }else{
        $("#dfrom").datetimepicker({
            format: "YYYY-MM-DD"
        });  
    }
});  
$("#save").click(function(){
    var newstat = $("#mh_status").val();
    var iscontinue = true;
    $( "#statusAlert" ).remove();
                

    
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
            schedule += $(this).find("input[name='fromtime']:first").val() + " - " + $(this).find("input[name='totime']:first").val();
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
            // schedule += pars2;
            // schedule += ($(this).find("input[name='toremove']:checked").val() === undefined ? "" : $(this).find("input[name='toremove']:checked").val());                                                 
          }
        });


        var    form_data = "timesched=" + schedule;
               form_data += "&csid=<?=$csid?>";
               form_data += "&base_id=<?=$base_id?>";
               form_data += "&employeeid=<?=$employeeid?>";
               form_data += "&date_active="+ $('input[name=dfrom]').val();
               form_data += "&reason="+$("textarea[name=reason]").val();
                                                console.log(form_data);
                                                // return;
     

        $.ajax({
            url:"<?=site_url("schedule_/saveChangeSchedule")?>",
            type:"POST",
            data:form_data,
            success: function(msg){
                $("#close").click();
                alert(msg);
                // console.log(msg);

                location.reload();  
            }
        });
    }
});

///<  @Angelica functions for change schedule with specific dates only

$('input[name=specific],input[name=start],input[name=end]').on('change',function(){
  if($('input[name=specific]').is(":checked")){
      $("input[name='dfrom']").val('');
      $("#dfrom").datetimepicker('remove');
      $("#start,#end").datetimepicker({
          format: "YYYY-MM-DD"
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
  }else{
    $("input[name='start']").val('');
    $("input[name='end']").val('');
    $("#start,#end").datepicker('remove');
    $("#dfrom").datetimepicker({
        format: "YYYY-MM-DD"
    });
    $('tr[tag=grp]').prop('hidden',false);
  }
});


$("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='absent_e'],input[name='early_d']").timepicker({
    format: "LT"
  });
</script>