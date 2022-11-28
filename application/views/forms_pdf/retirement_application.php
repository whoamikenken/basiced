<?php
//============================================================+
// File name   : retirement_application.php
// Begin       : 2014-09-02
// Last Update : 2014-09-02
//
// Description : Application for Retirement Form
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
<body style='font-family:calibri; font-size:12px;'>	
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr><th align='center'>SAINT JUDE CATHOLIC SCHOOL</th></tr>
</table>
<br/>
<table width='100%' cellpadding='2' cellspacing='2'>
	<tr><td align='center'>APPLICATION FOR RETIREMENT</td></tr>
</table>
<div style='width:55%; float:right;'>
	<div style='padding:1%; width:45%; float:left; border:#000 2px solid;'>SSS No.</div>
	<div style='padding:1%; width:45%; float:left; border:#000 2px solid;'>TIN.</div>
</div>
<br/><br/><br/>
<div style='text-align:justify; float:none;'>
	Instructions: Write your  SSS and TIN number in the boxes above. Print clearly in ink or
	type the requested information in all white areas and initial any change you make. sign and notarize 
	this application. Submission of this application initiates a claim for your retirement benefits.
</div>
<br/>
<div style='font-size:9px;'>
	<div style='border:#000 1px solid; width:20%; height: 8%; float:left;'>
		<div style='text-align:center;'>Effective date of Retirement<br/>(required)</div>
		<br/><br/><br/>
		<table width='100%' style='font-size:9px;'>
			<tr><td>Month</td><td>Day</td><td>Year</td></tr>
		</table>
	</div>
	<div style='border:#000 1px solid; width:65%; height: 8%; float:left;'>
		<table width='100%' style='font-size:9px;'>
			<tr><td>Last Name</td><td>First Name</td><td align='center'>Middle Name</td></tr>
		</table>
	</div>
	<div style='border:#000 1px solid; width:13.6%; height: 8%; float:left; padding-left:2px;'>Sex</div>

	<div style='border:#000 1px solid; width:20%; height: 6%; float:left;'>
		<div style='text-align:center;'>Employee ID Number</div>
	</div>
	<div style='border:#000 1px solid; width:79.3%; height: 6%; float:left;'>
		<table width='100%' style='font-size:9px;'>
			<tr><td>Address: No & Street Name</td><td>Brgy.</td></tr>
		</table>
	</div>

	<div style='border:#000 1px solid; width:20%; height: 8%; float:left;'>
		<div style='text-align:center;'>Date of Birth</div>
		<br/><br/><br/><br/>
		<table width='100%' style='font-size:9px;'>
			<tr><td>Month</td><td>Day</td><td>Year</td></tr>
		</table>
	</div>
	<div style='border:#000 1px solid; width:65%; height: 8%; float:left;'>
		<table width='100%' style='font-size:9px;'>
			<tr><td width='32%'>District/Municipality</td><td>City</td><td align='center'>Zip Code</td></tr>
		</table>
	</div>
	<div style='border:#000 1px solid; width:14%; height: 8%; float:left;'>
		<div style='text-align:center;'>Telephone<br/>Number</div>
	</div>

	<div style='border:#000 1px solid; width:20%; height: 7%; float:left;'>
		<div style='border:#000 1px solid; width:49%; height: 3%; float:left;'>
			<div style='text-align:center;'>Date Hired</div>
		</div>
		<div style='border:#000 1px solid; width:48%; height: 3%; float:right;'>
			<div style='text-align:center;'>No. of Years Employed</div>
		</div>
		<div style='border:#000 1px solid; width:49%; height: 4%; float:left;'></div>
		<div style='border:#000 1px solid; width:48%; height: 4%; float:right;'></div>
	</div>

	<div style='border:#000 1px solid; width:55%; height: 7.2%; float:left;'>
		<table width='59%' style='font-size:9px;'>
			<tr><td colspan='6'>Are you holding an administrative position?</td></tr>
			<tr><td align='right'>_____</td><td>Yes</td><td></td><td align='right'>_____</td><td>No</td><td></td></tr>
			<tr><td colspan='3'>If Yes, please specify:</td>
				<td colspan='3'> <hr style='height:2px; background-color:#000000; color:#000000; margin-top:20px;'/> </td>
			</tr>
		</table>
	</div>
	<div style='border:#000 1px solid; width:24%; height: 7.2%; float:left;'>
		<table width='100%' style='font-size:9px;'>
			<tr><td>Department</td></tr>
		</table>
	</div>
