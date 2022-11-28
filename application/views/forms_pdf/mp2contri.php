<?php

/**
 * @author Hyperion Programmers
 * @copyright 2020
 */
require_once(APPPATH."constants.php");
// $this->load->library('lib_includer');

set_time_limit(0);
ini_set('memory_limit',-1);
$arrayMonths = array(
					'12'=>"December",
					'11'=>"November",
					'10'=>"October",
					'09'=>"September",
					'08'=>"August",
					'07'=>"July",
					'06'=>"June",
					'05'=>"May",
					'04'=>"April",
					'03'=>"March",
					'02'=>"February",
					'01'=>"January"
					);


function AddZero($val)
{
	$months = "";
	if (strlen($val) == 1) {
		$months = "0".$val;
	}
	else
	{
		$months = $val;
	}
	return $months;
}
function Addkeys($rangefrom,$rangeto)
{
	$yearList = array();
	$yearRange = Range($rangefrom,$rangeto);
	foreach ($yearRange as $year) {
		$yearList[AddZero($year)] = AddZero($year);
	}
	return $yearList;
}
$genYearMonth = array();
$generatedyear = Range($pyearfrom,$pyearto);
$generatedmonth = Range($pfrom,$pto);
$monthGeneratedbyUser = array();
//generate period with same year
$gensameyear = Addkeys($pfrom,$pto);
//generate period from
$genyearfrom = Addkeys($pfrom,12);
//generate period between from and to
$genyearfromto = Addkeys(1,12);
//generate period to
$genyearto = Addkeys(1,$pto);


foreach ($generatedyear as $year) {
	if ($year == $pyearfrom && $year == $pyearto ) {
		$genYearMonth[$pyearfrom] = ($gensameyear) ;
	}
	else if ($year == $pyearfrom) {
		$genYearMonth[$pyearfrom] =($genyearfrom);
	}
	else if ($year == $pyearto) {
		$genYearMonth[$pyearto] = ($genyearto);
	}
	else
	{
		$genYearMonth[$year] =($genyearfromto);	
	}
}


	

//get all the data
$emplist = $sss = array();

$head = $this->reports->getDeptHead('HEAD','FIN');
$VPname = $this->reports->getVPFinanceHEAD($head);
$currentMonth = $currentEe = $currentEc = $currentEr = $currentmp2 = '~~';
$data = $this->reports->MP2VoluntaryContributionPerMY($pfrom,$pto,$pyearfrom,$pyearto,$employeeid, $sort_by);
// echo "<pre>"; print_r($data); die;
$datas = $this->reports->MP2VoluntaryContributionPerMY($pfrom,$pto,$pyearfrom,$pyearto,$employeeid , $sort_by);
	foreach ($data as $k => $val) {
		$cutoffstart = $val->cutoffstart;
		$cutoffend  = $val->cutoffend;
		$otherdeduc = $val->otherdeduc;
		$cutoffYear = date('Y',strtotime($cutoffstart));
		$cutoffMonth = date('m',strtotime($cutoffstart));
		$fullname = $val->fullname;
		$baseid = $val->id;
		$mp2 = 0;

		
		$otherdeducExplode = explode('/', $otherdeduc);
		for ($i=0; $i <count($otherdeducExplode) ; $i++) { 
				$explodedfix = explode('=', $otherdeducExplode[$i]);
				if ($explodedfix[0] === "1") {
					$mp2 = $explodedfix[1];
				}
		}
		$mp2total = $mp2;
		if($mp2 > 0){
			if($currentMonth == $cutoffMonth){
				$mp2total = $currentmp2 + $mp2;
				$emplist[$val->employeeid][$cutoffYear][$cutoffMonth] = array("mp2" =>$mp2,'baseid'=>$baseid,"OR"=>$val->or_number,"Datepaid"=>$val->datepaid,"mp2total"=>$mp2total);
			}else{
				$emplist[$val->employeeid][$cutoffYear][$cutoffMonth] = array("mp2" =>$mp2,'baseid'=>$baseid,"OR"=>$val->or_number,"Datepaid"=>$val->datepaid,"mp2total"=>$mp2total);
			}
			$emplist[$val->employeeid]['info'] = array("fullname"=>$fullname,'mp2total'=>$mp2total);
		}
		$currentMonth = $cutoffMonth;
		$currentmp2 = $mp2;
	}

