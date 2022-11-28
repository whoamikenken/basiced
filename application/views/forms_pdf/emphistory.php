<?php

/**
 * @author Justin
 * @copyright 2016
 * GOOD LUCK! (^__^)
 */
include "application/config/connection.php";
include "application/views/forms_pdf/function/payrollfunc.php";
$mpdf = new mPDF('utf-8','A4-L','10','','3','3','6','10','9','9');

$eid        = $_GET['eid']; 
$dept       = $_GET['dept'];
$dfrom      = $_GET['dfrom'];
$dto        = $_GET['dto'];
$schedule   = $_GET['schedule'];
$quarter    = $_GET['quarter']; 
$sort       = $_GET['sort'];

/*
 *  Cut-Off Date Function
 */
function displayCutoff($eid,$yr,$schedule){
    $c = 0;
    $dates = array();
    $return = "<tr>
                <th style='border-bottom: 1px solid black;'>&nbsp;</th>";
    $query = mysql_query("SELECT cutoffstart, cutoffend  FROM payroll_computed_table WHERE employeeid='$eid' AND SUBSTR(cutoffstart,1,4)='$yr' AND schedule='$schedule' ORDER BY cutoffstart");
    while($data = mysql_fetch_array($query)){
        if(date('M',strtotime($data['cutoffstart'])) == 'Sep'){
            if($c == 0) $return .= "<th class='center' style='border-bottom: 1px solid black;'><b>Total</b></th>";
            $c++;
        }
        
        $return .= "<th class='right' style='border-bottom: 1px solid black;'>".date('M d',strtotime($data['cutoffstart']))."-".date('d',strtotime($data['cutoffend']))."</th>";
        
        if(date('M',strtotime($data['cutoffstart'])) == 'Dec'){
            if($c == 0) $return .= "<th class='center' style='border-bottom: 1px solid black;'><b>Total</b></th>";
            $c = 0;
        }
        $dates[] = $data['cutoffstart'];
    }
    $return .= "</tr>";
    return array($return,$dates);
}

/*
 *  Salary Function
 */
function displaySalary($eid,$yr,$schedule){
    $c = 0;
    $total = $total2 = 0;
    $totalwh = $totalwh2 = 0;    
    $totalexc = $totalexc2 = 0; 
    $tarr   = array();
    $whtax  = array();
    $exc    = array();
    $return = "<tr>
                <td class='left'>Regular Pay</td>";
    $query = mysql_query("SELECT cutoffstart, salary, withholdingtax, (tardy+absents) AS excess   FROM payroll_computed_table WHERE employeeid='$eid' AND SUBSTR(cutoffstart,1,4)='$yr' AND schedule='$schedule' ORDER BY cutoffstart");
    while($data = mysql_fetch_array($query)){
        if(date('m',strtotime($data['cutoffstart'])) >= 1 && date('m',strtotime($data['cutoffstart'])) <= 8){  
            $total      += $data['salary'];
            $totalwh    += $data['withholdingtax'];
            $totalexc   += $data['excess'];
        }                        
        if(date('m',strtotime($data['cutoffstart'])) >= 9 && date('m',strtotime($data['cutoffstart'])) <= 12){ 
            $total2     += $data['salary'];
            $totalwh2   += $data['withholdingtax'];
            $totalexc2  += $data['excess'];
        } 
        
        if(date('M',strtotime($data['cutoffstart'])) == 'Sep'){
            if($c == 0){       
                $return .= "<td class='right'><b>".number_format($total,2)."</b></td>";
                $tarr["total"] = $total;
                $whtax["total"] = $totalwh;
                $exc["total"]   = $totalexc;
            }
            $c++;
        }
        
        $return .= "<td class='right'>".number_format($data['salary'],2)."</td>";
        $tarr[$data['cutoffstart']]     = $data['salary'];
        $whtax[$data['cutoffstart']]    = $data['withholdingtax'];
        $exc[$data['cutoffstart']]    = $data['excess'];
        
        if(date('M',strtotime($data['cutoffstart'])) == 'Dec'){
            if($c == 0){        
                $return .= "<td class='right'><b>".number_format($total2,2)."</b></td>";
                $tarr["total2"]  = $total2;
                $whtax["total2"] = $totalwh2;
                $exc["total2"]   = $totalexc2;
            }
            $c = 0;
        }                 
    }                     
    $return .= "</tr>";
    return array($return,$tarr,$whtax,$exc);
}

