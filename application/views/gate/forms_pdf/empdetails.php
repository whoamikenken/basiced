<?php
//============================================================+
// File name   : empdetails.php
// Begin       : 2014-09-09
// Last Update : 2014-09-09
//
// Description : Employee Details Form
//               
// Author: Melvin Cobar Empleo
//
//============================================================+
/**
* @author Melvin Cobar Empleo
* @copyright 2014
*/


$res = $this->employee->loadallemployee(array("employeeid"=>$id));
$detail = $res[0];
$pdf = new PdfCreator_mpdf();
$pdf->Bookmark('Start of the document');

$type = $this->extras->showemployeetype();
$position = $this->extras->showPostion();
$management = $this->extras->showManagement();
$department = $this->extras->showdepartment();
$empstatus = $this->extras->showemployeestatus();
$gender = $this->extras->showgender();
$citizenship = $this->extras->showCitizenship();
$religion = $this->extras->showReligion();
$nationality = $this->extras->showNationality();

$info = "
<body style='font-family:calibri; font-size:13px;'>	
	<div style='width:81%; float:left; text-align:center; font-size:20px; font-weight:bold; padding-top: 7%;'>
		".strtoupper($detail["lname"] . ", " . $detail["fname"] . " " . $detail["mname"])."
	</div>
	<div style='width:18%;float:right;'>
		<div style='border:#000 2px solid; width:120px; height:120px; '></div>
	</div>
	<div style='width:100%;'>
		<table width='100%'>
			<tr><th colspan='2' align='left'>EMPLOYEE INFORMATION</th></tr>
			<tr><td width='20%'>Employee ID:</td><td>".$detail["employeeid"]."</td></tr>
			<tr><td>Employee Type:</td><td>".$type[$detail["emptype"]]."</td></tr>
			<tr><td>Shift Schedule:</td><td>".$detail["empshift"]."</td></tr>
			<tr><td>Position:</td><td></td></tr>
			<tr><td>Management Level:</td><td></td></tr>
			<tr><td>Department:</td><td>".$department[$detail["deptid"]]."</td></tr>
			<tr><td>Employee Status:</td><td></td></tr>
			<tr><th colspan='2' align='left'>&nbsp;</th></tr>
			<tr><th colspan='2' align='left'>IDENTIFICATION NUMBERS</th></tr>
			<tr><td>TIN #:</td><td>".$detail["tin"]."</td></tr>
			<tr><td>SSS #:</td><td>".$detail["sss"]."</td></tr>
			<tr><td>PhilHealth:</td><td>".$detail["philhealth"]."</td></tr>
			<tr><td>PAG-IBIG:</td><td>".$detail["pagibig"]."</td></tr>
			<tr><td>PERAA:</td><td>".$detail["peraa"]."</td></tr>
			<tr><td>Medicare:</td><td>".$detail["medicare"]."</td></tr>
			<tr><th colspan='2' align='left'>&nbsp;</th></tr>
			<tr><th colspan='2' align='left'>PERSONAL INFORMATION</th></tr>
			<tr><td>Birthdate:</td><td>".date("M-d-Y",strtotime($detail["bdate"]))."</td></tr>
			<tr><td>Birthplace:</td><td>".$detail["bplace"]."</td></tr>
			<tr><td>Gender:</td><td>".$gender[$detail["gender"]]."</td></tr>
			<tr><td>Citizenship:</td><td>".$citizenship[$detail["citizenship"]]."</td></tr>
			<tr><td>Religion:</td><td>".$religion[$detail["religion"]]."</td></tr>
			<tr><td>Nationality:</td><td>".$nationality[$detail["nationality"]]."</td></tr>
			<tr><th colspan='2' align='left'>&nbsp;</th></tr>
			<tr><th colspan='2' align='left'>CONTACT INFORMATION</th></tr>
			<tr><td>Current Address:</td><td>".$detail["cityaddr"]."</td></tr>
			<tr><td>Permanent Address:</td><td>".$detail["permanentaddress"]."</td></tr>
			<tr><td>Mobile Number:</td><td>".$detail["mobile"]."</td></tr>
			<tr><td>Home Number:</td><td>".$detail["citytelno"]."</td></tr>
			<tr><td>Email Address:</td><td>".$detail["email"]."</td></tr>
			<tr><th colspan='2' align='left'>&nbsp;</th></tr>
			<tr><th colspan='2' align='left'>CONTACT PERSON IN CASE OF EMERGENCY</th></tr>
			<tr><td>Name:</td><td>".$detail["cp_name"]."</td></tr>
			<tr><td>Relationship:</td><td>".$detail["cp_relation"]."</td></tr>
			<tr><td>Address:</td><td>".$detail["cp_address"]."</td></tr>
			<tr><td>Mobile Number:</td><td>".$detail["cp_mobile"]."</td></tr>
			<tr><td>Home Number:</td><td>".$detail["cp_telno"]."</td></tr>
		</table>
	</div>
</body>";
$pdf->WriteHTML($info);

$pdf->Output();
// end of file
?>


