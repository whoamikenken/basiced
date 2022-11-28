<?php
require_once(APPPATH."constants.php");
$year  = ($_GET['year'] ? $_GET['year'] : $_POST['year']);

$pdf = new mpdf('utf-8','LEGAL-L','','UTF-8',5,5,5,5);
 

$pdf->WriteHTML($info);
$info = "
					<style>
						@page{            
							margin-top: 3.15cm;
							odd-header-name: html_Header;
							odd-footer-name: html_Footer;
						}  
					
						label {
							font-size:500px;
						}
						
					
					
					</style>
";
$info = "
					<htmlpageheader>

					</htmlpageheader>
";
$info .= "
					<div>
						<div>
							<label>Payroll Sheet</label><br>
							<label>for the year <u>".$year."</u></label>
						</div>
						<div style='padding-top:1%'>
							<label>Client : <b>".$SCHOOL_NAME."</b></label><br>
							<label>Address : </label>
							<label>SSS# : </label>
							<label>PEN : </label>
							<label>TIN : </label>
							<label>PAGIBIG TRN : </label>
						</div>
						<div style='padding-top:1%'>
							<table border='1' style='width:100%;font-size:.7em;border-collapse: collapse;'>
								<thead>
									<tr style='background-color:#ffdab9'>
										<th style='vertical-align: bottom;'>No.</th>
										<th style='vertical-align: bottom;'>Name</th>
										<th style='vertical-align: bottom;'>SSS #</th>
										<th style='vertical-align: bottom;'>TIN</th>
										<th style='vertical-align: bottom;'>Philhealth No.</th>
										<th style='vertical-align: bottom;'>Pag-Ibig</th>
										<th style='vertical-align: bottom;'>B-DAY</th>
										<th style='vertical-align: bottom;'>Period Covered</th>
										<th style='vertical-align: bottom;'>Net Income</th>
										<th style='vertical-align: bottom;'>COLA</th>
										<th style='vertical-align: bottom;'>Gross Income</th>
										<th style='vertical-align: bottom;'>Total SSS EE</th>
										<th style='vertical-align: bottom;'>Total MCR EE</th>
										<th style='vertical-align: bottom;'>TOTAL P-Ibig EE</th>
										<th style='vertical-align: bottom;'>Total EE</th>
										<th style='vertical-align: bottom;'>Net Pay w/o EE</th>
										<th style='vertical-align: bottom;'>13th Mo. Pay</th>
										<th style='vertical-align: bottom;'>Total Salary</th>
										<th style='vertical-align: bottom;'>Status</th>
										<th style='vertical-align: bottom;'>Personal Exemption</th>
										<th style='vertical-align: bottom;'>Tax due</th>
										<th style='vertical-align: bottom;'>Tax W/Held</th>
										<th style='vertical-align: bottom;'>Still Due</th>
										<th style='vertical-align: bottom;'>Monthly Tax</th>
										<th style='vertical-align: bottom;'>Still Due</th>
										<th style='vertical-align: bottom;'>Monthly Taxes</th>
									</tr>
								</thead>
";
$info .= "
								<tbody>
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td style='background-color:#8db600'></td>
										<td style='background-color:#8db600'></td>
										<td style='background-color:#8db600'></td>
										<td></td>
										<td></td>
										<td></td>
										<td style='background-color:#ffdab9'></td>
										<td style='background-color:#ffcc00'></td>
										<td></td>
										<td style='background-color:#3399ff'></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								</tbody>
";

$info .= "
								<tfoot>
									<tr style='background-color:#ffdab9'>
										<th></th>
										<th>TOTAL</th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</tfoot>
";

$info .= "
							</table>
						</div>
					</div>
";

$pdf->WriteHTML($info);
$pdf->Output();
?>