/*
 *  Income Function
 */
function displayIncome($eid,$yr,$cutoff,$schedule){
    $return = "";
    $marr  = array();
    $tarr  = array();
    $sarr  = array();
    $iarr  = array();    //  var to hold income desc;    
    $query = mysql_query("SELECT code_income FROM payroll_process_income WHERE employeeid='$eid' AND SUBSTR(cutoffstart,1,4)='$yr' AND schedule='$schedule' GROUP BY code_income ORDER BY cutoffstart,code_income;");
    while($data = mysql_fetch_array($query)){
    $total = $total2 = 0;
    $return .= "<tr>
              ";
        $code = $data['code_income'];
        $darr = array();
        $x    = 0;
        $return .= "<td class='left' width='10%'>".incomedesc($code)."</td>";
        $qval = mysql_query("SELECT cutoffstart, amount FROM payroll_process_income WHERE employeeid='$eid' AND SUBSTR(cutoffstart,1,4)='$yr' AND code_income='$code' AND schedule='$schedule' ORDER BY cutoffstart ;");
        while($val = mysql_fetch_array($qval)){
            $darr[$val['cutoffstart']] = $val['amount'];
        }
        
        foreach($cutoff as $date){
            
            if(date('m',strtotime($date)) >= 1 && date('m',strtotime($date)) <= 8)  $total  += $darr[$date];
            if(date('m',strtotime($date)) >= 9 && date('m',strtotime($date)) <= 12) $total2 += $darr[$date]; 
            
            if(date('M',strtotime($date)) == 'Sep'){
                if($c == 0){ 
                    $return .= "<td class='right'><b>".number_format($total,2)."</b></td>";
                    $tarr["total"] = $total;
                    $iarr[$code]["total"] = $total;
                }
                $c++;
            }
            
            if(!empty($darr[$date])){    
                $return .= "<td class='right'>".number_format($darr[$date],2)."</td>";
                // store value in array. 
                $tarr[$date] = $darr[$date];
                $iarr[$code][$date] = $darr[$date];
            }else{                        
                $return .= "<td class='right'>0.00</td>";
                // store value in array. 
                $tarr[$date] = $darr[$date];
                $iarr[$code][$date] = $darr[$date];
            }
            
            if(date('M',strtotime($date)) == 'Dec'){
                if($c == 0){ 
                    $return .= "<td class='right'><b>".number_format($total2,2)."</b></td>";
                    $tarr["total2"] = $total2;
                    $iarr[$code]["total2"] = $total2;
                }
                $c = 0;
            }
      }
      array_push($marr,$tarr);
    $return .= "</tr>";
    }
    return array($return,$marr,$iarr);
}

/*
 * Earnings Function
 */
function displayTotal($sarr,$tarr){
    $darr = array();
    
    // Joining of var "sarr" and "tarr"
    array_push($tarr,$sarr);
    
    // sum all total
    foreach($tarr as $row){
        foreach($row as $key=>$val){
            $darr[$key] += $val;
        }
    }
    
    // display all $darr values
    $return .= "<tr>";
    $return .= "<td class='right border-top' width='6%' style='text-align: right;'>EARNINGS</td>";
    foreach($darr as $row){
        $return .= "<td class='right border-top' width='6%'><b>".number_format($row,2)."</b></td>";
    }
    $return .= "</tr>";

    return array($return,$darr);
}

/*
 * Contribution Function
 */
