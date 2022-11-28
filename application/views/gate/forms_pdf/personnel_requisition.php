<?php
//============================================================+
// File name   : personnel_requisition.php
// Begin       : 2014-09-01
// Last Update : 2014-09-01
//
// Description : Personnel Requisitin Form
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
$blank1 = "______________________";
$blank2 = $blank1.$blank1.$blank1;
$info = "
<body style='font-family:helvetica; font-size:11.5px;'>	
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr><td align='center'>OFFICE OF THE HUMAN RESOURCE</td></tr>
</table>
<br/>
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr><td align='center'><u>PERSONNEL REQUISITION FORM</u></td></tr>
</table>
<br/>

<div style='border: #000000 1px solid; padding:5px;'>
	<div style='border: #000000 2px solid; padding:5px; width:42%; float:left;'>
		DEPARTMENT/UNIT: ".$blank1." 
	</div>
	<div style='border: #000000 1px solid; padding:5px; width:42%; float:right;'>
		DATE REQUESTED: 
	</div>
	<div style='border: #000000 1px solid; padding:5px; width:98.2%; margin-top:5px; text-align:center;'>
		POSITION INFORMATION
	</div>
	<table width='98.2%'>
		<tr><td >Full Time Faculty</td><td align='center'>General Services</td></tr>
		<tr><td align='center'>Part Time Faculty</td><td align='center'>OJT/Practicumer</td></tr>
		<tr><td align='center'>Staff</td><td align='center'>Others</td></tr>
	</table>
	<div style='border: #000000 1px solid; padding-left:5px; padding-top:8px; padding-right:5px; width:98.2%; margin-top:5px;'>
		REASON FOR REQUEST:".$blank2. "_________<br/>
		<hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/><br/>
		<hr style='height:2px; background-color:#000000; color:#000000;'/>
	</div>
	<div style='border: #000000 2px solid; padding:5px; width:98.2%; margin-top:10px;'>
		POSITION DESCRIPTION AND QUALIFICATIONS
	</div>
	<div style='border: #000000 1px solid; padding:5px; width:98.2%; margin-top:10px;'>
		EDUCATION:
	</div>
	<div style='border: #000000 1px solid; padding:5px; width:98.2%; margin-top:3px;'>
		WORK EXPERIENCE:
	</div>
	<div style='border: #000000 1px solid; padding:5px; width:98.2%; margin-top:3px;'>
		OTHERS:
	</div>
	<div style='border: #000000 1px solid; padding-left:5px; padding-top:8px; padding-right:5px; width:98.2%; margin-top:5px;'>
		DESCRIPTION OF DUTIES: ".$blank2."_______<br/>
		<hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/><br/>
		<hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/><br/>
		<hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/><br/>
		<hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/><br/>
		<hr style='height:2px; background-color:#000000; color:#000000;'/>
	</div>
	<div style='margin-top:7px;'>
		<div style='border: #000000 1px solid; padding:5px; width:55.1%;  float:left;'>
			APPROVALS
		</div>
		<div style='border: #000000 1px solid; padding:5px; width:41%; float:right;margin-left:0px;'>
			ACTION TAKEN (For OHR Use)
		</div>
	</div>
	<div style='margin-top:5px;'>
		<div style='border: #000000 1px solid; padding:5px; width:55.1%;  float:left;'>
			<table width='100%'>
				<tr><td>Requested by:</td><td></td></tr>
				<tr><td></td><td align='center'><hr>SIGNATURE OVER PRINTED NAME</td></tr>
			</table><br/>
			<table width='100%'>
				<tr><td>Endorsed by:</td><td></td></tr>
				<tr><td></td><td align='center'><hr>SIGNATURE OVER PRINTED NAME</td></tr>
				<tr><td></td><td align='center' style='font-size:11px;'>Department/Unit Head</td></tr>
			</table>
		</div>
		<div style='border: #000000 1px solid; padding:5px; width:41%; float:right;margin-left:0px;'>
			<table width='100%'>
				<tr><td>Applicant Hired: ".$blank1."_</td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td>Date Hired: ".$blank1."______</td></tr>
			</table><br/>
			<table width='100%'>
				<tr><td>&nbsp;</td></tr>
				<tr><td align='center'><hr>SIGNATURE OVER PRINTED NAME</td></tr>
				<tr><td align='center' style='font-size:11px;'>Department/Unit Head</td></tr>
			</table>
		</div>
	</div>
	<div style='border: #000000 1px solid; margin-top:5px; width:98.2%; padding:5px;'>
		APPROVED BY:
		<div align='center' style='text-alignment:center;'>
			".$blank1."____________<br/>
			SIGNATURE OVER PRINTED NAME<br/>
			<p align='center' style='font-size:11px;'>School Director/Principal</p>
		</div>
	</div>

</div>

</body>";
$pdf->WriteHTML($info);
$pdf->Output();


// end of file