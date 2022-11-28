<?php

/**
 * @author Justin
 * @copyright 2016
 */
/*end*/

set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set("memory_limit",-1);

$title = '';
if($category == "overtime"){
    $title = strtoupper($category)." REQUEST";
    $colspan = 6;
}else if($category == "att_adj"){
    $title = strtoupper("Adjustments");
}else{
    $title = strtoupper($category);
    $colspan = 4;
}
$payroll_cutoff = $this->extensions->getPayrollCutoffConfig($from_date, $to_date);
$pdf = new mpdf('P','LETTER','','UTF-8',5,5,8,5);
$info  = "  <style>
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
                   #datas tr:nth-child(odd)
                {
                    background-color:#C8C8C8;
                }
            </style>";
$info .= "
<htmlpageheader name='Header'>
     <div style='position: absolute;left:20%;top:02%;'><img src='".$imgurl."images/school_logo.jpg' style='width: 60px;text-align: center;' /></div>
     <div style='position: absolute;left:35%;'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></div>
     <div style='position: absolute;left:42%;top:05%;'><span style='font-size: 13px;'><b>&nbsp;&nbsp;" .strtoupper($category). " Report</b></span></div>
     <div style='position: absolute;left:23%;top:08%;'><span style='font-size: 13px;'><b>&nbsp;&nbsp;LIST OF ".$title." FOR THE PERIOD  ".$from_date." - ".$to_date."</b></span></div>
</htmlpageheader>";
if($category != "att_adj"){
    $info .= "
    <div>
        <table width='100%' border='1' align ='center'>
            <thead>
                <tr>
                    <th align='center' style='border-style: dashed;'>Employee ID</th>
                    <th align='center' style='border-style: dashed;'>Fullname</th>
                    <th align='center' style='border-style: dashed;'>Schedule Date</th>
                    <th align='center' style='border-style: dashed;'>Hours</th>";
                    if($category == "overtime"){
                        $info .="
                            <th align='center' style='border-style: dashed;'>Amount</th>
                            <th align='center' style='border-style: dashed;'>Type</th>";
                    }
    $info .= "     
                </tr>   
            </thead>
            <tbody>";
            if($records) { 
                // foreach($records as $key => $emp_list){
                    // $info .= "
                    //     <tr>
                    //         <td colspan='$colspan'>".$key."</td>
                    //     </tr>
                    // ";
                    // foreach($emp_list as $sub_key => $empinfo){
                        // $info .= "
                        //     <tr>
                        //         <td colspan='$colspan'>".$sub_key."</td>
                        //     </tr>
                        // ";
                        // foreach($empinfo as $employeeid => $row){
                          foreach($records as $row){
                            if($category != "overtime"){
                                if(isset($row['late']) && isset($row['undertime'])){
                                    $late = ($row['late']) ? $row['late'] : "00:00"; 
                                    $undertime = ($row['undertime']) ? $row['undertime'] : "00:00"; 
                                    $row['hours'] = $this->attcompute->sec_to_hm($this->attcompute->exp_time($late) + $this->attcompute->exp_time($undertime));
                                }else{
                                     $row['hours'] = $this->attcompute->sec_to_hm($this->attcompute->exp_time($row['hours']));
                                }
                                if($row['hours'] != "0:00"){
                                    $info .="
                                        <tr>
                                            <td align='center'>". $row['employeeid'] . "</td>
                                            <td align='left'>" . $row['fullname'] . "</td>
                                            <td align='center'>" . $row['sched_date'] . " </td>
                                            <td align='center'>" . $row['hours'] . " </td>";
                                            if($category == "overtime"){
                                                $info .="
                                                        <td align='center'>" . number_format($row['ot_amount'], 2) . "</td>
                                                        <td align='center'>" . $row['ot_type'] . " </td>";
                                            }

                                    $info.="</tr>";
                                }
                            }else{
                                foreach ($row as $ot_info) {
                                    $ot_info['hours'] = $this->attcompute->sec_to_hm($this->attcompute->exp_time($ot_info['hours']));

                                    if($ot_info['hours'] != "0:00"){
                                        $info .="
                                            <tr>
                                                <td align='center'>". $ot_info['employeeid'] . "</td>
                                                <td align='left'>" . $ot_info['fullname'] . "</td>
                                                <td align='center'>" . $ot_info['sched_date'] . " </td>
                                                <td align='center'>" . $ot_info['hours'] . " </td>";
                                                if($category == "overtime"){
                                                    $info .="
                                                            <td align='center'>" . number_format($ot_info['ot_amount'], 2) . "</td>
                                                            <td align='center'>" . $ot_info['ot_type'] . " </td>";
                                                }

                                        $info.="</tr>";
                                    }
                                }
                        //     }
                        // }
                        if($category == "overtime"){
                            $info .= "
                                <tr>
                                <td colspan='4' align='right'>Sub total :&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td colspan='2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".number_format(floatval($summary[$sub_key]), 2)."</td>
                                </tr>
                            ";
                        }
                    }
                }
                if($category == "overtime"){
                    $info .= "
                        <tr> <td colspan='6' align='left' style='color:white;'>. </td></tr>
                        <tr>
                        <td colspan='4' align='right'>Grand total :&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td colspan='2'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".number_format(floatval($summary['grand_total']), 2)."</td>
                        </tr>
                    ";
                }
            }
            $info .= "</tbody>
        </table>
    </div>
    ";
}else{
       $info .= "
        <div>
            <table width='100%' border='1' align ='center'>
                <thead>
                    <tr>
                        <th align='center'>Employee ID</th>
                        <th align='center'>Fullname</th>
                        <th align='center'>Deficiency Date</th>
                        <th align='center'>Total Hours</th>
                        <th align='center'>Amount</th>
                        <th align='center'>Income Adjustment</th>
                        <th align='center'>Payroll Cutoff</th>
                        <th align='center'>Status</th>
                        <th align='center'>Adjusted By: </th>
                    </tr>
                </thead>
                <tbody>
                        ";
        foreach($att_adj as $key => $value){
            $info .= "<tr><td colspan='9'><b>".strtoupper($key)."<b><td></tr>";
            foreach($value as $row){
                $info .= "
                    <tr>
                        <td align='center'>".$row['employeeid']."</td>
                        <td>".$row['fullname']."</td>
                        <td align='center'>".$row['date']."</td>
                        <td align='center'>".(isset($row['total_hours']) ? $row['total_hours']." hours/s" : $row['total_days']." day/s")."</td>
                        <td align='center'>".number_format($row['amount'], 2)."</td>
                        <td align='center'>Adjustment</td>
                        <td align='center'>".$payroll_cutoff."</td>
                        <td align='center'>".$row['status']."</td>
                        <td align='center'>".$row['addedby']."</td>
                    </tr>
                ";
            }
        }
        $info .= "
            </tbody>
        </table>    
    </div>";
}

$pdf->WriteHTML($info);

$pdf->Output();
?>