function displayContribution($eid,$yr,$cutoff,$schedule){
    $return = "";
    $marr = array();
    $tarr = array();
    $sarr = array();
    $query = mysql_query("SELECT code_deduct FROM payroll_process_contribution WHERE employeeid='$eid' AND SUBSTR(cutoffstart,1,4)='$yr' AND schedule='$schedule' GROUP BY code_deduct ORDER BY cutoffstart,code_deduct desc;");
    while($data = mysql_fetch_array($query)){
    $total = $total2 = 0;
    $return .= "<tr>
              ";
        $code = $data['code_deduct'];
        $darr = array();
        $x    = 0;
        $return .= "<td class='left' width='6%'>".$code."</td>";
        $qval = mysql_query("SELECT cutoffstart, amount FROM payroll_process_contribution WHERE employeeid='$eid' AND SUBSTR(cutoffstart,1,4)='$yr' AND code_deduct='$code' AND schedule='$schedule' ORDER BY cutoffstart ;");
        while($val = mysql_fetch_array($qval)){
            $darr[$val['cutoffstart']] = $val['amount'];
        }
        
        foreach($cutoff as $date){
            
            if(date('m',strtotime($date)) >= 1 && date('m',strtotime($date)) <= 8)  $total  += $darr[$date];
            if(date('m',strtotime($date)) >= 9 && date('m',strtotime($date)) <= 12) $total2 += $darr[$date]; 
            
            if(date('M',strtotime($date)) == 'Sep'){
                if($c == 0){ 
                    $return .= "<td class='right'><b>".number_format($total,2)."</b></td>";
                    $tarr["total"] = $total;
                }
                $c++;
            }
            
            if(!empty($darr[$date])){    
                $return .= "<td class='right'>".number_format($darr[$date],2)."</td>";
                // store value in array. 
                $tarr[$date] = $darr[$date];
            }else{                        
                $return .= "<td class='right'>0.00</td>";
                // store value in array. 
                $tarr[$date] = $darr[$date];
            }
            
            if(date('M',strtotime($date)) == 'Dec'){
                if($c == 0){ 
                    $return .= "<td class='right'><b>".number_format($total2,2)."</b></td>";
                    $tarr["total2"] = $total2;
                }
                $c = 0;
            }
      }
      array_push($marr,$tarr);      
    $return .= "</tr>";
    }
    return array($return,$marr);
}

/*
 * WithHolding Tax Function
 */
function displayWHTax($whtax){
    $return = "<tr>";
    $return .= "<td class='left'>WithHolding Tax</td>";
    foreach($whtax as $key=>$val){
        $return .= "<td class='right'>";
        if($key === "total" || $key === "total2")  $return .= "<b>";
                                                   $return .= number_format($val,2);
        if($key === "total" || $key === "total2")  $return .= "</b>";
        $return .= "</td>";
    }
    $return .= "</tr>";
    return $return;
}

/*
 * Excess Absent/Tardy Function
 */
function displayExcess($excess){
    $return = "<tr>";
    $return .= "<td class='left'>Excess Absences/Tardy</td>";
    foreach($excess as $key=>$val){
        $return .= "<td class='right'>";
        if($key === "total" || $key === "total2")  $return .= "<b>";
                                                   $return .= number_format($val,2);
        if($key === "total" || $key === "total2")  $return .= "</b>";
        $return .= "</td>";
    }
    $return .= "</tr>";
    return $return;
}

/*
 * Loan Function
 */
