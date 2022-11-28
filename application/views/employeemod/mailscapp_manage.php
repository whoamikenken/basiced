<?php
 $isreadonly = "readonly='true'";
 $isdisabled = "disabled";
$dateexplode = "";
 $datetoday = date("d-m-Y");
$disabled = "";
$hide = "";
if (!$isLastApprover) {
    $disabled = "disabled";
    $hide = "style='display:none'";
}

?>
<style>
.modal{
    width: 50%;
    left: 0;
    right: 0;
    margin: auto;
}
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="70px"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                </tr>
                <tr>
                    <td><strong>REQUEST FOR SERVICE CREDIT AUTHORITY</strong></td>
                </tr>
            </table>
        </div>
        <div class="modal-body">
            <form id="form_ot">
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Name</label>
                <div class="field">
                    <span style="line-height: 5px;"><b><?=$fullname?></b></span>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Department</label>
                <div class="field">
                    <span style="line-height: 5px;"><b><?=$edept?></b></span>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Position</label>
                <div class="field">
                    <span style="line-height: 5px;"><b><?=$pos?></b></span>
                </div>
            </div>

    		<div class="form_row">
                <label class="field_name align_right">Day Mode</label>
                <div class="field no-search">
                    <select name='dayMode' id='dayMode' class='chosen' <?=$disabled?>>
    					<option value='whole' <?=$dayMode == "whole"?"selected":""?>>Whole Day</option>
    					<option value='half' <?=$dayMode == "half"?"selected":""?>>Half Day</option>
    				</select>
                </div>
            </div>
            <?
                foreach ($sc_date_list as $sc_id => $sc_info) {
            ?>
            <div class="form_row">
                <label class="field_name align_right">Service Credit Date</label>
                <div class="field">
                     <?php if (str_word_count($date) == 2): ?>

                    <div class="input-group date" id="date" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="32" name="date" type="text" value="<?=$sc_info['date']?>" <?=$disabled?>>
                        <span class="add-on" <?=$hide?>><i class="glyphicon glyphicon-calendar"></i></span>
                    </div>
                    <?php else: ?>
                    <?php $dateexplode = explode('/',$date)?>
                    <div class="input-group date" id='datePickers' data-date="<?=$dateexplode[0]?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="date" id="date" type="text" value="<?=$dateexplode[0]?>" readonly="">
                        <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                    </div>
                    <div class="input-group date" id='datePickers' data-date="<?=$dateexplode[1]?>" data-date-format="yyyy-mm-dd">
                        
                        <input class="align_center" size="16" name="date1" id="date1" type="text" value="<?=$dateexplode[1]?>" readonly="">
                        <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                    </div>
                    <?php endif ?>
                    <input type='text' name="sc" id="sc" value="<?=$sc_info['service_credit']?>" readonly/>
                </div>
            </div>
            <!-- <div class="form_row">
                <label class="field_name align_right">Service Credit</label>
                <div class="field no-search">
                    <input type='text' name="sc" id="sc" value="<?=$sc_info['service_credit']?>" readonly/>
                </div>
            </div> -->
            <?
                }
            ?>
            
            <div class="form_row">
                <label class="field_name align_right">Reason</label>
                <div class="field no-search">
                    <textarea rows="4" style="width: 100%;resize: none;" name="reason" id="reason" placeholder="Reason" <?=$disabled?>><?=$reason?></textarea>
                </div>
            </div>
			
			 <div class="form_row">
                <label class="field_name align_right">Status</label>
                <div class="field no-search">
                    <select class="form-control" name="mh_status" id="mh_status" <?= (in_array($colstat,array("APPROVED","DISAPPROVED")) ? $isdisabled : "")?>>
                        <?
                            $opt_status = $this->extras->showLeaveStatus();
                            foreach($opt_status as $c=>$val){
                            if($val == "APPROVED"){
                                if($this->extensions->checkIfSecondApprover($idkey, "servicecredit")) $val = "APPROVED";
                                else $val = "ENDORSE";
                            }
                        ?><option<?=($c==$colstat ? " selected" : "")?> value="<?=$c?>" ><?=($val=="PENDING"?"Select status..":$val)?></option><?    
                        }
                        ?>
                    </select>
                </div>
            </div>
			
            </form>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div id="loading" hidden=""></div>
    <div id="saving">
        <?if($job == "edit"){?>
            <button type="button" id="saveSC" class="btn btn-danger">Save</button>
        <?}?>
        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</div>
<script>
$(document).ready(function(){
    // $(".date,#datePickers").datepicker({
    //     autoclose: true,
    //     todayBtn : true
    // });
});
$("#saveSC").unbind("click").bind("click",function(){  
    var newstat = $("#mh_status").val();
    var oldstat = "<?=$colstat?>";
     // var scid = "<?=$approval_id?>";

     // alert(scid);
    // alert("<?=$scid?>");return;
    if(newstat == oldstat){
        alert('No changes were made.');
        $("#close").click();
        return false;
    }

    $.ajax({
        url:"<?=site_url("service_credit_/saveSCStatusChange")?>",
        type:"POST",
        dataType : 'JSON',
        data:{
            colhead         : "<?=$colhead?>",
            isLastApprover  : "<?=$isLastApprover?>",
            scid            : "<?=$scid?>",
            approval_id     : "<?=$approval_id?>",
            code_request    : "<?=$code_request?>",
            // date            : $("#date").val(),
            // date1            : $("#date1").val(),
            status          : newstat
        },
        success: function(msg){

            $("#close").click();
            alert(msg.msg);
            if(msg.err_code == 0){
                $(".inner_navigation .main li .active a").click();
            }
        }
    });
});

$('.chosen').chosen();
</script>