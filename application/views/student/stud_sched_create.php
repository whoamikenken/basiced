<?php

/**
 * @author Angelica 2017
 */

$datetoday = date("d-m-Y");


?>
<style>
.leclab
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
}
#reason
{
  resize: none;
}

@media (min-width: 992px){
  .modal-lg {
      width: 1206px;
  }
}

.form_row{
    padding-bottom: 10px;
  }

  .panel-body{
    margin-top: 30px;
    margin-bottom: 10px;
  }

  .modal-body{
    margin-top: 30px;
  }
</style>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
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
        <center><b><h3 tag="title" class="modal-title">Create Student Schedule Form</h3></b></center>
        </div>
        <div class="modal-body">       
            <div class="form_row">
                <label class="field_name align_right">SY</label>
                <div class="col-md-4">
                    <select class="form-control" name="sy">
                            <option value="ALL">-All School Year-</option>
                      <? foreach ($sylist as $key => $desc) {
                            echo "<option value='$key'>$desc</option>";
                      } ?>
                    </select>
                </div>
                <label class="field_name align_right" style="margin-left: -100px;">Department</label>
                <div class="col-md-4">
                    <select class="chosen col-md-6" name="dept" multiple>
                            <option value="ALL">-All Department-</option>
                      <? foreach ($deptlist as $key => $desc) {
                            echo "<option value='$key'>$desc</option>";
                      } ?>
                    </select>
            </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Year Level</label>
                <div class="col-md-4">
                    <select class="form-control" name="yearLevel">
                            <option value="ALL">-All Year Level-</option>
                      <? foreach ($yearlevellist as $key => $desc) {
                            echo "<option value='$key'>$desc</option>";
                      } ?>
                    </select>
                </div>
                <label class="field_name align_right" style="margin-left: -100px;">Section</label>
                <div class="col-md-4">
                    <select class="chosen col-md-6" name="section">
                            <option value="ALL">-All Section-</option>
                      <? foreach ($sectionlist as $key => $desc) {
                            echo "<option value='$key'>$desc</option>";
                      } ?>
                    </select>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Date Active</label>
                <div class="col-md-4">
                  <div class='input-group date' id="dfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                      <input type='text' class="form-control" size="16" name="dfrom" value="" />
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                  </div>
                </div>
            </div>
            <br>
      
    
      
         
            <div class="form_row" style="border: transparent !important;">
                <table class="table table-striped table-bordered table-hover" width="100%">
                    <thead>
                        <tr>
                            <th rowspan="2"></th>
                            <th rowspan="2"></th>
                            <th rowspan="2">Day of Week</th>
                            <th rowspan="2">From</th>
                            <th rowspan="2">To</th>
                            <th colspan="2">First Half</th>
                            <th colspan="" >Second Half</th>
                        </tr>
                        <tr>
                            <th>Tardy Start</th>
                            <th>Absent Start</th>
                            <th>Half Day Absent</th>
                        </tr>
                    </thead>
                    
                    <tbody id="schedule">
                    <?
                    
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
                          <div class='input-group time'>
                              <input type='text' class="form-control" name="fromtime" value=""  />
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-time"></span>
                              </span>
                          </div>
                        </td>
                    
                        <td>
                          <div class='input-group time'>
                              <input type='text' class="form-control" name="totime" value=""  />
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-time"></span>
                              </span>
                          </div>
                        </td>
                    
                        <td>
                          <div class='input-group time'>
                              <input type='text' class="form-control" name="tardy_f" value=""  />
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-time"></span>
                              </span>
                          </div>
                        </td>
                    
                        <td>
                          <div class='input-group time'>
                              <input type='text' class="form-control" name="absent_f" value=""  />
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-time"></span>
                              </span>
                          </div>
                        </td>
                    
                        <td>
                          <div class='input-group time'>
                              <input type='text' class="form-control" name="absent_e" value=""  />
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-time"></span>
                              </span>
                          </div>
                        </td>
                        
                     
                      </tr>
                    <?}?>
                    </tbody>
                </table>
            </div>
            <br>
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
<script>
var schedarr = [];
$(".chosen").chosen();

$("#dfrom").datetimepicker({
    format: "YYYY-MM-DD"
});


$(".time").datetimepicker({
    format: 'LT'
  });

