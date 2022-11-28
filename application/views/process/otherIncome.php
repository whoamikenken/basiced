<style >
	#be_modal_history
	{
		width: 100em;
		margin-left: -50em !important;
	}

	.form_row{
		padding-bottom: 10px;
	}
	.panel-body{
		margin-top: 30px;
	}
	.row{
		margin: 0px 0px 0px 0px;
	}
     .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/library/jquery.dataTables.min.css">
<div id="content">
	<div class="widgets_area">
		<div class="row">
			<div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Other Income Set Up</b></h4></div>
					<form id="frm-other-income-setup">
					<div class="panel-body">
						<div class="form_row" id="edept">
								<label class="field_name align_right" style="margin-left: -60px;">Department</label>
							<div class="field">
								<div class="col-md-5" style="margin-left: -60px;">
									<select class="chosen" name="deptid">
									  <option value="">All Department</option>
									<?
									  $opt_department = $this->extras->showdepartment();
									  foreach($opt_department as $c=>$val){
									  ?><option value="<?=$c?>"><?=$val?></option><?
									  }
									?>
									</select>
								</div>
							</div>
							<label class="field_name align_right" style="margin-left: -40px; margin-right: 20px;">Category</label>
							<div class="field">
								<div class="col-md-5">
									<select id="othincome_drop" name="othincome_drop" class="chosen"><?=$this->payrolloptions->income();?></select>
								</div>
							</div>
						</div>

						<div class="form_row">
							<label class="field_name align_right" style="margin-left: -60px;">Employee Status</label>
							<div class="field">
								<div class="col-md-5" style="margin-left: -60px;">
									<select class="chosen" name="employmentstat">
									<?
									$opt_status = $this->extras->showemployeestatus("All Status");
									foreach($opt_status as $c=>$val){
									?><option value="<?=$c?>"><?=$val?></option><?    
									}
									?>
								  </select>
							  </div>
							</div>
							<label class="field_name align_right" style="margin-left: -40px; margin-right: 20px;">Display Deminimiss</label>
							<div class="field">
								<div class="col-md-5">
									<select id="isdetailed" name="isdetailed" class="chosen col-md-6">
										<option value="yes">Yes</option>
										<option value="no">No</option>
									</select>
								</div>
							</div>
						</div>

						<div class="form_row">
							<label class="field_name align_right" style="margin-left: -60px;">Employee</label>
							<div class="field">
								<div class="col-md-5" style="margin-left: -60px;">
									<select class="chosen" name="employeeid" id="employeeid" multiple="" >
										<option value="">All Employee</option>
									<?
										$CI =& get_instance();
										$CI->load->model('utils');
										$opt_type = $CI->utils->getEmplist();
									  foreach($opt_type as $key => $val){
									  ?><option value="<?=$key?>"><?=$val?></option><?    
									  }
									?>
									</select>
									<a href="#" class="btn btn-primary" id="multipleencode" style="margin-top: 10px;">Encode</a>
									(Click here for multiple employee encode)
								</div>
							</div>
							<label class="field_name align_right" style="margin-left: -40px; margin-right: 20px;"></label>
							<div class="field">
								<div class="col-md-5"> 
								<a href="#" class="btn btn-success" id="btn-generate-report">
									Other Income Report
								</a> 
							</div>
							</div>
							
						</div>
						</form>

						
					</div>
				</div>
				<div id="other_income"></div>
			</div>
		</div>
		
