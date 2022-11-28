<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schedule_ extends CI_Controller {

	/**
	 * Loads schedule model everytime this class is accessed.
	 */
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		$this->load->model('schedule');
	}


	/**
	 * Loads application form.
	 *
	 * @return view
	 */
	function loadApplyCSForm(){
		$this->load->model('employee');
		$this->load->model('utils');
		
		$data['office'] 	= $this->employee->getempdatacol('office');

        $teaching_list      = $this->utils->getEmplist($data['office'],'','','teaching');
		$nonteaching_list 	= $this->utils->getEmplist($data['office'],'','','nonteaching');
        $data["emplist"]    = $this->utils->mergeTeachingAndNonTeachingList($teaching_list, $nonteaching_list);

        unset($data['emplist']['']);
        $data['scheddays']  = $this->schedule->getSchedDays();
        $data['official_schedlist'] = $this->schedule->getOfficialSchedList();
		$this->load->view("change_schedule/cs_apply",$data);
	}

    function validateFinalizedEmp(){
        $data       = $this->input->post();
        $eids       = isset($data['eids'])          ? $data['eids']         : "";
        $dfrom      = isset($data['dfrom'])         ? $data['dfrom']        : "";
        $arr_emplist = explode(",", $eids);
        $res = "Good";
        if(sizeof($arr_emplist) > 0){
            $res = $this->schedule->findFinalizedEmp($arr_emplist,$dfrom);
            if($res != "") $res = "Invalid! The date ('".$dfrom."') and the employee/s you have been selected are already finalized. \n\nList of Employee.\n".$res."\n\nYou must change the Employee list or the Date to active.";
        }
        echo $res;
    }
	function SCHEDactions()
	{
		$id = $this->input->post('id');
        $base_id = $this->input->post('base_id');
		$job = $this->input->post('job');
		echo $this->schedule->SCHEDactions($id,$job,$base_id);
	}
    function validateDelete()
    {
        $id = $this->input->post('id');
        $id=$this->schedule->deleteValidate($id);
    }
 
	function saveChangeSchedule()
    {
		$timesched 		= $this->input->post('timesched');
		$csid 			= $this->input->post('csid');
		$base_id 		= $this->input->post('base_id');
		$employeeid		= $this->input->post('employeeid');
		$date_active 	= $this->input->post('date_active');
		$reason 		= $this->input->post('reason');
		$res = $this->schedule->saveChangeSchedule($csid, $timesched,$reason);
		if($res) echo "Successfully Updated!";
		else 	 echo 'Failed to set status.';
	}
	/**
	 * Saves new change schedule application.
	 *
	 * @return string
	 */
	function saveSchedApp(){
		$this->load->model('utils');

		$return 	= $empcount = "";
		$arr_data_failed = array();
		
		$data 		= $this->input->post();
		$date_effective	= isset($data['dfrom']) 	? $data['dfrom']  		: "";
		$eids 		= isset($data['eids'])			? $data['eids'] 		: "";
		$timesched 	= isset($data['timesched']) 	? $data['timesched'] 	: "";
        $el_document= isset($data['el_document'])   ? $data['el_document']  : "";
		$tnt 		= isset($data['tnt']) 			? $data['tnt'] 			: "";
		$specific 	= $data['specific']=='true'		? 1 					: 0;
		$start 		= isset($data['start']) 		? $data['start'] 		: "";
		$end 		= isset($data['end']) 			? $data['end'] 			: "";
		$user 		= $this->session->userdata('username');
		$reason     = isset($data['reason'])? $data['reason'] : "" ;

		if($specific == 1) $date_effective = $start;

		# for ica-hyperion 21194
		# justin (with e)
		# data para sa direct request
		$isAdmin 	= isset($data['isAdmin']) 		?  $data['isAdmin'] 	: "";
		$allowSeq 	= isset($data['allowSeq']) 		?  $data['allowSeq'] 	: "";
		# end for ica-hyperion 21194

		$qdept  = $this->db->query("SELECT office FROM employee WHERE employeeid='$user'");
		$office = ($qdept->num_rows() > 0 ? $qdept->row(0)->office : "");
		
		$arr_emplist = explode(",", $eids);

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

		$dhead = $chead = $hrhead = '';
		if(!$isAdmin){

			$empcount = 0;

			foreach ($arr_emplist_tnt as $tnt1 => $emp_list_temp) {
				$office = $emp_list_temp[0]['office'];
                $employeeid = $emp_list_temp[0]['employeeid'];
                if($office){
                    $dhead = $this->utils->getDeptHead('head',      $office);   
                    $chead = $this->utils->getDeptHead('divisionhead',$office); 
                    $hrhead = $this->utils->getDeptHead('head',     'HR');
                } //endif
				list($empcount_temp,$failed_temp,$return_temp) = $this->saveSchedAppProcess($tnt1,$office,$date_effective, $specific, $start, $end, $timesched, $dhead,$chead,$hrhead,$emp_list_temp,$employeeid,$reason);
				$empcount += $empcount_temp;
				$arr_data_failed = array_merge($arr_data_failed,$failed_temp);
				if($return_temp) $return .= $return_temp;
			}

			
	        if($empcount){
				$return .= "$empcount employee(s) successfully applied.";

			}else{ 			$return .= "Failed to save application.";}

	        echo $return;
    	}else{
    		# new condition added for ica-hyperion 21194
    		# by justin (with e)
    		# para ito sa saving ng sequence kapag direct request na nangaling sa admin

    		# para malaman kung ilang employee ang nasave
    		$empcount = 0;
    		
    		# array sa teaching and non teaching result
    		$teachingType_result = array();

    		# para sa saving ng employee kapag direct request
    		foreach ($arr_emplist_tnt as $tnt1 => $emp_list_temp) {
    			$office = $emp_list_temp[0]['office'];
    			$code_request = 'ECS';

    			$isHead = $this->utils->checkIfHead($user);

    			///< head will look up on head code setup
    			$forhead = '';
    			if($user==$dhead || $user==$chead || $isHead) $forhead = 'HEAD';
    			$code_request .= $forhead;

    			if($tnt1 == 'nonteaching') $code_request = $code_request.'NON';
    			if(!$forhead && $office == 'LIB') $code_request = str_replace('NON', '', $code_request); ///< #ICA-HYPERION21711

    			# i-process na dito yung nakuhang data.
    			$teachingType_result[$tnt1] = $this->saveSchedAppProcessForDirectRequest($code_request,$date_effective, $specific, $start, $end, $timesched, $emp_list_temp,$user,$reason, $tnt1, $allowSeq);
    			
    		}

    		# displayed result
    		# para sa show result ng saving
    		$result = '';
    		foreach ($teachingType_result as $tnt1 => $res) {
    			$result .= ($result) ? '\n' : '';
    			$result .= strtoupper($tnt1) . ":"; # title
    			extract($res);
    			$result .= "\n  * No. employee(s) successfully applied : ". $success_emp;
    			$result .= "\n  * No. employee(s) unsuccessfully applied : ". $failed_emp;
    			$result .= "\n  * Other error : ". $other_error;
    		}

    		echo $result;
    	}
    }
    # new function for ica-hyperion 21194
    # by justin (with e)
    function saveSchedAppProcessForDirectRequest($code, $date_effective, $specific, $start, $end, $timesched, $arr_emplist,$user,$reason, $teachingType, $isDirectApproved){
    	$result = array();
    	
    	# counter ng success at failed employee
    	$result['success_emp'] = 0;
    	$result['failed_emp'] = 0;
    	$result['other_error'] = '';

    	# default value para mga head
    	$dhead = $chead = $hrhead = $cphead = $fdhead = $bohead = $phead = $uphead = "";
    	# default value para sa sequence
    	$dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = "";

    	# kuhain mo muna yung sequence
    	$seq = $this->schedule->getAppSequence($code);
    	if($seq->num_rows > 0){
    		# sequence
    		$dseq  = $seq->row(0)->dhseq;
    		$cseq  = $seq->row(0)->chseq;
    		$hrseq = $seq->row(0)->hhseq;
    		$cpseq = $seq->row(0)->cpseq;
    		$fdseq = $seq->row(0)->fdseq;
    		$boseq = $seq->row(0)->boseq;
    		$pseq  = $seq->row(0)->pseq;
    		$upseq = $seq->row(0)->upseq;
    		
    		# head ng finance, budget officer, president at univ phy.
    		$fdhead = $seq->row(0)->financedir;
    		$bohead = $seq->row(0)->budgetoff;
    		$phead = $seq->row(0)->president;
    		$uphead = $seq->row(0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

    		
    		foreach ($arr_emplist as $emplist) {
                $empID = $emplist["employeeid"];
    			# get ko muna kung ano department sya belong
    			$office = $campusid = '';
    			$findoffice = $this->db->query("SELECT e.`office`, e.`employeeid`, e.`campusid` FROM employee e WHERE e.`employeeid`='{$empID}'");
    			if(count($findoffice->result()) > 0){
    				$office = $findoffice->row()->office;
    				$campusid = $findoffice->row()->campusid;
    			}
    			
    			# get muna yung mga head ng department, cluster, hr at campus principal
    			if($office){
    				
    				# * department
    				$dhead = $this->utils->getDeptHead('head',		$office);	
    				# * cluster
					$chead = $this->utils->getDeptHead('divisionhead',$office);	
					# * hr
					$hrhead = $this->utils->getDeptHead('head',		'HR');
					# * campus principal
					if($code == 'ECS'){
						$qPrincipal = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='{$campusid}'");
						$cphead = ($qPrincipal->num_rows() > 0) ? $qPrincipal->row()->campus_principal : '';
					}
    			}

    			# kapag naka zero yung isDirectApproved var, ay automatic direct approved na sya..
    			if($isDirectApproved == 0){
    				# tatangalin ko nalang yung sequence para hindi na sya makita sa ibang approver
    				$dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = "";
    			}

    			# insert ko muna sya sa change_sched_app, pag katapos save ay kunin na yung base_id
    			$base_id = $this->schedule->insertBaseSchedApp($user, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq, $date_effective, $specific, $start, $end,$reason);	
    			if($base_id){
    				# save na yung details
    				$dres = $this->schedule->insertSchedAppDetail($base_id, $timesched, $teachingType,$reason);

    				# save na yung
    				$ddate = "";
    				$csid = $this->schedule->insertSchedAppEmpListByAdmin($base_id, $empID, $teachingType, 'PENDING', $ddate, $user, $isDirectApproved, $reason);
    				$res = $csid;

    				if($isDirectApproved == 0){ //< @Angelica Ticket #ICA-HYPERION21362
    					$res = $this->schedule->saveSchedStatusChange($user,$base_id,$empID, 'APPROVED','','','',true, $timesched, $base_id,'','','');
    				}

    				if($res) $result['success_emp'] += 1;
    				else 				  $result['failed_emp'] += 1;
    			}else{
    				$result['failed_emp'] += 1;
    			}
    		}
    		

    	}else{
    		$result['other_error'] = "No current setup for $teachingType change schedule. ";
    	}

    	return $result;
    }
    # end of new function for ica-hyperion 21194
    /**
	 * Process saving application. Gets sequence per code and teaching type. Sorts emplist per campus. Inserts base app and detail.
	 *
	 * @return array (app success count, array of failed employeeid, error message)
	 */
    function saveSchedAppProcess($teachingType='teaching',$office='',$date_effective, $specific, $start, $end, $timesched, $dhead,$chead,$hrhead,$arr_emplist,$user,$reason){
    	$this->load->model('utils');
    	$code_request = 'ECS';
    	$return = '';
    	$empcount = 0;
    	$arr_data_failed = array();
    	$dstatus = "PENDING";
    	$ddate = "";
    	$dseq = $cseq = $hrseq = $cpseq = $fdseq = $boseq = $pseq = $upseq = $fdhead = $bohead = $phead = $uphead = "";

    	$isHead = $this->utils->checkIfHead($user);

    	///< head will look up on head code setup
    	$forhead = '';
    	if($user==$dhead || $user==$chead || $isHead) $forhead = 'HEAD';
    	$code_request .= $forhead;

    	if($teachingType == 'nonteaching') $code_request = $code_request.'NON';
    	if(!$forhead && $office == 'LIB') $code_request = str_replace('NON', '', $code_request); ///< #ICA-HYPERION21711

    	//get seq from form
    	$seq = $this->schedule->getAppSequence($code_request);
    	if($seq->num_rows > 0){
    		$dseq   = $seq->row (0)->dhseq;
    		$cseq   = $seq->row (0)->chseq;
    		$hrseq  = $seq->row (0)->hhseq;
    		$cpseq  = $seq->row (0)->cpseq;
    		$fdseq  = $seq->row (0)->fdseq;
    		$boseq  = $seq->row (0)->boseq;
    		$pseq   = $seq->row (0)->pseq;
    		$upseq  = $seq->row (0)->upseq;
    		$fdhead = $seq->row (0)->financedir;
    		$bohead = $seq->row (0)->budgetoff;
    		$phead  = $seq->row (0)->president;
    		$uphead = $seq->row (0)->univphy . ($seq->row(0)->univphyt <> ""?(",".$seq->row(0)->univphyt):"");

    		///< check if user is depthead ,if head set dstatus to approved
    		if($dseq && $dhead == $user){
    			$dstatus = "APPROVED";
    			$ddate 	 = date_format( new DateTime('today') ,"Y-m-d");
    		}


    		///< sort by campus && get campus head
    		$campuslist = array();
    		foreach ($arr_emplist as $empid) {
    			$campusid = $this->employee->getempdatacol('campusid',$empid["employeeid"]);
    			if(!array_key_exists($campusid, $campuslist)) 	$campuslist[$campusid] = array();
    			array_push($campuslist[$campusid], $empid);
    		}
    		foreach ($campuslist as $campusid => $emplist) {
    			$cphead = '';
                $c_res = $this->db->query("SELECT campus_principal FROM code_campus WHERE code='$campusid'");
    			if($c_res->num_rows() > 0){
    				$cphead = $c_res->row(0)->campus_principal;
    			}

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
                    if($val == 1) $this->extensions->sendEmailToNextApprover($approver_id);
                }

				///< insert
				$base_id = $this->schedule->insertBaseSchedApp($user, $dhead, $chead, $hrhead, $cphead, $fdhead, $bohead, $phead, $uphead, $dseq, $cseq, $hrseq, $cpseq, $fdseq, $boseq, $pseq, $upseq, $date_effective, $specific, $start, $end,$reason);					///< save base ot app
				
				if($base_id){
					//for detail
        			$dres 		= $this->schedule->insertSchedAppDetail($base_id, $timesched, $teachingType,$reason);

        			//for emplist
					list($empcounttemp,$failed_temp) = $this->schedule->insertSchedAppEmpList($base_id, $emplist,$teachingType, $dstatus, $ddate, $user, $reason); ///< save emplist
					$empcount += $empcounttemp;
					$arr_data_failed = array_merge($arr_data_failed,$failed_temp);
				}
				else 			$arr_data_failed = array_merge($arr_data_failed,$emplist);

    		}

    	}else{
    		$return = "No current setup for $teachingType change schedule. ";
    	}

		return array($empcount,$arr_data_failed,$return);

    }

    /**
	 * Get list of applications under a specific approver with given filters (app status, datefrom, dateto).
	 *
	 * @return view
	 */
    function getCSAppListToManage(){
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
		$cs_list = array();
		///< --------------------------(condition added for Campus Principal which is ommitted for non teaching)-------------------------------------------
		$cs_list_teaching = $this->getCSAppListToManageProcess('ECS',$status,$datefrom,$dateto,$user,'teaching');
		$cs_list_non = $this->getCSAppListToManageProcess('ECSNON',$status,$datefrom,$dateto,$user,'nonteaching');
        $cs_list_teaching_head = $this->getCSAppListToManageProcess('ECSHEAD',$status,$datefrom,$dateto,$user,'teaching');
        $cs_list_non_head = $this->getCSAppListToManageProcess('ECSNONHEAD',$status,$datefrom,$dateto,$user,'nonteaching');

		if(sizeof($cs_list_teaching) > 0) 	$cs_list =  array_merge($cs_list, $cs_list_teaching);
		if(sizeof($cs_list_non) > 0) 		$cs_list =  array_merge($cs_list, $cs_list_non);
        if(sizeof($cs_list_teaching_head) > 0)   $cs_list =  array_merge($cs_list, $cs_list_teaching_head);
        if(sizeof($cs_list_non_head) > 0)        $cs_list =  array_merge($cs_list, $cs_list_non_head);


		$data['cs_list'] 	= $cs_list;
		$data['isHrHead'] 	= $isHrHead; 
        $data['status'] = $status;
		$this->load->view("change_schedule/cs_history_manage",$data);
	}

	# for ica-hyperion 21194
	# copy by justin (with e) 
	# kinopya ko ito para hindi maapektohan yung sa employee..
	function getCSAppListToManageForAdmin()
    {
		# request
		$status 	= $this->input->post('status');
		$datefrom 	= $this->input->post('datefrom');
		$dateto 	= $this->input->post('dateto');
		$isLoad 	= $this->input->post('isLoad');
		# user id
		$user 	    = $this->session->userdata('username');
		# kukunin nya yung data sa change_sched_app, equal sa user na nagapplay..
		$getListEmpList = $this->schedule->getChangeSchedListByAdmin($user,$status,$datefrom,$dateto,$isLoad);
		$data['cs_list'] = array();
		# i-array yung na get result
		if(count($getListEmpList) > 0)
        {
			foreach ($getListEmpList as $sql) 
            {
				# push sa $data['cs_list']
				array_push($data['cs_list'], array
                (
					'base_id' 	 		=> $sql->base_id,
					'csid'				=> $sql->csid,
					'empId'   	 		=> $sql->empId,
					'fullname'   		=> $sql->fullname,
					'timestamp'  		=> $sql->timestamp,
					'date_effective'   	=> ($sql->isTemporary == 0) ? date('F d, Y',strtotime($sql->date_effective)) : date('F d, Y',strtotime($sql->dfrom)) .' - '. date('F d, Y',strtotime($sql->dto)),
					'reason'   			=> $sql->reason,
				    'status'   			=> $sql->status
			    ));
			}
		}
		$this->load->view("change_schedule/cs_history_admin",$data);
	}
	# end for ica-hyperion 21194
	/**
	 * Get list of applications per approver, code_request and given parameters. This will get the list as per current status of the application in the sequence.
	 *
	 * @return array (list of applications)
	 */
	function getCSAppListToManageProcess($code_request="ECS",$status='',$datefrom='',$dateto='',$user='',$teachingType='')
    {
		$colhead = $isLastApprover = "";
		$prevcolstatus = ""	;
		$cs_list = array();
		$arr_aprvl_seq 	= array();
		$setup 			= $this->schedule->getAppSequence($code_request);
		if($setup->num_rows() > 0){
			$arr_aprvl_seq = $this->schedule->sortApprovalSeq($setup->row(0));
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
			$temp_res = $this->schedule->getCSAppListToManage($user, $arr['colhead'], $arr['colstatus'], $status, $arr['prevcolstatus'], $datefrom, $dateto,$teachingType,$arr['seq_count']);
			if($temp_res->num_rows() > 0){
				foreach ($temp_res->result() as $key => $row) {
					$cs_list[$row->csid] = array('data_list'=>$row,'colhead'=>$arr['colhead'],'colstatus'=>$arr['colstatus'],'prevcolstatus'=>$arr['prevcolstatus'],'isLastApprover'=>$arr['isLastApprover'],'code_request'=>$arr['code_request']);
				}
			}
		}

		return $cs_list;
	}
	/**
	 * Loads specific view based on input post $view passing schedule details.
	 *
	 * @return view
	 */
	function getSchedDetails(){
		$post 		= $this->input->post();
		$csid 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		$baseid		= isset($post['baseid']) 	? $post['baseid'] 	: "";
		$view 		= isset($post['view']) 		? $post['view'] 	: "";
		$colhead 	= isset($post['colhead']) 	? $post['colhead'] 	: "";
		$colstatus 	=  $colhead ? (substr($colhead,0,-4) . 'status') : '';

		$data 					= $this->schedule->getSchedEmpDetails($csid,$colstatus);
		$data['csdata'] 		= $this->schedule->getSchedDetails($baseid);
		$data['query'] 			= $this->db->last_query();
		$data["idkey"]          = $csid;
		$data['job'] 	 		= $this->input->post("job");
		$data['colhead'] 		= $colhead;
		$data['isLastApprover'] = $this->input->post("isLastApprover");
		$data['code_request']	= $this->input->post("code_request");
        $data["colstatus"]      = $colstatus;

        list($is_admin_approved, $status) = $this->schedule->isApprovedByAdmin($csid);
        if($is_admin_approved) $data["colstatus"] = $status;

        $this->load->view("change_schedule/$view",$data);
	}

	/**
	 * Get sched details per application. Computes tardy, absent, and early_d if none is saved in the application.
	 *
	 * @return string (concat of sched details -- format referenced from timesched sent to saveSchedStatusChange)
	 */
	function getSchedDetailsForBatchApprove($base_id='',$csid='')
    {
		$timesched = '';
		$separator = '~u~';
		$cs_detail = $this->schedule->getSchedDetails($base_id);

		if($cs_detail)
        {
			$prev_day = '';
			
			foreach($cs_detail as $row)
            {
				$start              = $row->starttime!="00:00:00"?date("h:i A",strtotime($row->starttime)):"";
				$end                = $row->endtime!="00:00:00"?date("h:i A",strtotime($row->endtime)):"";
				$tardy_start        = $row->tardy_start!="00:00:00"?strtotime($row->tardy_start):"";
				$absent_start       = $row->absent_start!="00:00:00"?strtotime($row->absent_start):"";
				$early_dismissal    = $row->early_dismissal!="00:00:00"?strtotime($row->early_dismissal):"";

			    if($start)
                {
			        if(!$tardy_start)
                    {
			            if($prev_day == $row->dayofweek) $tardy_start = strtotime("+1 minutes",strtotime($start));
			            else                             $tardy_start = strtotime("+6 minutes",strtotime($start));
			        }
			        if(!$absent_start)  $absent_start = strtotime("+121 minutes",strtotime($start));
			        
			    }
			    if($end)
                {
			        if(!$early_dismissal) $early_dismissal = strtotime("-121 minutes",strtotime($end));
			    }

			    $tardy_start 		= $tardy_start?date("h:i A",$tardy_start):"";
			    $absent_start 		= $absent_start?date("h:i A",$absent_start):"";
			    $early_dismissal 	= $early_dismissal?date("h:i A",$early_dismissal):"";

				$timesched .= $timesched ? '|' : '';
				$timesched .= $row->id 				. $separator;
				$timesched .= $row->dayofweek 		. $separator;
				$timesched .= $row->idx 			. $separator;
				$timesched .= $start . ' - ' . $end . $separator;
				$timesched .= $tardy_start 			. $separator;
				$timesched .= $absent_start			. $separator;
				$timesched .= ''					. $separator;
				$timesched .= $early_dismissal		. $separator;
				$timesched .= $row->leclab			;

				$prev_day = $row->dayofweek;
			}
		}

		return $timesched;
	}
	/**
	 * Approver side -- changing of application status.
	 *
	 * @return json
	 */
	function saveSchedStatusChange(){
        $count = 0;
        $next_approver = "";
        ///< csid,status,colhead,isLastApprover -- if last na ,set status
        $csid           = $this->input->post('csid');
        $base_id        = $this->input->post('base_id');

        $employeeid     = $this->input->post('employeeid');
        $status         = $this->input->post('status');
        $date_active    = $this->input->post('date_active');
        $colhead        = $this->input->post('colhead');
        $isLastApprover = $this->input->post('isLastApprover');
        $code_request   = $this->input->post('code_request');
        $colstatus      = substr($colhead,0,-4) . 'status';
        $coldate        = substr($colhead,0,-4) . 'date';
        $reason         = $this->input->post('reason');
        $user = $this->session->userdata('username');


        if($this->input->post('isBatchApprove') && $colhead == 'hrhead'){
            $timesched      = $this->getSchedDetailsForBatchApprove($base_id,$csid);
        }else{
            $timesched      = $this->input->post('timesched');
        }

        $res = $this->schedule->saveSchedStatusChange($user,$csid,$employeeid, $status,$colstatus,$coldate,$colhead,$isLastApprover, $timesched, $base_id, $date_active,$reason);

        #echo "<pre>". $this->db->last_query(); print_r($this->session->all_userdata());

        if(!$isLastApprover && $status == 'APPROVED'){ ///< get next in sequence with same head id
            $arr_aprvl_seq  = array();
            $setup          = $this->schedule->getAppSequence($code_request);
            if($setup->num_rows() > 0){
                $arr_aprvl_seq = $this->schedule->sortApprovalSeq($setup->row(0));
            }
            $aprvl_count = sizeof($arr_aprvl_seq);
            $prevkey     = '';
            $arr_apprv = array();

            foreach ($arr_aprvl_seq as $key => $obj) {
                $isLastApprover_tmp = false;
                $colhead_tmp = $obj['position'];

                if($obj['head_id'] == $user && $colhead_tmp != $colhead){
                    if($aprvl_count == $key)     $isLastApprover_tmp = true;
                    if($key > 1)                 $prevkey        = $key - 1;

                    $colstatus_tmp =  $colhead_tmp ? (substr($colhead_tmp,0,-4) . 'status') : '';
                    $coldate_tmp =  $colhead_tmp ? (substr($colhead_tmp,0,-4) . 'date') : '';

                    if($isLastApprover_tmp){
                        if($arr_aprvl_seq[$prevkey]['head_id'] == $user){
                            $prev_colhead = $arr_aprvl_seq[$prevkey]['position'];
                            $res_tmp = $this->schedule->saveSchedStatusChange($user,$csid,$employeeid, $status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover, $timesched, $base_id, $date_active,$reason,$prev_colhead);
                        }
                    }else{
                        $res_tmp = $this->schedule->saveSchedStatusChange($user,$csid,$employeeid, $status,$colstatus_tmp,$coldate_tmp,$colhead_tmp,$isLastApprover, $timesched, $base_id, $date_active,$reason);
                    }

                }

                if($res){
                    if(isset($arr_aprvl_seq[$count+1]["head_id"]) && $arr_aprvl_seq[$count+1]["head_id"]) $next_approver = $arr_aprvl_seq[$count+1]["head_id"];
                    elseif(isset($arr_aprvl_seq[$count+2]["head_id"]) && $arr_aprvl_seq[$count+2]["head_id"]) $next_approver = $arr_aprvl_seq[$count+2]["head_id"];
                }

                $this->extensions->sendEmailToNextApprover($next_approver);

            }

        }

        if($res) echo json_encode(array('err_code'=>0,'msg'=>"Success! Status now is : $status"));
        else     echo json_encode(array('err_code'=>2,'msg'=>'Failed to set status.'));

    }
    
	/**
	 * Get employee change schedule app history for employee/applying side.
	 *
	 * @return view
	 */
	function getEmpSchedHistory(){
		$this->load->model('utils');
        $user 		= $this->session->userdata("username");
        $post 		= $this->input->post();
        $datefrom 	= isset($post['datefrom']) 	? $post['datefrom'] : "";
		$dateto 	= isset($post['dateto']) 	? $post['dateto'] 	: "";
		$status 	= isset($post['status']) 	? $post['status'] 	: "";
		$isread 	= isset($post['isread']) 	? $post['isread'] 	: "";
		$action 	= isset($post['action']) 	? $post['action'] 	: "";
        $data['isHead'] = $this->utils->checkIfHead($user);
        $data['cs_list'] = array();

    	if ($action == "load") 
        {
    		$data['cs_list'] = $this->schedule->getEmpSchedHistory($user, $datefrom, $dateto, '', '' ,$isread);	
    		/** HYPERION21629 **/
    		/** PAULO **/
    		/*if (sizeof($data['cs_list']) == 0) {
    			$data['cs_list'] = $this->schedule->getEmpSchedHistory($user,$datefrom, $dateto, 'PENDING', '' ,'1');
    		}*/
    		if (count($data['cs_list']->result()) == 0) {
    			$data['cs_list'] = $this->schedule->getEmpSchedHistory($user,$datefrom, $dateto, '', '' ,'');
    		}
    		/** END OF HYPERION21629 **/
    	}
    	//result in history after applying for application
    	else if($action == "apply")
    	{
    		$pending = $this->schedule->getEmpSchedHistory($user, $datefrom, $dateto,'PENDING', '' ,'1');
    		if($pending)
    		{
    			$data['cs_list'] = $this->schedule->getEmpSchedHistory($user,$datefrom, $dateto, 'PENDING', '' ,'1');
    		}
    		else
    		{
    			$data['cs_list'] = $this->schedule->getEmpSchedHistory($user, $datefrom, $dateto, '', '' ,$isread);	
    		}
    	}
    	else
    	{
    		//for filtering  purposes
            if ($status == "PENDING") {$data['cs_list'] = $this->schedule->getEmpSchedHistory($user, $datefrom, $dateto, 'PENDING', '' ,'1');
        }
            else if ($status == "") 
            { $data['cs_list'] = $this->schedule->getEmpSchedHistory($user, $datefrom, $dateto,$status, '' ,'');
            
        	}
            else{$data['cs_list'] = $this->schedule->getEmpSchedHistory($user, $datefrom, $dateto, $status, '' ,$isread);}
        }

        $data["status"] = $status;

        if(sizeof($data['cs_list']) == 0)  $data['cs_list'] = $this->schedule->getEmpSchedHistory($user, $datefrom, $dateto, $status, '' ,$isread);

        $this->load->view("change_schedule/cs_history_emp",$data);
	}

	/**
	 * Loads approval status view based on OT id passing array of approval details.
	 *
	 * @return view
	 */
	function getApprovalSeqStatus()
    {
		///< display position, name, status, date updated
		$this->load->model('utils');
		$post 		= $this->input->post();
		$id 		= isset($post['idkey']) 	? $post['idkey'] 	: "";
		// $position_names = array('dhead'=>'Department Head','chead'=>'Cluster Head','hrhead'=>'HR Director','fdhead'=>'Financial Director','bohead'=>'Budget Officer','phead'=>'President','uphead'=>'University Physician');
		$position_names = $this->utils->getRequestApprover();

		$arr_aprvl_seq 	= array();
		$setup = $this->schedule->getAppSequencePerSched($id);
		if($setup->num_rows() > 0)
        {
			$arr_aprvl_seq = $this->sortApprovalSeqPerSched($setup->row(0));
		}
		foreach ($arr_aprvl_seq as $key => $obj) 
        {
			$arr_aprvl_seq[$key]['position_name'] 	= $position_names[$obj['position']];
			$arr_aprvl_seq[$key]['fullname'] 		= $this->utils->getFullName($obj['head_id']);
		}
		$data['arr_aprvl_seq'] = $arr_aprvl_seq;
        // additional by justin (with e)  for ica-hyperion 21983
        $data['is_admin_approved'] = false;
        list($is_admin_approved, $status) = $this->schedule->isApprovedByAdmin($id);
        if($is_admin_approved)
        {
            $data['arr_aprvl_seq'] = array();
            $data['is_admin_approved'] = true;
            $data['status'] = $status;
        }

        #echo "<pre>"; print_r($data); die;
		$this->load->view("change_schedule/cs_approval_list",$data);
	}
	/**
	 * Sorts approval heads based on sequence. Stores sorted details in array.
	 *
	 * @param stdClass Object $setup approval sequence details of specific OT
	 *
	 * @return array
	 */
	function sortApprovalSeqPerSched($setup)
    {
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
	 * Get history of applications for admin side.
	 *
	 * @return view
	 */
	function getCSManagementHistory()
    {
		$post 		= $this->input->post();
		$category 	= isset($post['category']) ? $post['category'] : '';
		$office 	= isset($post['office']) ? $post['office'] : '';
		$dfrom 		= isset($post['dfrom']) ? $post['dfrom'] : '';
		$dto 		= isset($post['dto']) ? $post['dto'] : '';

		$data['cs_list'] = $this->schedule->getCSManagementHistory($category, $office, $dfrom, $dto);
		$data['category'] = $category;
		$this->load->view('process/view_cs_status', $data);
	}

	/**
	 * Saves new change schedule application directly by HR.
	 *
	 * @return string
	 */
	function saveSchedAppHRDirect()
    {
		$this->load->model('utils');

		$return 	= $empcount = "";
		$data 		= $this->input->post();
		$dfrom 		= isset($data['dfrom']) 			? $data['dfrom']  		: "";
		$eids 		= isset($data['eids'])			? $data['eids'] 		: "";
		$timesched 	= isset($data['timesched']) 	? $data['timesched'] 	: "";
		$tnt 		= isset($data['tnt']) 			? $data['tnt'] 			: "";
		$user 		= $this->session->userdata('username');


		$arr_emplist = explode(",", $eids);

	    $ret="Failed to save application.";
	    if(sizeof($arr_emplist) > 0)
        {
			$hrhead = $this->utils->getDeptHead('head','HR');
			if($hrhead)
            {
				$count = $this->schedule->saveSchedAppHRDirect($user, $arr_emplist, $hrhead, $dfrom, $timesched, $tnt);
				$ret = "($count) employee/s successfully applied.";

			}else $ret = 'No setup for HR Head.';
		}
        else $ret = 'No employee selected.';
		echo $ret;
	}
	
	function getDayofweekFromDates(){
		$this->load->model('utils');

		$start = $this->input->post("start");
		$end = $this->input->post("end");

		$arr_dow = $this->utils->getDayofweekFromDates($start,$end);
		echo json_encode($arr_dow);
	}
	function getEmployeeScheduleHistory()
    {
		$employeeid = $this->input->post('employeeid');
		$sched_list_q = $this->schedule->getEmployeeScheduleHistory($employeeid);

		$data['employeeid'] = $employeeid;

		if($sched_list_q->num_rows() == 0) echo '&emsp;&emsp;NO DATA AVAILABLE.';
		else 
        {
			$arr_sched_list = array();
			foreach ($sched_list_q->result() as $key => $row) 
            {
				$arr_sched_list[$row->dateactive][$row->idx][$row->editstamp] = array
                (
					'starttime' => $row->starttime, 
                    'endtime' => $row->endtime,
                    'dayofweek' => $row->dayofweek,
                    'tardy_start' => $row->tardy_start,
                    'absent_start' => $row->absent_start,
                    'early_dismissal' => $row->early_dismissal,
                    'leclab' => $row->leclab,
                    'flexible' => $row->flexible,
                    'hours' => $row->hours,
                    'breaktime' => $row->breaktime,
                    'mode' => $row->mode,
                    'course' => $row->course,
                    'section' => $row->section,
                    'subject' => $row->subject,
                    'aimms' => $row->aimsdept,
                    'weekly_sched' => $row->weekly_sched
                );
			}
			$data['arr_sched_list'] = $arr_sched_list;
			$data['scheddays'] 	= $this->schedule->getSchedDays();
			$this->load->view('employee/schedule_info_history',$data);
		}
	}

    function loadSelectAimsDept()
    {
        echo $this->schedule->showSelectAimsDept();
    }
    function loadSubject()
    {
        echo $this->setup->generateSubjectDropdown();
    }
	function updateEmployeeScheduleHistory(){
		$return = array('err_code'=>0,'msg'=>'Update successful.');

		$timesched = $this->input->post('timesched');
		$sched_id = $this->input->post('sched_id');
		$dateactive_time = $this->input->post('dateactive_time');
		$employeeid = $this->input->post('employeeid');
		$user = $this->session->userdata('username');

		$res = $this->schedule->updateEmployeeScheduleHistory($user,$sched_id,$employeeid,$timesched,$dateactive_time);

		if(!$res) $return = array('err_code'=>2,'msg'=>'Failed to update schedule.');
		echo json_encode($return);
	}

    function batchDeleteSchedule(){
        $sched_id = $this->input->post('sched_id');
        $employeeid = $this->input->post('employeeid');
        $sched_id = explode("~|~", $sched_id);
        foreach ($sched_id as $key => $value) {
            $this->schedule->deleteEmployeeScheduleHistory($value,$employeeid);
        }
    }

	function deleteEmployeeScheduleHistory(){
		$return = array('err_code'=>0,'msg'=>'Delete successful.');

		$sched_id = $this->input->post('sched_id');
		$employeeid = $this->input->post('employeeid');

		$res = $this->schedule->deleteEmployeeScheduleHistory($sched_id,$employeeid);

		if(!$res) $return = array('err_code'=>2,'msg'=>'Failed to delete schedule.');
		echo json_encode($return);
	}

    function getEmployeeSchedule(){
        $data = $this->input->post();
        $this->load->view('employee/emp_sched_details',$data);
    }

    function isScheduleCodeExists(){
        $toks = $this->input->post("toks");
        $code =  $this->gibberish->decrypt($this->input->post("code"), $toks);
        echo $this->schedule->isScheduleCodeExists($code);
    }

    function adjust_weekly_schedule(){
        $toks = $this->input->post("toks");
        $data['weekly_flexible'] =  $this->gibberish->decrypt($this->input->post("weekly_flexible"), $toks);
        $this->load->view('employee/weekly_schedule',$data);
    }

    function adjust_weekly_schedule_main(){
        $toks = $this->input->post("toks");
        $data['weekly_flexible'] =  $this->gibberish->decrypt($this->input->post("weekly_flexible"), $toks);
        $this->load->view('employee/weekly_schedule_main',$data);
    }

    function adjust_weekly_schedule_history(){
        $toks = $this->input->post("toks");
        $data['weekly_flexible'] =  $this->gibberish->decrypt($this->input->post("weekly_flexible"), $toks);
        $this->load->view('employee/weekly_schedule_history',$data);
    }
    
} //endoffile