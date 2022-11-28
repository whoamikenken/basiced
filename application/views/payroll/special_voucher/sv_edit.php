  <div class="form_row">
    <label class="field_name align_right" style="margin-right: 5%;width: 24%;" >Category</label>
    <div class="field">
      <input style="width: 73%;" class="form-control" id="edit_type" name="edit_type" type="text" value="<?= isset($type) ? $type : ""?>" readonly/>
    </div>
  </div><br>
  <div class="form_row">
    <label class="field_name align_right" style="margin-right: 5%;width: 24%;" >Account</label>
    <div class="field">
      <select style="width: 73%;" class="form-control" name="edit_account" id="edit_account">

      </select>
    </div>
  </div><br>
  <div class="form_row">
    <label class="field_name align_right" style="margin-right: 5%;width: 24%;" >Cut-Off</label>
    <div class="field">
      <select style="width: 73%;" class="form-control" name="edit_cutoff" id="edit_cutoff">
          <?= $this->extras->getPayrollCutoffSelect($cutoff); ?>
      </select>
    </div>
  </div><br>
  <div class="form_row">
    <label class="field_name align_right" style="margin-right: 5%;width: 24%;" >Amount</label>
    <div class="field">
      <input style="width: 73%;" class="form-control" id="edit_amount" name="edit_amount" type="text" value="<?= isset($amount) ? $amount : ""?>" />
    </div>
  </div><br>
  <div class="form_row">
    <label class="field_name align_right" style="margin-right: 5%;width: 24%;" >Remarks</label>
    <div class="field">
      <input style="width: 73%;" class="form-control" id="edit_remarks" name="edit_remarks" type="text" value="<?= isset($remarks) ? $remarks : ""?>" />
    </div>
  </div><br>
  <input style="width: 73%; display: none;" class="form-control"id="edit_employeeid" name="edit_employeeid" type="text" value="<?= isset($employeeid) ? $employeeid : ""?>" />
  <script>
$(document).ready(function(){
  var category = $("#edit_type").val();
  $.ajax({
                url : "<?= site_url('extensions_/getAccountSetup') ?>",
                type : "POST",
                data : {category:category},
                success:function(response){
                    $("#edit_account").html(response).trigger('chosen:updated')
                }
            });
});

    $("#button_save_modal").unbind().click(function(){
        data = {
          "edit_employeeid" : $("#edit_employeeid").val(),
          "edit_remarks" : $("#edit_remarks").val(),
          "edit_cutoff" : $("#edit_cutoff").val(),
          "edit_account" : $("#edit_account").val(),
          "edit_amount" : $("#edit_amount").val(),
          "edit_type" : $("#edit_type").val()
        };

        $.ajax({
          url : "<?= site_url('extensions_/validateUpdateVoucher')?>",
          type : "POST",
          data : data,
          success:function(response){
            alert(response);
            $("#modalclose").click();
            getEncodedHistory();
          }
        });
    });
  </script>