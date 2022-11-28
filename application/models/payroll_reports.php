<?php 
/**
 * @author Max Consul
 * @copyright 2019
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payroll_reports extends CI_Model {

	public function getEmployeeSignatureLists($teachingtype, $schedule, $campus, $quarter, $cutoffstart, $cutoffend){
		$showcampus = "";
		if($campus) $showcampus = " AND a.`campus` = '$campus' ";
		return $this->db->query("SELECT CONCAT(lname, ' ,', fname , ' ,', mname) AS fullname FROM employee a INNER JOIN payroll_computed_table b ON a.employeeid = b.`employeeid` WHERE a.`teachingtype` = '$teachingtype' AND b.`schedule` = '$schedule' AND cutoffstart = '$cutoffstart' AND cutoffend = '$cutoffend' AND quarter = '$quarter' $showcampus ORDER BY a.`fname` ");
	}

	public function getPayrollIncomeConfig(){
		return $this->db->query("SELECT * FROM payroll_income_config")->result_array();
	}

	public function getPayrollDeductionConfig(){
		return $this->db->query("SELECT * FROM payroll_deduction_config")->result_array();
	}

	public function getPayrollLoanConfig(){
		return $this->db->query("SELECT * FROM payroll_loan_config")->result_array();
	}

	public function getEmployeeWithIncome($employeeid="", $teaching_type="", $cutoff_start="", $cutoff_end="", $status=""){
        $where_clause = "";
        if($employeeid) $where_clause .= " AND a.employeeid='$employeeid' ";
        if($teaching_type) $where_clause .= " AND b.teachingtype='$teaching_type' ";
        
        return $this->db->query("SELECT CONCAT(b.lname, ', ', b.fname, ' ', b.mname) AS fullname, b.deptid, c.description AS dept_desc, b.campus, c.description AS campus_desc, b.teachingtype, a.* 
                                 FROM payroll_computed_table a
                                 INNER JOIN employee b ON b.employeeid = a.employeeid
                                 LEFT JOIN code_department c ON c.code = b.deptid
                                 WHERE status = '$status' AND DATE_FORMAT(a.cutoffstart, '%M~~%Y')='$cutoff_start' AND DATE_FORMAT(a.cutoffend, '%M~~%Y')='$cutoff_end' $where_clause
                                 ORDER BY fullname;")->result();
    }

    public function getIncomeConfigTaxable(){
        $config = array();
        $q_payroll_config = $this->db->query("SELECT id, taxable FROM payroll_income_config")->result();

        foreach ($q_payroll_config as $row) $config[$row->id] = $row->taxable;

        return $config;
    }
	
}