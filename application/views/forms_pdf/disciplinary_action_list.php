<?php
require_once(APPPATH."constants.php");
$CI =& get_instance();
$CI->load->model('disciplinary_action');

// $division = $dept = $offenseType = $dfrom = $dto = "";
$division  =isset($division)? $division : '';
$dept    =  isset($department)? $department : '';
$offenseType       =  isset($offenseType)? $offenseType : '';
$dfrom        =  isset($dfrom)? $dfrom : '';
$dto        =  isset($dto)? $dto : '';
$status        =  isset($status)? $status : '';

$empDisciplinaryActionList = $CI->disciplinary_action->employeeDisciplinaryActionList($division,$dept,$offenseType,$dfrom,$dto,$status)->result();
// echo "<pre>"; print_r($this->db->last_query()); die;

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

                th{
                    color: yellow;
                    font-weight: bold;
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
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 15px;' width='55%'><strong>LIST OF EMPLOYEE WITH DISCIPLINARY ACTION</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center'><span style='font-size: 10px;'><strong>From ".date("F d, Y",strtotime($dfrom))." to ".date("F d, Y",strtotime($dto))."</strong></span></td>
            </tr>
        </table>
    </div>
</htmlpageheader>";
$info .= "
<div class='content'>
    <div class='content-header'>
        <table border=1 width='100%' style='font-size: 9px;'>
            <tr style='background-color: black;' >
				<th>Employee ID</th>
				<th>Employee Name</th>
				<th>Department</th>
				<th>Type of Offense</th>
				<th>Date of Violation</th>
				<th>Given Action</th>
				<th>Date of Warning</th>
				<th>Status</th>
            </tr>";
                foreach($empDisciplinaryActionList as $row){
				
$info .= "
            <tr>
					<td>".$row->employeeid."</td>
					<td>".$row->fullname."</td>
					<td>".$row->department."</td>
					<td>".$row->offense_type."</td>
					<td>".$row->dateViolation."</td>
					<td>".$row->sanction."</td>
					<td>".$row->dateWarning."</td>
					<td>".($row->confirm == 'YES' ? 'CONFIRMED' : 'NOT CONFIRMED')."</td>
            </tr>";
		
                }		
$info .= "      
        </table>
    </div>
</div>";



$pdf->WriteHTML($info);

$pdf->Output();
?>



