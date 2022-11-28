<?php

/**
 * @author Justin
 * @copyright 2015
 */
require_once(APPPATH."constants.php");

$eid        = $eid;
$dept       = $dept;
$office       = $office;
$dfrom      = $dfrom;
$dto        = $dto;
$schedule   = $schedule;
$quarter    = $quarter;
$sort       = $sort;
$campus       = $campus;
$CI =&get_instance();
$CI->load->model('payrollcomputation');

include "application/config/connection.php";
include "application/views/forms_pdf/function/payrollfunc.php";
if($eid == ''){
  $mpdf = new mPDF('utf-8','A4-L','10','','10','10','10','10','9','9');
}
else{
  $custom_layout = array('180', '220');  
  $mpdf = new mpdf('P',$custom_layout,'','UTF-8',0,0,0,0);
}

list($sel_year, $sel_month, $sel_day) = explode("-", $dfrom);
$workhours_perdept = $this->payroll->getWorkHoursPerdept($eid, $dfrom, $dto);
function getEmpDesc($eid){
    $return = "";
    $query = mysql_query("SELECT b.description FROM employee a INNER JOIN code_office b ON a.deptid = b.code WHERE a.employeeid='$eid'");
    $data = mysql_fetch_array($query);
    $return = $data['description'];
    return $return; 
}

function headCashier(){
    $return = "";
    $query = mysql_query("SELECT headcashier FROM config_cashier");
    $data = mysql_fetch_array($query);
    $return = $data['headcashier'];
    return $return;
}

