<?php

/**
 * @author Justin
 * @copyright 2015
 */


$ot     = $this->input->post("ot");
$eid    = $this->input->post("eid");
$otdate = $this->input->post("otdate");
?>

<form id="frmot">
<input name="model" value="acceptOT" hidden=""/>
<input name="eid" value="<?=$eid?>" hidden="" />
<input name="otdate" value="<?=$otdate?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Accept Over Time</h4>
        </div>
        <div class="modal-body">
            <div class="content">
                <div class="form_row">
                    <label class="field_name align_right">Overtime</label>
                    <div class="field">
                        <input type="text" name="ot" value="<?=$ot?>"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="save" class="btn btn-danger">Save</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
$("#save").click(function(){    
    var form_data   =   $("#frmot").serialize();
    $("#saving").hide();
    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..")
    $.ajax({
       url      :   "<?=site_url("payroll_/loadmodelfunc")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        alert(msg);
        $("#close").click();
        $("#displayot").html("<img src='<?=base_url()?>images/loading.gif'>Loading, please wait...");
        $.ajax({
            url: "<?=site_url("process_/showindividualot")?>",
            type: "POST",
            data: {
               dset     :   $("input[name='dset']").val(),
               dsetto   :   $("input[name='dsetto']").val(),
               //deptid: $("select[name='deptid']").val(),
               fv       :   $("select[name='employeeid']").val()
            },
            success: function(msg) {
               $("#displayot").html(msg);
            }
        });          
       }
    });    
});
</script>