<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave_application_ extends CI_Controller {

	private $emp_office = '';
	private $emp_ttype = '';

	/**
	 * Loads leave_application model everytime this class is accessed.
	 */
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		$this->load->model('leave_application');
		$this->load->library('form_data_encryption');
	}

	/**
	 * Loads leave application view passing list of employees under the user's office. 
	 *
	 * @return view
	 */
	function loadApplyLeaveForm(){
		$this->load->model('employee');
		$this->load->model('utils');
		
		$data['office'] 	= $this->employee->getempdatacol('office');
		$data['emplist'] 	= $this->utils->getEmplist($data['office']);
		unset($data['emplist']['']);
		$data['officelist'] 	= $this->utils->getOffice();
		$this->load->view("employeemod/overtimeapply",$data);
	}
	

	/**
	 * Loads leave_application history view with list of employee's leave_application applications.
	 *
	 * @return view
	 */
	function getEmpLeaveHistory(){
        $user 		= $this->session->userdata("username");
        $post 		= $this->input->post();
        $toks 		= $this->input->post("toks");
		$status 	= isset($post['status']) 	? $this->gibberish->decrypt($post['status'], $toks) 	: "";
		$isread 	= isset($post['isread']) 	? $this->gibberish->decrypt($post['isread'], $toks) 	: "";
		$action 	= isset($post['action']) 	? $this->gibberish->decrypt($post['action'], $toks) 	: "";
        $data['leave_list'] = array();

		//result after loading the pages.
		if ($action == "load") {
			$data['leave_list'] = $this->leave_application->getEmpLeaveHistory($user, '', '' ,$isread);	
			// echo $this->db->last_query(); die;
			if (sizeof($data['leave_list']) ==  0 ) {
				$data['leave_list'] = $this->leave_application->getEmpLeaveHistory($user, 'PENDING', '' ,'1');
			}
		}
		//result in history after applying for applicationsaveLeaveApp
		else if($action == "apply"){
			$data['leave_list'] = $this->leave_application->getEmpLeaveHistory($user, 'PENDING', '' ,'1');
			if (sizeof($data['leave_list']) ==  0 ) {
				$data['leave_list'] = $this->leave_application->getEmpLeaveHistory($user, '', '' ,$isread);	
			}
		}
		else{
			$data['leave_list'] = $this->leave_application->getEmpLeaveHistory($user, $status, '' ,$isread);
	    }

        $data['stat'] = $status;

        if(sizeof($data['leave_list']) == 0) $data['leave_list'] = $this->leave_application->getEmpLeaveHistory($user, $status, '' ,$isread);
   
        $this->load->view("employeemod/leave_app/leave_history_emp",$data);
	}


	/**
	 * Loads specific view based on input post $view passing leave details.
	 *
	 * @return view
	 */
	function getLeaveDetails(){
		$this->load->model('utils');
		$post 		= $this->input->post();
		$toks 		= $this->input->post("toks");
		if($toks){
			foreach($post as $key => $val){
				$post[$key] = $this->gibberish->decrypt($val, $toks);
			}
		}
		$view 		= isset($post['view']) 		? $post['view'] 	: "";
		$id 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		$colhead 	= isset($post['colhead']) 	? $post['colhead'] 	: "";
		$colstatus 	=  $colhead ? (substr($colhead,0,-4) . 'status') : '';
		$data 		= $this->leave_application->getLeaveDetails($id,$colstatus);
		// echo "<pre>"; print_r($data); die;
		// $scheddisp  = $this->getStartEndtimePerDay($data['dfrom'],$data['dto'],$data['employeeid']);
		// $data['sched'] = $scheddisp['scheddisp'];
		$data["idkey"]			= $id;
		$data['job'] 	 		= $this->input->post("job");
		// $data['employeeid'] 	= $employeeid;
		$data['colhead'] 		= $colhead;
		$data['isLastApprover'] = $this->input->post("isLastApprover");
		$data['code_request'] = $this->input->post("code_request");
		$data["hrhead"] = $this->leave_application->getLeaveHRApprover($id);
		if(!$data["hrhead"]) $data["hrhead"] = $this->utils->getDeptHead('head',		'HR');
		$filename = $this->form_data_encryption->decryptString($data["filename"]);
		$idlen = strlen($data["base_id"]);
		$data['fileExtension'] = pathinfo($filename, PATHINFO_EXTENSION);
		$data['fileExtension'] = substr($data['fileExtension'], 0, -$idlen);
        $data["imgpath"] = base_url()."uploads/attachments/leave/".$data["filename"].".".$data['fileExtension'];
        if($data) $data = Globals::_array_XHEP($data);
		// echo "<pre>"; print_r($view); echo "</pre>";die;
		$this->load->view("employeemod/leave_app/$view",$data);
	}


	/**
	 * Loads manage leave details view passing list of employees with their leave app details and other approver necessary data.
	 *
	 * @return view
	 */
	function getLeaveAppListToManage(){
		$this->load->model('utils');
		$colhead = $status = $isLastApprover = "";		///< ex. $colhead = "dhead" / "chead" / "hrhead";
		$prevcolstatus = ""	;   ///< column name for head status to check if already approved by previous approver in sequence
		$status 	= $this->input->post('status');
		$datefrom 	= $this->input->post('datefrom');
		$dateto 	= $this->input->post('dateto');
		$deptid 	= $this->input->post('deptid');
		$office 	= $this->input->post('office');
		$isHrHead 	= false;

		$user 			= $this->session->userdata('username');
		// $userdept = $deptid ? $deptid : $this->extensions->getEemployeeCurrentData($user, "deptid");
		// $useroffice = $office ? $office : $this->extensions->getEemployeeCurrentData($user, "office");
		
		$hrhead = $this->utils->getDeptHead('head','HR');
		if($user == $hrhead) $isHrHead = true;
		
		///< for regular employee
		$leave_list = array();

		$form = $this->db->query("SELECT code_request FROM code_request_form");
		if($form->num_rows() > 0){
			foreach ($form->result() as $key => $row) {
				///< --------------------------(condition added for Campus Principal which is ommitted for non teaching)-------------------------------------------
				$leave_list_teaching = $this->getLeaveAppListToManageProcess($row->code_request,$status,$datefrom,$dateto,$user,'teaching',$deptid,$office);
				if(sizeof($leave_list_teaching) > 0) 	$leave_list =  $leave_list + $leave_list_teaching;

				$leave_list_non = $this->getLeaveAppListToManageProcess($row->code_request,$status,$datefrom,$dateto,$user,'nonteaching',$deptid,$office);
				if(sizeof($leave_list_non) > 0) 		$leave_list =  $leave_list + $leave_list_non;

				$leave_list_non = $this->getLeaveAppListToManageProcess($row->code_request.'NON',$status,$datefrom,$dateto,$user,'nonteaching',$deptid,$office);
				if(sizeof($leave_list_non) > 0) 		$leave_list =  $leave_list + $leave_list_non;

				$leave_list_teaching = $this->getLeaveAppListToManageProcess($row->code_request.'HEAD',$status,$datefrom,$dateto,$user,'teaching',$deptid,$office);
				if(sizeof($leave_list_teaching) > 0) 	$leave_list =  $leave_list + $leave_list_teaching;

				$leave_list_non = $this->getLeaveAppListToManageProcess($row->code_request.'HEADNON',$status,$datefrom,$dateto,$user,'nonteaching',$deptid,$office);
				if(sizeof($leave_list_non) > 0) 		$leave_list =  $leave_list + $leave_list_non;
				

			}
		}
		$data['leave_list'] =$leave_list;
		$data['isHrHead'] 	= $isHrHead; 
		$data['status'] = $status; 
		$data['deptid'] = $deptid; 
		$data['office'] = $office; 
		$this->load->view("employeemod/leave_app/leave_history_manage",$data);
	}


	function getLeaveAppListToManageProcess($code_request="VL",$status='',$datefrom='',$dateto='',$user='',$teachingType='',$deptid='',$office=''){
		$colhead = $isLastApprover = "";
		$prevcolstatus = ""	;
		$leave_list = array();
		$arr_aprvl_seq 	= array();
		$hrhead = $this->utils->getDeptHead('head',		'HR');
		$setup 			= $this->leave_application->getAppSequence($code_request);
		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->leave_application->sortApprovalSeq($setup->row(0));
		}

		$aprvl_count = sizeof($arr_aprvl_seq);
		$prevkey 	 = '';
		$arr_apprv = array();
		foreach ($arr_aprvl_seq as $key => $obj) {
			if($obj['head_id'] == $user){
				$colhead = $obj['position'];
				if($aprvl_count == $key && $hrhead == $user) 	 $isLastApprover = true;
				if($key > 1) 				 $prevkey 		 = $key - 1;
				// break;

				if($prevkey && isset($arr_aprvl_seq[$prevkey]['position'])){
					$prevcolstatus = substr($arr_aprvl_seq[$prevkey]['position'],0,-4) . 'status';
				}
				$colstatus =  $colhead ? (substr($colhead,0,-4) . 'status') : '';

				array_push($arr_apprv, array('seq_count'=>$key,'colhead'=>$colhead,'colstatus'=>$colstatus,'prevcolstatus'=>$prevcolstatus,'isLastApprover'=>$isLastApprover,'code_request'=>$code_request));

				$isLastApprover = '';
			}
		}

		foreach ($arr_apprv as $key => $arr) {
			$temp_res = $this->leave_application->getLeaveAppListToManage($user, $arr['colhead'], $arr['colstatus'], $status, $arr['prevcolstatus'], $datefrom, $dateto,$teachingType,$code_request,$arr['seq_count'],$deptid,$office);
			
			if($temp_res->num_rows() > 0){
				foreach ($temp_res->result() as $key => $row) {
					if(!$arr["code_request"]) $arr["code_request"] = $row->type;
					$leave_list[$row->aid] = array('data_list'=>$row,'colhead'=>$arr['colhead'],'colstatus'=>$arr['colstatus'],'prevcolstatus'=>$arr['prevcolstatus'],'isLastApprover'=>$arr['isLastApprover'],'code_request'=>$arr['code_request']);
				}
			}
		}

		return $leave_list;
	}

	/**
	 * Loads approval status view based on leave id passing array of approval details.
	 *
	 * @return view
	 */
	function getApprovalSeqStatus(){
		///< display position, name, status, date updated
		$this->load->model('utils');
		$post 		= $this->input->post();
		$toks 		= $this->input->post("toks");
		$otid 		= isset($post['idkey']) 	? $this->gibberish->decrypt($post['idkey'], $toks) 	: "";
		$position_names = $this->utils->getRequestApprover();
		$data['approver_admin'] = '';
		$arr_aprvl_seq 	= array();
		$setup = $this->leave_application->getAppSequencePerLeave($otid);

		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->sortApprovalSeqPerLeave($setup->row(0));
			if($setup->row(0)->approver_admin) $data['approver_admin'] = $this->utils->getApproverFullName($setup->row(0)->approver_admin);
			// echo "<pre>"; print_r($data);

		}
		foreach ($arr_aprvl_seq as $key => $obj) {
			$arr_aprvl_seq[$key]['position_name'] 	= $position_names[$obj['position']];
			$arr_aprvl_seq[$key]['fullname'] 		= $this->utils->getFullName($obj['head_id']);
		}
		$data['arr_aprvl_seq'] = $arr_aprvl_seq;
		$this->load->view("employeemod/approval_list_overtime",$data);
	}

	/**
	 * Sorts approval heads based on sequence. Stores sorted details in array.
	 *
	 * @param stdClass Object $setup approval sequence details of specific Leave
	 *
	 * @return array
	 */
	function sortApprovalSeqPerLeave($setup){
		$arr_aprvl_seq = array();
		$arr_aprvl_seq[ $setup->dseq ] = array('position'=>'dhead' , 'head_id'=>$setup->dhead ,  'status'=>$setup->dstatus , 'date'=>$setup->ddate);
		$arr_aprvl_seq[ $setup->cseq ] = array('position'=>'chead' , 'head_id'=>$setup->chead,   'status'=>$setup->cstatus , 'date'=>$setup->cdate);
		$arr_aprvl_seq[ $setup->hrseq ] = array('position'=>'hrhead', 'head_id'=>$setup->hrhead, 'status'=>$setup->hrstatus , 'date'=>$setup->hrdate);
		$arr_aprvl_seq[ $setup->cpseq ] = array('position'=>'cphead', 'head_id'=>$setup->cphead, 'status'=>$setup->cpstatus , 'date'=>$setup->cpdate);
		$arr_aprvl_seq[ $setup->dpseq ] = array('position'=>'dphead', 'head_id'=>$setup->dphead, 'status'=>$setup->dpstatus , 'date'=>$setup->dpdate);
		$arr_aprvl_seq[ $setup->fdseq ] = array('position'=>'fdhead', 'head_id'=>$setup->fdhead, 'status'=>$setup->fdstatus , 'date'=>$setup->fddate);
		$arr_aprvl_seq[ $setup->boseq ] = array('position'=>'bohead', 'head_id'=>$setup->bohead, 'status'=>$setup->bostatus , 'date'=>$setup->bodate);
		$arr_aprvl_seq[ $setup->pseq  ] = array('position'=>'phead' , 'head_id'=>$setup->phead,  'status'=>$setup->pstatus , 'date'=>$setup->pdate);
		$arr_aprvl_seq[ $setup->upseq ] = array('position'=>'uphead', 'head_id'=>$setup->uphead, 'status'=>$setup->upstatus , 'date'=>$setup->update);
		//unset 0
		unset($arr_aprvl_seq['0']);
		//ksort
		ksort($arr_aprvl_seq);
		return $arr_aprvl_seq;
	}

	/**
	 * Saves leave_application status change made by approver.
	 *
	 * @return string
	 */
	function saveLeaveStatusChange(){
		$next_approver = "";
		$count = 0;
		$data_array = $this->input->post();
		$res = "";
		foreach($data_array as $base_id => $data_values){

			///< leaveid,status,colhead,isLastApprover -- if last na ,set status
			$leaveid 		= $data_values['leaveid'];
			$base_id 		= $data_values['base_id'];
			$status 		= $data_values['status'];
			$endorse 		= isset($data_values['status_desc']) ? $data_values['status_desc'] : "";
			$colhead 		= $data_values['colhead'];
			$isLastApprover = $data_values['isLastApprover'];
			$code_request 	= $data_values['code_request'];
			$colstatus 		= substr($colhead, 0, -4). 'status';
			$coldate 		= substr($colhead, 0, -4). 'date';
			$remarks		= $data_values['remarks'];
			$notdeduct		= isset($data_values['notdeduct']) ? $data_values['notdeduct'] : "";

			$user = $this->session->userdata('username');

			/*update leave_app_base data*/
			$withpay = isset($data_values['withpay']) ? $data_values['withpay'] : "";
			$datesetfrom = isset($data_values['datesetfrom']) ? $data_values['datesetfrom'] : "";
			$datesetto = isset($data_values['datesetto']) ? $data_values['datesetto'] : "";
			$ndays = isset($data_values['ndays']) ? $data_values['ndays'] : "";
			$l_type = isset($data_values['l_type']) ? $data_values['l_type'] : "";
			if(!$l_type) $l_type = $code_request;

			if(isset($data_values['update'])) $this->leave_application->updateLeaveAppBaseData($withpay,$datesetfrom,$datesetto,$ndays,$l_type,$base_id); 
			/*end*/
			
			$res = $this->leave_application->saveLeaveStatusChange($user,$leaveid,$status,$colstatus,$coldate,$colhead,$isLastApprover,$base_id,$remarks, "", $endorse, $notdeduct);

			if(!$isLastApprover && $status == 'APPROVED'){ ///< get next in sequence with same head id

				$arr_aprvl_seq 	= array();
				$setup 			= $this->leave_application->getAppSequence($code_request);
				if($setup->num_rows() > 0){
					$arr_aprvl_seq = $this->leave_application->sortApprovalSeq($setup->row(0));
				}
				$aprvl_count = sizeof($arr_aprvl_seq);
				$prevkey 	 = '';
				$arr_apprv = array();

				foreach ($arr_aprvl_seq as $key => $obj) {
					$count+=1;
					$isLastApprover_tmp = false;
					$colhead_tmp = $obj['position'];

					if($obj['head_id'] == $user){
						if($aprvl_count == $key) 	 $isLastApprover_tmp = true;
						if($key > 1) 				 $prevkey 		 = $key - 1;

						$colstatus_tmp =  $colhead_tmp ? (substr($colhead_tmp,0,-4) . 'status') : '';
						$coldate_tmp =  $colhead_tmp ? (substr($colhead_tmp,0,-4) . 'date') : '';

						if($isLastApprover_tmp){
							if($arr_aprvl_seq[$prevkey]['head_id'] == $user){
								$prev_colhead = $arr_aprvl_seq[$prevkey]['position'];
								$res_tmp = $this->leave_application->saveLeaveStatusChange($user,$leaveid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$base_id,$remarks,$prev_colhead,$endorse, $notdeduct);
							}
						}else{
							$res_tmp = $this->leave_application->saveLeaveStatusChange($user,$leaveid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$base_id,$remarks,"",$endorse, $notdeduct);

						}

					}

					if($res){
						if(isset($arr_aprvl_seq[$count+1]["head_id"]) && $arr_aprvl_seq[$count+1]["head_id"]) $next_approver = $arr_aprvl_seq[$count+1]["head_id"];
						elseif(isset($arr_aprvl_seq[$count+2]["head_id"]) && $arr_aprvl_seq[$count+2]["head_id"]) $next_approver = $arr_aprvl_seq[$count+2]["head_id"];
					}

				}

			}
		}

		echo json_encode($res);
	}


	/**
	*  Save sick leave application details and upload medical certificate if any.
	*
	* @return string
	*/    

	function saveSickLeaveApp(){
	    $data   = $this->input->post("form_data");
	    $post_data = array();
	    $sched_affected = array();

	    // var_dump(json_decode($data));
	    foreach (json_decode($data) as $key) {
	      $post_data[$key->name] = $key->value;
	      if($key->name == 'sched_affected[]') array_push($sched_affected, $key->value);
	    }

	    $post_data['sched_affected'] = $sched_affected;
	    // var_dump($_FILES['filess']);

	    $forEdit = $byHr = false;
        if(isset($post_data['base_id'])) $forEdit = $post_data['base_id'] != '' ? true : false;

       	$allowApprover	= isset($post_data['allowApprover']) 			? $post_data['allowApprover'] : "";
        $isAdmin	= isset($post_data['isAdmin']) 			? $post_data['isAdmin'] : "";
        if(!$allowApprover && $isAdmin){
        	$byHr = true;
        }

	    $save_ret =  $this->validateSavingLeaveApp($post_data,$forEdit,$byHr); ///< Save application details

	    $medical_msg = ' No medical certificate submitted.';
	    if(isset($save_ret)){
	      if($save_ret['err_code'] == 1 && isset($_FILES['filess'])){

	        $fileName = $_FILES['filess']['name'];
	        $tmpName  = $_FILES['filess']['tmp_name'];
	        $fileSize = $_FILES['filess']['size'];
	        $fileType = $_FILES['filess']['type'];

	        if(isset($save_ret['base_id']) && $_FILES['filess']['error'] == 0 && $fileSize > 0 && $fileSize <= 100000){
	              #image/jpeg, image/png, application/pdf
	              if(in_array($fileType, array("image/jpeg","image/png","application/pdf"))) {
	                 $fp      = fopen($tmpName, 'r');
	                              $content = fread($fp, filesize($tmpName));
	                              $content = addslashes($content);
	                              fclose($fp);
	                $res = $this->db->query("INSERT into leave_app_attach_medicalcert (base_id,file_name,content,file_type) values ('{$save_ret['base_id']}','$fileName','$content','$fileType')"); ///< upload Medical cert
	                if($res) $medical_msg = ' Medical certificate successfully submitted.';
	              }
	        }
	      }
	    }

	    $save_ret['msg'] .= $medical_msg;
	    echo json_encode($save_ret);
	}

	function convertFormDataToArray($formdata){
		$data_arr = array();
		$formdata = explode("&", $formdata);
		foreach($formdata as $row){
			list($key, $value) = explode("=", $row);
			$data_arr[$key] = $value;
		}

		return $data_arr;
	}

	function saveLeaveApp(){
		$data   = $this->input->post();
        $forEdit = false;
        $save_ret = $this->validateSavingLeaveApp($data,$forEdit);
        echo json_encode($save_ret);
	}
	
	/**
	 * Saves new Leave application.
	 *
	 * @return string
	 */
	function validateSavingLeaveApp($data=array(),$forEdit=false){
		$this->load->model("ob_application");
		$this->load->model('extras');
		$this->load->model('utils');

		$return = $msg = $sched_affected = "";
		$filename = $file = $final_file = $size = $mime = $filetype = "";
		$allowed_types = array("jpg","jpeg","png","pdf","xlsx","csv","docx");
		if(isset($_FILES['files']['name'])){
	    	$filename = basename($_FILES['files']['name']);
	        $file = file_get_contents($_FILES['files']['tmp_name'], $filename);
	        $final_file = base64_encode($file);

			$size = $_FILES["files"]["size"] / 1024;
			$mime = Globals::convertMime($_FILES["files"]["type"]);
			$filetype = $_FILES["files"]["type"];
		}

		$formdata   = $this->gibberish->decrypt($this->input->post("formdata"), $data["toks"]);
		$data = Globals::convertFormDataToArray($formdata);
		// echo "<pre>"; print_r($data); die;
		if(isset($data['base_id'])) $forEdit = $data['base_id'] != '' ? true : false;

        $ishalfday = isset($data['ishalfday']) ? 1 : 0;
        if($ishalfday && isset($data['sched_affected[]'])) $sched_affected = $data['sched_affected[]'];

		$base_id_edit	= isset($data['base_id'])		? $data['base_id']							: "";
		$ltype 			= isset($data['ltype']) 		? $data['ltype'] 							: "";
		$tfrom 			= isset($data['timefrom']) 		? $data['timefrom'] 							: "";
		$tto 			= isset($data['timeto']) 		? $data['timeto'] 							: "";
		$othleave 		= isset($data['othleave']) 		? $data['othleave'] 						: "";
		$leave_category = isset($data['catleave']) 		? $data['catleave'] 						: "";
		$leave_category = str_replace("+", " ", $leave_category);
		$datefrom 		= isset($data['datesetfrom']) 	? $data['datesetfrom'] 						: "";
		$dateto 		= isset($data['datesetto']) 	? $data['datesetto'] 						: "";
		$paid 			= isset($data['withpay']) 		? $data['withpay'] 							: "NO";
        $nodays			= isset($data['ndays']) 		? $data['ndays'] 							: 0;
        $dayscount			= isset($data['nodays']) 		? $data['nodays'] 						: 0;
		$reason 		= isset($data['reason'])		? $data['reason']	  	: "";
		$liquidated 		= isset($data['liquidated'])		? $data['liquidated']	  	: "";
		$reason = str_replace("+", " ", $reason);
		$applyBY 		= $this->session->userdata('username');
		$user 			= isset($data['employee']) 		? $data['employee'] 						: $applyBY;
		$category 		= isset($data['category']) 		? $data['category'] 						: "";
		$seminar 		= isset($data['seminar']) 		? $data['seminar'] 							: "";
		$organizer 		= isset($data['organizer'])		? $data['organizer']	  	: "";
		$organizer = str_replace("+", " ", $organizer);
		$venue 		= isset($data['venue'])		? $data['venue']	  	: "";
		$venue = str_replace("+", " ", $venue);
		$location 		= isset($data['location'])		? $data['location']	  	: "";
		$location = str_replace("+", " ", $location);
		$fee 			= isset($data['fee']) 			? $data['fee'] 								: "";
		$transportation 	= isset($data['transportation']) ? $data['transportation'] 					: "";
		$accomodation 	= isset($data['accomodation']) 	? $data['accomodation'] 					: "";
		$others 		= isset($data['others']) 		? $data['others'] 							: "";
		$total 			= isset($data['total']) 		? str_replace(",", "", $data['total']) 							: "";
		$deadline 		= isset($data['deadline']) 		? $data['deadline'] 						: "";
		$title 		= isset($data['title'])		? $data['title']	  	: "";
		$title = str_replace("+", " ", $title);

        ///< sort by teaching type -- different setup per teaching type
		$isAllowApprover = (isset($data["allowApprover"])) ? $data["allowApprover"] : 1;
		$isAdmin	= isset($data['isAdmin']) 			? $data['isAdmin'] : "";
		if(!$isAllowApprover && $isAdmin) $isAllowApprover = 0;
		else $isAllowApprover = 1;
    	$det_res = $this->utils->getSingleTblData('employee',array('teachingType','office'),array('employeeid'=>$user));
    	if($det_res->num_rows() > 0){
    		$this->emp_ttype = $det_res->row(0)->teachingType;
    		$this->emp_office = $det_res->row(0)->office;
    	}
    	if(!$this->emp_ttype) {return array('err_code'=>0,'msg'=>'You have no employee type. Please set teaching or non-teaching.');}

    	if(!$this->emp_office) {return array('err_code'=>0,'msg'=>'Please set your office first.');}

    	if(!$reason) {return array('err_code'=>0,'msg'=>'Reason is required.');}
    	if($ishalfday && strlen($sched_affected) < 5) {return array('err_code'=>0,'msg'=>'Invalid schedule selected.');}
    	if(!$nodays && $paid == "YES") {return array('err_code'=>0,'msg'=>'Insufficient Leave Credits.');}
    	if($paid == "null") {return array('err_code'=>0,'msg'=>'With pay is required.');}

    	$hasfiledLeave = $this->leave_application->hasFiledLeave($user, $datefrom, $dateto, ($ishalfday) ? "true" : "false", $tfrom, $tto);
    	if($hasfiledLeave >= 1 && !$base_id_edit){
    		$msg .= "You already filed a leave on this date!";
			return array('err_code'=>0,'msg'=>$msg);
    	}

    	$hasfiledOb = $this->ob_application->hasFiledOB($user, $datefrom, $dateto, ($ishalfday) ? "true" : "false", $tfrom, $tto);
    	if($hasfiledOb >= 1 && !$base_id_edit){
    		$msg .= "You already filed a ob on this date!";
			return array('err_code'=>0,'msg'=>$msg);
    	}

    	// if(!$final_file && !$base_id_edit) {return array('err_code'=>0,'msg'=>'Please upload a file.');}

        $dhead = $this->utils->getDeptHead('head',		$this->emp_office);	
        $chead = $this->utils->getDeptHead('divisionhead',$this->emp_office);	
        $hrhead = $this->utils->getDeptHead('head',		'HR');
        $isHead = $this->utils->checkIfHead($user);

        if($ltype){
    	    $empcount = 0;
    	    $ltypetemp = $ltype;
    	    $availed = $balance = 0;

    	    ///< check for existing applications
    	    $exist_app = $this->leave_application->checkExistingLeaveApp($user,'APPROVED',$datefrom,$dateto);
    	    // if($exist_app) {return array('err_code'=>0,'msg'=>'You already have approved applications for this date.');}

    	    if($ltype != 'ABSENT' && $paid == "YES"){
	    	    ///< check for balances
	    	    
	    	    if($ltype != "VL" && $ltype != "EL" && $ltype != "PL-SEM" && $ltype != "PL-M" && $ltype != "PL-G"){
	    	    	list($haveCredits,$balance,$credit,$availed) = $this->leave_application->checkLeaveBalance($user,$ltype,$datefrom,$dateto);
	    	    }elseif($ltype == "PL-SEM" || $ltype == "PL-M" || $ltype == "PL-G"){
	    	    	list($haveCredits,$balance,$credit,$availed) = $this->leave_application->checkLeaveBalance($user,$ltype,$datefrom,$dateto);
	    	    }else{
	    	    	list($haveCredits,$balance,$credit,$availed) = $this->leave_application->checkLeaveBalance($user,$ltype,$datefrom,$dateto);

	    	    	if(!$haveCredits && $ltype == 'EL'){
	    	    		$ltypetemp = 'VL';
	    	    		list($haveCredits,$balance,$credit,$availed) = $this->leave_application->checkLeaveBalance($user,$ltypetemp,$datefrom,$dateto);
	    	    	}elseif(!$haveCredits && $ltype == 'VL'){
	    	    		$ltypetemp = 'EL';
	    	    		list($haveCredits,$balance,$credit,$availed) = $this->leave_application->checkLeaveBalance($user,$ltypetemp,$datefrom,$dateto);
	    	    	}
	    	    }

		    	if(!$haveCredits) {return array('err_code'=>0,'msg'=>"You have no leave credits for the given date.");}

		    	if($balance > 0){
		    		if($balance >= $nodays){

		    		}else {return array('err_code'=>0,'msg'=>"Insufficient leave balance.");}
		    	}else {return array('err_code'=>0,'msg'=>"You have no remaining balance.");}
		    }
    	    if($forEdit){
    	    	if($isAllowApprover == 0){
    	    		list($base_id,$empcount_temp,$failed_temp,$return_temp) = $this->saveLeaveAppHRDirectProcess($ltype, $othleave, $datefrom, $dateto, $paid, $nodays, $ishalfday, $sched_affected, $leave_category, $category, $seminar, $organizer, $venue, $location, $fee, $deadline, $title, $transportation, $accomodation, $others, $total, $reason, $user, $ltypetemp,$availed,$balance,$applyBY, $final_file, $size, $filetype, $liquidated, $dayscount);
    	    		$this->leave_application->deleteLeaveRequestForAdminAutoApprove($base_id_edit);
    	    	}else{
	    	    	$base_id = $base_id_edit;
	    	    	$alredyApproved = $this->leave_application->isAlreadyApproved($base_id);
	    	    	if($alredyApproved == 0) {
	    	    		$empcount_temp = $this->leave_application->modifyLeaveDetails($base_id,$paid,$nodays,$ishalfday,$sched_affected,$reason, $final_file, $size, $filetype,$title,$organizer,$venue,$seminar,$location,$dayscount, $ltype);
	    	    	}else if($alredyApproved == 1 || $alredyApproved == 2){
	    	    		return array('err_code'=>0,'msg'=>'This application is already in the approval process.');
	    	    	}else{
	    	    		return array('err_code'=>0,'msg'=>'Application is already approved.');
	    	    	} 
	    	    }

    	    }else{
    	    	if($isAllowApprover == 0){
				    list($base_id,$empcount_temp,$failed_temp,$return_temp) = $this->saveLeaveAppHRDirectProcess($ltype, $othleave, $datefrom, $dateto, $paid, $nodays, $ishalfday, $sched_affected, $leave_category, $category, $seminar, $organizer, $venue, $location, $fee, $deadline, $title, $transportation, $accomodation, $others, $total, $reason, $user, $ltypetemp,$availed,$balance,$applyBY, $final_file, $size, $filetype,$liquidated, $dayscount);
    	    	}else{
	    			list($base_id,$empcount_temp,$failed_temp,$return_temp) = 
	    			$this->saveLeaveAppProcess($ltype, $othleave, $datefrom, $dateto, $paid, $nodays, $ishalfday, $sched_affected, $leave_category, $category, $seminar, $organizer, $venue, $location, $fee, $deadline, $title, $transportation, $accomodation, $others, $total, $reason, $dhead,$chead,$hrhead,$user,$isHead,'',$applyBY, $final_file, $size, $filetype,$liquidated,$dayscount);
    	    	}
    	    }


    		$empcount += $empcount_temp;
    		if(isset($return_temp)) $msg .= $return_temp;

        }else {return array('err_code'=>0,'msg'=>'No leave type selected.');}
        

       
        if($empcount){
			if(!$base_id_edit) $msg .= "$empcount employee(s) successfully applied.";
			else $msg .= "Successfully updated leave application.";
			return array('err_code'=>1,'msg'=>$msg,'base_id'=>$base_id);

		}else{ 			
			$msg .= "Failed to save application.";
			return array('err_code'=>0,'msg'=>$msg);
		}

    }

    function saveLeaveAppHRDirectProcess($ltype, $othleave, $datefrom, $dateto, $paid, $nodays, $ishalfday, $sched_affected, $leave_category, $category, $seminar, $organizer, $venue, $location, $fee, $deadline, $title, $transportation, $accomodation, $others, $total, $reason, $user,$ltypetemp,$availed,$balance,$applyBY, $final_file, $size, $filetype,$liquidated,$dayscount){
    	$return = $base_id = '';
    	$empcount = 0;
    	$arr_data_failed = array();
    	#echo "<pre>"; print_r(array($ltype, $othleave, $datefrom, $dateto, $paid, $nodays, $ishalfday, $sched_affected, $reason, $user,$ltypetemp,$availed,$balance,$applyBY)); die;
    	///< insert
    	$base_id = $this->leave_application->insertBaseLeaveApp($applyBY, $ltype, $othleave, $datefrom, $dateto, $paid, $nodays, $ishalfday, $sched_affected, $leave_category, $category, $seminar, $organizer, $venue, $location, $fee, $deadline, $title, $transportation, $accomodation, $others, $total, $reason, '','','','','','','','','',0,0,0,0,0,0,0,0,0, $final_file, $size, $filetype,$liquidated,$dayscount);					
    	if($base_id){
    		list($empcounttemp,$failed_temp) = $this->leave_application->saveLeaveAppHRDirect($base_id,$this->emp_ttype,$ltype,$othleave,$ltypetemp,$user,$sched_affected, $category,  $seminar, $organizer, $venue, $location, $fee, $deadline, $title, $transportation, $accomodation, $others, $total,$datefrom,$dateto,$nodays,$availed,$balance, $paid, $final_file, $size, $filetype);
    		$empcount += $empcounttemp;
    		if($ishalfday){
    			$starttime = $endtime = "";
				if($sched_affected) list($starttime, $endtime) = explode("|", $sched_affected);
				$dateactive = $this->attcompute->employeeScheduleDateActive($user, $datefrom, $starttime, $endtime);
				$this->leave_application->saveLeaveSched($base_id, $starttime, $endtime, $dateactive);
			}
    		$arr_data_failed = array_merge($arr_data_failed,$failed_temp);
    		/*upload file*/
			/*if(!empty($_FILES['files']['name'])){
	            $config['upload_path'] =  FCPATH . 'uploads/attachments/leave';
	            //restrict uploads to this mime types
	            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|xls|csv|docx';
	            $config['file_name'] = $this->form_data_encryption->encryptString($_FILES['files']['name'].$base_id);
	            
	            //Load upload library and initialize configuration
	            $this->load->library('upload', $config);
	            $this->upload->initialize($config);
	            
	            if($this->upload->do_upload('files', true)){
	                $uploadData = $this->upload->data();
	                $this->leave_application->updateLeaveAppFile($config["file_name"], $base_id);
	            }else{
	                $return .= "File failed to upload. Please contact admin";
	            }
	        }*/
    	}
    	else 			$arr_data_failed = array($user);

    	return array($base_id,$empcount,$arr_data_failed,$return);
	}

    function saveLeaveAppProcess($ltype='', $othleave='', $datefrom='', $dateto='', $paid='', $nodays='', $ishalfday='', $sched_affected='',$leave_category='', $category='', $seminar='', $organizer='', $venue='', $location='', $fee, $deadline, $title, $transportation, $accomodation, $others, $total, $reason='', $dhead='',$chead='',$hrhead='', $user='', $isHead = false,$allowApprover='',$applyBY='', $final_file, $size, $filetype,$liquidated='',$dayscount=''){
    	$code_request = $ltype == 'other' ? $othleave : $ltype;
    	$return = $base_id = '';
    	$empcount = 0;
    	$arr_data_failed = array();
    	$dstatus = "PENDING";
    	$ddate = "";
    	$dseq = $cseq = $hrseq = $cpseq = $dpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";

    	///< head will look up on head code setup
    	$forhead = '';
    	if($user==$dhead || $user==$chead || $isHead) $forhead = 'HEAD';
    	$code_request .= $forhead;

    	if($this->emp_ttype == 'nonteaching') $code_request = $code_request.'NON';
    	if(!$forhead && $this->emp_office == 'LIB') $code_request = str_replace('NON', '', $code_request); ///< #ICA-HYPERION21711

    	//get seq from form
    	$seq = $this->leave_application->getAppSequence($code_request);
    	if($seq->num_rows > 0){
    		$dseq  = $seq->row(0)->dhseq;
    		$cseq  = $seq->row(0)->chseq;
    		$hrseq = $seq->row(0)->hhseq;
    		$cpseq = $seq->row(0)->cpseq;
    		$dpseq = $seq->row(0)->dpseq;
    		$fdseq = $seq->row(0)->fdseq;
    		$boseq = $seq->row(0)->boseq;
    		$pseq  = $seq->row(0)->pseq;
    		$upseq = $seq->row(0)->upseq;
    		$fdhead = $seq->row(0)->financedir;
    		$bohead = $seq->row(0)->budgetoff;
    		$phead = $seq->row(0)->president;
    		$uphead = $seq->row(0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

    		///< get campus head
			$campusid = $this->employee->getempdatacol('campusid',$user);
			$deptid = $this->employee->getempdatacol('deptid',$user);

			$cphead = '';
			$c_res = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='$campusid'");
			if($c_res->num_rows() > 0){
				$cphead = $c_res->row(0)->campus_principal;
			}

			$dphead = '';
			$d_res = $this->db->query("SELECT head FROM code_department WHERE code='$deptid'");
			if($d_res->num_rows() > 0){
				$dphead = $d_res->row(0)->head;
			}

			$head_arr = array(
				$dhead => $dseq,
				$chead => $cseq,
				$hrhead => $hrseq,
				$cphead => $cpseq,
				$dphead => $dpseq,
				$fdhead => $fdseq,
				$bohead => $boseq,
				$phead => $pseq,
				$uphead => $upseq
			);
			foreach($head_arr as $approver_id => $val){
				// if($val == 1) $this->extensions->sendEmailToNextApprover($approver_id);
			}

			///< insert
			$base_id = $this->leave_application->insertBaseLeaveApp($applyBY, $ltype, $othleave, $datefrom, $dateto, $paid, $nodays, $ishalfday, $sched_affected, $leave_category, $category, $seminar, $organizer, $venue, $location, $fee, $deadline, $title, $transportation, $accomodation, $others, $total, $reason, $dhead, $chead, $hrhead, $cphead, $dphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $dpseq, $fdseq, $boseq, $pseq, $upseq, $final_file, $size, $filetype,$liquidated,$dayscount);					///< save base leave app
			
			if($base_id){
				list($empcounttemp,$failed_temp) = $this->leave_application->insertLeaveAppEmpList($base_id, $user ,$this->emp_ttype, $dstatus, $ddate); ///< save emplist
				if($ishalfday){
					$starttime = $endtime = "";
					if($sched_affected) list($starttime, $endtime) = explode("|", $sched_affected);
					$dateactive = $this->attcompute->employeeScheduleDateActive($user, $datefrom, $starttime, $endtime);
					$this->leave_application->saveLeaveSched($base_id, $starttime, $endtime, $dateactive);
				}
				$empcount += $empcounttemp;
				$arr_data_failed = array_merge($arr_data_failed,$failed_temp);

				/*upload file*/
				if(!empty($_FILES['files']['name'])){
		            $config['upload_path'] =  FCPATH . 'uploads/attachments/leave';
		            //restrict uploads to this mime types
		            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|xls|csv|docx';
		            $config['file_name'] = $this->form_data_encryption->encryptString($_FILES['files']['name'].$base_id);
		            
		            //Load upload library and initialize configuration
		            $this->load->library('upload', $config);
		            $this->upload->initialize($config);
		            
		            if($this->upload->do_upload('files', true)){
		                $uploadData = $this->upload->data();
		                $this->leave_application->updateLeaveAppFile($config["file_name"], $base_id);
		            }else{
		                $return .= "File failed to upload. Please contact admin";
		            }
		        }

			}
			else 			$arr_data_failed = array($user);

    	}else{
    		$return = "No current setup for $forhead ". $this->emp_ttype ." leave. ";
    	}

		return array($base_id,$empcount,$arr_data_failed,$return);

    }

    /**
	 * Deletes leave app base on leave id.
	 *
	 * @return string
	 */
    function deleteLeaveApp(){
    	$id = $this->input->post('id');
    	$res = $this->leave_application->deleteLeaveApp($id);
    	// echo "<pre>"; print_r($this->db->last_query()); 
    	echo $res;
    }
		
	function deleteLeaveAppByAdmin(){
		$toks = $this->input->post('toks');
		$aid = ($toks) ? $this->gibberish->decrypt($this->input->post('aid'), $toks) : $this->input->post('aid');

		$leave_data = $this->leave_application->getLeaveDetails($aid);
		$res = 'Failed to delete application.';
		$timestamp = $this->extensions->getServerTime();
		$datenow = date("Y-m-d", strtotime($timestamp));
		if($datenow < $leave_data["dfrom"] || $datenow < $leave_data["dto"]){
			$err = 1;
			$msg = "Not allowed to cancel. Leave already used.";
		} 
		if(sizeof($leave_data) > 0){
			$res = $this->leave_application->deleteLeaveAppByAdmin($aid,$leave_data);
			$err = 0;
			$msg = "Successfully cancelled leave request.";
		}

		echo json_encode(array("err" => $err, "msg" => $msg));
	}

	function validateLeaveRequest(){
		$toks = $this->input->post('toks');
		$employeeid = ($toks) ? $this->gibberish->decrypt($this->input->post('employeeid'), $toks) : $this->input->post('employeeid');
		$dfrom = ($toks) ? $this->gibberish->decrypt($this->input->post('dfrom'), $toks) : $this->input->post('dfrom');
		$ltype = ($toks) ? $this->gibberish->decrypt($this->input->post('ltype'), $toks) : $this->input->post('ltype');
		$other = ($toks) ? $this->gibberish->decrypt($this->input->post('other'), $toks) : $this->input->post('other');
		
		$result = 0;
		if($ltype != "other") $result = $this->leave_application->validateLeaveRequest($employeeid, $ltype, $dfrom);
		else 				  $result = $this->leave_application->getAvailableOtherLeaveBalances($employeeid, $other, $dfrom);
		echo $result;
	}

	function countLeaveRequest(){
		$toks = $this->input->post('toks');
		$employeeid = ($toks) ? $this->gibberish->decrypt($this->input->post('employeeid'), $toks) : $this->input->post('employeeid');
		$dfrom = ($toks) ? $this->gibberish->decrypt($this->input->post('dfrom'), $toks) : $this->input->post('dfrom');
		$ltype = ($toks) ? $this->gibberish->decrypt($this->input->post('ltype'), $toks) : $this->input->post('ltype');
		$other = ($toks) ? $this->gibberish->decrypt($this->input->post('other'), $toks) : $this->input->post('other');
		
		$result = 0;
		if($ltype != "other") $result = $this->leave_application->countAvailableLeave($employeeid, $ltype, $dfrom);
		else 				  $result = $this->leave_application->getAvailableOtherLeaveBalances($employeeid, $other, $dfrom);

		echo $result;
	}

	function validateVacationLeave(){
		$this->load->model("leave");
		$toks = $this->input->post("toks");
		$employeeid = ($toks) ? $this->gibberish->decrypt($this->input->post("employeeid"), $toks) : $this->input->post("employeeid");
		$ndays = ($toks) ? $this->gibberish->decrypt($this->input->post("ndays"), $toks) : $this->input->post("ndays");
		$start = ($toks) ? $this->gibberish->decrypt($this->input->post("start"), $toks) : $this->input->post("start");
		$current_date = $this->extensions->getServerTime();
		$current_date = date("Y-m-d", strtotime($current_date));
		$leave_credits = $this->leave->getEmployeeVacationLeaveCredit($employeeid, $start);

		$pending_leave = $this->leave_application->validateVacationLeaveCredits($employeeid);
		if($leave_credits){
			$diff = $this->extensions->getMonthDifference($leave_credits[0]["dfrom"], $current_date);
			$credits = $diff * 1.25;
			$credits += 1.25;
			$allowed_credits = $leave_credits[0]["credit"] - ($leave_credits[0]["avail"] + $ndays + $pending_leave);
			if($allowed_credits > 0 || $leave_credits[0]["credit"] == ($leave_credits[0]["avail"] + $ndays + $pending_leave)) echo true;
			else echo false;
		}else{
			echo false;
		}
	}

	public function getSeminarAvailedAmount(){
		$dfrom = $dto = "";
		$data = $this->input->post();
		$toks = $data["toks"];
		$ltype = $this->gibberish->decrypt($data["ltype"], $toks);
		$employee = $this->gibberish->decrypt($data["employee"], $toks);
		$availed = $this->leave_application->getSeminarAvailedAmount($ltype, $employee);
		if($availed->num_rows() > 0){
			$dfrom = $availed->row()->dfrom;	
			$dto = $availed->row()->dto;
		}
		$total_amount = 0;
		$seminar = $this->leave_application->getAllApprovedSeminar($dfrom, $dto, $employee);
		foreach($seminar->result_array() as $seminars){
			$total_amount += $seminars["total"];
		}

		echo $total_amount;
	}

	public function hasFiledLeave(){
    	$data = $this->input->post();
    	$toks = $data["toks"];
    	unset($data["toks"]);
    	foreach($data as $key => $val){
    		$data[$key] = $this->gibberish->decrypt($val, $toks);
    		$data[$key] = str_replace("%3A", ":", $data[$key]);
			$data[$key] = str_replace("+", " ", $data[$key]);
    	}
    	$employeeid = $data["employeeid"];
    	$datesetfrom = $data["datesetfrom"];
    	$datesetto = $data["datesetto"];
    	$ishalfday = $data["ishalfday"];
    	$timefrom = $data["timefrom"];
    	$timeto = $data["timeto"];
    	echo $this->leave_application->hasFiledLeave($employeeid, $datesetfrom, $datesetto, $ishalfday, $timefrom, $timeto);
    }

    public function allowedProLeave(){
    	$toks = $this->input->post("toks");
    	$employeeid = ($toks) ? $this->gibberish->decrypt($this->input->post("employeeid"), $toks) : $this->input->post("employeeid");
    	echo $this->leave_application->allowedProLeave($employeeid);
    }

    public function getLeaveAttachments(){
    	$content = $mime = "";
    	$toks = $this->input->post("toks");
    	$id = ($toks) ? $this->gibberish->decrypt($this->input->post("base_id"), $toks) : $this->input->post("base_id");
    	$result = $this->leave_application->getLeaveAttachments($id);
    	if($result->num_rows() > 0){
    		$content = $result->row()->content;
    		$mime = $result->row()->mime;
    	}

    	$response = array("file" => $content, "mime" => $mime);
    	echo json_encode($response);
    }

    public function updateLiquidated(){
    	$toks = $this->input->post("toks");
    	$idkey = $this->gibberish->decrypt($this->input->post("idkey"), $toks);
    	$status = $this->gibberish->decrypt($this->input->post("status"), $toks);
    	$this->leave_application->updateLiquidated($idkey, $status);
    }

    function cancelLeaveApp(){
		$id = $this->input->post('id');
		$response = array();
    	/*get leave details*/
    	$leave_details = $this->leave_application->getLeaveDetails($id);
    	$nodays = $leave_details["nodays"];
    	$paid = $leave_details["paid"];
    	$leavetype = $leave_details["leavetype"];
    	$employeeid = $leave_details["employeeid"];
    	$dfrom = $leave_details["dfrom"];
    	$dto = $leave_details["dto"];
    	$status = $leave_details["status"];
    	if($paid == "YES" && $status == "APPROVED") $this->leave_application->returnLeaveCredit($employeeid, $nodays, $leavetype, $dfrom, $dto);
    	$res = $this->leave_application->cancelLeaveApp($id);
    	if($res) $response = array("icon" => "success", "title" => "Success!", "msg" => "Successfully cancelled.");
    	else $response = array("icon" => "warning", "title" => "Warning!", "msg" => "Failed to cancel.");

		echo json_encode($response);

	}

} //endoffile