<?php
 
/**
 * @author Justin
 * @copyright 2015
 */
 
$dates = explode(' ',$payrollcutoff);
$sdate = $dates[0];
$edate = $dates[1];
$dept = $deptid;
$departments = $this->extras->showdepartment();

$tincome = $tdeduct = $tloan = $tdeductoth = 0;
$tincome    = $this->payrolloptions->incometitle('','code_income',$schedule,$quarter,'',$sdate,$edate)->num_rows();                  // count income
$tdeduct    = $this->payrolloptions->deducttitle('','code_deduction','HIDDEN',$schedule,$quarter)->num_rows();      // count deduct fixed
$tloan      = $this->payrolloptions->loantitle('','code_loan',$schedule,$quarter,'',$sdate,$edate)->num_rows();                      // count loan
$tdeductoth = $this->payrolloptions->deducttitle('','code_deduction','SHOW',$schedule,$quarter)->num_rows();        // count deduct others

$disableprint = ($this->payroll->printpayslip($schedule,$sdate,$edate,$quarter)>0) ? true : false; 
?>
<div class="content no-search">
<div class="align_left" style="margin-bottom: 3px; font-color: red;" id="success" hidden=""></div>
<div class="align_right" style="margin-bottom: 3px;" id="failed"><a href="#" class="btn btn-primary" id="docutoff">Save Cut-Off</a> <!--&nbsp;<a href="#" class="btn btn-primary" id="printcutoff">Print Cut-Off</a></div>-->
<table class="table table-striped table-bordered table-hover" id="dble">
    <thead>
        <?if($dept != ""){?>
            <tr>
                <th colspan="11" class="align_center"><?=$departments[$dept]?></td>
            </tr>
        <?}?>
        
        <tr>
            <th colspan='2' class="align_center">Information</td>
            <th colspan='<?=2+($this->payrolloptions->incometitle('','code_income',$schedule,$quarter)->num_rows());?>' class="align_center">Earnings</td>
            <th colspan='10' class="align_center">Deductions</td>
        </tr>
        <tr class="ititle">
            <th class="align_center" height='40'>Employee Id</th>
            <th class="align_center">Name</th>
            <th class="align_center">Regular Pay</th>
            <?foreach($this->payrolloptions->incometitle('','code_income',$schedule,$quarter,'',$sdate,$edate)->result() as $row){?>
            <!--<th class="align_center"><?=ucwords(strtolower($this->payrolloptions->incomedesc($row->title)))?></th>--> <!-- INCOME TITLE-->
            <th class="align_center" cincome='<?=$row->code_income?>'><?=$this->payrolloptions->incomedesc($row->title)?></th>
            <?}?>
            <th class="align_center">Overtime</th>
            <th class="align_center">WithHolding Tax</th>
            <?foreach($this->payrolloptions->deducttitle('','code_deduction','HIDDEN',$schedule,$quarter)->result() as $row){?>
            <!--<th class="align_center"><?=ucwords(strtolower($row->title))?></th>--> <!-- FIXED DEDUCTIONS TITLE-->
            <th class="align_center"><?=$row->title?></th>
            <?}?>
            <?foreach($this->payrolloptions->loantitle('','code_loan',$schedule,$quarter,'',$sdate,$edate)->result() as $row){?>
            <!--<th class="align_center"><?=ucwords(strtolower($this->payrolloptions->loandesc($row->title)))?></th>--> <!-- LOANS TITLE-->
            <th class="align_center" cloan='<?=$row->code_loan?>'><?=$this->payrolloptions->loandesc($row->title)?></th>
            <?}?>
            <?foreach($this->payrolloptions->deducttitle('','code_deduction','SHOW',$schedule,$quarter,'',$sdate,$edate)->result() as $row){?>
            <!--<th class="align_center"><?=ucwords(strtolower($this->payrolloptions->deductiondesc($row->title)))?></th>--> <!-- OTHER DEDUCTIONS TITLE-->
            <th class="align_center" cdeduct='<?=$row->code_deduction?>'><?=$this->payrolloptions->deductiondesc($row->title)?></th>
            <?}?>
            <th class="align_center">Tardy</th>
            <th class="align_center">Absent</th>
        </tr>
         
    </thead>
    <tbody>  

            <?
                $query = $this->payroll->loadAllEmpbyDept($dept,$employeeid,$schedule);
                foreach($query as $row){
                    $empid = $row->employeeid;
                    $regpay =  $row->regpay;
                    $dependents = $row->dependents;
                    $count = 0;
                ?>
                    <tr class="idata">
                        <td class="align_center"><?=$row->employeeid?></td>
                        <td class="align_center"><?=$row->fullname?></td>
                        <td class="align_center"><?=$row->regpay?></td>
                        
                        <!-- INCOME -->
                        <?
                        foreach($this->payrolloptions->incometitle('','code_income',$schedule,$quarter,'',$sdate,$edate)->result() as $tdata){
                            if($this->payrolloptions->incometitle($empid,'amount',$schedule,$quarter,$tdata->title,$sdate,$edate)->num_rows() > 0){
                                foreach($this->payrolloptions->incometitle($empid,'amount',$schedule,$quarter,$tdata->title,$sdate,$edate)->result() as $row){
                                ?>
                                    <td class="align_center"><?=$row->title?></th>  <!-- INCOME -->
                                <?
                                }
                            }else{
                            ?>
                                <td class="align_center">0</th>                 <!-- INCOME -->
                            <?
                            }
                        }
                        ?>
                        
                        <!-- Overtime -->
                        <td class="align_center"><?=$this->payroll->ottime($empid,$schedule,$quarter,$sdate,$edate);?></td>  <!-- Overtime -->
                        
                        <!-- WithHolding Tax -->
                        <td class="align_center"><?=$this->payroll->WHTax($empid,$schedule,$quarter,$sdate,$edate,$regpay,$dependents);?></td>  <!-- WITHHOLDING TAX -->
                        
                        <!-- FIXED Deductions -->
                        <?  
                        if($this->payrolloptions->deducttitle('','code_deduction','HIDDEN',$schedule,$quarter)){
                            foreach($this->payrolloptions->deducttitle('','code_deduction','HIDDEN',$schedule,$quarter)->result() as $tdata){
                                if($this->payrolloptions->deducttitle($empid,'amount','HIDDEN',$schedule,$quarter,$tdata->title)->num_rows() > 0){
                                    foreach($this->payrolloptions->deducttitle($empid,'amount','HIDDEN',$schedule,$quarter,$tdata->title)->result() as $row){
                                    ?>
                                        <td class="align_center"><?=$row->title?></td>  <!-- FIXED Deductions -->
                                    <?
                                    }
                                }else{
                                ?>
                                    <td class="align_center">0</th>                 <!-- FIXED Deductions -->
                                <?
                                }
                            }
                        }
                        ?>
                        
                        <!-- LOANS -->
                        <?
                        foreach($this->payrolloptions->loantitle('','code_loan',$schedule,$quarter,'',$sdate,$edate)->result() as $tdata){
                            if($this->payrolloptions->loantitle($empid,'amount',$schedule,$quarter,$tdata->title,$sdate,$edate)->num_rows() > 0){
                                $nocutoff = $this->payrolloptions->loantitle($empid,'nocutoff',$schedule,$quarter,$tdata->title,$sdate,$edate)->row(0)->title;
                                if($nocutoff == 1) $title = "famount"; else $title = "amount"; 
                                foreach($this->payrolloptions->loantitle($empid,$title,$schedule,$quarter,$tdata->title,$sdate,$edate)->result() as $row){
                                ?>
                                    <td class="align_center"><?=$row->title?></td>  <!-- LOANS -->
                                <?
                                }
                            }else{
                            ?>
                                <td class="align_center">0</th>                 <!-- LOANS -->
                            <?
                            }
                        }
                        ?>
                        
                        <!-- OTHER Deductions -->
                        <?
                        foreach($this->payrolloptions->deducttitle('','code_deduction','SHOW',$schedule,$quarter,'',$sdate,$edate)->result() as $tdata){
                            if($this->payrolloptions->deducttitle($empid,'amount','SHOW',$schedule,$quarter,$tdata->title,$sdate,$edate)->num_rows() > 0){
                                foreach($this->payrolloptions->deducttitle($empid,'amount','SHOW',$schedule,$quarter,$tdata->title,$sdate,$edate)->result() as $row){
                                ?>
                                    <td class="align_center"><?=$row->title?></td>  <!-- OTHER Deductions -->
                                <?
                                }
                            }else{  
                            ?>
                                <td class="align_center">0</th>                 <!-- OTHER Deductions -->
                            <?
                            }
                        }
                            ?>
                            
                        <!-- DTR Deductions -->
                        <td class="align_center"><?=$this->payroll->tardydeduct($empid,$schedule,$quarter,$sdate,$edate);?></td>  <!-- Tardy DTR DEDUCTIONS -->
                        <td class="align_center"><?=$this->payroll->absentdeduct($empid,$schedule,$quarter,$sdate,$edate);?></td>  <!-- Excess Absents DTR DEDUCTIONS -->
                    </tr>
                <?
                }
            ?>
    </tbody>
