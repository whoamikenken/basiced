<?php

/**
 * @author Ken
 * @copyright 2019
 */

?>
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b><i class="glyphicon glyphicon-print">&nbsp;</i>PAYROLL REPORTS</b></h4></div>
                   <div class="panel-body">
                        <div class="col-md-4"><h2><b><i class="glyphicon glyphicon-paperclip"></i>&nbsp;&nbsp;&nbsp;INCOME REPORTS</b></h2>
                          <div class="col-sm-12" style="margin-top: 15px;">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate all income transactions per income ">
                                <a href="#" class="list-group-item incomereport" rep="incomereportxls" modaltitle="Income Transaction (Per Income)" data-toggle="modal" data-target="#myModal">
                                    <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Income Transaction (Per Income)</b></h6>
                                </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate all income transactions per employee">
                                  <a href="#" class="list-group-item incomereport" rep="incomereportemployeexls" modaltitle="Income Transaction (Per Employee)" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Income Transaction (Per Employee)</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-md-12 reportstyle" style="display: none;">
                            <a href="#" class="incomereport" rep="incomeadj" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-print large"></i> <b>Income Adjustment Report (Per Income)</b></a>
                          </div>
                          <div class="col-md-12 reportstyle" style="display: none;">
                            <a href="#" class="incomereport" rep="incomeadjemp" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-print large"></i> <b>Income Adjustment Report (Per Employee)</b></a>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate employee's Net Pay per cut-off.">
                                  <a href="#" class="list-group-item payroll-report" id="netPayHistory" modaltitle="Net Pay History" rep="netPayHistory" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Net Pay History</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-md-12 reportstyle div_remove" >
                            <a href="#" class="pdfforms" id="empledger" rep="empledgerForm" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-print large"></i> <b>Employee Ledger Report</b></a>
                          </div>
                        </div>
                        <div class="col-md-4"><h2><b><i class="glyphicon glyphicon-paperclip"></i>&nbsp;&nbsp;&nbsp;DEDUCTION REPORTS</b></h2>
                          <div class="col-sm-12" style="margin-top: 15px;">
                            <div class="list-group" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate SSS, PHILHEALTH and PAG IBIG Contributions per month.">
                                <a href="#" class="list-group-item pdfforms" id="rdcForm" rep="rdcForm" modaltitle="Reglementary Deduction Contributions" data-toggle="modal" data-target="#myModal">
                                    <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Reglementary Deduction Contributions</b></h6>
                                </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate Withholding Tax per month.">
                                  <a href="#" class="list-group-item pdfforms" id="mrrReport" rep="mrrReport" modaltitle="Monthly Remittance Return of Income Tax w/ Held" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Monthly Remittance Return of Income Tax w/ Held</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-md-12 reportstyle div_remove" >
                            <a href="#" class="pdfforms" id="sssform" rep="sssform" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-print large"></i> <b>SSS Contribution Collection List (R-3)</b></a>
                          </div>
                          <div class="col-md-12 reportstyle div_remove" >
                            <a href="#" class="pdfforms" id="pagibigform" rep="pagibigform" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-print large"></i> <b>PAG-IBIG MCRF (FPF060)</b></a>
                          </div>
                          <div class="col-md-12 reportstyle div_remove" >
                            <a href="#" class="pdfforms" id="philhealthform" rep="philhealthform" data-toggle="modal" data-target="#myModal"><i class="glyphicon glyphicon-print large"></i> <b>PHILHEALTH RF-1</b></a>
                          </div>
                         <!--  <div class="col-md-12 reportstyle" >
                            <a href="#"><i class="glyphicon glyphicon-print large"></i> <b>MONTHLY REMITTANCE RETURN OF INCOME TAX W/HELD (1601-C)</b></a>
                          </div> -->
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate all employee's Alphalist Per Year.">
                                  <a href="#" class="list-group-item pdfforms" id="alphalistform_new" rep="alphalistform_new"  modaltitle="Alphalist"  data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Alphalist</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate SSS Contribution Certification.">
                                  <a href="#" class="list-group-item pdfforms" id="ssscontri" rep="ssscontri" modaltitle="Certificate of SSS Contribution" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Certificate of SSS Contribution</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate Philhealth Contribution Certification.">
                                  <a href="#" class="list-group-item pdfforms" id="philcontri" modaltitle="Certificate of Philhealth Contribution" rep="philcontri" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Certificate of Philhealth Contribution</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate PAGIBIG Contribution Certification.">
                                  <a href="#" class="list-group-item pdfforms" id="hdmfcontri" modaltitle="Certificate of Pag-ibig Fund Contribution" rep="hdmfcontri" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Certificate of Pag-ibig Fund Contribution</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate MP2 Contribution Certification.">
                                  <a href="#" class="list-group-item pdfforms" id="mp2contri" modaltitle="Certificate of MP2 Contribution" rep="mp2contri" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Certificate of MP2 Contribution</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate Pag-IBIG Voluntary Contribution Certification.">
                                  <a href="#" class="list-group-item pdfforms" id="pagibigvolcontri" modaltitle="Certificate of Pag-IBIG Voluntary Contribution" rep="pagibigvolcontri" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Certificate of Pag-IBIG Voluntary Contribution</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12" style="display: none;">
                            <div class="list-group" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" title="Report Description" data-content="Report for PAGIBIG per employee by period and department.">
                                  <a href="#" class="list-group-item pdfforms" id="pagibigFileWriter" rep="pagibigFileWriter" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>PAGIBIG File Writer</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12" style="display: none;">
                            <div class="list-group" data-toggle="popover" data-placement="bottom" data-container="body" data-trigger="hover" title="Report Description" data-content="Report for PhilHealth File Generator per employee and department.">
                                  <a href="#" class="list-group-item pdfforms" id="philhealthFileGenerator" rep="philhealthFileGenerator" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>PhilHealth File Generator</b></h6>
                                  </a>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4"><h2><b><i class="glyphicon glyphicon-paperclip"></i>&nbsp;&nbsp;&nbsp;OTHERS</b></h2>
                          <div class="col-sm-12" style="margin-top: 15px;">
                            <div class="list-group" data-toggle="popover" data-placement="left" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate Deduction and Loan transactions per Employee within cut-off.">
                                <a href="#" class="list-group-item pdfforms" id="employeeBalances" rep="employeeBalancesPerEmployee" modaltitle="Employee Balances (Per Employee)" data-toggle="modal" data-target="#myModal">
                                    <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee Balances (Per Employee)</b></h6>
                                </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="left" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate Deduction and Loan transactions per Deduction within cut-off.">
                                  <a href="#" class="list-group-item pdfforms" id="employeeBalances" rep="employeeBalancesPerDeduction" modaltitle="Employee Balances (Per Deduction)"  data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee Balances (Per Deduction)</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="left" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate Payroll Register Report per cut-off.">
                                  <a href="#" class="list-group-item other_report" id="payroll_registrar" rep="payroll_registrar" modaltitle="Payroll Register Report" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Payroll Register Report</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="left" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate Payslip of the Employee's .">
                                  <a href="#" class="list-group-item other_report" id="payslip-report" rep="payslip-report" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>PaySlip</b></h6>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="left" data-container="body" data-trigger="hover" title="Report Description" data-content="Allows the user to generate Bank Remittance.">
                                  <a href="#" class="list-group-item other_report" id="atm-payroll-report" rep="atm-payroll-report" data-toggle="modal" data-target="#myModal">
                                      <h6 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>ATM Payroll List</b></h6>
                                  </a>
                            </div>
                          </div>
                        </div>
                        <div class="field" style="height: 300px;"></div>
                    </div>
                    <div class="well-header" style="background: #343434;display: none;">
                        <h5>LIST OF EMPLOYEE WITH PAYROLL</h5>
                    </div>
                    <div class="well-content" style="display:none;">
                        <form id="reportslist">
                        <input type="hidden" name="view" value="reportslist" />
                        <div class="form_row">
                            <label class="field_name align_right">Department</label>
                            <div class="field">
                                <select class="chosen col-md-4" name="deptid">
                                    <option value="">All Department</option>
                                    <?
                                        $opt_department = $this->extras->showdepartment();
                                        foreach($opt_department as $c=>$val){
                                    ?>      <option value="<?=$c?>"><?=$val?></option><?
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form_row">
                            <label class="field_name align_right">Employee</label>
                            <div class="field">
                                <select class="chosen col-md-4" name="employeeid">
                                    <option value="">All Employee</option>
                                    <?
                                        $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),'','',true);
                                        foreach($opt_type as $val){
                                    ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>    
                        <div class="form_row no-search">
                            <div class="field" id="btnshow">
                                <a href="#" class="btn btn-primary" id="display_list">Employee with payroll</a>
                            </div>
                        </div>
                        <div id="report"></div><br />
                        </form>
                    </div>
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
var toks = hex_sha512(" ");
$('[data-toggle="popover"]').popover();

// comment by justin (wiht e) for ica-hyperion 21568
$(".div_remove").remove();

$("select[name='deptid']").change(function(){
  $.ajax({
        url: "<?=site_url("process_/callemployee")?>",
        type: "POST",
        data: {
           toks:toks,
           deptid : GibberishAES.enc($(this).val(), toks)
        },
        success: function(msg) {
            $("select[name='employeeid']").html(msg).trigger('liszt:updated');
        }
    });   
});
$("#display_list").click(function(){
   // var form_data = $("#reportslist").serialize();
   $("#btnshow").hide();
   $("#report").html('<img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</img>');
   $.ajax({
       url      :   "<?=site_url("payroll_/payrollconfig")?>",
       type     :   "POST",
       data     :   {
        toks:toks,
        view : GibberishAES.enc($("input[name='view']").val(), toks),
        deptid : GibberishAES.enc($("input[name='deptid']").val(), toks),
        employeeid : GibberishAES.enc($("input[name='employeeid']").val(), toks)
       },
       success  :   function(msg){
        $("#btnshow").show();    
        $("#report").html(msg);    
       }
   });
});


// $("#empledger").click(function(){
//    var form_data   =   {
//                            rfile   : $(this).attr("rep"),
//                            view    :   "setup/reportconfig"                            
//                        }
//    $.ajax({
//        url      :   "<?=site_url("payroll_/payrollconfig")?>",
//        type     :   "POST",
//        data     :   form_data,
//        success  :   function(msg){
//            $("#myModal").html(msg);
//        }
//    });
// });


// pdf report
$(".pdfforms").click(function(){
  //alert($(this).attr("rep"));
   var form_data   =   {
                           toks: toks,
                           rfile   : GibberishAES.enc($(this).attr("rep"), toks),
                           view    :   GibberishAES.enc("setup/reportconfig", toks) ,
                           title :   GibberishAES.enc($(this).attr("modaltitle"), toks)                        
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

$(".incomereport").click(function(){
   var form_data   =   {
                           toks: toks,
                           rfile   : GibberishAES.enc($(this).attr("rep"), toks),
                           view    :   GibberishAES.enc("setup/reportconfig", toks),
                           title :   GibberishAES.enc($(this).attr("modaltitle"), toks),                          
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

$('.payroll-report').on('click',function(){
  $.ajax({
       url      :   "<?=site_url("reports_/loadPayrollReportSetup")?>",
       type     :   "POST",
       data     :   {toks:toks,reportname:GibberishAES.enc($(this).attr('rep'), toks), title :   GibberishAES.enc($(this).attr("modaltitle"), toks)},
       success  :   function(msg){
           $("#myModal").html(msg);
       }
   });
});

$(".other_report").unbind('click').click(function(){
  var title = $(this).find('b').text();
  var report = $(this).attr('rep');

  $.ajax({
    url : "<?=site_url("reports_/loadOtherReport")?>",
    type : "POST",
    data : {toks:toks,report : GibberishAES.enc(report, toks)},
    success : function(content){
      $("#myModal").html(content);
      $("#myModal").find(".modal-title").html(title);
    }
  });
});

$(".chosen").chosen();
</script>