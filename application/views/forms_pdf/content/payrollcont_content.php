<?php

/**
 * @author Justin
 * @copyright 2015
 */

function head($eid,$schedule,$quarter,$dfrom,$dto,$dept){    
$cutoffdate = (date('F Y',strtotime($dfrom)) == date('F Y',strtotime($dto))) ? date('F d',strtotime($dfrom)).' -  '.date('d, Y',strtotime($dto)) : date('F d',strtotime($dfrom)).' -  '.date('F d, Y',strtotime($dto));
$html .= '
<div class="header">
    <span><b>ST. JUDE CATHOLIC SCHOOL</b><span><br />
    <span><b>PAYROLL CONTRIBUTIONS AND LOANS</b><span><br />
    <span><b>'.$cutoffdate.'</b><span>
</div>
<div class="body">
    <table border="1">
            <tr>
                <th rowspan=1>NAME</th>
                <th rowspan=1>Department</th>
';

# Fixed Deduction #
if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true)){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true) as $row){
    if(strcasecmp($row,"sss") == 0)  $cspan = " colspan=3";
    else                             $cspan = " colspan=2";
    $html .= '
                    <th '.$cspan.'>'.$row.'</th>
             ';
             
    }
}

$html .= '                   
                <th rowspan=1>'.date('Y',strtotime($dfrom)).' WTAX per payday</th>
          ';
          
# Loans #
if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan',true)){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan',true) as $row){
    $html .= '
                    <th rowspan=1>'.loandesc($row).'</th>
             ';
             
    }
}

# Other Deductions #
if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc',true)){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc',true) as $row){
    $html .= '
                    <th rowspan=1>'.deductiondesc($row).'</th>
             ';
             
    }
}
          
$html .= '  
            </tr>';

# Fixed Deduction #
$html .= '
            <tr>
         ';
if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true)){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true) as $row){
    if(strcasecmp($row,"sss") == 0){
        $nrow = ' <th>EC</th>';
    }
    $html .= '
                    <th>EE</th>
                    <th>ER</th>
                    '.$nrow.'
                ';
   }  
}
$html .= '
            </tr>
         ';
         
return $html;
}

function content($fullname,$deptd,$rate,$eid,$schedule,$quarter,$dfrom,$dto,$dept){
    $gross = $tgross = $totalded = $tded = 0;
    $html .= '
                <tr>
                    <td>'.$fullname.'</td>
                    <td>'.$deptd.'</td>
             ';
    
    $gross += $rate;
    
    # Fixed Deduction #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true)){
        foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true) as $row){
        $amt = getDataamt($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',$row);
        if(strcasecmp($row,"sss") == 0){
            $tbl  = "sss_deduction";
            $nrow = ' <th>'.getincattr("emp_con",$tbl,$amt).'</th>';
        }else if(strcasecmp($row,"philhealth") == 0){
            $tbl  = "philhealth_deduction";
            $nrow = "";
        }else{
            $tbl  = "pagibig";
        }
        if($tbl != "pagibig"){
            $html .= '
                            <th>'.getincattr("emp_ee",$tbl,$amt).'</th>
                            <th>'.getincattr("emp_er",$tbl,$amt).'</th>
                            '.$nrow.'
                        ';
        }else{
            $html .= '
                            <th>'.$amt.'</th>
                            <th>'.$amt.'</th>
                        ';
        }
        
       }
    }
    
    $html .= '
                    <th>'.getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'withholdingtax').'</th>        <!-- WithHolding Tax -->
             ';
             $totalded += getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'withholdingtax');
    
    # Loans #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan')){
        foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan') as $row){
        $html .= '
                        <th>'.number_format($row,2).'</th>
                 ';
        $totalded += $row;
        }
    }
    
    # Other Deduc #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc')){
        foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc') as $row){
        $html .= '
                        <th>'.number_format($row,2).'</th>
                 ';
        $totalded += $row;
        }
    }
    
    $html .= '
                </tr>
             ';
    
    
    # TOTAL
    $trate  = getTotal($eid,$schedule,$quarter,$dfrom,$dto,$dept,'salary');        
    $html .= '
                <tr>
                    <th></td>
                    <th>GRAND TOTAL</th>
             ';
    
    $tgross += $trate;
    /*
    # Fixed Deduction #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc')){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true) as $ttle){
        $tfdeduc = getTotal($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',$ttle);
        $html .= '
                    <th>'.number_format($tfdeduc,2).'</th>
                 ';
        $tded += $tfdeduc;         
    }
    }
    */
    # Fixed Deduction #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true)){
        foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true) as $row){
        $ec = $er = "0";
        $tfdeduc = getTotali($eid,$schedule,$quarter,$dfrom,$dto,$dept,$row,"","");
        $ptfdeduc = getTotali($eid,$schedule,$quarter,$dfrom,$dto,$dept,'Pagibig',"","");
        
        if(strcasecmp($row,"sss") == 0){
            $tbl  = "sss_deduction";
            $ec = getTotali($eid,$schedule,$quarter,$dfrom,$dto,$dept,$row,"emp_con",$tbl);
            $nrow = ' <th>'.number_format($ec,2).'</th>';
        }else if(strcasecmp($row,"philhealth") == 0){
            $tbl  = "philhealth_deduction";
            $nrow = "";
        }else{
            $tbl  = "pagibig";
        }
        if($tbl != "pagibig"){
            $er = getTotali($eid,$schedule,$quarter,$dfrom,$dto,$dept,$row,"emp_er",$tbl);
            $html .= '
                            <th>'.number_format($tfdeduc,2).'</th>
                            <th>'.number_format($er,2).'</th>
                            '.$nrow.'
                        ';
        }else{
            $html .= '
                            <th>'.number_format($ptfdeduc,2).'</th>
                            <th>'.number_format($ptfdeduc,2).'</th>
                        ';
        }
        
       }  
    }
    
    $whtax = getTotalWHTax($eid,$schedule,$quarter,$dfrom,$dto,$dept);
    $html .= '
                    <th>'.number_format($whtax,2).'</th>        <!-- WithHolding Tax -->
             ';
          #   $tded += getTotal($eid,$schedule,$quarter,$dfrom,$dto,$dept,'withholdingtax');
    
    # Loans #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan')){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan',true) as $ttle){
        $tloans = getTotal($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan',$ttle);
        $html .= '
                        <th>'.number_format($tloans,2).'</th>
                 ';
        $tded += $tloans;
    }
    }
    
    # Other Deduc 
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc')){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc',true) as $ttle){
        $oth = getTotal($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc',$ttle);
        $html .= '
                        <th>'.number_format($oth,2).'</th>
                 ';
        $tded += $oth;
    }
    }
    
    
    $html .= '
                </tr>
             ';
    
    
    
    
    $html .= '
                </table>
             ';
    
    $html .= 
            '</div>';

return $html;
}


?>