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
        margin-top: 3.15cm;
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

    .content-header{
        text-align: center;
        font-size: 12px;
    }

    .content-body{
        border: 1px solid black;
        padding-top: 8px;
        padding-bottom: 8px;
        padding-left: 8px;
    }
</style>
";

$content .= "
<htmlpageheader name='Header'>
    <div>
        <table class='header'>
            <tr>
                <td>
                    <img src='". $imgurl ."images/school_logo.png' style='width: 80px;'/>
                </td>
                <td style='color:blue;'>
                    <b style='align_center'>
                        <h1>Pinnacle Technologies Inc.</h1>
                    </b>
                    <b>
                        Attendance Cut-Off : ". date("F d, Y", strtotime($date_from)) ." - ". date("F d, Y", strtotime($date_to)) ."
                    </b>
                </td>
            </tr>
        </table>
    </div>
</htmlpageheader>
";

$content .= "
<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
                <tr style='background-color: #3C8DBC'>
                    <th align='center' rowspan='2' style='width: 10%;'>Employee ID</th>
                    <th align='center' rowspan='2' style='width: 20%;'>Name</th>
                    <th align='center' colspan='3' style='width: 15%;'>Overtime</th>
                    <th align='center' rowspan='2' style='width: 05%;'>Late</th>
                    <th align='center' rowspan='2' style='width: 05%;'>Undertime</th>
                    <th align='center' colspan='4' style='width: 20%;'>Leave</th>
                    <th align='center' rowspan='2' style='width: 05%;'>Absent</th>
                    <th align='center' rowspan='2' style='width: 05%;'>No. of Days</th>
                    <th align='center' rowspan='2' style='width: 05%;'>Leave w/ Pay</th>
                    <th align='center' rowspan='2' style='width: 05%;'>Holiday</th>
                    <th align='center' rowspan='2' style='width: 05%;'>Signature</th>
                </tr>
                <tr style='background-color: #3C8DBC'>
                    <th align='center' style='width: 05%;'>Regular</th>
                    <th align='center' style='width: 05%;'>Rest Day</th>
                    <th align='center' style='width: 05%;'>Holiday</th>
                    
                    <th align='center' style='width: 05%;'>Vacation</th>
                    <th align='center' style='width: 05%;'>Sick</th>
                    <th align='center' style='width: 05%;'>Others</th>
                    <th align='center' style='width: 05%;'>Service Credit</th>
                </tr>
            </thead>
            <tbody>
