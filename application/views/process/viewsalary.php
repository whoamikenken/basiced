<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */


#$employeeid = $_POST['employeeid'];
#$cid = $_POST['cid'];
$condi="";$w=" WHERE ";

/** employee */
if($condi) $w = " AND ";
if($employeeid) $condi .= "{$w}a.employeeid='{$employeeid}'";

/** cut-off */
if($condi)$w=" AND ";
if($cid)$condi.="{$w}a.cutoffid='{$cid}'";

$sql = $this->db->query("select concat(b.lname,', ',b.mname,' ',b.lname) as fullname,c.description as incomebase,CONCAT(DATE_FORMAT(d.datefrom,'%M %d, %Y'),' - ',DATE_FORMAT(d.dateto,'%M %d, %Y')) AS `cdate`,a.* 
                         from payroll_summary a 
                         inner join cutoff_summary d on d.id=a.cutoffid
                         inner join employee b on b.employeeid=a.employeeid 
                         inner join code_income_base c on c.income_base=a.income_base{$condi} order by b.lname,b.fname,b.mname");

$sql_basicdeductions = $this->db->query("SELECT DISTINCT b.code_deduction,b.description FROM employee_deduction a 
                                         INNER JOIN deductions b ON b.code_deduction=a.code_deduction AND b._type='BASIC'".
                                        ($cid ?
                                        " INNER JOIN employee_deduction_percutoff c ON c.code_deduction=a.code_deduction AND c.cutoffid='{$cid}'":""));
$bdeductions = array();                                        
for($r=0;$r<$sql_basicdeductions->num_rows();$r++){
    $mrow_deduction = $sql_basicdeductions->row($r);
    $bdeductions[$mrow_deduction->code_deduction] = $mrow_deduction->description;
}
?>
<div class="well-content" style='border: transparent !important;'>
<table class="table table-striped table-bordered table-hover datatable">
    <thead>
        <tr>
            <th>Cut-off Date</th>
            <th>Employee #</th>
            <th>Name</th>
            <th>Type</th>
            <th>Tardy</th>
            <th>Undertime</th>
            <th>Absent</th>
            <th>Regular</th>
<?
foreach($bdeductions as $dcode=>$ddesc){
?>
            <th><?=$dcode?></th>
<?    
}
?>            
            <th>TAX</th>
            <th>INCOME</th>
            <th>DEDUCTION</th>
            <th>SALARY</th>
          </tr>
    </thead>
    <tbody id="employeelist">
<?
if($sql->num_rows()>0){
for($i=0;$i<$sql->num_rows();$i++){
$mrow = $sql->row($i);    
?>
  <tr>
    <td><?=$mrow->cdate?></td>
    <td><?=$mrow->employeeid?></td>
    <td><?=$mrow->fullname?></td>
    <td><?=$mrow->incomebase?></td>
    <td class='align_right'><?=number_format($mrow->total_hours_tardy,2)?></td>
    <td class='align_right'><?=number_format($mrow->total_hours_undertime,2)?></td>
    <td class='align_right'><?=number_format($mrow->total_hours_absent,2)?></td>
    <td class='align_right'><?=number_format($mrow->total_hours_reg,2)?></td>
<?
foreach($bdeductions as $dcode=>$ddesc){
    $basic_deduction = 0;
    $emp_deduct = $this->db->query("select sum(amount) as basic_deduction from employee_deduction_percutoff where employeeid='{$mrow->employeeid}' and cutoffid='{$mrow->cutoffid}' and code_deduction='{$dcode}'");
    if($emp_deduct->num_rows()>0) $basic_deduction = $emp_deduct->row(0)->basic_deduction;
?>
    <td class='align_right'><?=number_format($basic_deduction,2)?></td>
<?    
}
?>    
    <td class='align_right'><?=number_format($mrow->total_tax,2)?></td>
    <td class='align_right'><?=number_format($mrow->total_income,2)?></td>
    <td class='align_right'><?=number_format($mrow->total_deduction,2)?></td>
    <td class='align_right'><?=number_format($mrow->total_salary,2)?></td>
  </tr>
<?    
}
}
?>  
</tbody>
</table>
</div>