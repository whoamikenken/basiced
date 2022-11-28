<?php

include "application/config/connection.php";
include "application/views/forms_pdf/function/payrollfunc.php";
$year = date('Y');
$year = date('Y',strtotime($year."- 1 year"));
$campus  = $this->input->post('campus');
$employeeid = $this->input->post('empid');


$mpdf = new mPDF('utf-8','A3-L','10','','3','3','3','10','9','9');

$longevityList = $this->employee->showLongevity($year,$campus);

$datas = '<table  border=1 style="border-collapse:collapse" id="">
				<thead>
				<tr style="background-color:grey;color:white">
					<th style="text-align:center">Employee ID '.$employeeid.'</th>
					<th style="text-align:center">Employee Name</th>
					<th style="text-align:center" width="10%">Date Hired</th>
					<th style="text-align:center">Date of Regular Appointment</th>
					<th style="text-align:center"># of Credited Yrs. of Service as Regular</th>
					<th style="text-align:center">Previos Basic Pay<br>'.date("Y",strtotime("01-01-".$year."- 2 year"))." - ".date("Y",strtotime("01-01-".$year."- 1 year")).'</th>
					<th style="text-align:center">Present Basic Pay<br>'.date("Y",strtotime("01-01-".$year."- 1 year"))." - ".date("Y",strtotime("01-01-".$year)).'</th>
					<th style="text-align:center">'.date("Y",strtotime("01-01-".$year."- 4 year"))." - ".date("Y",strtotime($year."- 1 year")).'<br>Longevity Pay Per Month</th>
					<th style="text-align:center">'.date("Y",strtotime("01-01-".$year."- 1 year"))." - ".date("Y",strtotime("01-01-".$year)).'<br>Longevity Pay Per Month</th>
					<!-- <th style="text-align:center">Longevity Pay</th> -->
					<th style="text-align:center">Proposed Increase Per Month</th>					
				</tr>
				</thead>
				<tbody>

			';
