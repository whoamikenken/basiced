getEmpSeminarHistory();

$("#newrequest").click(function(){ 
	$.ajax({
		url      : $("#site_url").val() + "/seminar_/loadSeminarApplication",
		success: function(response){
			$("#myModal").html(response);
		}
	});  
});

function getEmpSeminarHistory(status,isread='0',action){
	$("#changeschedhistory").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
	$.ajax({
		url: $("#site_url").val() + "/seminar_/getEmpSeminarHistory",
		type: "POST",
		data     :    {status : status, isread:isread,action:action},
		success:function(response){
			$("#seminarhistory").html(response);
		}
	});
}