$("a[tag='add_sched']").click(function(){
  var obj = $(this).parent().parent().parent().clone(true);
  var copy_button  = $('<a class="btn" href="#" tag="copy_sched"  title="Copy"><i class="glyphicon glyphicon-copy"></i></a>').click(function(){var obj = $(this).parent().parent().parent();$("a[tag='copy_sched']").each(function(){   $(this).css({"color":"","background-color":""});    });$(this).css({"color":"#D10303","background-color":"#BABABA"});copytime(obj);});
  var paste_button = $('<a class="btn" href="#" tag="paste_sched" title="Paste"><i class="glyphicon glyphicon-paste"></i></a>').click(function(){var obj = $(this).parent().parent().parent();pastetime(obj);});
  var delete_button = $("<a class='btn' href='#' tag='delete_sched'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){$(this).parent().parent().remove();});
  var timefrom_picker = $("<input name='fromtime' type='text' class='form-control'/>");
  var timeto_picker   = $("<input name='totime' type='text' class='form-control'/>");
  var tardy_picker = $("<input name='tardy_f' type='text' class='form-control'/>");
  var absent_picker   = $("<input name='absent_f' type='text' class='form-control'/>");
  var half_picker = $("<input name='absent_e' type='text' class='form-control'/>");
  var earlyd_picker   = $("<input name='early_d' type='text' class='form-control'/>");
  var toptions        = $(obj).find("td:last").find("div:first").find("select:first").find("option").clone(true);
  var type_drop       = $("<select class='chosen' name='schedtype'><select/>").append(toptions);
  $(obj).find("td:first").find("div:first").html("");
  $(obj).find("td:eq(1)").html($(delete_button));
  $(obj).find("td:eq(2)").html('');
  $(obj).find("td:eq(3)").find("div:first").html("");
  $(obj).find("td:eq(3)").find("div:first").append($(timefrom_picker));
  $(obj).find("td:eq(3)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");  
  $(obj).find("td:eq(4)").find("div:first").html("");
  $(obj).find("td:eq(4)").find("div:first").append($(timeto_picker));
  $(obj).find("td:eq(4)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
  $(obj).find("td:eq(5)").find("div:first").html("");
  $(obj).find("td:eq(5)").find("div:first").append($(tardy_picker));
  $(obj).find("td:eq(5)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
  $(obj).find("td:eq(6)").find("div:first").html("");
  $(obj).find("td:eq(6)").find("div:first").append($(absent_picker));
  $(obj).find("td:eq(6)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
  $(obj).find("td:eq(7)").find("div:first").html("");
  $(obj).find("td:eq(7)").find("div:first").append($(half_picker));
  $(obj).find("td:eq(7)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
  $(obj).find("td:eq(8)").find("div:first").html("");
  $(obj).find("td:eq(8)").find("div:first").append($(earlyd_picker));
  $(obj).find("td:eq(8)").find("div:first").append("<span class='input-group-addon'><span class='glyphicon glyphicon-time'></span></span>");
  $(obj).find("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='absent_e'],input[name='early_d']").datetimepicker({
        format: "LT"
  }); 
  $(obj).insertAfter($(this).parent().parent().parent());   
  $(timefrom_picker).focus();
}); 
$("a[tag='delete_sched']").click(function(){    var obj = $(this).parent().parent().parent().remove();      });

$("#save").unbind().click(function(){
 
      if ($("input[name='dfrom']").val() == "") {
         alert("Date Active is Required!");   
      }else{

           var pars2 = "~u~"; 
           var schedule = "";
           var error_msg = tfrom = tto = "";
           var count = 0;
           $("#schedule").find("tr[tag='grp']").each(function(){
             if($(this).find("input[name='fromtime']:first").val() && $(this).find("input[name='totime']:first").val()){
              count ++ ;
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
             }

           });
               
           // check if no error
            if(error_msg != ""){
              alert("Please enter a valid time. \n\nList of days have error in time : \n"+error_msg);
              return;
            }
            else if (count <= 0 ) {
              alert("You must fill up time!");
            }
            else
            {

                   var form_data = "dfrom="+$("input[name='dfrom']").val();
                       form_data += "&timesched=" + schedule;
                       form_data += "&sy="+$('select[name=sy]').val();
                       form_data += "&dept="+$('select[name=dept]').val();
                       form_data += "&yearLevel="+$('select[name=yearLevel]').val();
                       form_data += "&section="+$('select[name=section]').val();

                       ///< pending, not done
                    console.log(form_data);

                       $.ajax({
                          url: "<?=site_url("student_/saveStudentSched")?>",
                          // dataType: 'json',
                          type : "POST",
                          data : form_data,
                          success:function(msg){
                            // alert(msg);
                            console.log(msg);
                            return;
                             changesched("","","","0","apply");
                              $("#close").click();
                          }
                       });
             }

       }
  


});

$("a[tag='copy_sched']").click(function(){  var obj = $(this).parent().parent().parent();   copytime(obj);
    $("a[tag='copy_sched']").each(function(){   $(this).css({"color":"","background-color":""});    });
    $(this).css({"color":"#D10303","background-color":"#BABABA"});
});
$("a[tag='paste_sched']").click(function(){ var obj = $(this).parent().parent().parent();   pastetime(obj); });



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