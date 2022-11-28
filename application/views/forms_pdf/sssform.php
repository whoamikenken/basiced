<?php

/**
 * @author Justin
 * @copyright 2017
 */
 
include "application/config/connection.php";
$mpdf = new mPDF('utf-8','A4-L','10','','15','10','10','5','','0');

function getemployeeinfo($eid){
    $query = mysql_query("SELECT * FROM employee WHERE employeeid='$eid'");
    return $query;
}

function ssscontri($eid,$month,$year,$col="amounttotal"){
    $date = sprintf('%02d', $month)."-".$year;
    $query = mysql_query("SELECT $col FROM payroll_process_contribution_collection WHERE code_deduct='SSS' AND DATE_FORMAT(cutoffstart,'%m-%Y') = '$date' AND employeeid='$eid'");
    $data = mysql_fetch_array($query);
    return $data[$col];
}

$tnt = $_GET['tnt'];
$month = $_GET['period'];
$year = $_GET['pyear'];
$eid = $_GET['employeeid'];
$empinfo = mysql_fetch_array(getemployeeinfo($eid));

$html .= '
<div class="container">

    <!-- Employer Information -->
    <div class="contentidnum float">
        <p class="letterspace">1234567890123</p>
    </div>
    <div class="contentempname float">
        <p>MANILA CENTRAL UNIVERSITY</p>
    </div>
    <div class="contentquarter">
        <p>'.sprintf('%02d', $month).$year.'</p>
    </div>
    
    <div class="contenttelno float">
        <p>&nbsp;</p>
    </div>
    <div class="contentaddr float">
        <p>'.$empinfo['cityaddr'].'</p>
    </div>
    <div class="contenttype"><p>X</p></div> <!-- Regular -->
    <div class="contenttype2"><p>X</p></div> <!-- Household -->
    
    <!-- Employee Information -->
';
$x = 0;
$ftamt = $stamt = $ttamt = $ecftamt = $ecstamt = $ecttamt = "";
$query = mysql_query("SELECT * FROM employee WHERE FIND_IN_SET(employeeid,'05041500,11041400,11081015,11100800')");
    while($rs = mysql_fetch_array($query)){
        $x++;
        $ftamt += ssscontri($rs['employeeid'],($month-2),$year);
        $stamt += ssscontri($rs['employeeid'],($month-1),$year);
        $ttamt += ssscontri($rs['employeeid'],($month),$year);
        $ecftamt += ssscontri($rs['employeeid'],($month-2),$year,"ec");
        $ecstamt += ssscontri($rs['employeeid'],($month-1),$year,"ec");
        $ecttamt += ssscontri($rs['employeeid'],$month,$year,"ec");
        
        if($x == 1){
            $html .= '
                <div class="employeecontent">
                    <div class="econtent_ssnumber">
                        '.$rs['emp_sss'].'
                    </div>
                    <div class="econtent_empname">
                        '.strtoupper($rs['lname'].', '.$rs['fname'].' '.$rs['mname']).'
                    </div>
                    <div class="econtent_ssfmonth">
                        '.ssscontri($rs['employeeid'],($month-2),$year).'
                    </div>
                    <div class="econtent_sssecmonth">
                        '.ssscontri($rs['employeeid'],($month-1),$year).'
                    </div>
                    <div class="econtent_sstmonth">
                        '.ssscontri($rs['employeeid'],$month,$year).'
                    </div>
                    <div class="econtent_ecomfmonth">
                        '.ssscontri($rs['employeeid'],($month-2),$year,"ec").'
                    </div>
                    <div class="econtent_ecomsecmonth">
                        '.ssscontri($rs['employeeid'],($month-1),$year,"ec").'
                    </div>
                    <div class="econtent_ecomtmonth">
                        '.ssscontri($rs['employeeid'],$month,$year,"ec").'
                    </div>
                    <div class="econtent_sepdate">&nbsp;</div>
                </div>';
        }else{
            $html .= '
                <div class="employeecontent_nr">
                    <div class="econtent_ssnumber">
                        '.$rs['emp_sss'].'
                    </div>
                    <div class="econtent_empname">
                        '.strtoupper($rs['lname'].', '.$rs['fname'].' '.$rs['mname']).'
                    </div>
                    <div class="econtent_ssfmonth">
                        '.ssscontri($rs['employeeid'],($month-2),$year).'
                    </div>
                    <div class="econtent_sssecmonth">
                        '.ssscontri($rs['employeeid'],($month-1),$year).'
                    </div>
                    <div class="econtent_sstmonth">
                        '.ssscontri($rs['employeeid'],$month,$year).'
                    </div>
                    <div class="econtent_ecomfmonth">
                        '.ssscontri($rs['employeeid'],($month-2),$year,"ec").'
                    </div>
                    <div class="econtent_ecomsecmonth">
                        '.ssscontri($rs['employeeid'],($month-1),$year,"ec").'
                    </div>
                    <div class="econtent_ecomtmonth">
                        '.ssscontri($rs['employeeid'],$month,$year,"ec").'
                    </div>
                    <div class="econtent_sepdate">&nbsp;</div>
                </div>';
        }
    }
    for($x; $x <= 15; $x++){
        $html .= '
                <div class="employeecontent_nr">
                    <div class="econtent_ssnumber">&nbsp;</div>
                    <div class="econtent_empname">&nbsp;</div>
                    <div class="econtent_ssfmonth">&nbsp;</div>
                    <div class="econtent_sssecmonth">&nbsp;</div>
                    <div class="econtent_sstmonth">&nbsp;</div>
                    <div class="econtent_ecomfmonth">&nbsp;</div>
                    <div class="econtent_ecomsecmonth">&nbsp;</div>
                    <div class="econtent_ecomtmonth">&nbsp;</div>
                    <div class="econtent_sepdate">&nbsp;</div>
                </div>';
    }
