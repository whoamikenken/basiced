<?
	$eid = $this->input->post("eid");
	$othIncome = $this->input->post("othIncome");
	$ratebased = $this->payroll->getEmployeeRateBased($eid);
?>
<style type="text/css">
	.panel-body{
		margin-top: 0px;
	}
</style>
<div class="panel">
	<div class="panel-heading"><h4><b>Employee List - <?=strtoupper($this->extensions->getIncomeDesc($othIncome))?></b></h4></div>
	<div class="panel-body"> 
		<a href="#" class="btn btn-primary" id="butt_save" style="float:right; margin-bottom: 15px;">Save</a>
		<table class="table table-striped table-bordered table-hover" id="emplist">
			<thead>
				<tr style="background-color:#0072c6;color:black">
					<th style="text-align:center">Employee</th>
					<th style="text-align:center">Fullname</th>
					<th style="text-align:center">Monthly </th>
					<th style="text-align:center">Daily</th>
					<th style="text-align:center">Hourly</th>
					<th style="text-align:center">Effectivity Date</th>
					<th style="text-align:center">End Date</th>
				</tr>
			</thead>
			<tbody>
				<?
					$i = 0;
					foreach($eid as $k => $v)
					{
						$isteaching = $this->employee->getempteachingtype($v);
						?>
							<tr empType="<?=$isteaching?>">
								<td style="text-align:center" employeeid="<?=$v?>"><?=$v?></td>
								<td style="text-align:center"><?=$this->employee->getfullname($v)?></td>
								<td style="text-align:center">
									<input class="monthly form-control required" id="monthly" name="monthly" style='text-align: center;' type="text" onkeypress="return numbersonly(this)"/>
								</td>
								<td style="text-align:center">
								</td>
								<td style="text-align:center">
								
								</td>
								<td style="text-align:center">
									<div class='input-group date' data-date="" data-date-format="yyyy-mm-dd">
										<input type='text' class="form-control" name="dateEffective" value=""/>
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</td>
								<td style="text-align:center">
									<div class='input-group date' data-date="" data-date-format="yyyy-mm-dd">
										<input type='text' class="form-control" name="dateEnd" value=""/>
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</td>
							</tr>
						<?
					}
				?>
			</tbody>
		</table>
	</div>
</div>

<script>

	$("#butt_save").unbind().click(function(){
		$(".greyHistory").click();
		$(".grey").click();
		$(".greyedit").click();
		var iscontinue = true;
		$("#emplist > tbody > tr").each(function()
		{	
			if($(this).find("td:eq(2)").find("input").val() == "")
			{
				$(this).find("td:eq(2)").css("background-color","red");
				iscontinue = false;
			}
			else
			{
				$(this).find("td:eq(2)").css("background-color","");
			}
			
			if($(this).find("td:eq(5)").find("input").val() == "")
			{
				$(this).find("td:eq(5)").css("background-color","red");
				iscontinue = false;
			}
			else
			{
				$(this).find("td:eq(5)").css("background-color","");
			}
		});
		
		if(iscontinue)
		{
			var datas = "";
			$("#emplist > tbody > tr").each(function(){
				datas += datas?"|":"";
				datas += $(this).find("td:eq(0)").html();
				datas += "~u~";
				datas += $(this).find("td:eq(2)").find("input").val();
				datas += "~u~";
				datas += $(this).find("td:eq(3)").html();
				datas += "~u~";
				datas += $(this).find("td:eq(4)").html();
				datas += "~u~";
				datas += $(this).find("td:eq(5)").find("input").val();
				datas += "~u~";
				datas += $(this).find("td:eq(6)").find("input").val();
			});
			//console.log(datas);
			$.ajax({
				type	: "POST",
				url		: "<?=site_url("process_/saveOtherIncome")?>",
				data	: {
							datas : datas,
							othIncome : "<?=$othIncome?>"
				},
				dataType:"json",
				success	: function(msg){
					// if(msg == "Success")
					// {
					// 	$('#be_modal').find('.modal-body').html("Successfully Saved!").css({'color':'green','font-size':'15px','font-weight':'bold'});	
					// 	$("#be_modal").modal('show');	
						
					// }
					// else
					// {
					// 	$('#be_modal').find('.modal-body').html("Failed to Save!").css({'color':'red','font-size':'15px','font-weight':'bold'});
					// 	$("#be_modal").modal('show');		
						
					// }
					// $("select[name='othincome_drop']").trigger("change");
					// $('#be_modal').find('.modal-body').html('Successfully Saved/Updated count: '+msg.save+'<br>').css({'color':'green','font-size':'15px','font-weight':'bold'});
					// $('#be_modal').find('.modal-body').append('<span style="color:red;">Data Already Exist/Failed count: '+msg.failed+'</span>');
					alert("Successfully saved "+msg.save+" Data Already Exist/Failed count: "+msg.failed);		
					$("select[name='othincome_drop']").trigger("change");
				}
			});
		}
	});
	
	$(".monthly").keyup(function(e){
		if (e.keyCode === 9) return false; 
		var empType = $(this).closest("tr").attr("empType");
		if("<?= $ratebased ?>" == "teaching"){
			workingdays      =   261;
		}else{
			workingdays      =  313;
		}
		monthly = $(this).val();
		
		daily  = Number(((monthly*2)*12)/workingdays); 
		daily  = parseFloat(daily).toFixed(2);
        $(this).closest("tr").find("td:eq(3)").html(addCommas(daily));
		
		hourly  = Number((daily)/8);  
		hourly  = parseFloat(hourly).toFixed(2);            // STATIC daily salary divided by total no. of workhours     
        $(this).closest("tr").find("td:eq(4)").html(addCommas(hourly));
    });
	
	function addCommas(nStr)
	{
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		//return x1 + x2;
		return nStr;
	}

	function numbersonly(myfield, e, dec, id)
	{
		var key;
		var keychar;
			
		if (window.event)   key = window.event.keyCode;
		else if (e)         key = e.which;
		else                return true;
		keychar = String.fromCharCode(key);
			
		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) ) return true;
			
		// numbers
		else if (((id ? "0123456789.- " : "0123456789.").indexOf(keychar) > -1))   return true;
			
		// decimal point jump
		else if (dec && (keychar == "."))
		{
			myfield.form.elements[dec].focus();
			return false;
		}
		else    return false;
	}

	$(".date").datetimepicker({
	    format: "YYYY-MM-DD"
	});
</script>