<?php

/**
 * @author justin (with e)
 * @copyright 2018
 * 
 * > for hyperion 21454 
 */
require_once(APPPATH."constants.php");
$division  =  isset($division) ? $division : '';
$department  =  isset($department) ? $department : '';
$cutoff  =  isset($cutoff) ? $cutoff : '';
$deduction  =  isset($deduction) ? $deduction : '';
$sort  =  isset($sort) ? $sort : '';
$type = "";

$isRDCForm = ($isMRRReport) ? false : true;

$result = $this->reports->rdc($division,$department,$cutoff,$deduction, $isRDCForm, $sort);

if($deduction == "PAGIBIG") $type = "PAG-IBIG";
else $type = $deduction;


$pdf = new mpdf('P','LETTER','','UTF-8',5,5,8,8,9,2);
$info  = "  
<style>
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

.footer{
	width: 100%;
	text-align: right;
}
</style>
";
# > added by justin (with e) for 21451
$cutoff_name = '';
$arr_cutoff = array();

if($isRDCForm){
	$arr_cutoff = $this->employeemod->getCutoff($cutoff);            
	$exp_co = explode("~~", $cutoff);	
	$cutoff_name = $exp_co[0];
}else{
	$cutoff_name = $cutoff;
	#echo "<pre>". $cutoff;
	list($start_date, $end_date) = explode(",",$cutoff);
	$arr_cutoff[] = array(
		'start_date' => $start_date,
		'end_date' => $end_date
	);

	#$result = array();
}            


$infos= "
<htmlpageheader name='Header'>
    <div>
		<p>".date('m-d-y H:i')."</p>
        <table width='100%' style='padding: 0;'>
            <tr>
                <td rowspan='3' style='text-align: right;'><img src='".$imgurl."images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td valign='middle' width='300px' style='padding-right: 50px;text-align: center;'><span style='font-size: 13px;'><b>".$SCHOOL_NAME."</b></span></td>
                <td rowspan='3' style='text-align: right;'><img style='width: 60px;text-align: center;'/></td>
            </tr>
            <tr>
                <td valign='middle' style='padding-right: 50px;text-align: center'><span style='font-size: 10px;'>".$SCHOOL_CAPTION."</span></td>
            </tr>
        </table>
    </div>
	<div style='text-align:center;margin-top:5%'>
		<label>MONTHLY REMITTANCE RETURN OF INCOME TAX W/HELD (1601-C)</label><br>
		<label>For the month of ". $cutoff_name ."</label>
	</div>
</htmlpageheader>";

$info .= "
<htmlpageheader name='Header'>
    <div>
        <table width='45%'  >
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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px; font-family:times new roman;' width='55%'><strong>Monthly Remittance Return Of Income Tax W/Held (1601-C)</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px; font-family:times new roman;' width='55%'><strong>For the month of ". $cutoff_name ."</strong></span></td>
            </tr>
            
        </table>
    </div>
</htmlpageheader>";


$info .= "
<div class='content'>
    <div class='content-header'>
		
        <table width='100%' style='font-size: 9px;'>
            <tr style='background: #000000'>
				<th style='color:yellow; width:15%;'>EMPLOYEE ID</th>
				<th style='color:yellow; width:25%;'>EMPLOYEE NAME</th>
				<th style='color:yellow; width:15%;'>TIN NUMBER</th>
				<th style='color:yellow; width:15%;'>Taxable Amount</th>
				<th style='color:yellow; width:15%;'>Tax Withheld</th>
				
			</tr>"; # > modified by justin (with e) for ica-hyperion 21451

			

			$t_tax_amount = 0;
			$t_ee_amount = 0;
			$t_total_amount = 0;
			
			$i = 0;
			$colspan = 'colspan="5"';
			$ofc = $officeDesc = 'sometext';
			# > displayed total..
			foreach($result->result() as $row)
			{
				$ee_amount = 0;
				$total_per_emp = 0;
				$ee_amount = $row->withholdingtax;
				/*$fixeddeduc = $row->fixeddeduc;
				$exp_fd = explode("/", $fixeddeduc);
				foreach ($exp_fd as $value) {
					$exp_val = explode("=", $value);
					$ee_amount += $exp_val[1];
				}*/

				$tax_amount = $this->reports->getTaxableAmount($row->employeeid, $cutoff, true);
				$total_per_emp = $ee_amount + $tax_amount;
				if($sort=='department'){
                    if($ofc !== $row->office){
                        if($officeDesc != $this->extensions->getOfficeDescriptionReport($row->office)){
                            $info .="  <tr>
                                        <td style='color:  #000000; text-align: left;' ".$colspan."><b>".$this->extensions->getOfficeDescriptionReport($row->office)."</b></td>
                                    </tr>";
                        }
                    }
                }
                $officeDesc = $this->extensions->getOfficeDescriptionReport($row->office);
                $ofc =$row->office;
				if($ee_amount != 0){
					$info .= "
					<tr>
						<td style='text-align:center'>". $row->employeeid ."</td>
						<td style='text-align:left'>". $row->fullname ."</td>
						<td style='text-align:center'>". $row->emp_tin ."</td>
						<td style='text-align:right'>". number_format($tax_amount,2) ."&nbsp;</td>
						<td style='text-align:right'>". number_format($ee_amount,2) ."&nbsp;</td>
						
					</tr>";
				}
				

				$t_tax_amount += $tax_amount;
				$t_ee_amount += $ee_amount;
				$t_total_amount += $total_per_emp;
			}#die;
$info .= "  <tr>
				<td colspan='3' style='text-align:right'> <strong>Grand Total : </strong></td>
				<td style='text-align:right'><strong>". number_format($t_tax_amount,2) ."&nbsp;</strong></td>
				<td style='text-align:right'><strong>". number_format($t_ee_amount,2) ."&nbsp;</strong></td>
				
			</tr>";
$info .= "      
        </table>
    </div>
</div>";

$info .= '
	<htmlpagefooter name="Footer">
		<br>
		<div class="footer">
			Page : {PAGENO} of {nb}
		</div>
	</htmlpagefooter>
'; 

$pdf->WriteHTML($info);

$pdf->Output();
?>