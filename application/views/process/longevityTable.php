<?
	$campus = $this->input->post("campus");
	$cutoff = $this->input->post("cutoff");
	$c = explode(",",$cutoff);
	$year = date("Y",strtotime($c[0]));
?>

<div class="well blue">
	<!-- <?=$cutoff;?> -->
	<div class="well-header">
	</div>
	<div class="well-content"> 

		<a href="#" class="btn btn-primary" id="butt_printresult" style="float:right">Print</a>
		
		<!-- <label>Cut-Off </label>
		<select class="chosen col-md-4" id="cutoff"><?=$this->employeemod->displayCutOff()?></select> -->
		<a href="#" class="btn btn-primary" id="butt_save">Save</a>
		<br>
		<br>
		<table class="table table-striped table-bordered table-hover" id="data" >
			<thead>
				<tr style="background-color:#343434;color:white">
					<th style="text-align:center">Employee ID </th>
					<th style="text-align:center">Employee Name</th>
					<th style="text-align:center" width="10%">Date Hired</th>
					<th style="text-align:center">Date of Regular Appointment</th>
					<th style="text-align:center"># of Credited Yrs. of Service as Regular</th>
					<th style="text-align:center">Previous Basic Pay<br><?=date("Y",strtotime("01-01-".$year."- 2 year"))." - ".date("Y",strtotime("01-01-".$year."- 1 year"))?></th>
					<th style="text-align:center">Present Basic Pay<br><?=date("Y",strtotime("01-01-".$year."- 1 year"))." - ".date("Y",strtotime("01-01-".$year))?></th>
					<th style="text-align:center"><?=date("Y",strtotime("01-01-".$year."- 4 year"))." - ".date("Y",strtotime($year."- 1 year"))?><br>Longevity Pay Per Month</th>
					<th style="text-align:center"><?=date("Y",strtotime("01-01-".$year."- 1 year"))." - ".date("Y",strtotime("01-01-".$year))?><br>Longevity Pay Per Month</th>
					<!-- <th style="text-align:center">Longevity Pay</th> -->
					<th style="text-align:center">Proposed Increase Per Month</th>
					<th style="text-align:center">Select All <br> <input class="double-sized-cb" type="checkbox" name="selectall"/></th>
				</tr>
			</thead>
			<tbody>
				<?
					
					$longevityList = $this->employee->showLongevity($year,$campus);
					if($longevityList->num_rows() > 0)
					{
						$dept =$a ="";
						
						foreach($longevityList->result() as $row)
						{
							$id = $row->employeeid;
							//GETTING CREDITED YEAR OF EMPLOYEE
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
							

						
							//COMPUTATION FOR GETTING LONGEVITY
							$pcpay= round(((($this->employee->GetBasicPreviousPay($id) + $this->employee->GetBasicCurrentPay($id))/ 2)/12),2); 
							$totallongevity = round(((($pcpay * 3)*$a)/26),2);
							

							// if($row->dateOfRegularAppointment >= $row->dateemployed)
							// {
							// 	if($year - date("Y",strtotime($row->dateOfRegularAppointment)) < 5)
							// 	{
							// 		continue;
							// 	}
							// 	$noOFCreditedYears = $year - date("Y",strtotime($row->dateOfRegularAppointment));
							// 	$dateOfRegularAppointment = date("Y",strtotime($row->dateOfRegularAppointment));
							// }
							// else
							// {
							// 	if($year - date("Y",strtotime($row->dateposition)) < 5)
							// 	{
							// 		continue;
							// 	}
							// 	$noOFCreditedYears = $year - date("Y",strtotime($row->dateposition));
							// 	$dateOfRegularAppointment = date("Y",strtotime($row->dateposition));
							// }
							
							 // $isteaching = $this->employee->getempteachingtype($row->employeeid);
							if($dept != $row->deptid && $noCreditYears > 5)
							{
								?>
								
									<tr>
										<td colspan="11" ><?=$this->extras->getDeptDesc($row->deptid)?></td>
									</tr>
								<?
								$dept = $row->deptid;
							}
							
							// $prevLongevityPayB = $presentLongevityPayB = "";
							// $prevLongevityPay = $presentLongevityPay = "";
							
							// if($noCreditYears - 1 >= 5)
							// {
							// 	$presentLongevityPayB = ((($row->lastPrevBasicPay + $row->prevBasicPay)/2)/12);
								
							// 	$prevLongevityPay = round(($presentLongevityPayB * 3 * (($noCreditYears - 1) - 4)) / 26,2);
							// }
							
							// $prevLongevityPayB = ((($row->prevBasicPay + $row->presentBasicPay)/2)/12);
							
							
							// $presentLongevityPay = round(($prevLongevityPayB * 3 * (($noCreditYears) - 4)) / 26,2);
							
							//AND (({$year} - YEAR(a.`dateposition`)) >= 5)
							if ($noCreditYears > 5 ) 
							{
							
							?>

								<tr class="marker">
								  
									<td style="text-align:center" name='id' employeeid="<?=$row->employeeid?>"><?=$row->employeeid?></td>
									<td style="text-align:center"><?=$row->fullname?></td>
									<td style="text-align:center"><?=date("m-d-Y",strtotime($this->employee->EmpHiredDate($id)))=='01-01-1970'?'':date("m-d-Y",strtotime($this->employee->EmpHiredDate($id)))?></td>
									<!-- <td style="text-align:center"><?=date("m-d-Y",strtotime($row->dateposition))?></td> -->
									<td style="text-align:center"><?=date("m-d-Y",strtotime($this->employee->EmpRegularDate($id)))?></td>
									<!-- <td style="text-align:center"><?=$dateOfRegularAppointment?></td> -->
									<td style="text-align:center"><?=$noCreditYears>="5"?$noCreditYears:''?></td>
									<td style="text-align:center"><?=$this->employee->GetBasicPreviousPay($id)?></td>
									<td style="text-align:center"><?=$this->employee->GetBasicCurrentPay($id)?></td>
									<td style="text-align:center"><??></td>
									<td style="text-align:center"><?=$totallongevity?></td>
									<td style="text-align:center"><?=$totallongevity?></td>
									<th style="text-align:center"><input class="double-sized-cb" type="checkbox" name="perEmp"/ value="<?=$row->employeeid?>"></th>
									<!-- <td style="text-align:center"><?=$presentLongevityPay<0?'':$presentLongevityPay?></td> -->
								</tr>

							<?
							}
						}
					}
					else
					{
						?>
							<td colspan="11">No Data!</td>
						<?
					}
				?>
			</tbody>
		</table>
	</div>
