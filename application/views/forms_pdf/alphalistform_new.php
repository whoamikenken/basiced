<?php
require_once(APPPATH."constants.php");
$year  = ($_GET['year'] ? $_GET['year'] : $_POST['year']);

$pdf = new mpdf('utf-8','LEGAL-L','','UTF-8',5,5,5,8,9,2);
 
$year  = ($_GET['year'] ? $_GET['year'] : $_POST['year']);
	
$result = $this->reports->alphalistEmp($year);

//$pdf->WriteHTML($info);


$info = "
<style>
.footer{
	width: 100%;
	text-align: right;
}
</style>
";
$info .= "
					<div>
						<div>
							<label>Payroll Sheet</label><br>
							<label>for the year <u>".$year."</u></label>
						</div>
						<div style='padding-top:1%;font-size:10px'>
							<label>Client : <b>".$SCHOOL_NAME."</b></label><br>
							<label>Address : <b>".$SCHOOL_ADDRESS."</b></label>
							<label>SSS# : <b>".$SCHOOL_SSS."</b></label>
							<label>PEN : <b>".$SCHOOL_PEN."</b></label>
							<label>TIN : <b>".$SCHOOL_TIN."</b></label>
							<label>PAGIBIG TRN : <b>".$SCHOOL_PAGIBIG_TRN."</b></label>
						</div>
						<div style='padding-top:1%'>
							<table border='1' style='width:100%;font-size:.7em;border-collapse: collapse;text-align:center'>
								<thead>
									<tr style='background-color:".$PEACH."'>
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
							</table>
						</div>
					</div>";

$footer = array (
    'odd' => array (
        'R' => array (
            'content' => 'Page : {PAGENO} of {nb}',
            'font-size' => 10,
            'font-family' => 'serif',
            'color'=>'#000000'
        )
    ),
    'even' => array ()
); 
$pdf->SetFooter($footer);
$pdf->WriteHTML($info);
$pdf->Output();

function formatAmount($amount=''){
	$return ="";
if($amount)
{
    $return = number_format( $amount, 2 );
}
else
{
    $return = '0.00';
}
return $return;
}
?>