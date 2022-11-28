<?php

/**
 * @author Justin
 * @copyright 2014
 */
include "application/config/connection.php";
                                                                                                                                                                                        include "application/views/forms_pdf/css/css_daily.php";
                                                                                                                                                                                        include "application/views/forms_pdf/content/daily_library_content.php";
$mpdf = new mPDF('utf-8','A4-L','10','','5','5','15','15','9','9');
$dailydate = $_GET['dailydate'];

$html = css().'
<htmlpageheader name="Header">
'.head($dailydate).'
</htmlpageheader>

<htmlpagefooter name="Footer">
<table width="100%" style="vertical-align: bottom; font-family: serif; font-size: 8pt; 
    color: #000000; font-weight: bold; font-style: italic;"><tr>
    <td width="33%" align="right" style="font-weight: bold; font-style: italic; font-size: 10;">{PAGENO}/{nbpg}</td>
    </tr></table>
</htmlpagefooter>

'.content($dailydate);

$mpdf->WriteHTML($html);

$mpdf->Output();
