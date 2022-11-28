<?php

/**
 * @author Justin
 * @copyright 2015
 * GOOD LUCK!
 */

function head($eid,$schedule,$quarter,$dfrom,$dto,$dept){    
$cutoffdate = (date('F Y',strtotime($dfrom)) == date('F Y',strtotime($dto))) ? date('F d',strtotime($dfrom)).' -  '.date('d, Y',strtotime($dto)) : date('F d',strtotime($dfrom)).' -  '.date('F d, Y',strtotime($dto));
$html .= '
<div class="header">
    <span><b>ST. JUDE CATHOLIC SCHOOL</b><span><br />
    <span><b>PAYROLL DETAIL</b><span><br />
    <span><b>'.$cutoffdate.'</b><span><br />
    <span><b>'.getEmpDesc("",$dept).'</b></span>
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
                    <th>'.$row.'</th>
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
                    <th>'.$row.'</th>
             ';
             
    }
}

# Other Deductions #
if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc',true)){
    foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc',true) as $row){
    $html .= '
                    <th>'.$row.'</th>
             ';
             
    }
}
          
$html .= '  
                <th>Total Ded</th>
                <th>Net Pay</th>';
return $html;
}

function content($fullname,$deptd,$rate,$eid,$schedule,$quarter,$dfrom,$dto){
    $gross = $tgross = $totalded = $tded = 0;
    $tarr = array();
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
        $tarr[] = $row; 
        $gross += $row;
        }
    }
    
    $html .= '      <td>0</td>';    # 13th Month Pay
    $tarr[] = 0;                    # 13th Month Pay
    
    $html .= '      <th>'.number_format($gross,2).'</th> <!-- Gross Income -->';
    $tarr[] = $gross;
    
    # Fixed Deduction #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc')){
        foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'fixeddeduc') as $row){
        $html .= '
                        <td>'.number_format($row,2).'</td>
                 ';
        $tarr[] = $row;
        $totalded += $row;         
        }
    }
    
    $whtax = getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'withholdingtax');
    $html .= '          <td>'.number_format($whtax,2).'</td>        <!-- WithHolding Tax -->';
    $totalded += $whtax;
    $tarr[]    = $whtax;
    
    # Loans #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan')){
        foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'loan') as $row){
        $html .= '
                        <td>'.number_format($row,2).'</td>
                 ';
        $tarr[] = $row;
        $totalded += $row;
        }
    }
    
    # Oth Deduct #
    if(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc')){
        foreach(getData($eid,$schedule,$quarter,$dfrom,$dto,$dept,'otherdeduc') as $row){
        $html .= '
                        <td>'.number_format($row,2).'</td>
                 ';
        $tarr[] = $row;
        $totalded += $row;
        }
    }
    
    $html .= '      <th>'.number_format($totalded,2).'</th>        <!-- Total Deduction --> ';
    $tarr[] = $totalded;
    
    $html .= '      <th>'.number_format(($gross - $totalded),2).'</th>        <!-- Net Pay --> ';
    $tarr[] = ($gross - $totalded);
    
    $html .= '
                </tr>
             ';
    
    $html .= '
                </tr>
             ';

return array($html,$tarr);
}

function total($tsalary,$tsumarr){
    
    $html .= '<tr>
            <th colspan=2 style="text-align: right">GRAND TOTAL</th>
            <th>'.number_format($tsalary,2).'</th>';
        foreach($tsumarr as $key=>$row){
            if($row > 0)    $gtotal = number_format($row,2);
            else            $gtotal = $row;
            $html .= '<th>'.$gtotal.'</th>';
        }
    
    return $html;
}


?>