<?php

/**
 * @author Justin
 * @copyright 2016
 */

$cnoti = $this->employeemod->leavenotif()->num_rows();
?>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background: #343434;">
                        <h5>Leave</h5>
                    </div>

                    <div id="leavecontent" class="well-content" style="padding-bottom: 32px;"></div>                    
                    <br />
                    
                    <!--<div id="leavehistory" class="well-content" style="padding-bottom: 32px;"></div>-->
                    
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                    
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
$(document).ready(function(){
    loadleavemod();          
});

/*
 *  FUNCTIONS
 */
 // leave
 function loadleavemod(){
    $("#leavecontent").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   {folder: "employeemod", view: "leavedetails"},
       success  :   function(msg){
        $("#leavecontent").html(msg);
        loadleavehistory("",<?=$cnoti?>);  
       }
    });
 }
 
 function loadleavehistory(stat = "<?=$this->employeemod->leavenotif()->num_rows() ? "APPROVED" : ""?>",cnoti=""){
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   {folder: "employeemod", view: "leavehistory", stat : stat, cnoti : cnoti},
       success  :   function(msg){
        $("#llh").remove();
        $("#leavecontent").append("<div id='llh'>"+msg+"</div>");
       }
    });
 }
 
 function loaddailyleave(){
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "leaveapplydaily"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });
 }
 $(".chosen").chosen();
</script>