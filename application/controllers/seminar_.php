<?php 

	if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Seminar_ extends CI_Controller {

		function __construct(){
			parent::__construct();
			if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
			$this->load->model('seminar');
			$this->load->library('PdfCreator_tcpdf');
			$this->load->library('PdfCreator_mpdf');
		}

		public function loadSeminarApplication(){
			if($this->session->userdata("usertype") != "ADMIN") $data["applied_by"] = $this->session->userdata("username");
			$data["employeelist"] = $this->employee->getEmployeeList("active");
			$this->load->view("employeemod/seminar/seminar_app", $data);
		}

		public function saveSeminarApp(){
			$data   = $this->input->post();
			if( $this->input->post('sched_affected') ){
		        $sched_affected[] = $this->input->post('sched_affected');
		        $data['sched_affected'] = $sched_affected[0];
			}

	        $forEdit = $byHr = false;
	        if(isset($data['base_id'])) $forEdit = $data['base_id'] != '' ? true : false;

	        $allowApprover	= isset($data['allowApprover']) 			? $data['allowApprover'] : "";
	        $isAdmin	= isset($data['isAdmin']) 			? $data['isAdmin'] : "";
	        if(!$allowApprover && $isAdmin){
	        	$byHr = true;
	        }

	        $save_ret = $this->validateSavingSeminarApp($data,$forEdit,$byHr);
	        echo json_encode($save_ret);
		}

		public function validateSavingSeminarApp($data=array(),$forEdit=false,$byHr=false){
			$this->load->model('extras');
			$this->load->model('utils');
			$return = $msg = "";
			$teachingtype = "";
			$exist_app = $empcount = $ltypetemp = $ltype = $availed = $balance = 0;
			$base_id_edit = isset($data['base_id']) ? $data['base_id'] : "";
	    	$det_res = $this->utils->getSingleTblData('employee',array('teachingtype','office'),array('employeeid'=>$data["applied_by"]));
	    	if($det_res->num_rows() > 0){
	    		$teachingtype = $det_res->row(0)->teachingtype;
	    		$office = $det_res->row(0)->office;
	    	}
	    	if(!$teachingtype) {return array('err_code'=>0,'msg'=>'You have no employee type. Please set teaching or non-teaching.');}

	    	if(!$office) {return array('err_code'=>0,'msg'=>'Please set your office first.');}

	        $dhead = $this->utils->getDeptHead('head',		$office);	
	        $chead = $this->utils->getDeptHead('divisionhead',$office);	
	        $hrhead = $this->utils->getDeptHead('head',		'HR');
	        $isHead = $this->utils->checkIfHead($data["applied_by"]);

    	    ///< check for existing applications
    	    $exist_app = $this->seminar->checkExistingSeminarApp($data["applied_by"],$data["datesetfrom"],$data["datesetto"]);
    	    if($exist_app && !$forEdit) {return array('err_code'=>0,'msg'=>'You already have approved applications for this date.');}

    	    if($forEdit){
    	    	if($byHr){
    	    		list($base_id,$empcount_temp,$failed_temp,$return_temp) = $this->saveLeaveAppHRDirectProcess($data, $teachingtype, $office, $data["applied_by"]);
    	    		$this->seminar->deleteLeaveRequestForAdminAutoApprove($base_id_edit);
    	    	}else{
	    	    	$base_id = $base_id_edit;
	    	    	$empcount_temp = $this->seminar->modifySeminarDetails($data, $base_id);
	    	    }

    	    }else{
    	    	if($byHr){
				    list($base_id,$empcount_temp,$failed_temp,$return_temp) = $this->saveLeaveAppHRDirectProcess($data, $teachingtype, $office);
    	    	}else{
	    			list($base_id,$empcount_temp,$failed_temp,$return_temp) = 
	    			$this->saveSeminarAppProcess($data, $teachingtype, $office, $dhead, $chead, $hrhead, $isHead, $data["applied_by"]);
    	    	}
    	    }


    		$empcount += $empcount_temp;
    		if(isset($return_temp)) $msg .= $return_temp;

	       
	        if($empcount){
				if(!$base_id_edit) $msg .= "$empcount employee(s) successfully applied.";
				else $msg .= "Successfully updated leave application.";
				return array('err_code'=>1,'msg'=>$msg,'base_id'=>$base_id);

			}else{ 			
				$msg .= "Failed to save application.";
				return array('err_code'=>0,'msg'=>$msg);
			}

	    }

	    public function saveLeaveAppHRDirectProcess($data, $teachingtype, $office, $user){
	    	$return = $base_id = '';
	    	$empcount = 0;
	    	$arr_data_failed = array();

	    	$base_id = $this->seminar->insertBaseSeminarApp($data);					
	    	if($base_id){
	    		list($empcounttemp,$failed_temp) = $this->seminar->saveLeaveAppHRDirect($base_id, $teachingtype, $data);
	    		$empcount += $empcounttemp;
	    		$arr_data_failed = array_merge($arr_data_failed,$failed_temp);
	    	}
	    	else 			$arr_data_failed = array($user);

	    	return array($base_id,$empcount,$arr_data_failed,$return);
		}

		public function saveSeminarAppProcess($data, $teachingtype, $office, $dhead, $chead, $hrhead, $isHead, $user){
	    	$code_request = "SEMINAR";
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

	    	if($teachingtype == 'nonteaching') $code_request = $code_request.'NON';
	    	if(!$forhead && $office == 'LIB') $code_request = str_replace('NON', '', $code_request); ///< #ICA-HYPERION21711

	    	//get seq from form
	    	$seq = $this->seminar->getAppSequence($code_request);
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
				$data["date_applied"] = date("Y-m-d");
				$data["dhead"]  = $dhead;
				$data["chead"]  = $chead;
				$data["hrhead"]  = $hrhead;
				$data["cphead"]  = $cphead;
				$data["dphead"]  = $dphead;
				$data["fdhead"]  = $fdhead;
				$data["bohead"]  = $bohead;
				$data["phead"]  = $phead;
				$data["uphead"]  = $uphead;

				$data["dseq"]  = $seq->row(0)->dhseq;
	    		$data["cseq"]  = $seq->row(0)->chseq;
	    		$data["hrseq"] = $seq->row(0)->hhseq;
	    		$data["cpseq"] = $seq->row(0)->cpseq;
	    		$data["dpseq"] = $seq->row(0)->dpseq;
	    		$data["fdseq"] = $seq->row(0)->fdseq;
	    		$data["boseq"] = $seq->row(0)->boseq;
	    		$data["pseq"]  = $seq->row(0)->pseq;
	    		$data["upseq"] = $seq->row(0)->upseq;
	    		$data["fdhead"] = $seq->row(0)->financedir;
	    		$data["bohead"] = $seq->row(0)->budgetoff;
	    		$data["phead"] = $seq->row(0)->president;
				///< insert
				$base_id = $this->seminar->insertBaseSeminarApp($data);					///< save base leave app
				if($base_id){
					list($empcounttemp,$failed_temp) = $this->seminar->insertSeminarAppEmpList($base_id, $user ,$teachingtype, $dstatus, $ddate); ///< save emplist
					$empcount += $empcounttemp;
					$arr_data_failed = array_merge($arr_data_failed,$failed_temp);
				}
				else 			$arr_data_failed = array($user);

	    	}else{
	    		$return = "No current setup for $forhead ". $teachingtype ." leave. ";
	    	}

			return array($base_id,$empcount,$arr_data_failed,$return);

	    }

	    public function getEmpSeminarHistory(){
	        $user 		= $this->session->userdata("username");
	        $post 		= $this->input->post();
			$status 	= isset($post['status']) 	? $post['status'] 	: "";
			$isread 	= isset($post['isread']) 	? $post['isread'] 	: "";
			$action 	= isset($post['action']) 	? $post['action'] 	: "";
	        $data['seminar_list'] = array();

			//result after loading the pages.
			if ($action == "load") {
				$data['seminar_list'] = $this->seminar->getEmpSeminarHistory($user, '', '' ,$isread);	
				if (sizeof($data['seminar_list']) ==  0 ) {
					$data['seminar_list'] = $this->seminar->getEmpSeminarHistory($user, 'PENDING', '' ,'1');
				}
			}
			//result in history after applying for application
			else if($action == "apply"){
				$data['seminar_list'] = $this->seminar->getEmpSeminarHistory($user, 'PENDING', '' ,'1');
				if (sizeof($data['seminar_list']) ==  0 ) {
					$data['seminar_list'] = $this->seminar->getEmpSeminarHistory($user, '', '' ,$isread);	
				}
			}
			else{
				$data['seminar_list'] = $this->seminar->getEmpSeminarHistory($user, $status, '' ,$isread);
		    }

	        $data['stat'] = $status;
	        if(sizeof($data['seminar_list']) == 0) $data['seminar_list'] = $this->seminar->getEmpSeminarHistory($user, $status, '' ,$isread);
	        $this->load->view("employeemod/seminar/seminar_history_emp",$data);
		}

		public function getApprovalSeqStatus(){
			///< display position, name, status, date updated
			$this->load->model('utils');
			$post 		= $this->input->post();
			$idkey 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
			$position_names = $this->utils->getRequestApprover();
			// echo "<pre>"; print_r($position_names); die;
			$arr_aprvl_seq 	= array();
			$setup = $this->seminar->getAppSequencePerSeminar($idkey);
			if($setup->num_rows() > 0){
				$arr_aprvl_seq = $this->sortApprovalSeqPerSeminar($setup->row(0));
			}
			foreach ($arr_aprvl_seq as $key => $obj) {
				$arr_aprvl_seq[$key]['position_name'] 	= $position_names[$obj['position']];
				$arr_aprvl_seq[$key]['fullname'] 		= $this->utils->getFullName($obj['head_id']);
			}
			$data['arr_aprvl_seq'] = $arr_aprvl_seq;
			$this->load->view("employeemod/approval_list_overtime",$data);
		}

		public function sortApprovalSeqPerSeminar($setup){
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

		public function getSeminarDetails(){
			$this->load->model('utils');
			$post 		= $this->input->post();
			$view 		= isset($post['view']) 		? $post['view'] 	: "";
			$id 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
			$colhead 	= isset($post['colhead']) 	? $post['colhead'] 	: "";
			$colstatus 	=  $colhead ? (substr($colhead,0,-4) . 'status') : '';
			$data 		= $this->seminar->getSeminarDetails($id,$colstatus);
			$data["idkey"]			= $id;
			$data['job'] 	 		= $this->input->post("job");
			$data['colhead'] 		= $colhead;
			$data['isLastApprover'] = $this->input->post("isLastApprover");
			$data['code_request'] = $this->input->post("code_request");
			$data["employeelist"] = $this->employee->getEmployeeList("active");
			$this->load->view("employeemod/seminar/$view",$data);
		}

	    public function deleteSeminarApp(){
	    	$id = $this->input->post('id');
	    	$res = $this->seminar->deleteSeminarApp($id);
	    	echo $res;
	    }
	
		public function getSeminarAppListToManage(){
			$this->load->model('utils');
			$colhead = $status = $isLastApprover = "";		///< ex. $colhead = "dhead" / "chead" / "hrhead";
			$prevcolstatus = ""	;   ///< column name for head status to check if already approved by previous approver in sequence
			$status 	= $this->input->post('status');
			$datefrom 	= $this->input->post('datefrom');
			$dateto 	= $this->input->post('dateto');
			$isHrHead 	= false;

			$user 			= $this->session->userdata('username');
			
			$hrhead = $this->utils->getDeptHead('head','HR');
			if($user == $hrhead) $isHrHead = true;
			
			///< for regular employee
			$leave_list = array();

			$leave_list_teaching = $this->getSeminarAppListToManageProcess("SEMINAR",$status,$datefrom,$dateto,$user,'teaching');
			if(sizeof($leave_list_teaching) > 0) 	$leave_list =  $leave_list + $leave_list_teaching;

			$leave_list_non = $this->getSeminarAppListToManageProcess("SEMINAR",$status,$datefrom,$dateto,$user,'nonteaching');
			if(sizeof($leave_list_non) > 0) 		$leave_list =  $leave_list + $leave_list_non;

			$leave_list_non = $this->getSeminarAppListToManageProcess("SEMINAR".'NON',$status,$datefrom,$dateto,$user,'nonteaching');
			if(sizeof($leave_list_non) > 0) 		$leave_list =  $leave_list + $leave_list_non;

			$leave_list_teaching = $this->getSeminarAppListToManageProcess("SEMINAR".'HEAD',$status,$datefrom,$dateto,$user,'teaching');
			if(sizeof($leave_list_teaching) > 0) 	$leave_list =  $leave_list + $leave_list_teaching;

			$leave_list_non = $this->getSeminarAppListToManageProcess("SEMINAR".'HEADNON',$status,$datefrom,$dateto,$user,'nonteaching');
			if(sizeof($leave_list_non) > 0) 		$leave_list =  $leave_list + $leave_list_non;

			$data['leave_list'] =$leave_list;
			$data['isHrHead'] 	= $isHrHead; 
			$data['status'] = $status; 
			$this->load->view("employeemod/seminar/seminar_history_manage",$data);
		}

		public function getSeminarAppListToManageProcess($code_request="SEMINAR",$status='',$datefrom='',$dateto='',$user='',$teachingType=''){
			$colhead = $isLastApprover = "";
			$prevcolstatus = ""	;
			$leave_list = array();
			$arr_aprvl_seq 	= array();
			$setup 			= $this->seminar->getAppSequence($code_request);
			if($setup->num_rows() > 0){
				$arr_aprvl_seq = $this->seminar->sortApprovalSeq($setup->row(0));
			}

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
				$temp_res = $this->seminar->getSeminarAppListToManage($user, $arr['colhead'], $arr['colstatus'], $status, $arr['prevcolstatus'], $datefrom, $dateto,$teachingType,$code_request,$arr['seq_count']);
				
				if($temp_res->num_rows() > 0){
					foreach ($temp_res->result() as $key => $row) {
						$leave_list[$row->aid] = array('data_list'=>$row,'colhead'=>$arr['colhead'],'colstatus'=>$arr['colstatus'],'prevcolstatus'=>$arr['prevcolstatus'],'isLastApprover'=>$arr['isLastApprover'],'code_request'=>$arr['code_request']);
					}
				}
			}

			return $leave_list;
		}

		function saveSeminarStatusChange(){

			$data_array = $this->input->post();
			foreach($data_array as $base_id => $data_values){

				$seminarid 		= $data_values['seminarid'];
				$base_id 		= $data_values['base_id'];
				$status 		= $data_values['status'];
				$endorse 		= isset($data_values['status_desc']) ? $data_values['status_desc'] : "";
				$colhead 		= $data_values['colhead'];
				$isLastApprover = $data_values['isLastApprover'];
				$code_request 	= $data_values['code_request'];
				$colstatus 		= substr($colhead, 0, -4). 'status';
				$coldate 		= substr($colhead, 0, -4). 'date';

				$user = $this->session->userdata('username');

				/*update leave_app_base data*/
				$datesetfrom = isset($data_values['datesetfrom']) ? $data_values['datesetfrom'] : "";
				$datesetto = isset($data_values['datesetto']) ? $data_values['datesetto'] : "";
				$timefrom = isset($data_values['timefrom']) ? $data_values['timefrom'] : "";
				$timeto = isset($data_values['timeto']) ? $data_values['timeto'] : "";

				if(isset($data_values['update'])) $this->seminar->updateSeminarAppBaseData($datesetfrom,$datesetto,$timefrom,$timeto,$base_id); 
				/*end*/
				
				$res = $this->seminar->saveSeminarStatusChange($user,$seminarid,$status,$colstatus,$coldate,$colhead,$isLastApprover,$base_id, "", $endorse);

				if(!$isLastApprover && $status == 'APPROVED'){ ///< get next in sequence with same head id

					$arr_aprvl_seq 	= array();
					$setup 			= $this->seminar->getAppSequence($code_request);
					if($setup->num_rows() > 0){
						$arr_aprvl_seq = $this->seminar->sortApprovalSeq($setup->row(0));
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
									$res_tmp = $this->seminar->saveSeminarStatusChange($user,$seminarid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$base_id,$prev_colhead,$endorse);
								}
							}else{
								$res_tmp = $this->seminar->saveSeminarStatusChange($user,$seminarid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$base_id,"",$endorse);

							}

						}
					}

				}
			}

			echo json_encode($res);
		}

		public function validateInhouseSeminar(){
			$res = "";
			$data['id'] = 0;
			$toks = $this->input->post("toks");
			$data = $toks ? Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks )) : $data = $this->input->post();
			$data['time_from'] = date("H:i", strtotime($data['time_from']));
			$data['time_to'] = date("H:i", strtotime($data['time_to']));
			// $data["attendees"] = $toks ? $data['attendees[]'] : $data["attendees"];
			$data["attendeesDept"] = $toks ? $data['attendeesDept[]'] : $data["attendeesDept"];
			$data["attendeesOffice"] = $toks ? $data['attendeesOffice[]'] : $data["attendeesOffice"];
			if(!$toks) $data["attendeesDept"] = implode(",", $data["attendeesDept"]);
			unset($data['attendeesDept[]']);

			if(!$toks) $data["attendeesOffice"] = implode(",", $data["attendeesOffice"]);
			unset($data['attendeesOffice[]']);

			$data["employees"] = $toks ? $data['employees[]'] : $data["employees"];
			if(!$toks) $data["employees"] = implode(",", $data["employees"]);
			unset($data['employees[]']);

			$data["password"] = md5($data["password"]);
			if(!$data["id"]) $res = $this->seminar->addInhouseSeminar($data);
			else $res = $this->seminar->updateInhouseSeminar($data);

			if($res) $msg = "Successfully save inhouse seminar.";
			else $msg = "Failed to save inhouse seminar. ";
			echo json_encode(array("stat"=>$res, "msg"=>$msg, "query"=>$this->db->last_query(), "update"=>$data["id"]));
		}

		public function checkIfExisting(){
			$toks = $this->input->post("toks");
			$username = $toks ? $this->gibberish->decrypt( $this->input->post("username"), $toks ) : $this->input->post("username");
			$tbl_id = $toks ? $this->gibberish->decrypt( $this->input->post("tbl_id"), $toks ) : $this->input->post("tbl_id");
			$existing = $this->seminar->checkIfExisting($username);
			$counter = 0;
			foreach ($existing as $value) {
				if($username == $value['username'] && $tbl_id != $value['id']) $counter = $counter + 1;
			}
			echo $counter;
		}

		public function seminarAttendees(){
			$response = array();
			$user = $this->session->userdata("username");
			$dateemployed = $this->extensions->employeeDateEmployed($user);
			if($dateemployed == 0 || $dateemployed == '' || !$dateemployed) $dateemployed = date('Y-m-d');
			$datenow = $this->extensions->getServerTime();
			$dateemployed = new DateTime($dateemployed);
			$curr_date = new DateTime($datenow);
			$diff = $curr_date->diff($dateemployed);
			$diff = $diff->y + 1;
			$datenow = date('Y-m-d', strtotime($datenow. ' + 4 days'));

			$deptid = $this->extensions->getEemployeeCurrentData($user, 'deptid');
			$office = $this->extensions->getEemployeeCurrentData($user, 'office');

			$seminar_today = $this->seminar->isSeminarToday($datenow, $user, $diff, $deptid, $office);
			// echo "<pre>"; print_r($this->db->last_query()); die;
			$data["seminarList"] = Globals::seminarList();

			if($seminar_today->num_rows() > 0){
				$seminar_attendees = $seminar_today->row()->attendees;
				$seminar_attendees = explode(",", $seminar_attendees);

				$seminar_date = new DateTime($seminar_today->row()->date_from);
				$datenow_diff = $curr_date->diff($seminar_date);

				$data["month"] = date("l", strtotime($seminar_today->row()->date_from));
				$data["day"] = date("d", strtotime($seminar_today->row()->date_from));
				$data["year"] = date("F Y", strtotime($seminar_today->row()->date_from));
				$data["time_from"] = date("h:i A", strtotime($seminar_today->row()->time_from));
				$data["time_to"] = date("h:i A", strtotime($seminar_today->row()->time_to));
				$data["category"] = $seminar_today->row()->category;
				$data["workshop"] = $seminar_today->row()->workshop;
				$data["title"] = $seminar_today->row()->title;
				$data["location"] = $seminar_today->row()->location;
				$data["venue"] = $seminar_today->row()->venue;
				$data["id"] = $seminar_today->row()->id;
				$attendees = $this->seminarPollAttendees($data["id"]);
				$content = $this->load->view("includes/seminar_attendees", $data, true);
				// in_array($diff, $seminar_attendees) && $datenow_diff->d <= 5 &&
				if( !in_array($user, $attendees)) $response = array("show" => 1, "content" => $content);
				else $response = array("show" => 0, "content" => "");
			}
			echo json_encode($response);
		}

		public function validateSeminarPoll(){
			$data = $this->input->post();
			$data["employeeid"] = $this->session->userdata("username");
			echo $this->seminar->saveSeminarPoll($data);
		}

		public function seminarPollAttendees($id){
			$attendees_arr = array();
			$attendees = $this->seminar->seminarPollAttendees($id);
			foreach($attendees as $attendees_row){
				$attendees_arr[] = $attendees_row["employeeid"];
			}

			return $attendees_arr;
		}

		public function seminarAttendeesList(){
			$user = $this->session->userdata("username");
			$usertype = $this->session->userdata("usertype");
			$toks = $this->input->post("toks");
			$data['isgoing'] = $toks ? $this->gibberish->decrypt( $this->input->post("isgoing"), $toks ) : $this->input->post("isgoing");
			$seminartype = $toks ? $this->gibberish->decrypt( $this->input->post("seminartype"), $toks ) : $this->input->post("seminartype");
			$where_clause = "";
			$encryption = "";
			if($usertype == "EMPLOYEE"){
				$office_under = $this->extensions->getAllOfficeUnder($user);
				$office_under = "'".implode("','", $office_under). "'";
				$where_clause .= " AND office IN ($office_under) ";
			}else{
				if($seminartype) $where_clause .= " AND e.level = '$seminartype'";
			}
			if($data['isgoing'] == "1") $where_clause .= " AND isgoing = '1' ";
			if($data['isgoing'] == "0") $where_clause .= " AND isgoing = '0' ";
			$data["record"] = $this->seminar->seminarAttendeesList($where_clause); 

			$str = $this->db->last_query();
   			$encrypted_string = base64_encode($str);
			$data['lastQuery'] = $encrypted_string;
			echo $this->load->view("employeemod/inhouse_seminar_details", $data);
		}

		public function seminarEmpAttendedList(){
			$attendedEmployee = array();
			$toks = $this->input->post("toks");
			$seminartype = $toks ? $this->gibberish->decrypt( $this->input->post("seminartype"), $toks ) : $this->input->post("seminartype");
			$seminarid = $toks ? $this->gibberish->decrypt( $this->input->post("seminarid"), $toks ) : $this->input->post("seminarid");
			$data['attendedEmployee'] = $this->seminar->getAttendedEmployee($seminartype, $seminarid);
			$seminar_id = $employeeid = '~';
			foreach ($data['attendedEmployee'] as $key => $value) {
				$attendedEmployee[$value['id']][$value['userid']][] = $value;
			}
			$data['attendedEmployee'] = $attendedEmployee;
			echo $this->load->view("employeemod/inhouse_seminar_empattended_list", $data);
		}

		public function annualSeminarReport(){
			$data['sy'] = $this->seminar->getSchoolYear('ORDER BY 2 DESC');
			echo $this->load->view("employeemod/annualSeminarReportModal", $data);
		}

		public function attendeesMarkread(){
			$id = $this->input->post("id");
			echo $this->seminar->attendeesMarkread($id);
		}

		public function seminarAttendeesPFDReport(){
			$formdata = $this->input->get("formdata");
			$formdata = base64_decode(urldecode($formdata));
			$data = Globals::convertFormDataToArray($formdata, $toks );
			$toks = $data['toks'];
			if($toks){
				foreach ($data as $key => $value) {
					$data[$key] = $this->gibberish->decrypt( $value, $toks );
				}
			}
			if($data["isgoing"] != 2){
				$this->load->view('forms_pdf/inhouseseminarattendees', $data);
			}else{
				$attendedEmployee = array();
				$data['attendedEmployee'] = $this->seminar->getAttendedEmployee($data["seminartype"], $data["seminarid"]);
				$seminar_id = $employeeid = '~';
				foreach ($data['attendedEmployee'] as $key => $value) {
					$attendedEmployee[$value['id']][$value['userid']][] = $value;
				}
				$data['attendedEmployee'] = $attendedEmployee;
				$this->load->view("forms_pdf/inhouse_seminar_empattended_list", $data);
			}
		}

		public function seminarAttendeesEXCELReport(){
			$formdata = $this->input->get("formdata");
			$formdata = base64_decode(urldecode($formdata));
			$data = Globals::convertFormDataToArray($formdata, $toks );
			$toks = $data['toks'];
			if($toks){
				foreach ($data as $key => $value) {
					$data[$key] = $this->gibberish->decrypt( $value, $toks );
				}
			}
			if($data["isgoing"] != 2){
				$this->load->view('reports_excel/inhouseseminarattendees', $data);
			}else{
				$attendedEmployee = array();
				$data['attendedEmployee'] = $this->seminar->getAttendedEmployee($data["seminartype"], $data["seminarid"]);
				$seminar_id = $employeeid = '~';
				foreach ($data['attendedEmployee'] as $key => $value) {
					$attendedEmployee[$value['id']][$value['userid']][] = $value;
				}
				$data['attendedEmployee'] = $attendedEmployee;
				$this->load->view("reports_excel/inhouse_seminar_empattended_list", $data);
			}
		}

		public function attendedEmployeePFDReport(){
			$formdata = $this->input->get("formdata");
			$formdata = base64_decode(urldecode($formdata));
			$data = Globals::convertFormDataToArray($formdata, $toks );
			$toks = $data['toks'];
			if($toks){
				unset($data['toks']);
				foreach ($data as $key => $value) {
					$data[$key] = $this->gibberish->decrypt( $value, $toks );
				}
			}
			list($data['month_from'], $data['month_to']) = $this->seminar->getSYMonth($data['year']);
			$data['employees'] = explode(',', $data['employees[]']);
			if($data['employees[]'] && $data['employees[]'] !== 'null' && $data['employees[]'] != 'all'){
	            $data['employees'] = explode(',', $data['employees[]']);
	            unset($data['employees[]']);
	            foreach ($data['employees'] as $key => $value){
	                $value = str_replace("'", "",$value);
	                if($key == 0) $data['employees'] = "'".$value."'";
	                else $data['employees'] .= ",'".$value."'";
	            }
	        }
	        $data['attendees'] = explode(',', $data['attendees']);
			$this->load->view('forms_pdf/inhouse_seminar_attended_employee', $data);
		}

		public function attendedEmployeeEXCELReport(){
			$formdata = $this->input->get("formdata");
			$formdata = base64_decode(urldecode($formdata));
			$data = Globals::convertFormDataToArray($formdata, $toks );
			$toks = $data['toks'];
			if($toks){
				unset($data['toks']);
				foreach ($data as $key => $value) {
					$data[$key] = $this->gibberish->decrypt( $value, $toks );
				}
			}
			list($data['month_from'], $data['month_to']) = $this->seminar->getSYMonth($data['year']);
			$data['employees'] = explode(',', $data['employees[]']);
			if($data['employees[]'] && $data['employees[]'] !== 'null' && $data['employees[]'] != 'all'){
	            $data['employees'] = explode(',', $data['employees[]']);
	            unset($data['employees[]']);
	            foreach ($data['employees'] as $key => $value){
	                $value = str_replace("'", "",$value);
	                if($key == 0) $data['employees'] = "'".$value."'";
	                else $data['employees'] .= ",'".$value."'";
	            }
	        }
	        $data['attendees'] = explode(',', $data['attendees']);
			$this->load->view('reports_excel/inhouse_seminar_attended_employee', $data);
		}

		public function seminarAnnouncement(){
			$response = array();
			$user = $this->session->userdata("username");
			$dateemployed = $this->extensions->employeeDateEmployed($user);
			$deptid = $this->extensions->getEemployeeCurrentData($user, 'deptid');
			$office = $this->extensions->getEemployeeCurrentData($user, 'office');
			$datenow = $this->extensions->getServerTime();
			$datenow = date('Y-m-d', strtotime($datenow. ' + 5 days'));
			// echo "<pre>";print_r($datenow);die;
			$seminar_today = $this->seminar->seminarAnnouncement($datenow, $user, $deptid, $office);
			// echo "<pre>"; print_r($this->db->last_query()); die;

			$response["seminarList"] = Globals::seminarList();
			if($seminar_today->num_rows() > 0){
				foreach($seminar_today->result_array() as $seminar_todays){
					$seminar_attendees = $seminar_todays["attendees"];
					$seminar_attendees = explode(",", $seminar_attendees);
					$dateemployed = new DateTime($dateemployed);
					$curr_date = new DateTime($datenow);
					$diff = $curr_date->diff($dateemployed);

					$seminar_date = new DateTime($seminar_todays["date_from"]);
					$datenow_diff = $curr_date->diff($seminar_date);
					if($datenow_diff->d <= 5){
						$response["record"][$seminar_todays["id"]]["year"] = date("Y", strtotime($seminar_todays["date_from"]));
						$response["record"][$seminar_todays["id"]]["day"] = date("D", strtotime($seminar_todays["date_from"]));
						$response["record"][$seminar_todays["id"]]["month"] = date("M d", strtotime($seminar_todays["date_from"]));
						$response["record"][$seminar_todays["id"]]["time_from"] = date("H:i A", strtotime($seminar_todays["time_from"]));
						$response["record"][$seminar_todays["id"]]["time_to"] = date("H:i A", strtotime($seminar_todays["time_to"]));
						$response["record"][$seminar_todays["id"]]["category"] = $seminar_todays["category"];
						$response["record"][$seminar_todays["id"]]["workshop"] = $seminar_todays["workshop"];
						$response["record"][$seminar_todays["id"]]["title"] = $seminar_todays["title"];
						$response["record"][$seminar_todays["id"]]["location"] = $seminar_todays["location"];
						$response["record"][$seminar_todays["id"]]["id"] = $seminar_todays["id"];
					}

					$dateemployed = $curr_date = "";
				}
			}
			$this->load->view("includes/seminar_announcement", $response);
		}

		public function attendeesAdminNotifCount(){
			echo $this->seminar->attendeesAdminNotifCount();
		}

		function seminar_details(){
			$seminarid = $this->input->post("seminarid");
			$data['seminardetails'] = $this->seminar->seminarDetails($seminarid);
			if(count($data["seminardetails"]) > 0) $data["seminardetails"] = Globals::resultarray_XHEP($data["seminardetails"]);
			$this->load->view("employeemod/seminar_details", $data);
		}

		function loadAttendees(){
			$option = "<option value='all'>Select All Employee</option>";
			$where_clause = 'WHERE 1 ';
			$datenow = date('Y-m-d');
			$currentTime = new DateTime(date('Y-m-d', strtotime($this->extensions->getServerTime())));
			$toks = $this->input->post("toks");
			$year_attendees = $toks ? $this->gibberish->decrypt($this->input->post('year_attendees'), $toks) : $this->input->post("year_attendees"); 
			$employees = $toks ? $this->gibberish->decrypt($this->input->post('employees'), $toks) : $this->input->post("employees"); 
			$status = $toks ? $this->gibberish->decrypt($this->input->post('status'), $toks) : $this->input->post("status"); 
			if($status != "all" && $status != ''){
		        if($status=="1"){
		          $where_clause .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
		        }
		        if($status=="0"){
		          $where_clause .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
		        }
		        if(is_null($status)) $where_clause .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
		     }
		    $year_attendees = explode(',', $year_attendees);
		    $employees = explode(',', $employees);
		    $records = $this->employee->load201sort($where_clause);
			foreach($records as $row){
				$dateemployed = new DateTime($row['dateemployed']);
                $yearOfService = $dateemployed->diff($currentTime)->y + 1;
                if(in_array($yearOfService, $year_attendees)){
                	$option .= "<option value='".$row['employeeid']."' ".(in_array($row['employeeid'], $employees) ? 'selected' : '')." >".$row['employeeid'].' - '.$row['lname'].", ".$row['fname']." ".$row['mname']."</option>";
                }
		    }
		    echo $option;
		}

		function loadAttendeesNew(){
			$option = "<option value='all'>Select All Employee</option>";
			$where_clause = 'WHERE 1 ';
			$datenow = date('Y-m-d');
			$currentTime = new DateTime(date('Y-m-d', strtotime($this->extensions->getServerTime())));
			$toks = $this->input->post("toks");
			$department = $toks ? $this->gibberish->decrypt($this->input->post('department'), $toks) : $this->input->post("department"); 
			$office = $toks ? $this->gibberish->decrypt($this->input->post('office'), $toks) : $this->input->post("office"); 
			$employees = $toks ? $this->gibberish->decrypt($this->input->post('employees'), $toks) : $this->input->post("employees"); 
			$status = "1"; 
			if($status != "all" && $status != ''){
		        if($status=="1"){
		          $where_clause .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
		        }
		        if($status=="0"){
		          $where_clause .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
		        }
		        if(is_null($status)) $where_clause .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
		     }
		    $employees = explode(',', $employees);
		    if($department && $department != 'all' && $department != 'null') $where_clause.= "AND FIND_IN_SET(deptid, '$department')";
		    if($office && $office != 'all' && $office != 'null') $where_clause.= "AND FIND_IN_SET(office, '$office')";
		    $records = $this->employee->load201sort($where_clause);
			foreach($records as $row){
                	$option .= "<option value='".$row['employeeid']."' ".(in_array($row['employeeid'], $employees) ? 'selected' : '')." >".$row['employeeid'].' - '.$row['lname'].", ".$row['fname']." ".$row['mname']."</option>";
		    }
		    echo $option;
		}

	}