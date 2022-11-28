<?php

?>
<div id="content"> <!-- Content start -->
	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="well blue">
					<div class="well-header">
						<h5>Student Schedule</h5>
					</div>
					<div class="well-content">
						<form id="studattendance">
							<div class="form_row">
								<div class="form_row col-md-4">
									<label class="field_name align_right">SY</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen col-md-7" id="sy">
											<?=$this->extras->showStudentSY();?>
											</select>
										</div>
									</div>
								</div>
								<div class="form_row col-md-4">
									<label class="field_name align_right">Time Start</label>
									<div class="field">
										<div class="col-md-6 no-search">
											<div class="input-group bootstrap-timepicker">
									            <input id="timeStart" name="timeStart" class="col-md-8 input-small align_center" type="text" />
									            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
	       									 </div>
										</div>
									</div>
								</div>
								<div class="form_row col-md-4">
									<label class="field_name align_right">Time End</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<div class="input-group bootstrap-timepicker">
									            <input id="timeEnd" name="timeEnd" class="col-md-8 input-small align_center" type="text" />
									            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
	       									 </div>
										</div>
									</div>
								</div>
								
							</div>
							<div class="form_row">
								<div class="form_row col-md-4">
									<label class="field_name align_right">Department</label>
									<div class="field">
										<div class="col-md-12">
											<select   class="chosen-select col-md-7 " data-placeholder="Select Department" multiple id="dept">
												<?=$this->extras->showStudentDepartmentType($depts='');?>
											</select>
										</div>
									</div>
								</div>
								<div class="form_row col-md-6">
									<label class="field_name align_right">Tardy Start</label>
									<div class="field">
										<div class="col-md-6 no-search">
											<div class="input-group bootstrap-timepicker">
									            <input id="tardyStart" name="tardyStart" class="col-md-8 input-small align_center" type="text" />
									            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
	       									 </div>
										</div>
									</div>
								</div>
								
							</div>
							<div class="form_row">
								<div class="form_row col-md-4">
									<label class="field_name align_right">Year Level</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen col-md-7" id="yearLevel">
												<?=$this->extras->showStudentYL();?>
											</select>
										</div>
									</div>
								</div>
								<div class="form_row col-md-6">
									<label class="field_name align_right">Half Day Start</label>
									<div class="field">
										<div class="col-md-6 no-search">
											<div class="input-group bootstrap-timepicker">
									            <input id="halfdayStart" name="halfdayStart" class="col-md-8 input-small align_center" type="text" />
									            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
	       									 </div>
										</div>
									</div>
								</div>
							</div>
							<div class="form_row">
								<div class="form_row col-md-4">
									<label class="field_name align_right">Section</label>
									<div class="field">
										<div class="col-md-12 ">
											<select class="chosen col-md-12" id="section">
												<?=$this->extras->showStudentSection();?>
											</select>
										</div>
									</div>
								</div>

								<div class="form_row col-md-6">
									<label class="field_name align_right">Absent Start</label>
									<div class="field">
										<div class="col-md-6 no-search">
											<div class="input-group bootstrap-timepicker">
									            <input id="absentStart" name="absentStart" class="col-md-8 input-small align_center" type="text" />
									            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
	       									 </div>
										</div>
									</div>
								</div>
								
							</div>
							<div class="form_row">
								<div class="col-md-4">
									<div class="field">
										<a href="#" class="btn btn-primary" id="save">Save</a>
									</div>
								</div>
								<!-- <div class="form_row col-md-6">
									<label class="field_name align_right">Applicable Date</label>
									<div class="field">
										<div class="col-md-6 no-search">
											<div class="input-group bootstrap-timepicker">
									            <input id="date" name="date" class="col-md-8 input-small align_center" type="text" />
									            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
	       									 </div>
										</div>
									</div>
								</div> -->
								<div class="form_row col-md-6">
									<label class="field_name align_right">Applicable Date</label>
									<div class="field">
										<div class="input-group date" id="date" data-date="<?=date("Y-m-d")?>" data-date-format="yyyy-mm-dd">
											<input class="align_center" size="16" name="date" type="text" value="<?=date("Y-m-d")?>" readonly>
											<span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div> 
			</div>
			<div id="table"></div>
		</div>
	</div>
</div>
<script>
$(document).ready(function(){

$.ajax({
			url:"<?=site_url('process_/viewStudentSchedule')?>",
			type:'GET',
			success:function(msg){
				console.log(msg);
				$("#table").html(msg);
			}
		});


	$('.chosen').chosen();
	 
	  $(".chosen-select").chosen({width: "95%"}); 
	$('#save').on("click",function(){
		
		var sy = $('#sy').val();
		var dept = $('#dept').val();
		
		var yl = $('#yearLevel').val();
		var sect = $('#section').val();

		if(sy == ""){sy = "all"}
		if(dept == ""){dept = "all"}
		if(yl == ""){ yl = "all"}
		if(sect == ""){ sect = "all"}
		
		var timeStart = $('#timeStart').val();
		var timeEnd = $('#timeEnd').val();

		var tardyStart = $('#tardyStart').val();
		var halfdayStart = $('#halfdayStart').val();
		var absentStart = $('#absentStart').val();

		var aDate = $("[name=date]").val();

		if(dept == null){
			alert('Department is Required.');
		}else{
		
		var formdata = {sy:sy,dept:dept,yl:yl,sect:sect,
			timeStart:timeStart,timeEnd:timeEnd,
			tardyStart:tardyStart,
			halfdayStart:halfdayStart,
			absentStart:absentStart,aDate:aDate};
			$("#table").html("Loading...");
		
		
		$.ajax({
			url:"<?=site_url('process_/addStudentSchedule')?>",
			type:'POST',
			data:formdata,
			success:function(msg){
				alert(msg);
				$("#table").html(msg);
				location.reload();
			}
		});

		}



	});
	
	$('#timeStart,#timeEnd,#tardyStart,#halfdayStart,#absentStart').timepicker({
    minuteStep: 1,
    showSeconds: false,
    showMeridian: true,
    defaultTime: false
}); 

	$("#date").datepicker({
		autoclose: true,
		todayBtn : true
	});
});
</script>