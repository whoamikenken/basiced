<?php
/**
 * @author Angelica
 * @copyright 2017
 *
 */

$datetoday = date('Y-m-d');
?>

<style type="text/css">
	.form_row{
		padding-bottom: 10px;
	}

	.panel-body{
		margin-top: 30px;
		margin-bottom: 10px;
	}
</style>

<div id="content">
	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading"><h4><b>Student Attendance</b></h4></div>
                  		<div class="panel-body">
                  			<div class="col-md-12">
								<div class="form_row col-md-6">
									<label class="field_name align_right">Section</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<div class='input-group time' id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
						                      <input class="form-control" size="16" name="datesetfrom" id="dfrom" type="text" value="<?=$datetoday?>"/>
						                      <span class="input-group-addon">
						                          <span class="glyphicon glyphicon-calendar"></span>
						                      </span>
						                    </div>
										</div>
									</div>
								</div>

								<div class="form_row col-md-6">
									<label class="field_name align_right">Department</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen" name="dept">
												<?=$this->extras->showStudentDepartmentType($depts='',$selected='');?>
											</select>
										</div>
									</div>
								</div>
							</div>

							<div class="col-md-12">
								<div class="form_row col-md-6">
									<label class="field_name align_right">Year Level</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen" name="yearLevel">
												<?=$this->extras->showStudentYL($selected='',$depts='');?>
											</select>
										</div>
									</div>
								</div>

								<div class="form_row col-md-6">
									<label class="field_name align_right" style="margin-left: 7px;">Section</label>
									<div class="field">
										<div class="col-md-12 no-search">
											<select class="chosen" name="section">
												<?=$this->extras->showStudentSection();?>
											</select>
										</div>
									</div>
								</div>
							</div>
				
							<div class="form_row" style="margin-left: 147px;">
								<a href="#" class="btn btn-primary" id="da" style="margin-right: 15px;">Daily Attendance</a>
								<a href="#" class="btn btn-primary" id="ta" style="margin-right: 15px;">Tardiness Report</a>
								<a href="#" class="btn btn-primary" id="ab">Abesences Report</a>
							</div>
							
						</form>
					</div>
				</div> 

				<div id="displaylogs" style="padding: 5px;"></div>


			</div>
		</div>
	</div>
</div>
<script>
	$('.chosen').chosen();
	$("#datesetfrom").datetimepicker({
		format: "DD-MM-YYYYY"
	});
	
	$("input[name=dept]").on("change",function(){
		var department = $(this).val();

		$.ajax({
			url:"<?=site_url('process_/yearLevel')?>",
			type:"POST",
			data:{dept:department},
			success: function(msg){
				$("input[name=yearLevel]").html(msg).trigger('liszt:updated');
			}
		});
		
	});

	$('#da').on('click',function(){
		console.log($('#studattendance').serialize());
		$("#displaylogs").html("Loading, please wait...");

		var form_data = $('#studattendance').serialize();
		$.ajax({
			url:"<?=site_url('student_/loadAttendanceReport')?>",
			type:"POST",
			data:form_data,
			success: function(msg){
				$('#displaylogs').html(msg);
			}
		});
	});

</script>