</div>
<br/>
Did you ever go on any leave of one year during your employment? _____ Yes   _____ No
<br/><br/>
If yes, state the school year ".$blank2."
<br/><br/>
Are you a member of, or retired from any other government retirement system such as GSIS? _____ Yes   _____ No
<br/><br/><br/>
<div style='border:#000 1px solid; text-align:justify; padding:2px;'>
	List below any change of address or telephone number which will occure retirement and 
	give the effective date of such change.
</div>
<div style='border:#000 1px solid; padding:2px; width:79%; height:6%; float:left;'>
	Change of Address and/or Telephone Number
</div>
<div style='border:#000 1px solid; padding:2px; width:19.2%; height:6%; float:right;'>
	Effective Date
</div>
<br/>
<div style='border:#000 1px solid; padding:2px; background-color:#92A7A7; font-size:11px;'>
	DO NOT WRITE ON THIS PART. THIS WILL BE FILLED OUT BY THE OFFICE OF THE HUMAN RESOURCE & THE OFFICE OF THE FINANCE.
</div>
<div style='border:#000 1px solid; padding:2px; width:15%; height:5%; float:left; font-size:10px;'>
	Basic Salary
</div>
<div style='border:#000 1px solid; padding:2px; width:13%; height:5%; float:left; font-size:10px;'>
	Years of Service
</div>
<div style='border:#000 1px solid; padding:2px; width:45%; height:5%; float:left; font-size:10px;'>
	Total Systemic Salary Add-ons
</div>
<div style='border:#000 1px solid; padding:2px; width:23.3%; height:5%; float:left; font-size:10px;'>
	Total Retirement Pay Entitlement
</div>

</body>";
$pdf->WriteHTML($info);

$info2 = "
<body style='font-family:calibri; font-size:12px;'>	
<br/><br/><br/>

	<div style='font-size:11px;'>
		If you become critically ill and/or die before your retirement date, you are permitted to be retired for disability and we will provide your
		beneficiary with the amount due to you under the disability retirement formula. Kindly list your beneficiaries below:
	</div>

<table width='100%'>
	<tr><th align='center'>BENEFICIARY DESIGNATION</th></tr>
</table>

<div style='border-left:#000 3px solid;border-top:#000 3px solid;border-right:#000 3px solid; font-size:10px;'>
	<div style='width:40%; border-right:#000 1px solid; float:left;'>
		<table width='100%'>
			<tr><td style='border-bottom: #000 1px solid;'>Beneficiary Name</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>Address</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</div>
	<div style='width:35%; border-right:#000 1px solid; float:left;'>
		<table width='100%'>
			<tr><td style='border-bottom: #000 1px solid;'>Date of Birth</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>Relationship</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</div>
	<div style='width:24.5%; float:left;'>
		<table width='100%'>
			<tr>
				<td>___ Primary</td>
				<td>___ Contingent</td>
			</tr>
			<tr>
				<td colspan='2' style='border-top: #000 1px solid;'>&nbsp;</td>
			</tr>
			<tr>
				<td>___ Male</td>
				<td>___ Female</td>
			</tr>
			<tr><td colspan='2' style='border-top: #000 1px solid; border-bottom: #000 1px solid;'>SSS number (required):</td></tr>
			<tr><td colspan='2' >&nbsp;</td></tr>
			<tr><td colspan='2' >&nbsp;</td></tr>

		</table>
	</div>