</div>
<script>
	$(".chosen").chosen();
	var empList = {};
	var pars = "~u~"; 
	$("#butt_save").unbind().click(function(){
		var formdata = $('#data').find('.marker').map(function(){ 
			 var $td = $(this).find('td');
			 return {
			 	emp_no: $td.first().text(),
			 	income: $td.last().text(),
			 	date: $("#cutoff").val().split(','),
			 }
			 }).toArray();
		formdata.forEach(function(value,key)
		{
			if(typeof empList["emp_no"]=="undefined"){
				empList["emp_no"] = [];
			}
			if(typeof empList["income"]=="undefined"){
				empList["income"] = [];
			}
			if(typeof empList["date"]=="undefined"){
				empList["date"] = [];
			}
			empList['emp_no'].push(value.emp_no);
			empList['income'].push(value.income);
			empList['date'].push(value.date);

			// empList[key] = {};
		})
			console.log(empList);
			// return ;
			$("#table").html("<img src='<?=base_url()?>images/loading.gif' /> Please wait... ");
			$.ajax({
					type: "POST",
					url	: "<?=site_url("process_/saveLongevityIncome")?>",
					data: empList,
					success: function(msg){
						console.log(msg);
						alert(msg);
						$("#searchlbtn").click();
					}
				});		
	});
	$("#butt_printresult").click(function()
	{	
	    var params = "";
		params = "?form=longevityExcel";
		params += "&cutoff=<?=$year?>";
		params += "&campus=<?=$campus?>";
		params += "&view=reports_excel/longevityExcel";
		if ($("input[name='perEmp']:checked").length <= 0) {
			
			var print = confirm("Are you sure do you want to print all the record?");
			if (print) {
				 window.open("<?=site_url("reports_/reportloader")?>"+params);
			}
		}
		else
		{	
			var empid = [];
			var print = confirm("Are you sure do you want to print chosen record?");
			if (print) {
				$("input[name='perEmp']").each(function()
				{
					if ($(this).is(":checked")){
					 empid.push($(this).val());
					}
					console.log(empid);return;
					
				});
				
				params += "&empid="+empid;
				 window.open("<?=site_url("reports_/reportloader")?>"+params);
				 // window.open("<?=site_url("forms/loadForm")?>"+params);
			}
		}
	});
	$("input[name='selectall']").click(function()
	{
		if ($(this).is(":checked")) {
			$("input[name='perEmp']").prop('checked',true);
		}
		else
		{
			$("input[name='perEmp']").prop('checked',false);	
		}
	});
	// if($("#cutoff").val() != "")
	// 	{
	// 		$("#longevityList").find("table > tbody  > tr").each(function(){
	// 			if($(this).find("td:first").attr("employeeid"))
	// 			{
	// 				empList += empList ? "|" : ""; 
	// 				empList += $(this).find("td:first").attr("employeeid");
	// 				empList += pars;
	// 				empList += $(this).find("td:eq(9)").html();
	// 			}
	// 		});
	// 		if(empList != "")
	// 		{ 
	// 			var datas = {
	// 				empList : empList,
	// 				cutoff : $("#cutoff").val()
	// 			}
	// 			console.log(empList);
	// 			$.ajax({
	// 				type: "POST",
	// 				url	: "<?=site_url("process_/saveLongevity")?>",
	// 				data: datas,
	// 				dataType : "text",
	// 				success: function(msg){
	// 					alert(msg);
	// 				}
	// 			});
	// 		}
	// 		else
	// 		{
	// 			alert("No data");
	// 		}
	// 	}
	// 	else
	// 	{
	// 		alert("Please select cutoff.");
	// 	}
</script>