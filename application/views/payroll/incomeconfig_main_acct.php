<?php

/**
 * @author Angelica
 * @copyright 2018
 */

?>

<form id="income">
<input name="model" value="newIncome" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Manage Main Account</h4>
        </div>
        <div class="modal-body">
            <div class="content">
                <table class="table">
                    <? foreach ($income_list as $id => $det) { ?>
                            <tr>
                                <td><b><?=$det['description']?></b></td>
                                <td width="10px">
                                    <div class="btn-group">
                                        <a class="btn" href="#" tag="delete_main_acct" income_code="<?=$id?>" income_desc="<?=$det['description']?>"><i class="glyphicon glyphicon-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                    <? } ?>
                </table>
            </div>
                
        </div>

        <div class="modal-footer">
            <div id="saving">
                <span id='errorMsg' class="error-msg"></span>
                <button type="button" id="save" class="btn btn-danger">Save</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
            <div id="loading" hidden=""></div>
        </div>
    </div>
</div>
</form>
<script>

    var income_list_to_delete = {};

    $('a[tag=delete_main_acct]').on('click',function(){
        $("#errorMsg").show().html("<img src='<?=base_url()?>images/loading.gif' />  Checking, Please Wait..")
        var tr_ = $(this).closest('tr');
        var id = $(this).attr('income_code');
        var desc = $(this).attr('income_desc');

        $.ajax({
           url      :   "<?=site_url("payroll_/checkTaggedToMainAcct")?>",
           type     :   "POST",
           data     :   {id : id},
           success  :   function(msg){
                            if(msg=='0'){
                                income_list_to_delete[id] = desc;
                                console.log(income_list_to_delete);
                                $("#errorMsg").html('');
                                $(tr_).remove();
                            }else{
                                $("#saving").show();
                                $("#loading").hide()
                                $('#errorMsg').html('Unable to delete. Account has tagged income.').show().delay(3000).fadeOut();
                                $(tr_).css('background-color','#FFEBEE');
                            }
                   }
        });
    });


    $("#save").click(function(){
        if(Object.keys(income_list_to_delete).length == 0){
            $('#errorMsg').html('No account to delete.').show().delay(3000).fadeOut();
            return;
        }

        $("#saving").hide();
        $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..")
        $.ajax({
           url      :   "<?=site_url("payroll_/deleteIncomeMainAcctSetup")?>",
           type     :   "POST",
           dataType :   'json',
           data     :   {income_list_to_delete : income_list_to_delete},
           success  :   function(msg){
                            if(msg.err_code == 0){
                                if(msg.failed_list_str == ''){
                                    alert(msg.msg);
                                }else{
                                    alert(msg.msg + '\n' + 'Failed List: ' + msg.failed_list_str);
                                }
                            }else{
                                alert(msg.msg);
                            }
                            $("#close").click();
           }
        });
    });
    $(".chosen").chosen();
</script>