<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Attendance extends CI_Model {
    private $base_sqlquery;
    private $from_date;
    private $to_date;

    private $indSummQuery;
    private $attSummQuery;

    /**
    * Attendance class constructor
    */
    public function __construct(){
        parent::__construct();
    }

    public function initialize($fdate = '', $tdate = ''){
        $this->from_date = $fdate;
        $this->to_date = $tdate;
        //$this->base_sqlquery = "select";
    }

    /**
    * queries database for attendance details
    * of employee(s) for summary reports
    * @param (date) start date to cover
    * @param (date) end date to cover
    * @param (string) employeeid (if any)
    * @param (string) deptid (if any)
    */
    public function giveIndividualSummary($from_date1 = '', $to_date1 = '', $empid = '', $edata = ''){
        $from_date = date("Y-m-d",strtotime($from_date1));
        $to_date = date("Y-m-d",strtotime($to_date1));
        if($edata == "OLD") $tbl = "timesheet_bak"; 
        else                $tbl = "timesheet";
        $this->indSummQuery = "call proc_individual_attendance_summary('".$empid."','".$from_date."','".$to_date."','".$tbl."')";
        mysqli_next_result($this->db->conn_id);
        return $this->db->query($this->indSummQuery)->result_array();
    }
    public function att_summary($from_date1 = '', $to_date1 = '', $empid = ''){
        $from_date = date("Y-m-d",strtotime($from_date1));
        $to_date = date("Y-m-d",strtotime($to_date1));
        $query = $this->db->query("SELECT SUM(tlate) AS late, SUM(tminlate) AS tminlate, SUM(tthlate) AS tthlate, SUM(tovertime) AS tovertime, SUM(tminovertime) AS tminovertime, SUM(tearlydismissal) AS  tearlydismissal, SUM(tearlymindismissal) AS  tearlymindismissal, 
                                    SUM(tabsent) AS tabsent, SUM(tleave) AS tleave, SUM(thalfday) AS thalfday, SUM(tfailuretolog) AS tfailuretolog, SUM(tnoholiday) AS tnoholiday
                                    FROM employee_att_summary WHERE employeeid='$empid' AND dfrom BETWEEN '$from_date1' AND '$to_date1';");

        return $query;        
    }
    /*
    public function otSummary($from_date1 = '', $to_date1 = '', $empid = ''){
        $from_date = date("Y-m-d",strtotime($from_date1));
        $to_date = date("Y-m-d",strtotime($to_date1));

        $this->indSummQuery = "call proc_individual_attendance_summary_ot('".$empid."','".$from_date."','".$to_date."')";
        mysqli_next_result($this->db->conn_id);
        return $this->db->query($this->indSummQuery)->result_array();
    }
    */
    public function giveAttendanceSummary($from_date = '', $to_date = '', $empid = '', $deptid = '', $tnt = '', $estatus = '',$campus=''){

        $condition = ($empid != "") 
            ? " and (TRIM(a.employeeid)='".$empid."' ) " : "";

        $condition .= ($deptid != "") ? " and a.deptid='$deptid' " : "";
        
        if($tnt)     $condition .= " AND a.teachingtype='$tnt'";
        if($estatus) $condition .= " AND a.employmentstat='$estatus'";
        if($campus)  $condition .= " AND a.campusid ='$campus'";
        $this->attSummQuery = "
            SELECT employeeid as qEmpId,deptid as qDeptId, CONCAT(lname,', ',fname,' ',mname) AS qFullname, b.description AS qDepartment,teachingtype
                FROM employee a
                INNER JOIN code_office b ON a.deptid = b.code 
                WHERE (a.dateresigned < a.dateposition OR a.dateresigned = '0000-00-00' OR a.dateresigned IS NULL) $condition GROUP BY a.employeeid ORDER BY deptid, qFullname";                
        
        return $this->db->query($this->attSummQuery)->result_array();
    }

    public function constructEmpListHaveSched($emplist, $date){
        $new_emplist = array();

        foreach ($emplist as $key => $info) {
            $employeeid = $info["qEmpId"];

            $q_sched = $this->db->query("SELECT * FROM employee_schedule_history WHERE employeeid='$employeeid' AND DATE_FORMAT(dateactive, '%Y-%m-%d') <= '$date';")->result();            
            if(count($q_sched) > 0) $new_emplist[] = $info;
        }

        return $new_emplist;
    }
    
    public function emp_not_yet_confirmed($dfrom="", $dto="", $tnt="",$employeeid="",$payroll_start="",$payroll_end="",$deptid="",$campus="",$office="",$isactive="",$employmentstat=""){
        $ifPayrollProcess = '';
        $wC = '';
        $datenow = date("Y-m-d");
        if($employeeid) $wC .= " AND a.employeeid='$employeeid'";
        if($deptid) $wC .= " AND a.deptid='$deptid'";
        if($office) $wC .= " AND a.office='$office'";
        if($campus) $wC .= " AND a.campusid='$campus'";
        if($employmentstat) $wC .= " AND a.employmentstat='$employmentstat'"; 
        if($isactive!="" && $isactive != 'undefined')   $wC .= " AND isactive='$isactive'";
        if($payroll_start && $payroll_end) $ifPayrollProcess = " AND a.employeeid NOT IN (SELECT employeeid FROM payroll_computed_table c WHERE a.employeeid = c.employeeid AND c.cutoffstart = '$payroll_start' AND cutoffend = '$payroll_end' AND status = 'PROCESSED' ) ";
        $this->attSummQuery = "
            SELECT a.employeeid as qEmpId,office as qDeptId, CONCAT(lname,', ',fname,' ',mname) AS qFullname, b.description AS qDepartment
                FROM employee a
                INNER JOIN code_office b ON a.office = b.code 
                INNER JOIN payroll_employee_salary d ON d.employeeid=a.employeeid
                WHERE ('$datenow' < a.dateresigned2 OR a.dateresigned2 = '0000-00-00' OR a.dateresigned2 = '1970-01-01' OR a.dateresigned2 IS NULL) AND a.isactive = '1'  AND a.employeeid NOT IN (SELECT employeeid FROM attendance_confirmed c WHERE a.employeeid = c.employeeid AND c.cutoffstart = '$dfrom' AND cutoffend = '$dto' ) $ifPayrollProcess
                AND a.teachingtype='$tnt'/* AND isactive = '1'*/ $wC
                GROUP BY a.employeeid ORDER BY qDepartment, qFullname";                
        return $this->db->query($this->attSummQuery)->result_array();
    }
    public function emp_not_yet_confirmed_nt($dfrom="", $dto="", $tnt="",$employeeid="",$payroll_start="",$payroll_end="",$deptid="",$campus="",$office="",$isactive="",$employmentstat=""){
        $ifPayrollProcess = '';
        $wC = '';
        $datenow = date("Y-m-d");
        if($employeeid) $wC .= " AND a.employeeid='$employeeid'";
        if($deptid) $wC .= " AND a.deptid='$deptid'";
        if($office) $wC .= " AND a.office='$office'";
        if($campus) $wC .= " AND a.campusid='$campus'";
        if($employmentstat) $wC .= " AND a.employmentstat='$employmentstat'"; 
        if($isactive!="" && $isactive != 'undefined')   $wC .= " AND isactive='$isactive'";
        if($payroll_start && $payroll_end) $ifPayrollProcess = " AND a.employeeid NOT IN (SELECT employeeid FROM payroll_computed_table c WHERE a.employeeid = c.employeeid AND c.cutoffstart = '$payroll_start' AND cutoffend = '$payroll_end' AND status = 'PROCESSED' ) ";
        $this->attSummQuery = "
            SELECT a.employeeid as qEmpId,office as qDeptId, CONCAT(lname,', ',fname,' ',mname) AS qFullname, b.description AS qDepartment
                FROM employee a
                INNER JOIN code_office b ON a.office = b.code 
                INNER JOIN payroll_employee_salary d ON d.employeeid=a.employeeid
                WHERE ('$datenow' < a.dateresigned2 OR a.dateresigned2 = '0000-00-00' OR a.dateresigned2 = '1970-01-01' OR a.dateresigned2 IS NULL) AND a.isactive = '1' AND  a.employeeid NOT IN (SELECT employeeid FROM attendance_confirmed_nt c 
                        WHERE a.employeeid = c.employeeid AND c.cutoffstart = '$dfrom' AND cutoffend = '$dto' ) $ifPayrollProcess
                    AND a.teachingtype='$tnt'/* AND isactive = '1'*/  $wC
                GROUP BY a.employeeid ORDER BY qDepartment, qFullname";                
        return $this->db->query($this->attSummQuery)->result_array();
    }
    
    public function emp_confirmed($dfrom="", $dto="", $tnt="", $eid="", $campus="", $deptid="", $office="", $category="", $isactive="", $employmentstat=""){
        $wC = "";
        $orderby = " ";
        $datenow = date("Y-m-d");
        if($eid) $wC .= " AND a.employeeid='$eid'";   
        if($campus) $wC .= " AND a.campusid='$campus'";
        if($deptid) $wC .= " AND a.deptid IN('$deptid')"; 
        if($office) $wC .= " AND a.office='$office'"; 
        if($employmentstat) $wC .= " AND a.employmentstat='$employmentstat'"; 
        if($isactive!="" && $isactive != 'undefined')   $wC .= " AND isactive='$isactive'";

        if($category == "campus")  $sort = " ORDER BY campusid,qFullname,qDeptId";
        else if($category == "department")  $sort = " ORDER BY qDeptId,qFullname,campusid";
        else $sort = " ORDER BY qFullname,qDeptId,campusid";

        $this->attSummQuery = "
            SELECT a.employeeid as qEmpId, office as qDeptId, CONCAT(lname,', ',fname,' ',mname) AS qFullname, a.campusid as qCampusId, b.description AS qDepartment,DATE(c.timestamp) as dateconfirmed,c.status, c.*,d.fixedday, c.isFinal, c.hold_status
                FROM employee a
                INNER JOIN code_office b ON a.office = b.code 
                INNER JOIN attendance_confirmed c ON a.employeeid = c.employeeid
                INNER JOIN payroll_employee_salary d ON d.employeeid=a.employeeid
                WHERE ('$datenow' < a.dateresigned2 OR a.dateresigned2 = '0000-00-00' OR a.dateresigned2 = '1970-01-01' OR a.dateresigned2 IS NULL) AND a.isactive = '1' 
                    AND c.cutoffstart = '$dfrom' AND cutoffend = '$dto' AND a.teachingtype='$tnt' $wC GROUP BY a.employeeid $orderby";  
        return $this->db->query($this->attSummQuery)->result_array();
    }

     public function emp_confirmedsorting($dfrom="", $dto="", $tnt="", $eid="",$category="",$campus=""){
        $wC = "";
        $sort = "";
        if ($category=="campus") {
            if($campus) $wC .= " AND a.campusid ='{$campus}'";$sort = "ORDER BY a.campusid,qFullname";
        }   
        else
        {
            $sort .= "ORDER BY qFullname";
            //$sort .= "ORDER BY qFullname,depid";
        }
        if($eid) $wC .= " AND a.employeeid='$eid'";   
        $this->attSummQuery = "
            SELECT a.campusid,a.employeeid as qEmpId,office as qDeptId, CONCAT(lname,', ',fname,' ',mname) AS qFullname, b.description AS qDepartment,DATE(c.timestamp) as dateconfirmed, c.*,d.fixedday
                FROM employee a
                INNER JOIN code_office b ON a.office = b.code 
                INNER JOIN attendance_confirmed c ON a.employeeid = c.employeeid
                LEFT JOIN payroll_employee_salary d ON d.employeeid=a.employeeid
                WHERE (a.dateresigned = '1970-01-01' OR a.dateresigned IS NULL OR a.dateresigned = '0000-00-00') AND c.cutoffstart = '$dfrom' AND cutoffend = '$dto' AND a.teachingtype='$tnt' $wC GROUP BY a.employeeid {$sort}";                
        return $this->db->query($this->attSummQuery)->result_array();
    }   
    
    public function emp_confirmed_nt($dfrom="", $dto="", $tnt="", $eid="" , $campus="", $deptid="", $office="", $category = "", $isactive="", $employmentstat=""){
        $wC = $sort = "";
        $datenow = date("Y-m-d");
        if($eid) $wC .= " AND a.employeeid='$eid'";
        if($campus) $wC .= " AND a.campusid='$campus'";
        if($deptid) $wC .= " AND a.deptid IN('$deptid')"; 
        if($office) $wC .= " AND a.office='$office'"; 
        if($employmentstat) $wC .= " AND a.employmentstat='$employmentstat'"; 
        if($isactive!="" && $isactive != 'undefined')   $wC .= " AND isactive='$isactive'";

        if($category == "campus")  $sort = " ORDER BY campusid,qFullname,qDeptId";
        else if($category == "department")  $sort = " ORDER BY qDeptId,qFullname,campusid";
        else $sort = " ORDER BY qFullname,qDeptId,campusid";

        $this->attSummQuery = "
            SELECT a.employeeid as qEmpId,office as qDeptId, CONCAT(lname,', ',fname,' ',mname) AS qFullname, b.description AS qDepartment, a.campusid as qCampusId,DATE(c.timestamp) as dateconfirmed, c.*,d.fixedday, c.status, c.isFinal, c.hold_status
                FROM employee a
                INNER JOIN code_office b ON a.office = b.code 
                INNER JOIN attendance_confirmed_nt c ON a.employeeid = c.employeeid
                INNER JOIN payroll_employee_salary d ON d.employeeid=a.employeeid
                WHERE ('$datenow' < a.dateresigned2 OR a.dateresigned2 = '0000-00-00' OR a.dateresigned2 = '1970-01-01' OR a.dateresigned2 IS NULL) AND a.isactive = '1' 
                    AND c.cutoffstart = '$dfrom' AND cutoffend = '$dto' AND a.teachingtype='$tnt' $wC GROUP BY a.employeeid $sort";                
        return $this->db->query($this->attSummQuery)->result_array();
    }    
    //newly added 03/20/2018
     public function emp_confirmedperdept($dfrom="", $dto="", $tnt="", $eid="", $office="", $campus="", $deptid = ""){
        $wC = "";
        if($eid) $wC .= " AND a.employeeid='$eid'"; 
        if($office) $wC .= " AND a.office='$office'";
        if($campus) $wC .= " AND a.campusid='$campus'"; 
        if($deptid) $wC .= " AND a.deptid IN($deptid)";   
        $this->attSummQuery = "
            SELECT a.employeeid as qEmpId,office as qDeptId, CONCAT(lname,', ',fname,' ',mname) AS qFullname, b.description AS qDepartment,DATE(c.timestamp) as dateconfirmed, c.*,d.fixedday, a.campusid
                FROM employee a
                INNER JOIN code_office b ON a.office = b.code 
                INNER JOIN attendance_confirmed c ON a.employeeid = c.employeeid
                LEFT JOIN payroll_employee_salary d ON d.employeeid=a.employeeid
                WHERE (a.dateresigned = '1970-01-01' OR a.dateresigned = '0000-00-00' OR a.dateresigned IS NULL) AND c.cutoffstart = '$dfrom' AND cutoffend = '$dto' AND a.teachingtype='$tnt' $wC GROUP BY a.employeeid ORDER BY qFullname, qDeptId, campusid, ";                
        return $this->db->query($this->attSummQuery)->result_array();
    }
    public function emp_confirmed_ntperdept($dfrom="", $dto="", $tnt="", $eid="", $office="", $campus="", $deptid=""){
        $wC = "";
        if($eid) $wC .= " AND a.employeeid='$eid'";
        if($office) $wC .= " AND a.office='$office'";
        if($campus) $wC .= " AND a.campusid='$campus'";
        if($deptid) $wC .= " AND a.deptid IN($deptid)"; 
        $this->attSummQuery = "
            SELECT a.employeeid as qEmpId,deptid as qDeptId, CONCAT(lname,', ',fname,' ',mname) AS qFullname, b.description AS qDepartment,DATE(c.timestamp) as dateconfirmed, c.*,d.fixedday,a.campusid
                FROM employee a
                INNER JOIN code_office b ON a.office = b.code 
                INNER JOIN attendance_confirmed_nt c ON a.employeeid = c.employeeid
                LEFT JOIN payroll_employee_salary d ON d.employeeid=a.employeeid
                WHERE (a.dateresigned < a.dateposition OR a.dateresigned = '0000-00-00' OR a.dateresigned IS NULL) AND c.cutoffstart = '$dfrom' AND cutoffend = '$dto' AND a.teachingtype='$tnt' $wC GROUP BY a.employeeid ORDER BY qFullname, qDeptId, campusid";                
        return $this->db->query($this->attSummQuery)->result_array();
    }

    
    public function giveBaseQuery(){
        return $this->indSummQuery;
    }

    public function giveAttSummQuery(){
        return $this->attSummQuery;
    }
    
    public function checkLeaveBalance($eid = ""){
        $return = "";
        $query = $this->db->query("SELECT leavetype FROM employee WHERE employeeid='$eid'");
        $ltype = $query->row(0)->leavetype;
        if($ltype){
            $query = $this->db->query("SELECT code_request FROM code_request_form WHERE leavetype='$ltype' AND CURRENT_DATE BETWEEN startdate AND enddate");
            if($query->num_rows() > 0){
                $crequest = $query->row(0)->code_request;
                $query = $this->db->query("SELECT (A.credits - IFNULL(B.creditleave,0)) as leavebalance
                                            FROM
                                            (SELECT credits FROM code_request_form WHERE CURRENT_DATE BETWEEN startdate AND enddate AND leavetype='$ltype') as A, 
                                            (SELECT SUM(no_days) as creditleave FROM leave_request WHERE employeeid='$eid' AND leavetype='$crequest') as B")->result();
                foreach($query as $row){
                    $return = $row->leavebalance;
                }
            }
        }
        return $return;
    }

    # ica-hyperion 22012
    # by justin (with e)
    public function saveAttendanceSummaryPerDay($data, $empid, $teachingtype){
        foreach ($data as $date => $info) {
            $is_allow_add = false;
            $this->db->query("DELETE FROM employee_attendance_detailed WHERE employeeid='$empid' AND sched_date='$date'");
            
            foreach ($info as $key => $value){
                if($value){
                    $is_allow_add = true;
                    break;
                }
            }

            if($is_allow_add){
                $overtime = $late = $undertime = $absents = "";

                if($teachingtype == "teaching"){
                    $overtime = isset($info["overtime"]) ? $this->attcompute->sec_to_hm($info["overtime"]) : "";
                    $late = isset($info["late"]) ? $info["late"] : "";
                    $undertime = isset($info["undertime"]) ? $info["undertime"] : "";
                    $ot_amount = isset($info["ot_amount"]) ? $info["ot_amount"] : "";
                    $ot_type = isset($info["ot_type"]) ? $info["ot_type"] : "";
                    $absents = ($info["absent"]) ? $this->convertTimeToNumber(date("H:i", strtotime(($info["absent"])))) : $info["absent"];
                }else{
                    $overtime = ($info["overtime"]) ? $this->attcompute->sec_to_hm($info["overtime"]) : $info["overtime"];
                    $late = ($info["late"]) ? $this->attcompute->sec_to_hm($info["late"]) : $info["late"];
                    $undertime = ($info["undertime"]) ? $this->attcompute->sec_to_hm($info["undertime"]) : $info["undertime"];
                    $absents = ($info["absent"]) ? $this->convertTimeToNumber(date("H:i", strtotime(($info["absent"])))) : $info["absent"];
                    $ot_amount = isset($info["ot_amount"]) ? $info["ot_amount"] : 0;
                    $ot_type = isset($info["ot_type"]) ? $info["ot_type"] : "";
                }

                $save_data = array(
                    "employeeid" => $empid,
                    "sched_date" => $date,
                    "overtime"   => $overtime,
                    "late"       => $late,
                    "undertime"  => $undertime,
                    "absents"    => $absents,
                    "ot_amount"    => $ot_amount,
                    "ot_type"    => $ot_type
                );
                
                $this->db->insert("employee_attendance_detailed", $save_data);
            }
        }
    }

    public function convertTimeToNumber($value, $revert=false){
        switch ($revert) {
            case true:
                $exp_value = explode(".", $value);

                if(count($exp_value) > 0){
                    $hrs = $exp_value[0];
                    $min = (isset($exp_value[1])) ? (60 * ("0.".$exp_value[1])) : "00";

                    return $hrs .":". ((int) $min);
                }else return "Failed to convert time";
                break;
            
            default:
                $exp_time = explode(":", $value);

                if(count($exp_time) > 0){
                    list($hrs, $min) = $exp_time;
                    $hrs = (int) $hrs;
                    $min = ((int) $min) / 60;

                    return ($hrs + $min);
                }else return "Failed to convert time";
                break;
        }
    }
    # end ica-hyperion 22012

    /**
     * @revised Angelica
     * Computation for employee attendance summary per cutoff. This will insert computed summary to corresponding table (attendance_confirmed).
     *
     * @param String $from_date
     * @param String $to_date
     * @param String $empid
     *
     * @return string
     */
    public function saveEmployeeAttendanceSummaryTeaching($from_date='',$to_date='',$payroll_start='',$payroll_end='',$payroll_quarter='',$empid='',$isBED=false,$hold_status='',$usertype=""){
        $username = $this->session->userdata("username");
        $dtrend_tmp = $to_date;
        $payrollend_tmp = $payroll_end;

        list($tlec,$tlab,$tadmin,$tabsent,$tdaily_absent,$tel,$tvl,$tsl,$tol,$tdlec,$tdlab,$tdadmin,$holiday,$hasSched,$hasLog,$twork_lec,$twork_lab,$twork_admin,$workhours_arr,$ot_list,$date_list,$totr,$totrest,$tothol) = $this->computeEmployeeAttendanceSummaryTeaching($from_date,$dtrend_tmp,$empid,false,$isBED);
        // echo "<pre>"; print_r($tdadmin); die;
        // list($tabsent,$tadmin,$tutlec,$totr,$totrest,$tothol,$tel,$tvl,$tsl,$tsl,$tol,$tholiday,$holiday,$hasSched,$hasLog,$twork_admin,$ot_list,$date_list,$tsc) = $this->computeEmployeeAttendanceSummaryNonTeaching($from_date,$to_date,$empid);
        $isnodtr = $this->extensions->checkIfCutoffNoDTR($from_date,$to_date);
        if($isnodtr){
            $date_list = $this->removeLateUtAbsent($date_list, $isnodtr);
            $tlec = $tlab = $tadmin = $tabsent = $tdlec = $tdlab = $tdadmin = $absent_day = '';
        }

        $this->attendance->saveAttendanceSummaryPerDay($date_list, $empid, "teaching");

        $query = $this->db->query("SELECT * FROM attendance_confirmed WHERE cutoffstart='$from_date' AND cutoffend='$to_date' AND employeeid='$empid'");
        if($query->num_rows() == 0){
            $base_id = '';
            $res = $this->db->query("INSERT INTO attendance_confirmed (employeeid,cutoffstart,cutoffend,overload, otreg, otrest, othol,latelec,latelab,lateadmin,absent,day_absent,isholiday,eleave,vleave,sleave,oleave,deduclec,deduclab,deducadmin,date_processed,
                                                                        payroll_cutoffstart,payroll_cutoffend,quarter,hold_status,hold_status_change,f_dtrend,f_payrollend,usertype,confirmedby) 
                                VALUES ('$empid','$from_date','$to_date','','$totr','$totrest','$tothol','$tlec','$tlab','$tadmin','$tabsent','$tdaily_absent','$holiday','$tel','$tvl','$tsl','$tol','$tdlec','$tdlab','$tdadmin','". date("Y-m-d H:i:s") ."',
                                            '$payroll_start','$payroll_end','$payroll_quarter','$hold_status','$hold_status','$dtrend_tmp','$payrollend_tmp','$usertype','$username')");
            if($res) $base_id = $this->db->insert_id();

            /*for confirmed logs */
            $logs_d = array(
                "employeeid" => $empid,
                "cutoffstart" => $from_date,
                "cutoffend" => $to_date,
                "confirmedby" => $username,
                "date_processed" => date("Y-m-d H:i:s")
            );

            $this->db->insert("att_confirm_logs", $logs_d);

            if($base_id){
                foreach ($ot_list as $ot_data_tmp){
                    $ot_data = $ot_data_tmp;
                    $ot_data["base_id"] = $base_id;

                    $this->db->insert('attendance_confirmed_ot_hours', $ot_data);
                }

                ///< perdepartment work hours
                foreach ($workhours_arr as $aimsdept => $leclab_arr) {
                    foreach ($leclab_arr as $type => $sec) {
                        $late_hours = $this->attcompute->sec_to_hm($sec['late_hours']);
                        $deduc_hours = $this->attcompute->sec_to_hm($sec['deduc_hours']);
                        $this->db->query("INSERT INTO workhours_perdept (base_id, work_days, late_hours, deduc_hours, type, aimsdept) VALUES ('$base_id',0,'$late_hours','$deduc_hours','$type','$aimsdept')");
                        $inserted_id[$this->db->insert_id()] = $this->db->insert_id();
                    }
                }
                ///<update workhours ( will refer to payroll cutoff)
                list($tlec,$tlab,$tadmin,$tabsent,$tdaily_absent,$tel,$tvl,$tsl,$tol,$tdlec,$tdlab,$tdadmin,$holiday,$hasSched,$hasLog,$twork_lec,$twork_lab,$twork_admin,$workhours_arr) = $this->computeEmployeeAttendanceSummaryTeaching($from_date,$to_date,$empid);

                $this->db->query("UPDATE attendance_confirmed SET workhours_lec='$twork_lec', workhours_lab='$twork_lab', workhours_admin='$twork_admin' WHERE id='$base_id'");

                ///< perdepartment work hours
                foreach ($workhours_arr as $aimsdept => $leclab_arr) {
                  foreach ($leclab_arr as $type => $sec) {
                    $work_hours = $this->attcompute->sec_to_hm($sec['work_hours']);
                    $late_hours = $this->attcompute->sec_to_hm($sec['late_hours']);
                    $deduc_hours = $this->attcompute->sec_to_hm($sec['deduc_hours']);
                    $update_query = $this->db->query("UPDATE workhours_perdept SET work_hours='$work_hours' WHERE base_id='$base_id' AND `type`='$type' AND aimsdept='$aimsdept' ");
                    $inserted_row = $this->checkWorkhoursExisting($base_id, $type, $aimsdept);
                    if(!in_array($inserted_row, $inserted_id)) $this->db->query("INSERT INTO workhours_perdept (base_id, work_hours, work_days,type, aimsdept) VALUES ('$base_id', '$work_hours' ,0,'$type','$aimsdept')");
                  }
                }
            } // end if base_id
        }else{
            return false;
        }

        if($res)    return true;
        else        return false;

    } ///< end of public function


    /**
     * @revised Angelica
     * This will insert computed attendance summary to corresponding table (attendance_confirmed_nt).
     *
     * @param String $from_date
     * @param String $to_date
     * @param String $empid
     *
     * @return boolean
     */
    public function saveEmployeeAttendanceSummaryNonTeaching($from_date='',$to_date='',$payroll_start='',$payroll_end='',$payroll_quarter='',$empid='',$hold_status='',$usertype=""){
        $this->load->model('utils');
        $username = $this->session->userdata("username");
        $startdate = $enddate = $quarter = $isnodtr = "";
        $payrollcutoff = $this->extras->getPayrollCutoff($from_date, $to_date);
        foreach($payrollcutoff as $cutoff_info){
            $startdate = $cutoff_info['startdate'];
            $enddate = $cutoff_info['enddate'];
            $quarter = $cutoff_info['quarter'];
            $isnodtr = $cutoff_info['nodtr'];
        }
        list($tabsent,$tlec,$tutlec,$totr,$totrest,$tothol,$tel,$tvl,$tsl,$tsl,$tol,$tholiday,$holiday,$hasSched,$hasLog,$workdays,$ot_list,$date_list,$tsc) = $this->computeEmployeeAttendanceSummaryNonTeaching($from_date,$to_date,$empid);
        // echo "<pre>"; print_r($tabsent); die;
        if($isnodtr){
            $date_list = $this->removeLateUtAbsent($date_list, $isnodtr);
            $tabsent = $tlec = $tutlec = '';
        }
        // echo "<pre>"; print_r($date_list); die;
        # ica-hyperion 22012
        $this->attendance->saveAttendanceSummaryPerDay($date_list, $empid, "nonteaching");

        // Save to database
        $query = $this->utils->getSingleTblData('attendance_confirmed_nt',array('id'),array('cutoffstart'=>$from_date,'cutoffend'=>$to_date,'employeeid'=>$empid));
        if($query->num_rows() == 0){
            $res = $this->db->query("INSERT INTO attendance_confirmed_nt (employeeid, cutoffstart, cutoffend, workdays, otreg, otrest, othol, lateut, ut, absent, eleave, vleave, sleave, oleave, status, isholiday, forcutoff, payroll_cutoffstart, payroll_cutoffend, quarter, date_processed, scleave, usertype, confirmedby) 
                                     VALUES ('$empid', '$from_date', '$to_date', '$workdays', '$totr', '$totrest', '$tothol', '$tlec', '$tutlec', '$tabsent', '$tel', '$tvl', '$tsl', '$tol', 'SUBMITTED', '$tholiday', '1', '$startdate', '$enddate', '$quarter', '". date("Y-m-d h:i:s") ."', '$tsc', '$usertype', '$username')");

            $base_id = '';
            if($res) $base_id = $this->db->insert_id();

            /*for confirmed logs */
            $logs_d = array(
                "employeeid" => $empid,
                "cutoffstart" => $from_date,
                "cutoffend" => $to_date,
                "confirmedby" => $username,
                "date_processed" => date("Y-m-d H:i:s")
            );

            $this->db->insert("att_confirm_logs", $logs_d);

            if($base_id){
                /*foreach ($ot_list as $ot_type => $det) {
                    foreach ($det as $holiday_type => $ex_det) {
                        foreach ($ex_det as $excess => $ottime) {
                            $ot_hours = $this->attcompute->sec_to_hm($ottime);
                            $ot_data = array(
                                'base_id'=>$base_id,
                                'ot_hours'=>$ot_hours,
                                'ot_type'=>$ot_type,
                                'holiday_type'=>$holiday_type,
                                'is_excess'=>$excess
                            );
                            $this->utils->insertSingleTblData('attendance_confirmed_nt_ot_hours', $ot_data);
                        }
                    }
                }*/

                foreach ($ot_list as $ot_data_tmp){
                    $ot_data = $ot_data_tmp;
                    $ot_data["base_id"] = $base_id;

                    $this->db->insert('attendance_confirmed_nt_ot_hours', $ot_data);
                }
            }

        }else{
            return false;
        }

        if($res)    return true;
        else        return false;

    } ///< end of public function      

    /**
     * @revised Angelica
     * Computation for employee attendance summary with given date range. (Teaching)
     *
     * @param String $from_date
     * @param String $to_date
     * @param String $empid
     *
     * @return array
     */
    function computeEmployeeAttendanceSummaryTeaching($from_date='',$to_date='',$empid='',$toCheckPrevAtt=false,$isBED=false){
        $date_list = array();
        $edata          = 'NEW';
        $deptid = $this->employee->getindividualoffice($empid);
        $tdaily_absent = '';
        $tlec = $tlab = $tadmin = $tabsent = $tabsentperday = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tdadmin = ""; 
        $tempabsent = $lateutlec= $lateutlab = $twork_lec = $twork_lab = $twork_admin = $utlec = "";
        $totr = $totrest = $tothol = 0;
        $workhours_arr = array();
        $aimsdept = '';
        $hasLog = $isSuspension = $isCreditedHoliday = false;
        $firstDate = true;
        $last_day = '';
        $absent_day = '';
        $date_list_absent = 0;
        $tholiday = 0;
        $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
        $haslog_forremarks = "";
        ///< based from source -> individual attendance_report
        $daily_absent = array();
        $ot_list = $ot_save_list = array();
        foreach ($qdate as $rdate) {
            $holiday_type = '';
            // Holiday
            $isSuspension = $hasSched = false;
            $holiday = $this->attcompute->isHolidayNew($empid,$rdate->dte,$deptid,"","", "teaching" ); 
            $holidayInfo = $this->attcompute->holidayInfo($rdate->dte, "", "teaching", $deptid);
            if($holiday)
            {
                if($holidayInfo){
                    if($holidayInfo["code"]=="SUS") $isSuspension = true;
                    // if($holidayInfo["withPay"]=='NO' || !$holidayInfo["withPay"]) $holiday = '';
                    // if($holidayInfo["holiday_rate"] <= 0) $holiday = ''; 
                }
                $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
            }

            $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
            if(!$is_holiday_valid){
                $holidayInfo = array();
                $holiday = "";
            }

            $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
            $sched = $this->attcompute->displaySched($empid,$rdate->dte);
            $countrow = $sched->num_rows();
                
            $isValidSchedule = true;

            if($countrow > 0){
                if($sched->row(0)->starttime == '00:00:00' && $sched->row(0)->endtime == '00:00:00') $isValidSchedule = false;
            }

            if(!$toCheckPrevAtt){
                ///< for validation of absent for 1st day in range. this will check for previous day attendance
                if($firstDate && $holiday){
                    $hasLog = $this->attendance->checkPreviousSchedAttendanceTeaching($rdate->dte,$empid);
                    $firstDate = false;
                }
            }

            $bed_isfirsthalf_absent = $bed_issechalf_absent = $bed_iswholeday_absent = true;
            $bed_setup = $this->getBEDAttendanceSetup();
            $perday_info = array();
            $ot_list = array();
            if($countrow > 0 && $isValidSchedule){
                $hasSched = true;
                $haswholedayleave = false;
                $hasleavecount = 0;

                ///< for validation of holiday (will only be credited if not absent during last schedule)
                $hasLogprev = $hasLog;
                $hasLog = false;

                if($hasLogprev || $isSuspension)    $isCreditedHoliday = true;
                else                                $isCreditedHoliday = false;

                $tempsched = "";
                $seq = 0;
                $isFirstSched = true;
                $bed_rowcount_half = 0;
                foreach($sched->result() as $rsched){
                    $persched_info = array();

                    if($tempsched == $dispLogDate)  $dispLogDate = "";
                    $seq += 1;
                    $stime = $rsched->starttime;
                    $etime = $rsched->endtime; 
                    $type  = $rsched->leclab;
                    $tardy_start = $rsched->tardy_start;
                    $absent_start = $rsched->absent_start;
                    $earlydismissal = $rsched->early_dismissal;
                    $aimsdept = $rsched->aimsdept;

                    // logtime
                    list($login,$logout,$q,$haslog_forremarks) = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq,$absent_start,$earlydismissal);
                    if($haslog_forremarks) $hasLog = true;
                    if(!$login) $login = $this->attcompute->getLogin($empid, $edata, $rdate->dte);
                    if(!$logout) $logout = $this->attcompute->getLogout($empid, $edata, $rdate->dte);
                    

                    // Overtime
                    list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,true);

                    if($isFirstSched){
                        $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,true,$holiday_type);
                        $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);

                        $ot_save_list = $this->attcompute->insertOTListToArray($ot_save_list, $ot_list);
                    }

                    
                    
                    // Leave
                    list($el,$vl,$sl,$ol,$oltype,$ob)     = $this->attcompute->displayLeave($empid,$rdate->dte,'',$stime,$etime,$seq);

                    // Absent
                    $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlydismissal, $isFirstSched, $absent_start);
                    // if($rdate->dte == "2022-08-22"){
                    //     echo "<pre>"; print_r($login.'~~'.$logout); 
                    // }
                    // Late / Undertime
                    list($lateutlec,$lateutlab,$lateutadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUT($stime,$etime,$tardy_start,$login,$logout,$type,$absent);
                    // if($lateutadmin){
                    //     echo "<pre>"; print_r($stime);
                    //     echo "<pre>"; print_r($etime);
                    //     echo "<pre>"; print_r($tardy_start);
                    //     echo "<pre>"; print_r($logout);
                    // }
                    $absent = $tschedadmin;
                    if ($vl >= 1 || $el >= 1 || $sl >= 1 || ($ol && !$ob && $ol !="CORRECTION") || $ob >= 1){
                            $absent = $tschedadmin = "";
                        $haswholedayleave = true;
                    }
                    if ($vl > 0.5 || $el > 0.5 || $sl > 0.5 || ($ol && !$ob && $ol !="CORRECTION") || $ob > 0.5){
                        $absent = $tschedadmin = "";
                        $hasleavecount+=0.5;
                    }

                    if($el || $vl || $sl || $ob || ($ol && !$ob && $ol !="CORRECTION") || ($holiday && $isCreditedHoliday)){
                         $lateutlec = $lateutlab = $lateutadmin = $tschedlec = $tschedlab = $tschedadmin = "";
                    }

                    // if($this->attcompute->exp_time($tschedadmin) >= 10800){
                    //     $tschedadmin = "4:00"; 
                    // }

                    

                    if($isBED){
                        $isAbsent = $this->attcompute->exp_time($absent) > 0 ? 1 : 0;
                        list($rowcount_half,$isfirsthalf_absent,$issechalf_absent,$iswholeday_absent) = 
                        $this->getBEDPerdayAbsent($bed_setup,array('sched_start'=>$stime,'sched_end'=>$etime,'isAbsent'=>$isAbsent));
                        
                        $bed_rowcount_half += $rowcount_half;

                        $bed_isfirsthalf_absent  =  $bed_isfirsthalf_absent ? (!$isfirsthalf_absent ? false : true) : false ;
                        $bed_issechalf_absent    =  $bed_issechalf_absent ? (!$issechalf_absent ? false : true) : false ;
                        $bed_iswholeday_absent    =  $bed_iswholeday_absent ? (!$iswholeday_absent ? false : true) : false ;
                    }

                    if($absent && !$type) $absent = '';

                    $tempsched = $dispLogDate;
                    /*
                     * ----------------Total---------------------------------------------
                     */ 

                    // Absent
                    if($absent){
                        if(!$isBED) $tabsent += $this->attcompute->exp_time($absent) > 0 ? 1 : 0;
                        // if($rdate->dte != $absent_day) $tdaily_absent .= substr($rdate->dte, 5)." 1/";
                        // $absent_day = $rdate->dte;

                    }
                    
                    if(!$holiday) $holiday = $this->attcompute->isHolidayNew($empid, $rdate->dte,$deptid,"","on","teaching" );  
                    if($holiday)
                    {
                        $sched_count = "";
                        if($isFirstSched) $sched_count = "first";
                        else $sched_count = "second";
                        $newholidayInfo = $this->attcompute->holidayInfo($rdate->dte, $sched_count, "teaching");
                        if(isset($newholidayInfo["halfday"])) $holidayInfo = $newholidayInfo;
                        if($holidayInfo){
                            if($holidayInfo["code"]=="SUS") $isSuspension = true;
                            // if($holidayInfo["holiday_rate"] <= 0) $holiday = ''; 
                        }
                    }
                    $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
                    if(isset($holidayInfo['description'])){
                        $log_remarks = '';
                        if(isset($holidayInfo['halfday'])){
                            if($holidayInfo['sched_count'] == "second" && !$isFirstSched){
                                $lateutlec = $lateutlec;
                                $utlec = $utlec;
                                $absent = $tschedadmin = '';
                                $tschedlec = $tschedlab = $tschedadmin = "";
                                $hasHalfdayHoliday = true;
                            }else if($holidayInfo['sched_count'] != "second" && $isFirstSched){
                                $lateutlec = $lateutlec;
                                $utlec = $utlec;
                                $absent = $tschedadmin = '';
                                $tschedlec = $tschedlab = $tschedadmin = "";
                                $hasHalfdayHoliday = true;
                            }else{
                                // $lateutlec = $utlec = $absent =  '';
                            }
                        }else{
                            $lateutlec = $utlec = $absent = $tschedadmin = '';
                        }
                    }else{
                        $log_remarks = '';
                        if($absent){
                            if(!$login && !$logout && !$haslog_forremarks) $log_remarks = 'NO TIME IN AND OUT';
                            elseif(!$login) $log_remarks = 'NO TIME IN';
                            elseif(!$logout) $log_remarks = 'NO TIME OUT';
                        }
                    }

                    // Leave
                    if($dispLogDate || $hasleavecount || (!$haswholedayleave && !$holiday))
                    {
                        $tel      += ($el) ? 0.5 : 0;
                        $tvl      += ($vl) ? 0.5 : 0;
                        $tsl      += ($sl) ? 0.5 : 0;
                        $tol      += ($ol > 0) ? 0.5 : 0;
                        $date_tmp  = $rdate->dte;
                        //$tol    += ($ol ? 1 : "") + ($q ? ($q == 1 ? "" : 1) : "") ;
                        // echo "<pre>". $rdate->dte ." - ". $ol . " - ". $q . "</pre>";
                    }
                    
                    if(!$isBED){
                        // Late / UT
                        if($tlec){
                            $secs  = strtotime($lateutlec)-strtotime("00:00:00");
                            if($secs>0) $tlec = date("H:i",strtotime($tlec)+$secs);
                        }else
                            $tlec    = $lateutlec;

                        $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"]) && $date_list[$rdate->dte]["late"]) ? date("H:i",strtotime($date_list[$rdate->dte]["late"]) + strtotime($lateutlec) - strtotime("00:00:00")) : $lateutlec;
                            
                        if($tlab){
                            $secs  = strtotime($lateutlab)-strtotime("00:00:00");
                            if($secs>0) $tlab = date("H:i",strtotime($tlab)+$secs);
                        }else
                            $tlab    = $lateutlab;

                        $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"]) && $date_list[$rdate->dte]["late"]) ? date("H:i",strtotime($date_list[$rdate->dte]["late"]) + strtotime($lateutlab) - strtotime("00:00:00")) : $lateutlab;

                        if($tadmin){
                            $secs  = strtotime($lateutadmin)-strtotime("00:00:00");
                            if($secs>0) $tadmin = date("H:i",strtotime($tadmin)+$secs);
                        }else
                            $tadmin    = $lateutadmin;

                        $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"]) && $date_list[$rdate->dte]["late"]) ? date("H:i",strtotime($date_list[$rdate->dte]["late"]) + strtotime($lateutadmin) - strtotime("00:00:00")) : $lateutadmin;

                        // Deductions
                        if($tschedlec)      $tdlec += $this->attcompute->exp_time($tschedlec);
                        $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"]) && $date_list[$rdate->dte]["absent"]) ? ($date_list[$rdate->dte]["absent"] + $this->attcompute->exp_time($tschedlec)) : $this->attcompute->exp_time($tschedlec);

                        if($tschedlab)      $tdlab += $this->attcompute->exp_time($tschedlab);
                        $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"]) && $date_list[$rdate->dte]["absent"]) ? ($date_list[$rdate->dte]["absent"] + $this->attcompute->exp_time($tschedlab)) : $this->attcompute->exp_time($tschedlab);

                        if($tschedadmin)    $tdadmin += $this->attcompute->exp_time($tschedadmin);
                        $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"]) && $date_list[$rdate->dte]["absent"]) ? ($date_list[$rdate->dte]["absent"] + $this->attcompute->exp_time($tschedadmin)) : $this->attcompute->exp_time($tschedadmin);

                    }else{
                        $persched_info['sched_type'] = $type;
                        $persched_info['lateut_lec'] = $lateutlec;
                        $persched_info['lateut_lab'] = $lateutlab;
                        $persched_info['lateut_admin'] = $lateutadmin;
                        $persched_info['deduc_lec'] = $tschedlec;
                        $persched_info['deduc_lab'] = $tschedlab;
                        $persched_info['deduc_admin'] = $tschedadmin;
                        array_push($perday_info, $persched_info);
                    }
                    
                    if(!$tschedadmin && !$absent) $hasLog = true;

                    list($work_lec,$work_lab,$work_admin,$workhours_arr) = $this->getWorkhoursPerdeptArr($stime,$etime,$type,$aimsdept,$workhours_arr,$lateutlec,$tschedlec,$lateutlab,$tschedlab,$lateutadmin,$tschedadmin);

                    if($work_admin >= 10800){
                        $work_admin = 14400;
                    }
                    $twork_lec += $work_lec;
                    $twork_lab += $work_lab;
                    $twork_admin += $work_admin;

                    $isFirstSched = false;
                }   // end foreach sched

                $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"])) ? $date_list[$rdate->dte]["overtime"] : "";

                if($otreg){

                    $totr += $this->attcompute->exp_time($otreg);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otreg) : $this->attcompute->exp_time($otreg);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);
/*
                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }

                if($otrest){
                    $totrest += $this->attcompute->exp_time($otrest);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otrest) : $this->attcompute->exp_time($otrest);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }

                if($othol){
                    $tothol += $this->attcompute->exp_time($othol);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($othol) : $this->attcompute->exp_time($othol);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }
               
                if($isBED){
                    if($bed_rowcount_half == $countrow) {
                        $bed_issechalf_absent = $bed_iswholeday_absent = false;
                    }elseif($bed_rowcount_half == 0){
                        $bed_isfirsthalf_absent = $bed_iswholeday_absent = false;
                    }

                    if((!$login || !$logout || $login == "0000-00-00 00:00:00" || $logout == "0000-00-00 00:00:00") && ($bed_issechalf_absent || $bed_isfirsthalf_absent)){
                        $bed_issechalf_absent = true;
                    }

                    $bed_absent = 0;
                    if($bed_iswholeday_absent){
                        $bed_absent = 1;
                        $tdadmin += 28800; ///< 8hrs for 1day absent -- BED is fixed to admin TYPE
                        $day_absent = substr($rdate->dte, 5);
                        $tdaily_absent .= $day_absent." 1/";
                        $date_list_absent += 28800;
                    }else{
                        if($bed_issechalf_absent || $bed_isfirsthalf_absent){
                            $bed_absent = 0.5;
                            $tdadmin += 14400; ///< 4hrs for half day absent -- BED is fixed to admin TYPE
                            $day_absent = substr($rdate->dte, 5);
                            $tdaily_absent .= $day_absent." 0.5/";
                            $date_list_absent += 14400;
                        }

                        ///< construct lateut
                        ///< if half/wholeday present , add deduc to late per specific sched

                        $lateut_perday = $this->constructLateUTBedSummary($perday_info,$bed_isfirsthalf_absent,$bed_issechalf_absent,$bed_rowcount_half);
                        $date_list_tlec = ($lateut_perday['tlec']) ? $this->attcompute->sec_to_hm($lateut_perday['tlec']) : 0;
                        $date_list_tlab = ($lateut_perday['tlab']) ? $this->attcompute->sec_to_hm($lateut_perday['tlab']) : 0;
                        $date_list_tadmin = ($lateut_perday['tadmin']) ? $this->attcompute->sec_to_hm($lateut_perday['tadmin']) : 0;

                        if($tlec){
                            if($lateut_perday['tlec']) $tlec = $this->attcompute->sec_to_hm($this->attcompute->exp_time($tlec) + $lateut_perday['tlec']);
                        }else $tlec = $lateut_perday['tlec'] ? $this->attcompute->sec_to_hm($lateut_perday['tlec']) : '';
                        $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"]) && $date_list[$rdate->dte]["late"]) ? date("H:i",strtotime($date_list[$rdate->dte]["late"]) + strtotime($tlec) - strtotime("00:00:00")) : $date_list_tlec;

                        if($tlab){
                            if($lateut_perday['tlab']) $tlab = $this->attcompute->sec_to_hm($this->attcompute->exp_time($tlab) + $lateut_perday['tlab']);
                        }else $tlab = $lateut_perday['tlab'] ? $this->attcompute->sec_to_hm($lateut_perday['tlab']) : '';
                        $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"]) && $date_list[$rdate->dte]["late"]) ? date("H:i",strtotime($date_list[$rdate->dte]["late"]) + strtotime($tlab) - strtotime("00:00:00")) : $date_list_tlab;

                        if($tadmin){
                            if($lateut_perday['tadmin']) $tadmin = $this->attcompute->sec_to_hm($this->attcompute->exp_time($tadmin) + $lateut_perday['tadmin']);
                        }else $tadmin = $lateut_perday['tadmin'] ? $this->attcompute->sec_to_hm($lateut_perday['tadmin']) : '';
                        $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"]) && $date_list[$rdate->dte]["late"]) ? date("H:i",strtotime($date_list[$rdate->dte]["late"]) + strtotime($tadmin) - strtotime("00:00:00")) : $date_list_tadmin;

                    }

                    $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"]) && $date_list[$rdate->dte]["absent"]) ? ($date_list[$rdate->dte]["absent"] + $date_list_absent) : $date_list_absent;
                    $tabsent     += $bed_absent;
                }
                if(!$login && $absent) $tdaily_absent .= substr($rdate->dte, 5)." 1/";

            } // end if valid sched
            else{
                /*no sched */
                $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"])) ? $date_list[$rdate->dte]["absent"] : "";

                $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"])) ? $date_list[$rdate->dte]["overtime"] : "";
                list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,false);
                /* Overtime */
                // total regular
                if($otreg){
                    $totr += $this->attcompute->exp_time($otreg);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otreg) : $this->attcompute->exp_time($otreg);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }
                // total saturday
                if($otrest){
                    $totrest += $this->attcompute->exp_time($otrest);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otrest) : $this->attcompute->exp_time($otrest);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }
                
                // total holiday
                if($othol){
                    $tothol += $this->attcompute->exp_time($othol);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($othol) : $this->attcompute->exp_time($othol);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }
                
                $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);
            }

            if($holiday && !isset($holidayInfo["halfday"])) $tholiday++;
            else if(isset($holidayInfo["halfday"])) $tholiday += 0.5;

            $date_list_absent = 0;
        } // end loop dates
        
        $twork_lec = $twork_lec ? $this->attcompute->sec_to_hm($twork_lec) : "";
        $twork_lab = $twork_lab ? $this->attcompute->sec_to_hm($twork_lab) : "";
        $twork_admin = $twork_admin ? $this->attcompute->sec_to_hm($twork_admin) : "";

        $tdlec = ($tdlec ? $this->attcompute->sec_to_hm($tdlec) : "");
        $tdlab = ($tdlab ? $this->attcompute->sec_to_hm($tdlab) : "");
        $tdadmin = ($tdadmin ? $this->attcompute->sec_to_hm($tdadmin) : "");
        $totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "");
        $totrest = ($totrest ? $this->attcompute->sec_to_hm($totrest) : ""); 
        $tothol = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "");

        return array($tlec,$tlab,$tadmin,$tabsent,$tdaily_absent,$tel,$tvl,$tsl,$tol,$tdlec,$tdlab,$tdadmin,$tholiday,$hasSched,$hasLog,$twork_lec,$twork_lab,$twork_admin,$workhours_arr,$ot_save_list,$date_list,$totr,$totrest,$tothol);
    } ///< end of function computeEmployeeAttendanceSummaryTeaching

    function constructLateUTBedSummary($perday_info=array(),$bed_isfirsthalf_absent=false,$bed_issechalf_absent=false,$bed_rowcount_half=0){
        $lateut_perday = array('tlec'=>0,'tlab'=>0,'tadmin'=>0);

        foreach ($perday_info as $key => $persched_info) {
            $lec = $lab = $admin = 0;

            $lec = $this->attcompute->exp_time($persched_info['deduc_lec']);
            $lab = $this->attcompute->exp_time($persched_info['deduc_lab']);
            $admin = $this->attcompute->exp_time($persched_info['deduc_admin']);

            $late_lec = $lec + $this->attcompute->exp_time($persched_info['lateut_lec']);
            $late_lab = $lab + $this->attcompute->exp_time($persched_info['lateut_lab']);
            $late_admin = $admin + $this->attcompute->exp_time($persched_info['lateut_admin']);

            if($key < $bed_rowcount_half){
                if(!$bed_isfirsthalf_absent){
                    if($persched_info['sched_type'] == 'LEC'){ 
                        $lateut_perday['tlec'] +=  $late_lec;
                    }elseif($persched_info['sched_type'] == 'LAB'){ 
                        $lateut_perday['tlab'] += $late_lab;
                    }else{                                        
                        $lateut_perday['tadmin'] += $late_admin;
                    }
                }
            }else{
                if(!$bed_issechalf_absent){
                    if($persched_info['sched_type'] == 'LEC'){ 
                        $lateut_perday['tlec'] +=  $late_lec;
                    }elseif($persched_info['sched_type'] == 'LAB'){ 
                        $lateut_perday['tlab'] += $late_lab;
                    }else{                                        
                        $lateut_perday['tadmin'] += $late_admin;
                    }
                }
            }
        }
        return $lateut_perday;
    }

    function getWorkhoursPerdeptArr($stime='',$etime='',$type='',$aimsdept='',$workhours_arr=array(),$lateutlec='',$tschedlec='',$lateutlab='',$tschedlab='',$lateutadmin='',$tschedadmin){
        $twork_lec = $twork_lab = $twork_admin = 0;
        $tsched   = round(abs(strtotime($stime) - strtotime($etime)) / 60,2);
        $tsched   = date('H:i', mktime(0,$tsched));
        $tsched   = $this->attcompute->exp_time($tsched);
        if($type == 'LEC')       $twork_lec =  $tsched;
        elseif($type == 'LAB')   $twork_lab = $tsched;
        else                    $twork_admin = $tsched;

        ///< perdepartment work hours
        if($type == 'LEC'){
            if(!isset($workhours_arr[$aimsdept]['LEC']['work_hours'])) $workhours_arr[$aimsdept]['LEC']['work_hours'] = 0;
            if(!isset($workhours_arr[$aimsdept]['LEC']['late_hours'])) $workhours_arr[$aimsdept]['LEC']['late_hours'] = 0;
            if(!isset($workhours_arr[$aimsdept]['LEC']['deduc_hours'])) $workhours_arr[$aimsdept]['LEC']['deduc_hours'] = 0;
            $workhours_arr[$aimsdept]['LEC']['work_hours'] += $tsched;
            $workhours_arr[$aimsdept]['LEC']['late_hours'] += $this->attcompute->exp_time($lateutlec);
            $workhours_arr[$aimsdept]['LEC']['deduc_hours'] += $this->attcompute->exp_time($tschedlec);
        }elseif($type == 'LAB'){
            if(!isset($workhours_arr[$aimsdept]['LAB']['work_hours'])) $workhours_arr[$aimsdept]['LAB']['work_hours'] = 0;
            if(!isset($workhours_arr[$aimsdept]['LAB']['late_hours'])) $workhours_arr[$aimsdept]['LAB']['late_hours'] = 0;
            if(!isset($workhours_arr[$aimsdept]['LAB']['deduc_hours'])) $workhours_arr[$aimsdept]['LAB']['deduc_hours'] = 0;
            $workhours_arr[$aimsdept]['LAB']['work_hours'] += $tsched;
            $workhours_arr[$aimsdept]['LAB']['late_hours'] += $this->attcompute->exp_time($lateutlab);
            $workhours_arr[$aimsdept]['LAB']['deduc_hours'] += $this->attcompute->exp_time($tschedlab);
        }else{
            if(!isset($workhours_arr[$aimsdept]['ADMIN']['work_hours'])) $workhours_arr[$aimsdept]['ADMIN']['work_hours'] = 0;
            if(!isset($workhours_arr[$aimsdept]['ADMIN']['late_hours'])) $workhours_arr[$aimsdept]['ADMIN']['late_hours'] = 0;
            if(!isset($workhours_arr[$aimsdept]['ADMIN']['deduc_hours'])) $workhours_arr[$aimsdept]['ADMIN']['deduc_hours'] = 0;
            $workhours_arr[$aimsdept]['ADMIN']['work_hours'] += $tsched;
            $workhours_arr[$aimsdept]['ADMIN']['late_hours'] += $this->attcompute->exp_time($lateutadmin);
            $workhours_arr[$aimsdept]['ADMIN']['deduc_hours'] += $this->attcompute->exp_time($tschedadmin);
        }

        return array($twork_lec,$twork_lab,$twork_admin,$workhours_arr);
    }

    function getBEDAttendanceSetup(){
        $setup = array();
        $setup['firsthalf_start']    = '05:00';
        $setup['halfday_cutoff']     = '12:00';
        $setup['sechalf_end']        = '21:00';
        return $setup;
    }


    function getBEDPerdayAbsent($setup=array(),$persched_info=array()){
        $rowcount_half = 0;
        $isfirsthalf_absent = $issechalf_absent = $iswholeday_absent = true;

        if( $this->attcompute->exp_time($persched_info['sched_start']) >= $this->attcompute->exp_time($setup['firsthalf_start']) && $this->attcompute->exp_time($persched_info['sched_end']) <= $this->attcompute->exp_time($setup['halfday_cutoff']) ){
            $rowcount_half++;

            if($persched_info['isAbsent'] == 0) $isfirsthalf_absent = $iswholeday_absent = false;

        }elseif( $this->attcompute->exp_time($persched_info['sched_start']) > $this->attcompute->exp_time($setup['halfday_cutoff']) && $this->attcompute->exp_time($persched_info['sched_end']) <= $this->attcompute->exp_time($setup['sechalf_end']) ){
            if($persched_info['isAbsent'] == 0) $issechalf_absent = $iswholeday_absent = false;

        }elseif( $this->attcompute->exp_time($persched_info['sched_start']) >= $this->attcompute->exp_time($setup['firsthalf_start']) && $this->attcompute->exp_time($persched_info['sched_end']) <= $this->attcompute->exp_time($setup['sechalf_end']) ){
            if($persched_info['isAbsent'] == 0) $iswholeday_absent = $isfirsthalf_absent = $issechalf_absent = false;
        }
        return array($rowcount_half,$isfirsthalf_absent,$issechalf_absent,$iswholeday_absent);
    }

    /**
     * @revised Angelica
     * Computation for employee attendance summary with given date range. (Non-teaching)
     *
     * @param String $from_date
     * @param String $to_date
     * @param String $empid
     *
     * @return array
     */

     public function computeEmployeeAttendanceSummaryNonTeaching($from_date='',$to_date='',$empid='',$toCheckPrevAtt=false){
        $x = $totr = $totrest = $tothol = $tlec = $tutlec= $absent = $tabsent = $tabsentperday = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = $pending = $tempOverload = $overload = $tOverload = $lastDayOfWeek = $cs_app = $date_tmp = ""; 
        $tempabsent = $lateutlec= $lateutlab = $lateutadmin = $utlec= $utlab = $utadmin = $tutlec = $tutlab = $tutadmin = $twork_lec = $twork_lab = $twork_admin = $work_lec = $work_lab = $work_admin = $hasSched = $tsc = "";
        $tlec = $workdays = $tworkdays = 0 ;
        $tempabsent = "";
        $t_service_credit = $service_credit = $isFirstSched = "";
        $seq_new = 0;
        $perday_absent = $total_perday_absent = 0;
        $login_new = $logout_new = $q_new = $haslog_forremarks_new = "";
        $not_included_ol = array("ABSENT", "EL", "VL", "SL", "CORRECTION");

        $hasLog = $isSuspension = false;
        $edata = 'NEW';
        $isCreditedHoliday = false;
        $hasHalfdayHoliday = false;
        $firstDate = true;
        $ob_data = array();
        $holidayInfo = array();
        $ot_list = array();
        $ot_save_list = array();
        $date_list = array();
        $teachingtype = $this->extensions->getEmployeeTeachingType($empid);
        $deptid = $this->employee->getindividualoffice($empid);
        $fixedday = $this->attcompute->isFixedDay($empid);
        $qdate = $this->attcompute->displayDateRange($from_date, $to_date);
        foreach ($qdate as $rdate) {
            $is_holiday_valid = false;
            $isSuspension = false;
            $holiday_type = '';
            $holiday = $this->attcompute->isHolidayNew($empid,$rdate->dte,$deptid,"","",$teachingtype ); 
            $holidayInfo = $this->attcompute->holidayInfo($rdate->dte, "", $teachingtype, "",$holiday);

            if($holiday)
            {
                if($holidayInfo){
                    if($holidayInfo["code"]=="SUS") $isSuspension = true;
                }
                $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
            }

            if(!$is_holiday_valid){
                $holidayInfo = array();
                $holiday = "";
            }

            $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
            $sched = $this->attcompute->displaySched($empid,$rdate->dte);
            $countrow = $sched->num_rows();
            $isValidSchedule = true;

            if($countrow > 0){
                if($sched->row(0)->starttime == '00:00:00' && $sched->row(0)->endtime == '00:00:00') $isValidSchedule = false;
            }

            if($x%2 == 0)   $color = " style='background-color: white;'";
            else            $color = " style='background-color: #fafafa;'";
            $x++;

            if($firstDate && $holiday){
                $hasLog = $this->attendance->checkPreviousSchedAttendanceNonTeaching($rdate->dte,$empid);
                $firstDate = false;
            }

            if($countrow > 0 && $isValidSchedule){
                $haswholedayleave = false;
                $hasleavecount = 0;

                $hasLogprev = $hasLog;
                $hasLog = false;
                
                if($hasLogprev || $isSuspension)    $isCreditedHoliday = "true";
                else                                $isCreditedHoliday = "false";
                $tempsched = "";
                $seq = 0;
                $service_credit = null;
                $service_credit_used = 0;

                $isFirstSched = true;
                $q_sched = $sched;
                $perday_absent = $this->attendance->getTotalAbsentPerday($sched->result(), $empid, $rdate->dte);
                $total_perday_absent += $perday_absent;
                foreach($sched->result() as $rsched){

                    if(!$is_holiday_valid && $isFirstSched){
                        $holidayInfo = $this->attcompute->holidayInfo($rdate->dte, "second");
                    }

                    $workdays = 0;
                    $ob_type = true;
                    //NOT FLEXIBLE
                    if($rsched->flexible != "YES")
                    {
                        if($tempsched == $dispLogDate){  $dispLogDate = "";}
                        $stime  = $rsched->starttime;
                        $etime  = $rsched->endtime; 
                        $tstart = $rsched->tardy_start; 
                        $absent_start = $rsched->absent_start;
                        $earlyd = $rsched->early_dismissal;
                        if($earlyd > $etime) $earlyd = $etime;
                        
                        $seq += 1;
                        
                        list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$lnopay)  = $this->attcompute->displayLeave($empid,$rdate->dte,'',$stime,$etime,$seq);
                        list($login,$logout,$q,$haslog_forremarks)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq,$absent_start,$earlyd);
                        list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,true);

                        // if($isFirstSched){
                        //     $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,true,$holiday_type);
                        //     $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);

                        //     $ot_save_list = $this->attcompute->insertOTListToArray($ot_save_list, $ot_list);
                        // }

                        $ob_data = $this->attcompute->displayLateUTAbs($empid, $rdate->dte);

                        $service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);

                        $cs_app = $this->attcompute->displayChangeSchedApp($empid,$rdate->dte);
                        
                        $pending = $this->attcompute->displayPendingApp($empid,$rdate->dte, "", $stime, $etime);

                        $pending_ob = $this->attcompute->displayPendingOBApp($empid,$rdate->dte, ($seq==1) ? "late" : "undertime");
                        if($ob) $pending_ob = "";
                        
                        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd, $isFirstSched,$absent_start);

                        if($oltype == "ABSENT") $absent = $absent;
                        else if($holiday && $isCreditedHoliday) $absent = "";

                        if ($vl >= 1 || $el >= 1 || $sl >= 1 || ($ol && !$ob && $ol !="CORRECTION" && $lnopay  && $q != 'Facial' && $q != 'webcheckin' && $q != 'Fingerprint') || $ob >= 1 || $service_credit >= 1){
                            $absent = "";
                            $haswholedayleave = true;
                        }
                        if ($vl > 0.5 || $el > 0.5 || $sl > 0.5 || ($ol && !$ob && $ol !="CORRECTION" && $lnopay  && $q != 'Facial' && $q != 'webcheckin' && $q != 'Fingerprint') || $ob > 0.5 || $service_credit > 0){
                            $absent = "";
                            $hasleavecount+=0.5;
                        }
                        if($abs_count >= 1) $haswholedayleave = true;


                        $lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent,$teachingtype,$tstart);
                        // if($lateutlec){
                        //     echo "<pre>"; print_r($stime);
                        //     echo "<pre>"; print_r($etime);
                        //     echo "<pre>"; print_r($login);
                        //     echo "<pre>"; print_r($logout);
                        //     echo "<pre>"; print_r($tstart);
                        //     die;
                        // }
                        $utlec  = $this->attcompute->computeUndertimeNT($stime,$etime,$login,$logout,$absent,$teachingtype,$earlyd);
                        // echo "<pre>"; print_r($utlec); 
                        // if($el || $vl || $sl/*  || $ob*/ || ($ol && !$ob && $ol !="CORRECTION" && !$lnopay) || $service_credit || ($holiday && $isCreditedHoliday)) $lateutlec = $utlec = "";
                        if($el || $vl || $sl  || $ol || (!$ob && $ol !="CORRECTION" && $lnopay  && $q != 'Facial' && $q != 'webcheckin' && $q != 'Fingerprint') || $service_credit || ($holiday && $isCreditedHoliday)) $lateutlec = $utlec = $absent = "";

                                            
                        if($holiday)
                        {
                            if($this->attcompute->isHolidayWithpay($rdate->dte) == "YES")
                            {
                                if($tempabsent)
                                {
                                    $absent = $absent;
                                }
                            }
                            else
                            {
                                if(!$login && !$logout)
                                {
                                    $absent = $absent;
                                }
                            }
                        }
                        else
                        {
                            $tempabsent = $absent;
                        }


                        // $hasOL = $ol ? (($ol != 'CORRECTION' && $ol != 'DIRECT') && $ol != 'undertime' && $ol != 'late' ? true : false) : false; 
                        $hasOL = $ol ? (($ol != 'CORRECTION' && $ol != 'DIRECT') && $ol != 'undertime' && $ol != 'late' && $ol == 0 ? true : false) : false; 

                        if($hasOL && !$ob) $login = $logout = "";

                        if(!$fixedday){
                            if($absent=='' || $hasOL) $workdays=1;
                        }

                        if($isFirstSched){
                            if(!$login && $absent) $login = $this->attcompute->getLogin($empid, $edata, $rdate->dte);
                            if(!$logout && $absent) $logout = $this->attcompute->getLogout($empid, $edata, $rdate->dte);
                            if($login && $logout && !$absent){
                                $lateutlec = $this->attcompute->displayLateUTNT($stime, $etime, $login, $logout, "", "", $tstart);
                                $utlec = $this->attcompute->computeUndertimeNT($stime,$etime,$login,$logout,"","",$earlyd);


                                if($absent) $lateutlec = $absent;
                                if($utlec || $lateutlec) $log_remarks = $absent = "";
                                $hasLog = TRUE;
                            }else{
                                 foreach($sched->result() as $rsched){
                                    if(isset($sched_new[1]->starttime)) $stime  = $rsched->starttime;
                                    if(isset($sched_new[1]->endtime)) $etime  = $rsched->endtime; 
                                    if(isset($sched_new[1]->tardy_start)) $tstart = $rsched->tardy_start; 
                                    if(isset($sched_new[1]->absent_start)) $absent_start = $rsched->absent_start;
                                    if(isset($sched_new[1]->early_dismissal)) $earlyd = $rsched->early_dismissal;
                                    $seq_new += 1;
                                    list($login_new,$logout_new,$q_new,$haslog_forremarks_new)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq_new,$absent_start,$earlyd);

                                 }
                            }
                            if($el || $vl || $sl  || $ol || (!$ob && $ol !="CORRECTION" && $lnopay  && $q != 'Facial' && $q != 'webcheckin' && $q != 'Fingerprint') || $service_credit || ($holiday && $isCreditedHoliday)) $lateutlec = $utlec = $absent = "";
                        }else{

                            if(!$login || $absent) $login = $this->attcompute->getLogin($empid, $edata, $rdate->dte);
                            if(!$logout || $absent) $logout = $this->attcompute->getLogout($empid, $edata, $rdate->dte);

                            if($el == FALSE && $vl == FALSE && $sl == FALSE  && $ob == FALSE && $service_credit == FALSE && $ol == FALSE){
                                if($login && $logout && !$absent){
                                    $lateutlec = $this->attcompute->displayLateUTNT($stime, $etime, $login, $logout, "", "", $tstart);

                                    $utlec = $this->attcompute->computeUndertimeNT($stime,$etime,$login,$logout,"","",$earlyd);

                                    if($absent) $utlec = $absent;
                                    if($utlec || $lateutlec) $log_remarks = $absent = "";
                                }

                            }
                            if($el || $vl || $sl  || $ol || (!$ob && $ol !="CORRECTION" && $lnopay  && $q != 'Facial' && $q != 'webcheckin' && $q != 'Fingerprint') || $service_credit || ($holiday && $isCreditedHoliday)) $lateutlec = $utlec = $absent = "";
                        }
                        if(!$holiday) $holiday = $this->attcompute->isHolidayNew($empid, $rdate->dte,$deptid,"","on",$teachingtype );  
                        if($holiday)
                        {
                            $sched_count = "";
                            if($isFirstSched) $sched_count = "first";
                            else $sched_count = "second";
                            $newholidayInfo = $this->attcompute->holidayInfo($rdate->dte, $sched_count, $teachingtype, $deptid);
                            if(isset($newholidayInfo["halfday"])) $holidayInfo = $newholidayInfo;
                            if($holidayInfo){
                                
                            }
                        }
                        $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
                        if($holiday && isset($holidayInfo['description'])){
                            $log_remarks = '';
                            if(isset($holidayInfo['halfday'])){
                                if($holidayInfo['sched_count'] == "second" && !$isFirstSched){
                                    $lateutlec = '';
                                    $utlec = '';
                                    $absent = '';
                                    $tschedlec = $tschedlab = $tschedadmin = "";
                                    $hasHalfdayHoliday = true;
                                }else if($holidayInfo['sched_count'] != "second" && $isFirstSched){
                                    $lateutlec = '';
                                    $utlec = '';
                                    $absent = '';
                                    $tschedlec = $tschedlab = $tschedadmin = "";
                                    $hasHalfdayHoliday = true;
                                }else{
                                    // $lateutlec = $utlec = $absent =  '';
                                }
                            }else{
                                $lateutlec = $utlec = $absent = '';
                            }
                        }else{
                            $log_remarks = '';
                            if($absent){
                                if(!$login && !$logout && !$haslog_forremarks) $log_remarks = 'NO TIME IN AND OUT';
                                elseif(!$login) $log_remarks = 'NO TIME IN';
                                elseif(!$logout) $log_remarks = 'NO TIME OUT';
                            }
                        }

                        // if($absent){
                        //     echo "<pre>"; print_r($ol); 
                        // }
                        $absent = $this->attcompute->exp_time($absent);

                        if($absent >= 10800) $absent = 14400;
                        $absent   = ($absent ? $this->attcompute->sec_to_hm($absent) : "");
                        if($lateutlec && !$utlec){
                            if(in_array("late", $ob_data)) $log_remarks = "EXCUSED LATE";
                            else{
                                $log_remarks = "UNEXCUSED LATE";
                                $ob_type = false;
                                $ob_data = array();
                            }
                        }else if($utlec){
                            if(in_array("undertime", $ob_data)) $log_remarks = "EXCUSED UNDERTIME";
                            else{
                                $log_remarks = "UNEXCUSED UNDERTIME";
                                $ob_type = false;
                                $ob_data = array();
                            }
                        }else if($absent){
                            if(in_array("absent", $ob_data)) $log_remarks = "EXCUSED ABSENT";
                            else{
                                if(strtotime($rdate->dte) < strtotime($date_tmp)){
                                    $log_remarks = "UNEXCUSED ABSENT";
                                    $ob_type = false;
                                    $ob_data = array();
                                }
                            }
                        }

                    }else{
                        $totalQ = 0;
                        if($tempsched == $dispLogDate){  $dispLogDate = "";}
                        $stime  = $rsched->starttime;
                        $etime  = $rsched->endtime; 
                        $tstart = $rsched->tardy_start; 
                        $earlyd = $rsched->early_dismissal;
                        
                        // logtime
                        $getLog = $this->attcompute->getLogsPerDay($empid,$rdate->dte,$edata,true);
                        $log = array();
                        if(count($getLog) > 1) $log[] = $getLog[0];
                        else                   $log = $getLog; 
                        
                        list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,true);

                        // if($isFirstSched){
                        //     $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,true,$holiday_type);
                        //     $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);
                        //     $ot_save_list = $this->attcompute->insertOTListToArray($ot_save_list, $ot_list);
                        // }

                        list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$lnopay)             = $this->attcompute->displayLeave($empid,$rdate->dte);

                        $pending = $this->attcompute->displayPendingApp($empid,$rdate->dte);
                        $pending_ob = $this->attcompute->displayPendingOBApp($empid,$rdate->dte, ($seq==1) ? "late" : "undertime");
                        if($ob) $pending_ob = "";

                        $service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);
                        
                        $count_leave = $vl > 0 ? $vl : ( $el > 0 ? $el : ( $sl > 0 ? $sl : ( $ob > 0 ? $ob : ( $service_credit > 0 ? $service_credit : 0 ) ) ) ) ;

                        $absent = $this->attcompute->displayAbsentFlexi($log,$rsched->hours,$rsched->mode,$empid,$rdate->dte,'',$rsched->breaktime, $count_leave);
                        if($oltype == "ABSENT") $absent = $absent;
                        else if($holiday && $isCreditedHoliday) $absent = "";

                        if ($vl > 0 || $el > 0 || $sl > 0 || $ob > 0 || $service_credit > 0){
                            $absent = "";
                        }

                        
                        $lateutlec = '';
                        $utlec = $this->attcompute->displayLateUTNTFlexi($log,$rsched->hours,$rsched->mode,$absent,$rsched->breaktime, $count_leave);

                        if($el >= 1 || $vl >= 1 || $sl >= 1 || $ob >= 1 || $service_credit >= 1 || ($holiday && $isCreditedHoliday)) $utlec = "";
                        if(date("Y-m-d",strtotime($utlec)) < $rdate->dte)
                        {
                            $utlec = $lateutlab = "";
                        }


                        $log_remarks = '';

                        $hasOL = $ol ? ($ol != 'CORRECTION' ? true : false) : false; 

                        if(!$fixedday){
                            if($absent=='' || $hasOL) $workdays=1;
                        }

                        $login = $logout = $q = "";
                        if(count($log) > 0)
                        {
                            for($i = 0;$i < count($log);$i++)
                            {
                                $login = $log[$i][0];
                                $logout = $log[$i][1];

                                if($login=='0000-00-00 00:00:00') $login = "";
                                if($logout=='0000-00-00 00:00:00') $logout = "";

                                if($absent){
                                    if(!$login && !$logout) $log_remarks = 'NO TIME IN AND OUT';
                                    elseif(!$login) $log_remarks = 'NO TIME IN';
                                    elseif(!$logout) $log_remarks = 'NO TIME OUT';
                                }

                                $q = $log[$i][2];
                                if($q) $totalQ++;
 
                                $stime = $etime = "";
                            }
                        }else{
                            if($absent) $log_remarks = 'NO TIME IN AND OUT';
                            
                        }
                    }///< end if FLEXIBLE/NOT

                    $tempsched = $dispLogDate;
                    
                    /*
                     * Total
                     */ 
                    $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"])) ? $date_list[$rdate->dte]["absent"] : "";
                    // Absent
                    if($absent){
                        if(!$fixedday && !$hasOL) $tabsent += $this->attcompute->exp_time($absent);
                        else $tabsent += $this->attcompute->exp_time($absent);
                        $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"]) && $date_list[$rdate->dte]["absent"]) ? $date_list[$rdate->dte]["absent"] + $this->attcompute->exp_time($absent) : $this->attcompute->exp_time($absent);
                    }else{
                        $hasLog = true;
                    }

                    $hasLog = $hasLog ? $hasLog : ($hasOL ? true : false); 
                    
                    // Late / UT
                    $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"])) ? $date_list[$rdate->dte]["late"] : "";
                    // echo "<pre>"; print_r($lateutlec); die;
                    if($lateutlec){
                        // echo "<pre>"; print_r($rdate->dte);
                        $tlec += $this->attcompute->exp_time($lateutlec);
                        $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"]) && $date_list[$rdate->dte]["late"]) ? $date_list[$rdate->dte]["late"] + $this->attcompute->exp_time($lateutlec) : $this->attcompute->exp_time($lateutlec);
                    }

                    $date_list[$rdate->dte]["undertime"] = (isset($date_list[$rdate->dte]["undertime"])) ? $date_list[$rdate->dte]["undertime"] : "";
                    if($utlec){
                        $tutlec += $this->attcompute->exp_time($utlec);
                        $date_list[$rdate->dte]["undertime"] = (isset($date_list[$rdate->dte]["undertime"]) && $date_list[$rdate->dte]["undertime"]) ? $date_list[$rdate->dte]["undertime"] + $this->attcompute->exp_time($utlec) : $this->attcompute->exp_time($utlec);
                    }
                    
                    // Leave
                    if($dispLogDate || $hasleavecount || (!$haswholedayleave && !$pending && !$holiday))
                    {
                        $tel      += ($el) ? 0.5 : 0;
                        $tvl      += ($vl) ? 0.5 : 0;
                        $tsl      += ($sl) ? 0.5 : 0;
                        $tol      += ($ol > 0) ? 0.5 : 0;
                        $date_tmp  = $rdate->dte;
                        //$tol    += ($ol ? 1 : "") + ($q ? ($q == 1 ? "" : 1) : "") ;
                        // echo "<pre>". $rdate->dte ." - ". $ol . " - ". $q . "</pre>";
                    }
                    #$tol     += $service_credit_used;
                    $service_credit_used = 0;

                    if($service_credit && ($dispLogDate || !$haswholedayleave)) $t_service_credit+=$service_credit;

                    
                    if(!$fixedday){
                        if($absent=='' || $hasOL) $tworkdays+=0.5;
                    }
                    
                    $hasHalfdayHoliday = false;
                    $isFirstSched = false;  
                    if(isset($holidayInfo["halfday"])) $isCreditedHoliday = false;
                }  ///< end foreach sched
                       
                
                // total holiday
                if($holiday && !isset($holidayInfo["halfday"])) $tholiday++;
                else if($holiday && isset($holidayInfo["halfday"])) $tholiday += 0.5;
                
                $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"])) ? $date_list[$rdate->dte]["overtime"] : "";

                /* Overtime */
                if($otreg){
                    $totr += $this->attcompute->exp_time($otreg);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otreg) : $this->attcompute->exp_time($otreg);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);
