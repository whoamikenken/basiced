<?php

/**
 * @author Justin
 * @copyright 2016
 */
ini_set('max_execution_time', 0);
ini_set("memory_limit", "2G");

require_once(APPPATH."constants.php");
$from_date  = isset($dfrom) ? $dfrom : '';
$to_date    = isset($dto) ? $dto : '';
$dept       =  isset($deptid) ? $deptid : '';
$tnt        =  isset($tnt) ? $tnt : '';
$estatus    = isset($estatus) ? $estatus : '';
$edata      =  isset($edata) ? $edata : '';
$division      =  isset($division) ? $division : '';
$department    = isset($department) ? $department : '';
$employee      = isset($employee) ? $employee : '';
$campus 	 = isset($campus) ? $campus : '';
$isactive 	 = isset($isactive) ? $isactive : '';

$result      = $this->reports->loadempdata($edata, $division, $department, $employee,$campus,$isactive);  
$cdata = $empstathistoryquery = $familyquery =  $childrenquery = $taxDependentsquery = "";

$cdata = $result;

$extracol = "";

$empstathistory = array("managementid2","deptid2","employmentstat2","positionid2","dateposition2");
$empstathistorycol = "";
$empstathistoryexist = 0;

$family = array("fmname","fmrelation","fmdob");
$familycol = "";
$familyexist = 0;

$children = array("childname","childbday","childage");
$childrencol = "";
$childrenexist = 0;

$taxDependents = array (   "tdname","tdrelation","tdaddress","tdcontact","tdbdate","tdlegitimate");
$taxDependentscol = "";
$taxDependentsexist = 0;

$father = array (   "father","fatheroccu","fatherstatus","fatheraddress","fathernumber");
$fathercol = "";
$fatherexist = 0;

$mother =  array (   "mother","motheroccu","motherstatus","motheraddress","mothernumber");
$mothercol = "";
$motherexist = 0;

$spouse =  array ( "spouse_name","occupation","spousestatus","spouseaddress","spousebaddress","spousenumber");
$spousecol = "";
$spouseexist = 0;

$immigrationDetails =  array ( "passport","visa","icardnum","crnno");
$immigrationDetailscol = "";
$immigrationDetailsexist = 0;

$i = 0;

