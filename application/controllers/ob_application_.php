<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ob_application_ extends CI_Controller {

	private $emp_office = '';
	private $emp_ttype = '';
	public $codedayofweek = array("0"=>"SU", "1"=>"M", "2"=>"T", "3"=>"W", "4"=>"TH", "5"=>"F", "6"=>"S");
	/**
	 * Loads ob_application model everytime this class is accessed.
	 */
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		$this->load->model('ob_application');
		$this->load->library('form_data_encryption');
	}

	/**
	 * Loads leave application view passing list of employees under the user's department. 
	 *
	 * @return view
	 */
	function loadApplyLeaveForm(){
		$this->load->model('employee');
		$this->load->model('utils');
		
		$data['office'] 	= $this->employee->getempdatacol('office');
		$data['emplist'] 	= $this->utils->getEmplist($data['office']);
		unset($data['emplist']['']);
		$data['deptlist'] 	= $this->utils->getDepartments();
		$this->load->view("employeemod/overtimeapply",$data);
	}


	/**`
	 * Loads OB_application history view with list of employee's OB_application applications.
	 *
	 * @return view
	 */
	function getEmpOBHistory(){
        $user 		= $this->session->userdata("username");
        $post 		= $this->input->post();
		$status 	= isset($post['status']) 	? $post['status'] 	: "";
		$isread 	= isset($post['isread']) 	? $post['isread'] 	: "";
		$target 	= isset($post['target']) 	? $post['target'] 	: "";
		$action 	= isset($post['action']) 	? $post['action'] 	: "";
		$data['ob_list'] = array();
		//result after loading the pages.
		if ($action == "load") {
			$data['ob_list'] = $this->ob_application->getEmpOBHistory($user, '', '' ,$isread,$target);
			if (sizeof($data['ob_list']) == 0) {
				$data['ob_list'] = $this->ob_application->getEmpOBHistory($user, 'PENDING', '' ,'1',$target);	
			}
		}
		//result in history after applying for application
		else if($action == "apply")
		{
			$pending = $this->ob_application->getEmpOBHistory($user, 'PENDING', '' ,'1',$target);
			
			if ($pending)$data['ob_list'] = $this->ob_application->getEmpOBHistory($user, 'PENDING', '' ,'1',$target);
			else
			$data['ob_list'] = $this->ob_application->getEmpOBHistory($user, '', '' ,$isread,$target);	

		}
		else
		{
		//for filtering for purposes

				if ($status == "PENDING") {$data['ob_list'] = $this->ob_application->getEmpOBHistory($user, 'PENDING', '' ,'',$target);}
				else if ($status== "") {
				$data['ob_list'] = $this->ob_application->getEmpOBHistory($user, $status, '' ,'',$target);
				}
				else
				{
					$data['ob_list'] = $this->ob_application->getEmpOBHistory($user, $status, '' ,'1',$target);
				}
		
		}

        $data['stat'] = $status;
        if(in_array('leave_list', $data)) $data['leave_list'] = $this->ob_application->getEmpOBHistory($user, $status, '' ,$isread);

        if($target=='CORRECTION') 	$this->load->view("employeemod/correction/correction_history_emp",$data);
        else 						$this->load->view("employeemod/ob_app/ob_history_emp",$data);
	}

	/**
	 * Loads specific view based on input post $view passing leave details.
	 *
	 * @return view
	 */
	function getLeaveDetails(){
		$this->load->model('utils');
		$post 		= $this->input->post();
		$view 		= isset($post['view']) 		? $post['view'] 	: "";
		$target 	= isset($post['target']) 	? $post['target'] 	: "";
		$id 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		$colhead 	= isset($post['colhead']) 	? $post['colhead'] 	: "";
		$colstatus 	=  $colhead ? (substr($colhead,0,-4) . 'status') : '';
		$data 		= $this->ob_application->getLeaveDetails($id,$colstatus);
		// $scheddisp  = $this->getStartEndtimePerDay($data['dfrom'],$data['dto'],$data['employeeid']);
		// $data['sched'] = $scheddisp['scheddisp'];

		$data["idkey"] = $id;
		$data['job'] 	 		= $this->input->post("job");
		// $data['employeeid'] 	= $employeeid;
		$data['colhead'] 		= $colhead;
		$data['isLastApprover'] = $this->input->post("isLastApprover");
		$data['code_request'] = $this->input->post("code_request");
		/*$filename = $this->form_data_encryption->decryptString($data["filename"]);
		$idlen = strlen($data["base_id"]);
		$data['fileExtension'] = pathinfo($filename, PATHINFO_EXTENSION);
		$data['fileExtension'] = substr($data['fileExtension'], 0, -$idlen);
        $data["imgpath"] = base_url()."uploads/attachments/ob/".$data["filename"].".".$data['fileExtension'];*/
		// Globals::pd($data); die;
		if($target=='CORRECTION') 	$this->load->view("employeemod/correction/$view",$data);
		else 						$this->load->view("employeemod/ob_app/$view",$data);

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
		$target 	= $this->input->post('target');
		$isHrHead 	= false;

		$user 			= $this->session->userdata('username');

		$hrhead = $this->utils->getDeptHead('head','HR');
		if($user == $hrhead) $isHrHead = true;
		
		///< for regular employee
		$leave_list = array();

		///< --------------------------(condition added for Campus Principal which is ommitted for non teaching)-------------------------------------------
		$leave_list_teaching = $this->getLeaveAppListToManageProcess('DA',$status,$datefrom,$dateto,$user,'teaching',$target,$deptid,$office);
		if(sizeof($leave_list_teaching) > 0) 	$leave_list =  $leave_list + $leave_list_teaching;

		$leave_list_non = $this->getLeaveAppListToManageProcess('DA'.'NON',$status,$datefrom,$dateto,$user,'nonteaching',$target,$deptid,$office);
		if(sizeof($leave_list_non) > 0) 		$leave_list =  $leave_list + $leave_list_non;

		$leave_list_teaching = $this->getLeaveAppListToManageProcess('DA'.'HEAD',$status,$datefrom,$dateto,$user,'teaching',$target,$deptid,$office);
		if(sizeof($leave_list_teaching) > 0) 	$leave_list =  $leave_list + $leave_list_teaching;

		$leave_list_non = $this->getLeaveAppListToManageProcess('DA'.'HEADNON',$status,$datefrom,$dateto,$user,'nonteaching',$target,$deptid,$office);
		if(sizeof($leave_list_non) > 0) 		$leave_list =  $leave_list + $leave_list_non;
		


		$data['leave_list'] =$leave_list;
		$data['deptid'] =$deptid;
		$data['office'] =$office;
		$data['isHrHead'] 	= $isHrHead; 
		$data['status'] = $status; 
		if($target=='CORRECTION') 	$this->load->view("employeemod/correction/correction_history_manage",$data);
        else 						$this->load->view("employeemod/ob_app/ob_history_manage",$data);

	}


	function getLeaveAppListToManageProcess($code_request="VL",$status='',$datefrom='',$dateto='',$user='',$teachingType='',$target='',$deptid='',$office=''){
		$colhead = $isLastApprover = "";
		$prevcolstatus = ""	;
		$leave_list = array();
		$arr_aprvl_seq 	= array();
		$setup 			= $this->ob_application->getAppSequence($code_request);
		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->ob_application->sortApprovalSeq($setup->row(0));
		}
		// echo "<pre>"; print_r($arr_aprvl_seq)
		$aprvl_count = sizeof($arr_aprvl_seq);
		$prevkey 	 = '';
		$arr_apprv = array();
		foreach ($arr_aprvl_seq as $key => $obj) {
			if($obj['head_id'] == $user){
				$colhead = $obj['position'];
				if($aprvl_count == $key) 	 $isLastApprover = true;
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
			$temp_res = $this->ob_application->getLeaveAppListToManage($user, $arr['colhead'], $arr['colstatus'], $status, $arr['prevcolstatus'], $datefrom, $dateto,$teachingType,$code_request,$target,$arr['seq_count'],$deptid,$office);
			if($temp_res->num_rows() > 0){
				foreach ($temp_res->result() as $key => $row) {
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
		$otid 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		$position_names = $this->utils->getRequestApprover();
		$data['approver_admin'] = '';
		$arr_aprvl_seq 	= array();
		$setup = $this->ob_application->getAppSequencePerLeave($otid);
		// echo "<pre>"; print_r($setup->row(0)); die;
		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->sortApprovalSeqPerLeave($setup->row(0));
			if($setup->row(0)->approver_admin) $data['approver_admin'] = $this->utils->getApproverFullName($setup->row(0)->approver_admin);
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
	 * Get sched details per application. Computes tardy, absent, and early_d if none is saved in the application.
	 *
	 * @return string (concat of sched details -- format referenced from timesched sent to saveSchedStatusChange)
	 */
	function getTimeRecordForBatchApprove($base_id='',$csid=''){
		$timerecord = '';
		$separator = '~u~';
		$cs_detail = $this->ob_application->getTimeRecord($base_id);

		if($cs_detail){
			
			foreach($cs_detail as $row){
				$timerecord .= $timerecord ? '|' : '';
				$timerecord .= 'TR-'.$row->tid . $separator;

				$time = explode(' - ', $row->request_time);

				if(isset($time[0]) && isset($time[1]) ) {
					$timerecord .= $time[0] . $separator . $time[1];
				}else{
					$timerecord .= $separator;
				}

			}
		}

		return $timerecord;
	}


	/**
	 * Saves ob_application status change made by approver.
	 *
	 * @return string
	 */
	function saveLeaveStatusChange(){
		$next_approver = "";
		$count = 0;
		///< leaveid,status,colhead,isLastApprover -- if last na ,set status
		$leaveid 		= $this->input->post('leaveid');
		$base_id 		= $this->input->post('base_id');
		$status 		= $this->input->post('status');
		$endorse 		= $this->input->post('endorse');
		$colhead 		= $this->input->post('colhead');
		$isLastApprover = $this->input->post('isLastApprover');
		$code_request 	= $this->input->post('code_request');
		$colstatus 		= substr($colhead,0,-4) . 'status';
		$coldate 		= substr($colhead,0,-4) . 'date';
		$remarks		= $this->input->post('remarks');
		$timefrom		= $this->input->post('timefrom');
		$timeto		= $this->input->post('timeto');
		$user = $this->session->userdata('username');

		if($this->input->post('isBatchApprove')/* && $isLastApprover*/){
			$timerecord 	= $this->getTimeRecordForBatchApprove($base_id,$leaveid);
		}else{
			$timerecord 	= $this->input->post('timerecord');
		}

		if(!$timefrom || !$timeto) $this->ob_application->updateApplicationTime($timefrom, $timeto, $base_id);
		$res = $this->ob_application->saveLeaveStatusChange($user,$leaveid,$status,$colstatus,$coldate,$colhead,$isLastApprover,$base_id,$remarks,$timerecord, "", $endorse);

		if(!$isLastApprover && $status == 'APPROVED'){ ///< get next in sequence with same head id
			$arr_aprvl_seq 	= array();
			$setup 			= $this->ob_application->getAppSequence($code_request);
			if($setup->num_rows() > 0){
				$arr_aprvl_seq = $this->ob_application->sortApprovalSeq($setup->row(0));
			}
			$aprvl_count = sizeof($arr_aprvl_seq);
			$prevkey 	 = '';
			$arr_apprv = array();
			foreach ($arr_aprvl_seq as $key => $obj) {
				$isLastApprover_tmp = false;
				$colhead_tmp = $obj['position'];

				if($obj['head_id'] == $user && $colhead_tmp != $colhead){
					if($aprvl_count == $key) 	 $isLastApprover_tmp = true;
					if($key > 1) 				 $prevkey 		 = $key - 1;

					$colstatus_tmp =  $colhead_tmp ? (substr($colhead_tmp,0,-4) . 'status') : '';
					$coldate_tmp =  $colhead_tmp ? (substr($colhead_tmp,0,-4) . 'date') : '';

					if($isLastApprover_tmp){
						if($arr_aprvl_seq[$prevkey]['head_id'] == $user){
							$prev_colhead = $arr_aprvl_seq[$prevkey]['position'];
							$res_tmp = $this->ob_application->saveLeaveStatusChange($user,$leaveid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$base_id,$remarks,$timerecord,$prev_colhead,$endorse);
						}
					}else{
						$res_tmp = $this->ob_application->saveLeaveStatusChange($user,$leaveid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$base_id,$remarks,$timerecord, "", $endorse);
					}

				}

                if($res){
                    if(isset($arr_aprvl_seq[$count+1]["head_id"]) && $arr_aprvl_seq[$count+1]["head_id"]) $next_approver = $arr_aprvl_seq[$count+1]["head_id"];
                    elseif(isset($arr_aprvl_seq[$count+2]["head_id"]) && $arr_aprvl_seq[$count+2]["head_id"]) $next_approver = $arr_aprvl_seq[$count+2]["head_id"];
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

	    $forEdit = false;
        if(isset($post_data['base_id'])) $forEdit = $post_data['base_id'] != '' ? true : false;

	    $save_ret =  $this->validateSavingLeaveApp($post_data,$forEdit); ///< Save application details

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


	function saveOBApp(){
		$data   = $this->input->post();

        $sched_affected[] = $this->input->post('sched_affected');
        $data['sched_affected'] = $sched_affected[0];

        $forEdit = false;
        # para sa half days.
        if(isset($data['ishalfday']) && $data['ishalfday'] == 1) $data['ndays'] = 0.5;
        // echo json_encode($data);die;

        $save_ret = $this->validateSavingLeaveApp($data,$forEdit);
        echo json_encode($save_ret);
	}
	
	function convertFormDataToArray($formdata){
		$data_arr = array();
		// echo "<pre>"; print_r($formdata);
		$formdata = explode("&", $formdata);
		foreach($formdata as $row){
			list($key, $value) = explode("=", $row);
			$data_arr[$key] = $value;
		}

		return $data_arr;
	}

	/**
	 * Saves new OB application.
	 *
	 * @return string
	 */
	function validateSavingLeaveApp($data=array(),$forEdit=false){
		$this->load->model('extras');
		$this->load->model('utils');
		$this->load->model('leave');
		$this->load->model('leave_application');
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
		$reason = $this->gibberish->decrypt($this->input->post("reason"), $data["toks"]);
		$data = $this->convertFormDataToArray($formdata);
		$return 	= $msg = "";
		$allemp = $this->utils->getEmpListToCbo();
		$sched_affected = "";
        $ishalfday = isset($data['ishalfday']) ? 1 : 0;
        if(isset($data['base_id'])) $forEdit = $data['base_id'] != '' ? "true" : false;
		$base_id_edit	= isset($data['base_id'])		? $data['base_id']							: "";
		$ltype 			= isset($data['ltype']) 		? $data['ltype'] 							: "";
		$datefrom 		= isset($data['datesetfrom']) 	? $data['datesetfrom'] 						: "";
		$dateto 		= isset($data['datesetto']) 	? $data['datesetto'] 						: "";
		$tfrom 			= isset($data['tfrom']) 		? ($data['tfrom'] == '' ? "00:00:00" : date("H:i:s",strtotime(str_replace("+"," ",$data['tfrom']))) )	: "";
		$tto 			= isset($data['tto']) 			? ($data['tto'] == '' ? "00:00:00" : date("H:i:s",strtotime(str_replace("+"," ",$data['tto']))) )	: "";
		$paid 			= isset($data['withpay']) 		? $data['withpay'] 							: "YES";
		if(isset($data['ishalfday']) && $data['ishalfday'] == 1) $data['ndays'] = 0.5;
        $nodays			= isset($data['ndays']) 		? $data['ndays'] 							: 0;
        $selEmp		= isset($data['sel_emp'])		? $data['sel_emp']							: "";
		// $reason 		= isset($reason)		? $reason	  	: "";
		$reason = str_replace("+", " ", $reason);
		$destination 		= isset($data['destination'])		? $data['destination']	  	: "";
		// $destination = str_replace("+", " ", $destination);
		$ob_type    =isset($data['ob_type'])	? $data['ob_type']: "";
		# for ica-hyperion 21194
		# by justin (with e)
		# > check if user is admin, kapag user is admin.. change user is equal to data->employee.. else user is equal to userdata->username
		$isAdmin = $this->extras->findIfAdmin($this->session->userdata('username'));
		
		# for user
		$user =  $this->session->userdata('username');
		if($isAdmin && isset($data['employee'])) $user = $data['employee'];
		if($isAdmin && !(isset($data['employee'])) && $forEdit){
			$user = $this->db->query("SELECT employeeid FROM ob_app_emplist WHERE base_id='{$base_id_edit}'")->row()->employeeid;
		}
		// echo "<pre>"; print_r($allemp); die;
		if($user == 'all' && $ltype == "CORRECTION"){
			$i = $failed = $success =  0;
			$baseid = '';
            foreach ($allemp as $user => $value) {
            	if($i > 0){
            		if($ishalfday){
						$sched_affected = $data['sched_affected'];
						$dates_arr = $this->utils->getDatesFromRange($datefrom, $dateto);
						$empsched_arr = $this->leave->getEmployeeSchedDays($user,$datefrom); 
						$daysCount = 0;
						foreach ($dates_arr as $date) {

							$sched = $this->attcompute->displaySched($user,$date);
							$countrow = $sched->num_rows();
							if($countrow > 0){
								$dayofwk = date('w', strtotime($date));
								if(in_array($this->codedayofweek[$dayofwk], $empsched_arr)) $daysCount++;
							}
						}

						$nodays = 0.5 * $daysCount;
				
					}

					$isAllowApprover = (isset($data["allowApprover"])) ? $data["allowApprover"] : "";

					$dateto = $datefrom;
					

			    	$det_res = $this->utils->getSingleTblData('employee',array('teachingtype','office'),array('employeeid'=>$user));
			    	if($det_res->num_rows() > 0){
			    		$this->emp_ttype = $det_res->row(0)->teachingtype;
			    		$this->emp_office = $det_res->row(0)->office;
			    	}

			    	if(!$this->emp_ttype) $failed++;

			    	if(!$this->emp_office) $failed++;

			    	if(strtotime($tfrom) > strtotime($tto) && $ob_type == "ob"){
			    		$failed++;
			    	}

			    	$hasfiledLeave = $this->leave_application->hasFiledLeave($user, $datefrom, $dateto, ($ishalfday) ? "true" : "false", $tfrom, $tto);
			    	if($hasfiledLeave >= 1 && !$base_id_edit){
			    		$failed++;
			    	}

			    	/*$hasfiledOb = $this->ob_application->hasFiledOB($user, $datefrom, $dateto, ($ishalfday) ? "true" : "false", $tfrom, $tto);
			    	if($hasfiledOb >= 1 && !$base_id_edit){
			    		$failed++;
			    	}*/

			        $dhead = $this->utils->getDeptHead('head',		$this->emp_office);	
			        $chead = $this->utils->getDeptHead('divisionhead',$this->emp_office);	
			        $hrhead = $this->utils->getDeptHead('head',		'HR');

			        if($ltype){
			    	    $empcount = 0;

			    	    ///< check for existing applications
			    	    // $exist_app = $this->ob_application->checkExistingLeaveApp($user,'APPROVED',$datefrom,$dateto);
			    	    // if($exist_app) {return array('err_code'=>0,'msg'=>'You already have approved applications for this date.');}

			    	    
			    	    if($forEdit){
			    	    	$base_id = $base_id_edit;
			    	    	$empcount_temp = $this->ob_application->modifyLeaveDetails($base_id,$datefrom, $dateto, $tfrom, $tto, $paid,$nodays,$ishalfday,$sched_affected,$reason,$destination, $final_file, $size, $filetype);
			    	    	if($ishalfday){
				    	    	$starttime = $endtime = "";
				    	    	if($sched_affected) list($starttime, $endtime) = explode("|", $sched_affected);
								$dateactive = $this->attcompute->employeeScheduleDateActive($user, $datefrom, $starttime, $endtime);
								$this->ob_application->saveOBSched($base_id, $starttime, $endtime, $dateactive);
							}
			    	    	# > kapag direct approved.. isave dito
							if($isAdmin && $isAllowApprover == 0){
								$aWc = ($selEmp != '') ? " AND employeeid = '$selEmp'" : "";
								$leave_id = $this->db->query("SELECT id FROM ob_app_emplist WHERE base_id='{$base_id}' $aWc")->row()->id;
								
								# change approved status pati tangalin yung seq..
								$res = $this->ob_application->changeSeqForDirectApproved($leave_id,$base_id);
								
								# save sa ob_request
								$res = $this->ob_application->directApprovedByAdmin($leave_id);
							}
			    	    }else{
			    	    	# add new..
			    			list($base_id,$empcount_temp,$failed_temp,$return_temp) = 
			    			$this->saveLeaveAppProcess($ltype, $datefrom, $dateto, $tfrom, $tto, $ob_type, $paid, $nodays, $ishalfday, $sched_affected, $reason, $dhead,$chead,$hrhead,$user,$destination,$isAdmin,$isAllowApprover, $final_file, $size, $filetype);
			    	    }


			    		$empcount += $empcount_temp;
			    		if(isset($return_temp)) $msg .= $return_temp;

			        }else {
			        	$failed++; 
			        }
			        


			        if($empcount){
						$baseid .= '|'.$base_id;
						$success++;
					}else{ 			
						$failed++;
					}
            	}
            	$i++;
            }
           	$msg = 'Successfully saved '.$success.' application and '.$failed.' failed application';
            return array('err_code'=>1,'msg'=>$msg,'base_id'=>$baseid);
		}else{

			if($ishalfday){
				$sched_affected = $data['sched_affected'];
				$dates_arr = $this->utils->getDatesFromRange($datefrom, $dateto);
				$empsched_arr = $this->leave->getEmployeeSchedDays($user,$datefrom); 
				$daysCount = 0;
				foreach ($dates_arr as $date) {
					$sched = $this->attcompute->displaySched($employeeid,$date);
					$countrow = $sched->num_rows();
					if($countrow > 0){

						$dayofwk = date('w', strtotime($date));
						if(in_array($this->codedayofweek[$dayofwk], $empsched_arr)) $daysCount++;
					}
				}

				$nodays = 0.5 * $daysCount;
		
			}

			$isAllowApprover = (isset($data["allowApprover"])) ? $data["allowApprover"] : "";
			# end of for ica-hyperion 21194

			if($ltype == "CORRECTION") $dateto = $datefrom;
			
	        ///< sort by teaching type -- different setup per teaching type

	    	$det_res = $this->utils->getSingleTblData('employee',array('teachingtype','office'),array('employeeid'=>$user));
	    	if($det_res->num_rows() > 0){
	    		$this->emp_ttype = $det_res->row(0)->teachingtype;
	    		$this->emp_office = $det_res->row(0)->office;
	    	}

	    	if(!$this->emp_ttype) {return array('err_code'=>0,'msg'=>'You have no employee type. Please set teaching or non-teaching.');}

	    	if(!$this->emp_office) {return array('err_code'=>0,'msg'=>'Please set your department first.');}

	    	if(strtotime($tfrom) > strtotime($tto) && $ob_type == "ob"){
	    		$msg .= "Please input a invalid time!";
				return array('err_code'=>0,'msg'=>$msg);
	    	}

	    	$hasfiledLeave = $this->leave_application->hasFiledLeave($user, $datefrom, $dateto, ($ishalfday) ? "true" : "false", $tfrom, $tto);
	    	if($hasfiledLeave >= 1 && !$base_id_edit){
	    		$filedLeaveDetails = $this->leave_application->hasFiledLeaveDetails($user, $datefrom, $dateto, ($ishalfday) ? "true" : "false", $tfrom, $tto);
	    		if($filedLeaveDetails[0]['isHalfDay'] == 0){
	    			$msg .= "You already filed a leave on this date!";
					return array('err_code'=>0,'msg'=>$msg);
	    		}
	    	}

	    	$hasfiledOb = $this->ob_application->hasFiledOB($user, $datefrom, $dateto, ($ishalfday) ? "true" : "false", $tfrom, $tto);
	    	if($hasfiledOb >= 1 && !$base_id_edit){
	    		$msg .= "You already filed a ob/correction on this date!";
				return array('err_code'=>0,'msg'=>$msg);
	    	}

	        $dhead = $this->utils->getDeptHead('head',		$this->emp_office);	
	        $chead = $this->utils->getDeptHead('divisionhead',$this->emp_office);	
	        $hrhead = $this->utils->getDeptHead('head',		'HR');

	        if($ltype){
	    	    $empcount = 0;

	    	    ///< check for existing applications
	    	    // $exist_app = $this->ob_application->checkExistingLeaveApp($user,'APPROVED',$datefrom,$dateto);
	    	    // if($exist_app) {return array('err_code'=>0,'msg'=>'You already have approved applications for this date.');}

	    	    
	    	    if($forEdit){
	    	    	$base_id = $base_id_edit;
	    	    	$empcount_temp = $this->ob_application->modifyLeaveDetails($base_id,$datefrom, $dateto, $tfrom, $tto, $paid,$nodays,$ishalfday,$sched_affected,$reason,$destination, $final_file, $size, $filetype);
	    	    	if($ishalfday){
		    	    	$starttime = $endtime = "";
		    	    	if($sched_affected) list($starttime, $endtime) = explode("|", $sched_affected);
						$dateactive = $this->attcompute->employeeScheduleDateActive($user, $datefrom, $starttime, $endtime);
						$this->ob_application->saveOBSched($base_id, $starttime, $endtime, $dateactive);
					}
	    	    	# > kapag direct approved.. isave dito

					if($isAdmin && $isAllowApprover == 0){
						// $leave_id = $this->db->query("SELECT id FROM ob_app_emplist WHERE base_id='{$base_id}' AND employeeid = '$user'")->row()->id;
						$aWc = ($selEmp != '') ? " AND employeeid = '$selEmp'" : "";
						$leave_id = $this->db->query("SELECT id FROM ob_app_emplist WHERE base_id='{$base_id}' $aWc")->row()->id;
						
						# change approved status pati tangalin yung seq..
						$res = $this->ob_application->changeSeqForDirectApproved($leave_id,$base_id);
						
						# save sa ob_request
						// echo "<pre>"; print_r($leave_id); die;

						$res = $this->ob_application->directApprovedByAdmin($leave_id);

					}
	    	    }else{
	    	    	# add new..
	    			list($base_id,$empcount_temp,$failed_temp,$return_temp) = 
	    			$this->saveLeaveAppProcess($ltype, $datefrom, $dateto, $tfrom, $tto, $ob_type, $paid, $nodays, $ishalfday, $sched_affected, $reason, $dhead,$chead,$hrhead,$user,$destination,$isAdmin,$isAllowApprover, $final_file, $size, $filetype);
	    	    }


	    		$empcount += $empcount_temp;
	    		if(isset($return_temp)) $msg .= $return_temp;

	        }else {return array('err_code'=>0,'msg'=>'No ob type selected.');}
	        


	        if($empcount){
				if(!$base_id_edit) $msg .= "$empcount employee(s) successfully applied.";
				else $msg .= "Successfully updated ob application.";
				return array('err_code'=>1,'msg'=>$msg,'base_id'=>$base_id);

			}else{ 			
				$msg .= "Failed to save application.";
				return array('err_code'=>0,'msg'=>$msg);
			}

		}
				
    }

    function saveLeaveAppProcess($ltype='', $datefrom='', $dateto='', $timefrom='', $timeto='', $ob_type='', $paid='', $nodays='', $ishalfday='', $sched_affected='', $reason='', $dhead='',$chead='',$hrhead='', $user='',$destination = '',$isAdmin = false, $isAllowApprover = '', $final_file, $size, $file_type){
    	$this->load->model('utils');
    	
    	$code_request = 'DA';
    	$return = $base_id = '';
    	$empcount = 0;
    	$arr_data_failed = array();
    	$dstatus = "PENDING";
    	$ddate = "";
    	$dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";

    	$isHead = $this->utils->checkIfHead($user);
    	if($ob_type != "ob") $paid = "NO";
    	///< head will look up on head code setup
    	$forhead = '';
    	if($user==$dhead || $user==$chead || $isHead) $forhead = 'HEAD';
    	$code_request .= $forhead;

    	if($this->emp_ttype == 'nonteaching') $code_request = $code_request.'NON';
    	if((!$forhead) && $this->emp_office == "CMSI" || $this->emp_office == "DISCP" || $this->emp_office == "GUID" ) $code_request = str_replace('NON', '', $code_request); 

    	//get seq from form
    	$seq = $this->ob_application->getAppSequence($code_request);
    	// echo "<pre>"; print_r($seq->result());
    	if($seq->num_rows > 0){
    		$dseq  = $seq->row(0)->dhseq;
    		$cseq  = $seq->row(0)->chseq;
    		$hrseq = $seq->row(0)->hhseq;
    		$cpseq = $seq->row(0)->cpseq;
    		$fdseq = $seq->row(0)->fdseq;
    		$boseq = $seq->row(0)->boseq;
    		$pseq  = $seq->row(0)->pseq;
    		$upseq = $seq->row(0)->upseq;
    		$fdhead = $seq->row(0)->financedir;
    		$bohead = $seq->row(0)->budgetoff;
    		$phead = $seq->row(0)->president;
    		$uphead = $seq->row(0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

    		///< get campus head
			$office = $this->employee->getempdatacol('office',$user);

			$cphead = '';
			$c_res = $this->db->query("SELECT divisionhead FROM code_office WHERE code='$office'");
			if($c_res->num_rows() > 0){
				$cphead = $c_res->row(0)->divisionhead;
			}

			# for ica-hyperion 21194
			# by justin (with e)
			# > kapag isAllowApprover is equal to 0, ang gagawin niya ay idi-disabled nya yung seq..
			$col_stat = '';
			$applied_by = $user;
			if($isAdmin && $isAllowApprover == 0){
				$dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";
				$col_stat = ", status";
				
			}

			# > change mo yung user into username ng admin... 
			if($isAdmin) $applied_by = $this->session->userdata("username");

			# echo $applied_by ." - ". $col_stat; die;

			$head_arr = array(
				$dhead => $dseq,
				$chead => $cseq,
				$hrhead => $hrseq,
				$cphead => $cpseq,
				$fdhead => $fdseq,
				$bohead => $boseq,
				$phead => $pseq,
				$uphead => $upseq
			);
			foreach($head_arr as $approver_id => $val){
				if($val == 1) {
					if(Globals::is_connect_internet()){
						$this->extensions->sendEmailToNextApprover($approver_id);
					}
				}
			}

			///< insert
			# modified by justin (with e)
			$base_id = $this->ob_application->insertBaseLeaveApp($applied_by, $ltype, $datefrom, $dateto, $timefrom, $timeto, $ob_type, $paid, $nodays, $ishalfday, $sched_affected, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq,$destination, $final_file, $size, $file_type);					///< save base leave app
			if($base_id){
				# modified by justin (with e)
				list($empcounttemp,$failed_temp) = $this->ob_application->insertLeaveAppEmpList($base_id, $user ,$this->emp_ttype, $dstatus, $ddate, $isAdmin, $col_stat); ///< save emplist
				if($ishalfday){
					$starttime = $endtime = "";
					if($sched_affected) list($starttime, $endtime) = explode("|", $sched_affected);
					$dateactive = $this->attcompute->employeeScheduleDateActive($user, $datefrom, $starttime, $endtime);
					$this->ob_application->saveOBSched($base_id, $starttime, $endtime, $dateactive);
				}
				// echo "<pre>"; var_dump($this->db->last_query()); die;
				# > kapag direct approved.. isave dito
				if($isAdmin && $isAllowApprover == 0){
					$leave_id = $this->db->query("SELECT id FROM ob_app_emplist WHERE base_id='{$base_id}'")->row()->id;
					$res = $this->ob_application->directApprovedByAdmin($leave_id);
				}

				$empcount += $empcounttemp;
				$arr_data_failed = array_merge($arr_data_failed,$failed_temp);

				/*upload file*/
				/*if(!empty($_FILES['files']['name'])){
		            $config['upload_path'] =  FCPATH . 'uploads/attachments/ob';
		            //restrict uploads to this mime types
		            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|xls|csv|docx';
		            $config['file_name'] = $this->form_data_encryption->encryptString($_FILES['files']['name'].$base_id);
		            
		            //Load upload library and initialize configuration
		            $this->load->library('upload', $config);
		            $this->upload->initialize($config);
		            
		            if($this->upload->do_upload('files', true)){
		                $uploadData = $this->upload->data();
		                $this->ob_application->updateLeaveAppFile($config["file_name"], $base_id);
		            }else{
		                $return .= "File failed to upload. Please contact admin";
		            }
		        }*/
			}
			else 			$arr_data_failed = array($user);

			# end for ica-hyperion 21194
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
    	$res = $this->ob_application->deleteLeaveApp($id);
    	echo $res;
    }

    function deleteCorrectionApp(){
    	$id = $this->input->post('id');
    	$res = $this->ob_application->deleteCorrectionApp($id);
    	echo $res;
    }


    function displayStartEndtimePerDay(){
    	$toks 	= $this->input->post('toks');
    	$startdate 	= ($toks) ? $this->gibberish->decrypt($this->input->post('startdate'), $toks) : $this->input->post('startdate');
    	$enddate 	= ($toks) ? $this->gibberish->decrypt($this->input->post('enddate'), $toks) : $this->input->post('enddate');
    	$employeeid 	= ($toks) ? $this->gibberish->decrypt($this->input->post('employeeid'), $toks) : $this->input->post('employeeid');
    	$ob_type 	= ($toks) ? $this->gibberish->decrypt($this->input->post('ob_type'), $toks) : $this->input->post('ob_type');
    	// $employeeid     = $this->session->userdata('username');

    	$enddate = $startdate;
    	if($ob_type == "ob") $ret_arr = $this->getStartEndtimePerDay($startdate, $enddate, $employeeid);
    	else $ret_arr = $this->getActualtimePerDay($startdate, $enddate, $employeeid, $ob_type);
    	echo json_encode($ret_arr);
    }

    function getStartEndtimePerDay($startdate='', $enddate='', $employeeid=''){
    	$this->load->model('utils');
    	$ret_arr = array();
    	$sched_start = $sched_end = '';
    	if($employeeid){
		    $dayofwk = date('w', strtotime($startdate));

    		$res = $this->ob_application->getEmpSchedMinMaxTimePerday($employeeid,$startdate);

    		if($res->num_rows() > 0){
    			foreach ($res->result() as $key => $row) {
    			    if($row->idx == $dayofwk){
    			        $sched_start 	= date('h:i A',strtotime($row->start)) ;
    			        $sched_end 		= date('h:i A',strtotime($row->end)) ;
    			        break;
    			    }
    			}
    		}

    	}
    	$ret_arr = array('sched_start'=>$sched_start, 'sched_end'=>$sched_end);
    	return $ret_arr;
    }

    function getActualtimePerDay($startdate='', $enddate='', $employeeid='', $ob_type=''){
    	$this->load->model('utils');
    	$ret_arr = array();
    	$sched_start = $sched_end = '';
    	if($employeeid){
    		$res = $this->ob_application->getEmpActualTimePerday($employeeid,$startdate);

    		if($res->num_rows() > 0){
    			foreach ($res->result() as $key => $row) {
			        $sched_start 	= date('h:i A',strtotime($row->timein)) ;
			        $sched_end 		= date('h:i A',strtotime($row->timeout)) ;
			        if($ob_type == "late") break;
    			}
    		}

    	}
    	$ret_arr = array('sched_start'=>$sched_start, 'sched_end'=>$sched_end);
    	return $ret_arr;
    }
		
    # for ica-hyperion 21194
    # by justin (with e)
    # new function added
    function delRequest(){
    	$id = $this->input->post('id');
    	
    	# get ko muna yung base id..
    	$base_id = $this->db->query("SELECT base_id FROM ob_app_emplist WHERE id='{$id}'")->row()->base_id;

    	# delete ob application
    	$this->db->query("DELETE FROM ob_app WHERE id='{$base_id}'");

    	# delete on ob request
    	$this->db->query("DELETE FROM ob_request WHERE aid='{$id}'");
    	
    	echo "Successfully deleted";
    }
    # end for ica-hyperion 21194

    function hasFiledCorrection(){
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
    	echo $this->ob_application->hasFiledCorrection($employeeid, $datesetfrom, $datesetto);
    }

    function hasFiledOB(){
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
    	echo $this->ob_application->hasFiledOB($employeeid, $datesetfrom, $datesetto, $ishalfday, $timefrom, $timeto);
    }

    function employeeAppSched(){
    	$isSame = 1;
    	$sched_arr = array();
    	$data = $this->input->post();
    	$toks = $data["toks"];
    	unset($data["toks"]);
    	foreach($data as $key => $val){
    		$data[$key] = $this->gibberish->decrypt($val, $toks);
    	}
    	$qdate = $this->attcompute->displayDateRange($data["datesetfrom"], $data["datesetto"]);
    	foreach($qdate as $rdate){
    		$sched = $this->attcompute->displaySched($data["employeeid"],$rdate->dte);
    		foreach($sched->result() as $schedule){
    			$sched_arr[$rdate->dte][] = $schedule->starttime."-".$schedule->endtime;
    		}

    	}

    	$last_sched = array();
    	foreach($sched_arr as $sched_list){
    		if($last_sched){
    			if(array_diff($last_sched, $sched_list)) $isSame = 0;
    		}
    		$last_sched = $sched_list;
    	}
    	
    	echo $isSame;
    }

    public function hasActualLog(){
    	$data = $this->input->post();
    	$toks = $data["toks"];
    	$employeeid = $this->gibberish->decrypt($data["employeeid"], $toks);
    	$date = $this->gibberish->decrypt($data["datesetfrom"], $toks);
    	echo $this->ob_application->hasActualLog($employeeid, $date);
    }

    public function hasUndertime(){
    	$res = 0;
    	$data = $this->input->post();
    	$toks = $data["toks"];
    	$employeeid = $this->gibberish->decrypt($data["employeeid"], $toks);
    	$date = $this->gibberish->decrypt($data["datesetfrom"], $toks);
    	$sched = $this->attcompute->displaySched($employeeid,$date);
    	$seq = 0;
    	foreach($sched->result() as $rsched){
    		$seq += 1;
            $stime  = $rsched->starttime;
            $etime  = $rsched->endtime; 
            $tstart = $rsched->tardy_start; 
            $absent_start = $rsched->absent_start;
            $earlyd = $rsched->early_dismissal;
            
            // logtime
            list($login,$logout,$q)           = $this->attcompute->displayLogTime($employeeid,$date,$stime,$etime,"NEW",$seq,$absent_start,$earlyd);
            $logout = date("H:i:s", strtotime($logout));
            $earlyd = date("H:i:s", strtotime($earlyd));
            if(strtotime($logout) < strtotime($earlyd) && $seq > 1){
            	$res = 1;
            }
            
    	}

    	echo $res;
    }

    public function getOBAttachments(){
    	$content = $mime = "";
    	$id = $this->input->post("base_id");
    	$result = $this->ob_application->getOBAttachments($id);
    	if($result->num_rows() > 0){
    		$content = $result->row()->content;
    		$mime = $result->row()->mime;
    	}

    	$response = array("file" => $content, "mime" => $mime);
    	echo json_encode($response);
    }

    function cancelOBApp(){
		$id = $this->input->post('id');
		$response = array();
		$ob_details = $this->ob_application->getLeaveDetails($id);
		$dfrom = $ob_details["dfrom"];
    	$dto = $ob_details["dto"];
    	$timefrom = $ob_details["timefrom"];
    	$timeto = $ob_details["timeto"];
    	$othertype = $ob_details["othertype"];
    	$status = $ob_details["status"];
    	$employeeid = $ob_details["employeeid"];

    	$timein = $dfrom." ". $timefrom;
    	$timeout = $dto." ". $timeto;

    	/*if status is approved delete inserted logs*/
    	if($status == "APPROVED" && $othertype == "DIRECT"){
    		$this->ob_application->deleteOBInsertedLogs($employeeid, $timein, $timeout);
    	}elseif($status == "APPROVED" && $othertype == "CORRECTION"){
    		$q_time = $this->ob_application->correctionTimeHistory($ob_details["base_id"]);
    		if($q_time->num_rows() > 0){
    			foreach($q_time->result() as $time_list ){
    				$actual_time = $time_list->actual_time;
    				$request_time = $time_list->request_time;
    				list($act_in, $act_out) = explode(" - ", $actual_time);
    				list($req_in, $req_out) = explode(" - ", $request_time);

    				if($act_in == "(--:-- --)") $act_in = $time_list->cdate." 00:00:00";
    				else $act_in = date("Y-m-d H:i:s", strtotime($time_list->cdate." ".$act_in));

    				if($act_out == "(--:-- --)") $act_out = $time_list->cdate." 00:00:00";
    				else $act_out = date("Y-m-d H:i:s", strtotime($time_list->cdate." ".$act_out));


    				$req_in = date("Y-m-d H:i:s", strtotime($time_list->cdate." ".$req_in));
    				$req_out = date("Y-m-d H:i:s", strtotime($time_list->cdate." ".$req_out));

    				$is_update = $this->attendance->timesheetExists($time_list->tid);
    				if($is_update->num_rows() > 0){
    					/*update timesheet */
    					$this->ob_application->updateTimesheetLogs($time_list->tid, $act_in, $act_out, $req_in, $req_out, $ob_details["employeeid"]);
    				}else{
    					/*delete timesheet if not existing*/
    					$this->ob_application->deleteTimesheetLogs($time_list->tid, $req_in, $req_out, $ob_details["employeeid"]);
    				}
    			}
    		}
    	}
    		
    	$res = $this->ob_application->cancelOBApp($id, $ob_details["base_id"]);
    	if($res) $response = array("icon" => "success", "title" => "Success!", "msg" => "Successfully cancelled.");
    	else $response = array("icon" => "warning", "title" => "Warning!", "msg" => "Failed to cancel.");

		echo json_encode($response);

	}

} //endoffile