function displayLoan($eid,$yr,$cutoff,$schedule){
    $return = "";
    $marr = array();
    $tarr = array();
    $sarr = array();
    $query = mysql_query("SELECT code_loan FROM payroll_process_loan WHERE employeeid='$eid' AND SUBSTR(cutoffstart,1,4)='$yr' AND schedule='$schedule' GROUP BY code_loan ORDER BY cutoffstart,code_loan;");
    while($data = mysql_fetch_array($query)){
    $total = $total2 = 0;
    $return .= "<tr>
              ";
        $code = $data['code_loan'];
        $darr = array();
        $x    = 0;
        $return .= "<td class='left' width='6%'>".loandesc($code)."</td>";
        $qval = mysql_query("SELECT cutoffstart, amount FROM payroll_process_loan WHERE employeeid='$eid' AND SUBSTR(cutoffstart,1,4)='$yr' AND code_loan='$code' AND schedule='$schedule' ORDER BY cutoffstart ;");
        while($val = mysql_fetch_array($qval)){
            $darr[$val['cutoffstart']] = $val['amount'];
        }
        
        foreach($cutoff as $date){
            
            if(date('m',strtotime($date)) >= 1 && date('m',strtotime($date)) <= 8)  $total  += $darr[$date];
            if(date('m',strtotime($date)) >= 9 && date('m',strtotime($date)) <= 12) $total2 += $darr[$date]; 
            
            if(date('M',strtotime($date)) == 'Sep'){
                if($c == 0){ 
                    $return .= "<td class='right'><b>".number_format($total,2)."</b></td>";
                    $tarr["total"] = $total;
                }
                $c++;
            }
            
            if(!empty($darr[$date])){    
                $return .= "<td class='right'>".number_format($darr[$date],2)."</td>";
                // store value in array. 
                $tarr[$date] = $darr[$date];
            }else{                        
                $return .= "<td class='right'>0.00</td>";
                // store value in array. 
                $tarr[$date] = $darr[$date];
            }
            
            if(date('M',strtotime($date)) == 'Dec'){
                if($c == 0){ 
                    $return .= "<td class='right'><b>".number_format($total2,2)."</b></td>";
                    $tarr["total2"] = $total2;
                }
                $c = 0;
            }
      }
      array_push($marr,$tarr);
    $return .= "</tr>";
    }
    return array($return,$marr);
}

/*
 * Other Deduction Function
 */
function displayOthDeduction($eid,$yr,$cutoff,$schedule){
    $return = "";
    $marr       = array();
    $tarr       = array();      // hold the value for addition.
    $sarr       = array();
    $othdarr    = array();      // hold the value for subtraction.
    $sthdarr    = array();    
    $query = mysql_query("SELECT code_deduct FROM payroll_process_otherdeduct WHERE employeeid='$eid' AND SUBSTR(cutoffstart,1,4)='$yr' AND schedule='$schedule' GROUP BY code_deduct ORDER BY cutoffstart,code_deduct;");
    while($data = mysql_fetch_array($query)){
    $total = $total2 = 0;

    $return .= "<tr>
              ";
        $code = $data['code_deduct'];
        $darr = array();
        $x    = 0;
        $return .= "<td class='left' width='6%'>".deductiondesc($code)."</td>";
        $qval = mysql_query("SELECT cutoffstart, amount FROM payroll_process_otherdeduct WHERE employeeid='$eid' AND SUBSTR(cutoffstart,1,4)='$yr' AND schedule='$schedule' AND code_deduct='$code' ORDER BY cutoffstart ;");
        while($val = mysql_fetch_array($qval)){
            $darr[$val['cutoffstart']] = $val['amount'];
        }
        
        // Get the arithmetic..
        $aquery  = mysql_query("SELECT arithmetic FROM payroll_deduction_config WHERE id='$code'");
        $rs     = mysql_fetch_array($aquery);
        $arith = $rs['arithmetic'];
        

        foreach($cutoff as $date){
            
            if(date('m',strtotime($date)) >= 1 && date('m',strtotime($date)) <= 8)  $total  += $darr[$date];
            if(date('m',strtotime($date)) >= 9 && date('m',strtotime($date)) <= 12) $total2 += $darr[$date]; 
            
            if(date('M',strtotime($date)) == 'Sep'){
                if($c == 0){ 
                    $return .= "<td class='right'><b>".number_format($total,2)."</b></td>";
                    if($arith == "add") $tarr["total"] = $total;
                    else                $othdarr["total"] = $total;
                }
                $c++;
            }
            
            if(!empty($darr[$date])){    
                $return .= "<td class='right'>".number_format($darr[$date],2)."</td>";
                // store value in array. 
                if($arith == "add") $tarr[$date] = $darr[$date];
                else                $othdarr[$date] = $darr[$date];
            }else{                        
                $return .= "<td class='right'>0.00</td>";
                // store value in array. 
                if($arith == "add") $tarr[$date] = $darr[$date];
                else                $othdarr[$date] = $darr[$date];
            }
            
            if(date('M',strtotime($date)) == 'Dec'){
                if($c == 0){ 
                    $return .= "<td class='right'><b>".number_format($total2,2)."</b></td>";
                    if($arith == "add") $tarr["total2"] = $total2;
                    else                $othdarr["total2"] = $total2;
                }
                $c = 0;
            }
      }
      if($arith == "add")   array_push($marr,$tarr);  // get all values for addition
      else                  array_push($sthdarr,$othdarr);  // get all values for subtraction
    $return .= "</tr>";
    }
    return array($return,$marr,$sthdarr);
}

