<?php  
/**
* @author justin (with e)
* @copyright 2018
*/
// if($config['department']){
// 	if(isset($config['department']['ACAD'])){
// 		$popped_department = $config['department']['ACAD'];
// 		unset($config['department']['ACAD']);
// 		$config['department']['ACAD'] = $popped_department;
// 	}
// }

ini_set('max_execution_time', 0);
ini_set("memory_limit", "2G");


$this->load->library('PdfCreator_mpdf');
$this->load->library('lib_includer');
require_once(APPPATH."constants.php");
$pdf = new mpdf('LONG-L','LONG-L','','UTF-8',5,5,8,13);
$show_content  = '
	<style>
	    @page{            
	        /*margin-top: 4.35cm;*/
	        margin-top: 3.5cm;
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
	        font-size: 14px;
	    }
	    .content-body{
	        border: 1px solid black;
	        padding-top: 8px;
	        padding-bottom: 8px;
	        padding-left: 8px;
	    }
	    .tr-header{
	    	background: #000000;
	    }
	    th{
	    	color:yellow !important;
	    }
	    .center{
	    	text-align: center;
	    }
	    .right{
	    	text-align: right;
	    	padding-right: 5px;
	    }
	</style>
		';
		// echo "<pre>"; print_r($inc_income); die;
$show_contents = '
<htmlpageheader name="Header">
    <div>
    	<table width="100%" style="padding: 0;">
    		<tr>
    			<td rowspan="3" style="text-align: right;"><img src="'.$imgurl.'images/school_logo.jpg" style="width: 60px;text-align: center;" /></td>
    			<td valign="middle" width="400px" style="padding-right: 50px;text-align: center;"><span style="font-size: 20px;color:blue;"><b>'.$SCHOOL_NAME.'</b></span></td>
    			<td rowspan="3" style="text-align: right;"><img style="width: 60px;text-align: center;"/></td>
    		</tr>
    	</table>
    </div>
    <div style="text-align:center;font-size: 14px;color:blue;">
		<label>PAYROLL SHEET FOR SALARY SCHEDULE</label><br>
		<label>'. $sched_display .'</label>
	</div>
</htmlpageheader>
<htmlpagefooter name="Footer"> 
		<table width="100%">
                <tr >
                    <td width="50%" style="text-align: right; border-top: 1px solid #000"><strong>'. date("F d, Y") .' -</strong> Page  <strong>{PAGENO}</strong> of <strong>{nbpg}</strong></td>
                </tr>
        </table> </htmlpagefooter>
';

$show_content .= "
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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>PAYROLL SHEET FOR SALARY SCHEDULE</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>".$sched_display."</strong></span></td>
            </tr>
            
        </table>
    </div>
</htmlpageheader>
<htmlpagefooter name='Footer'> 
		<table width='100%'>
                <tr >
                    <td width='50%' style='text-align: right; border-top: 1px solid #000'><strong>". date("F d, Y") ." -</strong> Page  <strong>{PAGENO}</strong> of <strong>{nbpg}</strong></td>
                </tr>
        </table> </htmlpagefooter>";

function showPerTableRow($arr_content){
	$show_content = '<tr>';

	foreach ($arr_content as $info) {
		list($caption, $style) = $info;

		$show_content .= '<td style="'. $style .'">'. $caption .'</td>';
	}

	
	$show_content .= '<tr>';
	return $show_content;
}

