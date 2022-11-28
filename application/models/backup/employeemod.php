<?php 
/**
 * @author Justin
 * @copyright 2016
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employeemod extends CI_Model {
    
    /*
     * Load Query
     */
    function displayleavetype($user = ""){
        $user = $user ? $user : $this->session->userdata("username");
        $query = $this->db->query("SELECT a.*, b.balance, b.credit, b.avail, b.leavetype,
                                    (SELECT SUM(balance) FROM employee_leave_credit c WHERE FIND_IN_SET(leavetype,'VL,EL') AND c.employeeid=b.employeeid AND CURRENT_DATE BETWEEN c.dfrom AND c.dto) AS VLELBAL 
                                    FROM code_request_form a
                                    INNER JOIN employee_leave_credit b ON a.code_request = b.leavetype
                                    WHERE ismain=1 AND b.employeeid='$user' AND CURRENT_DATE BETWEEN b.dfrom and b.dto");
        return $query;
    }
    function displayleavehistory($stat = ""){
        $user = $this->session->userdata("username");
        if($stat)   $wC = " AND status='$stat'";
        else        $wC = "";
        $query = $this->db->query("SELECT * FROM leave_app
                                    WHERE employeeid='$user' AND other != 'DA' $wC");
        return $query;
    }
    function displaybushistory($stat=""){
        $user = $this->session->userdata("username");
        $wC = "";
        if($stat)   $wC = " AND status='$stat'";
        $query = $this->db->query("SELECT * FROM leave_app WHERE employeeid='$user' AND other='DA' $wC");
        return $query;
    }
    function displayseminarhistory($category="",$dfrom="",$dto="",$stat=""){
        $user = $this->session->userdata("username");
        $wC   = "";
        if($category){   $wC = " AND a.status='$category'";     }
        
        $query = $this->db->query("SELECT a.*, b.timestamp as dateattached, b.status as attstat FROM seminar_app a LEFT JOIN seminar_app_attach b ON a.id = b.id WHERE a.employeeid='$user' $wC AND DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto'");
        return $query;
    }
    function displayschedrequesthistory($category="",$indi=""){
        $wC   = "";
        $user = $this->session->userdata("username");
        $deptcode = $this->employee->getHeadDeptCode($this->session->userdata('username'));
        if($category)   $wC = " AND status='$category'";
        if($indi){
            $wC .= " AND a.employeeid='".$this->session->userdata('username')."'";
        }else{
            if($deptcode)   $wC .= " AND FIND_IN_SET(deptid,'$deptcode') ";
        }
        
        $tbl = "change_schedule_request";
        if($this->employee->getClusterHead($user) && !$indi){
            $tbl = "change_schedule_request_chead";
            $wC = " AND a.head = '$user'";
        }   
        if($this->employee->getDHRCHead($user,"head",true) && !$indi){
           $tbl = "change_schedule_request_hrd";
            $wC = " AND a.head = '$user'";
        }  
                
        $query = $this->db->query("SELECT a.id,a.employeeid,timestamp,dateedit,status,isread FROM $tbl a INNER JOIN change_schedule b ON a.id = b.id INNER JOIN employee c ON a.employeeid = c.employeeid WHERE a.id <> '' $wC GROUP BY b.id");
        return $query;
    }
    function displayotrequest($dfrom="",$dto=""){
        $user = $this->session->userdata("username");
        $query = $this->db->query("SELECT * FROM overtime_app WHERE employeeid='$user'");
        return $query;
    }
    function loadleavestatus($category="",$dfrom="",$dto="",$dept="",$cnoti=""){
        $user = $this->session->userdata("username");
        $bfp = $this->employee->getBudgetFinPres($user);
        
        if($category)   $wC = " AND a.status='$category'";
        else            $wC = "";

        if($dept == "HR")   $tbl = "leave_app_hrd";
        else                $tbl = "leave_app_dhead";
        
        if($this->employee->getClusterHead($user))      $tbl = "leave_app_chead";
        if($this->employee->getUnivPhysician($user))    $tbl = "leave_app_uphy";
        if($bfp) $tbl = $bfp;

        if($cnoti){
        $query = $this->db->query("SELECT a.*, CONCAT(b.lname,', ',b.fname,' ',b.mname) as fullname, b.deptid FROM $tbl a
                                    LEFT JOIN employee b ON a.employeeid = b.employeeid 
                                    WHERE FIND_IN_SET('$user',a.head) AND a.status='PENDING' AND a.other<>'DA'");
        }else{
        $query = $this->db->query("SELECT a.*, CONCAT(b.lname,', ',b.fname,' ',b.mname) as fullname, b.deptid FROM $tbl a
                                    LEFT JOIN employee b ON a.employeeid = b.employeeid 
                                    WHERE FIND_IN_SET('$user',a.head) AND DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto' AND a.other<>'DA' $wC");
        }
                
        return $query;
    }
    function loadottatus($category="",$dfrom="",$dto="",$dept="",$cnoti=""){
        $user = $this->session->userdata("username");
        
        if($category)   $wC = " AND a.status='$category'";
        else            $wC = "";
        
        if($dept == "HR")   $tbl = "overtime_app_hrd";
        else                $tbl = "overtime_app_dhead";
        
        if($this->employee->getClusterHead($user))      $tbl = "overtime_app_chead";
        if($cnoti){
        $query = $this->db->query("SELECT a.*, CONCAT(b.lname,', ',b.fname,' ',b.mname) as fullname FROM $tbl a
                                    LEFT JOIN employee b ON a.employeeid = b.employeeid 
                                    WHERE FIND_IN_SET('$user',a.head) AND a.status='PENDING'");
        }else{
        $query = $this->db->query("SELECT a.*, CONCAT(b.lname,', ',b.fname,' ',b.mname) as fullname FROM $tbl a
                                    LEFT JOIN employee b ON a.employeeid = b.employeeid 
                                    WHERE FIND_IN_SET('$user',a.head) AND DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto' $wC");
        }         
        return $query;
    }
    
    function loadseminarstatus($category="",$dfrom="",$dto="",$cnoti=""){
        $user = $this->session->userdata("username");
        $ih   = "d.status";
        $dates = "";
        if($dfrom && $dto) $dates .= "AND DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto'";

        if($category){
            if($this->employee->getDHRCHead($user,"head") && $this->employee->getDHRCHead($user,"divisionhead") != $user && $this->employee->OffBusinessBudgetFinPres("budgetoff") != $user && $this->employee->OffBusinessBudgetFinPres("financedir") != $user)   $wC = " AND d.status='$category'";
            else   $wC = " AND a.status='$category'";
        }   else    $wC = "";
        if($this->employee->getDHRCHead($user,"head") && $this->employee->getDHRCHead($user,"divisionhead") != $user && $this->employee->OffBusinessBudgetFinPres("budgetoff") != $user && $this->employee->OffBusinessBudgetFinPres("financedir") != $user)    $ih = "a.status";    
        
        if($this->employee->getDHRCHead($user,"head")){
            if($user == $this->employee->getDHRCHead($user,"head"))         $tbl = "seminar_app_dhead";
        }            
        if($this->employee->getDHRCHead($user,"head",true)){
            if($user == $this->employee->getDHRCHead($user,"head",true))    $tbl = "seminar_app_hrd";
        }       
        if($this->employee->getDHRCHead($user,"divisionhead")){
            if($user == $this->employee->getDHRCHead($user,"divisionhead"))  $tbl = "seminar_app_chead";
        }
        if($this->employee->OffBusinessBudgetFinPres("budgetoff")){
            if($user == $this->employee->OffBusinessBudgetFinPres("budgetoff"))  $tbl = "seminar_app_budgetoff";
        }
        if($this->employee->OffBusinessBudgetFinPres("financedir")){
            if($user == $this->employee->OffBusinessBudgetFinPres("financedir")) $tbl = "seminar_app_findir";
        }
        if($this->employee->OffBusinessBudgetFinPres("president")){
            if($user == $this->employee->OffBusinessBudgetFinPres("president"))  $tbl = "seminar_app_president";
        }
        #if($this->employeemod->manageseminarnotif()->num_rows() && $this->employee->getDHRCHead($user,"head") && $this->employee->getDHRCHead($user,"divisionhead") != $user && $this->employee->OffBusinessBudgetFinPres("budgetoff") != $user && $this->employee->OffBusinessBudgetFinPres("financedir") != $user && !$wC){
        if($cnoti || $this->employee->getDHRCHead($user,"head") && $this->employee->getDHRCHead($user,"divisionhead") != $user && $this->employee->OffBusinessBudgetFinPres("budgetoff") != $user && $this->employee->OffBusinessBudgetFinPres("financedir") != $user && !$wC){
            $query = $this->db->query("SELECT a.*, CONCAT(b.lname,', ',b.fname,' ',b.mname) as fullname, c.points, c.timestamp as dateattached, c.status as attstat, $ih FROM $tbl a
                                    LEFT JOIN employee b ON a.employeeid = b.employeeid 
                                    LEFT JOIN seminar_app_attach c ON a.aid = c.id
                                    INNER JOIN seminar_app d ON a.aid = d.id 
                                    WHERE FIND_IN_SET('$user',a.head)  $wC");
        }else{
            $query = $this->db->query("SELECT a.*, CONCAT(b.lname,', ',b.fname,' ',b.mname) as fullname, c.points, c.timestamp as dateattached, c.status as attstat, a.status FROM $tbl a
                                        LEFT JOIN employee b ON a.employeeid = b.employeeid 
                                        LEFT JOIN seminar_app_attach c ON a.aid = c.id 
                                        INNER JOIN seminar_app d ON a.aid = d.id
                                        WHERE FIND_IN_SET('$user',a.head) $dates $wC");
        }
        return $query;
    }
    
    function loadseminarappdata($id="",$manage=false){
        $user = $this->session->userdata("username");
        $tbl  = "seminar_app";
        if($manage){
            if($this->employee->getDHRCHead($user,"head")){
                if($user == $this->employee->getDHRCHead($user,"head"))         $tbl = "seminar_app_dhead";
            }            
            if($this->employee->getDHRCHead($user,"head",true)){
                if($user == $this->employee->getDHRCHead($user,"head",true))    $tbl = "seminar_app_hrd";
            }       
            if($this->employee->getDHRCHead($user,"divisionhead")){
                if($user == $this->employee->getDHRCHead($user,"divisionhead")) $tbl = "seminar_app_chead";
            }
            if($this->employee->OffBusinessBudgetFinPres("budgetoff")){
                if($user == $this->employee->OffBusinessBudgetFinPres("budgetoff"))  $tbl = "seminar_app_budgetoff";
            }
            if($this->employee->OffBusinessBudgetFinPres("financedir")){
                if($user == $this->employee->OffBusinessBudgetFinPres("financedir")) $tbl = "seminar_app_findir";
            }
            if($this->employee->OffBusinessBudgetFinPres("president")){
                if($user == $this->employee->OffBusinessBudgetFinPres("president"))  $tbl = "seminar_app_president";
            }
        }
        $query = $this->db->query("SELECT *,c.description AS epos, d.description AS edept FROM $tbl a 
                                     LEFT JOIN employee b ON a.employeeid = b.employeeid
                                     LEFT JOIN code_position c ON b.positionid = c.positionid
                                     LEFT JOIN code_office d ON b.deptid = d.code 
                                     WHERE id='$id'");
        return $query;
    }
    
    function loadovertimeappdata($id="",$mng=false){
        $user = $this->session->userdata("username");
        $tbl  = "overtime_app";
        if(!$mng){
            if($this->employee->getDHRCHead($user,"head")){
                if($user == $this->employee->getDHRCHead($user,"head"))         $tbl = "overtime_app_dhead";
            }            
            if($this->employee->getDHRCHead($user,"head",true)){
                if($user == $this->employee->getDHRCHead($user,"head",true))    $tbl = "overtime_app_hrd";
            }       
        }
        $query = $this->db->query("SELECT * FROM $tbl WHERE id='$id'");
        return $query;
    }
    
    function loadoffbusstatus($category="",$dfrom="",$dto="",$dept="",$cnoti=""){
        $user = $this->session->userdata("username");
        $bfp = $this->employee->getBudgetFinPres($user);
        
        if($category)   $wC = " AND a.status='$category'";
        else            $wC = "";

        if($dept == "HR")   $tbl = "leave_app_hrd";
        else                $tbl = "leave_app_dhead";
        
        if($this->employee->getClusterHead($user))      $tbl = "leave_app_chead";
        if($this->employee->getUnivPhysician($user))    $tbl = "leave_app_uphy";
        if($bfp) $tbl = $bfp;

        if($cnoti){
        $query = $this->db->query("SELECT a.*, CONCAT(b.lname,', ',b.fname,' ',b.mname) as fullname, b.deptid FROM $tbl a
                                    LEFT JOIN employee b ON a.employeeid = b.employeeid 
                                    WHERE FIND_IN_SET('$user',a.head) AND a.status='PENDING' AND a.other='DA'");
        }else{
        $query = $this->db->query("SELECT a.*, CONCAT(b.lname,', ',b.fname,' ',b.mname) as fullname, b.deptid FROM $tbl a
                                    LEFT JOIN employee b ON a.employeeid = b.employeeid 
                                    WHERE FIND_IN_SET('$user',a.head) AND DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto' AND a.other='DA' $wC");
        }
        
        return $query;
    }
    
    function loadseminarapp_attachment($id=""){
        $user  = $this->session->userdata("username");
        $query = $this->db->query("SELECT a.*,b.points,b.timestamp as dateattached,b.status,a.hrdir FROM seminar_app a LEFT JOIN seminar_app_attach b ON a.id = b.id WHERE a.id='$id'");
        return $query;
    }
    
    function attendance_confirmation(){
        $return = "";
        $dfrom  = "";
        $dto    = "";
        $query = $this->db->query("SELECT * FROM cutoff WHERE CURRENT_DATE BETWEEN ConfirmFrom AND ConfirmTo");
        if($query->num_rows() > 0){
            $return  = "<b>Reminder : </b>Kindly confirm your attendance From <b><u>".date("F d, Y",strtotime($query->row(0)->CutoffFrom))."</u></b> to <b><u>".date("F d, Y",strtotime($query->row(0)->CutoffTo))."</u></b>. Failure to confirm your attendance within <b><u>".date("F d, Y",strtotime($query->row(0)->ConfirmFrom))."</u></b> to <b><u>".date("F d, Y",strtotime($query->row(0)->ConfirmTo))."</u></b> will be considered as confirmed.<br /><br />";
            $return .= "Click Here to view Attendance for this cut off: <a href='#' class='btn blue' id='viewcutoff'>View Cut-off</a>";
            $dfrom   = $query->row(0)->CutoffFrom;
            $dto     = $query->row(0)->CutoffTo;
        }
        return array($return,$dfrom,$dto);
    }
    
    function loadleaveCredsetup($data){
        $cred = 0;
        $query = $this->db->query("SELECT * FROM code_request_form WHERE code_request='{$data['ltype']}'");
        if($query->num_rows() > 0)  $cred = $query->row(0)->credits;
        return $cred;
    }    
    
    function loadleaveCred(){
        $query = $this->db->query("SELECT * FROM code_leave_setup ORDER BY timestamp DESC");
        return $query;
    }
    
    function load_leave_setup($id=""){
        $query = $this->db->query("SELECT * FROM code_leave_setup WHERE id='$id'");
        return $query;
    }
    
    function getLeaveHead($id=""){
        $query = $this->db->query("SELECT * FROM leave_app WHERE id='$id'");
        return $query;
    }
    
    function getSeminarHead($id=""){
        $query = $this->db->query("SELECT * FROM seminar_app WHERE id='$id'");
        return $query;
    }
    
    function getOvertimeHead($id=""){
        $query = $this->db->query("SELECT * FROM overtime_app WHERE id='$id'");
        return $query;
    }
    
    function getChangeSchedHead($id=""){
        $query = $this->db->query("SELECT * FROM change_schedule_request WHERE id='$id'");
        return $query;
    }
    
    function loadSchedday(){
        $query = $this->db->query("SELECT B.day_code,B.day_name, A.starttime,A.endtime,A.idx,A.tardy_start,A.absent_start,A.tardy_half_start,A.absent_half_start,A.no_schedule,A.half_schedule,A.early_dismissal from employee_schedule as A INNER join code_daysofweek AS B on A.dayofweek = B.day_code GROUP BY day_code ORDER BY idx")->result();                    
        return $query;
    }
    
    function loadschedreqdata($id=""){
        $query = $this->db->query("SELECT *,c.description AS epos, d.description AS edept FROM change_schedule_request a 
                                    LEFT JOIN employee b ON a.employeeid = b.employeeid 
                                    LEFT JOIN code_position c ON b.positionid = c.positionid
                                    LEFT JOIN code_office d ON b.deptid = d.code
                                    WHERE a.id='$id'");
        return $query;
    }
    function loadschedreqset($id=""){
        $query = $this->db->query("SELECT * FROM change_schedule a INNER JOIN code_daysofweek AS b ON a.dayofweek = b.day_code WHERE id='$id'");
        return $query;
    }
    
    // Notif
    function leavenotif(){
        $user  = $this->session->userdata("username");
        $query = $this->db->query("SELECT * FROM leave_app WHERE isread=0 AND other <> 'DA' AND employeeid='$user'");
        return $query; 
    }
    function seminarnotif(){
        $user  = $this->session->userdata("username");
        $query = $this->db->query("SELECT * FROM seminar_app WHERE isread=0 AND employeeid='$user'");
        return $query; 
    }
    function overtimenotif(){
        $user  = $this->session->userdata("username");
        $query = $this->db->query("SELECT * FROM overtime_app WHERE isread=0 AND employeeid='$user'");
        return $query; 
    }
    function offbusnotif(){
        $user  = $this->session->userdata("username");
        $query = $this->db->query("SELECT * FROM leave_app WHERE isread=0 AND other = 'DA' AND employeeid='$user'");
        return $query; 
    }
    function cschednotif(){
        $user  = $this->session->userdata("username");
        $query = $this->db->query("SELECT * FROM change_schedule_request WHERE isread=0 AND employeeid='$user'");
        return $query; 
    }
    function manageleavenotif(){
        $user = $this->session->userdata("username");
        $bfp = $this->employee->getBudgetFinPres($user);
        $tbl = "leave_app";
        if($this->employee->getDHRCHead($user,"head")){
            if($user == $this->employee->getDHRCHead($user,"head"))         $tbl = "leave_app_dhead";
        }            
        if($this->employee->getDHRCHead($user,"head",true)){
            if($user == $this->employee->getDHRCHead($user,"head",true))    $tbl = "leave_app_hrd";
        }       
        if($this->employee->getDHRCHead($user,"divisionhead")){
            if($user == $this->employee->getDHRCHead($user,"divisionhead")) $tbl = "leave_app_chead";
        }
        if($this->employee->getUnivPhysician($user))                        $tbl = "leave_app_uphy";
        if($bfp)                                                            $tbl = $bfp;    
         
        $query = $this->db->query("SELECT * FROM $tbl WHERE FIND_IN_SET('{$user}',head) AND other <> 'DA' AND status='PENDING'");
        return $query;
    }
    function manageseminarnotif($mng=false){
        $user = $this->session->userdata("username");
        $tbl = "seminar_app";
        if($this->employee->getDHRCHead($user,"head")){
            if($user == $this->employee->getDHRCHead($user,"head"))         $tbl = "seminar_app_dhead";
        }            
        if($this->employee->getDHRCHead($user,"head",true)){
            if($user == $this->employee->getDHRCHead($user,"head",true))    $tbl = "seminar_app_hrd";
        }       
        if($this->employee->getDHRCHead($user,"divisionhead")){
            if($user == $this->employee->getDHRCHead($user,"divisionhead")) $tbl = "seminar_app_chead";
        }
        if($this->employee->OffBusinessBudgetFinPres("budgetoff")){
            if($user == $this->employee->OffBusinessBudgetFinPres("budgetoff"))  $tbl = "seminar_app_budgetoff";
        }
        if($this->employee->OffBusinessBudgetFinPres("financedir")){
            if($user == $this->employee->OffBusinessBudgetFinPres("financedir")) $tbl = "seminar_app_findir";
        }
        if($this->employee->OffBusinessBudgetFinPres("president")){
            if($user == $this->employee->OffBusinessBudgetFinPres("president"))  $tbl = "seminar_app_president";
        }
        
        if($mng){
            if($this->employee->getDHRCHead($user,"head") && $this->employee->getDHRCHead($user,"divisionhead") != $user && $this->employee->OffBusinessBudgetFinPres("budgetoff") != $user && $this->employee->OffBusinessBudgetFinPres("financedir")  != $user)
            $query = $this->db->query("SELECT * FROM $tbl a INNER JOIN seminar_app b ON a.aid = b.id WHERE a.head='{$user}' AND b.status='APPROVED' AND isread=0");
            else
            $query = $this->db->query("SELECT * FROM $tbl a INNER JOIN seminar_app b ON a.aid = b.id WHERE a.head='{$user}' AND a.status='PENDING'");
        }else
            $query = $this->db->query("SELECT * FROM $tbl WHERE head='{$user}' AND status='APPROVED'");
        return $query; 
    }
    function manageovertimenotif(){
        $user = $this->session->userdata("username");
        $tbl = "overtime_app";                
        if($this->employee->getDHRCHead($user,"head")){
            if($user == $this->employee->getDHRCHead($user,"head"))         $tbl = "overtime_app_dhead";
        }            
        if($this->employee->getDHRCHead($user,"head",true)){
            if($user == $this->employee->getDHRCHead($user,"head",true))    $tbl = "overtime_app_hrd";
        }       
        if($this->employee->getDHRCHead($user,"divisionhead")){
            if($user == $this->employee->getDHRCHead($user,"divisionhead")) $tbl = "overtime_app_chead";
        }
        $query = $this->db->query("SELECT * FROM $tbl WHERE head='{$user}' AND status='PENDING'");
        return $query;
    }
    function manageoffbusnotif(){
        $user = $this->session->userdata("username");
        $tbl = "leave_app";
        if($this->employee->getDHRCHead($user,"head")){
            if($user == $this->employee->getDHRCHead($user,"head"))         $tbl = "leave_app_dhead";
        }            
        if($this->employee->getDHRCHead($user,"head",true)){
            if($user == $this->employee->getDHRCHead($user,"head",true))    $tbl = "leave_app_hrd";
        }       
        if($this->employee->getDHRCHead($user,"divisionhead")){
            if($user == $this->employee->getDHRCHead($user,"divisionhead")) $tbl = "leave_app_chead";
        }
        if($this->employee->getUnivPhysician($user))                        $tbl = "leave_app_uphy";
        if($this->employee->OffBusinessBudgetFinPres("budgetoff")){
            if($user == $this->employee->OffBusinessBudgetFinPres("budgetoff"))  $tbl = "leave_app_budgetoff";
        }
        if($this->employee->OffBusinessBudgetFinPres("financedir")){
            if($user == $this->employee->OffBusinessBudgetFinPres("financedir")) $tbl = "leave_app_financedir";
        }
        if($this->employee->OffBusinessBudgetFinPres("president")){
            if($user == $this->employee->OffBusinessBudgetFinPres("president"))  $tbl = "leave_app_president";
        }
        
        $query = $this->db->query("SELECT * FROM $tbl WHERE FIND_IN_SET('{$user}',head) AND other = 'DA' AND status='PENDING'");
        return $query;
    }
    function managecschednotif(){
        $user = $this->session->userdata("username");
        $tbl = "change_schedule_request";            
        if($this->employee->getDHRCHead($user,"divisionhead"))  if($user == $this->employee->getDHRCHead($user,"divisionhead")) $tbl = "change_schedule_request_chead";
        if($this->employee->getDHRCHead($user,"head",true))     if($user == $this->employee->getDHRCHead($user,"head",true))    $tbl = "change_schedule_request_hrd";
        
        $query = $this->db->query("SELECT * FROM $tbl WHERE FIND_IN_SET('{$user}',head) AND status='PENDING'");
        return $query;
    }
    function compensationhistory($user=""){
        $query = $this->db->query("SELECT * FROM payroll_employee_salary_history WHERE employeeid='$user'");
        return $query;
    }
    function empothincome($user=""){
        $query = $this->db->query("SELECT * FROM employee_income_oth a INNER JOIN payroll_income_oth_config b ON a.code_income = b.id WHERE employeeid='$user'");
        return $query;
    }
        
    /*
     * Display Options
     */
    function withPay($param=""){
        $arr = array("YES","NO");
        foreach($arr as $desc){
            if($param == $desc) $sel = " selected";
            else                $sel = "";
            $opt .= "<option value='$desc' $sel>$desc</option>";
        }
        return $opt;
    }
    function othLeave($type="",$isother=true){
        $opt = "<option value=''>Select Leave</option>";
        if($isother) $wC = " WHERE ismain=0";
        else         $wC = " WHERE ismain=1";
        $query = $this->db->query("SELECT * FROM code_request_form $wC");
        foreach($query->result() as $row){
            if($type == $row->code_request) $sel = " selected";
            else                            $sel = "";
            $opt .= "<option value='".$row->code_request."' $sel>".$row->description."</option>";
        }
        return $opt;
    }
    function displayCutOff(){
        $sel = "";
        $opt = "<option value=''>Select Cut-Off</option>";
        $query = $this->db->query("SELECT * FROM cutoff ORDER BY Timestamp DESC");
        foreach($query->result() as $row){
            $opt .= "<option value='".$row->CutoffFrom.",".$row->CutoffTo."' $sel>".date("F d, Y",strtotime($row->CutoffFrom))." - ".date("F d, Y",strtotime($row->CutoffTo))."</option>";
        }
        return $opt;
    }
    function getAppSequence($type = "",$deptid = "",$code = ""){
        
        $qdhead = $this->db->query("SELECT head,divisionhead FROM code_office WHERE code='$deptid'");
        $dhead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->head : "");                         // department head
        $chead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->divisionhead : "");                 // Cluster head
        $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
        $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                             // HR Director
        $qup    = $this->db->query("SELECT * FROM code_request_form WHERE (univphy<>'' OR univphyt<>'') AND code_request='$code'"); // University Physician
        $up     = ($qup->num_rows() > 0 ? $qup->row(0)->univphy.",".$qup->row(0)->univphyt : "");
        
        $seq = array();
        $lquery = $this->db->query("SELECT * FROM code_request_form WHERE code_request='{$type}'");
        if($lquery->num_rows()){
            $seq = array (
                            $lquery->row()->dhseq => array("type"=>"dh","tbl"=>"leave_app_dhead", "head"=> $dhead),
                            $lquery->row()->hhseq => array("type"=>"hh","tbl"=>"leave_app_hrd", "head"=> $hrd),
                            $lquery->row()->chseq => array("type"=>"ch","tbl"=>"leave_app_chead", "head"=> $chead),
                            $lquery->row()->upseq => array("type"=>"up","tbl"=>"leave_app_uphy", "head"=> $up),
                            $lquery->row()->boseq => array("type"=>"bo","tbl"=>"leave_app_budgetoff", "head"=> $lquery->row()->budgetoff),
                            $lquery->row()->fdseq => array("type"=>"fd","tbl"=>"leave_app_financedir", "head"=> $lquery->row()->financedir),
                            $lquery->row()->pseq  => array("type"=>"pt","tbl"=>"leave_app_president", "head"=> $lquery->row()->president)
                          );
            ksort($seq);
        }
        return $seq;
    }

    function getSeminarSequence($type = "",$deptid = "",$code = ""){
        
        $qdhead = $this->db->query("SELECT head,divisionhead FROM code_office WHERE code='$deptid'");
        $dhead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->head : "");                         // department head
        $chead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->divisionhead : "");                 // Cluster head
        $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
        $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                             // HR Director
        $qup    = $this->db->query("SELECT * FROM code_request_form WHERE (univphy<>'' OR univphyt<>'') AND code_request='$code'"); // University Physician
        $up     = ($qup->num_rows() > 0 ? $qup->row(0)->univphy.",".$qup->row(0)->univphyt : "");
        
        $seq = array();
        $lquery = $this->db->query("SELECT * FROM code_request_form WHERE code_request='{$type}'");
        if($lquery->num_rows()){
            $seq = array (
                            $lquery->row()->dhseq => array("type"=>"dh","tbl"=>"seminar_app_dhead", "head"=> $dhead),
                            $lquery->row()->hhseq => array("type"=>"hh","tbl"=>"seminar_app_hrd", "head"=> $hrd),
                            $lquery->row()->chseq => array("type"=>"ch","tbl"=>"seminar_app_chead", "head"=> $chead),
                            $lquery->row()->upseq => array("type"=>"up","tbl"=>"seminar_app_uphy", "head"=> $up),
                            $lquery->row()->boseq => array("type"=>"bo","tbl"=>"seminar_app_budgetoff", "head"=> $lquery->row()->budgetoff),
                            $lquery->row()->fdseq => array("type"=>"fd","tbl"=>"seminar_app_findir", "head"=> $lquery->row()->financedir),
                            $lquery->row()->pseq  => array("type"=>"pt","tbl"=>"seminar_app_president", "head"=> $lquery->row()->president)
                          );
            ksort($seq);
        }
        return $seq;
    }
    
    /*
     * Display Options Desc
     */
    function othLeaveDesc($type=""){
        $return = "";
        if($type)   $wC = " WHERE code_request='$type'";
        $query = $this->db->query("SELECT * FROM code_request_form $wC");
        foreach($query->result() as $row){
            $return = $row->description;
        }
        return $return;
    }
    
    /*
     * Save Data
     */
    function applyLeave($data){
        $return = ""; 
        $reason = $this->extras->clean($data['reason']);
        $eid    = $this->session->userdata("username");
        $data['datesetfrom']  = isset($data['datesetfrom']) ? $data['datesetfrom'] : "";
        $data['datesetto']    = isset($data['datesetto']) ? $data['datesetto'] : "";
        $tfrom  = isset($data['tfrom']) ? date("H:i:s",strtotime($data['tfrom'])) : "";
        $tto    = isset($data['tto']) ? date("H:i:s",strtotime($data['tto'])) : "";
        $dltype = isset($data['dltype']) ? $data['dltype'] : "";
        $user   = $this->session->userdata("username");
        $qdept  = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$user'");
        $deptid = ($qdept->num_rows() > 0 ? $qdept->row(0)->deptid : "");
        if($deptid){
            $qdhead = $this->db->query("SELECT head,divisionhead FROM code_office WHERE code='$deptid'");
            $dhead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->head : "");                         // department head
            $chead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->divisionhead : "");                 // Cluster head
            $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
            $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                             // HR Director
            $qup    = $this->db->query("SELECT * FROM code_request_form WHERE (univphy<>'' OR univphyt<>'') AND code_request='{$data['ltype']}'"); // University Physician
            $up     = ($qup->num_rows() > 0 ? $qup->row(0)->univphy.",".$qup->row(0)->univphyt : "");
            
            $continue = true;
            $msg      = "";
            if($data['ltype'] != "other"){
                $query  = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$eid' AND leavetype='{$data['ltype']}' AND ('{$data['datesetfrom']}' BETWEEN dfrom AND dto OR '{$data['datesetto']}' BETWEEN dfrom AND dto)");
                if($query->num_rows() > 0){  
                    $lbal = $query->row(0)->balance;
                    if($data['ltype'] == "VL"){
                        $qbal = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$eid' AND leavetype='EL' AND ('{$data['datesetfrom']}' BETWEEN dfrom AND dto OR '{$data['datesetto']}' BETWEEN dfrom AND dto)");
                        if($qbal->num_rows() > 0){
                            if($qbal->row(0)->balance <= 0 && $lbal <= 0){
                                $continue = false;
                                $msg      = "You have no remaining leave balance..";    
                            }
                        }                 
                    }else{
                        if($lbal <= 0){   
                            $continue = false;
                            $msg      = "You have no remaining leave balance..";
                        }
                    }
                }else    
                    $continue = false;
            }
                
            $query = $this->db->query("SELECT * FROM leave_app WHERE employeeid='$eid' AND status='APPROVED' AND ('{$data['datesetfrom']}' BETWEEN datefrom AND dateto) OR ({$data['datesetto']} BETWEEN datefrom AND dateto)");
            if($query->num_rows() > 0){
                $continue = false;
                $msg = "The date you applied is already approved"; 
            }
            $seq = $this->getAppSequence($data['ltype'], $deptid, $data['ltype']);                                                                                                     
            
            // Vacation, Emergency, Sick & Other Leave
            if(in_array($data['ltype'],array("VL","EL","SL","other")) && $continue){
                if($dltype)
                    $ins   = $this->db->query("INSERT INTO leave_app (employeeid,type,other,othertype,paid,datefrom,dateto,timefrom,timeto,nodays,reason,depthead,hrdir,univphy,clusterhead) VALUES ('{$user}','{$data['ltype']}','{$data['othleave']}','$dltype','{$data['withpay']}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$reason}','{$dhead}','{$hrd}','{$up}','{$chead}')");
                else
                    $ins   = $this->db->query("INSERT INTO leave_app (employeeid,type,other,paid,datefrom,dateto,nodays,reason,depthead,hrdir,univphy,clusterhead) VALUES ('{$user}','{$data['ltype']}','{$data['othleave']}','{$data['withpay']}','{$data['datesetfrom']}','{$data['datesetto']}','{$data['ndays']}','{$reason}','{$dhead}','{$hrd}','{$up}','{$chead}')");
                if($ins){
                    if($dltype) $wC = " AND timefrom='{$tfrom}' AND timeto='{$tto}'";
                    else        $wC = " AND datefrom='{$data['datesetfrom']}' AND dateto='{$data['datesetto']}'";
                    $qid   = $this->db->query("SELECT id FROM leave_app WHERE employeeid='$user' AND type='{$data['ltype']}' $wC  AND nodays='{$data['ndays']}' AND depthead='{$dhead}' AND hrdir='{$hrd}' AND reason='{$reason}'");
                    $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);
                        
                    if($dltype)
                        $query = $this->db->query("INSERT INTO ".$seq[1]["tbl"]." (aid,employeeid,head,type,other,othertype,paid,datefrom,dateto,timefrom,timeto,nodays,reason,status) VALUES ('$aid','{$user}','{$seq[1]["head"]}','{$data['ltype']}','{$data['othleave']}','$dltype','{$data['withpay']}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','$reason','PENDING')");
                    else
                        $query = $this->db->query("INSERT INTO ".$seq[1]["tbl"]." (aid,employeeid,head,type,other,paid,datefrom,dateto,nodays,reason,status) VALUES ('$aid','{$user}','{$seq[1]["head"]}','{$data['ltype']}','{$data['othleave']}','{$data['withpay']}','{$data['datesetfrom']}','{$data['datesetto']}','{$data['ndays']}','$reason','PENDING')");
                    if($query)  $return = "Application Sent!.";
                    else        $return = "Ooops, Failed!";
                }
            }else{
                if(!$continue && $msg)  $return = $msg;
                else                    $return = "Leave application is failed!. ";
            }
            
        }else   $return = "Failed to apply leave. Please set your department first.";
        return $return;
    }

    //033117
   function applyLeaveWithSequence($data){
        $deptid = $return = ""; 
        $reason = $this->extras->clean($data['reason']);
        $eid    = $this->session->userdata("username");
        $data['datesetfrom']  = isset($data['datesetfrom']) ? $data['datesetfrom'] : "";
        $data['datesetto']    = isset($data['datesetto']) ? $data['datesetto'] : "";
        $tfrom  = isset($data['tfrom']) ? date("H:i:s",strtotime($data['tfrom'])) : "";
        $tto    = isset($data['tto']) ? date("H:i:s",strtotime($data['tto'])) : "";
        $dltype = isset($data['dltype']) ? $data['dltype'] : "";
        $user   = $this->session->userdata("username");
        $ltype  = isset($data['ltype']) ? $data['ltype'] : "";

        $continue = true;
        $msg      = "";
        if($data['ltype'] != "other"){
            $query  = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$eid' AND leavetype='{$data['ltype']}' AND ('{$data['datesetfrom']}' BETWEEN dfrom AND dto OR '{$data['datesetto']}' BETWEEN dfrom AND dto)");
            if($query->num_rows() > 0){
                $lbal = $query->row(0)->balance;
                if($data['ltype'] == "VL"){
                    $qbal = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$eid' AND leavetype='EL' AND ('{$data['datesetfrom']}' BETWEEN dfrom AND dto OR '{$data['datesetto']}' BETWEEN dfrom AND dto)");
                    if($qbal->num_rows() > 0){
                        if($qbal->row(0)->balance <= 0 && $lbal <= 0){
                            $continue = false;
                            $msg      = "You have no remaining leave balance..";    
                        }
                    }                 
                }else{
                    if($lbal <= 0){   
                        $continue = false;
                        $msg      = "You have no remaining leave balance..";
                    }
                }
            }else    
                $continue = false;
        }

        if($continue){
            $query = $this->db->query("SELECT * FROM leave_app WHERE employeeid='$eid' AND status='APPROVED' AND ('{$data['datesetfrom']}' BETWEEN datefrom AND dateto) OR ({$data['datesetto']} BETWEEN datefrom AND dateto)");
            if($query->num_rows() > 0){
                $continue = false;
                $msg = "The date you applied is already approved"; 
            }
        }else return $msg;

        if($continue){
            #get data from code_request_from for list of approval based on leave type setup
            $leave_setup_data = array();
            if(!$dltype) $leave_setup_data = $this->db->query("SELECT * FROM code_request_form WHERE code_request='$ltype'")->result_array();
            else        $leave_setup_data = $this->db->query("SELECT * FROM code_request_form WHERE code_request='{$data['othleave']}'")->result_array();
            // echo '<pre>'.print_r($leave_setup_data);die;

            $dhead = $hrd = $chead = $up = $budgetOff = $financeDir = $president = "";
// echo "<pre>";print_r($leave_setup_data);die;
            foreach ($leave_setup_data as $key => $setup) {
                if($setup['dhseq'] != "0"){ 
                    $qdept  = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$user'");
                    $deptid = ($qdept->num_rows() > 0 ? $qdept->row(0)->deptid : "");
                    if($deptid){
                        $qdhead = $this->db->query("SELECT head,divisionhead FROM code_office WHERE code='$deptid'");
                        $dhead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->head : "");                         // department head
                        $chead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->divisionhead : "");                 // Cluster head
                    }
                }

                if($setup['hhseq'] != "0"){
                    $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
                    $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                             // HR Director
                } 

                if($setup['chseq'] == "0"){ 
                    $chead = "";
                }

                if($setup['upseq'] != "0"){ 
                    $qup    = $this->db->query("SELECT * FROM code_request_form WHERE (univphy<>'' OR univphyt<>'') AND code_request='$ltype'"); // University Physician
                    $up     = ($qup->num_rows() > 0 ? $qup->row(0)->univphy.",".$qup->row(0)->univphyt : "");
                }

                if($setup['boseq'] != "0") $budgetOff      = $setup['budgetoff'];
                if($setup['fdseq'] != "0") $financeDir     = $setup['financedir'];
                if($setup['pseq'] != "0")  $president      = $setup['president'];

                // $seq = $this->getAppSequence($data['ltype'], $deptid, $data['ltype']); 
                #condition for sequence
                $tbl = "";
                $tblhead = "";
                if($setup['dhseq'] == "1"){      $tbl = "leave_app_dhead";       $tblhead = $dhead;
                }elseif($setup['hhseq'] == "1"){  $tbl = "leave_app_hrd";         $tblhead = $hrd;
                }elseif($setup['chseq'] == "1"){  $tbl = "leave_app_chead";       $tblhead = $chead;
                }elseif($setup['upseq'] == "1"){  $tbl = "leave_app_uphy";        $tblhead = $up;
                }elseif($setup['boseq'] == "1"){  $tbl = "leave_app_budgetoff";   $tblhead = $budgetOff;
                }elseif($setup['fdseq'] == "1"){  $tbl = "leave_app_financedir";  $tblhead = $financeDir;
                }elseif($setup['pseq'] == "1"){   $tbl = "leave_app_president";   $tblhead = $president;}


                // Vacation, Emergency, Sick & Other Leave
                if(in_array($data['ltype'],array("VL","EL","SL","other")) && $continue){
                    if($dltype)
                        $ins   = $this->db->query("
                                INSERT INTO leave_app (employeeid,type,other,othertype,paid,datefrom,dateto,timefrom,timeto,nodays,reason,depthead,hrdir,univphy,clusterhead,budgetoff,financedir,president,dhseq,hhseq,chseq,upseq,boseq,fdseq,pseq) 
                                VALUES ('{$user}','{$data['ltype']}','{$data['othleave']}','$dltype','{$data['withpay']}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$reason}','{$dhead}','{$hrd}','{$up}','{$chead}','{$budgetOff}','{$financeDir}','{$president}','{$setup['dhseq']}','{$setup['hhseq']}','{$setup['chseq']}','{$setup['upseq']}','{$setup['boseq']}','{$setup['fdseq']}','{$setup['pseq']}')
                                ");
                    else
                        $ins   = $this->db->query("
                                INSERT INTO leave_app (employeeid,type,other,paid,datefrom,dateto,nodays,reason,depthead,hrdir,univphy,clusterhead,budgetoff,financedir,president,dhseq,hhseq,chseq,upseq,boseq,fdseq,pseq) 
                                VALUES ('{$user}','{$data['ltype']}','{$data['othleave']}','{$data['withpay']}','{$data['datesetfrom']}','{$data['datesetto']}','{$data['ndays']}','{$reason}','{$dhead}','{$hrd}','{$up}','{$chead}','{$budgetOff}','{$financeDir}','{$president}','{$setup['dhseq']}','{$setup['hhseq']}','{$setup['chseq']}','{$setup['upseq']}','{$setup['boseq']}','{$setup['fdseq']}','{$setup['pseq']}')
                            ");
                    if($ins){
                        if($dltype) $wC = " AND timefrom='{$tfrom}' AND timeto='{$tto}'";
                        else        $wC = " AND datefrom='{$data['datesetfrom']}' AND dateto='{$data['datesetto']}'";
                        $qid   = $this->db->query("SELECT id FROM leave_app WHERE employeeid='$user' AND type='{$data['ltype']}' $wC  AND nodays='{$data['ndays']}' AND depthead='{$dhead}' AND hrdir='{$hrd}' AND reason='{$reason}'");
                        $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);
                            
                        if($dltype)
                            $query = $this->db->query("INSERT INTO $tbl (aid,employeeid,head,type,other,othertype,paid,datefrom,dateto,timefrom,timeto,nodays,reason,status) VALUES ('$aid','{$user}','$tblhead','{$data['ltype']}','{$data['othleave']}','$dltype','{$data['withpay']}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','$reason','PENDING')");
                        else
                            $query = $this->db->query("INSERT INTO $tbl (aid,employeeid,head,type,other,paid,datefrom,dateto,nodays,reason,status) 
                                                        VALUES ('$aid','{$user}','$tblhead','{$data['ltype']}','{$data['othleave']}','{$data['withpay']}','{$data['datesetfrom']}','{$data['datesetto']}','{$data['ndays']}','$reason','PENDING')");
                        if($query)  $return = "Application Sent!.";
                        else        $return = "Ooops, Failed!";
                    }
                }else{
                    if(!$continue && $msg)  $return = $msg;
                    else                    $return = "Leave application is failed!. ";
                }
            }
        }else{
            if($msg) $return = $msg;
            else $return = "Failed to apply leave. Please set your department first.";
        }
        return $return;
   }
    function leave_approve_head($data){
        $return = $msg = "";
        if($data['id']){
            $id     = $data['id'];
            $aid    = $data['aid'];
            $status = $data['status'];
            $dept   = $data['dept'];
            $edept  = $data['eddept'];
            $eid    = $data['eid'];
            $ltype  = $data['ltype'];
            $dltype  = isset($data['dltype'])?$data['dltype']:"";
            $othleave  = isset($data['othleave'])?$data['othleave']:"";
            $cdate  = date("Y-m-d");
            
            $seq = array();
            if(!$dltype) $seq = $this->getAppSequence($data['ltype'],$edept,$ltype);
            else        $seq = $this->getAppSequence($othleave,$edept,$othleave);
            
            // echo "<pre>";print_r($seq);
            for($seqno = 1; $seqno<= count($seq); $seqno++){
                if(in_array($this->session->userdata("username"), explode(",",$seq[$seqno]["head"]))){
                    $tbl                = $seq[$seqno]["tbl"];
                    $head_col_status    = ($seq[$seqno]["type"] == "dh" ? "deptheadstatus" : 
                                            ($seq[$seqno]["type"] == "hh" ? "hrdirstatus" : 
                                            ($seq[$seqno]["type"] == "ch" ? "clusterheadstatus" : 
                                            ($seq[$seqno]["type"] == "up" ? "univphystatus" :
                                            ($seq[$seqno]["type"] == "bo" ? "budgetoffstatus" :
                                            ($seq[$seqno]["type"] == "fd" ? "financedirstatus" :
                                            ($seq[$seqno]["type"] == "pt" ? "presidentstatus" :
                                             "")))))));
                    $head_col_date      = ($seq[$seqno]["type"] == "dh" ? "deptheaddate" : 
                                            ($seq[$seqno]["type"] == "hh" ? "hrdirdate" : 
                                            ($seq[$seqno]["type"] == "ch" ? "clusterheaddate" : 
                                            ($seq[$seqno]["type"] == "up" ? "univphydate" : 
                                            ($seq[$seqno]["type"] == "bo" ? "budgetoffdate" :
                                            ($seq[$seqno]["type"] == "fd" ? "financedirdate" :
                                            ($seq[$seqno]["type"] == "pt" ? "presidentdate" :
                                            "")))))));
                    $seqno += 1;
                    break;
                }             
            }  
            // Vacation, Emergency, Sick & Other Leave
            if(in_array($ltype,array("VL","EL","SL","other"))){
                if($seqno == count($seq)){
                    if($status == "APPROVED"){
                        $this->db->query("INSERT INTO leave_request (aid,employeeid,leavetype,other,othertype,paid,fromdate,todate,timefrom,timeto,no_days,remarks,status,dateapplied,dateapproved)
                                            (SELECT aid,employeeid,type,other,othertype,paid,datefrom,dateto,timefrom,timeto,nodays,reason,'$status',DATE(timestamp),DATE(NOW()) FROM $tbl WHERE id='$id')  
                                         ");
                        if($ltype != "other"){
                            $query  = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$eid' AND leavetype='$ltype' AND CURRENT_DATE BETWEEN dfrom AND dto");
                            if($query->num_rows() > 0){
                                $bal = $query->row(0)->balance;
                                if($ltype == "VL"){
                                    if($query->row(0)->balance > 0)
                                        $this->db->query("UPDATE employee_leave_credit SET avail='".($query->row(0)->avail+1)."', balance='".($query->row(0)->balance-1)."' WHERE employeeid='$eid' AND leavetype='$ltype' AND CURRENT_DATE BETWEEN dfrom AND dto");
                                    else{
                                        $avail = ($query->row(0)->avail+1);
                                        $query  = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$eid' AND leavetype='EL' AND CURRENT_DATE BETWEEN dfrom AND dto");
                                        if($query->num_rows() > 0){
                                            if($query->row(0)->balance > 0){
                                                $this->db->query("UPDATE employee_leave_credit SET avail='$avail' WHERE employeeid='$eid' AND leavetype='$ltype' AND CURRENT_DATE BETWEEN dfrom AND dto");
                                                $this->db->query("UPDATE employee_leave_credit SET balance='".($query->row(0)->balance-1)."' WHERE employeeid='$eid' AND leavetype='EL' AND CURRENT_DATE BETWEEN dfrom AND dto");
                                            }else{
                                                $msg = "This employee have no remaining balance";
                                            }
                                        }
                                    }
                                }else{
                                    if($bal > 0)$this->db->query("UPDATE employee_leave_credit SET avail='".($query->row(0)->avail+1)."', balance='".($query->row(0)->balance-1)."' WHERE employeeid='$eid' AND leavetype='$ltype' AND CURRENT_DATE BETWEEN dfrom AND dto");
                                    else        $msg = "This employee have no remaining balance";
                                }                          
                            }                                              
                        }
                    }
                    if(!$msg){
                                    $this->db->query("UPDATE $tbl SET status='$status', dateapproved='$cdate' WHERE id='$id'");  
                        $query  =   $this->db->query("UPDATE leave_app SET status='$status', $head_col_status='$status', $head_col_date='$cdate', isread=0 WHERE id='$aid'");
                        
                    }
                }else{
                    if($status == "APPROVED"){
                        $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
                        $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                         // department head
                        $this->db->query("INSERT INTO ".$seq[$seqno]["tbl"]." (aid,employeeid,head,type,other,othertype,paid,datefrom,dateto,timefrom,timeto,nodays,reason,status,timestamp)
                                        (SELECT aid,employeeid,'{$seq[$seqno]["head"]}',type,other,othertype,paid,datefrom,dateto,timefrom,timeto,nodays,reason,status,timestamp FROM $tbl WHERE id='$id')  
                                        ");
                    }
                        $this->db->query("UPDATE $tbl SET status='$status', dateapproved='$cdate' WHERE id='$id'");
                    if($status == "DISAPPROVED"){
                        $query  = $this->db->query("UPDATE leave_app SET status='$status', $head_col_status='$status', $head_col_date='$cdate', isread=0 WHERE id='$aid'");
                    }else
                        $query  = $this->db->query("UPDATE leave_app SET $head_col_status='$status', $head_col_date='$cdate' WHERE id='$aid'");
                }
            }
            
            if($query)  $return = "Successfully $status!.";  
            else        $return = "Failed!.";
            if($msg)    $return = $msg;
        }
        return $return;
    }
    function modifyLeave($data){
        $return = $msg = "";
        $continue = true;
        $eid     = $data['eid'];
        if($data['ltype'] != "other"){
            $query  = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$eid' AND leavetype='{$data['ltype']}' AND ('{$data['datesetfrom']}' BETWEEN dfrom AND dto OR '{$data['datesetto']}' BETWEEN dfrom AND dto)");
            if($query->num_rows() > 0){  
                $lbal = $query->row(0)->balance;
                if($data['ltype'] == "VL"){
                    $qbal = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$eid' AND leavetype='EL' AND ('{$data['datesetfrom']}' BETWEEN dfrom AND dto OR '{$data['datesetto']}' BETWEEN dfrom AND dto)");
                    if($qbal->num_rows() > 0){
                        if($qbal->row(0)->balance <= 0 && $lbal <= 0){
                            $continue = false;
                            $msg      = "You have no remaining leave balance..";    
                        }
                    }                 
                }else{
                    if($lbal <= 0){   
                        $continue = false;
                        $msg      = "You have no remaining leave balance..";
                    }
                }
            }else{
                $continue = false;
                $msg = "You have no credit for this type of leave.";
            }
        }
        $query = $this->db->query("SELECT * FROM leave_app WHERE employeeid='$eid' AND status='APPROVED' AND ('{$data['datesetfrom']}' BETWEEN datefrom AND dateto) OR ({$data['datesetto']} BETWEEN datefrom AND dateto)");
        if($query->num_rows() > 0){
            $continue = false;
            $msg = "The date you applied is already approved"; 
        }
        if($continue){
            $query = $this->db->query("SELECT * FROM leave_app WHERE (deptheadstatus='APPROVED' OR deptheadstatus='DISAPPROVED') WHERE id='{$data['id']}'");
            if($query->num_rows() > 0){
                $return = "Failed to update!. The request has already ".$query->row()->deptheadstatus;
            }else{
                $query = $this->db->query("UPDATE leave_app       SET type='{$data['ltype']}', other='{$data['othleave']}', paid='{$data['withpay']}', datefrom='{$data['datesetfrom']}', dateto='{$data['datesetto']}', nodays='{$data['ndays']}', reason='{$data['reason']}' WHERE id='{$data['id']}'");        
                         $this->db->query("UPDATE leave_app_dhead SET type='{$data['ltype']}', other='{$data['othleave']}', paid='{$data['withpay']}', datefrom='{$data['datesetfrom']}', dateto='{$data['datesetto']}', nodays='{$data['ndays']}', reason='{$data['reason']}' WHERE aid='{$data['id']}'");
                         $this->db->query("UPDATE leave_app_chead SET type='{$data['ltype']}', other='{$data['othleave']}', paid='{$data['withpay']}', datefrom='{$data['datesetfrom']}', dateto='{$data['datesetto']}', nodays='{$data['ndays']}', reason='{$data['reason']}' WHERE aid='{$data['id']}'");
                         $this->db->query("UPDATE leave_app_uphy  SET type='{$data['ltype']}', other='{$data['othleave']}', paid='{$data['withpay']}', datefrom='{$data['datesetfrom']}', dateto='{$data['datesetto']}', nodays='{$data['ndays']}', reason='{$data['reason']}' WHERE aid='{$data['id']}'");
                         $this->db->query("UPDATE leave_app_hrd   SET type='{$data['ltype']}', other='{$data['othleave']}', paid='{$data['withpay']}', datefrom='{$data['datesetfrom']}', dateto='{$data['datesetto']}', nodays='{$data['ndays']}', reason='{$data['reason']}' WHERE aid='{$data['id']}'");
                $return = "Successfully Saved!";
            }
        }else{
            $return = $msg;
        }
        return $return;
    }
    function delLeave($data){
        $return = "";
        $query = $this->db->query("SELECT * FROM leave_app WHERE (deptheadstatus='APPROVED' OR deptheadstatus='DISAPPROVED') WHERE id='{$data['id']}'");
        if($query->num_rows() > 0){
            $return = "Failed to delete!. The request has already ".$query->row()->deptheadstatus;
        }else{
            $this->db->query("DELETE FROM leave_app       WHERE id ='{$data['id']}'");
            $this->db->query("DELETE FROM leave_app_dhead WHERE aid='{$data['id']}'");
            $this->db->query("DELETE FROM leave_app_chead WHERE aid='{$data['id']}'");
            $this->db->query("DELETE FROM leave_app_uphy  WHERE aid='{$data['id']}'");
            $this->db->query("DELETE FROM leave_app_hrd   WHERE aid='{$data['id']}'");
            $return = "Succesfully Deleted!.";
        }
        return $return;    
    }
    function leave_modify_query($id=""){
        $query = $this->db->query("SELECT * FROM leave_app WHERE id='$id'");
        return $query;
    }
    function applySeminar($data){
        $return = "";
        if(isset($data['employeeid']))
            $user   = $data['employeeid'];
        else
            $user   = $this->session->userdata("username"); 
        $tfrom  = date("H:i:s",strtotime($data['tfrom']));
        $tto    = date("H:i:s",strtotime($data['tto']));
        $poa    = $this->extras->clean($data['poa']);
        $course = $this->extras->clean($data['course']);
        $venue  = $this->extras->clean($data['venue']);
        $speaker= $this->extras->clean($data['speaker']);
        $misc   = $this->extras->clean($data['miscellaneous']);
        $soc    = $this->extras->clean($data['soc']);
        
        $qdept  = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$user'");
        $deptid = ($qdept->num_rows() > 0 ? $qdept->row(0)->deptid : "");
        
        if($deptid){
            $qdhead = $this->db->query("SELECT head,divisionhead FROM code_office WHERE code='$deptid'");
            $dhead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->head : "");                         // department head
            $chead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->divisionhead : "");                 // Cluster head
            $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
            $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                             // HR Director
            $qs     = $this->db->query("SELECT * FROM code_request_form WHERE ismain='3'");
            $boff   = ($qs->num_rows() > 0 ? $qs->row(0)->budgetoff : "");                             // Budget Officer
            $fdir   = ($qs->num_rows() > 0 ? $qs->row(0)->financedir : "");                            // Finance Director
            $pres   = ($qs->num_rows() > 0 ? $qs->row(0)->president : "");                             // President
                        
            if($user == $dhead){    // Dean/Head Seminar Application
                if(!empty($chead)){
                    $eid = $data['eid'];
                    $qbid   = $this->db->query("SELECT baseid FROM based_id_offbus");
                    $bid    = $qbid->row(0)->baseid;
                              $this->db->query("UPDATE based_id_offbus SET baseid='".($bid+1)."'");
                    foreach(explode(",",$eid) as $empid){
                        #if($empid != $user) $isread = 0;
                        #else                
                        $isread = 1;                        
                        $ins   = $this->db->query("INSERT INTO seminar_app (employeeid,base_id,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,depthead,deptheadstatus,deptheaddate,hrdir,cluster,budgetoff,financedir,president,isread) VALUES ('{$empid}','{$bid}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}','{$dhead}','APPROVED',CURRENT_DATE,'{$hrd}','{$chead}','{$boff}','{$fdir}','{$pres}','{$isread}')");
                        if($ins){
                            $qid   = $this->db->query("SELECT id FROM seminar_app WHERE employeeid='$empid' AND dfrom='{$data['datesetfrom']}' AND dto='{$data['datesetto']}' AND tstart='{$tfrom}' AND tend='{$tto}' AND nodays='{$data['ndays']}' AND purpose='{$poa}' AND course='{$course}' AND venue='{$venue}' AND statement='{$soc}'");
                            $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);
                                    
                            $query = $this->db->query("INSERT INTO seminar_app_dhead (aid,base_id,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,status,dateapproved,dateapplied) VALUES ('$aid','{$bid}','{$empid}','{$dhead}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}','APPROVED',NOW(),NOW())");
                            $query = $this->db->query("INSERT INTO seminar_app_hrd (aid,base_id,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied) VALUES ('$aid','{$bid}','{$empid}','{$hrd}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}',NOW())");                                                                                    
                        }
                    }
                    if($query)  $return = "Application Sent!.";
                    else        $return = "Ooops, Failed!";
                }else   $return = "Ooops, Failed! Your department has no cluster head.";
            }else if($user == $hrd){    // HR Head Seminar Application
                if(!empty($chead)){
                $ins   = $this->db->query("INSERT INTO seminar_app (employeeid,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,depthead,hrdir,cluster,budgetoff,financedir,president) VALUES ('{$user}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}','{$dhead}','{$hrd}','{$chead}','{$boff}','{$fdir}','{$pres}')");
                    if($ins){
                        $qid   = $this->db->query("SELECT id FROM seminar_app WHERE employeeid='$user' AND dfrom='{$data['datesetfrom']}' AND dto='{$data['datesetto']}' AND tstart='{$tfrom}' AND tend='{$tto}' AND nodays='{$data['ndays']}' AND purpose='{$poa}' AND course='{$course}' AND venue='{$venue}' AND statement='{$soc}'");
                        $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);
                                
                        $query = $this->db->query("INSERT INTO seminar_app_chead (aid,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied) VALUES ('$aid','{$user}','{$dhead}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}',NOW())");
                        if($query)  $return = "Application Sent!.";
                        else        $return = "Ooops, Failed!";
                    }
                }else   $return = "Ooops, Failed! Your department has no cluster head.";
            }else if($user == $chead){      // Cluster Head Seminar Application
                $ins   = $this->db->query("INSERT INTO seminar_app (employeeid,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,depthead,hrdir,cluster,budgetoff,financedir,president) VALUES ('{$user}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}','{$dhead}','{$hrd}','{$chead}','{$boff}','{$fdir}','{$pres}')");
                if($ins){
                    $qid   = $this->db->query("SELECT id FROM seminar_app WHERE employeeid='$user' AND dfrom='{$data['datesetfrom']}' AND dto='{$data['datesetto']}' AND tstart='{$tfrom}' AND tend='{$tto}' AND nodays='{$data['ndays']}' AND purpose='{$poa}' AND course='{$course}' AND venue='{$venue}' AND statement='{$soc}'");
                    $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);
                        
                    $query = $this->db->query("INSERT INTO seminar_app_budgetoff (aid,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied) VALUES ('$aid','{$user}','{$dhead}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}',NOW())");
                    if($query)  $return = "Application Sent!.";
                    else        $return = "Ooops, Failed!";
                }   
            }else{
                $ins   = $this->db->query("INSERT INTO seminar_app (employeeid,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,depthead,deptheadstatus,deptheaddate,hrdir,cluster,budgetoff,financedir,president) VALUES ('{$user}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}','{$dhead}','APPROVED',CURRENT_DATE,'{$hrd}','{$chead}','{$boff}','{$fdir}','{$pres}')");
                if($ins){
                    $qid   = $this->db->query("SELECT id FROM seminar_app WHERE employeeid='$user' AND dfrom='{$data['datesetfrom']}' AND dto='{$data['datesetto']}' AND tstart='{$tfrom}' AND tend='{$tto}' AND nodays='{$data['ndays']}' AND purpose='{$poa}' AND course='{$course}' AND venue='{$venue}' AND statement='{$soc}'");
                    $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);
                        
                    $query = $this->db->query("INSERT INTO seminar_app_dhead (aid,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,status,dateapproved,dateapplied) VALUES ('$aid','{$user}','{$dhead}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}','APPROVED',CURRENT_DATE,NOW())");
                    $query = $this->db->query("INSERT INTO seminar_app_hrd (aid,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied) VALUES ('$aid','{$user}','{$hrd}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}',NOW())");
                    if($query)  $return = "Application Sent!.";
                    else        $return = "Ooops, Failed!";
                }
            }
        }
        return $return;
    }

    function applySeminarWithSequence($data){
        $return = array("err_code"=>0,"msg"=>"Application Sent!.","base_id"=>"");
        $bid = "";

        if(isset($data['employeeid']))
             $user   = $data['employeeid'];
        else
             $user   = $this->session->userdata("username"); 

        $tfrom  = date("H:i:s",strtotime($data['tfrom']));
        $tto    = date("H:i:s",strtotime($data['tto']));
        $poa    = $this->extras->clean($data['poa']);
        $course = $this->extras->clean($data['course']);
        $venue  = $this->extras->clean($data['venue']);
        $speaker= $this->extras->clean($data['speaker']);
        $misc   = $this->extras->clean($data['miscellaneous']);
        $soc    = $this->extras->clean($data['soc']);

        $qdept  = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$user'");
        $deptid = ($qdept->num_rows() > 0 ? $qdept->row(0)->deptid : "");

        $dhead = $hrd = $chead = $up = $budgetOff = $financeDir = $president = "";
        $leave_setup_data = $this->db->query("SELECT * FROM code_request_form WHERE ismain='3'")->result_array();

        if($deptid){
            foreach ($leave_setup_data as $key => $setup) {
                if($setup['dhseq'] != "0"){ 
                    $qdept  = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$user'");
                    $deptid = ($qdept->num_rows() > 0 ? $qdept->row(0)->deptid : "");
                    if($deptid){
                        $qdhead = $this->db->query("SELECT head,divisionhead FROM code_office WHERE code='$deptid'");
                        $dhead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->head : "");                         // department head
                        $chead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->divisionhead : "");                 // Cluster head
                    }
                }

                if($setup['hhseq'] != "0"){
                    $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
                    $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                             // HR Director
                } 

                if($setup['chseq'] == "0"){ 
                    $chead = "";
                }

                if($setup['upseq'] != "0"){ 
                    $qup    = $this->db->query("SELECT * FROM code_request_form WHERE (univphy<>'' OR univphyt<>'') AND code_request='$ltype'"); // University Physician
                    $up     = ($qup->num_rows() > 0 ? $qup->row(0)->univphy.",".$qup->row(0)->univphyt : "");
                }

                if($setup['boseq'] != "0") $budgetOff      = $setup['budgetoff'];
                if($setup['fdseq'] != "0") $financeDir     = $setup['financedir'];
                if($setup['pseq'] != "0")  $president      = $setup['president'];

                // $seq = $this->getAppSequence($data['ltype'], $deptid, $data['ltype']); 
                #condition for sequence
                $tbl = "";
                $tblhead = "";
                if($setup['dhseq'] == "2"){      $tbl = "seminar_app_dhead";       $tblhead = $dhead;
                }elseif($setup['hhseq'] == "2"){  $tbl = "seminar_app_hrd";         $tblhead = $hrd;
                }elseif($setup['chseq'] == "2"){  $tbl = "seminar_app_chead";       $tblhead = $chead;
                }elseif($setup['upseq'] == "2"){  $tbl = "seminar_app_uphy";        $tblhead = $up;
                }elseif($setup['boseq'] == "2"){  $tbl = "seminar_app_budgetoff";   $tblhead = $budgetOff;
                }elseif($setup['fdseq'] == "2"){  $tbl = "seminar_app_findir";      $tblhead = $financeDir;
                }elseif($setup['pseq'] == "2"){   $tbl = "seminar_app_president";   $tblhead = $president;}

                if($user == $dhead){    // Dean/Head Seminar Application
                    // if(!empty($chead)){
                        $eid = $data['eid'];
                        $qbid   = $this->db->query("SELECT baseid FROM based_id_offbus");
                        $bid    = $qbid->row(0)->baseid;
                                  $this->db->query("UPDATE based_id_offbus SET baseid='".($bid+1)."'");
                        foreach(explode(",",$eid) as $empid){
                            $isread = 1;                        
                            $ins   = $this->db->query("
                                INSERT INTO seminar_app (employeeid,base_id,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,depthead,deptheadstatus,deptheaddate,hrdir,cluster,budgetoff,financedir,president,dhseq,hhseq,chseq,upseq,boseq,fdseq,pseq,isread) 
                                VALUES ('{$empid}','{$bid}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}','{$dhead}','APPROVED',CURRENT_DATE,'{$hrd}','{$chead}','{$budgetOff}','{$financeDir}','{$president}','{$setup['dhseq']}','{$setup['hhseq']}','{$setup['chseq']}','{$setup['upseq']}','{$setup['boseq']}','{$setup['fdseq']}','{$setup['pseq']}','{$isread}')");
                            if($ins){
                                $qid   = $this->db->query("SELECT id FROM seminar_app WHERE employeeid='$empid' AND dfrom='{$data['datesetfrom']}' AND dto='{$data['datesetto']}' AND tstart='{$tfrom}' AND tend='{$tto}' AND nodays='{$data['ndays']}' AND purpose='{$poa}' AND course='{$course}' AND venue='{$venue}' AND statement='{$soc}'");
                                $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);
                                        
                                $query = $this->db->query("INSERT INTO seminar_app_dhead (aid,base_id,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,status,dateapproved,dateapplied) VALUES ('$aid','{$bid}','{$empid}','{$dhead}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}','APPROVED',NOW(),NOW())");
                                $query = $this->db->query("INSERT INTO $tbl (aid,base_id,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied) VALUES ('$aid','{$bid}','{$empid}','$tblhead','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['meal']}','{$data['transpo']}','{$data['hotel']}','{$data['tc']}','{$venue}','{$speaker}','{$misc}','{$soc}',NOW())"); 
                            }
                        }
                        if($query){
                            // $return['err_code'] = "Application Sent!.";
                            $return = array("err_code"=>0,"msg"=>"Application Sent!.","base_id"=>$bid);
                        }  
                        else{
                            // $return['err_code'] = "Ooops, Failed!";
                            $return = array("err_code"=>2,"msg"=>"Ooops, Failed!","base_id"=>"");
                        }        
                    // }else   $return = "Ooops, Failed! Your department has no cluster head.";
                }
            } //end foreach

        } //end if

        return $return;
    }
    
    function modifySeminar($data){
        $return = "";
        $tfrom  = date("H:i:s",strtotime($data['tfrom']));
        $tto    = date("H:i:s",strtotime($data['tto']));
        $poa    = $this->extras->clean($data['poa']);
        $course = $this->extras->clean($data['course']);
        $venue  = $this->extras->clean($data['venue']);
        $speaker= $this->extras->clean($data['speaker']);
        $misc   = $this->extras->clean($data['miscellaneous']);
        $soc    = $this->extras->clean($data['soc']);
        $eid    = $data['eid'];
        foreach(explode(",",$eid) as $empid){
            $eq    = $this->db->query("SELECT * FROM seminar_app WHERE employeeid='$empid' AND base_id='{$data['id']}' AND (hrdirstatus='APPROVED' OR hrdirstatus='DISAPPROVED')");
            if($eq->num_rows() > 0){
                $return .= " Employee No.: ".$empid." is unable to update because the status is already ".$eq->row(0)->hrdirstatus." by HR Head.\n";         
            }else{
                     $this->db->query("UPDATE seminar_app SET purpose='{$poa}', course='{$course}', dfrom='{$data['datesetfrom']}', dto='{$data['datesetto']}', tstart='{$tfrom}', tend='{$tto}', nodays='{$data['ndays']}', paiddays='{$data['pwd']}', coursefee='{$data['cfee']}', meal='{$data['meal']}', transportation='{$data['transpo']}', hotel='{$data['hotel']}', totalcost='{$data['tc']}', venue='{$venue}', miscellaneous='{$misc}', statement='$soc' WHERE employeeid='$empid' AND base_id='{$data['id']}'");
            $query = $this->db->query("UPDATE seminar_app_hrd SET purpose='{$poa}', course='{$course}', dfrom='{$data['datesetfrom']}', dto='{$data['datesetto']}', tstart='{$tfrom}', tend='{$tto}', nodays='{$data['ndays']}', paiddays='{$data['pwd']}', coursefee='{$data['cfee']}', meal='{$data['meal']}', transportation='{$data['transpo']}', hotel='{$data['hotel']}', totalcost='{$data['tc']}', venue='{$venue}', miscellaneous='{$misc}', statement='$soc' WHERE employeeid='$empid' AND base_id='{$data['id']}'");
                $return .= " Employee No.: ".$empid." is successfully updated!\n";
            }
        }
        return $return;
    }
    function delSeminar($data){
        $return = "";
        $eq    = $this->db->query("SELECT * FROM seminar_app WHERE base_id='{$data['id']}' AND (hrdirstatus='APPROVED' OR hrdirstatus='DISAPPROVED')");
        if($eq->num_rows() > 0){
            foreach($eq->result() as $row){
                $return .= " Employee No.: ".$row->employeeid." is unable to delete because the status is already ".$row->hrdirstatus." by HR Head.\n";
            }
            $eq    = $this->db->query("SELECT * FROM seminar_app WHERE base_id='{$data['id']}' AND (hrdirstatus IS NULL OR hrdirstatus='PENDING')");
            foreach($eq->result() as $row){
                $this->db->query("DELETE FROM seminar_app WHERE employeeid='".$row->employeeid."' AND base_id='{$data['id']}'");
                $this->db->query("DELETE FROM seminar_app_hrd WHERE employeeid='".$row->employeeid."' AND base_id='{$data['id']}'");
                $return .= " Employee No.: ".$row->employeeid." is successfully deleted!.\n";
            }
        }else{
            $eq    = $this->db->query("SELECT * FROM seminar_app WHERE base_id='{$data['id']}' AND (hrdirstatus IS NULL OR hrdirstatus='PENDING')");
            foreach($eq->result() as $row){
                $this->db->query("DELETE FROM seminar_app WHERE employeeid='".$row->employeeid."' AND base_id='{$data['id']}'");
                $this->db->query("DELETE FROM seminar_app_hrd WHERE employeeid='".$row->employeeid."' AND base_id='{$data['id']}'");
                $return .= " Employee No.: ".$row->employeeid." is successfully deleted!.\n";
            }
        }
        return $return;
    }
        
    function seminar_modify_query($id=""){
        $query = $this->db->query("SELECT * FROM seminar_app WHERE base_id='$id'");
        return $query;
    }
    
    function seminar_approve_head($data){
        $user = $this->session->userdata("username");
        $id     = $data['id'];
        $aid    = $data['aid'];
        $status = $data['status'];
        
        $head       = $this->db->query("SELECT * FROM seminar_app WHERE id='$aid'");
        $depthead   = ($head->num_rows() > 0 ? $head->row(0)->depthead : "");
        $hrdir      = ($head->num_rows() > 0 ? $head->row(0)->hrdir : "");
        $cluster    = ($head->num_rows() > 0 ? $head->row(0)->cluster : "");
        $budgetoff  = ($head->num_rows() > 0 ? $head->row(0)->budgetoff : "");
        $financedir = ($head->num_rows() > 0 ? $head->row(0)->financedir : "");
        $president  = ($head->num_rows() > 0 ? $head->row(0)->president : "");
        
        if($depthead == $user){
            if($status == "APPROVED"){
            $query = $this->db->query(" INSERT INTO seminar_app_hrd 
                                        (aid,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied)
                                (SELECT aid,employeeid,'$hrdir',purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied FROM seminar_app_dhead WHERE id='$id')
                                      ");
            }
            if($status == "DISAPPROVED")
                $query = $this->db->query("UPDATE seminar_app SET deptheadstatus='$status', deptheaddate=CURRENT_DATE, isread=0 WHERE id='$aid'");
            else
                $query = $this->db->query("UPDATE seminar_app SET deptheadstatus='$status', deptheaddate=CURRENT_DATE WHERE id='$aid'");
                         $this->db->query("UPDATE seminar_app_dhead SET status='$status', dateapproved=CURRENT_DATE WHERE id='$id'");
        }else if($hrdir == $user){
            if($status == "APPROVED"){
            $query = $this->db->query(" INSERT INTO seminar_app_chead 
                                        (aid,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied)
                                (SELECT aid,employeeid,'$cluster',purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied FROM seminar_app_hrd WHERE id='$id')
                                      ");
            }
            if($status == "DISAPPROVED")
                $query = $this->db->query("UPDATE seminar_app SET hrdirstatus='$status', hrdirdate=CURRENT_DATE, isread=0 WHERE id='$aid'");
            else
                $query = $this->db->query("UPDATE seminar_app SET hrdirstatus='$status', hrdirdate=CURRENT_DATE WHERE id='$aid'");
                         $this->db->query("UPDATE seminar_app_hrd SET status='$status', dateapproved=CURRENT_DATE WHERE id='$id'");
        }else if($cluster == $user){
            if($status == "APPROVED"){
            $query = $this->db->query(" INSERT INTO seminar_app_budgetoff 
                                        (aid,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied)
                                (SELECT aid,employeeid,'$budgetoff',purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied FROM seminar_app_chead WHERE id='$id')
                                      ");
            }
            if($status == "DISAPPROVED")
                $query = $this->db->query("UPDATE seminar_app SET clusterstatus='$status', clusterdate=CURRENT_DATE, isread=0 WHERE id='$aid'");
            else
                $query = $this->db->query("UPDATE seminar_app SET clusterstatus='$status', clusterdate=CURRENT_DATE WHERE id='$aid'");
                         $this->db->query("UPDATE seminar_app_chead SET status='$status', dateapproved=CURRENT_DATE WHERE id='$id'");
        }else if($budgetoff == $user){
            if($status == "APPROVED"){
            $query = $this->db->query(" INSERT INTO seminar_app_findir 
                                        (aid,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied)
                                (SELECT aid,employeeid,'$financedir',purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied FROM seminar_app_budgetoff WHERE id='$id')
                                      ");
            }
            if($status == "DISAPPROVED")
                $query = $this->db->query("UPDATE seminar_app SET budgetoffstatus='$status', budgetoffdate=CURRENT_DATE, isread=0 WHERE id='$aid'");
            else            
                $query = $this->db->query("UPDATE seminar_app SET budgetoffstatus='$status', budgetoffdate=CURRENT_DATE WHERE id='$aid'");
                         $this->db->query("UPDATE seminar_app_budgetoff SET status='$status', dateapproved=CURRENT_DATE WHERE id='$id'");
        }else if($financedir == $user){
            if($status == "APPROVED"){
            $query = $this->db->query(" INSERT INTO seminar_app_president 
                                        (aid,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied)
                                (SELECT aid,employeeid,'$president',purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied FROM seminar_app_findir WHERE id='$id')
                                      ");
            }
            if($status == "DISAPPROVED")
                $query = $this->db->query("UPDATE seminar_app SET financedirstatus='$status', financedirdate=CURRENT_DATE, isread=0 WHERE id='$aid'");
            else    
                $query = $this->db->query("UPDATE seminar_app SET financedirstatus='$status', financedirdate=CURRENT_DATE WHERE id='$aid'");
                         $this->db->query("UPDATE seminar_app_findir SET status='$status', dateapproved=CURRENT_DATE WHERE id='$id'");
        }else if($president == $user){
            if($status == "APPROVED"){
            $query = $this->db->query(" INSERT INTO seminar_request 
                                        (aid,employeeid,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,status)
                                (SELECT aid,employeeid,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,'$status' FROM seminar_app_findir WHERE aid='$aid')
                                      ");
                     $this->db->query("INSERT INTO timesheet (userid,timein,timeout,otype) (SELECT employeeid,CONCAT(dfrom,' ',tstart),CONCAT(dto,' ',tend),'SEMINAR' FROM seminar_app_findir WHERE aid='$aid')");
            }
            if($status == "DISAPPROVED" || $status == "APPROVED"){
                $query = $this->db->query("UPDATE seminar_app SET status='$status',presidentstatus='$status', presidentdate=CURRENT_DATE, isread=0 WHERE id='$aid'");
                         $this->db->query("UPDATE seminar_app_president SET status='$status', dateapproved=CURRENT_DATE WHERE id='$id'");
            }
        }
        
        if($query)  $return = "Successfully $status!.";  
        else        $return = "Failed!.";
        
        return $return;
    }

    function seminar_approve_head_withsequence($data){
        $seqno = 0;
        $query = $return = "";
        $user = $this->session->userdata("username");
        $id     = $data['id'];
        $aid    = $data['aid'];
        $status = $data['status'];
        
        $seq = $this->getSeminarSequence('OBS',"",'OBS');
        // echo "<pre>";print_r($seq);
        for($seqno = 2; $seqno<= count($seq); $seqno++){
            if(in_array($user, explode(",",$seq[$seqno]["head"]))){
                $tbl                = $seq[$seqno]["tbl"];
                $head_col_status    = ($seq[$seqno]["type"] == "dh" ? "deptheadstatus" : 
                                        ($seq[$seqno]["type"] == "hh" ? "hrdirstatus" : 
                                        ($seq[$seqno]["type"] == "ch" ? "clusterstatus" : 
                                        ($seq[$seqno]["type"] == "up" ? "univphystatus" :
                                        ($seq[$seqno]["type"] == "bo" ? "budgetoffstatus" :
                                        ($seq[$seqno]["type"] == "fd" ? "financedirstatus" :
                                        ($seq[$seqno]["type"] == "pt" ? "presidentstatus" :
                                         "")))))));
                $head_col_date      = ($seq[$seqno]["type"] == "dh" ? "deptheaddate" : 
                                        ($seq[$seqno]["type"] == "hh" ? "hrdirdate" : 
                                        ($seq[$seqno]["type"] == "ch" ? "clusterdate" : 
                                        ($seq[$seqno]["type"] == "up" ? "univphydate" : 
                                        ($seq[$seqno]["type"] == "bo" ? "budgetoffdate" :
                                        ($seq[$seqno]["type"] == "fd" ? "financedirdate" :
                                        ($seq[$seqno]["type"] == "pt" ? "presidentdate" :
                                        "")))))));
                $seqno += 1;
                break;
            }             
        }  
        // echo $seq[$seqno]['tbl'];
        // echo print_r($seq);
        // echo $seqno. " - " .count($seq);

        if($seqno == count($seq)){
            if($status == "APPROVED"){
                $query = $this->db->query(" INSERT INTO seminar_request 
                                        (aid,employeeid,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,status)
                                (SELECT aid,employeeid,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,'$status' FROM $tbl WHERE aid='$aid')
                                      ");
                $this->db->query("INSERT INTO timesheet (userid,timein,timeout,otype) (SELECT employeeid,CONCAT(dfrom,' ',tstart),CONCAT(dto,' ',tend),'SEMINAR' FROM $tbl WHERE aid='$aid')");
            }
            if($status == "DISAPPROVED" || $status == "APPROVED"){
                $query = $this->db->query("UPDATE seminar_app SET status='$status', $head_col_status='$status', $head_col_date=CURRENT_DATE, isread=0 WHERE id='$aid'");
                         $this->db->query("UPDATE $tbl SET status='$status', dateapproved=CURRENT_DATE WHERE id='$id'");
            }
        }else{
           if($status == "APPROVED"){
                $query = $this->db->query(" INSERT INTO {$seq[$seqno]['tbl']} 
                                       (aid,employeeid,head,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied)
                               (SELECT aid,employeeid,'{$seq[$seqno]["head"]}',purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,meal,transportation,hotel,totalcost,venue,speaker,miscellaneous,statement,dateapplied FROM $tbl WHERE id='$id')
                                     ");
           }
           if($status == "DISAPPROVED")
               $query = $this->db->query("UPDATE seminar_app SET $head_col_status='$status', $head_col_date=CURRENT_DATE, isread=0 WHERE id='$aid'");
           else            
               $query = $this->db->query("UPDATE seminar_app SET $head_col_status='$status', $head_col_date=CURRENT_DATE WHERE id='$aid'");
                        $this->db->query("UPDATE $tbl SET status='$status', dateapproved=CURRENT_DATE WHERE id='$id'");
        }

        if($query)  $return = "Success! Status now is : $status";  
        else        $return = "Failed!.";

        return $return;
    }
    
    function applyOT($data){
        $return = ""; 
        $reason = $this->extras->clean($data['reason']);
        $tstart = date("H:i:s",strtotime($data['tfrom']));
        $tend    = date("H:i:s",strtotime($data['tto']));
        if(isset($data['employeeid']))
            $user   = $data['employeeid'];
        else
            $user   = $this->session->userdata("username");
        $qdept  = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$user'");
        $deptid = ($qdept->num_rows() > 0 ? $qdept->row(0)->deptid : "");
        if($deptid){
            $qdhead = $this->db->query("SELECT head,divisionhead FROM code_office WHERE code='$deptid'");
            $dhead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->head : "");                         // department head
            $chead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->divisionhead : "");                 // Cluster head
            $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
            $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                             // HR Director
                
                if($user == $dhead || $user == $hrd){    // Dean/Head & HR Head Vacation Leave Application
                    if(!empty($chead)){
                        $ins   = $this->db->query("INSERT INTO overtime_app (employeeid,dfrom,dto,tstart,tend,total,reason,depthead,hrdir) VALUES ('{$user}','{$data['datesetfrom']}','{$data['datesetto']}','{$tstart}','{$tend}','{$data['tot']}','{$reason}','{$chead}','{$hrd}')");
                        if($ins){
                            $qid   = $this->db->query("SELECT id FROM overtime_app WHERE employeeid='$user' AND dfrom='{$data['datesetfrom']}' AND dto='{$data['datesetto']}' AND tstart='{$tstart}' AND tend='{$tend}' AND total='{$data['tot']}' AND reason='{$reason}' AND depthead='{$chead}' AND hrdir='{$hrd}'");
                            $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);
                            
                            $query = $this->db->query("INSERT INTO overtime_app_chead (aid,employeeid,head,dfrom,dto,tstart,tend,total,reason,dateapplied) VALUES ('$aid','{$user}','{$chead}','{$data['datesetfrom']}','{$data['datesetto']}','{$tstart}','{$tend}','{$data['tot']}','{$reason}',NOW())");
                            if($query)  $return = "Application Sent!.";
                            else        $return = "Ooops, Failed!";
                        }
                    }else   $return = "Ooops, Failed! Your department has no cluster head.";
                }else if($user == $chead){      // Cluster Head Vacation Leave Application
                    $ins   = $this->db->query("INSERT INTO overtime_app (employeeid,dfrom,dto,tstart,tend,total,reason,depthead,hrdir) VALUES ('{$user}','{$data['datesetfrom']}','{$data['datesetto']}','{$tstart}','{$tend}','{$data['tot']}','{$reason}','{$dhead}','{$hrd}')");
                    if($ins){
                        $qid   = $this->db->query("SELECT id FROM overtime_app WHERE employeeid='$user' AND dfrom='{$data['datesetfrom']}' AND dto='{$data['datesetto']}' AND tstart='{$tstart}' AND tend='{$tend}' AND total='{$data['tot']}' AND reason='{$reason}' AND depthead='{$dhead}' AND hrdir='{$hrd}'");
                        $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);
                        
                        $query = $this->db->query("INSERT INTO leave_app_hrd (aid,employeeid,head,dfrom,dto,tstart,tend,total,reason,dateapplied) VALUES ('$aid','{$user}','{$dhead}','{$data['datesetfrom']}','{$data['datesetto']}','{$tstart}','{$tend}','{$data['tot']}','{$reason}',NOW())");
                        if($query)  $return = "Application Sent!.";
                        else        $return = "Ooops, Failed!";
                    }   
                }else{      // Employee Leave Application
                        $ins   = $this->db->query("INSERT INTO overtime_app (employeeid,dfrom,dto,tstart,tend,total,reason,depthead,hrdir) VALUES ('{$user}','{$data['datesetfrom']}','{$data['datesetto']}','{$tstart}','{$tend}','{$data['tot']}','{$reason}','{$dhead}','{$hrd}')");
                    if($ins){
                        $qid   = $this->db->query("SELECT id FROM overtime_app WHERE employeeid='$user' AND dfrom='{$data['datesetfrom']}' AND dto='{$data['datesetto']}' AND tstart='{$tstart}' AND tend='{$tend}' AND total='{$data['tot']}' AND reason='{$reason}' AND depthead='{$dhead}' AND hrdir='{$hrd}'");
                        $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);
                        
                        $query = $this->db->query("INSERT INTO overtime_app_dhead (aid,employeeid,head,dfrom,dto,tstart,tend,total,reason,dateapplied) VALUES ('$aid','{$user}','{$dhead}','{$data['datesetfrom']}','{$data['datesetto']}','{$tstart}','{$tend}','{$data['tot']}','{$reason}',NOW())");
                        if($query)  $return = "Application Sent!.";
                        else        $return = "Ooops, Failed!";
                    }
                }
        }else   $return = "Failed to apply leave. Please set your department first.";
        return $return;
    }
    
    function ot_approve_head($data){
        $return = "";
        if($data['id']){
            $id     = $data['id'];
            $aid    = $data['aid'];
            $status = $data['status'];
            $dept   = $data['dept'];
            $cdate  = date("Y-m-d");
            
                if($dept == "HR"){
                    if($status == "APPROVED"){
                              $this->db->query("INSERT INTO overtime_request (aid,employeeid,dfrom,dto,tstart,tend,total,reason,status,dateapproved,dateapplied)
                                                (SELECT aid,employeeid,dfrom,dto,tstart,tend,total,reason,'$status','$cdate',dateapplied FROM overtime_app_hrd WHERE id='$id')  
                                              ");
                    }
                              $this->db->query("UPDATE overtime_app SET status='$status',hrdirstatus='$status', hrdirdate='$cdate', isread=0 WHERE id='$aid'");
                    $query  = $this->db->query("UPDATE overtime_app_hrd SET status='$status', dateapproved='$cdate' WHERE id='$id'");
                }else{
                        if($this->employee->getClusterHead($this->session->userdata("username")))
                                $tbl = "overtime_app_chead";
                        else    $tbl = "overtime_app_dhead";
                        if($status == "APPROVED"){
                            $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
                            $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                         // department head
                                      $this->db->query("INSERT INTO overtime_app_hrd (aid,employeeid,head,dfrom,dto,tstart,tend,total,reason,dateapplied)
                                                        (SELECT aid,employeeid,'$hrd',dfrom,dto,tstart,tend,total,reason,dateapplied FROM $tbl WHERE id='$id')  
                                                      ");
                        }
                        if($status == "DISAPPROVED")
                              $this->db->query("UPDATE overtime_app SET status='$status',deptheadstatus='$status', deptheaddate='$cdate', isread=0 WHERE id='$aid'");
                        else
                              $this->db->query("UPDATE overtime_app SET deptheadstatus='$status', deptheaddate='$cdate' WHERE id='$aid'");
                    $query  = $this->db->query("UPDATE $tbl SET status='$status', dateapproved='$cdate' WHERE id='$id'");
                }
            
            if($query)  $return = "Successfully $status!.";  
            else        $return = "Failed!.";
        }
        return $return;
    }
    function modifyOT($data){
        $return = "";
        $id = $data['id'];
        $query = $this->db->query("SELECT * FROM overtime_app WHERE id='$id' AND (deptheadstatus='APPROVED' OR deptheadstatus='DISAPPROVED')");
        if($query->num_rows() > 0){
            $return = "Failed to update!. The request has already ".$query->row()->deptheadstatus;
        }else{
            $query = $this->db->query("UPDATE overtime_app       SET dfrom='{$data['datesetfrom']}', dto='{$data['datesetto']}', tstart='{$data['tfrom']}', tend='{$data['tto']}', total='{$data['tot']}', reason='{$data['reason']}' WHERE id='$id'");
                     $this->db->query("UPDATE overtime_app_chead SET dfrom='{$data['datesetfrom']}', dto='{$data['datesetto']}', tstart='{$data['tfrom']}', tend='{$data['tto']}', total='{$data['tot']}', reason='{$data['reason']}' WHERE aid='$id'");
                     $this->db->query("UPDATE overtime_app_hrd   SET dfrom='{$data['datesetfrom']}', dto='{$data['datesetto']}', tstart='{$data['tfrom']}', tend='{$data['tto']}', total='{$data['tot']}', reason='{$data['reason']}' WHERE aid='$id'");
            $return = "Successfully Saved!.";
        }
        return $return;
    }
    function delOT($data){
        $return = "";
        $id = $data['id'];
        $query = $this->db->query("SELECT * FROM overtime_app WHERE id='$id' AND (deptheadstatus='APPROVED' OR deptheadstatus='DISAPPROVED')");
        if($query->num_rows() > 0){
            $return = "Failed to delete!. The request has already ".$query->row()->deptheadstatus;
        }else{
            $query = $this->db->query("DELETE FROM overtime_app       WHERE id='$id'");
                     $this->db->query("DELETE FROM overtime_app_chead WHERE aid='$id'");
                     $this->db->query("DELETE FROM overtime_app_hrd   WHERE aid='$id'");
            $return = "Successfully Deleted!.";
        }
        return $return;
    }
    function ot_modify_query($id=""){
        $query = $this->db->query("SELECT * FROM overtime_app WHERE id='$id'");
        return $query;
    }
    function modifyOffBus($data){
        $return = "";
        $id = $data['id'];
        $query = $this->db->query("SELECT * FROM leave_app WHERE (deptheadstatus='APPROVED' OR deptheadstatus='DISAPPROVED') WHERE id='{$id}'");
        if($query->num_rows() > 0){
            $return = "Failed to update!. The request has already ".$query->row()->deptheadstatus;
        }else{
            $query = $this->db->query("UPDATE leave_app       SET type='{$data['ltype']}', other='{$data['othleave']}', othertype='{$data['dltype']}', paid='{$data['withpay']}', datefrom='{$data['datesetfrom']}', dateto='{$data['datesetto']}', timefrom='{$data['tfrom']}', timeto='{$data['tto']}', nodays='{$data['ndays']}', reason='{$data['reason']}' WHERE id='{$data['id']}'");        
                     $this->db->query("UPDATE leave_app_dhead SET type='{$data['ltype']}', other='{$data['othleave']}', othertype='{$data['dltype']}', paid='{$data['withpay']}', datefrom='{$data['datesetfrom']}', dateto='{$data['datesetto']}', timefrom='{$data['tfrom']}', timeto='{$data['tto']}', nodays='{$data['ndays']}', reason='{$data['reason']}' WHERE aid='{$data['id']}'");
                     $this->db->query("UPDATE leave_app_chead SET type='{$data['ltype']}', other='{$data['othleave']}', othertype='{$data['dltype']}', paid='{$data['withpay']}', datefrom='{$data['datesetfrom']}', dateto='{$data['datesetto']}', timefrom='{$data['tfrom']}', timeto='{$data['tto']}', nodays='{$data['ndays']}', reason='{$data['reason']}' WHERE aid='{$data['id']}'");
                     $this->db->query("UPDATE leave_app_uphy  SET type='{$data['ltype']}', other='{$data['othleave']}', othertype='{$data['dltype']}', paid='{$data['withpay']}', datefrom='{$data['datesetfrom']}', dateto='{$data['datesetto']}', timefrom='{$data['tfrom']}', timeto='{$data['tto']}', nodays='{$data['ndays']}', reason='{$data['reason']}' WHERE aid='{$data['id']}'");
                     $this->db->query("UPDATE leave_app_hrd   SET type='{$data['ltype']}', other='{$data['othleave']}', othertype='{$data['dltype']}', paid='{$data['withpay']}', datefrom='{$data['datesetfrom']}', dateto='{$data['datesetto']}', timefrom='{$data['tfrom']}', timeto='{$data['tto']}', nodays='{$data['ndays']}', reason='{$data['reason']}' WHERE aid='{$data['id']}'");
            $return = "Successfully Saved!";
        }
        return $return;
    }
    function delOffBus($data){
        $return = "";
        $query = $this->db->query("SELECT * FROM leave_app WHERE (deptheadstatus='APPROVED' OR deptheadstatus='DISAPPROVED') WHERE id='{$data['id']}'");
        if($query->num_rows() > 0){
            $return = "Failed to delete!. The request has already ".$query->row()->deptheadstatus;
        }else{
            $this->db->query("DELETE FROM leave_app       WHERE id ='{$data['id']}'");
            $this->db->query("DELETE FROM leave_app_dhead WHERE aid='{$data['id']}'");
            $this->db->query("DELETE FROM leave_app_chead WHERE aid='{$data['id']}'");
            $this->db->query("DELETE FROM leave_app_uphy  WHERE aid='{$data['id']}'");
            $this->db->query("DELETE FROM leave_app_hrd   WHERE aid='{$data['id']}'");
            $return = "Succesfully Deleted!.";
        }
        return $return;
    }
    function modifyOffbus_query($id=""){
        $query = $this->db->query("SELECT * FROM leave_app WHERE id='$id'");
        return $query;
    }
    function seminar_attach($data){
        $return = "";
        $query = $this->db->query("SELECT * FROM seminar_app_attach WHERE id='{$data['id']}'");
        if($query->num_rows() > 0){
            if($data['job'] == "view")
                $query = $this->db->query("UPDATE seminar_app_attach SET status='{$data['status']}' WHERE id='{$data['id']}'");
            else
                $query = $this->db->query("UPDATE seminar_app_attach SET points='{$data['points']}' WHERE id='{$data['id']}'");
            if($query)  $return = "Report Updated!.";
            else        $return = "Ooops, Failed!";
        }else{
            if($data['job'] != "view"){
                $query = $this->db->query("INSERT INTO seminar_app_attach (id,points) VALUES ('{$data['id']}','{$data['points']}')");
                if($query)  $return = "Report Successfully Attached!.";
                else        $return = "Ooops, Failed!";
            }else   $return = "Invalid! No Report Attached!.";
        }
        return $return;
    }
    
    function setupLeave($data){
        $ins  = 0;
        $msg  = "";
        $user = $this->session->userdata("username");
        $job  = $data['code'] ? true : false;
        $query = $this->db->query("SELECT * FROM code_leave_setup WHERE leavetype='{$data['mh_leavetype']}' AND ('{$data['datesetfrom']}' BETWEEN dfrom AND dto OR '{$data['datesetto']}' BETWEEN dfrom AND dto) ");
        if($query->num_rows() == 0)
             $this->db->query("INSERT INTO code_leave_setup (leavetype,credit,dfrom,dto,user) VALUES ('{$data['mh_leavetype']}','{$data['mh_credits']}','{$data['datesetfrom']}','{$data['datesetto']}','{$user}')");
        else $msg = "Conflict Schedule of Leave.";
        if(!$msg || $job){
            $query = $this->db->query("SELECT employeeid FROM employee WHERE isactive=1");
            foreach($query->result() as $row){
                $eid =  $row->employeeid;
                            $this->db->query("UPDATE code_leave_setup SET dfrom='{$data['datesetfrom']}', dto='{$data['datesetto']}' WHERE id='{$data['lid']}'");
                $cquery =   $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$eid' AND leavetype='{$data['mh_leavetype']}' AND ('{$data['datesetfrom']}' BETWEEN dfrom AND dto OR '{$data['datesetto']}' BETWEEN dfrom AND dto) ");
                if($cquery->num_rows() == 0){
                    $this->db->query("INSERT INTO employee_leave_credit (employeeid,leavetype,balance,credit,dfrom,dto,user) VALUES ('{$eid}','{$data['mh_leavetype']}','{$data['mh_credits']}','{$data['mh_credits']}','{$data['datesetfrom']}','{$data['datesetto']}','{$user}')");
                    $ins++;
                }
                $msg = " $ins Employee Leave Successfully Distributed!.";      
            }
        }
        return $msg;
    }
    
    function markasread($data){
        $tbl    = $data['tbl'];
        $id     = $data['id'];
        $val    = $data['val'];
        $this->db->query("UPDATE $tbl SET isread=$val WHERE id=$id");
    }
    
    function checkPendingApp($user = "",$dfrom = "", $dto = ""){
        $continue = false;
        $query = $this->db->query("SELECT * FROM leave_app WHERE employeeid='$user' AND datefrom BETWEEN '$dfrom' AND '$dto';");
        if($query->num_rows() > 0)      $continue = true;
        if(!$continue){
            $query = $this->db->query("SELECT * FROM overtime_app WHERE employeeid='$user' AND dfrom BETWEEN '$dfrom' AND '$dto';");
            if($query->num_rows() > 0)  $continue = true;    
        }
        if(!$continue){
            $query = $this->db->query("SELECT * FROM seminar_app WHERE employeeid='$user' AND dfrom BETWEEN '$dfrom' AND '$dto';");
            if($query->num_rows() > 0)  $continue = true;    
        }
        return $continue;
    }
    
    function confirmatt($data){
        $empid = $this->session->userdata("username");
        $tnt        = $this->employee->getempdatacol("teachingtype",$empid);
        $from_date  = $data['dfrom'];
        $to_date    = $data['dto'];
        $edata      = "NEW";
        $continue   = $this->checkPendingApp($empid,$from_date,$to_date);
        $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
        
        if($continue) return "You cannot confirm this attendance because you still have pending request.. Please check your requests..";
        
        if($tnt == "teaching"){ // Teaching
        $tlec = $tlab = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = ""; 
            foreach ($qdate as $rdate) {
                $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
                $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                $countrow = $sched->num_rows();
                
                if($countrow > 0){
                    $tempsched = "";
                    foreach($sched->result() as $rsched){
                        if($tempsched == $dispLogDate)  $dispLogDate = "";
                        $stime = $rsched->starttime;
                        $etime = $rsched->endtime; 
                        $type  = $rsched->leclab;
                        
                        // logtime
                        list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                        // Leave
                        list($el,$vl,$sl,$ol)     = $this->attcompute->displayLeave($empid,$rdate->dte);
                        // Absent
                        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte);
                        if($el || $vl || $sl || $ol) $absent = "";
                        
                        // Late / Undertime
                        list($lateutlec,$lateutlab,$tschedlec,$tschedlab) = $this->attcompute->displayLateUT($stime,$etime,$login,$logout,$type,$absent);
                        if($el || $vl || $sl || $ol){
                            $lateutlec = $lateutlab = $tschedlec = $tschedlab = "";
                        }
                        /*
                         * Total
                         */ 
                        // Absent
                        $tabsent  += $absent;
                        // Late / UT
                        if($lateutlec){
                            $tlec += $this->attcompute->exp_time($lateutlec);
                        }
                        if($lateutlab){
                            $tlab += $this->attcompute->exp_time($lateutlab);
                        }
                        // Deductions
                        if($tschedlec){
                            $tdlec += $this->attcompute->exp_time($tschedlec);
                        }
                        if($tschedlab){
                            $tdlab += $this->attcompute->exp_time($tschedlab);
                        }
                        
                    }   // end foreach
                    // Leave
                    if($dispLogDate){
                        $tel      += $el;
                        $tvl      += $vl;
                        $tsl      += $sl;
                        $tol      += $ol;
                    } // end if
                } // end if
            }
            $tlec = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "");
            $tlab = ($tlab ? $this->attcompute->sec_to_hm($tlab) : "");
            $tdlec = ($tdlec ? $this->attcompute->sec_to_hm($tdlec) : "");
            $tdlab = ($tdlab ? $this->attcompute->sec_to_hm($tdlab) : "");
            
            // Save to database
            $query = $this->db->query("SELECT * FROM attendance_confirmed WHERE cutoffstart='$from_date' AND cutoffend='$to_date' AND employeeid='$empid'");
            if($query->num_rows() == 0){
                $this->db->query("INSERT INTO attendance_confirmed (employeeid,cutoffstart,cutoffend,latelec,latelab,absent,eleave,vleave,sleave,oleave,deduclec,deduclab) 
                                    VALUES ('$empid','$from_date','$to_date','$tlec','$tlab','$tabsent','$tel','$tvl','$tsl','$tol','$tdlec','$tdlab')");
            }
        }else{ // non teaching
            $totr = $totsat = $totsun = $tothol =$tlec = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = ""; 
            foreach ($qdate as $rdate) {
            $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
            $sched = $this->attcompute->displaySched($empid,$rdate->dte);
            $countrow = $sched->num_rows();
                
            if($countrow > 0){#
                $tempsched = "";
                foreach($sched->result() as $rsched){
                    if($tempsched == $dispLogDate)  $dispLogDate = "";
                    $stime = $rsched->starttime;
                    $etime = $rsched->endtime;
                    $tstart = $rsched->tardy_start; 
                    $earlyd = $rsched->early_dismissal; 
                    
                    // Holiday
                    $holiday = $this->attcompute->isHoliday($rdate->dte); 
                    
                    // logtime
                    list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                        
                    // Overtime
                    list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                                
                    // Leave
                    list($el,$vl,$sl,$ol)     = $this->attcompute->displayLeave($empid,$rdate->dte);
                        
                    // Absent
                    $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd);
                    if($el || $vl || $sl || $ol || $holiday) $absent = "";
                        
                    // Late / Undertime
                    $lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent,$teachingtype,$tstart);
                    if($el || $vl || $sl || $ol || $holiday) $lateutlec = "";
                    /*
                    * Total
                    */ 
                    // Absent
                    $tabsent  += $absent;
                    // Late / UT
                    if($lateutlec){
                        $tlec += $this->attcompute->exp_time($lateutlec);
                    }
                }   // end foreach
                
                // Leave
                if($dispLogDate){
                    $tel      += $el;
                    $tvl      += $vl;
                    $tsl      += $sl;
                    $tol      += ($ol ? 1 : "");
                }
                // total holiday
                $tholiday += $holiday;
                
                /* Overtime */
                // total regular
                if($otreg){
                    $totr += $this->attcompute->exp_time($otreg);
                }
                // total saturday
                if($otsat){
                    $totsat += $this->attcompute->exp_time($otsat);
                }
                    // total sunday
                if($otsun){
                    $totsun += $this->attcompute->exp_time($otsun);
                }
                // total holiday
                if($othol){
                    $tothol += $this->attcompute->exp_time($othol);
                }
            } // end if                  
        } // end foreach
        $tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "");           
        $totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "");
        $totsat = ($totsat ? $this->attcompute->sec_to_hm($totsat) : ""); 
        $totsun = ($totsun ? $this->attcompute->sec_to_hm($totsun) : ""); 
        $tothol = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "");
            
        // Save to database
        $query = $this->db->query("SELECT * FROM attendance_confirmed_nt WHERE cutoffstart='$from_date' AND cutoffend='$to_date' AND employeeid='$empid'");
        if($query->num_rows() == 0){
            $this->db->query("INSERT INTO attendance_confirmed_nt (employeeid,cutoffstart,cutoffend,otreg,otsat,otsun,othol,lateut,absent,eleave,vleave,sleave,oleave,isholiday) 
                                VALUES ('$empid','$from_date','$to_date','$totr','$totsat','$totsun','$tothol','$tlec','$tabsent','$tel','$tvl','$tsl','$ol','$tholiday')");
        }
        }
        
    }
    function confirmatt_nt($data){
        $user = $this->session->userdata("username");
        $logdate    = date("Y-m-d",strtotime($data['logdate']));
        $schedstart = date("H:i:s",strtotime($data['schedstart']));
        $schedend   = date("H:i:s",strtotime($data['schedend']));
        $timein     = ($data['timein'] != "--") ? date("H:i:s",strtotime($data['timein']))  : "";
        $timeout    = ($data['timeout'] != "--") ? date("H:i:s",strtotime($data['timeout'])) : "";
        $query = $this->db->query("SELECT logdate FROM attendance_confirmed_nt WHERE logdate='$logdate' AND schedstart='$schedstart' AND schedend='$schedend' AND timein='$timein' AND timeout='$timeout'");
        if($query->num_rows() == 0){
            $this->db->query("INSERT INTO attendance_confirmed_nt (employeeid,logdate,schedstart,schedend,timein,timeout,otreg,otsat,otsun,othol,lateut,absent,eleave,vleave,sleave,oleave) 
                                VALUES ('$user','$logdate','$schedstart','$schedend','$timein','$timeout','{$data['otr']}','{$data['otsat']}','{$data['otsun']}','{$data['othol']}','{$data['lateut']}','{$data['absent']}','{$data['eleave']}','{$data['vleave']}','{$data['sleave']}','{$data['othleave']}')");
        }
    }
    
    function checkconfirmed_att($data){
        $user = $this->session->userdata("username");
        if($this->employee->getempdatacol("teachingtype") == "teaching"){
            $query = $this->db->query("SELECT * FROM attendance_confirmed WHERE cutoffstart = '{$data['dfrom']}' AND cutoffend = '{$data['dto']}' AND employeeid='$user'");
        }else{
            $query = $this->db->query("SELECT * FROM attendance_confirmed_nt WHERE cutoffstart = '{$data['dfrom']}' AND cutoffend = '{$data['dto']}' AND employeeid='$user'");
        }
        if($query->num_rows() > 0)  return true;
        else                        return false;
    }
    
    function hrconfirmatt($data){
        $empid      = $data['empid']; 
        $tnt        = $this->employee->getempdatacol("teachingtype",$empid);
        $from_date  = $data['dfrom'];
        $to_date    = $data['dto'];
        $edata      = "NEW";
        $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
        if($tnt == "teaching"){ // Teaching
            $tlec = $tlab = $tabsent = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $el = $vl = $sl = $ol = ""; 
            foreach ($qdate as $rdate) {
                $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
                $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                $countrow = $sched->num_rows();
                
                if($countrow > 0){
                    $tempsched = "";
                    foreach($sched->result() as $rsched){
                        if($tempsched == $dispLogDate)  $dispLogDate = "";
                        $stime = $rsched->starttime;
                        $etime = $rsched->endtime; 
                        $type  = $rsched->leclab;
                        
                        // logtime
                        list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                        // Leave
                        list($el,$vl,$sl,$ol)     = $this->attcompute->displayLeave($empid,$rdate->dte);
                        // Absent
                        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte);
                        if($el || $vl || $sl || $ol) $absent = "";
                        // Late / Undertime
                        list($lateutlec,$lateutlab,$tschedlec,$tschedlab) = $this->attcompute->displayLateUT($stime,$etime,$login,$logout,$type,$absent);
                        if($el || $vl || $sl || $ol){
                            $lateutlec = "";
                        }
                        
                        /*
                         * Total
                         */ 
                        // Absent
                        $tabsent  += $absent;
                        // Late / UT
                        if($lateutlec){
                            $tlec += $this->attcompute->exp_time($lateutlec);
                        }
                        if($lateutlab){
                            $tlab += $this->attcompute->exp_time($lateutlab);
                        }
                        // Deductions
                        if($tschedlec){
                            $tdlec += $this->attcompute->exp_time($tschedlec);
                        }
                        if($tschedlab){
                            $tdlab += $this->attcompute->exp_time($tschedlab);
                        }
                        
                    }   // end foreach
                    // Leave
                    if($dispLogDate){
                        $tel      += $el;
                        $tvl      += $vl;
                        $tsl      += $sl;
                        $tol      += $ol;
                    } // end if
                } // end if
            }
            $tlec = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "");
            $tlab = ($tlab ? $this->attcompute->sec_to_hm($tlab) : "");
            $tdlec = ($tdlec ? $this->attcompute->sec_to_hm($tdlec) : "");
            $tdlab = ($tdlab ? $this->attcompute->sec_to_hm($tdlab) : "");
            
            // Save to database
            $query = $this->db->query("SELECT * FROM attendance_confirmed WHERE cutoffstart='$from_date' AND cutoffend='$to_date' AND employeeid='$empid'");
            if($query->num_rows() == 0){
                $this->db->query("INSERT INTO attendance_confirmed (employeeid,cutoffstart,cutoffend,latelec,latelab,absent,eleave,vleave,sleave,oleave,deduclec,deduclab) 
                                    VALUES ('$empid','$from_date','$to_date','$tlec','$tlab','$tabsent','$tel','$tvl','$tsl','$tol','$tdlec','$tdlab')");
            }
                        
        }else{ // Non Teaching
                $totr = $totsat = $totsun = $tothol =$tlec = $tabsent = $tel = $tvl = $tsl = $tdlec = $tdlab = $el = $vl = $sl = $ol = $tholiday = ""; 
                foreach ($qdate as $rdate) {
                $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
                $sched = $this->attcompute->displaySched($empid,$rdate->dte);
                $countrow = $sched->num_rows();
                
                if($countrow > 0){#
                    $tempsched = "";
                    foreach($sched->result() as $rsched){
                        if($tempsched == $dispLogDate)  $dispLogDate = "";
                        $stime = $rsched->starttime;
                        $etime = $rsched->endtime; 
                        $type  = $rsched->leclab;
                        $earlyd = $rsched->early_dismissal;
                        
                        // Holiday
                        $holiday = $this->attcompute->isHoliday($rdate->dte); 
                        
                        // logtime
                        list($login,$logout,$q) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata);
                        
                        // Overtime
                        list($otreg,$otsat,$otsun,$othol) = $this->attcompute->displayOt($empid,$rdate->dte);
                                
                        // Leave
                        list($el,$vl,$sl,$ol)     = $this->attcompute->displayLeave($empid,$rdate->dte);
                        
                        // Absent
                        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd);
                        if($el || $vl || $sl || $ol || $holiday) $absent = "";
                            
                        // Late / Undertime
                        $lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent);
                        if($el || $vl || $sl || $ol || $holiday) $lateutlec = "";
                        
                        /*
                         * Total
                         */ 
                        // Absent
                        $tabsent  += $absent;
                        // Late / UT
                        if($lateutlec)  $tlec += $this->attcompute->exp_time($lateutlec);
                    }   // end foreach
                    
                    // Leave
                    if($dispLogDate){
                        $tel      += $el;
                        $tvl      += $vl;
                        $tsl      += $sl;
                        $ol       += ($ol ? 1 : 0);  
                    }
                    // total holiday
                    $tholiday += $holiday;
                    
                    /* Overtime */
                    // total regular
                    if($otreg){
                        $totr += $this->attcompute->exp_time($otreg);
                    }
                    // total saturday
                    if($otsat){
                        $totsat += $this->attcompute->exp_time($otsat);
                    }
                    // total sunday
                    if($otsun){
                        $totsun += $this->attcompute->exp_time($otsun);
                    }
                    // total holiday
                    if($othol){
                        $tothol += $this->attcompute->exp_time($othol);
                    }
                } // end if                  
                } // end foreach
            $tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "");       
            $totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "");
            $totsat = ($totsat ? $this->attcompute->sec_to_hm($totsat) : ""); 
            $totsun = ($totsun ? $this->attcompute->sec_to_hm($totsun) : ""); 
            $tothol = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "");
            
            // Save to database
            $query = $this->db->query("SELECT * FROM attendance_confirmed_nt WHERE cutoffstart='$from_date' AND cutoffend='$to_date' AND employeeid='$empid'");
            if($query->num_rows() == 0){
                $this->db->query("INSERT INTO attendance_confirmed_nt (employeeid,cutoffstart,cutoffend,otreg,otsat,otsun,othol,lateut,absent,eleave,vleave,sleave,oleave,isholiday) 
                                    VALUES ('$empid','$from_date','$to_date','$totr','$totsat','$totsun','$tothol','$tlec','$tabsent','$tel','$tvl','$tsl','$ol','$tholiday')");
            }
        }
        return " Cut-Off from ".date("F d, Y",strtotime($from_date))." to ".date("F d, Y",strtotime($to_date))." for employee no. $empid is successfully confirmed!."; 
    }
    
    function payrollconfirm($data){
        $tnt        = $data['tnt'];
        $from_date  = $data['dfrom'];
        $to_date    = $data['dto'];
        $empid      = $data['eid'];
        $ins = $return = "";
        
        if($tnt == "teaching"){
            $notconf    = $this->attendance->emp_not_yet_confirmed($from_date, $to_date, $tnt);
            if(count($notconf) == 0){
                $query = $this->attendance->emp_confirmed($from_date, $to_date, $tnt, $empid);
                if(count($query) > 0){
                    foreach ($query as $key => $data) { 
                        $tlec   = $data["latelec"];
                        $tlab   = $data["latelab"];
                        $tabsent= $data["absent"]; 
                        $tel    = $data["eleave"];
                        $tvl    = $data["vleave"];
                        $tsl    = $data["sleave"];
                        $tol    = $data["oleave"];         
                        $tdlec  = $data["deduclec"];
                        $tdlab  = $data["deduclab"];
                        $ins    = $this->db->query("INSERT INTO payroll_employee_attendance (employeeid,cutoffstart,cutoffend,latelec,latelab,absent,eleave,vleave,sleave,oleave,deduclec,deduclab) VALUES ('$empid','$from_date','$to_date','$tlec','$tlab','$tabsent','$tel','$tvl','$tsl','$tol','$tdlec','$tdlab')");
                                  $this->db->query("UPDATE attendance_confirmed SET status='SUBMITTED' WHERE employeeid='$empid' AND cutoffstart='$from_date' AND cutoffend='$to_date'"); 
                    }
                    $this->db->query("UPDATE cutoff SET TPostedDate=NOW() WHERE CutoffFrom='$from_date' AND CutoffTo='$to_date'");  
                }
            }else   $return = array(0,"Finalize Failed!. All employee's are not yet confirmed!.");        
        }else{  // Non Teaching
            $notconf    = $this->attendance->emp_not_yet_confirmed_nt($from_date, $to_date, $tnt);
            if(count($notconf) == 0){
                $query = $this->attendance->emp_confirmed_nt($from_date, $to_date, $tnt, $empid);
                if(count($query) > 0){
                    foreach ($query as $key => $data) { 
                        $totr = $data["otreg"];
                        $totsat = $data["otsat"];
                        $totsun = $data["otsun"];
                        $tothol = $data["othol"]; 
                        $tlec   = $data["lateut"];
                        $tabsent= $data["absent"];
                        $tel    = $data["eleave"];
                        $tvl    = $data["vleave"];
                        $tsl    = $data["sleave"];
                        $tol    = $data["oleave"];
                        $ishol  = $data["isholiday"]; 
                        $ins    = $this->db->query("INSERT INTO payroll_employee_attendance_nt (employeeid,cutoffstart,cutoffend,otreg,otsat,otsun,othol,lateut,absent,eleave,vleave,sleave,oleave,isholiday) VALUES ('$empid','$from_date','$to_date','$totr','$totsat','$totsun','$tothol','$tlec','$tabsent','$tel','$tvl','$tsl','$tol','$ishol')"); 
                                  $this->db->query("UPDATE attendance_confirmed SET status='SUBMITTED' WHERE employeeid='$empid' AND cutoffstart='$from_date' AND cutoffend='$to_date'"); 
                    }
                    $this->db->query("UPDATE cutoff SET NTPostedDate=NOW() WHERE CutoffFrom='$from_date' AND CutoffTo='$to_date'");
                }
            }else   $return = array(0,"Finalize Failed!. All employee's are not yet confirmed!.");   
            
        }
        if(!$return){
            if($ins)  $return = array(1,"Cut-Off from ".date("F d, Y",strtotime($from_date))." - ".date("F d, Y",strtotime($to_date))." for Employee No. : $empid is successfully finalized!");
            else      $return = array(0,"Finalize Failed!.");
        }
        return json_encode($return); 
    }
    
    function showFinalize($dfrom,$dto,$tnt){
        if($tnt == "teaching")
            $query = $this->db->query("SELECT * FROM payroll_employee_attendance WHERE cutoffstart='$dfrom' AND cutoffend='$dto'");
        else
            $query = $this->db->query("SELECT * FROM payroll_employee_attendance_nt WHERE cutoffstart='$dfrom' AND cutoffend='$dto'");
        if($query->num_rows() == 0)  return true;
        else                         return false;
    }
    
    function addAbsent($data){
        $user       = $this->session->userdata("username");
        $sjob       = $data['sjob'];
        $eid        = $data['eid'];
        $scheddate  = ($data['scheddate'] ? $data['scheddate'] : "");
        $schedstart = ($data['schedstart'] ? $data['schedstart'] : "");
        $schedend   = ($data['schedend'] ? $data['schedend'] : "");
        $absent     = ($data['absent'] ? $data['absent'] : "");
        $idkey      = ($data['idkey'] ? $data['idkey'] : "");
        if($sjob == "addAbsent"){
            $query = $this->db->query("INSERT INTO attendance_absent_checker VALUES ('','$eid','$scheddate','$schedstart','$schedend','$absent','$user',CURRENT_TIMESTAMP)");
        }else{
            $query = $this->db->query("DELETE FROM attendance_absent_checker WHERE id='$idkey'");
        }
        return $return;
    }
    
    function checktagAbsent($eid,$scheddate,$schedstart,$schedend){
        $id = "";
        $query = $this->db->query("SELECT * FROM attendance_absent_checker WHERE employeeid='$eid' AND scheddate='$scheddate' AND schedstart='$schedstart' AND schedend='$schedend'");
        if($query->num_rows() > 0)  $id = $query->row(0)->id;
        return $id;
    }
    
    // Process the request
    function requestsched($data){
        $msg = "";
        if($data["timesched"]){
              $eid = $data["eids"];  
              $dtime = $data["dfrom"]." 00:00:00";
              $head = $this->session->userdata("username");
              $user = $this->session->userdata("userid");
              $sched_list = explode("|",$data["timesched"]);
              
              $qdept  = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$head'");
              $deptid = ($qdept->num_rows() > 0 ? $qdept->row(0)->deptid : "");
                
              if($deptid){
                $qdhead = $this->db->query("SELECT head,divisionhead FROM code_office WHERE code='$deptid'");
                $chead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->divisionhead : "");                 // Cluster head
                $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
                $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                             // HR Director
                if($chead){
                    
                    foreach(explode(",",$eid) as $empid){
                      /** Insert new data */
                      if($empid == $head)
                        $this->db->query("INSERT INTO change_schedule_request (employeeid,dhead,chead,hrd,isread) VALUES ('$empid','$head','$chead','$hrd',1)");
                      else
                        $this->db->query("INSERT INTO change_schedule_request (employeeid,dhead,chead,hrd) VALUES ('$empid','$head','$chead','$hrd')");
                      $query = $this->db->query("SELECT * FROM change_schedule_request WHERE employeeid='$empid' ORDER BY id DESC LIMIT 1");
                      if($query->num_rows() > 0){
                        $id = $query->row(0)->id;
                        if(!empty($chead))  $this->db->query("INSERT INTO change_schedule_request_chead (id,employeeid,head) VALUES ('$id','{$empid}','{$chead}')");
                          foreach($sched_list as $slist){
                            $nosched = 0;
                            $halfsched = 0;
                            list($dow,$idx,$tsched,$tardy,$absent,$halfabsent,$earlyd,$leclab) = explode("~u~",$slist);
                              $extsched = explode("-",$tsched);
                              $start_time = date("H:i:s",strtotime($extsched[0]));
                              $end_time = date("H:i:s",strtotime($extsched[1]));
                              $tardy = $tardy ? date("H:i:s",strtotime($tardy)) : "";
                              $absent = $absent ? date("H:i:s",strtotime($absent)) : "";
                              $halfabsent = $halfabsent ? date("H:i:s",strtotime($halfabsent)) : "";
                              $earlyd = $earlyd ? date("H:i:s",strtotime($earlyd)) : "";
                              $this->db->query("INSERT INTO change_schedule(id,employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab, editedby, dateedit) 
        			                             VALUES('$id','$empid','$start_time','$end_time','$dow','$idx','$tardy','$absent','$halfabsent','$earlyd','$leclab','$user','$dtime')");
                          }
                      }
                    }
                $msg = "Schedule Successfully Requested!.";
                }else
                $msg = "Ooops, Failed! Your department has no cluster head.";
            }
        }
        return $msg;
    }
    function approvedSched($data){
        $msg    = "";
        $id     = $data['id'];
        $status = $data['status'];
        
        $qdhead = $this->db->query("SELECT head FROM change_schedule_request_chead WHERE id='$id'");
        $chead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->head : "");                 // Cluster head                
        $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
        $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                             // HR Director
        if($chead == $this->session->userdata("username")){
            if($hrd){
                $query = $this->db->query("SELECT * FROM change_schedule_request WHERE id='$id'");
                if($query->num_rows() > 0){
                 if($status != "DISAPPROVED")
                 $this->db->query("INSERT INTO change_schedule_request_hrd (id,employeeid,head) VALUES ('$id','".$query->row(0)->employeeid."','".$query->row(0)->hrd."')");
                 $this->db->query("UPDATE change_schedule_request SET cheadstatus='$status',cheaddate=CURRENT_DATE WHERE id='$id'");
                 $this->db->query("UPDATE change_schedule_request_chead SET status='$status' WHERE id='$id'");
                 $msg = "Successfully $status!.";   
                }
            }else$msg = "Failed to approve!. Hr Director is not exists..";    
        }else{
            if($status == "APPROVED"){
                $query = $this->db->query("SELECT employeeid FROM change_schedule_request_hrd WHERE id='$id'");
                if($query->num_rows() > 0){
                    $eid = $query->row(0)->employeeid;
                    $this->db->query("DELETE FROM employee_schedule WHERE employeeid = '$eid'");
                    $this->db->query("INSERT INTO employee_schedule(employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,editedby,dateedit) 
			                             (SELECT employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,editedby,CONCAT((DATE(dateedit)-INTERVAL 1 DAY),' 00:00:00') FROM change_schedule WHERE employeeid='{$eid}' AND id='$id')");
                    $this->db->query("INSERT INTO employee_schedule_history(employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,changeby,dateactive) 
			                             (SELECT employeeid, starttime, endtime, dayofweek, idx, tardy_start, absent_start, absent_half_start, early_dismissal, leclab,editedby,CONCAT((DATE(dateedit)-INTERVAL 1 DAY),' 00:00:00') FROM change_schedule WHERE employeeid='{$eid}' AND id='$id')");
                }
                $this->db->query("UPDATE change_schedule_request SET isread=0 WHERE id='$id'");
            }
            $this->db->query("UPDATE change_schedule_request SET hrdstatus='$status',status='$status',hrddate=CURRENT_DATE WHERE id='$id'");
            $this->db->query("UPDATE change_schedule_request_hrd SET status='$status' WHERE id='$id'");
            $msg = "Successfully $status!.";
        }
        return $msg;
    }
    
    function modify_offbus_time($data){
        $return = "";
        foreach($data as $key=>$val)    $$key = $val;
        
        if($newtfrom != $oldtfrom || $newtto != $oldtto){
            $newtfrom   = date("H:i:s",strtotime($newtfrom));
            $newtto     = date("H:i:s",strtotime($newtto));
            $oldtfrom   = date("H:i:s",strtotime($oldtfrom));
            $oldtto     = date("H:i:s",strtotime($oldtto));
            $this->db->query("INSERT INTO off_business_timehistory (aid,employeeid,timein,timeout,newtimein,newtimeout,user) VALUES ('$aid','$eid','$oldtfrom','$oldtto','$newtfrom','$newtto','".$this->session->userdata("username")."')");
            $this->db->query("UPDATE leave_app SET timefrom='$newtfrom', timeto='$newtto' WHERE id='$aid' AND employeeid='$eid'");
            $this->db->query("UPDATE leave_app_dhead SET timefrom='$newtfrom', timeto='$newtto' WHERE aid='$aid' AND employeeid='$eid'");
            $this->db->query("UPDATE leave_app_chead SET timefrom='$newtfrom', timeto='$newtto' WHERE aid='$aid' AND employeeid='$eid'");
            $this->db->query("UPDATE leave_app_uphy SET timefrom='$newtfrom', timeto='$newtto' WHERE aid='$aid' AND employeeid='$eid'");
            $this->db->query("UPDATE leave_app_hrd SET timefrom='$newtfrom', timeto='$newtto', otimefrom='$oldtfrom', otimeto='$oldtto' WHERE aid='$aid' AND employeeid='$eid'");
            $return = "Successfully Saved!.";
        }else{
            $return = "Nothing changes in time..";
        }
        
        return $return;
    }
    
    function offBuss_daytime($id="",$dfrom="",$dto=""){
        $wC = "";
        if($id)             $wC = " WHERE userid='$id'";
        if($dfrom && $dto)  $wC .= " AND DATE(timein) BETWEEN '$dfrom' AND '$dto'";  
        $query = $this->db->query("SELECT * FROM timesheet $wC");
        return $query;
    }
    
    function changepass($data){
        $return = "";
        $user = $this->session->userdata("username");
        $oldpass = $data['oldpass'];
        $newpass = $data['newpass'];
        $retpass = $data['retpass'];
        if($newpass == $retpass){
            $query = $this->db->query("SELECT * FROM user_info WHERE username='$user'");
            if($query->num_rows() == 1){
                $this->db->query("UPDATE user_info SET password=MD5('$retpass') WHERE username='$user'");
                $return = "Successfully Saved!.";
            }else
                $return = "Incorrect Current Password..";
        }else   $return = "Password did not matched!.";
        return $return;
    }
    
    function changeppass($data){
        $return = "";
        $user = $this->session->userdata("username");
        $newpass = $data['newpass'];
        $query = $this->db->query("SELECT * FROM user_info WHERE username='$user'");
        if($query->num_rows() > 0)
            $this->db->query("UPDATE user_info SET ppass='$newpass' WHERE username='$user'");
        $return = "Successfully Saved!.";
        return $return;
    }

    function saveemployeeeducation($employeeid,$user,$datas){
        list($eb_school,$eb_level,$eb_year_graduated,$eb_honor) = explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_education (employeeid,educational_level,school,minor,year_graduated,modified_by,modified_on,educ_level)
                                VALUES ('{$employeeid}','','{$eb_school}','{$eb_honor}','{$eb_year_graduated}','{$user}','".date('Y-m-d h:i:s')."','{$eb_level}')
                            ");
        return $query;
    }

    function saveEmployeeEligibilities($employeeid,$datas){
        list($el_description,$el_center,$el_date) = explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_eligibilities (employeeid,date_issued,educ_level,affiliating_center)
                                VALUES ('{$employeeid}','{$el_date}','{$el_description}','{$el_center}')
                            ");
      
        return $query;
    }

    function saveEmployeeSCTT($employeeid,$datas){
        list($el_subj_id,$el_remarks) = explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_subj_competent_to_teach (employeeid,subj_id,remarks)
                                VALUES ('{$employeeid}','{$el_subj_id}','{$el_remarks}')
                            ");
      
        return $query;
    }

    function saveEmployeePgd($employeeid,$user,$datas){
        list($educ_level,$title,$type,$date) = explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_pgd (employeeid,educ_level,title,`type`,datef,modified_by)
                                VALUES ('{$employeeid}','{$educ_level}','{$title}','{$type}','{$date}','{$user}')
                            ");
        return $query;
    }
    function saveEmployeeResearches($employeeid,$user,$datas){
        list($date,$educ_level,$title) = explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_researches (employeeid,educ_level,date_published,title,modified_by,modified_on)
                                VALUES ('{$employeeid}','{$educ_level}','{$date}','{$title}','{$user}','".date('Y-m-d h:i:s')."')
                            ");
        return $query;
    }

    function saveEmployeeAwardsRecog($employeeid,$user,$datas){
        list($educ_level,$title,$type,$date) = explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_awardsrecog (employeeid,educ_level,title,`type`,datef,modified_by)
                                VALUES ('{$employeeid}','{$educ_level}','{$title}','{$type}','{$date}','{$user}')
                            ");
        return $query;
    }

    function saveEmployeeScholarship($employeeid,$user,$datas){
        list($educ_level,$title,$type,$date) = explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_scholarship (employeeid,educ_level,title,`type`,datef,modified_by)
                                VALUES ('{$employeeid}','{$educ_level}','{$title}','{$type}','{$date}','{$user}')
                            ");
        return $query;
    }

    function saveEmployeeScs($employeeid,$user,$datas){
        list($sm_school,$educ_level,$sm_year_graduated,$sm_honor,$sm_type,$sm_location) = explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_scs (employeeid,educ_level,school,honor,year_graduated,modified_by,modified_on,`type`,location)
                                VALUES ('{$employeeid}','{$educ_level}','{$sm_school}','{$sm_honor}','{$sm_year_graduated}','{$user}','".date('Y-m-d h:i:s')."','{$sm_type}','{$sm_location}')
                            ");
        return $query;
    }

    function saveEmployeeTraining($employeeid,$user,$datas){
        list($sm_school,$educ_level,$sm_year_graduated,$sm_honor,$sm_type,$sm_location)= explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_workshops (employeeid,educ_level,school,honor,year_graduated,modified_by,modified_on,`type`,location)
                                VALUES ('{$employeeid}','{$educ_level}','{$sm_school}','{$sm_honor}','{$sm_year_graduated}','{$user}','".date('Y-m-d h:i:s')."','{$sm_type}','{$sm_location}')
                            ");
        return $query;
    }

    function saveEmployeeResource($employeeid,$user,$datas){
        list($sm_school,$educ_level,$sm_year_graduated,$sm_honor,$sm_type,$sm_location)= explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_resource (employeeid,educ_level,school,honor,year_graduated,modified_by,modified_on,`type`,location)
                                VALUES ('{$employeeid}','{$educ_level}','{$sm_school}','{$sm_honor}','{$sm_year_graduated}','{$user}','".date('Y-m-d h:i:s')."','{$sm_type}','{$sm_location}')
                            ");
        return $query;
    }

    function saveEmployeeOrganization($employeeid,$user,$datas){
         list($sm_school,$sm_level,$sm_year_graduated,$sm_honor)= explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_proorg (employeeid,educ_level,school,honor,year_graduated,modified_by,modified_on)
                                VALUES ('{$employeeid}','{$sm_level}','{$sm_school}','{$sm_honor}','{$sm_year_graduated}','{$user}','".date('Y-m-d h:i:s')."')
                            ");
        return $query;
    }

    function saveEmployeeAdministrative($employeeid,$user,$datas){
         list($sm_school,$sm_level,$sm_year_graduated,$sm_honor)= explode("~u~",$datas);
        $query="";
        $query   = $this->db->query("
                            INSERT INTO employee_administrative (employeeid,educ_level,school,honor,year_graduated,modified_by,modified_on)
                                VALUES ('{$employeeid}','{$sm_level}','{$sm_school}','{$sm_honor}','{$sm_year_graduated}','{$user}','".date('Y-m-d h:i:s')."')
                            ");
        return $query;
    }
}
?>