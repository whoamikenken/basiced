<?php
/**
* @author justin (with e)
* @copyright 2018
*/

set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set("memory_limit",-1);

$mpdf = new mPDF('P','LETTER','','UTF-8',5,5,8,5);
require_once(APPPATH."constants.php");

$content = "
<style>
    @page{            
        /*margin-top: 4.35cm;*/
        margin-top: 3.8cm;
        odd-header-name: html_Header;
        odd-footer-name: html_Footer;
    }  

    .content{
        height: 100%;
        margin-top: 15px;
    }

    table{
        border-collapse: collapse;
        font-size: 12px;
        border-spacing: 5px;
    }

    .table-bordered td, .table-bordered th {
        border: 1px solid grey;
    }

    .table-bordered td{
        text-align: center;
    }

    .content-header{
        text-align: center;
        font-size: 12px;
    }

    .headerTitle{
         font-family: BOOK ANTIQUA, sans-serif;
         font-weight: 700;
    }

    .content-body{
        border: 1px solid black;
        padding-top: 8px;
        padding-bottom: 8px;
        padding-left: 8px;
    }

    .attendanceCutOff{
        text-align: center;
        font-size: 12px;
    }

</style>
";

$content .= "
<htmlpageheader name='Header'>
    <div>
        <table width='60%'  >
            <tr>
                <td rowspan='5' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 70px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='50%'><span style='font-size: 12px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 10px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>ATTENDANCE REPORT</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 11px;' width='55%'>Attendance Cut-Off : ". date("F d, Y", strtotime($date_from)) ." - ". date("F d, Y", strtotime($date_to)) ."</span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>";

$content .= "
<div class='content'>
    <div class='content-header'>
        <table width='100%' class='table-bordered' style='font-size: 9px;' id='datas'>
            <thead>
                <tr style='background-color: black; border-color: #0072c6 '>
                    <th align='center' rowspan='2' style='width: 3%;color:yellow'>Employee ID</th>
                    <th align='center' rowspan='2' style='width: 3%;color:yellow'>Name</th>
                    <th align='center' colspan='3' style='width: 3%;color:yellow'>Overtime (hr:min)</th>
                    <th align='center' rowspan='2' style='width: 3%;color:yellow'>No. of late/UT (hr:min)</th>
                    <th align='center' colspan='1' rowspan='2' style='width: 3%;color:yellow'>Absent</th>
                    <th align='center' colspan='3' style='width: 3%;color:yellow'>Leaves</th>
                    <th align='center' rowspan='2' style='width: 3%;color:yellow'>No. of days</th>
                    <th align='center' rowspan='2' style='width: 3%;color:yellow'>Holiday</th>
                </tr>
                <tr style='background-color: black;'>
                    <th align='center' style='width: 3%;color:yellow'>Regular</th>
                    <th align='center' style='width: 3%;color:yellow'>Rest Day</th>
                    <th align='center' style='width: 3%;color:yellow'>Holiday</th>

                    <th align='center' style='width: 3%;color:yellow'>Emergency</th>
                    <th align='center' style='width: 3%;color:yellow'>Vacation</th>
                    <th align='center' style='width: 3%;color:yellow'>Sick</th>
                </tr>
            </thead>
            <tbody>
";
$old_key = '';
foreach($emp_list as $key => $employees){
    if($category == "department"){
        if($employees){
            $content .= "
                <tr>
                    <td colspan='9' align='left'>
                        <strong style='text-align:left;'>". $this->extensions->getDepartmentDescription($key) ."</strong>
                    </td>
                </tr>
            ";
        }
    }
    foreach ($employees as $employeeid => $info) {
        if($teaching_type == "teaching") $info["absent"] = $info["deducadmin"];
        // echo "<pre>"; print_r($info); die;
        /*attendance computation*/
        $totLate = $this->attcompute->exp_time($info['latelec']) + $this->attcompute->exp_time($info['latelab']) + $this->attcompute->exp_time($info['lateadmin']);
        $totLate = $this->attcompute->sec_to_hm($totLate);
        $totDeduction = $this->attcompute->exp_time($info['day_absent']);
        /*end*/
        
        $content .= "
                <tr>
                    <td align='center'>". $employeeid ."</td>
                    <td>". $info["name"] ."</td>
                    <td align='center'>". (($info["ot-regular"]) ? $info["ot-regular"] : 0) ."</td>
                    <td align='center'>". (($info["ot-rest-day"]) ? $info["ot-rest-day"] : 0) ."</td>
                    <td align='center'>". (($info["ot-holiday"]) ? $info["ot-holiday"] : 0) ."</td>
                    <td align='center'>". (($info["late"]) ? $info["late"] : "0:00") ."</td>
                    <td align='center'>". (($info["absent"]) ? $info["absent"] : 0) ."</td>
                    <td align='center'>". $info["sl"] ."</td>
                    <td align='center'>". $info["vl"] ."</td>
                    <td align='center'>". $info["vl"] ."</td>
                    <td align='center'>". round($info["no-days"]) ."</td>
                    <td align='center'>". $info["holiday"] ."</td>
                </tr>
        ";
    }
}
$content .= "               
            </tbody>
        </table>
        <br><br>
";
$content .= '
    <table style="width:100%;">
                <tr>
                    <td style="width:40%;">
                        <table border="1">
                            <tr>
                                <td style="border-bottom:2px solid white;border-right:0px;border-top:0px;border-left:1px solid white; font-weight:bold;">Acknowledge by : </td>
                                <td style="border-bottom:0px;border-right:0px;border-top:0px;border-left:1px solid white;font-size:20xp;">&nbsp;'.$this->extensions->getEmployeeName($this->session->userdata("username")).'</td>
                            </tr>
                            <tr>
                                <td style="border-bottom:0px;border-right:0px;border-left:0px;"> </td>
                                <td style="border-bottom:0px;border-right:0px;border-left:0px;color:white;"> _________________________________________ </td>
                            </tr>
                        </table>
                    </td>
                        ';
    if($this->extras->findIfAdmin($userlog) == true){
        $content .= '<td style="width:45%;">
                        <table border="1">
                            <tr>
                                <td style="border-bottom:2px solid white;border-right:0px;border-top:0px;border-left:1px solid white;font-weight:bold;">Certified Correct :</td>
                                <td style="border-bottom:0px;border-right:0px;border-top:0px;border-left:1px solid white;font-size:20xp;color:white;"> asda</td>
                            </tr>
                            <tr>
                                <td style="border-bottom:0px;border-right:0px;border-left:0px;"> </td>
                                <td style="border-bottom:0px;border-right:0px;border-left:0px;color:white;"> _________________________________________ </td>
                            </tr>
                            <!--<tr>
                                <td></td>
                                <td class="align_center">'.$this->extras->getAdminInfo($userlog).'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="align_center">'.$this->employee->getHeadDepartment($empid).'</td>
                            </tr>-->
                        </table>
                    </td>';
    }else{
        $content .=  '<td style="width:33%;">
                        <table>
                            <tr>
                                <td style="font-weight:bold;">Verified by : </td>
                                <td> _________________________________________ </td>
                            </tr>
                            <!--<tr>
                                <td></td>
                                <td class="align_center">'.$this->employee->getHeadDepartment($empid,'head_funame').'</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="align_center">'.$this->employee->getHeadDepartment($empid).'</td>
                            </tr>-->
                        </table>
                    </td>
                    ';
        }
    $content .= '</td></tr>
                </table>
            </div>
        </div>
    ';
$mpdf->WriteHTML($content);
$mpdf->Output();
