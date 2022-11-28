<?php

/**
 * @author Justin
 * @copyright 2016
 */

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\offbusiness.php
 */

$datetoday = date("d-m-Y");
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
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>OB/Excuse (Tardy/Undertime)</b></h4></div>
                   <div class="panel-body">
                        <div style="width: 99.7%;text-align: left;padding: 2px;"><a class="btn btn-primary" id="newrequest" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default">Apply for OB/Excuse (Tardy/undertime)</a></div>          
                    
                    <div id="offbushistory" style="padding-bottom: 31px;"></div>
                    </div>            
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                    
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
/**
 * @modified scripts Angelica
 * orig file: views\employeemod\offbusiness.php
 */

$(document).ready(function(){  
    loadbushistory('',0,'load');
});

$("#newrequest").click(function(){ 
  // $("#myModal").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait.."); 
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "ob_app/ob_apply"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});

/*
 *  FUNCTIONS
 */

function loadbushistory(status,isread='0',action){
   // $("#offbushistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("ob_application_/getEmpOBHistory")?>",
      type     :   "POST",
      data     :    {status : status, isread:isread,target:'DIRECT',action:action},
      success  :   function(msg){
       $("#offbushistory").html(msg);
      }
   });
}
$(".chosen").chosen();
</script>