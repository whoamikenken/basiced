<?php 

/**
 * @author Angelica
 * @copyright 2018
 *
 * Revised from old report (views\payroll\oldfiles\processedpayrollresult.php)
 * 
 */
 // echo "<pre>";
// print_r($emplist);

foreach ($income_config as $key => $val) {
    if($val['hasData'] == 0 || !isset($val['description'])) unset($income_config[$key]);
}
foreach ($income_adj_config as $key => $val) {
    if($val['hasData'] == 0 || !isset($val['description'])) unset($income_adj_config[$key]);
}
foreach ($incomeoth_config as $key => $val) {
    if($val['hasData'] == 0 || !isset($val['description'])) unset($incomeoth_config[$key]);
}
foreach ($fixeddeduc_config as $key => $val) {
    if($val['hasData'] == 0 || !isset($val['description'])) unset($fixeddeduc_config[$key]);
}
foreach ($deduction_config as $key => $val) {
    if($val['hasData'] == 0 || !isset($val['description'])) unset($deduction_config[$key]);
}
foreach ($loan_config as $key => $val) {
    if($val['hasData'] == 0 || !isset($val['description'])) unset($loan_config[$key]);
}

?>

<script type="text/javascript">
    var $j = jQuery.noConflict();
</script>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/library/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/library/fixedColumns.dataTables.min.css">
<style>
  #dble_paginate{
    margin: 10px 10px 0px 0px;
  }
  th, thead th,th.sorting{
    background-color: #0072c6 !important;
    background: #0072c6;
  }
input[type=search]{
    padding: 6px 12px;
    display: inline;
    width: 78%;
    height: 34px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
 }

 table th {
  text-transform: uppercase;
 }
</style>

<div class="content no-search">


    <br><br>
    <table class="table table-striped table-bordered table-hover" id="dble">
        <thead>
            <?if($dept != ""){?>
                <th colspan="<?=12+sizeof($income_config)+sizeof($income_adj_config) + sizeof($fixeddeduc_config)+sizeof($deduction_config)+sizeof($loan_config)?>" class="align_center"><?=$dept?></td>
            <?}?>
            <tr>
                <th rowspan="2" class="align_center">#</td>
                <th colspan='3' class="align_center">Information</td>
                <th colspan='<?=6+sizeof($income_config)+sizeof($income_adj_config)?>' class="align_center">Earnings</td>
                <th colspan='<?=1+sizeof($fixeddeduc_config)+sizeof($deduction_config)+sizeof($loan_config)?>' class="align_center">Deductions</td>
                <th rowspan="2">Net Pay</td>
                <th rowspan="2" class="align_center noSort">HOLD</th>
                <!-- <th rowspan="2">Edited By</td> -->
                <!-- <?if($hasEditPayrollComputedEditAccess){?>
                    <th rowspan="2"></td>
                <?}?> -->
            </tr>
            <tr>
                <th>Employee ID</th>
                <th>Office</th>
                <th style="width: 20vw;">Name <span style="visibility: hidden;">_______________</span></th>
                <th>Regular Pay</th>
                <th>Tardy</th>
                <th>Absent</th>
                <th>Net Basic Pay</td>

                <?
                    foreach ($income_config as $key => $v) {?>
                        <th><?=$v['description']?></th>
                    <?}
                ?>

                <?
                    foreach ($income_adj_config as $key => $v) {?>
                        <th><?=$v['description'].' ADJ'?></th>
                    <?}
                ?>

                <th>Overtime</th>
                <th>Gross Salary</td>

                <th>WithHolding Tax</th>

                <?
                    foreach ($fixeddeduc_config as $key => $v) {?>
                        <th><?=$key?></th>
                    <?}
                ?>

                <?
                    foreach ($deduction_config as $key => $v) {?>
                        <th><?=$v['description']?></th>
                    <?}
                ?>

                <?
                    foreach ($loan_config as $key => $v) {?>
                        <th><?=$v['description']?></th>
                    <?}
                ?>

                
            </tr>
        </thead>
        <tbody>  
            <?
                $income = $fixed = $deduction = $loans = $empcount = 0;
                foreach ($emplist as $empid => $detail) {
                    #echo '<pre>';print_r($detail);
                    $empcount++;
                    
                    ?>
                    <tr>
                        <td class="align_center"><?=$empcount?></td>
                        <td><?=$empid?></td>
                        <td><?=$this->extensions->getEmployeeOfficeDesc($empid)?></td>
                        <td><?=$detail['fullname']?></td>
                        <td><?=formatAmount($detail['salary'])?></td>
                        <td><?=isset($detail['tardy'])?formatAmount($detail['tardy']):'0.00'?></td>
                        <td><?=isset($detail['absents'])?formatAmount($detail['absents']):'0.00'?></td>
                        <td><?=number_format($detail['netbasicpay'],2)?></td>

                        <?
                            foreach ($income_config as $code => $v) {
                                $income = isset($detail['income'][$code]) ? $detail['income'][$code] : '';
                                ?>
                                <td><?=isset($detail['income'][$code])?formatAmount($detail['income'][$code]):'0.00'?></td>
                            <?}
                        ?>

                        <?
                            foreach ($income_adj_config as $code => $v) {
                                $income_adj = isset($detail['income_adj'][$code]) ? $detail['income_adj'][$code] : '';
                                ?>
                                <td><?=isset($detail['income_adj'][$code])?formatAmount($detail['income_adj'][$code]):'0.00'?></td>
                            <?}
                        ?>

                        <td><?=formatAmount($detail['overtime'])?></td>
                        <td><?=number_format($detail['grosspay'],2)?></td>

                        <td><?=isset($detail['whtax'])?formatAmount($detail['whtax']):'0.00'?></td>

                        <?
                            foreach ($fixeddeduc_config as $code => $v) {
                                $fixed = isset($detail['fixeddeduc'][$code]) ? $detail['fixeddeduc'][$code] : '';
                                ?>
                                <td><?=isset($detail['fixeddeduc'][$code])?formatAmount($detail['fixeddeduc'][$code]):'0.00'?></td>
                            <?}
                        ?>

                        <?
                            foreach ($deduction_config as $code => $v) {
                                $deduction = isset($detail['deduction'][$code]) ? $detail['deduction'][$code] : '';
                                ?>
                                <td><?=isset($detail['deduction'][$code])?formatAmount($detail['deduction'][$code]):'0.00'?></td>
                            <?}
                        ?>

                        <?
                            foreach ($loan_config as $code => $v) {

                                $loans = isset($detail['loan'][$code])? $detail['loan'][$code] : "";
                                ?>
                                <td><?=isset($detail['loan'][$code])?formatAmount($detail['loan'][$code]):'0.00'?></td>
                            <?}
                        ?>

                        
                        <td><?=number_format($detail['netpay'],2)?></td>
                        <td class="align_center">
                            <input type="checkbox" name="hold" class="double-sized-cb" base_id="<?=$detail['base_id']?>" <?=$detail['isHold']?'checked':''?>>
                        </td>
                      <!--   <td><?=$detail['editedby']?></td>
                        <?if($hasEditPayrollComputedEditAccess){?>
                            <td class="align_center"><a class='btn grey edit_data glyphicon glyphicon-edit' id="<?=$this->payrolloptions->dtrdeductdisplay($empid,$schedule,$quarter,$sdate,$edate,'id');?>" data-toggle="modal" data-target="#myModal"></a></td>
                        <?}?> -->

                    </tr>
                <?}

            ?>
        </tbody>
    </table>