$colspan =11;
foreach($longevityList->result() as $row)
	{
		$id = $row->employeeid;
		$regyear = $this->employee->EmpRegularDate($id);
		$noCreditYears = $year - date("Y",strtotime($regyear));
		if ($noCreditYears == 5)
		$a = 1;
		elseif ($noCreditYears == 6) 
		$a = 2;
		elseif ($noCreditYears == 7) 
		$a = 3;
		elseif ($noCreditYears == 8) 
		$a = 4;
		elseif ($noCreditYears == 9) 
		$a = 5;
		elseif ($noCreditYears == 10) 
		$a = 6;
		elseif ($noCreditYears == 11) 
		$a = 7;
		elseif ($noCreditYears == 12) 
		$a = 8;
		elseif ($noCreditYears == 13) 
		$a = 9;
		elseif ($noCreditYears == 14) 
		$a = 10;
		elseif ($noCreditYears == 15) 
		$a = 11;
		elseif ($noCreditYears == 16) 
		$a = 12;
		elseif ($noCreditYears == 17) 
		$a = 13;
		elseif ($noCreditYears == 18) 
		$a = 14;
		elseif ($noCreditYears == 19) 
		$a = 15;
		elseif ($noCreditYears == 20) 
		$a = 16;
		elseif ($noCreditYears == 21) 
		$a = 17;
		elseif ($noCreditYears == 22) 
		$a = 18;
		elseif ($noCreditYears >= 23) 
		$a = 19;
		if ($empid == "") {
			//COMPUTATION FOR GETTING LONGEVITY
					$pcpay= round(((($this->employee->GetBasicPreviousPay($id) + $this->employee->GetBasicCurrentPay($id))/ 2)/12),2); 
					$totallongevity = round(((($pcpay * 3)*$a)/26),2);
						if($dept != $row->deptid && $noCreditYears > 5)
						{
							$datas .='<tr id="tbl"><td colspan='.$colspan.' >'.$this->extras->getDeptDesc($row->deptid).'</td></tr>';
							$dept = $row->deptid;
						}
						if ($noCreditYears > 5 ) 
						{

							$datas .='<tr>
										
										<td style="text-align:center" name="id" >'.$row->employeeid.'</td>
										<td style="text-align:center">'.$row->fullname.'</td>
										<td style="text-align:center">'.(date("m-d-Y",strtotime($this->employee->EmpHiredDate($id)))=='01-01-1970'?'':date("m-d-Y",strtotime($this->employee->EmpHiredDate($id)))).'</td>
										<td style="text-align:center">'.(date("m-d-Y",strtotime($this->employee->EmpRegularDate($id)))).'</td>
										<td style="text-align:center">'.($noCreditYears>=5?$noCreditYears:'').'</td>
										<td style="text-align:center">'.$this->employee->GetBasicPreviousPay($id).'</td>
										<td style="text-align:center">'.$this->employee->GetBasicCurrentPay($id).'</td>
										<td style="text-align:center"></td>
										<td style="text-align:center">'.$totallongevity.'</td>
										<td style="text-align:center">'.$totallongevity.'</td>
									 </tr>';
						}
		}
		//filter for chosen employee only
		else
		{
				$eid = explode(',', $empid);
				foreach ($eid as $key) {
					if ($row->employeeid == $key) {
							//COMPUTATION FOR GETTING LONGEVITY
							$pcpay= round(((($this->employee->GetBasicPreviousPay($id) + $this->employee->GetBasicCurrentPay($id))/ 2)/12),2); 
							$totallongevity = round(((($pcpay * 3)*$a)/26),2);
								if($dept != $row->deptid && $noCreditYears > 5)
								{
									$datas .='<tr id="tbl"><td colspan='.$colspan.' >'.$this->extras->getDeptDesc($row->deptid).'</td></tr>';
									$dept = $row->deptid;
								}
								if ($noCreditYears > 5 ) 
								{

									$datas .='<tr>
												
												<td style="text-align:center" name="id" >'.$row->employeeid.'</td>
												<td style="text-align:center">'.$row->fullname.'</td>
												<td style="text-align:center">'.(date("m-d-Y",strtotime($this->employee->EmpHiredDate($id)))=='01-01-1970'?'':date("m-d-Y",strtotime($this->employee->EmpHiredDate($id)))).'</td>
											
												<td style="text-align:center">'.(date("m-d-Y",strtotime($this->employee->EmpRegularDate($id)))).'</td>
												
												<td style="text-align:center">'.($noCreditYears>=5?$noCreditYears:'').'</td>
												<td style="text-align:center">'.$this->employee->GetBasicPreviousPay($id).'</td>
												<td style="text-align:center">'.$this->employee->GetBasicCurrentPay($id).'</td>
												<td style="text-align:center"></td>
												<td style="text-align:center">'.$totallongevity.'</td>
												<td style="text-align:center">'.$totallongevity.'</td>
												
											 </tr>';
								}
						}
				}
				
			
		}
		
		
		

		
		
	}

$datas .='</tbody></table>';
$html = "
		<style>
		p{
		 margin-left:50px;
		}
		.tblremarks
		{
		 margin-left:50px;
		 width:100%;
		}
		.header
		{
		 width:3%;
		 position:absolute;
		 margin-left:370px;
		 text-align:center;
		 font-size:12px;
		}
		.datadeduction
		{
			text-align:right;
		}
		.datagrosspay
		{	
			text-align:right;
		}
		.tbl
		{
		 margin-left:50px;
	     border-collapse:collapse;
		 width:90%;

		}
		#grosspay
		{
			width:30%;
			margin-top:10px;
			 margin-left:50px;
		}
		#otherdeduction
		{
			 margin-left:50px;
			width:30%;
		margin-top: 10px;

		}
		.container{
			margin-top:10%;
			width:100%;
		}
		.data
		{
		 font-weight:normal;
		 font-size:12px;
		 font-family:times new roman;
		 text-align:center;
		 width:5px;
		}
		.head{
		text-align:center;
	 	font-size:12px;
	  	border:1px solid;	
		}
		#tbl
		{
			background-color:#C8C8C8;      
		}
		</style>
		<body>".$datas."</body>
		";

	

 $mpdf->WriteHTML($html);
 $mpdf->Output();
 

?>
