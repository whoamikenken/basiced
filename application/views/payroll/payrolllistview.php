<?php
 
/**
 * @author Justin
 * @copyright 2015
 */
 
$dates = explode('+',$payrollcutoff);
$sdate = $dates[0];
$edate = $dates[1];
$dept = $deptid;
$campus = $campusid;

// $departments = $this->extras->showdepartment();
// $tincome = $tdeduct = $tloan = $tdeductoth = 0;


/*// total employee in dept.
list($ttal,$cemp) = $this->payrolloptions->demptotal($employeeid,$dept,$sdate,$edate,$schedule,$quarter,$campus);
#print_r($cemp);

$tincome    = $this->payrolloptions->incometitle('','code_income',$schedule,$quarter)->num_rows();                  // count income
$tdeduct    = $this->payrolloptions->deducttitle('','code_deduction','HIDDEN',$schedule,$quarter)->num_rows();      // count deduct fixed
$tloan      = $this->payrolloptions->loantitle('','code_loan',$schedule,$quarter)->num_rows();                      // count loan
$tdeductoth = $this->payrolloptions->deducttitle('','code_deduction','SHOW',$schedule,$quarter)->num_rows();        // count deduct others
$countincome  = count($this->payrolloptions->incometitlep('',$schedule,$quarter,$sdate,$edate));
$countothdec = count($this->payrolloptions->deducttitleothp('',$schedule,$quarter,$sdate,$edate));*/

?>
<style>
.ui-dialog .ui-dialog-titlebar.ui-widget-header{background: none; border: none; height: 20px; width: 20px; padding: 0px; position: static; float: right; margin: 0px 2px 0px 0px;}
.ui-dialog-titlebar.ui-widget-header .ui-dialog-title{display: none;}
.ui-dialog-titlebar.ui-widget-header .ui-button{background: none; border: 1px solid #CCCCCC;}
.ui-dialog .ui-dialog-titlebar .ui-dialog-titlebar-close{margin: 0px; position: static;}
.ui-dialog .dialog.ui-dialog-content{padding: 0px 10px 10px 10px;}
.ui-dialog .ui-dialog-titlebar .ui-dialog-titlebar-close .ui-icon{position: relative; margin-top: 0px; margin-left: 0px; top: 0px; left: 0px;}
.ui-dialog .ui-dialog-titlebar-close {position: absolute;right: .3em;top: 50%;width: 21px;margin: -10px 0 0 0;padding: 1px;height: 20px;display:none;}
</style>
<div class="content no-search">
<div class="align_right" style="margin-bottom: 3px;">
    <a href="#" class="btn btn-primary" id="printregreport">Payroll Register Report</a>
    <a href="#" class="btn btn-primary" id="printcutoff">PaySlip</a>
    <!-- <a href="#" class="btn btn-primary" id="printdetail">Payroll Detail</a> -->
    <a href="#" class="btn btn-primary" id="printcontri" style="display:none;">Contributions &amp; Loans</a>
    <a href="#" class="btn btn-primary" id="printemphis" style="display:none;">Employee History</a>
    <a href="#" class="btn btn-primary" id="printbreakdown" style="display:none;">Payroll Break Down</a>
    <a href="#" class="btn btn-primary report" id="payrollsummary" reportname="payrollsummary" data-toggle="modal" data-target="#myModal" style="display:none;">Payroll Summary</a>
    <a href="#" class="btn btn-primary report" id="atmpayrolllist" reportname="atmpayrolllist" data-toggle="modal" data-target="#myModal">ATM Payroll List</a>
    <a href="#" class="btn btn-primary" id="pagibigLoan" style="display:none;">PAG-IBIG LOAN</a>
    <a href="#" class="btn btn-primary" id="pagibigPremium" style="display:none;">PAG-IBIG PREMIUM</a>
    <a href="#" class="btn btn-primary" id="sssContribution" style="display:none;">SSS Contribution</a>
   <!-- <a href="#" class="btn btn-primary" id="sample">sample</a> -->
</div>
<?php include ('processedpayrollresult.php');?>
</div>
<div class="modal fade" id="myModal" data-backdrop="static"></div>

<div id="selectbox" hidden="">
    <b>CLICK CHECKBOX TO DISPLAY </b>
    <br>
    <br>

    <b>EARNINGS</b>
    <br>
    <label><input type="checkbox" class="income"/> Select all</label><br>
    <?php if(isset($incomes)): ?>
        <?php foreach ($incomes as $k => $v): ?>
            <?php if ($v['description'] != "" || $v['description'] != null): ?>
                    <input class="align_right" type="checkbox" name="income" value="<?=$v["id"]?>"> <?=$v["description"]?><br>    
            <?php endif ?>
        <?php endforeach ?>
    <?php endif ?>
    <br>
    <b>LOANS</b>
    <br>
    <label><input type="checkbox"  class="loan"/> Select all</label>
    <br>
    <?php if(isset($loan)): ?>
        <?php foreach ($loan as $k => $v): ?>
                <input class="align_right" type="checkbox" name="loan" value="<?=$v["id"]?>"><?=$v["description"]?><br>
        <?php endforeach ?>
    <?php endif ?>
    <b>OTHER DEDUCTION</b>
    <br>
    <label><input type="checkbox" class="deduction"/> Select all</label><br>
    <?php if(isset($deductionsOthers)): ?>
        <?php foreach ($deductionsOthers as $k => $v): ?>
                <input class="align_right" type="checkbox" name="deduction" value="<?=$v["id"]?>"> <?=$v["description"]?><br>
        <?php endforeach  ?>
    <?php endif ?>
    <br>

</div>
<form id="reportForm">
    <input type="hidden" name="form">
    <input type="hidden" name="eid">
    <input type="hidden" name="dept">
    <input type="hidden" name="office">
    <input type="hidden" name="dfrom">
    <input type="hidden" name="dto">
    <input type="hidden" name="schedule">
    <input type="hidden" name="quarter">
    <input type="hidden" name="status">
    <input type="hidden" name="campus">
    <input type="hidden" name="sort">
</form>
<script src="<?=base_url()?>jsbstrap/jquery-ui-1.10.3.js" type="text/javascript"></script>
<script>
// $(document).ready(function(){
//  $('#selectbox input[name="loan"]').prop('checked', true);
// });

///< @Angelica - new config for reports
$(".report").click(function(){
   var form_data   =   {
                           reportname       : $(this).attr("reportname"),
                           view             : "setup/processed_report_config",
                           deptid           : "<?=$dept?>",                            
                           employeeid       : "<?=$employeeid?>",                            
                           payrollcutoff    : "<?=$payrollcutoff?>",                            
                           schedule         : "<?=$schedule?>",                            
                           quarter          : "<?=$quarter?>"                         
                       }
   $.ajax({
       url      :   "<?=site_url("payroll_/getReportConfig")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
           $("#myModal").html(msg);
       }
   });
});


