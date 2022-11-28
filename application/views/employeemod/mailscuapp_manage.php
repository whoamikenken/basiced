<?php
 $isreadonly = "readonly='true'";
 $isdisabled = "disabled";

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
                    <td><strong>REQUEST FOR USING SERVICE CREDIT AUTHORITY</strong></td>
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
                <label class="field_name align_right">Date when Service Credit will be use</label>
                <div class="field">
                    <div class="input-group date" id="date" data-date="<?=$date?>" data-date-format="yyyy-mm-dd">
						<input class="align_center" size="16" name="date" type="text" value="<?=$date?>" disabled>
                    </div>
                </div>
            </div>
			<!--
			<div class="form_row">
                <label class="field_name align_right">Day Mode</label>
                <div class="field no-search">
                    <select name='dayMode' id='dayMode' class='chosen' disabled>
						<option value='whole' <?=$dayMode == "whole"?"selected":""?>>Whole Day</option>
						<option value='half' <?=$dayMode == "half"?"selected":""?>>Half Day</option>
					</select>
                </div>
            </div>
			-->
			<div class="form_row">
                <label class="field_name align_right">Service Credit Used</label>
				<div class="field no-search">
					<input type='text' name="nsc" id="nsc" value="<?=$needed_service_credit?>" readonly/>
				</div>
            </div>
			
			<div class="form_row">
                <label class="field_name align_right">Service Credit Date</label>
				<div class="field no-search">
					<table width='70%'>
						<?
							$arrayDate	= array();
							$arraySC 	= array();
						
							if(strpos($service_credit_date_use, ' ') !== false && strpos($service_credit_use, ' ') !== false)
							{
								$arrayDate 	= $service_credit_date_use;
								$arraySC 	= $service_credit_use;
							}
							else
							{
								$arrayDate = explode("/",$service_credit_date_use);
								$arraySC = explode("/",$service_credit_use);
							}

							foreach(array_combine($arrayDate,$arraySC) as $k=>$v)
							{
								if($k && $v)
								{
								?>
								<tr>
									<td>
										<input type="text" class="" value="<?=$k?>" readonly>
									</td>
									<td>
										<label class="field_name align_right">Days</label>
										<div class="field no-search">
											<input type='text' value="<?=$v?>" readonly/>
										</div>
									</td>
								</tr>
								<?
								}
							}
						?>
					</table>
                </div>
            </div>
			
			<div class="form_row">
                <label class="field_name align_right">Remark</label>
                <div class="field no-search">
                    <textarea rows="4" style="width: 100%;resize: none;" readonly><?=$remark?></textarea>
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
                                if($this->extensions->checkIfSecondApprover($idkey, "useservicecredit")) $val = "APPROVED";
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
            <button type="button" id="saveSCU" class="btn btn-danger">Save</button>
        <?}?>
        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</div>
<script>
$("#saveSCU").unbind("click").bind("click",function(){ 
// alert($("input[name='date']").val());
    var newstat = $("#mh_status").val();
    var oldstat = "<?=$colstat?>";
    // alert(newstat);
    // alert(oldstat);
    if(newstat == oldstat){
        alert('No changes were made.');
        $("#close").click();
        return false;
    }
    var form_data = { 
            colhead         : "<?=$colhead?>",
            isLastApprover  : "<?=$isLastApprover?>",
            code_request    : "<?=$code_request?>",
            scid            : "<?=$scid?>",
            status          : newstat,
            empid           : "<?=$employeeid?>",
            scdate          :"<?=$service_credit_date_use?>",
            scused          :"<?=$service_credit_use?>",
            dated           : $("input[name='date']").val()}
    // console.log(form_data);return;
    $.ajax({
        url:"<?=site_url("service_credit_/saveSCUStatusChange")?>",
        type:"POST",
        dataType : 'JSON',
        data:form_data,
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