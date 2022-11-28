<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

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

    public function __construct(){
        parent::__construct();
        header("strict-transport-security: max-age=31536000");
        header("X-Frame-Options: DENY");
        header('X-Content-Type-Options: nosniff');
        header("Referrer-Policy: no-referrer");
        // header("Content-Security-Policy: default-src 'none'; script-src 'self'; connect-src 'self'; img-src 'self'; style-src 'self';");
        require_once APPPATH.'third_party/src/Google_Client.php';
        require_once APPPATH.'third_party/src/contrib/Google_Oauth2Service.php';
    }

	public function index()
	{
        // phpinfo();die;
        if(!$this->islogged()){            
            $this->loadloginform();
        }else{
            if(!$this->islogged()){            
                $this->loadloginform();
                return;
            }
                $data['title'] = 'Main';
                $data['content'] = 'main';
                $data['rootid'] = '';
                $data['menuid_selected'] = '';$this->load->model("utils");
                $data['autoload'] = '';
                $data['upload_file'] = false;
                $this->user->loaduserdata($data);
                // $this->session->set_userdata("canwrite", 1);
                $this->load->view('includes/template', $data);
        }
	}
    public function islogged(){
        return $this->session->userdata("logged_in");
    }
    public function loadloginform(){
        $this->load->view('login');
    }

    public function sessionchecker(){
        $result = 0;
        if(!$this->islogged()) $result = 1;
        echo "<user>
                <result>{$result}</result>
              </user>";
    }

    function suddenLogout(){
        $this->load->view('includes/logoutuser');
    }

    public function validate(){
        $toks = $this->input->post("toks");
        $username = $this->gibberish->decrypt( $this->input->post("fusername"), $toks );
        //CHECK USER STAT IF LOCK
        $lockedStat = $this->extras->getUserLockedStat($username);

        if($lockedStat == ""){
            echo "<user>
                    <result>0</result>
                    <message>Invalid username and password</message>
                  </user>";
        }elseif($lockedStat >= 5) {
            echo "<user>
                    <result>LOCKED</result>
                    <locked>5</locked>
                    <message>Your account has been locked.</message>
                  </user>";
        }else{
            $isvalidate = !$this->user->validate() ? 0 : 1;
            $loginTrail = array();
            $username = $this->gibberish->decrypt( $this->input->post("fusername"), $toks );
            $name = $this->extras->getAdminInfo($username);
            if (!$name) {
                $name = $this->extras->getAdminInfo($username);
                $username = $this->user->getUsernameByEmail($username);
            }
            $userid = $this->extras->getUserId($username);
            $lockedStat = $this->extras->getUserLockedStat($username);

            if ($isvalidate == 0) {
                $status = "failed";
                $lockedStat++;
                $this->extras->updateUserLockedStat($username, $lockedStat);
                if ($lockedStat == 5) {
                    $isExists = $this->extensions->checkUserForgotPass($username);
                    if ($isExists) {
                        $key = $this->extensions->generateRandomPassword(16);
                        $this->load->model("email");
                        $message = $this->getLockAccountEmail($key);
                        $requestTrail = array();
                        $requestTrail['userid'] = $username;
                        $requestTrail['key'] = $key;
                        $this->extras->insertRequestTrails($requestTrail);

                        $insertData['employeeid'] = $username;
                        $insertData['status'] = 'lock';

                        $this->extras->insertLockUnlockData($insertData);
                    
                        echo "<user>
                        <result>EMAILED</result>
                        <locked>{$lockedStat}</locked>
                        <message>Locked Email</message>
                      </user>";
                      $this->email->sendLockAccountEmail($message, $isExists['email']);
                      die;
                    }
                }
            }else{
                $status = "success";
                $lockedStat = 0;
                if ($lockedStat < 5) {
                    $this->extras->updateUserLockedStat($username, $lockedStat);
                }
            }

            $loginTrail['ip'] = $_SERVER['REMOTE_ADDR'];
            $loginTrail['status'] = $status;
            $loginTrail['username'] = $username;
            $loginTrail['userid'] = $userid;
            $loginTrail['mac'] = $this->extras->returnmacaddress($_SERVER['REMOTE_ADDR']);
            $loginTrail['name'] = ($name)? $name:"Not Registered User";
            $loginTrail['device'] = $_SERVER['HTTP_USER_AGENT'];
            // Insert Trail

            $this->extras->insertLoginTrails($loginTrail);

            $resulta = !$this->user->validate() ? "Invalid username and password" : "";
                echo "<user>
                        <result>{$isvalidate}</result>
                        <locked>{$lockedStat}</locked>
                        <message>{$resulta}</message>
                      </user>";
        }
    }

    public function site(){
        if(!$this->islogged()){            
            $this->loadloginform();
            return;
        }

        $data['title'] = $this->input->post("titlebar");
        $data['content'] = $this->input->post("sitename");
        $data['rootid'] = $this->input->post("rootid");
        $data['menuid_selected'] = $this->input->post("menuid");
        $data['autoload'] = '';
        $data['upload_file'] = "maintenance/idcapture"==$data['content'];
        
        $this->user->loaduserdata($data);
        $this->validateWriteAccess($this->input->post("menuid"));
        # $data['autoload'] = 'autofocusinput()';

        // echo "<pre>";print_r($data);die;
        if($data['menuid_selected'] <> "")
		  $this->load->view('includes/template', $data);
        else
          $this->index();
    }
    
    public function siteportion(){
        // $this->addRemainingSessions();
        $data = $this->input->post();
        ## employeeid
        $toks = $this->input->post('toks');

        if($toks){
            unset($data["toks"]);
            foreach($data as $key => $val){
                $data[$key] = $this->gibberish->decrypt($val, $toks);
            }
            $this->load->view($this->gibberish->decrypt( $this->input->post("view"), $toks ), $data);
        }
        else{
            $this->load->view($this->input->post("view"), $data);
        }
        
    }

    public function otherincome(){
        $view = $this->input->post("view");
        $this->load->view("employee/other_income");
        
    }

    public function signout(){
        $user = $this->session->userdata('username');
        // $this->db->query("update user_info set ipadd='' WHERE username='".$this->session->userdata('username')."'");
        $this->session->sess_destroy();

        $this->db->where('username', $user);
        $this->db->or_where('user_data', NULL);
        $this->db->or_where('username', NULL);
        $this->db->delete('ci_sessions');

        # echo site_url("main");
        echo base_url();
    }

    
    public function logout(){
        $this->db->query("update user_info set ipadd='' WHERE username='".$this->session->userdata('username')."'");
        $this->session->sess_destroy();
        $this->loadloginform();
    }
    public function uploadfile(){
        # print_r($_GET);
        $file_path = APPPATH.'config/'.ENVIRONMENT.'/database'.EXT;
        if ( ! file_exists($file_path)){
            log_message('debug', 'Database config for '.ENVIRONMENT.' environment is not found. Trying global config.');
            $file_path = APPPATH.'config/database'.EXT;
            if ( ! file_exists($file_path)){
                continue;
            }
        }
        include($file_path);
        $nconf = $db["default"];

        $this->load->library('lib_includer');
        $this->lib_includer->load("UploadHandler");
        $options = array(
            'delete_type' => 'DELETE',
            'db_host'     => $nconf['hostname'],
            'db_user'     => $nconf['username'],
            'db_pass'     => $nconf['password'],
            'db_name'     => $nconf['database'],
            'db_table'    => 'picture_upload',
            'db_path'     => site_url("inout/imageview?it=p&code="),
            'script_url'  => site_url("main/uploadfile"),
            'upload_type' =>'data',
            'accept_file_types' => '/\.(jpe?g|png)$/i'
        );
        $upload_handler = New UploadHandler($options);
    }

    public function listEmployees(){
        $thedept = $this->input->post("deptid");
        $list = $this->employee->listByDepartment($thedept);
        $response = array();
        $response["empdetail"] = array();

        // print("<pre>");
        // print_r($list);
        // print("</pre>");
        foreach ($list as $key => $value) {
            $item = array();
            $item["empnum"] = $value["employeeid"];
            $item["empfullname"] = $value["fullname"];
            $item["emptype"] = $value["typedesc"];
            $item["stattype"] = $value["statdesc"];
            array_push($response["empdetail"], $item);
        }
        print(json_encode($response));
    }

    public function aimsupdate(){
        $cadd = 0;
        $aexists = 0;
        $msg = "";
        
        $query = $this->db->query("SELECT employeeid,lname,fname,mname,gender,mobile,email,cityaddr,bdate,bplace FROM employee");
        foreach($query->result() as $row){
            $empid = $row->employeeid;
            $fcode = strtolower(str_replace(' ','',$row->fname)).".".strtolower($row->lname);
            $fullname = strtoupper($row->lname.", ".$row->fname." ".$row->mname);
            $aquery = $this->db->query("SELECT employeeid FROM StJude.tblFacultyProfile WHERE (employeeid='$empid' AND FCode='$fcode')");
            if($aquery->num_rows() == 0){
                $this->db->query("INSERT INTO StJude.tblFacultyProfile (FCode,employeeid,LName,FName,MName,Gender,Mobile,Email,CityAddr,BDate,BPlace) 
                                                VALUES 
                                                ('".$fcode."','".$empid."','".$row->lname."','".$row->fname."','".$row->mname."','".$row->gender."','".$row->mobile."','".$row->email."','".$row->cityaddr."','".$row->bdate."','".$row->bplace."') ");
                                                
                $salt = $this->user->salt(); 
                $pwrd = $this->user->hash("password", $salt);
                $this->db->query("INSERT INTO StJude.tblUserAcct (UserName,Password,UserType,Fullname,UserLevel,Salt) 
                                                VALUES 
                                                ('".$fcode."','".$pwrd."','3','".$fullname."','0','$salt')");
                $cadd++;
            }else   $aexists++;
        }
        echo $cadd." Record Added.. ".$aexists." Employee Already Exists";
    }

    public function checkAbsentNotificationMessage(){
        /*make the messagebox not appear again*/
        $data = array();
        $this->load->model('disciplinary_action');
        $disciplinary_action = $this->disciplinary_action->getOffenseHistory($this->session->userdata('username'),"NO")->num_rows();
        if(!$disciplinary_action) $this->session->set_userdata("message_box", "0");
        $start_date = date("Y-m-d", strtotime($this->extensions->getServerTime()));
        $end_date = '';
        $userid= $this->input->post('userid');
        $totaldates = array();
        $lacking_in_out = $days_half = $days_absent = 0;
        $alldates = array();
        $query = $this->db->query("SELECT CutoffFrom, CutoffTo FROM cutoff a INNER JOIN payroll_cutoff_config b ON b.baseid = a.ID WHERE '$start_date'  BETWEEN CutoffFrom AND CutoffTo")->result_array();
        if($query){
            foreach ($query as $key => $value) {
                if($start_date > $value['CutoffTo']){
                    $end_date = $value['CutoffFrom'];
                    $start_date = $value['CutoffTo'];
                }else{
                    $end_date = $value['CutoffFrom'];
                }
            }        
            if($this->extensions->getEmployeeTeachingType($userid) == "teaching"){
                $isBED = false;
                $bed_depts = Globals::getBEDDepartments();
                $deptid = $this->extensions->getEmployeeDeparment($userid);
                if(in_array($deptid, $bed_depts)) $isBED = true;
                $data = $this->attendance->computeEmployeeAttendanceSummaryTeaching($end_date,$start_date,$userid,"", $isBED);
                if(isset($data[21]) && $data[21]){
                    foreach($data[21] as $date_list){
                        if($date_list["absent"] <= 14400) $days_half += 1;
                        else $days_absent += 1;
                    }
                }
            }else{
                $data = $this->attendance->computeEmployeeAttendanceSummaryNonTeaching($end_date,$start_date,$userid);
                if(isset($data[17]) && $data[17]){
                    foreach($data[17] as $date_list){
                        if($date_list["absent"] <= 14400 && $date_list["absent"]) $days_half += 1;
                        if($date_list["absent"] > 14400) $days_absent += 1;
                    }
                }
            }
        }
        $data['disciplinary_action'] = $disciplinary_action;
        $data['absent'] = $days_absent;
        $data['half_day'] = $days_half;
        $data['islacking_in_out'] = $lacking_in_out;

        if(!$disciplinary_action){
            if($data['absent'] > 0) $this->load->view('includes/absentNotificationMessage', $data);
            else if($data['half_day'] > 0) $this->load->view('includes/absentNotificationMessage', $data);

            if($data['islacking_in_out'] > 0) $this->load->view('includes/absentNotificationMessage', $data);
        }
    }

    public function getTotalUnreadMessages(){
        $date_now = date("Y-m-d");
        /*kinopy ko lang yung mga codes ng main/site()*/
        $data['title'] = $this->input->post("titlebar");
        $data['content'] = $this->input->post("sitename");
        $data['rootid'] = $this->input->post("rootid");
        $data['menuid_selected'] = $this->input->post("menuid");

        /*for employee side, message_box*/
        // $data['message_box'] = $this->input->post("message_box");
        /*end*/
        $cutoff_query = $this->db->query("SELECT * FROM cutoff a
                          INNER JOIN payroll_cutoff_config b ON b.baseid = a.ID
                          WHERE '$date_now' 
                          BETWEEN b.`startdate`
                          AND b.`enddate`")->result_array();
        foreach($cutoff_query as $row){
            $data['startdate'] = $row['CutoffFrom'];
            $data['enddate'] = $date_now;
        }

        $data['autoload'] = '';
        $data['upload_file'] = "maintenance/idcapture"==$data['content'];
        $this->user->loaduserdata($data);
        # $data['autoload'] = 'autofocusinput()';
        if($data['menuid_selected'] <> "")
          $this->load->view('includes/template', $data);
        else
          $this->index();
    }

    public function googlelogin()
    {
    
        $clientId = '35598752410-fc4m7hmegq6lma4k0d2i9iiao26q8upf.apps.googleusercontent.com'; //Google client ID
        $clientSecret = 'AZ7-dhbIWw7LfZsSb0_d7RsM'; //Google client secret
        $redirectURL = base_url() .'index.php/main/login';
        
        //https://curl.haxx.se/docs/caextract.html

        //Call Google API
        $gClient = new Google_Client();
        $gClient->setApplicationName('Login');
        $gClient->setClientId($clientId);
        $gClient->setClientSecret($clientSecret);
        $gClient->setRedirectUri($redirectURL);
        $google_oauthV2 = new Google_Oauth2Service($gClient);
        
        if(isset($_GET['code']))
        {
            $gClient->authenticate($_GET['code']);
            $_SESSION['token'] = $gClient->getAccessToken();
            header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
        }

        if (isset($_SESSION['token'])) 
        {
            $gClient->setAccessToken($_SESSION['token']);
        }
        
        if ($gClient->getAccessToken()) {
            $userProfile = $google_oauthV2->userinfo->get();
            $this->session->set_userdata($userProfile);
        } 
        else 
        {
            $url = $gClient->createAuthUrl();
            header("Location: $url");
            exit;
        }
    }  

    public function isAccountExisting(){
        $response = array();
        $toks = $this->input->post("toks");
        $username = $this->gibberish->decrypt( $this->input->post("username"), $toks );
        $isExists = $this->extensions->checkUserForgotPass($username);
        if($isExists['email']){
                $key = $this->extensions->generateRandomPassword(16);
                $this->load->model("email");
                $message = $this->getEmailWithKey($key);
                $logs = $this->email->sendForgotPass($message, $isExists['email']);
                $this->extensions->forgotPassStatusKey($isExists['userid'], $key, "insert");

                $response['msg'] = "An email has been sent to ".$username.". Follow the directions in the email to reset your password.";
                $response['status'] = 1;
                echo json_encode($response); return;
        }else{
            $response['msg'] = "We couldn't find a portal account associated with ".$username.".";
            $response['status'] = 0;
        }

        echo json_encode($response);
    } 

    public function validateWriteAccess($menuid){
        $userid = $this->session->userdata("userid");
        // if($menuid == "63") $menuid = "110";
        $res = $this->menus->validateWriteAccess($userid, $menuid);
        if($res==0) $this->session->set_userdata("canwrite", 0);
        else $this->session->set_userdata("canwrite", 1);
    }

    public function google_login(){
        $clientId = '58425940075-6oqh64eehg3l7t77srhttm0tgj9ao68k.apps.googleusercontent.com'; //Google client ID
        $clientSecret = 'Xlz-imSDcHpxzLRDBGRM7J19'; //Google client secret
        $redirectURL = base_url() . 'Login/google_login';
        
        //Call Google API
        $gClient = new Google_Client();
        $gClient->setApplicationName('Login');
        $gClient->setClientId($clientId);
        $gClient->setClientSecret($clientSecret);
        $gClient->setRedirectUri($redirectURL);
        $google_oauthV2 = new Google_Oauth2Service($gClient);
        
        if(isset($_GET['code']))
        {
            $gClient->authenticate($_GET['code']);
            $_SESSION['token'] = $gClient->getAccessToken();
            header('Location: ' . filter_var($redirectURL, FILTER_SANITIZE_URL));
        }

        if (isset($_SESSION['token'])) 
        {
            $gClient->setAccessToken($_SESSION['token']);
        }
        
        if ($gClient->getAccessToken()) {
            $userProfile = $google_oauthV2->userinfo->get();
            if($userProfile["verified_email"]){
                $this->redirectUser($userProfile["email"]);
            }
        } 
        else 
        {
            $url = $gClient->createAuthUrl();
            header("Location: $url");
            exit;
        }
    }   

    public function redirectUser($email){
        $data["email"] = $email;
        $this->load->view("redirect_user", $data);
    }

    public function googleAuth(){
        $email = $this->input->post("email");
        $isvalidate = $this->user->validateGoogleAuth($email);
        if($isvalidate) echo true;
        else return false;
    }

    public function resetPass(){
        $response = array();
        $pass = $this->input->post('fpassword');
        $key = $this->input->post('key');
        $isExists = $this->extensions->checkUserForgotKey($key);
        if($isExists){
                $key = $this->user->updateUserPasswordByID($key, $isExists, md5($pass));
                // echo "<pre>"; print_r($this->db->last_query());die;
                $response['msg'] = "Your password has been successfully reset.";
                $response['status'] = 1;
                echo json_encode($response); return;
        }else{
            $response['msg'] = "This link has been used already please request again.";
            $response['status'] = 0;
        }

        echo json_encode($response);
    } 

    public function getEmailWithKey($key){
        $data["key"] = $key;
        return $this->load->view("email/forgotpassword", $data, TRUE);
    }

    public function getLockAccountEmail($key){
        $data["key"] = $key;
        return $this->load->view("email/lock_account", $data, TRUE);
    }

    public function loadForgotform(){
        $this->load->view("forgot");
    }

    public function loadunlockAccount(){
        
        $data["status"] = "";

        $key = $this->input->get("key");

        $status = $this->extras->getUnlockStatus($key);
        $userInfo = $this->extras->getUnlockUser($key);
        $accountStatus = $this->extras->getAccountStatus($userInfo);
        $timestamp = $this->extras->getUnlockTimeRequest($key);
        $serverTime = $this->extensions->getServerTime();

        $data["userinfo"] = $userInfo;
        $data["key"] = $key;

        $hourdiff = round((strtotime($serverTime) - strtotime($timestamp))/3600, 1);
     
        if ($accountStatus < 5) {
            $data["status"] = "Unlocked";
        }elseif($status == "READ"){
            $data["status"] = "Read";
        }elseif($hourdiff > 12 || $status == "EXPIRED"){
            $data["status"] = "Expired";
            $this->extras->updateLockedHistory("EXPIRED", $key);
        }else{
            $data["status"] = "Unlock";
        }

        $this->load->view("unlock_account", $data);
    }

    public function unlockAccount(){
        $toks = $this->input->post("toks");
        $username = $toks ? $this->gibberish->decrypt( $this->input->post("username"), $toks ) : $this->input->post("username") ; 
        $key = $toks ? $this->gibberish->decrypt( $this->input->post("key"), $toks ) : $this->input->post("key");
        $this->extras->updateUserLockedStat($username, 0);
        $this->extras->updateLockedHistory("READ", $key, $username);
        $insertData['updated_by'] =  $this->session->userdata("username");
        $insertData['employeeid'] = $username;
        $insertData['status'] = 'unlock';
        $this->extras->insertLockUnlockData($insertData);
        echo "success";
    }

    public function unlockResendAccount(){
        $username = $this->input->post("username");
        $isExists = $this->extensions->checkUserForgotPass($username);
        $name = $this->extras->getAdminInfo($username);
        if (!$name) {
            $username = $this->user->getUsernameByEmail($username);
        }
        if ($isExists) {
            $key = $this->extensions->generateRandomPassword(16);
            $this->load->model("email");
            $message = $this->getLockAccountEmail($key);
            $requestTrail = array();
            $requestTrail['userid'] = $username;
            $requestTrail['key'] = $key;
            $this->extras->readPastRequest($username);
            $this->extras->insertRequestTrails($requestTrail);
            $emailSend = $this->email->sendLockAccountEmail($message, $isExists['email']);
            if ($emailSend == "success") {
                echo "<user>
                <result>RESENT</result>
                <locked></locked>
                <message></message>
              </user>";
            }
        }
    }

     function checkRemainingSession(){
        $ip_address = $this->input->ip_address();
        $userid = $this->session->userdata("username");
        $session_time = $this->db->query("SELECT * FROM ci_sessions WHERE username = '$userid' AND ip_address = '$ip_address' LIMIT 1")->row()->last_activity;
        $to_time = $session_time;
        $from_time = strtotime(date("Y-m-d H:i:s"));
        echo round(abs($to_time - $from_time) / 60);
    }

    function addRemainingSessions(){
        $userid = $this->session->userdata("username");
        $date = $this->extensions->getServerTime();
        $currentDate = strtotime($date);
        $formatDate = date("Y-m-d H:i:s", $currentDate);
        $formatDate = strtotime($formatDate);
        $formatDate = $this->session->_get_time();
        $this->db->query("UPDATE ci_sessions SET last_activity = '$formatDate' WHERE username = '$userid' ");
    }

    function getWriteAccess(){
        $toks = $this->input->post("toks");
        $userid = $this->session->userdata("userid");
        $menuid = $this->gibberish->decrypt( $this->input->post("menuid"), $toks );
        echo $this->menus->validateWriteAccess($userid, $menuid);
    }
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */