<?php
// Kennedy

require_once(APPPATH."constants.php");

$cdata = $result;

$extracol = "";
$category    = ($_GET['category'] != "" ? $_GET['category'] : $_POST['category']);
$survey    = ($_GET['survey'] != "" ? $_GET['survey'] : $_POST['survey']);
$type    = ($_GET['type'] != "" ? $_GET['type'] : $_POST['type']);
$employeeFilter    = ($_GET['employeeFilter'] != "" ? $_GET['employeeFilter'] : $_POST['employeeFilter']);
$deptid    = ($_GET['deptid'] != "" ? $_GET['deptid'] : $_POST['deptid']);

$cdata = $this->webcheckin->getResponseData($category,$survey,$type,$employeeFilter,$deptid, true);   

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
                <td rowspan='3' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>As of ".date("Y/m/d")."</strong></span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>";
$info .= "

<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
            <tr style='background-color: #000000;'>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>EMPLOYEEID</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>NAME</th>
            <th style='padding: 5px;text-align: center;font-size: 20px;font-weight: bold;'>DATE</th>
            ";
$info .= "</thead>";
$info .= "<tbody>";
        $dept="";

            foreach($cdata as $emp){
                        // if($dept != $emp['description'])
                        // {
                        //     $dept = $emp['description'];
                        //     $description = $emp['description'];
                        //     if ($emp['description'] == "") {
                        //         $description = "No Deparment";
                        //     }
                        //     $info .= "<tr>
                        //             <td style='padding: 5px;text-align: left;font-size: 20px;font-weight: bold;' colspan='3'>".$description."</td>
                        //         </tr>";
                        // }
                    $info .= "<tr>
                            <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp['employeeid']."</td>
                            <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp['fullname']."</td>
                            <td style='padding: 2px;text-align: center;font-size: 13px;'>".$emp['date_created']."</td>";
                        
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



