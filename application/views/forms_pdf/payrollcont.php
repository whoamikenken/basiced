<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
include "application/config/connection.php";
include "application/views/forms_pdf/css/css_payrolldetail.php";
include "application/views/forms_pdf/content/payrollcont_content.php";
include "application/views/forms_pdf/function/payrollcontfunc.php";
include "application/views/forms_pdf/function/payrollfunc.php";
$mpdf = new mPDF('utf-8','A4-L','10','','10','10','10','10','9','9');

$eid        = $_GET['eid']; 
$dept       = $_GET['dept'];
$dfrom      = $_GET['dfrom'];
$dto        = $_GET['dto'];
$schedule   = $_GET['schedule'];
$quarter    = $_GET['quarter']; 

$html = css().'
<htmlpageheader name="Header">
'.head($eid,$schedule,$quarter,$dfrom,$dto,$dept).'
</htmlpageheader>

<htmlpagefooter name="Footer">
<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; 
    color: #000000; font-weight: bold; font-style: italic;"><tr>
    <!--<td width="33%" align="right" style="font-weight: bold; font-style: italic; font-size: 10;">{PAGENO}/{nbpg}</td>-->
    </tr></table>
</htmlpagefooter>

';

$query = getRecord($eid,$schedule,$quarter,$dfrom,$dto,$dept);
$data = mysql_fetch_array($query);
$html .= content($data['fullname'],getEmpDesc($eid),$data['salary'],$eid,$schedule,$quarter,$dfrom,$dto);


$mpdf->WriteHTML($html);

$mpdf->Output();
