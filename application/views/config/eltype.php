<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
?>
<style>
.input { font-size:16px; border-color:#cccccc; border-style:solid; padding:9px; border-width:3px; border-radius:12px; text-align: center; font-weight: bolder; } 
.input:focus { outline:none; } 
</style>
<div id="content" class="well"> <!-- Content start -->
<div class="widgets_area">
    <div class="row">
        <div class="col-md-12">
            <div class="well blue">
                <div class="well-header">
                    <h5>Employee Leave Type</h5>
                </div>
                <div class="well-content">
                <form id="frmltype">
                <div class="form_row">
                    <label class="field_name align_right">Department</label>
                    <div class="field">
                        <select class="chosen col-md-4" name="deptid">
                            <option value="">All Department</option>
                            <?
                            $opt_department = $this->extras->showdepartment();
                            foreach($opt_department as $c=>$val){
                            ?><option value="<?=$c?>"><?=$val?></option><?
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Employee</label>
                    <div class="field">
                        <div class="col-md-12">
                            <select class="chosen col-md-4" name="employeeid">
                                <option value="">All Employee</option>
                            <?
                              $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",true);
                              foreach($opt_type as $val){
                              ?><option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                              }
                            ?>
                            </select>
                        </div>
                    </div>
                </div>  
                <div class="form_row">
                    <label class="field_name align_right">Employee ID  (First Two Digits)</label>
                    <div class="field">
                        <div class="col-md-12">
                            <select class="chosen col-md-4" name="eidtwo">
                                <?=$this->extras->ftwodigits();?>
                            </select>
                        </div>
                    </div>
                </div>  
                <div class="form_row no-search">
                    <label class="field_name align_right">Leave Type</label>
                    <div class="field">
                        <div class="col-md-12">
                            <select class="chosen col-md-4" name="ltype">
                                <?=$this->extras->leavetype();?>
                            </select>
                        </div>
                    </div>
                </div> 
                <div class="form_row">
                    <div class="field">
                        <div id="load" hidden=""></div>
                        <a href="#" class="btn btn-primary" id="savebtn">Save</a>
                    </div>
                </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>    
</div>
<script>

$("#savebtn").click(function(){
var form_data = $("#frmltype").serialize();
$("#load").show().html("<td colspan='5' style='text-align: center'>Saving, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");
$("#savebtn").hide();
    $.ajax({
       url      :   "<?=site_url("configuration_/saveltype")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        alert(msg);
        $("#load").hide();
        $("#savebtn").show();
       }
    });
});
$(".chosen").chosen();
</script> 