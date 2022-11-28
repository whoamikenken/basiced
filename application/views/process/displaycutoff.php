<?php

/**
 * @author Justin
 * @copyright 2015
 */

$cutofffrom = "";
$cutoffto   = "";

if(isset($cutoff)){
    $cutoff = explode("|",$cutoff);
    if(count($cutoff) > 1){
        $cutofffrom = $cutoff[0];
        $cutoffto   = $cutoff[1];
    }
        
}
?>
<div class="panel">
    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Posted Cut-Off</b></h4></div>
    <div class="panel-body" style="margin-top: 5px;">
        <a href="#modal-view" data-toggle="modal" href="#"  id="addnewcutoff"><i class="icon glyphicon glyphicon-envelope" style="border: 1px solid yellow;"></i><strong>&nbsp;New Cut-Off</strong></a>
        <br><br>
        <table class="table table-bordered table-striped table-hover">
            <thead style="background-color: #0072c6;">
                <tr>
                    <th class="align_center" rowspan="2"></th>
                    <th class="align_center" colspan="2">Cut-Off Date</th>
                    <th class="align_center" rowspan="2">Schedule</th>
                    <th class="align_center" rowspan="2">Quarter</th>
                    <th class="align_center" colspan="2">Payroll Date</th>
                    <!-- <th class="align_center" rowspan="2">Teaching Date Posted</th> -->
                    <th class="align_center" colspan="2" >Confirmation Date</th>
                </tr>
                <tr style="background-color: #0072c6;">
                    <th>From</th>
                    <th>To</th>
                    <th>From</th>
                    <th>To</th>
                    <th>From</th>
                    <th>To</th>
                </tr>
            </thead>
            <tbody id="coffcontent">
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function(){
       loadcoffcontent(); 
    });

    $("#addnewcutoff").click(function(){  
        $("#modal-view").find("h3[tag='title']").html("Add New Cut-Off"); 
        $("#modal-view").find("div[tag='display']").html("Loading, please wait...");
        $("#modal-view").find(".err").remove();
        $("#button_save_modal").text("Save");
        $.ajax({
            url:"<?=site_url("process_/addnewcutoff")?>",
            success: function(msg){
                $("#modal-view").find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
                $("#modal-view").find("div[class='modal-footer']").append('<span class="err pull-left" style="color:red;"></span>');
                $("#modal-view").find("div[tag='display']").html(msg);
                
            }
        }); 
    });

    function loadcoffcontent(){
        $("#coffcontent").html("<tr><td colspan='4'><img src='<?=base_url()?>images/loading.gif'> Loading Please Wait..</img></td></tr>");
        $.ajax({
           url: "<?=site_url("main/siteportion")?>", 
           type: "POST",
           data: {
                    view  : "process/viewcutoff",
                    dfrom : "<?=$cutofffrom?>",
                    dto   : "<?=$cutoffto?>"
                 },
           success: function(msg){
            $("#coffcontent").html(msg);
           }
        });
    }
</script>