/*
 * Deduction Function
 */
function displayDeduct($dcontarr,$dloanarr,$dotharr,$othdvarr,$dwhtax,$dexcess){
    
    $darr  = array();
    $subt  = array();
    $total = array();
    
    // Joining of all array parameters..
    foreach($dloanarr as $key=>$val){
        array_push($dcontarr,$val);             
    }
    
    // Deduction to Add
    foreach($dotharr as $key=>$val){
        array_push($dcontarr,$val); 
    }
    array_push($dcontarr,$dwhtax);
    array_push($dcontarr,$dexcess);
    
    // sum all deduction
    foreach($dcontarr as $row){
        foreach($row as $key=>$val){
            $darr[$key] += $val;
        }
    }
    
    // sum all deduction to subtract
    foreach($othdvarr as $row){
        foreach($row as $key=>$val){
            $subt[$key] += $val;
        } 
    }
    
    // get total deduction
    foreach($darr as $key=>$val){
        $total[$key] = $val - $subt[$key];
    }
    
    // display all $darr values
    $return .= "<tr>";
    $return .= "<td class='right border-top border-bottom' width='6%' style='text-align: right;'>DEDUCTIONS</td>";
    foreach($total as $row){
        $return .= "<td class='right border-top border-bottom' width='6%'><b>".number_format($row,2)."</b></td>";
    }
    $return .= "</tr>";
    return array($return,$total);
}

/*
 * Net Pay Function
 */
function displayNetPay($tearnings,$tdeduct){
    $darr    = array();
    // Subtract Total Earnings && Total Deductions
    foreach ($tearnings as $key => $value) {
        if(array_key_exists($key, $tearnings) && array_key_exists($key, $tdeduct)){
            $darr[$key] = $value - $tdeduct[$key];
        }
    }
    // display all $darr values
    $return .= "<tr>";
    $return .= "<td class='right' width='6%' style='text-align: right;'><b>Employee NET PAY</b></td>";
    foreach($darr as $row){
        $return .= "<td class='right' width='6%'><b>".number_format($row,2)."</b></td>";
    }
    $return .= "</tr>";
    return $return;
}

/*
 * Net Pay Function
 */
function displayTaxStatus($eid){
    $query = mysql_query("SELECT IF(COUNT(*) > 0, COUNT(*)*25000, 0) as exemp FROM employee_legitimate_relations WHERE employeeid='$eid'");
    $rs = mysql_fetch_array($query);
    $return = '
                <tr>
                    <td class="right">Tax Status</td>
                    <td>'.civil_status($eid).'  </td>
                    <td class="right">Exemption</td>
                    <td class="right">PE</td>
                    <td class="right">'.number_format(Globals::getValue()+$rs['exemp'],2).'</td>
                </tr>
                <tr>
                    <td colspan=3></td>
                    <td class="right">APE</td>
                    <td class="right">'.number_format(0,2).'</td>
                </tr>
               ';
    return $return;
}

/*
 *  BOTTOM BOX DISPLAY
 */
