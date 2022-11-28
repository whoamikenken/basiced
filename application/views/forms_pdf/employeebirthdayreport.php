<?php
// Kennedy

require_once(APPPATH."constants.php");
$type  =  isset($birthdayInfo) ? $birthdayInfo : '';
$month    =  isset($empBirthdayMonth) ? $empBirthdayMonth : '';
$isactive    = isset($isactive) ? $isactive : '';


if ($type == "Age" && $month == "All") {
    $report = "AGE REPORT";
    $result  = $this->reports->loadempbirthdayreportage($isactive);   
}else if($type == "Month" && $month == "All"){
    $report = "ALL MONTH";
    $result  = $this->reports->loadempbirthdayreportall($isactive); 
}else{
    $dateObj   = DateTime::createFromFormat('!m', $month);
    $report = $dateObj->format('F');
    $result  = $this->reports->loadempbirthdayreportmonth($month, $isactive); 
}
// echo "<pre>"; print_r($this->db->last_query()); die;
$cdata = $result;

$extracol = "";
		
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
                #datas tr:nth-child(odd)
                {
                	background-color:#C8C8C8;
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
                <td rowspan='3' style='text-align: right;' width='60%'><img src='images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>";
        if ($report == 'AGE REPORT') {
            $info .= "<td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>EMPLOYEE'S AGE REPORT</strong></span></td>";
        }
        elseif($report == 'ALL MONTH'){
            $info .= "<td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>ALL EMPLOYEE'S BIRTHDAY BY MONTH</strong></span></td>";
        }
        else{
            $info .= "<td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>EMPLOYEE'S BIRTHDAY FOR ".strtoupper($report)."</strong></span></td>";
            //<td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>EMPLOYEE'S BIRTHDAY BY ".strtoupper($report)."</strong></span></td>    
        }
$info .= "</tr>
        </table>
    </div>
</htmlpageheader>";

$info .= "

<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
        	<thead>
            <tr style='background-color: #000000;'>
            <th align='center' colspan=''>#</th>
            <th align='center' colspan=''>EMPLOYEE ID</th>
            <th align='center' colspan=''>NAME</th>
            <th align='center' colspan=''>OFFICE</th>";
            //<th align='center' colspan=''>BIRTHDAY</th>
            //<th align='center' colspan=''>AGE</th>
            if ($report == "AGE REPORT") {
                $info .= "<th align='center' colspan=''>AGE</th>";
            }
            else{
                $info .= "<th align='center' colspan=''>BIRTHDAY</th>";
            }
$info .= "</thead>";
$empcount = 1;
foreach($cdata as $emp){

$info .= "<tbody><tr>";
					$info .= "
                            <td align='center'>".$empcount."</td>
							<td align='center'>".$emp->employeeid."</td>
							<td align='center'>".$emp->fullname."</td>
							<td align='center'>".$this->extensions->getOfficeDesc($emp->office)."</td>";
							// <td align='center'>".date('F  d,  Y', strtotime($emp->bdate))."</td>";
                            if ($report == "AGE REPORT") {
                                if (date_diff(date_create($emp->bdate), date_create('now'))->y == '2019') {
                                    $info .= "<td align='center'>Null</td>";
                                }else{
                                    $bday = new DateTime($emp->bdate);
                                    $today = new Datetime(date('m.d.y'));
                                    $diff = $today->diff($bday);
                                    if($emp->bdate == "0000-00-00" || $emp->bdate == "1970-01-01" || $emp->bdate == null){
                                        $info .= "<td align='center'></td>";
                                    }else{
                                        $info .= "<td align='center'>".$diff->y."</td>";
                                    }
                                    
                                }

                            }
                            else{
                                if($emp->bdate == "0000-00-00" || $emp->bdate == "1970-01-01" || $emp->bdate == null){
                                    $info .= "<td align='center'></td>";
                                }else{
                                    $info .= "<td align='center'>".date('F  d,  Y', strtotime($emp->bdate))."</td>";
                                }
                                
                            }
    $empcount++;
}
$info .= "</tr>";
$empcount = $empcount - 1;
$info .="  <tr>
            <td style='font-size: 13px; color:  #000000; text-align: right; padding-right: 10px;'  colspan='4'><b>Total:</b></td>
            <td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$empcount."</b></td>
        </tr>";
$info .= "   </tbody>   
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