$(document).on("click",'.income',function() {
    if ($(this).is(':checked')) {
        $('#selectbox input[name="income"]').prop('checked', true);
    } else {
        $('#selectbox input[name="income"]').prop('checked', false);
    }
});
$(document).on("click",'.loan',function() {
    if ($(this).is(':checked')) {
        $('#selectbox input[name="loan"]').prop('checked', true);
    } else {
        $('#selectbox input[name="loan"]').prop('checked', false);
    }
});
$(document).on("click",'.deduction',function() {
    if ($(this).is(':checked')) {
        $('#selectbox input[name="deduction"]').prop('checked', true);
    } else {
        $('#selectbox input[name="deduction"]').prop('checked', false);
    }
});
/*
 * Jquery Functions 
 */
// $('body').on('click','#selectbox :checkbox',function(e){
//     var index = $(this).index();
//     console.log(index);
//    $('.loantype').find('input[type=checkbox]').eq(index).click();
// });
$(".edit_data").click(function(){
    var form_data   =   {
                            id      : $(this).attr("id"),
                            view    :   "modifypayrolldata"                            
                        }
    $.ajax({
        url      :   "<?=site_url("payroll_/payrollconfig")?>",
        type     :   "POST",
        data     :   form_data,
        success  :   function(msg){
            $("#myModal").html(msg);
        }
    });
});

