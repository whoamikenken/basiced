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

$pdf = new PdfCreator_mpdf();
$pdf->Bookmark('Start of the document');
$blank1 = "________";
$blank2 = $blank1.$blank1;
$checkbox1 = '<img src="'.dirname(__FILE__) .'/img/checkbox.png"  width="20" height="15">';
$info = "
<body style='font-family:calibri; font-size:11px;'>	
	<div style='border:#000 3px solid; padding:3px; width:50%'>
		<table width='100%' style='font-size:13px;'>
			<tr><th align='center'>EARLY DISMISSAL FORM</th></tr>
		</table>
		<br/>
		<div style='width:40%; float:right; text-align:center;'>
			".$blank2.$blank1." Date
		</div>
		<br/><br/><br/>
		<table width='100%'>
			<tr>
				<td width='5%'>Name</td>
				<td width='1%'>: </td>
				<td>".$blank2.$blank2.$blank2.$blank1."____</td>
			</tr>
		</table>
		<table width='100%'>
			<tr>
				<td width='5%'>Department</td>
				<td width='1%'>:</td>
				<td>".$blank2.$blank2.$blank2."____</td>
			</tr>
		</table>
		<table width='100%'>
			<tr>
				<td width='40%'>Nature of the Business</td>
				<td width='1%'>:</td>
				<td>".$checkbox1." OFFICIAL</td>
				<td>".$checkbox1." PERSONAL</td>
			</tr>
		</table>
		<br/>
		Kindly state the Purpose/Reason/Destination:<br/>
		<hr style='height:1px; background-color:#000000; color:#000000; margin-top:15px;'/><br/>
		<hr style='height:1px; background-color:#000000; color:#000000;'/><br/>
		<hr style='height:1px; background-color:#000000; color:#000000;'/><br/>
		<hr style='height:1px; background-color:#000000; color:#000000;'/><br/>
		<br/>

		<div style='width:52%; float:right; text-align:center;'>
			".$blank2.$blank2."___ SIGNATURE OVER PRINTED NAME<br/> 
			<div style='font-weight:bold;'>Employee</div>
		</div>
		<br/>
		<div width='100%'>I have granted my permission.</div>
		<br/>

		<div style='width:52%; float:right; text-align:center;'>
			".$blank2.$blank2."___ SIGNATURE OVER PRINTED NAME<br/> 
			<div style='font-weight:bold;'>Department/Division Head</div>
		</div>
		<br/><br/><br/><br/>
		<div style='width:100%; font-size:8px;'>DISTRIBUTION (1) Department File, Guard's File</div>
		<br/>
		<div style='width:100%; font-size:7px; text-align:right'>Early Dismissal Form [06-2014]</div>
	</div>
</body>";
$pdf->WriteHTML($info);

$pdf->Output();
// end of file
?>
