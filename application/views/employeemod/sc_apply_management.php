<?php

/**
 * @author Justin
 * @copyright 2016
 */

/**
 * @modified Angelica Arangco  2017
 * orig file: views\employeemod\offbusinessapply.php
 */

$datetoday = "";
$timetoday = "";

$isdisabled = isset($leaveid) ? 'readonly': '';

$othertype  = isset($othertype) ? $othertype    : '';

$base_id    = isset($base_id)   ? $base_id      : '';
$paid       = isset($paid)      ? $paid         : '';
$nodays     = isset($nodays)    ? $nodays       : '';
$isHalfDay  = isset($isHalfDay) ? $isHalfDay    : '';
$dfrom      = isset($dfrom)     ? $dfrom        : '';
$dto        = isset($dto)       ? $dto          : '';
$reason     = isset($reason)    ? $reason       : '';
$destination= isset($destination)    ? $destination       : '';


# newly added for ica-hyperion 21194
# by justin (with e)
$CI =& get_instance();
$CI->load->model('utils');
$empID = $this->session->userdata('username');
$isAdmin = $this->extras->findIfAdmin($empID);
$sel_emp = '';

# kunin yung selected employee
if($isAdmin && $base_id){
    #echo "<pre>". "SELECT employeeid FROM ob_app_emplist WHERE base_id='{$base_id}'";
    $sel_emp = $this->db->query("SELECT employeeid FROM ob_app_emplist WHERE base_id='{$base_id}'")->row()->employeeid;
}
# end of newly added for ica-hyperion 21194
?>
<style>
.th-style{
    background-color: #2e5266;
    color: #ffffff;
    text-align: center;
}
.form_row{
        padding-bottom: 10px;
    }
</style>
<form id="frmsc">

<input type="hidden" name="base_id" value="<?=$base_id?>">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                        <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                        <p>D`Great</p>
                    </div>
                </div>
                <center><b><h3 tag="title" class="modal-title">OB/Excuse Slipr</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
                <!-- for ica-hyperion 21194 -->
                <!-- by justin (with e) -->
                <?if($isAdmin){
                    # kapag admin ang nag applay ng leave request.. lilitaw ito..
                ?>
                <!-- Approve by approver section -->
                <div class="form_row">
                    <label class="field_name align_right">Will be approve by approver?</label>
                    <div class="field no-search">
                        <select class="form-control" name="allowApprover" id="allowApprover">
                            <option value="1">YES</option> <!-- kapag yes, dadaan sa sequence approver -->
                            <option value="0">NO</option> <!-- kapag no, deretso approved na -->
                        </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Employee</label>
                    <div class="field">
                        <select class="chosen col-md-4" id="employee" name="employee" <?=($sel_emp) ? "disabled" : ""?>>
                            <?
                                $emplist = $CI->utils->getEmpListToCbo();

                                $i = 0;
                                # displayed employee list
                                foreach ($emplist as $key => $value) {
                                    if($i > 0){
                            ?>
                                    <option value="<?=$key?>" <?=($sel_emp == $key)? "selected" : "" ?>><?=$key ." - ". $value?></option>
                            <?      } # end of if condition
                                    $i += 1;
                                } # end of foreach 
                            ?>
                        </select>
                    </div>
                </div>
                <?}?>
                
                                    <!-- end for ica-hyperion 21194 -->
                                     <div class="form_row">
                                    <label class="field_name align_right">Day Mode</label>
                                    <div class="field no-search">
                                        <select name='dayMode' id='dayMode' class='chosen'>
                                            <option value='whole'>Whole Day</option>
                                            <option value='half'>Half Day</option>
                                        </select>
                                    </div>
                                </div>
                                 <div class="form_row">
                                    <label class="field_name align_right">Service Credit Date</label>
                                    <div class="field">
                                        <div class="col-md-12" style="padding-left: 0px;" >
                                          <div class="col-md-5" style="padding-left: 0px; margin-right: 13.7%;">
                                          <div class='input-group date' id='datePicker' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 120%;">
                                            <input type='text' class="form-control" size="16" name="date" id="date" type="text" value="<?=isset($dateInitial)?$dateInitial:''?>"/>
                                            <span class="input-group-addon">
                                                  <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                          </div>
                                        </div>
                                      <div class="col-md-5">
                                        <div class='input-group date' id='datePickers' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 120%;">
                                          <input type='text' class="form-control" size="16" name="date1" id="date1" type="text" value="<?=isset($dateInitial)?$dateInitial:''?>"/>
                                          <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                        </div>
                                      </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form_row">
                                    <label class="field_name align_right">Service Credit</label>
                                    <div class="field no-search">
                                        <input type='text' class="form-control" name="sc" id="sc" value="1" readonly/>
                                    </div>
                                </div>
                                
                                <div class="form_row">
                                    <label class="field_name align_right">Reason</label>
                                    <div class="field no-search">
                                        <textarea rows="4" class="form-control" name="reason" id="reason" placeholder="Reason"></textarea>
                                    </div>
                                </div>

            </div>
        </div>
        <div class="modal-footer" >
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-success">&nbsp;&nbsp;Save&nbsp;&nbsp;</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
    $(document).ready(function(){
    $("#datePicker,#datePickers").datetimepicker({
        format: "YYYY-MM-DD"
    });
});
$(".chosen").chosen();

$("#save").unbind("click").bind("click",function(){
    $("#errormsg").html('');
    var form_data   =   $("#frmsc").serialize();
    // console.log(form_data);return;
    if($("input[name='date']").val() == ""){
        $("#errormsg").show().html("Service Credit Date is required!.");
        return false;
    }else if($("#reason").val() == ""){
        $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
        return false;
    }
    else{
        if ($("#dayMode").val() == "whole") {
            if ($("#date").val() == "" || $("#date1").val() == "" ) {
                $("#errormsg").show().html("One of date is empty!.");
                return false; 
            }
            else
            {
                $("#saving").hide();
                $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
                $.ajax({
                   url      :   "<?=site_url("service_credit_/saveSCAppManagement")?>",
                   type     :   "POST",
                   data     :   form_data,
                   success  :   function(msg){
                   return;
                    alert(msg);
                    $(function(){
                    if (typeof loadsc !== 'undefined') {
                          loadsc();
                    }
                    $("#close").click();
                    });
                    if (typeof loadschistory !== 'undefined') {
                      loadschistory("");
                    }
                    location.reload();
                    $('#search').click();
                   }
                });
            }      
        
        }
        else
        {
            $("#saving").hide();
            $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
            $.ajax({
               url      :   "<?=site_url("service_credit_/saveSCAppManagement")?>",
               type     :   "POST",
               data     :   form_data,
               success  :   function(msg){
                console.log(msg);
                alert(msg);
                $(function(){
                if (typeof loadsc !== 'undefined') {
                      loadsc();
                }
                $("#close").click();
                });
                if (typeof loadschistory !== 'undefined') {
                  loadschistory("");
                }
               
               }
            });
        }
    }
});

$("#dayMode").change(function(){
    var dayMode = $(this).val();
    if(dayMode == 'whole')
    {
        $("#sc").val(1);
        $("#datePickers").show();
         $("#date").val('');
        $("#date1").val('');
    }
    else
    {
        $("#sc").val(0.5);
       $("#datePickers").hide();
        $("#date").val('');
        $("#date1").val('');
    }
});
</script>