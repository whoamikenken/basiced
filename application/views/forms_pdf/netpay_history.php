<?php 
/**
 * @author Angelica Arangco
 * @copyright 2017
 */
require_once(APPPATH."constants.php");		
function formatAmount($amount=''){
	$return ="";
	if($amount)
	{
	$return = number_format( $amount, 2 );
	}
	else
	{
	$return = '0.00';
	}
	return $return;
}

$CI =& get_instance();
$CI->load->model('extras');

$CI->load->library('PdfCreator_mpdf');

$mpdf = new mPDF('utf-8','LETTER','9','','10','10','35','5','5','5');
$montlist = Globals::monthList();
$month = $montlist[$month];
$header = "
        <table width='60%'  >
            <tr>
                <td rowspan='5' style='text-align: right;' width='60%'><img src='images/school_logo.jpg' style='width: 60px;text-align: center;' /></td>
                <td valign='middle' width='90%' style='padding: 0;text-align: center;' width='45%'><span style='font-size: 13px;'><b>Pinnacle Technologies Inc.</b></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center;'><span style='font-size: 13px;' width='45%'><strong>D`Great</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 13px;' width='55%'><strong>NET PAY HISTORY</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 10px;' width='55%'><strong>As of ".date("F Y")."</strong></span></td>
            </tr>
            <tr>
                <td valign='middle' style='padding: 0;text-align: center; margin-left:100px;'><span style='font-size: 10px;' width='55%'><strong>Period Cover ".$month.' '.$pyear."</strong></span></td>
            </tr>
            
        </table>
";


$mpdf->SetHTMLHeader($header);

$content = '
			<style>
				.container{
					content: "";
					display: block;
					clear: both;
				}
				table{
					border-collapse: collapse;
					font-family:calibri;
				}
				table td{
					padding: 0 5px;
				}
				th {
					color: yellow;
				}

				.center{
					text-align: center;
				}
			</style>


			<div class="container">
				<table class="table table-striped" cellspacing="0" border="1" width="100%"> 
					<thead>
						<tr style="background-color: #000000;">
							<th>#</th>
							<th>EMPLOYEE ID</th>
							<th>NAME</th>
							<th>AMOUNT</th>
							<th>CUT-OFF</th>
							<th>STATUS</th>';
							
							
$content .= '			</tr>
					</thead>
					<tbody>';
						$colspan = 'colspan="6"';
						$ofc = $officeDesc = 'sometext';
						$count = 1;
						$total = 0;
						foreach ($emplist as $key  => $val) {
							if($sort=='office'){
			                    if($ofc !== $val->office){
			                        if($officeDesc != $this->extensions->getOfficeDescriptionReport($val->office)){
			                        	if($ofc != "sometext"){
			                        		$content .="  <tr>
				                                        <td style='font-size: 13px; color:  #000000; text-align: right;' colspan='3'><b>TOTAL</b></td>
				                                        <td style='font-size: 13px; color:  #000000; text-align: left;' colspan='3'><b>".number_format($total, 2)."</b></td>
				                                    </tr>";
			                        	}
			                            $total = 0;
			                            $content .="  <tr>
			                                        <td style='font-size: 13px; color:  #000000; text-align: left; padding-left: 35px;' ".$colspan."><b>".$this->extensions->getOfficeDescriptionReport($val->office)."</b></td>
			                                    </tr>";

			                           
			                        }
			                    }
			                }
			                $officeDesc = $this->extensions->getOfficeDescriptionReport($val->office);
			                $ofc =$val->office;
							$content .= '
							<tr>
								<td>'.$count.'</td>
								<td>'.$val->employeeid.'</td>
								<td>'.$val->fullname.'</td>
								<td>'.number_format($val->net, 2).'</td>
								<td>'.date('M d-',strtotime($val->cutoffstart)) . date('d, Y',strtotime($val->cutoffend)).'</td>
								<td>'.$val->status.'</td>
							</tr>';
							$count++;
							$total = $total + $val->net;

						}
						$content .="  <tr>
		                                        <td style='font-size: 13px; color:  #000000; text-align: right;' colspan='3'><b>TOTAL</b></td>
		                                        <td style='font-size: 13px; color:  #000000; text-align: left;' colspan='3'><b>".number_format($total, 2)."</b></td>
		                                    </tr>";

$content .= '						
					</tbody>
				</table>
			</div>
			
';

$mpdf->WriteHTML($content);
$mpdf->Output();


?>