";
$old_campus = $old_deptid = $old_camp = '';
if($category == "department"){
    foreach($emp_list as $perdept => $empdata_per_dept){
        if($old_camp != (isset($campus[$perdept]) ? $campus[$perdept] : "")) $old_deptid = '';
        if($perdept == "ACAD"){
            $content .= "
                    <tr>
                        <td colspan='16'>
                            <strong>". (isset($department[$perdept]) ? $department[$perdept] : "&nbsp;") ."</strong>
                        </td>
                    </tr>
            ";
            foreach ($empdata_per_dept as $sort_key => $employees) {
                foreach ($employees as $employeeid => $info) {
                    if($info['campusid'] != $old_campus){
                        $content .= "
                                <tr>
                                    <td colspan='16'>
                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;". (isset($info['campusid']) ? $info['campusid'] : "&nbsp;") ."</strong>
                                    </td>
                                </tr>
                        ";
                    }
                    $content .= "
                            <tr>
                                <td align='center'>". $employeeid ."</td>
                                <td>". $info["name"] ."</td>
                                <td align='center'>". $info["ot-regular"] ."</td>
                                <td align='center'>". $info["ot-rest-day"] ."</td>
                                <td align='center'>". $info["ot-holiday"] ."</td>
                                <td align='center'>". $info["late"] ."</td>
                                <td align='center'>". $info["undertime"] ."</td>
                                <td align='center'>". $info["vl"] ."</td>
                                <td align='center'>". $info["sl"] ."</td>
                                <td align='center'>". $info["ol"] ."</td>
                                <td align='center'>". $info["scl"] ."</td>
                                <td align='center'>". $info["absent"] ."</td>
                                <td align='center'>". round($info["no-days"]) ."</td>
                                <td align='center'>". ($info["l_nopay"] ? $info["l_nopay"] : 0 )."</td>
                                <td align='center'>". $info["holiday"] ."</td>
                                <td></td>
                            </tr>
                    ";
                    $old_campus = $info['campusid'];
                }
            }
        }else{
            $content .= "
                    <tr>
                        <td colspan='16'>
                            <strong>". (isset($campus[$perdept]) ? $campus[$perdept] : "No Campus") ."</strong>
                        </td>
                    </tr>
            ";
            foreach ($empdata_per_dept as $sort_key => $employees) {
                foreach ($employees as $employeeid => $info) {
                    if($info['deptid'] != $old_deptid){
                        $content .= "
                                <tr>
                                    <td colspan='16'>
                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;". (isset($info['deptid']) ? $this->extensions->getDepartmentDescription($info['deptid']) : "No Campus") ."</strong>
                                    </td>
                                </tr>
                        ";
                    }
                    $content .= "
                            <tr>
                                <td align='center'>". $employeeid ."</td>
                                <td>". $info["name"] ."</td>
                                <td align='center'>". $info["ot-regular"] ."</td>
                                <td align='center'>". $info["ot-rest-day"] ."</td>
                                <td align='center'>". $info["ot-holiday"] ."</td>
                                <td align='center'>". $info["late"] ."</td>
                                <td align='center'>". $info["undertime"] ."</td>
                                <td align='center'>". $info["vl"] ."</td>
                                <td align='center'>". $info["sl"] ."</td>
                                <td align='center'>". $info["ol"] ."</td>
                                <td align='center'>". $info["scl"] ."</td>
                                <td align='center'>". $info["absent"] ."</td>
                                <td align='center'>". round($info["no-days"]) ."</td>
                                <td align='center'>". ($info["l_nopay"] ? $info["l_nopay"] : 0 )."</td>
                                <td align='center'>". $info["holiday"] ."</td>
                                <td></td>
                            </tr>
                    ";
                    $old_deptid = $info['deptid'];
                }
            }
        }
        $old_camp = (isset($campus[$perdept]) ? $campus[$perdept] : "");
    }
}else{
    foreach ($emp_list as $sort_key => $employees) {
        if($sort_key != "name"){
            $content .= "
                    <tr>
                        <td colspan='16'>
                            <strong>". (isset($campus[$sort_key]) ? $campus[$sort_key] : "&nbsp;") ."</strong>
                        </td>
                    </tr>
            ";
        }

        foreach ($employees as $employeeid => $info) {
            if($info['campusid'] != $old_campus){
                $content .= "
                        <tr>
                            <td colspan='16'>
                                <strong>". (isset($info['campusid']) ? $info['campusid'] : "&nbsp;") ."</strong>
                            </td>
                        </tr>
                ";
            }
            $content .= "
                    <tr>
                        <td align='center'>". $employeeid ."</td>
                        <td>". $info["name"] ."</td>
                        <td align='center'>". $info["ot-regular"] ."</td>
                        <td align='center'>". $info["ot-rest-day"] ."</td>
                        <td align='center'>". $info["ot-holiday"] ."</td>
                        <td align='center'>". $info["late"] ."</td>
                        <td align='center'>". $info["undertime"] ."</td>
                        <td align='center'>". $info["vl"] ."</td>
                        <td align='center'>". $info["sl"] ."</td>
                        <td align='center'>". $info["ol"] ."</td>
                        <td align='center'>". $info["scl"] ."</td>
                        <td align='center'>". $info["absent"] ."</td>
                        <td align='center'>". round($info["no-days"]) ."</td>
                        <td align='center'>". ($info["l_nopay"] ? $info["l_nopay"] : 0 )."</td>
                        <td align='center'>". $info["holiday"] ."</td>
                        <td></td>
                    </tr>
            ";
            $old_campus = $info['campusid'];
        }
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
                                <td style="border-bottom:2px solid white;border-right:0px;border-top:0px;border-left:1px solid white;">Noted by : </td>
                                <td style="border-bottom:0px;border-right:0px;border-top:0px;border-left:1px solid white;font-size:20xp;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$this->extensions->getEmployeeName($this->session->userdata("username")).'</td>
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
                                <td style="border-bottom:2px solid white;border-right:0px;border-top:0px;border-left:1px solid white;">Certified Correct :</td>
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
                                <td>Verified by : </td>
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
    $content .= '<td style="width:33%;"></td></tr>
                </table>
            </div>
        </div>
    ';
$mpdf->WriteHTML($content);
$mpdf->Output();