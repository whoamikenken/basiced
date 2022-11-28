<?php

/**
 * @author Justin 
 * @copyright 2015
 *
 * @modified justin (with e)      
 */

if(isset($empinfo)){
   $empdetails = $empinfo[0]['employeeid'];    
}else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0]['employeeid'];
}

?>
<div class="widgets_area animated fadeIn delay-1s">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Manage Loan</b></h4></div>
                   <div class="panel-body" id="loan_list_div">
                </div>
            </div>
            <div class="panel">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Loan Payment History</b></h4></div>
                   <div class="panel-body" id="loan_history_div">
                </div>
            </div>
    	</div>
    </div>    
</div>            

<script>
function displayLoanContent(action_url, formdata, display_div){
    $.ajax({
        url : action_url,
        type : "POST",
        data : formdata,
        success : function(content){
            $("#"+ display_div).html(content);
        }
    });
}

function loadLoanPage(){
    displayLoanContent("<?=site_url("loan_/showEmpLoanList")?>",{employeeid : "<?=$empdetails?>"}, "loan_list_div");
    displayLoanContent("<?=site_url("loan_/showLoanPaymentHistory")?>",{employeeid : "<?=$empdetails?>"}, "loan_history_div");
}

$(document).ready(function(){
    loadLoanPage();
});

 setTimeout(
  function() 
  {
    $(".widgets_area").removeClass("animated fadeIn");
  }, 2000);

function refreshtab(tabn){
    var form_data = { 
      view : $(tabn).attr("ld")
    }
    
    $.ajax({
            url: "<?=site_url("main/siteportion")?>",
            data: form_data,
            type:"POST",
            success: function(msg){
                $(tabn).html(msg);
            }
        });
}
</script>