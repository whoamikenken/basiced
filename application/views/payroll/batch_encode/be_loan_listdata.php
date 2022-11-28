
<style>
    .dataTables_scrollBody{
        overflow: visible !important;
    }
</style>
<?
$loanbase = isset($detail['loanbase'])?$detail['loanbase']:"";   
$CI =&get_instance();
$CI->load->model('utils');
?>

<div class="panel animated fadeIn delay-1s">
    <div class="panel-heading"><h4><b>Employee List</b></h4></div>
    <div class="panel-body emplist">
        <table class="table table-hover table-bordered datatable" id="be_loan">
    <thead style="background-color: #0072c6;padding-right: 10%;">
    <tr>
        <th >Employee</th>
        <th style="width: 18%;">Fullname</th>
        <th>Base on</th>
        <th style="width: 15%;">Deduction Dates</th>
        <th style="width: 11%;">Starting Balance</th>
        <th style="width: 11%;">Current Balance</th>
        <th style="width: 11%;">No. of cutoff</th>
        <th>Amount</th>
        <th>Payroll Cut-off</th>
        <th>Hold</th>
        <th>Action</th>
    </tr>
    </thead>
    <?php if(isset($list)) { ?>
    <tbody id="employeelist">
        <?
        foreach ($list as $employeeid => $detail): ?>
        <?php
        if(isset($detail["amount"])){
             if(!strpos($detail["amount"], ".") !== false && (is_int($detail["amount"]) || is_double($detail["amount"]))){
                $detail["amount"] = number_format($detail["amount"], 2);
             }
         }else{
            $detail["amount"] = "0.00";
         }
         $skip_loan = $CI->loan->checkIfSkipInLoanPayment($employeeid, $loan);
         $loanbase = isset($detail['loanbase'])?$detail['loanbase']:""; ?>


        <tr class="data-list" employeeid="<?=$employeeid?>" status-tag=''>
            <td><?=$employeeid?></td>
            <td><?=$detail['fullname']?></td>
            <td>
            <select class="baseon form-control" name='baseon' class="span11"><?=$CI->utils->basedon($loanbase)?></select>
            </td>
            <td>
                <div style="width: 100%;" class='input-group date' data-date="<?=isset($detail['deductiondate'])?$detail['deductiondate']:""?>" data-date-format="yyyy-mm-dd">
                    <input type='text' class="form-control ddatefrom" id="ddatefrom" name="ddatefrom" value="<?=isset($detail['deductiondate'])?$detail['deductiondate']:""?>" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>        
            </td>
            <td>
                <input style="width: 90%;" type="text" class="form-control startingbalance" name="startingbalance" value="<?=isset($detail['startingbalance'])? number_format((double)$detail['startingbalance'], 2):'0.00'?>">
            </td>
            <td>
                <input style="width: 90%;" type="text" class="form-control currentbalance" name="currentbalance"  value="<?=isset($detail['currentbalance'])? number_format((double)$detail['currentbalance'], 2):'0.00'?>">
            </td>
            <td>
                <input style="width: 90%;" type="text" class="form-control nocutoff" name="nocutoff"  value="<?=isset($detail['nocutoff'])?$detail['nocutoff']:""?>">
            </td>  
            <td>
                <input style="width: 100%;" type="text" class="form-control amount" name="amount"  value="<?=number_format((double)$detail['amount'], 2)?>" disabled>
            </td>
            <td>
                <select class="period form-control period" name="period" id="period" >
                    <?=isset($detail['cutoff_period']) ? $this->payrolloptions->quarter($detail['cutoff_period'],FALSE,$detail['schedule'],TRUE) : $this->payrolloptions->quarter('',FALSE,$detail['schedule'],TRUE)?>

                </select>
            </td>
            <td class="status-tag align_center stats" style="width: 60px;"><input type="checkbox" name="skip_loan" class="double-sized-cb" employeeid="<?= $employeeid ?>" <?= $skip_loan ? "checked" : "" ?> ></td>
            <td class="edit-tag align_center">
                    <a class="btn btn-danger clearInput" id="clearInput">CLEAR</i></a>

                <!-- <?if(isset($detail['can_edit'])):?>
                    <a class="btn btn-danger edit_erase" tag="delete"  employeeid="<?=$employeeid?>" base_id="<?=$detail['id']?>">CLEAR</i></a>
                <?endif;?> -->
            </td>
            <!-- <td style="width: 60px;" class="align_center">
                <?if(isset($detail["id"])):?>
                    <button id="clear" style="width: 100%;" name="clear" class="btn red clear" employeeid="<?=$employeeid?>" value="<?=$detail["id"]?>">CLEAR</button>
                <?endif;?>
            </td>   -->      
        </tr>
        <?php endforeach ?>
    </tbody>
<?php } ?>
</table>
    <!-- <div id="be_modal" class="modal hide fade" data-backdrop="static" data-replace="true" data-keyboard="false" tabindex="-1">
        <div class="modal-header">
            <button id="modalclose" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i></button>
        </div>
        <div class="modal-body">
            <div class="row-fluid">
                <div class="row-fluid span12" tag='display'></div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" type="button" data-dismiss="modal" aria-hidden="true" class="btn grey">Close</a>
            <a href="#" type="button" id="button_save_modal" class="btn btn-danger edit_erase" hidden>Delete</a>
        </div>
    </div> -->
</div>
</div>
<script src="<?=base_url()?>js/batch_encode/be_loan.js"></script>
