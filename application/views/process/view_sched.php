<?php

/**
 * @author Justin
 * @copyright 2015
 */

$dtoday = date('Y-m-d');
?>
<!--<a class="btn btn-primary" href="#modal-view" data-toggle='modal' href="#" id="addadjustment"><i class="glyphicon glyphicon-plus-sign"></i> Add Adjustment</a>-->
<div class="well-content" style='border: transparent !important;'>
<table id="adjustment_datatable" class="table table-striped table-bordered table-hover">
    <thead>
      <?if($chkopt == "chkemp"){
        $sqldata = $this->employee->getindividualemployee($employeeid);
        foreach($sqldata as $row){
            $lname = $row->fullname;
            $emid = $row->employeeid;
        }
      ?>
      <tr>
        <td colspan="4"><b><?=$lname. "($emid)"?></b></td>
      </tr>
      <?}else{?>
        <tr>
            <td colspan="4"><b><?=$this->extras->showShift($shifttype)?></b></td>
        </tr>
      <?}?>
      <tr style="background-color: #0072c6;">
        <!--<th class="col-md-2">Day</th>-->
        <th class="col-md-2">Date From</th>
        <th class="col-md-2">Date To</th>
        <th class="col-md-2">Start Time</th>
        <th class="col-md-2">End Time</th>
        <th class="col-md-2">Tardy</th>
        <th class="col-md-2">Absent</th>
        <th class="col-md-2">Halfday Absent</th>
        <!--<th class="col-md-2">Halfday Schedule</th>-->
        <th class="col-md-2">Early Dismissal</th>
      </tr> 
    </thead>  
      <tbody>
      <div id="display">
      <tr>
        <!--
        <td width='12%'>
            <select class="col-md-18" name="selday" id="selday"><?=$optdate = $this->extras->showDay()?></select>
        </td>
        -->
        <td class="col-md-2">
          <div class="input-group date" id="dfrom" data-date-format="yyyy-mm-dd" data-date="<?=$dtoday?>">
            <input class="align_center" size="16" name="dfrom" type="text" value="<?=$dtoday?>" readonly>
            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
          </div>
        </td>
        
        <td class="col-md-2">
          <div class="input-group date" id="dto" data-date-format="yyyy-mm-dd" data-date="<?=$dtoday?>">
            <input class="align_center" size="16" name="dto" type="text" value="<?=$dtoday?>" readonly>
            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
          </div>
        </td>
        
        <td class="col-md-2">
          <div class="input-group bootstrap-timepicker">
            <input class="col-md-8 input-small align-center" type="text" name="fromtime" id="fromtime" value="" />
            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
          </div>
        </td>
        <td class="col-md-2">
          <div class="input-group bootstrap-timepicker">
            <input class="col-md-8 input-small align-center" type="text" name="totime" id="totime" value="" />
            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
          </div>
        </td>
        <td class="col-md-2">
          <div class="input-group bootstrap-timepicker">
            <input class="col-md-8 input-small align-center" type="text" name="tardy" id="tardy" value="" />
            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
          </div>
        </td>
        <td class="col-md-2">
          <div class="input-group bootstrap-timepicker">
            <input class="col-md-8 input-small align-center" type="text" name="fabsent" id="fabsent" value="" />
            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
          </div>
        </td>
        <td class="col-md-2">
          <div class="input-group bootstrap-timepicker">
            <input class="col-md-8 input-small align-center" type="text" name="habsent" id="habsent" value="" />
            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
          </div>
        </td>
        <!--
        <td class="col-md-2">
          <div class="align_center">  
            <input class="col-md-2" type="checkbox" style="-webkit-transform: scale(1.5);" name="halfsched" id="halfsched" value="1" />
          </div>          
        </td>
        -->
        <td class="col-md-2">
          <div class="input-group bootstrap-timepicker">
            <input class="col-md-8 input-small align-center" type="text" name="earlyd" id="earlyd" value="" />
            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
          </div>
        </td>
      </tr>
