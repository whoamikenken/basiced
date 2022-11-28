<?php
/**
* @author justin (with e)
* @copyright 2018
*/

$pdf = new mpdf('utf-8','LETTER-L','9','','10','10','35','8','5','2');
$info  = "  <style>
                @page{            
                    /*margin-top: 4.35cm;*/
                    margin-top: 3.15cm;
                    odd-header-name: html_Header;
                    odd-footer-name: html_Footer;
                }  
                .content{
                    height: 100%;
                    margin-top: 15px;
                }
                table{
                    border-collapse: collapse;
                    font-size: 12px;
                    border-spacing: 5px;
                }
                .content-header{
                    text-align: center;
                    font-size: 12px;
                }
                .content-body{
                    border: 1px solid black;
                    padding-top: 8px;
                    padding-bottom: 8px;
                    padding-left: 8px;
                }
                   #datas tr:nth-child(odd)
                {
                    background-color:#C8C8C8;
                }

                #datas thead tr td
                {
                    color: yellow;
                }
                .footer{
                    width: 100%;
                    text-align: right;
                }
            </style>";

$info .= "
<htmlpageheader name='Header'>
     <div>
        <table width='60%'  >
            <tr>
                <td rowspan='3' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>".date('F j, Y', strtotime($dfrom))." - ".date('F j, Y', strtotime($dto))." </strong></span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>
";

$info .= "
<div class='content'>
    <div class='content-header'>
    	<table border=1 width='100%' style='font-size: 9px;' id='datas'>
    		<thead>
    			<tr style='background-color: black'>
	                <td align='center' style='width: 25%; font-weight: bold;'>EMPLOYEE NAME</td>
	                <td align='center' style='width: 10%; font-weight: bold;'>TYPE</td>
	                <td align='center' style='width: 15%; font-weight: bold;'>POSITION</td>
	                <td align='center' style='width: 10%; font-weight: bold;'>DEPARTMENT</td>
	                <td align='center' style='width: 15%; font-weight: bold;'>INCLUSIVE DATE</td>
	                <td align='center' style='width: 10%; font-weight: bold;'>DATE APPLIED</td>
	                <td align='center' style='width: 15%; font-weight: bold;'>REASON</td>
	            </tr>
	        </thead>
	        <tbody>
";

foreach ($emp_list as $row) {
	$info .= "
				<tr>
					<td>". $row["fullname"] ."</td>
					<td align='center'>". $row["type"] ."</td>
					<td>". $row["position"] ."</td>
					<td align='center'>". $row["department"] ."</td>
					<td align='center'>". $row["date_exclusive"] ."</td>
					<td align='center'>". $row["time_requested"] ."</td>
					<td>". $row["reason"] ."</td>
				</tr>
	";
}

$info .= "
	        </tbody>
	    </table>
	</div>
</div>
";

$info .= "
    <htmlpagefooter name='Footer'>
        <br>
        <div class='footer'>
            Page : {PAGENO} of {nb}
        </div>
    </htmlpagefooter>
";
$info = mb_convert_encoding($info, 'UTF-8', 'UTF-8');
$pdf->WriteHTML($info);
$pdf->Output();