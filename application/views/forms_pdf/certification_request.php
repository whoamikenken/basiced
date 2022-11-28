<?php
//============================================================+
// File name   : certification_request.php
// Begin       : 2014-09-01
// Last Update : 2014-09-02
//
// Description : Request for Certification Form
//               
// Author: Melvin Cobar Empleo
//
//============================================================+
/**
* @author Melvin Cobar Empleo
* @copyright 2014
*/

$pdf = new PdfCreator_mpdf();
$pdf->Bookmark('Start of the document');
$blank1 = "________";
$blank2 = $blank1.$blank1;

$info = "
<body style='font-family:helvetica; font-size:12px;'>	
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr><th align='center'>SAINT JUDE CATHOLIC SCHOOL</th></tr>
	<tr><th align='center'>OFFICE OF THE HUMAN RESOURCE</th></tr>
</table>
<br/>
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr><th align='center'>REQUEST FORM FOR CERTIFICATION</th></tr>
</table>
<br/>
<div style='padding-left:2%; padding-right:5%;'>
	<div style='padding-left:8%;'>
		<div>
			<div style='width:40%; float:left;'>
				<table>
					<tr><th>DATE</th><td style='border: #000 1px solid; width:100%;'></td></tr>
				</table>
			</div>

			<div style='width:25%; float:right'>
				<table>
					<tr><th>ID NO.</th><td style='border: #000 1px solid; width:100%;'></td></tr>
				</table>
			</div>
		</div>
		<table>
			<tr><th>NAME</th><td colspan='3' style='border: #000 1px solid; width:100%;'></td></tr>
			<tr>
				<td></td>
				<td align='left' style='font-size:8px;'>LAST</td>
				<td align='center' style='font-size:8px;'>FIRST</td>
				<td align='right' style='font-size:8px;'>MIDDLE INITIAL</td>
			</tr>
		</table>
	</div>

	<div style='width:45%; float:left;'>
		<table>
			<tr><th>DEPARTMENT</th><td style='border: #000 1px solid; width:100%;'></td></tr>
		</table>
		<table>
			<tr><th>COMPLETED YEARS OF SERVICE</th><td style='border: #000 1px solid; width:100%;'></td></tr>
		</table>
	</div>
	<div style='width:48%; float:right'>
		<table>
			<tr><th>DESIGNATION</th><td style='border: #000 1px solid; width:100%;'></td></tr>
		</table>
		<div style='width:83%; float:right'>
			<table>
				<tr><th>NO. OF COPIES NEEDED</th><td style='border: #000 1px solid; width:100%;'></td></tr>
			</table>
		</div>
	</div>
</div>
<br/>
Information to be included:

<div style='width:30%;margin-left:15%; float:left;'>
	<table>
		<tr><td align='right'>".$blank1."</td><td>Years of Service</td></tr>
		<tr><td align='right'>".$blank1."</td><td>Designation of work</td></tr>
		<tr><td align='right'>".$blank1."</td><td>Date Hired</td></tr>
	</table>
</div>

<div style='width:42%; float:right;'>
	<table>
		<tr><td align='right'>".$blank1."</td><td>Salary</td></tr>
		<tr><td align='right'>".$blank1."</td><td>Others (kindly fill up Special Instructions)</td></tr>
	</table>
</div>
<br/><br/><br/><br/>
Special Instructions:<br/>
<hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/><br/>
<hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/><br/>

<table width='100%'>
	<tr>
		<td>REASON FOR THE CERTIFICATION:</td>
		<td width='72%'><hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/></td>
	</tr>
</table>
<br/>
<div width='50%' style='text-align:center;'>
<hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/>
Signature over Printed Name
</div>
<p style='color:#f00; font-style:italic;'>*Note: Processing requires at least 7 working days.
	Unclaimed certifications after 2 months shall be disposed.</p>
<hr/>
<p style='color:#00f; font-style:bold;'>FOR OFFICE USE ONLY </p>
<table width='100%'>
	<tr>
		<th align='right'>Transaction completed:</th>
		<td style='border: #000 1px solid; width:20%;'></td>
		<td></td>
		<th align='right'>Date claimed:</th>
		<td style='border: #000 1px solid; width:20%;'></td>
	</tr>
	<tr>
		<th></th>
		<td style='width:20%; text-align:center; font-size:8px;'>date</td>
		<td></td>
		<th></th>
		<td style='width:20%; text-align:center; font-size:8px;'>date</td>
	</tr>
</table>

</body>";
$pdf->WriteHTML($info);
$pdf->Output();
// end of file