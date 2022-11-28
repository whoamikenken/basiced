<?php

/**
 * @author Glen Mark & Argyron
 * @copyright 2018
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
$generatedyear =Range($pyearfrom,$pyearto);
$generatedmonth = Range($pfrom,$pto);
$monthGeneratedbyUser = array();
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
$head = $this->reports->getDeptHead('HEAD','FIN');
$VPname = $this->reports->getVPFinanceHEAD($head);

$emplist = $philhealth = array();


$data = $this->reports->philhealthContributionPerMY($pfrom,$pto,$pyearfrom,$pyearto,$employeeid,$sort_by);
$datas = $this->reports->philhealthContributionPerMY($pfrom,$pto,$pyearfrom,$pyearto,$employeeid,$sort_by);
$currentMonth = $currentEe = $currentEc = $currentEr = '~~';
	foreach ($data as $k => $v) {
		$cutoffstart = $v->cutoffstart;
		$cutoffend  = $v->cutoffend;
		$fixeddeduc = $v->fixeddeduc;
		$cutoffYear = date('Y',strtotime($cutoffstart));
		$cutoffMonth = date('m',strtotime($cutoffstart));
		$fullname = $v->fullname;
		$philnumber = $v->philhealthnumber;
		$baseid = $v->id;
		$or = $v->or_number;
		$datepaid = $v->datepaid;
		$ee = $v->ee;
		$er = $v->er;
		$ec = $v->ec;
		$philhealthTotal = $ee + $er + $ec;
		#echo $cutoffMonth;
		$fixeddeducExplode = explode('/', $fixeddeduc);
		for ($i=0; $i <count($fixeddeducExplode) ; $i++) { 
				$explodedfix = explode('=', $fixeddeducExplode[$i]);
				if ($explodedfix[0] === "PHILHEALTH") {
					$philhealth	= $explodedfix[1];
				}
		}

		if($currentMonth == $cutoffMonth){
			$ee = $ee + $currentEe;
			$er = $er + $currentEr;
			$ec = $ec + $currentEc;
			$philhealthTotal = $ee + $er + $ec;
			$emplist[$v->employeeid][$cutoffYear][$cutoffMonth] = array("PHILHEALTH" =>$philhealth,'baseid'=>$baseid,"OR"=>$or,"Datepaid"=>$datepaid,"EE"=>$ee,"EC"=>$ec,"ER"=>$er,"PHILHEALTHTotal"=>$philhealthTotal);
		}else{
			$emplist[$v->employeeid][$cutoffYear][$cutoffMonth] = array("PHILHEALTH" =>$philhealth,'baseid'=>$baseid,"OR"=>$or,"Datepaid"=>$datepaid,"EE"=>$ee,"EC"=>$ec,"ER"=>$er,"PHILHEALTHTotal"=>$philhealthTotal);
		}
		$emplist[$v->employeeid]['info'] = array("fullname"=>$fullname,'philhealtno'=>$philnumber);

		$currentMonth = $cutoffMonth;
		$currentEe = $ee;
		$currentEc = $ec;
		$currentEr = $er;
		

	}
	
$pdf = new mpdf('P','LETTER','','UTF-8',5,5,8,5);
$info = "  <style>
			@media print {
			    .certi {page-break-before: always;}
			}
                @page{            
                    /*margin-top: 4.35cm;*/
                    margin-top: 3cm;
                    odd-header-name: html_Header;
                    odd-footer-name: html_Footer;
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
                .content{
                    height: 100%;
                    margin-top: 15px;
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
				.dataBold
				{
					font-weight:Bold;
					font-family:times new roman;
					font-size:12px;
				}
				.dataBoldTitle	
				{
					font-weight:Bold;
					font-family:Tahoma;
					font-size:15px;
				}
				.data  tr:nth-child(even)
				{
					 background-color:#C8C8C8;
					 
				}
				.font
				{
					font-size:12px;
					font-family:Tahoma;
				}
				.header{
					text-align:center;
				}
				.maintable
				{
					border:border-collapse;
				}
				.pg{
					page-break-after: always;
				}
				tr.noBorders td 
				{
				  
				  border-style:hidden;
				  border-bottom-style:solid;
				}
				.title td
				{
					text-align:center;

					
				}
				table{
                    border-collapse: collapse;
                    font-size: 12px;
                    border-spacing: 5px;
                }
                tr.noBorders td 
				{
				  
				  border-style:hidden;
				  border-bottom-style:solid;
				  border-top-style:solid;
				}
				#philhealth{
				    display:inline-block;
				    border-bottom:1px solid black;
				    padding-bottom:2px;
				}
				table,tr,td,div,span{
					font-family:times new roman;
				}
            </style>";

$infos .= "
<htmlpageheader name='Header'>
    <div>
        <table width='50%' style='padding: 0;' >
            <tr>
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo.jpg' style='width: 70px;' /></td>
                <td valign='bottom' width='300px' style='text-align: left;'><span style='font-size: 15px;'><b>".strtoupper($SCHOOL_NAME)."</b></span></td>
                
            </tr>
            <tr>
                
                <td valign='top' style='padding-right: 50px;text-left: center;'><span style='font-size: 10px;'></span></td>
            </tr>

        </table>

    </div>
</htmlpageheader>";


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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>Certificate of PHILHEALTH Contribution</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>As of ".date("F d, Y")."</strong></span></td>
            </tr>
            
        </table>
    </div>
</htmlpageheader>";

$header = array("YEAR","MONTH","AMOUNT EE","AMOUNT ER","PAYMENT REFERENCE#","DATE");
$emp_count = count($emplist);
$counter = 0;

