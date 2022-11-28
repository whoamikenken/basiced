<?php  
/**
* @author justin (with e)
* @copyright 2018
*/

list($month, $year) = explode("~~", $cutoff);
require_once(APPPATH."constants.php");
$pdf = new mpdf('P','LETTER','','UTF-8',5,5,8,8,9,2);
$info  = '
	<style>
	    @page{            
	        /*margin-top: 4.35cm;*/
	        margin-top: 3cm;
	        odd-header-name: html_Header;
	        odd-footer-name: html_Footer;
	    }  
	    .content{
	        height: 100%;
	    	font-family:times new roman;
	    }
	    th td{
	    	font-family:times new roman;
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
	    
	    .tr-header{
	    	background: #000000;
	    }
	    .center{
	    	text-align: center;
	    }
	    .right{
	    	text-align: right;
	    	padding-right: 5px;
	    }
	    .footer{
	    	width: 100%;
	    	text-align: right;
	    }

	</style>
		';

$infos = '
<htmlpageheader name="Header">
    <div>
    	<p>'.date('m-d-y H:i').'</p>
    	<table width="100%" style="padding: 0;">
    		<tr>
    			<td rowspan="3" style="text-align: right;"><img src="'.$imgurl.'images/school_logo.jpg" style="width: 60px;text-align: center;" /></td>
    			<td valign="middle" width="300px" style="padding-right: 50px;text-align: center;"><span style="font-size: 13px;"><b>'.$SCHOOL_NAME.'</b></span></td>
    			<td rowspan="3" style="text-align: right;"><img style="width: 60px;text-align: center;"/></td>
    		</tr>
    		<tr>
                <td valign="middle" style="padding-right: 50px;text-align: center"><span style="font-size: 10px;">'.$SCHOOL_CAPTION.'</span></td>
            </tr>
    	</table>
    </div>
    <div style="text-align:center;margin-top:2.5%">
		<label>'.$deduction.' Contribution</label><br>
		<label>For the month of '. $month .' - '. $year .'</label>
	</div>
</htmlpageheader>';

$info .= "
<htmlpageheader name='Header'>
    <div>
        <table width='60%'  >
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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px; font-family:times new roman;' width='55%'><strong>Reglementary Deduction Contributions</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px; font-family:times new roman;' width='55%'><strong>For the month of ". $month ." - ". $year ."</strong></span></td>
            </tr>
            
        </table>
    </div>
</htmlpageheader>";

if($sd_filter == "detailed"):
$info 	.= 	'
<div class="content">
	<div class="content-header">
		<table width="100%" style="font-size: 9px;">
			<thead>
				<tr class="tr-header">
					<th rowspan="2" style="color: yellow">EMPLOYEE ID</th>
					<th rowspan="2" style="color: yellow">EMPLOYEE NAME</th>
					<th rowspan="2" style="color: yellow">'. $deduction .' NUMBER</th>
					<th style="color: yellow" colspan="'.(count($cutoff_list) + 1).'">'. strtoupper($gb_display) .'</th>
					<th rowspan="2" width="7%" style="color: yellow">EE</th>
					<th rowspan="2" width="7%" style="color: yellow">EC</th>
					<th rowspan="2" width="7%" style="color: yellow">ER</th>
					<th rowspan="2" width="7%" style="color: yellow">TOTAL</th>
				</tr>
				<tr class="tr-header">
		 	';
	foreach ($cutoff_list as $co_date) {
$info 	.= 	'
					<th width="9%" style="color: yellow">'. date("M", strtotime($month)) .' '. $co_date . ', '. $year .'</th>
			';
	}

$info 	.= 	'		 					
					<th width="9%" style="color: yellow">Total</th>
				</tr>
			</thead>
			<tbody>
			';

	// table content
	ksort($emp_list);
	$old_deptid = $old_campusid = '';
	$first_cutoff = $second_cutoff = $total = $tot_ee = $tot_ec = $tot_er = $tot_totalfix = $cutoff_count = 0;
foreach ($emp_list as $sort_key => $employees) {

		// department or campus
		if($sort_key != "name"){
$info 	.= 	'
				<tr>
					<td colspan="'.(count($cutoff_list) + 8).'"><strong>'. $key_list[$sort_key] .'</strong></td>
				</tr>
			';			
		}

		if($sort == "department"):
				$info_temp = "";

				foreach ($employees as $empid => $e_info):
					if(!$e_info['campus']) $e_info['campus'] = "No Campus";
					$ee = $ec = $er = $total_fixed_deduction = 0;
					foreach ($e_info['ee'] as $m_key => $amount) $ee += $amount;
					foreach ($e_info['ec'] as $m_key => $amount) $ec += $amount;
					foreach ($e_info['er'] as $m_key => $amount) $er += $amount;
					foreach ($e_info['total_fixed_deduction'] as $m_key => $amount) $total_fixed_deduction += $amount;
					if($ec || $er || $ee){
							if($old_deptid != $e_info['deptid'] && $e_info['deptid'] != 'ACAD'){
								if($old_deptid){
									if(count($cutoff_list) > 1){
										$info_temp .= '
												<tr>
													<td colspan="3" class="right"><strong>Sub Total :</strong></td>
													<td class="right">'.number_format($first_cutoff, 2).'</td>
													<td class="right">'.number_format($second_cutoff, 2).'</td>
													<td class="right">'.number_format($total, 2).'</td>
													<td class="right">'.number_format($tot_ee, 2).'</td>
													<td class="right">'.number_format($tot_ec, 2).'</td>
													<td class="right">'.number_format($tot_er, 2).'</td>
													<td class="right">'.number_format($tot_totalfix, 2).'</td>
												</tr>
										';
									}else{
										$info_temp .= '
													<tr>
														<td colspan="3" class="right"><strong>Sub Total :</strong></td>
														<td class="right">'.number_format($first_cutoff, 2).'</td>
														<td class="right">'.number_format($total, 2).'</td>
														<td class="right">'.number_format($tot_ee, 2).'</td>
														<td class="right">'.number_format($tot_ec, 2).'</td>
														<td class="right">'.number_format($tot_er, 2).'</td>
														<td class="right">'.number_format($tot_totalfix, 2).'</td>
													</tr>
										';
									}

									$first_cutoff = $second_cutoff = $total = $tot_ee = $tot_ec = $tot_er = $tot_totalfix = $cutoff_count = 0;
								}
								$info_temp 	.= 	'
												<tr>
													<td colspan="'.(count($cutoff_list) + 8).'"><strong>'. $this->extensions->getDeparmentDescriptionReport($e_info['deptid']) .'</strong></td>
												</tr>
											';			
							}
							if($old_campusid != $e_info['campus'] && $e_info['deptid'] == 'ACAD'){
								if($old_deptid){
									if(count($cutoff_list) > 1){
										$info_temp .= '
												<tr>
													<td colspan="3" class="right"><strong>Sub Total :</strong></td>
													<td class="right">'.number_format($first_cutoff, 2).'</td>
													<td class="right">'.number_format($second_cutoff, 2).'</td>
													<td class="right">'.number_format($total, 2).'</td>
													<td class="right">'.number_format($tot_ee, 2).'</td>
													<td class="right">'.number_format($tot_ec, 2).'</td>
													<td class="right">'.number_format($tot_er, 2).'</td>
													<td class="right">'.number_format($tot_totalfix, 2).'</td>
												</tr>
										';
									}else{
										$info_temp .= '
													<tr>
														<td colspan="3" class="right"><strong>Sub Total :</strong></td>
														<td class="right">'.number_format($first_cutoff, 2).'</td>
														<td class="right">'.number_format($total, 2).'</td>
														<td class="right">'.number_format($tot_ee, 2).'</td>
														<td class="right">'.number_format($tot_ec, 2).'</td>
														<td class="right">'.number_format($tot_er, 2).'</td>
														<td class="right">'.number_format($tot_totalfix, 2).'</td>
													</tr>
										';
									}

									$first_cutoff = $second_cutoff = $total = $tot_ee = $tot_ec = $tot_er = $tot_totalfix = $cutoff_count = 0;
								}
								$info_temp 	.= 	'
												<tr>
													<td colspan="'.(count($cutoff_list) + 8).'"><strong>'. ($e_info['campus'] ? $this->extensions->getCampusDescription($e_info['campus']) : "No Campus" ) .'</strong></td>
												</tr>
											';	
							}
	$info_temp	.= 	'
					<tr>
						<td class="center">'. $empid .'</td>
						<td class="left">'. $e_info['name'] .'</td>
						<td class="center">'. ($e_info['tin_num'] ? $e_info['tin_num'] : "N/A" ) .'</td>';

		foreach ($cutoff_list as $co_date){
			if(!$cutoff_count) $first_cutoff +=  $e_info['gb_amount'][$co_date];
			else $second_cutoff += $e_info['gb_amount'][$co_date];
	$info_temp 	.= 	'
						<td class="right">'. number_format($e_info['gb_amount'][$co_date], 2).' </td>
				';
			$cutoff_count++;
		}

	$info_temp	.= 	'
						<td class="right">'. number_format($e_info['gb_total'], 2).' </td>
						<td class="right">'. number_format($ee, 2) .'</td>
						<td class="right">'. number_format($ec, 2) .'</td>
						<td class="right">'. number_format($er, 2) .'</td>
						<td class="right">'. number_format($total_fixed_deduction, 2) .'</td>
					</tr>
					';

					$old_deptid = $e_info['deptid'];
					$old_campusid = $e_info['campus'];
					$total += $e_info['gb_total'];
					$tot_ee += $ee;
					$tot_ec += $ec;
					$tot_er += $er;
					$tot_totalfix += $total_fixed_deduction;

					$cutoff_count = 0;
				}
		endforeach;
				if($sort_key != "name"){
					if(count($cutoff_list) > 1){
						$info_temp .= '
								<tr>
									<td colspan="3" class="right"><strong>Sub Total:</strong></td>
									<td class="right">'.number_format($first_cutoff, 2).'</td>
									<td class="right">'.number_format($second_cutoff, 2).'</td>
									<td class="right">'.number_format($total, 2).'</td>
									<td class="right">'.number_format($tot_ee, 2).'</td>
									<td class="right">'.number_format($tot_ec, 2).'</td>
									<td class="right">'.number_format($tot_er, 2).'</td>
									<td class="right">'.number_format($tot_totalfix, 2).'</td>
								</tr>
						';
					}else{
						$info_temp .= '
									<tr>
										<td colspan="3" class="right"><strong>Sub Total :</strong></td>
										<td class="right">'.number_format($first_cutoff, 2).'</td>
										<td class="right">'.number_format($total, 2).'</td>
										<td class="right">'.number_format($tot_ee, 2).'</td>
										<td class="right">'.number_format($tot_ec, 2).'</td>
										<td class="right">'.number_format($tot_er, 2).'</td>
										<td class="right">'.number_format($tot_totalfix, 2).'</td>
									</tr>
						';
					}
					$first_cutoff = $second_cutoff = $total = $tot_ee = $tot_ec = $tot_er = $tot_totalfix = $cutoff_count = 0;				
				}
				$info 	.= 	'
						<tr>
							
						</tr>
						'. $info_temp .'
			';						
		else :

			foreach ($employees as $empid => $e_info) {

				$gb_column = '';
				foreach ($cutoff_list as $co_key) {
					$gb_column .= '<td class="right">'. number_format($e_info['gb_amount'][$co_key], 2) .'</td>';
				}

				$ee = $ec = $er = $total_fixed_deduction = 0;
				foreach ($e_info['ee'] as $m_key => $amount) $ee += $amount;
				foreach ($e_info['ec'] as $m_key => $amount) $ec += $amount;
				foreach ($e_info['er'] as $m_key => $amount) $er += $amount;
				foreach ($e_info['total_fixed_deduction'] as $m_key => $amount) $total_fixed_deduction += $amount;
				if($ec || $er || $ee){

					$info 	.= 	'
									<tr>
										<td class="center">'. $empid .'</td>
										<td class="center">'. $e_info['name'] .'</td>
										<td class="center">'. $e_info['tin_num'] .'</td>
										'. $gb_column .'
										<td class="right">'. number_format($e_info['gb_total'], 2).' </td>
										<td class="right">'. number_format($ee, 2) .'</td>
										<td class="right">'. number_format($ec, 2) .'</td>
										<td class="right">'. number_format($er, 2) .'</td>
										<td class="right">'. number_format($total_fixed_deduction, 2) .'</td>
									</tr>
								';
				}
			}
		endif;

		// total
		if($sort_key != "name"):
$info 	.= 	'
				<tr>
					<td colspan="'.(count($cutoff_list) + 3).'" class="right"><strong> Total :</strong></td>
					<td class="right">'. number_format($summary[$sort_key]["gb_amount"], 2) .'</td>
					<td class="right">'. number_format($summary[$sort_key]["ee_amount"], 2) .'</td>
					<td class="right">'. number_format($summary[$sort_key]["ec_amount"], 2) .'</td>
					<td class="right">'. number_format($summary[$sort_key]["er_amount"], 2) .'</td>
					<td class="right">'. number_format($summary[$sort_key]["total_fixed_deduction"], 2) .'</td>
				</tr>
			';	
		endif;
	$old_deptid = '';
}



	$grand_total = array();
	foreach ($summary as $s_key => $s_info) {
		foreach ($s_info as $key => $amount) {
			if(is_numeric($amount)):
				if(!array_key_exists($key, $grand_total)) $grand_total[$key] = 0;

				$grand_total[$key] += $amount;
			endif;
		}
	}

	// grand total detailed
$info 	.= 	'
				<tr>
					<td colspan="'.(count($cutoff_list) + 3).'" class="right"><strong> Grand Total :</strong></td>
					<td class="right">'. number_format($grand_total["gb_amount"], 2) .'</td>
					<td class="right">'. number_format($grand_total["ee_amount"], 2) .'</td>
					<td class="right">'. number_format($grand_total["ec_amount"], 2) .'</td>
					<td class="right">'. number_format($grand_total["er_amount"], 2) .'</td>
					<td class="right">'. number_format($grand_total["total_fixed_deduction"], 2) .'</td>
				</tr>
			';	

$info 	.= 	'
			</tbody>			
		</table>
	</div>
</div>
			';

else :

$header = '&nbsp;';
if($sort == "department") $header = 'DEPARTMENT';
if($sort == "campus") $header = 'CAMPUS';
$info 	.= 	'
<div class="content">
	<div class="content-header">
		<table width="100%" style="font-size: 9px;">
			<thead>
				<tr class="tr-header">
					<th width="25%">'. $header .'</th>
					<th width="15%">'. strtoupper($gb_display) .'</th>
					<th width="15%">EE</th>
					<th width="15%">ER</th>
					<th width="15%">EC</th>
					<th width="15%">TOTAL</th>
				</tr>
			</thead>
			<tbody>
		 	';
 	
 	ksort($summary);
	foreach ($summary as $s_key => $s_info) {
		
$info 	.= 	'
				<tr>
					<td>'. (($s_key != 'name') ? $key_list[$s_key] : 'ALL EMPLOYEE') .'</td>
					<td class="right">'. number_format($s_info["gb_amount"], 2) .'</td>
					<td class="right">'. number_format($s_info["ee_amount"], 2) .'</td>
					<td class="right">'. number_format($s_info["ec_amount"], 2) .'</td>
					<td class="right">'. number_format($s_info["er_amount"], 2) .'</td>
					<td class="right">'. number_format($s_info["total_fixed_deduction"], 2) .'</td>
				</tr>
			';		
	}

	$grand_total = array();
	foreach ($summary as $s_key => $s_info) {
		foreach ($s_info as $key => $amount) {
			if(is_numeric($amount)):
				if(!array_key_exists($key, $grand_total)) $grand_total[$key] = 0;

				$grand_total[$key] += $amount;
			endif;
		}
	}

	// grand total detailed
$info 	.= 	'
				<tr>
					<td class="right"><strong> Grand Total :</strong></td>
					<td class="right">'. number_format($grand_total["gb_amount"], 2) .'</td>
					<td class="right">'. number_format($grand_total["ee_amount"], 2) .'</td>
					<td class="right">'. number_format($grand_total["ec_amount"], 2) .'</td>
					<td class="right">'. number_format($grand_total["er_amount"], 2) .'</td>
					<td class="right">'. number_format($grand_total["total_fixed_deduction"], 2) .'</td>
				</tr>
			';	

$info 	.= 	'
			</tbody>			
		</table>
	</div>
</div>
			';
endif;

$info .= '
	<htmlpagefooter name="Footer">
		<br>
		<div class="footer">
			Page : {PAGENO} of {nb}
		</div>
	</htmlpagefooter>
';
//$pdf->setFooter('{PAGENO}');
$pdf->WriteHTML($info);
$pdf->Output();            
?>