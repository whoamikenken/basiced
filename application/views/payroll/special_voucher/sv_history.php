<?php
/**
 * @author Max Consul
 * @copyright 2019
 *
 */

$account_name = $code = '';
if($type == "income" || $type == "deduction" || $type = "loan") $code = 'id';
else if($type == "regdeduction") $code = 'code_deduction';
else if($type == "witholdingtax") $account_name = "Witholdingtax";

?>
            <table class="table table-striped table-bordered table-hover" id="history_tbl">
                <thead style="background-color: #0072c6;">
                    <tr>
                        <th>Employee Name</th>
                        <th>Account</th>
                        <th>Cut-Off</th>
                        <th>Amount</th>
                        <th>Date Encoded</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <?php if(isset($records)) { ?>
                    <?php foreach($records as $key => $value): ?>    
                        <?php 
                            if($value['type'] == "income"){
                                foreach($income as $key => $setup_val){
                                    if($setup_val[$code] == $value['account']) $account_name = $setup_val['description'];
                                }
                            }
                            else if($value['type'] == "deduction"){
                                foreach($deduction as $key => $setup_val){
                                    if($setup_val[$code] == $value['account']) $account_name = $setup_val['description'];
                                }
                            }
                            else if($value['type'] == "loan"){
                                foreach($loan as $key => $setup_val){
                                    if($setup_val[$code] == $value['account']) $account_name = $setup_val['description'];
                                }
                            }
                            else if($value['type'] == "regdeduction"){
                                foreach($regdeduction as $key => $setup_val){
                                    if($key == $value['account']) $account_name = $setup_val;
                                }
                            }else{
                                $account_name = "";
                            }
                        ?>
                        <tr>
                            <td><?= $value['employeeid']." - ".$value['fullname']?></td>
                            <td class="align_center"><?= $account_name?></td>
                            <td class="align_center"><?= $this->extras->getPayrollCutoffDescription($value['cutoff']) ?></td>
                            <td class="align_center"><?= $value['amount']?></td>
                            <td class="align_center"><?= $value['date_encoded']?></td>
                            <td class="align_center"><?= $value['remarks']?></td>
                            <td class="align_center">
                                <a id="<?=$value['employeeid'] ?>" account = "<?= $value['account'] ?>" cutoff="<?= $value['cutoff'] ?>" remarks="<?= $value['remarks']?>" category ="<?= $value['type']?>" class="btn btn-info editbtn"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a id="<?=$value['employeeid'] ?>" category ="<?= $value['type']?>" account = "<?= $value['account'] ?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php } ?>
            </table>
        </div>
    </div>

<script>
    
    $(document).ready(function(){
        var table = $('#history_tbl').DataTable({
        });
        new $.fn.dataTable.FixedHeader( table );
    });

     $("#history_tbl").on('click', '.editbtn', function(){
        var employeeid = $(this).attr('id');
        var account = $(this).attr('account');
        var category = $(this).attr('category');
        var cutoff = $(this).attr('cutoff');
        var remarks = $(this).attr('remarks');
        $.ajax({
            url : "<?= site_url('extensions_/editEncodedVoucher') ?>",
            type : "POST",
            data : {employeeid:employeeid, category:category, account:account, cutoff:cutoff, remarks:remarks},
            success:function(response){
                $("#modal-view").find(".modal-title").text('Edit Voucher Data');
                $("#modal-view").find("div[tag='display']").html(response);
                $("#modal-view").find("#button_save_modal").html("Save");
                $("#modal-view").find("#modalclose").removeClass("btn-danger").addClass("btn-danger");
                $("#modal-view").find("#button_save_modal").removeClass("btn-danger").addClass("btn-success");
                $("#modal-view").modal('toggle');
            }
        });
    });

     $("#history_tbl").on('click', '.delbtn', function(){
        var employeeid = $(this).attr('id');
        var account = $(this).attr('account');
        var category = $(this).attr('category');
        $.ajax({
            url : "<?= site_url('extensions_/deleteEncodedVoucher') ?>",
            type : "POST",
            data : {employeeid:employeeid,category:category,account:account},
            success:function(response){
                $("#modal-view").find(".modal-title").text('Delete Voucher Data');
                $("#modal-view").find("div[tag='display']").html(response);
                $("#modal-view").find("#button_save_modal").addClass("delete");
                $("#modal-view").find("#button_save_modal").html("Close");
                $("#modal-view").find("#modalclose").html("Delete");
                $("#modal-view").find("#button_save_modal").removeClass("btn-danger").addClass("btn-defualt");
                $("#modal-view").find("#modalclose").removeClass("btn-success").addClass("btn-danger");
                $("#modal-view").modal('toggle');
            }
        });
    });


    $("#sort_history").on("change", function(){
        getEncodedHistory($(this).val());
    });

    $(".chzn-select").chosen();
    $(".chosen").chosen();

</script>