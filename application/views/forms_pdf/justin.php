<?php

/**
 * @author Justin
 * @copyright 2016
 */
include "application/config/connection.php";
include "application/views/forms_pdf/function/payrollfunc.php";
$mpdf = new mPDF('utf-8','A3','10','','3','3','6','10','9','9');

$eid        = $_GET['eid']; 
$dept       = $_GET['dept'];
$dfrom      = $_GET['dfrom'];
$dto        = $_GET['dto'];
$schedule   = $_GET['schedule'];
$quarter    = $_GET['quarter']; 
$sort       = $_GET['sort'];

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
    $total = 0;
    $taxable = 0;
    $query = mysql_query("SELECT income FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $income = $data['income'];
    
    $exincome = explode("/",$income);
    for($x = 0;$x < count($exincome); $x++){
        $iexincome = explode("=",$exincome[$x]);
        if($iexincome[1] != 0){
        $return .= "<tr>
                        <td class='eddesc'>".incomedesc($iexincome[0])."</td>
                        <td class='edamt'> ".number_format($iexincome[1],2)."</td>
                    </tr>                        
                   ";
        $total += $iexincome[1];
        
        $querytax = mysql_query("SELECT description FROM payroll_income_config WHERE description='".strtoupper($iexincome[0])."' AND taxable='withtax'");
        if(mysql_num_rows($querytax) > 0)   $taxable += $iexincome[1];
        }
        
    }
    return array($return,$total,$taxable);
}

