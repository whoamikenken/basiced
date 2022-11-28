<?php

$pdf = new PdfCreator_mpdf();
$pdf->Bookmark('Start of the document');
$blank1 = "______________________________";
$info = "
<body style='font-family:helvetica;'>	
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr><td align='center' colspan='4'>OFFICE OF THE HUMAN RESOURCE</td></tr>
</table>
<br/>
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr><td align='center' colspan='4'><u>PATERNITY NOTIFICATION FORM</u></td></tr>
</table>
<br/>
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr>
		<td>Employee Name</td><td>: ".$blank1."___</td> <td>Date</td><td>: ".$blank1."</td>
	</tr>
	<tr>
		<td>Designation</td><td>: ".$blank1."___</td> <td>Department</td><td>: ".$blank1."</td>
	</tr>
	<tr>
		<td>Wife's Maiden Name</td><td colspan='3'>: ".$blank1.$blank1."</td>
	</tr>
	<tr>
		<td>Home Address</td><td colspan='3'>: <hr></td>
	</tr>
	<tr>
		<td></td><td colspan='3'><hr></td>
	</tr>
</table>
<div style='margin-left:40px;'>
	This is to notify my employer that my wife is on her ______<sup>th</sup> month of pregnancy.<br/>
	She is expected to give birth on ".$blank1." to our<br/>
	<div style='padding-left:40px;'>
		<table width='75%'>
			<tr><td>[  ] first</td><td>[  ] second</td><td>[  ] third</td><td>[  ] fourth</td></tr>
		</table>
	</div>
	<i>NOTE: Counting includes all childbirths and miscarriages.</i><br/><br/>
	I have attached the following supporting documents
	<div style='padding-left:40px;'>
		<table width='75%'>
			<tr><td>[  ] physician's certification as to expected date of delivery.</td></tr>
			<tr><td>[  ] result and photocopy of the ultrasound.</td></tr>
		</table>
	</div><br/><br/>
	<p style='text-align: justify;'>I certify on my honor that the foregoing information is true and correct, and that I am providing 
	such information for the purpose of securing eligibility for Paternity Leave Benefit as provided under 
	R.A. No. 8187.</p>
</div>
<table align='right'>
<tr><td>".$blank1."____</td></tr>
<tr><td>SIGNATURE OVER PRINTED NAME</td></tr>
<tr><td align='center'>Employee</td></tr>
</table>
<br/>
<div>
	<div width='50%' style='float:left;'>
		<table >
			<tr><td>Verified by</td><td>:</td><td>".$blank1."____</td></tr>
			<tr><td></td><td></td><td>SIGNATURE OVER PRINTED NAME</td></tr>
			<tr><td></td><td></td><td align='center'>School Physician</td></tr>
			<tr><td>Noted by</td><td>:</td><td>".$blank1."____</td></tr>
			<tr><td></td><td></td><td>SIGNATURE OVER PRINTED NAME</td></tr>
			<tr><td></td><td></td><td align='center'>Department/Office Head</td></tr>
			<tr><td>Received by</td><td>:</td><td>".$blank1."____</td></tr>
			<tr><td></td><td></td><td>SIGNATURE OVER PRINTED NAME</td></tr>
			<tr><td></td><td></td><td align='center'>Office of the Human Resource</td></tr>
		</table>	
	</div>
	<div width='50%' style='float:right;'>
		<table align='right'>
			<tr><td>Date</td><td>:</td><td>".$blank1."</td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
			<tr><td>Date</td><td>:</td><td>".$blank1."</td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
			<tr><td>Date</td><td>:</td><td>".$blank1."</td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
			<tr><td>&nbsp;</td><td></td><td></td></tr>
		</table>	
	</div>
</div>
<footer><p style='font-size:8px;'>Distribution (1) Employee (2) OHR File</p></footer>
</body>";
$pdf->WriteHTML($info);
$pdf->Output();
?>
