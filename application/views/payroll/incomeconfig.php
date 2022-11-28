<?php

/**
 * @author Justin
 * @copyright 2015
 */

$desc = $deductedby = $taxable = $mainaccounts = $ismainaccount = $isIncluded = $incomeType = "";
$grosspayNotIncluded = $grosspayNotIncludedPhil = $grosspayNotIncludedPag = false;
if($this->input->post("id")){
    $query = $this->payroll->displayIncome($this->input->post("id"));
    $desc    = $query->row(0)->description;
    $taxable = $query->row(0)->taxable;
    // $grinc   = $query->row(0)->grossinc;
    $incomeType   = $query->row(0)->incomeType;
    $deductedby   = $query->row(0)->deductedby;
    $ismainaccount = $query->row(0)->ismainaccount;
    $mainaccounts = $query->row(0)->mainaccount;
    $isIncluded = $query->row(0)->isIncluded;
    $grosspayNotIncluded = $query->row(0)->grosspayNotIncluded;
    $grosspayNotIncludedPhil = $query->row(0)->grosspayNotIncludedPhil;
    $grosspayNotIncludedPag = $query->row(0)->grosspayNotIncludedPag;
}
?>

<form id="income">
<input name="model" value="newIncome" hidden=""/>
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
        <center><b><h3 tag="title" class="modal-title">Add Income</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="row">
                 <div class="col-md-12">
                    <div class="form-group">
                      <label class="col-sm-3 align_right">Description</label>
                      <div class="col-sm-9 form-inline">
                        <div class="form-group">
                            <input type="text" class="form-control" name="desc" value="<?=$desc?>"/>
                        </div>
                        <div class="form-group" style="display: none;">    
                            &nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="ismainaccount" value="1" class="double-sized-cb" <?=$ismainaccount?'checked':''?>> Is Main Account  
                        </div>       
                      </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                      <label  class="col-sm-3 align_right">Tax</label>
                      <div class="col-sm-9">
                         <select class="form-control" name="taxable">
                        <?=$this->payrolloptions->taxable($taxable)?>
                        </select>
                      </div>
                    </div>
                    <!-- <br><br> -->
                    <div class="form-group" style="display: none;">
                      <label  class="col-sm-3 align_right">Main Account</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="mainaccount">
                            <option value=''>-- Select Main Account --</option>
                        <?
                        $CI =& get_instance();
                        $CI->load->model('income');
                        $income = $CI->income->getIncomeSetupList(array('ismainaccount'=>'1'));
                        foreach ($income->result() as $field) {
                            if ($mainaccounts == $field->id) {?>
                                <option value="<?=$field->id?>" selected><?=$field->description?></option>
                                
                            <?}
                            else
                            {?>
                                <option value="<?=$field->id?>" ><?=$field->description?></option>
                            <?}
                        ?>
                        <?}
                        ?>

                        </select>
                      </div>
                    </div>
                    <!-- <br><br> -->
                    <div class="form-group" style="display: none;">
                      <label  class="col-sm-3 align_right">Type</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="incomeType">
                            <option value="deminimiss" <?=$incomeType == "deminimiss" ? "selected" : ""?> >Deminimiss</option>
                            <option value="other" <?=$incomeType == "other" ? "selected" : ""?> >Others</option>
                        </select>
                      </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                      <label  class="col-sm-3 align_right">Deducted by?</label>
                      <div class="col-sm-9">
                        <select class="form-control" name="deductedby">
                            <option value="">NONE</option>
                            <option value="TARDY" <?=$deductedby == "TARDY" ? "selected" : ""?> >TARDY</option>
                            <option value="ABSENT" <?=$deductedby == "ABSENT" ? "selected" : ""?> >ABSENT</option>
                            <option value="BOTH" <?=$deductedby == "BOTH" ? "selected" : ""?> >BOTH</option>
                        </select>
                      </div>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                <label  class="col-sm-3 align_right" style="margin-top: 20px;">Include?</label>
                  <div class="col-sm-9" style="margin-top: 20px;">
                    <label class="checkbox-inline" style="margin-left: 12px;"><input type="checkbox" name="isIncluded" value="1" class="double-sized-cb" <?=$isIncluded?'checked':''?>>Include in 13month</label>
                    <label class="checkbox-inline"><input type="checkbox" name="grosspayNotIncluded" value="1" class="double-sized-cb" <?=$grosspayNotIncluded?'checked':''?>>Include in SSS Computation</label>
                    <label class="checkbox-inline" style="float:right;margin-right: 5%;"><input type="checkbox" name="grosspayNotIncludedPhil" value="1" class="double-sized-cb" <?=$grosspayNotIncludedPhil?'checked':''?>>Include in Philhealth Computation</label>
                    <label class="checkbox-inline" style="float:right;margin-right: 8.5%;"><input type="checkbox" name="grosspayNotIncludedPag" value="1" class="double-sized-cb" <?=$grosspayNotIncludedPag?'checked':''?>>Include in Pagibig Computation</label>
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
        if (msg == 'Income Already Exists!.') {
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: msg,
              showConfirmButton: true,
              timer: 2000
          })
        }
        else{
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: msg,
              showConfirmButton: true,
              timer: 2000
          })

        loadincomeconfig();
        $("#close").click();
        }
       }
    });
});
</script>