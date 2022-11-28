<?php

/**
 * @author Justin
 * @copyright 2016
 */
$employeeid = $this->input->post("employeeid");
$income = $this->input->post("income");
$incomebase = $this->input->post("incomebase");
$amount = $this->input->post("amount");
$pos = $this->input->post("pos");

if($this->input->post("job")=="delete"){
  $this->db->query("delete from employee_income_oth where employeeid='{$employeeid}' and code_income='{$income}'");
}

?>
<form id="form_addincome">
<div class="form_row">
    <label class="field_name">Income</label>
    <div class="field">
        <div class="col-md-4 no-search">
            <select id="income_drop" name="income_drop" class="form-control" name="tax_status">
            <?=$this->payrolloptions->incomeoth($income);?>
            </select>
        </div>
    </div>
</div>
<div class="form_row">
    <label class="field_name">Amount</label>
    <div class="field">
         <input class="align_right col-md-4 required" id="amountincome" name="amountincome" type="text" value="<?=$amount?>"/>
    </div>
</div>
<div class="form_row">
    <label class="field_name">Position</label>
    <div class="field no-search">
         <select class="form-control" name="pos" id="pos">
            <option value="lower" <?= ($pos=="lower") ? " selected" : ""?>>Lower</option>
            <option value="upper" <?= ($pos=="upper") ? " selected" : ""?> >Upper</option>
         </select>
    </div>
</div>
</form>
<script>
$('.chosen').chosen();
$("#button_save_modal").unbind("click").click(function(){    
    var $validator = $("#form_addincome").validate({
        rules: {
            amountincome: {
              required: true
            }
        }
    });
    
    if($("#form_addincome").valid()){
           var form_data = $("#form_addincome").serialize();
               form_data += "&job=employee/income_info_oth";
           $.ajax({
              url: "<?=site_url("employee_/validateinfo")?>",
              data : form_data,
              type : "POST",
              success:function(msg){
                //alert(msg);
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Successfully saved data!',
                    showConfirmButton: true,
                    timer: 1000
                })
                $("#modalclose").click();
                refreshtab("#tab12");
                 loadotherincomeinfo();
              }
           });
           
   }else {
       $validator.focusInvalid();
       return false;
   }
});
$("#schedule").change(function(){
    $("#qshow").hide();
    $("#qload").show().html('<div class="form_row"><label class="field_name"></label><div class="field"><div class="col-md-5"><img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</div></div></div>');
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :   $(this).val(), 
          model     :   "quarter"
        },
        success: function(msg){
           $("#qload").hide();
           $("select[name='period_drop']").html(msg).trigger("liszt:updated");
           $("#qshow").show();
        }
    });
});
</script>