
<?php
// Kennedy

require_once(APPPATH."constants.php");

$data = $this->reports->getLockUnlockData($deptid, $dateFrom, $dateTo, 'unlock'); 
// echo "<pre>"; print_r($this->db->last_query()); die;
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
                <td rowspan='4' style='text-align: right;' width='60%'><img src='".$imgurl."images/school_logo.jpg' style='width: 70px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='50%'><span style='font-size: 12px;'><b>Pinnacle Technologies Inc.</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='".$imgurl."images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 10px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong> Unlock Account History Report</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 11px;' width='55%'>As of ".date("F Y")."</span></td>
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
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold; width: 5%'>#</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold; width: 10%'>EMPLOYEE ID</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold; width: 15%'>EMPLOYEE NAME</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold; width: 25%'>DEPARTMENT</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold; width: 15%'>POSITION</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold; width: 15%'>UNLOCKED BY</th>
            <th style='padding: 5px;text-align: center;font-size: 11px;font-weight: bold; width: 15%'>DATE&nbsp;UNLOCKED</th></tr>";

$info .= "</thead>";
$info .= "<tbody class='seminartbl'>";
$counter = 1;
        foreach($data as $emp){
            $info .= "<tr>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$counter."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$emp->employeeid."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$this->extensions->getEmployeeName($emp->employeeid)."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$this->extensions->getDeparmentDescriptionReport($this->extras->getemployeecol($emp->employeeid,"deptid"))."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$this->extensions->getPositionDescription($this->extras->getemployeecol($emp->employeeid,"positionid"))."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".$this->extras->getAdminInfo($emp->updated_by)."</td>
                        <td style='padding: 2px;text-align: center;font-size: 11px;'>".date("F d, Y", strtotime($emp->timestamp))."</td>
                        </tr>";
            $counter++;
        }

$info .= "      
            </tbody>
        </table>
    </div>
</div>";
// echo "<pre>"; print_r($info); die;
$info .= "
    <htmlpagefooter name='Footer'>
        <br>
        <div class='footer'>
            Page : {PAGENO} of {nb}
        </div>
    </htmlpagefooter>
";
// echo $info;
// echo "<pre>"; print_r($span); echo "</pre>";
$pdf->WriteHTML($info);

$pdf->Output();
?>



