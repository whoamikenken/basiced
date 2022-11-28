
<?php
// Kennedy

require_once(APPPATH."constants.php");

$employementhistorydata = $this->reports->getEmploymentHistoryData($officeid, $employee, $isactive); 
$officeDesc = '';
$officeHeader = $officeid ? $this->extensions->getOfficeDescriptionReport($officeid) : '';
$colspan = 'colspan="9"';
$totalcolspan = 'colspan="8"';
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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 11px;' width='55%'>As of ".date("F Y")."</span></td>
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
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold;'>#</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold;'>EMPLOYEE ID</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold;'>EMPLOYEE NAME</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold;'>DEPARTMENT</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold;'>OFFICE</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold;'>EMPLOYMENT STATUS</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold;'>POSITION</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold;'>START DATE</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold;'>END DATE</th>";

$info .= "</thead>";
$info .= "<tbody class='seminartbl'>";
        $CS="";
        $display= "";
        $counter =  0;
        $name = '';
        $grandtotals = 1;
        $empcount = 0;
        $fullname = '';
        $eid = '';
        $rowspans = '';
        $officetr = '1';
        $ofc = 'sometext';
        $lastemployee = '';
        $span = array();
        $sp = 1;
        $firstload = true;

        foreach($employementhistorydata as $emp){
            $eid = $emp['employeeid'];

            if (!$span[$eid]) {
                $span[$eid] = 1;
            }

            if ($lastemployee == $emp['employeeid']) {
                $span[$eid] += 1;
            }


            $lastemployee = $emp['employeeid'];
        }

            foreach($employementhistorydata as $emp){// if(!$officeid){
                //     if($ofc !== $emp['office']){
                //         if($officeDesc != $this->extensions->getOfficeDescriptionReport($emp['office'])){
                //             if($counter != 0){
                //                 $empcount = $empcount - 1;
                //                 $info .="  <tr>
                //                             <td style='font-size: 11px; color:  #000000; text-align: right; padding-right: 10px;'  ".$totalcolspan."><b>Total:</b></td>
                //                             <td style='font-size: 11px; color:  #000000; text-align: center; '><b>".$empcount."</b></td>
                //                         </tr>";
                //                 $info .="  <tr style='border-left: 1px solid white'>
                //                             <td style='font-size: 11px; color:  #000000; text-align: left;' ".$colspan.">&emsp;</td>
                //                         </tr>";
                //                         $empcount = 1;
                //                         $counter = 0;
                //             }
                //             $info .="  <tr style='background-color: yellow;'>
                //                         <td style='font-size: 11px; color:  #000000; text-align: left;' ".$colspan."><b>".$this->extensions->getOfficeDescriptionReport($emp['office'])."</b></td>
                //                     </tr>";
                           
                //         }
                //     }
                // }

                $officeDesc = $this->extensions->getOfficeDescriptionReport($emp['office']);
                $fullname = $emp['fullname'];
                $eid = $emp['employeeid'];
                // if($name == $fullname){
                //     $eid= $fullname ='';
                // }
                if ($lastemployee == $emp['employeeid'] && !$firstload) {
                    $sp += 1;
                }
                else{
                    $sp = 1;
                    $empcount++;
                }
                
                $span[$eid] += 1;
                if ($sp == 1) {
                    $info .= "<tr>
                    <td style='padding: 2px;text-align: center;font-size: 11px;' rowspan='".$span[$eid]."'>".$empcount."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;' rowspan='".$span[$eid]."'>".$eid."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;' rowspan='".$span[$eid]."'>".$fullname."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$this->extensions->getDeparmentDescriptionReport($this->reports->getEmpDetailbyid($eid)->row()->deptid)."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$this->extensions->getOfficeDescriptionReport($this->reports->getEmpDetailbyid($eid)->row()->office)."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$this->extensions->getEmpstatusdesc($this->reports->getEmpDetailbyid($eid)->row()->employmentstat)."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$this->extensions->getpositiondesc($this->reports->getEmpDetailbyid($eid)->row()->positionid)."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$this->reports->getEmpDetailbyid($eid)->row()->dateposition."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$this->reports->getEmpDetailbyid($eid)->row()->dateresigned."</td></tr>";

                    $info .= "<tr>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp['departmentDesc']."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$officeDesc."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp['employmentstatDesc']."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp['positionDesc']."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp['dateposition']."</td>
                    <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp['dateresigned']."</td></tr>";
                }
                else{
                    $info .= "<tr>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp['departmentDesc']."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$officeDesc."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp['employmentstatDesc']."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp['positionDesc']."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp['dateposition']."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp['dateresigned']."</td></tr>";
                }

                // $info .= "</tr>";
                $name = $emp['fullname'];
                $ofc = $emp['office'];
                $counter++;
                $lastemployee = $emp['employeeid'];
                $firstload = false;
            }

            $empcount = $empcount ;
            $info .="  <tr>
                        <td style='font-size: 11px; color:  #000000; text-align: right; padding-right: 10px;'  ".$totalcolspan."><b>Total:</b></td>
                        <td style='font-size: 11px; color:  #000000; text-align: center; '><b>".$empcount."</b></td>
                    </tr>";
            $info .="  <tr style='border-left: 1px solid white'>
                        <td style='font-size: 11px; color:  #000000; text-align: left;' ".$colspan.">&emsp;</td>
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
// echo $info;
// echo "<pre>"; print_r($span); echo "</pre>";
$pdf->WriteHTML($info);

$pdf->Output();
?>



