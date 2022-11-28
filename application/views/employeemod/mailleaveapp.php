<?php

/**
 * @author Justin
 * @copyright 2016
 */

$curr_date = date('Y-m-d');

$dept = $this->extras->getemployeecol($this->session->userdata("username"),"deptid");
$cnoti = $this->employeemod->manageleavenotif()->num_rows();
?>
<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background: #3b5998;">
                        <h5>Leave Management</h5>
                    </div>
                    <div class="well-content">                                
                        <div class="form_row no-search">
                            <div class="field">  
                                <select class="select blue" id="category">
                                <?
                                    $opt = $this->extras->showCategory();
                                    foreach($opt as $key=>$val){
                                    if($key == "PENDING")  $sel = " selected";
                                    else                    $sel = "";
                                ?>      
                                        <option value="<?=$key?>" <?=$sel?>><?=$val?></option><?
                                    }
                                ?>
									<option value="CANCELED">CANCELED</option>
                                </select>
                            </div>
                        </div>
                        <div class="form_row">
                            <label class="field_name align_right">Date</label>
                            <div class="field">
                                <div class="input-group date" id="ldfrom" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="ldfrom" type="text" value="<?=$curr_date?>" readonly>
                                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                                <div class="input-group date" id="ldto" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="ldto" type="text" value="<?=$curr_date?>" readonly>
                                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="field">
                                <div id="load" hidden=""></div>
                                <a href="#" class="btn btn-primary" id="searchlbtn">Search</a>
                            </div>
                        </div>  
                        <div id="manageleave"></div> 
                    </div>
                </div>    
            </div>
        </div>
    </div>    
</div>
<script>
$(document).ready(function(){
    $("#leavemngmnt").hide();
    if("<?=$cnoti?>" > 0)   view_leave_status("<?=$cnoti?>");
});
$("#searchlbtn").click(function(){
    view_leave_status();
});

function view_leave_status(cnoti=""){
    var form_data = {
                        folder   : "employeemod", 
                        view     : "mailleaveapp_details",
                        category : $("#category").val(), 
                        dfrom    : $("input[name='ldfrom']").val(), 
                        dto      : $("input[name='ldto']").val(),
                        dept     : "<?=$dept?>",
                        cnoti    : cnoti   
                    }
    $("#manageleave").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("employeemod_/fileconfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        $("#manageleave").html(msg);
       }
    });
}

$("#dfrom,#ldfrom,#ldto").datepicker({
    autoclose: true,
    todayBtn : true
});
$("#category").chosen();
</script> 
