<?php

/**
 * @author Justin
 * @copyright 2015
 */

$cdatefrom = date("Y-m-d");
$cdateto = date("Y-m-d");
?>
<div id="content"> <!-- Content start -->
<div class="widgets_area">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well blue">
                            <div class="well-header">
                                <h5>Messages</h5>
                                <div id="dialog" class="display_content" hidden=""></div>
                                <ul>
                                    <li class="color_pick"><a href="#"><i class="glyphicon glyphicon-th"></i></a>
                                        <ul>
                                            <li><a class="blue set_color" href="#"></a></li>
                                            <li><a class="light_blue set_color" href="#"></a></li>
                                            <li><a class="grey set_color" href="#"></a></li>
                                            <li><a class="pink set_color" href="#"></a></li>
                                            <li><a class="red set_color" href="#"></a></li>
                                            <li><a class="orange set_color" href="#"></a></li>
                                            <li><a class="yellow set_color" href="#"></a></li>
                                            <li><a class="green set_color" href="#"></a></li>
                                            <li><a class="dark_green set_color" href="#"></a></li>
                                            <li><a class="turq set_color" href="#"></a></li>
                                            <li><a class="dark_turq set_color" href="#"></a></li>
                                            <li><a class="purple set_color" href="#"></a></li>
                                            <li><a class="violet set_color" href="#"></a></li>
                                            <li><a class="dark_blue set_color" href="#"></a></li>
                                            <li><a class="dark_red set_color" href="#"></a></li>
                                            <li><a class="brown set_color" href="#"></a></li>
                                            <li><a class="black set_color" href="#"></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="well-content">
                            <div class="well-head">
                                <select id="category">
                                    <?
                                    $opt = $this->extras->showcstat();
                                    foreach($opt as $key=>$val){
                                        $sel = $key == "PENDING" ? " selected" : "";
                                        ?><option value="<?=$key?>" <?=$sel?> ><?=$val?></option><?
                                    }
                                    ?>
                                </select>
                                <label class="field_name align_right">Date From : </label>
                                <div class="input-group date" id="datesetfrom" data-date="<?=$cdatefrom?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="datesetfrom" type="text" placeholder="Calendar" readonly>
                                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                                <label class="field_name align_right">Date To : </label>
                                <div class="input-group date" id="datesetto" data-date="<?=$cdateto?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="datesetto" type="text" placeholder="Calendar" readonly>
                                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </div>
                                <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-md-4">Sender</th>
                                        <th class="sorting_asc col-md-3">Date</th>
                                        <th>Remarks</th>
                                        <th>Date Sent</th>
                                    </tr>
                                </thead>
                                <tbody id="contents">
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
</div>    
</div>           
<style>
.ui-dialog-titlebar { display: none }
</style>
<script>
$(document).ready(function(){
   loadContent(); 
});

$("#category").change(function(){
   loadContent();
});

function loadContent(){
    var form_data = {
                        cat: $("#category").val(),
                        dfrom: $("input[name='datesetfrom']").val(),
                        dto: $("input[name='datesetto']").val(),
                        view: "process/displaymessages"
                    };
    $("#contents").html("<tr><td colspan='4'><img src='<?=base_url()?>images/loading.gif'> Loading Please Wait..</img></td></tr>");
    $.ajax({
       url: "<?=site_url("main/siteportion")?>",
       type: "POST",
       data: form_data,
       success: function(msg){
        $("#contents").html(msg);
        //alert(msg);
       }
    });
}

$('.chosen').chosen();     
$("#datesetfrom,#datesetto").datepicker({
    autoclose: true,
    todayBtn : true
});
</script>