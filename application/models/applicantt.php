<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Applicantt extends CI_Model {


	/**
	 * Get list of applicants.
	 *
	 * @param 
	 *
	 * @return StrClass Object
	 */
	function getApplicantList($status="",$applicantStatus=""){
		$where_clause = "";
		if($applicantStatus) {
			// if($applicantStatus == "COMPLETE") $where_clause .= " AND a.status = '$applicantStatus' OR e.app_stat = 'APPROVED'";
			// else $where_clause .= " AND a.status = '$applicantStatus' AND a.applicantId NOT IN (SELECT applicantid FROM applicant_application_status)";
			if($applicantStatus == "COMPLETE") $where_clause .= " AND (a.datehired IS NOT NULL OR a.datehired <> '')";
			else $where_clause .= " AND (a.datehired IS NULL OR a.datehired = '')";
		}
		if($status != ''){
			$where_clause .= " AND a.isactive = '$status'";
		}
		$res = $this->db->query("
				SELECT DISTINCT a.applicantId,positionApplied,c.description AS posdesc,REPLACE(CONCAT(b.LName,', ',b.FName,' ',b.MName), 'Ã‘', 'Ñ') AS fullname,STATUS,d.`description` AS statdesc, dateApplied,a.seqno,a.isactive, a.redtag, a.datehired, c.isteaching as is_teaching
				FROM applicant a
				LEFT JOIN applicant_info b ON a.`applicantId`=b.baseId
				LEFT JOIN code_position c ON c.`positionid`=a.`positionApplied`
				LEFT JOIN code_applicant_status	d ON d.`id`=a.`status` 
				INNER JOIN applicant_application_status e ON e.`applicantid`=b.baseId WHERE 1 $where_clause;
			");
		return $res;
	}

	function getNumberOfAvailableJob(){
		$res = $this->db->query("SELECT COUNT(*) AS jobs FROM code_position WHERE hiring = 'YES'");
		return $res;
	}

	function listOfAvailableJob(){
		$res = $this->db->query("SELECT a.description as title  FROM code_position a INNER JOIN code_position_hiring b ON a.positionid = b.base_id WHERE b.hiring = 'YES' GROUP BY description ASC 
")->result_array();
		return $res;
	}

	function getJobData($word = "", $offset = "", $total = ""){
		$where = "";
		$limit = "";
		if ($word) {
			$where = "AND a.description LIKE '%$word%'";
		}
		if ($offset !== "" && $total != "") {
			$limit = "LIMIT $offset, $total";
		}

		$res = $this->db->query("SELECT a.positionid,a.description as title,DATE_FORMAT(b.hiringtill, '%d-%M-%Y') AS UNTIL,a.experience,b.`file`,b.filename,b.course,b.subject, a.`comment` AS description  FROM code_position a INNER JOIN code_position_hiring b ON a.positionid = b.base_id WHERE b.hiring = 'YES' $where GROUP BY description ASC $limit")->result();
		return $res;
	}

	function syncJobToday(){
		$res = $this->db->query("UPDATE code_position_hiring SET hiring = '(NULL)' WHERE hiringtill < CURDATE()");
	}

	/**
	 * Get applicant ID based on fullname.
	 *
	 * @param String $lname
	 * @param String $fname
	 * @param String $mname
	 *
	 * @return Int
	 */

	function getApplicantIdLogin($lname='', $email='', $positionid=''){
		$applicantId = $where_clause = '';
		if($email) $where_clause .= " AND b.email='$email'";
		if($positionid) $where_clause .= " AND a.positionApplied = '$positionid'";
		$res = $this->db->query("
							SELECT * FROM applicant a
							LEFT JOIN applicant_info b ON a.applicantId=b.baseId 
							WHERE b.lname='$lname' AND a.isactive = 1 $where_clause
						");
		if($res->num_rows() > 0){
			$applicantId = $res->row(0)->applicantId;
		}
		return $applicantId;
	}

	function getApplicantId($lname='', $fname='', $mname='', $email='', $positionid=''){
		$applicantId = $where_clause = '';
		if($email) $where_clause .= " AND b.email='$email'";
		if($positionid) $where_clause .= " AND a.positionApplied = '$positionid'";
		$res = $this->db->query("
							SELECT * FROM applicant a
							LEFT JOIN applicant_info b ON a.applicantId=b.baseId 
							WHERE b.lname='$lname' AND b.fname='$fname' AND b.mname='$mname' $where_clause
						");
		if($res->num_rows() > 0){
			$applicantId = $res->row(0)->applicantId;
		}
		return $applicantId;
	}

	/**
	 * Get applicant status with given applicant Id
	 *
	 * @param String $applicantId
	 *
	 * @return string
	 */
	function getApplicantStatus($applicantId=''){
		$applicantStatus = '';
		$this->db->where("applicantId",$applicantId);
		$res = $this->db->get('applicant');
		if($res->num_rows() > 0){
			$applicantStatus = $res->row(0)->status;
		}
		return $applicantStatus;
	}

	function loadPositionJob($id=''){
		$applicantStatus = "nodata";
		$res = $this->db->query("SELECT document FROM code_position_hiring WHERE base_id = '$id'");
		if($res->num_rows() > 0){
			$applicantStatus = $res->row(0)->document;
		}

		return $applicantStatus;
	}

	/**
	 * Get applicant information with given applicant Id
	 *
	 * @param String $applicantId
	 *
	 * @return StrClass Object
	 */
	function getApplicantPersonalInfo($applicantId=''){
		$this->db->from('applicant');
		$this->db->join('applicant_info','applicant_info.baseId=applicant.applicantId','inner');
		$this->db->where("baseId",$applicantId);
		$res = $this->db->get();

		return $res;
	}

	function getJobClass($positionid=''){
		$return = $this->db->query("SELECT * FROM code_position where positionid = '$positionid'");
		if($return->num_rows() > 0) $return = $return->row()->isteaching;
		if(isset($return) && $return == "YES") return $return;
		else return "no";
	}
	
	/**
	* Save applicable fields for applicant personal information, educational background and trainings.
	*
	* @return Boolean
	*/
	function saveApplicableFieldApplicant($employeeid="", $field="", $value=""){
		$res = $this->db->query("SELECT id FROM applicant_applicable_fields WHERE employeeid='$employeeid'");
		if($res->num_rows() > 0) $res = $this->db->query("UPDATE applicant_applicable_fields SET $field='$value' WHERE employeeid='$employeeid'");
		else                   $res = $this->db->query("INSERT INTO applicant_applicable_fields (employeeid, $field) VALUES ('$employeeid','$value')");
		return $res;
	}


	/**
	 * 
	 *
	 * @param 
	 *
	 * @return StrClass Object
	 */
	function getApprovalStatusSetup($isArray=false,$isOptionSelect=false,$approvalId=''){
		// $this->db->where("baseId",$applicantId);
		$res = $this->db->get('code_applicant_status');

		if($isArray){
			$res_arr = array();
			if($res->num_rows() > 0){
				foreach ($res->result() as $key => $row) {
					$res_arr[$row->id] = array('description'=>$row->description,'foremail'=>$row->foremail,'message'=>$row->message);
				}
			}
			return $res_arr;

		}elseif($isOptionSelect){
			$this->load->model('utils');
			$res_arr = array();
			if($res->num_rows() > 0){
				foreach ($res->result() as $key => $row) {
					$res_arr[$row->code] = $row->description;
				}
			}
			$res_opt = $this->utils->constructOptionSelect($res_arr,'Select Status..');
			return $res_opt;
		}

		return $res;
	}

	/**
	 * 
	 *
	 * @param 
	 *
	 * @return StrClass Object
	 */
	function getDocumentSetup($isArray=false,$isOptionSelect=false,$documentId=''){
		// $this->db->where("baseId",$applicantId);
		$res = $this->db->get('code_applicant_document');

		if($isArray){
			$res_arr = array();
			if($res->num_rows() > 0){
				foreach ($res->result() as $key => $row) {
					$res_arr[$row->code] = array('description'=>$row->description,'isRequired'=>$row->isRequired);
				}
			}
			return $res_arr;

		}elseif($isOptionSelect){
			$this->load->model('utils');
			$res_arr = array();
			if($res->num_rows() > 0){
				foreach ($res->result() as $key => $row) {
					$res_arr[$row->code] = $row->description;
				}
			}
			$res_opt = $this->utils->constructOptionSelect($res_arr,'Select Status..');
			return $res_opt;
		}

		return $res;
	}

	/**
	 * 
	 *
	 * @param 
	 *
	 * @return StrClass Object
	 */
	function getApplicantDocumentSubmitted($isArray=false,$applicantId=''){
		$this->db->where("applicantId",$applicantId);
		$res = $this->db->get('applicant_document_submitted');

		if($isArray){
			$res_arr = array();
			if($res->num_rows() > 0){
				foreach ($res->result() as $key => $row) {
					$res_arr[$row->docId] = array('dateSubmitted'=>$row->dateSubmitted);
				}
			}
			return $res_arr;

		}

		return $res;
	}

	/**
	 * 
	 *
	 * @param 
	 *
	 * @return StrClass Object
	 */
	function saveApplicantDocumentSubmitted($applicantId='',$docs=''){
		$res = $this->db->query("DELETE FROM applicant_document_submitted WHERE applicantId='$applicantId'");

		if($docs && $res){
			$docs_arr = explode('|', $docs);
			if(sizeof($docs_arr) > 0){
				foreach ($docs_arr as $doc) {
					$doc_arr = explode('~u~', $doc);
					if(isset($doc_arr[0]) && isset($doc_arr[1]) && $res){
						if($doc_arr[0] != '' && $doc_arr[1] != ''){
							$res = $this->db->query("INSERT INTO applicant_document_submitted (applicantId,docId,dateSubmitted) VALUES ('$applicantId','{$doc_arr[0]}','{$doc_arr[1]}');");
						}
					}
				}
			}
		}

		return $res;
	}

	function saveNewApplicantStatus($applicantId='',$status=''){
		$res = $this->db->query("UPDATE applicant SET status='$status' WHERE applicantId='$applicantId'");
		if($status == "HIRED"){ ///< insert applicant info to employee table if hired

		}
		return $res;

	}

	function getApplicantName($applicantId){
		$query = $this->db->query("SELECT * FROM applicant_info WHERE baseId = '$applicantId' ");
		if($query->num_rows > 0) return $query->row()->fname;
		else return false;
	}

	function getApplicantEmail($applicantId){
		$query = $this->db->query("SELECT * FROM applicant_info WHERE baseId = '$applicantId' ");
		if($query->num_rows > 0) return $query->row()->email;
		else return false;
	}

	function getLastApplicantid(){
		$query = '';
        $checkLastid = $this->db->query("SELECT MAX(applicantId) AS applicantId FROM applicant");
        if($checkLastid->num_rows() > 0) $query = $this->db->query("SELECT MAX(applicantId) AS applicantId FROM applicant")->row()->applicantId;
        else $query = FALSE;
        return $query;
	}

	function newApplicantId(){
		$datenow = date("Y-m");
	    $datenow = str_replace("-","",$datenow);
	    $last_applicant_id = $this->getLastApplicantid();
        $last_date = substr($last_applicant_id, 1, -7);
        if($last_date == date("Y")){
            $last_applicant_id = substr($last_applicant_id, 7) + 1;
            if(strlen($last_applicant_id) == 1) $last_applicant_id = "0000{$last_applicant_id}";
            if(strlen($last_applicant_id) == 2) $last_applicant_id = "000{$last_applicant_id}";
            if(strlen($last_applicant_id) == 3) $last_applicant_id = "00{$last_applicant_id}";
            if(strlen($last_applicant_id) == 4) $last_applicant_id = "0{$last_applicant_id}";
            $applicantId = "A".$datenow.$last_applicant_id;
        }else{
            $last_applicant_id = "00001";
            $applicantId = "A".$datenow.$last_applicant_id;
        }

        return $applicantId;
	}

	function getApplicantStatusData($id){
		return $this->db->query("SELECT * FROM code_applicant_status WHERE id = '$id' ")->result_array();
	}

	function getApplicantStatusCategory($id){
		return $this->db->query("SELECT * FROM applicant_category WHERE base_id = '$id' ORDER BY idseq ASC ")->result_array();
	}

	function getApplicantDocumentData($code){
		return $this->db->query("SELECT * FROM code_applicant_document WHERE code = '$code' ")->result_array();
	}

/*	function saveApplicantDocuments($data){
		return $this->db->insert("applicant_documents", $data);
	}*/

	function saveApplicantDocuments($data){
		$isexist = $this->db->query("SELECT * FROM applicant_documents WHERE employeeid = '{$data['employeeid']}' AND doc_id = '{$data['doc_id']}' ")->num_rows();
		if($isexist == 0){ 
			return $this->db->insert("applicant_documents", $data);
		}
		else{ 
			$this->db->where("employeeid", $data['employeeid']);
			$this->db->where("doc_id", $data['doc_id']);
			$this->db->set($data);
			return $this->db->update("applicant_documents");
		}
	}

	function saveApplicantApprovalStatus($insert_data){
		return $this->db->insert("code_applicant_status", $insert_data);
	}

	function saveApplicantApprovalCategory($insert_data){
		return $this->db->insert("applicant_category", $insert_data);
	}

	function deleteCategory($id){
		return $this->db->query("DELETE FROM applicant_category WHERE base_id = '$id'");
	}

	function updateApplicantApprovalStatus($update_data, $id){
		$this->db->where("id", $id);
		return $this->db->update("code_applicant_status", $update_data);
	}

	function updateApplicantApprovalcategory($update_data, $id){
		$this->db->where("id", $id);
		return $this->db->update("applicant_category", $update_data);
	}

	function checkDocumentExist($code){
		return $this->db->query("SELECT * FROM code_applicant_document WHERE code = '$code' ")->num_rows();
	}

	function saveDocumentSubmission($insert_data){
		return $this->db->insert("code_applicant_document", $insert_data);
	}

	function updateApplicantDocumentSubmission($update_data, $code){
		$this->db->where("code", $code);
		return $this->db->update("code_applicant_document", $update_data);
	}

	function deleteApprovalStatus($id){
		return $this->db->query("DELETE FROM code_applicant_status WHERE id = '$id' ");
	}

	function deleteApplicantDocs($code){
		return $this->db->query("DELETE FROM code_applicant_document WHERE code = '$code' ");
	}

	function getApplicantSetup($positionid, $code_status="", $viewall=false){
		$type = $where_clause = $curSeq = "";
		// if($code_status) $where_clause = " HAVING id <= '$code_status'";
		if($positionid == "YES") $type = "teaching";
		else $type = "nonteaching";
		$query = $this->db->query("SELECT seqno FROM code_applicant_status WHERE type = '$type' AND id = '$code_status'");
		if($query->num_rows() > 0) $curSeq = $query->row()->seqno;
		if($curSeq) $where_clause = " HAVING seqno <= '$curSeq'";
		if($viewall) $where_clause = '';
		return $this->db->query("SELECT * FROM code_applicant_status WHERE type = '$type' $where_clause ORDER BY seqno ASC")->result_array();
	}

	function getApplicantSequenceApprover($applicantid, $code_status){
		$q_approver = $this->db->query("SELECT * FROM applicant_application_status WHERE applicantid = '$applicantid' AND code_status = '$code_status'");
		if($q_approver->num_rows() > 0) return $q_approver->row()->assigned_head;
		else return false;
	}

	function getApplicantSequenceStatus($applicantid, $code_status){
		$q_approver = $this->db->query("SELECT * FROM applicant_application_status WHERE applicantid = '$applicantid' AND code_status = '$code_status'");
		if($q_approver->num_rows() > 0) return $q_approver->row()->app_stat;
		else return false;
	}

	function getLatestStatus($applicantId){
		$q_appstat = $this->db->query("SELECT * FROM `applicant_application_status` WHERE applicantid = '$applicantId' AND application_status = 'current' ORDER BY TIMESTAMP DESC LIMIT 1 ");
		if($q_appstat->num_rows() > 0) return $q_appstat->row()->code_status;
		else return false;
	}

	function getApplicantSetupById($id){
		return $this->db->query("SELECT * FROM code_applicant_status WHERE id = '$id' ")->result_array();
	}

	function getprereqseqno($positionid){
		$isteaching = $this->db->query("SELECT isteaching FROM code_position WHERE positionid = '$positionid'")->row()->isteaching;
		$isteaching = ($isteaching == 'YES' ? 'teaching' : 'nonteaching');
		return $this->db->query("SELECT seqno FROM code_applicant_status WHERE type = '$isteaching' AND isprerequirements = '1' ")->row()->seqno;
	}

	function getCodeStatusSequence($id){
		return $this->db->query("SELECT * FROM code_applicant_status WHERE id = '$id' ")->row()->seqno;
	}

	function updateApplicantSequence($seqno, $applicantid){
		return $this->db->query("UPDATE applicant SET seqno = '$seqno' WHERE applicantId = '$applicantid' ");
	}

	function saveApplicantStatus($data){
		return $this->db->insert("applicant_application_status", $data);
	}

	function getApplicantCurrentStatus($applicantId, $code_status){
		$data = array();
		$app_categ_arr = $app_categ_list = array();
		$q_applicantstat = $this->db->query("SELECT a.`id`, a.`categ_desc`, b.`app_stat`, b.`app_categ_list` FROM code_applicant_status a LEFT JOIN applicant_application_status b ON a.`id` = b.`code_status` WHERE b.`applicantid` = '$applicantId' AND a.`id` = '$code_status' ");

		if($q_applicantstat->num_rows == 0) $q_applicantstat = $this->db->query("SELECT a.`id`, a.`categ_desc`, b.`app_stat`, b.`app_categ_list` FROM code_applicant_status a LEFT JOIN applicant_application_status b ON a.`id` = b.`code_status` WHERE a.`id` = '$code_status' ");

		if($q_applicantstat->num_rows > 0){
			foreach($q_applicantstat->result_array() as $row){

				if(strpos($row["categ_desc"], '/') !== false) $app_categ_arr = explode("/", $row["categ_desc"]);
				else $app_categ_arr[] = $row["categ_desc"];

				if(strpos($row["app_categ_list"], '/') !== false) $app_categ_list = explode("/", $row["app_categ_list"]);
				else $app_categ_list[] = $row["app_categ_list"];

				foreach($app_categ_arr as $key => $categ_val){
					$data[$row["id"]]["app_categ_list"][$categ_val] = isset($app_categ_list[$key]) ? $app_categ_list[$key] : " ";
				}

				$data[$row["id"]]["app_stat"] = $row["app_stat"];
			}
		}
		return $data;
	}

	function getApplicantSequence($applicantid, $code_status){
		$data["record"] = array();
		$app_categ_arr = $app_categ_list = array();
		$q_applicantstat = $this->db->query("SELECT * FROM applicant_category WHERE base_id = '$code_status' GROUP BY idseq ASC")->result();
		$q_value = $this->db->query("SELECT * FROM applicant_application_status WHERE applicantid = '$applicantid' AND code_status = '$code_status'")->result();
		$categoryData = isset($q_value[0]->app_categ_list)? $q_value[0]->app_categ_list:"";
		$value = explode("~", $categoryData);
		$start = 0;
		foreach ($q_applicantstat as $key) {
			$data["record"][$start][$key->description]  = isset($value[$start])? $value[$start]:"";
			
			$start++;
		}
		return $data;
	}

	public function checkApplication($applicantid){
		return $this->db->query("SELECT * FROM applicant_application_status WHERE applicantId = '$applicantid'")->num_rows();
	}

	public function checkArchivedStatus($applicantId){
		return $this->db->query("SELECT * FROM applicant WHERE applicantId = '$applicantId' AND isactive = '0'")->num_rows();
	}

	public function checkIfSequenceExist($type, $seqno, $rowid){
		return $this->db->query("SELECT * FROM code_applicant_status WHERE type = '$type' AND seqno = '$seqno' AND id != '$rowid' ")->num_rows();
	}

	public function migrateData($appID,$datehired){
		$date_format = date('Y-m-d', strtotime($datehired));
    	$date = substr($date_format, 0, 8);
    	$num = 0;
		$true = true;
		while($true){
			$num++;
			if($num < 10){
				$check = "00".$num;
			}else if($num > 10 && $num < 100){
				$check = "0".$num;
			}else $check = $num;

			$newid = $date.$check;
			if($this->db->query("SELECT * FROM employee WHERE employeeid = '$newid'")->num_rows() == 0){
			$true = false;
			}
		}

		

    	$mobile = $this->db->query("SELECT mobile FROM applicant_info WHERE baseId = '$appID'")->result();
		$stripMobile = str_replace("-", "", preg_replace('/[^A-Za-z0-9\-]/', '', $mobile[0]->mobile)); 
    	$res = $this->db->query("INSERT INTO employee (employeeid,lname,fname,mname,nickname,gender,mobile,landline, email, regionaladdr,provaddr,cityaddr,addr, barangay,zip_code,age,bdate,bplace,height,weight,civil_status,tax_status,positionid,citizenid,religionid,nationalityid,prc,prc_expiration,mother,motheroccu,father,fatheroccu,hospitalized,hospitalizedtxt,operation,operationtxt,operationdate, medhistory,medhistorytxt,dateemployed,dateposition,campusid) 
			SELECT '$newid', lname, fname, mname, nname, gender, '$stripMobile', landline, email, regionaladdr,provaddr,cityaddr,addr,barangay,zip_code,age,bdate,bplace,height,weight,civil_status,tax_status,positionid,citizenid,religionid,nationalityid,prc,prc_expiration,mother,motheroccu,father,fatheroccu,hospitalized,hospitalizedtxt,operation,operationtxt,operationdate,medhistory,medhistorytxt,'$date_format','$date_format','POVEDA'
			FROM applicant_info WHERE applicant_info.`baseId` = '$appID'");
    	if($res){
    		$this->db->query("UPDATE applicant SET isemployee = '1', datehired = '$datehired' WHERE applicantId = '$appID' ");
    		$this->db->query("UPDATE elfinder_file SET name = '$newid' WHERE name = '%$appID%'");
    		$table_list = array("applicant_family" => "employee_family", "applicant_emergencyContact" => "employee_emergencyContact", "applicant_education" => "employee_education", "applicant_subj_competent_to_teach" => "employee_subj_competent_to_teach", "applicant_work_history_unrelated" => "employee_work_history_related" , "applicant_eligibilities" => "employee_eligibilities");
			$dbname = $this->db->database;
			$dbfilename = $this->db->database_files;
			foreach ($table_list as $key => $value) {
				$select = $filename = $content = $mime = '';
				$column_name = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = '$key'")->result();
				foreach ($column_name as $k => $v) {
					if($v->COLUMN_NAME != 'id' && $v->COLUMN_NAME != 'employeeid' && $v->COLUMN_NAME != 'honors' && $v->COLUMN_NAME != 'status'){
						if($select == '') $select .= $v->COLUMN_NAME;
						else $select .= ','.$v->COLUMN_NAME;
					}
				}
				if($key == 'applicant_eligibilities'){
					$applicant_eligibilities = $this->db->query("SELECT id, $select FROM applicant_eligibilities WHERE employeeid = '$appID'")->result();
					if(count($applicant_eligibilities) > 0){
						foreach ($applicant_eligibilities as $el) {
							$tblid = $el->id;
							$this->db->query("INSERT INTO $value (employeeid, status, $select) SELECT '$newid', 'PENDING', $select FROM $key WHERE employeeid = '$appID' AND id = '$tblid'");
							$emptblid = $this->db->insert_id();
							list($filename, $content, $mime) = $this->extensions->getEmployee201Files($key, $tblid, $appID);
							if($filename && $content && $mime){
								$this->db->query("INSERT INTO $dbfilename.employee201_files (base_id, table_name, filename, content, mime, employeeid) VALUES ('$emptblid', '$value', '$filename', '$content', '$mime', '$newid') ");
							}
						}
					}
				}else{
					$this->db->query("INSERT INTO $value (employeeid, status, $select) SELECT '$newid', 'PENDING', $select FROM $key WHERE employeeid = '$appID'");
				}
			}

    		$insert_data = $this->db->query("SELECT * FROM employee WHERE employeeid = '$newid'")->result_array();
    		$account_data = array(        #insert new account
	        "username" => $insert_data[0]['employeeid'],
	        "lastname" => $insert_data[0]['lname'],
	        "firstname" => $insert_data[0]['fname'],
	        "middlename" => $insert_data[0]['mname'],
	        "campus" => 'POVEDA',
	        "password" => md5(strtoupper($insert_data[0]['lname'])),
	        "status" => "ACTIVE",
	        "type" => "EMPLOYEE",
	        "ipadd" => $this->input->ip_address(),
	        "createdby" => $this->session->userdata('username')
	        );
	        $res = $this->employee->addNewEmployeeAccount($account_data);
	        // if(($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" || $_SERVER["HTTP_HOST"] == "poveda-hris.pinnacle.com.ph") && strpos($_SERVER["REQUEST_URI"], 'hris') !== false) $this->postDataToAllCard($insert_data);
    	}
	return $newid;

	}

	public function validateIsEmployee($applicantId){
		$query = $this->db->query("SELECT * FROM applicant WHERE applicantId = '$applicantId' AND isemployee = '1'");
		if($query->num_rows() > 0) return array("isemployee" => 1, "datehired" => $query->row()->datehired);
		else return array("isemployee" => 0);
	}

	public function isRedTag($lname, $fname, $mname){
		$isRedTag = $this->db->query("SELECT baseId from applicant_info a inner join applicant b on a.baseId = b.applicantId where a.lname = '$lname' AND a.fname = '$fname' AND a.mname='$mname' AND b.redtag = 1")->row();
		if($isRedTag) return 1;
		else 0;
	}

	public function getApplicantUploadedDocs($applicantid, $docid){
		$title = $content = $size = $mime = "";
		$docs = $this->db->query("SELECT * FROM applicant_documents WHERE employeeid = '$applicantid' AND doc_id = '$docid' ");
		if($docs->num_rows > 0) return array($docs->row()->title, $docs->row()->content, $docs->row()->size, $docs->row()->mime);
		else return array($title, $content, $size, $mime);
	}

	public function saveSigninApplicant($insert_applicant, $insert_applicant_info){
		$this->db->insert("applicant", $insert_applicant);
		$this->db->insert("applicant_info", $insert_applicant_info);
	}

	public function removeApplicantDoc($where_clause, $data=array()){
		if(!empty($data)){
			extract($data);
			$checkIfHasFiles = $this->db->query("SELECT * FROM applicant_documents WHERE doc_id = '$id'")->num_rows();
			if($checkIfHasFiles == 0){
				if((strpos($id, 'ini') !== false) || (strpos($id, 'pre') !== false)) {
					list($type, $id) = explode('_', $id);
					$this->db->query("DELETE FROM duplicate_requirements WHERE id = '$id' AND ini_or_pre = '$type' ");
				}
			}
		}

		
		return $this->db->delete("applicant_documents", $where_clause);
	}

	// public function saveApplicantFilledForm($table, $insert_data){
	// 	$res = $this->db->insert($table, $insert_data);
	// 	if($res) return $this->db->insert_id();
	// }
	public function saveApplicantFilledForm($table, $insert_data){
		date_default_timezone_set('Asia/Manila');
		$final_file = isset($insert_data['content']) ? $insert_data['content'] : '';
		$filetype = isset($insert_data['mime']) ? $insert_data['mime'] : '';
		$filename = isset($insert_data['filename']) ? $insert_data['filename'] : '';
		$attachment = (isset($insert_data['content']) && isset($insert_data['mime']) && isset($insert_data['filename']) ? "Yes" : "No");
		unset($insert_data['mime']);
		unset($insert_data['content']);
		unset($insert_data['filename']);
		$res = $this->db->insert($table, $insert_data);

		if($res){
			$baseid = $this->db->insert_id();
			$dbname = $this->db->database_files;
			$employeeid = $insert_data['employeeid'];
	        // if($_SERVER["HTTP_HOST"] == "192.168.2.97") $dbname = "PovedahrisFiles";
	        // else if($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" && strpos($_SERVER["REQUEST_URI"], 'hristrng') !== false) $dbname = "PovedahrisFiles_Trng";
	        // else if($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" && strpos($_SERVER["REQUEST_URI"], 'hris') !== false) $dbname = "PovedahrisFiles"; 
    		$this->db->query("INSERT INTO $dbname.employee201_files (base_id, table_name, filename, content, mime, employeeid) VALUES ('$baseid', '$table', '$filename', '$final_file', '$filetype', '$employeeid') ");
    		if($this->session->userdata("usertype") == "EMPLOYEE") {
    			$this->db->query("INSERT INTO data_request_details (`baseid`, `table`, `employeeid`, `attachment`) VALUES ('$baseid', '$table', '$employeeid' , '$attachment') ");
    		}
			return $baseid;
		}
	}
	public function checkbox($user, $c1, $c2, $c3, $c4, $c5, $c6, $c7, $c8, $c9, $c10){
		$checks = $this->db->query("SELECT * FROM employee_applicable_fields where employeeid = '$user'");
		if($checks->num_rows == 0 ){
			$check = $this->db->query("INSERT INTO employee_applicable_fields (employeeid, profTraining, profDevelopment, profDevelopmentprog, profGrowth, adminFunctions, comInvolvement, profOrg, speakingEngagement, scholarship, awards) values ('$user', '$c1', '$c2', '$c3', '$c4', '$c5', '$c6', '$c7', '$c8', '$c9', '$c10')");
			return $check;
		}
		else{
			$check = $this->db->query("UPDATE employee_applicable_fields SET profTraining = '$c1', profDevelopment = '$c2', profDevelopmentprog = '$c3', profGrowth = '$c4', adminFunctions = '$c5', comInvolvement = '$c6', profOrg = '$c7', speakingEngagement = '$c8', scholarship = '$c9', awards = '$c10' where employeeid = '$user'");
			return $check;
		}
	}

	public function EducationalCheckbox($user, $c1, $c2, $c3, $c4){
		$checks = $this->db->query("SELECT * FROM employee_applicable_fields where employeeid = '$user'");
		if($checks->num_rows == 0 ){
			$check = $this->db->query("INSERT INTO employee_applicable_fields (employeeid, educBackground, eligibility, sctt, workRelated) values ('$user', '$c1', '$c2', '$c3', '$c4')");
			return $check;
		}
		else{
			$check = $this->db->query("UPDATE employee_applicable_fields SET educBackground = '$c1', eligibility = '$c2', sctt = '$c3', workRelated = '$c4' where employeeid = '$user'");
			return $check;
		}
	}

	public function personalDataCheckbox($user, $c1, $c2){
		$checks = $this->db->query("SELECT * FROM employee_applicable_fields where employeeid = '$user'");
		if($checks->num_rows == 0 ){
			$check = $this->db->query("INSERT INTO employee_applicable_fields (employeeid, children, emergencyContact) values ('$user', '$c1', '$c2')");
			return $check;
		}
		else{
			$check = $this->db->query("UPDATE employee_applicable_fields SET children = '$c1', emergencyContact = '$c2' where employeeid = '$user'");
			return $check;
		}
	}

	public function updateApplicantFilledForm($table, $update_data, $where_clause)
	{
		$final_file = isset($update_data['content']) ? $update_data['content'] : '';
		$filetype = isset($update_data['mime']) ? $update_data['mime'] : '';
		$filename = isset($update_data['filename']) ? $update_data['filename'] : '';
		$base_id = $where_clause['id'];
		unset($update_data['mime']);
		unset($update_data['content']);
		unset($update_data['filename']);

		$dbname = $this->db->database_files;
		$employeeid = $update_data['employeeid'];
        // if($_SERVER["HTTP_HOST"] == "192.168.2.97") $dbname = "PovedahrisFiles";
        // else if($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" && strpos($_SERVER["REQUEST_URI"], 'hristrng') !== false) $dbname = "PovedahrisFiles_Trng";
        // else if($_SERVER["HTTP_HOST"] == "poveda.pinnacle.com.ph" && strpos($_SERVER["REQUEST_URI"], 'hris') !== false) $dbname = "PovedahrisFiles"; 

		$checkIfHasFiles = $this->db->query("SELECT * FROM $dbname.employee201_files WHERE table_name = '$table' AND base_id = '$base_id'");
		if($checkIfHasFiles->num_rows() > 0){
			if($final_file && $filetype) $this->db->query("UPDATE $dbname.employee201_files SET filename = '$filename', content = '$final_file', mime = '$filetype', employeeid = '$employeeid' WHERE base_id='$base_id' AND table_name = '$table'");
		}else{
			$this->db->query("INSERT INTO $dbname.employee201_files (base_id, table_name, filename, content, mime, employeeid) VALUES ('$base_id', '$table', '$filename', '$final_file', '$filetype', '$employeeid')");
		}
		// echo "<pre>"; print_r($this->db->last_query());

		$this->db->where($where_clause);
		$this->db->set($update_data);
		return $this->db->update($table);
	}

	public function birthOrderofChildren($tbl_id, $tbl,$employeeid){
		$border =  1;
		$bcounter = 1;
		$result = $this->db->query("SELECT * FROM $tbl WHERE employeeid='$employeeid' order by birthdate asc")->result_array();
		foreach ($result as $key => $value) {
			$id = $value['id'];
			$this->db->query("UPDATE $tbl set birthorder = '$bcounter' where id = '$id'");
			$bcounter++;
		}
		return true;

	}

	public function applicationStatusHistory($code_status, $applicantid){
		$q_status = $this->db->query("SELECT DISTINCT * FROM applicant_application_status a INNER JOIN code_applicant_status b ON a.`code_status` = b.`id` WHERE code_status = '$code_status' AND applicantid = '$applicantid' ");
		if($q_status->num_rows > 0) return $q_status->result_array();
		else return false;
	}

	///< end of function

	public function getApplicantPosition($applicantid){
		return $this->db->query("SELECT positionApplied FROM applicant WHERE applicantId = '$applicantid' ")->row()->positionApplied;
	}

	public function getPositionDescription($positionid){
		return $this->db->query("SELECT * FROM code_position WHERE positionid = '$positionid' ")->row()->description;
	}

	public function completeApplicantApplication($insert_data){
		return $this->db->insert("applicant_application_status", $insert_data);
	}

	public function isAlreadyOnProcess($applicantid, $id = ""){
		$where_clause = "";
		if($id) $where_clause = " AND code_status = '$id' ";
		return $this->db->query("SELECT * FROM applicant_application_status WHERE applicantid = '$applicantid' AND app_stat = 'APPROVED' ")->num_rows();
	}


	public function updateApplicantInformation($applicantid, $column, $value){
		$this->db->query("UPDATE applicant_info SET $column = '$value' WHERE baseId = '$applicantid' ");
		return 1;
	}

	public function checkIfHasData($lname, $mname, $fname, $email, $positionid){
		$q_applicant = $this->db->query("SELECT * FROM applicant_info a INNER JOIN applicant b ON b.applicantId = a.baseId WHERE a.lname = '$lname' AND a.mname = '$mname' AND a.fname = '$fname' AND a.email='$email' AND b.positionApplied='$positionid'");
		if($q_applicant->num_rows() > 0){
			$applicantid = $q_applicant->row()->applicantId;
			$submitted = $this->db->query("SELECT * FROM applicant_application_status WHERE applicantid = '$applicantid' AND app_stat = 'APPROVED' ")->num_rows();
			return array(true, $q_applicant->row()->seqno, $submitted, 0, $q_applicant->row()->redtag, $q_applicant->row()->isactive, $q_applicant->row()->datehired);
		}else{
			$checkEmail = $this->db->query("SELECT * FROM applicant_info where email = '$email'")->num_rows();
			return array(false, false, false, $checkEmail,0,1, 0);
		}
	}

	public function checkIfHasDataLogin($lname, $email, $positionid){
		$q_applicant = $this->db->query("SELECT * FROM applicant_info a INNER JOIN applicant b ON b.applicantId = a.baseId WHERE a.lname = '$lname' AND a.email='$email' AND b.positionApplied='$positionid'");
		if($q_applicant->num_rows() > 0){
			$applicantid = $q_applicant->row()->applicantId;
			$submitted = $this->db->query("SELECT * FROM applicant_application_status WHERE applicantid = '$applicantid' AND app_stat = 'APPROVED' ")->num_rows();
			return array(true, $q_applicant->row()->seqno, $submitted, 0, $q_applicant->row()->redtag, $q_applicant->row()->isactive, $q_applicant->row()->datehired);
		}
	}

	public function getFMname($lname, $email, $positionid){
		$q_applicant = $this->db->query("SELECT * FROM applicant_info a INNER JOIN applicant b ON b.applicantId = a.baseId WHERE a.lname = '$lname' AND a.email='$email' AND b.positionApplied='$positionid'");
		if($q_applicant->num_rows() > 0){
			return array($q_applicant->row()->fname, $q_applicant->row()->mname);
		}
	}

    public function sendSystemEmail($config, $content){

		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");
		$this->email->from($content["from"], $content["from_name"]);
		$this->email->to($content["to"]);
		$this->email->subject($content["subject"]);
		$this->email->message($content["message"]);
		$success = $this->email->send();
		if ($success) {
            // Raise error message
             echo 'Success to send email';
        } else {
            // Show success notification or other things here
           show_error($this->email->print_debugger());
        }
    }

    public function updateApplicantStatus($status, $applicantId){
    	return $this->db->query("UPDATE applicant SET isactive = '$status' WHERE applicantId = '$applicantId' ");
    }

    public function deleteApplication($applicantId){
    	$this->db->query("DELETE FROM applicant WHERE applicantId = '$applicantId'");
    	$this->db->query("DELETE FROM applicant_info WHERE baseId = '$applicantId'");
    	$this->db->query("DELETE FROM applicant_application_status WHERE applicantid = '$applicantId'");
    	$this->db->query("DELETE FROM employee_photo WHERE employeeid = '$applicantId'");
    	return true;
    }

    function tagredflag($applicantid){
    	$appData = $this->db->query("SELECT * from applicant_info where baseId = '$applicantid'")->result_array();
		$lname = $appData[0]['lname'];
		$mname = $appData[0]['mname'];
		$fname = $appData[0]['fname'];
		$email = $appData[0]['email'];
		$appid = $this->db->query("SELECT baseId from applicant_info WHERE lname = '$lname' AND fname = '$fname' AND mname='$mname' AND email='$email'")->result_array();
		foreach ($appid as $value) {
			$baseid = $value['baseId'];
    		$this->db->query("UPDATE applicant SET redtag = 1 WHERE applicantId = '$baseid'");
		}
		return true;
    }

    function redFlagRemarks($applicantId, $remark){
    	return $this->db->query("UPDATE applicant SET redTagRemarks = '$remark' WHERE applicantId = '$applicantId'");
    }

    public function getNextApplicantStatus($code_status){
    	$q_status = $this->db->query("SELECT * FROM code_applicant_status WHERE id = '$code_status' ");
    	if($q_status->num_rows() > 0){
    		$seqno = $q_status->row()->seqno;
    		$type = $q_status->row()->type;
    		$seqno+=1;
    		$q_next_status = $this->db->query("SELECT * FROM code_applicant_status WHERE seqno = '$seqno' AND type = '$type'");
    		if($q_next_status->num_rows() > 0) return $q_next_status->row()->id;
    		else return false;
    	}else{
    		return false;
    	}
    }

    public function getLastSequence($code_status='', $positionid=''){
    	if($positionid){
    		if($positionid == "YES") $type = "teaching";
			else $type = "nonteaching";
			$lastseq = $this->db->query("SELECT id FROM code_applicant_status WHERE type='$type' ORDER BY seqno DESC LIMIT 1");
    		if($lastseq->num_rows() > 0) return $lastseq->row()->id;
    		else return false;
    	}else{
    		$q_status = $this->db->query("SELECT * FROM code_applicant_status WHERE id = '$code_status' ");
	    	if($q_status->num_rows() > 0){
	    		$type = $q_status->row()->type;
	    		$lastseq = $this->db->query("SELECT id FROM code_applicant_status WHERE type='$type' ORDER BY seqno DESC LIMIT 1");
	    		if($lastseq->num_rows() > 0) return $lastseq->row()->id;
	    		else return false;
	    	}else{
	    		return false;
	    	}
    	}
	    	
    }

    public function getPrevApplicantStatus($code_status){
    	$q_status = $this->db->query("SELECT * FROM code_applicant_status WHERE id = '$code_status' ");
    	if($q_status->num_rows() > 0){
    		$seqno = $q_status->row()->seqno;
    		$type = $q_status->row()->type;
    		$seqno-=1;
    		$q_next_status = $this->db->query("SELECT * FROM code_applicant_status WHERE seqno = '$seqno' AND type = '$type'");
    		if($q_next_status->num_rows() > 0) return $q_next_status->row()->id;
    		else return false;
    	}else{
    		return false;
    	}
    }

    public function updateLastApplicantStatus($code_status=""){
    	$user = $this->session->userdata("username");
    	return $this->db->query("UPDATE applicant_application_status SET head_stat = 'done' WHERE code_status = '$code_status' AND assigned_head = '$user'");
    }

    public function deleteLastApplicantStatus($code_status="", $applicantid=""){
    	$this->db->query("UPDATE applicant_application_status SET application_status = 'history' WHERE applicantid = '$applicantid'");
    	return $this->db->query("DELETE FROM applicant_application_status WHERE code_status = '$code_status' AND applicantid = '$applicantid'");
    }

    public function getApplicantTableCount($table, $appid){
    	return $this->db->query("SELECT * FROM $table where employeeid = '$appid'")->num_rows();
    }

    public function checkRequirements($tnt, $id=""){
    	// echo "<pre>"; print_r($id); die;
    	$inireq = $prereq = $laststep = 0;
    	$isrequirements = $isprerequirements = $islaststep = '';
    	$query = $this->db->query("SELECT * FROM code_applicant_status WHERE type = '$tnt'");
    	if($query->num_rows() > 0){
    		$query = $this->db->query("SELECT SUM(isrequirements) as inireq, SUM(isprerequirements) as prereq, SUM(islaststep) as laststep FROM code_applicant_status WHERE type = '$tnt'");
    		$inireq = $query->row()->inireq;
    		$prereq = $query->row()->prereq;
    		$laststep = $query->row()->laststep;
    		if($id){
    			$query2 = $this->db->query("SELECT isrequirements, isprerequirements, islaststep FROM code_applicant_status WHERE id = '$id'");
    			$isrequirements = $query2->row()->isrequirements;
    			$isprerequirements = $query2->row()->isprerequirements;
    			$islaststep = $query2->row()->islaststep;
    		}
    	}

    	return array($inireq, $prereq, $laststep, $isrequirements, $isprerequirements, $islaststep);
    }

    public function saveEndorsement($data){
    	$this->updateLastApplicantStatus($data['code_status']);
    	unset($data['code_status']);
    	return $this->db->insert("applicant_endorsement", $data);
    }

    public function checkApplicationEndorsement($applicantid){
    	return $this->db->query("SELECT * FROM applicant_endorsement WHERE applicantid = '$applicantid'");
    }

    public function saveSharing($share_to, $app_id){
    	$share_to = implode(',', $share_to);
    	$this->db->query("UPDATE applicant SET share_to = '$share_to' WHERE applicantid = '$app_id'");
    }

} //endoffile