/*
                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }

                if($otrest){
                    $totrest += $this->attcompute->exp_time($otrest);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otrest) : $this->attcompute->exp_time($otrest);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }

                if($othol){
                    $tothol += $this->attcompute->exp_time($othol);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($othol) : $this->attcompute->exp_time($othol);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }

                
            }else{
                ///< no sched or not valid sched

                $totalQ = 0;
                $stime = "";
                $etime = ""; 
                
                $log = $this->attcompute->displayLogTimeFlexi($empid,$rdate->dte,$edata);
                
                // Leave
                list($el,$vl,$sl,$ol,$oltype)     = $this->attcompute->displayLeave($empid,$rdate->dte);
                //Service Credit 
                $service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);

                // Leave Pending
                $pending = $this->attcompute->displayPendingApp($empid,$rdate->dte);
                $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"])) ? $date_list[$rdate->dte]["absent"] : "";
                $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"])) ? $date_list[$rdate->dte]["late"] : "";
                $date_list[$rdate->dte]["undertime"] = (isset($date_list[$rdate->dte]["undertime"])) ? $date_list[$rdate->dte]["undertime"] : "";
                $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"])) ? $date_list[$rdate->dte]["overtime"] : "";
                // Overtime
                list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,false);
                // if($isFirstSched){
                //     $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,true,$holiday_type);
                //     $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);
                //     $ot_save_list = $this->attcompute->insertOTListToArray($ot_save_list, $ot_list);
                // }
                if($otreg){
                    $totr += $this->attcompute->exp_time($otreg);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otreg) : $this->attcompute->exp_time($otreg);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);
/*
                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }

                if($otrest){
                    $totrest += $this->attcompute->exp_time($otrest);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otrest) : $this->attcompute->exp_time($otrest);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }

                if($othol){
                    $tothol += $this->attcompute->exp_time($othol);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($othol) : $this->attcompute->exp_time($othol);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }
                
                if(count($log)> 0)
                {
                    ///< no sched with log

                    $login = $logout = $q = "";
                    $stime = $etime = "--";
                    
                    for($i = 0;$i < count($log);$i++)
                    {
                        $login = $log[$i][0];
                        $logout = $log[$i][1];
                        $q = $log[$i][2];
                        if($q) $totalQ++;
                        
                       
                    }
               }
               

               if($service_credit && ($dispLogDate || !$haswholedayleave)) $t_service_credit+=$service_credit;

            }///< end else no sched

            $holiday = '';
            $firstDate = true;
        }
        $tabsent = ($tabsent ? $this->attcompute->sec_to_hm($tabsent) : "");
        $tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "");       
        $tutlec   = ($tutlec ? $this->attcompute->sec_to_hm($tutlec) : "");       
        $totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "");
        $totrest = ($totrest ? $this->attcompute->sec_to_hm($totrest) : ""); 
        $tothol = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "");

        return array($tabsent,$tlec,$tutlec,$totr,$totrest,$tothol,$tel,$tvl,$tsl,$tsl,$tol,$tholiday,$holiday,$hasSched,$hasLog,$workdays, $ot_save_list, $date_list, $tsc); 
    }
     

    public function computeEmployeeAttendanceSummaryNonTeachingOld($from_date='',$to_date='',$empid='',$toCheckPrevAtt=false){
        $edata = 'NEW';
        $date_list = array();
        $deptid = $this->employee->getindividualoffice($empid);
        $not_included_ol = array("ABSENT", "EL", "VL", "SL", "CORRECTION");
        $date_tmp = "";

        $fixedday = $this->attcompute->isFixedDay($empid);

        $x = $totr = $totrest = $tothol = $tlec = $tutlec = $absent = $tabsent = $tabsentperday = $tel = $tvl = $tsl = $tol = $tdlec = $tdlab = $tholiday = $pending = $tempOverload = $overload = $tOverload = $lastDayOfWeek = $service_credit = $cs_app = $tsc = ""; 
        $workdays = 0;
        $seq_new = 0;
        $tlec = 0 ;
        $tempabsent = "";
        $hasLog = $isSuspension = false;
        $hasSched = $holiday = "";
        $ot_list = array();
        $ot_save_list = array();
        $haslog_forremarks = "";
        $qdate = $this->attcompute->displayDateRange($from_date, $to_date);

        $isCreditedHoliday = false;
        $firstDate = true;
        ///< based from source -> individual attendance_report
        foreach ($qdate as $rdate) {
            $holiday_type = '';

            // Holiday
            $isSuspension = false;
            $holiday = $this->attcompute->isHolidayNew($empid,$rdate->dte,$deptid,"","","nonteaching" ); 

            $holidayInfo = $this->attcompute->holidayInfo($rdate->dte, "", "teaching", $deptid);
            if($holiday)
            {
                if($holidayInfo){
                    if($holidayInfo["code"]=="SUS") $isSuspension = true;
                    // if($holidayInfo["withPay"]=='NO' || !$holidayInfo["withPay"]) $holiday = '';
                    // if($holidayInfo["holiday_rate"] <= 0) $holiday = ''; 
                }
                $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
            }

            $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
            if(!$is_holiday_valid){
                $holidayInfo = array();
                $holiday = "";
            }

            $dispLogDate = date("d-M (l)",strtotime($rdate->dte));
            $sched = $this->attcompute->displaySched($empid,$rdate->dte);
            $countrow = $sched->num_rows();
                
            $isValidSchedule = true;

            if($countrow > 0){
                if($sched->row(0)->starttime == '00:00:00' && $sched->row(0)->endtime == '00:00:00') $isValidSchedule = false;
            }


            $hasSched = false;

             if(!$toCheckPrevAtt){
                ///< for validation of absent for 1st day in range. this will check for previous day attendance
                if($firstDate && $holiday){
                    $hasLog = $this->attendance->checkPreviousSchedAttendanceNonTeaching($rdate->dte,$empid);
                    $firstDate = false;
                }
            }

            if($countrow > 0 && $isValidSchedule){
                $hasSched = $firstsched = true;
                $haswholedayleave = false;
                $hasleavecount = 0;
                ///< for validation of holiday (will only be credited if not absent during last schedule)
                $hasLogprev = $hasLog;
                $hasLog = false;

                
                if($hasLogprev || $isSuspension)    $isCreditedHoliday = true;
                else                                $isCreditedHoliday = false;

                $tempsched = "";
                $seq=0;

                $isFirstSched = true;
                $ot_list = array();
                $q_sched = $sched;
                foreach($sched->result() as $rsched){

                    //NOT FLEXIBLE -----------------------------------------------------------------------------------------------------------------------------------
                    if($rsched->flexible != "YES")
                    {

                        if($tempsched == $dispLogDate)  $dispLogDate = "";
                        $seq += 1;
                        $stime  = $rsched->starttime;
                        $etime  = $rsched->endtime; 
                        $tstart = $rsched->tardy_start; 
                        $absent_start = $rsched->absent_start;
                        $earlyd = $rsched->early_dismissal;
                        if($earlyd > $etime) $earlyd = $etime;

                         // Leave
                        // list($el,$vl,$sl,$ol,$oltype,$ob)  = $this->attcompute->displayLeave($empid,$rdate->dte,'',$stime,$etime,$seq);
                        list($el,$vl,$sl,$ol,$oltype,$ob,$abs_count,$lnopay)  = $this->attcompute->displayLeave($empid,$rdate->dte,'',$stime,$etime,$seq);
                        
                        // logtime
                        list($login,$logout,$q)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq,$absent_start,$earlyd);
                        
                         // Overtime
                        list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,true);

                        if($isFirstSched){
                            $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,true,$holiday_type);
                            $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);

                            $ot_save_list = $this->attcompute->insertOTListToArray($ot_save_list, $ot_list);
                        }

                       
                        //Service Credit 
                        $service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);

                        // Change Schedule
                        $cs_app = $this->attcompute->displayChangeSchedApp($empid,$rdate->dte);
                        
                        
                        // Leave Pending
                        $pending = $this->attcompute->displayPendingApp($empid,$rdate->dte);

                         // Absent
                        $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$rdate->dte,$earlyd);
                        if($oltype == "ABSENT") $absent = $absent;
                        else if($holiday && $isCreditedHoliday) $absent = "";

                        // if ($vl > 0 || $el > 0 || $sl > 0 || ($ol && !$ob) || $ob > 0 || $service_credit > 0){
                        //     $absent = "";
                        // }

                        if ($vl >= 1 || $el >= 1 || $sl >= 1 || ($ol && !$ob && $ol !="CORRECTION" && !$lnopay) || $ob >= 1 || $service_credit >= 1){
                            $absent = "";
                            $haswholedayleave = true;
                        }
                        if ($vl > 0.5 || $el > 0.5 || $sl > 0.5 || ($ol && !$ob && $ol !="CORRECTION" && !$lnopay) || $ob > 0.5 || $service_credit > 0){
                            $absent = "";
                            $hasleavecount+=0.5;
                        }
                        if($abs_count >= 1) $haswholedayleave = true;
                        
                        // Late / Undertime
                        $lateutlec = $this->attcompute->displayLateUTNT($stime,$etime,$login,$logout,$absent,'',$tstart);
                        $utlec  = $this->attcompute->computeUndertimeNT($stime,$etime,$login,$logout,$absent,'',$tstart);
                        // if($el || $vl || $sl || $ob || $service_credit || ($holiday && $isCreditedHoliday)) $lateutlec = $utlec = "";
                        if($el || $vl || $sl/*  || $ob*/ || ($ol && !$ob && $ol !="CORRECTION" && !$lnopay) || $service_credit || ($holiday && $isCreditedHoliday)) $lateutlec = $utlec = "";
                        // echo $lateutlec;
                                            
                        if($isFirstSched){
                            if(!$login || $absent) $login = $this->attcompute->getLogin($empid, $edata, $rdate->dte);
                            if(!$logout || $absent) $logout = $this->attcompute->getLogout($empid, $edata, $rdate->dte);

                            if($login && $logout){
                                $lateutlec = $this->attcompute->displayLateUTNT($stime, $etime, $login, $logout, "", "", $tstart);
                                $utlec = $this->attcompute->computeUndertimeNT($stime,$etime,$login,$logout,"","","");


                                if($absent) $lateutlec = $absent;
                                if($utlec || $lateutlec) $log_remarks = $absent = "";
                                $hasLog = TRUE;
                            }else{
                                 foreach($sched->result() as $rsched){
                                    if(isset($sched_new[1]->starttime)) $stime  = $rsched->starttime;
                                    if(isset($sched_new[1]->endtime)) $etime  = $rsched->endtime; 
                                    if(isset($sched_new[1]->tardy_start)) $tstart = $rsched->tardy_start; 
                                    if(isset($sched_new[1]->absent_start)) $absent_start = $rsched->absent_start;
                                    if(isset($sched_new[1]->early_dismissal)) $earlyd = $rsched->early_dismissal;
                                    $seq_new += 1;
                                    list($login_new,$logout_new,$q_new,$haslog_forremarks_new)           = $this->attcompute->displayLogTime($empid,$rdate->dte,$stime,$etime,$edata,$seq_new,$absent_start,$earlyd);
                                    if($login_new || $logout_new){
                                        // $lateutlec = $absent;
                                        // $lateutlab = $absent;
                                    }
                                 }
                                 // $absent = "";
                            }
                        }else{
                            
                            if(!$login || $absent) $login = $this->attcompute->getLogin($empid, $edata, $rdate->dte);
                            if(!$logout || $absent) $logout = $this->attcompute->getLogout($empid, $edata, $rdate->dte);

                            if($el == FALSE && $vl == FALSE && $sl == FALSE  && $ob == FALSE && $service_credit == FALSE && $ol == FALSE){
                                if($login){
                                    // $utlec = $absent;
                                    // $utlab = $absent;
                                    // $absent = "";
                                }
                                if($login && $logout){
                                    $lateutlec = $this->attcompute->displayLateUTNT($stime, $etime, $login, $logout, "", "", $tstart);
                                    $utlec = $this->attcompute->computeUndertimeNT($stime,$etime,$login,$logout,"","","");

                                    if($absent) $utlec = $absent;
                                    if($utlec || $lateutlec) $log_remarks = $absent = "";
                                }
                            }
                        }
                        if($holiday && $isCreditedHoliday) $lateutlec = $utlec = "";
                        $absent = $this->attcompute->exp_time($absent);
                        if($absent >= 10800) $absent = 14400;
                        $absent   = ($absent ? $this->attcompute->sec_to_hm($absent) : "");

                        if(!$holiday) $holiday = $this->attcompute->isHolidayNew($empid, $rdate->dte,$deptid,"","on","nonteaching" );  
                        if($holiday)
                        {
                            $sched_count = "";
                            if($isFirstSched) $sched_count = "first";
                            else $sched_count = "second";
                            $newholidayInfo = $this->attcompute->holidayInfo($rdate->dte, $sched_count, "nonteaching");
                            if(isset($newholidayInfo["halfday"])) $holidayInfo = $newholidayInfo;
                            if($holidayInfo){
                                if($holidayInfo["code"]=="SUS") $isSuspension = true;
                                // if($holidayInfo["holiday_rate"] <= 0) $holiday = ''; 
                            }
                        }
                        $is_holiday_valid = $this->attendance->getTotalHoliday($rdate->dte, $rdate->dte, $empid);
                        if(isset($holidayInfo['description'])){
                            $log_remarks = '';
                            if(isset($holidayInfo['halfday'])){
                                if($holidayInfo['sched_count'] == "second" && !$isFirstSched){
                                    $lateutlec = $lateutlec;
                                    $utlec = $utlec;
                                    $absent = '';
                                    $tschedlec = $tschedlab = $tschedadmin = "";
                                    $hasHalfdayHoliday = true;
                                }else if($holidayInfo['sched_count'] != "second" && $isFirstSched){
                                    $lateutlec = $lateutlec;
                                    $utlec = $utlec;
                                    $absent = '';
                                    $tschedlec = $tschedlab = $tschedadmin = "";
                                    $hasHalfdayHoliday = true;
                                }else{
                                    // $lateutlec = $utlec = $absent =  '';
                                }
                            }else{
                                $lateutlec = $utlec = $absent = '';
                            }
                        }else{
                            $log_remarks = '';
                            if($absent){
                                if(!$login && !$logout && !$haslog_forremarks) $log_remarks = 'NO TIME IN AND OUT';
                                elseif(!$login) $log_remarks = 'NO TIME IN';
                                elseif(!$logout) $log_remarks = 'NO TIME OUT';
                            }
                        }
                    }else{ ///< FLEXIBLE ---------------------------------------------------------------------------------------------------------------------------------
                        $stime  = $rsched->starttime;
                        $etime  = $rsched->endtime; 
                        $type  = $rsched->leclab;
                        $tstart = $rsched->tardy_start; 
                        $earlyd = $rsched->early_dismissal;
                        
                        // logtime
                        $log = $this->attcompute->displayLogTimeFlexi($empid,$rdate->dte,$edata);

                        // Overtime
                        list($otreg, $otrest, $othol) = $this->attcompute->displayOt($empid,$rdate->dte,true);



                        if($isFirstSched){
                            $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,true,$holiday_type);
                            $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);
                            $ot_save_list = $this->attcompute->insertOTListToArray($ot_save_list, $ot_list);
                        }

                        // Leave
                        list($el,$vl,$sl,$ol,$oltype,$ob)             = $this->attcompute->displayLeave($empid,$rdate->dte,$seq);

                        //Service Credit 
                        $service_credit = $this->attcompute->displayServiceCredit($empid,$stime,$etime,$rdate->dte);

                        $count_leave = $vl > 0 ? $vl : ( $el > 0 ? $el : ( $sl > 0 ? $sl : ( $ob > 0 ? $ob : ( $service_credit > 0 ? $service_credit : 0 ) ) ) ) ;
                        // Absent
                        $absent = $this->attcompute->displayAbsentFlexi($log,$rsched->hours,$rsched->mode,$empid,$rdate->dte,'',$rsched->breaktime, $count_leave);

                        if($oltype == "ABSENT") $absent = $absent;
                        else if($holiday && $isCreditedHoliday) $absent = "";

                        if ($vl > 0 || $el > 0 || $sl > 0 || $ob > 0 || $service_credit > 0){
                            $absent = "";
                        }


                        // Late / Undertime
                        $lateutlec = '';
                        $utlec = $this->attcompute->displayLateUTNTFlexi($log,$rsched->hours,$rsched->mode,$absent,$rsched->breaktime, $count_leave);

                        if($el >= 1 || $vl >= 1 || $sl >= 1 || $ob >= 1 || $service_credit >= 1 || ($holiday && $isCreditedHoliday)) $utlec = "";
                        if(date("Y-m-d",strtotime($utlec)) < $rdate->dte)
                        {
                            $utlec = $lateutlab = "";
                        }
                        
                        $login = $logout = $q = "";

                    }///< end if FLEXIBLE/NOT


                    $tempsched = $dispLogDate;
                    
                    /*
                     * ----------------Total---------------------------------------------
                     */ 
                    $hasOL = $ol ? ($ol != 'CORRECTION' ? true : false) : false; 
                    $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"])) ? $date_list[$rdate->dte]["absent"] : "";
                    // Absent
                    if($absent){
                        // $tabsentperday += $this->attcompute->exp_time($absent);
                        if(!$fixedday && !$hasOL)   {}
                        else{
                            // if($this->attcompute->exp_time($absent) >= 12600) $absent = "4:00";
                            $tabsent += $this->attcompute->exp_time($absent);
                            $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"]) && $date_list[$rdate->dte]["absent"]) ? $date_list[$rdate->dte]["absent"] + $this->attcompute->exp_time($absent) : $this->attcompute->exp_time($absent);
                        }
                    }else{
                        $hasLog = true;
                    }

                    $hasLog = $hasLog ? $hasLog : ($hasOL ? true : false); 
                    
                    // Late / UT
                    $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"])) ? $date_list[$rdate->dte]["late"] : "";
                    if($lateutlec){
                        $tlec += $this->attcompute->exp_time($lateutlec);
                        $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"]) && $date_list[$rdate->dte]["late"]) ? $date_list[$rdate->dte]["late"] + $this->attcompute->exp_time($lateutlec) : $this->attcompute->exp_time($lateutlec);
                    }

                    $date_list[$rdate->dte]["undertime"] = (isset($date_list[$rdate->dte]["undertime"])) ? $date_list[$rdate->dte]["undertime"] : "";
                    if($utlec){
                        $tutlec += $this->attcompute->exp_time($utlec);
                        $date_list[$rdate->dte]["undertime"] = (isset($date_list[$rdate->dte]["undertime"]) && $date_list[$rdate->dte]["undertime"]) ? $date_list[$rdate->dte]["undertime"] + $this->attcompute->exp_time($utlec) : $this->attcompute->exp_time($utlec);
                    }
                    
                    // Leave
                    if($dispLogDate || (!$pending && !$holiday))
                    {
                        $tel      += ($el) ? 0.5 : 0;
                        $tvl      += ($vl) ? 0.5 : 0;
                        $tsl      += ($sl) ? 0.5 : 0;
                        $tol      += ($ol > 0) ? 0.5 : 0;
                        $date_tmp  = $rdate->dte;
                        //$tol    += ($ol ? 1 : "") + ($q ? ($q == 1 ? "" : 1) : "") ;
                        // echo "<pre>". $rdate->dte ." - ". $ol . " - ". $q . "</pre>";
                    }

                    if($fixedday){
                        if($hasSched) $workdays+=0.5;
                    }else{
                        if($hasSched && ($absent=='' || $hasOL || $holiday)) $workdays+=0.5;
                    }
                    
                    $firstsched = false;
                    $isFirstSched = false;
                }   // end foreach
                
                /* Overtime */
                $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"])) ? $date_list[$rdate->dte]["overtime"] : "";

                if($otreg){

                    $totr += $this->attcompute->exp_time($otreg);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otreg) : $this->attcompute->exp_time($otreg);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);
