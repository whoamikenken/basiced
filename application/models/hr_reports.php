<?php 
/**
 * @author Max Consul
 * @copyright 2019
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Hr_reports extends CI_Model {

    public function getEmployeeDetails($employeeid, $column){
    	$query = $this->db->query("SELECT * FROM employee_attendance_detailed WHERE employeeid = '$employeeid'");
    	if($query->num_rows > 0) return $query->row()->$column;
    	else return false;    	
    }

    public function getLeaveReportSummary($types,$datefrom,$dateto){
    	$query = $this->db->query("SELECT a.`leavetype`, COUNT(*) AS TOTAL, a.`other`, b.`description` FROM leave_request a INNER JOIN code_request_form b ON a.`leavetype` = b.`code_request` OR a.`other` = b.`code_request` WHERE a.`fromdate` BETWEEN '$datefrom' AND '$dateto' OR a.`todate` BETWEEN '$datefrom' AND '$dateto' AND b.`code_request` IN ('$types') AND a.`status` = 'APPROVED' GROUP BY a.`leavetype`,other");
    	return $query->result();		
    }

	public function getAttConfirmed_summary($teachingtype='',$cutoffstart='',$cutoffend='',$payroll_start='',$employeeid='',$campus='',$deptid='', $empstat=''){
		$data = array();

		if($teachingtype == 'teaching'){
			$data = $this->getAttConfirmed_summary_T($teachingtype,$cutoffstart,$cutoffend,$payroll_start,$employeeid,$campus,$deptid, $empstat);
		}elseif($teachingtype == 'nonteaching'){
			$data = $this->getAttConfirmed_summary_NT($teachingtype,$cutoffstart,$cutoffend,$payroll_start,$employeeid,$campus,$deptid, $empstat);
		}
		return $data;
	}

	public function getAttConfirmed_summary_T($teachingtype='',$cutoffstart='',$cutoffend='',$payroll_start='',$employeeid='',$campus='',$deptid='', $empstat=''){
		$data = array();
		$where_clause = "";
        if($campus) $where_clause .= " AND a.campusid='$campus'";
        if($deptid) $where_clause .= " AND a.office='$deptid'"; 
		if($employeeid) $where_clause = " AND a.employeeid = '$employeeid' ";
		if($empstat) $where_clause = " AND a.employmentstat = '$empstat' ";
		$att_q = $this->db->query("
									SELECT c.id AS base_id, a.employeeid as qEmpId,office as qDeptId, CONCAT(lname,', ',fname,' ',mname) AS qFullname, b.description AS qDepartment,DATE(c.`timestamp`) as dateconfirmed, c.*
									                FROM employee a
									                INNER JOIN code_office b ON a.office = b.code 
									                INNER JOIN attendance_confirmed c ON a.employeeid = c.employeeid
									                WHERE (a.dateresigned < a.dateemployed 
										            		OR a.dateresigned = '0000-00-00' 
										            		OR a.dateresigned = '1970-01-01' 
										            		OR a.dateresigned IS NULL 
										            		OR a.dateresigned > '$payroll_start')  
									                	AND c.cutoffstart = '$cutoffstart' AND cutoffend = '$cutoffend' AND a.teachingtype='$teachingtype' $where_clause GROUP BY a.employeeid ORDER BY b.description, qFullname

						");

		if($att_q->num_rows() > 0){
			foreach ($att_q->result() as $key => $row) {
				
				$workdays = round($row->workhours_admin / 8);
				$data[$row->qDeptId][$row->qEmpId] = array('fullname'=>$row->qFullname,'oleave'=>$row->oleave,'vleave'=>$row->vleave,'sleave'=>$row->sleave,'otrest'=>$row->otrest,'absent'=>$row->absent,'otreg'=>$row->otreg,'otsat'=>$row->otsat,'otsun'=>$row->otsun,'othol'=>$row->othol,'lateut'=>$row->lateadmin,'isholiday'=>$row->isholiday,'workdays'=>$workdays,'day_absent'=>$row->day_absent,'hold_status_change'=>$row->hold_status_change);

				$perdept_arr = array();
				$perdept_q = $this->db->query("SELECT work_hours, late_hours, deduc_hours, `type`, aimsdept FROM workhours_perdept WHERE base_id='{$row->base_id}'");
				// echo "<pre>"; print_r($this->db->last_query()); die;

				foreach ($perdept_q->result() as $key_dept => $row_dept) {
					$perdept_arr[$row_dept->aimsdept][$row_dept->type] = array('work_hours'=>$row_dept->work_hours,'late_hours'=>$row_dept->late_hours,'deduc_hours'=>$row_dept->deduc_hours);
				}

				$data[$row->qDeptId][$row->qEmpId]['perdept_arr'] = $perdept_arr;
			}
		}
		return $data;
	}

	public function getAttConfirmed_summary_NT($teachingtype='',$cutoffstart='',$cutoffend='',$payroll_start='',$employeeid='',$campus='',$deptid='', $empstat=''){
		$data = array();
		$where_clause = "";
		if($campus) $where_clause .= " AND a.campusid='$campus'";
        if($deptid) $where_clause .= " AND a.office='$deptid'"; 
		if($employeeid) $where_clause = " AND a.employeeid = '$employeeid' ";
		if($empstat) $where_clause = " AND a.employmentstat = '$empstat' ";
		
		$att_q = $this->db->query("
									SELECT c.id AS base_id, a.employeeid as qEmpId,office as qDeptId, CONCAT(lname,', ',fname,' ',mname) AS qFullname, b.description AS qDepartment,DATE(c.`timestamp`) as dateconfirmed, c.*
									                FROM employee a
									                INNER JOIN code_office b ON a.office = b.code 
									                INNER JOIN attendance_confirmed_nt c ON a.employeeid = c.employeeid
									                WHERE (a.dateresigned < a.dateemployed 
										            		OR a.dateresigned = '0000-00-00' 
										            		OR a.dateresigned = '1970-01-01' 
										            		OR a.dateresigned IS NULL 
										            		OR a.dateresigned > '$payroll_start')  
									                	AND c.cutoffstart = '$cutoffstart' AND cutoffend = '$cutoffend' AND a.teachingtype='$teachingtype' $where_clause GROUP BY a.employeeid ORDER BY b.description, qFullname

						");

		if($att_q->num_rows() > 0){
			foreach ($att_q->result() as $key => $row) {

				$data[$row->qDeptId][$row->qEmpId] = array('fullname'=>$row->qFullname,'oleave'=>$row->oleave,'vleave'=>$row->vleave,'sleave'=>$row->sleave,'otrest'=>$row->otrest,'absent'=>$row->absent,'otreg'=>$row->otreg,'otsat'=>$row->otsat,'otsun'=>$row->otsun,'othol'=>$row->othol,'lateut'=>$row->lateut,'isholiday'=>$row->isholiday,'workdays'=>$row->workdays,'day_absent'=>$row->day_absent,'hold_status_change'=>$row->hold_status_change);
			}
		}
		return $data;
	}

}