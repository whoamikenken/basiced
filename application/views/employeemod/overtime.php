<?php
    $datetoday = date("Y-m-d");
?>
<style type="text/css">
   .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="panel animated fadeIn">
                    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Overtime</b></h4></div>
                    <div class="panel-body">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <!-- <div class="col-md-12">
                                <label class="field_name" class="col-md-2" style="float: left;">Date</label>
                                <div class="col-md-5" style="width: 15%;">
                                    <div class='input-group date' id='datetimepicker1' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" name="datesetfrom" value="<?=$datetoday?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-5" style="width: 15%;">
                                    <div class='input-group date' id='datesetto' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" name="datesetto" value="<?=$datetoday?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div> -->
                            <div class="col-md-12" style="padding-left: 0px;">
                                <!-- <a href="#" class="btn btn-primary" id="search">Search</a>&nbsp;&nbsp;&nbsp; -->
                                <a class="btn btn-primary" id="newrequest" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default">New Request</a>
                            </div>  
                            
                            <div class="panel-body" id="otrequest" style="padding: 0px; margin-top: 50px;"></div>
                        </div>
                    </div>
                    
                </div>
            </div>        
        </div>        
    </div>
</div>
<div class="modal fade" id="myModal" data-backdrop="static"></div>
<div class="modal fade" id="myModalatt" data-backdrop="static"></div>

<script>
$(document).ready(function(){  
    loadOvertimehistory("","","",0,'load');
    // loadbushistory();

    setTimeout(
      function() 
      {
        $("#removeAni").removeClass("animated fadeIn delay-1s");
      }, 2000);
});

$("#search").click(function(){
    // loadbushistory();
    var category = "", 
        dfrom    = $("input[name='datesetfrom']").val(), 
        dto      = $("input[name='datesetto']").val(),
        isread   = '';
    loadOvertimehistory(dfrom,dto,category,isread);
});

$("#newrequest").click(function(){  
    $.ajax({
        // url      :   "<?=site_url("employeemod_/fileconfig")?>",
        url      : "<?=site_url("overtime_/loadApplyOTForm")?>",
        type     : "POST",
        // data     : {folder: "employeemod", view: "overtimeapply"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});

/*
 *  FUNCTIONS
 */

 function loadOvertimehistory(status, datefrom, dateto,isread="0",action){
   $("#otrequest").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      // url      :   "<?=site_url("employeemod_/fileconfig")?>",
      url      :   "<?=site_url("overtime_/getEmpOTHistory")?>",
      type     :   "POST",
      data     :   {datefrom : datefrom, dateto : dateto, status : status, isread:isread,action:action},
      success  :   function(msg){
       $("#otrequest").html(msg);
      }
   });
}

function loadbushistory(stat = "<?=$this->employeemod->overtimenotif()->num_rows() ? "APPROVED" : ""?>"){
   $("#otrequest").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      // url      :   "<?=site_url("employeemod_/fileconfig")?>",
      type     :   "POST",
      data     :   {folder: "employeemod", view: "overtimehistory", dfrom : $("input[name='datesetfrom']").val(), dto : $("input[name='datesetto']").val(), stat : stat},
      success  :   function(msg){
       $("#otrequest").html(msg);
      }
   });
}
$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();
</script>