<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */

$CI =& get_instance();
$CI->load->model('extras');

$CI->load->library('PdfCreator_mpdf');

$mpdf = new mPDF('utf-8','LETTER-L','9','','10','10','35','5','5','5');

$header = "
			
			

			<table class='header' width='100%' style='padding:0;'>
            <tr>
                <td rowspan='3' style='text-align: left;' width='60px'><img src='images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td>&nbsp;</td>
                
            </tr>
            <tr>
                <td valign='middle' width='90%' style='font-size: 20px;'><b>Pinnacle Technologies Inc.</b></span></td>
            </tr>
            <tr>
                <td style='border-bottom: 1px solid #000;' width='90%'>Human Resources Department</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td></td>
                <td valign='middle' style='text-align: center;'><strong>Leave Report</strong></span></td>
                
            </tr>
        </table>
";
# for mcu-hyperion 21233
# by justin (with e)
# > footer para sa pdf na ito..

$n_data = $this->employee->getHRUserAndUserFullname($this->session->userdata('username'));
$user_fullname = '<strong>'.$n_data['user'].'</strong>';
$hr_fullname = '<strong>'.$n_data['hr'].'</strong>';
$footer = "
    
        
            <table width='100%'>
                <tr >
                    <td width='50%' style='border-top: 1px solid #000'>". $user_fullname ."</td>
                    <td width='50%' style='text-align: right; border-top: 1px solid #000'><strong>". date("F m, Y") ." -</strong> Page  <strong>{PAGENO}</strong> of <strong>{nbpg}</strong></td>
                </tr>
            </table>
             
    
";
# end for mcu-hyperion 21233

$mpdf->SetHTMLHeader($header);
$mpdf->SetHTMLFooter("$footer");

$content = '
			<style>
				.table-striped tr:nth-child(even) {background: #CCC}
				.table-striped tr:nth-child(odd) {background: #FFF}
				.container{
					content: "";
					display: block;
					clear: both;
				}
				table{
					border-collapse: collapse;
					font-family:calibri;
				}
				table td{
					padding: 0 5px;
				}
				th {
					padding: 10px 5px;
				}

				.center{
					text-align: center;
				}
			</style>


			<div class="container">
				<table class="table table-striped" cellspacing="10" border="1"> 
					<thead>
						<tr>
							<th>EMPLOYEE NAME</th>
							<th>POSITION</th>
							<th>DEPARTMENT</th>
							<th>TYPE</th>
							<th>DAYS</th>
							<th>DATE OF EXCLUSIVE</th>
							<th>REASON</th>
							<th>REMAINING BALANCE</th>
						</tr>
					</thead>
					<tbody>';
						
						if($leave['list']){
							foreach ($leave['list'] as $key => $row) {
$content .= '						
								<tr>
									<td>'.$row->fullname.'</td>
									<td>'.$row->posdesc.'</td>
									<td>'.$row->deptid.'</td>
									<td class="center">'.($row->leavetype == "other" ? $row->other : $row->leavetype).'</td>
									<td class="center">'.$row->no_days.'</td>
									<td class="center" style="width:180px;">'.date("M d, Y",strtotime($row->fromdate)). " - " . date("M d, Y",strtotime($row->todate)).'</td>
									<td>'.$row->remarks.'</td>
									<td class="center">'.$row->balance.'</td>
								</tr>';
						
							}
						}
						if($service_credit['list']){
							foreach ($service_credit['list'] as $key => $row) {
$content .= '						
								<tr>
									<td>'.$row->fullname.'</td>
									<td>'.$row->description.'</td>
									<td>'.$row->deptid.'</td>
									<td class="center">SC</td>
									<td class="center">'.$row->total_sc.'</td>
									<td class="center" style="width:180px;">'.date("M d, Y",strtotime($row->date)). " - " . date("M d, Y",strtotime($row->date)).'</td>
									<td>'.$row->remarks.'</td>
									<td class="center">'.$row->available_sc.'.00</td>
								</tr>';
						
							}
						}

						if($ob_app['list']){
							foreach ($ob_app['list'] as $key => $row) {
								if($row->othertype == "DIRECT") $othertype = "OB";
								if($row->othertype == "CORRECTION") $othertype = "CORRECTION";
$content .= '						
								<tr>
									<td>'.$row->fullname.'</td>
									<td>'.$row->description.'</td>
									<td>'.$row->deptid.'</td>
									<td class="center">'.$othertype.'</td>
									<td class="center">'.$row->no_days.'</td>
									<td class="center" style="width:180px;">'.date("M d, Y",strtotime($row->fromdate)). " - " . date("M d, Y",strtotime($row->todate)).'</td>
									<td>'.$row->remarks.'</td>
									<td class="center">--</td>
								</tr>';
						
							}
						}
$content .= '						
					</tbody>
				</table>
			</div>
			<br>
			<br>
			&nbsp;&nbsp;&nbsp;&nbsp;<b>Generated by:</b>
			<br>
			<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'. $user_fullname .'</u><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Position
			<br>
			<br>
			&nbsp;&nbsp;&nbsp;&nbsp;<b>Noted by:</b>
			<br>
			<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<u>'. $hr_fullname .'</u><br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HR Director
';

$mpdf->WriteHTML($content);
$mpdf->Output();

die;
?>
<style>
	.table-striped tr:nth-child(even) {background: #CCC}
	.table-striped tr:nth-child(odd) {background: #FFF}
	.container{
		content: "";
		display: block;
		clear: both;
	}
	table{
		border-collapse: collapse;
		font-family:calibri;
	}

	.center{
		text-align: center;
	}
</style>


<div class="container">
	<table class="table table-striped" cellspacing="0" border="1"> 
		<thead>
			<tr>
				<th>EMPLOYEE NAME</th>
				<th>POSITION</th>
				<th>DEPARTMENT</th>
				<th>TYPE</th>
				<th>DAYS</th>
				<th>DATE OF EXCLUSIVE</th>
				<th>REASON</th>
				<th>REMAINING BALANCE</th>
			</tr>
		</thead>
		<tbody>
			<?
			if($list){
				foreach ($list as $key => $row) {
			?>
					<tr>
						<td><?=$row->fullname?></td>
						<td><?=$row->posdesc?></td>
						<td><?=$row->deptid?></td>
						<td><?=($row->leavetype == "other" ? $row->other : $row->leavetype)?></td>
						<td><?=$row->no_days?></td>
						<td><?=date("M d, Y",strtotime($row->fromdate)). " - " . date("M d, Y",strtotime($row->todate))?></td>
						<td><?=$row->remarks?></td>
						<td class="center"><?=$row->balance?></td>
					</tr>
			<?
				}
			}
			?>
		</tbody>
	</table>
</div><br>