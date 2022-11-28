<?php 
/**
 * @author Max Consul
 * @copyright 2019
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Documents extends CI_Model {

	/**
	* Query to for documents db data
	*
	* @return query result
	*/

	public function loadAvailableDocuments(){
		$q_doc = $this->db->query("SELECT * FROM code_documents ");
		if($q_doc->num_rows > 0) return $q_doc->result_array();
		else return false;
	}

	public function savedDocumentData($code, $description){
		$q_check = $this->db->query("SELECT * FROM code_documents WHERE code = '$code' ");
		if($q_check->num_rows > 0){
			$this->updateDocumentData($code,$description);
			return true;
		}

		$q_insert = $this->db->query("INSERT INTO code_documents (code, description) VALUES ('$code', '$description') ");
		if($q_insert) return true;
		else return false;
	}

	public function updateDocumentData($code, $description){
		return $this->db->query("UPDATE code_documents SET description = '$description' WHERE code = '$code' ");
	}

	public function deleteDocumentData($code){
		return $this->db->query("DELETE FROM code_documents WHERE code = '$code' ");
	}

	public function getDocumentsApplications(){
		return $this->db->query("SELECT * FROM document_app")->result_array();
	}

	public function savedDocApplication($employeeid,$documents,$date_req,$purpose){
		return $this->db->query("INSERT INTO document_app (employeeid, doc_requested, dateapplied, reason, status) VALUES ('$employeeid', '$documents', '$date_req', '$purpose', 'PENDING') ");
	}

	public function loadDocumentRequests($employeeid='', $status='',$datefrom='', $dateto=''){
		$where_clause = "WHERE 1";
		$sort = "";
		if($this->session->userdata("usertype") == "ADMIN") $sort = " DESC";
		else $sort = " ASC";
		if($employeeid) $where_clause .= " AND a.employeeid = '$employeeid'";
		if($status != "ALL" && $status != "") $where_clause .= " AND b.status = '$status'";
		if($datefrom && $dateto) $where_clause .= " AND DATE(b.dateapplied) BETWEEN '$datefrom' AND '$dateto'";
		return $this->db->query("SELECT CONCAT (a.lname, ', ', a.fname, ', ', mname) AS fullname, b.* FROM employee a INNER JOIN document_app b ON b.`employeeid` = a.`employeeid` $where_clause ORDER BY status $sort ")->result_array();
	}

	public function ifHasPendingRequest(){
		return $this->db->query("SELECT * FROM document_app WHERE STATUS = 'PENDING' AND employeeid <> 'undefined' AND employeeid <> ''")->num_rows();
	}

	public function loadDocumentRequestsSorted($status, $datefrom, $dto){
		$where_clause = "";
		if($status && $datefrom && $dto) $where_clause .= " WHERE b.status = '$status' AND dateapplied BETWEEN '$datefrom' AND '$dto' ";
		return $this->db->query("SELECT CONCAT (a.lname, ', ', a.fname, ', ', mname) AS fullname, b.* FROM employee a INNER JOIN document_app b ON b.`employeeid` = a.`employeeid` $where_clause ")->result_array();
	}	

	public function deleteApplyDoc($id){
		return $this->db->query("DELETE FROM document_app WHERE id = '$id' ");
	}

	public function loadApplicationDetails($id){
		return $this->db->query("SELECT CONCAT (a.lname, ', ', a.fname, ', ', mname) AS fullname, b.* FROM employee a INNER JOIN document_app b ON b.`employeeid` = a.`employeeid` WHERE b.id = '$id' ")->result_array();
	}

	public function changeApplicationStatus($app_id, $remarks, $update_stat, $dateclaim, $purpose=""){
		$statusUpdate = "";
		$user = $this->session->userdata('username');
		if($this->session->userdata('usertype') != "EMPLOYEE") $statusUpdate = ", approvedby = '$user',status = '$update_stat'";
		if($purpose) $statusUpdate .= ",reason = '$purpose'";
		$res = $this->db->query("UPDATE document_app SET remarks = '$remarks', date_to_claim = '$dateclaim' $statusUpdate WHERE id = '$app_id' ");
		return $res;
	}

	public function countDocumentApplication($username){
		$where_clause = " WHERE status = 'PENDING' ";
		if($username) $where_clause = " WHERE employeeid = '$username' AND isread != '1' AND `status` IN('APPROVED','DISAPPROVED')";
		return $this->db->query("SELECT * FROM document_app $where_clause ");
	}

	public function getEmployeeStatusHistory($employeeid){
		return $this->db->query("SELECT a.`employeeid`, a.`positionid`, b.`description` AS pos_desc, c.`description` AS stat_desc, dateposition, dateresigned FROM employee_employment_status_history a INNER JOIN code_position b ON b.`positionid` = a.`positionid` INNER JOIN code_status c ON c.`code` = a.`employeestat`WHERE a.employeeid = '$employeeid' ");
	}

	public function getLatestEmployeeStatus($employeeid){
		return $this->db->query("SELECT a.`employeeid`, a.`positionid`, b.`description` AS pos_desc, c.`description` AS stat_desc, dateposition, dateresigned FROM employee a INNER JOIN code_position b ON b.`positionid` = a.`positionid` INNER JOIN code_status c ON c.`code` = a.`employmentstat`WHERE a.employeeid = '$employeeid' ");
	}

	public function getDocumentLists(){
		$query = $this->db->get('document_upload');
		return $query->result(); 
	}

	public function getDocumentList($where_clause=''){
		$query = $this->db->query("SELECT * FROM document_upload $where_clause");
		return $query->result(); 
	}

	public function insertfile($file){
		return $this->db->insert('document_upload', $file);
	}

	public function updatefile($file, $id){
		$description = $file['description'];
		$uploaded_by = $file['uploaded_by'];
		$filename = $file['filename'];
		return $this->db->query("UPDATE document_upload SET description = '$description', uploaded_by = '$uploaded_by', filename = '$filename', date_upload = CURRENT_TIMESTAMP WHERE id = '$id' ");
	}

	public function download($id){
		$query = $this->db->get_where('document_upload',array('id'=>$id));
		return $query->row_array();
	}

	public function updateUploadedForms($id, $description){
		return $this->db->query("UPDATE document_upload SET description = '$description' WHERE id = '$id' ");
	}

	public function deleteUploadedForms($id){
		return $this->db->query("DELETE FROM document_upload WHERE id = '$id' ");
	}

	public function markAsReadApplication($id, $val){
		return $this->db->query("UPDATE document_app SET isread = '$val' WHERE id = '$id' ");
	}
	
}