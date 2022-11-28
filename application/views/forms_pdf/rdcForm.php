<?php

require_once(APPPATH."constants.php");
$division  = ($_GET['division'] ? $_GET['division'] : $_POST['division']);
$department  = ($_GET['department'] ? $_GET['department'] : $_POST['department']);
$cutoff  = ($_GET['cutoff'] ? $_GET['cutoff'] : $_POST['cutoff']);
$deduction  = ($_GET['deduction'] ? $_GET['deduction'] : $_POST['deduction']);
$type = "";

$isRDCForm = ($isMRRReport) ? false : true;

$result = $this->reports->rdc($division,$department,$cutoff,$deduction, $isRDCForm);

if($deduction == "PAGIBIG") $type = "PAG-IBIG";
else $type = $deduction;


$pdf = new mpdf('P','LETTER','','UTF-8',5,5,8,5);
$info  = "  <style>
                @page{            
                    /*margin-top: 4.35cm;*/
                    margin-top: 5.5cm;
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
				

            </style>";

$cutoff_name = '';
$arr_cutoff = array();

if($isRDCForm){
	$arr_cutoff = $this->employeemod->getCutoff($cutoff);            
	$exp_co = explode("~~", $cutoff);	
	$cutoff_name = $exp_co[0];
}else{
	$cutoff_name = $cutoff;
	
	list($start_date, $end_date) = explode(",",$cutoff);
	$arr_cutoff[] = array(
		'start_date' => $start_date,
		'end_date' => $end_date
	);

	
}            


$info .= "
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
	<div style='text-align:center;margin-top:2.5%'>
		<label>".$type." Contribution</label><br>
		<label>For the month of ". $cutoff_name ."</label>
	</div>
</htmlpageheader>";


$thead = "";

foreach ($arr_cutoff as $key => $l_cutoff) {
	extract($l_cutoff);
	$date = date("M", strtotime($start_date)).' '.date("d",strtotime($start_date)).'-'.date("d",strtotime($end_date)).', '. date("Y", strtotime($start_date));
	$thead .= "<th style='padding:1%;'>$date</th>";
}

$info .= "
<div class='content'>
    <div class='content-header'>
		
        <table width='100%' style='font-size: 9px;' >
            <tr style='background: #3C8DBC'>
				<th style='padding:1%;' rowspan='2'>EMPLOYEE ID</th>
				<th style='padding:1%; width:20%;' rowspan='2'>EMPLOYEE NAME</th>
				<th style='padding:1%;' rowspan='2'>".$type." NUMBER</th>
				<th style='padding:1%; width:30%;' colspan='". (count($arr_cutoff) + 1) ."'>". (($type != "PHILHEALTH") ? "Gross" : "Basic") ." Pay</th>
				<th style='padding:1%;' rowspan='2'>EE</th>
				<th style='padding:1%;' rowspan='2'>EC</th>
				<th style='padding:1%;' rowspan='2'>ER</th>
				<th style='padding:1%;' rowspan='2'>TOTAL</th>
			</tr>
			<tr style='background: #3C8DBC'>
				$thead
				<th style='padding:1%;'>Total</th>
			</tr>"; 

			

			$page_ee = 0;
			$page_ec= 0;
			$page_er = 0;
			$page_total = 0;
			
			$total_ee = 0;
			$total_ec= 0;
			$total_er = 0;
			$total_total = 0;
			
			$i = 0;
			foreach($result as $row)
			{
				
				$efixeddeduc = explode("/",$row->fixeddeduc);
				for($x=0;$x < count($efixeddeduc);$x++){
				$eefixeddeduc = explode("=", $efixeddeduc[$x]);
					if($eefixeddeduc[0] == $deduction){
					
					$fixeddeduc = $eefixeddeduc[1];
						
					}

				}
				if($deduction == "PAGIBIG"){
				$table = 'hdmf_deduction';	
				}else{
				$table = strtolower($type).'_deduction';
				}
				


				$sqlDed = $this->db->query("SELECT * FROM $table WHERE emp_ee ='$fixeddeduc'")->result_array();
				


				$deduc = $deduction;
				$t = ""; $totalCont = 0;
				if($deduc == "SSS"){
					$empcon = $sqlDed[0]['emp_con'];
					$emper = $sqlDed[0]['emp_er'];
					$t = $row->emp_sss;
					$totalCont = $sqlDed[0]['total_contribution'];
				}else if($deduc == "PHILHEALTH"){
					$emper = $fixeddeduc;
					$totalCont = $fixeddeduc + $emper;
					$t = $row->emp_philhealth;
				}else if($deduc == 'PAGIBIG'){
					$emper = $fixeddeduc;
					$totalCont = $fixeddeduc + $emper;
					$t = $row->emp_pagibig;
				}
				$i+=1;
				
				$gob = 0; 
				$info .="<tr>
							<td style='text-align:center'>".$row->employeeid."</td>
							<td style='text-align:center'>".$row->fullname."</td>";
				if($fixeddeduc > 0){
						
						$column = ($type != "PHILHEALTH") ? "gross" : "salary";
						$col_val = "";
						
						$td_val = '';
						$row_val = 0;
						foreach ($arr_cutoff as $key => $l_cutoff) {
							extract($l_cutoff);
							$payroll_base_id = '';
							$q_amount = $this->db->query("SELECT $column AS amount,id FROM payroll_computed_table WHERE employeeid='{$row->employeeid}' AND cutoffstart='{$start_date}' AND cutoffend='{$end_date}' AND bank <> '';")->result();

							$amount_gob = 0;
							foreach ($q_amount as $res) {
								$amount_gob += $res->amount;
								$row_val += $res->amount;
								$gob += $res->amount;
								$payroll_base_id = $res->id;
							}

							if($payroll_base_id){
								$ee_er_q = $this->db->query("SELECT * FROM payroll_computed_ee_er WHERE base_id='$payroll_base_id'");
								foreach ($ee_er_q->result() as $key => $row) {
									if($row->code_deduction == "PAGIBIG"){
										$fixeddeduc = $row->EE;
										$empcon = $row->EC;
										$emper = $row->ER;
										$prov_er = $row->provident_er;
										$prov_ee = $row->provident_ee;
										$tot_er = $row->total_er;
										$tot_ee = $row->total_ee;
										$total = $row->total;
										$totalCont += ($fixeddeduc + $emper + $empcon);
									}
								}
							}

							if(count($q_amount) == 0 ){
								$td_val .="<td style='text-align:center'>". number_format(0,2) ."</td>";
							}else{
								$td_val .="<td style='text-align:center'>". number_format($amount_gob,2) ."</td>";

							}

						}

						$td_val .="<td style='text-align:center'>". number_format($row_val,2) ."</td>";

						
						$info .= "
							<td style='text-align:center'>".$t."</td>
							$td_val
							<td style='text-align:center'>".number_format($fixeddeduc,2)."</td>
							<td style='text-align:center'>".number_format($empcon,2)."</td>
							<td style='text-align:center'>".number_format($emper,2)."</td>
							<td style='text-align:center'>".number_format($prov_er,2)."</td>
							<td style='text-align:center'>".number_format($prov_ee,2)."</td>
							<td style='text-align:center'>".number_format($tot_er,2)."</td>
							<td style='text-align:center'>".number_format($tot_ee,2)."</td>
							<td style='text-align:center'>".number_format($total,2)."</td>
							<td style='text-align:center'>".number_format($totalCont,2)."</td>
						</tr>";
				}

				$page_gob += $gob;
				$page_ee += $fixeddeduc;
				$page_ec += $empcon;
				$page_er += $emper;
				$page_total += $totalCont;
				
				$td_val = '';
				foreach ($arr_cutoff as $key => $value) $td_val .="<td></td>";

				$total_gob = 0;
				if(!$result[$i] && $i % 60)
				{
					$info .= "<tr>
						<td></td>
						<td></td>
						$td_val
						<td style='text-align:center;padding-top:5%'>Page Total</td>
						<td style='text-align:center;padding-top:5%'>".number_format($page_gob,2)."</td>
						<td style='text-align:center;padding-top:5%'>".number_format($page_ee,2)."</td>
						<td style='text-align:center;padding-top:5%'>".number_format($page_ec,2)."</td>
						<td style='text-align:center;padding-top:5%'>".number_format($page_er,2)."</td>
						<td style='text-align:center;padding-top:5%'>".number_format($page_prov_er,2)."</td>
						<td style='text-align:center;padding-top:5%'>".number_format($page_prov_ee,2)."</td>
						<td style='text-align:center;padding-top:5%'>".number_format($page_tot_er,2)."</td>
						<td style='text-align:center;padding-top:5%'>".number_format($page_tot_ee,2)."</td>
						<td style='text-align:center;padding-top:5%'>".number_format($page_total,2)."</td>
						<td style='text-align:center;padding-top:5%'>".number_format($page_total,2)."</td>
					</tr>";
					
					$total_gob += $page_gob;
					$total_ee += $page_ee;
					$total_ec += $page_ec;
					$total_er += $page_er;
					$total_prov_er += $page_er;
					$total_prov_ee += $page_er;
					$total_tot_er += $page_er;
					$total_tot_ee += $page_er;
					$total_tot += $page_er;
					$total_total += $page_total;
				}
				
				if(!$result[$i])
				{
					$info .= "<tr >
						<td></td>
						<td></td>
						$td_val
						<td style='text-align:center;font-weight:bold;padding-top:3%'>Grand Total</td>
						<td style='text-align:center;font-weight:bold;padding-top:3%'>".number_format($total_gob,2)."</td>
						<td style='text-align:center;font-weight:bold;padding-top:3%'>".number_format($total_ee,2)."</td>
						<td style='text-align:center;font-weight:bold;padding-top:3%'>".number_format($total_ec,2)."</td>
						<td style='text-align:center;font-weight:bold;padding-top:3%'>".number_format($total_er,2)."</td>
						<td style='text-align:center;font-weight:bold;padding-top:3%'>".number_format($total_prov_er,2)."</td>
						<td style='text-align:center;font-weight:bold;padding-top:3%'>".number_format($total_prov_ee,2)."</td>
						<td style='text-align:center;font-weight:bold;padding-top:3%'>".number_format($total_tot_er,2)."</td>
						<td style='text-align:center;font-weight:bold;padding-top:3%'>".number_format($total_tot_ee,2)."</td>
						<td style='text-align:center;font-weight:bold;padding-top:3%'>".number_format($total_tot,2)."</td>
						<td style='text-align:center;font-weight:bold;padding-top:3%'>".number_format($total_total,2)."</td>
					</tr>";
				}
			  
			}
			
$info .= "      
        </table>
    </div>
</div>"; 

$pdf->WriteHTML($info);

$pdf->Output();
?>



