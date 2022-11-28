
<?php
// Kennedy

require_once(APPPATH."constants.php");

$cdata = $result;

$extracol = "";
$seminarData = $this->reports->getSeminarData($scheddeptid, $dateFrom, $dateTo, $sortby, $officeid, $employee);   
$officeDesc = '';
$officeHeader = $officeid ? $this->extensions->getOfficeDescriptionReport($officeid) : '';
$grandtotal = array();
$grandName = array();
$rowspan = array();
$colspan = 'colspan="6"';
foreach($seminarData as $emp){
    if(array_key_exists($emp['employeeid'], $grandtotal)){
        $grandtotal[$emp['employeeid']] = $grandtotal[$emp['employeeid']]+$emp['total'];
        $rowspan[$emp['employeeid']] = $rowspan[$emp['employeeid']] + 1;
    }else{
        $grandtotal[$emp['employeeid']] = $emp['total'];
        $rowspan[$emp['employeeid']] = 1;
    }
}
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
                <td rowspan='4' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>As of ".date("Y/m/d")."</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>".$officeHeader."</strong></span></td>
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
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>EMPLOYEE ID</th>
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold; width='20%''>NAME</th>
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>DATE</th>
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>PLACE</th>
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>ORGANIZER</th>
            <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;' width='30%'>TITLE</th>";
            if($withAmount == 1){
                $colspan = 'colspan="11"';
                $info .="
                    <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>REGISTRATION FEE</th>
                    <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>TRANSPORTATION</th>
                    <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>ACCOMMODATION</th>
                    <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>TOTAL</th>
                    <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>GRAND TOTAL</th>
                    ";
            }
$info .= "</thead>";
$info .= "<tbody class='seminartbl'>";
        $CS="";
        $display= "";
        $counter = 0;
        $name = '';
        $grandtotals = 0;
        $fullname = '';
        $eid = '';
        $rowspans = '';
        $officetr = '1';
        $ofc = 'sometext';
            foreach($seminarData as $emp){
                if($sortby=='office' && !$officeid){
                    if($ofc !== $emp['office']){
                        if($officeDesc != $this->extensions->getOfficeDescriptionReport($emp['office'])){
                            $info .="  <tr style='border-left: 1px solid white'>
                                        <td style='font-size: 13px; color:  #000000; text-align: center;' ".$colspan.">&emsp;</td>
                                    </tr>";
                            $info .="  <tr style='background-color: yellow;'>
                                        <td style='font-size: 13px; color:  #000000; text-align: center;' ".$colspan."><b>".$this->extensions->getOfficeDescriptionReport($emp['office'])."</b></td>
                                    </tr>";
                           
                        }
                    }
                }

                $officeDesc = $this->extensions->getOfficeDescriptionReport($emp['office']);
                $fullname = $emp['fullname'];
                $eid = $emp['employeeid'];
                if($name == $fullname){
                    $eid = $fullname = $grandtotals = $rowspans = '';
                }else{
                    $grandtotals = number_format($grandtotal[$emp['employeeid']], 2);
                    $rowspans = 'rowspan="'.$rowspan[$emp['employeeid']].'"';
                }

                
                $info .= "<tr>
                    <td style='padding: 2px;text-align: center;font-size: 13px;'>".$eid."</td>
                    <td style='padding: 2px;text-align: center;font-size: 13px;'>".$fullname."</td>
                    <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp['daterange']."</td>
                    <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp['location']."</td>
                    <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp['organizer']."</td>
                    <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp['seminar_title']."</td>";
                    if($withAmount == 1){
                        $info .= "
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".number_format($emp['regfee'], 2)."</td>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".number_format($emp['transfee'], 2)."</td>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".number_format($emp['accfee'], 2)."</td>
                        <td style='padding: 2px;text-align: center;font-size: 13px;'>".number_format($emp['total'], 2)."</td>";

                        if($rowspans != ''){
                            $info .= "<td style='padding: 2px;text-align: center;font-size: 13px;' ".$rowspans.">".$grandtotals."</td>";
                        }
                }
                $info .= "</tr>";
                $name = $emp['fullname'];
                $ofc = $emp['office'];
            }

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



