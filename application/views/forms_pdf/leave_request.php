<?php
//============================================================+
// File name   : leave_request.php
// Begin       : 2014-08-29
// Last Update : 2014-08-30
//
// Description : Generates Leave Request Form
//               
// Author: Melvin Cobar Empleo
//
//============================================================+
/**
* @author Melvin Cobar Empleo
* @copyright 2014
*/

// create new PDF document
$pdf = new PdfCreator_tcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('SAINT JUDE CATHOLIC SCHOOL');
$pdf->SetTitle('Leave Request Form');
$pdf->SetSubject('Leave Request Form');
$pdf->SetKeywords('Leave, Request, Form');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
//$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Add a page
$pdf->AddPage();

$blank = "__________________________";
$blank2 = "______________________________________________";
$blank3 = "__________________";
$space1 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$space2 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$space3 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
$space4 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

$checkbox1 = '<img src="'.dirname(__FILE__) .'/img/checkbox.png"  width="20" height="15">';
$tblLeaveChoices = "
<table cellspacing='2' border='1'>
	<tr>
		<td>".$checkbox1."&nbsp;&nbsp;&nbsp;&nbsp;Maternity*</td><td>".$checkbox1."&nbsp;&nbsp;&nbsp;&nbsp;Sickness*</td><td>".$checkbox1."&nbsp;&nbsp;Personal&nbsp;&nbsp;". $blank3."</td>
	</tr>
	<tr>
		<td>".$checkbox1."&nbsp;&nbsp;&nbsp;&nbsp;Paternity*</td><td>".$checkbox1."&nbsp;&nbsp;&nbsp;&nbsp;Study/Professional Growth</td><td>".$checkbox1."&nbsp;&nbsp;Others&nbsp;&nbsp;&nbsp;&nbsp;". $blank3."</td>
	</tr>
</table>";

$headerCenter = array(
	array(
		'headerVal' => "SAINT JUDE CATHOLIC SCHOOL",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 12,
		'headerAlignment' => "C",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "C",
		'nextLine' => true
	),
	array(
		'headerVal' => "OFFICE OF THE HUMAN RESOURCE",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "C",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "C",
		'nextLine' => true
	),
	array(
		'headerVal' => "<u>LEAVE REQUEST FORM</u>",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "C",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "C",
		'nextLine' => true
	),
	array(
		'headerVal' => $blank,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 9,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => "Date" . $space1,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 9,
		'headerAlignment' => "R",
		'nextLine' => true
	),	
	array(
		'headerVal' => "Name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;" . $blank2,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "Department&nbsp;:&nbsp;" . $blank2,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "Designation&nbsp;:&nbsp;" . $blank2,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "<i>Kindly fill in the necessary information<i>",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "Reason for leave <i>(please check one):</i>",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => $tblLeaveChoices,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "<i>* Kindly attach supporting documents (medical certificates, medical records, etc.)<i>",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 12,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "Number of Days Needed/Entitled&nbsp;:&nbsp;".$blank,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "Leave Starts On&nbsp;:&nbsp;".$blank . "&nbsp;Reports Back to Work On&nbsp;:&nbsp;".$blank,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "Tasks/Duties that may be affected by the leave:",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 10,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "<hr>",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "<hr>",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "<hr>",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "<hr>",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => $blank,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => "SIGNATURE OVER PRINTED NAME&nbsp;&nbsp;&nbsp;",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 9,
		'headerAlignment' => "R",
		'nextLine' => true
	),	
	array(
		'headerVal' => "Employee" . $space1,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 9,
		'headerAlignment' => "R",
		'nextLine' => true
	),	
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => "Noted by:",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 9,
		'headerAlignment' => "L",
		'nextLine' => false
	),
	array(
		'headerVal' => "Approved by:" . $space2,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 9,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => $blank,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "L",
		'nextLine' => false
	),
	array(
		'headerVal' => $blank,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 11,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => "&nbsp;&nbsp;SIGNATURE OVER PRINTED NAME",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 9,
		'headerAlignment' => "L",
		'nextLine' => false
	),
	array(
		'headerVal' => "SIGNATURE OVER PRINTED NAME&nbsp;&nbsp;&nbsp;",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 9,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => $space3."Department/Unit Head",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 9,
		'headerAlignment' => "L",
		'nextLine' => false
	),
	array(
		'headerVal' => "Division Head" . $space4,
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 9,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => " ",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 14,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => " ",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 14,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => "DISTRIBUTION: (1) Department File, (2) OHR File",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 8,
		'headerAlignment' => "L",
		'nextLine' => true
	),
	array(
		'headerVal' => "",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 12,
		'headerAlignment' => "R",
		'nextLine' => true
	),
	array(
		'headerVal' => "Leave Request Form [06-2014]",
		'headerFontFamily' => "helvetica",
		'headerFontSize' => 8,
		'headerAlignment' => "R",
		'nextLine' => true
	),
);

foreach ($headerCenter as $key => $head) {
	$pdf->SetFont($head["headerFontFamily"] , '', $head["headerFontSize"] , '', true);
	//$pdf->writeHTMLCell(0, 0, '', '', $head["headerVal"], 0, 1, 0, true, $head["headerAlignment"], true);
	$pdf->writeHTML($head["headerVal"], $head["nextLine"], 0, 1, 0, $head["headerAlignment"]);
}

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('Leave Request.pdf', 'I');

// end of file