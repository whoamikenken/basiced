<?php
//============================================================+
// File name   : seminar_application.php
// Begin       : 2014-09-01
// Last Update : 2014-09-01
//
// Description : Seminar Application Form
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
$hyphen = "----------";

$info = "
<body style='font-family:helvetica; font-size:12.5px;'>	
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr><td align='center'>OFFICE OF THE HUMAN RESOURCE</td></tr>
</table>
<br/>
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr><td align='center'><u>APPLICATION FORM FOR SEMINAR</u></td></tr>
</table>
<br/>
<table width='100%'>
	<tr><td width='60%'>Name: ".$blank1.$blank1."_________</td><td>Date Filed: ".$blank1."______</td></tr>
	<tr><td width='60%'>Department/Unit: ".$blank1."____________________</td><td>Designation: ".$blank1."____</td></tr>
</table>
<table width='100%'>
	<tr><td>A.</td><td>NATURE OF THE SEMINAR<i>(please attache invitation/brochure)</i></td></tr>
	<tr><td></td><td>TOPIC: ".$blank2.$blank1."___</td></tr>
	<tr><td></td><td>DATE: ".$blank1.$blank1."____ TIME: ".$blank1."_______________</td></tr>
	<tr><td></td><td>VENUE: ".$blank2.$blank1."__</td></tr>
</table>
<table width='100%'>
	<tr><td width='1%'>B.</td><td colspan='3'>ENDORSEMENTS</td></tr>
	<tr><td></td><td width='30%'>Subject Area Coordinator</td><td>:</td><td>".$blank1.$blank1.$blank1."</td></tr>
	<tr><td></td><td width='30%'>Assistant Head Teacher</td><td>:</td><td>".$blank1.$blank1.$blank1."</td></tr>
	<tr><td></td><td width='30%'>Head Teacher/Unit Head</td><td>:</td><td>".$blank1.$blank1.$blank1."</td></tr>
</table>
<table width='100%' >
	<tr><td width='1%'>C.</td><td>RECOMMENDATION</td></tr>
</table>
<table align='center' width='80%'>
	<tr>
		<td style='text-align:center;'><input type='checkbox'>&nbsp;For Approval</td>
		<td style='text-align:center;'><input type='checkbox'>&nbsp;For Disapproval</td>
	</tr>
</table>
<div style='padding-left:6%;'>
		<div style='width:30%; float:left'>
			<p align='center'>".$blank1."_______<br/>
			SIGNATURE OVER PRINTED NAME<br/>
			Division Head</p>
		</div>
		<div style='width:30%; float:right'>
			<p align='center'>".$blank1."_______<br/>
				Date</p>
		</div>
		Reason/s for Disapproval:<br/>
		<hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/><br/>
</div>
<table width='100%'>
	<tr>
		<td width='1%'>&nbsp;</td>
		<td><hr style='height:2px; background-color:#000000; color:#000000;'/></td>
	</tr>
</table>
<table width='100%' >
	<tr><td width='1%'>D.</td><td>ACTION TAKEN</td></tr>
</table>


<table align='center' width='80%'>
	<tr>
		<td style='text-align:center;'><input type='checkbox'>&nbsp;Approved</td>
		<td style='text-align:center;'><input type='checkbox'>&nbsp;Disapproved</td>
	</tr>
</table>
<div style='padding-left:6%;'>
		<div style='width:30%; float:left'>
			<p align='center'>".$blank1."_______<br/>
			SIGNATURE OVER PRINTED NAME<br/>
			School Director/Principal</p>
		</div>
		<div style='width:30%; float:right'>
			<p align='center'>".$blank1."_______<br/>
				Date</p>
		</div>
		Reason/s for Disapproval:<br/>
		<hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/><br/>
</div>
<table width='100%'>
	<tr>
		<td width='1%'>&nbsp;</td>
		<td><hr style='height:2px; background-color:#000000; color:#000000;'/></td>
	</tr>
</table>

<div style='margin-left:1%;'>
".$hyphen.$hyphen.$hyphen.$hyphen.$hyphen.$hyphen."<i>to be accomplished by the attendee</i>".$hyphen.$hyphen.$hyphen.$hyphen.$hyphen.$hyphen."
</div>

<div style='padding-left:6%;'>
	<table width='90%'>
		<tr><td>1.</td><td>Submission of Official Receipt to the Office of the Cashier</td><td></td></tr>
		<tr><td></td><td align='center'>".$blank1.$blank1."</td><td>&nbsp;</td><td align='center'>".$blank1."</td></tr>
		<tr><td></td><td align='center'>SIGNATURE OVER PRINTED NAME</td><td>&nbsp;</td><td align='center'>Date</td></tr>
		<tr><td></td><td align='center'>Head Cashier</td><td>&nbsp;</td><td>&nbsp;</td></tr>
	</table>
	<table>
		<tr><td>1.</td><td>Submission of the Photocopy of Certificate to the Office of the Human Resource</td><td></td></tr>
	</table>
	<table width='90%'>
		<tr><td width='14%'>&nbsp;</td><td align='center'>".$blank1.$blank1."</td><td>&nbsp;</td><td align='center'>".$blank1."</td></tr>
		<tr><td></td><td align='center'>SIGNATURE OVER PRINTED NAME</td><td width='16%'>&nbsp;</td><td align='center'>Date</td></tr>
		<tr><td></td><td align='center'>Head, Office of the Human Resource</td><td>&nbsp;</td><td>&nbsp;</td></tr>
	</table>
</div>



</body>";
$pdf->WriteHTML($info);
$pdf->Output();


// end of file
?>
