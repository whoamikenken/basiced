<?php
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_ extends CI_Controller {

	/**
	 * Loads student model everytime this class is accessed.
	 */
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		$this->load->model('student');
	}



	public function loadNewStudSchedForm(){
		$this->load->model('schedule');
		$data['scheddays'] 	= $this->schedule->getSchedDays();
		$data['sylist'] = $this->student->getStudSYList();
		$data['deptlist'] = $this->student->getStudDepartmentList();
		$data['yearlevellist'] = $this->student->getStudYearLevelList();
		$data['sectionlist'] = $this->student->getStudSectionList();

		$this->load->view('student/stud_sched_create',$data);
	}

	public function saveStudentSched(){
		$result = array();

		$dateactive = $this->input->post('dfrom');
		$timesched 	= $this->input->post('timesched');
		$sy 		= $this->input->post('sy');
		$dept 		= $this->input->post('dept');
		$yearlevel 	= $this->input->post('yearLevel');
		$section 	= $this->input->post('section');

		$dept_arr = explode(',', $dept);

		$isDuplicateSched = $this->student->isDuplicateSched('',$sy,$yearlevel,$section,$dept,$dept_arr);

		if(!$isDuplicateSched){
			$base_id = $this->student->insertBaseStudSched($dateactive,$sy,$yearlevel,$section,$dept);

			if($base_id){
				$this->student->insertStudSchedDetail($base_id,$timesched,$dateactive);
				$result = array('err_code'=>1,'msg'=>'Successfully saved.');
			}else{
				$result = array('err_code'=>2,'msg'=>'Failed to Save.');
			}
		}else{
			$result = array('err_code'=>2,'msg'=>'Failed. Duplicate Schedule.');
		}

		echo json_encode($result);
	}

	public function loadAttendanceReport(){
		$info = array();

		$dfrom 		= $this->input->post('datesetfrom');
		$dept 		= $this->input->post('dept');
		$yearlevel 	= $this->input->post('yearlevel');
		$section 	= $this->input->post('section');

		// get list stud
		$stud_list_q = $this->student->getStudList('','',$yearlevel,$section,'',$dept);
		print_r($stud_list_q->num_rows());

		if($stud_list_q->num_rows() > 0){
			foreach ($stud_list_q->result() as $key => $row) {

				$stud_id = $row->studentid;
				$stud_sy = $row->sy;
				$stud_yl = $row->yearlevel;
				$stud_section = $row->section;
				$stud_dept = $row->depttype;

				$info['studlist'][$stud_id] = $row->lname;

				//get sched

				$sched_q = $this->db->query("SELECT * FROM student_schedule_batch 
									WHERE (sy='$stud_sy' OR sy='all') AND (yl='$stud_yl' OR yl='all') 
										AND (section='$stud_section' OR section='all') 
										AND (FIND_IN_SET('$stud_dept',department) OR department='all');");

				if($sched_q->num_rows() > 0){
					$sched 			= $sched_q->row(0);

					$sched_start 	= $sched->timeStart;
					$sched_end 		= $sched->timeEnd;
					$tardy_start 	= $sched->tardyStart;
					$halfday_start 	= $sched->halfdayStart;
					$absent_start 	= $sched->absentStart;

					list($login,$logout,$ol) = $this->student->getLogTime($stud_id,$dfrom,$sched_start,$sched_end,$absent_start,'','NEW');
					var_dump($login);
				}



				//get timesheet

			}
		}

		$this->load->view('student/stud_att_detailed_bed',$info);

	}

	public function postDataToAllCard($student){
		$api_url = Globals::apiUrl()."/api/person/addstudent";
		$studinfo = array(
			"PersonType" => "",
			"IDNumber"  => $student['studentid'],
			"FirstName" => $student['fname'],
			"MiddleName" => $student['mname'],
			"LastName" => $student['lname'],
			"BirthDate" => isset($student['bdate']) ? $student['bdate'] : "",
			"Gender" => isset($student['gender']) ? $student['gender'] : "",
			"Address" => isset($student['addr']) ? $student['addr'] : "",
			"ContactNumber" => isset($student['mobile']) ? $student['mobile'] : "",
			"TelephoneNumber" => isset($student['landline']) ? $student['landline'] : "",
			"EmailAddress" => isset($student['email']) ? $student['email'] : "",
			"CampusName" => "Pinnacle Technologies Inc.",
			"EducLevelName" => "Grade School",
			"YearSectionName" => "Grade 3",
			"StudSecName" => "Matiyaga",
			"EmergencyContactPerson" => isset($student['cp_name']) ? $student['cp_name'] : "",
			"EmergencyContactNo" => isset($student['cp_mobile']) ? $student['cp_mobile'] : "",
			"EmergencyContactAddress" => isset($student['cp_address']) ? $student['cp_address'] : ""
		);

		$studinfo = json_encode($studinfo);
		$curl = curl_init();
		$token = $this->extensions->getPostmanToken();
		$access_token = "Authorization: Bearer ".$token;
		curl_setopt_array($curl, array(
			CURLOPT_URL => $api_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $studinfo,
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json')),
			CURLOPT_HTTPHEADER => array($access_token),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			echo "cURL Error #:" . $err;
		} else {
			echo json_encode($response);
		}

	}

	public function syncAimsStudent(){
		$where_clause = "";
		$count = 0;
		$data = $this->input->post();
		$dept = $data["dept"];
		$yearlevelid = $data["yearlevelid"];
		$sectionid = $data["sectionid"];
		$sy = $data["sy"];
		$sem = $data["sem"];
		if($dept) $where_clause = " AND a.department = '$dept' ";
		if($yearlevelid) $where_clause = " AND a.YearLevel = '$yearlevelid' ";
		if($sectionid) $where_clause = " AND b.SY = '$sectionid' ";
		if($sy) $where_clause = " AND b.Sem = '$sy' ";
		if($sem) $where_clause = " AND a.SectCode = '$sem' ";

		$stud_list = $this->student->getAimsStudentList($where_clause);
		foreach($stud_list as $row){
			$res = $this->student->insertAimsStudentToHyperion($row);
			if($res) $count++;
		}

		echo "Successfully updated ".$count." student.";
	}

}
