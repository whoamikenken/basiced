<?php

	$CI =&get_instance();
	$CI->load->model('utils');

?>
<style type="text/css">
	.chosen-container .chosen-choices .search-field:only-child,
.chosen-container .chosen-choices .search-field:only-child input {
    width: 100% !important;
}
</style>
<input type="hidden" id="function" value="<?=isset($function) ? $function : ''?>">
<div class="modal-dialog modal-md">

	<div class="modal-content" >
		<div class="modal-header" >
			<div class="media">
				<div class="media-left">
					<img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
				</div>
				<div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
					<h4 class="media-heading" ><b>Pinnacle Technologies Inc.</b></h4>
					<p style="font-family:Avenir; margin-top: -1%;">D`Great</p>
				</div>
			</div>
			<center><b><h3 tag="title" class="modal-title" style="font-family: 'Poppins';">Batch Process</h3></b></center>
		</div>
		<div class="modal-body">
			<div class="row">
		        <form class="form-horizontal" id="batch_process_form">
		        	<!-- <input type="hidden" name="deptid" value="<?=$deptid?>">
		        	<input type="hidden" name="office" value="<?=$office?>">
		        	<input type="hidden" name="status" value="<?=$status?>">
		        	<input type="hidden" name="teachingtype" value="<?=$teachingtype?>">
		        	<input type="hidden" name="employmentstat" value="<?=$employmentstat?>">
		        	<input type="hidden" name="campus" value="<?=$campus?>"> -->

		        	<div class="form-group">
						<label class="control-label col-sm-3">Category:</label>
						<div class="col-sm-8">
							<select class="form form-control  process_category" name="process_category" id="process_category">
                            </select>
						</div>
					</div>

		        	<div class="form-group">
						<label class="control-label col-sm-3" for="deptid">Department:</label>
						<div class="col-sm-8">
							<select class="chosen batchprocess_filter" name="deptid">
								
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-3" for="office">Office:</label>
						<div class="col-sm-8">
							<select class="chosen batchprocess_filter" name="office">
								<?=$this->extras->getOffice()?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-3" for="campus">Campus:</label>
						<div class="col-sm-8">
							<select class="chosen batchprocess_filter" name="campus">
								<option value="POVEDA">Pinnacle Technologies Inc.</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-3" for="employmentstat">Employment Status:</label>
						<div class="col-sm-8">
							<select class="chosen batchprocess_filter" name="employmentstat[]" id="employmentstat" multiple>
								
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-3" for="status">Status:</label>
						<div class="col-sm-8">	
							<select class="chosen batchprocess_filter" name="status">
									<option value="">All status</option>
									<option value="1">Active</option>
									<option value="0">Inactive</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-3" for="teachingtype">Teaching Type:</label>
						<div class="col-sm-8">
							<select class="chosen batchprocess_filter" name="teachingtype" >
								<option value="">All employee type</option>
								<option value="teaching">Teaching</option>
								<option value="nonteaching">Non Teaching</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-3" for="employeeid">Employee:</label>
						<div class="col-sm-8">
							<select class="chosen col-md-6" id="employeeid" name="employeeid[]" multiple="">
								<option value="">All Employees</option>
	                            <?php foreach($emplist as $row): ?>
	                            	<option value="<?=$row['employeeid']?>"><?=$row['employeeid']." - ".$row['fullname']?></option>
	                            <?php endforeach ?>
	                        </select>&nbsp;&nbsp;
						</div>
					</div>

		        	<div id="not_salary">
						<div class="form-group is_regdeduc">
							<label class="control-label col-sm-3" for="date" id="datelabel"> Date:</label>
							<div class="col-sm-8">
								<div class='input-group date' data-date="" data-date-format="yyyy-mm-dd">
	                                <input type='text' class="form-control dateset" size="16" name="dateset" type="text" value=""/>
	                                <span class="input-group-addon">
	                                <span class="glyphicon glyphicon-calendar"></span>
	                                </span>
	                            </div>
							</div>
						</div>
						<div class="form-group" style="display: none;">
							<label class="control-label col-sm-3" for="date">End Date:</label>
							<div class="col-sm-8">
								<div class='input-group date' data-date="" data-date-format="yyyy-mm-dd">
									<input type='text' class="form-control dateto" size="16" name="dateto" type="text" value=""/>
									<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
									</span>
								</div>
							</div>
						</div>
						<div id="is_loan" style="display: none;">
							<div class="form-group">
								<label class="control-label col-sm-3" for="process_starting_balance">Base On:</label>
								<div class="col-sm-8">
									<select class="baseon form-control" name='process_baseon' class="span11"><?=$CI->utils->basedon($loanbase)?></select>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3" for="process_starting_balance">Starting Balance:</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="process_starting_balance" placeholder="Enter starting balance">
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-3">Current Balance:</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="process_current_balance" placeholder="Enter current_balance">
								</div>
							</div>
						</div>
						<div class="form-group is_regdeduc">
							<label class="control-label col-sm-3">No. of Cut-Off/s:</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="process_nocutoff" placeholder="Enter no cut-off">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Amount:</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="process_amount" placeholder="Enter amount">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Cut-off Schedule:</label>
							<div class="col-sm-8">
								<select class="chosen" name="process_schedule">
									<option value="1">1st Cut-off</option>
									<option value="2">2nd Cut-off</option>
									<option value="3">all Cut-off</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3" id="codetype">Type:</label>
							<div class="col-sm-8">
								<select class="chosen" name="code_categ">
									
								</select>
							</div>
						</div>
					</div>
					<div id="is_salary" style="display: none;">
						<!-- <div class="form-group">
							<label class="control-label col-sm-3">Type:</label>
							<div class="col-sm-8">
								<select class="form-control type" name="process_type">
                                    <option value="">- Select Type -</option>
                                    <?php foreach($type_config as $value): ?>
                                        <option value="<?= $value['id'] ?>"> <?= $value['description'] ?></option>
                                    <?php endforeach ?>
                                </select>
							</div>
						</div> -->
						<div class="form-group">
							<label class="control-label col-sm-3">Monthly:</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" name="process_monthly" placeholder="Enter starting balance">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Semi monthly:</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" name="process_semimonthly" placeholder="Enter starting balance">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Daily:</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" name="process_daily" placeholder="Enter starting balance">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Hourly:</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" name="process_hourly" placeholder="Enter starting balance">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Minutely:</label>
							<div class="col-sm-8">
								<input type="number" class="form-control" name="process_minutely" placeholder="Enter starting balance">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Schedule:</label>
							<div class="col-sm-8">
								<select class="form-control" name="process_salary_schedule"><?=$this->payrolloptions->payschedule($schedule);?></select>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-3">Tax Status:</label>
							<div class="col-sm-8">
								<select class="form-control" name="process_tax_status"><?=$this->payrolloptions->taxdependents($tax_status);?></select>
							</div>
						</div>
					</div>
				</form>
		    </div>
		</div>
		<div class="modal-footer">
			<div id="batchsaving">
				<button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
				<button type="button" class="btn btn-success" id='save_batch_encode'>Save changes</button>

			</div>
			<div id="batchloading" style="display: none;"><img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.</div>
		</div>
	</div>

</div>
<!-- <div id="loading"><img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.</div> -->
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<input type="hidden" id="username" value="<?= $this->session->userdata('username') ?>">
<script src="<?=base_url()?>js/batch_encode/be_main.js"></script>
<script>
	var toks = hex_sha512(" ");
	loadDepartmentSelection();
	loadEmpStatusSelection();
	loadCategorySelection2();

	$("#process_category").change(function(){
		var categ = $(this).val();
		if(categ == "salary"){
			$("#not_salary").hide();
			$("#is_salary").show();
		}else{
			$("#not_salary").show();
			$("#is_salary").hide();
		}

		if(categ == "loan"){
			$("#is_loan").show();
		}else{
			$("#is_loan").hide();
		}

		if(categ == "regdeduc"){
			$(".is_regdeduc").hide();
		}else{
			$(".is_regdeduc").show();
		}

		if(categ == "income"){
			$("#datelabel").text("Income Date");
		}else{
			$("#datelabel").text("Deduction Date");
		}

		if(categ != "regdeduc"){
			$("#codetype").text("Type ");
		}else{
			$("#codetype").text("Reglamentory ");
		}

		/*for function*/
		if(categ == "income") $("#function").val("getIncomeBatchEncodeData");
		else if(categ == "deduction") $("#function").val("getDeductionBatchEncodeData");
		else if(categ == "loan") $("#function").val("getLoanBatchEncodeData");
		else if(categ == "salary") $("#function").val("getSalaryBatchEncodeData");
		else if(categ == "regdeduc") $("#function").val("getReglamentoryBatchEncodeData");
	});

	$("#employee").on("change", function(){
	    var elementId = $(this).attr("id");
	    var value = $("#"+elementId).parent().find('#'+elementId+"_chzn").find(".chzn-drop").find("ul").each(function(){ $(this).find(".active-result").show();});
	});

	$("#employmentstat").on("change", function(){
	    var elementId = $(this).attr("id");
	    var value = $("#"+elementId).parent().find('#'+elementId+"_chzn").find(".chzn-drop").find("ul").each(function(){ $(this).find(".active-result").show();});
	});

	$("input[name=process_monthly]").keyup(function(e){
       if (e.keyCode === 9) return false;   

       var teachingtype = "nonteaching"; 
       var monthly = $(this).val();

       computeSalary(monthly,'monthly',teachingtype);
    });
    
    $("input[name=process_semimonthly]").keyup(function(e){
       if (e.keyCode === 9) return false;

       var teachingtype = "nonteaching"; 
       var monthly = $(this).val() * 2;

       computeSalary(monthly,'semimonthly',teachingtype);
    });

    $("input[name=process_daily]").keyup(function(e){
       if (e.keyCode === 9) return false; 

       var teachingtype = "nonteaching"; 
       var monthly = getMonthlyFromDaily($(this).val(),teachingtype);

       computeSalary(monthly,'monthly',teachingtype);
    });
    
    $("input[name=process_hourly]").keyup(function(e){
       if (e.keyCode === 9) return false; 

       var teachingtype = "nonteaching"; 
       var daily = $(this).val() * 8;
       var monthly = getMonthlyFromDaily(daily,teachingtype);

       computeSalary(monthly,'monthly',teachingtype);
       
    });
    
    $("input[name=process_minutely]").keyup(function(e){
       if (e.keyCode === 9) return false; 

       var teachingtype = "nonteaching"; 
       var daily = $(this).val() * 8 * 60;
       var monthly = getMonthlyFromDaily(daily,teachingtype);

       computeSalary(monthly,'monthly',teachingtype);
       
    });

    $("select[name='deptid']").change(function(){
	    $.ajax({
	        url: $("#site_url").val() + "/setup_/getOffice",
	        type: "POST",
	        data: {department: GibberishAES.enc( $(this).val(), toks), toks:toks},
	        success: function(msg){
	            $("#batch_process_form").find("select[name='office']").html(msg).trigger("chosen:updated");
	        }
	    });
	});

	$("input[name='process_nocutoff'], input[name='process_current_balance'], input[name='process_amount']").change(function(){
		var category = $("#process_category").val();
		var process_nocutoff = $("input[name='process_nocutoff']").val();
		var process_current_balance = $("input[name='process_current_balance']").val();
		var process_baseon = $("select[name='process_baseon']").val();
		var process_amount = $("input[name='process_amount']").val();
		var amount = 0;
		var cutoff = 0;
		if(category == "loan"){
			if(process_baseon == "0"){
				if(process_nocutoff && process_current_balance){
					amount = parseInt(process_current_balance) / parseInt(process_nocutoff);
					$("input[name='process_amount']").val(amount);
				}
			}else if(process_baseon == "1"){
				if(process_amount && process_current_balance){
					cutoff = parseInt(process_current_balance) / parseInt(process_amount);
					$("input[name='process_nocutoff']").val(cutoff);
				}
			}

		}
	});

    $('.batchprocess_filter').on('change',function(){
	    var campus = GibberishAES.enc($("#batch_process_form").find("select[name='campus']").val(), toks);
	    var teachingType = GibberishAES.enc($("#batch_process_form").find("select[name='teachingtype']").val(), toks);
	    var employmentstat = GibberishAES.enc($("#batch_process_form").find("select[name='employmentstat[]']").val(), toks);
	    var office = GibberishAES.enc($("#batch_process_form").find("select[name='office']").val(), toks);
	    var department = GibberishAES.enc($("#batch_process_form").find("select[name='deptid']").val(), toks);
	    var status = GibberishAES.enc($("#batch_process_form").find("select[name='status']").val(), toks);
	    $.ajax({
	        type : "POST",
	        url: "<?=site_url('employee_/load201sort')?>",
	        data: {campus: campus, teachingType:teachingType, department:department, status:status, office:office, employmentstat:employmentstat,toks:toks},
	        success: function(data){
	            $("select[name='employeeid[]']").html(data).trigger("chosen:updated");
	        }
	    });
	});

	$("select[name='process_baseon']").change(function(){
		var basedon = $(this).val();
		if(basedon == "0"){
			$("input[name='process_amount']").attr("readonly", true);
			$("input[name='process_nocutoff']").attr("readonly", false);
		}else{
			$("input[name='process_amount']").attr("readonly", false);
			$("input[name='process_nocutoff']").attr("readonly", true);
		}
	});

    function getMonthlyFromDaily(daily,teachingtype){
        var monthly = 0;
        if(teachingtype == 'teaching') monthly = (daily * 262) / 12;
        else                           monthly = (daily * 314) / 12;
        return monthly;
    }

    function computeSalary(monthly,input_type='monthly',teachingtype='teaching'){
        var semimonthly = monthly / 2;
      
        if(input_type != 'monthly'){
            $('input[name=process_monthly]').val(monthly.toFixed(2));
        }
        if(input_type != 'semimonthly'){
            $('input[name=process_semimonthly]').val(semimonthly.toFixed(2));
        }
        if(input_type != 'daily'){
            daily  = floorFigure(Number((monthly*12)/360));
            $('input[name=process_daily]').val(daily);
        }
        if(input_type != 'hourly'){
            hourly  = Number(daily/8);
            $('input[name=process_hourly]').val(hourly.toFixed(2));
            minutely  = floorFigure(Number(($('input[name=process_hourly]').val())/60));
            $('input[name=process_minutely]').val(minutely);
        }

    }

    function floorFigure(figure, decimals){
        if (!decimals) decimals = 2;
        var d = Math.pow(10,decimals);
        return (parseInt(figure*d)/d).toFixed(decimals);
    }

</script>