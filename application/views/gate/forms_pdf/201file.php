<?php

/**
 * @author Justin
 * @copyright 2015
 */

include "application/config/connection.php";

$pdf = new mpdf('P','A4','','UTF-8',5,5,5,5);

$query = mysql_query("SELECT CONCAT(FName,' ',SUBSTR(MName,1,1),'. ',LName) AS fullname, b.description AS religion,c.description AS civilstatus, d.description AS citizenship, gender, DATE_FORMAT(bdate,'%M %d, %Y') as bdate,
                        	emp_sss, emp_tin, emp_philhealth, emp_pagibig, cityaddr, cp_mobile, cp_telno, email, spouse_name
                        FROM employee a
                        LEFT JOIN code_religion b ON a.religionid = b.religionid
                        LEFT JOIN code_civil_status c ON a.civil_status = c.code
                        LEFT JOIN code_citizenship d ON a.citizenid= d.citizenid
                         WHERE a.employeeid='$eid'");
$rs = mysql_fetch_array($query);
$fullname = $rs['fullname'];

$queryedu = mysql_query("SELECT school, year_graduated as sy, course as degree FROM employee_education WHERE employeeid='$eid'");
$queryworkhis = mysql_query("SELECT POSITION, company AS institution, IF(DATEDIFF(date_to,date_from) = 0, 1, DATEDIFF(date_to,date_from)) AS yremployed FROM employee_work_history WHERE employeeid='$eid';");

$iquery = mysql_query("SELECT * FROM elfinder_file where title='$eid'");
$irs = mysql_num_rows($iquery);
if($irs > 0)
    
    $img = "<img src='".site_url('forms/loadForm')."?form=imgview&eid=$eid' width='20%' height='20%' style='border: 1px solid black'/>";
else
    $img = "<img src='".base_url()."images/no_image.gif' width='20%' height='20%'/>";

#$logo = "<img src='".base_url()."images/logos.png' width='5%' height='5%' align='left'>";


$style =    "<style>
                /* FRONT PAGE */
                .fcontainer{
                    border: 2px solid black;
                    height: 100%;
                    width: 100%;
                    position: relative;
                }
                .logo{
                    position: absolute;
                    margin-top: 65px;
                    margin-left: 450px;
                    background-image: url('".base_url()."images/logos.png');
                    background-position:center;
                    background-repeat:no-repeat;        
                    background-size: 10.5%;
                }
                .header{
                  position: absolute;
                  font-size: 14px;
                  color: #034116;
                  font-family: 'oldenglishtext';
                }
                .content{
                    height: 100%;
                    width: 100%;
                    display: block;                                        
                }
                .regu{
                    font-family: Arial;
                    font-size: 12px;    
                    color: #034116;
                }
                .reguC{
                    font-family: Arial;
                    font-size: 12px;    
                    color: #034116;
                    text-align: center;
                }                    
                .line{
                    padding: 1px;
                    border-style: solid;
                    border-bottom: thin black;               
                }                    
                table{
                    border-collapse: collapse;
                }
                .borderheader{
                    border-top: 2px solid;
                    border-bottom: 2px solid;
                }
                .bordertop{
                    border-top: 2px solid;
                }
                .borderlast{
                    border-bottom: 1px solid;
                }
                .bordercontent{
                    border-bottom: 1px solid;
                    border-right: 1px solid;
                }
            </style>";

/* FRONT PAGE */
$info = $style."
<div class='logo'></div>
<div class='fcontainer' >
    <div style='color: black; margin-left: 20px; margin-top: 5px; font-size: 12px; font-family: Amazone BT, times, serif; '><b>ID NO.</b></div>
    <div class='header' style='text-align:center;'>Saint Jude Catholic School<br /><span style='font-family: arial; font-size: 11px'>327 Ycaza St., San Miguel, Manila 1005</span></div>
    <div class='content'>
        <table width='100%' style='border-collapse: collapse; table-layout: fixed;'>
            <tr>
                <td class='regu' width='10%'>&nbsp;</td>
                <td class='regu' width='25%'>&nbsp;</td>
                <td class='regu' width='10%'>&nbsp;</td>
                <td class='regu' width='25%'>&nbsp;</td>
                <td class='regu' width='30%' rowspan='12' style='text-align: center;'><img src='".base_url()."images/no_image.gif' width='25%' height='30%'/></td>
            </tr>
            <tr>
                <td class='regu'>Name</td>
                <td class='regu line' colspan='3'>$fullname</td>
            </tr>
            <tr>
                <td class='regu'>Date of Birth</td>
                <td class='regu line'>{$rs['bdate']}</td>
                <td class='regu'>Sex</td>
                <td class='regu line'>{$rs['gender']}</td>
            </tr>
            <tr>
                <td class='regu'>Civil Status</td>
                <td class='regu'>{$rs['civilstatus']}</td>
                <td class='regu'>Citizenship</td>
                <td class='regu line'>{$rs['citizenship']}</td>
            </tr>
            <tr>
                <td class='regu'>Religion</td>
                <td class='regu line'>{$rs['religion']}</td>
                <td class='regu'>PRC No.</td>
                <td class='regu line'>&nbsp;</td>
            </tr>
            <tr>
                <td class='regu'>SSS No.</td>
                <td class='regu line'>{$rs['emp_sss']}</td>
                <td class='regu'>TIN No.</td>
                <td class='regu line'>{$rs['emp_tin']}</td>
            </tr>
            <tr>
                <td class='regu'>Philhealth</td>
                <td class='regu line'>{$rs['emp_philhealth']}</td>
                <td class='regu'>Pag-ibig</td>
                <td class='regu line'>{$rs['emp_pagibig']}</td>
            </tr>
            <tr>
                <td class='regu'>Address</td>
                <td class='regu line' colspan='3'>{$rs['cityaddr']}</td>
            </tr>
            <tr>
                <td class='regu'>&nbsp;</td>
                <td class='regu line'>&nbsp;</td>
                <td class='regu line'>&nbsp;</td>
                <td class='regu line'>&nbsp;</td>
            </tr>
            <tr>
                <td class='regu'>Landline No.</td>
                <td class='regu line'>{$rs['cp_telno']}</td>
                <td class='regu'>Cel. No</td>
                <td class='regu line'>{$rs['cp_celno']}</td>
            </tr>
            <tr>
                <td class='regu'>E-Mail</td>
                <td class='regu line'>{$rs['email']}</td>
                <td class='regu'>Passport No.</td>
                <td class='regu line'>&nbsp;</td>
            </tr>
            <tr>
                <td class='regu'>Spouse</td>
                <td class='regu line' colspan='3'>{$rs['spouse_name']}</td>
            </tr>
        </table>
        <table width='100%' style='border-collapse: collapse; table-layout: fixed; margin-top: 5px;'>
            <tr>
                <td class='regu borderheader' colspan='4' style='color: black; background-color: #129E70;'><b>Educational Attainment</b></td>
            </tr>
            <tr>
                <td class='reguC bordercontent' width='15%'>Course</td>
                <td class='reguC bordercontent' width='35%'>School University</td>
                <td class='reguC bordercontent' width='20%'>Years Covered</td>
                <td class='reguC borderlast' width='30%'>Degree</td>
                <!--
                <td>
                    <table width='100%' style='border-collapse: collapse; table-layout: fixed; border-top: solid; border-top: solid;'>
                        <tr>
                            <td class='reguC borderheader' width='10%'>Course</td>
                            <td class='reguC borderheader' width='30%'>School/University</td>
                            <td class='reguC borderheader' width='30%'>Years Covered</td>
                            <td class='reguC borderheader' width='30%'>Degree</td>
                        </tr>
                    </table>
                </td>
                -->
            </tr>
            <tr>
                <td class='regu bordercontent' height='50'>Elementary</td>
                <td class='regu bordercontent'></td>
                <td class='regu bordercontent'></td>
                <td class='regu borderlast'></td>
            </tr>
            <tr>
                <td class='regu bordercontent' height='50'>High School</td>
                <td class='regu bordercontent'></td>
                <td class='regu bordercontent'></td>
                <td class='regu borderlast'></td>
            </tr>
            <tr>
                <td class='regu bordercontent' height='50'>College</td>
                <td class='regu bordercontent'></td>
                <td class='regu bordercontent'></td>
                <td class='regu borderlast'></td>
            </tr>
            <tr>
                <td class='regu bordercontent' height='50'>Graduate Studies</td>
                <td class='regu bordercontent'></td>
                <td class='regu bordercontent'></td>
                <td class='regu borderlast'></td>
            </tr>
        </table>
        <table width='100%' style='border-collapse: collapse; table-layout: fixed; margin-top: 5px;'>
            <tr>
                <td class='regu' width='15%'>MAJOR</td>
                <td class='regu line' width='30%'>&nbsp;</td>
                <td class='regu' width='15%'>&nbsp;</td>
                <td class='regu' width='10%' style='text-align: right;'>MINOR</td>
                <td class='regu line' width='30%'>&nbsp;</td>
            </tr>
            <tr>
                <td class='regu'>OTHER SKIL:</td>
                <td class='regu line' colspan='4'>&nbsp;</td>
            </tr>
            <tr>
                <td class='regu' colspan='5'>Experience (in other institutions)</td>
            </tr>
        </table>
        <table width='100%' style='border-collapse: collapse; table-layout: fixed;'>
            <tr style='background-color: #444473;'>
                <td class='reguC borderheader bordercontent' width='30%' style='color: white;'>POSITION</td>
                <td class='reguC borderheader bordercontent' width='40%' style='color: white;'>INSTITUTION</td>
                <td class='reguC borderlast bordertop' width='30%' style='color: white;'>YEARS OF EMPLOYMENT</td>
            </tr>
            <tr>
                <td class='regu bordercontent'>&nbsp;</td>
                <td class='regu bordercontent'>&nbsp;</td>
                <td class='regu borderlast'>&nbsp;</td>
            </tr>
            <tr>
                <td class='regu bordercontent'>&nbsp;</td>
                <td class='regu bordercontent'>&nbsp;</td>
                <td class='regu borderlast'>&nbsp;</td>
            </tr>
        </table>
        <span class='regu'>Experience (in the present institution)</span>
        <table width='100%' style='border-collapse: collapse; table-layout: fixed;'>
            <tr style='background-color: #916E34'>
                <td class='reguC borderheader bordercontent' width='30%' style='color: white;'>POSITION</td>
                <td class='reguC borderheader bordercontent' width='15%' style='color: white;'>SCHOOL YEAR</td>
                <td class='reguC borderheader bordercontent' width='40%' style='color: white;'>ASSIGNMENT</td>
                <td class='reguC borderlast bordertop' width='15%' style='color: white;'>Remarks</td>
            </tr>
            <tr>
                <td class='regu bordercontent'>&nbsp;</td>
                <td class='regu bordercontent'>&nbsp;</td>
                <td class='regu bordercontent'>&nbsp;</td>
                <td class='regu borderlast '>&nbsp;</td>
            </tr>
        </table>
        
    </div>
</div>          
";

$pdf->WriteHTML($info);


$pdf->Output();
?>