$html .= '
                <div class="employeecontent_total">
                    <div class="econtent_ssnumber">&nbsp;</div>
                    <div class="econtent_empname">&nbsp;</div>
                    <div class="econtent_ssfmonth" style="padding-top: 0;">'.($ftamt        ? $ftamt : "&nbsp;").'</div>
                    <div class="econtent_sssecmonth" style="padding-top: 0;">'.($stamt      ? $stamt : "&nbsp;").'</div>
                    <div class="econtent_sstmonth" style="padding-top: 0;">'.($ttamt        ? $ttamt : "&nbsp;").'  </div>
                    <div class="econtent_ecomfmonth" style="padding-top: 0;">'.($ecftamt    ? $ecftamt : "&nbsp;").'</div>
                    <div class="econtent_ecomsecmonth" style="padding-top: 0;">'.($ecstamt  ? $ecstamt : "&nbsp;").'</div>
                    <div class="econtent_ecomtmonth" style="padding-top: 0;">'.($ecttamt    ? $ecttamt : "&nbsp;").'</div>
                    <div class="econtent_sepdate">&nbsp;</div>
                </div>';


$html .= '
                <div class="grandtotal">
                    <div class="ssspayment">'.($ftamt        ? $ftamt : "&nbsp;").'</div>
                    <div class="ecpayment">'.($ecftamt    ? $ecftamt : "&nbsp;").'</div>
                    <div class="gtpayment">'.($ftamt        ? number_format(($ftamt+$ecftamt),2) : "&nbsp;").'</div>
                    <div class="divspace">'.($ftamt        ? number_format(($ftamt+$ecftamt),2) : "&nbsp;").'</div>
                </div>';
$html .= '
                <div class="grandtotaloth" style="float: left;margin-top: -2px;">
                    <div class="ssspayment">'.($stamt      ? $stamt : "&nbsp;").'</div>
                    <div class="ecpayment">'.($ecstamt  ? $ecstamt : "&nbsp;").'</div>
                    <div class="gtpayment">'.($stamt        ? number_format(($stamt+$ecstamt),2) : "&nbsp;").'</div>
                    <div class="divspace">'.($stamt        ? number_format(($stamt+$ecstamt),2) : "&nbsp;").'</div>
                </div>';
$html .= '
                <div class="grandtotaloth" style="float: left;margin-top: -3px;">
                    <div class="ssspayment">'.($ttamt        ? $ttamt : "&nbsp;").'</div>
                    <div class="ecpayment">'.($ecttamt    ? $ecttamt : "&nbsp;").'</div>
                    <div class="gtpayment">'.($ttamt        ? number_format(($ttamt+$ecttamt),2) : "&nbsp;").'</div>
                    <div class="divspace">'.($ttamt        ? number_format(($ttamt+$ecttamt),2) : "&nbsp;").'</div>
                </div>';
