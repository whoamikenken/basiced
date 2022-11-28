<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee extends CI_Model {
  function loadfieldemployee($field,$employeeid){
     $this->db->_reset_select(); /** reset active select */
     $this->db->select($field);
     $this->db->where("employeeid",$employeeid);
     $g = $this->db->get("employee");
     $return = "";
     if($g->num_rows()>0) $return = $g->row(0)->$field;
     return $return;
  }

  function loademployeeforadjustment($date='',$campus='',$cluster='',$deptid='',$etype='', $status='', $office='')
  {
    $datenow = date('Y-m-d');
    $wC = "";
    // if ($date) { $wC .= "AND date(b.timein)= '{$date}'"; }
    if ($campus) $wC .= " AND campusid= '{$campus}'"; 
    if ($cluster)  $wC .= " AND emptype= '{$cluster}'"; 
    if($etype) $wC .= " AND teachingtype = '$etype'";
    if($deptid) $wC .= " AND deptid = '$deptid'";
    if($status && $status !='all'){
      if($active==='1'){
            $wC .= " AND ('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive = '1'";
          }else{
            $wC .= " AND ('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01') OR isactive = 0";
          }
    }
    if($office) $wC  .= " AND office = '$office'";
     $query = $this->db->query("SELECT * FROM employee WHERE 1 $wC GROUP BY lname")->result();
    return $query;
  }

  //  function loadtimesheet($date='',$campus='',$cluster='',$employeeid='')
  // {
  //   $wC = "";
  //   if ($date) { $wC .= "AND date(b.timein)= '{$date}'"; }
  //   if ($campus) { $wC .= "AND a.campusid= '{$campus}'"; }
  //   if ($cluster) { $wC .= "AND a.emptype= '{$cluster}'"; }
  //   if ($employeeid) { $wC .= "AND a.employeeid= '{$employeeid}'"; }
  //    $query = $this->db->query("SELECT a.employeeid, b.`timein`,b.`timeout`,c.`logtime` AS timeinonly FROM employee a INNER JOIN timesheet b   ON (a.`employeeid` = b.`userid`)  LEFT JOIN timesheet_trail c
  //  ON (a.`employeeid` = c.`userid`) WHERE (dateresigned='1970-01-01' OR dateresigned='0000-00-00' OR dateresigned IS NULL) $wC  LIMIT 2")->result();
  //   return $query;
  // }
  function clearotherIncomedata($data)
  {
     $user = $this->session->userdata("username");
    $return = array("err_code"=>0,"msg"=>"");
    $countdata = 0;
    $wC = "";
    // if ($data['emp']) { $wC .= "AND employeeid='$data['emp']'";}
    // if ($data['emp']) { $wC .= "AND employeeid='$data['emp']'";}
    if ($data['othIncome']) {
            $InsertQuery = $this->db->query("INSERT INTO other_income_history(employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,timestamp,appliedby,status) SELECT employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,CURRENT_TIMESTAMP,'$user','DELETED' FROM other_income WHERE other_income='{$data['othIncome']}'");
            // $InsertQuery = $this->db->query("INSERT INTO other_income_history SELECT * FROM other_income WHERE other_income ='{$data['othIncome']}' ");

            if ($InsertQuery === TRUE) {
                $SelectQuery = $this->db->query("SELECT * FROM other_income WHERE other_income='{$data['othIncome']}'");
                $countdata = $SelectQuery->num_rows();
                // echo $countdata;die;
                // $countdata = count($SelectQuery);
                $DeleteQuery = $this->db->query("UPDATE other_income SET monthly='',daily='',hourly='' WHERE other_income ='{$data['othIncome']}'");
                if ($DeleteQuery === TRUE) {
                      $return = array("err_code"=>2,"msg"=>"Successfully Clear!","count"=>$countdata);
                }
                else
                {
                    $return = array("err_code"=>0,"msg"=>"Failed to Delete!");
                }
            }
            else
            {
              $return = array("err_code"=>0,"msg"=>"Failed to Delete!");
            }
    }
    return $return;

  }

  public function loadRegisteredEmployees(){
        $q_employee = $this->db->query("SELECT CONCAT(fname, ' ', mname, ' ', lname) AS fullname, employeeid FROM employee")->result_array();
        return $q_employee;
    }

  function saveEditedOtherIncome($data)
  {
     $user = $this->session->userdata("username");
    $return = array("err_code"=>0,"msg"=>"");
    if ($data['otherIncome']) {

        $query = $this->db->query("UPDATE other_income SET monthly='{$data['monthly']}',daily='{$data['daily']}',hourly='{$data['hourly']}',dateEffective='{$data['efdate']}',dateEnd='{$data['edate']}'  WHERE employeeid='{$data['employeeid']}' AND other_income='{$data['otherIncome']}'");
        if ($query) {
           $queryInsert = $this->db->query("INSERT INTO other_income_history(employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,timestamp,appliedby,status) SELECT employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,CURRENT_TIMESTAMP,'$user','UPDATED' FROM other_income WHERE employeeid='{$data['employeeid']}' AND other_income='{$data['otherIncome']}'");
            $return = array("err_code"=>2,"msg"=>"Successfully Updated!");
        }
        else
        {
             $return = array("err_code"=>0,"msg"=>"Failed to update!");
        }
    }
    else
    {
        $return = array("err_code"=>0,"msg"=>"Failed to update!");
    }
    return $return;

  }

  function loadtimesheet($date='',$campus='',$cluster='',$employeeid='')
  {
    $wC = "";
    if ($date) { $wC .= "WHERE DATE(timein)= '{$date}'"; }
    if ($campus) { $wC .= "AND b.campusid= '{$campus}'"; }
    if ($cluster) { $wC .= "AND b.emptype= '{$cluster}'"; }
    if ($employeeid) { $wC .= "AND a.userid= '{$employeeid}'"; }

    $query = $this->db->query(" SELECT a.timein,a.timeout,a.timestamp,a.timeid,b.* FROM timesheet a INNER JOIN employee  b ON(a.`userid` = b.`employeeid`)  $wC   ORDER BY a.timein");
    if ($query->num_rows() == 0) {

      $wC = "";
      if ($date) { $wC .= "WHERE DATE(a.logtime)= '{$date}'"; }
      if ($campus) { $wC .= "AND b.campusid= '{$campus}'"; }
      if ($cluster) { $wC .= "AND b.emptype= '{$cluster}'"; }
      if ($employeeid) { $wC .= "AND a.userid= '{$employeeid}'"; }
        $timeinonly = $this->db->query("SELECT a.logtime AS timein,'' AS timeout,a.userid AS employeeid,'' AS timeid FROM timesheet_trail a INNER JOIN employee  b ON(a.`userid` = b.`employeeid`)  $wC  ORDER BY timein LIMIT 1");
      if($timeinonly->num_rows() == 0){
        $wC = "";
        if ($date) { $wC .= "WHERE DATE(a.localtimein)= '{$date}'"; }
        if ($campus) { $wC .= "AND b.campusid= '{$campus}'"; }
        if ($cluster) { $wC .= "AND b.emptype= '{$cluster}'"; }
        if ($employeeid) { $wC .= "AND a.userid= '{$employeeid}'"; }
          $timeinonly = $this->db->query("SELECT a.localtimein AS timein,'' AS timeout,a.userid AS employeeid,'' AS timeid FROM webcheckin_history a INNER JOIN employee  b ON(a.`userid` = b.`employeeid`)  $wC  ORDER BY a.localtimein LIMIT 1");
      }
        return $timeinonly->result();
    }
    else
    {
      return $query->result();
    }

  }

  function loadtimeonly($date='',$campus='',$cluster='',$employeeid='')
  {
    $wC = $timeinonly =  "";
    if ($date) { $wC .= "WHERE DATE(b.logtime)= '{$date}'"; }
    if ($campus) { $wC .= "AND c.campusid= '{$campus}'"; }
    if ($cluster) { $wC .= "AND c.emptype= '{$cluster}'"; }
    if ($employeeid) { $wC .= "AND a.userid= '{$employeeid}'"; }
    #echo "<pre>". "SELECT b.`logtime` AS timeinonly FROM timesheet_trail b INNER JOIN timesheet a ON(a.`userid` = b.`userid`)  $wC ORDER BY b.`logtime` ASC LIMIT 1";
     $query = $this->db->query("SELECT b.`logtime` AS timeinonly FROM timesheet_trail b INNER JOIN timesheet a ON(a.`userid` = b.`userid`) INNER JOIN employee c ON(c.employeeid = b.userid)  $wC ORDER BY b.`logtime` ASC LIMIT 1")->result();
     if(count($query) == 0){

      $wC = $timeinonly =  "";
    if ($date) { $wC .= "WHERE DATE(b.localtimein)= '{$date}'"; }
    if ($campus) { $wC .= "AND c.campusid= '{$campus}'"; }
    if ($cluster) { $wC .= "AND c.emptype= '{$cluster}'"; }
    if ($employeeid) { $wC .= "AND a.userid= '{$employeeid}'"; }
    
          $query = $this->db->query("SELECT b.`localtimein` AS timeinonly FROM webcheckin_history b INNER JOIN timesheet a ON(a.`userid` = b.`userid`) INNER JOIN employee c ON(c.employeeid = b.userid)  $wC ORDER BY b.`localtimein` ASC LIMIT 1")->result();
      }

     if (count($query) > 0) {
        foreach ($query as $q ) $timeinonly = $q->timeinonly;
     }
    return $timeinonly;
  }
  function timesheetrailExist($timein='',$date='',$employeeid='')
  {
    $wC =  "";
    $return = false;
    $query = $this->db->query("SELECT *,b.timein as timein FROM timesheet_trail a INNER JOIN timesheet b ON(a.`userid` = b.`userid`) WHERE a.userid='$employeeid' AND DATE(b.timein)='$date' AND TIME(b.timein) LIKE '%$timein%'");
    if ($query->num_rows() > 0) {
      return $return = true;
    }
    else
    {
      return $return = false;
    }
  }

  function getBankDesc($empid = ''){
    $query = $this->db->select("SELECT emp_bank from employee where employeeid = '$empid'");
    return $query;
  }

  function loadallemployee($con="",$sort="",$limit="",$end="",$resigned = false,$etype = "",$deptcode = "",$active = "all", $empstatus="", $office=""){
     $returns = array();
     $this->db->_reset_select(); /** reset active select */
     $datenow = date('Y-m-d');
     /** select fields */
     $this->db->select("employeeid,employeecode,emptype,empshift,date_active,employmentstat,deptid,office,lname,fname,mname,nname,suffix,gender,mobile,email,personal_email,cityaddr,provaddr,regionaladdr,barangay,zip_code,addr,citytelno,bdate,bplace,maxregular,maxparttime,dateemployed,campusid,civil_status,spouse_name,spouse_contact,income_base,tax_status,blood_type,height,weight,dateresigned,dateresigned2,resigned_reason,emp_tin,emp_sss,emp_philhealth,emp_pagibig,emp_peraa ,emp_medicare,emp_bank, emp_accno,positionid,dateposition,assignment,remarks,managementid,citizenid,religionid,nationalityid,prc,passport,visa,icardnum,crnno,permanentaddr,cp_name,cp_relation,cp_address,cp_mobile,cp_telno,teaching,teachingtype,isactive,leavetype,occupation,age,aimcheckbox,aimsdept,mother,motheroccu,father,fatheroccu,distinguishingMarks,hospitalized,hospitalizedtxt,operation,operationtxt,operationdate,medhistory,medhistorytxt,medconditions,prc_expiration,passport_expiration,emp_hmo,landline,permaAddress,permaRegion,permaProvince,permaBarangay,permaMunicipality,permaZipcode,rank");

     /** condition where */
     # if(is_array($con)){
     #   foreach($con as $f=>$v) $this->db->where($f,$v);
     #}

     #if($resigned)  $this->db->where("dateresigned","1970-01-01");
     // if($resigned)  $this->db->where("(dateresigned='1970-01-01' OR dateresigned='0000-00-00' OR dateresigned IS NULL) AND FIND_IN_SET(deptid,'$deptcode')"); @Angelica - comment
     if($resigned)  $this->db->where("(dateresigned2='1970-01-01' OR dateresigned2='0000-00-00' OR dateresigned2 IS NULL)");
     if($etype)     $this->db->where("teachingtype",$etype);
     if($deptcode)  $this->db->where("FIND_IN_SET(deptid,'$deptcode') !=",0);
     if($office)  $this->db->where("FIND_IN_SET(office,'$office') !=",0);
     if($empstatus)  $this->db->where("FIND_IN_SET(employmentstat,'$empstatus') !=",0);
     if($active == "active")    $this->db->where("('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive = '1'");
     else if($active == "inactive")    $this->db->where("('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01') OR isactive = 0");
     /** condition or */
     if($con && is_array($con)){
        foreach($con as $f=>$v){
         if($v) $this->db->or_where($f,$v);
        }
     }

     /** sort display */
     if(is_array($sort)){
       foreach($sort as $f){
          list($fi,$si) = $f;
          $this->db->order_by($fi,$si);
       }
     }

     /** limit display */
     if(is_numeric($limit) && $end) $this->db->limit($end,$limit);
     else if($end) $this->db->limit($end);

     $q = $this->db->get("employee")->result();
     
    # $q = $this->db->get("employee");

    # for($t=0;$t<$q->num_rows();$t++){
    foreach($q as $row){
    # $row = $q->row($t);
      array_push($returns,$row);
    }
     # print_r($returns);
     $reform = array();
     $returns = Globals::result_XHEP($returns);
     foreach($returns as $row){
      $lg = $this->db->query("select legitimate_name,legitimate_relation,legitimate_address,legitimate_contactno,legitimate_bdate,legit from employee_legitimate_relations where employeeid='{$row->employeeid}'")->result();
      $legitimate = array();
      if(count($lg)>0) foreach($lg as $mrow_lg) array_push($legitimate,"{$mrow_lg->legitimate_name}~u~{$mrow_lg->legitimate_relation}~u~{$mrow_lg->legitimate_address}~u~{$mrow_lg->legitimate_contactno}~u~{$mrow_lg->legitimate_bdate}~u~{$mrow_lg->legit}");
      # print_r($legitimate);
      $date_active = ($row->date_active != NULL && $row->date_active != '0000-00-00 00:00:00') ? date("Y-m-d",strtotime($row->date_active)) : '';

      $tarrs = array(
                     "employeeid"=>$row->employeeid,
                     "employeecode"=>$row->employeecode,
                     "lname"=>$row->lname,
                     "fname"=>$row->fname,
                     "mname"=>$row->mname,
                     "nname"=>$row->nname,
                     "suffix"=>$row->suffix,
                     "deptid"=>$row->deptid,
                     "office"=>$row->office,
                     "cityaddr"=>$row->cityaddr,
                     "provaddr"=>$row->provaddr,
                     "regaddr"=>$row->regionaladdr,
                     "barangay"=>$row->barangay,
                     "zip_code"=>$row->zip_code,
                     "addr"=>$row->addr,
                     "occupation"=>$row->occupation,
                     "age"=>$row->age,
                     "bdate"=>date("Y-m-d",strtotime($row->bdate)),
                     "months_b"=>date("m",strtotime($row->bdate)),
                     "days_b"=>date("d",strtotime($row->bdate)),
                     "years_b"=>date("Y",strtotime($row->bdate)),
                     "gender"=>$row->gender,
                     "emptype"=>$row->emptype,
                     "empshift"=>$row->empshift,
                     "date_active"=>$date_active,
                     "employmentstat"=>$row->employmentstat,
                     "bplace"=>$row->bplace,
                     "mobile"=>$row->mobile,
                     "citytelno"=>$row->citytelno,
                     "email"=>$row->email,
                     "personal_email"=>$row->personal_email,
                     "maxregular"=>$row->maxregular,
                     "maxparttime"=>$row->maxparttime,
                     "income_base"=>$row->income_base,
                     "tax_status"=>$row->tax_status,
                     "aimsdept" =>$row->aimsdept,
                     "aimcheckbox" => $row->aimcheckbox,
                     "civil_status"=>$row->civil_status,
                     "spouse_name"=>$row->spouse_name,
                     "spouse_contact"=>$row->spouse_contact,
                     "dateemployed"=>date("Y-m-d",strtotime($row->dateemployed)),
                     "campusid"=>$row->campusid,
                     "blood_type"=>$row->blood_type,
                     "height"=>$row->height,
                     "weight"=>$row->weight,
                     "month_employed_b"=>date("m",strtotime($row->dateemployed)),
                     "days_employed_b"=>date("d",strtotime($row->dateemployed)),
                     "years_employed_b"=>date("Y",strtotime($row->dateemployed)),
                     "dateresigned"=>$row->dateresigned != '0000-00-00' ? date("Y-m-d",strtotime($row->dateresigned)) : '',
                     "dateresigned2"=>$row->dateresigned2 != '0000-00-00' ? date("Y-m-d",strtotime($row->dateresigned2)) : '',
                     "position"=>$row->positionid,
                     "dateposition"=>$row->dateposition,
                     "assignment"=>$row->assignment,
                     "remarks"=>$row->remarks,
                     "management"=>$row->managementid,
                     "resigned_reason"=>$row->resigned_reason,
                     "tinno"=>$row->emp_tin,
                     "sssno"=>$row->emp_sss,
                     "philhealth"=>$row->emp_philhealth,
                     "pagibig"=>$row->emp_pagibig,
                     "peraa"=>$row->emp_peraa,
                     "medicare"=>$row->emp_medicare,
                     "emp_bank"=>$row->emp_bank,
                     "emp_accno"=>$row->emp_accno,
                     "citizenship"=>$row->citizenid,
                     "religion"=>$row->religionid,
                     "nationality"=>$row->nationalityid,
                     "prc"=>$row->prc,
                     "passport"=>$row->passport,
                     "visa"=>$row->visa,
                     "icard"=>$row->icardnum,
                     "crn"=>$row->crnno,
                     "permanentaddress"=>$row->permanentaddr,
                     "cp_name"=>$row->cp_name,
                     "cp_relation"=>$row->cp_relation,
                     "cp_address"=>$row->cp_address,
                     "cp_mobile"=>$row->cp_mobile,
                     "cp_telno"=>$row->cp_telno,
                     "teaching"=>$row->teaching,
                     "teachingtype"=>$row->teachingtype,
                     "isactive"=>$row->isactive,
                     "leavetype"=>$row->leavetype,
                     "mother"=>$row->mother,
                     "motheroccu"=>$row->motheroccu,
                     "father"=>$row->father,
                     "fatheroccu"=>$row->fatheroccu,
                     "distinguishingMarks"=>$row->distinguishingMarks,
                     "hospitalized"=>$row->hospitalized,
                     "hospitalizedtxt"=>$row->hospitalizedtxt,
                     "operation"=>$row->operation,
                     "operationtxt"=>$row->operationtxt,
                     "operationdate"=>$row->operationdate,
                     "medhistory"=>$row->medhistory,
                     "medhistorytxt"=>$row->medhistorytxt,
                     "medconditions"=>$row->medconditions,
                     "prc_expiration"=>$row->prc_expiration,
                     "passport_expiration"=>$row->passport_expiration,
                     "emp_hmo"=>$row->emp_hmo,
                     "landline"=>$row->landline,
                     "permaAddress"=>$row->permaAddress,
                     "permaMunicipality"=>$row->permaMunicipality,
                     "permaProvince"=>$row->permaProvince,
                     "permaRegion"=>$row->permaRegion,
                     "permaBarangay"=>$row->permaBarangay,
                     "permaZipcode"=>$row->permaZipcode,
                     "rank"=>$row->rank,
                     "legitimate_relations"=>$legitimate
                   );
                   array_push($reform,$tarrs);
     }
    return $reform;
  }
  /**
  * Save applicable fields for employee personal information, educational background and trainings.
  */
  function saveApplicableField($employeeid="", $field="", $value=""){
      $res = $this->db->query("SELECT id FROM employee_applicable_fields WHERE employeeid='$employeeid'");
      if($res->num_rows > 0) $res = $this->db->query("UPDATE employee_applicable_fields SET $field='$value' WHERE employeeid='$employeeid'");
      else                   $res = $this->db->query("INSERT INTO employee_applicable_fields (employeeid, $field) VALUES ('$employeeid','$value')");
      return $res;
  }

  function showempnores($deptid = "",$estatus="",$etype="", $campusid="", $officeid="", $isactive=""){
        $wC = "";
        $return = "<option value=''>All Employee</option>";
       if($deptid && $deptid != 'undefined')  $wC .= " AND deptid = '$deptid'";
       if($officeid && $officeid != 'undefined')  $wC .= " AND office = '$officeid'";
       if($estatus && $estatus != 'undefined') $wC .= " AND employmentstat = '$estatus'";
       if($etype && $etype != 'undefined')   $wC .= " AND teachingtype='$etype'";
       if($campusid && $campusid != 'undefined')   $wC .= " AND campusid='$campusid'";
       if($isactive!="" && $isactive != 'undefined')   $wC .= " AND isactive='$isactive'";
       $query = $this->db->query("SELECT employeeid,lname,fname,mname FROM employee WHERE (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00') $wC ORDER BY lname")->result();
       foreach($query as $val){
          $return .= "<option value='".$val->employeeid."'>".$val->employeeid." - ".Globals::_e($val->lname.", ".$val->fname." ".$val->mname)."</option>";
       }
       return $return;
    }

  function loademployee_deduction($employeeid=""){
     $returns = array();
     $this->db->_reset_select(); /** reset active select */
     /** select fields */
     $this->db->select("a.code_deduction,b.description,a.memberid");
     $this->db->from("employee_deduction a");
     $this->db->join("deductions b","b.code_deduction=a.code_deduction");
     $this->db->where("a.employeeid",$employeeid);
     $this->db->order_by("b.description","asc");

     $q = $this->db->get();
     for($t=0;$t<$q->num_rows();$t++){
      $row = $q->row($t);
      array_push($returns,$row);
     }

     $reform = array();
     foreach($returns as $row){
       array_push($reform,array("code_deduction"=>$row->code_deduction,"description"=>$row->description,"memberid"=>$row->memberid));
     }
    return $reform;
  }
  function loademployee_income($employeeid=""){
     $returns = array();
     $this->db->_reset_select(); /** reset active select */
     /** select fields */
     $this->db->select("a.code_income,b.description,a.income_base");
     $this->db->from("employee_income a");
     $this->db->join("incomes b","b.code_income=a.code_income");
     $this->db->where("a.employeeid",$employeeid);
     $this->db->order_by("b.description","asc");

     $q = $this->db->get();
     for($t=0;$t<$q->num_rows();$t++){
      $row = $q->row($t);
      array_push($returns,$row);
     }

     $reform = array();
     foreach($returns as $row){
       array_push($reform,array("code_income"=>$row->code_income,"description"=>$row->description,"income_base"=>$row->income_base));
     }
    return $reform;
  }
  function loademployee_salary($employeeid="",$reform){
     $returns = array();
     $this->db->_reset_select(); /** reset active select */

     /** select fields */
     $this->db->select("a.employeeid,a.rate_type,a.salary_type,a.amount");
     $this->db->from("employee_salary a");
     $this->db->where("a.employeeid",$employeeid);
     # $this->db->order_by("b.description","asc");
     $q = $this->db->get();

     for($t=0;$t<$q->num_rows();$t++){
      $row = $q->row($t);
      array_push($returns,$row);
     }

     foreach($returns as $row){
       # array_push($reform,array("code_deduction"=>$row->code_deduction,"description"=>$row->description,"memberid"=>$row->memberid));
       $reform[$row->rate_type][$row->salary_type] = $row->amount;
     }
    return $reform;
  }
  /**
   *    Retrieve Employee's Allowed leaves.
   *    Allowed Leaves are based on the data and is generic to most employees.
   *    @return ARRAY : the resulting rows from the table.
   */
  function getEmployeeAllowedLeaves($eid = "") {
        $query = $this->db->query("SELECT * FROM code_request_form INNER JOIN employee USING (leavetype) WHERE is_leave=1 AND employeeid='$eid'");
        return $query->result();
  }
  /**
   *    Retrieve current employee`s applied leaves.
   *    @param $employee_id STRING - the employee_id/unique identifier of current "logger".
   *    @return ARRAY - resulting data of all the current employee's applied leaves.
   */
  function getEmployeeLeaveInfo( $employee_id ) {
        $this->db->where("employeeid",$employee_id);
        $q = $this->db->get('leave_request');
        $leaves = array();
        for($t=0;$t<$q->num_rows();$t++){
          $leaves[] = $q->row($t);
         }
        return $leaves;
  }
  /**
   *    Retrieve Personal Information Configurations
   *        - Personal Information are:
   *            -- Civil Status
   *            -- Gender
   *            -- Nationality
   *            -- Religion
   *            -- Citizenship
   *            -- Relationship
   *            -- Employment Status
   *    @param $info_type STRING - the type of Information to be gathered (i.e.: Gender)
   *    @return ARRAY - resulting data of all the current employee's applied leaves.
   */
  function getPersonnelInfoConfigList( $info_type, $id=0 ) {
        $tbl = $this->getInfoTypeTable($info_type);

        if( $id !== 0 ){
            $pk = $this->getTablePK($tbl);
            if( $pk ) $this->db->where($pk, $id);
        }

        $q = $this->db->get($tbl);
        $returns = array();
        for($t=0;$t<$q->num_rows();$t++){
          $returns[] = $q->row($t);
         }
        return $returns;
  }
  function savePersonnelInfoTypes( $info_type, $action, $data, $code="" ) {
        // check if the id specified already exists!
        $tbl = $this->getInfoTypeTable($info_type);
        $pk = $this->getTablePK($tbl);
        $check = $this->getPersonnelInfoConfigList($info_type, isset($data[$pk]) ? $data[$pk] : "");
        $id = $data['id'];
        $nID = $this->getNewId($tbl, $pk);
        $newId = json_decode(json_encode($nID[0]), true);
        $newId = array_values($newId);
        unset($data['id']);
        if($tbl == 'code_status') $id = $data['code'];

        $return = array("err_code"=>0, 'msg'=>"Success.");

        if($id != 0){
            if( $action == 'add' ) {
                if( count($check) > 0 )
                    $return = array("err_code"=>2,"msg"=>"Code already exists.");


                $insertdata = $data;

                $this->db->insert($tbl, $insertdata);
                $return = array("err_code"=>0, 'msg'=>"Success.");
            }
            elseif( $action == 'edit' ) {
                if( count($check) > 0 )
                    // if( $id !== $data[$pk] ) $return = array("err_code"=>2,"msg"=>"Code already exists.");

                    $this->db->where($pk, $id);
                    $this->db->update($tbl, $data);
                    $return = array("err_code"=>0, 'msg'=>"Success.");
            }
        }
        else{
            if( $action == 'add' && $tbl != "code_type") {
                if( count($check) > 0 )
                    $return = array("err_code"=>2,"msg"=>"Code already exists.");
                if($tbl == "code_status"){
                  $insertdata = $data;
                }else{
                  $insertdata = array(
                              $pk => $newId[0]+1,
                              'description' => $data['description']
                              );
                }
                  
                $this->db->insert($tbl, $insertdata);
                $return = array("err_code"=>0, 'msg'=>"Success.");

            }
            elseif( $action == 'edit' ) {
                if( count($check) > 0 )
                    if( $id !== $data[$pk] ) $return = array("err_code"=>2,"msg"=>"Code already exists.");

                    $this->db->where($pk, $id);
                    $this->db->update($tbl, $data);
                    $return = array("err_code"=>0, 'msg'=>"Success.");
            }
        }
        /** This is for attaching schedule to employee
         *  Triggered :
         *     saving data into table `code_type`
         */
        if($tbl=='code_type'){
            if($return['err_code']==0){
                $date             = new DateTime($data['date_active']);
                // $prev_date_active = new DateTime($prev_date_active);

                // $prev_date_active->modify('-1 day');
                // $prev_date_active = $prev_date_active->format('Y-m-d');

                $date_orig = $date->format('Y-m-d');
                $date->modify('-1 day');
                $data['date_active'] = $date->format('Y-m-d');
                ///< schedule will not be changed if date_active is already processed
                // $processed = $this->db->query("SELECT * FROM payroll_employee_attendance_nt WHERE '{$data['date_active']}' <= cutoffend");
                // if($processed->num_rows() == 0){
                //       $q = $this->db->query("select employeeid from employee where emptype='{$data['code']}'")->result();
                //       $total_count = count($q);
                //       $current_count = 0;
                //       $this->db->query("INSERT INTO code_type_progress(total_count, emptype)VALUES('$total_count', '{$data['code']}')");
                //       foreach($q as $row){
                //         // echo "<pre>"; print_r("riel"); die;
                //         // if($prev_date_active == $date_active){
                //           ///< if change schedule within 1 day, prev sched with the given date will be removed
                //           // $prev_date_active .= ' 00:00:00';
                //           $this->db->query("DELETE FROM employee_schedule_history WHERE employeeid='{$row->employeeid}' AND DATE_FORMAT(dateactive,'%Y-%m-%d %H:%i:%s')='{$data['date_active']}".' 00:00:00'."';");
                //         // }

                //         $this->db->query("UPDATE employee SET date_active='$date_orig' WHERE employeeid='{$row->employeeid}'");
                //         $this->db->query("CALL prc_employee_schedule_pershift('{$row->employeeid}','{$data['schedid']}','{$data['code']}','{$data['date_active']}','".$this->session->userdata("userid")."')");
                //         $current_count++;
                //         if($current_count >= $total_count) $this->db->query("DELETE FROM code_type_progress WHERE emptype='{$data['code']}'");
                //         else $this->db->query("UPDATE code_type_progress SET current_count = '$current_count' WHERE emptype= '{$data['code']}'");
                //       }
                // }else $return = array("err_code"=>3, 'msg'=>"Failed to save schedule to employees. Effectivity date is already processed.");

                $employeelist = $this->db->query("select employeeid, teachingtype from employee where emptype='{$data['code']}'")->result();
                $excluded = array();
                // foreach ($employeelist as $row) {
                //    if($row->teachingtype == "teaching"){
                //       $processed = $this->db->query("SELECT * FROM payroll_employee_attendance WHERE '{$data['date_active']}' <= cutoffend AND employeeid = '{$row->employeeid}'");
                //       if($processed->num_rows() > 0){
                //         $excluded[] = $row->employeeid;
                //       }
                //    }else{
                //       $processed_nt = $this->db->query("SELECT * FROM payroll_employee_attendance_nt WHERE '{$data['date_active']}' <= cutoffend AND employeeid = '{$row->employeeid}'");
                //       if($processed_nt->num_rows() > 0){
                //         $excluded[] = $row->employeeid;
                //       }
                //    }
                // }
                $excluded = implode(',', $excluded);
                $q = $this->db->query("select employeeid from employee where emptype='{$data['code']}' AND employeeid NOT IN (SELECT employeeid FROM employee WHERE FIND_IN_SET(employeeid, '$excluded') )")->result();
                $total_count = count($q);
                $current_count = 0;
                $this->db->query("INSERT INTO code_type_progress(total_count, emptype)VALUES('$total_count', '{$data['code']}')");
                foreach($q as $row){
                  // if($prev_date_active == $date_active){
                    ///< if change schedule within 1 day, prev sched with the given date will be removed
                    // $prev_date_active .= ' 00:00:00';
                    $this->db->query("DELETE FROM employee_schedule_history WHERE employeeid='{$row->employeeid}' AND DATE_FORMAT(dateactive,'%Y-%m-%d %H:%i:%s')='{$data['date_active']}".' 00:00:00'."';");
                  // }

                  $this->db->query("UPDATE employee SET date_active='$date_orig' WHERE employeeid='{$row->employeeid}'");
                  // $this->db->query("CALL prc_employee_schedule_pershift('{$row->employeeid}','{$data['schedid']}','{$data['code']}','{$data['date_active']}','".$this->session->userdata("userid")."')");

                  // start prc_employee_schedule_pershift
                  $this->db->query("DELETE FROM employee_schedule WHERE employeeid='{$row->employeeid}'");
                  $this->db->query("INSERT INTO employee_schedule(employeeid, starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,editedby,dateedit, weekly_sched) (SELECT '{$row->employeeid}',starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,'".$this->session->userdata("userid")."','{$data['date_active']}', weekly_flexible FROM code_schedule_detail WHERE schedid='{$data['schedid']}')");

                  $this->db->query("INSERT INTO employee_schedule_history(employeeid, starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,changeby,dateactive, weekly_sched) (SELECT '{$row->employeeid}',starttime,endtime,`dayofweek`,idx,tardy_start,absent_start,tardy_half_start,absent_half_start,no_schedule,half_schedule,early_dismissal,leclab,flexible,hours,breaktime,`mode`,'".$this->session->userdata("userid")."','{$data['date_active']}', weekly_flexible FROM code_schedule_detail WHERE schedid='{$data['schedid']}')");

                  if($data['code'] != ''){
                    $this->db->query("UPDATE employee SET empshift='{$data['schedid']}' WHERE emptype='{$data['code']}'");
                  }
                  //end prc_employee_schedule_pershift
 
                  $current_count++;
                  if($current_count >= $total_count) $this->db->query("DELETE FROM code_type_progress WHERE emptype='{$data['code']}'");
                  else $this->db->query("UPDATE code_type_progress SET current_count = '$current_count' WHERE emptype= '{$data['code']}'");
                }
            }
        }
        return $return;
    }

  function getInfoTypeTable( $info_type ){
        $tblname = 'code_'.strtolower($info_type);
        if( $this->db->table_exists($tblname) ) return $tblname;
        else return false;
  }

  function getTablePK($tbl){
        $fields = $this->getTableFields($tbl);
        if( count($fields) > 0 ) return $fields[0];
        else return false;
  }

  function getTableFields($tbl) {
        $fields = $this->db->list_fields($tbl);
        if( count($fields) > 0 ) return $fields;
        else return false;
  }

  function getNewId($tbl, $pk){
        $maxId = $this->db->query("SELECT MAX($pk) FROM $tbl");
        return $maxId->result();
  }
  function saveDivision($managementid = ""){
        $msg = "";
        $query=$this->db->query("DELETE FROM code_managementlevel where managementid = '$managementid' ");
        if ($query) $msg = "Delete Success";
        else $msg = "Error";
        return $msg;
    }
  // function saveDivision($data = "", $action){
  //     if($action == "delete"){
  //       $validate_delete = $this->db->query("SELECT * FROM employee WHERE code_managementlevel = '$data' ");
  //       if($validate_delete->num_rows() > 0) return false;
  //       $this->db->query("DELETE FROM code_managementlevel WHERE managementid = '$data' ");
  //       return true;

  //     }
  //   }
  function deleteFromTable($info_type, $id){
        $tbl = $this->getInfoTypeTable($info_type);
        $pk = $this->getTablePK($tbl);

        $querycheck = $this->db->query("SELECT * FROM employee WHERE emptype='$id'");
        if($querycheck->num_rows() > 0){
            echo    "Failed to delete.. This schedule is already in use..";
        }else{
            $this->db->delete($tbl, array($pk => $id));
            echo "Batch Schedule has been deleted successfully";
        }
  }

  function loademployeleavestatus($category='',$dfrom='',$dto=''){
     $stat=$category;
     $cdate = "a.dateapplied BETWEEN '$dfrom' AND '$dto'";

     $returns = array();
     $this->db->_reset_select(); /** reset active select */
     /** select fields */
     $this->db->select("a.id,a.employeeid,a.leavetype,a.dateapplied,a.no_days,a.fromdate,a.todate,a.status,a.dateapproved,a.approvedby,a.remarks,b.lname,b.fname,b.mname");
     $this->db->from("leave_request a");
     $this->db->join("employee b","b.employeeid=a.employeeid");
     if(!empty($stat)){
     $this->db->where("a.status",$stat);
     }
     $this->db->where($cdate);
     $this->db->order_by("a.employeeid","asc");

     $q = $this->db->get();

     for($t=0;$t<$q->num_rows();$t++){
      $row = $q->row($t);
      array_push($returns,$row);
      }

     $reform = array();
     foreach($returns as $row){
     $tarrs = array(
                     "id"=>$row->id,
                     "employeeid"=>$row->employeeid,
                     "lname"=>$row->lname,
                     "fname"=>$row->fname,
                     "mname"=>$row->mname,
                     "leavetype"=>$row->leavetype,
                     "dateapplied"=>$row->dateapplied,
                     "no_days"=>$row->no_days,
                     "fromdate"=>$row->fromdate,
                     "todate"=>$row->todate,
                     "status"=>$row->status,
                     "dateapproved"=>$row->dateapproved,
                     "approvedby"=>$row->approvedby,
                     "remarks"=>$row->remarks
                   );
                   array_push($reform,$tarrs);
     }

    return $reform;
  }
  function GetBasicPreviousPay($id)
  {
    $ppay = '';
    $query = $this->db->query("SELECT monthly FROM payroll_employee_salary_history WHERE employeeid ='{$id}' ORDER BY TIMESTAMP DESC");
    foreach ($query->result() as $key) {
      $ppay = $key->monthly;
    }
    return $ppay;
  }
  function GetBasicCurrentPay($id)
  {
    $cpay = '';
    $query = $this->db->query("SELECT monthly FROM payroll_employee_salary WHERE employeeid ='{$id}' ORDER BY TIMESTAMP ASC");
    foreach ($query->result() as $key) {
      $cpay = $key->monthly;
    }
    return $cpay;
  }
    function EmpregularDate($id)
    {
      //GETTING THE DATE WHEN THE EMPLOYEE BECOME REGULAR
      $reg = '';
      $query = $this->db->query("SELECT a.`employeeid`,a.`employmentstat`,a.`deptid`,a.`dateposition` FROM employee AS a
      LEFT JOIN employee_employment_status_history  AS b ON (a.`employeeid` = b.`employeeid`) WHERE a.employeeid='{$id}' AND a.employmentstat='REG'");
      foreach ($query->result() as $key) {
        $reg = $key->dateposition;
      }
      return $reg;

    }

    function EmpHiredDate($id)
    {
      //GETTING THE OLDEST DATE HIRED OF EMPLOYEE
      $Hireddate = '';
      $query = $this->db->query("SELECT dateposition FROM employee_employment_status_history WHERE employeeid='{$id}' ORDER BY dateposition DESC");
      foreach ($query->result() as $key) {

        $Hireddate = $key->dateposition;
      }
      return $Hireddate;
    }
  function empleavelist($category="",$ltype="",$dfrom="",$dto="",$deptid="",$othtype=''){
    $wC = "";
    if($ltype)           $wC .= " AND b.`type`='$ltype'";
    if($category)        $wC .= " AND a.`status`='$category'";
    if($deptid)          $wC.= " AND c.deptid='$deptid'";

    $res = $this->db->query("SELECT a.id AS leaveid, a.*,b.* ,REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname
                  FROM leave_app_emplist a
              INNER JOIN leave_app_base b ON a.`base_id`=b.`id`
              INNER JOIN employee c ON a.employeeid=c.employeeid
              WHERE b.`date_applied` BETWEEN '$dfrom' AND '$dto' $wC");
    return $res;
  }
  # for ica-hyperion 21194
  # by justin (with e)
  # > idi-displayed yung mga history ng OB request na ina-apply ni Admin...
  function empObListForAdmin($category="",$dfrom="",$dto="",$deptid="",$othtype=""){
    # get muna yung username ni admin
    $user = $this->session->userdata("username");


    # set query
    $sql = "SELECT b.`id`, b.`employeeid`, CONCAT(c.`lname`, ', ', c.`fname`, ' ', c.`mname`) AS fullname, a.`type` AS ltype, a.`nodays`,
            a.`datefrom` AS dfrom, a.`dateto` AS dto, b.`status`, DATE_FORMAT(b.`timestamp`, '%Y-%m-%d') AS cdate, a.date_applied
            FROM ob_app a
            INNER JOIN ob_app_emplist b ON b.`base_id` = a.`id`
            INNER JOIN employee c ON c.`employeeid` = b.`employeeid`
            WHERE a.`applied_by` != '' ";

    # for other type filtering
    if($othtype == "DA") $sql .= " AND a.`type`='DIRECT'";
    else                 $sql .= " AND a.`type`='CORRECTION'";

    # if selected category
    if($category) $sql .= " AND b.`status`='". $category ."'";

    # if selected date from and to
    if($dfrom && $dto) $sql .= " AND (a.datefrom BETWEEN '". $dfrom ."' AND '". $dto ."') AND (a.dateto BETWEEN '". $dfrom ."' AND '". $dto ."') ";

    # order by employeeid
    $sql .=" ORDER BY cdate, b.`employeeid`;";

    # run the query
    $search_empList = $this->db->query($sql)->result();

    # set array for emplist
    $empOBList = array();

    # status column
    $status_col = array(
                          "status",
                          "dstatus",
                          "cstatus",
                          "hrstatus",
                          "cpstatus",
                          "fdstatus",
                          "bostatus",
                          "pstatus",
                          "upstatus"
                        );


    # get result
    if(count($search_empList) > 0){
      foreach ($search_empList as $se) {
        # para sa pwedeng i edit si request
        $isEdit = true;

        # hanapin kung pwede i-edit dito..
        $search_col = $this->db->query("SELECT * FROM ob_app_emplist WHERE id='{$se->id}'")->result();
        foreach ($search_col as $sc) {
          for($i = 0; $i < count($status_col); $i++){
            $col = $status_col[$i];
            if($sc->$col == "APPROVED" || $sc->$col == "DISAPPROVED" || $sc->$col == "CANCELLED"){
              $isEdit = false;
              break;
            }
          }
        }

        # push the data on array emplist
        array_push($empOBList, array(
                                          "id" => $se->id,
                                          "empID" =>  $se->employeeid,
                                          "fullname" =>  $se->fullname,
                                          "nodays" =>  $se->nodays,
                                          "dfrom" =>  $se->dfrom,
                                          "dto" =>  $se->dto,
                                          "status" =>  $se->status,
                                          "cdate" =>  $se->date_applied,
                                          "isEdit" => $isEdit
                                         ));
      }
    }

    return $empOBList;
  }
  # end for ica-hyperion 21194
  function empoblist($category="",$dfrom="",$dto="",$deptid=""){
    $wC = "";
    if($category)        $wC .= " AND a.`status`='$category'";
    if($deptid)          $wC.= " AND c.deptid='$deptid'";

    $res = $this->db->query("SELECT a.id AS leaveid, a.*,b.* ,REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname
                  FROM ob_app_emplist a
              INNER JOIN ob_app b ON a.`base_id`=b.`id`
              INNER JOIN employee c ON a.employeeid=c.employeeid
              WHERE b.`date_applied` BETWEEN '$dfrom' AND '$dto' $wC");
    return $res;
  }

  function empseminarlist($dfrom,$dto,$category="",$deptid=""){
    $wC = "";
    if($deptid) $wC = " AND c.deptid='$deptid'";
    if(in_array($category,array("PENDING","DISAPPROVED"))){
        $query = $this->db->query("SELECT a.*,a.dfrom as startdate, a.dto as enddate,CONCAT(c.lname,', ',c.fname,' ',c.mname) as fullname, g.timestamp as dateattached
                                    FROM seminar_app a
                                    LEFT JOIN employee c ON a.employeeid = c.employeeid
                                    LEFT JOIN seminar_app_attach g ON a.id = g.id
                                    WHERE a.status='$category' AND DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto' $wC");
    }else if(in_array($category,array("APPROVED"))){
        $query = $this->db->query("SELECT a.*,a.aid as id,a.dfrom as startdate, a.dto as enddate,CONCAT(c.lname,', ',c.fname,' ',c.mname) as fullname ,
                                        d.status as deptheadstatus, e.status as cheadstatus, f.status as hrdirstatus, g.timestamp as dateattached, d.dateapplied as timestamp
                                    FROM seminar_request a
                                    LEFT JOIN employee c ON a.employeeid = c.employeeid
                                    LEFT JOIN seminar_app_dhead d ON a.aid = d.aid
                                    LEFT JOIN seminar_app_chead e ON a.aid = e.aid
                                    LEFT JOIN seminar_app_hrd f ON a.aid = f.aid
                                    LEFT JOIN seminar_app_attach g ON a.aid = g.id
                                    WHERE a.status='$category' AND DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto' $wC");
    }else{
        $query = $this->db->query("SELECT a.*,a.dfrom as startdate, a.dto as enddate,CONCAT(c.lname,', ',c.fname,' ',c.mname) as fullname, g.timestamp as dateattached
                                    FROM seminar_app a
                                    LEFT JOIN seminar_request b ON a.employeeid = b.employeeid AND a.dfrom AND b.dfrom AND a.dto = b.dto
                                    LEFT JOIN employee c ON a.employeeid = c.employeeid
                                    LEFT JOIN seminar_app_attach g ON a.id = g.id
                                    WHERE DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto' $wC");
    }

    return $query;
  }

  function empovertimelist($dfrom,$dto,$category="",$deptid=""){
    $wC = "";
    if($deptid) $wC = " AND c.deptid='$deptid'";
    if(in_array($category,array("PENDING","DISAPPROVED"))){
        $query = $this->db->query("SELECT a.*,a.dfrom as startdate, a.dto as enddate,CONCAT(c.lname,', ',c.fname,' ',c.mname) as fullname
                                    FROM overtime_app a
                                    LEFT JOIN employee c ON a.employeeid = c.employeeid
                                    WHERE a.status='$category' AND DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto' $wC");
    }else if(in_array($category,array("APPROVED"))){
        $query = $this->db->query("SELECT a.*,a.aid as id,a.dfrom as startdate, a.dto as enddate,CONCAT(c.lname,', ',c.fname,' ',c.mname) as fullname, d.status as deptheadstatus, e.status as cheadstatus, f.status as hrdirstatus
                                    FROM overtime_request a
                                    LEFT JOIN employee c ON a.employeeid = c.employeeid
                                    LEFT JOIN overtime_app_dhead d ON a.aid = d.aid
                                    LEFT JOIN overtime_app_chead e ON a.aid = e.aid
                                    LEFT JOIN overtime_app_hrd f ON a.aid = f.aid
                                    WHERE a.status='$category' AND DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto' $wC");
    }else{
        $query = $this->db->query("SELECT a.*,a.dfrom as startdate, a.dto as enddate,CONCAT(c.lname,', ',c.fname,' ',c.mname) as fullname
                                    FROM overtime_app a
                                    LEFT JOIN overtime_request b ON a.employeeid = b.employeeid AND a.dfrom AND b.dfrom AND a.dto = b.dto
                                    LEFT JOIN employee c ON a.employeeid = c.employeeid
                                    WHERE DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto' $wC");
    }

    return $query;
  }

  function empovertimelist2($dfrom='',$dto='',$category="",$deptid=""){
        $wC = "";
    if($category)                $wC .= " AND a.`status`='$category'";
        if($deptid) $wC.= " AND c.deptid='$deptid'";

    $res = $this->db->query("SELECT a.id AS otid, a.*,b.* ,REPLACE(CONCAT(c.LName,', ',c.FName,' ',c.MName), 'Ã‘', 'Ñ') AS fullname
                                FROM ot_app_emplist a
                            INNER JOIN ot_app b ON a.`base_id`=b.`id`
                            INNER JOIN employee c ON a.employeeid=c.employeeid
                            WHERE ((b.`dfrom` BETWEEN '$dfrom' AND '$dto') AND (b.`dto` BETWEEN '$dfrom' AND '$dto')) $wC ");
    return $res;
  }

  /*
    <tr employeeid='<?=$row['employeeid']?>' style="cursor: pointer;">
        <td><?=$row['employeeid']?></td>
        <td><?=$row['fullname']?></td>
        <td><?=$row['type']?></td>
        <td><?=$row['timestamp']?></td>
        <td><?=$row['no_days']?></td>
        <td><?=$row['startdate']?></td>
        <td><?=$row['enddate']?></td>
        <td><?=$row['status']?></td>
        <td><?=$row['reason']?></td>
      </tr>
  */
  function loadallempid($id='',$all = false){
    $return = "";
    $return = "<option value=''>- Select Employee -</option>";
    if($all)    $return .= "<option value=''>- All Employees -</option>";

    $query = $this->db->query("SELECT employeeid, lname, fname, mname FROM employee");
    foreach($query->result() as $row){
        $empid = $row->employeeid;
        $lname = $row->lname;
        $fname = $row->fname;
        $mname = $row->mname;
        if($id == $empid)   $return .= "<option value='$empid' selected>$empid - $lname, $fname $mname</option>";
        else                $return .= "<option value='$empid'>$empid - $lname, $fname $mname</option>";
    }

    return $return;
  }

  function loadallofficeheadempid($id='',$all = false){
    $return = "";
    $return = "<option value=''>- Select an employee -</option>";
    if($all)    $return .= "<option value=''>- All Employees -</option>";
    $id = explode(',', $id);
    $query = $this->db->query("SELECT employeeid, lname, fname, mname FROM employee a INNER JOIN code_office b ON a.employeeid = b.head OR a.employeeid = b.divisionhead GROUP BY employeeid");
    foreach($query->result() as $row){
        $empid = $row->employeeid;
        $lname = $row->lname;
        $fname = $row->fname;
        $mname = $row->mname;
        if(in_array($empid, $id)) $return .= "<option value='$empid' selected>$empid - $lname, $fname $mname</option>";
        // if($id == $empid)   $return .= "<option value='$empid' selected>$empid - $lname, $fname $mname</option>";
        else                $return .= "<option value='$empid'>$empid - $lname, $fname $mname</option>";
    }

    return $return;
  }

  function loadApplicantApproverList($approver_id, $approver_list=''){
    $return = "";
    $return = "<option value=''>- Select Employee -</option>";
    if($approver_list){
      $query = $this->db->query("SELECT employeeid, lname, fname, mname FROM employee a INNER JOIN code_office b ON a.employeeid = b.head OR a.employeeid = b.divisionhead WHERE FIND_IN_SET(employeeid, '$approver_list') GROUP BY employeeid");
      foreach($query->result() as $row){
          $empid = $row->employeeid;
          $lname = $row->lname;
          $fname = $row->fname;
          $mname = $row->mname;
          if($approver_id == $empid)   $return .= "<option value='$empid' selected>$empid - $lname, $fname $mname</option>";
          else                $return .= "<option value='$empid'>$empid - $lname, $fname $mname</option>";
      }
    }else{
      if($approver_id) $return = $this->loadallofficeheadempid($approver_id);
      else $return = $this->loadallofficeheadempid();
      
    }
    return $return;
  }

  function loadallclusterhead($id='',$all = false){
    $return = "";
    $return = "<option value=''>- Select Employee -</option>";
    if($all)    $return .= "<option value=''>- All Employees -</option>";

    $query = $this->db->query("SELECT employeeid, lname, fname, mname FROM employee WHERE teaching <> ''");
    foreach($query->result() as $row){
        $empid = $row->employeeid;
        $lname = $row->lname;
        $fname = $row->fname;
        $mname = $row->mname;
        if($id == $empid)   $return .= "<option value='$empid' selected>$empid - $lname, $fname $mname</option>";
        else                $return .= "<option value='$empid'>$empid - $lname, $fname $mname</option>";
    }

    return $return;
  }

  function loadallabsent($dfrom = ""){
    $query = $this->db->query("SELECT employeeid, CONCAT(lname,', ',fname,' ',mname) AS fullname FROM employee a
                                WHERE employeeid NOT IN (SELECT employeeid FROM leave_request WHERE dateapplied = '$dfrom')
                                AND   employeeid NOT IN (SELECT userid FROM timesheet WHERE DATE(timein) = '$dfrom' GROUP BY userid)
                                AND   (dateresigned='1970-01-01' OR dateresigned IS NULL)
                              ");
    return $query->result_array();
  }
  function absenttoleave($data = ""){
    $return = "";
    $eid = $data["eid"];
    $cdate = $data["dfrom"];
    $user = $this->session->userdata('username');
    $dtoday = date("Y-m-d");

    $query = $this->db->query("SELECT * FROM leave_request WHERE employeeid='$eid' AND fromdate='$cdate' AND todate='$cdate' AND status='APPROVED'");
    if($query->num_rows() == 0){

        $qetype = $this->db->query("SELECT leavetype FROM employee WHERE employeeid='$eid'");
        $eltype = $qetype->row(0)->leavetype;
        if(!empty($eltype)){
            $qcode = $this->db->query("SELECT code_request FROM code_request_form WHERE '$dtoday' BETWEEN startdate AND enddate AND leavetype='$eltype'");
            $ltype = $qcode->row(0)->code_request;

            $query = $this->db->query("INSERT INTO leave_request (employeeid,leavetype,dateapplied,no_days,fromdate,todate,status,dateapproved,approvedby,remarks)
                                        VALUES ('$eid','$ltype','$cdate','1','$cdate','$cdate','APPROVED','$cdate','$user',CURRENT_TIMESTAMP)");
            $return = "Successfully Saved!.";
        }else   $return = "Please Set Leave Type first for this employee..";
    }else{
        $return = "Failed to Saved.. We encountered some problem.. It's either, this employee have already filed leave on the date you set or your connection to the server is lost..";
    }
    return $return;
  }
  function listByDepartment($department=''){
    $where = ($department != "") ? " WHERE deptid = '{$department}' " : "";
    $sql = "SELECT
            C.employeeid,
            C.fullname,
            C.description as typedesc,
            D.description as statdesc
            FROM
            (SELECT
              A.employeeid,
              CONCAT(A.lname, ', ', A.fname, ' ', A.mname) AS fullname,
              A.emptype,
              A.employmentstat,
              A.deptid,
              B.description
            FROM (SELECT * FROM employee ".$where." ORDER BY lname ASC, fname ASC, mname ASC) AS A
            LEFT JOIN code_type AS B ON A.emptype = B.code) AS C
            LEFT JOIN code_status AS D
            ON C.employmentstat = D.code";
    return $this->db->query($sql)->result_array();
  } // end function listByDepartment

  function getindividualemployee($employeeid = ''){
    $sql = "SELECT employeeid, CONCAT(lname,', ',fname,' ',mname) as fullname, isFlexi FROM employee WHERE employeeid='$employeeid';";
    return $this->db->query($sql)->result();
  }

  function getempteachingtype($user = ""){
    $return = false;
    $query = $this->db->query("SELECT teachingtype FROM employee WHERE employeeid='$user'");
    if($query->num_rows() > 0)  $return = ($query->row(0)->teachingtype == "teaching" ? true : false);
    return $return;
  }

  function getDeptHead($employeeid = "",$include = false){
    $arr = array();
    $sql = $this->db->query("SELECT b.head,b.divisionhead FROM employee a LEFT JOIN code_office b ON (a.deptid = b.code OR a.employeeid = b.divisionhead OR a.employeeid = b.head) WHERE (b.divisionhead='$employeeid' OR b.head = '$employeeid') ORDER BY divisionhead DESC LIMIT 1");
    $arr = ($sql->num_rows() > 0 ? array($sql->row(0)->head,$sql->row(0)->divisionhead) : array());
    $sql = $this->db->query("SELECT * FROM code_request_form WHERE (univphy='$employeeid' OR univphyt='$employeeid');");
    if($sql->num_rows() > 0){   $arr = array($sql->row(0)->univphy,$sql->row(0)->univphyt); }

    if($include){
        $sql = $this->db->query("SELECT * FROM code_request_form");
        if($sql->num_rows() > 0){
          for ($r=0; $r < $sql->num_rows(); $r++) {
            if($sql->row($r)->budgetoff == $employeeid){
               array_push($arr,$sql->row($r)->budgetoff);
               break;
            }
            if($sql->row($r)->financedir == $employeeid){
              array_push($arr,$sql->row($r)->financedir);
              break;
            }
            if($sql->row($r)->president == $employeeid){
               array_push($arr,$sql->row($r)->president);
               break;
            }
          }
        }
    }
    // echo "<pre>"; print_r($arr);
    return $arr;
  }

  function getDeptHeadOnly($employeeid = "",$include = false){
    $arr = "";
    $sql = $this->db->query("SELECT b.head FROM employee a LEFT JOIN code_office b ON (a.deptid = b.code OR a.employeeid = b.head) WHERE a.employeeid='$employeeid' ORDER BY divisionhead DESC LIMIT 1");
    $arr = ($sql->num_rows() > 0 ? array($sql->row(0)->head) : "");
    $sql = $this->db->query("SELECT * FROM code_request_form WHERE (univphy='$employeeid' OR univphyt='$employeeid');");
    if($sql->num_rows() > 0){   $arr = array($sql->row(0)->univphy,$sql->row(0)->univphyt); }

    if($include){
        $sql = $this->db->query("SELECT * FROM code_request_form");
        if($sql->num_rows() > 0){
          for ($r=0; $r < $sql->num_rows(); $r++) {
            if($sql->row($r)->budgetoff == $employeeid){
               array_push($arr,$sql->row($r)->budgetoff);
               break;
            }
            if($sql->row($r)->financedir == $employeeid){
              array_push($arr,$sql->row($r)->financedir);
              break;
            }
            if($sql->row($r)->president == $employeeid){
               array_push($arr,$sql->row($r)->president);
               break;
            }
          }
        }
    }

    return $arr;
  }

  function checkIfOfficeHead($employeeid){
    return $this->db->query("SELECT * FROM code_office where divisionhead = '$employeeid'")->num_rows();
  }

  function checkifDefDepartmentHead($employeeid){
    return $this->db->query("SELECT * FROM code_office a INNER JOIN department_deficiency b ON a.code = b.deptid where a.divisionhead = '$employeeid' OR a.head = '$employeeid'")->num_rows();
  }

  function getDeptCode($employeeid){
    $return = false;
    $sql = $this->db->query("SELECT b.head,b.divisionhead FROM employee a LEFT JOIN code_office b ON (a.deptid = b.code OR a.employeeid = b.divisionhead) WHERE a.employeeid='$employeeid'");
    $dhead = ($sql->num_rows() > 0 ? $sql->row(0)->head : "");
    $chead = ($sql->num_rows() > 0 ? $sql->row(0)->divisionhead : "");
    if(in_array($employeeid,array($dhead,$chead)))  $return = true;
    return $return;
  }

  function getClusterHead($employeeid){
    $return = false;
    $sql = $this->db->query("SELECT b.head,b.divisionhead FROM employee a LEFT JOIN code_office b ON (a.employeeid = b.divisionhead) WHERE a.employeeid='$employeeid'");
    $chead = ($sql->num_rows() > 0 ? $sql->row(0)->divisionhead : "");
    if($employeeid == $chead)  $return = true;
    return $return;
  }

  function getUnivPhysician($employeeid){
    $return = false;
    $sql = $this->db->query("SELECT * FROM code_request_form WHERE (univphy='$employeeid' OR univphyt='$employeeid')");
    $return = ($sql->num_rows() > 0 ? true : false);
    return $return;
  }

  function getBudgetFinPres($employeeid){
    $return = "";
    $sql = $this->db->query("SELECT * FROM code_request_form WHERE (budgetoff='$employeeid' OR financedir='$employeeid' OR president='$employeeid') AND code_request != 'OBS'");
    if($sql->num_rows()){
        if($sql->row()->budgetoff   == $employeeid)  $return = " leave_app_budgetoff";
        if($sql->row()->financedir  == $employeeid)  $return = " leave_app_financedir";
        if($sql->row()->president   == $employeeid)  $return = " leave_app_president";
    }
    return $return;
  }

  //added 4-28-2017
  function getBudgetOff($employeeid){
    $return = "";
    $sql = $this->db->query("SELECT * FROM code_request_form WHERE budgetoff='$employeeid' AND code_request = 'OBS'");
    if($sql->num_rows() > 0){
        $return = "passed";
    }
    else
    {
        $return = "failed";
    }
    return $return;
  }

  function getDHRCHead($employeeid,$col="",$ishr=false){
    $return = false;
    $wC = "";

    if($col == "divisionhead"){
        $query = $this->db->query("SELECT DISTINCT b.$col FROM employee a
                                    LEFT JOIN code_office b ON a.deptid = b.code
                                    WHERE b.divisionhead='$employeeid' LIMIT 1");
    }else{
        if($ishr)   $wC = " AND code='HR'";
        // else        $wC = " AND code <> 'HR'";
        $query = $this->db->query("SELECT b.$col FROM employee a
                                    LEFT JOIN code_office b ON a.deptid = b.code
                                    WHERE a.employeeid='$employeeid' $wC");
    }
    if($query->num_rows() > 0)  $return = $query->row(0)->$col;

    return $return;
  }

  function OffBusinessBudgetFinPres($col){
    $return = "";
    $query = $this->db->query("SELECT $col FROM code_request_form WHERE ismain=3");
    if($query->num_rows() > 0)  $return = $query->row(0)->$col;
    return $return;
  }

  function getHeadDeptCode($head=""){
    $query = $this->db->query("SELECT GROUP_CONCAT(CODE SEPARATOR ',') AS deptid FROM code_office WHERE head='$head' OR divisionhead='$head'");
    if($query->num_rows() > 0)  return $query->row(0)->deptid;
    else                        return "";
  }

  function getfullname($employeeid = ''){
    $return = "";
    $id = explode(',',$employeeid);
    foreach($id as $eid){
        $sql = "SELECT CONCAT(lname,', ',fname,' ',mname) as fullname FROM employee WHERE employeeid='$eid';";
        foreach($this->db->query($sql)->result() as $row){
            if($return) $return .= "<br />";
            $return .= $row->fullname;
        }
    }
    return $return;
  }

  function getfullnameunivphy($id = ""){
    $return = "";
    $sql = $this->db->query("SELECT approvedby FROM employee a INNER JOIN leave_app_uphy b ON a.employeeid = b.employeeid WHERE b.aid='$id'");
    if($sql->num_rows() > 0){
        $sql = $this->db->query("SELECT CONCAT(lname,', ',fname,' ',mname) as fullname FROM employee WHERE employeeid='".$sql->row(0)->approvedby."';");
        $return = $sql->num_rows() > 0 ? $sql->row(0)->fullname : "";
    }
    return $return;
  }

  function getfullnameuser($user = ""){
    $return = "";
    $sql = "SELECT CONCAT(lastname,', ',firstname,' ',middlename) as fullname FROM user_info WHERE id='$user';";
    foreach($this->db->query($sql)->result() as $row){
        $return = $row->fullname;
    }
    return $return;
  }

  function getempdatacol($col="",$eid=""){
    $return = '';
    $user =  $this->session->userdata("username");
    if($eid) $user = $eid;
    $sql = "SELECT $col FROM employee WHERE employeeid='$user';";
    foreach($this->db->query($sql)->result() as $row){
        $return = $row->$col;
    }
    return $return;
  }

  function getempforsched($id = ''){
    $sqldemp = "SELECT employeeid FROM employee_schedule_adjustment WHERE id='$id'";
    return $this->db->query($sqldemp)->result();
  }

  function eseries(){
    $return = "<option value=''>- Series -</option>";
    $sql = $this->db->query("SELECT SUBSTR(employeeid,1,2) AS eid FROM employee WHERE SUBSTR(employeeid,1,2)*1 <> 0 GROUP BY eid");
    foreach($sql->result() as $row){
        $return .= "<option value='".$row->eid."'>".$row->eid."</option>";
    }
    return $return;
  }

  function getEmail($eid = ""){
    $return = "";
    $query = $this->db->query("SELECT email FROM employee WHERE employeeid='$eid'");
    $email = trim($query->row(0)->email);
    return $email;
  }

  function sendMessageList($access){
    $return = "";
    $query = $this->db->query("SELECT * FROM user_info WHERE id='{$access['uid']}'");
    if($query->num_rows() > 0){
        $sql = $this->db->query("UPDATE user_info SET msgaccess='{$access['val']}' WHERE id='{$access['uid']}'");
        if($sql)    $return = "Successfully Saved!.";
    }
    return $return;
  }

  function proconfig($data){
    $msg = "";
    $query = $this->db->query("SELECT * FROM employee_restriction WHERE employeeid='{$data['eid']}' AND datefrom='{$data['dfrom']}' AND dateto='{$data['dto']}'");
    if($query->num_rows() > 0){
        $query = $this->db->query("DELETE FROM employee_restriction WHERE employeeid='{$data['eid']}' AND datefrom='{$data['dfrom']}' AND dateto='{$data['dto']}'");
        if($query) $msg = "Successfully Deleted!";
    }
    return $msg;
  }

  // function earlydismissal($data,$type = false){
  //   if($type){
  //       $query = $this->db->query("SELECT * FROM earlydismissal ORDER BY id DESC");
  //       return $query;
  //   }else{
  //       $startdate  = date("Y-m-d",strtotime($data['datesetfrom']));
  //       $minutes    = $data['minutes'];
  //       $dept       = $data['deptid'] ? $data['deptid'] : "ALL";
  //       $query = $this->db->query("INSERT INTO earlydismissal (department,minutes,datestart) VALUES ('$dept','$minutes','$startdate')");
  //       if($query)  $msg = "Successfully Saved!.";
  //       else        $msg = "Failed to Save!. Please check your connection..";
  //       return $msg;
  //   }
  // }
  function earlydismissal($data,$type = false){

    if($type){
        $query = $this->db->query("SELECT * FROM earlydismissal WHERE year <> 'NT' ORDER BY id DESC");
        return $query;
    }
    else{
        $stime=$etime=$total=$day=$id=$b=$ttardy=$tabsent=$tearly=$r=$tstart=$tend = $sequence= $tardyset=$comptardy=$absentset=$compabsent=$earlyset=$compearly="";
        $from    = $data['from'];
        $to     = $data['to'];
        $tardy = $data['tardy_e'];
        $absent = $data['absent_e'];
        $early = $data['early_d'];
        $year = $data['year'];
        $sequence = $data['sequence'];

        $tardy = $tardy * 60;
        $tardy = $this->attcompute->sec_to_hm($tardy);
        $absent = $absent * 60;
        $absent = $this->attcompute->sec_to_hm($absent);
        $early = $early * 60;
        $early = $this->attcompute->sec_to_hm($early);
        // $dept       = $data['deptid'] ? $data['deptid'] : "ALL";


        $query = $this->db->query("SELECT * FROM earlydismissal WHERE rangefrom='{$from}' AND rangeto='{$to}' AND  year='{$year}'");
                if ($query->num_rows()>0) {
                    $msg = "THIS SET-UP IS ALREADY EXIST!";
                }
                else
                {
                    $query = $this->db->query("INSERT INTO earlydismissal (rangefrom,rangeto,tardy,absent,early,year,sequence) VALUES ('$from','$to','$tardy','$absent','$early','$year','$sequence')");
                    if($query)
                      {
                          //EMPLOYEE SCHEDULE
                            $query = $this->db->query("SELECT dayofweek,employeeid,starttime,endtime FROM employee_schedule WHERE DATE_FORMAT(dateactive,'%Y')= '{$year}'  AND (leclab='LEC' OR leclab= 'LAB')");
                            if ($query->num_rows()>0) {
                              // print_r($query->result());
                             foreach ($query->result()  as $key => $row) {

                                       $stime = $row->starttime;
                                       $etime = $row->endtime;
                                       $day   = $row->dayofweek;
                                       $tstart = date('H:i:s',strtotime($row->starttime));
                                       $tend  = date('H:i:s',strtotime($row->endtime));
                                       $id = $row->employeeid;
                                       $total = (abs(strtotime($row->starttime) - strtotime($row->endtime))/ 3600)*60;



                                       $tardyset = date('H:i:s',strtotime($tardy));
                                       $comptardy = strtotime($tardyset) - strtotime("00:00:00");
                                       $ttardy = date("H:i:s A",strtotime($tstart)+$comptardy);



                                       $absentset = date('H:i:s',strtotime($absent));
                                       $compabsent = strtotime($absentset) - strtotime("00:00:00");
                                       $tabsent = date("H:i:s A",strtotime($tstart) + $compabsent);



                                       $earlyset = date('H:i:s',strtotime($early));
                                       $compearly = strtotime($earlyset) - strtotime("00:00:00");
                                       $tearly = date("H:i:s A",strtotime($tend) - $compearly);

                                       // echo 'START TIME '.$row->starttime.'<br> END TIME '.$row->endtime. '<br> TARDY '. $ttardy. '<br> ABSENT '.$tabsent .' EARLY '. $tabsent;

                                       if($total >= $from && $total <= $to)
                                        {
                                          $query = $this->db->query("UPDATE employee_schedule SET tardy_start='{$ttardy}',absent_start='{$tabsent}',early_dismissal='{$tearly}' WHERE dayofweek='{$day}' AND employeeid='$id' AND starttime='{$tstart}' AND endtime='{$tend}'  ");

                                        }

                                     }
                            }
                            //EMPLOYE SCHEDULE HISTORY
                            $query = $this->db->query("SELECT dayofweek,employeeid,starttime,endtime FROM employee_schedule_history WHERE DATE_FORMAT(dateactive,'%Y')= '{$year}'  AND (leclab='LEC' OR leclab= 'LAB')");
                            if ($query->num_rows()>0) {
                              // print_r($query->result());
                             foreach ($query->result()  as $key => $row) {

                                       $stime = $row->starttime;
                                       $etime = $row->endtime;
                                       $day   = $row->dayofweek;
                                       $tstart = date('H:i:s',strtotime($row->starttime));
                                       $tend  = date('H:i:s',strtotime($row->endtime));
                                       $id = $row->employeeid;
                                       $total = (abs(strtotime($row->starttime) - strtotime($row->endtime))/ 3600)*60;



                                       $tardyset = date('H:i:s',strtotime($tardy));
                                       $comptardy = strtotime($tardyset) - strtotime("00:00:00");
                                       $ttardy = date("H:i:s A",strtotime($tstart)+$comptardy);



                                       $absentset = date('H:i:s',strtotime($absent));
                                       $compabsent = strtotime($absentset) - strtotime("00:00:00");
                                       $tabsent = date("H:i:s A",strtotime($tstart) + $compabsent);



                                       $earlyset = date('H:i:s',strtotime($early));
                                       $compearly = strtotime($earlyset) - strtotime("00:00:00");
                                       $tearly = date("H:i:s A",strtotime($tend) - $compearly);

                                       // echo 'START TIME '.$row->starttime.'<br> END TIME '.$row->endtime. '<br> TARDY '. $ttardy. '<br> ABSENT '.$tabsent .' EARLY '. $tabsent;

                                       if($total >= $from && $total <= $to)
                                        {
                                          $query = $this->db->query("UPDATE employee_schedule_history SET tardy_start='{$ttardy}',absent_start='{$tabsent}',early_dismissal='{$tearly}' WHERE dayofweek='{$day}' AND employeeid='$id' AND starttime='{$tstart}' AND endtime='{$tend}'  ");
                                        }

                                     }
                            }

                      $msg = "Successfully Saved!.";
                      }
                    else
                      $msg = "Failed to Save!. Please check your connection..";
        }
        return  $msg;
        // date('h:i:s',strtotime($ttardy)).'<br>'.$stime.'<br>'.date('h:i:s',strtotime($tardy)).'<br>'.$t;
    }
  }

  function getindividualdept($eid = ""){
    $query = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$eid' ");
    return $query->row(0)->deptid;
  }

  function getindividualoffice($eid = ""){
    $query = $this->db->query("SELECT office FROM employee WHERE employeeid='$eid' ");
    if($query->num_rows() > 0) return $query->row(0)->office;
    else return false;
  }

  function getindividualed($dept = "",$dstart = ""){
    $query = $this->db->query("SELECT minutes FROM earlydismissal WHERE FIND_IN_SET(department,'$dept,ALL') AND  '$dstart' >= datestart ORDER BY id DESC LIMIT 1");
    if($query->num_rows() > 0)  $time = $query->row(0)->minutes;
    else                        $time = 0;
    $minutes = ($time ? $time : 75);
    return $minutes;
  }

  function new_leave($data){
    $return = "";
    $reason = $this->extras->clean($data['reason']);
    $user   = $data['eid'];
    $qdept  = $this->db->query("SELECT deptid FROM employee WHERE employeeid='$user'");
    $deptid = ($qdept->num_rows() > 0 ? $qdept->row(0)->deptid : "");
    if($deptid){
        $qdhead = $this->db->query("SELECT head FROM code_office WHERE code='$deptid'");
        $dhead  = ($qdhead->num_rows() > 0 ? $qdhead->row(0)->head : "");                         // department head
        $qhrd   = $this->db->query("SELECT head FROM code_office WHERE code='HR'");
        $hrd    = ($qhrd->num_rows() > 0 ? $qhrd->row(0)->head : "");                         // department head
        if($data['ltype'] == "VL"){
            $ins   = $this->db->query("INSERT INTO leave_app (employeeid,type,other,paid,datefrom,dateto,nodays,reason,depthead,hrdir) VALUES ('{$user}','{$data['ltype']}','{$data['othleave']}','{$data['withpay']}','{$data['datesetfrom']}','{$data['datesetto']}','{$data['ndays']}','{$reason}','{$dhead}','{$hrd}')");
            if($ins){
                $qid   = $this->db->query("SELECT id FROM leave_app WHERE employeeid='$user' AND type='{$data['ltype']}' AND datefrom='{$data['datesetfrom']}' AND dateto='{$data['datesetto']}' AND nodays='{$data['ndays']}' AND depthead='{$dhead}' AND hrdir='{$hrd}' AND reason='{$reason}'");
                $aid   = ($qid->num_rows() > 0 ? $qid->row(0)->id : 0);

                $query = $this->db->query("INSERT INTO leave_app_dhead (aid,employeeid,head,type,other,paid,datefrom,dateto,nodays,reason,status) VALUES ('$aid','{$user}','{$dhead}','{$data['ltype']}','{$data['othleave']}','{$data['withpay']}','{$data['datesetfrom']}','{$data['datesetto']}','{$data['ndays']}','$reason','PENDING')");
                if($query)  $return = "Application Sent!.";
                else        $return = "Ooops, Failed!";
            }
        }
    }else   $return = "Failed to apply leave. Please set your department first.";
    return $return;
  }

  function saveSeminarHRDirect($data){
    $bid = "";
    $return = array("err_code"=>2,"msg"=>"Ooops, Failed!","base_id"=>"","count"=>0);

    $tfrom  = date("H:i:s",strtotime($data['tfrom']));
    $tto    = date("H:i:s",strtotime($data['tto']));
    $poa    = $this->extras->clean($data['poa']);
    $course = $this->extras->clean($data['course']);
    $venue  = $this->extras->clean($data['venue']);
    $speaker= $this->extras->clean($data['speaker']);
    $misc   = $this->extras->clean($data['miscellaneous']);
    $soc    = $this->extras->clean($data['soc']);

    $qbid   = $this->db->query("SELECT baseid FROM based_id_offbus");
    $bid    = $qbid->row(0)->baseid;
              $this->db->query("UPDATE based_id_offbus SET baseid='".($bid+1)."'");

    $count = 0;
    foreach ($data['arr_emplist'] as $empid) {
      $ins   = $this->db->query("
          INSERT INTO seminar_app (employeeid,base_id,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,coursefee_approved,meal,meal_approved,transportation,transportation_approved,hotel,hotel_approved,othermiscellaneous,othermiscellaneous_approved,totalcost,totalcost_approved,venue,speaker,miscellaneous,statement, status, hrdir, hrdirdate, hrdirstatus, hhseq, isread)
          VALUES ('{$empid}','{$bid}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['cfeeApproved']}','{$data['meal']}','{$data['mealApproved']}','{$data['transpo']}','{$data['transpoApproved']}','{$data['hotel']}','{$data['hotelApproved']}','{$data['othermiscellaneous']}','{$data['othermiscellaneousApproved']}','{$data['tc']}','{$data['tcApproved']}','{$venue}','{$speaker}','{$misc}','{$soc}', 'APPROVED', '{$data['hrhead']}', CURRENT_DATE, 'APPROVED', '1', 0)");

      if($ins) $aid = $this->db->insert_id();

      if($aid) $ins = $this->db->query("
        INSERT INTO seminar_request (employeeid,base_id,aid,purpose,course,dfrom,dto,tstart,tend,nodays,paiddays,coursefee,coursefee_approved,meal,meal_approved,transportation,transportation_approved,hotel,hotel_approved,othermiscellaneous,othermiscellaneous_approved,totalcost,totalcost_approved,venue,speaker,miscellaneous,statement, status)
          VALUES ('{$empid}','{$bid}','{$aid}','{$poa}','{$course}','{$data['datesetfrom']}','{$data['datesetto']}','{$tfrom}','{$tto}','{$data['ndays']}','{$data['pwd']}','{$data['cfee']}','{$data['cfeeApproved']}','{$data['meal']}','{$data['mealApproved']}','{$data['transpo']}','{$data['transpoApproved']}','{$data['hotel']}','{$data['hotelApproved']}','{$data['othermiscellaneous']}','{$data['othermiscellaneousApproved']}','{$data['tc']}','{$data['tcApproved']}','{$venue}','{$speaker}','{$misc}','{$soc}', 'APPROVED')");
       if($ins) $ins = $this->db->query("INSERT INTO timesheet (userid,timein,timeout,otype) (SELECT employeeid,CONCAT(dfrom,' ',tstart),CONCAT(dto,' ',tend),'SEMINAR' FROM seminar_request WHERE aid='$aid')");

      if($ins) $count++;
    }

    if($count>0){
      $return = array("err_code"=>1,"msg"=>"Success","base_id"=>$bid,"count"=>$count);
    }
    return $return;
  }

  #added functions @author Angelica

  function getEmploymentStatusHistory($employeeid = "",$last_inserted="", $estatid=""){
      $wC = $limit = "";
      if($estatid) $wC .= "AND id='$estatid'";
      if($last_inserted) $limit .= "LIMIT 1";
      $res = $this->db->query("SELECT a.*, b.`description` AS mgmtdesc, c.`description` AS deptdesc, f.`description` AS officedesc, d.`description` AS statdesc, e.`description` AS posdesc
                            FROM employee_employment_status_history a
                            LEFT JOIN code_managementlevel b ON a.`managementid`=b.`managementid`
                            LEFT JOIN code_department c ON a.`deptid`=c.`code`
                            LEFT JOIN code_office f ON a.`office`=f.`code`
                            LEFT JOIN code_status d ON a.`employeestat`=d.`code`
                            LEFT JOIN code_position e ON a.`positionid`=e.`positionid`
                            WHERE employeeid='$employeeid'
                            $wC
                            ORDER BY `timestamp` DESC $limit")->result();
      return $res;
  }

  function getCurrentEmpStatusData($employeeid = ""){
      $deptid = $office = $employementstatus = $position = $dateposition = $management = $dateresigned2 = $resigned_reason = '';
      $query = $this->db->query("SELECT * FROM employee where employeeid = '$employeeid'");
      if($query->num_rows() > 0){
        $deptid = $query->row()->deptid;
        $office = $query->row()->office;
        $employementstatus = $query->row()->employmentstat;
        $position = $query->row()->positionid;
        $dateposition = $query->row()->dateposition;
        $resigned_reason = $query->row()->resigned_reason;
        $management = $query->row()->managementid;
        $dateresigned2 = $query->row()->dateresigned2;
        $datepos = (!empty($dateposition) && $dateposition != "0000-00-00" && $dateposition != "1970-01-01") ? date("Y-m-d",strtotime($dateposition)) : "";
      }

      return array($deptid, $office, $employementstatus, $position, $dateposition, $management, $dateresigned2, $resigned_reason);
  }

  function saveEmploymentStatusChanges($employeeid="",$deptid="",$office="",$employmentstat="",$positionid="",$dateposition="",$dateresigned="",$resignedreason=""){
      $res = "";

      $present = $this->getPresentEStatus($employeeid);
      //update history
      if($present->num_rows > 0){
        $present = $present->row(0);
        $res = $this->db->query("
                                INSERT INTO employee_employment_status_history (employeeid,deptid,office,employeestat,positionid,dateposition,dateresigned, reason)
                                 VALUES ('$employeeid','{$present->deptid}','{$present->office}','{$present->employmentstat}','{$present->positionid}','{$present->dateposition}','{$dateresigned}', '{$resignedreason}')");
      }


      //update employee data
      if($res){
         $res = $this->db->query("UPDATE employee SET
                                                   deptid='$deptid',
                                                   office='$office',
                                                   employmentstat='$employmentstat',
                                                   positionid='$positionid',
                                                   dateposition='$dateposition',
                                                   dateresigned ='$dateresigned',
                                                   dateresigned2 ='$dateresigned',
                                                   resigned_reason ='$resignedreason'
                                              WHERE employeeid='$employeeid'");
      }

      return $res;

  }

  function deleteEmploymentStatusHistory($estatid=""){
    $res = $this->db->query("DELETE FROM employee_employment_status_history WHERE id='$estatid'");
    return $res;
  }

  function deleteData($tbl_id, $table){
    $res = $this->db->query("DELETE FROM $table WHERE id='$tbl_id'");
    return $res;
  }

  function deleteDatachildren($tbl_id, $table){
    $res = $this->db->query("DELETE FROM applicant_children WHERE id='$tbl_id'");
    return $res;
  }

 function delete_education($tbl_id, $table){
    $res = $this->db->query("DELETE FROM $table WHERE id='$tbl_id'");
    return $res;
  }
  function getPresentEStatus($employeeid=""){
    $res = $this->db->query("SELECT deptid, office, employmentstat, positionid, dateposition,dateresigned2 FROM employee WHERE employeeid='$employeeid'");
    return $res;
  }

  function savePosition($action,$title,$hiring,$experience,$till,$id,$isteaching,$type=1,$course,$subject,$filename,$file,$documentFilename,$document,$desc) {

      $sql = $this->db->query("SELECT positionid FROM code_position WHERE description = '".$title."' AND positionid != '".$id."'");
      if($sql->num_rows() == 0)
      {
          if($action == "add")
          {
              $query = $this->db->query("INSERT INTO code_position (description,hiring,type,hiringtill,experience,isteaching,course,subject,comment) VALUES('".$title."','".$hiring."','$type','$till','$experience','$isteaching','$course','$subject','$desc')");
              // echo "<pre>";print_r($this->db->last_query());die;
              $this->updateHiringPosition($this->db->insert_id(), $hiring, $till, $course, $subject, $filename, $file, $documentFilename, $document);
          }
          else
          {
              $query = $this->db->query("UPDATE code_position SET description='".$title."' ,hiring='".$hiring."' , type='$type', hiringtill='$till', experience='$experience', isteaching='$isteaching', course='$course', subject='$subject', `comment`='$desc' WHERE positionid = '".$id."'");
              
              $this->updateHiringPosition($id, $hiring, $till, $course, $subject, $filename, $file, $documentFilename, $document);
          }
          if($query) return array("err_code"=>"0","msg"=>"SUCCESS!","last_id"=>$this->db->insert_id());
          else return array("err_code"=>"1","msg"=>"FAILED TO SAVE!");
      }
      else {
          return array("err_code"=>"2","msg"=>"RECORD ALREADY EXIST!");
      }
  }

  function updateHiringPosition($id, $hiring, $hiringtill, $course, $subject, $filename = "", $file = "", $documentFilename = "", $document = ""){
  
    $sql = $this->db->query("SELECT base_id FROM code_position_hiring WHERE base_id = '$id'");
      if($sql->num_rows() == 0){
        return $this->db->query("INSERT INTO code_position_hiring (base_id, hiring, hiringtill, course, subject,filename,file,documentFilename,document) VALUES ('$id', '$hiring', '$hiringtill', '$course', '$subject', '$filename', '$file', '$documentFilename', '$document')");
      }else{
        $filesUpload = $documentsUpload = "";
        if ($filename) {
          $filesUpload = ",filename='$filename', file='$file'";
        }
        if ($documentFilename) {
          $documentsUpload = ",documentFilename='$documentFilename', document='$document'";
        } 
        return $this->db->query("UPDATE code_position_hiring SET hiring='$hiring', hiringtill='$hiringtill', course='$course', subject='$subject' $filesUpload $documentsUpload WHERE base_id = '$id'");
      }

    
  }

  function savePositionDesc($data,$id) {
      $this->db->query("DELETE FROM code_position_description WHERE positionid='{$id}'");
      if($data != "")
      {
          $d = explode("<=>",$data);
          if(count($d) != 0)
          {
              foreach($d as $key => $value)
              {
                  $this->db->query("INSERT INTO code_position_description(positionid,description) VALUES('".$id."','".$value."')");
              }
          }
      }
  }

  function savePhoto($employeeid,$filename,$file){
    $hasPhoto = $this->db->query("SELECT * FROM employee_photo where employeeid = '$employeeid'")->result();
    if(count($hasPhoto) > 0){
      $updatePhoto = $this->db->query("UPDATE employee_photo set file = '$file', filename ='$filename' where employeeid = '$employeeid'");
      if($updatePhoto) $return = array("err_code" => "1", "msg" => "Successfully Updated Photo!");
      else $return = array("err_code" => "2", "msg" => "Failed to update photo..");
    }
    else{
      $newPhoto = $this->db->query("INSERT INTO employee_photo(employeeid, file, filename) VALUES ('$employeeid', '$file', '$filename')");
      if($newPhoto) $return = array("err_code" => "3", "msg" => "Successfully Added Photo!");
      else $return = array("err_code" => "4", "msg" => "Failed to add photo..");
    }

    return $return;
  }

  function savePhoto_elfinder($filename,$filetype,$file){
    $hasPhoto = $this->db->query("SELECT * FROM elfinder_file a WHERE a.name = '$filename'")->result();
    if(count($hasPhoto) > 0){
      $updatePhoto = $this->db->query("UPDATE elfinder_file set content = '$file', mime ='$filetype' where name = '$filename'");
      if($updatePhoto) $return = array("err_code" => "1", "msg" => "Successfully Updated Photo!");
      else $return = array("err_code" => "2", "msg" => "Failed to update photo..");
    }
    else{
      $newPhoto = $this->db->query("INSERT INTO elfinder_file(name, content, mime, parent_id) VALUES ('$filename', '$file', '$filetype', '1')");
      if($newPhoto) $return = array("err_code" => "3", "msg" => "Successfully Added Photo!");
      else $return = array("err_code" => "4", "msg" => "Failed to add photo..");
    }

    return $return;
  }

  function getEmployeePhoto($employeeid){
    $hasPhoto = $this->db->query("SELECT * FROM employee_photo where employeeid = '$employeeid'")->result();
    return $hasPhoto;
  }

  function getElfinderImage($employeeid){
    $photo = '';
    $employee_elfinder_file = $this->db->query("SELECT * FROM elfinder_file a WHERE a.name LIKE '%$employeeid%'")->result();
    foreach ($employee_elfinder_file as $key => $value) {
      $photo = "data:".$value->mime.";base64,".base64_encode($value->content);
    }
    return $photo;
  }

  function showDateHired($id)
  {
   $hiredate = '';
   $query = $this->db->query("SELECT dateposition FROM employee_employment_status_history WHERE employeeid='{$row->employeeid}' ORDER BY dateposition DESC");
              foreach ($query->result() as $key) {

                $hiredate = $key->dateposition;
              }
    return $hiredate;
  }

    //ADDED 08-11-2017 LONGEVITY
    function showLongevity($year,$campus){
        $wC = "";
        if($campus) $wC = " AND a.campusid = '".$campus."'";

        $query = $this->db->query("
                                    SELECT
                                        a.`employeeid`,
                                        a.`dateposition`,
                                        CONCAT(a.lname,', ',a.fname,' ',a.`mname`) AS fullname,
                                        a.`deptid`,
                                        a.`dateemployed`,
                                        a.`dateresigned`
                                        -- (SELECT b.dateposition FROM employee_employment_status_history b WHERE b.employeestat = 'REG' ORDER BY b.dateposition ASC LIMIT 1) AS dateOfRegularAppointment,
                                        -- (SELECT c.monthly FROM payroll_employee_salary_history c WHERE YEAR(c.TIMESTAMP) < ({$year} - 1) ORDER BY c.TIMESTAMP DESC LIMIT 1 ) AS lastPrevBasicPay,
                                        -- (SELECT c.monthly FROM payroll_employee_salary_history c WHERE YEAR(c.TIMESTAMP) < {$year} ORDER BY c.TIMESTAMP DESC LIMIT 1 ) AS prevBasicPay,
                                        -- (SELECT d.monthly FROM payroll_employee_salary d ORDER BY d.TIMESTAMP ASC LIMIT 1 ) AS presentBasicPay
                                    FROM employee a
                                    WHERE
                                        (a.`dateresigned` > a.`dateemployed` OR a.`dateresigned` = '1970-01-01' OR a.`dateresigned` IS NULL OR a.`dateresigned` = '0000-00-00')
                                        AND a.`employmentstat` = 'REG'
                                    ORDER BY deptid,lname ASC");
        return $query;
    }

  # for ica-hyperion 21289
  # by naces
  function getHeadDepartment($eid, $col = "code"){
    $data = array();
    $sql = "SELECT a.`code`, CONCAT(b.`lname`, ', ', b.`fname`, ' ', b.`mname`) AS head_funame, a.`description` AS head_position
            FROM code_office a
            LEFT JOIN employee b ON b.`employeeid` = a.`head`
            WHERE a.`code` = (SELECT deptid FROM employee WHERE employeeid='$eid');";
    $q_head = $this->db->query($sql)->result();
    foreach ($q_head as $res) {
      $data['code'] = $res->code;
      $data['head_funame'] = $res->head_funame;
      $data['head_position'] = $res->head_position;
    }

   return $data[$col];
  }
  # end for ica-hyperion 21289



  function saveOtherIncomeData($data)
  {
      $id = $data['id'];
      $income = $data['income'];
      $code_income = $data['incomedata'];
      $cutoff = $data['cutoff'];
      $datacutoff = explode(',',$cutoff);
      $dataArray = array_combine($id, $income);
      #echo "<pre>"; var_dump($data); die;
      $payrollstart=$payrollend = $insertIncome='';
       $res =0;

       # for ica-hyperion 21294
       # by justin (with e)
       # > additional ko lang sa pag save.. para malaman kung sino yung mga na process na employee at hindi pa..
       $error_emp = array();
       $success_emp = 0;
       # end for ica-hyperion 21294

      // $payrollcuttoff = $this->db->query("SELECT a.startdate,a.enddate FROM `payroll_cutoff_config` a LEFT JOIN cutoff b ON (a.`baseid` = b.`ID`) WHERE b.CutoffFrom='$datacutoff[0]' AND b.CutoffTo='$datacutoff[1]'");
      // if ($payrollcuttoff->num_rows() > 0) {
      //     $payrollstart = $payrollcuttoff->row()->startdate;
      //     $payrollend = $payrollcuttoff->row()->enddate;
          foreach ($dataArray as $key => $value) {
            $verifydata = $this->db->query("SELECT * FROM employee_income WHERE employeeid='$key' AND code_income='$code_income' AND datefrom='$datacutoff[0]'");
            #echo "<pre>"."SELECT * FROM employee_income WHERE employeeid='$key' AND code_income='$code_income' AND datefrom='$datacutoff[0]'";
            if ($verifydata->num_rows() > 0)
             {
                $res++;

                # > push sa array..
                $error_emp[$key] = "This Payrollcuttoff was already processed!";
             }
             else
             {
                $schedule = 'semi-monthly';
                $quarter = 3;

                $cutoff_q = $this->db->query("SELECT schedule, quarter FROM payroll_cutoff_config WHERE startdate='$datacutoff[0]' AND enddate='$datacutoff[1]'");
                if($cutoff_q->num_rows() > 0){
                  $schedule = $cutoff_q->row(0)->schedule;
                  $quarter = $cutoff_q->row(0)->quarter;
                }

                $isExist = $this->db->query("SELECT * FROM employee_income WHERE employeeid='$key' AND code_income='$code_income'")->result();

                if(count($isExist) > 0){
                  $updateIncome = $this->db->query("UPDATE employee_income SET datefrom='$datacutoff[0]', amount='$value', nocutoff=1, schedule='$schedule', cutoff_period='$quarter'
                                                    WHERE employeeid='$key' AND code_income='$code_income'");

                  if(!$updateIncome){
                    $res++;
                    $error_emp[$key] = "Failed to Saved!";
                  }
                  else $success_emp++;

                }else{
                  $insertIncome = $this->db->query("INSERT INTO employee_income(employeeid,code_income,datefrom,amount,nocutoff,schedule,cutoff_period)VALUES('$key','$code_income','$datacutoff[0]','$value',1,'$schedule','$quarter')");
                  if(!$insertIncome){
                    $res++;
                    $error_emp[$key] = "Failed to Saved!";
                  }
                  else $success_emp++;

                }

             }
          }
      // }
      /*if ($insertIncome) {
        $msg = "Successfully Saved!";
      }
      else if ($res > 0) {
          $msg = "This Payrollcuttoff was already processed!";
      }
      else
      {
        $msg="Failed to Saved!";
      }*/
      $return = array(
        "ERROR" => $error_emp,
        "ERROR_TOTAL" => $res,
        "SUCCESS" => $success_emp
      );

      return $return;
  }
    //Save longevity income
  function saveLongevityIncome($data)
  {
    $datefrom = $data['date'][0][0];
    $dateto   = $data['date'][0][1];
    $empid = $data['emp_no'];
    $income = $data['income'];
    $startdate = $sched='';
    $count = 0;
    $result = array();
    $result = array_combine($empid, $income);


    $query = $this->db->query("SELECT b.`startdate`,b.`schedule` FROM cutoff a
    LEFT JOIN payroll_cutoff_config b ON(a.`ID` = b.`id`) WHERE a.`CutoffFrom` ='$datefrom' AND a.`CutoffTo`='$dateto' ;");
    if ($query->num_rows()>0)
    {
        $sched = $query->row(0)->schedule;
        $startdate = $query->row(0)->startdate;
          $select = $this->db->query("SELECT * FROM employee_income WHERE datefrom='$startdate'");
                      if ($select->num_rows() > 0)
                      {
                        return 'This record is already saved!';

                      }
                      else
                      {
                        foreach ($result as $key => $value) {

                           if ($value !=0)
                             {
                               $proposedincome = ($value/2);

                                   $savedata = $this->db->query("INSERT INTO employee_income(employeeid,code_income,datefrom,amount,nocutoff,schedule,cutoff_period)VALUES('$key','14','$startdate','$proposedincome','24','$sched','3')");
                                   if ($savedata === true) {
                                      $count ++;
                                   }

                             }
                         }
                      }





    }
    if (count($savedata) > 0) {
       return ($count)." Employee(s) Successfully S aved!";
    }
    else
    {
       return "Failed to saved!";
    }



  }

    function saveLongevity($emplist,$cutoff){
    $emp = explode("|",$emplist);
    $sched = explode(",",$cutoff);
    foreach($emp as $list){
        list($employeeid,$longevity) = explode("~u~",$list);
        list($datefrom,$dateto) = $sched;
        $income = "3";
        $incomebase = "3";
        $remarks = $income;
        $schedule = "semi-monthly";
        $period = "3";
        $amount = $longevity;
        $nocutoff = 1;
        $dsetfrom = $datefrom ? date("Y-m-d",strtotime($datefrom)) : "0000-00-00";
        $dsetto = $dateto ? date("Y-m-d",strtotime($dateto)) : "0000-00-00";

        $this->db->query("CALL prc_employee_income_set('{$employeeid}',
                                                    '{$income}',
                                                    '{$remarks}',
                                                    '{$dsetfrom}',
                                                    '{$dsetto}',
                                                    '{$amount}',
                                                    '{$nocutoff}',
                                                    '{$incomebase}',
                                                    '{$schedule}',
                                                    '{$period}')");
        }
        return "Success";
    }

  public function addNewEmployee($insert_data){
    $res = $this->db->insert("employee", $insert_data);
    return $res;
  }

  public function addNewEmployeeAccount($insert_data){
    $res = $this->db->insert("user_info", $insert_data);
    return $res;
  }

  function getOtherIncomedata($data)
  {
    $return = "";
    $query = $this->db->query("SELECT * FROM other_income WHERE employeeid='{$data['employeeid']}' AND other_income='{$data['otherIncome']}'");
    return $query->result();
  }
    function saveOtherIncome($datas,$othIncome){
    $return = "";
    $result = array('save'=>0,'failed'=>0);
    $countFailed = 0;
    $countSaved = 0;
    $user = $this->session->userdata("username");
        foreach(explode("|",$datas) as $k => $v)
        {

            list($empid,$monthly,$daily,$hourly,$dateEffective,$dateEnd) = explode("~u~",$v);

      $query = $this->db->query("SELECT * FROM other_income WHERE employeeid = '{$empid}' AND other_income = '{$othIncome}'");
      if ($query->num_rows() > 0) {
        $countFailed++;
          $UpdateQuery = $this->db->query("UPDATE other_income SET monthly='{$monthly}',daily='{$daily}',hourly='{$hourly}',dateEffective='{$dateEffective}',dateEnd='{$dateEnd}' WHERE employeeid = '{$empid}' AND other_income = '{$othIncome}'");
          if ($UpdateQuery)
          {
             $InsertQuery = $this->db->query("INSERT INTO other_income_history(employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,timestamp,appliedby,status) SELECT employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,CURRENT_TIMESTAMP,'$user','UPDATED' FROM other_income WHERE other_income='{$othIncome}'  AND employeeid='$empid' AND monthly <> '0'");
          }


      }
      else
      {
        $countSaved++;
        $this->db->query("DELETE FROM other_income where employeeid = '{$empid}' AND other_income = '{$othIncome}'");
        $this->db->query("INSERT INTO other_income (employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,timestamp,appliedby)
        VALUES('{$empid}','{$othIncome}','{$monthly}','{$daily}','{$hourly}','{$dateEffective}','{$dateEnd}',CURRENT_TIMESTAMP,'$user')");
         $InsertQuery = $this->db->query("INSERT INTO other_income_history(employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,timestamp,appliedby,status) SELECT employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,CURRENT_TIMESTAMP,'$user','ADDED' FROM other_income WHERE other_income='{$othIncome}'  AND employeeid='$empid' AND monthly <> '0'");
      }


        }

    if ($countSaved > 0) {
      $return = "Success";
    }
    else if ($countFailed > 0 ) {
      $return = "Failed";
    }
    $result = array('save'=>$countSaved,'failed'=>$countFailed);
        return $result;
    }

    function deleteOtherIncome($employeeid,$othIncome)
  {
     $user = $this->session->userdata("username");
    $return = "";
    $user = $this->session->userdata("username");
    $queryInsert = $this->db->query("INSERT INTO other_income_history(employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,timestamp,appliedby,status) SELECT employeeid,other_income,monthly,daily,hourly,dateEffective,dateEnd,timestamp,'$user','DELETED' FROM other_income WHERE  employeeid = '{$employeeid}' AND other_income = '{$othIncome}'");
    if ($queryInsert) {
        $query = $this->db->query("DELETE FROM other_income WHERE employeeid = '{$employeeid}' AND other_income = '{$othIncome}'");
        $return ="Success";
    }
    else
    {
       $return ="Failed";
    }

    return $return;
  }

  function CountOtherIncomeHistory($oth)
  {
    $return = 0;
    $query = $this->db->query("SELECT * FROM other_income_history WHERE other_income='{$oth}'");
    if ($query->num_rows() > 0) {
      $return = $query->num_rows();
    }
    return $return;
  }
    function emplistWithOtherIncome($otherIncome, $emp)
    {
        if($emp != ''){
        $return = $this->db->query("SELECT a.*, CONCAT(b.lname, ' ,', b.fname, ' ,', b.mname) AS fullname FROM other_income a INNER JOIN employee b ON b.employeeid = a.employeeid WHERE a.other_income = '{$otherIncome}' and a.employeeid = '{$emp}'");
        return $return;
        }
        else{
            $return = $this->db->query("SELECT a.*, CONCAT(b.lname, ' ,', b.fname, ' ,', b.mname) AS fullname FROM other_income a INNER JOIN employee b ON b.employeeid = a.employeeid WHERE a.other_income = '{$otherIncome}'");
            return $return;
        }
    }

  function emplistWithOtherIncomeHistory($otherIncome)
  {
    $return = $this->db->query("SELECT * FROM other_income_history WHERE other_income = '{$otherIncome}' ORDER BY TIME(TIMESTAMP) DESC");
    return $return;
  }
    function showOtherTable($sdate='',$edate='',$campus='',$othIncome=''){
        $wC = "";
        if($campus) $wC = "AND b.campusid = '{$campus}'";
        $return = $this->db->query("SELECT a.*,b.teachingtype FROM other_income a
                                    LEFT JOIN employee b on a.employeeid = b.employeeid
                                    WHERE a.other_income = '{$othIncome}'
                                AND ( (dateEffective <= '$edate') OR (dateEnd >= '$edate') )
                                    {$wC}
                                    order by a.employeeid");
        return $return;
    }

    function showOverloadTable($cutoff,$campus,$othIncome){
        $wC = "";
        if($campus) $wC = "AND b.campusid = '{$campus}'";
        $return = $this->db->query("SELECT a.* FROM other_income a
        LEFT JOIN employee b on a.employeeid = b.employeeid
        WHERE a.other_income = '{$othIncome}'
        {$wC}
        order by a.employeeid");
        return $return;
    }
  // Title : find campus principal
  // Author: justin (with e)
  // Date : 08/26/2017
  function campus_principal($userid){
      $sql = $this->db->query("SELECT * FROM code_campus WHERE campus_principal='{$userid}'")->result();
      //echo "<pre>". print_r("SELECT * FROM code_campus WHERE campus_principal={$userid}, ". count($sql)); die;
      if(count($sql) > 0) return true;
      return false;
  }
  // END of find campus principal

  //Glenmark
  //saving for new remarks
  function saveRemarks($data)
  {
    $toks = $data['toks'];
    $description = $this->gibberish->decrypt($data['desc'], $toks );
    $msg = "";
    $query = $this->db->query("SELECT * FROM code_request_type WHERE description='$description'");
    if ($query->num_rows() > 0) {
        $msg = "This description was already taken!   ";
    }
    else
    {
      $query = $this->db->query("INSERT INTO code_request_type(description)VALUES('$description')");
      if ($query ===  TRUE) {
        $msg = "Successfully Saved!";
      }
      else
      {
        $msg ="Failed to saved!";
      }

    }
    return $msg;
  }

  # for ica-hyperion 21578
  # by justin (with e)
  function loadallemployees()
  {
    $return = "";
    $query = $this->db->query(" SELECT `employeeid`, `employeecode`, `emptype`, `empshift`, `date_active`, `employmentstat`, `deptid`, `lname`, `fname`, `mname`, `nname`, `gender`, `mobile`, `email`, `cityaddr`, `provaddr`, `regionaladdr`, `addr`, `citytelno`, `bdate`, `bplace`, `maxregular`, `maxparttime`, `dateemployed`, `civil_status`, `spouse_name`, `income_base`, `tax_status`, `dateresigned`, `resigned_reason`, `emp_tin`, `emp_sss`, `emp_philhealth`, `emp_pagibig`, `emp_peraa`, `emp_medicare`, `emp_accno`, `positionid`, `dateposition`, `assignment`, `remarks`, `managementid`, `citizenid`, `religionid`, `nationalityid`, `prc`, `passport`, `visa`, `icardnum`, `crnno`, `permanentaddr`, `cp_name`, `cp_relation`, `cp_address`, `cp_mobile`, `cp_telno`, `teaching`, `teachingtype`, `isactive`, `leavetype`, `occupation`, `age`, `mother`, `motheroccu`, `father`, `fatheroccu`, `hospitalized`, `hospitalizedtxt`, `operation`, `operationtxt`, `operationdate`, `medhistory`, `medhistorytxt`, `medconditions`
    FROM (`employee`)
    WHERE (dateresigned='1970-01-01' OR dateresigned='0000-00-00' OR dateresigned IS NULL)
    ORDER BY `lname` ASC, `fname` ASC, `mname` ASC")->result_array();
    return $query;
  }
  # end for ica-hyperion 21578

   function getHRUserAndUserFullname($empId){
    $hrName = $fullname = "";

    # find name ng hr
    $hrName = $this->db->query("SELECT CONCAT(b.`fname`, ' ', b.`mname`, ' ', b.`lname`) AS fullname
                                FROM code_office a
                                INNER JOIN employee b ON b.`employeeid` = a.`head`
                                WHERE a.`code`='HR';");
    if($hrName->num_rows() > 0){
      $hrName = $hrName->row()->fullname;
    }else{
      $hrName = "";
    }

    # user fullname
    $fullname = $this->db->query("SELECT CONCAT(b.`firstname`, ' ', b.`middlename`, ' ', b.`lastname`) AS fullname FROM `user_info` b WHERE b.`username`='{$empId}';");

    if($fullname->num_rows() > 0){
      $fullname = $fullname->row()->fullname;
    }else{
      $fullname = "";
    }

    return array(
              "hr"    => $hrName,
              "user"  => $fullname
           );
  }

  function isFlexiEmployee($employeeid){
    $result = false;

    $q_flexi = $this->db->query("SELECT isFlexi FROM employee WHERE employeeid='$employeeid'")->result();
    foreach ($q_flexi as $row) if($row->isFlexi) $result = true;

    return $result;
  }

  public function addNewBorrower($insert_data){
    return $this->db->insert("Library.borrowers", $insert_data);
  }
  function getEmployeeList($active){
     $where_clause = "";
     if($active == "active") $where_clause = " WHERE((dateresigned2='0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND (isactive = '1'))";
     else if($active == "inactive") $where_clause = "WHERE ((dateresigned2 != '1970-01-01' AND dateresigned2 != '0000-00-00' AND dateresigned2 IS NOT NULL) OR (isactive = '0'))";
     $employee_list = $this->db->query("SELECT CONCAT(lname, ' ,', fname , ' ,', mname) AS fullname, employeeid,emptype,campusid, office, deptid, teachingtype, age, gender FROM employee $where_clause ORDER BY fullname")->result_array();
     return $employee_list;
  }
  function getEmployeeListWithBio(){
     $employee_list = $this->db->query("SELECT CONCAT(a.`lname`, ' ,', a.`fname` , ' ,', a.`fname`) AS fullname, a.`employeeid`,a.`teachingtype` FROM employee a INNER JOIN bio b WHERE a.`employeeid` = b.`userID` GROUP BY a.`employeeid`")->result_array();
     return $employee_list;
  }

  function getEmployeeListExcluded(){
     $employee_list = $this->db->query("SELECT CONCAT(a.`lname`, ' ,', a.`fname` , ' ,', a.`fname`) AS fullname, a.`employeeid`,a.`teachingtype` FROM employee a LEFT JOIN bios_excluded b ON a.`employeeid` = b.`userid` WHERE b.`userid` IS NULL")->result_array();
     return $employee_list;
  }

  function getEmployeeExcludedInOut(){
     $list = $this->db->query("SELECT CONCAT(a.`lname`, ' ,', a.`fname` , ' ,', a.`fname`) AS fullname, a.`employeeid`,a.`teachingtype`, b.`isactive` FROM employee a INNER JOIN bios_excluded b WHERE a.`employeeid` = b.`userid` GROUP BY a.`employeeid`")->result_array();
     return $list;
  }

  function getEmployeeBioPic($empid){
     $employee_bio = $this->db->query("SELECT * FROM bio WHERE userID = '$empid'")->result_array();
     return $employee_bio;
  }

  function updateEmployeeInformation($employeeid, $column, $value){
    return $this->db->query("UPDATE employee SET $column = '$value' WHERE employeeid = '$employeeid' ");
  }

  function isEmployeeIDExist($employeeid){
     $hyperion = $this->db->query("SELECT * FROM employee WHERE employeeid = '$employeeid' ")->num_rows();
     $user = $this->db->query("SELECT * FROM user_info WHERE username = '$employeeid' ")->num_rows();
     $aims = $this->db->query("SELECT * FROM Poveda.tblFacultyProfile WHERE FCode = '$employeeid' ")->num_rows();
     $total = $hyperion + $user + $aims;
     return $total;
  }

  function isUsernameExist($employeeid){
     $res = $this->db->query("SELECT * FROM user_info WHERE username = '$employeeid' ")->num_rows();
     if ($res == 0) $res = $this->db->query("SELECT employeeid FROM employee WHERE employeeid = '$employeeid' ")->num_rows();
     return $res;
  }

  function updateTableStatus($table, $id, $approver){
    $check = $this->db->query("SELECT * FROM $table where id = '$id'")->result_array();
    if($check){
      if($check[0]['status'] == 'APPROVED'){
        $update = $this->db->query("UPDATE $table SET status = 'PENDING' WHERE id = '$id'");
        // echo "<pre>"; print_r($this->db->last_query());
        $insert = $this->db->query("INSERT INTO status_history(table_name, approver, recent_status, table_id)VALUES('$table', '$approver', 'APPROVED', '$id')");
        return "PENDING";
      }else{
        $update = $this->db->query("UPDATE $table SET status = 'APPROVED' WHERE id = '$id'");
        if($table == 'employee_eligibilities'){
          if(strpos($check[0]['description'], 'LPT') !== false || strpos($check[0]['description'], 'lpt') !== false){
            $this->employee->updatePRC($check[0]['employeeid'], $check[0]['date_expired'], $check[0]['license_number']);
          }
        } 
        // echo "<pre>"; print_r($this->db->last_query());
        $insert = $this->db->query("INSERT INTO status_history(table_name, approver, recent_status, table_id)VALUES('$table', '$approver', 'PENDING', '$id')");
        return "APPROVED";
      }
    }



  }

  function loadStatusHistory($id, $table){
    return $this->db->query("SELECT * FROM status_history where table_id = '$id' and table_name = '$table' ORDER BY id DESC")->result_array();
  }

  function getemployeestatus($id){
    return $this->db->query("SELECT dateresigned, dateresigned2, isactive FROM employee where employeeid = '$id'")->result_array();
  }

  function getemployeecampus($id){
    return $this->db->query("SELECT b.description FROM employee a INNER JOIN code_campus b ON a.campusid = b.code where employeeid = '$id'")->row()->description;
  }

  function checkifIDExist($value, $column, $employeeid){
    return $this->db->query("SELECT * FROM employee where $column = '$value' AND $column <> '' AND employeeid <> '$employeeid'")->num_rows();
  }

  function emailChecker($email){
      $isExist = false;
      $q_alumniAims = $this->db->query("SELECT * FROM employee a INNER JOIN user_info b WHERE b.email = '$email' OR a.email = '$email' OR a.personal_email = '$email' limit 1")->result();
      foreach ($q_alumniAims as $row) $isExist = true;
      return $isExist;
  }

  function webCheckInChecker($employee){
      $isExist = false;
      $today = date("Y-m-d", strtotime($this->db->query("SELECT CURRENT_TIMESTAMP")->row()->CURRENT_TIMESTAMP));

      $this->db->query("UPDATE weblogin_setup SET `status` = 'inactive' WHERE date_to < '$today'");
      $q_alumniAims = $this->db->query("SELECT * FROM weblogin_setup WHERE employee = '$employee' AND date_to >= '$today' AND date_from <= '$today' AND `status` = 'active' ")->result();
      foreach ($q_alumniAims as $row) $isExist = true;
      return $isExist;
  }

  function webCheckRecent($employee){
      $today = date("Y-m-d", strtotime($this->db->query("SELECT CURRENT_TIMESTAMP")->row()->CURRENT_TIMESTAMP));
      $recentCheckIn = $this->db->query("SELECT localtimein, log_type FROM webcheckin_history WHERE userid = '$employee' AND DATE(localtimein) = '$today'  ORDER BY localtimein DESC LIMIT 4")->result();
      return $recentCheckIn;
  }

  function checkIFBankExist($value, $employeeid, $bank){
    $return = 0;
    $bankAccounts = $this->db->query("SELECT * FROM employee where emp_bank LIKE '%$value%' AND employeeid <> '$employeeid'")->result_array();
    if($bankAccounts){
        $convert_to_array = explode('/', $bankAccounts[0]["emp_bank"]);
        for($i=0; $i < count($convert_to_array ); $i++){
            $key_value = explode('=', $convert_to_array [$i]);
            $banks[$key_value [0]] = isset($key_value [1]) ? $key_value [1] : "";
        }

        foreach ($banks as $key => $val) {
          if($key.'='.$val == $value){
            $return++;
          }
        }
        return $return;
    }else{
      return 0;
    }
  }

  function load201sort($where_clause){
    return $this->db->query("SELECT * FROM employee $where_clause order by lname, fname, mname")->result_array();
  }

  function webCheckSurvey($employee){
      return $this->db->query("SELECT a.*, b.`rank` FROM survey_items a LEFT JOIN survey_category b ON a.`category` = b.`id` WHERE a.`status` = 'Active' AND a.`audience` = 'all' OR a.`audience` LIKE '%$employee%' ORDER BY b.`rank` ASC")->result();
  }

  public function updateEmployeeRank($employeeid, $value){
    $this->db->query("UPDATE payroll_employee_salary SET rank = '$value' WHERE employeeid = '$employeeid'");
    // $this->db->query("UPDATE payroll_employee_salary_history SET rank = '$value' WHERE employeeid = '$employeeid' ORDER BY date_effect LIMIT 1");
  }

  public function getEmployeeRank($employeeid){
    $q_rank = $this->db->query("SELECT rank FROM employee WHERE employeeid = '$employeeid' ");
    if($q_rank->num_rows() > 0) return $q_rank->row()->rank;
    else return false;
  }

  public function saveALLCARDLogs($empinfo, $isbatch=false){
    if(!$isbatch){
      $this->db->insert("allcard_logs", $empinfo);
    }
    else{
      foreach($empinfo as $row){
        $row["processed_by"] = $this->session->userdata("username");
        $this->db->insert("allcard_logs", $row);
      }
    }
  }

  function updatePRC($employeeid, $prc_expiration, $prc){
  return $this->db->query("UPDATE employee SET prc_expiration = '$prc_expiration', prc = '$prc' WHERE employeeid = '$employeeid' ");
}

}
/* End of file employee.php */
/* Location: ./application/models/employee.php */
