<?php

/**
 * @author Justin
 * @copyright 2017
 */
include "application/config/connection.php";
include "application/views/forms_pdf/function/payrollfunc.php";
#$mpdf = new mPDF('utf-8','A4-L','10','','10','10','10','10','9','9');
#$mpdf = new mPDF('utf-8','A4-13','10','','10','10','3','10','9','9');
$mpdf = new mPDF('utf-8','A4-L','10','','10','10','10','5','','0');

$html .= '
<div class="container">

    <!-- Employer Information -->
    <div class="space"></div>
    <div class="contentidnum"><br /><br />
        <p>123456789012</p>
    </div>
    <div class="contenttin">
        <p>123456789</p>
    </div>
    
    <div class="contentempname float">
        <p>MANILA CENTRAL UNIVERSITY</p>
    </div>
    <div class="contentbranch float">
        <p>MANILA CENTRAL UNIVERSITY</p>
    </div>
    <!--
    <div class="contentaddr float">
        <p>SAMPLE ADDRESS</p>
    </div>
    <div class="contenttype"><p>X</p></div> 
    <div class="contentquarter">
        <p>'.$this->payrolloptions->monthdesc(sprintf('%02d', $month))." ".$year.'</p>
    </div>
    -->
    <!-- Employee Information -->
';
/*
$x = 0;
$ftamt = $stamt = $ttamt = $ecftamt = $ecstamt = $ecttamt = "";
$query = mysql_query("SELECT * FROM employee a INNER JOIN payroll_computed_table b ON a.employeeid = b.employeeid WHERE DATE_FORMAT(cutoffstart,'%m%Y')='$month$year' 
                      AND FIND_IN_SET(a.employeeid,'01030200,01041600,01050400,01051501')
                     ");
    while($rs = mysql_fetch_array($query)){
        $x++;
        $ftamt += contri($rs['employeeid'],($month-2),$year);
        $stamt += contri($rs['employeeid'],($month-1),$year);
        $ttamt += contri($rs['employeeid'],($month),$year);
        $ecftamt += contri($rs['employeeid'],($month-2),$year,"ec");
        $ecstamt += contri($rs['employeeid'],($month-1),$year,"ec");
        $ecttamt += contri($rs['employeeid'],$month,$year,"ec");
        
        if($x == 1){
            $html .= '
                <div class="employeecontent">
                    <div class="econtent_pnumber">
                        '.$rs['emp_pagibig'].'
                    </div>
                    <div class="econtent_empname">
                        '.strtoupper($rs['lname'].', '.$rs['fname'].' '.$rs['mname']).'
                    </div>
                    <div class="econtent_accno">
                        123456789
                    </div>
                </div>';
        }else{
            $html .= '
                <div class="employeecontent_nr">
                    <div class="econtent_pnumber">
                        '.($rs['emp_pagibig'] ? $rs['emp_pagibig'] : "&nbsp;").'
                    </div>
                    <div class="econtent_empname">
                        '.strtoupper($rs['lname'].', '.$rs['fname'].' '.$rs['mname']).'
                    </div>
                    <div class="econtent_accno">
                        123456789
                    </div>
                </div>';
       }
    }
    for($x; $x <= 30; $x++){
        $html .= '
                <div class="employeecontent_nr">
                    <div class="econtent_pnumber">&nbsp;</div>
                    <div class="econtent_empname">&nbsp;</div>
                    <div class="econtent_accno">&nbsp;</div>
                </div>';
    }
$html .= " </div>";
*/

$towrite = "
	<style type='text/css'>
        .container{
            width: 100%;
            height: 100%;
            float: left;
            background-image: url('".base_url()."images/philhealth2.png');
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }
        .space{height: 28px;}
        .contentidnum{
            width: 80%;
            padding-left: 160px;
            text-align: left;
            margin-top: -10px;
            letter-spacing: 1.45em;            
        }
        .contenttin{
            width: 80%;
            padding-left: 152px;
            text-align: left;
            margin-top: -28px;
            letter-spacing: 1.65em;
        }
        .contentempname{
            width: 500px;
            text-align: center;
            margin-top: -22px;
            padding-left: 10px;
            float: left;
        }
        .contentbranch{
            width: 250px;
            text-align: center;
            margin-top: 15px;
            margin-left: -375px;
            float: left;
        }
        .contentquarter{
            width: 234px;
            padding-left: 8px;
            float: right;
            letter-spacing: 0.1em;
            margin-top: 23px;
        }
        .contentaddr{
            position: absolute;
            width: 480px;
            padding-left: 40px;
            margin-top: -37px;
        }
        .contenttype{
            width: 85px;
            padding-left: 22px;
            float: left;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.6em;
            margin-top: -2px;
        }
        /* Employee Css */
        .employeecontent{
            display:block;
            width: 100%;
            margin-top: 19px;
            padding-left: 7px;
            padding-bottom: 7px;
            height: 10px;
        }
        .employeecontent_nr{
            display:block;
            width: 100%;
            margin-top: -12px;
            padding-left: 7px;
            padding-bottom: 7px;
            padding-top: 0;
            height: 10px;
            float: left;
        }
        .econtent_pnumber{
            width: 100px;
            float: left;
            padding-top: 7px;
            margin-left: 27px;
            padding-left: 5px;
            font-size: 12px;
        }
        .econtent_empname{
            width: 240px;
            float: left;
            padding-top: 5px;
            padding-left: 2px;
            margin-left: -15px;
        }
        .econtent_accno{
            width: 75px;
            float: left;
            padding-left: 5px;
            padding-top: 5px;
            text-align: center;
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
