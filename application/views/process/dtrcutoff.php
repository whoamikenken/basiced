<?php

/**
 * @author Justin
 * @copyright 2016
 */


$cdatefrom = date("Y-m-d");
$cdateto = date("Y-m-d");
?>
<div id="content"> <!-- Content start -->
<div class="widgets_area">
<div class="row">
    <div class="col-md-12">
        <div class="panel animated fadeIn delay-1s">
           <div class="panel-heading"><h4><b>DTR Cut-Off</b></h4></div>
             <div class="panel-body">
            <div class="form_row">
                <label class="field_name align_right">Date</label>
                <div class="field">
                    <div class="col-md-12">
                      <div class="col-md-5" style="padding-left: 0px;">
                      <div class='input-group date' id="datesetfrom" data-date="<?=$cdatefrom?>" data-date-format="yyyy-mm-dd">
                        <input type='text' class="form-control" size="16" name="datesetfrom" type="text" value="<?=$cdatefrom?>"/>
                        <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
                    </div>
                  <div class="col-md-5">
                    <div class='input-group date' id="datesetto" data-date="<?=$cdateto?>" data-date-format="yyyy-mm-dd">
                      <input type='text' class="form-control" size="16" name="datesetto" type="text" value="<?=$cdateto?>"/>
                      <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                    </div>
                  </div>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Department</label>
                <div class="field">
                    <div class="col-md-11">
                        <select class="chosen col-md-6" name="deptid">
                          <option value="">All Department</option>
                        <?
                          $opt_department = $this->extras->showdepartment();
                          foreach($opt_department as $c=>$val){
                          ?><option value="<?=$c?>"><?=$val?></option><?
                          }
                        ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <div class="col-md-11">
                        <select class="chosen col-md-6" name="employeeid">
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
            <div class="form_row">
                <label class="field_name align_right">Data</label>
                <div class="field">
                    <div class="col-md-11">
                        <select class="chosen col-md-6" name="edata">
                            <option value="NEW">ACTUAL DATA</option>
                            <option value="OLD">OLD DATA ( OLDER THAN 2 MONTHS )</option>
                        </select>
                    </div>
                </div>
            </div>
            <br>                                   
            <div class="field">
                <a href="#" class="btn btn-primary" id="butt_tardiness_payroll" style="margin-left: 291px;">Payroll Report</a>
               
            </div>
            </div>
            <div id="displaylogs" style="padding: 5px;"></div>

</div>
</div>
</div>
</div>
</div>

<script>
var print_report = '';

$("#butt_tardiness_payroll").click(function(){
  if($("input[name='datesetfrom']").val()=="" || $("input[name='datesetto']").val()==""){
    alert("Please set a range of date first");
      return;
  }
  $("#displaylogs").html("Loading, please wait...");

  $.ajax({
    url: "<?=site_url("process_/showTardinessForPayroll")?>",
    type: "POST",
    data: {
      datesetfrom: $("input[name='datesetfrom']").val(), 
      datesetto: $("input[name='datesetto']").val(),
      fv : $("select[name='employeeid']").val(),
      deptid : $("select[name='deptid']").val(),
      edata : $("select[name='edata']").val(),
      dcut : "1"
    },
      success: function(msg) {
          $("#displaylogs").html(msg);
      }
    });   
  return false;  
});

$('.chosen').chosen();
$('.date').datetimepicker({
format: 'DD-MM-YYYY'
});


</script>