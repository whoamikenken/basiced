<?php
//============================================================+
// File name   : overtime_request.php
// Begin       : 2014-09-03
// Last Update : 2014-09-03
//
// Description : Overtime Request Form
//               
// Author: Melvin Cobar Empleo
//
//============================================================+
/**
* @author Melvin Cobar Empleo
* @copyright 2014
*/

$pdf = new PdfCreator_mpdf('', 'Letter', 0, '', 12.7, 12.7, 14, 12.7, 8, 8);
$pdf->Bookmark('Start of the document');
$blank1 = "________";
$blank2 = $blank1.$blank1;

$info = "
<body style='font-family:calibri; font-size:12px;'>	
	<div style='border:#000 3px solid; padding:3px;'>
		<table width='100%' cellpadding='2' cellspacing='2'>
			<tr><th align='center'>SY 20___-20___</th></tr>
		</table>
		<br/>
		<table width='100%' cellpadding='2' cellspacing='2' style='font-size:13px;'>
			<tr><th align='center'>OVERTIME REQUEST FORM</th></tr>
		</table>
		<br/>
		<table width='100%'>
			<tr>
				<td width='16%'>Employee Name</td><td>:</td><td>".$blank2.$blank2.$blank2."</td>
				<td>Date of Request</td><td>:</td><td>".$blank2.$blank2."</td>
			</tr>
			<tr>
				<td>Department</td><td>:</td><td>".$blank2.$blank2.$blank2."</td>
				<td>Date of Overtime</td><td>:</td><td>".$blank2.$blank2."</td>
			</tr>
			<tr>
				<td>Designation</td><td>:</td><td>".$blank2.$blank2.$blank2."</td>
				<td></td><td></td><td></td>
			</tr>
		</table>
		<table width='70%'>
			<tr>
				<td>Duration of Overtime:</td><td>".$blank2." HOURS</td><td>from ".$blank1." to".$blank1."</td>
			</tr>
		</table>
		Purpose of Overtime:
		<hr style='height:1px; background-color:#000000; color:#000000; margin-top:20px;'/>
		<div style='float:left; width:40%;'>
			<table width='100%'>
				<tr><td>Description of Work:</td></tr>
				<tr><td><hr style='height:1px; background-color:#000000; color:#000000; margin-top:15px;'/></td></tr>
				<tr><td><hr style='height:1px; background-color:#000000; color:#000000;'/></td></tr>
				<tr><td align='center'>&nbsp;</td></tr>
				<tr><td align='center'>&nbsp;</td></tr>
				<tr><th align='center'>&nbsp;</th></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>Endorsed by:</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td align='center'>".$blank2.$blank2.$blank2."</td></tr>
				<tr><td align='center'>SIGNATURE OVER PRINTED NAME</td></tr>
				<tr><th align='center'>Department/Office Head</th></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td style='font-size:9px;'>DISTRIBUTION: (1) Department/Office File, (2) OHR File</td></tr>
			</table>
		</div>
		<div style='float:right; width:40%;'>
			<table width='100%'>
				<tr><td>Expected Output:</td></tr>
				<tr><td><hr style='height:1px; background-color:#000000; color:#000000; margin-top:15px;'/></td></tr>
				<tr><td><hr style='height:1px; background-color:#000000; color:#000000;'/></td></tr>
				<tr><td align='center'>".$blank2.$blank2.$blank2."</td></tr>
				<tr><td align='center'>SIGNATURE OVER PRINTED NAME</td></tr>
				<tr><th align='center'>Employee</th></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>Approved by:</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td align='center'>".$blank2.$blank2.$blank2."</td></tr>
				<tr><td align='center'>SIGNATURE OVER PRINTED NAME</td></tr>
				<tr><th align='center'>Division Head</th></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td style='font-size:9px; text-align:right'>Overtime Request Form [06-2014]</td></tr>
			</table>
			<br/>
		</div>
	</div>
</body>";
$pdf->WriteHTML($info);

$pdf->Output();
// end of file
?>













