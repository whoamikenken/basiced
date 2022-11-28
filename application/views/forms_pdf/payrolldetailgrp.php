<?php

/**
 * @author Justin
 * @copyright 2015
 * GOOD LUCK!
 */
 
include "application/config/connection.php";include "application/views/forms_pdf/css/css_payrolldetail.php";include "application/views/forms_pdf/content/payrolldetail_contentgrp.php";include "application/views/forms_pdf/function/payrolldetailfuncgrp.php";
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
$tarr    = array();
$tsalary = $inc = 0;
$query = getRecord($eid,$schedule,$quarter,$dfrom,$dto,$dept);
while($data = mysql_fetch_array($query)){
    $eid = $data['employeeid'];
    list($content,$total) = content($data['fullname'],getEmpDesc($eid),$data['salary'],$eid,$schedule,$quarter,$dfrom,$dto,$tarr,$inc); 
    $html       .= $content;
    $tarr[$inc]  = $total;
    $tsalary    += $data['salary'];
    $inc++;
}
$tsumarr = computetotal($tarr);
$html   .= total($tsalary,$tsumarr);
$html   .= '
            </tr>  
           ';
$html .= "</table></div>";
$mpdf->WriteHTML($html);
$mpdf->Output();
