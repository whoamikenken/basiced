<style>
	.b_check {
		transform :scale(2);
		margin:3%;
	}
</style>
<div class="widgets_area">
	<form id="form_schedule">
		<div class="row">
			<div class="col-md-12">
				<div class="well blue">
					<div class="form_row">
						<label class="field_name align_right">SY</label>
						<div class="field">
							<div class="col-md-6 no-search">
								<select class="chosen col-md-6" id="sy">
										
								</select>
							</div>
						</div>
					</div>
					<div class="form_row">
						<label class="field_name align_right">Department</label>
						<div class="field">
							<div class="col-md-8 no-search">
								<select class="chosen col-md-6" id="sy">
										
								</select>
							</div>
						</div>
					</div>
					<div class="form_row">
						<label class="field_name align_right">Year Level</label>
						<div class="field">
							<div class="col-md-8 no-search">
								<select class="chosen col-md-6" id="sy">
										
								</select>
							</div>
						</div>
					</div>
					<div class="form_row">
						<label class="field_name align_right">Section</label>
						<div class="field">
							<div class="col-md-8 no-search">
								<select class="chosen col-md-6" id="sy" multiple>
										
								</select>
							</div>
						</div>
					</div>
					<div class="form_row">
						<label class="field_name align_right">Tardi Start</label>
						<div class="field">
							<div class="input-group bootstrap-timepicker">
                                <input name="tardi" class="col-md-8 input-small align_center timepicker" type="text" value=""/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
						</div>
					</div>
					<div class="form_row">
						<label class="field_name align_right">Half Day Start</label>
						<div class="field">
							<div class="input-group bootstrap-timepicker">
                                <input name="half" class="col-md-8 input-small align_center timepicker" type="text" value=""/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
						</div>
					</div>
					<div class="form_row">
						<label class="field_name align_right">Absent Start</label>
						<div class="field">
							<div class="input-group bootstrap-timepicker">
                                <input name="absent" class="col-md-8 input-small align_center timepicker" type="text" value=""/>
                                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                            </div>
						</div>
					</div>
					
				</div>    
			</div>    
		</div>    
	</form>
</div>  
<script>
$(".timepicker").timepicker({
    minuteStep: 1,
    showSeconds: false,
    showMeridian: true,
    defaultTime: false
  });
</script>