foreach ($emplist as $employeeid => $field) {
	
	$counter ++;
	$empFullname = $field["fullname"];
     $empPHILno = $field["philhealtno"];
     $empid = $employeeid;
     $sameyear = "";

	$info .="<div class='dataBold'>DATE: ".date('F d, Y')."</div><div class='dataBold'>
	<span id='philhealth'>PHILHEALTH Contributions - Summary of Actual Premiums</span></div>
			<table width='100%' class='maintable' border=1>
				<thead>
				<tr class='noBorders'>
					<td width='15%'>Company Name </td><td width='35%' colspan='2'><b>Pinnacle Technologies Inc.</b></td><td width='15%'>&emsp;&emsp;&emsp;&emsp;&emsp;Employee&nbsp;Name </td><td width='35%' colspan='2'><b>&emsp;&emsp;".$field['info']["fullname"]."</b></td>
				</tr>
				<tr class='noBorders'>
					<td>Employer ID No. </td><td width='35%' colspan='2'><b>37-0077400-7</b></td><td>&emsp;&emsp;&emsp;&emsp;&emsp;PHILHEALTH&nbsp;ID&nbsp;No. </td><td width='35%' colspan='2'><b>&emsp;&emsp;".$field['info']["philhealtno"]."</b></td>
				</tr>
				<tr class='noBorders'>
					<td colspan='3'></td><td  >&emsp;&emsp;&emsp;&emsp;&emsp;Period&nbsp;Covered </td><td width='35%' colspan='2'><b>&emsp;&emsp;".$arrayMonths[$pfrom]." ".$pyearfrom." - ".$arrayMonths[$pto]." ".$pyearto."</b></td>
				</tr>
				<tr class='noBorders'>
					<td colspan='6'><b>Monthly Premiums</b></td>
				</tr>
				";
					
	$info .= "	
				</thead></table><table width='100%' class='maintable' border=1><thead><tr bgcolor='#000000'>";
				$widthper = count($header);
				$widthper = 100 / $widthper;

				foreach ($header as $title) {
						$info .= "<td color='yellow' class='header' width='".$widthper."%' style='text-align=\"center\"' ><b>".$title."</b></td>";
					}	
			$info  .="</tr></thead>";


							
			krsort($genYearMonth);
			foreach ($genYearMonth as $year =>$monthcode) {
				
				foreach ($arrayMonths as $days => $description) 
				{	
				  //show all the months that has been filtered by user
						if (isset($monthcode[$days])) {
						$datepaidFormat = isset($field[$year][$days]["Datepaid"])?date('m-d-Y',strtotime($field[$year][$days]["Datepaid"])):"";							
								if ($sameyear != $year) {
									$info .="
										<tr>
										<td>".$year."</td>
										<td>".$description."</td>";

										if (isset($field[$year][$days]["PHILHEALTH"])) {
// $info .="<td style='text-align:right'>".number_format($this->reports->getphilhealthContribution($field[$year][$days]['baseid'],$field[$year][$days]["PHILHEALTH"],'totalphilhealth'),2)."</td>
// <td style='text-align:right'>".number_format($this->reports->getphilhealthContribution($field[$year][$days]['baseid'],$field[$year][$days]["PHILHEALTH"],'er'),2)."</td>";
											$info .="<td style='text-align:right'>".number_format($field[$year][$days]["PHILHEALTHTotal"],2)."</td>
											<td style='text-align:right'>".number_format($field[$year][$days]["ER"],2)."</td>";
										}
										else
										{
											$info .="<td  style='text-align:right'>0.00</td><td style='text-align:right'>0.00</td>";
										}
										$info .="
												<td align='center'>".$field[$year][$days]["OR"]."</td>
												<td>".$datepaidFormat."</td>
												</tr>";
										$sameyear = $year;
								}
								else
								{
									$info .= "<tr>
											<td></td>
											<td>".$description."</td>";
									if (isset($field[$year][$days]["PHILHEALTH"])) {
// $info .="<td style='text-align:right'>".number_format($this->reports->getphilhealthContribution($field[$year][$days]['baseid'],$field[$year][$days]["PHILHEALTH"],'totalphilhealth'),2)."</td>
// 		<td style='text-align:right'>".number_format($this->reports->getphilhealthContribution($field[$year][$days]['baseid'],$field[$year][$days]["PHILHEALTH"],'er'),2)."</td>";
										$info .="<td style='text-align:right'>".number_format($field[$year][$days]["PHILHEALTHTotal"],2)."</td>
											<td style='text-align:right'>".number_format($field[$year][$days]["ER"],2)."</td>";		
									}
									else
									{
										$info .="<td  style='text-align:right'>0.00</td><td style='text-align:right'>0.00</td>";
									}
									$info .="
												<td align='center'>".$field[$year][$days]["OR"]."</td>
												<td>".$datepaidFormat."</td>
												</tr>";
								}
					    }

				}
			

			}
			
			$info .= "
			<tr class='noBorders' >
				<td>&nbsp;</td>
			</tr>
			<tr class='noBorders' >
				<td colspan='6'><div  class='font'>This cerfication is issued upon request of the above-mentioned name for PHILHEALTH reference.</div></td>
			</tr>
			<tr class='noBorders' >
				<td>&nbsp;</td>
			</tr>
			<tr class='noBorders' >
				<td>&nbsp;</td>
			</tr>
			<tr class='noBorders' >
				<td colspan='4'></td>
				<td ><div class='certi'><b>CERTIFIED CORRECT :</b></div></td>
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
				<td><div  class='approver'><b>".str_replace(' ', '&nbsp;', $this->employee->getfullname($certifiedcorrect))."</b></div></td>
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