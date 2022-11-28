<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

 if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave_ extends CI_Controller {

	public $codedayofweek = array("0"=>"SU", "1"=>"M", "2"=>"T", "3"=>"W", "4"=>"TH", "5"=>"F", "6"=>"S");

	/**
	 * Loads leave model everytime this class is accessed.
	 */
	function __construct(){
		parent::__construct();
		if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
		// ob_end_clean();
		$this->load->model('leave');
	}
	
	function loadLeavePage(){
		$data = $this->input->post();
		$view = $this->input->post("view");
		$this->load->view($view,$data);
	}

	function getLeaveDateRange(){
		$leave_type = $this->input->post("leavetype");
		$employeeid = $this->input->post("employeeid");
		$employmentstatus = $this->extras->getEmploymentStatus($employeeid);
		echo $this->leave->getLeaveDateRange($leave_type, $employmentstatus);
	}

	/**
	* Counts number of days that have schedule within specified dates for applying leave.
	*
	* @return int
	*/
	function countDaysWithinSchedule(){
		$this->load->model('utils');
		$toks 	= $this->input->post('toks');
		$alldays 	=  ($toks) ? $this->gibberish->decrypt($this->input->post('alldays'), $toks) : $this->input->post('alldays');
		$withpay 	= ($toks) ? $this->gibberish->decrypt($this->input->post('withpay'), $toks) : $this->input->post('withpay');
		$start 	= ($toks) ? $this->gibberish->decrypt($this->input->post('start'), $toks) : $this->input->post('start');
		$end 	= ($toks) ? $this->gibberish->decrypt($this->input->post('end'), $toks) : $this->input->post('end');
		$fordetails 	= ($toks) ? $this->gibberish->decrypt($this->input->post('fordetails'), $toks) : $this->input->post('fordetails');
		$leavetype 	= ($toks) ? $this->gibberish->decrypt($this->input->post('leavetype'), $toks) : $this->input->post('leavetype');
		$format = 'Y-m-d';
		$daysCount = 0;
		if(!$withpay) $withpay = "YES";
		# newly added by justin (with e) for ica-hyperion 21194
		# para sa sinelect ni ADMIN na employee, else kukunin nya yung nag apply na employee
		$data = $this->input->post();
		if(isset($data["empID"]) && $toks) $data["empID"] = $this->gibberish->decrypt($data["empID"], $toks); 
		$employeeid =  isset($data['empID']) ?$data['empID'] : $this->session->userdata('username'); 
		
		// echo "<pre>"; print_r($start); die;
		if( $start <> '' && $start <> 'undefined' && $end <> '' && $end <> 'undefined'){
			$start 	= date_format(date_create($start), 'Y-m-d');
			$end 	= date_format(date_create($end), 'Y-m-d');

			$this->load->model('leave');
			$dates_arr = $this->utils->getDatesFromRange($start, $end);
			if($leavetype == 'ML')  $empsched_arr = $this->leave->getEmployee_Schedule($employeeid,$start);
			else $empsched_arr = $this->leave->getEmployeeSchedDays($employeeid,$start); # updated by justin (with e) for ica-hyperion 21194, new : '$employeeid' -- old '$this->session->userdata('username')'

			// echo "<pre>"; print_r($dates_arr); die;

			foreach ($dates_arr as $date) {

				$sched = $this->attcompute->displaySched($employeeid,$date);
				$countrow = $sched->num_rows();
				if($countrow > 0){
					$dayofwk = date('w', strtotime($date));
					if(!$alldays){
						if($withpay == "YES"){
							if(in_array($this->codedayofweek[$dayofwk], $empsched_arr)) $daysCount++;
						}else if($leavetype == 'ML'){
							if(in_array($this->codedayofweek[$dayofwk], $empsched_arr)) $daysCount++;
						}else{
							if($fordetails){
								if(in_array($this->codedayofweek[$dayofwk], $empsched_arr)) $daysCount++;
							}
						}
					}else if($leavetype == 'ML'){
						if(in_array($this->codedayofweek[$dayofwk], $empsched_arr)) $daysCount++;
					}else{
						$daysCount++;
					}
				}
					
			}
		}
		echo number_format($daysCount, 2);
	}
	
	/**
	 * Get employee schedule with given date
	 *
	 * @param string $date (Default: "")
	 *
	 * @return json
	 */
	function getEmployeeScheduleStartEnd(){
		
		$toks = $this->input->post('toks');
		$date = $this->input->post('start');
		if($toks) $date = $this->gibberish->decrypt($this->input->post('start'), $toks);
		# updated by justin (with e) for ica-hyperion 21194
		# para sa sinelect ni ADMIN na employee, else kukunin nya yung nag apply na employee
		$data = $this->input->post();
		// $data["empID"] = $this->gibberish->decrypt($data["empID"], $toks);
		// $employeeid =  isset($data['empID']) ?$this->gibberish->decrypt($data["empID"], $toks) : $this->session->userdata('username'); 
		
		if(isset($data['empID'])){
			if($toks) $employeeid = $this->gibberish->decrypt($data["empID"], $toks);
			else $employeeid = $data["empID"];
		}else{
			$employeeid = $this->session->userdata('username');
		}
		
		$sched = $this->leave->getEmployeeSchedule($employeeid, $date);
		$sched_arr = array();
		$isAm = $isBoth = 0; ///< for checking if schedule is subject for a whole day leave
		if($sched->num_rows() > 0){
			foreach ($sched->result() as $key => $row) {

				/*if($row->flexible == 'YES'){
					$sched_arr['FLEXI'] = 1;

				}else{*/
					$isAm 	= ( (strtotime($row->starttime) < strtotime('12:00:00') && strtotime($row->endtime) <= strtotime('12:00:00')) ? 1 : 0 );
					$isBoth = ( (strtotime($row->starttime) < strtotime('12:00:00') && strtotime($row->endtime) > strtotime('12:00:00')) ? 1 : 0 );

					$sched_arr[$row->starttime.'|'.$row->endtime.'|'.((strtotime($row->endtime) - strtotime($row->starttime)) / 3600).'|'.$isAm.'|'.$isBoth] 
							= date('h:i A', strtotime($row->starttime)) . ' - ' . date('h:i A', strtotime($row->endtime));
				// }

			}
		}
		echo json_encode($sched_arr);
	}

	///< ICA-HYPERION21710
	function getVL_MinimumDateToApply(){
		$employeeid =  isset($data['empID']) ?$data['empID'] : $this->session->userdata('username');

		$vl_min_daycount = 3;
		$valid_day = $today = date('Y-m-d');
		$empsched_arr = $this->leave->getEmployeeSchedDays($employeeid,$today);

		if(sizeof($empsched_arr) > 0){
			while ($vl_min_daycount > 0) :
				if(in_array($this->codedayofweek[date('w',strtotime($valid_day .' + 1 day'))], $empsched_arr)) :
					$vl_min_daycount--;
				endif;
				$valid_day = date('Y-m-d',strtotime($valid_day .' + 1 day'));
			endwhile;
		}

		echo $valid_day;
	}


	/**
	*  Display uploaded file based on leave application base_id
	*
	* @return file (image/jpeg or application/pdf)
	*/
	function viewSeminarInvitation(){
		$base_id = $this->input->get("id");
		$this->load->model('leave');

		if(isset($base_id)){
			$res = $this->leave->getSeminarInvitation($base_id);

			if($res->num_rows() > 0){
				$file = $res->row(0)->content;
				ob_clean();
				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: private',false);
				header('Content-type: '.$res->row(0)->file_type);
				header('Content-Disposition: inline; filename='.$res->row(0)->file_name);
				// header('Content-Length: ' . filesize($file));
				header('Connection: close');
				print $file;
				exit();
			}else{
				echo "No uploaded file.";
			}
		}else{
			echo "No uploaded file.";
		}
	}

	/**
	*  validate type and size of invitation file to be uploaded.
	*
	* @return int
	*/
	function validateInvitationFile(){
		$return = 0; ///< 0 for valid, 1 for invalid file type, 2 for file too large
		if(isset($_FILES['filess'])){
			$fileType = $_FILES['filess']['type'];
			$fileSize = $_FILES['filess']['size'];
			if($fileSize > 100000){
				$return = 2;
			}
			if(!in_array($fileType, array("image/jpeg","image/png","application/pdf"))) {
				$return = 1;
			}
		}
		echo $return;
	}

	/**
	*  validate type and size of upload file.
	*
	* @return int
	*/
	function validateUploadFile(){
		$return = 0; ///< 0 for valid, 1 for invalid file type, 2 for file too large
		$maxfilesize = 100000;
		$allowedtype = array("image/jpeg","image/png","application/pdf");
		if(isset($_FILES['filess'])){
			$fileType = $_FILES['filess']['type'];
			$fileSize = $_FILES['filess']['size'];
			if($fileSize > $maxfilesize){
				$return = 2;
			}
			if(!in_array($fileType, $allowedtype)) {
				$return = 1;
			}
		}
		echo $return;
	}

	/**
	*  Save sick leave application details and upload medical certificate if any.
	*
	* @return string
	*/    

	function saveSickLeaveApp(){
	    $data   = $this->input->post("form_data");
	    $post_data = array();
	    $sched_affected = array();
	    // var_dump(json_decode($data));
	    foreach (json_decode($data) as $key) {
	      $post_data[$key->name] = $key->value;
	      if($key->name == 'sched_affected[]') array_push($sched_affected, $key->value);
	    }

	    $post_data['sched_affected'] = $sched_affected;
	    // var_dump($_FILES['filess']);

	    // $model  = $this->input->post("model");
	    $model = $post_data['model'];

	    $save_ret =  $this->employeemod->$model($post_data); ///< Save application details

	    if(isset($save_ret)){
	      if($save_ret['err_code'] == 0 && isset($_FILES['filess'])){
	        $fileName = $_FILES['filess']['name'];
	        $tmpName  = $_FILES['filess']['tmp_name'];
	        $fileSize = $_FILES['filess']['size'];
	        $fileType = $_FILES['filess']['type'];
	        if(isset($save_ret['base_id']) && $_FILES['filess']['error'] == 0 && $fileSize > 0 && $fileSize <= 100000){
	              #image/jpeg, image/png, application/pdf
	              if(in_array($fileType, array("image/jpeg","image/png","application/pdf"))) {
	                 $fp      = fopen($tmpName, 'r');
	                              $content = fread($fp, filesize($tmpName));
	                              $content = addslashes($content);
	                              fclose($fp);
	                $this->db->query("INSERT into leave_app_attach_medicalcert (base_id,file_name,content,file_type) values ('{$save_ret['base_id']}','$fileName','$content','$fileType')"); ///< upload Medical cert
	              }
	        }
	      }
	    }

	    // echo $save_ret["msg"];
	    echo json_encode($save_ret);
	}

	# new function added for ica-hyperion 21194
	# by justin (with e)
	function getEmpListBYTeachingType(){
		$toks = $this->input->post('toks');
		$tnt = ($toks) ? $this->gibberish->decrypt($this->input->post("tnt"), $toks) : $this->input->post('tnt');
		$this->load->model('utils');

		# get list
		$emplist = $this->utils->findEmpListPerType($tnt);

		# displayed list
		foreach ($emplist as $code => $desc) {
			echo "<option values='". $code ."'>". $desc ."</option>";
		}
	}
	# end of new function added for ica-hyperion 21194
	
	function saveLeaveApp(){
        $data   = $this->input->post();
        // echo print_r($data);die;
        $sched_affected[] = $this->input->post('sched_affected');
        $data['sched_affected'] = $sched_affected[0];

        $model  = $this->input->post("model");
        $ret =  $this->employeemod->$model($data);

        echo json_encode($ret);
    }
	

	/**
	*  Display uploaded file based on leave application base_id
	*
	* @return file (image/jpeg or application/pdf)
	*/
	function viewUploadedFile(){
		$base_id = $this->input->get("id");
		// $base_id = $this->input->get("id");
		$tablename = "leave_app_attach_medicalcert";
		$this->load->model('leave');

		if(isset($base_id)){
			$res = $this->leave->getUploadedFile($tablename, $base_id);

			if($res->num_rows() > 0){
				$file = $res->row(0)->content;
				ob_clean();
				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: private',false);
				header('Content-type: '.$res->row(0)->file_type);
				header('Content-Disposition: inline; filename='.$res->row(0)->file_name);
				// header('Content-Length: ' . filesize($file));
				header('Connection: close');
				print $file;
				exit();
			}else{
				echo "No uploaded file.";
			}
		}else{
			echo "No uploaded file.";
		}
	}

	function saveLeaveHRDirect(){
		///< clean data
		///< construct array empids
		///< get head
		///< 
		$this->load->model('utils');
		$data = $this->input->post();
		$arr_emplist 	= isset($data['eid'])			? $data['eid'] 		: "";
		$datefrom 	= isset($data['datesetfrom']) 	? $data['datesetfrom'] 	: "";
		$dateto 	= isset($data['datesetto']) 	? $data['datesetto'] 	: "";
		$ltype 		= isset($data['ltype']) 		? $data['ltype'] 	: "";
		$othleave 	= isset($data['othleave']) 		? $data['othleave'] 	: "";
		$ndays 		= isset($data['ndays']) 		? $data['ndays'] 	: "";
		$withpay 	= isset($data['withpay']) 		? $data['withpay'] 	: "";
		$reason 	= isset($data['reason'])		? $this->extras->clean($data['reason'])	  : "";

		// $arr_emplist = array();
        // if($emplist){
        	// $arr_emplist = explode(",", $emplist);
        	if(sizeof($arr_emplist) > 0){
        		$hrhead = $this->utils->getDeptHead('head','HR');
        		if($hrhead){
        			$count = $this->leave->saveLeaveHRDirect($arr_emplist, $hrhead, $datefrom, $dateto, $ltype, $othleave, $ndays, $withpay, $reason );
        			$ret = "($count) employee/s successfully applied.";

        		}else $ret = 'No setup for HR Head.';
        	}else $ret = 'No employee selected.';
        // }else $ret = 'No employee selected.';

        echo $ret;

	}
	
	function addIndividualLeaveCredit(){
		$employeeid = $this->input->post('employeeid');
		$ltype = $this->input->post('mh_leavetype');
		$credits = $this->input->post('mh_credits');
		$cutoff = $this->input->post('mh_cutoff');

		$cutoff_arr = explode('|', $cutoff);
		$dfrom = $cutoff_arr[0];
		$dto = $cutoff_arr[1];

		$message = $this->leave->addLeaveCredit($employeeid,$ltype,$credits,$dfrom,$dto);
		echo $message;
	}

	/**
	*  Saves new leave credit details
	*
	* @return string
	*/
	function saveLeaveCredit(){
		$return 	= "";
		$post 		= $this->input->post();
		$employeeid	= isset($post['employeeid'])? $post['employeeid'] 	: 0;
        $VLcredit 	= isset($post['VLcredit']) 	? $post['VLcredit'] 	: 0;
		$SLcredit 	= isset($post['SLcredit']) 	? $post['SLcredit'] 	: 0;
		$ELcredit 	= isset($post['ELcredit']) 	? $post['ELcredit'] 	: 0;
		$BLcredit 	= isset($post['BLcredit']) 	? $post['BLcredit'] 	: 0;
		$MLcredit 	= isset($post['MLcredit']) 	? $post['MLcredit'] 	: 0;
		$MLNcredit 	= isset($post['MLNcredit']) 	? $post['MLNcredit'] 	: 0;
		$STUDLcredit 	= isset($post['STUDLcredit']) 	? $post['STUDLcredit'] 	: 0;
		$SCcredit 	= isset($post['SCcredit']) 	? $post['SCcredit'] 	: 0;

		$VLbalance 	= isset($post['VLbalance']) 	? $post['VLbalance'] 	: 0;
		$SLbalance 	= isset($post['SLbalance']) 	? $post['SLbalance'] 	: 0;
		$ELbalance 	= isset($post['ELbalance']) 	? $post['ELbalance'] 	: 0;
		$BLbalance 	= isset($post['BLbalance']) 	? $post['BLbalance'] 	: 0;
		$MLbalance 	= isset($post['MLbalance']) 	? $post['MLbalance'] 	: 0;
		$MLNbalance 	= isset($post['MLNbalance']) 	? $post['MLNbalance'] 	: 0;
		$STUDLbalance 	= isset($post['STUDLbalance']) 	? $post['STUDLbalance'] 	: 0;
		$SCbalance 	= isset($post['SCbalance']) 	? $post['SCbalance'] 	: 0;

		$leaves = $this->leave->getEmpLeaveCredit($employeeid);
		if($leaves->num_rows() > 0){
			foreach ($leaves->result() as $key => $row) {
				if($row->leavetype == 'VL'){
					if($VLcredit <> $row->credit){
						$newBal = $VLcredit - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'VL','','',$VLcredit,$row->avail,$newBal);
						else 	$return .= "Failed to save VL credit. ";
					}
				}
				if($row->leavetype == 'SL'){
					if($SLcredit <> $row->credit){
						$newBal = $SLcredit - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'SL','','',$SLcredit,$row->avail,$newBal);
						else 	$return .= "Failed to save SL credit. ";
					}
				}
				if($row->leavetype == 'EL'){
					if($ELcredit <> $row->credit){
						$newBal = $ELcredit - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'EL','','',$ELcredit,$row->avail,$newBal);
						else 	$return .= "Failed to save EL credit. ";
					}
				}
				if($row->leavetype == 'BL'){
					if($BLcredit <> $row->credit){
						$newBal = $BLcredit - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'BL','','',$BLcredit,$row->avail,$newBal);
						else 	$return .= "Failed to save BL credit. ";
					}
				}
				if($row->leavetype == 'ML'){
					if($MLcredit <> $row->credit){
						$newBal = $MLcredit - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'ML','','',$MLcredit,$row->avail,$newBal);
						else 	$return .= "Failed to save ML credit. ";
					}
				}
				if($row->leavetype == 'MLN'){
					if($MLNcredit <> $row->credit){
						$newBal = $MLNcredit - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'MLN','','',$MLNcredit,$row->avail,$newBal);
						else 	$return .= "Failed to save MLN credit. ";
					}
				}
				if($row->leavetype == 'STUDL'){
					if($STUDLcredit <> $row->credit){
						$newBal = $STUDLcredit - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'STUDL','','',$STUDLcredit,$row->avail,$newBal);
						else 	$return .= "Failed to save STUDL credit. ";
					}
				}
				if($row->leavetype == 'SC'){
					if($SCcredit <> $row->credit){
						$newBal = $SCcredit - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'SC','','',$SCcredit,$row->avail,$newBal);
						else 	$return .= "Failed to save SC credit. ";
					}
				}

				/*for balance*/
				/*if($row->leavetype == 'VL'){
					if($VLbalance <> $row->balance){
						$newBal = $VLbalance - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'VL','','',$VLbalance,$row->avail,$newBal);
						else 	$return .= "Failed to save VL balance. ";
					}
				}
				if($row->leavetype == 'SL'){
					if($SLbalance <> $row->balance){
						$newBal = $SLbalance - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'SL','','',$SLbalance,$row->avail,$newBal);
						else 	$return .= "Failed to save SL balance. ";
					}
				}
				if($row->leavetype == 'EL'){
					if($ELbalance <> $row->balance){
						$newBal = $ELbalance - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'EL','','',$ELbalance,$row->avail,$newBal);
						else 	$return .= "Failed to save EL balance. ";
					}
				}
				if($row->leavetype == 'BL'){
					if($BLbalance <> $row->balance){
						$newBal = $BLbalance - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'BL','','',$BLbalance,$row->avail,$newBal);
						else 	$return .= "Failed to save BL balance. ";
					}
				}
				if($row->leavetype == 'ML'){
					if($MLbalance <> $row->balance){
						$newBal = $MLbalance - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'ML','','',$MLbalance,$row->avail,$newBal);
						else 	$return .= "Failed to save ML balance. ";
					}
				}
				if($row->leavetype == 'MLN'){
					if($MLNbalance <> $row->balance){
						$newBal = $MLNbalance - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'MLN','','',$MLNbalance,$row->avail,$newBal);
						else 	$return .= "Failed to save MLN balance. ";
					}
				}
				if($row->leavetype == 'STUDL'){
					if($STUDLbalance <> $row->balance){
						$newBal = $STUDLbalance - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'STUDL','','',$STUDLbalance,$row->avail,$newBal);
						else 	$return .= "Failed to save STUDL balance. ";
					}
				}
				if($row->leavetype == 'SC'){
					if($SCbalance <> $row->balance){
						$newBal = $SCbalance - $row->avail;
						if($newBal >= 0) $this->leave->saveLeaveCredit($employeeid,'SC','','',$SCbalance,$row->avail,$newBal);
						else 	$return .= "Failed to save SC balance. ";
					}
				}*/
			}
		}
		if($return) echo $return;
		else echo "Save successful.";
	}

	/**
	*  For editing and adding of leave setup.
	*
	* @return string
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

	function saveLeaveSetup(){
		$toks = $this->input->post("toks");
		$formdata = $this->gibberish->decrypt($this->input->post("formdata"), $toks);
		$data   		= $this->convertFormDataToArray($formdata);
		// if(isset($data['empstatus%5B%5D'])) $data['empstatus'] = array($data['empstatus%5B%5D']);
		$data['empstatus'] = array();
		foreach ($this->input->post("empstatuses") as $key => $value) {
                array_push($data['empstatus'] , $this->gibberish->decrypt($value, $toks));
		}
		$dfrom = $data["datesetfrom"];
		$dto = $data["datesetto"];
		if(!$dfrom || !$dto){
			$return = array('err_code'=>2,'msg'=>'Please fill-up a valid date.','success_count'=>0,'data_failed'=>0); 
		    echo json_encode($return);
		    return;
		}
		if(strtotime($dfrom) > strtotime($dto)){
			$return = array('err_code'=>2,'msg'=>'Please fill-up a valid date.','success_count'=>0,'data_failed'=>0); 
			echo json_encode($return);
			return;
		}
		$return 		= array('err_code'=>2,'msg'=>'Failed to save.','success_count'=>0,'data_failed'=>array());
		

		$checkIfDateOverlapping = $this->leave->leaveOverlapChecker($data['mh_leavetype'], $data["datesetfrom"], $data["datesetto"]);
		// echo "<pre>";print_r($data);die;
		if ($checkIfDateOverlapping != "none" && $data['code'] == "") {
			$return 		= array('err_code'=>2,'msg'=> $checkIfDateOverlapping,'success_count'=>0,'data_failed'=>array());
			echo json_encode($return);
			return;
		}
		///< check if there is selected emp status
        if(!isset($data['empstatus']) && !$data['lid']){
        	$return = array('err_code'=>2,'msg'=>'Failed to save. Please select employment status.','success_count'=>0,'data_failed'=>array());
        }else{

        	if(isset($data['lid']) && $data['lid']){ ///< edit leave setup ----------------------------------------
        		$return = $this->leave->editLeaveSetup($data);

        	}else{ ///< add new leave setup ----------------------------------------
        		
        		$return = $this->leave->addNewLeaveSetup($data);
        	}

        }

        echo json_encode($return);

        
	}

	function getApplicableLeave(){
		$this->load->model('utils');
		$wc = '';
		$return = '';
		$ret = array();
		$employeeid = $this->input->post('empid');
		$empgender = $this->utils->getEmployeeGender($employeeid);
		if($empgender) $wc = " AND genderApplicable = '$empgender' OR  genderApplicable = '' ";

		$query = $this->db->query("SELECT * FROM code_request_form WHERE is_leave = 1 AND ismain = 0 $wc ")->result_array();

		$return .= "<option value=''>Select Leave</option>";
		foreach($query as $code_val){
			$code_r = $code_val['code_request'];
	            $pos = strpos($code_r, 'NON');
	            $pos1 = strpos($code_r, 'HEAD');
	            if($pos==false && $pos1==false){
	            	$return .= "<option value='".$code_val['code_request']."'>".$code_val['description']."</option>";
				}
		}
		echo $return;
	}

	function getAvailableOtherLeave(){
		$this->load->model("leave_application");
		$data = array();
		$data["other_leave"] = array();
		$employeeid = $this->input->post('employeeid');
		
		$other_leave_arr = array();
		$q_other_leave = $this->leave_application->getOtherLeave();
		foreach ($q_other_leave as $row) $other_leave_arr[$row->code_request] = $row->description;

		$q_available_other_leave = $this->leave_application->getAvailableEmployeeOtherLeave($employeeid);
		foreach ($q_available_other_leave as $row){
			if(array_key_exists($row->leavetype, $other_leave_arr) && $row->balance > 0){
				$data["other_leave"][$row->leavetype] = $other_leave_arr[$row->leavetype];
			}
		}

 		echo json_encode($data);
	}

	function loadLeaveReports(){
        $this->load->model('service_credit');
        $this->load->model('ob_application');
        $this->load->model('leave_application');
        $this->load->model('hr_reports');

        $data = array();
        $leavetype = '';
        $reportname = $this->input->post('form');
        $reportformat = $this->input->post('reportformat');
        $pdfreport = $this->input->post('pdfreport');
        
        if($reportname == 'leavereport'){

            $dfrom      = $this->input->post('dfrom');
            $dto        = $this->input->post('dto');
            $type       = $this->input->post('type');
            if($this->input->post('type')){
                $leavetype = implode("','",$type);
            }


			if ($pdfreport == 'detailed') {
			 ///< get necessary data here
			            $q_leave = $this->leave_application->getLeaveHistory(false,$leavetype,'',$dfrom,$dto,'','',"ORDER BY lname,a.leavetype,fromdate",true);
			            $q_service_credit = $this->service_credit->getServiceCreditHistory($dfrom,$dto);
			            $q_ob_app = $this->ob_application->getObAppHistory($dfrom,$dto);
            
			            
			            $data["list"] = array();
			            foreach ($q_leave as $row) {
			            	$formattedString = mb_strtolower($row->remarks);
							$remarks = iconv('UTF-8', 'utf-8//TRANSLIT', $formattedString);
			            	$data["list"][$row->teachingtype][] = array(
			            		"name" => $row->fullname,
			            		"position" => $row->posdesc,
			            		"department" => $row->deptid,
			            		"type" => $row->leavetype,
			            		"days" => $row->no_days,
			            		"date_exclusive" => date("M d, Y",strtotime($row->fromdate)). " - " . date("M d, Y",strtotime($row->todate)),
			            		"reason" => $remarks,
			            		"balance" => $row->balance
			            	);
			            }


			            if(in_array("SC", $type)){
			            	foreach ($q_service_credit as $row) {
				            	$data["list"][$row->teachingtype][] = array(
				            		"name" => $row->fullname,
				            		"position" => $row->description,
				            		"department" => $row->deptid,
				            		"type" => "SC",
				            		"days" => $row->total_sc,
				            		"date_exclusive" => date("M d, Y",strtotime($row->date)). " - " . date("M d, Y",strtotime($row->date)),
				            		"reason" => "",
				            		"balance" => $row->available_sc
				            	);
				            }
			            }

			            if(in_array("DA", $type)){
			            	foreach ($q_ob_app as $row) {
			            		$othertype = "";
								if($row->othertype == "DIRECT") $othertype = "OB";
								if($row->othertype == "CORRECTION") $othertype = "CORRECTION";

				            	$data["list"][$row->teachingtype][] = array(
				            		"name" => $row->fullname,
				            		"position" => $row->description,
				            		"department" => $row->deptid,
				            		"type" => $othertype,
				            		"days" => $row->no_days,
				            		"date_exclusive" => date("M d, Y",strtotime($row->fromdate)). " - " . date("M d, Y",strtotime($row->todate)),
				            		"reason" => $row->remarks,
				            		"balance" => "--"
				            	);
				            }
			            }
                $data["user_data"] = $this->employee->getHRUserAndUserFullname($this->session->userdata('username'));

			}else{
				 $data['leavedata']  = $this->hr_reports->getLeaveReportSummary($leavetype,$dfrom,$dto);
			}
            
            $data['dfrom'] = $this->input->post('dfrom');
            $data['dto'] = $this->input->post('dto');

            ob_end_clean();
			ob_start();
			
        	if($pdfreport == 'detailed') $this->load->view('forms_pdf/leave-reports',$data);
        	else $this->load->view('reports_excel/leavereport',$data);

        }
	}

	public function getEmployeeLeaveList(){
		$toks = $this->input->post("toks");
		$employeeid = ($toks) ? $this->gibberish->decrypt($this->input->post("employeeid"), $toks) : $this->input->post("employeeid");
		$leavetype = ($toks) ? $this->gibberish->decrypt($this->input->post("leavetype"), $toks) : $this->input->post("leavetype");
		$date = ($toks) ? $this->gibberish->decrypt($this->input->post("date"), $toks) : $this->input->post("date");
		$option = "<option value=''> - Select a leave - </option>";
		$data = $this->leave->getEmployeeLeaveList($employeeid, $date);
		// echo "<pre>";print_r($this->db->last_query());die;
		if($data){
			foreach($data as $row){
				$option .= "<option value='".Globals::_e($row["leavetype"])."'".($leavetype == Globals::_e($row["leavetype"]) ? 'selected' : '').">".Globals::_e($row["description"])."</option>";
			}
		}

		echo $option;
	}

	public function getAvailableDateForLeave(){
		$data = $this->input->post();
		$toks = $this->input->post("toks");
		if($toks){
			foreach($data as $key => $val){
				$data[$key] = $this->gibberish->decrypt($val, $toks);
			}
		}
		$empID = $this->session->userdata('username');
		$isAdmin = $this->extras->findIfAdmin($empID);
		$data["isAdmin"] = $isAdmin;
		$this->load->view("employeemod/leave_app/leavedates", $data);
	}

	public function updateLeaveCredit(){
		$data = $this->input->post();
		extract($data);
		$this->leave->updateLeaveCreditData($employeeid, $lt, $balance, $credit, $avail);
	}

	public function getSeminarAllowance(){
		$this->load->model("leave_application");
		$toks = $this->input->post("toks");
		$employeeid = ($toks) ? $this->gibberish->decrypt($this->input->post("employeeid"), $toks) : $this->input->post("employeeid");
		echo $this->leave_application->getRemAllowance($employeeid);
	}

	public function checkIfSameSchedLeave(){
		$toks = $this->input->post("toks");
		$start = $this->gibberish->decrypt($this->input->post("start"), $toks);
		$end = $this->gibberish->decrypt($this->input->post("end"), $toks);
		$employeeid = true;
		if(isset($data['empID'])){
			if($toks) $employeeid = $this->gibberish->decrypt($data["empID"], $toks);
			else $employeeid = $data["empID"];
		}else{
			$employeeid = $this->session->userdata('username');
		}
		$idx = array();
		$qdate = $this->attcompute->displayDateRange($start, $end);
		foreach($qdate as $rdate){
			$idx[] = date("w", strtotime($rdate->dte));
		}
		$idx = "'" . implode ( "', '", $idx ) . "'";
		$sched = $this->leave->getEmployeeSameScheduleLeave($employeeid, $idx); 
		// echo "<pre>"; print_r($this->db->last_query()); die;
		if($sched->num_rows() > 2 || $sched->num_rows() == 0) echo false;
		else echo true;
	}

	public function getEmployeeLeaveSchedule(){
		$toks = $this->input->post("toks");
		$base_id = $this->gibberish->decrypt($this->input->post("base_id"), $toks);

		$sched_arr = array();
		$leave_d = $this->db->query("SELECT a.base_id, b.datefrom, employeeid FROM leave_app_emplist a INNER JOIN leave_app_base b ON a.base_id = b.id WHERE a.id = '$base_id'");
				// echo "<pre>"; print_r($this->db->last_query()); die;
		
		if($leave_d->num_rows() > 0){
			$leave_id = $leave_d->row()->base_id;
			$date = $leave_d->row()->datefrom;
			$employeeid = $leave_d->row()->employeeid;
			$leave_sched = $this->db->query("SELECT * FROM leave_schedref WHERE base_id = '$base_id' ");
			if($leave_sched->num_rows() > 0){
				$dateactive = $leave_sched->row()->dateactive;
				$sched = $this->leave->getEmployeeScheduleLeave($employeeid, $date);
				// $sched = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid = '$employeeid' AND dateactive = '$dateactive' AND idx  = DATE_FORMAT('$date','%w') ");
				$isAm = $isBoth = 0; ///< for checking if schedule is subject for a whole day leave
			// echo "<pre>"; print_r($this->db->last_query()); die;

				if($sched->num_rows() > 0){
					foreach ($sched->result() as $key => $row) {
							$isAm 	= ( (strtotime($row->starttime) < strtotime('12:00:00') && strtotime($row->endtime) <= strtotime('12:00:00')) ? 1 : 0 );
							$isBoth = ( (strtotime($row->starttime) < strtotime('12:00:00') && strtotime($row->endtime) > strtotime('12:00:00')) ? 1 : 0 );

							$sched_arr[$row->starttime.'|'.$row->endtime.'|'.((strtotime($row->endtime) - strtotime($row->starttime)) / 3600).'|'.$isAm.'|'.$isBoth] 
									= date('h:i A', strtotime($row->starttime)) . ' - ' . date('h:i A', strtotime($row->endtime));
					}
				}
			}else{
				$data = $this->input->post();
				if(isset($data['empID'])){
					if($toks) $employeeid = $this->gibberish->decrypt($data["empID"], $toks);
					else $employeeid = $data["empID"];
				}else{
					$employeeid = $this->session->userdata('username');
				}
				
				$sched = $this->leave->getEmployeeScheduleLeave($employeeid, $date);
				if($sched->num_rows() > 0){
					foreach ($sched->result() as $key => $row) {
							$isAm 	= ( (strtotime($row->starttime) < strtotime('12:00:00') && strtotime($row->endtime) <= strtotime('12:00:00')) ? 1 : 0 );
							$isBoth = ( (strtotime($row->starttime) < strtotime('12:00:00') && strtotime($row->endtime) > strtotime('12:00:00')) ? 1 : 0 );

							$sched_arr[$row->starttime.'|'.$row->endtime.'|'.((strtotime($row->endtime) - strtotime($row->starttime)) / 3600).'|'.$isAm.'|'.$isBoth] 
									= date('h:i A', strtotime($row->starttime)) . ' - ' . date('h:i A', strtotime($row->endtime));
					}
				}
			}
		}

		echo json_encode($sched_arr);
	}

	public function onlineApplicationList(){
		$type = $this->input->post("type");
		$data = array();
		$app_list = $this->setup->onlineApplicationList($type);
		if($app_list->num_rows() > 0){
			$data["app_list"] = $app_list->result();
			foreach($app_list->result() as $row){
				$app_base_list = $this->setup->onlineApplicationBaseList($row->id);
				if($app_base_list->num_rows() > 0){
					$data["app_base_list"][$row->id] = $app_base_list->result();
				}
			}
		}
		// echo"<pre>";print_r($data);die;
		if($type == "other") $this->load->view("maintenance/other_online_application_list", $data);
		else $this->load->view("maintenance/online_application_list", $data);
	}
	
} //endoffile