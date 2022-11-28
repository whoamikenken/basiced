<?php
/**
 * @author Angelica Arangco
 * @copyright 2018
 */
?>

<div class="form_row">
    <label class="field_name align_right">Cutoff</label>
    <div class="field">
        <div class="span12 no-search">
            <select class="chosen span6" name="cutoff" id="cutoff">
                <?=$cutoff_list?>
            </select>
            <span id="cutoff_msg" class="error-msg"></span>
        </div>
    </div>
</div>  

<div class="form_row">
    <label class="field_name align_right">Reglamentory</label>
    <div class="field">
        <div class="span12 no-search">
            <select class="chosen span6" name="reglamentory" id="reglamentory">
            <?
              $type = array("SSS"=>"SSS","PHILHEALTH"=>"PHILHEALTH","PAGIBIG"=>"PAGIBIG");
              foreach($type as $c=>$val){
              ?><option value="<?=$c?>"><?=$val?></option><?
              }
            ?>
            </select>
        </div>
    </div>
</div>  

<div class="form_row">
    <div class="field">
        <span> <input type="button" id="be_payment_reglamentory_btn" class="btn blue" value="ENCODE"> </span>
    </div>
</div>  

<script>
    $('#be_payment_reglamentory_btn').on('click',function(){

        if($('#cutoff').val() == ''){
            $('#cutoff_msg').html("Please select cutoff.");
            return;
        }
        
        $('#wrapListEncode').html("<img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.");
        $.ajax({
            url : "<?=site_url('payroll_/loadPayrollBatchEncode')?>",
            type : "POST",
            data : {
                category : $('#category').val(),
                deptid : $("select[name=deptid]").val(),
                employmentstat : $("select[name=employmentstat]").val(),
                cutoff : $("select[name=cutoff]").val(),
                reglamentory : $("select[name=reglamentory]").val()
            },
            success : function(msg){
                $('#cutoff_msg').html('');
                $('#wrapListEncode').html(msg);
            }
        });
    });

    $('.chosen').chosen();
</script>