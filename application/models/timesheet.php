<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Timesheet extends CI_Model {
  function logmenow($uid,$logtype,$secs,$macadd){
      $this->db->query("CALL prc_timesheet_set('$uid','".date("Y-m-d H:i:s")."','{$macadd}','$logtype','{$secs}',@res,@fullname,@message,@timed,@submacadd,@userid)");
      # echo "CALL prc_timesheet_set('$uid','".date("Y-m-d H:i:s")."','".$this->extras->returnmacaddress()."','$logtype','{$secs}',@res,@fullname,@message,@timed)";
      $q = $this->db->query("select @res as RESULT_NUM,@fullname as FULLNAME_SET,@message as MESSAGE_SET,@timed as TIME_LEFT,@submacadd as TIME_LEFT,@userid as USERID");
      return $q->row(0);
  }
  function machinedisplaystatus($macadd=""){
      $return = "";
      $mstat = "";
      $mdescription = "";
      $this->db->_reset_select(); /** reset active select */
      $this->db->select("mac_add,type,description,mac_status");
      $this->db->from("machine_setup");
      $this->db->where("mac_add",$macadd);
        $q = $this->db->get();
        
        if($q->num_rows()>0){
         $mstat = $q->row(0)->type; 
         $mdescription= $q->row(0)->description;
        }
        switch($mstat){
           case 'OUT':
             $return = "This station is for LOGGING OUT only.";
           break;
           case 'IN':
             $return = "This station is for LOGGING IN only.";
           break;
           case 'IN-OUT':
             $return = "";
           break;
           default:
             $return = "This station is not registered.";
           break; 
        }
       return array($mstat,$return,$mdescription); 
  }
  function loglist_user($limit,$date,$macadd){
    # echo "pasok";
      $this->db->_reset_select(); /** reset active select 
      $this->db->select("a.userid,a.logtime,a.log_type,b.lname,b.fname,b.mname");

      $this->db->from("timesheet_trail a");
      $this->db->join("employee b","a.userid=b.employeecode");
      $this->db->order_by("a.logtime","DESC");
      */
      
      
      if($date){
      #  $this->db->where("DATE(a.logtime)",$date);
      }
      # $this->db->limit($limit);
      # $q = $this->db->get();
                            
      $q = $this->db->query("SELECT a.userid,a.logtime,a.log_type,a.lname,a.fname,a.mname,a.mac_add FROM 
                            (SELECT a.userid,a.logtime,a.log_type,b.lname,b.fname,b.mname,a.mac_add FROM timesheet_trail a INNER JOIN employee b ON a.userid=b.employeeid WHERE a.userid<>''".($date?" AND DATE(a.logtime)='{$date}'":"").($macadd?" AND a.mac_add='{$macadd}'":"")." 
                            UNION 
                            SELECT a.userid,a.logtime,a.log_type,b.lname,b.fname,b.mname,a.mac_add FROM timesheet_trail a INNER JOIN student b ON a.userid=b.studentid WHERE a.userid<>''".($date?" AND DATE(a.logtime)='{$date}'":"").($macadd?" AND a.mac_add='{$macadd}'":"").")
                            AS a
                            order by a.logtime DESC LIMIT $limit")->result();
      return $q; 
  }
  function csvatt($data){
        if($data){
            $inserted = 0;
            $ins = $this->db->insert('timesheet', $data);
            if($ins)    $inserted++;
            return $inserted;
        }
    }
	
	//ADDED 07-03-2017
	function csvUploaded($data){
        if($data){
            $query = $this->db->insert('timesheet_uploaded', $data);
        }
    }
	
  function csvsched($data,$display=false){
        if($data){
            $inserted = 0;
            if($this->checkemp($data['employeeid'])){
                if($display){
                    $this->db->query("DELETE FROM employee_schedule WHERE employeeid='{$data['employeeid']}'");
                    $this->db->insert('employee_schedule', $data);  
                }else{
                    $ins = $this->db->insert('employee_schedule_history', $data);
                    if($ins)     $inserted++;
                }
            }
            return $inserted;
        }
    }
  function dow($day = "",$idx = false){
        if($idx)
            $arr = array("Monday"=>"1","Tuesday"=>"2","Wednesday"=>"3","Thursday"=>"4","Friday"=>"5","Saturday"=>"6");
        else
            $arr = array("Monday"=>"M","Tuesday"=>"T","Wednesday"=>"W","Thursday"=>"TH","Friday"=>"F","Saturday"=>"S");
        return $arr[$day];
    }
  function checkemp($eid = ""){
    $query = $this->db->query("SELECT * FROM employee WHERE isactive=1 AND employeeid='$eid'");
    if($query->num_rows() > 0)
        return true;
    else
        return false;
  }
  
  function userlog($data=""){
    $job        = $data['job'];
    $userid     = $data['userid'];
    $machine_id = $data['macid'];
    $ltype      = $data['ltype'];
    $localtime  = $data['localtime'];
    
    // Machine Type
    if(!$ltype){  
        $query = $this->db->query("select type from machine_setup where mac_add='{$machine_id}'");
        if($query->num_rows() > 0) $ltype = $query->row(0)->type;  
    }
    
    // PROCESS
    $this->db->query("CALL prc_timesheet_set('$userid','".date("Y-m-d H:i:s")."','{$machine_id}','{$ltype}',0,@res,@fullname,@message,@timed,@submacadd,@userid)");
    
    // RESULT
    $query = $this->db->query("select IFNULL(@res,'') as RESULT_NUM,IFNULL(@fullname,'') as FULLNAME_SET,IFNULL(@message,'') as MESSAGE_SET,IFNULL(@timed,'') as TIME_LEFT,IFNULL(@submacadd,'') as SUBMACADD,IFNULL(@userid,'') as USERID");
    foreach($query->result() as $row){
        $status        = $row->RESULT_NUM;
        $name          = mb_convert_encoding($row->FULLNAME_SET,"UTF-8");
        $user_message  = $row->MESSAGE_SET;
        $submachine_id = $row->SUBMACADD;
        $userid        = $row->USERID;
    }
    return json_encode(array('type'=>'machine', 'machineid'=>$machine_id, 'submachineid'=>$submachine_id, 'userid'=>$userid, 'name'=>$name, 'message'=>$user_message, 'status'=>$status));;
  }


  function userlog1($data=""){
    $job        = $data['job'];
    $userid     = $data['userid'];
    $machine_id = $data['macid'];
    $ltype      = $data['ltype'];
    $localtime  = $data['localtime'];
    $username   = $this->session->userdata('username');
    $machine_id = '';

    ///< validation if no user is logged in
    if(!$username) return json_encode(array('type'=>'machine', 'machineid'=>$machine_id, 'submachineid'=>'', 'userid'=>$userid, 'name'=>'', 'message'=>'Gate is inactive. Please refresh page.', 'status'=>'5'));

    // Machine Type
    if(!$ltype){  
        $query = $this->db->query("select type from machine_setup where mac_add='{$machine_id}'");
        if($query->num_rows() > 0) $ltype = $query->row(0)->type;  
    }
	
    $isEmployee = $is_teaching = false;
    $tbl_timesheet_trail = 'timesheet_trail';
  	$log_time = $employeeid = $bdate = $bdaymsg = $studentid = '';
  	$employeeid_q = $this->db->query("SELECT employeeid, bdate, teachingtype FROM employee WHERE (employeecode='$userid' OR employeeid='$userid' OR REPLACE(employeeid,'-','')='$userid')");

  	if($employeeid_q->num_rows() >0){
      $isEmployee = true;
      $employeeid = $employeeid_q->row(0)->employeeid;
      $bdate      = $employeeid_q->row(0)->bdate;
      if($bdate) $bdaymsg = (date('m-d',strtotime($bdate)) == date('m-d')) ? ' Happy Birthday!  ' : '';
      if($employeeid_q->row(0)->teachingtype == "teaching") $is_teaching = true;
    }else{
      $tbl_timesheet_trail = 'timesheet_trail_student';

        $stud_q = $this->db->query("SELECT studentid FROM student WHERE (studentcode='$userid' OR studentid='$userid' OR REPLACE(studentid,'-','')='$userid')");

        if($stud_q->num_rows() >0){
          $studentid = $stud_q->row(0)->studentid;
        }
    }

    $temp_id = $isEmployee ? $employeeid : $studentid;

    $login = $this->db->query("SELECT DATE_FORMAT(logtime,'%H:%i:%s') AS login FROM $tbl_timesheet_trail WHERE userid='$temp_id' AND log_type='IN' AND DATE_FORMAT(logtime,'%Y-%m-%d')='".date("Y-m-d")."' ORDER BY logtime DESC LIMIT 1");

    if($login->num_rows > 0){
      $log_time = $login->row(0)->login;
    } 

    
    # for ica-hyperion 22101
    $gate_type = ($isEmployee) ? (($is_teaching) ? "ET" : "ENT") : "ST";
    $allow_gate_config = array(
      "ST" => "Student",
      "ET" => "Employee (Teaching)",
      "ENT" => "Employee (Non Teaching)"
    );
    
    $allow_arr = array();
    $q_gate_allow = $this->db->query("SELECT gate_tap_allow FROM user_info WHERE username='{$this->session->userdata('username')}'")->result();
    foreach ($q_gate_allow as $row) $allow_arr = explode(",", $row->gate_tap_allow);

    if(!in_array($gate_type, $allow_arr)) return json_encode(array('type'=>'machine', 'machineid'=>$machine_id, 'submachineid'=>'', 'userid'=>$userid, 'name'=>'', 'message'=> $allow_gate_config[$gate_type] .' is not allowed to access this gate', 'status'=>'5'));
    # end for ica-hyperion 22101

    if($isEmployee) return $this->logEmployee($log_time,$machine_id,$ltype,$username,$userid,$bdaymsg);
    else return $this->logStudent($log_time,$machine_id,$ltype,$username,$userid);

  }

  ///<----------------------------------employee---------------------------------------------------------
  function logEmployee($log_time='',$machine_id='',$ltype='',$username='',$userid='',$bdaymsg=''){

    /*delete last day timesheet_trail data*/

    $old_userid = $old_logtype = $old_date = $old_time = '';
    $timein = $timeout = '';
    list($login_date, $login_time) = explode(" ", $log_time);
    $check_last_log = $this->db->query("SELECT DATE_FORMAT(logtime, '%Y-%m-%d') AS lastlog FROM timesheet_trail ORDER BY logtime DESC LIMIT 1")->row()->lastlog;
    if($check_last_log != $login_date){
      $all_lastlog = $this->db->query("SELECT userid,DATE_FORMAT(logtime, '%Y-%m-%d') AS DATE, DATE_FORMAT(logtime, '%H:%i:%s') AS TIME, log_type FROM timesheet_trail WHERE DATE_FORMAT(logtime, '%Y-%m-%d') = '$check_last_log' ORDER BY userid,logtime")->result_array();
      foreach($all_lastlog as $value){
        if($value['userid'] == $old_userid && $old_logtype != $value['log_type']){

           $timein = $old_date." ".$old_time;
           $timeout = $value['DATE']." ".$value['TIME'];

           $this->db->query("DELETE FROM timesheet_trail WHERE logtime = '$timein' AND userid = '{$value['userid']}' AND username != 'Facial' ");
           $this->db->query("DELETE FROM timesheet_trail WHERE logtime = '$timeout' AND userid = '{$value['userid']}' AND username != 'Facial'");
        }
      $old_userid = $value['userid'];
      $old_logtype = $value['log_type'];
      $old_date = $value['DATE'];
      $old_time = $value['TIME'];
      }
    }

    /*end*/

    /*if($log_time){
      ///< checking logout 1 min after login
      if( ( strtotime(date("H:i:s",time())) - strtotime($log_time) ) <= 60 ){
        return json_encode(array('type'=>'machine', 'machineid'=>$machine_id, 'submachineid'=>'', 'userid'=>$userid, 'name'=>'', 'message'=>$bdaymsg.'You already tapped your card. Please try after 1 minute.', 'status'=>'5'));
      }
    }*/

    // PROCESS
    $this->db->query("CALL prc_timesheet_set_wd_username('$userid','". $log_time ."','{$machine_id}','{$ltype}',0,'$username',@res,@fullname,@message,@timed,@submacadd,@userid)");

    // RESULT
    $query = $this->db->query("select IFNULL(@res,'') as RESULT_NUM,IFNULL(@fullname,'') as FULLNAME_SET,IFNULL(@message,'') as MESSAGE_SET,IFNULL(@timed,'') as TIME_LEFT,IFNULL(@submacadd,'') as SUBMACADD,IFNULL(@userid,'') as USERID");
    foreach($query->result() as $row){
        $status        = $row->RESULT_NUM;
        $name          = mb_convert_encoding($row->FULLNAME_SET,"UTF-8");
        $user_message  = $row->MESSAGE_SET;
        $submachine_id = $row->SUBMACADD;
        $userid        = $row->USERID;
    }
    return json_encode(array('type'=>'machine', 'machineid'=>$machine_id, 'submachineid'=>$submachine_id, 'userid'=>$userid, 'name'=>$name, 'message'=>$bdaymsg.$user_message, 'status'=>$status));
  }

  ///<----------------------------------student---------------------------------------------------------
  function logStudent($log_time='',$machine_id='',$ltype='',$username='',$userid=''){

    /*if($log_time){
      ///< checking logout 2 mins after login
      if( ( strtotime(date("H:i:s",time())) - strtotime($log_time) ) <= 120 ){
        return json_encode(array('type'=>'machine', 'machineid'=>$machine_id, 'submachineid'=>'', 'userid'=>$userid, 'name'=>'', 'message'=>'You already tapped your card. Please try after 2 minutes.', 'status'=>'5'));
      }
    }*/

    // PROCESS
    $this->db->query("CALL prc_timesheet_set_student('$userid','". $log_time ."','{$machine_id}','{$ltype}',0,'$username',@res,@fullname,@message,@timed,@submacadd,@userid)");

    // RESULT
    $query = $this->db->query("select IFNULL(@res,'') as RESULT_NUM,IFNULL(@fullname,'') as FULLNAME_SET,IFNULL(@message,'') as MESSAGE_SET,IFNULL(@timed,'') as TIME_LEFT,IFNULL(@submacadd,'') as SUBMACADD,IFNULL(@userid,'') as USERID");
    foreach($query->result() as $row){
        $status        = $row->RESULT_NUM;
        $name          = mb_convert_encoding($row->FULLNAME_SET,"UTF-8");
        $user_message  = $row->MESSAGE_SET;
        $submachine_id = $row->SUBMACADD;
        $userid        = $row->USERID;
        $user_logtype = explode(" ",$user_message);
        if(in_array("IN", $user_logtype)){
            $this->db->query("INSERT INTO timesheet_history_student (userid, timein, mac_add, username) VALUES ('$userid', '".date("Y-m-d H:i:s")."','$machine_id', 'EAST')");
        }
    }
    return json_encode(array('type'=>'machine', 'machineid'=>$machine_id, 'submachineid'=>$submachine_id, 'userid'=>$userid, 'name'=>$name, 'message'=>$user_message, 'status'=>$status));
  }



  function loglist_user1($limit,$date,$macadd){
      $this->user->getGateUsername();
      $username = $this->session->userdata('username');
      $this->db->_reset_select();
                            
      $q = $this->db->query("SELECT a.userid,a.logtime,a.log_type,a.lname,a.fname,a.mname,a.mac_add ,a.username FROM 
                            (SELECT a.userid,a.logtime,a.log_type,b.lname,b.fname,b.mname,a.mac_add ,a.username FROM timesheet_trail a INNER JOIN employee b ON a.userid=b.employeeid WHERE a.userid<>''".($date?" AND DATE(a.logtime)='{$date}'":"").($macadd?" AND a.mac_add='{$macadd}'":"")." AND a.username='$username'  
                            UNION 
                            SELECT a.userid,a.logtime,a.log_type,b.lname,b.fname,b.mname,a.mac_add ,a.username FROM timesheet_trail_student a INNER JOIN student b ON a.userid=b.studentid WHERE a.userid<>''".($date?" AND DATE(a.logtime)='{$date}'":"").($macadd?" AND a.mac_add='{$macadd}'":"")."  AND a.username='$username' )
                            AS a
                            order by a.logtime DESC LIMIT $limit")->result();
      return $q; 
  }

  function employeeTimeinToday($employeeid, $datetoday){
    $login = false;
    $q_att = $this->db->query("SELECT DATE_FORMAT(timein, '%H:%i:%s') as timein FROM timesheet WHERE userid = '$employeeid' AND DATE_FORMAT(timein, '%Y-%m-%d') = '$datetoday' ");
    if($q_att->num_rows > 0) $login = $q_att->row()->timein;

    if(!$login){
        $q_findLogTime = $this->db->query("SELECT DATE_FORMAT(localtimein, '%H:%i:%s') as localtimein FROM timesheet_trail WHERE userid='$employeeid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$datetoday' AND log_type='IN' ORDER BY localtimein DESC;")->result();

        if (count($q_findLogTime) == 0) {
            $q_findLogTime = $this->db->query("SELECT DATE_FORMAT(localtimein, '%H:%i:%s') as localtimein FROM webcheckin_history WHERE userid='$employeeid' AND DATE_FORMAT(localtimein, '%Y-%m-%d') = '$datetoday' AND log_type='IN' ORDER BY localtimein DESC;")->result();
              // echo "<pre>"; print_r($this->db->last_query());
        }
        
        foreach ($q_findLogTime as $res){
            if($res->localtimein != "0000-00-00 00:00:00" && $res->localtimein) $login = $res->localtimein;
        }
    }
    
    return $login;
  }

  function getTimesheetTrailOld($dfrom, $dto, $where_clause){
        $data = array();
        $last_emp = $last_terminal = "";
        $timesheet_record = $this->db->query("SELECT DATE(datecreated) AS date, fullname, user_id, username, action FROM login_attempts` WHERE DATE(datecreated) BETWEEN '$dfrom' AND '$dto' $where_clause AND status = 'success' ORDER BY user_id, date ");
        if($timesheet_record->num_rows() > 0){  
            foreach($timesheet_record->result_array() as $row){
                if($last_emp != $row["user_id"]){
                    $last_terminal = $row["username"];
                }else{
                    if($last_terminal != $row["username"]){
                        $data[$row["user_id"]][$row["date"]]["fullname"] = $row["fullname"];
                        $data[$row["user_id"]][$row["date"]]["in"] = $last_terminal;
                        $data[$row["user_id"]][$row["date"]]["out"] = $row["username"];
                    }
                }

                $last_emp = $row["user_id"];
                $last_terminal = $row["username"];
            }
        }

        return $data;
    }

    function getTimesheetTrail($dfrom, $dto, $where_clause, $terminal, $gate, $logs, $employeeid){
        $data = array();
        $datenow = date("Y-m-d");
        $last_emp = $last_terminal = "";
        $facial_WC = "1";
        $imageDatabase = $this->db->database_files;
        $timesheet_record = $this->db->query("SELECT DATE(datecreated) AS date, fullname, user_id, username, action FROM login_attempts` WHERE DATE(datecreated) BETWEEN '$dfrom' AND '$dto' $where_clause AND status = 'success' ORDER BY user_id, date ");
        if($terminal &&  $gate == "facial") $facial_WC .= " AND d.id = '".$terminal."'";
        if ($employeeid != "") $facial_WC .= " AND c.employeeid = '".$employeeid."'";
        if($dfrom && $dto)  $facial_WC .= " AND DATE(FROM_UNIXTIME(FLOOR(a.time/1000))) BETWEEN '$dfrom' AND '$dto'";
        $WC .= " AND (('$datenow' < c.dateresigned2 OR c.dateresigned2 = '0000-00-00' OR c.dateresigned2 = '1970-01-01' OR c.dateresigned2 IS NULL) AND c.isactive ='1')";

        $facial_record = $this->db->query("SELECT DATE(FROM_UNIXTIME(FLOOR(a.time/1000))) as date, `c`.`employeeid` AS `employeeid`,`a`.`id` AS `id`,`a`.`deviceKey` AS `deviceKey`,`b`.`name` AS `name` ,`e`.`description` AS `description`,FROM_UNIXTIME(FLOOR(a.time/1000), '%h:%i:%s %p') AS `time`,`d`.`deviceName` AS `deviceName`,`f`.`base64image` AS `base64image` FROM `facial_Log` AS `a` INNER JOIN `facial_person` AS `b` ON (`a`.`personId` = `b`.`personId`) INNER JOIN `employee` AS `c` ON (`b`.`employeeid` = `c`.`employeeid`) LEFT JOIN `facial_heartbeat` AS `d` ON (`a`.`deviceKey` = `d`.`deviceKey`) LEFT JOIN `code_office` AS `e` ON (`c`.`office` = `e`.`code`) LEFT JOIN ".$imageDatabase.".`facial_logs_image` AS `f` ON (`a`.`id` = `f`.`base_id`) WHERE $facial_WC GROUP BY a.id ORDER BY employeeid, date");
        $last_emp = $last_terminal = "";
        if($timesheet_record->num_rows() > 0 && ($gate == "all" || $gate == "terminal")){  
            foreach($timesheet_record->result_array() as $row){
                if($last_emp != $row["user_id"]){
                    $last_terminal = $row["username"];
                }else{
                    if($last_terminal != $row["username"]){
                        $data[$row["user_id"]][$row["date"]]["fullname"] = $row["fullname"];
                        $data[$row["user_id"]][$row["date"]]["in"] = $last_terminal;
                        $data[$row["user_id"]][$row["date"]]["out"] = $row["username"];
                    }
                }
                $last_emp = $row["user_id"];
                $last_terminal = $row["username"];
            }
        }
        $last_date = $last_employee = "";
        if($facial_record->num_rows() > 0  && ($gate == "all" || $gate == "facial")){  
            foreach($facial_record->result_array() as $row){
              if($last_date != ''){
                if($last_date != $row["date"] && $last_employee != $row["employee"]){
                    $data[$row["employeeid"]][$row["date"]]["fullname"] = $row["name"];
                    $data[$row["employeeid"]][$row["date"]]["in"] = $row['time'];
                  }else{
                     $data[$row["employeeid"]][$row["date"]]["out"] = $row['time'];
                  }
              }else{
                  $data[$row["employeeid"]][$row["date"]]["fullname"] = $row["name"];
                  $data[$row["employeeid"]][$row["date"]]["in"] = $row['time'];
              }
                  
                $last_date = $row["date"];
                $last_employee = $row["employeeid"];
            }
        }
        // echo "<pre>"; print_r($data); die;
        return $data;
    }
  
}

/* End of file timesheet.php */
/* Location: ./application/models/timesheet.php */