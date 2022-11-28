<?php

/**
 * @author Justin
 * @copyright 2016
 */
/*end*/
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
     <div style='position: absolute;left:42%;top:07%;'><span style='font-size: 13px;'><b>&nbsp;&nbsp; Terminal Report</b></span></div>
     <div style='position: absolute;left:23%;top:09%;'><span style='font-size: 13px;'>&nbsp;&nbsp;LIST OF TERMINAL FOR THE PERIOD  ".$from_date." - ".$to_date."</span></div>
</htmlpageheader>";

    $info .= "
    <div>
        <table width='100%' border='1' align ='center'>
            <thead>";
                if($logs == "IN/OUT"){
                    $info .= "
                        <tr>
                            <th align='center' style='border:1px solid grey;color: yellow;background-color: black;'>Date</th>
                            <th align='center' style='border:1px solid grey;color: yellow;background-color: black;'>In</th>
                            <th align='center' style='border:1px solid grey;color: yellow;background-color: black;'>Out</th>
                        </tr>
                    ";
                }else if($logs == "IN"){
                    $info .= "
                        <tr>
                            <th align='center' style='border:1px solid grey;color: yellow;background-color: black;'>Date</th>
                            <th align='center' style='border:1px solid grey;color: yellow;background-color: black;'>In</th>
                        </tr>
                    ";
                }else{
                    $info .= "
                        <tr>
                            <th align='center' style='border:1px solid grey;color: yellow;background-color: black;'>Date</th>
                            <th align='center' style='border:1px solid grey;color: yellow;background-color: black;'>Out</th>
                        </tr>
                    ";
                }
    $info .= "     
            </thead>
            <tbody>";
                foreach($trail_records as $empid => $empdata){
                    if($logs == "IN/OUT"){
                        $info .= "<tr>
                            <td colspan='3'><b>".$this->extensions->getEmployeeName($empid)."</b></td>
                        </tr>";
                    }else{
                        $info .= "<tr>
                            <td colspan='2'><b>".$this->extensions->getEmployeeName($empid)."</b></td>
                        </tr>";
                    }
                    foreach($empdata as $date => $records){
                        if($logs == "IN/OUT"){
                            $info .= "<tr>
                                <td>".date('F d, Y', strtotime($date))."</td>
                                <td>".$records["in"]."</td>
                                <td>".$records["out"]."</td>
                            </tr>";
                        }else if($logs == "IN"){
                            $info .= "<tr>
                                <td>".date('F d, Y', strtotime($date))."</td>
                                <td>".$records["in"]."</td>
                            </tr>";
                        }else{
                           $info .= "<tr>
                                <td>".date('F d, Y', strtotime($date))."</td>
                                <td>".$records["out"]."</td>
                            </tr>";
                        }
                    }
                }
            $info .= "</tbody>
        </table>
    </div>
    ";

$pdf->WriteHTML($info);

$pdf->Output();

?>



