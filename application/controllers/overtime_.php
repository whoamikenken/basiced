<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Overtime_ extends CI_Controller {

	private $emp_office = '';
	private $emp_ttype = '';

	/**
	 * Loads overtime model everytime this class is accessed.
	 */
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		$this->load->model('overtime');
	}

	/**
	 * Loads overtime application view passing list of employees under the user's department. 
	 *
	 * @return view
	 */

	function convertFormDataToArray($formdata){
		$data_arr = array();
		$formdata = explode("&", $formdata);
		foreach($formdata as $row){
			list($key, $value) = explode("=", $row);
			$data_arr[$key] = $value;
		}

		return $data_arr;
	}

	function loadApplyOTForm(){
		$this->load->model('employee');
		$this->load->model('utils');
		$post = $this->input->post();
		$data = array();

		$id 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		if($id){
			$data 		= $this->overtime->getOTDetails($id);
			$scheddisp  = $this->getStartEndtimePerDay($data['dfrom'],$data['dto'],$data['employeeid']);
			$data['sched'] = $scheddisp['scheddisp'];
		}
			
		// echo "<pre>"; print_r($data);

		$data['office'] 	= $this->employee->getempdatacol('office');
		$data['emplist'] 	= $this->utils->getEmplist($data['office']);
		unset($data['emplist']['']);
		$data['officelist'] 	= $this->utils->getOffice();
		$this->load->view("employeemod/overtimeapply",$data);
	}

	/**
	 * Generate list of employees under a specific department.
	 *
	 * @return string
	 */
	function getEmplist(){
		$this->load->model('utils');
		$toks 	= $this->input->post('toks');
		$office 	= $this->input->post('deptid');
		if($toks) $office = $this->gibberish->decrypt($office, $toks);
		$emplist 	= $this->utils->getEmplist($office);
		echo $this->constructOptionSelect($emplist, 'Select an employee');
	}

	/**
	 * Generate an html option select with given data.
	 *
	 * @param array $data list for options
	 * @param string $caption (Default: "")
	 *
	 * @return string
	 */
	function constructOptionSelect($data, $caption=""){
		$select = "";
		if($caption) $select .= "<option value=''>$caption</option>"; 
		foreach ($data as $key => $value) {
			$select .= "<option value='$key'>$value</option>";
		}
		return $select;
	}

	/**
	 * Loads overtime history view with list of employee's overtime applications.
	 *
	 * @return view
	 */
	function getEmpOTHistory(){
        $user 		= $this->session->userdata("username");
        $post 		= $this->input->post();
        $datefrom 	= isset($post['datefrom']) 	? $post['datefrom'] : "";
		$dateto 	= isset($post['dateto']) 	? $post['dateto'] 	: "";
		$status 	= isset($post['status']) 	? $post['status'] 	: "";
		$isread 	= isset($post['isread']) 	? $post['isread'] 	: "";
		$action 	= isset($post['action']) 	? $post['action'] 	: "";


		$data['OT_list'] = array();

		//result after loading the pages.
		if ($action == "load") {
			$data['OT_list'] = $this->overtime->getEmpOTHistory($user,$datefrom, $dateto, '', '' ,$isread);	
			if (sizeof($data['OT_list']) == 0) {
				$data['OT_list'] = $this->overtime->getEmpOTHistory($user, $datefrom, $dateto,'PENDING', '' ,'1');
			}
		}
		//result in history after applying for application
		else if($action == "apply")
		{
			$pending = $this->overtime->getEmpOTHistory($user,$datefrom, $dateto,'PENDING', '' ,'1');
			if ($pending)$data['OT_list'] = $this->overtime->getEmpOTHistory($user, $datefrom, $dateto,'PENDING', '' ,'1');
			else
			$data['OT_list'] = $this->overtime->getEmpOTHistory($user,$datefrom, $dateto, '', '' ,$isread);	
		}
		$data['stat'] = $status;
        if(sizeof($data['OT_list']) == 0)  $data['OT_list'] = $this->overtime->getEmpOTHistory($user, $datefrom, $dateto, $status, '' ,$isread);
        $this->load->view("employeemod/overtimehistory",$data);
	}

	/**
	 * Loads specific view based on input post $view passing overtime details.
	 *
	 * @return view
	 */
	function getOTDetails(){
		$this->load->model('utils');
		$post 		= $this->input->post();
		$total_app = '';
		$id 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		// $employeeid	= isset($post['code']) 	? $post['code'] 	: "";
		$view 		= isset($post['view']) 		? $post['view'] 	: "";
		$colhead 	= isset($post['colhead']) 	? $post['colhead'] 	: "";
		$colstatus 	=  $colhead ? (substr($colhead,0,-4) . 'status') : '';

		$data 		= $this->overtime->getOTDetails($id,$colstatus);
		$scheddisp  = $this->getStartEndtimePerDay($data['dfrom'],$data['dto'],$data['employeeid']);
		$attendance = $this->overtime->getAttendance($data['employeeid'], $data['dfrom'], $data['dto']);
		$tstart 	= date("H:i:s", strtotime($data['tstart']));
		$tend 		= date("H:i:s", strtotime($data['tend']));
		$data['sched'] = $scheddisp['scheddisp'];

		if($data['total_approved'] == '' && $data['status'] == "APPROVED"){
			if($attendance->num_rows() > 0){
				foreach ($attendance->result_array() as $key => $value) {
					$timein 	= date("H:i:s", strtotime($value['timein']));
					$timeout 	= date("H:i:s", strtotime($value['timeout']));
					if((strtotime($timein) <= strtotime($tstart)) && (strtotime($timeout) >= strtotime($tend))){
						$tmp = $this->attcompute->exp_time($data['total']);
						$tmp_tot = $tmp/* - ($tmp % 1800)*/;
						if($tmp_tot < 3600) $tmp_tot = 0;
						$data['total_approved'] = $this->attcompute->sec_to_hm($tmp_tot);
					} 
					else{
						$time1 = new DateTime($tstart);
						$time2 = new DateTime($tend);
						$interval = $time1->diff($time2);
						$total = $interval->format('%H:%i');
						$tmp = $this->attcompute->exp_time($total);
						$tmp_tot = $tmp/* - ($tmp % 1800)*/;
						if($tmp_tot < 3600) $tmp_tot = 0;
						$data['total_approved'] = $this->attcompute->sec_to_hm($tmp_tot);
					} 
 				}
			}
			
		}

		///< logs are validated for HR
		if($colhead=='hrhead' || $data['total_approved'] == ''){
			$data['ot_logs'] 		= $this->overtime->getOTDetailsForHR($data);
			$ot_logs = $data['ot_logs'];
			foreach ($ot_logs as $date => $log_detail) {
                if($this->attcompute->exp_time($log_detail['otsubtotal']) < 3600){
                	$data['total_approved'] = "00:00";
                }else{
            		$data['total_approved'] = $log_detail['creditedOT'];
            	}
            }
		}

		// echo "<pre>"; print_r($data) ; die;
		$data["idkey"] = $id;
		$data['job'] 	 		= $this->input->post("job");
		// $data['employeeid'] 	= $employeeid;
		$data['colhead'] 		= $colhead;
		$data['isLastApprover'] = $this->input->post("isLastApprover");
		$data['code_request'] = $this->input->post("code_request");
		$this->load->view("employeemod/$view",$data);
	}


	/**
	 * Loads manage overtime details view passing list of employees with their overtime app details and other approver necessary data.
	 *
	 * @return view
	 */
	function getOTAppListToManage(){
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
		$hrhead = $this->utils->getDeptHead('head','HR');
		if($user == $hrhead) $isHrHead = true;

		///< for regular employee
		$ot_list = array();
		///< --------------------------(condition added for Campus Principal which is ommitted for non teaching)-------------------------------------------
		$ot_list = $this->getOTAppListToManageProcess('OT',$status,$datefrom,$dateto,$user,'teaching',$deptid,$office);
		$ot_list = $this->getOTAppListToManageProcess('OTHEAD',$status,$datefrom,$dateto,$user,'teaching',$deptid,$office);
		$ot_list = $this->getOTAppListToManageProcess('OTNON',$status,$datefrom,$dateto,$user,'nonteaching',$deptid,$office);
		$ot_list = $this->getOTAppListToManageProcess('OTHEADNON',$status,$datefrom,$dateto,$user,'nonteaching',$deptid,$office);

		/*if(sizeof($ot_list_teaching) > 0) 	$ot_list =  array_merge($ot_list, $ot_list_teaching);
		if(sizeof($ot_list_non) > 0) 		$ot_list =  array_merge($ot_list, $ot_list_non);
		if(sizeof($ot_list_teaching_head) > 0) 	$ot_list =  array_merge($ot_list, $ot_list_teaching_head);
		if(sizeof($ot_list_non_head) > 0) 		$ot_list =  array_merge($ot_list, $ot_list_non_head);*/

		$data['ot_list'] =$ot_list;
		$data['isHrHead'] 	= $isHrHead;
		$data['status'] = $status;
		$data["deptid"] = $deptid;
		$data["office"] = $office;
		$this->load->view("employeemod/mailotapp_details",$data);
	}


	function getOTAppListToManageProcess($code_request="OT",$status='',$datefrom='',$dateto='',$user='',$teachingType='',$deptid='',$office=''){
		$colhead = $isLastApprover = "";
		$prevcolstatus = ""	;
		$ot_list = array();
		$arr_aprvl_seq 	= array();
		$setup 			= $this->overtime->getAppSequence($code_request);

		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->overtime->sortApprovalSeq($setup->row(0));
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
			$temp_res = $this->overtime->getOTAppListToManage($user, $arr['colhead'], $arr['colstatus'], $status, $arr['prevcolstatus'], $datefrom, $dateto,$teachingType,$arr['seq_count'],$deptid,$office);
			if($temp_res->num_rows() > 0){
				foreach ($temp_res->result() as $key => $row) {
					$ot_list[$row->aid] = array('data_list'=>$row,'colhead'=>$arr['colhead'],'colstatus'=>$arr['colstatus'],'prevcolstatus'=>$arr['prevcolstatus'],'isLastApprover'=>$arr['isLastApprover'],'code_request'=>$arr['code_request']);
				}
			}
		}

		return $ot_list;
	}

	/**
	 * Loads approval status view based on OT id passing array of approval details.
	 *
	 * @return view
	 */
	function getApprovalSeqStatus(){
		///< display position, name, status, date updated
		$this->load->model('utils');
		$post 		= $this->input->post();
		$otid 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		$data['approver_admin'] = '';
		// $position_names = array('dhead'=>'Department Head','chead'=>'Cluster Head','hrhead'=>'HR Director','fdhead'=>'Financial Director','bohead'=>'Budget Officer','phead'=>'President','uphead'=>'University Physician');
		$position_names = $this->utils->getRequestApprover();

		$arr_aprvl_seq 	= array();
		$setup = $this->overtime->getAppSequencePerOT($otid);
		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->sortApprovalSeqPerOT($setup->row(0));
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
	 * @param stdClass Object $setup approval sequence details of specific OT
	 *
	 * @return array
	 */
	function sortApprovalSeqPerOT($setup){
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
	 * Saves overtime app status change made by approver.
	 *
	 * @return string
	 */
	function saveOTStatusChange(){
		$next_approver = "";
		$count = 0;
		///< otid,status,colhead,isLastApprover -- if last na ,set status
		$otid 			= $this->input->post('otid');
		$base_id 			= $this->input->post('base_id');
		$status 		= $this->input->post('status');
		$ottotal 		= $this->input->post('ottotal');
		$colhead 		= $this->input->post('colhead');
		$isLastApprover = $this->input->post('isLastApprover');
		$code_request 	= $this->input->post('code_request');
		$colstatus 		= substr($colhead,0,-4) . 'status';
		$coldate 		= substr($colhead,0,-4) . 'date';
		$remarks		= $this->input->post('remarks');
		$user = $this->session->userdata('username');

		$res = $this->overtime->saveOTStatusChange($user,$otid,$status,$colstatus,$coldate,$colhead,$isLastApprover,$base_id,$ottotal,$remarks);

		if(!$isLastApprover && $status == 'ENDORSED'){ ///< get next in sequence with same head id
			$arr_aprvl_seq 	= array();
			$setup 			= $this->overtime->getAppSequence($code_request);
			if($setup->num_rows() > 0){
				$arr_aprvl_seq = $this->overtime->sortApprovalSeq($setup->row(0));
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
							$res_tmp = $this->overtime->saveOTStatusChange($user,$otid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$base_id,$ottotal,$remarks,$prev_colhead);
						}
					}else{
						$res_tmp = $this->overtime->saveOTStatusChange($user,$otid,$status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover_tmp,$base_id,$ottotal,$remarks);
					}

				}

				if($res){
					if(isset($arr_aprvl_seq[$count+1]["head_id"]) && $arr_aprvl_seq[$count+1]["head_id"]) $next_approver = $arr_aprvl_seq[$count+1]["head_id"];
					elseif(isset($arr_aprvl_seq[$count+2]["head_id"]) && $arr_aprvl_seq[$count+2]["head_id"]) $next_approver = $arr_aprvl_seq[$count+2]["head_id"];
				}

				// $this->extensions->sendEmailToNextApprover($next_approver);

			}

		}

		if($res) echo json_encode(array('err_code'=>0,'msg'=>"Success! Status now is : $status"));
		else 	 echo json_encode(array('err_code'=>2,'msg'=>'Failed to set status.'));
		
	}

	/**
	 * Saves new overtime application.
	 *
	 * @return string
	 */
	function saveOTApp($data = array()){
		$this->load->model('extras');
		$this->load->model('utils');

		$return 	= $empcount = "";
		$arr_data_failed = array();
		
		if(sizeof($data) == 0){
			$toks = $this->input->post("toks");
			$formdata 		= $this->gibberish->decrypt($this->input->post("form_data"), $toks);
			$data = $this->convertFormDataToArray($formdata);
			foreach($data as $key => $val){
				$data[$key] = str_replace("%3A", ":", $data[$key]);
				$data[$key] = str_replace("+", " ", $data[$key]);
			}
		}

		$otid 		= isset($data['otid']) 			? $data['otid']  		: "";
		$base_id 	= isset($data['base_id']) 		? $data['base_id']  	: "";
		$office 	= isset($data['departments']) 	? $data['departments']  : "";
		$allemp 	= isset($data['allemp'])		? $data['allemp'] 		: "";
		$emplist 	= isset($data['emplist'])		? $data['emplist'] 		: "";
		$datefrom 	= isset($data['datesetfrom']) 	? $data['datesetfrom'] 	: "";
		$dateto 	= isset($data['datesetto']) 	? $data['datesetto'] 	: "";
		$dateto 	= $datefrom;
        $tstart 	= isset($data['tfrom']) 		? date("H:i:s",strtotime($data['tfrom'])) : "";
        $tend 		= isset($data['tto']) 			? date("H:i:s",strtotime($data['tto'])) : "";
        if($data['tot'] != 0) $total = $data['tot']; else $total = $data['tots'];
		$reason 		= isset($data['reason'])		? $data['reason']	  	: "";
		$reason = str_replace("+", " ", $reason);
		$user 		= $this->session->userdata('username');
		// $total 		= $this->formatTotalHours($total);

		$arr_emplist = array();
		$empid = '';
        if($allemp){
        	$arr_emplist = $this->utils->getEmpIDs($office);
        }else{
        	if($emplist) $arr_emplist = explode(",", $emplist);

        	$empid = $arr_emplist[0];
        }

        ///< sort by teaching type -- different setup per teaching type
        $arr_emplist_tnt = array();
        foreach ($arr_emplist as $employeeid) {
        	$det_res = $this->utils->getSingleTblData('employee',array('teachingType','office'),array('employeeid'=>$employeeid));
        	if($det_res->num_rows() > 0){
        		$teachingType = $det_res->row(0)->teachingType;
        		$office1 	  = $det_res->row(0)->office;
        	}

        	if(!array_key_exists($teachingType, $arr_emplist_tnt)) $arr_emplist_tnt[$teachingType] = array();
        	array_push($arr_emplist_tnt[$teachingType], array('employeeid'=>$employeeid,'office'=>$office1));
        }

        $dhead = $this->overtime->getDeptHead('head',		$office);	
        $chead = $this->overtime->getDeptHead('divisionhead',$office);	
        $hrhead = $this->overtime->getDeptHead('head',		'HR');


        $empcount = 0;

        foreach ($arr_emplist_tnt as $tnt => $emp_list_temp) {
        	$office = $emp_list_temp[0]['office'];

	        $dhead = $this->overtime->getDeptHead('head',		$office);	
	        $chead = $this->overtime->getDeptHead('divisionhead',$office);	

        	list($empcount_temp,$failed_temp,$return_temp) = $this->saveOTAppProcess($tnt,$office,$datefrom, $dateto, $tstart, $tend, $total, $reason, $dhead,$chead,$hrhead,$emp_list_temp,$user,$otid,$base_id, $empid);
        	$empcount += $empcount_temp;
        	$arr_data_failed = array_merge($arr_data_failed,$failed_temp);
        	if($return_temp) $return .= $return_temp;
        }


        if($empcount){
			$return .= "$empcount employee(s) successfully applied.";
		}else{ 			$return .= "Failed to save application.";}

        echo $return;
    }

    function saveOTAppProcess($teachingType='teaching',$office='',$datefrom='', $dateto='', $tstart='', $tend='', $total='', $reason='', $dhead='',$chead='',$hrhead='',$arr_emplist=array(),$user='',$otid='',$base_id_edit='', $employeeid=''){
    	$this->load->model('utils');
    	
    	$code_request = 'OT';
    	$return = '';
    	$empcount = 0;
    	$arr_data_failed = array();
    	$dstatus = "PENDING";
    	$ddate = "";
    	$dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";

    	$isHead = $this->utils->checkIfHead(($employeeid) ? $employeeid : $user);

    	///< head will look up on head code setup
    	$forhead = '';
    	if($user==$dhead || $user==$chead || $isHead) $forhead = 'HEAD';
    	$code_request .= $forhead;

    	if($teachingType == 'nonteaching') $code_request = $code_request.'NON';
    	if(!$forhead && $office == 'LIB') $code_request = str_replace('NON', '', $code_request); ///< #ICA-HYPERION21711

    	//get seq from form
    	$seq = $this->overtime->getAppSequence($code_request);
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

    		///< check if user is depthead ,if head set dstatus to approved
    		if($dhead == $user){
    			$dstatus = "APPROVED";
    			$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");
    		}


    		///< sort by campus && get campus head
    		$campuslist = array();
    		foreach ($arr_emplist as $det) {
    			$campusid = $this->employee->getempdatacol('campusid',$det['employeeid']);
    			if(!array_key_exists($campusid, $campuslist)) 	$campuslist[$campusid] = array();
    			array_push($campuslist[$campusid], $det['employeeid']);
    		}

    		foreach ($campuslist as $campusid => $emplist) {
    			$cphead = '';
    			$c_res = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='$campusid'");
    			if($c_res->num_rows() > 0){
    				$cphead = $c_res->row(0)->campus_principal;
    			}
    			if($base_id_edit){
    				$this->overtime->modifyOVertimeApplication($datefrom, $dateto, $tstart, $tend, $total, $reason,$base_id_edit);
    				list($empcounttemp,$failed_temp) = $this->overtime->updateOTApp($user,$base_id_edit,$otid,$emplist,$teachingType, $dstatus, $ddate, $datefrom, $dateto, $tstart, $tend, $total, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq);
    				$empcount += $empcounttemp;
					$arr_data_failed = array_merge($arr_data_failed,$failed_temp);
    			}else{

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
	                    // if($val == 1) $this->extensions->sendEmailToNextApprover($approver_id);
	                }
    				
					///< insert
					$base_id = $this->overtime->insertBaseOTApp($user, $datefrom, $dateto, $tstart, $tend, $total, $reason, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq);					///< save base ot app
					
					if($base_id){
						list($empcounttemp,$failed_temp) = $this->overtime->insertOTAppEmpList($base_id, $emplist,$teachingType, $dstatus, $ddate, $user, $datefrom, $dateto, $tstart, $tend); ///< save emplist
						$empcount += $empcounttemp;
						$dateactive = $this->attcompute->employeeScheduleDateActive($user, $datefrom);
						$this->overtime->saveOTSched($base_id, $tstart, $tend, $dateactive);
						$arr_data_failed = array_merge($arr_data_failed,$failed_temp);
					}
					else 			$arr_data_failed = array_merge($arr_data_failed,$emplist);
				}
				$base_id = isset($base_id) ? $base_id : $base_id_edit;

		    	// if($empcount) $this->overtime->chkHRapproved($base_id,$dhead,$hrhead,$dseq);///< APPROVED IF THE HEAD IS HR -- Added 5-18-17
    						
    		}

    	}else{
    		$return = "No current setup for $teachingType overtime. ";
    	}

		return array($empcount,$arr_data_failed,$return);

    }

    /**
	 * Deletes overtime app base on ot id.
	 *
	 * @return string
	 */
    function deleteOTApp(){
    	$id = $this->input->post('id');
    	$res = $this->overtime->deleteOTApp($id);
    	echo $res;
    }
		
	function formatTotalHours($total=''){
		$d_time = '';
		if($total){
			$new = explode(':', $total);
			if(isset($new[1])){
				$d_time = ($new[0]) + ($new[1] / 60);
				$d_time = round($d_time, 2);
			}
		}
		return $d_time;
	}

	/**
	 * Generate an array of string dates between 2 dates
	 *
	 * @param string $start Start date
	 * @param string $end End date
	 * @param string $format Output format (Default: Y-m-d)
	 *
	 * @return array
	 */

	function getDatesFromRange($start, $end, $format = 'Y-m-d') {
	    $array = array();
	    $interval = new DateInterval('P1D');

	    $realEnd = new DateTime($end);
	    $realEnd->add($interval);

	    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

	    foreach($period as $date) { 
			$array[] = $date->format($format); 
	    }

	    return $array;
	}

	function displayStartEndtimePerDay(){
		$toks 	= $this->input->post('toks');
		$startdate 	= ($toks) ? $this->gibberish->decrypt($this->input->post('startdate'), $toks) : $this->input->post('startdate');
		$enddate 	= ($toks) ? $this->gibberish->decrypt($this->input->post('enddate'), $toks) : $this->input->post('enddate');
		$eidarr     = ($toks) ? $this->gibberish->decrypt($this->input->post('employeeid'), $toks) : $this->input->post('employeeid');

		$employeeid = '';
		if($eidarr){
			if(sizeof($eidarr) == 1) $employeeid = $eidarr[0];
		}
		
		if(!is_array($eidarr)) $employeeid = $eidarr;


		$enddate = $startdate;
		$ret_arr = $this->getStartEndtimePerDay($startdate, $enddate, $employeeid);
		echo json_encode($ret_arr);
	}

	function getStartEndtimePerDay($startdate='', $enddate='', $employeeid=''){
		$this->load->model('utils');
		$ret_arr = array();
		$scheddisp = '';
		$min_otstart = $max_to = '';

		if($employeeid){
			$dates_arr = $this->utils->getDatesFromRange($startdate,$enddate);
			$daysofwk_arr = array();
			foreach ($dates_arr as $date) {
			    $dayofwk = date('w', strtotime($date));
			    array_push($daysofwk_arr, $dayofwk);
			}

			$res = $this->overtime->getEmpSchedMinMaxTimePerday($employeeid,$enddate);
			$min_otstart = '';

			if($res->num_rows() > 0){
				foreach ($res->result() as $key => $row) {
				    if(in_array($row->idx, $daysofwk_arr)){
				        $scheddisp .= $row->DAYOFWEEK . ' ' . date('h:i A',strtotime($row->START)) . '-' . date('h:i A',strtotime($row->END)) . ' | ';

				        ///< for overtime min start time
				        if(!$min_otstart) $min_otstart = strtotime($min_otstart) > strtotime($row->END) ? $row->END : $row->START;
				        else $min_otstart = $row->END;
				    }
				}
			}
			if($scheddisp) $scheddisp = substr($scheddisp, 0, -3);

			///< get max timeout
			$max_to = $min_ti = '';
			$logs = $this->db->query("SELECT MIN(timein) AS timein, MAX(timeout) AS timeout FROM timesheet WHERE userid='$employeeid' AND  DATE_FORMAT(timein,'%Y-%m-%d') = '$startdate'");
			// echo $this->db->last_query();
			
			if($logs->num_rows > 0){
				if($logs->row(0)->timeout != null) $max_to = date('H:i:s',strtotime($logs->row(0)->timeout));
				if($logs->row(0)->timein != null)  $min_ti = date('H:i:s',strtotime($logs->row(0)->timein));
			}

			$office = $this->employee->getempdatacol('office',$employeeid);
			$isHoliday = $this->attcompute->isHolidayNew($employeeid,$startdate,$office ); 

			if(!$min_otstart || $isHoliday) $min_otstart = $min_ti;

		}
		$ret_arr = array('min_otstart'=>$min_otstart, 'max_timeout'=>$max_to, 'scheddisp'=>$scheddisp);
		return $ret_arr;
	}

	/**
	 * Saves new overtime application directly to overtime_request.
	 *
	 * @return string
	 */
	function saveOTAppHRDirect(){
		$toks = $this->input->post("toks");
		$formdata 		= $this->gibberish->decrypt($this->input->post("form_data"), $toks);
		$data = $this->convertFormDataToArray($formdata);
		foreach($data as $key => $val){
			$data[$key] = str_replace("%3A", ":", $data[$key]);
			$data[$key] = str_replace("+", " ", $data[$key]);
		}
		// echo "<pre>"; print_r($data); die;
		$allowApprover	= isset($data['allowApprover']) 			? $data['allowApprover'] : "";

		if(isset($data["modify"])) $this->overtime->deleteOvertimeRequest($data["id"]);
		// echo "<pre>"; print_r($data); die;
		if($allowApprover){
			$return  = $this->saveOTApp($data);
		}else{
			$return  = $this->saveOTAppHRDirectProcess($data);
		}

		echo $return;
	}

	///< @Angelica direct Approve
	function saveOTAppHRDirectProcess($data=array()){
		$this->load->model('extras');
		$this->load->model('utils');

		$return 	= $empcount = "";
		
		$office 	= isset($data['departments']) 	? $data['departments']  : "";
		$allemp 	= isset($data['allemp'])		? $data['allemp'] 		: "";
		$emplist 	= isset($data['emplist'])		? $data['emplist'] 		: "";
		$datefrom 	= isset($data['datesetfrom']) 	? $data['datesetfrom'] 	: "";
		$dateto 	= isset($data['datesetto']) 	? $data['datesetto'] 	: "";
        $tstart 	= isset($data['tfrom']) 		? date("H:i:s",strtotime($data['tfrom'])) : "";
        $tend 		= isset($data['tto']) 			? date("H:i:s",strtotime($data['tto'])) : "";
        if($data['tot'] != 0) $total = $data['tot']; else $total = $data['tots'];
		$reason 	= isset($data['reason'])		? $this->extras->clean($data['reason'])	  : "";
		$user 		= $this->session->userdata('username');

		$arr_emplist = array();
        if($allemp){
        	$arr_emplist = $this->utils->getEmpIDs($office);
        }else{
        	if($emplist) $arr_emplist = explode(",", $emplist);
        }

        $ret="Failed to save application.";
        if(sizeof($arr_emplist) > 0){
    		$hrhead = $this->utils->getDeptHead('head','HR');
    		if($hrhead){
    			$count = $this->overtime->saveOTAppHRDirect($arr_emplist, $user, $datefrom, $dateto, $tstart, $tend, $total, $reason, $hrhead);
	    			$ret = "$count employee(s)successfully applied.";

    		}else $ret = 'No setup for HR Head.';
    	}else $ret = 'No employee selected.';
    	
    	return $ret;
	}

	function getOTLogsDirect(){
		$this->load->model('utils');
		$data = $this->input->post();
		$toks = $data["toks"];
		$info = array();
		$info['employeeid'] = isset($data['emplist'])		? $data['emplist'][0] 		: "";
		$info['dfrom'] 		= isset($data['dfrom']) 	? $data['dfrom'] 	: "";
		$info['dto'] 		= isset($data['dto']) 	? $data['dto'] 	: "";
        $info['tstart'] 	= isset($data['tstart']) 		? date("H:i:s",strtotime($data['tstart'])) : "";
        $info['tend'] 		= isset($data['tend']) 			? date("H:i:s",strtotime($data['tend'])) : "";
        if($toks){
        	foreach($info as $key => $val){
        		if($key != "employeeid") $info[$key] = $this->gibberish->decrypt($val, $toks);
        	}
        }
        $info['ot_logs'] 		= $this->overtime->getOTDetailsForHR($info);

        $this->load->view('employeemod/overtime_logs',$info);

	}

	# ica-hyperion 21535
	# by justin (with e)
	function deleteOvertimeRequest(){
		$base_id = $this->input->post("base_id");

		$arr_query = $this->overtime->deleteOvertimeRequest($base_id);

		$return = "Successfully Deleted";
		foreach ($arr_query as $query) {
			if(!$query){
				$retun = "Failed to delete request..";
				break;
			}
		}

		echo $return;
	}
	# end of ica-hyperion 21535

	# for ica-hyperion 21668
	function saveOvertimeSetup(){
		$result 		 = array();
		$this->load->model('utils');
		$data 			 = $this->input->post();
		$toks = $data["toks"];
		unset($data["toks"]);
		foreach($data as $key => $val){
			if(!is_array($val)) $data[$key] = $this->gibberish->decrypt($val, $toks);
		}
		$arr_code_status = $data["status"];
		if($data["status"][0] === "all"){
			$q_code_status = $this->utils->getCodeStatus();
		    foreach ($q_code_status as $row) {	
				foreach ($data["ot_types"] as $ot_type => $ot_data) {
					$tbl_data = array(
						"code_status" 				=> $row->code,
						"ot_types"					=> $ot_type,
						"percent"					=> $this->gibberish->decrypt($ot_data["percent"], $toks),
						"excess_percent"			=> $this->gibberish->decrypt($ot_data["excess"], $toks),
						"regular_percent"			=> $this->gibberish->decrypt($ot_data["regular_percent"], $toks),
						"regular_percent_excess"	=> $this->gibberish->decrypt($ot_data["regular_excess"], $toks),
						"other_percent"				=> $this->gibberish->decrypt($ot_data["other_percent"], $toks),
						"other_percent_excess"		=> $this->gibberish->decrypt($ot_data["other_excess"], $toks)
					);
					$is_continue = true;
					if($data["id"] == "new") $is_continue = $this->overtime->isOvertimeSetupIsExist($row->code, $ot_type);
					
					if(!$is_continue) $result["error"][$row->code] = "is already exist!..";
					else{
						$q_save_overtime_setup = false;

						if($data["id"] == "new") $q_save_overtime_setup = $this->overtime->saveNewOvertimeSetup($tbl_data);
						else 					 $q_save_overtime_setup = $this->overtime->saveUpdateOvertimeSetup($row->code, $ot_type, $tbl_data);

						if(!$q_save_overtime_setup) $result["error"][$row->code] = "failed to save..";
					}
				}
			}
		}
		else{
			$arr_code_status = explode(",", $arr_code_status);
			foreach ($arr_code_status as $code_status) {
			foreach ($data["ot_types"] as $ot_type => $ot_data) {
				
				$tbl_data = array(
					"code_status" 				=> $code_status,
					"ot_types"					=> $ot_type,
					"percent"					=> $this->gibberish->decrypt($ot_data["percent"], $toks),
					"excess_percent"			=> $this->gibberish->decrypt($ot_data["excess"], $toks),
					"regular_percent"			=> $this->gibberish->decrypt($ot_data["regular_percent"], $toks),
					"regular_percent_excess"	=> $this->gibberish->decrypt($ot_data["regular_excess"], $toks),
					"other_percent"				=> $this->gibberish->decrypt($ot_data["other_percent"], $toks),
					"other_percent_excess"		=> $this->gibberish->decrypt($ot_data["other_excess"], $toks)
				);
				
				$is_continue = true;
				if($data["id"] == "new") $is_continue = $this->overtime->isOvertimeSetupIsExist($code_status, $ot_type);
				
				if(!$is_continue) $result["error"][$code_status] = "is already exist!..";
				else{
					$q_save_overtime_setup = false;

					if($data["id"] == "new") $q_save_overtime_setup = $this->overtime->saveNewOvertimeSetup($tbl_data);
					else 					 $q_save_overtime_setup = $this->overtime->saveUpdateOvertimeSetup($code_status, $ot_type, $tbl_data);

					if(!$q_save_overtime_setup) $result["error"][$code_status] = "failed to save!..";
				}
			}
		}
		}
		// echo $this->db->last_query(); die;
		$result["success"] = "Successfully Saved!..";
		$this->load->view("config/overtime_result", $result);
	}

	function loadOvertimeSetupList(){
		$data = array();

		$data["overtime_list"] = array();
		$q_overtime_setup = $this->overtime->getOvertimeSetupList();
		foreach ($q_overtime_setup as $row) {
			$data["overtime_list"][$row->code] = $row->description;
		}

		$this->load->view("config/overtime_list", $data);
	}

	function editOvertimeSetup(){
		$toks = $this->input->post('toks');
		$code_status = $this->gibberish->decrypt($this->input->post('code'), $toks);
		$data = array();
		$data["ot_types"] = array();
		$q_overtime_setup = $this->overtime->getOvertimeSetupInfo($code_status);
		foreach ($q_overtime_setup as $row) {
			$data["ot_types"][$row->ot_types] = array(
				"percent" 			=> $row->percent,
				"excess" 			=> $row->excess_percent,
				"regular_percent" 	=> $row->regular_percent,
				"regular_excess" 	=> $row->regular_percent_excess,
				"other_percent" 	=> $row->other_percent,
				"other_excess" 		=> $row->other_percent_excess
			);
		}

		echo json_encode($data);
	}

	function deleteOvertimeSetup(){
		$toks = $this->input->post('toks');
		$code_status = $this->gibberish->decrypt($this->input->post('code'), $toks);

		$q_delete_setup = $this->overtime->deleteOvertimeSetup($code_status);

		$result["success"] = "Successfully Deleted!..";
		$result["error"] = array();
		if(!$q_delete_setup) $result["error"][$code_status] = "failed to save!..";

		echo $result;
	}
	# end for ica-hyperion 21668

	function modifyOTManagementRequest(){
		$this->load->model("utils");
		$data = array();
		$data = $this->input->post();
		
		$data['office']   = $this->employee->getempdatacol('office');
		$data['emplist']  = $this->utils->getEmplist($data['office']);
		unset($data['emplist']['']);
		$data['deptlist']   = $this->utils->getDepartments();
		unset($data['deptlist']['']);

		$q_overtime = $this->overtime->getOvertimeAppRequest($data["id"]);
		if($q_overtime) $q_overtime = Globals::result_XHEP($q_overtime);
		foreach ($q_overtime as $row) {
			$data["fullname"] = $row->fullname;
			$data["employeeid"] = $row->employeeid;
			$data["dfrom"] = $row->dfrom;
			$data["dto"] = $row->dto;
			$data["tstart"] = date("h:i A", strtotime($row->tstart));
			$data["tend"] = date("h:i A", strtotime($row->tend));
			$data["total"] = $row->total;
			$data["reason"] = $row->reason;
			$ot_logs = $this->overtime->getOTDetailsForHR($data);
			foreach ($ot_logs as $date => $log_detail) {
                if($this->attcompute->exp_time($log_detail['otsubtotal']) < 3600){
                	$data['total_approved'] = "00:00";
                }else{
            		$data['total_approved'] = $log_detail['creditedOT'];
            	}
            }
		}

		#echo "<pre>"; print_r($data); echo "</pre>";
		$this->load->view("process/modify-ot-management", $data);
	}

	function viewOvertimeDetails(){
		$this->load->model("utils");
		$data = array();
		$data = $this->input->post();
		
		$data['office']   = $this->employee->getempdatacol('office');
		$data['emplist']  = $this->utils->getEmplist($data['office']);
		unset($data['emplist']['']);
		$data['deptlist']   = $this->utils->getDepartments();
		unset($data['deptlist']['']);

		$q_overtime = $this->overtime->getOvertimeAppRequest($data["id"]);
		if($q_overtime) $q_overtime = Globals::result_XHEP($q_overtime);
		foreach ($q_overtime as $row) {
			$data["fullname"] = $row->fullname;
			$data["employeeid"] = $row->employeeid;
			$data["dfrom"] = $row->dfrom;
			$data["dto"] = $row->dto;
			$data["tstart"] = date("h:i A", strtotime($row->tstart));
			$data["tend"] = date("h:i A", strtotime($row->tend));
			$data["total"] = $row->total;
			$data["reason"] = $row->reason;
			$ot_logs = $this->overtime->getOTDetailsForHR($data);
			foreach ($ot_logs as $date => $log_detail) {
                if($this->attcompute->exp_time($log_detail['otsubtotal']) < 3600){
                	$data['total_approved'] = "00:00";
                }else{
            		$data['total_approved'] = $log_detail['creditedOT'];
            	}
            }
		}

		#echo "<pre>"; print_r($data); echo "</pre>";
		$this->load->view("process/view-ot-management", $data);
	}

	function getTotalApprovedHours(){
		$data = $this->input->post();
		$total_approved = '';
		$ot_logs = $this->overtime->getOTDetailsForHR($data);
		foreach ($ot_logs as $date => $log_detail) {
            if($this->attcompute->exp_time($log_detail['otsubtotal']) < 3600){
            	$total_approved = "00:00";
            }else{
            	$total_approved = $log_detail['creditedOT'];
            }
        }

        echo $total_approved;
	}

	function hasFiledOT(){
    	$data = $this->input->post();
    	$toks = $data["toks"];
    	$employeeid = ($toks) ?  $this->gibberish->decrypt($data["employeeid"], $toks) : $data["employeeid"];
    	$datesetfrom = ($toks) ?  $this->gibberish->decrypt($data["datesetfrom"], $toks) : $data["datesetfrom"];
    	$datesetto = ($toks) ?  $this->gibberish->decrypt($data["datesetto"], $toks) : $data["datesetto"];
    	$timefrom = ($toks) ?  $this->gibberish->decrypt($data["timefrom"], $toks) : $data["timefrom"];
    	$timeto = ($toks) ?  $this->gibberish->decrypt($data["timeto"], $toks) : $data["timeto"];
    	echo $this->overtime->hasFiledOT($employeeid[0], $datesetfrom, $datesetto, $timefrom, $timeto);
    }

} //endoffiles