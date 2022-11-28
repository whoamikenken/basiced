<style>
.dataTables_paginate {
    margin-top: 0px;
}
#oth tr td,#oth tr th{
    text-align: center;
}
#oth tr th{
    background-color: #8b9dc3;
    color: white;
}
table.dataTable thead .sorting_asc {
     background-image: url(); 
}

table.dataTable thead .sorting {
    background-image: url();
}

</style>

<?
	$eid = $this->input->post("eid");
	$otherIncome = $this->input->post("otherIncome");
	$otherHistory = $this->employee->CountOtherIncomeHistory($otherIncome);
?>

<div class="panel" >
	<div class="panel-heading" style="background-color: #0072c6;"><h4><b>History</b></h4></div>
	<div class="panel-body"  style="margin: -15px -15px 0px -15px;">
		<!-- <a href="#" class="btn blue pull-left dataTables_paginate" id="history" ></a>  -->
		<a href="#" class="btn btn-primary" id="butt_save" style="float:right; margin: 10px 15px 10px 0px;">Save </a>
		<br><br>
		<table class="table table-hover table-bordered" id="emplist" >
			<thead style="background-color: #0072c6;">
				<tr>
					<th style="text-align:center">Employee</th>
					<th style="text-align:center">Fullname</th>
					<th style="text-align:center">Monthly <?=$otherIncome=="29"? '<a href="#" class="btn blue glyphicon glyphicon-trash" id="clearall" style="width:15px;height:10px;" > Clear</a> ':""?></th>
					<th style="text-align:center">Daily</th>
					<th style="text-align:center">Hourly</th>
					<th style="text-align:center">Effectivity Date</th>
					<th style="text-align:center">End Date</th>
					<th style="text-align:center">Actions</th>
				</tr>
			</thead>
			<tbody>
				<?
					foreach($eid as $k => $v)
					{
						$emplist = $this->employee->emplistWithOtherIncome($otherIncome, $v);
						if($emplist->num_rows() > 0)
						{
							foreach($emplist->result() as $row)
							{
								$isteaching = $this->employee->getempteachingtype($row->employeeid);
								?>
								<tr id='information' empType="<?=$isteaching?>" empid = "<?=$row->employeeid?>">
									<td style="text-align:center"><?=$row->employeeid?></td>
									<td style="text-align:center"><?= $row->fullname?></td>
									<td style="text-align:center" ><span style="display: none;"><?=$row->monthly?></span><input class="form-control monthly required" id="monthly" name="monthly" type="text" value='<?=$row->monthly?>' style='text-align: center;' onkeypress="return numbersonly(this)"/></td>
									<td style="text-align:center"><?=$row->daily?></td>
									<td style="text-align:center"><?=$row->hourly?></td>
									<td style="text-align:center">
										<div class='input-group date' data-date="<?=$row->dateEffective == ""?"":date("Y-m-d",strtotime($row->dateEffective))?>" data-date-format="yyyy-mm-dd">
											<input type='text' class="form-control efdate" name="efdate" value="<?=$row->dateEffective == ""?"":date("Y-m-d",strtotime($row->dateEffective))?>"/>
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</span>
										</div>
									</td>
									<td style="text-align:center">
										<div class='input-group date' data-date="<?=$row->dateEnd == ""?"":date("Y-m-d",strtotime($row->dateEnd))?>" data-date-format="yyyy-mm-dd">
											<input type='text' class="form-control efdate" name="efdate" value="<?=$row->dateEnd == ""?"":date("Y-m-d",strtotime($row->dateEnd))?>"/>
											<span class="input-group-addon">
												<span class="glyphicon glyphicon-calendar"></span>
											</span>
										</div>
									</td>
									<td style="text-align:center">
										<!-- <a href="#" employeeid="<?=$row->employeeid?>" othIncome="<?=$otherIncome?>" class="btn btn-info editbtn" h><span class="glyphicon glyphicon-edit"></span></a> -->
										<a href="#" employeeid="<?=$row->employeeid?>" othIncome="<?=$otherIncome?>" class="btn btn-danger delbtn"><span class="glyphicon glyphicon-trash"></span></a>
									</td>
								</tr>
							<?
							}
						}
					}
				?>
			</tbody>
		</table>
		
	</div>
