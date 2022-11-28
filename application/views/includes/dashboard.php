<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" integrity="sha512-s+xg36jbIujB2S2VKfpGmlC3T5V2TF3lY48DX7u2r9XzGzgPsa6wTpOQA7J9iffvdeBN0q9tKzRxVxw1JviZPg==" crossorigin="anonymous"></script>
<script src="<?=base_url()?>js/html2canvas.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.min.js"></script>

<style type="text/css">	    
.panel-body {
    height: 420px;
}
</style>
<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-4" id="kupal">
            	<div class="panel animated fadeIn delay-1s" style="border: #337ab7 !important;height: 476px !important;">
                	<div class="panel-heading" style="background-color: #337ab7!important;color: white;">
						<div class="ui menu">
							<center>
								<a href="" class="item"><i class="large camera retro icon"></i><span class="nav-text"> <img src="<?=base_url()?>/images/timecard.png" style="width: 35px;"></span></a>
								<a class="item"><span class="nav-text" style="font-weight: bold;font-size: 14px;letter-spacing: 1px;">Today's Attendance</span><i class="large help basic icon"></i></a>
							</center>
						</div>
                	</div>
                	<div class="panel-body" style="padding-left: 0px;padding-right: 0px;"> 
					    <canvas id="TodayAttendanceCanvas"></canvas>
					</div>
				</div>

                <div class="panel animated fadeIn delay-1s" style="border: #337ab7 !important;height: 476px !important;">
                	<div class="panel-heading" style="background-color: #337ab7!important;color: white;">
						<div class="ui menu">
							<center>
								<a href="" class="item"><i class="large camera retro icon"></i><span class="nav-text"> <img src="<?=base_url()?>/images/present.png" style="width: 35px;"></span></a>
								<a class="item"><span class="nav-text" style="font-weight: bold;font-size: 14px;letter-spacing: 1px;">Present Employee Time in Accuracy</span><i class="large help basic icon"></i></a>
							</center>
						</div>
                	</div>
                	<div class="panel-body" style="padding-left: 0px;padding-right: 0px;"> 
					    <canvas id="TimeAccuracyCanvas"></canvas>
					</div>
				</div>
			</div>
            <div class="col-md-4">
                <div class="panel animated fadeIn delay-1s" style="border: #337ab7 !important;height: 1048px !important;">
                	<div class="panel-heading" style="background-color: #337ab7!important;color: white;">
						<div class="ui menu">
							<center>
								<a href="" class="item"><i class="large camera retro icon"></i><span class="nav-text"> <img src="<?=base_url()?>/images/presentlist.png" style="width: 35px;"></span></a>
								<a class="item"><span class="nav-text" id="employeelistTitle" style="font-weight: bold;font-size: 14px;letter-spacing: 1px;">Present</span><i class="large help basic icon"></i></a>
							</center>
						</div>
                	</div>
                	<div class="panel-body" id="presentlist" style="padding-left: 0px;padding-right: 0px;height: 920px;"> 
					    
					</div>
				</div>
			</div>

            <div class="col-md-4">
                <div class="panel animated fadeIn delay-1s" style="border: #337ab7 !important;height: 476px !important;">
                	<div class="panel-heading" style="background-color: #337ab7!important;color: white;">
						<div class="ui menu">
							<center>
								<a href="" class="item"><i class="large camera retro icon"></i><span class="nav-text"> <img src="<?=base_url()?>images/activity.png" style="width: 35px;"></span></a>
								<a class="item"><span class="nav-text" style="font-weight: bold;font-size: 14px;letter-spacing: 1px;">Today's Activity</span><i class="large help basic icon"></i></a>
							</center>
						</div>
                	</div>
                	<div class="panel-body" id="announcementlist" style="padding-left: 0px;padding-right: 0px;height: 410px;"> 

					</div>
				</div>

                <div class="panel animated fadeIn delay-1s" style="border: #337ab7 !important;height: 476px !important;">
                	<div class="panel-heading" style="background-color: #337ab7!important;color: white;">
						<div class="ui menu">
							<center>
								<a href="" class="item"><i class="large camera retro icon"></i><span class="nav-text"> <img src="<?=base_url()?>/images/birthday.png" style="width: 35px;"></span></a>
								<a class="item"><span class="nav-text" style="font-weight: bold;font-size: 14px;letter-spacing: 1px;" id="birthdaySpan">Today's Birthday Celebrants</span><i class="large help basic icon"></i></a>
							</center>
						</div>
                	</div>
                	<div class="panel-body" id="birthdaylist" style="padding-left: 0px;padding-right: 0px;height: 410px;display: none"> 
					</div>
					<div class="panel-body" id="usageCanvas" style="padding-left: 0px;padding-right: 0px;height: 410px;display: none">
						<canvas id="loginUsageCanvas"></canvas>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="attendanceModal" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
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
				<center><b><h3 tag="title" class="modal-title" style="font-family: 'Poppins';">Modal Header</h3></b></center>
			</div>
			<div class="modal-body">
				<p>Some text in the modal.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>

	</div>
</div>

