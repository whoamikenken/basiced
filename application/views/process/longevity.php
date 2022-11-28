<div id="content">
	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="well blue">
					<div class="well-header">
						<h5>Longevity</h5>
					</div>
					<div class="well-content"> 
                        <div class="form_row">
                            <label class="field_name align_right">Year</label>
                            <div class="field">
                                <div class="col-md-12">
                                    <select class="form-control" name="year" id="year">
                                    <?
										for($i = date("Y");$i >= 2016;$i--)
										{
											echo "<option value='{$i}'>{$i}</option>";
										}
									?>
									</select>
                                </div>
                            </div>
                        </div>
						<div class="form_row">
                            <label class="field_name align_right">Department</label>
                            <div class="field">
                                <div class="col-md-12">
                                    <select class="chosen col-md-6" name="deptid" id="deptid">
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
                        </div>
                        <div class="form_row">
                            <div class="field">
                                <div id="load" hidden=""></div>
                                <a href="#" class="btn btn-primary" id="searchlbtn">Payroll Report</a>
                            </div>
                        </div>  
						<div id="longevityList"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

		$(".chosen").chosen();
		
		$("#searchlbtn").click(function(){
			var year = $("#year").val();
			var deptid = $("#deptid").val();
			
			$.ajax({
				url:"<?=site_url("process_/showLongevityTable")?>",
				type:"POST",
				data:{
					year:year,
					deptid,deptid
				},
				success:function(msg){
					$("#longevityList").html(msg);
				}
			});
		});

</script>