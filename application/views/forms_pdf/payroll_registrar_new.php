
<?php
// Kennedy

require_once(APPPATH."constants.php");

$pdf = new mpdf('LONG-L','LONG-L','','UTF-8',5,5,8,13);
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
                <td rowspan='4' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 70px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='50%'><span style='font-size: 12px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 10px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>PAYROLL SHEET FOR SALARY SCHEDULE </strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 11px;' width='55%'>".$sched_display."</span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>";
function showPerTableRow($arr_content){
	$info = '<tr>';

	foreach ($arr_content as $info) {
		list($caption, $style) = $info;

		$info .= '<td style="'. $style .'">'. $caption .'</td>';
	}

	
	$info .= '<tr>';
	return $info;
}

# ======================================================= detailed =======================================================
if($sd_filter == "detailed"){
	function showIncomeHeader($income_end_col, $inc_income, $inc_adjustment, $config, $deduction_end_col,$inc_fixed_deduc,$inc_deduction,$inc_loan,$config){
		
		$info = '
			<tr class="tr-header">
				<th colspan="'. $income_end_col .'" style="text-align: left;">INCOME AND DEDUCTION</th>
			</tr>
			<tr class="tr-header">
				<th>#</th>
				<th>EMPLOYEE ID</th>
				<th width="15%">EMPLOYEE NAME</th>
				<th>SALARY</th>
				<th>TARDY</th>
				<th>ABSENT</th>
				<th>BASIC PAY</th>
						';
		foreach ($inc_income['deminimissList'] as $key => $value) $info .= '<th>'. $config["income"][$key] .'</th>';

		$info .= '		
				<th>OTHER DEMINIMIS</th>';

		foreach ($inc_income['noDeminimissList'] as $key => $value) $info .= '<th>'. $config["income"][$key] .'</th>';

		$info .= '		
				<th>OTHER INCOME</th>';

	    foreach ($inc_adjustment as $key => $value) $info .= '<th>'. $config["income"][$key] .' ADJ</th>';

		$info .= '			
				<th>OTHER ADJUSTMENT</th>
				<th>OVERTIME</th>
				<th>GROSS PAY</th>
			</tr>
						';

		foreach ($inc_fixed_deduc as $key => $value) $info .= '<th>'. $key .'</th>';						
		foreach ($inc_deduction as $key => $value) $info .= '<th>'. $config["deduction"][$key] .'</th>';		

		foreach ($inc_loan as $key => $value) $info .= '<th>'. $config["loan"][$key] .'</th>';

		$info .= '	
				<th>OTHER DEDUCTION</th>
				<th>WITH HOLDING TAX</th>
				<th>TOTAL DEDUCTION</th>
				<th>NET</th>
			</tr>
						';

		return $info;
	}


	function showDecductionHeader($deduction_end_col,$inc_fixed_deduc,$inc_deduction,$inc_loan,$config){
		
		$info = '
			<tr class="tr-header">
				<th colspan="'. $deduction_end_col .'" style="text-align: left;">Deduction</th>
			</tr>
			<tr class="tr-header">
				<th>#</th>
				<th>EMPLOYEE ID</th>
				<th>EMPLOYEE NAME</th>
						';
		foreach ($inc_fixed_deduc as $key => $value) $info .= '<th>'. $key .'</th>';						
		foreach ($inc_deduction as $key => $value) $info .= '<th>'. $config["deduction"][$key] .'</th>';		

		foreach ($inc_loan as $key => $value) $info .= '<th>'. $config["loan"][$key] .'</th>';

		$info .= '	
				<th>OTHER DEDUCTION</th>
				<th>WITH HOLDING TAX</th>
				<th>TOTAL DEDUCTION</th>
				<th>NET</th>
			</tr>
						';
		return $info;
	}


	function pageBreakDeduction($deduction_header, $deduction_content, $income_header){
		$info .='
			<tbody>			 
		</table>
		<pagebreak>
		<table width="100%" border="1">
			<thead>
				'. $income_header .'
			</thead>
			<tbody>
		</pagebreak>
								';
		
		return $info;								
	}
	
	function showSortDescription($sort_desc, $income_end_col){
		$info = '
			<tr>
				<td style="font-weight: bold;" colspan="'. $income_end_col .'">'. $sort_desc .'</td>
			</tr>
		';

		return $info;
	}
	function showPerDeptCampusTotal($total_arr){
		$arr_content = array();
		$arr_content[] = array("Total : ", 'text-align: right;" colspan="3');

		foreach ($total_arr as $key => $value) $arr_content[] = array(number_format($value .' ',2), 'text-align:right; padding-right: 5px;');

		return showPerTableRow($arr_content);
	}

	function setPerDeptCampusTotal(){
		// hold muna may priority sa ngayon 11-23-2018 by justine.. 
	}

	$income_end_col = 5 + count($inc_income['deminimissList']) + count($inc_income['noDeminimissList']) + count($inc_adjustment) + 7 + count($inc_fixed_deduc) + count($inc_deduction) + count($inc_loan) + 4;
	$income_header = showIncomeHeader($income_end_col, $inc_income, $inc_adjustment, $config, $deduction_end_col,$inc_fixed_deduc,$inc_deduction,$inc_loan,$config);


	$deduction_content = '';

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



