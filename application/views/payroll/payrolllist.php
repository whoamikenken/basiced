<?php 

/**
 * @author Angelica
 * @copyright 2017
 *
 * Process based from old report (views\payroll\oldfiles\payrolllist.php)
 * This displays payroll summary . Summary can then be Finalized to payroll_computed_table.
 * 
 */
 // echo "<pre>";
// print_r($emplist);

$canFinalize = count($emplist);
$explodecutoff = explode('+', $cutoff);
$sdate = $explodecutoff[0];
$edate = $explodecutoff[1];


foreach ($income_adj_config as $key => $val) {
    if($val['hasData'] == 0) unset($income_adj_config[$key]);
}
foreach ($incomeoth_config as $key => $val) {
    if($val['hasData'] == 0) unset($incomeoth_config[$key]);
}
foreach ($income_config as $key => $val) {
    if($val['hasData'] == 0) unset($income_config[$key]);
    if($key == 40 || $key == 39) unset($income_config[$key]);
}
foreach ($fixeddeduc_config as $key => $val) {
    if($val['hasData'] == 0) unset($fixeddeduc_config[$key]);
}
foreach ($deduction_config as $key => $val) {
    if($val['hasData'] == 0) unset($deduction_config[$key]);
}
foreach ($loan_config as $key => $val) {
    if($val['hasData'] == 0) unset($loan_config[$key]);
}
?>
<style>
  #dble_paginate{
    margin: 10px 10px 0px 0px;
  }
  th, thead th,th.sorting{
    background-color: #0072c6 !important;
    background: #fff;
  }

   th, td { white-space: nowrap; }
    div.dataTables_wrapper {
        margin: 0 auto;
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
    <?if(isset($recompute_msg)){?>
        <span style="color: green;font-weight: bold;font-size: 15px;"><?=$recompute_msg?></span>

    <?}?>

    <br>
    <div style="margin:0 0 3px 20px">
        <? if($status=='PENDING'){ ?>
          <span class="align_left">
              <b>B A N K</b> &nbsp;&nbsp;<select class="isreq pbank form-control" name="bank" id="bank" style="width: 25%;display: inline;"><?=$this->payrolloptions->getBankListSelect();?></select>
          </span>
        <? } ?>
        <span style="float: right;"  id="failed">
           <!--  <a href="#" class="btn btn-primary" id="printregreport">Payroll Registrar Report</a> -->

            <?if($issaved){?>
                <a href="#" class="btn btn-primary" id="print_payrollreg">Print Payroll Register</a> 
                <a href="#" class="btn btn-danger" id="undocutoff">UNDO Cut-Off</a> 
                <a href="#" class="btn btn-primary" id="finalize" <?= (!$canFinalize) ? "style='display:none;'" : "" ?> >FINALIZE</a> 
            <?}else{?>
                <a href="#" class="btn btn-primary" id="docutoff">Save Cut-Off</a> 
            <?}?>
        </span>

    </div>
    <br><br>
    
    <span class="align_left" id="loading" hidden=""><img src="<?=base_url()?>images/loading.gif">Loading.. Please wait.</span>

    <table class="table table-striped table-bordered table-hover" id="dble">
        <thead>
            <?if($dept != ""){?>
                <tr>
                    <th colspan="<?=12+sizeof($income_config)+sizeof($income_adj_config) + sizeof($fixeddeduc_config)+sizeof($deduction_config)+sizeof($loan_config)?>" class="align_center"><?=$dept?></td>
                </tr>
            <?}?>
            <tr>
                <th colspan='3' class="align_center">Information</td>
                <th colspan='<?=6+sizeof($income_config)+sizeof($income_adj_config)?>' class="align_center">Income</td>
                <th colspan='<?=1+sizeof($fixeddeduc_config)+sizeof($deduction_config)+sizeof($loan_config)?>' class="align_center">Deductions</td>
                <th rowspan="2">Total Deduction</td>
                <th rowspan="2">Net Pay</td>
                <th rowspan="2" class="align_center noSort" hidden="">Include<br>
                    <input type="checkbox" id="include_payroll_all" class="double-sized-cb"  checked disabled>
                </th>
                <th rowspan="2" class="align_center noSort">HOLD<br>
                    <input type="checkbox" id="hold_all" class="double-sized-cb">
                </th>
            </tr>
            <tr>
                <th  id="office_th" tag="office_th">Office&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                <th  id="employee_th" tag="employee_th">Employee ID</th>
                <th  id="name_th" tag="name_th">Name&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                <th>Regular Pay</th>
                <th>Overtime</th>
                <th>Tardy</th>
                <th>Absent</th>
                <th>Net Basic Pay</td>

                <?
                    foreach ($income_config as $key => $v) {?>
                        <th ><div style=" white-space: pre-wrap; <?=strlen($v['description']) <= 12 ? 'font-size: 14px' : 'font-size: 12px'?>"><?=$v['description']?></div></th>
                    <?}
                ?>

                <?
                    foreach ($income_adj_config as $key => $v) {?>
                        <th ><div style=" white-space: pre-wrap; <?=strlen($v['description'].' ADJ') <= 12 ? 'font-size: 14px' : 'font-size: 12px'?>"><?=$v['description'].' ADJ'?></div></th>
                    <?}
                ?>

                <th>Gross Salary</td>

                <th>WithHolding Tax</th>

                <?
                    foreach ($fixeddeduc_config as $key => $v) {?>
                        <th ><div style=" white-space: pre-wrap; <?=strlen($key) <= 12 ? 'font-size: 14px' : 'font-size: 12px'?>"><?=$key?></div></th>
                    <?}
                ?>

                <?
                    foreach ($deduction_config as $key => $v) {?>
                        <th ><div style=" white-space: pre-wrap; <?=strlen($v['description']) <= 12 ? 'font-size: 14px' : 'font-size: 12px'?>"><?=$v['description']?></div></th>
                    <?}
                ?>

                <?
                    foreach ($loan_config as $key => $v) {?>
                        <th ><div style=" white-space: pre-wrap; <?=strlen($v['description']) <= 12 ? 'font-size: 14px' : 'font-size: 12px'?>"><?=$v['description']?></div></th>
                    <?}
                ?>

             
                
            </tr>
        </thead>
        <tbody>  
            <?
                $income = $income_adj = $fixed = $deduction = $loans =  0;
                foreach ($emplist as $empid => $detail) {
                    $totalDeduction = 0;
                    ?>
                    <tr employeeid="<?=$empid?>" id="<?=$empid?>">
                        <td ><div style="  white-space: pre-wrap;"><?=$this->extensions->getEmployeeOfficeDesc($empid)?></div></td>
                        <td ><div style="  white-space: pre-wrap;"><?=$empid?></div></td>
                        <td ><div style="  white-space: pre-wrap;"><?=$detail['fullname']?></div></td>
                        <td><?=formatAmount($detail['salary'])?></td>
                        <td><?=formatAmount($detail['overtime'])?></td>
                        <td><?=isset($detail['tardy'])?formatAmount($detail['tardy']):'0.00'?></td>
                        <td><?=isset($detail['absents'])?formatAmount($detail['absents']):'0.00'?></td>
                        <td><?=number_format(($detail['netbasicpay'] + $detail['overtime']),2)?></td>

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
                        
                        <td><?=number_format($detail['grosspay'],2)?></td>

                        <td><?=isset($detail['whtax'])?formatAmount($detail['whtax']):'0.00'?></td>

                        <?
                        $totalDeduction += isset($detail['whtax'])?$detail['whtax']:0;
                            foreach ($fixeddeduc_config as $code => $v) {
                                $fixed = isset($detail['fixeddeduc'][$code]) ? $detail['fixeddeduc'][$code] : 0;
                                $totalDeduction += $fixed;
                                ?>
                                <td><?=isset($detail['fixeddeduc'][$code])?formatAmount($detail['fixeddeduc'][$code]):'0.00'?></td>
                            <?}
                        ?>

                        <?
                            foreach ($deduction_config as $code => $v) {
                                $deduction = isset($detail['deduction'][$code]) ? $detail['deduction'][$code] : 0;
                                $totalDeduction += $deduction;
                                ?>
                                <td><?=isset($detail['deduction'][$code])?formatAmount($detail['deduction'][$code]):'0.00'?></td>
                            <?}
                        ?>

                        <?
                            foreach ($loan_config as $code => $v) {

                                $loans = isset($detail['loan'][$code])? $detail['loan'][$code] : 0;
                                $totalDeduction += $loans;
                                ?>
                                <td><?=isset($detail['loan'][$code])?formatAmount($detail['loan'][$code]):'0.00'?></td>
                            <?}
                        ?>

                        <td><?=number_format($totalDeduction,2)?></td>
                        <td><?=number_format($detail['netpay'],2)?></td>

                        <td class="align_center" hidden="">
                            <input type="checkbox" name="include_payroll" class="double-sized-cb" employeeid="<?=$empid?>" checked disabled>
                        </td>
                        <td class="align_center">
                            <input type="checkbox" name="hold" class="double-sized-cb hold" base_id="<?=$detail['base_id']?>" <?=$detail['isHold']?'checked':''?>>
                        </td>

                    </tr>
                <?}

            ?>
        </tbody>
    </table>
</div>

<?

function formatAmount($amount=''){
    if($amount){
      if(is_numeric($amount)){
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
    }else{
        $amount = '0.00';
    }
    return $amount;
}

?>

<form id="reportform">
  <input type="hidden" name="deptid" value="<?=$deptid?>">
  <input type="hidden" name="employeeid" value="<?=$employeeid?>">
  <input type="hidden" name="schedule" value="<?=$schedule?>">
  <input type="hidden" name="cutoff" value="<?=$cutoff?>">
  <input type="hidden" name="quarter" value="<?=$quarter?>">
  <input type="hidden" name="status" value="SAVED">
  <input type="hidden" name="bank">
</form>
<script>
$(document).ready(function(){
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
            scrollY:        "300px",
            scrollX:        true,
            scrollCollapse: true,
            paging:         false,
            retrieve: true,
            // fixedHeader: true,
            columnDefs: [
              { width: 500, targets: 0 },
              { width: 300, targets: 1 },
              { width: 500, targets: 2 }
            ],
            fixedColumns:   {
                leftColumns: 3
            }


        });
        $(".DTFC_LeftBodyLiner").css({"overflow-x":"hidden"});
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


    ///< select employees to include ---- hide to include in ica

    /*$('#include_payroll_all').on('click',function(){
      toggleIncludeAll(this,'input[name=include_payroll]',true,payroll_table,'select[name=dble_length]');
    });

    $('input[name=include_payroll]').on('click',function(){
      toggleInclude(this,'#include_payroll_all');
    });*/

    ///< select employees to HOLD

    $('#hold_all').on('click',function(){
      toggleIncludeAll(this,'input[name=hold]',true,payroll_table,'select[name=dble_length]');
    });

    $('input[name=hold]').on('click',function(){
      toggleInclude(this,'#hold_all');
    });

});


    $("#print_payrollreg").click(function(){
      $("#reportform").find("input[name='bank']").val($('.bank').val());
      $("#reportform").attr("target", "_blank");
      $("#reportform").attr("action", "<?=site_url("forms/payrollRegisterReport")?>");
      $("#reportform").attr("method", "post");
      $("#reportform").submit();
    });


    $("#docutoff").click(function(){
        // $(this).hide();
        var emplist = [];

        $('input[name=include_payroll]:checked').each(function(){
            var tr_id = $(this).closest('tr').attr('id');
            var isOnhold = $("tr[id='"+ tr_id +"']").find("input[name=hold]").is(':checked') ? 1 : 0;
            if(isOnhold == 0) emplist.push($(this).attr('employeeid'));
        });
        // console.log(emplist); return;
        if(emplist.length == 0){
            Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'No employee selected.',
              showConfirmButton: true,
              timer: 2000
          });
          return;
        }

        if(!$('.pbank').val()){
          Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'Bank is required.',
              showConfirmButton: true,
              timer: 2000
          });
          return;
        }

        // return;

        $('#loading').removeAttr('hidden');
        $("#failed,#docutoff").hide();
        $("#success").hide();

        var form_data = {
          deptid       :   "<?=$deptid?>",
          employeeid   :   "<?=$employeeid?>",
          schedule     :   "<?=$schedule?>",
          cutoff       :   "<?=$cutoff?>", 
          quarter      :   "<?=$quarter?>",
          status       :   "SAVED",
          bank         :   $('.pbank').val()
        };

        form_data['emplist'] = emplist;


        $.ajax({
         url     :   "<?=site_url("payroll_/savePayrollCutoffSummary")?>",
         type    :   "POST",
         dataType : 'json',
         data    :   form_data,
         success :   function(msg){
                          $("#docutoff").show();
                         $('#modal-view').find('.modal-header, #button_save_modal').hide();

                         var data_failed = msg.data_failed;
                         var failed = '';
                         for (var key in data_failed) {
                             failed += data_failed[key] + ", ";
                         }
                         if(failed) failed = failed.substring(0, failed.length-2);
                         else failed = 'NONE';

                         if(msg.err_code == 0){
                           
                           if(failed == 'NONE') $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'green','font-size':'15px','font-weight':'bold'});
                           else{
                             $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>').css({'color':'green','font-size':'15px','font-weight':'bold'});
                             $('#modal-view').find('.modal-body').append('<span style="color:red;">Data insert failed: '+failed+'</span>');
                           }                  
                         }else{
                           $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'red','font-size':'15px','font-weight':'bold'});
                         }

                         $('#modal-view').modal('show');
                         $('#loading').attr('hidden',true);
                         displayPayroll();

                     }
        });

    });

    


    $("#undocutoff").click(function(){

        var emplist = [];

        $('input[name=include_payroll]:checked').each(function(){
            var tr_id = $(this).closest('tr').attr('id');
            /*var isOnhold = $("tr[id='"+ tr_id +"']").find("input[name=hold]").is(':checked') ? 1 : 0;
            if(isOnhold == 1)*/ emplist.push($(this).attr('employeeid'));
        });

        if(emplist.length == 0){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'No employee selected.',
                showConfirmButton: true,
                timer: 2000
            });
            return;
        }
        
        $('#loading').removeAttr('hidden');
        $("#failed,#undocutoff").hide();
        $("#success").hide();

        var form_data = {
          deptid       :   "<?=$deptid?>",
          employeeid   :   "<?=$employeeid?>",
          schedule     :   "<?=$schedule?>",
          cutoff       :   "<?=$cutoff?>", 
          quarter      :   "<?=$quarter?>",
          status       :   "PENDING"
        };


        form_data['emplist'] = emplist;


        $.ajax({
         url     :   "<?=site_url("payroll_/savePayrollCutoffSummary")?>",
         type    :   "POST",
         dataType : 'json',
         data    :   form_data,
         success :   function(msg){
                         $('#modal-view').find('.modal-header, #button_save_modal').hide();

                         var data_failed = msg.data_failed;
                         var failed = '';
                         for (var key in data_failed) {
                             failed += data_failed[key] + ", ";
                         }
                         if(failed) failed = failed.substring(0, failed.length-2);
                         else failed = 'NONE';

                         if(msg.err_code == 0){
                           
                           if(failed == 'NONE') $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'green','font-size':'15px','font-weight':'bold'});
                           else{
                             $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>').css({'color':'green','font-size':'15px','font-weight':'bold'});
                             $('#modal-view').find('.modal-body').append('<span style="color:red;">Data insert failed: '+failed+'</span>');
                           }                  
                         }else{
                           $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'red','font-size':'15px','font-weight':'bold'});
                         }

                         $('#modal-view').modal('show');
                         $('#loading').attr('hidden',true);

                         $('#display_payroll_saved').click();
                     }
        });

    });


    $("#finalize").click(function(){
        var emplist = [];

        $('input[name=include_payroll]:checked').each(function(){
            var tr_id = $(this).closest("tr").attr("employeeid");
            var ishold = $("tr[employeeid='"+ tr_id +"']").find(".hold").is(':checked') ? 1 : 0;
            if(ishold == 0) emplist.push($(this).attr('employeeid'));
        });
        if(emplist.length == 0){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'No employee selected.',
                showConfirmButton: true,
                timer: 2000
            });
            return;
        }

        $('#loading').removeAttr('hidden');
        $("#failed,#docutoff").hide();
        $("#success").hide();

        var form_data = {
          deptid       :   "<?=$deptid?>",
          employeeid   :   "<?=$employeeid?>",
          schedule     :   "<?=$schedule?>",
          cutoff       :   "<?=$cutoff?>", 
          quarter      :   "<?=$quarter?>",
          status       :   "PROCESSED"
        };

        form_data['emplist'] = emplist;


        $.ajax({
         url     :   "<?=site_url("payroll_/finalizePayrollCutoffSummary")?>",
         type    :   "POST",
         dataType : 'json',
         data    :   form_data,
         success :   function(msg){
                         $('#modal-view').find('.modal-header, #button_save_modal').hide();

                         var data_failed = msg.data_failed;
                         var failed = '';
                         for (var key in data_failed) {
                             failed += data_failed[key] + ", ";
                         }
                         if(failed) failed = failed.substring(0, failed.length-2);
                         else failed = 'NONE';

                         if(msg.err_code == 0){
                           
                           if(failed == 'NONE') $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'green','font-size':'15px','font-weight':'bold'});
                           else{
                             $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>').css({'color':'green','font-size':'15px','font-weight':'bold'});
                             $('#modal-view').find('.modal-body').append('<span style="color:red;">Data insert failed: '+failed+'</span>');
                           }                  
                         }else{
                           $('#modal-view').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.success_count+'<br>'+'Data insert failed: '+failed).css({'color':'red','font-size':'15px','font-weight':'bold'});
                         }

                         $('#modal-view').modal('show');
                         $('#loading').attr('hidden',true);


                     }
        });

    });


    $('input[name=hold]').on('click',function(){
        var obj = $(this);
        var base_id = $(this).attr('base_id');
        var isHold = $(this).is(':checked') ? 1 : 0;
        if(isHold){
          var tr = $(this).closest("tr");
          tr.find("input[name='include_payroll']").prop("checked", false);
        }else{
          var tr = $(this).closest("tr");
          tr.find("input[name='include_payroll']").prop("checked", true);
        }
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


    $("#printregreport").click(function(){

        var emplist = [];

        $('input[name=include_payroll]:checked').each(function(){
            emplist.push($(this).attr('employeeid'));
        });

        var params = "?form=registrarreport";
            params += "&eid=<?=$employeeid?>";
            params += "&dept=<?=$deptid?>";
            params += "&dfrom=<?=$sdate?>"; 
            params += "&dto=<?=$edate?>";
            params += "&schedule=<?=$schedule?>";
            params += "&quarter=<?=$quarter?>";
            params += "&campus=<?=$campus?>";
            params += "&status=<?=$status?>";
            params += "&emplist="+emplist;
        
     var dialog = $('<b>Do you want to sort by name??</b>').dialog({
            modal: true,
            draggable: false,    
            resizable: false,
            buttons: {
                "Yes": function() {
                    params += "&sort=0";
                    window.open("<?=site_url("forms/loadForm")?>"+params);
                     dialog.dialog('close');
                },
                // "No":  function() {
                //     params += "&sort=1";
                //     window.open("<?=site_url("forms/loadForm")?>"+params);
                //      dialog.dialog('close');
                // },
                "Cancel":  function() {
                    dialog.dialog('close');
                }
            }
        });
         
    });



   

    
</script>