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
    <span><b>PAYROLL DETAIL</b><span><br />
    <span><b>'.$cutoffdate.'</b><span>
</div>
<div class="body">
    <table border="1">
            <tr>
                <th>NAME</th>
                <th>Department</th>
                <th>Regular Pay</th>
';

# Income #
if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'income',true)){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'income',true) as $row){
    $html .= '
                    <th>'.incomedesc($row).'</th>
             ';
             
    }
}
         
$html .= '
                <th>13th Month</th>
                <th>Gross Income</th>
         ';

# Fixed Deduction #
if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true)){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc',true) as $row){
    $html .= '
                    <th>'.$row.'</th>
             ';
             
    }
}

$html .= '                   
                <th>'.date('Y',strtotime($dfrom)).' WTAX per payday</th>
          ';
          
# Loans #
if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan',true)){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan',true) as $row){
    $html .= '
                    <th>'.loandesc($row).'</th>
             ';
             
    }
}

# Other Deductions #
if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc',true)){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc',true) as $row){
    $html .= '
                    <th>'.deductiondesc($row).'</th>
             ';
             
    }
}
          
$html .= '  
                <th>Total Ded</th>
                <th>Net Pay</th>
            </tr>';
return $html;
}

function content($fullname,$deptd,$rate,$eid,$schedule,$quarter,$dfrom,$dto,$dept){
    $gross = $tgross = $totalded = $tded = 0;
    $html .= '
                <tr>
                    <td>'.$fullname.'</td>
                    <td>'.$deptd.'</td>
                    <td>'.number_format($rate).'</td>
             ';
    
    $gross += $rate;
    
    # Income #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'income')){
        foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'income') as $row){
        $html .= '
                    <td>'.number_format($row,2).'</td>
                 ';
        $gross += $row;
        }
    }
    
    $html .= '
                    <th>0</th>
                    <th>'.number_format($gross,2).'</th> <!-- Gross Income -->
             ';
    
    # Fixed Deduction #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc')){
        foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc') as $row){
        $html .= '
                        <th>'.number_format($row,2).'</th>
                 ';
        $totalded += $row;         
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
                    <th>'.number_format($totalded,2).'</th>        <!-- Total Deduction -->
                    <th>'.number_format(($gross - $totalded),2).'</th>        <!-- Net Pay -->
             ';
    
    $html .= '
                </tr>
             ';
    
    
    # TOTAL
    $trate  = getTotal($eid,$schedule,$quarter,$dfrom,$dto,$dept,'salary');        
    $html .= '
                <tr>
                    <th></td>
                    <th>GRAND TOTAL</th>
                    <th>'.number_format($trate).'</th>
             ';
    
    $tgross += $trate;
    
    # TOTAL Income #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'income')){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'income',true) as $ttle){
      $tincome = getTotal($eid,$schedule,$quarter,$dfrom,$dto,$dept,'income',$ttle);  
      $html .= '
                    <th>'.number_format($tincome,2).'</th>
               ';
      $tgross += $tincome;
    }
    }
    
    $html .= '
                    <th>0</th>
                    <th>'.number_format($tgross,2).'</th> <!-- Gross Income -->
             ';
    
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
    
    $whtax = getTotalWHTax($eid,$schedule,$quarter,$dfrom,$dto,$dept);
    $html .= '
                    <th>'.$whtax.'</th>        <!-- WithHolding Tax -->
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
                    <th>'.number_format($tded,2).'</th>        <!-- Total Deduction -->
                    <th>'.number_format(($tgross - $tded),2).'</th>        <!-- Net Pay -->
             ';
    
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