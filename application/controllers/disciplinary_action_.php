<?php 
	//Added 6-1-2017

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Disciplinary_Action_ extends CI_Controller {

    function __construct(){
        parent::__construct();
        if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
        $this->load->model('disciplinary_action');
    }

	public function viewForm(){
		$toks = $this->input->post("toks");
		$data = $this->input->post();
		if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
       	}

       	$data['info_type'] = isset($data["info_type"]) ? $data["info_type"] : $this->input->post("info_type");
	    $data['code'] = isset($data["action"]) ? $data["action"] : $this->input->post("action");
	    $data['type_dis'] = isset($data["func_type"]) ? $data["func_type"] : $this->input->post("func_type");
	    $data['func_type'] = isset($data["func_type"]) ? $data["func_type"] : $this->input->post("func_type");
	    $this->load->view('disciplinary_action/disciplinary_action_setup_modal', $data);
	}
	
	public function viewBatchAdd(){
	    $this->load->view('disciplinary_action/add_emp_disciplinary_action_modal');
	}
	
	public function saveForm(){
		$return = array("err_code"=>0, 'msg'=>"Success.");
		$toks = $this->input->post("toks");
        $sanctions_setup = $functionExist = $message = $sanctions  = '';
        if($toks){
        	$data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("formdata"), $toks ));
        	unset($data['undefined']);
        	if(isset($data['sanctions[Verbal]'])){
        		$sanctions['Verbal'] = $data['sanctions[Verbal]']; unset($data['sanctions[Verbal]']);
				$sanctions['Written'] = $data['sanctions[Written]']; unset($data['sanctions[Written]']);
				$sanctions['Suspend'] = $data['sanctions[Suspend]']; unset($data['sanctions[Suspend]']);
        	}
        	extract($data);
        }else{
        	$code = $this->input->post("code");
	        $desc = $this->input->post("desc");
	        $message = $this->input->post("message");
	        $sanctions = $this->input->post("sanctions");

	        $action = $this->input->post("action");
	        $info_type = $this->input->post("info_type");
	        $frequency = $this->input->post("frequency");
	        $month = $this->input->post("month");
        }
			

		if($info_type == "code_disciplinary_action_offense_type")
		{
			$functionExist = "getOffensesInfo";
			$functionInsert = "insertOffensesInfo";
			$functionUpdate = "updateOffensesInfo";
		}
		else if($info_type == "code_disciplinary_action_sanction")
		{
			$functionExist = "getSanctionsInfo";
			$functionInsert = "insertSanctionsInfo";
			$functionUpdate = "updateSanctionsInfo";
		}
		if(is_array($sanctions)){
			foreach($sanctions as $key => $value){
				$sanctions_setup .= $key."=".$value."/";
			}
		}
		$sanctions_setup = substr($sanctions_setup,0, -1);


        if($action == 'add'){
        	$query_res = $this->disciplinary_action->$functionInsert($code, $desc, $message, $sanctions_setup, $frequency, $month);
        	if(!$query_res) $return = array("err_code"=>2,"msg"=>"Code already exists."); 
        }
        elseif($action == 'edit'){
	        $query_res = $this->disciplinary_action->$functionUpdate($code, $desc, $message, $sanctions_setup, $frequency, $month);
	        if(!$query_res) $return = array("err_code"=>2,"msg"=>"Failed to update."); 
        }
        echo json_encode($return);
    }
	
	public function deleteRow(){
		$toks = $this->input->post("toks");
        $code = $toks ?  $this->gibberish->decrypt($this->input->post('code'), $toks) : $this->input->post("code");
        $info_type = $toks ?  $this->gibberish->decrypt($this->input->post('infotype'), $toks) : $this->input->post("infotype");
        $query_res = $this->disciplinary_action->removeRecord($code,$info_type);
        if($query_res) echo "SUCCESS!";
        else 		   echo "FAILED!";
    }
	
	public function loadOffenseHistory(){
		$toks = $this->input->post("toks");
		$employeeid = $toks ? $this->gibberish->decrypt( $this->input->post("employeeid"), $toks ) : $this->input->post("employeeid");
        $return=array();
		$return['d_list'] = $this->disciplinary_action->getOffenseHistory($employeeid);
		$this->load->view('disciplinary_action/disciplinary_action_history',$return);
    }

    public function loadNotifCount(){
    	$toks = $this->input->post("toks");
    	$year = $toks ? $this->gibberish->decrypt( $this->input->post("year"), $toks ) : $this->input->post("year");
    	$department = $toks ? $this->gibberish->decrypt( $this->input->post("department"), $toks ) : $this->input->post("department");
    	$return['tardy'] = $this->disciplinary_action->empWithExcessiveTardiness(true,'',false,$year,$department);
    	$return['absent'] = $this->disciplinary_action->empWithExcessiveAbsenteism(true,'',false,$year,$department);

    	echo json_encode($return, true);
    }
	
	public function saveEmployeeOffense(){
		$toks = $this->input->post("toks");
        if($toks){
            $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("form_data"), $toks ));
            unset($data['undefined']);
        }
		$def_id = isset($data["def_id"]) ? $data["def_id"] : $this->input->post("def_id");
        $employeeid = isset($data["employeeid"]) ? $data["employeeid"] :$this->input->post("employeeid");
        $dateWarning = isset($data["dateWarning"]) ? $data["dateWarning"] : $this->input->post("dateWarning");
		$offense_code = isset($data["offense"]) ? $data["offense"] : $this->input->post("offense");
		$dateViolation = isset($data["dateViolation"]) ? $data["dateViolation"] : $this->input->post("dateViolation");
		$employeersStatement = isset($data["employeersStatement"]) ? $data["employeersStatement"] : $this->input->post("employeersStatement");
		$empStatement =isset($data["empStatement"]) ? $data["empStatement"] :  $this->input->post("empStatement");
		$sanction_code = isset($data["sanction"]) ? $data["sanction"] :$this->input->post("sanction");
		if($def_id) $return = array("err_code"=>0, 'msg'=>"Employee Offense has been updated successfully.");
        else $return = array("err_code"=>0, 'msg'=>"Employee Offense has been saved successfully.");
        try {
            $query_res = $this->disciplinary_action->saveEmpOffense($def_id,$employeeid,$dateWarning,$offense_code,$dateViolation,$employeersStatement,$empStatement,$sanction_code);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
        if(!isset($query_res)) $return = array("err_code"=>2,"msg"=>"Failed saving employee offense."); 
        else $query = $this->disciplinary_action->saveEmpOffenseHistory($employeeid,$offense_code,$sanction_code);	
        echo json_encode($return);
    }
	
	function getEmpOffenseDetails(){
		$toks = $this->input->post("toks");
        $def_id = $toks ? $this->gibberish->decrypt( $this->input->post("def_id"), $toks ) : $this->input->post("def_id");
        $res = $this->disciplinary_action->getEmpOffenseDetails($def_id);
        $data = array();
        if($res->num_rows() > 0){
            foreach ($res->result() as $key => $row) {
                $data['id']                	 	= $row->id;
				$data['employeeid']        	 	= $row->employeeid;
                $data['dateWarning']       		= $row->dateWarning;
                $data['offense_code']     		= $row->offense_code;
                $data['dateViolation']   		= $row->dateViolation;
                $data['employeers_statement'] 	= $row->employeers_statement;
                $data['employee_statement']     = $row->employee_statement;
				$data['sanction_code']    		= $row->sanction_code;
                $data['user']               	= $row->user;
                $data['date_created']       	= $row->date_created;
            }
        }
        echo json_encode($data);
	}
	
	public function deleteEmpOffense(){
		$toks = $this->input->post("toks");
        $id = $toks ? $this->gibberish->decrypt( $this->input->post("id"), $toks ) : $this->input->post("id");
        $query_res = $this->disciplinary_action->deleteEmpOffense($id);
        if($query_res) echo "Successfully Deleted Offense!";
        else 		   echo "Deletion of offense failed..";
    }
	
	public function batchSaveEmployeeOffense(){
        $return = "Success.";
        $toks = $this->input->post("toks");
        if($toks){
            $data = Globals::convertFormDataToArray($this->gibberish->decrypt( $this->input->post("form_data"), $toks ));
            unset($data['undefined']);
        }
        $def_id = isset($data["def_id"]) ? $data["def_id"] : $this->input->post("def_id");
        $dateWarning = isset($data["dateWarning"]) ? $data["dateWarning"] : $this->input->post("dateWarning");
		$offense_code = isset($data["offense"]) ? $data["offense"] : $this->input->post("offense");
		$dateViolation =  isset($data["dateViolation"]) ? $data["dateViolation"] : $this->input->post("dateViolation");
		$employeersStatement = isset($data["employeersStatement"]) ? $data["employeersStatement"] : $this->input->post("employeersStatement");
		$empStatement = isset($data["empStatement"]) ? $data["empStatement"] : $this->input->post("empStatement");
		$sanction_code = isset($data["sanction"]) ? $data["sanction"] : $this->input->post("sanction");
		$year = isset($data["year"]) ? $data["year"] : $this->input->post("year");
		$month = isset($data["month"]) ? $data["month"] : $this->input->post("month");
		$q = true;
		if(isset($data['employeeid[]'])){
			if($data['employeeid[]'] == 'allemployee'){
				$employees = '';
				$emplist = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",false,'');
				$counter = 0;
				foreach ($emplist as $value) {
					$employees[$counter] = $value['employeeid'];
					$counter++;
				}
			}else{
				$employees = explode(",", $data['employeeid[]']);
			}
			foreach($employees as $empid)
			{	
				$employeeid = $empid;
				if($employeeid != ''){
						$query_res = $this->disciplinary_action->saveEmpOffense($def_id,$employeeid,$dateWarning,$offense_code,$dateViolation,$employeersStatement,$empStatement,$sanction_code,$month,$year);
					if(!$query_res){
						$return = "Failed saving employee offense."; 
						break;
					}
					$query = $this->disciplinary_action->saveEmpOffenseHistory($employeeid,$offense_code,$sanction_code);
				}
			}
		}else{
			foreach($this->input->post("employeeid") as $empid)
			{	
				$employeeid = $empid;
				if($employeeid != ''){
						$query_res = $this->disciplinary_action->saveEmpOffense($def_id,$employeeid,$dateWarning,$offense_code,$dateViolation,$employeersStatement,$empStatement,$sanction_code,$month,$year);
					if(!$query_res){
						$return = "Failed saving employee offense."; 
						break;
					}
					$query = $this->disciplinary_action->saveEmpOffenseHistory($employeeid,$offense_code,$sanction_code);
				}
			}
		}

		
        
        echo $return;
    }
	
	public function viewExcessiveDetails(){
		$return = $this->input->post();
		$this->load->view('disciplinary_action/disciplinary_action_excessive_details',$return);
    }
	
	public function confirmAction(){
		$id = $this->input->post("id");
		$query = $this->db->query("UPDATE employee_disciplinary_action SET confirm = 'YES' WHERE id = '$id'");
		if($query)
		{
			echo "Disciplinary action has been confirmed Successfully!";
		}
		else
		{
			echo "Something Went Wrong!";
		}
    }
    //getting Disciplinary Sanction
    function getSanction()
    {
    	echo $this->disciplinary_action->getSanction();
    }


} //endoffile