$rowspan = 1;
foreach(explode(',',$edata) as $data){
	if(in_array($data,$empstathistory))
	{
		$empstathistorycol += 1;
		$rowspan = 2;
	}
	if(in_array($data,$family))
	{
		$familycol += 1;
		$rowspan = 2;
	}
	if(in_array($data,$children))
	{
		$childrencol += 1;
		$rowspan = 2;
	}
	if(in_array($data,$taxDependents))
	{
		$taxDependentscol += 1;
		$rowspan = 2;
	}
	if(in_array($data,$father))
	{
		$fathercol += 1;
		$rowspan = 2;
	}
	if(in_array($data,$mother))
	{
		$mothercol += 1;
		$rowspan = 2;
	}
	if(in_array($data,$spouse))
	{
		$spousecol += 1;
		$rowspan = 2;
	}
	if(in_array($data,$immigrationDetails))
	{
		$immigrationDetailscol += 1;
		$rowspan = 2;
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
                .content{
                    height: 100%;
                    margin-top: 15px;
                }
                table{
                    border-collapse: collapse;
                    font-size: 12px;
                    border-spacing: 5px;
                }
                th{
                	color: yellow;
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
$totalcolumn = count(explode(',',$edata)) + 1;
$percentage = 95/$totalcolumn;
$info .= "

<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 12px;' id='datas'>
        	<thead>
            <tr style='background-color: black;'>
            <th align='center' rowspan='".$rowspan."' width='5%'>#</th>";
            foreach(explode(',',$edata) as $data){
				if(in_array($data,$empstathistory))
				{
					if($empstathistoryexist == 0)
					{
						$info .= "<th align='center' colspan='".$empstathistorycol."'>Employment Status History</th>";
					}
					$extracol .= "<th align='center' width='".$percentage."%'>".$this->reports->showdesc($data)."</th>";
					$empstathistoryexist += 1;
				}
				else if(in_array($data,$family))
				{
					if($familyexist == 0)
					{
						$info .= "<th align='center' colspan='".$familycol."'>Family Member</th>";
					}
					$extracol .= "<th align='center' width='".$percentage."%'>".$this->reports->showdesc($data)."</th>";
					$familyexist += 1;
				}
				else if(in_array($data,$children))
				{
					if($childrenexist == 0)
					{
						$info .= "<th align='center' colspan='".$childrencol."'>Number of Children</th>";
					}
					$extracol .= "<th align='center' width='".$percentage."%'>".$this->reports->showdesc($data)."</th>";
					$childrenexist += 1;
				}
				else if(in_array($data,$taxDependents))
				{
					if($taxDependentsexist == 0)
					{
						$info .= "<th align='center' colspan='".$taxDependentscol."'>Tax Dependents</th>";
					}
					$extracol .= "<th align='center' width='".$percentage."%'>".$this->reports->showdesc($data)."</th>";
					$taxDependentsexist += 1;
				}
				else if(in_array($data,$father))
				{
					if($fatherexist == 0)
					{
						$info .= "<th align='center' colspan='".$fathercol."'>Father</th>";
					}
					$extracol .= "<th align='center' width='".$percentage."%'>".$this->reports->showdesc($data)."</th>";
					$fatherexist += 1;
				}
				else if(in_array($data,$mother))
				{
					if($motherexist == 0)
					{
						$info .= "<th align='center' colspan='".$mothercol."'>Mother</th>";
					}
					$extracol .= "<th align='center' width='".$percentage."%'>".$this->reports->showdesc($data)."</th>";
					$motherexist += 1;
				}
				else if(in_array($data,$spouse))
				{
					if($spouseexist == 0)
					{
						$info .= "<th align='center' colspan='".$spousecol."'>Spouse</th>";
					}
					$extracol .= "<th align='center' width='".$percentage."%'>".$this->reports->showdesc($data)."</th>";
					$spouseexist += 1;
				}
				else if(in_array($data,$immigrationDetails))
				{
					if($immigrationDetailsexist == 0)
					{
						$info .= "<th align='center' colspan='".$immigrationDetailscol."' >Immigration Details</th>";
					}
					$extracol .= "<th align='center' width='".$percentage."%'>".$this->reports->showdesc($data)."</th>";
					$immigrationDetailsexist += 1;
				}
				else 
				{
					$info .= "
						<th align='center' rowspan='".$rowspan."' width='".$percentage."%'>".$this->reports->showdesc($data)."</th>";
						
				}

				$i += 1;
            }
			
if($extracol)
{
	$info .= "<tr style='background: black;'>";
	$info .= $extracol;
	$info .= "</tr>";
}
$info .= "
            </tr>
            </thead>";
            $empcount = 1;
                foreach($cdata as $emp){
$info .= "
            <tbody><tr>";
            $columncount = 1;

					$info .= "<td style='text-align:center;border:1px solid;'>".$empcount."</td>";

                foreach(explode(',',$edata) as $col){
                	$columncount++;
					if(in_array($col,$empstathistory))
					{
						$empstathistoryquery = $this->reports->empstathistoryquery($emp->employeeid,$col);
							$info .= "			<td style='text-align:center;border:1px solid;'>
												<table style='font-size: 12px;'>
									 ";
							foreach($empstathistoryquery as $r)
							{
								$info .= "
											<tr><td>".$r->$col."</td></tr>
									 ";
							}
							$info .= "
												</table>
											</td>
									 ";
					}
					else if(in_array($col,$children))
					{
						$childrenquery = $this->reports->childrenquery($emp->employeeid,$col);
						
						$info .= "
										<td style='text-align:center;border:1px solid;'>
											<table style='font-size: 12px;'>
								 ";
						foreach($childrenquery as $r)
						{
							$info .= "
										<tr><td>".$r->$col."</td></tr>
								 ";
						}
						$info .= "
											</table>
										</td>
								 ";
					}
					else if(in_array($col,$family))
					{
						$familyquery = $this->reports->familyquery($emp->employeeid,$col);
						
						$info .= "
										<td style='text-align:center;border:1px solid;'>
											<table style='font-size: 12px;'>
								 ";
						foreach($familyquery as $r)
						{
							$info .= "
										<tr><td>".$r->$col."</td></tr>
								 ";
						}
						$info .= "
											</table>
										</td>
								 ";
					}
					else if(in_array($col,$taxDependents))
					{
						$taxDependentsquery = $this->reports->taxDependentsquery($emp->employeeid,$col);
						
						$info .= "
										<td style='text-align:center;border:1px solid;'>
											<table style='font-size: 12px;'>
								 ";
						foreach($taxDependentsquery as $r)
						{
							$info .= "
										<tr><td>".$r->$col."</td></tr>
								 ";
						}
						$info .= "
											</table>
										</td>
								 ";
					}
					else
					{

	$info .= "
					<td style='text-align:center;border:1px solid;'>".( !in_array($emp->$col,array("0000-00-00","1970-01-01")) ? $emp->$col : "" )."</td>
			 ";
					
					}
                }
				
$info .= "  
            </tr> </tbody>    
         ";
					$empcount++;

                }
$empcount = $empcount - 1;
$columncount = $columncount - 1;
$info .="  <tr>
            <td style='font-size: 13px; color:  #000000; text-align: right; padding-right: 10px;'  colspan='".$columncount."'><b>Total:</b></td>
            <td style='font-size: 13px; color:  #000000; text-align: center; '><b>".$empcount."</b></td>
        </tr>";
$info .= "      
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
// echo "<pre>";print_r($info);die;
$pdf->WriteHTML($info);

$pdf->Output();
?>



