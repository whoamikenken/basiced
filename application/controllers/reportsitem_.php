<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reportsitem_ extends CI_Controller {

	function __construct(){
	    parent::__construct();
	    if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
	}

	function educbackgrounditems(){
      $data['title'] = '';
      $data['naction']  = $this->input->post('naction');
      $data['category'] = $this->input->post('category');
      $data['displaydata'] = $this->input->post('displaydata');
      if ($data['category'] == "SCTT") {
      	$this->load->view('reportsitem/sctt_modal', $data);
      }else{
      	$this->load->view('reportsitem/educbackground_modal', $data);
      }							
      
    }

	function saveeducbackground(){
		$return 	= "";
		$toks = $this->input->post("toks");
		$data   	= $toks ? Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks )) : $this->input->post();

		$educlevel 	= isset($data['eb_educlevel']) 	? $data['eb_educlevel'] : "";
		$educlevelpoints = isset($data['eb_points']) 	? $data['eb_points'] : "";
		$educid = isset($data['eb_id']) 	? $data['eb_id'] : "";
		#$category = isset($data['eb_category']) ? $data['eb_category'] : "";
		// echo "<pre>";print_r($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));die;
		$reportcode = isset($data['eb_category']) ? $data['eb_category'] : "";
		$displaydata = isset($data['displaydata']) ? $data['displaydata'] : "";
		$description = isset($data['reportdesc']) ? $data['reportdesc'] : "Educational Background";

		$this->load->model('reportsitem');
		$q = $this->reportsitem->saveEducBackground($educid,$reportcode,$description,$educlevel,$educlevelpoints);
		
		$return['eb_list'] = $this->reportsitem->getReportList($reportcode);
		$return['category'] = $reportcode;
		$return['displaydata'] = $displaydata;
		$return['th_label'] =  strtoupper($description);
		$this->load->view('reportsitem/educbackground',$return);
	}

	function saveSubjCompetentToTeach(){
		$return 	= "";
		$toks = $this->input->post("toks");
		$data   	= $toks ? Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks )) : $this->input->post();
		$subj_code 	= isset($data['eb_subjcode']) 	? $data['eb_subjcode'] : "";
		$description = isset($data['eb_description']) 	? $data['eb_description'] : "";
		$remarks = isset($data['eb_remarks']) 	? $data['eb_remarks'] : "";
		$id = isset($data['eb_id']) 	? $data['eb_id'] : "";
		
		$reportcode = isset($data['eb_category']) ? $data['eb_category'] : "";
		$displaydata = isset($data['displaydata']) ? $data['displaydata'] : "";

		$this->load->model('reportsitem');
		$q = $this->reportsitem->saveSubjCompetentToTeach($id,$subj_code,$description,$remarks);
		$return['eb_list'] = $this->reportsitem->getReportList($reportcode);
		$return['category'] = $reportcode;
		$return['displaydata'] = $displaydata;
		$this->load->view('reportsitem/educbackground',$return);
	}

	function saveSchoolYear(){
		$this->load->model('reportsitem');
		$data = $this->input->post();
		$id = $this->input->post("id");
		$data['sy'] = $data['yr_from'].'-'.$data['yr_to'];
		unset($data['yr_from']);
		unset($data['yr_to']);
		// print_r($data);
		$query = $this->reportsitem->saveSchoolYear($data, $id);
		if($query && $id) echo "School year has been updated successfully!";
		else echo "School year has been saved successfully!";
	}

	function checkReportsItemCategory($category, $id){
		$this->load->model('reportsitem');
		$table = '~~';
		foreach(Globals::reportsItemTableList() as $categoryCode => $categoryTable){
			if($categoryCode == $category) $table = $categoryTable;
		}

		if($table == '~~'){
			return 0;
		}else{
			return $this->reportsitem->checkReportsItemCategory($table, $id);
		}
	}

	function deletereportsitemdata(){
		$this->load->model('reportsitem');
		$toks = $this->input->post("toks");
		$data = $this->input->post();
		if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       	}

		$id = $data['editvalue'];
		$reportcode = isset($data['category']) ? $data['category'] : "";
		$displaydata = isset($data['displaydata']) ? $data['displaydata'] : "";
		$return['th_label'] = isset($data['reportdesc']) ? strtoupper($data['reportdesc']) : "Educational Background";
		$return['naction']= $data['naction'];
		$usage = $this->checkReportsItemCategory($reportcode, $id);
		if($usage == 0){
			if($reportcode == "SCTT")		$this->reportsitem->deleteReportData($id);
			else 							$this->reportsitem->deleteSCTTData($id);

			$return['eb_list'] = $this->reportsitem->getReportList($reportcode);
			$return['category'] = $reportcode;
			$return['displaydata'] = $displaydata;
			$this->load->view('reportsitem/educbackground',$return);
		}else{
			echo 'used';
		}
			
		
	}

	function deleteSchoolYear(){
		$this->load->model('reportsitem');
		$id = $this->input->post('id');
		$query = $this->reportsitem->deleteSchoolYear($id);
		echo "School year deleted!";
	}



	function getreportsitemdata(){
		$this->load->model('reportsitem');
		$toks = $this->input->post("toks");
		$data = $this->input->post();
		if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       	}

		$id = $data['editvalue'];
		$displaydata = isset($data['displaydata']) ? $data['displaydata'] : "";
		$reportcode = isset($data['category']) ? $data['category'] : "";

		$return['naction']= $data['naction'];
		$return['category'] = $reportcode;
		$return['displaydata'] = $displaydata;

		if($reportcode <> "SCTT"){
			$return['eb_data'] = $this->reportsitem->getReportData($id);
			$this->load->view('reportsitem/educbackground_modal', $return);
		}else{
			$return['eb_data'] = $this->reportsitem->getSCTTData($id);
			$this->load->view('reportsitem/sctt_modal', $return);
		}
	}

	function manageSchoolYear(){
		$this->load->model('reportsitem');
		$id = $this->input->post("id");
		if($id) $return['sydata'] = $this->reportsitem->getSchoolYearData($id);
		$return['existing'] = $this->reportsitem->getExistingSchoolYear();
		$this->load->view('reportsitem/schoolyear_modal', $return);
	}

	

	function loadreportsitemdata(){
		$this->load->model('reportsitem');
		$toks = $this->input->post("toks");
		$data = $this->input->post();
		if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       	}
		$code = $data['category'];
		$return['category'] = $data['category'];
		$return['displaydata'] = $data['displaydata'];
		$return['eb_list'] = $this->reportsitem->getReportList($code);
		 $this->load->view('reportsitem/educbackground',$return);
	}

	function loadSchoolYearData(){
		$this->load->model('reportsitem');
		$return['eb_list'] = $this->reportsitem->getSchoolYearData();
		$this->load->view('reportsitem/schoolyeardata',$return);
	}

	function loadreportsitemelig(){
		$this->load->model('reportsitem');
		$toks = $this->input->post("toks");
		$data = $this->input->post();
		if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       	}
		$code = $data['category'];
		$return['category'] = $data['category'];
		$return['eb_list'] = $this->reportsitem->getReportList($code);
		 $this->load->view('reportsitem/educbackground',$return);
	}

	function loadreportsitemothercred(){
		$this->load->model('reportsitem');
		$toks = $this->input->post("toks");
		$data = $this->input->post();
		if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       	}
		$code = $data['category'];
		$return['category'] = $data['category'];
		$return['eb_list'] = $this->reportsitem->getReportList($code);
		 $this->load->view('reportsitem/educbackground',$return);
	}
	function loadreportsitemscho(){
		$this->load->model('reportsitem');
		$toks = $this->input->post("toks");
		$data = $this->input->post();
		if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       	}
		$code = $data['category'];
		$return['category'] = $data['category'];
		$return['eb_list'] = $this->reportsitem->getReportList($code);
		 $this->load->view('reportsitem/educbackground',$return);
	}

	function loadAnnouncementHistory(){
		$this->load->model('announcements');
		$data = $this->input->post();
		$user = $this->session->userdata("username");
		$return['a_list'] = $this->announcements->getHistory($user);
		$this->load->view('announcements/announcement_history',$return);
	}



}