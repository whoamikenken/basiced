<?php
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set("memory_limit",-1);

require_once(APPPATH."constants.php");
$inhouse_title = "Seminar Attendees";
$date = ltrim($inhouse_title,$seminartype);
$pdf = new mpdf('utf-8','LETTER-L','','UTF-8',5,5,8,8,9,2);
$info  = "  <style>
                @page{            
                    /*margin-top: 4.35cm;*/
                    margin-top: 3.15cm;
                    odd-header-name: html_Header;
                    odd-footer-name: html_Footer;
                }
                th{
                	color: yellow;
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

			    .footer{
			    	width: 100%;
			    	text-align: right;
			    }

            </style>";
if($seminartype){
    $info .= "
        <htmlpageheader name='Header'>
            <div>
                <table width='60%'  >
                    <tr>
                        <td rowspan='3' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 80px;text-align: center;' /></td>
                        <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='0%'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></td>
                       <!-- 
                        <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
                    </tr>
                    <tr>
                        <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>D`Great</strong></span></td>
                    </tr>
                    <tr>
                        <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>".$seminartype."</strong></span><br><span style='font-size: 10px;' width='55%'><strong>".$date."</strong></span><br><span style='font-size: 13px;' width='55%'><strong>".$inhouseAttendance."</strong></span></td>
                    </tr>
                </table>
            </div>
        </htmlpageheader>";
    }else{
       $info .= "
        <htmlpageheader name='Header'>
            <div>
                <table width='60%'  >
                    <tr>
                        <td rowspan='3' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 80px;text-align: center;' /></td>
                        <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='0%'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></td>
                       <!-- 
                        <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
                    </tr>
                    <tr>
                        <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>D`Great</strong></span></td>
                    </tr>
                    <tr>
                        <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>".$date."</strong></span><br><span style='font-size: 13px;' width='55%'><strong>".$inhouseAttendance."</strong></span></td>
                    </tr>
                </table>
            </div>
        </htmlpageheader>"; 
    }
    
$info .= "
<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
                <tr style='background-color: #000000;'>
                <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>EMPLOYEE ID</th>
                <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>FULLNAME</th>
                <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>LOGIN</th>
                <th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>LOGOUT</th>
                ";
                if($isgoing == 0) $info .="<th style='padding: 5px;text-align: center;font-size: 13px;font-weight: bold;'>REASON</th>";
$info .= "</thead>";
$info .= "<tbody class='inhouseAttendees'>";
            foreach ($attendedEmployee as $seminarid => $userid){
                foreach ($userid as $k => $v){
                    $login = $logout = '';
                    foreach ($v as $value){
                        $fullname = Globals::_e($value['fullname']);
                        $timein = date("h:i:s A", strtotime($value['localtimein']));
                        if($value['log_type'] == 'IN') $login = $timein;
                        else $logout = $timein;
                    }

                    $info .= "<tr>
                        <td>".$k."/td>
                        <td>".$fullname."</td>
                        <td>".$login."</td>
                        <td>".$logout."</td> 
                    </tr>";
                }
            }
$info .= "      
            </tbody>
        </table>
    </div>
</div>";
$info .= "
	<htmlpagefooter name='Footer'>
		<br>
		<div class='footer'>
			Page : {PAGENO} of {nb}
		</div>
	</htmlpagefooter>
";
$pdf->WriteHTML($info);

$pdf->Output();
?>



