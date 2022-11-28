<?php

/**
 * @author Justin
 * @copyright 2015   
 */
$deduct_array = array();

if(isset($empinfo)){
   $empdetails = $empinfo[0]['employeeid'];    
}else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0]['employeeid'];
}
$deduction = "";
?>

<div class="widgets_area">
    <br /><br />
    <a id="adddeduction" href="#modal-view" class="btn btn-primary" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add Deduction</a>
    <br /><br />
    <div class="panel">
        <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Employee Deduction</b></h4></div>
           <div class="panel-body">
               
                 <form id="deductions" method="POST">
                <input style="display:none" type="" id="emp" datas='<?=$empdetails?>'>
                </form>
            <div id="deductiondetails"></div>
        </div>
    </div>
</div>

<script>
var toks = hex_sha512(" ");
$(document).ready(function()
{
    loaddeductions();
});
function loaddeductions()
{
    $("#deductiondetails").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: {view: GibberishAES.enc("employee/deduction_details" , toks),empdetails: GibberishAES.enc($("#emp").attr("datas") , toks), toks:toks},
        success: function(msg){
           
            $("#deductiondetails").html(msg);
        }
    });
}

$("#adddeduction").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Add Deduction");
    var form_data = {
        view:  GibberishAES.enc("employee/adddeduction" , toks),
        toks:toks
    };
    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });
});

 setTimeout(
  function() 
  {
    $(".widgets_area").removeClass("animated fadeIn");
  }, 2000);
  
</script>