</div>
<div style='border-left:#000 3px solid;border-top:#000 3px solid;border-right:#000 3px solid; font-size:10px;'>
	<div style='width:40%; border-right:#000 1px solid; float:left;'>
		<table width='100%'>
			<tr><td style='border-bottom: #000 1px solid;'>Beneficiary Name</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>Address</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</div>
	<div style='width:35%; border-right:#000 1px solid; float:left;'>
		<table width='100%'>
			<tr><td style='border-bottom: #000 1px solid;'>Date of Birth</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>Relationship</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</div>
	<div style='width:24.5%; float:left;'>
		<table width='100%'>
			<tr>
				<td>___ Primary</td>
				<td>___ Contingent</td>
			</tr>
			<tr>
				<td colspan='2' style='border-top: #000 1px solid;'>&nbsp;</td>
			</tr>
			<tr>
				<td>___ Male</td>
				<td>___ Female</td>
			</tr>
			<tr><td colspan='2' style='border-top: #000 1px solid; border-bottom: #000 1px solid;'>SSS number (required):</td></tr>
			<tr><td colspan='2' >&nbsp;</td></tr>
			<tr><td colspan='2' >&nbsp;</td></tr>

		</table>
	</div>
</div>

<div style='border:#000 3px solid; font-size:10px;'>
	<div style='width:40%; border-right:#000 1px solid; float:left;'>
		<table width='100%'>
			<tr><td style='border-bottom: #000 1px solid;'>Beneficiary Name</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>Address</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</div>
	<div style='width:35%; border-right:#000 1px solid; float:left;'>
		<table width='100%'>
			<tr><td style='border-bottom: #000 1px solid;'>Date of Birth</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>&nbsp;</td></tr>
			<tr><td style='border-bottom: #000 1px solid;'>Relationship</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</div>
	<div style='width:24.5%; float:left;'>
		<table width='100%'>
			<tr>
				<td>___ Primary</td>
				<td>___ Contingent</td>
			</tr>
			<tr>
				<td colspan='2' style='border-top: #000 1px solid;'>&nbsp;</td>
			</tr>
			<tr>
				<td>___ Male</td>
				<td>___ Female</td>
			</tr>
			<tr><td colspan='2' style='border-top: #000 1px solid; border-bottom: #000 1px solid;'>SSS number (required):</td></tr>
			<tr><td colspan='2' >&nbsp;</td></tr>
			<tr><td colspan='2' >&nbsp;</td></tr>
		</table>
	</div>
</div>
<br/>
<table width='100%'>
	<tr><th align='center'>THIS APPLICATION MUST BE SIGNED AND NOTARIZED TO BE VALID.</th></tr>
</table>
<br/><br/>
<table width='100%'>
	<tr><td align='center'>".$blank2.$blank2."</td><td align='center'>".$blank2.$blank2."</td></tr>
	<tr><td align='center'>Name of Applicant</td><td align='center'>Signature of Applicant</td></tr>
</table>
<div style='padding:3%;'>
	<p style='text-indent:7%; text-align:justify;'>Signed on this ".$blank1." day of ".$blank2." in the year ".$blank1." before me, 
	the undersigned, a Notary Public, personally appeared ".$blank2.$blank1.", personally known to me or proved to me on the basis of satisfactory evidence
	to be the individual whose name is subscribed to the within instrument, and acknowledged to me that he/she executed the same in his/her capacity,
	and that by his/her signature on the instrument, the individual, or the person upon behalf of which the individual acted, executed the instrument.
	</p>
</div>
<div style='padding-left:10%;'>WITNESS MY HAND AND SEAL this ".$blank2.$blank1." in ".$blank2.".</div>
<br/><br/><br/><br/><br/><br/>
<div style='text-align:left;'>
	Doc. No. ".$blank1.",<br/>
	Page. No. ".$blank1.",<br/>
	Book. No. ".$blank1.",<br/>
	Series of 20".$blank1.".
</div>
</body>";
$pdf->AddPage();
$pdf->WriteHTML($info2);

$pdf->Output();
// end of file
?>
<div></div>


