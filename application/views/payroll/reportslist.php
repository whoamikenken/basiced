<?php
 
/**
 * @author Justin
 * @copyright 2015
 */
 
$dept = $deptid;
$departments = $this->extras->showdepartment();

?>
<div class="content no-search">
<table class="table table-striped table-bordered table-hover" id="dble">
    <thead>
        <?
        if($dept != ""){
        ?>
        <tr>
            <th colspan="11" class="align_center"><?=$departments[$dept]?></td>
        </tr>
        <?
        }
        ?>
        <tr>
            <th class="align_center" height='40'>Employee Id</th>
            <th class="align_center">Name</th>
            <th class="align_center">Regular Pay</th>
            <?foreach($this->payroll->listHeaders('payroll_income_config','description') as $row){?>
                <th class="align_center"><?=$row->description?></th>
            <?}?>
            <?foreach($this->payroll->listhiddenHeaders('employee_deduction','code_deduction') as $row){?>
                <th class="align_center"><?=$row->code_deduction?></th>
            <?}?>            
            <?foreach($this->payroll->listHeaders('payroll_deduction_config','description') as $row){?>
                <th class="align_center"><?=$row->description?></th>
            <?}?>
            <?foreach($this->payroll->listHeaders('payroll_loan_config','description') as $row){?>
                <th class="align_center"><?=$row->description?></th>
            <?}?>
            <th class="align_center">WithHolding Tax</th>
        </tr>
    </thead>
    <tbody>  
    <?foreach($this->payroll->listEmployeeWithSalary($employeeid,$dept)->result() as $data){
        $eid = $data->employeeid;?>
        <tr>
            <td class="align_center"><?=$data->employeeid?></td>
            <td class="align_center"><?=$data->fullname?></td>
            <td class="align_center"><?=$data->semimonthly?></td>
            <?foreach($this->payroll->listHeaders('payroll_income_config','description') as $row){
                $amt = $this->payroll->listAmount('employee_income','amount',$eid,$row->description,false,'code_income');?>
                <td class="align_center"><?=$amt?></td>
            <?}?>
            <?foreach($this->payroll->listhiddenHeaders('employee_deduction','code_deduction') as $row){
                $amt = $this->payroll->listAmount('employee_deduction','amount',$eid,$row->code_deduction,true,'code_deduction');?>
                <td class="align_center"><?=$amt?></td>
            <?}?>
            <?foreach($this->payroll->listHeaders('payroll_deduction_config','description') as $row){
                $amt = $this->payroll->listAmount('employee_deduction','amount',$eid,$row->description,false,'code_deduction');?>
                <td class="align_center"><?=$amt?></td>
            <?}?>
            <?foreach($this->payroll->listHeaders('payroll_loan_config','description') as $row){
                $amt = $this->payroll->listAmount('employee_loan','amount',$eid,$row->description,false,'code_loan');?>
                <td class="align_center"><?=$amt?></td>
            <?}?>
            <td class="align_center"><?=$data->whtax?></td>
        </tr>
    <?}?>
    </tbody>
</table>
</div>
<script>

/*
 * Jquery Functions 
 */
 /*
/*
$("#printlist").click(function(){
    var params = "?form=reportlist";
       params += "&eid=<?=$employeeid?>";
       params += "&dept=<?=$deptid?>";
       //params += "&view=reports_excel/reportslist"; 
       //window.open("<?=site_url("reports_/reportloader")?>"+params,"reports");
        window.open("<?=site_url("forms/loadForm")?>"+params);
});
*/
$("#dble").dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 }
});
$(".chosen").chosen();
</script>