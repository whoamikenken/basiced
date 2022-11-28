<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utils_ extends CI_Controller {

	/**
	 * Loads utils model everytime this class is accessed.
	 */
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		$this->load->model('utils');
	}

	/**
	 * Generate list of employees under a specific filter.
	 *
	 * @return string
	 */
	function getEmplist(){
		$this->load->model('utils');
		$post  		= $this->input->post();
		$deptid 	= isset($post['deptid']) 	? $post['deptid'] : '';
		$division 	= isset($post['division']) 	? $post['division'] : '';
		$tnt 		= isset($post['tnt']) 		? $post['tnt'] : '';
		$caption 	= isset($post['caption']) 	? $post['caption'] : '';
		$official_sched 	= isset($post['official_sched']) 	? $post['official_sched'] : '';
		$select 		= isset($post['select']) 		? $post['select'] : '';

		$emplist = array();
		/*if(explode(',', $deptid) > 1){
			foreach (explode(',', $deptid) as $id) {
			  $emplist = array_merge($emplist, $this->utils->getEmplist($id, $division, $caption, $tnt));
			}
		}*/
		$emplist = $this->utils->getEmplist($deptid, $division, $caption, $tnt, $official_sched);

		// $emplist 	= $this->utils->getEmplist($deptid, $division, $caption, $tnt);
		unset($emplist['']);
		if(count($emplist) != 0){
			if($select == 'selectAll'){
				echo $this->constructOptionSelect($emplist,"selectAll");
			}else{
				echo $this->constructOptionSelect($emplist,"selectingAll");
			}
		}else{
			echo $this->constructOptionSelect($emplist);
		}
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
		if($caption == 'selectingAll'){
			$select .= "<option value='selectAll'>Select All</option>";
			foreach ($data as $key => $value) {
			$select .= "<option value='$key'>$value</option>";
			}
		}else if($caption == 'selectAll'){
			foreach ($data as $key => $value) {
			$select .= "<option value='$key' selected>$value</option>";
			}
		}else{
			foreach ($data as $key => $value) {
			$select .= "<option value='$key'>$value</option>";
			}
		}
		
		return $select;
	}

	function storeCurrentMenu(){
		$toks = $this->input->post("toks");
		$menuid = $toks ? $this->gibberish->decrypt($this->input->post('menuid'), $toks) :  $this->input->post('menuid');
		$this->session->set_userdata('activemenu',$menuid);
		echo $this->session->userdata('activemenu');
	}

	function getAge(){
		$menuid = $this->input->post('menuid');
		$this->session->set_userdata('activemenu',$menuid);
		echo $this->session->userdata('activemenu');
	}

	function loadAuditTrailHistory(){
		$ret = '';
        $dfrom  = $this->input->post('dfrom');
        $dto    = $this->input->post('dto');

        $data['auditTrail'] = $this->utils->getAuditTrailHistory($dfrom,$dto);

        $this->load->view('config/audit_trail_history',$data);
    }

    function loadApplicantStatus(){
        $records['data'] = $this->utils->loadApplicantStatus();
        $this->load->view('applicant/applicant_status_config_list', $records);
    }

    function getEmpListSched(){
    	$tnt = $this->input->post("tnt");
    	$emplist = $this->utils->getEmpListSched($tnt);
    	$return = '';
    	foreach ($emplist as $value) {
    		$fullname = $value['fname'].' '.$value['lname'];
    		$return .= '<option value="'.$value['employeeid'].'" >'.$value['employeeid']." - ".$fullname.'</option>';
    	}
    	echo $return;
    }

    function checkIfHolidayHalfday(){
    	$toks = $this->input->post("toks");
    	$data = array();
    	$holiday_id = $this->gibberish->decrypt( $this->input->post("holiday_id"), $toks );
    	if($holiday_id){
	    	$records = $this->utils->checkIfHolidayHalfday($holiday_id);
	    	foreach($records as $row){
	    		$data['halfday'] = $row['halfday'];
	    		$data['fromtime'] = $row['fromtime'];
	    		$data['totime'] = $row['totime'];
	    		$data['sched_count'] = $row['sched_count'];
	    	}
	    }
	    echo json_encode($data);
    
    }

    function getUpdatedNotification(){
    	$module = $this->input->post("module");
    	if($module == "CORRECTION") echo $this->utils->getNotifOB('CORRECTION');
    	if($module == "DIRECT") echo $this->utils->getNotifOB('DIRECT');
    }

    function getApproverUpdatedNotification(){
    	$module = $this->input->post("module");
    	if($module == "CORRECTION") echo $this->utils->getNotifManageOB('CORRECTION');
    	if($module == "OB") echo $this->utils->getNotifManageOB('DIRECT');
    	if($module == "LEAVE") echo $this->utils->getNotifManageLEAVE();
    	if($module == "OVERTIME") echo $this->utils->getNotifManageOvertime();
    }

    function getUpdatedManageNotification(){
    	$mlnotifcount    = $this->utils->getNotifManageLEAVE();
    	$msnotifcount    = $this->utils->getNotifManageSeminar();
    	$moffbusnotifcount = $this->utils->getNotifManageOB('DIRECT');
    	$mcornotifcount = $this->utils->getNotifManageOB('CORRECTION');
	    $monotifcount       = $this->utils->getNotifManage('ot_app','ot_app_emplist','OT');
        $monotifcount       += $this->utils->getNotifManage('ot_app','ot_app_emplist','OTNON');
        $monotifcount       += $this->utils->getNotifManage('ot_app','ot_app_emplist','OTHEAD');
        $monotifcount       += $this->utils->getNotifManage('ot_app','ot_app_emplist','OTHEADNON');
        $mcschednotifcount  = $this->utils->getNotifManage('change_sched_app','change_sched_app_emplist','ECS','schedule');
        $mcschednotifcount  += $this->utils->getNotifManage('change_sched_app','change_sched_app_emplist','ECSNON','schedule');
        $mcschednotifcount  += $this->utils->getNotifManage('change_sched_app','change_sched_app_emplist','ECSHEAD','schedule');
        $mcschednotifcount  += $this->utils->getNotifManage('change_sched_app','change_sched_app_emplist','ECSNONHEAD','schedule');
        echo $mlnotifcount + $msnotifcount + $moffbusnotifcount + $monotifcount + $mcschednotifcount /* + $mscnotifcount */ + $mcornotifcount;
    }

} //endoffile