$pdf = new mpdf('P','LETTER','','UTF-8',5,5,8,5);

// $image = $imgurl."images/school_logo.jpg";
// $pdf->SetWatermarkImage($image);
// $pdf->showWatermarkImage = true;
$info = "  <style>
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
				.title td
				{
					text-align:center;

					
				}
				.dataBold
				{
					font-weight:Bold;
					font-family:times new roman;
					font-size:12px;
				}
				.dataBoldTitle	
				{
					font-weight:Bold;
					font-family:times new roman;
					font-size:15px;
				}
				.font
				{
					font-size:12px;
					font-family:times new roman;
				}
				.pg{
					page-break-after: always;
				}
				.data  tr:nth-child(even)
				{
					 background-color:#C8C8C8;
					 
				}
				.maintable
				{
					border:border-collapse;
					// page-break-inside:avoid; margin: 10px 0 10px 0;
				}
				tr.noBorders td 
				{
				  
				  border-style:hidden;
				  border-bottom-style:solid;
				  border-top-style:solid;
				}
				.certi
				{
					margin-top:1em auto;
					margin-left:27.5em auto;
					font-size:13px;
					font-family:times new roman;
				}
				.approver
				{
					font-family:Arial;
					margin-top:2em auto;
					font-family:times new roman;
					margin-left:27em auto;
					font-size:13px;
					display:inline-block;
				    border-bottom:1px solid black;
				    padding-top:2px;
				}
				.approverposition
				{
					margin-left:27em auto;
					font-family:times new roman;
					font-size:10px;
				}
				#sss{
				    display:inline-block;
				    border-bottom:1px solid black;
				    padding-bottom:2px;
				    border-color:black;
				    font-family:times new roman;
				}
				#fontfam
				{
					font-family:times new roman;
				}
			
            </style>";

$infos = "
<htmlpageheader name='Header'>
    <div>
        <table width='50%' style='padding: 0;' >
            <tr >
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo.jpg' style='width: 70px;' /></td>
                <td valign='bottom' width='350px' style='text-align: left;'><span style='font-size: 15px;font-family:times new roman;'><b>".strtoupper($SCHOOL_NAME)."</b></span></td>
                
            </tr>
            <tr>
                
                <td valign='top' style='padding-right: 50px;text-left: center;'><span style='font-size: 10px;font-family:times new roman;'></span></td>
            </tr>

        </table>

    </div>
</htmlpageheader>";

$info .= "
<htmlpageheader name='Header'>
    <div>
        <table width='55%'  >
            <tr>
                <td rowspan='4' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 13px; font-family:times new roman;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px; font-family:times new roman;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px; font-family:times new roman;' width='55%'><strong>Certificate of Modified Pag-IBIG 2 Contribution</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px; font-family:times new roman;' width='55%'><strong>As of ".date("F d, Y")."</strong></span></td>
            </tr>
            
        </table>
    </div>
