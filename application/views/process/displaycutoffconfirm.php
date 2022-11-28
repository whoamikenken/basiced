<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
$cutofffrom = "";
$cutoffto   = "";

if(!empty($cutoff)){
    $cutoff = explode("|",$cutoff);
    $cutofffrom = $cutoff[0];
    $cutoffto   = $cutoff[1];
}
?>
<div class="well-content">
    <div class="well-head">
    <div class="display"><b>Department :</b></div>
        <select class="form-control" id="dept" style="width: auto;">
            <?=$this->extras->listEmpDept()?>
        </select>
    <div class="align_right" style="float: right;"><a href="javascript:loadxls()"><i class="icon-print"> Printer-Friendly</i></a></div>    
    </div>
    <div class="well-header">
        <h5><b><?=$cat?></b></h5>
    </div><br />
    <div id="coffcontent"></div><br />
</div>
<script>
$(document).ready(function(){
   loadcoffcontent(); 
});
$("#dept").change(function(){
   loadcoffcontent(); 
});
function loadcoffcontent(){
    $("#coffcontent").html("<tr><td colspan='4'><img src='<?=base_url()?>images/loading.gif'> Loading Please Wait..</img></td></tr>");
    $.ajax({
       url: "<?=site_url("main/siteportion")?>", 
       type: "POST",
       data: {
                view  : "process/viewcutoffconfirm",
                dfrom : "<?=$cutofffrom?>",
                dto   : "<?=$cutoffto?>",
                dept  : $("#dept").val()
             },
       success: function(msg){
        $("#coffcontent").html(msg);
       }
    });
}

function loadxls(){
    var params  = "?view=reports_excel/emp_confirmed";
        params += "&dfrom=<?=$cutofffrom?>";
        params += "&dto=<?=$cutoffto?>";
        params += "&dept="+$("#dept").val();
    window.open("<?=site_url("reports_/reportloader")?>"+params,"Confirmed"); 
}
$(".chosen").chosen();
</script>