$html .= " </div>";

# CSS Style
$towrite = "
	<style type='text/css'>
        .container{
		    border: 1px solid white;
            width: 100%;
            height: 100%;
            float: left;
            background-image: url('".base_url()."images/sss.png');
            background-repeat: no-repeat;
            background-size: 100% 100%;
            padding-top: 2px;
        }
        
        /* First Row */
        .contentidnum{
            width: 220px;
            margin-top: 85px;
            padding-left: 8px;
        }
        .contentempname{
            width: 560px;
            text-align: center;
        }
        .contentquarter{
            width: 94px;
            padding-left: 8px;
            float: right;
            letter-spacing: 0.6em;
            margin-top: 3px;
        }
        
        /* Second Row */
        .contenttelno{
            width: 220px;
            margin-top: -10px;
            padding-left: 8px;
        }
        .contentaddr{
            position: absolute;
            width: 560px;
            text-align: center;
            margin-left: 220px;
            margin-top: -50px;
            padding-left: 8px;
        }
        .contenttype{
            width: 85px;
            padding-left: 32px;
            float: left;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.6em;
            margin-top: 6px;
        }
        .contenttype2{
            width: 85px;
            padding-left: 12px;
            float: left;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.6em;
            margin-top: -14px;
            display: none;
        }
        
        /* Employee Css */
        .employeecontent{
            display:block;
            width: 100%;
            margin-top: 30px;
            padding-left: 7px;
            padding-top: 7px;
            padding-bottom: 7px;
            height: 10px;
        }
        .employeecontent_nr{
            width: 100%;
            margin-top: -15px;
            padding-left: 7px;
            padding-top: 7px;
            padding-bottom: 7px;
            height: 10px;
        }
        .econtent_ssnumber{
            width: 160px;
            float: left;
            letter-spacing: 0.62em;
            padding-top: 3px;
        }
        .econtent_empname{
            width: 308px;
            float: left;
            padding-top: 3px;
            padding-left: 8px;
        }
        .econtent_ssfmonth{
            width: 89px;
            float: left;
            padding-left: 5px;
            letter-spacing: 0.75em;
            padding-top: 3px;
            text-align: right;
        }
        .econtent_sssecmonth{
            width: 89px;
            float: left;
            padding-left: 1px;
            letter-spacing: 0.75em;
            padding-top: 3px;
            text-align: right;
        }
        .econtent_sstmonth{
            width: 89px;
            float: left;
            letter-spacing: 0.75em;
            padding-top: 3px;
            text-align: right;
        }
        
        .econtent_ecomfmonth{
            width: 59px;
            float: left;
            margin-left: -10px;
            letter-spacing: 0.75em;
            padding-top: 3px;
            text-align: right;
        }
        .econtent_ecomsecmonth{
            width: 59px;
            float: left;
            margin-left: -10px;
            letter-spacing: 0.70em;
            padding-top: 3px;
            text-align: right;
        }
        .econtent_ecomtmonth{
            width: 59px;
            float: left;
            margin-left: -10px;
            letter-spacing: 0.70em;
            padding-top: 3px;
            text-align: right;
        }
        .econtent_sepdate{
            position: absolute;
            width: 125px;
            float: left;
            margin-left: -5px;
            letter-spacing: 0.59em;
            padding-top: 3px;
            padding-left: 2px;
        }
        .employeecontent_total{
            width: 100%;
            margin-top: -55px;
            padding-left: 7px;
            padding-bottom: 17px;
        }
        
        .grandtotal{
            width: 100%;
            margin-top: 10px;
            margin-left: 60px;
        }
        .grandtotaloth{
            width: 100%;
            margin-left: 60px;
        }
        .ssspayment{
            width: 87px;
            text-align: right;
            padding-right: 3px;
            float: left;
        }
        .ecpayment{
            width: 78px;
            text-align: right;
            padding-right: 3px;
            float: left;
        }
        .gtpayment{
            width: 84px;
            text-align: right;
            padding-right: 3px;
            float: left;
        }
        .divspace{
            margin-left: 20%;            
            width: 30.5%;
            text-align: right;
        }
        
        .letterspace { letter-spacing: 0.63em; } 
        .float{ float: left; }
	</style>
</head>
<body>
".$html."
</body>";

$mpdf->WriteHTML($towrite);
$mpdf->Output();