function getAnnualDate($eid,$yr,$schedule){
    $query = mysql_query("SELECT C.cutoffstart, C.cutoffend FROM
                            (
                            SELECT * FROM 
                            (SELECT cutoffstart FROM payroll_computed_table WHERE employeeid='$eid' AND schedule='$schedule' AND SUBSTR(cutoffstart,1,4)='$yr' ORDER BY cutoffstart ASC LIMIT 1) as A,
                            (SELECT cutoffstart as cutoffend FROM payroll_computed_table WHERE employeeid='$eid' AND schedule='$schedule' AND SUBSTR(cutoffstart,1,4)='$yr' ORDER BY cutoffstart DESC LIMIT 1) as B
                            ) AS C
                        ");
    $rs = mysql_fetch_array($query);
    $adate = date('F - ',strtotime($rs['cutoffstart'])).date('F Y',strtotime($rs['cutoffend']));
    return $adate;
}

/*
 * Deduction Function
 * Non-taxable
 */
function TotalContri($dcontarr,$arrkey){
    $darr = array();
    // sum all total
    foreach($dcontarr as $row){
        foreach($row as $key=>$val){
            if($key == $arrkey) $darr[$arrkey] += $val;
        }
    }
    return $darr[$arrkey];
}

/*
 * NET OF EA
 */
function netofea($salaryarr,$excess,$income,$total){
    $return = "";
    $holdid = "";
    $incomearr = array();
    $sumincome = 0;
    $net = 0;
    
    // get all income id
    foreach($income as $key=>$val){
        if(!empty($holdid)) $holdid .= ",";
        $holdid .= $key;
    }
    
    $query = mysql_query("SELECT id FROM payroll_income_config WHERE FIND_IN_SET(id,'$holdid') AND grossinc > 0");
    while($row = mysql_fetch_array($query)){
        $incomearr[] = $income[$row['id']];
    }
    
    // sum all income
    foreach($incomearr as $gkey=>$arrval){
        foreach($arrval as $key=>$val){
            if($total == $key)    $sumincome += $val;
        }
    }
    
    $net = ($sumincome+$salaryarr)-$excess;
    return $net;
}

/*
 * OTHER FUNCS..
 */
function salaryAdjustment($eid,$yr,$schedule){
    $return = "";
    $query = mysql_query("SELECT SUM(a.amount) AS adj FROM payroll_process_income a
                            INNER JOIN payroll_income_config b ON a.code_income = b.id
                            WHERE b.description LIKE '%SAL%ADJUSTMENT%' AND a.employeeid='$eid' AND a.schedule='$schedule' AND SUBSTR(cutoffstart,1,4)='$yr';");
    $rs = mysql_fetch_array($query);
    return $rs['adj'];
}

/*
 *  EARNINGS
 */
list($display,$cutoffdate)                  =   displayCutoff($eid,date('Y',strtotime($dfrom)),$schedule);                  // display cutoff dates
list($dsalary,$salaryarr,$whtax,$excess)    =   displaySalary($eid,date('Y',strtotime($dfrom)),$schedule);                  // display salary
list($dincome,$incomearr,$incomedvarr)      =   displayIncome($eid,date('Y',strtotime($dfrom)),$cutoffdate,$schedule);      // display income
list($earnings,$tearnings)                  =   displayTotal($salaryarr,$incomearr);                                        // display earnings
/*
 *  DEDUCTIONS
 */
list($dcont,$dcontarr)           =   displayContribution($eid,date('Y',strtotime($dfrom)),$cutoffdate,$schedule);           // display contributions
list($dloan,$dloanarr)           =   displayLoan($eid,date('Y',strtotime($dfrom)),$cutoffdate,$schedule);                   // display loans
list($doth,$dotharr,$othdvarr)   =   displayOthDeduction($eid,date('Y',strtotime($dfrom)),$cutoffdate,$schedule);           // display Other Deduction
$dwhtax                          =   displayWHTax($whtax);                                                                  // display WithHolding Tax
$dexcess                         =   displayExcess($excess);                                                                // display Excess
list($deduction,$tdeduct)        =   displayDeduct($dcontarr,$dloanarr,$dotharr,$othdvarr,$whtax,$excess);                            // display deductions
/*
 * NET PAY
 */
$netpay =   displayNetPay($tearnings,$tdeduct);    // display net pay

if(!empty($cutoffdate)){
    $html .= '
    <div class="container">
        <div class="header">
            <h4>EMPLOYEE HISTORY REPORT</h4>
        </div>
        <div>
            <table class="fixed" border=0>
                '.$display.'
                <tr>
                    <td class="left">
                        '.substr($eid,-3).'
                    </td>
                </tr>
                '.$dsalary.'
                '.$dincome.'
                '.$earnings.'
                <tr><td>&nbsp;</td></tr>
                '.$dcont.'
                '.$dwhtax.'
                '.$dexcess.'
                '.$dloan.'
                '.$doth.'
                '.$deduction.'
                '.$netpay.'
                '.displayTaxStatus($eid).'
            </table>
            
        </div>
    </div>
    ';

/*
 * ANNUAL Total
 * Bottom Container Box
 */
 
/*
 * Functions
 */
 
 // Gross Income
 $gijanaug          =   netofea($salaryarr["total"],$excess['total'],$incomedvarr,"total");
 $gisepdec          =   netofea($salaryarr["total2"],$excess['total2'],$incomedvarr,"total2");
 // Non-taxable
 $ntcontrijanaug    =   TotalContri($dcontarr,"total");
 $ntcontrisepdec    =   TotalContri($dcontarr,"total2");
 // Taxable
 $taxjanaug         =   $gijanaug-$ntcontrijanaug;
 $taxsepdec         =   $gisepdec-$ntcontrisepdec;
 // Sal. Adjustment
 $saladj            =   salaryAdjustment($eid,date('Y',strtotime($dfrom)),$schedule);
 
 $annualcutoffdate = getAnnualDate($eid,date('Y',strtotime($dfrom)),$schedule); 
   $html .= '
    <div class="container">
        <div class="left-box">
            <table class="fixed btm-style" border=1>
                <tr>
                    <th width="35%">&nbsp;</th>
                    <th width="15%" class="center">Gross Income</th>
                    <th width="15%" class="center">Non-taxable</th>
                    <th width="13%" class="center">Taxable</th>
                    <th width="22%" class="center">Tax Due</th>
                </tr>
                <tr>
                    <td class="left" colspan="5">'.$annualcutoffdate.'</td>
                </tr>
                <tr>
                    <td class="left">Reg Pay Jan - Aug (net of EA)</td>
                    <td class="right">'.number_format($gijanaug,2).'</td>
                    <td class="right">'.number_format($ntcontrijanaug,2).'</td>
                    <td class="right">'.number_format($taxjanaug,2).'</td>
                </tr>
                <tr>
                    <td class="left">Reg Pay Sep - Dec (net of EA)</td>
                    <td class="right">'.number_format($gisepdec,2).'</td>
                    <td class="right">'.number_format($ntcontrisepdec,2).'</td>
                    <td class="right">'.number_format($taxsepdec,2).'</td>
                </tr>
                <tr>
                    <td class="left">De Minimis</td>
                    <td class="right"></td>
                    <td class="right"></td>
                    <td class="right"></td>
                </tr>
                <tr>
                    <td class="left">Salary Adj. June-Aug</td>
                    <td class="right">'.number_format($saladj,2).'</td>
                    <td class="right"></td>
                    <td class="right">'.number_format($saladj,2).'</td>
                </tr>
            </table>
        </div>
    </div>
   '; 
}

$towrite = "
	<style type='text/css'>
        .fixed{
            table-layout:fixed; 
            border-collapse: collapse;
        }
		body{
			font-family: 'Trebuchet MS', Arial, Verdana;
            font-size: 10px;
        }
        .btm-style{
            font-family: 'Trebuchet MS', Arial, Verdana;
            font-size: 7px;
        }
        .container{
		    border: 1px solid black;
            margin-top: 5px;
            margin-left; 3%;
            width: 100%;
            height: 33%;
            float: left;
        }
        .left-box{
            border: 1px solid black;
            margin-top: 5px;
            margin-left; 3%;
            width: 31%;
            height: 40%;
            float: left;
        }
        .header{
            text-align: center;
        }
        .left{
            text-align: left;
        }
        .right{
            text-align: right;
        }
        .center{
            text-align: center;
        }
        table{
            width: 100%;
            padding: 2mm;
        }
        td{
            text-align: center;
        }
        .border-top{
            border-top: 1px solid black;
        }
        .border-bottom{
            border-bottom: 1px solid black;
        }
        .footer{
            margin-left: 1%;
            width: 99%;
            height: 5%;   
            /*border: 1px solid black;*/
        }
        .footer .text{
            margin-top: 2%;
            font-weight: bold;
        }
	</style>
</head>
<body>
".$html."
</body>";

$mpdf->WriteHTML($towrite);

$mpdf->Output();
#$mpdf->Output("pdf.pdf","D");