</div>

<?

function formatAmount($amount=''){
    if($amount){
        if($amount < 0) {
            $amount = $amount * -1;
            $amount = number_format( $amount, 2 );
            $amount = '(' . $amount . ')';
        }else{
            $amount = number_format( $amount, 2 );
        }
    }else{
        $amount = '0.00';
    }
    return $amount;
}

?>
<script src="<?=base_url()?>jsbstrap/jquery-1.12.4.js"></script>
<script src="<?=base_url()?>jsbstrap/library/jquery.dataTables.min.js"></script>
<script src="<?=base_url()?>jsbstrap/library/dataTables.fixedColumns.min.js"></script>

<script>
 
$j(document).ready(function(){
    var payroll_table;

setTimeout(function(){
          payroll_table = $("#dble").dataTable({
        "sPaginationType": "full_numbers",
        "oLanguage": {
                         "sEmptyTable":     "No Data Available.."
                     },
        "aLengthMenu": [[-1, 10, 20], ["All",10, 20]],
        "aoColumnDefs": [ 
                { "bSortable": false, "aTargets": [ 'noSort' ] }
                ],
        "sDom": "lfrti",
        scrollY:        "400px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         true,
        fixedHeader: true,
        fixedColumns:   {
            leftColumns: 3
        }
    });
    $j(".DTFC_LeftBodyLiner").css({"overflow-y":"hidden","overflow-x":"hidden"});


},0);

  
    ///< for hovering Table Row(tr)
    $("#dble").on("mouseleave mouseover","tr.even, tr.odd",function(e){
        // console.log(e);
        var i = $(this).index();
        var type = e.type=="mouseover";

        $(this).toggleClass("active",type);
        //left Table or fixed columns
        $(".DTFC_Cloned > tbody").find("tr").eq(i).toggleClass("active",type);
        //right Table
        $("#dble > tbody").find("tr").eq(i).toggleClass("active",type);
     });

    $('input[name=hold]').on('click',function(){
        var obj = $(this);
        var base_id = $(this).attr('base_id');
        var isHold = $(this).is(':checked') ? 1 : 0;

        $(this).attr('disabled',true);

        $.ajax({
         url     :   "<?=site_url("payroll_/setPayrollHoldStatus")?>",
         type    :   "POST",
         dataType : 'json',
         data    :   {base_id:base_id, isHold:isHold},
         success :   function(ret){
              if(ret.err_code!=0){
                if(isHold) $(obj).prop('checked',false);
                else $(obj).prop('checked',true);
              }
              $(obj).removeAttr('disabled');
            }
        });
    });


    ///< select employees to include

    // $('#include_payroll_all').on('click',function(){
    //   if($(this).is(':checked')){

    //     var oSettings = payroll_table.fnSettings();
    //     oSettings._iDisplayLength = -1;
    //     payroll_table.fnDraw();

    //     $('select[name=dble_length]').val('-1');
    //     $('input[name=include_payroll]').prop('checked',true); 

    //   }else{     

    //     $('input[name=include_payroll]').prop('checked',false);

    //     var oSettings = payroll_table.fnSettings();
    //     oSettings._iDisplayLength = 10;
    //     payroll_table.fnDraw();

    //     $('select[name=dble_length]').val('10');

    //   }


    // });

    // $('input[name=include_payroll]').on('click',function(){
    //   if(!$(this).is(':checked'))     $('#include_payroll_all').prop('checked',false);
    // });

         
});
</script>