</table>
</div>
<div id="msched"></div>
<table>
        <tr>
        <td class="col-md-2">
        <div class="field" style="margin-top: 10px;">
            <input type="button" class="btn btn-primary" id="savesched" value="Save" />
        </div>
        </td>
        </tr>
</table>
<br />
      </tbody>
      <?if($chkopt == "chkemp"){?>
        <table id="adjustment_datatable" class="table table-striped table-bordered table-hover datatable">
          <tfoot id="showh">
          </tfoot>
        </table>
      <?}?>
</div>
<script>

  $(document).ready(function(){
    var table = $('#adjustment_datatable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

if("<?=$chkopt?>" == "chkemp")  loadofficialschedhis();
$("input[name='fromtime'],input[name='totime'],input[name='tardy'],input[name='fabsent'],input[name='habsent'],input[name='earlyd']").timepicker({
    minuteStep: 1,
    showSeconds: false,
    showMeridian: true,
    defaultTime: false
  });

$("#dfrom,#dto").datepicker({
    autoclose: true,
    todayBtn : true
});

$("#savesched").click(function(){
    var employeeid = "<?=$employeeid?>";
    var chkopt = "<?=$chkopt?>";
    var form_data = $("#request_form").serialize();
    var tfrom = tto = "";
    if(chkopt == "chkemp"){
        if($("#fromtime").val() == ""){
            alert("Please set the Start time..");
            $("#fromtime").focus();
        }else if($("#totime").val() == ""){
            alert("Please set the End time..");
            $("#totime").focus();
        }else if($("#tardy").val() == ""){
            alert("Please set the tardy time..");
            $("#tardy").focus();
        }else if($("#fabsent").val() == ""){
            alert("Please set the Absent time..");
            $("#fabsent").focus();
        }else if($("#habsent").val() == ""){
            alert("Please set the Halfday Absent time..");
            $("#habsent").focus();
        }else if($("#totime").val() != "" && $("#fromtime").val() != ""){
          tfrom = convertTimeToNumber($("#fromtime").val());
          tto = convertTimeToNumber($("#totime").val());
          if(tfrom > tto){
            alert("Invalid Start Time or End Time..");
          }
        }else{
        $("#display").hide();
        $("#savesched").hide();
        $("#msched").html("<td colspan='5' style='text-align: center'>Saving Please Wait.. <br /> <img src='<?=base_url()?>images/loading42.gif' /></td>");
        $.ajax({
           url : "<?=site_url("process_/saveschedbyemp")?>",
           type : "POST",
           data : form_data,
           success : function(msg){
            alert(msg);
            loadofficialschedhis();
            $("#savesched").show();
            $("#display").show();
            $("#msched").hide();
           }
        });
        }
    }else{
        if($("#fromtime").val() == ""){
            alert("Please set the Start time..");
            $("#fromtime").focus();
        }else if($("#totime").val() == ""){
            alert("Please set the End time..");
            $("#totime").focus();
        }else if($("#tardy").val() == ""){
            alert("Please set the tardy time..");
            $("#tardy").focus();
        }else if($("#fabsent").val() == ""){
            alert("Please set the Absent time..");
            $("#fabsent").focus();
        }else if($("#habsent").val() == ""){
            alert("Please set the Halfday Absent time..");
            $("#habsent").focus();
        }else{
        $("#display").hide();
        $("#savesched").hide();
        $("#msched").html("<td colspan='5' style='text-align: center'>Saving Please Wait.. <br /> <img src='<?=base_url()?>images/loading42.gif' /></td>");
        $.ajax({
           url : "<?=site_url("process_/saveschedbyshift")?>",
           type : "POST",
           data : form_data,
           success : function(msg){
            alert(msg);
            $("#savesched").show();
            $("#display").show();
            $("#msched").hide();
           }
        });
        }
    }
});

function loadofficialschedhis(){
    var employeeid = "<?=$employeeid?>";
    $.ajax({
       url : "<?=site_url("process_/viewofficialschedhis")?>",
       type : "POST",
       data: {employeeid:employeeid},
       success: function(msg){
        $("#showh").html(msg);
       }
    });
}
</script>
<script type="text/javascript" src="<?=base_url()?>/js/timeValidation.js"></script>