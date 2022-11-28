<?php
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Applicant extends CI_Controller {

	/**
	 * Loads applicant model everytime this class is accessed.
	 */
	function __construct(){
		parent::__construct();
		$this->load->model('applicantt');
	}

	public function index(){
        $data['list'] = $this->applicantt->getJobData();
        $this->load->view('applicant/jobvacancy_material', $data);
	}

    function getListOfJobs(){
      $return = array("err_code"=>0, 'msg'=>"Success.");
      $res = $this->applicantt->listOfAvailableJob();
      if($res) $return = $res;
      else $return = array("err_code"=>2, 'msg'=>"No Available");

      echo json_encode($return);
    }

   function loadJobMaterilize(){
        $total_records_per_page = 4;
        $toks = $this->input->post('toks');
        $word = $toks ? $this->gibberish->decrypt($this->input->post('string'), $toks) :  $this->input->post('string');
        $page_no = $toks ? $this->gibberish->decrypt($this->input->post('page'), $toks) :  $this->input->post('page');

        $offset = ($page_no-1) * $total_records_per_page;
        $previous_page = $page_no - 1;
        $next_page = $page_no + 1;
        $adjacents = "2";
        $data["page_no"] = $page_no;

        $data['record'] = $this->applicantt->getJobData($word, $offset, $total_records_per_page);
        // echo "<pre>";print_r($data);die;
        $data['nolimit'] = $this->applicantt->getJobData($word);
        $total_no_of_pages = ceil(count($data["nolimit"]) / $total_records_per_page);
        $second_last = $total_no_of_pages - 1;
        $data["total_page"] = $total_no_of_pages;

        
        $this->load->view("applicant/listCard", $data);
    }

	function loadApplicantConfigForm(){
        $data['info_type'] = $this->input->post("info_type");
        $data['id'] = $this->input->post("action");
        $view = $this->input->post('view');
        $this->load->view("applicant/$view", $data);
    }

	function loadJobTable(){
        $toks = $this->input->post('toks');
		$word = $toks ? $this->gibberish->decrypt($this->input->post('string'), $toks) :  $this->input->post('string');
        $data['record'] = $this->applicantt->getJobData($word);
        $this->load->view("applicant/tableJob", $data);
    }

	function openDetails(){
        $data['id'] = $this->input->post("id");
        $this->load->view("applicant/pdf", $data);
    }

	function checkjobs(){
        $this->applicantt->syncJobToday();
        echo "successfully sync";
    }

    function loadFile(){
        $file = $this->applicantt->loadPositionJob($this->input->post("id"));
        echo $file;
    }

	/**
	 * Get list of job description for selected position.
	 *
	 * @return String unordered list
	 */
	function getJobDescription(){
		$positionid = $this->input->post('positionid');

		$res = $this->db->query("SELECT a.positionid, b.description
								 FROM code_position a
								 LEFT JOIN code_position_description b ON b.`positionid`=a.`positionid`
								 WHERE a.`positionid`='$positionid'");
		$list = array();
		if($res->num_rows() > 0){
			foreach ($res->result() as $key => $row) {
				array_push($list, $row->description);
			}
		}

		echo $this->constructUnorderedList($list);


	}

	function deleteData(){
      $return = array("err_code"=>0, 'msg'=>"Success.");
      $toks = $this->input->post('toks');
      $data = $this->input->post();
        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       }
      $table = $data["table"];
      $tbl_id = $data["tbl_id"];
      $res = $this->employee->deleteData($data["tbl_id"], $data["table"]);
      if($res) $return = array("err_code"=>0, 'msg'=>"Success.");
      else $return = array("err_code"=>2, 'msg'=>"Failed to delete.");

      echo json_encode($return);
    }
	function deleteDatachildren(){
      $return = array("err_code"=>0, 'msg'=>"Success.");
      $toks = $this->input->post('toks');
      $data = $this->input->post();
        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       }
      $table = $data["table"];
      $tbl_id = $data["tbl_id"];
      $res = $this->employee->deleteDatachildren($data["tbl_id"], $data["table"]);
      $birthorder = $this->applicantt->birthOrderofChildren($tbl_id, $table,$data['employeeid']);
      if($res) $return = array("err_code"=>0, 'msg'=>"Success.");
      else $return = array("err_code"=>2, 'msg'=>"Failed to delete.");

      echo json_encode($return);
    }
    function delete_education(){
      $return = array("err_code"=>0, 'msg'=>"Success.");
      $toks = $this->input->post('toks');
      $data = $this->input->post();
        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       }
      $table = $data["table"];
      $tbl_id = $data["tbl_id"];
      $res = $this->employee->delete_education($data["tbl_id"], $data["table"]);
      if($res) $return = array("err_code"=>0, 'msg'=>"Success.");
      else $return = array("err_code"=>2, 'msg'=>"Failed to delete.");

      echo json_encode($return);
    }