</htmlpageheader>";
$fieldname = array("YEAR","MONTH","AMOUNT","REFERENCE NUMBER","DATE PAID");
$widthArr = array("10","15","15","30","30");
$emp_count = count($emplist);
$counter = 0;
foreach ($emplist as $employeeid => $field) {
	$counter ++;
     $empFullname = $field['info']["fullname"];
	 $empid = $employeeid;


		$sameyear = "";
		$info .="
				<table width='100%' class='maintable' border=1>
					<thead>
					<tr class='noBorders' >
						<td colspan='6'><div  class='font'>This is to certify that the following are the MP2 Contributions of Mr/Ms ".$empFullname." with an MP2 amount of ".number_format($field['info']['mp2total'], 2).".</div></td>
					</tr>
					<tr class='noBorders' >
						<td>&nbsp;</td>
					</tr>
					<tr bgcolor='#000000'>";
					foreach ($fieldname as $key =>$title) 
					{
						$info .= "<td color='yellow' align='center' id='fontfam' width='".$widthArr[$key]."%'><b>".$title."</b></td>";
					}
		$info  .="</tr></thead>";	

			krsort($genYearMonth);
			foreach ($genYearMonth as $year =>$monthcode) {
					
				foreach ($arrayMonths as $days => $description) 
				{	

						
						//show all the months that has been filtered by user
						if (isset($monthcode[$days])) {
					
									$SSSdatepaid= isset($field[$year][$days]["Datepaid"])?date('m-d-Y',strtotime($field[$year][$days]["Datepaid"])):"";
								if ($sameyear != $year) {
									$info .="<tbody>
										<tr>
										<td>".$year."</td>
										<td>".$description."</td>";

										
										if (isset($field[$year][$days]["mp2"])) {

											$info .="<td style='text-align:right' id='fontfam'>".number_format($field[$year][$days]["mp2total"],2)."</td>";
											// <td style='text-align:right' id='fontfam'>".number_format($field[$year][$days]["EC"],2)."</td>";
											// $info .="<td style='text-align:right' id='fontfam'>".number_format($this->reports->getSSSContribution($field[$year][$days]['baseid'],$field[$year][$days]["SSS"],'totalsss'),2)."</td>
											// <td style='text-align:right' id='fontfam'>".number_format($this->reports->getSSSContribution($field[$year][$days]['baseid'],$field[$year][$days]["SSS"],'ec'),2)."</td>";
										}
										else
										{
											$info .="<td style='text-align:right' id='fontfam'> 0.00</td>";
										}
										$info .="
												<td align='center'>".$field[$year][$days]["OR"]."</td>
												<td>".$SSSdatepaid."</td>
												</tr>";
										$sameyear = $year;
								}
								else
								{
									$info .= "<tr>
											<td></td>
											<td>".$description."</td>";
									if (isset($emplist[$empid][$year][$days]["mp2"])) {
										$info .="<td style='text-align:right' id='fontfam'>".number_format($field[$year][$days]["mp2total"],2)."</td>";
											// <td style='text-align:right' id='fontfam'>".number_format($field[$year][$days]["EC"],2)."</td>";
										// $info .="<td style='text-align:right' id='fontfam'>".number_format($this->reports->getSSSContribution($field[$year][$days]['baseid'],$field[$year][$days]["SSS"],'totalsss'),2)."</td>
										// 		<td style='text-align:right' id='fontfam'>".number_format($this->reports->getSSSContribution($field[$year][$days]['baseid'],$field[$year][$days]["SSS"],'ec'),2)."</td>";
									}
									else
									{
										$info .="<td style='text-align:right' id='fontfam'>0.00</td>";
									}
									$info .="<td align='center'>".$field[$year][$days]["OR"]."</td>
											<td align='center'>".$SSSdatepaid."</td>
											 </tr></tbody>";
								}
					
						}
							
				}
			}

	$info .= "
			<tr class='noBorders' >
				<td>&nbsp;</td>
			</tr>
			<tr class='noBorders' >
				<td colspan='6'><div  class='font'>This cerfication is issued upon request of the above-mentioned name for MP2 reference.</div></td>
			</tr>
			<tr class='noBorders' >
				<td>&nbsp;</td>
			</tr>
			<tr class='noBorders' >
				<td>&nbsp;</td>
			</tr>
			<tr class='noBorders' >
				<td colspan='4'></td>
				<td ><div class='certi'><b>Certified Correct :</b></div></td>
			</tr>
			<tr class='noBorders' >
				<td>&nbsp;</td>
			</tr>
			<tr class='noBorders' >
				<td>&nbsp;</td>
			</tr>
			<tr class='noBorders' >
				<td></td>
			</tr>
			
			<tr class='noBorders' >
				<td colspan='4'></td>
				<td><div  class='approver'><b>".$this->employee->getfullname($certifiedcorrect)."</b></div></td>
			</tr>
			<tr class='noBorders' >
				<td colspan='4'></td>
				<td ><div  class='approverposition'>".$this->extensions->getPositionDesc($this->extensions->getEmployeePositionId($certifiedcorrect))."</div></td>
			</tr>
	</table>
	
	
	";
	if($counter < $emp_count) $info .= "<pagebreak>";
}

	

$pdf->WriteHTML($info);
$pdf->Output();
?>