$("#printbreakdown").click(function(){
    $("#selectbox").show();
    var params = "";
    var loans = "";
    var loan=[];
    var income =[];
    var fixdeduction = [];
    var deduction = [];

   
    params = "?form=payrollbreakdown";
    params += "&eid=<?=$employeeid?>";
    params += "&dept=<?=$deptid?>";
    params += "&dfrom=<?=$sdate?>"; 
    params += "&dto=<?=$edate?>";
    params += "&schedule=<?=$schedule?>";
    params += "&quarter=<?=$quarter?>";
   
   
 
   
       
    var dialog = $('#selectbox').dialog({
        modal: true,
        draggable: false,    
        resizable: false,
        width: "20%",
        buttons: {
            "OK": function() {
                $("#selectbox input[name='loan']").each(function()
                {
                if ($(this).is(":checked")){
                 loan.push($(this).val());
                }
                });
                $("#selectbox input[name='income']").each(function()
                {
                if ($(this).is(":checked")){
                 income.push($(this).val());
                }
                });
                $(".fixdeductiontype input[name='fixdeduction']").each(function()
                {
                if ($(this).is(":checked")){
                 fixdeduction.push($(this).val());
                
}                });
                 $("#selectbox input[name='deduction']").each(function()
                {
                if ($(this).is(":checked")){
                 deduction.push($(this).val());
                }
                });
                params += "&loan="+ loan;
                params += "&income="+ income;
                params += "&fixdeduction="+ fixdeduction;
                params += "&deduction="+ deduction;
                params += "&sort=0";
                window.open("<?=site_url("forms/loadForm")?>"+params);
                
                $("#selectbox").find("input[type='checkbox']").prop('checked',false);
                $('.loantype').find("input[type=checkbox]").prop('checked',false);
                dialog.dialog('close');
                
            },
            // "No":  function() {
            //     $(".loantype input[name='loan']").each(function()
            //     {
            //     if ($(this).is(":checked")){
            //      array.push($(this).val());

            //     }
            //     });
            //     params += "&loan="+ array;
            //     params += "&sort=1";
            //     window.open("<?=site_url("forms/loadForm")?>"+params);
            //     $("#selectbox").find("input[type='checkbox']").prop('checked',false);
            //     $('.loantype').find('input[type=checkbox]').prop('checked',false);
            //     dialog.dialog('close');
            // },
            "Cancel":  function() {
                dialog.dialog('close');
            }
        }
    }); 
});

$("#printregreport").click(function(){
    $("input[name='form']").val("payslip");
    $("input[name='eid']").val("<?=$employeeid?>");
    $("input[name='dept']").val("<?=$deptid?>");
    $("input[name='office']").val("<?=$office?>");
    $("input[name='dfrom']").val("<?=$sdate?>");
    $("input[name='dto']").val("<?=$edate?>");
    $("input[name='schedule']").val("<?=$schedule?>");
    $("input[name='quarter']").val("<?=$quarter?>");
    $("input[name='status']").val("<?=$status?>");
    $("input[name='campus']").val("<?=$campus?>");
    $("input[name='sort']").val("0");
    $("#reportForm").find("input[name='bank']").val($('.pbank').val());
    $("#reportForm").attr("target", "_blank");
    $("#reportForm").attr("action", "<?=site_url("forms/payrollRegisterReport")?>");
    $("#reportForm").attr("method", "post");
    $("#reportForm").submit();
});

$("#printcutoff").click(function(){
    var params = "form=payslip";
        params += "&eid=<?=$employeeid?>";
        params += "&dept=<?=$deptid?>";
        params += "&dfrom=<?=$sdate?>"; 
        params += "&dto=<?=$edate?>";
        params += "&schedule=<?=$schedule?>";
        params += "&quarter=<?=$quarter?>";
        params += "&campus=<?=$campus?>";
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    })

    swalWithBootstrapButtons.fire({
        title: 'Generate Report',
        text: "Generate PDF Report?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
      if (result.value) {
            params += "&sort=0";
            var encodedData = encodeURIComponent(window.btoa(params));
            window.open("<?=site_url("forms/loadForm")?>?formdata="+encodedData,"");
            // window.open("<?=site_url("forms/loadForm")?>"+params);
      } else if (
        result.dismiss === Swal.DismissReason.cancel
      ) {
            swalWithBootstrapButtons.fire(
                'Cancelled',
                '',
                'error'
            )
        }
    })

      
       
    // var dialog = $('<b>Generate PDF format?</b>').dialog({
    //     modal: true,
    //     draggable: false,    
    //     resizable: false,
    //     buttons: {
    //         "Yes": function() {
    //             params += "&sort=0";
    //             window.open("<?=site_url("forms/loadForm")?>"+params);
    //         },
    //         "No":  function() {
    //             // params += "&sort=1";
    //             // window.open("<?=site_url("forms/loadForm")?>"+params);
    //             dialog.dialog('close');
    //         }
    //         // ,
    //         // "Cancel":  function() {
    //         //     dialog.dialog('close');
    //         // }
    //     }
    // }); 
});

$("#printdetail").click(function(){
   var frm = "";
   if("<?=$employeeid?>" == "" && "<?=$deptid?>" != "") frm = "payrolldetailgrp"
   else frm = "payrolldetail";
   var params = "?form="+frm;
       params += "&eid=<?=$employeeid?>";
       params += "&dept=<?=$deptid?>";
       params += "&office=<?=$office?>";
       params += "&dfrom=<?=$sdate?>"; 
       params += "&dto=<?=$edate?>";
       params += "&schedule=<?=$schedule?>";
       params += "&quarter=<?=$quarter?>";
       if("<?=$employeeid?>" != "" || "<?=$deptid?>" != "")
        window.open("<?=site_url("forms/loadForm")?>"+params);
       else
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please Select An Employee Or Department.',
            showConfirmButton: true,
            timer: 2000
        });
});

