<?php
	//Added 6-4-2017
	$CI =& get_instance();
    $CI->load->model('my_payroll');
	$payslipList = $CI->my_payroll->getEmpPayslipList($this->session->userdata('username'));
	$reglamentoryDeductArray = array('PAGIBIG','SSS','PHILHEALTH');
	
	$query = $this->db->query("SELECT deptid FROM employee where employeeid = '".$this->session->userdata('username')."'")->result();
	$deptid = $query[0]->deptid
?>
<style>
.dataTables_paginate {
    margin-top: 6px;
}
.datatable tr th{
    padding: 1px 12px 1px 12px;
}
table tr td, tr th{
    text-align: center;
}
table tr th{
    background-color: #313131;
    color: #FFFFFF;
}
</style>
<div class="content no-search">
	<table class="table table-hover table-bordered table-striped">
		<thead>
			<tr>
				<th rowspan='2' class="align_center">Cut off date</td>
				<!-- <th rowspan='2' class="align_center">Basic Salary</td> -->
				<!--INCOME-->
				<!-- <?if($CI->my_payroll->getEmpIncome($this->session->userdata('username'))->num_rows() != 0){?>
				<th colspan='<?=$CI->my_payroll->getEmpIncome($this->session->userdata('username'))->num_rows()?>' class="align_center">Income</td>
				<?}?> -->
				<!--DEDUCTION-->
				<!-- <th colspan='4' class="align_center">Reglamentory Deduction</td> -->
				<!-- <th colspan='<?=$CI->my_payroll->getEmpOtherDeduction($this->session->userdata('username'))->num_rows() + $CI->my_payroll->getEmpLoan($this->session->userdata('username'))->num_rows() +2?>' class="align_center">Other Deduction</td> -->
				<!--NETPAY-->
			<!-- 	<th rowspan='2' class="align_center">Net Pay</td> -->
				<th rowspan='2' class="align_center">Print</td>
			</tr>
			<tr>
				<!-- <?
					foreach($CI->my_payroll->getEmpIncome($this->session->userdata('username'))->result() as $row)
					{?>
						<th class="align_center"><?=$CI->my_payroll->getOtherIncomeDescription($row->code_income)?></td>
					<?}
				?> -->
				<!-- <th class="align_center">WithHolding Tax</th>
				<?
					foreach($reglamentoryDeductArray as $row => $value)
					{?>
						<th class="align_center"><?=$value?></th>
					<?}
	
					foreach($CI->my_payroll->getEmpLoan($this->session->userdata('username'))->result() as $row)
					{?>
						<th class="align_center"><?=$CI->my_payroll->getLoanDescription($row->code_loan)?></th>
					<?}

					foreach($CI->my_payroll->getEmpOtherDeduction($this->session->userdata('username'))->result() as $row)
					{?>
						<th class="align_center"><?=$CI->my_payroll->getOtherDeductionDescription($row->code_deduction)?></th>
					<?}
				?> -->
				<!-- <th class="align_center">Tardy</th>
				<th class="align_center">Absent</th> -->
			</tr>
		</thead>
		<tbody>
			<?
				foreach($payslipList as $row)
				{
					$salary = $totalIncome = $totalDeduction = $netpay = 0;
				?>
					<tr>
						<td class="align_center"><?=date("F d",strtotime($row->cutoffstart))?> - <?=date("d Y",strtotime($row->cutoffend))?></td>
						<!-- <td class="align_center"><?=number_format($row->salary)?></td> -->
				<!-- <?
				//INCOME
					$salary = $row->salary;
					foreach($CI->my_payroll->getEmpIncome($this->session->userdata('username'))->result() as $r)
					{
						if($row->income)
						{
							$incomeArray = explode("/",$row->income);
							$exist= false;
							foreach($incomeArray as $ia)
							{
								$income = explode("=",$ia);
								if($r->code_income == $income[0])
								{
				?>
						<td class="align_center"><?=number_format($income[1],2)?></td>
						<?
									$totalIncome += $income[1];
									$exist = true;
									break;
								}
							}
							if(!$exist){ ?>	<td class="align_center"><?=number_format(0,2)?></td><?	}
						}
						else
						{
						?>
								<td class="align_center"><?=number_format(0,2)?></td>
							<?
						}
					}
							?> -->
						<!--WITHHOLDING TAX-->
						<!-- <td class="align_center"><?=number_format($row->withholdingtax,2)?></td> -->
							<!-- <?
							//DEDUCTION
							foreach($reglamentoryDeductArray as $r => $value)
							{
								if($row->fixeddeduc)
								{
									$fixeddeducArray = explode("/",$row->fixeddeduc);
									$exist= false;
									foreach($fixeddeducArray as $fd)
									{
										$fixeddeduc = explode("=",$fd);
										if($value == $fixeddeduc[0])
										{
							?>
											<td class="align_center"><?=number_format($fixeddeduc[1],2)?></td>
											<?		
												$totalDeduction += $fixeddeduc[1];
												$exist = true;
												break;
										}
									}
									if(!$exist){ ?>	<td class="align_center"><?=number_format(0,2)?></td><?	}
								}
								else
								{
									?>
										<td class="align_center"><?=number_format(0,2)?></td>
									<?
								}
							}
													
							foreach($CI->my_payroll->getEmpLoan($this->session->userdata('username'))->result() as $r)
							{
								if($row->loan)
								{
									$loanArray = explode("/",$row->loan);
									$exist= false;
									foreach($loanArray as $l)
									{
										$loan = explode("=",$l);
										if($r->code_loan == $loan[0])
										{
									?>
												<td class="align_center"><?=number_format($loan[1],2)?></td>
											<?		
												$totalDeduction += $loan[1];
												$exist = true;
												break;
										}
									}
									if(!$exist){ ?>	<td class="align_center"><?=number_format(0,2)?></td><?	}
								}
								else
								{
										?>
										<td class="align_center"><?=number_format(0,2)?></td>
									<?
								}
							}
											
							foreach($CI->my_payroll->getEmpOtherDeduction($this->session->userdata('username'))->result() as $r)
							{
								if($row->otherdeduc)
								{
									$deductionArray = explode("/",$row->otherdeduc);
									$exist= false;
									foreach($deductionArray as $oa)
									{
										$otherdeduc = explode("=",$oa);
										if($r->code_deduction == $otherdeduc[0])
										{
											?>
												<td class="align_center"><?=number_format($otherdeduc[1],2)?></td>
											<?		
												$totalDeduction += $otherdeduc[1];
												$exist = true;
												break;
										}
									}
									if(!$exist){ ?>	<td class="align_center"><?=number_format(0,2)?></td><?	}
								}
								else
								{
									?>
										<td class="align_center"><?=number_format(0,2)?></td>
									<?
								}
							}
								?> -->
								<!--TARDY AND ABSENTS-->
								<!-- <td class="align_center"><?=number_format($row->tardy,2)?></td>
								<td class="align_center"><?=number_format($row->absents,2)?></td>
								<?
									$netpay = $salary + ($totalIncome - ($totalDeduction + $row->withholdingtax + $row->tardy + $row->absents)); 
								?> -->
								<!-- <td class="align_center"><?=number_format($netpay,2)?></td> -->
								<td class="align_center"><a href="#" class="printcutoff"  style='font-weight:bold' dfrom='<?=$row->cutoffstart?>' dto='<?=$row->cutoffend?>' schedule='<?=$row->schedule?>' quarter='<?=$row->quarter?>'><span class="icon-print"></span> View Payslip</a></td>
							</tr>
			<?	}?>
		</tbody>
	</table>
</div>

<script>
	$(".printcutoff").click(function(){
		var dfrom = $(this).attr('dfrom');
		var dto = $(this).attr('dto');
		var schedule = $(this).attr('schedule');
		var quarter = $(this).attr('quarter');
		
		var params = "form=payslip";
		params += "&eid=<?=$this->session->userdata('username')?>";
		params += "&dept=<?=$deptid?>";
		params += "&dfrom="+dfrom; 
		params += "&dto="+dto;
		params += "&schedule="+schedule;
		params += "&quarter="+quarter;
		$.ajax({
                url: "<?=site_url('reports_/constructEncryptedFormData')?>",
                type: "POST",
                data: {params: params},
                success: function(response){
                    window.open("<?=site_url("reports_/viewPayslipReport")?>?data="+response,"");
                }
            })
		// console.log(params);
		// window.open("<?=site_url("forms/loadForm")?>"+params);
	});
</script>