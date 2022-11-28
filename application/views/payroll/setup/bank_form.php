<?php

/**
 * @author Justin
 * @copyright 2015
 */

$account_number = $bank_name = $branch = "";
$code = $this->input->post("code");
$job = $this->input->post("job");
if($code){
    $query            = $this->payroll->displayBankList($code);
    if($query->num_rows() > 0){
      $account_number   = $query->row(0)->account_number;
      $bank_name        = $query->row(0)->bank_name;
      $branch           = $query->row(0)->branch;
    }
}
?>

<form id="income">
<input name="model" value="newBank" hidden=""/>
<input name="code" value="<?=$code?>" hidden="" />
<input name="job" value="<?=$job?>" hidden="" />
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
            <center><b><h3 tag="title" class="modal-title">Add Bank</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
                <div class="form-group">
                    <label  class="col-sm-3 align_right">Code</label>
                    <div class="col-sm-9">
                        <input type="text" id="bank_code" name="code" class="form-control" value="<?=$code?>" <?=$code?'readonly':''?>/>
                    </div>
                </div>
                <br><br><br>
                <div class="form-group">
                    <label  class="col-sm-3 align_right">Account Number</label>
                    <div class="col-sm-9">
                        <input type="text" name="account_number" class="form-control" value="<?=$account_number?>"/>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label  class="col-sm-3 align_right">Bank Name</label>
                    <div class="col-sm-9">
                        <input type="text" name="bank_name" class="form-control" value="<?=$bank_name?>"/>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label  class="col-sm-3 align_right">Branch</label>
                    <div class="col-sm-9">
                        <input type="text" name="branch" class="form-control" value="<?=$branch?>"/>
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

    if ($("#bank_code").val() == '') {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Code is required!',
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }
    if ($("input[name='account_number']").val() == '') {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Account number is required!',
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }
    if ($("input[name='bank_name']").val() == '') {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Bank name is required!',
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }
    if ($("input[name='branch']").val() == '') {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Branch is required!',
            showConfirmButton: true,
            timer: 2000
        })
        return;
    }

    $("#saving").hide();
    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..")
    $.ajax({
       url      :   "<?=site_url("payroll_/loadmodelfunc")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        if (msg == 'Bank Already Exists!.') {
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: msg,
              showConfirmButton: true,
              timer: 2000
          })
        } 
        //Bank has been saved successfully.
        else{
          Swal.fire({
              icon: 'success',
              title: 'Success!',
              text: msg,
              showConfirmButton: true,
              timer: 2000
          }) 
        loadbankconfig();
        $("#close").click();
       }
       }
    });
});
$(".chosen").chosen();
</script>