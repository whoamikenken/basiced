<?php
/**
* @author justin (with e)
* @copyright 2018
*/

set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set("memory_limit",-1);

$mpdf = new mpdf('LONG-L','LONG-L','','UTF-8',5,5,8,5);
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

    table th {
      text-transform: uppercase;
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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>Payroll Register</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 11px;' width='55%'>Payroll Register : ". date("F d, Y", strtotime($date_from)) ." - ". date("F d, Y", strtotime($date_to)) ."</span></td>
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
                    <th align='center' rowspan='2' style='color: yellow; border-color:gray'>#</th>
                    <th align='center' colspan='3' style='color: yellow; border-color:gray'>Information</th>
                    <th align='center' style='color: yellow; border-color:gray' colspan='".(count($inc_disp) + 4)."'>Earnings</th>
                    <th align='center' style='color: yellow; border-color:gray' colspan='".(count($deduc_disp) + count($loan_disp) + 7)."'>Deduction</th>
                </tr>
                <tr style='background-color: black; border-color: #0072c6 '>
                    <th style='color: yellow;'>Office</th>
                    <th style='color: yellow;'>Employeeid ID</th>
                    <th style='color: yellow;'>Name</th>
                    <th style='color: yellow;'>Regular Pay</th>
                    <th style='color: yellow;'>Tardy</th>
                    <th style='color: yellow;'>Absent</th>
                    <th style='color: yellow;'>Net Basic Pay</th>";
                    foreach($inc_disp as $inc_name){
                        $content .= "<th style='color: yellow;'>".($inc_name)."</th>";
                    }

                    foreach($inc_adj_disp as $inc_name){
                        $content .= "<th style='color: yellow;'>".($inc_name)."</th>";
                    }

                    $content .= "<th style='color: yellow;'>Overtime</th>";

                    $content .= "<th style='color: yellow;'>Gross Salary</th>";

                    $content .= "<th style='color: yellow;'>Witholding Tax</th>";

                    foreach($fixeddeduc_disp as $deduc_name){
                        $content .= "<th style='color: yellow;'>".($deduc_name)."</th>";
                    }

                    foreach($deduc_disp as $deduc_name){
                        $content .= "<th style='color: yellow;'>".($deduc_name)."</th>";
                    }

                    foreach($loan_disp as $loan_name){
                        $content .= "<th style='color: yellow;'>".($loan_name)."</th>";
                    }


                    $content .= "<th style='color: yellow;'>Net Pay</th>";
                $content .= "</tr>
            </thead>
            <tbody>";
            // echo "<pre>"; print_r($inc_disp); die;
            $empcount = 0;
            foreach($emplist as $row){
                $empcount++;
                $content .= "
                <tr>
                <td>".$empcount."</td>
                    <td>".($row['office'])."</td>
                    <td>".($row['employeeid'])."</td>
                    <td>".($row['fullname'])."</td>
                    <td>".($row['regpay'])."</td>
                    <td>".($row['tardy'])."</td>
                    <td>".($row['absent'])."</td>
                    <td>".($row['netbasicpay'])."</td>";
                    foreach($inc_disp as $key => $inc_name){
                        $content .= "<td>".(isset($row["income"][$key]) ? (number_format($row["income"][$key], 2)) : '0.00')."</td>";
                    }

                    foreach($inc_adj_disp as $key => $inc_name){
                        $content .= "<td>". (isset($row["income_adj"][$key]) ? (number_format($row["income_adj"][$key], 2)) : '0.00')."</td>";
                    }

                    $content .= "<td>".($row['overtime'])."</td>";

                    $content .= "<td>".($row['grosspay'])."</td>";

                    $content .= "<td>".($row['witholdingtax'])."</td>";

                    foreach($fixeddeduc_disp as $key => $deduc_name){
                        $content .= "<td>". (isset($row["fixeddeduc"][$key]) ? ($row["fixeddeduc"][$key]) : '0.00')."</td>";
                    }

                    foreach($deduc_disp as $key => $deduc_name){
                        $content .= "<td>". (isset($row["deduction"][$key]) ? ($row["deduction"][$key]) : '0.00')."</td>";
                    }


                    foreach($loan_disp as $key => $loan_name){
                        $content .= "<td>". (isset($row["loan"][$key]) ? ($row["loan"][$key]) : '0.00')."</td>";
                    }


                    $content .= "<td>".($row['netpay'])."</td>";
                $content .= "</tr>";
            }
        $content .= "</tbody></table>
    </div>
</div>
";
$mpdf->WriteHTML($content);
$mpdf->Output();
