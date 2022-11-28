<?php

/**
 * @author Justin
 * @copyright 2016
 */

$datetoday = date("d-m-Y");
?>
<!-- newly added by justin (with e) for #ica-hyperion 21090 -->
<style>
  #myModal{
  top: 100px;
  }
</style>
<!--end of newly added by justin (with e) for #ica-hyperion 21090 -->
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background: #343434;">
                        <h5>Official Business</h5>
                    </div>
                    <div class="well-content">
                        <div style="width: 99.7%;text-align: left;padding: 2px;"><a class="btn btn-primary" id="newrequest" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default">New Request</a></div>          
                    
                    <div id="offbushistory" style="padding-bottom: 31px;"></div>
                    </div>            
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                    
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
$(document).ready(function(){  
    loadbushistory();
});

$("#newrequest").click(function(){ 
  $("#myModal").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait.."); 
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "offbusinessapply"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});

/*
 *  FUNCTIONS
 */

function loadbushistory(stat = "",cnoti=""){
   $("#offbushistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("employeemod_/fileconfig")?>",
      type     :   "POST",
      data     :   {folder: "employeemod", view: "offbusinesshistory", stat : stat, cnoti : cnoti},
      success  :   function(msg){
       $("#offbushistory").html(msg);
      }
   });
}
$(".chosen").chosen();
</script>