<?php

?>

<style type="text/css">
	.form_row{
		padding-bottom: 10px;
	}	
</style>

<div id="content"> <!-- Content start -->
	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading"><h4><b>Student Schedule</b></h4></div>
                   <div class="panel-body">
						<a class="btn btn-primary" id="newrequest" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default">New Schedule</a>

						<br><br>
						<div id="table"></div>
						<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true"></div>
					</div>
				</div> 
			</div>
			
		</div>
	</div>
</div>
<script>

$("#newrequest").click(function(){  
    if($(this).prop("disabled")) alert("Please Attach Post Activity first.");
    $.ajax({
        url      : "<?=site_url("student_/loadNewStudSchedForm")?>",
        type     : "POST",
        data     : {
                        // folder: "employeemod", 
                        // view: "changesched_apply"
                    },
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});



$(document).ready(function(){

$.ajax({
	url:"<?=site_url('process_/viewStudentSchedule')?>",
	type:'GET',
	success:function(msg){
		
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
	
	$('#timeStart,#timeEnd,#tardyStart,#halfdayStart,#absentStart').datetimepicker({
    format: "LT"
}); 

	$("#date").datetimepicker({
		format: "MM-DD-YYYY"
	});
});
</script>