$("#printcontri").click(function(){
   var frm = "";
   frm = "payrollcont";
   var params = "?form="+frm;
       params += "&eid=<?=$employeeid?>";
       params += "&dept=<?=$deptid?>";
       params += "&office=<?=$office?>";
       params += "&dfrom=<?=$sdate?>"; 
       params += "&dto=<?=$edate?>";
       params += "&schedule=<?=$schedule?>";
       params += "&quarter=<?=$quarter?>";
       if("<?=$employeeid?>" != "")
        window.open("<?=site_url("forms/loadForm")?>"+params);
       else
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please Select An Employee.',
            showConfirmButton: true,
            timer: 2000
        });
});
$("#printemphis").click(function(){
   frm = "emphistory";
   var params = "?form="+frm;
       params += "&eid=<?=$employeeid?>";
       params += "&dept=<?=$deptid?>";
       params += "&office=<?=$office?>";
       params += "&dfrom=<?=$sdate?>"; 
       params += "&dto=<?=$edate?>";
       params += "&schedule=<?=$schedule?>";
       params += "&quarter=<?=$quarter?>";
       if("<?=$employeeid?>" != "")
        window.open("<?=site_url("forms/loadForm")?>"+params);
       else
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please Select An Employee.',
            showConfirmButton: true,
            timer: 2000
        });
});


$("#pagibigLoan").click(function(){
    var params = "?form=pagibigLoanXls";
        params += "&eid=<?=$employeeid?>";
        params += "&dept=<?=$deptid?>";
        params += "&office=<?=$office?>";
        params += "&dfrom=<?=$sdate?>"; 
        params += "&dto=<?=$edate?>";
        params += "&schedule=<?=$schedule?>";
        params += "&quarter=<?=$quarter?>";
        params += "&sort=0";
        
       
    var dialog = $('<b>Download Excel Format?</b>').dialog({
        modal: true,
        draggable: false,    
        resizable: false,
        buttons: {
            "Yes": function() {
                window.open("<?=site_url("forms/loadExcelReport")?>"+params);
            },
            "No":  function() {
                // params += "&sort=1";
                // window.open("<?=site_url("forms/loadForm")?>"+params);
                dialog.dialog('close');
            }
            // ,
            // "Cancel":  function() {
            //     dialog.dialog('close');
            // }
        }
    }); 
});

$("#pagibigPremium").click(function(){
    var params = "?form=pagibigPremiumXls";
        params += "&eid=<?=$employeeid?>";
        params += "&dept=<?=$deptid?>";
        params += "&office=<?=$office?>";
        params += "&dfrom=<?=$sdate?>"; 
        params += "&dto=<?=$edate?>";
        params += "&schedule=<?=$schedule?>";
        params += "&quarter=<?=$quarter?>";
        params += "&sort=0";
        
       
    var dialog = $('<b>Download Excel Format?</b>').dialog({
        modal: true,
        draggable: false,    
        resizable: false,
        buttons: {
            "Yes": function() {
                window.open("<?=site_url("forms/loadExcelReport")?>"+params);
            },
            "No":  function() {
                // params += "&sort=1";
                // window.open("<?=site_url("forms/loadForm")?>"+params);
                dialog.dialog('close');
            }
            // ,
            // "Cancel":  function() {
            //     dialog.dialog('close');
            // }
        }
    }); 
});



$("#sssContribution").click(function(){
    var params = "";
        params = "?eid=<?=$employeeid?>";
        params += "&dept=<?=$deptid?>";
        params += "&office=<?=$office?>";
        params += "&dfrom=<?=$sdate?>"; 
        params += "&dto=<?=$edate?>";
        params += "&schedule=<?=$schedule?>";
        params += "&quarter=<?=$quarter?>";
        params += "&campus=<?=$campus?>";
        
       
    var dialog = $('<b>Download Excel Format?</b>').dialog({
        modal: true,
        draggable: false,    
        resizable: false,
        buttons: {
            "Yes": function() {
                params += "&sort=0";
                params += "&employeeid=";
                params += "&view=reports_excel/sssContributionXls";
                window.open("<?=site_url("reports_/reportloader")?>"+params);
                 dialog.dialog('close');
            },
            "No":  function() {
                // params += "&sort=1";
                // window.open("<?=site_url("forms/loadForm")?>"+params);
                dialog.dialog('close');
            }
            // ,
            // "Cancel":  function() {
            //     dialog.dialog('close');
            // }
        }
    }); 
});

</script>