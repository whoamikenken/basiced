<?php

?>

<style type="text/css">
	.panel-body{
		margin-top: 30px;
	}

	.padding{
		padding-bottom: 10px;
	}
</style>

<div id="content"> <!-- Content start -->
	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading"><h4><b>Student Attendance</b></h4></div>
                   <div class="panel-body">
						<form id="studattendance">
							<div class="form_row">
								<div class="col-md-12 padding">
								<div class="form_row col-md-6 ">
									<label class="field_name align_right">Department</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen col-md-12" id="dept">
												<?=$this->extras->showStudentDepartmentType($depts='',$selected='');?>
											</select>
										</div>
									</div>
								</div>
								<div class="form_row col-md-6">
									<label class="field_name align_right">Year Level</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen col-md-12" id="yearLevel">
												<?=$this->extras->showStudentYL($selected='',$depts='');?>
											</select>
										</div>
									</div>
								</div>
								</div>
								<div class="col-md-12 padding">
								<div class="form_row col-md-6">
									<label class="field_name align_right">Schedule</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen col-md-12" id="sched">
											
											</select>
										</div>
									</div>
								</div>
								<div class="form_row col-md-6">
									<label class="field_name align_right">Professor</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen col-md-6" id="prof">
											
											</select>
										</div>
									</div>
								</div>
								</div>
								<!-- <div class="form_row col-md-4">
									<label class="field_name align_right">Section</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen col-md-6" id="section">
											
											</select>
										</div>
									</div>
								</div> -->
							</div>
							<div class="form_row">
								<div class="col-md-12 padding">
								<div class="form_row col-md-6">
									<label class="field_name align_right">Subject</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen col-md-12" id="subject">
											
											</select>
										</div>
									</div>
								</div>
								<div class="form_row col-md-6">
									<label class="field_name align_right">Students</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen col-md-12" id="stud">
											
											</select>
										</div>
									</div>
								</div>
								</div>
							</div>
							<div class="form_row">
								<div class="col-md-12 padding">
								<div class="form_row col-md-6">
									<label class="field_name align_right" style="margin-right: 24px;" >Date</label>
									<div class="field" style="width: 77%;">
										<div class='input-group date' id='date' data-date="<?=date("Y-m-d")?>" data-date-format="yyyy-mm-dd">
	                                      <input type='text' class="form-control" size="16" name="date" type="text" value="<?=date("Y-m-d")?>"/>
	                                      <span class="input-group-addon">
	                                            <span class="glyphicon glyphicon-calendar"></span>
	                                      </span>
	                                    </div>
									</div>
								</div>
							</div>
						</div>
							<div class="form_row">
								<div class="col-md-6">
									<div class="field" style="margin-left: 127px; margin-top: 20px; margin-bottom: 20px;">
										<a href="#" class="btn btn-primary" id="generate" style="margin-right: 15px;">Generate</a>
										<a href="#" class="btn btn-primary" id="print">Print Result</a>
									</div>
								<div>
							</div>
						</form>
					</div>
				</div> 
			</div>
		</div>
	</div>
</div>
<script>
	$('.chosen').chosen();
	$("#date").datetimepicker({
		format: 'DD-MM-YYYY'
	});
		$("#dept").on("change",function(){
		var department = $(this).val();

		$.ajax({
			url:"<?=site_url('process_/yearLevel')?>",
			type:"POST",
			data:{dept:department},
			success: function(msg){
				$("#yearLevel").html(msg).trigger('liszt:updated');
				// alert(msg);
				
			}
		});
		
	});
</script>