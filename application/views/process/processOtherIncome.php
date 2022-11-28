<?
	$CI =& get_instance();
	$CI->load->model('utils');
?>

<style>
	#longevity_emp_included{
		text-decoration: underline;
		font-weight: bold;
		font-style: italic;
	}

	.form_row{
		padding-bottom: 10px;
	}

	.panel-body{
		margin-top: 30px;
	}

</style>

<div id="content">
	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading"><h4><b>Process Other Income</b></h4></div>
                   <div class="panel-body">
                        <div class="form_row">
                            <label class="field_name align_right">Campus</label>
                            <div class="field">
                                <div class="col-md-6">
                                    <select class="chosen col-md-6" name="campus" id="campus">
										<option value="">All Campuses</option>
									<?
									  
									  $opt_type = $CI->utils->getCampusList();
									  foreach($opt_type as $c=>$val){
									  ?><option value="<?=$c?>"><?=$val?></option><?    
									  }
									?>
									</select>
                                </div>
                            </div>
                        </div>
						<div class="form_row">
							<label class="field_name align_right">Other Income</label>
							<div class="field">
								<div class="col-md-6">
									<select id="othincome_drop" name="othincome_drop" class="chosen col-md-6"><?=$this->payrolloptions->income();?></select>
								</div>
							</div>
						</div> 
						<div class="form_row">
                            <label class="field_name align_right">DTR Cut Off</label>
                            <div class="field">
                                <div class="col-md-6">
                                    <select class="chosen col-md-4" id="cutoff"><?=$this->employeemod->displayCutOff()?></select>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="form_row">
                            <div class="field">
                                <div id="load" hidden=""></div>
                                <a href="#" class="btn btn-primary" id="searchlbtn" style="margin-left: 15px;">View Report</a>&nbsp;
                                <a href="#"  id="longevity_emp_included" hidden="">Employees Included</a>
                            </div>
                        </div>  
						<div id="table"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

		$('#othincome_drop').on('change',function(){
			if($(this).val() == '14') $('#longevity_emp_included').show();
			else 					  $('#longevity_emp_included').hide();
		});

		$('#longevity_emp_included').on('click',function(e){
			e.preventDefault();
			$("#table").html("<img src='<?=base_url()?>images/loading.gif' /> Please wait... ");

			$.ajax({
				type:"POST",
				url:"<?=site_url("payroll_/loadLongevityEmpIncluded")?>",
				success:function(msg){
					$("#table").html(msg);
				}
			});

		});

		$(".chosen").chosen();
		function searchdata()
		{
			var campus = $("#campus option:selected").val();
			var othIncome = $("#othincome_drop option:selected").val();
			var cutoff = $("#cutoff option:selected").val();
			console.log(cutoff);
			console.log(campus);
			if($("#othincome_drop option:selected").html() == "LONGEVITY"){
				$("#table").html("<img src='<?=base_url()?>images/loading.gif' /> Please wait... ");
				$.ajax({
					type:"POST",
					url:"<?=site_url("payroll_/loadEmployeeLongevity")?>",
					// url:"<?=site_url("process_/showLongevityTable")?>",
					data:{
						campus:campus,
						cutoff:cutoff
					},
					success:function(msg){
						$("#table").html(msg);
					}
				});
				}
			else if($("#othincome_drop option:selected").html() == "OVER LOAD"){
				var d = {
					othIncome:othIncome,
					campus:campus,
					cutoff:cutoff
				};
				$("#table").html("<img src='<?=base_url()?>images/loading.gif' /> Please wait... ");
				$.ajax({
					type:"POST",
					url:"<?=site_url("process_/showOverloadTable")?>",
					data:d,
					success:function(msg){
						$("#table").html(msg);
					}
				});
			}
			else
			{
				var d = {
					othIncome:othIncome,
					campus:campus,
					cutoff:cutoff
				};
				// console.log(d);
				$("#table").html("<img src='<?=base_url()?>images/loading.gif' /> Please wait... ");
				$.ajax({
					type:"POST",
					url:"<?=site_url("process_/showOtherTable")?>",
					data:d,
					success:function(msg){
						$("#table").html(msg);
					}
				});
			}

		}
		$("#searchlbtn").click(function(){
			searchdata();
		});

</script>