</table>
</div>
<script>

/*
 * Jquery Functions 
 */
 /*
$("#savebtn").click(function(){
   var sched            =  $("#schedule").val();
   var payrollcutoffdd  =  $("#payrollcutoffdd").val();
   var quarter          =  $("#quarter").val();
   if(sched != "" && payrollcutoffdd != "" && quarter != ""){
        var isConfirmed = confirm("Do you want to save this cut-off?.");
        if(isConfirmed){
           $("#cutoffdeductlist").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
           $.ajax({
            url         : "<?=site_url('payroll_/loadmodelfunc')?>",
            type        : "POST",
            data        : {
                            deductionsd :   "<?=$sdate?>",
                            deductioned :   "<?=$edate?>",
                            schedule    :   sched,
                            quarter     :   quarter,
                            cutoffdate  :   payrollcutoffdd,
                            model       :   "payrolldeductcutoffsave"
                          },
            success     :   function(msg){
                alert(msg);
                location.reload();
            } 
           });
       }else    return false;
   }else{
    alert("Please make sure all fields are not empty..");
    return false;
   }
});
*/
$("#docutoff").click(function(){
    var titlei = titledeductfixed = titleloan = titledeductoth= ""; 
    var income = deductfixed = loans = deductothers = "";
   $(".ititle").each(function(){
    // income   title
    var incomex = 3;
    
    for(incomex ; incomex <= 2+Number("<?=$tincome?>"); incomex++){
        if(titlei != "")    titlei += ":";
        //titlei += $(this).find("th:eq("+incomex+")").text().trim();
        titlei += $(this).find("th:eq("+incomex+")").attr("cincome");
    }
    
    // deduct fixed title
    var deductfx = (incomex+2);
    for(deductfx ; deductfx <= (incomex+1)+Number("<?=$tdeduct?>"); deductfx++){
        if(titledeductfixed != "")    titledeductfixed += ":";
        //titledeductfixed += $(this).find("th:eq("+deductfx+")").text().trim();
        titledeductfixed += $(this).find("th:eq("+deductfx+")").text();
    }
    
    // loan title
    var loanx = deductfx;
    for(loanx ; loanx <= (deductfx+Number("<?=$tloan?>"))-1; loanx++){
        if(titleloan != "")    titleloan += ":";
        //titleloan += $(this).find("th:eq("+loanx+")").text().trim();
        titleloan += $(this).find("th:eq("+loanx+")").attr("cloan");
    }
    
    // other deduct title
    var deductox = loanx;
    for(deductox ; deductox <= (loanx+Number("<?=$tdeductoth?>"))-1; deductox++){
        if(titledeductoth != "")    titledeductoth += ":";
        //titledeductoth += $(this).find("th:eq("+deductox+")").text().trim();
        titledeductoth += $(this).find("th:eq("+deductox+")").attr("cdeduct");
    }
   });
   
   var incomearr    = titlei.split(":");
   var deductarr    = titledeductfixed.split(":");
   var loanarr      = titleloan.split(":");
   var deductoarr   = titledeductoth.split(":");
   
   $(".idata").each(function(){
     
    // income
    var incomex = 4;
    var incrment = 0;
    for(incomex ; incomex <= 3+Number("<?=$tincome?>"); incomex++){
        if(income != "")    income += "/";
        income              += incomearr[incrment]+"=";
        income              += $(this).find("td:eq("+incomex+")").text().trim();
        incrment++;
    }
    
    // deduct fixed
    var deductfx = (incomex+1);
    var incrment = 0;
    for(deductfx ; deductfx <= incomex+Number("<?=$tdeduct?>"); deductfx++){
        if(deductfixed != "")    deductfixed += "/";
        deductfixed              += deductarr[incrment]+"=";
        deductfixed              += $(this).find("td:eq("+deductfx+")").text().trim();
        incrment++;
    }
    
    // loan
    var loanx = deductfx;
    var incrment = 0;
    for(loanx ; loanx <= (deductfx-1)+Number("<?=$tloan?>"); loanx++){
        if(loans != "")         loans += "/";
        loans                   += loanarr[incrment]+"=";
        loans                   += $(this).find("td:eq("+loanx+")").text().trim();
        incrment++;
    }
    
    // other deductions 
    var deductox = loanx;
    var incrment = 0;
    for(deductox ; deductox <= (loanx-1)+Number("<?=$tdeductoth?>"); deductox++){
        if(deductothers != "")         deductothers += "/";
        deductothers                   += deductoarr[incrment]+"=";
        deductothers                   += $(this).find("td:eq("+deductox+")").text().trim();
        incrment++;
    }

     var form_data = {
       eid          :   $(this).find("td:eq(0)").text(), 
       dfrom        :   "<?=$sdate?>",
       dto          :   "<?=$edate?>", 
       schedule     :   "<?=$schedule?>",
       quarter      :   "<?=$quarter?>",
       regularpay   :   $(this).find("td:eq(2)").text(),       
       income       :   income,
       ottime       :   $(this).find("td:eq("+(incomex-1)+")").text(),
       withholding  :   $(this).find("td:eq("+incomex+")").text(),
       deductfixed  :   deductfixed,
       loans        :   loans,
       deductothers :   deductothers,
       tardy        :   $(this).find("td:eq("+deductox+")").text(),
       absents      :   $(this).find("td:eq("+(deductox+1)+")").text(),
       model        :   "<?=$model?>"
     };
     $("#failed,#docutoff").hide();
     $("#success").hide();
        $.ajax({
            url     :   "<?=site_url("payroll_/loadmodelfunc")?>",
            type    :   "POST",
            data    :   form_data,
            success :   function(msg){
                //console.log(msg);
                $("#success").show().html("<b>"+msg+"</b>");
                $("#failed").show();
            }
        });
    income = deductfixed = loans = deductothers = ""; 
   });
   $("#docutoff").show();
});

$("#printcutoff").click(function(){
    var params = "?form=payslip";
       params += "&eid=<?=$employeeid?>";
       params += "&dept=<?=$deptid?>";
       params += "&dfrom=<?=$sdate?>"; 
       params += "&dto=<?=$edate?>";
       params += "&schedule=<?=$schedule?>";
       params += "&quarter=<?=$quarter?>";
       //params += "&view=reports_excel/payslip"; 
       //window.open("<?=site_url("reports_/reportloader")?>"+params,"payslip");
       if("<?=$disableprint?>" == true)
        window.open("<?=site_url("forms/loadForm")?>"+params);
       else
        alert("You must save the cut-off first to proceed in printing..");
  
});

$(".chosen").chosen();
</script>