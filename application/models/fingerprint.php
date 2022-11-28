<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fingerprint extends CI_Model {

    private $device_id, 
            $serial_no, 
            $machine_no,
            $terminal_id,
            $machine_id,
            $current_ip, 
            $current_hostname, 
            $current_city, 
            $current_region, 
            $current_country, 
            $current_loc, 
            $current_postal, 
            $current_org, 
            $current_user_id, 
            $is_logged_in = false; 

    public function getEmployeeListWithBio(){
        $employee_list = $this->db->query("SELECT CONCAT(a.`lname`, ' ,', a.`fname` , ' ,', a.`mname`) AS fullname, a.`employeeid`,a.`teachingtype`, a.`campusid`, b.`rfid` FROM employee a INNER JOIN bio b WHERE a.`employeeid` = b.`userID` GROUP BY a.`employeeid`")->result_array();
    return $employee_list;
    }

    public function getEmployeeListExcluded(){
        $employee_list = $this->db->query("SELECT CONCAT(a.`lname`, ', ', a.`fname` , ', ', a.`mname`) AS fullname, a.`employeeid`,a.`teachingtype`, a.`campusid`  FROM employee a LEFT JOIN bios_excluded b ON a.`employeeid` = b.`userid` WHERE b.`userid` IS NULL ORDER BY fullname ASC")->result_array();
    return $employee_list;
    }

    public function getEmployeeExcludedInOut($employee='', $status='', $deptid=''){
        $where_clause = "";
        if(isset($status) && $status != "all")            $where_clause .= "AND a.isactive = '$status' ";
        if($employee)        $where_clause .= "AND a.employeeid = '$employee'";
        if($deptid)        $where_clause .= "AND a.deptid = '$deptid'";

        $list = $this->db->query("SELECT CONCAT(a.`lname`, ' ,', a.`fname` , ' ,', a.`mname`) AS fullname, a.`employeeid`,a.`teachingtype`, b.`isactive`, a.`campusid` FROM employee a INNER JOIN bios_excluded b WHERE a.`employeeid` = b.`userid` $where_clause GROUP BY a.`employeeid`")->result_array();
        return $list;
    }

    public function getEmployeeBioPic($empid){
        $employee_bio = $this->db->query("SELECT * FROM bio WHERE userID = '$empid'")->result_array();
        return $employee_bio;
    }

    public function saveUpdateBio($data, $userID, $finger){
       $this->db->where("userID = '$userID' AND finger = '$finger'");
        $q_save_civil_status = $this->db->update('bio', $data);
        $rfid = $data['rfid'];
        $employeeid = $data['userID'];
        $this->db->query("UPDATE employee SET `employeecode` = '$rfid' WHERE employeeid = '$employeeid'");
        return $q_save_civil_status;
    }

    public function saveInsertBio($data){
        $q_save_civil_status = $this->db->insert("bio", $data);
        $rfid = $data['rfid'];
        $employeeid = $data['userID'];
        $this->db->query("UPDATE employee SET `employeecode` = '$rfid' WHERE employeeid = '$employeeid'");
        return $q_save_civil_status;
    }

    public function setupBioExcluded($userid, $name, $action){
        if($action == "add"){
            $this->db->query("INSERT INTO bios_excluded (userid,name) VALUES ('$userid','$name')");
            return TRUE;
        }else{
            $this->db->query("DELETE FROM bios_excluded WHERE userid = '$userid'");
            return true;
        }
    }

    public function get_terminal_username($current_ip){
        $terminal_name = '';
        $q_terminal = $this->db->query("SELECT a.username as auname, a.*, c.* FROM user_gate_history a LEFT JOIN terminal c ON a.username = c.username WHERE ip = '$current_ip' AND logout = '0000-00-00 00:00:00' AND logout_by = ''");
        if($q_terminal->num_rows() > 0){
            $terminal_name = ($q_terminal->row()->terminal_name == '' || $q_terminal->row()->terminal_name == NULL ? $q_terminal->row()->auname : $q_terminal->row()->terminal_name);
        }
        return $terminal_name;
    }

    public function getServerTime(){
        return $this->db->query("SELECT CURRENT_TIMESTAMP")->row()->CURRENT_TIMESTAMP;
    }

    public function getFullName($employeeid=''){
        $res = $this->db->query("SELECT fullname FROM bio c WHERE id='$employeeid'");
        return $res->row(0)->fullname;
    }

    public function getFullNameEmployee($employeeid=''){
        $res = $this->db->query("SELECT CONCAT(a.`lname`, ' ,', a.`fname` , ' ,', a.`mname`) AS fullname FROM employee a WHERE employeeid='$employeeid'");
        if(isset($res->row(0)->fullname)){
            return $res->row(0)->fullname;
        }else{
            $res = $this->db->query("SELECT CONCAT(a.`lname`, ' ,', a.`fname` , ' ,', a.`mname`) AS fullname FROM student a WHERE studentid='$employeeid'");
            if(isset($res->row(0)->fullname)){
                return $res->row(0)->fullname;
            }else{
                return "";
            }
        }
        
    }

    public function getCampus($compuid=''){
        $res = $this->db->query("SELECT description FROM code_campus WHERE code='$compuid'");
        if (isset($res->row(0)->description)) return $res->row(0)->description;
        else return "";
    }

    public function getDept($dept=''){
        $res = $this->db->query("SELECT description FROM code_department WHERE code='$dept'");
        if (isset($res->row(0)->description)) return $res->row(0)->description;
        else return "";
    }

    public function getUserId($id=''){
        $res = $this->db->query("SELECT userID FROM bio c WHERE id='$id'");
        return $res->row(0)->userID;
    }

    public function validate_is_logged_in() 
    { 

        $checker = $this->db->query("SELECT * FROM `user_gate_history` WHERE ip = '$this->current_ip' AND logout = '0000-00-00 00:00:00' AND logout_by = ''")->num_rows() > 0;
        return $checker; 
    }

    function getTerminalCampus($username=''){
        $res = $this->db->query("SELECT campus FROM terminal c WHERE username='$username'");
        return $res->row(0)->campus;
    }

    public function validate_terminal_id()
    {
        $fields             = array();
        $datas              = $this->input->post();
        $sys_salt           = $this->config->config['encryption_key'];  
        $this->current_ip = $datas['privateip']; 
        $this->device_id  = $datas['dvid']; 
        $temp = "1";
        list($this->serial_no,$this->machine_no) = $this->unq_machine_id($sys_salt);  
        $query = $this->db->query("SELECT * FROM `login_terminals` WHERE deviceid = '$this->device_id' AND isactive = '1' LIMIT 1");
        $template = $this->db->query("SELECT b.template FROM login_trail_gate a LEFT JOIN terminal b ON a.`userid` = b.`username` WHERE a.ip = '$this->current_ip' LIMIT 1");
        if (isset($template->row(0)->template)) {
            $temp = $template->row(0)->template;
        }

        if ($query -> num_rows() == 0) 
        {  
            $fields = array("deviceid" => $this->device_id, "ipaddr" => $this->current_ip, "description" => "", "userid" => 1);
            $this->db->insert("login_terminals", $fields); 
            // Retrieve the last inserted id  
            $this->terminal_id = $this->db->insert_id();   

        }else{ 
            $fields = array(
                "ipaddr" => $this->current_ip,  
                "timestamp" => date("Y-m-d H:i:s", strtotime($this->getServerTime()))
            );
            $this->db->where("deviceid",$this->device_id) -> where("isactive",1) -> update("login_terminals",$fields);
            foreach ($query -> result() as $rw) 
            { 
                 $this->terminal_id = $rw->id;
            }
        }
        
        $query = $this->db->query("SELECT * FROM `login_hardwares` WHERE machineid = '$this->machine_no' AND isactive = '1' LIMIT 1");
        switch (true) 
        { 
            case $query->num_rows() == 0:
                $fields = array("serialno" => $this->serial_no, "machineid" => $this->machine_no, "salt" => $sys_salt);
                $this->db->insert("login_hardwares", $fields); 
                // Retrieve the last inserted id 
                $this->machine_id = $this->db->insert_id();  
            break; 
            case $query->num_rows() == 1: 
                foreach ($query -> result() as $rw) 
                { 
                     $this->machine_id = $rw->id; 
                } 
            break;
        } 

        $response = array( "is_logged_in" => $this->validate_is_logged_in(), "current_ip" => $this->current_ip, "template" => 0 ); 

        echo json_encode($response); 
    }

    function unq_machine_id($salt = "") 
    { 
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
        {  
            $temp = sys_get_temp_dir().DIRECTORY_SEPARATOR."diskpartscript.txt";
            if(!file_exists($temp) && !is_file($temp)) file_put_contents($temp, "select disk 0\ndetail disk");
            $output = shell_exec("diskpart /s ".$temp);
            $lines = explode("\n",$output);
            $result = array_filter($lines,function($line) 
            { 
                return stripos($line,"ID:")!==false;
            });
            if(count($result)>0) 
            { 
                $result = array_shift(array_values($result));
                $result = explode(":",$result);
                $result = trim(end($result));       
            } else $result = $output;       
        } 
        else 
        { 
            $result = shell_exec("blkid -o value -s UUID");  
            if (stripos($result,"blkid")!==false) 
            {
                $result = $_SERVER['HTTP_HOST'];
            }
        }    
        $result = !empty($result) ? $result : HARDWARE_SERIAL; 
        return array( $result, md5($salt.md5($result)) );
    }

    function _get_attempts_query()
    { 
        // DB table to use
        $table = 'login_attempts'; 

        // Table's primary key 
        $primaryKey = 'id';

        // Table columns  
        $columns = array(   
            array( 'db' => 'datecreated', 'dt' => 0, 'formatter' => function( $d, $row ) { return date("m/d/Y",strtotime($d)); } ),
            array( 'db' => 'stamp', 'dt' => 1, 'formatter' => function( $d, $row ) { return date("h:i:s A",strtotime($d)); } ),
            array( 'db' => 'fullname', 'dt' => 2 ),
            array( 'db' => 'action', 'dt' => 3 ),
            array( 'db' => 'username', 'dt' => 4 ), 
        ); 

        // SQL server connection information
        $sql_details = array(
            'user' => $this-> db ->username,
            'pass' => $this-> db ->password,
            'db'   => $this-> db ->database,
            'host' => $this-> db ->hostname
        );  

        /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
         * If you just want to use the basic configuration for DataTables with PHP
         * server-side, there is no need to edit below this line.
         */  
        $joinQuery = $extraWhere = $groupBy = $having = $data = "";

        $datas = $this->input->get();
        
        $username = $this->get_terminal_username($datas["ip"]);
        //QUICK FIX - ken demo sir james
        // $username = $this->get_terminal_username($this->input->ip_address());
        $extraWhere = array( "status = 'success'" );

        if (isset($datas['today'])) $extraWhere[] = "date(datecreated) = '".date("Y-m-d")."' AND username = '".$username."'";
        $extraWhere = implode( " AND ",$extraWhere ); 
        // echo "<pre>";print_r($extraWhere);die;
        echo json_encode( 
            SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy, $having )
        );

    }

    function logged_in_other_device($username){
        $q_terminal = $this->db->query("SELECT * FROM `user_gate_history` WHERE username = '$username' AND logout = '0000-00-00 00:00:00' AND logout_by = ''");
        if($q_terminal->num_rows() > 0) return true;
    }

    function process_gate()
    { 
        // session_unset();
        // // {check_username: "test", check_password: "", p: "U2FsdGVkX19RLiOPYGDh88gY2RWnXQp4tp0nB1ufBTU=↵", toks: "f90ddd77e400dfe6a3fcf479b00b1ee29e7015c5bb8cd70f5f…ddc7324f45168cffaf81f8c3ac93996f6536eef38e5e40768"}
        // if (!session_id()) session_start(); //we need to call PHP's session object to access it through CI 
        $sess_array = $datas = $fields = array();  
        $hashedPword = ""; 
        $return = 3;
        $datas = $this->input->post();   
        $gib = $this->gibberish->decrypt( $datas["p"], $datas["toks"] ); 
        $this->privateip = $datas['privateip'];
        $this->ip = $datas['privateip'];
        if($this->logged_in_other_device($datas['check_username'])) return 2;

        switch (true) 
        { 
            case !$this->validate_is_logged_in():
                
                if ($this->db->query("prepare stmt from 'select * from terminal where username = ? and password = ? limit 1;';") ) 
                {  
                    $this->db->query("set @a = '".$datas['check_username']."';");
                    $this->db->query("set @b = '".md5($gib)."';");
                    $query = $this->db->query("execute stmt using @a, @b;");

        // echo "<pre>";print_r($query->num_rows());die;
                    if ($query->num_rows() == 1) 
                    { 
                        foreach ($query->result() as $rw) 
                        { 
                            
                            $isresigned = false; 
                            switch ($rw -> type) 
                            { 
                                case 'EMPLOYEE':
                                    $isresigned = $this -> db -> select("*") -> from("employee") -> where("employeeid",$datas['check_username']) -> where("(dateresigned = '1970-01-01' OR dateresigned = '0000-00-00' OR dateresigned IS NULL) =",0) -> where("isactive",1) -> limit(1) -> get() -> num_rows() == 0; 
                                break; 
                            } 

                            switch (true) 
                            {
                                case !$isresigned: 

                                    $user_session = md5($_COOKIE['ci_sessions']);
                                    // Get the user-agent string of the user.
                                    $user_browser = $_SERVER['HTTP_USER_AGENT']; 
                                    // XSS protection as we might print this value
                                    $userinfoid = preg_replace("/[^0-9]+/", "", $rw->id); 
                                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/","",$rw->username); 
                                    $login_string = hash('sha512', $user_session . $user_browser); 
                                    $csrf = (function_exists('random_int')?'N'.random_int(0,PHP_INT_MAX):'N'.rand(0,PHP_INT_MAX)); 
                                    $macadd  = $this->return_macaddress($this->ip);  

                                    $fullname = "{$rw->terminal_name}"; 
                                    $fullname = trim(strtoupper(str_replace("?","Ñ",$fullname)));

                                    $fields = array(
                                        "userid" => $username,
                                        "ip" => $this->ip,
                                        "mac" => $macadd,
                                        "time" => time()  
                                    ); 
                                    $this -> db -> insert("login_trail_gate",$fields);   

                                    $sess_array = array( 
                                        'username' => $username,
                                        'logged_in' => '1', 
                                        'fullname' => $fullname,
                                        'userid' => $userinfoid, 
                                        'usertype' => $rw->type,
                                        'campus' => $rw->campus,
                                        'building' => $rw->building,
                                        'floor' => $rw->floor,
                                        'accessdetailed' => array(),
                                        'useraccess' => '',
                                        'activemenu' => 0, 
                                        'ci_id' => $user_session, 
                                        'csrf' => $csrf,
                                        'login_string' => $login_string  
                                    );  
                                    // Set session 

                                    $this->session->set_userdata('logged_in', $sess_array);  
                                    
                                    // Set token 
                                    // setcookie('XSRF-TOKEN',$sess_array['csrf'],0,'/'); 
                                    $fields = array(
                                        "username" => $username,
                                        "ip" => $this -> ip,
                                        "login" => date("Y-m-d H:i:s", strtotime($this->getServerTime())) 
                                    );
                                    $this -> db -> insert("user_gate_history", $fields );
                                     
                                    // removed muna 
                                    // $fields = array(
                                    //     "session_id"    => $user_session, 
                                    //     "ip_address"    => $this -> ip, 
                                    //     "user_agent"    => $user_browser, 
                                    //     "last_activity" => time(),
                                    //     "user_data"     => serialize($sess_array), 
                                    //     "username"      => $username 
                                    // );
                                    // $this -> db -> insert( $this -> db -> dtr_base.".ci_sessions", $fields );

                                    $return = 1;

                                break;
                                default: 

                                    $return = 2; 

                                break;
                            }
                        }
                    }else{
                        $res = $this->inhouseSeminarGate($datas['check_username'], md5($gib));
                        if($res) return 1;
                    }
                }

            break; 
        } 

        return $return;
    }

    function syncfingers()
    { 

        $campusid = $this->fingerprint->getTerminalCampus($this->get_terminal_username($this->input->ip_address()));
        // echo "<pre>"; print_r($campusid);die;
        $FinalData = array();
        $campusfinger = array();
        $bypassEmployee = array();

     
        $campusfinger = $this ->db->query("select id,template,rfid from bio a INNER JOIN employee b ON a.`userID` = b.`employeeid` WHERE b.`campusid` = '$campusid'" )->result_array();

        $bypassEmployee = $this ->db->query("select employee from bypass_employee WHERE `code` LIKE '%$campusid%'" )->result_array();
        $rfid = 666;
        $counter = 1;
        foreach ($campusfinger as $data) {
            if ($data["rfid"] == $rfid){
                unset($data["id"]);
                end($FinalData);
                $lastKey = key($FinalData);
                $FinalData[$lastKey] += array('template'.$counter => $data["template"]);
                $counter++;
            } 
            else{
                $rfid = $data["rfid"];
                unset($data["rfid"]);
                array_push($FinalData, $data);
                $counter = 1; 
            }
        }

        foreach ($bypassEmployee as $key) {
            $emp = substr($key['employee'], 1);
            $employesslist = explode(",", $emp);
            foreach ($employesslist as $key => $value) {
                $bypassEmployeeData = $this ->db->query("select id,template,rfid from bio where userID = '$value'" )->result_array();
                $newrfid = 666;
                $newcounter = 1;
                foreach ($bypassEmployeeData as $row) {
                    if ($row["rfid"] == $newrfid){
                        unset($row["id"]);
                        end($FinalData);
                        $newlastKey = key($FinalData);
                        $FinalData[$newlastKey] += array('template'.$newcounter => $row["template"]);
                        $newcounter++;

                    } 
                    else{
                        $newrfid = $row["rfid"];
                        unset($row["rfid"]);
                        array_push($FinalData, $row);
                        $newcounter = 1; 
                    } 
                    
               }
            }
        }
        echo json_encode($FinalData);  
    }

    function return_macaddress( $remoteIp = "" )
    { 
        //050406
        // This code is under the GNU Public Licence
        // Written by michael_stankiewicz {don't spam} at yahoo {no spam} dot com
        // Tested only on linux, please report bugs

        // WARNinG: the commands 'which' and 'arp' should be executable
        // by the apache user; on most linux boxes the default configuration
        // should work fine
        $ipFound = false; 
        // get the arp executable path
        $location = `which arp`;
        $location = rtrim($location);
        // Execute the arp command and store the output in $arpTable
        $arpTable = `$location -n`;
        //echo $arpTable;
        // Split the output so every line is an entry of the $arpSplitted array
        $arpSplitted = explode("\n", $arpTable);
        //echo $arpSplitted[6];
        // get the remote ip address (the ip address of the client, the browser)
        $remoteIp = str_replace(".", "\\.", $remoteIp);
        //echo $remoteIp;
        // Cicle the array to find the match with the remote ip address
        foreach ($arpSplitted as $value) 
        { 
            // Split every arp line, this is done in case the format of the arp
            // command output is a bit different than expected
            $valueSplitted = explode(" ",$value);
            //echo $valueSplitted[0];
            foreach ($valueSplitted as $spLine) 
            { 
                //echo $spLine;
                if (preg_match("/$remoteIp/",$spLine)) 
                {
                    $ipFound = true;
                }
                // The ip address has been found, now rescan all the string
                // to get the mac address
                if ($ipFound) 
                { 
                    // Rescan all the string, in case the mac address, in the string
                    // returned by arp, comes before the ip address
                    // (you know, Murphy's laws)
                    reset($valueSplitted);
                    foreach ($valueSplitted as $spLine) 
                    { 
                        if (preg_match("/[0-9a-f][0-9a-f][:-]".
                        "[0-9a-f][0-9a-f][:-]".
                        "[0-9a-f][0-9a-f][:-]".
                        "[0-9a-f][0-9a-f][:-]".
                        "[0-9a-f][0-9a-f][:-]".
                        "[0-9a-f][0-9a-f]/i",$spLine)) 
                        {
                            return $spLine;
                        }
                    } 
                }
                $ipFound = false;
            }
        }
        return $ipFound;
    }

    private function get_current_user()
    {
        
        $return = '';
        // $query = $this->db->select("a.username") -> from("`user_gate_history` a") -> where("a.ip", $this->current_ip) -> where("a.logout","0000-00-00 00:00:00") -> where("a.logout_by","") -> limit(1) -> get(); Ken, kinomment ko lang kasi minsan walang ip na nakukuha, or baka sa dev lang ganun? - Riel

        $query = $this->db->select("a.username") -> from("`user_gate_history` a") -> where("a.ip", $this->current_ip) -> where("a.logout","0000-00-00 00:00:00") -> where("a.logout_by","") -> limit(1) -> get();
        if ($query -> num_rows() == 1) 
        { 
            foreach ($query -> result() as $rw) 
            {  
                $return = $rw->username;
            }
        }
        return $return; 
    }

    function checkIfExcluded($employeeid, $tap_allow){
        $q_gate = $this->db->query("SELECT * FROM bypass_employee WHERE employee LIKE '%$employeeid%' ");

        if($q_gate->num_rows() > 0){
            foreach($q_gate->result_array() as $row){
                foreach(explode(",", $row["code"]) as $code){
                    if(in_array($code, $tap_allow)) return true;
                }
            }
        }else{
            return false;
        }
    }

    function get_pic( $id = "" )
    {    
        $img = "";
        $query = $this -> db -> select("content,mime") -> from("elfinder_file") -> where( "name", $id.".JPG" )->get();
        if ($query -> num_rows() > 0) 
        { 
            foreach ($query->result() as $rw) 
            { 
                $img = "data:".$rw->mime.";base64,".base64_encode($rw->content); 
            }
        } 
        return $img;
    }

    function getIDBio($id=''){
        $res = $this->db->query("SELECT id FROM bio WHERE userID = '$id' ORDER BY id ASC LIMIT 1");
        if (isset($res->row(0)->id)) return $res->row(0)->id;
        else return false;
    }

    function getEmployeeID($id=''){
        $res = $this->db->query("SELECT employeeid FROM employee WHERE employeecode = '$id' ORDER BY employeeid ASC LIMIT 1");
        if (isset($res->row(0)->employeeid)) return $res->row(0)->employeeid;
        else return false;
    }

    function validate_rfid_2()
    {
        $password = "";
        $return = array( "text" => "<b>Oh snap!</b> User does not exists.", "stat" => 0 ); 
        $datas = $this ->input->post();  

        $query = $this->db->select("id,ipaddr")->from("login_terminals")->where("ipaddr",$datas["dvid"])->where("isactive",1)->limit(1)->get();
        foreach ($query -> result() as $rw) { 
             $this->terminal_id = $rw->id;
             $this->current_ip = $rw->ipaddr;
        }
        // echo "<pre>";print_r($this->db->last_query());die;
        // echo "<pre>";print_r($query->result());die;
        // <<< get ams allow login
        $tap_allow = array();
        $q_gate = $this->db->get_where("terminal", array("username" => $this->get_current_user()))->result();

        $last_log_type = $this->getLastLog($this->getEmployeeID($datas['tmp']));
        $LR = "";
        if ($last_log_type['log_type'] == "IN") $LR = "OUT";
        else $LR = "IN";
        $isStudent = false;

        foreach ($q_gate as $row) $tap_allow = explode(",", $row->campus);
        $query = $this -> db -> query("select employeeid, deptid, campusid, concat(trim(lname),', ',trim(fname)) as fullname from employee where employeecode = '".$datas['tmp']."';" );
        if ($query -> num_rows() == 0) {
            $query = $this -> db -> query("select studentid, concat(trim(lname),', ',trim(fname)) as fullname from student where studentcode = '".$datas['tmp']."';" );
            $isStudent = true;
        }

        if ($query -> num_rows() > 0) 
        {         
            foreach ($query -> result() as $rw) 
            {   
                if($isStudent){
                    $isexluded = true;
                    $this->current_user_id = $rw->studentid;
                    $rw->employeeid = $rw->studentid;
                    $type = $rw->deptid = "";

                    $lastRecordChecker = $this->db->query("SELECT `action`, (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(datecreated) ) > 60 AS difference FROM (`login_attempts`) WHERE `user_id` = '$rw->studentid' AND `status` = 'success' ORDER BY `id` desc LIMIT 1" )->result();
                    if (isset($lastRecordChecker[0]->action)){
                        $lastRecordChecker = $lastRecordChecker[0]->action;
                    }else{
                        $lastRecordChecker = "OUT";
                    } 
                }else{
                    $isexluded = $this->checkIfExcluded($rw->employeeid, $tap_allow);
                    $this->current_user_id = $rw->employeeid;
                    $type = $rw->campusid;

                    $lastRecordChecker = $this->db->query("SELECT `action`, (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(datecreated) ) > 60 AS difference FROM (`login_attempts`) WHERE `user_id` = '$rw->employeeid' AND `status` = 'success' ORDER BY `id` desc LIMIT 1" )->result();
                    if (isset($lastRecordChecker[0]->action)){
                        $lastRecordChecker = $lastRecordChecker[0]->action;
                    }else{
                        $lastRecordChecker = "OUT";
                    } 
                }
                
                $ServerTime = date('Y-m-d', strtotime($this->getServerTime()));
                // $webSetup = '';

                // $weblogin = $this->db->query("SELECT `status` FROM  weblogin_setup WHERE employee = '$rw->employeeid' AND `status` = 'active'");
                // if($weblogin->num_rows() > 0) $webSetup = $weblogin->row()->status;
                // // echo "<pre>";print_r($webSetup);die;
                // if($webSetup){
                //     $return = array( 
                //         'text' => "<b>Failed!</b> Employee weblogin is active!",
                //         'stat' => 0
                //     );
                // }else
                if(in_array($type, $tap_allow) || $isexluded){
                    // $isexempted = $this -> db -> select("id") -> from("bios_excluded") -> where("userid", $rw->employeeid) -> where("isactive",1) -> get() -> num_rows() > 0; 
                    $isexempted = true;

                    switch (true) 
                    { 
                        case $isexempted: 
                            $return = array( 
                                'text' => "<b>Notice!</b> User is exempted in finger-print.",
                                'stat' => 2, 
                                'EmpID' => $rw->employeeid, 
                                'Fullname' => $rw->fullname, 
                                'Dept' => $this->fingerprint->getDept($rw->deptid),
                                'Image' => $this->fingerprint->get_pic($rw->employeeid), 
                                'S1' => "",
                                'S1' => "",
                                'CreatedBy' => "", 
                                'CreatedWhen' => "",
                                'LR' => $LR 
                            ); 
                        break; 
                        default: 
                            $return = array(
                                'text' => "<b>Notice!</b> Please place your finger.",
                                'stat' => 1, 
                                'EmpID' => $rw->employeeid, 
                                'Fullname' => $rw->fullname,
                                'Dept' => $this->fingerprint->getDept($rw->deptid), 
                                'Image' => $this->fingerprint->get_pic($rw->employeeid),
                                'CreatedBy' => "", 
                                'id' => $this->fingerprint->getID($rw->employeeid),
                                'LR' => $LR 
                            ); 
                        break;
                    }
                }else{
                    $return = array( 
                        'text' => "<b>Failed!</b> Employee is not allowed to tap in this gate..",
                        'stat' => 0
                    );
                }
            }
        }  
        echo json_encode($return);  
    }

    function getID($id=''){
        $res = $this->db->query("SELECT id FROM bio WHERE userID = '$id' ORDER BY id ASC LIMIT 1");
        if (isset($res->row(0)->id)) return $res->row(0)->id;
        else return false;
    }

    function validate_rfid_2_seminar()
    {
        $password = "";
        $return = array( "text" => "<b>Oh snap!</b> User does not exists.", "stat" => 0 ); 
        $datas = $this ->input->post();  

        $query = $this->db->select("id,ipaddr")->from("login_terminals")->where("deviceid",$datas["dvid"])->where("isactive",1)->limit(1)->get();
        foreach ($query -> result() as $rw) { 
             $this->terminal_id = $rw->id;
             $this->current_ip = $rw->ipaddr;
        }

        // <<< get ams allow login
        $tap_allow = array();
        $q_gate = $this->db->get_where("terminal", array("username" => $this->get_current_user()))->result();

        $last_log_type = $this->getLastLogInhouseSeminar($this->getEmployeeID($datas['tmp']), $this->get_current_user());
        $LR = "";
        if ($last_log_type['log_type'] == "IN") $LR = "OUT";
        else $LR = "IN";

        foreach ($q_gate as $row) $tap_allow = explode(",", $row->campus);
        $query = $this->db->query("select employeeid, deptid, campusid, concat(trim(lname),', ',trim(fname)) as fullname from employee where employeecode = '".$datas['tmp']."';" );

        if ($query->num_rows() > 0) 
        {         
            foreach ($query -> result() as $rw) 
            {   
                $isexluded = true;
                $this->current_user_id = $rw->employeeid;
                $type = $rw->campusid;

                $lastRecordChecker = $this->db->query("SELECT `action`, (UNIX_TIMESTAMP() - UNIX_TIMESTAMP(datecreated) ) > 60 AS difference FROM (`login_attempts`) WHERE `user_id` = '$rw->employeeid' AND `status` = 'success' ORDER BY `id` desc LIMIT 1" )->result();
                if (isset($lastRecordChecker[0]->action)){
                    $lastRecordChecker = $lastRecordChecker[0]->action;
                }else{
                    $lastRecordChecker = "OUT";
                } 
                $ServerTime = date('Y-m-d', strtotime($this->getServerTime()));

                if(in_array($type, $tap_allow) || $isexluded){
                    // $isexempted = $this -> db -> select("id") -> from("bios_excluded") -> where("userid", $rw->employeeid) -> where("isactive",1) -> get() -> num_rows() > 0;
                    $isexempted = true; 
                    switch (true) 
                    { 
                        case $isexempted: 
                            $return = array( 
                                'text' => "<b>Notice!</b> User is exempted in finger-print.",
                                'stat' => 2, 
                                'EmpID' => $rw->employeeid, 
                                'Fullname' => $rw->fullname, 
                                'Dept' => $this->fingerprint->getDept($rw->deptid),
                                'Image' => $this->fingerprint->get_pic($rw->employeeid), 
                                'S1' => "",
                                'S1' => "",
                                'CreatedBy' => "", 
                                'CreatedWhen' => "",
                                'LR' => $LR 
                            ); 
                        break; 
                        default: 
                            $return = array(
                                'text' => "<b>Notice!</b> Please place your finger.",
                                'stat' => 1, 
                                'EmpID' => $rw->employeeid, 
                                'Fullname' => $rw->fullname,
                                'Dept' => $this->fingerprint->getDept($rw->deptid), 
                                'Image' => $this->fingerprint->get_pic($rw->employeeid),
                                'CreatedBy' => "", 
                                'id' => $this->fingerprint->getID($rw->employeeid),
                                'LR' => $LR 
                            ); 
                        break;
                    }
                }else{
                    $return = array( 
                        'text' => "<b>Failed!</b> Employee is not allowed to tap in this gate..",
                        'stat' => 0
                    );
                }
            }
        }  
        echo json_encode($return);  
    }

    function getLastLog($employeeid){
        $return['log_type'] = "new";
        $ServerTime = date('Y-m-d', strtotime($this->getServerTime()));
        $query = $this->db->query("SELECT log_type, localtimein FROM timesheet_trail WHERE date(localtimein) = '$ServerTime' AND userid = '$employeeid'")->result();
        foreach($query as $row)
        {
            $return['log_type'] = $row->log_type;
            $return['localtimein'] = $row->localtimein;
        }
        return $return;
    }

    function getLastLogInhouseSeminar($employeeid, $username){
        $return['log_type'] = "new";
        $ServerTime = date('Y-m-d', strtotime($this->getServerTime()));
        $query = $this->db->query("SELECT log_type, localtimein FROM inhouse_seminar_timesheet WHERE date(localtimein) = '$ServerTime' AND userid = '$employeeid' AND username='$username'")->result();
        foreach($query as $row)
        {
            $return['log_type'] = $row->log_type;
            $return['localtimein'] = $row->localtimein;
        }
        return $return;
    }

    public function saveNoOut($data){
        return $this->db->insert("timesheet_noout", $data);
    }

    public function saveTimesheetTrail($data){
        return $this->db->insert("timesheet_trail", $data);
    }

    public function saveInhouseTimesheet($data){
        return $this->db->insert("inhouse_seminar_timesheet", $data);
    }

    public function saveTimesheetHistory($data){
        return $this->db->insert("timesheet_history", $data);
    }

    public function saveTimesheetHistoryStudent($data){
        return $this->db->insert("timesheet_history_student", $data);
    }

    public function saveInhouseTimesheetHistory($data){
        return $this->db->insert("inhouse_seminar_timesheet_history", $data);
    }

    public function deleteNoOut($userid, $time, $username){
        $this->db->query("DELETE FROM timesheet_noout WHERE userid = '$userid' AND logtime = '$time' AND username = '$username'");
    }

    public function deleteNoOutStudent($userid, $time, $username){
        $this->db->query("DELETE FROM timesheet_noout WHERE userid = '$userid' AND logtime = '$time' AND username = '$username'");
    }

    public function saveCheckInToTimesheet($data){
        return $this->db->insert("timesheet", $data);
    }

    function deleteOtherLogs($userid, $date, $username){
        $this->db->query("DELETE FROM timesheet_trail WHERE userid = '$userid' AND date(localtimein) = '$date' AND username = '$username'");
    }

    function validate_result_3()
    {
        $this->load->model("facial");
        $this->load->model("webcheckin");

        $return = array( 
            'type'  => 'danger',
            'class' => 'my-alert-danger',
            'msg'   => '<b>Oh snap!</b> Change a few things up and try submitting again.'  
        );

        $datas = $this->input->post();

        $data['localtimein'] = date('Y-m-d', strtotime($this->getServerTime()))." ".date("H:i:s",strtotime($datas['ChkIn']));

        
        // echo "<pre>"; print_r($last_log_type);


        $query = $this->db-> select("*") -> from("login_terminals") -> where("ipaddr",$datas["dvid"]) -> where("isactive",1) -> limit(1) -> get();
        foreach ($query -> result() as $rw) 
        { 
             $this->terminal_id = $rw->id;
             $this->current_ip = $rw->ipaddr;
        }

        $query = $this->db-> select("*") -> from("login_hardwares") -> where("isactive",1) -> limit(1) -> get();
        foreach ($query -> result() as $rw) 
        { 
             $this->machine_id = $rw->id;
        }



        $previosDate = date('Y-m-d', strtotime('-1 day', strtotime($this->getServerTime())));


        $Type = (isset($datas["exemp"]))? "Exclued":"Fingerprint";

        $data['userid'] = $datas["EmpID"];
        $data['username'] = $this->get_current_user();
        $data['machine_id'] = $this->machine_id;
        $data['mac_add'] = $this->current_ip;

        $dataHistory['localtimein'] = date('Y-m-d', strtotime($this->getServerTime()))." ".date("H:i:s",strtotime($datas['ChkIn']));
        $dataHistory['userid'] = $datas["EmpID"];
        $dataHistory['username'] = $this->get_current_user();
        $dataHistory['mac_add'] = $this->current_ip;

        $dataAttempts["user_id"] = $data['userid'];
        $dataAttempts["ip"] = $this->current_ip;
        $dataAttempts["terminalid"] = $this->terminal_id;
        $dataAttempts["fullname"] = $this->getFullNameEmployee($data['userid']);
        $dataAttempts["stamp"] = date("H:i:s",strtotime($datas['ChkIn']));
        $dataAttempts['username'] = $this->get_current_user();
        $dataAttempts['time'] = time();

        $isStudentChecker = $this->facial->isStudent($data['userid']);

        if($isStudentChecker == "employee"){

            $last_log_type = $this->getLastLog($datas['EmpID']);
            if ($last_log_type["log_type"] != "new") {
                $start = date_create($last_log_type['localtimein']);
                $end = date_create($data['localtimein']);
                $diff = $end->getTimestamp() - $start->getTimestamp();
                if ($diff < 30) {
                    echo "wait";
                    die;
                }
            }

            if ($last_log_type['log_type'] == "new"){
                $nout = array();
                $data['log_type'] = "IN";
                $nout['userid'] = $data['userid'];
                $nout['logtime'] = $data['localtimein'];
                $nout['log_type'] = "IN";
                $nout['machine_id'] = $this->machine_id;
                $nout['mac_add'] = $this->current_ip;
                $nout['username'] = $this->get_current_user();

                $dataAttempts['action'] = "IN";
                $dataAttempts['status'] = "success";
                
                $this->saveNoOut($nout);
                $this->saveTimesheetTrail($data);
                // echo "<pre>";print_r($this->db->last_query());
                $this->db->insert("login_attempts", $dataAttempts);
                
                $save_data = $this->saveTimesheetHistory($dataHistory);

            }elseif ($last_log_type['log_type'] == "IN") {
                $data['log_type'] = "OUT";
                $timesheetData = array();
                $timesheetData['userid'] = $data['userid'];
                $timesheetData['timein'] = $last_log_type['localtimein'];
                $timesheetData['timeout'] = $data['localtimein'];
                $timesheetData['otype'] = $Type;
                $timesheetData['username'] = $this->get_current_user();

                $dataAttempts['action'] = "OUT";
                $dataAttempts['status'] = "success";
                
                $this->deleteNoOut($data['userid'], $last_log_type['localtimein'], $this->get_current_user());
                $this->saveTimesheetTrail($data);
                $this->saveTimesheetHistory($dataHistory);
                $this->db->insert("login_attempts", $dataAttempts);
                // echo "<pre>";print_r($this->db->last_query());
                $save_data = $this->saveCheckInToTimesheet($timesheetData);

            }elseif ($last_log_type['log_type'] == "OUT") {
                $nout = array();
                $data['log_type'] = "IN";
                $nout['userid'] = $data['userid'];
                $nout['logtime'] = $data['localtimein'];
                $nout['log_type'] = "IN";
                $nout['machine_id'] = $this->machine_id;
                $nout['mac_add'] = $this->current_ip;
                $nout['username'] = $this->get_current_user();

                $dataAttempts['action'] = "IN";
                $dataAttempts['status'] = "success";

                $this->saveNoOut($nout);
                $this->deleteOtherLogs($data['userid'], $previosDate, $this->get_current_user());
                $this->saveTimesheetTrail($data);
                $this->db->insert("login_attempts", $dataAttempts);
                // echo "<pre>";print_r($this->db->last_query());
                $save_data = $this->saveTimesheetHistory($dataHistory);
            }
        }elseif($isStudentChecker == "student"){

            $last_log_type = $this->facial->getLastLogFacialStudent($datas['EmpID'], date('Y-m-d', strtotime($this->getServerTime())));
            if ($last_log_type["log_type"] != "new") {
                $start = date_create($last_log_type['localtimein']);
                $end = date_create($data['localtimein']);
                $diff = $end->getTimestamp() - $start->getTimestamp();
                if ($diff < 30) {
                    echo "wait";
                    die;
                }
            }

            $getStudentParentNumber = $this->facial->getNumberStudentParent($datas['EmpID']);
            if ($last_log_type['log_type'] == "new"){
                $nout = array();
                $data['log_type'] = "IN";
                $nout['userid'] = $data['userid'];
                $nout['logtime'] = $data['localtimein'];
                $nout['log_type'] = "IN";
                $nout['machine_id'] = $this->machine_id;
                $nout['mac_add'] = $this->current_ip;
                $nout['username'] = $this->get_current_user();

                $dataAttempts['action'] = "IN";
                $dataAttempts['status'] = "success";
                
                $this->webcheckin->saveNoOutStudent($nout);
                $this->facial->saveFacialCheckInStudent($data);
                // echo "<pre>";print_r($this->db->last_query());
                $this->db->insert("login_attempts", $dataAttempts);
                if ($getStudentParentNumber != 0) {
                    $msg = $data['userid']." has entered through ". $nout['username']." at ".$data['localtimein'] ;
                    $this->smsSenderMain($getStudentParentNumber, $msg);
                }
                $save_data = $this->saveTimesheetHistoryStudent($dataHistory);

            }elseif ($last_log_type['log_type'] == "IN") {
                $data['log_type'] = "OUT";
                $timesheetData = array();
                $timesheetData['userid'] = $data['userid'];
                $timesheetData['timein'] = $last_log_type['localtimein'];
                $timesheetData['timeout'] = $data['localtimein'];
                $timesheetData['otype'] = $Type;
                $timesheetData['username'] = $this->get_current_user();

                $dataAttempts['action'] = "OUT";
                $dataAttempts['status'] = "success";
                
                $this->deleteNoOutStudent($data['userid'], $last_log_type['localtimein'], $this->get_current_user());
                $this->facial->saveFacialCheckInStudent($data);
                $this->saveTimesheetHistoryStudent($dataHistory);
                $this->db->insert("login_attempts", $dataAttempts);
                // echo "<pre>";print_r($this->db->last_query());
                $save_data = $this->saveCheckInToTimesheetStudent($timesheetData);

                if ($getStudentParentNumber != 0) {
                    $msg = $data['userid']." has entered through ". $timesheetData['username']." at ".$data['localtimein'] ;
                    $this->smsSenderMain($getStudentParentNumber, $msg);
                }

            }elseif ($last_log_type['log_type'] == "OUT") {
                $nout = array();
                $data['log_type'] = "IN";
                $nout['userid'] = $data['userid'];
                $nout['logtime'] = $data['localtimein'];
                $nout['log_type'] = "IN";
                $nout['machine_id'] = $this->machine_id;
                $nout['mac_add'] = $this->current_ip;
                $nout['username'] = $this->get_current_user();

                $dataAttempts['action'] = "IN";
                $dataAttempts['status'] = "success";

                $this->saveNoOutStudent($nout);
                $this->deleteNoOutStudent($data['userid'], $previosDate, $this->get_current_user());
                $this->facial->saveFacialCheckInStudent($data);
                $this->db->insert("login_attempts", $dataAttempts);
                // echo "<pre>";print_r($this->db->last_query());
                $save_data = $this->saveTimesheetHistoryStudent($dataHistory);

                if ($getStudentParentNumber != 0) {
                    $msg = $data['userid']." has entered through ". $nout['username']." at ".$data['localtimein'] ;
                    $this->smsSenderMain($getStudentParentNumber, $msg);
                }
            }
        }
        if ($save_data) $save_data = "success";
        echo $save_data;
    }

    function smsSenderMain($number, $msg)
    {

        $sms = urlencode($msg);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://122.54.191.90:8085/goip_send_sms.html?username=root&password=root&port=2&recipients=" . $number . "&sms=" . $sms);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
        return "success";
    }

    function validate_result_3Seminar()
    {
        $return = array( 
            'type'  => 'danger',
            'class' => 'my-alert-danger',
            'msg'   => '<b>Oh snap!</b> Change a few things up and try submitting again.'  
        );
        $this->current_ip = $this->input->post('ip');
        $terminal_name =  $this->get_terminal_username($this->input->post('ip'));
        if($terminal_name == ''){
            echo "refresh"; die;
        }

        $datas = $this->input->post();

        $data['localtimein'] = date('Y-m-d', strtotime($this->getServerTime()))." ".date("H:i:s",strtotime($datas['ChkIn']));

        $last_log_type = $this->getLastLogInhouseSeminar($datas['EmpID'], $this->get_current_user());


        $query = $this->db-> select("*") -> from("login_terminals") -> where("deviceid",$datas["dvid"]) -> where("isactive",1) -> limit(1) -> get();
        foreach ($query -> result() as $rw) 
        { 
             $this->terminal_id = $rw->id;
             $this->current_ip = $rw->ipaddr;
        }

        $query = $this->db-> select("*") -> from("login_hardwares") -> where("isactive",1) -> limit(1) -> get();
        foreach ($query -> result() as $rw) 
        { 
             $this->machine_id = $rw->id;
        }

        if ($last_log_type["log_type"] != "new") {
            $start = date_create($last_log_type['localtimein']);
            $end = date_create($data['localtimein']);
            $diff = $end->getTimestamp() - $start->getTimestamp();
            if ($diff < 30) {
                echo "wait";
                die;
            }
        }

        $previosDate = date('Y-m-d', strtotime('-1 day', strtotime($this->getServerTime())));

        $this->current_ip = ($this->current_ip ? $this->current_ip : $this->input->ip_address());

        $Type = (isset($datas["exemp"]))? "Exclued":"Fingerprint";

        $data['userid'] = $datas["EmpID"];
        $data['username'] = $this->get_current_user();
        $data['machine_id'] = $this->machine_id;
        $data['mac_add'] = $this->current_ip;

        $dataHistory['localtimein'] = date('Y-m-d', strtotime($this->getServerTime()))." ".date("H:i:s",strtotime($datas['ChkIn']));
        $dataHistory['userid'] = $datas["EmpID"];
        $dataHistory['username'] = $this->get_current_user();
        $dataHistory['mac_add'] = $this->current_ip;

        $dataAttempts["user_id"] = $data['userid'];
        $dataAttempts["ip"] = $this->current_ip;
        $dataAttempts["terminalid"] = $this->terminal_id;
        $dataAttempts["fullname"] = $this->getFullNameEmployee($data['userid']);
        $dataAttempts["stamp"] = date("H:i:s",strtotime($datas['ChkIn']));
        $dataAttempts['username'] = $this->get_current_user();
        $dataAttempts['time'] = time();

        if ($last_log_type['log_type'] == "new"){
            $nout = array();
            $data['log_type'] = "IN";
            $nout['userid'] = $data['userid'];
            $nout['logtime'] = $data['localtimein'];
            $nout['log_type'] = "IN";
            $nout['machine_id'] = $this->machine_id;
            $nout['mac_add'] = $this->current_ip;
            $nout['username'] = $this->get_current_user();
            $logs = $this->saveInhouseTimesheet($data);
            if($logs){
                $this->saveSeminarProfile($data['userid']);
                $dataAttempts['action'] = "IN";
                $dataAttempts['status'] = "success";
                
                $this->saveNoOut($nout);
                
                $this->db->insert("login_attempts", $dataAttempts);
                
                $save_data = $this->saveInhouseTimesheetHistory($dataHistory);
            }else{
                echo "refresh"; die;
            }
        }elseif ($last_log_type['log_type'] == "IN") {
            // $data['log_type'] = "OUT";
            // $timesheetData = array();
            // $timesheetData['userid'] = $data['userid'];
            // $timesheetData['timein'] = $last_log_type['localtimein'];
            // $timesheetData['timeout'] = $data['localtimein'];
            // $timesheetData['otype'] = $Type;
            // $timesheetData['username'] = $this->get_current_user();

            // $dataAttempts['action'] = "OUT";
            // $dataAttempts['status'] = "success";
            
            // $this->deleteNoOut($data['userid'], $last_log_type['localtimein'], $this->get_current_user());
            // $this->saveTimesheetTrail($data);
            // $this->saveTimesheetHistory($dataHistory);
            // $this->db->insert("login_attempts", $dataAttempts);
            // echo "<pre>";print_r($this->db->last_query());
            // $save_data = $this->saveCheckInToTimesheet($timesheetData);

            $nout = array();
            $data['log_type'] = "OUT";
            $nout['userid'] = $data['userid'];
            $nout['logtime'] = $data['localtimein'];
            $nout['log_type'] = "OUT";
            $nout['machine_id'] = $this->machine_id;
            $nout['mac_add'] = $this->current_ip;
            $nout['username'] = $this->get_current_user();

            $dataAttempts['action'] = "OUT";
            $dataAttempts['status'] = "success";
            $logs = $this->saveInhouseTimesheet($data);
            if($logs){
                $this->saveNoOut($nout);
                $this->db->insert("login_attempts", $dataAttempts);
                $save_data = $this->saveInhouseTimesheetHistory($dataHistory);
            }else{
                echo "refresh"; die;
            }
               

        }elseif ($last_log_type['log_type'] == "OUT") {
            $nout = array();
            $data['log_type'] = "IN";
            $nout['userid'] = $data['userid'];
            $nout['logtime'] = $data['localtimein'];
            $nout['log_type'] = "IN";
            $nout['machine_id'] = $this->machine_id;
            $nout['mac_add'] = $this->current_ip;
            $nout['username'] = $this->get_current_user();

            $dataAttempts['action'] = "IN";
            $dataAttempts['status'] = "success";
            $logs = $this->saveInhouseTimesheet($data);
            if($logs){
                $this->saveSeminarProfile($data['userid']);
                $this->saveNoOut($nout);
                $this->deleteOtherLogs($data['userid'], $previosDate, $this->get_current_user());
                
                $this->db->insert("login_attempts", $dataAttempts);
                $save_data = $this->saveInhouseTimesheetHistory($dataHistory);
            }else{
                echo "refresh"; die;
            }
                
            // $nout = array();
            // $data['log_type'] = "IN";
            // $nout['userid'] = $data['userid'];
            // $nout['logtime'] = $data['localtimein'];
            // $nout['log_type'] = "IN";
            // $nout['machine_id'] = $this->machine_id;
            // $nout['mac_add'] = $this->current_ip;
            // $nout['username'] = $this->get_current_user();

            // $dataAttempts['action'] = "IN";
            // $dataAttempts['status'] = "success";
            // $this->saveSeminarProfile($data['userid']);
            // $this->saveNoOut($nout);
            // $this->deleteOtherLogs($data['userid'], $previosDate, $this->get_current_user());
            // $this->saveTimesheetTrail($data);
            // $this->db->insert("login_attempts", $dataAttempts);
            // $save_data = $this->saveTimesheetHistory($dataHistory);
        }
        if ($save_data) $save_data = "success";
        echo $save_data;
    }
    
    function isCardnumberExists($cardnumber){
        return $this->db->query("SELECT * FROM employee WHERE employeecode = '$cardnumber'")->num_rows();
    }

    function inhouseSeminarGate($username, $password){
        $q_inhouse = $this->db->query("SELECT * FROM inhouse_seminar WHERE username = '$username' AND password = '$password'");
        // echo "<pre>"; print_r($this->db->last_query()); 
        if($q_inhouse->num_rows() > 0){
            foreach ($q_inhouse -> result() as $rw){ 
                $user_session = md5($_COOKIE['ci_sessions']);
                // Get the user-agent string of the user.
                $user_browser = $_SERVER['HTTP_USER_AGENT']; 
                // XSS protection as we might print this value
                $userinfoid = preg_replace("/[^0-9]+/", "", $rw->id); 
                // $username = preg_replace("/[^a-zA-Z0-9_\-]+/","",$rw ->username); 
                $username = $rw ->username; 
                $login_string = hash('sha512', $user_session . $user_browser); 
                $csrf = (function_exists('random_int')?'N'.random_int(0,PHP_INT_MAX):'N'.rand(0,PHP_INT_MAX)); 
                $macadd  = $this -> return_macaddress($this->ip);  

                $fullname = "{$rw->title}"; 
                $fullname = trim(strtoupper(str_replace("?","Ñ",$fullname)));

                $fields = array(
                    "userid" => $username,
                    "ip" => $this->ip,
                    "mac" => $macadd,
                    "time" => time()  
                ); 
                $this->db->insert("login_trail_gate",$fields);   

                $sess_array = array( 
                    'username' => $username,
                    'logged_in' => '1', 
                    'fullname' => $fullname,
                    'userid' => $userinfoid, 
                    'usertype' => "seminar",
                    'accessdetailed' => array(),
                    'useraccess' => '',
                    'activemenu' => 0, 
                    'ci_id' => $user_session, 
                    'csrf' => $csrf,
                    'login_string' => $login_string  
                );  
                // Set session 
                $this->session->set_userdata('logged_in', $sess_array);   
                // Set token 
                setcookie('XSRF-TOKEN',$sess_array['csrf'],0,'/'); 

                $fields = array(
                    "username" => $username,
                    "ip" => $this->ip,
                    "login" => date("Y-m-d H:i:s", strtotime($this->getServerTime())) 
                );
                $this->db->insert("user_gate_history", $fields );
                return true;
            }
        }
    }

    function saveSeminarProfile($employeeid){
        $insert_data = array();
        $userdata = $this->session->userdata("logged_in"); 
        $id = $userdata["userid"];
        $q_inhouse = $this->db->query("SELECT * FROM inhouse_seminar a LEFT JOIN reports_item b ON a.workshop = b.ID WHERE a.id = '$id' ");
        if($q_inhouse->num_rows() > 0){
            foreach($q_inhouse->result_array() as $inhouse){
                $insert_data["employeeid"] = $employeeid;
                $insert_data["title"] = ($inhouse["title"] == "")? $inhouse["level"] : $inhouse["title"];
                $insert_data["datef"] = $inhouse["date_from"];
                $insert_data["datet"] = $inhouse["date_to"];
                $insert_data["time_from"] = $inhouse["time_from"];
                $insert_data["time_to"] = $inhouse["time_to"];
                $insert_data["organizer"] = $inhouse["organizer"];
                $insert_data["venue"] = $inhouse["venue"];
                $insert_data["seminar_title"] = ($inhouse["title"] == "")? $inhouse["level"] : $inhouse["title"];
                $insert_data["location"] = $inhouse["location"];
                $insert_data["regfee"] = $inhouse["regfee"];
                $insert_data["transfee"] = $inhouse["transfee"];
                $insert_data["accfee"] = $inhouse["accfee"];
                $insert_data["total"] = $inhouse["total"];
                $insert_data["modified_by"] = $this->session->userdata("fullname");
                $this->db->insert($this->inhouseSeminarList($inhouse["category"]), $insert_data);
            }
        }
    }

    public static function inhouseSeminarList($category){
        $list = array("PTS_PDP"=>"employee_pts", "PTS_PDP1"=>"employee_pts_pdp1", "PTS_PDP2"=>"employee_pts_pdp2", "PTS_PDP3"=>"employee_pts_pdp3");
        return isset($list[$category]) ? $list[$category] : 'employee_pts';
    }

    function getTerminalLogID($employeeid, $locatimein){
        $return['id'] = "";
        $query = $this->db->query("SELECT id FROM `login_attempts_terminal` WHERE user_id = '$employeeid' AND stamp_in = '$locatimein'")->result();
        foreach($query as $row)
        {
            $return['id'] = $row->id;
        }
        
        return $return['id'];
    }

}


