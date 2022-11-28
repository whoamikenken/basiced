<?php

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\leave.php
 */

?>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="panel animated fadeIn">
                    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Leave</b></h4></div>
                    <div id="leavecontent" class="panel-body" style="padding-bottom: 32px;">      
                    </div>
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                </div>
                <div id="leavehistory" class="panel-body" style="padding: 0px;"></div>
            </div>        
        </div>        
    </div>
</div>
<div class="modal fade" id="mymodalleave" data-backdrop="static"></div>
<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
    loadleavemod();          
});

/*
 *  FUNCTIONS
 */
 // leave
 function loadleavemod(){
    // $("#leavecontent").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   {toks:toks,folder: GibberishAES.enc("employeemod", toks), view: GibberishAES.enc("leave_app/leave_credit_details", toks)},
       success  :   function(msg){
        $("#leavecontent").html(msg);
        loadleavehistory('',0,'load');  
       }
    });
 }
 
function loadleavehistory(status,isread='0',action){
   // $("#changeschedhistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("leave_application_/getEmpLeaveHistory")?>",
      type     :   "POST",
      data     :    {toks:toks,status : GibberishAES.enc(status, toks), isread:GibberishAES.enc(isread, toks),action:GibberishAES.enc(action, toks)},
      success  :   function(msg){
        $("#leavehistory").html(msg);
      }
   });
}
 
 function loaddailyleave(){
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {toks:toks,folder: GibberishAES.enc("employeemod", toks), view: GibberishAES.enc("leaveapplydaily", toks)},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });
 }
 $(".chosen").chosen();
</script>