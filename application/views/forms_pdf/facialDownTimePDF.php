<?php
// Kennedy

require_once(APPPATH."constants.php");

$cdata = $result;

$extracol = "";
$cdata = $this->reports->getFacialDowntimeData();   
// echo"<pre>";print_r($cdata);die;
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
$info .= "
<htmlpageheader name='Header'>
   <div>
        <table width='60%'  >
            <tr>
                <td rowspan='5' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 70px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='50%'><span style='font-size: 12px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 10px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>Facial Downtime Report </strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 11px;' width='55%'>As of ".date("F Y")."</span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 10px;' width='55%'><strong>".$officeHeader."</strong></span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>";
$empcount = 1;
$counter=0;
$info .= "

<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
            <tr style='background-color: #000000;'>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>DEVICE ID</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>DEVICE NAME</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>SCHOOL</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>STATUS</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>TIME</th>
            ";
$info .= "</thead>";
$info .= "<tbody>";
        $color="";
        $display= "";
            foreach($cdata as $emp){
                
                        if ($emp->status == "Reconnected") $color = "Green";
                        else if($emp->status == "Disconnected") $color = "Red";

                    $info .= "<tr>
                            <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp->deviceKey."</td>
                            <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp->name."</td>
                            <td style='padding: 2px;text-align: center;font-size: 13px;'>PTI</td>
                            <td style='padding: 2px;text-align: center;font-size: 13px;color:".$color."'>".$emp->status."</td>
                            <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp->timestamp."</td>";
                        
$info .= "</tr>";
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