function displayIncome($eid,$schedule,$quarter,$dfrom,$dto){
    $return = "";
    $total =$ttotal= 0;
    $demi = $other = 0;
    $query = mysql_query("SELECT * FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $income = $data['income'];
    $ottime = $data['overtime'];
    $count = 1;
    $exincome = explode("/",$income);
    for($x = 0;$x < count($exincome); $x++){
       
        $iexincome = explode("=",$exincome[$x]);
        if($iexincome[1] != 0  ){
        $count ++;
       
       $querytax = mysql_query("SELECT description FROM payroll_income_config WHERE id='".strtoupper($iexincome[0])."' AND taxable='withtax'");
            if(mysql_num_rows($querytax) > 0)   
                {
                $displayTaxable .= "<tr>
                            <td colspan='2' align='left' class='eddesc'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".incomedesc($iexincome[0])."</td>
                            <td colspan='2' align='right' class='edamt'> ".number_format($iexincome[1],2)."</td>
                        </tr>                        
                       ";
                    $totalTaxableIncome += $iexincome[1];
                }
            else{
                $displayNonTaxable .= "<tr>
                            <td colspan='2' align='left' class='eddesc'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".incomedesc($iexincome[0])."</td>
                            <td colspan='2' align='right' class='edamt'> ".number_format($iexincome[1],2)."</td>
                        </tr>                        
                       ";
                $totalNonTaxableIncome += $iexincome[1];
            }

        $querydemi = mysql_query("SELECT description FROM payroll_income_config WHERE id='".strtoupper($iexincome[0])."' AND incomeType='deminimiss'");
        if (mysql_num_rows($querydemi) > 0) {
           $return .= "
                   <tr>
                        <td colspan='2' class='eddesc'>".incomedesc($iexincome[0])."</td>
                        <td colspan='2' class='edamt'> ".number_format($iexincome[1],2)."</td>
                    </tr>                        
                   ";
           $total +=  $iexincome[1];
        }
        
        $queryother = mysql_query("SELECT description FROM payroll_income_config WHERE id='".strtoupper($iexincome[0])."' AND  (ISNULL(incometype) OR  incomeType='other')");
        if(mysql_num_rows($queryother) > 0) {
          $other += $iexincome[1];
        }
         

        }
    }
    if ($ottime != 0) {
        $return .= "<tr>
                        <td class='eddesc'>Overtime</td>
                        <td class='edamt'> ".number_format($ottime,2)."</td>
                    </tr>                        
                   ";
    }
    else
    {
       if ($count == 0) {
            for ($i=1; $i <= 30; $i++) { 
              $return .= "
                 <tr>
                  <td class='eddesc'></td>
                  <td class='edamt'> </td>
                 </tr>
              ";
            }
        }
        else if ($count == 1) {
            for ($i=1; $i <= 38 ; $i++) { 
              $return .= "
                 <tr>
                  <td class='eddesc'></td>
                  <td class='edamt'> </td>
                 </tr>
              ";
            }
        }
         else if ($count == 2) {
            for ($i=1; $i <= 38; $i++) { 
              $return .= "
                 <tr>
                  <td class='eddesc'></td>
                  <td class='edamt'> </td>
                 </tr>
              ";
            }
        }
         else if ($count == 3) {
            for ($i=1; $i <= 38 ; $i++) { 
              $return .= "
                 <tr>
                  <td class='eddesc'></td>
                  <td class='edamt'> </td>
                 </tr>
              ";
            }
        }
       else if ($count == 4) {
            for ($i=1; $i <= 37 ; $i++) { 
              $return .= "
                 <tr>
                  <td class='eddesc'></td>
                  <td class='edamt'> </td>
                 </tr>
              ";
            }
        }
        else if ($count == 5) {
            for ($i=1; $i <= 34; $i++) { 
              $return .= "
                 <tr>
                  <td class='eddesc'></td>
                  <td class='edamt'> </td>
                 </tr>
              ";
            }
        }
        else if ($count == 6) {
            for ($i=1; $i <= 33 ; $i++) { 
              $return .= "
                 <tr>
                  <td class='eddesc'></td>
                  <td class='edamt'> </td>
                 </tr>
              ";
            }
        }
        else if ($count == 7) {
            for ($i=1; $i <= 30 ; $i++) { 
              $return .= "
                 <tr>
                  <td class='eddesc'></td>
                  <td class='edamt'> </td>
                 </tr>
              ";
// echo "<pre>"; print_r($this->db->last_query()); die;
            }
        }
       
    // echo "<pre>"; print_r($total); die;

    }
    return array($displayTaxable,$displayNonTaxable,$totalTaxableIncome,$totalNonTaxableIncome,$return,$total,$other,$count);
}

function displayContribution($eid,$schedule,$quarter,$dfrom,$dto){
    $return = "";
    $total = 0;
    $query = mysql_query("SELECT fixeddeduc FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $fixeddeduc = $data['fixeddeduc'];
    
    $labelArray = array();
    $amntArray = array();
    $efixeddeduc = explode("/", $fixeddeduc);
    

    
    for($x=0;$x < count($efixeddeduc);$x++){
      $eefixeddeduc = explode("=", $efixeddeduc[$x]);  
      array_push($labelArray, $eefixeddeduc[0]);
      $amntArray[$eefixeddeduc[0]] = $eefixeddeduc[1];
    }


    $fixedArray = array("PAGIBIG","PHILHEALTH","SSS");
    for($i=0;$i < count($fixedArray);$i++){

      if(in_array($fixedArray[$i], $labelArray)){
        $return .= "<tr>
                        <td colspan='2' align='left' class='eddesc'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$fixedArray[$i]."</td>
                        <td colspan='2' align='right' class='edamt'>".number_format($amntArray[$fixedArray[$i]],2)."</td>
                        
                    </tr>                        
                   ";
        $total += $amntArray[$fixedArray[$i]];
      }else{
        $return .= "<tr>
                        <td colspan='2' align='left' class='eddesc'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$fixedArray[$i]."</td>
                        <td colspan='2' align='right' class='edamt'>0.00</td>
                    </tr>                        
                   ";

      }
    }
    
    
    return array($return,$total);
}

function displayLoan($eid,$schedule,$quarter,$dfrom,$dto){
    $return = "";
    $total = 0;
    $query = mysql_query("SELECT loan FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $loan = $data['loan'];
    
    $exloan = explode("/",$loan);
    for($x = 0;$x < count($exloan); $x++){
        $iexloan = explode("=",$exloan[$x]);
        if($iexloan[1] != 0){
        $return .= "<tr>
                        <td colspan='2' align='left' class='eddesc'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".loandesc($iexloan[0])."</td>
                        <td colspan='2' align='right' class='edamt'> ".number_format($iexloan[1],2)."</td>
                    </tr>                        
                   ";
        $total += $iexloan[1];
// echo "<pre>"; print_r($total); die;
        }
    }
    return array($return,$total);
}

function displayOthDeduc($eid,$schedule,$quarter,$dfrom,$dto){
    $return = "";
    $total = 0;
    $query = mysql_query("SELECT otherdeduc FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $otherdeduc = $data['otherdeduc'];
    
    $exotherdeduc = explode("/",$otherdeduc);
    for($x = 0;$x < count($exotherdeduc); $x++){
        $iexotherdeduc = explode("=",$exotherdeduc[$x]);
        if($iexotherdeduc[1] != 0){
        $return .= "<tr>
                        <td class='eddesc' colspan='2' align='left'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HMO(".deductiondesc($iexotherdeduc[0]).")</td>
                        <td class='edamt' colspan='2' align='right'> ".number_format($iexotherdeduc[1],2)."</td>
                    </tr>                        
                   ";
        $total += $iexotherdeduc[1];
        }
    }
    return array($return,$total);
}

function displaydtrDeduc($eid,$schedule,$quarter,$dfrom,$dto,$title,$col){
    $return = "";
    $total = 0;
    $query = mysql_query("SELECT $title as dtrdeduc,
                         (SELECT startdate FROM payroll_employee_deductions WHERE deductcutoffstart='$dfrom' AND deductcutoffend='$dto' AND employeeid='$eid') as dtrcutoff 
                         FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $dtrdeduc = $data['dtrdeduc'];
    $dtrcutoff = $data['dtrcutoff'];
    if($dtrdeduc > 0){
        $return .= "<tr >
                        <td class='eddesc' colspan='2' align='left'  width='1%' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$col</td>
                        <td class='edamt' colspan='2' align='right' width='2%'> ".number_format($dtrdeduc,2)."</td>
                    </tr>
                   ";
        $total += $dtrdeduc;

    }
    
    return array($return,$total);
}

function displayIncomeAdj($eid,$schedule,$quarter,$dfrom,$dto,$payroll_config){
  if($payroll_config){
    $return = '';
    $query_payroll = mysql_query("SELECT * FROM payroll_computed_table WHERE employeeid = '$eid' AND QUARTER = '$quarter' AND SCHEDULE = '$schedule' AND cutoffstart = '$dfrom' AND cutoffend = '$dto' ");
    $data = mysql_fetch_array($query_payroll);
    $income_adj = $data['income_adj'];
    list($incadj_desc,$incadj_amount) = explode("=", $income_adj);
    $return .= "
      <tr>
        <td>".ucwords(strtolower($payroll_config[$incadj_desc]))."</td>
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$incadj_amount."</td>
      </tr>
    ";

    return array($return, $incadj_amount);
  }
}

$counter = 0;
$status = (isset($payroll_status)) ? $payroll_status : "";
$data = $this->payroll->SlipRecord($eid,$schedule,$quarter,$dfrom,$dto,$dept,$office,$campus,$sort, $status);
foreach($data as $row){
        // echo "<pre>"; print_r($data); die;
  if($row->status == "PROCESSED"){
    $counter ++;
        $cutoffdate = (date('F Y',strtotime($row->cutoffstart)) == date('F Y',strtotime($row->cutoffend))) ? date('F d',strtotime($row->cutoffstart)).' -  '.date('d, Y',strtotime($row->cutoffend)) : date('F d',strtotime($row->cutoffstart)).' -  '.date('F d, Y',strtotime($row->cutoffend));
        $cutoffdate2 = (date('M Y',strtotime($row->cutoffstart)) == date('M Y',strtotime($row->cutoffend))) ? date('M d',strtotime($row->cutoffstart)).' -  '.date('d, Y',strtotime($row->cutoffend)) : date('M d',strtotime($row->cutoffstart)).' -  '.date('M d, Y',strtotime($row->cutoffend));
        
        list($displayTaxableInc,$displayNonTaxableInc,$totTaxableInc,$totNonTaxableInc,$dincome,$tincome,$other,$count) = displayIncome($row->employeeid,$schedule,$quarter,$dfrom,$dto);
        list($displayIncomeAdj, $income_adj_amount) = displayIncomeAdj($row->employeeid,$schedule,$quarter,$dfrom,$dto,$payroll_config);
        list($dcontribution,$tcontribution)  = displayContribution($row->employeeid,$schedule,$quarter,$dfrom,$dto);
        list($dloan,$tloan)                  = displayLoan($row->employeeid,$schedule,$quarter,$dfrom,$dto);
        list($dothdeduc,$tothdeduc)          = displayOthDeduc($row->employeeid,$schedule,$quarter,$dfrom,$dto);
        list($tardydeduc,$totardydeduc)      = displaydtrDeduc($row->employeeid,$schedule,$quarter,$dfrom,$dto,'tardy','Tardy');
        list($absentdeduc,$toabsentdeduc)    = displaydtrDeduc($row->employeeid,$schedule,$quarter,$dfrom,$dto,'absents','Late/Tardiness');
                                
        $earnings   = number_format((($row->salary + $tincome + $other) - $toabsentdeduc),2);
        $deductions = number_format($tcontribution + $tloan + $tothdeduc +  $row->withholdingtax,2);
        $netpay     = number_format((($row->salary + $tincome + $other )- $toabsentdeduc) - ($tcontribution + $tloan + $tothdeduc + $totardydeduc +  $row->withholdingtax),2);  
        $adjustments = $row->tardy + $row->absents;
         // <!-- <th width="50%" style="text-align: left;">ATM : '.$row->emp_accno.'</th>-> // CODE FOR ATM IF NEEDED!
    if (!$dcontribution) {
        $dcontribution = '';}
    if (!$dloan) {
        $dloan = '';
                    }
    if (!$dothdeduc) {
        $dothdeduc = '';
                    }
    if (!$tardydeduc) {
        $tardydeduc = '';
                    }
    if (!$absentdeduc) {
        $absentdeduc = '';
                    }
    if ($row->withholdingtax !=0) {
        $wt =  number_format($row->withholdingtax,2);
    }

    # added by justin (with e) for ica-hyperion 21555
    $ytd_withholding_tax_amount = 0;
    $ytd_withholding_tax_amount = $CI->payrollcomputation->getYearToDateSummaries_whTax($row->employeeid, $sel_year, $dto);

    $html .= '

    <div class="container">
        <div width = "100%">
            <table>
                <tr>
                    <th colspan="2"><img src="'.$imgurl.'images/school_logo.jpg" style="text-align: center;'.(($eid == '') ? 'width: 70px;' : 'width: 90px; margin-top: 5%;').'" /></th>
                    <th style="'.(($eid == '') ? 'width:300px;' : 'width: 450px; margin-top: 20%;').'"><h4>Pinnacle Technologies Inc.</h4><h5>Employee PaySlip</h5></th>
                </tr>
            </table>
            <table width="100%">
                <tr>
                    <th colspan="2"></th>
                    <th colspan="1"></th>
                    <th colspan="1">&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;</th>
                    <th colspan="2">'.$cutoffdate.'</th>
                </tr>
                <tr>
                    <th colspan="2"></th>
                    <th colspan="1"></th>
                    <th colspan="1"></th>
                    <th class="totalH" colspan="2">Pay Period</th>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <th colspan="2"></th>
                    <th colspan="1"></th>
                    <th colspan="1"></th>
                    <th colspan="2">'.ucwords(strtolower($row->description)).'</th>
                </tr>
                <tr>
                    <th colspan="2"></th>
                    <th colspan="1"></th>
                    <th colspan="1"></th>
                    <th class="totalH" colspan="2">Department</th>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <th colspan="2">'.$row->employeeid.'</th>
                    <th colspan="1"></th>
                    <th colspan="1"></th>
                    <th colspan="2">'.$row->fullname.'</th>
                </tr>
                <tr>
                    <th colspan="2" class="totalH" >Employee&nbsp;No.</th>
                    <th colspan="1"></th>
                    <th colspan="1"></th>
                    <th class="totalH"  colspan="2">Employee Name</th>
                </tr>
            </table>
            <table width="100%">
                <tr>
                    <th colspan="4" class="totals"></th>
                </tr>
                <tr>
                    <td colspan="2"><h4>COMPENSATION</h4></td>
                </tr>
                <tr>
                    <td colspan="2" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Basic Salary</b></td>
                    <td colspan="2" align="right"><b>'.number_format($row->salary,2).'</b></td>
                </tr>
                '.$displayNonTaxableInc.'
                '.$displayTaxableInc.'
                <tr>
                    <td colspan="2"  >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Overtime</td>
                    <td colspan="2" align="right">'.number_format($row->overtime,2).'</td>
                </tr>
                '.
                //<tr>
                    //<td colspan="2" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Total of Other Incomes</b></td>
                    //<td colspan="2"  align="right"><b>'.number_format($other, 2).'</b></td>
                //</tr>
                '<tr>
                    <td colspan="2" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Less</b></td>
                    <td colspan="2" align="right"></td>
                </tr>
                '.$tardydeduc.'
                '.$absentdeduc.'
                <tr>
                  <td colspan="2" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Gross pay</b></td>
                  <td colspan="2" align="right" ><b>'.number_format($grossTaxable = $row->salary + $totTaxableInc + $row->overtime + floatval($totNonTaxableInc) + $income_adj_amount - $adjustments,2).'</b></td>
                </tr>
                <tr>
                    <td><h4>DEDUCTIONS</h4></td>
                </tr>
                
                '.$dcontribution.'
                '.$dothdeduc.'
                '.$tloan.'
                '.$dloan.'
                <tr>
                    <td colspan="2" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Withholding Tax</td>
                    <td colspan="2" align="right">'.number_format($row->withholdingtax,2).'</td>
                </tr>
                <tr>
                    <td colspan="2" align="left" class="eddesc"><b>TOTAL DEDUCTIONS</b></td>
                    <td colspan="2" align="right" class="edamt">'.$deductions.'</td>
                </tr>
                 <tr>
                    <th colspan="4" class="totals"></th>
                </tr>
                <tr>
                    <td colspan="2" align="right">NET PAY</td>
                    <td colspan="2" align="right">'.number_format($row->net,2).'</td>
                </tr>
                 <tr>
                    <th colspan="4" class="totals"></th>
                </tr>
                <tr>
                    <th><br></th>
                </tr>
                <tr>
                    <th colspan="1"></th>
                    <th colspan="1"></th>
                    <th align="center" class="total" colspan="2">Signature</th>
                </tr>
                <tr>
                    <th align="left">Note: Strictly Confidential</th>
                </tr>
                
            </table>
        </div>
    </div>
    ';
    if($counter < count($data)){
      if($counter % 2 == 0){
        $html .= "<pagebreak>";
      }
    }
  }
  // }else{
  //   $html .= "<pagebreak>";
  //   // $html .= '<b>NOTICE: </b>'.$this->extensions->getEmployeeName($row->employeeid).' PAYROLL IS NOT YET PROCESSED.';
  // }
}
$towrite = "
   <style type='text/css'>
        body{
            margin-top:40px;
            font-family: 'Trebuchet MS', Arial, Verdana;
            font-size: 12px;
        }
        .container{
            /*border:1px solid green;*/
            width: ".(($eid == '') ? '40%' : '85%').";
            float: left;
            margin-left:7%;
            ".(($eid == '') ? '' : 'margin-top: 20px;')."
        }
        .total{
            border-top: 1px solid black;
            text-align: right;
        }
        .totalH{
            border-top: 1px solid black;
            text-align: center;
        }
        .totals{
            border-top: 4px solid black;
            text-align: right;
        }
    </style>
</head>
<body>
".$html."
</body>";
// echo $towrite; die;
$mpdf->WriteHTML($towrite);
/*$mpdf->Output("PaySlip.pdf","D");*/
if(isset($_GET['isr'])){$mpdf->Setppage($eid);}
#$content = $mpdf->Output("","S");
$mpdf->Output();