<div id="be_modal" class="modal hide fade" data-backdrop="static" data-replace="true" data-keyboard="false" tabindex="-1">
    <div class="modal-header">
        <button id="modalclose" type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="glyphicon glyphicon-remove-sign"></i></button>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="row col-md-12" tag='display'></div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" data-dismiss="modal" aria-hidden="true" class="btn grey">Close</a>
    </div>
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
	if("<?=$this->session->userdata('canwrite')?>" == 0) $("#multipleencode").css("pointer-events", "none");
    else $("#multipleencode").css("pointer-events", "");
	$('.chosen').chosen();
	$("#employeeid").on("change", function(){
		var emplist = $(this).val();
		if(emplist !== null){
			$.each( emplist, function( key, value ) {
				if(!value){
					$("#employeeid_chosen .chosen-drop").css("pointer-events", "none");
				}
				else{
					if(emplist != null){
					var itemToDisable = $("option:contains('All Employee')");
					itemToDisable.css("pointer-events", "none");
					$("#employeeid").trigger("chosen:updated");
					}
					else{
					
					}
					// $("#employeeid option[value='']").attr("disabled", "disabled");
				}
			});
		}else{
			$('#employeeid').trigger("chosen:updated"); 
			$(".chosen-drop").css("pointer-events", "");
			var itemToEnable = $("option:contains('All Employee')");
			itemToEnable.css("pointer-events", "");
			$("#employeeid").trigger("chosen:updated");
		}

	});

	$("#multipleencode").click(function(){
		var othIncome = $("select[name='othincome_drop']").val();
		if($("select[name='employeeid'] option:selected").length > 0)
		{
			if(othIncome != ""){  
				$.ajax({
					url      : "<?=site_url('process_/showOtherIncomeEmpList')?>",
					type     : "POST",
					data     : {
								eid     	:   $("select[name='employeeid']").val(),
								othIncome   :   othIncome
							   },
					dataType : "html",
					success: function(msg){
						$("#other_income").html(msg);
					},
					error : function(msg){
						console.log(msg);
					}
				});
			}else    alert("Please choose a category first..");
		}
		else	alert("Please choose employee first..");
		
	});
	
	$("select[name='othincome_drop']").change(function(){
		var otherIncome = $(this).val();
		if($("select[name='employeeid'] option:selected").length > 0){
			$('#other_income').html("<img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.");
			$.ajax({
				type	: "POST",
				url		: "<?=site_url("process_/viewOtherIncomeTable")?>",
				data	: {
					eid     	:   $("select[name='employeeid']").val(), 
					otherIncome : otherIncome
				},
				success : function(msg){
					
					$("#history").show();
					$("#butt_save").show();
					$("#other_income").html(msg);
					

				}
			});
		}
	});

	
	function otherIncomeHistory(otherIncome)
	{
		$.ajax({
			type	: "POST",
			url		: "<?=site_url("process_/viewotherIncomeHistory")?>",
			data	: { otherIncome : otherIncome},
			success : function(msg){
				$('#be_modal').find('.modal-body').html(msg);
				$("#be_modal").modal('show');	
				// $("#other_income_history").html(msg);

			}
		});
	}
	$("select[name='deptid']").change(function(){
		var form_data = "&deptid="+$(this).val();
		if($("select[name='employmentstat']").val() != "")
		{
			form_data = form_data + "&estatus="+$("select[name='employmentstat']").val();
		}
		$.ajax({
			url: "<?=site_url("process_/callemployee")?>",
			type: "POST",
			data: form_data,
			success: function(msg) {
				$("select[name='employeeid']").html(msg).trigger('liszt:updated');
			}
		});
	});
	$("select[name='employmentstat']").change(function(){
		var form_data = "&estatus="+$(this).val();
		if($("select[name='deptid']").val() != "")
		{
			form_data = form_data + "&deptid="+$("select[name='deptid']").val();
		}
		$.ajax({
			url: "<?=site_url("process_/callemployee")?>",
			type: "POST",
			data: form_data,
			success: function(msg) {
				$("select[name='employeeid']").html(msg).trigger('liszt:updated');
			}
		});
	});
	function loadempopt(etype = ""){
		$.ajax({
			url: "<?=site_url("process_/callemployee")?>",
			type: "POST",
			data: {
			   etype : etype
			},
			success: function(msg) {
				$("select[name='employeeid']").html(msg).trigger('liszt:updated');
			}
		});   
	}

	$("#btn-generate-report").unbind("click").click(function(){
		var site_url = "<?=site_url("forms/showOtherIncomeSetupReport")?>";

        $("#frm-other-income-setup").attr("target", "_blank");
        $("#frm-other-income-setup").attr("action", site_url);
        $("#frm-other-income-setup").attr("method", "post");
        $("#frm-other-income-setup").submit();
	});
	
</script>