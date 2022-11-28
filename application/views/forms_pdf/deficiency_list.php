<?php

/**
 * @author Justin
 * @copyright 2016
 */
require_once(APPPATH."constants.php");
$division = $dept = $concerned_dept = $status = "";
$division  = isset($division) ? $division : '';
$dept    = isset($department) ? $department : '';
$office    = isset($office) ? $office : '';
$concerned_dept       = isset($cdepartment) ? $cdepartment : '';
$statusComplete        =  isset($statusComplete) ? $statusComplete : '';
$statusIncomplete        = isset($statusIncomplete) ? $statusIncomplete : '';

if($statusComplete && $statusIncomplete)
{
	$status = "Both";
}
else if($statusComplete && !$statusIncomplete)
{
	$status = $statusComplete;
}
else if(!$statusComplete && $statusIncomplete)
{
	$status = $statusIncomplete;
}
else
{
	$status = "";
}

if($depthead != 0){
    $concerned_dept = $dept;
    unset($dept);
} 
$title="";
// $notif        = ($_GET['notif'] ? $_GET['notif'] : $_POST['notif']);
if(isset($notif)){
    $empDef = $this->employeemod->employeedeficiencynotif('','','','',true)->result();
    $title = "LIST OF CLEARANCE UPDATES REPORT";
}else{
    $utype = $this->session->userdata('usertype');
    if($utype == 'EMPLOYEE'){
        $empDef = $this->employeemod->employeedeficiencynotif($division,$dept,$concerned_dept,$status,$notif, false, $office)->result();
        $title = "LIST OF CLEARANCE REPORT";
    }else{
        $empDef = $this->employeemod->employeedeficiencynotifs($division,$dept,$concerned_dept,$status,$notif,$office)->result();
        $title = "LIST OF CLEARANCE REPORT";
    }
        
}
// $empDef = $this->employeemod->employeedeficiencynotif('','','','',true)->result();

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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>".$title."</strong></span></td>
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
        <table border=1 width='100%' style='font-size: 9px;'>
            <tr style='background:black;'>
				<th style='color: yellow;text-align: center;font-size: 12px;font-weight: bold;padding: 5px;'>Employee ID</th>
				<th style='color: yellow;text-align: center;font-size: 12px;font-weight: bold;padding: 5px;'>Employee Name</th>
				<th style='color: yellow;text-align: center;font-size: 12px;font-weight: bold;padding: 5px;'>Concerned Department</th>
				<th style='color: yellow;text-align: center;font-size: 12px;font-weight: bold;padding: 5px;'>Remarks</th>
				<th style='color: yellow;text-align: center;font-size: 12px;font-weight: bold;padding: 5px;'>Submission Date</th>
            </tr>";
                foreach($empDef as $row){
$info .= "
            <tr>
					<td>".$row->employeeid."</td>
					<td>".$row->fullname."</td>
					<td>".$row->department."</td>
					<td>".$row->deficiency_desc."</td>
					<td>".$row->submission_date."</td>";
				
                
				
				
$info .= "  
            </tr>      
         ";
                }		
$info .= "      
        </table>
    </div>
</div>";



$pdf->WriteHTML($info);

$pdf->Output();
?>



