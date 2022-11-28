<?php

/**
 * @author Justin
 * @copyright 2016
 */

$desc = "";
if($this->input->post("id")){
    $query = $this->payroll->displayIncomeOth($this->input->post("id"));
    if ($query->num_rows()>0) {
        $desc    = $query->row(0)->description;
        $taxable = $query->row(0)->taxable;
        $gross   = $query->row(0)->grossinc;
    }
   
}
?>

<form id="income">
<input name="model" value="newIncomeOth" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                    <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                     <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title">Add Other Income</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
                <div class="form-group">
                    <label  class="col-sm-3 align_right">Description</label>
                    <div class="col-sm-9">
                        <input type="text" name="desc" class="form-control" value="<?=$desc?>"/>
                    </div>
                </div>
                <br><br><br>
                <div class="form-group">
                    <label  class="col-sm-3 align_right">Tax</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="taxable">
                        <?=$this->payrolloptions->taxable($taxable)?>
                        </select>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label  class="col-sm-3 align_right">Deminimiss?</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="grossinc">
                        <?=$this->payrolloptions->yesno($gross)?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
$("#save").click(function(){
    var form_data   =   $("#income").serialize();
    $("#saving").hide();
    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..")
    $.ajax({
       url      :   "<?=site_url("payroll_/loadmodelfunc")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        alert(msg);
        loadincomeothconfig();
        $("#close").click();
       }
    });
});
$(".chosen").chosen();
</script>