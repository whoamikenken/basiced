<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Model {

    function validate(){
        $toks = $this->input->post("toks");
        $uname = $this->gibberish->decrypt( $this->input->post("fusername"), $toks );
        $upass = $this->gibberish->decrypt( $this->input->post("fpassword"), $toks );
        $this->db->where('username',$uname);
        $this->db->where('password',md5($upass));
        $query = $this->db->get('user_info');
        if($query->num_rows == 0){
            $uname = $this->getUsernameByEmail($uname);
            $this->db->where('username',$uname);
            $this->db->where('password',md5($upass));
            $query = $this->db->get('user_info');
        }

        if($query->num_rows==1){
			foreach($query->result() as $row)
			{
				$etype = $row->type;
			}
			if($etype == "EMPLOYEE")
			{
				$query2 = $this->db->query("SELECT * FROM employee
				WHERE employeeid = '{$uname}'
				AND (dateresigned = '1970-01-01' OR dateresigned = '0000-00-00' OR dateresigned IS NULL)
				AND `isactive` = 1");
				
				if($query2->num_rows()==0){
					return false;
				}
			}
            $sess = array(
                 'username'  => $uname,
                 'logged_in' => TRUE
            );
                
            /** Load first the main info */
            $this->session->set_userdata($sess);
            /** Load Other User Info */    
            $this->loaduserdata($sess);
            /** Load info */
            $this->session->set_userdata($sess);
            /** Load User Access Rights */
            $this->load->model("user_access");
            $this->user_access->loaduseraccess($sess);      
            /** Add the other info */
            $this->session->set_userdata($sess);
        
            $this->db->query($this->db->update_string("user_info", array("ipadd"=>$this->input->ip_address()), array("username" => "$uname")));

            $this->db->query($this->db->update_string("ci_sessions", array("username"=>$uname), array("session_id" => $this->session->userdata('session_id'))));
            
            $this->menus->login_trail($uname);

            // added by justin (with e) for ica-hyperion 21912
            $this->session->sess_expiration = 1200; // 20 mins session expiration 
            return true;        
        }else return false;  
    }

    /**
     * Validate gate login
     *
     * @return int (0 > Invalid username or password; 1 > valid gate access ; 2 > valid access but multiple login ; 3 > no gate access)
     */

    function validateGateAccess(){
        $uname = $this->input->post("fusername");
        $upass = $this->input->post("fpassword");
        $this->db->where('username',$uname);
        $this->db->where('password',md5($upass));
        $query = $this->db->get('user_info');
        if($query->num_rows==1){
            if($query->row(0)->gateaccess){
                if(!$this->checkMultipleLogin($uname)){
                        $sess = array(
                             'username'  => $uname,
                             'logged_in' => TRUE
                        );

                        /** Load first the main info */
                        $this->session->set_userdata($sess);
                        /** Load Other User Info */    
                        $this->loaduserdata($sess);
                        /** Load info */
                        $this->session->set_userdata($sess);
                        /** Load User Access Rights */
                        $this->load->model("user_access");
                        $this->user_access->loaduseraccess($sess);      
                        /** Add the other info */
                        $this->session->set_userdata($sess);

                        $this->db->query($this->db->update_string("ci_sessions", array("username"=>$uname), array("session_id" => $this->session->userdata('session_id'))));

                        $this->db->query($this->db->update_string("user_info", array("ipadd"=>$this->input->ip_address()), array("username" => "$uname")));

                        $this->db->query($this->db->insert_string("user_gate_history",array("username"=>$uname, "ip"=>$this->input->ip_address(), "login"=>date('Y-m-d H:i:s'))));

                        // added by justin (with e) for ica-hyperion 21912
                        $this->session->sess_expiration = 0; // 0 mins session expiration 
                        return 1;        
                }else return 2;
            }else return 3;
        }else return 0;  
    }

    function validateGoogleAuth($email){
        $q_user = $this->db->query("SELECT * FROM employee
        WHERE personal_email = '{$email}'
        AND (dateresigned = '1970-01-01' OR dateresigned = '0000-00-00' OR dateresigned IS NULL)
        AND `isactive` = 1");
        
        if($q_user->num_rows==0){
            return false;
        }
        $uname = $q_user->row()->employeeid;
        $sess = array(
             'username'  => $uname,
             'logged_in' => TRUE
        );
            
        /** Load first the main info */
        $this->session->set_userdata($sess);
        /** Load Other User Info */    
        $this->loaduserdata($sess);
        /** Load info */
        $this->session->set_userdata($sess);
        /** Load User Access Rights */
        $this->load->model("user_access");
        $this->user_access->loaduseraccess($sess);      
        /** Add the other info */
        $this->session->set_userdata($sess);
    
        $this->db->query($this->db->update_string("user_info", array("ipadd"=>$this->input->ip_address()), array("username" => "$uname")));

        $this->db->query($this->db->update_string("ci_sessions", array("username"=>$uname), array("session_id" => $this->session->userdata('session_id'))));
        
        $this->menus->login_trail($userid);

        // added by justin (with e) for ica-hyperion 21912
        $this->session->sess_expiration = 1200; // 20 mins session expiration 
        return true;        
    }

    function checkMultipleLogin($uname = ''){
        $count = 0;
        $logs = $this->db->query("SELECT user_data FROM ci_sessions WHERE user_data IS NOT NULL");
        if($logs->num_rows() > 0){
            foreach ($logs->result() as $key => $row) {
              foreach (unserialize($row->user_data) as $k => $v) {
                if($k == 'username'){
                    if($v == $uname) $count++;
                }
              }
            }
        }
        return $count;
    }
    function checkFirstLogged($uname = '', $sessionid=''){
        $logs = $this->db->query("SELECT user_data, session_id FROM ci_sessions WHERE user_data IS NOT NULL");
        if($logs->num_rows() > 0){
            foreach ($logs->result() as $key => $row) {
              foreach (unserialize($row->user_data) as $k => $v) {
                if($k == 'username'){
                    if($v == $uname){
                        if($row->session_id == $sessionid) return true;
                        else return false;
                    }
                }
              }
            }
        }
        return false;
    }

    function loaduserdata(&$data){
        $q = $this->db->query("SELECT CONCAT(lastname,', ',firstname,' ',middlename) as FULLNAME,id,type,gateaccess FROM user_info where username='".$this->session->userdata('username')."'");
        $funame = "";
        $fid = "";
        $ftype = "";
        $gateaccess = 0;
        if($q->num_rows()){
          $row = $q->row(0);
          $funame = $row->FULLNAME;
          $fid = $row->id;
          $ftype = $row->type; 
          $gateaccess = $row->gateaccess;  
        }
        $data['fullname'] = $funame;
        $data['userid'] = $fid;
        $data['usertype'] = $ftype;
        $data['gateaccess'] = $gateaccess;
        $data['message_box'] = 1;
        $data['canwrite'] = 1;
    }
    
    function get_fullname($user){
        $q = $this->db->query("SELECT CONCAT(lastname,', ',firstname) as FULLNAME FROM user_info where id='$user'");
        $funame = "";
        if($q->num_rows()){
          $row = $q->row(0);
          $funame = $row->FULLNAME;
        }
        return $funame;
    }
    function get_employee_fullname($empid){
        $q = $this->db->query("SELECT CONCAT(lname,', ',fname,' ', mname) AS FULLNAME FROM employee WHERE employeeid='$empid'");
        $funame = "";
        if($q->num_rows()){
          $row = $q->row(0);
          $funame = $row->FULLNAME;
        }
        return $funame;
    }
    function get_alluserbytype($type="",$like="",$sort=""){
        $q = $this->db->query("SELECT CONCAT(lastname,', ',firstname,IF(middlename<>'',concat(' ',substr(middlename,1,1),'.'),'')) as FULLNAME FROM user_info where username<>'' AND status='ACTIVE' and lastname<>''".($type ? " and type='$type'" : "").($like ? " and (lastname like '$like%' OR firstname like '$like%' OR middlename like '$like%')" : "").($sort ? " ORDER BY $sort" : ""));
        $fnames = array();
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          array_push($fnames,$row->FULLNAME);
        }
        return $fnames;
    }
    function get_allbytype($type="",$like="",$sort=""){
        $q = $this->db->query("SELECT id,CONCAT(lastname,', ',firstname,IF(middlename<>'',concat(' ',substr(middlename,1,1),'.'),'')) as FULLNAME FROM user_info where username<>'' AND status='ACTIVE'".($type ? " and type='$type'" : "").($like ? " and (lastname like '$like%' OR firstname like '$like%' OR middlename like '$like%')" : "").($sort ? " ORDER BY $sort" : ""));
        $fnames = array();
        for($t=0;$t<$q->num_rows();$t++){
          $row = $q->row($t);
          array_push($fnames,array($row->id,$row->FULLNAME));
        }
        return $fnames;
    }
    function matchfullname($fullname){
        $q = $this->db->query("SELECT id FROM user_info where CONCAT(lastname,', ',firstname,IF(middlename<>'',concat(' ',substr(middlename,1,1),'.'),''))=TRIM('$fullname')");
        $id = "";
        if($q->num_rows()){
          $row = $q->row(0);
          $id = $row->id;
        }
        return $id;
    }
    function showusers($advancesearch = ""){
        $q = $this->db->query("select id,username,CONCAT(lastname,', ',firstname,IF(middlename<>'',concat(' ',substr(middlename,1,1),'.'),'')) as FULLNAME,lastname,firstname,middlename,type from user_info where status='ACTIVE' and type<>'SUPER ADMIN'" . ($advancesearch ? "$advancesearch" : "")." ORDER BY FULLNAME");
        return $q; 
    }
    /* ADDED BY JUSTIN - 06-13-2015 */
    function showMsg($id=""){
        $query = $this->db->query("SELECT DISTINCT COUNT(id) as totalpending FROM messages WHERE (FIND_IN_SET('$id',receiver) OR receiver = '0') AND status='PENDING'");
        foreach($query->result() as $row){
            $tpending = " (".$row->totalpending.")";
        }
        return $tpending;
    }
    function eprofileconfig($data = ""){
        $user   = $this->session->userdata("username");
        $eid    = $data['employeeid'];
        $dept   = $data['deptid'];
        $dfrom  = $data['datefrom'];
        $dto    = $data['dateto'];
        $tnt    = $data['tnt'];
        $status = $data['estatus'];
        $data  += array("editedby"=>$user);
        $param  = "";
        
        if($eid)    $param .= " AND employeeid='$eid'";
        if($dept)   $param .= " AND deptid='$dept'";
        if($tnt)    $param .= " AND teachingtype='$tnt'";
        if($status) $param .= " AND employmentstat='$status'";
        $query = $this->db->query("SELECT * FROM employee WHERE (dateresigned='1970-01-01' OR dateresigned='0000-00-00' OR dateresigned IS NULL) $param");
        if($query->num_rows() > 0){
            foreach($query->result() as $row){
                $empid = $row->employeeid;
                $qexist = $this->db->query("SELECT * FROM employee_restriction WHERE employeeid='$empid'");
                if($qexist->num_rows() > 0)
                    $this->db->query("UPDATE employee_restriction SET datefrom='$dfrom', dateto='$dto', editedby='$user', timestamp=CURRENT_TIMESTAMP WHERE employeeid='$empid'");
                else
                    $this->db->query("INSERT INTO employee_restriction (employeeid,datefrom,dateto,editedby) VALUES ('$empid','$dfrom','$dto','$user')");
            }
        }
        
        if($query)  return "Employee Access has been saved successfully";
        else        return "Failed to Saved!. Please check your connection";
    }
    function hash( $string, $salt=FALSE ){
        if( !$salt ) $salt = $this->salt();
        $passarr = array( 'salt'=>$salt );
        $newstr = $this->bconv( $string.$salt );
        $newstr = hash( 'sha1', $newstr.$salt );
        $newstr = $this->bconv( $newstr );
        $newstr = hash( 'md5', $newstr );
        $newstr = hash( 'sha256', $newstr );
        return $newstr;
    }
    function salt(){
        $salt = uniqid();
        return $salt;
    }
    function bconv( $string ){
        $newstr = base_convert($string, 32, 8);
        return $newstr;
    }

    function getLastGateUserLogin($ip_address){
       //$this->session->sess_destroy();
        $q_last_log = $this->db->query("SELECT * FROM user_gate_history WHERE ip='$ip_address' AND logout='0000-00-00 00:00:00' ORDER BY login DESC LIMIT 1;")->result();
        
        foreach ($q_last_log as $row) {
            $username = $row->username;
            
            $q_session_id = $this->db->query("SELECT * FROM ci_sessions WHERE username='$username' AND ip_address='$ip_address'")->result();
            
            $sessionid = $user_agent = $last_activity = "";
            foreach ($q_session_id as $res) {
                $sessionid = $res->session_id;
                $user_agent = $res->user_agent;
                $last_activity = $res->last_activity;
            }

            $session = array(
                "logged_in"  => true,
                "gateaccess" => true,
                "username"   => $username,
                "session_id" => $sessionid,
                "ip_address" => $ip_address,
                "user_agent" => $user_agent,
                "last_activity" => $last_activity
            );

            $this->session->set_userdata($session);

            $this->db->query("DELETE FROM ci_sessions WHERE ip_address='$ip_address' AND user_data IS NULL AND username IS NULL;");
        }
    }

    // =================================== new gate
    function getGateUserInfo($user_id="", $ip_address="", $has_no_logout=false, $limit=""){
        $where_clause  = "";
        $where_clause .= ($user_id) ? "AND a.username = '$user_id'' " : "";
        $where_clause .= ($ip_address) ? "AND b.ip='$ip_address' " : "";
        $where_clause .= ($has_no_logout) ? "AND b.logout='0000-00-00' AND logout_by='' " : "";
        
        $limit_clause = ($limit) ? "LIMIT $limit" : "";
        
        return $this->db->query("SELECT b.*
                                         FROM user_info a
                                         INNER JOIN user_gate_history b ON b.username = a.username
                                         WHERE a.gateaccess = '1' $where_clause
                                         ORDER BY b.login DESC
                                         $limit_clause;")->result();
    }

    function getGateUsername(){
        $username = "";

        $q_gate_info = $this->user->getGateUserInfo("", $this->session->userdata('ip_address'), true, 1);
        foreach ($q_gate_info as $row) $username = $row->username;
        if(!$this->session->userdata("username") && isset($username)) $this->session->set_userdata('username', $username);
    }

    function isGateAccount($username){
        $is_valid = false;

        $q_user_info = $this->db->query("SELECT * FROM user_info WHERE username='$username' AND gateaccess=1;")->result();
        if(count($q_user_info) > 0) $is_valid = true;
        
        return $is_valid;
    }

    /*login using emal*/
    function getUsernameByEmail($email){
        $q_user = $this->db->query("SELECT * FROM employee WHERE email = '$email' OR personal_email = '$email' ");
        if($q_user->num_rows > 0) return $q_user->row()->employeeid;
        else return "0";
    }

    public function isUserExists($username){
        $q_user = $this->db->query("SELECT * FROM user_info WHERE username = '$username' ");
        if($q_user->num_rows() == 0) return $this->db->query("SELECT * FROM employee WHERE email = '$username' "); 
        else return true;
    }

    public function updateUserPassword($username, $password){
        $this->db->query("UPDATE user_info SET password = '$password' WHERE username = '$username' ");
    }

    public function updateUserPasswordByID($key, $id, $password){
        $this->db->query("UPDATE user_info SET password = '$password' WHERE id = '$id' ");
        $this->db->query("UPDATE forgot_password_history SET status = 'READ' WHERE `key` = '$key' ");
    }

}

/* End of file user.php */
/* Location: ./application/models/user.php */