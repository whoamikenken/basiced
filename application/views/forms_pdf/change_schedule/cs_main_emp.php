<?php
/**
 * @modified Angelica Arangco  2017
 */

$datetoday = date("d-m-Y");
?>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background: #3b5998;">
                        <h5>Change Schedule Request</h5>
                    </div>
                    <div class="well-content">
                        <div style="width: 99.7%;text-align: right;padding: 2px;"><a class="btn btn-primary" id="newrequest" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default">New Request</a></div>          
                    </div>    
                    <div id="changeschedhistory" class="well-content" style="padding-bottom: 32px;"></div>
                    
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                    
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
$(document).ready(function(){  
    changesched("","","","0","load");
});

$("#search").click(function(){
  var category = "", 
        dfrom    = $("input[name='datesetfrom']").val(), 
        dto      = $("input[name='datesetto']").val();
        isread   = '';
    changesched(dfrom,dto,category,isread);
});

$("#newrequest").click(function(){  
    if($(this).prop("disabled")) alert("Please Attach Post Activity first.");
    $.ajax({
        url      : "<?=site_url("schedule_/loadApplyCSForm")?>",
        type     : "POST",
        data     : {
                        // folder: "employeemod", 
                        // view: "changesched_apply"
                    },
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});

/*
 *  FUNCTIONS
 */
function changesched(datefrom, dateto, status, isread='',action){
   $("#changeschedhistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("schedule_/getEmpSchedHistory")?>",
      type     :   "POST",
      data     :    {datefrom : datefrom, dateto : dateto, status : status, isread:isread, action:action},
      success  :   function(msg){
       $("#changeschedhistory").html(msg);
      }
   });
}
$(".chosen").chosen();
$("#datesetfrom,#datesetto").datepicker({
   autoclose: true,
   todayBtn : true
});
</script>