# ======================================================= detailed =======================================================
if($sd_filter == "detailed"){
	function showIncomeHeader($income_end_col, $inc_income, $inc_adjustment, $config, $deduction_end_col,$inc_fixed_deduc,$inc_deduction,$inc_loan,$config){
		
		$show_content = '
			<tr class="tr-header">
				<th colspan="3" style="text-align: center;">&nbsp;</th>
				<th colspan="'. (9+(count($inc_income['deminimissList']))+(count($inc_income['noDeminimissList']))+(count($inc_adjustment))).'" style="text-align: center;">INCOME</th>
				<th colspan="'. (3+(count($inc_fixed_deduc))+(count($inc_deduction))+(count($inc_loan))).'" style="text-align: center;">DEDUCTION</th>
				<th  style="text-align: center;">&nbsp;</th>
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
		foreach ($inc_income['deminimissList'] as $key => $value) $show_content .= '<th>'. $config["income"][$key] .'</th>';

		$show_content .= '		
				<th>OTHER DEMINIMIS</th>';

		foreach ($inc_income['noDeminimissList'] as $key => $value) $show_content .= '<th>'. $config["income"][$key] .'</th>';

		$show_content .= '		
				<th>OTHER INCOME</th>';

	    foreach ($inc_adjustment as $key => $value) $show_content .= '<th>'. $config["income"][$key] .' ADJ</th>';

		$show_content .= '			
				<th>OTHER ADJUSTMENT</th>
				<th>OVERTIME</th>
				<th>GROSS PAY</th>

						';

		foreach ($inc_fixed_deduc as $key => $value) $show_content .= '<th>'. $key .'</th>';						
		foreach ($inc_deduction as $key => $value) $show_content .= '<th>'. $config["deduction"][$key] .'</th>';		

		foreach ($inc_loan as $key => $value) $show_content .= '<th>'. $config["loan"][$key] .'</th>';

		$show_content .= '	
				<th>OTHER DEDUCTION</th>
				<th>WITH HOLDING TAX</th>
				<th>TOTAL DEDUCTION</th>
				<th>NET</th>
			</tr>
						';

		return $show_content;
	}


	function showDecductionHeader($deduction_end_col,$inc_fixed_deduc,$inc_deduction,$inc_loan,$config){
		
		$show_content = '
			<tr class="tr-header">
				<th colspan="'. $deduction_end_col .'" style="text-align: center;">Deduction</th>
			</tr>
			<tr class="tr-header">
				<th>#</th>
				<th>EMPLOYEE ID</th>
				<th>EMPLOYEE NAME</th>
						';
		foreach ($inc_fixed_deduc as $key => $value) $show_content .= '<th>'. $key .'</th>';						
		foreach ($inc_deduction as $key => $value) $show_content .= '<th>'. $config["deduction"][$key] .'</th>';		

		foreach ($inc_loan as $key => $value) $show_content .= '<th>'. $config["loan"][$key] .'</th>';

		$show_content .= '	
				<th>OTHER DEDUCTION</th>
				<th>WITH HOLDING TAX</th>
				<th>TOTAL DEDUCTION</th>
				<th>NET</th>
			</tr>
						';
		return $show_content;
	}


	function pageBreakDeduction($deduction_header, $deduction_content, $income_header){
		$show_content .='
			<tbody>			 
		</table>
		<pagebreak>
		<table width="100%" border="1">
			<thead>
				'. $income_header .'
			</thead>
			<tbody>
		
								';
		
		return $show_content;								
	}
	
	function showSortDescription($sort_desc, $income_end_col){
		$show_content = '
			<tr>
				<td style="font-weight: bold;" colspan="'. $income_end_col .'">'. $sort_desc .'</td>
			</tr>
		';

		return $show_content;
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
	
// $show_content .= '
// <div class="content">
// 	<div class="content-header">
// 		<table width="100%" border="1">
// 			<thead>
// 				'. $income_header .'
// 			</thead>
// 			<tbody>
// 				 ';
	
// 	// ksort($emp_list);
// 	$count_emp = 0;
// 	$counter = 0;
// 	$old_campusid = '';
// 	$employee_count = 0;
// 	foreach ($teaching['emp_list'] as $sort_key => $employees) {
// 		if($sort == "department" && $sort_key == "ACAD"){

// 			if($sort_key != "name"){
// 				$show_content .= showSortDescription($config[$sort][$sort_key], $income_end_col);
				
// 				$deduction_content .= showSortDescription($config[$sort][$sort_key], $deduction_end_col);
// 				$count_emp += 1;
// 				if($count_emp == 37){
// 					$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

// 					$deduction_content = '';
// 					$count_emp = 0;

// 				}
// 			}

// 			$total_key = "";
// 			$total_arr = array();
// 			foreach ($employees as $idx => $info){
// 				if($sort == "department" && $sort_key == "ACAD"){
// 					if($info["campusid"] != $old_campusid){
				
// 						if($employee_count){
// 								// echo $summary[$sort_key][$old_campusid]['count']; echo $employee_count; die;
// 								$arr_content = array();
// 								$arr_content[] = array("Sub Total : ", 'font-weight: bold; text-align: right;" colspan="3');
// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['salary']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['tardy']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['absent']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['basic_pay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 								foreach ($inc_income['deminimissList'] as $key => $value) {
// 									$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 					            }
					        
// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['totalOtherDeminimissToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 								foreach ($inc_income['noDeminimissList'] as $key => $value) {
// 									$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 					            }

// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['totalOtherIncomeToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 								foreach ($inc_adjustment as $key => $value) {
// 									$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
// 					            }
// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['totalOtherAdjustmentToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['overtime']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['gross']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 								// deduction

// 								foreach ($inc_fixed_deduc as $key => $value) {
// 									$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold;text-align:right; padding-right: 5px;');                    
// 				                }

// 								foreach ($inc_deduction as $key => $value) {
// 									$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold;text-align:right; padding-right: 5px;');                      
// 				                }

// 								foreach ($inc_loan as $key => $value) {
// 									$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["loan_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold;text-align:right; padding-right: 5px;');                      
// 				                }

// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['totalOtherDeductionToDisplay']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');

// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['with_holding_tax']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');

// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['total_deduction']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');
// 								$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key][$old_campusid]['net']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');

// 								$show_content .= showPerTableRow($arr_content);
// 								$count_emp += 1;
// 								$employee_count=0;
// 							}

// 						// <<< display campus header, if sorting is selected on department
// 						$show_content .= showSortDescription(($info["campusid"]) ? $info["campusid"] : "No Campus", $income_end_col);
// 						$deduction_content .= showSortDescription(($info["campusid"]) ? $info["campusid"] : "No Campus", $deduction_end_col);

// 						$count_emp += 1;
// 						if($count_emp == 37){
// 							$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

// 							$deduction_content = '';
// 							$count_emp = 0;

// 						}
// 						// <<< end of displaying campus header, if sorting is selected on department

// 						$total_arr[$info["campusid"]]["income"] 	= array();
// 						$total_arr[$info["campusid"]]["deduction"] 	= array();
// 						$employee_count++;
// 					}
// 				}
				
// 				if($count_emp < 40){
// 					$counter += 1;
// 					// income..
// 					$arr_content = array();
// 					$arr_content[] = array(($counter), 'text-align:center;');
// 					$arr_content[] = array($info['employeeid'], 'text-align:center;');
// 					$arr_content[] = array($info['name'], 'text-align:left;');

// 					$arr_content[] = array(number_format($info['income']['salary'].' ',2), 'text-align:right; padding-right: 5px;');
// 					$arr_content[] = array(number_format($info['income']['tardy'].' ',2), 'text-align:right; padding-right: 5px;');
// 					$arr_content[] = array(number_format($info['income']['absent'].' ',2), 'text-align:right; padding-right: 5px;');
// 					$arr_content[] = array(number_format($info['income']['basic_pay'].' ',2), 'text-align:right; padding-right: 5px;');

// 					foreach ($inc_income['deminimissList'] as $key => $value) {
// 						$arr_content[] = array(((isset($info["income"]["income_list"][$key])) ? number_format($info["income"]["income_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
// 	                }

// 					$arr_content[] = array(number_format($info['income']['totalOtherDeminimissToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

// 					foreach ($inc_income['noDeminimissList'] as $key => $value) {
// 						$arr_content[] = array(((isset($info["income"]["income_list"][$key])) ? number_format($info["income"]["income_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
// 	                }

// 					$arr_content[] = array(number_format($info['income']['totalOtherIncomeToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

// 					foreach ($inc_adjustment as $key => $value) {
// 						$arr_content[] = array(((isset($info["income"]["adjustment_list"][$key])) ? number_format($info["income"]["adjustment_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
// 	                }
// 					$arr_content[] = array(number_format($info['income']['totalOtherAdjustmentToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

// 					$arr_content[] = array(number_format($info['income']['overtime'].' ',2), 'text-align:right; padding-right: 5px;');
// 					$arr_content[] = array(number_format($info['income']['gross'].' ',2), 'text-align:right; padding-right: 5px;');


// 					// deduction

// 					foreach ($inc_fixed_deduc as $key => $value) {
// 						$arr_content[] = array(((isset($info["deduction"]["fixed_deduc_list"][$key])) ? number_format($info["deduction"]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
// 	                }

// 					foreach ($inc_deduction as $key => $value) {
// 						$arr_content[] = array(((isset($info["deduction"]["deduc_list"][$key])) ? number_format($info["deduction"]["deduc_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
// 	                }

// 					foreach ($inc_loan as $key => $value) {
// 						$arr_content[] = array(((isset($info["deduction"]["loan_list"][$key])) ? number_format($info["deduction"]["loan_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
// 	                }

// 					$arr_content[] = array(number_format($info['deduction']['totalOtherDeductionToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

// 					$arr_content[] = array(number_format($info['deduction']['with_holding_tax'].' ',2), 'text-align:right; padding-right: 5px;');

// 					$arr_content[] = array(number_format($info['deduction']['total_deduction'].' ',2), 'text-align:right; padding-right: 5px;');
// 					$arr_content[] = array(number_format($info['deduction']['net'].' ',2), 'text-align:right; padding-right: 5px;');

// 					$show_content .= showPerTableRow($arr_content);

// 					$count_emp += 1;

// 				}
				
// 				if($count_emp == 37){
// 					$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

// 					$deduction_content = '';
// 					$count_emp = 0;

// 				}	
// 				$old_campusid = $info['campusid'];		
// 			}

// 			if($sort_key != "name"){
// 				// income..
// 				$arr_content = array();
// 				$arr_content[] = array("Sub Total : ", 'font-weight: bold; text-align: right;" colspan="3');
// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['salary'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['tardy'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['absent'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['basic_pay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				foreach ($inc_income['deminimissList'] as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 	            }
	        
// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['totalOtherDeminimissToDisplay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				foreach ($inc_income['noDeminimissList'] as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 	            }

// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['totalOtherIncomeToDisplay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 				foreach ($inc_adjustment as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
// 	            }
// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['totalOtherAdjustmentToDisplay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['overtime'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['gross'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				
// 				// deduction..

// 				foreach ($inc_fixed_deduc as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 	            }

// 				foreach ($inc_deduction as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 	            }

// 				foreach ($inc_loan as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key][$old_campusid]["loan_list"][$key])) ? number_format($teaching['summary'][$sort_key][$old_campusid]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
// 	            }
// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['totalOtherDeductionToDisplay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['with_holding_tax'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['total_deduction'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
	            
// 				$arr_content[] = array(number_format($teaching['summary'][$sort_key][$old_campusid]['net'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				$show_content .= showPerTableRow($arr_content);
				
				
// 				$count_emp += 1;
// 				if($count_emp == 37){
// 					$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

// 					$deduction_content = '';
// 					$count_emp = 0;

// 				}
// 			}
// 		}else if($sort == "department" && $sort_key != "ACAD"){
// 			$show_content .= showSortDescription($config['campus'][$sort_key], $income_end_col);
			
// 			$deduction_content .= showSortDescription($config[$sort][$sort_key], $deduction_end_col);
// 			$count_emp += 1;
// 			if($count_emp == 37){
// 				$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

// 				$deduction_content = '';
// 				$count_emp = 0;

// 			}

// 			$total_key = "";
// 			$total_arr = array();
// 			foreach ($employees as $idx => $info){
// 				if($count_emp < 40){
// 					$counter += 1;
// 					// income..
// 					$arr_content = array();
// 					$arr_content[] = array(($counter), 'text-align:center;');
// 					$arr_content[] = array($info['employeeid'], 'text-align:center;');
// 					$arr_content[] = array($info['name'], 'text-align:left;');

// 					$arr_content[] = array(number_format($info['income']['salary'].' ',2), 'text-align:right; padding-right: 5px;');
// 					$arr_content[] = array(number_format($info['income']['tardy'].' ',2), 'text-align:right; padding-right: 5px;');
// 					$arr_content[] = array(number_format($info['income']['absent'].' ',2), 'text-align:right; padding-right: 5px;');
// 					$arr_content[] = array(number_format($info['income']['basic_pay'].' ',2), 'text-align:right; padding-right: 5px;');

// 					foreach ($inc_income['deminimissList'] as $key => $value) {
// 						$arr_content[] = array(((isset($info["income"]["income_list"][$key])) ? number_format($info["income"]["income_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
// 	                }

// 					$arr_content[] = array(number_format($info['income']['totalOtherDeminimissToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

// 					foreach ($inc_income['noDeminimissList'] as $key => $value) {
// 						$arr_content[] = array(((isset($info["income"]["income_list"][$key])) ? number_format($info["income"]["income_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
// 	                }

// 					$arr_content[] = array(number_format($info['income']['totalOtherIncomeToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

// 					foreach ($inc_adjustment as $key => $value) {
// 						$arr_content[] = array(((isset($info["income"]["adjustment_list"][$key])) ? number_format($info["income"]["adjustment_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
// 	                }
// 					$arr_content[] = array(number_format($info['income']['totalOtherAdjustmentToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

// 					$arr_content[] = array(number_format($info['income']['overtime'].' ',2), 'text-align:right; padding-right: 5px;');
// 					$arr_content[] = array(number_format($info['income']['gross'].' ',2), 'text-align:right; padding-right: 5px;');


// 					// deduction

// 					foreach ($inc_fixed_deduc as $key => $value) {
// 						$arr_content[] = array(((isset($info["deduction"]["fixed_deduc_list"][$key])) ? number_format($info["deduction"]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
// 	                }

// 					foreach ($inc_deduction as $key => $value) {
// 						$arr_content[] = array(((isset($info["deduction"]["deduc_list"][$key])) ? number_format($info["deduction"]["deduc_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
// 	                }

// 					foreach ($inc_loan as $key => $value) {
// 						$arr_content[] = array(((isset($info["deduction"]["loan_list"][$key])) ? number_format($info["deduction"]["loan_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
// 	                }

// 					$arr_content[] = array(number_format($info['deduction']['totalOtherDeductionToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

// 					$arr_content[] = array(number_format($info['deduction']['with_holding_tax'].' ',2), 'text-align:right; padding-right: 5px;');

// 					$arr_content[] = array(number_format($info['deduction']['total_deduction'].' ',2), 'text-align:right; padding-right: 5px;');
// 					$arr_content[] = array(number_format($info['deduction']['net'].' ',2), 'text-align:right; padding-right: 5px;');

// 					$show_content .= showPerTableRow($arr_content);

// 					$count_emp += 1;

// 				}
				
// 				if($count_emp == 37){
// 					$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

// 					$deduction_content = '';
// 					$count_emp = 0;

// 				}	
// 				$old_campusid = $info['deptid'];		
// 			}

// 			if($sort_key != "name"){
// 				// income..
// 				$arr_content = array();
// 				$arr_content[] = array("Total : ", 'font-weight: bold; text-align: right;" colspan="3');
// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['income']['salary']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['income']['tardy']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['income']['absent']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['income']['basic_pay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				foreach ($inc_income['deminimissList'] as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key]["income"]["income_list"][$key])) ? number_format($teaching['summary'][$sort_key]["income"]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 	            }
	        
// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['income']['totalOtherDeminimissToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				foreach ($inc_income['noDeminimissList'] as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key]["income"]["income_list"][$key])) ? number_format($teaching['summary'][$sort_key]["income"]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 	            }

// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['income']['totalOtherIncomeToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 				foreach ($inc_adjustment as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key]["income"]["adjustment_list"][$key])) ? number_format($teaching['summary'][$sort_key]["income"]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
// 	            }
// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['income']['totalOtherAdjustmentToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['income']['overtime']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['income']['gross']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				
// 				// deduction..

// 				foreach ($inc_fixed_deduc as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key]["deduction"]["fixed_deduc_list"][$key])) ? number_format($teaching['summary'][$sort_key]["deduction"]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 	            }

// 				foreach ($inc_deduction as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key]["deduction"]["deduc_list"][$key])) ? number_format($teaching['summary'][$sort_key]["deduction"]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 	            }

// 				foreach ($inc_loan as $key => $value) {
// 					$arr_content[] = array(((isset($teaching['summary'][$sort_key]["deduction"]["loan_list"][$key])) ? number_format($teaching['summary'][$sort_key]["deduction"]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
// 	            }
// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['deduction']['totalOtherDeductionToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['deduction']['with_holding_tax']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['deduction']['total_deduction']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
	            
// 				$arr_content[] = array(number_format(floatval($teaching['summary'][$sort_key]['deduction']['net']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 				$show_content .= showPerTableRow($arr_content);
				
				
// 				$count_emp += 1;
// 				if($count_emp == 37){
// 					$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

// 					$deduction_content = '';
// 					$count_emp = 0;

// 				}
// 			}
// 		}
// 	}

// 	$arr_content = array();
// 	$arr_content[] = array("Grand Total : ", 'font-weight: bold; text-align: right;" colspan="3');
// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['income']['salary']) ? $teaching['grand_total']['income']['salary'] : '0') .' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['income']['tardy']) ? $teaching['grand_total']['income']['tardy'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['income']['absent']) ? $teaching['grand_total']['income']['absent'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['income']['basic_pay']) ? $teaching['grand_total']['income']['basic_pay']: '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 	foreach ($inc_income['deminimissList'] as $key => $value) {
// 		$arr_content[] = array(((isset($teaching['grand_total']["income"]["income_list"][$key])) ? number_format($teaching['grand_total']["income"]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
//     }

    
// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['income']['totalOtherDeminimissToDisplay']) ? $teaching['grand_total']['income']['totalOtherDeminimissToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 	foreach ($inc_income['noDeminimissList'] as $key => $value) {
// 		$arr_content[] = array(((isset($teaching['grand_total']["income"]["income_list"][$key])) ? number_format($teaching['grand_total']["income"]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
//     }

// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['income']['totalOtherIncomeToDisplay']) ? $teaching['grand_total']['income']['totalOtherIncomeToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 	foreach ($inc_adjustment as $key => $value) {
// 		$arr_content[] = array(((isset($teaching['grand_total']["income"]["adjustment_list"][$key])) ? number_format($teaching['grand_total']["income"]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
//     }
// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['income']['totalOtherAdjustmentToDisplay']) ? $teaching['grand_total']['income']['totalOtherAdjustmentToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['income']['overtime']) ? $teaching['grand_total']['income']['overtime']: '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['income']['gross']) ? $teaching['grand_total']['income']['gross'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');					

// 	// deduction..

// 	foreach ($inc_fixed_deduc as $key => $value) {
// 		$arr_content[] = array(((isset($teaching['grand_total']["deduction"]["fixed_deduc_list"][$key])) ? number_format($teaching['grand_total']["deduction"]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 	}

// 	foreach ($inc_deduction as $key => $value) {
// 		$arr_content[] = array(((isset($teaching['grand_total']["deduction"]["deduc_list"][$key])) ? number_format($teaching['grand_total']["deduction"]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
// 	}

// 	foreach ($inc_loan as $key => $value) {
// 		$arr_content[] = array(((isset($teaching['grand_total']["deduction"]["loan_list"][$key])) ? number_format($teaching['grand_total']["deduction"]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
// 	}

// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['deduction']['totalOtherDeductionToDisplay']) ? $teaching['grand_total']['deduction']['totalOtherDeductionToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['deduction']['with_holding_tax']) ? $teaching['grand_total']['deduction']['with_holding_tax']: '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['deduction']['total_deduction']) ? $teaching['grand_total']['deduction']['total_deduction'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 	$arr_content[] = array(number_format((isset($teaching['grand_total']['deduction']['net']) ? $teaching['grand_total']['deduction']['net'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

// 	$show_content .= showPerTableRow($arr_content);

// $show_content .= '
// 			<tbody>			 
// 		</table>
// 	</div>
// </div>';
$show_content .= '
<div class="content">
	<div class="content-header">
		<table width="100%" border="1">
			<thead>
				'. $income_header .'
			</thead>
			<tbody>
				 ';
	
	// ksort($emp_list);
	$count_emp = 0;
	$counter = 0;
	$old_campusid = '';
	$employee_count = 0;
	$sort = ($sort != 'name' ? 'department' : $sort);
	if($sort == 'name'){
		$nonteaching['emp_list'] = $emp_list;
	}
	// $show_content .="<pagebreak></pagebreak>";
	foreach ($nonteaching['emp_list'] as $sort_key => $employees) {
		if($sort == "department" && $sort_key == 'ACAD'){

			if($sort_key != "name"){
				$show_content .= showSortDescription($config[$sort][$sort_key], $income_end_col);
				
				$deduction_content .= showSortDescription($config[$sort][$sort_key], $deduction_end_col);
				$count_emp += 1;
				// if($count_emp == 32){
				// 	$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

				// 	$deduction_content = '';
				// 	$count_emp = 0;

				// }
			}

			$total_key = "";
			$total_arr = array();
			foreach ($employees as $idx => $info){
				if($sort == "department" && $sort_key == "ACAD"){
					if($info["campusid"] != $old_campusid){
				
						if($employee_count){
								// echo $summary[$sort_key][$old_campusid]['count']; echo $employee_count; die;
								$arr_content = array();
								$arr_content[] = array("Sub Total : ", 'font-weight: bold; text-align: right;" colspan="3');
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['salary']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['tardy']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['absent']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['basic_pay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

								foreach ($inc_income['deminimissList'] as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
					            }
					        
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherDeminimissToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

								foreach ($inc_income['noDeminimissList'] as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
					            }

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherIncomeToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
								foreach ($inc_adjustment as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
					            }
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherAdjustmentToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['overtime']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['gross']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

								// deduction

								foreach ($inc_fixed_deduc as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold;text-align:right; padding-right: 5px;');                    
				                }

								foreach ($inc_deduction as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold;text-align:right; padding-right: 5px;');                      
				                }

								foreach ($inc_loan as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["loan_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold;text-align:right; padding-right: 5px;');                      
				                }

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherDeductionToDisplay']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['with_holding_tax']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['total_deduction']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['net']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');

								$show_content .= showPerTableRow($arr_content);
								$count_emp += 1;
								$employee_count=0;
							}

						// <<< display campus header, if sorting is selected on department
						$show_content .= showSortDescription(($info["campusid"]) ? $info["campusid"] : "No Campus", $income_end_col);
						$deduction_content .= showSortDescription(($info["campusid"]) ? $info["campusid"] : "No Campus", $deduction_end_col);

						// $count_emp += 1;
						// if($count_emp == 32){
						// 	$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

						// 	$deduction_content = '';
						// 	$count_emp = 0;

						// }
						// <<< end of displaying campus header, if sorting is selected on department

						$total_arr[$info["campusid"]]["income"] 	= array();
						$total_arr[$info["campusid"]]["deduction"] 	= array();
						$employee_count++;
					}
				}
				$count_emp = 0;
				if($count_emp < 33){
					$counter += 1;
					// income..
					$arr_content = array();
					$arr_content[] = array(($counter), 'text-align:center;');
					$arr_content[] = array($info['employeeid'], 'text-align:center;');
					$arr_content[] = array($info['name'], 'text-align:left;');

					$arr_content[] = array(number_format($info['income']['salary'].' ',2), 'text-align:right; padding-right: 5px;');
					$arr_content[] = array(number_format($info['income']['tardy'].' ',2), 'text-align:right; padding-right: 5px;');
					$arr_content[] = array(number_format($info['income']['absent'].' ',2), 'text-align:right; padding-right: 5px;');
					$arr_content[] = array(number_format($info['income']['basic_pay'].' ',2), 'text-align:right; padding-right: 5px;');

					foreach ($inc_income['deminimissList'] as $key => $value) {
						$arr_content[] = array(((isset($info["income"]["income_list"][$key])) ? number_format($info["income"]["income_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
	                }

					$arr_content[] = array(number_format($info['income']['totalOtherDeminimissToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

					foreach ($inc_income['noDeminimissList'] as $key => $value) {
						$arr_content[] = array(((isset($info["income"]["income_list"][$key])) ? number_format($info["income"]["income_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
	                }

					$arr_content[] = array(number_format($info['income']['totalOtherIncomeToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

					foreach ($inc_adjustment as $key => $value) {
						$arr_content[] = array(((isset($info["income"]["adjustment_list"][$key])) ? number_format($info["income"]["adjustment_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
	                }
					$arr_content[] = array(number_format($info['income']['totalOtherAdjustmentToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

					$arr_content[] = array(number_format($info['income']['overtime'].' ',2), 'text-align:right; padding-right: 5px;');
					$arr_content[] = array(number_format($info['income']['gross'].' ',2), 'text-align:right; padding-right: 5px;');


					// deduction

					foreach ($inc_fixed_deduc as $key => $value) {
						$arr_content[] = array(((isset($info["deduction"]["fixed_deduc_list"][$key])) ? number_format($info["deduction"]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
	                }

					foreach ($inc_deduction as $key => $value) {
						$arr_content[] = array(((isset($info["deduction"]["deduc_list"][$key])) ? number_format($info["deduction"]["deduc_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
	                }

					foreach ($inc_loan as $key => $value) {
						$arr_content[] = array(((isset($info["deduction"]["loan_list"][$key])) ? number_format($info["deduction"]["loan_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
	                }

					$arr_content[] = array(number_format($info['deduction']['totalOtherDeductionToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

					$arr_content[] = array(number_format($info['deduction']['with_holding_tax'].' ',2), 'text-align:right; padding-right: 5px;');

					$arr_content[] = array(number_format($info['deduction']['total_deduction'].' ',2), 'text-align:right; padding-right: 5px;');
					$arr_content[] = array(number_format($info['deduction']['net'].' ',2), 'text-align:right; padding-right: 5px;');

					$show_content .= showPerTableRow($arr_content);

					$count_emp += 1;

				}
				
				// if($count_emp == 32){
				// 	$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

				// 	$deduction_content = '';
				// 	$count_emp = 0;

				// }	
				$old_campusid = $info['campusid'];		
			}

			if($sort_key != "name"){
				// income..
				$arr_content = array();
				$arr_content[] = array("Sub Total : ", 'font-weight: bold; text-align: right;" colspan="3');
				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['salary'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['tardy'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['absent'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['basic_pay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				foreach ($inc_income['deminimissList'] as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }
	        
				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherDeminimissToDisplay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				foreach ($inc_income['noDeminimissList'] as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }

				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherIncomeToDisplay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				foreach ($inc_adjustment as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
	            }
				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherAdjustmentToDisplay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['overtime'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['gross'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				
				// deduction..

				foreach ($inc_fixed_deduc as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }

				foreach ($inc_deduction as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }

				foreach ($inc_loan as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["loan_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
	            }
				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherDeductionToDisplay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['with_holding_tax'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['total_deduction'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
	            
				$arr_content[] = array(number_format($nonteaching['summary'][$sort_key][$old_campusid]['net'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$show_content .= showPerTableRow($arr_content);
				
				
				$count_emp += 1;
				// if($count_emp == 32){
				// 	$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

				// 	$deduction_content = '';
				// 	$count_emp = 0;

				// }
			}
		}else if($sort_key != "ACAD"){
			if($sort == 'department') $show_content .= showSortDescription($config['campus'][$sort_key], $income_end_col);
			$deduction_content .= showSortDescription($config[$sort][$sort_key], $deduction_end_col);
			$count_emp += 1;
			// if($count_emp == 32){
			// 	$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

			// 	$deduction_content = '';
			// 	$count_emp = 0;

			// }

			$total_key = "";
			$total_arr = array();
			foreach ($employees as $idx => $info){
				if($sort == "department" && $sort_key != "ACAD"){					
					if($info["deptid"] != $old_campusid){
					
						if($employee_count){
								// echo $nonteaching['summary'][$sort_key][$old_campusid]['count']; echo $employee_count; die;
								$arr_content = array();
								$arr_content[] = array("Sub Total : ", 'font-weight: bold; text-align: right;" colspan="3');
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['salary']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['tardy']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['absent']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['basic_pay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

								foreach ($inc_income['deminimissList'] as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
					            }
					        
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherDeminimissToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

								foreach ($inc_income['noDeminimissList'] as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
					            }

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherIncomeToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
								foreach ($inc_adjustment as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
					            }
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherAdjustmentToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['overtime']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['gross']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

								// deduction

								foreach ($inc_fixed_deduc as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold;text-align:right; padding-right: 5px;');                    
				                }

								foreach ($inc_deduction as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold;text-align:right; padding-right: 5px;');                      
				                }

								foreach ($inc_loan as $key => $value) {
									$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["loan_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold;text-align:right; padding-right: 5px;');                      
				                }

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherDeductionToDisplay']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['with_holding_tax']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');

								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['total_deduction']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');
								$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['net']).' ',2), 'font-weight: bold;text-align:right; padding-right: 5px;');

								$show_content .= showPerTableRow($arr_content);
								$employee_count=0;
							}

						// <<< display campus header, if sorting is selected on department
						$dept_name = $this->extensions->getDepartmentDescription($info["deptid"]);
						$show_content .= showSortDescription(($info["deptid"]) ? $dept_name : "No Department", $income_end_col);
						$deduction_content .= showSortDescription(($info["deptid"]) ? $dept_name : "No Department", $deduction_end_col);

						$count_emp += 1;
						// if($count_emp == 32){
						// 	$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

						// 	$deduction_content = '';
						// 	$count_emp = 0;

						// }
						// <<< end of displaying campus header, if sorting is selected on department

						$total_arr[$info["deptid"]]["income"] 	= array();
						$total_arr[$info["deptid"]]["deduction"] 	= array();
						$employee_count++;
					}
				}
				$count_emp = 1;
				if($count_emp < 33){
					$counter += 1;
					// income..
					$arr_content = array();
					$arr_content[] = array(($counter), 'text-align:center;');
					$arr_content[] = array($info['employeeid'], 'text-align:center;');
					$arr_content[] = array($info['name'], 'text-align:left;');

					$arr_content[] = array(number_format($info['income']['salary'].' ',2), 'text-align:right; padding-right: 5px;');
					$arr_content[] = array(number_format($info['income']['tardy'].' ',2), 'text-align:right; padding-right: 5px;');
					$arr_content[] = array(number_format($info['income']['absent'].' ',2), 'text-align:right; padding-right: 5px;');
					$arr_content[] = array(number_format($info['income']['basic_pay'].' ',2), 'text-align:right; padding-right: 5px;');

					foreach ($inc_income['deminimissList'] as $key => $value) {
						$arr_content[] = array(((isset($info["income"]["income_list"][$key])) ? number_format($info["income"]["income_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
	                }

					$arr_content[] = array(number_format($info['income']['totalOtherDeminimissToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

					foreach ($inc_income['noDeminimissList'] as $key => $value) {
						$arr_content[] = array(((isset($info["income"]["income_list"][$key])) ? number_format($info["income"]["income_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
	                }

					$arr_content[] = array(number_format($info['income']['totalOtherIncomeToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

					foreach ($inc_adjustment as $key => $value) {
						$arr_content[] = array(((isset($info["income"]["adjustment_list"][$key])) ? number_format($info["income"]["adjustment_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
	                }
					$arr_content[] = array(number_format($info['income']['totalOtherAdjustmentToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

					$arr_content[] = array(number_format($info['income']['overtime'].' ',2), 'text-align:right; padding-right: 5px;');
					$arr_content[] = array(number_format($info['income']['gross'].' ',2), 'text-align:right; padding-right: 5px;');


					// deduction

					foreach ($inc_fixed_deduc as $key => $value) {
						$arr_content[] = array(((isset($info["deduction"]["fixed_deduc_list"][$key])) ? number_format($info["deduction"]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
	                }

					foreach ($inc_deduction as $key => $value) {
						$arr_content[] = array(((isset($info["deduction"]["deduc_list"][$key])) ? number_format($info["deduction"]["deduc_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
	                }

					foreach ($inc_loan as $key => $value) {
						$arr_content[] = array(((isset($info["deduction"]["loan_list"][$key])) ? number_format($info["deduction"]["loan_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
	                }

					$arr_content[] = array(number_format($info['deduction']['totalOtherDeductionToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

					$arr_content[] = array(number_format($info['deduction']['with_holding_tax'].' ',2), 'text-align:right; padding-right: 5px;');

					$arr_content[] = array(number_format($info['deduction']['total_deduction'].' ',2), 'text-align:right; padding-right: 5px;');
					$arr_content[] = array(number_format($info['deduction']['net'].' ',2), 'text-align:right; padding-right: 5px;');

					$show_content .= showPerTableRow($arr_content);

					$count_emp += 1;

				}
				
				// if($count_emp == 32){
				// 	$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

				// 	$deduction_content = '';
				// 	$count_emp = 0;

				// }	
				$old_campusid = $info['deptid'];		
			}

			if($sort != "name"){
				// income..
				$arr_content = array();
				$arr_content[] = array("Sub Total : ", 'font-weight: bold; text-align: right;" colspan="3');
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['salary']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['tardy']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['absent']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['basic_pay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				foreach ($inc_income['deminimissList'] as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }
	        
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherDeminimissToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				foreach ($inc_income['noDeminimissList'] as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }

				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherIncomeToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				foreach ($inc_adjustment as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
	            }
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherAdjustmentToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['overtime']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['gross']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				
				// deduction..

				foreach ($inc_fixed_deduc as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }

				foreach ($inc_deduction as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }

				foreach ($inc_loan as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$old_campusid]["loan_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$old_campusid]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
	            }
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['totalOtherDeductionToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['with_holding_tax']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['total_deduction']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
	            
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$old_campusid]['net']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$show_content .= showPerTableRow($arr_content);
				
				
				$count_emp += 1;
				// if($count_emp == 32){
				// 	$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

				// 	$deduction_content = '';
				// 	$count_emp = 0;

				// }
				$employee_count=0;
			}
			// echo "<pre>"; print_r($sort_key); die;
			if($sort != "name"){
				// income..
				$arr_content = array();
				$arr_content[] = array("Total : ", 'font-weight: bold; text-align: right;" colspan="3');
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['salary']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['tardy']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['absent']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['basic_pay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				foreach ($inc_income['deminimissList'] as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$info['campusid']]["income_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$info['campusid']]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }
	        
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['totalOtherDeminimissToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				foreach ($inc_income['noDeminimissList'] as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$info['campusid']]["income_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$info['campusid']]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }

				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['totalOtherIncomeToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				foreach ($inc_adjustment as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$info['campusid']]["adjustment_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$info['campusid']]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
	            }
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['totalOtherAdjustmentToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['overtime']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['gross']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
				
				// deduction..

				foreach ($inc_fixed_deduc as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$info['campusid']]["fixed_deduc_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$info['campusid']]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }

				foreach ($inc_deduction as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$info['campusid']]["deduc_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$info['campusid']]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	            }

				foreach ($inc_loan as $key => $value) {
					$arr_content[] = array(((isset($nonteaching['summary'][$sort_key][$info['campusid']]["loan_list"][$key])) ? number_format($nonteaching['summary'][$sort_key][$info['campusid']]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
	            }
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['totalOtherDeductionToDisplay']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['with_holding_tax']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['total_deduction']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
	            
				$arr_content[] = array(number_format(floatval($nonteaching['summary'][$sort_key][$info['campusid']]['net']).' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

				$show_content .= showPerTableRow($arr_content);
				
				
				$count_emp += 1;
				// if($count_emp == 32){
				// 	$show_content .= pageBreakDeduction($deduction_header, $deduction_content, $income_header);

				// 	$deduction_content = '';
				// 	$count_emp = 0;

				// }
				$employee_count=0;

			}

		}
	}
	// echo "<pre>"; print_r($nonteaching['grand_total']); die;
	if($sort == 'department'){
		$arr_content = array();
		$arr_content[] = array("Grand Total : ", 'font-weight: bold; text-align: right;" colspan="3');
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['income']['salary']) ? $nonteaching['grand_total']['nonteaching']['income']['salary'] : '0') .' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['income']['tardy']) ? $nonteaching['grand_total']['nonteaching']['income']['tardy'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['income']['absent']) ? $nonteaching['grand_total']['nonteaching']['income']['absent'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['income']['basic_pay']) ? $nonteaching['grand_total']['nonteaching']['income']['basic_pay']: '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		foreach ($inc_income['deminimissList'] as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']['nonteaching']["income"]["income_list"][$key])) ? number_format($nonteaching['grand_total']['nonteaching']["income"]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	    }

	    
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['income']['totalOtherDeminimissToDisplay']) ? $nonteaching['grand_total']['nonteaching']['income']['totalOtherDeminimissToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		foreach ($inc_income['noDeminimissList'] as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']['nonteaching']["income"]["income_list"][$key])) ? number_format($nonteaching['grand_total']['nonteaching']["income"]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	    }

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['income']['totalOtherIncomeToDisplay']) ? $nonteaching['grand_total']['nonteaching']['income']['totalOtherIncomeToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
		foreach ($inc_adjustment as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']['nonteaching']["income"]["adjustment_list"][$key])) ? number_format($nonteaching['grand_total']['nonteaching']["income"]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
	    }
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['income']['totalOtherAdjustmentToDisplay']) ? $nonteaching['grand_total']['nonteaching']['income']['totalOtherAdjustmentToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['income']['overtime']) ? $nonteaching['grand_total']['nonteaching']['income']['overtime']: '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['income']['gross']) ? $nonteaching['grand_total']['nonteaching']['income']['gross'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');					

		// deduction..

		foreach ($inc_fixed_deduc as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']['nonteaching']["deduction"]["fixed_deduc_list"][$key])) ? number_format($nonteaching['grand_total']['nonteaching']["deduction"]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
		}

		foreach ($inc_deduction as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']['nonteaching']["deduction"]["deduc_list"][$key])) ? number_format($nonteaching['grand_total']['nonteaching']["deduction"]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
		}

		foreach ($inc_loan as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']['nonteaching']["deduction"]["loan_list"][$key])) ? number_format($nonteaching['grand_total']['nonteaching']["deduction"]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
		}

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['deduction']['totalOtherDeductionToDisplay']) ? $nonteaching['grand_total']['nonteaching']['deduction']['totalOtherDeductionToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['deduction']['with_holding_tax']) ? $nonteaching['grand_total']['nonteaching']['deduction']['with_holding_tax']: '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['deduction']['total_deduction']) ? $nonteaching['grand_total']['nonteaching']['deduction']['total_deduction'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['nonteaching']['deduction']['net']) ? $nonteaching['grand_total']['nonteaching']['deduction']['net'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');



		$show_content .= showPerTableRow($arr_content);	


		// income..
		$arr_content = array();
		$arr_content[] = array("Payroll Register Total : ", 'font-weight: bold; text-align: right;" colspan="3');
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['income']['salary']) ? $nonteaching['grand_total']['income']['salary'] : '0') .' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['income']['tardy']) ? $nonteaching['grand_total']['income']['tardy'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['income']['absent']) ? $nonteaching['grand_total']['income']['absent'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['income']['basic_pay']) ? $nonteaching['grand_total']['income']['basic_pay']: '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		foreach ($inc_income['deminimissList'] as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']["income"]["income_list"][$key])) ? number_format($nonteaching['grand_total']["income"]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	    }

	    
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['income']['totalOtherDeminimissToDisplay']) ? $nonteaching['grand_total']['income']['totalOtherDeminimissToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		foreach ($inc_income['noDeminimissList'] as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']["income"]["income_list"][$key])) ? number_format($nonteaching['grand_total']["income"]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	    }

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['income']['totalOtherIncomeToDisplay']) ? $nonteaching['grand_total']['income']['totalOtherIncomeToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
		foreach ($inc_adjustment as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']["income"]["adjustment_list"][$key])) ? number_format($nonteaching['grand_total']["income"]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
	    }
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['income']['totalOtherAdjustmentToDisplay']) ? $nonteaching['grand_total']['income']['totalOtherAdjustmentToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['income']['overtime']) ? $nonteaching['grand_total']['income']['overtime']: '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['income']['gross']) ? $nonteaching['grand_total']['income']['gross'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');					

		// deduction..

		foreach ($inc_fixed_deduc as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']["deduction"]["fixed_deduc_list"][$key])) ? number_format($nonteaching['grand_total']["deduction"]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
		}

		foreach ($inc_deduction as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']["deduction"]["deduc_list"][$key])) ? number_format($nonteaching['grand_total']["deduction"]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
		}

		foreach ($inc_loan as $key => $value) {
			$arr_content[] = array(((isset($nonteaching['grand_total']["deduction"]["loan_list"][$key])) ? number_format($nonteaching['grand_total']["deduction"]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
		}

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['deduction']['totalOtherDeductionToDisplay']) ? $nonteaching['grand_total']['deduction']['totalOtherDeductionToDisplay'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['deduction']['with_holding_tax']) ? $nonteaching['grand_total']['deduction']['with_holding_tax']: '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['deduction']['total_deduction']) ? $nonteaching['grand_total']['deduction']['total_deduction'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

		$arr_content[] = array(number_format((isset($nonteaching['grand_total']['deduction']['net']) ? $nonteaching['grand_total']['deduction']['net'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');



		$show_content .= showPerTableRow($arr_content);	
	}
		

$show_content .= '
			<tbody>			 
		</table>
	</div>
</div>';
// echo "<pre>"; print_r($show_content); die;
}else{

# ======================================================= summary =======================================================
$show_content .= '
<div class="content">
	<div class="content-header">
		<table width="100%" border="1">
			<thead>
				 ';

	$income_end_col = 5 + count($inc_income) + count($inc_adjustment) + 4;				 
$show_content .= '
			<tr class="tr-header">
				<th colspan="'. $income_end_col .'" style="text-align: left;">INCOME</th>
			</tr>
			<tr class="tr-header">
				<th>#</th>
				<th>'. (($sort == "name") ? "EMPLOYEE" : strtoupper($sort)) .'</th>
				<th>SALARY</th>
				<th>TARDY</th>
				<th>ABSENT</th>
				<th>BASIC PAY</th>
						';


$show_content .= '		
				<th>OTHER DEMINIMISS</th>
				<th>OTHER INCOME</th>';

	    foreach ($inc_adjustment as $key => $value) $show_content .= '<th>'. $config["income"][$key] .' ADJ</th>';
	    
$show_content .= '		

				<th>OTHER ADJUSTMENT</th>
				<th>OVERTIME</th>
				<th>GROSS PAY</th>
			</tr>
			</thead>
			<tbody>
				 ';
	$idx = 1;
	ksort($summary);
	foreach ($summary as $sort_key => $info) {
		$arr_content = array();
		$arr_content[] = array($idx, 'text-align: right;');
		$arr_content[] = array($config[$sort][$sort_key], 'text-align: left;" width="100px; padding-left:5px;');
		$arr_content[] = array(number_format($info['income']['salary'].' ',2), 'text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format($info['income']['tardy'].' ',2), 'text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format($info['income']['absent'].' ',2), 'text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format($info['income']['basic_pay'].' ',2), 'text-align:right; padding-right: 5px;');

		foreach ($inc_income['deminimissList'] as $key => $value) {
			$arr_content[] = array(((isset($info["income"]["income_list"][$key])) ? number_format($info["income"]["income_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
        }
        
        $arr_content[] = array(number_format($info['income']['totalOtherDeminimissToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

		foreach ($inc_income['noDeminimissList'] as $key => $value) {
			$arr_content[] = array(((isset($info["income"]["income_list"][$key])) ? number_format($info["income"]["income_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
        }

        $arr_content[] = array(number_format($info['income']['totalOtherIncomeToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');
		foreach ($inc_adjustment as $key => $value) {
			$arr_content[] = array(((isset($info["income"]["adjustment_list"][$key])) ? number_format($info["income"]["adjustment_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
        }
        $arr_content[] = array(number_format($info['income']['totalOtherAdjustmentToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

		$arr_content[] = array(number_format($info['income']['overtime'].' ',2), 'text-align:right; padding-right: 5px;');
		$arr_content[] = array(number_format($info['income']['gross'].' ',2), 'text-align:right; padding-right: 5px;');

		$show_content .= showPerTableRow($arr_content);

		$idx += 1;
	}

	// income..
	$arr_content = array();
	$arr_content[] = array("Grand Total : ", 'font-weight: bold; text-align: right;" colspan="2');
	$arr_content[] = array(number_format((isset($grand_total['income']['salary']) ? $grand_total['income']['salary'] : '0') .' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
	$arr_content[] = array(number_format((isset($grand_total['income']['tardy']) ? $grand_total['income']['tardy'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
	$arr_content[] = array(number_format((isset($grand_total['income']['absent']) ? $grand_total['income']['absent'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
	$arr_content[] = array(number_format((isset($grand_total['income']['basic_pay']) ? $grand_total['income']['basic_pay']: '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

	foreach ($inc_income['deminimissList'] as $key => $value) {
		$arr_content[] = array(((isset($grand_total["income"]["income_list"][$key])) ? number_format($grand_total["income"]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
    }

    
	// $arr_content[] = array(number_format((isset($grand_total['income']['totalOtherDeminimissToDisplay']) ? $grand_total['totalOtherDeminimissToDisplay']['gross'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

	$arr_content[] = array((isset($grand_total['income']['totalOtherDeminimissToDisplay']) ? number_format($grand_total['totalOtherDeminimissToDisplay']['gross'], 2) : '0.00'), 'font-weight: bold; text-align:right; padding-right: 5px;');

	foreach ($inc_income['noDeminimissList'] as $key => $value) {
		$arr_content[] = array(((isset($grand_total["income"]["income_list"][$key])) ? number_format($grand_total["income"]["income_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
    }

	// $arr_content[] = array(number_format((isset($grand_total['income']['totalOtherIncomeToDisplay']) ? $grand_total['totalOtherIncomeToDisplay']['gross'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

	$arr_content[] = array((isset($grand_total['income']['totalOtherIncomeToDisplay']) ? number_format($grand_total['totalOtherIncomeToDisplay']['gross'], 2) : '0.00'), 'font-weight: bold; text-align:right; padding-right: 5px;');
	foreach ($inc_adjustment as $key => $value) {
		$arr_content[] = array(((isset($grand_total["income"]["adjustment_list"][$key])) ? number_format($grand_total["income"]["adjustment_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
    }
	// $arr_content[] = array(number_format((isset($grand_total['income']['totalOtherAdjustmentToDisplay']) ? $grand_total['totalOtherAdjustmentToDisplay']['gross'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

	// $arr_content[] = array(number_format((isset($grand_total['income']['overtime']) ? $grand_total['income']['overtime'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');
	// $arr_content[] = array(number_format((isset($grand_total['income']['gross']) ? $grand_total['income']['gross'] : '0').' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

	$arr_content[] = array((isset($grand_total['income']['totalOtherAdjustmentToDisplay']) ? number_format($grand_total['totalOtherAdjustmentToDisplay']['gross'], 2) : '0.00'), 'font-weight: bold; text-align:right; padding-right: 5px;');
	$arr_content[] = array((isset($grand_total['income']['overtime']) ? number_format($grand_total['income']['overtime'], 2) : '0.00'), 'font-weight: bold; text-align:right; padding-right: 5px;');
	$arr_content[] = array((isset($grand_total['income']['gross']) ? number_format($grand_total['gross']['gross'], 2) : '0.00'), 'font-weight: bold; text-align:right; padding-right: 5px;');


	$show_content .= showPerTableRow($arr_content);	

$show_content .= '				 
			</tbody>
				 ';

	$deduction_end_col = 3 + count($grand_total["deduction"]["fixed_deduc_list"]) + count($grand_total["deduction"]["deduc_list"]) + count($grand_total["deduction"]["loan_list"]) + 3;

$show_content .= '
		</table>
		
		<table width="100%" border="1">
			<thead>
				 ';

$show_content .= '
	<tr class="tr-header">
		<th colspan="'. $deduction_end_col .'" style="text-align: left;">Deduction</th>
	</tr>
	<tr class="tr-header">
		<th>#</th>
		<th>'. (($sort == "name") ? "EMPLOYEE" : strtoupper($sort)) .'</th>
		<th>WITH HOLDING TAX</th>
				 ';
foreach ($inc_fixed_deduc as $key => $value) $show_content .= '<th>'. $key .'</th>';						
foreach ($inc_deduction as $key => $value) $show_content .= '<th>'. $config["deduction"][$key] .'</th>';		

foreach ($inc_loan as $key => $value) $show_content .= '<th>'. $config["loan"][$key] .'</th>';

$show_content .= '		
		<th>OTHER DEDUCTION</th>
		<th>TOTAL DEDUCTION</th>
		<th>NET</th>
		
	</tr>
				 ';	
$show_content .= '			
			</thead>
				 ';
$show_content .= '
		<tbody>
				 ';
	
	$idx = 1;
	foreach ($summary as $sort_key => $info) {
		$arr_deduction_content = array();
		$arr_deduction_content[] = array($idx, 'text-align: right;');
		$arr_deduction_content[] = array($config[$sort][$sort_key], 'text-align: left; padding-left:5px;');

		foreach ($inc_fixed_deduc as $key => $value) {
			$arr_deduction_content[] = array(((isset($summary[$sort_key]["deduction"]["fixed_deduc_list"][$key])) ? number_format($summary[$sort_key]["deduction"]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
        }

		foreach ($inc_deduction as $key => $value) {
			$arr_deduction_content[] = array(((isset($summary[$sort_key]["deduction"]["deduc_list"][$key])) ? number_format($summary[$sort_key]["deduction"]["deduc_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                    
        }

		foreach ($inc_loan as $key => $value) {
			$arr_deduction_content[] = array(((isset($summary[$sort_key]["deduction"]["loan_list"][$key])) ? number_format($summary[$sort_key]["deduction"]["loan_list"][$key], 2) : '0.00').' ', 'text-align:right; padding-right: 5px;');                      
        }

		$arr_deduction_content[] = array(number_format($summary[$sort_key]['deduction']['totalOtherDeductionToDisplay'].' ',2), 'text-align:right; padding-right: 5px;');

		$arr_deduction_content[] = array(number_format($summary[$sort_key]['deduction']['with_holding_tax'].' ',2), 'text-align:right; padding-right: 5px;');

		$arr_deduction_content[] = array(number_format($summary[$sort_key]['deduction']['total_deduction'].' ',2), 'text-align:right; padding-right: 5px;');
        
		$arr_deduction_content[] = array(number_format($summary[$sort_key]['deduction']['net'].' ',2), 'text-align:right; padding-right: 5px;');


		$show_content .= showPerTableRow($arr_deduction_content);

		$idx += 1;
	}


	// deduction..
	$arr_deduction_content = array();
	$arr_deduction_content[] = array("Grand Total : ", 'font-weight: bold; text-align: right;" colspan="2');

	foreach ($inc_fixed_deduc as $key => $value) {
		$arr_deduction_content[] = array(((isset($grand_total["deduction"]["fixed_deduc_list"][$key])) ? number_format($grand_total["deduction"]["fixed_deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	}

	foreach ($inc_deduction as $key => $value) {
		$arr_deduction_content[] = array(((isset($grand_total["deduction"]["deduc_list"][$key])) ? number_format($grand_total["deduction"]["deduc_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                    
	}

	foreach ($inc_loan as $key => $value) {
		$arr_deduction_content[] = array(((isset($grand_total["deduction"]["loan_list"][$key])) ? number_format($grand_total["deduction"]["loan_list"][$key], 2) : '0.00').' ', 'font-weight: bold; text-align:right; padding-right: 5px;');                      
	}

	$arr_deduction_content[] = array(number_format($grand_total['deduction']['totalOtherDeductionToDisplay'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

	$arr_deduction_content[] = array(number_format($grand_total['deduction']['with_holding_tax'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

	$arr_deduction_content[] = array(number_format($grand_total['deduction']['total_deduction'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');

	$arr_deduction_content[] = array(number_format($grand_total['deduction']['net'].' ',2), 'font-weight: bold; text-align:right; padding-right: 5px;');


	$show_content .= showPerTableRow($arr_deduction_content);	

$show_content .= '				 
		</tbody>
		</table>
		</pagebreak>
	</div>
</div>
				 ';
}
// die;
$pdf->WriteHTML($show_content);
$pdf->Output();            
?>
