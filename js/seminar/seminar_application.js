$("#save").click(function(){
	var employeeid = $("select[name='employeeid']").val();
	var base_id = $("input[name='base_id']").val();
	var datesetfrom = $("input[name='datesetfrom']").val();
	var datesetto = $("input[name='datesetto']").val();
	var fromtime = $("input[name='fromtime']").val();
	var totime = $("input[name='totime']").val();
	var category = $("select[name='category']").val();
	var seminar = $("select[name='seminar']").val();
	var organizer = $("input[name='organizer']").val();
	var venue = $("select[name='venue']").val();
	var location = $("input[name='location']").val();
	var fee = $("input[name='fee']").val();
	var title = $("#seminar_title").val();
	var deadline = $("input[name='deadline']").val();
	var remarks = $("#remarks").val();
	var formdata = {
		base_id			:base_id,
		applied_by		:employeeid,
		datesetfrom		:datesetfrom,
		datesetto		:datesetto,
		timefrom		:fromtime,
		timeto			:totime,
		category		:category,
		seminar			:seminar,
		organizer		:organizer,
		venue			:venue,
		location		:location,
		fee				:fee,
		title			:title,
		deadline		:deadline,
		remarks			:remarks
	};
	if(!datesetfrom || !datesetto){
		alert("Please input a valid date from and date to.");
		return;
	}else if(!fromtime || !totime || !category || !organizer || !venue || !seminar || !location || !fee || !title || !deadline){
		alert("All fields are required!");
	}else if(!employeeid){
		alert("Please input atleast 1 employee.");
	}else{
		$.ajax({
			url : $("#site_url").val() + "/seminar_/saveSeminarApp",
			type: "POST",
			data: formdata,
			dataType: "json",
			success:function(response){
				if(response.err_code){
					alert(response.msg);
					window.location.reload()
				}else{
					alert(response.msg);
				}
			}
		});
	}
});

$("select[name='category']").change(function(){
	var code = $(this).val();
	$.ajax({
		url : $("#site_url").val() + "/extensions_/showreportseduclevel",
		type: "POST",
		data: {code: code},
		success:function(response){
			$("select[name='seminar']").html(response).trigger("chosen:updated");
		}
	})
});

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$(".time").datetimepicker({
	format: "LT"
});

$('.chosen').chosen();