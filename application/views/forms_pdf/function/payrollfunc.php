<?php

/**
 * @author Justin
 * @copyright 2016
 */

// deduction desc
function deductiondesc($deduction = ""){
    $query = mysql_query("SELECT * FROM payroll_deduction_config WHERE id='$deduction'");
    $rs = mysqL_fetch_array($query);
    return ucwords(strtolower($rs['description']));
}
// income desc
function incomedesc($income = ""){
    $query = mysql_query("SELECT * FROM payroll_income_config WHERE id='$income'");
    $rs = mysqL_fetch_array($query);
    return ucwords(strtolower($rs['description']));
}
// loan desc
function loandesc($loan = ""){
    $query = mysql_query("SELECT * FROM payroll_loan_config WHERE id='$loan'");
    $rs = mysqL_fetch_array($query);
    return ucwords(strtolower($rs['description']));

}
// civil status
function civil_status($eid){
    $query = mysql_query("SELECT civil_status FROM employee WHERE employeeid='$eid'");
    $rs    = mysql_fetch_array($query);
    $query = mysql_query("SELECT description FROM code_civil_status WHERE CODE LIKE '%{$rs['civil_status']}'");
    $rs    = mysql_fetch_array($query);
    return ucwords(strtolower($rs['description']));
}
?>