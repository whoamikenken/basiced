<?php 
/**
 * @author Justin
 * @copyright 2016
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports extends CI_Model {
    
    /*
     * Load Options
     */
    function employeetype($tnt = ""){
        $return  = "";
        $type = array("teaching"=>"teaching","nonteaching"=>"non teaching");
        foreach($type as $key=>$val){
            if($tnt == $key) $selected = "selected";
            else $selected = "";
            $return .= "<option value='$key' $selected>".ucwords($val)."</option>";    
        }
        return $return;
    }
    
    /*
     * Load Query Data
     */ 
    function loadempdata($col='', $division='', $deptid='', $employeeid='',$campus='',$isactive=''){
        $datenow = date("Y-m-d");
        $return = "";
        
        $empstathistory = array("managementid2","deptid2","employmentstat2","positionid2","dateposition2");
        $family = array("fmname","fmrelation","fmdob");
        $children = array("childname","childbday","childage");
        $taxDependents = array ("tdname","tdrelation","tdaddress","tdcontact","tdbdate","tdlegitimate");
        
        
        $excol  = explode(',',$col);
        foreach($excol as $str){
            if(!in_array($str,$empstathistory) && !in_array($str,$family) && !in_array($str,$taxDependents))
            {
            if($return) $return .= ",";
            if($str == "rank")          $str = " z.description as rank ";
            if($str == "empshift")          $str = " b.schedcode as empshift ";
            if($str == "emptype")           $str = " c.description as emptype ";
            if($str == "employmentstat")    $str = " d.description as employmentstat ";
            if($str == "deptid")            $str = " ed.description as deptid ";
            if($str == "civil_status")      $str = " f.description as civil_status ";
            if($str == "isactive")          $str = " IF(isactive = 1,'Active','In Active') as isactive";
            if($str == "positionid")        $str = " g.description as positionid";
            if($str == "managementid")      $str = " h.description as managementid";
            if($str == "cregion")            $str = " j.region_name as cregion";
            if($str == "cprovince")          $str = " k.provDesc as cprovince";
            if($str == "cmunicipality")      $str = " l.citymunDesc as cmunicipality";
            if($str == "gender")            $str = " m.description as gender";
            if($str == "religionid")        $str = " n.description as religionid";
            if($str == "citizenid")         $str = " o.description as citizenid";
            if($str == "nationalityid")     $str = " p.description as nationalityid";
            if($str == "pregion")            $str = " q.region_name as pregion";
            if($str == "pprovince")          $str = " r.provDesc as pprovince";
            if($str == "pmunicipality")          $str = " s.citymunDesc as pmunicipality";
            if($str == "pbrgy")          $str = " t.brgyDesc as pbrgy";
            if($str == "pzipcode")          $str = " a.permaZipcode as pzipcode";
            if($str == "czipcode")          $str = " a.zip_code as czipcode";
            if($str == "cbrgy")          $str = " w.brgyDesc as cbrgy";
            if($str == "spouse_name")        $str = " a.spouse_name as spouse_name";
            if($str == "paddr")          $str = " a.permaAddress as paddr";
            if($str == "caddr")          $str = " a.addr as caddr";
            if($str == "work_email")          $str = " a.email as work_email";
            if($str == "spouse_occupation")   $str = " a.occupation as spouse_occupation";
            if($str == "spouse_contact")   $str = " a.spouse_contact as spouse_contact";
            if($str == "officeid")   $str = " e.description as officeid";
            $return .= $str; 
            }
        }
        
        $wC = '';
        if($division)       $wC .= " AND a.managementid='$division'";
        if($deptid)         $wC .= " AND a.deptid='$deptid'";
        if($employeeid)     $wC .= " AND a.employeeid='$employeeid'";
        if($campus)        $wC .= " AND a.campusid='$campus'";

        if($isactive != ""){
          if($isactive=="1"){
            $wC .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wC .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wC .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        if($return == '') $return = '*';
        $query = $this->db->query("SELECT DISTINCT $return FROM employee a 
                                    LEFT JOIN code_schedule b ON a.empshift = b.schedid 
                                    LEFT JOIN code_type c ON a.emptype = c.code
                                    LEFT JOIN code_status d ON a.employmentstat = d.code
                                    LEFT JOIN code_office e ON a.office = e.code
                                    LEFT JOIN code_department ed ON a.deptid = ed.code
                                    LEFT JOIN code_civil_status f ON a.civil_status = f.code
                                    LEFT JOIN code_position g ON a.positionid = g.positionid
                                    LEFT JOIN code_managementlevel h ON a.managementid = h.managementid
                                    LEFT JOIN regions j ON a.regionaladdr = j.region_code
                                    LEFT JOIN refprovince k ON a.provaddr = k.provCode
                                    LEFT JOIN refcitymun l ON a.cityaddr = l.citymunCode
                                    LEFT JOIN code_gender m ON a.gender = m.genderid
                                    LEFT JOIN code_religion n ON a.religionid = n.religionid
                                    LEFT JOIN code_citizenship o ON a.citizenid = o.citizenid
                                    LEFT JOIN code_nationality p ON a.nationalityid = p.nationalityid
                                    LEFT JOIN regions q ON a.permaRegion = q.region_code
                                    LEFT JOIN refprovince r ON a.permaProvince = r.provCode
                                    LEFT JOIN refcitymun s ON s.citymunCode = a.permaMunicipality
                                    LEFT JOIN refbrgy t ON a.permaBarangay = t.brgyCode
                                    LEFT JOIN refbrgy w ON a.barangay = w.brgyCode
                                    LEFT JOIN rank_code_type z ON a.rank = z.id
                                    WHERE a.employeeid != '' $wC ORDER BY lname")->result();

        return $query;
    }

    function loadempbirthdayreportage($isactive=""){
        $wc = "";
        $datenow = date("Y-m-d");
        // if($isactive != "") $wc = " WHERE isactive = '$isactive' ";
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        $query = $this->db->query("SELECT employeeid, fname, lname, mname, deptid, bdate, age, CONCAT(lname, ', ', fname, ' ', mname) AS fullname, office FROM employee WHERE 1 $wc ORDER BY bdate DESC")->result();

        return $query;
    }

    function loadempbirthdayreportall($isactive=""){
        $wc = "";
        $datenow = date("Y-m-d");
        // if($isactive != "") $wc = " WHERE isactive = '$isactive' ";
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        $query = $this->db->query("SELECT employeeid, fname, lname, mname, deptid, bdate, age, CONCAT(lname, ', ', fname, ' ', mname) AS fullname, office FROM employee WHERE 1 $wc ORDER BY DATE_FORMAT(bdate, '%m %d')")->result();
        return $query;
    }

    function loadempbirthdayreportmonth($month, $isactive=""){
        $wc = "";
        $datenow = date("Y-m-d");
        // if($isactive != "") $wc = " WHERE isactive = '$isactive' ";
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        $query = $this->db->query("SELECT employeeid, fname, lname, mname, deptid, bdate, age, CONCAT(lname, ', ', fname, ' ', mname) AS fullname, office FROM employee WHERE DATE_FORMAT(bdate, '%m') = $month $wc ORDER BY DATE_FORMAT(bdate, '%d')")->result();
        return $query;
    }

    function getTaxableAmount($empid, $cutoff, $isMRRreport = ""){
        $amount = $gross = $tardy = $absent = 0;
        $list_income = '';

        if(!$isMRRreport){
            $ecutoff = explode(",",$cutoff);
            $cStart = $ecutoff[0];
            $cEnd = $ecutoff[1];
            $q_tax = $this->db->query("SELECT a.gross,a.income, a.tardy, a.absents 
                                    FROM payroll_computed_table a
                                    WHERE a.employeeid = '$empid' AND a.cutoffstart='$cStart' AND a.cutoffend = '$cEnd' AND a.bank <> '' AND a.empAccNo <> '';")->result();
            if(count($q_tax) > 0){
                foreach ($q_tax as $res) {
                    $gross = $res->gross;
                    $tardy = $res->tardy;
                    $absent = $res->absents;
                    $list_income = $res->income;
                }

                $amount = $gross;
                foreach (explode("/", $list_income) as $exp_list_income) {
                    list($id, $value) = explode("=", $exp_list_income);

                    $isNoTax = $this->getSetupForPayrollIncome("taxable", $id);

                    if($isNoTax == 'notax') $amount -= $value;
                }
            }else{
                $amount = 0;
            }
        }else{
            $q_tax = $this->db->query("SELECT a.gross,a.income, a.tardy, a.absents 
            FROM payroll_computed_table a
            WHERE a.employeeid = '$empid' AND DATE_FORMAT(a.`cutoffstart`, '%M~~%Y') = '$cutoff' AND DATE_FORMAT(a.`cutoffstart`, '%M~~%Y') = '$cutoff' AND a.bank <> '' ")->result();
        

            if(count($q_tax) > 0){
                foreach ($q_tax as $res) {
                    $gross += $res->gross;
                    $tardy += $res->tardy;
                    $absent += $res->absents;
                    $list_income .= "/".$res->income;
                }
                $list_income = substr($list_income, 1);
                $amount = $gross;
                foreach (explode("/", $list_income) as $exp_list_income) {
                    list($id, $value) = explode("=", $exp_list_income);

                    $isNoTax = $this->getSetupForPayrollIncome("taxable", $id);

                    if($isNoTax == 'notax') $amount -= $value;
                }
            }else{
                $amount = 0;
            }
            return $amount;
        }
    }

    function alphalistEmp($year)
    {
        $return = $this->db->query("SELECT 
                                        CONCAT(a.lname,' ,',a.fname) AS fullname,
                                        a.emp_sss,
                                        a.emp_tin,
                                        a.emp_philhealth,
                                        a.emp_pagibig,
                                        a.bdate,
                                        b.employeeid
                                    FROM employee a
                                    INNER JOIN
                                    (
                                        SELECT DISTINCT(employeeid)
                                        FROM payroll_computed_table
                                        WHERE YEAR(cutoffstart) = '{$year}' AND YEAR(cutoffend) = '{$year}'
                                    ) b ON a.employeeid = b.employeeid
                                    ORDER BY a.lname")->result();
        return $return;
    }

    function alphalistData($empid,$year)
    {
        $return = $this->db->query("SELECT *
                                    FROM payroll_computed_table
                                    WHERE YEAR(cutoffstart) = '{$year}' AND YEAR(cutoffend) = '{$year}' AND employeeid = '{$empid}'
                                    ORDER BY cutoffstart")->result();
        return $return;
    }
    function loademployeeDeduction($datefrom="",$dto="",$employeeid="",$code= "")
    {
        $wC = "";
        if ($datefrom) {$wC .= "AND cutoffstart ='$datefrom'";}
        if ($dto) {$wC .= "AND cutoffend ='$dto'";}
        if ($employeeid) { $wC .= " AND a.employeeid='$employeeid'";}
        if ($code) { $wC .= "AND a.code_loan='$code'";}
       
        $query = $this->db->query("SELECT *, SUM(a.amount) AS amounts  FROM employee_loan_history a  LEFT JOIN employee b ON (a.`employeeid` = b.employeeid) WHERE a.mode='CUTOFF' AND  (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00') AND a.employeeid  !='' $wC  ORDER BY a.employeeid, a.code_loan")->result();

        return $query;
    }

    function loademployeeDeductionForVerify($datefrom="",$dto="",$employeeid="",$code= "")
    {
        $wC = "";
        if ($datefrom && $dto) {$wC .= "AND cutoffstart BETWEEN '$datefrom' AND '$dto'";}
        /*if ($dto) {$wC .= "AND cutoffend ='$dto'";}*/
        if ($employeeid) { $wC .= " AND a.employeeid='$employeeid'";}
        if ($code) { $wC .= "AND a.code_loan='$code'";}
       
        $query = $this->db->query("SELECT * FROM employee_loan_history a  LEFT JOIN employee b ON (a.`employeeid` = b.employeeid) WHERE (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00') AND a.employeeid  !='' $wC  ORDER BY a.employeeid, a.code_loan")->result();

        return $query;
    }

    function loademployeeDeductionDetailed($stats,$startdate="",$enddate="",$employeeid="",$code)
    {
        $wC = "";
        if ($startdate) {$wC .= "AND cutoffstart ='$startdate'";}
        if ($enddate) {$wC .= "AND p.cutoffend ='$enddate'";}
        if ($employeeid) { $wC .= " AND a.employeeid='$employeeid'";}
        if ($code) { $wC .= "AND a.code_loan='$code'";}
       
        $query = $this->db->query("SELECT * FROM employee_loan_history a  LEFT JOIN employee b ON (a.`employeeid` = b.employeeid) LEFT JOIN payroll_computed_table p ON (b.`employeeid` = p.employeeid) WHERE  p.status = '$stats' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00') AND a.employeeid  !='' $wC  AND a.mode !='DELETED' ORDER BY a.employeeid,a.code_loan,a.timestamp")->result();
        
        return $query;
    }
    function loadEmployeeLedgerData($eid="",$empstat="",$dept="",$year="")
    {
        $whereClause="";
        if($eid)  $whereClause .="AND b.employeeid='$eid'";
        if($empstat) $whereClause .="AND b.employmentstat='$empstat'";
        if($dept) $whereClause .="AND b.deptid='$dept'";
        // if($year) $whereClause .="AND YEAR(a.cutoffstart)='$year' AND YEAR(a.cutoffend)='$year'";

        $query = $this->db->query("SELECT a.*, CONCAT(lname, ', ', fname, ' ', mname) AS fullname,b.emp_accno,b.emp_sss,b.`emp_tin`,c.`description` AS empPosition,d.`description` AS department,e.`description` AS employmentstatus
        FROM payroll_computed_table a LEFT JOIN employee b ON a.employeeid = b.employeeid LEFT JOIN code_position c ON(c.`positionid` = b.`positionid`) LEFT JOIN code_office d ON(d.code = b.`deptid`) LEFT JOIN code_status e ON(e.code = b.employmentstat) WHERE YEAR(a.cutoffstart) = '$year'  $whereClause ORDER BY a.cutoffstart ASC LIMIT 5")->result();
        return $query;
    }


    
    //Added 5-19-17
    function loadempdataschedule($division="",$department="",$tnt="",$dfrom="",$isactive=""){
        $wC ="";
        $datenow = date("Y-m-d");
        if($division) $wC.="AND a.managementid = '{$division}'";
        if($department) $wC.="AND a.deptid = '{$department}'";
        if($tnt) $wC.="AND a.teachingtype = '{$tnt}'";
        if($isactive != ""){
          if($isactive=="1"){
            $wC .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wC .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wC .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        if($dfrom) $wC.="AND a.dateemployed <= '{$dfrom}'";
        $this->db->query("UPDATE employee set deptid = NULL where deptid = ''");
        $query = $this->db->query("SELECT DISTINCT a.*, CONCAT(a.lname,', ',a.fname,' ',a.mname) as fullname  FROM employee a 
                                    INNER JOIN employee_schedule b on a.employeeid = b.employeeid
                                    WHERE 1 $wC GROUP BY employeeid ORDER BY case when a.deptid is null then 1 else 0 end, a.deptid, fullname")->result();
        // echo "<pre>"; print_r($this->db->last_query()); die;
        return $query;
    }

    function getPayrollIncomeConfig($table="", $id="", $is_adjustment=false){
        $wC = "";
        if($id != "selectAll") $wC .= "id = '$id' ";
        if($is_adjustment) $wC .= "description LIKE '% adj%' OR description LIKE '%adjustment%' OR description LIKE '%adj %'";

        $wC = ($wC) ? "WHERE ". $wC : "";
        $data = $this->db->query("SELECT * FROM $table $wC")->result_array();
        return $data;
    }

    function getPayrollIncomeConfigDeminimis($table="", $id=""){
        $wC = "";
        ($id == 'selectAll') ? $wC .= ""  : $wC .= " AND id = '$id'";
        $data = $this->db->query("SELECT * FROM $table WHERE incomeType = 'deminimiss' $wC")->result_array();
        return $data;
    }

    function getPayrollIncomeConfigNoDeminimis($table="", $id=""){
        $wC = "";
        ($id == 'selectAll') ? $wC .= ""  : $wC .= " AND id = '$id'";
        $data = $this->db->query("SELECT * FROM $table WHERE incomeType != 'deminimiss' $wC")->result_array();
        return $data;
    }

    function getEmployeeListPerCodeIncome($code_income="",$cutoffstart="", $cutoffend="", $tnt="",$eid=""){
        $wC = "";
        if($code_income) $wC .= " AND p.code_income='$code_income'";
        if($tnt) $wC .= " AND a.teachingtype='$tnt'";
        $data = $this->db->query("SELECT DISTINCT p.employeeid,REPLACE(CONCAT(a.lname,', ',a.fname,' ',a.mname), 'Ã‘', 'Ñ') AS fullname, p.cutoffstart, p.cutoffend,p.code_income, p.amount
                                    FROM payroll_process_income p
                                    INNER JOIN employee a ON a.`employeeid`=p.`employeeid`
                                    WHERE p.`cutoffstart`='$cutoffstart' AND p.`cutoffend`='$cutoffend' $wC")->result_array();
        return $data;
    }

    function getEmployeeListByIncome($code_income="",$cutoffstart="", $cutoffend="", $tnt="",$eid="",$sort=""){
        $wC = "";
        ini_set('display_errors',1);
        error_reporting(-1);
        // if($code_income) $wC .= " AND p.code_income='$code_income'";
        if($tnt) $wC .= " AND a.teachingtype='$tnt'";
        $data = $this->db->query("SELECT DISTINCT p.employeeid,REPLACE(CONCAT(a.lname, ', ', a.fname, ' ', a.mname),'Ã‘','Ñ') AS fullname,p.cutoffstart,p.cutoffend,p.income,a.emp_accno FROM payroll_computed_table p 
                                    INNER JOIN employee a ON a.`employeeid`=p.`employeeid`
                                    WHERE p.`status` = '$sort' AND p.`cutoffstart`='$cutoffstart' AND p.`cutoffend`='$cutoffend' AND p.income <> '' AND a.`emp_accno` <> '' $wC")->result_array();
        // echo "SELECT DISTINCT p.employeeid,REPLACE(CONCAT(a.lname, ', ', a.fname, ' ', a.mname),'Ã‘','Ñ') AS fullname,p.cutoffstart,p.cutoffend,p.income FROM payroll_computed_table p 
        //                             INNER JOIN employee a ON a.`employeeid`=p.`employeeid`
        //                             WHERE p.`cutoffstart`='$cutoffstart' AND p.`cutoffend`='$cutoffend' AND p.income <> ''  AND p.`empAccNo` <> ''  $wC";
        return $data;
    }

    
    /*
     * Description
     */
    function showdesc($data){
        $return = array ( 
                            "employeeid"=>"EMPLOYEE ID",
                            "lname"=>"LAST NAME",
                            "fname"=>"FIRST NAME",
                            "mname"=>"MIDDLE NAME",
                            "nname"=>"NICK NAME",
                            "rank"=>"Rank",
                            
                            "emp_tin"=>"TIN #",
                            "emp_sss"=>"SSS #",
                            "emp_philhealth"=>"PHILHEALTH #",
                            "emp_pagibig"=>"PAG-IBIG #",
                            "prc"=>"PRC #",
                            "emp_accno"=>"ACCOUNT #",
                            
                            "teachingtype"=>"TYPE",
                            "isactive"=>"ACCOUNT",
                            "emptype"=>"SHIFT TYPE",
                            "empshift"=>"SHIFT SCHEDULE",
                            "dateemployed"=>"DATE EMPLOYED",
                            "dateresigned"=>"DATE RESIGNED",
                            "resigned_reason"=>"REASON",
                            
                            "officeid"=>"OFFICE",
                            "deptid"=>"DEPARTMENT",
                            "employmentstat"=>"EMPLOYEE STATUS",
                            "positionid"=>"POSITION",
                            "dateposition"=>"START DATE",
                            
                            "managementid2"=>"MANAGEMENT",
                            "deptid2"=>"DEPARTMENT",
                            "employmentstat2"=>"EMPLOYEE STATUS",
                            "positionid2"=>"POSITION",
                            "dateposition2"=>"INCLUSIVE DATES",
                            
                            "bdate"=>"DATE OF BIRTH",
                            "age"=>"AGE",
                            "bplace"=>"PLACE OF BIRTH",
                            "gender"=>"GENDER",
                            "nationalityid"=>"NATIONALITY",
                            "religionid"=>"RELIGION",
                            "civil_status"=>"CIVIL STATUS",
                            "citizenid"=>"CITIZENSHIP",
                            "personal_email"=>"PERSONAL EMAIL",
                            "mobile"=>"MOBILE NUMBER",
                            "landline"=>"LANDLINE",
                            "work_email"=>"WORK EMAIL",
                            "spouse_name"=>"SPOUSE NAME",
                            "spouse_occupation"=>"SPOUSE OCCUPATION",
                            "spouse_contact"=>"SPOUSE CONTACT #",
                            "cregion"=>"CURRENT REGION",
                            "cprovince"=>"CURRENT PROVINCE",
                            "cmunicipality"=>"CURRENT CITY/MUNICIPALITY",
                            "caddr"=>"CURRENT HOUSE#",
                            "cbrgy"=>"CURRENT BARANGAY",
                            "czipcode"=>"CURRENT ZIPCODE",
                            "pregion"=>"PERMANENT REGION",
                            "pprovince"=>"PERMANENT PROVINCE",
                            "pmunicipality"=>"PERMANENT CITY/MUNICIPALITY",
                            "paddr"=>"PERMANENT HOUSE#",
                            "pbrgy"=>"PERMANENT BARANGAY",
                            "pzipcode"=>"PERMANENT ZIPCODE",

                            "fmname"=>"NAME",
                            "fmrelation"=>"RELATION",
                            "fmdob"=>"DATE OF BIRTH",
                            
                            "father"=>"NAME",
                            "fatheroccu"=>"OCCUPATION",
                            
                            "mother"=>"NAME",
                            "motheroccu"=>"OCCUPATION",
                            
                            
                            "childname"=>"CHILD`S NAME",
                            "childbday"=>"BIRTHDAY",
                            "childage"=>"AGE",
                            
                            "passport"=>"PASSPORT #",
                            "visa"=>"VISA #",
                            "icardnum"=>"ICARD #",
                            "crnno"=>"CRN #",
                            
                            "tdname"=>"NAME",
                            "tdrelation"=>"RELATION",
                            "tdaddress"=>"ADDRESS",
                            "tdcontact"=>"CONTACT #",
                            "tdbdate"=>"BIRTHDAY",
                            "tdlegitimate"=>"LEGITIMATE",
        );
        // $return = array (   "employeeid"=>"Employee ID",
                            // "employeecode"=>"Employee Code",
                            // "emptype"=>"Leave Type",
                            // "empshift"=>"Shift Schedule",
                            // "employmentstat"=>"Employee Status",
                            // "deptid"=>"Department",
                            // "lname"=>"Last Name",
                            // "fname"=>"First Name",
                            // "mname"=>"Middle Name",
                            // "gender"=>"Gender",
                            // "mobile"=>"Mobile",
                            // "email"=>"Email",
                            // "provaddr"=>"Provincial Address",
                            // "occupation"=>"Occupation",
                            // "age"=>"Age",
                            // "bdate"=>"Birthdate",
                            // "bplace"=>"Birthplace",
                            // "dateemployed"=>"Date Employed",
                            // "civil_status"=>"Civil Status",
                            // "emp_accno"=>"Account No.",
                            // "dateposition"=>"Date Position",
                            // "assignment"=>"Assignment",
                            // "remarks"=>"Remarks",
                            // "managementid"=>"Age",
                            // "dateresigned"=>"Date Resigned",
                            // "resigned_reason"=>"Reason",
                            // "prc"=>"Prc",
                            // "passport"=>"Passport #",
                            // "visa"=>"Visa #",
                            // "crnno"=>"CRN #",
                            // "teaching"=>"Cluster Head",
                            // "teachingtype"=>"Type",
                            // "isactive"=>"Account",
                            // "leavetype"=>"Leave Type",
                            // "mother"=>"Mother",
                            // "motheroccu"=>"Mother Occupation",
                            // "father"=>"Father",
                            // "fatheroccu"=>"Father Occupation",
                            // "spouse_name"=>"Spouse",
                            // "cityaddr"=>"City Address",
                            // "positionid"=>"Position"
                        // );
        return $return[$data];
    }
    
    
    /*
     * Count Data
     */
    function countLicensedEmployee($dept='',$campus='')
    {
        $return = "";
         $whereC = '';
        if ($campus) {$whereC = "AND campusid='$campus'";}  
        $query = $this->db->query("SELECT COUNT(employeeid) as licensed FROM employee WHERE prc != '' AND deptid='$dept' $whereC");
        if ($query->num_rows() > 0) {
               $return = $query->row(0)->licensed;
           }   
        return $return;
    }

    function countHeadByEducBackground($dept='',$campus='',$type='', $isactive='', $office='')
    {
        $return='';
         $whereC = '';
         $datenow = date("Y-m-d");
        if ($campus) {$whereC .= " AND a.campusid='$campus'";}  
        if ($type) {$whereC .= " AND b.educ_level='$type'";} 
        if ($office) {$whereC .= " AND a.deptid='$office'";} 
        if($isactive != ""){
          if($isactive=="1"){
            $whereC .= " AND (('$datenow' < a.dateresigned2 OR a.dateresigned2 = '0000-00-00' OR a.dateresigned2 = '1970-01-01' OR a.dateresigned2 IS NULL) AND a.isactive ='1')";
          }
          if($isactive=="0"){
            $whereC .= " AND (('$datenow' >= a.dateresigned2 AND a.dateresigned2 IS NOT NULL AND a.dateresigned2 <> '0000-00-00' AND a.dateresigned2 <> '1970-01-01' ) OR a.isactive = '0')";
          }
          if(is_null($isactive)) $whereC .= " AND a.isactive = '1' AND (a.dateresigned2 = '0000-00-00' OR a.dateresigned2 = '1970-01-01' OR a.dateresigned2 IS NULL)";
        }

        // $query = $this->db->query("SELECT count(b.educ_level) as count FROM employee a INNER JOIN employee_education b ON a.employeeid = b.employeeid WHERE a.office = '$deptid' AND b.educ_level = '$type' $whereClause");
        $query = $this->db->query("SELECT count(educ_level) as count , a.`employeeid`
            FROM employee a
            INNER JOIN employee_education b ON b.`employeeid` = a.`employeeid` AND b.`educ_level` = (SELECT MAX(educ_level) FROM employee_education WHERE employeeid = a.`employeeid` )
            WHERE b.`educ_level` != '' AND a.`office`='$dept' $whereC");


        
        if ($query->num_rows() > 0) 
        {
            $return = $query->row(0)->count;
        }        
        return $return; 
    }
    // function countHeadByEducBackground($dept='',$campus='',$type='')
    // {
    //     $return='';
    //      $whereC = '';
    //     if ($campus) {$whereC = "AND campusid='$campus'";}  
    //     if ($type == "1") {
    //         $query = $this->db->query("SELECT   count(educ_level) as count , a.`employeeid`
    //         FROM employee a
    //         LEFT JOIN employee_education b ON b.`employeeid` = a.`employeeid` AND b.`educ_level` = (SELECT MAX(educ_level) FROM employee_education WHERE employeeid = a.`employeeid` )
    //         WHERE b.`educ_level` != '' AND a.`deptid`='$dept' AND b.`educ_level`='1' $whereC");
    //             }
    //             else if ($type == "2") {
    //                 $query = $this->db->query("SELECT   count(educ_level)  as count, a.`employeeid`
    //         FROM employee a
    //         LEFT JOIN employee_education b ON b.`employeeid` = a.`employeeid` AND b.`educ_level` = (SELECT MAX(educ_level) FROM employee_education WHERE employeeid = a.`employeeid` )
    //         WHERE b.`educ_level` != '' AND a.`deptid`='$dept' AND b.`educ_level`='2' $whereC");
    //             }
    //             else{
    //                 $query = $this->db->query("SELECT   count(educ_level)  as count, a.`employeeid`
    //         FROM employee a
    //         LEFT JOIN employee_education b ON b.`employeeid` = a.`employeeid` AND b.`educ_level` = (SELECT MAX(educ_level) FROM employee_education WHERE employeeid = a.`employeeid` )
    //         WHERE b.`educ_level` != '' AND a.`deptid`='$dept' AND b.`educ_level`='234' $whereC");
    //     }
        
    //     if ($query->num_rows() > 0) 
    //     {
    //         $return = $query->row(0)->count;
    //     }        
    //     return $return; 
    // }
    function countDeptHeads($dept=""){
        $return = "";
        $whereC = '';
              
        
        $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM code_office WHERE code='$dept' AND head <> '' ");
        if($query->num_rows() > 0)  $return = $query->row(0)->thead;
        return ($return ? $return : "");

    }
    function countHeads($dept=""){
        $return = "";
        $query = $this->db->query("SELECT DISTINCT COUNT(*) AS thead FROM code_managementlevel WHERE managementid='$dept'");
        if($query->num_rows() > 0)  $return = $query->row(0)->thead;
        return ($return ? $return : "");
    }
    // function countDeptESTAT($dept="",$date="",$type="",$campusid='',$isactive=''){
    //     $return = "";
    //     $whereC = '';
    //     if ($campusid) {$whereC .= "AND campusid='$campusid'";}   
    //     if ($isactive!="") {$whereC .= "AND isactive='$isactive'";}   
    //     if($type == "REGULAR")
    //         $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE deptid='$dept' AND employmentstat = 'REG' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");
    //     else if($type == "PROBITIONARY")
    //         $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE deptid='$dept' AND employmentstat = 'PROB' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");
    //      else if($type == "FULLTIME")
    //         $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE deptid='$dept' AND employmentstat = 'FULL' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");
    //      else if($type == "CASUAL")
    //         $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE deptid='$dept' AND employmentstat = 'CAS' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");

    //     else
    //         $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE deptid='$dept' AND employmentstat = 'CON' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");
    //     if($query->num_rows() > 0)  $return = $query->row(0)->thead;
    //     return ($return ? $return : "");


    // }

    function countDeptESTAT($office="",$date="",$type="",$campusid='',$isactive='',$tnt ='', $deptid=''){
        $return = "";
        $whereC = '';
        $datenow = date("Y-m-d");
        if($tnt) $whereC .= " AND teachingtype = '$tnt'";
        if ($campusid) {$whereC .= " AND campusid='$campusid'";}   
        // if ($isactive!="") {$whereC .= "AND isactive='$isactive'";}   
        if($isactive != "all"){
          if($isactive=="1"){
            $whereC .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="2"){
            $whereC .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $whereC .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        if ($office) $whereC .= " AND office='$office'";
        if($deptid) $whereC .= " AND deptid='$deptid'";
            
        
        $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE employmentstat = '$type' $whereC");
        // echo "<pre>"; print_r($this->db->last_query()); die;
        if($query->num_rows() > 0)  $return = $query->row(0)->thead;
        return ($return ? $return : "");


    }

    function countDeptESTATNew($dept="",$type="",$campus='',$active='', $tnt='', $company='',$description=''){
        $return = "";
        $datenow = date("Y-m-d");
        $whereC = ''; 
        if($active != "all"){
          if($active=="1"){
            $whereC .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($active=="2"){
            $whereC .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($active)){ $whereC .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";}
        }   
        if ($tnt) {$whereC .= " AND a.teachingtype='$tnt'";}    
        // if ($company) {$whereC .= " AND a.company_campus='$company'";}  
        // $usercampus = $this->extras->getCampusUser();
        // if($campus && $campus!="All"){
        //   $whereC .= " AND campusid = '$campus'";
        // }else{
        //     if($usercampus){
        //       $usercampus .= ",All";
        //       $whereC .= " AND FIND_IN_SET (campusid,'$usercampus') ";
        //     }
        // }
        // $query3 = $this->db->query("SELECT code FROM code_campus")->result_array();
        // foreach ($query3 as $key => $value) {
        //     $query2 = $this->db->query("SELECT dhead FROM campus_office WHERE base_code = '$deptid' AND campus = '$value'");
        //     $headid = $query2->row(0)->dhead;
        //     $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE office='$dept' AND (employmentstat = '$type' OR employmentstat = '$description') AND employeeid != '$headid' $whereC");
        //     // if($query->num_rows() > 0)  $return = $query->row(0)->thead;
        //     // return ($return ? $return : "");
        //     return $query->row(0)->thead;
        // }
        $query = $this->db->query("SELECT COUNT(a.employeeid) AS thead FROM employee a WHERE a.office='$dept' AND (a.employmentstat = '$type' OR a.employmentstat = '$description') $whereC");
        return $query->row(0)->thead;
    }

    function countnoofficeandnoemploymentstat($dept="",$active='', $tnt='',$type="",$campus='', $company='',$description=''){
        $return = "";
        $whereC = '';
        $datenow = date("Y-m-d");
        if ($dept) {
            $whereC .= " AND a.office='$dept'";
        }else{ $whereC .= " AND (a.office IS NULL OR a.office = '')"; }
        if($active != "all"){
          if($active=="1"){
            $whereC .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($active=="2"){
            $whereC .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($active)){ $whereC .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";}
        }  
        if ($tnt) {$whereC .= " AND a.teachingtype='$tnt'";}    
        // if ($company) {$whereC .= " AND a.company_campus='$company'";}

        // $query3 = $this->db->query("SELECT code FROM code_campus")->result_array();
        // foreach ($query3 as $key => $value) {
        //     $query2 = $this->db->query("SELECT dhead FROM campus_office WHERE base_code = '$deptid' AND campus = '$value'");
        //     $headid = $query2->row(0)->dhead;
        //     $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE office='$dept' AND (employmentstat = '$type' OR employmentstat = '$description') AND employeeid != '$headid' $whereC");
        //     // if($query->num_rows() > 0)  $return = $query->row(0)->thead;
        //     // return ($return ? $return : "");
        //     return $query->row(0)->thead;
        // }
        // $usercampus = $this->extras->getCampusUser();
        // if($campus && $campus!="All"){
        //   $whereC .= " AND campusid = '$campus'";
        // }else{
        //     if($usercampus){
        //       $usercampus .= ",All";
        //       $whereC .= " AND FIND_IN_SET (campusid,'$usercampus') ";
        //     }
        // }
        $q = $this->db->query("SELECT code, description FROM code_status");
        $empStatusAll = '';
        $empCount = $q->num_rows();
        $c = 0;
        foreach ($q->result() as $key => $value) {
            $c = $c + 1;
            if ($c != $empCount) {
                $empStatusAll .= " employmentstat != '$value->code' AND employmentstat != '$value->description' AND";
            }
            else{
                $empStatusAll .= " employmentstat != '$value->code' AND employmentstat != '$value->description'";
            }
            
        }
        $query = $this->db->query("SELECT COUNT(a.employeeid) AS thead FROM employee a WHERE (a.employmentstat = '' OR a.employmentstat IS NULL) $whereC");
        return $query->row(0)->thead;
        // return $empStatusAll;
    }

    function countDeptDivision($managementid="",$date="",$type="",$campusid=''){
        $return = ""; $whereC = '';
       $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE managementid='$managementid' AND employmentstat = '$type' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");
       if($query->num_rows() > 0)  $return = $query->row(0)->thead;
       return ($return ? $return : "");
        if($query->num_rows() > 0)  $return = $query->row(0)->thead;
        return ($return ? $return : "");
    }


    // function countDeptDivision($managementid="",$date="",$type="",$campusid=''){
    //     $return = ""; $whereC = '';
        
    //    if($type == "permanent")
    //        $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE managementid='$managementid' AND employmentstat = 'PER' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");
    //    else if($type == "probitionary")
    //        $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE managementid='$managementid' AND employmentstat = 'PROB' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");
    //    else if($type == "full")
    //        $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE managementid='$managementid' AND employmentstat = 'FULL' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");
    //    else if($type == "casual")
    //        $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE managementid='$managementid' AND employmentstat = 'CAS' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");
    //    else
    //        $query = $this->db->query("SELECT DISTINCT COUNT(*) as thead FROM employee WHERE managementid='$managementid' AND employmentstat = 'CON' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date') $whereC");
    //    if($query->num_rows() > 0)  $return = $query->row(0)->thead;
    //    return ($return ? $return : "");
    //     if($query->num_rows() > 0)  $return = $query->row(0)->thead;
    //     return ($return ? $return : "");
    // }

    function countPRC($dept="",$date="",$type="")
    {
        $return = "";
        if($type == "prc")
            $query = $this->db->query("SELECT DISTINCT COUNT(*) as prc FROM employee WHERE deptid='$dept' AND prc != '' AND (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00' OR DATE_FORMAT(dateresigned,'%Y-%m') >= '$date')");
        if ($query->num_rows() > 0 ) {
            $return = $query->row(0)->prc;
        }
        return ($return?$return:"");
    }
    function showEmpDetailCols($desc=''){
        
        $return = "";
        if($desc == "General Information")
        {
            $arrcol = array (   "employeeid","lname","fname","mname","nname","rank" );
        }
        else if($desc == "Identification Numbers")
        {
            $arrcol = array (   "emp_tin","emp_sss","emp_philhealth","emp_pagibig","prc","emp_accno" );
        }
        else if($desc == "Employee Information")
        {
            $arrcol = array (   "teachingtype","isactive","emptype","empshift","dateemployed","dateresigned","resigned_reason" );
        }
        else if($desc == "Employment Details")
        {
            $arrcol = array (   "officeid","deptid","employmentstat","positionid","dateposition" );
        }
        else if($desc == "Employment Status History")
        {
            $arrcol = array (   "managementid2","deptid2","employmentstat2","positionid2","dateposition2" );
        }
        else if($desc == "Personal Information")
        {
            $arrcol = array (   "bdate","age","bplace","gender","nationalityid","religionid","civil_status","citizenid","mobile","landline","work_email","spouse_name","spouse_occupation","spouse_contact","cregion","cprovince","cmunicipality","caddr","cbrgy","czipcode","pregion","pprovince","pmunicipality","paddr","pbrgy","pzipcode");
        }
        else if($desc == "Family Members")
        {
            $arrcol = array ("fmname","fmrelation", "fmdob");
        }
        else if($desc == "Mother")
        {
            $arrcol = array ("mother","motheroccu");
        }
        else if($desc == "Spouse")
        {
            $arrcol = array ( "spouse_name","occupation");
        }
        else if($desc == "Number of Children")
        {
            $arrcol = array (   "childname","childbday","childage");
        }
        else if($desc == "Immigration Details")
        {
            $arrcol = array (   "passport","visa","icardnum","crnno");
        }
        else if($desc == "Tax Dependents")
        {
            $arrcol = array (   "tdname","tdrelation","tdaddress","tdcontact","tdbdate","tdlegitimate");
        }
        // $arrcol = array (   "employeeid","fname","lname","mname","employeecode","passport","visa","crnno","prc",
                            // "teaching","teachingtype","leavetype","emptype","empshift","positionid","dateposition",
                            // "assignment","remarks","managementid","deptid","employmentstat","dateemployed","dateresigned","resigned_reason","bdate","bplace","age","civil_status",
                            // "spouse_name","occupation","gender","mobile","cityaddr","provaddr",
                            // "emp_accno",
                            // "isactive",
                            // "mother",
                            // "motheroccu",
                            // "father",
                            // "fatheroccu"
                        // );
        
        #$query = $this->db->query("SHOW COLUMNS FROM employee WHERE !FIND_IN_SET(FIELD,'title,citytelno,maxregular,maxparttime,numberofdependents,income_base,emp_sss,emp_tin,emp_philhealth,emp_pagibig,emp_peraa,emp_medicare,tax_status,positionid,managementid,citizenid,religionid,nationalityid,permanentaddr,cp_name,cp_relation,cp_address,cp_mobile,cp_telno,isFlexi,hospitalized,hospitalizedtxt,operation,operationtxt,operationdate,medhistory,medhistorytxt,medconditions,createdby,createdon,icardnum');")->result();
        $return .=  '<div class="col-md-6" style="margin-bottom:5%"><span><strong>'.$desc.'</strong></span><br />';
        foreach($arrcol as $row){
            #$col = $row->Field;
            $col = $row;
            $return .=  '
                                <div class="col-md">
                                    <input type="checkbox" class="selectall" name="edata" id="edata" value="'.$col.'" > '.$this->showdesc($col).'
                                </div>
                        ';
        }
        $return .=  '</div>';
        return $return;
    }
    
    function rdc($division="",$department="",$cutoff="",$deduction="",$isRDCForm="", $sort=""){
        $wC ="";
        $orderby = "";
        if($division) {if($wC) { $wC .="AND ";} $wC .= "b.managementid = '$division' ";}
        if($department) {if($wC) { $wC .="AND ";} $wC .= "b.deptid = '$department' ";}
   //      if($cutoff) {
            // // $cutoff = date('m', strtotime($cutoff));
   //          // if($wC) { $wC .="AND ";} $wC .= "MONTH(a.cutoffstart) = '$cutoff' AND MONTH(a.cutoffend) = '$cutoff' ";
            // $c = explode("~~",$cutoff);
            // $c1 = date('m', strtotime($c[0]));
            //  if($wC) { $wC .="AND ";} $wC .= "MONTH(a.cutoffstart) = '$c1' AND MONTH(a.cutoffend) = '$c1' AND YEAR(a.cutoffstart) = '".$c[1]."' AND YEAR(a.cutoffend) = '".$c[1]."'";
   //      }
   //      if($deduction) {if($wC) { $wC .="AND ";} $wC .= "a.code_deduct = '$deduction' ";}
        
        if($wC) $wC = "WHERE " . $wC;
        $whereClause = "";
        if($isRDCForm){
            $exp_co = explode("~~", $cutoff);
            $cutoffstart = $exp_co[1] .'-'. date("m", strtotime($exp_co[0])) .'-%';
            $whereClause = "a.`cutoffstart` LIKE '$cutoffstart' AND a.fixeddeduc <> ''";
        }
        if($sort == "department") $orderby = "b.office";
        else  $orderby = "fullname";

        if($isRDCForm){ $query = $this->db->query("SELECT a.fixeddeduc,a.employeeid,a.cutoffstart,a.cutoffend,CONCAT(b.lname, ', ', b.fname, ' ', b.mname) AS fullname,
                                  b.emp_sss,b.emp_pagibig,b.emp_philhealth,b.emp_tin,a.withholdingtax,b.office
                                FROM payroll_computed_table a 
                                  INNER JOIN employee b ON a.employeeid = b.employeeid
                                WHERE $whereClause 
                                $wC GROUP BY employeeid ORDER BY $orderby")->result();

        }else{
            $query = $this->db->query("SELECT a.fixeddeduc,a.employeeid,a.cutoffstart,a.cutoffend,CONCAT(b.lname, ', ', b.fname, ' ', b.mname) AS fullname,
                                  b.emp_sss,b.emp_pagibig,b.emp_philhealth,b.emp_tin,b.office,SUM(a.withholdingtax) as withholdingtax
                                FROM payroll_computed_table a 
                                  INNER JOIN employee b ON a.employeeid = b.employeeid
                                WHERE DATE_FORMAT(a.`cutoffstart`, '%M~~%Y') = '$cutoff' AND DATE_FORMAT(a.`cutoffstart`, '%M~~%Y') = '$cutoff' 
                                 GROUP BY employeeid ORDER BY $orderby");
        }
        // $query = $this->db->query("SELECT a.*,CONCAT(b.lname,', ',b.fname,' ',b.mname) AS fullname,b.emp_sss,b.`emp_pagibig`,b.`emp_philhealth`
        // FROM payroll_process_contribution_collection a
        // INNER JOIN employee b ON a.`employeeid` = b.`employeeid`
        // $wC  ORDER BY employeeid")->result();
        
        return $query;
    }
    
    function empstathistoryquery($empid,$col)
    {
        $empstathistory = array("managementid2","deptid2","employmentstat2","positionid2","job_level2","dateposition2");
        $excol  = explode(',',$col);
        $a = "";
        foreach($excol as $str){
            if($a) $a .= ",";
            if($str == "managementid2")          $str = " IFNULL( b.description,'') as managementid2 ";
            if($str == "deptid2")           $str = " IFNULL( c.description,'') as deptid2 ";
            if($str == "employmentstat2")    $str = " IFNULL( d.description,'') as employmentstat2 ";
            if($str == "positionid2")            $str = " IFNULL( e.description,'') as positionid2 ";
            if($str == "dateposition2") $str = "IFNULL( a.dateposition,'') as dateposition2";
            $a .= $str; 
        }
            $return=$this->db->query("SELECT {$a}
                FROM employee_employment_status_history a
                LEFT JOIN code_managementlevel b ON a.`managementid`=b.`managementid`
                LEFT JOIN code_office c ON a.`deptid`=c.`code`
                LEFT JOIN code_status d ON a.`employeestat`=d.`code`
                LEFT JOIN code_position e ON a.`positionid`=e.`positionid`
                WHERE employeeid = '{$empid}'
                ORDER BY `timestamp` DESC ")->result();
                            
        return $return;
    }
    
    function childrenquery($empid,$col)
    {
        $children = array("childname","childbday","childage");
        $excol  = explode(',',$col);
        $b = "";
        foreach($excol as $str){
            if($b) $b .= ",";
            if($str == "childname")          $str = " IFNULL(name,'') as childname ";
            if($str == "childbday")           $str = " IFNULL(birthdate,'') as childbday ";
            if($str == "childage")      $str = " IFNULL(age,'') as childage ";
            $b .= $str; 
        }
            $return=$this->db->query("select {$b} from employee_children WHERE employeeid = '{$empid}'")->result();
                            
        return($return);
    }
    function familyquery($empid,$col)
    {
        $family = array("childname","childbday","childage");
        $excol  = explode(',',$col);
        $b = "";
        foreach($excol as $str){
            if($b) $b .= ",";
            if($str == "fmname")          $str = " name as fmname ";
            if($str == "fmrelation")           $str = " relation as fmrelation ";
            if($str == "fmdob")      $str = " bdate as fmdob ";
            $b .= $str; 
        }
            $return=$this->db->query("select {$b} from employee_family WHERE employeeid = '{$empid}'")->result();
                            
        return($return);
    }
    
    function taxDependentsquery($empid,$col)
    {
        $taxDependents = array (   "tdname","tdrelation","tdaddress","tdcontact","tdbdate","tdlegitimate");
        $excol  = explode(',',$col);
        $c = "";
        foreach($excol as $str){
            if($c) $c .= ",";
            if($str == "tdname")          $str = " IFNULL(legitimate_name,'') as tdname ";
            if($str == "tdrelation")           $str = " IFNULL(legitimate_relation,'') as tdrelation ";
            if($str == "tdaddress")           $str = " IFNULL(legitimate_address,'') as tdaddress ";
            if($str == "tdcontact")           $str = " IFNULL(legitimate_contactno,'') as tdcontact ";
            if($str == "tdbdate")           $str = " IFNULL(legitimate_bdate,'') as tdbdate ";
            if($str == "tdlegitimate")           $str = " IFNULL(legit,'') as tdlegitimate ";
            $c .= $str;  
        }
            $return=$this->db->query("select {$c} from employee_legitimate_relations WHERE employeeid = '{$empid}'")->result();
                            
        return($return);
    }
    
    # for ica-hyperion 21578
    # by justin (with e)
    function getDeptHead($col="", $code=""){
        $head = "";
        $res = $this->db->query("SELECT $col FROM code_office WHERE code='$code'");
        if($res->num_rows() > 0) $head = $res->row(0)->$col;
        return $head;
    }

    function getVPFinanceHEAD($empid)
    {
         $VPFullname="";
        $query = $this->db->query("SELECT CONCAT( `fname`,' ',SUBSTR(mname,1,1),'. ',`lname`) AS fullname  FROM employee  WHERE employeeid='$empid' LIMIT 1")->result();
        foreach ($query as $data) {
            $VPFullname = $data->fullname;
        }
        return $VPFullname;
    }

    function MP2VoluntaryContributionPerMY($pfrom,$pto,$pyearfrom,$pyearto,$empid, $sort_by)
    {
        $wC = $orderby = "";
        if($sort_by == "department") $orderby = "ORDER BY b.office";
        else $orderby = "ORDER BY b.lname"; 
        if ($empid) {
            $wC .= " AND a.employeeid='$empid'";
        }
        $query =  $this->db->query("SELECT a.id,CONCAT(b.fname,' ',b.mname,' ',b.lname) AS fullname, a.cutoffstart, a.cutoffend, a.otherdeduc, a.employeeid, c.or_number, c.datepaid FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid INNER JOIN payroll_computed_ee_er c ON a.id = c.base_id WHERE a.otherdeduc <> '' AND MONTH(a.cutoffstart) BETWEEN '$pfrom' AND '$pto' AND MONTH(a.cutoffend) BETWEEN '$pfrom' AND '$pto' AND YEAR(a.cutoffstart) BETWEEN '$pyearfrom' AND '$pyearto' AND YEAR(a.cutoffend) BETWEEN '$pyearfrom' AND '$pyearto' AND a.status='PROCESSED' AND a.bank <> '' AND c.`code_deduction` = 'PAGIBIG' $wC $orderby")->result();

        return $query;
    }

    function SSSContributionPerMY($pfrom,$pto,$pyearfrom,$pyearto,$empid,$sort_by='')
    {
        $wC = $orderby = "";
        if($sort_by == "department") $orderby = "ORDER BY b.office";
        else $orderby = "ORDER BY b.lname";

        if ($empid) {
            $wC .= " AND a.employeeid='$empid'";
        }
        $query =  $this->db->query("SELECT a.id,CONCAT(b.fname,' ',b.mname,' ',b.lname) AS fullname,b.`emp_sss` AS sssnumber ,b.employeeid,a.fixeddeduc,a.cutoffstart,a.cutoffend,c.or_number,c.datepaid, c.ee, c.er, c.ec FROM payroll_computed_table a INNER JOIN employee b ON(a.employeeid = b.employeeid) INNER JOIN payroll_computed_ee_er c  ON (a.`id` = c.`base_id`) WHERE fixeddeduc <> '' AND MONTH(cutoffstart) BETWEEN '$pfrom' AND '$pto' AND MONTH(cutoffend) BETWEEN '$pfrom' AND '$pto' AND YEAR(cutoffstart) BETWEEN '$pyearfrom' AND '$pyearto' AND YEAR(cutoffend) BETWEEN '$pyearfrom' AND '$pyearto' AND a.status='PROCESSED' AND a.bank <> '' AND c.`code_deduction` = 'SSS' $wC $orderby")->result();

        return $query;
    }

    function getSSSContribution($id,$sss,$type)
    {
        $return = "";
        if ($type == "ec") {
            $query = $this->db->query("SELECT b.`EE`,b.`EC` ,b.`ER` FROM payroll_computed_table a INNER JOIN payroll_computed_ee_er b ON (a.`id` = b.`base_id`) WHERE b.`base_id`='$id' AND b.`code_deduction` ='SSS' LIMIT 1 ")->result();
            foreach ($query as $res) {
                $return = $res->EC?$res->EC:"0.00";
            }
         
        }
        else if ($type == "totalsss") {
            $query = $this->db->query("SELECT b.`EE`,b.`EC` ,b.`ER` FROM payroll_computed_table a INNER JOIN payroll_computed_ee_er b ON (a.`id` = b.`base_id`) WHERE b.`base_id`='$id' AND b.`code_deduction` ='SSS'  LIMIT 1 ")->result();
            foreach ($query as $res) {
                $return = $res->EE + $res->ER?$res->EE + $res->ER:"0.00";
            }
        }    
        return $return?$return:"0.00";
       
    }

    function philhealthContributionPerMY($pfrom,$pto,$pyearfrom,$pyearto,$empid, $sort_by)
    {
        $wC = $orderby = "";
        if($sort_by == "department") $orderby = "ORDER BY b.office";
        else $orderby = "ORDER BY b.lname";
        if ($empid) {
            $wC .= " AND a.employeeid='$empid'";
        }
     $query =  $this->db->query("SELECT a.id,CONCAT(b.fname,' ',b.mname,' ',b.lname) AS fullname,b.`emp_philhealth` AS philhealthnumber ,b.employeeid,a.fixeddeduc,a.cutoffstart,a.cutoffend,c.or_number,c.datepaid, c.ee,c.er,c.ec FROM payroll_computed_table a INNER JOIN employee b ON(a.employeeid = b.employeeid) INNER JOIN payroll_computed_ee_er c  ON (a.`id` = c.`base_id`) WHERE fixeddeduc <> '' AND MONTH(cutoffstart) BETWEEN '$pfrom' AND '$pto' AND MONTH(cutoffend) BETWEEN '$pfrom' AND '$pto' AND YEAR(cutoffstart) BETWEEN '$pyearfrom' AND '$pyearto' AND YEAR(cutoffend) BETWEEN '$pyearfrom' AND '$pyearto' AND a.status='PROCESSED' AND a.bank <> '' AND c.`code_deduction` = 'PHILHEALTH' $wC $orderby")->result();

        return $query;
    }

    function getphilhealthContribution($id,$philhealth,$type)
    {
        $return = "";
        if ($type == "er") {
            $query = $this->db->query("SELECT b.`EE`,b.`EC` ,b.`ER` FROM payroll_computed_table a INNER JOIN payroll_computed_ee_er b ON (a.`id` = b.`base_id`) WHERE b.`base_id`='$id' AND b.`code_deduction` ='PHILHEALTH' LIMIT 1 ")->result();
            foreach ($query as $res) {
                $return = $res->ER?$res->ER:"0.00";
            }
        }
        else if ($type == "totalphilhealth") {
            $query = $this->db->query("SELECT b.`EE`,b.`EC` ,b.`ER` FROM payroll_computed_table a INNER JOIN payroll_computed_ee_er b ON (a.`id` = b.`base_id`) WHERE b.`base_id`='$id' AND b.`code_deduction` ='PHILHEALTH' LIMIT 1 ")->result();
            foreach ($query as $res) {
                $return = $res->EE ? $res->EE :"0.00";
            }
        }    

        return $return?$return:"0.00";
    }
    function getpagibigContribution($id,$philhealth,$type){
        $return = "";
        if ($type == "er") {
            $query = $this->db->query("SELECT b.`EE`,b.`EC` ,b.`ER` FROM payroll_computed_table a INNER JOIN payroll_computed_ee_er b ON (a.`id` = b.`base_id`) WHERE b.`base_id`='$id' AND b.`code_deduction` ='PAGIBIG' LIMIT 1 ")->result();
            foreach ($query as $res) {
                $return = $res->ER?$res->ER:"0.00";
            }
        }
        else if ($type == "totalpagibig") {
            $query = $this->db->query("SELECT b.`EE`,b.`EC` ,b.`ER` FROM payroll_computed_table a INNER JOIN payroll_computed_ee_er b ON (a.`id` = b.`base_id`) WHERE b.`base_id`='$id' AND b.`code_deduction` ='PAGIBIG' LIMIT 1 ")->result();
            foreach ($query as $res) {
                $return = $res->EE ? $res->EE :"0.00";
            }
        }    

        return $return?$return:"0.00";

    }
    function hdmfContributionPerMY($pfrom,$pto,$pyearfrom,$pyearto,$empid,$sort_by)
    {
        $wC = $orderby = "";
        if($sort_by == "department") $orderby = "ORDER BY b.office";
        else $orderby = "ORDER BY b.lname"; 
        if ($empid) {
            $wC .= " AND a.employeeid='$empid'";
        }
        $query =  $this->db->query("SELECT a.id,CONCAT(b.fname,' ',b.mname,' ',b.lname) AS fullname,b.`emp_pagibig` AS pagibignumber ,b.employeeid,a.fixeddeduc,a.cutoffstart,a.cutoffend,c.or_number,c.datepaid, c.ee, c.ec, c.er FROM payroll_computed_table a INNER JOIN employee b ON(a.employeeid = b.employeeid) INNER JOIN payroll_computed_ee_er c  ON (a.`id` = c.`base_id`) WHERE fixeddeduc <> '' AND MONTH(cutoffstart) BETWEEN '$pfrom' AND '$pto' AND MONTH(cutoffend) BETWEEN '$pfrom' AND '$pto' AND YEAR(cutoffstart) BETWEEN '$pyearfrom' AND '$pyearto' AND YEAR(cutoffend) BETWEEN '$pyearfrom' AND '$pyearto' AND a.status='PROCESSED' AND a.bank <> '' AND c.`code_deduction` = 'PAGIBIG' $wC $orderby")->result();

        return $query;
        
    }
    # end for ica-hyperion 21578

    # for ica-hyperion 21655
    # by justin (with e)
    function getRDCEmpList($division = '', $department = '', $cutoff, $status ='', $office="", $tnt=""){
        $whereClause  = ($department) ? "AND b.deptid='$department' " : "";
        $whereClause .= ($division) ? "AND b.managementid='$division'" : "";
        $whereClause .= ($office) ? "AND b.office='$office'" : "";
        $whereClause .= ($tnt) ? "AND b.teachingtype='$tnt'" : "";

        
        $q_emplist = $this->db->query("SELECT CONCAT(b.lname, ', ', b.fname, ' ', b.mname) AS fullname, c.code AS office_code,c.description AS office_desc, e.code AS dept_code,e.description AS dept_desc, d.code AS campus_code, d.description AS campus_desc, b.emp_sss, b.emp_pagibig, b.emp_philhealth, b.emp_tin, b.emp_peraa, a.*
                                        FROM payroll_computed_table a
                                        INNER JOIN employee b ON b.employeeid = a.employeeid
                                        LEFT JOIN code_office c ON c.code = b.office
                                        LEFT JOIN code_campus d ON d.code = b.campusid
                                        LEFT JOIN code_department e ON e.code = b.deptid
                                        WHERE a.status = '$status' AND DATE_FORMAT(a.cutoffstart, '%Y-%m') = '$cutoff' AND DATE_FORMAT(a.cutoffend, '%Y-%m') = '$cutoff' $whereClause
                                        ORDER BY deptid,campusid,fullname;")->result();
        
        return $q_emplist;
    }

    function findRDCEEER($pct_id, $code_deduction){

        $q_ee_er = $this->db->query("SELECT * FROM payroll_computed_ee_er WHERE base_id='$pct_id' AND code_deduction='$code_deduction';")->result();
        return $q_ee_er;
    }
    # end for ica-hyperion 21655

    function getIncomeDeminimiss(){
        $this->db->from("payroll_income_config");
        $this->db->where('incomeType', 'deminimiss');
        $query = $this->db->get();
        return $query->result();
    }
    function getIncome(){
        $this->db->from("payroll_income_config");
        $query = $this->db->get();
        return $query->result();
    }
    function getDeductionConfig(){
        $this->db->from("payroll_deduction_config");
        $query = $this->db->get();
        return $query->result();
    }
 
    function getOtherIncome(){
        $this->db->from("payroll_income_config");
        $this->db->where('incomeType', 'other');
        $this->db->or_where('incomeType', '');
        $query = $this->db->get();
        return $query->result();
    }

    function save_payrollregister_filter($data, $type){
        $code = "";
        if(isset($data['selectalltdeminimis'])){
            unset($data['selectalltdeminimis']);
        }
        foreach($data as $key => $row){
            $code .= $key.",";
        }
        $this->db->query("INSERT INTO payroll_register_history (code, filter_type) VALUES ('$code','$type') ");
    }

    function getFilterHistory($filter_type){
        $query = $this->db->query("SELECT code FROM payroll_register_history WHERE filter_type = '$filter_type' ORDER BY timestamp DESC LIMIT 1");
        if($query->num_rows() > 0) return $this->db->query("SELECT code FROM payroll_register_history WHERE filter_type = '$filter_type' ORDER BY timestamp DESC LIMIT 1")->row()->code;
        else return false;
    }

    function getEmployeeList($employeeid="", $campusid="", $deptid="", $teachingtype="", $is_show_resign=false){
        $where_clause = "";

        if($employeeid)     $where_clause .= "a.employeeid='$employeeid' ";
        if($campusid)       $where_clause .= (($where_clause) ? "AND " : ""). "a.campusid='$campusid' ";
        if($deptid)         $where_clause .= (($where_clause) ? "AND " : ""). "a.deptid='$deptid' ";
        if($teachingtype)   $where_clause .= (($where_clause) ? "AND " : ""). "a.teachingtype='$teachingtype' ";
        if(!$is_show_resign) $where_clause .= (($where_clause) ? "AND " : ""). "(a.dateresigned = '1970-01-01' OR a.dateresigned IS NULL OR a.dateresigned = '0000-00-00') ";

        $where_clause = (($where_clause) ? "WHERE " : "") ."". $where_clause;
        return $this->db->query("SELECT a.*, CONCAT(a.lname, ', ', a.fname, ' ', a.mname) AS fullname, b.description AS dept_desc, c.description AS campus_desc
                                 FROM employee a
                                 LEFT JOIN code_office b ON b.code = a.deptid
                                 LEFT JOIN code_campus c ON c.code = a.campusid
                                 $where_clause
                                 ORDER BY fullname
                                 ");
    }

    function getDateIncluded($dfrom, $dto){
        $date_list = array();
        $base_date = $dfrom;

        $no_days = $this->dateDifference($dfrom, $dto);
        $date_list[] = $base_date;
        if($no_days > 0){
            for($i = 1; $i <= $no_days; $i++ ){
                $date_list[] = date('Y-m-d', strtotime($base_date. " + $i days"));
            }
        }

        return $date_list;
    }

    function dateDifference($from_date , $to_date , $differenceFormat = '%a' ){
        $start_date = date_create($from_date);
        $end_date   = date_create($to_date);
        
        $interval   = date_diff($start_date, $end_date);
        
        return $interval->format($differenceFormat);
    }

    function getMonthList($date_list){
        $list = array();

        foreach ($date_list as $date) $list[date("m", strtotime($date))] = date("F Y", strtotime($date));

        return $list;
    }

    function convertTimeToNumber($value, $revert=false){
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

    function getAbsentOBCorrectionReport($dfrom, $dto, $type, $status=''){
        $type_arr = array("ABSENT", "DIRECT", "CORRECTION");
        $data = array();
        $datenow = date("Y-m-d");
        if(!$type){
            foreach ($type_arr as $type) {
                $where_clause = ($type == "ABSENT") ? "a.leavetype = '$type'" : "a.othertype = '$type'";
                $table = ($type == "ABSENT") ? "leave_request" : "ob_request";
                if($status != ""){
                  if($status=="1"){
                    $where_clause .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
                  }
                  if($status=="0"){
                    $where_clause .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
                  }
                  if(is_null($status)) $where_clause .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
                }

                $data[$type] = $this->db->query("SELECT a.*, CONCAT(b.lname, ', ', b.fname, ' ', b.mname) AS fullname, c.description AS position_desc, b.deptid, b.office
                                                 FROM $table a
                                                 LEFT JOIN employee b ON b.employeeid = a.employeeid
                                                 LEFT JOIN code_position c ON c.positionid = b.positionid
                                                 WHERE $where_clause AND ((a.fromdate BETWEEN '$dfrom' AND '$dto') AND (a.todate BETWEEN '$dfrom' AND '$dto')) ORDER BY fullname")->result_array();
            }
        }else{
            $where_clause = ($type == "ABSENT") ? "a.leavetype = '$type'" : "a.othertype = '$type'";
            $table = ($type == "ABSENT") ? "leave_request" : "ob_request";
            if($status != ""){
                  if($status=="1"){
                    $where_clause .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
                  }
                  if($status=="0"){
                    $where_clause .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
                  }
                  if(is_null($status)) $where_clause .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
                }

            $data[$type] = $this->db->query("SELECT a.*, CONCAT(b.lname, ', ', b.fname, ' ', b.mname) AS fullname, c.description AS position_desc, b.deptid, b.office
                                             FROM $table a
                                             LEFT JOIN employee b ON b.employeeid = a.employeeid
                                             LEFT JOIN code_position c ON c.positionid = b.positionid
                                             WHERE $where_clause AND ((a.fromdate BETWEEN '$dfrom' AND '$dto') AND (a.todate BETWEEN '$dfrom' AND '$dto'))  ORDER BY fullname")->result_array();
        }
        return $data;
    }

    function showDetailedAttendance($fv, $datesetfrom, $datesetto, $category, $office, $deptid){
        $where_clause = '';
        $selected_category = "a.*,";
        if($category && $category != 'att_terminal'){
            $selected_category = " $category "." AS hours, ";
            $where_clause .= " AND $category != '' ";
        }
        if($category == "overtime") $selected_category .= " ,ot_amount, ot_type";
        if($fv) $where_clause .= " AND b.employeeid = '$fv' ";
        if($deptid) $where_clause .= " AND b.deptid = '$deptid' ";
        if($office && $deptid) $where_clause .= " AND b.office = '$office' AND c.department_id = '$deptid' ";
        if($office && !$deptid) $where_clause .= " AND b.office = '$office' ";
        if($category == "lateut") $selected_category .= " late, undertime ";

        $data = $this->db->query("SELECT  $selected_category b.`employeeid`, a.`sched_date`, CONCAT(lname, ',', fname, ',', mname) AS fullname, deptid, campusid, description  FROM employee_attendance_detailed a INNER JOIN employee b  ON b.`employeeid` = a.employeeid INNER JOIN code_office c ON b.office = c.code WHERE sched_date BETWEEN '$datesetfrom' AND '$datesetto'  $where_clause ");
        // echo "<pre>"; print_r($this->db->last_query()); die;
        if($data->num_rows() > 0) return $data->result_array();
        else return FALSE;
    }

    function getEmployeeListFromPayrollComputedTable($employeeid="", $teaching_type="", $cutoff_start="", $cutoff_end="", $status="", $is_have_income = false, $is_have_other_deduc=false, $is_have_loan = false, $categ = ''){
        $where_clause = "";
        if($employeeid) $where_clause .= "a.employeeid='$employeeid' ";
        if($teaching_type) $where_clause .= (($where_clause) ? "AND " : "") ."b.teachingtype='$teaching_type' ";
        if($cutoff_start && $cutoff_end && $categ == "DEDUCTION") $where_clause .= (($where_clause) ? "AND " : "") ."a.cutoffstart='$cutoff_start' AND a.cutoffend='$cutoff_end' ";
        if($cutoff_start && $cutoff_end && $categ == "LOAN") $where_clause .= (($where_clause) ? "AND " : "") ."DATE_FORMAT(a.cutoffstart, '%M~~%Y')='$cutoff_start' AND DATE_FORMAT(a.cutoffend, '%M~~%Y')='$cutoff_end' ";
        if($is_have_income) $where_clause .= (($where_clause) ? "AND " : "") ."a.income <> '' ";
        if($is_have_other_deduc) $where_clause .= (($where_clause) ? "AND " : "") ."a.otherdeduc <> '' ";
        if($is_have_loan) $where_clause .= (($where_clause) ? "AND " : "") ."a.loan <> '' ";
         if($status) $where_clause .= (($where_clause) ? "AND " : "") ."a.status='$status' ";

        $where_clause = ($where_clause) ? "WHERE $where_clause" : "";
        return $this->db->query("SELECT CONCAT(b.lname, ', ', b.fname, ' ', b.mname) AS fullname, b.deptid, c.description AS dept_desc, b.campusid, c.description AS campus_desc, b.teachingtype, a.* 
                                 FROM payroll_computed_table a
                                 INNER JOIN employee b ON b.employeeid = a.employeeid
                                 LEFT JOIN code_office c ON c.code = b.deptid
                                 $where_clause
                                 ORDER BY fullname;")->result();
    }


    function getOtherIncomeEmployeeList($employeeid=""){
        $where_clause = ($employeeid) ? "AND FIND_IN_SET(a.employeeid, '$employeeid')" : "";

        /*return $this->db->query("SELECT CONCAT(b.lname, ', ', b.fname, ' ', b.mname) AS fullname, b.deptid, c.description AS dept_desc, b.teachingtype, d.description AS income_desc, a.*  
                                 FROM other_income a
                                 INNER JOIN employee b ON b.employeeid = a.employeeid
                                 LEFT JOIN code_office c ON c.code = b.deptid
                                 LEFT JOIN payroll_income_config d ON d.id = a.other_income
                                 $where_clause
                                 ORDER BY fullname, income_desc;")->result();*/
        return $this->db->query("SELECT a.employeeid AS empID, CONCAT(a.lname, ', ', a.fname, ' ', a.mname) AS fullname, a.deptid, c.description AS dept_desc, a.teachingtype, d.description AS income_desc, b.*
                                 FROM employee a
                                 LEFT JOIN other_income b ON b.employeeid = a.employeeid
                                 LEFT JOIN code_office c ON c.code = a.deptid
                                 LEFT JOIN payroll_income_config d ON d.id = b.other_income
                                 WHERE (dateresigned = '1970-01-01' OR dateresigned IS NULL OR dateresigned = '0000-00-00') $where_clause
                                 ORDER BY fullname, income_desc;")->result();
    }

    function getDeminimissIncome(){
        $income = array();

        $q_income = $this->db->query("SELECT * FROM payroll_income_config WHERE incomeType='deminimiss'")->result();
        foreach ($q_income as $row) $income[] = $row->id;

        return $income;
    }

    function getAllCampus(){
        $campus_list = array();

        $q_campus = $this->db->query("SELECT code, description FROM code_campus;")->result();
        foreach ($q_campus as $row) $campus_list[$row->code] = $row->description;

        return $campus_list;
    }

    function getAllDepartment(){
        $department_list = array();

        $q_campus = $this->db->query("SELECT code, description FROM code_department ")->result();
        foreach ($q_campus as $row) $department_list[$row->code] = $row->description;

        return $department_list;
    }

    function getPayrollConfig($table_key){
        $table_arr = array(
            "income" => "payroll_income_config",
            "deduction" => "payroll_deduction_config",
            "loan" => "payroll_loan_config"
        );
        $table = $table_arr[$table_key];

        $config = array();
        $q_payroll_config = $this->db->query("SELECT id, description FROM $table")->result();

        foreach ($q_payroll_config as $row) $config[$row->id] = $row->description;

        return $config;
    }

    function getLoanBalance($employeeid, $code, $date, $forReport = ''){
        $balance = 0;
        $where_clause = '';
        if($forReport) $where_clause = " AND DATE_FORMAT(b.timestamp, '%M~~%Y')='$date' ";
        else $where_clause = "  AND DATE(b.timestamp) <= '$date' ";

        $q_balance = $this->db->query("SELECT b.balance
                                       FROM employee_loan a 
                                       LEFT JOIN employee_loan_payment b ON b.base_id = a.id
                                       WHERE a.employeeid='$employeeid' AND a.code_loan='$code' $where_clause 
                                       ORDER BY b.timestamp DESC 
                                       LIMIT 1;")->result();
        foreach ($q_balance as $row) $balance = $row->balance;
        return $balance;
    }

    function getSetupForPayrollIncome($col, $id){
        $result = '';
        $q_income = $this->db->query("SELECT $col AS selCol FROM payroll_income_config WHERE id='$id';")->result();
        foreach ($q_income as $res) $result = $res->selCol;

        return $result;
    }

    function getPayrollComputedData($cutoffstart, $cutoffend, $status='', $teachingtype='', $sort_by=''){
        $where_clause = $orderby = "";
        if($status) $where_clause .= "AND a.status='$status' ";
        if($teachingtype) $where_clause .= "AND b.teachingtype='$teachingtype' ";

        if($sort_by == "department") $orderby = "ORDER BY b.office";
        else $orderby = "ORDER BY fullname";

        return $this->db->query("SELECT UPPER(REPLACE(CONCAT(b.LName,', ',b.FName,' ',b.MName), 'Ã‘', 'Ñ')) AS fullname, b.office, c.description AS dept_desc, b.campusid, a.*
                                 FROM payroll_computed_table a
                                 INNER JOIN employee b ON b.employeeid = a.employeeid
                                 INNER JOIN code_office c ON c.code = b.office
                                 WHERE cutoffstart='$cutoffstart' AND cutoffend='$cutoffend' $where_clause
                                 $orderby")->result();
    }

    function getPayrollIncomeAdjustment($selected_income=''){
        $data = array();
        $where_clause = ($selected_income) ? "AND FIND_IN_SET(id, '$selected_income')" : "";

        $q_income = $this->db->query("SELECT * FROM payroll_income_config WHERE (description LIKE '% adj%' OR description LIKE '%adjustment%' OR description LIKE '%adj %') $where_clause ORDER BY description;")->result();
        foreach ($q_income as $row) {
            $data[$row->id] = array(
                "description" => $row->description,
                "taxable"     => $row->taxable
            );
        }
        return $data;
    }

    function getEmploymentHistoryData($office='', $employeeid='', $isactive=''){
        $wc = "";
        $this->db->query("UPDATE employee_employment_status_history set office = NULL WHERE office = ''");
        if($office) $wc .= " AND b.office = '$office'";
        if($employeeid) $wc .= " AND b.employeeid = '$employeeid'";
        $datenow = date("Y-m-d");
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="2"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        $query = $this->db->query("SELECT CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname, a.employeeid, e.description AS departmentDesc, b.office, c.description AS employmentstatDesc, d.description AS positionDesc, b.dateposition, b.dateresigned FROM employee_employment_status_history b INNER JOIN employee a ON b.employeeid = a.employeeid INNER JOIN code_status c ON b.employeestat = c.code INNER JOIN code_position d ON b.positionid = d.positionid INNER JOIN code_department e ON b.deptid = e.code WHERE b.employeeid != '' $wc ORDER BY fullname ASC")->result_array();

        //$query = $this->db->query("SELECT CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname, a.employeeid, e.description AS departmentDesc, b.office, c.description AS employmentstatDesc, d.description AS positionDesc, b.dateposition, b.dateresigned FROM employee_employment_status_history b INNER JOIN employee a ON b.employeeid = a.employeeid INNER JOIN code_status c ON b.employeestat = c.code INNER JOIN code_position d ON b.positionid = d.positionid INNER JOIN code_department e ON b.deptid = e.code WHERE b.employeeid != '' $wc ORDER BY case when b.office is null then 1 else 0 end, b.office, fullname")->result_array();

        // $query = $this->db->query("SELECT DISTINCT a.employeeid, CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname,  e.description AS departmentDesc, b.office, a.employmentstat, c.description AS employmentstatDesc, d.description AS positionDesc, b.dateposition, b.dateresigned 
        //     FROM employee_employment_status_history b 
        //     INNER JOIN employee a ON b.employeeid = a.employeeid 
        //     INNER JOIN code_status c ON a.employmentstat = c.code 
        //     INNER JOIN code_position d ON b.positionid = d.positionid 
        //     INNER JOIN code_department e ON b.deptid = e.code
        //     WHERE b.employeeid != '' $wc ORDER BY case when b.office is null then 1 else 0 end, b.office, fullname")->result_array();

        return $query;

    }

    public function getSeminarData($department='', $datefrom='', $dateto='', $sortby='', $office='', $employeeid='', $isactive='', $seminar=''){
        $wc = $wowc = "";
        $orderby = $woorderby = "";
        $datenow = date("Y-m-d");
        if($department && $department != 'all' && $department != 'All'){
            $wc .= " AND a.deptid = '$department'";
            $wowc .= " AND deptid = '$department'";
        }
        if($office && $office != 'all' && $office != 'All'){
            $wc .= " AND a.office = '$office'";
            $wowc .= " AND office = '$office'";
        }
        if($employeeid){
            $wc .= " AND a.employeeid = '$employeeid' AND a.employeeid != '' ";
            $wowc .= " AND employeeid = '$employeeid' AND employeeid != '' ";
        }
        // if($isactive != "") $wc .= " AND a.isactive = '$isactive'";
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
            $wowc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
            $wowc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)){
                $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
                $wowc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
            }
        }

        if($sortby == "alphabets"){
            $orderby .= " ORDER BY fullname";
            $woorderby .= " ORDER BY fullname";
        } else{
            $orderby .= " ORDER BY a.office, fullname";
            $woorderby .= " ORDER BY office, fullname";
        }

        $employee_pts = $this->db->query("SELECT CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname, b.datef AS daterange, b.location, b.organizer, b.title as seminar_title, b.other_title, a.employeeid, a.fname, a.mname, a.lname, a.office, 0 as regfee, 0 as accfee, 0 as transfee, 0 as total, 0 as otherfee  FROM employee a INNER JOIN employee_pts b ON a.employeeid = b.employeeid WHERE (b.datef BETWEEN '$datefrom' AND '$dateto') AND b.title <> '' $wc $orderby")->result_array();
        // echo "<pre>"; print_r($this->db->last_query()); die;
        $employee_pts_pdp1 = $this->db->query("SELECT CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname, CONCAT(b.datef,' - TO - ',b.datet) AS daterange, b.location, b.organizer, b.seminar_title, b.regfee, b.transfee, b.accfee, b.total, b.otherfee, a.employeeid, a.fname, a.mname, a.lname, a.office FROM employee a INNER JOIN employee_pts_pdp1 b ON a.employeeid = b.employeeid WHERE ((b.datef BETWEEN '$datefrom' AND '$dateto') OR (b.datet BETWEEN '$datefrom' AND '$dateto')) AND b.seminar_title <> '' $wc $orderby")->result_array();
        $employee_pts_pdp2 = $this->db->query("SELECT CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname, b.datef AS daterange, b.location, b.organizer, b.title as seminar_title, b.other_title, a.employeeid, a.fname, a.mname, a.lname, a.office, 0 as regfee, 0 as accfee, 0 as transfee, 0 as total, 0 as otherfee  FROM employee a INNER JOIN employee_pts_pdp2 b ON a.employeeid = b.employeeid WHERE (b.datef BETWEEN '$datefrom' AND '$dateto') AND b.title <> '' $wc $orderby")->result_array();
        $employee_pts_pdp3 = $this->db->query("SELECT CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname, b.datef AS daterange, b.location, b.organizer, b.title as seminar_title, b.other_title, a.employeeid, a.fname, a.mname, a.lname, a.office, 0 as regfee, 0 as accfee, 0 as transfee, 0 as total, 0 as otherfee  FROM employee a INNER JOIN employee_pts_pdp3 b ON a.employeeid = b.employeeid WHERE (b.datef BETWEEN '$datefrom' AND '$dateto') AND b.title <> '' $wc $orderby")->result_array();

        $employee_wo_seminar = $this->db->query("SELECT CONCAT(lname,', ',fname,' ', mname) AS fullname, employeeid, '' as daterange, '' as location, '' as organizer, '' as seminar_title, '' as other_title, fname, mname, lname, office, 0 as regfee, 0 as transfee, 0 as accfee, 0 as total, 0 as otherfee FROM employee WHERE employeeid NOT IN (SELECT employeeid FROM employee_pts WHERE (datef BETWEEN '$datefrom' AND '$dateto')) AND employeeid NOT IN (SELECT employeeid FROM employee_pts_pdp1 WHERE (datef BETWEEN '$datefrom' AND '$dateto')) AND employeeid NOT IN (SELECT employeeid FROM employee_pts_pdp2 WHERE (datef BETWEEN '$datefrom' AND '$dateto')) AND employeeid NOT IN (SELECT employeeid FROM employee_pts_pdp3 WHERE (datef BETWEEN '$datefrom' AND '$dateto')) $wowc $woorderby")->result_array();

        $employee_pts_wo = $this->db->query("SELECT CONCAT(lname,', ',fname,' ', mname) AS fullname, employeeid, '' as daterange, '' as location, '' as organizer, '' as seminar_title, '' as other_title, fname, mname, lname, office, 0 as regfee, 0 as transfee, 0 as accfee, 0 as total, 0 as otherfee FROM employee WHERE employeeid NOT IN (SELECT employeeid FROM employee_pts WHERE (datef BETWEEN '$datefrom' AND '$dateto')) $wowc $woorderby")->result_array();

        $employee_pts_pdp1_wo = $this->db->query("SELECT CONCAT(lname,', ',fname,' ', mname) AS fullname, employeeid, '' as daterange, '' as location, '' as organizer, '' as seminar_title, '' as other_title, fname, mname, lname, office, 0 as regfee, 0 as transfee, 0 as accfee, 0 as total, 0 as otherfee FROM employee WHERE employeeid NOT IN (SELECT employeeid FROM employee_pts_pdp1 WHERE (datef BETWEEN '$datefrom' AND '$dateto')) $wowc $woorderby")->result_array();

        $employee_pts_pdp2_wo = $this->db->query("SELECT CONCAT(lname,', ',fname,' ', mname) AS fullname, employeeid, '' as daterange, '' as location, '' as organizer, '' as seminar_title, '' as other_title, fname, mname, lname, office, 0 as regfee, 0 as transfee, 0 as accfee, 0 as total, 0 as otherfee FROM employee WHERE employeeid NOT IN (SELECT employeeid FROM employee_pts_pdp2 WHERE (datef BETWEEN '$datefrom' AND '$dateto')) $wowc $woorderby")->result_array();

        $employee_pts_pdp3_wo = $this->db->query("SELECT CONCAT(lname,', ',fname,' ', mname) AS fullname, employeeid, '' as daterange, '' as location, '' as organizer, '' as seminar_title, '' as other_title, fname, mname, lname, office, 0 as regfee, 0 as transfee, 0 as accfee, 0 as total, 0 as otherfee FROM employee WHERE employeeid NOT IN (SELECT employeeid FROM employee_pts_pdp3 WHERE (datef BETWEEN '$datefrom' AND '$dateto')) $wowc $woorderby")->result_array();
        if($seminar == ''){
            $allseminars = array_merge($employee_wo_seminar, $employee_pts, $employee_pts_pdp1, $employee_pts_pdp2, $employee_pts_pdp3);
            if($sortby == "alphabets"){
                 foreach ($allseminars as $key => $row) {
                    $fullnamesort[$key] = $row['fullname'];
                }
                array_multisort($fullnamesort, SORT_ASC, $allseminars);
            }else{
                foreach ($allseminars as $key => $row) {
                    $officesort[$key]  = $row['office'];
                    $fullnamesort[$key] = $row['fullname'];
                }
                array_multisort($officesort, SORT_ASC, $fullnamesort, SORT_ASC, $allseminars);
            }
            return $allseminars;
        }else{
            if($seminar == 'employee_pts'){
                $allseminars = array_merge($employee_pts, $employee_pts_wo);
                    if($sortby == "alphabets"){
                         foreach ($allseminars as $key => $row) {
                            $fullnamesort[$key] = $row['fullname'];
                        }
                        array_multisort($fullnamesort, SORT_ASC, $allseminars);
                    }else{
                        foreach ($allseminars as $key => $row) {
                            $officesort[$key]  = $row['office'];
                            $fullnamesort[$key] = $row['fullname'];
                        }
                        array_multisort($officesort, SORT_ASC, $fullnamesort, SORT_ASC, $allseminars);
                    }
            }
            else if($seminar == 'employee_pts_pdp1'){
                $allseminars = array_merge($employee_pts_pdp1, $employee_pts_pdp1_wo);
                    if($sortby == "alphabets"){
                         foreach ($allseminars as $key => $row) {
                            $fullnamesort[$key] = $row['fullname'];
                        }
                        array_multisort($fullnamesort, SORT_ASC, $allseminars);
                    }else{
                        foreach ($allseminars as $key => $row) {
                            $officesort[$key]  = $row['office'];
                            $fullnamesort[$key] = $row['fullname'];
                        }
                        array_multisort($officesort, SORT_ASC, $fullnamesort, SORT_ASC, $allseminars);
                    }
            }
            else if($seminar == 'employee_pts_pdp2'){
                $allseminars = array_merge($employee_pts_pdp2, $employee_pts_pdp2_wo);
                    if($sortby == "alphabets"){
                         foreach ($allseminars as $key => $row) {
                            $fullnamesort[$key] = $row['fullname'];
                        }
                        array_multisort($fullnamesort, SORT_ASC, $allseminars);
                    }else{
                        foreach ($allseminars as $key => $row) {
                            $officesort[$key]  = $row['office'];
                            $fullnamesort[$key] = $row['fullname'];
                        }
                        array_multisort($officesort, SORT_ASC, $fullnamesort, SORT_ASC, $allseminars);
                    }
            }
            else if($seminar == 'employee_pts_pdp3'){
                $allseminars = array_merge($employee_pts_pdp3, $employee_pts_pdp3_wo);
                    if($sortby == "alphabets"){
                         foreach ($allseminars as $key => $row) {
                            $fullnamesort[$key] = $row['fullname'];
                        }
                        array_multisort($fullnamesort, SORT_ASC, $allseminars);
                    }else{
                        foreach ($allseminars as $key => $row) {
                            $officesort[$key]  = $row['office'];
                            $fullnamesort[$key] = $row['fullname'];
                        }
                        array_multisort($officesort, SORT_ASC, $fullnamesort, SORT_ASC, $allseminars);
                    }
            }
            return $allseminars;
        }
    }

    public function getEmployeewith10kbalance($department='', $sortby='', $office='', $isactive=''){
        $wc = "";
        $orderby = "";
        if($department) $wc .= " AND deptid = '$department'";
        if($office) $wc .= " AND office = '$office'";
        $datenow = date("Y-m-d");
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        // if($employeeid) $wc .= " AND a.employeeid = '$employeeid'";
        if($sortby == "alphabets"){
            $orderby .= " ORDER BY lname";
        } else{
            $orderby .= " ORDER BY case when office is null then 1 else 0 end, office, lname";
        }
        $query = $this->db->query("SELECT * FROM employee WHERE employeeid != '' $wc $orderby")->result_array();
        return $query;

    }

    function getSeminarAllowance($empid='', $dfrom='',$dto=''){
        $limit = 10000;
        $wC = "";
        if(isset($dfrom) && isset($dto)) $wC .= " AND (a.datefrom BETWEEN '$dfrom' AND '$dto') AND (a.dateto BETWEEN '$dfrom' AND '$dto')";
        $remaining = $this->db->query("SELECT SUM(a.total) as remaining FROM leave_app_base a INNER JOIN leave_app_emplist b ON a.id = b.base_id where a.type = 'PL-SEM' AND a.applied_by = '$empid' AND b.status <> 'DISAPPROVED' $wC")->row()->remaining;
        return $remaining;

    }

    //Added may 29 2019 ken
    public function allEmpByDept($isactive=""){
        $wc = "";
        // if($isactive!="") $wc = " WHERE isactive = '$isactive' ";
        $datenow = date("Y-m-d");
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        $query = $this->db->query("SELECT DISTINCT a.`deptid`, CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname, b.`description`, a.employeeid FROM employee a LEFT JOIN code_department b ON a.`deptid` = b.`code` WHERE 1 $wc GROUP BY a.`employeeid` ORDER BY b.`description` IS NULL,a.`deptid`,a.`lname` ASC")->result();
        return $query;
    }

    public function allEmpByGender($isactive=""){
        $wc = "";
        $this->db->query("UPDATE employee set gender = NULL WHERE gender = ''");
        $datenow = date("Y-m-d");
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        $query = $this->db->query("SELECT DISTINCT a.*, CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname  FROM employee a WHERE 1 $wc GROUP BY employeeid ORDER BY case when gender is null then 1 else 0 end, gender, fullname")->result();
        return $query;
    }

    public function allEmpByCS($isactive=""){
        $wc = "WHERE 1";
        $this->db->query("UPDATE employee set civil_status = NULL WHERE civil_status = ''");
        $datenow = date("Y-m-d");
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        $query = $this->db->query("SELECT a.*, CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname  FROM employee a $wc GROUP BY employeeid ORDER BY case when civil_status is null then 1 else 0 end, civil_status, fullname")->result();
        return $query;
    }

    

    public function getCivilStatus($code, $isactive=""){
        $wc = "";
        if($isactive!="") $wc = " AND isactive = '$isactive' ";
        $query = $this->db->query("SELECT description FROM code_civil_status WHERE CODE = '$code' $wc ")->result();
        return $query;
    }

    public function allEmpByOffice($isactive=""){
        $wc = "";
        $datenow = date("Y-m-d");
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        $query = $this->db->query("SELECT DISTINCT a.*, CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname  FROM employee a WHERE 1 $wc GROUP BY employeeid ORDER BY case when office is null then 1 else 0 end, office, fullname")->result();
        return $query;
    }

    public function allEmpByEmpStat($isactive=""){
        $wc = "";
        $this->db->query("UPDATE employee set employmentstat = NULL WHERE employmentstat = ''");
        $datenow = date("Y-m-d");
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        $query = $this->db->query("SELECT DISTINCT a.*, CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname  FROM employee a WHERE 1 $wc GROUP BY employeeid ORDER BY case when employmentstat is null then 1 else 0 end, employmentstat, fullname")->result();
        return $query;
    }


    public function allEmpByPosition($isactive=""){
        $wc = "";
        $this->db->query("UPDATE employee set positionid = NULL WHERE positionid = ''");
        $datenow = date("Y-m-d");
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        }
        $query = $this->db->query("SELECT DISTINCT a.*, CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname  FROM employee a WHERE 1 $wc GROUP BY employeeid ORDER BY case when positionid is null then 1 else 0 end, positionid, fullname")->result();
        return $query;
    }

    public function allEmpAttendancePresent($date,$sort){
        $query = $this->db->query("SELECT a.`lname`,a.`fname`,a.`mname`,a.`deptid`,a.`office`,a.`employeeid` AS oldemployee,b.`userid`,b.`timein`,CONCAT(a.lname, ', ', a.fname, ' ', a.mname) AS fullname FROM employee a INNER JOIN timesheet b ON a.`employeeid` = b.`userid` AND b.`timein` LIKE '$date%' GROUP BY a.`employeeid` ORDER BY a.$sort,a.`lname` ASC ")->result();
        return $query;
    }

    public function allEmpAttendanceAbsent($date,$sort){
        $query = $this->db->query("SELECT a.`lname`,a.`fname`,a.`mname`,a.`deptid`,a.`office`,a.`employeeid` AS oldemployee,b.`userid`,b.`timein`,CONCAT(a.lname, ', ', a.fname, ' ', a.mname) AS fullname FROM employee a INNER JOIN timesheet b ON a.`employeeid` = b.`userid` AND b.`timein` NOT LIKE '$date%' GROUP BY a.`employeeid` ORDER BY a.$sort,a.`lname` ASC ")->result();
        return $query;
    }

    public function allEmpAttendance($sort){
        $query = $this->db->query("SELECT a.`employeeid` AS oldemployee, a.`deptid`,a.`office`, CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname  FROM employee a GROUP BY employeeid ORDER BY $sort, a.lname ASC")->result();
        return $query;
    }

    public function getLeaveDate($employeeid){
        $query = $this->db->query("SELECT b.`dateto`, b.`type` FROM leave_app_emplist a INNER JOIN leave_app_base b ON a.`base_id` = b.`id` AND a.`employeeid` = '$employeeid'")->result_array();
        return $query;
    }

    public function getOBDate($employeeid){
        $query = $this->db->query("SELECT b.`dateto`, b.`type` FROM ob_app_emplist a INNER JOIN ob_app b ON a.`base_id` = b.`id` AND a.`employeeid` = '$employeeid'")->result_array();
        return $query;
    }

    public function LeaveOBChecker($employeeid,$date){
        if(sizeof($this->getOBDate($employeeid)) > 0){
            $query = $this->getOBDate($employeeid);
            $date_now = $date;
                if ($date_now > date($query[0]['dateto'])) {
                   return  $return = null;
                }else{
                    return $return = $this->employeemod->othLeaveDesc($query[0]['type']);
                }
        }else if(sizeof($this->getLeaveDate($employeeid)) > 0){
            $query = $this->getLeaveDate($employeeid);
            $date_now = $date;
                if ($date_now > date($query[0]['dateto'])){
                    return $return = null;
                }else{
                    return $return = $this->employeemod->othLeaveDesc($query[0]['type']);
                }
        }else return false;
    }

    public function allEmpBySalary($isactive=""){
        $wc = "";
        $datenow = date("Y-m-d");
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        };
        $query = $this->db->query("SELECT DISTINCT a.`lname`, a.`fname`, a.`mname`, a.`employeeid` AS oldemployee, b.`employeeid`, b.`monthly`, CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname FROM employee a LEFT JOIN payroll_employee_salary b ON a.`employeeid` = b.`employeeid` WHERE 1 $wc GROUP BY a.`employeeid` ORDER BY a.`lname` ASC ")->result();
        return $query;
    }

    public function allEmpYearService($isactive=""){
        $wc = "WHERE 1";
        $datenow = date("Y-m-d");
        if($isactive != ""){
          if($isactive=="1"){
            $wc .= " AND (('$datenow' < dateresigned2 OR dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL) AND isactive ='1')";
          }
          if($isactive=="0"){
            $wc .= " AND (('$datenow' >= dateresigned2 AND dateresigned2 IS NOT NULL AND dateresigned2 <> '0000-00-00' AND dateresigned2 <> '1970-01-01' ) OR isactive = '0')";
          }
          if(is_null($isactive)) $wc .= " AND isactive = '1' AND (dateresigned2 = '0000-00-00' OR dateresigned2 = '1970-01-01' OR dateresigned2 IS NULL)";
        };
        $query = $this->db->query("SELECT DISTINCT a.`lname`, a.`fname`, a.`mname`, a.`employeeid` AS oldemployee, b.`employeeid`, b.`dateposition`, a.`dateemployed`, CONCAT(a.lname,', ',a.fname,' ',a.mname) AS fullname FROM employee a LEFT JOIN employee_employment_status_history b ON a.`employeeid` = b.`employeeid` $wc GROUP BY a.`employeeid` ORDER BY a.`lname` ASC")->result();
        return $query;
    }

    public function getEmpDetailbyid($id){
        return $this->db->query("SELECT * FROM employee WHERE employeeid = '$id' LIMIT 1");
    }

    function getFacialDowntimeData(){
        // $this->db->select('email');
        $this->db->limit(300);
        $this->db->order_by("timestamp","DESC");
        return $this->db->get('facial_downtime')->result();
    }

    function allLogsData($deptid, $dfrom, $dto){
        
        $wc = '';
        if($deptid){
            $deptid = $this->extensions->getOfficeDescriptionReport($deptid);
            $wc = " AND a.DepartmentName = '$deptid'";
        }
        $datenow = date("Y-m-d");
        $query = $this->db->query("SELECT a.* FROM allcard_logs a INNER JOIN employee b ON a.IDNumber = b.employeeid WHERE (('$datenow' < b.dateresigned2 OR b.dateresigned2 = '0000-00-00' OR b.dateresigned2 = '1970-01-01' OR b.dateresigned2 IS NULL) AND b.isactive ='1') AND  (DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto') $wc GROUP BY a.IDNumber")->result();
        return $query;
    }

    function getLockUnlockData($deptid, $dfrom, $dto, $status){
        $wc = '';
        if($deptid){
            $wc = " AND b.deptid = '$deptid";
        }
        $datenow = date("Y-m-d");
        $query = $this->db->query("SELECT a.* FROM lock_unlock_account a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.status = '$status' AND (DATE(a.timestamp) BETWEEN '$dfrom' AND '$dto') $wc ")->result();
        return $query;
    }
}