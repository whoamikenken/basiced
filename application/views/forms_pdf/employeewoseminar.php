
<?php
// Kennedy

require_once(APPPATH."constants.php");
$employeelist = $this->reports->getEmployeewith10kbalance($scheddeptid, $sortby, $officeid, $isactive);   
$officeDesc = '';
$colspan = 'colspan="5"';
$totalcolspan = 'colspan="4"';
$empcount = 1;
$counter = 0;
$officeHeader = $officeid ? $this->extensions->getOfficeDescriptionReport($officeid) : '';
$pdf = new mpdf('utf-8','LETTER-L','','UTF-8',5,5,8,8,9,2);
$info  = "  <style>
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
                <td rowspan='5' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 70px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='50%'><span style='font-size: 12px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 10px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>".strtoupper($reportTitle)." REPORT </strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 11px;' width='55%'>Date Range:  ".date("F d, Y", strtotime($dateFrom))." - ".date("F d, Y", strtotime($dateTo))."</span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 10px;' width='55%'><strong>".$officeHeader."</strong></span></td>
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
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>#</th>
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>EMPLOYEE ID</th>
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>LAST NAME</th>
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>FIRST NAME</th>
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>AMOUNT</th>
";
$info .= "</thead>";
$info .= "<tbody>";
        $CS="";
        $display= "";
        $counter = 0;
        $name = '';
        $grandtotals = $amount = $totalEmpCount = 0;
        $fullname = '';
        $eid = '';
        $rowspans = '';
        $officetr = '1';
        $ofc = 'sometext';
            foreach($employeelist as $emp){
                $amount = $this->reports->getSeminarAllowance($emp['employeeid'], $dateFrom, $dateTo);
                $seminarData = $this->reports->getSeminarData($scheddeptid, $dateFrom, $dateTo, $sortby, $officeid, $emp['employeeid'], $isactive); 
                if($sortby != "alphabets"){
                    if(!$officeid){
                        if($ofc !== $emp['office'] && (!$amount && count($seminarData) == 0)){
                            if($officeDesc != $this->extensions->getOfficeDescriptionReport($emp['office'])){
                                if($counter != 0){
                                    $empcount = $empcount - 1;
                                    $info .="  <tr>
                                                <td style='font-size: 13px; color:  #000000; text-align: right; padding-right: 10px;'  ".$totalcolspan."><b>Total:</b></td>
                                                <td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$empcount."</b></td>
                                            </tr>";
                                    $info .="  <tr style='border-left: 1px solid white'>
                                                <td style='font-size: 13px; color:  #000000; text-align: left;' ".$colspan.">&emsp;</td>
                                            </tr>";
                                            $empcount = 1;
                                            $counter = 0;
                                }
                                $info .="  <tr style='background-color: yellow;'>
                                            <td style='font-size: 13px; color:  #000000; text-align: left;' ".$colspan."><b>".$this->extensions->getOfficeDescriptionReport($emp['office'])."</b></td>
                                        </tr>";
                               
                            }
                        }
                    }
                }
                $officeDesc = $this->extensions->getOfficeDescriptionReport($emp['office']);
                
                if(!$amount && count($seminarData) == 0){
                    $info .= "<tr>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$empcount."</td>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp['employeeid']."</td>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp['lname']."</td>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp['fname']."</td>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>PHP&nbsp;10,000.00</td>";
                    $info .= "</tr>";
                    $name = $emp['fullname'];
                    $ofc = $emp['office'];
                    $empcount++;
                    $counter++;
                    $totalEmpCount++;
                }
                
            }

            $empcount = $empcount - 1;
            $info .="  <tr>
                        <td style='font-size: 13px; color:  #000000; text-align: right; padding-right: 10px;'  ".$totalcolspan."><b>Total:</b></td>
                        <td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$empcount."</b></td>
                    </tr>";
            $info .="  <tr style='border-left: 1px solid white'>
                        <td style='font-size: 13px; color:  #000000; text-align: left;' ".$colspan.">&emsp;</td>
                    </tr>";
            $info .="  <tr>
                        <td style='font-size: 13px; color:  #000000; text-align: right; padding-right: 10px;'  ".$totalcolspan."><b>Total Employee:</b></td>
                        <td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$totalEmpCount."</b></td>
                    </tr>";
            $info .="  <tr style='border-left: 1px solid white'>
                        <td style='font-size: 13px; color:  #000000; text-align: left;' ".$colspan.">&emsp;</td>
                    </tr>";
            

$info .= "      
            </tbody>
        </table>
    </div>
</div>";
// echo "<pre>"; print_r($info); die;
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