function getJobQualification(){
        $toks = $this->input->post("toks");
		$positionid = $toks ? $this->gibberish->decrypt($this->input->post('positionid'), $toks) :  $this->input->post('positionid');

		$res = $this->db->query("SELECT a.positionid, b.qualification
								 FROM code_position a
								 LEFT JOIN code_position_qualification b ON b.`positionid`=a.`positionid`
								 WHERE a.`positionid`='$positionid'");
		$list = array();
		if($res->num_rows() > 0){
			foreach ($res->result() as $key => $row) {
				array_push($list, $row->qualification);
			}
		}

		echo $this->constructUnorderedList($list);

	}

	function getQualification(){
		$positionid = $this->input->post('positionid');

		$res = $this->db->query("SELECT a.positionid, b.qualification
								 FROM code_position a
								 LEFT JOIN code_position_qualification b ON b.`positionid`=a.`positionid`
								 WHERE a.`positionid`='$positionid'");
		$list = array();
		if($res->num_rows() > 0){
			foreach ($res->result() as $key => $row) {
				array_push($list, $row->qualification);
			}
		}

		echo $this->constructUnorderedList($list);


	}

	/**
	 * Construct unordered list for given array of String.
	 *
	 * @param array $data
	 *
	 * @return String unordered list
	 */
	function constructUnorderedList($data){
		$list = "";
		foreach ($data as $key => $value) {
			$list .= "<li>$value</li>";
		}
		return $list;
	}

	/**
	 * Validate sign in of applicant. Only one application is allowed.
	 *
	 * @return json
	 */
	function validate(){
		///< check info
        $toks = $this->input->post("toks");
        if($toks) $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));

        extract($data);
        // $fname      = $toks? str_replace("%C3%B1","ñ",strtoupper($fname)) :  strtoupper($this->input->post('fname'));
        // $mname      = $toks? str_replace("%C3%B1","ñ",strtoupper($mname)) :  strtoupper($this->input->post('mname'));
        // $lname      = $toks? str_replace("%C3%B1","ñ",strtoupper($lname)) :  strtoupper($this->input->post('lname'));
        // $email      = $toks? str_replace("%40","@",strtoupper($email)) : strtoupper($this->input->post('email'));
		$fname      = strtoupper($toks? str_replace("%C3%B1","ñ",$fname): $this->input->post('fname'));
        $mname      = strtoupper($toks? str_replace("%C3%B1","ñ",$mname): $this->input->post('mname'));
        $lname      = strtoupper($toks? str_replace("%C3%B1","ñ",$lname): $this->input->post('lname'));
        $email      = strtoupper($toks? str_replace("%40","@",$email) : $this->input->post('email'));
		$positionid = $toks? $positionid : $this->input->post('positionid');
		$applicantId = $this->applicantt->getApplicantId($lname, $fname, $mname, $email, $positionid);
		if($applicantId && $this->applicantt->getApplicantStatus($applicantId) != "INC"){
			echo json_encode(array("err_code"=>1, "msg"=>"You already have pending application."));
		}

		$return = array('err_code'=>0, 'msg'=>'');
		$res = $this->db->query("
							SELECT * FROM applicant a
							LEFT JOIN applicant_info b ON a.applicantId=b.baseId
							WHERE lname='$lname' AND fname='$fname' AND mname='$mname' AND email = '$email' AND positionApplied = '$positionid' AND status != 'INC'
						");
		if($res->num_rows() > 0){
			$return = array('err_code'=>2, 'msg'=>'You have already applied for a position.');
		}
		echo json_encode($return);

	}

    function validateLogin(){
        ///< check info
        $toks = $this->input->post("toks");
        if($toks) $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));
        // echo "<pre>"; print_r($data)
        extract($data);
        $lname      = strtoupper($toks? str_replace("%C3%B1","ñ",$password): $this->input->post('password'));
        $email      = strtoupper($toks? str_replace("%40","@",$email) : $this->input->post('email'));
        $positionid = $toks? $positionid : $this->input->post('positionid');
        $applicantId = $this->applicantt->getApplicantId($lname, $email, $positionid);
        if($applicantId && $this->applicantt->getApplicantStatus($applicantId) != "INC"){
            echo json_encode(array("err_code"=>1, "msg"=>"You already have pending application."));
        }

        $return = array('err_code'=>0, 'msg'=>'');
        $res = $this->db->query("
                            SELECT * FROM applicant a
                            LEFT JOIN applicant_info b ON a.applicantId=b.baseId
                            WHERE lname='$lname' AND email = '$email' AND positionApplied = '$positionid' AND status != 'INC'
                        ");
        if($res->num_rows() > 0){
            $return = array('err_code'=>2, 'msg'=>'You have already applied for a position.');
        }
        echo json_encode($return);

    }

	/**
	 * Load corresponding page for applicant sign up.
	 *
	 * @return view
	 */
	function signup(){
		$data 			= array();
		$data['fname'] 	= strtoupper($this->input->post('fname'));
		$data['mname'] 	= strtoupper($this->input->post('mname'));
		$data['lname'] 	= strtoupper($this->input->post('lname'));
		$data['email'] 	= strtoupper($this->input->post('email'));
		$data['positionid']	= $this->input->post('positionid');

		if($data['fname'] && $data['mname'] && $data['lname'] && $data['email'] && $data['positionid']){
			$this->saveSigninApplicant($data['lname'], $data['fname'], $data['mname'], $data['positionid'], $data['email']);
			$this->session->set_userdata("username", $data['positionid']);
			$this->load->view('applicant/applicant_info',$data);
		}
		else
			$this->load->view('applicant/jobvacancy');

	}

    function login(){
        $data           = array();
        $data['lname']  = strtoupper($this->input->post('password'));
        $data['email']  = strtoupper($this->input->post('email'));
        $data['positionid'] = $this->input->post('positionid');
        list($data['fname'], $data['mname']) = $this->applicantt->getFMname($data['lname'],$data['email'] ,$data['positionid']);
        if($data['fname'] && $data['mname'] && $data['lname'] && $data['email'] && $data['positionid']){
            // $this->saveSigninApplicant($data['lname'], $data['fname'], $data['mname'], $data['positionid'], $data['email']);
            $this->session->set_userdata("username", $data['positionid']);
            $this->load->view('applicant/applicant_info',$data);
        }
        else
            $this->load->view('applicant/jobvacancy');

    }


    function saveNewApplicantStatus(){
    	$applicantId 	= $this->input->post('applicantId');
    	$status 		= $this->input->post('status');
    	$message 		= strip_tags( trim( $this->input->post('message') ) );
    	$forEmail 		= $this->input->post('forEmail');
    	$applicant_email = $this->applicantt->getApplicantEmail($applicantId);
    	$res = $this->applicantt->saveNewApplicantStatus($applicantId,$status);

    	var_dump($forEmail && $message && $res);
    	if($forEmail && $message && $res){ ///< send email to applicant
    		// Set SMTP Configuration
		$emailConfig = array(
	            'protocol' => 'smtp',
	            'smtp_host' => 'ssl://smtp.googlemail.com',
	            'smtp_port' => 465,
	            'smtp_user' => 'maxconsul17@gmail.com',
	            'smtp_pass' => 'yokipanget',
	            'mailtype' => 'html',
	            'charset' => 'iso-8859-1'
	        );
	        // Set your email information
	        $from = array(
	            'email' => 'Pinnacle Sample Email',
	            'name' => 'Hyperion'
	        );
	       	$data['message'] = $message;
	       	$applicant_email = strtolower($applicant_email);
	        $to = array($applicant_email);
	        $subject = 'Your gmail subject here';
	      //  $message = 'Type your gmail message here'; // use this line to send text email.
	        // load view file called "welcome_message" in to a $message variable as a html string.
	        $message =  $this->load->view('welcome_message',$data,true);
	        // Load CodeIgniter Email library
	        $this->load->library('email', $emailConfig);
	        // Sometimes you have to set the new line character for better result
	        $this->email->set_newline("\r\n");
	        // Set email preferences
	        $this->email->from($from['email'], $from['name']);
	        $this->email->to($to);
	        $this->email->subject($subject);
	        $this->email->message($message);
	        // Ready to send email and check whether the email was successfully sent
	        if (!$this->email->send()) {
	            // Raise error message
	            show_error($this->email->print_debugger());
	        } else {
	            // Show success notification or other things here
	            echo 'Success to send email';
	        }
    	}


    }

    function saveApplicantDocumentSubmitted(){
    	$applicantId 	= $this->input->post('applicantId');
    	$docs 			= $this->input->post('docs');

    	$res = $this->applicantt->saveApplicantDocumentSubmitted($applicantId,$docs);

    	if($res) echo 1;
    	else 	 echo 0;
    }

    function applicantSignupForm(){
    	$toks = $this->input->post("toks");
        $data   = $this->input->post();
        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
    	$this->load->view('applicant/signup',$data);
    }

    function getApplicantDocuments(){
    	$status = $msg = "";
    	$toks = $this->input->post("toks");
    	$applicantid = $toks ? $this->gibberish->decrypt( $this->input->post("applicantid"), $toks ) : $this->input->post("applicantid");
    	$allowed_types = array("jpg","jpeg","png","pdf","xlsx","csv","docx");

    	$filename = basename($_FILES['files']['name']);
        $file = file_get_contents($_FILES['files']['tmp_name'], $filename);
        $final_file = base64_encode($file);

		$size = $_FILES["files"]["size"] / 1024;
		$mime = Globals::convertMime($_FILES["files"]["type"]);
		if(!in_array($mime, $allowed_types)){
			$status = "failed";
			$msg = "File type is not allowed, please select another file.";
		}elseif(in_array($mime, $allowed_types)){
			$insert_data = array(
				"employeeid" => $applicantid,
				"doc_id" => $toks ? $this->gibberish->decrypt( $this->input->post("doc_id"), $toks ) : $this->input->post("doc_id"),
				"title" => $_FILES["files"]['name'],
				"content" => $final_file,
				"size" => $size,
				"mime" => $_FILES["files"]["type"]
			);

			$res = $this->applicantt->saveApplicantDocuments($insert_data);
			if($res){
				$status = "success";
				$msg = "Successfully sent uploaded documents.";
			}
			else{
				$status = "failed";
				$msg = "Failed to sent uploaded documents. Please try again. ";
			}
		}

    	$response = array("status" => $status, "msg" => $msg);
    	echo json_encode($response);
    }

    function loadApplicantStatusDetail(){
    	$data["id"] = "";
        $data["description"] = "";
        $data["message"] = "";
        $data["seqno"] = "";
        $data["foremail"] = "";
        $data["type"] = "";
        $data["categ_desc"] = "";
        $data["isrequirements"] = "";
        $data["isprerequirements"] = "";
        $data["islaststep"] = "";
        $data["approver_list"] = "";
        $this->load->view('applicant/applicant_status_config', $data);
    }

    function loadApplicantDocumentDetail(){
    	$data["code"] = "";
        $data["description"] = "";
        $data["isRequired"] = "";
        $this->load->view('applicant/applicant_document_config', $data);
    }

    function validateApplicantApprovalStatus(){
    	$toks = $this->input->post("toks");
        $approver_list = $this->input->post("approver_list");
    	if($toks){
    		$data = array();
    		foreach ($this->input->post() as $key => $value) {
    			$data[$key] = $this->gibberish->decrypt($value, $toks);
    		}
    	}else{
    		$data = $this->input->post();
    	}
    	unset($data["toks"]);
        $data['approver_list'] = implode(',',$approver_list);
    	if($data["action"] == "add"){
    		unset($data["action"]);
    		$res = $this->applicantt->saveApplicantApprovalStatus($data);
    	}else{
    		unset($data["action"]);
    		$res = $this->applicantt->updateApplicantApprovalStatus($data, $data["id"]);
    	}
    	echo $res;
    }

    function deleteApplicantCategory(){
    	$toks = $this->input->post("toks");
    	$baseid = $toks ? $this->gibberish->decrypt( $this->input->post("base_id"), $toks ): $this->input->post("base_id");
		$this->applicantt->deleteCategory($baseid);
    	echo "true";
    }

    function validateApplicantCategory(){
    	$toks = $this->input->post("toks");
    	if($toks){
    		$data = array();
    		foreach ($this->input->post() as $key => $value) {
    			$data[$key] = $this->gibberish->decrypt($value, $toks);
    		}
    	}else{
    		$data = $this->input->post();
    	}
    	unset($data["toks"]);
		$res = $this->applicantt->saveApplicantApprovalCategory($data);
    	echo $res;
    }

    function updateApplicantCategory(){
    	$data = $this->input->post();
		$res = $this->applicantt->updateApplicantApprovalcategory($data,$this->input->post("id"));
    	echo $res;
    }

    function validateDocumentSubmission(){
    	$data = $this->input->post();
    	if($data["action"] == "add"){
    		$isexist = $this->applicantt->checkDocumentExist($data["code"]);
    		if($isexist){
    			echo false; die;
    		}
    		unset($data["action"]);
    		$res = $this->applicantt->saveDocumentSubmission($data);
    	}else{
    		unset($data["action"]);
    		$res = $this->applicantt->updateApplicantDocumentSubmission($data, $data["code"]);
    	}

    	echo $res;
    }

    function manageApplicantStatus(){
    	$toks = $this->input->post("toks");
    	$id = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) : $this->input->post("id");
    	$records = $this->applicantt->getApplicantStatusData($id);
    	$category = $this->applicantt->getApplicantStatusCategory($id);
    	$data = array();
    	foreach($records as $row){
    		$data["id"] = $row["id"];
	        $data["description"] = $row["description"];
	        $data["message"] = $row["message"];
	        $data["seqno"] = $row["seqno"];
	        $data["foremail"] = $row["foremail"];
	        $data["type"] = $row["type"];
	        $data["isrequirements"] = $row["isrequirements"];
	        $data["isprerequirements"] = $row["isprerequirements"];
            $data["approver_list"] = $row["approver_list"];
            $data["islaststep"] = $row["islaststep"];
	        $data["categ_desc"] = $category;
    	}
    	$data["id"] = $id;
    	$this->load->view('applicant/applicant_status_config', $data);
    }

    function manageApplicantDocument(){
    	$code = $this->input->post("code");
    	$tag = $this->input->post("tag");
    	$records = $this->applicantt->getApplicantDocumentData($code);
    	$data = array();
    	foreach($records as $row){
    		$data["code"] = $row["code"];
	        $data["description"] = $row["description"];
	        $data["isRequired"] = $row["isRequired"];
    	}

    	$this->load->view('applicant/applicant_document_config', $data);
    }

    function deleteApprovalStatus(){
    	$toks = $this->input->post("toks");
    	$id = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) : $this->input->post("id");
    	$res = $this->applicantt->deleteApprovalStatus($id);
    	echo $res;
    }

    function deleteApplicantDocs(){
    	$code = $this->input->post("code");
    	$res = $this->applicantt->deleteApplicantDocs($code);
    	echo $res;
    }

    function saveApplicantToEmployee(){
    	$toks = $this->input->post("toks");
    	$appID = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) : $this->input->post("id");
    	$datehired = $toks ? $this->gibberish->decrypt( $this->input->post("datehired"), $toks ) : $this->input->post("datehired");
    	$res = $this->applicantt->migrateData($appID,$datehired);
    	$this->saveToAllCardNewEmployee($res);
    	echo $res;
    }

    function saveToAllCardNewEmployee($employeeid){
		$api_url = Globals::apiUrl()."/person/addemployee";
		$empinfo = array(
			"PersonType" => "E",
			"IDNumber"  => $employeeid,
			"FirstName" => $this->extensions->getEmployeeFname($employeeid),
			"MiddleName" => $this->extensions->getEmployeeMname($employeeid),
			"LastName" => $this->extensions->getEmployeeLname($employeeid),
			"CampusName" => "Pinnacle Technologies Inc.",
			"DepartmentName" => "Administrative Support Sevices",
			"PositionName" => "Head, Administrative Support Services"
		);

		$empinfo = json_encode($empinfo);
		$token = $this->extensions->getPostmanToken();
		$access_token = "Bearer ".$token;
		$headers = array(
			'Content-type: application/json',
			'Authorization: '.$access_token,
		);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1 );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $empinfo);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    }

    function saveApplicantStatus(){
    	$response = array();
    	$data = $this->input->post();
    	$toks = $this->input->post("toks");
    	if($toks){
			foreach ($this->input->post() as $key => $value) {
				$data[$key] = $this->gibberish->decrypt($value, $toks);
			}
		}
        // echo "<pre>"; print_r($data); die;
		unset($data['toks']);
    	$prev_stat = $this->applicantt->deleteLastApplicantStatus($data["code_status"],$data["applicantid"]);
    	$data["changedby"] = $this->session->userdata("fullname");
        $data['application_status'] = 'current';
      	foreach($data as $key => $val) if(!$val) unset($data[$key]);
    	$result = $this->applicantt->saveApplicantStatus($data);
    	if($result){
            $response["msg"] = "Successfully saved applicant status";
            if($data['app_stat'] == "NOT RECOMMENDED"){
                $this->applicantt->updateApplicantStatus(0, $data["applicantid"]);
            }else{
                $this->applicantt->updateApplicantStatus(1, $data["applicantid"]);
            }
        }
    	else{
            $response["msg"] = "Failed to saved applicant status";
        }

    	echo json_encode($response);
    }

    function SendMail(){
    	$response = array();
    	$data = $this->input->post();
    	$toks = $this->input->post("toks");
    	if($toks){
			foreach ($this->input->post() as $key => $value) {
				$data[$key] = $this->gibberish->decrypt($value, $toks);
			}
		}
		unset($data['toks']);
    	$emaildata = array();
    	$applicantdata = $this->applicantt->getApplicantPersonalInfo($data["applicantid"])->result();
    	$emaildata["information"] = explode("/",$data["app_categ_list"]);
    	$email = $applicantdata[0]->email;
    	$emaildata["lname"] = $applicantdata[0]->lname;
    	$emaildata["position"] = $this->applicantt->getPositionDescription($applicantdata[0]->positionid);

    	$message = $this->getEmail($emaildata);

    	$emailConfig = array(
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'khipolito@schools.ph',
            'smtp_pass' => 'ewanko250',
            'mailtype' => 'html',
            'charset' => 'utf-8'
        );
    	$content = array(
            "from"      => 'khipolito@schools.ph',
            "from_name" => 'Poveda',
            "to"        => $email,
            "subject"   => 'Job Application',
            "message"   => $message
        );

    	$result = $this->applicantt->sendSystemEmail($emailConfig, $content);

    	if($result) $response["msg"] = "Successfully Sent Email";
    	else $response["msg"] = "Failed to Send Email";

    	echo json_encode($response);
    }

	public function getEmail($data){
		return $this->load->view("applicant/emailApplicant", $data, TRUE);
	}

    function modifyApplicantStatus(){
    	$toks = $this->input->post('toks');
        if($toks){
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }else{
    		$data = $this->input->post();
        }
        unset($data["toks"]);
    	$seqno = $this->getCodeStatusSequence($data["status"]);
    	$res = $this->applicantt->updateApplicantSequence($seqno, $data["applicantid"]);
    }

    function updateActiveStatus(){
    	$toks = $this->input->post('toks');
        $data = $this->input->post();
        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }else{
    		$data = $this->input->post();
        }
    	echo $this->applicantt->updateApplicantStatus($data["status"], $data["applicantid"]);
    }

    function updateActiveStatusBatch(){
        $toks = $this->input->post('toks');
        $data = $this->input->post();
        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }else{
            $data = $this->input->post();
        }

        $idlist = explode('~', $data['idlist']);
        foreach ($idlist as $key => $id) {
           $this->applicantt->updateApplicantStatus($data['status'], $id);
        }
        echo "done";
    }

    function deleteApplication(){
        $toks = $this->input->post('toks');
        $data = $this->input->post();
        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }else{
            $data = $this->input->post();
        }
        echo $this->applicantt->deleteApplication($data["applicantid"]);
    }

    function deleteApplicationBatch(){
        $toks = $this->input->post('toks');
        $data = $this->input->post();
        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }else{
            $data = $this->input->post();
        }
        $idlist = explode('~', $data['idlist']);
        foreach ($idlist as $key => $id) {
           $this->applicantt->deleteApplication($id);
        }

        echo "done";
    }

    function getCodeStatusSequence($id){
    	return $this->applicantt->getCodeStatusSequence($id);
    }

    function checkIfSequenceExist(){
    	$toks = $this->input->post("toks");
    	$seqno = $toks ? $this->gibberish->decrypt( $this->input->post("seqno"), $toks ) : $this->input->post("seqno");
    	$type =  $toks ? $this->gibberish->decrypt( $this->input->post("type"), $toks ) : $this->input->post("type");
    	$rowid =  $toks ? $this->gibberish->decrypt( $this->input->post("rowid"), $toks ) : $this->input->post("rowid");
    	echo $this->applicantt->checkIfSequenceExist($type, $seqno, $rowid);
    }

	function saveSigninApplicant($lname, $fname, $mname, $positionid, $email){
		$applicantId = "";
		$last_applicant_id = $this->applicantt->getLastApplicantid();
		if(!$this->applicantt->getApplicantId($lname, $fname, $mname, $email, $positionid)){
			$datenow = date("Y-m");
		    $datenow = str_replace("-","",$datenow);
		    if(!$applicantId){
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
		    }

		    $isRedTag = $this->applicantt->isRedTag($lname, $fname, $mname);
		    // echo "<pre>"; print_r($isRedTag); die;

			$insert_applicant = array(
				"applicantId" => $applicantId,
				"positionApplied" => $positionid,
				"seqno" => "0",
				"dateApplied" => date("Y-m-d H:i:s"),
				"redtag" => $isRedTag
			);

			$insert_applicant_info = array(
				"baseId" => $applicantId,
				"lname" => $lname,
				"fname" => $fname,
				"mname" => $mname,
				"email" => $email
			);

			$this->applicantt->saveSigninApplicant($insert_applicant, $insert_applicant_info);
		}
	}

	function loadInitialRequirementTab(){
		$data = $this->input->post();
		$toks = $this->input->post('toks');
        if($toks){
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
        unset($data["toks"]);
		$this->load->view("applicant/info_requirements", $data);
	}

	function loadPreEmploymentRequirementTab(){
		$data = $this->input->post();
		$toks = $this->input->post('toks');
        if($toks){
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
        unset($data["toks"]);
		$this->load->view("applicant/info_prerequirements", $data);
	}

	function removeApplicantDoc(){
		$toks = $this->input->post('toks');
    	$data = $this->input->post();

        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
		$where_clause = array("employeeid" => $data["applicantid"], "doc_id" => $data["id"]);
		$res = $this->applicantt->removeApplicantDoc($where_clause, $data);
		if($res) echo json_encode(array("status" => "success", "msg" => "Successfully remove uploaded documents."));
		else echo json_encode(array("status" => "success", "msg" => "Failed to removed uploaded document."));
	}

	function saveApplicantFilledForm()
	{
		$toks = $this->input->post('toks');
    	$data = $this->input->post();

        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
		$table = $data["table"];
		$tbl_id = $data["tbl_id"];
		$b_order = 0;
		unset($data["table"]);
		unset($data["tbl_id"]);
		if (strpos($table, 'applicant') !== false) {
			if($this->session->userdata("usertype") == "ADMIN") $data["status"] = "APPROVED";
			else $data["status"] = "PENDING";
		}

		if(isset($_FILES['content']['name'])){
            $data['filename'] = basename($_FILES['content']['name']);
            $data['mime'] = $_FILES['content']['type'];
            $data['content'] = file_get_contents($_FILES['content']['tmp_name'], $data['filename']);
            $data['content'] = base64_encode($data['content']);
        }

        $data['modified_by'] = $this->session->userdata('username');
        if($table == "employee_pts_pdp1") $data['is201'] = 'YES';
        if($tbl_id) $data['modified_on'] = date('Y-m-d H:i:s');
        if (strpos($table, 'applicant') !== false) {
            unset($data["modified_by"]);
            unset($data["status"]);
            unset($data["dra_remarks"]);
            unset($data["status"]);
            if(isset($data['modified_on'])) unset($data["modified_on"]);
        }
        // echo "<pre>"; print_r($data); die;
		if(!$tbl_id) $res = $this->applicantt->saveApplicantFilledForm($table, $data);
		else $res = $this->applicantt->updateApplicantFilledForm($table, $data, array("id"=>$tbl_id));
		if($table == "employee_children" || $table =="applicant_children"){
			$b_order = $this->applicantt->birthOrderofChildren($res, $table, $data['employeeid']);
		}
		if($res) echo json_encode(array("tbl_id" => $res, "status"=>"success", "msg"=>"Successfully saved all the filled information", "b_order"=>$b_order, "query" => $this->db->last_query()));
		else echo json_encode(array("status"=>"failed", "msg"=>"Failed to saved all the filled information"));
	}

	function checkbox(){
		$toks = $this->input->post('toks');
        $data = $this->input->post();

        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }

		$check = $this->applicantt->checkbox($data['employeeid'], $data['profTraining'], $data['profDevelopment'], $data['profDevelopmentprog'], $data['profGrowth'], $data['adminFunctions'], $data['comInvolvenent'], $data['profOrg'], $data['speakingEngagement'], $data['scholarship'], $data['awards']);
		echo json_encode($check);
	}

    function personalDataCheckbox(){
        $toks = $this->input->post('toks');
        $data = $this->input->post();

        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }

        $check = $this->applicantt->personalDataCheckbox($data['employeeid'], $data['children'], $data['emergencyContact']);
        echo json_encode($check);
    }

	function EducationalCheckbox(){
		$toks = $this->input->post('toks');
        $data = $this->input->post();

        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
		
		$check = $this->applicantt->EducationalCheckbox($data['employeeid'], $data['educBackground'], $data['eligibility'], $data['sctt'], $data['workRelated']);
		echo json_encode($check);
	}

	function applicationStatusHistory(){
		$toks = $this->input->post('toks');
    	$data = $this->input->post();

        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
		$data["records"] = $this->applicantt->applicationStatusHistory($data["id"], $data["applicantid"]);
		$this->load->view("applicant/status_history", $data);
	}

	function validateIsEmployee(){
		$applicantId = $this->input->post("applicantId");
		echo json_encode($this->applicantt->validateIsEmployee($applicantId));
	}

	function tagasredflag(){
		$applicantId = $this->input->post("applicantId");
		$tag = $this->applicantt->tagredflag($applicantId);
		if($tag) echo 1;
		else echo 0;
	}

	function redFlagRemarks(){
		$applicantId = $this->input->post("applicantId");
		$remark = $this->input->post("remark");
        $tag = $this->applicantt->tagredflag($applicantId);
		$addRemark = $this->applicantt->redFlagRemarks($applicantId, $remark);
	}

	function submitFormApplication(){
		$toks = $this->input->post("toks");
		$applicantid = $toks ? $this->gibberish->decrypt( $this->input->post("applicantid"), $toks ) : $this->input->post("applicantid");
        $isArchive = $this->applicantt->checkArchivedStatus($applicantid);
        if($isArchive == 1){
            $this->applicantt->updateApplicantStatus(1, $applicantid);
            echo "isactive";
        }else{
            $positionid = $this->applicantt->getApplicantPosition($applicantid);
            $insert_data = array(
                "applicantid" => $applicantid,
                "code_status" => 80,
                "app_stat" => "APPROVED"
            );
            echo $this->applicantt->completeApplicantApplication($insert_data);
        }
    		
	}

	function updateApplicantInformation(){
		$toks = $this->input->post("toks");
		$column = $toks ? $this->gibberish->decrypt( $this->input->post("column"), $toks ) : $this->input->post("column");
		$applicantId = $toks ? $this->gibberish->decrypt( $this->input->post("applicantId"), $toks ) : $this->input->post("applicantId");
		if($column != "cur_email" && $column != "email") $value = $toks ? strtoupper($this->gibberish->decrypt( $this->input->post("value"), $toks )) : strtoupper($this->input->post("value"));
		else $value = $toks ? $this->gibberish->decrypt( $this->input->post("value"), $toks ) : $this->input->post("value");
		$res = $this->applicantt->updateApplicantInformation($applicantId, $column, $value);
		echo $res;
	}

    function updateCheckBoxApplicant(){
        $toks = $this->input->post("toks");
        $column = $toks ? $this->gibberish->decrypt( $this->input->post("column"), $toks ) : $this->input->post("column");
        $applicantId = $toks ? $this->gibberish->decrypt( $this->input->post("applicantId"), $toks ) : $this->input->post("applicantId");
        $value = $toks ? $this->gibberish->decrypt( $this->input->post("value"), $toks ) : $this->input->post("value");
        $this->applicantt->saveApplicableFieldApplicant($applicantId, $column, $value);

    }

	function checkIfHasData(){
		$toks = $this->input->post("toks");
        if($toks) $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));
        extract($data);
        $fname      = strtoupper($toks? str_replace("%C3%B1","ñ",$fname): $this->input->post('fname'));
        $mname      = strtoupper($toks? str_replace("%C3%B1","ñ",$mname): $this->input->post('mname'));
        $lname      = strtoupper($toks? str_replace("%C3%B1","ñ",$lname): $this->input->post('lname'));
        $email      = strtoupper($toks? str_replace("%40","@",$email) : $this->input->post('email'));
		$positionid = $data["positionid"];
		list($response['isexist'], $response['seqno'], $response['submitted'], $response['email'], $response['redtag'], $response['isactive'], $response['datehired']) = $this->applicantt->checkIfHasData($lname, $mname, $fname, $email, $positionid);
		echo json_encode($response);
	}

    function checkIfHasDataLogin(){
        $toks = $this->input->post("toks");
        if($toks) $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));
        extract($data);
        $lname      = ($toks ? str_replace("%C3%B1","ñ",$password): $this->input->post('password'));
        $email      = strtoupper($toks? str_replace("%40","@",$email) : $this->input->post('email'));
        $positionid = $data["positionid"];
        list($response['isexist'], $response['seqno'], $response['submitted'], $response['email'], $response['redtag'], $response['isactive'], $response['datehired']) = $this->applicantt->checkIfHasDataLogin($lname, $email, $positionid);
        // echo "<pre>"; print_r($this->db->last_query());
        echo json_encode($response);
    }

	function checkApplicationForm(){
		$toks = $this->input->post("toks");
		$applicantid = $toks ? $this->gibberish->decrypt( $this->input->post("applicantid"), $toks ) : $this->input->post("applicantid");
		$applicant_form = Globals::applicantForm();
		foreach($applicant_form as $tbl_name){
			$q_tbl = $this->db->query("SELECT * FROM $tbl_name WHERE employeeid = '$applicantid'")->num_rows();
			if(!$q_tbl){
				echo false;
				return;
			}
		}
	}

	function loadApplicantTable(){
		$toks = $this->input->post("toks");
		if($toks){
			$data['status'] = $this->gibberish->decrypt( $this->input->post("status"), $toks );
			$data['applicantStatus'] = $this->gibberish->decrypt( $this->input->post("applicantStatus"), $toks );
		}
		else{
			$data = $this->input->post();
		}
		// if (!isset($date['applicantStatus'])) $applicantStatus = "";
		echo $this->load->view("applicant/applicant_table", $data);
	}

	function countAvailableJobs(){
		$data = $this->applicantt->getJobData("");
		echo count($data);
	}

	function updateLastApplicantStatus(){
		$toks = $this->input->post("toks");
		$code_status = $toks ? $this->gibberish->decrypt( $this->input->post("code_status"), $toks ) : $this->input->post("code_status");
		echo $this->applicantt->updateLastApplicantStatus($code_status);
	}

	function getApplicantTableCount(){
		$toks = $this->input->post("toks");
		$table = $toks ? $this->gibberish->decrypt( $this->input->post("table"), $toks ) : $this->input->post("table");
		$appid =  $toks ? $this->gibberish->decrypt( $this->input->post("applicantId"), $toks ) : $this->input->post("applicantId");
		echo $this->applicantt->getApplicantTableCount($table, $appid);
	}

    function checkRequirements(){
        $toks = $this->input->post("toks");
        $tnt = $toks ? $this->gibberish->decrypt( $this->input->post("tnt"), $toks ) : $this->input->post("tnt");
        $id = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) : $this->input->post("id");
        list($response['inireq'], $response['prereq'], $response['laststep'],$response['isrequirements'], $response['isprerequirements'], $response['islaststep']) = $this->applicantt->checkRequirements($tnt, $id);
        echo json_encode($response);
    }

    function endorseToAdmin(){
        $toks = $this->input->post('toks');
        $data = $this->input->post();

        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
        $data['endorsed_by'] = $this->session->userdata('username');
        echo $this->applicantt->saveEndorsement($data);
    }

    function updateEndorsedCount(){
        echo $this->employeemod->endorsedApplicant()->num_rows();
    }

    function loginForm(){
        $toks = $this->input->post('toks');
        $data = $this->input->post();

        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
        }
        $this->load->view('applicant/loginform', $data);
    }

    function getDataRequestDetails(){
        $data = $this->input->post();
        extract($data);
        $return = array('err_code'=>0, 'request_date'=>'', 'attachment'=>'');
        $res = $this->db->query("SELECT * FROM data_request_details WHERE `table`='$table' AND `baseid` = '$baseid' AND `employeeid` = '$employeeid'");
        if($res->num_rows() > 0){
            $return = array('err_code'=>1, 'request_date'=>date('F d, Y', strtotime($res->row(0)->request_date)), 'attachment'=>$res->row(0)->attachment);
        }
        echo json_encode($return);
    }

    function manageSharing(){
        $data = $this->input->post();
        extract($data);
        $data['share_to'] = '';
        $res = $this->db->query("SELECT share_to FROM applicant WHERE  `applicantId` = '$app_id'");
        if($res->num_rows() > 0){
            $data['share_to'] = $res->row(0)->share_to;
        }
        echo $this->load->view('applicant/share_to', $data);
    }

    function saveSharing(){
        $data = $this->input->post();
        extract($data);
        $this->applicantt->saveSharing($share_to, $app_id);
    }
}

 //endoffile
