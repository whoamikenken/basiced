<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_id extends CI_Controller {

	/**
	 * Loads applicant model everytime this class is accessed.
	 */
	function __construct(){
		parent::__construct();
		$this->load->model('studentt');
		$this->load->library('PdfCreator_mpdf');
	}

	public function index(){
        $this->load->view('studentId/studentPrinting');
	}

	public function camview(){
        $this->load->view('studentId/studentCam');
	}

	public function saveStudentImage(){
		$name = trim($this->input->get('studentname'));
		$id = $this->input->get('id');

		$getblob = $this->db->escape(file_get_contents($_FILES['webcam']['tmp_name']));
		$removelast = rtrim($getblob,"'");
		$finalstring = ltrim($removelast,"'");

        $this->studentt->saveStudentImage($finalstring,$name,$id);
        $this->load->view('studentId/studentCam');
	}

	public function viewResult(){
		$id = $this->input->post('id');
		$data['record'] = $this->studentt->getPreviewData($id);
        $this->load->view('studentId/details',$data);
	}

	public function printID(){
		$data['id'] = $this->input->get('id');
		$template = $this->input->get('template');
		if ($template == "Employee") {
			$this->load->view('studentId/multiprintingemp',$data);
		}else{
			$this->load->view('studentId/multiprintingstd',$data);
		} 
        
	}
}

