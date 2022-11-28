<?php 
/**
 * @author Angelica Arangco
 * @copyright 2018
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Application_adj_ extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model('application_adj');
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
	}


	################################## LEAVE #########################################################

	function loadLeaveAdjustment(){
		$data = array();
		list($data['dtr_cutoff_arr'], $data['payroll_cutoff_arr']) = $this->application_adj->getCutoffData();
		$this->load->view('application_adj/leave_adj',$data);
	}


	function getLeaveAdjList(){
		$this->load->model('income');
		$this->load->model('payrollprocess');

		$dtr_cutoff_id = $this->input->post('dtr_cutoff_id');

		list($dtr_start,$dtr_end,$p_start,$p_end) = $this->payrolloptions->getDtrPayrollCutoffPair('','','','',$dtr_cutoff_id);

		list($adj_list, $processed_request_ids) = $this->application_adj->getLeaveAdjProcessed($dtr_cutoff_id);
		
		// $payroll_time = $this->application_adj->getPayrollProcessedTime($p_start,$p_end);
		$payroll_time = $this->application_adj->getPayrollProcessedTime($dtr_start,$dtr_end);

		$pending_list = $this->application_adj->getLeaveAdjPending($payroll_time,$dtr_start,$dtr_end,$processed_request_ids);
		
		///< merge pending and processed adjustments ***********************
		foreach ($pending_list as $empid => $per_app) {
			foreach ($per_app as $request_id => $det) {
				array_unique($det['request_ids']);
				array_unique($det['dates']);

				$request_ids_str 	= implode('|', $det['request_ids']);
				$dates_str 			= implode('|', $det['dates']);

				///< compute income_adj
				list($income_adj,$income_adj_str) = $this->income->computeOtherIncomeAdj($empid,$det['total_days'],$p_start,$p_end);

				$adj_list[$empid]['PENDING'][$request_id] = array(
															'request_id' 		=> $request_ids_str,
															'payroll_cutoff_id' => '',
															'startdate' 		=> '',
															'enddate' 			=> '',
															'date' 				=> $dates_str,
															'total_days' 		=> $det['total_days'],
															'amount' 			=> $det['amount'],
															'fullname' 			=> $det['fullname'],
															'timestamp' 			=> $det['timestamp'],
															'income_adj' 		=> $income_adj,
															'income_adj_str' 	=> $income_adj_str
															);
			}
		}

		$income_config_q = $this->income->getIncomeSetupList(array('ismainaccount'=>'0','mainaccount'=>'30'));
		$arr_income_config = $this->payrollprocess->constructArrayListFromStdClass($income_config_q,'id','description');

		$data['arr_income_config'] = $arr_income_config;
		$data['adj_list'] = $adj_list;
		$data['dtr_cutoff_id'] = $dtr_cutoff_id;

		// echo "<pre>"; print_r($adj_list); echo "</pre>";
		$this->load->view('application_adj/leave_adj_list',$data);
	}


	function saveLeaveAdj(){
		$this->load->model('income');
		$this->load->model('payrollprocess');
		$success_count = 0;
		$arr_data_failed = array();

		$type = $this->input->post('type');
		$dtr_cutoff_id = $this->input->post('dtr_cutoff_id');
		$payroll_cutoff_id = $this->input->post('payroll_cutoff_id');
		$emplist = $this->input->post('emplist') ? $this->input->post('emplist') : '';
		$user = $this->session->userdata('username');

		$tbl = 'leave_adjustment';
		if($type=='OB') $tbl = 'ob_adjustment';
		else $type = 'LEAVE';

		if($emplist){
			foreach ($emplist as $key => $row) {
				$res = $this->db->query("INSERT INTO $tbl (employeeid, request_id, dtr_cutoff_id, payroll_cutoff_id, `date`, total_days, amount, income_adj, status, addedby) 
													VALUES ('{$row['employeeid']}', '{$row['request_id']}', '$dtr_cutoff_id', '$payroll_cutoff_id', '{$row['date']}', '{$row['total_days']}', '{$row['amount']}','{$row['income_adj_str']}', 'PROCESSED', '$user')");

				if($res){
					$success_count++;

					///< insert to employee_income_adj (for payroll)
					$arr_adj_to_add = $this->payrollprocess->constructArrayListFromComputedTable($row['income_adj_str']);
					$this->income->saveIncomeAdj_FromApplication($row['employeeid'],$payroll_cutoff_id,$arr_adj_to_add);

				}else 		array_push($arr_data_failed, $row['employeeid']);
			}

			if($success_count) 	$return = array('err_code'=>0,'msg'=>'Successfully saved.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
			else 				$return = array('err_code'=>2,'msg'=>'Failed to save.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
		}else{
			$return = array('err_code'=>2,'msg'=>'Failed. No employees to save.','success_count'=>$success_count,'data_failed'=>array()); 
		}

		echo json_encode($return);
	}


	################################## OB #########################################################

	function loadOBAdjustment(){
		$data = array();
		list($data['dtr_cutoff_arr'], $data['payroll_cutoff_arr']) = $this->application_adj->getCutoffData();
		$data['ob_type'] = $_POST['ob_type'];
		if($data["ob_type"] == "CORRECTION") $data["title"] = "Correction for Time In/Out Adjustment";
		else $data["title"] = "Official Business Adjustment";
		$this->load->view('application_adj/ob_adj',$data);
	}

	function loadOBAdjList(){
		$dtr_cutoff_id 	= $this->input->post('dtr_cutoff_id');
		$ob_type 		= $this->input->post('ob_type');

		if($ob_type == 'DIRECT'){
			$this->getOBAdjList($dtr_cutoff_id);
		}else{
			$this->getCorrectionAdjList($dtr_cutoff_id);
		}
	}


	function getOBAdjList($dtr_cutoff_id=''){
		$this->load->model('income');
		$this->load->model('payrollprocess');

		list($dtr_start,$dtr_end,$p_start,$p_end) = $this->payrolloptions->getDtrPayrollCutoffPair('','','','',$dtr_cutoff_id);
		
		list($adj_list,$processed_request_ids) = $this->application_adj->getLeaveAdjProcessed($dtr_cutoff_id,'OB');
		
		$payroll_time = $this->application_adj->getPayrollProcessedTime($p_start,$p_end);

		$pending_list = $this->application_adj->getLeaveAdjPending($payroll_time,$dtr_start,$dtr_end,$processed_request_ids,'OB');

		///< merge pending and processed adjustments ***********************
		foreach ($pending_list as $empid => $per_app) {
			foreach ($per_app as $request_id => $det) {
				array_unique($det['request_ids']);
				array_unique($det['dates']);

				$request_ids_str 	= implode('|', $det['request_ids']);
				$dates_str 			= implode('|', $det['dates']);

				///< compute income_adj
				list($income_adj,$income_adj_str) = $this->income->computeOtherIncomeAdj($empid,$det['total_days'],$p_start,$p_end);

				$adj_list[$empid]['PENDING'][$request_id] = array(
																'request_id' 		=> $request_ids_str,
																'payroll_cutoff_id' => '',
																'startdate' 		=> '',
																'enddate' 			=> '',
																'date' 				=> $dates_str,
																'total_days' 		=> $det['total_days'],
																'amount' 			=> $det['amount'],
																'fullname' 			=> $det['fullname'],
																'timestamp' 			=> $det['timestamp'],
																'paid' 			=> $det['paid'],
																'income_adj' 		=> $income_adj,
																'income_adj_str' 	=> $income_adj_str
																);
			}
		}

		$income_config_q = $this->income->getIncomeSetupList(array('ismainaccount'=>'0','mainaccount'=>'30'));
		$arr_income_config = $this->payrollprocess->constructArrayListFromStdClass($income_config_q,'id','description');

		$data['arr_income_config'] = $arr_income_config;
		$data['adj_list'] = $adj_list;
		$data['dtr_cutoff_id'] = $dtr_cutoff_id;

		$this->load->view('application_adj/ob_adj_list',$data);
	}

	function getCorrectionAdjList($dtr_cutoff_id=''){
		$this->load->model('income');
		$this->load->model('payrollprocess');

		list($dtr_start,$dtr_end,$p_start,$p_end) = $this->payrolloptions->getDtrPayrollCutoffPair('','','','',$dtr_cutoff_id);
		
		// list($adj_list,$processed_request_ids) = $this->application_adj->getCorrectionAdjProcessed($dtr_cutoff_id);
		$adj_list = $processed_request_ids = array();
		
		$payroll_time = $this->application_adj->getPayrollProcessedTime($p_start,$p_end);

		$pending_list = $this->application_adj->getCorrectionAdjPending($payroll_time,$dtr_start,$dtr_end,$processed_request_ids);

		///< merge pending and processed adjustments ***********************
		foreach ($pending_list as $empid => $per_app) {
			foreach ($per_app as $request_id => $det) {
				array_unique($det['request_ids']);
				array_unique($det['dates']);

				$request_ids_str 	= implode('|', $det['request_ids']);
				$dates_str 			= implode('|', $det['dates']);

				// $request_ids_str 	= implode('|', $det['request_ids']);
				// $dates_str 			= implode('|', $det['dates']);

				///< compute income_adj
				list($income_adj,$income_adj_str) = $this->income->computeOtherIncomeAdj($empid,0,$p_start,$p_end,true,$det['total_hours']);

				$adj_list[$empid]['PENDING'][$request_id] = array(
																'request_id' 		=> $request_ids_str,
																'payroll_cutoff_id' => '',
																'startdate' 		=> '',
																'enddate' 			=> '',
																'date' 				=> $dates_str,
																'total_hours' 		=> $det['total_hours'],
																'amount' 			=> $det['amount'],
																'fullname' 			=> $det['fullname'],
																'timestamp' 			=> $det['timestamp'],
																'paid' 			=> $det['paid'],
																'income_adj' 		=> $income_adj,
																'income_adj_str' 	=> $income_adj_str
																);
			}
		}
		$income_config_q = $this->income->getIncomeSetupList(array('ismainaccount'=>'0','mainaccount'=>'30'));
		$arr_income_config = $this->payrollprocess->constructArrayListFromStdClass($income_config_q,'id','description');

		$data['arr_income_config'] = $arr_income_config;
		$data['adj_list'] = $adj_list;
		$data['dtr_cutoff_id'] = $dtr_cutoff_id;

		$this->load->view('application_adj/correction_adj_list',$data);
	}


	function saveCorrectionAdj(){
		$this->load->model('income');
		$this->load->model('payrollprocess');
		$success_count = 0;
		$arr_data_failed = array();

		$dtr_cutoff_id = $this->input->post('dtr_cutoff_id');
		$payroll_cutoff_id = $this->input->post('payroll_cutoff_id');
		$emplist = $this->input->post('emplist') ? $this->input->post('emplist') : '';
		$user = $this->session->userdata('username');

		if($emplist){
			foreach ($emplist as $key => $row) {
				$res = $this->db->query("INSERT INTO correction_adjustment (employeeid, request_id, dtr_cutoff_id, payroll_cutoff_id, `date`, total_hours, amount, income_adj, status, addedby) 
													VALUES ('{$row['employeeid']}', '{$row['request_id']}', '$dtr_cutoff_id', '$payroll_cutoff_id', '{$row['date']}', '{$row['total_hours']}', '{$row['amount']}','{$row['income_adj_str']}', 'PROCESSED', '$user')");

				if($res){
					$success_count++;

					///< insert to employee_income_adj (for payroll)
					// $arr_adj_to_add = $this->payrollprocess->constructArrayListFromComputedTable($row['income_adj_str']);
					// $this->income->saveIncomeAdj_FromApplication($row['employeeid'],$payroll_cutoff_id,$arr_adj_to_add);

				}else 		array_push($arr_data_failed, $row['employeeid']);
			}

			if($success_count) 	$return = array('err_code'=>0,'msg'=>'Successfully saved.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
			else 				$return = array('err_code'=>2,'msg'=>'Failed to save.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
		}else{
			$return = array('err_code'=>2,'msg'=>'Failed. No employees to save.','success_count'=>$success_count,'data_failed'=>array()); 
		}

		echo json_encode($return);
	}


	///< migrate old data (used on 040118)
	function migrateLeave(){
		$old_q = $this->db->query("SELECT * FROM leave_app WHERE `other` != 'DA';");
		$count = 0;
		foreach ($old_q->result() as $key => $row) {
			$date_applied = date('Y-m-d',strtotime($row->timestamp));
			$teachingType = $this->employee->getempdatacol('teachingType',$row->employeeid);
			$base_ins_q = $this->db->query("
							INSERT INTO leave_app_base (applied_by, `type`, other, paid, datefrom, dateto, nodays, isHalfDay, sicktype, sched_affected, reason, 
									dhead,chead,hrhead,cphead,fdhead,bohead,phead,uphead,
									dseq,cseq,hrseq,cpseq,fdseq,boseq,pseq,upseq,
									date_applied,`timestamp`)

							VALUES	('{$row->employeeid}','{$row->type}','{$row->other}','{$row->paid}','{$row->datefrom}','{$row->dateto}','{$row->nodays}','{$row->isHalfDay}','{$row->sicktype}','{$row->sched_affected}','{$row->reason}',
								'{$row->depthead}','{$row->clusterhead}','{$row->hrdir}','','{$row->financedir}','{$row->budgetoff}','{$row->president}','{$row->univphy}',
								'{$row->dhseq}','{$row->chseq}','{$row->hhseq}','0','{$row->fdseq}','{$row->boseq}','{$row->pseq}','{$row->upseq}',
								'{$date_applied}','{$row->timestamp}'
							)
						");

			if($base_ins_q){

				$base_ins_id = $this->db->insert_id();
				if($base_ins_id){

					$res = $this->db->query("
							INSERT INTO leave_app_emplist (base_id,employeeid,teachingType,`status`,
									dstatus,ddate,cstatus,cdate,hrstatus,hrdate,fdstatus,fddate,bostatus,bodate,pstatus,pdate,upstatus,`update`,
									isread,`timestamp`,remarks)

							VALUES ('$base_ins_id','{$row->employeeid}','{$teachingType}','{$row->status}',
									'{$row->deptheadstatus}','{$row->deptheaddate}','{$row->clusterheadstatus}','{$row->clusterheaddate}','{$row->hrdirstatus}','{$row->hrdirdate}',
									'{$row->financedirstatus}','{$row->financedirdate}','{$row->budgetoffstatus}','{$row->budgetoffdate}','{$row->presidentstatus}','{$row->presidentdate}','{$row->univphystatus}','{$row->univphydate}',
									'{$row->isread}','{$row->timestamp}','{$row->remarks}'
							)
						");

					if($res) $count++;

				}
			}

		}

		echo $count;
	}
	

}