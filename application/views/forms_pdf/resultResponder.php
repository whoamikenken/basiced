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

$pdf = new mpdf('utf-8','LETTER','','UTF-8',5,5,8,8,9,2);
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
foreach ($cdata as $key => $value) {
    // echo "<pre>";print_r(explode("/", substr($value['answer'], 1)));die;
    $gender = "";
    $gender = ($value['gender'] == "F")? "FEMALE":"MALE";

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
        <h1 style='text-align:center'>".$value['description']."</h1>
        <ul style='list-style-type:none;font-size: 19px;margin-top:8%;line-height: 2.4;'>
            <li>
            <b>NAME:</b> ".$value['fullname']."
            </li>
            
            <li>
            <b>RESPONSE DATE:</b> ".date("Y/m/d h:i:s a", strtotime($value['date_created']))."
            </li>
            
            <li>
            <b>GENDER:</b> ".$gender."
            </li>
            
            <li>
            <b>AGE:</b> ".$value['age']."
            </li>
            
            <li>
            <b>DEPARTMENT:</b> ".$value['department']."
            </li>
        </ul>
    <div class='col-md-12'>
    <ol style='font-size:14px;line-height:2.4'>";

foreach (explode("/", substr($value['answer'], 1)) as $row => $val) {
        $dataInfo = explode("*", $val);
        if ($dataInfo[0] == "YN") {
            $CheckNo = "";
            $CheckYes = "";
            $CheckNo = ($dataInfo[1] == "No")? "checked='checked'":"";
            $CheckYes = ($dataInfo[1] == "Yes")? "checked='checked'":"";
           $info .= "
            <li>".$dataInfo[2]." <br> 
                <label class='btn btn-success active'>
                    <input type='checkbox' val='Yes' autocomplete='off' ".$CheckYes."> Yes
                    </label>
                    <label class='btn btn-danger '>
                    <input type='checkbox' val='No' autocomplete='off' ".$CheckNo."> No
                </label>
            </li>
            ";
        }else{
            $info .= "
            <li>".$dataInfo[2]." <br> 
                ".$dataInfo[1]."
            </li>
            ";
        }
}
    $info .= "
    </ol>";
    $info .= "
        <htmlpagefooter name='Footer'>
            <br>
            <div class='footer'>
                Page : {PAGENO} of {nb}
            </div>
        </htmlpagefooter>
    ";
}

$pdf->WriteHTML($info);

$pdf->Output();
?>



