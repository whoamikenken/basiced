<?php

/**
 * @author Justin
 * @copyright 2016
 */

$cdatefrom = date("Y-m-d");
$cdateto = date("Y-m-d");
?>

<style type="text/css">
  .form_row{
    padding-bottom: 10px;
  }
</style>

<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
               <div class="panel animated fadeIn delay-1s">
                 <div class="panel-heading"><h4><b>Attendance</b></h4></div>
                   <div class="panel-body" style="margin-top: 30px; margin-bottom: 20px;">
                    <div class="form_row">
                          <label class="field_name align_right">Date</label>
                          <div class="field">
                              <div class="col-md-12"style="padding-left: 0px;">
                                <div class="col-md-5">
                                <div class='input-group time' id="datesetfrom" data-date="<?=$cdatefrom?>" data-date-format="yyyy-mm-dd">
                                  <input class="form-control" size="16" name="datesetfrom" type="text" value="<?=$cdatefrom?>" />
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                              </div>
                            <div class="col-md-5">
                              <div class='input-group time' id="datesetto" data-date="<?=$cdateto?>" data-date-format="yyyy-mm-dd">
                                  <input class="form-control" size="16" name="datesetto" type="text" value="<?=$cdateto?>"/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                              </div>
                            </div>
                              </div>
                          </div>
                      </div>
                      <div class="form_row">
                          <label class="field_name align_right">Employee</label>
                          <div class="field">
                              <div class="col-md-12"style="padding-left: 0px;">
                                <div class="col-md-10">
                                    <select class="chosen" name="employeeid" id="employeeid">
                                        <option value="">All Employee</option>
                                    <?
                                      $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",true);
                                      foreach($opt_type as $val){
                                      ?><option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                                      }
                                    ?>
                                    </select>
                                  </div>
                              </div>
                          </div>
                      </div>
                    
                      <div class="form-row" style="margin-left: 60px;">
                          <div class="field">
                            <div class="col-md-4">
                              <a href="#" class="btn btn-primary pull-right" id="timesheet" style="margin-right: 59px;">Timesheet</a>
                            </div>
                          </div>
                      </div>
                    </div>
                </div>
                <div id="removeAni" class="panel animated fadeIn delay-1s">
                   <div class="panel-heading"><h4><b>Attendance</b></h4></div>
                   <div class="panel-body">
                    <div id="displaylist" style="padding: 5px;"></div>
            </div>
        </div>
    </div>
</div>
<script>
$("#timesheet").click(function(){
   var dfrom = $("input[name='datesetfrom']").val();
   var dto   = $("input[name='datesetto']").val();
   var eid   = $("#employeeid").val();
   if(eid){
    loaddata(dfrom,dto,eid);
   }else    alert("Employee is required!.");
});

setTimeout(
    function() 
    {
      $("#removeAni").removeClass("animated fadeIn delay-1s");
    }, 2000);

function loaddata(dfrom,dto,eid){
    $("#displaylist").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
      url      :   "<?=site_url("employeemod_/fileconfig")?>",
      type     :   "POST",
      data     :   {
                    folder: "employeemod", 
                    view: "checkerlist",
                    dfrom,dto,eid
                    },
      success  :   function(msg){
       $("#displaylist").html(msg);
      }
    });
}

$("#datesetfrom,#datesetto").datetimepicker({
   format: "YYYY-MM-DD"
});
$(".chosen").chosen();
</script>