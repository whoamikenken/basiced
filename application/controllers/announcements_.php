<?php
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Announcements_ extends CI_Controller {

	function __construct(){
	    parent::__construct();
	    if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
	}

	/**
	* Loads the page for announcement history.
	*
	* @return view
	*/
	function loadAnnouncementHistory(){
		$this->load->model('announcements');
		$data 				= $this->input->post();
		$user 				= $this->session->userdata("username");
		$return['a_list'] 	= $this->announcements->getHistory($user);
		$return['totalDept'] 	= $this->extras->countDeoartment($user);
		// echo "<pre>";print_r($return);die;
		$this->load->view('announcements/announcement_history',$return);
	}
	function editAnnouncement(){
		$this->load->model('announcements');
		$toks = $this->input->post("toks");
		$data['codes'] = $toks ? $this->gibberish->decrypt( $this->input->post("code"), $toks ) : $this->input->post("code");
		$q_announcement = $this->announcements->getAnnouncementsInfo($data["codes"]);
		// echo "<pre>";print_r($q_announcement->result_array());die;
		$totalSelectedDept = $q_announcement->num_rows();
		$return = array();
		$return["deptid"] = "";
		if($q_announcement->num_rows() > 0){
			foreach($q_announcement->result_array() as $row){
				$return["id"] = $row["base_id"];
				$return["datefrom"] = $row["datefrom"];
				$return["dateto"] = $row["dateto"];
				$return["timefrom"] = date('g:i A', strtotime($row["timefrom"]));
				$return["timeto"] = date('g:i A', strtotime($row["timeto"]));
				$return["event"] = Globals::_e($row["event"]);
				$return["venue"] = Globals::_e($row["venue"]);
				$return["posted_until"] = $row["posted_until"];
				$return["deptid"] .= $row["deptid"].',';
			}
		}
		$totalDept = $this->extras->countDeoartment();
		
		if ($totalDept === $totalSelectedDept) {
			$return["deptid"] = "alldept";
		}

		echo json_encode($return);
	}
	function actionAnnouncement()
	{
		$this->load->model('announcements');
		$toks = $this->input->post("toks");
		$job = $toks ? $this->gibberish->decrypt( $this->input->post("job"), $toks ) : $this->input->post('job');
		$id = $toks ? $this->gibberish->decrypt( $this->input->post("code"), $toks ) : $this->input->post('code');
		$msg = "";
		if ($job =="delete") {
			if ($this->announcements->delAnnouncement($id) > 0) {
				$msg = "Announcement has been deleted successfully.";
			}
			else
			{
				$msg = "Failed to delete!";
			}
		}
		else
		{

		}
		echo $msg;
	}
	/**
	* Save new announcement created.
	*
	* @return string
	*/
	function saveAnnouncement(){
		$this->load->model('announcements');
		$return 	= "";
		$toks = $this->input->post("toks");
		$data   	= $toks ? Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks )) : $this->input->post();
		$ids 	= isset($data['ids'])		? $data['ids'] 		: "";
		$alldept 	= isset($data['alldept'])		? $data['alldept'] 		: "";
		$deptids 	= isset($data['deptids'])		? $data['deptids'] 		: "";
		$datefrom 	= isset($data['datesetfrom']) 	? $data['datesetfrom']  : "";
		$dateto 	= isset($data['datesetto']) 	? $data['datesetto'] 	: "";
		$tfrom  	= isset($data['tfrom']) 		? date("H:i:s", strtotime($data['tfrom']))		: "";
        $tto    	= isset($data['tto']) 			? date("H:i:s", strtotime($data['tto']))  		: "";
        $event		= isset($data['event'])			? $data['event']	  	: "";
        $venue		= isset($data['venue'])			? $data['venue']  		: "";
        $posted_until = isset($data['posted_until'])? $data['posted_until'] : "";
        $user 		= $this->session->userdata("username");
        if($deptids) $alldept = "";
       if ($ids) {
        	$query = $this->announcements->editAnnouncement($ids);
        	if ($query) {
        		$arr_deptids = array();
			    if($alldept){
			    	$arr_deptids = $this->announcements->getAllDepartmentIDs();
			    }else{
			    	if($deptids) $arr_deptids = explode(",", $deptids);
			    }

				$q = $this->announcements->saveAnnouncement($arr_deptids, $datefrom, $dateto, $tfrom, $tto, $event, $venue, $posted_until, $user);
				if($q) 	$return = 'Announcement has been updated successfully.';
				else 	$return 	= 'Failed to update announcement.';
        	}
        }
        else
        {

		    $arr_deptids = array();
		    if($alldept){
		    	$arr_deptids = $this->announcements->getAllDepartmentIDs();
		    }else{
		    	if($deptids) $arr_deptids = explode(",", $deptids);
		    }

			$q = $this->announcements->saveAnnouncement($arr_deptids, $datefrom, $dateto, $tfrom, $tto, $event, $venue, $posted_until, $user);
			if($q) 	$return = 'Announcement has been saved successfully.';
			else 	$return 	= 'Failed to save announcement.';
		}
		echo json_encode($return);
	}

	function loadpastEvents()
	{
		$data = $this->input->post();
		$toks = $this->input->post("toks");
		if($toks){
			if($toks){
			  unset($data["toks"]);
			  foreach($data as $key => $val){
			    $data[$key] = $this->gibberish->decrypt($val, $toks);
			  }
			}
		}
		$this->load->view('pastevent',$data);
	}

    public function getTodayAnnouncement(){
    	$this->load->model('announcements');
        $time = "";
        $announce_list = array();
        $holiday_list = array();
        $totalDept 	= $this->extras->countDeoartment();
        $announce_data = $this->announcements->getTodayAnnouncement();
        $holiday_data = $this->announcements->getTodayAnnouncementHoliday();
        // echo "<pre>"; print_r($holiday_data); die;
        
	    if($announce_data){
	        foreach($announce_data as $row){
	        	$dept = "";
	        	$getDept = $this->announcements->getAnnoucementDetail($row["id"]);
	            $time = date("g:i A", strtotime($row['timefrom'])) ." - ". date("g:i A", strtotime($row['timeto']));
	            $announce_list[$row["id"]]["event"] = $row["event"];
	            $announce_list[$row["id"]]["venue"] = $row["venue"];
	            $announce_list[$row["id"]]["time"] = $time;
	        
	            foreach ($getDept as $key => $rows) {
	            	if ($key == 0) {
	            		$dept = $rows['description'];
	            	}else{
	            		$dept .= ", ".$rows['description'];
	            	}
	            	
	            }

	            $announce_list[$row["id"]]["department"] = ($row["total"] == $totalDept) ? "All Department <button code='".$row['id']."' class='btn btn-primary annoucenentDetails' style='padding: 1px 6px;float: right;'>View More</button>":(($row["total"] > 2) ? $row['total']." Included Department <button code='".$row['id']."' class='btn btn-primary annoucenentDetails' style='padding: 1px 6px;float: right;'>View More</button>":$row["description"]);
	        }
	    }

	    if($holiday_data){
	        foreach($holiday_data as $row){
	        	$getDept = $this->extras->listDepartmentsAffectedByHoliday($row['holiday_id']);
	            $time = $row['fromtime'] ." - ". $row['totime'];
	            $holiday_list[$row["id"]]["event"] = $row["hdescription"];
	            $holiday_list[$row["id"]]["venue"] = $row["campus"];
	            $holiday_list[$row["id"]]["teaching_type"] = ($row["teaching_type"] == 'all' ? 'ALL TEACHING TYPE' : ($row["teaching_type"] == 'teaching' ? 'TEACHING' : 'NON-TEACHING'));
	            $holiday_list[$row["id"]]["type"] = $row["description"];
	            $holiday_list[$row["id"]]["department"] = (count($getDept) == $totalDept) ? "ALL OFFICE <button code='".$row['holiday_id']."' class='btn btn-primary holidayDetails' style='padding: 1px 6px;float: right;'>VIEW MORE</button>":((count($getDept) > 2) ? count($getDept)." INCLUDED OFFICE <button code='".$row['holiday_id']."' class='btn btn-primary holidayDetails' style='padding: 1px 6px;float: right;'>VIEW MORE</button>":$row["description"]);

	        }
	    }
        $data["announce_list"] = $announce_list;
        $data["holiday_list"] = $holiday_list;
        // echo "<pre>";print_r($data);die;
        $this->load->view("includes/announcement", $data);
    }

    public function getDeptDetails(){
    	$this->load->model('announcements');
        $time = "";
        $announce_list = array();
        $holiday_list = array();
        $toks = $this->input->post("toks");
        $id = $this->gibberish->decrypt($this->input->post("id"), $toks);
        $announce_data = $this->announcements->getAnnoucementDetail($id);

        $holiday_data = $this->announcements->getTodayAnnouncementHoliday();
       	$table = '<table class="table table-striped table-bordered table-hover">
		    <thead>                      
		        <tr style="background-color: #0072c6;">
		            <th><b>Description</b></th>
		        </tr>
		    </thead>
	    <tbody>';
        foreach($announce_data as $row){
        	$table .= '<tr>
	            <td>'.$row['description'].'</td>
	        </tr>';
        }
        $table .= '</tbody></table>';
	    
        echo $table;
    }

    public function getIncludedDepartmentInHoliday(){
    	$this->load->model('announcements');
        $time = "";
        $announce_list = array();
        $holiday_list = array();
        $toks = $this->input->post("toks");
        $id = $this->gibberish->decrypt($this->input->post("id"), $toks);
        $getDept = $this->extras->listDepartmentsAffectedByHoliday($id);
       	$table = '<table class="table table-striped table-bordered table-hover">
		    <thead>                      
		        <tr style="background-color: #0072c6;">
		            <th><b>Description</b></th>
		        </tr>
		    </thead>
	    <tbody>';
        foreach($getDept as $row){
        	$table .= '<tr>
	            <td>'.$this->extensions->getOfficeDescriptionReport($row).'</td>
	        </tr>';
        }
        $table .= '</tbody></table>';
	    
        echo $table;
    }
}