<script>

	var toks = hex_sha512(" "); 
	
	getAttendanceToday();
	getTodayAnnouncement();
	getBirthdayCelebrantsToday();
	getPresentAttendanceToday();
	loadDetailedModal("Present");
	// loadLoginUsage();

	function getAttendanceToday(){
		$.ajax({
			url: "<?= site_url('attendance_/getAttendanceToday') ?>",
			dataType: "json",
			async: false,
			success:function(response){
				generateChartAttendance(response);
			}
		});
	}

	function getPresentAttendanceToday(){
		$.ajax({
			url: "<?= site_url('attendance_/getPresentAttendanceToday') ?>",
			dataType: "json",
			async: false,
			success:function(response){
				generateChartPresent(response);
			}
		});
	}

	function getTodayAnnouncement(){
		$.ajax({
			url: "<?= site_url('announcements_/getTodayAnnouncement') ?>",
			success:function(response){
				$("#announcementlist").html(response);
			}
		});
	}

	function getBirthdayCelebrantsToday(){
		$.ajax({
			url: "<?= site_url('employee_/getBirthdayCelebrantsToday') ?>",
			success:function(response){
				if (response == "usage") {
					$.ajax({
						url: "<?= site_url('attendance_/getUsageLogin') ?>",
						dataType: "json",
						async: false,
						success:function(res){	
							$("#birthdaySpan").text("System Usage")
							$("#usageCanvas").show();
							loadLoginUsage(res);
						}
					});
				}else{
					$("#birthdaylist").show();
					$("#birthdaylist").html(response);
				}
			}
		});
	}

	function loadDetailedModal(label){
		var title = "";
		if(label == "Present") title = "List of Present Employee";
		else if(label == "Late") title = "List of Late Employee";
		else if(label == "Absent") title = "List of Absent Employee";
		else if(label == "Leave/OB") title = "List of Employees On Leave";
		else if(label == "On Time") title = "List of Employee On Time";
		else if(label == "Flexible") title = "List of Employee With Flexible Schedule";
		else if(label == "Part-time") title = "List of Part-time Employee";
		$("#employeelistTitle").text(title);
		$.ajax({
			url: "<?= site_url('employee_/getAttendanceListModal') ?>",
			type: "POST",
			data: {label:GibberishAES.enc(label, toks), toks:toks},
			success:function(response){
				$("#presentlist").html(response);
			}
		});
	}


	function loadLoginUsage(data){
		var ctx = $('#loginUsageCanvas');
		var height = $('#loginUsageCanvas').parent().height();
		var width = $('#loginUsageCanvas').parent().width();
		$('#loginUsageCanvas').attr('height', height);
		$('#loginUsageCanvas').attr('width', width);
		var labelData = [];
		var datasetData = [];
		data.forEach(function(entry) {
			
			labelData.push(entry.DATE);
			datasetData.push(entry.LOG);
		});
		var myChart = new Chart(ctx, {
		    type: 'line',
		    data: {
					labels: labelData,
					datasets: [{
						label: 'Login count',
						fill: false,
						backgroundColor: 'rgb(76, 130, 211)',
						borderColor: 'rgb(7, 81, 191)',
						data: datasetData,
					}]
				},
				options: {
					responsive: false,
					title: {
						display: false,
						text: 'Min and Max Settings'
					},
					legend: {
				        display: false
				    },
					scales: {
						yAxes: [{
							ticks: {
								// the data minimum used for determining the ticks is Math.min(dataMin, suggestedMin)
								suggestedMin: 10,

								// the data maximum used for determining the ticks is Math.max(dataMax, suggestedMax)
								suggestedMax: 50
							}
						}]
					}
				}
		});
	}

	function generateChartAttendance(data){
		var ctx = $('#TodayAttendanceCanvas');
		var height = $('#TodayAttendanceCanvas').parent().height();
		var width = $('#TodayAttendanceCanvas').parent().width();
		$('#TodayAttendanceCanvas').attr('height', height);
		$('#TodayAttendanceCanvas').attr('width', width);
		var myChart = new Chart(ctx, {
		    type: 'pie',
		    data: {
					datasets: [{
						data: [
							data.present,
							data.holiday,
							data.leave_ob,
							data.absent,
							data.flexible,
							data.pt,
						],
						backgroundColor: [
							'rgb(14, 237, 133)',
							'rgb(236, 245, 66)',
							'rgb(7, 81, 191)',
							'rgb(204, 16, 16)',
							'rgb(245, 200, 66)',
							'rgb(14, 90, 66)',
						],
						labels: [
							'Present',
							'On Holiday',
							'Leave/OB',
							'Absent',
							'Flexible',
							'Part-time',
						]
					}],
					labels: [
						'Present',
						'On Holiday',
						'Leave/OB',
						'Absent',
						'Flexible',
						'Part-time',
					]
				},
				options: {
					responsive: true,
					'onClick' : function (evt, item) {
	                    loadDetailedModal(item[0]._model.label);
	                }
				}
		});
	}


function generateChartPresent(data){
		var ctx = $('#TimeAccuracyCanvas');
		var height = $('#TimeAccuracyCanvas').parent().height();
		var width = $('#TimeAccuracyCanvas').parent().width();
		$('#TimeAccuracyCanvas').attr('height', height);
		$('#TimeAccuracyCanvas').attr('width', width);

		var myChart = new Chart(ctx, {
		    type: 'bar',
		    data: {
			labels: ['Data'],
			datasets: [{
				label: 'On Time',
				backgroundColor: 'rgb(21, 219, 87)',
				borderColor: 'rgb(4, 142, 50)',
				borderWidth: 1,
				data: [
					data.ontime
				]
			},{
				label: 'Late',
				backgroundColor: 'rgb(255, 176, 193)',
				borderColor: 'rgb(232, 16, 16)',
				borderWidth: 1,
				data: [
					data.late
				]
			}]

		},
		options: {
				// Elements options apply to all of the options unless overridden in a dataset
				// In this case, we are setting the border of each horizontal bar to be 2px wide
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				responsive: true,
				'onClick' : function (evt, item) {
	                // loadDetailedModal(item[0]._model.label);/
	            },
	            scales: {
		            yAxes: [{
		                ticks: {
		                    min: 0,
		                    precision:0
		                }
		            }]
		        }
			},
		});
	}
 
</script>