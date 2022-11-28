<?php 

/**
 * @author Angelica
 * @copyright 2017
 *
 */

$CI =& get_instance();
$CI->load->library('PdfCreator_mpdf');

$mpdf = new mPDF('utf-8','LETTER-L','8','arial','5','5','5','5','9','9');


$datedisplay = "";
$from_date = $datesetfrom;
$to_date = $datesetto;
$empid = $fv;
$edata = $edata;
$deptids = $this->employee->getindividualdept($empid);
$datedisplay = $this->time->createRangeToDisplay($from_date,$to_date);
$teachingtype = $this->employee->getempteachingtype($empid);

$data = array('from_date'=>$from_date,'to_date'=>$to_date,'datedisplay'=>$datedisplay,'empid'=>$empid,'edata'=>$edata,'deptid'=>$deptid,'teachingtype'=>$teachingtype);


$content = '';


$content .= '
			<style>
			table, .well-content{
				width: 100%;
				font-family:calibri;
			}
			.header, #maincontent th{
				color: blue;
			}
			.table-bordered{
				border: 1px solid #fff;
			    border-collapse: collapse;
			}

			.table-bordered td, .table-bordered th {
			    border: 1px solid grey;
			}
			
			.table-bordered tr th{
			    background-color: #3b5998;
			    color: #FFF;
			}
			.align_center{
				text-align: center;
			}
			.align_right{
				text-align: right;
			}
			.border_bottom{
				border-bottom: 1px solid grey;
			}
			</style>
';

$content .= '
			<table class="header">
				<tr>
					<td class="align_center" rowspan="2" style="padding:0 20px;" valign="bottom"><img src="images/school_logo3.bmp" style="width: 50px;"/></td>
					<td style="font-size:15px;width: 100%;"><b>Pinnacle Technologies Inc.</b></td>
				</tr>
				<tr>
					<td>'.$datedisplay.' Attendance</td>
				</tr>
			</table><br><br>
';


if($teachingtype){  // Teaching
	include('attendance_report_teaching.php');
}else{
	include('attendance_report_nonteaching.php');
}


// echo $content;die;
$mpdf->WriteHTML($content);

$mpdf->Output();

die;
?>