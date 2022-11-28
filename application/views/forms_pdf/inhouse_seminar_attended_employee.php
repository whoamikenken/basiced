<?php
require_once(APPPATH."constants.php");
$totalSeminar = $empSeminarAttended = 0;
$schoolYear = explode("-", $year);
$seminarSY = $this->seminar->getSeminarWithinSY($schoolYear[0], $schoolYear[1], $attendees, $month_from, $month_to, $month);
// echo "<pre>"; print_r($this->db->last_query()); die;

$employeeList = $this->seminar->getAttendedEmployeeList($sortby, $status, $employees);
$currentTime = new DateTime(date('Y-m-d', strtotime($this->extensions->getServerTime())));
$pdf = new mpdf('utf-8','LETTER-L','','UTF-8',5,5,8,8,9,2);
$info  = "  
<style>
    @page{            
        /*margin-top: 4.35cm;*/
        margin-top: 3.15cm;
        odd-header-name: html_Header;
        odd-footer-name: html_Footer;
    }
    th{
        color: yellow;
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

    .footer{
        width: 100%;
        text-align: right;
    }
</style>";
$info .= "
<htmlpageheader name='Header'>
    <div>
        <table width='60%'  >
            <tr>
                <td rowspan='3' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>Summary Of Attendance (".$year.")</strong></span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>";

$info .= "
<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
                <tr style='background-color: #000000;'>
                <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;' rowspan='2'>#</th>
                <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;' rowspan='2'>EMPLOYEE ID</th>
                <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;' rowspan='2'>FULL NAME</th>";
                $existSeminar = $existSeminars = array();
                if(!in_array("all", $attendees)){

                    foreach ($seminarSY as $value) {
                        $attend = explode(',', $value['attendees']);
                        if(is_array($attend)){
                            foreach ($attend as $k => $v) {
                                if(!in_array($value['username'], $existSeminar)){
                                    if(in_array($v, $attendees)){
                                        $existSeminar[$value['id']] = $value['username'];
                                        $totalSeminar+=1;
                                        $seminarDate = date('M j', strtotime($value['date_from']));
                                        $info .= "<th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>".$this->extensions->reportCodeDescription($value['workshop'])."</th>";
                                    }
                                }
                            }
                        }
                    }
                }else{
                    foreach ($seminarSY as $value) {
                        $totalSeminar+=1;
                        $seminarDate = date('M j', strtotime($value['date_from']));
                        $info .= "<th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>".$this->extensions->reportCodeDescription($value['workshop'])."</th>";
                    }
                }
                $info .= "<th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;' rowspan='2'>TOTAL</th>";

$info .= "</tr>
         <tr style='background-color: #000000;'>";
         $existSeminar = $existSeminars = array();
         if(!in_array("all", $attendees)){
                foreach ($seminarSY as $value) {
                    $attend = explode(',', $value['attendees']);
                    if(is_array($attend)){
                        foreach ($attend as $k => $v) {
                            if(!in_array($value['username'], $existSeminar)){
                                if(in_array($v, $attendees)){
                                    $existSeminar[$value['id']] = $value['username'];
                                    $seminarDate = date('M j', strtotime($value['date_from']));
                                    $info .= "<th style='padding: 0px;text-align: center;font-size: 13px;font-weight: bold; white-space: nowrap;'>".strtoupper($seminarDate)."</th>";
                                }
                            }
                        }
                    }
                }
            }else{
                foreach ($seminarSY as $value) {
                    $seminarDate = date('M j', strtotime($value['date_from']));
                    $info .= "<th style='padding: 0px;text-align: center;font-size: 13px;font-weight: bold; white-space: nowrap;'>". strtoupper($seminarDate)."</th>";
                }
            }
$info .= "</tr></thead>";
$officeRow = $totalSeminar+4;
$colspan = "colspan=".$officeRow;
$ofc = 'sometext';
$officeDesc = '';
$empcount = 0;
$info .= "<tbody class='inhouseAttendees'>";
            foreach ($employeeList as $key => $value){
                
                if($sortby=='Office'){
                    if($ofc !== $value['office']){
                        if($officeDesc != $this->extensions->getOfficeDescriptionReport($value['office'])){
                            $info .="
                                <tr style='background-color: yellow;'>
                                    <td style='font-size: 13px; color:  #000000; text-align: left; padding-left:5px;' ".$colspan."><b>".$this->extensions->getOfficeDescriptionReport($value['office'])."</b></td>
                                </tr>";
                        }
                    }
                }
                $ofc = $value['office'];
                $officeDesc = $this->extensions->getOfficeDescriptionReport($value['office']);
                $empSeminarAttended=0;
                if(!in_array("all", $attendees)){
                    $dateemployed = new DateTime($value['dateemployed']);
                    $yearOfService = $dateemployed->diff($currentTime)->y + 1;
                    if(in_array($yearOfService, $attendees)){
                        $empcount++;
                        $info .= "
                        <tr>
                            <td style='padding: 2px;text-align: center;font-size: 13px;'>".$empcount."</td>
                            <td style='padding: 2px;text-align: center;font-size: 13px;'>".$value['employeeid']."</td>
                            <td style='padding: 2px;text-align: center;font-size: 13px;'>".$value['fullname']."</td>";
                            
                            foreach ($seminarSY as $val) {
                                $existSeminars = array();
                                $attend = explode(',', $val['attendees']);
                                if(is_array($attend)){
                                    foreach ($attend as $k => $v) {
                                        if(!in_array($val['username'], $existSeminars)){
                                            if(in_array($v, $attendees)){
                                                $existSeminars[$val['id']] = $val['username'];
                                                list($timein, $timeout) = $this->seminar->getEmployeeSeminarAttendance($val['username'], $value['employeeid']);

                                                list($leavetype, $leaveColspan) = $this->seminar->getLeavetypeAndColspan($value['employeeid'], $val['date_from'], $val['date_to']);

                                                if($leavetype != ''){
                                                    $lColspan = "colspan=".$leaveColspan;
                                                    if($leavetype != $leavedesc){
                                                         $info .= "
                                                            <td style='padding: 2px;text-align: center;font-size: 13px;'>".$leavetype."</td>
                                                        ";
                                                    }
                                                    $leavedesc = $leavetype;
                                                }else{
                                                    if($timein == '' && $timeout == ''){
                                                        $attended = "<span style='color: red'>---</span>";
                                                    }else if($timein && $timeout){
                                                        $timein = new DateTime(date('H:i:s', strtotime($timein)));
                                                        $timeout = new DateTime(date('H:i:s', strtotime($timeout)));
                                                        $timefrom = new DateTime($val['time_from']);
                                                        $timeto = new DateTime($val['time_to']);
                                                        if($timein <= $timefrom && $timeout >= $timeto) {
                                                            $attended = "<span style='font-size:15px;'>&#10004;</span>";
                                                            $empSeminarAttended+=1;
                                                        }
                                                        else if($timein > $timefrom && $timeout < $timeto){ 
                                                            $attended = "<span style='font-size:8px; color: red'>Late & Early Exit</span>";
                                                            $empSeminarAttended+=1;
                                                        }
                                                        else if($timein > $timefrom && $timeout >= $timeto){
                                                            $attended = "<span style='font-size:8px; color: red'>Late</span>";
                                                            $empSeminarAttended+=1;
                                                        }
                                                        else{
                                                            $attended = "<span style='font-size:8px; color: red'>Early Exit</span>";
                                                            $empSeminarAttended+=1;
                                                        }
                                                    }else{
                                                        $attended = "<span style='font-size:8px; color: red'>Did not lot out</span>";
                                                    }
                                                    $info .= "
                                                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$attended."</td>
                                                    ";
                                                }
                                            }else{
                                                $attended = "<span style='color: red'>---</span>";
                                                $info .= "
                                                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$attended."</td>
                                                    ";
                                            }
                                        }else{
                                            $attended = "<span style='color: red'>---</span>";
                                            $info .= "
                                                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$attended."</td>
                                                    ";
                                        }
                                    }
                                }else{
                                    $attended = "<span style='color: red'>---</span>";
                                    $info .= "
                                                <td style='padding: 2px;text-align: center;font-size: 13px;'>".$attended."</td>
                                            ";
                                }
                            }
                        $info .= "<td style='padding: 2px;text-align: center;font-size: 13px;'>".$empSeminarAttended."/".$totalSeminar."</td>
                        </tr>";

                    }
                }else{
                    $empcount++;
                    $info .= "
                    <tr>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$empcount."</td>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$value['employeeid']."</td>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$value['fullname']."</td>";
                        foreach ($seminarSY as $val) {
                            list($timein, $timeout) = $this->seminar->getEmployeeSeminarAttendance($val['username'], $value['employeeid']);
                            // echo "<pre>"; print_r($this->db->last_query()); die;

                            list($leavetype, $leaveColspan) = $this->seminar->getLeavetypeAndColspan($value['employeeid'], $val['date_from'], $val['date_to']);
                            if($leavetype != ''){
                                $lColspan = "colspan=".$leaveColspan;
                                if($leavetype != $leavedesc){
                                     $info .= "
                                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$leavetype."</td>
                                    ";
                                }else{
                                     $info .= "
                                        <td style='padding: 2px;text-align: center;font-size: 13px;'><span style='color: red'>---</span></td>
                                    ";
                                }
                                $leavedesc = $leavetype;
                            }else{
                                if($timein == '' && $timeout == ''){
                                    $attended = "<span style='color: red'>---</span>";
                                }else if($timein && $timeout){
                                    $timein = new DateTime(date('H:i:s', strtotime($timein)));
                                    $timeout = new DateTime(date('H:i:s', strtotime($timeout)));
                                    $timefrom = new DateTime($val['time_from']);
                                    $timeto = new DateTime($val['time_to']);
                                    if($timein <= $timefrom && $timeout <= $timeto) {
                                        $attended = "<span style='font-size:15px;'>&#10004;</span>";
                                        $empSeminarAttended+=1;
                                    }
                                    else if($timein > $timefrom && $timeout > $timeto){ 
                                        $attended = "<span style='font-size:8px; color: red'>Late & Early Exit</span>";
                                        $empSeminarAttended+=1;
                                    }
                                    else if($timein > $timefrom && $timeout <= $timeto){
                                        $attended = "<span style='font-size:8px; color: red'>Late</span>";
                                        $empSeminarAttended+=1;
                                    }
                                    else{
                                        $attended = "<span style='font-size:8px; color: red'>Early Exit</span>";
                                        $empSeminarAttended+=1;
                                    }
                                }else{
                                    $attended = "<span style='font-size:8px; color: red'>Did not log out</span>";
                                }
                                $info .= "
                                    <td style='padding: 2px;text-align: center;font-size: 13px;'>".$attended."</td>
                                ";
                            }
                        }
                    $info .= "<td style='padding: 2px;text-align: center;font-size: 13px;'>".$empSeminarAttended."/".$totalSeminar."</td>
                    </tr>";
                }
            }

$info .= "      
            </tbody>
        </table>
    </div>
</div>";
$info .= "
<htmlpagefooter name='Footer'>
    <br>
    <div class='footer'>
        Page : {PAGENO} of {nb}
    </div>
</htmlpagefooter>
";
$pdf->WriteHTML($info);

$pdf->Output();
?>



