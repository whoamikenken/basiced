$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$(".chosen").chosen();

$('.time').datetimepicker({
    format: 'LT'
});

$("#transfee, #regfee, #accfee").keypress(function(){
	var trans = $("#transfee").val();
	var reg = $("#regfee").val();
	var acc = $("#accfee").val();
	var total = reg + trans + acc;
	$("#total").val(total);
});

$("#button_save_modal").on("click", function(){
	var iscontinue = true;
	var formdata = $("#inhouse_form").serialize();
	$("#inhouse_form input, select").each(function(){
		if(!$(this).val() && $(this).attr("name") != "id"){
			$(this).css("border", "1px solid red");
			iscontinue = false;
		}else{
			$(this).css("border", "1px solid #ccc");
		}
	});

	if(!iscontinue) return;

	$.ajax({
		url: $("#site_url").val() + "/seminar_/validateInhouseSeminar",
		type: "POST",
		data: formdata,
		dataType: "json",
		success:function(response){
			if(response.stat){
	            alert(response.msg);
				loadSeminar();
				$("#modal-view").modal("toggle");
			}else{
				$("#msg_header").removeClass("alert alert-success");
	            $("#msg_header").addClass("alert alert-danger");
	            $("#msg_header").find("strong").text("Failed! ");
	            $("#msg_header").find("span").text(response.msg);
	            $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
			}
		}
	});
});

function workshopSelection() {
	$.ajax({
		url: $("#site_url").val() + "/setup_/workshopSelection",
		success:function(response){
			$("#workshop").html(response).trigger("chosen:updated");
		}
	});
}
