<?php

/**
 * @author Justin
 * @copyright 2015
 */

$desc = "";
if($this->input->post("id")){
    $query = $this->payroll->displayDeduction($this->input->post("id"));
    $desc  = $query->row(0)->description;
    $loan_acc = $query->row(0)->loanaccount;
    $arith = $query->row(0)->arithmetic;
    $tax   = $query->row(0)->taxable;
    $gross = $query->row(0)->grossinc;
    /**remove L in loan account**/
    $loan_acc = substr($loan_acc, 1);
}
?>

<form id="deduction">
<input name="model" value="newDeduction" hidden=""/>
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
        <center><b><h3 tag="title" class="modal-title">Add Deduction</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                      <label for="employeeid" class="col-sm-3 align_right">Description</label>
                      <div class="col-sm-9">
                        <input type="text" name="desc" class="form-control" value="<?=$desc?>"/>
                      </div>
                    </div>
                    <!-- <br><br> -->
                    <div class="form-group" style="display: none;">
                      <label  for="employeeid" class="col-sm-3 align_right">Loan Account</label>
                      <div class="col-sm-9">
                         <select class="form-control" name="loan_acc"><?=$this->payrolloptions->loan($loan_acc);?></select>
                      </div>
                    </div>
                    <!-- <br><br> -->
                    <div class="form-group" style="display: none;">
                      <label  for="employeeid" class="col-sm-3 align_right">Compute As</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="arithmetic" id="arithmetic"><?=$this->payrolloptions->arithmetic($arith);?></select>
                      </div>
                    </div>
                    <!-- <br><br> -->
                    <div class="form-group" style="display: none;">
                      <label  for="employeeid" class="col-sm-3 align_right">Tax</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="taxable">
                        <?=$this->payrolloptions->taxable($taxable)?>
                        </select>
                      </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                      <label  for="employeeid" class="col-sm-3 align_right">Deminimiss?</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="grossinc">
                        <?=$this->payrolloptions->yesno($gross)?>
                        </select>
                      </div>
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
    var form_data   =   $("#deduction").serialize();

    if ($("input[name='desc']").val() == '') {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Description is required!',
            showConfirmButton: true,
            timer: 2000
        });
        return;
    }

    $("#saving").hide();
    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..")
    $.ajax({
       url      :   "<?=site_url("payroll_/loadmodelfunc")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: msg,
            showConfirmButton: true,
            timer: 2000
        });
        loaddeducconfig();
        $("#close").click();
       }
    });
});
$(".chosen").chosen();
</script>