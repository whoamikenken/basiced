<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Process_ extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */

  function __construct(){
      parent::__construct();
      if(!$this->session->userdata('username')) redirect('main/suddenLogout'); ///< prevent access to routes without session
  }
  
	public function index()
	{
        # nothing
	}
    function verifyemployee(){
      $stat = 0;
      $cutofid = "";  
      $fullname = "";
      $employeeid = ""; 
      $employeeid = $this->input->post("employeeid");
    
      $q = $this->db->query("select a.employeeid,a.income_base,a.lname,a.fname,a.mname 
                             from employee a 
                             where (trim(a.employeeid)=TRIM('{$employeeid}') or TRIM(CONCAT(a.LName,', ',a.FName,' ',a.MName))=TRIM('{$employeeid}')) limit 1");
      
      $stat = $q->num_rows()>0 ? 1 : 0;
      $cutofid = "";
      # echo $q;
      if($q->num_rows()>0){
        $mrow = $q->row(0);
        $fullname = $mrow->lname . ", " . $mrow->fname . " " . $mrow->mname;
        $employeeid = $mrow->employeeid;
      } 
      
      echo "<user>
              <status>{$stat}</status>
              <fullname>{$fullname}</fullname>
              <employeeid>{$employeeid}</employeeid>
            </user>";
    }
    function displayschedule(){  
      $toks = $this->input->post("toks");
      $data['employeeid'] = $this->gibberish->decrypt( $this->input->post("employeeid"), $toks );
      $data['dto'] = $this->gibberish->decrypt( $this->input->post("dto"), $toks );
      $data['dtoedit'] = $this->gibberish->decrypt( $this->input->post("dtoedit"), $toks );
      $data['chkbox'] = $this->gibberish->decrypt( $this->input->post("chkbox"), $toks );
      $data['title'] = '';
      // $view = $this->input->post('view');
      #echo $data['chkbox'];
      $this->load->view('process/plot_schedule', $data);  
    }
    function manageschedule(){  
      $data['employeeid'] = $this->input->post("employeeid");
      $data['shifttype'] = $this->input->post("shifttype");
      $data['chkopt'] = $this->input->post("chkopt");
      $data['title'] = '';
      # echo $data['employeeid'];
      $this->load->view('process/view_sched', $data);  
    }
    function viewofficialschedhis(){
      $data['employeeid'] = $this->input->post("employeeid");
      $data['job'] = $this->input->post("job");
      $this->load->view('process/view_sched_history');
    }
    function displayschedule_dtr(){
      $data['cutoffid'] = $this->input->post("cutoffid");  
      $data['employeeid'] = $this->input->post("employeeid");
      $data['title'] = '';
      # echo $data['employeeid'];
      $this->load->view('process/plot_schedule_dtr', $data);  
    }
    function displayschedule_ms(){
      $data['cutoffid'] = $this->input->post("cutoffid");  
      $data['employeeid'] = $this->input->post("employeeid");
      $data['title'] = '';
      # echo $data['employeeid'];
      $this->load->view('process/plot_schedule_ms', $data);  
    }
    function viewdtr(){
      $data['employeeid'] = $this->input->post("employeeid");
      $data['title'] = '';
      # echo $data['employeeid'];
      $this->load->view('process/viewdtr', $data);  
    }
    function salary(){
      $data['employeeid'] = $this->input->post("employeeid");
      $data['cid'] = $this->input->post("cid");
      $data['title'] = '';
      $this->load->view('process/viewsalary', $data); 
    }
    function cardsetuplist(){
      $data['ltype'] = $this->input->post("ltype");
      $data['dept'] = $this->input->post("dept");
      $data['yearlevel'] = $this->input->post("yearlevel");
      $data['section'] = $this->input->post("section");
      $data['sy'] = $this->input->post("sy");
      $data['sem'] = $this->input->post("sem");
      $data['departmentid'] = $this->input->post("departmentid");
      $data['status'] = $this->input->post("status");
      $data['empstat'] = $this->input->post("empstat");
      $data['office'] = $this->input->post("office");
      $data['teachingtype'] = $this->input->post("teachingtype");
      $this->load->view('process/viewcardsetuplist', $data);  
    }
    function savecardid(){
        $toks = $this->input->post("toks");
        $newcode = $this->gibberish->decrypt( $this->input->post("newcode"), $toks );
        $eid = $this->gibberish->decrypt( $this->input->post("eid"), $toks );
        $entype = $this->gibberish->decrypt( $this->input->post("entype"), $toks );

        if($entype == 'S'){
               $msg = "";
              $query = $this->db->query("SELECT * FROM student WHERE studentcode='{$newcode}' AND studentid != '{$eid}'");
              if ($query->num_rows() > 0) 
              {
                $msg = "EXIST";
              }
              else{
                $this->db->query("CALL prc_cardno_set('$entype','$eid','$newcode')");          
              }

        }else{

              $msg = "";
              $query = $this->db->query("SELECT * FROM employee WHERE employeecode='{$newcode}' AND employeeid != '{$eid}'");
              if ($query->num_rows() > 0) 
              {
                $msg = "EXIST";
              }
              else{
                $this->db->query("CALL prc_cardno_set('$entype','$eid','$newcode')");          
              }
        }
        echo $msg;
    }
    //glen mark
    function addbyBatchEncode()
    {
       $this->load->view('process/addadjustmentbybatch');   
    }

    //glenmark
    function showAdjustment()
    {
      $data = $this->input->post();
      $this->load->view('process/adjustmentperbatch',$data);    
    }
    //justin
    function addnewadjustment(){
       $toks = $this->input->post("toks");
       $data['job'] = $this->gibberish->decrypt( $this->input->post("job"), $toks );
       $data['adateid'] = $this->gibberish->decrypt( $this->input->post("adateid"), $toks );       
       $data['uid'] = $this->gibberish->decrypt( $this->input->post("uid"), $toks );
       $data['chkbox'] = $this->gibberish->decrypt( $this->input->post("chkbox"), $toks );
       $data['dto'] = $this->gibberish->decrypt( $this->input->post("dto"), $toks );
       #echo $data['adateid'].' - '.$data['chkbox'];
       // echo "<pre>";print_r($data);die;
       $this->load->view('process/addadjustment_new', $data);     
    }
    //justin - 08-13-15
    function addnewcutoff(){
       $this->load->view('process/addnewcutoff');     
    }
    function editcutoff(){
       $this->load->view('process/editcutoff',$this->input->post());
    }

    function saverequest_dtr(){
         $dow = array("SUN","M","T","W","TH","F","SAT");
         $cutoffid = $this->input->post("cutoffid");
         $employeeid = $this->input->post("employeeid");
         $timeid = $this->input->post("timeid");
         $cdate = $this->input->post("cdate");
         
         $tfrom = date("H:i:s",strtotime($this->input->post("tfrom")));
         $tto = date("H:i:s",strtotime($this->input->post("tto")));
         
         $this->db->query("CALL prc_timesheet_manual_set('0','{$timeid}','{$employeeid}','{$cdate} {$tfrom}','{$cdate} {$tto}','".$this->session->userdata('userid')."',@tid);");
         $q = $this->db->query("select @tid as timeid");  
         echo "<user><timeid>".$q->row(0)->timeid."</timeid></user>";
         # echo "<user><timeid>{$timeid}</timeid></user>";
    }
    function saverequest_ms(){
         $dow = array("SUN","M","T","W","TH","F","SAT");
         $cutoffid = $this->input->post("cutoffid");
         $employeeid = $this->input->post("employeeid");
         $timeid = $this->input->post("timeid");
         $cdate = $this->input->post("cdate");
         
         $tfrom = date("H:i:s",strtotime($this->input->post("tfrom")));
         $tto = date("H:i:s",strtotime($this->input->post("tto")));
         
         $ctype = $this->input->post("ctype");
         $idx = date("w",strtotime($cdate));
         
         
         $this->db->query("CALL prc_employee_sched_percutoff_set('{$cutoffid}','{$cdate}','{$employeeid}','{$tfrom}','{$tto}','{$dow[$idx]}','{$idx}','".$this->session->userdata('userid')."','{$ctype}');");
    }
    function clearschedfirst(){
         $cutoffid = $this->input->post("cutoffid");
         $employeeid = $this->input->post("employeeid");
         $this->db->query("delete from employee_schedule_percutoff where employeeid='{$employeeid}' and cutoffid='{$cutoffid}'");
    }
    function deletetimeload_dtr(){
        $employeeid = $this->input->post("employeeid");
        $timeid = $this->input->post("timeid");
        $this->db->query("CALL prc_timesheet_manual_set('1','{$timeid}','{$employeeid}','0000-00-00 00:00:00','0000-00-00 00:00:00','',@tid);");        
    }
    function processdtr(){
        $cutoffid=$this->input->post("cid");
        $se = $this->db->query("SELECT id FROM cutoff_summary ".($cutoffid ? "where id='{$cutoffid}' " : "")."ORDER BY `datefrom` DESC LIMIT 1;");
        if($se->num_rows()>0){
           $cutoffid=$se->row(0)->id;   
        }
        $this->db->query("CALL prc_process_timesheetload('{$cutoffid}',@res,@message);");      
        //$this->db->query("CALL prc_process_payroll_percutoff('{$cutoffid}',@res,@message);");
  
        $q = $this->db->query("select @res as result_view,@message as message");  
        echo "<user><result>".$q->row(0)->result_view."</result><message>".$q->row(0)->message."</message></user>";
    }
    function processpayrollcutoff(){
        $cutoffid=$this->input->post("cid");
        $se = $this->db->query("SELECT id,cutoff_period FROM cutoff_summary ".($cutoffid ? "where id='{$cutoffid}' " : "")."ORDER BY `datefrom` DESC LIMIT 1;");
        if($se->num_rows()>0){
           $cutoffid=$se->row(0)->id; 
           $period = $se->row(0)->cutoff_period;  
        }
        $this->db->query("CALL prc_process_payroll_percutoff('{$cutoffid}','{$period}');");        
    }
    function dbadjustmentlist($userid){
        $this->load->library('datatables');
        $this->datatables
             #->select('a.id,a.cdate,DATE_FORMAT(a.starttime,"%h:%i %p"),DATE_FORMAT(a.endtime,"%h:%i %p"),b.description',false)
             ->select('a.cdate,DATE_FORMAT(a.starttime,"%h:%i %p"),DATE_FORMAT(a.endtime,"%h:%i %p"),b.description,a.editedby,DATE_FORMAT(a.timestamp,"%b. %d, %Y %h:%i %p")',false)
             #->edit_column('a.id', 
             #              '<div class="btn-group">
             #                 <a class="btn" href="#modal-view" tag="edit_d" data-toggle="modal" adateid="$1"><i class="glyphicon glyphicon-edit"></i></a>
             #                 <a class="btn" href="#" tag="delete_d" adateid="$1"><i class="glyphicon glyphicon-trash"></i></a>
             #               </div>',
             #              'a.id')
             ->where('employeeid',$userid)                              
             ->from('employee_schedule_adjustment as a')
             ->join('code_request_type as b','b.id=a.remarks','left');
        $results = $this->datatables->generate('json');
        echo $results;
    }
    // FUNCTIONS ADDED BY JUSTIN 
    function dbadjustmentlistall($dto){
        $this->load->library('datatables');
        $this->datatables
             ->select("a.employeeid,CONCAT(a.lname,', ',a.fname,' ',a.mname) as fullname",false)
             ->edit_column("a.employeeid",
                           "<div class='btn-group'>
                              <a class='btn' style='margin-left: 22px; width: 100%;' href='#modal-view' tag='add_d' data-toggle='modal' aempid='$1'><i class='glyphicon glyphicon-plus-sign'></i></a>
                            </div>",
                           "a.employeeid")
             ->from("employee as a")
             #->where("(a.dateresigned = '1970-01-01' OR a.dateresigned is null) AND a.employeeid NOT IN (SELECT employeeid FROM employee_schedule_adjustment WHERE cdate='$dto')");\
             ->where("(a.dateresigned = '1970-01-01' OR a.dateresigned is null) 
             AND a.employeeid NOT IN (SELECT employeeid FROM employee_schedule_adjustment WHERE cdate='$dto') 
             AND a.employeeid NOT IN (SELECT userid FROM timesheet_noout WHERE SUBSTR(localtimein,1,10)='$dto' AND log_type='IN') ");
        $results = $this->datatables->generate('json');
        echo $results;
    }
    function dbadjustmentlistedit($dtoedit){
        /*
        // last modified by justin dated : 02-18-2015
        $param = "cdate = '$dtoedit' AND (a.starttime IS NULL OR a.endtime IS NULL)";
        #$param = "(SUBSTRING(c.timein,1,10)='$dtoedit' AND (SUBSTRING(c.timein,12,5) >= SUBSTRING(c.timeout,12,5)))";
        $this->load->library('datatables');
        $this->datatables
             ->select('a.id,CONCAT(b.lname,", ",b.fname," ",b.mname) as fullname,DATE_FORMAT(a.starttime,"%h:%i %p"),DATE_FORMAT(a.endtime,"%h:%i %p"),a.employeeid',false)
             #->select('a.id,CONCAT(b.lname,", ",b.fname," ",b.mname) as fullname,DATE_FORMAT(c.timein,"%h:%i %p"),DATE_FORMAT(c.timeout,"%h:%i %p"),a.employeeid',false)
             ->edit_column('a.id,a.employeeid', 
                           '<div class="btn-group">
                              <a class="btn" href="#modal-view" tag="edit_d" data-toggle="modal" adateid="$1" uid="$2"><i class="glyphicon glyphicon-edit"></i></a>
                              <a class="btn" href="#" tag="delete_d" adateid="$1" uid="$2"><i class="glyphicon glyphicon-trash"></i></a>
                            </div>',
                           'a.id,a.employeeid')                         
             ->from('employee_schedule_adjustment as a')
             ->join('employee as b','a.employeeid = b.employeeid','inner')
             #->join('employee_schedule_adjustment as c','a.employeeid = c.userid','right')
             ->where($param);
        $results = $this->datatables->generate('json');
        echo $results;
        */
        
        #$param = "SUBSTR(timein,1,10) = '$dtoedit'";
        #$param = "b.employeeid = '06-0388' AND SUBSTR(timein,1,10) = '$dtoedit' AND SUBSTR(timein,12,5) >= SUBSTR(timeout,12,5)";        
        #$param = "SUBSTR(timein,1,10) = '$dtoedit' AND SUBSTR(timein,12,5) = SUBSTR(timeout,12,5)";
        #$param = "SUBSTR(timein,1,10) = '$dtoedit' AND (TIME_FORMAT(TIMEDIFF(SUBSTR(a.timeout, 12,5),SUBSTR(a.timein,12,5)),'%i') = '00' OR TIME_FORMAT(TIMEDIFF(SUBSTR(a.timeout, 12,5),SUBSTR(a.timein,12,5)),'%i') = '01' )";
        
        // last modified by justin dated : 04-11-2015
        $param = "SUBSTR(timein,1,10) = '$dtoedit' AND (TIME_TO_SEC(TIMEDIFF(timeout,timein)) <= '60' OR (timein='0000-00-00 00:00:00' OR timeout='0000-00-00 00:00:00') ) ";
        // last modified by justin dated : 09-22-2015 : 1 line below added
        $param .= " AND a.userid NOT IN (SELECT employeeid FROM employee_schedule_adjustment d WHERE d.employeeid=a.userid AND d.cdate='$dtoedit' AND d.starttime IS NOT NULL AND d.endtime IS NOT NULL)";
        
        $this->load->library('datatables');
        $this->datatables
             ->select('a.timeid,CONCAT(b.lname,", ",b.fname," ",b.mname) as fullname,IF(timein = "0000-00-00 00:00:00" ,"",DATE_FORMAT(timein,"%h:%i %p")) as timein,IF(timeout = "0000-00-00 00:00:00","",DATE_FORMAT(timeout,"%h:%i %p")) as timeout,a.userid',false)
             ->edit_column('a.timeid,a.userid', 
                           '<div class="btn-group">
                              <a class="btn" href="#modal-view" tag="edit_d" data-toggle="modal" adateid="$1" uid="$2"><i class="glyphicon glyphicon-edit"></i></a>
                              <!--<a class="btn" href="#" tag="delete_d" adateid="$1" uid="$2"><i class="glyphicon glyphicon-trash"></i></a>-->
                            </div>',
                           'a.timeid,a.userid')                         
             ->from('timesheet as a')
             ->join('employee as b','a.userid = b.employeeid','inner')
             ->where($param);
             #->where($param);
        $results = $this->datatables->generate('json');
        echo $results;
        
    } 
    
    function saverequest(){
        $dow = array("SUN","M","T","W","TH","F","SAT");
        $job = $this->input->post("job")==1?1:0;
        $employeeid = $this->input->post("uid");
        $chkbox = $this->input->post("chkbox");
        $adjustid = $this->input->post("adateid") ? $this->input->post("adateid") : 0;
        $cdate = date("Y-m-d", strtotime($this->input->post("u_date")));

        $cfrom = $this->input->post("u_timein")?date("H:i:s",strtotime("2001-01-01 " . $this->input->post("u_timein"))):"";
        $cto = $this->input->post("u_timeout")?date("H:i:s",strtotime("2001-01-01 " . $this->input->post("u_timeout"))) : "";

        // modified by: justin 02-12-2015 
        if($chkbox == "chkdate"){
            $rsdemp = $this->employee->getempforsched($adjustid);
                foreach($rsdemp as $erow){
                $employeeid = $erow -> employeeid;
                }
        }
        
        $editedby = $this->session->userdata("username");
        // last modified by: justin 02-16-2015
        
        // modified by justin (with e) 08/30/2017
        if($cfrom != "" && $cto != ""){
            $this->db->query("DELETE FROM employee_schedule_adjustment WHERE employeeid='{$employeeid}' AND cdate='{$cdate}'");
            $this->db->query("DELETE FROM timesheet WHERE userid='{$employeeid}' AND timein LIKE '%{$cdate}%'");
            $timein = date("Y-m-d H:i:s", strtotime($cdate ." ". $cfrom));
            $timeout = date("Y-m-d H:i:s", strtotime($cdate ." ". $cto));
            $this->db->query("INSERT INTO timesheet (userid, timein, timeout) VALUES ('{$employeeid}','{$timein}','{$timeout}')");
        }
        // end of modified
        
        $remarks = $this->input->post("u_remarks");
        $idx = date("w",strtotime($cdate));
        $this->db->query("CALL prc_employee_adjusment_set('{$job}','{$adjustid}','{$employeeid}','{$cdate}','{$cfrom}','{$cto}','{$dow[$idx]}','{$idx}','{$remarks}','{$editedby}',@tid);");
        echo "CALL prc_employee_adjusment_set('{$job}','{$adjustid}','{$employeeid}','{$cdate}','{$cfrom}','{$cto}','{$dow[$idx]}','{$idx}','{$remarks}','{$editedby}',@tid);";
        $q = $this->db->query("select @tid as adjustid");  
        echo "<user><adjustid>".$q->row(0)->adjustid."</adjustid></user>";
    }
    
    function savecutoff(){
        $this->load->model('payrolloptions');
        $return = "";
        $toks = $this->input->post("toks");
        $formdata = $this->input->post("formdata");
        $data = ($formdata) ? Globals::convertFormDataToArray($this->gibberish->decrypt( $formdata, $toks )) : '';
        $id             = isset($data['dkey']) ? $data['dkey'] : $this->input->post('dkey');
        $dto            = isset($data['dto']) ? $data['dto'] : $this->input->post("dto");
        $dfrom          = isset($data['dfrom']) ? $data['dfrom'] : $this->input->post("dfrom");
        $postedby       = $this->session->userdata("username");
        $payrollsched   = isset($data['payrollschedule']) ? $data['payrollschedule'] : $this->input->post("payrollschedule");
        $payrollquarter = isset($data['payrollquarter']) ? $data['payrollquarter'] : $this->input->post("payrollquarter");
        $payrolldto     = isset($data['payrolldto']) ? $data['payrolldto'] : $this->input->post("payrolldto");
        $payrolldfrom   = isset($data['payrolldfrom']) ? $data['payrolldfrom'] : $this->input->post("payrolldfrom");
        $confrmdfrom    = isset($data['confirm_dfrom']) ? $data['confirm_dfrom'] : $this->input->post("confirm_dfrom");
        $confrmdto      = isset($data['confirm_dto']) ? $data['confirm_dto'] : $this->input->post("confirm_dto");
        $tfrom      = isset($data['tfrom']) ? $data['tfrom'] : $this->input->post("tfrom");
        $tto      = isset($data['tto']) ? $data['tto'] : $this->input->post("tto");
        $user           = $this->session->userdata('username');
        $nodtr          = isset($data['nodtr']) ? $data['nodtr'] : $this->input->post('nodtr');

        $tfrom = date("H:i:s", strtotime($tfrom));
        $tto = date("H:i:s", strtotime($tto));
        if ($id) $return = $this->payrolloptions->cutoffsetup($dfrom,$dto,$postedby,$id,$payrollsched,$payrollquarter,$payrolldfrom,$payrolldto,$confrmdfrom,$confrmdto,$user,$nodtr,$tfrom,$tto);
        else $return = $this->payrolloptions->cutoffsetup($dfrom,$dto,$postedby,$id,$payrollsched,$payrollquarter,$payrolldfrom,$payrolldto,$confrmdfrom,$confrmdto,$user,$nodtr,$tfrom,$tto);
        // echo "<pre>"; print_r($this->db->last_query()); die;
        echo json_encode($return); 
    
    }
    
    function deleteadjustload(){
        $employeeid = $this->input->post("employeeid");
        $adjustid = $this->input->post("adjustid") ? $this->input->post("adjustid") : 0;
        
        $cutoffid="";
        $se = $this->db->query("SELECT id FROM cutoff_summary WHERE is_process=0 ORDER BY `datefrom` DESC LIMIT 1;");
        if($se->num_rows()>0){
           $cutoffid=$se->row(0)->id;   
        }
        # echo "CALL prc_employee_adjusment_set('1','{$adjustid}','{$cutoffid}','{$employeeid}','0000-00-00','0000-00-00 00:00:00','0000-00-00 00:00:00','','0','','',@tid);";
        $this->db->query("CALL prc_employee_adjusment_set('1','{$adjustid}','{$cutoffid}','{$employeeid}','0000-00-00','0000-00-00 00:00:00','0000-00-00 00:00:00','','0','','',@tid);");  
    }
    
    function deletetimeload(){
        $cutoffid = $this->input->post("cutoffid");
        $counterid = $this->input->post("counterid");
        
        $nwhere = array("counterid"=>$counterid,"id"=>$cutoffid);                
        $this->db->delete("timesheet_load",$nwhere);        
    }
    function searchemployee(){
        $search = $this->input->post("s");
        $js = "";
        # if($search) $js = " where (employeeid like '{$search}%' OR lname like '{$search}%' OR fname like '{$search}%')";
        $sql = $this->db->query("select TRIM(CONCAT(lname,', ',fname,' ',mname)) as ser from employee where CONCAT(lname,', ',fname,' ',mname) like '{$search}%' union all select TRIM(employeeid) as ser from employee where employeeid like '{$search}%'");
        if($sql->num_rows() > 0){
           $return = "";
           for($c=0;$c<$sql->num_rows();$c++){
             $mrow = $sql->row($c);
             $return .= ($return ? ",":"");
             $return .= "\"" . $mrow->ser. "\"";   
           }
           $return = "Array({$return})";
           echo $return;
        }
    }

     function showalldeptlogs(){
        $toks = $this->input->post("toks");
        $cutoffdata = $toks ? $this->gibberish->decrypt( $this->input->post("cutoff"), $toks ) : $this->input->post('cutoff');
        $cutoff  = explode(',', $cutoffdata);
        $data['datesetfrom'] = $cutoff[0];
        $data['datesetto'] = $cutoff[1];
        $data['deptid'] = $toks ? $this->gibberish->decrypt( $this->input->post("deptid"), $toks ) : $this->input->post("deptid");
        $data['fv'] = $toks ? $this->gibberish->decrypt( $this->input->post("fv"), $toks ) : $this->input->post("fv");
        // $data['edata'] = $this->input->post("edata");
        $this->load->view('process/displaylogsdept', $data);
    }
    //old
    function showalllogs(){
        $toks = $this->input->post("toks");
        $data['datesetfrom'] = $toks ? $this->gibberish->decrypt( $this->input->post("datesetfrom"), $toks ) : $this->input->post("datesetfrom");
        $data['datesetto'] = $toks ? $this->gibberish->decrypt( $this->input->post("datesetto"), $toks ) : $this->input->post("datesetto");
        $data['fv'] = $toks ? $this->gibberish->decrypt( $this->input->post("fv"), $toks ) : $this->input->post("fv");
        $data['deptid'] = $toks ? $this->gibberish->decrypt( $this->input->post("deptid"), $toks ) : $this->input->post("deptid");
        $data['edata'] = $toks ? $this->gibberish->decrypt( $this->input->post("edata"), $toks ) : $this->input->post("edata");
        $this->load->view('process/displaylogs', $data);
    }
    function showattendancesummary(){
        $this->load->model("attendance");
        $data['datesetfrom'] = $this->input->post("datesetfrom");
        $data['datesetto'] = $this->input->post("datesetto");
        $data['fv'] = $this->input->post("fv");
        $data['deptid'] = $this->input->post("deptid");
        $data['edata'] = $this->input->post("edata");
        $data['tnt'] = $this->input->post("tnt");
        $data['estatus'] = $this->input->post("estatus");
        $this->load->view('process/attendance_summary_combine', $data);
    }

     function saveEmployeeAttendanceSummaryDept(){
        $this->load->model('attendance');
        $this->load->model('utils');
        $success_count  = 0;
        $return = array();

        $user       = $this->session->userdata("username");

        if(!$user){
          $return = array('err_code'=>2,'msg'=>'Failed to save. Your session may have expired. Please refresh page and try again.','success_count'=>$success_count,'data_failed'=>array()); 
          echo json_encode($return);
          return;
        }
        $toks = $this->input->post("toks");
        $cutoff = $toks ?  $this->gibberish->decrypt($this->input->post('cutoff'), $toks) : $this->input->post('cutoff');
        $cutoff_arr   = explode(",",$cutoff);
        $dfrom        = isset($cutoff_arr[0]) ? $cutoff_arr[0] : '';
        $dto          = isset($cutoff_arr[1]) ? $cutoff_arr[1] : '';

        $recompute    = $toks ?  $this->gibberish->decrypt($this->input->post('recompute'), $toks) : $this->input->post('recompute');
        $tnt          = $toks ?  $this->gibberish->decrypt($this->input->post('tnt'), $toks) : $this->input->post("tnt");
        $employeeid   = $toks ?  $this->gibberish->decrypt($this->input->post('employeeid'), $toks) : $this->input->post("employeeid");
        $emplist      = array();

        if($tnt == "teaching") $tbl = "attendance_confirmed";
        else  $tbl = "attendance_confirmed_nt";
        $wC = "";
        if($employeeid) $wC .= " AND employeeid='$employeeid'";
        if($recompute == 'true') $this->db->query("DELETE FROM $tbl WHERE `cutoffstart` = '{$dfrom}' AND `cutoffend` = '{$dto}' $wC");
        ///< get list of unconfirmed employees first

        if($tnt == 'teaching'){
          $emplist = $this->attendance->emp_not_yet_confirmed($dfrom,$dto,$tnt,$employeeid);

        }elseif($tnt == 'nonteaching'){
          $emplist = $this->attendance->emp_not_yet_confirmed_nt($dfrom,$dto,$tnt,$employeeid);
        }

        $emplist = $this->attendance->constructEmpListHaveSched($emplist, $dto);
        ///< save attendance summary per employee
        $res = $deptid = $dateresigned = $hold_status = $isBED = '';
        $arr_data_failed = array();
        if(sizeof($emplist)>0 && $dfrom && $dto){
            foreach ($emplist as $key => $row) {
                list($dtr_start,$dtr_end,$payroll_start,$payroll_end,$payroll_quarter) = $this->payrolloptions->getDtrPayrollCutoffPair($dfrom,$dto);
                if($tnt){
                    $canConfirm = false;
                    $emp_data = $this->utils->getEmployeeInfo('teachingtype,deptid,dateresigned',array('employeeid'=>$employeeid));
                    if($emp_data){
                        $deptid       = $emp_data[0]->deptid;
                        $dateresigned = $emp_data[0]->dateresigned;
                        $canConfirm   = $this->attendance->empCanConfirmAttendance($payroll_start,$dateresigned);
                    }
                    if($canConfirm){
                        $bed_depts = Globals::getBEDDepartments();
                        if(in_array($deptid, $bed_depts)) $isBED = true;
                        if($tnt == 'teaching'){
                            $res = $this->attendance->saveEmployeeAttendanceSummaryTeaching($dfrom,$dto,$payroll_start,$payroll_end,$payroll_quarter,$row['qEmpId'], $isBED, $hold_status, $this->session->userdata("usertype"));
                        }elseif($tnt == 'nonteaching'){
                            $res = $this->attendance->saveEmployeeAttendanceSummaryNonTeaching($dfrom,$dto,$payroll_start,$payroll_end,$payroll_quarter,$row['qEmpId'], $hold_status, $this->session->userdata("usertype"));
                        }
                    }
                    if($res)   $success_count++;
                    else       array_push($arr_data_failed, $row['qEmpId']);

                }

                if($success_count) $return = array('err_code'=>0,'msg'=>'Employees successfully confirmed.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 
                else               $return = array('err_code'=>2,'msg'=>'Failed to confirm.','success_count'=>$success_count,'data_failed'=>$arr_data_failed); 

            }
        }
        else{
            $return = array('err_code'=>0,'msg'=>'No additional employee confirmed.','success_count'=>$success_count,'data_failed'=>array()); 
        }
        echo json_encode($return);
    }
    // show tardiness report
    function showTardinessReport(){
        $this->load->model("attendance");
        $data['datesetfrom'] = $this->input->post("datesetfrom");
        $data['datesetto'] = $this->input->post("datesetto");
        $data['fv'] = $this->input->post("fv");
        $data['deptid'] = $this->input->post("deptid");       
        $this->load->view('process/tardiness_report', $data);
    }
    // show tardiness report
    function showHalfdayReport(){
        $this->load->model("attendance");
        $data['datesetfrom'] = $this->input->post("datesetfrom");
        $data['datesetto'] = $this->input->post("datesetto");
        $data['fv'] = $this->input->post("fv");
        $data['deptid'] = $this->input->post("deptid");       
        $this->load->view('process/halfday_report', $data);
    }
    // show absence report
    function showAbsenceReport(){
        $this->load->model("attendance");
        $data['datesetfrom'] = $this->input->post("datesetfrom");
        $data['datesetto'] = $this->input->post("datesetto");
        $data['fv'] = $this->input->post("fv");
        $data['deptid'] = $this->input->post("deptid");       
        $this->load->view('process/absence_report', $data);
    }
    // show Tardiness For Payroll report
    function showTardinessForPayroll(){
        $this->load->model("attendance");
        $data['datesetfrom'] = $this->input->post("datesetfrom");
        $data['datesetto'] = $this->input->post("datesetto");
        $data['fv'] = $this->input->post("fv");
        $data['deptid'] = $this->input->post("deptid");     
        $data['edata'] = $this->input->post("edata");
        $data['dcut'] = $this->input->post("dcut");    
        $this->load->view('process/tardiness_for_payroll', $data);
    }
    function showallindividual(){
        $toks = $this->input->post("toks");
        $this->load->model("user");
        $this->load->model("attendance");
        $data['datesetfrom'] = $toks ? $this->gibberish->decrypt( $this->input->post("datesetfrom"), $toks ) : $this->input->post("datesetfrom");
        $data['datesetto'] = $toks ? $this->gibberish->decrypt( $this->input->post("datesetto"), $toks ) :  $this->input->post("datesetto");
        $data['fv'] = $toks ?  $this->gibberish->decrypt( $this->input->post("fv"), $toks ) :  $this->input->post("fv");
        $data['edata'] = $toks ? $this->gibberish->decrypt( $this->input->post("edata"), $toks ) :  $this->input->post("edata");
        // $data["datesetfrom"] = $data["datesetto"] = "2020-11-20"; die;
        $this->load->view('process/displayindividuallogs', $data);
    }
    function showindividualot(){
        $this->load->model("user");
        $this->load->model("attendance");
        $data['dset']   = $this->input->post("dset");
        $data['dsetto'] = $this->input->post("dsetto");
        $data['fv']     = $this->input->post("fv");
        //$data['deptid'] = $this->input->post("deptid");
        $this->load->view('process/displayotindividual', $data);
    }
    function addnewrowrequest(){
        $field = $this->input->post("field");
        $date = "<div class='input-group date requestdate' data-date='".date("Y-m-d")."' data-date-format='yyyy-mm-dd'>
                        <input class='align_center' size='16' name='requestdate' adjustid='' type='text' value=".date("Y-m-d").">
                        <span class='add-on'><i class='glyphicon glyphicon-calendar'></i></span>
                    </div>";
        $starttime = "<div class='input-group bootstrap-timepicker'>
                        <input name='fromtime' class='col-md-8 input-small align_center' type='text' value=''/>
                        <span class='add-on'><i class='glyphicon glyphicon-time'></i></span>
                    </div>"; 
        $endtime = "<div class='input-group bootstrap-timepicker'>
                        <input name='totime' class='col-md-8 input-small align_center' type='text' value=''/>
                        <span class='add-on'><i class='glyphicon glyphicon-time'></i></span>
                    </div>";        
        $type = "<div class='col-md-4 no-search'>
                        <select class='chosen' name='schedtype'>";
                          $opt_type = $this->extras->showadjustment_code(false);
                          foreach($opt_type as $c=>$val){
                           $type .= "<option value='{$c}'>{$val}</option>";    
                          }   
              $type .= "</select>
                    </div>";             
       /**
        $remarks = "<div class='field'>
                        <input type='text' name='remarks' class='col-md-12'/>
                    </div>";
                    */        
         $remarks = "<div class='col-md-4 no-search'>
                        <select class='chosen' name='remarks'>";
                          $opt_remarks = $this->extras->showrequestform();
                          foreach($opt_remarks as $c=>$val){
                           $remarks .= "<option value='{$c}'>{$val}</option>";    
                          }   
              $remarks .= "</select>
                    </div>";            
        $button = "<div class='btn-group'>
                        <a class='btn' href='#' tag='add_sched'><i class='glyphicon glyphicon-plus'></i></a>
                        <a class='btn' href='#' tag='delete_sched'><i class='glyphicon glyphicon-trash'></i></a>
                    </div>";
        switch($field){
            case "date":
              $return = $date;
            break;
            case "starttime":
              $return = $starttime;
            break;
            case "endtime":
              $return = $endtime;
            break;
            case "type":
              $return = $type;
            break;
            case "remarks":
              $return = $remarks;
            break;
            case "button":
              $return = $button;
            break;
            default:
            $return =  "<tr tag='rowrequest'>
                        <td class='datedisplay'>
                            {$date}
                        </td>
                        <td class='starttimedisplay'>
                            {$starttime}
                        </td>
                        <td class='endtimedisplay'>
                            {$endtime}
                        </td>
                        <td class='typedisplay'>
                            {$type}
                        </td>
                        <td class='remarksdisplay'>
                           {$remarks}
                        </td>
                        <td class='buttondisplay'>
                            {$button}
                        </td>
                      </tr>";
            break;
        }
        echo $return;
    }
    function addnewrowrequest_dtr(){
        $field = $this->input->post("field");
        $cutoffid = $this->input->post("cutoffid");
        $employeeid = $this->input->post("employeeid");
           
        $timein = "<div class='input-group bootstrap-timepicker'>
                        <input name='timein' class='col-md-8 input-small align_center' type='text' value=''/>
                        <span class='add-on'><i class='glyphicon glyphicon-time'></i></span>
                    </div>"; 
        $timeout = "<div class='input-group bootstrap-timepicker'>
                        <input name='timeout' class='col-md-8 input-small align_center' type='text' value=''/>
                        <span class='add-on'><i class='glyphicon glyphicon-time'></i></span>
                    </div>";        
        $datelog = "<div class='col-md-4 no-search'>
                        <select class='chosen' name='datelog' timeid=''>";
                          $opt_type = $this->extras->showcutofdatebyid($cutoffid,$employeeid);
                          foreach($opt_type as $t=>$val){
                            $datelog .= "<option value='{$t}'>{$val}</option>";    
                          }  
              $datelog .= "</select>
                    </div>";        
        $button = "<div class='btn-group'>
                        <a class='btn' href='#' tag='add_sched'><i class='glyphicon glyphicon-plus'></i></a>
                        <a class='btn' href='#' tag='delete_sched' timeid=''><i class='glyphicon glyphicon-trash'></i></a>
                    </div>";
        switch($field){
            case "timein":
              $return = $timein;
            break;
            case "timeout":
              $return = $timeout;
            break;
            case "datelog":
              $return = $datelog;
            break;
            case "button":
              $return = $button;
            break;
            default:
            $return =  "<tr tag='rowrequest'>
                        <td class='datelogdisplay'>
                            {$datelog}
                        </td>
                        <td class='timeindisplay'>
                            {$timein}
                        </td>
                        <td class='timeoutdisplay'>
                            {$timeout}
                        </td>
                        <td class='buttondisplay'>
                            {$button}
                        </td>
                      </tr>";
            break;
        }
        echo $return;
    }
    function callemployee(){
       $toks = $this->input->post("toks");
       $data['isactive'] = $toks ? $this->gibberish->decrypt( $this->input->post("isactive"), $toks ) : $this->input->post("isactive");
       $data['officeid'] = $toks ? $this->gibberish->decrypt( $this->input->post("officeid"), $toks ) : $this->input->post("officeid");
       $data['deptid'] = $toks ?  $this->gibberish->decrypt( $this->input->post("deptid"), $toks ) :  $this->input->post("deptid");
       $data['estatus'] = $toks ?  $this->gibberish->decrypt( $this->input->post("estatus"), $toks ) :  $this->input->post("estatus");
       $data['etype'] =  $toks ?  $this->gibberish->decrypt( $this->input->post("etype"), $toks ) : $this->input->post("etype");
       $data['campusid'] = $toks ?  $this->gibberish->decrypt( $this->input->post("campusid"), $toks ) :  $this->input->post("campusid");
       echo $this->employee->showempnores($data['deptid'],$data['estatus'],$data['etype'],$data['campusid'],$data['officeid'], $data["isactive"]);
    }
    function view_leave_status(){
        $toks = $this->input->post("toks");
        $data['category'] = $this->input->post("category");
        $data['ltype'] = $this->input->post("ltype");
        $data['othtype'] = $this->input->post("othtype");
        $data['dfrom'] = $this->input->post("dfrom");
        $data['dto'] = $this->input->post("dto");
        $data['deptid'] = $this->input->post("deptid");
        $data['noDA'] = $this->input->post("noDA");
        foreach($data as $key => $val){
          $data[$key] = $this->gibberish->decrypt($val, $toks);
        }
        # for ica-hyperion 21194
        # by justin (with e)
        $this->load->model("leave_application");
        # end of for ica-hyperion 21194
        $this->load->view('process/view_leave_status',$data);
    }
    function view_sc_status(){
       
        $data['status'] = $this->input->post("status");
        $data['dfrom'] = $this->input->post("dfrom");
        $data['dto'] = $this->input->post("dto");
        $this->load->view('process/schistorymanagement',$data);
    }

     function view_scuse_status(){
       
        $data['status'] = $this->input->post("status");
        $data['dfrom'] = $this->input->post("dfrom");
        $data['dto'] = $this->input->post("dto");
        $this->load->view('process/scusehistorymanagement',$data);
    }
    function view_ob_status(){
        $toks = $this->input->post("toks");
        $data['category'] = $this->gibberish->decrypt($this->input->post("category"), $toks);
        $data['dfrom'] = $this->gibberish->decrypt($this->input->post("dfrom"), $toks);
        $data['dto'] = $this->gibberish->decrypt($this->input->post("dto"), $toks);
        $data['deptid'] = $this->gibberish->decrypt($this->input->post("deptid"), $toks);
        $data['otherType'] = ($this->input->post("othtype") != "") ? $this->gibberish->decrypt($this->input->post("othtype"), $toks) : "";
        $this->load->view('process/view_ob_status',$data);
    }
    function view_seminar_status(){
        $data['category'] = $this->input->post("category");
        $data['dfrom'] = $this->input->post("dfrom");
        $data['dto'] = $this->input->post("dto");
        $data['deptid'] = $this->input->post("deptid");
        $this->load->view('process/view_seminar_status',$data);
    }
    function view_overtime_status(){
        $toks = $this->input->post("toks");
        $data['category'] = $this->gibberish->decrypt($this->input->post("category"), $toks);
        $data['dfrom'] = $this->gibberish->decrypt($this->input->post("dfrom"), $toks);
        $data['dto'] = $this->gibberish->decrypt($this->input->post("dto"), $toks);
        $data['deptid'] = $this->gibberish->decrypt($this->input->post("deptid"), $toks);
        $this->load->view('process/view_overtime_status',$data);
    }
    function loadltype(){
        $data['eid'] = $this->input->post("eid");
        echo $this->extras->showLeaveType("",$data['eid']);
    }
    function leave_status(){
       $this->load->model('utils');
       $data['code'] = $this->input->post("code");
       $data['idnum'] = $this->input->post("idnum");
       $data['category'] = $this->input->post("category");
       $data['job'] = $this->input->post("job");
       $emplist = $this->utils->getEmplist();

       unset($emplist['']);
       $data['emplist'] = $emplist;
       $this->load->view('process/leave_management_details', $data); 
    }
    function ob_status(){
       $this->load->model('utils');
       $data['code'] = $this->input->post("code");
       $data['idnum'] = $this->input->post("idnum");
       $data['category'] = $this->input->post("category");
       $data['job'] = $this->input->post("job");
       $emplist = $this->utils->getEmplist();

       unset($emplist['']);
       $data['emplist'] = $emplist;
       $this->load->view('process/ob_management_details', $data); 
    }
    function overtime_status(){
      $this->load->model('employee');
      $this->load->model('utils');
      
      $data['deptid']   = $this->employee->getempdatacol('deptid');
      $data['emplist']  = $this->utils->getEmplist($data['deptid']);
      unset($data['emplist']['']);
      $data['deptlist']   = $this->utils->getOffice();
      unset($data['deptlist']['']);
      $this->load->view("process/overtime_management_details",$data);
    }
    function cs_status(){
      $this->load->model('employee');
      $this->load->model('utils');
      $this->load->model('schedule');
      
      $data['deptid']   = $this->employee->getempdatacol('deptid');
      $data['emplist']  = $this->utils->getEmplist($data['deptid'],'','','teaching');
      unset($data['emplist']['']);
      $data['scheddays']  = $this->schedule->getSchedDays();
      $data['official_schedlist'] = $this->schedule->getOfficialSchedList();
      # ica-hyperion 21194
      # by justin (with e)
      # comment ko muna ito.. bali ipapasok ko sya sa cs_apply.php para sync yung process nya..
      # $this->load->view("process/cs_management_details",$data); 
      # ito na yung bagong displayed para sa ticket na ito..
      $this->load->view("change_schedule/cs_apply",$data);
    }
    function seminar_status(){
       $this->load->model('utils');
       $data['job'] = $this->input->post("job");
       $emplist = $this->utils->getEmplist();

       unset($emplist['']);
       $data['emplist'] = $emplist;
       $this->load->view('process/seminar_management_details', $data); 
    }
    
    function saveSeminarHRDirect(){
      $this->load->model('utils');

      $data   = $this->input->post("form_data");
      $post_data = array();
      // var_dump(json_decode($data));
      foreach (json_decode($data) as $key) {
        $post_data[$key->name] = $key->value;
      }

      $emplist  = isset($post_data['eid'])     ? $post_data['eid']    : "";

      $arr_emplist = array();
      if($emplist){
        $arr_emplist = explode(",", $emplist);
        if(sizeof($arr_emplist) > 0){
          $hrhead = $this->utils->getDeptHead('head','HR');
          if($hrhead){

            $post_data['arr_emplist'] = $arr_emplist;
            $post_data['hrhead']      = $hrhead;
            $save_ret =  $this->employee->saveSeminarHRDirect($post_data); ///< Save application details
            if($save_ret['err_code'] == 1){

                $ret = "({$save_ret['count']}) employee/s successfully applied.";

                if(isset($_FILES['filess'])){
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
                          $this->db->query("INSERT into seminar_app_attach_invitation (base_id,file_name,content,file_type) values ('{$save_ret['base_id']}','$fileName','$content','$fileType')"); ///< upload invitation
                        }
                  }
                }
                
            }

          }else $ret = 'No setup for HR Head.';
        }else $ret = 'No employee selected.';
      }else $ret = 'No employee selected.';

      echo $ret;

    }
    
    function absent_to_leave(){
       echo $this->employee->absenttoleave($this->input->post()); 
    }
    
    function save_new_leave(){
        $data = $this->input->post();
        echo $this->employee->new_leave($data);
    }
    
    function save_leave_status(){
         $idnum = $this->input->post("idnum");
         $code = $this->input->post("code");
         $leavetype = $this->input->post("leavetype");
         $dateapplied = $this->input->post("dateapplied");
         $no_days = $this->input->post("no_days");
         $fromdate = $this->input->post("fromdate");
         $todate = $this->input->post("todate");
         $status = $this->input->post("status");
         $dateapproved = $this->input->post("dateapproved");
         $approvedby = $this->input->post("approvedby");
         $remarks = $this->input->post("remarks");
         $job = $this->input->post("job");
         $remarks = htmlspecialchars($remarks,ENT_QUOTES);
         
         if($job == "add"){
            $this->db->query("INSERT INTO leave_request (employeeid,leavetype,dateapplied,no_days,fromdate,todate,status,dateapproved,approvedby,remarks) VALUES ('$code','$leavetype','$dateapplied','$no_days','$fromdate','$todate','$status','$dateapproved','$approvedby','$remarks') ");
         }else{
            $this->db->query("UPDATE leave_request SET id='$idnum',employeeid='$code',leavetype='$leavetype',dateapplied='$dateapplied',no_days='$no_days',fromdate='$fromdate',todate='$todate',status='$status',dateapproved='$dateapproved',remarks='".$remarks."',approvedby='$approvedby' WHERE id='$idnum'");
         }
    }

    function loadTimesForAdjustment(){
      $date = $this->input->post("logdate");
      $uid = $this->input->post("userid");
      $query = "SELECT IFNULL(SUBSTRING(GET_CORRECT_LOG_AND_FREQ('{$uid}','{$date}','IN'),12,8),NULL) AS login, IFNULL(SUBSTRING(GET_CORRECT_LOG_AND_FREQ('{$uid}','{$date}','OUT'),12,8),NULL) AS logout";
      $res = $this->db->query($query)->result();
      $response = array();
      foreach ($res as $key => $value) {
        $timein = ($value->login != "") ? date("h:i A",strtotime($value->login)) : "";
        $timeout = ($value->logout != "") ? date("h:i A",strtotime($value->logout)) : "";
        $response["timein"] = $timein;
        $response["timeout"] = $timeout;
      }
      print(json_encode($response));
    }
    
    /* ADDED BY JUSTIN - 12/09/2014 */
    function saveschedbyemp(){
        $employeeid = $this->input->post("employeeid");             // employee id
        #$selday = $this->input->post("selday");
        $dfrom = $this->input->post("dfrom");
        $dto = $this->input->post("dto");
        $fromtime = $this->input->post("fromtime");
        $totime = $this->input->post("totime");
        $tardy = $this->input->post("tardy");
        $fabsent = $this->input->post("fabsent");
        $habsent = $this->input->post("habsent");
        #$halfsched = $this->input->post("halfsched");
        $earlyd = $this->input->post("earlyd");
        $user = $this->session->userdata("userid");                 // changed by user
        $uname = $this->session->userdata("username");
        $dfrom = date('Y-m-d',strtotime($dfrom));
        
        $datetime = date('Y-m-d h-i-s');                            // current day & time
        $time = date('H:i:s');                                      // current time
        
        /* CONVERT TIME TO 24 Hours Format */
        $fromtime = date('H:i:s',strtotime($fromtime));
        $totime = date('H:i:s',strtotime($totime));
        $tardy = date('H:i:s',strtotime($tardy));
        $fabsent = date('H:i:s',strtotime($fabsent));
        $habsent = date('H:i:s',strtotime($habsent));
        $earlyd = date('H:i:s',strtotime($earlyd));
        #echo $employeeid." - ".$dfrom." - ".$dto." - ".$fromtime." - ".$totime." - ".$tardy." - ".$fabsent." - ".$habsent." - ".$uname." - ".date('Y-m-d h:i:s');die;
        /* VARIABLES */
        $x = 0;
        $idx = 0;
        #$temp_text = "";
        
        /* date difference */
        $diff_dfrom = date('d',strtotime($dfrom));
        if($dfrom == $dto)
        $neg = "";
        else
        $neg = "- $diff_dfrom day";
        $difftotal = date('d',strtotime($dto.$neg));
        if($dfrom == $dto)
        $difftotal = 1;
        else
        $difftotal += 1;
        
        if($difftotal > 7){
            echo "The maximum date interval is 7 days. \n Please set the date interval up to 7 days only.. ";
        }else{
                                /* SPECIFIC EMPLOYEE */
            
            /* START LOOPING FOR THE TEMPORARY CHANGE OF SCHEDULE OF EMPLOYEES */
            
            for($x = 0;$x<$difftotal;$x++){
                
                // GETTING THE PRESENT DATE AND TIME 
            $ins_date = date('Y-m-d',strtotime($dfrom."+$x day"));
            $ins_date = date('Y-m-d',strtotime($ins_date."-1 day"));
            $ins_dateactive = $ins_date." ".$time;                      // DATE ACTIVE
            
                // GETTING NAME OF THE DAY 
            $ins_get_day = date('D',strtotime(date('Y-m-d',strtotime($dfrom."+$x day"))));
            
            if($ins_get_day == "Sun" || $ins_get_day == "Thu")
            $ins_get_day = strtoupper($ins_get_day);
            if($ins_get_day != "SUN" ){
                if($ins_get_day == "THU")
                    $ins_get_day = substr($ins_get_day,0,2);
                else
                    $ins_get_day = substr($ins_get_day,0,1);            // DAY OF WEEK
            }
            
                // GET IDX 
            switch($ins_get_day){
                case "M" : $idx = "1";break;
                case "T" : $idx = "2";break;
                case "W" : $idx = "3";break;
                case "TH" : $idx = "4";break;
                case "F" : $idx = "5";break;
                case "S" : $idx = "6";break;
                default:
                    $idx = 0;                                       // IDX
            }
            
                                /* AUTOMATIC INSERTING OF CURRENT DATE AND TIME  */
            
            #$sql = "SELECT * FROM employee_schedule_history WHERE employeeid='$employeeid' AND dayofweek='$ins_get_day' LIMIT 1";
            $sql = "SELECT * FROM employee_schedule_history WHERE employeeid='$employeeid' AND dayofweek='$ins_get_day'";
            $rs = $this->db->query($sql)->result();
            foreach($rs as $row){
             $empid = $row->employeeid;
             $starttime = $row->starttime;
             $endtime = $row->endtime;
             $dayofweek = $row->dayofweek;
             $tardy_start = $row->tardy_start;
             $absent_start = $row->absent_start;
             $absent_half_start = $row->absent_half_start;
             $half_schedule = $row->half_schedule;
             $early_dismissal = $row->early_dismissal;
            }
            
            /* date addition */
            $auto_date = date('Y-m-d',strtotime($ins_date."+1 day"));
            $auto_date_time = $auto_date." ".$time;                      // DATE ACTIVE
            
           #$temp_text .= $empid." / ".$starttime." / ".$endtime." / ".$dayofweek." / ".$idx." / ".$tardy_start." / ".$absent_start." / ".$absent_half_start." / ".$user." / ".$auto_date_time;
            $query1 = $this->db->query("INSERT INTO employee_schedule_history 
                                        (employeeid,starttime,endtime,dayofweek,idx,tardy_start,absent_start,absent_half_start,early_dismissal,changeby,dateactive)  
                                        VALUES  
                                        ('$employeeid','$fromtime','$totime','$ins_get_day','$idx','$tardy','$fabsent','$habsent','$earlyd','$user','$ins_dateactive');
                                        ");    
            $query2 = $this->db->query("INSERT INTO employee_schedule_history
                                        (employeeid,starttime,endtime,dayofweek,idx,tardy_start,absent_start,absent_half_start,early_dismissal,changeby,dateactive) 
                                        VALUES 
                                        ('$empid','$starttime','$endtime','$dayofweek','$idx','$tardy_start','$absent_start','$absent_half_start','$early_dismissal','$user','$auto_date_time');
                                        ");
            #$temp_text .= $employeeid." / ".$fromtime." / ".$totime." / ".$ins_get_day." / ".$idx." / ".$tardy." / ".$fabsent." / ".$habsent." / ".$user." / ".$ins_dateactive. " / ".$auto_date_time;
            }
         #  echo $temp_text;
            if($query1 && $query2){
                $this->db->query("INSERT INTO employee_official_schedule_history (employeeid,datefrom,dateto,start_time,end_time,tardy,absent,halfday_absent,early_dismissal,user,timestamp) VALUES ('$employeeid','$dfrom','$dto','$fromtime','$totime','$tardy','$fabsent','$habsent','$earlyd','$uname','".date('Y-m-d h:i:s')."')");
                echo "Succesfully Saved!.";
            }else
                echo "Failed to Saved.. Please Check your Connection";
                                /* END OF SPECIFIC EMPLOYEE */       
        }
    }    // END OF FUNCTION 
    
    function saveschedbyshift(){
        $emptype = $this->input->post("emptype");
        $dfrom = $this->input->post("dfrom");
        $dto = $this->input->post("dto");
        $fromtime = $this->input->post("fromtime");
        $totime = $this->input->post("totime");
        $tardy = $this->input->post("tardy");
        $fabsent = $this->input->post("fabsent");
        $habsent = $this->input->post("habsent");
        #$halfsched = $this->input->post("halfsched");
        $earlyd = $this->input->post("earlyd");
        $user = $this->session->userdata("userid");                 // changed by user
        $uname = $this->session->userdata("username");
        
        $dfrom = date('Y-m-d',strtotime($dfrom));
        
        $datetime = date('Y-m-d h-i-s');                            // current day & time
        $time = date('H:i:s');                                      // current time
        
        /* CONVERT TIME TO 24 Hours Format */
        $fromtime = date('H:i:s',strtotime($fromtime));
        $totime = date('H:i:s',strtotime($totime));
        $tardy = date('H:i:s',strtotime($tardy));
        $fabsent = date('H:i:s',strtotime($fabsent));
        $habsent = date('H:i:s',strtotime($habsent));
        $earlyd = date('H:i:s',strtotime($earlyd));
        
        /* VARIABLES */
        $x = 0;
        $idx = 0;
        #$temp_text = "";
        
        /* date difference */
        $diff_dfrom = date('d',strtotime($dfrom));
        if($dfrom == $dto)
        $neg = "";
        else
        $neg = "- $diff_dfrom day";
        $difftotal = date('d',strtotime($dto.$neg));
        if($dfrom == $dto)
        $difftotal = 1;
        else
        $difftotal += 1;
        
        if($difftotal > 7){
            echo "The maximum date interval is 7 days. \n Please set the date interval up to 7 days only.. ";
        }else{
            $sql = "SELECT employeeid FROM employee where emptype='$emptype'";
            $qcount = $this->db->query($sql)->num_rows();
            if($qcount > 0){
            $query = $this->db->query($sql)->result();
            foreach($query as $row){
                $empid = $row->employeeid;
                
                for($x = 0;$x<$difftotal;$x++){
                    // GETTING THE PRESENT DATE AND TIME 
                $ins_date = date('Y-m-d',strtotime($dfrom."+$x day"));
                $ins_date = date('Y-m-d',strtotime($ins_date."-1 day"));
                $ins_dateactive = $ins_date." ".$time;                      // DATE ACTIVE
                
                    // GETTING NAME OF THE DAY 
                $ins_get_day = date('D',strtotime(date('Y-m-d',strtotime($dfrom."+$x day"))));
                if($ins_get_day == "Sun" || $ins_get_day == "Thu")
                $ins_get_day = strtoupper($ins_get_day);
                if($ins_get_day != "SUN" ){
                    if($ins_get_day == "THU")
                        $ins_get_day = substr($ins_get_day,0,2);
                    else
                        $ins_get_day = substr($ins_get_day,0,1);            // DAY OF WEEK
                }
                
                    // GET IDX 
                switch($ins_get_day){
                    case "M" : $idx = "1";break;
                    case "T" : $idx = "2";break;
                    case "W" : $idx = "3";break;
                    case "TH" : $idx = "4";break;
                    case "F" : $idx = "5";break;
                    case "S" : $idx = "6";break;
                    default:
                        $idx = 0;                                       // IDX
                }
                
                                    /* AUTOMATIC INSERTING OF CURRENT DATE AND TIME */
                /* date addition */
                $auto_date = date('Y-m-d',strtotime($ins_date."+1 day"));
                $auto_date_time = $auto_date." ".$time;                      // DATE ACTIVE 
                
                $sqld = "SELECT * FROM employee_schedule_history WHERE employeeid='$empid' AND dayofweek='$ins_get_day' ";
                $rs = $this->db->query($sqld)->result();
                foreach($rs as $row){
                 $employedid = $row->employeeid;
                 $starttime = $row->starttime;
                 $endtime = $row->endtime;
                 $dayofweek = $row->dayofweek;
                 $tardy_start = $row->tardy_start;
                 $absent_start = $row->absent_start;
                 $absent_half_start = $row->absent_half_start;
                 $half_schedule = $row->half_schedule;
                 $early_dismissal = $row->early_dismissal;
                }
                
                #echo $empid." - ".$fromtime." - ".$totime." - ".$ins_get_day." - ".$idx." - ".$tardy." - ".$fabsent." - ".$habsent." - ".$user." - ".$ins_dateactive."\n";
                $query1 = $this->db->query("INSERT INTO employee_schedule_history 
                                            (employeeid,starttime,endtime,dayofweek,idx,tardy_start,absent_start,absent_half_start,early_dismissal,changeby,dateactive)  
                                            VALUES  
                                            ('$empid','$fromtime','$totime','$ins_get_day','$idx','$tardy','$fabsent','$habsent','$earlyd','$user','$ins_dateactive');
                                            ");    
                $query2 = $this->db->query("INSERT INTO employee_schedule_history
                                            (employeeid,starttime,endtime,dayofweek,idx,tardy_start,absent_start,absent_half_start,early_dismissal,changeby,dateactive) 
                                            VALUES 
                                            ('$employedid','$starttime','$endtime','$dayofweek','$idx','$tardy_start','$absent_start','$absent_half_start','$early_dismissal','$user','$auto_date_time');
                                            ");
                }   // END OF FOR LOOP
            $this->db->query("INSERT INTO employee_official_schedule_history (employeeid,datefrom,dateto,start_time,end_time,tardy,absent,halfday_absent,early_dismissal,user,timestamp) VALUES ('$empid','$dfrom','$dto','$fromtime','$totime','$tardy','$fabsent','$habsent','$earlyd','$uname','".date('Y-m-d h:i:s')."')");
            }   // END OF EMPLOYEE LOOP    justin dito
                if($query1 && $query2){
                    echo "Successfully Saved!.";
                }else
                    echo "Failed to Save.. Please Check your Connection";
        }else{
            echo "There are no employees in this type of shift.. Please put some employees before saving..";
        }
        }
    }   // END OF FUNCTION
    
    function removeID(){
        $ltype = $this->input->post('ltype');
        echo $this->extras->removeID($ltype);
    }
    
    function showtimedtr(){
        $tdate  = $this->input->post('tdate');
        $eid    = $this->input->post('uid');
        list($timein,$timeout) = $this->extras->showtimedtr($eid,$tdate);
        $return =   "
                        <user>
                            <timein>$timein</timein>
                            <timeout>$timeout</timeout>
                        </user>
                    ";
        echo $return;
    }
    
    function earlydismissal(){
        $data = $this->input->post();
        $type = $this->input->post("type");
        if($type)
            $this->load->view('process/earlydismissal');
        else
            echo $this->employee->earlydismissal($data);          
    }
      function earlydismissals()
    {
        $data = $this->input->post();
        //$id = $this->input->post('id');
        $folder = $this->input->post('folder');
        $view = $this->input->post('view');
        $this->load->view("$folder/$view",$data);
    }
    function earlydismissalsActions()
    {
      $this->load->model('schedule');
      $msg = "";
      $id = $this->input->post('id');
      $job = $this->input->post('job');
      $rangefrom = $this->input->post('from');
      $rangeto = $this->input->post('to');
      $tardy = $this->input->post('tardy_e');
      $absent = $this->input->post('absent_e');
      $early = $this->input->post('early_d');
      $year = $this->input->post('year');
      $sequences = $this->input->post('sequences');
      
      if ($job == "delete") {
      $msg = $this->schedule->earlydismissal($id,$rangefrom,$rangeto,$tardy,$absent,$early,$job,$year);
      }
      else
      {
       $msg = $this->schedule->earlydismissal($id,$rangefrom,$rangeto,$tardy,$absent,$early,$job,$year,$sequences); 
      }

      echo json_encode($msg);

      // echo $job;
      

    }

	 //Added 3-19-2018 LACKING OF IN/OUT for DEPARTMENT ATTENDANCE
  function showLackInOutForDepartment(){
      $this->load->view('process/lackingInOutDepartment');
    }
	//Added 6-3-2017 LACKING OF IN/OUT
	function showLackInOut(){
        $this->load->view('process/lackingInOut');
    }
	
	//Added 8-10-2017 LONGEVITY
	function showLongevityTable(){
		$data = $this->input->post();
    $this->load->view('process/longevityTable',$data);
    }
	
	function saveLongevity(){
        $emplist = $this->input->post("empList");
        $cutoff = $this->input->post("cutoff");
		$query = $this->employee->saveLongevity($emplist,$cutoff);
		if($query == "Success") echo "Success";
		else echo $query;
			
    }
  function saveLongevityIncome(){
    $data = $this->input->post();
    echo $this->employee->saveLongevityIncome($data);    
  }
  //glenmark
  //saving for new remark setup
  function addnewremarks(){
     $this->load->view('process/addremarks');     
  }
  function saveRemarks()
  {
    $data = $this->input->post();
    echo $this->employee->saveRemarks($data);
  }
  //glenmark
  //saving for otherIncome in Process Other Income
  function saveOtherIncomeData(){
    $data = $this->input->post();
    $result = $this->employee->saveOtherIncomeData($data);    

    # for ica-hyperion 21294
    # by justin (with e)
    # > additional ko lang sa pag save.. para malaman kung sino yung mga na process na employee at hindi pa..
    if($result["SUCCESS"] > 0) echo $result["SUCCESS"]." Employee(s) was Successfully Saved! \n";
    if($result["ERROR_TOTAL"] > 0){
      echo $result["ERROR_TOTAL"]." Employee(s) was Failed to Saved! \n";
      foreach ($result["ERROR"] as $key => $error) {
        echo "* ". $key ." - ". $error ."\n";
      }
    } 
    # end for ica-hyperion 21294
  }

  # for ica-hyperion 21294
  # by justin (with e)
  # > para sa print output ng other income..
  function printOtherIncome(){
    $data = $this->input->get();
    $this->load->view("process/reports_pdf/other_income",$data);
  }
  # end for ica-hyperion 21294

	function showOtherIncomeEmpList()
	{
		$data = $this->input->post();
  	$this->load->view('process/otherIncomeEmpList',$data);
	}
	
	function viewOtherIncomeTable()
	{
		// $otherIncome = $this->input->post("otherIncome");
		// $this->load->view('process/viewOtherIncomeEmpList',$otherIncome);
    $data = $this->input->post();
    $this->load->view('process/viewOtherIncomeEmpList',$data);
	}

  function viewotherIncomeHistory()
  {
    $otherIncome = $this->input->post("otherIncome");
    $this->load->view('process/viewOtherIncomeEmpListHistory',$otherIncome); 
  }

  function editOtherIncome()
  {
    $info = array();
    $data = $this->input->post();
    $otherIncome = $this->input->post("otherIncome");
    $information = $this->employee->getOtherIncomedata($data);
   
    foreach ($information as $empdata) {
       // echo '<pre>';print_r($empdata);
      $info['employeeid'] = $empdata->employeeid;
      $info['other_income'] = $empdata->other_income;
      $info['monthly'] = $empdata->monthly;
      $info['daily'] = $empdata->daily;
      $info['hourly'] = $empdata->hourly;
      $info['dateEffective'] = $empdata->dateEffective?$empdata->dateEffective:"";
      $info['dateEnd'] = $empdata->dateEnd?$empdata->dateEnd:"";
      $info['otherIncome'] = $otherIncome;
    }

   
    $this->load->view('process/editotherIncome',$info);
  }

  function clearotherIncomedata()
  {
    $data = $this->input->post();
    $datas = $this->input->post("datas");
    $return = $this->employee->clearotherIncomedata($data,$datas);
    echo json_encode($return);
  }

  function saveEditedOtherIncome()
  {
    $data = $this->input->post();
    $return = $this->employee->saveEditedOtherIncome($data);
    echo json_encode($return);
  }
  function saveOtherIncome()
	{
		$datas = $this->input->post("datas");
		$othIncome = $this->input->post("othIncome");
		
		$query = $this->employee->saveOtherIncome($datas,$othIncome);
		echo json_encode($query);
	}
	
	function deleteOtherIncome()
	{
		$employeeid = $this->input->post("employeeid");
		$othIncome = $this->input->post("othIncome");
		$query = $this->employee->deleteOtherIncome($employeeid,$othIncome);
    echo $query;
	}
	
  ///< @Angelica revised for other income computation
	function showOtherTable(){
      $codeIncome = $this->input->post('othIncome');
      $campus = $this->input->post('campus');
  		$cutoff = $this->input->post('cutoff');

      $dates = explode(',',$cutoff);
      if(isset($dates[0]) && isset($dates[1])){
        $sdate = $dates[0];
        $edate = $dates[1];
      }else{
        echo 'Invalid Cutoff';
        return;
      }

      $arr_info = $emplist = array();

      $this->load->model('income');
      $oth_q = $this->income->getEmployeeOtherIncomeComputed($sdate,$edate,$campus,$codeIncome);

      if($oth_q->num_rows() > 0){
        foreach ($oth_q->result() as $key => $row) {
          $emplist[$row->employeeid] = array( 
                                            'fullname' => '',
                                            'deduc_hours' => $row->hours_deduc,
                                            'monthly_pay' => $row->monthly,
                                            'total_pay' => $row->amount_total
                                        );
        }
    }

    list($dtr_start,$dtr_end,$p_cutoff_from,$p_cutoff_to) = $this->payrolloptions->getDtrPayrollCutoffPair($sdate,$edate,'','');

    $arr_info['empList'] = $emplist;
    $arr_info['codeIncome'] = $codeIncome;
    $arr_info['campus'] = $campus;
    $arr_info['cutoff'] = $cutoff;
    $arr_info['p_cutoff_from'] = $p_cutoff_from;
    $arr_info['p_cutoff_to'] = $p_cutoff_to;


    $this->load->view('process/otherTable',$arr_info);
  }
	
	function showOverloadTable(){
		$data = $this->input->post();
        $this->load->view('process/overloadTable',$data);
    }
        /* END OF ADDED FUNCTIONS */
        
  // For manage dtr
  // Author : Justin (with e)
  function findTimeRecord(){
      $toks = $this->input->post("toks");
      $eid   = $this->gibberish->decrypt( $this->input->post("eid"), $toks );
      $cdate = $this->gibberish->decrypt( $this->input->post("cdate"), $toks );
      $result = $this->extras->findTimeRecordModel($eid, $cdate);
      // echo "<pre>"; print_r($result); die;
      if(count($result) > 0){
          foreach ($result as $key) {
            $type = $key->type=="EDIT"?"UPDATED":"";
            echo "<tr id=\"row-".$key->timeid."\">";
            echo "<td class=\"input-small align_center\" hidden>".$key->timeid."</td>";
            echo "<td class=\"input-small align_center\" id=\"timein-".$key->timeid."\">".strtoupper(date("h:i a",strtotime($key->timein)))."</td>";
            echo "<td class=\"input-small align_center\" id=\"timeout-".$key->timeid."\">".($key->timeout && $key->timeout != '0000-00-00 00:00:00' ? strtoupper(date("h:i a",strtotime($key->timeout))) : '--:-- --')."</td>";
            // echo "<td class=\"input-small align_center\" id=\"timeout-".$key->type."\">".$type."</td>";

            // echo "<td class=\"input-small align_center\" id=\"timeout-".$key->type."\">".$key->type=="EDIT"?"UPDATED":""."</td>";
            echo "<td class=\"input-small align_center\"><a class=\"btn btn-info\" id=\"".$key->timeid."\" onclick=\"clickEdit(this.id)\"><i class=\"icon glyphicon glyphicon-edit\"></i></a><a class=\"btn btn-danger\" id=\"".$key->timeid."\" onclick=\"clickRemove(this.id)\"><i class=\"icon glyphicon glyphicon-remove-sign\"></i></a></td>";
            echo "</tr>";
          }
      }else{
        echo "No result found!.";
      }
  }
  //glen mark
  #batch adjustment for employee in DTR
  function batchApprovalDTR()
  {
    $data = $this->input->post('data');
    $toks = $this->input->post('toks');
    echo $this->extras->batchApprovalDTR($data, $toks);

  }
  
  function saveManageDTR(){
      $data = $this->input->post();
      $toks = $this->input->post('toks');
      $idx = date("w",strtotime($this->gibberish->decrypt( $data['time_record'], $toks)));
      $editedby = $this->session->userdata("username");
      // save to employee_schedule_adjustment
      $base_id = $this->extras->saveManageDTRModel($data, $idx, $editedby, $toks);
      // save existing timesheet here
      $result = $this->extras->findTimeRecordModel($this->gibberish->decrypt( $data['eid'], $toks ),$this->gibberish->decrypt( $data['cdate'], $toks ));
      // echo "<pre>";print_r($result);die;
      // 
      $userid = '';
      // save timesheet and employee_schedule_adjustment_ext
      foreach (explode("|", $data['time_record']) as $tr){
            $newData = "";
            $key = explode("~u~", $tr);

            $tID = $this->gibberish->decrypt( $key[0], $toks ); // table ID
            $key[1] = $this->gibberish->decrypt( $key[1], $toks );
            $key[2] = $this->gibberish->decrypt( $key[2], $toks );
            $timein = date("Y-m-d H:i:s", strtotime($key[1])); // time in
            $timeout = date("Y-m-d H:i:s", strtotime($key[2])); // time out
            $newData = date("Y-m-d h:i A", strtotime($key[1]))." - ".date("Y-m-d h:i A", strtotime($key[2]));

            $final_time = $key[1] ." - ".  $key[2];
            $this->extras->saveManageDTRAndTimesheet($this->gibberish->decrypt( $data['eid'], $toks ),$base_id, $tID,$final_time,$timein,$timeout);

            if(count($result) > 0){
              foreach ($result as $key){
                $time  = "";
                $tIDs = $key->timeid;
                $userid = $key->userid;

                $this->db->query("DELETE FROM timesheet WHERE timeid={$tIDs} AND userid = '$userid' "); // delete the old timesheet
                $actual_time = date("h:i a",strtotime($key->timein)) ." - ".date("h:i a",strtotime($key->timeout));
                $time = date("Y-m-d h:i A",strtotime($key->timein))." - ".date("Y-m-d h:i A",strtotime($key->timeout));
                if ($time != $newData) {
                  //updating time..
                 $update =  $this->db->query("UPDATE employee_schedule_adjustment_ext SET actual_time='$actual_time' WHERE tID ='$tIDs' AND final_time='$newData' AND ISNULL(actual_time)");
                     if($update)
                     {
                         $this->db->query("UPDATE employee_schedule_adjustment_ext SET actual_time='$actual_time' WHERE final_time='$time' AND ISNULL(actual_time)");
                     }
                }
             
                 
                
               
              }
            }
            //echo "{$tID}, {$timein}, {$timeout} \n";
      }
      echo "Successfully Saved.";
  }
  function viewNewAdjustment(){
      $id = $this->input->post("bID");
      $this->load->view('process/view_adjustment');
  }
  // End for manage dtr

  # for ica-hyperion 21194
  # by justin (with e)
  function doDeleteLeaveRequest(){
    $this->load->model('leave_application');
    $id = $this->input->post('id');
    
    # delete mo muna sa leave_app_base
    $res = $this->leave_application->deleteLeaveRequestByAdmin($id);

    # delete na sa leave_request
    $res = $this->leave_application->deleteLeaveRequestByAdmin($id,true);

    # return success caption
    echo "Successfully deleted.";
  }
  # end for ica-hyperion 21194

  # by Naces 12-16-17
  function addStudentSchedule(){
    $data = $this->input->post();
    $this->extras->saveStudentSchedule($data);
    

  }
  function viewStudentSchedule(){
    $this->load->view('process/viewStudentSchedule');
  }
  function yearLevel(){
    $dept = $this->input->post('dept');
    $yl = $this->extras->showStudentYL("",$dept);
    echo $yl;
  }

  function recomputePercentage(){
    $toks = $this->input->post("toks");
    $data["selected"] = $this->gibberish->decrypt($this->input->post("tnt"), $toks);
    $data['teaching'] = $this->db->query("SELECT * FROM recomputing_percentage WHERE teachingtype = 'teaching' ")->result_array();
    $data['nonteaching'] = $this->db->query("SELECT * FROM recomputing_percentage WHERE teachingtype = 'nonteaching' ")->result_array();
    // echo "<pre>"; print_r($data);
    $this->load->view('recomputePercentage', $data);
  }

  function loadEmployees(){
    $data = $this->input->post();
    $toks = $this->input->post("toks");
    $data['employee'] = $this->employee->loadallemployee("","","","",false,$this->gibberish->decrypt($data['tnt'], $toks ),$this->gibberish->decrypt($data['deptid'], $toks ),$this->gibberish->decrypt($data['status'], $toks ), $this->gibberish->decrypt($data['empstat'], $toks ));
    $this->load->view('process/employeelist', $data);
  }

  function deletecutoff(){
    $err = 0;
    $id = $this->input->post("dkey");
    $hasprocess = $this->attendance->cutoffHasProcessed($id);
    if($hasprocess) $err = 1;
    else $this->attendance->deleteCutoff($id);
    echo $err;
  }

  function loadRestrictionData(){
    $this->load->model('loaddata');
    $data['data'] =  $this->loaddata->loadeprofileconfig()->result();
    $this->load->view("config/restriction_data", $data);
  }

  #end by Naces
}

/* End of file process_.php */
/* Location: ./application/controllers/process_.php */