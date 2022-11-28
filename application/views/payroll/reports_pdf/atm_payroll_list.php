<?php 

/**
 * @author Angelica
 * @copyright 2017
 *
 */

$CI =& get_instance();
$CI->load->library('PdfCreator_mpdf');
// echo '<pre>';
// print_r($list);

// function mPDF($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P') {

// $mpdf = new mPDF('utf-8','LETTER','10','','3','3','6','10','9','9');
$mpdf = new mPDF('utf-8','LETTER','10','','15','15','15','15','9','9');

$mpdf->setFooter(date("Y-m-d h:i A")."       Page {PAGENO} of {nb}");
$styles = '
			<style>
				table{
					width: 100%;
					font-family:type new roman;
					border-collapse: collapse;
				}
				.header, #maincontent th{
					color: black;
				}
				.amount{
					text-align: right;
				}
			</style>



';

// $header = '
			
// 			<table class="header">
// 				<tr>
// 					<td class="align_right" rowspan="2" style="padding-left:150px;" valign="bottom"><img src="images/school_logo.jpg" style="width: 50px;"/></td>
// 					<td><b style="font-size: 12px;">&nbsp;&nbsp;&nbsp;&nbsp;Pinnacle Technologies Inc.</b></td>
// 					<td></td>
// 				</tr>
// 				<tr>
// 					<td>Payroll Cut-Off '.date("F d, Y",strtotime($sdate))." - ".date("F d, Y",strtotime($edate)).'</td>
// 				</tr>
// 				<tr><td>&nbsp;</td></tr>
// 			</table><br>

// 			<p style="text-align:center;">Bank: '.$bank_name.'</p>
// ';

$header = "
        <table width='45%'  >
            <tr>
                <td rowspan='4' style='text-align: right;' width='60%'><img src='images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>Payroll Cut-Off ".date("F d, Y",strtotime($sdate))." - ".date("F d, Y",strtotime($edate))."</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 10px;' width='55%'><strong>Bank: ".$bank_name."</strong></span></td>
            </tr>
            
        </table>
";


$content = '
			<p style="margin-left: 290px;font-size: 12px;font-weight: bold;color:black;">ATM PAYROLL LIST</p>
			<table id="maincontent" class="table" cellspacing="0" cellpadding="10" border="1">
				<thead>
					<tr>
						<td class="align_center"></td>
						<td class="align_center"><b style="font-size: 12px;">ACCOUNT #</b></td>
						<td class="align_center"><b style="font-size: 12px;">EMPLOYEE NAME</b></td>
						<td class="align_center"><b style="font-size: 12px;">NET SALARY</b></td>
					</tr>
				</thead>
				
				<tbody>';
						$count = 1;
						$sum = 0;
						foreach ($list as $employeeid => $det) { 
$content .= ' 
								
								<tr>
									<td>'.$count.'</td>
									<td>'.$det["account_num"].'</td>
									<td>'.strtoupper(utf8_decode($det["fullname"])).'</td>
									<td>'.formatAmount($det["net_salary"]).'</td>
								</tr>
						
					'; 		
								$count++;
								$sum += $det["net_salary"];
						}
$content .= ' 					
				</tbody>
				<tfoot>
				    <tr>
				    	<td></td>
				    	<td></td>
				    	<td><b>Page Total: </b></td>
				        <td><b>'.formatAmount($sum).'</b></td>
				    </tr>
				</tfoot>
			</table>
';


$main = "
			$styles
			<div class='container'>
			$header
			$content

			</div>

";



function formatAmount($amount=''){
    if($amount){
        $amount = number_format( $amount, 2 );
    }else{
        $amount = '0.00';
    }
    return $amount;
}


$mpdf->WriteHTML($main);

$mpdf->Output();

die;
?>

			<style>
				table{
					width: 100%;
					font-family:arial;
					border-collapse: collapse;
					border: none;
				}
				.header, #maincontent th{
					color: black;
				}
				/*#maincontent table, #maincontent td {
				    border: 1px solid black;
				}*/
				/*#maincontent tbody tr:nth-child(even) {
				  background-color: white;
				}
				#maincontent tbody tr:nth-child(odd) {
				  background-color: #BDBDBD;
				}*/
				.amount{
					text-align: right;
				}
			</style>

			<div class="container">
				<table class="header">
					<tr>
						<td class="align_left" rowspan="2" style="padding:0 20px;" valign="bottom"></td>
						<td><b>Pinnacle Technologies Inc.</b></td>
					</tr>
					<tr>
						<td>Payroll Cut-Off <?=date("F d, Y",strtotime($sdate))." - ".date("F d, Y",strtotime($edate))?></td>
					</tr>
					<tr>
						<td style="color: black;"><?=$branch." :: ".$bank_name?></td>
					</tr>
				</table><br><br>

				<table id="maincontent" class="table" cellspacing="0">
					<thead>
						<tr>
							<th colspan="2">ATM PAYROLL LIST</th>
						</tr>
						<tr style="border-bottom: 1px solid black;">
							<td></td>
							<td><b>ACCOUNT #</b></td>
							<td><b>EMPLOYEE NAME</b></td>
							<td><b>NET SALARY</b></td>
						</tr>
					</thead>
					<tfoot>
					    <tr>
					    	<td></td>
					    	<td></td>
					        <td>Column total: (using colsum4 in {})</td>
					        <td>{colsum4}</td>
					    </tr>
					</tfoot>
					<tbody>
						<? 
							$count = 1;
							foreach ($list as $bank => $value) { 
								foreach ($value as $employeeid => $det) { ?>
									
									<tr>
										<td><?=$count?></td>
										<td><?=$det["account_num"]?></td>
										<td><?=utf8_decode($det["fullname"])?></td>
										<td><?=formatAmount($det["net_salary"])?></td>
									</tr>
							
						<? 		
									$count++;
								} 
							}
						?>
					</tbody>
				</table>

			</div>
