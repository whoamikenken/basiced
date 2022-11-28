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
    <a id="addincome_adj" href="#modal-view" class="btn" style="background-color: #337ab7; border-color: #2e6da4; color: white;" data-toggle="modal"><i class="glyphicon glyphicon-plus-sign"></i> Add Income Adjustment</a>
    <br /><br />
    <div class="panel">
        <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Employee Income Adjustment</b></h4></div>
           <div class="panel-body">
            <form id="incomes" method="POST">
                <input style="display:none" type="" id="emp" datas='<?=$empdetails?>'>
            </form>
            <div id="incomedetails_adj">
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function()
{
    loadincome_adj();
});
function loadincome_adj()
{
    $("#incomedetails_adj").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: {view:"employee/income_adj_detail",empdetails:$("#emp").attr("datas")},
        success: function(msg){
           
            $("#incomedetails_adj").html(msg);
        }
    });
}

$("#addincome_adj").click(function(){
    $("#modal-view").find("h3[tag='title']").text("Add Income Adjustment");  
    var form_data = {
        view: "employee/addincome_adj"
    };
    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
             // loadincome_adj();
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });
});
</script>