</div>
<div id="myModalatts" class="modal hide fade" data-backdrop="static" data-replace="true" data-keyboard="false" tabindex="-1">
    <div class="modal-header">
        <button id="modalclose" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove-sign"></i></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="row col-md-12" tag='display'></div>
        </div>
    </div>
    <!-- <div class="modal-footer">
        <a href="#" data-dismiss="modal" aria-hidden="true" class="btn greyedit">Close</a>
    </div> -->
</div>
<div id="be_modal_history" class="modal hide fade" data-backdrop="static" data-replace="true" data-keyboard="false" tabindex="-1">
    <div class="modal-header">
        <button id="modalclose" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove-sign"></i></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="row col-md-12" tag='display'></div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" data-dismiss="modal" aria-hidden="true" class="btn greyhistory">Close</a>
    </div>
</div>
<script>
	var otherHistory = 0;
	$(function()
	{
		$("#butt_save").show();
		$(".date").datetimepicker({
		    format: "YYYY-MM-DD"
		});
	});
	
	$(".delbtn").click(function(){
		$(".greyHistory").click();
		$(".grey").click();
		$(".greyedit").click();
		var deleteConfirm = confirm("Do you want to delete this?");
		if(deleteConfirm)
		{
			var employeeid = $(this).attr("employeeid");
			var othIncome = $(this).attr("othIncome");
			
			$.ajax({
				type : "POST",
				url : "<?=site_url("process_/deleteOtherIncome")?>",
				data : {
					employeeid : employeeid,
					othIncome : othIncome
				},
				success : function(msg){
					if(msg == "Success")
						{
							// $('#be_modal').find('.modal-body').html("Successfully Deleted!").css({'color':'green','font-size':'15px','font-weight':'bold'});	
							// $("#be_modal").modal('show');
							alert("Successfully Deleted!");	
							$("select[name='othincome_drop']").trigger("change");
						}
						else
						{
							// $('#be_modal').find('.modal-body').html("Failed to Delete!").css({'color':'red','font-size':'15px','font-weight':'bold'});
							// $("#be_modal").modal('show');	
							alert("Deletion Failed");	
							$("select[name='othincome_drop']").trigger("change");
						}
				}
			});
		}
    });

    $(".editbtn").unbind('click').click(function()
    {	
    	var iscontinue = true;
		var updateDataConfirm = confirm("Are you sure you want to update this data?");
		if(updateDataConfirm){
			
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
				var employeeid = $(this).attr("employeeid");
				var othIncome = $(this).attr("othIncome");
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
					// datas += "~u~";
					// datas += $(this).find("td:eq(7)").find("input").val();
				});
				
				$.ajax({
					type	: "POST",
					url		: "<?=site_url("process_/saveOtherIncome")?>",
					data	: {
								datas : datas,
								othIncome : othIncome,
								employeeid : employeeid
					},
					dataType:"JSON",
					success	: function(msg){
						alert("Successfully Updated Other Income data!")
						$("select[name='othincome_drop']").trigger("change");
					}
				});
			}
		}
    });


    $("#clearall").unbind('click').click(function(){
    	var ans = confirm('Are you sure do you want to clearall data?');
    	var dataContent  = $("#information").length;
    	var iscontinue = true; 
    	if (dataContent <= 0) {
    		alert("There is no data found in the table!");
    		return;
    	}
    	var othIncome = $("select[name='othincome_drop']").val();
    	if (ans) {
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
					// datas += "~u~";
					// datas += $(this).find("td:eq(7)").find("input").val();
				});
	    		
	    		$.ajax({
	    		url:"<?=site_url("process_/clearotherIncomedata")?>",
	    			type:"POST",
	    			data:{othIncome:othIncome,dept:$("select[name='deptid']").val(),empstatus:$("select[name='employmentstat']").val(),emp:$("select[name='employeeid']").val(),datas:datas},
	    			dataType:"JSON",
	    			success:function(msg)
	    			{
	    					if (msg.err_code == 2) {
	    						$('#be_modal').find('.modal-body').html(msg.msg+'<br>'+'Success count: '+msg.count+'<br>').css({'color':'green','font-size':'15px','font-weight':'bold'});
	    						$("#be_modal").modal('show');	
	    					}
	    					else
	    					{
	    						$('#be_modal').find('.modal-body').html(msg.msg).css({'color':'red','font-size':'15px','font-weight':'bold'});	
	    						$("#be_modal").modal('show');
	    					}
						$("select[name='othincome_drop']").trigger("change");

	    					
	    			}
	    		});
	    	}
    	}
    });

    $(".monthly").keyup(function(e){
		if (e.keyCode === 9) return false; 
		var empType = $(this).closest("tr").attr("empType");
		var empid = $(this).closest("tr").attr("empid");
		var ratebased = '';
		ratebased = getRateBased(empid);
		if(ratebased){
			if(ratebased == "teaching"){
				workingdays      =   261;
			}else{
				workingdays      =  313;
			}
		}else{
			if(empType){
				workingdays      =   261;
			}else{
				workingdays      =  313;
			}
		}

		monthly = $(this).val();
		daily  = Number(((monthly*2)*12)/workingdays); 
		var string_daily = daily.toString();
		var checker = string_daily.substr(string_daily.indexOf(".") + 3);
		checker = checker.substring(0,1);
		if(checker % 2 == 0){
			daily  = parseFloat(daily).toFixed(3);
			daily = daily.slice(0,-1);
		}else{
			daily  = parseFloat(daily).toFixed(2);
		}

        $(this).closest("tr").find("td:eq(3)").html(addCommas(daily));
		
		hourly  = Number((daily)/8);                     // STATIC daily salary divided by total no. of workhours 
		hourly  = parseFloat(hourly).toFixed(2);  
        $(this).closest("tr").find("td:eq(4)").html(addCommas(hourly));


		minutely  = Number((hourly)/60);                    
		minutely  = parseFloat(minutely).toFixed(2);  
        // $(this).closest("tr").find("td:eq(5)").html(addCommas(minutely));
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

	$("#butt_save").unbind().click(function(){
		var iscontinue = true;
		var updateMultipleConfirm = confirm("Update Other Income data/s?");
		if(updateMultipleConfirm){
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
				var othIncome = $("select[name='othincome_drop']").val();
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
					// datas += "~u~";
					// datas += $(this).find("td:eq(7)").find("input").val();
				});
				
				$.ajax({
					type	: "POST",
					url		: "<?=site_url("process_/saveOtherIncome")?>",
					data	: {
								datas : datas,
								othIncome : othIncome
					},
					dataType:"JSON",
					success	: function(msg){
						// // alert(msg);
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
						// $('#be_modal').find('.modal-body').html('Successfully saved/updated count: '+msg.failed+'<br>').css({'color':'green','font-size':'15px','font-weight':'bold'});
						// $('#be_modal').find('.modal-body').append('<span style="color:red;">Data failed: '+msg.failed+'</span>');
						// $("#be_modal").modal('show');		
						// $("select[name='othincome_drop']").trigger("change");
						alert("Successfully Updated Other Income data/s!")
						$("select[name='othincome_drop']").trigger("change");
					}
				});
			}
		}
	});
	$("#history").unbind('click').click(function()
	{
		 otherHistory = "<?=$otherHistory?>";
		if (otherHistory != 0) {
			$(".grey").click();
			$(".greyhistory").click();
			var otherIncome = $("select[name='othincome_drop']").val();
			$.ajax({
				type	: "POST",
				url		: "<?=site_url("process_/viewotherIncomeHistory")?>",
				data	: { otherIncome : otherIncome},
				success : function(msg){
					
					
					$('#be_modal_history').find('.modal-body').html(msg);
					$("#be_modal_history").modal('show');
					
					// $("#other_income_history").html(msg);

				}
			});	
		}
		else
		{
			alert('No Data Found!');
		}
		
	});

	function getRateBased(empid){
	    var ratebased = '';
        $.ajax({
            type: "POST",
            async: false,
            url: "<?= site_url('payroll_/getRateBased') ?>",
            data: {empid:empid},
            success:function(response){
                ratebased = response;
            }
        });
	    return ratebased;
	}

	$("#emplist").dataTable({
	    "pagination": "number",
	    "oLanguage": {
	                     "sEmptyTable":     "No Data Available.."
	                 },
	    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
	    "ordering": false
	});

	// var table = $('#emplist').DataTable({
 //    responsive: true
	// });
	// new $.fn.dataTable.FixedHeader( table );
</script>