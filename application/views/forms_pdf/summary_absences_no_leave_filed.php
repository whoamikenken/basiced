<?php
/**
* @author justin (with e)
* @copyright 2018
*/

set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set("memory_limit",-1);

$pdf = new mpdf('utf-8','LETTER-L','','UTF-8',5,5,5,5);

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
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>Summary of Absences (No leave filed)</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>As of ".date('F Y')."</strong></span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>";

$info .= "
<div class='content'>
    <div class='content-header'>
    	<table border=1 width='100%' style='font-size: 9px;' id='datas'>
    		<thead>
    			<tr style='background-color: #0099FF'>
	                <td align='center' style='width: 20%;' rowspan='3'>NAME</td>
";

$th_absences = $th_no_leave_filed = "";
foreach ($month_list as $key => $caption) {
	$info .= "
					<td align='center' style='border-bottom: 0;'>". $caption ."</td>
					<td align='center' style='width: 5%;' rowspan='3'>NLF Total</td>
	";
	
	$th_absences .= "
					<td align='center' style='border-top: 0;border-bottom: 0;'>ABSENCES</td>
	";	
	
	$th_no_leave_filed .= "
					<td align='center' style='border-top: 0;'>No Leave filed</td>
	";	
}    	

$info .= "
				</tr>
				<tr style='background-color: #0099FF'>
					". $th_absences ."
				</tr>
				<tr style='background-color: #0099FF'>
					". $th_no_leave_filed ."
				</tr>
			</thead>
			<tbody>
";

foreach ($list as $employeeid => $emp_info) {
	$row_td = "";
    $is_display = 0;

	foreach ($month_list as $month => $caption):
		$td_month = "";
		$td_total_month = 0;
		
		foreach ($emp_info["absences_list"] as $date => $value) {
			if($month == date("m", strtotime($date)) && $value > 0){
				$td_month .= date("d", strtotime($date)) ." ". $value ."; ";
				$td_total_month += $value;
			}
		}

		$row_td .= "
                    <td align='center'>". $td_month ."</td>
					<td align='center'>". $td_total_month ."</td>
		";

        $is_display += $td_total_month;
	endforeach;

    if($is_display){
        $info .= "
                <tr>
                    <td>". $emp_info["name"] ."</td>
                    ". $row_td ."
                </tr>
        ";
    }
}

$info .= "
			</tbody>
		</table>    	
    </div>
</div>
";

$pdf->WriteHTML($info);
$pdf->Output();