/*
                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }

                if($otrest){
                    $totrest += $this->attcompute->exp_time($otrest);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otrest) : $this->attcompute->exp_time($otrest);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }

                if($othol){
                    $tothol += $this->attcompute->exp_time($othol);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($othol) : $this->attcompute->exp_time($othol);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }

               
            }else{  ////< to compute for overtime if employee have no schedule for this day ----------------------------------------------------------------------
                $date_list[$rdate->dte]["absent"] = (isset($date_list[$rdate->dte]["absent"])) ? $date_list[$rdate->dte]["absent"] : "";
                $date_list[$rdate->dte]["late"] = (isset($date_list[$rdate->dte]["late"])) ? $date_list[$rdate->dte]["late"] : "";
                $date_list[$rdate->dte]["undertime"] = (isset($date_list[$rdate->dte]["undertime"])) ? $date_list[$rdate->dte]["undertime"] : "";
                $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"])) ? $date_list[$rdate->dte]["overtime"] : "";
                list($otreg,$otrest,$othol) = $this->attcompute->displayOt($empid,$rdate->dte,false);
                /* Overtime */
                // total regular
                if($otreg){
                    $totr += $this->attcompute->exp_time($otreg);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otreg) : $this->attcompute->exp_time($otreg);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }
                // total saturday
                if($otrest){
                    $totrest += $this->attcompute->exp_time($otrest);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($otrest) : $this->attcompute->exp_time($otrest);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }
                
                // total holiday
                if($othol){
                    $tothol += $this->attcompute->exp_time($othol);
                    $date_list[$rdate->dte]["overtime"] = (isset($date_list[$rdate->dte]["overtime"]) && $date_list[$rdate->dte]["overtime"]) ? $date_list[$rdate->dte]["overtime"] + $this->attcompute->exp_time($othol) : $this->attcompute->exp_time($othol);
                    $ot_save_list[count($ot_save_list) - 1]["ot_hours"] = $this->attcompute->sec_to_hm($date_list[$rdate->dte]["overtime"]);

/*                    $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                    $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);*/

                    list($ot_amount, $ot_mode) = $this->attcompute->getOvertimeAmountDetailed($empid, $ot_list, $date_list[$rdate->dte]["overtime"]);
                    $date_list[$rdate->dte]["ot_type"] = $ot_mode;
                    $date_list[$rdate->dte]["ot_amount"] = $ot_amount;
                }
                
                $ot_list_tmp = $this->attcompute->getOvertime($empid,$rdate->dte,false,$holiday_type);
                $ot_list = $this->attcompute->constructOTlist($ot_list,$ot_list_tmp);

            } // end if  
            if($holiday && !isset($holidayInfo["halfday"])) $tholiday++;
            else if(isset($holidayInfo["halfday"])) $tholiday += 0.5;

            $firstDate = true;

        }

        $tabsent = ($tabsent ? $this->attcompute->sec_to_hm($tabsent) : "");

        $tlec   = ($tlec ? $this->attcompute->sec_to_hm($tlec) : "");       
        $tutlec   = ($tutlec ? $this->attcompute->sec_to_hm($tutlec) : "");       
        $totr   = ($totr ? $this->attcompute->sec_to_hm($totr) : "");
        $totrest = ($totrest ? $this->attcompute->sec_to_hm($totrest) : ""); 
        $tothol = ($tothol ? $this->attcompute->sec_to_hm($tothol) : "");
        // $tOverload = ($tOverload ? $this->attcompute->sec_to_hm($tOverload) : "");

        return array($tabsent,$tlec,$tutlec,$totr,$totrest,$tothol,$tel,$tvl,$tsl,$tsl,$tol,$tholiday,$holiday,$hasSched,$hasLog,$workdays, $ot_save_list, $date_list, $tsc); ///< $hasSched is applicable only for checking of attendance good for 1 day

    } ///< end of functio computeEmployeeAttendanceSummaryNonTeaching


    /**
     * @Angelica
     * Computation for employee attendance before the given date. This is applicable for validation of credited holidays. (Teaching)
     *
     * @param String $date
     * @param String $empid
     *
     * @return string
     */
    public function checkPreviousSchedAttendanceTeaching($date='',$empid=''){
        
        // $continueloop = true;
        // $loopcount = 0;
        // while($continueloop){
        //     if($loopcount==15) return '';

        //     $date = new DateTime($date);
        //     $date->modify('-1 day');
        //     $date = $date->format('Y-m-d');
        //     list($tlec,$tlab,$tadmin,$tabsent,$tdaily_absent,$tel,$tvl,$tsl,$tol,$tdlec,$tdlab,$tdadmin,$holiday,$hasSched,$hasLog,$twork_lec,$twork_lab,$twork_admin,$workhours_arr,$date_list) = $this->computeEmployeeAttendanceSummaryTeaching($date,$date,$empid,true);

        //     if(!$hasSched && !$holiday){
        //         $continueloop = false;
        //         $date_list = true;
        //     }else if($hasSched && !$holiday){
        //         $continueloop = false;
        //     }else if(!$hasSched && $holiday){
        //         $continueloop = false;
        //         $date_list = true;
        //     }

        //     $loopcount++;
        // }
        // // if($tabsent) return $tabsent;
        // // else          return $tdadmin;

        // return $date_list;
        return true;

    }   


    /**
     * @Angelica
     * Computation for employee attendance before the given date. This is applicable for validation of credited holidays. (Non-teaching)
     *
     * @param String $date
     * @param String $empid
     *
     * @return string
     */
    public function checkPreviousSchedAttendanceNonTeaching($date='',$empid=''){
        
        /*$continueloop = true;
        $loopcount = 0;
        while($continueloop){
            if($loopcount==15) return '';
            
            $date = new DateTime($date);
            $date->modify('-1 day');
            $date = $date->format('Y-m-d');
            list($tabsent,$tlec,$tutlec,$totr,$totrest,$tothol,$tel,$tvl,$tsl,$tsl,$tol,$tholiday,$holiday,$hasSched,$hasLog) = $this->computeEmployeeAttendanceSummaryNonTeaching($date,$date,$empid,true);

            if(!$hasSched && !$holiday){
                $continueloop = false;
                $hasLog = true;
            }else if($hasSched && !$holiday){
                $continueloop = false;
            }else if(!$hasSched && $holiday){
                $continueloop = false;
                $hasLog = true;
            }
            $loopcount++;
        }

        return $hasLog;*/
        return true;
    }       


    
    public function dateRange($first, $last, $step = '+1 day', $format = 'Y-m-d' ) {
        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while( $current <= $last ) {    
            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }
        return $dates;
    }

    public function isValidEmployeesHoliday($empid, $date){
        $is_valid_holiday = false;

        return $is_valid_holiday;
    }

   public function getAttendanceCutOffReportDataForTeaching($cutoff, $tnt, $employeeid, $category, $campusid, $is_emp_side=false, $deptid="", $office="", $employmentstat=""){
        $this->load->model("attcompute");
        $data = array();
        list($from_date, $to_date) = explode(",", $cutoff);
        $q_emplist = $this->emp_confirmed($from_date, $to_date, $tnt, $employeeid, $campus, $deptid,$office, $category, '', $employmentstat);
        $q_emplist = ($is_emp_side) ? $this->emp_confirmedperdept($from_date, $to_date, $tnt, $employeeid, $office, $campusid, $deptid) : $q_emplist;
        foreach ($q_emplist as $row) {
            if($category == "campus")  $sort_key = $row["qCampusId"];
            else if($category == "department")  $sort_key = $row["qDeptId"];
            else $sort_key = "name";

            $deptid = $this->employee->getindividualdept($row["qEmpId"]);
            $sort_key = ($is_emp_side) ? $deptid : $sort_key;

            $totUndertime = $this->attcompute->exp_time($row['utadmin']) + $this->attcompute->exp_time($row['utlec']) + $this->attcompute->exp_time($row['utlab']);
            $totLate = $this->attcompute->exp_time($row['latelec']) + $this->attcompute->exp_time($row['latelab']) + $this->attcompute->exp_time($row['lateadmin']);
            $totDeduction = $this->attcompute->exp_time($row['deducperday']);
            $totDeduction = $this->attcompute->sec_to_hm($totDeduction);

            $data[$sort_key][$row["qEmpId"]] = array(
                "name" => $row["qFullname"],
                "ot-regular" => $row["otreg"], 
                "ot-rest-day" => $row["otrest"], 
                "ot-holiday" => $row["othol"], 
                "late" => ($totLate) ? $this->attcompute->sec_to_hm($totLate) : "",
                "undertime" => ($totUndertime) ? $this->attcompute->sec_to_hm($totUndertime) : "",
                "deduclec" => $row["deduclec"],
                "deduclab" => $row["deduclab"],
                "deducadmin" => $row["deducadmin"],
                "vl" => $row["vleave"],
                "sl" => $row["sleave"],
                "scl" => $row["scleave"],
                "ol" => ($row["oleave"] + $row["eleave"]),
                "l_nopay" => $row["l_nopay"],
                "absent" => ($totDeduction) ? ($this->convertTimeToNumber($totDeduction) / 8) : "",
                "no-days" => (!$row["fixedday"]) ? ($this->convertTimeToNumber($row["workhours_admin"]) / 8) : ($this->convertTimeToNumber($row["workhours_admin"]) / 8),
                "holiday" => $row["isholiday"],
                "campusid" => $row["qCampusId"],
                "deptid" => $row["qDeptId"],
                "status" => $row["status"],
            );
        }

        return $data;
    }

    public function getAttendanceCutOffReportDataForNonTeaching($cutoff, $tnt, $employeeid, $category, $campusid, $is_emp_side=false, $deptid="", $office="", $employmentstat){
        $this->load->model("attcompute");
        $data = array();
        list($from_date, $to_date) = explode(",", $cutoff);
        $q_emplist = $this->attendance->emp_confirmed_nt($from_date, $to_date, $tnt, $employeeid, $campus, $deptid,$office,$category, '', $employmentstat);

        $q_emplist = ($is_emp_side) ? $this->emp_confirmed_ntperdept($from_date, $to_date, $tnt, $employeeid, $office, $campusid,$deptid) : $q_emplist;
        foreach ($q_emplist as $row) {
            // echo "<pre>"; print_r($row); die;
            if($category == "campus")  $sort_key = $row["qCampusId"];
            else if($category == "department")  $sort_key = $row["qDeptId"];
            else $sort_key = "name";

            $deptid = $this->employee->getindividualdept($row["qEmpId"]);
            $sort_key = ($is_emp_side) ? $deptid : $sort_key;

            $data[$sort_key][$row["qEmpId"]] = array(
                "name" => $row["qFullname"],
                "ot-regular" => $row["otreg"], 
                "ot-rest-day" => $row["otrest"], 
                "ot-holiday" => $row["othol"],
                "late" => $row["lateut"],
                "undertime" => $row["ut"],
                "vl" => $row["vleave"],
                "sl" => $row["sleave"],
                "scl" => $row["scleave"],
                "l_nopay" => $row["l_nopay"],
                "ol" => ($row["oleave"] + $row["eleave"]),
                "absent" => ($row["absent"]) ? $row["absent"] : "",
                "no-days" => (!$row["fixedday"]) ? $row['workdays'] : $row["workdays"],
                "holiday" => $row["isholiday"],
                "campusid" => $row["qCampusId"],
                "deptid" => $row["qDeptId"]
            );
        }
        // ksort($data);
        $acad_data = $data['ACAD'];
        unset($data['ACAD']);
        $data['ACAD'] = $acad_data;
        return $data;
    }

   public function getTotalHoliday($date_from, $date_to, $employeeid){
        $count_holiday = 0;

        $status = "";
        $q_emp_data = $this->db->query("SELECT CONCAT(office, '~', employmentstat) AS status_included FROM employee WHERE employeeid='$employeeid';")->result();
        foreach ($q_emp_data as $row) $status = $row->status_included;

        $q_count_holiday = $this->db->query("SELECT COUNT(*) AS count_holiday
                                             FROM  code_holiday_calendar a
                                             INNER JOIN holiday_inclusions b ON b.holi_cal_id = a.holiday_id
                                             WHERE ('$date_from' BETWEEN a.date_from AND a.date_to OR '$date_to' BETWEEN a.date_from AND a.date_to) AND status_included LIKE '%$status%'")->result();
        foreach ($q_count_holiday as $row) $count_holiday = $row->count_holiday;
        
        return $count_holiday;
    }

    public function unconfirmedTeachingEmployeeAttendance($dfrom, $dto, $empid){
        $res = $this->db->query("DELETE FROM attendance_confirmed WHERE cutoffstart = '$dfrom' AND cutoffend = '$dto' AND employeeid = '$empid' ");
        return $res;
    }

    public function unconfirmedNonTeachingEmployeeAttendance($dfrom, $dto, $empid){
        $res = $this->db->query("DELETE FROM attendance_confirmed_nt WHERE cutoffstart = '$dfrom' AND cutoffend = '$dto' AND employeeid = '$empid' ");
        return $res;
    }

    public function checkWorkhoursExisting($base_id, $type, $aimsdept){
        $query = $this->db->query("SELECT * FROM workhours_perdept WHERE base_id = '$base_id' AND type = '$type' AND aimsdept = '$aimsdept' ");
        if($query->num_rows > 0) return $query->row()->id;
        else return FALSE;
    }

    public function empCanConfirmAttendance($payroll_start='',$dateresigned=''){
        $canConfirm = true;
        $payroll_start = new DateTime($payroll_start);

        if($dateresigned != '0000-00-00' && $dateresigned != '1970-01-01' && $dateresigned != NULL){
            $dateresigned = new DateTime($dateresigned);

            if($dateresigned < $payroll_start) $canConfirm = false;
        }
        return $canConfirm;
    }

    public function removeLateUtAbsent($date_list,$isnodtr){
        $data = array();
        if($isnodtr){
            foreach($date_list as $key => $value){
                $date_list[$key]['late'] = '';
                $date_list[$key]['absent'] = '';
                $date_list[$key]['undertime'] = '';
            }
        }
        return $date_list;
    }

    public function checkIfHasPendingAttendance($sdate,$edate,$teachingtype,$payroll_start,$payroll_end){
        if($teachingtype == "teaching") $tbl = ' attendance_confirmed';
        else $tbl = ' attendance_confirmed_nt';
        return $this->db->query("SELECT * FROM $tbl WHERE cutoffstart = '$sdate' AND cutoffend = '$edate' AND payroll_cutoffstart = '$payroll_start' AND payroll_cutoffend = '$payroll_end' AND status = 'PENDING' ")->num_rows();   
    }

    public function getTotalAbsentPerday($schedule, $empid, $date){
        $stime = $etime = $type = $seq = $tardy_start = $absent_start = $earlydismissal = "";
        $login = $logout = $q = $haslog_forremarks = "";
        $perday_absent = 0;

        foreach($schedule as $rsched){
            $stime = $rsched->starttime;
            $etime = $rsched->endtime; 
            $type  = $rsched->leclab;
            $seq += 1;
            $tardy_start = $rsched->tardy_start;
            $absent_start = $rsched->absent_start;
            $earlydismissal = $rsched->early_dismissal;

            // logtime
            list($login,$logout,$q,$haslog_forremarks) = $this->attcompute->displayLogTime($empid,$date,$stime,$etime,"NEW",$seq,$absent_start,$earlydismissal);
            if($login=='0000-00-00 00:00:00') $login = '';
            if($logout=='0000-00-00 00:00:00') $logout = '';

            // Absent
            $absent = $this->attcompute->displayAbsent($stime,$etime,$login,$logout,$empid,$date,$earlydismissal, $absent_start);
            
            // Late / Undertime
            list($lateutlec,$lateutlab,$lateutadmin,$tschedlec,$tschedlab,$tschedadmin) = $this->attcompute->displayLateUT($stime,$etime,$tardy_start,$login,$logout,$type,$absent);
            $perday_absent += $this->attcompute->exp_time($tschedadmin);
        }

        return $perday_absent;
    }

    function cutoffHasProcessed($id){
        $att_teaching = $this->db->query("SELECT * FROM cutoff a INNER JOIN attendance_confirmed b ON a.`CutoffFrom` = b.`cutoffstart` AND a.`CutoffTo` = b.`cutoffend` WHERE a.`ID` = '$id'")->num_rows();
        $att_nonteaching = $this->db->query("SELECT * FROM cutoff a INNER JOIN attendance_confirmed_nt b ON a.`CutoffFrom` = b.`cutoffstart` AND a.`CutoffTo` = b.`cutoffend` WHERE a.`ID` = '$id'")->num_rows();
        return $att_teaching + $att_nonteaching;
    }

    function deleteCutoff($id){
        $this->db->query("DELETE FROM cutoff WHERE id = '$id'");

    }

    public function isCutoffExists($cutofffrom, $cutoffto, $payrolldfrom, $payrolldto, $dkey){
        return $this->db->query("SELECT * FROM cutoff a INNER JOIN payroll_cutoff_config b ON a.ID = b.baseid WHERE ((CutoffFrom = '$cutofffrom' AND CutoffTo = '$cutoffto') OR (startdate = '$payrolldfrom' AND enddate = '$payrolldto')) AND a.ID != '$dkey' ")->num_rows();
    }

    public function batchScheduleDateActive($code){
        $q_sched = $this->db->query("SELECT * FROM code_type WHERE code = '$code'");
        if($q_sched->num_rows() > 0) return date("Y-m-d", strtotime($q_sched->row()->date_active));
        else return false;
    }

    public function getLeaveNoPay($date, $employeeid){
        $q_att = $this->db->query("SELECT description FROM leave_request a INNER JOIN code_request_form b ON a.leavetype = b.code_request WHERE '$date' BETWEEN fromdate AND todate AND employeeid='$employeeid' ");
        if($q_att->num_rows() > 0) return $q_att->row()->description;
        else return false;
    }

    public function getTeachingOvertime($base_id){
        $otreg = $otrest = $othol = $otsat = $otsun = 0;
        $overtime = $this->db->query("SELECT * FROM attendance_confirmed_ot_hours WHERE base_id='$base_id}'");
        if($overtime->num_rows() > 0){
            foreach($overtime->result() as $ot_row){
                if($ot_row == "NO_SCHED") $otrest = ($ot_row->ot_hours != "0:00") ? $ot_row->ot_hours : "";
                else if($ot_row == "WITH_SCHED") $otreg = ($ot_row->ot_hours != "0:00") ? $ot_row->ot_hours : "";
                else if($ot_row == "WITH_SCHED_WEEKEND") $otsat = ($ot_row->ot_hours != "0:00") ? $ot_row->ot_hours : "";
                else $othol = ($ot_row->ot_hours != "0:00") ? $ot_row->ot_hours : "";
            }
        }

        return array($otreg, $otrest, $othol, $otsat, $otsun);
    }

    public function getEmployeeOnHoliday($datenow){
        $included = array();
        $emplist = $this->db->query("SELECT * FROM employee WHERE (dateresigned < dateposition OR dateresigned = '0000-00-00' OR dateresigned IS NULL) AND isactive = '1'");
        foreach($emplist->result() as $row){
            $empid = $row->employeeid;
            $deptid = $row->deptid;
            $teachingtype = $row->teachingtype;
            $holiday = $this->attcompute->isHolidayNew($empid,$datenow,$deptid,"POVEDA","", $teachingtype ); 
            if($holiday){
                $included[$empid] = array(
                    "employeeid" => $empid,
                    "deptid" => $deptid,
                    "age" => $row->age,
                    "gender" => $row->gender,
                    "fullname" => $row->lname.", ".$row->fname." ".$row->mname." ",
                    "lname" => $row->lname,
                    "mname" => $row->mname,
                    "fname" => $row->fname
                );
            }
        }

        return $included;
    }

    public function isAttendanceConfirmed($employeeid, $date){
        return $this->db->query("SELECT * FROM attendance_confirmed_nt WHERE employeeid = '$employeeid' AND '$date' BETWEEN cutoffstart AND cutoffend");
    }

    public function timesheetExists($timeid){
        return $this->db->query("SELECT * FROM timesheet WHERE timeid = '$timeid' ");
    }

    public function confirmed_history($cutoffstart, $cutoffend, $sdate, $edate){
        return $this->db->query("SELECT a.employeeid, CONCAT(lname,', ',fname,' ',mname) AS fullname, c.description AS dept, status, d.date_processed, a.confirmedby 
            FROM attendance_confirmed a LEFT JOIN employee b ON a.employeeid = b.employeeid 
            LEFT JOIN code_department c ON b.deptid = c.code 
            LEFT JOIN att_confirm_logs d ON a.employeeid = d.employeeid
            WHERE a.cutoffstart = '$cutoffstart' AND a.cutoffend = '$cutoffend' AND DATE(d.date_processed) BETWEEN '$sdate' AND '$edate' ORDER BY fullname");
    }

    public function confirmed_history_nt($cutoffstart, $cutoffend, $sdate, $edate){
        return $this->db->query("SELECT a.employeeid, CONCAT(lname,', ',fname,' ',mname) AS fullname, c.description AS dept, status, d.date_processed, a.confirmedby 
            FROM attendance_confirmed_nt a LEFT JOIN employee b ON a.employeeid = b.employeeid 
            LEFT JOIN code_department c ON b.deptid = c.code 
            LEFT JOIN att_confirm_logs d ON a.employeeid = d.employeeid
            WHERE a.cutoffstart = '$cutoffstart' AND a.cutoffend = '$cutoffend' AND DATE(d.date_processed) BETWEEN '$sdate' AND '$edate' ORDER BY fullname");
    }

    function saveConfirmationProgress($data){
        $teachingtype = $data['teachingtype'];
        $cutoff = $data['cutoff'];
        $checker = $this->db->query("SELECT * FROM confirm_attendance_progress WHERE teachingtype = '$teachingtype' AND cutoff = '$cutoff'");
        if($checker->num_rows() > 0){
            $this->db->query("DELETE FROM confirm_attendance_progress WHERE teachingtype = '$teachingtype' AND cutoff = '$cutoff'");
            $this->db->query("DELETE FROM confirming_attendance_result WHERE teachingtype = '$teachingtype' AND cutoff = '$cutoff'");
            $this->db->insert("confirm_attendance_progress", $data);
            return 'ongoing';
        }else{
            $this->db->insert("confirm_attendance_progress", $data);
            return 'success';
        }
    }

    function processingConfirmation($tnt, $dto, $dfrom){
        $this->load->model('utils');
        $cutoff = $dfrom.'~|~'.$dto;
        $success_count = $failed_count = $failed = $success = 0;
        $res = $deptid = $dateresigned = $hold_status = '';
        $usertype   = $this->session->userdata("usertype");
        $query = $this->db->query("SELECT current_count, total_count, employeelist FROM confirm_attendance_progress WHERE teachingtype = '$tnt' AND cutoff = '$cutoff'");
        if($query->num_rows() > 0){
            $current_count =  $query->row(0)->current_count;
            $total_count =  $query->row(0)->total_count;
            $employeelist =  $query->row(0)->employeelist;
            $emplist = explode(',', $employeelist);
            if(count($emplist) > 0){
                $counter = 0;
                $newEmplist = '';
                foreach ($emplist as $uid) {
                    if($counter <= rand(1,5)){
                        list($dtr_start,$dtr_end,$payroll_start,$payroll_end,$payroll_quarter) = $this->payrolloptions->getDtrPayrollCutoffPair($dfrom,$dto);
                        $canConfirm = false;
                        $emp_data = $this->utils->getEmployeeInfo('teachingtype,deptid,dateresigned',array('employeeid'=>$uid));
                        if($emp_data){
                          $deptid       = $emp_data[0]->deptid;
                          $dateresigned = $emp_data[0]->dateresigned;
                          $canConfirm   = $this->attendance->empCanConfirmAttendance($payroll_start,$dateresigned);
                        }

                        if($canConfirm){
                            if($tnt == 'teaching'){
                                $isBED = false;
                                $bed_depts = $this->extensions->getBEDDepartments();
                                if(in_array($deptid, $bed_depts)) $isBED = true;
                                $res = $this->attendance->saveEmployeeAttendanceSummaryTeaching($dfrom,$dto,$payroll_start,$payroll_end,$payroll_quarter,$uid, $isBED, $hold_status, $usertype);
                            }elseif($tnt == 'nonteaching'){
                                $res = $this->attendance->saveEmployeeAttendanceSummaryNonTeaching($dfrom,$dto,$payroll_start,$payroll_end,$payroll_quarter,$uid, $hold_status, $usertype);
                            }
                        }

                        if($canConfirm) $success_count++;
                        else $failed_count++;

                        $current_count++;
                        if($current_count >= $total_count){
                            $this->db->query("DELETE FROM confirm_attendance_progress WHERE teachingtype = '$tnt' AND cutoff = '$cutoff'");
                            $this->db->query("DELETE FROM confirming_attendance_result WHERE teachingtype = '$tnt' AND cutoff = '$cutoff'");
                        }
                        else{
                            $this->db->query("UPDATE confirm_attendance_progress SET current_count = '$current_count' WHERE teachingtype = '$tnt' AND cutoff = '$cutoff'");
                        }
                        $counter++;
                    }else{
                        if($newEmplist) $newEmplist .= ','.$uid;
                        else $newEmplist .= $uid;
                    }
                }
            }

            if($newEmplist != ''){
                $this->db->query("UPDATE confirm_attendance_progress set employeelist = '$newEmplist' WHERE teachingtype = '$tnt' AND cutoff = '$cutoff'");
            }

            $result_query = $this->db->query("SELECT * FROM confirming_attendance_result WHERE teachingtype = '$tnt' AND cutoff = '$cutoff'");
            if($result_query->num_rows() > 0){
                $success =  $result_query->row(0)->success + $success_count;
                $failed =  $result_query->row(0)->failed + $failed_count;
                $this->db->query("UPDATE confirming_attendance_result SET success = '$success', failed = '$failed' WHERE teachingtype = '$tnt' AND cutoff = '$cutoff'");
            }else{
                $success = $success_count;
                $failed = $failed_count;
                $this->db->query("INSERT INTO confirming_attendance_result(teachingtype, cutoff, success, failed) VALUES ('$tnt', '$cutoff', '$success_count', '$failed_count')");
            }

            $query = $this->db->query("SELECT CONCAT('[',current_count,'/',total_count,']') as progress, current_count, total_count FROM confirm_attendance_progress WHERE teachingtype = '$tnt' AND cutoff = '$cutoff'");
            if($query->num_rows() > 0){
                $current_count =  $query->row(0)->current_count;
                $total_count =  $query->row(0)->total_count;
                if($current_count >= $total_count){
                    $this->db->query("DELETE FROM confirm_attendance_progress WHERE teachingtype = '$tnt' AND cutoff = '$cutoff'");
                    return 0;
                }else{
                    return 'Processing Employees '.$query->row(0)->progress.'</br>'.'Success: '.$success.'</br>'.'Failed: '.$failed;
                }
            }
            else{
                $this->db->query("DELETE FROM confirming_attendance_result WHERE teachingtype = '$tnt' AND cutoff = '$cutoff'");
                return 0;
            }

        }else{
            return 0;
        }
    }

}
// EOF...