<?php  
/**
* @author justin (with e)
* @copyright 2018
*
* > for ica-hyperion 21294
*/

$CI =& get_instance();
$CI->load->library('PdfCreator_mpdf');

$mpdf = new mPDF('utf-8','LETTER-L','8','arial','5','5','30','5','9','9');

$content = '';

$tbody = "";
$id = explode(",", $id);
$income = explode(",", $income);
$hours = explode(",", $hours);
$t_pay = explode(",", $t_pay);

# > for displaying table content
for ($i=0; $i < count($id); $i++) { 
	$tbody .='
		<tr>
			<td style="text-align: right;">'. ($i + 1) .'</td>
			<td style="text-align: center;">'. $id[$i] .'</td>
			<td style="text-align: center;">'. $this->employee->getfullname($id[$i]) .'</td>
			<td style="text-align: right;">'. number_format($income[$i],2) .'</td>
			<td style="text-align: center;">'. $hours[$i] .'</td>
			<td style="text-align: right;">'. number_format($t_pay[$i],2) .'</td>
		</tr>
	';
}

$mpdf->SetHTMLHeader('<table class="header">
	<tr>
		<td class="align_center" rowspan="2" style="padding:0 20px;" valign="bottom"><img src="images/school_logo3.bmp" style="width: 50px;"/></td>
		<td style="font-size:15px;width: 100%;"><b>Pinnacle Technologies Inc.</b></td>
	</tr>
	<tr>
		<td>Other Income</td>
	</tr>
</table>','',false);

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
	    font-size: 10px;
	}

	.table-bordered td, .table-bordered th {
	    border: 1px solid grey;
	}

	.table-bordered tr th{
	    background-color: #3b5998;
	    color: #FFF;
	}

	.table-header{
		background-color: #3b5998;
	    color: #FFF;
	}

	.table-bordered td{
		text-align: center;
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
	.late{
		color:red;
	}
	.absent{
		background-color: #ffe6e6;
	}
	.nosched td{
		background-color: gray !important;
		color: white !important;
	}
</style>
';

# > content header..
/*$content .= '
<table class="header">
	<tr>
		<td class="align_center" rowspan="2" style="padding:0 20px;" valign="bottom"><img src="images/school_logo3.bmp" style="width: 50px;"/></td>
		<td style="font-size:15px;width: 100%;"><b>Pinnacle Technologies Inc.</b></td>
	</tr>
	<tr>
		<td>Other Income</td>
	</tr>
</table>
';*/
# > end of content header..
$date = explode(",", $cutoff);
$code_income = $this->db->query("SELECT description FROM payroll_income_config WHERE id='$incomedata';")->row()->description;
# > table
$content .='
<table border=1>
	<thead>
		<tr>
			<th colspan="6" class="table-header" style="text-align: left;"> Cut-Off : '. date('F d, Y', strtotime($date[0])) .' - '. date('F d, Y', strtotime($date[1])) .'</th>
		</tr>
		<tr>
			<th class="table-header" width="5%"> # </th>
			<th class="table-header" width="10%"> Employee ID </th>
			<th class="table-header" width="35%"> Employee Name </th>
			<th class="table-header" width="15%"> Monthly '. $code_income .'</td>
			<th class="table-header" width="20%"> Total Number of Hours To be Deduct </th>
			<th class="table-header" width="15%"> Total '. $code_income .'</th>
		</tr>
	</thead>
	<tbody>
		'. $tbody .'
	</tbody>
</table>
';
# > end of table

$mpdf->WriteHTML($content);

$mpdf->Output();

die;
?>
