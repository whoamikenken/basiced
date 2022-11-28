<?php

/**
 * @author Justin
 * @copyright 2016
 */

$datetoday = date("d-m-Y");
?>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background: #4a4a4a;">
                        <h5>Change Schedule Request</h5>
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
    changesched();
});

$("#search").click(function(){
    changesched()
});

$("#newrequest").click(function(){  
    if($(this).prop("disabled")) alert("Please Attach Post Activity first.");
    $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "changesched_apply"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});

/*
 *  FUNCTIONS
 */
function changesched(){
   $("#changeschedhistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("employeemod_/fileconfig")?>",
      type     :   "POST",
      data     :   {folder: "employeemod", view: "changesched_details", category: "", dfrom : $("input[name='datesetfrom']").val(), dto : $("input[name='datesetto']").val(), indi : 1},
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