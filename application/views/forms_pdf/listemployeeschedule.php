<?php

/**
 * @author Justin
 * @copyright 2016
 */
require_once(APPPATH."constants.php");

$division  =  isset($scheddiv) ? $scheddiv : ''; 
$deptid    = isset($scheddeptid) ? $scheddeptid : '';
$tnt       = isset($schedtnt) ? $schedtnt : '';
$dfrom     =  isset($scheddfrom) ? $scheddfrom : '';
$isactive 	 = isset($isactive) ? $isactive : '';
$cdata      = $this->reports->loadempdataschedule($division,$deptid,$tnt,$dfrom,$isactive);
$lastCount = count($cdata);   
// var_dump($cdata);
// die();
// $pdf = new mpdf('utf-8','LETTER-L','','UTF-8',5,5,5,5);
$pdf = new mPDF('utf-8','A4','10','','10','10','10','8','','2');
$pdf->shrink_tables_to_fit=1;
// $pdf->keep_table_proportions = true;
$info  = "  <style>
                @page{            
                    /*margin-top: 4.35cm;*/
                    margin-top: 3cm;
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
                    overflow: wrap;
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
				
                .sched tr:nth-child(odd) {background-color: #f0f0f0}
            </style>";
$info .= "
<htmlpageheader name='Header'>
<br>
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
$empcount= 1;
$counter = $lastTotal = 0 ;
$info .= "
<div class='content'>
    <div class='content-header'>
		<b><h2>EMPLOYEE SCHEDULE</h2></b>
	</div>
	<div style='margin-top:-3%;'>";
		
		$dept="";
		foreach($cdata as $emp){
			$lastTotal++;
			$sched="";
			$info .= "<table width='100%' style='text-align:center;margin-top:2%'>";
			if($dept != $emp->deptid)
			{
				if($counter != 0){
                    $empcount = $empcount - 1;
                    $info .="  <tr>
                                <td style='font-size: 13px; color:  #000000; text-align: right; padding-right: 10px;'  colspan='3'><b>Total:</b></td>
                                <td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$empcount."</b></td>
                            </tr>";
                    $info .="  <tr style='border-left: 1px solid white'>
                                <td style='font-size: 13px; color:  #000000; text-align: left;' colspan='4'>&emsp;</td>
                            </tr>";
                            $empcount = 1;
                            $counter = 0;
                }
				$info .="  <tr >
                                <td style='font-size: 13px; color:  #000000; text-align: left;' colspan='4'><b>".$this->extensions->getDeparmentDescriptionReport($emp->deptid)."</b></td>
                            </tr>";
				// $info .= "<thead><span style='text-align:left;margin-top:5%;margin-bottom:-0.5%'>Department : ".$this->extras->getDeptDesc($emp->deptid)."</span>";
				$dept = $emp->deptid;
				$info .= "<thead>
						<tr>
							<th width='5%'>#</th>
							<th width='15%'>EMPLOYEE ID</th>
							<th width='20%'>EMPLOYEE NAME</th>
							<th width='60%'>SCHEDULE</th>
						</tr>
						";
			}
			
			$info .= "</thead><tr>";
				$info .= "<td width='5%'>".$empcount."</td>";
				$info .= "<td width='15%'>".$emp->employeeid."</td>";
				$info .= "<td width='20%' style='text-align:left'>".$emp->fullname."</td>";
				$info .= "<td width='60%'>";
				$sched = $this->extras->getEmpSchedule($emp->employeeid);
				// echo "<pre>"; print_r($sched); die;
				// $darray = array("M","T","W","TH","F","S");
				// $d = explode(",",$darray);
				
				if($sched)
				{
					$info .= "<table class='sched' style='width:100%; white-space:nowrap;'>";
					$day=$day1=$start1=$end2=$start1=$end2="";
					$string ="";
					$i=0;	
						foreach($sched as $row)
						{
							$i += 1;
							
							if($day != $row->dayofweek)
							{
							$day = $row->dayofweek;
							
							
							$start1 = date("g:i A", strtotime($row->starttime));
							$end1 = date("g:i A", strtotime($row->endtime));
							// $info .= "<tr>
								// <td width='40%' style='text-align:center'>".$day." ".$start." - ".$end."</td>
							// </tr>";
							
								if($day != $sched[$i]->dayofweek)
								{
									
									if($row->dayofweek == "M") $day1 ="MONDAY";
									else if($row->dayofweek == "T") $day1 ="TUESDAY";
									else if($row->dayofweek == "W") $day1 ="WEDNESDAY";
									else if($row->dayofweek == "TH") $day1 ="THURSDAY";
									else if($row->dayofweek == "F") $day1 ="FRIDAY";
									else if($row->dayofweek == "S") $day1 ="SATURDAY";
									else if($row->dayofweek == "SUN") $day1 ="SUNDAY";
									
									$info .= "<tr>
									<td width='20%' style='text-align:left; white-space: nowrap;'>".$day1."</td>
									<td width='10.7%' style='text-align:left; white-space: nowrap;'>".$start1."</td>
									<td width='10.7%' style='text-align:center; white-space: nowrap;'> - </td>
									<td width='10.7%' style='text-align:left; white-space: nowrap;'>".$end1."</td>
									<td width='10.7%' style='text-align:left; white-space: nowrap;'> </td>
									<td width='10.7%' style='text-align:left; white-space: nowrap;'> </td>
									<td width='10.7%' style='text-align:left; white-space: nowrap;'> </td>
									<td width='10.7%' style='text-align:left; white-space: nowrap;'> </td>
								</tr>";
								}
								else
								{
									$start2 = date("g:i A", strtotime($sched[$i]->starttime));
									$end2 = date("g:i A", strtotime($sched[$i]->endtime));
									$string .= " | " . $start2 . " - " . $end2;
									
									if($row->dayofweek == "M") $day1 ="MONDAY";
									else if($row->dayofweek == "T") $day1 ="TUESDAY";
									else if($row->dayofweek == "W") $day1 ="WEDNESDAY";
									else if($row->dayofweek == "TH") $day1 ="THURSDAY";
									else if($row->dayofweek == "F") $day1 ="FRIDAY";
									else if($row->dayofweek == "S") $day1 ="SATURDAY";
									else if($row->dayofweek == "SUN") $day1 ="SUNDAY";
									
									$info .= "<tr>
									<td width='20%' style='text-align:left; white-space: nowrap;'>".$day1."</td>
									<td width='10.7%' style='text-align:left; white-space: nowrap;'>".$start1."</td>
									<td width='10.7%' style='text-align:center; white-space: nowrap;'> - </td>
									<td width='10.7%' style='text-align:left; white-space: nowrap;'>".$end1."</td>
									<td width='10.7%' style='text-align:center; white-space: nowrap;'> | </td>
									<td width='10.7%' style='text-align:left; white-space: nowrap;'>".$start2."</td>
									<td width='10.7%' style='text-align:center; white-space: nowrap;'> - </td>
									<td width='10.7%' style='text-align:left; white-space: nowrap;'>".$end2."</td>
								</tr>";
								}
							
							}
							// else
							// {
								// $day = $row->dayofweek;
								// $start2 = date("g:i A", strtotime($row->starttime));
								// $end2 = date("g:i A", strtotime($row->endtime));
								// $string .= " | " . $start2 . " - " . $end2;
								
								// if($row->dayofweek == "M") $day ="MONDAY";
								// else if($row->dayofweek == "T") $day ="TUESDAY";
								// else if($row->dayofweek == "W") $day ="WENESDAY";
								// else if($row->dayofweek == "TH") $day ="THURSDAY";
								// else if($row->dayofweek == "F") $day ="FRIDAY";
								// else if($row->dayofweek == "S") $day ="SATURDAY";
								
								// $info .= "<tr>
								// <td width='30%' style='text-align:center'>".$day."</td>
								// <td width='70%' style='text-align:center'>".$string."</td>
							// </tr>";
							// }
						}
					$info .= "</table>";
				}
				
			$info .= "</td>";
				
			$info .= "</tr>";
			if($lastCount == $lastTotal){
				$info .= "<tr>
                                <td style='font-size: 13px; color:  #000000; text-align: right; padding-right: 10px;'  colspan='3'><b>Total:</b></td>
                                <td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$empcount."</b></td>
                            </tr></table>";
			}else{
				$info .= "</table>";
			}
			$empcount++;
			$counter++;
		
		}
        
$info .= "  
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



