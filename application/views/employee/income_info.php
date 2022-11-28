<?php

/**
 * @author Justin
 * @copyright 2015
 */
$income_array = array();

if(isset($empinfo)){
   $empdetails = $empinfo[0]['employeeid'];    
}else{
   $empinfo = $this->session->userdata("personalinfo"); 
   $empdetails = $empinfo[0]['employeeid'];
}

$income = "";
?>
<div class="widgets_area">
    <br /><br />
    <a id="addincome" href="#modal-view" data-toggle="modal" class="btn btn-primary" ><i class="glyphicon glyphicon-plus-sign"></i> Add Income</a>
    <br /><br />
    <div class="panel">
        <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Employee Income</b></h4></div>
           <div class="panel-body">
            <br/><br>
            <form id="incomes" method="POST">
                <input style="display:none" type="" id="emp" datas='<?=$empdetails?>'>
            </form>
            <div id="incomedetails"></div>
            </div>
        </div>
    </div>
<script>
    var toks = hex_sha512(" ");
$(document).ready(function()
{
    loadincome();
});
function loadincome()
{
    $("#incomedetails").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: {view: GibberishAES.enc("employee/income_detail" , toks),empdetails: GibberishAES.enc($("#emp").attr("datas") , toks), toks:toks},
        success: function(msg){
           
            $("#incomedetails").html(msg);
        }
    });
}
$("#addincome").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Add Income");  
    var form_data = {
        view:  GibberishAES.enc("employee/addincome" , toks),
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
</script>