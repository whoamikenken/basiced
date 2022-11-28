<?php
/**
* @author Max Consul
* @copyright 2019
* 
* documents controller
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Documents_ extends CI_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('documents');
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
    }

	public function loadAvailableDocuments(){
		$data['records'] = $this->documents->loadAvailableDocuments();
		$this->load->view('process/documents_details', $data);
	}

	public function validateDocumentData(){
		$toks = $this->input->post("toks");
        $data   = $this->input->post();
        if($toks){
          unset($data["toks"]);
          foreach($data as $key => $val){
            $data[$key] = $this->gibberish->decrypt($val, $toks);
          }
        }
		$res = $this->documents->savedDocumentData($data['code'], $data['description']);
		echo $res;
	}

	public function readyDocumentData(){
        $toks = $this->input->post("toks");
		$code = $toks ?  $this->gibberish->decrypt($this->input->post('code'), $toks) : $this->input->post('code');
        $res = $this->documents->deleteDocumentData($code);
        echo $res;
	}

    public function loadModalSetup(){
        $this->load->view('process/setup_details');
    }

    public function viewImageModal(){
        $id = $this->input->post("id");
        $alldata = $this->documents->download($id);
        $data['fileExtension'] = pathinfo($alldata['filename'], PATHINFO_EXTENSION);
        $data["imgpath"] = base_url()."application/uploads/".$alldata['filename'];
        $data['description'] = $alldata['description'];
        $this->load->view('process/view_uploaded_docs', $data);
    }

    public function loadApplicationList(){
        $toks = $this->input->post("toks");
        $data['dfrom'] = $data['dto'] = '';
        $data   = $this->input->post();
        if($toks){
          unset($data["toks"]);
          foreach($data as $key => $val){
            $data[$key] = $this->gibberish->decrypt($val, $toks);
          }
        }
        $employeeid = '';
        if($this->session->userdata('usertype') == "EMPLOYEE"){
            $employeeid = $this->session->userdata('username');
            $data['dfrom'] = $data['dto'] = '';
        } 
        $data['status'] = isset($data['status']) ? $data['status'] : '';
        $data['records'] = $this->documents->loadDocumentRequests($employeeid, $data['status'], $data['dfrom'], $data['dto']);
        $this->load->view('process/docapp_details', $data);
    }

    public function loadApplyDocModal(){
        $data['emp_list'] = $this->employee->loadRegisteredEmployees();
        $data['doc_setup'] = $this->extensions->getDocumentSetup();
        $utype = $this->session->userdata('usertype');
        if($utype == "ADMIN") $this->load->view('process/addemployee_doc', $data);
        else $this->load->view('employeemod/document_app/addemployee_doc', $data);
    }

    public function validateDocApplication(){
        $input = $this->input->post();
        $toks = $this->input->post("toks");
        if($toks){
          unset($input["toks"]);
            foreach($input as $key => $val){
                if($key != "documents"){
                    $input[$key] = $this->gibberish->decrypt($val, $toks);
                }else{
                    $input['documents'] = array();
                    if(is_array($val)){
                        foreach ($val as $k => $v) {
                            array_push($input['documents'] , $this->gibberish->decrypt($v, $toks));
                        }
                    }else{
                        $input[$key] = $this->gibberish->decrypt($val, $toks);
                    }
                }
            }
        }
        $documents = $input['documents'];
        // echo "<pre>"; print_r($input); die;
        $employeeid = $input['employee'];
        $date_req = $input['date_req'];
        $purpose = $input['purpose'];

        foreach ($input as $key => $value) {            /*validate user input*/
            if(!$value){
                echo false;
                return;
            }
        }

        if(!is_array($documents)){
            $this->documents->savedDocApplication($employeeid,$documents,$date_req,$purpose);
        }
        else{
            foreach($documents as $doc_type){
                // $doc_type = $this->gibberish->decrypt($doc_type, $toks);
                if($doc_type) $this->documents->savedDocApplication($employeeid,$doc_type,$date_req,$purpose);
            }
        }
        echo true;
    }

    public function loadApplyDocModalBySort(){
        $fil_data = $this->input->post();
        $toks = $this->input->post("toks");
        if($toks){
          unset($fil_data["toks"]);
          foreach($fil_data as $key => $val){
            $fil_data[$key] = $this->gibberish->decrypt($val, $toks);
          }
        }
        $status = $fil_data['status'];
        $datefrom = $fil_data['datefrom'];
        $dto = $fil_data['dto'];
        $data['records'] = $this->documents->loadDocumentRequestsSorted($status, $datefrom, $dto);
        $this->load->view('process/docapp_details', $data);
    }

    public function deleteApplyDoc(){
        $toks = $this->input->post('toks'); 
        $id = $toks ?  $this->gibberish->decrypt($this->input->post('id'), $toks) : $this->input->post('id');
        $this->documents->deleteApplyDoc($id);
    }

    public function processApplyDoc(){
        $data = array();
        $toks = $this->input->post('toks'); 
        $id = $toks ?  $this->gibberish->decrypt($this->input->post('id'), $toks) : $this->input->post('id');
        $records = $this->documents->loadApplicationDetails($id);
        foreach($records as $row){
            $data['id'] = $row['id'];
            $data['fullname'] = $row['fullname'];
            $data['empid'] = $data['employeeid'] = $row['employeeid'];
            $data['dateapplied'] = $row['dateapplied'];
            $data['doc_requested'] = $row['doc_requested'];
            $data['doc_desc'] = $this->extensions->getDocumentDescription($row['doc_requested']);
            $data['reason'] = $row['reason'];
            $data['remarks'] = $row['remarks'];
            $data['date_to_claim'] = $row['date_to_claim'];
            $data['status'] = $row['status'];
            $data['approvedby'] = $row['approvedby'];
        }
        $this->load->view('process/process_docapplication', $data);
    }

    public function validateProcessApplication(){
        $formdata = $this->input->post("formdata");
        $formdata = base64_decode(urldecode($formdata));
        $data = Globals::convertFormDataToArray($formdata);
        $toks = $data['toks'];
        if($toks){
            foreach ($data as $key => $value) {
                if($key != "toks") $data[$key] = $this->gibberish->decrypt( $value, $toks );
            }
        }
        // echo "<pre>"; print_r($data); die;
        $app_id = isset($data['app_id']) ? $data['app_id'] : '';
        $date_req =  isset($data['date_req']) ? $data['date_req'] : '';
        $documents =  isset($data['documents']) ? $data['documents'] : '';
        $purpose =  isset($data['purpose']) ? $data['purpose'] : '';
        $remarks =  isset($data['remarks']) ? $data['remarks'] : '';
        $update_stat =  isset($data['update_stat']) ? $data['update_stat'] : '';
        $dateclaim =  isset($data['dateclaim']) ? $data['dateclaim'] : '';

        $this->documents->changeApplicationStatus($app_id, $remarks, $update_stat, $dateclaim, $purpose);
        echo $update_stat;
    }

    public function getEmployeeStatusHistory(){
        $data = array();
        $latest_data = array();
        $employeeid = $this->input->post('employeeid');
        $empdata = $this->documents->getEmployeeStatusHistory($employeeid);
        if($empdata->num_rows() > 0) $data['records'] = $empdata->result_array();
        $latest_status = $this->documents->getLatestEmployeeStatus($employeeid);
        if($latest_status->num_rows > 0){
            $latest_data = $latest_status->result_array();
            array_push($data['records'], $latest_data[0]);
        }
        $this->load->view('process/empstat_details', $data);
    }

    public function loadGoodMoralCertificate(){
        $this->load->library('PdfCreator_tcpdf');
        $this->load->library('PdfCreator_mpdf');
        $employeeid = $this->input->post('employee');
        $data['datehired'] = $this->getFirstEmploymentStatus($employeeid);
        $data['fullname'] = $this->extensions->getEmployeeName($employeeid);
        $positionid = $this->extensions->getEmployeePositionId($employeeid);
        $data['position'] = $this->extensions->getPositionDesc($positionid);
        $data['hrhead'] = $this->extensions->getHRHead();
        $data['datenow'] = $this->input->post('dateselect');
        $this->load->view('forms_pdf/goodmoralcertificate',$data);
    }

    public function loadContributionCertificate(){
        $this->load->library('PdfCreator_tcpdf');
        $this->load->library('PdfCreator_mpdf');
        $old_month = $month = "";
        $fmonth = "";
        $employeeid = $this->input->post('employee');
        $contribution = $this->input->post('contribution');
        $dfrom = $this->input->post('pyear1');
        $dto = $this->input->post('pyear2');
        $data['datehired'] = $this->getFirstEmploymentStatus($employeeid);
        $data['fullname'] = $this->extensions->getEmployeeName($employeeid);
        $data['hrhead'] = $this->extensions->getHRHead();
        $contri_data = $this->getAllContribution($employeeid, $dfrom, $dto);
        $first_month = true;
        foreach($contri_data as $value){
            $month = date('F Y', strtotime($value['cutoffstart']));
            if($first_month) $fmonth = $month;
            if($month != $old_month){
                $data['records'][$value['id']]['month'] = $month;
                $data['records'][$value['id']]['or_number'] = $value['or_number'];
                $data['records'][$value['id']]['datepaid'] = $value['datepaid'];
            }
            $old_month = date('m', strtotime($value['cutoffstart']));
            $first_month = false;
        }

        $data['fmonth'] = $fmonth;
        $data['lmonth'] = $month;

        $this->load->view('forms_pdf/contributioncertificate',$data);
    }

    public function getFirstEmploymentStatus($employeeid){
        $q_empstat = $this->db->query("SELECT * FROM `employee_employment_status_history` WHERE employeeid = '$employeeid' ORDER BY TIMESTAMP DESC LIMIT 1 ");
        if($q_empstat->num_rows > 0) return $q_empstat->row()->dateposition;
        else return false;
    }

    public function getAllContribution($employeeid, $dfrom, $dto){
        $q_contri = $this->db->query("SELECT * FROM payroll_computed_table a INNER JOIN payroll_computed_ee_er b ON a.`id` = b.`base_id` WHERE employeeid = '$employeeid' AND (DATE_FORMAT(a.`cutoffstart`, '%Y') = '$dfrom' OR DATE_FORMAT(a.`cutoffend`, '%Y') = '$dto') ORDER BY datepaid ASC; ");
        if($q_contri->num_rows > 0) return $q_contri->result_array();
            else return false;
    }

    public function loadCOECertificate(){
        $this->load->library('PdfCreator_tcpdf');
        $this->load->library('PdfCreator_mpdf');
        $add_text = $tag = "";
        $data = $this->input->post();
        $data['datehired'] = $this->getFirstEmploymentStatus($data['employee']);
        $data['fullname'] = $this->extensions->getEmployeeName($data['employee']);
        $data['hrhead'] = $this->extensions->getHRHead();
        if(isset($data['dto'])) unset($data['dto']);
        if($data['docs'] == "coereportsalary"){
            if(isset($data['position'])) $add_text .= " as a <b>".$data['position']."</b>";

            if(isset($data['dfrom']) && isset($data['dto'])) $add_text .= " from <b>".$data['dfrom']."</b> up to <b>".$data['dto']."</b>"; 
            else if(isset($data['dfrom']) && !isset($data['dto'])) $add_text .= " from <b>".$data['dfrom']."</b> up to present. "; 
            else if(!isset($data['dfrom']) && isset($data['dto'])) $add_text .= " from <b>".$data['datehired']."</b> up to <b>".$data['dto']."</b>"; 

            if(isset($data['salary'])) $add_text .= " Her monthly salary is presented below: ";

            $data['add_text'] = $add_text;
            $this->load->view('forms_pdf/coereport',$data);
        }else{
            $gender = $this->extensions->getEmployeeGender($data['employee']);
            if($gender == "M"){
                $tag = "He";
                $data['tag'] = "his";
            }
            else{
                $tag = "She";
                $data['tag'] = "her";
            }

            if(isset($data['position'])) $add_text .= " as a <b>".$data['position']."</b>";

            if(isset($data['dfrom']) && isset($data['dto'])) $add_text .= " from <b>".$data['dfrom']."</b> up to <b>".$data['dto']."</b>"; 
            else if(isset($data['dfrom']) && !isset($data['dto'])) $add_text .= " from <b>".$data['dfrom']."</b> up to present. "; 
            else if(!isset($data['dfrom']) && isset($data['dto'])) $add_text .= " from <b>".$data['datehired']."</b> up to <b>".$data['dto']."</b>"; 

            if(isset($data['leavefrom']) && isset($data['leavefrom'])) $add_text .= " $tag has filed a leave absence from <b>" .$data['leavefrom']. "</b> to <b>" .$data['leaveto']. "</b>";
            $data['add_text'] = $add_text;
            $this->load->view('forms_pdf/coereporttravel',$data);
        }
    }

    public function loadUploadDocumentModal(){
        $this->load->view("process/upload_document_modal");
    }

    public function loadUploadDocument(){
        $this->load->view("process/upload_document");
    }

    public function insert(){
        //load session library to use flashdata
        $this->load->library('session');
        $id = $this->input->post("id");
        //Check if file is not empty
        if(!empty($_FILES['upload']['name'])){
            $config['upload_path'] =  APPPATH . 'uploads/';
            //restrict uploads to this mime types
            $config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|xls|csv|docx';
            $config['file_name'] = $_FILES['upload']['name'];
            
            //Load upload library and initialize configuration
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            
            if($this->upload->do_upload('upload', true)){
                $uploadData = $this->upload->data();
                $filename = $uploadData['file_name'];

                //set file data to insert to database
                $file['description'] = $this->input->post('description');
                $file['uploaded_by'] = $this->session->userdata("username");
                $file['filename'] = $filename;
                if($id == ''){
                    $query = $this->documents->insertfile($file);
                }else{
                    $query = $this->documents->updatefile($file, $id);
                }
                if($query){
                    echo "File uploaded successfully";
                }
                else{
                    echo "File uploaded but not inserted to database";
                }

            }else{
                echo "Cannot upload file.";
            }
        }else{
            echo "Please upload a file.";
        }

    }

    public function download($id){
        $this->load->helper('download');
        $fileinfo = $this->documents->download($id);
        $file = APPPATH . 'uploads/'.$fileinfo['filename'];
        force_download($file, NULL);
    }

    public function loadDownloadDocumentModal(){
        $data['files'] = $this->documents->getDocumentLists();
        $this->load->view("employeemod/download_list", $data);
    }

    public function uploadedFormsTable(){
        $where_clause = '';
        $dates = $this->input->post();
        $toks = $this->input->post("toks");
        if($toks){
          unset($dates["toks"]);
          foreach($dates as $key => $val){
            $dates[$key] = $this->gibberish->decrypt($val, $toks);
          }
        }
        extract($dates);
        if($datefrom && $dateto) $where_clause .= " WHERE DATE(date_upload) BETWEEN '$datefrom' AND '$dateto'";
        $data['records'] = $this->documents->getDocumentList($where_clause);
        $this->load->view("config/uploaded_forms_table", $data);
    }

    public function uploadedFormsList(){
        $data['records'] = $this->documents->getDocumentLists();
        $this->load->view("config/uploaded_forms_list", $data);
    }

    public function validateFormData(){
        $data = $this->input->post();
        $res  = $this->documents->updateUploadedForms($data['id'], $data['description']);
        echo $res;
    }

    public function deleteUploadedForm(){
        $id = $this->input->post("id");
        $res  = $this->documents->deleteUploadedForms($id);
        echo $res;
    }

    public function markAsReadApplication(){
        $toks = $this->input->post("toks");
        $data   = $this->input->post();
        if($toks){
          unset($data["toks"]);
          foreach($data as $key => $val){
            $data[$key] = $this->gibberish->decrypt($val, $toks);
          }
        }
        echo $this->documents->markAsReadApplication($data["id"], $data["val"]);
    }

    public function updatedDocumentNotification(){
        echo $this->documents->ifHasPendingRequest();
    }

}