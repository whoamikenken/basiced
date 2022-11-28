<?php
// Kennedy

require_once(APPPATH."constants.php");
$result  = $this->facial->facialDevicePerson($serial); 
// echo "<pre>"; print_r($this->db->last_query()); die;
$cdata = $result;

$extracol = "";
        // echo "<pre>"; print_r($cdata); die;
$pdf = new mpdf('utf-8','LETTER-L','','UTF-8',5,5,8,8,9,2);
$info  = "  <style>
                @page{            
                    /*margin-top: 4.35cm;*/
                    margin-top: 3.15cm;
                    odd-header-name: html_Header;
                    odd-footer-name: html_Footer;
                }
                th{
                    color: black;
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
                <td rowspan='3' style='text-align: right;' width='60%'><img src='images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 13px;'><b>DEPARTMENT OF EDUCATION</b></span></td>
               <!-- 
                <td rowspan='3' style='text-align: left;'><img src='images/school_logo2.jpg' style='width: 60px;text-align: center;' /></td>-->
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>MAKATI DIVISION OFFICE</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>IMAGE SYNC REPORT</strong></span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>";


$info .= "

<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;' id='datas'>
            <thead>
            <tr style='background-color: gray;'>
            <th align='center' colspan=''>Person ID</th>
            <th align='center' colspan=''>Emplyoee ID</th>
            <th align='center' colspan=''>Name</th>
            <th align='center' colspan=''>School</th>
            <th align='center' colspan=''>Facial Feature #1</th>
            <th align='center' colspan=''>Facial Feature #2</th>
            <th align='center' colspan=''>Facial Feature #3</th>
            ";
$info .= "</thead><tbody>";
foreach($cdata as $emp){
    $face1 = $face2 = $face3 = "No Data";
    if($emp->facial_status1 == "Success" &&  $emp->FaceId1 != ""){
        $face1 = "<h4 style='color:green'>Success</h4>";
    }elseif($emp->facial_status1 == "Error" &&  $emp->FaceId1 != ""){
        $face1 = "<h4 style='color:red'>Failed</h4>";
    }elseif($emp->facial_status1 == "Pending" &&  $emp->FaceId1 != ""){
        $face1 = "<h4 style='color:blue'>Pending</h4>";
    }else{
        $desc = ($emp->facial_status1) ? "Success": "No Image";
        $face1 = "<h4 style='color:black'>". $desc."</h4>";
    }

    if($emp->facial_status2 == "Success" &&  $emp->FaceId2 != ""){
        $face2 = "<h4 style='color:green'>Success</h4>";
    }elseif($emp->facial_status2 == "Error" &&  $emp->FaceId2 != ""){
        $face2 = "<h4 style='color:red'>Failed</h4>";
    }elseif($emp->facial_status2 == "Pending" &&  $emp->FaceId2 != ""){
        $face2 = "<h4 style='color:blue'>Pending</h4>";
    }else{
        $desc = ($emp->facial_status2) ? "Success": "No Image";
        $face2 = "<h4 style='color:black'>" . $desc . "</h4>";
    }

    if($emp->facial_status3 == "Success" &&  $emp->FaceId3 != ""){
        $face3 = "<h4 style='color:green'>Success</h4>";
    }elseif($emp->facial_status3 == "Error" &&  $emp->FaceId3 != ""){
        $face3 = "<h4 style='color:red'>Failed</h4>";
    }elseif($emp->facial_status3 == "Pending" &&  $emp->FaceId3 != ""){
        $face3 = "<h4 style='color:blue'>Pending</h4>";
    }else{
        $desc = ($emp->facial_status3) ? "Success": "No Image";
        $face3 = "<h4 style='color:black'>" . $desc . "</h4>";
    }

$info .= "<tr>";
                    $info .= "
                            <td align='center'>".$emp->personId."</td>
                            <td align='center'>".$emp->employeeid."</td>
                            <td align='center'>".$emp->fullname. "</td>
                            <td align='center'>" .$emp->description . "</td>
                            <td align='center'>".$face1."</td>
                            <td align='center'>".$face2."</td>
                            <td align='center'>".$face3."</td>";
$info .= "</tr>";
                        }

$info .= "   </tbody>   
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



