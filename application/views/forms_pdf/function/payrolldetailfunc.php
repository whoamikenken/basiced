<?php

/**
 * @author Justin
 * @copyright 2015
 */

function getRecord($eid,$sched,$quarter,$sdate,$edate,$dept){
    $whereClause = "";
    if($eid)    $whereClause .= " AND a.employeeid='$eid'";
    if($dept)   $whereClause .= " AND b.deptid='$dept'";
    $query = mysql_query("SELECT a.*, CONCAT(lname,', ',fname,' ',mname) as fullname FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE schedule='$sched' AND quarter='$quarter' AND cutoffstart='$sdate' AND cutoffend='$edate' $whereClause");
    return $query;
}

function getEmpDesc($eid){
    $return = "";
    $query = mysql_query("SELECT b.description FROM employee a INNER JOIN code_office b ON a.deptid = b.code WHERE a.employeeid='$eid'");
    $data = mysql_fetch_array($query);
    $return = $data['description'];
    return $return; 
}

function getData($eid,$sched,$quarter,$sdate,$edate,$dept,$col,$th = false){
    $amt = $title = array();
    $whereClause = "";
    if($eid)    $whereClause .= " AND employeeid='$eid'";
    #if($dept)   $whereClause .= " AND deptid='$dept'";
    $query = mysql_query("SELECT $col FROM payroll_computed_table WHERE schedule='$sched' AND quarter='$quarter' AND cutoffstart='$sdate' AND cutoffend='$edate' $whereClause");
    $rs = mysql_fetch_array($query);
    $data = $rs[$col];
    if($col == "withholdingtax")    return $data;
    if($data){
        $iex = explode('/',$data);
        for($x = 0; $x < count($iex); $x++){
            $singleex = explode('=',$iex[$x]); 
            if($singleex[1] > 0){
                $title[] = $singleex[0];
                $amt[]   = $singleex[1];
            }
        }
        if($th) return $title; else return $amt;
    }else return "";
}

function getTotal($eid,$sched,$quarter,$sdate,$edate,$dept,$col,$ttle){
    $amt = $lamt = array();
    $whereClause = "";
    $yr = date('Y',strtotime($sdate));
    if($eid)    $whereClause .= " AND employeeid='$eid'";
    if($dept)   $whereClause .= " AND deptid='$dept'";
    $query = mysql_query("SELECT $col FROM payroll_computed_table WHERE schedule='$sched' AND SUBSTR(cutoffstart,1,4) = '$yr' AND cutoffend <= '$edate' $whereClause");
    while($rs = mysql_fetch_array($query)){
        $data = $rs[$col];
        if($col == "salary" || $col == "withholdingtax")    $amt[] = $data;
        else{
            if($data){
                $iex = explode('/',$data);
                for($x = 0; $x < count($iex); $x++){
                    $singleex = explode('=',$iex[$x]); 
                    if($singleex[0] == $ttle){
                        if($singleex[1] > 0){
                            $lamt[]   = $singleex[1];
                        }
                    }
                }
                #return $amt;
            }
        }
    }
    if($col == "salary" || $col == "withholdingtax")    return array_sum($amt);
    else                                                return array_sum($lamt);
}

function getTotalWHTax($eid,$sched,$quarter,$sdate,$edate,$dept){
    $amt = $lamt = array();
    $whereClause = "";
    $yr = date('Y',strtotime($sdate));
    if($eid)    $whereClause .= " AND employeeid='$eid'";
    if($dept)   $whereClause .= " AND deptid='$dept'";
    $query = mysql_query("SELECT SUM(withholdingtax) as tax FROM payroll_computed_table WHERE schedule='$sched' AND SUBSTR(cutoffstart,1,4) = '$yr' AND cutoffend <= '$edate' $whereClause");
    $rs = mysql_fetch_array($query);
    return $rs['tax'];
}
?>