function displayContribution($eid,$schedule,$quarter,$dfrom,$dto){
    $return = "";
    $total = 0;
    $query = mysql_query("SELECT fixeddeduc FROM payroll_computed_table a INNER JOIN employee b ON a.employeeid = b.employeeid WHERE a.employeeid='$eid' AND schedule='$schedule' AND quarter='$quarter' AND cutoffstart='$dfrom' AND cutoffend='$dto'");
    $data = mysql_fetch_array($query);
    $fixeddeduc = $data['fixeddeduc'];
    
    $exfixeddeduc = explode("/",$fixeddeduc);
    for($x = 0;$x < count($exfixeddeduc); $x++){
        $iexfixeddeduc = explode("=",$exfixeddeduc[$x]);
        if($iexfixeddeduc[1] != 0){
        $return .= "<tr>
                        <td class='eddesc'>".$iexfixeddeduc[0]."</td>
                        <td class='edamt'> ".number_format($iexfixeddeduc[1],2)."</td>
                    </tr>                        
                   ";
        $total += $iexfixeddeduc[1];
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
                        <td class='eddesc'>".loandesc($iexloan[0])."</td>
                        <td class='edamt'> ".number_format($iexloan[1],2)."</td>
                    </tr>                        
                   ";
        $total += $iexloan[1];
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
                        <td class='eddesc'>".deductiondesc($iexotherdeduc[0])."</td>
                        <td class='edamt'> ".number_format($iexotherdeduc[1],2)."</td>
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
        $return .= "<tr>
                        <td class='eddesc'>$col ".date('M',strtotime($dtrcutoff))."</td>
                        <td class='edamt'> ".number_format($dtrdeduc,2)."</td>
                    </tr>
                   ";
        $total += $dtrdeduc;
    }
    
    return array($return,$total);
}

$data = $this->payroll->SlipRecord($eid,$schedule,$quarter,$dfrom,$dto,$dept,$sort);
foreach($data as $row){
    $cutoffdate = (date('F Y',strtotime($row->cutoffstart)) == date('F Y',strtotime($row->cutoffend))) ? date('F d',strtotime($row->cutoffstart)).' -  '.date('d, Y',strtotime($row->cutoffend)) : date('F d',strtotime($row->cutoffstart)).' -  '.date('F d, Y',strtotime($row->cutoffend));
    $cutoffdate2 = (date('M Y',strtotime($row->cutoffstart)) == date('M Y',strtotime($row->cutoffend))) ? date('M d',strtotime($row->cutoffstart)).' -  '.date('d, Y',strtotime($row->cutoffend)) : date('M d',strtotime($row->cutoffstart)).' -  '.date('M d, Y',strtotime($row->cutoffend));
    
    list($dincome,$tincome,$taxableloan) = displayIncome($row->employeeid,$schedule,$quarter,$dfrom,$dto);
    list($dcontribution,$tcontribution)  = displayContribution($row->employeeid,$schedule,$quarter,$dfrom,$dto);
    list($dloan,$tloan)                  = displayLoan($row->employeeid,$schedule,$quarter,$dfrom,$dto);
    list($dothdeduc,$tothdeduc)          = displayOthDeduc($row->employeeid,$schedule,$quarter,$dfrom,$dto);
    list($tardydeduc,$totardydeduc)      = displaydtrDeduc($row->employeeid,$schedule,$quarter,$dfrom,$dto,'tardy','Tardy');
    list($absentdeduc,$toabsentdeduc)    = displaydtrDeduc($row->employeeid,$schedule,$quarter,$dfrom,$dto,'absents','Excess Absent');
    
    $earnings   = number_format($row->salary + $tincome,2);
    $deductions = number_format($tcontribution + $tloan + $tothdeduc + $totardydeduc + $toabsentdeduc + $row->withholdingtax,2);
    $netpay     = number_format(($row->salary + $tincome) - ($tcontribution + $tloan + $tothdeduc + $totardydeduc + $toabsentdeduc + $row->withholdingtax),2);  
    
$html .= '
<div class="containerleft">
    <div class="content-left">
        <div class="space"></div>
        <div class="contenttext">
        <br />
        I acknowledge to have received from<br /> 
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ST. JUDE CATHOLIC SCHOOL<br />
        the amount stated below and have no<br /> further claims for services rendered.
        <br /><br />
        <table style="font-size: 12px;">
            <tr>
                <td width="22%">ATM &#8216;021</td>
                <td width="1%">:</td>
                <td colspan="4">'.$row->emp_accno.'</td>
            </tr>
            <tr>
                <td width="22%">Date</td>
                <td width="1%">:</td>
                <td width="14%">'.date('m/d/y').'</td>
                <td width="2%">Rate</td>
                <td width=".5%">:</td>
                <td width="15%">'.number_format($row->salary,2).'</td>
            </tr>
            <tr>
                <td width="22%">Period</td>
                <td width="1%">:</td>
                <td colspan="4">'.$cutoffdate2.'</td>
            </tr>
            <tr>
                <td width="22%">Dept</td>
                <td width="1%">:</td>
                <td colspan="4">'.getEmpDesc($row->employeeid).'</td>
            </tr>
            <tr>
                <td width="22%">Emp #</td>
                <td width="1%">:</td>
                <td colspan="4">'.$row->employeeid.'</td>
            </tr>
            <tr>
                <td width="22%">Name</td>
                <td width="1%">:</td>
                <td colspan="4">'.$row->fullname.'</td>
            </tr>
            <tr>
                <td width="22%">Net Pay</td>
                <td width="1%">:</td>
                <td colspan="4">'.$netpay.'</td>
            </tr>
        </table>
        <br />
        <hr width="97%">
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         Signature
        </div>
    </div>
</div>
<div class="container">
    <div class="header">
        <div style="height: 10px; width: 100%;text-align: right;">'.substr($row->employeeid,-3).'</div>
        <h2>SAINT JUDE CATHOLIC SCHOOL</h2>
        <h2>P A Y  S L I P</h2>
    </div>
    <div class="body">
        <table class="fixed">
            <tr>
                <th width="50%" style="text-align: left;">Employee : '.$row->employeeid.'&nbsp;&nbsp;&nbsp;'.$row->fullname.'</th>
                <th width="50%" style="text-align: left;">ATM : '.$row->emp_accno.'</th>
            </tr>
            <tr>
                <th style="text-align: left;">Pay Period : '.$cutoffdate.'</td>
                <th style="text-align: left;">Dept : '.getEmpDesc($row->employeeid).'</td>
            </tr>
        </table>
        <table>
            <tr>
                <th class="tableheader">Earnings</th>
                <th class="tableheader" style="text-align: right;">Amount</th>
                <th class="tableheader">Deductions</th>
                <th class="tableheader" style="text-align: right;">Amount</th>
            </tr>      
        </table>
        
        <!-- EARNINGS -->
        <div class="earnings">
            <table class="edtbl">
                <tr>
                    <td class="eddesc">Regular Pay</td>
                    <td class="edamt">'.number_format($row->salary,2).'</td>
                </tr>
                '.$dincome.'
            </table>
        </div>
        <!-- DEDUCTIONS -->
        
        <div class="deduction" style="margin-left: 2px;">
            <table class="edtbl">
                <tr>
                    <td class="eddesc">WithHolding Tax</td>
                    <td class="edamt">'.number_format($row->withholdingtax,2).'</td>
                </tr>
                '.$dcontribution.'
                '.$dloan.'
                '.$dothdeduc.'
                '.$tardydeduc.'
                '.$absentdeduc.'
            </table>
        </div>
        
        <!-- DEDUCTIONS -->
        
        <div class="footer">
            <table class="edtbl">
                <tr>
                    <td class="eddesc" style="text-align: right;" width="16%">Total Earnings:</td>
                    <td class="eddesc" style="text-align: left;" width="16%"><b>'.$earnings.'</b></td>
                    <td class="eddesc" style="text-align: right;" width="16%">Total Deduction:</td>
                    <td class="eddesc" style="text-align: left;" width="16%"><b>'.$deductions.'</b></td>
                    <td class="eddesc" style="text-align: right;" width="16%">Net Pay:</td>
                    <td class="eddesc" style="text-align: left;" width="16%"><b>'.$netpay.'</b></td>
                </tr>
            </table>
            <div class="text">
                <div style="margin-left: 10%"><img src="'.site_url('forms/loadForm').'?form=imgview&eid=hcs" width="10%" height="5%"/></div>
                <div>Prepared by : <u>'.headCashier().' - Head Cashier</u></div>
            </div>
        </div>
        
    </div>
</div>
';
}

$towrite = "
	<style type='text/css'>
        .content-left{
            width: 100%;
            height: 30%;
        }
        .space{
            width: 100%;
            height: 2.5%;
        }
        .contenttext{
            margin-left: 2%;
            width: 95%;
            height: 24.5%;
            border-right: 1px solid black;
            border-style: dashed;            
            text-align: justify;
            text-justify: inter-word;
            font-size: 12px;
        }
        .fixed{
            table-layout:fixed; 
        }
		body{
			font-family: 'Trebuchet MS', Arial, Verdana;
            font-size: 12px;
		}
        .containerleft{
            margin-top: 5px;
            width: 27%;
            height: 33%;
            float: left;
        }
        .container{
		    /*border: 1px solid black;*/
            margin-top: 5px;
            margin-left; 3%;
            width: 72%;
            height: 33%;
            float: left;
        }
        .header{
            text-align: center;
        }
        table{
            width: 100%;
        }
        .tableheader{
            /*font-size: 15px;*/
            /*font-size: 8px;*/
            border-bottom-width: 1px;
            border-bottom-style: solid;
            border-bottom-color: #CDC1A7;
            border-top-width: 1px;
            border-top-style: solid;
            border-top-color: #CDC1A7;
            text-align: left;
        }
        .earnings{
            float: left;
            /*border: 1px solid black;*/
            width: 46.6%;
            height: 16%;
            text-align: right;
        }
        .deduction{
            float: left;
            /*border: 1px solid black;*/
            width: 52.6%;
            height: 16%;
            text-align: right;
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
        .edtbl{
            width: 100%;
        }
        .eddesc{
            text-align: left;
        }
        .edamt{
            text-align: right;
        }
	</style>
</head>
<body>
".$html."
</body>";

$mpdf->WriteHTML($towrite);

#$mpdf->Output();
#$mpdf->Output("$eid.pdf","D");
$mpdf->Output($_SERVER['DOCUMENT_ROOT']."/stjudedtr/pdf/$eid.pdf", "F",true);
