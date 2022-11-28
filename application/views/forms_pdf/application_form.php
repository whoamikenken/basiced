<?php
//============================================================+
// File name   : application_form.php
// Begin       : 2014-09-02
// Last Update : 2014-09-05
//
// Description : Application Form
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
<body style='font-family:calibri; font-size:11px;'>   
    <table width='100%' style='font-size:10px;'>
        <tr>
            <td><strong>(&nbsp;&nbsp;&nbsp;)TEACHING</strong></td>
            <td>Department:</td>
            <td><strong>HSD&nbsp;&nbsp;&nbsp;ED&nbsp;&nbsp;&nbsp;ECD</strong></td>
            <td>Subject: ".$blank2.$blank1."</td>
            <td><strong>&nbsp;&nbsp;(&nbsp;&nbsp;&nbsp;)NON TEACHING</strong></td>
            <td>Department: ".$blank2.$blank1."</td>
        </tr>
    </table>

    <div style='font-style:italic; font-weight:bold; font-size:9px; text-align:justify; padding-top:3px;'>
        Fill out this form carefully and print all information requested. 
        Only application forms correctly and completely filled out will be accepted.
    </div>

    <div style='border: #000 2px solid; font-size:10px; padding:3px;'>
        <table style='width:100%; font-size:10px;'>
            <tr>
                <td width='5%'>Name:</td>
                <td align='left'>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank1."___</td>
            </tr>
        </table>
            <table align='right' width='90%' style='width:100%; font-size:7px; text-align:center'>
                <tr>
                    <td>(Last)</td><td>(First)</td><td>(Middle)</td>
                </tr>
            </table>
        <table style='width:100%; font-size:10px;'>
            <tr>
                <td width='6%'>Address:</td>
                <td align='left'>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank1."</td>
            </tr>
        </table>
        <div style='width:100%;'>
            <table width='100%'  style='font-size:10px;'>
                <tr>
                    <td width='2%'>Sex: </td><td>".$blank2.$blank2."</td>
                    <td width='10%' align='right'>Citizenship: </td><td>".$blank2.$blank2.$blank1."</td>
                    <td align='right'>Religion/Faith: </td><td>".$blank2.$blank2."</td>
                </tr>
                <tr>
                    <td>Age: </td><td>".$blank2.$blank2."</td>
                    <td align='right'>Date of Birth: </td><td>".$blank2.$blank2.$blank1."</td>
                    <td align='right'>Place of Birth: </td><td>".$blank2.$blank2."</td>
                </tr>
            </table>
            <table width='100%'  style='font-size:10px;'>
                <tr>
                    <td width='10%'>LandLine No.:</td><td align='left'>".$blank2.$blank2."___</td>
                    <td style='text-align:right; width:8%;' >Cell No.: </td><td>".$blank2.$blank2."</td>
                    <td align='right'>Email: </td><td>".$blank2.$blank2."____</td>
                </tr>
            </table>
        </div>
    </div>
    <div style='border: #000 2px solid; font-size:10px; padding:3px; margin-top:3px;'>
        <table width='75%' style='font-size:10px;'>
            <tr>
                <td>Marital Status: </td>
                <td>[&nbsp;&nbsp;&nbsp;]Single</td>
                <td>[&nbsp;&nbsp;&nbsp;]Married</td>
                <td>[&nbsp;&nbsp;&nbsp;]Separated</td>
                <td>[&nbsp;&nbsp;&nbsp;]Widow</td>
            </tr>
        </table>
        <table width='100%' style='font-size:10px;'>
            <tr>
                <td width='13%'>Name of Spouse:</td>
                <td align='left'>".$blank2.$blank2.$blank2."</td>
                <td align='right'>Occupation of Spouse:</td>
                <td>".$blank2.$blank2.$blank2."</td>
            </tr>
        </table>
        <table width='100%' style='font-size:10px;'>
            <tr>
                <td width='10%'>Date Married:</td>
                <td align='left'>".$blank2.$blank2.$blank1."</td>
                <td align='right'>No. of Children:</td>
                <td>".$blank2."</td>
                <td align='right'>Their Ages:</td>
                <td>".$blank2.$blank2.$blank1."</td>
            </tr>
        </table>
        <table width='100%' style='font-size:10px;'>
            <tr>
                <td width='19%'>Name of Nearest Relative:</td>
                <td align='left'>".$blank2.$blank2.$blank2."___</td>
                <td align='right'>Affiliation:</td>
                <td>".$blank2.$blank2.$blank2.$blank1."</td>
            </tr>
        </table>
        <table width='100%' style='font-size:10px;'>
            <tr>
                <td>Address:</td>
                <td align='left'>".$blank2.$blank2.$blank2.$blank2.$blank2."</td>
                <td align='right'>Contact No.:</td>
                <td>".$blank2.$blank2.$blank1."</td>
            </tr>
        </table>
    </div>
    <div style='border: #000 2px solid; font-size:11px; padding:3px; margin-top:3px;'>
        <div style='font-weight:bold;'>EDUCATIONAL BACKGROUND</div>
        <table style='width:100%;font-size:10px;' cellpadding='2px'>
            <tr>
                <td rowspan='2' style='text-align:center; width:12%; border: #000 1px solid;'></td>
                <td rowspan='2' style='text-align:center; width:28%; border: #000 1px solid;'>Name of Institution</td>
                <td rowspan='2' style='text-align:center; width:15%; border: #000 1px solid;'>Location</td>
                <td colspan='2' style='text-align:center; border: #000 1px solid;'>Years Attended</td>
                <td rowspan='2' style='text-align:center; border: #000 1px solid;'>Degrees/Units Earned <br>(State in Full)</td>
            </tr>
            <tr>
                <td style='text-align:center; width:9%; border: #000 1px solid;'>From</td>
                <td style='text-align:center; width:9%; border: #000 1px solid;'>To</td>
            </tr>
            <tr>
                <td style='border: #000 1px solid;'>Elementary</td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
            </tr>
            <tr>
                <td style='border: #000 1px solid;'>Secondary</td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
            </tr>
            <tr>
                <td style='border: #000 1px solid;'>Collegiate</td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
            </tr>
            <tr>
                <td style='border: #000 1px solid;'>Masteral</td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
                <td style='border: #000 1px solid;'></td>
            </tr>
        </table>
        <table width='100%'>
            <tr>
                <td width='29%'>Distinctions/Honors/Awards Received:</td>
                <td align='left'>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank1."_______</td>
            </tr>
        </table>
        <table width='100%'>
            <tr>
                <td width='23%'>Certifications/Special Studies:</td>
                <td>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank2."_______</td>
            </tr>
        </table>
        <table width='100%'>
            <tr>
                <td width='10%'>Other Skills:</td>
                <td align='left'>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank1."</td>
            </tr>
        </table>

        <div style='font-weight:bold;'>Civil Service/Licensure Examination Passed:</div>

        <table style='width:100%;font-size:10px;' cellpadding='2px'>
            <tr>
                <td style='text-align:center; width:70%; border: #000 1px solid;'>Name of Examination</td>
                <td style='text-align:center; width:17%; border: #000 1px solid;'>Date</td>
                <td style='text-align:center; width:12%; border: #000 1px solid;'>Rating</td>
            </tr>
            <tr>
                <td style='text-align:center; width:70%; border: #000 1px solid;'>&nbsp;</td>
                <td style='text-align:center; width:17%; border: #000 1px solid;'>&nbsp;</td>
                <td style='text-align:center; width:12%; border: #000 1px solid;'>&nbsp;</td>
            </tr>
            <tr>
                <td style='text-align:center; width:70%; border: #000 1px solid;'>&nbsp;</td>
                <td style='text-align:center; width:17%; border: #000 1px solid;'>&nbsp;</td>
                <td style='text-align:center; width:12%; border: #000 1px solid;'>&nbsp;</td>
            </tr>
            <tr>
                <td style='text-align:center; width:70%; border: #000 1px solid;'>&nbsp;</td>
                <td style='text-align:center; width:17%; border: #000 1px solid;'>&nbsp;</td>
                <td style='text-align:center; width:12%; border: #000 1px solid;'>&nbsp;</td>
            </tr>
        </table>
    </div>
    <div style='border: #000 2px solid; padding:3px; margin-top:3px;'>
        <div><strong>EMPLOYMENT HISTORY</strong> (Please list all positions separately with most recent job first)</div>
        <table style='width:100%; font-size:10px;'>
            <tr>
                <td>Institution:</td><td>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank1."</td>
                <td>Location:</td><td>".$blank2.$blank2.$blank1."</td>
            </tr>
        </table>
        <table style='width:100%; font-size:10px;'>
            <tr>
                <td width='10%'>Position Held:</td><td align='left'>".$blank2.$blank2.$blank1."___</td>
                <td align='right'>Period of Employment:</td><td>".$blank2.$blank1."___</td>
                <td align='right'>Monthly Salary:</td><td>".$blank2."___</td>
            </tr>
        </table>
        <table style='width:100%; font-size:10px;'>
            <tr>
                <td width='12%'>Responsibilities:</td><td align='left'>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2."___</td>
            </tr>
        </table>
        <table style='width:100%; font-size:10px;'>
            <tr>
                <td width='15%'>Reason for Leaving:</td><td align='left'>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank1."_____</td>
            </tr>
        </table>
        <br/>
        <table style='width:100%; font-size:10px;'>
            <tr>
                <td>Institution:</td><td>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank1."</td>
                <td>Location:</td><td>".$blank2.$blank2.$blank1."</td>
            </tr>
        </table>
        <table style='width:100%; font-size:10px;'>
            <tr>
                <td width='10%'>Position Held:</td><td align='left'>".$blank2.$blank2.$blank1."___</td>
                <td align='right'>Period of Employment:</td><td>".$blank2.$blank1."___</td>
                <td align='right'>Monthly Salary:</td><td>".$blank2."___</td>
            </tr>
        </table>
        <table style='width:100%; font-size:10px;'>
            <tr>
                <td width='12%'>Responsibilities:</td><td align='left'>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2."___</td>
            </tr>
        </table>
        <table style='width:100%; font-size:10px;'>
            <tr>
                <td width='15%'>Reason for Leaving:</td><td align='left'>".$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank2.$blank1."_____</td>
            </tr>
        </table>
    </div>
    <div style='margin-top:5px;'><strong>REFERENCES:</strong> (Please list all persons, positions and contact information)</div>    
    <table style='width:100%; text-align:center; font-size:8px;'>
        <tr><td>".$blank2.$blank2.$blank1."</td><td>".$blank2.$blank2.$blank1."</td><td>".$blank2.$blank2.$blank1."</td><td>".$blank2.$blank2.$blank1."</td></tr>
        <tr><td>Name of Reference Person</td><td>Position</td><td>Institution</td><td>Contact Number</td></tr>
        <tr><td>".$blank2.$blank2.$blank1."</td><td>".$blank2.$blank2.$blank1."</td><td>".$blank2.$blank2.$blank1."</td><td>".$blank2.$blank2.$blank1."</td></tr>
        <tr><td>Name of Reference Person</td><td>Position</td><td>Institution</td><td>Contact Number</td></tr>
    </table>
    <div style='text-align:center;'><strong>VERIFICATION</strong></div>    
    <div style='text-align:justify;'>
        I certify that the information in this application is true and complete to the best of my knowledge and understand that any false \
        information on this application may be grounds for not hiring me.
    </div>    
    <br/>
    <div style='width:40%; float:left; text-align:center;'>
        ".$blank2.$blank2.$blank2."<br/>
        Signature over Printed Name
    </div>
    <div style='width:40%; float:right; text-align:center;'>
        ".$blank2.$blank2.$blank2."<br/>
        Date
    </div>
</body>";
$pdf->WriteHTML($info);

$pdf->Output();
?>

